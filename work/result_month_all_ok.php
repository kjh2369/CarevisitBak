<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');

	echo $myF->header_script();

	/**********************************************************

		mode

		1 : 등록
		2 : 취소

	**********************************************************/

	$code   = $_POST['code'];
	$year   = $_POST['year'];
	$month  = $_POST['month'];
	$mode   = $_POST['mode'];
	$flag   = $_POST['flag'];
	$k_list = $_POST['kind_list'];
	$k_cnt  = sizeof($k_list);


	/*
	// 말일부터 근무일수 -3일을 구한다.
	$today = date('Ymd', mktime());

	$tmp_dt = $myF->lastDay(date('Y', mktime()),date('m', mktime()));
	$tmp_dt = date('Y-m-', mktime()).(intval($tmp_dt) < 10 ? '0' : '').intval($tmp_dt);

	$loop_cnt = 0;

	while(1){
		$tmp_weekday = date('w', strtotime($tmp_dt));

		if ($tmp_weekday > 0 && $tmp_weekday < 6){
			if ($loop_cnt > 2) break;

			$loop_cnt ++;
		}
		$tmp_dt = $myF->dateAdd('day', -1, $tmp_dt, 'Y-m-d');
	}

	$limit_dt = str_replace('-', '', $tmp_dt);
	*/
	$today    = date('Ymd', mktime());
	$limit_dt = substr($today,0,6).'15';



	$debug_mdoe = 1;
	$conn->mode = $debug_mdoe;

	$conn->begin();

	switch($mode){
		case 1:
			$sl = "select *
					from t01iljung
				   where t01_ccode         = '$code'
					 and t01_sugup_date like '$year$month%'
					 and t01_del_yn        = 'N'
					 AND	t01_mkind != '6'";

			if ($flag[0] == 'Y'){
				$sl .= " and case when t01_status_gbn = '0' then '9' else t01_status_gbn end = '9'";
			}

			//일자의 제한을 둔다.
			if ($year.$month >= substr($today,0,6) && $today < $limit_dt){
			//	$sl .= " and concat(t01_sugup_date, t01_sugup_fmtime) <= date_format(now(), '%Y%m%d%h%i')";
			}

			$sql = '';

			for($i=0; $i<$k_cnt; $i++){
				$sql .= (!empty($sql) ? ' union all ' : '').$sl.' and t01_mkind = \''.$k_list[$i].'\'';
			}

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$iljung[] = $row;
			}

			if (is_array($iljung)){
				foreach($iljung as $idx => $row){
					//바우처 가사간병, 노인돌봄, 자애인활동지원은 시간단위로 처리한다.
					if ($row['t01_mkind'] == '1' || $row['t01_mkind'] == '2' || $row['t01_mkind'] == '4'){
						if ($row['t01_mkind'] == '1' || $row['t01_mkind'] == '2'){
							if (($row['t01_mkind'] == '1' && $row['t01_sugup_date'] >= '20140201') || ($row['t01_mkind'] == '2' && $row['t01_sugup_date'] >= '20150201')){
								$fromTime	= $myF->time2min($row['t01_sugup_fmtime']);
								$toTime		= $myF->time2min($row['t01_sugup_totime']);
								$soyoTime	= $toTime - $fromTime;

								$tmpTime = $soyoTime % 60;

								if ($tmpTime >= 45){
									$soyoTime = $myF->cutOff($soyoTime,60)+60;
								}else if ($tmpTime >= 15 && $tmpTime < 45){
									$soyoTime = $myF->cutOff($soyoTime,60)+30;
								}else{
									$soyoTime = $myF->cutOff($soyoTime,60);
								}

								$row['t01_sugup_soyotime']	= $soyoTime;
							}else{
								$fromTime	= $myF->time2min($row['t01_sugup_fmtime']);
								$toTime		= $myF->time2min($row['t01_sugup_totime']);
								$soyoTime	= $toTime - $fromTime;
								$soyoTime	= Round($soyoTime / 60) * 60;

								$row['t01_sugup_soyotime']	= $soyoTime;
							}
						}else{
							$fromTime	= $myF->time2min($row['t01_sugup_fmtime']);
							$toTime		= $myF->time2min($row['t01_sugup_totime']);
							$soyoTime	= $toTime - $fromTime;
							$soyoTime	= Round($soyoTime / 60) * 60;

							$row['t01_sugup_soyotime']	= $soyoTime;

							if ($row['t01_mkind'] == '4'){
								if ($row['t01_svc_subcode'] == '200'){
									$loSuga = $mySuga->findSugaDis($row['t01_ccode'], $row['t01_svc_subcode'], $row['t01_sugup_date'], $row['t01_sugup_fmtime'], $row['t01_sugup_totime'], SubStr($row['t01_suga_code1'],2,1),	SubStr($row['t01_suga_code1'],3,1), '', $row['t01_yoyangsa_id2'] ? 2 : 1);

									if ($row['t01_holiday'] == 'Y' || $row['t01_sugup_yoil'] == '0'){
										$row['t01_suga_tot'] = $loSuga['costHoliday'];
									}else{
										$row['t01_suga_tot'] = $loSuga['costTotal'];
									}
								}
							}
						}
					}

					// 일정계획 테이블 수정
					$sql = "update t01iljung
							   set t01_conf_date		= '".$row['t01_sugup_date']."'
							,      t01_conf_fmtime		= '".$row['t01_sugup_fmtime']."'
							,      t01_conf_totime		= '".$row['t01_sugup_totime']."'
							,      t01_conf_soyotime	= '".$row['t01_sugup_soyotime']."'
							,      t01_conf_suga_code	= '".$row['t01_suga_code1']."'
							,      t01_conf_suga_value	= '".$row['t01_suga_tot']."'
							,      t01_status_gbn		= '1'
							,      t01_modify_yn		= 'A'
							,      t01_trans_yn			= 'N'
							 where t01_ccode			= '".$row['t01_ccode']."'
							   and t01_mkind			= '".$row['t01_mkind']."'
							   and t01_jumin			= '".$row['t01_jumin']."'
							   and t01_sugup_date		= '".$row['t01_sugup_date']."'
							   and t01_sugup_fmtime		= '".$row['t01_sugup_fmtime']."'
							   and t01_sugup_seq		= '".$row['t01_sugup_seq']."'";
					//echo $sql.'<br><br><br>';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}

					$temp_y_list[0] = $row['t01_yoyangsa_id1'];
					$temp_y_list[1] = $row['t01_yoyangsa_id2'];

					for($k=0; $k<sizeof($temp_y_list); $k++){
						if ($temp_y_list[$k] != ''){
							// 요양보호사 일정 테이블 수정
							if ($temp_array['t01_del_yn'] == 'N'){
								$sql = "replace into t11iljung_y (
										 t11_ccode
										,t11_mkind
										,t11_jumin_y
										,t11_jumin_s
										,t11_sugup_date
										,t11_sugup_fmtime
										,t11_sugup_seq
										,t11_sugup_totime
										,t11_sugup_soyotime
										,t11_sugup_yoil
										,t11_holiday_yn
										,t11_svc_subcode
										,t11_status_gbn
										,t11_toge_umu
										,t11_bipay_umu
										,t11_suga_code
										,t11_suga
										,t11_suga_over
										,t11_suga_night
										,t11_suga_tot
										,t11_e_time
										,t11_n_time
										,t11_ysudang_yn
										,t11_ysudang
										,t11_ysudang_yul
										,t11_conf_date
										,t11_conf_fmtime
										,t11_conf_totime
										,t11_conf_soyotime
										,t11_conf_suga_code
										,t11_conf_suga_value
										,t11_modify_yn
										,t11_ms_index
										) values (
										 '".$row['t01_ccode']."'
										,'".$row['t01_mkind']."'
										,'".$row['t01_yoyangsa_id'.($k+1)]."'
										,'".$row['t01_jumin']."'
										,'".$row['t01_sugup_date']."'
										,'".$row['t01_sugup_fmtime']."'
										,'".$row['t01_sugup_seq']."'
										,'".$row['t01_sugup_totime']."'
										,'".$row['t01_sugup_soyotime']."'
										,'".$row['t01_sugup_yoil']."'
										,'".$row['t01_holiday']."'
										,'".$row['t01_svc_subcode']."'
										,'1'
										,'".$row['t01_toge_umu']."'
										,'".$row['t01_bipay_umu']."'
										,'".$row['t01_suga_code1']."'
										,'".$row['t01_suga']."'
										,'".$row['t01_suga_over']."'
										,'".$row['t01_suga_night']."'
										,'".$row['t01_suga_tot']."'
										,'".$row['t01_e_time']."'
										,'".$row['t01_n_time']."'
										,'".$row['t01_ysudang_yn']."'
										,'".$row['t01_ysudang']."'
										,'".$row['t01_ysudang_yul'.($k+1)]."'
										,'".$row['t01_sugup_date']."'
										,'".$row['t01_sugup_fmtime']."'
										,'".$row['t01_sugup_totime']."'
										,'".$row['t01_sugup_soyotime']."'
										,'".$row['t01_suga_code1']."'
										,'".$row['t01_suga_tot']."'
										,'A'
										,'".($k+1)."')";

								//echo $sql.'<br><br><br>';

								if (!$conn->execute($sql)){
									$conn->rollback();
									echo $conn->err_back();
									if ($conn->mode == 1) exit;
								}
							}else{
								$sql = "delete
										  from t11iljung_y
										 where t11_ccode        = '".$row['t01_ccode']."'
										   and t11_mkind        = '".$row['t01_mkind']."'
										   and t11_jumin_y      = '".$row['t01_yoyangsa_id'.($k+1)]."'
										   and t11_jumin_s      = '".$row['t01_jumin']."'
										   and t11_sugup_date   = '".$row['t01_sugup_date']."'
										   and t11_sugup_fmtime = '".$row['t01_sugup_fmtime']."'
										   and t11_sugup_seq    = '".$row['t01_sugup_seq']."'";
								//echo $sql.'<br><br><br>';

								if (!$conn->execute($sql)){
									$conn->rollback();
									echo $conn->err_back();
									if ($conn->mode == 1) exit;
								}
							}
						}
					}
				}
			}

			$conn->row_free();
			break;

		case 2:
			foreach($k_list as $i => $k){
				/************************

					직원일정삭제

				************************/
				$sql = 'delete
						  from t11iljung_y
						 where t11_ccode               = \''.$code.'\'
						   and t11_mkind               = \''.$k.'\'
						   and left(t11_sugup_date, 6) = \''.$year.$month.'\'';

				if ($flag[0] == 'Y'){
					$sql .= ' and t11_modify_yn = \'A\'';
				}

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}


				/*********************************************************
				 *	계획없는 실적 삭제
				 *********************************************************/
				$sql = 'DELETE
						FROM	t01iljung
						WHERE	t01_ccode	= \''.$code.'\'
						AND		t01_mkind	= \''.$k.'\'
						AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
						AND		IFNULL(t01_sugup_fmtime,\'\') = \'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}

				/*********************************************************
				 *	주민번호 없는 일정 삭제
				 *********************************************************/
				$sql = 'DELETE
						FROM	t01iljung
						WHERE	t01_ccode	= \''.$code.'\'
						AND		t01_mkind	= \''.$k.'\'
						AND		t01_jumin	= \'\'
						AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 echo $conn->err_back();
					 if ($conn->mode == 1) exit;
				}


				/****************************************

					실적취소

				****************************************/
				$sql = 'update t01iljung
						   set t01_conf_date           = \'\'
						,      t01_conf_fmtime         = \'\'
						,      t01_conf_totime		   = \'\'
						,      t01_conf_soyotime	   = \'\'
						,      t01_conf_suga_code	   = t01_suga_code1
						,      t01_conf_suga_value	   = t01_suga_tot
						,      t01_status_gbn		   = \'0\'
						,      t01_modify_yn		   = \'N\'
						,      t01_trans_yn			   = \'N\'
						 where t01_ccode               = \''.$code.'\'
						   and t01_mkind               = \''.$k.'\'
						   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
						   and t01_del_yn              = \'N\'';

				if ($flag[0] == 'Y'){
					$sql .= ' and t01_modify_yn = \'A\'';
				}

				if (!$conn->execute($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}

			break;
	}
	$conn->commit();

	$orgNo	= $code;
	$yymm	= $year.$month;
	include_once('../iljung/summary.php');
	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert("<?=$myF->message('ok','N');?>");
	if ('<?=$conn->mode;?>' == '1')
		location.replace("result_month_all.php?year=<?=$year;?>&month=<?=$month;?>&mode=<?=$mode;?>");
</script>
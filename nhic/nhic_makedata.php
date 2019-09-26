<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];
	$file = $_POST['file'];
	$gbn  = $_POST['gbn'];

	$reg_id = $_SESSION['userCode'];
	$reg_dt = date('Y-m-d', mktime());
	$today  = date('Y-m-d', mktime());

	if (!empty($date)) $reg_dt = $date;

	$f_name = $_POST['f_name'];
	$f_type = $_POST['f_type'];
	$f_size = $_POST['f_size'];



	/*********************************************************

		파일읽기

	*********************************************************/
	if (($handle = fopen($file, "r")) !== FALSE) {
		$row_id = 0;

		while(true){
			$str = fgets($handle);

			$data = explode(chr(9), $str);

			if (empty($data[0])){
				for($i=0; $i<sizeof($data); $i++){
					$row[$row_id][$i] = $myF->utf($data[$i]);
				}
				$row[$row_id]['key'] = $row[$row_id][1].'_'.$row[$row_id][2].'_'.$row[$row_id][8].'_'.$row[$row_id][9];
				$row_id ++;
			}

			if (feof($handle)) break;
		}
		fclose($handle);
	}else{
		echo $myF->message($file.'/업로드하신 파일을 찾을 수 없습니다. 잠시후 다시 시도하여 주십시오.', 'Y', 'Y');
		exit;
	}

	$row   = $myF->sortArray($row, 'key', 1);
	$r_cnt = sizeof($row);


	if ($debug){
		echo 'read count : '.$r_cnt;
		echo '<br>---------------------------------------------------------------------------------------<br>';
	}



	/*********************************************************

		배열정보

	**********************************************************
		[0] => 청구반영 여부(엑셀에서 이미지 처리)
		[1] => 서비스 종류
		[2] => 수급자 성명
		[3] => 수급자 주민번호
		[4] => 요양보호사 성명
		[5] => 요양보호사 주민번호
		[6] => 자동여부
		[7] => 상동
		[8] => 시작 YYYY.MM.DD HH:MM
		[9] => 종료 YYYY.MM.DD HH:MM
		[10] => 진행시간
		[11] => 90분여부
		[12] => 사용않함.
	*********************************************************/



	/*********************************************************

		배열정리

	*********************************************************/
	$index = 0;

	for($i=0; $i<$r_cnt; $i++){
		$svc_list[$index] = init_array($row[$i]);

		if (!empty($svc_list[$index]['svc_cd'])){
			/*********************************************************
				수급자
			*********************************************************/
			$sql = 'select m03_jumin
					  from m03sugupja
					 where m03_ccode    = \''.$code.'\'
					   and m03_name  like \''.$svc_list[$index]['c_nm'].'%\'
					   and m03_jumin like \''.$svc_list[$index]['c_cd'].'%\'
					   and m03_del_yn   = \'N\'
					 limit 1';
			$svc_list[$index]['c_cd'] = $conn->get_data($sql);



			/*********************************************************
				요양사
			*********************************************************/
			$sql = 'select m02_yjumin
					  from m02yoyangsa
					 where m02_ccode     = \''.$code.'\'
					   and m02_yname  like \''.$svc_list[$index]['m_nm1'].'%\'
					   and m02_yjumin like \''.$svc_list[$index]['m_cd1'].'%\'
					   and m02_del_yn    = \'N\'
					 limit 1';
			$svc_list[$index]['m_cd1'] = $conn->get_data($sql);

			$svc_list[$index]['conf_from'] = $svc_list[$index]['plan_from'];
			$svc_list[$index]['conf_time'] = $myF->cutOff($svc_list[$index]['plan_time'], 30);

			if ($svc_list[$index]['svc_cd'] == '800'){
				if ($svc_list[$index]['conf_time'] > 60) $svc_list[$index]['conf_time'] = 60;
			}

			$tmp_time = explode(':', $svc_list[$index]['conf_from']);
			$int_time = intval($tmp_time[0]) * 60 + intval($tmp_time[1]) + intval($svc_list[$index]['conf_time']);
			$tmp_hour = floor($int_time / 60);
			$tmp_min  = ($int_time % 60);
			$tmp_hour = ($tmp_hour < 10 ? '0' : '').$tmp_hour;
			$tmp_min  = ($tmp_min < 10 ? '0' : '').$tmp_min;

			$svc_list[$index]['conf_to'] = $tmp_hour.':'.$tmp_min;
			$svc_list[$index]['key']     = $svc_list[$index]['svc_cd'].'_'.$svc_list[$index]['c_nm'].'_'.$svc_list[$index]['m_nm1'].'_'.$svc_list[$index]['date'].'_'.$svc_list[$index]['plan_from'].'_'.$svc_list[$index]['plan_to'];

			unset($tmp_time);
			unset($int_time);
			unset($tmp_hour);
			unset($tmp_min);

			/*********************************************************
				목욕 합치기
			*********************************************************/
			if (!empty($svc_list[$index]['c_cd']) && !empty($svc_list[$index]['m_cd1'])){
				if ($svc_list[$index]['svc_cd']	== '500'						 &&
					$svc_list[$index]['svc_cd'] == $svc_list[$index-1]['svc_cd'] &&
					$svc_list[$index]['c_cd']	== $svc_list[$index-1]['c_cd']	 &&
					$svc_list[$index]['date']	== $svc_list[$index-1]['date']	 ){

					$svc_list[$index-1]['m_cd2'] = $svc_list[$index]['m_cd1'];
					$svc_list[$index-1]['m_nm2'] = $svc_list[$index]['m_nm1'];

					unset($svc_list[$index]);
				}else{
					$str_yymm = substr($svc_list[$index]['date'],0, 7);

					if (!is_numeric(strpos($yymm, $str_yymm)) && !empty($str_yymm)){
						$yymm .= $str_yymm.' / ';
					}

					$index ++;
				}
			}else{
				/*********************************************************
					수급자 및 요양보호사 에러
				*********************************************************/
				$error_id = sizeof($error_list);
				$error_list[$error_id] = $svc_list[$index];

				if (empty($svc_list[$index]['c_cd']))  $error_list[$error_id]['status_cd'] .= 'E1/';
				if (empty($svc_list[$index]['m_cd1'])) $error_list[$error_id]['status_cd'] .= 'E2/';

				unset($svc_list[$index]);
			}
		}else{
			/*********************************************************
				서비스 종류 에러
			*********************************************************/
			unset($svc_list[$index]);
		}
	}

	unset($row);


	/*********************************************************

		 배열 정렬

	*********************************************************/
	$svc_list = $myF->sortArray($svc_list, 'key', 1);

	if ($debug){
		echo '배열 정렬 완료';
		echo '<br>---------------------------------------------------------------------------------------<br>';
	}



	/*********************************************************

		가공

	*********************************************************/
	$first = false;
	$log_seq = 0;
	$log_sql_into = 'insert into nhic_log ('.$conn->_field('nhic_log').') values ';

	if (is_array($svc_list)){
		/*********************************************************
			처음일자와 마지막일자 조회
		*********************************************************/
		foreach($svc_list as $i => $svc){
			if (empty($min_dt) || $min_dt > $svc['date']) $min_dt = $svc['date'];
			if (empty($max_dt) || $max_dt < $svc['date']) $max_dt = $svc['date'];
		}

		$min_dt = str_replace('.', '', $min_dt);
		$max_dt = str_replace('.', '', $max_dt);



		/*********************************************************
			일정조회
		*********************************************************/
		$sql = 'select t01_sugup_date as dt
				,      t01_sugup_fmtime as plan_from
				,      t01_sugup_totime as plan_to
				,      t01_sugup_seq as plan_seq
				,      t01_sugup_soyotime as plan_time
				,      t01_sugup_yoil as weekday

				,      t01_conf_fmtime as conf_from
				,      t01_conf_totime as conf_to
				,      t01_conf_soyotime as conf_time

				,      t01_svc_subcode as svc_cd
				,      t01_status_gbn as stat

				,      t01_suga_code1 as suga_cd
				,      t01_holiday as holiday_yn

				,      t01_toge_umu as family_yn

				,      t01_jumin as c_cd
				,      m03_name as c_nm

				,      t01_yoyangsa_id1 as m_cd1
				,      t01_yname1 as m_nm1
				,      t01_yoyangsa_id2 as m_cd2
				,      t01_yname2 as m_nm2
				  from t01iljung
				 inner join m03sugupja
				    on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 where t01_ccode       = \''.$code.'\'
				   and t01_sugup_date >= \''.$min_dt.'\'
				   and t01_sugup_date <= \''.$max_dt.'\'
				   and t01_del_yn      = \'N\'
				   and ifnull(t01_bipay_umu, \'N\') != \'Y\'
				 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		/*********************************************************
			일정데이타 배열생성
		*********************************************************/
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$id = sizeof($iljung[$row['c_cd']][$row['dt']]);

			$iljung[$row['c_cd']][$row['dt']][$id] = array('plan_from'	=>$row['plan_from']
														  ,'plan_to'	=>$row['plan_to']
														  ,'plan_seq'	=>$row['plan_seq']
														  ,'plan_time'	=>$row['plan_time']

														  ,'conf_from'	=>$row['conf_from']
														  ,'conf_to'	=>$row['conf_to']
														  ,'conf_time'	=>$row['conf_time']

														  ,'svc_cd'		=>$row['svc_cd']
														  ,'stat'		=>$row['stat']

														  ,'suga_cd'	=>$row['suga_cd']
														  ,'holiday_yn'	=>$row['holiday_yn']
														  ,'family_yn'	=>$row['family_yn']

														  ,'c_nm'		=>$row['c_nm']

														  ,'m_cd1'		=>$row['m_cd1']
														  ,'m_nm1'		=>$row['m_nm1']
														  ,'m_cd2'		=>$row['m_cd2']
														  ,'m_nm2'		=>$row['m_nm2']

														  ,'link_id'	=>0);
		}

		$conn->row_free();



		/*********************************************************
			건보 실적 데이타와 일정데이타 연결
		*********************************************************/
		$link_id = 1;

		foreach($svc_list as $s => $svc){
			if ($svc['link_id'] == 0){
				$cd = $svc['c_cd'];
				$dt = str_replace('.', '', $svc['date']);

				/*********************************************************
					데이타 연결 시작
				*********************************************************/
				if (is_array($iljung[$cd][$dt])){
					foreach($iljung[$cd][$dt] as $i => $cal){
						/*
						if ($cal['svc_cd']  == $svc['svc_cd'] &&
							$cal['m_cd1']   == $svc['m_cd1'] &&
							$cal['m_cd2']   == $svc['m_cd2'] &&
							$cal['link_id'] == 0){
							$link_is = true;
						}else if ($cal['svc_cd']  == $svc['svc_cd'] &&
								  $cal['m_cd1']   == $svc['m_cd2'] &&
								  $cal['m_cd2']   == $svc['m_cd1'] &&
								  $cal['link_id'] == 0){
							$link_is = true;
						}else{
							$link_is = false;
						}
						*/
						$link_is = false;

						if ($cal['svc_cd']  == $svc['svc_cd']){
							if (($cal['m_cd1'].'_'.$cal['m_cd2'] == $svc['m_cd1'].'_'.$svc['m_cd2']) ||
								($cal['m_cd1'].'_'.$cal['m_cd2'] == $svc['m_cd2'].'_'.$svc['m_cd1']) ){
								if ($cal['link_id'] == 0){
									$link_is = true;
								}
							}
						}

						if ($link_is){
							/*********************************************************
								건보실적 데이타와 계획 데이타를 연결한다.
							*********************************************************/
							$svc_list[$s]['plan_from'] = $cal['plan_from'];
							$svc_list[$s]['plan_to']   = $cal['plan_to'];
							$svc_list[$s]['plan_seq']  = $cal['plan_seq'];
							$svc_list[$s]['plan_time'] = $cal['plan_time'];
							$svc_list[$s]['family_yn'] = $cal['family_yn'];
							$svc_list[$s]['status_cd'] = $cal['stat'];
							$svc_list[$s]['link_id']   = $link_id;
							$iljung[$cd][$dt][$i]['link_id'] = $link_id;

							$link_id ++;

							/*********************************************************
								동거가족인 경우 진행시간 확인
							*********************************************************/
							if ($svc_list[$s]['family_yn'] == 'Y'){
								if ($svc_list[$s]['conf_time'] > $svc_list[$s]['plan_time']){
									$svc_list[$s]['conf_time'] = $svc_list[$s]['plan_time'];

									$conf_from = $myF->time2min($svc_list[$s]['conf_from']);
									$conf_to   = $myF->min2time($conf_from + $svc_list[$s]['conf_time']);

									$svc_list[$s]['conf_to'] = str_replace(':', '', $conf_to);
								}
							}


							/*********************************************************
								수가정보
								[code]			=> 수가코드
								[name]			=> 수가명
								[cost]			=> 단가
								[evening_cost]	=> 연장단가
								[night_cost]	=> 야간단가
								[total_cost]	=> 하볘
								[sudang_pay]	=> 수당
								[evening_time]	=> 연장시간
								[night_time]	=> 야간시간
								[evening_yn]	=> 연장여부
								[night_yn]		=> 야간여부
								[holiday_yn]	=> 휴일여부
							*********************************************************/
							$svc_list[$s]['suga_if'] = $conn->_find_suga_($code
																		 ,$svc_list[$s]['svc_cd']
																		 ,str_replace('.', '', $svc_list[$s]['date'])
																		 ,str_replace(':', '', $svc_list[$s]['conf_from'])
																		 ,str_replace(':', '', $svc_list[$s]['conf_to'])
																		 ,$svc_list[$s]['conf_time']
																		 ,$svc_list[$s]['family_yn']);

							break;
						}
					}
				}else{
					/*********************************************************
						 계획을 찾을 수 없음
					*********************************************************/
					$error_id = sizeof($error_list);
					$error_list[$error_id] = array('kind'		=>'0'
												  ,'svc_cd'		=>$svc['svc_cd']
												  ,'svc_nm'		=>''
												  ,'c_cd'		=>$svc['c_cd']
												  ,'c_nm'		=>$svc['c_nm']
												  ,'m_cd1'		=>$svc['m_cd1']
												  ,'m_nm1'		=>$svc['m_nm1']
												  ,'m_cd2'		=>$svc['m_cd2']
												  ,'m_nm2'		=>$svc['m_nm2']
												  ,'date'		=>$svc['date']
												  ,'plan_from'	=>''
												  ,'plan_to'	=>''
												  ,'plan_time'	=>0
												  ,'plan_seq'	=>0
												  ,'conf_from'	=>$svc['conf_from']
												  ,'conf_to'	=>$svc['conf_to']
												  ,'conf_time'	=>$svc['conf_time']
												  ,'work_time'	=>$svc['work_time']
												  ,'join_yn'	=>'N'
												  ,'90_yn'		=>'N'
												  ,'family_yn'	=>'N'
												  ,'status_cd'	=>'E3/'
												  ,'link_id'	=>0
												  ,'suga_if'	=>null
												  ,'claim_yn'	=>'N'
												  ,'key'		=>'');
				}
				/*********************************************************
					데이타 연결 종료
				*********************************************************/
			}
		}


		/*********************************************************
			마스터 로그 순번
		*********************************************************/
		if ($gbn == '2'){
			$nhic_seq = $seq;
		}else{
			$sql = 'select ifnull(max(nhic_seq), 0) + 1
					  from nhic_log_mst
					 where org_no  = \''.$code.'\'
					   and nhic_dt = \''.$reg_dt.'\'';

			$nhic_seq = $conn->get_data($sql);
		}



		/*********************************************************
			마스터 로그 저장
		*********************************************************/
		$sql = 'insert into nhic_log_mst ('.$conn->_field('nhic_log_mst').') values (
				 \''.$code.'\'
				,\''.$reg_dt.'\'
				,\''.$nhic_seq.'\'
				,\''.$min_dt.'\'
				,\''.$max_dt.'\'
				,\''.$file.'\'
				,\''.$f_name.'\'
				,\''.$f_type.'\'
				,\''.$f_size.'\'
				,\''.$today.'\'
				,\''.$reg_id.'\')';
		$sql_mst = $sql;



		/*********************************************************
			로그저장
		*********************************************************/
		foreach($svc_list as $s => $svc){
			if ($svc['link_id'] > 0){
				/*********************************************************
					저장데이타
				*********************************************************/
				$log_sql[$log_seq] = $log_sql_into.get_query($code, $reg_dt, $nhic_seq, $s, $svc);
				$log_seq ++;

				$first = true;
			}else{
				/*********************************************************
					계획에러
				*********************************************************/
				$error_id = sizeof($error_list);
				$error_list[$error_id] = $svc;
				$error_list[$error_id]['plan_from']  = '';
				$error_list[$error_id]['plan_to']    = '';
				$error_list[$error_id]['plan_time']  = '';
				$error_list[$error_id]['status_cd'] .= 'E3/';
				$error_list[$error_id]['claim_yn']   = 'N';
			}
		}
	}else{
		echo '<script language=\'javascript\'>
				alert(\'선택하신 TEXT파일에서 일정을 찾을 수 없습니다.\');
				self.close();
			  </script>';
		exit;
	}

	if ($debug){
		echo '가공 완료';
		echo '<br>---------------------------------------------------------------------------------------<br>';
	}


	/*********************************************************

		실적없는 에러리스트

	*********************************************************/
	if (is_array($iljung)){
		foreach($iljung as $i => $iljung_jumin){
			foreach($iljung_jumin as $j => $iljung_jumin_date){
				foreach($iljung_jumin_date as $k => $cal){
					if ($cal['link_id'] == 0){
						$error_id = sizeof($error_list);
						$error_list[$error_id] = array('kind'		=>'0'
													  ,'svc_cd'		=>$cal['svc_cd']
													  ,'svc_nm'		=>''
													  ,'c_cd'		=>$i
													  ,'c_nm'		=>$cal['c_nm']
													  ,'m_cd1'		=>$cal['m_cd1']
													  ,'m_nm1'		=>$cal['m_nm1']
													  ,'m_cd2'		=>$cal['m_cd2']
													  ,'m_nm2'		=>$cal['m_nm2']
													  ,'date'		=>$j
													  ,'plan_from'	=>$cal['plan_from']
													  ,'plan_to'	=>$cal['plan_to']
													  ,'plan_time'	=>$cal['plan_time']
													  ,'plan_seq'	=>$cal['plan_seq']
													  ,'conf_from'	=>''
													  ,'conf_to'	=>''
													  ,'conf_time'	=>0
													  ,'work_time'	=>0
													  ,'join_yn'	=>'N'
													  ,'90_yn'		=>'N'
													  ,'family_yn'	=>'N'
													  ,'status_cd'	=>$cal['stat'].'/E4/'
													  ,'link_id'	=>0
													  ,'suga_if'	=>null
													  ,'claim_yn'	=>'N'
													  ,'check_yn'	=>'N'
													  ,'key'		=>'');
					}
				}
			}
		}
	}




	/*********************************************************

		에러리스트

		E1 : 수급자에러
		E2 : 요양사에러
		E3 : 계획없음

	*********************************************************/
	if (is_array($error_list)){
		foreach($error_list as $e => $error){
			#$sql .= ($first ? ',' : '');
			#$sql .= get_query($code, $reg_dt, $nhic_seq, $s+$e+1, $error);

			$log_sql[$log_seq] = $log_sql_into.get_query($code, $reg_dt, $nhic_seq, $s+$e+1, $error);
			$log_seq ++;

			$first = true;
		}
	}

	#$sql_log = $sql;



	/*********************************************************

		메모리 해제

	*********************************************************/
	unset($svc_list);
	unset($iljung);
	unset($error_list);



	/*********************************************************

		업로드 파일 삭제

	*********************************************************/
	#if (is_file($file)) @unlink($file);



	if ($debug){
		echo '준비 완료';
		echo '<br>---------------------------------------------------------------------------------------<br>';
	}



	/*********************************************************

		로그저장

	*********************************************************/
	$conn->begin();

	if ($gbn == '2'){
		$sql = 'delete
				  from nhic_log
				 where org_no  = \''.$code.'\'
				   and mst_dt  = \''.$reg_dt.'\'
				   and mst_seq = \''.$nhic_seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->err_back();
			if ($debug){
				echo 'error 1';
				echo '<br>---------------------------------------------------------------------------------------<br>';
			}
			if ($conn->mode == 1) exit;
		}

		$sql = 'delete
				  from nhic_log_mst
				 where org_no   = \''.$code.'\'
				   and nhic_dt  = \''.$reg_dt.'\'
				   and nhic_seq = \''.$nhic_seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->err_back();
			if ($debug){
				echo 'error 2';
				echo '<br>---------------------------------------------------------------------------------------<br>';
			}
			if ($conn->mode == 1) exit;
		}
	}

	if (!empty($sql_mst)){
		if (!$conn->execute($sql_mst)){
			$conn->rollback();
			$conn->err_back();
			if ($debug){
				echo 'error 3';
				echo '<br>---------------------------------------------------------------------------------------<br>';
			}
			if ($conn->mode == 1) exit;
		}
	}

	if (is_array($log_sql)){
		$log_sql_cnt = sizeof($log_sql);

		for($i=0; $i<$log_sql_cnt; $i++){
			if (!$conn->execute($log_sql[$i])){
				$conn->rollback();
				$conn->err_back();
				if ($debug){
					echo $conn->error_msg;
					echo '<br>---------------------------------------------------------------------------------------<br>';
				}
				if ($conn->mode == 1) exit;
			}
		}
	}

	/*
	if (!empty($sql_log)){
		if (!$conn->execute($sql_log)){
			$conn->rollback();
			$conn->err_back();
			if ($debug){
				echo $conn->error_msg;
				echo '<br>---------------------------------------------------------------------------------------<br>';
			}
			if ($conn->mode == 1) exit;
		}
	}
	*/

	$conn->commit();
?>


<script language='javascript'>
<!--

window.onload = function(){
	if (confirm('TEXT파일이 업로드되었습니다.\n\n지금 바로 리스트를 확인하시겠습니까?')){
		parent.document.getElementById('seq').value  = '<?=$nhic_seq;?>';
		parent.document.getElementById('date').value = '<?=$reg_dt;?>';
		parent.close_loading();
	}else{
		parent.self.close();
	}
}

-->
</script>


<?
	include_once('../inc/_footer.php');


	/*********************************************************

		배열 초기화

	*********************************************************/
	function init_array($tmp_arr, $text_yn = 'Y'){
		switch(trim($tmp_arr[1])){
			case '방문요양': $svc_cd = '200'; break;
			case '방문목욕': $svc_cd = '500'; break;
			case '방문간호': $svc_cd = '800'; break;
		}

		if ($text_yn == 'Y'){
			$claim_yn = 'Y';
		}else{
			$claim_yn = 'N';
		}

		$arr = array('kind'		=>'0'
					,'svc_cd'	=>$svc_cd
					,'svc_nm'	=>trim($tmp_arr[1])
					,'c_cd'		=>substr(str_replace('-', '', $tmp_arr[3]), 0, 7)
					,'c_nm'		=>$tmp_arr[2]
					,'m_cd1'	=>substr(str_replace('-', '', $tmp_arr[5]), 0, 7)
					,'m_nm1'	=>$tmp_arr[4]
					,'m_cd2'	=>''
					,'m_nm2'	=>''
					,'date'		=>substr($tmp_arr[8], 0, 10)
					,'plan_from'=>trim(substr($tmp_arr[8], 10))
					,'plan_to'	=>trim(substr($tmp_arr[9], 10))
					,'plan_time'=>$tmp_arr[10]
					,'plan_seq'	=>0
					,'conf_from'=>''
					,'conf_to'	=>''
					,'conf_time'=>0
					,'work_time'=>$tmp_arr[10]
					,'join_yn'	=>$tmp_arr[6]
					,'90_yn'	=>$tmp_arr[11]
					,'family_yn'=>'N'
					,'status_cd'=>''
					,'link_id'	=>0
					,'suga_if'	=>null
					,'claim_yn'	=>$claim_yn
					,'apply_yn'	=>'N'
					,'check_yn'	=>'N'
					,'key'		=>'');

		return $arr;
	}



	/*********************************************************

		저장 쿼리

	*********************************************************/
	function get_query($code, $dt, $seq, $no, $svc){
		$sql .= '(\''.$code.'\'
				 ,\''.$dt.'\'
				 ,\''.$seq.'\'
				 ,\''.$no.'\'
				 ,\''.$svc['kind'].'\'
				 ,\''.$svc['claim_yn'].'\'
				 ,\''.$svc['svc_cd'].'\'
				 ,\''.$svc['c_cd'].'\'
				 ,\''.$svc['c_nm'].'\'
				 ,\''.$svc['m_cd1'].'\'
				 ,\''.$svc['m_nm1'].'\'
				 ,\''.$svc['m_cd2'].'\'
				 ,\''.$svc['m_nm2'].'\'
				 ,\''.str_replace('.', '', $svc['date']).'\'
				 ,\''.str_replace(':', '', $svc['plan_from']).'\'
				 ,\''.str_replace(':', '', $svc['plan_to']).'\'
				 ,\''.$svc['plan_time'].'\'
				 ,\''.$svc['plan_seq'].'\'
				 ,\''.str_replace(':', '', $svc['conf_from']).'\'
				 ,\''.str_replace(':', '', $svc['conf_to']).'\'
				 ,\''.$svc['conf_time'].'\'
				 ,\''.$svc['work_time'].'\'
				 ,\''.$svc['join_yn'].'\'
				 ,\''.$svc['90_yn'].'\'
				 ,\''.$svc['family_yn'].'\'
				 ,\''.$svc['status_cd'].'\'
				 ,\''.$svc['suga_if']['code'].'\'
				 ,\''.$svc['suga_if']['name'].'\'
				 ,\''.$svc['suga_if']['cost'].'\'
				 ,\''.$svc['suga_if']['evening_cost'].'\'
				 ,\''.$svc['suga_if']['night_cost'].'\'
				 ,\''.$svc['suga_if']['total_cost'].'\'
				 ,\''.$svc['suga_if']['sudang_pay'].'\'
				 ,\''.$svc['suga_if']['evening_time'].'\'
				 ,\''.$svc['suga_if']['night_time'].'\'
				 ,\''.$svc['suga_if']['evening_yn'].'\'
				 ,\''.$svc['suga_if']['night_yn'].'\'
				 ,\''.$svc['suga_if']['holiday_yn'].'\'
				 ,\''.$svc['apply_yn'].'\'
				 ,\''.$svc['check_yn'].'\')';

		return $sql;
	}
?>
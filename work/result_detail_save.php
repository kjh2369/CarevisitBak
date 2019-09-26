<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	/*
	 * mode 설정
	 * 1 : 일실적등록(수급자)
	 * 2 : 월실적등록(수급자)
	 * 3 : 월실적등록(요양보호사)
	 */

	// 기관정보
	$mode	= $_POST['mode'];	//모드
	$code	= $_POST['code'];	//기관코드
	$kind	= $_POST['kind'];	//기관분류
	$year	= $_POST['year'];	//년
	$month	= $_POST['month'];	//월
	$day	= $_POST['day'];	//일

	$conn->mode = 1;

	switch($mode){
	case 1:
		$temp_modify_yn = 'D';
		break;
	case 2:
		$temp_modify_yn = 'M';
		break;
	case 3:
		$temp_modify_yn = 'Y';
		break;
	}

	$svc_kind = $_POST['svc_kind'];

	$target = $ed->de($_POST['jumin']);

	// 수급자 배열
	$client	= $_POST['client'];	//수급자

	// 서비스 배열
	$svc_code	= $_POST['svc_code'];	//서비스구분

	// 계획 배열
	$plan_date	= $_POST['plan_date'];	//계획일자
	$plan_from	= $_POST['plan_from'];	//계획시작시간
	$plan_to	= $_POST['plan_to'];		//계획종료시간
	$plan_time	= $_POST['plan_time'];	//계획진행시간
	$plan_seq	= $_POST['plan_seq'];		//계획순번

	// 확정 배열
	$conf_from	= $_POST['conf_from'];	//확정시작시간
	$conf_to	= $_POST['conf_to'];		//확정종료시간
	$conf_time	= $_POST['conf_time'];	//확정진행시간

	// 확정 수가 배열
	$suga_code	= $_POST['suga_code'];	//확정수가코드
	$suga_price	= $_POST['suga_price'];	//확정수가금액

	// 확정 담당자
	$mem_cd = $_POST['conf_mem_cd'];
	$mem_nm = $_POST['conf_mem_nm'];

	// 상태 배열
	$status_gbn	= $_POST['status_gbn'];	//상태

	// 변경여부 배열
	$change_flag	= $_POST['change_flag'];	//변경여부

	// 취소여부 배열
	$cancel_flag	= $_POST['cancel_flag'];	//취소여부

	// 수당배열
	$sql = "select m21_mcode2, m21_svalue
			  from m21sudang
			 where m21_mcode = '$code'";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$sudang[$row['m21_mcode2']] = $row['m21_svalue'];
	}

	$conn->row_free();

	$conn->begin(); //트렌젝션 시작

	for($i=0; $i<sizeof($change_flag); $i++){
		if ($change_flag[$i] == 'Y'){ //변경된 데이타만 저장한다.
			$jumin = $ed->de($client[$i]);	//주민번호

			if ($cancel_flag[$i] == 'N'){
				/*
				if ($status_gbn[$i] == '1'){
					$modify_yn = 'N';
				}else{
					$modify_yn = $temp_modify_yn;
				}
				*/
				$modify_yn = $temp_modify_yn;

				// 상태가 에러인 경우 히스토리를 남긴다.
				if ($status_gbn[$i] == 'C'){
					$sql = "insert into e01iljung (e01_ccode,e01_mkind,e01_jumin,e01_sugup_date,e01_sugup_fmtime,e01_sugup_seq,e01_sugup_totime,e01_sugup_soyotime,e01_sugup_yoil,e01_wrk_date,e01_wrk_fmtime,e01_wrk_totime,e01_svc_subcode,e01_svc_subcd,e01_status_gbn,e01_svc_name,e01_toge_umu,e01_bipay_umu,e01_time_doub,e01_yoyangsa_id1,e01_yoyangsa_id2,e01_yoyangsa_id3,e01_yoyangsa_id4,e01_yoyangsa_id5,e01_yname1,e01_yname2,e01_yname3,e01_yname4,e01_yname5,e01_suga_code1,e01_suga,e01_suga_over,e01_suga_night,e01_suga_tot,e01_ysigup,e01_plan_work,e01_plan_sudang,e01_plan_cha,e01_act_work,e01_act_sudang,e01_act_cha,e01_del_yn,e01_trans_yn,e01_mobile_yn,e01_modify_yn,e01_modify_pos,e01_car_no,e01_e_time,e01_n_time,e01_ysudang_yn,e01_ysudang,e01_ysudang_yul1,e01_ysudang_yul2,e01_conf_date,e01_conf_fmtime,e01_conf_totime,e01_conf_soyotime,e01_conf_suga_code,e01_conf_suga_value,e01_holiday,e01_gps_x,e01_gps_y,e01_sudang_conf_yn,e01_mem_cd1,e01_mem_cd2,e01_mem_nm1,e01_mem_nm2)
							select t01_ccode,t01_mkind,t01_jumin,t01_sugup_date,t01_sugup_fmtime,t01_sugup_seq,t01_sugup_totime,t01_sugup_soyotime,t01_sugup_yoil,t01_wrk_date,t01_wrk_fmtime,t01_wrk_totime,t01_svc_subcode,t01_svc_subcd,t01_status_gbn,t01_svc_name,t01_toge_umu,t01_bipay_umu,t01_time_doub,t01_yoyangsa_id1,t01_yoyangsa_id2,t01_yoyangsa_id3,t01_yoyangsa_id4,t01_yoyangsa_id5,t01_yname1,t01_yname2,t01_yname3,t01_yname4,t01_yname5,t01_suga_code1,t01_suga,t01_suga_over,t01_suga_night,t01_suga_tot,t01_ysigup,t01_plan_work,t01_plan_sudang,t01_plan_cha,t01_act_work,t01_act_sudang,t01_act_cha,t01_del_yn,t01_trans_yn,t01_mobile_yn,t01_modify_yn,t01_modify_pos,t01_car_no,t01_e_time,t01_n_time,t01_ysudang_yn,t01_ysudang,t01_ysudang_yul1,t01_ysudang_yul2,t01_conf_date,t01_conf_fmtime,t01_conf_totime,t01_conf_soyotime,t01_conf_suga_code,t01_conf_suga_value,t01_holiday,t01_gps_x,t01_gps_y,t01_sudang_conf_yn,t01_mem_cd1,t01_mem_cd2,t01_mem_nm1,t01_mem_nm2
							  from t01iljung
							 where t01_ccode        = '".$code."'
							   and t01_mkind        = '".$svc_kind[$i]."'
							   and t01_jumin        = '".$jumin."'
							   and t01_sugup_date   = '".$plan_date[$i]."'
							   and t01_sugup_fmtime = '".str_replace(':', '', $plan_from[$i])."'
							   and t01_sugup_seq    = '".$plan_seq[$i]."'";
					//echo $sql.'<br><br><br>';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo '1';
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}

				// 일정계획 테이블 수정
				/*
					   t01_wrk_date			= '".$plan_date[$i]."'
				,      t01_wrk_fmtime		= '".str_replace(':', '', $conf_from[$i])."'
				,      t01_wrk_totime		= '".str_replace(':', '', $conf_to[$i])."'
				,
				 */
				$sql = "update t01iljung
						   set t01_conf_date		= '".$plan_date[$i]."'
						,      t01_conf_fmtime		= '".str_replace(':', '', $conf_from[$i])."'
						,      t01_conf_totime		= '".str_replace(':', '', $conf_to[$i])."'
						,      t01_conf_soyotime	= '".$conf_time[$i]."'
						,      t01_conf_suga_code	= '".$suga_code[$i]."'
						,      t01_conf_suga_value	= '".$suga_price[$i]."'
						,      t01_yoyangsa_id1     = '".$ed->de($mem_cd[$i])."'
						,      t01_yname1           = '".$mem_nm[$i]."'
						,      t01_status_gbn		= '1'
						,      t01_modify_yn		= '".$modify_yn."'
						,      t01_trans_yn			= 'N'
						 where t01_ccode			= '".$code."'
						   and t01_mkind			= '".$svc_kind[$i]."'
						   and t01_jumin			= '".$jumin."'
						   and t01_sugup_date		= '".$plan_date[$i]."'
						   and t01_sugup_fmtime		= '".str_replace(':', '', $plan_from[$i])."'
						   and t01_sugup_seq		= '".$plan_seq[$i]."'";
				//echo $sql.'<br><br><br>';
				/*,      t01_ysudang			= case when t01_svc_subcode != '200' then case when t01_ysudang_yn = 'Y' then '".$sugang[$suga_code[$i]]."' else t01_ysudang end else t01_ysudang end*/

				if (!$conn->execute($sql)){
					$conn->rollback();
					echo '2';
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}

				// 일정 요양사 실적으로 저장
				$sql = "select t01_sugup_date
						,      t01_sugup_fmtime
						,      t01_sugup_seq
						,      t01_sugup_totime
						,      t01_sugup_soyotime
						,      t01_sugup_yoil
						,      t01_holiday
						,      t01_svc_subcode
						,      t01_status_gbn
						,      t01_toge_umu
						,      t01_bipay_umu
						,      t01_suga_code1
						,      t01_suga
						,      t01_suga_over
						,      t01_suga_night
						,      t01_suga_tot
						,      t01_e_time
						,      t01_n_time
						,      t01_ysudang_yn
						,      t01_ysudang
						,      t01_ysudang_yul1
						,      t01_ysudang_yul2
						,      t01_yoyangsa_id1
						,      t01_yoyangsa_id2
						,      t01_del_yn
						  from t01iljung
						 where t01_ccode		= '".$code."'
						   and t01_mkind		= '".$svc_kind[$i]."'
						   and t01_jumin		= '".$jumin."'
						   and t01_sugup_date   = '".$plan_date[$i]."'
						   and t01_sugup_fmtime = '".str_replace(':', '', $plan_from[$i])."'
						   and t01_sugup_seq    = '".$plan_seq[$i]."'";
				$temp_array = $conn->get_array($sql);

				$temp_y_list[0] = $temp_array['t01_yoyangsa_id1'];
				$temp_y_list[1] = $temp_array['t01_yoyangsa_id2'];

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
									 '".$code."'
									,'".$svc_kind[$i]."'
									,'".$temp_array['t01_yoyangsa_id'.($k+1)]."'
									,'".$jumin."'
									,'".$temp_array['t01_sugup_date']."'
									,'".$temp_array['t01_sugup_fmtime']."'
									,'".$temp_array['t01_sugup_seq']."'
									,'".$temp_array['t01_sugup_totime']."'
									,'".$temp_array['t01_sugup_soyotime']."'
									,'".$temp_array['t01_sugup_yoil']."'
									,'".$temp_array['t01_holiday']."'
									,'".$temp_array['t01_svc_subcode']."'
									,'1'
									,'".$temp_array['t01_toge_umu']."'
									,'".$temp_array['t01_bipay_umu']."'
									,'".$temp_array['t01_suga_code1']."'
									,'".$temp_array['t01_suga']."'
									,'".$temp_array['t01_suga_over']."'
									,'".$temp_array['t01_suga_night']."'
									,'".$temp_array['t01_suga_tot']."'
									,'".$temp_array['t01_e_time']."'
									,'".$temp_array['t01_n_time']."'
									,'".$temp_array['t01_ysudang_yn']."'
									,'".$temp_array['t01_ysudang']."'
									,'".$temp_array['t01_ysudang_yul'.($k+1)]."'
									,'".$plan_date[$i]."'
									,'".str_replace(':', '', $conf_from[$i])."'
									,'".str_replace(':', '', $conf_to[$i])."'
									,'".$conf_time[$i]."'
									,'".$suga_code[$i]."'
									,'".$suga_price[$i]."'
									,'".$modify_yn."'
									,'".($k+1)."')";

							//echo $sql.'<br><br><br>';

							if (!$conn->execute($sql)){
								$conn->rollback();
								echo '3';
								echo $conn->err_back();
								if ($conn->mode == 1) exit;
							}
						}else{
							$sql = "delete
									  from t11iljung_y
									 where t11_ccode        = '".$code."'
									   and t11_mkind        = '".$svc_kind[$i]."'
									   and t11_jumin_y      = '".$temp_array['t01_yoyangsa_id'.($k+1)]."'
									   and t11_jumin_s      = '".$jumin."'
									   and t11_sugup_date   = '".$temp_array['t01_sugup_date']."'
									   and t11_sugup_fmtime = '".$temp_array['t01_sugup_fmtime']."'
									   and t11_sugup_seq    = '".$temp_array['t01_sugup_seq']."'";
							//echo $sql.'<br><br><br>';

							if (!$conn->execute($sql)){
								$conn->rollback();
								echo '4';
								echo $conn->err_back();
								if ($conn->mode == 1) exit;
							}
						}
					}
				}
			}else{
				// 입력을 취소한다.
				$sql = "update t01iljung
						   set t01_conf_date		= ''
						,      t01_conf_fmtime		= ''
						,      t01_conf_totime		= ''
						,      t01_conf_soyotime	= '0'
						,      t01_conf_suga_code	= t01_suga_code1
						,      t01_conf_suga_value	= t01_suga_tot
						,      t01_yoyangsa_id1     = t01_mem_cd1
						,      t01_yname1           = t01_mem_nm1
						,      t01_status_gbn		= '0'
						,      t01_modify_yn		= 'N'
						,      t01_trans_yn			= 'N'
						 where t01_ccode			= '".$code."'
						   and t01_mkind			= '".$svc_kind[$i]."'
						   and t01_jumin			= '".$jumin."'
						   and t01_sugup_date		= '".$plan_date[$i]."'
						   and t01_sugup_fmtime		= '".str_replace(':', '', $plan_from[$i])."'
						   and t01_sugup_seq		= '".$plan_seq[$i]."'
						   and t01_del_yn           = 'N'";
				//echo $sql.'<br><br><br>';
				/*,      t01_ysudang			= case when t01_svc_subcode != '200' then case when t01_ysudang_yn = 'Y' then '".$sugang[$suga_code[$i]]."' else t01_ysudang end else t01_ysudang end*/

				if (!$conn->query($sql)){
					$conn->rollback();
					echo $conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}
		}
	}

	$conn->commit(); //트렌젝션 종료

	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert("<?=$myF->message('ok','N');?>");

	if ('<?=$conn->mode;?>' == '1')
		location.replace("result_detail.php?mode=<?=$mode;?>&code=<?=$code;?>&kind=<?=$kind;?>&year=<?=$year;?>&month=<?=$month;?>&day=<?=$day;?>&jumin=<?=$ed->en($target);?>");
</script>
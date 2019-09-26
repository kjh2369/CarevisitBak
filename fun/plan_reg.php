<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];//$_POST['code'];
	$jumin = $_POST['jumin'];
	$kind  = $_POST['kind'];
	$svcCd = $_POST['svcCd'];
	$date  = $_POST['date'];
	$time  = $_POST['time'];
	$ynFamily = $_POST['ynFamily'];
	$family90 = $_POST['family90'];

	parse_str($_POST['suga'],$suga);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//로그 조회
	$sql = 'select lc_family_yn as family_yn
			,      lc_m_cd1 as m_cd1
			,      lc_m_cd2 as m_cd2
			,      lc_m_nm1 as m_nm1
			,      lc_m_nm2 as m_nm2
			,      lc_suga_cd as suga_cd
			,      lc_suga_cost as suga_cost
			,      lc_suga_cost_e as suga_cost_e
			,      lc_suga_cost_n as suga_cost_n
			,      lc_suga_cost_t as suga_cost_t
			,      lc_suga_time_e as suga_time_e
			,      lc_suga_time_n as suga_time_n
			,      lc_conf_from as conf_from
			,      lc_conf_to as conf_to
			,      lc_conf_time as conf_time
			,      lc_suga_holiday_yn as holiday_yn
			  from longcare_log
			 where org_no       = \''.$code.'\'
			   and lc_kind      = \''.$kind.'\'
			   and lc_svc_cd    = \''.$svcCd.'\'
			   and lc_c_cd      = \''.$jumin.'\'
			   and lc_dt        = \''.$date.'\'
			   and lc_plan_from = \''.$time.'\'';
	$loLog = $conn->get_array($sql);

	if ($ynFamily == 'Y'){
		if ($family90 == 'Y'){
			$liConfTime = 90;
		}else{
			$liConfTime = 60;
		}

		if ($loLog['conf_time'] > $liConfTime){
			$loLog['conf_time'] = $liConfTime;
		}
		$loLog['conf_to'] = Str_Replace(':','',$myF->min2time($myF->time2min($loLog['conf_from']) + $loLog['conf_time']));
	}

	$conn->begin();

	//로그 수정
	$sql = 'update longcare_log
			   set lc_status    = \'1\'
			 where org_no       = \''.$code.'\'
			   and lc_kind      = \''.$kind.'\'
			   and lc_svc_cd    = \''.$svcCd.'\'
			   and lc_c_cd      = \''.$jumin.'\'
			   and lc_dt        = \''.$date.'\'
			   and lc_plan_from = \''.$time.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 8;
		 exit;
	}

	//계획시간
	$lsPlanFrom = $loLog['conf_from'];
	$lsPlanTime = $loLog['conf_time'];
	$lsPlanTo   = Str_Replace(':','',$myF->min2time($myF->time2min($lsPlanFrom)+$lsPlanTime));

	//일정조회
	$sql = 'select ifnull(max(t01_sugup_seq),0) + 1
			  from t01iljung
			 where t01_ccode        = \''.$code.'\'
			   and t01_mkind        = \''.$kind.'\'
			   and t01_jumin        = \''.$jumin.'\'
			   and t01_sugup_date   = \''.$date.'\'
			   and t01_sugup_fmtime = \''.$lsPlanFrom.'\'';
	$liSeq = $conn->get_data($sql);

	if ($svcCd == '200'){
		$lsExtraYn   = 'N';
		$liExtraPay  = 0;
		$liExtraVal1 = 0;
		$liExtraVal2 = 0;
	}else{
		$sql = 'SELECT m21_svalue
				  FROM m21sudang
				 WHERE m21_mcode  = \''.$code.'\'
				   AND m21_mcode2 = \''.$suga['code'].'\'';
		$lsExtraYn  = 'Y';
		$liExtraPay = $conn->get_data($sql);

		if ($svcCd == '500'){
			$liExtraVal1 = 50;
			$liExtraVal2 = 50;
		}else{
			$liExtraVal1 = $liExtraPay;
			$liExtraVal2 = 0;
		}
	}

	//저장
	$sql = 'insert into t01iljung (
			 t01_ccode
			,t01_mkind
			,t01_jumin
			,t01_sugup_date
			,t01_sugup_fmtime
			,t01_sugup_totime
			,t01_sugup_soyotime
			,t01_sugup_seq
			,t01_sugup_yoil
			,t01_svc_subcode
			,t01_status_gbn
			,t01_toge_umu
			,t01_yoyangsa_id1
			,t01_yoyangsa_id2
			,t01_yname1
			,t01_yname2
			,t01_suga_code1
			,t01_suga
			,t01_suga_over
			,t01_suga_night
			,t01_suga_tot
			,t01_del_yn
			,t01_trans_yn
			,t01_modify_pos
			,t01_e_time
			,t01_n_time
			,t01_conf_date
			,t01_conf_fmtime
			,t01_conf_totime
			,t01_conf_soyotime
			,t01_conf_suga_code
			,t01_conf_suga_value
			,t01_holiday
			,t01_mem_cd1
			,t01_mem_cd2
			,t01_mem_nm1
			,t01_mem_nm2
			,t01_yname5

			,t01_ysudang_yn
			,t01_ysudang
			,t01_ysudang_yul1
			,t01_ysudang_yul2

			,t01_request) values (
			 \''.$code.'\'
			,\''.$kind.'\'
			,\''.$jumin.'\'
			,\''.$date.'\'
			,\''.$lsPlanFrom.'\'
			,\''.$lsPlanTo.'\'
			,\''.$lsPlanTime.'\'
			,\''.$liSeq.'\'
			,\''.date('w',strtotime($date)).'\'
			,\''.$svcCd.'\'
			,\'1\'
			,\''.$ynFamily/*$loLog['family_yn']*/.'\'
			,\''.$loLog['m_cd1'].'\'
			,\''.$loLog['m_cd2'].'\'
			,\''.$loLog['m_nm1'].'\'
			,\''.$loLog['m_nm2'].'\'
			,\''.$suga['code'].'\'
			,\''.$suga['cost'].'\'
			,\''.$suga['costEvening'].'\'
			,\''.$suga['costNight'].'\'
			,\''.$suga['costTotal'].'\'
			,\'N\'
			,\'N\'
			,\'L\'
			,\''.$suga['timeEvening'].'\'
			,\''.$suga['timeNight'].'\'
			,\''.$date.'\'
			,\''.$loLog['conf_from'].'\'
			,\''.$loLog['conf_to'].'\'
			,\''.$loLog['conf_time'].'\'
			,\''.$suga['code'].'\'
			,\''.$suga['costTotal'].'\'
			,\''.$loLog['holiday_yn'].'\'
			,\''.$loLog['m_cd1'].'\'
			,\''.$loLog['m_cd2'].'\'
			,\''.$loLog['m_nm1'].'\'
			,\''.$loLog['m_nm2'].'\'
			,\''.($svcCd != '200' ? 'PERSON' : '').'\'

			,\''.($svcCd != '200' ? 'Y' : 'N').'\'
			,\''.$liExtraPay.'\'
			,\''.$liExtraVal1.'\'
			,\''.$liExtraVal2.'\'

			,\'AUTO\')';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	unset($loLog);

	include_once('../inc/_db_close.php');
?>
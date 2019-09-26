<?
	/*
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	*/

	$stress_code = $_POST['process_code'];
	$stress_ssn  = $ed->de($_POST['process_ssn']);
	$stress_seq	 = $_POST['process_seq'];
	$stress_mode = $_POST['process_mode'];
	
	//직원평가자료(상담기록지) 복사버튼눌렀을 경우
	if($_POST['copy_yn'] == 'Y'){
		$stress_seq = 0;
	}
	
	if ($stress_seq == 0){
		$sql = "select ifnull(max(stress_seq), 0) + 1
				  from counsel_stress
				 where org_no     = '$stress_code'
				   and stress_ssn = '$stress_ssn'";
		$stress_seq = $conn->get_data($sql);

		$sql = "insert into counsel_stress (
				 org_no
				,stress_ssn
				,stress_seq) values (
				 '$stress_code'
				,'$stress_ssn'
				,'$stress_seq')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}

	$stress_talker_cd		= $ed->de($_POST['stress_talker_cd']);
	$stress_talker_nm		= $conn->member_name($stress_code, $stress_talker_cd);
	$stress_type			= $_POST['stress_type'];
	$stress_date			= $_POST['stress_date'];
	$stress_work_hard		= addslashes($_POST['stress_work_hard']);
	$stress_work_aptitude	= addslashes($_POST['stress_work_aptitude']);
	$stress_work_client		= addslashes($_POST['stress_work_client']);
	$stress_work_day		= $_POST['stress_work_day'];
	$stress_work_week		= $_POST['stress_work_week'];
	$stress_work_month		= $_POST['stress_work_month'];
	$stress_work_hope_time	= addslashes($_POST['stress_work_hope_time']);
	$stress_work_hope_pay	= addslashes($_POST['stress_work_hope_pay']);
	$stress_work_change		= addslashes($_POST['stress_work_change']);
	$stress_person_family	= addslashes($_POST['stress_person_family']);
	$stress_person_economy	= addslashes($_POST['stress_person_economy']);
	$stress_person_health	= addslashes($_POST['stress_person_health']);
	$stress_person_other	= addslashes($_POST['stress_person_other']);
	$stress_center_meet		= addslashes($_POST['stress_center_meet']);
	$stress_center_edu		= addslashes($_POST['stress_center_edu']);
	$stress_center_worker	= addslashes($_POST['stress_center_worker']);
	$stress_center_person	= addslashes($_POST['stress_center_person']);
	$stress_center_other	= addslashes($_POST['stress_center_other']);
	$stress_self_edu		= addslashes($_POST['stress_self_edu']);
	$stress_self_meet		= addslashes($_POST['stress_self_meet']);
	$stress_self_other		= addslashes($_POST['stress_self_other']);
	$stress_other			= addslashes($_POST['stress_other']);
	$stress_talker_cont		= addslashes($_POST['stress_talker_cont']);
	$stress_result			= addslashes($_POST['stress_result']);

	$sql = "update counsel_stress
			   set stress_dt			= '$stress_date'
			,      stress_talker_ssn	= '$stress_talker_cd'
			,      stress_talker_nm		= '$stress_talker_nm'
			,      stress_type			= '$stress_type'
			,      stress_work_hard		= '$stress_work_hard'
			,      stress_work_aptitude	= '$stress_work_aptitude'
			,      stress_work_client	= '$stress_work_client'
			,      stress_work_day		= '$stress_work_day'
			,      stress_work_week		= '$stress_work_week'
			,      stress_work_month	= '$stress_work_month'
			,      stress_work_hope_time= '$stress_work_hope_time'
			,      stress_work_hope_pay	= '$stress_work_hope_pay'
			,      stress_work_change	= '$stress_work_change'
			,      stress_person_family	= '$stress_person_family'
			,      stress_person_economy= '$stress_person_economy'
			,      stress_person_health	= '$stress_person_health'
			,      stress_person_other	= '$stress_person_other'
			,      stress_center_meet	= '$stress_center_meet'
			,      stress_center_edu	= '$stress_center_edu'
			,      stress_center_worker	= '$stress_center_worker'
			,      stress_center_person	= '$stress_center_person'
			,      stress_center_other	= '$stress_center_other'
			,      stress_self_edu		= '$stress_self_edu'
			,      stress_self_meet		= '$stress_self_meet'
			,      stress_self_other	= '$stress_self_other'
			,      stress_other			= '$stress_other'
			,      stress_talker_cont	= '$stress_talker_cont'
			,      stress_result		= '$stress_result'
			 where org_no				= '$stress_code'
			   and stress_ssn			= '$stress_ssn'
			   and stress_seq			= '$stress_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}
	
	
	/*********************************
	리포트메뉴 통해서 저장하였을경우
	********************************/
	
	/*
	if($copy_yn == ''){
		echo '<script>';
			echo 'alert(\''.$myF->message('ok','N').'\');';
			echo 'location.replace(\'report.php?report_menu='.$report_menu.'&report_index='.$report_index.'&yymm='.$yymm.'&seq='.$stress_seq.'&ssn='.$ed->en($stress_ssn).'&year='.$year.'&month='.$month.'\');';
		echo '</script>';
		
	}
	*/

	/*
	include_once("../inc/_db_close.php");
	*/
?>
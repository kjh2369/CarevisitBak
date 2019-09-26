<?php
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	/*********************************

		파라메타

	*********************************/
	$code = $_POST['code'];         //기관코드
	$ssn  = $_POST['ssn'] != '' ? $ed->de($_POST['ssn']) : $ed->de($_POST['jumin']); //직원 주민번호
	$seq  = $_POST['seq'];          //순번
	/********************************/

	$sql = 'update counsel_stress
			   set del_flag   = \'Y\'
			 where org_no     = \''.$code.'\'
			   and stress_ssn = \''.$ssn.'\'
			   and stress_seq = \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		$conn->rollback();
		$result = 'N';
	}else{
		$conn->commit();
		$result = 'Y';
	}

	include_once('../inc/_db_close.php');
	
	echo $result;
	
?>
<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");

	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];

	$conn->begin();

	$sql = 'update counsel_client_stress
			   set del_flag    = \'Y\'
			 where org_no      = \''.$code.'\'
			   and stress_yymm = \''.$yymm.'\'
			   and stress_seq  = \''.$seq.'\'';

	if ($conn->execute($sql)){
		$conn->commit();
		echo 'ok';
	}else{
		$conn->rollback();
		echo 'error';
	}

	include_once("../inc/_db_close.php");
?>
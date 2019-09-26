<?php
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	#echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code = $_POST['code'];
	$dt   = $_POST['dt'];
	$seq  = $_POST['seq'];

	$conn->begin();

	if (empty($result)){
		$sql = "update counsel_client_normal
				   set del_flag   = 'Y'
				 where org_no     = '$code'
				   and client_dt  = '$dt'
				   and client_seq = '$seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$result = 'FAILE';
		}
	}

	if (empty($result)){
		$sql = "update counsel_client_baby
				   set del_flag   = 'Y'
				 where org_no     = '$code'
				   and client_dt  = '$dt'
				   and client_seq = '$seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$result = 'FAILE';
		}
	}

	if (empty($result)){
		$sql = "update counsel_client
				   set del_flag   = 'Y'
				 where org_no     = '$code'
				   and client_dt  = '$dt'
				   and client_seq = '$seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$result = 'FAILE';
		}
	}

	if (empty($result)){
		$conn->commit();
		$result = 'OK';
	}

	include_once("../inc/_db_close.php");

	echo $result;
?>
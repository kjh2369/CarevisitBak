<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['ssn']);
	$yymm	= $_POST['yymm'];

	$sql = 'DELETE
			FROM	counsel_client_desire
			WHERE	org_no		= \''.$orgNo.'\'
			AND		desire_ssn	= \''.$jumin.'\'
			AND		desire_yymm	= \''.$yymm.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once("../inc/_db_close.php");
?>
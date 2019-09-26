<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo = $_SESSION['userCenterCode'];
	$yymm = $_POST['yymm'];

	$sql = 'DELETE
			FROM	tmp_dft_amt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo $conn->error_msg;
	}

	include("../inc/_db_close.php");
?>
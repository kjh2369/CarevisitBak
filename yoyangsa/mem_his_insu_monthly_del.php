<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['ym'];

	$sql = 'DELETE
			FROM	ltcf_stnd_monthly
			WHERE	org_no	= \''.$code.'\'
			AND		ipin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '9';
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
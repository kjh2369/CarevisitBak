<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$fromDt	= $_POST['fromDt'];

	$sql = 'DELETE
			FROM	ltcf_insu_hist
			WHERE	org_no	= \''.$code.'\'
			AND		ipin	= \''.$jumin.'\'
			AND     from_dt = \''.$fromDt.'\'';

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
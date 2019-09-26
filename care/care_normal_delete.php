<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$seq	= $_POST['seq'];

	$sql = 'DELETE
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
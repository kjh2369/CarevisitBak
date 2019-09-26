<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$userCd	= $_SESSION['userCode'];
	$userPw	= $_SESSION['userPass'];

	$nowPw	= $_POST['nowPw'];
	$newPw	= $_POST['newPw'];

	if ($userCd != $nowPw){
		echo 8;
		exit;
	}

	$sql = 'UPDATE	member
			SET		pswd	= \''.$newPw.'\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		code	= \''.$userCd.'\'';

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
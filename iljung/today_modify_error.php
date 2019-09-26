<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$date = $_POST['date'];
	$errorMsg = $_POST['error'];


	$sql = 'UPDATE	plan_change_request
			SET		error_yn	= \'Y\'
			,		error_msg	= \''.$errorMsg.'\'
			WHERE	org_no		= \''.$orgNo.'\'
			AND		svc_cd		= \'0\'
			AND		jumin		= \''.$jumin.'\'
			AND		date		= \''.$date.'\'
			AND		error_yn	= \'N\'
			AND		del_flag	= \'N\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $con->close();
		 echo 9;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
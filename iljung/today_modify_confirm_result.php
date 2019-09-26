<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$svcCd = '0';
	$jumin = $ed->de($_POST['jumin']);
	$date = Date('Ymd');
	$time = $_POST['time'];
	$seq = $_POST['seq'];
	$idx = $_POST['idx'];

	$sql = 'UPDATE	plan_change_request
			SET		complete_yn	= \'Y\'
			,		complete_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		svc_cd		= \''.$svcCd .'\'
			AND		jumin		= \''.$jumin.'\'
			AND		date		= \''.$date.'\'
			AND		time		= \''.$time.'\'
			AND		seq			= \''.$seq.'\'
			AND		idx			= \''.$idx.'\'
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
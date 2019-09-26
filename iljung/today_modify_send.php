<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	//기관
	$orgNo = $_SESSION['userCenterCode'];

	//TODAY
	$today = Date('Ymd');

	$jumin = $ed->de($_POST['jumin']);

	$sql = 'UPDATE	plan_change_request
			SET		send_yn		= \'Y\'
			,		send_dt		= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		svc_cd		= \'0\'
			AND		jumin		= \''.$jumin.'\'
			AND		date		= \''.$today.'\'
			AND		result_yn	= \'Y\'
			AND		send_yn		= \'N\'
			AND		del_flag	= \'N\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	//$orgNo	= $_SESSION['userCenterCode'];
	$orgNo	= $_POST['orgNo'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['id'];

	$sql = 'UPDATE	board_list
			SET		del_yn		= \'Y\'
			,		update_id	= \''.$_SESSION['userCenterCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		brd_type	= \''.$type.'\'
			AND		dom_id		= \''.$gDomainID.'\'
			AND		brd_cd		= \''.$cd.'\'
			AND		brd_id		= \''.$id.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg;
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
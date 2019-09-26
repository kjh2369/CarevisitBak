<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	/*
		게시판 저장
	 */

	//$orgNo	= $_SESSION['userCenterCode'];
	$orgNo	= $_POST['orgNo'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['id'];

	$sql = 'UPDATE	board_list
			SET		notice_yn	= CASE WHEN notice_yn = \'Y\' THEN \'N\' ELSE \'Y\' END
			,		update_id	= \''.$_SESSION['userCode'].'\'
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
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
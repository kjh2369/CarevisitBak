<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 *	재가지원 대상 실태조사표
	 */

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$IPIN	= $_POST['IPIN'];//대상자

	$sql = 'DELETE
			FROM	care_actual_research
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		IPIN	= \''.$IPIN.'\'';

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
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['id'];
	$file	= $_POST['file'];

	$sql = 'DELETE
			FROM	board_file
			WHERE	org_no	= \''.$orgNo.'\'
			AND		brd_type= \''.$type.'\'
			AND		dom_id	= \''.$gDomainID.'\'
			AND		brd_cd	= \''.$cd.'\'
			AND		brd_id	= \''.$id.'\'
			AND		file_id	= \''.$file.'\'';

	$conn->begin();
	$conn->execute($sql);
	$conn->commit();

	@unlink('./files/'.$type.'/'.$orgNo.'/'.$gDomainID.'_'.$cd.'_'.$id.'_'.$file);
	echo 1;

	include_once('../inc/_db_close.php');
?>
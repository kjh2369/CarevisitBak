<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgType = '40';
	$orgNo	= $_SESSION['userCenterCode'];
	$userCd = $_SESSION['userCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$yymm	= Date('Ym');
?>
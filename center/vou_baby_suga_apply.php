<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$sugaCd = 'VM'.$type.'01';
	$suga = $_POST['amt'];
	$from = $_POST['from'];
	$to = $_POST['to'];

	include_once('../inc/_db_close.php');
?>
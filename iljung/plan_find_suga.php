<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$code     = $_POST['code'];
	$svcCd    = $_POST['svcCd'];
	$svcKind  = $_POST['svcKind'];
	$date     = $_POST['date'];
	$fromTime = $_POST['fromTime'];
	$toTime   = $_POST['toTime'];
	$ynFamily = $_POST['ynFamily'];
	$bathKind = $_POST['bathKind'];

	//findSugaCare($code, $svcKind, $date, $fromTime, $toTime, $ynFamily = 'N', $bathKind = '')

	print_r($_POST);

	include_once('../inc/_db_close.php');
?>
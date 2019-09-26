<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$mService = $_POST['mService'];
	$mPlan = $_POST['mPlan'];
	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
	$mDate = $mYear.$mMonth;
	$lastDay = $myF->lastDay($mYear, $mMonth);
	$cols = $lastDay + 8;

	include_once('../work/table_list_sub.php');
	include_once("../inc/_footer.php");
?>
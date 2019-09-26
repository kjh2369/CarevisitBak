<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );

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
	include_once('../inc/_db_close.php');
?>
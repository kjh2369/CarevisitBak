<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code	= $_GET['code'];
	$kind	= $_GET['kind'];
	$year	= $_GET['year'];
	$month	= $_GET['month'];
	

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	include_once('work_status_sub.php');
	include_once('../inc/_db_close.php');
?>
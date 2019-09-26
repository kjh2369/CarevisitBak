<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$IsExcel = true;

	include_once('./work_year_list.php');
	include_once('../inc/_db_close.php');
?>
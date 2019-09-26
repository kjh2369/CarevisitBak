<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$mode  = $_POST['mode'];
	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$f_type = $_POST['f_type'];

	
	require_once('./member_proc_counsel_list2.php');
	
	include_once('../inc/_db_close.php');
?>
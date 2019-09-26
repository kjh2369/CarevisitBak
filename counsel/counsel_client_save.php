<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	echo $myF->header_script();
	
	$mode  = $_POST['mode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	
	$conn->begin();
	
	if($debug) echo $mode; exit;

	include_once('../counsel/client_counsel_'.$mode.'_save.php');
	
	$conn->commit();
	
	include_once('../inc/_db_close.php');
	
	echo '<script>';
	echo 'alert(\''.$myF->message('ok','N').'\');';
	echo 'location.replace(\'./counsel_client.php?year='.$year.'&month='.$month.'\');';
	echo '</script>';
?>
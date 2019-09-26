<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$key	= $_POST['key'];
	$index	= $_POST['index'];
	$exp	= $_POST['exp'];

	$file = './files/'.$orgNo.'/'.$year.$month.'/F_'.$key.'_'.$index.'.'.$exp;

	if (is_file($file)) @unlink($file);

	//@unlink('./files/34273000017/201411/303_1.png');
	//@unlink('./files/34273000017/201411/303_2.png');
	//exit;

	echo 1;

	include_once('../inc/_db_close.php');
?>
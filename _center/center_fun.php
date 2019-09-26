<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$menuId	= $_POST['menuId'];
	$funPath= './center_'.$menuId.'_fun.php';

	if (!is_file($funPath)) return;

	include_once($funPath);
	include_once('../inc/_db_close.php');
?>
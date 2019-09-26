<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$vrNo = str_replace('-','',$_POST['vrNo']);

	$sql = 'SELECT	COUNT(*)
			FROM	cv_vr_list
			WHERE	REPLACE(vr_no,\'-\',\'\') = \''.$vrNo.'\'';

	$cnt = $conn->get_data($sql);

	echo $cnt;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$CMS = $_POST['CMSNo'];

	$sql = 'SELECT	COUNT(*)
			FROM	cv_cms_list
			WHERE	cms_no = \''.$CMS.'\'';

	$cnt = $conn->get_data($sql);

	echo $cnt;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$id = $_POST['id'];

	$sql = 'SELECT result
			  FROM faq
			 WHERE id = \''.$id.'\'';

	$data = $conn->get_data($sql);

	echo $data;

	include_once('../inc/_db_close.php');
?>
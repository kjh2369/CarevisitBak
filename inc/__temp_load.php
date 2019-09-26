<?
	include_once('../inc/_db_open.php');

	$sql = 'SELECT	COUNT(*)
			FROM	m00center
			WHERe	m00_mcode = \'1234\'';

	$conn->query($sql);
	$conn->fetch();

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
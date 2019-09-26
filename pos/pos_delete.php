<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$posCd	= $_POST['cd'];

	$sql = 'UPDATE	mem_pos
			SET		del_flag	= \'Y\'
			WHERE	org_no		= \''.$code.'\'
			AND		pos_cd		= \''.$posCd.'\'';

	$conn->execute($sql);

	include_once('../inc/_db_close.php');
?>
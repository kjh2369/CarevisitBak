<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$yymm  = $_POST['yymm'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = 'SELECT care
			,      bath
			,      nurse
			  FROM longcare_his
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND yymm   = \''.$yymm.'\'';

	$row = $conn->get_array($sql);

	echo $row['care'].chr(1).$row['bath'].chr(1).$row['nurse'];

	include_once('../inc/_db_close.php');
?>
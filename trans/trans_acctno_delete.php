<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$seq  = $_POST['seq'];

	$sql = 'DELETE
			  FROM acct_no
			 WHERE org_no = \''.$code.'\'
			   AND seq    = \''.$seq.'\'';

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once('../inc/_db_close.php');
?>
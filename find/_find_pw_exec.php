<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$pass	= $_POST['pass'];

	$sql	= 'SELECT COUNT(*)
				 FROM m97user
				WHERE m97_user	= \''.$code.'\'
				  AND m97_pass	= \''.$pass.'\'';

	$liCnt	= $conn->get_data($sql);

	if ($liCnt > 0){
		echo 1;
	}else{
		echo 9;
	}

	include_once('../inc/_db_close.php');
?>
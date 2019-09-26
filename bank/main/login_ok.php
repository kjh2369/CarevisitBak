<?
	include_once('../inc/_db_open.php');

	$id = $_POST['id'];
	$pass = $_POST['pass'];

	if ($id == 'ADMIN' && $pass == 'ADMIN'){
		$_SESSION['USER_CODE'] = 'ADMIN';
		echo 1;
	}else{
		echo 9;
	}

	include_once('../inc/_db_close.php');
?>
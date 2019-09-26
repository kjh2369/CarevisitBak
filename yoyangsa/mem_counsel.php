<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	$mode = $_POST['mode'];
	$type = $_POST['type'];

	if ($mode == '1'){
		//과정상담
		$path = '../counsel/mem_stress_';
	}else if ($mode == '2'){
		$path = '../counsel/client_counsel_stress_';
	}else if ($mode == '3'){
		$path = '../counsel/client_counsel_case_';
	}else{
		$path = './';
	}

	if ($type == 'LIST'){
		//리스트
		$path .= 'list.php';
	}else if ($type == 'REG'){
		//작성
		$path .= 'reg.php';
	}else if ($type == 'DEL'){
		if ($mode == '1'){
			$path .= 'del.php';
		}else{
			$path .= 'delete.php';
		}
	}

	if (Is_File($path)){
		include_once($path);
	}

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$type = $_POST['type'];
	$code = $_POST['code'];

	if ($type == 'orgNo'){
		//기관기호 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	m00center
				WHERE	m00_mcode = \''.$code.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			echo 'N';
		}else{
			echo 'Y';
		}

	}else if ($type == 'logId'){
		//로그인 ID 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	han_member
				WHERE	id = \''.$code.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			echo 'N';
		}else{
			echo 'Y';
		}
	}

	include_once('../inc/_db_close.php');
?>
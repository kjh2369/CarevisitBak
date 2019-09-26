<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$findGbn = $_POST['findGbn'];

	if ($findGbn == '101'){
		//기관명 찾기
		$orgNo = $_POST['orgNo'];

		$sql = 'SELECT	m00_store_nm
				FROM	m00center
				WHERE	m00_mcode = \''.$orgNo.'\'
				ORDER	BY m00_mkind
				LIMIT	1';

		$orgName = $conn->get_data($sql);

		echo $orgName;

	}else{
	}

	include_once('../inc/_db_close.php');
?>
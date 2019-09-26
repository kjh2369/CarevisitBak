<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$type	= $_GET['type'];
	$year	= $_GET['year'];
	$month	= IntVal($_GET['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	if ($type == '1'){
		//요금내역생성
		$sql = 'SELECT	cls_yn
				FROM	cv_close_set
				WHERE	yymm = \''.$yymm.'\'';

		echo $conn->get_data($sql);
	}

	include_once('../inc/_db_close.php');
?>
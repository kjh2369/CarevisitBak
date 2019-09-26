<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	close_yn
			FROM	ie_bm_close_yn
			WHERE	yymm = \''.$year.$month.'\'';

	$close = $conn->get_data($sql);

	$sql = 'SELECT	m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			ORDER	BY m00_mkind
			LIMIT	1';

	$name = $conn->get_data($sql);

	echo 'orgNm='.$name.'&close='.($close == 'Y' ? 'Y' : 'N');

	include_once('../inc/_db_close.php');
?>
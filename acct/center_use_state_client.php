<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_POST['code'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	t01_mkind AS kind
			,		COUNT(DISTINCT t01_jumin) AS cnt
			FROM	t01iljung
			WHERE	t01_ccode = \''.$code.'\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			AND		t01_del_yn = \'N\'
			GROUP	BY t01_mkind';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($data) $data .= '&';
		$data .= $row['kind'].'='.$row['cnt'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
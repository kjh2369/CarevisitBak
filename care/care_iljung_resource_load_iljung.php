<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$sr		= $_POST['sr'];

	$sql = 'SELECT	t01_suga_code1 AS cd
			,		LEFT(t01_sugup_date,6) AS yymm
			,		COUNT(t01_suga_code1) AS cnt
			FROM	t01iljung
			WHERE	t01_ccode = \''.$code.'\'
			AND		t01_mkind = \''.$sr.'\'
			AND		t01_sugup_date >= \''.$year.'0101\'
			AND		t01_sugup_date <= \''.$year.'1231\'
			AND		t01_del_yn = \'N\'
			GROUP	BY t01_suga_code1, LEFT(t01_sugup_date,6)';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'cd='.$row['cd'];
		$data .= '&mon='.SubStr($row['yymm'],4,2);
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
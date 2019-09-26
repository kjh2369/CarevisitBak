<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year = $_POST['year'];

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month, cls_yn
			FROM	cv_close_set
			WHERE	LEFT(yymm, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		echo ($i > 0 ? '&' : '').$row['month'].'='.$row['cls_yn'];
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
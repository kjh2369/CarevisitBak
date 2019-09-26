<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		close_yn
			,		close_dt
			,		acct_dt
			FROM	ie_bm_close_yn
			WHERE	LEFT(yymm,4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= ($data ? '?' : '').'month='.$row['month'].'&close='.$row['close_yn'].'&closeDt='.$row['close_dt'].'&acctDt='.$row['acct_dt'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
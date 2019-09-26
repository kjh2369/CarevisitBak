<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];

	$sql = 'SELECT	a.org_no
			,		CAST(RIGHT(b.yymm,2) AS unsigned) AS month
			,		SUM(b.acct_amt) AS acct_amt
			FROM	(
					SELECT	DISTINCT m00_mcode AS org_no
					FROM	m00center
					INNER	JOIN	b02center
							ON		b02_center = m00_mcode
					WHERE	m00_domain = \''.$company.'\'
					) AS a
			INNER	JOIN	cv_svc_acct_list AS b
					ON		b.org_no = a.org_no
					AND		LEFT(b.yymm,4) = \''.$year.'\'
			GROUP	BY b.yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$data = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= ($data ? '?' : '');
		$data .= 'month='.$row['month'];
		$data .= '&acctAmt='.$row['acct_amt'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
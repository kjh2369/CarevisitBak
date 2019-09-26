<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		close_yn
			FROM	ie_bm_close_yn
			WHERE	LEFT(yymm,4)= \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$yn[$row['month']] = $row['close_yn'];
	}

	$conn->row_free();



	$sql = 'SELECT	month
			,		SUM(CASE WHEN idx = 1 AND gbn = \'1\' THEN 1 ELSE 0 END) AS in_cnt1
			,		SUM(CASE WHEN idx = 1 AND gbn = \'1\' THEN amt ELSE 0 END) AS in_amt1
			,		SUM(CASE WHEN idx = 1 AND gbn = \'2\' THEN 1 ELSE 0 END) AS in_cnt2
			,		SUM(CASE WHEN idx = 1 AND gbn = \'2\' THEN amt ELSE 0 END) AS in_amt2
			,		SUM(CASE WHEN idx = 1 AND gbn = \'3\' THEN 1 ELSE 0 END) AS in_cnt3
			,		SUM(CASE WHEN idx = 1 AND gbn = \'3\' THEN amt ELSE 0 END) AS in_amt3
			,		SUM(CASE WHEN idx = 1 AND gbn = \'4\' THEN 1 ELSE 0 END) AS in_cnt4
			,		SUM(CASE WHEN idx = 1 AND gbn = \'4\' THEN amt ELSE 0 END) AS in_amt4
			,		SUM(CASE WHEN idx = 1 AND gbn = \'5\' THEN 1 ELSE 0 END) AS in_cnt5
			,		SUM(CASE WHEN idx = 1 AND gbn = \'5\' THEN amt ELSE 0 END) AS in_amt5
			,		SUM(CASE WHEN idx = 1 AND gbn = \'6\' THEN 1 ELSE 0 END) AS in_cnt6
			,		SUM(CASE WHEN idx = 1 AND gbn = \'6\' THEN amt ELSE 0 END) AS in_amt6
			,		SUM(CASE WHEN idx = 1 AND gbn = \'X\' THEN 1 ELSE 0 END) AS in_cntX
			,		SUM(CASE WHEN idx = 1 AND gbn = \'X\' THEN amt ELSE 0 END) AS in_amtX
			,		SUM(CASE WHEN idx = 2 THEN 1 ElSE 0 END) AS out_cnt
			,		SUM(CASE WHEN idx = 2 THEN amt ElSE 0 END) AS out_amt
			FROM	(
					SELECT	1 AS idx
					,		CAST(MID(yymm,5) AS unsigned) AS month
					,		gbn
					,		seq
					,		amt
					FROM	ie_bm_other_in
					WHERE	org_no		= \''.$orgNo.'\'
					AND		LEFT(yymm,4)= \''.$year.'\'
					UNION	ALL
					SELECT	2 AS idx
					,		CAST(MID(yymm,5) AS unsigned) AS month
					,		\'\'
					,		seq
					,		amt
					FROM	ie_bm_charge
					WHERE	org_no		= \''.$orgNo.'\'
					AND		LEFT(yymm,4)= \''.$year.'\'
					) AS a
			GROUP	BY month';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$data = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data[$row['month']] = Array(
			'inCnt1'=>$row['in_cnt1']
		,	'inAmt1'=>$row['in_amt1']
		,	'inCnt2'=>$row['in_cnt2']
		,	'inAmt2'=>$row['in_amt2']
		,	'inCnt3'=>$row['in_cnt3']
		,	'inAmt3'=>$row['in_amt3']
		,	'inCnt4'=>$row['in_cnt4']
		,	'inAmt4'=>$row['in_amt4']
		,	'inCnt5'=>$row['in_cnt5']
		,	'inAmt5'=>$row['in_amt5']
		,	'inCnt6'=>$row['in_cnt6']
		,	'inAmt6'=>$row['in_amt6']
		,	'inCntX'=>$row['in_cntX']
		,	'inAmtX'=>$row['in_amtX']
		,	'outCnt'=>$row['out_cnt']
		,	'outAmt'=>$row['out_amt']
		);
	}

	$conn->row_free();

	$str = '';

	for($i=1; $i<=12; $i++){
		$str	.= ($str ? '?' : '').'month='.$i
				.	'&inCnt1='.$data[$i]['inCnt1']
				.	'&inAmt1='.$data[$i]['inAmt1']
				.	'&inCnt2='.$data[$i]['inCnt2']
				.	'&inAmt2='.$data[$i]['inAmt2']
				.	'&inCnt3='.$data[$i]['inCnt3']
				.	'&inAmt3='.$data[$i]['inAmt3']
				.	'&inCnt4='.$data[$i]['inCnt4']
				.	'&inAmt4='.$data[$i]['inAmt4']
				.	'&inCnt5='.$data[$i]['inCnt5']
				.	'&inAmt5='.$data[$i]['inAmt5']
				.	'&inCnt6='.$data[$i]['inCnt6']
				.	'&inAmt6='.$data[$i]['inAmt6']
				.	'&inCntX='.$data[$i]['inCntX']
				.	'&inAmtX='.$data[$i]['inAmtX']
				.	'&outCnt='.$data[$i]['outCnt']
				.	'&outAmt='.$data[$i]['outAmt']
				.	'&close='.($yn[$i] == 'Y' ? 'Y' : 'N');
	}

	echo $str;

	Unset($data);
	Unset($yn);

	include_once('../inc/_db_close.php');
?>
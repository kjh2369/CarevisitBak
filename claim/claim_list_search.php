<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	$sql = 'SELECT	yymm
			FROM	cv_claim_yymm';

	$yymm = $conn->get_data($sql);

	$sql = 'SELECT	LEFT(from_dt, 6) AS from_ym, LEFT(to_dt, 6) AS to_ym, bill_kind
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			AND		LEFT(from_dt, 4) <= \''.$year.'\'
			AND		LEFT(to_dt, 4)	 >= \''.$year.'\'
			';
	$cv_bill_info = $conn->_fetch_array($sql);


	$sql = 'SELECT	CAST(MID(claim_yymm, 5) AS unsigned) AS month, SUM(in_amt) AS in_amt
			FROM	cv_pay_in_dtl
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			AND		LEFT(claim_yymm, 4) = \''.$year.'\'
			GROUP	BY claim_yymm';

	$inAmt = $conn->_fetch_array($sql, 'month');


	$sql = 'SELECT	CAST(RIGHT(acct_ym, 2) AS unsigned) AS month, use_amt, acct_amt + IFNULL(dft_amt, 0) AS acct_amt, acct_amt - use_amt AS dis_amt, IFNULL(dft_amt, 0) AS dft_amt, acct_ym
			FROM	(
					SELECT	a.acct_ym
					,		a.use_amt
					,		(SELECT amt FROM cv_svc_acct_amt WHERE org_no = a.org_no AND yymm <= a.yymm ORDER BY yymm DESC LIMIT 1) AS acct_amt
					,		(SELECT month_dft_amt FROM cv_svc_acct_amt WHERE org_no = a.org_no AND yymm = a.yymm ORDER BY yymm DESC LIMIT 1) AS dft_amt
					FROM	(
							SELECT	org_no, acct_ym, yymm
							,		SUM(CASE WHEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt > 0
											 THEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt ELSE 0 END) AS use_amt
							FROM	cv_svc_acct_list AS a
							WHERE	org_no = \''.$orgNo.'\'
							AND		LEFT(acct_ym, 4)  = \''.$year.'\'
							AND		LEFT(acct_ym, 6) <= \''.$yymm.'\'
							GROUP	BY yymm
							) AS a
					) AS a';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($year.($row['month'] < 10 ? '0' : '').$row['month'] < '201612'){
			$row['dft_amt'] = '-';
			$inAmt[$row['month']]['in_amt'] = '-';
		}

		$bill_kind = '2';

		for($j=0; $j<count($cv_bill_info); $j++){
			if ($row['acct_ym'] >= $cv_bill_info[$j]['from_ym'] && $row['acct_ym'] <= $cv_bill_info[$j]['to_ym']){
				$bill_kind = $cv_bill_info[$j]['bill_kind'];
				break;
			}
		}

		echo ($i > 0 ? '?' : '').'month='.$row['month'].'&useAmt='.$row['use_amt'].'&acctAmt='.$row['acct_amt'].'&disAmt='.$row['dis_amt'].'&dftAmt='.$row['dft_amt'].'&inAmt='.$inAmt[$row['month']]['in_amt'].'&bill_kind='.$bill_kind;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
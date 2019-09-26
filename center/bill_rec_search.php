<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	//전월까지 청구금액
	$sql = 'SELECT	SUM(acct_amt) AS acct_amt
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym	< \''.$year.'01\'';
	$acctAmt = $conn->get_data($sql);


	//전월까지 입금금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym	< \''.$year.'01\'
			AND		del_flag= \'N\'
			AND		IFNULL(link_stat,\'1\') = \'1\'';
	$inAmt = $conn->get_data($sql);


	//전월까지 미납금액
	$nonpay = $acctAmt - $inAmt;


	//데이타 구조
	$tmpData = Array(
		'MON_PAY'	=>0 //당월청구금액
	,	'NON_PAY'	=>0 //전월까지 미납금
	,	'IN_AMT'	=>0 //당월입금액
	,	'NON_AMT'	=>0 //미납금
	);

	for($i=1; $i<=12; $i++) $data[$i] = $tmpData;


	//청구내역
	$sql = 'SELECT	CAST(RIGHT(acct_ym,2) AS unsigned) AS month
			,		SUM(acct_amt) AS acct_amt
			FROM	cv_svc_acct_list
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(acct_ym,4) = \''.$year.'\'
			GROUP	BY acct_ym';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$data[$row['month']]['MON_PAY'] = $row['acct_amt'];
	}

	$conn->row_free();


	//입금내역
	$sql = 'SELECT	CAST(RIGHT(acct_ym,2) AS unsigned) AS month
			,		SUM(CASE WHEN IFNULL(in_stat,\'1\') = \'1\' THEN IFNULL(link_amt,0) ELSE 0 END) AS link_amt
			,		SUM(CASE WHEN IFNULL(in_stat,\'1\') = \'9\' THEN IFNULL(link_amt,0) ELSE 0 END) AS cut_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			AND		LEFT(acct_ym,4) = \''.$year.'\'
			AND		IFNULL(link_stat,\'1\') = \'1\'
			GROUP	BY acct_ym';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$data[$row['month']]['IN_AMT'] = $row['link_amt'];
		$data[$row['month']]['CUT_AMT'] = $row['cut_amt'];
	}

	$conn->row_free();


	//미납금내역
	for($i=1; $i<=12; $i++){
		//미납금
		$data[$i]['NON_AMT'] = $data[$i]['MON_PAY'] - ($data[$i]['IN_AMT'] + $data[$i]['CUT_AMT']);

		//미납분
		if ($data[$i]['MON_PAY'] > 0){
			if ($i == 1){
				$data[$i]['NON_PAY'] = $nonpay;
			}else{
				$data[$i]['NON_PAY'] = $data[$i-1]['NON_PAY'] + $data[$i-1]['NON_AMT'];
			}
		}
	}


	$val = '';
	//출력
	for($i=1; $i<=12; $i++){
		$val .= ($val ? '?' : '');
		$val .= 'month='.$i;
		$val .= '&MON_PAY='.$data[$i]['MON_PAY'];
		$val .= '&NON_PAY='.$data[$i]['NON_PAY'];
		$val .= '&IN_AMT='.$data[$i]['IN_AMT'];
		$val .= '&CUT_AMT='.$data[$i]['CUT_AMT'];
		$val .= '&NON_AMT='.$data[$i]['NON_AMT'];
	}

	echo $val;
	Unset($data);

	include_once('../inc/_db_close.php');
?>
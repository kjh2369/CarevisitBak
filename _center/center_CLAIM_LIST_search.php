<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];

	//전년까지 미납분
	$sql = 'SELECT	(
					SELECT	SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	acct_ym < \''.$year.'01\'
					) - (
					SELECT	SUM(link_amt) AS link_amt
					FROM	cv_cms_link
					WHERE	acct_ym < \''.$year.'01\'
					AND		del_flag= \'N\'
					AND		IFNULL(link_stat,\'1\') = \'1\'
					)';
	$preyearNonpay = $conn->get_data($sql);

	//청구내역
	$sql = 'SELECT	a.acct_ym, COUNT(DISTINCT a.org_no) AS cnt, SUM(a.acct_amt) AS acct_amt, SUM(a.link_amt) AS link_amt
			FROM	(
					SELECT	a.org_no, a.acct_ym, IFNULL(a.acct_amt,0) - IFNULL(a.dc_amt,0) AS acct_amt, IFNULL(b.link_amt,0) AS link_amt
					FROM	(
							SELECT	org_no, acct_ym
							,		SUM(CASE WHEN svc_gbn != \'9\' AND svc_cd != \'99\' THEN acct_amt ELSE 0 END) AS acct_amt
							,		SUM(CASE WHEN svc_gbn = \'9\' AND svc_cd = \'99\' THEN acct_amt ELSE 0 END) AS dc_amt
							FROM	cv_svc_acct_list
							WHERE	LEFT(acct_ym,4) = \''.$year.'\'
							GROUP	BY org_no, acct_ym
							) AS a
					LEFT	JOIN (
							SELECT	org_no, acct_ym, SUM(link_amt) AS link_amt
							FROM	cv_cms_link
							WHERE	LEFT(acct_ym,4) = \''.$year.'\'
							AND		del_flag = \'N\'
							AND		CASE WHEN IFNULL(link_stat,\'\') = \'\' THEN \'1\' ELSE link_stat END = \'1\'
							GROUP	BY org_no, acct_ym
							) AS b
							ON		b.org_no	= a.org_No
							AND		b.acct_ym	= a.acct_ym
					) AS a
			INNER	JOIN (
					SELECT	m00_mcode AS org_no, GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm
					FROM	m00center
					WHERE	m00_domain = \''.$company.'\'
					GROUP	BY m00_mcode
					) AS b
					ON		b.org_no = a.org_no
			WHERE	a.acct_amt > 0
			GROUP	BY a.acct_ym
			ORDER	By acct_ym';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$m = IntVal(SubStr($row['acct_ym'],4));

		$data[$m]['cnt'] = $row['cnt']; //기관수
		$data[$m]['acctAmt'] = $row['acct_amt']; //청구금액
		//$data[$m]['unpaid'] = $unpaid; //미납분
		$data[$m]['deposit'] = $row['link_amt']; //입금
		$data[$m]['nonpay'] = $row['acct_amt'] - $row['link_amt']; //미납
	}

	$conn->row_free();

	$unpaid = $preyearNonpay; //미납분
	for($i=1; $i<=12; $i++){
		$data[$i]['unpaid'] = $unpaid;
		$unpaid += $data[$i]['nonpay'];
	}

	$val = '';
	for($i=1; $i<=12; $i++){
		$val .= ($val ? '?' : '');
		$val .= 'month='.$i;
		$val .= '&cnt='.$data[$i]['cnt'];
		$val .= '&acctAmt='.$data[$i]['acctAmt'];
		$val .= '&unpaid='.($data[$i]['cnt'] > 0 ? $data[$i]['unpaid'] : 0);
		$val .= '&deposit='.$data[$i]['deposit'];
		$val .= '&nonpay='.$data[$i]['nonpay'];
	}

	Unset($data);
	echo $val;

	include_once('../inc/_db_close.php');
?>
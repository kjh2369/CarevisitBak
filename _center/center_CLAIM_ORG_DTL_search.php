<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];

	//전년까지 미납분
	$sql = 'SELECT	(
					SELECT	SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym < \''.$year.'01\'
					) - (
					SELECT	SUM(link_amt) AS link_amt
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		acct_ym < \''.$year.'01\'
					AND		del_flag= \'N\'
					AND		IFNULL(link_stat,\'1\') = \'1\'
					)';
	$preyearNonpay = $conn->get_data($sql);


	//전년도까지 과임금액
	$sql = 'SELECT	SUM(a.in_amt - a.link_amt) AS over_amt
			FROM	(
					SELECT	a.cms_no, a.cms_dt, a.cms_seq, SUM(a.link_amt) AS link_amt, b.in_amt
					FROM	cv_cms_link AS a
					INNER	JOIN	cv_cms_reg AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.seq		= a.cms_seq
							AND		b.del_flag	= \'N\'
					WHERE	a.org_no	= \''.$orgNo.'\'
					AND		a.acct_ym	< \''.$year.'01\'
					AND		a.del_flag	= \'N\'
					GROUP	BY a.cms_no, a.cms_dt, a.cms_seq
					) AS a';
	//$overAmt = $conn->get_data($sql);

	$sql = 'SELECT	a.acct_ym, a.cms_ym, COUNT(DISTINCT a.org_no) AS cnt, SUM(a.acct_amt) AS acct_amt, SUM(a.link_amt) AS link_amt, SUM(a.in_amt) AS in_amt, SUM(a.in_link_amt) AS in_link_amt
			FROM	(
					SELECT	a.org_no, a.acct_ym, b.cms_ym, IFNULL(a.acct_amt,0) AS acct_amt, IFNULL(b.link_amt,0) AS link_amt, IFNULL(b.in_amt,0) AS in_amt, IFNULL(b.in_link_amt,0) AS in_link_amt
					FROM	(
							SELECT	org_no, acct_ym, SUM(acct_amt) AS acct_amt
							FROM	cv_svc_acct_list
							WHERE	org_no	= \''.$orgNo.'\'
							AND		LEFT(acct_ym,4) = \''.$year.'\'
							GROUP	BY org_no, acct_ym
							) AS a
					LEFT	JOIN (
							SELECT	a.org_no, a.acct_ym, a.cms_ym, SUM(a.link_amt) AS link_amt, SUM(a.in_amt) AS in_amt, a.in_link_amt
							FROM	(
									SELECT	a.org_no, a.cms_no, a.cms_dt, a.seq AS cms_seq, MID(a.cms_dt,1,6) AS cms_ym, IFNULL(b.acct_ym,MID(a.cms_dt,1,6)) AS acct_ym, SUM(b.link_amt) AS link_amt
									,		CASE WHEN MID(a.cms_dt,1,6) = IFNULL(b.acct_ym,MID(a.cms_dt,1,6)) THEN a.in_amt ELSE SUM(b.link_amt) END AS in_amt
									,		CASE WHEN MID(a.cms_dt,1,6) = IFNULL(b.acct_ym,MID(a.cms_dt,1,6)) THEN
											(	SELECT	SUM(link_amt)
												FROM	cv_cms_link
												WHERE	org_no	= b.org_no
												AND		cms_no	= b.cms_no
												AND		cms_dt	= b.cms_dt
												AND		cms_seq	= b.cms_seq
												AND		del_flag= \'N\'
												AND		CONCAT(yymm,\'_\',seq) != CONCAT(b.yymm,\'_\',b.seq))
											ELSE 0 END AS in_link_amt
									FROM	cv_cms_reg AS a
									LEFT	JOIN	cv_cms_link AS b
											ON		b.org_no	= a.org_no
											AND		b.cms_no	= a.cms_no
											AND		b.cms_dt	= a.cms_dt
											AND		b.cms_seq	= a.seq
											AND		b.del_flag	= \'N\'
									WHERE	a.org_no	= \''.$orgNo.'\'
									AND		a.del_flag	= \'N\'
									AND		LEFT(a.cms_dt,4) = \''.$year.'\'
									GROUP	BY a.org_no, a.cms_no, a.cms_dt, a.seq, IFNULL(b.acct_ym,MID(a.cms_dt,1,6))
									) AS a
							GROUP BY a.org_no, a.acct_ym
							) AS b
							ON		b.org_no	= a.org_No
							AND		b.acct_ym	= a.acct_ym
					) AS a
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
		$data[$m]['deposit'] = $row['in_amt']; //입금
		$data[$m]['nonpay'] = $row['acct_amt'] - $row['link_amt']; //미납
		$data[$m]['overAmt'] = $row['in_amt'] - $row['link_amt'] - $row['in_link_amt']; //과입금
		$data[$m]['cmsYm'] = IntVal(SubStr($row['cms_ym'],4));
		$nowOver += $data[$m]['overAmt'];
	}

	$conn->row_free();

	$unpaid = $preyearNonpay; //미납분
	for($i=1; $i<=12; $i++){
		$data[$i]['unpaid'] = $unpaid;
		$unpaid += $data[$i]['nonpay'];
	}

	$nowUnpaid = $conn->get_data('SELECT (SELECT IFNULL(SUM(acct_amt),0) FROM cv_svc_acct_list WHERE org_no = \''.$orgNo.'\') - (SELECT IFNULL(SUM(in_amt),0) FROM cv_cms_reg WHERE org_no = \''.$orgNo.'\' AND del_flag = \'N\')');

	$val = '';
	for($i=1; $i<=12; $i++){
		$val .= ($val ? '?' : '');
		$val .= 'month='.$i;
		$val .= '&cnt='.$data[$i]['cnt'];
		$val .= '&acctAmt='.$data[$i]['acctAmt'];
		$val .= '&unpaid='.($data[$i]['cnt'] > 0 ? $data[$i]['unpaid'] : 0);
		$val .= '&deposit='.$data[$i]['deposit'];
		$val .= '&nonpay='.$data[$i]['nonpay'];
		$val .= '&overAmt='.$data[$i]['overAmt'];
		$val .= '&lastOver='.$overAmt;
		$val .= '&nowOver='.$nowOver;
		$val .= '&cmsYm='.$data[$i]['cmsYm'];
		$val .= '&nowUnpaid='.$nowUnpaid;
	}

	Unset($data);
	echo $val;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$CMS	= $_POST['CMS'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	if (StrLen($CMS) < 8){
		$CMS = '00000000'.$CMS;
		$CMS = SubStr($CMS, StrLen($CMS) - 8, StrLen($CMS));
	}

	//청구금액
	$sql = 'SELECT	SUM(acct_amt)
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$acctAmt = $conn->get_data($sql);


	//CMS 미연결 금액
	$nonLinkAmt = 0;
	$sql = 'SELECT	SUM(IFNULL(in_amt,0) - IFNULL(link_amt,0)) AS non_link_amt
			FROM	(
					SELECT	a.cms_no
					,		a.cms_dt
					,		a.seq
					,		a.in_amt
					,		SUM(b.link_amt) AS link_amt
					FROM	cv_cms_reg AS a
					LEFT	JOIN	cv_cms_link AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.cms_seq	= a.seq
							AND		b.del_flag	= \'N\'
					WHERE	a.org_no	= \''.$orgNo.'\'
					/*AND		a.cms_no	= \''.$CMS.'\'*/
					AND		a.del_flag	= \'N\'
					GROUP	BY a.cms_no, a.cms_dt, a.seq
					) AS a';
	$nonLinkAmt = $conn->get_data($sql);

	//선입금금액
	$sql = 'SELECT	SUM(IFNULL(link_amt,0)) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no		= \''.$orgNo.'\'
			AND		link_stat	= \'5\'
			AND		del_flag	= \'N\'
			AND		IFNULL(cms_no,\'\')	= \'\'
			AND		CASE WHEN yymm = \'000000\' THEN \''.$yymm.'\' ElSE yymm END = \''.$yymm.'\'';
	$nonLinkAmt += $conn->get_data($sql);


	//CMS 연결금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			/*AND		cms_no	= \''.$CMS.'\'*/
			AND		cms_no != \'\'
			AND		del_flag= \'N\'';
	$linkAmt = $conn->get_data($sql);


	//입금등록금액
	$sql = 'SELECT	SUM(IFNULL(link_amt,0)) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no		= \''.$orgNo.'\'
			AND		yymm		= \''.$yymm.'\'
			AND		link_stat	= \'1\'
			AND		del_flag	= \'N\'
			AND		IFNULL(cms_no,\'\')	= \'\'';
	$bankAmt = $conn->get_data($sql);

	//미납금액
	$nonpay = $acctAmt - $linkAmt - $bankAmt;

	echo 'acctAmt='.$acctAmt.'&nooLinkAmt='.$nonLinkAmt.'&linkAmt='.$linkAmt.'&bankAmt='.$bankAmt.'&nonpay='.$nonpay;

	include_once('../inc/_db_close.php');
?>
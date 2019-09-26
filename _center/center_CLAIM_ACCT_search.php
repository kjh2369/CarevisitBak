<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];

	//청구분
	$sql = 'SELECT	SUM(acct_amt) AS acct_amt
			FROM	cv_svc_acct_list
			WHERE	acct_ym < \''.$year.'01\'';
	$arrNonpay['acctAmt'] = $conn->get_data($sql);


	//입금분
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	acct_ym	< \''.$year.'01\'
			AND		del_flag= \'N\'
			AND		IFNULL(link_stat,\'1\') = \'1\'';
	$arrNonpay['linkAmt'] = $conn->get_data($sql);


	//청구내역
	$sql = 'SELECT	a.org_no
			,		CASE WHEN a.acct_gbn = \'1\' THEN \'CMS\'
						 WHEN a.acct_gbn = \'2\' THEN \'BANK\' ElSE a.acct_gbn END AS acct_gbn
			,		a.acct_ym
			,		a.acct_amt
			,		CASE WHEN b.link_stat IS NULL THEN \'CMS\'
						 WHEN b.link_stat = \'1\' THEN \'BANK\' ELSE \'\' END AS link_gbn
			,		b.link_amt
			FROM	(
					SELECT	a.org_no, a.acct_gbn, a.yymm, a.acct_ym, SUM(a.acct_amt) AS acct_amt
					FROM	cv_svc_acct_list AS a
					INNER	JOIN (
							SELECT	m00_mcode AS org_no, GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm
							FROM	m00center
							WHERE	m00_domain = \''.$company.'\'
							GROUP	BY m00_mcode
							) AS b
							ON		b.org_no = a.org_no
					WHERE	LEFT(acct_ym,4) = \''.$year.'\'
					GROUP	BY a.org_no, a.acct_gbn, a.yymm, a.acct_ym
					) AS a
			LEFT	JOIN	cv_cms_link AS b
					ON		b.org_no	= a.org_no
					AND		b.yymm		= a.yymm
					AND		b.del_flag	= \'N\'
			ORDER	BY acct_ym';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$month = IntVal(SubStr($row['acct_ym'],4));

		if (!$tmpCMS[$row['org_no'].'_'.$row['acct_ym'].'_'.$row['acct_gbn']]){
			 $tmpCMS[$row['org_no'].'_'.$row['acct_ym'].'_'.$row['acct_gbn']] = 'Y';
			 $data[$month]['C'][$row['acct_gbn']] += $row['acct_amt']; //청구내역
		}

		if (!$tmpBank[$row['org_no'].'_'.$row['acct_ym'].'_'.$row['link_gbn']]){
			 $tmpBank[$row['org_no'].'_'.$row['acct_ym'].'_'.$row['link_gbn']] = 'Y';
			 $data[$month]['A'][$row['link_gbn']] += $row['link_amt']; //입금내역
		}
	}

	$conn->row_free();


	$nonpay = $arrNonpay['acctAmt'] - $arrNonpay['linkAmt'];
	for($i=1; $i<=12; $i++){
		if (!$data[$i]) continue;
		$data[$i]['N']['NP'] = $nonpay;
		$data[$i]['N']['CMS'] = $data[$i]['C']['CMS'] - $data[$i]['A']['CMS'];
		$data[$i]['N']['BANK'] = $data[$i]['C']['BANK'] - $data[$i]['A']['BANK'];

		$nonpay += ($data[$i]['N']['CMS'] + $data[$i]['N']['BANK']);
	}


	$str = '';
	for($i=1; $i<=12; $i++){
		if (!$data[$i]) continue;
		$str .= ($str ? '?' : '');
		$str .= 'month='.$i;
		$str .= '&CC='.$data[$i]['C']['CMS'];
		$str .= '&CB='.$data[$i]['C']['BANK'];
		$str .= '&AC='.$data[$i]['A']['CMS'];
		$str .= '&AB='.$data[$i]['A']['BANK'];
		$str .= '&NN='.$data[$i]['N']['NP'];
		$str .= '&NC='.$data[$i]['N']['CMS'];
		$str .= '&NB='.$data[$i]['N']['BANK'];
	}

	Unset($data);
	Unset($tmpCMS);
	Unset($tmpBank);

	echo $str;

	include_once('../inc/_db_close.php');
?>
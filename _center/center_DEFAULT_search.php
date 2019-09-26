<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

/*
--청구내역
SELECT org_no, yymm, acct_gbn, SUM(acct_amt) AS acct_amt
FROM cv_svc_acct_list
WHERE acct_ym = '201503'
GROUP BY org_no, yymm, acct_gbn

--연결내역
SELECT org_no, yymm, acct_gbn, SUM(link_amt) AS link_amt
FROM (
SELECT org_no, yymm, CASE WHEN link_stat IS NULL THEN '1' ELSE '2' END AS acct_gbn, link_amt
FROM cv_cms_link
WHERE acct_ym = '201503'
AND del_flag = 'N'
AND IFNULL(link_stat,'1') = '1'
) AS a
GROUP BY org_no, yymm, acct_gbn

--CMS 입금내역
SELECT org_no, in_amt - link_amt AS prepay
FROM   (
SELECT a.org_no, a.cms_no, a.cms_dt, a.seq, a.in_amt, SUM(IFNULL(b.link_amt,0)) AS link_amt
FROM   cv_cms_reg AS a
LEFT   JOIN cv_cms_link AS b
       ON b.org_no = a.org_no
       AND b.cms_no = a.cms_no
       AND b.cms_dt = a.cms_dt
       AND b.cms_seq= a.seq
       AND b.del_flag = 'N'
WHERE  a.org_no != ''
AND    a.del_flag = 'N'
GROUP  BY a.org_no, a.cms_no, a.cms_dt, a.seq
       ) AS a
WHERE  in_amt - link_amt != 0


--무통장 입금내역
SELECT org_no, link_stat, prepay_seq, link_amt
FROM cv_cms_link
WHERE CASE WHEN IFNULL(link_stat,'') = '' THEN '1' ELSE link_stat END != '1'
*/

	//청구내역
	$sql = 'SELECT	a.org_no, b.org_nm, b.mg_nm, a.yymm, a.acct_gbn, SUM(a.acct_amt) AS acct_amt
			FROM	cv_svc_acct_list AS a
			INNER	JOIN (
					SELECT	DISTINCT
							m00_mcode AS org_no
					,		m00_store_nm AS org_nm
					,		m00_mname AS mg_nm
					FROM	m00center
					WHERE	m00_domain = \''.$company.'\'
					) AS b
					ON		b.org_no = a.org_no
			WHERE	a.acct_ym = \''.$yymm.'\'
			GROUP	BY a.org_no, a.yymm, a.acct_gbn
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$data[$orgNo]){
			 $data[$orgNo] = Array(
				'orgNo'	 =>$row['org_no']
			,	'name'	 =>$row['org_nm']
			,	'manager'=>$row['mg_nm']
			,	'yymm'	 =>$row['yymm']
			,	'ACCT'	 =>Array('CMS'=>0, 'BANK'=>0)
			,	'LINK'	 =>Array('CMS'=>0, 'BANK'=>0)
			,	'IN'	 =>Array('CMS'=>0, 'BANK'=>0)
			);
		}

		if ($row['acct_gbn'] == '1'){
			$data[$orgNo]['ACCT']['CMS'] = $row['acct_amt'];
		}else if ($row['acct_gbn'] == '2'){
			$data[$orgNo]['ACCT']['BANK'] = $row['acct_amt'];
		}
	}

	$conn->row_free();


	//입금연결내역
	$sql = 'SELECT	org_no, yymm, acct_gbn, SUM(link_amt) AS link_amt
			FROM	(
					SELECT	org_no, yymm, CASE WHEN link_stat IS NULL THEN \'1\' ELSE \'2\' END AS acct_gbn, link_amt
					FROM	cv_cms_link
					WHERE	acct_ym	= \''.$yymm.'\'
					AND		del_flag= \'N\'
					AND		IFNULL(link_stat,\'1\') = \'1\'
					) AS a
			GROUP	BY org_no, yymm, acct_gbn';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if ($row['acct_gbn'] == '1'){
			$data[$orgNo]['LINK']['CMS'] = $row['link_amt'];
		}else if ($row['acct_gbn'] == '2'){
			$data[$orgNo]['LINK']['BANK'] = $row['link_amt'];
		}
	}

	$conn->row_free();


	//무통장 입금내역
	$sql = 'SELECT	org_no, link_amt
			FROM	cv_cms_link
			WHERE	del_flag = \'N\'
			AND		IFNULL(link_stat,\'1\') != \'1\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];
		$data[$orgNo]['IN']['BANK'] += $row['link_amt'];
	}

	$conn->row_free();


	//미연결 CMS
	$sql = 'SELECT	org_no, SUM(in_amt) - SUM(link_amt) AS in_amt
			FROM	(
					SELECT	a.org_no, a.cms_no, a.cms_dt, a.seq, a.in_amt, SUM(IFNULL(b.link_amt,0)) AS link_amt
					FROM	cv_cms_reg AS a
					LEFT	JOIN	cv_cms_link AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.cms_seq	= a.seq
							AND		b.del_flag	= \'N\'
					WHERE	a.org_no	!= \'\'
					AND		a.del_flag	 = \'N\'
					GROUP	BY a.org_no, a.cms_no, a.cms_dt, a.seq
					) AS a
			GROUP	BY org_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];
		$data[$orgNo]['IN']['CMS'] += $row['in_amt'];
	}

	$conn->row_free();


	if (is_array($data)){
		$no = 0;
		foreach($data as $orgNo => $R){
			if (($R['ACCT']['CMS'] + $R['ACCT']['BANK']) - ($R['LINK']['CMS'] + $R['LINK']['BANK']) > 0){
				$no ++; ?>
				<tr>
					<td class="center"><?=$no;?></td>
					<td><div class="left"><?=$R['name'];?></div></td>
					<td><div class="left nowrap" style="width:90px;"><?=$orgNo;?></div></td>
					<td><div class="right"><?=number_format($R['ACCT']['CMS'] + $R['ACCT']['BANK']);?></div></td>
					<td><div class="right"><?=number_format($R['LINK']['CMS'] + $R['LINK']['BANK']);?></div></td>
					<td><div class="right"><?=number_format(($R['ACCT']['CMS'] + $R['ACCT']['BANK']) - ($R['LINK']['CMS'] + $R['LINK']['BANK']));?></div></td>
					<td><div class="right"><?=number_format($R['IN']['CMS'] + $R['IN']['BANK']);?></div></td>
					<td><div class="right"><?=number_format($R['IN']['CMS']);?></div></td>
					<td><div class="right"><?=number_format($R['IN']['BANK']);?></div></td>
					<td class="last"></td>
				</tr><?
			}
		}
	}

	if ($no < 1){?>
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);


	include_once('../inc/_db_close.php');
?>
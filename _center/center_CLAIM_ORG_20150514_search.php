<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	//전년까지 미납분
	$sql = 'SELECT	a.org_no, b.org_nm, b.mg_nm, SUM(a.acct_amt) AS acct_amt
			FROM	cv_svc_acct_list AS a
			INNER	JOIN (
					SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm
					FROM	m00center
					WHERE	m00_domain =\''.$company.'\'
					) AS b
					ON		b.org_no = a.org_no
			WHERE	a.acct_ym < \''.$yymm.'\'
			GROUP	BY a.org_no
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrNonpay[$row['org_no']] = Array('orgNm'=>$row['org_nm'],'mgNm'=>$row['mg_nm'],'amt'=>$row['acct_amt']);
	}

	$conn->row_free();


	$sql = 'SELECT	a.org_no, b.org_nm, b.mg_nm, SUM(a.link_amt) AS link_amt
			FROM	cv_cms_link AS a
			INNER	JOIN (
					SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm
					FROM	m00center
					WHERE	m00_domain =\''.$company.'\'
					) AS b
					ON		b.org_no = a.org_no
			WHERE	a.acct_ym < \''.$yymm.'\'
			AND		a.del_flag= \'N\'
			AND		IFNULL(a.link_stat,\'1\') = \'1\'
			GROUP	BY a.org_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!is_array($arrNonpay[$row['org_no']])){
			$arrNonpay[$row['org_no']] = Array('orgNm'=>$row['org_nm'],'mgNm'=>$row['mg_nm'],'amt'=>0);
		}

		$arrNonpay[$row['org_no']]['amt'] -= $row['link_amt'];
	}

	$conn->row_free();


	//청구내역
	$sql = 'SELECT	a.org_no, b.org_nm, b.mg_nm, a.acct_ym, SUM(a.acct_amt) AS acct_amt, SUM(a.link_amt) AS link_amt
			FROM	(
					SELECT	a.org_no, a.acct_ym, IFNULL(a.acct_amt,0) AS acct_amt, IFNULL(b.link_amt,0) AS link_amt
					FROM	(
							SELECT	org_no, acct_ym, SUM(acct_amt) AS acct_amt
							FROM	cv_svc_acct_list
							WHERE	acct_ym = \''.$yymm.'\'
							GROUP	BY org_no, acct_ym
							) AS a
					LEFT	JOIN (
							SELECT	org_no, acct_ym, SUM(link_amt) AS link_amt
							FROM	cv_cms_link
							WHERE	acct_ym	 = \''.$yymm.'\'
							AND		del_flag = \'N\'
							AND		CASE WHEN IFNULL(link_stat,\'\') = \'\' THEN \'1\' ELSE link_stat END = \'1\'
							GROUP	BY org_no, acct_ym
							) AS b
							ON		b.org_no	= a.org_No
							AND		b.acct_ym	= a.acct_ym
					) AS a
			INNER	JOIN (
					SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm
					FROM	m00center
					WHERE	m00_domain =\''.$company.'\'
					) AS b
					ON		b.org_no = a.org_no
			GROUP	BY a.org_no, a.acct_ym
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		$data[$orgNo]['orgNm'] = $row['org_nm'];
		$data[$orgNo]['mgNm'] = $row['mg_nm'];
		$data[$orgNo]['acctAmt'] = $row['acct_amt'];
		//$data[$orgNo]['unpaid'] = $arrNonpay[$orgNo];
		$data[$orgNo]['deposit'] = $row['link_amt'];
		$data[$orgNo]['nonpay'] = $row['acct_amt'] - $row['link_amt'];

	}

	$conn->row_free();

	if (is_array($arrNonpay)){
		foreach($arrNonpay as $orgNo => $R){
			$data[$orgNo]['orgNm'] = $R['orgNm'];
			$data[$orgNo]['mgNm'] = $R['mgNm'];
			$data[$orgNo]['unpaid'] = $R['amt'];
		}
	}

	if (is_array($data)){
		$no = 1;

		foreach($data as $orgNo => $R){?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><div class="left"><?=$R['orgNm'];?></div></td>
				<td class="center"><div class="left"><?=$orgNo;?></div></td>
				<td class="center"><div class="left"><?=$R['mgNm'];?></div></td>
				<td class="center"><div class="right"><?=number_format($R['acctAmt']+$R['unpaid']);?></div></td>
				<td class="center"><div class="right"><?=number_format($R['acctAmt']);?></div></td>
				<td class="center"><div class="right"><?=number_format($R['unpaid']);?></div></td>
				<td class="center"><div class="right"><?=number_format($R['deposit']);?></div></td>
				<td class="center"><div class="right"><?=number_format($R['nonpay']);?></div></td>
				<td class="center last">
					<div class="left">
						<span class="btn_pack small"><button onclick="lfPopDtl('<?=$orgNo;?>');">상세</button></span>
					</div>
				</td>
			</tr><?

			$tot['acctAmt'] += $R['acctAmt'];
			$tot['unpaid']	+= $R['unpaid'];
			$tot['deposit'] += $R['deposit'];
			$tot['nonpay']	+= $R['nonpay'];

			$no ++;
		}?>
		<!--CUT_LINE-->
		<tr>
			<td class="sum center" colspan="4"><div class="right">합계</div></td>
			<td class="sum center"><div class="right"><?=number_format($tot['acctAmt']+$tot['unpaid']);?></div></td>
			<td class="sum center"><div class="right"><?=number_format($tot['acctAmt']);?></div></td>
			<td class="sum center"><div class="right"><?=number_format($tot['unpaid']);?></div></td>
			<td class="sum center"><div class="right"><?=number_format($tot['deposit']);?></div></td>
			<td class="sum center"><div class="right"><?=number_format($tot['nonpay']);?></div></td>
			<td class="sum center last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
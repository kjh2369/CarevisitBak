<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	//청구분
	$sql = 'SELECT	org_no, SUM(acct_amt) AS acct_amt
			FROM	cv_svc_acct_list
			WHERE	acct_ym < \''.$yymm.'\'
			GROUP	BY org_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrNonpay[$row['org_no']] = $row['acct_amt'];
	}

	$conn->row_free();


	//입금분
	$sql = 'SELECT	org_no, SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	acct_ym	< \''.$yymm.'\'
			AND		del_flag= \'N\'
			AND		IFNULL(link_stat,\'1\') = \'1\'
			GROUP	BY org_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrNonpay[$row['org_no']] -= $row['link_amt'];
	}

	$conn->row_free();


	//청구내역
	$sql = 'SELECT	a.org_no
			,		a.org_nm
			,		CASE WHEN a.acct_gbn = \'1\' THEN \'CMS\'
						 WHEN a.acct_gbn = \'2\' THEN \'BANK\' ElSE a.acct_gbn END AS acct_gbn
			,		a.acct_ym
			,		a.acct_amt
			,		CASE WHEN c.in_gbn = \'1\' THEN \'CMS\'
						 WHEN c.in_gbn = \'2\' THEN \'BANK\' ELSE \'\' END AS link_gbn
			,		b.link_amt
			FROM	(
					SELECT	a.org_no, b.org_nm, a.acct_gbn, a.yymm, a.acct_ym
					,		SUM(CASE WHEN svc_gbn != \'9\' AND svc_cd != \'99\' THEN acct_amt ELSE 0 END - CASE WHEN svc_gbn = \'9\' AND svc_cd = \'99\' THEN acct_amt ELSE 0 END) AS acct_amt
					FROM	cv_svc_acct_list AS a
					INNER	JOIN (
							SELECT	m00_mcode AS org_no, GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm
							FROM	m00center
							WHERE	m00_domain = \''.$company.'\'
							GROUP	BY m00_mcode
							) AS b
							ON		b.org_no = a.org_no
					WHERE	a.acct_ym = \''.$yymm.'\'
					AND		a.acct_amt != 0
					GROUP	BY a.org_no, a.acct_gbn, a.yymm, a.acct_ym
					) AS a
			LEFT	JOIN	cv_cms_link AS b
					ON		b.org_no	= a.org_no
					AND		b.yymm		= a.yymm
					AND		b.del_flag	= \'N\'
			LEFT	JOIN	cv_cms_reg AS c
					ON		c.org_no = a.org_no
					AND		c.cms_no = b.cms_no
					AND		c.cms_dt = b.cms_dt
					AND		c.seq	 = b.cms_seq
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$data[$orgNo]) $data[$orgNo]['name'] = $row['org_nm'];

		if (!$tmpCMS[$row['org_no'].'_'.$row['acct_gbn']]){
			 $tmpCMS[$row['org_no'].'_'.$row['acct_gbn']] = 'Y';
			 $data[$orgNo]['C'][$row['acct_gbn']] += $row['acct_amt']; //청구내역
		}

		if (!$tmpBank[$row['org_no'].'_'.$row['link_gbn']]){
			 $tmpBank[$row['org_no'].'_'.$row['link_gbn']] = 'Y';
			 $data[$orgNo]['A'][$row['link_gbn']] += $row['link_amt']; //입금내역
		}
	}

	$conn->row_free();


	if (is_array($data)){
		$no = 1;

		foreach($data as $orgNo => $R){
			$R['N']['NP'] = $arrNonpay[$orgNo];
			$R['N']['CMS'] = $R['C']['CMS'] - $R['A']['CMS'];
			$R['N']['BANK'] = $R['C']['BANK'] - $R['A']['BANK'];
			?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td><div class="left"><?=$R['name'];?></div></td>
				<td style="background-color:#FFF2E6;"><div class="right"><?=number_format($R['C']['CMS']+$R['C']['BANK']);?></div></td>
				<td style="background-color:#FFF2E6;"><div class="right"><?=number_format($R['C']['CMS']);?></div></td>
				<td style="background-color:#FFF2E6;"><div class="right"><?=number_format($R['C']['BANK']);?></div></td>
				<td style="background-color:#F6FFCC;"><div class="right"><?=number_format($R['A']['CMS']+$R['A']['BANK']);?></div></td>
				<td style="background-color:#F6FFCC;"><div class="right"><?=number_format($R['A']['CMS']);?></div></td>
				<td style="background-color:#F6FFCC;"><div class="right"><?=number_format($R['A']['BANK']);?></div></td>
				<td style="background-color:#FAEBFF;"><div class="right"><?=number_format($R['N']['NP'] + $R['N']['CMS'] + $R['N']['BANK']);?></div></td>
				<td style="background-color:#FAEBFF;"><div class="right"><?=number_format($R['N']['NP']);?></div></td>
				<td style="background-color:#FAEBFF;"><div class="right"><?=number_format($R['N']['CMS']);?></div></td>
				<td style="background-color:#FAEBFF;"><div class="right"><?=number_format($R['N']['BANK']);?></div></td>
				<td class="last"></td>
			</tr><?

			$tot['C']['CMS'] += $R['C']['CMS'];
			$tot['C']['BANK'] += $R['C']['BANK'];
			$tot['A']['CMS'] += $R['A']['CMS'];
			$tot['A']['BANK'] += $R['A']['BANK'];
			$tot['N']['NP'] += $R['N']['NP'];
			$tot['N']['CMS'] += $R['N']['CMS'];
			$tot['N']['BANK'] += $R['N']['BANK'];

			$no ++;
		}?>
		<!--CUT_LINE-->
		<tr>
			<td class="sum" colspan="2"><div class="right">합계</div></td>
			<td class="sum" colspan="3"><div class="right"><?=number_format($tot['C']['CMS']+$tot['C']['BANK']);?></div></td>
			<!--<td class="sum"><div class="right"><?=number_format($tot['C']['CMS']);?></div></td>-->
			<!--<td class="sum"><div class="right"><?=number_format($tot['C']['BANK']);?></div></td>-->
			<td class="sum" colspan="3"><div class="right"><?=number_format($tot['A']['CMS']+$tot['A']['BANK']);?></div></td>
			<!--<td class="sum"><div class="right"><?=number_format($tot['A']['CMS']);?></div></td>-->
			<!--<td class="sum"><div class="right"><?=number_format($tot['A']['BANK']);?></div></td>-->
			<td class="sum" colspan="4"><div class="right"><?=number_format($tot['N']['NP'] + $tot['N']['CMS'] + $tot['N']['BANK']);?></div></td>
			<!--<td class="sum"><div class="right"><?=number_format($tot['N']['NP']);?></div></td>-->
			<!--<td class="sum"><div class="right"><?=number_format($tot['N']['CMS']);?></div></td>-->
			<!--<td class="sum"><div class="right"><?=number_format($tot['N']['BANK']);?></div></td>-->
			<td class="sum last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}


	Unset($data);
	Unset($tmpCMS);
	Unset($tmpBank);
	Unset($arrNonpay);

	include_once('../inc/_db_close.php');
?>
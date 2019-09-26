<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	//$addr	= $_POST['addr'];
	//$page	= $_POST['page'];

	//$itemCnt = 25;

	$sql = 'SELECT	UPPER(org_no) AS org_no, SUM(acct_amt) AS amt
			FROM	cv_svc_acct_list
			WHERE	acct_ym <= \''.$yymm.'\'
			GROUP	BY org_no';

	$acctAmt = $conn->_fetch_array($sql,'org_no');

	$sql = 'SELECT	org_no, SUM(in_amt) AS amt
			FROM	cv_cms_reg
			WHERE	LEFT(cms_dt,6) <= \''.$yymm.'\'
			AND		del_flag = \'N\'
			GROUP	BY org_no';

	$inAmt = $conn->_fetch_array($sql,'org_no');

	$sql = 'SELECT	a.org_no, a.cms_no, a.cms_dt, a.seq, c.org_nm, a.in_gbn, a.in_amt, a.in_dt, a.cms_com, a.cms_mem_no
			,		b.yymm, b.acct_ym
			,		SUM(b.link_amt) AS link_amt
			FROM	cv_cms_reg AS a
			INNER	JOIN (
					SELECT	DISTINCT m00_mcode AS org_no, GROUP_CONCAT(m00_store_nm) AS org_nm
					FROM	m00center
					WHERE	m00_mcode != \'\'';

	if ($orgNo) $sql .= ' AND m00_mcode LIKE \''.$orgNo.'%\'';
	if ($orgNm) $sql .= ' AND m00_store_nm LIKE \''.$orgNm.'%\'';
	if ($mgNm) $sql .= ' AND m00_mname LIKE \''.$mgNm.'%\'';

	$sql .= '		GROUP	BY m00_mcode
					) AS c
					ON		c.org_no = a.org_no
			LEFT	JOIN	cv_cms_link AS b
					ON		b.org_no	= a.org_no
					AND		b.cms_no	= a.cms_no
					AND		b.cms_dt	= a.cms_dt
					AND		b.cms_seq	= a.seq
					AND		b.del_flag	= \'N\'
			WHERE	LEFT(a.cms_dt,6) = \''.$yymm.'\'
			AND		a.del_flag	= \'N\'
			GROUP	BY a.org_no, a.cms_no, a.cms_dt, a.seq
			ORDER	BY org_nm, org_no, in_gbn, cms_dt, cms_mem_no';

	//$sql = 'SELECT	COUNT(*)
	//		FROM	('.$bsl.') AS a';
	//$totCnt = $conn->get_data($sql);

	#if ($totCnt < (IntVal($page) - 1) * $itemCnt){
	#	$page = 1;
	#}

	//$pageCnt = (intVal($page) - 1) * $itemCnt;


	//$sql = $bsl.' LIMIT '.$pageCnt.','.$itemCnt;

	//echo '<tr><td colspan="11">'.nl2br($sql).'</td></tr>';


	/*
	$sql = 'SELECT	a.org_no, a.org_nm, a.yymm, a.acct_ym, a.acct_amt
			,		b.cms_no, b.cms_dt, b.seq AS cms_seq, IFNULL(b.in_amt,0) AS in_amt
			,		c.link_amt
			FROM	(
					SELECT	a.org_no, m00_store_nm AS org_nm, a.yymm, a.acct_ym, a.acct_amt, a.tmp_amt
					FROM	(
							SELECT	org_no, yymm, acct_ym, SUM(acct_amt) AS acct_amt, SUM(tmp_amt) AS tmp_amt
							FROM	cv_svc_acct_list
							WHERE	acct_ym = \''.$yymm.'\'
							GROUP	BY org_no, yymm
							) AS a
					INNER	JOIN	m00center
							ON		m00_mcode = a.org_no
					GROUP	BY a.org_no, a.yymm
					) AS a
			LEFT	JOIN	cv_cms_reg AS b
					ON		b.org_no	= a.org_no
					AND		b.del_flag	= \'N\'
					AND		LEFT(b.cms_dt,6) = a.acct_ym
			LEFT	JOIN	cv_cms_link AS c
					ON		c.org_no= a.org_no
					AND		c.yymm	= a.yymm
					AND		c.cms_no= b.cms_no
					AND		c.cms_dt= b.cms_dt
					AND		c.cms_seq = b.seq
			WHERE	b.cms_no IS NOT NULL
			ORDER	BY org_nm, org_no';
	*/
	//echo '<tr><td colspan="11">'.nl2br($sql).'</td></tr>';


	/*
	$sql = 'SELECT	a.org_no, a.org_nm, a.yymm, a.acct_ym, IFNULL(a.acct_amt,0) AS acct_amt
			,		b.cms_no, b.cms_dt, b.seq AS cms_seq, IFNULL(b.in_amt,0) AS in_amt, b.in_dt
			,		IFNULL(c.link_amt,0) AS link_amt
			FROM	(
					SELECT	a.org_no, a.org_nm, b.yymm, IFNULL(b.acct_ym,\''.$yymm.'\') AS acct_ym, b.acct_amt, b.tmp_amt
					FROM	(
							SELECT	m00_mcode AS org_no, m00_store_nm AS org_nm
							FROM	m00center
							GROUP	BY m00_mcode
							) AS a
					INNER	JOIN (
							SELECT	org_no, yymm, acct_ym, SUM(acct_amt) AS acct_amt, SUM(tmp_amt) AS tmp_amt
							FROM	cv_svc_acct_list
							WHERE	acct_ym = \''.$yymm.'\'
							GROUP	BY org_no, yymm
							) AS b
							ON		b.org_no	= a.org_no
					) AS a
			LEFT	JOIN	cv_cms_link AS c
					ON		c.org_no	= a.org_no
					AND		c.yymm		= a.yymm
					AND		c.del_flag	= \'N\'
			LEFT	JOIN	cv_cms_reg AS b
					ON		b.org_no	= a.org_no
					AND		b.cms_no	= c.cms_no
					AND		b.cms_dt	= c.cms_dt
					AND		b.seq		= c.cms_seq
					AND		b.del_flag	= \'N\'
			WHERE	IFNULL(a.acct_amt,0) + IFNULL(b.in_amt,0) + IFNULL(c.link_amt,0) > 0
			ORDER	BY org_nm, org_no';
	*/

	$sql = 'SELECT	a.org_no, a.org_nm, a.yymm, a.acct_ym, a.acct_amt
			,		b.cms_no, b.cms_dt, b.cms_seq, b.in_dt, b.in_amt, b.link_amt
			FROM	(
					SELECT	a.org_no, a.org_nm, b.yymm, b.acct_ym, b.acct_amt, b.tmp_amt
					FROM	(
							SELECT	m00_mcode AS org_no, m00_store_nm AS org_nm
							FROM	m00center
							WHERE	m00_mcode != \'\'';

	if ($orgNo) $sql .= ' AND m00_mcode LIKE \''.$orgNo.'%\'';
	if ($orgNm) $sql .= ' AND m00_store_nm LIKE \'%'.$orgNm.'%\'';
	if ($mgNm) $sql .= ' AND m00_mname LIKE \'%'.$mgNm.'%\'';

	$sql .= '				GROUP	BY m00_mcode
							) AS a
					INNER	JOIN (
							SELECT	org_no, yymm, IFNULL(acct_ym,\''.$yymm.'\') AS acct_ym, SUM(acct_amt) AS acct_amt, SUM(tmp_amt) AS tmp_amt
							FROM	cv_svc_acct_list
							WHERE	acct_ym = \''.$yymm.'\'
							GROUP	BY org_no, yymm
							) AS b
							ON		b.org_no = a.org_no
					/*WHERE	b.acct_amt > 0*/
					) AS a
			LEFT	JOIN (
					SELECT	a.org_no, a.cms_no, a.cms_dt, a.seq AS cms_seq, a.in_dt, IFNULL(a.in_amt,0) AS in_amt, b.link_amt, b.yymm, b.acct_ym
					FROM	cv_cms_reg AS a
					LEFT	JOIN	cv_cms_link AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.cms_seq	= a.seq
							AND		b.acct_ym	= \''.$yymm.'\'
							AND		b.del_flag	= \'N\'
					WHERE	MID(a.cms_dt,1,6) = \''.$yymm.'\'
					AND		a.del_flag = \'N\'';
					/*
					UNION	ALL
					SELECT	a.org_no, a.cms_no, a.cms_dt, a.cms_seq, b.in_dt, a.link_amt, a.link_amt, a.yymm, a.acct_ym
					FROM	cv_cms_link AS a
					LEFT	JOIN	cv_cms_reg AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.seq		= a.cms_seq
							AND		b.del_flag	= \'N\'
					WHERE	a.acct_ym	= \''.$yymm.'\'
					AND		a.org_amt	= \'0\'
					AND		a.del_flag	= \'N\'
					*/
	$sql .= '		) AS b
					ON		b.org_no = a.org_no
			ORDER	BY org_nm, org_no';

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';


	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$data[$orgNo]){
			if ($row['in_gbn'] == '1'){
				$data[$orgNo]['ORG']['inGbn'] = 'CMS';
			}else if ($row['in_gbn'] == '2'){
				$data[$orgNo]['ORG']['inGbn'] = '무통장';
			}else{
				$data[$orgNo]['ORG']['inGbn'] = $row['in_gbn'];
			}

			$data[$orgNo]['ORG']['name'] = $row['org_nm'];
			$data[$orgNo]['ORG']['acctDt'] = $yymm.'20'; //$row['cms_dt'];
			//$data[$orgNo]['ORG']['acctAmt'] = $acctAmt[StrToUpper($row['org_no'])]['acct_amt'];
			$data[$orgNo]['ORG']['acctAmt'] = $row['acct_amt'];
			$data[$orgNo]['ORG']['inAmt'] = 0;
			$data[$orgNo]['ORG']['cnt'] = 0;
		}

		$data[$orgNo]['ORG']['cnt'] ++;
		$data[$orgNo]['ORG']['inAmt'] += $row['in_amt'];

		$data[$orgNo]['LIST'][] = Array(
			'cmsNo'=>$row['cms_no']
		,	'cmsSeq'=>$row['seq']
		,	'totInAmt'=>0
		,	'inDt'=>$row['in_dt']
		,	'inAmt'=>$row['in_amt']
		,	'linkAmt'=>$row['link_amt']
		,	'unlinkAmt'=>$row['in_amt'] - $row['link_amt']
		);

		$data[$orgNo]['LIST'][0]['totInAmt'] += $row['in_amt'];
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;

		$tot[4] = 0;
		$tot[6] = 0;
		$tot[7] = 0;
		$tot[8] = 0;
		$tot[9] = 0;

		foreach($data as $orgNo => $orgR){
			/*
			if ($orgR['ORG']['acctAmt'] > $orgR['ORG']['inAmt']){
				$bgcolor = '#FFDDDD';
				$color = 'RED';
			}else if ($orgR['ORG']['acctAmt'] < $orgR['ORG']['inAmt']){
				$bgcolor = '#EBE8FF';
				$color = 'BLUE';
			}else{
				$bgcolor = '';
				$color = '';
			}
			*/
			if ($orgR['ORG']['acctAmt'] > $orgR['ORG']['inAmt']){
				$bgcolor = '#FFDDDD';
				$color = 'RED';
			}else if ($orgR['ORG']['acctAmt'] < $orgR['ORG']['inAmt']){
				$bgcolor = '#EBE8FF';
				$color = 'BLUE';
			}else{
				$bgcolor = '';
				$color = '';
			}?>
			<tr>
			<td class="center" style="background-color:<?=$bgcolor;?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><?=$no;?></td>
			<td class="center" style="background-color:<?=$bgcolor;?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="left nowrap" style="width:100px;"><?=$orgR['ORG']['name'];?></div></td>
			<td class="center" style="background-color:<?=$bgcolor;?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="left nowrap" style="width:70px;"><?=$orgNo;?></div></td>
			<td class="center" style="background-color:#FFFED7;" rowspan="<?=$orgR['ORG']['cnt'];?>"><?=$myF->dateStyle($orgR['ORG']['acctDt'],'.');?></td>
			<td class="center" style="background-color:#FFFED7; color:<?=$color;?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="right"><?=number_format($orgR['ORG']['acctAmt']);?></div></td><?

			$tot[4] += $orgR['ORG']['acctAmt'];

			$IsFirst = true;

			foreach($orgR['LIST'] as $tmpIdx => $R){
				if (!$IsFirst){?>
					<tr><?
				}?>
				<td class="center" style="background-color:#E0FFDB;"><?=$myF->dateStyle($R['inDt'],'.');?></td>
				<td class="center" style="background-color:#E0FFDB; color:<?=$color;?>;"><div class="right"><?=number_format($R['inAmt']);?></div></td>
				<td class="center" style="background-color:#ECEBFF;"><div class="right"><?=number_format($R['linkAmt']);?></div></td><?
				if ($IsFirst){
					$unpay = $orgR['ORG']['acctAmt'] - $orgR['LIST'][0]['totInAmt'];
					$totUnpay = $acctAmt[StrToUpper($orgNo)]['amt'] - $inAmt[StrToUpper($orgNo)]['amt'];?>
					<!--td class="center" style="background-color:#FFEBFF;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="right"><?=number_format($R['unlinkAmt']);?></div></td-->
					<!--td class="center" style="background-color:#FFEBFF; color:<?=$color;?>;"><div class="right"><?=number_format($orgR['ORG']['acctAmt'] - $orgR['ORG']['inAmt'] > 0 ? $orgR['ORG']['acctAmt'] - $orgR['ORG']['inAmt'] : '');?></div></td-->
					<td class="center" style="background-color:#FFEBFF; color:<?=$unpay > 0 ? 'RED' : 'BLUE';?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="right"><?=number_format($unpay);?></div></td>
					<td class="center" style="background-color:#FFEBFF; color:<?=$totUnpay > 0 ? 'RED' : 'BLUE';?>;" rowspan="<?=$orgR['ORG']['cnt'];?>"><div class="right"><?=number_format($totUnpay);?></div></td><?
				}?>
				<td class="center last">
					<div class="CLS_BTN left">
						<span class="btn_pack small"><button onclick="lfModify('<?=$no;?>','<?=$orgNo;?>','<?=$R['cmsNo'];?>','<?=$orgR['ORG']['acctDt'];?>','<?=$R['cmsSeq'];?>');">수정</button></span>
						<span class="btn_pack small"><button onclick="lfMenu('CLAIM_ORG_DTL','&orgNo=<?=$orgNo;?>&year=<?=$year;?>','Y');">상세</button></span>
					</div>
				</td>
				</tr><?

				$tot[6] += $R['inAmt'];
				$tot[7] += $R['linkAmt'];
				//$tot[8] += $R['unlinkAmt'];
				//$tot[9] += 0;

				if ($IsFirst){
					$tot[8] += $unpay;
					$tot[9] += $totUnpay;
				}

				$IsFirst = false;
			}

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);
	Unset($acctAmt);
	Unset($inAmt);

	include_once('../inc/_db_close.php');?>

	<script type="text/javascript">
		$('#ID_CELL_SUM_4').text(__num2str('<?=$tot[4];?>'));
		$('#ID_CELL_SUM_6').text(__num2str('<?=$tot[6];?>'));
		$('#ID_CELL_SUM_7').text(__num2str('<?=$tot[7];?>'));
		$('#ID_CELL_SUM_8').text(__num2str('<?=$tot[8];?>'));
		$('#ID_CELL_SUM_9').text(__num2str('<?=$tot[9];?>'));
	</script><?
?>
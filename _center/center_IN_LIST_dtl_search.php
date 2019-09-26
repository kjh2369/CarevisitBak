<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$gbn	= $_POST['gbn'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$page	= $_POST['page'];

	$itemCnt = 10;

	if ($gbn == 'CMS'){
		$bsl = 'SELECT	a.cms_no, a.cms_dt AS dt, a.seq, a.in_amt, b.yymm, b.acct_ym, b.link_amt
				FROM	cv_cms_reg AS a
				LEFT	JOIN	cv_cms_link AS b
						ON		b.org_no	= a.org_no
						AND		b.cms_no	= a.cms_no
						AND		b.cms_dt	= a.cms_dt
						AND		b.cms_seq	= a.seq
						AND		b.del_flag	= \'N\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.del_flag	= \'N\'';

		if ($fromDt && $toDt){
			$bsl .= ' AND a.cms_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
		}else if ($fromDt){
			$bsl .= ' AND a.cms_dt >= \''.$fromDt.'\'';
		}else if ($toDt){
			$bsl .= ' AND a.cms_dt <= \''.$toDt.'\'';
		}
	}else{
		$bsl = 'SELECT	a.bank_dt AS dt, a.bank_nm AS cms_no, a.org_amt AS in_amt, b.acct_ym, b.link_amt
				FROM	(
						SELECT	bank_dt, bank_nm, org_amt
						FROM	cv_cms_link
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		del_flag = \'N\'
						AND		IFNULL(link_stat, \'\') != \'1\'
						) AS a
				INNER	JOIN (
						SELECT	bank_dt, acct_ym, link_amt
						FROM	cv_cms_link
						WHERE	org_no		= \''.$orgNo.'\'
						AND		link_stat	= \'1\'
						AND		del_flag	= \'N\'
						) AS b
						ON	b.bank_dt = a.bank_dt
				WHERE	a.bank_dt != \'\'';

		if ($fromDt && $toDt){
			$bsl .= ' AND a.bank_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
		}else if ($fromDt){
			$bsl .= ' AND a.bank_dt >= \''.$fromDt.'\'';
		}else if ($toDt){
			$bsl .= ' AND a.bank_dt <= \''.$toDt.'\'';
		}
	}


	$sql = 'SELECT	COUNT(*)
			FROM	('.$bsl.') AS a';
	$totCnt = $conn->get_data($sql);

	$pageCnt = (intVal($page) - 1) * $itemCnt;


	$sql = 'SELECT	*
			FROM	('.$bsl.') AS a
			ORDER	BY dt, acct_ym
			LIMIT	'.$pageCnt.','.$itemCnt;

	#echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$inDt	= $row['dt'];
		$yymm	= $row['acct_ym'];

		if (!$data[$inDt]){
			 $data[$inDt] = Array(
				'CMS'=>$row['cms_no']
			,	'inAmt'=>$row['in_amt']
			,	'notlink'=>$row['in_amt']
			);
		}

		$data[$inDt]['SUB'][$yymm]['linkAmt'] = $row['link_amt'];
		$data[$inDt]['notlink'] -= $row['link_amt'];

		$data[$inDt]['cnt'] ++;
	}

	$conn->row_free();


	if (is_array($data)){
		$no = 1;

		foreach($data as $inDt => $R1){?>
			<tr>
			<td class="center" rowspan="<?=$R1['cnt'];?>"><?=$pageCnt + $no;?></td>
			<td class="center" rowspan="<?=$R1['cnt'];?>"><?=$myF->dateStyle($inDt,'.');?></td>
			<td><div class="left"><?=$R1['CMS']?></div></td>
			<td><div class="right"><?=$R1['inAmt'] != 0 ? number_format($R1['inAmt']) : '';?></div></td><?

			$IsFirst = true;

			foreach($R1['SUB'] as $yymm => $R2){
				if ($IsFirst){
					$IsFirst = false;
				}else{?>
					<tr><?
				}?>
				<td class="center"><?=$myF->_styleYYMM($yymm,'.');?></td>
				<td><div class="right"><?=$R2['linkAmt'] != 0 ? number_format($R2['linkAmt']) : '';?></div></td><?

				if ($R1['cnt'] > 0){?>
					<td rowspan="<?=$R1['cnt'];?>"><div class="right"><?=$R1['notlink'] != 0 ? number_format($R1['notlink']) : '';?></div></td>
					<td rowspan="<?=$R1['cnt'];?>"></td><?

					$R1['cnt'] = 0;
				}
			}

			$no ++;
		}
	}


	include_once('../inc/_db_close.php');
?>
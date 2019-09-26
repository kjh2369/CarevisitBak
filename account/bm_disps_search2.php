<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	a.org_no
			,		a.org_nm
			,		CAST(MID(b.yymm,5) AS unsigned) AS month
			,		b.allot_cnt
			,		b.employ_cnt
			,		b.allot_amt
			,		b.deduct_amt
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS org_no
					,		m00_store_nm AS org_nm
					FROM	m00center
					INNER	JOIN	b02center
							ON		b02_center = m00_mcode
					WHERE	m00_domain = \''.$gDomain.'\'
					) AS a
			LEFT	JOIN	ie_bm_disps AS b
					ON		b.org_no = a.org_no
					AND		LEFT(b.yymm,4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$orgNo = $row['org_no'];

		if (!$data[$orgNo]['name']){
			$data[$orgNo]['name'] = str_replace('돌보인','',str_replace('돌보인요양보호사','',str_replace('돌보인 방문요양센터 ','',$row['org_nm'])));

			$sql = 'SELECT	MAX(yymm)
					FROM	ie_bm_disps
					WHERE	org_no	= \''.$orgNo.'\'
					AND		allot_cnt + employ_cnt + allot_amt + deduct_amt > 0';

			$data[$orgNo]['yymm'] = $conn->get_data($sql);
		}

		$data[$orgNo]['1'][$row['month']]['CNT'] = $row['allot_cnt'];
		$data[$orgNo]['1'][$row['month']]['AMT'] = $row['allot_amt'];
		$data[$orgNo]['2'][$row['month']]['CNT'] = $row['employ_cnt'];
		$data[$orgNo]['2'][$row['month']]['AMT'] = $row['deduct_amt'];

		$data[$orgNo]['1']['AMT1'] += $row['allot_amt'];
		$data[$orgNo]['2']['AMT1'] += $row['deduct_amt'];

		$tot['1'][$row['month']]['CNT'] += $row['allot_cnt'];
		$tot['2'][$row['month']]['CNT'] += $row['employ_cnt'];

		$tot['1'][$row['month']]['AMT'] += $row['allot_amt'];
		$tot['2'][$row['month']]['AMT'] += $row['deduct_amt'];

		$tot['1']['AMT1'] += $row['allot_amt'];
		$tot['2']['AMT1'] += $row['deduct_amt'];
	}

	$conn->row_free();


	if (is_array($data)){
		$no = 1;

		foreach($data as $orgNo => $row){
			$sql = 'SELECT	allot_amt
					,		deduct_amt
					FROM	ie_bm_disps
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$row['yymm'].'\'';

			$R = $conn->get_array($sql);

			$row['1']['AMT2'] = $R['allot_amt'];
			$row['2']['AMT2'] = $R['deduct_amt'];

			$tot['1']['AMT2'] += $R['allot_amt'];
			$tot['2']['AMT2'] += $R['deduct_amt'];

			Unset($R);

			if ($no % 2 == 1){
				$color = 'FFFFFF';
			}else{
				$color = 'F6F6F6';
			}?>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center" rowspan="2"><?=$no;?></td>
				<td class="center" rowspan="2"><div class="left"><?=$row['name'];?></div></td>
				<td class="center">분담인원</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_1" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['1'][$i]['AMT'];?>"><?=$row['1'][$i]['CNT'] > 0 ? number_format($row['1'][$i]['CNT']) : '';?></div></td><?
				}?>
				<td class="center"><div id="ID_CELL_CNT_<?=$orgNo;?>_1" class="right"><?=$row['1']['AMT2'] > 0 ? number_format($row['1']['AMT2']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_1" class="right"><?=$row['1']['AMT1'] > 0 ? number_format($row['1']['AMT1']) : '';?></div></td>
				<td class="center bottom last"></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">채용인원</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_2" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['2'][$i]['AMT'];?>"><?=$row['2'][$i]['CNT'] > 0 ? number_format($row['2'][$i]['CNT']) : '';?></div></td><?
				}?>
				<td class="center"><div id="ID_CELL_CNT_<?=$orgNo;?>_2" class="right"><?=$row['2']['AMT2'] > 0 ? number_format($row['2']['AMT2']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_2" class="right"><?=$row['2']['AMT1'] > 0 ? number_format($row['2']['AMT1']) : '';?></div></td>
				<td class="center last"></td>
			</tr><?

			$no ++;
		}?>
		<!--CUT_LINE-->
		<tr>
			<td class="sum center" rowspan="2" colspan="2">합계</td>
			<td class="sum center">분담인원</td><?
			for($i=1; $i<=12; $i++){?>
				<td class="sum center"><div id="ID_CELL_TOT_VAL_<?=$i;?>_1" class="right" amt="<?=$tot['1'][$i]['AMT'];?>"><?=$tot['1'][$i]['CNT'] > 0 ? number_format($tot['1'][$i]['CNT']) : '';?></div></td><?
			}?>
			<td class="sum center"><div id="ID_CELL_TOT_CNT_1" class="right"><?=$tot['1']['AMT2'] > 0 ? number_format($tot['1']['AMT2']) : '';?></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_AMT_1" class="right"><?=$tot['1']['AMT1'] > 0 ? number_format($tot['1']['AMT1']) : '';?></div></td>
			<td class="sum center bottom last"></td>
		</tr>
		<tr>
			<td class="sum center">채용인원</td><?
			for($i=1; $i<=12; $i++){?>
				<td class="sum center"><div id="ID_CELL_TOT_VAL_<?=$i;?>_2" class="right" amt="<?=$tot['2'][$i]['AMT'];?>"><?=$tot['2'][$i]['CNT'] > 0 ? number_format($tot['2'][$i]['CNT']) : '';?></div></td><?
			}?>
			<td class="sum center"><div id="ID_CELL_TOT_CNT_2" class="right"><?=$tot['2']['AMT2'] > 0 ? number_format($tot['2']['AMT2']) : '';?></div></td>
			<td class="sum center"><div id="ID_CELL_TOT_AMT_2" class="right"><?=$tot['2']['AMT1'] > 0 ? number_format($tot['2']['AMT1']) : '';?></div></td>
			<td class="sum center last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="18">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);
	Unset($tot);

	include_once('../inc/_db_close.php');
?>
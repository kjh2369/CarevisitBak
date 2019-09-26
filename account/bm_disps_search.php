<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	DISTINCT
					m00_mcode AS org_no
			,		REPLACE(m00_store_nm,\'돌보인 방문요양센터\',\'\') AS org_nm
			FROM	m00center
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
			WHERE	m00_domain = \''.$gDomain.'\'
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$sl1 = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$data[$row['org_no']] = Array(
			'name'	=>$row['org_nm']
		,	'month'	=>Array(
				1=>Array(
					1	=>Array('cnt'=>0, 'amt'=>0)
				,	2	=>Array('cnt'=>0, 'amt'=>0)
				,	3	=>Array('cnt'=>0, 'amt'=>0)
				,	4	=>Array('cnt'=>0, 'amt'=>0)
				,	5	=>Array('cnt'=>0, 'amt'=>0)
				,	6	=>Array('cnt'=>0, 'amt'=>0)
				,	7	=>Array('cnt'=>0, 'amt'=>0)
				,	8	=>Array('cnt'=>0, 'amt'=>0)
				,	9	=>Array('cnt'=>0, 'amt'=>0)
				,	10	=>Array('cnt'=>0, 'amt'=>0)
				,	11	=>Array('cnt'=>0, 'amt'=>0)
				,	12	=>Array('cnt'=>0, 'amt'=>0))
			,	2=>Array(
					1	=>Array('cnt'=>0, 'amt'=>0)
				,	2	=>Array('cnt'=>0, 'amt'=>0)
				,	3	=>Array('cnt'=>0, 'amt'=>0)
				,	4	=>Array('cnt'=>0, 'amt'=>0)
				,	5	=>Array('cnt'=>0, 'amt'=>0)
				,	6	=>Array('cnt'=>0, 'amt'=>0)
				,	7	=>Array('cnt'=>0, 'amt'=>0)
				,	8	=>Array('cnt'=>0, 'amt'=>0)
				,	9	=>Array('cnt'=>0, 'amt'=>0)
				,	10	=>Array('cnt'=>0, 'amt'=>0)
				,	11	=>Array('cnt'=>0, 'amt'=>0)
				,	12	=>Array('cnt'=>0, 'amt'=>0))
			)
		,	'allotAmt'	=>0
		,	'dedcutAmt'	=>0
		,	'allotCnt'	=>0
		,	'employCnt'	=>0
		);

		if ($sl1) $sl1 .= ' UNION ALL ';
		$sl1.= 'SELECT	org_no
				,		CAST(MID(yymm,5) AS unsigned) AS month
				,		allot_cnt
				,		allot_amt
				,		employ_cnt
				,		deduct_amt
				FROM	ie_bm_disps
				WHERE	org_no		= \''.$row['org_no'].'\'
				AND		LEFT(yymm,4)= \''.$year.'\'';
	}

	$conn->row_free();

	if (is_array($data)){
		$sql = 'SELECT	org_no, month, allot_cnt, allot_amt, employ_cnt, deduct_amt
				FROM	('.$sl1.') AS a';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$data[$row['org_no']]['month'][1][$row['month']]['cnt'] = $row['allot_cnt'];
			$data[$row['org_no']]['month'][1][$row['month']]['amt'] = $row['allot_amt'];
			$data[$row['org_no']]['month'][2][$row['month']]['cnt'] = $row['employ_cnt'];
			$data[$row['org_no']]['month'][2][$row['month']]['amt'] = $row['deduct_amt'];

			$data[$row['org_no']]['allotCnt']	+= $row['allot_cnt'];
			$data[$row['org_no']]['employCnt']	+= $row['employ_cnt'];

			$data[$row['org_no']]['allotAmt']	+= $row['allot_amt'];
			$data[$row['org_no']]['dedcutAmt']	+= $row['deduct_amt'];
		}

		$conn->row_free();


		$no = 1;

		foreach($data as $orgNo => $row){
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
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_1" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['month'][1][$i]['amt'];?>"><?=$row['month'][1][$i]['cnt'] > 0 ? number_format($row['month'][1][$i]['cnt']) : '';?></div></td><?
				}?>
				<!--
					<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_1" class="right"><?=$row['allotAmt'] > 0 ? number_format($row['allotAmt']) : '';?></div></td>
				-->
				<td class="center"><div id="ID_CELL_CNT_<?=$orgNo;?>_1" class="right"><?=$row['allotCnt'] > 0 ? number_format($row['allotCnt']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_1" class="right"><?=$row['allotAmt'] > 0 ? number_format($row['allotAmt']) : '';?></div></td>
				<td class="center bottom last"></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">채용인원</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_2" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['month'][2][$i]['amt'];?>"><?=$row['month'][2][$i]['cnt'] > 0 ? number_format($row['month'][2][$i]['cnt']) : '';?></div></td><?
				}?>
				<!--
					<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_2" class="right"><?=$row['dedcutAmt'] > 0 ? number_format($row['dedcutAmt']) : '';?></div></td>
				-->
				<td class="center"><div id="ID_CELL_CNT_<?=$orgNo;?>_2" class="right"><?=$row['employCnt'] > 0 ? number_format($row['employCnt']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_AMT_<?=$orgNo;?>_2" class="right"><?=$row['dedcutAmt'] > 0 ? number_format($row['dedcutAmt']) : '';?></div></td>
				<td class="center last"></td>
			</tr><?

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="19">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($data);

	include_once('../inc/_db_close.php');
?>
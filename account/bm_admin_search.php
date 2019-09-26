<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	DISTINCT
					m00_mcode AS org_no
			,		m00_store_nm AS org_nm
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
			'name'	=>str_replace('돌보인','',str_replace('돌보인요양보호사','',str_replace('돌보인 방문요양센터 ','',$row['org_nm'])))
		,	'month'	=>Array(
				'mg'=>Array(
					1	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	2	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	3	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	4	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	5	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	6	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	7	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	8	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	9	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	10	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	11	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	12	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0))
			,	'mm'=>Array(
					1	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	2	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	3	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	4	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	5	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	6	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	7	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	8	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	9	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	10	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	11	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0)
				,	12	=>Array('cnt'=>0, 'amt'=>0, 'ins'=>0, 'retire'=>0))
			)
		,	'mgAmt' =>0, 'mmAmt' =>0
		,	'mgIns' =>0, 'mmIns' =>0
		,	'inAmt' =>0, 'outAmt' =>0
		,	'mgRetire' =>0, 'mmRetire' =>0
		,	'totRetire' =>0
		,	'mgSum' =>0, 'mmSum' =>0, 'totSum' =>0
		);

		/*
			//수입내역
			$sl1.= ($sl1 ? ' UNION ALL ' : '');
			$sl1.= 'SELECT	org_no
					,		SUM(amt) AS amt
					FROM	ie_bm_other_in
					WHERE	org_no		= \''.$row['org_no'].'\'
					AND		LEFT(yymm,4)= \''.$year.'\'
					GROUP	BY org_no';
		*/

		/*
			//지출내역
			$sl2.= ($sl2 ? ' UNION ALL ' : '');
			$sl2.= 'SELECT	org_no
					,		SUM(amt) AS amt
					FROM	ie_bm_charge
					WHERE	org_no		= \''.$row['org_no'].'\'
					AND		LEFT(yymm,4)= \''.$year.'\'
					GROUP	BY org_no';
		*/

		//임금내역
		$sl3.= ($sl3 ? ' UNION ALL ' : '');
		$sl3.= 'SELECT	org_no
				,		CAST(MID(yymm,5) AS unsigned) AS month
				,		CASE WHEN job = \'01\' THEN \'mg\' ELSE \'mm\' END AS job
				,		COUNT(job) AS cnt
				,		SUM(salary) AS salary
				,		SUM(insu_amt) AS insu
				,		SUM(retire_amt) AS retire
				FROM	ie_bm_salary
				WHERE	org_no		= \''.$row['org_no'].'\'
				AND		LEFT(yymm,4)= \''.$year.'\'
				GROUP	BY org_no, yymm, job';
	}

	$conn->row_free();

	if (is_array($data)){
		/*
			//수입내역
			$sql = 'SELECT	org_no, amt
					FROM	('.$sl1.') AS a';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$data[$row['org_no']]['inAmt'] = $row['amt'];
			}

			$conn->row_free();
		*/

		/*
			//지출내역
			$sql = 'SELECT	org_no, amt
					FROM	('.$sl2.') AS a';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$data[$row['org_no']]['outAmt'] = $row['amt'];
			}

			$conn->row_free();
		*/

		//임금내역
		$sql = 'SELECT	org_no, month, job, cnt, salary, insu, retire
				FROM	('.$sl3.') AS a';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$data[$row['org_no']]['month'][$row['job']][$row['month']]['cnt'] += $row['cnt'];
			$data[$row['org_no']]['month'][$row['job']][$row['month']]['amt'] += $row['salary'];
			$data[$row['org_no']]['month'][$row['job']][$row['month']]['ins'] += $row['insu'];
			$data[$row['org_no']]['month'][$row['job']][$row['month']]['retire'] += $row['retire'];

			$data[$row['org_no']][$row['job'].'Amt'] += $row['salary'];
			$data[$row['org_no']][$row['job'].'Ins'] += $row['insu'];
			$data[$row['org_no']][$row['job'].'Retire'] += $row['retire'];
			$data[$row['org_no']]['totRetire'] += $row['retire'];

			$data[$row['org_no']][$row['job'].'Sum'] += ($row['salary'] + $row['insu'] + $row['retire']);
			$data[$row['org_no']]['totSum'] += ($row['salary'] + $row['insu'] + $row['retire']);
		}

		$conn->row_free();

		/*
			//재가요양 수입내역
			$sql = 'SELECT	a.org_no
					,		a.yymm
					,		a.longterm_amt
					,		a.expense_amt
					,		a.wek1_plan + a.wek2_plan + a.wek3_plan + a.wek4_plan AS plan_amt
					,		a.wek1_conf + a.wek2_conf + a.wek3_conf + a.wek4_conf AS conf_amt
					FROM	ie_bm_close_amt AS a
					INNER	JOIN (
							SELECT	DISTINCT m00_mcode AS org_no
							FROM	m00center
							WHERE	m00_domain = \''.$gDomain.'\'
							) AS b
							ON b.org_no = a.org_no
					WHERE	LEFT(yymm,4) = \''.$year.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['longterm_amt'] + $row['expense_amt'] > 0){
					$data[$row['org_no']]['inAmt'] += ($row['longterm_amt'] + $row['expense_amt']);
				}else{
					$sql = 'SELECT	COUNT(*)
							FROM	salary_basic
							WHERE	org_no		= \''.$row['org_no'].'\'
							AND		salary_yymm = \''.$row['yymm'].'\'';

					$cnt = $conn->get_data($sql);

					if ($cnt > 0){
						$data[$row['org_no']]['inAmt'] += $row['conf_amt'];
					}else{
						$data[$row['org_no']]['inAmt'] += $row['plan_amt'];
					}
				}
			}

			$conn->row_free();
		*/


		$no = 1;

		foreach($data as $orgNo => $row){
			if ($no % 2 == 1){
				$color = 'FFFFFF';
			}else{
				$color = 'F6F6F6';
			}?>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center" rowspan="3"><?=$no;?></td>
				<td class="center" rowspan="3"><div class="left"><?=$row['name'];?></div></td>
				<td class="center">센터장</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_MG" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['month']['mg'][$i]['amt'];?>" ins="<?=$row['month']['mg'][$i]['ins'];?>" retire="<?=$row['month']['mg'][$i]['retire'];?>"><?=$row['month']['mg'][$i]['cnt'] > 0 ? number_format($row['month']['mg'][$i]['cnt']) : '';?></div></td><?
				}?>
				<td class="center"><div id="ID_CELL_SALARY_<?=$orgNo;?>_MG" class="right"><?=$row['mgAmt'] > 0 ? number_format($row['mgAmt']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_4INSU_<?=$orgNo;?>_MG" class="right"><?=$row['mgIns'] > 0 ? number_format($row['mgIns']) : '';?></div></td>
				<!--
					<td class="center bottom"><div class="right"><?=$row['inAmt'] > 0 ? number_format($row['inAmt']) : '';?></div></td>
					<td class="center bottom last"><div class="right"><?=$row['outAmt'] > 0 ? number_format($row['outAmt']) : '';?></div></td>
				-->
				<td class="center"><div id="ID_CELL_RETIRE_<?=$orgNo;?>_MG" class="right"><?=$row['mgRetire'] > 0 ? number_format($row['mgRetire']) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>_MG" class="right"><?=$row['mgSum'] > 0 ? number_format($row['mgSum']) : '';?></div></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">정직원</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_MM" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['month']['mm'][$i]['amt'];?>" ins="<?=$row['month']['mm'][$i]['ins'];?>" retire="<?=$row['month']['mm'][$i]['retire'];?>"><?=$row['month']['mm'][$i]['cnt'] > 0 ? number_format($row['month']['mm'][$i]['cnt']) : '';?></div></td><?
				}?>
				<td class="center"><div id="ID_CELL_SALARY_<?=$orgNo;?>_MM" class="right"><?=$row['mmAmt'] > 0 ? number_format($row['mmAmt']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_4INSU_<?=$orgNo;?>_MM" class="right"><?=$row['mmIns'] > 0 ? number_format($row['mmIns']) : '';?></div></td>
				<!--
					<td class="center bottom"><?=$row['inAmt'] > 0 ? '[<a href="#" onclick="lfInDetail(\''.$orgNo.'\'); return false;">상세보기</a>]' : '';?></td>
					<td class="center bottom last"><?=$row['outAmt'] > 0 ? '[<a href="#" onclick="lfOutDetail(\''.$orgNo.'\'); return false;">상세보기</a>]' : '';?></td>
				-->
				<td class="center"><div id="ID_CELL_RETIRE_<?=$orgNo;?>_MM" class="right"><?=$row['mmRetire'] > 0 ? number_format($row['mmRetire']) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>_MM" class="right"><?=$row['mmSum'] > 0 ? number_format($row['mmSum']) : '';?></div></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">소&nbsp;&nbsp;&nbsp;계</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_SUM_<?=$orgNo;?>_<?=$i;?>" class="right"><?=$row['month']['mg'][$i]['cnt']+$row['month']['mm'][$i]['cnt'] > 0 ? number_format($row['month']['mg'][$i]['cnt']+$row['month']['mm'][$i]['cnt']) : '';?></div></td><?
				}?>
				<td class="center"><div id="ID_CELL_SALARY_<?=$orgNo;?>" class="right"><?=$row['mgAmt']+$row['mmAmt'] > 0 ? number_format($row['mgAmt']+$row['mmAmt']) : '';?></div></td>
				<td class="center"><div id="ID_CELL_4INSU_<?=$orgNo;?>" class="right"><?=$row['mgIns']+$row['mmIns'] > 0 ? number_format($row['mgIns']+$row['mmIns']) : '';?></div></td>
				<!--
					<td class="center"></td>
					<td class="center last"></td>
				-->
				<td class="center"><div id="ID_CELL_RETIRE_<?=$orgNo;?>" class="right"><?=$row['totRetire'] > 0 ? number_format($row['totRetire']) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>" class="right"><?=$row['totSum'] > 0 ? number_format($row['totSum']) : '';?></div></td>
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
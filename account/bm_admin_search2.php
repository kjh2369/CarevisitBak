<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	a.org_no
			,		a.org_nm
			,		CAST(MID(b.yymm,5) AS unsigned) AS month
			,		SUM(CASE WHEN b.job = \'01\' THEN 1 ELSE 0 END) AS mg_cnt
			,		SUM(CASE WHEN b.job = \'01\' THEN salary ELSE 0 END) AS mg_salary
			,		SUM(CASE WHEN b.job = \'01\' THEN insu_amt ELSE 0 END) AS mg_insu_amt
			,		SUM(CASE WHEN b.job = \'01\' THEN retire_amt ELSE 0 END) AS mg_retire_amt
			,		SUM(CASE WHEN b.job != \'01\' THEN 1 ELSE 0 END) AS mm_cnt
			,		SUM(CASE WHEN b.job != \'01\' THEN salary ELSE 0 END) AS mm_salary
			,		SUM(CASE WHEN b.job != \'01\' THEN insu_amt ELSE 0 END) AS mm_insu_amt
			,		SUM(CASE WHEN b.job != \'01\' THEN retire_amt ELSE 0 END) AS mm_retire_amt
			FROM	(
					SELECT	DISTINCT
							m00_mcode AS org_no
					,		m00_store_nm AS org_nm
					FROM	m00center AS a
					INNER	JOIN	b02center
							ON		b02_center = m00_mcode
					INNER	JOIN	cv_reg_info AS b
							ON		b.org_no = m00_mcode
							AND		LEFT(b.from_dt, 4) <= \''.$year.'\'
							AND		LEFT(b.to_dt, 4) >= \''.$year.'\'
					WHERE	m00_domain = \''.$gDomain.'\'
					) AS a
			LEFT	JOIN	ie_bm_salary AS b
					ON		b.org_no = a.org_no
					AND		LEFT(b.yymm,4) = \''.$year.'\'
			GROUP	BY a.org_no, b.yymm
			ORDER	BY org_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$orgNo = $row['org_no'];

		if (!$data[$orgNo]['name']){
			$data[$orgNo]['name'] = str_replace('돌보인','',str_replace('돌보인요양보호사','',str_replace('돌보인 방문요양센터 ','',$row['org_nm'])));

			$sql = 'SELECT	MAX(yymm)
					FROM	ie_bm_salary
					WHERE	org_no	= \''.$orgNo.'\'';

			$data[$orgNo]['yymm'] = $conn->get_data($sql);
		}

		$data[$orgNo]['MG'][$row['month']]['CNT'] = $row['mg_cnt'];
		$data[$orgNo]['MG'][$row['month']]['SALARY'] = $row['mg_salary'];
		$data[$orgNo]['MG'][$row['month']]['INSU'] = $row['mg_insu_amt'];
		$data[$orgNo]['MG'][$row['month']]['RETIRE'] = $row['mg_retire_amt'];
		$data[$orgNo]['MG']['SUM'] += ($row['mg_salary'] + $row['mg_insu_amt'] + $row['mg_retire_amt']);

		$data[$orgNo]['MM'][$row['month']]['CNT'] = $row['mm_cnt'];
		$data[$orgNo]['MM'][$row['month']]['SALARY'] = $row['mm_salary'];
		$data[$orgNo]['MM'][$row['month']]['INSU'] = $row['mm_insu_amt'];
		$data[$orgNo]['MM'][$row['month']]['RETIRE'] = $row['mm_retire_amt'];
		$data[$orgNo]['MM']['SUM'] += ($row['mm_salary'] + $row['mm_insu_amt'] + $row['mm_retire_amt']);
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;

		foreach($data as $orgNo => $row){
			$sql = 'SELECT	CASE WHEN job = \'01\' THEN \'MG\' ELSE \'MM\' END AS job
					,		salary
					,		insu_amt
					,		retire_amt
					FROM	ie_bm_salary
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$row['yymm'].'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$R = $conn->select_row($i);
				$row['AMT'][$R['job']]['SALARY'] += $R['salary'];
				$row['AMT'][$R['job']]['INSU'] += $R['insu_amt'];
				$row['AMT'][$R['job']]['RETIRE'] += $R['retire_amt'];
				$row['AMT'][$R['job']]['SUM'] += ($R['salary'] + $R['insu_amt'] + $R['retire_amt']);
			}

			$conn->row_free();


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
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_MG" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['MG'][$i]['SALARY'];?>" ins="<?=$row['MG'][$i]['INSU'];?>" retire="<?=$row['MG'][$i]['RETIRE'];?>"><?=$row['MG'][$i]['CNT'] > 0 ? $row['MG'][$i]['CNT'] : '';?></div></td><?
				}?>
				<td class="center"><div class="right"><?=$row['AMT']['MG']['SALARY'] > 0 ? number_format($row['AMT']['MG']['SALARY']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MG']['INSU'] > 0 ? number_format($row['AMT']['MG']['INSU']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MG']['RETIRE'] > 0 ? number_format($row['AMT']['MG']['RETIRE']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MG']['SUM'] > 0 ? number_format($row['AMT']['MG']['SUM']) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>_MG" class="right"><?=$row['MG']['SUM'] > 0 ? number_format($row['MG']['SUM']) : '';?></div></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">정직원</td><?
				for($i=1; $i<=12; $i++){?>
					<td class="center"><div id="ID_CELL_VAL_<?=$orgNo;?>_<?=$i;?>_MM" class="right" orgNo="<?=$orgNo;?>" month="<?=$i;?>" amt="<?=$row['MM'][$i]['SALARY'];?>" ins="<?=$row['MM'][$i]['INSU'];?>" retire="<?=$row['MM'][$i]['RETIRE'];?>"><?=$row['MM'][$i]['CNT'] > 0 ? $row['MM'][$i]['CNT'] : '';?></div></td><?
				}?>
				<td class="center"><div class="right"><?=$row['AMT']['MM']['SALARY'] > 0 ? number_format($row['AMT']['MM']['SALARY']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MM']['INSU'] > 0 ? number_format($row['AMT']['MM']['INSU']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MM']['RETIRE'] > 0 ? number_format($row['AMT']['MM']['RETIRE']) : '';?></div></td>
				<td class="center"><div class="right"><?=$row['AMT']['MM']['SUM'] > 0 ? number_format($row['AMT']['MM']['SUM']) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>_MM" class="right"><?=$row['MM']['SUM'] > 0 ? number_format($row['MM']['SUM']) : '';?></div></td>
			</tr>
			<tr style="background-color:#<?=$color;?>;">
				<td class="center">소&nbsp;&nbsp;&nbsp;계</td><?
				for($i=1; $i<=12; $i++){
					$cnt = $row['MG'][$i]['CNT'] + $row['MM'][$i]['CNT'];?>
					<td class="center"><div id="ID_CELL_SUM_<?=$orgNo;?>_<?=$i;?>" class="right"><?=$cnt > 0 ? $cnt : '';?></div></td><?
				}

				$salary = $row['AMT']['MG']['SALARY'] + $row['AMT']['MM']['SALARY'];
				$insu = $row['AMT']['MG']['INSU'] + $row['AMT']['MM']['INSU'];
				$retire = $row['AMT']['MG']['RETIRE'] + $row['AMT']['MM']['RETIRE'];
				$sum = $row['AMT']['MG']['SUM'] + $row['AMT']['MM']['SUM'];
				$total = $row['MG']['SUM'] + $row['MM']['SUM'];?>
				<td class="center"><div class="right"><?=$salary > 0 ? number_format($salary) : '';?></div></td>
				<td class="center"><div class="right"><?=$insu > 0 ? number_format($insu) : '';?></div></td>
				<td class="center"><div class="right"><?=$retire > 0 ? number_format($retire) : '';?></div></td>
				<td class="center"><div class="right"><?=$sum > 0 ? number_format($sum) : '';?></div></td>
				<td class="center last"><div id="ID_CELL_SUM_<?=$orgNo;?>" class="right"><?=$total > 0 ? number_format($total) : '';?></div></td>
			</tr><?

			$no ++;


			//전체합계
			for($i=1; $i<=12; $i++){
				$tot['MG'][$i] += $row['MG'][$i]['CNT'];
				$tot['MM'][$i] += $row['MM'][$i]['CNT'];
				$tot['MT'][$i] += ($row['MG'][$i]['CNT'] + $row['MM'][$i]['CNT']);
			}

			$tot['MG']['SALARY']+= $row['AMT']['MG']['SALARY'];
			$tot['MG']['INSU']	+= $row['AMT']['MG']['INSU'];
			$tot['MG']['RETIRE']+= $row['AMT']['MG']['RETIRE'];
			$tot['MG']['SUM']	+= $row['AMT']['MG']['SUM'];
			$tot['MG']['TOTAL'] += $row['MG']['SUM'];

			$tot['MM']['SALARY']+= $row['AMT']['MM']['SALARY'];
			$tot['MM']['INSU']	+= $row['AMT']['MM']['INSU'];
			$tot['MM']['RETIRE']+= $row['AMT']['MM']['RETIRE'];
			$tot['MM']['SUM']	+= $row['AMT']['MM']['SUM'];
			$tot['MM']['TOTAL'] += $row['MM']['SUM'];

			$tot['MT']['SALARY']+= ($row['AMT']['MG']['SALARY'] + $row['AMT']['MM']['SALARY']);
			$tot['MT']['INSU']	+= ($row['AMT']['MG']['INSU'] + $row['AMT']['MM']['INSU']);
			$tot['MT']['RETIRE']+= ($row['AMT']['MG']['RETIRE'] + $row['AMT']['MM']['RETIRE']);
			$tot['MT']['SUM']	+= ($row['AMT']['MG']['SUM'] + $row['AMT']['MM']['SUM']);
			$tot['MT']['TOTAL'] += ($row['MG']['SUM'] + $row['MM']['SUM']);
		}?>

		<!--CUT_LINE-->
		<tr>
			<td class="center sum" rowspan="3" colspan="2">합계</td>
			<td class="center sum">센터장</td><?
			for($i=1; $i<=12; $i++){?>
				<td class="center sum"><div class="right"><?=$tot['MG'][$i] > 0 ? $tot['MG'][$i] : '';?></div></td><?
			}?>
			<td class="center sum"><div class="right"><?=$tot['MG']['SALARY'] > 0 ? number_format($tot['MG']['SALARY']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MG']['INSU'] > 0 ? number_format($tot['MG']['INSU']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MG']['RETIRE'] > 0 ? number_format($tot['MG']['RETIRE']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MG']['SUM'] > 0 ? number_format($tot['MG']['SUM']) : '';?></div></td>
			<td class="center last sum"><div class="right"><?=$tot['MG']['TOTAL'] > 0 ? number_format($tot['MG']['TOTAL']) : '';?></div></td>
		</tr>
		<tr>
			<td class="center sum">정직원</td><?
			for($i=1; $i<=12; $i++){?>
				<td class="center sum"><div class="right"><?=$tot['MM'][$i] > 0 ? $tot['MM'][$i] : '';?></div></td><?
			}?>
			<td class="center sum"><div class="right"><?=$tot['MM']['SALARY'] > 0 ? number_format($tot['MM']['SALARY']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MM']['INSU'] > 0 ? number_format($tot['MM']['INSU']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MM']['RETIRE'] > 0 ? number_format($tot['MM']['RETIRE']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MM']['SUM'] > 0 ? number_format($tot['MM']['SUM']) : '';?></div></td>
			<td class="center last sum"><div class="right"><?=$tot['MM']['TOTAL'] > 0 ? number_format($tot['MM']['TOTAL']) : '';?></div></td>
		</tr>
		<tr>
			<td class="center sum">소&nbsp;&nbsp;&nbsp;계</td><?
			for($i=1; $i<=12; $i++){?>
				<td class="center sum"><div class="right"><?=$tot['MT'][$i] > 0 ? $tot['MT'][$i] : '';?></div></td><?
			}?>
			<td class="center sum"><div class="right"><?=$tot['MT']['SALARY'] > 0 ? number_format($tot['MT']['SALARY']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MT']['INSU'] > 0 ? number_format($tot['MT']['INSU']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MT']['RETIRE'] > 0 ? number_format($tot['MT']['RETIRE']) : '';?></div></td>
			<td class="center sum"><div class="right"><?=$tot['MT']['SUM'] > 0 ? number_format($tot['MT']['SUM']) : '';?></div></td>
			<td class="center last sum"><div class="right"><?=$tot['MT']['TOTAL'] > 0 ? number_format($tot['MT']['TOTAL']) : '';?></div></td>
		</tr><?

		Unset($data);
		Unset($tot);
	}else{?>
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
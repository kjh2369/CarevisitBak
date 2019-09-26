<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];

	for($i=1; $i<=12; $i++){
		$data[$i] = Array();
	}

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		gbn
			,		subject
			,		amt
			FROM	ie_bm_other_in
			WHERE	org_no		= \''.$orgNo.'\'
			AND		LEFT(yymm,4)= \''.$year.'\'
			ORDER	BY yymm, gbn, subject';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data[$row['month']]['list'][$row['gbn']]['list'][] = Array('subject'=>$row['subject'],'amt'=>$row['amt']);
		$data[$row['month']]['list'][$row['gbn']]['cnt'] ++;
		$data[$row['month']]['cnt'] ++;
	}

	$conn->row_free();


	$sql = 'SELECT	yymm
			,		longterm_amt
			,		expense_amt
			,		wek1_plan + wek2_plan + wek3_plan + wek4_plan AS plan_amt
			,		wek1_conf + wek2_conf + wek3_conf + wek4_conf AS conf_amt
			FROM	ie_bm_close_amt
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(yymm,4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$gbn = '재가요양';
		$month = IntVal(SubStr($row['yymm'],4));

		if ($row['longterm_amt'] + $row['expense_amt'] > 0){
			$data[$month]['list'][$gbn]['list'][] = Array('subject'=>'방문요양','amt'=>$row['longterm_amt'] + $row['expense_amt']);
		}else{
			$sql = 'SELECT	COUNT(*)
					FROM	salary_basic
					WHERE	org_no		= \''.$row['org_no'].'\'
					AND		salary_yymm = \''.$row['yymm'].'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				if ($row['conf_amt'] == 0) continue;
				$data[$month]['list'][$gbn]['list'][] = Array('subject'=>'방문요양(실적)','amt'=>$row['conf_amt']);
			}else{
				if ($row['plan_amt'] == 0) continue;
				$data[$month]['list'][$gbn]['list'][] = Array('subject'=>'방문요양(계획)','amt'=>$row['plan_amt']);
			}
		}

		$data[$month]['list'][$gbn]['cnt'] ++;
		$data[$month]['cnt'] ++;
	}

	$conn->row_free();


	if (is_array($data)){
		$amt[0] = 0; //구분별 소계
		$amt[1] = 0; //월별 소계
		$amt[2] = 0; //합계

		foreach($data as $month => $mRow){
			if (!is_array($mRow['list'])) continue;

			if ($mRow['list']['1']['cnt'] > 0) $mRow['cnt'] ++;
			if ($mRow['list']['2']['cnt'] > 0) $mRow['cnt'] ++;
			if ($mRow['list']['재가요양']['cnt'] > 0) $mRow['cnt'] ++;?>
			<tr>
			<td class="center" rowspan="<?=$mRow['cnt']+1;?>"><?=$month;?>월</td><?

			$IsFirst[0] = true;

			$amt[0] = 0; //구분별 소계
			$amt[1] = 0; //월별 소계

			foreach($mRow['list'] as $gbn => $gRow){
				if (!$IsFirst[0]){?>
					<tr><?
				}

				if ($gbn == '1'){
					$str = '기타수입';
				}else if ($gbn == '2'){
					$str = '매출미수';
				}else{
					$str = $gbn;
				}?>
				<td class="center" rowspan="<?=$gRow['cnt']+1;?>"><?=$str;?></td><?

				$IsFirst[0] = false;
				$IsFirst[1] = true;

				$amt[0] = 0; //구분별 소계

				foreach($gRow['list'] as $idx => $row){
					if (!$IsFirst[1]){?>
						<tr><?
					}?>
					<td><div class="left"><?=$row['subject'];?></div></td>
					<td><div class="right"><?=number_format($row['amt']);?></div></td>
					<td></td>
					</tr><?

					$amt[0] += $row['amt'];
					$amt[1] += $row['amt'];
					$amt[2] += $row['amt'];
				}?>
				<tr>
					<td class="sum"><div class="right"><?=$str;?> 소계</div></td>
					<td class="sum"><div class="right"><?=number_format($amt[0]);?></div></td>
					<td class="sum"></td>
				</tr><?
			}?>
			<tr>
				<td class="sum" colspan="2"><div class="right"><?=$month;?>월 소계</div></td>
				<td class="sum"><div class="right"><?=number_format($amt[1]);?></div></td>
				<td class="sum"></td>
			</tr><?
		}?>
		<tr>
			<td class="sum" colspan="3"><div class="right">합계</div></td>
			<td class="sum"><div class="right"><?=number_format($amt[2]);?></div></td>
			<td class="sum"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center" colspan="5">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($amt);

	include_once('../inc/_db_close.php');
?>
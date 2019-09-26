<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];

	$sql = 'SELECT	CAST(MID(a.yymm,5) AS unsigned) AS month
			,		a.acct_cd
			,		b.name AS acct_nm
			,		a.amt
			FROM	(
					SELECT	org_no
					,		yymm
					,		acct_cd
					,		SUM(amt) AS amt
					FROM	ie_bm_charge
					WHERE	org_no		= \''.$orgNo.'\'
					AND		LEFT(yymm,4)= \''.$year.'\'
					GROUP	BY yymm, acct_cd
					) AS a
			INNER	JOIN	ie_bm_acct_cd AS b
					ON		b.cd = a.acct_cd
			ORDER	BY month, acct_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data[$row['month']]['list'][] = Array('cd'=>$row['acct_cd'],'nm'=>$row['acct_nm'],'amt'=>$row['amt']);
		$data[$row['month']]['cnt'] ++;
	}

	$conn->row_free();

	if (is_array($data)){
		$amt[0] = 0;
		$amt[1] = 0;

		foreach($data as $month => $mRow){?>
			<tr>
			<td class="center" rowspan="<?=$mRow['cnt']+1;?>"><?=$month;?>월</td><?

			$IsFirst = true;
			$amt[0] = 0;

			foreach($mRow['list'] as $idx => $row){
				if (!$IsFirst){?>
					<tr><?
				}?>
				<td><div class="left"><?=$row['cd'];?> - <?=$row['nm'];?></div></td>
				<td><div class="right"><?=number_format($row['amt']);?></div></td>
				<td></td>
				</tr><?

				$amt[0] +=$row['amt'];
				$amt[1] +=$row['amt'];
			}?>
			<tr>
				<td class="sum"><div class="right">월별 소계</div></td>
				<td class="sum"><div class="right"><?=number_format($amt[0]);?></div></td>
				<td class="sum"></td>
			</tr><?
		}?>
		<tr>
			<td class="sum" colspan="2"><div class="right">합계</div></td>
			<td class="sum"><div class="right"><?=number_format($amt[1]);?></div></td>
			<td class="sum"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center" colspan="4">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($amt);

	include_once('../inc/_db_close.php');
?>
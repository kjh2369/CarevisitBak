<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELEcT	seq
			,		gbn
			,		subject
			,		amt
			FROM	ie_bm_other_in
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'
			ORDER	BY seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['gbn'] == '1'){
			$row['gbn'] = '기타매출';
		}else if ($row['gbn'] == '2'){
			$row['gbn'] = '치매수당매출';
		}else if ($row['gbn'] == '3'){
			$row['gbn'] = '사복가산금매출';
		}else if ($row['gbn'] == '4'){
			$row['gbn'] = '장기근속수당';
		}else if ($row['gbn'] == '5'){
			$row['gbn'] = '치매관리자';
		}else if ($row['gbn'] == '6'){
			$row['gbn'] = '장기근속관리자';
		}else if ($row['gbn'] == 'X'){
			$row['gbn'] = '매출미수';
		}?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['gbn'];?></div></td>
			<td class="center"><div class="left"><?=$row['subject'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['amt']);?></div></td>
			<td class="center">
				<span class="btn_pack small" style="float:left; margin-left:5px;"><button onclick="lfRemove('<?=$row['seq'];?>');" style="color:RED;">삭제</button></span>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
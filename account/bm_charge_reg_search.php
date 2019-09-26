<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	a.seq
			,		a.acct_cd
			,		b.name AS acct_nm
			,		a.amt
			FROM	ie_bm_charge AS a
			LEFT	JOIN	ie_bm_acct_cd AS b
					ON		b.cd = a.acct_cd
			WHERE	a.org_no= \''.$orgNo.'\'
			AND		a.yymm	= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['acct_cd'];?> - <?=$row['acct_nm'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['amt']);?></div></td>
			<td class="center">
				<div class="left">
					<span class="btn_pack small"><button onclick="lfRemove('<?=$row['seq'];?>');" style="color:RED;">삭제</button></span>
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
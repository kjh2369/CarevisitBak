<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	seq
			,		reg_dt
			,		memo
			FROM	iljung_memo
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$year.$month.'\'';

	if ($seq){
		$sql .= ' AND seq = \''.$seq.'\'';
	}

	$sql .= '
			AND		del_flag= \'N\'
			ORDER	BY	reg_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr id="rowId_<?=$row['seq'];?>">
			<th>작성일자</th>
			<td>
				<div style="float:left; width:auto;"><input id="txtDate_<?=$row['seq'];?>" type="text" value="<?=$row['reg_dt'];?>" class="date clsData"></div>
				<div style="float:left; width:auto;">
					<span class="btn_pack m"><button onclick="lfModify('<?=$row['seq'];?>');">수정</button></span>
					<span class="btn_pack m"><button onclick="lfDelete('<?=$row['seq'];?>');">삭제</button></span>
				</div>
			</td>
		</tr>
		<tr id="rowId_<?=$row['seq'];?>">
			<th>작성내용</th>
			<td><textarea id="txtMemo_<?=$row['seq'];?>" name="txts" class="clsData" style="width:100%; height:50px;"><?=StripSlashes($row['memo']);?></textarea></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
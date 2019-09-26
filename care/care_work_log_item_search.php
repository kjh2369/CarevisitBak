<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];

	$sql = 'SELECT	seq
			,		name
			,		order_no
			,		from_dt
			,		to_dt
			,		prt_yn
			FROM	care_work_log_item
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd = \''.$sugaCd.'\'
			AND		del_flag= \'N\'
			ORDER	BY order_no, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr id="rowId_<?=$row['seq'];?>" seq="<?=$row['seq'];?>" save="Y">
			<td class="center"><?=$no;?></td>
			<td><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><?=$myF->dateStyle($row['from_dt'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['to_dt'],'.');?></td>
			<td class="center"><?=$row['prt_yn'];?></td>
			<td class="center"><?=$row['order_no'];?></td>
			<td class="last">
				<div class="left">
					<span class="btn_pack small"><button onclick="lfModify(this);" style="color:BLUE;">수정</button></span>
					<span class="btn_pack small"><button onclick="lfDelete(this);" style="color:RED;">삭제</button></span>
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
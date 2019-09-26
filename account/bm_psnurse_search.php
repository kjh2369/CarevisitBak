<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_POST['orgNo'];

	$sql = 'SELECT	seq
			,		from_dt
			,		to_dt
			,		psn_cnt
			FROM	ie_bm_psnurse
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY from_dt DESC, to_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$myF->_styleYYMM($row['from_dt'],'.');?> ~ <?=$myF->_styleYYMM($row['to_dt'],'.');?></td>
			<td class="center"><?=$row['psn_cnt'];?></td>
			<td class="center last">
				<div class="left"><span class="btn_pack small"><button onclick="lfRemove('<?=$row['seq'];?>');">삭제</button></span></div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
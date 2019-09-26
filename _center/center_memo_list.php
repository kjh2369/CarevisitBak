<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$memoType='1';
	$orgNo	= $_POST['orgNo'];

	$sql = 'SELECT	*
			FROM	cv_memo
			WHERE	memo_type=\''.$memoType.'\'
			AND		org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			ORDER	BY insert_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgclr = 'FFFFFF';
		}else{
			$bgclr = 'F6F6F6';
		}?>
		<tr style="background-color:#<?=$bgclr;?>;">
			<td class="center" rowspan="2"><?=$rowCnt - $no;?></td>
			<td class="center"><?=str_replace('-','.',$row['insert_dt']);?></td>
			<td class="center"><?=str_replace('-','.',$row['update_dt']);?></td>
			<td class="center"><?=$row['mod_cnt'];?></td>
			<td class="">&nbsp;<?=$row['subject'];?></td>
			<td class="">&nbsp;<?=$row['reg_nm'];?></td>
			<td class="last">&nbsp;
				<span class="btn_pack small"><button onclick="lfMemo('MemoReg',{'seq':'<?=$row['seq'];?>'});" style="color:BLUE;">수정</button></span>
				<span class="btn_pack small"><button onclick="lfMemo('MemoDel',{'seq':'<?=$row['seq'];?>'});" style="color:RED;">삭제</button></span>
			</td>
		</tr>
		<tr style="background-color:#<?=$bgclr;?>;">
			<td class="last" colspan="6"><div id="ID_CELL_MEMO_<?=$i;?>" style="padding:0 5px 0 5px;"><?=nl2br(stripslashes($row['contents']));?></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
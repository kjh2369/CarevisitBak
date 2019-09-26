<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$sql = 'SELECT	ispt_seq
			,		ispt_dt
			,		ispt_rst
			FROM	hce_re_ispt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag= \'N\'
			ORDER	BY ispt_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		switch($row['ispt_rst']){
			case '1':
				$row['ispt_rst'] = '종결';
				break;
			case '2':
				$row['ispt_rst'] = '서비스 재계획';
				break;
			case '3':
				$row['ispt_rst'] = '의뢰';
				break;
			case '4':
				$row['ispt_rst'] = '현상태 유지';
				break;
		}?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=112&seq=<?=$row['ispt_seq'];?>" target="frmBody"><?=$myF->dateStyle($row['ispt_dt'],'.');?></a></td>
			<td><div class="nowrap" style="width:98%; margin-left:5px;"><?=$row['ispt_rst'];?></div></td>
			<td class="last"></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
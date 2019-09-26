<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$order = $_POST['order'];

	if (!$order) $order = 'DESC';

	$sql = 'SELECT	proc.proc_seq
			,		proc.counsel_dt
			,		CT.name AS counsel_gbn
			,		proc.counsel_text AS counsel_text
			,		proc.counsel_nm
			FROM	hce_proc_counsel AS proc
			INNER	JOIN	hce_gbn AS CT
					ON		CT.type		= \'CT\'
					AND		CT.code		= proc.counsel_gbn
					AND		CT.use_yn	= \'Y\'
			WHERE	proc.org_no		= \''.$orgNo.'\'
			AND		proc.org_type	= \''.$hce->SR.'\'
			AND		proc.IPIN		= \''.$hce->IPIN.'\'
			AND		proc.rcpt_seq	= \''.$hce->rcpt.'\'
			AND		proc.del_flag   = \'N\'
			ORDER	BY counsel_dt '.$order;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=82&procSeq=<?=$row['proc_seq'];?>" target="frmBody"><?=$myF->dateStyle($row['counsel_dt'],'.');?></a></td>
			<td class="center"><?=$row['counsel_gbn'];?></td>
			<td><div class="nowrap" style="width:395px; margin-left:5px;"><?=stripslashes($row['counsel_text']);?></div></td>
			<td class="center"><?=$row['counsel_nm'];?></td>
			<td class="last"></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
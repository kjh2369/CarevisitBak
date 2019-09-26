<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$sql = 'SELECT	mntr_seq
			,		mntr_dt
			,		CT.name AS mntr_gbn
			,		mntr_type
			,		per_nm
			,		inspector_nm
			FROM	hce_monitor
			INNER	JOIN	hce_gbn AS CT
					ON		CT.type		= \'CT\'
					AND		CT.code		= mntr_gbn
					AND		CT.use_yn	= \'Y\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND	    del_flag= \'N\'
			ORDER	BY mntr_dt DESC, mntr_seq DESC';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=102&seq=<?=$row['mntr_seq'];?>" target="frmBody"><?=$myF->dateStyle($row['mntr_dt'],'.');?></a></td>
			<td class="center"><?=$row['mntr_gbn'];?></td>
			<td class="center"><?=($row['mntr_type'] == '1' ? '초기' : '정기');?></td>
			<td class="center"><?=$row['per_nm'];?></td>
			<td class="center"><?=$row['inspector_nm'];?></td>
			<td class="center last"></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
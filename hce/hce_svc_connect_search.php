<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$sql = 'SELECT	conn.conn_seq
			,		conn.req_dt
			,		conn.reqor_nm
			,		HR.name AS reqor_rel
			,		conn.conn_orgnm
			,		LEFT(conn.req_rsn,100) AS req_rsn
			FROM	hce_svc_connect AS conn
			INNER	JOIN  hce_gbn AS HR
					ON HR.type		= \'HR\'
					AND HR.code		= conn.reqor_rel
					AND HR.use_yn	= \'Y\'
			WHERE	conn.org_no		= \''.$orgNo.'\'
			AND		conn.org_type	= \''.$hce->SR.'\'
			AND		conn.IPIN		= \''.$hce->IPIN.'\'
			AND		conn.rcpt_seq	= \''.$hce->rcpt.'\'
			AND		conn.del_flag	= \'N\'
			ORDER	BY req_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=92&connSeq=<?=$row['conn_seq'];?>" target="frmBody"><?=$myF->dateStyle($row['req_dt'],'.');?></a></td>
			<td><div class="nowrap" style="width:98%; margin-left:5px;"><?=$row['reqor_nm'];?></div></td>
			<td class="center"><?=$row['reqor_rel'];?></td>
			<td><div class="nowrap" style="width:195px; margin-left:5px;"><?=$row['conn_orgnm'];?></div></td>
			<td class="last"><div class="nowrap" style="width:310px; margin-left:5px;"><?=$row['req_rsn'];?></div></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
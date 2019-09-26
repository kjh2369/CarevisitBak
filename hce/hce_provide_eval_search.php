<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_hce.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	DISTINCT m02_yjumin AS jumin, m02_yname AS name
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$orgNo.'\'';

	$mem = $conn->_fetch_array($sql, 'jumin');

	$sql = 'SELECT	evl_seq, evl_dt, evl_cd
			FROM	hce_provide_evl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag= \'N\'
			ORDER	BY evl_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=142&evlSeq=<?=$row['evl_seq'];?>" target="frmBody"><?=$myF->dateStyle($row['evl_dt'],'.');?></a></td>
			<td class="center"><?=$mem[$row['evl_cd']]['name'];?></td>
			<td class="center last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
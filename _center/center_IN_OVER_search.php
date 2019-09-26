<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	*
			FROM	(
					SELECT	a.org_no, a.org_nm, a.cms_no, a.cms_dt, a.cms_seq AS cms_seq, a.in_dt, SUM(a.in_amt) AS in_amt, SUM(a.link_amt) AS link_amt
					FROM	(
							SELECT	a.org_no, a.org_nm, a.cms_no, a.cms_dt, a.cms_seq AS cms_seq, a.in_dt, a.in_amt, SUM(a.link_amt) AS link_amt
							FROM	(
									SELECT	DISTINCT a.org_no, m00_store_nm AS org_nm, a.cms_no, a.cms_dt, a.seq AS cms_seq, a.in_dt, a.in_amt, b.link_amt
									FROM	cv_cms_reg AS a
									INNER	JOIN	m00center
											ON		m00_mcode	= a.org_no
											AND		m00_del_yn	= \'N\'
									LEFT	JOIN	cv_cms_link AS b
											ON		b.org_no	= a.org_no
											AND		b.cms_no	= a.cms_no
											AND		b.cms_dt	= a.cms_dt
											AND		b.cms_seq	= a.seq
											AND		b.del_flag	= \'N\'
									WHERE	a.del_flag = \'N\'
									) AS a
							GROUP	BY a.org_no, a.cms_no, a.cms_dt, a.cms_seq
							) AS a
					GROUP	BY a.org_no
					) AS a
			WHERE	in_amt - link_amt > 0
			ORDER	BY org_nm';

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left nowrap" style="width:150px;"><?=$row['org_nm'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:90px;"><?=$row['org_no'];?></div></td>
			<td class="center"><?=$myF->dateStyle($row['cms_dt'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['in_dt'],'.');?></td>
			<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['link_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['in_amt'] - $row['link_amt']);?></div></td>
			<td class="center last">
				<div class="left">
					<!--span class="btn_pack small"><button onclick="lfMenu('CLAIM_ORG_DTL','&orgNo=<?=$row['org_no'];?>&year=<?=SubStr($row['cms_dt'],0,4);?>','Y');">입금적용</button></span-->
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
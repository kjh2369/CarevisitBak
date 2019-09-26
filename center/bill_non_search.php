<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

/*
SELECT a.org_no, a.acct_ym, a.yymm, b.svc_nm, a.svc_gbn, a.svc_cd, a.use_from, a.use_to, a.acct_amt
FROM   cv_svc_acct_list AS a
INNER  JOIN (
       SELECT CAST('1' AS char) AS svc_gbn, svc_cd, svc_nm, parent_cd
       FROM   cv_svc_main
       UNION  ALL
       SELECT CAST('2' AS char), svc_cd, svc_nm, parent_cd
       FROM   cv_svc_sub
       ) AS b
       ON   b.svc_gbn = a.svc_gbn
       AND  b.svc_cd = a.svc_cd
WHERE  a.org_no	= '1234'
ORDER  BY acct_ym, svc_gbn, svc_cd
 */

	$orgNo	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	a.acct_ym, a.yymm, a.use_from,a.use_to, a.acct_amt
			,		SUM(CASE WHEN IFNULL(b.in_stat,\'1\') = \'1\' THEN IFNULL(b.link_amt,0) ELSE 0 END) AS link_amt
			,		SUM(CASE WHEN IFNULL(b.in_stat,\'1\') = \'9\' THEN IFNULL(b.link_amt,0) ELSE 0 END) AS cut_amt
			FROM	(
					SELECT	org_no, acct_ym, yymm, use_from, use_to, SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					GROUP	BY org_no, acct_ym, yymm
					) AS a
			LEFT	JOIN	cv_cms_link AS b
					ON		b.org_no	= a.org_no
					AND		b.yymm		= a.yymm
					AND		b.del_flag	= \'N\'
					AND		IFNULL(b.link_stat,\'1\') = \'1\'
			GROUP	BY a.acct_ym, a.yymm
			ORDER	BY acct_ym';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$myF->_styleYYMM($row['acct_ym'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['use_from'],'.');?> ~ <?=$myF->dateStyle($row['use_to'],'.');?></td>
			<td><div class="right"><?=number_format($row['acct_amt']);?></div></td>
			<td><div class="right"><?=number_format($row['link_amt']);?></div></td>
			<td><div class="right"><?=number_format($row['cut_amt']);?></div></td>
			<td><div class="right"><?=number_format($row['acct_amt'] - ($row['link_amt'] + $row['cut_amt']));?></div></td>
			<td class="last"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
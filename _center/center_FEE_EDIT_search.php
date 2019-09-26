<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	$allYn	= $_POST['allYn'];

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$acctYm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $acctYm.'01', 'Ym');

	$sql = 'SELECT	a.org_no, GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm, m00_mname AS mg_nm, a.acct_amt, a.dc_amt
			FROM	(
					SELECT	org_no
					,		SUM(CASE WHEN svc_gbn != \'9\' AND svc_cd != \'99\' THEN acct_amt ELSE 0 END) AS acct_amt
					,		SUM(CASE WHEN svc_gbn = \'9\' AND svc_cd = \'99\' THEN acct_amt ELSE 0 END) AS dc_amt
					FROM	cv_svc_acct_list
					WHERE	yymm = \''.$yymm.'\'';

	if ($orgNo) $sql .= ' AND org_no LIKE \''.$orgNo.'%\'';

	$sql .= '		GROUP	BY org_no
					) AS a
			INNER	JOIN	m00center
					ON		m00_mcode = a.org_no
			WHERE	a.org_no != \'\'';

	if ($allYn == 'Y') $sql .= ' AND a.acct_amt > 0';

	if ($orgNm) $sql .= ' AND m00_store_nm LIKE \'%'.$orgNm.'%\'';
	if ($mgNm) $sql .= ' AND m00_mname LIKE \'%'.$mgNm.'%\'';

	$sql .= '
			GROUP	BY a.org_no
			ORDER	BY org_nm';

	//echo '<tr><td colspan="8">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['org_no'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:170px;"><a href="#" onclick="return lfRowSel(this,'<?=$row['org_no'];?>');"><?=$row['org_nm'];?></a></div></td>
			<td class="center"><div class="left"><?=$row['mg_nm'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['acct_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['dc_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['acct_amt'] + $row['dc_amt']);?></div></td>
			<td class="center"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
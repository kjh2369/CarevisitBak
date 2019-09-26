<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	$addr	= $_POST['addr'];
	$page	= $_POST['page'];


	//CMS 리스트
	$sql = 'SELECT	org_no, GROUP_CONCAT(CONCAT(cms_no, CAST(CASE cms_com WHEN \'1\' THEN \'(굿이오스)\' WHEN \'2\' THEN \'(지케어)\' WHEN \'3\' THEN \'(케어비지트)\' ELSE \'\' END AS char))) AS cms_no
			FROM	cv_cms_list
			GROUP	BY org_no';

	$cmsList = $conn->_fetch_array($sql,'org_no');


	$itemCnt = 20;

	$bsl = 'SELECT	DISTINCT
					m00_mcode AS org_no
			,		m00_store_nm AS org_nm
			,		m00_mname AS mg_nm
			,		m00_caddr1 AS addr
			,		m00_caddr2 AS addr_dtl
			FROM	m00center
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
			WHERE	m00_mcode != \'\'';

	if ($orgNo){
		$bsl .= '
			AND		m00_mcode LIKE \''.$orgNo.'%\'';
	}

	if ($orgNm){
		$bsl .= '
			AND		m00_store_nm LIKE \'%'.$orgNm.'%\'';
	}

	if ($mgNm){
		$bsl .= '
			AND		m00_mname LIKE \'%'.$mgNm.'%\'';
	}

	if ($addr){
		$bsl .= '
			AND		CONCAT(m00_caddr1,m00_caddr2) LIKE \'%'.$addr.'%\'';
	}

	#echo '<tr><td colspan="5">'.nl2br($bsl).'</td></tr>';

	$sql = 'SELECT	COUNT(*)
			FROM	('.$bsl.') AS a';
	$totCnt = $conn->get_data($sql);

	if ($totCnt < (IntVal($page) - 1) * $itemCnt){
		$page = 1;
	}

	$pageCnt = (intVal($page) - 1) * $itemCnt;

	$sql = 'SELECT	*
			FROM	('.$bsl.') AS a
			ORDER	BY org_nm
			LIMIT	'.$pageCnt.','.$itemCnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr onclick="lfRegSelOrg(this);" orgNo="<?=$row['org_no'];?>" orgNm="<?=$row['org_nm'];?>" mgNm="<?=$row['mg_nm'];?>" cmsNo="<?=$cmsList[$row['org_no']]['cms_no'];?>" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='';" style="cursor:pointer;">
			<td class="center"><?=$pageCnt + ($i + 1);?></td>
			<td><div class="left"><?=$row['org_nm'];?></div></td>
			<td><div class="left"><?=$row['org_no'];?></div></td>
			<td class="center"><?=$row['mg_nm'];?></td>
			<td class="last"><div class="left"><?=$row['addr'];?> <?=$row['addr_dtl'];?></div></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$code	= $_POST['code'];

	/*
		$year	= $_POST['year'];
		$month	= $_POST['month'];
		$month	= ($month < 10 ? '0' : '').$month;
	*/
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	

	
	$sql = 'SElECT	a.jumin
			,		m03_name AS name
			,		a.cnt
			FROM	(
					SELECT	t01_ccode AS org_no
					,		t01_jumin AS jumin
					,		COUNT(t01_jumin) AS cnt
					FROM	t01iljung
					WHERE	t01_ccode		= \''.$orgNo.'\'
					AND		t01_mkind		= \''.$SR.'\'
					AND		t01_suga_code1	= \''.$code.'\'
					AND		t01_status_gbn	= \'1\'';

	/*
		$sql .= '	AND		t01_sugup_date >= \''.$year.$month.'01\'
					AND		t01_sugup_date <= \''.$year.$month.'31\'';
	*/
	$sql .= '		AND		t01_sugup_date >= \''.$fromDt.'\'
					AND		t01_sugup_date <= \''.$toDt.'\'';

	$sql .= '		AND		t01_del_yn		= \'N\'
					GROUP	BY t01_ccode, t01_jumin
					) AS a
			INNER	JOIN	m03sugupja
					ON		m03_ccode = a.org_no
					AND		m03_mkind = \'6\'
					AND		m03_jumin = a.jumin
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['cnt']);?></div></td>
			<td class="center"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
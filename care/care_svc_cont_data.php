<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	t01_suga_code1 AS suga_cd
			,		suga.suga_nm AS suga_nm
			,		m03_key AS code
			,		m03_name AS name
			FROM	t01iljung
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no = t01_ccode
					AND		suga.suga_sr = t01_mkind
					AND		CONCAT(suga_cd,suga_sub) = t01_suga_code1
					AND		REPLACE(suga.from_dt, \'-\', \'\') <= t01_sugup_date
					AND		REPLACE(suga.to_dt, \'-\', \'\') >= t01_sugup_date
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= t01_ccode
					AND		m03_mkind	= \'6\'
					AND		m03_jumin	= t01_jumin
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			ORDER	BY suga_nm,name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($data) $data .= '?';

		$data .= 'sugaCd='.$row['suga_cd'];
		$data .= '&sugaNm='.$row['suga_nm'];
		$data .= '&code='.$row['code'];
		$data .= '&name='.$row['name'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
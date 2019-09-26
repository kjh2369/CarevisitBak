<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$type	= $_POST['type'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	if ($type == 'SERVICE'){
		$sql = 'SELECT	DISTINCT
						t01_suga_code1 AS cd
				,		suga.suga_nm AS nm
				FROM	t01iljung
				INNER	JOIN care_suga AS suga
						ON suga.org_no = t01_ccode
						AND suga.suga_sr = t01_mkind
						AND CONCAT(suga_cd,suga_sub) = t01_suga_code1
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \''.$SR.'\'
				AND		t01_del_yn	= \'N\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				ORDER	BY nm';
	}else if ($type == 'CLIENT'){
		$sql = 'SELECT	DISTINCT
						t01_jumin AS cd
				,		m03_name AS nm
				FROM	t01iljung
				INNER	JOIN m03sugupja
						ON m03_ccode	= t01_ccode
						AND m03_mkind	= \'6\'
						AND m03_jumin	= t01_jumin
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \''.$SR.'\'
				AND		t01_del_yn	= \'N\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				ORDER	BY nm';
	}else if ($type == 'DATA'){
		$sql = 'SELECT	DISTINCT
						t01_suga_code1 AS suga_cd
				,		t01_jumin AS name_cd
				FROM	t01iljung
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \''.$SR.'\'
				AND		t01_del_yn	= \'N\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				ORDER	BY suga_cd,name_cd';
	}else{
		exit;
	}

	/*
	$sql = 'SELECT	DISTINCT
					t01_suga_code1 AS cd
			,		suga.suga_nm AS nm
			,		t01_jumin AS jumin
			,		m03_key AS code
			,		m03_name AS name
			FROM	t01iljung
			INNER	JOIN care_suga AS suga
					ON suga.org_no = t01_ccode
					AND suga.suga_sr = t01_mkind
					AND CONCAT(suga_cd,suga_sub) = t01_suga_code1
			INNER	JOIN m03sugupja
					ON m03_ccode	= t01_ccode
					AND m03_mkind	= \'6\'
					AND m03_jumin	= t01_jumin
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			ORDER	BY cd,name';
	*/

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($type == 'DATA'){
			$data .= '?suga='.$row['suga_cd'];
			$data .= '&name='.$ed->en($row['name_cd']);
		}else{
			if ($type == 'SERVICE'){
				$data .= '?cd='.$row['cd'];
			}else{
				$data .= '?cd='.$ed->en($row['cd']);
			}
			$data .= '&nm='.$row['nm'];
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
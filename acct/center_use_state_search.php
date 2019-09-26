<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$svcArr	= Array(0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'S',6=>'R');
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	DISTINCT m00_mcode AS code
			,		m00_store_nm AS name
			,		m00_mname AS manager
			,		m00_ctel AS telno
			FROM	m00center
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
					AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
			INNER	JOIN	b00branch
					ON		b00_code	= b02_branch
					AND		b00_domain	= \''.$gDomain.'\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'code='	.$row['code'];
		$data .= '&name='	.$row['name'];
		$data .= '&manager='.$row['manager'];
		$data .= '&telno='	.$row['telno'];
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
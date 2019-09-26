<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];

	$sql = 'SELECT	DISTINCT	m03_jumin AS jumin
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$code.'\'
			AND		m03_del_yn	= \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$jumin .= '/'.$row['jumin'];
	}

	$conn->row_free();

	$sql = 'SELECT	jumin
			,		normal_seq
			,		name
			,		addr
			,		addr_dtl
			,		phone
			,		mobile
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		del_flag	= \'N\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (StrLen($row['jumin']) != 13 || !Is_Numeric(StrPos($jumin,'/'.$row['jumin']))){
			$data .= 'seq='.$row['normal_seq'];
			$data .= '&name='.$row['name'];
			$data .= '&jumin='.$row['jumin'];
			$data .= '&addr='.$row['addr'].' '.$row['addr_dtl'];
			$data .= '&tel='.($row['phone'] ? $row['phone'] : $row['mobile']);
			$data .= chr(11);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
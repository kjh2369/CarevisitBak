<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];

	if ($yymm == 'NOW'){
		$yymm = Date('Ym');
	}else{
		$yymm = '';
	}

	$sql = 'SELECT	yymm
			,		pay
			FROM	ltcf_stnd_monthly
			WHERE	org_no	= \''.$code.'\'
			AND		ipin	= \''.$jumin.'\'';

	if ($yymm){
		$sql .= '
			AND		yymm <= \''.$yymm.'\'';
	}

	$sql .= '
			ORDER	BY yymm DESC';

	if ($yymm){
		$sql .= '
			LIMIT	1';
	}
	

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'ym='.$row['yymm'];
		$data .= '&pay='.$row['pay'];

		if (!$yymm){
			$data .= chr(11);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
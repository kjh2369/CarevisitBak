<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$seq   = $_POST['seq'];

	$sql = 'SELECT	seq
			,		from_dt
			,		to_dt
			,		annuity_yn
			,		health_yn
			,		employ_yn
			,		sanje_yn
			,		paye_yn
			,		from_stat
			,		to_stat
			FROM	mem_insu
			WHERE	org_no = \''.$code.'\'
			AND		jumin  = \''.$jumin.'\'';

	if (!Empty($seq)){
		if (Is_Numeric($seq)){
			$sql .= '
				AND		seq = \''.$seq.'\'';
		}
	}

	$sql .= '
			ORDER	BY seq DESC';

	if ($seq == 'MAX'){
		$sql .= '
			LIMIT	1';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'seq='.$row['seq'];
		$data .= '&from='.$row['from_dt'];
		$data .= '&to='.$row['to_dt'];
		$data .= '&a='.$row['annuity_yn'];
		$data .= '&h='.$row['health_yn'];
		$data .= '&e='.$row['employ_yn'];
		$data .= '&s='.$row['sanje_yn'];
		$data .= '&p='.$row['paye_yn'];
		$data .= '&statF='.$row['from_stat'];
		$data .= '&statT='.$row['to_stat'];

		if ($seq != 'MAX'){
			$data .= chr(11);
		}
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
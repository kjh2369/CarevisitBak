<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$fromDt   = $_POST['fromDt'];

	$sql = 'SELECT	from_dt, to_dt, nps_flag, nhic_flag, ei_flag, lai_flag, income_tax_off_flag
			FROM	ltcf_insu_hist
			WHERE	org_no	 = \''.$code.'\'
			AND		ipin	 = \''.$jumin.'\'
			AND		del_flag = \'N\'';

	if ($lmtcnt) $sql .= ' AND from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')';

	$sql .= ' ORDER	BY from_dt DESC';

	if ($lmtcnt) $sql .= ' LIMIT '.$lmtcnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'seq='.$row['seq'];
		$data .= '&from='.$row['from_dt'];
		$data .= '&to='.$row['to_dt'];
		$data .= '&a='.$row['nps_flag'];
		$data .= '&h='.$row['nhic_flag'];
		$data .= '&e='.$row['ei_flag'];
		$data .= '&s='.$row['lai_flag'];
		$data .= '&p='.$row['income_tax_off_flag'];

	
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
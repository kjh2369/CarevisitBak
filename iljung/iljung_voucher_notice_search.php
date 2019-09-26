<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	svc_cd
			,		notice
			FROM	voucher_notice
			WHERE	org_no	= \''.$code.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'svcCd='.$row['svc_cd'];
		$data .= '&notice='.StripSlashes($row['notice']);
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
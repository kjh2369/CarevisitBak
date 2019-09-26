<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//$code	= $_POST['code'];
	$code	= $_SESSION['userCenterCode'];
	$jumin	= $_POST['jumin'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$svcCd	= $_POST['svcCd'];
	$type	= $_POST['type'];

	parse_str($_POST['para'],$para);

	if ($svcCd == 'S' || $svcCd == 'R') $svcCd = '6';

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($svcCd == '0')
		$lsIdx = '0';
	else if ($svcCd == '4')
		$lsIdx = '4';
	else if ($svcCd >= 'A' && $svcCd <= 'C')
		$lsIdx = '9';
	else
		$lsIdx = '1';

	if (is_file('../iljung/plan_client_info_'.$lsIdx.'.php')){
		include_once('../iljung/plan_client_info_list.php');
		include_once('../iljung/plan_client_info_'.$lsIdx.'.php');
	}else{
		include_once('../iljung/plan_client_info_error.php');
	}
	include_once('../inc/_db_close.php');
?>
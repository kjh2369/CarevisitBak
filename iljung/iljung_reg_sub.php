<?
	if (!isset($_POST)) include_once('../inc/_http_home.php');

	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code      = $_POST['code'];
	$kind      = $_POST['kind'];
	$kind_list = $conn->kind_list($code, true);
	$svc_id    = $_POST['svc_id'];
	$svc_cd    = $conn->kind_code($kind_list, $svc_id);
	$kind_nm   = $conn->kind_name($kind_list, $svc_id, 'id');
	$jumin     = $ed->de($_POST['jumin']);
	$key       = $_POST['key'];
	$year      = $_POST['year'];
	$month     = $_POST['month'];
	$day       = $_POST['day'];
	$mode      = $_POST['mode'];

	###########################################################################
	#
	# 공통으로 사용할 변수
	#
		echo '<input name=\'mode\' type=\'hidden\' value=\''.$mode.'\'>';
	#
	###########################################################################

	if ($svc_id > 10 && $svc_id < 20){
		$url = 'iljung_reg_care.php';
	}else if ($svc_id > 20 && $svc_id < 30){
		$url = 'iljung_reg_voucher.php';
	}else if ($svc_id > 30 && $svc_id < 40){
		$url = 'iljung_reg_other.php';
	}else{
		exit;
	}

	include_once($url);
	include_once('../inc/_db_close.php');
?>
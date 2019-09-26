<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once('iljung_config.php');

	########################################################
	#
	# 서비스내역
	#
	########################################################

	$type	   = $_POST['type'];
	$code	   = $_POST["code"];
	$kind	   = $_POST["kind"];
	$svc_id    = $_POST["svc_id"];
	$kind_list = $conn->kind_list($code, true);
	$kind_nm   = $conn->kind_name($kind_list, $svc_id, 'id');
	$svc_cd    = $conn->kind_code($kind_list, $svc_id);
	$jumin	   = $ed->de($_POST["jumin"]);
	$key       = $_POST["key"];
	$year	   = $_POST["year"];
	$month	   = $_POST["month"];
	$ym		   = $year.$month;

	if ($svc_id > 10 && $svc_id < 20){
		/**************************************************

			방문재가

		**************************************************/
		include_once('iljung_const_care.php');

	}else if ($svc_id == 24){
		/**************************************************

			장애인 활동지원

		**************************************************/
		include_once('iljung_const_voucher.php');

	}else{
		/**************************************************

			바우처 및 기타유료

		**************************************************/
		include_once('iljung_const_other.php');
	}

	include_once("../inc/_db_close.php");
?>
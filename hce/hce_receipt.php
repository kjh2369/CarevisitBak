<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례접수
	 *********************************************************/

	$year	= Date('Y');
	$month	= Date('m');

	if ($type == '1'){
		//리스트
		include_once('./hce_receipt_list.php');
	}else if ($type == '11' || $type == '12'){
		//등록
		include_once('./hce_receipt_reg.php');
	}

	include_once('../inc/_db_close.php');
?>
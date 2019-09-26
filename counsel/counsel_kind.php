<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$counsel_kind = $_POST['counsel_kind']; //상담기록지 구분

	include_once('client_counsel_info.php');

	if ($counsel_kind == 1){
		include_once('client_counsel_normal.php');
		include_once('client_counsel_other.php');
	}else{
		include_once('client_counsel_baby.php');
	}
?>
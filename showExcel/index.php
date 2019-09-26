<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION["userCenterCode"];

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=CAREVISIT_".date("YmdHis").".xls" );

	$filepath = '../'.$_POST['root'].'/'.$_POST['fileName'].'_'.$_POST['fileType'].'.php';

	if (is_file($filepath)){
		include($filepath);
	}

	include_once("../inc/_db_close.php");
?>
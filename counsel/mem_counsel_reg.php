<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$is_path = 'counsel';

	/********************************************

		- ������ ���

		  - 100 : �����򰡰���
		  - 110 : �����򰡰���

	********************************************/
		$parent_id = $_REQUEST['parent_id'];
	/********************************************/

	include_once('mem_counsel_reg_sub.php');

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
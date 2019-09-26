<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	print_r($_POST);

	include_once("../inc/_db_close.php");
?>
<script>
//	location.replace('branch_reg.php?menuIndex=13&menuSeq=1&code=<?=$code;?>');
</script>
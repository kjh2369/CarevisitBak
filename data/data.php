<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$type = $_GET['type'];
?>
<form id="f" name="f">
<?
	if ($type == '101'){
		include_once('./data_copy.php');
	}else{
		include_once('../inc/_http_home.php');
	}
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
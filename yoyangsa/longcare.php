<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$mode = $_GET['mode'];
?>
<script type="text/javascript">

</script>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<?
	if ($mode == '101'){
		include_once('./longcare/mem_comp.php');
	}else{
		include('../inc/_http_home.php');
		exit;
	}
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
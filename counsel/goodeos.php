<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$type = $_GET['type'];
?>
<script type="text/javascript">

</script>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<?
	if ($type == '101'){
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
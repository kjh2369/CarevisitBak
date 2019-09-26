<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$mode	= $_GET['mode'];

	if ($mode == '10'){
		$lsTitle	= '공단수급자조회';
	}else{
		exit;
	}
?>
<script type="text/javascript">

</script>

<div class="title title_border"><?=$lsTitle;?></div>

<form id="f" name="f" method="post"><?
	if ($mode == '10'){
		include_once('./ltc/ltc_client.php');
	}?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
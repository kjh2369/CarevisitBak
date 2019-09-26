<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo	= $_SESSION['userCenterCode'];
	$IPIN	= $_POST['IPIN'];
?>
<div class="title title_border">
	<div style="float:left; width:auto;">제공계획서(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="">저장</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" class="bold" onclick="">출력</button></span>
	</div>
</div>
<?
	include_once('../inc/_db_close.php');
?>
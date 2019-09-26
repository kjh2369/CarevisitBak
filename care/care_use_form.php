<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	function lfReg(IPIN){
		var f = document.f;

		if (!IPIN) IPIN = '';

		f.IPIN.value = IPIN;
		f.action = '../care/care.php?sr=<?=$sr;?>&type=<?=$type;?>_REG';
		f.submit();
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">이용신청서(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="lfReg();">작성</button></span>
	</div>
</div>
<input name="IPIN" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>
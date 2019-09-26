<?
	include("../inc/_header.php");
	include("../inc/_ed.php");

	$isManager	= $_GET['manager'];

	if ($isManager != true){
		include("../inc/_body_header.php");
	}

	$p = $_REQUEST;

	$mYear		= $p["curYear"];
	$mMonth		= $p["curMonth"];
	$mCode		= $p["curMcode"];
	$mKind		= $p["curMkind"];
	$mSugupja	= $ed->de($p["curSugupja"]);
?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post" action="month_conf_ok.php">
<?
	if ($isManager){
	?>
		<div style="margin-left:10px; margin-right:10px; padding-bottom:10px;">
	<?
	}
?>
<table style="width:100%;">
<tr>
<td class="title" colspan="2">수급자별 청구액산정 확정처리(<?=$mYear?>.<?=$mMonth?>)</td>
</tr>
<tr>
	<td class="noborder">
	<div id="myBody" style=""></div>
	</td>
</tr>
</table>
<?
	if ($isManager){
	?>
		</div>
	<?
	}
?>
<input name="mCode" type="hidden" value="<?=$mCode;?>">
<input name="mKind" type="hidden" value="<?=$mKind;?>">
<input name="mSugupja" type="hidden" value="<?=$p["curSugupja"];?>">
</form>
<?
	if ($isManager != true){
		include("../inc/_body_footer.php");
	}
	include("../inc/_footer.php");
?>
<script language='javascript'>
	getMonthConfSugupjaList(document.getElementById('myBody'), '<?=$mYear;?>', '<?=$mMonth;?>', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($mSugupja);?>', '<?=$isManager;?>');
</script>
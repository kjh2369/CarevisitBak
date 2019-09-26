<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$mCode = $_SESSION["userCenterCode"];
	$mKind = $_POST["myKind"] != "" ? $_POST["myKind"] : $_SESSION["userCenterKind"][0];
	$mYear = date('Y', mkTime());
	$mMonth = date('m', mkTime());
?>
<script src="../js/work.js" type="text/javascript"></script>
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2">방문일정현황</td>
	</tr>
	<tr>
		<td class="noborder">
			<div id="myBody"></div>
		</td>
	</tr>
</table>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	getWorkMonthList('<?=$mCode;?>','<?=$mKind;?>',' ','<?=$mYear;?>','<?=$mMonth;?>');
</script>
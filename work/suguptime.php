<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$_PARAM = $_REQUEST;

	$mCode    = $_PARAM["mCode"]    != "" ? $_PARAM["mCode"]    : $_SESSION["userCenterCode"];
	$mKind    = $_PARAM["mKind"]    != "" ? $_PARAM["mKind"]    : $_SESSION["userCenterKind"][0];
	$mKey     = $_PARAM['mKey']     != '' ? $_PARAM['mKey']     : ' ';
	$mYear    = $_PARAM['mYear']    != '' ? $_PARAM['mYear']    : date('Y', mkTime());
	$mMonth   = $_PARAM['mMonth']   != '' ? $_PARAM['mMonth']   : date('m', mkTime());
?>
<script src="../js/work.js" type="text/javascript"></script>
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2">수급시간 확정처리</td>
	</tr>
	<tr>
		<td class="noborder">
			<div id="myBody"></div>
			<div id="sumBody"></div>
		</td>
	</tr>
</table>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	getSugupTimetList('<?=$mCode;?>','<?=$mKind;?>','<?=$mYear;?>','<?=$mMonth;?>',' ','<?=$mKey;?>');
</script>
<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$_PARAM = $_REQUEST;

	$mCode    = $_PARAM["mCode"]    != "" ? $_PARAM["mCode"]    : $_SESSION["userCenterCode"];
	$mKind    = $_PARAM["mKind"]    != "" ? $_PARAM["mKind"]    : $_SESSION["userCenterKind"][0];
	$mSvcCode = $_PARAM['mSvcCode'] != '' ? $_PARAM['mSvcCode'] : '200';
	$mKey     = $_PARAM['mKey']     != '' ? $_PARAM['mKey']     : ' ';
	$mYear    = $_PARAM['mYear']    != '' ? $_PARAM['mYear']    : date('Y', mkTime());
	$mMonth   = $_PARAM['mMonth']   != '' ? $_PARAM['mMonth']   : date('m', mkTime());
	$mRate    = $_PARAM['mRate']    != '' ? $_PARAM['mRate']    : '';
?>
<script src="../js/work.js" type="text/javascript"></script>
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2">청구액산정 확정처리</td>
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
	getSugupConfList('<?=$mCode;?>','<?=$mKind;?>','<?=$mYear;?>','<?=$mMonth;?>','<?=$mRate;?>');
</script>
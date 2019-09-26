<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$_PARAM = $_REQUEST;

	$mCode = $_PARAM["mCode"] != "" ? $_PARAM["mCode"] : $_SESSION["userCenterCode"];
	$mKind = $_PARAM["mKind"] != "" ? $_PARAM["mKind"] : $_SESSION["userCenterKind"][0];
	$mYear = $_PARAM['mYear'] != '' ? $_PARAM['mYear'] : date('Y', mkTime());

	$setYear = $conn->get_min_max_year('t13sugupja', 't13_pay_date');
	$nowYear = date('Y', mkTime());
?>
<script src="../js/claim.js" type="text/javascript"></script>
<table style="width:100%;">
<tr>
<td class="title" colspan="2">공단부담금 내역</td>
</tr>
</table>
<form name="f" method="post" action="">
<table style="width:100%;">
<tr>
<td class="noborder" style="width:64%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
	<input name="mCode" type="hidden" value="<?=$mCode;?>">
	<select name="mKind" style="width:150px;">
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mYear" tag="<?=$mYear;?>" style="width:65px;">
	<?
		for($i=$setYear[0]; $i<=$setYear[1]; $i++){
		?>
			<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
		<?
		}
	?>
	</select>
	<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();"  onClick="myDetail.innerHTML = ''; getMonthBill(myBody, document.f.mCode.value, document.f.mKind.value, document.f.mYear.value);">조회</button></span>
</td>
<td style="width:36%; height:33px; text-align:left; border:none;" id="btnBody"></td>
</tr>
</table>
<div id="myBody"></div>
<div id="myDetail"></div>
</form>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	getMonthBill(myBody, document.f.mCode.value, document.f.mKind.value, document.f.mYear.value);
</script>
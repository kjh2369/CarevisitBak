<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$_PARAM = $_REQUEST;

	$mCode    = $_PARAM["mCode"]  != "" ? $_PARAM["mCode"]    : $_SESSION["userCenterCode"];
	$mKind    = $_PARAM["mKind"]  != "" ? $_PARAM["mKind"]    : $_SESSION["userCenterKind"][0];
	$mYear    = $_PARAM['mYear']  != '' ? $_PARAM['mYear']    : date('Y', mkTime());
	$mMonth   = $_PARAM['mMonth'] != '' ? $_PARAM['mMonth']   : date('m', mkTime());
	$mRate    = $_PARAM['mRate']  != '' ? $_PARAM['mRate']    : '';

	$setYear = $conn->get_iljung_year($mCode);
?>
<script src="../js/work.js" type="text/javascript"></script>
<table style="width:100%;">
<tr>
<td class="title" colspan="2">본인부담금 내역</td>
</tr>
<tr>
<td class="noborder">
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
		<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();"  onClick="myDetail.innerHTML=''; getPaymentsBill(myBody, document.f.mCode.value, document.f.mKind.value, document.f.mYear.value);">조회</button></span>
	</td>
	<td style="width:36%; height:33px; text-align:left; border:none;" id="btnBody"></td>
	</tr>
	</table>
	<div id="myBody"></div>
	<div id="myDetail"></div>
	</form>
</td>
</tr>
</table>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<!--
<div id="buttonBody" style="left:0px; top:0px; width:0px; height:0px; position:absolute; background-color:#ffffff; border:1px solid #cccccc;" onMouseOut="printButton.style.display='none';">
	<table>
	<tr>
	<td style="width:288px; height:24px; border:none; text-align:left; font-weight:bold; padding-left:5px;" onMouseOver="printButton.style.display='';">본인부담금 수납대장(34호)</td>
	</tr>
	<tbody id="printButton" style="display:none;" onMouseOver="printButton.style.display='';">
		<tr>
		<td style="height:24px; border:none; text-align:left; font-weight:bold; padding-left:5px; padding-top:2px; line-height:1.2em;">
		<input type="button" onClick="_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, document.f.mYear.value + document.f.mMonth.value, '1');" value="" style="width:71px; height:18px; border:0px; background:url('../image/btn_storge.png') no-repeat; cursor:pointer;">
		<input type="button" onClick="_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, document.f.mYear.value + document.f.mMonth.value, '2');" value="" style="width:101px; height:18px; border:0px; background:url('../image/btn_receipt_4.png') no-repeat; cursor:pointer;">
		<input type="button" onClick="_printPaymentsAcc(document.f.mCode.value, document.f.mKind.value, document.f.mYear.value + document.f.mMonth.value, '3');" value="" style="width:101px; height:18px; border:0px; background:url('../image/btn_receipt_5.png') no-repeat; cursor:pointer;">
		<input type="button" onClick="_printPaymentIssu(document.f.mCode.value, document.f.mKind.value, document.f.mYear.value + document.f.mMonth.value)" value="" style="width:283px; height:18px; border:0px; background:url('../image/btn_receipt_6.png') no-repeat; cursor:pointer; margin-top:3px; margin-bottom:3px;">
		</td>
		</tr>
	</tbody>
	</table>
</div>
-->
<script language="javascript">
	getPaymentsBill(myBody, document.f.mCode.value, document.f.mKind.value, document.f.mYear.value,'','');
	/*
	var x = __getObjectLeft(document.getElementById('btnBody'));
	var y = __getObjectTop (document.getElementById('btnBody')) + 11;

	buttonBody.style.left = x;
	buttonBody.style.top  = y;
	buttonBody.style.height = 28;
	*/
</script>
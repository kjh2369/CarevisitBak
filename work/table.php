<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once("../inc/_myFun.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != "" ? $_POST['mKind'] : $_SESSION["userCenterKind"][0];
	$mYear = $_POST['mYear'] != '' ? $_POST['mYear'] : date('Y', mkTime());
	$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());

	$setYear = $myF->year();
?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title" colspan="2">근무현황(직원)</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left;" colspan="2">
		<table style="width:100%;">
		<tr>
			<td class="noborder" style="width:70%; text-align:left; padding-top:10px;">
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
				<select name="mService" style="width:auto;">
					<option value="all">전체</option>
					<option value="200">요양</option>
					<option value="500">목욕</option>
					<option value="800">간호</option>
				</select>
				<select name="mPlan" style="width:auto;">
					<option value="plan">계획</option>
					<option value="conf">실적</option>
				</select>
				<select name="mKind" style="width:auto;">
				<?
					for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
						echo "<option value='".$_SESSION["userCenterKind"][$r]."' ".($_SESSION["userCenterKind"][$r] == $mKind ? "selected" : "").">".$_SESSION["userCenterKindName"][$r]."</option>";
					}
				?>
				</select>
				<select name="mYear" style="width:auto;">
				<?
					for($i=$setYear[0]; $i<=$setYear[1]; $i++){
						echo '<option value="'.$i.'" '.($i == $mYear?'selected':'').'>'.$i.'년</option>';
					}
				?>
				</select>
				<select name="mMonth" style="width:auto;">
				<?
					for($i=1; $i<=12; $i++){
						echo '<option value="'.(($i<10?'0':'').$i).'" '.((($i<10?'0':'').$i)==$mMonth?'selected':'').'>'.$i.'월</option>';
					}
				?>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="workTableList();">조회</button></span>
				<span class="btn_pack m icon"><span class="excel"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="workTableExcel();">엑셀</button></span>
				<span class="btn_pack m icon"><span class="pdf"></span><button type="button" onFocus="this.blur();" onClick="pdf();">PDF</button></span>
			</td>
			<td class="noborder" style="text-align:right; padding-top:10px;"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left; vertical-align:top;" colspan="2">
		<div id="myBody" style="width:1140px;"></div>
	</td>
</tr>
</table>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language="javascript">
	workTableList();
</script>
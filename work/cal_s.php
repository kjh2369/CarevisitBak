<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include('../inc/_ed.php');
	include("../inc/_myFun.php");

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != "" ? $_POST['mKind'] : $_SESSION["userCenterKind"][0];
	$mYear = $_POST['mYear'] != '' ? $_POST['mYear'] : date('Y', mkTime());
	$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());
	
	$setYear[0] = 2010;
	$setYear[1] = date('Y', mkTime());
?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title" colspan="2">서비스제공일정표(수급자기준)</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left;" colspan="2">
		<table>
		<tr>
			<td class="noborder" style="text-align:left; padding-top:10px;">
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
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
				<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="">검색</button></span>
			</td>
			<td class="noborder" style="text-align:right; padding-top:10px;"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left; vertical-align:top;" colspan="2">
		<div id="myBody"></div>
	</td>
</tr>
</table>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	//workTableList();
</script>
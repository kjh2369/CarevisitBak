<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_POST['mCode'];
	}else{
		$mCode = $_SESSION['userCenterCode'];
	}
	//$mCode  = $_REQUEST['mCode']  != '' ? $_REQUEST['mCode']  : $_SESSION['userCenterCode'];
	$mKind  = $_REQUEST['mKind']  != '' ? $_REQUEST['mKind']  : $_SESSION['userCenterKind'][0];
?>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title" colspan="2">고객 현황</td>
</tr>
<tr>
	<td class="noborder" style="width:60%; text-align:left; padding-top:10px;">
		<input name="mCode" type="hidden" value="<?=$mCode;?>">
		<select name="mKind" style="width:auto;">
		<?
			for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
			?>
				<option value="<?=$_SESSION["userCenterKind"][$r];?>"><?=$_SESSION["userCenterKindName"][$r];?></option>
			<?
			}
		?>
		</select>
		<select name="mGubun" style="width:auto;">
		<option value="1">판정등급별</option>
		<option value="2">본인부담비율별</option>
		</select>
		<input type="button" value="조회" class="btnSmall2" onFocus="this.blur();" onClick="conditionSearch(myBody, document.f.mCode.value, document.f.mKind.value, document.f.mGubun.value);">
	</td>
	<td class="noborder" style="width:40%; text-align:right;">
	</td>
</tr>
<tr>
	<td class="noborder" style="vertical-align:top;" colspan="2">
		<div id="myBody"></div>
	</td>
</tr>
</table>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script>
	conditionSearch(myBody, '', '', '', '')
</script>
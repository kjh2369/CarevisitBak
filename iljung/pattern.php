<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include('../inc/_ed.php');

	$mCode = $_REQUEST['mCode'] != '' ? $_REQUEST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_REQUEST['mKind'] != '' ? $_REQUEST['mKind'] : $_SESSION['userCenterKind'][0];
?>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title" colspan="2">수급자별 일정패턴등록</td>
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
					?>
						<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $mKind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
					<?
					}
				?>
				</select>
				<input type="button" value="검색" class="btnSmall2" onFocus="this.blur();" onClick="patternList(document.f.mCode.value, document.f.mKind.value);">
			</td>
			<td class="noborder" style="text-align:right; padding-top:10px;"></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="noborder" style="width:40%; vertical-align:top;">
		<div id="mySugupja"></div>
	</td>
	<td class="noborder" style="width:60%; vertical-align:top;">
		<div id="myList"></div>
	</td>
</tr>
</table>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	patternList("<?=$mCode;?>", "<?=$mKind;?>");
</script>
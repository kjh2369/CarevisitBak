<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');

	$mCode  = $_POST['mCode']  != '' ? $_POST['mCode']  : $_SESSION['userCenterCode'];
	$mKind  = $_POST['mKind']  != '' ? $_POST['mKind']  : $_SESSION['userCenterKind'][0];
	$mGubun = $_GET['mGubun'];
?>
<script src="../js/account.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2"><?=($mGubun=='NOT'?'미수금현황 조회':'입금내역조회(일별, 수급자별)');?></td>
	</tr>
	<tr>
		<td class="noborder" style="text-align:left;">
			<input name="mCode" type="hidden" value="<?=$mCode;?>">
			<select name="mKind" style="width:150px;">
			<?
				for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
				?>
					<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $mKind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
				<?
				}
			?>
			</select>
			<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="getDepositList(document.f.mCode.value,document.f.mKind.value);">조회</button></span>
		</td>
	</tr>
	<tr>
		<td class="noborder">
			<div id="myBody"></div>
			<div id="myUnpaid"></div>
			<div id="myDetail"></div>
		</td>
	</tr>
</table>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	getDepositList('<?=$mCode;?>','<?=$mKind;?>','<?=$mGubun;?>','');
</script>
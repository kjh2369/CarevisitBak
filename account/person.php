<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');

	$mCode  = $_POST['mCode']  != '' ? $_POST['mCode']  : $_SESSION['userCenterCode'];
	$mKind  = $_POST['mKind']  != '' ? $_POST['mKind']  : $_SESSION['userCenterKind'][0];
	$mYear  = $_POST['mYear']  != '' ? $_POST['mYear']  : date('Y', mkTime());
	//$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());

	$setYear = $conn->get_iljung_year($mCode);
?>
<script src="../js/account.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2">월별미수금현황조회</td>
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
			<select name="mYear" style="width:65px;">
			<?
				for($i=$setYear[0]; $i<=$setYear[1]; $i++){
				?>
					<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
				<?
				}
			?>
			</select>
			<input type="button" value="검색" class="btnSmall2" onClick="getPersonAccountList(myBody,document.f.mCode.value,document.f.mKind.value,document.f.mYear.value);">
		</td>
	</tr>
	<tr>
		<td class="noborder">
			<div id="myBody"></div>
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
	getPersonAccountList(myBody,'','','');
</script>
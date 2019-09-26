<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != '' ? $_POST['mKind'] : $_SESSION['userCenterKind'][0];
?>
<script src="../js/account.js" type="text/javascript"></script>
<table style="width:100%;">
	<tr>
		<td class="title" colspan="2">수급자별 미수금입금처리</td>
	</tr>
	<tr>
		<td class="noborder">
			<div id="myBody"></div>
		</td>
	</tr>
</table>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	getNotAccountList('<?=$mCode;?>','<?=$mKind;?>');
</script>
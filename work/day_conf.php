<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$P = $_REQUEST;

	$mCode = $P["mCode"] != "" ? $P["mCode"] : $_SESSION["userCenterCode"];
	$mKind = $P["mKind"] != "" ? $P["mKind"] : $_SESSION["userCenterKind"][0];
	$mYear = $P["mYear"] != "" ? $P["mYear"] : date("Y", mkTime());
	$mMonth = $P["mMonth"] != "" ? $P["mMonth"] : date("m", mkTime());
	$mDay = $P['mDay'];
	$mType = $P['mType'];
?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<div id="myBody"></div>
<input name="mCode" type="hidden" value="<?=$mCode?>">
</form>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	if ('<?=$mType;?>' == 'DAY'){
		getDayConfList(document.getElementById('myBody'),  "<?=$mCode;?>", "<?=$mKind;?>", "<?=$mYear;?>", "<?=$mMonth;?>", "<?=$mDay;?>");
	}else{
		setDayConfCalendar(document.getElementById('myBody'), "<?=$mCode;?>", "<?=$mKind;?>", "<?=$mYear;?>", "<?=$mMonth;?>");
	}
</script>
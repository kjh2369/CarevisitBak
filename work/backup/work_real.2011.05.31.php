<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	$mCode = $_SESSION["userCenterCode"];
	$mKind = $_POST["myKind"] != "" ? $_POST["myKind"] : $_SESSION["userCenterKind"][0];
	$mYear = date('Y', mkTime());
	$mMonth = date('m', mkTime());
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function member_detail(year, month, jumin, member){
	var f = document.f;

	f.year.value   = year;
	f.month.value  = month;
	f.jumin.value  = jumin;
	f.member.value = member;

	f.action = '../work/work_month.php';
	f.submit();
}
-->
</script>
<table style="width:100%;">
<colgroup>
	<col width="50%" span="2">
<colgroup>
<tr>
	<td class="title">당일일정(기관)</td>
	<td class="noborder" style="text-align:right; border-bottom:1px dotted #e5e5e5;">
		<div style="border:0;">
			새로고침<input name="progressTime" type="text" value="30" style="width:30px; text-align:center;" onFocus="document.getElementById('reloadTime').focus();" readOnly>초
			간격<input name="reloadTime" type="text" value="30" style="width:30px; text-align:center;" onFocus="this.select();">
			<span class="btn_pack small"><button type="button" onFocus="this.blur();" onClick="timerRestart();">적용</button></span>
		</div>
	</td>
</tr>
<tr>
	<td class="noborder" colspan="2">
		<div id="myBody"></div>
	</td>
</tr>
</table>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	getWorkRealList('<?=$mCode;?>','all','all');
</script>
<script language="javascript">
<!--

var it_timer    = null;
var ii_sec      = 30;
var it_secounds = 30;

function timerInt(){
	it_timer = setInterval("timer()", 1000);
}

function timerClear(){
	clearInterval(it_timer);
}

function timer(){
	ii_sec--;

	if (ii_sec < 1){
		ii_sec = it_secounds;
		getWorkRealList('<?=$mCode;?>', document.f.mKind.value, document.f.mStat.value);
	}

	document.getElementById('progressTime').value = ii_sec;
}

function timerRestart(){
	it_secounds = document.getElementById('reloadTime').value;
	ii_sec = it_secounds;
	timerClear();
	timerInt();
}
timerInt();
//-->
</script>
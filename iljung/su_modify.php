<?
	include("../inc/_header.php");
	include("../inc/_ed.php");

	$p = $_REQUEST;

	$mYear = $p['calYear'];
	$mMonth = $p['calMonth'];
	$mSugupja = $ed->de($p['mSugupja']);

	if ($p["mType"] == 'DAY'){
		$mYoyangsa = $ed->de($p['mYoyangsa']);
	}else{
		$mYoyangsa = '';
	}
?>
<style>
body{
	margin-top:10px;
	margin-left:10px;
	overflow-x:hidden;
}
</style>
<form name="f" method="post" action="su_iljung_ok.php">
<input name="pressCal" type="hidden" value="N">
<input name="modifyFlag" type="hidden" value="<?=$p["mType"];?>">
<div id="center_info"></div>
<div style="width:900px; padding-top:10px; padding-bottom:5px; text-align:right;">
	<!--<a href="#" onClick="_iljungSubmit();"><img src="../image/btn_save_2.png"></a>-->
</div>
<div id="calendar" style="width:900px;"></div>
<div id="bodyLayer" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
</form>
<?
	include("../inc/_footer.php");
?>
<script language='javascript'>
function _setCalendar(pType, pCalYear, pCalMonth, pCalDay){
	var mCode = null;
	var mKind = null;

	mCode = document.f.mCode.value;
	mKind = document.f.mKind.value;

	if (pType == 'DAY'){
		calendar.innerHTML = getHttpRequest('su_calendar.php?gubun=reg&calYear='+pCalYear+'&calMonth='+pCalMonth+'&calDay='+pCalDay+'&mCode='+mCode+'&mKind='+mKind+'&workType=dayModify&mJuminNo=<?=$ed->en($mSugupja);?>&mYoyangsa=<?=$ed->en($mYoyangsa);?>');
	}else{
		calendar.innerHTML = getHttpRequest('su_calendar.php?gubun=reg&calYear='+pCalYear+'&calMonth='+pCalMonth+'&mCode='+mCode+'&mKind='+mKind+'&workType=modify&mJuminNo=<?=$ed->en($mSugupja);?>');
	}
}
	_setCenterInfo('<?=$p["mCode"];?>', '<?=$p["mKind"];?>', '<?=$p["mKey"];?>','<?=$p["calYear"]?>','<?=$p["calMonth"]?>');
	_setCalendar('<?=$p["mType"]?>','<?=$p["calYear"]?>','<?=$p["calMonth"]?>','<?=$p["calDay"]?>');
</script>
<script language="javascript">
	self.focus();
</script>
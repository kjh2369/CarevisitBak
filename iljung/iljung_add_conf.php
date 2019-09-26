<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
?>
<style>
body{
	margin-top:10px;
	margin-left:10px;
}
</style>
<script type="text/javascript" src="../js/iljung.reg.js"></script>
<script type="text/javascript" src="../js/iljung.add.js"></script>
<script language='javascript'>
<!--
var opener = null;
var f = null;

function currnetRow(value1, value2, value3, value4, value5){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;
	currentItem[4] = value5;

	window.returnValue = currentItem;
	window.close();
}


window.onload = function(){
	var agmt = window.dialogArguments;

	opener = agmt.win;
	f = document.f;

	f.mCode.value	= agmt.code;
	f.addDate.value	= agmt.date;
	f.c_cd.value	= agmt.c_cd;
	f.m_cd.value	= agmt.m_cd;

	_set_center_info(f.mCode.value,f.c_cd.value,f.addDate.value.substring(0,4),f.addDate.value.substring(4,6),'','ADD_CONF',f.addDate.value.substring(6,8));
}

//-->
</script>
<form name="f" method="post">

<div id="win_add_body" style="padding-bottom:10px;">

<div id="center_info"></div>

<div id="iljung_care"></div>

<div id="iljung_const"></div>

<div id="iljung_button"></div>

</div>

<input name='mCode' type='hidden' value=''>
<input name='addDate' type='hidden' value=''>
<input name='c_cd' type='hidden' value=''>
<input name='c_ky' type='hidden' value=''>
<input name='m_cd' type='hidden' value=''>

</form>

<?
	include_once("../inc/_footer.php");
?>
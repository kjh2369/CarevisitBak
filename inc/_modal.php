<?
	include_once('../inc/_header.php');

	$param = explode(',', $_GET['param']);

	//print_r($param);

	$mather  = 'modal';
	$mKind   = $param[0];
	$date    = $param[1];
	$yoyCode = $param[2];
	$seq     = $param[3];
	$gubun   = $param[4]; //지정 폴더
	$type    = $param[5]; //작업구분
	$index   = $param[6]; //인덱스
	$exp     = $param[7]; //확장자
	$value1  = $param[8]; //리포트 메뉴
	$value2  = $param[9]; //리포트 텝

	$mCode  = $_SESSION['userCenterCode'];
	$mMenu  = $value1;
	$mTab   = $value2;
	$mIndex = $index;
?>
<base target="_self">
<style>
.view_type1 thead th{
	margin:0;
	padding:0;
	text-align:center;
}

.view_type1 .td19{
	background-color:#fbfbe7;
	border-left:0;
	border-right:1px solid #ccc;
	border-bottom:1px solid #ccc;
	border-top:1px solid #ccc;
}

.view_type2 tbody td{
	height:26px;
}

.gender_m{
	background:url('http://www.carevisit.net/image/m.png') no-repeat;
}

.gender_f{
	background:url('http://www.carevisit.net/image/f.png') no-repeat;
}

.f_me{
	position:absolute;
	width:80px;
	height:60px;
	font-weight:bold;
	text-align:center;
	vertical-align:top;
	padding-top:15px;
	border:2px solid #fff600;
	background-color:#fffcd0;
	cursor:default;
}

.f_m{
	position:absolute;
	width:80px;
	height:60px;
	font-weight:bold;
	text-align:center;
	vertical-align:top;
	padding-top:15px;
	border:2px solid #0000ff;
	background-color:#b8b8ff;
	cursor:default;
}

.f_f{
	position:absolute;
	width:80px;
	height:60px;
	font-weight:bold;
	text-align:center;
	vertical-align:top;
	padding-top:15px;
	border:2px solid #ff0000;
	background-color:#ffbdbd;
	cursor:default;
}

.f_p{
	position:absolute;
	width:80px;
	height:60px;
	font-weight:bold;
	text-align:center;
	vertical-align:top;
	padding-top:15px;
	border:2px solid #4cff06;
	background-color:#d2ffc1;
	cursor:default;
}

.border_tr{
	border-top:1px solid #000;
	border-right:1px solid #000;
}

.border_tl{
	border-top:1px solid #000;
	border-left:1px solid #000;
}

.border_t{
	border-top:1px solid #000;
}
</style>
<script type="text/javascript" src='../js/report.js'></script>
<script language='javascript'>
function onSubmit(p_gubun, p_index){
	var form = document.f;
	var path = null;

	switch(p_gubun){
	case 'report':
		path = '../report/report_save_'+p_index+'.php';
		break;
	}

	if (!checkReportForm(p_index, form)){
		return;
	}

	form.action = path;
	form.submit();
}
</script>
<div style="width:1000px; height:670px; margin-left:0px; margin-top:-5px; overflow-x:hidden; overflow-y:scroll;">
<?
	switch($gubun){
	case 'report':
		$path = '../report/report_'.$type.'_'.$index.'.'.$exp;
		break;
	}

	echo '<form name="f" method="post">';

	include($path);

	echo '<input name="mather" type="hidden" value="'.$mather.'">';
	echo '</form>';
?>
</div>
<div style="width:1005px; height:30px; text-align:right; margin-left:-20px; padding-top:5px; padding-right:5px; border-top:2px solid #ccc;">
	<span class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onClick="onSubmit('<?=$gubun;?>','<?=$mIndex;?>');">저장</button></span>
	<span class="btn_pack m icon"><span class="delete"></span><button type="button" onFocus="this.blur();" onClick="window.close();">닫기</button></span><!--location.reload();-->
</div>
<?
	include_once('../inc/_footer.php');
?>
<script language='javascript'>
	window.onload = function(){
		reportAddEvent('<?=$mIndex;?>');

	}
</script>
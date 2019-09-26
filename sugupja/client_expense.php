<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$year	= date('Y');
	$month	= IntVal(date('m'));
	$month	--;

	if ($month == 0){
		$year --;
		$month	= 12;
	}
?>
<div class="title title_border">본인부담금 계산</div>
<form name="f" method="post">
<table style="margin:10px;">
	<colgroup>
		<col width="100px">
		<col width="75px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left">본인부담금 계산</td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left">계산 기준년월</td>
			<td class="my_border_blue my_bold my_center">
				<div class="left" style="padding-top:2px;">
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="year"><?=$year;?></div>
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="my_border_blue my_center"><? echo $myF->_btn_month($month, 'lfMoveMonth(', ');');?></td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left" rowspan="2">계산구분</td>
			<td class="my_border_blue my_bold my_left" colspan="2">
				<input id="optCalGbn1" name="optCalGbn" type="radio" class="radio" value="1"><label for="optCalGbn1">전체</label>
				<input id="optCalGbn2" name="optCalGbn" type="radio" class="radio" value="2"><label for="optCalGbn2">개별</label>
			</td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_left" colspan="2" id="lblMsg" calTarget="">&nbsp;</td>
		</tr>
		<tr>
			<td class="my_border_blue my_bold my_bg_blue my_left">선택사항</td>
			<td class="my_border_blue my_bold my_left" colspan="2">
				<label><input id="chkRemoveIljung" type="checkbox" value="Y" class="checkbox" <?=$gDomain == 'dolvoin.net' ? 'checked' : '';?>>재가요양 본인부담금 계산 누락 대상자의 재가요양 일정을 삭제합니다.</label><br>
				<label><input id="chkRemovePlan" type="checkbox" value="Y" class="checkbox">실적이 없는 재가요양계획을 삭제합니다.</label><br>
			</td>
		</tr>
		<tr>
			<td class="my_border_blue center" colspan="3">
				<span class="btn_pack m"><button id="btnExec" type="button" onclick="lfExpenseExec();" style="font-weight:bold;">본인부담금 계산 실행</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table style="width:100%; margin-left:10px; margin-top:20px; font-weight:bold;">
	<colgroup>
		<col width="15px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
			<td class="noborder" style="vertical-align:top; text-align:left;">수급자 실적내역으로 수급자의 본인부담금액을 계산합니다.</td>
		</tr>
		<tr>
			<td class="noborder" style="vertical-align:top; text-align:center;">※</td>
			<td class="noborder" style="vertical-align:top; text-align:left;"><span style="color:blue;">개별계산</span>을 하실 경우 "<span style="color:blue;">개별</span>"을 클릭 후 <span style="color:blue;">고객조회</span>에서 계산을 원하시는 대상을 선택한 후<br>"<span style="color:blue;">본인부담금 계산 실행</span>"을 클릭하여 주십시오.</td>
		</tr>
	</tbody>
</table>
<div id="bg" style="position:absolute; left:0; top:0; width:100%; height:100%; z-index:1000; background:url('../img/bg.png') repeat left top; display:none;"></div>
<div id="proc" style="position:absolute; top:0; left:0; z-index:1010; background-color:#ffffff; border:3px solid #666666; display:none;"></div>

<input id="code" name="txt" type="hidden" value="<?=$code;?>">
<input id="month" name="txt" type="hidden" value="<?=$month;?>">

</form>

<script type="text/javascript">
$(document).ready(function(){
	$('input:radio[name="optCalGbn"]').unbind('click').bind('click',function(){
		if ($(this).val() == '1'){
			$('#lblMsg').html('<span>전체 대상의 본인부담금을 계산합니다.</span>');
		}else{
			$('#lblMsg').attr('calTarget','').html('<span>대상을 선택하여 주십시오.</span>');
			lfFindClient();
		}
	});
	$('#optCalGbn1').click();
});

function lfFindClient(){
	var jumin = $('#txtClient').attr('jumin');
	var h = 400;
	var w = 600;
	var t = (screen.availHeight - h) / 2;
	var l = (screen.availWidth - w) / 2;

	var url = '../inc/_find_person.php';
	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
	var win = window.open('about:blank', 'FIND_CLIENT', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			'type':'sugupja'
		,	'jumin':jumin
		,	'year':'<?=$year;?>'
		,	'month':'<?=$month;?>'
		,	'return':'lfMemFindResult'
		};

	var form = document.createElement('form');
	var objs;
	for(var key in parm){
		objs = document.createElement('input');
		objs.setAttribute('type','hidden');
		objs.setAttribute('name',key);
		objs.setAttribute('value',parm[key]);

		form.appendChild(objs);
	}

	form.setAttribute('target','FIND_CLIENT');
	form.setAttribute('method','post');
	form.setAttribute('action',url);

	document.body.appendChild(form);

	form.submit();
}

function lfMemFindResult(obj){
	$('#lblMsg').attr('calTarget',obj[0]).html('"<span style=\'color:blue;\'>'+obj[1]+'</span>"님의 본인부담금을 계산합니다.</span>');
}

function lfMoveYear(aiPos){
	var year = parseInt($('#year').text()) + aiPos;

	$('#year').text(year);
}

function lfMoveMonth(aiMon){
	$(document).find('.my_month').each(function(){
		if ($(this).attr('id').toString().substr($(this).attr('id').toString().length - aiMon.toString().length - 1, $(this).attr('id').toString().length) == '_'+aiMon.toString()){
			$(this).removeClass('my_month_1');
			$(this).addClass('my_month_y');
		}else{
			$(this).removeClass('my_month_y');
			$(this).addClass('my_month_1');
		}
	});
	$('#month').val(aiMon);
}

function lfExpenseExec(){
	var carGbn = $('input:radio[name="optCalGbn"]:checked').val();
	var target = '';

	if (carGbn == '2'){
		target = $('#lblMsg').attr('calTarget');

		if (!target){
			alert('본인부담금을 계산할 대상을 선택하여 주십시오.');
			lfFindClient();
			return;
		}
	}

	$('#btnExec').attr('disabled',true);

	var removeIljung, removePlan;

	if ($('#chkRemoveIljung').attr('checked')){
		removeIljung = 'Y';
	}else{
		removeIljung = 'N';
	}

	if ($('#chkRemovePlan').attr('checked')){
		removePlan = 'Y';
	}else{
		removePlan = 'N';
	}
	
	$.ajax({
		type: 'POST'
	,	url : './client_expense_exec.php'
	,	data: {
			'year':$('#year').text()
		,	'month':$('#month').val()
		,	'calGbn':carGbn
		,	'calTarget':target
		}
	,	beforeSend: function (){
			var w = 300; //$(document).width() - 80;
			var h = 100; //$(document).height() - 200;
			var l = ($(document).width() - w) / 2;
			var t = 270;

			$('#proc')
				.css('top',t+'px')
				.css('left',l+'px')
				.css('width',w+'px')
				.css('height',h+'px')
				.html('<div style="text-align:center; font-weight:bold;"><br>본인부담금을 계산하고 있습니다.<br><br>잠시 기다려 주십시오.<br><br></div>')
				.show();
		}
	,	success: function(result){
			$('#proc').hide();

			if (result == 1){
				if (carGbn == '1'){
					if (removeIljung == 'Y') lfRemoveIljung();
					if (removePlan == 'Y') lfRemovePlan();
				}

				alert('정상적으로 처리되었습니다.');
			}else if (result == 9){
				alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}

			$('#btnExec').attr('disabled',false);
		}
	,	complite: function(result){
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfRemoveIljung(){
	$.ajax({
		type: 'POST'
	,	url : './client_expense_iljung_remove.php'
	,	async:false
	,	data: {
			'year':$('#year').text()
		,	'month':$('#month').val()
		}
	,	beforeSend: function (){
		}
	,	success: function(result){
			if (result) alert(result);
		}
	,	complite: function(result){
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfRemovePlan(){
	$.ajax({
		type: 'POST'
	,	url : './client_expense_plan_remove.php'
	,	async:false
	,	data: {
			'year':$('#year').text()
		,	'month':$('#month').val()
		}
	,	beforeSend: function (){
		}
	,	success: function(result){
			if (result) alert(result);
		}
	,	complite: function(result){
		}
	,	error: function (){
		}
	}).responseXML;
}
</script>
<?
	unset($data);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

?>
<script type="text/javascript">
var arrLvlPay = new Array();

$(document).ready(function(){
	//서비스구분
	$('input:radio[name="val"]').click(function(){
		if ($(this).val() == '1'){
			$('#lvl3').show();
			$('#lvl4').show();
			$('label[for="lvl3"]').show();
			$('label[for="lvl4"]').show();
		}else{
			$('#lvl3').attr('checked', false).hide();
			$('#lvl4').attr('checked', false).hide();
			$('label[for="lvl3"]').hide();
			$('label[for="lvl4"]').hide();
		}

		if ($('input:radio[name="lvl"]:checked').length > 0){
			$('input:radio[name="lvl"]:checked').click();
		}else{
			$('#amt').attr('value', '0').text('0');
			$('#time').attr('value', '0').text('0시간');
		}
	});

	$('input:radio[name="lvl"]').click(function(){
		var val  = $('input:radio[name="val"]:checked').val();
		var lvl  = $(this).val();
		var amt  = $(this).attr('amt'+val);
		var time = $(this).attr('time'+val);
		
		$('#amt').attr('value', amt).text(__num2str(amt));
		$('#time').attr('value', time).text(time+'시간');
	});

	arrLvlPay[1] = {
		 0:{'lvl':'1','from':'201102','to':'201301','amt1':'860000','time1':'103','amt2':'520000','time2':'63'}
		,1:{'lvl':'1','from':'201302','to':'201302','amt1':'886000','time1':'103','amt2':'0','time2':'0'}
		,2:{'lvl':'1','from':'201303','to':'201307','amt1':'919000','time1':'107','amt2':'0','time2':'0'}
		,3:{'lvl':'1','from':'201308','to':'201512','amt1':'1010000','time1':'118','amt2':'0','time2':'0'}
		,4:{'lvl':'1','from':'201601','to':'201612','amt1':'1063000','time1':'118','amt2':'0','time2':'0'}
		,5:{'lvl':'1','from':'201701','to':'201712','amt1':'1091000','time1':'118','amt2':'0','time2':'0'}
		,6:{'lvl':'1','from':'201801','to':'201812','amt1':'1270000','time1':'118','amt2':'0','time2':'0'}
		,7:{'lvl':'1','from':'201901','to':'999912','amt1':'1530000','time1':'118','amt2':'0','time2':'0'}
	};
	arrLvlPay[2] = {
		 0:{'lvl':'2','from':'201102','to':'201301','amt1':'690000','time1':'83','amt2':'350000','time2':'42'}
		,1:{'lvl':'2','from':'201302','to':'201302','amt1':'711000','time1':'83','amt2':'0','time2':'0'}
		,2:{'lvl':'2','from':'201303','to':'201307','amt1':'738000','time1':'86','amt2':'0','time2':'0'}
		,3:{'lvl':'2','from':'201308','to':'201512','amt1':'810000','time1':'94','amt2':'0','time2':'0'}
		,4:{'lvl':'2','from':'201601','to':'201612','amt1':'852000','time1':'94','amt2':'0','time2':'0'}
		,5:{'lvl':'2','from':'201701','to':'201712','amt1':'869000','time1':'94','amt2':'0','time2':'0'}
		,6:{'lvl':'2','from':'201801','to':'201812','amt1':'1012000','time1':'94','amt2':'0','time2':'0'}
		,7:{'lvl':'2','from':'201901','to':'999912','amt1':'1219000','time1':'94','amt2':'0','time2':'0'}
	};
	arrLvlPay[3] = {
		 0:{'lvl':'3','from':'201102','to':'201301','amt1':'520000','time1':'63','amt2':'0','time2':'0'}
		,1:{'lvl':'3','from':'201302','to':'201302','amt1':'536000','time1':'63','amt2':'0','time2':'0'}
		,2:{'lvl':'3','from':'201303','to':'201307','amt1':'556000','time1':'65','amt2':'0','time2':'0'}
		,3:{'lvl':'3','from':'201308','to':'201512','amt1':'610000','time1':'71','amt2':'0','time2':'0'}
		,4:{'lvl':'3','from':'201601','to':'201612','amt1':'642000','time1':'71','amt2':'0','time2':'0'}
		,5:{'lvl':'3','from':'201701','to':'201712','amt1':'657000','time1':'71','amt2':'0','time2':'0'}
		,6:{'lvl':'3','from':'201801','to':'201812','amt1':'764000','time1':'71','amt2':'0','time2':'0'}
		,7:{'lvl':'3','from':'201901','to':'999912','amt1':'921000','time1':'71','amt2':'0','time2':'0'}
	};
	arrLvlPay[4] = {
		 0:{'lvl':'4','from':'201102','to':'201301','amt1':'350000','time1':'42','amt2':'0','time2':'0'}
		,1:{'lvl':'4','from':'201302','to':'201302','amt1':'361000','time1':'42','amt2':'0','time2':'0'}
		,2:{'lvl':'4','from':'201303','to':'201307','amt1':'374000','time1':'43','amt2':'0','time2':'0'}
		,3:{'lvl':'4','from':'201308','to':'201512','amt1':'410000','time1':'47','amt2':'0','time2':'0'}
		,4:{'lvl':'4','from':'201601','to':'201612','amt1':'430000','time1':'47','amt2':'0','time2':'0'}
		,5:{'lvl':'4','from':'201701','to':'201712','amt1':'435000','time1':'47','amt2':'0','time2':'0'}
		,6:{'lvl':'4','from':'201801','to':'201812','amt1':'506000','time1':'47','amt2':'0','time2':'0'}
		,7:{'lvl':'4','from':'201901','to':'999912','amt1':'610000','time1':'47','amt2':'0','time2':'0'}
	};

	/*
	arrLvlPay[1][0] = {'lvl':'1','from':'201102','to':'201301','amt1':'860000','time1':'103','amt2':'520000','time2':'63'};
	arrLvlPay[1][1] = {'lvl':'1','from':'201302','to':'999912','amt1':'886000','time1':'103','amt2':'0','time2':'0'};
	arrLvlPay[2][0] = {'lvl':'2','from':'201102','to':'201301','amt1':'690000','time1':'83','amt2':'350000','time2':'42'};
	arrLvlPay[2][1] = {'lvl':'2','from':'201302','to':'999912','amt1':'711000','time1':'83','amt2':'0','time2':'0'};
	arrLvlPay[3][0] = {'lvl':'3','from':'201102','to':'201301','amt1':'520000','time1':'63','amt2':'0','time2':'0'};
	arrLvlPay[3][1] = {'lvl':'3','from':'201302','to':'999912','amt1':'536000','time1':'63','amt2':'0','time2':'0'};
	arrLvlPay[4][0] = {'lvl':'4','from':'201102','to':'201301','amt1':'350000','time1':'42','amt2':'0','time2':'0'};
	arrLvlPay[4][1] = {'lvl':'4','from':'201302','to':'999912','amt1':'361000','time1':'42','amt2':'0','time2':'0'};
	*/

	$('input:radio[name="val"]:input[value="'+opener.val+'"]').click();

	lfSetOption();
});

function lfSetOption(){
	var fromDt = $('#fromDt').val();
	var toDt   = $('#toDt').val();
	var yymm = getDt(fromDt ? fromDt : opener.from, toDt ? toDt : opener.to);
		yymm = yymm.split('-').join('').substring(0,6);

	for(var i=1; i<arrLvlPay.length; i++){
		for(var j in arrLvlPay[i]){
			if (arrLvlPay[i][j]['from'] <= yymm && arrLvlPay[i][j]['to'] >= yymm){
				$('#lvl'+i)
					.attr('amt1',arrLvlPay[i][j]['amt1'])
					.attr('time1',arrLvlPay[i][j]['time1'])
					.attr('amt2',arrLvlPay[i][j]['amt2'])
					.attr('time2',arrLvlPay[i][j]['time2']);
				break;
			}
		}
	}

	if (yymm >= '201301'){
		$('#val2').hide();
		$('label[for="val2"]').hide();
		$('#val1').attr('checked',true).click();
	}else{
		$('#val2').show();
		$('label[for="val2"]').show();
	}
}


function execApply9(){
	if (!$('input:radio[name="val"]:checked').val()){
		alert('나이구분을 선택하여 주십시오.');
		return;
	}

	if (!$('input:radio[name="lvl"]:checked').val()){
		alert('활동지원등급을 선택하여 주십시오.');
		return;
	}

	if (!$('#fromDt').val()){
		alert('적용기간 시작일자를 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	if (!$('#toDt').val()){
		alert('적용기간 종료일자를 입력하여 주십시오.');
		$('#toDt').focus();
		return;
	}

	var diffDt = __DateDiff(__getDate($('#fromDt').val()), __getDate($('#toDt').val()));

	if (diffDt < 0){
		alert('적용기간의 시작일자가 종료일자보다 큽니다.\n확인하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var seq = opener.seq;

	if ($('#modify').attr('checked')) seq = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : seq
		,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	mode  : 15
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				execDis(seq);
			}else if (result == 2 || result == 3){
				alert('입력하신 적용기간 이전에 등록된 내역이 있습니다.\n확인하여 주십이오.');
				$('#fromDt').focus();
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}

/*********************************************************

	산모신생아 적용

*********************************************************/
function execDis(seq){
	$.ajax({
		type: 'POST'
	,	url : './client_apply.php'
	,	data: {
			code     : opener.code
		,	jumin    : opener.jumin
		,	seq      : seq
		,	from	 : $('#fromDt').val()
		,	to		 : $('#toDt').val()
		,	val		 : $('input:radio[name="val"]:checked').val()
		,   lvl      : $('input:radio[name="lvl"]:checked').val()
		,	mode     : 18
		,	type     : opener.type
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			execApplyResult(result);
		}
	,	error: function (){
		}
	}).responseXML;
}

function setDetail9(){
	var td  = $('td', $('#tblList tr:first'));

	opener.from = __getDate($(td).eq(0).text());
	opener.to   = __getDate($(td).eq(1).text());
	opener.val  = $('#val_0').attr('value');
	opener.lvl  = $('#lvl_0').attr('value');
	opener.seq  = $('#seq_0').text();

	if (opener.seq > 0){
		$('.modify').show();
		setDtEnabled($('#modify'));
	}else{
		$('.modify').hide();
		$('#fromDt').removeAttr('readonly');
		$('#toDt').removeAttr('readonly');
	}
	
	$('input:radio[name="val"]:input[value="'+opener.val+'"]').attr('checked',true);
	$('input:radio[name="lvl"]:input[value="'+opener.lvl+'"]').attr('checked',true);
	$('#fromDt').val(opener.from).attr('value1',opener.from);
	$('#toDt').val(opener.to).attr('value1',opener.to);
	$('input:radio[name="lvl"]:checked').click();

	initForm();
}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="95px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>나이구분</th>
			<td>
				<input id="val1" name="val" type="radio" value="1" class="radio"><label for="val1">성인(18세이상)</label>
				<input id="val2" name="val" type="radio" value="2" class="radio"><label for="val2">아동(6세~18세미만)</label>
			</td>
			<td class="center" rowspan="4">
				<span class="btn_pack m"><button type="button" onclick="execApply();">적용</button></span>
				<?
					if ($debug){?>
						<span class="btn_pack m"><button type="button" onclick="document.f.submit();">Re</button></span><?
					}
				?>
			</td>
		</tr>
		<tr>
			<th>활동지원등급</th>
			<td>
				<input id="lvl1" name="lvl" type="radio" value="1" amt1="860000" time1="103" amt2="520000" time2="63" class="radio"><label for="lvl1">1등급</label>
				<input id="lvl2" name="lvl" type="radio" value="2" amt1="690000" time1="83" amt2="350000" time2="42" class="radio"><label for="lvl2">2등급</label>
				<input id="lvl3" name="lvl" type="radio" value="3" amt1="520000" time1="63" amt2="0" time2="0" class="radio"><label for="lvl3">3등급</label>
				<input id="lvl4" name="lvl" type="radio" value="4" amt1="350000" time1="42" amt2="0" time2="0" class="radio"><label for="lvl4">4등급</label>
			</td>
		</tr>
		<tr>
			<th>지원금액/시간</th>
			<td class="left">
				<div id="amt" value="0" style="float:left; width:auto;">0</div>
				<div style="float:left; width:auto; margin-left:3px; margin-right:3px;">/</div>
				<div id="time" value="0" style="float:left; width:auto;">0시간</div>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="lfSetOption();"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="lfSetOption();">
				<? if(!$IsClientInfo){ ?><input id="modify" name="modify" type="checkbox" class="checkbox modify" onclick="setDtEnabled(this);"><label for="modify" class="modify">재등록</label>
				<? }else { ?>
					</br><font color="red">※ 재등록은 일자만 입력하시면 추가등록 됩니다.</font>
				<? }?>
			</td>
		</tr>
	</tbody>
</table>

<input id="seq" name="seq" type="hidden" value="0">

<div class="title title_border">계약내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="110px">
		<col width="70px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">구분</th>
			<th class="head">지원금액</th>
			<th class="head">시간</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" class="center top">
				<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>
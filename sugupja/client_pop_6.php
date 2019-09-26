<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	var lvlBody = '';
	var dt = __getDt(opener.from,opener.to);
	
	
	if (opener.svcCd == '1' || opener.svcCd == '2' || opener.svcCd == '4'){
		lvlBody = '<div style="float:left; width:47%;"><input id="lvl1" name="lvl" type="radio" value="1" class="radio"><label for="lvl1">기초생할수급자</label></div>'
				+ '<div style="float:left; width:47%;"><input id="lvl2" name="lvl" type="radio" value="2" class="radio"><label for="lvl2">차상위계층</label></div>';
	}

	if (opener.svcCd == '2'){
		lvlBody += '<div style="float:left; width:47%;"><input id="lvl3" name="lvl" type="radio" value="3" class="radio"><label for="lvl3">차상위초과</label></div>';
		if (opener.from >= '2019-01-01'){
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl4" name="lvl" type="radio" value="4" class="radio"><label for="lvl4">120%이상~140%미만</label></div>';
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl5" name="lvl" type="radio" value="5" class="radio"><label for="lvl5">140%이상~160%이하</label></div>';
		}else if (opener.from >= '2018-01-01'){
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl4" name="lvl" type="radio" value="4" class="radio"><label for="lvl4">110%이상~140%미만</label></div>';
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl5" name="lvl" type="radio" value="5" class="radio"><label for="lvl5">140%이상~160%이하</label></div>';
		}else {
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl4" name="lvl" type="radio" value="4" class="radio"><label for="lvl4">100%이상~130%미만</label></div>';
			lvlBody += '<div style="float:left; width:47%; display:none;"><input id="lvl5" name="lvl" type="radio" value="5" class="radio"><label for="lvl5">130%이상~150%이하</label></div>';
		}
	}else if (opener.svcCd == '3'){
		lvlBody = '<div style="float:left; width:47%;"><input id="lvl1" name="lvl" type="radio" value="1" class="radio"><label for="lvl1">40%이하</label></div>'
				+ '<div style="float:left; width:47%;"><input id="lvl2" name="lvl" type="radio" value="2" class="radio"><label for="lvl2">40%초과~50%이하</label></div>';
	}else if (opener.svcCd == '4'){
		lvlBody += '<div style="float:left; width:47%;"><input id="lvl3" name="lvl" type="radio" value="3" class="radio"><label for="lvl3">50%이하</label></div>'
				+  '<div style="float:left; width:47%;"><input id="lvl4" name="lvl" type="radio" value="4" class="radio"><label for="lvl4">50%초과~100%이하</label></div>'
				+  '<div style="float:left; width:47%;"><input id="lvl5" name="lvl" type="radio" value="5" class="radio"><label for="lvl5">100%초과~150%이하</label></div>'
				+  '<div style="float:left; width:47%;"><input id="lvl6" name="lvl" type="radio" value="6" class="radio"><label for="lvl6">150%초과</label></div>';
	}



	lvlBody += '<div style="float:left; width:47%;"><input id="lvl9" name="lvl" type="radio" value="9" class="radio"><label for="lvl9">일반</label></div>';

	$('#lvlBody').html(lvlBody);

	//서비스시간
	$('input:radio[name="lvl"]').click(function(){
		setExpense(this);
	});

	var valNm = '';
	var tmNm  = '';
	var time  = 0;

	var nurseStndVal = new Array();

	switch(opener.svcCd){
		case '1':
			if (opener.val == '1'){
				if (opener.stndDt >= '2013-02-01'){
					time = 24;
				}else{
					time = 18;
				}
			}else if (opener.val == '2'){
				if (opener.stndDt >= '2013-02-01'){
					time = 27;
				}else{
					time = 24;
				}
			}

			tmNm = time+'시간';

			break;

		case '2':
			if (opener.val == '1'){
				valNm = '방문';
			}else if (opener.val == '2'){
				valNm = '주간보호';
			}else if (opener.val == '3'){
				valNm = '단기가사';
			}

			if (opener.val == '3'){
				if (opener.time == '1'){
					tmNm = valNm+'[24시간(1개월)]';
					time = 24;
				}else{
					tmNm = valNm+'[48시간(2개월)]';
					time = 48;
				}

			}else{
				if (opener.time == '1'){
					time = (opener.val == '1' ? 27 : 9);
				}else if (opener.time == '2'){
					time = (opener.val == '1' ? 36 : 12);
				}

				tmNm = valNm+'['+time+(opener.val == '1' ? '시간' : '일')+']';
			}

			break;

		case '3':
			switch(opener.val){
				case '1': tmNm = '단태아[12일]'; time = 12; break;
				case '2': tmNm = '쌍태아[18일]'; time = 18; break;
				case '3': tmNm = '삼태아[24일]'; time = 24; break;
			}
			break;

		case '4':
			switch(opener.val){
				case '1': tmNm = '성인/'; break;
				case '2': tmNm = '아동/'; break;
			}

			tmNm += opener.spt+'등급';
			time  = 1;
			break;
	}

	setSvcTime(time);

	$('#time').attr('value',time).text(tmNm);
	$('input:radio[name="lvl"]:input[value="'+opener.lvl+'"]').click();
});

function setSvcTime(time){
	var suga = '';
	var time = __str2num(time);
	var amt  = 0;

	switch(opener.svcCd){
		case '1':
			suga = 'VH001';
			break;

		case '2':
			if (opener.val == '1'){
				suga = 'VOV01';
			}else if (opener.val == '2'){
				suga = 'VOD01';
			}else if (opener.val == '3'){
				suga = 'VOS01';
			}
			break;

		case '3':
			suga = 'VM'+opener.val+'01';
			break;

		case '4':
			suga = 'VA'+(opener.val == '1' ? 'A' : opener.val == '2' ? 'C' : '')+opener.spt+'0';
			break;
	}

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()+'&stnd='+opener.stndDt
		,	svcCd : opener.svcCd
		,	suga  : suga
		,	mode  : 9
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (opener.svcCd == '4'){
				var val = __parseStr(result);

				$('#amt').attr('value', val['amt']).attr('value1', val['time']).text(__num2str(val['amt'])+'/'+val['time']+'시간');
			}else{
				amt = cutOff(result * time);
				$('#amt').attr('value', amt).text(__num2str(amt));
			}
		}
	,	error: function (){
		}
	}).responseXML;
}

function setExpense(obj){
	var suga = '';
	var val  = '';
	var lvl  = $(obj).val();
	var dt = __getDt($('#fromDt').val() ? $('#fromDt').val() : opener.from, $('#toDt').val() ? $('#toDt').val() : opener.to);
	
	if(dt.length == 8){
		dt = dt.substr(0,4)+'-'+dt.substr(4,2)+'-'+dt.substr(6,2);
	}

	switch(opener.svcCd){
		case '1':
			val  = opener.val;
			suga = 'VH0';
			break;
			
		case '2':
			
			if (dt >= '2014-02-01' && dt < '2018-01-01'){
				$('label[for="lvl3"]').text('차상위초과~100%미만');
				$('input:radio[id="lvl4"]').parent().show();
				$('input:radio[id="lvl5"]').parent().show();
			}else if (dt >= '2019-01-01'){
				$('label[for="lvl3"]').text('차상위초과~120%미만');
				$('label[for="lvl4"]').text('120%이상~140%미만');
				$('label[for="lvl5"]').text('140%이상~160%미만');
				$('input:radio[id="lvl4"]').parent().show();
				$('input:radio[id="lvl5"]').parent().show();
			}else if (dt >= '2018-01-01'){
				$('label[for="lvl3"]').text('차상위초과~110%미만');
				$('label[for="lvl4"]').text('110%이상~140%미만');
				$('label[for="lvl5"]').text('140%이상~160%미만');
				$('input:radio[id="lvl4"]').parent().show();
				$('input:radio[id="lvl5"]').parent().show();
			}else{
				$('label[for="lvl3"]').text('차상위초과');
				$('input:radio[id="lvl4"]').parent().hide();
				$('input:radio[id="lvl5"]').parent().hide();
			}


			val = opener.time;
			//suga = 'VO'+(opener.val == '1' ? 'V' : 'D');

			if (opener.val == '1'){
				suga = 'VOV';
			}else if (opener.val == '2'){
				suga = 'VOD';
			}else if (opener.val == '3'){
				suga = 'VOS';
			}

			break;

		case '3':
			val  = opener.val;
			suga = 'VM0';
			break;

		case '4':
			if (opener.val == '1'){
				//성인
				if (opener.spt == '1'){
					val = '4';
				}else if (opener.spt == '2'){
					val = '3';
				}else if (opener.spt == '3'){
					val = '2';
				}else if (opener.spt == '4'){
					val = '1';
				}
			}else if (opener.val == '2'){
				//아동
				val = opener.spt;
			}

			suga = 'VA0';
			break;
	}
	
	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	val  : val
		,	suga : suga
		,	lvl  : lvl
		,	mode : 11
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			result = __str2num(result);

			if (result < 0)
				result = __str2num($('#amt').attr('value'));

			var amt = __num2str(result);

			$('#expense').text(amt);
		}
	,	error: function (){
		}
	}).responseXML;
}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>서비스시간</th>
			<td class="left"><div id="time" value="0">0</div></td>
			<th>총지원금액</th>
			<td class="left"><div id="amt" value="0" value1="0">0</div></td>
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
			<th>소득등급</th>
			<td colspan="3"><div id="lvlBody"></div></td>
		</tr>
		<tr>
			<th>본인부담금</th>
			<td class="left" colspan="3"><div id="expense">0</div></td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td colspan="3">
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setExpense($('input:radio[name=\'lvl\']:checked'));"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setExpense($('input:radio[name=\'lvl\']:checked'));">
				<? if(!$IsClientInfo){ ?><input id="modify" name="modify" type="checkbox" class="checkbox modify" onclick="setDtEnabled(this,true);"><label for="modify" class="modify">재등록</label>
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
		<col width="130px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">소득등급</th>
			<th class="head">본인부담금</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="center top">
				<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>
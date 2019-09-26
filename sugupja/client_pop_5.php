<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	//서비스시간
	$('input:radio[name="nurseVal"]').click(function(){
		setNurseTime(this);
	});

	setServiceTime(__getDt(opener.from,opener.to));
	
	if (!opener.val) opener.val = '1';

	$('input:radio[name="nurseVal"]:input[value="'+opener.val+'"]').click();
});

function setNurseTime(obj){
	setServiceTime(__getDt());

	var time = $(obj).attr('value1');
	var amt  = 0;

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	svcCd : opener.svcCd
		,	suga  : 'VH001'
		,	mode  : 9
		}
	,	beforeSend: function (){
		}
	,	success: function (cost){
			
			amt = cost * time;
			
			if(time == 40){
				amt = amt - 5000;
			}

			$('#nurseAmt').attr('value', amt).text(__num2str(amt));

		}
	,	error: function (){
		}
	}).responseXML;
}

function setServiceTime(asDt){
	if (asDt.split('-').join('') >= '201302' && asDt.split('-').join('') < '201901'){
		$('#nurseVal1').attr('value1','24');
		$('#nurseVal2').attr('value1','27');
		$('label[for="nurseVal1"]').text('24시간');
		$('label[for="nurseVal2"]').text('27시간');

	}else if (asDt.split('-').join('') >= '201901'){
		$('#nurseVal1').attr('value1','24');
		$('#nurseVal2').attr('value1','27');
		$('#nurseVal3').attr('value1','40');
		$('label[for="nurseVal1"]').text('24시간');
		$('label[for="nurseVal2"]').text('27시간');
		$('label[for="nurseVal3"]').text('40시간');
	}else{
		$('#nurseVal3').hide();
		$('#nurseVal1').attr('value1','18');
		$('#nurseVal2').attr('value1','24');
		$('label[for="nurseVal1"]').text('18시간');
		$('label[for="nurseVal2"]').text('24시간');
	}
}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>서비스시간</th>
			<td class="left">
				<input id="nurseVal1" name="nurseVal" type="radio" value="1" value1="18" class="radio"><label for="nurseVal1">18시간</label>
				<input id="nurseVal2" name="nurseVal" type="radio" value="2" value1="24" class="radio"><label for="nurseVal2">24시간</label>
				<input id="nurseVal3" name="nurseVal" type="radio" value="3" value1="40" class="radio" ><label for="nurseVal3">40시간</label>
			</td>
			<td class="center" rowspan="3">
				<span class="btn_pack m"><button type="button" onclick="execApply();">적용</button></span>
				<?
					if ($debug){?>
						<span class="btn_pack m"><button type="button" onclick="document.f.submit();">Re</button></span><?
					}
				?>
			</td>
		</tr>
		<tr>
			<th>지원금액</th>
			<td class="left"><span id="nurseAmt" value="0">0</span></td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setNurseTime($('input:radio[name=\'nurseVal\']:checked'));"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setNurseTime($('input:radio[name=\'nurseVal\']:checked'));">
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
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">서비스시간</th>
			<th class="head">지원금액</th>
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
<div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div>
<?
	include_once('../inc/_db_close.php');
?>
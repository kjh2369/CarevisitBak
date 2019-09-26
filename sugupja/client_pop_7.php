<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	//서비스구분
	$('input:radio[name="oldVal"]').click(function(){
		if ($(this).val() == '1'){
			$('label[for="oldTm1"]').text('27시간').show();
			$('label[for="oldTm2"]').text('36시간').show();
			$('#oldTm1').attr('value1','27').show();
			$('#oldTm2').attr('value1','36').show();
		}else if ($(this).val() == '2'){
			$('label[for="oldTm1"]').text('9일').show();
			$('label[for="oldTm2"]').text('12일').show();
			$('#oldTm1').attr('value1','9').show();
			$('#oldTm2').attr('value1','12').show();
		}else if ($(this).val() == '3'){
			$('label[for="oldTm1"]').text('24시간(1개월)').show();
			$('#oldTm1').attr('value1','24').show();
			//$('label[for="oldTm2"]').hide();
			//$('#oldTm2').hide();
			$('label[for="oldTm2"]').text('48시간(2개월)').show();
			$('#oldTm2').attr('value1','48').show();
		}

		$('#oldTm1').click();
	});

	//서비스시간
	$('input:radio[name="oldTm"]').click(function(){
		setOldAmt(this);
	});

	if (!opener.val) opener.val = '1';
	if (!opener.time) opener.time = '1';

	$('input:radio[name="oldVal"]:input[value="'+opener.val+'"]').click();
	$('input:radio[name="oldTm"]:input[value="'+opener.time+'"]').click();
});

function setOldAmt(obj){
	var cd = $('input:radio[name="oldVal"]:checked').val();
	var tm = $(obj).attr('value1');

	if (cd == '3'){
		if ($('#fromDt').val() && $('#fromDt').val() < '2014-04-01'){
			alert('단기가사서비스는 2014.04.01 이후부터 가능합니다.');
			$('#fromDt').val('').focus();
			$('#toDt').val('');
			return;
		}
	}

	if (cd == '1')
		cd = 'V'; //방문
	else if (cd == '2')
		cd = 'D'; //주간보호
	else if (cd == '3')
		cd = 'S'; //단기가사

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	svcCd : opener.svcCd
		,	suga  : 'VO'+cd+'01'
		,	mode  : 9
		}
	,	beforeSend: function (){
		}
	,	success: function (cost){
			var amt = __num2str(cost * tm);

			$('#oldAmt').text(amt);
		}
	,	error: function (){
		}
	}).responseXML;
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
			<th>서비스구분</th>
			<td>
				<input id="oldVal1" name="oldVal" type="radio" value="1" class="radio"><label for="oldVal1">방문</label>
				<input id="oldVal2" name="oldVal" type="radio" value="2" class="radio"><label for="oldVal2">주간보호</label>
				<input id="oldVal3" name="oldVal" type="radio" value="3" class="radio"><label for="oldVal3">단기가사</label>
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
			<th>서비스시간/일수</th>
			<td>
				<input id="oldTm1" name="oldTm" type="radio" value="1" value1="" class="radio"><label for="oldTm1"></label>
				<input id="oldTm2" name="oldTm" type="radio" value="2" value1="" class="radio"><label for="oldTm2"></label>
			</td>
		</tr>
		<tr>
			<th>지원금액</th>
			<td class="left"><div id="oldAmt">0</div></td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setOldAmt($('input:radio[name=\'oldTm\']:checked'));"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setOldAmt($('input:radio[name=\'oldTm\']:checked'));">
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
		<col width="90px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">구분</th>
			<th class="head">시간/일수</th>
			<th class="head">지원금액</th>
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
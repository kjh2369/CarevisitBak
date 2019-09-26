<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	//서비스구분
	$('input:radio[name="val"]').click(function(){
		setAmt(this);
	});

	$('input:radio[name="val"]:input[value="'+opener.val+'"]').click();
});

function setAmt(obj){
	var cd = $(obj).val();
	var tm = $(obj).attr('value1');

	$.ajax({
		type: 'POST'
	,	url : './client_pop_fun.php'
	,	data: {
			para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
		,	svcCd : opener.svcCd
		,	suga  : 'VM'+cd+'01'
		,	mode  : 9
		}
	,	beforeSend: function (){
		}
	,	success: function (cost){
			var amt = cutOff(cost * tm);
			$('#amt').text(__num2str(amt));
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
				<input id="val1" name="val" type="radio" value="1" value1="12" class="radio"><label for="val1">단태아[12일]</label>
				<input id="val2" name="val" type="radio" value="2" value1="18" class="radio"><label for="val2">쌍태아[18일]</label>
				<input id="val3" name="val" type="radio" value="3" value1="24" class="radio"><label for="val3">삼태아[24일]</label>
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
			<td class="left"><div id="amt">0</div></td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setAmt($('input:radio[name=\'val\']:checked'));"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setAmt($('input:radio[name=\'val\']:checked'));">
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
		<col width="130px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">구분</th>
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
<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>
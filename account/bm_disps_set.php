<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;
	var close = '';

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = false;

		$('input:text').each(function(){
			__init_object(this);
		});

		lfLoadOrgNm();
		lfSearch();
	});

	window.onunload = function(){
		if (!opener.result) return;

		var val = {};

		val['allotCnt']	= __str2num($('#txtAllotCnt').val());
		val['allotAmt']	= __str2num($('#txtAllotAmt').val());
		val['employCnt']= __str2num($('#txtEmployCnt').val());
		val['deductAmt']= __str2num($('#txtDeductAmt').val());

		opener.win.lfSetSub(opener.orgNo, __str2num(opener.month), val);
	}

	function lfLoadOrgNm(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_load_info.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseVal(data);

				$('#lblOrgNm').text(col['orgNm']);

				close = col['close'];

				if (close == 'Y'){
					$('#btnSave').attr('disabled',true);
				}else{
					$('#btnSave').attr('disabled',false);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_disps_set_search.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#txtAllotCnt').val(col['allotCnt']);
				$('#txtAllotAmt').val(__num2str(col['allotAmt']));
				$('#txtEmployCnt').val(col['employCnt']);
				$('#txtDeductAmt').val(__num2str(col['deductAmt']));
				$('#txtMemo').val(col['memo']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_disps_set_save.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			,	'allotCnt':$('#txtAllotCnt').val()
			,	'allotAmt':$('#txtAllotAmt').val()
			,	'employCnt':$('#txtEmployCnt').val()
			,	'deductAmt':$('#txtDeductAmt').val()
			,	'memo':$('#txtMemo').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					opener.result = true;
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDataCopy(){
		if (!confirm('전월 데이타를 적용하시면 현재데이타는 삭제됩니다.\n전월 데이타를 적용하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./bm_disps_set_datacopy.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					opener.result = true;
					lfSearch();
				}else if (result == 5){
					alert('전월의 데이타가 존재하지 않습니다. 확인하여 주십시오.');
				}else if (result == 7){
					alert('마감된 년월입니다. 확인하여 주십시오.');
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">급여등록 및 수정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">센터명</th>
			<td class="left" id="lblOrgNm"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="110px">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">장애인 분담금 인원</th>
			<td class="center"><input id="txtAllotCnt" type="text" value="0" class="number" style="width:70px;"></td>
			<th class="center">금액</th>
			<td class=""><input id="txtAllotAmt" type="text" value="0" class="number" style="width:70px;"></td>
		</tr>
		<tr>
			<th class="left">장애인 채용 인원</th>
			<td class="center"><input id="txtEmployCnt" type="text" value="0" class="number" style="width:70px;"></td>
			<th class="center">공제금</th>
			<td class=""><input id="txtDeductAmt" type="text" value="0" class="number" style="width:70px;"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">메모</th>
			<td class="center">
				<textarea id="txtMemo" style="width:100%; height:50px"></textarea>
			</td>
		</tr>
	</tbody>
</table>
<div class="center" style="margin-top:10px;">
	<span class="btn_pack small"><button id="btnSave" onclick="lfSave();">저장</button></span>
	<span class="btn_pack small"><button id="btnSave" onclick="lfDataCopy();">전월데이타 적용</button></span>
</div>
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_stopset_search.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				//alert(html);
				$('tbody',$('#ID_DIV_LIST')).html(html);
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfStopSet(IsYn,seq){
		if (IsYn == 'D'){
			if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하겠습니까?')) return;
		}

		if (IsYn == 'N'){
			if (!$('#txtDate').val()){
				alert('중지일자를 입력하여 주십시오.');
				$('#txtDate').focus();
				return;
			}

			if (!$('#txtTxt').val()){
				alert('미납내역을 입력하여 주십시오.');
				$('#txtTxt').focus();
				return;
			}

			if (!$('#txtAmt').val()){
				alert('미납금액을 입력하여 주십시오.');
				$('#txtAmt').focus();
				return;
			}
		}else{
			if (!seq) return;
		}

		if (!seq) seq = '';

		/*
		var memo = '';

		if (IsYn == 'Y' || IsYn == 'M'){
			memo = prompt('남기실 내용을 작성하여 주십시오.','');

			if (memo == null) memo = '';
		}
		*/

		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_stopset_save.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'seq'	:seq
			,	'gbn'	:$('input:radio[name="optGbn"]:checked').val()
			,	'stopDt':$('#txtDate').val()
			,	'defTxt':$('#txtTxt').val()
			,	'defAmt':$('#txtAmt').val()
			,	'clsDt'	:$('#txtClsDt').val()
			,	'other'	:$('#txtOther').val()
			,	'clsYn'	:IsYn
			,	'memo'	:$('#txtMemo').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					lfInit();
					lfSearch();
				}else{
					alert(result);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfInit(){
		$('#txtDate').val('');
		$('#txtTxt').val('');
		$('#txtAmt').val('');
		$('#txtClsDt').val('');
		$('#txtSeq').val('');
		$('#txtMemo').val('');
		$('#txtOther').val('');
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="200px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">팝업일자</th>
			<td>
				<input id="txtDate" type="text" class="date" value="<?=Date('Y-m-d');?>" onchange="$('#txtClsDt').val(addDate('d', 6, $(this).val()));">
				<label><input name="optGbn" type="radio" class="radio" value="1">중지</label>
				<label><input name="optGbn" type="radio" class="radio" value="2" checked>미납</label>
			</td>
			<th class="center">중지일자</th>
			<td>
				<input id="txtClsDt" type="text" class="date" value="<?=$myF->dateAdd('day', 6, Date('Y-m-d'), 'Y-m-d');?>">
			</td>
			<td class="left" rowspan="2">
				<span class="btn_pack m"><button onclick="if($('#txtSeq').val()){lfStopSet('M',$('#txtSeq').val());}else{lfStopSet('N');}">적용</button></span>
				<span class="btn_pack m"><button onclick="lfInit();">신규</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">미납내역</th>
			<td>
				<input id="txtTxt" type="text" style="width:100%;">
			</td>
			<th class="center">미납금액</th>
			<td>
				<input id="txtAmt" type="text" class="number" style="width:70px;">
			</td>
		</tr>
		<tr>
			<th class="center">메모</th>
			<td colspan="4">
				<input id="txtMemo" type="text" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">비고</th>
			<td colspan="4">
				<textarea id="txtOther" style="width:100%; height:35px;"></textarea>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="70px">
	<col width="70px">
	<col width="70px">
	<col width="60px">
	<col width="70px">
	<col width="30px">
	<col>';?>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">팝업일자</th>
			<th class="head">구분</th>
			<th class="head">미납내역</th>
			<th class="head">미납금액</th>
			<th class="head">상태</th>
			<th class="head">중지일자</th>
			<th class="head">메모</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_DIV_LIST" style="overflow-x:hidden; overflow-y:scroll; height:205px;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>

<div class="center" style="padding-top:5px; border-top:1px solid #CCCCCC;">
	<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
</div>

<input id="txtSeq" type="hidden">
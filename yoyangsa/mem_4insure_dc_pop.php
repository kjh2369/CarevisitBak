<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.seq = '';

		$('#divList').height(__GetHeight($('#divList')));
		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./mem_4insure_dc_q.php'
		,	data:{
				'jumin'	:opener.jumin
			,	'mode'	:'2'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('tbody',$('table',$('#divList'))).html(html);

				/*
				var obj = $('tr:first',$('tbody',$('table',$('#divList'))));

				opener.seq = $(obj).attr('seq');

				$('#optInsure'+$(obj).attr('gbn')).attr('checked',true);
				$('#txtVal').val($(obj).attr('val'));
				$('#txtFrom').val($(obj).attr('from'));
				$('#txtTo').val($(obj).attr('to'));
				*/
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		if (!$('#txtFrom').val() || !$('#txtTo').val()){
			alert('적용기간을 입력하여 주십시오.');
			if (!$('#txtFrom').val()){
				$('#txtFrom').focus();
			}else{
				$('#txtTo').focus();
			}
			return;
		}

		if ($('#txtFrom').val() > $('#txtTo').val()){
			alert('적용기간 입력오류입니다. 확인하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./mem_4insure_dc_q.php'
		,	data:{
				'jumin'	:opener.jumin
			,	'seq'	:''
			,	'gbn'	:$('input:radio[name="optInsure"]:checked').val()
			,	'val'	:$('#txtVal').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'mode'	:'1'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('#txtVal').val('');
					$('#txtFrom').val('');
					$('#txtTo').val('');
					lfSearch();
				}else if (result == 7){
					alert('입력하신 기간이 중복됩니다. 확인하여 주십시오.');
				}else if (result == 9){
					alert('처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(seq){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./mem_4insure_dc_q.php'
		,	data:{
				'jumin'	:opener.jumin
			,	'seq'	:seq
			,	'mode'	:'3'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 9){
					alert('처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfClose(){
		self.close();
	}
</script>
<div class="title title_border">보험감액 적용</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="65px">
		<col width="360px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>보험</th>
			<td colspan="2">
				<label style="display:none;"><input id="optInsure01" name="optInsure" type="radio" class="radio" value="01">국민연금</label>
				<label style="display:none;"><input id="optInsure02" name="optInsure" type="radio" class="radio" value="02">건강보험</label>
				<label><input id="optInsure03" name="optInsure" type="radio" class="radio" value="03" checked>장기요양보험</label>
				<label style="display:none;"><input id="optInsure04" name="optInsure" type="radio" class="radio" value="04">고용보험</label>
			</td>
		</tr>
		<tr>
			<th>감액률</th>
			<td><input id="txtVal" type="text" value="" class="number" style="width:30px;" maxlength="3">%</td>
			<td class="center" rowspan="2">
				<span class="btn_pack m"><button onclick="lfApply();">적용</button></span><br>
				<span class="btn_pack m"><button onclick="lfClose();">닫기</button></span>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="txtFrom" type="text" value="" class="yymm"> ~
				<input id="txtTo" type="text" value="" class="yymm">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="60px">
		<col width="110px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">보험</th>
			<th class="head">감액률</th>
			<th class="head">적용기간</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div id="divList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="100px">
			<col width="60px">
			<col width="110px">
			<col>
		</colgroup>
		<tbody></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
?>
<base target="_self">
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		__init_form(document.f);

		setTimeout('lfLoadHis()',200);
	});

	function lfLoadHis(){
		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_monthly_list.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			}
		,	success: function(data){
				if (!data) return;
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr';
						html += ' yymm="'+col['ym']+'"';
						html += ' pay="'+col['pay']+'"';
						html += '>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['ym'].substring(0,4)+'.'+col['ym'].substring(4)+'</td>';
						html += '<td class="center"><div class="right">'+__num2str(col['pay'])+'</div></td>';
						html += '<td class="center"><div class="left">';
						html += '<span class="btn_pack m"><a href="#" onclick="lfDelete(\''+col['ym']+'\'); return false;">삭제</a></span>';
						html += '</div></td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);

				var obj = $('#tbodyList tr:first');

				opener.yymm	= $(obj).attr('yymm').substring(0,4)+'-'+$(obj).attr('yymm').substring(4);
				opener.pay	= __num2str($(obj).attr('pay'));

				$('#txtMonthly').val(__str2num(opener.pay));
				$('#txtYYMM').val(opener.yymm);
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfApply(){
		if (__str2num($('#txtMonthly').val()) <= 0){
			 alert('금액을 입력하여 주십시오.');
			 $('#txtMonthly').focus();
			 return;
		}

		if (!$('#txtYYMM').val()){
			 alert('적용년월을 입력하여 주십시오.');
			 $('#txtYYMM').focus();
			 return;
		}

		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_monthly_reg.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			,	'pay':__str2num($('#txtMonthly').val())
			,	'ym':$('#txtYYMM').val().split('-').join('')
			}
		,	success: function(result){
				if (result == '9'){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result){
					alert(result);
				}else{
					lfLoadHis();
				}
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfDelete(yymm){
		if (!confirm('삭제 후 북구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_monthly_del.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			,	'ym':yymm
			}
		,	success: function(result){
				if (result == '9'){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result){
					alert(resutl);
				}else{
					lfLoadHis();
				}
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<form id="f" name="f" method="post">
<div class="title title_border">급여보수신고급여변경</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="130px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>금액</th>
			<td>
				<input id="txtMonthly" name="txt" type="text" value="0" class="number" style="width:70px;">
			</td>
			<td class="left" rowspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span><br>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
			</td>
		</tr>
		<tr>
			<th>적용년월</th>
			<td>
				<input id="txtYYMM" name="txt" type="text" value="" orgVal="" class="yymm">
			</td>
		</tr>
	</tbody>
</table>
<div class="title title_border">변경이력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">적용년월</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="20">
				<div style="overflow-x:hidden; overflow-y:scroll; height:286px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="30px">
							<col width="70px" span="2">
							<col>
						</colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="txtSeq" type="hidden" value="0">
</form>
<?
	include_once('../inc/_footer.php');
?>
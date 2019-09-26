<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $ed->de($_POST['orgNo']);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#ID_LIST').height(__GetHeight($('#ID_LIST')));
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
		lfBillChk();
	});

	window.onunload = function(){
		opener.lfBillChangeSearch();
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_bill_change_search.php'
		,	data:{
				'orgNo':'<?=$ed->en($orgNo)?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_LIST tbody').html(html);
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfApply(){
		if ($('input:radio[name="otpBillGbn"]:checked').val() == '1'){
			if (!$('#txtCmsNo').val()){
				alert('CMS 번호를 입력하여 주십시오.');
				$('#txtCmsNo').focus();
				return;
			}

			if (!$('#cboCmsCom').val()){
				alert('CMS 회사를 입력하여 주십시오.');
				$('#cboCmsCom').focus();
				return;
			}
		}

		$.ajax({
			type:'POST'
		,	url:'./center_bill_change_apply.php'
		,	data:{
				'orgNo':'<?=$ed->en($orgNo)?>'
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'billGbn':$('input:radio[name="otpBillGbn"]:checked').val()
			,	'bill_kind':$('input:radio[name="otpBillKind"]:checked').val()
			,	'cmsno':$('#txtCmsNo').val()
			,	'cmsCom':$('#cboCmsCom').val()
			,	'orgFromDt':$('#txtFromDt').attr('orgFromDt') ? $('#txtFromDt').attr('orgFromDt') : ''
			,	'orgBillGbn':$('#txtFromDt').attr('orgBillGbn') ? $('#txtFromDt').attr('orgBillGbn') : ''
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
					lfBillChk();
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

	function lfModify(obj){
		$('#txtFromDt').attr('orgFromDt', $(obj).attr('fromDt')).attr('orgBillGbn', $(obj).attr('billGbn')).val(__getDate($(obj).attr('fromDt')));
		$('#txtToDt').val(__getDate($(obj).attr('toDt')));
		$('#txtCmsNo').val($(obj).attr('cmsno'));
		$('#cboCmsCom').val($(obj).attr('cmsCom'));
		$('input:radio[name="otpBillGbn"][value="'+$(obj).attr('billGbn')+'"]').attr('checked', true);
		$('input:radio[name="otpBillKind"][value="'+$(obj).attr('bill_kind')+'"]').attr('checked', true);

		lfBillChk();
	}

	function lfDelete(obj){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_bill_change_delete.php'
		,	data:{
				'orgNo':'<?=$ed->en($orgNo)?>'
			,	'fromDt':$(obj).attr('fromDt')
			,	'billGbn':$(obj).attr('billGbn')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
					lfBillChk();
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

	function lfBillChk(){
		if ($('input:radio[name="otpBillGbn"]:checked').val() == '1'){
			$('#txtCmsNo').attr('disabled', false);
			$('#cboCmsCom').attr('disabled', false);

			if (!$('#cboCmsCom').val()) $('#cboCmsCom').val('3');
		}else{
			$('#txtCmsNo').attr('disabled', true);
			$('#cboCmsCom').attr('disabled', true);
		}
	}

	function lfInit(){
		var date = new Date();
		var today = date.getFullYear()+'-'+(date.getMonth()+1 < 10 ? '0' : '')+(date.getMonth()+1)+'-'+(date.getDate() < 10 ? '0' : '')+date.getDate();


		$('#txtFromDt').val(today);
		$('#txtToDt').val('9999-12-31');
		$('#txtCmsNo').val('');
		$('#cboCmsCom').val('');
		$('input:radio[name="otpBillGbn"][value="2"]').attr('checked', true);

		lfBillChk();
	}
</script>
<div class="title title_border">청구정보 변경</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">적용일자</th>
			<td><input id="txtFromDt" type="text" class="date"></td>
			<th class="center">종료일자</th>
			<td><input id="txtToDt" type="text" class="date"></td>
			<td class="left" rowspan="3">
				<span class="btn_pack m"><button onclick="lfInit();">신규</button></span>
				<span class="btn_pack m"><button onclick="lfApply();">적용</button></span>
				<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">청구구분</th>
			<td>
				<label><input name="otpBillGbn" type="radio" value="1" class="radio" onclick="lfBillChk();" checked>CMS</label>
				<label><input name="otpBillGbn" type="radio" value="2" class="radio" onclick="lfBillChk();">무통장</label>
			</td>
			<th class="center">선후불구분</th>
			<td>
				<label><input name="otpBillKind" type="radio" value="1" class="radio" onclick="">선불</label>
				<label><input name="otpBillKind" type="radio" value="2" class="radio" onclick="" checked>후불</label>
			</td>
		</tr>
		<tr>
			<th class="center">CMS 번호</th>
			<td><input id="txtCmsNo" type="text"></td>
			<th class="center">CMS 회사</th>
			<td>
				<select id="cboCmsCom" style="width:auto;">
					<option value="">-</option>
					<option value="3">케어비지트</option>
					<option value="1">굿이오스</option>
					<option value="2">지케어</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:671px;">
	<colgroup>
		<col width="80px" span="2">
		<col width="70px">
		<col width="60px">
		<col width="150px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold" colspan="7">- 변경이력</th>
		</tr>
		<tr>
			<th class="center">적용일자</th>
			<th class="center">종료일자</th>
			<th class="center">청구구분</th>
			<th class="center">선/후불</th>
			<th class="center">CMS 번호</th>
			<th class="center">CMS 회사</th>
			<th class="center">비고</th>
		</tr>
	</tbody>
</table>
<div id="ID_LIST" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
	<table class="my_table" style="width:671px;">
		<colgroup>
			<col width="80px" span="2">
			<col width="70px">
			<col width="60px">
			<col width="150px">
			<col width="100px">
			<col>
		</colgroup>
		<tbody></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>
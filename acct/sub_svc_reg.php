<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		if (opener.svcCd == '5'){
			$('#cobSvcCd').append('<option value="5">주야간보호</option>');
		}else if (opener.svcCd == '7'){
			$('#cobSvcCd').append('<option value="7">복지용구</option>');
		}

		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url	:'./sub_svc_reg_search.php'
		,	data:{
				'code'	:opener.code
			,	'svcCd'	:opener.svcCd
			,	'seq'	:opener.seq
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#btnFindCenter').hide();
				$('#lblCode').text(opener.code);
				$('#lblName').text(col['name']);
				$('#cobSvcCd option[value!="'+opener.svcCd+'"]').remove();
				$('input:radio[name="optAcct"][value="'+col['acctYn']+'"]').attr('checked',true);
				$('#txtFrom').val(col['fromDt']);
				$('#txtTo').val(col['toDt']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfFindCenter(){
		var objModal= new Object();
		var url		= '../find/_find_center.php';
		var style	= 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '99';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$('#lblCode').text(objModal.code);
		$('#lblName').text(objModal.name);
	}

	function lfApply(){
		if (!checkDate($('#txtFrom').val())){
			alert('적용일자를 입력하여 주십시오.');
			$('#txtFrom').focus();
			return;
		}

		if (!checkDate($('#txtTo').val())){
			alert('종료일자를 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		if ($('#txtFrom').val() > $('#txtTo').val()){
			alert('적용기간 입력오류입니다. 확인 후 다시 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url	:'./sub_svc_reg_save.php'
		,	data:{
				'code'	:$('#lblCode').text()
			,	'svcCd'	:$('#cobSvcCd').val()
			,	'seq'	:opener.seq
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'acctYn':$('input:radio[name="optAcct"]:checked').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					opener.result	= true;
					opener.code		= $('#lblCode').text();
					opener.name		= $('#lblName').text();
					opener.svcCd	= $('#cobSvcCd').val();
					opener.seq		= (opener.seq ? opener.seq : '1');
					opener.fromDt	= $('#txtFrom').val();
					opener.toDt		= $('#txtTo').val();
					opener.acctYn	= $('input:radio[name="optAcct"]:checked').val();
					self.close();
				}else if (result == 7){
					alert('적용기간이 중복됩니다. 확인 후 다시 입력하여 주십시오.');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfClose(){
		opener.result = false;
		self.close();
	}
</script>
<div class="title title_border">주야간보호 기관등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관코드</th>
			<td class="left">
				<div id="btnFindCenter" style="float:left; width:auto; height:25px;"><span class="btn_pack find" onclick="lfFindCenter();"></span></div>
				<div style="float:left; width:auto;"><span id="lblCode" class="bold"></span></div>
			</td>
		</tr>
		<tr>
			<th>기관명</th>
			<td class="left" id="lblName"></td>
		</tr>
		<tr>
			<th>서비스</th>
			<td>
				<select id="cobSvcCd" style="width:auto;">
					<option value="">-서비스를 선택하여 주십시오.-</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>과금</th>
			<td>
				<label><input id="optAcctY" name="optAcct" type="radio" class="radio" value="Y" checked>예</label>
				<label><input id="optAcctN" name="optAcct" type="radio" class="radio" value="N">아니오</label>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td>
				<input id="txtFrom" type="text" value="" class="date"> ~
				<input id="txtTo" type="text" value="" class="date">
			</td>
		</tr>
		<tr>
			<td class="center bottom last" colspan="2">
				<span class="btn_pack m"><button onclick="lfApply();">저장</button></span>
				<span class="btn_pack m"><button onclick="lfClose();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>
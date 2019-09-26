<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		__init_form(document.f);

		$('input:text[name="txtCd"]').unbind('click').bind('click',function(){
			if ($(this).attr('readonly')){
				var id = $(this).attr('id').substring(0,3);

				$('#'+id+'Nm').focus();
			}
		}).unbind('keyup').bind('keyup',function(){
			var id	= $(this).attr('id').substring(0,3);
			var cd	= $(this).val().toUpperCase();
			var obj	= $('div',$('#div'+id));

			if ($(this).val().length == $(this).attr('maxlength')){
				$(obj).each(function(){
					var code = $('#code',this).text();

					if (code == cd){
						$(this).click();
						return false;
					}
				});

				lfUpper(this);

				$('#'+id+'Nm').focus();
			}
		}).unbind('blur').bind('blur',function(){
			if ($(this).val().length != $(this).attr('maxlength')){
				$(this).val('');
			}
		});;

		setTimeout('lfLoad(\'MST\')',200);
	});

	function lfResize(){
		var h = $(document).height();
		var t = $('#gbnM').offset().top;
		var height = h - t - 230;

		$('#gbnM').height(height);
		$('#gbnS').height(height);
		$('#gbnD').height(height);
	}

	//대분류 코드조회
	function lfLoad(type){
		var html = '';
		var mstCd = $('#mstCd').val();
		var proCd = $('#proCd').val();

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'CARE_SUGA_FIND'
			,	'type':type
			,	'mstCd':mstCd
			,	'proCd':proCd
			,	'SR':opener.SR
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);
						var border = 'border-top:1px solid #cccccc;';

						if (i == 0){
							border = '';
						}

						html += '<div';
						html += ' class="bold"';
						html += ' style="width:auto; '+border+'"';
						html += ' onmouseover="this.style.color=\'#0000ff\';"';
						html += ' onmouseout="this.style.color=\'#000000\';"';
						html += ' onclick="lfInData(\''+type.toLowerCase()+'\',this);"';
						html += '>';
						html += '<span id="code">'+col['code']+'</span>.';
						html += '<span id="name" style="margin-left:3px;">'+col['name']+'</span>';

						if (type == 'SVC'){
							html += '<span id="seq" style="display:none;">'+col['seq']+'</span>';
							html += '<span id="cost" style="display:none;">'+col['cost']+'</span>';
							html += '<span id="from" style="display:none;">'+col['from']+'</span>';
							html += '<span id="to" style="display:none;">'+col['to']+'</span>';
						}

						html += '</div>';
					}
				}

				$('#div'+type).html(html);
				$('#div'+type+' div:first').click();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//신규
	function lfNew(type,focus){
		if (focus){
			focus = false;
		}else{
			focus = true;
		}

		$('#'+type+'Cd').val('').attr('readonly',false).css('background-color','#f9f3c0');
		$('#'+type+'Cd').parent().css('background-color','#f9f3c0');
		$('#'+type+'Nm').val('');

		if (focus){
			$('#'+type+'Cd').focus();
		}

		if (type == 'mst'){
			type = 'PRO';
		}else if (type == 'pro'){
			type = 'SVC';
		}else{
			return;
		}

		lfLoad(type);
		lfNew(type.toLowerCase(),true);
	}

	//수정
	function lfInData(type,obj){
		var code = $('#code',obj).text();
		var name = $('#name',obj).text();

		$('#'+type+'Cd').val(code).attr('readonly',true).css('background-color','#ffffff');
		$('#'+type+'Cd').parent().css('background-color','#ffffff');
		$('#'+type+'Nm').val(name).focus();

		if (type == 'mst'){
			lfLoad('PRO');
		}else if (type == 'pro'){
			lfLoad('SVC');
		}else{
			//이력내역
			lfHisList();
		}
	}

	//이력내역
	function lfHisList(){
		var html = '';
		var mstCd = $('#mstCd').val();
		var proCd = $('#proCd').val();
		var svcCd = $('#svcCd').val();
		//tbodyList

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'CARE_SUGA_HIS'
			,	'mstCd':mstCd
			,	'proCd':proCd
			,	'svcCd':svcCd
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr';
						html += ' seq="'		+col['seq']+'"';
						html += ' code="'		+col['code']+'"';
						html += ' cost="'		+col['cost']+'"';
						html += ' from="'		+col['from']+'"';
						html += ' to="'			+col['to']+'"';
						html += '>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:100px;">'+col['mst']+'</div></td>';
						html += '<td class="center"><div class="left nowrap" style="width:100px;">'+col['pro']+'</div></td>';
						html += '<td class="center"><div class="left nowrap" style="width:100px;">'+col['svc']+'</div></td>';
						html += '<td class="center"><div class="right">'+col['cost']+'</div></td>';
						html += '<td class="center">'+__getDate(col['from'],'.')+'</td>';
						html += '<td class="center">'+__getDate(col['to'],'.')+'</td>';
						html += '<td class="center">';

						if (i == 0){
							html += '<div class="left"><span class="btn_pack m"><button id="btnDel" type="button" onclick="lfDelete($(this).parent().parent().parent().parent());">삭제</button></span></div>';
						}

						html += '</td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					html = '<tr><td class="center" colsapn="20">::데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);

				if ($('#tbodyList tr').length > 0){
					if (no <= 2){
						$('#btnDel').attr('disabled',true);
					}

					var from = __getDate($('#tbodyList tr:first').attr('from'));
					var to	 = __getDate($('#tbodyList tr:first').attr('to'));

					$('#txtSeq').val($('#tbodyList tr:first').attr('seq'));
					$('#txtCost').val($('#tbodyList tr:first').attr('cost'));
					$('#txtFromDt').attr('orgVal',from).val(from);
					$('#txtToDt').attr('orgVal',to).val(to);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//적용
	function lfApply(type){
		if (type == 'mst'){
			if (!$('#mstCd').val()){
				alert('대분류 코드를 입력하여 주십시오');
				$('#mstCd').focus();
				return;
			}

			if (!$('#mstNm').val()){
				alert('대분류 명칭를 입력하여 주십시오');
				$('#mstNm').focus();
				return;
			}

		}else if (type == 'pro'){
			if (!$('#mstCd').val()){
				alert('대분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#proCd').val()){
				alert('중분류 코드를 입력하여 주십시오');
				$('#proCd').focus();
				return;
			}

			if (!$('#proNm').val()){
				alert('중분류 명칭를 입력하여 주십시오');
				$('#proNm').focus();
				return;
			}

		}else if (type == 'svc'){
			if (!$('#mstCd').val()){
				alert('대분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#proCd').val()){
				alert('중분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#svcCd').val()){
				alert('소분류 코드를 입력하여 주십시오');
				$('#svcCd').focus();
				return;
			}

			if (!$('#svcNm').val()){
				alert('소분류 명칭를 입력하여 주십시오');
				$('#svcNm').focus();
				return;
			}

		}else if (type == 'other'){
			if (!$('#mstCd').val()){
				alert('대분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#proCd').val()){
				alert('중분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#svcCd').val()){
				alert('소분류 선택을 선행하여 주십시오.');
				return;
			}

			if (!$('#txtFromDt').val()){
				alert('적용일을 입력하여 주십시오.');
				$('#txtFromDt').focus();
				return;
			}

			if (!$('#txtToDt').val()){
				alert('종료일을 입력하여 주십시오.');
				$('#txtFromDt').focus();
				return;
			}

			if ($('#txtFromDt').val() > $('#txtToDt').val()){
				alert('적용일이 종료일보다 큽니다. 확인 후 다시 입력하여 주십시오.');
				$('#txtFromDt').focus();
				return;
			}
		}

		var data = {};

		if (type == 'mst'){
			data = {
				'mode':'CARE_SUGA_APPLY'
			,	'type':type
			,	'mstCd':$('#mstCd').val()
			,	'mstNm':$('#mstNm').val()
			};
		}else if (type == 'pro'){
			data = {
				'mode':'CARE_SUGA_APPLY'
			,	'type':type
			,	'mstCd':$('#mstCd').val()
			,	'proCd':$('#proCd').val()
			,	'proNm':$('#proNm').val()
			};
		}else if (type == 'svc'){
			data = {
				'mode':'CARE_SUGA_APPLY'
			,	'type':type
			,	'mstCd':$('#mstCd').val()
			,	'proCd':$('#proCd').val()
			,	'svcCd':$('#svcCd').val()
			,	'svcNm':$('#svcNm').val()
			};
		}else if (type == 'other'){
			data = {
				'mode':'CARE_SUGA_APPLY'
			,	'type':type
			,	'mstCd':$('#mstCd').val()
			,	'proCd':$('#proCd').val()
			,	'svcCd':$('#svcCd').val()
			,	'cost':$('#txtCost').val()
			,	'seq':$('#txtSeq').val()
			,	'from':$('#txtFromDt').val()
			,	'to':$('#txtToDt').val()
			,	'reYn':$('#chkReReg').attr('checked') ? 'Y' : 'N'
			};
		}else{
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./apply.php'
		,	data :data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정삭적으로 처리되었습니다.');

					if (type == 'other'){
						lfHisList();
					}else{
						lfLoad(type.toUpperCase());
					}

					opener.result = 1;
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result == 91){
					alert('대분류명을 선입력하여 주십시오.');
				}else if (result == 92){
					alert('중분류명을 선입력하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//삭제
	function lfDelete(obj){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./apply.php'
		,	data :{
				'mode':'CARE_SUGA_DELETE'
			,	'code':$(obj).attr('code')
			,	'seq':$(obj).attr('seq')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfHisList();
					opener.result = 1;
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//대문자로 변환
	function lfUpper(obj){
		var val = $(obj).val().toUpperCase();

		$(obj).val(val);
	}

	function lfRePeriod(){
		var chk = $('#chkReReg').attr('checked');

		try{
			if (chk){
				var dt = $('#txtToDt').attr('orgVal');
					dt = addDate('d',1,dt);

				$('#txtFromDt').val(dt);
				$('#txtToDt').val('');
			}else{
				$('#txtFromDt').val($('#txtFromDt').attr('orgVal'));
				$('#txtToDt').val($('#txtToDt').attr('orgVal'));
			}
		}catch(e){
			$('#txtFromDt').val($('#txtFromDt').attr('orgVal'));
			$('#txtToDt').val($('#txtToDt').attr('orgVal'));
			$('#chkReReg').attr('checked',false);
			alert('종료일을 수정하여 주십시오.');
			$('#txtToDt').focus();
		}
	}
</script>

<form id="f" name="f" method="post">
<div class="title title_border">서비스 등록 및 수정</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="400px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top" style="border-right:2px solid #0e69b0;">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="30px">
						<col width="30px">
						<col width="15px">
						<col width="30px">
						<col width="160px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center" rowspan="2">대<br>분<br>류</th>
							<th class="center">코드</th>
							<td class="center">
								<input id="mstCd" name="txtCd" type="text" value="" style="ime-mode:disabled; width:20px; text-align:center; border:none;" maxlength="1">
							</td>
							<th class="center">명칭</th>
							<td>
								<input id="mstNm" name="txt" type="text" value="" style="width:100%;">
							</td>
							<td class="left last">
								<span class="btn_pack m"><button type="button" onclick="lfNew('mst');">신규</button></span>
								<span class="btn_pack m"><button type="button" onclick="lfApply('mst');">저장</button></span>
							</td>
						</tr>
						<tr>
							<td class="last" colspan="5">
								<div id="divMST" style="height:100px; overflow-x:hidden; overflow-y:scroll; padding:5px; line-height:1.7em;"></div>
							</td>
						</tr>

						<tr>
							<th class="center" style="border-bottom:2px solid #0e69b0;" rowspan="2">중<br>분<br>류</th>
							<th class="center">코드</th>
							<td class="center">
								<input id="proCd" name="txtCd" type="text" value="" style="ime-mode:disabled; width:20px; text-align:center; border:none;" maxlength="2" onkeyup="lfUpper(this);">
							</td>
							<th class="center">명칭</th>
							<td>
								<input id="proNm" name="txt" type="text" value="" style="width:100%;">
							</td>
							<td class="left last">
								<span class="btn_pack m"><button type="button" onclick="lfNew('pro');">신규</button></span>
								<span class="btn_pack m"><button type="button" onclick="lfApply('pro');">저장</button></span>
							</td>
						</tr>
						<tr>
							<td class="last" style="border-bottom:2px solid #0e69b0;" colspan="5">
								<div id="divPRO" style="height:100px; overflow-x:hidden; overflow-y:scroll; padding:5px; line-height:1.7em;"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="center top">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="30px">
						<col width="30px">
						<col width="15px">
						<col width="30px">
						<col width="160px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center" style="border-bottom:2px solid #0e69b0;" rowspan="2">소<br>분<br>류</th>
							<th class="center">코드</th>
							<td class="center">
								<input id="svcCd" name="txtCd" type="text" value="" style="ime-mode:disabled; width:20px; text-align:center; border:none;" maxlength="2" onkeyup="lfUpper(this);">
							</td>
							<th class="center">명칭</th>
							<td>
								<input id="svcNm" name="txt" type="text" value="" style="width:100%;">
							</td>
							<td class="left last">
								<span class="btn_pack m"><button type="button" onclick="lfNew('svc');">신규</button></span>
								<span class="btn_pack m"><button type="button" onclick="lfApply('svc');">저장</button></span>
							</td>
						</tr>
						<tr>
							<td class="last" style="border-bottom:2px solid #0e69b0;" colspan="5">
								<div id="divSVC" style="height:227px; overflow-x:hidden; overflow-y:scroll; padding:5px; line-height:1.7em;"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">적용단가</th>
							<td class="bold last" colspan="5">
								<input id="txtCost" name="txt" type="text" value="0" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="center bottom">적용기간</th>
							<td class="bottom last" colspan="5">
								<input id="txtFromDt" name="txtDt" type="text" value="" orgVal="" class="date"> ~
								<input id="txtToDt" name="txtDt" type="text" value="" orgVal="" class="date">
								<input id="chkReReg" name="chk" type="checkbox" class="checkbox" value="Y" onclick="lfRePeriod();"><label id="lblReReg" for="chkReReg">재등록</label>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="top" style="padding:5px;">
				<div>
					<span class="btn_pack m"><button type="button" onclick="lfApply('other');">적용</button></span>
					<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
				</div>
				<div class="bold">
					※ <span style="color:blue;">적용일</span>과 <span style="color:blue;">종료일</span>은 <span style="color:blue;">월단위</span>로 입력하여 주십시오.
				</div>
			</td>
		</tr>
		<tr>
			<td class="bottom last" colspan="2">
				<div class="title title_border">이력내역</div>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="100px" span="3">
						<col width="70px">
						<col width="70px" span="2">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">대분류</th>
							<th class="head">중분류</th>
							<th class="head">소분류</th>
							<th class="head">단가</th>
							<th class="head">적용일</th>
							<th class="head">종료일</th>
							<th class="head">비고</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="20">
								<div style="height:155px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup>
											<col width="40px">
											<col width="100px" span="3">
											<col width="70px">
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
			</td>
		</tr>
	</tbody>
</table>
<input id="txtSeq" type="hidden" value="">
</form>

<?
	include_once('../inc/_footer.php');
?>
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

		$('input:radio').unbind('click').bind('click',function(){
			var id = $(this).attr('id');

			if (id == 'optPY'){
				if ($('#optAY').attr('checked') ||
					$('#optHY').attr('checked') ||
					$('#optEY').attr('checked') ||
					$('#optSY').attr('checked')){
					alert('4대보험에 가입된 대상자는 원천징수 대상자가 될 수 없습니다.');
					$('#optPN').attr('checked',true);
				}
			}else{
				if (id == 'optAY' ||
					id == 'optHY' ||
					id == 'optEY' ||
					id == 'optSY'){
					if ($('#optPY').attr('checked')){
						alert('4대보험에 가입된 대상자는 원천징수 대상자가 될 수 없습니다.');
						$('#optPN').attr('checked',true);
					}
				}
			}
		});

		$('#chkReReg').unbind('click').bind('click',function(){
			if ($(this).attr('checked')){
				try{
					var from= __addDate('d',1,$('#txtToDt').val());
					var to	= __addDate('d',-1,__addDate('yyyy',1,from));

					$('#txtFromDt').val(from);
					$('#txtToDt').val(to);

					$('#statF').val('9');
					$('#statT').val('9');

					lfSetDtObj($('#txtFromDt'),$('#statF').val());
					lfSetDtObj($('#txtToDt'),$('#statT').val());
				}catch(e){
					alert('재등록 처리할 수 없습니다.');

					var from	= __getDate($('#txtFromDt').attr('orgVal'));
					var to		= __getDate($('#txtToDt').attr('orgVal'));
					var statF	= $('#statF').attr('orgVal');
					var statT	= $('#statT').attr('orgVal');

					$('#txtFromDt').val(from);
					$('#txtToDt').val(to);
					$('#chkReReg').attr('checked',false);

					lfSetDtObj($('#txtFromDt'),$('#statF').attr('orgVal'));
					lfSetDtObj($('#txtToDt'),$('#statT').attr('orgVal'));
				}
			}else{
				var from	= __getDate($('#txtFromDt').attr('orgVal'));
				var to		= __getDate($('#txtToDt').attr('orgVal'));
				var statF	= $('#statF').attr('orgVal');
				var statT	= $('#statT').attr('orgVal');

				$('#txtFromDt').val(from);
				$('#txtToDt').val(to);

				lfSetDtObj($('#txtFromDt'),$('#statF').attr('orgVal'));
				lfSetDtObj($('#txtToDt'),$('#statT').attr('orgVal'));
			}
		});

		setTimeout('__init_form(document.f)',200);
		setTimeout('lfLoadHis()',300);
	});

	function lfLoadHis(){
		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_list.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr';
						html += ' seq="'+col['seq']+'"';
						html += ' from="'+col['from']+'"';
						html += ' to="'+col['to']+'"';
						html += ' a="'+col['a']+'"';
						html += ' h="'+col['h']+'"';
						html += ' e="'+col['e']+'"';
						html += ' s="'+col['s']+'"';
						html += ' monthly="'+col['pay']+'"';
						html += ' p="'+col['p']+'"';
						html += ' statF="'+col['statF']+'"';
						html += ' statT="'+col['statT']+'"';
						html += '>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['a']+'</td>';
						html += '<td class="center">'+col['h']+'</td>';
						html += '<td class="center">'+(col['e'] == 'O' ? '사': col['e'])+'</td>';
						html += '<td class="center">'+col['s']+'</td>';
						html += '<td class="center">'+col['p']+'</td>';
						html += '<td class="center">'+__getDate(col['from'],'.')+'~'+__getDate(col['to'],'.')+'</td>';
						html += '<td class="center">';

						if (no == 1){
							html += '<div class="left"><span class="btn_pack m"><button type="button" onclick="lfDelete();">삭제</button></span></div>';
						}
						html += '</td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					 html = '<tr><td class="center" colspan="10">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);

				var tr = $('#tbodyList tr:first');

				opener.aYn = ($(tr).attr('a') ? $(tr).attr('a') : 'N');
				opener.hYn = ($(tr).attr('h') ? $(tr).attr('h') : 'N');
				opener.eYn = ($(tr).attr('e') ? $(tr).attr('e') : 'N');
				opener.sYn = ($(tr).attr('s') ? $(tr).attr('s') : 'N');
				opener.pYn = ($(tr).attr('p') ? $(tr).attr('p') : 'N');
				opener.monthly = __str2num($(tr).attr('monthly'));
				opener.from = $(tr).attr('from');
				opener.to = $(tr).attr('to');
				opener.seq = $(tr).attr('seq');

				var statF = $(tr).attr('statF');
				var statT = $(tr).attr('statT');

				$('#optA'+opener.aYn).attr('checked',true);
				$('#optH'+opener.hYn).attr('checked',true);
				$('#optE'+opener.eYn).attr('checked',true);
				$('#optS'+opener.sYn).attr('checked',true);
				$('#optP'+opener.pYn).attr('checked',true);

				try{
					$('#txtFromDt').attr('orgVal',opener.from).val(__getDate(opener.from));
					$('#txtToDt').attr('orgVal',opener.to).val(__getDate(opener.to));

					$('#statF').attr('orgVal',statF).val(statF);
					$('#statT').attr('orgVal',statT).val(statT);

					lfSetDtObj($('#txtFromDt'),statF);
					lfSetDtObj($('#txtToDt'),statT);
				}catch(e){
					$('#chkReReg').hide();
					$('label[for="chkReReg"]').hide();
				}

				$('#txtSeq').val(opener.seq);
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfSetDtObj(obj,stat){
		if (stat == '1'){
			$(obj).css('background-color','#efefef').attr('readonly',true);
		}else{
			$(obj).css('background-color','#ffffff').attr('readonly',false);
		}
	}

	function lfApply(){
		if (!$('#txtFromDt').val()){
			 alert('적용일을 입력하여 주십시오.');
			 $('#txtFromDt').focus();
			 return;
		}

		if (!$('#txtToDt').val()){
			 alert('종료일을 입력하여 주십시오.');
			 $('#txtToDt').focus();
			 return;
		}

		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_reg.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			,	'seq':$('#txtSeq').val()
			,	'a':$('input:radio[name="optA"]:checked').val()
			,	'h':$('input:radio[name="optH"]:checked').val()
			,	'e':$('input:radio[name="optE"]:checked').val()
			,	's':$('input:radio[name="optS"]:checked').val()
			,	'p':$('input:radio[name="optP"]:checked').val()
			,	'from':$('#txtFromDt').val().split('-').join('')
			,	'to':$('#txtToDt').val().split('-').join('')
			,	'reReg':$('#chkReReg').attr('checked') ? 'Y' : 'N'
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
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제 후 북구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type: 'POST'
		,	url : './mem_his_insu_del.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':opener.jumin
			,	'seq':$('#txtSeq').val()
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
<div class="title title_border">보험변경</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="130px">
		<col width="60px">
		<col width="130px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>국민연금</th>
			<td>
				<input id="optAY" name="optA" type="radio" class="radio" value="Y"><label for="optAY">가입</label>
				<input id="optAN" name="optA" type="radio" class="radio" value="N"><label for="optAN">미가입</label>
			</td>
			<th>건강보험</th>
			<td>
				<input id="optHY" name="optH" type="radio" class="radio" value="Y"><label for="optHY">가입</label>
				<input id="optHN" name="optH" type="radio" class="radio" value="N"><label for="optHN">미가입</label>
			</td>
			<td class="center" rowspan="4">
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span><br>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span><?
				if ($debug){?>
					<span class="btn_pack m"><button type="button" onclick="document.f.submit();">ReLoad</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th>고용보험</th>
			<td colspan="3">
				<input id="optEY" name="optE" type="radio" class="radio" value="Y"><label for="optEY">가입</label>
				<input id="optEN" name="optE" type="radio" class="radio" value="N"><label for="optEN">미가입</label>
				<input id="optEO" name="optE" type="radio" class="radio" value="O"><label for="optEO">사업주만</label>
			</td>
			
		</tr>
		<tr>
			<th>산재보험</th>
			<td>
				<input id="optSY" name="optS" type="radio" class="radio" value="Y"><label for="optSY">가입</label>
				<input id="optSN" name="optS" type="radio" class="radio" value="N"><label for="optSN">미가입</label>
			</td>
			<th>원천징수</th>
			<td >
				<input id="optPY" name="optP" type="radio" class="radio" value="Y"><label for="optPY">예</label>
				<input id="optPN" name="optP" type="radio" class="radio" value="N"><label for="optPN">아니오</label>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td colspan="3">
				<input id="txtFromDt" name="txt" type="text" value="" orgVal="" class="date"> ~
				<input id="txtToDt" name="txt" type="text" value="" orgVal="" class="date">
				<input id="statF" type="hidden" value="" orgVal="">
				<input id="statT" type="hidden" value="" orgVal="">
				<input id="chkReReg" name="chk" type="checkbox" class="checkbox" value="Y"><label for="chkReReg">재등록</label>
			</td>
		</tr>
	</tbody>
</table>
<div class="title title_border">변경이력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="30px" span="5">
		<col width="130px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">국</th>
			<th class="head">건</th>
			<th class="head">고</th>
			<th class="head">산</th>
			<th class="head">원</th>
			<th class="head">적용기간</th>
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
							<col width="30px" span="5">
							<col width="130px">
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
<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	/*
	opener.code
	opener.svcCd
	opener.insuCd
	opener.insuSeq
	opener.result
	 */
?>
<script type="text/javascript">
var opener = null;

$(document).ready(function(){
	opener = window.dialogArguments;

	__init_form(document.f);

	var h = $(document).height() - $('#body').offset().top;

	$('#body').height(h);

	lfList();

	$('#chkRe').unbind('click').bind('click',function(){
		if ($(this).attr('checked')){
			$('#fromDt').val($('#toDt').val());
			//$('#toDt').val(addDate('yyyy',1,$('#toDt').val()));
			$('#toDt').val(__addDate('yyyy', 1, $('#toDt').val()));

		}else{
			$('#fromDt').val(opener.fromDt);
			$('#toDt').val(opener.toDt);
		}
	});
});

function lfList(){
	$.ajax({
		type: 'POST'
	,	async:false
	,	url : './insu_list.php'
	,	data: {
			'code':opener.code
		,	'svcCd':opener.svcCd
		}
	,	beforeSend: function (){
		}
	,	success: function (data){
			var html = '';
			var idx  = 1;
			var list = data.split(String.fromCharCode(1));
			var seq    = '';
			var insuCd = '';

			for(var i=0; i<list.length; i++){
				if (list[i]){
					var val = list[i].split(String.fromCharCode(2));
					var del = '';

					if (idx == 1){
						seq = val[0];
						insuCd = val[1];
						del = '<span class="btn_pack m"><button type="button" onclick="lfDelete(this)" seq="'+seq+'" cd="'+insuCd+'">삭제</button></span>';
					}

					html += '<tr>'
						 +  '<td class="center">'+idx+'</td>'
						 +  '<td class="center"><div class="left nowrap" style="width:75px;">'+val[2]+'</div></td>'
						 +  '<td class="center">'+val[3]+'</td>'
						 +  '<td class="center"><div class="right">'+__num2str(val[6])+'</div></td>'
						 +  '<td class="center">'+val[4]+'~'+val[5]+'</td>'
						 +  '<td class="center">'+del+'</td>'
						 +  '</tr>';
					idx ++;
				}
			}

			$('#list').html(html);

			var obj = $('td', $('tr:first', $('#list')));

			//var seq    = $('button:first', $(obj).eq(4)).attr('seq');
			//var insuCd = $('button:first', $(obj).eq(4)).attr('cd');
			var sequNo = $(obj).eq(2).text();
			var pay    = $(obj).eq(3).text();
			var tmpDt  = $(obj).eq(4).text().split('.').join('-').split('~');

			$('#insuCd').val(insuCd);
			$('#secuNo').val(sequNo);
			$('#pay').val(pay);
			$('#fromDt').val(tmpDt[0]);
			$('#toDt').val(tmpDt[1]);
			$('#chkRe').attr('checked',false);

			opener.seq    = seq;
			opener.insuNm = $('#insuCd option:selected').text();
			opener.fromDt = $('#fromDt').val();
			opener.toDt   = $('#toDt').val();
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfApplay(){
	if (!$('#insuCd').val()){
		alert('보험사를 선택하여 주십시오.');
		$('#insuCd').focus();
		return;
	}

	if ($('#fromDt').val() > $('#toDt').val()){
		alert('입력날짜 오류입니다.\n시작일자가 종료일자보다 클 수 없습니다. 확인 후 다시 입력하여 주십시오.');
		$('#fromDt').focus();
		return;
	}

	var next = true;
	var first = true;

	$('tr', $('#list')).each(function(){
		if ($('#chkRe').attr('checked')){
			first = false;
		}

		if (!first){
			var tmpDt = $('td', $(this)).eq(3).text().split('.').join('-').split('~');

			if ($('#fromDt').val() >= tmpDt[0] && $('#fromDt').val() < tmpDt[1]){
				alert('시작일자이 기존의 보험내역과 중첩됩니다. 확인 후 다시 입력하여 주십시오.');
				$('#fromDt').focus();
				next = false;
				return false;
			}

			if ($('#toDt').val() > tmpDt[0] && $('#toDt').val() <= tmpDt[1]){
				alert('종료일자가 기존의 보험내역과 중첩됩니다. 확인 후 다시 입력하여 주십시오.');
				$('#toDt').focus();
				next = false;
				return false;
			}
		}

		first = false;
	});

	if (!next) return;

	var seq = __str2num(opener.seq);

	$.ajax({
		type: 'POST'
	,	url : './insu_apply.php'
	,	data: {
			'code':opener.code
		,	'svcCd':opener.svcCd
		,	'seq':seq
		,	'insuCd':$('#insuCd').val()
		,	'secuNo':$('#secuNo').val()
		,	'pay':__str2num($('#pay').val())
		,	'fromDt':$('#fromDt').val()
		,	'toDt':$('#toDt').val()
		,	're':$('#chkRe').attr('checked')
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				alert('정상적으로 처리되었습니다.');
				lfList();
				lfClose();
			}else if (result == 9){
				alert('데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfDelete(obj){
	if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

	$.ajax({
		type: 'POST'
	,	url : './insu_delete.php'
	,	data: {
			'code':opener.code
		,	'svcCd':opener.svcCd
		,	'seq':$(obj).attr('seq')
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			if (result == 1){
				alert('정상적으로 처리되었습니다.');
				lfList();
			}else if (result == 9){
				alert('데이타 삭제중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfClose(){
	opener.result = 1;
	self.close();
}
</script>
<form id="f" name="f" method="post">
<div id="title" class="title title_border">책임보험 변경안내</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="140px">
		<col width="60px">
		<col width="140px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>보험사명</th>
			<td>
				<select id="insuCd" name="cbo" style="width:auto;">
				<option value="">-</option><?
				//보험사 리스트
				if ($gDomain == 'kdolbom.net'){
					$sql = 'SELECT g01_code AS cd
							,      g01_name AS nm
							  FROM g01ins
							 WHERE g01_code IN (\'2\',\'99999\')
							 ORDER BY g01_code';
				}else{
					$sql = 'SELECT g01_code AS cd
							,      g01_name AS nm
							  FROM g01ins
							 WHERE g01_use = \'Y\'
							 ORDER BY g01_code';
				}
				$laInsuMst = $conn->_fetch_array($sql,'cd');

				foreach($laInsuMst as $laInsu){?>
					<option value="<?=$laInsu['cd'];?>"><?=$laInsu['nm'];?></option><?
				}?>
				</select>
			</td>
			<th>증권번호</th>
			<td>
				<input id="secuNo" name="txt" type="text" style="width:100%;">
			</td>
			<td class="center" rowspan="3">
				<span class="btn_pack m"><button type="button" onclick="lfApplay();">적용</button></span><br>
				<span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span>
			</td>
		</tr>
		<tr>
			<th>보험료</th>
			<td colspan="3"><input id="pay" name="txt" type="text" value="0" class="number" style="width:70px;"></td>
		</tr>
		<tr>
			<th>가입기간</th>
			<td colspan="3">
				<input id="fromDt" name="txt" type="text" class="date"> ~
				<input id="toDt" name="txt" type="text" class="date">
				<input id="chkRe" name="chk" type="checkbox" class="checkbox"><label for="chkRe">재등록</label>
			</td>
		</tr>
	</tbody>
</table>

<div id="title" class="title title_border">변경내역</div>
<?
	$colgroup = '<col width="40px">
				 <col width="80px">
				 <col width="100px">
				 <col width="65px">
				 <col width="130px">
				 <col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">보험사명</th>
			<th class="head">증권번호</th>
			<th class="head">보험료</th>
			<th class="head">가입기간</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top" colspan="6">
				<div id="body" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="list"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>
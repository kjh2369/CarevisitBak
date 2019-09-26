<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$colgroup = '<col width="80px"><col width="80px"><col width="65px"><col width="270px"><col>';
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		$('#lblCode').text(opener.code);
		$('#lblName').text(opener.name);
		$('#lblCharge').text(__num2str(opener.charge));

		__init_form(document.f);

		lfResize();
		lfSearch();
	});

	$(window).unload(function(){
		if (__str2num(opener.unpaid) != __str2num($('#lblUnpaid').text())){
			opener.win.lfSearch();
		}
	});

	function lfResize(){
		var h = $(this).height();
		var t = $('#list').offset().top;

		h = h - t - 27;

		$('#list').height(h);
	}

	function lfReg(){
		if (!$('#txtDate').val()){
			alert('입금일을 입력하여 주십시오.');
			$('#txtDate').focus();
			return;
		}

		if (!$('#txtAmt').val()){
			alert('입금금액을 입력하여 주십시오.');
			$('#txtAmt').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./apply.php'
		,	data :{
				'mode':opener.mode+'_1'
			,	'code':opener.code
			,	'date':$('#txtDate').val()
			,	'amt':__str2num($('#txtAmt').val())
			,	'type':$('#cboType').val()
			,	'other':$('#txtOther').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 7){
					alert('하루에 2건이상의 입금등록을 할 수 없습니다.');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다.\n잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();

				if (result == 1){
					$('#txtDate').val('');
					$('#txtAmt').val(0);
					$('#txtOther').val('');
					$('#cboType').val('1');
					lfSearch();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./search.php'
		,	data :{
				'mode':opener.mode+'_1'
			,	'code':opener.code
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var list = data.split(String.fromCharCode(1));
				var html = '<table class="my_table" style="width:100%;"><colgroup><?=$colgroup;?></colgroup><tbody>';
				var deposit = 0;
				var unpaid  = 0;

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						if (val[2] == '1'){
							val[2] = 'CMS';
						}else if (val[2] == '3'){
							val[2] = '현금';
						}else if (val[2] == '5'){
							val[2] = '카드';
						}else{
							val[2] = '기타';
						}

						deposit += __str2num(val[1]);

						html += '<tr>'
							 +  '<td class="center">'+val[0]+'</td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[1])+'</div></td>'
							 +  '<td class="center">'+val[2]+'</td>'
							 +  '<td class="center"><div class="left">'+val[3]+'</div></td>'
							 +  '<td class="center last"><div class="left"><span class="btn_pack m"><button type="button" onclick="lfDelete(\''+val[0]+'\');">삭제</button></span></div></td>'
							 +  '</tr>';
					}
				}

				html += '</tbody></table>';

				unpaid = __str2num($('#lblCharge').text()) - deposit;

				$('#list').html(html);
				$('#lblDeposit').text(__num2str(deposit));
				$('#lblUnpaid').text(__num2str(unpaid));
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(asDate){
		if (!confirm('삭제후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./delete.php'
		,	data :{
				'mode':opener.mode+'_1'
			,	'code':opener.code
			,	'date':asDate
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
				}else if (result == 9){
				}else{
					alert(result);
				}
				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<base target="_self">
<form name="f">

<div class="title title_border">SMS 입금등록</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">기관기호</th>
			<td class="left" id="lblCode"></td>
			<th class="head">기관명</th>
			<td class="left last" id="lblName"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px" span="6">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">총사용금액</th>
			<td class="right bold" style="color:black;" id="lblCharge">0</td>
			<th class="head">총입금금액</th>
			<td class="right bold" style="color:blue;" id="lblDeposit">0</td>
			<th class="head">현미남금액</th>
			<td class="right bold" style="color:red;" id="lblUnpaid">0</td>
			<th class="head">비고</th>
			<td class="left last"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">입금일시</th>
			<th class="head">입금금액</th>
			<th class="head">입금구분</th>
			<th class="head last" colspan="2">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center"><input id="txtDate" name="txt" type="text" class="date"></td>
			<td class="center"><input id="txtAmt" name="txt" type="text" class="number" style="width:100%;"></td>
			<td class="center">
				<select id="cboType" name="cbo" style="width:auto;">
					<option value="1">CMS</option>
					<option value="3">현금</option>
					<option value="5">카드</option>
					<option value="9">기타</option>
				</select>
			</td>
			<td class="center"><input id="txtOther" name="txt" type="text" style="width:100%;"></td>
			<td class="center last">
				<div class="left"><span class="btn_pack m"><button type="button" onclick="lfReg();">등록</button></span></div>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="top center last" colspan="5">
				<div id="list" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center last" colspan="5">
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>
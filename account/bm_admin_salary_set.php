<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;
	var close = '';

	$(document).ready(function(){
		opener = window.dialogArguments;

		$('input:text').each(function(){
			__init_object(this);
		});

		$(':text[id="nps_amt"], :text[id="nhic_amt"], :text[id="ei_amt"]').unbind('change').bind('change', function(){
			$('#insu_amt').text(__num2str(__str2num($('#nps_amt').val()) + __str2num($('#nhic_amt').val()) + __str2num($('#ei_amt').val())));
		});

		lfLoadOrgNm();
	});

	window.onunload = function(){
		var val = {};

		val['mgCnt'] = 0;
		val['mgAmt'] = 0;
		val['mgIns'] = 0;
		val['mgRetire'] = 0;
		val['mmCnt'] = 0;
		val['mmAmt'] = 0;
		val['mmIns'] = 0;
		val['mmRetire'] = 0;

		$('tr',$('#tbodyList')).each(function(){
			if ($('td',this).eq(1).text() == '센터장'){
				val['mgAmt'] += __str2num($('td',this).eq(3).text());
				val['mgIns'] += __str2num($('td',this).eq(7).text());
				val['mgRetire'] += __str2num($('td',this).eq(8).text());
				val['mgCnt'] ++;
			}

			if ($('td',this).eq(1).text() == '정직원'){
				val['mmAmt'] += __str2num($('td',this).eq(3).text());
				val['mmIns'] += __str2num($('td',this).eq(7).text());
				val['mmRetire'] += __str2num($('td',this).eq(8).text());
				val['mmCnt'] ++;
			}
		});

		opener.win.lfSetSub(opener.orgNo, __str2num(opener.month), val);
	}

	function lfResize(){
		var obj = __GetTagObject($('#tbodyList'),'DIV');
		$(obj).height(__GetHeight($(obj)));
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
				}else{
					$('#tbodyReg').show();
					$('#ID_BTN_BEFORE').show();
				}

				lfResize();
				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_salary_set_search.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			,	'close'	:close
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('div[id^="tot_"]').text('0');
				$('#tbodyList tr').each(function(){
					$('#tot_salary').text(__num2str(__str2num($('#tot_salary').text()) + __str2num($('td', this).eq(3).text())));
					$('#tot_nps').text(__num2str(__str2num($('#tot_nps').text()) + __str2num($('td', this).eq(4).text())));
					$('#tot_nhic').text(__num2str(__str2num($('#tot_nhic').text()) + __str2num($('td', this).eq(5).text())));
					$('#tot_ei').text(__num2str(__str2num($('#tot_ei').text()) + __str2num($('td', this).eq(6).text())));
					$('#tot_insu').text(__num2str(__str2num($('#tot_insu').text()) + __str2num($('td', this).eq(7).text())));
					$('#tot_re').text(__num2str(__str2num($('#tot_re').text()) + __str2num($('td', this).eq(8).text())));
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		if (!$('#lblMemIf').attr('jumin')){
			alert('직원을 선택하여 주십시오.');
			lfFindMem();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_salary_set_save.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			,	'jumin'	:$('#lblMemIf').attr('jumin')
			,	'job'	:$('#cboJob').val()
			,	'amt'	:$('#txtAmt').val()
			//,	'insu'	:$('#txtInsu').val()
			,	'nps_amt':$('#nps_amt').val()
			,	'nhic_amt':$('#nhic_amt').val()
			,	'ei_amt':$('#ei_amt').val()
			,	'retir'	:$('#txtRe').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('input:text').each(function(){
						if ($(this).hasClass('number')){
							$(this).val(0);
						}else{
							$(this).val('');
						}
					});

					lfSearch();
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

	function lfRemove(jumin){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_salary_set_remove.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			,	'jumin'	:jumin
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
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

	function lfFindMem(){
		var obj = __findMember(opener.orgNo);
		if (!obj) return false;
		$('#lblMemIf').attr('jumin',obj['jumin']).text(obj['name']);
		return true;
	}

	function lfBeforeDataLoad(){
		if (!confirm('전월 데이타를 적용하시면 현재데이타는 삭제됩니다.\n전월 데이타를 적용하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_salary_set_beforedata.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			,	'month'	:opener.month
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 7){
					alert('전월에 복사할 데이타가 존재하지 않습니다.');
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
			<td class="left">
				<div id="lblOrgNm" style="float:left; width:auto;"></div>
				<div id="ID_BTN_BEFORE" style="float:right; width:auto; padding:3px 5px; display:none;">
					<span class="btn_pack small"><button onclick="lfBeforeDataLoad();">전월데이타 적용</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="100px">
		<col width="70px" span="6">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">직책</th>
			<th class="head" rowspan="2">성명</th>
			<th class="head" rowspan="2">임금</th>
			<th class="head" colspan="4">4대보험</th>
			<th class="head" rowspan="2">퇴직충당금</th>
			<th class="head" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">국민</th>
			<th class="head">건강</th>
			<th class="head">고용</th>
			<th class="head">합계</th>
		</tr>
	</thead>
	<tbody id="tbodyReg" style="display:none;">
		<tr>
			<td class="center">-</td>
			<td class="center">
				<select id="cboJob" style="float:left; width:auto;">
					<option value="01">센터장</option>
					<option value="02">정직원</option>
				</select>
			</td>
			<td class="center">
				<div style="float:left; width:auto; margin-left:3px; margin-top:2px; height:25px;"><span class="btn_pack find" onclick="lfFindMem();" jumin=""></span></div>
				<div id="lblMemIf" jumin="" style="float:left; width:auto; margin-left:3px; margin-top:3px;"></div>
			</td>
			<td class="center">
				<input id="txtAmt" type="text" value="0" class="number" style="width:100%;">
			</td>
			<td class="center">
				<input id="nps_amt" type="text" value="0" class="number" style="width:100%;">
			</td>
			<td class="center">
				<input id="nhic_amt" type="text" value="0" class="number" style="width:100%;">
			</td>
			<td class="center">
				<input id="ei_amt" type="text" value="0" class="number" style="width:100%;">
			</td>
			<td class="center">
				<div class="right" id="insu_amt">0</div>
			</td>
			<td class="center">
				<input id="txtRe" type="text" value="0" class="number" style="width:100%;">
			</td>
			<td class="center">
				<span class="btn_pack small" style="float:left; margin-left:5px;"><button onclick="lfSave();">등록</button></span>
			</td>
		</tr>
		<tr>
			<td class="sum" colspan="3"><div class="right">합계</div></td>
			<td class="sum"><div id="tot_salary" class="right">0</div></td>
			<td class="sum"><div id="tot_nps" class="right">0</div></td>
			<td class="sum"><div id="tot_nhic" class="right">0</div></td>
			<td class="sum"><div id="tot_ei" class="right">0</div></td>
			<td class="sum"><div id="tot_insu" class="right">0</div></td>
			<td class="sum"><div id="tot_re" class="right">0</div></td>
			<td class="sum last"></td>
		</tr>
	</tbody>
</table>
<div style="width:100%; height:100px; overflow-x:hidden;overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="100px">
			<col width="100px">
			<col width="70px" span="6">
			<col>
		</colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input').each(function(){
			__init_object(this);
		});

		$('input:radio').unbind('click').bind('click',function(){
			if ($(this).attr('name') == 'optSvcCd'){
				$('div[id^="ID_SALARY_"],div[id^="ID_LAYER_"]').hide();

				if ($(this).val() == '11'){
					$('#ID_SALARY_1').show();
					$('#ID_SALARY_2').show();
					$('#ID_SALARY_3').show();
					$('#ID_SALARY_5').show();
					$('#ID_SALARY_6').show();
				}else if ($(this).val() == '12'){
					$('#ID_SALARY_1').show();
					$('#ID_SALARY_3').show();
					$('#ID_SALARY_5').show();
					$('#ID_SALARY_6').show();
				}else if ($(this).val() == '23'){
					$('#ID_SALARY_1').show();
					$('#ID_SALARY_3').show();
					$('#ID_SALARY_4').show();
					$('#ID_SALARY_5').show();
				}else{
					$('#ID_SALARY_1').show();
					$('#ID_SALARY_3').show();
					$('#ID_SALARY_5').show();
				}

				lfSetLayer(false);
			}else if ($(this).attr('name').indexOf('optUse') >= 0){
				var id = $(this).attr('name').replace('optUse','');

				if ($(this).val() == 'Y'){
					$('#ID_LAYER_'+id).hide();
				}else{
					$('#ID_LAYER_'+id).show();
				}

				lfChkNot(true);
			}else{
				if ($(this).attr('target')){
					var obj = $(this).attr('target');
					$('input:text[id^="'+obj+'"]').attr('disabled',true).css('background-color','#EAEAEA');
					$('input:text[id^="'+obj+'_'+$(this).val()+'"]').attr('disabled',false).css('background-color','#FFFFFF').eq(0).focus();
				}
			}
		}).eq(0).attr('checked',true).click();

		$('input:radio[name^="optAplAmt"]:checked').click();

		lfSetLayer(true);
	});

	function lfSetLayer(IsInit){
		var html = '';

		$('div[id^="ID_SALARY_"]').each(function(){
			var id = $(this).attr('id').replace('ID_SALARY_','');
			var th = $('th',$('tr',this).eq(1)).eq(0);
			var td = $('td',$('tr',this).eq(1)).eq(0);
			var bt = $('td',$('tr:last',this)).eq(0);

			var left	= $(th).offset().left + 2;
			var top		= $(th).offset().top + 1;
			var width	= $(th).width() + $(td).width() + 3;
			var height	= $(bt).offset().top + $(bt).height() - top - 1;
			var display	= '';

			if ($('input:radio[name="optUse'+id+'"]:checked').val() == 'Y'){
				display = 'none';
			}

			if (IsInit){
				html += '<div id="ID_LAYER_'+id+'" style="position:absolute; left:'+left+'; top:'+top+'; width:'+width+'; height:'+height+'; display:'+display+'; z-index:101; background:url(\'../image/tmp_bg.png\')"></div>';
			}else{
				if ($(this).css('display') != 'none'){
					if (display == ''){
						$('#ID_LAYER_'+id)
							.css('left',left)
							.css('top',top)
							.css('width',width)
							.css('height',height)
							.show();
					}else{
						$('#ID_LAYER_'+id).hide();
					}
				}else{
					$('#ID_LAYER_'+id).hide();
				}
			}
		});

		if (IsInit) $('#ID_SALARY_5').after(html);
	}

	function lfSave(){
		if ($('input:checkbox[id^="chkMemGbn"]:checked').length == 0){
			alert('고용형태를 하나 이상 선택하여 주십시오.');
			return;
		}

		var cnt = 0;
		var err = false;
		var data = {};

		$('div[id^="ID_SALARY_"]').each(function(){
			var id = $(this).attr('id').replace('ID_SALARY_','');
			if ($('input:radio[name="optUse'+id+'"]:checked').val() == 'Y'){
				if (!$('#txtYYMM'+id).val()){
					alert('적용년월을 입력하여 주십시오.');
					$('#txtYYMM'+id).focus();
					err = true;
					return false;
				}

				$('input',this).each(function(){
					switch($(this).attr('type')){
						case 'radio':
							if ($(this).attr('checked')){
								data[$(this).attr('name')] = $(this).val();
							}
							break;

						case 'checkbox':
							if ($(this).attr('checked')){
								data[$(this).attr('id')] = $(this).val();
							}
							break;

						default:
							data[$(this).attr('id')] = $(this).val();
					}
				});

				cnt ++;
			}
		});

		if (err){
			return false;
		}

		if (!cnt){
			alert('적용할 급여형태를 선택하여 주십시오.');
			return;
		}

		if (!confirm('선택하신 급여내역을 변경하시겠습니까?')) return;

		data['chkMemGbn1'] = $('#chkMemGbn1').attr('checked') ? 'Y' : 'N';
		data['chkMemGbn2'] = $('#chkMemGbn2').attr('checked') ? 'Y' : 'N';
		data['chkMemGbn3'] = $('#chkMemGbn3').attr('checked') ? 'Y' : 'N';
		data['chkMemGbn4'] = $('#chkMemGbn4').attr('checked') ? 'Y' : 'N';
		data['memNot'] = $('#chkMemNot').attr('checked') ? 'Y' : 'N';
		data['svcCd'] = $('input:radio[name="optSvcCd"]:checked').val();

		$.ajax({
			type:'POST',
			url:'./salary_save.php',
			data:data,
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(result){
				if('<?=$debug;?>') $('#ID_SALARY_1').html(result); 

				$('#tempLodingBar').remove();

				if (__resultMsg(result)){
				}
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfChkNot(IsSet){
		var cnt = 0;

		if (!IsSet) IsSet = false;

		$('div[id^="ID_SALARY_"]').each(function(){
			var id = $(this).attr('id').replace('ID_SALARY_','');
			if ($('input:radio[name="optUse'+id+'"]:checked').val() == 'Y'){
				cnt ++;
			}
		});

		if ($('#chkMemNot').attr('checked')){
			if (IsSet && cnt > 1){
				alert('급여형태를 2개이상 선택시 "급여 미등록 직원 포함"이 해제됩니다.');
				$('#chkMemNot').attr('checked',false);
				return;
			}

			if (cnt > 1){
				alert('급여형태를 하나만 선택하여 주십시오.');
				$('#chkMemNot').attr('checked',false);
				return;
			}
		}
	}
</script>
<div class="title title_border">급여정보 일괄수정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>고용형태</th>
			<td class="last">
				<div style="float:left; width:auto;">
					<label><input id="chkMemGbn1" type="checkbox" class="checkbox" value="1">정규직</label>
					<label><input id="chkMemGbn2" type="checkbox" class="checkbox" value="2">계약직</label>
					<label><input id="chkMemGbn3" type="checkbox" class="checkbox" value="3">단시간(60시간이상)</label>
					<label><input id="chkMemGbn4" type="checkbox" class="checkbox" value="4">단시간(60시간미만)</label>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				</div>
				<div style="float:center; width:auto; text-align:center;">
					<label><input id="chkMemNot" type="checkbox" class="checkbox" value="Y" onclick="lfChkNot();">급여 미등록 직원을 포함.</label>
				</div>
			</td>
		</tr>
		<tr>
			<th>선택서비스</th>
			<td class="last"><?
				if ($gHostSvc['homecare']){
					//재가요양?>
					<label><input id="optSvcCd11" name="optSvcCd" type="radio" class="radio" value="11">재가요양</label>
					<label><input id="optSvcCd12" name="optSvcCd" type="radio" class="radio" value="12">가족요양</label><?
				}

				if ($gHostSvc['nurse']){
					//가사간병?>
					<label><input id="optSvcCd21" name="optSvcCd" type="radio" class="radio" value="21">가사간병</label><?
				}

				if ($gHostSvc['old']){
					//노인돌봄?>
					<label><input id="optSvcCd22" name="optSvcCd" type="radio" class="radio" value="22">노인돌봄</label><?
				}

				if ($gHostSvc['baby']){
					//산모신생아?>
					<label><input id="optSvcCd23" name="optSvcCd" type="radio" class="radio" value="23">산모신생아</label><?
				}

				if ($gHostSvc['dis']){
					//장애인활동지원?>
					<label><input id="optSvcCd24" name="optSvcCd" type="radio" class="radio" value="24">장애인활동지원</label><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<div id="ID_SALARY_1" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="4">시<br>급</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse1Y" name="optUse1" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse1N" name="optUse1" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM1" type="text" value="" class="yymm">
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt1_1" name="optAplAmt1" type="radio" class="radio" value="1" target="txtAmt1" checked>적용시급</label></th>
				<td class="last">
					<input id="txtAmt1_1" type="text" value="0" class="number" style="width:70px;">
					<span>※변경될 시급을 입력합니다.</span>
				</td>
			</tr>
			<tr>
				<th class="bottom" style="padding:0;"><label><input id="optAplAmt1_2" name="optAplAmt1" type="radio" class="radio" target="txtAmt1" value="2">추가시급</label></th>
				<td class="bottom last">
					<input id="txtAmt1_2" type="text" value="0" class="number" style="width:70px;">
					<span>※현 시급에 추가할 금액을 입력합니다.</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="ID_SALARY_2" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="4">수<br>가<br>별<br>수<br>당</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse2Y" name="optUse2" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse2N" name="optUse2" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM2" type="text" value="" class="yymm">
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt2_1" name="optAplAmt2" type="radio" class="radio" value="1" target="txtAmt2" checked>적용시급</label></th>
				<td class="last">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px" span="8">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th>30분</th>
								<td><input id="txtAmt2_1_1" type="text" value="0" class="number" style="width:50px;"></td>
								<th>60분</th>
								<td><input id="txtAmt2_1_2" type="text" value="0" class="number" style="width:50px;"></td>
								<th>90분</th>
								<td><input id="txtAmt2_1_3" type="text" value="0" class="number" style="width:50px;"></td>
								<th>120분</th>
								<td><input id="txtAmt2_1_4" type="text" value="0" class="number" style="width:50px;"></td>
								<td class="left bottom" rowspan="2">
									<span>※변경될 수당을 입력합니다.</span>
								</td>
							</tr>
							<tr>
								<th class="bottom">150분</th>
								<td class="bottom"><input id="txtAmt2_1_5" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">180분</th>
								<td class="bottom"><input id="txtAmt2_1_6" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">210분</th>
								<td class="bottom"><input id="txtAmt2_1_7" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">240분</th>
								<td class="bottom"><input id="txtAmt2_1_8" type="text" value="0" class="number" style="width:50px;"></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th class="bottom" style="padding:0;"><label><input id="optAplAmt2_2" name="optAplAmt2" type="radio" class="radio" target="txtAmt2" value="2">추가시급</label></th>
				<td class="bottom last">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px" span="8">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th>30분</th>
								<td><input id="txtAmt2_2_1" type="text" value="0" class="number" style="width:50px;"></td>
								<th>60분</th>
								<td><input id="txtAmt2_2_2" type="text" value="0" class="number" style="width:50px;"></td>
								<th>90분</th>
								<td><input id="txtAmt2_2_3" type="text" value="0" class="number" style="width:50px;"></td>
								<th>120분</th>
								<td><input id="txtAmt2_2_4" type="text" value="0" class="number" style="width:50px;"></td>
								<td class="left bottom" rowspan="2">
									<span>※현 수당에 추가할 금액을 입력합니다.</span>
								</td>
							</tr>
							<tr>
								<th class="bottom">150분</th>
								<td class="bottom"><input id="txtAmt2_2_5" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">180분</th>
								<td class="bottom"><input id="txtAmt2_2_6" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">210분</th>
								<td class="bottom"><input id="txtAmt2_2_7" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">240분</th>
								<td class="bottom"><input id="txtAmt2_2_8" type="text" value="0" class="number" style="width:50px;"></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="ID_SALARY_3" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="4">총<br>액<br>비<br>율</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse3Y" name="optUse3" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse3N" name="optUse3" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM3" type="text" value="" class="yymm">
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt3_1" name="optAplAmt3" type="radio" class="radio" value="1" target="txtAmt3" checked>적용시급</label></th>
				<td class="last">
					<input id="txtAmt3_1_1" type="text" value="0" class="number" style="width:30px; margin-right:0;" maxlength="2"> .
					<input id="txtAmt3_1_2" type="text" value="0" class="number" style="width:30px; margin-left:0;" maxlength="2">
					<span>※변경될 비율을 입력합니다.</span>
				</td>
			</tr>
			<tr>
				<th class="bottom" style="padding:0;"><label><input id="optAplAmt3_2" name="optAplAmt3" type="radio" class="radio" target="txtAmt3" value="2">추가시급</label></th>
				<td class="bottom last">
					<input id="txtAmt3_2_1" type="text" value="0" class="number" style="width:30px; margin-right:0;" maxlength="2"> .
					<input id="txtAmt3_2_2" type="text" value="0" class="number" style="width:30px; margin-left:0;" maxlength="2">
					<span>※현 시급에 추가할 비율을 입력합니다.</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="ID_SALARY_4" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="4">일<br>당</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse4Y" name="optUse4" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse4N" name="optUse4" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM4" type="text" value="" class="yymm">
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt4_1" name="optAplAmt4" type="radio" class="radio" value="1" target="txtAmt4" checked>적용시급</label></th>
				<td class="last">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="50px">
							<col width="70px">
							<col width="50px">
							<col width="70px">
							<col width="50px">
							<col width="70px">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="bottom">단태아</th>
								<td class="bottom"><input id="txtAmt4_1_1" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">쌍태아</th>
								<td class="bottom"><input id="txtAmt4_1_2" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">삼태아</th>
								<td class="bottom"><input id="txtAmt4_1_3" type="text" value="0" class="number" style="width:50px;"></td>
								<td class="left bottom" rowspan="2">
									<span>※변경될 일당을 입력합니다.</span>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th class="bottom" style="padding:0;"><label><input id="optAplAmt4_2" name="optAplAmt4" type="radio" class="radio" target="txtAmt4" value="2">추가시급</label></th>
				<td class="bottom last">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="50px">
							<col width="70px">
							<col width="50px">
							<col width="70px">
							<col width="50px">
							<col width="70px">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="bottom">단태아</th>
								<td class="bottom"><input id="txtAmt4_2_1" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">쌍태아</th>
								<td class="bottom"><input id="txtAmt4_2_2" type="text" value="0" class="number" style="width:50px;"></td>
								<th class="bottom">삼태아</th>
								<td class="bottom"><input id="txtAmt4_2_3" type="text" value="0" class="number" style="width:50px;"></td>
								<td class="left bottom" rowspan="2">
									<span>※변경될 일당을 입력합니다.</span>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="ID_SALARY_5" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="5">고<br>정<br>임<br>금</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse5Y" name="optUse5" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse5N" name="optUse5" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM5" type="text" value="" class="yymm">
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt5_1" name="optAplAmt5" type="radio" class="radio" value="1" target="txtAmt5" checked>적용시급</label></th>
				<td class="last">
					<input id="txtAmt5_1" type="text" value="0" class="number" style="width:70px;">
					<span>※변경될 고정임금을 입력합니다.</span>
				</td>
			</tr>
			<tr>
				<th class="" style="padding:0;"><label><input id="optAplAmt5_2" name="optAplAmt5" type="radio" class="radio" target="txtAmt5" value="2">추가시급</label></th>
				<td class="last">
					<input id="txtAmt5_2" type="text" value="0" class="number" style="width:70px;">
					<span>※현 시급에 추가할 금액을 입력합니다.</span>
				</td>
			</tr>
			<tr>
				<td class="bottom" colspan="2">
					<label><input id="chkExtraPay" type="checkbox" class="checkbox" value="Y">목욕간호수당포함(재가요양, 장애인활동지원만 적용)</label>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="ID_SALARY_6" style="margin:10px; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom" rowspan="2">공<br>단</th>
				<th class="left">적용여부</th>
				<td class="last">
					<label><input id="optUse6Y" name="optUse6" type="radio" class="radio" value="Y">예</label>
					<label><input id="optUse6N" name="optUse6" type="radio" class="radio" value="N" checked>아니오</label>
				</td>
			</tr>
			<tr>
				<th>적용년월</th>
				<td class="last">
					<input id="txtYYMM6" type="text" value="" class="yymm">
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="padding-left:10px;">
	※ 고용형태별 선택서비스 급여내역을 변경할 수 있습니다.<br>
	&nbsp;&nbsp;&nbsp;&nbsp;각 직원 마지막 설정된 급여내역을 적용년월 이전달까지로 설정하고 적용년월부터 새로운 급여내역이 적용됩니다.<br>
	&nbsp;&nbsp;&nbsp;&nbsp;적용시급은 시급금액을 수정하며 추가시급은 현재 시급에 금액을 추가할 수 있습니다.<br>
</div>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
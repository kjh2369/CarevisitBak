<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');?>
	<table id="tblExtraBath" class="my_table my_border_blue" style="width:auto; background-color:#ffffff;">
		<colgroup>
			<col width="50px">
			<col width="50px">
			<col width="150px">
			<col width="50px">
		</colgroup>
		<thead>
			<tr>
				<th class="head last" colspan="4">
					<div style="float:right; width:auto; margin-right:5px;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfExtraPayHide();"></div>
					<div class="bold" style="float:center; width:auto;">기관 및 개별 수당입력</div>
				</th>
			</tr>
			<tr>
				<th class="head" colspan="2">구분</th>
				<th class="head">금액 및 비율</th>
				<th class="head">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center" colspan="2">개별</th>
				<td class="left">
					<span id="lblBathPay1" style="font-weight:bold;">0</span> /
					<span id="lblBathPay2" style="font-weight:bold;">0</span>
				</td>
				<td class="center last">
					<span class="btn_pack small"><button type="button" onclick="lfApplyExtraPay('BATH','PERSON',true);">선택</button></span>
				</td>
			</tr>
			<tr>
				<th class="center" rowspan="3">기관</th>
				<th class="center">수당</th>
				<td class="last" colspan="2">
					<input id="txtBathPay" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);">
				</td>
			</tr>
			<tr>
				<th class="center">비율</th>
				<td>
					<input id="txtBathRate1" name="txtBathRate" type="text" value="0" class="number" style="width:30px;" onchange="_planExtraPayChk(this);">% /
					<input id="txtBathRate2" name="txtBathRate" type="text" value="0" class="number" style="width:30px;" onchange="_planExtraPayChk(this);">%
				</td>
				<td class="center last">
					<span class="btn_pack small"><button type="button" onclick="lfApplyExtraPay('BATH','RATE',true);">선택</button></span>
				</td>
			</tr>
			<tr>
				<th class="center">금액</th>
				<td>
					<input id="txtBathPay1" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);"> /
					<input id="txtBathPay2" name="txtBathPay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);">
				</td>
				<td class="center last">
					<span class="btn_pack small"><button type="button" onclick="lfApplyExtraPay('BATH','AMT',true);">선택</button></span>
				</td>
			</tr>
		</tbody>
	</table>

	<table id="tblExtraNurse" class="my_table my_border_blue" style="width:auto; background-color:#ffffff;">
		<colgroup>
			<col width="50px">
			<col width="70px">
			<col width="50px">
		</colgroup>
		<thead>
			<tr>
				<th class="head last" colspan="4">
					<div style="float:right; width:auto; margin-right:5px;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfExtraPayHide();"></div>
					<div class="bold" style="float:center; width:auto;">기관 및 개별 수당입력</div>
				</th>
			</tr>
			<tr>
				<th class="head">구분</th>
				<th class="head">금액</th>
				<th class="head">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center">개별</th>
				<td class="left">
					<span id="lblNursePay" style="font-weight:bold;"></span>
				</td>
				<td class="center last">
					<span class="btn_pack small"><button type="button" onclick="lfApplyExtraPay('NURSE','PERSON',true);">선택</button></span>
				</td>
			</tr>
			<tr>
				<th class="center">기관</th>
				<td>
					<input id="txtNursePay" name="txtNursePay" type="text" value="0" class="number" style="width:50px;" onchange="_planExtraPayChk(this);">
				</td>
				<td class="center last">
					<span class="btn_pack small"><button type="button" onclick="lfApplyExtraPay('NURSE','AMT',true);">선택</button></span>
				</td>
			</tr>
		</tbody>
	</table><?
	include_once('../inc/_db_close.php');?>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#tblExtraBath').hide();
		$('#tblExtraNurse').hide();
	});

	/*
	//수당검열
	function lfExtraPayChk(obj){
		if ($(obj).attr('name') == 'txtBathRate'){
			var liVal1 = __str2num($(obj).val());
			var liVal2 = 0;

			if (liVal1 > 100){
				liVal1 = 100;
				$(obj).val(liVal1);
			}

			liVal2 = 100 - liVal1;

			if ($(obj).attr('id') == 'txtBathRate1'){
				$('#txtBathRate2').val(liVal2);
			}else{
				$('#txtBathRate1').val(liVal2);
			}
		}else if ($(obj).attr('name') == 'txtBathPay'){
			var liVal1 = __str2num($('#txtBathPay1').val());
			var liVal2 = __str2num($('#txtBathPay2').val());;
			var liExtraPay = __str2num($('#txtBathPay').val());

			if (liVal1 > liExtraPay){
				liVal1 = liExtraPay;

				if ($(obj).attr('id') == 'txtBathPay1'){
					$('#txtBathPay1').val(__num2str(liVal2));
				}else if ($(obj).attr('id') == 'txtBathPay2'){
					$('#txtBathPay2').val(__num2str(liVal2));
				}
			}

			liVal2 = liExtraPay - liVal1;

			if ($(obj).attr('id') == 'txtBathPay1'){
				$('#txtBathPay2').val(__num2str(liVal2));
			}else if ($(obj).attr('id') == 'txtBathPay2'){
				$('#txtBathPay1').val(__num2str(liVal2));
			}else{
				$('#txtBathPay1').val(__num2str(liExtraPay*0.5));
				$('#txtBathPay2').val(__num2str(liExtraPay*0.5));
			}
		}
	}
	*/

	//적용수당
	function lfApplyExtraPay(asKind, asGbn, abHide){
		var liVal1 = 0
		,	liVal2 = 0;
		var lsGbn = '';

		switch(asKind){
			case 'BATH':
				switch(asGbn){
					case 'PERSON':
						liVal1 = __str2num($('#lblBathPay1').text());
						liVal2 = __str2num($('#lblBathPay2').text());
						break;

					case 'RATE':
						liVal1 = __str2num($('#txtBathRate1').val());
						liVal2 = __str2num($('#txtBathRate2').val());
						lsGbn  = '%';
						break;

					case 'AMT':
						liVal1 = __str2num($('#txtBathPay1').val());
						liVal2 = __str2num($('#txtBathPay2').val());
						break;
				}

				$('#lblApplyBathPay1').attr('value',liVal1).text(__num2str(liVal1)+lsGbn);
				$('#lblApplyBathPay2').attr('value',liVal2).text(__num2str(liVal2)+lsGbn);
				break;

			case 'NURSE':
				switch(asGbn){
					case 'PERSON':
						liVal1 = __str2num($('#lblNursePay').text());
						break;

					case 'AMT':
						liVal1 = __str2num($('#txtNursePay').val());
						break;
				}

				$('#lblApplyNursePay').attr('value',liVal1).text(__num2str(liVal1));
				break;
		}

		if (asKind == 'BATH' && asGbn != 'PERSON'){
			$('#lblApplyBathPay')
				.attr('value',__str2num($('#txtBathPay').val()))
				.text($('#txtBathPay').val());
		}

		lfExtraPayDisplay(asKind, asGbn);

		$('#loExtraPay').attr('kind',asKind).attr('gbn',asGbn);

		if (abHide){
			lfExtraPayHide();
		}
	}

	//수당금액 표시
	function lfExtraPayDisplay(asKind, asGbn){
		if (asKind == 'BATH'){
			if (asGbn == 'RATE' || asGbn == 'AMT'){
				$('.clsBathCenter').show();
			}else{
				$('.clsBathCenter').hide();
			}
		}else{
			$('.clsBathCenter').hide();
		}
	}
	</script>
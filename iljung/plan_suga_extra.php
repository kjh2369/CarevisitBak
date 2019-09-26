<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');?>
	<table class="my_table my_border_blue" style="width:475px; background-color:#ffffff;">
		<colgroup>
			<col width="50px">
			<col width="70px" span="2">
			<col width="95px">
			<col width="110px">
			<col width="80px">
		</colgroup>
		<thead>
			<tr>
				<th class="head last" colspan="8">
					<div style="float:right; width:auto; margin-right:5px;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lbExtraHide();"></div>
					<div class="bold" style="float:center; width:auto;">비급여 실비처리구분</div>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="center" rowspan="4">적용<br>수가</th>
				<td class="last" colspan="7">
					<input id="txtExtraKind1" name="txtExtraKind" type="radio" value="1" class="radio"><label for="txtExtraKind1">공단수가</label>
					<input id="txtExtraKind2" name="txtExtraKind" type="radio" value="2" class="radio"><label for="txtExtraKind2">기관비급여수가</label>
					<input id="txtExtraKind3" name="txtExtraKind" type="radio" value="3" class="radio"><label for="txtExtraKind3">고객개별수가</label>
				</td>
			</tr>
			<tr>
				<th class="center">공단수가</th>
				<th class="center">비급여수가</th>
				<th class="center">고객수가</th>
				<th class="center">실비지급여부</th>
				<th class="center last">실비지급금액</th>
			</tr>
			<tr>
				<td class="center"><div id="txtBipayCost1" value="0" class="right">0</div></td>
				<td class="center"><div id="txtBipayCost2" value="0" class="right">0</div></td>
				<td class="center"><input id="txtBipayCost3" name="txtBipayCost3" type="text" value="0" class="number" style="width:50px;">/<span id="lblExtraTimeStr">시간</span></td>
				<td class="center">
					<input id="yRealPay" name="ynRealPay" type="radio" value="Y" class="radio"><label for="yRealPay">예</label>
					<input id="nRealPay" name="ynRealPay" type="radio" value="N" class="radio"><label for="nRealPay">아니오</label>
				</td>
				<td class="center last"><input id="txtRealPay" name="txtRealPay" type="text" value="0" class="number" style="width:100%;"></td>
			</tr>
		</tbody>
	</table><?
	include_once('../inc/_db_close.php');?>
	<script type="text/javascript">
	$(document).ready(function(){
		//적용수가 선택
		$('input:radio[name="txtExtraKind"]').unbind('click').click(function(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var loCost;

			if (lsSvcCd == '0' || lsSvcCd == '4'){
				if (lsSvcKind == '500' || lsSvcKind == '800'){
					loCost = $('#lblSugaCost', $('#loSuga2'));
				}else{
					loCost = $('#lblSugaCost', $('#loSuga1'));
				}
			}else{
				loCost = $('#lblSugaCost');
			}

			var obj = $('#txtBipayCost'+$(this).val());
			var liSvcTime = 0;
			var liPay = __str2num($(obj).attr('value'));

			if (lsSvcCd == '0'){
				if (lsSvcKind == '500' || lsSvcKind == '800'){
				}else{
					if ($(this).val() == '1' || $(this).val() == '2'){
					}else{
						//liSvcTime = __str2num($('#txtSvcTime').val());
						//비급여 시 적용수가 계산
						liSvcTime = __time2min($('#txtToH').val() + ':' + $('#txtToM').val()) - __time2min($('#txtFromH').val() + ':' + $('#txtFromM').val());
						liSvcTime = Math.floor(liSvcTime / 30);

						//240분 이상 시 휴식시간 30분 제거
						if (liSvcTime > 8){
							liSvcTime --;
						}

						liPay = liPay * (liSvcTime * 30 / 60);
					}
				}
			}else if (lsSvcCd == '3'){
			}else if (lsSvcCd == '4'){
				if (lsSvcKind == '500' || lsSvcKind == '800'){
				}else{
					if ($(this).val() == '1' || $(this).val() == '2'){
					}else{
						liSvcTime = __str2num($('#txtSvcTimeStr').attr('value'));
						liPay = liPay * (liSvcTime * 30 / 60);
					}
				}
			}else{
			}

			$(loCost).text(__num2str(liPay));
			$('#lblSugaTot').text(__num2str(liPay * __str2num($('#lblSugaCnt').text())));
			$('#loSuga').attr('costBipay',liPay);
		});

		//고객수가 변경 이벤트
		$('#txtBipayCost3').unbind('change').change(function(){
			if ($('input:radio[name="txtExtraKind"]:checked').val() == '3'){
				$('input:radio[name="txtExtraKind"]:checked').click();
			}
		});

		//실비지급여부 선택
		$('input:radio[name="ynRealPay"]').unbind('click').click(function(){
			if ($(this).val() == 'Y'){
				var lbDisabled = false;
				var lsColor = '#ffffff';
			}else{
				var lbDisabled = true;
				var lsColor = '#efefef';
			}

			$('#txtRealPay').attr('disabled',lbDisabled).css('background-color',lsColor);
		});

		//실비금액 변경 이벤트
		$('#txtRealPay').unbind('change').change(function(){
			var lsExtraKind = $('input:radio[name="txtExtraKind"]:checked').val();
			var liExtraPay  = __str2num($('#lblSugaCost').text());
			var liExtraVal  = __str2num($(this).val());

			if (liExtraVal > liExtraPay)
				liExtraVal = liExtraPay;

			$(this).val(liExtraVal);
		});

		$('#txtExtraKind1').attr('checked',true);
		$('#nRealPay').attr('checked',true);
		$('#nRealPay').click();
	});
	</script>
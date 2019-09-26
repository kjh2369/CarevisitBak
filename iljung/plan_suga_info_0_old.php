<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col width="70px">
			<col width="60px">
			<col width="20px">
			<col width="60px">
			<col width="20px">
			<col width="30px">
			<col width="85px">
			<col width="30px">
			<col width="85px">
			<col width="95px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">제공서비스</th>
				<th class="head">케어구분</th>
				<th class="head" colspan="4">요양보호사</th>
				<th class="head" colspan="4">방문시간</th>
				<th class="head"><span id="lblSvcTime">제공시간</span></th>
				<th class="head last">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<select id="txtSvcKind" name="txtSvcKind" style="width:auto;">
						<option value="200"><?=$svcCd == '0' ? '방문요양' : '활동지원';?></option>
						<option value="500">방문목욕</option>
						<option value="800">방문간호</option>
					</select>
				</td>
				<td>
					<select id="txtPayKind" name="txtPayKind" style="width:auto;">
						<option value="1">일반</option>
						<option value="3">비급여</option>
					</select>
					<div id="lblPayKind" style="width:auot; color:#ff0000; display:none;">본인부담</div>
				</td>
				<td class="last"><input id="txtMemCd1" name="txtMemCd" type="text" value="<?=$lsMemNm1;?>" code="<?=$ed->en($lsMemCd1);?>" ynFamily="<?=$ynMemFamily;?>" calendar="<?=$lsMemCalendar[1];?>" style="width:100%; cursor:default;" alt="not" readonly></td>
				<td><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfMemClear('1');"></td>
				<td class="last"><input id="txtMemCd2" name="txtMemCd" type="text" value="<?=$lsMemNm2;?>" code="<?=$ed->en($lsMemCd2);?>" ynFamily="N" calendar="<?=$lsMemCalendar[2];?>" style="width:100%; cursor:default;" alt="not" readonly></td>
				<td><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfMemClear('2');"></td>
				<th class="center">시작</th>
				<td>
					<input id="txtFromH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
					<input id="txtFromM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
				</td>
				<th class="center">종료</th>
				<td>
					<input id="txtToH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
					<input id="txtToM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
				</td>
				<td>
					<select id="txtSvcTime" name="svcTime" style="width:auto; display:none;"></select>
					<div id="txtSvcTimeStr" value="0" class="right" style="display:none;">0</div>
					<select id="txtCarNo" name="bathGbn" style="width:auto; display:none;">
						<option value=""></option>
					</select>
				</td>
				<td class="last"></td>
			</tr>
		</tbody>
	</table>
	<table id="loSuga1" class="my_table" style="width:100%; display:;">
		<colgroup>
			<col width="50px">
			<col width="150px">
			<col width="65px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom">수가명</th>
				<td class="left bottom"><div id="lblSugaNm"></div></td>
				<th class="center bottom"><div id="btnApplySuga">적용수가</div></th>
				<td class="left bottom last"><?
					if ($svcCd == '0'){?>
						기준수가[<span id="lblSugaCost" style="color:#0000ff;">0</span>]<span id="lblText1"> + (야간[<span id="lblSugaEveing" style="color:#0000ff;">0</span>] + 심야[<span id="lblSugaNight" style="color:#0000ff;">0</span>]) = 수가계[<span id="lblSugaTot" style="color:#0000ff;">0</span>]</span>원<?
					}else{?>
						기준수가[<span id="lblSugaCost" style="color:#0000ff;">0</span>]<span id="lblText1"> X 시간[<span id="lblSugaHour" style="color:#0000ff;">0</span>] +
						연장수가[<span id="lblNightCost" style="color:#0000ff;">0</span>]<span id="lblText1"> X 시간[<span id="lblNightHour" style="color:#0000ff;">0</span>] =
						수가계[<span id="lblSugaTot" style="color:#0000ff;">0</span>]원<?
					}?>
				</td>
			</tr>
		</tbody>
	</table>
	<table id="loSuga2" class="my_table" style="width:100%; display:none;">
		<colgroup>
			<col width="50px">
			<col width="150px">
			<col width="65px">
			<col width="70px">
			<col width="50px">
			<col width="80px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom">수가명</th>
				<td class="left bottom"><div id="lblSugaNm"></div></td>
				<th class="center bottom"><div id="btnApplySuga">적용수가</div></th>
				<td class="left bottom"><span id="lblSugaCost">0</span>원</td>
				<th class="center bottom">수당</th>
				<td class="center bottom"><input id="txtExtraPay" name="txtExtraPay" type="text" value="0" class="number" style="width:70px;"></td>
				<th class="center bottom clsExtrBath">
					<select id="txtExtraGbn" name="txtExtraGbn" style="width:auto;">
						<option value="rate">비율</option><?
						if ($debug){?>
							<option value="amt">금액</option><?
						}?>
					</select>
				</th>
				<td class="bottom last clsExtrBath">
					<input id="txtExtraPay1" name="txtExtraPR" type="text" value="<?=$liRate1;?>" rate="<?=$liRate1;?>" amt="0" class="number" style="width:30px;"><span class="clsRate">%</span>&nbsp;&nbsp;/
					<input id="txtExtraPay2" name="txtExtraPR" type="text" value="<?=$liRate2;?>" rate="<?=$liRate2;?>" amt="0" class="number" style="width:30px;"><span class="clsRate">%</span>
				</td>

				<td class="bottom last clsExtrNurs">&nbsp;</td>
				<td class="bottom last clsExtrNurs">&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<div id="loClientIfno"
		statNot="<?=$ynStatNot;?>"
		ynFamily90="<?=$ynFamily90;?>"
		ynPartner="<?=$ynPartner;?>"
		familyLimitCnt="<?=$liFamilyLimitCnt;?>"
		careLimitCnt="<?=$liCareLimitCnt;?>"
		style="display:none;"></div>
	<div id="loSvcInfo" ynMakeSvc="<?=$ynMakeSvc;?>" style="display:none;"></div>

	<script type="text/javascript">
		//제공서비스 변경이벤트
		$('#txtSvcKind').unbind('change').change(function(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $(this).val();

			if (lsSvcCd == '0'){
				lfSetCareGbn();
			}

			//방문요양
			if (lsSvcCd == '0'){
				//제공시간 설정
				$('#txtSvcTime').show();
				lfSetSvcTime(lfGetKindGbn());
			}else if (lsSvcCd == '4'){
				if (lsSvcKind == '200'){
					$('#txtSvcTime').hide();
					$('#txtSvcTimeStr').show();
				}else{
					$('#txtSvcTime').show();
					$('#txtSvcTimeStr').hide();
				}
				lfSetSvcTime(lfGetKindGbn());
			}

			var lbMem2 = false;

			if ((lsSvcKind == '500') || (lsSvcCd == '4' && lsSvcKind == '200')){
				lbMem2 = true;
			}
			if (lbMem2){
				$('#txtMemCd2').css('background-color','#ffffff').attr('disabled',false);
			}else{
				$('#txtMemCd2').attr('code','').attr('ynFamily','').attr('calendar','').css('background-color','#efefef').attr('disabled',true).val('');
			}

			if (lsSvcKind == '200'){
				$('#loSuga1').show();
				$('#loSuga2').hide();
			}else{
				$('#loSuga1').hide();
				$('#loSuga2').show();
			}

			if (lsSvcKind == '500'){
				$('.clsExtrBath').show();
				$('.clsExtrNurs').hide();
			}else if (lsSvcKind == '800'){
				$('.clsExtrBath').hide();
				$('.clsExtrNurs').show();
			}
		});

		//케어구분 변경이벤트
		$('#txtPayKind').unbind('change').change(function(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var obj;

			if (lsSvcKind == '500' || lsSvcKind == '800'){
				obj = $('#btnApplySuga', $('#loSuga2'));
				$('#lblExtraTimeStr').text('횟수');
			}else{
				obj = $('#btnApplySuga', $('#loSuga1'));
				$('#lblExtraTimeStr').text('시간');
			}

			if ($(this).val() != '3'){
				lbExtraHide();

				$(obj).text('적용수가');
				$('#lblText1').show();
			}else{
				//비급여 설정
				$(obj).html('<span class="btn_pack small"><button type="button" onclick="lfExtraShow(this);">적용수가</button></span>');
				$('#lblText1').hide();
			}

			if (lsSvcCd == '0' && lsSvcKind == '200'){
				if (($(this).val() == '1' && $('#txtMemCd1').attr('code') != '' && $('#txtMemCd1').attr('ynFamily') == 'Y') ||
					($(this).val() == '2' && $('#txtMemCd1').attr('code') != '' && $('#txtMemCd1').attr('ynFamily') != 'Y')){
					lfMemClear('1',true);
					lfMemClear('2',true);
				}

			}

			//제공시간 설정
			lfSetSvcTime(lfGetKindGbn());
		});

		//방문시간 변경 이벤트
		$('input:text[name="txtTime"]').unbind('keyup').keyup(function(){
			var lsPayKind = $('#txtPayKind').val();

			if ($(this).val().length == $(this).attr('maxlength')){
				//시간 초과시 변경
				if ($(this).attr('id') == 'txtFromH' || $(this).attr('id') == 'txtToH'){
					var liVal = __str2num($(this).val());

					if (liVal >= 24){
						liVal = liVal % 24;
					}

					liVal = (liVal < 10 ? '0' : '')+liVal;

					$(this).val(liVal);
				}

				//분 초과시 변경
				if ($(this).attr('id') == 'txtFromM' || $(this).attr('id') == 'txtToM'){
					var liVal = __str2num($(this).val());

					if (liVal >= 60){
						liVal = 0;
					}

					liVal = (liVal < 10 ? '0' : '')+liVal;

					$(this).val(liVal);
				}

				if ($(this).attr('id') == 'txtFromH'){
					$('#txtFromM').focus();
					return;
				}else if ($(this).attr('id') == 'txtFromM'){
					$('#txtToH').val('').focus();
					lfSetEndTime();
					return;
				}else if ($(this).attr('id') == 'txtToH'){
					$('#txtToM').val('').focus();
					return;
				}else if ($(this).attr('id') == 'txtToM'){
					if (lsPayKind == '2'){
						lfSetEndTime();
					}else{
						lfSetProcTime();
					}
					return;
				}
			}
		}).unbind('change').change(function(){
			/*
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();

			if (lsSvcKind == '200'){
				if (lsSvcCd == '0'){
					lfSetEndTime();
				}else if (lsSvcCd == '4'){
					lfFindSuga();
				}
			}else if (lsSvcKind == '500'){
				return;
			}else{
				lfSetProcTime();
			}
			*/
		}).unbind('focus').focus(function(){
		}).unbind('blur').blur(function(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var lsPayKind = $('#txtPayKind').val();
			var lsSvcTime = $('#txtSvcTime').val();

			lfSetTimePos(this);

			if ($(this).attr('id') == 'txtFromH' || $(this).attr('id') == 'txtFromM'){
				lfSetEndTime();

				if (lsSvcCd == '4'){
					lfSetProcTime();
				}
			}else if ($(this).attr('id') == 'txtToH' || $(this).attr('id') == 'txtToM'){
				if (lsSvcCd == '0'){
					if (lsSvcKind == '200' || lsSvcKind == '800'){
						if (lsSvcTime < '9'){
							if ($(this).attr('id') == 'txtToH'){
								lfSetProcTime();
							}else{
								if (lsPayKind == '2'){
									lfSetEndTime();
								}else{
									lfSetProcTime();
								}
							}
						}else{
							lfSetProcTime();
						}
					}else{
						lfFindSuga();
					}
				}
			}
		});

		//제공시간
		$('#txtSvcTime').unbind('change').change(function(){
			var lsSvcKind = $('#txtSvcKind').val();

			if (lsSvcKind != '500'){
				lfSetEndTime();
			}else{
				lfFindSuga();
			}
		});

		//요양보호사
		$('input:text[name="txtMemCd"]').unbind('click').click(function(){
			var liIdx = $(this).attr('id').split('txtMemCd').join('');

			//lfMemFind(liIdx);
			//공용함수로 옴김
			_planMemFind(liIdx, $('#centerInfo').attr('value'), $('#clientInfo').attr('value'), $('#planInfo').attr('svcCd'), ($('#txtMemCd1').attr('code')+','+$('#txtMemCd2').attr('code')), ($('#txtSvcKind').val() == '200' && $('#txtPayKind').val() == '2' ? 'Y' : $('#txtSvcKind').val()), 'lfMemFindResult');
		});

		//수당구분
		$('#txtExtraGbn').unbind('change').change(function(){
			if ($(this).val() == 'rate'){
				$('#txtExtraPay1').css('width','30px').val($('#txtExtraPay1').attr('rate'));
				$('#txtExtraPay2').css('width','30px').val($('#txtExtraPay2').attr('rate'));
				$('.clsRate').show();
			}else{
				var liExtraPay = __str2num($('#txtExtraPay').val());
				var liRate1 = __str2num($('#txtExtraPay1').attr('rate'));
				var liRate2 = __str2num($('#txtExtraPay2').attr('rate'));
				var liAmt1 = __num2str(liExtraPay * liRate1 * 0.01);
				var liAmt2 = __num2str(liExtraPay * liRate2 * 0.01);

				$('#txtExtraPay1').css('width','50px').val(liAmt1);
				$('#txtExtraPay2').css('width','50px').val(liAmt2);
				$('.clsRate').hide();
			}
		});

		//수당분리
		$('input:text[name="txtExtraPR"]').unbind('change').change(function(){
			var lsExtraGbn = $('#txtExtraGbn').val();
			var liTxt = 0;
			var liVal = 0;

			liVal = $(this).val();

			if (lsExtraGbn == 'rate'){
				if (liVal > 100) liVal = 100;
				liTxt = 100 - liVal;
			}else{
				if (liVal > __str2num($('#txtExtraPay').val())) liVal = __str2num($('#txtExtraPay').val());
				liTxt = __str2num($('#txtExtraPay').val()) - liVal;
			}

			if ($(this).attr('id') == 'txtExtraPay1'){
				$('#txtExtraPay1').val(liVal);
				$('#txtExtraPay2').val(liTxt);
			}else{
				$('#txtExtraPay1').val(liTxt);
				$('#txtExtraPay2').val(liVal);
			}
		});

		$(document).ready(function(){
			$('#txtSvcKind').change();

			if ($('#txtMemCd1').attr('ynFamily') == 'Y'){
				$('#txtPayKind').val('2').change();
			}

			if ($('#loSvcInfo').attr('ynMakeSvc') != 'Y'){
				$('#txtPayKind').val('3').hide().parent().addClass('center');
				$('#lblPayKind').show();
				$('#btnApplySuga').html('<span class="btn_pack small"><button type="button" onclick="lfExtraShow(this);">적용수가</button></span>');
			}

			lbWinLoad = true;
		});
	</script>
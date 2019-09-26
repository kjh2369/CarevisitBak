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
				<th class="head" colspan="4"><span id="lblPos">요양보호사</span><span id="lblPrmStr" style="display:none;">/프로그램관리자</span></th>
				<th class="head" colspan="4">방문시간</th>
				<th class="head"><span id="lblSvcTime">제공시간</span></th>
				<th class="head last">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<select id="txtSvcKind" name="txtSvcKind" style="width:auto;">
						<option value="200" val1=""><?=$svcCd == '0' ? '방문요양' : '활동지원';?></option>
						<option value="500" val1="">방문목욕</option>
						<option value="800" val1="">방문간호</option><?
						if ($year.$month >= '201609'){?>
							<option value="200" val1="dementia">인지활동</option>
							<option value="210" val1="">치매가족</option><?
						}?>
					</select>
				</td>
				<td>
					<select id="txtPayKind" name="txtPayKind" style="width:auto;">
						<option value="1">일반</option>
						<option value="3">비급여</option>
					</select>
					<div id="lblPayKind" style="width:auot; color:#ff0000; cursor:default; display:none;" title="바우처를 성성하지 않았습니다.">본인부담</div>
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
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom">수가명</th>
				<td class="left bottom"><div id="lblSugaNm"></div></td>
				<th class="center bottom"><div id="btnApplySuga">적용수가</div></th>
				<td class="left bottom"><span id="lblSugaCost">0</span>원 <span id="lblNurseLabel" style="display:none;">+ <span id="lblSugaEveing" style="color:#0000ff;">0</span>원 = <span id="lblSugaTot">0</span>원</span></td>
				<th class="center bottom"><span class="btn_pack m"><button type="button" onclick="lfExtraPayShow(this);">수당</button></span></th>
				<td class="center bottom last">
					<div id="loExtraPay" kind="" gbn="" class="left">
						<span id="lblApplyBathPay" value="0" class="clsBath clsBathCenter" style="font-weight:bold;">0</span>
						<span class="clsBathCenter clsCenter" style="padding:0 5px 0 5px;">=</span>
						<span id="lblApplyBathPay1" value="0" class="clsBath" style="font-weight:bold;">0</span>
						<span class="clsBath" style="padding:0 5px 0 5px;">/</span>
						<span id="lblApplyBathPay2" value="0" class="clsBath" style="font-weight:bold;">0</span>
						<span id="lblApplyNursePay" value="0" class="clsNurse" style="font-weight:bold;">0</span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="loClientIfno"
		statNot			="<?=$ynStatNot;?>"
		ynFamily90		="<?=$ynFamily90;?>"
		ynPartner		="<?=$ynPartner;?>"
		familyLimitCnt	="<?=$liFamilyLimitCnt;?>"
		careLimitCnt	="<?=$liCareLimitCnt;?>"
		bathRate1		="<?=$liRate1;?>"
		bathRate2		="<?=$liRate2;?>"
		careLvl         ="<?=$careLvl;?>"
		dementiaYn      ="<?=$dementiaYn;?>"
		style="display:none;"></div>
	<div id="loSvcInfo" ynMakeSvc="<?=$ynMakeSvc;?>" style="display:none;"></div>

	<script type="text/javascript">
		//제공서비스 변경이벤트
		$('#txtSvcKind').unbind('change').change(function(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $(this).val();
			var lsSvcLvl = $('#infoClient').attr('svcLvl');

			if (lsSvcCd == '0'){
				lfSetCareGbn();
			}
			
			
			//방문요양
			if (lsSvcCd == '0'){
				//제공시간 설정
				$('#txtSvcTime').show();
				
				if(lsSvcKind == '800'){
					$('#lblPos').text('간호사');
				}else if(lsSvcKind == '500'){
					$('#lblPrmStr').text('');
				}else {
					$('#lblPos').text('요양보호사');
					$('#lblPrmStr').text('/프로그램관리자');
				}

				lfSetSvcTime(lfGetKindGbn());
			}else if (lsSvcCd == '4'){

				$('#lblPos').text('활동보조인');

				if (lsSvcKind == '200'){
					$('#txtSvcTime').hide();
					$('#txtSvcTimeStr').show();		
				}else if (lsSvcKind == '500'){
					$('#lblPrmStr').text('');
					$('#txtSvcTime').show();
					$('#txtSvcTimeStr').hide();
				}else{
					$('#txtSvcTime').show();
					$('#txtSvcTimeStr').hide();
				}
				lfSetSvcTime(lfGetKindGbn());
			}

			var lbMem2 = false;

			if (
				(lsSvcKind == '500') ||			
				(lsSvcCd == '0' && lsSvcKind == '200' && $('#infoClient').attr('svcLvl') == '5') ||
				(lsSvcCd == '0' && lsSvcKind == '200' && $('#txtSvcKind option:selected').attr('val1') == 'dementia')
				){

				$('#lblPrmStr').show();
				lbMem2 = true;
			}else {
				$('#lblPrmStr').hide();
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
				$('#lblNurseLabel').hide();
				$('#lblSugaCost', $('#loSuga2')).parent().width(70);
			}else if (lsSvcKind == '800'){
				$('#lblSugaCost', $('#loSuga2')).parent().width(170);
				$('#lblNurseLabel').show();
			}

			if (lsSvcKind == '500'){
				$('.clsBath').show();
				$('.clsNurse').hide();
			}else if (lsSvcKind == '800'){
				$('.clsBath').hide();
				$('.clsNurse').show();
			}

			if (lsSvcKind == '500'){
				//목욕수당 설정이 없으면 강제로 개별설정한다.
				if ($('#loExtraPay').attr('kind') != 'BATH'){
					$('#loExtraPay').attr('kind','BATH');
				}
				if (!$('#loExtraPay').attr('gbn')){
					$('#loExtraPay').attr('gbn','PERSON'); //RATE
				}
			}else if (lsSvcKind == '800'){
				//간호수당 설정이 없으면 강제로 개별설정한다.
				if ($('#loExtraPay').attr('kind') != 'NURSE'){
					$('#loExtraPay').attr('kind','NURSE');
				}
				if ($('#loExtraPay').attr('gbn') != 'AMT'){
					$('#loExtraPay').attr('gbn','PERSON'); //AMT
				}
			}
			lfExtraPayDisplay($('#loExtraPay').attr('kind'),$('#loExtraPay').attr('gbn'));

			lbExtraHide();
			lfBabyAddHide();
			lfExtraPayHide();
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

				if (lsSvcCd == 0){
					$('div[id^="txtDay_"][pastYn="Y"]', $('#tblAssignCal')).attr('value','N').css('font-weight','normal').css('background-color','#EFEFEF');
				}
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
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
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

				if (lsSvcCd == '0' && lsSvcKind == '200' && $('#txtSvcKind option:selected').attr('val1') == 'dementia'){
					var liTmpProcTime = __time2min($('#txtToH').val()+$('#txtToM').val()) - __time2min($('#txtFromH').val()+$('#txtFromM').val());
					if (liTmpProcTime >= 120 && liTmpProcTime <= 180){
					}else{
						var laTmpAddTime = __add_time($('#txtFromH').val()+$('#txtFromM').val(), 120);
						$('#txtToH').val(laTmpAddTime[0]);
						$('#txtToM').val(laTmpAddTime[1]);
					}
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
				if (lsSvcCd == '4'){
					//lfSetProcTime();
				}else{
					lfSetEndTime();
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
				}else{
					if ($('#txtToH').val() && $('#txtToM').val()){
						lfFindSuga();
					}
				}
			}
		});

		//제공시간
		$('#txtSvcTime').unbind('change').change(function(){
			var lsSvcKind = $('#txtSvcKind').val();

			if (lsSvcKind != '500'){
				lfSetEndTime(true);
			}else{
				lfFindSuga();
			}
		});

		//요양보호사
		$('input:text[name="txtMemCd"]').unbind('click').click(function(){
			var liIdx = $(this).attr('id').split('txtMemCd').join('');

			//lfMemFind(liIdx);
			//공용함수로 옴김
			_planMemFind(liIdx,
				$('#centerInfo').attr('value'),
				$('#clientInfo').attr('value'),
				$('#planInfo').attr('svcCd'),
				($('#txtMemCd1').attr('code')+','+$('#txtMemCd2').attr('code')),
				($('#txtSvcKind').val() == '200' && $('#txtPayKind').val() == '2' && liIdx == '1' ? 'Y' : $('#txtSvcKind').val()),
				'lfMemFindResult');
		});

		/*
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
		*/

		/*
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
		*/

		$(document).ready(function(){
			if ($('#infoClient').attr('svcLvl') == '5'){
				$('#lblPrmStr').show();
			}else{
				$('#lblPrmStr').hide();
			}

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
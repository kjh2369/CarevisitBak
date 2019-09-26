<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	
	$sql = 'select svc_val
			  from client_his_old
			 where org_no       = \''.$code.'\'
			   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,\'%Y%m\') >= \''.$year.$month.'\'
			   and jumin = \''.$jumin.'\'
			';
	$codeVal = $conn->get_data($sql);	
	
	//주간보호노인돌봄
	if($codeVal == '2') $sCd = 'VOD01';
	

	?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="60px">
			<col width="20px">
			<col width="30px">
			<col width="85px">
			<col width="30px">
			<col width="85px">
			<col width="80px">
			<col width="80px">
			<col width="90px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">제공서비스</th>
				<th class="head">비용구분</th>
				<th class="head" colspan="2">직원</th>
				<th class="head" colspan="4">방문시간</th>
				<th class="head">제공시간/일</th>
				<th class="head">서비스단가</th>
				<th class="head">서비스시간/일</th>
				<th class="head last">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center"><?=$conn->_svcNm($svcCd);?></td>
				<td><?
					if ($svcCd == 'A' || $svcCd == 'B' || $svcCd == 'C'){?>
						<div class="center">본인부담</div><?
					}else{?>
						<input id="txtPayKind" name="txtPayKind" type="checkbox" value="Y" class="checkbox"><label for="txtPayKind">비급여</label><?
					}?>
				</td>
				<td class="last"><input id="txtMemCd1" name="txtMemCd" type="text" value="<?=$lsMemNm1;?>" code="<?=$ed->en($lsMemCd1);?>" calendar="<?=$lsMemCalendar[1];?>" style="width:100%;"></td>
				<td><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfMemClear('1');"></td>
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
				<td class="center"><div id="lblProcTime" class="right">0</div></td>
				<td class="center"><div id="lblSvcCost" value="0" class="right">0</div></td>
				<td class="center"><div id="lblLimitCnt" value="0" class="right">0</div></td>
				<td class="center last">&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%; display:;">
		<colgroup>
			<col width="50px">
			<col width="150px">
			<col width="65px">
			<col><?
			if ($svcCd == '3'){?>
				<col width="80px"><?
			}?>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom">수가명</th>
				<td class="left bottom"><input type="hidden" id="lblSugaCode" value="<?=$sCd;?>" /><div id="lblSugaNm"></div></td>
				<th class="center bottom"><div id="btnApplySuga">적용수가</div></th>
				<td class="left bottom last">기준수가[<span id="lblSugaCost" style="color:#0000ff;">0</span>] X 시간/일[<span id="lblSugaCnt" style="color:#0000ff;">0</span>] = 수가계[<span id="lblSugaTot" style="color:#0000ff;">0</span>]원</td><?
				if ($svcCd == '3' || $svcCd == 'A'){?>
					<td class="right bottom last"><span class="btn_pack m"><button type="button" onclick="lfBabyAddShow(this);">추가요금</button></span></td><?
				}?>
			</tr>
		</tbody>
	</table>
	<div id="loSvcInfo" ynMakeSvc="<?=$ynMakeSvc;?>" style="display:none;"></div>
	<script type="text/javascript">
		//방문시간 변경 이벤트
		var loTimer = null;

		$('input:text[name="txtTime"]').unbind('keyup').keyup(function(){
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
					lfFindSuga();
					return;
				}
			}
		}).unbind('change').change(function(){
			lfFindSuga();
		}).unbind('focus').focus(function(){
		}).unbind('blur').blur(function(){
			lfSetTimePos(this);

			if ($(this).attr('id') == 'txtFromH' || $(this).attr('id') == 'txtFromM'){
				lfSetEndTime();
			}else if ($(this).attr('id') == 'txtToH' || $(this).attr('id') == 'txtToM'){
				lfFindSuga();
			}
		});

		//비용구분
		$('#txtPayKind').unbind('click').click(function(){
			var lsSvcCd = $('#planInfo').attr('svcCd');
			if (lsSvcCd == '3'){
				$('#lblExtraTimeStr').text('횟수');
			}else{
				$('#lblExtraTimeStr').text('시간');
			}

			if (!$(this).attr('checked')){
				lbExtraHide();
				$('#btnApplySuga').text('적용수가');
				//$('#lblText1').show();
			}else{
				//비급여 설정
				$('#btnApplySuga').html('<span class="btn_pack small"><button type="button" onclick="lfExtraShow(this);">적용수가</button></span>');
				//$('#lblText1').hide();
			}

			//수가
			lfFindSuga();
		});

		//요양보호사
		$('input:text[name="txtMemCd"]').unbind('click').click(function(){
			var liIdx = $(this).attr('id').split('txtMemCd').join('');
			lfMemFind(liIdx);

			/*
			_planMemFind(
				liIdx
				, $('#centerInfo').attr('value')
				, $('#clientInfo').attr('value')
				, $('#planInfo').attr('svcCd')
				, ($('#txtMemCd1').attr('code')+','+$('#txtMemCd2').attr('code'))
				, ($('#txtSvcKind').val() == '200' && $('#txtPayKind').val() == '2' ? 'Y' : $('#txtSvcKind').val())
				, 'lfMemFindResult');
			*/
		});

		//기타유료 서비스단가 및 서비시일자
		function lfSetOtherSvcInfo(){
			if ($('#infoClient').attr('ynLoad') != 'Y'){
				return;
			}

			clearTimeout(loTimer);
			loTimer = null;

			var liSvcCost = __str2num($('#infoClient').attr('svcCost'));
			var liSvcCnt  = __str2num($('#infoClient').attr('svcCnt'));

			$('#lblSvcCost').attr('value',liSvcCost).text(__num2str(liSvcCost));
			$('#lblLimitCnt').attr('value',liSvcCnt).text(__num2str(liSvcCnt));
		}

		$(document).ready(function(){
			var lsSvcCd = $('#planInfo').attr('svcCd');

			if ($('#loSvcInfo').attr('ynMakeSvc') != 'Y'){
				$('#txtPayKind').attr('checked','checked').hide().parent().addClass('center');
				$('label[for="txtPayKind"]').css('color','#ff0000').text('본인부담');
				$('#btnApplySuga').html('<span class="btn_pack small"><button type="button" onclick="lfExtraShow(this);">적용수가</button></span>');
			}

			if (lsSvcCd >= '0' && lsSvcCd <= '4'){
				if (__str2num($('#lblSvcCost').attr('value')) == 0 && __str2num($('#lblLimitCnt').attr('value')) == 0){
					if ($('#infoClient').text() == 'Y'){
						var liLimitPay = $('#infoClient').attr('limitAmt');
						var liSvcCost  = $('#infoClient').attr('svcCost');

						$('#lblLimitCnt').attr('value',liLimitPay).text(__num2str(liLimitPay));
						$('#lblSvcCost').attr('value',liSvcCost).text(__num2str(liSvcCost));
					}
				}
			}else{
				loTimer = setInterval('lfSetOtherSvcInfo()', 10);
			}
			lbWinLoad = true;
		});
	</script>
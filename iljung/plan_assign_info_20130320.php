<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$type  = $_POST['type'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//수급자계약기간
	$sql = 'select date_format(from_dt,\'%Y%m%d\') as from_dt
			,      date_format(to_dt,\'%Y%m%d\') as to_dt
			  from client_his_svc
			 where org_no           = \''.$code.'\'
			   and jumin            = \''.$jumin.'\'
			   and svc_cd           = \''.$svcCd.'\'
			   and left(from_dt,4) <= \''.$year.'\'
			   and left(to_dt,4)   >= \''.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$idx = sizeof($loPeriod);

		$loPeriod[$idx] = array('from'=>$row['from_dt'],'to'=>$row['to_dt']);
	}

	$conn->row_free();

	//휴일리스트
	$sql = 'select cast(substring(mdate,7) as unsigned) as day
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate,6) = \''.$year.$month.'\'';
	$loHolidayList = $conn->_fetch_array($sql,'day');

	//if (intval($month) == 5){
	//	$loHolidayList[1] = array('day'=>1, 'nm'=>'근로자의 날', 'holiday'=>'N');
	//}

	//마감처리여부
	$ynClose = $conn->_isCloseResult($code, $year.$month);

	//급여마감여부
	$ynSalary = $conn->_isCloseSalary($code, $year.$month);
?>
<style>
	.divCalCont{
		width:100%;
		height:17px;
		line-height:17px;
	}
</style>
<div id="infoResult" ynLoad="N" ynClose="<?=$ynClose;?>" ynSalary="<?=$ynSalary;?>" style=""><?
	if ($type == 'PLAN'){?>
		<div style="float:left; width:auto; border:2px solid #0e69b0; border-left:none; border-right:1px solid #0e69b0;">
			<table id="tblAssignCal" class="my_table" style="width:auto;">
				<colgroup>
					<col width="36px" span="7">
				</colgroup>
				<tbody>
					<tr>
						<td class="center last" colspan="7">
							<div class="left" style="float:left; width:auto;">
								<img src="./img/btn_calen1.gif" onclick="_planAssingAll('A');" alt="전체선택">
								<img src="./img/btn_calen2.gif" onclick="_planAssingAll('W');" alt="평일선택">
								<img src="./img/btn_calen3.gif" onclick="_planAssingAll('H');" alt="휴일선택">
								<img src="./img/btn_calen4.gif" onclick="_planAssingAll('X');" alt="전택해제">
							</div>
							<div class="right" style="float:right; width:auto;">
								<div id="btnAssign1" style="display:none;">
									<img src="./img/btn_calen5.gif" onclick="lfGetPattern(this);" alt="패턴리스트">
									<img src="./img/btn_calen6.gif" onclick="lfAssign();" alt="배정">
								</div>
								<div id="btnAssign2" style="display:none;">
									<span style="font-weight:bold; color:#ff0000;">실 적 마 감</span>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th class="center"><div id="weekday_0" onclick="_planAssignWeekSel(this);" value="N" weekly="0" style="cursor:default; color:ff0000;">일</div></th>
						<th class="center"><div id="weekday_1" onclick="_planAssignWeekSel(this);" value="N" weekly="1" style="cursor:default; color:000000;">월</div></th>
						<th class="center"><div id="weekday_2" onclick="_planAssignWeekSel(this);" value="N" weekly="2" style="cursor:default; color:000000;">화</div></th>
						<th class="center"><div id="weekday_3" onclick="_planAssignWeekSel(this);" value="N" weekly="3" style="cursor:default; color:000000;">수</div></th>
						<th class="center"><div id="weekday_4" onclick="_planAssignWeekSel(this);" value="N" weekly="4" style="cursor:default; color:000000;">목</div></th>
						<th class="center"><div id="weekday_5" onclick="_planAssignWeekSel(this);" value="N" weekly="5" style="cursor:default; color:000000;">금</div></th>
						<th class="center last"><div id="weekday_6" onclick="_planAssignWeekSel(this);" value="N" weekly="6" style="cursor:default; color:0000ff;">토</div></th>
					</tr><?
					$liFirstWeekly = date('w', strtotime($year.$month.'01'));
					$liLastDay = intval($myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $year.$month.'01', 'Y-m-d'), 'd'));
					$liChkWeek = ceil(($liLastDay + $liFirstWeekly) / 7);
					$liWeekday = 0;
					$lbLastWeek= false;

					for($i=0; $i<$liFirstWeekly; $i++){
						if ($liWeekday % 7 == 0){?>
							<tr><?
						}?>
						<td class="center">&nbsp;</td><?
						$liWeekday ++;
					}

					if ($liWeekday > 0)
						$liWeekly = 1;
					else
						$liWeekly = 0;

					for($i=1; $i<=$liLastDay; $i++){
						if ($liWeekday % 7 == 0){
							$liWeekday = 0;
							$liWeekly ++;

							if ($liChkWeek <= $liWeekly) $lbLastWeek = true;

							if ($liFirstWeekly != 0){?>
								</tr><?
							}?>
							<tr><?
						}

						if (!empty($loHolidayList[$i]['nm'])){
							$liWeekIdx = 0;
							$lsFontClr = '#ff0000';
						}else{
							$liWeekIdx = $liWeekday;

							switch($liWeekday){
								case 0: $lsFontClr = '#ff0000'; break;
								case 6: $lsFontClr = '#0000ff'; break;
								default: $lsFontClr = '#000000';
							}
						}

						$lsDt  = $year.$month.($i < 10 ? '0' : '').$i;
						$lbAdd = false;

						foreach($loPeriod as $row){
							if ($row['from'] <= $lsDt && $row['to'] >= $lsDt){
								$lbAdd = true;
								break;
							}
						}?>
						<td class="center <?=$liWeekday == 6 ? 'last' : '';?> <?=$lbLastWeek ? 'bottom' : '';?>"><?
						if ($lbAdd){?>
							<div id="txtDay_<?=$i;?>" value="N" week="<?=$liWeekly;?>" weekly="<?=$liWeekIdx;?>" class="clsWeek_<?=$liWeekday;?>" style="cursor:default; font-weight:normal; background-color:#ffffff; color:<?=$lsFontClr;?>;" onclick="_planAssignDaySel(this);"><?=$i;?></div><?
						}else{?>
							<div id="tmpDay_0" value="N" week="<?=$liWeekly;?>" weekly="<?=$liWeekIdx;?>" class="" style="cursor:default; background-color:#efefef; color:#cccccc;"><?=$i;?></div><?
						}?>
						</td><?
						$liWeekday ++;
					}

					if ($liWeekday % 7 == 0){?>
						</tr><?
					}else{
						for($i=$liWeekday+1; $i<=7; $i++){?>
							<td class="center <?=$liWeekday == 6 ? 'last' : '';?> <?=$lbLastWeek ? 'bottom' : '';?>">&nbsp;</td><?
							$liWeekday ++;
						}?>
						</tr><?
					}?>
				</tbody>
			</table>
		</div><?
	}

	if ($type == 'PLAN'){?>
		<div style="float:left; width:auto; border:2px solid #0e69b0; border-left:none; border-right:none;"><?
	}else{?>
		<div style="float:left; width:auto; border:2px solid #0e69b0; border-left:none; border-right:none; border-top:none;"><?
	}?>

		<table class="my_table" style="width:100%;">
			<colgroup>
				<col><?
				if ($svcCd != 'A' && $svcCd != 'B' && $svcCd != 'C'){?>
					<col width="50px"><?
				}?>
			</colgroup>
			<tbody>
				<tr>
					<td class="center last" colspan="2"><?
						if ($svcCd >= '0' && $svcCd <= '4'){?>
							<div id="txtSvcData" value="" style="float:left; width:auto; padding-left:5px;"><?
								if ($svcCd == '0' || $svcCd == '1' || $svcCd == '2' || $svcCd == '3'){
									if ($svcCd == '0'){?>
										<span class="btn_pack m"><button type="button" onclick="lfSetSvcList('0');" style="cursor:default;">서비스별 내역</button></span><?
									}?>
									<span class="btn_pack m"><button type="button" onclick="lfSetSvcList('1');" style="cursor:default;">요양보호사별 내역</button></span>
									<span class="btn_pack m"><button type="button" onclick="lfSetSvcList('2');" style="cursor:default;">수가별 내역</button></span><?
								}?>
							</div><?
						}else{
							if ($svcCd == 'A'){?>
								<div class="bold" style="width:auto;">산모유료 서비스내역</div><?
							}else if ($svcCd == 'B'){?>
								<div class="bold" style="width:auto;">병원간병 서비스내역</div><?
							}else if ($svcCd == 'C'){?>
								<div class="bold" style="width:auto;">기타비급여 서비스내역</div><?
							}
						}?>
					</td>
				</tr>
				<tr>
					<td class="center top bottom"><?
						if ($svcCd == '4'){
							include('./plan_tbl_dis.php');
						}else{
							include('./plan_tbl_mem.php');
						}
						if ($svcCd == '0' || $svcCd == '1' || $svcCd == '2' || $svcCd == '3'){
							if ($svcCd == '0'){
								include('./plan_tbl_svc.php');
							}
							include('./plan_tbl_suga.php');
						}?>
					</td><?
					if ($svcCd != 'A' && $svcCd != 'B' && $svcCd != 'C'){?>
						<td class="center top bottom last"><?
							if ($svcCd == '0'){
								$liTblWidth = 90;
							}else if ($svcCd == '4'){
								$liTblWidth = 80;
							}else if ($svcCd == '3'){
								$liTblWidth = 180;
							}else if ($svcCd == '1' || $svcCd == '2'){
								$liTblWidth = 90;
							}?>
							<table class="my_table" style="width:<?=$liTblWidth;?>px;">
								<colgroup>
									<col>
								</colgroup>
								<tbody><?
									if ($svcCd == '0'){?>
										<tr>
											<th class="center bold last" style="height:25px;">급여한도금액</th>
										</tr>
										<tr>
											<td class="center last"><div class="right"><span id="txtLimitPay" value="0" style="font-weight:bold;">0</span>원</div></td>
										</tr>
										<tr>
											<th class="center bold last">청구한도금액</th>
										</tr>
										<tr>
											<td class="center last"><div class="right"><span id="txtClaimPay" value="0" style="font-weight:bold;">0</span>원</div></td>
										</tr>
										<tr>
											<th class="center bold last">청구한도잔액</th>
										</tr>
										<tr>
											<td class="center bottom last" style="height:27px;"><div class="right"><span id="txtBalance" value="0" style="font-weight:bold; color:#0000ff;">0</span>원</div></td>
										</tr><?
									}else if ($svcCd == '4'){?>
										<tr>
											<th class="center bold last" style="height:25px;">생성금액</th>
										</tr>
										<tr>
											<td class="center last"><div class="right"><span id="txtLimitPay" value="0" style="font-weight:bold;">0</span>원</div></td>
										</tr>
										<tr>
											<th class="center bold last">잔여금액</th>
										</tr>
										<tr>
											<td class="center bottom last"><div class="right"><span id="txtBalance" value="0" style="font-weight:bold; color:#0000ff;">0</span>원</div></td>
										</tr><?
									}else if ($svcCd == '3'){?>
										<tr>
											<th class="center bold" style="height:25px;">생성시간/일</th>
											<th class="center bold" style="height:25px;">잔여시간/일</th>
										</tr>
										<tr>
											<td class="center"><div class="right"><span id="txtLimitPay" value="0" style="font-weight:bold;">0</span>시간/일</div></td>
											<td class="center"><div class="right"><span id="txtBalance" value="0" style="font-weight:bold; color:#0000ff;">0</span>시간/일</div></td>
										</tr><?
									}else if ($svcCd == '1' || $svcCd == '2'){?>
										<tr>
											<th class="center bold last" style="height:25px;">생성시간/일</th>
										</tr>
										<tr>
											<td class="center last"><div class="right"><span id="txtLimitPay" value="0" style="font-weight:bold;">0</span>시간/일</div></td>
										</tr>
										<tr>
											<th class="center bold last">잔여시간/일</th>
										</tr>
										<tr>
											<td class="center last"><div class="right"><span id="txtBalance" value="0" style="font-weight:bold; color:#0000ff;">0</span>시간/일</div></td>
										</tr><?
									}?>
								</tbody>
							</table>
						</td><?
					}?>
				</tr>
			</tbody>
		</table>
	</div><?

	if ($svcCd == '3' || $svcCd == 'A'){
		include('./plan_tbl_babyadd.php');
	}?>
</div><?
include_once('../inc/_db_close.php');?>
<script type="text/javascript">
	var lsCalenda1 = '';
	var lsCalenda2 = '';
	var lsMemCode1 = '';
	var lsMemCode2 = '';
	var loTimerAssign = null;
	var loTimerBabyAdd = null;
	var loTimer = null;

	$(document).ready(function(){
		lfLoad();
	});

	function lfLoad(){
		var lsSvcCd = $('#planInfo').attr('svcCd');

		_planSetLimitAmt();

		var ynClose = $('#infoResult').attr('ynClose');

		if (ynClose == 'Y'){
			$('#btnAssign2').show();
		}else{
			$('#btnAssign1').show();
		}

		var h = lfGetHeight();

		if (lsSvcCd == '4'){
			try{
				$('#tblSvcList3').height(h);
			}catch(e){
			}
			$('#tblSvcList3').show();
		}else{
			$('div:first', $('#tblSvcList1')).height(h);
			$('div:first', $('#tblSvcList2')).height(h);

			if (lsSvcCd == '0'){
				lfSetSvcList('0');
			}else{
				lfSetSvcList('1');
			}
		}

		$('#infoResult').attr('ynLoad','Y');
	}

	//배정
	function lfAssign(){
		var lsSvcCd   = $('#planInfo').attr('svcCd'); //서비스구분
		var lsPayKind = $('#txtPayKind').val(); //제공서비스
		var liCnt     = $('div[id^="txtDay_"][value="Y"]').length;

		if (liCnt == 0){
			alert('배정할 일자를 선택하여 주십시오.');
			return;
		}

		lfShowLoading();

		//lfWriteCalendar(lsSvcCd);
		//lbExtraHide();
		//lfBabyAddHide();
		setTimeout('lfDrawCalendar()',10);

		//패턴저장
		setTimeout('lfSetPattern()',10);

		self.focus();
	}

	function lfShowLoading(){
		$('#loLoading').html(__get_loading());

		var w = ($(window).width() - $('#loLoading').width()) / 2;

		$('#loLoading').css('left', w).show();
	}

	function lfDrawCalendar(){
		var lsSvcCd = $('#planInfo').attr('svcCd');

		lfWriteCalendar(lsSvcCd);
		lbExtraHide();
		lfBabyAddHide();

		$('#loLoading').hide();
	}

	//방문요양 배정 체크
	function lfWriteCalendar(asSvcCd){
		var lsSvcCd    = asSvcCd;
		var lsSvcKind  = '';
		var liLimitAmt = 0;
		var liClaimAmt = 0;
		var loMstObj = null;

		if ($('#txtFromH').val().length != $('#txtFromH').attr('maxlength')){
			alert('방문시작 시간을 입력하여 주십시오.');
			$('#txtFromH').focus();
			return false;
		}else if ($('#txtFromM').val().length != $('#txtFromM').attr('maxlength')){
			alert('방문시작 시간을 입력하여 주십시오.');
			$('#txtFromM').focus();
			return false;
		}else if ($('#txtToH').val().length != $('#txtToH').attr('maxlength')){
			alert('방문종료 시간을 입력하여 주십시오.');
			$('#txtToH').focus();
			return false;
		}else if ($('#txtToM').val().length != $('#txtToM').attr('maxlength')){
			alert('방문종료 시간을 입력하여 주십시오.');
			$('#txtToM').focus();
			return false;
		}

		if ($('#txtMemCd1').val() == ''){
			alert('요양보호사를 선택하여 주십시오.');
			return false;
		}

		if (lsSvcCd == '0'){
			lsSvcKind  = $('#txtSvcKind').val();

			if ('<?=$lbLimitSet;?>' == '1'){
				var liClaim = {'200':__str2num($('#infoClient').attr('claimCare')),'500':__str2num($('#infoClient').attr('claimBath')),'800':__str2num($('#infoClient').attr('claimNurse'))};

				if (liClaim['200'] > 0 ||
					liClaim['500'] > 0 ||
					liClaim['800'] > 0){
					liLimitAmt = liClaim[lsSvcKind];
				}else{
					liLimitAmt = __str2num($('#infoClient').attr('claimAmt'));
				}
			}else{
				liLimitAmt = __str2num($('#infoClient').attr('claimAmt'));
			}
		}else if (lsSvcCd == '4'){
			lsSvcKind  = $('#txtSvcKind').val();
			liLimitAmt = __str2num($('#txtLimitPay').attr('value'));
		}else if (lsSvcCd == '1' || lsSvcCd == '2' || lsSvcCd == '3'){
			lsSvcKind  = '2'+lsSvcCd;
			liLimitAmt = __str2num($('#lblLimitCnt').attr('value'));
		}else if (lsSvcCd == 'A'){
			lsSvcKind  = '31';
			liLimitAmt = 0; //__str2num($('#infoClient').attr('svcCnt'));
		}else if (lsSvcCd == 'B'){
			lsSvcKind  = '32';
			liLimitAmt = 0;
		}else{
			lsSvcKind  = '33';
			liLimitAmt = 0;
		}

		if (lsSvcKind == '500' || lsSvcKind == '800'){
			if (lsSvcKind == '500'){
				var lsMemNm1 = $('#txtMemCd1').val()
				,	lsMemNm2 = $('#txtMemCd2').val();

				if (lsMemNm2 == ''){
					alert('요양보호사를 선택하여 주십시오.');
					return false;
				}
			}else if (lsSvcKind == '800'){
				var liPay = __str2num($('#lblApplyNursePay').text());

				if (liPay == 0){
					alert('간호수당이 입력되지 않았습니다.\n간호수당을 입력하여 주십시오.');
					return false;
				}
			}
		}

		if (lsSvcCd >= '0' && lsSvcCd <= '4'){
			if ('<?=$lbLimitSet;?>' == '1' && lsSvcCd == '0'){
				var liClaimVal = {'200':__str2num($('#lblSvcAmtC_1').text()),'500':__str2num($('#lblSvcAmtB_1').text()),'800':__str2num($('#lblSvcAmtN_1').text())};

				if (liClaim['200'] > 0 ||
					liClaim['500'] > 0 ||
					liClaim['800'] > 0){
					liClaimAmt = liClaimVal[lsSvcKind];
				}else{
					liClaimAmt = liLimitAmt - __str2num($('#txtBalance').attr('value'));
				}
			}else{
				liClaimAmt = liLimitAmt - __str2num($('#txtBalance').attr('value'));
			}
		}else{
			liClaimAmt = 0;
		}

		if (lsSvcKind == '500'){
			if ($('#txtMemCd2').val() == ''){
				alert('요양보호사를 선택하여 주십시오.');
				return false;
			}
			lsCalenda2 = __parseStr($('#txtMemCd2').attr('calendar'));
			lsMemCode2 = $('#txtMemCd2').attr('code');
		}

		lsCalenda1 = __parseStr($('#txtMemCd1').attr('calendar'));
		lsMemCode1 = $('#txtMemCd1').attr('code');

		loMstObj = $('div[id^="txtDay_"][value="Y"]');

		var liSudangVal1 = 0
		,	liSudangVal2 = 0
		,	liSudangPay  = 0
		,	lsSudangKind = '';

		if (lsSvcKind == '500'){
			liSudangVal1 = __str2num($('#lblApplyBathPay1').attr('value'));
			liSudangVal2 = __str2num($('#lblApplyBathPay2').attr('value'));
		}
		liSudangPay  = __str2num($('#loSuga').attr('sudangPay'));
		lsSudangKind = $('#loExtraPay').attr('gbn');

		var sugaCd      = $('#loSuga').attr('code')
		,	sugaNm      = $('#loSuga').attr('name')
		,	sugaCost    = __str2num($('#loSuga').attr('cost'))
		,	costEvening = __str2num($('#loSuga').attr('costEvening'))
		,	costNight   = __str2num($('#loSuga').attr('costNight'))
		,	costTotal   = __str2num($('#loSuga').attr('costTotal'))
		,	sudangPay   = liSudangPay
		,	sudangKind  = lsSudangKind
		,	sudangVal1  = liSudangVal1
		,	sudangVal2  = liSudangVal2
		,	timeEvening = __str2num($('#loSuga').attr('timeEvening'))
		,	timeNight   = __str2num($('#loSuga').attr('timeNight'))
		,	ynEvening   = $('#loSuga').attr('ynEvening')
		,	ynNight     = $('#loSuga').attr('ynNight')
		,	ynHoliday   = $('#loSuga').attr('ynHoliday')
		,	costBipay   = __str2num($('#loSuga').attr('costBipay'))
		,	costHoliday = __str2num($('#loSuga').attr('costHoliday'))
		,	procTime    = __str2num($('#loSuga').attr('procTime'));

		$(loMstObj).each(function(){
			var day = __str2num($(this).text());
			var obj = $('#loCal_'+day);
			var cnt = $('.clsCal', $(obj)).length;
			var week = $(this).attr('week');

			var lsStyle  = 'clear:both; text-align:left; padding-left:3px;';
				lsStyle += 'border-top:1px dotted #666666;';

			var ynHoliday = $(obj).attr('ynHoliday');
			var ynBipay = 'N';
			var ynFamily = ($('#txtPayKind').val() == '2' ? 'Y' : 'N');

			if (ynFamily == 'Y'){
				ynHoliday = 'N';
			}

			var lsFrom = $('#txtFromH').val()+':'+$('#txtFromM').val(); //시작시간
			var lsTo   = $('#txtToH').val()+':'+$('#txtToM').val(); //종료시간
			var lsTime = lsFrom+'~'+lsTo;

			var lsMem1 = $('#txtMemCd1').val();
			var lsMem2 = $('#txtMemCd2').val() ? $('#txtMemCd2').val() : '';
			var lsMem  = lsMem1+(lsMem2 ? '/'+lsMem2 : '');

			var lsSugaNm = sugaNm;//$('#lblSugaNm').text();

			if (lsSvcCd == '0' || lsSvcCd == '4'){
				ynBipay = ($('#txtPayKind').val() == '3' ? 'Y' : 'N');
			}else{
				if ($('#txtPayKind').attr('checked')){
					ynBipay = 'Y';
				}else{
					ynBipay = 'N';
				}
			}

			if (ynBipay == 'Y'){
				lsSugaNm += '(<span style=\'color:#ff0000;\'>비</span>)';
			}else if (ynHoliday == 'Y'){
				if (lsSvcCd == '0'){
					if (lsSvcKind != '500'){
						lsSugaNm += '(<span style=\'color:#ff0000;\'>30%</span>)';
					}
				}
			}

			var lsDt = $('#planInfo').attr('year')+'-'+$('#planInfo').attr('month')+'-'+(day < 10 ? '0' : '')+day;
			var liBipayCost = __str2num($('#txtBipayCost3').val()); //비급여단가
			var lsExtraKind = $('input:radio[name="txtExtraKind"]:checked').val();
			var ynRealPay   = $('input:radio[name="ynRealPay"]:checked').val(); //실비지급여부
			var liRealPay   = __str2num($('#txtRealPay').val()); //실비금액

			if (ynRealPay != 'Y') liRealPay = 0;

			var lsSugaCd = sugaCd
			,	liCostS  = sugaCost
			,	liCostE  = costEvening
			,	liCostN  = costNight
			,	liCostT  = costTotal
			,	liTimeE  = timeEvening
			,	liTimeN  = timeNight;

			if (ynBipay == 'Y'){
				liCostS  = costBipay
				liCostE  = 0;
				liCostN  = 0;
				liCostT  = liCostS;
				liTimeE  = 0;
				liTimeN  = 0;
			}else if (ynHoliday == 'Y'){
				if (lsSvcCd == '0' && lsSvcKind != '500'){
					lsSugaCd = lsSugaCd.substring(0,2)+'H'+lsSugaCd.substring(3,5);
				}

				if (lsSvcCd != 'C'){
					liCostS  = costHoliday;
					liCostE  = 0;
					liCostN  = 0;
					liCostT  = liCostS;
					liTimeE  = 0;
					liTimeN  = 0;
				}
			}

			if (ynBipay != 'Y'){
				//일정 급여
				if (lsSvcCd == '0' || lsSvcCd == '4'){
					liClaimAmt += liCostT;
				}else if (lsSvcCd == '1' || lsSvcCd == '2'){
					liClaimAmt += procTime;
				}else if (lsSvcCd == '3'){
					liClaimAmt += 1;
				}

				var lbDuplicate = 1;

				if (lsSvcCd != '4'){
					lbDuplicate = lfDuplicate('loCal',lsSvcKind,day,cnt,lsFrom,lsTo,$('#txtMemCd1').attr('code')+'|'+$('#txtMemCd2').attr('code'),week,1);

					if (liLimitAmt >= liClaimAmt){
					}else{
						//한도초과
						if (lbDuplicate == 1){
							lbDuplicate = 9;
						}
					}
				}
			}else{
				//일정 비급여
				var lbDuplicate = 1;
			}

			//산모신생아 추가요금
			var lsBabyAddPay = '';
			var lsDuplicate = 'N';

			if (lsSvcCd == '0'){
				//중복여부
				if (lbDuplicate != 1 && lbDuplicate != 9){
					lsDuplicate  = 'Y';
				}
			}else if (lsSvcCd == '3' || lsSvcCd == 'A'){
				lsBabyAddPay = __str2num($('#txtNotSchoolCnt').val())+'/'+__str2num($('#txtNotSchoolCost').val())+'/'+__str2num($('#txtNotSchoolAmt').attr('value'))+'/'
							 + __str2num($('#txtSchoolCnt').val())+'/'+__str2num($('#txtSchoolCost').val())+'/'+__str2num($('#txtSchoolAmt').attr('value'))+'/'
							 + __str2num($('#txtFamilyCnt').val())+'/'+__str2num($('#txtFamilyCost').val())+'/'+__str2num($('#txtFamilyAmt').attr('value'))+'/';

				if ($('#ynInHome').attr('checked')){
					lsBabyAddPay += __str2num($('#txtInHomeCost').val())+'/';
				}else{
					lsBabyAddPay += '0/';
				}

				lsBabyAddPay += __str2num($('#txtHolidayCost').val())+'/'
							 +  __str2num($('#txtAddTotAmt').attr('value'));

				//중복여부
				if (lbDuplicate != 1){
					lsDuplicate  = 'Y';
				}
			}else{
				//중복여부
				if (lbDuplicate != 1){
					lsDuplicate  = 'Y';
				}
			}

			/*
			if ('<?=$debug;?>' == '1'){
				if (lsSvcCd == '4' && lsSvcKind == '200'){
					var addTime = 0;

					$('div[id^="loCal_'+day+'"]').each(function(){
						if ($(this).attr('day') != undefined){
							addTime += __str2num($(this).attr('timeNight'));
						}
					});

					if (addTime > 0){
						liTimeN -= addTime;

						if (liTimeN < 0){
							liTimeN = 0;
						}

						var tmpProcTime = procTime / 60;

						tmpProcTime -= liTimeN;

						liCostT = tmpProcTime * liCostS + liTimeN * liCostN;
					}
				}
			}
			*/

			var html = '<div id="loCal_'+day+'_'+cnt+'" class="clsCal" style="'+lsStyle+'" onclick="lfShowCalendar(this,\''+lbDuplicate+'\');" onmouseover="_planMouseOver(this);" onmouseout="_planMouseOut(this);"'
					 + ' day="'+day+'"'
					 + ' cnt="'+cnt+'"'
					 + ' week="'+week+'"'
					 + ' svcKind="'+lsSvcKind+'"'
					 + ' from="'+lsFrom+'"'
					 + ' to="'+lsTo+'"'
					 + ' memCd1="'+$('#txtMemCd1').attr('code')+'"'
					 + ' memNm1="'+lsMem1+'"'
					 + ' memCd2="'+$('#txtMemCd2').attr('code')+'"'
					 + ' memNm2="'+lsMem2+'"'
					 + ' duplicate="'+lsDuplicate+'"'
					 + ' sugaName="'+sugaNm+'"'
					 + ' sugaCd="'+lsSugaCd+'"'
					 + ' sugaNm="'+lsSugaNm+'"'
					 + ' procTime="'+procTime+'"'
					 + ' cost="'+liCostS+'"'
					 + ' costEvening="'+liCostE+'"'
					 + ' costNight="'+liCostN+'"'
					 + ' costTotal="'+liCostT+'"'
					 + ' sudangPay="'+sudangPay+'"'
					 + ' sudangKind="'+sudangKind+'"'
					 + ' sudangVal1="'+sudangVal1+'"'
					 + ' sudangVal2="'+sudangVal2+'"'
					 + ' timeEvening="'+liTimeE+'"'
					 + ' timeNight="'+liTimeN+'"'
					 + ' ynNight="'+ynNight+'"'
					 + ' ynEvening="'+ynEvening+'"'
					 + ' ynHoliday="'+ynHoliday+'"'
					 + ' ynBipay="'+ynBipay+'"'
					 + ' ynFamily="'+ynFamily+'"'
					 + ' extraKind="'+lsExtraKind+'"'
					 + ' bipayCost="'+liBipayCost+'"'
					 + ' ynRealPay="'+ynRealPay+'"'
					 + ' realPay="'+liRealPay+'"'
					 + ' babyAddPay="'+lsBabyAddPay+'"'
					 + ' ynAddRow="N"'
					 + ' ynSave="N"'
					 + ' stat="9"'
					 + ' seq="'+cnt+'"'
					 + ' svcSeq=""'
					 + ' request="PLAN"'
					 + ' modifyPos="N"'
					 + '>'
					 + '<div class="divCalCont" style="font-weight:bold; cursor:default;">'
					 + '	<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><img src="../image/btn_close.gif" onclick="return lfCalRemove(\''+day+'\',\''+cnt+'\');" style="margin-top:3px;"></div>'
					 + '	<div id="lblTimeStr" style="float:left; width:auto; cursor:default;">'+lsTime+'</div>'
					 + '</div>'
					 + '<div id="lblMemStr" class="divCalCont" style="cursor:default;">'+lsMem+'</div>'
					 + '<div id="lblSugaStr" class="divCalCont" style="cursor:default;">'
					 + '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+lsSugaNm+'</div>';

			if (lsSugaCd.substring(0,2) == 'CB'){
				html += '<div style="float:left; width:auto;"><img src="../image/icon_bath.png" style="width:15px; height:14px;"></div>';
			}else if (lsSugaCd.substring(0,2) == 'CN'){
				html += '<div style="float:left; width:auto;"><img src="../image/icon_nurs.png" style="width:15px; height:14px;"></div>';
			}

			html += '</div>';

			if (lbDuplicate == 1){
				html += '<div class="divCalCont" style="display:none;"><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold; cursor:default;"></span></div>';
			}else{
				html += '<div class="divCalCont"><span id="divErrorMsg" style="color:'+(lsDuplicate == 'Y' ? '#ff0000' : '#0000ff')+'; font-size:11px; font-weight:bold; cursor:default;">'+_planErrorMsg(lbDuplicate)+'</span></div>';
			}

			html += '</div>';

			if (cnt > 0){
				$('.clsCal:last', $(obj)).after(html);
			}else{
				$(obj).html(html);
			}

			$('.clsCal:first', $(obj)).css('border-top','none');
		});

		//일정 데이타 통계
		if (lsSvcCd == '4'){
			loTimerAssign = setInterval('lfCalendarDisData(1)', 50);
		}else{
			loTimerAssign = setTimeout('lfCalendarData(1)', 10);
		}
	}

	//패턴저장
	function lfSetPattern(){
		var lsSvcCd = $('#planInfo').attr('svcCd');
		var lsDt    = '';
		var lsKey   = $('#loSuga').attr('code')+'_'+$('#txtFromH').val()+$('#txtFromM').val();

		$('div[id^="txtDay_"][value="Y"]').each(function(){
			lsDt += __str2num($(this).text())+'/';
		});

		var para = '';
			para = 'svcKind='+$('#txtSvcKind').val()
				 + '&payKind='+$('#txtPayKind').val()
				 + '&sugaNm='+$('#loSuga').attr('name')
				 + '&memCd1='+$('#txtMemCd1').attr('code')
				 + '&memNm1='+$('#txtMemCd1').val()
				 + '&memCd2='+($('#txtMemCd2').attr('code') ? $('#txtMemCd2').attr('code') : '')
				 + '&memNm2='+($('#txtMemCd2').val() ? $('#txtMemCd2').val() : '')
				 + '&fromH='+$('#txtFromH').val()
				 + '&fromM='+$('#txtFromM').val()
				 + '&toH='+$('#txtToH').val()
				 + '&toM='+$('#txtToM').val()
				 + '&svcTime='+$('#txtSvcTime').val()
				 + '&dt='+lsDt;

		$.ajax({
			type : 'POST'
		,	url  : './plan_pattern.php'
		,	data : {
				code  : $('#centerInfo').attr('value')
			,	jumin : $('#clientInfo').attr('value')
			,	svcCd : $('#planInfo').attr('svcCd')
			,	yymm  : $('#planInfo').attr('year')+$('#planInfo').attr('month')
			,	key   : lsKey
			,	mode  : 'set'
			,	para  : para
			}
		,	success: function(result){
				if (result != 1){
					alert('패턴 저장중 오류가 발생하였습니다.');
				}
			}
		});
	}
	function lfGetPattern(obj){
		var lsTbl = '';

		$.ajax({
			type : 'POST'
		,	url  : './plan_pattern.php'
		,	data : {
				code  : $('#centerInfo').attr('value')
			,	jumin : $('#clientInfo').attr('value')
			,	svcCd : $('#planInfo').attr('svcCd')
			,	yymm  : $('#planInfo').attr('year')+$('#planInfo').attr('month')
			,	mode  : 'get'
			}
		,	success: function(pattern){
				if (!pattern) return;

				var pattern = pattern.split(';');

				lsTbl = '<table class="my_table my_border_blue" style="width:470px; background-color:#ffffff;">'
					  + '	<colgroup>'
					  + '		<col width="40px">'
					  + '		<col width="100px">'
					  + '		<col width="80px">'
					  + '		<col width="60px">'
					  + '		<col width="130px">'
					  + '		<col>'
					  + '	</colgroup>'
					  + '	<thead>'
					  + '		<tr>'
					  + '			<th class="head">No</th>'
					  + '			<th class="head">서비스</th>'
					  + '			<th class="head">제공시간</th>'
					  + '			<th class="head">일자</th>'
					  + '			<th class="head">담당직원</th>'
					  + '			<th class="head"><div style="text-align:right; padding-right:5px;"><img src="../image/btn_close.gif" onclick="$(\'#patternCont\').hide(300);"></div></th>'
					  + '		</tr>'
					  + '	</thead>'
					  + '	<tbody>';

				for(var i=0; i<pattern.length; i++){
					if (!pattern[i]) break;

					var pat = pattern[i].split('|');
					var val = __parseStr(pat[1]);

					lsTbl += '<tr>'
						  +  '<td class="center">'+(i+1)+'</td>'
						  +  '<td class="left">'+val['sugaNm']+'</td>'
						  +  '<td class="center">'+val['fromH']+':'+val['fromM']+'~'+val['toH']+':'+val['toM']+'</td>'
						  +  '<td class="center">'+(val['dt'].split('/').length-1)+'일</td>'
						  +  '<td class="left">'+val['memNm1']+(val['memNm2'] ? '/'+val['memNm2'] : '')+'</td>'
						  +  '<td class="center">'
						  +	 '<img src="./img/btn_calen9.gif" onclick="lfPutPattern(\''+pattern[i]+'\');" style="margin-left:3px;">'
						  +  '<img src="./img/btn_calen10.gif" onclick="lfDelPattern(\''+pat[0]+'\');" style="margin-left:3px; margin-right:3px;">'
						  +  '</tr>';
				}

				lsTbl += '</tbody>'
					  +  '</table>';

				if (!obj){
					$('#patternCont').html(lsTbl);
					return;
				}

				var l = $(obj).offset().left;
				var t = $(obj).offset().top + $(obj).height();

				$('#patternCont').css('left',l).css('top',t).html(lsTbl).show(300);
			}
		});
	}
	function lfPutPattern(obj){
		var val = __parseStr(obj.split('|')[1]);

		$('#txtSvcKind').val(val['svcKind']).change();
		$('#txtPayKind').val(val['payKind']).change();
		$('#txtMemCd1').attr('code',val['memCd1']);
		$('#txtMemCd1').val(val['memNm1']);
		$('#txtMemCd2').attr('code',val['memCd2']);
		$('#txtMemCd2').val(val['memNm2']);
		$('#txtFromH').val(val['fromH']);
		$('#txtFromM').val(val['fromM']);
		$('#txtToH').val(val['toH']);
		$('#txtToM').val(val['toM']);
		$('#txtSvcTime').val(val['svcTime']);

		if (val['svcKind'] == '500'){
			$('#txtMemCd2').css('background-color','#ffffff').attr('disabled',false);
		}else{
			$('#txtMemCd2').css('background-color','#efefef').attr('disabled',true);
		}

		$('div[id^="txtDay_"]').attr('value','N').css('font-weight','normal').css('background-color','#ffffff');

		var laDt = val['dt'].split('/');

		for(var i=0; i<laDt.length-1; i++){
			//$('div[id="txtDay_'+laDt[i]+'"]').attr('value','Y').css('font-weight','bold').css('background-color','#fffabb');
			_planAssignDaySel($('div[id="txtDay_'+laDt[i]+'"]'));
		}

		//일정리스트
		if (val['memCd1']) setTimeout('lfMemCalendar("1","'+val['memCd1']+'")',1);
		if (val['memCd2']) setTimeout('lfMemCalendar("2","'+val['memCd2']+'")',1);

		setTimeout('lfFindSuga()',300);

		$('#patternCont').hide(300);
	}
	function lfDelPattern(asKey){
		$.ajax({
			type : 'POST'
		,	url  : './plan_pattern.php'
		,	data : {
				code  : $('#centerInfo').attr('value')
			,	jumin : $('#clientInfo').attr('value')
			,	svcCd : $('#planInfo').attr('svcCd')
			,	yymm  : $('#planInfo').attr('year')+$('#planInfo').attr('month')
			,	key   : asKey
			,	mode  : 'remove'
			}
		,	success: function(result){
				lfGetPattern();
			}
		});
	}

	//일정표 정리
	function lfChkCalendar(){
		clearTimeout(loTimerAssign);
		loTimerAssign = null;

		var lsSvcCd    = $('#planInfo').attr('svcCd'); //서비스구분
		var lsSvcKind  = $('#txtSvcKind').val();
		var liLimitAmt = 0;
		var liClaimAmt = 0;
		var liClaimVal = {'200':0,'500':0,'800':0};
		var liProcTime = 0;
		var lsMsg = '';

		if (lsSvcCd == '0'){
			if ('<?=$lbLimitSet;?>' == '1'){
				var liClaim = {'200':__str2num($('#infoClient').attr('claimCare')),'500':__str2num($('#infoClient').attr('claimBath')),'800':__str2num($('#infoClient').attr('claimNurse'))};

				if (liClaim['200'] > 0 ||
					liClaim['500'] > 0 ||
					liClaim['800'] > 0){
					liLimitAmt = 0;
				}else{
					liLimitAmt = __str2num($('#infoClient').attr('claimAmt'));
				}
			}else{
				liLimitAmt = __str2num($('#infoClient').attr('claimAmt'));
			}
		}else{
			liLimitAmt = __str2num($('#lblLimitCnt').attr('value'));
		}

		$('div[id^="loCal_"][class="divCalObj"]').each(function(){
			var liDay = $(this).attr('id').split('loCal_').join('');

			$('div[id^="loCal_'+liDay+'_"]').each(function(){
				var lsObj = $(this).attr('id').split('_');
				var liCnt = lsObj[lsObj.length-1];

				if ($(this).attr('ynBipay') != 'Y'){
					if (lsSvcCd == '0'){
						if ('<?=$lbLimitSet;?>' == '1'){
							if (liClaim['200'] > 0 ||
								liClaim['500'] > 0 ||
								liClaim['800'] > 0){
								liClaimVal[$(this).attr('svcKind')] += __str2num($(this).attr('costTotal'));
							}else{
								liClaimAmt += __str2num($(this).attr('costTotal'));
							}
						}else{
							liClaimAmt += __str2num($(this).attr('costTotal'));
						}
					}else{
						//liClaimAmt += __str2num($(this).attr('procTime'));

						liProcTime = __str2num($(this).attr('procTime'));

						if (lsSvcCd == '3'){
							liProcTime = 1;
						}else{
							if (liProcTime >= 60){
								liProcTime = Math.round(liProcTime / 60);
							}
						}

						liClaimAmt += liProcTime;
					}
				}

				var lbDuplicate = 1;

				if ($(this).attr('ynBipay') != 'Y'){
					lbDuplicate = lfDuplicate('loCal'
									,$(this).attr('svcKind')
									,liDay
									,liCnt
									,$(this).attr('from')
									,$(this).attr('to')
									,$(this).attr('memCd1')+'|'+$(this).attr('memCd2')
									,$(this).attr('week'));

					if ('<?=$lbLimitSet;?>' == '1' && lsSvcCd == '0'){
						if (liClaim['200'] > 0 ||
							liClaim['500'] > 0 ||
							liClaim['800'] > 0){
							if (liClaim[$(this).attr('svcKind')] >= liClaimVal[$(this).attr('svcKind')]){
							}else{
								//한도초과
								if (lbDuplicate == 1){
									lbDuplicate = 9;
								}
							}
						}else{
							if (liLimitAmt >= liClaimAmt){
							}else{
								//한도초과
								if (lbDuplicate == 1){
									lbDuplicate = 9;
								}
							}
						}
					}else{
						if (liLimitAmt >= liClaimAmt){
						}else{
							//한도초과
							if (lbDuplicate == 1){
								lbDuplicate = 9;
							}
						}
					}
				}

				var lsDuplicate = 'N';

				if (lsSvcCd == '0'){
					if (lbDuplicate != 1 && lbDuplicate != 9){
						lsDuplicate  = 'Y';
					}
				}else if (lsSvcCd == 'C'){
					lbDuplicate = 1;
				}else{
					if (lbDuplicate != 1){
						lsDuplicate  = 'Y';
					}
				}

				lsMsg = _planErrorMsg(lbDuplicate);

				$(this).attr('duplicate',lsDuplicate);

				if (lbDuplicate == 1){
					$('#divErrorMsg', this).hide();
				}else{
					$('#divErrorMsg', this).css('color',(lsDuplicate == 'Y' ? '#ff0000' : '#0000ff')).text(lsMsg).parent().show();
				}
			});

			//$('.clsCal:first', $(this)).css('border-top','none');
		});

		if (lsSvcCd == '3' || lsSvcCd == 'A'){
			loTimerBabyAdd = setTimeout('lfGetBabyAddPay()', 10);
		}
	}

	//중복체크
	function lfDuplicate(asObj, asSvcKind, aiDay, aiCnt, asFrom, asTo, asMemCd, aiWeek, aiCareCnt){
		var lsSvcCd = $('#planInfo').attr('svcCd'); //서비스구분
		var obj = $('div[id^="'+asObj+'_'+aiDay+'_"]');

		var lsSugaCd = $(obj).attr('sugaCd');
		var ynBipay  = $(obj).attr('ynBipay');
		var liFrom   = _planTime2Min(asFrom);
		var liTo     = _planTime2Min(asTo);
		var lsMemCd  = asMemCd.split('|');
		var liResult = 1;

		if (liTo < liFrom)
			liTo = liTo + 24 * 60;

		if (!aiCareCnt)
			aiCareCnt = 0;

		var laCal, laTime, liChkF, liChkT, liGabF, liGabT;
		var liBathWeekCnt = __str2num($('#infoClient').attr('bathWeekCnt')); //주간목욕가능횟수

		if (lsMemCd[0] == lsMemCode1){
			if (lsCalenda1[aiDay]){
				laCal = lsCalenda1[aiDay].split(';');

				for(var i=0; i<laCal.length; i++){
					laTime = laCal[i].split('/');
					liChkF = _planTime2Min(laTime[0]);
					liChkT = _planTime2Min(laTime[1]);

					//if ('<?=$debug;?>' == '1'){
					//	alert(lsMemCd[0]+'\n'+lsMemCode1+
					//		liFrom+'~'+liTo+'\n'+
					//		liChkF+'~'+liChkT);
					//}

					if (liChkF + liChkT > 0){
						if ((liFrom <= liChkF && liTo > liChkF) ||
							(liFrom < liChkT && liTo >= liChkT) ||
							(liFrom > liChkF && liTo < liChkT)){
							//if ('<?=$debug;?>' == '1'){
							//	alert(lsCalenda1[aiDay]);
							//}
							return 2;
						}
					}
				}
			}
		}

		if (lsMemCd[1] == lsMemCode2){
			if (lsCalenda2[aiDay]){
				laCal = lsCalenda2[aiDay].split(';');

				for(var i=0; i<laCal.length; i++){
					laTime = laCal[i].split('/');
					liChkF = _planTime2Min(laTime[0]);
					liChkT = _planTime2Min(laTime[1]);

					if (liChkF + liChkT > 0){
						if ((liFrom <= liChkF && liTo > liChkF) ||
							(liFrom < liChkT && liTo >= liChkT) ||
							(liFrom > liChkF && liTo < liChkT)){
							//if ('<?=$debug;?>' == '1'){
							//	alert('ERROR_7');
							//}
							return 21;
						}
					}
				}
			}
		}

		var cnt = $(obj).length;

		for(var i=0; i<cnt; i++){
			try{
				obj = $('div[id^="'+asObj+'_'+aiDay+'_'+i+'"]');

				if (aiCnt != i && $(obj).attr('ynBipay') != 'Y'){
					liChkF = _planTime2Min($(obj).attr('from'));
					liChkT = _planTime2Min($(obj).attr('to'));

					if (asSvcKind == '200' && obj.attr('sugaCd').substring(0,4) == 'CCWC' && lsSugaCd.substring(0,2) == 'CC' && ynBipay != 'Y'){
						//가족일정
						return 7;
					}

					//일정중복확인
					if (liChkF + liChkT > 0){
						if ((liFrom <= liChkF && liTo > liChkF) ||
							(liFrom < liChkT && liTo >= liChkT) ||
							(liFrom > liChkF && liTo < liChkT)){
							if ($(obj).attr('memCd1') == asMemCd || $(obj).attr('memCd2') == asMemCd){
								//if ('<?=$debug;?>' == '1'){
								//	alert('2');
								//}
								return 2;
							}
							if ($(obj).attr('svcKind') == '800'){
								if (asSvcKind == '800'){
									//간호가 중복된경우
									//if ('<?=$debug;?>' == '1'){
									//	alert('ERROR_3');
									//}
									return 2;
								}

								if (asMemCd == $(obj).attr('memCd1')){
									//담당요양보호사가 중복된경우
									//if ('<?=$debug;?>' == '1'){
									//	alert('ERROR_4');
									//}
									return 2;
								}

								if (asMemCd == $(obj).attr('memCd2')){
									//담당요양보호사가 중복된경우
									//if ('<?=$debug;?>' == '1'){
									//	alert('ERROR_5');
									//}
									return 2;
								}
							}else if ($(obj).attr('svcKind') == '200' || $(obj).attr('svcKind') == '500'){
								if (asSvcKind == '200' || asSvcKind == '500') return 3;
							}else{
								if ($(obj).attr('svcKind') == asSvcKind) return 3;
							}
						}
					}

					//재가 일정간 2시간 간격확인
					if (lsSvcCd == '0'){
						if (asSvcKind == '200' && $(obj).attr('svcKind') == '200'){
							liGabF = liFrom - 120;
							liGabT = liTo + 120;

							//if ($('#centerInfo').attr('value') == '1234'){
							//	alert(liFrom+'~'+liTo+'\n'+liGabF+'~'+liGabT+'\n'+liChkF+'~'+liChkT);
							//}

							//if ((liGabF <= liChkF && liGabT >= liChkF) ||
							//	(liGabF < liChkT && liGabT >= liChkT) ||
							//	(liGabF > liChkF && liGabT < liChkT)){
							//	return 4;
							//}

							if ((liGabF <= liChkF && liGabT > liChkF) ||
								(liGabF < liChkT && liGabT >= liChkT) ||
								(liGabF > liChkF && liGabT < liChkT)){
								return 4;
							}
						}
					}

					//목욕주간횟수 확인
					if (asSvcKind == '500' && $(obj).attr('svcKind') == '500'){
						return 5;
					}
				}
			}catch(e){
			}
		}

		if (asSvcKind == '500'){
			var obj = $('div[id^="loCal_"][week="'+aiWeek+'"][sugaCd^="CB"]', $('.clsCalRow', $('#tblCalBody')));

			if ($(obj).length + aiCareCnt > liBathWeekCnt){
				//alert(aiWeek);
				return 6;
			}
		}

		if (lsSvcCd == '0'){
			//가족요양 월횟수 확인
			if (asSvcKind == '200' && $('#txtPayKind').val() == '2'){
				var obj = $('div[id^="loCal_"][sugaCd^="CCWC"]', $('.clsCalRow', $('#tblCalBody')));
				var liLimitCnt = __str2num($('#loClientIfno').attr('familyLimitCnt'));

				if (aiCareCnt == 1){
					if (liLimitCnt <= obj.length){
						return 8;
					}
				}else{
					if (liLimitCnt < obj.length){
						return 8;
					}
				}
			}
		}

		return liResult;
	}

	//일정 데이타 통계
	function lfCalendarData(aiPos){
		try{
			var lsSvcCd = $('#planInfo').attr('svcCd'); //서비스구분
			var lsDataIdx = $('#txtSvcData').attr('value');
			var html = '';
			var obj;

			var lsTmpKey1 = '', lsTmpKey2 = '';
			var lsKey1 = '', lsKey2 = '';
			var liCnt1 = 0, liCnt2 = 0;
			var liTotPay = 0;
			var liSugaTot = 0, liBipayTot = 0, liCost = 0;

			var liLimitPay = __str2num($('#infoClient').attr('limitAmt'));
			var liClaimPay = __str2num($('#infoClient').attr('claimAmt'));
			var liBalance = 0;

			var lsSugaNm = '';
			var liRate = 1;
			var laRate = new Array();
			var ynBipay = 'N';
			var lsProcTime = '', liProcTime = 0;

			if (lsSvcCd == '0'){
				liRate = $('#infoClient').attr('rate');
			}

			//청구한도 설정
			if (liClaimPay == 0)
				liClaimPay = liLimitPay;

			var lsCdGbn = '';

			$('.clsCal[ynAddRow="N"]').each(function(){
				//요양보호사별 서비스내역
				if (lsSvcCd == '0' || lsSvcCd == '3'){
					lsKey1 = $(this).attr('sugaCd')+'_'+$(this).attr('memNm1')+'_'+$(this).attr('memNm2');
				}else{
					lsKey1 = $(this).attr('sugaCd')+'_'+$(this).attr('memNm1')+'_'+$(this).attr('memNm2')+'_'+$(this).attr('procTime');
				}

				if ($('#tblSvcListSub1 tr[id="'+lsKey1+'"]').length == 0){
					liCnt1 = 1;
					liSugaTot = liCnt1 * $(this).attr('costTotal');

					ynBipay = $(this).attr('ynBipay') == 'Y' ? 'Y' : 'N';

					if (ynBipay != 'Y'){
						liCnt2 = 1;
					}else{
						liCnt2 = 0;
					}

					liTotPay += (liCnt2 * $(this).attr('costTotal'));

					if (lsSvcCd == '0'){
						lsProcTime = $(this).attr('procTime')+'분';
					}else{
						liProcTime = __str2num($(this).attr('procTime'));

						if (lsSvcCd == '3'){
							liProcTime = 1;
						}else{
							if (liProcTime >= 60){
								liProcTime = Math.round(liProcTime / 60);
							}
						}

						lsProcTime = liProcTime+'시간/일';
					}

					html = '<tr id="'+lsKey1+'" ynBipay="'+ynBipay+'">'
						 + '<td><div class="left">'+$(this).attr('memNm1')+($(this).attr('memNm2') != '' ? '/'+$(this).attr('memNm2') : '')+'</div></td>'
						 + '<td><div class="left">'+$(this).attr('sugaNm')+'</div></td>'
						 + '<td><div class="center clsSugaVal" value="'+$(this).attr('procTime')+'">'+lsProcTime+'</div></td>'
						 + '<td><div class="right">'+__num2str($(this).attr('cost'))+'</div></td>'
						 + '<td><div class="right clsSugaCnt" value="'+liProcTime+'">'+liCnt1+'</div></td>'
						 + '<td><div class="right clsSugaTot">'+__num2str(liSugaTot)+'</div></td>'
						 + '<td><div class="left">&nbsp;</div></td>'
						 + '</tr>';

					if ($('#tblSvcListSub1 tr').length == 0){
						$('#tblSvcListSub1').html(html);
					}else{
						$('#tblSvcListSub1 tr:last').after(html);
					}
				}else{
					obj = $('#tblSvcListSub1 tr[id="'+lsKey1+'"]');

					liProcTime = __str2num($(this).attr('procTime'));
					if (lsSvcCd == '3'){
						liProcTime = 1;
					}else{
						if (liProcTime >= 60){
							liProcTime = Math.round(liProcTime / 60);
						}
					}

					liCnt1 = __str2num($('td:eq(4)', $(obj)).text()) + 1;
					liCnt2 = __str2num($('div', $('td:eq(4)', $(obj))).attr('value')) + liProcTime;
					liSugaTot = __str2num($('td:eq(5)', $(obj)).text()) + __str2num($(this).attr('costTotal'));

					if ($(this).attr('ynBipay') == 'N'){
						liTotPay += __str2num($(this).attr('costTotal'));
					}

					$('div', $('td:eq(4)', $(obj))).text(__num2str(liCnt1));
					$('div', $('td:eq(4)', $(obj))).attr('value',__num2str(liCnt2));
					$('div', $('td:eq(5)', $(obj))).text(__num2str(liSugaTot));
				}

				if (lsSvcCd >= '0' && lsSvcCd <= '4'){
					//수가별서비스내역
					lsKey2 = $(this).attr('sugaCd');
					liRate = $('#infoClient').attr('rate'+$(this).attr('day'));

					if ($('#tblSvcListSub2 tr[id="'+lsKey2+'"]').length == 0){
						liCnt1 = 1;
						liCost = $(this).attr('cost');
						liSugaTot = liCnt1 * $(this).attr('costTotal');
						liExpense = 0;
						liBipayTot = 0;

						if ($(this).attr('ynBipay') != 'Y'){
							lsSugaNm = $(this).attr('sugaNm');
							if (lsSvcCd == '0'){
								liExpense = cut(liSugaTot * liRate * 0.01,10);
							}
						}else{
							lsSugaNm = $(this).attr('sugaName');
							liBipayTot = liCnt1 * $(this).attr('costTotal');
						}

						html = '<tr id="'+lsKey2+'" svcKind="'+$(this).attr('svcKind')+'">'
							 + '<td><div class="left">'+lsSugaNm+'</div></td>'
							 + '<td><div class="right">'+liCnt1+'</div></td>'
							 + '<td><div class="right">'+__num2str(liCost)+'</div></td>'
							 + '<td><div class="right">'+__num2str(liSugaTot)+'</div></td>'
							 + '<td><div class="right">'+__num2str(liExpense)+'</div></td>'
							 + '<td><div class="right">'+__num2str(liBipayTot)+'</div></td>'
							 + '<td><div class="right">'+__num2str(liExpense+liBipayTot)+'</div></td>'
							 + '<td><div class="left">&nbsp;</div></td>'
							 + '</tr>';

						if ($('#tblSvcListSub2 tr').length == 0){
							$('#tblSvcListSub2').html(html);
						}else{
							$('#tblSvcListSub2 tr:last').after(html);
						}
					}else{
						obj = $('#tblSvcListSub2 tr[id="'+lsKey2+'"]');

						liCnt1 = __str2num($('td:eq(1)', $(obj)).text()) + 1;
						liSugaTot = __str2num($('td:eq(3)', $(obj)).text()) + __str2num($(this).attr('costTotal'));

						if ($(this).attr('ynBipay') != 'Y'){
							if (lsSvcCd == '0'){
								liExpense = __str2num($('td:eq(4)', $(obj)).text()) + cut(__str2num($(this).attr('costTotal')) * liRate * 0.01,10);
							}else{
								liExpense = 0;
							}
						}else{
							liBipayTot = __str2num($('td:eq(5)', $(obj)).text()) + __str2num($(this).attr('costTotal'));
						}

						$('div', $('td:eq(1)', $(obj))).text(__num2str(liCnt1));
						$('div', $('td:eq(3)', $(obj))).text(__num2str(liSugaTot));

						if ($(this).attr('ynBipay') != 'Y'){
							$('div', $('td:eq(4)', $(obj))).text(__num2str(liExpense));
						}else{
							$('div', $('td:eq(5)', $(obj))).text(__num2str(liBipayTot));
						}

						liExpense = __str2num($('td:eq(4)', $(obj)).text());
						liBipayTot = __str2num($('td:eq(5)', $(obj)).text());

						$('div', $('td:eq(6)', $(obj))).text(__num2str(liExpense+liBipayTot));
					}
				}

				$(this).attr('ynAddRow','Y');
			});

			//청구한도 잔액
			if (lsSvcCd >= '0' && lsSvcCd <= '4'){
				lfGetBalance();
			}

			if (lsSvcCd == '0'){
				//재가요양
				loTimerBabyAdd = setTimeout('lfCalendarSvcData()', 10);
			}else if (lsSvcCd == '3' || lsSvcCd == 'A'){
				//산모신생아 추가요금
				loTimerBabyAdd = setTimeout('lfGetBabyAddPay()', 10);
			}

			if (aiPos == 1){
				clearTimeout(loTimerAssign);
				loTimerAssign = null;
			}else{
				return true;
			}
		}catch(e){
			return false;
		}
	}

	//장애인활동지원 일정 데이타 통계
	function lfCalendarDisData(aiPos){
		if ($('#infoClient').attr('ynLoad') != 'Y'){
			return false;
		}

		var liVal = new Array();
			liVal = {
				'1' : {
						'1'	: {'1':0,'2':0}
					,	'2'	: {'1':0,'2':0}
					,	'3'	: {'1':0,'2':0}
					}
			,	'2' : {
						'1'	: {'1':0,'2':0}
					,	'2'	: {'1':0,'2':0}
					,	'3'	: {'1':0,'2':0}
					}
			,	'3' : {
						'1'	: {'1':0,'2':0}
					,	'2'	: {'1':0,'2':0}
					,	'3'	: {'1':0,'2':0}
					}
			};

		var liLimitAmt  = __str2num($('#txtLimitPay').attr('value'));
		var liClaimAmt  = 0; //liLimitAmt - __str2num($('#txtBalance').attr('value'));
		var liConfAmt   = 0; //실적금액
		var lbDuplicate = 0;
		var lsMsg = '';

		$('.clsCal').each(function(){
			var lsRowIdx = '0';
			var lsPayIdx = '0';

			if ($(this).attr('svcKind') == '200'){
				lsRowIdx = '1'; //활동지원
			}else if ($(this).attr('svcKind') == '500'){
				lsRowIdx = '2'; //방문목욕
			}else{
				lsRowIdx = '3'; //방문간호
			}

			if ($(this).attr('ynBipay') != 'Y'){
				lsPayIdx = '3'; //비급여
			}else{
				lsPayIdx = '2'; //일반
			}

			var liSvcCost = __str2num($(this).attr('costTotal'));

			if ($(this).attr('ynBipay') != 'Y'){
				liClaimAmt += liSvcCost;
			}

			lbDuplicate = 1;

			if ($(this).attr('stat') == '1'){
			}else{
				if (liLimitAmt >= liClaimAmt){
					lbDuplicate = lfDuplicate('loCal'
											,$(this).attr('svcKind')
											,$(this).attr('day')
											,$(this).attr('cnt')
											,$(this).attr('from')
											,$(this).attr('to')
											,$(this).attr('memCd1')+'|'+$(this).attr('memCd2')
											,$(this).attr('week'));
				}else{
					//한도초과
					lbDuplicate = 9;
				}
			}

			lsMsg = _planErrorMsg(lbDuplicate);

			$(this).attr('duplicate',(lbDuplicate == 1 ? 'N' : 'Y'));
			$('#divErrorMsg', this).text(lsMsg).parent().show();

			liVal[lsRowIdx][lsPayIdx]['1'] += __round(__str2num($(this).attr('procTime')) / 60, 1, false);
			liVal[lsRowIdx][lsPayIdx]['2'] += liSvcCost;
		});

		var lsId  = '#txtDis';
		var liPay = liVal['1']['3']['2'] + liVal['2']['3']['2'] + liVal['3']['3']['2']; //총사용금액

		var liCost    = __str2num($('#infoClient').attr('svcCost')); //단가
		var liMakePay = __str2num($('#txtDis1_5_2').attr('value')); //한도금액
		var liAddPay  = __str2num($('#txtDis2_5_2').attr('value')); //추가금액
		var liTmpIdx  = 0;
		var liTmpPay  = 0;

		liTmpPay = liMakePay - liPay;

		if (liTmpPay < 0){
			liTmpPay  = liMakePay - (liMakePay % liCost);
			liMakePay = liMakePay % liCost;
			liPay    -= liTmpPay;
			liAddPay -= liPay;

			if (liAddPay < 0) liAddPay = 0;
		}else{
			liMakePay -= liPay;
		}

		for(var i in liVal){
			for(var j in liVal[i]){
				for(var k in liVal[i][j]){
					liVal[i]['1'][k] = liVal[i]['2'][k] + liVal[i]['3'][k];
					$(lsId+i+'_'+j+'_'+k).attr('value',liVal[i][j][k]).text(__num2str(liVal[i][j][k]));
				}
			}
		}

		var liMakeHour  = __round(liMakePay / liCost, 1, false);
		var liAddHour   = __round(liAddPay / liCost, 1, false);
		var liLimitHour = __round(liMakeHour + liAddHour, 1, false);
		var liLimitPay  = liMakePay + liAddPay;

		$('#txtDis1_4_1').attr('value', liMakeHour).text(liMakeHour);            //한도시간
		$('#txtDis1_4_2').attr('value', liMakePay).text(__num2str(liMakePay));   //한도금액
		$('#txtDis2_4_1').attr('value', liAddHour).text(liAddHour);              //추가시간
		$('#txtDis2_4_2').attr('value', liAddPay).text(__num2str(liAddPay));     //추가금액
		$('#txtDis3_4_1').attr('value', liLimitHour).text(liLimitHour);          //합계시간
		$('#txtDis3_4_2').attr('value', liLimitPay).text(__num2str(liLimitPay)); //합계금액

		liPay = liVal['1']['3']['2'] + liVal['2']['3']['2'] + liVal['3']['3']['2'];

		var liLimitPay = __str2num($('#txtLimitPay').attr('value'));
		var liBalance  = liLimitPay - liPay;
		var lsColor    = '#0000ff';

		if (liBalance < 0){
			lsColor = '#ff0000';
		}

		$('#txtBalance').attr('value',liBalance).css('color',lsColor).text(__num2str(liBalance));

		if (aiPos == 1){
			clearTimeout(loTimerAssign);
			loTimerAssign = null;
		}else{
			return true;
		}
	}

	//서비스별 내역
	function lfCalendarSvcData(){
		var liSvcAmt = new Array();
			liSvcAmt = {
				'C' : {'1':0,'2':0,'3':0,'4':0,'5':0}
			,	'B' : {'1':0,'2':0,'3':0,'4':0,'5':0}
			,	'N' : {'1':0,'2':0,'3':0,'4':0,'5':0}
			,	'T' : {'1':0,'2':0,'3':0,'4':0,'5':0}
			};

		var liClaimPay = __str2num($('#infoClient').attr('claimAmt'));

		if ('<?=$lbLimitSet;?>' == '1'){
			var liClaim = {'200':__str2num($('#infoClient').attr('claimCare')),'500':__str2num($('#infoClient').attr('claimBath')),'800':__str2num($('#infoClient').attr('claimNurse'))};
			var laTot   = {'200':0,'500':0,'800':0};
			var liTot   = 0;
		}else{
			var liTot = 0;
		}

		$('div[id^="loCal_"]').each(function(){
			var liPay   = __str2num($(this).attr('costTotal'))
			,	liRate  = __str2num($('#infoClient').attr('rate'+$(this).attr('day')))
			,	liVal   = 0
			,	liOver  = 0
			,	liBipay = 0;

			if ('<?=$lbLimitSet;?>' == '1'){
				if ($(this).attr('ynBipay') != 'Y'){
					if (liClaim['200'] > 0 ||
						liClaim['500'] > 0 ||
						liClaim['800'] > 0){
						if (laTot[$(this).attr('svcKind')] + liPay >= liClaim[$(this).attr('svcKind')]){
							liVal = liClaim[$(this).attr('svcKind')] - laTot[$(this).attr('svcKind')];
							laTot[$(this).attr('svcKind')] += liPay;

							if (liVal > 0){
								liOver = liPay - liVal;
								liPay  = liVal;
							}else{
								liOver = liPay;
								liPay  = 0;
							}
						}else{
							laTot[$(this).attr('svcKind')] += liPay;
						}
					}else{
						if (liTot + liPay >= liClaimPay){
							liVal = liClaimPay - liTot;
							liTot += liPay;

							if (liVal > 0){
								liOver = liPay - liVal;
								liPay  = liVal;
							}else{
								liOver = liPay;
								liPay  = 0;
							}
						}else{
							liTot += liPay;
						}
					}
				}else{
					liBipay = liPay;
					liPay   = 0;
				}
			}else{
				if ($(this).attr('ynBipay') != 'Y'){
					if (liTot + liPay >= liClaimPay){
						liVal = liClaimPay - liTot;
						liTot += liPay;

						if (liVal > 0){
							liOver = liPay - liVal;
							liPay  = liVal;
						}else{
							liOver = liPay;
							liPay  = 0;
						}
					}else{
						liTot += liPay;
					}
				}else{
					liBipay = liPay;
					liPay   = 0;
				}
			}

			switch($(this).attr('svcKind')){
				case '200':
					liSvcAmt['C']['1'] += liPay;
					liSvcAmt['C']['2'] += (liPay * liRate / 100);
					liSvcAmt['C']['3'] += liBipay;
					liSvcAmt['C']['5'] += liOver;
					break;

				case '500':
					liSvcAmt['B']['1'] += liPay;
					liSvcAmt['B']['2'] += (liPay * liRate / 100);
					liSvcAmt['B']['3'] += liBipay;
					liSvcAmt['B']['5'] += liOver;
					break;

				case '800':
					liSvcAmt['N']['1'] += liPay;
					liSvcAmt['N']['2'] += (liPay * liRate / 100);
					liSvcAmt['N']['3'] += liBipay;
					liSvcAmt['N']['5'] += liOver;
					break;
			}
		});

		//방문요양
		liSvcAmt['C']['2'] = Math.floor(liSvcAmt['C']['2']);
		liSvcAmt['C']['4'] = liSvcAmt['C']['2'] + liSvcAmt['C']['3'] + liSvcAmt['C']['5'];

		$('#lblSvcAmtC_1').text(__num2str(liSvcAmt['C']['1']));
		$('#lblSvcAmtC_2').text(__num2str(liSvcAmt['C']['2']));
		$('#lblSvcAmtC_3').text(__num2str(liSvcAmt['C']['3']));
		$('#lblSvcAmtC_4').text(__num2str(liSvcAmt['C']['4']));
		$('#lblSvcAmtC_5').css('color',(liSvcAmt['C']['5'] > 0 ? '#ff0000' : '#000000')).text(__num2str(liSvcAmt['C']['5']));

		//방문목욕
		liSvcAmt['B']['2'] = Math.floor(liSvcAmt['B']['2']);
		liSvcAmt['B']['4'] = liSvcAmt['B']['2'] + liSvcAmt['B']['3'] + liSvcAmt['B']['5'];

		$('#lblSvcAmtB_1').text(__num2str(liSvcAmt['B']['1']));
		$('#lblSvcAmtB_2').text(__num2str(liSvcAmt['B']['2']));
		$('#lblSvcAmtB_3').text(__num2str(liSvcAmt['B']['3']));
		$('#lblSvcAmtB_4').text(__num2str(liSvcAmt['B']['4']));
		$('#lblSvcAmtB_5').css('color',(liSvcAmt['B']['5'] > 0 ? '#ff0000' : '#000000')).text(__num2str(liSvcAmt['B']['5']));

		//방문간호
		liSvcAmt['N']['2'] = Math.floor(liSvcAmt['N']['2']);
		liSvcAmt['N']['4'] = liSvcAmt['N']['2'] + liSvcAmt['N']['3'] + liSvcAmt['N']['5'];

		$('#lblSvcAmtN_1').text(__num2str(liSvcAmt['N']['1']));
		$('#lblSvcAmtN_2').text(__num2str(liSvcAmt['N']['2']));
		$('#lblSvcAmtN_3').text(__num2str(liSvcAmt['N']['3']));
		$('#lblSvcAmtN_4').text(__num2str(liSvcAmt['N']['4']));
		$('#lblSvcAmtN_5').css('color',(liSvcAmt['N']['5'] > 0 ? '#ff0000' : '#000000')).text(__num2str(liSvcAmt['N']['5']));

		liSvcAmt['T']['1'] = liSvcAmt['C']['1']+liSvcAmt['B']['1']+liSvcAmt['N']['1'];
		liSvcAmt['T']['2'] = liSvcAmt['C']['2']+liSvcAmt['B']['2']+liSvcAmt['N']['2'];
		liSvcAmt['T']['3'] = liSvcAmt['C']['3']+liSvcAmt['B']['3']+liSvcAmt['N']['3'];
		liSvcAmt['T']['4'] = liSvcAmt['C']['4']+liSvcAmt['B']['4']+liSvcAmt['N']['4'];
		liSvcAmt['T']['5'] = liSvcAmt['C']['5']+liSvcAmt['B']['5']+liSvcAmt['N']['5'];

		$('#lblSvcAmtT_1').text(__num2str(liSvcAmt['T']['1']));
		$('#lblSvcAmtT_2').text(__num2str(liSvcAmt['T']['2']));
		$('#lblSvcAmtT_3').text(__num2str(liSvcAmt['T']['3']));
		$('#lblSvcAmtT_4').text(__num2str(liSvcAmt['T']['4']));
		$('#lblSvcAmtT_5').css('color',(liSvcAmt['T']['5'] > 0 ? '#ff0000' : '#000000')).text(__num2str(liSvcAmt['T']['5']));
	}

	//한도잔액 lbLimitSet
	function lfGetBalance(){
		try{
			var lsSvcCd = $('#planInfo').attr('svcCd'); //서비스구분

			var liLimitPay = 0;
			var liClaimPay = 0;
			var liBalance  = 0;
			var liProcTime = 0;
			var liTotPay   = 0;

			var liClaimCare  = 0;  //방문요양 청구한도
			var liClaimBath  = 0;  //방문목욕 청구한도
			var liClaimNurse = 0; //방문간호 청구한도

			if (lsSvcCd == '0'){
				liLimitPay = __str2num($('#infoClient').attr('limitAmt'));
				liClaimPay = __str2num($('#infoClient').attr('claimAmt'));

				liClaimCare  = __str2num($('#infoClient').attr('claimCare'));  //방문요양 청구한도
				liClaimBath  = __str2num($('#infoClient').attr('claimBath'));  //방문목욕 청구한도
				liClaimNurse = __str2num($('#infoClient').attr('claimNurse')); //방문간호 청구한도

				//서비스구분
				var lsSvcKindCd = $(this).attr('svcKind');

				$('tr', $('#tblSvcListSub2')).each(function(){
					var liTot = __str2num($('td:eq(3)', $(this)).text());
					var liBi  = __str2num($('td:eq(5)', $(this)).text());

					liTotPay += (liTot - liBi);
				});
			}else{
				liLimitPay = __str2num($('#infoClient').attr('limitAmt'));
				liClaimPay = liLimitPay;

				$('.clsSugaCnt', $('#tblSvcListSub1 tr[ynBipay="N"]')).each(function(){
					liTotPay += __str2num($(this).attr('value'));
				});
			}

			liBalance = liClaimPay - liTotPay;

			var lsColor = '#0000ff';

			if (liBalance < 0){
				lsColor = '#ff0000';
			}

			$('#txtBalance').attr('value',liBalance).css('color',lsColor).text(__num2str(liBalance));
		}catch(e){
		}
	}

	//산모신생아 추가요금
	function lfGetBabyAddPay(){
		clearTimeout(loTimerBabyAdd);
		loTimerBabyAdd = null;

		var liAdd1Cnt = 0, liAdd1Amt = 0, liAdd1Tot = 0, liAdd2Cnt = 0, liAdd2Amt = 0, liAdd2Tot = 0, liAdd3Cnt = 0, liAdd3Amt = 0, liAdd3Tot = 0, liAddHome = 0, liAddHoliday = 0, liAddTot = 0;

		$('.clsCal[ynAddRow="Y"]').each(function(){
			var laBabyAdd = $(this).attr('babyAddPay').split('/');

			liAdd1Cnt += __str2num(laBabyAdd[0]);
			liAdd1Amt += __str2num(laBabyAdd[1]);
			liAdd1Tot += __str2num(laBabyAdd[2]);

			liAdd2Cnt += __str2num(laBabyAdd[3]);
			liAdd2Amt += __str2num(laBabyAdd[4]);
			liAdd2Tot += __str2num(laBabyAdd[5]);

			liAdd3Cnt += __str2num(laBabyAdd[6]);
			liAdd3Amt += __str2num(laBabyAdd[7]);
			liAdd3Tot += __str2num(laBabyAdd[8]);

			liAddHome += __str2num(laBabyAdd[9]);
			liAddHoliday += __str2num(laBabyAdd[10]);
			liAddTot += __str2num(laBabyAdd[11]);
		});

		$('#txtBabyAdd1Cnt').attr('value',liAdd1Cnt).text(__num2str(liAdd1Cnt));
		$('#txtBabyAdd1Amt').attr('value',liAdd1Amt).text(__num2str(liAdd1Amt));
		$('#txtBabyAdd1Tot').attr('value',liAdd1Tot).text(__num2str(liAdd1Tot));

		$('#txtBabyAdd2Cnt').attr('value',liAdd2Cnt).text(__num2str(liAdd2Cnt));
		$('#txtBabyAdd2Amt').attr('value',liAdd2Amt).text(__num2str(liAdd2Amt));
		$('#txtBabyAdd2Tot').attr('value',liAdd2Tot).text(__num2str(liAdd2Tot));

		$('#txtBabyAdd3Cnt').attr('value',liAdd3Cnt).text(__num2str(liAdd3Cnt));
		$('#txtBabyAdd3Amt').attr('value',liAdd3Amt).text(__num2str(liAdd3Amt));
		$('#txtBabyAdd3Tot').attr('value',liAdd3Tot).text(__num2str(liAdd3Tot));

		$('#txtBabyAddHome').attr('value',liAddHome).text(__num2str(liAddHome));
		$('#txtBabyAddHoliday').attr('value',liAddHoliday).text(__num2str(liAddHoliday));
		$('#txtBabyAddTot').attr('value',liAddTot).text(__num2str(liAddTot));
	}

	function lfGetHeight(){
		var lsSvcCd = $('#planInfo').attr('svcCd');
		var h = $('#tblAssignCal').height()-52;

		if (lsSvcCd == '3' || lsSvcCd == 'A')
			h = h - 80;
		else if (lsSvcCd == '4')
			h = h + 26;

		return h;
	}

	function lfSetSvcList(aiIdx){
		$('div[id^="tblSvcList"]').hide();
		$('#tblSvcList'+aiIdx).hide();
		$('#txtSvcData').attr('value',aiIdx);
		$('#tblSvcList'+aiIdx).show();
	}

	//일정삭제
	function lfCalRemove(aiDay,aiCnt){
		var lsSvcCd = $('#planInfo').attr('svcCd');
		var loObj = $('#loCal_'+aiDay+'_'+aiCnt);
		//var lsKey = $(loObj).attr('sugaCd')+'_'+$(loObj).attr('memNm1')+'_'+$(loObj).attr('memNm2');
		var lsKey = '';

		if (lsSvcCd == '0' || lsSvcCd == '3'){
			lsKey = $(loObj).attr('sugaCd')+'_'+$(loObj).attr('memNm1')+'_'+$(loObj).attr('memNm2');
		}else{
			lsKey = $(loObj).attr('sugaCd')+'_'+$(loObj).attr('memNm1')+'_'+$(loObj).attr('memNm2')+'_'+$(loObj).attr('procTime');
		}

		var loTr = $('#tblSvcListSub1 tr[id="'+lsKey+'"]');
		var liCnt = 0;
		var liVal = 0;
		var liSugaTot = __str2num($('td:eq(5)', $(loTr)).text()) - __str2num($(loObj).attr('costTotal'));
		var liRate = $('#infoClient').attr('rate');
		var liProcTime = 0;

		liCnt = __str2num($('td:eq(4)', $(loTr)).text()) - 1;

		if (lsSvcCd == '0'){
		}else{
			//잔여시간
			//liVal = __str2num($('div', $('td:eq(2)', $(loTr))).attr('value'));
			//liVal = __str2num($('div', $('td:eq(4)', $(loTr))).attr('value')) - liVal;

			liProcTime = __str2num($('div', $('td:eq(2)', $(loTr))).attr('value'));
			if (lsSvcCd == '3'){
				liProcTime = 1;
			}else{
				if (liProcTime >= 60){
					liProcTime = Math.round(liProcTime / 60);
				}
			}

			liVal = __str2num($('div', $('td:eq(4)', $(loTr))).attr('value')) - liProcTime;
		}

		if (liCnt > 0){
			$('div', $('td:eq(4)', $(loTr))).text(__num2str(liCnt));
			$('div', $('td:eq(5)', $(loTr))).text(__num2str(liSugaTot));

			if (lsSvcCd == '0'){
			}else{
				$('div', $('td:eq(4)', $(loTr))).attr('value',liVal);
			}
		}else{
			$(loTr).remove();
		}

		lsKey = $(loObj).attr('sugaCd');
		loTr = $('#tblSvcListSub2 tr[id="'+lsKey +'"]');

		liCnt = __str2num($('td:eq(1)', $(loTr)).text()) -1;
		liSugaTot = __str2num($('td:eq(3)', $(loTr)).text()) - __str2num($(loObj).attr('costTotal'));

		if ($(loObj).attr('ynBipay') != 'Y'){
			liExpense = __str2num($('td:eq(4)', $(loTr)).text()) - cut(__str2num($(loObj).attr('costTotal')) * liRate * 0.01,10);
		}else{
			liBipayTot = __str2num($('td:eq(5)', $(loTr)).text()) - __str2num($(loObj).attr('costTotal'));
		}

		if (liCnt > 0){
			$('div', $('td:eq(1)', $(loTr))).text(__num2str(liCnt));
			$('div', $('td:eq(3)', $(loTr))).text(__num2str(liSugaTot));

			if ($(loObj).attr('ynBipay') != 'Y'){
				$('div', $('td:eq(4)', $(loTr))).text(__num2str(liExpense));
			}else{
				$('div', $('td:eq(5)', $(loTr))).text(__num2str(liBipayTot));
			}

			liExpense = __str2num($('td:eq(4)', $(loTr)).text());
			liBipayTot = __str2num($('td:eq(5)', $(loTr)).text());

			$('div', $('td:eq(6)', $(loTr))).text(__num2str(liExpense+liBipayTot));
		}else{
			$(loTr).remove();
		}

		if (lsSvcCd == '0'){
			setTimeout('lfCalendarSvcData()', 10);
		}

		var loParent = $(loObj).parent();

		$(loObj).remove();
		$('.clsCal:first', $(loParent)).css('border-top','none');

		if (lsSvcCd == '4'){
			loTimerAssign = setInterval('lfCalendarDisData(1)', 50);
		}else{
			loTimerAssign = setTimeout('lfChkCalendar()', 10);
			lfGetBalance();
		}

		return false;
	}

	//일정정리
	function lfCalClean(asType){
		var lsSvcCd = $('#planInfo').attr('svcCd');

		if (asType == '1'){
			//중복일정삭제
			$('.clsCal[duplicate="Y"]').remove();
		}else if (asType == '2'){
			//미저장삭제
			$('.clsCal[stat="9"][svcSeq=""]').remove();
		}

		$('.clsCal').attr('ynAddRow','N');
		$('#tblSvcListSub1 tr').remove();
		$('#tblSvcListSub2 tr').remove();

		if (lsSvcCd == '4'){
			loTimerAssign = setInterval('lfCalendarDisData(1)', 50);
		}else{
			loTimerAssign = setTimeout('lfChkCalendar()', 10);
			loTimerAssign = setTimeout('lfCalendarData(1)', 10);
		}
	}

	//일정오류내용보기
	function lfShowCalendar(obj,asDuplicate){
		if ('<?=$debug;?>' == '1'){
			if ($(obj).attr('flag1') == '1'){
				$(obj).attr('flag1','0');
				return false;
			}
		}
		var h = 500;
		var w = 500;
		var t = 100;
		var l = (screen.availWidth - w) / 2;

		if (gPlanWin != null){
		//	gPlanWin.close();
		//	gPlanWin = null;
		}

		if ($(obj).attr('ynBipay') == 'Y'){
			alert('비급여 일정 수정은 준비중입니다.');
			return;
		}

		var lsType = $('#document').attr('type');
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url    = './plan_pop.php';
		var win    = window.open('', 'PLANPOP', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				code    : $('#centerInfo').attr('value')
			,	jumin   : $('#clientInfo').attr('value')
			,	svcCd   : $('#planInfo').attr('svcCd')
			,	svcKind : $(obj).attr('svcKind')
			,	year    : $('#planInfo').attr('year')
			,	month   : $('#planInfo').attr('month')
			,	day     : (parseInt($(obj).attr('day'), 10) < 10 ? '0' : '')+parseInt($(obj).attr('day'), 10)
			,	from    : (lsType == 'PLAN' ? $(obj).attr('from') : $(obj).attr('planFrom')) //$(obj).attr('from')
			,	to      : $(obj).attr('to')
			,	seq     : $(obj).attr('svcSeq')
			,	cNm     : $('#lblCNm').text()
			,	memCd1  : $(obj).attr('memCd1')
			,	memNm1  : $(obj).attr('memNm1')
			,	memCd2  : $(obj).attr('memCd2')
			,	memNm2  : $(obj).attr('memNm2')
			,	sugaNm  : $(obj).attr('sugaNm')
			,	suga    : $(obj).attr('costTotal')

			,	sudangPay  : $(obj).attr('sudangPay')
			,	sudangKind : $(obj).attr('sudangKind')
			,	sudangVal1 : $(obj).attr('sudangVal1')
			,	sudangVal2 : $(obj).attr('sudangVal2')

			,	svcVal  : $('#infoClient').attr('svcVal')
			,	svcLvl  : $('#infoClient').attr('svcLvl')
			,	svcCost : __str2num($('#infoClient').attr('svcCost'))

			,	ynHoliday : $(obj).attr('ynHoliday')
			,	ynBipay   : $(obj).attr('ynBipay')
			,	stat	  : $(obj).attr('stat')
			,	ynSave    : $(obj).attr('ynSave')
			,	id        : $(obj).attr('id')

			,	request : $(obj).attr('request')

			,	ynClose : $('#infoResult').attr('ynClose')
			,	ynSalary: $('#infoResult').attr('ynSalary')

			,	type : lsType

			,	modifyPos : $(obj).attr('modifyPos')
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'PLANPOP');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	//팝업일정체크
	function lfPopDuplicate(asId, asFrom, asTo, asMemCd){
		var obj = $('#'+asId);

		var lsSvcKind = $(obj).attr('svcKind');
		var liDay = $(obj).attr('day');
		var liCnt = $(obj).attr('cnt');
		var liWeek = $(obj).attr('week');

		if ($(obj).attr('ynBipay') != 'Y'){
			var liDuplicate = lfDuplicate('loCal',lsSvcKind,liDay,liCnt,asFrom,asTo,asMemCd,liWeek);
		}else{
			var liDuplicate = 1;
		}

		return liDuplicate;
	}
</script>
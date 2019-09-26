<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo		= $_SESSION['userCenterCode'];
	$code		= $_POST['code'];
	$jumin		= $ed->de($_POST['jumin']);
	$year		= $_POST['year'];
	$month		= $_POST['month'];
	$svcCd		= $_POST['svcCd'];
	$type		= $_POST['type'];
	$lastDay	= $myF->lastDay($year,$month);

	parse_str($_POST['para'],$para);

	//주야간보호
	if ($para['DayAndNight'] == 'Y'){
		$svcCd = '5';
	}

	//가족관계
	$sql = 'SELECT	cf_mem_cd AS jumin
			,		cf_kind AS gbn
			FROM	client_family
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cf_jumin= \''.$jumin.'\'';

	$familyRel = $conn->_fetch_array($sql,'jumin');

	$longtermErrMsg = '공단계획 불일치';

	if ($svcCd == '5'){
		//주야간보호 할증여부
		$sql = 'SELECT	yn
				FROM	dan_extra_charge
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$year.$month.'\'';

		$danExtraChargeYn = $conn->get_data($sql);

		if (!$danExtraChargeYn) $danExtraChargeYn = 'N';
	}
?>
<script type="text/javascript">
	var longtermSec = 0;
	var timer1,timer2,timer3;
	//var loadYn	= {'001':'N','002':'N','003':'N','004':'N'};
	var IsError = false;

	if ('<?=$svcCd;?>' == '0'){
		var loadYn	= {'001':'N','002':'N','003':'N'};
	}else{
		var loadYn	= {'004':'N'};
	}

	$(document).ready(function(){
		timer3	= setTimeout('lfLoadOption()',50);

		if ('<?=$lbTodayPlanReg;?>' == '1'){
			setTimeout('lfLongtermChk()', 1000);
		}

		try{
			lfGetMemPlantime();
		}catch(e){
		}
	});

	function lfLongtermChk(){
		//5분이 지나면 공단정보 조회를 유도한다.
		if (longtermSec > 300){
			$('#divLongtermUploadYn').text('N');
			$('#divReloadBtn').show();
			$('#divUploadBtn').hide();
		}else{
			longtermSec ++;
			setTimeout('lfLongtermChk()', 1000);
		}
	}

	function lfLoadOption(svcKind){
		clearTimeout(timer1);

		if ('<?=$svcCd;?>' == '0'){
			loadYn['001'] = 'N';
			loadYn['002'] = 'N';
			loadYn['003'] = 'N';
		}else{
			loadYn['004'] = 'N';
		}

		$('td[id^="lblPlan_"]').attr('200Yn','N').attr('500Yn','N').attr('800Yn','N').attr('loadYn','N').attr('cnt','0').attr('htm','');

		setTimeout('lfUploadFlag(false)',10);

		timer2	= setInterval('lfTimer()',500);

		setTimeout('lfLongTermMgmt(\''+svcKind+'\')',200);
	}

	function lfUploadFlag(show){
		$('#divUploadBtn').css('display',!show ? 'none' : '');
		//$('#btnUpload1').css('display',!show ? 'none' : '');
		//$('#btnUpload2').css('display',!show ? 'none' : '');
		//$('#btnUpload3').css('display',!show ? 'none' : '');

		if ('<?=$lbTodayPlanReg;?>' == '1'){
			$('#divReloadBtn').hide();

			if (show){
				$('#divChkDataChange').text('N');
				$('#divLongtermUploadYn').text('Y');
				$('#divReloadBtn').hide();
				$('#divUploadBtn').show();

				longtermSec = 0;
				setTimeout('lfLongtermChk()', 1000);
			}
		}
	}

	function lfTimer(){
		var msg	= $('#lblMsg').text();

		try{
			if (msg.substring(0,7) != 'Loading' || msg.substring(0,7) != 'Loading......'){
				msg	= 'Loading';
			}
		}catch(e){
			msg	= 'Loading';
		}

		msg += '.';

		var lbFlag = false;

		if ('<?=$svcCd;?>' == '0'){
			if (loadYn['001'] == 'Y' && loadYn['002'] == 'Y' && loadYn['003'] == 'Y') lbFlag = true;
		}else{
			if (loadYn['004'] == 'Y') lbFlag = true;
		}

		if (lbFlag){
			clearInterval(timer2);
			clearTimeout(timer3);
			$('#lblMsg').text('');

			setTimeout('lfChkPlanLongterm()',1000);
			setTimeout('lfUploadFlag(true)',10);
		}else{
			$('#lblMsg').text(msg);
		}
	}

	/*********************************************************

		수급자 인정번호

	*********************************************************/
	function lfLongTermMgmt(svcKind){
		try{
			$.ajax({
				type : 'POST'
			,	url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=TR' //TR->PR
			,	data : {
					'longTermAdminSym'	: $('#centerInfo').attr('giho')
				,	'longTermAdminNm'	: $('#centerInfo').attr('name')
				,	'adminKindCd'		: 'C'
				,	'searchPayYyyy'		: '20%'
				,	'searchGbn'			: 'searchMgmtNo'
				,	'searchValue'		: $('#clientAppNo').text()
				,	'searchDt'			: 'searchCtrDt'
				,	'fnc'				: 'select'
				}
			,	beforeSend: function(){
				}
			,	success: function (data){
					var selCheck = 'value2';
					var addFlag = false;

					var selectCheck = $('input[type="checkbox"][name="selectCheck"]',data).val();
					var scIdx = (selectCheck ? selectCheck : '').indexOf('|'+$('#clientAppNo').text());

					if (scIdx >= 0) addFlag = true;

					//if ($('input[type="checkbox"][name="selectCheck"]['+selCheck+'="'+$('#clientAppNo').text()+'"]', data).val()){
					if (addFlag){
						var addInfo = selectCheck.split('|');

						//longTermMgmtNo  = $('input[type="checkbox"][name="selectCheck"]', data).attr('value2');
						//longTermMgmtSeq = $('input[type="checkbox"][name="selectCheck"]', data).attr('value4');
						//jumin			= getHttpRequest('../inc/_ed_code.php?type=2&value='+$('#clientInfo').attr('value'));
						//payCtrNo		= $('input[type="checkbox"][name="selectCheck"]', data).attr('value');

						longTermMgmtNo	= addInfo[1].split(' ').join('');
						longTermMgmtSeq	= addInfo[3].split(' ').join('');
						jumin			= addInfo[2].split(' ').join('');
						payCtrNo		= addInfo[0].split(' ').join('');

						lfPayCtrNo(jumin, longTermMgmtNo, longTermMgmtSeq, svcKind,payCtrNo);
					}else{
						clearInterval(timer2);

						//if ($('input[type=\'checkbox\'][name=\'selectCheck\']', data).length == 0){
						//	clearInterval(timer2);
							//timer3	= setTimeout('lfLoadOption()',500);
						//}

						$('#lblMsg').text('건보 로그인 후 새로고침을 클릭하여 주십시오.');
						return false;
					}
				}
			,	error: function (request, status, error){
					clearTimeout(timer3);

					if (g_ErrorCnt == 0){
						g_ErrorCnt ++;
						lfLoadOption();
					}else{
						//alert("CODE : " + request.status+"/"+status+"/"+error+"\nMESSAGE : " + request.responseText);
					}
				}
			});
		}catch(e){
		}
	}

	/*********************************************************

		수급자 계약번호 가져오기

	*********************************************************/
	function lfPayCtrNo(jumin, longTermMgmtNo, longTermMgmtSeq, svcKind,payCtr){
		try{
			var YYMM		= $('#planInfo').attr('year')+''+$('#planInfo').attr('month');
			var payCtrNo	= '';
			var tmpCtrNo	= '';
			var data = {};

			if ('<?=$svcCd;?>' == '0'){
				var arrCtrNo = {'001':'','002':'','003':''};
			}else{
				var arrCtrNo = {'004':''};
			}

			var tmpCrtNo = '';

			//첫일과 말일
			var firstDate	= YYMM+'01';
			var lastDate	= YYMM+getLastDay(__getDate(firstDate));

			//에러 플래그
			IsError = false;

			//기존
			//data['tgtJuminNo'] = jumin;
			//data['longTermMgmtNo'] = longTermMgmtNo;
			//data['fnc'] = 'select';

			data['payCtrNo'] = payCtr;
			data['tgtJuminNo'] = jumin;
			data['longTermMgmtNo'] = longTermMgmtNo;
			data['longTermMgmtSeq'] = longTermMgmtSeq;
			data['fnc'] = 'select';

			$.ajax({
				type : 'POST',
				url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=PR',  //TR->PR
				data: data,
				success: function (data){
					$('#npayTable td:nth-child(1)', data).each(function(){
						var tmpYYMM = $('td',$(this).parent()).eq(1).text().split('.').join('');
						var tmpTerm = $(this).text().replace(/[^0-9]/g, '');
						var tmpYm_s = tmpTerm.substr(0,6);
						var tmpYm_e = tmpTerm.substr(8,6);

						//계약 종료일자
						var tmpYmd = tmpTerm.substr(8);

						//계약기가
						if (tmpYmd > firstDate && tmpYmd < lastDate){
							IsError = true;
						}

						//if ('<?=$code;?>' == '31135000074'){
						//	$('#strDummy').html(data).show();
						//}

						//if (!tmpYYMM) tmpYYMM = YYMM;
						if (tmpYm_s<=YYMM && tmpYm_e>=YYMM /*&& tmpYYMM == YYMM*/){
							/*
								payCtrNo = $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');

								if (payCtrNo){
									if (payCtrNo != tmpCtrNo){
										tmpCtrNo	= payCtrNo;
										timer1		= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+payCtrNo+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","'+svcKind+'")',1);
									}
								}
							 */
							var selectCheck = $('input[type="checkbox"][name="selectCheck"]',$(this).parent()).val();
							var addInfo = selectCheck.split('|');

							/*
							svcKind		= $('input[type=\'checkbox\'][name=\'selectCheck\']', $(this).parent()).attr('value4');
							payCtrNo	= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');
							tgtDemoChasu= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value5');
							*/

							svcKind			= addInfo[3].split(' ').join('');
							payCtrNo		= addInfo[2].split(' ').join('');
							tgtDemoChasu	= addInfo[7].split(' ').join('');
							longTermMgmtSeq	= addInfo[5].split(' ').join('');
							longTermLevel	= addInfo[6].split(' ').join('');

							var IsAddRow = false;

							if ('<?=$svcCd;?>' == '0'){
								if (svcKind == '001' || svcKind == '002' || svcKind == '003'){
									IsAddRow = true;
								}
							}else{
								if (svcKind == '004'){
									IsAddRow = true;
								}
							}

							//if (!arrCtrNo[svcKind]){
								if (IsAddRow){
									/*
										if (payCtrNo != arrCtrNo[svcKind]){
											arrCtrNo[svcKind]	= payCtrNo;

											timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo[svcKind]+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","'+svcKind+'","'+tgtDemoChasu+'")',10);
											//return false;
										}
									 */
									if (tmpCrtNo.indexOf('/'+payCtrNo) == -1){
										tmpCrtNo += ('/'+payCtrNo);
										arrCtrNo[svcKind] = payCtrNo;

										timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo[svcKind]+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","'+svcKind+'","'+tgtDemoChasu+'","'+longTermLevel+'")',10);
									}
								}
							//}
						}
					});

					if ('<?=$svcCd;?>' == '0'){
						if (arrCtrNo['001']){
							//timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo['001']+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","001")',10);
						}else{
							loadYn['001']	= 'Y';
						}

						if (arrCtrNo['002']){
							//timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo['002']+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","002")',10);
						}else{
							loadYn['002']	= 'Y';
						}

						if (arrCtrNo['003']){
							//timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo['003']+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","003")',10);
						}else{
							loadYn['003']	= 'Y';
						}
					}else{
						if (arrCtrNo['004']){
							//timer1	= setTimeout('lfLoadData("'+YYMM+'","'+jumin+'","'+arrCtrNo['001']+'","'+longTermMgmtNo+'","'+longTermMgmtSeq+'","001")',10);
						}else{
							loadYn['004']	= 'Y';
						}
					}
				},
				error: function (request, status, error){
					alert('[ERROR No.02]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			});
		}catch(e){
		}
	}

	function lfLoadData(YYMM, jumin, payCtrNo, longTermMgmtNo, longTermMgmtSeq, svcKind,tgtDemoChasu,longTermLevel){
		/*
			var admtGradeCd = $('#infoClient').attr('svcLvl');
			if (admtGradeCd == '4'){
				admtGradeCd = 'D';
			}else if (admtGradeCd == '5'){
				admtGradeCd = 'E';
			}
		*/
		var admtGradeCd = longTermLevel;

		try{
			$.ajax({
				type: 'POST'
			,	url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU'
			,	data: {
					'longTermAdminSym'	: $('#centerInfo').attr('giho')
				,	'payCtrNo'			: payCtrNo
				,	'payMm'				: YYMM
				,	'longTermMgmtNo'	: longTermMgmtNo
				,	'longTermMgmtSeq'	: longTermMgmtSeq
				,	'tgtJuminNo'		: jumin
				,	'serviceKind'		: svcKind
				,	'adminDemoChasu'	: '01'
				,	'tgtDemoChasu'		: tgtDemoChasu
				,	'admtGradeCd'		: admtGradeCd
				,	'fnc'				: 'select'
				}
			,	beforeSend: function(){
				}
			,	success: function(result){
					lfChkLvlGbn(YYMM, svcKind, result);
					lfChkPlan(result, svcKind);
				}
			,	error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}

	function lfChkLvlGbn(YYMM, svcKind, result){
		var data = result.toString().split('<b>').join('').split('</b>').join('');
		var div = $('div[id="TableData10"]:last',data);
		var monthPay = $('#mmAmt',div).text();
		var allPay = $('#mmAmtAll',div).text();
		var limitPay = $('#mmLimitAmt',div).text();
		var overPay = $('#mmExcsAmt',div).text();

		monthPay = __str2num(monthPay) + __str2num($('#lblLCMonthPay').text());

		$('#lblLCMonthPay').text(__num2str(monthPay));
		$('#lblLCAllPay').text(allPay);
		$('#lblLCLimitPay').text(limitPay);
		$('#lblLCOverPay').text(overPay);

		var data	= result.toString();
		var pos		= data.toString().indexOf('<div id="TableData10"');

		data	= data.substring(pos);
		pos		= data.indexOf('<div id="TableData10"',1);
		data	= data.substring(0,pos);

		var day	= 1;

		$('input[name="srcAmdtGradeCd"]',data).each(function(){
			//등급
			if ($(this).val()){
				var lvl	= $(this).val().split(' ').join('');
				var dt	= YYMM+(day < 10 ? '0' : '')+day;
				var clr	= '#ff0000';
				var upload = false;

				if (lvl == 'D') lvl = '4';
				if (lvl == 'E') lvl = '5';

				$('.clsLvlList').each(function(){
					if ($(this).attr('from') <= dt && $(this).attr('to') >= dt){
						if ($(this).attr('lvl') == lvl){
							clr	= '#000000';
							upload = true;
							return false;
						}
					}
				});

				var tmp	= $('#lblLvl_'+day).text();

				if (tmp != lvl){
					if (tmp){
						tmp	+= '/';
					}

					tmp += lvl;

					$('#lblLvl_'+day).css('color',clr).text(tmp);
				}

				$('#lblSvc'+svcKind+'_'+day).text('Y');

				if ($('#lblUpload_'+day).text() == '' || $('#lblUpload_'+day).text() == 'Y'){
					$('#lblUpload_'+day).css('color',(upload ? '#000000' : '#FF0000')).text(!IsError && upload ? 'Y' : 'N');
				}
			}
			day ++;
		});

		day	= 1;

		$('input[name="srcTgtPrsnCd"]',data).each(function(){
			//구분
			/*
				1 : 일반
				2 : 의료
				3 : 기쵸
				4 : 경감
			 */
			if ($(this).val()){
				var gbn	= $(this).val();
				var dt	= YYMM+(day < 10 ? '0' : '')+day;
				var clr	= '#ff0000';
				var upload = false;

				$('.clsGbnList').each(function(){
					var tmp = $(this).attr('gbn');

					switch(tmp){
						case '1':	tmp	= '3';	break;
						case '2':	tmp	= '4';	break;
						case '3':	tmp	= '1';	break;
						case '4':	tmp	= '2';	break;
						default :	tmp = '3';
					}

					if ($(this).attr('from') <= dt && $(this).attr('to') >= dt){
						if (tmp == gbn){
							clr	= '#000000';
							upload = true;
							return false;
						}
					}
				});

				switch(gbn){
					case '1':	gbn	= '기초';	break;
					case '2':	gbn	= '경감';	break;
					case '3':	gbn	= '일반';	break;
					case '4':	gbn	= '의료';	break;
				}

				var tmp	= $('#lblGbn_'+day).text();

				if (tmp != gbn){
					if (tmp){
						tmp	+= '/';
					}

					tmp += gbn;

					$('#lblGbn_'+day).css('color',clr).text(tmp);

					if ($('#lblUpload_'+day).text() == '' || $('#lblUpload_'+day).text() == 'Y'){
						$('#lblUpload_'+day).css('color',(upload ? '#000000' : '#FF0000')).text(!IsError && upload ? 'Y' : 'N');
					}
				}

				//$('#lblGbn_'+day).css('color',clr).text(gbn);
			}
			day ++;
		});

		day	= 1;

		$('input[name="src986Cd"]',data).each(function(){
			//치매 90분 가능여부
			if ($(this).val()){
				var gbn	= $(this).val();
				var clr	= '#ff0000';
				var fnt	= 'normal';

				if (gbn != 'Y'){
					clr	= '#000000';
				}else{
					fnt	= 'bold';
				}

				var tmp	= $('#lbl986_'+day).text();

				if (tmp != $(this).val()){
					if (tmp){
						tmp	+= '/';
					}

					tmp += $(this).val();

					$('#lbl986_'+day).css('color',clr).css('font-weight',fnt).text(tmp);
				}

				//$('#lbl986_'+day).css('color',clr).css('font-weight',fnt).text($(this).val());
			}
			day ++;
		});

		loadYn[svcKind]	= 'Y';
	}

	function lfChkPlan(result, svcKind){
		var data	= result.toString().split('<b>').join('').split('</b>').join('');
		var div		= $('div[id="TableData3"]',data);
		var tbl		= $('table:first',div);
		var tr		= $('tr',tbl);
		var subCd	= '';
		var i		= 0;
		var fromTm	= '';

		/*
		if ('<?=$code;?>' == '32911000088'){
			var tmp = $('#strDummy').html();

			tmp += ('<br><br>'+$(div).html());

			$('#strDummy').html(tmp).show();
		}
		*/

		if (svcKind == '001'){
			subCd = '200';
		}else if (svcKind == '002'){
			subCd = '500';
		}else if (svcKind == '003'){
			subCd = '800';
		}

		$(tr).each(function(){
			//var tmpChk = $('#careJuminNo'+i,this).text();
			if ('<?=$svcCd;?>' == '0'){
				var tmpObj = $('th[id^="careJuminNo"]:first',this);
			}else{
				var tmpObj = $('th[id^="sugaCd"]:first',this);
			}

			var tmpChk = $(tmpObj).text();

			$('td[id^="lblPlan_"]',$('#tblLongterm')).attr('loadYn','N');

			if (tmpChk){
				if ('<?=$orgNo;?>' == '31154500113'){
				}
				if ('<?=$svcCd;?>' == '0'){
					i = $(tmpObj).attr('id').split('careJuminNo').join('');
				}else{
					i = $(tmpObj).attr('id').split('sugaCd').join('');
				}

				//B2300000 차량 60분 - CBKD1
				//B2380000 차랑 40분~60분미만 - CBKD1
				//B2400000 가정내 60분 - CBKD2
				//B2480000 가정내 40분~60분미만 - CBKD2
				//B2500000 미차랑 60분 - CBFD1
				//B2580000 미차랑 40분~60분미만 - CBFD1
				var qlfNo		= $('#qlfNo'+i,this).text(); //요양요원자격증번호
				var sugaCd		= $('#sugaCd'+i,this).text(); //수가코드
				var serviceTmFr = $('#serviceTmFr'+i,this).text(); //시작시간
				var serviceTmTo = $('#serviceTmTo'+i,this).text(); //종료시간
				var careJuminNo1= getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo'+i,this).text()); //직원
				var careNm1		= $('#careNm'+i,this).text(); //직원명
				var familyYn1	= ($('#familyYn1'+i,this).attr('checked') ? 'Y' : 'N'); //가족관계
				var familyRel1	= $('#_familyRel1'+i,this).val(); //가족관계 코드
				var careJuminNo2= getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#careJuminNo2'+i,this).text()); //직원
				var careNm2		= $('#careNm2'+i,this).text(); //직원명
				var familyYn2	= ($('#familyYn2'+i,this).attr('checked') ? 'Y' : 'N'); //가족관계
				var familyRel2	= $('#_familyRel2'+i,this).val(); //가족관계 코드
				var payDt		= $('input:checkbox[name="payDt'+i+'"]',this);
				var planCnt		= 0;
				var day = 1;

				fromTm = serviceTmFr;

				if ('<?=$svcCd;?>' != '0'){
					//주야간보호 임시
					careJuminNo2= careJuminNo1;
					careNm2		= careNm1;
					careJuminNo1= '';
					careNm1		= '';
				}

				//if ($('#centerInfo').attr('giho') == '24273000050'){
				//	alert(sugaCd);
				//}

				if (sugaCd == 'B2300000' || sugaCd == 'B2380000'){
					sugaCd = 'CBKD1';
				}else if (sugaCd == 'B2400000' || sugaCd == 'B2480000'){
					sugaCd = 'CBKD2';
				}else if (sugaCd == 'B2500000' || sugaCd == 'B2580000'){
					sugaCd = 'CBFD1';
				}

				$(payDt).each(function(){
					var obj = $('#lblPlan_'+day); //결과를 담을 곳
					var cnt = __str2num($(obj).attr('cnt')); //일자별 일정수
					var htm = $(obj).attr('htm');
					var str = '';

					if (!htm) htm = '';
					if ($(this).attr('checked')){
						str = ('?memNm1='+(careNm1 ? careNm1 : '')
							+	'&memCd1='+(careJuminNo1 ? careJuminNo1 : '')
							+	'&familyYn1='+(familyYn1 ? familyYn1 : '')
							+	'&familyRel1='+(familyRel1 ? familyRel1 : '')
							+	'&memNm2='+(careNm2 ? careNm2 : '')
							+	'&memCd2='+(careJuminNo2 ? careJuminNo2 : '')
							+	'&familyYn2='+(familyYn2 ? familyYn2 : '')
							+	'&familyRel2='+(familyRel2 ? familyRel2 : '')
							+	'&from='+(serviceTmFr ? serviceTmFr : '')
							+	'&to='+(serviceTmTo ? serviceTmTo : '')
							+	'&subCd='+(subCd ? subCd : '')
							+	'&sugaCd='+(sugaCd ? sugaCd : '')
							);

						if (htm.indexOf(str) < 0){
							cnt ++;
							htm += str;
						}
					}

					var link = cnt;
					var dayLoadYn = $(obj).attr('loadYn');

					if (cnt > 0){
						dayLoadYn = 'Y';
					}

					$(obj).attr('htm',htm).attr('cnt',cnt).attr('loadYn',dayLoadYn).html(link);

					day ++;
				});

				//i ++;
			}else{
				if ($('th:first',this).text().split(' ').join('') == '급여내용자료관리상태'){
					var td = $(this).html();

					td = td.split('TH').join('TD').split('</TD>');

					for(var t in td){
						if (t > 0){
							var val = td[t].split('<TD>').join('').split(' ').join('');

							if (val.indexOf('저장') >= 0){
								$('#lblPlan_'+t).attr('fix','Y').css('color','BLUE');

								if ('<?=$orgNo;?>' == '34273000017'){
									$('.clsCal').each(function(){
										if (__str2num($(this).attr('day')) == __str2num(t)){
											if (fromTm == $(this).attr('from').split(':').join('')){
												$('#lblLGSaveYn',this).attr('lgSaveYn','Y').show();
												return false;
											}
										}
									});
								}
							}
						}
					}
				}
			}
		});
	}

	function lfChkPlanLongterm(){
		//년월
		var YYMM = $('#planInfo').attr('year')+'-'+$('#planInfo').attr('month');
		var lastday = getLastDay(YYMM+'-01');
		var longtermRegShow = false;
		var today = getToday().split('-').join('');

		//일정비교
		for(var i=1; i<=lastday; i++){
			var dt = $('#planInfo').attr('year')+$('#planInfo').attr('month')+(i < 10 ? '0' : '')+i;
			var plan = $('div[id^="loCal_'+i+'_"]'); //기관계획
			var longterm = $('#lblPlan_'+i).attr('htm'); //공단계획
			var fix = $('#lblPlan_'+i).attr('fix'); //공단확정여부

			if ($(plan).length == 0 && !longterm) continue;

			var row = longterm.split('?');
			var err = true;
			var errPlan = false;

			for(var j=0; j<row.length; j++){
				if (row[j]){
					//if (!row[j]) continue;

					var col = __parseVal(row[j]);

					$(plan).each(function(){
						var planNm1 = $(this).attr('memNm1'); //주요양보호사
						var planNm2 = $(this).attr('memNm2'); //부요양보호사
						var planFrom = $(this).attr('from').replace(':',''); //시작시간
						var planTo = $(this).attr('to').replace(':',''); //종료시간
						var planFamily = $(this).attr('ynFamily'); //가족여부
						var planSub = $(this).attr('svcKind');
						var planErr = true;
						/*
						var planStr11 =  planNm1 + planNm2;
						var planStr12 =  planNm2 + planNm1;
						var planStr21 =  col['memNm1'] + col['memNm2'];
						var planStr22 =  col['memNm2'] + col['memNm1'];
						 */
						var planStr11 =  $(this).attr('memCd1') + '_' + $(this).attr('memCd2');
						var planStr12 =  $(this).attr('memCd2') + '_' +  $(this).attr('memCd1');
						var planStr21 =  col['memCd1'] + '_' +  col['memCd2'];
						var planStr22 =  col['memCd2'] + '_' +  col['memCd1'];

						/*
						if ('<?=$orgNo;?>' == '31154500113'){
							alert(planStr11+'\n'+planStr21
								+'\n\n'+planStr12+'\n'+planStr22
								+'\n\n'+planFrom+'\n'+col['from']
								+'\n\n'+planTo+'\n'+col['to']
								+'\n\n'+planSub+'\n'+col['subCd']);
						}
						*/

						if ((planStr11 == planStr21 || planStr11 == planStr22) &&
							planFrom == col['from'] &&
							planTo == col['to'] &&
							planSub == col['subCd']){

							if (planSub == '500'){
								if ($(this).attr('sugaCd') == col['sugaCd']){
									err = false;
								}
							}else{
								err = false;
							}
							return false;
						}
					});

					if (today > dt){
						$('#lblPlan_'+i).attr(col['subCd']+'Yn',err ? 'Y' : 'N');
					}

					if (!err) break;
				}
			}

			if ($(plan).length != row.length - 1){
				err = true;
			}

			/*
			if (err){
				var cnt = __str2num($('#lblPlan_'+i).attr('cnt'));

				if (cnt > 0){
					var link = '<span style="color:#FF0000; font-weight:'+(today > dt ? 'bold' : 'normal')+';">'+$('#lblPlan_'+i).attr('cnt')+'</span>';

					$('#lblPlan_'+i).html(link).unbind('click').bind('click',function(){
						lfPlanShow(this);
					});
				}

				longtermRegShow = true;
			}
			*/
			var cnt = __str2num($('#lblPlan_'+i).attr('cnt'));

			if (cnt > 0){
				var link = '<span style="color:#'+(err ? 'FF0000' : '')+'; font-weight:'+(fix == 'Y' || (err && today > dt) ? 'bold' : 'normal')+';">'+$('#lblPlan_'+i).attr('cnt')+'</span>';

				$('#lblPlan_'+i).html(link).unbind('click').bind('click',function(){
					lfPlanShow(this);
				});
			}

			if (err) longtermRegShow = true;


			//계획기준의 오류 검사
			$(plan).each(function(){
				var planNm1 = $(this).attr('memNm1'); //주요양보호사
				var planNm2 = $(this).attr('memNm2'); //부요양보호사
				var planFrom = $(this).attr('from').replace(':',''); //시작시간
				var planTo = $(this).attr('to').replace(':',''); //종료시간
				var planFamily = $(this).attr('ynFamily'); //가족여부
				var planSub = $(this).attr('svcKind');
				var err = true;

				for(var j=0; j<row.length; j++){
					//if (!row[j] || !$(this).attr('day')) continue;

					var col = __parseVal(row[j]);
					/*
					var planStr11 =  planNm1 + planNm2;
					var planStr12 =  planNm2 + planNm1;
					var planStr21 =  col['memNm1'] + col['memNm2'];
					var planStr22 =  col['memNm2'] + col['memNm1'];
					*/
					var planStr11 = $(this).attr('memCd1') + '_' + $(this).attr('memCd2');
					var planStr12 = $(this).attr('memCd2') + '_' + $(this).attr('memCd1');
					var planStr21 = col['memCd1'] + '_' + col['memCd2'];
					var planStr22 = col['memCd2'] + '_' + col['memCd1'];

					/*
					if ('<?=$orgNo;?>' == '31154500113'){
						alert(this+'\n'+$(this).attr('day')+'\n'+row[j]
							+'\n'+planStr11+'/'+planStr21
							+'\n'+planStr11+'/'+planStr22
							+'\n'+planFrom+'/'+col['from']
							+'\n'+planTo+'/'+col['to']
							+'\n'+planSub+'/'+col['subCd']);
					}
					*/

					if ((planStr11 == planStr21 || planStr11 == planStr22) &&
						planFrom == col['from'] &&
						planTo == col['to'] &&
						planSub == col['subCd']){

						if (planSub == '500'){
							if ($(this).attr('sugaCd') == col['sugaCd']){
								err = false;
							}
						}else{
							err = false;
						}

						break;
					}
				}

				if (err){
					if ($(this).attr('ynBipay') != 'Y'){
						var msg = $('#divLongtermMsg',this);
						var btn = $('#btnPlanClose',this);

						$(btn).show();
						$(msg).text('<?=$longtermErrMsg;?>');

						if (today > dt){
							$(msg).css('font-weight','bold');
							$(this).attr('longtermYn','N');
						}else{
							$(msg).css('font-weight','normal');
						}

						$(msg).show();

						var cnt = __str2num($('#lblPlan_'+i).attr('cnt'));

						if (cnt > 0){
							var link = '<span style="color:#'+(err ? 'FF0000' : '')+'; font-weight:'+(err && today > dt ? 'bold' : 'normal')+';">'+cnt+'</span>';
							$('#lblPlan_'+i).html(link)
						}
					}
				}
			});
		}
	}

	function lfPlanShow(obj){
		var day = $(obj).attr('id').replace('lblPlan_','');
		var html = $(obj).attr('htm');
		var row = html.split('?');
		var no = 1;

		var plan = $('div[id^="loCal_'+day+'_"]'); //기관계획

		//html += '<div class="title title_border">공단일정</div>';
		html =	'<table class="my_table my_border_blue" style="width:auto;">'
			 +	'<colgroup>'
			 +	'<col width="40px">'
			 +	'<col width="50px">'
			 +	'<col width="100px">'
			 +	'<col width="50px">'
			 +	'<col width="50px">'
			 +	'<col width="50px">'
			 +	'<col width="150px">'
			 +	'</colgroup>'
			 +	'<thead>'
			 +	'<tr>'
			 +	'<th class="head">No</th>'
			 +	'<th class="head">서비스</th>'
			 +	'<th class="head">요양보호사</th>'
			 +	'<th class="head">시작</th>'
			 +	'<th class="head">종료</th>'
			 +	'<th class="head">불일치</th>'
			 +	'<th class="head last">'
			 +	'<div style="float:right; width:auto; margin-right:5px;"><img src="../image/btn_close.gif" onclick="$(\'#divPlanChkLayer\').hide();" style="cursor:pointer;"></div>'
			 +	'<div style="float:cener; width:auto;">비고</div>'
			 +	'</th>'
			 +	'</tr>'
			 +	'</thead>'
			 +	'<tbody>';

		for(var i=0; i<row.length; i++){
			if (row[i]){
				var col = __parseVal(row[i]);
				var subnm = '';
				//var para = '&planDate='+$('#planInfo').attr('year')+$('#planInfo').attr('month')+(day < 10 ? '0' : '')+day;
				var para = '&day='+day;
				var err = true;

				$(plan).each(function(){
					var planNm1 = $(this).attr('memCd1'); //주요양보호사
					var planNm2 = $(this).attr('memCd2'); //부요양보호사
					var planFrom = $(this).attr('from').replace(':',''); //시작시간
					var planTo = $(this).attr('to').replace(':',''); //종료시간
					var planFamily = $(this).attr('ynFamily'); //가족여부
					var planSub = $(this).attr('svcKind');
					var planStr11 =  planNm1 + '_' + planNm2;
					var planStr12 =  planNm2 + '_' + planNm1;
					var planStr21 =  col['memCd1'] + '_' + col['memCd2'];
					var planStr22 =  col['memCd2'] + '_' + col['memCd1'];

					if (planSub == col['subCd']){
						para += '&planRowId='+$(this).attr('id');
						para += '&planTime='+planFrom;
						para += '&planSeq='+$(this).attr('svcSeq');
					}

					if ((planStr11 == planStr21 || planStr11 == planStr22) &&
						planFrom == col['from'] &&
						planTo == col['to'] &&
						planSub == col['subCd']){

						if (planSub == '500'){
							if ($(this).attr('sugaCd') == col['sugaCd']){
								para = '';
								err = false;
							}
						}else{
							para = '';
							err = false;
						}
					}
				});

				if ('<?=$svcCd;?>' == '0'){
					switch(col['subCd']){
						case '200': subnm = '요양'; break;
						case '500': subnm = '목욕'; break;
						case '800': subnm = '간호'; break;
					}
				}else if ('<?=$svcCd;?>' == '5'){
					subnm = '주야간';
				}

				html += '<tr>'
					 +	'<td class="center">'+no+'</td>'
					 +	'<td class="center">'+subnm+'</td>'
					 +	'<td class="left">'+col['memNm1']+(col['memNm2'] ? '/'+col['memNm2'] : '')+'</td>'
					 +	'<td class="center">'+__styleTime(col['from'])+'</td>'
					 +	'<td class="center">'+__styleTime(col['to'])+'</td>'
					 +	'<td class="center">'+(err ? '<span style="color:red; font-weight:bold;">Y</span>' : '')+'</td>';

				html +=	'<td class="left last">';
				if (err){
					html +=	'<span class="btn_pack m"><button id="btnLongtermPlan" onclick="lfLongtermPlanReg(\''+row[i]+para+'\');">계획등록</button></span>';
				}
				html +=	'</td>'
				html +=	'</tr>';

				no ++;
			}
		}

		html += '</tbody>'
			 +	'</table>';

		var top = $(obj).offset().top + $(obj).height()- 2;
		var left = $(obj).offset().left - 3;

		if (left + 510 > $(document).width()){
			left = $(document).width() - 510;
		}

		$('#divPlanChkLayer').css('top',top).css('left',left).html(html).show();
	}

	function lfLongtermPlanReg(para, nextProc){
		//if (!confirm('공단의 계획을 케어비지트계획으로 등록하시겠습니까?')) return;

		if (!nextProc) nextProc = true;

		var col = __parseVal(para);

		var lsStyle  = 'clear:both; text-align:left; padding-left:3px;';
			lsStyle += 'border-top:1px dotted #666666;';

		var day		= col['day'];
		var obj		= $('#loCal_'+day);
		var rowId	= (col['planRowId'] ? col['planRowId'] : '');
		var date	= $('#planInfo').attr('year')+$('#planInfo').attr('month')+(day < 10 ? '0' : '')+day;
		var week	= getWeekDay(__getDate(date));
		var cnt		= $('.clsCal', $(obj)).length;
		var ynFamily	= (col['familyYn1'] == 'Y' ? 'Y' : 'N');
		var bathKind	= '';
		var sudangKind	= '';

		if (col['subCd'] == '200'){
		}else if (col['subCd'] == '500'){
			sudangKind = 'PERSON';

			//B2300000 차량 60분 - CBKD1
			//B2380000 차랑 40분~60분미만 - CBKD1
			//B2400000 가정내 60분 - CBKD2
			//B2480000 가정내 40분~60분미만 - CBKD2
			//B2500000 미차랑 60분 - CBFD1
			//B2580000 미차랑 40분~60분미만 - CBFD1

			/*
			if (col['sugaCd'].substring(0,3) == 'B23'){
				bathKind = '1';
			}else if (col['sugaCd'].substring(0,3) == 'B24'){
				bathKind = '2';
			}else if (col['sugaCd'].substring(0,3) == 'B25'){
				bathKind = '3';
			}
			*/
			if (col['sugaCd'] == 'CBKD1'){
				bathKind = '1';
			}else if (col['sugaCd'] == 'CBKD2'){
				bathKind = '2';
			}else if (col['sugaCd'] == 'CBFD1'){
				bathKind = '3';
			}
		}else if (col['subCd'] == '800'){
			sudangKind = 'PERSON';
		}

		var lsBipayInfo = '';
		var liBiapyAmt = 0;

		if ('<?=$svcCd;?>' == '5'){
			//주야간보호
			$('input:checkbox[id^="chkNpmt_"]:checked').each(function(){
				if (lsBipayInfo) lsBipayInfo += '@';

				lsBipayInfo += ($(this).attr('id').split('chkNpmt_').join('')+'#'+$(this).val());
				liBiapyAmt += __str2num($(this).val());
			});
		}

		var lvl = '';

		$('tr',$('tbody',$('#tblLvlInfo'))).each(function(){
			if (date >= $(this).attr('from') && date <= $(this).attr('to')){
				lvl = $(this).attr('lvl');
			}
		});

		if (!lvl) lvl = $('#infoClient').attr('svcLvl');

		//수가
		$.ajax({
			type:'POST'
		,	async:false
		,	url:'../find/find_suga.php'
		,	data:{
				'code'		:$('#centerInfo').attr('value')
			,	'svcCd'		:'<?=$svcCd;?>'
			,	'svcKind'	:col['subCd']
			,	'date'		:date
			,	'fromTime'	:col['from']
			,	'toTime'	:col['to']
			,	'ynFamily'	:ynFamily
			,	'bathKind'	:bathKind
			,	'svcLvl'	:lvl
			,	'memCnt'	:1
			}
		,	success: function(result){
				var suga = __parseVal(result);

				if ('<?=$svcCd;?>' == '5'){
					//주야간보호
					if (suga['ynHoliday'] == 'Y'){
						suga['name'] += '(<span style=\'color:RED\'>30%</span>)';
					}
				}

				var html = '<div id="loCal_'+day+'_'+cnt+'" class="clsCal" style="'+lsStyle+'" onclick="lfShowCalendar(this,\'1\');" onmouseover="_planMouseOver(this);" onmouseout="_planMouseOut(this);"'
						 + ' day		="'+day+'"'
						 + ' cnt		="'+cnt+'"'
						 + ' week		="'+week+'"'
						 + ' svcKind	="'+col['subCd']+'"'
						 + ' from		="'+col['from']+'"'
						 + ' to			="'+col['to']+'"'
						 + ' memCd1		="'+col['memCd1']+'"'
						 + ' memNm1		="'+col['memNm1']+'"'
						 + ' memCd2		="'+col['memCd2']+'"'
						 + ' memNm2		="'+col['memNm2']+'"'
						 + ' duplicate	="N"'
						 + ' sugaName	="'+suga['name']+'"'
						 + ' sugaCd		="'+suga['code']+'"'
						 + ' sugaNm		="'+suga['name']+'"'
						 + ' procTime	="'+suga['procTime']+'"'
						 + ' cost		="'+suga['cost']+'"'
						 + ' costEvening="'+suga['costEvening']+'"'
						 + ' costNight	="'+suga['costNight']+'"'
						 + ' costTotal	="'+suga['costTotal']+'"'
						 + ' sudangPay	="'+suga['sudangPay']+'"'
						 + ' sudangKind	="sudangKind"'
						 + ' sudangVal1	=""'
						 + ' sudangVal2	=""'
						 + ' timeEvening="'+suga['timeEvening']+'"'
						 + ' timeNight	="'+suga['timeNight']+'"'
						 + ' ynNight	="'+suga['ynNight']+'"'
						 + ' ynEvening	="'+suga['ynEvening']+'"'
						 + ' ynHoliday	="'+suga['ynHoliday']+'"'
						 + ' ynBipay	="N"'
						 + ' ynFamily	="'+ynFamily+'"'
						 + ' extraKind	=""'
						 + ' bipayCost	=""'
						 + ' bipayInfo	="'+lsBipayInfo+'"'
						 + ' ynRealPay	=""'
						 + ' realPay	=""'
						 + ' babyAddPay	=""'
						 + ' ynAddRow	="N"'
						 + ' ynSave		="N"'
						 + ' stat		="9"'
						 + ' seq		="'+cnt+'"'
						 + ' svcSeq		=""'
						 + ' request	="PLAN"'
						 + ' modifyPos	="N"'
						 + ' togetherYn	="N"'
						 + '>'
						 + '<div class="divCalCont" style="font-weight:bold; cursor:default;">'
						 + '	<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><img src="../image/btn_close.gif" onclick="return lfCalRemove(\''+day+'\',\''+cnt+'\');" style="margin-top:3px;"></div>'
						 + '	<div id="lblTimeStr" style="float:left; width:auto; cursor:default; color:;">'+__styleTime(col['from'])+'~'+__styleTime(col['to'])+'</div>'
						 + '</div>';

				if ('<?=$svcCd;?>' == '5'){
					html += '<div class="divCalCont" style="cursor:default;">비급여:<span id="lblNonpayment">'+__num2str(liBiapyAmt)+'</span></div>';
				}else{
					html += '<div id="lblMemStr" class="divCalCont" style="cursor:default;">'+col['memNm1']+(col['memNm2'] ? '/'+col['memNm2'] : '')+'</div>';
				}

				html	+= '<div id="lblSugaStr" class="divCalCont" style="cursor:default;">'
						+  '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+suga['name']+'</div>';

				if (suga['code'].substring(0,2) == 'CB'){
					html += '<div style="float:left; width:auto;"><img src="../image/icon_bath.png" style="width:15px; height:14px;"></div>';
				}else if (suga['code'].substring(0,2) == 'CN'){
					html += '<div style="float:left; width:auto;"><img src="../image/icon_nurs.png" style="width:15px; height:14px;"></div>';
				}

				html += '</div>';

				html += '<div class="divCalCont" style="display:none;"><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold; cursor:default;"></span></div>';
				html += '<div id="divLongtermMsg" style="color:#ff0000; font-size:11px; font-weight:bold; display:none;"></div>';

				if (__str2num(suga['costTotal']) == 0){
					html += '<div style="color:#ff0000; font-size:11px; font-weight:bold;">수가금액오류</div>';
				}

				html += '</div>';

				if (cnt > 0){
					$('.clsCal:last', $(obj)).after(html);
				}else{
					$(obj).html(html);
				}

				$('.clsCal:first', $(obj)).css('border-top','none');

				if (nextProc){
					loTimerAssign = setTimeout('lfCalendarData(1)', 10);
				}

				$('#divPlanChkLayer').hide();
			}
		});
	}

	function lfLongtermPlanAll(){
		//불일치 일정삭제
		//$('div[id^="loCal_"][longtermYn="N"]').remove();
		$('div[id^="loCal_"]').each(function(){
			if ($('#divLongtermMsg',this).text() == '<?=$longtermErrMsg;?>'){
				var day = $(this).attr('day');
				var cnt = $(this).attr('cnt');

				if (day && cnt){
					$('#loCal_'+day+'_'+cnt).remove();
				}
			}
		});

		//공단일정 등록
		$('td[id^="lblPlan_"]').each(function(){
			var day = $(this).attr('id').replace('lblPlan_','');
			var html = $(this).attr('htm');
			var row = html.split('?');

			var plan = $('div[id^="loCal_'+day+'_"]'); //기관계획

			for(var i=0; i<row.length; i++){
				if (row[i]){
					var col = __parseVal(row[i]);
					var para = '&day='+day;
					var err = true;

					$(plan).each(function(){
						var planNm1 = $(this).attr('memCd1'); //주요양보호사
						var planNm2 = $(this).attr('memCd2'); //부요양보호사
						var planFrom = $(this).attr('from').replace(':',''); //시작시간
						var planTo = $(this).attr('to').replace(':',''); //종료시간
						var planFamily = $(this).attr('ynFamily'); //가족여부
						var planSub = $(this).attr('svcKind');
						var planStr11 =  planNm1 + '_' + planNm2;
						var planStr12 =  planNm2 + '_' + planNm1;
						var planStr21 =  col['memCd1'] + '_' + col['memCd2'];
						var planStr22 =  col['memCd2'] + '_' + col['memCd1'];

						if (planSub == col['subCd']){
							para += '&planRowId='+$(this).attr('id');
							para += '&planTime='+planFrom;
							para += '&planSeq='+$(this).attr('svcSeq');
						}

						if ((planStr11 == planStr21 || planStr11 == planStr22) &&
							planFrom == col['from'] &&
							planTo == col['to'] &&
							planSub == col['subCd']){
							para = '';
							err = false;
						}
					});

					if (err){
						lfLongtermPlanReg(row[i]+para,false);
					}
				}
			}
		});

		loTimerAssign = setTimeout('lfCalendarData(1)', 10);
	}

	function lfSetDanExtraCharge(obj){
		$.ajax({
			type:'POST'
		,	url:'../iljung/plan_extra_charge.php'
		,	data:{
				'jumin':'<?=$ed->en($jumin);?>'
			,	'yymm':$('#planInfo').attr('year')+''+$('#planInfo').attr('month')
			,	'yn':($(obj).attr('checked') ? 'Y' : 'N')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					if ($(obj).attr('checked')){
						var claimAmt = Math.floor(__str2num($('#infoClient').attr('limitAmt')) * 1.5);

						claimAmt = Math.round(claimAmt / 10) * 10;

						$('#infoClient').attr('claimAmt',claimAmt);
					}else{
						$('#infoClient').attr('claimAmt',$('#infoClient').attr('limitAmt'));
					}

					$('#txtClaimPay').text(__num2str($('#infoClient').attr('claimAmt')));

					_planSetLimitAmt();
					setTimeout('lfCalendarData(1)',100);
				}else{
					$(obj).attr('checked',!$(obj).attr('checked'));
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfGetMemPlantime(){
		$.ajax({
			type:'POST'
		,	url:'../iljung/plan_mem_plantime.php'
		,	data:{
				'jumin':'<?=$ed->en($jumin);?>'
			,	'yymm':$('#planInfo').attr('year')+''+$('#planInfo').attr('month')
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				if (html){
					$('#ID_MEM_PLANTIME').html(html).show();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<table id="tblLongterm" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="27px" span="<?=$lastDay;?>">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">일자</th><?
			for($i=1; $i<=$lastDay; $i++){
				$week = Date('w',StrToTime($year.$month.($i < 10 ? '0' : '').$i));

				if ($week == 0){
					$clr = 'FF0000';
				}else if ($week == 6){
					$clr = '0000FF';
				}else{
					$clr = '000000';
				}?>
				<th class="center" style="color:#<?=$clr;?>;"><?=$i;?></th><?
			}?>
			<th class="center last">비고</th>
		</tr>
		<tr style="display:none;">
			<th class="center">요양</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblSvc001_<?=$i;?>" style="background-color:#e8ffdd;"></td><?
			}?>
			<td class="center top bottom last"></td>
		</tr>
		<tr style="display:none;">
			<th class="center">목욕</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblSvc002_<?=$i;?>" style="background-color:#dfddff;"></td><?
			}?>
			<td class="center top bottom last"></td>
		</tr>
		<tr style="display:none;">
			<th class="center">간호</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblSvc003_<?=$i;?>" style="background-color:#ffdfdd;"></td><?
			}?>
		</tr>
		<tr style="display:none;">
			<th class="center">업로드</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblUpload_<?=$i;?>"></td><?
			}?>
		</tr>
		<tr>
			<th class="center">등급</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblLvl_<?=$i;?>"></td><?
			}?>
			<td class="center last">
				<a href="#" onclick="lfLoadOption(); return false;">새로고침</a>
			</td>
		</tr>
		<tr>
			<th class="center">구분</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblGbn_<?=$i;?>"></td><?
			}?>
			<td class="center top last" rowspan="2"><div id="lblMsg" class="left" style="line-height:1.3em;"></div></td>
		</tr>
		<tr>
			<th class="center">치매</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lbl986_<?=$i;?>"></td><?
			}?>
		</tr>
		<tr style="background-color:#FFD8D8;">
			<th class="center">공단</th><?
			for($i=1; $i<=$lastDay; $i++){?>
				<td class="center" id="lblPlan_<?=$i;?>" cnt="0" htm="" loadYn="" 200Yn="N" 500Yn="N" 800Yn="N" fix="N" style="cursor:default;"></td><?
			}?>
			<td class="left top last" style="padding-top:1px; background-color:#FFFFFF;">
				<span class="btn_pack m"><button onclick="lfLongtermPlanAll();">일괄처리</button></span>
				<span class="btn_pack m"><button onclick="window.open('../popup/upload/carevisit_upload.pdf','UPLOAD_MANUAL','width=800,height=600,left=0,top=0,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');">메뉴얼</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="50px">
		<col width="100px">
		<col width="110px">
		<col width="100px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center bold">공단금액 정보</th>
			<th class="center">월금액</th>
			<td class="left bold" id="lblLCMonthPay">0</td>
			<th class="center">타기관포함 월금액</th>
			<td class="left bold" id="lblLCAllPay">0</td>
			<th class="center">월한도액</th>
			<td class="left bold" id="lblLCLimitPay">0</td>
			<th class="center">초과금액</th>
			<td class="left bold last" style="color:red;" id="lblLCOverPay">0</td>
		</tr>
	</tbody>
</table><?
if ($svcCd == '0'){?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="100px">
			<col width="60px">
			<col width="100px">
			<col width="60px">
			<col width="100px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bold bottom">예상처우개선비</th>
				<th class="center bottom">방문요양</th>
				<td class="left bold bottom" id="lblDealCare">0</td>
				<th class="center bottom">방문목욕</th>
				<td class="left bold bottom" id="lblDealBath">0</td>
				<th class="center bottom">합계</th>
				<td class="left bold bottom last" id="lblDealPay">0</td>
			</tr>
		</tbody>
	</table><?

	if ($gDomain == 'dolvoin.net'){?>
		<div id="ID_MEM_PLANTIME" style="display:none;"></div><?
	}
}else if ($svcCd == '5'){?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="100px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bold">예상처우개선비</th>
				<td class="left bold last" id="lblDealPay">0</td>
			</tr>
			<tr>
				<th class="center bold bottom">150% 할증여부</th>
				<td class="bottom last">
					<label><input id="chkDanExtraChargeYn" type="checkbox" class="checkbox" value="Y" onclick="lfSetDanExtraCharge(this);" <?=$danExtraChargeYn == 'Y' ? 'checked' : '';?>>하루 8시간이상 20일 초과시 및 기타 150% 할증이 가능한 경우 선택하여 주십시오.</label>
				</td>
			</tr>
		</tbody>
	</table><?
}?>
<div id="divPlanChkLayer" style="position:absolute; z-index:10; top:0; left:0; width:0; height:0; background-color:#FFFFFF; display:none;"></div>
<div id="divChkDataChange" style="display:none;" alt="데이타변경여부">N</div>
<div id="divLongtermUploadYn" style="display:none;" alt="업로드 가능시간경과여부">N</div>
<?
	include_once('../inc/_db_close.php');
?>
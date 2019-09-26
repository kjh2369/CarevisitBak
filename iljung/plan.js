var gPlanWin  = null;
var gProcFlag = true;


/*********************************************************
 * 일정등록 팝업
 *********************************************************/
function _planReg(asId,asYear,asMonth,asJumin,asSvcCd,asCode,asSr,para){
	var h = 750; //screen.availHeight;
	var w = 1065;
	var t = 0;
	var l = (screen.availWidth - w) / 2;

	if (!asSr) asSr = '';
	if (!para) para = '';

	if (gPlanWin != null){
		gPlanWin.close();
		gPlanWin = null;
	}

	var target = 'PLANREG';
	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=yes';
	var url    = '../iljung/plan_reg.php';
		gPlanWin = window.open('', target, option);
		gPlanWin.opener = self;
		gPlanWin.focus();

	if (!asCode){
		asCode	= $('#code').attr('value');
	}

	var parm = new Array();
		parm = {
			'code'	:asCode
		,	'jumin'	:asJumin
		,	'year'	:asYear
		,	'month' :asMonth
		,	'svcCd' :asSvcCd
		,	'id'	:asId
		,	'sr'	:asSr
		,	'para'	:para
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

	form.setAttribute('target', target);
	form.setAttribute('method', 'post');
	form.setAttribute('action', url);

	document.body.appendChild(form);

	form.submit();
}

function _planRegResult(asId,aiCnt,asJumin,asYYMM){
	var obj   = $('#'+asId);
	var liCnt = parseInt(aiCnt,10);
	var jumin = asJumin;
	var yymm  = asYYMM;

	if (liCnt > 0){
		$(obj).removeClass('my_month_1').addClass('my_month_y');
	}else{
		$(obj).removeClass('my_month_y').addClass('my_month_1');
	}

	$.ajax({
		type :'POST'
	,	async:false
	,	url  :'../iljung/plan_lc_log.php'
	,	data :{
			'jumin':jumin
		,	'yymm' :yymm
		}
	,	beforeSend: function(){
		}
	,	success: function(data){
			if (data){
				var val = data.split(String.fromCharCode(1));
				
				$('#divCare_'+asId).css('background',(val[0] == 'Y' ? 'url(../image/bg_cal_g.gif) no-repeat' : ''));
				$('#divBath_'+asId).css('background',(val[1] == 'Y' ? 'url(../image/bg_cal_b.gif) no-repeat' : ''));
				$('#divNurs_'+asId).css('background',(val[2] == 'Y' ? 'url(../image/bg_cal_r.gif) no-repeat' : ''));
			}
		}
	});
}


/*********************************************************
 * 로딩바
 *********************************************************/
function _planLoading(obj){
	try{
		var l = ($(obj).width() - 200) / 2;
		var t = $(obj).offset().top + ($(obj).height() - 20) / 2;

		return '<div id=\''+$(obj).attr('id')+'Bar\' style=\'position:absolute; width:auto; top:'+t+'px; left:'+l+'px; text-align:center;\'>'+__get_loading()+'</div></center></div>';
	}catch(e){
	}
}


/*********************************************************
 * 고객정보로드
 *********************************************************/
function _planCltLoad(){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_client_info.php'
	,	data : {
			'code'	:$('#centerInfo').attr('value')
		,	'jumin'	:$('#clientInfo').attr('value')
		,	'year'	:$('#planInfo').attr('year')
		,	'month'	:$('#planInfo').attr('month')
		,	'svcCd'	:$('#planInfo').attr('svcCd')
		,	'type'	:$('#document').attr('type')
		,	'para'	:$('#planInfo').attr('para')
		}
	,	beforeSend: function(){
		}
	,	success: function(data){
			$('#clientInfo').after(data).hide();
		}
	});
}


/*********************************************************
 * 수가정보
 *********************************************************/
function _planSvcLoad(){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_svc_info.php'
	,	data : {
			'code'	:$('#centerInfo').attr('value')
		,	'jumin'	:$('#clientInfo').attr('value')
		,	'year'	:$('#planInfo').attr('year')
		,	'month'	:$('#planInfo').attr('month')
		,	'svcCd'	:$('#planInfo').attr('svcCd')
		}
	,	beforeSend: function(){
			//$('#svcInfo').after(_planLoading($('#svcInfo')));
		}
	,	success: function(data){
			//$('#svcInfoBar').remove();

			if (!data){
				gProcFlag = false;
				alert('계약된 서비스내역이 없습니다.\n확인 후 다시 시도하여 주십시오.');
				return;
			}
			
			$('#svcInfo').after(data).hide();
			//$('#svcInfo').html(data);

			_planSugaLoad();
		}
	});
}

function _planSugaLoad(){
	if (!gProcFlag) return;

	$.ajax({
		type	:'POST'
	,	url		:'../iljung/plan_suga_info.php'
	,	data	:{
			'code'	:$('#centerInfo').attr('value') 
		,	'jumin'	:$('#clientInfo').attr('value')
		,	'year'	:$('#planInfo').attr('year')
		,	'month'	:$('#planInfo').attr('month')
		,	'svcCd'	:$('input:hidden[name="svcCd"]').val()
		,	'para'	:$('#planInfo').attr('para')
		}
	,	beforeSend:function(){
			//$('#sugaInfo').after(_planLoading($('#sugaInfo')));
		}
	,	success:function(data){
			//$('#sugaInfoBar').remove();
			$('#tblSuga').remove();
			$('#sugaInfo').after(data).hide();
		}
	});
}


/*********************************************************
 * 배정일자 및 요일
 *********************************************************/
function _planAssignLoad(){
	if (!gProcFlag) return;

	$.ajax({
		type : 'POST'
	,	async: false
	,	url  : '../iljung/plan_assign_info.php'
	,	data : {
			'code'	:$('#centerInfo').attr('value')
		,	'jumin'	:$('#clientInfo').attr('value')
		,	'svcCd'	:$('#planInfo').attr('svcCd')
		,	'year'	:$('#planInfo').attr('year')
		,	'month'	:$('#planInfo').attr('month')
		,	'type'	:$('#document').attr('type')
		,	'para'	:$('#planInfo').attr('para')
		}
	,	beforeSend: function(){
			//$('#assignInfo').after(_planLoading($('#assignInfo')));
		}
	,	success: function(data){
			//$('#assignInfoBar').remove();
			//$('#assignInfo').html(data);
			$('#assignInfo').after(data).hide();
		}
	});
}

function _planAssingAll(asSel){
	var clr  = '';
	var lsChk = asSel;
	var ynChk = 'N';
	
	if (lsChk == 'A' || lsChk == 'X'){
		for(var i=0; i<7; i++){
			if (lsChk == 'A')
				ynChk = 'Y';
			else
				ynChk = 'N';
			
			$('#weekday_'+i, $('#tblAssignCal')).attr('value', (ynChk == 'Y' ? 'Y' : 'N'));
			$('.clsWeek_'+i+'[pastYn="N"]', $('#tblAssignCal')).attr('value', (ynChk == 'Y' ? 'Y' : 'N')).css('font-weight', (ynChk == 'Y' ? 'bold' : 'normal')).css('background-color', (ynChk == 'Y' ? '#fffabb' : '#ffffff'));
		}
	}else{
		$('div[id^="txtDay_"]').attr('value', 'N').css('font-weight','normal').css('background-color', '#ffffff');
		if (lsChk == 'W'){
			$('div[id^="txtDay_"][weekly!="0"]').attr('value', 'Y').css('font-weight','bold').css('background-color', '#fffabb');
		}else{
			$('div[id^="txtDay_"][weekly="0"]').attr('value', 'Y').css('font-weight','bold').css('background-color', '#fffabb');
		}
	}
}

function _planAssignWeekSel(obj){
	var isYn   = $(obj).attr('value');
	var weekly = $(obj).attr('weekly');

	if (isYn == 'Y')
		isYn = 'N';
	else 
		isYn = 'Y';

	var bold  = 'normal';
	var bgClr = '#ffffff';
	
	if (isYn == 'Y'){
		bold  = 'bold';
		bgClr = '#fffabb';
	}

	$(obj).attr('value', isYn);
	$('.clsWeek_'+weekly+'[pastYn="N"]', $('#tblAssignCal')).attr('value', isYn).css('font-weight',bold).css('background-color',bgClr);
}

function _planAssignDaySel(obj){
	var isYn   = $(obj).attr('value');
	var weekly = $(obj).attr('weekly');
	var pastYn = $(obj).attr('pastYn');

	if ($('#planInfo').attr('svcCd') == '0'){
		if (pastYn == 'Y'){
			if ($('#txtPayKind').val() != '3'){
				return;
			}
		}
	}
	
	if (isYn == 'Y')
		isYn = 'N';
	else 
		isYn = 'Y';

	var bold  = 'normal';

	if (pastYn == 'Y'){
		var bgClr = '#EFEFEF';
	}else{
		var bgClr = '#FFFFFF';
	}

	if (isYn == 'Y'){
		bold  = 'bold';
		bgClr = '#FFFABB';
	}

	$(obj).attr('value', isYn).css('font-weight',bold).css('background-color',bgClr);
}


/*********************************************************
 * 버튼그룹
 *********************************************************/
function _planCalBtnLoad(){
	if (!gProcFlag) return;

	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_cal_btn.php'
	,	data : {
			'code'	: $('#centerInfo').attr('value') 
		,	'year'	: $('#planInfo').attr('year')
		,	'month'	: $('#planInfo').attr('month')
		,	'svcCd'	: $('#planInfo').attr('svcCd')
		,	'type'	: $('#document').attr('type')
		,	'jumin'	: $('#clientInfo').attr('value')
		,	'para'	: $('#planInfo').attr('para')
		}
	,	beforeSend: function(){
			//$('#assignInfo').after(_planLoading($('#assignInfo')));
		}
	,	success: function(data){
			$('#calBtn').after(data).hide();
		}
	});
}


/*********************************************************
 * 실비지급
 *********************************************************/
function _planExtraLoad(obj){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_suga_extra.php'
	,	data : {
			'code'  : $('#centerInfo').attr('value') 
		,	'jumin'	: $('#clientInfo').attr('value')
		,	'year'  : $('#planInfo').attr('year')
		,	'month' : $('#planInfo').attr('month')
		,	'svcCd' : $('#planInfo').attr('svcCd')
		}
	,	beforeSend: function(){
			//$('#clientInfo').after(_planLoading($('#clientInfo')));
		}
	,	success: function(html){
			//$('#clientInfoBar').remove();
			$('#extraCont').html(html);
		}
	});
}


/*********************************************************
 * 산모 신생아 추가 금액
 *********************************************************/
function _planBabyAddLoad(){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_suga_babyadd.php'
	,	data : {
			'code'  : $('#centerInfo').attr('value') 
		,	'jumin'	: $('#clientInfo').attr('value')
		,	'year'  : $('#planInfo').attr('year')
		,	'month' : $('#planInfo').attr('month')
		,	'svcCd' : $('#planInfo').attr('svcCd')
		}
	,	beforeSend: function(){
			//$('#clientInfo').after(_planLoading($('#clientInfo')));
		}
	,	success: function(html){
			//$('#clientInfoBar').remove();
			$('#babyAddCont').html(html);
		}
	});
}


/*********************************************************
 * 수당 금액
 *********************************************************/
function _planExtraPayLoad(){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_extra_pay.php'
	,	data : {
		}
	,	beforeSend: function(){
			//$('#clientInfo').after(_planLoading($('#clientInfo')));
		}
	,	success: function(html){
			//$('#clientInfoBar').remove();
			$('#extraPayCont').html(html);
		}
	});
}


/*********************************************************
 * 일정표달력
 *********************************************************/
function _planCalContLoad(){
	if (!gProcFlag) return;

	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_cal_cont.php'
	,	data : {
			'code'	:$('#centerInfo').attr('value') 
		,	'jumin'	:$('#clientInfo').attr('value')
		,	'svcCd'	:$('#planInfo').attr('svcCd')
		,	'year'	:$('#planInfo').attr('year')
		,	'month'	:$('#planInfo').attr('month')
		,	'type'	:$('#document').attr('type')
		,	'sr'	:$('#planInfo').attr('sr')
		,	'para'	:$('#planInfo').attr('para')
		}
	,	beforeSend: function(){
			//$('#assignInfo').after(_planLoading($('#assignInfo')));
		}
	,	success: function(data){
			$('#tblCalBody').remove();
			$('#tblSvcListSub1 tr').remove();
			$('#tblSvcListSub2 tr').remove();
			$('#calCont').after(data).remove();
			
			try{
				//setTimeout('lfLoadOption()',100);
			}catch(e){
			}
		}
	,	complete:function(){
		}
	});
}


function _planCalResize(objH){
	try{
		
		if (__str2num(objH) > 0)
			var h = objH;
		else
			var h = $(this).height();

		h = h - $('#tblCal').offset().top + 1;

		$('#calBody').height(h);
	}catch(e){
	}
}

function _planCalTblResize(){
	try{
		for(var i=0; i<6; i++)
			$('.clsCalCol'+i, $('#tblCal')).width($('.clsCalCol'+i, $('#tblCalBody')).width());
	}catch(e){
	}
}

//시간을 분으로 변경
function _planTime2Min(asTime){
	if (!asTime)
		return 0;

	var lsTime = asTime.split(':').join('');
	var liH = __str2num(lsTime.substring(0,2));
	var liM = __str2num(lsTime.substring(2,4));

	liMin = liH * 60 + liM;

	return liMin;
}

//한도금액 설정
function _planSetLimitAmt(){
	var lsSvcCd = $('#planInfo').attr('svcCd');

	try{
		if (lsSvcCd == '0'){
			var liLimitPay = __str2num($('#infoClient').attr('limitAmt'));
			var liClaimPay = __str2num($('#infoClient').attr('claimAmt'));

			if (liLimitPay > 0){
				$('#txtLimitPay').attr('value',liLimitPay).text(__num2str(liLimitPay)); //급여한도금액
				$('#txtClaimPay').attr('value',liLimitPay).text(__num2str(liLimitPay)); //청구한도금액
				$('#txtBalance').attr('value',liLimitPay).text(__num2str(liLimitPay)); //청구한도잔액
			}
			
			if (liClaimPay > 0){
				$('#txtClaimPay').attr('value',liClaimPay).text(__num2str(liClaimPay)); //청구한도금액
				$('#txtBalance').attr('value',liClaimPay).text(__num2str(liClaimPay)); //청구한도잔액
			}
		}else if (lsSvcCd == '4'){
			var liLimitPay = __str2num($('#infoClient').attr('limitAmt'));
			var liClaimPay = liLimitPay;
			var liBalance  = Math.abs(__str2num($('#txtBalance').attr('value')));

			$('#txtLimitPay').attr('value',liLimitPay).text(__num2str(liLimitPay)); //한도

			if (liBalance > 0){
				liClaimPay = liLimitPay - liBalance;
			}else{
				liClaimPay = liLimitPay;
			}

			var liCost    = __str2num($('#infoClient').attr('svcCost')); //단가
			var liMakePay = __str2num($('#infoClient').attr('makePay')); //생성시간
			var liAddPay  = __str2num($('#infoClient').attr('addPay')); //추가시간

			var liMakeHour  = __round(liMakePay / liCost, 1, false);
			var liAddHour   = __round(liAddPay / liCost, 1, false);
			var liLimitHour = __round(liLimitPay / liCost, 1, false);

			//한도
			$('#txtDis1_5_1').attr('value', liMakeHour).text(liMakeHour);            //한도시간
			$('#txtDis1_5_2').attr('value', liMakePay).text(__num2str(liMakePay));   //한도금액
			$('#txtDis2_5_1').attr('value', liAddHour).text(liAddHour);              //추가시간
			$('#txtDis2_5_2').attr('value', liAddPay).text(__num2str(liAddPay));     //추가금액
			$('#txtDis3_5_1').attr('value', liLimitHour).text(liLimitHour);          //합계시간 
			$('#txtDis3_5_2').attr('value', liLimitPay).text(__num2str(liLimitPay)); //합계금액
			
			//잔여
			$('#txtDis1_4_1').attr('value', liMakeHour).text(liMakeHour);            //한도시간
			$('#txtDis1_4_2').attr('value', liMakePay).text(__num2str(liMakePay));   //한도금액
			$('#txtDis2_4_1').attr('value', liAddHour).text(liAddHour);              //추가시간
			$('#txtDis2_4_2').attr('value', liAddPay).text(__num2str(liAddPay));     //추가금액
			$('#txtDis3_4_1').attr('value', liLimitHour).text(liLimitHour);          //합계시간 
			$('#txtDis3_4_2').attr('value', liLimitPay).text(__num2str(liLimitPay)); //합계금액

			$('#txtBalance').attr('value',liClaimPay).text(__num2str(liClaimPay)); //잔여
		}else{
			var liLimitPay = $('#infoClient').attr('limitAmt');
			var liClaimPay = liLimitPay;
			var liBalance  = Math.abs(__str2num($('#txtBalance').attr('value')));
			var liSvcCost  = $('#infoClient').attr('svcCost');

			$('#txtLimitPay').attr('value',liLimitPay).text(__num2str(liLimitPay)); //한도

			if (liBalance > 0){
				liClaimPay = liLimitPay - liBalance;
			}else{
				liClaimPay = liBalance;
			}
			$('#txtBalance').attr('value',liClaimPay).text(__num2str(liClaimPay)); //잔여
		}
	}catch(e){
	}
}

//에러메세지
function _planErrorMsg(liIdx){
	var lsMsg = '';

	switch(liIdx){
		case 2:
			lsMsg = '요양보호사 일정중복';
			break;
		
		case 21:
			lsMsg = '부요양보호사 일정중복';
			break;

		case 3:
			lsMsg = '수급자 일정중복';
			break;

		case 4:
			lsMsg = '전일정과 2시간간격';
			break;

		case 5:
			lsMsg = '목욕1일1회 초과';
			break;

		case 6:
			lsMsg = '목욕 주간횟수 초과';
			break;

		case 7:
			lsMsg = '가족수가 일정 초과';
			break;

		case 8:
			lsMsg = '가족수가 제한 횟수 초과';
			break;

		case 9:
			lsMsg = '한도초과';
			break;

		case 31:
			lsMsg = '2인요양 시간 초가';
			break;

		case 32:
			lsMsg = '요양 인원수 초가';
			break;

		case 33:
			lsMsg = '전일정 시간 초가';
			break;

		case 34:
			lsMsg = '90분 시간 초가';
			break;

		case 35:
			lsMsg = '하루 3회 일정초과';
			break;

		case 36:
			lsMsg = '하루 210분이상 초과';
			break;

		case 37:
			lsMsg = '210분이상은 하루에 한번';
			break;

		case 41:
			lsMsg = '가족일정중복';
			break;

		case 51:
			lsMsg = '주야간보호 일정중복';
			break;

		case 91:
			lsMsg = '오늘30분등록제한';
			break;
	}

	return lsMsg;
}

function _planMouseOver(aoObj){
	$(aoObj).css('background-color','#dfe5f5');
}

function _planMouseOut(aoObj){
	$(aoObj).css('background-color','#ffffff');
}

/*********************************************************
 * 직원찾기
 *********************************************************/
function _planMemFind(aiIdx, asCode, asJumin, asSvcCd, asMemCd, asFamilyYn, asReturn){
	var code     = asCode;
	var jumin    = asJumin;
	var svcCd    = asSvcCd;
	var memCd    = asMemCd;
	var ynFamily = (asFamilyYn ? asFamilyYn : 'N');
	var subCd = '';

	if (asFamilyYn == 'Y'){
		subCd = '200';
	}else{
		subCd = asFamilyYn;
	}

	if (!asReturn) asReturn = 'lfMemFindResult';

	var h = 400;
	var w = 600;
	var t = (screen.availHeight - h) / 2;
	var l = (screen.availWidth - w) / 2;

	var url    = '../inc/_find_person.php';
	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
	var win    = window.open('about:blank', 'FIND_MEMBER', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			'type'		:'member'
		,	'code'		:code
		,	'kind'		:svcCd
		,	'jumin'		:jumin
		,	'yoy'		:memCd
		,	'idx'		:aiIdx
		,	'subCd'		:subCd
		,	'ynFamily'	:ynFamily
		,	'return'	:asReturn
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

	form.setAttribute('target', 'FIND_MEMBER');
	form.setAttribute('method', 'post');
	form.setAttribute('action', url);

	document.body.appendChild(form);

	form.submit();
}

/*********************************************************
 * 건보에 일정 업로드
 *********************************************************/
function _longcareUpload(YYMM, svcKind, uploadYN, debug, chgSayu, chgSayuEtc, lgPara){
	if (!uploadYN) uploadYN = 'Y';

	var msg = '건보 공단 홈페이지에 로그인되어 있지 않습니다.\n건보 공단 홈페이지 로그인을 선행하여 주십시오.';
	var lsYYMM = getToday().split('-').join('').substring(0,6);

	//alert($('#centerInfo').attr('value')); //32729000215

	try{
		if (debug == '1'){
			if (confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
				_planListNew(svcKind, uploadYN, chgSayu, chgSayuEtc, lgPara);
			}
		}else{
			$.ajax({
				type: 'GET',
				url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
				success: function (data){
					if ($('.welcome',data).html()){
						if (YYMM >= '201108'){
							var lbFlag = true;

							if (YYMM < lsYYMM){
								lbFlag = false;
							}

							if (uploadYN == 'Y'){
								if (confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
									if (!lbFlag){
										if (!confirm('현재 선택하신 년월은 현재보다 과거의 일정입니다.\n\n정말로 일정을 업로드 하시겠습니까?')) return;
									}
									_planListNew(svcKind, uploadYN, chgSayu, chgSayuEtc, lgPara);
								}
							}else{
								if (confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
									_planListNew(svcKind, uploadYN, chgSayu, chgSayuEtc, lgPara);
								}
							}
						}else{
							alert('건보공단 일정 업로드는 2011년 8월 일정부터 가능합니다.\n\n확인하여 주십시오.');
						}
					}else{
						alert('1 : '+msg);
					}
				},
				error:function(request, status, error){
					alert('ERROR');
				}
			});
		}
	}catch(e){
		alert('2 : '+msg);
	}
}

/*********************************************************
 * 수당검열
 *********************************************************/
function _planExtraPayChk(obj){
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

/*********************************************************
 * 공단 계획 입력 옵션 조회
 *********************************************************/
function _planOptionLoad(){
	$.ajax({
		type : 'POST'
	,	url  : '../iljung/plan_body_option.php'
	,	data : {
			'code'  : $('#centerInfo').attr('value')
		,	'jumin'	: $('#clientInfo').attr('value')
		,	'year'  : $('#planInfo').attr('year')
		,	'month' : $('#planInfo').attr('month')
		,	'svcCd' : $('#planInfo').attr('svcCd')
		,	'type'	: $('#document').attr('type')
		,	'para'	: $('#planInfo').attr('para')
		}
	,	beforeSend: function(){
		}
	,	success: function(data){
			$('#planOption').html(data).show();
		}
	});
}
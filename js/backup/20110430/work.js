var popupWork = null;

// -----------------------------------------------------------------------------
// 일별확정처리
function setDayConfCalendar(myBody, mCode, mKind, mYear, mMonth){
	var URL = 'day_conf_calendar.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 일별확정리스트
function getDayConfList(myBody, mCode, mKind, mYear, mMonth, mDay){
	var URL = 'month_conf_sugupja_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mType:'DAY',
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mDay:mDay,
				mSugupja:'all'
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// -----------------------------------------------------------------------------
// 월별확정처리
function getMonthConfList(myBody, mYear, mCode, mKind, mRate){
	var URL = 'month_conf_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mYear:mYear,
				mCode:mCode,
				mKind:mKind,
				mRate:mRate
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급자별 청구액산정 확정처리
function goMonthConfSugupja(mYear, mMonth, mCode, mKind, mSugupja){
	document.f.curYear.value = mYear;
	document.f.curMonth.value = mMonth;
	document.f.curMcode.value = mCode;
	document.f.curMkind.value = mKind;
	document.f.curSugupja.value = mSugupja;
	document.f.action = 'month_conf_sugupja.php';
	document.f.submit();
}

// 수급자별 청구액산정 상세내역
function getMonthConfSugupjaList(myBody, mYear, mMonth, mCode, mKind, mSugupja, isManager){
	var URL = 'month_conf_sugupja_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mYear:mYear,
				mMonth:mMonth,
				mCode:mCode,
				mKind:mKind,
				mSugupja:mSugupja,
				isManager:isManager
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				getMonthConfSugupjaDetail(myDetail, mYear, mMonth, mCode, mKind, mSugupja);
			//	setSugupConfSum();
			}
		}
	);
}

// 수급자별 청국액산정 내역
function getMonthConfSugupjaDetail(myBody, mYear, mMonth, mCode, mKind, mSugupja){
	var URL = 'month_conf_sugupja_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mYear:mYear,
				mMonth:mMonth,
				mCode:mCode,
				mKind:mKind,
				mSugupja:mSugupja
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				setSugupTimeSum();
			}
		}
	);
}

// 수급자 실적시간을 계획시간에서 가져오기
function setPlanTimeToWorkTime(index){
	if (index == 'all'){
		for(var i=0; i<document.getElementsByName('planToWork[]').length; i++){
			if (document.getElementsByName('planToWork[]')[i].value == 'Y'){
				document.getElementsByName('workDate[]')[i].value     = document.getElementsByName('planDate[]')[i].value;
				document.getElementsByName('workFmTime[]')[i].value   = document.getElementsByName('planFromTime[]')[i].value;
				document.getElementsByName('workToTime[]')[i].value   = document.getElementsByName('planToTime[]')[i].value;
				document.getElementsByName('workProcTime[]')[i].value = document.getElementsByName('planSoyoTime[]')[i].value;
				document.getElementsByName('planProcTime[]')[i].value = document.getElementsByName('planSoyoTime[]')[i].value;
				setWorkPorcTime(i);
			}
		}
	}else{
		document.getElementsByName('workDate[]')[index].value     = document.getElementsByName('planDate[]')[index].value;
		document.getElementsByName('workFmTime[]')[index].value   = document.getElementsByName('planFromTime[]')[index].value;
		document.getElementsByName('workToTime[]')[index].value   = document.getElementsByName('planToTime[]')[index].value;
		document.getElementsByName('workProcTime[]')[index].value = document.getElementsByName('planSoyoTime[]')[index].value;
		document.getElementsByName('planProcTime[]')[index].value = document.getElementsByName('planSoyoTime[]')[index].value;
		setWorkPorcTime(index);
	}
}

// 근무현황 실시간 조회
function getWorkRealList(mCode, mKind, mStat){
	var URL = 'work_real_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mStat:mStat
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 근무현황 월전체 조회
function getWorkMonthList(mCode, mKind, mYoy, mYear, mMonth){
	if (mYoy == ''){
		alert('요양사를 선택하여 주십시오.');
		return;
	}
	var URL = 'work_month_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mYoy:mYoy,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급시간 조회
function getSugupTimetList(mCode, mKind, mYear, mMonth, mSugup, mKey){
	if (mSugup == ''){
		alert('수급자를 선택하여 주십시오.');
		return;
	}

	var URL = 'suguptime_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mSugup:mSugup,
				mKey:mKey,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				setSugupTimeSum();
				getSugupSumList(sumBody, mCode, mKind, mYear, mMonth, mSugup, mKey);
			}
		}
	);
}

// 수급시간 합계
function getSugupSumList(pBody, pCode, pKind, pYear, pMonth, pSugup, pKey){
	if (pSugup == ''){
		return;
	}

	var URL = 'sugup_sumlist.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:pCode,
				mKind:pKind,
				mSugup:pSugup,
				mKey:pKey,
				mYear:pYear,
				mMonth:pMonth
			},
			onSuccess:function (responseHttpObj) {
				pBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급시간 팝업 조회
function popupSugupTimeList(mCode, mKind, mYear, mMonth, mSvcCode, mKey){
	var width  = 900;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popupWork = window.open('suguptime_popup.php?mPopup=Y&mCode='+mCode+'&mKind='+mKind+'&mYear='+mYear+'&mMonth='+mMonth+'&mSvcCode='+mSvcCode+'&mKey='+mKey, 'POPUP_SUGUP_TIME_LIST', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 수급시간 팝업 로드
function popupSugupTimeLoad(mPopup, mCode, mKind, mYear, mMonth, mSvcCode, mKey){
	var URL = 'suguptime_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mPopup:mPopup,
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mSvcCode:mSvcCode,
				mKey:mKey
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급시간 집계
function setSugupTimeSum(){
	var planProcTime = document.getElementsByName('planProcTime[]');
	var workProcTime = document.getElementsByName('workProcTime[]');
	var sugaPrice = document.getElementsByName('sugaPrice[]');
	var sugaPricePlan = document.getElementsByName('sugaPricePlan[]');
	var planTimeSum = 0;
	var workTimeSum = 0;
	var sugaAmount = 0;
	var sugaPlanAmount = 0;

	for(var i=0; i<planProcTime.length; i++){
		planTimeSum    += parseInt(planProcTime[i].value);
		workTimeSum    += parseInt(workProcTime[i].value);
		sugaAmount     += parseInt(sugaPrice[i].value);
		sugaPlanAmount += parseInt(sugaPricePlan[i].value);
	}
	
	planTime.innerHTML = planTimeSum+'분('+((planTimeSum/60).toFixed(1))+'H)';
	realTime.innerHTML = workTimeSum+'분('+((workTimeSum/60).toFixed(1))+'H)';
	totalSuga.innerHTML = __commaSet(sugaAmount);
	totalSugaPlan.innerHTML = __commaSet(sugaPlanAmount);
}

// 근무시간, 수당산정 조회
function getWorkTimetList(mCode, mKind, mSvcCode, mYoy, mYear, mMonth, mKey){
	if (mYoy == ''){
		alert('요양사를 선택하여 주십시오.');
		return;
	}

	var URL = 'worktime_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mSvcCode:mSvcCode,
				mYoy:mYoy,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 청구액산정 확정처리 조회
function getSugupConfList(mCode, mKind, mYear, mMonth, mRate){
	var URL = 'sugupconf_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mRate:mRate
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				//setSugupConfSum();
			}
		}
	);
}

// 수급자 리스트
function setSugupList(mCode, mSugup){
	var mKind    = document.f.mKind.value;
	var mYear    = document.f.mYear.value;
	var mMonth   = document.f.mMonth.value;
	var mSvcCode = document.f.mSvcCode.value;

	var request = getHttpRequest('../inc/_check.php?gubun=getSugupSvcList&mCode='+mCode+'&mKind='+mKind+'&mSvcCode='+mSvcCode+'&mDate='+mYear+mMonth);
	var select = null;	
		select = document.getElementById(mSugup);
		select.innerHTML = '';

		__setSelectBox(select, '', '-수급자선택-');

	var suList = request.split(';;');
	
	for(var i=0; i<suList.length - 1; i++){
		var suValue = suList[i].split('//');

		__setSelectBox(select, suValue[0], suValue[1]);
	}
}

// 요양사 리스트
function setYoyList(mCode, mYoy){
	var mKind    = document.f.mKind.value;
	var mYear    = document.f.mYear.value;
	var mMonth   = document.f.mMonth.value;
	var mSvcCode = '';

	try{
		mSvcCode = document.f.mSvcCode.value;
	}catch(e){}
	

	var request = getHttpRequest('../inc/_check.php?gubun=getYoySvcListAll&mCode='+mCode+'&mKind='+mKind+'&mSvcCode='+mSvcCode+'&mDate='+mYear+mMonth);
	var select = null;	
		select = document.getElementById(mYoy);
		select.innerHTML = '';

		__setSelectBox(select, '', '-요양사선택-');

	var suList = request.split(';;');
	
	for(var i=0; i<suList.length - 1; i++){
		var suValue = suList[i].split('//');

		__setSelectBox(select, suValue[0], suValue[1]);
	}
}

// 수급확정 시간 입력
function setWorkPorcTime(index){
	var svcCode      = document.getElementsByName('mSvcCode[]')[index].value;
	var workFmTime   = document.getElementsByName('workFmTime[]')[index].value.split(':').join('');
	var workToTime   = document.getElementsByName('workToTime[]')[index].value.split(':').join('');
	var workProcTime = document.getElementsByName('workProcTime[]')[index];
	var timeF1 = workFmTime.substring(0,2);
	var timeF2 = workFmTime.substring(2,4);
	var timeT1 = workToTime.substring(0,2);
	var timeT2 = workToTime.substring(2,4);
	
	var tempH, tempM;

	if (timeF1.substring(0,1) == '0') timeF1 = timeF1.substring(1,2);
	if (timeF2.substring(0,1) == '0') timeF2 = timeF2.substring(1,2);
	if (timeT1.substring(0,1) == '0') timeT1 = timeT1.substring(1,2);
	if (timeT2.substring(0,1) == '0') timeT2 = timeT2.substring(1,2);

	if (timeF2 > 59){
		timeF2 = 59;
		tempH = timeF1;
		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = timeF2
		document.getElementsByName('workFmTime[]')[index].value = tempH + tempM;
	}
	if (timeT2 > 59){
		timeT2 = 59;
		tempH = timeF1;
		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = timeF2
		document.getElementsByName('workToTime[]')[index].value = tempH + tempM;
	}
	
	var timeF = parseInt(timeF1) * 60 + parseInt(timeF2);
	var timeT = parseInt(timeT1) * 60 + parseInt(timeT2);

	if (timeF > timeT){
		//timeT = timeT + (12 * 60);
		timeT = timeF + parseInt(workProcTime.value);

		tempH = parseInt(timeT / 60);
		tempM = timeT % 60;

		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = (tempM < 10 ? '0' : '') + tempM;

		document.getElementsByName('workToTime[]')[index].value = tempH + tempM;
	}

	var timeP = timeT - timeF;

	if (isNaN(timeP)){
		timeP = 0;
	}

	if (timeP < 1) timeP = 0;
	
	if (svcCode == '200'){
		if (timeP > 240) timeP = 240;
	}
	
	/*
	if (svcCode == '500' || svcCode == '800'){
		if (timeP > 89){
			timeP = 89;
		}
		timeP ++;
	}
	*/

	timeP = cut(timeP, 30);

	document.getElementsByName('changeFlag[]')[index].value = 'Y';
	
	/*
	if (svcCode == '500' || svcCode == '800'){
		workProcTime.value = timeP - 1;
	}else{
		workProcTime.value = timeP;
	}
	*/
	workProcTime.value = timeP;
	
	if (timeP == 0){
		document.getElementsByName('sugaName[]')[index].innerHTML  = '';
		document.getElementsByName('sugaValue[]')[index].innerHTML = 0;
		document.getElementsByName('sugaPrice[]')[index].value     = 0;

		setSugupTimeSum();

		return;
	}
	
	/*
	 * 야간 및 심야 할증을 계산한다.
	 *
	 ****************************************************************************************/
	var TN      = cutOff(timeP);
	var ETNtime = 0; //야간시간
	var NTNtime = 0; //심야시간
	var ERang1 = 18 * 60;
	var ERang2 = 21 * 60 + 59;
	var NRang1 = 22 * 60;
	var NRang2 = 24 * 60 + 3 * 60 + 59;
	var NRang3 = 3 * 60 + 59;

	var EAMT  = 0;
	var NAMT  = 0;
	var EFrom = 0;
	var ETo   = 0;
	var NFrom = 0;
	var NTo   = 0;

	EFrom = cut(timeF - ERang1, 30);
	ETo   = cut(timeT - ERang1, 30);

	if (svcCode == '200'){
		if (timeF < NRang3){
			NFrom   = NRang3 - timeF;
			NTo     = NRang3 - timeT;
			NTNtime = NFrom - (NTo < 0 ? 0 : NTo);
		}else{
			NFrom   = timeF - NRang1;
			NTo     = timeT - NRang1;
			NTNtime = NTo - (NFrom < 0 ? 0 : NFrom);
		}
		
		ETNtime = ETo - (EFrom < 0 ? 0 : EFrom);

		NTNtime = NTNtime < 0 ? 0 : NTNtime;
		ETNtime = ETNtime < 0 ? 0 : ETNtime - NTNtime;
	}else{
		// 목욕 및 간호는 할증을 실행하자 않는다.
		NTNtime = 0;
		ETNtime = 0;
	}
	/****************************************************************************************/

	timeP /= 30;

	if (timeP < 1) timeP = 1;
	if (timeP > 8) timeP = 8;

	if (svcCode == '200'){
		var sugaCode = document.getElementsByName('sugaCode[]')[index].value.substring(0,4);
	}else if (svcCode == '500'){
		var sugaCode = document.getElementsByName('sugaCode[]')[index].value;
	}else{
		if (timeP > 3){
			timeP = 3;
		}
		var sugaCode = document.getElementsByName('sugaCode[]')[index].value.substring(0,4) + timeP;
	}
	
	var request = getHttpRequest('../inc/_check.php?gubun=getSugaTimeValue&mCode='+document.f.mCode.value+'&mSvcCode='+svcCode+'&mSugaCode='+sugaCode+'&mTime='+timeP);
	var suga    = request.split('//');
	var sugaValue = 0;

	if (ETNtime > 0) EAMT = cutOff((parseInt(suga[2]) * (ETNtime / TN)) * 0.2);
	if (NTNtime > 0) NAMT = cutOff((parseInt(suga[2]) * (NTNtime / TN)) * 0.3);

	sugaValue = parseInt(suga[2]) + EAMT + NAMT;
	//sugaValue = suga[2];

	var sugaNameString = null;

	if (suga[1].length > 8){
		sugaNameString = suga[1].substring(0, 8) + '...';
	}else{
		sugaNameString = suga[1];
	}

	document.getElementsByName('sugaCode[]')[index].value      = suga[0];
	document.getElementsByName('sugaName[]')[index].innerHTML  = sugaNameString;
	document.getElementsByName('sugaName[]')[index].title      = suga[1];
	document.getElementsByName('sugaValue[]')[index].innerHTML = __commaSet(sugaValue);
	document.getElementsByName('sugaPrice[]')[index].value     = sugaValue;

	setSugupTimeSum();
}

// 수급확정 시간 입력
function _set_conf_proc_time(index){
	var svcCode      = document.getElementsByName('svc_code[]')[index].value;
	var workFmTime   = document.getElementsByName('conf_from[]')[index].value.split(':').join('');
	var workToTime   = document.getElementsByName('conf_to[]')[index].value.split(':').join('');
	var workProcTime = document.getElementsByName('conf_time[]')[index];

	var timeF1 = workFmTime.substring(0,2);
	var timeF2 = workFmTime.substring(2,4);
	var timeT1 = workToTime.substring(0,2);
	var timeT2 = workToTime.substring(2,4);
	
	var tempH, tempM;

	if (timeF1.substring(0,1) == '0') timeF1 = timeF1.substring(1,2);
	if (timeF2.substring(0,1) == '0') timeF2 = timeF2.substring(1,2);
	if (timeT1.substring(0,1) == '0') timeT1 = timeT1.substring(1,2);
	if (timeT2.substring(0,1) == '0') timeT2 = timeT2.substring(1,2);

	if (timeF2 > 59){
		timeF2 = 59;
		tempH = timeF1;
		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = timeF2
		document.getElementsByName('conf_from[]')[index].value = tempH + tempM;
	}
	if (timeT2 > 59){
		timeT2 = 59;
		tempH = timeF1;
		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = timeF2
		document.getElementsByName('conf_to[]')[index].value = tempH + tempM;
	}
	
	var timeF = parseInt(timeF1) * 60 + parseInt(timeF2);
	var timeT = parseInt(timeT1) * 60 + parseInt(timeT2);

	if (timeF > timeT){
		//timeT = timeT + (12 * 60);
		timeT = timeF + parseInt(workProcTime.value);

		tempH = parseInt(timeT / 60);
		tempM = timeT % 60;

		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = (tempM < 10 ? '0' : '') + tempM;

		document.getElementsByName('conf_to[]')[index].value = tempH + tempM;
	}

	var timeP = timeT - timeF;

	if (isNaN(timeP)){
		timeP = 0;
	}

	if (timeP < 1) timeP = 0;

	if (svcCode == '200'){
		if (timeP > 240) timeP = 240;
	}

	/*
	if (svcCode == '500' || svcCode == '800'){
		if (timeP > 89){
			timeP = 89;
		}
		timeP ++;
	}
	*/

	timeP = cut(timeP, 30);

	var new_time = __add_time(document.getElementsByName('conf_from[]')[index].value, timeP);
	
	document.getElementsByName('conf_to[]')[index].value = new_time[0]+''+new_time[1];

	document.getElementsByName('change_flag[]')[index].value = 'Y';
	
	/*
	if (svcCode == '500' || svcCode == '800'){
		workProcTime.value = timeP - 1;
	}else{
		workProcTime.value = timeP;
	}
	*/
	workProcTime.value = timeP;
	
	if (timeP == 0){
		document.getElementsByName('suga_name[]')[index].innerHTML  = '';
		document.getElementsByName('suga_value[]')[index].innerHTML = 0;
		document.getElementsByName('suga_price[]')[index].value     = 0;

		//setSugupTimeSum();

		return;
	}
	
	/*
	 * 야간 및 심야 할증을 계산한다.
	 *
	 ****************************************************************************************/
	var TN      = cutOff(timeP);
	var ETNtime = 0; //야간시간
	var NTNtime = 0; //심야시간
	var ERang1 = 18 * 60;
	var ERang2 = 21 * 60 + 59;
	var NRang1 = 22 * 60;
	var NRang2 = 24 * 60 + 3 * 60 + 59;
	var NRang3 = 3 * 60 + 59;

	var EAMT  = 0;
	var NAMT  = 0;
	var EFrom = 0;
	var ETo   = 0;
	var NFrom = 0;
	var NTo   = 0;

	EFrom = cut(timeF - ERang1, 30);
	ETo   = cut(timeT - ERang1, 30);

	if (svcCode == '200'){
		if (timeF < NRang3){
			NFrom   = NRang3 - timeF;
			NTo     = NRang3 - timeT;
			NTNtime = NFrom - (NTo < 0 ? 0 : NTo);
		}else{
			NFrom   = timeF - NRang1;
			NTo     = timeT - NRang1;
			NTNtime = NTo - (NFrom < 0 ? 0 : NFrom);
		}
		
		ETNtime = ETo - (EFrom < 0 ? 0 : EFrom);

		NTNtime = NTNtime < 0 ? 0 : NTNtime;
		ETNtime = ETNtime < 0 ? 0 : ETNtime - NTNtime;
	}else{
		// 목욕 및 간호는 할증을 실행하자 않는다.
		NTNtime = 0;
		ETNtime = 0;
	}
	/****************************************************************************************/

	timeP /= 30;

	if (timeP < 1) timeP = 1;
	if (timeP > 8) timeP = 8;

	if (svcCode == '200'){
		var sugaCode = document.getElementsByName('suga_code[]')[index].value.substring(0,4);
	}else if (svcCode == '500'){
		var sugaCode = document.getElementsByName('suga_code[]')[index].value;
	}else{
		if (timeP > 3){
			timeP = 3;
		}
		var sugaCode = document.getElementsByName('suga_code[]')[index].value.substring(0,4) + timeP;
	}
	
	var request = getHttpRequest('../inc/_check.php?gubun=getSugaTimeValue&mCode='+document.f.code.value+'&mSvcCode='+svcCode+'&mSugaCode='+sugaCode+'&mTime='+timeP);
	var suga    = request.split('//');
	var sugaValue = 0;

	if (ETNtime > 0) EAMT = cutOff((parseInt(suga[2]) * (ETNtime / TN)) * 0.2);
	if (NTNtime > 0) NAMT = cutOff((parseInt(suga[2]) * (NTNtime / TN)) * 0.3);

	sugaValue = parseInt(suga[2]) + EAMT + NAMT;
	//sugaValue = suga[2];

	var sugaNameString = null;
	
	/*
	if (suga[1].length > 8){
		sugaNameString = suga[1].substring(0, 8) + '...';
	}else{
		sugaNameString = suga[1];
	}
	*/
	sugaNameString = suga[1];

	document.getElementsByName('suga_code[]')[index].value      = suga[0];
	document.getElementsByName('suga_name[]')[index].innerHTML  = sugaNameString;
	document.getElementsByName('suga_name[]')[index].title      = suga[1];
	document.getElementsByName('suga_value[]')[index].innerHTML = __commaSet(sugaValue);
	document.getElementsByName('suga_price[]')[index].value     = sugaValue;

	//setSugupTimeSum();
}

// 수급자 실적시간을 계획시간에서 가져오기
function _set_plan_to_conf(index){
	if (index == 'all'){
		for(var i=0; i<document.getElementsByName('plan_copy[]').length; i++){
			if (document.getElementsByName('plan_copy[]')[i].value == 'Y'){
				document.getElementsByName('conf_from[]')[i].value = __styleTime(document.getElementsByName('plan_from[]')[i].value);
				document.getElementsByName('conf_to[]')[i].value   = __styleTime(document.getElementsByName('plan_to[]')[i].value);
				document.getElementsByName('conf_time[]')[i].value = __styleTime(document.getElementsByName('plan_time[]')[i].value);

				_set_conf_proc_time(i);
			}
		}
	}else{
		document.getElementsByName('conf_from[]')[index].value = __styleTime(document.getElementsByName('plan_from[]')[index].value);
		document.getElementsByName('conf_to[]')[index].value   = __styleTime(document.getElementsByName('plan_to[]')[index].value);
		document.getElementsByName('conf_time[]')[index].value = __styleTime(document.getElementsByName('plan_time[]')[index].value);

		_set_conf_proc_time(index);
	}
}

// 수급자 입력 취소
function _set_conf_to_cancel(index){
	var cancel_flag = document.getElementsByName('cancel_flag[]');
	var conf_from   = document.getElementsByName('conf_from[]');
	var conf_to     = document.getElementsByName('conf_to[]');
	var conf_time   = document.getElementsByName('conf_time[]');
	var change_flag = document.getElementsByName('change_flag[]');

	if (cancel_flag[index].value == 'N'){
		cancel_flag[index].value = 'Y';
		conf_from[index].style.textDecoration = 'line-through';
		conf_to[index].style.textDecoration   = 'line-through';
		conf_time[index].style.textDecoration = 'line-through';
	}else{
		cancel_flag[index].value = 'N';
		conf_from[index].style.textDecoration = 'none';
		conf_to[index].style.textDecoration   = 'none';
		conf_time[index].style.textDecoration = 'none';
	}

	change_flag[index].value = 'Y';
}

// 근무수당 산정
function setWorkSudang(index){
	var payKind = document.getElementsByName('payKind[]')[index].value;
	var pay = document.getElementsByName('pay[]')[index].value;
	var workProcTime = document.getElementsByName('workProcTime[]')[index].value;

	var servicePrice = 0; //서비스건별 수당
	var defaultPrice = 0; //단순시급 수당
	var sugaRatePrice = 0; //수가비율 수당

	if (payKind == '1'){
		servicePrice = pay * (workProcTime / 60);
	}
	if (payKind == '2'){
		defaultPrice = pay * (workProcTime / 60);
	}

//	sugaRatePrice


//	$sugaRatePrice = number_format(cutOff(floor($row['t01_conf_suga_value'] * ($row['workProcTime'] / 100))));

	alert(servicePrice);

	document.getElementsByName('servicePrice[]')[index].innerHTML = servicePrice;
	document.getElementsByName('defaultPrice[]')[index].innerHTML = 0;
	document.getElementsByName('sugaRatePrice[]')[index].innerHTML = 0;
}

// 수급확정 시간 저장
function saveSugupConfirm(index){
	if (index == 'all'){
		if (!checkYM('2')){
			return;
		}

		var changeFlag = document.getElementsByName('changeFlag[]');
		var checkFlag = false;

		for(var i=0; i<changeFlag.length; i++){
			if (changeFlag[i].value == 'Y'){
				checkFlag = true;
				break;
			}
		}

		if (!checkFlag){
			alert('변경된 데이타가 없습니다.');
			return;
		}

		if (confirm('변경된 데이타를 저장하시겠습니까?')){
			document.f.submit();
		}
		return;
	}

	var mCode = document.f.mCode.value;
	var mKind = document.f.mKind.value;
	var mSugup   = document.f.mSugup.value;
	var mSvcCode = document.getElementsByName('mSvcCode[]')[index].value;
	var mDate    = document.getElementsByName('mDate[]')[index].value;
	var mFmTime  = document.getElementsByName('mFmTime[]')[index].value;
	var mSeq     = document.getElementsByName('mSeq[]')[index].value;
	var changeFlag = document.getElementsByName('changeFlag[]')[index].value;

	if (!checkYMD(mDate)){
		return;
	}

	if (changeFlag != 'Y'){
		alert('변경된 데이타가 없습니다.');
		return;
	}
	
	var workDate   = document.getElementsByName('workDate[]')[index].value;
	var workFmTime = document.getElementsByName('workFmTime[]')[index].value.split(':').join('');
	var workToTime = document.getElementsByName('workToTime[]')[index].value.split(':').join('');
	var workProcTime = document.getElementsByName('workProcTime[]')[index].value;
	var sugaCode  = document.getElementsByName('sugaCode[]')[index].value;
	var sugaPrice = document.getElementsByName('sugaPrice[]')[index].value;

	var request = getHttpRequest('suguptime_save_ok.php?changeFlag[0]='+changeFlag+'&mCode='+mCode+'&mKind='+mKind+'&mSugup='+mSugup+'&mDate[0]='+mDate+'&mFmTime[0]='+mFmTime+'&mSeq[0]='+mSeq+'&workDate[0]='+workDate+'&workFmTime[0]='+workFmTime+'&workToTime[0]='+workToTime+'&workProcTime[0]='+workProcTime+'&sugaCode[0]='+sugaCode+'&sugaPrice[0]='+sugaPrice);
}

// 청구액산정 합계
function setSugupConfSum(){
	var totalPay  = document.getElementsByName('totalPay[]');
	var boninPay1 = document.getElementsByName('boninPay1[]');
	var boninPay2 = document.getElementsByName('boninPay2[]');
	var boninPay3 = document.getElementsByName('boninPay3[]');
	var centerPay = document.getElementsByName('centerPay[]');

	var amtTotalPay = 0, amtBoninPay1 = 0, amtBoninPay2 = 0, amtBoninPay3 = 0, amtCenterPay = 0;

	for(var i=0; i<totalPay.length; i++){
		amtTotalPay  += parseInt(cutOff(totalPay[i].value));
		amtBoninPay1 += parseInt(cutOff(boninPay1[i].value));
		amtBoninPay2 += parseInt(cutOff(boninPay2[i].value));
		amtBoninPay3 += parseInt(cutOff(boninPay3[i].value));
		amtCenterPay += parseInt(cutOff(centerPay[i].value));
	}

	document.getElementById('amtTotalPay').innerHTML  = __commaSet(amtTotalPay);
	document.getElementById('amtBoninPay1').innerHTML = __commaSet(amtBoninPay1);
	document.getElementById('amtBoninPay2').innerHTML = __commaSet(amtBoninPay2);
	document.getElementById('amtBoninPay3').innerHTML = __commaSet(amtBoninPay3);
	document.getElementById('amtCenterPay').innerHTML = __commaSet(amtCenterPay);
}

// 확정전 일정 저장
function sugupDiaryOk(){
	var title = '';

	title += '실행년월 : ' + document.f.confYear.value + '년' + document.f.confMonth.value + '월\n';
	title += '수 급 자 : ' + document.f.confSugupja.value + '\n';
	title += '위 수급자의 일정을 수정하시겠습니까?';

	if (!confirm(title)){
		return;
	}

	document.f.action = 'month_diary_ok.php';
	document.f.submit();
}

// 일별 일정 저장
function dayDiaryOk(){
	var flag = document.getElementsByName('changeFlag[]');
	var check = false;

	for(var i=0; i<flag.length; i++){
		if (flag[i].value == 'Y'){
			check = true;
			break;
		}
	}

	if (!check){
		alert('변경된 내역이 없습니다.');
		return;
	}
	if (!confirm('입력하신 일정을 수정하시겠습니까?')){
		return;
	}

	document.f.action = 'day_diary_ok.php';
	document.f.submit();
}

// 청구액산정 확정
function sugupConfOk(){
	var title = '';
	var row = document.getElementById("listDetail");

	// 테스트 기관에서는 확정월의 확인을 스킵한다.
	/*
	if (document.getElementById('mCode').value != '1234'){
		if (!__checkYM(document.f.confYear.value + document.f.confMonth.value)){
			alert(document.f.confYear.value + '년' + document.f.confMonth.value + '월은 아직 확정처리할 수 없습니다.\n확인하여 주십시오.');
			return;
		}
	}
	*/
	
	title += '실행년월 : ' + document.f.confYear.value + '년' + document.f.confMonth.value + '월\n';
	title += '수 급 자 : ' + document.f.confSugupja.value + '\n';
	title += '위 데이타의 확정처리를 진행하시겠습니까?';

	if (!confirm(title)){
		return;
	}
	
	// 수정된 리스트 저장
	//for(var i=0; i<document.getElementsByName('planToWork[]').length; i++){
	//	saveSugupConfirm(i);
	//}

	document.f.action = 'month_conf_ok.php';
	document.f.submit();
}

// 청구액산정 확정 취소
function sugupConfCancel(){
	var row = document.getElementById("listDetail");

	if (row.childNodes.length < 1){
		alert('확정취소할 데이타가 없습니다.');
		return;
	}

	if (!confirm('확정취소를 진행하시겠습니까?')){
		return;
	}

	document.f.action = 'month_conf_del.php';
	document.f.submit();
}

// 일정완료 상세 팝업
function popupWorkDetail(pCode, pKind, pKey, pDate, pFmTime, pSeq){
	var width  = 300;
	var height = 316;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popupWork = window.open('work_detail_popup.php?mPopup=Y&mCode='+pCode+'&mKind='+pKind+'&mkey='+pKey+'&mDate='+pDate+'&mFmTime='+pFmTime+'&mSeq='+pSeq, 'POPUP_WORK_DETAIL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

//  일정완료 상세
function showWorkDetail(pPopup, pCode, pKind, pKey, pDate, pFmTime, pSeq){
	var URL = 'work_detail_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mPopup:pPopup,
				mCode:pCode,
				mKind:pKind,
				mKey:pKey,
				mDate:pDate,
				mFmTime:pFmTime,
				mSeq:pSeq
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 년월비교
function checkYM(gubun){
	var now = new Date();
	var nowYear  = now.getFullYear();
	var nowMonth = now.getMonth() + 1;
		nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	var nowDate  = nowYear + nowMonth;
	var mDate = document.f.mYear.value + document.f.mMonth.value;

	if (document.f.mYear.value != document.f.mYear.tag || document.f.mMonth.value != document.f.mMonth.tag){
		alert('검색을 다시 하여 주십시오.');
		return false;
	}

	if (gubun == '1'){
		if (nowDate <= mDate){
			alert('현재달의 데이타를 확정처리 및 변경하실 수 없습니다. 확인하여 주십시오.');
			return false;
		}
	}else{
		if (nowDate < mDate){
			alert('현재달의 데이타를 확정처리 및 변경하실 수 없습니다. 확인하여 주십시오.');
			return false;
		}
	}

	return true;
}

// 년월일비교
function checkYMD(pDate){
	var now = new Date();
	var nowYear  = now.getFullYear();
	var nowMonth = now.getMonth() + 1;
		nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	var nowDay   = now.getDate();
		nowDay   = (nowDay < 10 ? '0' : '') + nowDay;
	var nowDate  = nowYear + nowMonth + nowDay;
	var mDate = pDate;

	if (document.f.mYear.value != document.f.mYear.tag || document.f.mMonth.value != document.f.mMonth.tag){
		alert('검색을 다시 하여 주십시오.');
		return false;
	}

	if (nowDate < mDate){
		alert('현재일의 데이타는 변경하실 수 없습니다. 확인하여 주십시오.');
		return false;
	}

	return true;
}

// 청구서 월별
function getPaymentsBill(pBody, pCode, pKind, pYear, pMonth, pRate){
	var URL = 'payments_bill_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:pCode,
				mKind:pKind,
				mYear:pYear,
				mMonth:pMonth,
				mRate:pRate
			},
			onSuccess:function (responseHttpObj) {
				pBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 청구서 수급자별
function getPaymentsBillDetail(p_body, p_code, p_kind, p_ym){
	var URL = 'payments_bill_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYM:p_ym
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;
				//setTotalAmount();
			}
		}
	);
}

// 합계금액
function setTotalAmount(){
	document.getElementById('totAmount1').innerHTML = __commaSet(document.f.amount1.value);
	document.getElementById('totAmount2').innerHTML = __commaSet(document.f.amount2.value);
	document.getElementById('totAmount3').innerHTML = __commaSet(document.f.amount3.value);
}

// 본인부담 24호 프린트
function _printPayments24ho(pCode, pKind, pDate, pBoninYul, pKey, misu_yn){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_24ho_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey+'&misy_yn='+misu_yn, 'POPUP_24HO_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 24호X 프린트
function _printPayments24hox(pCode, pKind, pDate, pBoninYul, pKey, misu_yn){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('payments_24hox_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey+'&misy_yn='+misu_yn, 'POPUP_24HOX_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 영수증
function _printPaymentsBill(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('payments_bill_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 일정
function _showPaymentsDiary(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 800;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('payments_diary.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 수급일자별 리포트
function _printPaymentsAcc(pCode, pKind, pDate, pType){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_acc_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mType='+pType, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 명세서 발급내역
function _printPaymentIssu(pCode, pKind, pDate){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('payments_issu_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 위치조회 팝업
function _locationFind(code, kind, sugupja, sugupDate, sugupFmTime, sugupSeq, yoyangsa){
	var width  = 600;
	var height = 500;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('location.php?mCode='+code+'&mKind='+kind+'&sugupja='+sugupja+'&sugupDate='+sugupDate+'&sugupFmTime='+sugupFmTime+'&sugupSeq='+sugupSeq+'&yoyangsa='+yoyangsa, 'POPUP_LOCATION', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 청구서
function billPrint(p_code, p_kind, p_date, p_sugupja, p_boinYul){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	window.open('bill_print.php?mCode='+p_code+'&mKind='+p_kind+'&mDate='+p_date+'&mSugupja='+p_sugupja+'&mBoninYul='+p_boinYul, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
}

// 근무현황표
function workTableList(){
	var URL = 'table_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mService:document.f.mService.value,
				mPlan:document.f.mPlan.value,
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mYear:document.f.mYear.value,
				mMonth:document.f.mMonth.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 근무현황표 엑셀출력
function workTableExcel(){
	document.f.action = 'table_excel.php';
	document.f.submit();
}

// 서비스 제공일정표 (수급자기준)
function serviceCalendarList(){
	var URL = 'cal_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mYear:document.f.mYear.value,
				mType:document.f.mType.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 서비스 제공일정표
function serviceCalendarShow(p_code, p_kind, p_year, p_month, p_target, p_type, p_useType, p_printType, p_detailYN){
	var width  = 1000;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	
	if (p_printType == 'pdf'){
		window.open('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, 'POPUP', 'width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
		//var modal = showModalessDialog('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, window, 'dialogWidth:700px; dialogHeight:900px; dialogHide:yes; scroll:yes; status:yes');
		//var temp_window = showModelessDialog('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, window, 'dialogWidth:700px; dialogHeight:900px; dialogHide:yes;')
	}else{
		window.open('../work/cal_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&sugupja='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, 'POPUP', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
	}
}

// 서비스 제공일정표
function serviceCalendarPDF(){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	var code = document.f.mCode.value;
	var kind = document.f.mKind.value;
	var year = document.f.mYear.value;
	var month = document.f.mMonth.value;
	var sugupja = document.f.mJumin.value;

	window.open('cal_pdf.php?code='+code+'&kind='+kind+'&year='+year+'&month='+month+'&sugupja='+sugupja, 'PDF', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
}

// 제공일정표 출력
function _printServiceCalendar(){
	window.onbeforeprint = function(){
								var day = document.getElementsByName('day[]');

								for(var i=0; i<day.length; i++){
									day[i].style.fontSize = '9px';
								}

								btnPrint.style.display = 'none';
							}; //Hidden
	window.onafterprint = function(){
								var day = document.getElementsByName('day[]');

								for(var i=0; i<day.length; i++){
									day[i].style.fontSize = '12px';
								}

								btnPrint.style.display = '';
							}; //Show
	window.print();
}

// 에러난 일정 처리
function _errorDiaryConf(index){
	alert(index);
}

// 일정저장
function _diary_save(){
	var flag      = document.getElementsByName('change_flag[]');
	var conf_from = document.getElementsByName('conf_from[]');
	var conf_to   = document.getElementsByName('conf_to[]');
	var check     = false;

	for(var i=0; i<flag.length; i++){
		if (flag[i].value == 'Y'){
			check = true;

			alert(conf_from[i].value);

			if (!checkDate(conf_from[i].value)){
				alert('실적 시작시간 오류입니다. 확인하여 주십시오.');
				conf_from[i].focus();
				return;
			}

			if (!checkDate(conf_to[i].value)){
				alert('실적 종료시간 오류입니다. 확인하여 주십시오.');
				conf_from[i].focus();
				return;
			}

			break;
		}
	}

	if (!check){
		alert('변경된 내역이 없습니다.');
		return;
	}

	if (!confirm('입력하신 일정을 수정하시겠습니까?')){
		return;
	}

	//document.f.action = 'day_diary_ok.php';
	//document.f.submit();
}

// 수급자 실적확정
function _work_confirm(code, year, month, gubun){
	var w = 400;
	var h = 300;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	if (gubun == '1'){
		if (!confirm(year+'년 '+month+'월의 실적확정을 수동실행하시겠습니까?')) return;

		var win = window.open('../work/result_confirm.php?pos=2&code='+code+'&year='+year+'&month='+month+'&gubun='+gubun, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
			win.close();
	}else{
		if (!confirm(year+'년 '+month+'월의 급여계산을 수동실행하시겠습니까?')) return;

		var win = window.open('../work/result_salary.php?pos=2&code='+code+'&year='+year+'&month='+month+'&gubun='+gubun, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
			win.close();
	}

	/*
	var URL = 'result_confirm.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:code,
				year:year,
				month:month,
				gubun:gubun
			},
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
			}
		}
	);
	*/
}

// 명세서
function _payslip(code, kind, year, month, member){
	var f = document.f;
	var w = 900;
	var h = 700;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	var win = window.open('../work/salary_payslip.php?code='+code+'&kind='+kind+'&year='+year+'&month='+month+'&member='+member,'REPORT','width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}
var popupWork = null;
var __BATH_SUGA_RATE__ = 80; //목욕 시간차의 적용 비율
var winModal = null;

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

// 시간체크
function _check_time(object, event){
	if (event.keyCode != 13 && event.keyCode != 9){
		__onlyNumber(object);
		return true;
	}

	var time = object.value.split(':').join('');

	if (time.length != 4) return false;

	time = __styleTime(time);

	return checkDate(time);
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
	var svcKind      = document.getElementsByName('svc_kind[]')[index].value;
	var svcCode      = document.getElementsByName('svc_code[]')[index].value;
	var workFmTime   = document.getElementsByName('conf_from[]')[index].value.split(':').join('');
	var workToTime   = document.getElementsByName('conf_to[]')[index].value.split(':').join('');
	var workProcTime = document.getElementsByName('conf_time[]')[index];
	var holiday_yn   = document.getElementsByName('holiday[]')[index].value;
	var family_yn	 = document.getElementsByName('family[]')[index].value;
	
	if (!checkDate(__styleTime(document.getElementsByName('conf_from[]')[index].value)) && 
		!checkDate(__styleTime(document.getElementsByName('conf_to[]')[index].value))){

		document.getElementsByName('conf_from[]')[index].value = document.getElementsByName('conf_from[]')[index].tag;
		document.getElementsByName('conf_to[]')[index].value   = document.getElementsByName('conf_to[]')[index].tag;
		document.getElementsByName('conf_time[]')[index].value = document.getElementsByName('conf_time[]')[index].tag;

		_sum_tot_data();

		return;
	}

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
		document.getElementsByName('conf_from[]')[index].value = tempH + ':' +tempM;
	}
	if (timeT2 > 59){
		timeT2 = 59;
		tempH = timeF1;
		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = timeF2
		document.getElementsByName('conf_to[]')[index].value = tempH + ':' +tempM;
	}
	
	var timeF = parseInt(timeF1) * 60 + parseInt(timeF2);
	var timeT = parseInt(timeT1) * 60 + parseInt(timeT2);

	if (timeF > timeT){
		 //timeT = timeF + parseInt(workProcTime.value);
		timeT = timeT + 24 * 60;

		tempH = parseInt(timeT / 60);
		tempM = timeT % 60;

		tempH = (tempH < 10 ? '0' : '') + tempH;
		tempM = (tempM < 10 ? '0' : '') + tempM;

		document.getElementsByName('conf_to[]')[index].value = tempH + ':' +tempM;

		timeT = parseInt(tempH, 10) * 60 + parseInt(tempM, 10);
	}

	var timeP = timeT - timeF;
	var lb_add1Min = false;

	if (isNaN(timeP)){
		timeP = 0;
	}

	if (timeP < 1) timeP = 0;

	/*
	 * 270이상 수가틀 처리하기 위해 막는다.
	if (svcCode == '200'){
		if (timeP > 240) timeP = 240;
	}
	*/

	//재가의 최대근무시간을 510분으로 설정한다.
	if (svcCode == '200')
		if (timeP > 510) timeP = 510;

	// 동거가족은 최대 90분이다.
	if (family_yn == 'Y'){
		if (timeP > 90) timeP = 90;
	}

	/*
	if (svcCode == '500' || svcCode == '800'){
		if (timeP > 89){
			timeP = 89;
		}
		timeP ++;
	}
	*/
	
	if (svcKind == '0'){
		/**************************************************
		
			방문요양은 30분 단위로 절사
			
		**************************************************/
		//if (svcCode == '200') timeP = cut(timeP, 30);
		
	}else if (svcKind == '4'){
		/**************************************************
		
			장애활동지원은 60분 단위로 절사
		
		**************************************************/
		if (svcCode == '200') timeP = cut(timeP, 60);
		
	}else{
	}

	var new_time = __add_time(document.getElementsByName('conf_from[]')[index].value, timeP);

	document.getElementsByName('conf_to[]')[index].value = new_time[0]+':'+new_time[1];

	document.getElementsByName('change_flag[]')[index].value = 'Y';

	/*
	if (svcCode == '500' || svcCode == '800'){
		workProcTime.value = timeP - 1;
	}else{
		workProcTime.value = timeP;
	}
	*/

	//시간 절사 여부
	var lb_timeCut = false;

	if (svcKind == '0' || svcKind == '4')
		if (svcCode == '200') 
			lb_timeCut = true;

	if (lb_timeCut)
		workProcTime.value = cut(timeP, 30);
	else
		workProcTime.value = timeP;
	
	if (timeP == 0){
		document.getElementsByName('suga_name[]')[index].innerHTML  = '';
		document.getElementsByName('suga_value[]')[index].innerHTML = 0;
		document.getElementsByName('suga_price[]')[index].value     = 0;

		_sum_tot_data();

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

	//EFrom = cut(timeF - ERang1, 30);
	EFrom = timeF - ERang1;
	

	/*********************************************************
		근무시간이 510분이상 넘어갈 경우 510분까지만 인정한다.
	*********************************************************/
	if (parseInt(timeT, 10) - parseInt(timeF, 10) >= 8.5 * 60){ //510분초과시 510분 인정
		//ETo = cut((parseInt(timeF, 10) + 8.5 * 60) - ERang1, 30);
		ETo = (parseInt(timeF, 10) + 8.5 * 60) - ERang1;

	}else if (parseInt(timeT, 10) - parseInt(timeF, 10) >= 4.5 * 60){ //270분초과시 30분 빼기
		//ETo = (parseInt(timeF, 10) + (parseInt(timeT, 10) - parseInt(timeF, 10) /*- 30*/)) - ERang1;
		var liCutMin = 0;

		if (parseInt(timeT, 10) > ERang1){
			if (parseInt(timeT, 10) - parseInt(timeF, 10) <= 270){
				liCutMin = 30;
			}
		}

		ETo = (parseInt(timeF, 10) + (parseInt(timeT, 10) - parseInt(timeF, 10) - liCutMin)) - ERang1;
	
	}else if (parseInt(timeT, 10) - parseInt(timeF, 10) >= 4 * 60 && parseInt(timeT, 10) - parseInt(timeF, 10) <= 4.5 * 60){ //240분~270분시 240분적용
		ETo = (parseInt(timeF, 10) + 4 * 60) - ERang1;
	
	}else{
		//ETo = cut(timeT - ERang1, 30);
		ETo = timeT - ERang1;
	}
	
	if (svcCode == '200'){
		if (family_yn != 'Y'){
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
			NTNtime = cut(NTNtime, 30);
			ETNtime = ETNtime < 0 ? 0 : ETNtime - NTNtime;

			if (NTNtime > 480) NTNtime = 480;
			if (ETNtime > 480) ETNtime = 480;

			//새벽 6시 이전에 근무한 시간을 야간으로 적용한다.
			if (timeF < 360){
				var tmpTT = 360 - (timeT + 1);

				if (tmpTT < 0) tmpTT = 0;

				NTNtime = 360 - timeF - tmpTT;
			}
		}else{
			NTNtime = 0;
			ETNtime = 0;
		}
	}else{
		// 목욕 및 간호는 할증을 실행하자 않는다.
		NTNtime = 0;
		ETNtime = 0;
	}
	
	// 야간과 심야의 계산시간을 30분 단위로한다.
	NTNtime = cut(NTNtime, 30);
	ETNtime = cut(ETNtime, 30);

	timeP /= 30;
	//timeP = Math.round(timeP);
	timeP = Math.floor(timeP);

	if (timeP < 1) timeP = 1;
	//if (timeP > 8) timeP = 8;
	if (timeP > 9) timeP = 9;
	
	var Egubun = 'N';
	var Ngubun = 'N';

	var dt    = document.getElementsByName('plan_date[]')[index].value;
	var bipay = document.getElementsByName('bipay[]')[index].value;
	
	if (svcKind == '0'){
		if (svcCode == '200'){
			var sugaCode    = document.getElementsByName('suga_code[]')[index].value.substring(0,4);
			var suga_code   = sugaCode + timeP;
			var suga_name   = getHttpRequest('../inc/_check.php?gubun=getSugaName&mCode='+document.f.code.value+'&mSuga='+suga_code); //명칭
			var sugaPrice   = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.code.value+'&mSuga='+suga_code+'&mYM='+dt+'&bipay='+bipay)); //단가

			var tempValue = new Array();
			var tempTime  = new Array();
			var tempIndex = 0;

			if (timeP == 9){
				// 270분 이상일 경우 수가를 계산
				var tempFmH = timeF1;
				var tempFmM = timeF2;
				var tempToH = timeT1;
				var tempToM = timeT2;

				if (parseInt(tempFmH) > parseInt(tempToH)){
					tempToH = parseInt(tempToH) + 24;
				}
				tempFmH = parseInt(tempFmH) * 60 + parseInt(tempFmM);
				tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - parseInt(tempFmH);

				/*********************************************************
					최대 8시간 30분까지만 허용한다.
				*********************************************************/
				if (tempToH > 8.5 * 60) tempToH = 8.5 * 60;
				
				var tempL = cut(tempToH, 30) / 30;
				var tempK = 0;
				var temp_first = false;

				sugaPrice = 0;

				while(1){
					if (tempL >= 8){
						tempK = 8;
					}else if (tempL <= 0 || tempK <= 0){
						break;
					}else{
						tempK = tempL % 8;
					}
					tempL = tempL - tempK;

					if (!temp_first){
						tempL = tempL - 1; // 4시간후 30분을 뺀다.
						temp_first = true;

						if (tempFmH + (tempK * 30) >= 1320 ||
							tempFmH + (tempK * 30) <  360){
							//심야
							if (NTNtime > 0) NTNtime -= 30;
						}else if (tempFmH + (tempK * 30) >= 1080){
							//야간
							if (ETNtime > 0) ETNtime -= 30;
						}else{
							//주간
						}
					}

					tempValue[tempIndex] = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.code.value+'&mSuga='+sugaCode+tempK+'&mYM='+dt+'&bipay='+bipay)); 
					tempTime[tempIndex]  = tempK;

					sugaPrice += tempValue[tempIndex]; //parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga=CCWS'+tempK)); //단가

					tempIndex ++;
				}
			}

			var temp_e = 0;
			var i = 0;
			var liMax = 0;

			if (holiday_yn != 'Y'){
				var ETN = 0;
				var NTN = 0;

				switch(ETNtime){
					case 30 : ETN = 1; break;
					case 60 : ETN = 2; break;
					case 90 : ETN = 3; break;
					case 120: ETN = 4; break;
					case 150: ETN = 5; break;
					case 180: ETN = 6; break;
					case 210: ETN = 7; break;
					case 240: ETN = 8; break;
					default : ETN = 0;
				}

				switch(NTNtime){
					case 30 : NTN = 1; break;
					case 60 : NTN = 2; break;
					case 90 : NTN = 3; break;
					case 120: NTN = 4; break;
					case 150: NTN = 5; break;
					case 180: NTN = 6; break;
					case 210: NTN = 7; break;
					case 240: NTN = 8; break;
					default : NTN = 0;
				}

				if (NTNtime > 0){
					if (sugaCode != 'HS' && sugaCode != 'HD'){
						if (timeP == 9){
							temp_e = NTNtime / 30;
							//i = tempValue.length - 1;
							liMax = tempValue.length - 1;
							i = 0;

							NAMT = 0;

							while(1){
								//if (i < 0) break;
								if (i > liMax) break;
								if (temp_e <= 0) break;

								if (tempTime[i] >= temp_e){
									//NAMT += tempValue[i] * 0.3;
									NAMT += Math.floor((tempValue[i] / tempTime[i] * temp_e * 0.3));
									break;
								}else{
									NAMT += Math.floor(tempValue[i] * 0.3);
									temp_e -= tempTime[i];
								}

								//i--;
								i++;
							}
						}else{
							//NAMT = (sugaPrice * (NTN / TN)) * 0.3;
							NAMT = Math.floor((sugaPrice * (NTN / timeP)) * 0.3);
						}

						// 2011.04.29 절사에서 반올림으로 변경함.
						//NAMT = NAMT - (NAMT % 10); //절사
						//NAMT = __round(NAMT, 1, true); //반올림
						NAMT = __round(NAMT, 0, false);
					}
					Ngubun = 'Y';
				}

				if (ETNtime > 0){
					if (sugaCode != 'HS' && sugaCode != 'HD'){
						if (timeP == 9){
							temp_e = ETNtime / 30;
							
							//if (i == 0) i = tempValue.length - 1;
							if (i == 0){
								i = tempValue.length - 1;
							}else{
								if (tempTime[i] <= temp_e){
									i --;
								}

								if (i < 0) i = 0;
							}

							EAMT = 0;

							while(1){
								if (i < 0) break;
								if (temp_e <= 0) break;

								if (tempTime[i] >= temp_e){
									//EAMT += tempValue[i] * 0.2;
									EAMT += Math.floor((tempValue[i] / tempTime[i] * temp_e * 0.2));
									break;
								}else{
									EAMT += Math.floor(tempValue[i] * 0.2);
									temp_e -= tempTime[i];
								}

								i--;
							}
						}else{
							//alert(sugaPrice + '/' + ETN + '/' + TN);
							//EAMT = (sugaPrice * (ETN / TN)) * 0.2;
							EAMT = Math.floor((sugaPrice * (ETN / timeP)) * 0.2);
						}
						// 2011.04.29 절사에서 반올림으로 변경함.
						//EAMT = EAMT - (EAMT % 10); //절사
						//EAMT = __round(EAMT, 1, true); //반올림
						EAMT = __round(EAMT, 0, false);
					}
					Egubun = 'Y';
				}
			}
			
			if (timeP == 9){
				//TAMT = cutOff(parseInt(sugaPrice) + parseInt(EAMT) + parseInt(NAMT));
				TAMT = __round(parseInt(sugaPrice) + parseInt(EAMT) + parseInt(NAMT), 1, true);
			}else{
				TAMT = __round(parseInt(sugaPrice) + parseInt(EAMT) + parseInt(NAMT), 1, true);
			}

			var suga_value = TAMT;
		}else if (svcCode == '500' || svcCode == '800'){
			if (svcCode == '500'){
				var sugaCode = document.getElementsByName('suga_code[]')[index].value;
			}else if (svcCode == '800'){
				if (timeP > 3) timeP = 3;
				var sugaCode = document.getElementsByName('suga_code[]')[index].value.substring(0,4) + timeP;
			}else{
				var sugaCode = document.getElementsByName('suga_code[]')[index].value;
			}

			var request    = getHttpRequest('../inc/_check.php?gubun=getSugaTimeValue&mCode='+document.f.code.value+'&svcKind='+svcKind+'&mSvcCode='+svcCode+'&mSugaCode='+sugaCode+'&mTime='+timeP+'&mDT='+dt);
			var suga       = request.split('//');
			
			var suga_code  = suga[0];
			var suga_name  = suga[1];
			var suga_value = 0;

			if (ETNtime > 0) EAMT = cutOff((parseInt(suga[2]) * (ETNtime / TN)) * 0.2);
			if (NTNtime > 0) NAMT = cutOff((parseInt(suga[2]) * (NTNtime / TN)) * 0.3);

			suga_value = parseInt(suga[2]) + EAMT + NAMT;

			// 2011년 7월 1일부터 목욕 적용수가를 시간기준으로 변경한다.(2011.07.11 적용)
			if (dt >= '201107'){
				if (svcCode == '500'){
					var tempFmH = timeF1;
					var tempFmM = timeF2;
					var tempToH = timeT1;
					var tempToM = timeT2;

					if (parseInt(tempFmH) > parseInt(tempToH)){
						tempToH = parseInt(tempToH) + 24;
					}
					tempFmH = parseInt(tempFmH) * 60 + parseInt(tempFmM);
					tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - parseInt(tempFmH);

					if (tempToH < 40){
						suga_value = 0;
					}else if (tempToH >= 40 && tempToH < 60){
						suga_value = suga_value * __BATH_SUGA_RATE__ / 100;
						suga_value = cutOff(suga_value);
					}
				}
			}
		}
		
		
		
	/**************************************************
		
		장애인활동지원
		
	**************************************************/
	}else if (svcKind == 4){
		var c_cd     = document.getElementsByName('client[]')[index].value;
		var suga_cd  = document.getElementsByName('suga_code[]')[index].value;
		var bipay_yn = document.getElementsByName('bipay[]')[index].value;
		var date     = document.getElementsByName('plan_date[]')[index].value;
		var from_time= document.getElementsByName('conf_from[]')[index].value;
		var to_time  = document.getElementsByName('conf_to[]')[index].value;

		var bipay_pay = document.getElementsByName('bipay_pay[]')[index].value;

		var m_cd1 = document.getElementsByName('conf_mem_cd[]')[index].value;
		var m_cd2 = document.getElementsByName('conf_mem_cd2[]')[index].value;
		var m_cnt = 0;

		if (m_cd1 != '') m_cnt ++;
		if (m_cd2 != '') m_cnt ++;

		if (svcCode == '800'){
			suga_cd = suga_cd.substring(0,3);
			
			var diffMin = diffDate('n', from_time, to_time);

			if (diffMin >= 60){
				suga_cd += '30';
			}else if (diffMin >= 30){
				suga_cd += '20';
			}else{
				suga_cd += '10';
			}
		}

		var suga_tm  = getHttpRequest('../iljung/iljung_value.php?type=suga_voucher&mode=&code='+document.f.code.value+'&kind='+svcKind+'&svc_cd='+svcCode+'&suga_cd='+suga_cd+'&c_cd='+c_cd+'&bipay_yn='+bipay_yn+'&date='+date+'&from_time='+from_time+'&to_time='+to_time+'&m_cnt='+m_cnt+'&bipay_pay='+bipay_pay);
		//var cal_time = __round(TN / 60);
		var cal_time = 0;

		if (svcCode == '200'){
			cal_time = __round(TN / 60);
		}else{
			cal_time = TN;
		}
		
		var suga_if  = suga_tm.split('//');

		var suga_code  = suga_if[0];
		var suga_name  = suga_if[1];
		var suga_value = suga_if[2];

		if (dt >= '201107'){
			if (svcCode == '500'){
				var tempFmH = timeF1;
				var tempFmM = timeF2;
				var tempToH = timeT1;
				var tempToM = timeT2;

				if (parseInt(tempFmH) > parseInt(tempToH)){
					tempToH = parseInt(tempToH) + 24;
				}
				tempFmH = parseInt(tempFmH) * 60 + parseInt(tempFmM);
				tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - parseInt(tempFmH);

				if (tempToH < 40){
					suga_value = 0;
				}else if (tempToH >= 40 && tempToH < 60){
					suga_value = suga_value * __BATH_SUGA_RATE__ / 100;
					suga_value = cutOff(suga_value);
				}
			}
		}

	
	
	/**************************************************
	
		바우처 및 기타유료
	
	**************************************************/
	}else{
		var client   = document.getElementsByName('client[]')[index].value;
		var sugaCode = document.getElementsByName('suga_code[]')[index].value;
		var request  = getHttpRequest('../inc/_check.php?gubun=getSugaTimeValue&mCode='+document.f.code.value+'&svcKind='+svcKind+'&mSvcCode='+svcCode+'&mSugaCode='+sugaCode+'&mTime='+timeP+'&mDT='+dt+'&client='+client);
		var suga     = request.split('//');
		
		if (svcCode == 21){
			/***************************************************

				가사간병은 15 ~ 45분은 30분으로
				45분이상은 1시간로 계산한다.

			***************************************************/
			var cal_time = parseInt(workProcTime.value, 10) % 60;

			if (cal_time >= 15 && cal_time < 45){
				cal_time = 30;
			}else if (cal_time >= 45){
				cal_time = 60;
			}else{
				cal_time = 0;
			}

			cal_time = Math.floor(TN / 60) + (cal_time / 60);
		}else{
			/***************************************************

				시간으로 계산하여 시간당 수가금액을 계산한다.

			***************************************************/
			var cal_time = __round(TN / 60);
		}
		
		if (svcCode == '23' ||
			svcCode == '500' ||
			svcCode == '800'){
			/**************************************************
			
				산모신생아
				장애활동지원 - 방문목욕, 방문간호
			
			**************************************************/
			
			cal_time = 1;
		}else if (svcCode == '31'){
			/**************************************************
			
				기타유료 산모신생아
			
			**************************************************/
			if (cal_time > 6) cal_time = cal_time - 1;
		}

		var suga_code  = suga[0];
		var suga_name  = suga[1];
		var suga_value = parseInt(suga[2], 10) * cal_time;
	}

	var suga_note = document.getElementsByName('csv_suga[]')[index];

	if (bipay == 'Y'){
		suga_name += '[<span style=\'color:#ff0000;\'>비</span>]';

		var bipay_coust = __str2num(document.getElementsByName('suga_price[]')[index].value);
		
		if (suga_value != bipay_coust){
			suga_value = bipay_coust;
		}
	}

	if (suga_note == null){
		document.getElementsByName('suga_code[]')[index].value      = suga_code;
		document.getElementsByName('suga_name[]')[index].innerHTML  = suga_name;
		document.getElementsByName('suga_name[]')[index].title      = suga_name;
		document.getElementsByName('suga_value[]')[index].innerHTML = __commaSet(suga_value);
		document.getElementsByName('suga_price[]')[index].value     = suga_value;

		_sum_tot_data();
	}else{
		document.getElementsByName('suga_code[]')[index].value  = suga_code;
		document.getElementsByName('suga_price[]')[index].value = suga_value;
		document.getElementsByName('suga_name[]')[index].value  = suga_name;

		suga_note.innerHTML = suga_name+'['+__commaSet(suga_value)+']';
	}

	
	if (svcKind == 0){
		//재가요양일 때 한도금액 초과 여부를 확인한다.
		_sum_tot_data();
	}
}

// 수급자 실적시간을 계획시간에서 가져오기
function _set_plan_to_conf(index){
	var mode = document.getElementById('mode').value;

	if (index == 'all'){
		//if (!confirm('전체복사를 실행하시면 계획데이타가 실적데이타로 입력됩니다.\n전체복사를 실행하시겠습니까?')) return;
		var modal = showModalDialog('../inc/_msg.php?gubun=100', window, 'dialogWidth:400px; dialogHeight:150px; dialogHide:yes; scroll:no; status:no');

		if (modal != 1 && modal != 2) return;

		for(var i=0; i<document.getElementsByName('plan_copy[]').length; i++){
			if (document.getElementsByName('plan_copy[]')[i].value == 'Y'){
				if (modal == 1){
					if (document.getElementsByName('status_gbn[]')[i].value == '0' ||
						document.getElementsByName('status_gbn[]')[i].value == '9'){
						var set_flag = true;
					}else{
						var set_flag = false;
					}
				}else{
					var set_flag = true;
				}

				if (set_flag){
					document.getElementsByName('conf_from[]')[i].value   = __styleTime(document.getElementsByName('plan_from[]')[i].value);
					document.getElementsByName('conf_to[]')[i].value     = __styleTime(document.getElementsByName('plan_to[]')[i].value);
					document.getElementsByName('conf_time[]')[i].value   = document.getElementsByName('plan_time[]')[i].value;

					if (document.getElementsByName('cancel_flag[]')[i].value == 'Y'){
						document.getElementsByName('cancel_flag[]')[i].value = 'N';
						document.getElementsByName('conf_from[]')[i].style.textDecoration = 'none';
						document.getElementsByName('conf_to[]')[i].style.textDecoration   = 'none';
						document.getElementsByName('conf_time[]')[i].style.textDecoration = 'none';
					}

					if (mode == 1 || mode == 2){
						document.getElementsByName('conf_mem_cd[]')[i].value = document.getElementsByName('plan_mem_cd[]')[i].value;
						document.getElementsByName('conf_mem_nm[]')[i].value = document.getElementsByName('plan_mem_nm[]')[i].value;
					}

					_set_conf_proc_time(i);
				}
			}
		}

		alert('전체복사가 완료되었습니다.');
	}else{
		document.getElementsByName('conf_from[]')[index].value   = __styleTime(document.getElementsByName('plan_from[]')[index].value);
		document.getElementsByName('conf_to[]')[index].value     = __styleTime(document.getElementsByName('plan_to[]')[index].value);
		document.getElementsByName('conf_time[]')[index].value   = document.getElementsByName('plan_time[]')[index].value;

		if (mode == 1 || mode == 2){
			document.getElementsByName('conf_mem_cd[]')[index].value = document.getElementsByName('plan_mem_cd[]')[index].value;
			document.getElementsByName('conf_mem_nm[]')[index].value = document.getElementsByName('plan_mem_nm[]')[index].value;
		}
		
		if (document.getElementsByName('cancel_flag[]')[index].value == 'Y'){
			document.getElementsByName('cancel_flag[]')[index].value = 'N';
			document.getElementsByName('conf_from[]')[index].style.textDecoration = 'none';
			document.getElementsByName('conf_to[]')[index].style.textDecoration   = 'none';
			document.getElementsByName('conf_time[]')[index].style.textDecoration = 'none';
		}

		_set_conf_proc_time(index);
	}

	_sum_tot_data();
}

// 수급자 입력 취소
function _set_conf_to_cancel(index){
	var cancel_flag = document.getElementsByName('cancel_flag[]');
	var conf_from   = document.getElementsByName('conf_from[]');
	var conf_to     = document.getElementsByName('conf_to[]');
	var conf_time   = document.getElementsByName('conf_time[]');
	var change_flag = document.getElementsByName('change_flag[]');
	var status_gbn  = document.getElementsByName('status_gbn[]');

	var mode = document.getElementById('mode').value;
	
	if (index == 'all'){
		if (!confirm('전체취소를 실행하시면 입력된 데이타는 삭제되며 일정의 상태가 완료에서 미수행으로 변경됩니다.\n전체취소를 실행하시겠습니까?')) return;

		for(var i=0; i<cancel_flag.length; i++){
			if (cancel_flag[i].value == 'N'){
				if (status_gbn[i].value != '1' && status_gbn[i].value != 'C'){
					conf_from[i].value      = '';
					conf_to[i].value        = '';
					conf_time[i].value      = '0';
					change_flag[i].value    = 'N';
					document.getElementsByName('suga_name[]')[i].innerHTML  = '';
					document.getElementsByName('suga_value[]')[i].innerHTML = '0';
				}else{
					cancel_flag[i].value = 'Y';
					conf_from[i].style.textDecoration = 'line-through';
					conf_to[i].style.textDecoration   = 'line-through';
					conf_time[i].style.textDecoration = 'line-through';
					change_flag[i].value = 'Y';
				}
				
				if (mode == 1 || mode == 2){
					document.getElementsByName('conf_mem_cd[]')[i].value = document.getElementsByName('plan_mem_cd[]')[i].value;
					document.getElementsByName('conf_mem_nm[]')[i].value = document.getElementsByName('plan_mem_nm[]')[i].value;
				}
			}
		}
	}else{
		if (cancel_flag[index].value == 'N'){
			if (status_gbn[index].value != '1' && status_gbn[index].value != 'C'){
				conf_from[index].value      = '';
				conf_to[index].value        = '';
				conf_time[index].value      = '0';
				change_flag[index].value    = 'N';
				document.getElementsByName('suga_name[]')[index].innerHTML  = '';
				document.getElementsByName('suga_value[]')[index].innerHTML = '0';
			}else{
				cancel_flag[index].value = 'Y';
				conf_from[index].style.textDecoration = 'line-through';
				conf_to[index].style.textDecoration   = 'line-through';
				conf_time[index].style.textDecoration = 'line-through';
				change_flag[index].value = 'Y';
			}
			
			if (mode == 1 || mode == 2){
				document.getElementsByName('conf_mem_cd[]')[index].value = document.getElementsByName('plan_mem_cd[]')[index].value;
				document.getElementsByName('conf_mem_nm[]')[index].value = document.getElementsByName('plan_mem_nm[]')[index].value;
			}
		}else{
			return;
		}
	}

	_sum_tot_data();
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

	popup_iljung = window.open('../work/payments_24hox_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey+'&misy_yn='+misu_yn, 'POPUP_24HOX_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 영수증
function _printPaymentsBill(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_bill_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 명세서
function _printDetailBill(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_detail_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_DETAIL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 본인부담 일정
function _showPaymentsDiary(pCode, pKind, pDate, pBoninYul, pKey){
	var width  = 800;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_diary.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mBoninYul='+pBoninYul+'&mKey='+pKey, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 수급일자별 리포트
function _printPaymentsAcc(pCode, pDate, pType, pGbn, pHomecare, pVoucher){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_acc_print.php?mCode='+pCode+'&mDate='+pDate+'&mType='+pType+'&mGbn='+pGbn+'&mHomecare='+pHomecare+'&mVoucher='+pVoucher, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 명세서 발급내역
function _printPaymentIssu(pCode, pDate, pHomecare, pVoucher){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('../work/payments_issu_print.php?mCode='+pCode+'&mDate='+pDate+'&mHomecare='+pHomecare+'&mVoucher='+pVoucher, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
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

	window.open('../work/bill_print.php?mCode='+p_code+'&mKind='+p_kind+'&mDate='+p_date+'&mSugupja='+p_sugupja+'&mBoninYul='+p_boinYul, 'POPUP_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
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

function pdf(){
	var f = document.f;

	var w = 900;
	var h = 700;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

	f.target = 'SHOW_PDF';
	f.action = '../work/work_table_pdf.php';
	f.submit();
	f.target = '_self';
	f.action = '../work/table.php';
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
function serviceCalendarShow(p_code, p_kind, p_year, p_month, p_target, p_type, p_useType, p_printType, p_detailYN, p_pagePL, p_family){
	var width  = 850;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	
	switch(p_pagePL){
		case 'l':
			width  = 900;
			height = 700;
			break;
		default:
			width  = 700;
			height = 900;
			break;
	}

	var param = '';

	try{
		param = $('#svcParam').attr('value');
	}catch(e){
	}

	try{
		$printDT = $('#printDT').attr('value');
	}catch(e){
		$printDT = '';
	}
	
	if (!param) param = '';
	
	var w_gbn = '';

	if(p_type == 'y'){
		w_gbn = $('#w_gbn').attr('value');

		if (!w_gbn){
			w_gbn = $('#cboSubjectGbn option:selected').val();
		}
	}

	if (p_printType == 'pdf'){
		var win = window.open('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN+'&page_pl='+p_pagePL+'&family='+p_family+'&printDT='+$printDT+'&svcDtlYn='+$('#svcDtlYn').val()+'&param='+param+'&w_gbn='+w_gbn, 'POPUP', 'width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
		//var modal = showModalessDialog('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, window, 'dialogWidth:700px; dialogHeight:900px; dialogHide:yes; scroll:yes; status:yes');
		//var temp_window = showModelessDialog('../work/cal_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN, window, 'dialogWidth:700px; dialogHeight:900px; dialogHide:yes;')
	}else{
		var win = window.open('../work/cal_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&sugupja='+p_target+'&type='+p_type+'&useType='+p_useType+'&detail='+p_detailYN+'&param='+param+'&w_gbn='+w_gbn, 'POPUP2', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
	}
	
	try{
		win.focus();
	}catch(e){
	}
}

/*
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
*/

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

// 수급자 실적마감
function _work_confirm(code, year, month, mode){
	var w = 400;
	var h = 300;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var uri = location.href+'?mode='+mode+'&code='+code+'&year='+year+'&month='+month;

	if (mode == '1'){
		var conf_flag = document.getElementById('conf_flag').value;

		if (conf_flag == 'Y'){
			alert('실적마감이 이미 진행되었습니다. 재확정을 하시려면 실적마감을 취소 후 실행하여 주십시오.');
			return;
		}

		if (!confirm(year+'년 '+month+'월의 실적마감을 실행하시겠습니까?')) return;
		
		//if ($('#code').val() == '1234'){
		//	var win = window.open('../work/result_confirm.php?pos=1&code='+code+'&year='+year+'&month='+month+'&gubun='+mode, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=yes');
		//}else{
			var win = showModelessDialog('../work/result_confirm.php?pos=2&code='+code+'&year='+year+'&month='+month+'&gubun='+mode, window, 'dialogWidth:'+w+'px; dialogHeight:'+h+'px; dialogHide:yes; scroll:yes; status:yes');
			location.replace(uri);
		//}
	}else{
		var act_cls_yn = document.getElementById('act_cls_yn').value;

		if (act_cls_yn != 'Y'){
			alert('실적마감 처리가 되지 않았습니다. 실적마감 처리가 선행되어야 합니다.');
			return;
		}

		var conf_flag = document.getElementById('conf_flag').value;

		if (conf_flag != 'Y'){
			alert('실적마감이 진행되지 않았습니다. 실적마감이 선행되어야 합니다.');
			return;
		}

		var calc_flag = document.getElementById('calc_flag').value;

		if (calc_flag == 'Y'){
			alert('급여계산이 이미 진행되었습니다. 급여를 다시 계산하시려면 급여계산을 취소 후 실행하여 주십시오.');
			return;
		}

		var salary_cls_yn = document.getElementById('salary_cls_yn').value;

		if (salary_cls_yn == 'Y'){
			alert('급여계산을 실행하시려면 급여마감을 취소 후 다시 실행하여 주십시오.');
			return;
		}

		if (!confirm(year+'년 '+month+'월의 급여계산을 실행하시겠습니까?')) return;

		//var win = window.open('../work/result_salary.php?pos=2&code='+code+'&year='+year+'&month='+month+'&gubun='+mode, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
		var win = showModelessDialog('../work/result_salary.php?pos=2&code='+code+'&year='+year+'&month='+month+'&gubun='+mode, window, 'dialogWidth:'+w+'px; dialogHeight:'+h+'px; dialogHide:yes; scroll:yes; status:yes');
		location.reload(uri);
	}
}

// 명세서
function _payslip(code, kind, year, month, member, dept){
	var f = document.f;
	var w = 900;
	var h = 700;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	var win = window.open('../salaryNew/salary_payslip.php?code='+code+'&kind='+kind+'&year='+year+'&month='+month+'&member='+member+'&dept='+dept,'REPORT','width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

function _payslipHwp(code, kind, year, month, member, dept){
	var url = './salary_payslip_hwp.php';
	
	var parm = new Array();
		parm = {
				'kind':kind	
			,	'year':year
			,	'month':month
			,	'member':member
			,	'dept':dept
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

	form.setAttribute('target', '_self');
	form.setAttribute('method', 'post');
	form.setAttribute('action', url);
	
	document.body.appendChild(form);
	
	form.submit();
}

// 합계계산
function _sum_tot_data(){
	var mode        = document.getElementById('mode').value;
	var plan_day_cnt= document.getElementById('plan_day_cnt').value;
	var conf_day_cnt= document.getElementById('conf_day_cnt').value;
	var plan_time	= document.getElementsByName('plan_time[]');
	var plan_value	= document.getElementsByName('plan_value[]');
	var conf_time	= document.getElementsByName('conf_time[]');
	var conf_value	= document.getElementsByName('suga_value[]');
	var bipay       = document.getElementsByName('bipay[]');
	var count		= plan_time.length;

	var plan_hour	= 0;
	var plan_amt	= 0;
	var conf_hour	= 0;
	var conf_amt	= 0;
	
	for(var i=0; i<count; i++){
		if (bipay[i].value != 'Y'){
			plan_hour	+= __str2num(plan_time[i].value);
			plan_amt	+= __str2num(plan_value[i].innerHTML);
			conf_hour	+= __str2num(conf_time[i].value);
			conf_amt	+= __str2num(conf_value[i].innerHTML);
		}
	}

	if (mode == 1){
		tot_plan_time.innerHTML = plan_hour+'분 ['+__round(plan_hour/60, 1)+'H]';
		tot_conf_time.innerHTML = conf_hour+'분 ['+__round(conf_hour/60, 1)+'H]';
	}else{
		tot_plan_time.innerHTML = plan_day_cnt+'일 ['+__round(plan_hour/60, 1)+'H]';
		tot_conf_time.innerHTML = conf_day_cnt+'일 ['+__round(conf_hour/60, 1)+'H]';
	}
	tot_plan_amt.innerHTML  = __num2str(plan_amt);
	tot_conf_amt.innerHTML  = __num2str(conf_amt);

	_workLimitPayCheck();
}

// 급여조정
function _salary_edit(p_kind, p_jumin){
	var f = document.f;

	f.kind.value  = p_kind;
	f.jumin.value = p_jumin;
	f.action = 'salary_edit_2.php';
	f.submit();
}


// 건보계획입력서식
function gunboPlanShow(p_code, p_kind, p_year, p_month, p_target, p_svc){
	window.open('../work/gunbo_pdf_show.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target+'&svccd='+p_svc, 'POPUP', 'width=900,height=700,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

/*
 * 일정표출력 서비스별
 */
// 서비스 제공일정표
function _iljung_print(code, kind, year, month, target, service){
	var f      = document.f;
	var width  = 900;
	var height = 700;
	var left   = (window.screen.width  - width)  / 2;
	var top    = (window.screen.height - height) / 2;
	
	
	window.open('../work/iljung_show.php?code='+code+'&kind='+kind+'&year='+year+'&month='+month+'&target='+target+'&service='+service, 'POPUP', 'left='+left+',top='+top+',width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

/*
 * 일정
 */
function _set_iljung(index){
	var code = document.getElementById('code');
	var p_dt = document.getElementsByName('plan_date[]')[index];
	var c_cd = document.getElementsByName('client[]')[index];
	var m_cd = document.getElementsByName('plan_mem_cd[]')[index];
	var data = _add_conf_data(code.value, p_dt.value, c_cd.value, m_cd.value);

	if (data == undefined) return;
	if (data == 'ok'){
		location.reload();
	}
}



/*
 * 장기요양급여 명세서 출력
 */
function _show_bill(type, jumin, kind, svcKind, bipayYn, in_day, in_seq){
	var w = 700;
	var h = 900;
	
	if (type == '24hox' || type == 'receiptx' || type == 'detail' || type == 'in_reciept'){
		w = 900;
		h = 700;
	}
	
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	/*
	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;

	f.type.value  = type;
	f.jumin.value = jumin;
	f.target = 'SHOW_PDF';
	f.action = '../bill/?type=pdf';
	f.submit();
	f.target = '_self';
	*/

	if (winModal != null){
		try{
			winModal.close();
		}catch(e){
		}
	}
	
	winModal = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

	var pdf = document.createElement('form');

	pdf.appendChild(__create_input('code', $('#code').attr('value')));
	pdf.appendChild(__create_input('year', ($('#year').attr('value') != undefined ? $('#year').attr('value') : $('#year').text())));
	pdf.appendChild(__create_input('month', $('#month').attr('value')));
	pdf.appendChild(__create_input('printDT', $('#printDT').attr('value')));
	pdf.appendChild(__create_input('opt1', ($('#chkOpt1').attr('checked') != undefined ? $('#chkOpt1').attr('checked')?'Y':'N' : 'Y')));
	pdf.appendChild(__create_input('type', type));
	pdf.appendChild(__create_input('jumin', jumin));
	pdf.appendChild(__create_input('kind', kind));
	pdf.appendChild(__create_input('svcKind', svcKind));
	pdf.appendChild(__create_input('bipayYn', bipayYn));
	pdf.appendChild(__create_input('in_day', in_day));	//(입금영수증)입금일
	pdf.appendChild(__create_input('in_seq', in_seq));	//(입금영수증)입금순
	pdf.appendChild(__create_input('svc_homecare', ($('#svc_homecare').attr('value') == 'Y' ? 'Y' : 'N')));
	pdf.appendChild(__create_input('svc_voucher', ($('#svc_voucher').attr('value') == 'Y' ? 'Y' : 'N')));
	pdf.appendChild(__create_input('unpaid_yn', ($('#misu_amt_yn').attr('value') == 'Y' ? 'Y' : 'N')));
	

	pdf.setAttribute('method', 'post');
	
	document.body.appendChild(pdf);

	pdf.target = 'SHOW_PDF';
	pdf.action = '../bill/?type=pdf';
	pdf.submit();
}


function _show_bill2(type, jumin, kind, svcKind, bipayYn, in_day, in_seq){
	var w = 700;
	var h = 900;
	
	if (type == '24hox' || type == 'receiptx' || type == 'detail' || type == 'in_reciept'){
		w = 900;
		h = 700;
	}
	
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	/*
	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;

	f.type.value  = type;
	f.jumin.value = jumin;
	f.target = 'SHOW_PDF';
	f.action = '../bill/?type=pdf';
	f.submit();
	f.target = '_self';
	*/
	
	
	if (winModal != null){
		try{
			winModal.close();
		}catch(e){
		}
	}
	
	data = '';
	
	if(jumin == 'all'){
		$('input:checkbox[name="chkIn"]').each(function(){
			var obj = $(this).parent().parent();
			
			data += (data ? '?' : '');

			if ($(this).attr('checked')){
				data += 'cltCd='+$(this).attr('cltCd');
			}
		});
	}
	
	if(data == ''){
		alert('출력할 수급자를 선택하시기 바랍니다.');
		return false;
	}

	winModal = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

	var pdf = document.createElement('form');
	var data = data;

	pdf.appendChild(__create_input('code', $('#code').attr('value')));
	pdf.appendChild(__create_input('year', ($('#year').attr('value') != undefined ? $('#year').attr('value') : $('#year').text())));
	pdf.appendChild(__create_input('month', $('#month').attr('value')));
	pdf.appendChild(__create_input('printDT', $('#printDT').attr('value')));
	pdf.appendChild(__create_input('opt1', ($('#chkOpt1').attr('checked') != undefined ? $('#chkOpt1').attr('checked')?'Y':'N' : 'Y')));
	pdf.appendChild(__create_input('type', type));
	pdf.appendChild(__create_input('jumin', jumin));
	pdf.appendChild(__create_input('kind', kind));
	pdf.appendChild(__create_input('svcKind', svcKind));
	pdf.appendChild(__create_input('bipayYn', bipayYn));
	pdf.appendChild(__create_input('in_day', in_day));	//(입금영수증)입금일
	pdf.appendChild(__create_input('in_seq', in_seq));	//(입금영수증)입금순
	pdf.appendChild(__create_input('svc_homecare', ($('#svc_homecare').attr('value') == 'Y' ? 'Y' : 'N')));
	pdf.appendChild(__create_input('svc_voucher', ($('#svc_voucher').attr('value') == 'Y' ? 'Y' : 'N')));
	pdf.appendChild(__create_input('unpaid_yn', ($('#misu_amt_yn').attr('value') == 'Y' ? 'Y' : 'N')));
	pdf.appendChild(__create_input('data', data));

	pdf.setAttribute('method', 'post');
	
	document.body.appendChild(pdf);

	pdf.target = 'SHOW_PDF';
	pdf.action = '../bill/?type=pdf';
	pdf.submit();
}



/*********************************************************

	실적금액과 한도금액 비교

*********************************************************/
function _workLimitPayCheck(){
	if ($('#mode').val() != '2') return;
	if ($('#kind').val() != '0') return;

	var limitPay = __str2num($('#careLimitPay').text());
	var checkPay = 0;
	var limitYN  = false;

	$('.clsSuga').each(function(){
		checkPay += __str2num($(this).text());
		limitYN   = false;
		
		if (checkPay > limitPay)
			limitYN = true;

		if (limitYN)
			$(this).css('color', '#ff0000');
		else
			$(this).css('color', '#000000');
	});
}
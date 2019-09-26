var popup_iljung = null;
var bathTimes = 60; //목욕기본시간(분)

// -----------------------------------------------------------------------------
// 방문일정 수급자 리스트
function _getSugupjaList(mBody, mYear, mGubun, mCode, mKind){
	var URL = 'su_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mYear:mYear,
				mGubun:mGubun,
				mCode:mCode,
				mKind:mKind
			},
			onSuccess:function (responseHttpObj) {
				mBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 방문일정 등록
function _setSugupjaReg(mCode, mKind, mKey, mYear, mMonth){
	//var modal = showModalDialog('su_reg.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey, window, 'dialogWidth:940px; dialogHeight:768px; dialogHide:yes; scroll:yes; status:no; help:no;');
	//window.open('su_reg.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'SugupjaReg');

	var width  = 960;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	var popupIljung = window.open('su_reg.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'POPUP_DIRAY', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 방문일정 조회
function _setSugupjaSearch(mCode, mKind, mKey, mYear, mMonth){
	//var modal = showModalDialog('su_search.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey, window, 'dialogWidth:940px; dialogHeight:768px; dialogHide:yes; scroll:yes; status:no; help:no;');
	//window.open('su_search.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'SugupjaSearch');

	var width  = 960;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	var popupIljung = window.open('su_search.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'POPUP_DIRAY', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 특정 수급자 방문일정 등록
function _setSugupjaDiaryReg(mCode, mKind, mYear, mMonth, mSugupja, mKey){
	var width  = 960;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	var popupIljung = window.open('../iljung/su_modify.php?mCode='+mCode+'&mKind='+mKind+'&calYear='+mYear+'&calMonth='+mMonth+'&mKey='+mKey+'&mSugupja='+mSugupja, 'POPUP_DIRAY', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 특정 수급자 방문일별일정 등록
function _setSugupjaDayReg(mCode, mKind, mYear, mMonth, mDay, mSugupja, mYoyangsa, mKey){
	var width  = 960;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	//alert(mCode+'/'+mKind+'/'+mYear+'/'+mMonth+'/'+mDay+'/'+mSugupja+'/'+mKey);

	var popupIljung = window.open('../iljung/su_modify.php?mType=DAY&mCode='+mCode+'&mKind='+mKind+'&calYear='+mYear+'&calMonth='+mMonth+'&calDay='+mDay+'&mKey='+mKey+'&mSugupja='+mSugupja+'&mYoyangsa='+mYoyangsa, 'POPUP_DIRAY', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 시작시간입력 후 포커스 이동
function _setEntTimeFocus(){
	if(document.f.ftMin.value.length == 2){
		if(document.f.svcSubCode[0].checked){
			if(document.f.procTime.value == '0'){
				document.f.ttHour.focus();
			}else{
				document.f.procTime.focus();
			}
		}else{
			document.f.procTime.focus();
		}
	}
}

// 요양설정
function _setSvc1(){
	svcTitle.innerHTML = '소요시간';
	labelYoy.style.display = '';
	objSvcSubCD.style.display = 'none';

	document.f.togeUmu.disabled = false;

	document.f.visitSudangCheck.checked = false;
	document.f.visitSudangCheck.disabled = true;
	document.f.visitSudang.disabled = true;
	document.f.visitSudang.style.backgroundColor = '#eeeeee';
	document.f.sudangYul1.disabled = true;
	document.f.sudangYul1.style.backgroundColor = '#eeeeee';
	document.f.sudangYul2.disabled = true;
	document.f.sudangYul2.style.backgroundColor = '#eeeeee';

	txtCarNo.style.display = 'none';

	var select = null;

	select = document.getElementById("procTime");
	select.innerHTML = '';
	
	_setSelectBox(select, '30', '30분');
	_setSelectBox(select, '60', '60분');
	_setSelectBox(select, '90', '90분');
	_setSelectBox(select, '120', '120분');
	_setSelectBox(select, '150', '150분');
	_setSelectBox(select, '180', '180분');
	_setSelectBox(select, '210', '210분');
	_setSelectBox(select, '240', '240분');
	_setSelectBox(select, '0', '270분이상');
	_setNeedTime();
	_checkTimeH();
	_setIljungSuga();
}

// 목욕설정
function _setSvc2(){
	svcTitle.innerHTML = '차량여부';
	labelYoy.style.display = 'none';
	objSvcSubCD.style.display = '';

	document.f.togeUmu.disabled = true;

	document.f.visitSudangCheck.checked = true;
	document.f.visitSudangCheck.disabled = false;
	document.f.visitSudang.disabled = false;
	document.f.visitSudang.style.backgroundColor = '#ffffff';
	document.f.sudangYul1.disabled = false;
	document.f.sudangYul1.style.backgroundColor = '#ffffff';
	//document.f.sudangYul1.value = document.f.sudangYul1.tag;
	document.f.sudangYul2.disabled = false;
	document.f.sudangYul2.style.backgroundColor = '#ffffff';
	//document.f.sudangYul2.value = document.f.sudangYul2.tag;

	var bodyTop  = __getObjectTop(document.getElementById('carBocy')) - parseInt(document.getElementById('carBocy').offsetHeight) - 1;
	var bodyLeft = __getObjectLeft(document.getElementById('carBocy')) - 1;

	txtCarNo.style.top  = bodyTop;
	txtCarNo.style.left = bodyLeft;
	txtCarNo.style.display = '';

	var select = null;
	
	select = document.getElementById("procTime");
	select.innerHTML = '';

	_setSelectBox(select, 'K', '차량');
	_setSelectBox(select, 'F', '미차량');
	_checkTimeH();
	_setIljungSuga();

	var newToTime = getTimeValue(document.f.ftHour.value+document.f.ftMin.value, bathTimes);

	document.f.ttHour.readOnly = false;
	document.f.ttMin.readOnly  = false;
	document.f.ttHour.style.backgroundColor = '#ffffff';
	document.f.ttMin.style.backgroundColor  = '#ffffff';
	document.f.ttHour.value = newToTime[0];
	document.f.ttMin.value  = newToTime[1];
	document.f.ttHour.onfocus = function(){document.f.ttHour.select();}
	document.f.ttMin.onfocus  = function(){document.f.ttMin.select();}
}

function _setSvc2Sub(){
	var select = null;

	//document.f.ttHour.value = document.f.ftHour.value;
	//document.f.ttMin.value  = document.f.ftMin.value;

	select = document.getElementById("svcSubCD");

	if (document.f.procTime.value == 'K'){
		txtCarNo.style.display = '';

		if (select.options[0].text == '차량입욕'){
			return;
		}
	}

	if (document.f.procTime.value == 'F'){
		txtCarNo.style.display = 'none';
	}

	select.innerHTML = '';

	if (document.f.procTime.value == 'K'){
		_setSelectBox(select, '1', '차량입욕');
		_setSelectBox(select, '2', '가정내입욕');
	}else{
		_setSelectBox(select, '1', '가정내입욕');
		_setSelectBox(select, '2', '미입욕');
	}
	_setEndTimeSub();
}

// 간호설정
function _setSvc3(){
	svcTitle.innerHTML = '소요시간';
	labelYoy.style.display = '';
	objSvcSubCD.style.display = 'none';
	
	document.f.togeUmu.disabled = true;

	document.f.visitSudangCheck.checked = true;
	document.f.visitSudangCheck.disabled = false;
	document.f.visitSudang.disabled = false;
	document.f.visitSudang.style.backgroundColor = '#ffffff';
	document.f.sudangYul1.disabled = false;
	document.f.sudangYul1.style.backgroundColor = '#ffffff';
	//document.f.sudangYul1.value = "100.00";
	document.f.sudangYul2.disabled = false;
	document.f.sudangYul2.style.backgroundColor = '#ffffff';
	//document.f.sudangYul2.value = "0.00";

	txtCarNo.style.display = 'none';

	var select = null;
	
	select = document.getElementById("procTime");
	select.innerHTML = '';

	_setSelectBox(select, '29', '30분미만');
	_setSelectBox(select, '59', '30분');
	_setSelectBox(select, '89', '60분이상');

	_checkTimeH();
	_setIljungSuga();
}

function _setSelectBox(object, value, text){
	var option = null;

	option = document.createElement("option");
	option.value = value;
	option.text  = text;
	object.add(option);
}

function checkVisitSugang(checked){
	if (checked){
		document.f.visitSudang.disabled = false;
		document.f.visitSudang.style.backgroundColor = '#ffffff';
		document.f.sudangYul1.disabled = false;
		document.f.sudangYul1.style.backgroundColor = '#ffffff';
		document.f.sudangYul2.disabled = false;
		document.f.sudangYul2.style.backgroundColor = '#ffffff';
	}else{
		document.f.visitSudang.disabled = true;
		document.f.visitSudang.style.backgroundColor = '#eeeeee';
		document.f.sudangYul1.disabled = true;
		document.f.sudangYul1.style.backgroundColor = '#eeeeee';
		document.f.sudangYul2.disabled = true;
		document.f.sudangYul2.style.backgroundColor = '#eeeeee';
	}
}

// 소요시간 설정
function _setNeedTime(){
	var newValue = false;
	var yoyCount = _getYoySetCount();
	var msg_type = 1;

	if (yoyCount > 1){
		//newValue = true;
		msg_type = 1;
		if (document.f.procTime.options[document.f.procTime.selectedIndex].index > 2) newValue = true;
	}

	if (document.f.togeUmu.checked){
		//newValue = true;
		msg_type = 2;
		if (document.f.procTime.options[document.f.procTime.selectedIndex].index > 2) newValue = true;
	}

	/*
	for(var i=3; i<document.f.procTime.options.length; i++){
		document.f.procTime.options[i].disabled = newValue;
	}
	*/
	var selectedIndex = 0;
	for(var i=0; i<document.f.procTime.options.length; i++){
		if (document.f.procTime.options[i].selected){
			selectedIndex = i;
			break;
		}
	}

	var svcSubCode = _getSvcSubCode();

	if (svcSubCode == '200'){
		select = document.getElementById("procTime");
		select.innerHTML = '';

		if (newValue){
			_setSelectBox(select, '30', '30분');
			_setSelectBox(select, '60', '60분');
			_setSelectBox(select, '90', '90분');
		}else{
			_setSelectBox(select, '30', '30분');
			_setSelectBox(select, '60', '60분');
			_setSelectBox(select, '90', '90분');
			_setSelectBox(select, '120', '120분');
			_setSelectBox(select, '150', '150분');
			_setSelectBox(select, '180', '180분');
			_setSelectBox(select, '210', '210분');
			_setSelectBox(select, '240', '240분');
			_setSelectBox(select, '0', '270분이상');
		}
	}else if (svcSubCode == '500'){
		return;
	}else if (svcSubCode == '800'){
		return;
	}

	if (newValue){
		if (msg_type == 1){
			alert('요양보호사 2인이상 선택시 소요시간이 90분을 초과할 수 없습니다. 소요시간을 90분으로 설정합니다.');	
		}else{
			alert('동거가족 선택시 소요시간이 90분을 초과할 수 없습니다. 소요시간을 90분으로 설정합니다.');
		}
		
		document.f.procTime.options[2].selected = true;
		_setEndTime();
	}else{
		document.f.procTime.options[selectedIndex].selected = true;
	}
}

// 요양보호사 선택
function _helpSuYoy(mCode, mKind, mKey){
	var svcSubCode = _getSvcSubCode();
	
	var date     = document.f.addDate.value != '' ? document.f.addDate.value : document.f.calYear.value + document.f.calMonth.value;
	var fromTime = document.f.ftHour.value + document.f.ftMin.value; //일정시작시간
	var toTime   = document.f.ttHour.value + document.f.ttMin.value; //일정종료시간

	var help = showModalDialog('../inc/_help.php?r_gubun=suYoyFind&mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mDate='+date+'&mFromTime='+fromTime+'&mToTime='+toTime, window, 'dialogWidth:200px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	var object1, object2, object3;
	var value;
	var index = 1;

	if (help == undefined){
		return;
	}

	for(var i=0; i<5; i++){
		object1 = eval('document.f.yoy'+(i+1));
		object2 = eval('document.f.yoyNm'+(i+1));
		object1.value = '';
		object2.value = '';
	}

	for(var i=0; i<help.length; i++){
		if (help[i] != undefined){
			object1 = eval('document.f.yoy'+index);
			object2 = eval('document.f.yoyNm'+index);
			object3 = eval('document.f.yoyTA'+index);
			
			value = help[i].split('//');

			//alert(value[2]);

			object1.value = value[0];
			object2.value = value[1];
			object3.value = value[2];

			index++;
		}
	}
	
	_setWeekDay(mCode, mKind, document.f.yoy1.value);
	_setNeedTime();
	_setIljungSuga();
}

function _helpSuYoyP(mCode, mKind, mKey, yoyCode, yoyName, yoyTA){
	var yoy1 = document.f.yoy1.value;
	var yoy2 = document.f.yoy2.value;
	var yoy3 = document.f.yoy3.value;
	var yoy4 = document.f.yoy4.value;
	var yoy5 = document.f.yoy5.value;
	var yoy = '';

	if (yoy1 != '') yoy += ",'"+yoy1+"'";
	if (yoy2 != '') yoy += ",'"+yoy2+"'";
	if (yoy3 != '') yoy += ",'"+yoy3+"'";
	if (yoy4 != '') yoy += ",'"+yoy4+"'";
	if (yoy5 != '') yoy += ",'"+yoy5+"'";

	if (yoy != ''){
		yoy = yoy.substring(1, yoy.length);
	}

	var date     = document.f.addDate.value != '' ? document.f.addDate.value : document.f.calYear.value + document.f.calMonth.value;
	var fromTime = document.f.ftHour.value + document.f.ftMin.value; //일정시작시간
	var toTime   = document.f.ttHour.value + document.f.ttMin.value; //일정종료시간
	var help = showModalDialog('../inc/_help.php?r_gubun=yoyFind&mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&yoy='+yoy+'&mDate='+date+'&mFromTime='+fromTime+'&mToTime='+toTime, window, 'dialogWidth:200px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	
	if (help == undefined){
		return;
	}

	var index = '';

	if (yoyCode.value == ''){
		if (yoy5 == '') index = '5';
		if (yoy4 == '') index = '4';
		if (yoy3 == '') index = '3';
		if (yoy2 == '') index = '2';
		if (yoy1 == '') index = '1';
	}

	if (index == ''){
		yoyCode.value = help[1];
		yoyName.value = help[2];
		yoyTA.value   = help[3];
	}else{
		eval('document.f.yoy'+index).value   = help[1];
		eval('document.f.yoyNm'+index).value = help[2];
		eval('document.f.yoyTA'+index).value = help[3];
	}
	
	_setWeekDay(mCode, mKind, document.f.yoy1.value);
	_setNeedTime();
	_setIljungSuga();
}

//요양사별 요일 설정
function _setWeekDay(mCode, mKind, mYoy){
	if (document.f.mMode.value == 'IN'){
	}else if (document.f.mMode.value == 'PATTERN'){
	}else{
		return;
	}

	var request = getHttpRequest('../inc/_check.php?gubun=checkYoyWeekDay&mCode='+mCode+'&mKind='+mKind+'&mYoy='+mYoy);
	var weekvalue = request.split('//');

	document.f.weekDay1.checked = (weekvalue[0] == 'Y' ? document.f.weekDay1.checked : false);
	document.f.weekDay2.checked = (weekvalue[1] == 'Y' ? document.f.weekDay2.checked : false);
	document.f.weekDay3.checked = (weekvalue[2] == 'Y' ? document.f.weekDay3.checked : false);
	document.f.weekDay4.checked = (weekvalue[3] == 'Y' ? document.f.weekDay4.checked : false);
	document.f.weekDay5.checked = (weekvalue[4] == 'Y' ? document.f.weekDay5.checked : false);
	document.f.weekDay6.checked = (weekvalue[5] == 'Y' ? document.f.weekDay6.checked : false);
	document.f.weekDay0.checked = (weekvalue[6] == 'Y' ? document.f.weekDay0.checked : false);

	document.f.weekDay1.disabled = (weekvalue[0] == 'Y' ? false : true);
	document.f.weekDay2.disabled = (weekvalue[1] == 'Y' ? false : true);
	document.f.weekDay3.disabled = (weekvalue[2] == 'Y' ? false : true);
	document.f.weekDay4.disabled = (weekvalue[3] == 'Y' ? false : true);
	document.f.weekDay5.disabled = (weekvalue[4] == 'Y' ? false : true);
	document.f.weekDay6.disabled = (weekvalue[5] == 'Y' ? false : true);
	document.f.weekDay0.disabled = (weekvalue[6] == 'Y' ? false : true);
}

//요양사 선택해제
function _yoyNot(index){
	eval('document.f.yoy'+index).value = '';
	eval('document.f.yoyNm'+index).value = '';
	eval('document.f.yoyTA'+index).value = '';

	for(var i=(parseInt(index)+1); i<=5; i++){
		eval('document.f.yoy'+(i-1)).value = eval('document.f.yoy'+i).value;
		eval('document.f.yoyNm'+(i-1)).value = eval('document.f.yoyNm'+i).value;
		eval('document.f.yoyTA'+(i-1)).value = eval('document.f.yoyTA'+i).value;

		eval('document.f.yoy'+i).value = '';
		eval('document.f.yoyNm'+i).value = '';
		eval('document.f.yoyTA'+i).value = '';
	}

	_setWeekDay(document.f.mCode.value, document.f.mKind.value, document.f.yoy1.value);
	_setNeedTime();
	_setIljungSuga();
}

function _getYoySetCount(){
	var yoyCount = 0;

	if (document.f.yoy1.value != '') yoyCount++;
	if (document.f.yoy2.value != '') yoyCount++;
	if (document.f.yoy3.value != '') yoyCount++;
	if (document.f.yoy4.value != '') yoyCount++;
	if (document.f.yoy5.value != '') yoyCount++;

	return yoyCount;
}

// 방문시간 확인
function _checkTimeH(){
	var value = document.f.ftHour.value;

	if (value.substring(0,1) == '0'){
		value = value.substring(1,value.length);
	}

	if (value == ''){
		value = '0';
	}

	if (parseInt(value) < 10){
		value = '0' + parseInt(value);
	}

	if (parseInt(value) >= 24){
		value = '00';
	}

	document.f.ftHour.value = value;

	_checkTimeM();

//	_setNeedTime();
//	_setEndTime();
}

function _checkTimeM(){
	var value = document.f.ftMin.value;

	if (value == ''){
		value = '0';
	}

	/*
	if (parseInt(value) < 30){
		value = '00';
	}else{
		value = '30';
	}
	*/

	value = (value < 10 ? '0' : '') + value;
	value = value.substring(value.length - 2, value.length);

	if (parseInt(value) >= 60){
		value = '59';
	}

	document.f.ftMin.value = value;
	
	_setNeedTime();
	_setEndTime();
}

function getTimeValue(time, addTime){
	var hour = time.substring(0,2);
	var min  = time.substring(2,4);

	if (hour.substring(0,1) == '0') hour = hour.substring(1,hour.length);
	if (min.substring(0,1)  == '0') min  = min.substring(1,min.length);
	
	if (hour == '') hour = '0';
	if (min  == '') min  = '0';

	//if (parseInt(hour) < 10) hour = '0' + parseInt(hour);
	//if (parseInt(min)  < 10) min  = '0' + parseInt(min);

	var newTime = parseInt(hour) * 60 + parseInt(min) + addTime;

	hour = Math.floor(newTime / 60);
	min  = newTime % 60;

	hour = (parseInt(hour) < 10 ? '0' : '') + hour;
	min  = (parseInt(min)  < 10 ? '0' : '') + min;
	
	if (parseInt(hour) >= 24){
		hour = '00';
	}

	return new Array(hour, min);
}

function _setEndTime(){
	var svcSubCode = _getSvcSubCode();

	if (svcSubCode == '500'){
		_setSvc2Sub();
		//var newToTime = getTimeValue(document.f.ftHour.value+document.f.ftMin.value, 89);
		var newToTime = getTimeValue(document.f.ftHour.value+document.f.ftMin.value, bathTimes);  //목욕을 89분에서 30분으로 변경
		//document.f.ttHour.value = document.f.ftHour.value;
		//document.f.ttMin.value  = document.f.ftMin.value;

		document.f.ttHour.value = newToTime[0];
		document.f.ttMin.value  = newToTime[1];
		
		//return;
	}
	_setEndTimeSub();
}

// 종료시간 확인
function _setEndTimeCheck(){
	if (document.f.procTime.value != '0'){
		return true;
	}

	var fHTime = document.f.ftHour.value;
	var tHTime = document.f.ttHour.value;

	if (fHTime.substring(0,1) == '0') fHTime = fHTime.substring(1,fHTime.length);
	if (tHTime.substring(0,1) == '0') tHTime = tHTime.substring(1,tHTime.length);

	if (parseInt(fHTime) > parseInt(tHTime)){
		tHTime = parseInt(tHTime) + 24;
	}

	var checkTime = parseInt(fHTime) * 60 + parseInt(document.f.ftMin.value) + 300;
	var toTime = parseInt(tHTime) * 60 + parseInt(document.f.ttMin.value);

	if (checkTime > toTime){
		alert('종료시간을 시작시간보다 300분 이상크게 입력하여 주십시오.');
		document.f.ttHour.focus();
		return false;
	}
	return true;
}

function _setEndTimeM(){
	var fHTime = document.f.ftHour.value;
	var fMTime = document.f.ftMin.value;
	var tHTime = document.f.ttHour.value;
	var tMTime = document.f.ttMin.value;
	var svcSubCode = _getSvcSubCode();

	if (fHTime.substring(0,1) == '0') fHTime = fHTime.substring(1,fHTime.length);
	if (fMTime.substring(0,1) == '0') fMTime = fMTime.substring(1,fMTime.length);

	if (tHTime.substring(0,1) == '0') tHTime = tHTime.substring(1,tHTime.length);
	if (tMTime.substring(0,1) == '0') tMTime = tMTime.substring(1,tMTime.length);

	if (parseInt(fHTime) > parseInt(tHTime)){
		tHTime = parseInt(tHTime) + 24;
	}

	var fTime = parseInt(fHTime) * 60 + parseInt(fMTime);
	var tTime = parseInt(tHTime) * 60 + parseInt(tMTime);

	if (document.f.procTime.value == '0' && svcSubCode == '200'){
		if (fTime + 300 > tTime){
			tTime = fTime + 300;
		}
	}
	
	tHTime = tTime / 60;
	tHTime = Math.floor(tHTime);

	if (tHTime >= 24){
		if (document.f.procTime.value == '0' && svcSubCode == '200'){
			tHTime = tHTime - 24;
			tHTime = (parseInt(tHTime) < 10 ? '0' : '')+tHTime;
		}else{
			tHTime = '00';
		}
	}else{
		tHTime = (tHTime < 10 ? '0' : '') + tHTime;
	}

	tMTime = tTime % 60;
	tMTime = Math.floor(tMTime);
	tMTime = (tMTime < 10 ? '0' : '') + tMTime;

	document.f.ttHour.value = tHTime;
	document.f.ttMin.value  = tMTime;

	_setIljungSuga();
}

function _setEndTimeSub(){
	var svcSubCode = _getSvcSubCode();

	if (svcSubCode != '500'){
		if (document.f.ftHour.value == '' || document.f.ftMin.value == ''){
			document.f.ttHour.value = '';
			document.f.ttMin.value = '';
			return;
		}
		
		var fHTime = document.f.ftHour.value;

		if (fHTime.substring(0,1) == '0'){
			fHTime = fHTime.substring(1,fHTime.length);
		}

		fHTime = parseInt(fHTime) * 60;

		var fMTime = parseInt(document.f.ftMin.value);
		var pTime  = parseInt(document.f.procTime.value);

		if (svcSubCode == '200' && parseInt(pTime) == 0){
			var tempFmH = document.f.ftHour.value;
			var tempFmM = document.f.ftMin.value;
			var tempToH = document.f.ttHour.value;
			var tempToM = document.f.ttMin.value;

			if (isNaN(tempToH) || tempToH == '') tempToH = '00';
			if (isNaN(tempToM) || tempToM == '') tempToM = '00';
			
			if (tempFmH.substring(0,1) == '0'){
				tempFmH = tempFmH.substring(1,tempFmH.length);
			}

			if (tempToH.substring(0,1) == '0'){
				tempToH = tempToH.substring(1,tempToH.length);
			}

			var tempFH = parseInt(tempFmH) * 60 + parseInt(tempFmM);
			var tempTH = parseInt(tempToH) * 60 + parseInt(tempToM);

			var tTime = parseInt(tempTH) - parseInt(tempFH);
			
			if (tTime < 300){
				tTime = 300;
			}

			tTime += parseInt(tempFH)
			
			document.f.ttHour.readOnly = false;
			document.f.ttMin.readOnly  = false;
			document.f.ttHour.style.backgroundColor = '#ffffff';
			document.f.ttMin.style.backgroundColor  = '#ffffff';
			document.f.ttHour.onfocus = function(){document.f.ttHour.select();}
			document.f.ttMin.onfocus  = function(){document.f.ttMin.select();}
		}else{
			var tTime = parseInt(fHTime) + parseInt(fMTime) + parseInt(pTime);

			document.f.ttHour.readOnly = true;
			document.f.ttMin.readOnly  = true;
			document.f.ttHour.style.backgroundColor = '#eeeeee';
			document.f.ttMin.style.backgroundColor  = '#eeeeee';
			document.f.ttHour.onfocus = function(){document.f.procTime.focus();}
			document.f.ttMin.onfocus  = function(){document.f.procTime.focus();}
		}

		var tHTime = parseInt(tTime / 60);
		var tMTime = tTime % 60;

		if (tHTime > 24){
			tHTime = tHTime - 24;
		}

		if (tHTime < 10){	
			tHTime = '0' + tHTime;
		}

		if (tHTime == 24){
			tHTime = '00';
		}
		
		if (svcSubCode != '800'){
			/*
			if (tMTime < 30){
				tMTime = '00';
			}else{
				tMTime = '30';
			}
			*/
			tMTime = (tMTime < 10 ? '0' : '') + tMTime;
		}
		
		document.f.ttHour.value = tHTime;
		document.f.ttMin.value = tMTime;
	}
	_setIljungSuga();
}

// 적용수가 계산
function _setIljungSuga(){
	// 시작시간 입력여부 확인
	if (document.f.ftHour.value == ''){
		//alert('방문시간을 먼저 입력하여 주십시오.');
		document.f.ftHour.focus();
		return;
	}

	if (document.f.ftMin.value == ''){
		//alert('방문시간을 먼저 입력하여 주십시오.');
		document.f.ftMin.focus();
		return;
	}

	// 요양보호사 선택 여부 확인
	var yoyCount = _getYoySetCount();
	
	if (yoyCount == 0){	
		//alert('선택된 요양보호사가 없습니다. 요양보호사를 선택하여 주십시오.');
		return;
	}

	// 입력시간
	var FT = document.f.ftHour.value;

	if (FT.substring(0,1) == '0'){
		FT = FT.substring(1,FT.length);
	}

	FT = parseInt(FT) * 60;
	FT = FT + parseInt(document.f.ftMin.value);

	var TT = document.f.ttHour.value;

	if (TT.substring(0,1) == '0'){
		TT = TT.substring(1,TT.length);
	}

	TT = parseInt(TT) * 60;
	TT = TT + parseInt(document.f.ttMin.value);

	if (TT < FT){
		TT = TT + 24 * 60;
	}

	TT = TT - 1;

	// 오늘의 일자와 요일, 휴일 확인
	var today = null;

	if (document.f.addDate.value.length == 8){
		today = document.f.addDate.value;
		
		var year = today.substring(0,4);
		var month = today.substring(4,6);
		var day = today.substring(6,8);

		if (month.substring(0,1) == '0') month = month.substring(1,2);
		if (day.substring(0,1) == '0') day = day.substring(1,2);

		var now = new Date(parseInt(year), parseInt(month)-1, parseInt(day));

		today = today.substring(0,4)+'-'+today.substring(4,6)+'-'+today.substring(6,8);
	}else{
		var now = new Date();
		var year = now.getFullYear();
		var month = now.getMonth()+1;
		var day = now.getDate();

		if (month < 10){
			month = '0'+month;
		}

		if (day < 10){
			day = '0'+day;
		}

		today = year+'-'+month+'-'+day;
	}

	var weekday = now.getDay();
	var Hgubun = 'N';

	/*
	if (weekday == "6" || weekday == "0"){
		Hgubun = 'Y';	
	}
	*/
	//토요일을 휴일에서 제외한다.
	if (weekday == "0"){
		Hgubun = 'Y';	
	}

	var holiday = getHttpRequest('../inc/_check.php?gubun=checkHoliday&mDate='+today);

	if (holiday == 'Y'){
		Hgubun = 'Y';
	}

	// 구분 시간값등을 확인
	var TN  = document.f.procTime.value;
	var ETN = 0;
	var NTN = 0;
	var ETNtime = 0; //야간시간
	var NTNtime = 0; //심야시간
	var ERang1 = 18 * 60;
	var ERang2 = 21 * 60 + 59;
	var NRang1 = 22 * 60;
	var NRang2 = 24 * 60 + 3 * 60 + 59;
	var NRang3 = 3 * 60 + 59;
	var togeUmu = 'N'; //동거여부
	var Egubun  = 'N'; //야간여부
	var Ngubun  = 'N'; //심야여부

	var EAMT  = 0;
	var NAMT  = 0;
	var TAMT  = 0;
	var EFrom = 0;
	var ETo   = 0;
	var NFrom = 0;
	var NTo   = 0;

	// 동거여부
	if (document.f.togeUmu.checked){
		togeUmu = 'Y';
	}

	var svcSubCode = _getSvcSubCode();
	
	EFrom = FT - ERang1;
	ETo   = TT - ERang1 + 1;

	if (svcSubCode == '200'){
		// 요양 중 동거가 아닐경우만 야간및 심야 할증을 실행한다.
		if (togeUmu != 'Y'){
			if (FT < NRang3){
				NFrom   = NRang3 - FT;
				NTo     = NRang3 - TT;
				NTNtime = NFrom - (NTo < 0 ? 0 : NTo) + 1;
			}else{
				NFrom   = FT - NRang1;
				NTo     = TT - NRang1 + 1;
				NTNtime = NTo - (NFrom < 0 ? 0 : NFrom);
			}
			
			ETNtime = ETo - (EFrom < 0 ? 0 : EFrom);
			
			NTNtime = NTNtime < 0 ? 0 : NTNtime;
			ETNtime = ETNtime < 0 ? 0 : ETNtime - NTNtime;
		}else{
			NTNtime = 0;
			ETNtime = 0;
		}
	}else{
		// 목욕 및 간호는 할증을 실행하자 않는다.
		NTNtime = 0;
		ETNtime = 0;
	}

	if (svcSubCode == '200'){
		switch(TN){
			case '30' : TN = 1; break;
			case '60' : TN = 2; break;
			case '90' : TN = 3; break;
			case '120': TN = 4; break;
			case '150': TN = 5; break;
			case '180': TN = 6; break;
			case '210': TN = 7; break;
			case '240': TN = 8; break;
			case '0'  : TN = 9; break;
		}
	}else if (svcSubCode == '800'){
		switch(TN){
			case '29': TN = 1; break;
			case '59': TN = 2; break;
			case '89': TN = 3; break;
		}
	}

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

	var sugaKey = '';
	var sugaGubun = '';

	if (svcSubCode == '200'){
		// 요양
		if (togeUmu != 'Y'){
			if (yoyCount == 1 && Hgubun == 'N'){
				sugaGubun = 'CCWS';
			}else if (yoyCount > 1 && Hgubun == 'N'){
				sugaGubun = 'CCWD';
			}else if (yoyCount == 1 && Hgubun == 'Y'){
				sugaGubun = 'CCHS';
			}else if (yoyCount > 1 && Hgubun == 'Y'){
				sugaGubun = 'CCHD';
			}
		}else{
			sugaGubun = 'CCWC';
		}
	}else if (svcSubCode == '500'){
		// 목욕
		sugaGubun = 'CB';
	}else{
		// 간호
		if (Hgubun != 'Y'){
			sugaGubun = 'CNW';
		}else{
			sugaGubun = 'CNH';
		}

		// 간호에서는 요양사 수는 상관없다.
		/*
		if (yoyCount > 1){
			sugaGubun += 'D';
		}else{
			sugaGubun += 'S';
		}
		*/
		sugaGubun += 'S';
	}

	sugaKey = sugaGubun+TN;

	if (svcSubCode == '500'){
		sugaKey += 'D'+document.f.svcSubCD.value;
	}

	//var help = showModalDialog('../inc/_help.php?r_gubun=sugaFind', window, 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	var sugaName    = getHttpRequest('../inc/_check.php?gubun=getSugaName&mCode='+document.f.mCode.value+'&mSuga='+sugaKey); //명칭
	var sugaPrice   = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga='+sugaKey)); //단가
	var sudangPrice = parseInt(getHttpRequest('../inc/_check.php?gubun=getSudangPrice&mCode='+document.f.mCode.value+'&mSuga='+sugaKey)); //수당

//	if (!isNaN(sugaPrice)){
//		sugaPrice = 0;
//	}

	if (sugaPrice == ''){
		sugaPrice = 0;
	}

	sugaCont.innerHTML = sugaName;

	if (TN == 9){
		// 270분 이상일 경우 수가를 계산
		var tempFmH = document.f.ftHour.value;
		var tempFmM = document.f.ftMin.value;
		var tempToH = document.f.ttHour.value;
		var tempToM = document.f.ttMin.value;

		if (tempFmH.substring(0,1) == '0') tempFmH = tempFmH.substring(1,tempFmH.length);
		if (tempFmM.substring(0,1) == '0') tempFmM = tempFmM.substring(1,tempFmM.length);
		if (tempToH.substring(0,1) == '0') tempToH = tempToH.substring(1,tempToH.length);
		if (tempToM.substring(0,1) == '0') tempToM = tempToM.substring(1,tempToM.length);

		if (parseInt(tempFmH) > parseInt(tempToH)){
			tempToH = parseInt(tempToH) + 24;
		}
		tempFmH = parseInt(tempFmH) * 60 + parseInt(tempFmM);
		tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - 30 - parseInt(tempFmH);

		var tempL = cut(tempToH, 30) / 30;
		var tempK = 0;
		
		sugaPrice = 0;

		while(1){
			if (tempL >= 8){
				tempK = 8;
			}else if (tempL == 0 || tempK == 0){
				break;
			}else{
				tempK = tempL % 8;
			}
			tempL = tempL - tempK;

			var tempValue = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga='+sugaGubun+tempK)); 

			sugaPrice += tempValue; //parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga=CCWS'+tempK)); //단가
		}
	}

	if (ETNtime > 0){
		if (sugaGubun != 'HS' && sugaGubun != 'HD'){
			EAMT = (sugaPrice * (ETN / TN)) * 0.2;
			EAMT = EAMT - (EAMT % 10);
		}
		Egubun = 'Y';
	}

	if (NTNtime > 0){
		if (sugaGubun != 'HS' && sugaGubun != 'HD'){
			NAMT = (sugaPrice * (NTN / TN)) * 0.3;
			NAMT = NAMT - (NAMT % 10);
		}
		Ngubun = 'Y';
	}
	
	TAMT = parseInt(sugaPrice) + parseInt(EAMT) + parseInt(NAMT);

	document.f.visitSudang.value = __commaSet(sudangPrice);
	document.f.sPrice.value = __commaSet(sugaPrice);
	document.f.ePrice.value = __commaSet(EAMT);
	document.f.nPrice.value = __commaSet(NAMT);
	document.f.tPrice.value = __commaSet(TAMT);
	document.f.sugaCode.value = sugaKey;
	document.f.sugaName.value = sugaName;
	document.f.Egubun.value = Egubun;
	document.f.Ngubun.value = Ngubun;
	document.f.Etime.value = ETNtime;
	document.f.Ntime.value = NTNtime;
}

// 일정배정
function _setIljungAss(){
	pattern.style.display = "";
}

// 배정
function _setAss(){
	if (!_setEndTimeCheck()){
		return;
	}

	var now = new Date();
	var nowYear = now.getFullYear();
	var nowMonth = now.getMonth()+1;
	var nowDay = now.getDate();
	var nowHour = now.getHours();
	var nowMin = now.getMinutes();

	nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	nowDay = (nowDay < 10 ? '0' : '') + nowDay;
	nowHour = (nowHour < 10 ? '0' : '') + nowHour;
	nowMin = (nowMin < 10 ? '0' : '') + nowMin;

	var nowDate = nowYear + '-' + nowMonth + '-' + nowDay;
	var nowTime = nowHour + ':' + nowMin;

	nowDate = __replace(nowDate, '-', '');
	nowTime = __replace(nowTime, ':', '');

	var addTime = document.f.ftHour.value + document.f.ftMin.value;

	// 시작시간 입력여부 확인
	if (document.f.ftHour.value == ''){
		alert('방문시간을 입력하여 주십시오.');
		document.f.ftHour.focus();
		return;
	}

	if (document.f.ftMin.value == ''){
		alert('방문시간을 입력하여 주십시오.');
		document.f.ftMin.focus();
		return;
	}

	try{
		if (opener.document.f.modifyFlag.value == 'DAY' ||
			opener.document.f.modifyFlag.value == ''){
			var historyModify = 'Y';
		}else{
			var historyModify = 'N';
		}
	}catch(e){
		var historyModify = 'N';
	}

	if (historyModify != 'Y'){
		if (document.f.addDate.value != ''){
			// 계약시작월은 과거의 데이타입력을 허용한다.
			if (document.f.contDate.value != nowDate.substring(0, 6)){
				if (nowDate > document.f.addDate.value){
					alert('과거의 일정은 입력할 수 없습니다. 확인하여 주십시오.');
					return;
				}

				if (nowDate == document.f.addDate.value && nowTime > addTime){
					alert('과거의 일정은 입력할 수 없습니다. 확인하여 주십시오.');
					return;
				}
			}
		}
	}

	// 요양보호사 선택 여부 확인
	var yoyCount = 0;

	if (document.f.yoy1.value != '') yoyCount++;
	if (document.f.yoy2.value != '') yoyCount++;
	if (document.f.yoy3.value != '') yoyCount++;
	if (document.f.yoy4.value != '') yoyCount++;
	if (document.f.yoy5.value != '') yoyCount++;

	if (yoyCount == 0){	
		alert('선택된 요양보호사가 없습니다. 요양보호사를 선택하여 주십시오.');
		return;
	}

	// 요일 선택 여부 확인
	var weekCount = 0;

	if (document.f.weekDay1.checked) weekCount++;
	if (document.f.weekDay2.checked) weekCount++;
	if (document.f.weekDay3.checked) weekCount++;
	if (document.f.weekDay4.checked) weekCount++;
	if (document.f.weekDay5.checked) weekCount++;
	if (document.f.weekDay6.checked) weekCount++;
	if (document.f.weekDay0.checked) weekCount++;

	if (weekCount == 0){
		alert('제공요일을 하나 이상 선택하여 주십시오.');
		return;
	}

	// 수가정보가 올바르지 않을 경우 탈출한다.
	if (document.f.sPrice.value == '' ||
		document.f.sPrice.value == '0' ||
		isNaN(__commaUnset(document.f.sPrice.value))){
		alert('기준수가 오류입니다. 다시 입력하여 주십시오.');
		return;
	}

	var oldFmTime = null, oldToTiem = null;

	if (document.f.mMode.value == 'IN'){
		_setPattern(); //패턴등록
		_inCalendar();
	}else if (document.f.mMode.value == 'ADD'){
		_addCalendar();
	}else{
		oldFmTime = opener.document.getElementById('mFmTime_'+document.f.addDay.value+'_'+document.f.addIndex.value).value;
		oldToTiem = opener.document.getElementById('mToTime_'+document.f.addDay.value+'_'+document.f.addIndex.value).value;

		_modifyCalendar();
	}

	if (document.f.mMode.value != 'IN'){
		var mCode = opener.document.f.mCode.value;
		var mDate = document.f.addDate.value;
		var mDay  = document.f.addDay.value;
		var mIndex  = document.f.addIndex.value;
		var mFmTime = document.f.ftHour.value + document.f.ftMin.value;
		var mToTime = document.f.ttHour.value + document.f.ttMin.value;
		var mYoy     = '';
		var mRequest = 'N';
		var mYoy = new Array(document.f.yoy1.value, document.f.yoy2.value, document.f.yoy3.value, document.f.yoy4.value, document.f.yoy5.value);
		var mYoyDT = '';

		opener.document.getElementById('mSugupja_'+mDay+'_'+mIndex).value = 'N';

		mYoyDT = '';
		if (document.f.mMode.value == 'MODIFY'){
			// 수정시 자신의 일자, 시간을 제외한 일정만 검사한다.
			/*
			mYoyDT += opener.document.getElementById('mDate_'+mDay+'_'+mIndex).value;
			mYoyDT += opener.document.getElementById('mFmTime_'+mDay+'_'+mIndex).value;
			mYoyDT += opener.document.getElementById('mToTime_'+mDay+'_'+mIndex).value;
			*/
			mYoyDT = document.f.oldDate.value;
		}

		for(var i=0; i<5; i++){
			if (mYoy[i] != ''){
				mRequest = getHttpRequest('../inc/_check.php?gubun=checkDuplicate&mCode='+mCode+'&mDate='+mDate+'&mYoy='+mYoy[i]+'&mFmTime='+mFmTime+'&mToTime='+mToTime+'&mYoyDT='+mYoyDT);
				
				if (opener.document.getElementById('mSugupja_'+mDay+'_'+mIndex).value == 'N'){
					opener.document.getElementById('mSugupja_'+mDay+'_'+mIndex).value = mRequest;
				}
			}
		}
		_checkDuplicate();
	}
}

function _getSvcSubCode(){
	var svcSubCode = '';
	var svcCount = document.f.svcSubCode.length;

	for(var i=0; i<svcCount; i++){
		if (document.f.svcSubCode[i].checked){
			svcSubCode = document.f.svcSubCode[i].value;
			break;
		}
	}

	return svcSubCode;
}

function _setSubject(pDay, pIndex, pOpener){
	var tempSubject = '';
	var tempObject = null;
	var newIndex = 1;

	while(1){
		tempObject = eval(pOpener+'document.f.mUse_'+pDay+'_'+newIndex);

		if (tempObject == undefined){
			//if (eval(pOpener+'document.f.mUse_'+pDay+'_'+(newIndex+1)) == undefined){
			break;
			//}
		}

		if (tempObject.value == 'Y'){
			tempSubject += eval(pOpener+'document.f.mSubject_'+pDay+'_'+newIndex).value;
		}

		newIndex++;
	}

	return tempSubject;
}

function _modifyDiary(pDay, pIndex){
	var mCode = document.f.mCode.value;
	var mKind = document.f.mKind.value;
	var mKey = document.f.mKey.value;
	var mDate = eval('document.f.mDate_'+pDay+'_'+pIndex).value;
	var mWeek = eval('document.f.mWeekDay_'+pDay+'_'+pIndex).value;

	if (mWeek == '7'){
		mWeek = '0';
	}

	var modal = showModalDialog('su_add_iljung.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mDay='+pDay+'&mIndex='+pIndex+'&mDate='+mDate+'&mWeek='+mWeek+'&mMode=MODIFY', window, 'dialogWidth:900px; dialogHeight:195px; dialogHide:yes; scroll:yes; status:no');
	//window.open('su_add_iljung.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mDay='+pDay+'&mIndex='+pIndex+'&mDate='+mDate+'&mWeek='+mWeek+'&mMode=MODIFY');
}

function _clearDiary(pDay, pIndex){
	// 일정취소시 선택여부를 막는다.
	//if (!confirm('선택하신 일자의 일정을 취소하시겠습니까?')){
	//	return;
	//}

	/*
	var mCode = document.f.mCode.value;
	var mKind = document.f.mKind.value;
	var mJumin = document.f.mJuminNo.value;
	var mDate = eval('document.f.mDate_'+pDay+'_'+pIndex).value; 
	var mFmTime = eval('document.f.mFmTime_'+pDay+'_'+pIndex).value;
	var mSeq = eval('document.f.mSeq_'+pDay+'_'+pIndex).value;

	var request = getHttpRequest('su_iljung_delete_ok.php?mCode='+mCode+'&mKind='+mKind+'&mJumin='+mJumin+'&mDate='+mDate+'&mFmTime='+mFmTime+'&mSeq='+mSeq);

	if (request != 'Y'){	
		alert('일정 삭제 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		return;
	}
	*/

	var mSeq = eval('document.f.mSeq_'+pDay+'_'+pIndex).value;

	if (mSeq == '0'){
		eval('document.f.mUse_'+pDay+'_'+pIndex).value = 'N';
	}

	eval('document.f.mDelete_'+pDay+'_'+pIndex).value = 'Y';
	
	document.getElementById('txtSubject_'+pDay+'_'+pIndex).innerHTML = '';
	document.getElementById('txtSubject_'+pDay+'_'+pIndex).style.display = 'none';
}

function _addDiary(pCode, pKind, pKey, pDay, pDate, pWeek){
	var Index = 1;
	var tempObject = null;

	while(1){
		tempObject = eval('document.f.mUse_'+pDay+'_'+Index);

		if (tempObject == undefined){
			break;
		}
		//if (tempObject.value == 'N' && parseInt(Index) == 1){
		if (tempObject.value == 'N'){
			break;
		}

		Index++;
	}

	var mode = 'ADD';
	var modal = showModalDialog('su_add_iljung.php?mCode='+pCode+'&mKind='+pKind+'&mKey='+pKey+'&mDay='+pDay+'&mIndex='+Index+'&mDate='+pDate+'&mWeek='+pWeek+'&mMode='+mode, window, 'dialogWidth:900px; dialogHeight:195px; dialogHide:yes; scroll:yes; status:no');
}

function _setCenterInfo(pCode, pKind, pKey, year, month){
	center_info.innerHTML = getHttpRequest('su_center_info.php?mCode='+pCode+'&mKind='+pKind+'&mKey='+pKey+'&year='+year+'&month='+month);
}

/*
function _setCalendar(){
	var calYear = null;
	var calMonth = null;
	
	try{
		calYear = document.f.calYear.value;
		calMonth = document.f.calMonth.value;
	}catch(e){
		var now = new Date();

		calYear = now.getFullYear();
		calMonth = now.getMonth()+1;
	}

	var URL = 'su_calendar.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				calYear:calYear,
				calMonth:calMonth,
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mKey:document.f.mKey.value,
				mJuminNo:document.f.mJuminNo.value
			},
			onSuccess:function (responseHttpObj) {
				calendar.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}
*/

function _inCalendar(){
	var calYear = null;
	var calMonth = null;
	var svcSubCode = null;
	
	try{
		calYear = document.f.calYear.value;
		calMonth = document.f.calMonth.value;

		if (calMonth.substring(0,1) == '0'){
			calMonth = calMonth.substring(1,2);
		}
	}catch(e){
		var now = new Date();

		calYear = now.getFullYear();
		calMonth = now.getMonth()+1;
	}

	svcSubCode = _getSvcSubCode();
	
	//calendar.innerHTML = getHttpRequest('su_calendar.php?calYear='+calYear+'&calMonth='+calMonth);

	var URL = 'su_calendar.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				calYear:calYear,
				calMonth:calMonth,
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mKey:document.f.mKey.value,
				mJuminNo:document.f.mJuminNo.value,
				svcSubCode:svcSubCode,
				svcSubCD:document.f.svcSubCD.value,
				fmTime:document.f.ftHour.value+document.f.ftMin.value,
				ttTime:document.f.ttHour.value+document.f.ttMin.value,
				procTime:document.f.procTime.value,
				togeUmu:document.f.togeUmu.checked ? document.f.togeUmu.value : '',
				bipayUmu:document.f.bipayUmu.checked ? document.f.bipayUmu.value : '',
				timeDoub:document.f.timeDoub.checked ? document.f.timeDoub.value : '',
				weekDay1:document.f.weekDay1.checked ? document.f.weekDay1.value : '',
				weekDay2:document.f.weekDay2.checked ? document.f.weekDay2.value : '',
				weekDay3:document.f.weekDay3.checked ? document.f.weekDay3.value : '',
				weekDay4:document.f.weekDay4.checked ? document.f.weekDay4.value : '',
				weekDay5:document.f.weekDay5.checked ? document.f.weekDay5.value : '',
				weekDay6:document.f.weekDay6.checked ? document.f.weekDay6.value : '',
				weekDay0:document.f.weekDay0.checked ? document.f.weekDay0.value : '',
				yoy1:document.f.yoy1.value,
				yoy2:document.f.yoy2.value,
				yoy3:document.f.yoy3.value,
				yoy4:document.f.yoy4.value,
				yoy5:document.f.yoy5.value,
				yoyNm1:document.f.yoyNm1.value,
				yoyNm2:document.f.yoyNm2.value,
				yoyNm3:document.f.yoyNm3.value,
				yoyNm4:document.f.yoyNm4.value,
				yoyNm5:document.f.yoyNm5.value,
				yoyTA1:document.f.yoyTA1.value,
				yoyTA2:document.f.yoyTA2.value,
				yoyTA3:document.f.yoyTA3.value,
				yoyTA4:document.f.yoyTA4.value,
				yoyTA5:document.f.yoyTA5.value,
				sPrice:__commaUnset(document.f.sPrice.value),
				ePrice:__commaUnset(document.f.ePrice.value),
				nPrice:__commaUnset(document.f.nPrice.value),
				tPrice:__commaUnset(document.f.tPrice.value),
				sugaCode:document.f.sugaCode.value,
				sugaName:document.f.sugaName.value,
				Egubun:document.f.Egubun.value,
				Ngubun:document.f.Ngubun.value,
				Etime:document.f.Etime.value,
				Ntime:document.f.Ntime.value,
				visitSudangCheck:(document.f.visitSudangCheck.checked ? "Y" : "N"),
				visitSudang:document.f.visitSudang.value,
				sudangYul1:document.f.sudangYul1.value,
				sudangYul2:document.f.sudangYul2.value,
				carNo:document.f.carNo.value,
				gubun:'reg'
			},
			onSuccess:function (responseHttpObj) {
				calendar.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function _checkDuplicate(){
	//var mCode     = opener.document.f.mCode.value;
	//var mDate     = '';
	var lastDay   = opener.document.f.mLastDay.value;
	var newDay    = document.f.addDay.value;
	var newIndex  = document.f.addIndex.value;
	var newYoy    = new Array();
	var newFmTime = 0;
	var newToTime = 0;
	var newRequest = '';
	var checkUse;
	var checkYoy  = new Array();
	var checkFmTime = 0, checkToTime = 0;
	var checkIndex = 1;
	var checkLoop = true;
	var checkDuplicate = false;
	var checkTemp = '';
	var checkSugupja = 'N';

	newFmTime = _getTimeValue(document.f.ftHour.value, document.f.ftMin.value);
	newToTime = _getTimeValue(document.f.ttHour.value, document.f.ttMin.value);

	newYoy[1] = document.f.yoy1.value;
	newYoy[2] = document.f.yoy2.value;
	newYoy[3] = document.f.yoy3.value;
	newYoy[4] = document.f.yoy4.value;
	newYoy[5] = document.f.yoy5.value;

	checkSugupja = opener.document.getElementById('mSugupja_'+newDay+'_'+newIndex); //eval('opener.document.f.mSugupja_'+i+'_'+checkIndex);

	if (checkSugupja != undefined){
		if (checkSugupja.value == 'Y'){
			opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
		//	opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
		}else{
			if (opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value == 'N'){
				opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = 'none';
				opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '';
			//	opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
			}
		}
	}

	//for(var i=1; i<=lastDay; i++){
	for(var i=newDay; i<=newDay; i++){
		checkDuplicate = false;
		checkLoop = true;
		checkIndex = 1;
		
		while(checkLoop){
			if (checkIndex != newIndex){
				checkUse = eval('opener.document.f.mUse_'+i+'_'+checkIndex);

				if (checkUse == undefined){
					checkLoop = false;
				}

				if (checkLoop){
					if (checkUse.value == 'Y'){
						try{
							checkSugupja = opener.document.getElementById('mSugupja_'+newDay+'_'+newIndex); //eval('opener.document.f.mSugupja_'+newDay+'_'+newIndex);

							if (checkSugupja.value == 'Y'){
								opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
								opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
							//	opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
							}else{
								if (opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value == 'N'){
									opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = 'none';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '';
								}
								checkYoy[1] = eval('opener.document.f.mYoy1_'+i+'_'+checkIndex).value;
								checkYoy[2] = eval('opener.document.f.mYoy2_'+i+'_'+checkIndex).value;
								checkYoy[3] = eval('opener.document.f.mYoy3_'+i+'_'+checkIndex).value;
								checkYoy[4] = eval('opener.document.f.mYoy4_'+i+'_'+checkIndex).value;
								checkYoy[5] = eval('opener.document.f.mYoy5_'+i+'_'+checkIndex).value;

								checkTemp = eval('opener.document.f.mFmTime_'+i+'_'+checkIndex).value;
								checkFmTime = _getTimeValue(checkTemp.substring(0,2),checkTemp.substring(2,4));

								checkTemp = eval('opener.document.f.mToTime_'+i+'_'+checkIndex).value;
								checkToTime = _getTimeValue(checkTemp.substring(0,2),checkTemp.substring(2,4));

								//if (newFmTime >= checkFmTime && newFmTime <= checkToTime){
								//opener.alert(checkFmTime + ' - ' + checkToTime + ' / ' + newFmTime + ' - ' + newToTime);
								if ((checkFmTime <= newFmTime && checkToTime >  newFmTime) ||
									(checkToTime >  newToTime && checkFmTime <= newToTime)){
									//opener.alert(checkFmTime + '>=' + newFmTime + '&&' + checkToTime + '>' + newFmTime + '&&' + checkFmTime + '<' + newToTime);
									opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
									opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

									eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
									eval('opener.checkSugupja_'+newDay+'_'+newIndex).style.display = '';
									eval('opener.txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
									eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
								}

								/*
								for(var newI=1; newI<=5; newI++){
									for(var checkI=1; checkI<=5; checkI++){
										if (newYoy[newI] != '' && checkYoy[checkI] != ''){
											if (newYoy[newI] == checkYoy[checkI]){
												if ((newFmTime >= checkFmTime && newFmTime <  checkToTime) || 
													(newToTime >  checkFmTime && newToTime <= checkToTime)){
													opener.document.getElementById('checkDuplicate_'+newDay+'_'+newIndex).style.display = '';
													opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
													opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
													eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
													eval('opener.checkDuplicate_'+newDay+'_'+newIndex).style.display = '';
													eval('opener.txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
													eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
												}
											}
										}
									}
								}
								*/
							}
						}catch(e){
						//	checkLoop = false;
						}
					}
				}
			}
			checkIndex ++;
		}
	}
}

function _addCalendar(){
	var newH     = '';
	var newDay   = document.f.addDay.value;
	var newIndex = document.f.addIndex.value;
	var newDate  = document.f.addDate.value;
	var newWeek  = document.f.addWeek.value;
	
	var newSvcSubCode = _getSvcSubCode();
	var newSubject = _getCalendarSubject('ADD', newDay, newIndex);

	var mTogeUmu = 'N';
	var mBiPayUmu = 'N';
	var mTimeDoub = 'N';
	var mSudangYN = 'N';
	var tempObject = eval('opener.document.f.mUse_'+newDay+'_'+newIndex);
	var tempNew = true;

	var mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+newDate);

	if (tempObject != undefined){
		if (tempObject.value == 'N'){
			tempNew = false;
		}
	}

	var timeDiff = 0;

	if (newSvcSubCode == '200' && document.f.procTime.value == '0'){
		timeDiff = _getTimeDiff(document.f.ftHour.value+document.f.ftMin.value, document.f.ttHour.value+document.f.ttMin.value);
	}else{
		timeDiff = document.f.procTime.value;
	}

	var modifyFlag = _getModifyPos(); //수정위치

	if (!tempNew){
		eval('opener.document.f.mUse_'       +newDay+'_'+newIndex).value = 'Y';
		eval('opener.document.f.mDate_'      +newDay+'_'+newIndex).value = newDate;
		eval('opener.document.f.mSvcSubCode_'+newDay+'_'+newIndex).value = newSvcSubCode;
		eval('opener.document.f.mSvcSubCD_'  +newDay+'_'+newIndex).value = document.f.svcSubCD.value;
		eval('opener.document.f.mFmTime_'    +newDay+'_'+newIndex).value = document.f.ftHour.value+document.f.ftMin.value;
		eval('opener.document.f.mToTime_'    +newDay+'_'+newIndex).value = document.f.ttHour.value+document.f.ttMin.value;
		eval('opener.document.f.mProcTime_'  +newDay+'_'+newIndex).value = timeDiff;
		eval('opener.document.f.mTogeUmu_'   +newDay+'_'+newIndex).value = document.f.togeUmu.checked  ? 'Y' : 'N'; //document.f.togeUmu.value : '';
		eval('opener.document.f.mBiPayUmu_'  +newDay+'_'+newIndex).value = document.f.bipayUmu.checked ? 'Y' : 'N'; //document.f.bipayUmu.value : '';
		eval('opener.document.f.mTimeDoub_'  +newDay+'_'+newIndex).value = document.f.timeDoub.checked ? 'Y' : 'N'; //document.f.timeDoub.value : '';
		eval('opener.document.f.mYoy1_'      +newDay+'_'+newIndex).value = document.f.yoy1.value;
		eval('opener.document.f.mYoy2_'      +newDay+'_'+newIndex).value = document.f.yoy2.value;
		eval('opener.document.f.mYoy3_'      +newDay+'_'+newIndex).value = document.f.yoy3.value;
		eval('opener.document.f.mYoy4_'      +newDay+'_'+newIndex).value = document.f.yoy4.value;
		eval('opener.document.f.mYoy5_'      +newDay+'_'+newIndex).value = document.f.yoy5.value;
		eval('opener.document.f.mYoyNm1_'    +newDay+'_'+newIndex).value = document.f.yoyNm1.value;
		eval('opener.document.f.mYoyNm2_'    +newDay+'_'+newIndex).value = document.f.yoyNm2.value;
		eval('opener.document.f.mYoyNm3_'    +newDay+'_'+newIndex).value = document.f.yoyNm3.value;
		eval('opener.document.f.mYoyNm4_'    +newDay+'_'+newIndex).value = document.f.yoyNm4.value;
		eval('opener.document.f.mYoyNm5_'    +newDay+'_'+newIndex).value = document.f.yoyNm5.value;
		eval('opener.document.f.mYoyTA1_'    +newDay+'_'+newIndex).value = document.f.yoyTA1.value;
		eval('opener.document.f.mYoyTA2_'    +newDay+'_'+newIndex).value = document.f.yoyTA2.value;
		eval('opener.document.f.mYoyTA3_'    +newDay+'_'+newIndex).value = document.f.yoyTA3.value;
		eval('opener.document.f.mYoyTA4_'    +newDay+'_'+newIndex).value = document.f.yoyTA4.value;
		eval('opener.document.f.mYoyTA5_'    +newDay+'_'+newIndex).value = document.f.yoyTA5.value;
		eval('opener.document.f.mSValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.sPrice.value);
		eval('opener.document.f.mEValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.ePrice.value);
		eval('opener.document.f.mNValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.nPrice.value);
		eval('opener.document.f.mTValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.tPrice.value);
		eval('opener.document.f.mSugaCode_'  +newDay+'_'+newIndex).value = document.f.sugaCode.value;
		eval('opener.document.f.mSugaName_'  +newDay+'_'+newIndex).value = document.f.sugaName.value;
		eval('opener.document.f.mEGubun_'    +newDay+'_'+newIndex).value = document.f.Egubun.value;
		eval('opener.document.f.mNGubun_'    +newDay+'_'+newIndex).value = document.f.Ngubun.value;
		eval('opener.document.f.mETime_'     +newDay+'_'+newIndex).value = document.f.Etime.value;
		eval('opener.document.f.mNTime_'     +newDay+'_'+newIndex).value = document.f.Ntime.value;
		eval('opener.document.f.mWeekDay_'   +newDay+'_'+newIndex).value = newWeek;
		eval('opener.document.f.mSubject_'   +newDay+'_'+newIndex).value = newSubject;
		eval('opener.document.f.mDuplicate_' +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mSeq_'       +newDay+'_'+newIndex).value = '0';
		eval('opener.document.f.mSugupja_'   +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mDelete_'    +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mTrans_'     +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mStatusGbn_' +newDay+'_'+newIndex).value = '9';
		eval('opener.document.f.mCarNo_'     +newDay+'_'+newIndex).value = document.f.carNo.value;
		eval('opener.document.f.mSudangYN_'  +newDay+'_'+newIndex).value = document.f.visitSudangCheck.checked ? 'Y' : 'N';
		eval('opener.document.f.mSudang_'    +newDay+'_'+newIndex).value = document.f.visitSudang.value;
		eval('opener.document.f.mSudangYul1_'+newDay+'_'+newIndex).value = document.f.sudangYul1.value;
		eval('opener.document.f.mSudangYul2_'+newDay+'_'+newIndex).value = document.f.sudangYul2.value;
		eval('opener.document.f.mHoliday_'   +newDay+'_'+newIndex).value = mHoliday;
		eval('opener.document.f.mModifyPos_' +newDay+'_'+newIndex).value = modifyFlag; //수정위치

		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ffffff';
		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;
	}else{
		if (document.f.togeUmu.checked){
			mTogeUmu = 'Y';
		}
		if (document.f.bipayUmu.checked){
			mBiPayUmu = 'Y';
		}
		if (document.f.timeDoub.checked){
			mTimeDoub = 'Y';
		}
		if (document.f.visitSudangCheck.checked){
			mSudangYN = 'Y';
		}

		newH  = '';
		newH += '<input name="mUse_'       +newDay+'_'+newIndex+'" type="hidden" value="Y">';
		newH += '<input name="mDate_'      +newDay+'_'+newIndex+'" type="hidden" value="'+newDate+'">';
		newH += '<input name="mSvcSubCode_'+newDay+'_'+newIndex+'" type="hidden" value="'+newSvcSubCode+'">';
		newH += '<input name="mSvcSubCD_'  +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.svcSubCD.value+'">';
		newH += '<input name="mFmTime_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.ftHour.value+document.f.ftMin.value+'">';
		newH += '<input name="mToTime_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.ttHour.value+document.f.ttMin.value+'">';
		newH += '<input name="mProcTime_'  +newDay+'_'+newIndex+'" type="hidden" value="'+timeDiff+'">';
		newH += '<input name="mTogeUmu_'   +newDay+'_'+newIndex+'" type="hidden" value="'+mTogeUmu+'">';
		newH += '<input name="mBiPayUmu_'  +newDay+'_'+newIndex+'" type="hidden" value="'+mBiPayUmu+'">';
		newH += '<input name="mTimeDoub_'  +newDay+'_'+newIndex+'" type="hidden" value="'+mTimeDoub+'">';
		newH += '<input name="mYoy1_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy1.value+'">';
		newH += '<input name="mYoy2_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy2.value+'">';
		newH += '<input name="mYoy3_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy3.value+'">';
		newH += '<input name="mYoy4_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy4.value+'">';
		newH += '<input name="mYoy5_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy5.value+'">';
		newH += '<input name="mYoyNm1_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm1.value+'">';
		newH += '<input name="mYoyNm2_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm2.value+'">';
		newH += '<input name="mYoyNm3_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm3.value+'">';
		newH += '<input name="mYoyNm4_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm4.value+'">';
		newH += '<input name="mYoyNm5_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm5.value+'">';
		newH += '<input name="mYoyTA1_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA1.value+'">';
		newH += '<input name="mYoyTA2_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA2.value+'">';
		newH += '<input name="mYoyTA3_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA3.value+'">';
		newH += '<input name="mYoyTA4_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA4.value+'">';
		newH += '<input name="mYoyTA5_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA5.value+'">';
		newH += '<input name="mSValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+__commaUnset(document.f.sPrice.value)+'">';
		newH += '<input name="mEValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+__commaUnset(document.f.ePrice.value)+'">';
		newH += '<input name="mNValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+__commaUnset(document.f.nPrice.value)+'">';
		newH += '<input name="mTValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+__commaUnset(document.f.tPrice.value)+'">';
		newH += '<input name="mSugaCode_'  +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sugaCode.value+'">';
		newH += '<input name="mSugaName_'  +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sugaName.value+'">';
		newH += '<input name="mEGubun_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.Egubun.value+'">';
		newH += '<input name="mNGubun_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.Ngubun.value+'">';
		newH += '<input name="mETime_'     +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.Etime.value+'">';
		newH += '<input name="mNTime_'     +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.Ntime.value+'">';
		newH += '<input name="mWeekDay_'   +newDay+'_'+newIndex+'" type="hidden" value="'+newWeek+'">';
		newH += '<input name="mSubject_'   +newDay+'_'+newIndex+'" type="hidden" value="'+newSubject+'">';
		newH += '<input name="mDuplicate_' +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mSeq_'       +newDay+'_'+newIndex+'" type="hidden" value="0">';
		newH += '<input name="mSugupja_'   +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mDelete_'    +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mTrans_'     +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mStatusGbn_' +newDay+'_'+newIndex+'" type="hidden" value="9">';
		newH += '<input name="mCarNo_'     +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.carNo.value+'">';
		newH += '<input name="mSudangYN_'  +newDay+'_'+newIndex+'" type="hidden" value="'+mSudangYN+'">';
		newH += '<input name="mSudang_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.visitSudang.value+'">';
		newH += '<input name="mSudangYul1_'+newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sudangYul1.value+'">';
		newH += '<input name="mSudangYul2_'+newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sudangYul2.value+'">';
		newH += '<input name="mHoliday_'   +newDay+'_'+newIndex+'" type="hidden" value="'+mHoliday+'">';
		newH += '<input name="mModifyPos_' +newDay+'_'+newIndex+'" type="hidden" value="'+modifyFlag+'">'; //수정위치

		opener.addCalendar.innerHTML += newH;

		eval('opener.txtSubject_'+newDay).innerHTML += newSubject;
	}
	
	window.close();
}

// 수정위치
function _getModifyPos(){
	var modifyFlag = 'N';
	if (opener.document.getElementById('modifyFlag') != null){
		if (opener.document.getElementById('modifyFlag').value == 'DAY'){
			modifyFlag = 'D';
		}else{
			modifyFlag = 'M';
		}
	}
	return modifyFlag;
}

function _modifyCalendar(){
	var newH = opener.addCalendar.innerHTML;
	var newDay = document.f.addDay.value;
	var newIndex = document.f.addIndex.value;
	var newDate = document.f.addDate.value;
	var newWeek = document.f.addWeek.value;
	
	var mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+newDate);

	var newSvcSubCode = _getSvcSubCode();
	var newSubject = _getCalendarSubject('MODIFY', newDay, newIndex);

	var timeDiff = 0;

	if (newSvcSubCode == '200' && document.f.procTime.value == '0'){
		timeDiff = _getTimeDiff(document.f.ftHour.value+document.f.ftMin.value, document.f.ttHour.value+document.f.ttMin.value);
	}else{
		timeDiff = document.f.procTime.value;
	}

	var modifyFlag = _getModifyPos(); //수정위치

	eval('opener.document.f.mUse_'       +newDay+'_'+newIndex).value = 'Y';
	eval('opener.document.f.mDate_'      +newDay+'_'+newIndex).value = newDate;
	eval('opener.document.f.mSvcSubCode_'+newDay+'_'+newIndex).value = newSvcSubCode;
	eval('opener.document.f.mSvcSubCD_'  +newDay+'_'+newIndex).value = document.f.svcSubCD.value;
	eval('opener.document.f.mFmTime_'    +newDay+'_'+newIndex).value = document.f.ftHour.value+document.f.ftMin.value;
	eval('opener.document.f.mToTime_'    +newDay+'_'+newIndex).value = document.f.ttHour.value+document.f.ttMin.value;
	eval('opener.document.f.mProcTime_'  +newDay+'_'+newIndex).value = timeDiff; //document.f.procTime.value;
	eval('opener.document.f.mTogeUmu_'   +newDay+'_'+newIndex).value = document.f.togeUmu.checked == true ? document.f.togeUmu.value : '';
	eval('opener.document.f.mBiPayUmu_'  +newDay+'_'+newIndex).value = document.f.bipayUmu.checked  == true  ? document.f.bipayUmu.value : '';
	eval('opener.document.f.mTimeDoub_'  +newDay+'_'+newIndex).value = document.f.timeDoub.checked  == true  ? document.f.timeDoub.value : '';
	eval('opener.document.f.mYoy1_'      +newDay+'_'+newIndex).value = document.f.yoy1.value;
	eval('opener.document.f.mYoy2_'      +newDay+'_'+newIndex).value = document.f.yoy2.value;
	eval('opener.document.f.mYoy3_'      +newDay+'_'+newIndex).value = document.f.yoy3.value;
	eval('opener.document.f.mYoy4_'      +newDay+'_'+newIndex).value = document.f.yoy4.value;
	eval('opener.document.f.mYoy5_'      +newDay+'_'+newIndex).value = document.f.yoy5.value;
	eval('opener.document.f.mYoyNm1_'    +newDay+'_'+newIndex).value = document.f.yoyNm1.value;
	eval('opener.document.f.mYoyNm2_'    +newDay+'_'+newIndex).value = document.f.yoyNm2.value;
	eval('opener.document.f.mYoyNm3_'    +newDay+'_'+newIndex).value = document.f.yoyNm3.value;
	eval('opener.document.f.mYoyNm4_'    +newDay+'_'+newIndex).value = document.f.yoyNm4.value;
	eval('opener.document.f.mYoyNm5_'    +newDay+'_'+newIndex).value = document.f.yoyNm5.value;
	eval('opener.document.f.mYoyTA1_'    +newDay+'_'+newIndex).value = document.f.yoyTA1.value;
	eval('opener.document.f.mYoyTA2_'    +newDay+'_'+newIndex).value = document.f.yoyTA2.value;
	eval('opener.document.f.mYoyTA3_'    +newDay+'_'+newIndex).value = document.f.yoyTA3.value;
	eval('opener.document.f.mYoyTA4_'    +newDay+'_'+newIndex).value = document.f.yoyTA4.value;
	eval('opener.document.f.mYoyTA5_'    +newDay+'_'+newIndex).value = document.f.yoyTA5.value;
	eval('opener.document.f.mSValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.sPrice.value);
	eval('opener.document.f.mEValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.ePrice.value);
	eval('opener.document.f.mNValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.nPrice.value);
	eval('opener.document.f.mTValue_'    +newDay+'_'+newIndex).value = __commaUnset(document.f.tPrice.value);
	eval('opener.document.f.mSugaCode_'  +newDay+'_'+newIndex).value = document.f.sugaCode.value;
	eval('opener.document.f.mSugaName_'  +newDay+'_'+newIndex).value = document.f.sugaName.value;
	eval('opener.document.f.mEGubun_'    +newDay+'_'+newIndex).value = document.f.Egubun.value;
	eval('opener.document.f.mNGubun_'    +newDay+'_'+newIndex).value = document.f.Ngubun.value;
	eval('opener.document.f.mETime_'     +newDay+'_'+newIndex).value = document.f.Etime.value;
	eval('opener.document.f.mNTime_'     +newDay+'_'+newIndex).value = document.f.Ntime.value;
	eval('opener.document.f.mWeekDay_'   +newDay+'_'+newIndex).value = newWeek;
	eval('opener.document.f.mSubject_'   +newDay+'_'+newIndex).value = newSubject;
	eval('opener.document.f.mDuplicate_' +newDay+'_'+newIndex).value = 'N';
	eval('opener.document.f.mCarNo_'     +newDay+'_'+newIndex).value = document.f.carNo.value;
	eval('opener.document.f.mSudangYN_'  +newDay+'_'+newIndex).value = document.f.visitSudangCheck.checked ? 'Y' : 'N';
	eval('opener.document.f.mSudang_'    +newDay+'_'+newIndex).value = document.f.visitSudang.value;
	eval('opener.document.f.mSudangYul1_'+newDay+'_'+newIndex).value = document.f.sudangYul1.value;
	eval('opener.document.f.mSudangYul2_'+newDay+'_'+newIndex).value = document.f.sudangYul2.value;
	eval('opener.document.f.mHoliday_'   +newDay+'_'+newIndex).value = mHoliday;
	eval('opener.document.f.mModifyPos_' +newDay+'_'+newIndex).value = modifyFlag; //수정위치

	//eval('opener.document.f.mSeq_'       +newDay+'_'+newIndex).value = '0';
	//eval('opener.document.f.mSugupja_'   +newDay+'_'+newIndex).value = 'N';
	//eval('opener.document.f.mDelete_'    +newDay+'_'+newIndex).value = 'N';
	eval('opener.document.f.mTrans_'+newDay+'_'+newIndex).value = 'N';
	//eval('opener.document.f.mStatusGbn_' +newDay+'_'+newIndex).value = '9';
	
	//var tempSubject = _setSubject(newDay, newIndex, 'opener.');

	//eval('opener.txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;
	//opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
	opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;

	window.close();
}

// 일정표 제목
function _getCalendarSubject(gubun, pDay, pIndex){
	var newSubject = '';
	var newSTable = '';
	var newBorderTop = '';
	var svcSubCode = _getSvcSubCode();

	/*
	if (svcSubCode == '500'){
		newSTable  = document.f.ftHour.value+':'+document.f.ftMin.value+'<br>';
	}else{
		newSTable  = document.f.ftHour.value+':'+document.f.ftMin.value+'~';
		newSTable += document.f.ttHour.value+':'+document.f.ttMin.value+'<br>';
	}
	*/

	newSTable  = document.f.ftHour.value+':'+document.f.ftMin.value+'~';
	newSTable += document.f.ttHour.value+':'+document.f.ttMin.value+'<br>';

	newSTable += document.f.yoyNm1.value != '' ? document.f.yoyNm1.value+',' : '';
	newSTable += document.f.yoyNm2.value != '' ? document.f.yoyNm2.value+',' : '';
	newSTable += document.f.yoyNm3.value != '' ? document.f.yoyNm3.value+',' : '';
	newSTable += document.f.yoyNm4.value != '' ? document.f.yoyNm4.value+',' : '';
	newSTable += document.f.yoyNm5.value != '' ? document.f.yoyNm5.value+',' : '';
	newSTable  = newSTable.substring(0, newSTable.length-1)+'<br>'+document.f.sugaName.value;

	if (pIndex != '1' && gubun == 'ADD'){
		newBorderTop = 'border-top:1px dotted #cccccc;';
	}

	newSubject  = "<div id='txtSubject_"+pDay+"_"+pIndex+"' style='display:; "+newBorderTop+"'>";
	newSubject += "<table>";
	newSubject += "  <tr>";
	newSubject += "    <td class='noborder' style='width:100%; text-align:left; vertical-align:top; line-height:1.3em;'>";
	newSubject += "      <div style='position:absolute; width:100%; height:100%;'>";
	newSubject += "        <div style='position:absolute; top:1px; left:80px;'>";
	newSubject += "          <img src='../image/btn_edit.png' style='cursor:pointer;' onClick='_modifyDiary("+pDay+","+pIndex+");'>";
	newSubject += "          <img src='../image/btn_del.png' style='cursor:pointer;' onClick='_clearDiary("+pDay+","+pIndex+");'>";
	newSubject += "        </div>";
	newSubject += "      </div>";
	newSubject += "      <div>"+newSTable+"</div>";
	newSubject += "      <div id='checkDuplicate_"+pDay+"_"+pIndex+"' style='display:none;'>중복</div>";
	newSubject += "      <div id='checkSugupja_"+pDay+"_"+pIndex+"' style='display:none;'>타수급자중복</div>";
	newSubject += "    </td>";
	newSubject += "  </tr>";
	newSubject += "</table>";
	newSubject += "</div>";

	return newSubject;
}

// 수급자 월수급 현황 계산
function _addYoySudangList(){
	var yoyInfo = new Array();

	var mCode = document.f.mCode.value;
	var mMode = document.f.mMode.value;

	var boninYul = document.f.boninYul.value;
	var MaxAmount = parseInt(document.f.maxAmount.value);
	var maxTempAmt = 0;
	var maxTempPrc = 0;

	var amtSugub200 = 0;
	var amtSugub500 = 0;
	var amtSugub800 = 0;
	var amtSugubTot = 0;

	var amtBiPay200 = 0;
	var amtBiPay500 = 0;
	var amtBiPay800 = 0;
	var amtBiPayTot = 0;

	var amtBonin200 = 0
	var amtBonin500 = 0
	var amtBonin800 = 0
	var amtBoninTot = 0

	var amtOver200 = 0
	var amtOver500 = 0
	var amtOver800 = 0
	var amtOverTot = 0

	var row = document.getElementById("yoyConstBody");
	var row_tr = null; //document.createElement("tr");
	var row_td = new Array();
	var row_td_count = 12;

	// 데이타 시작
	var mLastDay = document.f.mLastDay.value;
	var mUse, mDuplicate, mDelete, mYoyangsa, mStatus, mHoliday, mGoto;
	var mIndex = 1;
	var checkLoop = true;
	var checkIndex = 0;
	var dayIndex = 0;
	var newFlag = true;
	var sugaRate = 0;

	for(var mDay=1; mDay<=mLastDay; mDay++){
		checkLoop = true;
		mIndex = 1;

		while(checkLoop){
			mUse       = eval('document.f.mUse_'+mDay+'_'+mIndex);
			mDuplicate = eval('document.f.mDuplicate_'+mDay+'_'+mIndex);
			mDelete    = eval('document.f.mDelete_'+mDay+'_'+mIndex);
			mYoyangsa  = eval('document.f.mYoy1_'+mDay+'_'+mIndex);
			mStatus    = eval('document.f.mStatusGbn_'+mDay+'_'+mIndex);

			if (mUse       == undefined ||
				mDuplicate == undefined ||
				mDelete    == undefined ||
				mYoyangsa  == undefined){
				checkLoop = false;
			}

			if (checkLoop){
				if (mMode == 'MODIFY'){
					if (mStatus.value == '1'){
						mGoto = 'Y';
					}else{
						mGoto = 'N';
					}
				}else{
					mGoto = 'Y';
				}
				
				//if (mUse.value == 'Y' && mDuplicate.value == 'N' && eval('document.f.mYoy1_'+mDay+'_'+mIndex).value != ''){
				if (mUse.value == 'Y' && mDuplicate.value == 'N' && mDelete.value == 'N' && mYoyangsa.value != '' && mGoto == 'Y'){
					if (dayIndex == 0){
						//if (eval('document.f.mYoy1_'+mDay+'_'+mIndex).value != ''){
						if (mYoyangsa != ''){
							newFlag = true;
						}else{
							newFlag = false;
						}
					}else{
						newFlag = true;
						mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+eval('document.f.mDate_'+mDay+'_'+mIndex).value);
						for(var i=1; i<yoyInfo.length; i++){
							if (yoyInfo[i]['YoyCD1']  == eval('document.f.mYoy1_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['YoyCD2']  == eval('document.f.mYoy2_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['YoyCD3']  == eval('document.f.mYoy3_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['YoyCD4']  == eval('document.f.mYoy4_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['YoyCD5']  == eval('document.f.mYoy5_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['SugaCD']  == eval('document.f.mSugaCode_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['FmTime']  == eval('document.f.mFmTime_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['ToTime']  == eval('document.f.mToTime_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['Holiday'] == mHoliday){
								newFlag = false;
								dayIndex = i;
								break;}}}}}}}}
							}
						}
					}

					if (newFlag){
						if (dayIndex == 0){
							dayIndex = 1;
						}else{
							dayIndex = yoyInfo.length;
						}

						mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+eval('document.f.mDate_'+mDay+'_'+mIndex).value);

						yoyInfo[dayIndex] = new Array();
						
						yoyInfo[dayIndex]['Holiday'] = mHoliday;

						yoyInfo[dayIndex]['SvcSubCode'] = eval('document.f.mSvcSubCode_'+mDay+'_'+mIndex).value;

						yoyInfo[dayIndex]['SugaCD'] = eval('document.f.mSugaCode_'+mDay+'_'+mIndex).value //수가코드
						yoyInfo[dayIndex]['SugaNM'] = eval('document.f.mSugaName_'+mDay+'_'+mIndex).value //수가명

						sugaRate = getHttpRequest('../inc/_check.php?gubun=getSugaRateValue&mCode='+mCode+'&mSugaCode='+yoyInfo[dayIndex]['SugaCD']); //수가 할증비율
						sugaRate = sugaRate / 100;
						
						yoyInfo[dayIndex]['YoyCD1'] = eval('document.f.mYoy1_'+mDay+'_'+mIndex).value; //요양사1
						yoyInfo[dayIndex]['YoyCD2'] = eval('document.f.mYoy2_'+mDay+'_'+mIndex).value; //요양사2
						yoyInfo[dayIndex]['YoyCD3'] = eval('document.f.mYoy3_'+mDay+'_'+mIndex).value; //요양사3
						yoyInfo[dayIndex]['YoyCD4'] = eval('document.f.mYoy4_'+mDay+'_'+mIndex).value; //요양사4
						yoyInfo[dayIndex]['YoyCD5'] = eval('document.f.mYoy5_'+mDay+'_'+mIndex).value; //요양사5

						yoyInfo[dayIndex]['YoyNM1'] = eval('document.f.mYoyNm1_'+mDay+'_'+mIndex).value; //요양사명1
						yoyInfo[dayIndex]['YoyNM2'] = eval('document.f.mYoyNm2_'+mDay+'_'+mIndex).value; //요양사명2
						yoyInfo[dayIndex]['YoyNM3'] = eval('document.f.mYoyNm3_'+mDay+'_'+mIndex).value; //요양사명3
						yoyInfo[dayIndex]['YoyNM4'] = eval('document.f.mYoyNm4_'+mDay+'_'+mIndex).value; //요양사명4
						yoyInfo[dayIndex]['YoyNM5'] = eval('document.f.mYoyNm5_'+mDay+'_'+mIndex).value; //요양사명5

						yoyInfo[dayIndex]['YoyTA1'] = parseInt(eval('document.f.mYoyTA1_'+mDay+'_'+mIndex).value) + (eval('document.f.mYoyTA1_'+mDay+'_'+mIndex).value * sugaRate); //요양사시급1
						yoyInfo[dayIndex]['YoyTA2'] = eval('document.f.mYoyTA2_'+mDay+'_'+mIndex).value; //요양사시급2
						yoyInfo[dayIndex]['YoyTA3'] = eval('document.f.mYoyTA3_'+mDay+'_'+mIndex).value; //요양사시급3
						yoyInfo[dayIndex]['YoyTA4'] = eval('document.f.mYoyTA4_'+mDay+'_'+mIndex).value; //요양사시급4
						yoyInfo[dayIndex]['YoyTA5'] = eval('document.f.mYoyTA5_'+mDay+'_'+mIndex).value; //요양사시급5

						yoyInfo[dayIndex]['FmTime'] = eval('document.f.mFmTime_'+mDay+'_'+mIndex).value
						yoyInfo[dayIndex]['ToTime'] = eval('document.f.mToTime_'+mDay+'_'+mIndex).value
						yoyInfo[dayIndex]['Time']   = yoyInfo[dayIndex]['FmTime'].substring(0,2)+':'+yoyInfo[dayIndex]['FmTime'].substring(2,4)+'~'
												    + yoyInfo[dayIndex]['ToTime'].substring(0,2)+':'+yoyInfo[dayIndex]['ToTime'].substring(2,4);

						yoyInfo[dayIndex]['ETime'] = eval('document.f.mETime_'+mDay+'_'+mIndex).value;
						yoyInfo[dayIndex]['NTime'] = eval('document.f.mNTime_'+mDay+'_'+mIndex).value;

						if (yoyInfo[dayIndex]['ETime'] == ''){
							yoyInfo[dayIndex]['ETime'] = 0;
						}
						if (yoyInfo[dayIndex]['NTime'] == ''){
							yoyInfo[dayIndex]['NTime'] = 0;
						}
						
						/*
						if (yoyInfo[dayIndex]['Holiday'] == 'Y'){
							yoyInfo[dayIndex]['SugaPrice'] = eval('document.f.mSValue_'+mDay+'_'+mIndex).value;
						}else{
							yoyInfo[dayIndex]['SugaPrice'] = eval('document.f.mTValue_'+mDay+'_'+mIndex).value;
						}
						*/
						//yoyInfo[dayIndex]['SugaPrice'] = eval('document.f.mSValue_'+mDay+'_'+mIndex).value; //기준수가로 계산
						yoyInfo[dayIndex]['SugaPrice'] = eval('document.f.mTValue_'+mDay+'_'+mIndex).value; //수가계로 계산
						yoyInfo[dayIndex]['Count'] = 1;

						if (eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == '29' ||
							eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == '59' ||
							eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == '89'){
							yoyInfo[dayIndex]['ProcTime'] = 0;
							yoyInfo[dayIndex]['TempProcTime'] = 0;
						}else{
							yoyInfo[dayIndex]['ProcTime'] = (!isNaN(eval('document.f.mProcTime_'+mDay+'_'+mIndex).value) ? eval('document.f.mProcTime_'+mDay+'_'+mIndex).value : 0) / 60;
							yoyInfo[dayIndex]['TempProcTime'] = (!isNaN(eval('document.f.mProcTime_'+mDay+'_'+mIndex).value) ? eval('document.f.mProcTime_'+mDay+'_'+mIndex).value : 0) / 60;
						}

						yoyInfo[dayIndex]['ProcTime'] = yoyInfo[dayIndex]['ProcTime'].toFixed(1);
						
						// 휴일이 아닐경우만 할증을 계산한다.
						if (yoyInfo[dayIndex]['Holiday'] == 'Y'){
							yoyInfo[dayIndex]['ETime'] = 0;
							yoyInfo[dayIndex]['NTime'] = 0;
						}

						if (yoyInfo[dayIndex]['ETime'] > 0){
							yoyInfo[dayIndex]['ETime'] = yoyInfo[dayIndex]['ETime'] / 60 * 0.2;
							yoyInfo[dayIndex]['ETime'] = yoyInfo[dayIndex]['ETime'].toFixed(1);
							yoyInfo[dayIndex]['TempProcTime'] = yoyInfo[dayIndex]['TempProcTime'] + (eval('document.f.mETime_'+mDay+'_'+mIndex).value / 60 * 0.2);
						}
						if (yoyInfo[dayIndex]['NTime'] > 0){
							yoyInfo[dayIndex]['NTime'] = yoyInfo[dayIndex]['NTime'] / 60 * 0.3;
							yoyInfo[dayIndex]['NTime'] = yoyInfo[dayIndex]['NTime'].toFixed(1);
							yoyInfo[dayIndex]['TempProcTime'] = yoyInfo[dayIndex]['TempProcTime'] + (eval('document.f.mNTime_'+mDay+'_'+mIndex).value / 60 * 0.3);
						}
						yoyInfo[dayIndex]['TempProcTime'] = yoyInfo[dayIndex]['TempProcTime'].toFixed(1);

						yoyInfo[dayIndex]['YoyCount'] = 0;
						yoyInfo[dayIndex]['YoyAlt']   = yoyInfo[dayIndex]['YoyNM1'];

						if (yoyInfo[dayIndex]['YoyNM2'] != ''){
							yoyInfo[dayIndex]['YoyCount']++;
							yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM2'];
						}
						if (yoyInfo[dayIndex]['YoyNM3'] != ''){
							yoyInfo[dayIndex]['YoyCount']++;
							yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM3'];
						}
						if (yoyInfo[dayIndex]['YoyNM4'] != ''){
							yoyInfo[dayIndex]['YoyCount']++;
							yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM4'];
						}
						if (yoyInfo[dayIndex]['YoyNM5'] != ''){
							yoyInfo[dayIndex]['YoyCount']++;
							yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM5'];
						}

						yoyInfo[dayIndex]['YoyName'] = yoyInfo[dayIndex]['YoyNM1'];

						if (yoyInfo[dayIndex]['YoyCount'] > 0){
							yoyInfo[dayIndex]['YoyName'] += '외'+yoyInfo[dayIndex]['YoyCount']+'명';
						}else{
							yoyInfo[dayIndex]['YoyAlt'] = '';
						}
					}else{
						if (yoyInfo.length > 0){
							yoyInfo[dayIndex]['Count']++;
						}
					}

					if (yoyInfo.length > 0){
						yoyInfo[dayIndex]['SugaTotal']   = yoyInfo[dayIndex]['Count'] * yoyInfo[dayIndex]['SugaPrice'];

						if (yoyInfo[dayIndex]['SvcSubCode'] == '200'){
							yoyInfo[dayIndex]['Sudang'] = parseInt(yoyInfo[dayIndex]['YoyTA1'] * yoyInfo[dayIndex]['TempProcTime']);
						}else{
							if (eval('document.f.mSudangYN_'+mDay+'_'+mIndex).value == 'Y'){
								yoyInfo[dayIndex]['Sudang'] = __commaUnset(eval('document.f.mSudang_'+mDay+'_'+mIndex).value);
							}else{
								yoyInfo[dayIndex]['Sudang'] = parseInt(yoyInfo[dayIndex]['YoyTA1'] * yoyInfo[dayIndex]['TempProcTime']);
							}
						}
						yoyInfo[dayIndex]['SudangTotal'] = yoyInfo[dayIndex]['Count'] * yoyInfo[dayIndex]['Sudang'];
						yoyInfo[dayIndex]['Amount']      = yoyInfo[dayIndex]['SugaTotal'] - yoyInfo[dayIndex]['SudangTotal'];
						
						if (eval('document.f.mBiPayUmu_'+mDay+'_'+mIndex).value == 'Y'){
							switch(yoyInfo[dayIndex]['SvcSubCode']){
								case '200': amtBiPay200 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
								case '500': amtBiPay500 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
								case '800': amtBiPay800 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
							}
						}else{
							maxTempAmt = amtSugub200 + amtSugub500 + amtSugub800 + parseInt(yoyInfo[dayIndex]['SugaPrice']);
							if (MaxAmount > maxTempAmt){
								switch(yoyInfo[dayIndex]['SvcSubCode']){
									case '200': amtSugub200 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
									case '500': amtSugub500 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
									case '800': amtSugub800 += parseInt(yoyInfo[dayIndex]['SugaPrice']); break;
								}
							}else{
								if (MaxAmount >= amtSugub200 + amtSugub500 + amtSugub800){
									maxTempPrc = (MaxAmount - (amtSugub200 + amtSugub500 + amtSugub800));
									
									switch(yoyInfo[dayIndex]['SvcSubCode']){
										case '200': amtSugub200 += maxTempPrc; break;
										case '500': amtSugub500 += maxTempPrc; break;
										case '800': amtSugub800 += maxTempPrc; break;
									}
								}

								switch(yoyInfo[dayIndex]['SvcSubCode']){
									case '200': amtOver200 += (parseInt(yoyInfo[dayIndex]['SugaPrice']) - maxTempPrc); break;
									case '500': amtOver500 += (parseInt(yoyInfo[dayIndex]['SugaPrice']) - maxTempPrc); break;
									case '800': amtOver800 += (parseInt(yoyInfo[dayIndex]['SugaPrice']) - maxTempPrc); break;
								}
							}
						}
						
						if (eval('document.f.mBiPayUmu_'+mDay+'_'+mIndex).value != 'Y'){
							// 비급여이면 본인부담액을 포함하지 않는다.
							switch(yoyInfo[dayIndex]['SvcSubCode']){
								case '200': amtBonin200 += Math.floor(parseInt(yoyInfo[dayIndex]['SugaPrice']) * boninYul / 100); break;
								case '500': amtBonin500 += Math.floor(parseInt(yoyInfo[dayIndex]['SugaPrice']) * boninYul / 100); break;
								case '800': amtBonin800 += Math.floor(parseInt(yoyInfo[dayIndex]['SugaPrice']) * boninYul / 100); break;
							}
						}
					}
				}
			}
			mIndex ++;
		}
	}
	// 데이타 종료

	while(1){
		if (parseInt(row.childNodes.length) == 0){
			break;
		}

		var last = parseInt(row.childNodes.length)-1;
			row.removeChild(row.childNodes[last]);
	}

	var amtSugaTotal = 0;
	var amtudangTotal = 0;
	var amtAmoutTotal = 0;
	var n = 0;
	
	for(var i=1; i<=yoyInfo.length; i++){
		row_tr = document.createElement("tr");

		for(var ii=1; ii<=row_td_count; ii++){
			row_td[ii] = document.createElement("td");
			row_td[ii].style.height = '24px';
			//row_td[ii].style.lineHeight = '1.2em';
		}

		if (i == yoyInfo.length){
			row_td[1].innerHTML  = '계';
			row_td[2].innerHTML  = '';
			row_td[3].innerHTML  = '';
			row_td[4].innerHTML  = '';
			row_td[5].innerHTML  = '';
			row_td[6].innerHTML  = __commaSet(amtSugaTotal);
			row_td[7].innerHTML  = '';
			row_td[8].innerHTML  = '';
			row_td[9].innerHTML  = '';
			row_td[10].innerHTML = __commaSet(amtudangTotal);
			row_td[11].innerHTML = __commaSet(amtAmoutTotal);
			row_td[12].innerHTML = '';

			row_td[1].style.fontWeight  = 'bold';
		//	row_td[1].style.backgroundColor  = '#cccccc';

			row_td[1].style.textAlign = 'center';
		}else{
			row_td[1].innerHTML  = yoyInfo[i]['YoyName'];
			row_td[2].innerHTML  = yoyInfo[i]['SugaNM'];
			row_td[3].innerHTML  = yoyInfo[i]['Time'];
			row_td[4].innerHTML  = __commaSet(yoyInfo[i]['SugaPrice']);
			row_td[5].innerHTML  = yoyInfo[i]['Count'];
			row_td[6].innerHTML  = __commaSet(yoyInfo[i]['SugaTotal']);
			row_td[7].innerHTML  = __commaSet(yoyInfo[i]['YoyTA1']);
			row_td[8].innerHTML  = yoyInfo[i]['ProcTime'];

			if (yoyInfo[i]['ETime'] > 0){
				row_td[8].innerHTML += '[';
				row_td[8].innerHTML += '<font color="#0000ff" title="야간">'+yoyInfo[i]['ETime']+'</font>';
				if (yoyInfo[i]['NTime'] == 0){
					row_td[8].innerHTML += ']';
				}
			}
			if (yoyInfo[i]['NTime'] > 0){
				if (yoyInfo[i]['ETime'] == 0){
					row_td[8].innerHTML += '[';
				}else{
					row_td[8].innerHTML += ',';
				}
				row_td[8].innerHTML += '<font color="#ff0000" title="심야">'+yoyInfo[i]['NTime']+'</font>';
				row_td[8].innerHTML += ']';
			}

			row_td[9].innerHTML  = __commaSet(yoyInfo[i]['Sudang']);
			row_td[10].innerHTML = __commaSet(yoyInfo[i]['SudangTotal']);
			row_td[11].innerHTML = __commaSet(yoyInfo[i]['Amount']);
			row_td[12].innerHTML = '';

			if (yoyInfo[dayIndex]['YoyAlt'] != ''){
				row_td[1].title = yoyInfo[dayIndex]['YoyAlt'];
			}

			amtSugaTotal  += yoyInfo[i]['SugaTotal'];
			amtudangTotal += yoyInfo[i]['SudangTotal'];
			amtAmoutTotal += yoyInfo[i]['Amount'];

			row_td[1].style.textAlign = 'left';
			row_td[2].style.textAlign = 'left';
			row_td[1].style.paddingLeft = '2px';
			row_td[2].style.paddingLeft = '2px';
		}

		row_td[4].style.textAlign = 'right';
		row_td[5].style.textAlign = 'right';
		row_td[6].style.textAlign = 'right';
		row_td[7].style.textAlign = 'right';
		row_td[9].style.textAlign = 'right';
		row_td[10].style.textAlign = 'right';
		row_td[11].style.textAlign = 'right';
		row_td[4].style.paddingRight = '5px';
		row_td[5].style.paddingRight = '5px';
		row_td[6].style.paddingRight = '5px';
		row_td[7].style.paddingRight = '5px';
		row_td[9].style.paddingRight = '5px';
		row_td[10].style.paddingRight = '5px';
		row_td[11].style.paddingRight = '5px';

		row_td[6].style.backgroundColor  = '#fffbe2';
		row_td[10].style.backgroundColor = '#fffbe2';
		 
		row_td[6].style.fontWeight  = 'bold';
		row_td[10].style.fontWeight = 'bold';

		for(var ii=1; ii<=row_td_count; ii++){
			row_tr.appendChild(row_td[ii]);
		}

		row.appendChild(row_tr);
	}

	var amtSurplus = 0;
	var amtSurplusColor = '#0000ff';
	var amtBoninSum200 = 0;
	var amtBoninSum500 = 0;
	var amtBoninSum800 = 0;
	var amtBoninSumTot = 0;

	/*
	amtSugub200 = cutOff(amtSugub200);
	amtSugub500 = cutOff(amtSugub500);
	amtSugub800 = cutOff(amtSugub800);
	amtBiPay200 = cutOff(amtBiPay200);
	amtBiPay500 = cutOff(amtBiPay500);
	amtBiPay800 = cutOff(amtBiPay800);
	amtBonin200 = cutOff(amtBonin200);
	amtBonin500 = cutOff(amtBonin500);
	amtBonin800 = cutOff(amtBonin800);
	amtOver200  = cutOff(amtOver200);
	amtOver500  = cutOff(amtOver500);
	amtOver800  = cutOff(amtOver800);
	*/

	amtSugubTot = amtSugub200 + amtSugub500 + amtSugub800;
	amtBiPayTot = amtBiPay200 + amtBiPay500 + amtBiPay800;
	amtBoninTot = amtBonin200 + amtBonin500 + amtBonin800;
	amtOverTot  = amtOver200  + amtOver500  + amtOver800;

	amtSurplus = MaxAmount - amtSugubTot - amtBiPayTot - amtOverTot;
	
	try{
		if (document.f.pressCal.value != 'N'){
			if (amtSurplus < 0){
				alert('수급자의 급여한도가 초과되었습니다. 확인하여 주십시오.');
			}
		}
	}catch(e){}
	if (amtSurplus < 0){
		amtSurplusColor = '#ff0000';
	}

	amtBoninSum200 = amtBonin200 + amtOver200 + amtBiPay200;
	amtBoninSum500 = amtBonin500 + amtOver500 + amtBiPay500;
	amtBoninSum800 = amtBonin800 + amtOver800 + amtBiPay800;
	amtBoninSumTot = amtBoninTot + amtOverTot + amtBiPayTot;

	document.getElementById('txtSugub200Amt').innerHTML = __commaSet(amtSugub200);
	document.getElementById('txtSugub500Amt').innerHTML = __commaSet(amtSugub500);
	document.getElementById('txtSugub800Amt').innerHTML = __commaSet(amtSugub800);
	document.getElementById('txtSugubTotAmt').innerHTML = __commaSet(amtSugubTot);
	
	document.getElementById('txtBonin200Amt').innerHTML = __commaSet(amtBonin200);
	document.getElementById('txtBonin500Amt').innerHTML = __commaSet(amtBonin500);
	document.getElementById('txtBonin800Amt').innerHTML = __commaSet(amtBonin800);
	document.getElementById('txtBoninTotAmt').innerHTML = __commaSet(amtBoninTot);

	document.getElementById('txtOver200Amt').innerHTML = __commaSet(amtOver200);
	document.getElementById('txtOver500Amt').innerHTML = __commaSet(amtOver500);
	document.getElementById('txtOver800Amt').innerHTML = __commaSet(amtOver800);
	document.getElementById('txtOverTotAmt').innerHTML = __commaSet(amtOverTot);

	document.getElementById('txtBiPay200Amt').innerHTML = __commaSet(amtBiPay200);
	document.getElementById('txtBiPay500Amt').innerHTML = __commaSet(amtBiPay500);
	document.getElementById('txtBiPay800Amt').innerHTML = __commaSet(amtBiPay800);
	document.getElementById('txtBiPayTotAmt').innerHTML = __commaSet(amtBiPayTot);

	document.getElementById('txtBoninSum200Amt').innerHTML = __commaSet(amtBoninSum200);
	document.getElementById('txtBoninSum500Amt').innerHTML = __commaSet(amtBoninSum500);
	document.getElementById('txtBoninSum800Amt').innerHTML = __commaSet(amtBoninSum800);
	document.getElementById('txtBoninSumTotAmt').innerHTML = __commaSet(amtBoninSumTot);

	document.getElementById('txtSurAmount').innerHTML = __commaSet(amtSurplus);
	document.getElementById('txtSurAmount').style.color = amtSurplusColor;

	document.getElementById('sugub200Amt').value = amtSugub200;
	document.getElementById('sugub500Amt').value = amtSugub500;
	document.getElementById('sugub800Amt').value = amtSugub800;
	document.getElementById('sugubTotAmt').value = amtSugubTot;

	document.getElementById('bonin200Amt').value = amtBonin200;
	document.getElementById('bonin500Amt').value = amtBonin500;
	document.getElementById('bonin800Amt').value = amtBonin800;
	document.getElementById('boninTotAmt').value = amtBoninTot;

	document.getElementById('over200Amt').value = amtOver200;
	document.getElementById('over500Amt').value = amtOver500;
	document.getElementById('over800Amt').value = amtOver800;
	document.getElementById('overTotAmt').value = amtOverTot;

	document.getElementById('biPay200Amt').value = amtBiPay200;
	document.getElementById('biPay500Amt').value = amtBiPay500;
	document.getElementById('biPay800Amt').value = amtBiPay800;
	document.getElementById('biPayTotAmt').value = amtBiPayTot;

	document.getElementById('boninSum200Amt').value = amtBoninSum200;
	document.getElementById('boninSum500Amt').value = amtBoninSum500;
	document.getElementById('boninSum800Amt').value = amtBoninSum800;
	document.getElementById('boninSumTotAmt').value = amtBoninSumTot;

	document.getElementById('amtSurAmount').value = amtSurplus;

	document.getElementById('totAmount').innerHTML = '총수가계 ( <font color="#0000ff">'+__commaSet(amtSugubTot + amtBiPayTot) + '</font> ) = 수급계 ( <font color="#0000ff">' + __commaSet(amtSugubTot) + '</font> ) + 비급여 ( <font color="#0000ff">' + __commaSet(amtBiPayTot) + '</font> )';
}

// 시간계산을 위한...
function _getTimeValue(pHour, pMin){
	var checkTemp = pHour;
	var checkMin  = pMin;

	if (checkTemp.substring(0,1) == '0'){
		checkTemp = checkTemp.substring(1,2);
	}

	if (checkMin.substring(0,1) == '0'){
		checkMin = checkMin.substring(1,2);
	}
	
	return parseInt(checkTemp) * 60 + parseInt(checkMin);
}

function _iljungCal(){
	if (!getDateYN()){
		alert('과거의 일정은 계산하실 수 없습니다.');
		return;
	}

	_addYoySudangList();

	document.f.pressCal.value = 'Y';
}

function _iljungSubmit(){
	if (!getDateYN()){
		alert('과거의 일정은 저장하실 수 없습니다.');
		return;
	}

	if (document.f.pressCal.value == 'N'){
		alert('계산처리 후 저장하여 주십시오.');
		return;
	}

	document.f.submit();
}

// 일정 수정
function _iljungModify(){
	if (!getDateYN()){
		alert('과거의 일정은 저장하실 수 없습니다.');
		return;
	}

	if (!confirm('일정을 변경하시겠습니까?')){
		return;
	}

	document.f.submit();
}

function _iljungTest(){
	document.f.action = 'su_test.php';
	document.f.submit();
}

function _iljungCopy(){
	if (!getDateYN()){
		alert('과거의 일정은 복사하실 수 없습니다.');
		return;
	}
	bodyLayer.style.width  = document.body.offsetWidth;

	if (document.body.scrollHeight > document.body.offsetHeight){
		bodyLayer.style.height = document.body.scrollHeight;
	}else{
		bodyLayer.style.height = document.body.offsetHeight;
	}
	
	var tableLeft = (parseInt(__replace(bodyLayer.style.width, 'px', '')) - parseInt(__replace(iljungCopyTable.style.width, 'px', ''))) / 2+'px';
	var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(iljungCopyTable.style.height, 'px', ''))) / 2+'px';

	tableLayer.style.top = tableTop;
	tableLayer.style.left = tableLeft;
	tableLayer.style.width = iljungCopyTable.style.width;
	tableLayer.style.height = iljungCopyTable.style.height;
	tableLayer.style.display = '';
	iljungCopyTable.style.display = '';
}

function _iljungCopyCancel(){
	bodyLayer.style.width = 0;
	bodyLayer.style.height = 0;
	tableLayer.style.width = 0;
	tableLayer.style.height = 0;
	iljungCopyTable.style.display = 'none';
}

function _iljungCopyExec(){
	var now = new Date();
	var nYear = now.getFullYear();
	var nMonth = now.getMonth()+1;
		nMonth = (nMonth < 10 ? '0' : '') + nMonth;
	var nDate = nYear + nMonth;
	var nowYear = document.f.calYear.value;
	var nowMonth = document.f.calMonth.value;
	var nowDate = document.f.calYear.options[document.f.calYear.selectedIndex].text+document.f.calMonth.options[document.f.calMonth.selectedIndex].text;
	var copyYear = document.f.copyYear.value;
	var copyMonth = document.f.copyMonth.value;
	var mCode = document.f.mCode.value;
	var mKind = document.f.mKind.value;
	var mJuminNo = document.f.mJuminNo.value;
	var cDate = copyYear+copyMonth;
	var mDate = nowYear+nowMonth;

	if (mDate < nDate){
		alert('복사할 일정은 현재일보다 과거일 수는 없습니다. 확인하여 주십시오.');
		_iljungCopyCancel();
		return;
	}

	if (mDate < cDate){
		alert('복사할 일정은 대상일정보다 과거일 수는 없습니다. 확인하여 주십시오.');
		_iljungCopyCancel();
		return;
	}

	var request = getHttpRequest('../inc/_check.php?gubun=checkIljungCopy&mCode='+mCode+'&mKind='+mKind+'&mJuminNo='+mJuminNo+'&mDate='+mDate);

	if (request != 'Y'){
		alert('등록되어 있는 '+nowDate+'의 일정이 있습니다.\n일정이 있을 시 일정복사를 실행할 수 없습니다.\n확인하여 주십시오.');
		_iljungCopyCancel();
		return;
	}

	if (!confirm('선택하신 '+document.f.copyYear.options[document.f.copyYear.selectedIndex].text+' '+document.f.copyMonth.options[document.f.copyMonth.selectedIndex].text+'의 일정을 '+nowDate+'의 일정으로 복사를 실행하시겠습니까?')){
		return;
	}

	document.f.action = 'su_iljung_copy_ok.php';
	document.f.submit();
}

function _iljungDelete(){
	if (!getDateYN()){
		alert('과거의 일정은 삭제하실 수 없습니다.');
		return;
	}

	var now = new Date();
	var nYear		= now.getFullYear();
	var nMonth		= now.getMonth()+1;
		nMonth		= (nMonth < 10 ? '0' : '') + nMonth;
	var nDay		= now.getDate();
	var nDate		= nYear + nMonth;
	var nowYear		= document.f.calYear.value;
	var nowMonth	= document.f.calMonth.value;
	var nowDate		= document.f.calYear.options[document.f.calYear.selectedIndex].text+document.f.calMonth.options[document.f.calMonth.selectedIndex].text;
	var mCode		= document.f.mCode.value;
	var mKind		= document.f.mKind.value;
	var mJuminNo	= document.f.mJuminNo.value;
	var mDate		= nowYear+nowMonth;

	if (mDate < nDate){
		alert('과거 데이타는 삭제할 수 없습니다. 확인하여 주십시오.');
		return;
	}

	var request = getHttpRequest('../inc/_check.php?gubun=checkIljungDelete&mCode='+mCode+'&mKind='+mKind+'&mJuminNo='+mJuminNo+'&mDate='+mDate);

	if (request == 'Y'){
		alert(nowDate+'의 일정 중 진행된 일정이 있으므로 삭제할 수 없습니다. 확인하여 주십시오.');
		return;
	}

	if (!confirm('삭제 후 복구가 불가능합니다.\n'+nowDate+'의 일정을 삭제하시겠습니까?')){
		return;
	}

	document.f.action = 'su_iljung_month_delete_ok.php';
	document.f.submit();
}

function getDateYN(){
	var curDate = document.f.calYear.value + document.f.calMonth.value;
	var now = new Date();
	var nowYear = now.getFullYear();
	var nowMonth = now.getMonth() + 1;
		nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	var nowDate = nowYear + nowMonth;

	if (curDate < nowDate){
		return false;
	}

	return true;
}

// 시간의 차이를 계산후 리턴
function _getTimeDiff(timeS, timeE){
	var fHTime = timeS.substring(0,2);
	var fMTime = timeS.substring(2,4);
	var tHTime = timeE.substring(0,2);
	var tMTime = timeE.substring(2,4);

	if (fHTime.substring(0,1) == '0') fHTime = fHTime.substring(1,fHTime.length);
	if (fMTime.substring(0,1) == '0') fMTime = fMTime.substring(1,fMTime.length);

	if (tHTime.substring(0,1) == '0') tHTime = tHTime.substring(1,tHTime.length);
	if (tMTime.substring(0,1) == '0') tMTime = tMTime.substring(1,tMTime.length);

	if (parseInt(fHTime) > parseInt(tHTime)){
		tHTime = parseInt(tHTime) + 24;
	}

	var fTime = parseInt(fHTime) * 60 + parseInt(fMTime);
	var tTime = parseInt(tHTime) * 60 + parseInt(tMTime);
	
	return tTime - fTime;
}

// 일별일정조회
function _getCalendar(pBody, pCode, pKind, pYear, pMonth){
	var URL = 'day_calendar.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:pCode,
				mKind:pKind,
				mYear:pYear,
				mMonth:pMonth
			},
			onSuccess:function (responseHttpObj) {
				pBody.innerHTML = responseHttpObj.responseText;

				if (pYear == ''){
					_getWorkList(myBody, pCode, pYear, '');
				}
			}
		}
	);
}

// 일별일정 상세조회
function _getWorkList(myBody, pCode, pKind, pDate){
	var URL = 'day_work_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:pCode,
				mKind:pKind,
				mDate:pDate
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 일별일정 프린트
function _printDayWork(pCode, pKind, pDate, pService){
	var width  = 800;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popup_iljung = window.open('day_work_print.php?mCode='+pCode+'&mKind='+pKind+'&mDate='+pDate+'&mService='+pService, 'POPUP_DAY_WORK', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 현월일정내역
function _nowMonthDiaryList(myBody, myDetail, mCode, mKind, mYear, mMonth){
	var URL = 'month_list.php';
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
				myDetail.innerHTML = '';
				myBody.innerHTML = responseHttpObj.responseText;
				
				if (document.getElementById('rngDate').value.length > 5){
					dateRng.innerHTML = '('+document.getElementById('rngDate').value+')';
				}
			}
		}
	);
}

// 현월일정 상세내역
function _nowMonthDiaryDetail(myBody, mCode, mKind, mDate, mSugupja){
	var URL = 'month_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mDate:mDate,
				mSugupja:mSugupja
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 패턴등록
function patternList(p_code, p_kind){
	sugupjaList(mySugupja, p_code, p_kind);
	sugupjaPattern(myList, p_code, p_kind, '', '', '');
}

// 수급자리스트
function sugupjaList(p_body, p_code, p_kind){
	var URL = 'sugupja_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급자 패턴 리스트
function sugupjaPattern(p_body, p_code, p_kind, p_sugupja, p_key, p_index){
	var URL = 'pattern_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mSugupja:p_sugupja,
				mKey:p_key
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;

				try{
					var count = parseInt(document.getElementById('sugupjaCount').value);
				}catch(e){
					var count = 0;
				}

				for(var i=0; i<count; i++){
					//document.getElementById('sugupja_'+i).style.textDecoration = '';
					document.getElementById('sugupja_'+i).style.backgroundColor = "#ffffff";
				}

				try{
					//document.getElementById('sugupja_'+p_index).style.textDecoration = 'underline';
					document.getElementById('sugupja_'+p_index).style.backgroundColor = "#eeeeee";
				}catch(e){}
			}
		}
	);
}

// 수급자 패턴 등록
function patternReg(p_code, p_kind, p_sugupja, p_key, p_seq){
	var modal = window.open('su_add_iljung.php?mCode='+p_code+'&mKind='+p_kind+'&mKey='+p_key+'&mSeq='+p_seq+'&mMode=PATTERN');
	//var modal = showModalDialog('su_add_iljung.php?mCode='+p_code+'&mKind='+p_kind+'&mKey='+p_key+'&mSeq='+p_seq+'&mMode=PATTERN', window, 'dialogWidth:900px; dialogHeight:195px; dialogHide:yes; scroll:yes; status:no');
}

// 수그밪 패턴 저장
function patternRegOk(){
	document.f.action = "pattern_ok.php";
	document.f.submit();
}

// 히스토리 저장
function _setPattern(){
	var URL = 'pattern_ok.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				p_code:document.f.mCode.value,
				p_kind:document.f.mKind.value,
				p_jumin:document.f.mJuminNo.value,
				p_svc_subcode:_getSvcSubCode(),
				p_svc_subcd:document.f.svcSubCD.value,
				p_car_no:document.f.carNo.value,
				p_sugup_fmtime:document.f.ftHour.value+document.f.ftMin.value,
				p_sugup_totime:document.f.ttHour.value+document.f.ttMin.value,
				p_sugup_soyotime:document.f.procTime.value,
				p_family_gbn:document.f.togeUmu.checked?"Y":"N",
				p_bipay_gbn:document.f.bipayUmu.checked?"Y":"N",
				p_week_day1:document.f.weekDay1.checked?"Y":"N",
				p_week_day2:document.f.weekDay2.checked?"Y":"N",
				p_week_day3:document.f.weekDay3.checked?"Y":"N",
				p_week_day4:document.f.weekDay4.checked?"Y":"N",
				p_week_day5:document.f.weekDay5.checked?"Y":"N",
				p_week_day6:document.f.weekDay6.checked?"Y":"N",
				p_week_day0:document.f.weekDay0.checked?"Y":"N",
				p_week_use1:document.f.weekDay1.disabled?"N":"Y",
				p_week_use2:document.f.weekDay2.disabled?"N":"Y",
				p_week_use3:document.f.weekDay3.disabled?"N":"Y",
				p_week_use4:document.f.weekDay4.disabled?"N":"Y",
				p_week_use5:document.f.weekDay5.disabled?"N":"Y",
				p_week_use6:document.f.weekDay6.disabled?"N":"Y",
				p_week_use0:document.f.weekDay0.disabled?"N":"Y",
				p_yoy_jumin1:document.f.yoy1.value,
				p_yoy_jumin2:document.f.yoy2.value,
				p_yoy_jumin3:document.f.yoy3.value,
				p_yoy_jumin4:document.f.yoy4.value,
				p_yoy_jumin5:document.f.yoy5.value,
				p_yoy_name1:document.f.yoyNm1.value,
				p_yoy_name2:document.f.yoyNm2.value,
				p_yoy_name3:document.f.yoyNm3.value,
				p_yoy_name4:document.f.yoyNm4.value,
				p_yoy_name5:document.f.yoyNm5.value,
				p_yoy_ta1:document.f.yoyTA1.value,
				p_yoy_ta2:document.f.yoyTA2.value,
				p_yoy_ta3:document.f.yoyTA3.value,
				p_yoy_ta4:document.f.yoyTA4.value,
				p_yoy_ta5:document.f.yoyTA5.value,
				p_visit_chk:document.f.visitSudangCheck.checked?"Y":"N",
				p_visit_amt:__commaUnset(document.f.visitSudang.value),
				p_sudang_yul1:document.f.sudangYul1.value,
				p_sudang_yul2:document.f.sudangYul2.value,
				p_price_s:__commaUnset(document.f.sPrice.value),
				p_price_e:__commaUnset(document.f.ePrice.value),
				p_price_n:__commaUnset(document.f.nPrice.value),
				p_price_t:__commaUnset(document.f.tPrice.value),
				p_suga_code:document.f.sugaCode.value,
				p_suga_name:document.f.sugaName.value,
				p_gubun_e:document.f.Egubun.value,
				p_gubun_n:document.f.Ngubun.value,
				p_time_e:document.f.Etime.value,
				p_time_n:document.f.Ntime.value
			},
			onSuccess:function (responseHttpObj) {
				return; //responseHttpObj.responseText;
			}
		}
	);
}

// 패턴리스트
function _patternList(p_body, p_code, p_kind, p_jumin, p_year, p_month){
	var URL = 'pattern_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mJumin:p_jumin,
				mYear:p_year,
				mMonth:p_month
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 패턴리스트 닫기
function _patternClose(p_body){
	p_body.style.display = "none";
}

// 패턴입력
function _patternInput(p_body, p_index){
	switch(document.getElementsByName("p_svc_subcode[]")[p_index].value){
	case "200":
		document.f.svcSubCode[0].checked = true;
		//_setSvc1();
		break;
	case "500":
		document.f.svcSubCode[1].checked = true;
		//_setSvc2();
		break;
	case "800":
		document.f.svcSubCode[2].checked = true;
		//_setSvc3();
		break;
	}

	document.f.yoy1.value = document.getElementsByName("p_yoy_jumin1[]")[p_index].value;
	document.f.yoy2.value = document.getElementsByName("p_yoy_jumin2[]")[p_index].value;
	document.f.yoy3.value = document.getElementsByName("p_yoy_jumin3[]")[p_index].value;
	document.f.yoy4.value = document.getElementsByName("p_yoy_jumin4[]")[p_index].value;
	document.f.yoy5.value = document.getElementsByName("p_yoy_jumin5[]")[p_index].value;

	document.f.yoyNm1.value = document.getElementsByName("p_yoy_name1[]")[p_index].value;
	document.f.yoyNm2.value = document.getElementsByName("p_yoy_name2[]")[p_index].value;
	document.f.yoyNm3.value = document.getElementsByName("p_yoy_name3[]")[p_index].value;
	document.f.yoyNm4.value = document.getElementsByName("p_yoy_name4[]")[p_index].value;
	document.f.yoyNm5.value = document.getElementsByName("p_yoy_name5[]")[p_index].value;

	document.f.yoyTA1.value = document.getElementsByName("p_yoy_ta1[]")[p_index].value;
	document.f.yoyTA2.value = document.getElementsByName("p_yoy_ta2[]")[p_index].value;
	document.f.yoyTA3.value = document.getElementsByName("p_yoy_ta3[]")[p_index].value;
	document.f.yoyTA4.value = document.getElementsByName("p_yoy_ta4[]")[p_index].value;
	document.f.yoyTA5.value = document.getElementsByName("p_yoy_ta5[]")[p_index].value;
	
	switch(document.getElementsByName("p_svc_subcode[]")[p_index].value){
	case "200":
		_setSvc1();
		break;
	case "500":
		_setSvc2();
		break;
	case "800":
		_setSvc3();
		break;
	}

	document.f.ftHour.value = document.getElementsByName("p_sugup_fmtime[]")[p_index].value.substring(0,2);
	document.f.ftMin.value = document.getElementsByName("p_sugup_fmtime[]")[p_index].value.substring(2,4);

	document.f.ttHour.value = document.getElementsByName("p_sugup_totime[]")[p_index].value.substring(0,2);
	document.f.ttMin.value = document.getElementsByName("p_sugup_totime[]")[p_index].value.substring(2,4);

	/*
	switch(document.getElementsByName("p_svc_subcode[]")[p_index].value){
	case "200":
		_setSvc1();
		break;
	case "500":
		_setSvc2();
		break;
	case "800":
		_setSvc3();
		break;
	}
	*/

	document.f.svcSubCD.value = document.getElementsByName("p_svc_subcd[]")[p_index].value;
	document.f.carNo.value = document.getElementsByName("p_car_no[]")[p_index].value;
	
	document.f.procTime.value = document.getElementsByName("p_sugup_soyotime[]")[p_index].value;
	
	document.f.togeUmu.checked = (document.getElementsByName("p_family_gbn[]")[p_index].value == "Y" ? true : false);
	document.f.bipayUmu.checked = (document.getElementsByName("p_bipay_gbn[]")[p_index].value == "Y" ? true : false);
	
	document.f.weekDay1.checked = (document.getElementsByName("p_week_day1[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay2.checked = (document.getElementsByName("p_week_day2[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay3.checked = (document.getElementsByName("p_week_day3[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay4.checked = (document.getElementsByName("p_week_day4[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay5.checked = (document.getElementsByName("p_week_day5[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay6.checked = (document.getElementsByName("p_week_day6[]")[p_index].value == "Y" ? true : false); 
	document.f.weekDay0.checked = (document.getElementsByName("p_week_day0[]")[p_index].value == "Y" ? true : false); 

	document.f.weekDay1.disabled = (document.getElementsByName("p_week_use1[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay2.disabled = (document.getElementsByName("p_week_use2[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay3.disabled = (document.getElementsByName("p_week_use3[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay4.disabled = (document.getElementsByName("p_week_use4[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay5.disabled = (document.getElementsByName("p_week_use5[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay6.disabled = (document.getElementsByName("p_week_use6[]")[p_index].value == "N" ? true : false); 
	document.f.weekDay0.disabled = (document.getElementsByName("p_week_use0[]")[p_index].value == "N" ? true : false); 

	document.f.visitSudangCheck.checked = (document.getElementsByName("p_visit_chk[]")[p_index].value == "Y" ? true : false);

	document.f.sudangYul1.value = document.getElementsByName("p_sudang_yul1[]")[p_index].value;
	document.f.sudangYul2.value = document.getElementsByName("p_sudang_yul2[]")[p_index].value;

	document.f.sPrice.value = __commaSet(document.getElementsByName("p_price_s[]")[p_index].value);
	document.f.ePrice.value = __commaSet(document.getElementsByName("p_price_e[]")[p_index].value);
	document.f.nPrice.value = __commaSet(document.getElementsByName("p_price_n[]")[p_index].value);
	document.f.tPrice.value = __commaSet(document.getElementsByName("p_price_t[]")[p_index].value);

	document.f.sugaCode.value = document.getElementsByName("p_suga_code[]")[p_index].value;
	document.f.sugaName.value = document.getElementsByName("p_suga_name[]")[p_index].value;
	
	document.f.Egubun.value = document.getElementsByName("p_gubun_e[]")[p_index].value;
	document.f.Ngubun.value = document.getElementsByName("p_gubun_n[]")[p_index].value;

	document.f.Etime.value = document.getElementsByName("p_time_e[]")[p_index].value;
	document.f.Ntime.value = document.getElementsByName("p_time_n[]")[p_index].value;

	_patternClose(p_body);
}

// 일정출력
function _printIljung(){
	spanYear.innerHTML = document.getElementById('calYear').value+'년';
	spanMonth.innerHTML = document.getElementById('calMonth').value+'월';

	window.onbeforeprint = hideDivs;
	window.onafterprint = showDivs;
	window.print();
}

function showDivs(){
	yoy_const.style.display = '';
	su_const.style.display = '';

	spanYear.style.display = 'none';
	spanMonth.style.display = 'none';
	spanIcon.style.display = '';

	document.getElementById('calYear').style.display = '';
	document.getElementById('calMonth').style.display = '';
	document.getElementById('btnIljungPrint').style.display = '';
}
function hideDivs(){
	yoy_const.style.display = 'none';
	su_const.style.display = 'none';

	spanYear.style.display = '';
	spanMonth.style.display = '';
	spanIcon.style.display = 'none';

	document.getElementById('calYear').style.display = 'none';
	document.getElementById('calMonth').style.display = 'none';
	document.getElementById('btnIljungPrint').style.display = 'none';
}

function _setBathRate(gubun){
	var target1 = document.getElementById('sudangYul1');
	var target2 = document.getElementById('sudangYul2');
	var rate1   = parseFloat(target1.value);
	var rate2   = parseFloat(target2.value);

	if (rate1 < 0 || rate1 > 100 || rate2 < 0 || rate2 > 100){
		alert('비율은 0부터 100까지 입력가능합니다. 확인하여 주십시오.');
		target1.value = target1.tag;
		target2.value = target2.tag;

		if (gubun == '1'){
			target1.focus();
			target1.select();
		}else{
			target2.focus();
			target2.select();
		}

		return false;
	}
	
	if (gubun == '1'){
		target2.value = 100 - rate1;
	}else{
		target1.value = 100 - rate2;
	}

	return true;
}
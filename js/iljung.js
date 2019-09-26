var popup_iljung = null;
var bathTimes = 60; //목욕기본시간(분)
var __BATH_SUGA_RATE__ = 80; //목욕 시간차의 적용 비율
var nursingTimes = 29; //간호기본시간(분)
var nursingTimeList = new Array({'cd':'01', 'nm':'30분미만'},{'cd':'30', 'nm':'60분미만'},{'cd':'60', 'nm':'60분이상'}); //간호 시간리스트
var __CHECK_ADD_TIME__ = 120; //전일정과 비교할 시간간격
var __FAMILY_SUGA_CD__ = 'CCWC'; //동거수가코드
var __CARE_SUGA_CD__ = 'CC';
var bathSugaCD = 'CB'; //목욕수가코드
var __VOU_BABY_TO_TIME1__ = '1700';
var __VOU_BABY_TO_TIME2__ = '1300';

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
function _setSugupjaReg(mCode, mKind, mKey, mYear, mMonth, mNew){
	//var modal = showModalDialog('su_reg.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey, window, 'dialogWidth:940px; dialogHeight:768px; dialogHide:yes; scroll:yes; status:no; help:no;');
	//window.open('su_reg.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'SugupjaReg');

	var sh     = screen.height - 50;
	var width  = 960;
	var height = (sh > 800 ? sh : 800);
	var left   = (window.screen.width  - width)  / 2;
	var top    = (window.screen.height - height) / 2;
	var url    = 'su_reg.php';

	if (mNew)
		url = 'iljung_reg.php';
	
	var popupIljung = window.open(url+'?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&calYear='+mYear+'&calMonth='+mMonth, 'POPUP_DIRAY', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
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
	//temp_title.innerHTML = '소요시간';
	//labelYoy.style.display = '';
	//objSvcSubCD.style.display = 'none';
	
	document.getElementById('yoyNm2').value = '';
	document.getElementById('yoy2').value = '';
	document.getElementById('yoyTA2').value = '';

	document.getElementById('yoyNm2').style.display = 'none';
	document.getElementById('delete_yoy2').style.display = 'none';

	document.getElementById('svcSubCD').disabled = true;
	document.getElementById('carNo').disabled = true;

	document.f.togeUmu.disabled = false;

	document.f.visitSudangCheck.checked = false;
	document.f.visitSudangCheck.disabled = true;
	document.f.visitSudang.disabled = true;
	document.f.visitSudang.style.backgroundColor = '#eeeeee';
	document.f.sudangYul1.disabled = true;
	document.f.sudangYul1.style.backgroundColor = '#eeeeee';
	document.f.sudangYul2.disabled = true;
	document.f.sudangYul2.style.backgroundColor = '#eeeeee';

	//objCarList.style.display = 'none';

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
	//temp_title.innerHTML = '차량여부';
	//labelYoy.style.display = 'none';
	
	document.f.togeUmu.disabled = true;
	document.f.togeUmu.checked = false;

	document.getElementById('yoyNm2').style.display = '';
	document.getElementById('delete_yoy2').style.display = '';

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

	//var temp_body = document.getElementById('temp_body');
	//var bodyTop   = __getObjectTop(temp_body);
	//var bodyLeft  = __getObjectLeft(temp_body);

	//objSvcSubCD.style.top  = bodyTop;
	//objSvcSubCD.style.left = bodyLeft - 1;
	//objSvcSubCD.style.display = '';

	//objCarList.style.top  = bodyTop;
	//objCarList.style.left = bodyLeft + objSvcSubCD.offsetWidth - 2;
	//objCarList.style.display = '';

	document.getElementById('svcSubCD').disabled = false;
	document.getElementById('carNo').disabled = false;

	var select = null;
	
	select = document.getElementById("procTime");
	select.innerHTML = '';

	_setSelectBox(select, 'K', '차량');
	_setSelectBox(select, 'F', '미차량');
	_checkTimeH();
	_setIljungSuga();
	
	/*
	var newToTime = getTimeValue(document.f.ftHour.value+document.f.ftMin.value, bathTimes);

	document.f.ttHour.readOnly = false;
	document.f.ttMin.readOnly  = false;
	document.f.ttHour.style.backgroundColor = '#ffffff';
	document.f.ttMin.style.backgroundColor  = '#ffffff';
	document.f.ttHour.value = newToTime[0];
	document.f.ttMin.value  = newToTime[1];
	document.f.ttHour.onfocus = function(){document.f.ttHour.select();}
	document.f.ttMin.onfocus  = function(){document.f.ttMin.select();}
	*/
}

function _setSvc2Sub(){
	var select = null;

	//document.f.ttHour.value = document.f.ftHour.value;
	//document.f.ttMin.value  = document.f.ftMin.value;

	select = document.getElementById("svcSubCD");

	if (document.f.procTime.value == 'K'){
		//objCarList.style.display = '';
		document.getElementById('carNo').disabled = false;

		if (select.options[0].text == '차량입욕'){
			return;
		}
	}

	if (document.f.procTime.value == 'F'){
		//objCarList.style.display = 'none';
		document.getElementById('carNo').disabled = true;
	}

	var mode = _getMode();

	select.innerHTML = '';

	if (document.f.procTime.value == 'K'){
		_setSelectBox(select, '1', '차량입욕');
		_setSelectBox(select, '2', '가정내입욕');
	}else{
		//_setSelectBox(select, '1', '가정내입욕');
		//_setSelectBox(select, '2', '미입욕');

		// 2011년 7월 1일 부터 방문목욕이 바뀜
		if (mode.value == 'IN'){
			var dt = document.f.calYear.value+document.f.calMonth.value;
		}else{
			var dt = document.f.addDate.value;
		}
		dt = dt.substring(0, 6);
		
		if (dt < '201107'){
			_setSelectBox(select, '1', '가정내입욕');
			_setSelectBox(select, '2', '미입욕');
		}else{
			_setSelectBox(select, '1', '목욕');
		}
	}
	_setEndTimeSub();
}

// 간호설정
function _setSvc3(){
	//temp_title.innerHTML = '소요시간';
	//labelYoy.style.display = '';
	//objSvcSubCD.style.display = 'none';
	
	document.getElementById('yoyNm2').value = '';
	document.getElementById('yoy2').value = '';
	document.getElementById('yoyTA2').value = '';

	document.getElementById('yoyNm2').style.display = 'none';
	document.getElementById('delete_yoy2').style.display = 'none';

	document.getElementById('svcSubCD').disabled = true;
	document.getElementById('carNo').disabled = true;
	
	document.f.togeUmu.disabled = true;
	document.f.togeUmu.checked = false;

	document.f.visitSudangCheck.checked = true;
	document.f.visitSudangCheck.disabled = false;
	document.f.visitSudang.disabled = false;
	document.f.visitSudang.style.backgroundColor = '#ffffff';
	document.f.sudangYul1.disabled = true;
	document.f.sudangYul1.style.backgroundColor = '#eeeeee';
	//document.f.sudangYul1.value = "100.00";
	document.f.sudangYul2.disabled = true;
	document.f.sudangYul2.style.backgroundColor = '#eeeeee';
	//document.f.sudangYul2.value = "0.00";

	//objCarList.style.display = 'none';

	var select = null;
	
	select = document.getElementById("procTime");
	select.innerHTML = '';

	/*
	_setSelectBox(select, '29', '30분미만');
	_setSelectBox(select, '59', '30분');
	_setSelectBox(select, '89', '60분이상');
	*/
	_setSelectBox(select, nursingTimeList[0]['cd'], nursingTimeList[0]['nm']);
	_setSelectBox(select, nursingTimeList[1]['cd'], nursingTimeList[1]['nm']);
	_setSelectBox(select, nursingTimeList[2]['cd'], nursingTimeList[2]['nm']);

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
	var svcSubCode = _getSvcSubCode();

	if (checked){
		document.f.visitSudang.disabled = false;
		document.f.visitSudang.style.backgroundColor = '#ffffff';

		if (svcSubCode == '500'){
			document.f.sudangYul1.disabled = false;
			document.f.sudangYul1.style.backgroundColor = '#ffffff';
			document.f.sudangYul2.disabled = false;
			document.f.sudangYul2.style.backgroundColor = '#ffffff';
		}else{
			document.f.sudangYul1.disabled = true;
			document.f.sudangYul1.style.backgroundColor = '#eeeeee';
			document.f.sudangYul2.disabled = true;
			document.f.sudangYul2.style.backgroundColor = '#eeeeee';
		}
	}else{
		document.f.visitSudang.disabled = true;
		document.f.visitSudang.style.backgroundColor = '#eeeeee';
		document.f.sudangYul1.disabled = true;
		document.f.sudangYul1.style.backgroundColor = '#eeeeee';
		document.f.sudangYul2.disabled = true;
		document.f.sudangYul2.style.backgroundColor = '#eeeeee';
	}
}

function _chk_family_bipay(obj){
	var f = document.f;

	switch(obj.name){
		case 'togeUmu':
			f.bipayUmu.checked = false;
			break;

		case 'bipayUmu':
			f.togeUmu.checked = false;
			break;
	}
}

// 소요시간 설정
function _setNeedTime(){
	var f = document.f;
	var newValue = false;
	var yoyCount = _getYoySetCount();
	var msg_type = 1;

	if (yoyCount > 1){
		msg_type = 1;
		if (f.procTime.options[f.procTime.selectedIndex].index > 2) newValue = true;
	}

	if (f.togeUmu.checked){
		if (f.procTime.options[f.procTime.selectedIndex].index > 2){
			msg_type = 2;
		}else{
			msg_type = 0;
		}
		newValue = true;
	}

	var selectedIndex = 0;
	for(var i=0; i<f.procTime.options.length; i++){
		if (f.procTime.options[i].selected){
			selectedIndex = i;
			break;
		}
	}

	var svcSubCode = _getSvcSubCode();
	var mode = _getMode();

	if (svcSubCode == '200'){
		select = document.getElementById("procTime");
		select.innerHTML = '';

		if (newValue){
			_setSelectBox(select, '30', '30분');
			_setSelectBox(select, '60', '60분');

			// 2011년 8월 1일 부터 동거가족 수가가 바뀜
			var family_min = f.family_min.value;

			if (mode.value == 'IN'){
				var dt = f.calYear.value+f.calMonth.value;
			}else{
				var dt = f.addDate.value;
			}
			dt = dt.substring(0, 6);
			
			// 동거가족
			if (dt < '201108' || family_min != 60){
				_setSelectBox(select, '90', '90분');
			}
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
		}else if (msg_type == 2){
			alert('동거가족 선택시 소요시간이 90분을 초과할 수 없습니다. 소요시간을 90분으로 설정합니다.');
		}

		if (selectedIndex >= f.procTime.options.length) selectedIndex = f.procTime.options.length - 1;

		f.procTime.options[selectedIndex].selected = true;
		_setEndTime();
	}else{
		f.procTime.options[selectedIndex].selected = true;
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
	try{
		var svc_id = _get_current_svc('id');
	}catch(e){
		var svc_id = '11';
	}

	//재가인 경우만 동거여부를 확인한다.
	if (svc_id == '11'){
		var family_yn = document.f.togeUmu.checked ? 'Y' : 'N';
	}else{
		var family_yn = 'N';
	}

	var svcSubCode = _getSvcSubCode();
	var yoy1 = document.f.yoy1.value;

	if (svc_id == '11'){
		var yoy2 = document.f.yoy2.value;
	}else{
		var yoy2 = '';
	}

	var yoy = '';

	if (yoy1 != '') yoy += ",'"+yoy1+"'";
	if (yoy2 != '') yoy += ",'"+yoy2+"'";
	
	if (yoy != ''){
		yoy = yoy.substring(1, yoy.length);
	}
	
	if (svc_id == '11'){
		if (!_newType()){
			var date = document.f.addDate.value != '' ? document.f.addDate.value : document.f.calYear.value + document.f.calMonth.value;
		}else{
			var mode = _getMode();

			if (mode.value == 'IN'){
				var date = document.f.calYear.value + document.f.calMonth.value;
			}else{
				var date = document.f.addDate.value;
			}
		}
	}

	var fromTime = document.f.ftHour.value + document.f.ftMin.value; //일정시작시간
	var toTime   = document.f.ttHour.value + document.f.ttMin.value; //일정종료시간
	var help     = showModalDialog('../inc/_help.php?r_gubun=yoyFind&mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&yoy='+yoy+'&mDate='+date+'&mFromTime='+fromTime+'&mToTime='+toTime+'&family_yn='+family_yn+'&svcSubCode='+svcSubCode, window, 'dialogWidth:200px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	
	if (help == undefined){
		return;
	}

	var index = '';

	if (yoyCode.value == ''){
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

	//재가인경우만 실행
	if (svc_id == '11'){
		if (help[4] == 'Y'){
			document.f.family_min.value = document.f.family_min.tag;
			document.f.family_cnt.value = document.f.family_cnt.tag;
		}else{
			document.f.family_min.value = document.f.back_family_min.value;
			document.f.family_cnt.value = document.f.back_family_cnt.value;
		}

		_setNeedTime();
		_setIljungSuga();
	}else{
		document.f.ftHour.focus();
	}
}

function _helpSuYoyPA(mCode, mKind, mKey, yoyCode, yoyName, yoyTA){
	try{
		var svc_id = _get_current_svc('id');
	}catch(e){
		var svc_id = '11';
	}
	
	//재가인 경우만 동거여부를 확인한다.
	if (svc_id == '11'){
		var family_yn = document.f.togeUmu.checked ? 'Y' : 'N';
	}else{
		var family_yn = 'N';
	}

	var svcSubCode = _getSvcSubCode();
	var yoy1 = document.f.yoy1.value;

	if (svc_id == '11'){
		var yoy2 = document.f.yoy2.value;
	}else{
		var yoy2 = '';
	}

	var yoy = '';

	if (yoy1 != '') yoy += ","+yoy1;
	if (yoy2 != '') yoy += ","+yoy2;
	
	if (yoy != ''){
		yoy = yoy.substring(1, yoy.length);
	}
	
	if (svc_id == '11'){
		if (!_newType()){
			var date = document.f.addDate.value != '' ? document.f.addDate.value : document.f.calYear.value + document.f.calMonth.value;
		}else{
			var mode = _getMode();

			if (mode.value == 'IN'){
				var date = document.f.calYear.value + document.f.calMonth.value;
			}else{
				var date = document.f.addDate.value;	
			}
		}	
	}else {
		var mode = _getMode();

		if (mode.value == 'IN'){
			var date = document.f.calYear.value + document.f.calMonth.value;
		}else{
			var date = document.f.addDate.value;	
		}
	}
	
	var fromTime = document.f.ftHour.value + document.f.ftMin.value; //일정시작시간
	var toTime   = document.f.ttHour.value + document.f.ttMin.value; //일정종료시간
	var help     = showModalDialog('../inc/_find_person.php?type=yoyangsa&code='+mCode+'&kind='+mKind+'&mKey='+mKey+'&yoy='+yoy+'&mDate='+date+'&mFromTime='+fromTime+'&mToTime='+toTime+'&family_yn='+family_yn+'&svcSubCode='+svcSubCode, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no');
	
	if (help == undefined){
		return;
	}

	var index = '';

	if (yoyCode.value == ''){
		if (yoy2 == '') index = '2';
		if (yoy1 == '') index = '1';
	}
	
	if (index == ''){
		yoyCode.value = help[0];
		yoyName.value = help[1];
		yoyTA.value   = help[8];
	}else{
		eval('document.f.yoy'+index).value   = help[0];
		eval('document.f.yoyNm'+index).value = help[1];
		eval('document.f.yoyTA'+index).value = help[8];
	}
	
	_setWeekDay(mCode, mKind, document.f.yoy1.value);

	//재가인경우만 실행
	if (svc_id == '11'){
		if (help[9] == 'Y'){
			document.f.family_min.value = document.f.family_min.tag;
			document.f.family_cnt.value = document.f.family_cnt.tag;
		}else{
			document.f.family_min.value = document.f.back_family_min.value;
			document.f.family_cnt.value = document.f.back_family_cnt.value;
		}

		_setNeedTime();
		_setIljungSuga();
	}else{
		document.f.ftHour.focus();
	}
}

//요양사별 요일 설정
function _setWeekDay(code, kind, mem_cd){
	var mode = _getMode();

	if (mode.value == 'IN'){
	}else if (mode.value == 'PATTERN'){
	}else{
		return;
	}

	var request = getHttpRequest('../inc/_check.php?gubun=checkYoyWeekDay&mCode='+code+'&mKind='+kind+'&mYoy='+mem_cd);
	var weekvalue = request.split('//');

	document.f.weekDay1.checked = (weekvalue[0] == 'Y' ? true : false);
	document.f.weekDay2.checked = (weekvalue[1] == 'Y' ? true : false);
	document.f.weekDay3.checked = (weekvalue[2] == 'Y' ? true : false);
	document.f.weekDay4.checked = (weekvalue[3] == 'Y' ? true : false);
	document.f.weekDay5.checked = (weekvalue[4] == 'Y' ? true : false);
	document.f.weekDay6.checked = (weekvalue[5] == 'Y' ? true : false);
	document.f.weekDay0.checked = (weekvalue[6] == 'Y' ? true : false);
}

//요양사 선택해제
function _yoyNot(index){
	eval('document.f.yoy'+index).value = '';
	eval('document.f.yoyNm'+index).value = '';
	eval('document.f.yoyTA'+index).value = '';

	for(var i=(parseInt(index)+1); i<=2; i++){
		eval('document.f.yoy'+(i-1)).value = eval('document.f.yoy'+i).value;
		eval('document.f.yoyNm'+(i-1)).value = eval('document.f.yoyNm'+i).value;
		eval('document.f.yoyTA'+(i-1)).value = eval('document.f.yoyTA'+i).value;

		eval('document.f.yoy'+i).value = '';
		eval('document.f.yoyNm'+i).value = '';
		eval('document.f.yoyTA'+i).value = '';
	}

	_setWeekDay($('#code').attr('value'), $('#kind').attr('value'), document.f.yoy1.value);
	_setNeedTime();
	_setIljungSuga();
}

function _getYoySetCount(){
	var yoyCount = 0;

	if (document.f.yoy1.value != '') yoyCount++;
	if (document.f.yoy2.value != '') yoyCount++;
	//if (document.f.yoy3.value != '') yoyCount++;
	//if (document.f.yoy4.value != '') yoyCount++;
	//if (document.f.yoy5.value != '') yoyCount++;

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
		//hour = '00';
		hour = parseInt(hour) - 24;
		hour = (parseInt(hour, 10) < 10 ? '0' : '')+parseInt(hour, 10);
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

		//alert(newToTime[0]+'/'+newToTime[1]);

		document.f.ttHour.value = newToTime[0];
		document.f.ttMin.value  = newToTime[1];
		
		//return;
	}
	_setEndTimeSub();
}

// 종료시간 확인
function _setEndTimeCheck(){
	var svc_id = __object_get_value('svc_id[]');
	
	if (document.f.procTime.value != '0') return true;
	
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
		if (svc_id == 11){
			alert('종료시간을 시작시간보다 300분 이상크게 입력하여 주십시오.');
		}else{
			alert('종료시간 입력오류입니다. 종료시간을 입력하여 주십시오.');
		}
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
	
	//요양은 종료시간을 30분단위로 맞춘다.
	/*
	if (svcSubCode == '200'){
		var gabTime = (tTime - fTime) % 30;

		if (gabTime > 0)
			tTime = tTime - gabTime;
	}
	*/
	
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

	//alert(tHTime+'/'+tMTime);

	document.f.ttHour.value = tHTime;
	document.f.ttMin.value  = tMTime;

	_setIljungSuga();
}

function _setEndTimeSub(){
	var svcSubCode = _getSvcSubCode();

	if (svcSubCode != '500'){
		if (document.f.ftHour.value == '' || document.f.ftMin.value == ''){
			document.f.ttHour.value = '';
			document.f.ttMin.value  = '';
			return;
		}
		
		var fHTime = document.f.ftHour.value;

		if (fHTime.substring(0,1) == '0'){
			fHTime = fHTime.substring(1,fHTime.length);
		}

		fHTime = parseInt(fHTime,10) * 60;

		var fMTime = parseInt(document.f.ftMin.value,10);
		var pTime  = parseInt(document.f.procTime.value,10);

		//alert(fHTime+'/'+fMTime+'/'+pTime);

		if ((svcSubCode == '200' && parseInt(pTime,10) == 0) || (svcSubCode == '800')){
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

			var tempFH = parseInt(tempFmH,10) * 60 + parseInt(tempFmM,10);
			var tempTH = parseInt(tempToH,10) * 60 + parseInt(tempToM,10);

			var tTime = parseInt(tempTH,10) - parseInt(tempFH,10);
			
			if (svcSubCode == '200'){
				if (tTime < 300) tTime = 300;
				tTime += parseInt(tempFH,10)
			}else{
				tTime = parseInt(fHTime,10) + parseInt(fMTime,10) + parseInt(pTime,10);
			}

			//alert('1 : '+tTime);

			document.f.ttHour.readOnly = false;
			document.f.ttMin.readOnly  = false;
			document.f.ttHour.style.backgroundColor = '#ffffff';
			document.f.ttMin.style.backgroundColor  = '#ffffff';
			document.f.ttHour.onfocus = function(){document.f.ttHour.select();}
			document.f.ttMin.onfocus  = function(){document.f.ttMin.select();}

			if (svcSubCode == '800'){
				document.f.ttHour.onchange = function(){
					_setNursingProcTime(this);
				};
				document.f.ttMin.onchange  = function(){
					_setNursingProcTime(this);
				};;
			}else{
				document.f.ttHour.onchange = null;
				document.f.ttMin.onchange  = null;
			}
		}else{
			var tTime = parseInt(fHTime,10) + parseInt(fMTime,10) + parseInt(pTime,10);

			document.f.ttHour.readOnly = true;
			document.f.ttMin.readOnly  = true;
			document.f.ttHour.style.backgroundColor = '#eeeeee';
			document.f.ttMin.style.backgroundColor  = '#eeeeee';
			document.f.ttHour.onfocus  = function(){document.f.procTime.focus();}
			document.f.ttMin.onfocus   = function(){document.f.procTime.focus();}
			document.f.ttHour.onchange = null;
			document.f.ttMin.onchange  = null;
		}

		var tHTime = parseInt(tTime / 60,10);
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
		
		tMTime = (tMTime < 10 ? '0' : '') + tMTime;

		document.f.ttHour.value = tHTime;
		document.f.ttMin.value = tMTime;
	}else{
		var newToTime = getTimeValue(document.f.ftHour.value+document.f.ftMin.value, bathTimes);

		document.f.ttHour.readOnly = false;
		document.f.ttMin.readOnly  = false;
		document.f.ttHour.style.backgroundColor = '#ffffff';
		document.f.ttMin.style.backgroundColor  = '#ffffff';
		document.f.ttHour.value = newToTime[0];
		document.f.ttMin.value  = newToTime[1];
		document.f.ttHour.onfocus  = function(){document.f.ttHour.select();}
		document.f.ttMin.onfocus   = function(){document.f.ttMin.select();}
		document.f.ttHour.onchange = null;
		document.f.ttMin.onchange  = null;
	}
	_setIljungSuga();
}

// 간호 소요시간 수정
function _setNursingProcTime(obj){
	var fromTime = document.f.ftHour.value + document.f.ftMin.value;
	var toTime   = document.f.ttHour.value + document.f.ttMin.value;
	var procTime = document.f.procTime;
	var diffTime = _getTimeDiff(fromTime, toTime);

	if (diffTime > 0 && diffTime < 30){
		procTime.options[0].selected = true;
	}else if (diffTime > 30 && diffTime < 60){
		procTime.options[1].selected = true;
	}else{
		procTime.options[2].selected = true;
	}
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
	var mode = _getMode();

	//if (document.f.addDate.value.length == 8){
	if (mode.value != 'IN'){
		today = document.f.addDate.value;
		
		var year = today.substring(0,4);
		var month = today.substring(4,6);
		var day = today.substring(6,8);

		if (month.substring(0,1) == '0') month = month.substring(1,2);
		if (day.substring(0,1) == '0') day = day.substring(1,2);

		var now = new Date(parseInt(year), parseInt(month)-1, parseInt(day));

		today = today.substring(0,4)+'-'+today.substring(4,6)+'-'+today.substring(6,8);

		var today_yn = false;
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

		var today_yn = true;
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

	/******************************************************

		휴일설정을 하지 않고 수가를 계산한다.

	******************************************************/
		if (today_yn) Hgubun = 'N';
	/*****************************************************/

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
	var togeUmu = (document.f.togeUmu.checked ? 'Y' : 'N'); //동거여부
	var bipay   = (document.f.bipayUmu.checked ? 'Y' : 'N'); //비급여구분
	var Egubun  = 'N'; //야간여부
	var Ngubun  = 'N'; //심야여부
	
	var EAMT  = 0;
	var NAMT  = 0;
	var TAMT  = 0;
	var EFrom = 0;
	var ETo   = 0;
	var NFrom = 0;
	var NTo   = 0;

	var svcSubCode = _getSvcSubCode();

	EFrom = cut(FT - ERang1,30);
	//ETo   = TT - ERang1 + 1;

	
	/*********************************************************
		근무시간이 510분이상 넘어갈 경우 510분까지만 인정한다.
	*********************************************************/
	if (parseInt((TT+1), 10) - parseInt(FT, 10) > 8.5 * 60){
		ETo = cut((parseInt(FT, 10) + 8.5 * 60) - ERang1, 30);
	}else{
		ETo = cut((TT+1) - ERang1, 30);
	}

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
			NTNtime = cut(NTNtime, 30);
			ETNtime = ETNtime < 0 ? 0 : ETNtime - NTNtime;

			if (NTNtime > 480) NTNtime = 480;
			if (ETNtime > 480) ETNtime = 480;

			//새벽 6시 이전에 근무한 시간을 야간으로 적용한다.
			if (FT < 360){
				var tmpTT = 360 - (TT + 1);

				if (tmpTT < 0) tmpTT = 0;

				NTNtime = 360 - FT - tmpTT;
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
			/*
			case '29': TN = 1; break;
			case '59': TN = 2; break;
			case '89': TN = 3; break;
			*/
			case nursingTimeList[0]['cd']: TN = 1; break;
			case nursingTimeList[1]['cd']: TN = 2; break;
			case nursingTimeList[2]['cd']: TN = 3; break;
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
		if (togeUmu == 'Y'){
			sugaGubun = 'CCWC'; //동거
		}else if (bipay == 'Y'){
			sugaGubun = 'CCWS'; //비급여
		}else{
			if (yoyCount == 1 && Hgubun == 'N'){
				sugaGubun = 'CCWS';
			}else if (yoyCount > 1 && Hgubun == 'N'){
				sugaGubun = 'CCWD';
			}else if (yoyCount == 1 && Hgubun == 'Y'){
				sugaGubun = 'CCHS';
			}else if (yoyCount > 1 && Hgubun == 'Y'){
				sugaGubun = 'CCHD';
			}
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

	var mode = _getMode();
	
	if (mode.value == 'IN'){
		var dt = document.f.calYear.value+document.f.calMonth.value;
	}else{
		var dt = document.f.addDate.value;
	}

	if (!_newType()){
		var code = document.f.mCode.value;
	}else{
		var code = document.f.code.value;
	}

	//alert(dt);
	
	/*******************************
	
		고객 코드
	
	*******************************/
	var c_cd = document.f.jumin.value;
	
	//var help = showModalDialog('../inc/_help.php?r_gubun=sugaFind', window, 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	var sugaName    = getHttpRequest('../inc/_check.php?gubun=getSugaName&mCode='+code+'&mSuga='+sugaKey+'&mYM='+dt); //명칭
	//var sugaPrice   = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga='+sugaKey)); //단가
	
	if (bipay != 'Y'){
		var sugaPrice = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+code+'&mSuga='+sugaKey+'&mYM='+dt+'&bipay='+bipay+'&svc_cd='+svcSubCode+'&c_cd='+c_cd)); //단가
	}else{
		try{
			var bipay_kind = __object_get_value('bipay_kind');
			var suga_if    = getHttpRequest('iljung_value.php?type=suga_care&code='+f.code.value+'&suga_cd='+sugaKey).split('//');
			
			document.getElementById('bipay_cost_publid').value  = __num2str(suga_if[1]);
			document.getElementById('bipay_cost_private').value = __num2str(suga_if[2]);
		}catch(e){
			var bipay_kind = '3';
		}
		
		var svc_id    = __object_get_value('svc_id[]');

		switch(bipay_kind){
			case '1':
				var sugaPrice = __str2num(document.getElementById('bipay_cost_publid').value);
				break;
			case '2':
				var sugaPrice = __str2num(document.getElementById('bipay_cost_private').value);
				break;
			default:
				var sugaPrice = __str2num(document.getElementById('exp_max_pay').value);
		}
	}
	
	var sudangPrice = parseInt(getHttpRequest('../inc/_check.php?gubun=getSudangPrice&mCode='+code+'&mSuga='+sugaKey)); //수당

	if (svcSubCode == '200'){
		if (bipay == 'Y'){
			var tmp_time = TT + 1 - FT;
			
			if (bipay_kind == '1' || bipay_kind == '2'){
			}else{
				sugaPrice = sugaPrice * (parseInt(tmp_time, 10) / 60);
			}
		}
	}else if (svcSubCode == '500'){
		// 2011년 7월 1일부터 목욕 적용수가를 시간기준으로 변경한다.(2011.07.11 적용)
		if (dt >= '201107'){
			var tmp_time = TT + 1 - FT;

			if (tmp_time < 40){
				sugaPrice = 0;
			}else if (tmp_time >= 40 && tmp_time < 60){
				sugaPrice = sugaPrice * __BATH_SUGA_RATE__ / 100;
				sugaPrice = cutOff(sugaPrice);
			}
		}
	}
	
	if (sugaPrice == ''){
		sugaPrice = 0;
	}

	sugaCont.innerHTML = sugaName;

	/****************************************
	
		비급여가 아닐 경우만 할증계산
	
	****************************************/
	if (bipay != 'Y'){
		var tempValue = new Array();
		var tempTime  = new Array();
		var tempIndex = 0;

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
			//tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - 30 - parseInt(tempFmH);
			tempToH = parseInt(tempToH) * 60 + parseInt(tempToM) - parseInt(tempFmH);
			
			/*********************************************************
				최대 8시간 30분까지만 허용한다.
			*********************************************************/
			if (tempToH > 8.5 * 60) tempToH = 8.5 * 60;

			var tempL = cut(tempToH, 30) / 30;
			var tempK = 0;
			var temp_first = false;

			sugaPrice = 0;

			if (!_newType()){
				var code = document.f.mCode.value;
			}else{
				var code = document.f.code.value;
			}

			while(1){
				//if ($('#code').val() == '1234') alert(tempL);

				if (tempL >= 8){
					tempK = 8;
				}else if (tempL == 0 || tempK == 0){
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

				//tempValue[tempIndex] = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+code+'&mSuga='+sugaGubun+tempK)); 
				tempValue[tempIndex] = parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+code+'&mSuga='+sugaGubun+tempK+'&mYM='+dt+'&bipay='+bipay)); 
				tempTime[tempIndex]  = tempK;

				//if ($('#code').val() == '1234') alert(tempTime[tempIndex]+' / '+tempValue[tempIndex]);

				//if ($('#code').val() == '1234'){
				//	alert(tempValue[tempIndex]+' / '+tempTime[tempIndex]);
				//}

				sugaPrice += tempValue[tempIndex]; //parseInt(getHttpRequest('../inc/_check.php?gubun=getSugaPrice&mCode='+document.f.mCode.value+'&mSuga=CCWS'+tempK)); //단가

				tempIndex ++;
			}
		}

		var temp_e = 0;
		var i = 0;
		var liMax = 0;

		if (Hgubun != 'Y'){
			if (NTNtime > 0){
				if (sugaGubun != 'HS' && sugaGubun != 'HD'){
					if (TN == 9){
						temp_e = NTNtime / 30;
						//i = tempValue.length - 1;
						liMax = tempValue.length - 1;
						i = 0;

						NAMT = 0;

						while(1){
							//alert('N'+'/'+i + '/' + tempTime[i] + '/' + temp_e + '/' + tempValue[i]+'/'+tempValue.length);
							//if (i < 0) break;
							if (i > liMax) break;
							if (temp_e <= 0) break;

							if (tempTime[i] >= temp_e){
								//if ($('#code').val() == '1234') alert(tempValue[i]+' / '+tempTime[i]+' / '+temp_e);
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
						NAMT = Math.floor((sugaPrice * (NTN / TN)) * 0.3);
					}

					// 2011.04.29 절사에서 반올림으로 변경함.
					//NAMT = NAMT - (NAMT % 10); //절사
					NAMT = __round(NAMT, 0, false); //반올림
				}
				Ngubun = 'Y';
			}

			if (ETNtime > 0){
				if (sugaGubun != 'HS' && sugaGubun != 'HD'){
					if (TN == 9){
						temp_e = ETNtime / 30;

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
							//alert('E'+'/'+i + '/' + tempTime[i] + '/' + temp_e + '/' + tempValue[i]+'/'+tempValue.length);
							if (i < 0) break;
							if (temp_e <= 0) break;

							if (tempTime[i] >= temp_e){
								//EAMT += tempValue[i] * 0.2;
								//alert('1 : '+EAMT + '/' + tempValue[i] + '/' + tempTime[i] + '/' + temp_e);
								EAMT += Math.floor((tempValue[i] / tempTime[i] * temp_e * 0.2));
								break;
							}else{
								//alert('2 : '+EAMT + '/' + tempValue[i] + '/' + tempTime[i] + '/' + temp_e);
								EAMT += Math.floor(tempValue[i] * 0.2);
								temp_e -= tempTime[i];
							}

							i--;
						}
					}else{
						EAMT = Math.floor((sugaPrice * (ETN / TN)) * 0.2);
					}

					// 2011.04.29 절사에서 반올림으로 변경함.
					//EAMT = EAMT - (EAMT % 10); //절사
					EAMT = __round(EAMT, 0, false); //반올림  
				}
				Egubun = 'Y';
			}
		}
	}
	
	if (TN == 9){
		//TAMT = cutOff(__str2num(sugaPrice) + __str2num(EAMT) + __str2num(NAMT));
		TAMT = __round(__str2num(sugaPrice) + __str2num(EAMT) + __str2num(NAMT), 1, true);
	}else{
		TAMT = __round(__str2num(sugaPrice) + __str2num(EAMT) + __str2num(NAMT), 1, true);
	}
	
	if (mode.value != 'MODIFY')
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

	//선택된 서비스
	if (!_newType()){
		var svc_id = '11';
	}else{
		var svc_id = _get_current_svc('id');
	}

	var now      = new Date();
	var nowYear  = now.getFullYear();
	var nowMonth = now.getMonth()+1;
	var nowDay   = now.getDate();
	var nowHour  = now.getHours();
	var nowMin   = now.getMinutes();

	nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	nowDay   = (nowDay   < 10 ? '0' : '') + nowDay;
	nowHour  = (nowHour  < 10 ? '0' : '') + nowHour;
	nowMin   = (nowMin   < 10 ? '0' : '') + nowMin;

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
		var closing_yn = document.f.closing_yn.value;
	}catch(e){
		var closing_yn = 'N';
	}

	if (closing_yn == 'Y'){
		alert('실적마감처리되어 등록,수정,삭제를 할 수 없습니다.');
		return;
	}
	
	// 요양보호사 선택 여부 확인
	var yoyCount = 0;

	if (document.f.yoy1.value != '') yoyCount++;
	
	if (svc_id == '11'){
		if (document.f.yoy2.value != '') yoyCount++;
	}
	
	if (yoyCount == 0){	
		alert('선택된 요양보호사가 없습니다. 요양보호사를 선택하여 주십시오.');
		return;
	}


	/*********************************************************

		수가금액 0원이면 경고

	*********************************************************/
	if ($('#bipayUmu').attr('checked')){
		var warningIs = false;
		if (svc_id == '11'){
			if (__str2num($('#tPrice').attr('value')) == 0){
				warningIs = true;
			}
		}else{
			if (__str2num($('#sugaTot').attr('value')) == 0){
				warningIs = true;
			}
		}

		if (warningIs){
			alert('비급여 수가금액이 0원으로 입력되었습니다.');
		}
	}

	
	var svc_code = _getSvcSubCode();
	
	if (svc_code == '500'){
		if(document.f.yoy1.value == '' || document.f.yoy2.value == ''){
			alert('요양보호사 모두를 입력하여 주십시오.');
			return;
		}
	}

	var weekCount = 0; // 요일 선택 여부 확인
	var dateCount = 0; // 일자 선택 여부 확인

	if (!_newType()){
		var mode = document.f.mMode.value;
	}else{
		var mode = document.f.mode.value;
	}

	if (mode == 'IN'){
		if (document.f.svc_in_type.value == 'weekday'){
			if (document.f.weekDay1.checked) weekCount++;
			if (document.f.weekDay2.checked) weekCount++;
			if (document.f.weekDay3.checked) weekCount++;
			if (document.f.weekDay4.checked) weekCount++;
			if (document.f.weekDay5.checked) weekCount++;
			if (document.f.weekDay6.checked) weekCount++;
			if (document.f.weekDay0.checked) weekCount++;
		}else{
			var lastday = document.f.svc_last_day.value;

			for(var i=1; i<=lastday; i++){
				if (document.getElementById('svc_dt_'+i).value == 'Y'){
					dateCount ++;
					break;
				}
			}
		}

		if (weekCount == 0 && dateCount == 0){
			alert('제공요일 및 제공일자를 선택하여 주십시오.');
			return;
		}
	}

	if (svc_id == '11' || svc_id == '24'){
		if (svc_code != '200'){
			if (!document.f.bipayUmu.checked){
				if (__str2num(document.f.visitSudang.value) < 1){
					alert('방문건별 수당을 입력하여 주십시오.');
					document.f.visitSudang.focus();
					return;
				}
			}
		}
		
		if (svc_id == '11'){
			// 수가정보가 올바르지 않을 경우 탈출한다.
			if (!document.f.bipayUmu.checked){
				if (document.f.sPrice.value == '' ||
					document.f.sPrice.value == '0' ||
					isNaN(__commaUnset(document.f.sPrice.value))){
					alert('기준수가 오류입니다. 다시 입력하여 주십시오.');
					return;
				}
			}
		}
	}

	var oldFmTime = null, oldToTiem = null;
	var mode = _getMode();

	if (mode.value == 'IN'){
		_setPattern(); //패턴등록
		_inCalendar();
	}else if (mode.value == 'ADD'){
		_addCalendar();
	}else if (mode.value == 'ADD_CONF'){
		document.f.action = 'iljung_add_conf_ok.php';
		document.f.submit();
		return;
	}else{
		oldFmTime = opener.document.getElementById('mFmTime_'+document.f.addDay.value+'_'+document.f.addIndex.value).value;
		oldToTiem = opener.document.getElementById('mToTime_'+document.f.addDay.value+'_'+document.f.addIndex.value).value;

		_modifyCalendar();
	}

	if (mode.value != 'IN'){
		if (svc_id == '11'){
			if (!_newType()){
				var mCode = opener.document.f.mCode.value;
			}else{
				var mCode = opener.document.f.code.value;
			}
			var mYoy = new Array(document.f.yoy1.value, document.f.yoy2.value);
		}else{
			var mCode = opener.document.f.code.value;
			var mYoy = new Array(document.f.yoy1.value);
		}
		
		var mDate    = document.f.addDate.value;
		var mDay     = document.f.addDay.value;
		var mIndex   = document.f.addIndex.value;
		var mFmTime  = document.f.ftHour.value + document.f.ftMin.value;
		var mToTime  = document.f.ttHour.value + document.f.ttMin.value;
		var mRequest = 'N';
		var mYoyDT   = '';

		opener.document.getElementById('mSugupja_'+mDay+'_'+mIndex).value = 'N';

		mYoyDT = '';
		if (mode.value == 'MODIFY'){
			if (svc_id == '11'){
				mYoyDT = document.f.oldDate.value;
			}else{
				mYoyDT = opener.document.getElementById('mOldDate_'+mDay+'_'+mIndex).value;
			}
		}

		for(var i=0; i<mYoy.length; i++){
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
	
	if (!_newType()){
		var svc_id = '11';
	}else{
		//선택된 서비스 코드를 찾는다.
		var svc_id = _get_current_svc('id');
	}

	if (svc_id == '11'){
		var svcCount = document.f.svcSubCode.length;

		for(var i=0; i<svcCount; i++){
			if (document.f.svcSubCode[i].checked){
				svcSubCode = document.f.svcSubCode[i].value;
				break;
			}
		}
	}else if (svc_id == '24'){
		svcSubCode = __object_get_value('svcSubCode');
	}else{
		svcSubCode = svc_id;
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
	if (!_newType()){
		var mCode = document.f.mCode.value;
		var mKind = document.f.mKind.value;
		var mKey  = document.f.mKey.value;
		var url   = 'su_add_iljung.php';
		var w     = 901;
		var h     = 193;
	}else{
		var mCode = document.f.code.value;
		//var mKind = document.f.kind.value;
		var mKind = document.getElementById('mKind_'+pDay+'_'+pIndex).value;
		var mKey  = document.f.key.value;
		var url   = 'iljung_add.php';
		var w     = 1000;
		var h     = 335;
	}
	
	var mDate = eval('document.f.mDate_'+pDay+'_'+pIndex).value;
	var mWeek = eval('document.f.mWeekDay_'+pDay+'_'+pIndex).value;

	if (mWeek == '7'){
		mWeek = '0';
	}

	var modal = showModalDialog(url+'?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mDay='+pDay+'&mIndex='+pIndex+'&mDate='+mDate+'&mWeek='+mWeek+'&mMode=MODIFY', window, 'dialogWidth:'+w+'px; dialogHeight:'+h+'px; dialogHide:yes; scroll:yes; status:no');
	//window.open('su_add_iljung.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mDay='+pDay+'&mIndex='+pIndex+'&mDate='+mDate+'&mWeek='+mWeek+'&mMode=MODIFY');
}



/*********************************************************

	일정삭제

*********************************************************/
function _clearDiary(pDay, pIndex){
	if ($('#mOldDate_'+pDay+'_'+pIndex).attr('value') != '') {
		var code   = $('#code').attr('value');
		var jumin  = $('#jumin').attr('value');
		var kind   = $('#mKind_'+pDay+'_'+pIndex).attr('value');
		var date   = $('#mDate_'+pDay+'_'+pIndex).attr('value');
		var time   = $('#mFmTime_'+pDay+'_'+pIndex).attr('value');
		var seq    = $('#mSeq_'+pDay+'_'+pIndex).attr('value');
		var result = getHttpRequest('./iljung_clear.php?code='+code+'&kind='+kind+'&jumin='+jumin+'&date='+date+'&time='+time+'&seq='+seq);

		if (result == 'error'){
			alert('선택하신 일정 삭제중 오류가 발생하였습니다.\n\n잠시후 다시 시도하여 주십시오.');
			return;
		}
	}
	

	var svc_id = _get_current_svc('id');
	var mSeq   = eval('document.f.mSeq_'+pDay+'_'+pIndex).value;

	if (mSeq == '0'){
		eval('document.f.mUse_'+pDay+'_'+pIndex).value = 'N';
	}

	eval('document.f.mDelete_'+pDay+'_'+pIndex).value = 'Y';
	
	document.getElementById('txtSubject_'+pDay+'_'+pIndex).innerHTML = '';
	document.getElementById('txtSubject_'+pDay+'_'+pIndex).style.display = 'none';

	var modifyFlag = _getModifyPos(); //수정위치

	if (modifyFlag == 'D' || modifyFlag == 'M'){
		var temp_add_suga = false;
	}else{
		var temp_add_suga = true;
	}
	
	if (temp_add_suga){
		var temp_old_suga  = document.getElementById('mTValue_'+pDay+'_'+pIndex).value;
		var temp_old_svc   = document.getElementById('mSvcSubCode_'+pDay+'_'+pIndex).value;
		var temp_old_bipay = document.getElementById('mBiPayUmu_'+pDay+'_'+pIndex).value;
		var temp_new_suga  = 0;
		var temp_new_svc   = temp_old_svc;
		var temp_new_bipay = temp_old_bipay;

		if (svc_id == '11'){
			_set_month_amount_care('delete', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_bipay, temp_new_bipay);
		}else{
			var temp_old_time = document.getElementById('mProcStr_'+pDay+'_'+pIndex).value;
			var temp_new_time = 0;

			_set_month_amount_other('delete', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_time, temp_new_time, temp_old_bipay, temp_new_bipay);
			
			/*********************************************************
				산모신생아의 경우 추가요금계를 구한다.
			*********************************************************/
			_set_addpay_summly();
		}
	}
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

	if (!_newType()){
		var url = '../iljung/su_add_iljung.php';
		var w   = 901;
		var h   = 193;
	}else{
		var url = '../iljung/iljung_add.php';
		var w   = 1000;
		var h   = 360;
		var svc = document.getElementsByName('svc_id[]');

		for(var i=0; i<svc.length; i++){
			if (svc[i].checked){
				pKind = document.getElementsByName('svc_cd[]')[i].value;
				break;
			}
		}
	}

	var mode = 'ADD';
	var modal = showModalDialog(url+'?mCode='+pCode+'&mKind='+pKind+'&mKey='+pKey+'&mDay='+pDay+'&mIndex='+Index+'&mDate='+pDate+'&mWeek='+pWeek+'&mMode='+mode, window, 'dialogWidth:'+w+'px; dialogHeight:'+h+'px; dialogHide:yes; scroll:yes; status:no');
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

	var svcDate = 'N';

	if (document.f.svc_in_type.value == 'date'){
		var lastday = document.f.svc_last_day.value;

		for(var i=1; i<=lastday; i++){
			svcDate += (','+document.getElementById('svc_dt_'+i).value);
		}
	}
	
	if (!_newType()){
		var code   = document.f.mCode.value;
		var kind   = document.f.mKind.value;
		var jumin  = document.f.mJuminNo.value;
		var key    = document.f.mKey.value;
		var URL    = 'su_calendar.php';
		var body   = calendar;
		var svc_id = '11';
	}else{
		var code   = document.f.code.value;
		var kind   = _get_current_svc();
		var jumin  = document.f.jumin.value;
		var key    = document.f.key.value;
		var URL    = 'iljung_calendar.php';
		var body   = iljung_calendar;
		var svc_id = _get_current_svc('id');
	}

	var fmTime   = document.f.ftHour.value + document.f.ftMin.value;
	var ttTime   = document.f.ttHour.value + document.f.ttMin.value;
	var weekDay1 = document.f.weekDay1.checked ? document.f.weekDay1.value : '';
	var weekDay2 = document.f.weekDay2.checked ? document.f.weekDay2.value : '';
	var weekDay3 = document.f.weekDay3.checked ? document.f.weekDay3.value : '';
	var weekDay4 = document.f.weekDay4.checked ? document.f.weekDay4.value : '';
	var weekDay5 = document.f.weekDay5.checked ? document.f.weekDay5.value : '';
	var weekDay6 = document.f.weekDay6.checked ? document.f.weekDay6.value : '';
	var weekDay0 = document.f.weekDay0.checked ? document.f.weekDay0.value : '';
	var yoy1     = document.f.yoy1.value;
	var yoyNm1   = document.f.yoyNm1.value;
	var yoyTA1   = document.f.yoyTA1.value;
	var sugaCode = document.f.sugaCode.value;
	var sugaName = document.f.sugaName.value;
	var svcInType= document.f.svc_in_type.value;
	var bipayUmu = document.f.bipayUmu.checked ? document.f.bipayUmu.value : '';


	/**************************************************
		비급여 구분
	**************************************************/
		var bipay_kind = __object_get_value('bipay_kind');
	/*************************************************/
	
	
	if (svc_id == '11'){
		var procTime  = document.f.procTime.value;
		var svcSubCd  = document.f.svcSubCD.value;
		var togeUmu   = document.f.togeUmu.checked ? document.f.togeUmu.value : '';
		var yoy2      = document.f.yoy2.value;
		var yoyNm2    = document.f.yoyNm2.value;
		var yoyTA2    = document.f.yoyTA2.value;
		var sPrice    = __commaUnset(document.f.sPrice.value);
		var ePrice    = __commaUnset(document.f.ePrice.value);
		var nPrice    = __commaUnset(document.f.nPrice.value);
		var tPrice    = __commaUnset(document.f.tPrice.value);
		var Egubun    = document.f.Egubun.value;
		var Ngubun    = document.f.Ngubun.value;
		var Etime     = document.f.Etime.value;
		var Ntime     = document.f.Ntime.value;
		var visitSudangCheck = (document.f.visitSudangCheck.checked ? "Y" : "N");
		var visitSudang      = document.f.visitSudang.value;
		var sudangYul1       = document.f.sudangYul1.value;
		var sudangYul2       = document.f.sudangYul2.value;
		var carNo            = document.f.carNo.value;
	}else if (svc_id == '24'){
		var procTime  = document.f.procTime.value;
		var svcSubCd  = document.f.svcSubCD.value;
		var togeUmu   = '';
		var yoy2      = document.f.yoy2.value;
		var yoyNm2    = document.f.yoyNm2.value;
		var yoyTA2    = document.f.yoyTA2.value;
		var sPrice    = __commaUnset(document.f.sugaCost.value);
		var ePrice    = __commaUnset(f.sugaCostNight.value);
		var nPrice    = 0;
		var tPrice    = __commaUnset(f.sugaTot.value);
		var Ngubun    = '';
		var Ntime     = 0;

		if (svc_id == '24'){
			var Etime  = $('#sugaTimeNight').attr('value');
			var Egubun = (Etime > 0 ? 'Y' : '');
		}else{
			var Egubun = '';
			var Etime  = 0;
		}

		var visitSudangCheck = (document.f.visitSudangCheck.checked ? "Y" : "N");
		var visitSudang      = document.f.visitSudang.value;
		var sudangYul1       = document.f.sudangYul1.value;
		var sudangYul2       = document.f.sudangYul2.value;
		var carNo            = document.f.carNo.value;
	}else{
		var procTime  = document.f.sugaTime.value;
		var svcSubCd  = '1';
		var togeUmu   = '';
		var yoy2      = '';
		var yoyNm2    = '';
		var yoyTA2    = '';
		var sPrice    = __commaUnset(document.f.sugaCost.value);
		var ePrice    = f.svcStnd.value;
		var nPrice    = f.svcCnt.value;
		var tPrice    = sPrice * procTime;
		var Egubun    = '';
		var Ngubun    = '';
		var Etime     = 0;
		var Ntime     = 0;
		var visitSudangCheck = 'N';
		var visitSudang      = 0;
		var sudangYul1       = 0;
		var sudangYul2       = 0;
		var carNo            = '';
	}
	
	
	/****************************************
		비급여 실비 처리 구분
	 ****************************************/
		var bipay1 = 0;
		var bipay2 = 0;
		var bipay3 = 0;
		
		var exp_yn  = 'N';
		var exp_pay = 0;
			
		if (svc_id > 10 && svc_id < 30){
			bipay1 = __str2num(document.getElementById('bipay_cost1').value);
			bipay2 = __str2num(document.getElementById('bipay_cost2').value);
			bipay3 = __str2num(document.getElementById('bipay_cost3').value);
			
			exp_yn  = __object_get_value('exp_yn');
			exp_pay = __str2num(document.getElementById('exp_pay').value);
		}
	/****************************************/
	
	
	
	/****************************************
		산모신생아, 산모유료 추가금액(비급여)
	 ****************************************/
		var school_not_cnt = 0;
		var school_not_pay = 0;
		var school_cnt     = 0;
		var school_pay     = 0;
		var family_cnt     = 0;
		var family_pay     = 0;
		var home_in_yn     = 'N';
		var home_in_pay    = 0;
		var holiday_pay    = 0;
		
		if (svc_id == '23' || svc_id == '31'){
			school_not_cnt = __str2num(document.getElementById('school_not_cnt').value);
			school_not_pay = __str2num(document.getElementById('school_not_pay').value);
			school_cnt     = __str2num(document.getElementById('school_cnt').value);
			school_pay     = __str2num(document.getElementById('school_pay').value);
			family_cnt     = __str2num(document.getElementById('family_cnt').value);
			family_pay     = __str2num(document.getElementById('family_pay').value);
			home_in_yn     = document.getElementById('home_in_yn').checked ? 'Y' : 'N';
			home_in_pay    = __str2num(document.getElementById('home_in_pay').value);
			holiday_pay    = __str2num(document.getElementById('holiday_pay').value);
		}
	/****************************************/

	try{
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:{
					calYear:calYear,
					calMonth:calMonth,
					mCode:code,
					mKind:kind,
					mKey:key,
					mJuminNo:jumin,
					svcSubCode:svcSubCode,
					svcSubCD:svcSubCd,
					fmTime:fmTime, ttTime:ttTime,
					procTime:procTime,
					togeUmu:togeUmu,
					bipayUmu:bipayUmu,
					weekDay1:weekDay1, weekDay2:weekDay2, weekDay3:weekDay3, weekDay4:weekDay4, weekDay5:weekDay5, weekDay6:weekDay6, weekDay0:weekDay0,
					yoy1:yoy1, yoy2:yoy2,
					yoyNm1:yoyNm1, yoyNm2:yoyNm2,
					yoyTA1:yoyTA1, yoyTA2:yoyTA2,
					sPrice:sPrice, ePrice:ePrice, nPrice:nPrice, tPrice:tPrice,
					sugaCode:sugaCode, sugaName:sugaName,
					Egubun:Egubun, Ngubun:Ngubun,
					Etime:Etime, Ntime:Ntime,
					visitSudangCheck:visitSudangCheck,
					visitSudang:visitSudang,
					sudangYul1:sudangYul1,
					sudangYul2:sudangYul2,
					carNo:carNo,
					svcInType:svcInType,
					svcDate:svcDate,
					svcId:svc_id,
					bipay1:bipay1, bipay2:bipay2, bipay3:bipay3, expenseYn:exp_yn, expensePay:exp_pay,
					bipay_kind:bipay_kind,
					school_not_cnt:school_not_cnt, school_not_pay:school_not_pay, school_cnt:school_cnt, school_pay:school_pay, family_cnt:family_cnt, family_pay:family_pay, home_in_yn:home_in_yn, home_in_pay:home_in_pay, holiday_pay:holiday_pay,
					gubun:'reg'
				},
				onSuccess:function (responseHttpObj) {
					body.innerHTML = responseHttpObj.responseText;
					
					/*********************************************************
						해당일정만 수정,삭제 버튼을 활성화한다.
					*********************************************************/
					try{
						_set_current_svc_enabled();
					}catch(e){
					}

					var temp_old_suga  = 0;
					var temp_old_bipay = 'N';
					var temp_new_suga  = document.getElementById('new_total_suga').value;
					var temp_new_bipay = document.f.bipayUmu.checked?'Y':'N';

					if (svc_id == '11'){
						_init_month_amount_care();

						var temp_old_svc   = svcSubCode;
						var temp_new_svc   = svcSubCode;
						
						_set_month_amount_care('insert', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_bipay, temp_new_bipay);
					}else{
						_init_month_amount_other();
						
						if (svc_id == '24'){
							var temp_old_svc   = svcSubCode;
							var temp_new_svc   = svcSubCode;
						}else{
							var temp_old_svc   = svc_id;
							var temp_new_svc   = svc_id;
						}
						
						var temp_old_time  = 0;
						var temp_new_time  = document.getElementById('new_total_time').value;
						
						_set_month_amount_other('insert', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_time, temp_new_time, temp_old_bipay, temp_new_bipay);
						/*********************************************************
							산모신생아의 경우 추가요금계를 구한다.
						*********************************************************/
						_set_addpay_summly();
					}
				}
			}
		);
	}catch(e){
		alert(e.description);
	}
}

function _checkDuplicate(){
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
	var newSvcSubCode = _getSvcSubCode();

	//선택된 서비스
	if (!_newType()){
		var svc_id = '11';
	}else{
		var svc_id = _get_current_svc('id');
	}

	newFmTime = _getTimeValue(document.f.ftHour.value, document.f.ftMin.value);
	newToTime = _getTimeValue(document.f.ttHour.value, document.f.ttMin.value);

	newYoy[1] = document.f.yoy1.value;

	if (svc_id == '11'){
		newYoy[2] = document.f.yoy2.value;
	}else{
		newYoy[2] = '';
	}
	
	checkSugupja = opener.document.getElementById('mSugupja_'+newDay+'_'+newIndex); //eval('opener.document.f.mSugupja_'+i+'_'+checkIndex);
	
	if (checkSugupja != undefined){
		if (checkSugupja.value == 'Y'){
			opener.document.getElementById('checkDuplicate_'+newDay+'_'+newIndex).style.display = '';
			opener.document.getElementById('checkDuplicate_'+newDay+'_'+newIndex).style.backgroundColor = '#ffffff';
			opener.document.getElementById('checkDuplicate_'+newDay+'_'+newIndex).innerHTML = '<span style=\'color:#ff0000; font-weight:bold; cursor:pointer;\' onclick=\'_chk_iljung('+newDay+','+newIndex+');\'>일정이 중복되었습니다.</span>';
			opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
			//opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
			
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '5px';
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '5px';
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle  = 'solid';
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth  = '2px';
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor  = '#ff0000';
			opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

			//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
			//eval('opener.checkSugupja_'+newDay+'_'+newIndex).style.display = '';
			//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
			
			opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
			opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';

			eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
		}else{
			if (opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value == 'N'){
				try{opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = 'none';}catch(e){}
				try{opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '';}catch(e){}
			}
		}
	}
	
	
	////////////////////////////////////////////////////////
	//
	// 재가요양인 경우 제한한다.
	//
	////////////////////////////////////////////////////////
	if (svc_id == '11'){
		// 동거가족 제한(2011년 8월부터)
		var limit_dt   = document.getElementById('addDate').value;
			limit_dt   = limit_dt.substring(0,6); //일정등록 년월
		var family_min = opener.document.getElementById('family_min').value; //하루에 등록가능한 시간
		var family_cnt = opener.document.getElementById('family_cnt').value; //한달에 등록가능한 일수
		var family_time= 0;
		var bath_week_cnt = opener.document.getElementById('bath_week_cnt').value; //주별 등록 가능한 목욕 횟수

		// 동거가족 한달의 허용일수를 초과했을 경우
		if (limit_dt >= '201108'){
			var sugaCode = opener.document.getElementById('mSugaCode_'+newDay+'_'+newIndex);

			if (sugaCode.value.substring(0, 4) == __FAMILY_SUGA_CD__){
				// 동거가족
				var family_days= _getMonthFamilyCnt();

				// 동거가족 월별 횟수 확인
				if (family_days > family_cnt){
					opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
					opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;\'>등록가능일수를 초과하였습니다.</span>';
					opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#f2a2ac';
					opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
					opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

					//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
					//eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

					return;
				}
			}
		}

		// 목욕 제한 횟수
		if (limit_dt >= '201107'){
			var bath_weeks = _getMonthBathCnt(document.f.addDate.value);
			var bath_today = _getTodaySvcCnt(newDay, '500');

			// 목욕 주별 횟수 확인
			if (bath_weeks > bath_week_cnt || bath_today > bath_week_cnt){
				opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
				opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;">주간별 목욕 가능횟수를 초과하였습니다.</span>';
				opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#9cdbf0';
				opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
				opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

				//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
				//eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
				return;
			}
		}

		// 동거외 다른 요양수가 등록 제한 횟수
		var care_limit_cnt = opener.document.getElementById('care_limit_cnt').value;
		var care_reg_cnt   = 0;
	}

	try{
		for(var i=newDay; i<=newDay; i++){
			checkDuplicate = false;
			checkLoop = true;
			checkIndex = 1;
			
			if (opener.document.getElementById('mSugaCode_'+newDay+'_'+newIndex).value.substring(0, 4) == __FAMILY_SUGA_CD__){
				family_time = newToTime - newFmTime;
			}else{
				family_time = 0;
			}

			care_reg_cnt = 0;
			
			while(checkLoop){
				if (checkIndex != newIndex){
					checkUse = eval('opener.document.f.mUse_'+i+'_'+checkIndex);

					if (checkUse == undefined){
						checkLoop = false;
					}

					if (newSvcSubCode == 23){
						/************************************************************************

							산모신생아 토요일은 13시까지 등록
							일요일 및 휴일은 등록을 막는다.

						************************************************************************/
						/*
						var new_weekday = opener.document.getElementById('mWeekday_'+newDay+'_'+newIndex).value;
						var new_holiday = opener.document.getElementById('mHoliday_'+newDay+'_'+newIndex).value;
						
						if (new_weekday == 0 || new_holiday == 'Y'){
							opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
							opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;">산모신생아는 휴일 및 일요일에 등록할 수 없습니다.</span>';
							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#fcfae8';
							opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle  = '';
							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth  = '0';
							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor  = '';

							opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
							opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

							checkLoop = false;
						}
						*/
						/************************************************************************/
					}

					if (checkLoop){
						if (checkUse.value == 'Y'){
							try{
								checkSugupja = opener.document.getElementById('mSugupja_'+newDay+'_'+newIndex); //eval('opener.document.f.mSugupja_'+newDay+'_'+newIndex);

								if (checkSugupja.value == 'Y'){
									opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
									//opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';

									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle   = 'solid';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth   = '2px';
									opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor   = '#ff0000';
								}else{
									if (opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value == 'N'){
										opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = 'none';
										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '';

										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle   = '';
										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth   = '0';
										opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor   = '';
									}
									checkYoy[1] = eval('opener.document.f.mYoy1_'+i+'_'+checkIndex).value;
									checkYoy[2] = eval('opener.document.f.mYoy2_'+i+'_'+checkIndex).value;
								
									checkTemp = eval('opener.document.f.mFmTime_'+i+'_'+checkIndex).value;
									checkFmTime = _getTimeValue(checkTemp.substring(0,2),checkTemp.substring(2,4));

									checkTemp = eval('opener.document.f.mToTime_'+i+'_'+checkIndex).value;
									checkToTime = _getTimeValue(checkTemp.substring(0,2),checkTemp.substring(2,4));

									var isDuplicate = opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value == 'Y' ? true : false;

									if ((checkFmTime <= newFmTime && checkToTime >  newFmTime) || 
										(checkToTime >  newToTime && checkFmTime <  newToTime) ||
										(checkFmTime >= newFmTime && checkToTime <= newToTime)){
										
										if (!isDuplicate){
											if (opener.document.getElementById('mSvcSubCode_'+newDay+'_'+newIndex).value == '800'){
												if (opener.document.getElementById('mSvcSubCode_'+newDay+'_'+checkIndex).value == '800' && newSvcSubCode == '800'){
													isDuplicate = true;
												}else{
													if (checkYoy[1] == newYoy[1]){
														isDuplicate = true;
													}
												}
											}else{
												if (opener.document.getElementById('mSvcSubCode_'+newDay+'_'+checkIndex).value != '800'){
													isDuplicate = true;
												}
											}

											if (isDuplicate){
												opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
												opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.backgroundColor = '#ffffff';
												opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style=\'color:#ff0000; font-weight:bold;\'>일정이 중복되었습니다.</span>';
												//opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ffffff';
												
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '5px';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '5px';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle  = 'solid';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth  = '2px';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor  = '#ff0000';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
												opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

												//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
												eval('opener.checkSugupja_'+newDay+'_'+newIndex).style.display = '';
												//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff0000';
												eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
											}
										}
									}else if (opener.document.getElementById('mSvcSubCode_'+newDay+'_'+newIndex).value == '200' && 
											  opener.document.getElementById('mSvcSubCode_'+newDay+'_'+checkIndex).value == '200' &&
											  document.getElementById('bipayUmu').checked == false){
										// 요양인경우
										// 전일정과 2시간 간격여부 확인
										if(!isDuplicate){
											if (svc_id == 11){
												var bipay = opener.document.getElementById('mBiPayUmu_'+newDay+'_'+checkIndex).value;
												
												if (bipay != 'Y'){
													// 전 일정과 2시간 차이를 확인한다.
													var tmpCheckFmTime = checkFmTime - __CHECK_ADD_TIME__;
													var tmpCheckToTime = checkToTime + __CHECK_ADD_TIME__;

													if ((newFmTime > tmpCheckFmTime && newFmTime < tmpCheckToTime) ||
														(newToTime > tmpCheckFmTime && newToTime < tmpCheckToTime)){
														opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
														opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;">전일정과 2시간의 간격이 필요합니다.</span>';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#77fd74';
														opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle   = '';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth   = '0';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor   = '';
														opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';

														//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
														eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
													}
												}
											}
										}
									}

									// 동거가족 제한(2011년 8월부터)
									if (limit_dt >= '201108'){
										var tmpSugaCode = null;
										var tmpNewCode  = null;
										var tmpSvcCode  = null;
										
										//tmpSugaCode = opener.document.getElementById('mSugaCode_'+newDay+'_'+newIndex).value;
										tmpSugaCode = opener.document.getElementById('mSugaCode_'+newDay+'_'+checkIndex).value;
										tmpNewCode  = opener.document.getElementById('mSugaCode_'+newDay+'_'+newIndex).value;
										tmpSvcCode  = opener.document.getElementById('mSvcSubCode_'+newDay+'_'+checkIndex).value;

										if (tmpSvcCode == '200'){
											care_reg_cnt ++;
										}
										
										// 동거가족
										if (tmpSugaCode.substring(0, 4) == __FAMILY_SUGA_CD__){
											family_time += (checkToTime - checkFmTime);

											if (family_time > family_min){
												opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
												opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;">하루허용 시간을 초과하였습니다.</span>';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ff9844';
												opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle   = '';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth   = '0';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor   = '';
												opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';

												//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
												eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
											}
										}

										// 동거가 등록되어 있다면 다른 요양수가는 등록이 불가능하다.
										//if ((care_reg_cnt >= care_limit_cnt) && (tmpSugaCode.substring(0, 4) == __FAMILY_SUGA_CD__ || tmpNewCode.substring(0, 4) == __FAMILY_SUGA_CD__) && tmpSugaCode.substring(0, 4) != tmpNewCode.substring(0, 4)){
										if ((care_reg_cnt >= care_limit_cnt) && 
											(tmpSugaCode.substring(0, 4) == __FAMILY_SUGA_CD__ || tmpNewCode.substring(0, 4) == __FAMILY_SUGA_CD__) && 
											(tmpSugaCode.substring(0, 4) != tmpNewCode.substring(0, 4)) &&
											(tmpNewCode.substring(0,2) == __CARE_SUGA_CD__)){
											opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).style.display = '';
											opener.document.getElementById('checkSugupja_'+newDay+'_'+newIndex).innerHTML = '<span style="color:#ff0000; font-weight:bold;">동거수가가 등록된 일은 다른 방문용양일정을 등록할 수 없습니다.</span>';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#f2f2f2';
											opener.document.getElementById('mDuplicate_'+newDay+'_'+newIndex).value = 'Y';

											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginTop    = '0';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.marginBottom = '0';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderStyle   = '';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderWidth   = '0';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.borderColor   = '';
											opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';

											//eval('opener.txtSubject_'+newDay+'_'+newIndex).style.display = '';
											eval('opener.document.f.mDuplicate_'+newDay+'_'+newIndex).value = 'Y';
										}
									}
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
	}catch(e){
		if (e instanceof Error){
			opener.alert('System Error : ' + e.description);
		}else if (typeof(e) == 'string'){
			opener.alert('String Error : ' + e.description);
		}else{
			opener.alert('Error Number : '+e.number+'\n Description : '+e.description);
		}
	}
}

// 일정표에 등록된 동거일수를 계산한다.
function _getMonthFamilyCnt(){
	var parent = opener.document;
	var lastDay = parent.f.mLastDay.value;
	var familyCnt = 0;

	for(var day=1; day<=lastDay; day++){
		var index = 1;
		var loop = true
		
		while(loop){
			var use = parent.getElementById('mUse_'+day+'_'+index);

			if (use == null){
				loop = false;
			}else{
				if (use.value == 'Y'){
					var del = parent.getElementById('mDelete_'+day+'_'+index);
					var duplicate = parent.getElementById('mDuplicate_'+day+'_'+index);

					if (del.value == 'N' && duplicate.value == 'N'){
						var sugaCode = parent.getElementById('mSugaCode_'+day+'_'+index);

						if (sugaCode.value.substring(0, 4) == __FAMILY_SUGA_CD__){
							familyCnt ++;
							loop = false;
						}
					}
				}
			}

			index ++;
		}
	}

	return familyCnt;
}

// 주간별 목욕횟수를 확인한다.
function _getMonthBathCnt(dt){
	var parent = opener.document;
	var lastDay = parent.f.mLastDay.value;
	var bathCnt = new Array();
	
	var startDay = new Date(dt.substring(0,4), parseInt(dt.substring(4,6), 10)-1, 1);
	var startWeek = startDay.getDay(); //1일의 요일
	var totalWeek = Math.ceil((parseInt(lastDay, 10) + parseInt(startWeek, 10)) / 7); //총 몇 주인지 구하기
	var lastsDay = new Date(dt.substring(0,4), parseInt(dt.substring(4,6), 10)-1, lastDay);
	var lastWeek = lastsDay.getDay(); //마일의 요일
	var bath_first_week_cnt = parent.getElementById('bath_first_week_cnt').value; // 목욕 일정제한을 위해 전월 마지막주 목욕 일정수
	var bath_last_week_cnt  = parent.getElementById('bath_last_week_cnt').value;  // 목욕 일정제한을 위해 익월 첫주 목욕 일정수
	var weekIndex = 1;
	var day = 1;

	for(var i=1; i<=totalWeek; i++){
		// 목욕 일정제한을 위해 주간 횟수
		switch(i){
			case 1:
				bathCnt[i] = bath_first_week_cnt;
				break;
			case totalWeek:
				bathCnt[i] = bath_last_week_cnt;
				break;
			default:
				bathCnt[i] = 0;
		}

		for(var j=0; j<7; j++){
			if (!((i == 1 && j < startWeek) || (i == totalWeek && j > lastWeek))){
				if (parseInt(dt.substring(6,8), 10) == day){
					tmp_dt = new Date(dt.substring(0,4), parseInt(dt.substring(4,6), 10) - 1, dt.substring(6,8));
					
					if (tmp_dt.getDay() == 0){
						weekIndex = i - 1;
					}else{
						weekIndex = i;
					}
				}

				var index = 1;
				var loop = true
				
				while(loop){
					var use = parent.getElementById('mUse_'+day+'_'+index);

					if (use == null){
						loop = false;
					}else{
						if (use.value == 'Y'){
							var del = parent.getElementById('mDelete_'+day+'_'+index);
							var duplicate = parent.getElementById('mDuplicate_'+day+'_'+index);

							if (del.value == 'N' && duplicate.value == 'N'){
								var sugaCode = parent.getElementById('mSugaCode_'+day+'_'+index);

								if (sugaCode.value.substring(0, 2) == bathSugaCD){
									tmp_dt = new Date(dt.substring(0,4), parseInt(dt.substring(4,6), 10) - 1, day);

									if (tmp_dt.getDay() == 0){
										bathCnt[i - 1] ++;
									}else{
										bathCnt[i] ++;
									}
									loop = false;
								}
							}
						}
					}

					index ++;
				}

				day ++;
			}
		}
	}

	return bathCnt[weekIndex];
}

function _getTodaySvcCnt(day, svc){
	var checkDuplicate = false;
	var checkLoop = true;
	var checkIndex = 1;
	var checkCnt = 0;
	
	while(checkLoop){
		var checkUse = eval('opener.document.f.mUse_'+day+'_'+checkIndex);

		if (checkUse == undefined){
			checkLoop = false;
		}

		if (checkLoop){
			if (checkUse.value == 'Y'){
				try{
					var obj = opener.document.getElementById('mSvcSubCode_'+day+'_'+checkIndex);
					
					if (obj.value == svc) checkCnt ++;
				}catch(e){
				}
			}
		}
			
		checkIndex ++;
	}
	
	return checkCnt;
}

/*
 * 일정추가
 */
function _addCalendar(){
	var newH     = '';
	var newDay   = document.f.addDay.value;
	var newIndex = document.f.addIndex.value;
	var newDate  = document.f.addDate.value;
	var newWeek  = document.f.addWeek.value;
	
	var newSvcSubCode = _getSvcSubCode();
	
	var mTogeUmu = 'N';
	var mBiPayUmu = 'N';
	//var mTimeDoub = 'N';
	var mSudangYN = 'N';
	var tempObject = eval('opener.document.f.mUse_'+newDay+'_'+newIndex);
	var tempNew = true;

	var mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+newDate);

	if (tempObject != undefined){
		if (tempObject.value == 'N'){
			tempNew = false;
		}
	}

	var svc_id   = _get_current_svc('id');
	var timeDiff = 0;

	if (newSvcSubCode == '200' && document.f.procTime.value == '0'){
		timeDiff = _getTimeDiff(document.f.ftHour.value+document.f.ftMin.value, document.f.ttHour.value+document.f.ttMin.value);
	}else{
		if (svc_id == '11'){
			timeDiff = document.f.procTime.value;
		}else{
			timeDiff = document.f.sugaTime.value;
		}
	}

	var modifyFlag = _getModifyPos(); //수정위치

	if (modifyFlag == 'D' || modifyFlag == 'M'){
		var temp_add_suga = false;
	}else{
		var temp_add_suga = true;
	}

	if (temp_add_suga){
		try{var temp_old_suga  = opener.document.getElementById('mTValue_'+newDay+'_'+newIndex).value;	  }catch(e){var temp_old_suga  = 0;}
		try{var temp_old_svc   = opener.document.getElementById('mSvcSubCode_'+newDay+'_'+newIndex).value;}catch(e){var temp_old_svc   = 0;}
		try{var temp_old_bipay = opener.document.getElementById('mBiPayUmu_'+newDay+'_'+newIndex).value;  }catch(e){var temp_old_bipay = 0;}

		var temp_new_svc   = newSvcSubCode;
		var temp_new_bipay = document.f.bipayUmu.checked ? 'Y' : 'N';

		if (svc_id == '11'){
			var temp_new_suga  = __commaUnset(document.f.tPrice.value);
			var temp_new_svc   = newSvcSubCode;
			
			_set_month_amount_care('add', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_bipay, temp_new_bipay);
		}else{
			try{var temp_old_time  = __str2num(opener.document.getElementById('mProcStr_'+newDay+'_'+newIndex).value);}catch(e){var temp_old_time  = 0;}
			var temp_new_suga  = __commaUnset(document.f.sugaTot.value);
			var temp_new_time  = document.f.sugaTime.value;

			_set_month_amount_other('add', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_time, temp_new_time, temp_old_bipay, temp_new_bipay);
		}
	}
	
	var bipayUmu = document.f.bipayUmu.checked ? document.f.bipayUmu.value : '';

	if (svc_id == '11'){
		var svcSubCd = document.f.svcSubCD.value;
		var togeUmu  = document.f.togeUmu.checked ? document.f.togeUmu.value : '';
		var yoy2     = document.f.yoy2.value;
		var yoyNm2   = document.f.yoyNm2.value;
		var yoyTA2   = document.f.yoyTA2.value;
		var sPrice   = __commaUnset(document.f.sPrice.value);
		var ePrice   = __commaUnset(document.f.ePrice.value);
		var nPrice   = __commaUnset(document.f.nPrice.value);
		var tPrice   = __commaUnset(document.f.tPrice.value);
		var Egubun   = document.f.Egubun.value;
		var Ngubun   = document.f.Ngubun.value;
		var Etime    = document.f.Etime.value;
		var Ntime    = document.f.Ntime.value;
		var visitSudangCheck = (document.f.visitSudangCheck.checked ? "Y" : "N");
		var visitSudang      = document.f.visitSudang.value;
		var sudangYul1       = document.f.sudangYul1.value;
		var sudangYul2       = document.f.sudangYul2.value;
		var carNo            = document.f.carNo.value;
	}else{
		var svcSubCd = '1';
		var togeUmu  = '';

		try{
			var yoy2   = document.f.yoy2.value;
			var yoyNm2 = document.f.yoyNm2.value;
			var yoyTA2 = document.f.yoyTA2.value;
		}catch(e){
			var yoy2   = '';
			var yoyNm2 = '';
			var yoyTA2 = '';
		}

		var sPrice   = __commaUnset(document.f.sugaCost.value);
		var ePrice   = f.svcStnd.value;


		if (svc_id == '24'){
			$sugaTime = __str2num($('#sugaTime').val()) + __str2num($('#sugaTimeNight').val());

			var nPrice   = __str2num($('#sugaCostNight').val());
			var tPrice   = __str2num($('#sugaTot').val());
			var Etime  = $('#sugaTimeNight').attr('value');
			var Egubun = (Etime > 0 ? 'Y' : '');
		}else{
			var nPrice   = f.svcCnt.value;
			var tPrice   = sPrice * timeDiff;
			var Egubun = '';
			var Etime  = 0;
		}
		
		//var Egubun   = '';
		//var Etime    = 0;

		var Ngubun   = '';
		var Ntime    = 0;		
		var visitSudangCheck = 'N';
		var visitSudang      = 0;
		var sudangYul1       = 0;
		var sudangYul2       = 0;
		var carNo            = '';
	}
	
	/****************************************
		비급여 실비 처리 구분
	 ****************************************/
		var bipay1 = 0;
		var bipay2 = 0;
		var bipay3 = 0;
		
		var exp_yn  = 'N';
		var exp_pay = 0;
		
		if (svc_id > 10 && svc_id < 30){
			bipay1 = __str2num(document.getElementById('bipay_cost1').value);
			bipay2 = __str2num(document.getElementById('bipay_cost2').value);
			bipay3 = __str2num(document.getElementById('bipay_cost3').value);
			
			exp_yn  = __object_get_value('exp_yn');
			exp_pay = __str2num(document.getElementById('exp_pay').value);
		}
		
		var bipay_kind = __object_get_value('bipay_kind');
	/****************************************/
	
	
	
	/****************************************
		산모신생아, 산모유료 추가금액(비급여)
	 ****************************************/
		var school_not_cnt = 0;
		var school_not_pay = 0;
		var school_cnt     = 0;
		var school_pay     = 0;
		var family_cnt     = 0;
		var family_pay     = 0;
		var home_in_yn     = 'N';
		var home_in_pay    = 0;
		var holiday_pay    = 0;
		
		if (svc_id == '23' || svc_id == '31'){
			school_not_cnt = __str2num(document.getElementById('school_not_cnt').value);
			school_not_pay = __str2num(document.getElementById('school_not_pay').value);
			school_cnt     = __str2num(document.getElementById('school_cnt').value);
			school_pay     = __str2num(document.getElementById('school_pay').value);
			family_cnt     = __str2num(document.getElementById('family_cnt').value);
			family_pay     = __str2num(document.getElementById('family_pay').value);
			home_in_yn     = document.getElementById('home_in_yn').checked ? 'Y' : 'N';
			home_in_pay    = __str2num(document.getElementById('home_in_pay').value);
			holiday_pay    = __str2num(document.getElementById('holiday_pay').value);
		}
	/****************************************/

	/*
	if (svc_id == '24'){
		$tempFH = parseInt(document.f.ftHour.value,10);
		$tempFM = parseInt(document.f.ftMin.value,10);
		$tempTH = parseInt(document.f.ttHour.value,10);
		$tempTM = parseInt(document.f.ttMin.value,10);

		if ($tempFH > $tempTH) $tempTH += 24;

		$procTime = (($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM)) / 60;

		//연장 최대시간은 4시간으로 제한한다.
		$prolongTime = $procTime; //연장시간
		if ($prolongTime > 4) $prolongTime = 4;

		$stndTime = $procTime - $prolongTime; //기본시간
		$sugaTime = $stndTime + $prolongTime;

		//if (newSvcSubCode == '200'){
		//	tPrice = $stndTime * 8300 + $prolongTime * 9300;
		//}
	}
	*/

	

	var max_amount  = 0;
	var suga_total  = 0;
	var over_amt_yn = 'N';

	if (!tempNew){
		if (svc_id != '11'){
			if (temp_add_suga){
				//alert(__str2num(opener.document.getElementById('max_amount').value) +'/' + __str2num(opener.document.getElementById('suga_total').value) + '/' + __str2num(sPrice));

				
				max_amount = __str2num(opener.document.getElementById('max_amount').value); 
				suga_total = __str2num(opener.document.getElementById('suga_total').value) + __str2num(sPrice);

				//alert(max_amount+'/'+suga_total+'/'+sPrice);

				if (suga_total > max_amount && bipayUmu == ''){
					over_amt_yn = 'Y';
				}
			}
		}

		var newSubject = _getCalendarSubject('ADD', newDay, newIndex, over_amt_yn);

		eval('opener.document.f.mKind_'      +newDay+'_'+newIndex).value = _get_current_svc(); //선택된 서비스 분류코드
		eval('opener.document.f.mUse_'       +newDay+'_'+newIndex).value = 'Y';
		eval('opener.document.f.mDate_'      +newDay+'_'+newIndex).value = newDate;
		eval('opener.document.f.mSvcSubCode_'+newDay+'_'+newIndex).value = newSvcSubCode;
		eval('opener.document.f.mSvcSubCD_'  +newDay+'_'+newIndex).value = svcSubCd;
		eval('opener.document.f.mFmTime_'    +newDay+'_'+newIndex).value = document.f.ftHour.value+document.f.ftMin.value;
		eval('opener.document.f.mToTime_'    +newDay+'_'+newIndex).value = document.f.ttHour.value+document.f.ttMin.value;
		eval('opener.document.f.mProcTime_'  +newDay+'_'+newIndex).value = timeDiff;

		if (svc_id == '11'){
			eval('opener.document.f.mProcStr_'+newDay+'_'+newIndex).value = document.f.procTime.value;
		}else if (svc_id == '24'){
			eval('opener.document.f.mProcStr_'+newDay+'_'+newIndex).value = $sugaTime;
		}else{
			eval('opener.document.f.mProcStr_'+newDay+'_'+newIndex).value = document.f.sugaTime.value;
		}

		eval('opener.document.f.mTogeUmu_'   +newDay+'_'+newIndex).value = togeUmu;
		eval('opener.document.f.mBiPayUmu_'  +newDay+'_'+newIndex).value = bipayUmu;
		eval('opener.document.f.mYoy1_'      +newDay+'_'+newIndex).value = document.f.yoy1.value;
		eval('opener.document.f.mYoy2_'      +newDay+'_'+newIndex).value = yoy2;
		eval('opener.document.f.mYoyNm1_'    +newDay+'_'+newIndex).value = document.f.yoyNm1.value;
		eval('opener.document.f.mYoyNm2_'    +newDay+'_'+newIndex).value = yoyNm2;
		eval('opener.document.f.mYoyTA1_'    +newDay+'_'+newIndex).value = document.f.yoyTA1.value;
		eval('opener.document.f.mYoyTA2_'    +newDay+'_'+newIndex).value = yoyTA2;
		eval('opener.document.f.mSValue_'    +newDay+'_'+newIndex).value = sPrice;
		eval('opener.document.f.mEValue_'    +newDay+'_'+newIndex).value = ePrice;
		eval('opener.document.f.mNValue_'    +newDay+'_'+newIndex).value = nPrice;
		eval('opener.document.f.mTValue_'    +newDay+'_'+newIndex).value = tPrice;
		eval('opener.document.f.mSugaCode_'  +newDay+'_'+newIndex).value = document.f.sugaCode.value;
		eval('opener.document.f.mSugaName_'  +newDay+'_'+newIndex).value = document.f.sugaName.value;
		eval('opener.document.f.mEGubun_'    +newDay+'_'+newIndex).value = Egubun;
		eval('opener.document.f.mNGubun_'    +newDay+'_'+newIndex).value = Ntime;
		eval('opener.document.f.mETime_'     +newDay+'_'+newIndex).value = Etime;
		eval('opener.document.f.mNTime_'     +newDay+'_'+newIndex).value = Ntime;
		eval('opener.document.f.mWeekDay_'   +newDay+'_'+newIndex).value = newWeek;
		eval('opener.document.f.mSubject_'   +newDay+'_'+newIndex).value = newSubject;
		eval('opener.document.f.mDuplicate_' +newDay+'_'+newIndex).value = over_amt_yn;
		eval('opener.document.f.mSeq_'       +newDay+'_'+newIndex).value = '0';
		eval('opener.document.f.mSugupja_'   +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mDelete_'    +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mTrans_'     +newDay+'_'+newIndex).value = 'N';
		eval('opener.document.f.mStatusGbn_' +newDay+'_'+newIndex).value = '9';
		eval('opener.document.f.mCarNo_'     +newDay+'_'+newIndex).value = carNo;
		eval('opener.document.f.mSudangYN_'  +newDay+'_'+newIndex).value = visitSudangCheck;
		eval('opener.document.f.mSudang_'    +newDay+'_'+newIndex).value = visitSudang;
		eval('opener.document.f.mSudangYul1_'+newDay+'_'+newIndex).value = sudangYul1;
		eval('opener.document.f.mSudangYul2_'+newDay+'_'+newIndex).value = sudangYul2;
		eval('opener.document.f.mHoliday_'   +newDay+'_'+newIndex).value = mHoliday;
		eval('opener.document.f.mModifyPos_' +newDay+'_'+newIndex).value = modifyFlag; //수정위치
		eval('opener.document.f.mOldDate_'   +newDay+'_'+newIndex).value = ''; //기존일자
		
		eval('opener.document.f.mBipay1_'+newDay+'_'+newIndex).value = bipay1; //비급여금액(요양, 바우처)
		eval('opener.document.f.mBipay2_'+newDay+'_'+newIndex).value = bipay2; //비급여금액(목욕)
		eval('opener.document.f.mBipay3_'+newDay+'_'+newIndex).value = bipay3; //비급여금액(간호)
		
		eval('opener.document.f.mExpenseYn_' +newDay+'_'+newIndex).value = exp_yn;  //실비지급여부
		eval('opener.document.f.mExpensePay_'+newDay+'_'+newIndex).value = exp_pay; //실비지급금액
		
		//추가단가
		eval('opener.document.f.mAddPay_'+newDay+'_'+newIndex).value = 'school_not_cnt='+school_not_cnt+'&school_not_cost='+school_not_pay+'&school_cnt='+school_cnt+'&school_cost='+school_pay+'&family_cnt='+family_cnt+'&family_cost='+family_pay+'&home_in_yn='+home_in_yn+'&home_in_cost='+home_in_pay+'&holiday_cost='+holiday_pay;
		
		//기타
		eval('opener.document.f.mOther_'+newDay+'_'+newIndex).value = 'bipay_kind='+bipay_kind;
		
		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.backgroundColor = '#ffffff';
		opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;
	}else{
		if (svc_id != '11'){
			if (temp_add_suga){
				max_amount = parseInt(opener.document.getElementById('max_amount').value); 
				suga_total = parseInt(opener.document.getElementById('suga_total').value);

				if (suga_total > max_amount){
					//over_amt_yn = 'Y';
				}

				if (suga_total > max_amount){
					//over_amt_yn = 'Y';
				}
			}
		}

		var newSubject = _getCalendarSubject('ADD', newDay, newIndex, over_amt_yn);

		try{
			if (document.f.togeUmu.checked) mTogeUmu = 'Y';
		}catch(e){
		}
		
		if (document.f.bipayUmu.checked){
			mBiPayUmu = 'Y';
		}
		//if (document.f.timeDoub.checked){
		//	mTimeDoub = 'Y';
		//}
		if (document.f.visitSudangCheck.checked){
			mSudangYN = 'Y';
		}

		newH  = '';
		newH += '<input name="mKind_'      +newDay+'_'+newIndex+'" type="hidden" value="'+_get_current_svc()+'">';
		newH += '<input name="mUse_'       +newDay+'_'+newIndex+'" type="hidden" value="Y">';
		newH += '<input name="mDate_'      +newDay+'_'+newIndex+'" type="hidden" value="'+newDate+'">';
		newH += '<input name="mSvcSubCode_'+newDay+'_'+newIndex+'" type="hidden" value="'+newSvcSubCode+'">';
		newH += '<input name="mSvcSubCD_'  +newDay+'_'+newIndex+'" type="hidden" value="'+svcSubCd+'">';
		newH += '<input name="mFmTime_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.ftHour.value+document.f.ftMin.value+'">';
		newH += '<input name="mToTime_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.ttHour.value+document.f.ttMin.value+'">';
		newH += '<input name="mProcTime_'  +newDay+'_'+newIndex+'" type="hidden" value="'+timeDiff+'">';

		if (svc_id == '11'){
			newH += '<input name="mProcStr_'+newDay+'_'+newIndex+'" type="hidden" value="'+document.f.procTime.value+'">';
		}else if (svc_id == '24' && mHoliday == 'Y'){
			newH += '<input name="mProcStr_'+newDay+'_'+newIndex+'" type="hidden" value="'+$sugaTime+'">';
		}else{
			newH += '<input name="mProcStr_'+newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sugaTime.value+'">';
		}

		newH += '<input name="mTogeUmu_'   +newDay+'_'+newIndex+'" type="hidden" value="'+mTogeUmu+'">';
		newH += '<input name="mBiPayUmu_'  +newDay+'_'+newIndex+'" type="hidden" value="'+mBiPayUmu+'">';
		newH += '<input name="mYoy1_'      +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoy1.value+'">';
		newH += '<input name="mYoy2_'      +newDay+'_'+newIndex+'" type="hidden" value="'+yoy2+'">';
		newH += '<input name="mYoyNm1_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyNm1.value+'">';
		newH += '<input name="mYoyNm2_'    +newDay+'_'+newIndex+'" type="hidden" value="'+yoyNm2+'">';
		newH += '<input name="mYoyTA1_'    +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.yoyTA1.value+'">';
		newH += '<input name="mYoyTA2_'    +newDay+'_'+newIndex+'" type="hidden" value="'+yoyTA2+'">';
		newH += '<input name="mSValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+sPrice+'">';
		newH += '<input name="mEValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+ePrice+'">';
		newH += '<input name="mNValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+nPrice+'">';
		newH += '<input name="mTValue_'    +newDay+'_'+newIndex+'" type="hidden" value="'+tPrice+'">';
		newH += '<input name="mSugaCode_'  +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sugaCode.value+'">';
		newH += '<input name="mSugaName_'  +newDay+'_'+newIndex+'" type="hidden" value="'+document.f.sugaName.value+'">';
		newH += '<input name="mEGubun_'    +newDay+'_'+newIndex+'" type="hidden" value="'+Egubun+'">';
		newH += '<input name="mNGubun_'    +newDay+'_'+newIndex+'" type="hidden" value="'+Ngubun+'">';
		newH += '<input name="mETime_'     +newDay+'_'+newIndex+'" type="hidden" value="'+Etime+'">';
		newH += '<input name="mNTime_'     +newDay+'_'+newIndex+'" type="hidden" value="'+Ntime+'">';
		newH += '<input name="mWeekDay_'   +newDay+'_'+newIndex+'" type="hidden" value="'+newWeek+'">';
		newH += '<input name="mSubject_'   +newDay+'_'+newIndex+'" type="hidden" value="'+newSubject+'">';
		newH += '<input name="mDuplicate_' +newDay+'_'+newIndex+'" type="hidden" value="'+over_amt_yn+'">';
		newH += '<input name="mSeq_'       +newDay+'_'+newIndex+'" type="hidden" value="0">';
		newH += '<input name="mSugupja_'   +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mDelete_'    +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mTrans_'     +newDay+'_'+newIndex+'" type="hidden" value="N">';
		newH += '<input name="mStatusGbn_' +newDay+'_'+newIndex+'" type="hidden" value="9">';
		newH += '<input name="mCarNo_'     +newDay+'_'+newIndex+'" type="hidden" value="'+carNo+'">';
		newH += '<input name="mSudangYN_'  +newDay+'_'+newIndex+'" type="hidden" value="'+mSudangYN+'">';
		newH += '<input name="mSudang_'    +newDay+'_'+newIndex+'" type="hidden" value="'+visitSudang+'">';
		newH += '<input name="mSudangYul1_'+newDay+'_'+newIndex+'" type="hidden" value="'+sudangYul1+'">';
		newH += '<input name="mSudangYul2_'+newDay+'_'+newIndex+'" type="hidden" value="'+sudangYul2+'">';
		newH += '<input name="mHoliday_'   +newDay+'_'+newIndex+'" type="hidden" value="'+mHoliday+'">';
		newH += '<input name="mModifyPos_' +newDay+'_'+newIndex+'" type="hidden" value="'+modifyFlag+'">'; //수정위치
		newH += '<input name="mOldDate_'   +newDay+'_'+newIndex+'" type="hidden" value="">'; //기존일자
		
		newH += '<input name="mBipay1_'+newDay+'_'+newIndex+'" type="hidden" value="'+bipay1+'">'; //비급여금액(요양, 바우처)
		newH += '<input name="mBipay2_'+newDay+'_'+newIndex+'" type="hidden" value="'+bipay2+'">'; //비급여금액(목욕)
		newH += '<input name="mBipay3_'+newDay+'_'+newIndex+'" type="hidden" value="'+bipay3+'">'; //비급여금액(간호)
		
		newH += '<input name="mExpenseYn_' +newDay+'_'+newIndex+'" type="hidden" value="'+exp_yn+'">';  //실비지급여부
		newH += '<input name="mExpensePay_'+newDay+'_'+newIndex+'" type="hidden" value="'+exp_pay+'">'; //실비지급금액
		
		//추가단가
		newH += '<input name=\'mAddPay_'+newDay+'_'+newIndex+'\' type=\'hidden\' value=\'school_not_cnt='+school_not_cnt+'&school_not_cost='+school_not_pay+'&school_cnt='+school_cnt+'&school_cost='+school_pay+'&family_cnt='+family_cnt+'&family_cost='+family_pay+'&home_in_yn='+home_in_yn+'&home_in_cost='+home_in_pay+'&holiday_cost='+holiday_pay+'\'>';
		
		//기타
		newH += '<input name=\'mOther_'+newDay+'_'+newIndex+'\' type=\'hidden\' value=\'bipay_kind='+bipay_kind+'\'>';
		
		opener.addCalendar.innerHTML += newH;

		eval('opener.txtSubject_'+newDay).innerHTML += newSubject;
	}

	window.onunload = function(){
		/*********************************************************
			산모신생아의 경우 추가요금계를 구한다.
		*********************************************************/
		opener._set_addpay_summly();
		//$('#svcSuga',opener).attr('value', $('#svcSuga').attr('value'));
		_setSvcInfo('add');
	}

	window.close();
}

// 수정위치
function _getModifyPos(){
	var modifyFlag = 'N';
	
	try{
		if (opener.document.getElementById('modifyFlag') != null){
			if (opener.document.getElementById('modifyFlag').value == 'DAY'){
				modifyFlag = 'D';
			}else{
				modifyFlag = 'M';
			}
		}else{
			if (document.getElementById('modifyFlag') != null){
				if (document.getElementById('modifyFlag').value == 'DAY'){
					modifyFlag = 'D';
				}else{
					modifyFlag = 'M';
				}
			}
		}
	}catch(e){
	}
	return modifyFlag;
}

/*
 * 일정수정
 */
function _modifyCalendar(){
	var newH     = opener.addCalendar.innerHTML;
	var newDay   = document.f.addDay.value;
	var newIndex = document.f.addIndex.value;
	var newDate  = document.f.addDate.value;
	var newWeek  = document.f.addWeek.value;

	var mHoliday = getHttpRequest('../inc/_check.php?gubun=getHoliday&mDate='+newDate);

	var newSvcSubCode = _getSvcSubCode();
	//var newSubject = _getCalendarSubject('MODIFY', newDay, newIndex, 'N');

	//선택된 서비스
	var svc_id   = _get_current_svc('id');
	var timeDiff = 0;

	if (newSvcSubCode == '200' && document.f.procTime.value == '0'){
		timeDiff = _getTimeDiff(document.f.ftHour.value+document.f.ftMin.value, document.f.ttHour.value+document.f.ttMin.value);
	}else{
		if (svc_id == '11'){
			timeDiff = document.f.procTime.value;
		}else{
			timeDiff = document.f.sugaTime.value;
		}
	}

	var modifyFlag = _getModifyPos(); //수정위치

	if (modifyFlag == 'D' || modifyFlag == 'M'){
		var temp_add_suga = false;
	}else{
		var temp_add_suga = true;
	}
	
	if (temp_add_suga){
		var temp_old_suga  = opener.document.getElementById('mTValue_'+newDay+'_'+newIndex).value;
		var temp_old_svc   = opener.document.getElementById('mSvcSubCode_'+newDay+'_'+newIndex).value;
		var temp_old_bipay = opener.document.getElementById('mBiPayUmu_'+newDay+'_'+newIndex).value;
		var temp_new_svc   = newSvcSubCode;
		var temp_new_bipay = document.f.bipayUmu.checked ? 'Y' : 'N';
		
		if (svc_id == '11'){
			var temp_new_suga  = __commaUnset(document.f.tPrice.value);
			
			_set_month_amount_care('modify', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_bipay, temp_new_bipay);
		}else{
			var temp_old_time  = opener.document.getElementById('mProcStr_'+newDay+'_'+newIndex).value;
			var temp_new_suga  = __commaUnset(document.f.sugaTot.value);
			var temp_new_time  = document.f.sugaTime.value;
			
			_set_month_amount_other('modify', temp_old_svc, temp_new_svc, temp_old_suga, temp_new_suga, temp_old_time, temp_new_time, temp_old_bipay, temp_new_bipay);
		}
	}

	var bipayUmu = document.f.bipayUmu.checked ? document.f.bipayUmu.value : '';

	if (svc_id == '11'){
		var procTime = document.f.procTime.value;
		var svcSubCd = document.f.svcSubCD.value;
		var togeUmu  = document.f.togeUmu.checked ? document.f.togeUmu.value : '';
		var yoy2     = document.f.yoy2.value;
		var yoyNm2   = document.f.yoyNm2.value;
		var yoyTA2   = document.f.yoyTA2.value;
		var sPrice   = __commaUnset(document.f.sPrice.value);
		var ePrice   = __commaUnset(document.f.ePrice.value);
		var nPrice   = __commaUnset(document.f.nPrice.value);
		var tPrice   = __commaUnset(document.f.tPrice.value);
		var Egubun   = document.f.Egubun.value;
		var Ngubun   = document.f.Ngubun.value;
		var Etime    = document.f.Etime.value;
		var Ntime    = document.f.Ntime.value;
		var visitSudangCheck = (document.f.visitSudangCheck.checked ? "Y" : "N");
		var visitSudang      = document.f.visitSudang.value;
		var sudangYul1       = document.f.sudangYul1.value;
		var sudangYul2       = document.f.sudangYul2.value;
		var carNo            = document.f.carNo.value;
	}else{
		var procTime = document.f.sugaTime.value;
		var svcSubCd = '1';
		var togeUmu  = '';

		try{
			var yoy2   = document.f.yoy2.value;
			var yoyNm2 = document.f.yoyNm2.value;
			var yoyTA2 = document.f.yoyTA2.value;
		}catch(e){
			var yoy2   = '';
			var yoyNm2 = '';
			var yoyTA2 = '';
		}

		var sPrice   = __commaUnset(document.f.sugaCost.value);
		var ePrice   = f.svcStnd.value;
		

		if (svc_id == '24'){
			$sugaTime = __str2num($('#sugaTime').val()) + __str2num($('#sugaTimeNight').val());

			var nPrice   = __str2num($('#sugaCostNight').val());
			var tPrice   = __str2num($('#sugaTot').val());
			var Etime  = $('#sugaTimeNight').attr('value');
			var Egubun = (Etime > 0 ? 'Y' : '');
		}else{
			var nPrice   = f.svcCnt.value;
			var tPrice   = sPrice * timeDiff;
			var Egubun = '';
			var Etime  = 0;
		}

		var Ngubun   = '';
		var Ntime    = 0;
		var visitSudangCheck = 'N';
		var visitSudang      = 0;
		var sudangYul1       = 0;
		var sudangYul2       = 0;
		var carNo            = '';
	}
	
	var max_amount  = 0;
	var suga_total  = 0;
	var over_amt_yn = 'N';
	
	if (svc_id != '11'){
		if (temp_add_suga){
			max_amount = __str2num(opener.document.getElementById('max_amount').value); 
			suga_total = __str2num(opener.document.getElementById('suga_total').value) + __str2num(sPrice);
			
			if (suga_total > max_amount && bipayUmu == ''){
				over_amt_yn = 'Y';
			}
		}
	}
	
	var newSubject = _getCalendarSubject('MODIFY', newDay, newIndex, over_amt_yn);
	
	/****************************************
		비급여 실비 처리 구분
	 ****************************************/
		var bipay1 = 0;
		var bipay2 = 0;
		var bipay3 = 0;
		
		var exp_yn  = 'N';
		var exp_pay = 0;
			
		if (svc_id > 10 && svc_id < 30){
			bipay1 = __str2num(document.getElementById('bipay_cost1').value);
			bipay2 = __str2num(document.getElementById('bipay_cost2').value);
			bipay3 = __str2num(document.getElementById('bipay_cost3').value);
			
			exp_yn  = __object_get_value('exp_yn');
			exp_pay = __str2num(document.getElementById('exp_pay').value);
		}
		
		var bipay_kind = __object_get_value('bipay_kind');
	/****************************************/
	
	/****************************************
		산모신생아, 산모유료 추가금액(비급여)
	 ****************************************/
		var school_not_cnt = 0;
		var school_not_pay = 0;
		var school_cnt     = 0;
		var school_pay     = 0;
		var family_cnt     = 0;
		var family_pay     = 0;
		var home_in_yn     = 'N';
		var home_in_pay    = 0;
		var holiday_pay    = 0;
		
		if (svc_id == '23' || svc_id == '31'){
			school_not_cnt = __str2num(document.getElementById('school_not_cnt').value);
			school_not_pay = __str2num(document.getElementById('school_not_pay').value);
			school_cnt     = __str2num(document.getElementById('school_cnt').value);
			school_pay     = __str2num(document.getElementById('school_pay').value);
			family_cnt     = __str2num(document.getElementById('family_cnt').value);
			family_pay     = __str2num(document.getElementById('family_pay').value);
			home_in_yn     = document.getElementById('home_in_yn').checked ? 'Y' : 'N';
			home_in_pay    = __str2num(document.getElementById('home_in_pay').value);
			holiday_pay    = __str2num(document.getElementById('holiday_pay').value);
		}
	/****************************************/


	/*
	if (svc_id == '24'){
		//if (mHoliday == 'Y'){
			$tempFH = parseInt(document.f.ftHour.value,10);
			$tempFM = parseInt(document.f.ftMin.value,10);
			$tempTH = parseInt(document.f.ttHour.value,10);
			$tempTM = parseInt(document.f.ttMin.value,10);

			if ($tempFH > $tempTH) $tempTH += 24;

			$procTime = (($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM)) / 60;

			//연장 최대시간은 4시간으로 제한한다.
			$prolongTime = $procTime; //연장시간
			if ($prolongTime > 4) $prolongTime = 4;

			$stndTime = $procTime - $prolongTime; //기본시간
			procTime = $stndTime + $prolongTime;

			//if (newSvcSubCode == '200'){
			//	tPrice   = $stndTime * 8300 + $prolongTime * 9300;
			//}
		//}
		
	}
	*/
	
	eval('opener.document.f.mKind_'      +newDay+'_'+newIndex).value = _get_current_svc(); //선택된 서비스 분류코드
	eval('opener.document.f.mUse_'       +newDay+'_'+newIndex).value = 'Y';
	eval('opener.document.f.mDate_'      +newDay+'_'+newIndex).value = newDate;
	eval('opener.document.f.mSvcSubCode_'+newDay+'_'+newIndex).value = newSvcSubCode;
	eval('opener.document.f.mSvcSubCD_'  +newDay+'_'+newIndex).value = svcSubCd;
	eval('opener.document.f.mFmTime_'    +newDay+'_'+newIndex).value = document.f.ftHour.value+document.f.ftMin.value;
	eval('opener.document.f.mToTime_'    +newDay+'_'+newIndex).value = document.f.ttHour.value+document.f.ttMin.value;
	eval('opener.document.f.mProcTime_'  +newDay+'_'+newIndex).value = timeDiff; //document.f.procTime.value;
	eval('opener.document.f.mProcStr_'   +newDay+'_'+newIndex).value = procTime;
	eval('opener.document.f.mTogeUmu_'   +newDay+'_'+newIndex).value = togeUmu;
	eval('opener.document.f.mBiPayUmu_'  +newDay+'_'+newIndex).value = bipayUmu;
	eval('opener.document.f.mYoy1_'      +newDay+'_'+newIndex).value = document.f.yoy1.value;
	eval('opener.document.f.mYoy2_'      +newDay+'_'+newIndex).value = yoy2;
	eval('opener.document.f.mYoyNm1_'    +newDay+'_'+newIndex).value = document.f.yoyNm1.value;
	eval('opener.document.f.mYoyNm2_'    +newDay+'_'+newIndex).value = yoyNm2;
	eval('opener.document.f.mYoyTA1_'    +newDay+'_'+newIndex).value = document.f.yoyTA1.value;
	eval('opener.document.f.mYoyTA2_'    +newDay+'_'+newIndex).value = yoyTA2;
	eval('opener.document.f.mSValue_'    +newDay+'_'+newIndex).value = sPrice;
	eval('opener.document.f.mEValue_'    +newDay+'_'+newIndex).value = ePrice;
	eval('opener.document.f.mNValue_'    +newDay+'_'+newIndex).value = nPrice;
	eval('opener.document.f.mTValue_'    +newDay+'_'+newIndex).value = tPrice;
	eval('opener.document.f.mSugaCode_'  +newDay+'_'+newIndex).value = document.f.sugaCode.value;
	eval('opener.document.f.mSugaName_'  +newDay+'_'+newIndex).value = document.f.sugaName.value;
	eval('opener.document.f.mEGubun_'    +newDay+'_'+newIndex).value = Egubun;
	eval('opener.document.f.mNGubun_'    +newDay+'_'+newIndex).value = Ngubun;
	eval('opener.document.f.mETime_'     +newDay+'_'+newIndex).value = Etime;
	eval('opener.document.f.mNTime_'     +newDay+'_'+newIndex).value = Ntime;
	eval('opener.document.f.mWeekDay_'   +newDay+'_'+newIndex).value = newWeek;
	eval('opener.document.f.mSubject_'   +newDay+'_'+newIndex).value = newSubject;
	eval('opener.document.f.mDuplicate_' +newDay+'_'+newIndex).value = over_amt_yn;
	eval('opener.document.f.mCarNo_'     +newDay+'_'+newIndex).value = sudangYul2;
	eval('opener.document.f.mSudangYN_'  +newDay+'_'+newIndex).value = visitSudangCheck;
	eval('opener.document.f.mSudang_'    +newDay+'_'+newIndex).value = visitSudang;
	eval('opener.document.f.mSudangYul1_'+newDay+'_'+newIndex).value = sudangYul1;
	eval('opener.document.f.mSudangYul2_'+newDay+'_'+newIndex).value = sudangYul2;
	eval('opener.document.f.mHoliday_'   +newDay+'_'+newIndex).value = mHoliday;
	eval('opener.document.f.mModifyPos_' +newDay+'_'+newIndex).value = modifyFlag; //수정위치

	//eval('opener.document.f.mSeq_'       +newDay+'_'+newIndex).value = '0';
	//eval('opener.document.f.mSugupja_'   +newDay+'_'+newIndex).value = 'N';
	//eval('opener.document.f.mDelete_'    +newDay+'_'+newIndex).value = 'N';
	eval('opener.document.f.mTrans_'+newDay+'_'+newIndex).value = 'N';
	//eval('opener.document.f.mStatusGbn_' +newDay+'_'+newIndex).value = '9';
	
	eval('opener.document.f.mBipay1_'+newDay+'_'+newIndex).value = bipay1;
	eval('opener.document.f.mBipay2_'+newDay+'_'+newIndex).value = bipay2;
	eval('opener.document.f.mBipay3_'+newDay+'_'+newIndex).value = bipay3;
	
	eval('opener.document.f.mExpenseYn_' +newDay+'_'+newIndex).value = exp_yn;
	eval('opener.document.f.mExpensePay_'+newDay+'_'+newIndex).value = exp_pay;
	
	//추가단가
	eval('opener.document.f.mAddPay_'+newDay+'_'+newIndex).value = 'school_not_cnt='+school_not_cnt+'&school_not_cost='+school_not_pay+'&school_cnt='+school_cnt+'&school_cost='+school_pay+'&family_cnt='+family_cnt+'&family_cost='+family_pay+'&home_in_yn='+home_in_yn+'&home_in_cost='+home_in_pay+'&holiday_cost='+holiday_pay;
	
	//기타
	eval('opener.document.f.mOther_'+newDay+'_'+newIndex).value = 'bipay_kind='+bipay_kind;
	
	//var tempSubject = _setSubject(newDay, newIndex, 'opener.');

	//eval('opener.txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;
	//opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).style.display = '';
	opener.document.getElementById('txtSubject_'+newDay+'_'+newIndex).innerHTML = newSubject;
	
	window.onunload = function(){
		/*********************************************************
			산모신생아의 경우 추가요금계를 구한다.
		*********************************************************/
		opener._set_addpay_summly();
		_setSvcInfo('modify');
	}

	window.close();
}

// 일정표 제목
function _getCalendarSubject(gubun, pDay, pIndex, pOverAmt){
	var newSubject = '';
	var newSTable = '';
	var newBorderTop = '';
	var svcSubCode = _getSvcSubCode();

	//선택된 서비스
	var svc_id = _get_current_svc('id');

	newSTable  = document.f.ftHour.value+':'+document.f.ftMin.value+'~';
	newSTable += document.f.ttHour.value+':'+document.f.ttMin.value+'<br>';

	newSTable += document.f.yoyNm1.value != '' ? document.f.yoyNm1.value+',' : '';
	
	if (svc_id == 11 || svc_id == 24){
		newSTable += document.f.yoyNm2.value != '' ? document.f.yoyNm2.value+',' : '';
	}

	newSTable = newSTable.substring(0, newSTable.length-1)+'<br>'+document.f.sugaName.value;
	
	var bipay_yn = (document.f.bipayUmu.checked ? 'Y' : 'N');

	if (bipay_yn == 'Y'){
		pOverAmt   = 'N';
		newSTable += '<span style=\'font-size:8pt; color:#ff0000;\'>[비]</span>';
	}

	if (pIndex != '1' && gubun == 'ADD'){
		newBorderTop = 'border-top:1px dotted #cccccc;';
	}

	var background_color = '';
	
	if (pOverAmt == 'Y'){
		//background_color = 'margin-top:5px; margin-bottom:5px; border:2px solid #ff0000;';
	}

	newSubject  = "<div id='txtSubject_"+pDay+"_"+pIndex+"' class='svcSubject"+svc_id+"' style='display:; "+newBorderTop+" "+background_color+"'>";
	newSubject += "<table>";
	newSubject += "  <tr>";
	newSubject += "    <td class='noborder' style='width:100%; text-align:left; vertical-align:top; line-height:1.3em; border:none;'>";
	newSubject += "      <div style='position:absolute; width:100%; height:100%;'>";
	newSubject += "        <div style='position:absolute; top:1px; left:80px;'>";
	newSubject += "          <img class='svcSubjectBtn"+svc_id+"' src='../image/btn_edit.png' style='cursor:pointer;' onClick='_modifyDiary("+pDay+","+pIndex+");'>";
	newSubject += "          <img class='svcSubjectBtn"+svc_id+"' src='../image/btn_del.png' style='cursor:pointer;' onClick='_clearDiary("+pDay+","+pIndex+");'>";
	newSubject += "        </div>";
	newSubject += "      </div>";
	newSubject += "      <div>"+newSTable+"</div>";
	
	/*
	if (pOverAmt == 'Y'){
		newSubject += "      <div id='checkDuplicate_"+pDay+"_"+pIndex+"' style='display:none;'>중복</div>";
		newSubject += "      <div id='checkSugupja_"+pDay+"_"+pIndex+"' style='display:;'>한도초과</div>";
	}else{
		newSubject += "      <div id='checkDuplicate_"+pDay+"_"+pIndex+"' style='display:none;'>중복</div>";
		newSubject += "      <div id='checkSugupja_"+pDay+"_"+pIndex+"' style='display:none;'>타수급자중복</div>";
	}
	*/
	
	if (pOverAmt == 'Y'){
		newSubject += '<div id=\'checkDuplicate_'+pDay+'\'_\''+pIndex+'\' style=\'display:; cursor:pointer;\' onclick=\'_chk_iljung('+pDay+','+pIndex+');\'><span style=\'color:#ff0000; font-weight:bold;\'>한도가 초과되었습니다.저장안됨</span></div>';
	}
	
	newSubject += "      <div id='checkDuplicate_"+pDay+"_"+pIndex+"' style='display:none;'></div>";
	newSubject += "      <div id='checkSugupja_"+pDay+"_"+pIndex+"' style='display:;'></div>";

	newSubject += "    </td>";
	newSubject += "  </tr>";
	newSubject += "</table>";
	newSubject += "</div>";

	return newSubject;
}

// 수급자 월수급 현황 계산
function _addYoySudangList(exec_pos){
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
							//if (yoyInfo[i]['YoyCD3']  == eval('document.f.mYoy3_'+mDay+'_'+mIndex).value){
							//if (yoyInfo[i]['YoyCD4']  == eval('document.f.mYoy4_'+mDay+'_'+mIndex).value){
							//if (yoyInfo[i]['YoyCD5']  == eval('document.f.mYoy5_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['SugaCD']  == eval('document.f.mSugaCode_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['FmTime']  == eval('document.f.mFmTime_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['ToTime']  == eval('document.f.mToTime_'+mDay+'_'+mIndex).value){
							if (yoyInfo[i]['Holiday'] == mHoliday){
								newFlag = false;
								dayIndex = i;
								break;}}}}}//}}}
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
						//yoyInfo[dayIndex]['YoyCD3'] = eval('document.f.mYoy3_'+mDay+'_'+mIndex).value; //요양사3
						//yoyInfo[dayIndex]['YoyCD4'] = eval('document.f.mYoy4_'+mDay+'_'+mIndex).value; //요양사4
						//yoyInfo[dayIndex]['YoyCD5'] = eval('document.f.mYoy5_'+mDay+'_'+mIndex).value; //요양사5

						yoyInfo[dayIndex]['YoyNM1'] = eval('document.f.mYoyNm1_'+mDay+'_'+mIndex).value; //요양사명1
						yoyInfo[dayIndex]['YoyNM2'] = eval('document.f.mYoyNm2_'+mDay+'_'+mIndex).value; //요양사명2
						//yoyInfo[dayIndex]['YoyNM3'] = eval('document.f.mYoyNm3_'+mDay+'_'+mIndex).value; //요양사명3
						//yoyInfo[dayIndex]['YoyNM4'] = eval('document.f.mYoyNm4_'+mDay+'_'+mIndex).value; //요양사명4
						//yoyInfo[dayIndex]['YoyNM5'] = eval('document.f.mYoyNm5_'+mDay+'_'+mIndex).value; //요양사명5

						yoyInfo[dayIndex]['YoyTA1'] = parseInt(eval('document.f.mYoyTA1_'+mDay+'_'+mIndex).value) + (eval('document.f.mYoyTA1_'+mDay+'_'+mIndex).value * sugaRate); //요양사시급1
						yoyInfo[dayIndex]['YoyTA2'] = eval('document.f.mYoyTA2_'+mDay+'_'+mIndex).value; //요양사시급2
						//yoyInfo[dayIndex]['YoyTA3'] = eval('document.f.mYoyTA3_'+mDay+'_'+mIndex).value; //요양사시급3
						//yoyInfo[dayIndex]['YoyTA4'] = eval('document.f.mYoyTA4_'+mDay+'_'+mIndex).value; //요양사시급4
						//yoyInfo[dayIndex]['YoyTA5'] = eval('document.f.mYoyTA5_'+mDay+'_'+mIndex).value; //요양사시급5

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

						if (eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == nursingTimeList[0]['cd'] ||
							eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == nursingTimeList[1]['cd'] ||
							eval('document.f.mProcTime_'+mDay+'_'+mIndex).value == nursingTimeList[2]['cd']){
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

						//if (yoyInfo[dayIndex]['YoyNM3'] != ''){
						//	yoyInfo[dayIndex]['YoyCount']++;
						//	yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM3'];
						//}
						//if (yoyInfo[dayIndex]['YoyNM4'] != ''){
						//	yoyInfo[dayIndex]['YoyCount']++;
						//	yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM4'];
						//}
						//if (yoyInfo[dayIndex]['YoyNM5'] != ''){
						//	yoyInfo[dayIndex]['YoyCount']++;
						//	yoyInfo[dayIndex]['YoyAlt'] += ', '+yoyInfo[dayIndex]['YoyNM5'];
						//}

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

	if (exec_pos == 1) return;

	/*
	 * 2011.03.30 계산시 "요양보호사 수당현황"만 계산하고
	 * 아래의 "수급자 월수급 현황"은 계산하지않는다.
	 */

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

function _client_month_pay_stat(){
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

	// 데이타 시작
	var mLastDay = document.f.mLastDay.value;
	var mUse, mDuplicate, mDelete, mYoyangsa, mStatus, mHoliday, mGoto;
	var mIndex = 1;
	var checkLoop = true;
	var checkIndex = 0;
	var dayIndex = 0;
	var sugaRate = 0;
	var sugaPrice = 0;
	var svcSubCode = ''

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
				
				if (mUse.value == 'Y' && mDuplicate.value == 'N' && mDelete.value == 'N' && mYoyangsa.value != '' && mGoto == 'Y'){
					sugaPrice  = parseInt(eval('document.f.mTValue_'+mDay+'_'+mIndex).value, 10);
					svcSubCode = eval('document.f.mSvcSubCode_'+mDay+'_'+mIndex).value;

					if (eval('document.f.mBiPayUmu_'+mDay+'_'+mIndex).value == 'Y'){
						switch(svcSubCode){
							case '200': amtBiPay200 += sugaPrice; break;
							case '500': amtBiPay500 += sugaPrice; break;
							case '800': amtBiPay800 += sugaPrice; break;
						}
					}else{
						maxTempAmt = amtSugub200 + amtSugub500 + amtSugub800 + sugaPrice;
						if (MaxAmount > maxTempAmt){
							switch(svcSubCode){
								case '200': amtSugub200 += sugaPrice; break;
								case '500': amtSugub500 += sugaPrice; break;
								case '800': amtSugub800 += sugaPrice; break;
							}
						}else{
							if (MaxAmount >= amtSugub200 + amtSugub500 + amtSugub800){
								maxTempPrc = (MaxAmount - (amtSugub200 + amtSugub500 + amtSugub800));
								
								switch(svcSubCode){
									case '200': amtSugub200 += maxTempPrc; break;
									case '500': amtSugub500 += maxTempPrc; break;
									case '800': amtSugub800 += maxTempPrc; break;
								}
							}

							switch(svcSubCode){
								case '200': amtOver200 += (sugaPrice - maxTempPrc); break;
								case '500': amtOver500 += (sugaPrice - maxTempPrc); break;
								case '800': amtOver800 += (sugaPrice - maxTempPrc); break;
							}
						}
					}
					
					if (eval('document.f.mBiPayUmu_'+mDay+'_'+mIndex).value != 'Y'){
						// 비급여이면 본인부담액을 포함하지 않는다.
						switch(svcSubCode){
							case '200': amtBonin200 += Math.floor(sugaPrice * boninYul / 100); break;
							case '500': amtBonin500 += Math.floor(sugaPrice * boninYul / 100); break;
							case '800': amtBonin800 += Math.floor(sugaPrice * boninYul / 100); break;
						}
					}
				}
			}
			mIndex ++;
		}
	}

	alert(amtSugub200+amtSugub500+amtSugub800);
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
	/*
	if (!getDateYN()){
		alert('과거의 일정은 계산하실 수 없습니다.');
		return;
	}
	*/

	_addYoySudangList(1);

	document.f.pressCal.value = 'Y';
}

function _iljungSubmit(){
	/*
	if (!getDateYN()){
		alert('과거의 일정은 저장하실 수 없습니다.');
		return;
	}
	*/
	/*
	 * 2011.03.30 계산처리를 제외한다.
	if (document.f.pressCal.value == 'N'){
		alert('계산처리 후 저장하여 주십시오.');
		return;
	}
	*/

	if (!_newType()){
		var svc_id = '11';
	}else{
		var svc_id = _get_current_svc('id');
		
		if (svc_id > 10 && svc_id < 20){
			var action = document.f._SAVE_CARE_.value;
		}else{
			var action = document.f._SAVE_VOUCHER_.value;
		}

		document.f.action = action;
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
	
	var mCode		= document.f.mCode.value;
	var mKind		= document.f.mKind.value;
	var mJuminNo	= document.f.mJuminNo.value;
	var mDate		= nowYear+nowMonth;

	try{
		var nowDate = document.f.calYear.options[document.f.calYear.selectedIndex].text+document.f.calMonth.options[document.f.calMonth.selectedIndex].text;
	}catch(e){
		var nowDate = document.f.calYear.value+document.f.calMonth.value;
	}

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

// 일정삭제
function _delete_iljung(){
	try{
		var closing_yn = document.f.closing_yn.value;
	}catch(e){
		var closing_yn = 'N';
	}
	
	if (closing_yn == 'Y'){
		alert('실적마감처리되어 등록,수정,삭제를 할 수 없습니다.');
		return;
	}

	if (!confirm('진행되지 않은 일정만 삭제됩니다. 삭제된 일정은 복구할 수 없습니다. 정말로 삭제하시겠습니까?')) return;

	document.f.action = 'iljung_delete_ok.php';
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
	if (!_newType()){
		var code   = document.f.mCode.value;
		var kind   = document.f.mKind.value;
		var jumin  = document.f.mJuminNo.value;
		var svc_id = '11';
	}else{
		var code   = document.f.code.value;
		var kind   = document.f.kind.value;
		var jumin  = document.f.jumin.value;
		var svc_id = _get_current_svc('id');
	}

	//재가요양만 패턴을 저장한다.
	//if (code != '1234' && svc_id != '11') return;

	var suvCD = '', carNO = '', familyYN = 'N';
	var visitExpenseYN = 'N', visitExpensePay = 0, visitExpenseRate1 = 0, visitExpenseRate2 = 0;
	var sugaPay = 0, eveingPay = 0, nightPay = 0, totalPay = 0;
	var sugaCD = '', sugaNM = '';
	var eveingYN = 'N', nightYN = 'N';
	var eveingTime = 0, nightTime = 0;

	if (svc_id == '11'){
		suvCD    = document.f.svcSubCD.value;
		carNO    = document.f.carNo.value;
		familyYN = document.f.togeUmu.checked ? "Y":"N";

		visitExpenseYN    = document.f.visitSudangCheck.checked?"Y":"N";
		visitExpensePay   = __str2num(document.f.visitSudang.value);
		visitExpenseRate1 = document.f.sudangYul1.value;
		visitExpenseRate2 = document.f.sudangYul2.value;

		sugaPay   = __str2num(document.f.sPrice.value);
		eveingPay = __str2num(document.f.ePrice.value);
		nightPay  = __str2num(document.f.nPrice.value);
		totalPay  = __str2num(document.f.tPrice.value);

		sugaCD = document.f.sugaCode.value;
		sugaNM = document.f.sugaName.value;
		
		eveingYN = document.f.Egubun.value;
		nightYN  = document.f.Ngubun.value;

		eveingTime = document.f.Etime.value;
		nightTime  = document.f.Ntime.value;
	}else if (svc_id == '24'){
		sugaPay   = __str2num(document.f.sugaCost.value);
		nightPay  = __str2num(document.f.sugaCostNight.value);
		totalPay  = __str2num(document.f.sugaTot.value);

		sugaCD = document.f.sugaCode.value;
		sugaNM = $('#sugaCont').text();
		
		nightTime  = document.f.sugaTimeNight.value;
	}else{
		sugaPay  = __str2num(document.f.sugaCost.value);
		totalPay = __str2num(document.f.sugaTot.value);

		sugaCD = document.f.sugaCode.value;
		sugaNM = $('#sugaCont').text();
	}


	try{
		var memCD2 = document.f.yoy2.value;
		var memNM2 = document.f.yoyNm2.value;
		var memTA2 = document.f.yoyTA2.value;
	}catch(e){
		var memCD2 = '';
		var memNM2 = '';
		var memTA2 = '';
	}


	var URL = 'pattern_ok.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				p_code			:code
			,	p_kind			:kind
			,	p_jumin			:jumin
			,	p_ym			:document.f.copyYear.value+document.f.copyMonth.value
			,	p_svc_subcode	:_getSvcSubCode()
			,	p_svc_subcd		:suvCD
			,	p_car_no		:carNO
			,	p_sugup_fmtime	:document.f.ftHour.value+document.f.ftMin.value
			,	p_sugup_totime	:document.f.ttHour.value+document.f.ttMin.value
			,	p_sugup_soyotime:document.f.procTime.value
			,	p_family_gbn	:familyYN
			,	p_bipay_gbn		:document.f.bipayUmu.checked?"Y":"N"
			,	p_week_day1		:document.f.weekDay1.checked?"Y":"N"
			,	p_week_day2		:document.f.weekDay2.checked?"Y":"N"
			,	p_week_day3		:document.f.weekDay3.checked?"Y":"N"
			,	p_week_day4		:document.f.weekDay4.checked?"Y":"N"
			,	p_week_day5		:document.f.weekDay5.checked?"Y":"N"
			,	p_week_day6		:document.f.weekDay6.checked?"Y":"N"
			,	p_week_day0		:document.f.weekDay0.checked?"Y":"N"
			,	p_week_use1		:document.f.weekDay1.disabled?"N":"Y"
			,	p_week_use2		:document.f.weekDay2.disabled?"N":"Y"
			,	p_week_use3		:document.f.weekDay3.disabled?"N":"Y"
			,	p_week_use4		:document.f.weekDay4.disabled?"N":"Y"
			,	p_week_use5		:document.f.weekDay5.disabled?"N":"Y"
			,	p_week_use6		:document.f.weekDay6.disabled?"N":"Y"
			,	p_week_use0		:document.f.weekDay0.disabled?"N":"Y"

			,	p_svc_dt_1		:document.f.svc_dt_1.value
			,	p_svc_dt_2		:document.f.svc_dt_2.value
			,	p_svc_dt_3		:document.f.svc_dt_3.value
			,	p_svc_dt_4		:document.f.svc_dt_4.value
			,	p_svc_dt_5		:document.f.svc_dt_5.value
			,	p_svc_dt_6		:document.f.svc_dt_6.value
			,	p_svc_dt_7		:document.f.svc_dt_7.value
			,	p_svc_dt_8		:document.f.svc_dt_8.value
			,	p_svc_dt_9		:document.f.svc_dt_9.value
			,	p_svc_dt_10		:document.f.svc_dt_10.value
			,	p_svc_dt_11		:document.f.svc_dt_11.value
			,	p_svc_dt_12		:document.f.svc_dt_12.value
			,	p_svc_dt_13		:document.f.svc_dt_13.value
			,	p_svc_dt_14		:document.f.svc_dt_14.value
			,	p_svc_dt_15		:document.f.svc_dt_15.value
			,	p_svc_dt_16		:document.f.svc_dt_16.value
			,	p_svc_dt_17		:document.f.svc_dt_17.value
			,	p_svc_dt_18		:document.f.svc_dt_18.value
			,	p_svc_dt_19		:document.f.svc_dt_19.value
			,	p_svc_dt_20		:document.f.svc_dt_20.value
			,	p_svc_dt_21		:document.f.svc_dt_21.value
			,	p_svc_dt_22		:document.f.svc_dt_22.value
			,	p_svc_dt_23		:document.f.svc_dt_23.value
			,	p_svc_dt_24		:document.f.svc_dt_24.value
			,	p_svc_dt_25		:document.f.svc_dt_25.value
			,	p_svc_dt_26		:document.f.svc_dt_26.value
			,	p_svc_dt_27		:document.f.svc_dt_27.value
			,	p_svc_dt_28		:document.f.svc_dt_28.value
			,	p_svc_dt_29		:document.getElementById('svc_dt_29') != null ? document.getElementById('svc_dt_29').value : ''
			,	p_svc_dt_30		:document.getElementById('svc_dt_30') != null ? document.getElementById('svc_dt_30').value : ''
			,	p_svc_dt_31		:document.getElementById('svc_dt_31') != null ? document.getElementById('svc_dt_31').value : ''

			,	p_yoy_jumin1	:document.f.yoy1.value
			,	p_yoy_name1		:document.f.yoyNm1.value
			,	p_yoy_ta1		:document.f.yoyTA1.value

			,	p_yoy_jumin2	:memCD2 //document.f.yoy2.value
			,	p_yoy_name2		:memNM2 //document.f.yoyNm2.value
			,	p_yoy_ta2		:memTA2 //document.f.yoyTA2.value
				
			,	p_visit_chk		:visitExpenseYN //document.f.visitSudangCheck.checked?"Y":"N"
			,	p_visit_amt		:visitExpensePay //__commaUnset(document.f.visitSudang.value)
			,	p_sudang_yul1	:visitExpenseRate1 //document.f.sudangYul1.value
			,	p_sudang_yul2	:visitExpenseRate2 //document.f.sudangYul2.value

			,	p_price_s		:sugaPay  //__commaUnset(document.f.sPrice.value)
			,	p_price_e		:eveingPay  //__commaUnset(document.f.ePrice.value)
			,	p_price_n		:nightPay  //__commaUnset(document.f.nPrice.value)
			,	p_price_t		:totalPay  //__commaUnset(document.f.tPrice.value)
			,	p_suga_code		:sugaCD  //document.f.sugaCode.value
			,	p_suga_name		:sugaNM  //document.f.sugaName.value
			,	p_gubun_e		:eveingYN  //document.f.Egubun.value
			,	p_gubun_n		:nightYN  //document.f.Ngubun.value
			,	p_time_e		:eveingTime  //document.f.Etime.value
			,	p_time_n		:nightTime  //document.f.Ntime.value
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
	
	document.f.yoyNm1.value = document.getElementsByName("p_yoy_name1[]")[p_index].value;
	document.f.yoyNm2.value = document.getElementsByName("p_yoy_name2[]")[p_index].value;
	
	document.f.yoyTA1.value = document.getElementsByName("p_yoy_ta1[]")[p_index].value;
	document.f.yoyTA2.value = document.getElementsByName("p_yoy_ta2[]")[p_index].value;
	
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
	document.f.ftMin.value  = document.getElementsByName("p_sugup_fmtime[]")[p_index].value.substring(2,4);

	document.f.ttHour.value = document.getElementsByName("p_sugup_totime[]")[p_index].value.substring(0,2);
	document.f.ttMin.value  = document.getElementsByName("p_sugup_totime[]")[p_index].value.substring(2,4);

	document.f.svcSubCD.value = document.getElementsByName("p_svc_subcd[]")[p_index].value;
	document.f.carNo.value    = document.getElementsByName("p_car_no[]")[p_index].value;
	
	document.f.procTime.value = document.getElementsByName("p_sugup_soyotime[]")[p_index].value;

	if (document.getElementsByName("p_svc_subcode[]")[p_index].value == '500'){
		_setSvc2Sub();
		document.f.svcSubCD.value = document.getElementsByName("p_svc_subcd[]")[p_index].value;
	}
	
	document.f.togeUmu.checked  = (document.getElementsByName("p_family_gbn[]")[p_index].value == "Y" ? true : false);
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
	
	for(var i=1; i<=31; i++){
		if (document.getElementById('svc_dt_'+i) != null){
			if (document.getElementsByName('p_svc_dt_'+i+'[]')[p_index].value == 'Y'){
				document.getElementById('svc_dt_'+i).value = 'Y';
				document.getElementById('str_svc_dt_'+i).className = 'my_box my_box_2';
				document.getElementById('svc_in_type').value = 'date';
			}else{
				document.getElementById('svc_dt_'+i).value = 'N';
				document.getElementById('str_svc_dt_'+i).className = 'my_box my_box_1';
			}
		}
	}

	_setIljungSuga();
	_patternClose(p_body);
}


/*********************************************************
	
	바우처 패턴리스트 입력

*********************************************************/
function _putPattern(body, idx){
	if ($('#code').attr('value') != '1234') return;

	var tmp = $('#pattern_'+idx).attr('value').split('&');
	var i, obj, str;
	var pattern = new Array();

	for(i=0; i<tmp.length; i++){
		obj = tmp[i].split('=');
		pattern[obj[0]] = obj[1];
	}
	
	alert(pattern['svcCD']);

	//제공서비스
	$('#svcSubCode_'+pattern['svcCD']).attr('checked', 'checked');
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

// 수급자 월수급 현황 수정
function _set_month_amount_care(type, old_svc, new_svc, old_suga, new_suga, old_bipay, new_bipay){
	if (type == 'delete' || type == 'insert'){
		var obj_opener = document;
	}else{
		var obj_opener = opener.document;
	}

	var suga_total = obj_opener.getElementById('suga_total');

	var max_amount = parseFloat(obj_opener.getElementById('max_amount').value, 10);	//급여한도액
	var bonin_yul  = parseFloat(obj_opener.getElementById('bonin_yul').value, 10);	//본인부담율
	
	var obj_total = null;
	var obj_bonin = null;
	var obj_over  = null;
	var obj_bipay = null;
	var obj_sum   = null;
	var obj_sur	  = null;
	
	var amt_total = 0;
	var amt_bonin = 0;
	var amt_over  = 0;
	var amt_bipay = 0;
	var amt_sum   = 0;

	old_suga = parseFloat(old_suga, 10);
	new_suga = parseFloat(new_suga, 10);

	if (type == 'modify' || type == 'delete'){
		obj_total = obj_opener.getElementById('txt_'+old_svc+'_total');
		obj_bonin = obj_opener.getElementById('txt_'+old_svc+'_bonin');
		obj_over  = obj_opener.getElementById('txt_'+old_svc+'_over');
		obj_bipay = obj_opener.getElementById('txt_'+old_svc+'_bipay');
		obj_sum   = obj_opener.getElementById('txt_'+old_svc+'_sum');
		obj_sur   = obj_opener.getElementById('txt_sur_amt');
		
		// 현재 금액정보
		amt_total = parseFloat(__commaUnset(obj_total.innerHTML),	10);
		amt_bonin = parseFloat(__commaUnset(obj_bonin.innerHTML),	10);
		amt_over  = parseFloat(__commaUnset(obj_over.innerHTML),	10);
		amt_bipay = parseFloat(__commaUnset(obj_bipay.innerHTML),	10);
		amt_sum   = parseFloat(__commaUnset(obj_sum.innerHTML),		10);

		if (type != 'insert'){
			suga_total.value = parseFloat(suga_total.value,10) - old_suga;
		}

		if (old_bipay == 'Y'){
			// 비급여인 경우
			amt_bipay = amt_bipay - old_suga;
		}else{
			// 비급여가 아닐경우
			if (amt_over >= old_suga){
				// 초과금액이 수가금액보다 크다.
				amt_over = amt_over - old_suga;
			}else{
				// 초과금액이 수가금액보다 작다.
				if (amt_over > 0){
					// 초과금액이 있다면 초과금액을 우선으로 처리한다.
					// 본인부담금 수정
					//amt_bonin = amt_bonin - Math.floor(amt_over * bonin_yul / 100);
					amt_bonin = amt_bonin - Math.floor((old_suga - amt_over) * bonin_yul / 100);
					
					old_suga = old_suga - amt_over;
					amt_over = 0;
				}else{
					// 본인부담금 수정
					amt_bonin = amt_bonin - Math.floor(old_suga * bonin_yul / 100);
				}
				
				// 수가액을 수정한다.
				amt_total = amt_total - old_suga;
			}

			// 본인부담계 수정
			amt_sum = amt_bonin + amt_over + amt_bipay;
		}
		
		obj_total.innerHTML	= __commaSet(amt_total);
		obj_bonin.innerHTML	= __commaSet(amt_bonin);
		obj_over.innerHTML	= __commaSet(amt_over);
		obj_bipay.innerHTML	= __commaSet(amt_bipay);
		obj_sum.innerHTML	= __commaSet(amt_bonin + amt_over + amt_bipay);

		//obj_total.tag	= amt_total;
		//obj_bonin.tag	= amt_bonin;
		//obj_over.tag	= amt_over;
		//obj_bipay.tag	= amt_bipay;
		//obj_sum.tag		= amt_bonin + amt_over + amt_bipay;

		_set_client_month_sur(type);

		if (type == 'delete') return;
	}

	obj_total = obj_opener.getElementById('txt_'+new_svc+'_total');
	obj_bonin = obj_opener.getElementById('txt_'+new_svc+'_bonin');
	obj_over  = obj_opener.getElementById('txt_'+new_svc+'_over');
	obj_bipay = obj_opener.getElementById('txt_'+new_svc+'_bipay');
	obj_sum   = obj_opener.getElementById('txt_'+new_svc+'_sum');
	obj_sur   = obj_opener.getElementById('txt_sur_amt');

	amt_total = parseFloat(__commaUnset(obj_total.innerHTML),	10);
	amt_bonin = parseFloat(__commaUnset(obj_bonin.innerHTML),	10);
	amt_over  = parseFloat(__commaUnset(obj_over.innerHTML),	10);
	amt_bipay = parseFloat(__commaUnset(obj_bipay.innerHTML),	10);
	amt_sum   = parseFloat(__commaUnset(obj_sum.innerHTML),		10);

	if (type != 'insert'){
		suga_total.value = parseFloat(suga_total.value) + new_suga;
	}

	if (new_bipay == 'Y'){
		// 비급여인 경우
		amt_bipay = amt_bipay + new_suga;
	}else{
		var total_pay = parseFloat(__commaUnset(obj_opener.getElementById('txt_200_total').innerHTML), 10)
					  + parseFloat(__commaUnset(obj_opener.getElementById('txt_500_total').innerHTML), 10)
					  + parseFloat(__commaUnset(obj_opener.getElementById('txt_800_total').innerHTML), 10); //일정수가총액
		var total_pay_proc = 0;

		// 비급여가 아닐경우
		if (max_amount > total_pay + new_suga){
			amt_total += new_suga;
		}else{
			if (max_amount >= total_pay){
				total_pay_proc = max_amount - total_pay;
				amt_total += total_pay_proc;
			}else{
				total_pay_proc = 0;
			}
			amt_over += (new_suga - total_pay_proc);
		}
		//amt_bonin += Math.floor((new_suga - total_pay_proc) * bonin_yul / 100);

		// 등급이 일반인 경우는 본인 부담금을 잡지 않는다.
		if (bonin_yul != 100){
			//amt_bonin += Math.floor((new_suga - total_pay_proc) * bonin_yul / 100);
			if (total_pay_proc == 0){
				amt_bonin += Math.floor(new_suga * bonin_yul / 100);
			}else{
				amt_bonin += Math.floor(total_pay_proc * bonin_yul / 100);
			}
		}
	}

	obj_total.innerHTML	= __commaSet(amt_total);
	obj_bonin.innerHTML	= __commaSet(amt_bonin);
	obj_over.innerHTML	= __commaSet(amt_over);
	obj_bipay.innerHTML	= __commaSet(amt_bipay);
	obj_sum.innerHTML	= __commaSet(amt_bonin + amt_over + amt_bipay);

	//obj_total.tag	= amt_total;
	//obj_bonin.tag	= amt_bonin;
	//obj_over.tag	= amt_over;
	//obj_bipay.tag	= amt_bipay;
	//obj_sum.tag		= amt_bonin + amt_over + amt_bipay;

	_set_client_month_sur(type);
}

// 수급자 월수급 금여잔액
function _set_client_month_sur(type){
	if (type == 'delete' || type == 'insert'){
		var obj_opener = document;
	}else{
		var obj_opener = opener.document;
	}

	var max_amount = parseFloat(obj_opener.getElementById('max_amount').value, 10);	//급여한도액
	var obj_sur = obj_opener.getElementById('txt_sur_amt');	//잔액

	var obj_tot_total = obj_opener.getElementById('txt_tot_total');	//수가계
	var obj_tot_bonin = obj_opener.getElementById('txt_tot_bonin');	//본인부담계
	var obj_tot_over  = obj_opener.getElementById('txt_tot_over');	//초과계
	var obj_tot_bipay = obj_opener.getElementById('txt_tot_bipay');	//비급여계
	var obj_tot_sum   = obj_opener.getElementById('txt_tot_sum');	//본인부담계

	var obj_200_total = obj_opener.getElementById('txt_200_total');	//수가계
	var obj_200_bonin = obj_opener.getElementById('txt_200_bonin');	//본인부담계
	var obj_200_over  = obj_opener.getElementById('txt_200_over');	//초과계
	var obj_200_bipay = obj_opener.getElementById('txt_200_bipay');	//비급여계
	var obj_200_sum   = obj_opener.getElementById('txt_200_sum');	//본인부담계

	var obj_500_total = obj_opener.getElementById('txt_500_total');	//수가계
	var obj_500_bonin = obj_opener.getElementById('txt_500_bonin');	//본인부담계
	var obj_500_over  = obj_opener.getElementById('txt_500_over');	//초과계
	var obj_500_bipay = obj_opener.getElementById('txt_500_bipay');	//비급여계
	var obj_500_sum   = obj_opener.getElementById('txt_500_sum');	//본인부담계

	var obj_800_total = obj_opener.getElementById('txt_800_total');	//수가계
	var obj_800_bonin = obj_opener.getElementById('txt_800_bonin');	//본인부담계
	var obj_800_over  = obj_opener.getElementById('txt_800_over');	//초과계
	var obj_800_bipay = obj_opener.getElementById('txt_800_bipay');	//비급여계
	var obj_800_sum   = obj_opener.getElementById('txt_800_sum');	//본인부담계

	obj_tot_total.innerHTML = __commaSet(parseFloat(__commaUnset(obj_200_total.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_500_total.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_800_total.innerHTML), 10));
	//obj_tot_total.tag = __commaUnset(obj_tot_total.innerHTML);

	obj_tot_bonin.innerHTML = __commaSet(parseFloat(__commaUnset(obj_200_bonin.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_500_bonin.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_800_bonin.innerHTML), 10));
	//obj_tot_bonin.tag = __commaUnset(obj_tot_bonin.innerHTML);

	obj_tot_over.innerHTML	= __commaSet(parseFloat(__commaUnset(obj_200_over.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_500_over.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_800_over.innerHTML), 10));
	//obj_tot_over.tag = __commaUnset(obj_tot_over.innerHTML);

	obj_tot_bipay.innerHTML = __commaSet(parseFloat(__commaUnset(obj_200_bipay.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_500_bipay.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_800_bipay.innerHTML), 10));
	//obj_tot_bipay.tag = __commaUnset(obj_tot_bipay.innerHTML);

	obj_tot_sum.innerHTML	= __commaSet(parseFloat(__commaUnset(obj_200_sum.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_500_sum.innerHTML), 10)
									   + parseFloat(__commaUnset(obj_800_sum.innerHTML), 10));
	//obj_tot_sum.tag = __commaUnset(obj_tot_sum.innerHTML);

	var total_pay  = parseFloat(__commaUnset(obj_tot_total.innerHTML), 10);
	var amt_over   = parseFloat(__commaUnset(obj_tot_over.innerHTML), 10);
	var sur_amount = max_amount - (total_pay + amt_over);
	
	obj_sur.innerHTML = __commaSet(sur_amount);

	if (parseInt(__commaUnset(obj_sur.innerHTML), 10) < 0){
		obj_sur.style.color = '#ff0000';
	}else{
		obj_sur.style.color = '#0000ff';
	}
	//obj_sur.tag		  = sur_amount;
}

// 수급자 월수급 초기화
function _init_month_amount_care(){
	var obj_opener = document;

	var obj_max = obj_opener.getElementById('max_amount');	//급여한도액
	var obj_sur = obj_opener.getElementById('txt_sur_amt');	//잔액

	var obj_tot_total = obj_opener.getElementById('txt_tot_total');	//수가계
	var obj_tot_bonin = obj_opener.getElementById('txt_tot_bonin');	//본인부담계
	var obj_tot_over  = obj_opener.getElementById('txt_tot_over');	//초과계
	var obj_tot_bipay = obj_opener.getElementById('txt_tot_bipay');	//비급여계
	var obj_tot_sum   = obj_opener.getElementById('txt_tot_sum');	//본인부담계

	var obj_200_total = obj_opener.getElementById('txt_200_total');	//수가계
	var obj_200_bonin = obj_opener.getElementById('txt_200_bonin');	//본인부담계
	var obj_200_over  = obj_opener.getElementById('txt_200_over');	//초과계
	var obj_200_bipay = obj_opener.getElementById('txt_200_bipay');	//비급여계
	var obj_200_sum   = obj_opener.getElementById('txt_200_sum');	//본인부담계

	var obj_500_total = obj_opener.getElementById('txt_500_total');	//수가계
	var obj_500_bonin = obj_opener.getElementById('txt_500_bonin');	//본인부담계
	var obj_500_over  = obj_opener.getElementById('txt_500_over');	//초과계
	var obj_500_bipay = obj_opener.getElementById('txt_500_bipay');	//비급여계
	var obj_500_sum   = obj_opener.getElementById('txt_500_sum');	//본인부담계

	var obj_800_total = obj_opener.getElementById('txt_800_total');	//수가계
	var obj_800_bonin = obj_opener.getElementById('txt_800_bonin');	//본인부담계
	var obj_800_over  = obj_opener.getElementById('txt_800_over');	//초과계
	var obj_800_bipay = obj_opener.getElementById('txt_800_bipay');	//비급여계
	var obj_800_sum   = obj_opener.getElementById('txt_800_sum');	//본인부담계

	obj_max.vaue      = obj_max.tag;
	obj_sur.innerHTML = __commaSet(obj_sur.tag);

	obj_tot_total.innerHTML = __commaSet(obj_tot_total.tag);
	obj_tot_bonin.innerHTML = __commaSet(obj_tot_bonin.tag);
	obj_tot_over.innerHTML  = __commaSet(obj_tot_over.tag);
	obj_tot_bipay.innerHTML = __commaSet(obj_tot_bipay.tag);
	obj_tot_sum.innerHTML   = __commaSet(obj_tot_sum.tag);

	obj_200_total.innerHTML = __commaSet(obj_200_total.tag);
	obj_200_bonin.innerHTML = __commaSet(obj_200_bonin.tag);
	obj_200_over.innerHTML  = __commaSet(obj_200_over.tag);
	obj_200_bipay.innerHTML = __commaSet(obj_200_bipay.tag);
	obj_200_sum.innerHTML   = __commaSet(obj_200_sum.tag);

	obj_500_total.innerHTML = __commaSet(obj_500_total.tag);
	obj_500_bonin.innerHTML = __commaSet(obj_500_bonin.tag);
	obj_500_over.innerHTML  = __commaSet(obj_500_over.tag);
	obj_500_bipay.innerHTML = __commaSet(obj_500_bipay.tag);
	obj_500_sum.innerHTML   = __commaSet(obj_500_sum.tag);

	obj_800_total.innerHTML = __commaSet(obj_800_total.tag);
	obj_800_bonin.innerHTML = __commaSet(obj_800_bonin.tag);
	obj_800_over.innerHTML  = __commaSet(obj_800_over.tag);
	obj_800_bipay.innerHTML = __commaSet(obj_800_bipay.tag);
	obj_800_sum.innerHTML   = __commaSet(obj_800_sum.tag);
}

// 제공일자
function _set_svc_dt(dt){
	var obj_dt = document.getElementById('svc_dt_'+dt);		//일자객체
	var str_dt = document.getElementById('str_svc_dt_'+dt);	//일자텍스트
	
	for(var i=0; i<=6; i++){
		document.getElementById('weekDay'+i).checked = false;
	}

	if (obj_dt.value == 'N'){
		obj_dt.value = 'Y';
		str_dt.className = 'my_box my_box_2';
	}else{
		obj_dt.value = 'N';
		str_dt.className = 'my_box my_box_1';
	}

	document.getElementById('svc_in_type').value = 'date';
}

// 제공요일
function _set_svc_week(){
	var lastday = document.getElementById('svc_last_day');

	for(var i=1; i<=lastday.value; i++){
		document.getElementById('svc_dt_'+i).value = 'N';
		document.getElementById('str_svc_dt_'+i).className = 'my_box my_box_1';
	}

	document.getElementById('svc_in_type').value = 'weekday';
}

function _show_guide(){
	
	cLayer.style.width  = document.body.offsetWidth;
		
	if (document.body.scrollHeight > document.body.offsetHeight){
		cLayer.style.height = document.body.scrollHeight;
	}else{
		cLayer.style.height = document.body.offsetHeight;
	}

	var tableLeft = (parseInt(__replace(cLayer.style.width, 'px', '')) - parseInt(__replace(guideTable.style.width, 'px', ''))) / 2+'px';
	var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(guideTable.style.height, 'px', ''))) / 2+'px';
	guideLayer.style.top = tableTop;
	guideLayer.style.left = tableLeft;
	guideLayer.style.width = guideTable.style.width;
	guideLayer.style.height = guideTable.style.height;
	guideTable.style.display = '';
}

function _hidden_guide(){
	cLayer.style.width = 0;
	cLayer.style.height = 0;
	guideLayer.style.width = 0;
	guideLayer.style.height = 0;
	guideTable.style.display = 'none';
}

function _getMode(){
	var mode = null;

	mode = document.getElementById('mode');

	if (mode == null){
		mode = document.getElementById('mMode');
	}

	return mode;
}

function _newType(){
	var href = __replace(location.href, 'http://', '');
		href = href.split('/')[1];

	if (href == 'iljung'){
		if (location.href.indexOf('iljung_reg.php') < 0 && 
			location.href.indexOf('iljung_add.php') < 0 &&
			location.href.indexOf('iljung_add_conf.php') < 0){
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

/*
 * 일정보기
 */
function _viewIljung(){
}

/********************************************************************

	일정중복리스트를 작성한다.

********************************************************************/
function _chk_iljung(day, index){
	var member    = document.getElementById('mYoy1_'+day+'_'+index);
	var client    = document.getElementById('mSugupja_'+day+'_'+index);
	var code      = document.getElementById('code');
	var date      = document.getElementById('mDate_'+day+'_'+index);
	var from_time = document.getElementById('mFmTime_'+day+'_'+index);
	var to_time   = document.getElementById('mToTime_'+day+'_'+index);
	
	var result = getHttpRequest('../inc/_check_class.php?check=iljung_duplicate&code='+code.value+'&member='+member.value+'&date='+date.value+'&from_time='+from_time.value+'&to_time='+to_time.value);
}

/*
 *	실적만 추가한다.
 */
function _add_conf_data(code, date, c_cd, m_cd){
	var url   = '../iljung/iljung_add_conf.php';
	var w     = 1000;
	var h     = 335;
	var mode  = 'ADD_CONF';
	var param = new Object();
		param.win  = window;
		param.code = code;
		param.date = date;
		param.c_cd = c_cd;
		param.m_cd = m_cd;
	var modal = showModalDialog(url+'?mMode='+mode, param, 'dialogWidth:'+w+'px; dialogHeight:'+h+'px; dialogHide:yes; scroll:yes; status:no');

	return modal;
}




/*********************************************************

	고객정보 출력

*********************************************************/
function _findClientInfo(para){
	if ($('#lbTestMode').val() == true){
		var val = __parseStr(para);
		var dt  = new Date();
		var fromDt = dt.getFullYear()+'-'+(((dt.getMonth()+1) < 10 ? '0' : '')+(dt.getMonth()+1))+'-01';
		var toDt   = __addDate('d',-1,__addDate('m',1,fromDt));

		$('#strName').text(val['name']);
		$('#strJumin').text(val['strJumin']);
		$('#strLevel').text(val['lvl_nm']);
		$('#strInjungNo').text(val['app_no']);
		$('#strUseDate').text(fromDt.split('-').join('.')+'~'+toDt.split('-').join('.'));
		$('#strRate').text(val['rate']);
		$('#jumin').attr('value', val['jumin']);
	}else{
		var jumin = para.split('&')[1].split('=')[1];
		
		try{
			$.ajax({
				type: 'POST',
				url : '../find/_find_client_info.php',
				data: {
					'code':$('#code').attr('value')
				,	'kind':$('#kind').attr('value')
				,	'jumin':jumin
				,	'result':'name,jumin,ylvl,injung_no,injung_from,injung_to,bonin_yul'
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					$('#strName').text( $(xmlHttp).filter('.find_name').text() );
					$('#strJumin').text( $(xmlHttp).filter('.find_jumin').text() );
					$('#strLevel').text( $(xmlHttp).filter('.find_ylvl').text() );
					$('#strInjungNo').text( $(xmlHttp).filter('.find_injung_no').text() );
					$('#strUseDate').text( __replace(__getDate($(xmlHttp).filter('.find_injung_from').text()),'-','.')+' ~ '+__replace(__getDate($(xmlHttp).filter('.find_injung_to').text()),'-','.') );
					$('#strRate').text( $(xmlHttp).filter('.find_bonin_yul').text() );
					$('#jumin').attr('value', jumin);
				}
				,
				error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}
}


/*********************************************************

	수가입력전 수급자의 선택여부를 확인한다.

*********************************************************/
function _chkSugaInfo(code, svcCD, svcID){
	if ($('#jumin').attr('value') != ''){
		_findSugaInfo(__findSuga(code, svcCD, $('#stndSugaDt').attr('value')), svcCD, svcID);
	}else{
		alert('수급자 선택을 먼저 실행하여 주십시오.');
	}
}


/*********************************************************

	서비스별 수가 조회

*********************************************************/
function _findSugaInfo(sugaIf, svcCD, svcID){
	if (!sugaIf) return;

	var arr = sugaIf.split('&'); 
	var val = new Array();

	for(var i=0; i<arr.length; i++){
		var tmp = arr[i].split('=');

		val[tmp[0]] = tmp[1];
	}
	
	$('#strSugaCode'+svcCD+'_'+svcID).text( val['code'] );
	$('#strSugaName'+svcCD+'_'+svcID).text( val['name'] );
	$('#strSugaCost'+svcCD+'_'+svcID).text( __num2str(val['cost']) );
}


/*********************************************************

	더미

*********************************************************/
function fn_refresh(){
	
}
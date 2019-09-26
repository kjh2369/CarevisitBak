/*********************************************************

	캘린더

*********************************************************/
var _doc    = null;
var _yymm   = null;
var _week   = null;
var _menu   = null;
var _body   = null;
var _list   = null;
var _reg    = null;
var _popup  = null;
var _target = null;
var _backBG = '#ffffff';
var _flagRegOpen = null;
var _flagViewOpen = null;
var _clickKind = 0;
var _eventMode = null;



/*********************************************************

	년월이동

*********************************************************/
function _moveYYMM(pos){
	var year  = document.getElementById('year');
	var month = document.getElementById('month');
	var str   = document.getElementById('yymm');
	var mode  = document.getElementById('mode').value;
	var yymm  = __addDate('m', pos, year.value+'-'+month.value+'-01').split('-');

	year.value  = yymm[0];
	month.value = yymm[1];
	
	_getCalendar(mode);
}

/*******************************************************

	 주간별 주 이동

*******************************************************/
function _moveWEEK(pos){
	var year  = document.getElementById('year');
	var month = document.getElementById('month');
	var str   = document.getElementById('yymm');
	var mode  = document.getElementById('mode').value;
	var yymm  = __addDate('m', pos, year.value+'-'+month.value+'-01').split('-');
	var week   = document.getElementById('week');
	var startDt	= new Date(year.value,month.value-1,1);
	var weekly  = parseInt(week.value) + pos;
	
	var startDay = new Date(year.value, month.value-1, 1);
	var monthsDay = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	var lastDay  = monthsDay[parseInt(month.value, 10)];
	
	var startWeek = startDay.getDay(); //1일의 요일
	var totalWeek = Math.ceil((lastDay + startWeek) / 7); //총 몇 주인지 구하기

	if (startDt.getDay() <= 6){
		startDt.setDate(startDt.getDate()-startDt.getDay());
	}

	//년월 이동 시 주차가 당월총주수보다 클 경우
	if(weekly > totalWeek){
		weekly = totalWeek;
	}
	
	
	for(var i=1; i<7; i++){
		
		var tmpDt	= new Date(startDt.valueOf());		
		startDt.setDate(startDt.getDate()+6);
		
		//alert(tmpDt.toLocaleDateString()+'~'+startDt.toLocaleDateString());
		
		if(i == weekly){
			var y1 = tmpDt.getFullYear();
			var m1 = tmpDt.getMonth()+1;
			var d1 = tmpDt.getDate();
			
			var y2 = startDt.getFullYear();
			var m2 = startDt.getMonth()+1;
			var d2 = startDt.getDate();
		
			document.getElementById('fromDt').value = d1;
			document.getElementById('toDt').value = d2;
			var from = y1+'-'+(m1 < 10 ? '0' : '')+m1+'-'+(d1 < 10 ? '0' : '')+d1;
			var to   = y2+'-'+(m2 < 10 ? '0' : '')+m2+'-'+(d2 < 10 ? '0' : '')+d2;
		}
		
		if(i<=totalWeek){		
			$('#lblWeekly'+i).html('<a href="#" onclick="_goWEEK('+i+','+from+','+to+'); return false;">'+i+'주차</a>').show();
		}else {
			$('#lblWeekly'+i).html('<a href="#" onclick="return false;">'+i+'주차</a>').hide();
		}

		//if (startDt.getMonth() != __str2num(month.value-1)) break;
		
		startDt.setDate(startDt.getDate()+1);
	}

	
	if(pos == -1){
		if(week.value > 1){	
			if(weekly <= i){
				week.value = weekly;
			}
		}
	}else {
		if(weekly <= totalWeek){
			week.value = weekly;
		}
	}
	
	_getCalendar(mode);

}

function _goWEEK(pos){
	var year  = document.getElementById('year');
	var month = document.getElementById('month');
	var str   = document.getElementById('yymm');
	var mode  = document.getElementById('mode').value;
	var week   = document.getElementById('week');
	var startDt	= new Date(year.value,month.value-1,1);
	var weekly  = pos;
	
	if (startDt.getDay() <= 6){
		startDt.setDate(startDt.getDate()-startDt.getDay());
	}
	
	for(var i=1; i<7; i++){
		
		var tmpDt	= new Date(startDt.valueOf());		
		startDt.setDate(startDt.getDate()+6);
		
		//alert(tmpDt.toLocaleDateString()+'~'+startDt.toLocaleDateString());
		
		if(i == weekly){
			var y1 = tmpDt.getFullYear();
			var m1 = tmpDt.getMonth()+1;
			var d1 = tmpDt.getDate();
			
			var y2 = startDt.getFullYear();
			var m2 = startDt.getMonth()+1;
			var d2 = startDt.getDate();

			var from = y1+'-'+(m1 < 10 ? '0' : '')+m1+'-'+(d1 < 10 ? '0' : '')+d1;
			var to   = y2+'-'+(m2 < 10 ? '0' : '')+m2+'-'+(d2 < 10 ? '0' : '')+d2;

			document.getElementById('fromDt').value = d1;
			document.getElementById('toDt').value = d2;
			
		}
		
		//if (startDt.getMonth() != __str2num(month.value-1)) break;
		
		startDt.setDate(startDt.getDate()+1);
	}


	week.value = weekly;
	
	_getCalendar(mode);
}
/*********************************************************

	달력그리기

*********************************************************/
function _getCalendar(mode){
	var code  = document.getElementById('code').value;
	var year  = document.getElementById('year').value;
	var month = document.getElementById('month').value;
	var week  = document.getElementById('week').value;
	var fromDt= document.getElementById('fromDt').value;
	var toDt  = document.getElementById('toDt').value;
	
	document.getElementById('mode').value = mode;

	if (mode == 'week'){
		$.ajax({
			type	:'POST'
		,	url		:'./calendar_weekly.php'
		,	data	:{
				'code'	:code
			,	'year'	:year
			,	'month'	:month
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#this_yymm').html(year+'.'+month);
				$('#this_body').html(html);
				lfWeeklyDraw();
			}
		});
	}else{
		switch(mode){
			case 'list':
				var URL = './calendar_list.php';
				break;
			
			default:
				var URL = './calendar_month.php';
		}

		var params  = {'code':code,'year':year,'month':month,'mode':mode,'week':week,'fromDt':fromDt,'toDt':toDt};
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:params,
				onSuccess:_getCalendarResult,
				onError:__ajax_error,
				onFailure:__ajax_failure
			}
		);
	}
}

function _getCalendarResult(responseHttpObj){
	var year  = document.getElementById('year').value;
	var month = document.getElementById('month').value;
	var week  = document.getElementById('week').value;
	var mode  = document.getElementById('mode').value;
	var week_prev = document.getElementById('week_prev');
	var week_next = document.getElementById('week_next');
	_regPopupClose();
	_yymm.innerHTML = year+'.'+month;
	
	try{
		if(mode == 'week'){
			//week_prev.style.display = '';
			//week_next.style.display = '';
			//_week.style.display = '';
			//_week.innerHTML = week+'주차';
			cal_month.style.display = 'none';
			cal_weekly.style.display = '';
			//lblWeekly.style.display = '';
		}else if(mode == 'list'){
			//week_prev.style.display = 'none';
			//week_next.style.display = 'none';
			//_week.style.display = 'none';
			cal_month.style.display = 'none';
			cal_weekly.style.display = 'none';
			//lblWeekly.style.display = 'none';
		}else {
			//week_prev.style.display = 'none';
			//week_next.style.display = 'none';
			//_week.style.display = 'none';
			cal_month.style.display = '';
			cal_weekly.style.display = 'none';
			//lblWeekly.style.display = 'none';
		}
	}catch(e){
	}
	
	_body.innerHTML = responseHttpObj.responseText;
}


/*********************************************************

	일정등록팝업

*********************************************************/
function _regCalendar(obj, code, date, seq, no, mode, time){
	if (_reg == null) return;
	
	if (_flagRegOpen != obj){
		_flagRegOpen = obj;
		_regPopupClose(1);
	}else{
		_regPopupClose();
		return;
	}

	_target = obj;
	_clickKind = 1;
	_eventMode = mode;

	if (_target.tagName == 'TD'){
		_backBG = _target.style.backgroundColor;
		_target.style.backgroundColor = '#f3f9fd';
	}
	
	var URL     = './calendar_reg.php';
	var params  = {'code':code,'date':date,'seq':seq,'no':no,'mode':mode,'time':time};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_regCalendarBody,
			onError:__ajax_error,
			onFailure:__ajax_failure
		}
	);
}

function _regCalendarBody(responseHttpObj){
	_reg.innerHTML = responseHttpObj.responseText;
	_popup = document.getElementById('this_popup');
	_getPopupPosition();
	_regFullTimeSet();
	_reg.style.display = '';
	__init_form(document.f);
}


/*********************************************************

	팝업저장

*********************************************************/
function _regSave(){
	var f = document.f;

	if (__replace(f.regSubject.value, ' ', '') == ''){
		alert('제목을 입력하여 주십시오.');
		f.regSubject.focus();
		return;
	}

	if (!f.regFromDate.value){
		alert('일자 입력오류입니다. 확인하여 주십시오.');
		f.regFromDate.focus();
		return;
	}

	if (!f.regToDate.value){
		alert('일자 입력오류입니다. 확인하여 주십시오.');
		f.regToDate.focus();
		return;
	}

	if (f.regFromDate.value > f.regToDate.value){
		alert('시작일자가 종료일자보다 클 수 없습니다. 확인하여 주십시오.');
		f.regFromDate.focus();
		return;
	}
	
	if (f.regFromDate.value == f.regToDate.value){
		if (f.regFromTime.selectedIndex > f.regToTime.selectedIndex){
			alert('시작시간이 종료시간보다 클 수 없습니다. 확인하여 주십시오.');
			f.regFromTime.focus();
			return;
		}
	}

	var URL     = './calendar_reg_ok.php';
	var params  = {'code':f.code.value
				  ,'yymm':f.yymm.value
				  ,'seq':f.seq.value
				  ,'no':f.no.value
				  ,'para':'subject='+f.regSubject.value+'&contents='+f.regContents.value+'&from='+f.regFromDate.value+' '+f.regFromTime.options[f.regFromTime.selectedIndex].text+'&to='+f.regToDate.value+' '+f.regToTime.options[f.regToTime.selectedIndex].text+'&fulltime='+(f.regFullTime.checked ? 'Y' : 'N')};

	//alert(params['para']);
	//return;

	try{
		params['para'] += '&loopGbn='+f.cboLoopGbn.value;
		params['para'] += '&weekly1='+(f.chkWeekly1.checked ? 'Y' : 'N');
		params['para'] += '&weekly2='+(f.chkWeekly2.checked ? 'Y' : 'N');
		params['para'] += '&weekly3='+(f.chkWeekly3.checked ? 'Y' : 'N');
		params['para'] += '&weekly4='+(f.chkWeekly4.checked ? 'Y' : 'N');
		params['para'] += '&weekly5='+(f.chkWeekly5.checked ? 'Y' : 'N');
		params['para'] += '&weekly6='+(f.chkWeekly6.checked ? 'Y' : 'N');
		params['para'] += '&weekly0='+(f.chkWeekly0.checked ? 'Y' : 'N');
	}catch(e){
	}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_regSaveResult,
			onError:__ajax_error,
			onFailure:__ajax_failure
		}
	);
}


function _regSaveResult(responseHttpObj){
	var result = responseHttpObj.responseText;

	if (result == 'ok'){
		_regPopupClose();
		_getCalendar(document.getElementById('mode').value);
	}else if (result == 'error'){
		alert('일정 저장 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		_regPopupClose();
	}else{
		alert(result);
	}
}


/*********************************************************

	 팝업닫기

*********************************************************/
function _regPopupClose(mode){
	if (_target != null && _target.tagName == 'TD'){
		_target.style.backgroundColor = _backBG;
	}

	if (mode == 1) return;

	_reg.style.display = 'none';
	_reg.style.left    = window.event.x;
	_reg.style.top     = window.event.y;
	_flagRegOpen       = null;
	_flagViewOpen      = null;
	_eventMode		   = null;
	_clickKind		   = 0;
}



/*********************************************************

	내용팝업

*********************************************************/
function _viewCalendar(obj, code, yymm, seq, no, mode, dt){
	if (_flagViewOpen != obj){
		_flagViewOpen  = obj;
		_regPopupClose(1);
	}else{
		_regPopupClose();
		return;
	}

	_target = obj;
	_clickKind = 2;
	_eventMode = mode;

	
	var URL     = './calendar_view.php';
	var params  = {'code':code,'yymm':yymm,'seq':seq,'no':no,'mode':mode,'dt':dt};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_viewCalendarBody,
			onError:__ajax_error,
			onFailure:__ajax_failure
		}
	);
}


function _viewCalendarBody(responseHttpObj){
	_reg.innerHTML = responseHttpObj.responseText;
	_popup = document.getElementById('this_popup');
	_getPopupPosition();
	_reg.style.display = '';
}



/*********************************************************

	팝업위치

*********************************************************/
function _getPopupPosition(){
	var popupTop    = parseInt(_reg.style.top, 10);
	var popupLeft   = parseInt(_reg.style.left, 10);
	var popupWidth  = parseInt(_popup.style.width, 10);
	var popupHeight = parseInt(_popup.style.height, 10);

	var bodyTop    = __getObjectTop(_body);
	var bodyLeft   = __getObjectLeft(_body);
	var bodyWidth  = _body.offsetWidth;
	var bodyHeight = document.body.offsetHeight - bodyTop;

	var targetTop    = __getObjectTop(_target);
	var targetLeft   = __getObjectLeft(_target);
	var targetWidth  = _target.offsetWidth;
	var targetHeight = _target.offsetHeight;

	if (targetLeft + popupWidth > bodyLeft + bodyWidth){
		_reg.style.left = bodyLeft + bodyWidth - popupWidth;
	}else{
		_reg.style.left = targetLeft;
	}

	_reg.style.zIndex = 100;

	var gabHeight = 0;

	if (_eventMode == 'list'){
		gabHeight = targetHeight;
	}

	if (targetTop + targetHeight + popupHeight > bodyTop + bodyHeight){
		_reg.style.top = targetTop - popupHeight - gabHeight;
	}else{
		_reg.style.top = targetTop + targetHeight - gabHeight;
	}
}


/*********************************************************

	수정

*********************************************************/
function _modifyCalendar(code, yymm, seq, no, mode){
	_regCalendar(_target, code, yymm, seq, no, mode);
}



/*********************************************************

	삭제

*********************************************************/
function _deleteCalendar(code, yymm, seq, no){
	if (!confirm('선택하신 스케줄을 정말로 삭제하시겠습니까?')) return;

	var URL     = './calendar_del_ok.php';
	var params  = {'code':code,'yymm':yymm,'seq':seq,'no':no};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				_getCalendar(document.getElementById('mode').value);
			},
			onError:__ajax_error,
			onFailure:__ajax_failure
		}
	);
}



/*********************************************************

	종일선택

*********************************************************/
function _regFullTimeSet(){
	var fromTime = document.getElementById('regFromTime');
	var toTime   = document.getElementById('regToTime');
	var fulltime = document.getElementById('regFullTime').checked;

	if (fulltime){
		fromTime.disabled = true;
		toTime.disabled   = true;
	}else{
		fromTime.disabled = false;
		toTime.disabled   = false;
	}
}
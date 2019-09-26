// 달력 열기
var carlendar = null;
var IsCalBtnShow = true; //년월이동버튼 숨김여부
var defaultCalDate = ''; //기본일자

function _showCarlendar(target, object){
	if (carlendar == null){
		var today = new Date();
		var year = today.getFullYear();
		var month = today.getMonth()+1;

		month = (month < 10 ? '0' : '')+month;
		carlendar = new Calrendar(calBody, year, month);
	}
	carlendar.target = target;
	carlendar.object = object;
	carlendarLayer.style.left = __getObjectLeft(target) - 6;
	carlendarLayer.style.top  = __getObjectTop(target) + 14;
	carlendarLayer.style.display = '';
	_object_event();
}

// 달력열기
function _carlendar(object){
	var temp_date = __getDate(object.value);

	if (__isValDate(temp_date)){
		var date  = temp_date.split('-');
		var year  = date[0];
		var month = date[1];
		var day   = date[2];
	}else{
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth()+1;
		var day   = today.getDate();

		month = (month < 10 ? '0' : '')+month;
		day   = (day   < 10 ? '0' : '')+day;
	}

	if (carlendar == null){
		carlendar = null;
	}
	carlendar = new Calrendar(calBody, year, month, day, 1);
	carlendar.target = object;
	carlendar.object = object;
	carlendarLayer.style.left = __getObjectLeft(object) - 6;
	carlendarLayer.style.top  = __getObjectTop(object) + 14;
	carlendarLayer.style.display = '';
	_object_event();
}

// 월별달력
function _carlendar_month(object){
	var temp_date = __replace(object.value, '-', '');

	if (temp_date.length == 6){
		var year  = temp_date.substring(0, 4);
		var month = temp_date.substring(4, 6);
	}else{
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth()+1;
		
		month = (month < 10 ? '0' : '')+month;
	}

	if (carlendar == null){
		carlendar = null;
	}
	carlendar = new Calrendar(calBody, year, month, 1, 2);
	carlendar.target = object;
	carlendar.object = object;
	carlendarLayer.style.left = __getObjectLeft(object) - 6;
	carlendarLayer.style.top  = __getObjectTop(object) + 14;
	carlendarLayer.style.display = '';
	_object_event();
}

// 오브젝트 이벤트
function _object_event(){
	carlendarLayer.onmousemove = function(){
		carlendar.mouse_event_time = 0;
	}

	_timer_init();
}

// 월이동
function _moveCarlendar(p_year, p_month){
	var today = new Date();
	var year  = p_year;
	var month = p_month;
	var day   = '';

	if (isNaN(year)) year = today.getFullYear();
	if (isNaN(month)) month = today.getMonth()+1;

	month = (month < 10 ? '0' : '')+month;
	
	if (carlendar == null){
		carlendar = new Calrendar(calBody, year, month, day, 1);
	}else{
		carlendar.body.innerHTML = carlendar.set(year, month, day);
	}
}

function _curCarlendar(p_year, p_month, p_day){
	var y = p_year;
	var m = (parseInt(p_month, 10) < 10 ? '0' : '') + parseInt(p_month, 10);
	var d = (parseInt(p_day, 10) < 10 ? '0' : '') + parseInt(p_day, 10);
	
	try{
		if (carlendar.type == 1)
			carlendar.target.innerHTML = y + '/' + m + '/' + d;
		else
			carlendar.target.innerHTML = y + '/' + m;
	}catch(e){
	}

	if (carlendar.type == 1)
		carlendar.object.value = y + '-' + m + '-' + d;
	else
		carlendar.object.value = y + '-' + m;

	try{
		if (carlendar.object.tag != ''){
			switch(carlendar.object.alt){
			case '_checkInsLimitDate':
				_checkInsLimitDate();
				break;
			case 'report_77':
				//_checkDate(carlendar.object,'date[]');
				_checkData();
				break;
			case 'tag':
				eval(carlendar.object.tag);
				break;
			default:
				eval(carlendar.object.alt);
				//_insToDate(carlendar.object.value, carlendar.object.tag);
			}
		}
	}catch(e){
	}

	/*********************************************************
		변경후 체인지 이벤트를 실행한다.
	*********************************************************/
	try{
		carlendar.object.onchange();
	}catch(e){
	}
	
	_hiddenCarlendar();
}

// 달력 닫기
function _hiddenCarlendar(){
	carlendarLayer.style.display = 'none';
	carlendarLayer.onmousemove = '';
	carlendar.mouse_event_time = 0;

	if (carlendar.it_timer != null){
		_timer_clear();
	}
}

// 월이동
var Calrendar = function(body, year, month, day, type){
	this.target = null;
	this.object = null;
	this.body = (typeof(body) == 'object' ? body : null);
	this.type = type;
	this.curDate = '';
	this.limit_time = 2;

	if (typeof(body) != null){
		this.body.innerHTML = this.set(year, month, day);
	}
}

Calrendar.prototype.set = function(year, month, date){
	if (this.type == 1){
		return this.cal_date(year, month, date);
	}else{
		return this.cal_month(year, month);
	}
}

Calrendar.prototype.cal_date = function(year, month, date){
	var mouse_event_time = 0;
	var it_timer		 = null;

	var today   = new Date();
	var t_year  = today.getFullYear();
	var t_month = today.getMonth()+1;
	var t_day   = today.getDate();

	t_month = (t_month < 10 ? '0' : '')+t_month;
	t_day   = (t_day   < 10 ? '0' : '')+t_day;

	var t_date = t_year+'-'+t_month+'-'+t_day;

	if (defaultCalDate){
		var nowDate = defaultCalDate;
	}else{
		var nowDate = year + '-' + month + '-01';
	}
	
	var nowYM    = nowDate.split('-');
	var beforeY  = __addDate('yyyy', -1, nowDate).split('-');
	var nextY    = __addDate('yyyy', 1, nowDate).split('-');
	var beforeYM = __addDate('m', -1, nowDate).split('-');
	var nextYM   = __addDate('m', 1, nowDate).split('-');

	var monthsDay = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	
	var startDay = new Date(nowYM[0], parseInt(nowYM[1], 10)-1, 1);
	var lastDay  = monthsDay[parseInt(nowYM[1], 10)];

	if ((nowYM[0] % 400 == 0) || (nowYM[0] % 4 == 0 && nowYM[0] % 100 != 0)){
		if (parseInt(nowYM[1], 10) == 2){
			lastDay = 29;
		}
	}

	var startWeek = startDay.getDay(); //1일의 요일
	var totalWeek = Math.ceil((lastDay + startWeek) / 7); //총 몇 주인지 구하기
	var lastsDay = new Date(nowYM[0], parseInt(nowYM[1], 10)-1, lastDay);
	var lastWeek = lastsDay.getDay(); //마일의 요일

	var cal = '';

	cal = '	<a class="close" onClick="_hiddenCarlendar();"><img alt="달력 레이어 닫기" src="http://www.carevisit.net/image/btn_close.gif" width="15" height="14"></a>'
		+ '	<table class="cal_simple" border="1" cellspacing="0" summary="'+nowYM[0]+'년 '+parseInt(nowYM[1], 10)+'월 달력">'
		+ '	<caption>'
		+ '		<a onClick="_moveCarlendar('+beforeY[0]+','+beforeY[1]+');" style="display:'+(!IsCalBtnShow ? 'none' : '')+';"><img alt=작년 src="http://www.carevisit.net/image/ico_prev_ca.gif" width="6" height="7"></a>'
		+ '		<strong>'+nowYM[0]+'년</strong>'
		+ '		<a onClick="_moveCarlendar('+nextY[0]+','+nextY[1]+');" style="display:'+(!IsCalBtnShow ? 'none' : '')+';"><img alt=내년 src="http://www.carevisit.net/image/ico_next_ca.gif" width="6" height="7"></a>'
		+ '		<a onClick="_moveCarlendar('+beforeYM[0]+','+beforeYM[1]+');" style="display:'+(!IsCalBtnShow ? 'none' : '')+';"><img alt=이전달 src="http://www.carevisit.net/image/ico_prev_ca.gif" width="6" height="7"></a>'
		+ '     <strong>'+parseInt(nowYM[1], 10)+'월</strong>'
		+ '		<a onClick="_moveCarlendar('+nextYM[0]+','+nextYM[1]+');" style="display:'+(!IsCalBtnShow ? 'none' : '')+';"><img alt=다음달 src="http://www.carevisit.net/image/ico_next_ca.gif" width="6" height="7"></a>'
		+ '	</caption>'
		+ '	<thead>'
		+ '		<tr>'
		+ '			<th scope="col">일</th>'
		+ '			<th scope="col">월</th>'
		+ '			<th scope="col">화</th>'
		+ '			<th scope="col">수</th>'
		+ '			<th scope="col">목</th>'
		+ '			<th scope="col">금</th>'
		+ '			<th scope="col">토</th>'
		+ '		</tr>'
		+ '	</thead>'
		+ '	<tbody>';
	
	var day = 1;
	for(var i=1; i<=totalWeek; i++){
		cal += '<tr>';
		for(var j=0; j<7; j++){
			if (!((i == 1 && j < startWeek) || (i == totalWeek && j > lastWeek))){
				if (j == 0){
					cal += '<td><a style="color:#ff0000;" ';
				}else if (j == 6){
					cal += '<td><a style="color:#0000ff;" ';
				}else{
					cal += '<td><a style="color:#000000;" ';
				}

				if (t_date == nowYM[0]+'-'+nowYM[1]+'-'+(day < 10 ? '0' : '')+day){
					var temp_day = '<font color="#0000ff"><b>'+day+'</b></font>';
				}else{
					if (date == day){
						var temp_day = '<b>'+day+'</b>';
					}else{
						var temp_day = day;
					}
				}
				
				cal += 'title='+nowYM[0]+'-'+nowYM[1]+'-'+(day < 10 ? '0' : '')+day+' onClick="_curCarlendar(\''+nowYM[0]+'\',\''+nowYM[1]+'\',\''+day+'\');">'+temp_day+'</a></td>';
				day ++;
			}else{
				cal += '<td></td>';
			}
		}
		cal += '</tr>';
	}
	cal += '</tbody></table>';
	
	return cal;
}

Calrendar.prototype.cal_month = function(year, month){
	var mouse_event_time = 0;
	var it_timer		 = null;

	var today   = new Date();
	var t_year  = today.getFullYear();
	var t_month = today.getMonth()+1;
	
	t_month = (t_month < 10 ? '0' : '')+t_month;
	
	var t_date = t_year+'-'+t_month;

	var nowDate  = year + '-' + month;
	
	var nowYM    = nowDate.split('-');
	var beforeY  = __addDate('yyyy', -1, nowDate).split('-');
	var nextY    = __addDate('yyyy', 1, nowDate).split('-');
	
	var cal = '';

	cal = '	<a class="close" onClick="_hiddenCarlendar();"><img alt="달력 레이어 닫기" src="http://www.carevisit.net/image/btn_close.gif" width="15" height="14"></a>'
		+ '	<table class="cal_simple" border="1" cellspacing="0" summary="'+nowYM[0]+'년 '+parseInt(nowYM[1], 10)+'월 달력">'
		+ '	<caption>'
		+ '		<a onClick="_moveCarlendar('+beforeY[0]+','+beforeY[1]+');"><img alt=작년 src="http://www.carevisit.net/image/ico_prev_ca.gif" width="6" height="7"></a>'
		+ '		<strong>'+nowYM[0]+'년</strong>'
		+ '		<a onClick="_moveCarlendar('+nextY[0]+','+nextY[1]+');"><img alt=내년 src="http://www.carevisit.net/image/ico_next_ca.gif" width="6" height="7"></a>'
		+ '	</caption>'
		+ '	<tbody>';

	for(var i=0; i<2; i++){
		cal += '<tr>';

		for(var j=1; j<=6; j++){
			var mon = (i * 6) + j;
			var str_mon = (mon < 10 ? '0' : '') + mon;

			cal += '<td style="width:50px;"><a onclick="_curCarlendar(\''+nowYM[0]+'\',\''+str_mon+'\');">';

			if (t_date == year + '-' + str_mon){
				cal += '<font color="#0000ff"><b>'+mon+'월</b>';
			}else{
				if (str_mon == month)
					cal += '<font color="#000000"><b>'+mon+'월</b>';
				else
					cal += '<font color="#000000">'+mon+'월';
			}
			
			cal += '</font></a></td>';
		}

		cal += '</tr>';
	}
	
	cal += '</tbody></table>';
	
	return cal;
}

function _timer_init(){
	carlendar.mouse_event_time = 0;
	carlendar.it_timer = setInterval("_timer()",1000);
}

function _timer_clear(){
	carlendar.mouse_event_time = 0;
	clearInterval(carlendar.it_timer);
	carlendar.it_timer = null;
}

function _timer(){
	if (carlendar.mouse_event_time == carlendar.limit_time){
		_timer_clear();
		_hiddenCarlendar();
	}

	carlendar.mouse_event_time ++;
}
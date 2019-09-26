// 달력 열기
var carlendar = null;

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
	if (carlendar == null){
		var today = new Date();
		var year = today.getFullYear();
		var month = today.getMonth()+1;

		month = (month < 10 ? '0' : '')+month;
		carlendar = new Calrendar(calBody, year, month);
	}
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
	var year = p_year;
	var month = p_month;


	if (isNaN(year)) year = today.getFullYear();
	if (isNaN(month)) month = today.getMonth()+1;

	month = (month < 10 ? '0' : '')+month;
	
	if (carlendar == null){
		carlendar = new Calrendar(calBody, year, month);
	}else{
		carlendar.body.innerHTML = carlendar.set(year, month);
	}
}

function _curCarlendar(p_year, p_month, p_day){
	var y = p_year;
	var m = (parseInt(p_month, 10) < 10 ? '0' : '') + parseInt(p_month, 10);
	var d = (parseInt(p_day, 10) < 10 ? '0' : '') + parseInt(p_day, 10);
	
	try{
		carlendar.target.innerHTML = y + '/' + m + '/' + d;
	}catch(e){
	}
	carlendar.object.value = y + '-' + m + '-' + d;

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
				_insToDate(carlendar.object.value, carlendar.object.tag);
			}
		}
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
var Calrendar = function(body, year, month){
	this.target = null;
	this.object = null;
	this.body = (typeof(body) == 'object' ? body : null);
	this.curDate = '';

	if (typeof(body) != null){
		this.body.innerHTML = this.set(year, month);
	}
}

Calrendar.prototype.set = function(year, month){
	var mouse_event_time = 0;
	var it_timer		 = null;

	var nowDate  = year + '-' + month + '-01';
	
	var nowYM    = nowDate.split('-');
	var beforeYM = addDate('m', -1, nowDate).split('-');
	var nextYM   = addDate('m', 1, nowDate).split('-');

	var monthsDay = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	
	var startDay = new Date(nowYM[0], parseInt(nowYM[1], 10)-1, 1);
	var lastDay = monthsDay[parseInt(nowYM[1], 10)];
	var startWeek = startDay.getDay(); //1일의 요일
	var totalWeek = Math.ceil((lastDay + startWeek) / 7); //총 몇 주인지 구하기
	var lastsDay = new Date(nowYM[0], parseInt(nowYM[1], 10)-1, lastDay);
	var lastWeek = lastsDay.getDay(); //마일의 요일
	
	var cal = '';

	cal = '	<a class="close" onClick="_hiddenCarlendar();"><img alt="달력 레이어 닫기" src="http://www.carevisit.net/image/btn_close.gif" width="15" height="14"></a>'
		+ '	<table class="cal_simple" border="1" cellspacing="0" summary="'+nowYM[0]+'년 '+parseInt(nowYM[1], 10)+'월 달력">'
		+ '	<caption>'
		+ '		<a onClick="_moveCarlendar('+beforeYM[0]+','+beforeYM[1]+');"><img alt=이전달 src="http://www.carevisit.net/image/ico_prev_ca.gif" width="6" height="7"></a>'
		+ '		<strong>'+nowYM[0]+'년 '+parseInt(nowYM[1], 10)+'월</strong>'
		+ '		<a onClick="_moveCarlendar('+nextYM[0]+','+nextYM[1]+');"><img alt=다음달 src="http://www.carevisit.net/image/ico_next_ca.gif" width="6" height="7"></a>'
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
				cal += 'title='+nowYM[0]+'-'+nowYM[1]+'-'+day+' onClick="_curCarlendar(\''+nowYM[0]+'\',\''+nowYM[1]+'\',\''+day+'\');">'+day+'</a></td>';
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

function _timer_init(){
	carlendar.mouse_event_time = 0;
	carlendar.it_timer = setInterval("_timer()",1000);
}

function _timer_clear(){
	clearInterval(carlendar.it_timer);
	carlendar.it_timer = null;
}

function _timer(){
	if (carlendar.mouse_event_time >= 3){
		_timer_clear();
		_hiddenCarlendar();
	}

	carlendar.mouse_event_time ++;
}
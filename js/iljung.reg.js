var _MODE_ = 1;
var _HOUR_ = 60; //서비스 최소 시간
var _MIN_ = 30;

//기관정보
function _set_center_info(code, key, year, month, svc_id, mode, day){
	var URL = 'iljung_center_info.php';
	var params = {'code':code,'key':key,'year':year,'month':month,'svc_id':svc_id,'day':day,'mode':mode};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				center_info.innerHTML = responseHttpObj.responseText;

				if (mode == 'VOUCHER'){
					_set_service_use(svc_id,day);
				}else if (mode == 'VOUCHER_OVERTIME'){
					_set_service_overtime(svc_id,day);
				}else if (mode == 'ERROR'){
					_set_error_msg(svc_id);
				}else{
					if (mode == 'IN'){
						svc_id = __object_get_value('svc_id[]');
					}else if (mode == 'ADD_CONF'){
						document.getElementsByName('svc_id[]')[0].checked = true;
						svc_id = __object_get_value('svc_id[]');
					}else{
						if (svc_id == '11'){
							svc_id  = __object_get_value('svc_id[]',opener);
						}
					}
					
					_set_iljung_reg(svc_id,mode);
				}
			}
			//onFailure:function...
		}
	);
}

//이용서비스
function _set_service_use(svc_id,seq){
	var resize = 1;

	try{
		resize = document.getElementById('onload').value;
	}catch(e){
		//alert(e.description);
	}

	var URL = 'iljung_service_use.php';
	var params = {'code':document.getElementById('code').value,'svc_id':svc_id,'jumin':document.getElementById('jumin').value,'year':document.getElementById('year').value,'month':document.getElementById('month').value,'seq':seq};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				use_service.innerHTML = responseHttpObj.responseText;

				__window_resize(900, document.getElementById('window_body').offsetHeight+88);
				__init_form(document.f);
				_makeVoucherUseIfno();
			}
		}
	);
}

//이용서비스 이월시간 등록
function _set_service_overtime(svc_id, seq){
	var URL = 'iljung_service_overtime.php';
	var params = {'code':document.getElementById('code').value,'svc_id':svc_id,'jumin':document.getElementById('jumin').value,'year':document.getElementById('year').value,'month':document.getElementById('month').value,'seq':seq};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				use_service.innerHTML = responseHttpObj.responseText;

				__window_resize(900, document.getElementById('window_body').offsetHeight+88);
				__init_form(document.f);
			}
		}
	);
}

// 재가일정등록
function _set_iljung_reg(svc_id, mode){
	var URL = 'iljung_reg_sub.php';
	var params = {'code':document.getElementById('code').value,'kind':document.getElementById('kind').value,'svc_id':svc_id,'jumin':document.getElementById('jumin').value,'key':document.getElementById('key').value,'year':document.getElementById('year').value,'month':document.getElementById('month').value,'day':document.getElementById('day').value,'mode':mode};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				iljung_care.innerHTML = responseHttpObj.responseText;

				try{
					if (svc_id > 20 && svc_id < 30){
						//바우처
						/*
						if (document.getElementById('voucher_make_yn').value == 'N'){
							var code  = document.getElementById('code').value;
							var kind  = document.getElementById('kind').value;
							var key   = document.getElementById('key').value;
							var year  = document.getElementById('year').value
							var month = document.getElementById('month').value
							
							location.replace('iljung_error.php?mCode='+code+'&mKind='+kind+'&mKey='+key+'&calYear='+year+'&calMonth='+month);
							return;
						}
						*/
					}
				}catch(e){
					//__show_error(e);
				}

				if (mode == 'IN'){
					if (_MODE_ == 1)
						_set_iljung_botton();
					else
						_set_iljung_status();
				}else if (mode == 'ADD_CONF'){
					_set_voucher_svc();
				}else if (mode == 'MODIFY'){
					if (svc_id > 10 && svc_id < 20){
						_TIMER_ADD_ = setInterval("_set_care_values()",100);
					}else{
						_TIMER_ADD_ = setInterval("_set_voucher_values()",100);
					}
				}else{
					if (mode == 'ADD'){
						_set_voucher_svc();
					}
					_set_bipay_pay();
					//_is_make_voucher();
				}
			}
		}
	);
}

//일정수급현황
function _set_iljung_status(){
	var svc_id  = _get_current_svc('id');
	var URL     = 'iljung_const.php';
	var params  = {'type':'reg','code':document.getElementById('code').value,'kind':document.getElementById('kind').value,'svc_id':svc_id,'jumin':document.getElementById('jumin').value,'key':document.getElementById('key').value,'year':document.getElementById('year').value,'month':document.getElementById('month').value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_get_iljung_status
		}
	);
}

//
function _get_iljung_status(responseHttpObj){
	iljung_const.innerHTML = responseHttpObj.responseText;
	_set_voucher_svc();
	_set_bipay_pay();
	_is_make_voucher();
	_set_addpay_summly();
	_setSvcInfo();
}

function _set_iljung_botton(){
	var f       = document.f;
	var msg     = ''; //f.iljung_msg.value
	var code    = f.code.value;
	var kind    = f.kind.value;
	var year    = f.year.value;
	var month   = f.month.value;
	var key     = f.key.value;
	var vm_yn   = f.voucher_make_yn.value;
	var URL     = 'iljung_button.php';
	var params  = {'msg':msg,'code':code,'kind':kind,'year':year,'month':month,'key':key,'vm_yn':vm_yn};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_get_iljung_botton,
			onError:__ajax_error,
			onFailure:__ajax_failure
		}
	);
}

function _get_iljung_botton(responseHttpObj){
	iljung_button.innerHTML = responseHttpObj.responseText;
	_set_voucher_svc();
	_set_calendar(document.getElementById('year').value, document.getElementById('month').value);
}

function _set_calendar(pCalYear, pCalMonth){
	var calYear  = null;
	var calMonth = null;
	var code     = null;
	var kind     = null;
	var key      = null;
	var jumin    = null;
	
	try{
		calYear = pCalYear ;
		calMonth = pCalMonth;

		if (calYear == undefined || calMonth == undefined){
			calYear = '';
			calMonth = '';
		}

		if (calYear == '' || calMonth == ''){
			calYear  = document.f.calYear.value;
			calMonth = document.f.calMonth.value;
		}
	}catch(e){
		var now = new Date();

		calYear = now.getFullYear();
		calMonth = now.getMonth()+1;
	}

	code  = document.f.code.value;
	kind  = document.f.kind.value;
	key   = document.f.key.value;
	jumin = document.f.jumin.value;
	svcId = _get_current_svc('id');
	
	var URL     = 'iljung_calendar.php';
	var params  = {'gubun':'reg','calYear':calYear,'calMonth':calMonth,'mCode':code,'mKind':kind,'mKey':key,'mJuminNo':jumin,'svcId':svcId};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_get_calendar
		}
	);
}

function _get_calendar(responseHttpObj){
	iljung_calendar.innerHTML = responseHttpObj.responseText;

	var code  = document.f.code.value;
	var kind  = document.f.kind.value;
	var jumin = document.f.jumin.value;
	var year  = document.f.calYear.value;
	var month = document.f.calMonth.value;
	
	_set_iljung_status();
	_patternClose(pattern);
	_patternList(pattern, code, kind, jumin, year, month );
	_set_bipay_pay();
	_set_current_svc_enabled();
}

function _set_error_msg(svc_id){
	var URL     = 'iljung_error_msg.php';
	var params  = {'svc_id':svc_id};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:_get_error_msg
		}
	);
}

function _get_error_msg(responseHttpObj){
	error_message.innerHTML = responseHttpObj.responseText;

	__window_resize(1000, 185, 1);
}

///////////////////////////////////////////////////////
//
// 선택된서비스
//
///////////////////////////////////////////////////////
function _get_current_svc(type){
	var svc = document.getElementsByName('svc_id[]');
	var idx = 0;

	for(var i=0; i<svc.length; i++){
		if (svc[i].checked){
			idx = i;
			break;
		}
	}

	var rst = null;

	if (type == 'id'){
		rst = svc[idx].value;
	}else{
		rst = document.getElementsByName('svc_cd[]')[idx].value;
	}

	return rst;
}

//선택된서비스명
function _get_current_svc_nm(cd, type){
	if (type == 'id'){
		var svc = document.getElementsByName('svc_id[]');
	}else{
		var svc = document.getElementsByName('svc_cd[]');
	}

	var idx = 0;
	
	for(var i=0; i<svc.length; i++){
		if (svc[i].value == cd){
			idx = i;
			break;
		}
	}

	var rst = document.getElementsByName('svc_nm[]')[idx].value;

	return rst;
}

//서비스선택
function _set_current_svc(svc_id){
	var svc = document.getElementsByName('svc_id[]');

	for(var i=0; i<svc.length; i++){
		if (svc[i].value == svc_id){
			svc[i].checked = true;
			break;
		}
	}

	var mode = null;

	if (location.href.indexOf('iljung_reg') > -1){
		mode = 'IN';
	}else{
		mode = document.getElementById('mode').value;
	}

	_MODE_ = 2;
	_set_iljung_reg(svc_id, mode);

	
	/*********************************************************
		해당서비스만활성화
	*********************************************************/
	_set_current_svc_enabled();
}

//일정입력시간을 체크한다.
function _iljung_check_time(obj){
	var svc_id   = _get_current_svc('id');
	var svc_kind = __object_get_value('svcSubCode'); 
	var bipay_yn = document.getElementById('bipayUmu').checked ? 'Y' : 'N';
	
	if (svc_id > 20 && svc_id < 30){
		var svc_gbn = document.getElementById('svcGbn').value;
	}else{
		var svc_gbn = '';
	}

	if (svc_id == 23){
		/***************************************************************

			산모신생아

		***************************************************************/
		
		if (bipay_yn != 'Y'){
			//비급여가 아닐 경우 수가를 찾는다.
			_get_iljung_suga();
			return;
		}
	}
	
	if (typeof(obj) != 'object'){
		_get_svc_suga();
		return;
	}	
	
	if (__check_max_length(obj)){
		var chk_hour_change = false;
		var chk_hour        = 0;

		//기본체크시간
		if (svc_id == 22){
			if (svc_gbn == 'V'){
				chk_hour = _HOUR_ * 1; // 2012.02.06 기본 2시간에서 1시간으로 변경
			}else{
				chk_hour = _HOUR_ * 9;
				chk_hour_change = true;
			}
		}else if (svc_id == 24){
			if (svc_kind == '500' ||
				svc_kind == '800'){
				chk_hour = 0;
			}else{
				chk_hour = _HOUR_
			}
		}else{
			chk_hour = _HOUR_
		}
		
		var from_hour = document.getElementById('ftHour');
		var from_min  = document.getElementById('ftMin');
		var to_hour   = document.getElementById('ttHour');
		var to_min    = document.getElementById('ttMin');
		var proc_time = document.getElementById('procTime');

		if (obj.name == 'ftHour' || obj.name == 'ttHour'){
			if (obj.value > 23) obj.value = '23';
		}

		if (obj.name == 'ftMin' || obj.name == 'ttMin'){
			if (obj.value > 59) obj.value = '00'
		}
		
		if (obj.name == 'ftHour' || obj.name == 'ftMin'){
			if (from_min.value == '') from_min.value = '00';
		}

		from_hour.value = (parseInt(from_hour.value, 10) < 10 ? '0' : '')+parseInt(from_hour.value, 10);
		to_hour.value   = (parseInt(to_hour.value, 10) < 10 ? '0' : '')+parseInt(to_hour.value, 10);

		var from_time = _getTimeValue(from_hour.value, from_min.value);
		var to_time   = _getTimeValue(to_hour.value, to_min.value);
		var new_time  = to_time - from_time;
		
		if (new_time < 0){
			new_time += (24 * _HOUR_);
		}
		
		//시작시간과 종료시간의 차이가 1시간이 안되면 1시간으로 조절한다.
		if (new_time < chk_hour || isNaN(new_time)){
			if (obj.name == from_hour.name ||
				obj.name == from_min.name){
				
				if (svc_kind == '500'){
					var to_time = getTimeValue(from_hour.value+from_min.value, _HOUR_);
				}else if (svc_kind == '800'){
					var to_time = getTimeValue(from_hour.value+from_min.value, 29);
				}else{
					var to_time = getTimeValue(from_hour.value+from_min.value, chk_hour);
				}
			}else{
				var to_time = getTimeValue(from_hour.value+from_min.value, chk_hour);
			}
			
			to_hour.value = to_time[0];
			to_min.value  = to_time[1];
		}
		
		from_time = _getTimeValue(from_hour.value, from_min.value);
		to_time   = _getTimeValue(to_hour.value, to_min.value);
		new_time  = to_time - from_time;
		
		if (new_time < 0){
			new_time += (24 * _HOUR_);
		}
		
		if (Math.ceil(new_time / chk_hour) < 1){
			//add_time = chk_hour - (new_time % chk_hour);
			//to_time  = getTimeValue(to_hour.value+to_min.value, add_time);
			to_time = addTime('n', chk_hour, from_hour.value+':'+from_min.value+':00').split(':');

			to_hour.value = (to_time[0] < 10 ? '0' : '')+to_time[0];
			to_min.value  = (to_time[1] < 10 ? '0' : '')+to_time[1];
		}

		from_time = _getTimeValue(from_hour.value, from_min.value);
		to_time   = _getTimeValue(to_hour.value, to_min.value);
		
		if (svc_id == 21){
			cal_time_gbn = 1;
		}else if (svc_id == 24){
			if (svc_kind == '500' ||
				svc_kind == '800'){
				cal_time_gbn = 3;
			}else{
				cal_time_gbn = 1;
			}
		}else if (svc_id == 31){
			cal_time_gbn = 4;
		}else{
			cal_time_gbn = 2;
		}
		
		if (cal_time_gbn == 1 /*svc_id == 21*/){
			var int_time = (to_time - from_time) % 60;
			
			if (int_time >= 15 && int_time < 45){
				int_time = 30;
			}else if (int_time >= 45){
				int_time = 60;
			}else{
				int_time = 0;
			}
			
			//int_time = Math.floor((to_time - from_time) / 60) + (int_time / 60);
			int_time = Math.floor((to_time - from_time) / 60 + (int_time / 60));
		}else if (cal_time_gbn == 2){
			var int_time = __com_time(to_time - from_time);
		}else if (cal_time_gbn == 4){
			var int_time = 1;
		}else{
			var int_time = __round((to_time - from_time) / 60, 2);
		}
		
		// 노인돌봄 주간방문인 경우 1일 9시간으로 처리한다.
		if (svc_id == 22 && svc_gbn == 'D'){
			int_time = 1;
		}
		
		
		
		/**************************************************
		
			종료일이 익일로 넘어갔다.
		
		**************************************************/
		if (int_time < 0){
			int_time += 24;
		}
		
		
		
		/**************************************************
		
			입력제한
		
		**************************************************/
		var limit_time = document.getElementById('svcLimitTime'+svc_kind).value;
		
		if (limit_time > 0){
			if (svc_kind == '500'){
				/**************************************************
				
					방문목욕/주 횟수 제한(1회)
				
				**************************************************/
			
			}else if (svc_kind == '800'){
				/**************************************************
				
					방문간호/주 횟수 제한(3회)
					
				**************************************************/
				
			}else{
				/**************************************************
				
					기타 서비스/1일 시간제한
				
				**************************************************/
				if (svc_id == 24){
					var mem_cnt = _set_mem_cnt(); //입력된 제공자수
					
					if (mem_cnt > 1)
						limit_time = 3;
				}
				
				if (limit_time * _HOUR_ < new_time){
					alert('선택하신 시작시간부터 최대 '+limit_time+'시간까지 등록가능합니다.');
					int_time = limit_time;
					
					to_time = getTimeValue(from_hour.value+''+from_min.value, int_time * _HOUR_);
					
					to_hour.value = to_time[0];
					to_min.value  = to_time[1];
					
					_iljung_check_time(to_hour);
					return;
				}
			}
		}
		
		
		proc_time.value = int_time; //(to_time - from_time) / chk_hour;

		//////////////////////////////////////////////////////////////////
		//
		// 수가를 조회한다.
		//
			_get_iljung_suga();
		//
		//////////////////////////////////////////////////////////////////

		if (window.event.keyCode == 9){
			// TAB시 포커스를 이동하지 않는다.
		}else{
			switch(obj.name){
				case 'ftHour':
					from_min.focus();
					break;
				case 'ftMin':
					to_hour.focus();
					break;
				case 'ttHour':
					to_min.focus();
					break;
				case 'ttMin':
					break;
			}
		}
	}
}

/***************************************************************

	비급여 설정

***************************************************************/
function _set_bipay_yn(){
	var svc_id    = __object_get_value('svc_id[]');
	var bipay_yn  = document.getElementById('bipayUmu').checked ? 'Y' : 'N';
	var suga_cost = document.getElementById('sugaCost');
	var from_hour = document.getElementById('ftHour');
	var from_min  = document.getElementById('ftMin');
	var to_hour   = document.getElementById('ttHour');
	var to_min    = document.getElementById('ttMin');
	var procTime  = document.getElementById('procTime');
	
	if (bipay_yn == 'Y'){
		/*
		suga_cost.readOnly = false;
		suga_cost.style.backgroundColor = '#ffffff';
		suga_cost.onchange = function(){
			var f    = document.f;
			var time = document.f.sugaTime.value;
			var amt  = document.f.sugaTot;
			
			amt.value = __num2str(__str2num(this.value) * time);
		}
		*/
		
		if (svc_id == '23'){
			from_hour.readOnly = false;
			from_min.readOnly  = false;
			to_hour.readOnly   = false;
			to_min.readOnly    = false;
			
			from_hour.style.backgroundColor = '#ffffff';
			from_min.style.backgroundColor  = '#ffffff';
			to_hour.style.backgroundColor   = '#ffffff';
			to_min.style.backgroundColor    = '#ffffff';
		}
	}else{
		/*
		suga_cost.readOnly = true;
		suga_cost.style.backgroundColor = '#eeeeee';
		suga_cost.onchange = null;
		*/
		
		if (svc_id == '23'){
			from_hour.readOnly = true;
			from_min.readOnly  = true;
			to_hour.readOnly   = true;
			to_min.readOnly    = true;
			
			from_hour.style.backgroundColor = '#efefef';
			from_min.style.backgroundColor  = '#efefef';
			to_hour.style.backgroundColor   = '#efefef';
			to_min.style.backgroundColor    = '#efefef';

			from_hour.value = from_hour.tag;
			from_min.value  = from_min.tag;
			to_hour.value   = to_hour.tag;
			to_min.value    = to_min.tag;
			procTime.value  = procTime.tag;
		}
	}
	__init_object(suga_cost);
}

/**************************************************
	
	수가조회

**************************************************/
function _get_iljung_suga(){
	//if ($('#code').attr('value') == '1234'){
	//	var svcWeekday = _iljungGetVoucherSuga(false);
	//	var svcHoliday = _iljungGetVoucherSuga(true);
	//}

	var f         = document.f;
	var suga_obj  = document.getElementById('sugaCont');
	var svc_id    = _get_current_svc('id');           //선택된 서비스코드
	var svc_cd    = _get_current_svc();               //선택된 서비스코드
	var svc_kind  = __object_get_value('svcSubCode'); //제공서비스
	var suga_cd   = null; //수가코드
	var bipay_yn  = document.getElementById('bipayUmu').checked ? 'Y' : 'N';
	var c_cd      = document.getElementById('jumin').value;

	if (f.mode.value == 'IN'){
		var date = document.f.calYear.value+document.f.calMonth.value;
	}else{
		var date = document.f.addDate.value;
	}
	
	if (svc_id > 20 && svc_id < 30){
		/**************************************************
	
			바우처 수가
		
		**************************************************/
		if (svc_kind == '500'){
			/**************************************************
			
				방문목욕
			
			**************************************************/
			var suga_cd  = document.getElementById('svcSuga').value.substring(0,2); //수가코드
				suga_cd += 'B';
				suga_cd += f.svcSubCD.value;
				suga_cd += '0';
				
		}else if (svc_kind == '800'){
			/**************************************************
			
				방문간호
			
			**************************************************/
			var suga_cd  = document.getElementById('svcSuga').value.substring(0,2); //수가코드
				suga_cd += 'N';
				
				var from_time = _getTimeValue(f.ftHour.value, f.ftMin.value);
				var to_time   = _getTimeValue(f.ttHour.value, f.ttMin.value);
				var time_gab  = to_time - from_time;
				
				if (time_gab < 30){
					suga_cd += '1';
				}else if (time_gab > 30 && time_gab < 60){
					suga_cd += '2';
				}else{
					suga_cd += '3';
				}
				
				suga_cd += '0';
			
		}else{
			var suga_cd = document.getElementById('svcSuga').value; //수가코드
		}
	}else{
		/**************************************************
	
			기타유료 수가
		
		**************************************************/
		var suga_cd = 'VZ' + __object_set_format(svc_id, 'number', 3, '0'); //수가코드
	}
	
	var mode    = f.mode.value;
	var suga_tm = getHttpRequest('iljung_value.php?type=suga_info&code='+f.code.value+'&svc_kind='+svc_cd+'&suga_cd='+suga_cd+'&c_cd='+c_cd+'&bipay_yn='+bipay_yn+'&date='+date);
	var suga_if = suga_tm.split(__COL_CUTER__);
	var suga_nm = suga_if[0]; //수가명
	var holiday = suga_if[suga_if.length - 2];

	if (mode != 'MODIFY' && bipay_yn != 'Y'){
		//var suga_stnd  = suga_if[!holiday ? 2 : 4]; //수가 기본단가
		var suga_stnd  = suga_if[2]; //수가 기본단가
	}else{
		if (bipay_yn != 'Y'){
			if (svc_id == 24){
				var suga_stnd  = suga_if[2];	
			}else{
				var suga_stnd = suga_if[!holiday ? 2 : 4]; //수가 기본단가
			}
		}else{
			var suga_stnd = __str2num(f.sugaCost.value); //수가 기본단가
		}
	}

	
	/**************************************************
	
		연장단가
	
	**************************************************/
	var suga_night = suga_if[3]; //수가 연장단가
	
	if (suga_night == 0) suga_night = suga_stnd;
	/*************************************************/
	
	
	//연장수가 카운트
	var suga_cnt_night = 0;
	var extrapay       = 0;

	if (bipay_yn != 'Y'){
		if (svc_kind == '500' ||
			svc_kind == '800'){
			/**************************************************
			
				방문목욕, 방문간호는 회당 수가로 처리한다.
			
			**************************************************/
			var suga_cnt  = 1;         //단가를 맞출 횟수
			var suga_cost = suga_stnd; //수가 단가
			var extrapay  = parseInt(getHttpRequest('../inc/_check.php?gubun=getSudangPrice&mCode='+f.code.value+'&mSuga='+suga_cd)); //수당
			
		}else{
			if (svc_id == 24){
				/**************************************************
				
					장애활동지원
				
				**************************************************/
				var from_time = _getTimeValue(f.ftHour.value, f.ftMin.value); //시작시간
				var to_time   = _getTimeValue(f.ttHour.value, f.ttMin.value); //종료시간
				var proctimes = to_time - from_time; //진행시간
				
				var hour_stnd    = 0; //기준시간
				var hour_prolong = 0; //연장시간
				
				if (proctimes < 0) proctimes += (24 * _HOUR_);

				if (!holiday){
					var time_list = {0:[6 * _HOUR_, 22 * _HOUR_], 
									 1:[22 * _HOUR_, 6 * _HOUR_ + 24 * _HOUR_],
									 2:[0, 6 * _HOUR_]};
				}else{
					var time_list = {0:[0 * _HOUR_, 24 * _HOUR_],
									 1:[0 * _HOUR_, 24 * _HOUR_],
									 2:[0 * _HOUR_, 24 * _HOUR_]};
				}
								 
				if ((from_time >= time_list[1][0] && from_time < time_list[1][1]) ||
					(from_time >= time_list[2][0] && from_time < time_list[2][1])){
					/**************************************************
					
						시작이 22시 이후부터 연장수가를 적용한다.
					
					**************************************************/
					//alert(from_time + '/' + time_list[2][0] + '/' + time_list[2][1] + '/' + proctimes);
					if (from_time >= time_list[1][0] && from_time < time_list[1][1]){
						var index = 1;
					}else{
						var index = 2;
					}
					
					if (from_time + proctimes > time_list[index][1]){
						hour_prolong = time_list[index][1] - from_time;
					}else{
						hour_prolong = proctimes;
					}
					
				}else if ((to_time >= time_list[1][0] && to_time < time_list[1][1]) ||
						  (to_time >= time_list[2][0] && to_time < time_list[2][1])){
					/**************************************************
					
						종료가 22시를 넘어가면 연장수가를 적용한다.
					
					**************************************************/
					//alert('to_time : '+to_time+'/'+time_list[1][0]+'/'+time_list[1][1]+'-'+proctimes);
					if (to_time >= time_list[1][0]){
						var tmp_time = to_time;
					}else{
						var tmp_time = to_time + 24 * _HOUR_;
					}
					
					hour_prolong = tmp_time - time_list[1][0];
					
				}else{
					hour_stnd = proctimes;
				}
				
				/**************************************************
							
					연장 최대시간은 4시간으로 제한한다.
				
				**************************************************/
				if (hour_prolong > 4 * _HOUR_) hour_prolong = 4 * _HOUR_;
				
				hour_stnd = proctimes - hour_prolong;

				suga_cnt       = Math.round(hour_stnd / _HOUR_);    //기준시간
				suga_cnt_night = Math.round(hour_prolong / _HOUR_); //연장시간
				
				var mem_cnt = _set_mem_cnt(); //입력된 제공자수
				
				if (mem_cnt > 1){
					if (mode != 'MODIFY'){
						var suga_cost = suga_stnd * 1.5; //수가 단가
					}else{
						var suga_cost = suga_stnd;
					}
				}else{
					var suga_cost = suga_stnd; //수가 단가
				}
			}else{
				var suga_cnt  = f.svcCost.value / suga_stnd; //단가를 맞출 횟수
				var suga_cost = suga_stnd * suga_cnt;        //수가 단가
			}
		}
	}else{
		try{
			var bipay_kind = __object_get_value('bipay_kind');
			var suga_if    = getHttpRequest('iljung_value.php?type=suga_care&code='+f.code.value+'&suga_cd='+suga_cd).split('//');
			
			document.getElementById('bipay_cost_publid').value  = __num2str(suga_if[1]);
			document.getElementById('bipay_cost_private').value = __num2str(suga_if[2]);
		}catch(e){
			var bipay_kind = '3';
		}
		
		switch(bipay_kind){
			case '1':
				suga_stnd = __str2num(document.getElementById('bipay_cost_publid').value);
				break;
			case '2':
				suga_stnd = __str2num(document.getElementById('bipay_cost_private').value);
				break;
			default:
				suga_stnd = __str2num(document.getElementById('exp_max_pay').value);
		}
		
		if (svc_kind == '500' ||
			svc_kind == '800'){
			var suga_cnt = 1; //단가를 맞출 횟수
			var suga_cost = suga_stnd * suga_cnt; //수가 단가
		}else{
			var suga_cnt = (!isNaN(parseInt(f.procTime.value, 10)) ? parseInt(f.procTime.value, 10) : 0);
			var suga_cost = suga_stnd; //수가 단가
		}
	}

	suga_obj.innerHTML = suga_nm;

	f.sugaCode.value = suga_cd;
	f.sugaName.value = suga_nm;

	f.svcStnd.value = suga_stnd;
	f.svcCnt.value  = suga_cnt;

	f.sugaCost.value = __num2str(suga_cost);  //단가

	
	if (svc_id == 24){
		if (suga_cnt_night > 0){
			f.sugaCostNight.value = __num2str(suga_night);
			f.sugaTimeNight.value = __num2str(suga_cnt_night);
		}else{
			f.sugaCostNight.value = 0;
			f.sugaTimeNight.value = 0;
		}
	}
	
	if (svc_id == 31){
		if (__num2str(f.procTime.value) > 6){
			f.sugaTime.value = __num2str(f.procTime.value) - 1;
		}else{
			f.sugaTime.value = __num2str(f.procTime.value); //소요시간
		}
	}else if (svc_id == 24){
		if (svc_kind == '500' ||
			svc_kind == '800'){
			f.sugaTime.value = __num2str(f.procTime.value); //소요시간
		}else{
			f.sugaTime.value = __num2str(suga_cnt); //소요시간
		}
	}else if (svc_id == 23){
		f.sugaTime.value = __num2str(suga_cnt); //소요시간
	}else{
		f.sugaTime.value = __num2str(f.procTime.value); //소요시간
	}

	if (svc_kind == '500' ||
		svc_kind == '800'){
		f.sugaTot.value     = f.sugaCost.value;    //수가계
		f.visitSudang.value = __num2str(extrapay); //방문수당
	}else{
		var suga_tot = __str2num(f.sugaCost.value) * __str2num(f.sugaTime.value);
		
		if (suga_cnt_night > 0){
			suga_tot += (__str2num(f.sugaCostNight.value) * __str2num(f.sugaTimeNight.value));
		}
		f.sugaTot.value = __num2str(suga_tot); //수가계
	}
}

/////////////////////////////////////////
//
// 기타유료 서비스내역 초기화
//
/////////////////////////////////////////
function _init_month_amount_other(){
	var svc_id   = document.getElementsByName('svc_id[]');
	var tmp_id   = _get_current_svc('id');
	var start_id = parseInt(tmp_id, 10) - (parseInt(tmp_id,10) % 10);
	var end_id   = parseInt(start_id, 10) + 10;

	try{
		for(var i=0; i<svc_id.length; i++){
			if (parseInt(svc_id[i].value, 10) > start_id && 
				parseInt(svc_id[i].value, 10) < end_id){
				var tot_pay  = document.getElementById('tot_pay_'+svc_id[i].value);
				var over_pay = document.getElementById('over_pay_'+svc_id[i].value);
				var bipay    = document.getElementById('bipay_'+svc_id[i].value);
				var use_time = document.getElementById('use_time_'+svc_id[i].value);
				var re_time  = document.getElementById('re_time_'+svc_id[i].value);

				tot_pay.innerHTML  = __num2str(tot_pay.tag);
				over_pay.innerHTML = __num2str(over_pay.tag);
				bipay.innerHTML    = __num2str(bipay.tag);
				use_time.innerHTML = __num2str(use_time.tag);
				re_time.innerHTML  = __num2str(re_time.tag);
			}
		}
	}catch(e){
		//alert(e.description);
	}
}





/**************************************************

	기타유료 서비스내역 수정

**************************************************/
function _set_month_amount_other(type, old_svc, new_svc, old_suga, new_suga, old_time, new_time, old_bipay, new_bipay){
	var svc_id = _get_current_svc('id');
	
	if (svc_id == 24){
		_set_month_amount_voucher_dis(type, old_svc, new_svc, old_suga, new_suga, old_time, new_time, old_bipay, new_bipay);
	}else{
		_set_month_amount_other_voucher(type, old_svc, new_svc, old_suga, new_suga, old_time, new_time, old_bipay, new_bipay);
	}
}



/**************************************************

	장애인할동지원 서비스내역 수정

**************************************************/
function _set_month_amount_voucher_dis(type, old_svc, new_svc, old_suga, new_suga, old_time, new_time, old_bipay, new_bipay){
	/*
	if ($('#code').attr('value') != '1234'){
		if (type == 'delete' || type == 'insert'){
			var obj_opener = document;
		}else{
			var obj_opener = opener.document;
		}
		
		var svc_if =  'bipay_time_200='+__str2num(obj_opener.getElementById('bipay_time_200').value)
				   + '&bipay_time_500='+__str2num(obj_opener.getElementById('bipay_time_500').value)
				   + '&bipay_time_800='+__str2num(obj_opener.getElementById('bipay_time_800').value)
				   + '&bipay_pay_200=' +__str2num(obj_opener.getElementById('bipay_pay_200').value)
				   + '&bipay_pay_500=' +__str2num(obj_opener.getElementById('bipay_pay_500').value)
				   + '&bipay_pay_800=' +__str2num(obj_opener.getElementById('bipay_pay_800').value)
				   + '&use_time_200='  +__str2num(obj_opener.getElementById('use_time_200').value)
				   + '&use_time_500='  +__str2num(obj_opener.getElementById('use_time_500').value)
				   + '&use_time_800='  +__str2num(obj_opener.getElementById('use_time_800').value)
				   + '&use_pay_200='   +__str2num(obj_opener.getElementById('use_pay_200').value)
				   + '&use_pay_500='   +__str2num(obj_opener.getElementById('use_pay_500').value)
				   + '&use_pay_800='   +__str2num(obj_opener.getElementById('use_pay_800').value);
				   
		var svc_id  = _get_current_svc('id');
		var URL     = '../iljung/iljung_const.php';
		var params  = {'type':type, 'code':document.getElementById('code').value, 'kind':document.getElementById('kind').value, 'svc_id':svc_id, 'jumin':document.getElementById('jumin').value, 'key':document.getElementById('key').value, 'year':document.getElementById('year').value, 'month':document.getElementById('month').value, 'old_svc':old_svc, 'new_svc':new_svc, 'old_suga':old_suga, 'new_suga':new_suga, 'old_time':old_time, 'new_time':new_time, 'old_bipay':old_bipay, 'new_bipay':new_bipay, 'svc_if':svc_if};
		
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:params,
				onComplete:function(responseHttpObj){
					
				},
				onError:function(){
					alert('error');
				},
				onSuccess:function(responseHttpObj){
					var const_body = obj_opener.getElementById('iljung_const');
						const_body.innerHTML = responseHttpObj.responseText;
				}
			}
		);

		return;
	}
	*/

	/*********************************************************
		테스트...
	*********************************************************/
	_setSvcInfo(type);
}



/*********************************************************

	서비스 내역정보

*********************************************************/
function _setSvcInfo(type){
	if (!type) type = 'onload';

	//대상
	if (type == 'delete' || type == 'insert' || type == 'onload'){
		$objOpener = document;
	}else{
		$objOpener = opener.document;
	}

	if ($(':radio[name="svc_id[]"]',$objOpener).attr('value') != '24') return;


	/*********************************************************

		새로 작업 중.

	*********************************************************/
	//초기화
	$('.strConstClass', $objOpener).each(function(){
		if ($(this).attr('id').toString().substring(0, ('strLeftTime').length) == 'strLeftTime'){
			$id = $(this).attr('id').toString().substring(('strLeftTime').length,$(this).attr('id').toString().length);
			$('#strLeftTime'+$id,$objOpener).text( $('#strLimitTime'+$id,$objOpener).text() );
		
		}else if ( $(this).attr('id').toString().substring(0, ('strLeftPay').length) == 'strLeftPay'){
			$id = $(this).attr('id').toString().substring(('strLeftPay').length,$(this).attr('id').toString().length);
			$('#strLeftPay'+$id,$objOpener).text( $('#strLimitPay'+$id,$objOpener).text() );
		
		}else{
			$(this).text('0');
		}
	});

	$('#strTotTimeSum',   $objOpener).text('0');
	$('#strTotPaySum',    $objOpener).text('0');
	$('#strBipayTimeSum', $objOpener).text('0');
	$('#strBipayPaySum',  $objOpener).text('0');
	$('#strUseTimeSum',   $objOpener).text('0');
	$('#strUsePaySum',    $objOpener).text('0');
	
	$('#strLeftTotTime', $objOpener).text($('#strLimitTotTime', $objOpener).text());
	$('#strLeftTotPay',  $objOpener).text($('#strLimitTotPay',  $objOpener).text());

	$sugaIfno = getHttpRequest('./iljung_value.php?type=suga_info&code='+$('#code',$objOpener).attr('value')+'&svc_kind=4&suga_cd='+$('#svcSuga',$objOpener).attr('value')+'&date='+$('#year',$objOpener).attr('value')+''+$('#month',$objOpener).attr('value'));
	$sugaCost = __str2num($('#strSugaCost',$objOpener).text());

	
	//계산시작
	$('.iljungID',$objOpener).each(function(){
		$id = $(this).attr('id').toString().substring(6,$(this).attr('id').toString().length);

		if ($('#mUse_'+$id,$objOpener).attr('value')    == 'Y' &&
			$('#mDelete_'+$id,$objOpener).attr('value') == 'N' ){
			$svcCD = $('#mSvcSubCode_'+$id,$objOpener).attr('value');

			if ($('#mBiPayUmu_'+$id,$objOpener).attr('value') != 'Y' ){
				$('#strUseTime'+$svcCD,$objOpener).text( __num2str( __str2num($('#strUseTime'+$svcCD,$objOpener).text()) + __str2num($('#mProcStr_'+$id,$objOpener).attr('value')) ) );
				$('#strUsePay'+$svcCD,$objOpener).text(  __num2str( __str2num($('#strUsePay'+$svcCD,$objOpener).text())  + __str2num($('#mTValue_'+$id,$objOpener).attr('value')) ) );
			}else{
				$('#strBipayTime'+$svcCD,$objOpener).text( __num2str( __str2num($('#strBipayTime'+$svcCD,$objOpener).text()) + __str2num($('#mProcStr_'+$id,$objOpener).attr('value')) ) );
				$('#strBipayPay'+$svcCD,$objOpener).text(  __num2str( __str2num($('#strBipayPay'+$svcCD,$objOpener).text())  + __str2num($('#mTValue_'+$id,$objOpener).attr('value')) ) );
			}
			
			$('#strTotTime'+$svcCD,$objOpener).text( __num2str( __str2num($('#strUseTime'+$svcCD,$objOpener).text()) + __str2num($('#strBipayTime'+$svcCD,$objOpener).text()) ) );
			$('#strTotPay'+$svcCD,$objOpener).text(  __num2str( __str2num($('#strUsePay'+$svcCD,$objOpener).text())  + __str2num($('#strBipayPay'+$svcCD,$objOpener).text()) ) );
		}
	});

	
	//합계
	$('.strConstClass',$objOpener).each(function(){
		if ($(this).attr('id').toString().substring(0, ('strLeftTime').length) == 'strLeftTime'){
		}else if ($(this).attr('id').toString().substring(0, ('strLeftPay').length) == 'strLeftPay'){
		}else{
			$id = '#'+$(this).attr('id').toString().substring( 0, $(this).attr('id').toString().length - 3 )+'Sum';
			$($id,$objOpener).text( __num2str( __str2num( $($id,$objOpener).text() ) + __str2num( $(this).text() ) ) );
		}
	});
	

	//이용시간 및 금액
	$usePay  = __str2num( $('#strUsePaySum',$objOpener).text() );
	$useTime = $usePay / $sugaCost;

	
	//잔여계산
	$cnt  = $('.strConstLeft',$objOpener).length;
	$cost = __str2num( $('#svcCost',$objOpener).attr('value') );
	
	for($i=0; $i<$cnt; $i++){
		$leftPay  = __str2num( $('#strLimitPay'+$i,$objOpener).text() );
		$leftTime = $leftPay / $sugaCost;
		
		if ($leftTime - $useTime > 0){
			$time = $leftTime - $useTime;

			$('#strLeftPay'+$i,$objOpener).text( __num2str(Math.round($time * $sugaCost)) );
			$('#strLeftTime'+$i,$objOpener).text( __round($time, 1 ) );

			break;
		}else{
			$useTime -= Math.floor($leftTime);
			$pay      = Math.floor($leftTime) * $sugaCost;
			
			$('#strLeftPay'+$i,$objOpener).text( __num2str($leftPay - $pay) );
			$('#strLeftTime'+$i,$objOpener).text( __round($leftTime - Math.floor($leftTime), 1) );
		}
	}
	
	$pay  = 0;
	$time = 0;

	$('.strConstLeft',$objOpener).each(function(){
		$id = __getSplitStr( $(this).attr('id'), 'strLeftTime');

		$pay  += __str2num($('#strLeftPay'+$id,$objOpener).text());
		$time += parseFloat($('#strLeftTime'+$id,$objOpener).text());
	});

	$('#strLeftTotPay',$objOpener).text( __num2str($pay) );
	$('#strLeftTotTime',$objOpener).text( __num2str($time) );
}



/**************************************************

	바우처 기타유료 서비스내역 수정

**************************************************/
function _set_month_amount_other_voucher(type, old_svc, new_svc, old_suga, new_suga, old_time, new_time, old_bipay, new_bipay){
	if (type == 'delete' || type == 'insert'){
		var obj_opener = document;
	}else{
		var obj_opener = opener.document;
	}

	var obj_tot_pay = null, obj_over_pay = null, obj_bipay = null, obj_use_time = null, obj_re_time = null, obj_tot_time = null, obj_limit_pay = null;
	var int_tot_pay = 0, int_over_pay = 0, int_bipay = 0, int_use_time = 0, int_re_time = 0, int_tot_time = 0, int_limit_pay = 0;

	if (type == 'modify' || type == 'delete'){
		//사용할 객체

		try{
			obj_tot_pay   = obj_opener.getElementById('tot_pay_'  +old_svc);
			obj_use_time  = obj_opener.getElementById('use_time_' +old_svc);
			obj_over_pay  = obj_opener.getElementById('over_pay_' +old_svc);
			obj_bipay     = obj_opener.getElementById('bipay_'    +old_svc);
			obj_re_time   = obj_opener.getElementById('re_time_'  +old_svc);
			obj_tot_time  = obj_opener.getElementById('tot_time_' +old_svc);
			obj_limit_pay = obj_opener.getElementById('limit_pay_'+old_svc);
		}catch(e){
			//__show_error(e);
		}

		//현재데이타
		try{
			int_tot_pay    = __str2num(obj_tot_pay.innerHTML);  //총액
			int_use_time   = __str2num(obj_use_time.innerHTML); //이용시간
			int_over_pay   = __str2num(obj_over_pay.innerHTML); //초과금액
			int_bipay      = __str2num(obj_bipay.innerHTML);    //비급여액
			int_re_time    = __str2num(obj_re_time.innerHTML);  //잔여시간
			int_tot_time   = __str2num(obj_tot_time.value);     //총시간
			int_limit_pay  = __str2num(obj_limit_pay.value);    //제한금액
		}catch(e){
			//__show_error(e);
		}
		
		if (type != 'insert' && old_bipay != 'Y'){
			int_use_time = parseInt(int_use_time, 10) - parseInt(old_time, 10);     //사용시간
			int_re_time  = parseInt(int_tot_time, 10) - parseInt(int_use_time, 10); //잔여시간
		}

		if (old_bipay == 'Y'){
			// 비급여인 경우
			int_bipay = int_bipay - old_suga;
		}else{
			if (int_over_pay >= old_suga){
				int_over_pay = int_over_pay - old_suga;
			}else{
				if (int_over_pay > 0){
					int_tot_pay = int_tot_pay - int_over_pay;
					
					old_suga = old_suga - int_over_pay;
					int_over_pay = 0;
				}else{
					int_tot_pay = int_tot_pay - old_suga;
				}
			}
		}

		try{
			obj_tot_pay.innerHTML   = __num2str(int_tot_pay);
			obj_use_time.innerHTML  = int_use_time;
			obj_over_pay.innerHTML  = __num2str(int_over_pay);
			obj_bipay.innerHTML     = __num2str(int_bipay);
			obj_re_time.innerHTML   = int_re_time;
		}catch(e){
			//__show_error(e);
		}

		_set_month_amount_other_sum(type, new_svc);

		if (type == 'delete') return;
	}

	//사용할 객체
	try{
		obj_tot_pay   = obj_opener.getElementById('tot_pay_'  +new_svc);
		obj_use_time  = obj_opener.getElementById('use_time_' +new_svc);
		obj_over_pay  = obj_opener.getElementById('over_pay_' +new_svc);
		obj_bipay     = obj_opener.getElementById('bipay_'    +new_svc);
		obj_re_time   = obj_opener.getElementById('re_time_'  +new_svc);
		obj_tot_time  = obj_opener.getElementById('tot_time_' +new_svc);
		obj_limit_pay = obj_opener.getElementById('limit_pay_'+new_svc);
	}catch(e){
		//__show_error(e);
	}

	//현재데이타
	try{
		int_tot_pay    = __str2num(obj_tot_pay.innerHTML);  //총액
		int_use_time   = __str2num(obj_use_time.innerHTML); //이용시간
		int_over_pay   = __str2num(obj_over_pay.innerHTML); //초과금액
		int_bipay      = __str2num(obj_bipay.innerHTML);    //비급여액
		int_re_time    = __str2num(obj_re_time.innerHTML);  //잔여시간
		int_tot_time   = __str2num(obj_tot_time.value);     //총시간
		int_limit_pay  = __str2num(obj_limit_pay.value);    //제한금액
	}catch(e){
		//__show_error(e);
	}
	

	if (new_bipay != 'Y'){
		//사용시간
		int_use_time = parseInt(int_use_time, 10) + parseInt(new_time, 10);

		//잔여시간
		try{
			int_re_time  = parseInt(int_tot_time, 10) - parseInt(int_use_time, 10);
		}catch(e){
			//__show_error(e);
		}
	}

	if (new_bipay == 'Y'){
		//비급여
		int_bipay += parseInt(new_suga, 10);
	}else{
		if (new_suga > int_tot_pay + int_limit_pay){
			//한도초과
			int_tot_pay   = int_limit_pay;
			int_over_pay += (parseInt(new_suga, 10) - int_tot_pay);
		}else{
			int_tot_pay += parseInt(new_suga, 10);
		}
	}

	try{
		obj_tot_pay.innerHTML   = __num2str(int_tot_pay);
		obj_use_time.innerHTML  = int_use_time;
		obj_over_pay.innerHTML  = __num2str(int_over_pay);
		obj_bipay.innerHTML     = __num2str(int_bipay);
		obj_re_time.innerHTML   = int_re_time;
	}catch(e){
		//__show_error(e);
	}

	_set_month_amount_other_sum(type, new_svc);
}



/**************************************************

	기타유료 서비스내역 합계

**************************************************/
function _set_month_amount_other_sum(type, svc_id){
	if (type == 'delete' || type == 'insert'){
		var obj_opener = document;
	}else{
		var obj_opener = opener.document;
	}

	var from_id = svc_id - (svc_id % 10) + 1;
	var to_id   = from_id + 10 - 1;

	var obj_tot_pay = null, obj_over_pay = null, obj_bipay = null, obj_use_time = null, obj_re_time = null, obj_tot_time = null, obj_limit_pay = null;
	var int_tot_pay = 0, int_over_pay = 0, int_bipay = 0, int_use_time = 0, int_re_time = 0, int_tot_time = 0, int_limit_pay = 0;
	
	var id = from_id;

	if (svc_id > 20 && svc_id < 30){
		obj_tot_pay   = obj_opener.getElementById('tot_pay_'+svc_id);
		obj_use_time  = obj_opener.getElementById('use_time_'+svc_id);
		obj_over_pay  = obj_opener.getElementById('over_pay_'+svc_id);
		obj_bipay     = obj_opener.getElementById('bipay_'+svc_id);
		obj_re_time   = obj_opener.getElementById('re_time_'+svc_id);
		obj_tot_time  = obj_opener.getElementById('tot_time_'+svc_id);
		obj_limit_pay = obj_opener.getElementById('limit_pay_'+svc_id);

		int_tot_pay   = __str2num(obj_tot_pay.innerHTML);  //총액
		int_use_time  = __str2num(obj_use_time.innerHTML); //이용시간
		int_over_pay  = __str2num(obj_over_pay.innerHTML); //초과금액
		int_bipay     = __str2num(obj_bipay.innerHTML);    //비급여액
		int_re_time   = __str2num(obj_re_time.innerHTML);  //잔여시간
	}else{
		while(true){
			if (id > to_id) break;

			//사용할 객체
			try{
				obj_tot_pay   = obj_opener.getElementById('tot_pay_'+id);
				obj_use_time  = obj_opener.getElementById('use_time_'+id);
				obj_tot_time  = obj_opener.getElementById('tot_time_'+id);
				obj_limit_pay = obj_opener.getElementById('limit_pay_'+id);

				int_tot_pay   += __str2num(obj_tot_pay.innerHTML);  //총액
				int_use_time  += __str2num(obj_use_time.innerHTML); //이용시간
				
				id ++;
			}catch(e){
				break;
			}
		}
	}

	if (svc_id > 20 && svc_id < 30){
		obj_tot_pay   = obj_opener.getElementById('tot_pay_tot');
		obj_over_pay  = obj_opener.getElementById('over_pay_tot');
		obj_bipay     = obj_opener.getElementById('bipay_tot');
		obj_use_time  = obj_opener.getElementById('use_time_tot');
		obj_re_time   = obj_opener.getElementById('re_time_tot');

		obj_tot_pay.innerHTML   = __num2str(int_tot_pay);
		obj_over_pay.innerHTML  = __num2str(int_over_pay);
		obj_bipay.innerHTML     = __num2str(int_bipay);
		obj_use_time.innerHTML  = int_use_time;
		obj_re_time.innerHTML   = int_re_time;
	}else{
		obj_tot_pay   = obj_opener.getElementById('tot_pay_tot');
		obj_use_time  = obj_opener.getElementById('use_time_tot');
		
		obj_tot_pay.innerHTML   = __num2str(int_tot_pay);
		obj_use_time.innerHTML  = int_use_time;
	}
}

function _get_url_data(url, params, exec){
	var URL = url;
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				switch(exec){
					case 'r':
						return responseHttpObj.responseText;
						break;
					default:
						eval(exec+'(\''+svc+'\',\''+responseHttpObj.responseText+'\')');
				}
			}
		}
	);
}

////////////////////////////////////////////////////////////////////////////////
//
// 바우처 생성내역 등록
//
////////////////////////////////////////////////////////////////////////////////
function _voucher_make(code, svc_cd, ssn, key, year, month, seq){
	var sh     = screen.height - 50;
	var width  = 900;
	var height = 400;
	var left   = (window.screen.width  - width)  / 2;
	var top    = 150;
	var url    = 'iljung_voucher_make.php';

	var my_popup = window.open(url+'?code='+code+'&svc_cd='+svc_cd+'&ssn='+ssn+'&key='+key+'&year='+year+'&month='+month+'&seq='+seq, 'MAKE_VOUCHER', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

////////////////////////////////////////////////////////////////////////////////
//
// 바우처 이월시간설정
//
////////////////////////////////////////////////////////////////////////////////
function _voucher_overtime(code, svc_cd, ssn, key, year, month, seq){
	var sh     = screen.height - 50;
	var width  = 900;
	var height = 400;
	var left   = (window.screen.width  - width)  / 2;
	var top    = (window.screen.height - height) / 2;
	var url    = 'iljung_voucher_overtime.php';

	var my_popup = window.open(url+'?code='+code+'&svc_cd='+svc_cd+'&ssn='+ssn+'&key='+key+'&year='+year+'&month='+month+'&seq='+seq, 'MAKE_VOUCHER', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

////////////////////////////////////////////////////////////////////////////////
//
// 바우처 생성내역 저장
//
////////////////////////////////////////////////////////////////////////////////
function _voucher_run(action){
	var f = document.f;

	f.action = action;
	f.submit();
}

////////////////////////////////////////////////////////////////////////////////
//
// 바우처 구매시간
// 
// - svc_id : 바우처 ID
// - make_time : 생성시간
//
////////////////////////////////////////////////////////////////////////////////
function _voucher_time(svc_id, make_time){
	var overTime  = document.getElementById('overTime');  //이월시간
	var makeTime  = document.getElementById('makeTime');  //생성시간
	var totalTime = document.getElementById('totalTime'); //총구매시간

	var strOver  = document.getElementById('strOverTime');  //
	var strMake  = document.getElementById('strMakeTime');  //
	var strTotal = document.getElementById('strTotalTime'); //

	makeTime.value  = make_time;
	totalTime.value = parseFloat(overTime.value, 10) + parseFloat(makeTime.value, 10);

	strOver.innerHTML  = __num2str(overTime.value);
	strMake.innerHTML  = __num2str(makeTime.value);
	strTotal.innerHTML = totalTime.value;
}


/*********************************************************

	바우처 생성(바우처 구매시간)	

*********************************************************/
function _voucher_time_dis(svc_id){
	var suga_cost = __str2num(document.getElementById('suga_cost').value);

	var pay_stnd_tot  = document.getElementById('pay_stnd_tot');
	var pay_stnd_time = document.getElementById('pay_stnd_time');
	var pay_stnd_use  = document.getElementById('pay_stnd_use');
	var pay_stnd_self = document.getElementById('pay_stnd_self');
	
	var pay_add_tot  = document.getElementById('pay_add_tot');
	var pay_add_time = document.getElementById('pay_add_time');
	var pay_add_use  = document.getElementById('pay_add_use');
	var pay_add_self = document.getElementById('pay_add_self');
	
	var pay_sido_tot  = document.getElementById('pay_sido_tot');
	var pay_sido_time = document.getElementById('pay_sido_time');
	var pay_sido_use  = document.getElementById('pay_sido_use');
	var pay_sido_self = document.getElementById('pay_sido_self');
	
	var pay_jach_tot  = document.getElementById('pay_jach_tot');
	var pay_jach_time = document.getElementById('pay_jach_time');
	var pay_jach_use  = document.getElementById('pay_jach_use');
	var pay_jach_self = document.getElementById('pay_jach_self');
	
	var pay_over_tot  = document.getElementById('pay_over_tot');
	var pay_over_time = document.getElementById('pay_over_time');
	var pay_over_use  = document.getElementById('pay_over_use');
	var pay_over_self = document.getElementById('pay_over_self');

	var pay_total_tot  = document.getElementById('pay_total_tot');
	var pay_total_time = document.getElementById('pay_total_time');
	var pay_total_use  = document.getElementById('pay_total_use');
	var pay_total_self = document.getElementById('pay_total_self');

	pay_over_tot.value  = __num2str(pay_over_use.value);
	pay_over_time.value = __round(__str2num(pay_over_use.value) / suga_cost, 1);
	
	pay_sido_use.value = __num2str(__str2num(pay_sido_time.value) * suga_cost);
	pay_sido_tot.value = pay_sido_use.value;
	pay_jach_use.value = __num2str(__str2num(pay_jach_time.value) * suga_cost);
	pay_jach_tot.value = pay_jach_use.value;

	pay_total_tot.value  = __str2num(pay_stnd_tot.value)
						 + __str2num(pay_add_tot.value)
						 + __str2num(pay_sido_tot.value)
						 + __str2num(pay_jach_tot.value)
						 + __str2num(pay_over_tot.value);

	pay_total_time.value = parseFloat(pay_stnd_time.value, 10)
						 + parseFloat(pay_add_time.value, 10)
						 + parseFloat(pay_sido_time.value, 10)
						 + parseFloat(pay_jach_time.value, 10)
						 + parseFloat(pay_over_time.value, 10);

	pay_total_use.value  = __str2num(pay_stnd_use.value)
						 + __str2num(pay_add_use.value)
						 + __str2num(pay_sido_use.value)
						 + __str2num(pay_jach_use.value)
						 + __str2num(pay_over_use.value);

	pay_total_self.value = __str2num(pay_stnd_self.value)
						 + __str2num(pay_add_self.value)
						 + __str2num(pay_sido_self.value)
						 + __str2num(pay_jach_self.value)
						 + __str2num(pay_over_self.value);


	pay_total_tot.value  = __num2str(pay_total_tot.value);
	//pay_total_time.value = __num2str(pay_total_time.value);
	pay_total_use.value  = __num2str(pay_total_use.value);
	pay_total_self.value = __num2str(pay_total_self.value);
	
	var makeTime = __round(__str2num(pay_total_time.value) - __str2num(pay_over_time.value), 1);

	_voucher_time(svc_id, makeTime);
}

//바우처(노인돌봄)서비스구분선택
function _sel_svc_gbn(gbn, svc_id, make_time){
	var gbnD = document.getElementById('gbnD');
	var gbnV = document.getElementById('gbnV');

	if (gbn == 'D'){
		gbnD.style.display = '';
		gbnV.style.display = 'none';
	}else{
		gbnD.style.display = 'none';
		gbnV.style.display = '';
	}

	_voucher_time(svc_id, make_time);
}

//로딩
function _show_loading(){
	var w = document.body.offsetWidth;
	var h = document.body.offsetHeight;
	var html = '<div id="loding_body" style="z-index:1000; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#cccccc; position:absolute; left:0; top:0; height:'+h+'; width:'+w+';"></div>'
			 + '<div id="loding_cont" style="z-index:1001; position:absolute; text-align:center; padding-top:30px;">'
			 + __loading()
			 + '</div>';

	return html;
}

/**************************************************

	비급여 실비처리 구분

**************************************************/
function _set_bipay_pay(mode){
	if (document.getElementsByName('svc_id[]')[0].type != 'hidden'){
		var svc_id = __object_get_value('svc_id[]');
	}else{
		var svc_id = document.getElementsByName('svc_id[]')[0].value;
	}
	
	if (svc_id > 30) return;

	var bipay_yn = document.getElementById('bipayUmu').checked ? 'Y' : 'N';

	if (bipay_yn == 'Y'){
		$('#divExpenseBody').show();
	}else{
		$('#divExpenseBody').hide();
	}
	
	var bipay_cost1 = document.getElementById('bipay_cost1');
	var bipay_cost2 = document.getElementById('bipay_cost2');
	var bipay_cost3 = document.getElementById('bipay_cost3');
	var bipay_cost  = null;
	
	var exp_yn  = document.getElementsByName('exp_yn');
	var exp_pay = document.getElementById('exp_pay');
	var max_pay = document.getElementById('exp_max_pay');
	
	__object_enabled(bipay_cost1, false);

	if (bipay_cost2 != null) __object_enabled(bipay_cost2, false);
	if (bipay_cost3 != null) __object_enabled(bipay_cost3, false);
	
	__object_enabled(exp_yn[0], false);
	__object_enabled(exp_yn[1], false);
	__object_enabled(exp_pay, false);

	var svc_sub_cd  = __object_get_value('svcSubCode');
	
	if (bipay_yn != 'Y'){
		if (svc_sub_cd != '200'){
			try{
				document.getElementById('visitSudangCheck').checked = true;
				__object_enabled(document.getElementById('visitSudangCheck'), true);
				checkVisitSugang(true);
			}catch(e){
			}
		}
	}else{
		try{
			document.getElementById('visitSudangCheck').checked = false;
			__object_enabled(document.getElementById('visitSudangCheck'), false);
			checkVisitSugang(false);
		}catch(e){
		}
	}

	if ((svc_id > 10 && svc_id < 20) ||
		(svc_id == 24)){
		switch(svc_sub_cd){
			case '200': 
				__object_enabled(bipay_cost1, true); 
				max_pay.value = __str2num(bipay_cost1.value);
				bipay_cost    = bipay_cost1;
				break;
			case '500': 
				__object_enabled(bipay_cost2, true); 
				max_pay.value = __str2num(bipay_cost2.value);
				bipay_cost    = bipay_cost2;
				break;
			case '800': 
				__object_enabled(bipay_cost3, true); 
				max_pay.value = __str2num(bipay_cost3.value);
				bipay_cost    = bipay_cost3;
				break;
		}
	}else if (svc_id > 20 && svc_id < 30){
		__object_enabled(bipay_cost1, true); 
		max_pay.value = __str2num(bipay_cost1.value);
		bipay_cost    = bipay_cost1;
	}else{
		return;
	}
	
	if (mode == 'ADD'){
	}else{
		exp_pay.value = __num2str(max_pay.value);
	}
	
	__object_enabled(exp_yn[0], true);
	__object_enabled(exp_yn[1], true);
	__object_enabled(exp_pay, true);

	_set_expense_yn();
	_chk_expense_max(bipay_cost, mode);
}

function _set_expense_yn(){
	var exp_yn  = __object_get_value('exp_yn');
	var exp_pay = document.getElementById('exp_pay');
	
	__object_enabled(exp_pay, (exp_yn == 'Y' ? true : false));
}

function _chk_expense_pay(){
	var exp_pay = document.getElementById('exp_pay');
	var max_pay = document.getElementById('exp_max_pay');
	
	if (__str2num(exp_pay.value) > __str2num(max_pay.value)){
		alert('실비지급금액의 최대금액은 '+__num2str(max_pay.value)+'원 입니다.');
		exp_pay.value = max_pay.value;
		exp_pay.focus();
	}
}

function _chk_expense_max(obj, mode){
	if (document.getElementsByName('svc_id[]')[0].type != 'hidden'){
		var svc_id = __object_get_value('svc_id[]');
	}else{
		var svc_id = document.getElementsByName('svc_id[]')[0].value;
	}
	
	var max_pay = document.getElementById('exp_max_pay');
	var exp_pay = document.getElementById('exp_pay');
	
	max_pay.value = __num2str(__str2num(obj.value));
	
	if (svc_id == '11'){
		var suga_cost = document.getElementById('sPrice');
	}else{
		var suga_cost = document.getElementById('sugaCost');
	}
	
	if (suga_cost != null) suga_cost.value = max_pay.value;
	
	if (__str2num(exp_pay.value) > __str2num(max_pay.value)){
		exp_pay.value = __num2str(max_pay.value);
	}

	try{
		if (svc_id == '11'){
			_setIljungSuga(mode);
		}else{
			_get_iljung_suga();
		}
	}catch(e){
	}
}

/*
 * 합계
 */
function _set_object_tot(obj){
	var obj1 = document.getElementById(obj+'_cnt');
	var obj2 = document.getElementById(obj+'_pay');
	var obj3 = document.getElementById(obj+'_tot');
	
	obj3.value = __num2str(__str2num(obj1.value) * __str2num(obj2.value));
	
	_set_addpay_tot();
}

function _set_addpay_tot(){
	var addpay     = document.getElementById('addpay_tot');
	var school_not = __str2num(document.getElementById('school_not_tot').value);
	var school     = __str2num(document.getElementById('school_tot').value);
	var family     = __str2num(document.getElementById('family_tot').value);
	var home_in    = __str2num(document.getElementById('home_in_pay').value);
	var holiday    = __str2num(document.getElementById('holiday_pay').value);
	
	addpay.innerHTML = __num2str(school_not + school + family + home_in + holiday);
}

function _is_make_voucher(){
	var voucher_msg = document.getElementById('voucher_msg');
	var voucher_yn  = document.getElementById('voucher_make_yn').value;
	
	if (voucher_yn != 'Y'){
		voucher_msg.style.display = '';
		
		if (confirm('생성된 바우처 내역이 없습니다.\n\n배우처 내역을 생성하시려면 "예"를\n\n비급여로 등록하시려면 "취소"를 클릭하여 주십시오.')){
			self.close();
			opener.location.replace('./iljung_list.php?mode=3');
		}
	}else{
		voucher_msg.style.display = 'none';
	}
}



/**************************************************

	비급여 구분선택

**************************************************/
function _current_bipay(){
	var mode       = document.getElementById('mode').value;
	var svc_id     = __object_get_value('svc_id[]', mode != 'IN' ? opener : '');
	var bipay_kind = __object_get_value('bipay_kind');
	
	if ((svc_id > 10 && svc_id < 20) ||
		(svc_id == 24)){
		switch(bipay_kind){
			case '1':
				var suga_cost = __str2num(document.getElementById('bipay_cost_publid').value);
				break;
			case '2':
				var suga_cost = __str2num(document.getElementById('bipay_cost_private').value);
				break;
			default:
				var suga_cost = __str2num(document.getElementById('exp_max_pay').value);
		}
		
		if (svc_id > 10 && svc_id < 20){
			document.getElementById('sPrice').value = __num2str(suga_cost);
			document.getElementById('tPrice').value = __num2str(suga_cost);
		}else{
			document.getElementById('sugaCost').value = __num2str(suga_cost);
			_iljung_check_time(document.getElementById('ftHour'));
		}
	}else if (svc_id > 20 && svc_id < 30){
		switch(bipay_kind){
			case '1':
				var suga_cost = __str2num(document.getElementById('bipay_cost_publid').value);
				break;
			case '2':
				var suga_cost = __str2num(document.getElementById('bipay_cost_private').value);
				break;
			default:
				var suga_cost = __str2num(document.getElementById('exp_max_pay').value);
		}
		
		document.getElementById('sugaCost').value = __num2str(suga_cost);
		document.getElementById('sugaTot').value = __num2str(__str2num(document.getElementById('sugaTime').value) * suga_cost);
	}else{
	}
}



/**************************************************

	제공자 삭제

**************************************************/
function _unset_mem(index){
	document.getElementById('yoy'+index).value   = '';
	document.getElementById('yoyNm'+index).value = '';
	document.getElementById('yoyTA'+index).value = '';
	
	_get_svc_suga();
}



/**************************************************

	수가 다시 계산

**************************************************/
function _get_svc_suga(){
	var mode       = document.getElementById('mode').value;
	var svc_id     = __object_get_value('svc_id[]', mode != 'IN' ? opener : '');
	var svc_sub_cd = __object_get_value('svcSubCode');
	
	if (svc_id == 24){
		if (svc_sub_cd == '200' ||
			svc_sub_cd == '500'){
			_get_iljung_suga();
		}
	}
}



/**************************************************

	장애할동지원 서비스 선택

**************************************************/
function _set_voucher_svc(){
	var f = document.f;
	var svc_id   = __object_get_value('svc_id[]');
	var svc_kind = __object_get_value('svcSubCode');
	var mode     = document.getElementById('mode').value;
	
	if (svc_id == '')
		svc_id = document.getElementsByName('svc_id[]')[0].value;
	
	if (svc_id > 20 && svc_id < 30){
	}else{
		return;
	}

	if (mode == 'MODIFY'){
		var svcs = document.getElementsByName('svcSubCode');
		
		for(var i=0; i<svcs.length; i++){
			svcs[i].disabled = (svcs[i].value == svc_kind ? false : true);
		}
	}

	if (svc_kind == '200'){
		/**************************************************
		
			활동지원
		
		**************************************************/
		document.getElementById('mem_if2').style.display = '';
		document.getElementById('frame_suga_night').style.display  = '';
		document.getElementById('frame_suga_tot').style.paddingTop = '15px';
		
		f.yoyNm2.value = f.yoyNm2.tag;
		f.yoy2.value   = f.yoy2.tag
		f.yoyTA2.value = f.yoyTA2.tag;
		
		f.svcSubCD.disabled = true;
		f.carNo.disabled    = true;

		f.visitSudangCheck.checked  = false;
		f.visitSudangCheck.disabled = true;
		f.visitSudang.disabled      = true;
		f.visitSudang.style.backgroundColor = '#eeeeee';
		
		f.sudangYul1.disabled = true;
		f.sudangYul1.style.backgroundColor = '#eeeeee';
		
		f.sudangYul2.disabled = true;
		f.sudangYul2.style.backgroundColor = '#eeeeee';
	
	}else if (svc_kind == '500'){
		/**************************************************
			
			방문목욕
		
		**************************************************/
		document.getElementById('mem_if2').style.display           = '';
		document.getElementById('frame_suga_night').style.display  = 'none';
		document.getElementById('frame_suga_tot').style.paddingTop = '3px';
		
		f.yoyNm2.value = f.yoyNm2.tag;
		f.yoy2.value   = f.yoy2.tag
		f.yoyTA2.value = f.yoyTA2.tag;

		f.visitSudangCheck.checked  = true;
		f.visitSudangCheck.disabled = false;
		f.visitSudang.disabled      = false;
		f.visitSudang.style.backgroundColor = '#ffffff';
		f.sudangYul1.disabled = false;
		f.sudangYul1.style.backgroundColor = '#ffffff';
		
		f.sudangYul2.disabled = false;
		f.sudangYul2.style.backgroundColor = '#ffffff';
		
		f.svcSubCD.disabled = false;
		f.carNo.disabled    = false;
	}else if (svc_kind == '800'){
		/**************************************************
		
			방문간호
		
		**************************************************/
		document.getElementById('mem_if2').style.display           = 'none';
		document.getElementById('frame_suga_night').style.display  = 'none';
		document.getElementById('frame_suga_tot').style.paddingTop = '3px';
		
		f.yoyNm2.value = '';
		f.yoy2.value   = '';
		f.yoyTA2.value = '';

		f.svcSubCD.disabled = true;
		f.carNo.disabled    = true;
		
		f.visitSudangCheck.checked  = true;
		f.visitSudangCheck.disabled = false;
		f.visitSudang.disabled      = false;
		f.visitSudang.style.backgroundColor = '#ffffff';
		
		f.sudangYul1.disabled = true;
		f.sudangYul1.style.backgroundColor = '#eeeeee';
		
		f.sudangYul2.disabled = true;
		f.sudangYul2.style.backgroundColor = '#eeeeee';
	}

	if (svc_kind != '')	_iljung_check_time(f.ftHour);

	_set_bipay_pay();
}



/**************************************************

	입력된 제공자 수

**************************************************/
function _set_mem_cnt(){
	var mem_cnt = 0;
	
	if (document.getElementById('yoy1').value != '') mem_cnt ++;
	if (document.getElementById('yoy2').value != '') mem_cnt ++;
	
	return mem_cnt;
}


/*********************************************************

	해당서비스의 버튼들만 활성화 시킨다.

*********************************************************/
function _set_current_svc_enabled(){
	var svcID = $(':radio[name="svc_id[]"]:checked').attr('value');

	$('.svcSubjectBtn11').css('display','none');
	$('.svcSubjectBtn21').css('display','none');
	$('.svcSubjectBtn22').css('display','none');
	$('.svcSubjectBtn23').css('display','none');
	$('.svcSubjectBtn24').css('display','none');
	$('.svcSubjectBtn31').css('display','none');
	$('.svcSubjectBtn32').css('display','none');
	$('.svcSubjectBtn33').css('display','none');
	$('.svcSubjectBtn'+svcID).css('display','');

	/*
	$('.svcSubject').each(function(){
		var tag = $(this).attr('tag');
		if (tag != svc){
			$('.svcSubjectBtn'+tag).css('display','none');
		}else{
			$('.svcSubjectBtn'+tag).css('display','');
		}
	});
	*/
}



/*********************************************************

	산모신생아의 경우 추가요금계를 구한다.

*********************************************************/
function _set_addpay_summly(){
	var svcID = $(':radio[name="svc_id[]"]:checked').attr('value');

	if (svcID == '23' || svcID == '31'){
	}else{
		return;
	}

	var newAdd = false;
	var addpay = new Array();
	var addAmt = new Array();

	for(var i=0; i<=7; i++) addAmt[i] = 0;

	$('.svcSubject'+svcID).each(function(){
		obj = $(this).attr('id').toString().split('txtSubject').join('');
		
		if ($('#mUse'+obj).attr('value')       == 'Y' && 
			$('#mDelete'+obj).attr('value')    == 'N' && 
			$('#mDuplicate'+obj).attr('value') == 'N'){
			
			val = $('#mAddPay'+obj).attr('value');
			val = val.toString().split('&');

			for(var i=0; i<=7; i++) addpay[i] = 0;
			for(var i=0; i<val.length; i++){
				str = val[i].toString().split('=');
				
				switch(str[0]){
					case 'school_not_cnt':  addpay[0] += parseInt(str[1], 10); break;
					case 'school_not_cost': addpay[1] += parseInt(str[1], 10); break;
					case 'school_cnt':      addpay[2] += parseInt(str[1], 10); break;
					case 'school_cost':     addpay[3] += parseInt(str[1], 10); break;
					case 'family_cnt':      addpay[4] += parseInt(str[1], 10); break;
					case 'family_cost':     addpay[5] += parseInt(str[1], 10); break;
					case 'home_in_cost':    addpay[6] += parseInt(str[1], 10); break;
					case 'holiday_cost':
						if ($('#mWeekDay'+obj).attr('value') == '0' ||
							$('#mWeekDay'+obj).attr('value') == '7' ||
							$('#mHoliday'+obj).attr('value') == 'Y'){
							addpay[7] += parseInt(str[1], 10);
						}
						break;
				}
			}

			addAmt[0] += addpay[0];
			addAmt[1] += (addpay[0] * addpay[1]);
			addAmt[2] += addpay[2];
			addAmt[3] += (addpay[2] * addpay[3]);
			addAmt[4] += addpay[4];
			addAmt[5] += (addpay[4] * addpay[5]);
			addAmt[6] += addpay[6];
			addAmt[7] += addpay[7];
		}
	});

	$('#addpayNotChildCnt').text(__num2str(addAmt[0]));
	$('#addpayNotChildAmt').text(__num2str(addAmt[1]));
	$('#addpayChildCnt').text(__num2str(addAmt[2]));
	$('#addpayChildAmt').text(__num2str(addAmt[3]));
	$('#addpayFamilyCnt').text(__num2str(addAmt[4]));
	$('#addpayFamilyAmt').text(__num2str(addAmt[5]));
	$('#addpayHouseAmt').text(__num2str(addAmt[6]));
	$('#addpayHolidayAmt').text(__num2str(addAmt[7]));
	$('#addpayTotalAmt').text(__num2str(addAmt[1]+addAmt[3]+addAmt[5]+addAmt[6]+addAmt[7]));
}



/*********************************************************

	바우처 생성시 이용 정보 출력

*********************************************************/
function _makeVoucherUseIfno(){
	$svcID = $('#svcID').attr('value');
	$svcCD = $('#svcCD').attr('value');
	$cLvl  = $('#svc_kind').attr('value');

	$addPay      = 0;
	$addTime     = 0;
	$addExpenses = 0;

	if ($svcID != '24') return;

	$incomeLvlInfo = eval( 'infoIncomeLvl_'+$svcCD+'_'+$cLvl );
	
	try{
		$addSvcInfo = eval( 'infoAddSvc_'+$svcCD+'_'+$(':radio[name="gbn2"]:checked').attr('value') );

		$addPay  += parseInt($addSvcInfo['pay'], 10);
		$addTime += parseInt($addSvcInfo['time'], 10);

		if ($incomeLvlInfo['pay'] > 0){
			$addExpenses += parseInt($incomeLvlInfo['pay'], 10);
		}else{
			$addExpenses += parseInt($addSvcInfo['pay'], 10) * parseFloat($incomeLvlInfo['rate'], 10);
		}
	}catch(e){
	}
	
	$(':input[name="addPayGbn[]"]:checked').each(function(){
		$addSvcInfo = eval( 'infoAddSvc_'+$svcCD+'_'+$(this).attr('value') );

		$addPay  += parseInt($addSvcInfo['pay'], 10);
		$addTime += parseInt($addSvcInfo['time'], 10);

		if ($incomeLvlInfo['pay'] > 0){
			$addExpenses += parseInt($incomeLvlInfo['pay'], 10);
		}else{
			$addExpenses += cut(parseInt($addSvcInfo['pay'], 10) * parseFloat($incomeLvlInfo['rate'], 10), 100);
		}
	});

	//alert( $addPay + '/' + $addTime + '/' + $addExpenses );

	$addSptPay = $addPay - $addExpenses;


	$('#payAddTot').attr('value', __num2str($addPay));
	$('#payAddTime').attr('value', __num2str($addTime));
	$('#payAddUse').attr('value', __num2str($addSptPay));
	$('#payAddSelf').attr('value', __num2str($addExpenses));

	_voucher_time_dis($svcID);
}


/*********************************************************

	바우처 수가

*********************************************************/
function _iljungGetVoucherSuga(isHoliday){
	var f         = document.f;
	var suga_obj  = document.getElementById('sugaCont');
	var svc_id    = _get_current_svc('id');           //선택된 서비스코드
	var svc_cd    = _get_current_svc();               //선택된 서비스코드
	var svc_kind  = __object_get_value('svcSubCode'); //제공서비스
	var suga_cd   = null; //수가코드
	var bipay_yn  = document.getElementById('bipayUmu').checked ? 'Y' : 'N';
	var c_cd      = document.getElementById('jumin').value;

	if (f.mode.value == 'IN'){
		var date = document.f.calYear.value+document.f.calMonth.value;
	}else{
		var date = document.f.addDate.value;
	}
	
	if (svc_id > 20 && svc_id < 30){
		/**************************************************
	
			바우처 수가
		
		**************************************************/
		if (svc_kind == '500'){
			/**************************************************
			
				방문목욕
			
			**************************************************/
			var suga_cd  = document.getElementById('svcSuga').value.substring(0,2); //수가코드
				suga_cd += 'B';
				suga_cd += f.svcSubCD.value;
				suga_cd += '0';
				
		}else if (svc_kind == '800'){
			/**************************************************
			
				방문간호
			
			**************************************************/
			var suga_cd  = document.getElementById('svcSuga').value.substring(0,2); //수가코드
				suga_cd += 'N';
				
				var from_time = _getTimeValue(f.ftHour.value, f.ftMin.value);
				var to_time   = _getTimeValue(f.ttHour.value, f.ttMin.value);
				var time_gab  = to_time - from_time;
				
				if (time_gab < 30){
					suga_cd += '1';
				}else if (time_gab > 30 && time_gab < 60){
					suga_cd += '2';
				}else{
					suga_cd += '3';
				}
				
				suga_cd += '0';
			
		}else{
			var suga_cd = document.getElementById('svcSuga').value; //수가코드
		}
	}else{
		/**************************************************
	
			기타유료 수가
		
		**************************************************/
		var suga_cd = 'VZ' + __object_set_format(svc_id, 'number', 3, '0'); //수가코드
	}
	
	var mode    = f.mode.value;
	var suga_tm = getHttpRequest('iljung_value.php?type=suga_info&code='+f.code.value+'&svc_kind='+svc_cd+'&suga_cd='+suga_cd+'&c_cd='+c_cd+'&bipay_yn='+bipay_yn+'&date='+date);
	var suga_if = suga_tm.split(__COL_CUTER__);
	var suga_nm = suga_if[0]; //수가명
	
	if (mode != 'MODIFY' && bipay_yn != 'Y'){
		//var suga_stnd  = suga_if[!holiday ? 2 : 4]; //수가 기본단가
		var suga_stnd  = suga_if[2]; //수가 기본단가
	}else{
		if (bipay_yn != 'Y'){
			if (svc_id == 24){
				var suga_stnd  = suga_if[2];	
			}else{
				var suga_stnd = suga_if[!isHoliday ? 2 : 4]; //수가 기본단가
			}
		}else{
			var suga_stnd = __str2num(f.sugaCost.value); //수가 기본단가
		}
	}

	
	/**************************************************
	
		연장단가
	
	**************************************************/
	var suga_night = suga_if[3]; //수가 연장단가
	
	if (suga_night == 0) suga_night = suga_stnd;
	/*************************************************/
	
	
	//연장수가 카운트
	var suga_cnt_night = 0;
	var extrapay       = 0;
	
	if (bipay_yn != 'Y'){
		if (svc_kind == '500' ||
			svc_kind == '800'){
			/**************************************************
			
				방문목욕, 방문간호는 회당 수가로 처리한다.
			
			**************************************************/
			var suga_cnt  = 1;         //단가를 맞출 횟수
			var suga_cost = suga_stnd; //수가 단가
			var extrapay  = parseInt(getHttpRequest('../inc/_check.php?gubun=getSudangPrice&mCode='+f.code.value+'&mSuga='+suga_cd)); //수당
			
		}else{
			if (svc_id == 24){
				/**************************************************
				
					장애활동지원
				
				**************************************************/
				var from_time = _getTimeValue(f.ftHour.value, f.ftMin.value); //시작시간
				var to_time   = _getTimeValue(f.ttHour.value, f.ttMin.value); //종료시간
				var proctimes = to_time - from_time; //진행시간
				
				var hour_stnd    = 0; //기준시간
				var hour_prolong = 0; //연장시간
				
				if (proctimes < 0) proctimes += (24 * _HOUR_);

				if (!isHoliday){
					var time_list = {0:[6 * _HOUR_, 20 * _HOUR_], 
									 1:[20 * _HOUR_, 6 * _HOUR_ + 24 * _HOUR_],
									 2:[0, 6 * _HOUR_]};
				}else{
					var time_list = {0:[0 * _HOUR_, 24 * _HOUR_],
									 1:[0 * _HOUR_, 24 * _HOUR_],
									 2:[0 * _HOUR_, 24 * _HOUR_]};
				}
								 
				if ((from_time >= time_list[1][0] && from_time < time_list[1][1]) ||
					(from_time >= time_list[2][0] && from_time < time_list[2][1])){
					/**************************************************
					
						시작이 22시 이후부터 연장수가를 적용한다.
					
					**************************************************/
					//alert(from_time + '/' + time_list[2][0] + '/' + time_list[2][1] + '/' + proctimes);
					if (from_time >= time_list[1][0] && from_time < time_list[1][1]){
						var index = 1;
					}else{
						var index = 2;
					}
					
					if (from_time + proctimes > time_list[index][1]){
						hour_prolong = time_list[index][1] - from_time;
					}else{
						hour_prolong = proctimes;
					}
					
				}else if ((to_time >= time_list[1][0] && to_time < time_list[1][1]) ||
						  (to_time >= time_list[2][0] && to_time < time_list[2][1])){
					/**************************************************
					
						종료가 22시를 넘어가면 연장수가를 적용한다.
					
					**************************************************/
					//alert('to_time : '+to_time+'/'+time_list[1][0]+'/'+time_list[1][1]+'-'+proctimes);
					if (to_time >= time_list[1][0]){
						var tmp_time = to_time;
					}else{
						var tmp_time = to_time + 24 * _HOUR_;
					}
					
					hour_prolong = tmp_time - time_list[1][0];
					
				}else{
					hour_stnd = proctimes;
				}
				
				/**************************************************
							
					연장 최대시간은 4시간으로 제한한다.
				
				**************************************************/
				if (hour_prolong > 4 * _HOUR_) hour_prolong = 4 * _HOUR_;
				
				hour_stnd = proctimes - hour_prolong;

				suga_cnt       = Math.round(hour_stnd / _HOUR_);    //기준시간
				suga_cnt_night = Math.round(hour_prolong / _HOUR_); //연장시간
				
				var mem_cnt = _set_mem_cnt(); //입력된 제공자수
				
				if (mem_cnt > 1){
					if (mode != 'MODIFY'){
						var suga_cost = suga_stnd * 1.5; //수가 단가
					}else{
						var suga_cost = suga_stnd;
					}
				}else{
					var suga_cost = suga_stnd; //수가 단가
				}
			}else{
				var suga_cnt  = f.svcCost.value / suga_stnd; //단가를 맞출 횟수
				var suga_cost = suga_stnd * suga_cnt;        //수가 단가
			}
		}
	}else{
		try{
			var bipay_kind = __object_get_value('bipay_kind');
			var suga_if    = getHttpRequest('iljung_value.php?type=suga_care&code='+f.code.value+'&suga_cd='+suga_cd).split('//');
			
			document.getElementById('bipay_cost_publid').value  = __num2str(suga_if[1]);
			document.getElementById('bipay_cost_private').value = __num2str(suga_if[2]);
		}catch(e){
			var bipay_kind = '3';
		}
		
		switch(bipay_kind){
			case '1':
				suga_stnd = __str2num(document.getElementById('bipay_cost_publid').value);
				break;
			case '2':
				suga_stnd = __str2num(document.getElementById('bipay_cost_private').value);
				break;
			default:
				suga_stnd = __str2num(document.getElementById('exp_max_pay').value);
		}
		
		if (svc_kind == '500' ||
			svc_kind == '800'){
			var suga_cnt = 1; //단가를 맞출 횟수
			var suga_cost = suga_stnd * suga_cnt; //수가 단가
		}else{
			var suga_cnt = (!isNaN(parseInt(f.procTime.value, 10)) ? parseInt(f.procTime.value, 10) : 0);
			var suga_cost = suga_stnd; //수가 단가
		}
	}

	//suga_obj.innerHTML = suga_nm;

	//f.sugaCode.value = suga_cd;
	//f.sugaName.value = suga_nm;

	//f.svcStnd.value = suga_stnd;
	//f.svcCnt.value  = suga_cnt;
	
	//f.sugaCost.value = __num2str(suga_cost);  //단가
	
	
	if (svc_id == 24){
		if (suga_cnt_night > 0){
		}else{
			suga_night    = 0;
			suga_cnt_night = 0;
		}
	}
	
	if (svc_id == 31){
		if (__num2str(f.procTime.value) > 6){
			var sugaTime = __num2str(f.procTime.value) - 1;
		}else{
			var sugaTime = __num2str(f.procTime.value); //소요시간
		}
	}else if (svc_id == 24){
		if (svc_kind == '500' ||
			svc_kind == '800'){
			var sugaTime = __num2str(f.procTime.value); //소요시간
		}else{
			var sugaTime = __num2str(suga_cnt); //소요시간
		}
	}else if (svc_id == 23){
		var sugaTime = __num2str(suga_cnt); //소요시간
	}else{
		var sugaTime = __num2str(f.procTime.value); //소요시간
	}

	if (svc_kind == '500' ||
		svc_kind == '800'){
		var sugaTot     = suga_cost;    //수가계
		var visitSudang = __num2str(extrapay); //방문수당
	}else{
		var suga_tot = suga_cost * sugaTime;
		
		if (suga_cnt_night > 0){
			suga_tot += (suga_night * suga_cnt_night);
		}
		var sugaTot     = suga_tot; //수가계
		var visitSudang = 0;
	}
	
	var str = new Array();
		str = {
				'sugaNM'	 :suga_nm
			,	'sugaCD'	 :suga_cd
			,	'sugaStnd'	 :suga_stnd
			,	'sugaCnt'	 :suga_cnt
			,	'sugaCost'	 :suga_cost
			,	'sugaTime'	 :sugaTime
			,	'sugaTot'	 :sugaTot
			,	'visitSudang':visitSudang
		};
	
	return str;
}


/*********************************************************

	건보에 일정 업로드

*********************************************************/
function _longcareUpload(YYMM, svcKind, uploadYN){
	/*
	var loginFlag = _longcareLoginCheck();

	if (!loginFlag){
		alert('건보 공단 홈페이지에 로그인되어 있지 않습니다.\n건보 공단 홈페이지 로그인을 선행하여 주십시오.');
		return;
	}
	
	if (YYMM >= '201108'){
		if(confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
			_iljungPlanList(svcKind);
		}
	}else{
		alert('건보공단 일정 업로드는 2011년 8월 일정부터 가능합니다.\n\n확인하여 주십시오.');
	}
	*/

	if (!uploadYN) uploadYN = 'Y';

	var msg = '건보 공단 홈페이지에 로그인되어 있지 않습니다.\n건보 공단 홈페이지 로그인을 선행하여 주십시오.';

	try{
		$.ajax({
			type: 'GET',
			url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
			success: function (data){
				if ($('.welcome',data).html()){
					if (YYMM >= '201108'){
						if (uploadYN == 'Y'){
							if(confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
								_iljungPlanList(svcKind, uploadYN);
							}
						}else{
							_iljungPlanList(svcKind, uploadYN);
						}
					}else{
						alert('건보공단 일정 업로드는 2011년 8월 일정부터 가능합니다.\n\n확인하여 주십시오.');
					}
				}else{
					alert('1 : '+msg);
				}
			}
		});
	}catch(e){
		alert('2 : '+msg);
	}
	
	/*
	try{
		$.ajax({
			type: 'POST',
			url : 'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify',
			data: {
				'pageIndex'   : 1
			,	'serviceKind' : ''
			,	'searchFrDt'  : getToday()
			,	'searchToDt'  : getToday()
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				if ($('.npaging', xmlHttp).html()){
					if (YYMM >= '201108'){
						if (uploadYN == 'Y'){
							if(confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
								_iljungPlanList(svcKind, uploadYN);
							}
						}else{
							_iljungPlanList(svcKind, uploadYN);
						}
					}else{
						alert('건보공단 일정 업로드는 2011년 8월 일정부터 가능합니다.\n\n확인하여 주십시오.');
					}
				}else{
					try{
						$.ajax({
							type: 'GET',
							url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
							success: function (data){
								if ($('.welcome',data).html()){
									if (YYMM >= '201108'){
										if (uploadYN == 'Y'){
											if(confirm("일정을 건보공단 일정으로 업로드하시겠습니까?")){
												_iljungPlanList(svcKind, uploadYN);
											}
										}else{
											_iljungPlanList(svcKind, uploadYN);
										}
									}else{
										alert('건보공단 일정 업로드는 2011년 8월 일정부터 가능합니다.\n\n확인하여 주십시오.');
									}
								}else{
									alert(msg);
								}
							}
						});
					}catch(e){
						alert(msg);
					}
				}
			},
			complete: function(){
			},
			error: function (){
				alert(msg);
			}
		}).responseXML;
	}catch(e){
		alert(msg);
	}
	*/
}


/*********************************************************

	가족요양보호사 등록안내

*********************************************************/
function _familyCareRegInfo(obj){
	var t = 0, l = 0, w = 600, h = 200;
	
	t = (document.body.offsetHeight - h) / 2;
	l = (document.body.offsetWidth  - w) / 2;

	// 배경레이어
	$('#cLayer').css('width', $(document).width()).css('height',$(document).height());
	
	//if ($('#iljung_family_info').text() == ''){
		$.ajax({
			type : 'POST'
		,	url : './iljung_family_reg_info.php'
		,	data: {
			}
		,	beforeSend: function(){
				$('#iljung_family_info').css('top',t).css('left',l).css('width',w+'px').css('height',h+'px').css('background-color','#ffffff').css('border','3px solid #cccccc');
			}
		,	success: function (data){
				$('#iljung_family_info').html(data);
			}
		,	complete: function (){
				$('#iljung_family_info').show();
			}
		,	error: function (){
			}
		});
	//}
}


/*********************************************************

	바우처 생성삭제

*********************************************************/
function _voucherClear(){
	$.ajax({
		type : 'POST'
	,	url : './iljung_service_use_del.php'
	,	data: {
			'code'  : $('#code').val()
		,	'year'  : $('#year').val()
		,	'month' : $('#month').val()
		,	'jumin' : $('#jumin').val()
		,	'svcCD' : $('#svcCD').val()
		,	'svcID' : $('#svcID').val()
		,	'seq'	: $('#seq').val()
		}
	,	beforeSend: function(){
		}
	,	success: function (result){
			if (result == 'ok'){
				alert('정상적으로 처리되었습니다.');
				_set_service_use($('#svcID').val());
			}else{
				alert(result);
			}
		}
	,	complete: function (){
		}
	,	error: function (){
		}
	});
}


/*********************************************************

	가족요양보호사 등록 보로가기

*********************************************************/
function _moveFamilyReg(jumin){
	var parm = new Array();
		parm = {
			'jumin'		  : jumin
		,	'page'		  : 1
		,	'current_menu': 'family'
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
    form.setAttribute('action', '../sugupja/client_reg.php');
    
	document.body.appendChild(form);
    
	form.submit();
}


/*********************************************************

	바우처 기본급여 적용여부

*********************************************************/
function _setStndPay(obj, svcId){
	if (svcId == 24){
		//장애인활동지원
		var payYn = $(obj).val().substring(0,1);

		$('#payStndTot').val(payYn != 'X' ? $('#payStndTot').attr('value1') : '0');
		$('#payStndTime').val(payYn != 'X' ? $('#payStndTime').attr('value1') : '0');
		$('#payStndUse').val(payYn != 'X' ? $('#payStndUse').attr('value1') : '0');
		$('#payStndSelf').val(payYn != 'X' ? $('#payStndSelf').attr('value1') : '0');

		_voucher_time_dis(svcId);
	}
}
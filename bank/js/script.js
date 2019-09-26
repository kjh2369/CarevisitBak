var it_timer	= null;	//타이머
var detailAddr	= null; //상세주소

var __COL_CUTER__ = '//';
var __ROW_CUTER__ = ';;';

var on_off = 0;	//마우스 업다운 키
var coord_x = 0;
var coord_y = 0;

var winModal = null;
var gForm = null; //공용 사용 폼

var menu=function(){
	var t=15,z=50,s=6,a;
	function dd(n){this.n=n; this.h=[]; this.c=[]}
	dd.prototype.init=function(p,c){
		a=c; var w=document.getElementById(p), s=w.getElementsByTagName('ul'), l=s.length, i=0;
		for(i;i<l;i++){
			var h=s[i].parentNode; this.h[i]=h; this.c[i]=s[i];
			h.onmouseover=new Function(this.n+'.st('+i+',true)');
			h.onmouseout=new Function(this.n+'.st('+i+')');
		}
	}
	dd.prototype.st=function(x,f){
		var c=this.c[x], h=this.h[x], p=h.getElementsByTagName('a')[0];
		clearInterval(c.t); c.style.overflow='hidden';
		if(f){
			p.className+=' '+a;
			if(!c.mh){c.style.display='block'; c.style.height=''; c.mh=c.offsetHeight; c.style.height=0}
			if(c.mh==c.offsetHeight){c.style.overflow='visible'}
			else{c.style.zIndex=z; z++; c.t=setInterval(function(){sl(c,1)},t)}
		}else{p.className=p.className.replace(a,''); c.t=setInterval(function(){sl(c,-1)},t)}
	}
	function sl(c,f){
		var h=c.offsetHeight;
		if((h<=0&&f!=1)||(h>=c.mh&&f==1)){
			if(f==1){c.style.filter=''; c.style.opacity=1; c.style.overflow='visible'}
			clearInterval(c.t); return
		}
		var d=(f==1)?Math.ceil((c.mh-h)/s):Math.ceil(h/s), o=h/c.mh;
		c.style.opacity=o; c.style.filter='alpha(opacity='+(o*100)+')';
		c.style.height=h+(d*f)+'px'
	}
	return{dd:dd}
}();

function __onlyNumber(object, gubun){
	e = window.event;

	if(e.keyCode >= 48 && e.keyCode <= 57 || e.keyCode >= 96 && e.keyCode <= 105 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 46){
	//	if (parseInt(object.value.length) >= parseInt(object.getAttribute('maxlength'))){
	//		//e.keyCode = 9;
	//		alert('test');
	//	}
		return;
	}else if(e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40){
		//방향키 통과
		return;
	}else if(e.keyCode == 13){
		//엔터키 - 탭으로 변경
		e.keyCode = 9;
	}else{
		//그외 막기
		if (gubun == '' || gubun == undefined){
			e.returnValue = false;
		}else if(gubun == '-'){
			if (e.keyCode == 189 || e.keyCode == 109){
				return;
			}else{
				e.returnValue = false;
			}
		}else if(gubun == '.' || gubun == '.'){
			if (e.keyCode == 110 || e.keyCode == 190){
				if (object.value.indexOf('.') < 0){
					return;
				}else{
					e.returnValue = false;
				}
			}else{
				e.returnValue = false;
			}
		}else if(gubun == ',-'){
			if (e.keyCode == 188 || e.keyCode == 189 || e.keyCode == 109){
				return;
			}else{
				e.returnValue = false;
			}
		}else{
			if (gubun.indexOf(e.keyCode) == -1){
				e.returnValue = false;
			}
		}
	}
}

function __testNumber(object, gubun){
	e = window.event;

	if(e.keyCode >= 48 && e.keyCode <= 57 || e.keyCode >= 96 && e.keyCode <= 105 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 46){
		alert(1);
		return;
	}else if(e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40){
		//방향키 통과
		alert(2);
		return;
	}else if(e.keyCode == 13){
		//엔터키 - 탭으로 변경
		alert(3);
		e.keyCode = 9;
	}else{
		//그외 막기
		if (gubun == '' || gubun == undefined){
			alert(4);
			e.returnValue = false;
		}else if(gubun == '-'){
			if (e.keyCode == 189 || e.keyCode == 109){
				alert(5);
				return;
			}else{
				alert(6);
				e.returnValue = false;
			}
		}else if(gubun == '.' || gubun == '.'){
			if (e.keyCode == 110 || e.keyCode == 190){
				if (object.value.indexOf('.') < 0){
					alert(7);
					return;
				}else{
					alert(8);
					e.returnValue = false;
				}
			}else{
				alert(9);
				e.returnValue = false;
			}
		}else if(gubun == ',-'){
			if (e.keyCode == 188 || e.keyCode == 189 || e.keyCode == 109){
				alert(10);
				return;
			}else{
				alert(11);
				e.returnValue = false;
			}
		}else{
			if (gubun.indexOf(e.keyCode) == -1){
				alert(12);
				e.returnValue = false;
			}else{
				alert(13);
			}
		}
	}
}

function __enterFocus(){
	e = window.event;

	if(e.keyCode == 13){
		e.keyCode = 9;
	}
}

function __checkRowCount(object){
	var target;

	if (object == undefined){
		target = 'check[]';
	}else{
		target = object;
	}

	var check = document.getElementsByName(target);
	var check_count = 0;

	for(var i=0; i<check.length; i++){
		if(check[i].checked){
			check_count++;
			break;
		}
	}

	if(check_count == 0){
		alert("선택된 데이타가 없습니다.");
		return false;
	}

	return true;
}

function __checkRowNo(object){
	var target;
	
	if (object == undefined){
		target = 'check[]';
	}else{
		target = object;
	}

	var check = document.getElementsByName(target);
	var check_count = 0;

	for(var i=0; i<check.length; i++){
		if(check[i].checked){
			check_count++;
			break;
		}
	}

	if(check_count == 0){
		return check_count;
	}

	return check_count;
}

function __checkRow(value, object){
	var target;

	if (object == undefined){
		target = 'check[]';
	}else{
		target = object;
	}

	var check = document.getElementsByName(target);
	
	for(var i=0; i<check.length; i++){
		if (check[i].value == value){
			check[i].checked = !check[i].checked;
			break;
		}
	}
}

function __checkValue(index, object){
	var target;

	if (object == undefined){
		target = 'check[]';
	}else{
		target = object;
	}

	var check = document.getElementsByName(target);
	
	return check[index].value;
}

function __checkSetValue(object){
	var check = document.getElementsByName(object);

	for(var i=0; i<check.length; i++){
		check[i].checked = true;
	}
}

/*
 * target : 체크박스 배열
 * checked : 체크여부
 * 개요 : target의 체크를 checked 로 변환한다.
 */
function __checkMyValue(target, checked){
	var target = document.getElementsByName(target);
	var checked = (typeof(checked) == 'object' ? checked.checked : checked);

	for(var i=0; i<target.length; i++){
		target[i].checked = checked;
	}
}

/*
 * 개요 : target의 체크박스 배열에서 선택된 체크븍스가 몇개인지 찾아본다.
 */
function __checkMyCount(target){
	var target = document.getElementsByName(target);
	var count = 0;

	for(var i=0; i<target.length; i++){
		if (target[i].checked) count ++;
	}

	return count;
}

function __get_value(object){
	var value = '';

	object = __getObject(object);

	for(var i=0; i<object.length; i++){
		if (object[i].checked){
			value = object[i].value;
			break;
		}
	}

	return value;
}

function __get_tag(p_object){
	var object = __getObject(p_object);
	var tag    = '';

	for(var i=0; i<object.length; i++){
		if (object[i].checked){
			tag = object[i].tag;
			break;
		}
	}

	return tag;
}

function __get_temp(p_object){
	var object = __getObject(p_object);
	var temp    = '';

	for(var i=0; i<object.length; i++){
		if (object[i].checked){
			temp = object[i].temp;
			break;
		}
	}

	return temp;
}

function __commaSet(object) {
    var src;
    var i;
    var factor;
    var su;
    var Spacesize = 0;
    
	if (typeof(object) == 'object'){
		var String_val = object.value.toString();
	}else{
		var String_val = object.toString();
	}

	var str_val = '';

	if (String_val.indexOf('.') != -1){
		var temp_str = String_val.split('.');

		String_val = temp_str[0];
		str_val    = temp_str[1];
	}
    
    factor = String_val.length % 3;
    su = (String_val.length - factor) /3;
    src = String_val.substring(0,factor);

    for(i=0; i<su ; i++)
    {
       if ((factor==0)&&(i==0))// " XXX "の場合
        {
             src += String_val.substring(factor+(3*i), factor+3+(3*i));
        }
        else 
        {
            if ( String_val.substring(factor+(3*i) - 1, factor+(3*i)) != "-" ) src +=",";
            src += String_val.substring(factor+(3*i), factor+3+(3*i));
        }
    }

	if (str_val != ''){
		src = src+'.'+str_val;
	}

	if (typeof(object) == 'object'){
		object.value = src;
	}else{
		return src;
	}
}
    
function __commaUnset(object) {
	var x, ch;
	var i=0;
	var newVal="";

	if (typeof(object) == 'object'){
		for(x=0; x <object.value.length ; x++){
			ch=object.value.substring(x,x+1);
			if(ch != ",")  newVal += ch;
		}
		object.value = newVal;
		object.select();
	}else{
		for(x=0; x <object.length ; x++){
			ch=object.substring(x,x+1);
			if(ch != ",")  newVal += ch;
		}
		return newVal;
	}
}

function __objectCommaSet(obj) {
    var src;
    var i;
    var factor;
    var su;
    var Spacesize = 0;
    
    var String_val = obj.value.toString();
    
    factor = String_val.length % 3;
    su = (String_val.length - factor) /3;
    src = String_val.substring(0,factor);

    for(i=0; i<su ; i++)
    {
       if ((factor==0)&&(i==0))// " XXX "の場合
        {
             src += String_val.substring(factor+(3*i), factor+3+(3*i));
        }
        else 
        {
            if ( String_val.substring(factor+(3*i) - 1, factor+(3*i)) != "-" ) src +=",";
            src += String_val.substring(factor+(3*i), factor+3+(3*i));
        }
    }
    obj.value = src;
}

function __objectCommaUnset(obj) {
	var x, ch;
	var i=0;
	var newVal="";
	for(x=0; x <obj.value.length ; x++){
		ch=obj.value.substring(x,x+1);
		if(ch != ",")  newVal += ch;
	}
	obj.value = newVal;
}

function __isYear(tmpYear){
	if(tmpYear == "") return false;
	if(isNaN(tmpYear) || tmpYear<1900 || tmpYear>2100){
		return false;
	}else{
		return true;
	}
}

function __isMonth(tmpMonth){
	if(tmpMonth == "") return false;
	if(isNaN(tmpMonth) || tmpMonth<1 || tmpMonth>12){
		return false;
	}else{
		return true;
	}
}

function __isDate(tmpYear,tmpMonth, tmpDay){
	if (!__isYear(tmpYear)){
		alert('날짜형식 오류입니다. 확인하여 주십시오.');
		return false;
	}
	if (!__isMonth(tmpMonth)){
		alert('날짜형식 오류입니다. 확인하여 주십시오.');
		return false;
	}
	if(isNaN(tmpDay)){
		alert('날짜형식 오류입니다. 확인하여 주십시오.');
		return false;
	}else{
		if(tmpMonth==2 && tmpDay!='' && tmpYear%4==0 && (tmpDay<1 || tmpDay>29)){
			alert('날짜형식 오류입니다. 확인하여 주십시오.');
			return false;
		}else if(tmpMonth==2 && tmpDay!='' && tmpYear%4!=0 && (tmpDay<1 || tmpDay>28)){
			alert('날짜형식 오류입니다. 확인하여 주십시오.');
			return false;
		}else if((tmpMonth==4 || tmpMonth==6 || tmpMonth==9 || tmpMonth==11) && tmpDay!='' && (tmpDay<1 || tmpDay>30)){
			alert('날짜형식 오류입니다. 확인하여 주십시오.');
			return false;
		}else if((tmpMonth==1 || tmpMonth==3 || tmpMonth==5 || tmpMonth==7 || tmpMonth==8 || tmpMonth==10 || tmpMonth==12) && tmpDay!='' && (tmpDay<1 || tmpDay>31)){
			alert('날짜형식 오류입니다. 확인하여 주십시오.');
			return false;
		}else{
			return true;
		}
	}
}

function __isDateYN(tmpYear,tmpMonth, tmpDay){
	if (!__isYear(tmpYear)){
		return false;
	}
	if (!__isMonth(tmpMonth)){
		return false;
	}
	if(isNaN(tmpDay)){
		return false;
	}else{
		if(tmpMonth==2 && tmpDay!='' && tmpYear%4==0 && (tmpDay<1 || tmpDay>29)){
			return false;
		}else if(tmpMonth==2 && tmpDay!='' && tmpYear%4!=0 && (tmpDay<1 || tmpDay>28)){
			return false;
		}else if((tmpMonth==4 || tmpMonth==6 || tmpMonth==9 || tmpMonth==11) && tmpDay!='' && (tmpDay<1 || tmpDay>30)){
			return false;
		}else if((tmpMonth==1 || tmpMonth==3 || tmpMonth==5 || tmpMonth==7 || tmpMonth==8 || tmpMonth==10 || tmpMonth==12) && tmpDay!='' && (tmpDay<1 || tmpDay>31)){
			return false;
		}else{
			return true;
		}
	}
}

function __checkDate(date2, date1){
	var value1 = __setToNumber(date1.value);
	var value2 = __setToNumber(date2.value);

	if (!__isDate(value1.substring(0,4), value1.substring(4,6), value1.substring(6,8))){
		try{
			date1.select();
		}catch(e){
		}
		return;
	}

	if (!__isDate(value2.substring(0,4), value2.substring(4,6), value2.substring(6,8))){
		date2.select();
		return;
	}

	if (parseInt(value1) > parseInt(value2)){
		alert(date2.alt + '은 ' + date1.alt + '보다 커야합니다.');
		date2.value = '';
		date2.focus();
		date2.select();
		return;
	}
}

// 년월비교
function __checkYM(date, gubun){
	var now = new Date();
	var nowYear  = now.getFullYear();
	var nowMonth = now.getMonth() + 1;
		nowMonth = (nowMonth < 10 ? '0' : '') + nowMonth;
	var nowDate  = nowYear + nowMonth;
	var mDate = date;

	switch(gubun){
		default:
			if (nowDate > mDate){
				return true;
			}else{
				return false;
			}
	}
}

function __isSSN(jumin1,jumin2) {
	var today = new Date();
	var chkYear1 = today.getYear();
	var chkYear2 = 0;

	if (chkYear1 < 2000) chkYear1 += 1900;

	var chk = 0;
	var chk2 = 0;
	var chk3 = 0;
	var yy = jumin1.substring(0,2);
	var mm = jumin1.substring(2,4);
	var dd = jumin1.substring(4,6);
	var chkSex = jumin2.substring(0,1); 

	if ((jumin1.length != 6) || (mm<1 || mm>12 || dd<1 || dd>31 )) return false;
	//if ((chkSex != 1 && chkSex !=2 && chkSex !=3 && chkSex !=4) || (jumin2.length != 7)) return false;
	if ((jumin2.length != 7)) return false;

	chkYear2 = parseInt(yy,10);

	if (chkSex <=2) chkYear2 += 1900;
	else chkYear2 += 2000;
 
	for (var i = 0; i <=5 ; i++) chk = chk + ((i%8+2) * parseInt(jumin1.substring(i,i+1)));

	for (var i = 6; i <=11 ; i++) chk = chk + ((i%8+2) * parseInt(jumin2.substring(i-6,i-5)));

	chk = 11 - (chk %11);
	chk = chk % 10;

	if (chk != jumin2.substring(6,7)) return false;  

	return true;
}

function _setDisabled(object, target){
	if(object.value != 9){
		target.disabled = true;
		target.style.backgroundColor = "#eeeeee";
		//target.style.borderColor = "#cccccc";
	}else{
		target.disabled = false;
		target.style.backgroundColor = "#ffffff";
		//target.style.borderColor = "#cccccc";
	}
}

function __setEnabled(target, enabled){
	try{
		if (typeof(target) != 'object'){
			target = document.getElementsByName(target);
		}else{
			target = document.getElementsByName(target.name);
		}

		if (target.length == undefined){
			var cnt = 1;
		}else{
			var cnt = target.length;
		}

		for(var i=0; i<cnt; i++){
			if (enabled){
				target[i].disabled = false;

				if (target[i].className == 'checkbox' || target[i].className == 'radio'){
				}else{
					target[i].style.backgroundColor = "#ffffff";
				}
			}else{
				target[i].disabled = true;

				if (target[i].className == 'checkbox' || target[i].className == 'radio'){
				}else{
					target[i].style.backgroundColor = "#eeeeee";
				}
			}
		}
	}catch(e){
		//__show_error(e);
	}
}

function __setToNumber(target){
	var num = "0123456789";
	var str = "";
	var ret = "";

	if (typeof(target) == 'object'){
		var value = target.value;
	}else{
		var value = target;
	}

	for(var i=0; i<value.length; i++){
		str = value.substring(i,i+1);

		if(num.indexOf(str) != -1){
			ret += str;
		}
	}

	if (typeof(target) == 'object'){
		target.value = ret;
	}else{
		return ret;
	}
}

function __toNumber(target){
	var value = null;

	if (typeof(target) == 'object'){
		value = target.value;
	}else{
		value = target;
	}

	var num = "0123456789";
	var str = "";
	var ret = "";
	try{
		for(var i=0; i<value.length; i++){
			str = value.substring(i,i+1);

			if(num.indexOf(str) != -1){
				ret += str;
			}
		}
	}catch(e){
		ret = 0;
	}

	if (typeof(target) == 'object'){
		target.value = ret;
	}else{
		return ret;
	}
}

function __setToDate(target){
	var value   = __toNumber(target.value);
	var value_y = value.substring(0, 4);
	var value_m = value.substring(4, 6);
	var value_d = value.substring(6, 8);
	
	target.value = value_y + '년' + value_m + '월' + value_d + '일'

	if (target.value.length != 11){
		target.value = '';
	}
}

function __setDateYMD(value){
	var value_s = __toNumber(value);
	var value_y = value_s.substring(0, 4);
	var value_m = value_s.substring(4, 6);
	var value_d = value_s.substring(6, 8);
	var value_r = value_y + '년' + value_m + '월' + value_d + '일';

	return value_r;
}

function __setDate(target){
	var value = null;

	if (typeof(target) == 'object'){
		value = __toNumber(target.value);
	}else{
		value = __toNumber(target);
	}

	var value_y = value.substring(0, 4);
	var value_m = value.substring(4, 6);
	var value_d = value.substring(6, 8);
	
	if (typeof(target) == 'object'){
		target.value = value_y + '.' + value_m + '.' + value_d;
	}else{
		return value_y + '.' + value_m + '.' + value_d;
	}
}

function __show_tooltip(object, value){
	var x  = document.body.clientTop + __getObjectLeft(object);
		//x -= w - object.offsetWidth;
	var y  = document.body.clientTop + __getObjectTop(object);
		y += object.offsetHeight;

	ToolTip_Body.innerHTML = value;
	pop_ToolTop.style.left = x;
	pop_ToolTop.style.top = y;
	pop_ToolTop.style.display = '';
}

function __close_tooltip(){
	pop_ToolTop.style.display = 'none';
}

function __checkText(target){
	if (target.value.split(' ').join('') == ''){
		alert(target.alt);
		target.focus();
		return false;
	}
	return true;
}

function __checkRadio(target){
	for(var i=0; i<target.length; i++){
		if (target[i].checked){
			return true;
		}
	}
	alert(target[0].alt);
	return false;
}

function __getRadioValue(obj){
	for(var i=0; i<obj.length; i++){
		if (obj[i].checked){
			return i;
		}
	}
	return 0;
}

function __getObjectTop(obj){
	try{
		if (obj.offsetParent == document.body)
			return obj.offsetTop;
		else
			return obj.offsetTop + __getObjectTop(obj.offsetParent);
	}catch(e){
		return 0;
	}
}

function __getObjectLeft(obj){
	try{
		if (obj.offsetParent == document.body)
			return obj.offsetLeft;
		else
			return obj.offsetLeft + __getObjectLeft(obj.offsetParent);
	}catch(e){
		return 0;
	}
}

/*
 * 객체의 넓이보다 문장의 넓이가 더 길면 길이만큼 자르고 문장뒤에 ...를 붙여준다.
 * 객체의 Mouse Over, Out 이벤트에 툴팁을 설정한다.
 */
function __getStringValue(target, value, gab_value){
	var gab = 0;

	if (typeof(gab_value) == 'number'){
		gab = Math.abs(gab_value);
	}else{
		gab = 0;
	}
	var stringV = value + '▒▒';
	var target_parent = target.offsetParent;
	var string_width  = __get_string_width(stringV);
	var string_value  = '';
	var string_tool   = '';

	if (string_width > target_parent.offsetWidth){
		if (typeof(gab_value) == 'number'){
			gab = gab_value;
		}
		string_value = __get_string_width(stringV, target_parent.offsetWidth - gab);
		string_tool  = value;
	}else{
		string_value = value;
		string_tool  = '';
	}

	if (string_tool != ''){
		if (string_tool.substring(string_tool.length-2, string_tool.length) == '▒▒'){
			string_tool = string_tool.substring(0,string_tool.length-2);
		}
		target_parent.onmouseover = function(){__show_tooltip(target_parent, string_tool);};
		target_parent.onmouseout  = __close_tooltip;
	}else{
		target_parent.onmouseover = __close_tooltip;
		target_parent.onmouseout  = __close_tooltip;
	}
	
	if (string_value.substring(string_value.length-2, string_value.length) == '▒▒'){
		string_value = string_value.substring(0,string_value.length-2);
	}

	return string_value;
}

function __tooltip_set(target, string_tool){
	target.onmouseover = function(){__show_tooltip(target, string_tool);};
	target.onmouseout  = __close_tooltip;
}

function __tooltip_unset(target){
	target.onmouseover = __close_tooltip;
	target.onmouseout  = __close_tooltip;
}

/* 문자열의 길이를 픽셀단위로 리턴한다. */
function __get_string_width(string_value, string_length){
    var ascii_code;
    var string_value_length = string_value.length;
    var character;
    var character_width;
    var character_length;
    var total_width = 0;
    var total_length = 0;

    var special_char_size = 6;
    var multibyte_char_size = 12;
    var base_char_start = 32;
    var base_char_end =  127;
    var ascii_char_size = Array(4,4,4,6,6,10,8,4,5,5,6,6,4,6,4,6,6,6,6,6,6,6,6,6,6,6,4,4,8,6,8,6,12,8,8,9,8,8,7,9,8,3,6,8,7,11,9,9,8,9,8,8,8,8,8,10,8,8,8,6,11,6,6,6,4,7,7,7,7,7,3,7,7,3,3,6,3,11,7,7,7,7,4,7,3,7,6,10,7,7,7,6,6,6,9,6);
	var num_string = '0123456789';
	var return_string = '';
	var return_length = 0;

	if (typeof(string_length) == 'undefined'){
		return_length = '';
	}else{
		return_length = parseInt(string_length);
	}
	if (return_length == ''){
		return_length = 0;
	}

	for(i=0; i<string_value_length; i++){
        character = string_value.substring(i,(i+1));
        ascii_code = character.charCodeAt(0);

        if(ascii_code < base_char_start){
            character_width = special_char_size;
        }else if(ascii_code <= base_char_end){
            idx = ascii_code - base_char_start;
            character_width = ascii_char_size[idx];
        }else if(ascii_code > base_char_end){
            character_width = multibyte_char_size;
        }
        total_width += character_width;

		if (parseInt(return_length) > 0){
			if (parseInt(return_length) < parseInt(total_width)){
				return_string = string_value.substring(0, i - 2)+'...';
				break;
			}
		}
    }

	if (parseInt(return_length) == 0){
		return_string = total_width;
	}

	return return_string;
}

/* 주소찾기 */
function __zipcode(postno1, postno2, address1, address2){
	var modal = showModalDialog('../inc/_zipcode.asp', window, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');

	if (modal == undefined){
		return;
	}else if(modal == 'cancel'){
		return;
	}else{
		var value = modal.split('//');

		postno1.value = value[0];
		postno2.value = value[1];
		address1.value = value[2];
		address2.value = value[3];
		address2.style.imeMode = 'active';
		address2.focus();
	}
}

/* 전화번호 */
function __getPhoneNo(target){
	if (typeof(target) == 'object'){
		target.value = target.value.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,'$1-$2-$3');
	}else{
		return target.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,'$1-$2-$3');
	}
}

function __getDate(target){
	var ret = null;
	var varDate;
	var y, m, d;
	var ry, rm, rd;
	
	if (typeof(target) == 'object'){
		varDate = target.value;
	}else{
		varDate = target;
	}

	varDate = __replace(varDate, '-', '');
	varDate = __replace(varDate, '.', '');

	if (varDate.length != 8){
		varDate = '';
	}else{
		y = varDate.substring(0,4);
		m = varDate.substring(4,6);
		d = varDate.substring(6,8);

		if (m.substring(0,1) == '0'){
			m = m.substring(1,2);
		}

		if (d.substring(0,1) == '0'){
			d = d.substring(1,2);
		}

		y = parseInt(y);
		m = parseInt(m);
		d = parseInt(d);

		if (y > 1900 && (m > 1 || m < 12)){
			if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12){
				if (d > 31){
					varDate = '';
				}
			}else if (m == 4 || m == 6 || m == 9 || m == 11){
				if (d > 30){
					varDate = '';
				}
			}else if(m == 2){
				if (d > 29){
					varDate = '';
				}
			}else{
			}
			if (varDate != ''){
				ry = y;
				rm = m < 10 ? '0'+m : m;
				rd = d < 10 ? '0'+d : d;
				varDate = ry+'-'+rm+'-'+rd;
			}

			if (!checkDate(varDate)){
				varDate = '';
			}
		}else{
			varDate = '';
		}
	}

	if (typeof(target) == 'object'){
		target.value = varDate;
	}else{
		return varDate;
	}
}

function __getYYSCode(target){
	if (typeof(target) == 'object'){
		try{
			target.innerHTML = target.innerHTML.substring(0,4)+'-'+target.innerHTML.substring(4,11);
		}catch(e){
			target.value = target.value.substring(0,4)+'-'+target.value.substring(4,11);
		}
	}else{
		return target.substring(0,4)+'-'+target.substring(4,11);
	}
}

//사업자등록번호 체크 
function __checkBizID(target){ 
	if (typeof(target) == 'object'){
		var bizID = target.value;
	}else{
		var bizID = target;
	}
    // bizID는 숫자만 10자리로 해서 문자열로 넘긴다. 
    var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1); 
    var tmpBizID, i, chkSum=0, c2, remander; 
     bizID = bizID.replace(/-/gi,''); 

     for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i); 
     c2 = "0" + (checkID[8] * bizID.charAt(8)); 
     c2 = c2.substring(c2.length - 2, c2.length); 
     chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1)); 
     remander = (10 - (chkSum % 10)) % 10 ; 

    if (Math.floor(bizID.charAt(9)) == remander){
		bizID = bizID.substring(0,3)+'-'+bizID.substring(3,5)+'-'+bizID.substring(5,10);
		if (typeof(target) == 'object'){
			target.value = bizID;
		}else{
			target = bizID;
		}
		return true;
	}else{
		if (typeof(target) == 'object'){
			target.value = '';
		}else{
			target = '';
		}
		//ralert('입력하신 사업자등록번호의 형식이 올바르지 않습니다. 확인하여 주십시오.');
		event.returnValue = false
		return false; 
	}
} 


/* 문자 치환 */
function __replace(target, value1, value2){
	if (typeof(target) == 'object'){
		target.value = target.value.split(value1).join(value2);
		target.select();
	}else{
		return target.split(value1).join(value2);
	}
}

function __sumNumber(obj, object){
	var target;
	var number = 0;
	var index = 1;

	while(1){
		try{
			target = eval(obj+index);

			if (typeof(target) == 'object'){
				number = parseInt(number) + parseInt(__commaUnset(target.value) == '' ? '0' : __commaUnset(target.value));
			}else{
				break;
			}
		}catch(e){
			break;
		}
		index++;
	}

	if (typeof(object) == 'object'){
		if (object.value != undefined){
			object.value = __commaSet(number);
		}else{
			object.innerHTML = __commaSet(number);
		}
	}else{
		return __commaSet(number);
	}
}



/******************************************************
 * tbl      : 병합할 대상 table object
 * startRow : 병합 시작 row, title 한 줄일 경우 1
 * cNum     : 병합 실시할 컬럼번호, 0부터 시작
 * length   : 병합할 row의 길이, 보통 1
 * add      : 비교할 기준에 추가할 컬럼번호
 * A | 1
 * B | 1
 * 을 서로 구분하고 싶다면, add에 0번째
 * 컬럼을 추가
 *****************************************************/
function __mergeCell(tbl, startRow, cNum, length, add){
	var isAdd = false;
    
	if(tbl == null) return;
    if(startRow == null || startRow.length == 0) startRow = 1;
    if(cNum == null || cNum.length == 0) return ;
	if(add  == null || add.length == 0) {
		isAdd = false;
	}else {
		isAdd = true;
		add   = parseInt(add);
	}
	cNum   = parseInt(cNum);
	length = parseInt(length);

	rows   = tbl.rows;
	rowNum = rows.length;

	tempVal  = '';
	cnt      = 0;
	startRow = parseInt(startRow);

	for( i = startRow; i < rowNum; i++ ) { 
		curVal = rows[i].cells[cNum].innerHTML;
		if(isAdd) curVal += rows[i].cells[add].innerHTML;
		if( curVal == tempVal ) {
			if(cnt == 0) {
				cnt++;
				startRow = i - 1;
			}
			cnt++;
		}else if(cnt > 0) {
			merge(tbl, startRow, cnt, cNum, length);
			startRow = endRow = 0;
			cnt = 0;
		}else {
		}
		tempVal = curVal;
	}

	if(cnt > 0) {
		merge(tbl, startRow, cnt, cNum, length);
	}
}
/***************************************************
 * mergeCell에서 사용하는 함수
 **************************************************/
function merge(tbl, startRow, cnt, cellNum, length){
	rows = tbl.rows;
	row  = rows[startRow];

	for( i = startRow + 1; i < startRow + cnt; i++ ) {
		for( j = 0; j < length; j++) {
			rows[i].deleteCell(cellNum);
		}
	}
	for( j = 0; j < length; j++) {
		row.cells[cellNum + j].rowSpan = cnt;
	}
}
/********************************************
 * 공백병합 표3의 경우에 사용.
 * tbl      : 병합할 대상 table object
 * startRow : 병합 시작 row, title 한 줄일 경우 1
 * cNum     : 병합 실시할 컬럼번호, 0부터 시작
 * 예) mergeCellSpace($('list_tbl'), 1, 1);
 *******************************************/
function mergeCellSpace(tbl, startRow, cNum){
	if(tbl == null) return;
    if(startRow == null || startRow.length == 0) startRow = 1;
    if(cNum == null || cNum.length == 0) return ;

	var rows = tbl.rows;
    var targetCell;
    var cnt = 1;

	for(var i = startRow; i < rows.length; i++ ) {
		var cell = rows[i].cells[cNum];

		if(cell.innerHTML.length > 0) {
			if(cnt > 1) {
				targetCell.rowSpan = cnt;
				cnt = 1;
			}
			targetCell = cell;
		}else{
			cnt++;
			rows[i].deleteCell(cNum);
		} 
    }
    if(cnt > 1) targetCell.rowSpan = cnt;
}

function __helpMCenter(mCode, mKind, code1, cName){
	var help = showModalDialog('../inc/_help.php?r_gubun=centerList', window, 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no');
	
	if (typeof(help) != 'object'){
		return false;
	}

	if (typeof(mCode) == 'object'){
		try{
			mCode.innerHTML = help[0];
		}catch(e){
			mCode.value = help[0];
		}
	}

	if (typeof(mKind) == 'object'){
		try{
			mKind.innerHTML = help[4];
		}catch(e){
			mKind.value = help[4];
		}
	}

	if (typeof(code1) == 'object'){
		try{
			code1.innerHTML = help[2];
		}catch(e){
			code1.value = help[2];
		}
	}

	if (typeof(cName) == 'object'){
		try{
			cName.innerHTML = help[3];
		}catch(e){
			cName.value = help[3];
		}
	}

	try{
		notBody.style.display = 'none';
		yoyBody.style.display = '';
	}catch(e){
	}

	try{
		/*
		document.forms[0].curMcode.value = help[0];
		document.forms[0].curMkind.value = help[1];
		document.forms[0].curCode1.value = help[2];
		document.forms[0].curCname.value = help[3];
		*/
		document.getElementById('curMcode').value = help[0];
		document.getElementById('curMkind').value = help[1];
		document.getElementById('curCode1').value = help[2];
		document.getElementById('curCname').value = help[3];
	}catch(e){
	}

	try{
		/*
		document.forms[0].searchMcode.value = help[0];
		document.forms[0].searchMkind.value = help[1];
		document.forms[0].searchCode1.value = help[2];
		document.forms[0].searchCname.value = help[3];
		*/
		document.getElementById('searchMcode').value = help[0];
		document.getElementById('searchMkind').value = help[1];
		document.getElementById('searchCode1').value = help[2];
		document.getElementById('searchCname').value = help[3];
	}catch(e){
	}
}

function __helpAddress(postNo1, postNo2, addr1, addr2, join){
	var help = showModalDialog('../inc/_help.php?r_gubun=address&join='+join, window, 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');

	if (help == undefined){
		return;
	}

	var postNo = help[0].split('-');

	var postNo1 = __getObject(postNo1);
	var postNo2 = __getObject(postNo2);
	var addr1 = __getObject(addr1);
	
	detailAddr = __getObject(addr2);

	//detailAddr.style.imeMode = 'auto';

	postNo1.value = postNo[0];
	postNo2.value = postNo[1];

	addr1.value = help[1];
	detailAddr.value = help[2];

	it_timer = setInterval("__setAddrFocus()",500);
	
	//addr2.focus();
	//addr2.select();
}

function __setAddrFocus(){
	detailAddr.focus();
	detailAddr.select();

	clearInterval(it_timer);
}

function __helpAddress2(postNo, addr1){
	var help = showModalDialog('../inc/_help.php?r_gubun=address', window, 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');

	if (help == undefined) return;
	
	postNo.value = help[0];
	addr1.value = help[1];
}

function __helpAddress3(addr1){
	var help = showModalDialog('../inc/_help.php?r_gubun=address', window, 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');

	if (help == undefined) return;
	
	addr1.value = '['+help[0]+'] '+help[1]+' ';
}

function __helpYoy(mCode, mKind, yCode, yName){
	var help = showModalDialog('../inc/_help.php?r_gubun=yoyFind&mCode='+mCode+'&mKind='+mKind, window, 'dialogWidth:200px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no');
	
	if (help == undefined){
		return;
	}

	try{
		for(var i=1;i<=5;i++){
			var object = eval('document.center.yoyangsa'+i);

			if (object.name != yCode.name){
				if (object.value == help[1]){
					alert('선택하신 요양보호사는 담당요양보호사'+i+'에 이미 등록되어 있습니다. 다른 요양보하사를 선택하여 주십시오.');
					yCode.value = '';
					yName.value = '';
					return;
				}
			}
		}
	}catch(e){}

	yCode.value = help[1];
	yName.value = help[2];
}

function __notYoy(index){
	var yoyID = document.getElementById('yoyangsa'+index);
	var yoyNM = document.getElementById('yoyangsa'+index+'Nm');

	yoyID.value = '';
	yoyNM.value = '';
}


function cutOff(val){
	return val - (val % 10);
}

function cut(val, cutVal){
	return val - (val % cutVal);
}

function __setCookie(name, value, expiredays ){ 
	var todayDate = new Date(); 
		todayDate.setDate( todayDate.getDate() + expiredays ); 

	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
}
/*
function __getCookie( name ){  
	var nameOfCookie = name + "="; 
	var x = 0; 
	
	while ( x <= document.cookie.length ) { 
		var y = (x+nameOfCookie.length); 
		
		if ( document.cookie.substring( x, y ) == nameOfCookie ){ 
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 ) 
				endOfCookie = document.cookie.length; 
				return unescape( document.cookie.substring( y, endOfCookie ) ); 
			} 
			x = document.cookie.indexOf( " " ) + 1; 
			if ( x == 0 ) break; 
	} 
	return ""; 
}
*/
function __getCookie(name){
	var bikky = document.cookie;
	var index = bikky.indexOf(name + "=");

	if (index == -1) return null;

	index = bikky.indexOf("=", index) + 1; // first character
	var endstr = bikky.indexOf(";", index);

	if (endstr == -1) endstr = bikky.length; // last character

	return unescape(bikky.substring(index, endstr));
}


function __setSelectBox(object, value, text){
	var option = null;

	option = document.createElement("option");
	option.value = value;
	option.text  = text;
	object.add(option);
}

// 시간 형식
function __styleTime(target){
	var value = '';

	if (typeof(target) == 'object'){
		value = target.value;
	}else{
		value = target;
	}

	value = __replace(value, ':', '');

	var time = value.substring(0,2)+':'+value.substring(2,4);

	if (typeof(target) == 'object'){
		target.value = time;
	}else{
		return time;
	}
}

function __getTime(target){
	var value = '';

	if (typeof(target) == 'object'){
		value = target.value;
	}else{
		value = target;
	}

	var time = value.substring(0,2);

	if (typeof(target) == 'object'){
		target.value = time;
	}else{
		return time;
	}
}

// 날짜 형식
function __styleDate(target){
	var value = '';

	if (typeof(target) == 'object'){
		value = target.value;
	}else{
		value = target;
	}

	var time = value.substring(0,2)+'/'+value.substring(2,4);

	if (typeof(target) == 'object'){
		target.value = time;
	}else{
		return time;
	}
}

// 요양사
function __setYoyList(mCode, mKind, mSelect){
	var request = getHttpRequest('../inc/_check.php?gubun=getYoyList&mCode='+mCode+'&mKind='+mKind);
	var select = null;	
		select = document.getElementById(mSelect);
		select.innerHTML = '';

	var yoyList = request.split(';;');

	for(var i=0; i<yoyList.length - 1; i++){
		var yoyValue = yoyList[i].split('//');

		__setSelectBox(select, yoyValue[0], yoyValue[1]);
	}
}

// 일정이 있는 요양사
function __setYoyIljungList(mCode, mKind, mDate, mSelect, mYoyangsa, mLocation){
	if (mDate == '') return;
	
	var select = null;	
		select = document.getElementById(mSelect);

	if (select == null) return;

	var request = getHttpRequest('../inc/_check.php?gubun=getYoyIljungList&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&location='+mLocation);
	
	select.innerHTML = '';

	var yoyList = request.split(';;');

	__setSelectBox(select, '', '-요양사-');

	for(var i=0; i<yoyList.length - 1; i++){
		var yoyValue = yoyList[i].split('//');

		__setSelectBox(select, yoyValue[0], yoyValue[1]);

		if (mYoyangsa == undefined || mYoyangsa == ''){
		}else{
			if (mYoyangsa == yoyValue[0]){
				select.options[i+1].selected = true;
			}
		}
	}
}

// 수급자
function __setSugupList(mCode, mKind, mSelect){
	var request = getHttpRequest('../inc/_check.php?gubun=getSugupList&mCode='+mCode+'&mKind='+mKind);
	var select = null;	
		select = document.getElementById(mSelect);
		select.innerHTML = '';

	var suList = request.split(';;');
	
	for(var i=0; i<suList.length - 1; i++){
		var suValue = suList[i].split('//');

		__setSelectBox(select, suValue[0], suValue[1]);
	}
}

// 일정년도
function __setInIljungYear(mCode, mSelect, mYear){
	var request = getHttpRequest('../inc/_check.php?gubun=getInIljungYear&mCode='+mCode);
	var select = null;	
		select = document.getElementById(mSelect);
		select.innerHTML = '';

	var suList = request.split(';;');
	
	for(var i=0; i<suList.length - 1; i++){
		var suValue = suList[i].split('//');

		__setSelectBox(select, suValue[0], suValue[1]);

		if (mYear == suValue[0]){
			select.options[i].selected = true;
		}
	}
}

// 일정월
function __setInInjungMonth(mSelect, mMonth){
	var select = null;	
		select = document.getElementById(mSelect);
		select.innerHTML = '';

	var monthValue = 0;

	for(var i=1; i<=12; i++){
		monthValue = i;
		if (monthValue < 10) monthValue = '0'+monthValue;
		__setSelectBox(select, monthValue, monthValue);

		if (mMonth == monthValue){
			select.options[i-1].selected = true;
		}
	}
}

// 일정일
function __setInInjungDay(pYear, pMonth, pDay, mSelect){
	var select = null;	
		select = document.getElementById(mSelect);
		select.innerHTML = '';

	var lastDay = __getLastDay(pYear, pMonth);
	var dayValue = 0;

	for(var i=1; i<=lastDay; i++){
		dayValue = i;
		if (dayValue < 10) dayValue = '0'+dayValue;
		__setSelectBox(select, dayValue, dayValue);

		if (pDay == dayValue){
			select.options[i-1].selected = true;
		}
	}
}

// 말일
function __getLastDay(pYear, pMonth){
	var dayOfMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30 ,31, 30 ,31);

	if (((pYear % 4 == 0) && (pYear % 100 != 0)) || (pYear % 400 == 0)){
		dayOfMonth[1] = 29;
	}

	var lastDay = dayOfMonth[pMonth - 1];

	return lastDay;
}

// 체크
function __setCheckValue(object){
	if (object.checked){
		object.checked = false;
	}else{
		object.checked = true;
	}
}

// TextArea MaxLength
function __checkMaxLength(object, length){
	var len = 0;
	var maxlength = length; //object.getAttribute ? parseInt(object.getAttribute('maxlength')) : '';

	if (object.value == null){
		return 0;
	}

	for(var i=0; i<object.value.length; i++){
		var es_len = escape(object.value.charAt(i));

		if(es_len.length == 1) len ++;
		else if(es_len.indexOf('%u') != -1) len += 2;
		else if(es_len.indexOf('%') != -1) len += es_len.length / 3;
	}

	if (len >= maxlength){
		object.value = object.value.substring(0, maxlength);
	}
}

// 텍스트 포맷
function __formatString(target, typeF){
	if (typeof(target) == 'object'){
		var value = target.value;
	}else{
		var value = target;
	}

	if (__replace(value, ' ', '') == ''){
		return '';
	}

	var len = 0;
	var l = 0;
	var sLen = 0;
	var s = '';
	var t = '';
	for(var i=0; i<typeF.length; i++){
		if (typeF.substring(i, i+1) == '#'){
			len ++;
		}else{
			t = typeF.substring(len + l, len+l+1);
			s += value.substring(sLen, len) + t;
			sLen = len;
			l ++;
		}
	}
	s += value.substring(sLen, len);

	if (typeof(target) == 'object'){
		target.value = s;
	}else{
		return s;
	}
}

// 주민번호로 성별을 판단한다.
function __getGender(pJuminNo){
	if (__replace(pJuminNo, '-', '').length != 13){
		return '';
	}

	if (pJuminNo.length < 7){
		return '';
	}

	var gender = pJuminNo.split('-').join('').substring(6,7);

	if (gender % 2 == 1){
		return '남';
	}else{
		return '여';
	}
}

// 주민번호에서 생년월일을 가져온다.
function __getBirthday(pJuminNo){
	if (__replace(pJuminNo, '-', '').length != 13){
		return '';
	}

	var value = __replace(pJuminNo, '-', '');
	var gubun = value.substring(6, 7);
		value = value.substring(0, 2)+"-"+value.substring(2, 4)+"-"+value.substring(4, 6);

	switch(gubun){
		case "1":
			value = "19"+value;
			break;
		case "2":
			value = "19"+value;
			break;
		case "9":
			value = "18"+value;
			break;
		case "0":
			value = "18"+value;
			break;
		default:
			value = "20"+value;
	}
	return value;
}

// 주민번호로 나이를 구한다.
function getJuminToAge(jumin){
	var now = new Date();
	var y1 = now.getFullYear();
	var y2 = parseInt(__getJuminToYear(jumin.split('-').join('').substring(6,7)))+parseInt(jumin.substring(0, 2));
	
	return y1-y2;
}

// 주민번호 7번째 구분자로 연도를 산출한다.
function __getJuminToYear(gubun){
	var value = '';

	switch(gubun){
		case "1":
			value = 1900;
			break;
		case "2":
			value = 1900;
			break;
		case "9":
			value = 1800;
			break;
		case "0":
			value = 1800;
			break;
		default:
			value = 2000;
	}

	return value;
}

// 
function __setDis(object, target){
	object = ((typeof(object) == 'object') ? object : document.getElementsByName(object));
	target = ((typeof(target) == 'object') ? target : document.getElementById(target));

	if (object.length > 1){
		for(var i=0; i<object.length; i++){
			if (object[i].checked){
				if (object[i].value != 9){
					target.disabled = true;
					target.style.backgroundColor = "#eeeeee";
				}else{
					target.disabled = false;
					target.style.backgroundColor = "#ffffff";
				}
				break;
			}
		}
	}else{
		if (object.value != 9){
			target.disabled = true;
			target.style.backgroundColor = "#eeeeee";
		}else{
			target.disabled = false;
			target.style.backgroundColor = "#ffffff";
		}
	}
}


//
function __setDiss(object, target){
	object = ((typeof(object) == 'object') ? object : document.getElementsByName(object));
	target = ((typeof(target) == 'object') ? target : document.getElementById(target));

	if (object.length > 1){
		for(var i=0; i<object.length; i++){
			if (object[i].checked){
				if (object[i].value != 'A' && object[i].value != 'B'){
					target.disabled = true;
					target.style.backgroundColor = "#eeeeee";
				}else{
					target.disabled = false;
					target.style.backgroundColor = "#ffffff";
				}
				break;
			}
		}
	}else{
		if (object.value != 'A' && object.value != 'B'){
			target.disabled = true;
			target.style.backgroundColor = "#eeeeee";
		}else{
			target.disabled = false;
			target.style.backgroundColor = "#ffffff";
		}
	}
}


//
function __checkVBDate(object){
	try{
		if(object.value.length == 4){
			object.value=object.value.substring(0,2)+':'+object.value.substring(2,4);
			
			if (!checkDate('2010-11-11 '+object.value+':01')){
				object.value='';
			}
		}else{
			object.value='';
		}
	}catch(e){}
}

// 요일
function __getWeekDay(target, returnType){
	if (typeof(target) == 'object'){
		var date = target.value;
	}else{
		var date = target;
	}

	if (date.length == 10){
		var dd = date.split('-');
		var now = new Date();

		now.setFullYear(dd[0]);
		now.setMonth(dd[1]-1);
		now.setDate(dd[2]);
	}else if(date.length == 8){
		var year = date.substring(0,4);
		var month = date.substring(4,6);
		var day = date.substring(6,2);
		
		var now = new Date();
		
		now.setFullYear(year);
		now.setMonth(month-1);
		now.setDate(day);
	}else{
		return '';
	}

	switch(returnType){
	case 'K':
		switch(now.getDay()){
		case 0:
			var weekDay = '<font color="#ff0000">일요일</font>';
			break;
		case 1:
			var weekDay = '월요일';
			break;
		case 2:
			var weekDay = '화요일';
			break;
		case 3:
			var weekDay = '수요일';
			break;
		case 4:
			var weekDay = '목요일';
			break;
		case 5:
			var weekDay = '금요일';
			break;
		case 6:
			var weekDay = '<font color="#0000ff">토요일</font>';
			break;
		}
		break;
	case 'E':
		switch(now.getDay()){
		case 0:
			var weekDay = '<font color="#ff0000">Sun</font>';
			break;
		case 1:
			var weekDay = 'Mon';
			break;
		case 2:
			var weekDay = 'Tue';
			break;
		case 3:
			var weekDay = 'Wed';
			break;
		case 4:
			var weekDay = 'Thu';
			break;
		case 5:
			var weekDay = 'Fri';
			break;
		case 6:
			var weekDay = '<font color="#0000ff">Sat</font>';
			break;
		}
		break;
	default:
		var weekDay = now.getDay();
	}

	return weekDay;
}

// 년월 요양사리스트
function __yoyangsaList(p_body, p_code, p_kind, p_year, p_month, p_gubun){
	var URL = 'yoyangsa_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mGubun:p_gubun
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 객체를 리턴한다.
function __getObject(object){
	if (typeof(object) == 'object'){
		var obj = object;
	}else{
		var obj = document.getElementById(object);
	}

	return obj;

	//return (typeof(p_object) == 'object' ? p_object : document.getElementById(p_object));
}

// 로딩중
function __loading(){
	return '<div class="ld_section" style="margin-top:100px;"><img alt=로딩중 src="../image/ico_ld_cen.gif" width="17" height="17"> </div>';
}

// 로딩중
function __loadingBar(){
	return '<div class="ly_loading" style="margin-top:100px;">'
		 + '	<img alt=로딩중 src="http://static.naver.com/common/loading/load_b01_02.gif" width="200" height="20">'
		 + '	<p class="dsc_loading">데이타 로딩중입니다...</p>'
		 + '</div>';
}

// 반올림
// p_value : 반올림할 값
// p_pos : 소수점 자리수
function __round(p_value, p_pos, p_int){
	var value  = parseFloat(p_value);
	var pos    = '';
	var result = 0;

	for(var i=1; i<=parseFloat(p_pos,10); i++){
		pos += '0';
	}
	pos = parseFloat('1' + pos);
	
	if (p_int == true){
		result = Math.round( value / pos) * pos;
	}else{
		result = Math.round(value * pos) / pos;
	}

	return result;
}

// 숫자여부 확인
function __NaN(target){
	var value = (typeof(target) == 'object' ? target.value : target);

	if (isNaN(value)){
		value = 0;
	}else{
		value = (typeof(target) == 'object' ? target.value : target);
	}

	if (!value || value == '') value = 0;

	if (typeof(target) == 'object'){
		target.value = value;
	}else{
		return value;
	}
}

// 현제 주소를 새고고침
function _listReplace(p_loc, p_param){
	location.replace(p_loc+'.php?'+p_param);
}
/*
// 모달폼
function __modal(param){
	modalWindow = showModalDialog('../inc/_modal.php?param='+param, window, 'dialogWidth:1000px; dialogHeight:700px; dialogHide:yes; scroll:no; status:yes');
	
	if (modalWindow == 'Y'){
		_manageSearch();
	}
}
*/

function __my_modal(param, p_body, p_index, p_code, p_kind, p_jumin, p_svcode){
	modalWindow = showModalDialog('../inc/_modal.php?param='+param, window, 'dialogWidth:1000px; dialogHeight:700px; dialogHide:yes; scroll:no; status:yes');

	if (modalWindow == 'Y'){
		var body  = __getObject(p_body);
		var index = p_index;
		var code  = __getObject(p_code);
		var kind  = __getObject(p_kind);
		var jumin = p_jumin;
		var svcode = p_svcode;
		
		if(code == '[object]'){
			code = code.value;
			kind = kind.value;
		}else {
			code = p_code;
			kind = p_kind;
		}

		if(jumin != undefined){
			
			body.innerHTML = '<a href="#" onclick="_member_report_layer(\''+p_body+'\',\''+p_index+'\',\''+code+'\',\''+kind+'\',\''+jumin+'\',\''+svcode+'\');">작성완료</a>';
			//_manageSearch();
		}
	}
}

// 트리 선택
function showTreeMenu(id){
	if (document.getElementById('id_tree_'+id).style.display != 'none'){
		document.getElementById('id_btn_'+id).className = 'toggle plus';
		document.getElementById('id_tree_'+id).style.display = 'none';
	}else{
		document.getElementById('id_btn_'+id).className = 'toggle minus';
		document.getElementById('id_tree_'+id).style.display = '';
	}
}

// 기관 지사 연결 기관 리스트
function __showB2CenterList(){
	var help = showModalDialog('../inc/_help.php?r_gubun=addCenterList', window, 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no');

	if (help == undefined) return;

	switch(help[7]){
	case '0':
		var status = '운영중';
		break;
	case '1':
		var status = '휴업중';
		break;
	case '8':
		var status = '파업중';
		break;
	case '9':
		var status = '폐업';
		break;
	default:
		var status = '-';
	}

	document.getElementById('centerCode').value = help[0];
	document.getElementById('centerKind').value = help[1];
	document.getElementById('idCenterCode').innerHTML = document.getElementById('centerCode').value;
	document.getElementById('idCenterName').innerHTML = help[3];
	document.getElementById('idCenterManager').innerHTML = help[5];
	document.getElementById('idCenterWorkDate').innerHTML = __getDate(help[6]);
	document.getElementById('idCenterStat').innerHTML = status;
	document.getElementById('idCenterAddr').innerHTML = help[8];
}

// 메세지 출력
function __alert(p_target){
	var target = __getObject(p_target);
	var result = true;

	switch(target.className){
	case 'number':
		var value = __NaN(target.value.split(',').join(''));
		
		if (value == 0) result = false;
		break;
	case 'date':
		if (!checkDate(target.value)) result = false;
		break;
	default:
		if (__replace(target.value, ' ', '') == '') result = false;
	}

	if (!result){
		alert(target.tag);
		try{
			target.focus();
		}catch(e){
		}
	}

	return result;
}

// 수급자 찾기
function __find_sugupja(p_code, p_kind, p_jumin, p_name){
	var modal = showModalDialog('../inc/_find_person.php?type=sugupja&code='+p_code+'&kind='+p_kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (modal == undefined){
		return;
	}

	var jumin = __getObject(p_jumin);
	var name  = __getObject(p_name);

	jumin.value = modal[0];
	name.innerHTML = modal[1]+' / '+modal[2]+' / '+modal[3];
}


/* 수급자 찾기 */
/*
report37
성명/인정번호 가져오기
*/
function __find_sugupja2(p_code, p_kind, p_jumin, p_name){
	var modal = showModalDialog('../inc/_find_person.php?type=sugupja&code='+p_code+'&kind='+p_kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (modal == undefined){
		return;
	}

	var jumin = __getObject(p_jumin);
	var name  = __getObject(p_name);

	jumin.value = modal[0];
	name.innerHTML = modal[1]+' / '+modal[4];
}

// 수급자 찾기
function __find_sugupja3(p_code, p_kind, p_jumin, p_name){
	var modal = showModalDialog('../inc/_find_person.php?type=sugupja&code='+p_code+'&kind='+p_kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (modal == undefined){
		return false;
	}

	var jumin = __getObject(p_jumin);
	var name  = __getObject(p_name);

	jumin.value = modal[0];
	name.innerHTML = modal[1];

	return true;
}

// 수급자 찾기
function __find_client(code, target){
	var modal = showModalDialog('../inc/_find_person.php?type=sugupja&code='+code+'&kind=', window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');
		
	if (!modal) return;
	if (!target) return modal;

	for(var i=0; i<target.length; i++){
		if (target[i] != ''){
			var obj = document.getElementById(target[i]);

			try{
				obj.innerHTML = modal[i];
			}catch(e){
				obj.value = modal[i];
			}
		}
	}
}

// 요양보호사 찾기
function __find_yoyangsa(p_code, p_kind, p_jumin, p_name){
	var modal = showModalDialog('../inc/_find_person.php?type=yoyangsa&code='+p_code+'&kind='+p_kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (modal == undefined){
		return false;
	}

	if (typeof(p_jumin) == 'object'){
		var jumin = p_jumin;
	}else{
		var jumin = __getObject(p_jumin);
	}

	if (typeof(p_name) == 'object'){
		var name = p_name;
	}else{
		var name = __getObject(p_name);
	}
	
	//var jumin = __getObject(p_jumin);
	//var name  = __getObject(p_name);

	jumin.value = modal[0];

	try{
		name.innerHTML = modal[1];
	}catch(e){
		name.value = modal[1];
	}

	return true;
}

function __find_member(code, kind, target){
	var modal = showModalDialog('../inc/_find_person.php?type=yoyangsa&code='+code+'&kind='+kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (!modal){
		return null;
	}

	if (!target) return modal;

	for(var i=0; i<target.length; i++){
		if (target[i] != ''){
			var obj = document.getElementById(target[i]);
			
			if (obj == null) break;
			
			try{
				obj.innerHTML = modal[i];
			}catch(e){
				obj.value = modal[i];
			}
		}
	}
}







// 이미지 확장자 여부 확인
function __checkImageExp(p_object){
	var object = __getObject(p_object);

	if (object.value != ''){
		var exp = object.value.split('.');

		if (exp[exp.length-1].toLowerCase() == 'jpg' || exp[exp.length-1].toLowerCase() == 'png' || exp[exp.length-1].toLowerCase() == 'gif' || exp[exp.length-1].toLowerCase() == 'bmp'){
			return true;
		}else{
			alert('jpg, png, gif, bmp의 이미지 파일을 선택하여 주십시오.');
			return false;
		}
	}
}

// 기관등록 ICON, 직인 이미지 확장자 여부 확인
function __checkImageExp(p_object){
	var object = __getObject(p_object);

	if (object.value != ''){
		var exp = object.value.split('.');

		if (exp[exp.length-1].toLowerCase() == 'pdf'){
			return true;
		}else{
			alert('pdf 파일을 선택하여 주십시오.');
			return false;
		}
	}
}

// 로칼 사진 보여주기
function __showLocalImage(p_object, pictureView){
	var object = __getObject(p_object);

	if (!__checkImageExp(object)){
		return;
	}

	pictureView = (typeof(pictureView) == 'object') ? pictureView : document.getElementById('pictureView');
	pictureView.innerHTML = "";

	var ua = window.navigator.userAgent;

	if (ua.indexOf('MSIE') > -1){
		var img_path = '';

		if (object.value.indexOf('\\fakepath\\') < 0){
			img_path = object.value;
		}else{
			object.select();

			var selectionRange = document.selection.createRange();

			img_path = selectionRange.text.toString();

			object.blur();
		}
		pictureView.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='file://"+img_path+"', sizingMethod='scale')";
		pictureView.style.border = '1px solid black';
	}else{
		var W = pictureView.offsetWidth;
		var H = pictureView.offsetHeight;
		var tmpImage = document.createElement("img");

		pictureView.appendChild(tmpImage);

		tmpImage.onerror = function(){
			return pictureView.innerHTML = "";
		}

		tmpImage.onload = function(){
		}

		if (ua.indexOf("Firefox/3") > -1){
			var picData = object.files.item(0).getAsDataURL();

			tmpImage.src = picData;
		}else{
			tmpImage.src = "file://"+object.value;
		}
	}
}


function __getFilePath(obj){
	var ua = window.navigator.userAgent;
	var img_path = "";

	if (ua.indexOf("MSIE") > -1){
		if (obj.value.indexOf("\\fakepath\\") < 0){
			img_path = obj.value;
		}else{
			obj.select();

			var selectionRange = document.selection.createRange();

			img_path = selectionRange.text.toString();

			obj.blur();
		}
	}else{
		if (ua.indexOf("Firefox/3") > -1){
			img_path = obj.files.item(0).getAsDataURL();
		}else{
			img_path = "file://"+obj.value;
		}
	}

	return img_path;
}

function __get_file_path(p_object){
	var object = __getObject(p_object);
	var ua = window.navigator.userAgent;
	var path = '';

	if (ua.indexOf('MSIE') > -1){
		if (object.value.indexOf('\\fakepath\\') < 0){
			path = object.value;
		}else{
			object.select();

			var selectionRange = document.selection.createRange();

			path = selectionRange.text.toString();

			object.blur();
		}
	}else{
		if (ua.indexOf("Firefox/3") > -1){
			path = object.files.item(0).getAsDataURL();
		}else{
			path = "file://"+object.value;
		}
	}

	return path;
}

// div 생성
function __makeDiv(parent, id, display, body){
	var parent = __getObject(parent);
	var div = document.createElement('div');
	
	//div.style.padding  = '0';
	//div.style.margin   = '0';
	//div.style.border   = '0';
	div.style.position = 'absolute';
	div.style.display  = display != undefined ? display : 'none';
	div.style.width	   = 'auto';
	div.id = id;
	div.innerHTML = body;
	
	if (parent) {
		parent.appendChild(div);
	} 
	return div;
}

// div 삭제
function __removeDiv(id){
	var id = __getObject(id);

	id.parentNode.removeChild(id);
}

// 마우스 업다운 이벤트
function __move_on_off(object, check, e){
	var object = __getObject(object);

	if (check == 1){
		coord_x = e.clientX - object.offsetLeft;
		coord_y = e.clientY - object.offsetTop;
	}

	on_off = check;
}


// 마우스 무브 이벤트
function __move(parent, object, e){
	var parent = __getObject(parent);
	var object = __getObject(object);

	var result_x = 0;
	var result_y = 0;
	
	var parent_x = __getObjectLeft(parent);
	var parent_y = __getObjectTop(parent);
	var parent_w = __getObjectLeft(parent) + parent.offsetWidth  - object.offsetWidth;
	var parent_h = __getObjectTop(parent)  + parent.offsetHeight - object.offsetHeight;
	
	if (on_off == 1){
		result_x = e.clientX - coord_x;
		result_y = e.clientY - coord_y;

		if (result_x > 0) object.style.left = result_x;
		if (result_y > 0) object.style.top  = result_y;
		if (result_x < parent_x) object.style.left = parent_x;
		if (result_y < parent_y) object.style.top  = parent_y;
		if (result_x > parent_w) object.style.left = parent_w;
		if (result_y > parent_h) object.style.top  = parent_h;
	}
}

function __hidden_object(p_target){
	var target = __getObject(p_target);
	target.style.display = 'none';
}

// 출력메세지 선택
function __message(p_gubun){
	var gubun = p_gubun.toLowerCase();
	if (gubun == 'ok'){
		alert('정상적으로 처리되었습니다.');
		return true;
	}else if (gubun == 'reset'){
		return confirm('입력하신 데이타를 리셋하시겠습니까?');
	}else if (gubun == 'delete'){
		return confirm('삭제 후 데이타 복구가 불가능합니다. 정말로 삭제하시겠습니까?');
	}else{
		return false;
	}
}

// 시간더하기
function __add_time(p_time, p_addTime){
	var time    = __replace(p_time, ':', '');
	var addTime = p_addTime;
	var hour    = time.substring(0,2);
	var min     = time.substring(2,4);

	if (hour.substring(0,1) == '0') hour = hour.substring(1,hour.length);
	if (min.substring(0,1)  == '0') min  = min.substring(1,min.length);
	
	if (hour == '') hour = '0';
	if (min  == '') min  = '0';

	//if (parseInt(hour) < 10) hour = '0' + parseInt(hour);
	//if (parseInt(min)  < 10) min  = '0' + parseInt(min);

	var newTime = parseInt(hour) * 60 + parseInt(min) + parseInt(addTime);

	hour = Math.floor(newTime / 60);
	min  = newTime % 60;

	hour = (parseInt(hour) < 10 ? '0' : '') + hour;
	min  = (parseInt(min)  < 10 ? '0' : '') + min;
	
	if (parseInt(hour, 10) >= 24){
		//hour = '00';
		hour = parseInt(hour, 10) - 24;
		hour = (parseInt(hour, 10) < 10 ? '0' : '') + parseInt(hour, 10);
	}

	return new Array(hour, min);
}

function __init_form(form){
	var count = form.elements.length;

	for(var i=0; i<count; i++){
		var el = form.elements[i];

		__init_object(el);
	}
}

function __init_object(object){
	var el   = object;
	var type = el.getAttribute('type');
	var alt  = el.getAttribute('alt');
	var c_name = el.className.split(' ')[0];

	if (!el.readOnly){
		if (type == 'text'){
			if (c_name == 'phone'){
				el.style.imeMode = 'disabled';
				el.setAttribute('maxLength', 11);
				el.onfocus = function(){
					__replace(this, '-', '');
					this.style.borderColor='#0e69b0';
				}
				el.onblur = function(){
					__getPhoneNo(this);
					this.style.borderColor='';
				}

				if (el.onkeydown == null){
					el.onkeydown = function(){
						__onlyNumber(this);
					}
				}
			}else if (c_name == 'date' || c_name == 'yymm'){
				el.style.imeMode = 'disabled';
				if (c_name == 'date'){
					el.setAttribute('maxLength', 8);
				}else if (c_name == 'yymm'){
					el.setAttribute('maxLength', 6);
				}

				if (alt == 'not'){
					el.onfocus = function(){
						this.blur();
					}

					el.onclick = null;
					el.style.backgroundColor = '#eee';
					el.style.cursor = 'default';
				}else{
					el.onfocus = function(){
						__replace(this, '-', '');
						this.style.borderColor='#0e69b0';
					}
					el.onblur = function(){
						switch(c_name){
							case 'date':
								__getDate(this);
								break;
							case 'yymm':
								__get_yymm(this);
								break;
						}
						this.style.borderColor='';
					}

					if (el.onkeydown == null){
						el.onkeydown = function(){
							__onlyNumber(this);
						}
					}

					if (el.onclick == null){
						el.onclick = function(){
							try{
								switch(c_name){
									case 'date':
										_carlendar(this);
										break;
									case 'yymm':
										_carlendar_month(this);
										break;
								}
							}catch(e){
							}
						}
					}

					el.style.backgroundColor = '#fff';
					el.style.cursor = 'default';
				}

				switch(c_name){
					case 'date':
						el.setAttribute('maxLength', 8);
						break;
					case 'yymm':
						el.setAttribute('maxLength', 6);
						break;
				}
			}else if (c_name == 'number'){
				el.style.imeMode = 'disabled';
				el.onfocus = function(){
					if (alt == 'not'){
						this.select();
					}else{
						__commaUnset(this);
					}
					this.style.borderColor='#0e69b0';
				}
				el.onblur = function(){
					if (alt == 'not'){
					}else if (alt == 'onblur'){
						eval(el.getAttribute('tag'));
					}else{
						__commaSet(this);
					}
					this.style.borderColor='';

					if (this.value == '') this.value = 0;
				}

				if (el.onkeydown == null){
					el.onkeydown = function(){
						__onlyNumber(this);
					}
				}
			}else if (c_name == 'no_string'){
				el.style.imeMode = 'disabled';
				if (alt == 'time'){
					el.setAttribute('maxLength', 4);
					el.style.width = 40;
				}

				el.onfocus = function(){
					if (alt == 'time'){
						__replace(this, ':', '');
					}else if (alt == 'read'){
						this.blur();
					}else if (alt != ''){
						this.value = __setToNumber(this.value);
						this.select();
					}else{
						this.select();
					}
					this.style.borderColor='#0e69b0';
				}
				el.onblur = function(){
					if (alt == 'time'){
						if (this.value.split(':').join('').length != 4){
							this.value = '';
						}else{
							this.value = __styleTime(this.value);
						}
					}else if (alt != ''){
						eval(alt);
					}
					this.style.borderColor='';
				}

				if (el.onkeydown == null){
					el.onkeydown = function(){
						__onlyNumber(this);
					}
				}
			}else{
				el.onfocus = function(){
					if (alt == 'taxid'){
						__replace(this, '-', '');
					}else{
						this.select();
					}
					this.style.borderColor='#0e69b0';
				}
				el.onblur = function(){
					if (alt == 'taxid'){
						__formatString(this, '###-##-#####');
					}else if (alt != ''){
						eval(alt);
					}else{
					}
					this.style.borderColor='';
				}
				
				if (el.onkeydown == null){
					el.onkeydown = function(){
						__enterFocus();
					}
				}
			}
		}else if (type == 'textarea'){
			el.onfocus = function(){
				this.style.borderColor='#0e69b0';
			}
			el.onblur = function(){
				this.style.borderColor='';
			}
		}else if (type == 'password'){
			el.onfocus = function(){
				this.style.borderColor='#0e69b0';
			}
			el.onblur = function(){
				this.style.borderColor='';
			}
		}
	}else{
		el.onfocus = function(){
			this.blur();
		}

		if (alt == 'hand'){
			el.style.backgroundColor = '#eee';
			el.style.cursor = 'pointer';
		}else{
			el.onclick = null;
			el.onblur = null;

			if (alt != 'not'){
				el.style.backgroundColor = '#eee';
				el.style.cursor = 'default';
			}
		}
	}
}

/*
 * 숫자로 리턴
 */
function __str2num(str){
	if (!str) str = 0;
	
	if (typeof(str) != 'number'){
		str = __commaUnset(str);
	}
	str = __NaN(str);

	return parseFloat(str, 10);
}

/*
 * 문자로 리턴
 */
function __num2str(num){
	if (typeof(num) != 'number'){
		num = __commaUnset(num);
	}

	if (!num) num = 0;

	num = __NaN(num);
	num = __commaSet(num);
	
	return num;
}

/*
 * 
 */
function __false(){
	return false;
}

/*
 * 말일
 */
function __set_lastday(target){
	try{
		var y = document.getElementById(target+'_y');
		var m = document.getElementById(target+'_m');
		var d = document.getElementById(target+'_d');

		var date = y.value + '-' + m.value + '-01';
		var last = getDay(addDate('d', -1, addDate('m', 1, date)));

		for(var i=0; i<d.options.length; i++){
			if (parseInt(d.options[i].text, 10) <= last){
				d.options[i].disabled = false;
			}else{
				d.options[i].disabled = true;
			}
		}

		if (d.selectedIndex > parseInt(last, 10) - 1){
			d.options[parseInt(last, 10) - 1].selected = true;
		}
	}catch(e){
	}
}

/*
 * 새로고침 방지
 */
function __not_reload(evt){
    var e = evt;

    if(e==null) e = window.event;
    if( (e.ctrlKey == true && (e.keyCode == 78 || e.keyCode == 82)) || (e.keyCode == 116) ){
        if(window.netscape) // FireFox
        {
            e.preventDefault();
        }
        else if(navigator.appName=='Netscape') // Safari
        {
            e.preventDefault();
        }
        else if(navigator.appName=='Opera') // Opera
        {
            e.preventDefault();
        }
        else // IE
        {
            e.keyCode      = 0;
            e.cancelBubble = true;
            e.returnValue  = false;
            //alert('새로고침 방지');
        }
    }
}

function __go_menu(menu, uri){
	__setCookie('__left_menu__', menu, 1);

	if (typeof(uri) != 'string'){
		if (menu == ''){
			location.href = '../index.html';
		}else if (menu == 'center'){
			location.href = '../center/list.php';
		}else if (menu == 'iljung'){
			location.href = '../iljung/iljung_list.php?mode=1';
		}else if (menu == 'work'){
			//location.href = '../iljung/month.php?gubun=day';
			location.href = '../iljung/iljung_result.php?gubun=client';
		}else if (menu == 'claim'){
			location.href = '../claim/public_amt_list.php';
		}else if (menu == 'salary'){
			//location.href = '../salary/payroll.php';
			location.href = '../salaryNew/salary_edit_list.php';
		}else if (menu == 'account'){
			location.href = '../account/income_list.php?io_type=i&mode=1';
		}else if (menu == 'other'){
			//location.href = '../goodeos/notice_list.php';
			location.href = '../goodeos/board_list.php?board_type=noti';
		}else if (menu == 'report'){
			location.href = '../reportMenu/report.php?report_menu=10';
		}else if (menu == 'help'){
			location.href = '../help/help.php';
		}else if (menu == 'branch'){
			location.href='../branch/branch_list.php?mode=branch';
		}else if (menu == 'goodeos'){
			location.href='../goodeos/notice_list.php';
		}else if (menu == 'proc'){
			location.href='../work/work_real.php';
		}else if (menu == 'store'){
			location.href='../branch/branch_list.php?mode=store';
		}else if (menu == 'company'){
			location.href='../branch/branch_reg.php?mode=company';
		}else if (menu == 'eval_data'){
			location.href='../eval_data/eval_data.php';
		}
	}else{
		location.href = uri;
	}
}

// 벨류값의 객체를 선택한다.
function __object_checked(target, value){
	if (typeof(target) != 'object'){
		var target = document.getElementsByName(target);
	}

	for(var i=0; i<target.length; i++){
		if (target[i].value == value){
			target[i].checked = true;
			break;
		}
	}
}

// 벨류값 객체의 인덱스를 리턴한다.
function __object_index(target, value){
	if (typeof(target) != 'object'){
		var target = document.getElementsByName(target);
	}

	var index = 0;

	for(var i=0; i<target.length; i++){
		if (target[i].type != 'hidden'){
			if (target[i].value == value){
				break;
			}
			index++;
		}
	}

	return index;
}

// 객체가 선택되어있는지 확인한다.
function __object_is_checked(target){
	if (typeof(target) != 'object'){
		var target = document.getElementsByName(target);
	}

	var is_checked = false;

	for(var i=0; i<target.length; i++){
		if (target[i].checked){
			is_checked = true;
			break;
		}
	}

	return is_checked;
}

// 객체의 벨류값을 가져온다.
function __object_get_value(target, opener){
	if (typeof(target) != 'object'){
		if (typeof(opener) == 'object'){
			var target = opener.document.getElementsByName(target);
		}else{
			var target = document.getElementsByName(target);
		}
	}

	var value = '';

	for(var i=0; i<target.length; i++){
		if (target[i].checked){
			value = target[i].value;
			break;
		}
	}

	return value;
}

// 객체의 벨류값을 체크한다.
function __object_set_value(target, value, opener, checked){
	if (typeof(target) != 'object'){
		if (typeof(opener) == 'object'){
			var obj = opener.document.getElementsByName(target);
		}else{
			var obj = document.getElementsByName(target);
		}
	}

	for(var i=0; i<obj.length; i++){
		if (obj[i].value == value){
			if (obj[i].type == 'radio'){
				var chk_is = true;
			}else{
				var chk_is = (checked ? checked : !obj[i].checked);
			}
		
			obj[i].checked = chk_is;
			break;
		}
	}
}

// 선택되어진 객체를 리턴한다.
function __object_check(target){
	if (typeof(target) != 'object'){
		var target = document.getElementsByName(target);
	}

	var rst = null;

	for(var i=0; i<target.length; i++){
		if (target[i].checked){
			rst = target[i];
			break;
		}
	}

	if (rst == null) rst = target;

	return rst;
}

// 객체의 maxlength
function __object_get_maxlength(target){
	if (typeof(target) != 'object'){
		var target = document.getElementById(target);

		if (target == null){
			target = document.getElementsByName(target);
		}
	}

	if (target == null) return 0;

	return target.getAttribute('maxLength');
}

// 입력길이 확인
function __check_max_length(target){
	if (typeof(target) != 'object'){
		var target = document.getElementById(target);

		if (target == null){
			target = document.getElementsByName(target);
		}
	}

	if (target == null) return true;

	var maxlen = __object_get_maxlength(target);

	if (target.value.length == maxlen){
		var result = true;
	}else{
		var result = false;
	}

	return result;
}

// yyyy-mm 형식으로 리턴한다.
function __get_yymm(target){
	var result = '';

	if (typeof(target) == 'object'){
		var date = target.value;
	}else{
		var date = target;
	}

	if (date.length == 6){
		var yy = date.substring(0, 4);
		var mm = date.substring(4, 6);

		if (parseInt(mm, 10) >= 1 && parseInt(mm, 10) <= 12){
			result = yy + '-' + mm;
		}
	}

	if (typeof(target) == 'object'){
		target.value = result;
	}else{
		return result;
	}
}

// 서비스 코드
function __get_svc_code(obj){
	if (typeof(obj) != 'object') obj = document.getElementById(obj);

	var obj_id = obj.name.substring(obj.name.length - 2, obj.name.length);

	if (isNaN(parseInt(obj_id))){
		obj_id = obj.name.substring(0, 2);
	}

	return obj_id;
}

// 객체의 속성 읽기
function __object_get_att(obj, att){
	if (typeof(obj) != 'object'){
		var obj = document.getElementsByName(obj);
	}

	if (obj == null) return null;

	var val = null;

	for(var i=0; i<obj.length; i++){
		if (obj[i].checked || obj[i].selected){
			val = obj[i].getAttribute(att);
			break;
		}
	}
	
	return val;
}

// Ajax로 데이타 가져오기
function __get_url_data(url, svc, parms, exec){
	var URL = url;
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:parms,
			onSuccess:function (responseHttpObj) {
				eval(exec+'(\''+svc+'\',\''+responseHttpObj.responseText+'\')');
			}
		}
	);
}

// 자리수 맟추기
function __object_set_format(object, type, len, str){
	var target = 'object';

	try{
		if (typeof(obj) != 'object'){
			var obj = document.getElementById(object);
		}
	}catch(e){
		var target = 'string';
		var obj = object;
	}

	if (obj == null){
		var target = 'string';
		var obj = object;
	}

	if (isNaN(len)) len = obj.value.length;
	if (str == undefined) str = ' ';

	var rst = '';

	if (type == 'number'){
		for(var i=0; i<len; i++){
			rst += '0';
		}
	}else if (type == 'string'){
		for(var i=0; i<len; i++){
			rst += str;
		}
	}else{
	}
	
	if (target == 'object')
		rst += obj.value;
	else
		rst += obj;


	rst  = rst.substring(rst.length - len, rst.length);
	
	if (target == 'object')
		obj.value = rst;

	return rst;
}

//산성시간
function __com_time(time){
	return Math.round(time / 60);
}

//화면크기설정
function __window_resize(w, h, gbn){
	var x = (screen.width - w) / 2;
	var y = (screen.height - h) / 2;

	window.resizeTo(w, h);

	if (parseInt(gbn, 10) == 1)
		window.moveTo(x, y);
}

//메세지 출력
function __show_error(e, win){
	if (typeof(win) != 'object') win = window;

	if (e instanceof Error){
		win.alert('System Error : ' + e.description);
	}else if (typeof(e) == 'string'){
		win.alert('String Error : ' + e.description);
	}else{
		win.alert('Error Number : '+e.number+'\n Description : '+e.description);
	}
}

function __object_enabled(target, enabled){
	try{
		if (enabled){
			target.disabled = false;

			if (target.className == 'checkbox' || target.className == 'radio'){
			}else{
				target.style.backgroundColor = "#ffffff";
			}
		}else{
			target.disabled = true;

			if (target.className == 'checkbox' || target.className == 'radio'){
			}else{
				target.style.backgroundColor = "#eeeeee";
			}
		}
	}catch(e){
		__show_error(e);
	}
}



/*********************************************************

	로딩화면

*********************************************************/
function __show_loading(my_body){
	var height = document.body.clientHeight;
	var top    = (height - 40) / 2;
	var body   = document.getElementById(my_body);
		body.style.paddingTop = top;
		body.innerHTML = '<center><div id=\'my_load\' class=\'ly_loading\'><p class=\'dsc_loading\'>문서를 불러오는 중...</p><img alt=로딩중 src=\'../common/img/load_b01_02.gif\' width=\'200\' height=\'20\'></div></center>';
}

function __get_loading(){
	return '<center><div class=\'ly_loading\' style=\'\'><p class=\'dsc_loading\'>문서를 불러오는 중...</p><img alt=로딩중 src=\'http://www.carevisit.net/common/img/load_b01_02.gif\' width=\'200\' height=\'20\'>';
}



/**************************************************

	직원정보

**************************************************/
	function __find_member_if(code, target){
		var modal = showModalDialog('../inc/_find_person.php?type=member&code='+code, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

		if (!modal){
			return null;
		}
		
		if (!target) return modal;
		
		for(var i in target){
			try{
				document.getElementById(target[i]).innerHTML = modal[i];
			}catch(e){
				document.getElementById(target[i]).value = modal[i];
			}
		}
	}

/*************************************************/





//직무평가 체크박스 하나만선택
function __check_only(chk, names) {
  var obj = names;
  for(var i=0; i<obj.length; i++) {
	 if (obj[i] != chk) {
		obj[i].checked = false;
	 }
  }
}



/*********************************************************

	Ajax 호출 에러

*********************************************************/
function __ajax_error(){
	alert('error');
}



/*********************************************************

	Ajax 호출 실패

*********************************************************/
function __ajax_failure(){
	alert('false');
}


/*********************************************************

	create object

*********************************************************/
function __create_input(name, value){
	var obj = document.createElement('input');
		obj.setAttribute('type', 'hidden');
		obj.setAttribute('id', name);
		obj.setAttribute('name', name);
		obj.setAttribute('value', value);

	return obj;
}



/*********************************************************

	find client

*********************************************************/
function __findClient(code,kind,para){
	var objModal = new Object();
	var url      = '../find/_find_client.php';
	var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

	var year = '', month = '';

	try{
		var val = __parseStr(para);

		year  = val['year'];
		month = val['month'];
	}catch(e){
	}
	
	objModal.code  = (code ? code : '');
	objModal.kind  = (kind ? kind : '');
	objModal.year  = year;
	objModal.month = month;

	window.showModalDialog(url, objModal, style);

	var result = objModal.para;	

	if (!result) return;

	return result;
	
	/*
	var arr = result.split('&'); 
	var val = new Array();

	for(var i=0; i<arr.length; i++){
		var tmp = arr[i].split('=');

		val[tmp[0]] = tmp[1];
	}

	return 'name='+val['name']+'&jumin='+val['jumin']+'&app_no='+val['app_no'];
	*/
}



/*********************************************************

	find member

*********************************************************/
function __findMember(code,kind){
	var result = showModalDialog('../inc/_find_person.php?type=member&code='+code+'&kind='+kind, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (!result){
		return null;
	}

	return result;
}



/*********************************************************

	find suga

*********************************************************/
function __findSuga(code,svcCD,date,over270YN){
	var objModal = new Object();
	var url      = '../find/_find_suga.php';
	var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

	objModal.code      = (code ? code : '');
	objModal.svcCD     = (svcCD ? svcCD : '');
	objModal.date      = (date ? date : '');
	objModal.over270YN = (over270YN ? over270YN : 'Y')

	window.showModalDialog(url, objModal, style);

	var result = objModal.para;	

	if (!result) return;

	return result
}


/*********************************************************

	get suga info

*********************************************************/
function __getSugaInfo(code, sugaCode, stndDate){
	return getHttpRequest('../find/_find_suga_info.php?code='+code+'&sugaCD='+sugaCode+'&stndDT='+stndDate);
}



/*********************************************************

	pdf print

*********************************************************/
function __printPDF(arguments){
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	
	if (winModal != null){
		try{
			winModal.close();
		}catch(e){
		}
	}

	winModal = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	
	window.onunload = function(){
	//	win.close();
	}

	var pdf = document.createElement('form');
	
	pdf.appendChild(__create_input('arguments', arguments));

	pdf.setAttribute('method', 'post');
	
	document.body.appendChild(pdf);

	pdf.target = 'SHOW_PDF';
	pdf.action = '../showPdf/';
	pdf.submit();
}


function __request_print(){
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	
	if (winModal != null){
		try{
			winModal.close();
		}catch(e){
		}
	}

	winModal = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	
	window.onunload = function(){
	//	win.close();
	}
}


/*********************************************************

	년도이동 공통함수

*********************************************************/
function __moveYear(pos){

}



/*********************************************************

	특정문자 자른 뒤 문자열

*********************************************************/
function __getSplitStr(str, val){
	var rst = '';

	if (str.toString().substring(0, val.toString().length) == val.toString()){
		rst = str.toString().substring(val.toString().length,str.toString().length);
	}

	return rst;
}


/*********************************************************

	활성화 여부

*********************************************************/
function __getObjectValue(obj){
	if (!$(obj).attr('disabled')){
		if ($(obj).hasClass('number')){
			$val = __str2num($(obj).attr('value'));
		}else{
			$val = $(obj).attr('value');
		}
	}else{
		if ($(obj).hasClass('number')){
			$val = 0;
		}else{
			$val = '';
		}
	}
	
	return $val;
}


/*********************************************************

	메일 형식 여부

*********************************************************/
function __isMail(mail){
	var mail_pattern = /^(\S+)\@(\S+)\.(\S+)$/;

	return mail_pattern.test(mail)
}


/*********************************************************

	건보로그인

*********************************************************/
function __longcareLogin(code){
	if (code == '32729000223' ||
		/*code == '32714000222' ||*/
		code == '1138000167'){
		var cont   = $('#divLongcareCont');
		var result = getHttpRequest('http://www.longtermcare.or.kr/portal/site/nydev/');

		if ($('.welcome',result).html()){
			/*********************************************************
				로그인인증
			*********************************************************/
			if ($(cont).css('display') != 'none'){
				__longcareHide();
			}else{
				alert('이미 로그인되어 있습니다.');
			}
		}else{
			$(cont).load('../longcare/longcare_login_check.php?code='+code, function(){
				try{
					l = $('#left_box').width() + 10;
					t = $('#left_box').offset().top + 10; 
				}catch(e){
					l = 90;
					t = 165;
				}

				$(cont).css('left', l).css('top', t).css('height', 500).css('padding-top', '150px').css('line-height', '35px').show();
			});
		}
	}else{
		var body = $('#divLongcareBody');
		var cont = $('#divLongcareCont');

		var w  = $(document).width();
		var h  = $(document).height();
		var l  = 0, t = 0;
		var dt = new Date();
		var today = dt.getFullYear()+'-'
				  + ((dt.getMonth()+1 < 10 ? '0' : '')+(dt.getMonth()+1))+'-'
				  + (dt.getDate() < 10 ? '0' : '')+dt.getDate();

		try{
			$.ajax({
				type: 'POST',
				url : 'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify',
				data: {
					'pageIndex'   : 1
				,	'serviceKind' : ''
				,	'searchFrDt'  : today
				,	'searchToDt'  : today
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					if ($('.npaging', xmlHttp).html()){
						/*********************************************************
							로그인인증
						*********************************************************/
						if ($(cont).css('display') != 'none'){
							__longcareHide();
						}else{
							$.ajax({
								type: 'GET',
								url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
								success: function (data){
									if ($('.welcome',data).html()){
										/*********************************************************
											로그인 되어 있음.
										*********************************************************/
										alert('이미 로그인되어 있습니다.');
									}else{
										/*********************************************************
											로그인 실행
										*********************************************************/
										$(cont).load('../longcare/longcare_login_check.php?code='+code, function(){
											try{
												l = $('#left_box').width() + 10;
												t = $('#left_box').offset().top + 10; 
											}catch(e){
												l = 90;
												t = 165;
											}

											$(cont).css('left', l).css('top', t).css('height', 500).css('padding-top', '150px').css('line-height', '35px').show();
										});
									}
								}
							});
						}
					}else{
						body.css('width', w).css('height', h).show();

						try{
							$.ajax({
								type: 'GET',
								url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
								success: function (data){
									if ($('.welcome',data).html()){
										/*********************************************************
											최초홈페이지 열기
										*********************************************************/
										$(cont).load('../longcare/longcare_open_check.php', function(){
											try{
												l = $('#left_box').width() + 10;
												t = $('#left_box').offset().top + 10; 
											}catch(e){
												l = 90;
												t = 165;
											}

											$(cont).css('left', l).css('top', t).css('height', 500).css('padding-top', '150px').css('line-height', '35px').show();
										});
									}else{
										/*********************************************************
											로그인 실행
										*********************************************************/
										$(cont).load('../longcare/longcare_login_check.php?code='+code, function(){
											try{
												l = $('#left_box').width() + 10;
												t = $('#left_box').offset().top + 10; 
											}catch(e){
												l = 90;
												t = 165;
											}

											$(cont).css('left', l).css('top', t).css('height', 500).css('padding-top', '150px').css('line-height', '35px').show();
										});
									}
								}
							});
						}catch(e){
							/*********************************************************
								권한이 없음.
							*********************************************************/
							body.css('width', w).css('height', h).show();

							$(cont).load('../longcare/longcare_conn.php', function(){
								l = $('#left_box').width() + 10;
								t = $('#left_box').offset().top + 10; 

								$(cont).css('left', l).css('top', t).show();
							});
						}
					}
				},
				complete: function(){
				},
				error: function (){
					alert('건보공단 홈페이지에 접속할 수 없습니다.\n\n잠시후 다시 시도하여 주십시오.');
				}
			}).responseXML;
		}catch(e){
			/*********************************************************
				권한이 없음.
			*********************************************************/
			body.css('width', w).css('height', h).show();

			$(cont).load('../longcare/longcare_conn.php', function(){
				l = $('#left_box').width() + 10;
				t = $('#left_box').offset().top + 10; 

				$(cont).css('left', l).css('top', t).show();
			});
		}
	}
}

function __longcareHide(){
	$('#divLongcareBody').hide();
	$('#divLongcareCont').hide();
}

function __popupHide(){
	$('#divPopupBody').hide();
	$('#divPopupLayer').hide();
}



/*********************************************************

	path

*********************************************************/
function __getPathName(){
	var pathName = location.pathname.split('/');
		pathName = pathName[pathName.length - 1].split('.');
		pathName = pathName[0];

	return pathName;
}



/*********************************************************

	

*********************************************************/
function __parseStr(str){
	var arr = str.split('&'); 
	var val = new Array();

	for(var i=0; i<arr.length; i++){
		var tmp = arr[i].split('=');

		val[tmp[0]] = tmp[1];
	}

	return val;
}



/*********************************************************

	시간여부 확인

*********************************************************/
function __checkIsTime(obj){
	if ($(obj).attr('tagName') == 'INPUT'){
		var val = $(obj).val();
	}else{
		var val = $(obj).text();
	}
	
	val = val.split(':').join('');

	var str = '';

	for(var i=4; i>val.length; i--){
		str += '0';
	}

	val = str+val;
	val = val.substring(0,2) + ':' + val.substring(2,4);

	if ($(obj).attr('tagName') == 'INPUT'){
		$(obj).val(val);
	}else{
		$(obj).text(val);
	}

	return checkDate(val);
}


/*********************************************************

	파일 업로드 콜백함수 설정

*********************************************************/
function __fileUploadInit(obj, fun){
	if (!fun) fun = '__fileUploadCallback';

	var frm = obj;
		frm.ajaxForm(eval(fun));
		frm.submit(function(){
			return false;
		});
}



/*********************************************************

	파일 업로드 콜백함수

*********************************************************/
function __fileUploadCallback(data, state){
	$('#tempLodingBar').remove();

	if (data == 1){
		return true;
	}else{
		return false;
	}
}



/*********************************************************

	등급명칭

*********************************************************/
function __lvlNm(as_lvlCd){
	var lvlNm = '';

	switch(as_lvlCd){
		case '1': lvlNm = '1등급'; break;
		case '2': lvlNm = '2등급'; break;
		case '3': lvlNm = '3등급'; break;
		default: lvlNm = '일반';
	}

	return lvlNm;
}


/*********************************************************

	수급자구분 명칭

*********************************************************/
function __kindNm(as_kindCd){
	var kindNm = '';

	switch(as_kindCd){
		case '3': kindNm = '기초수급권자'; break;
		case '2': kindNm = '의료수급권자'; break;
		case '4': kindNm = '경감대상자'; break;
		default: kindNm = '일반';
	}

	return kindNm;
}


/*********************************************************
 * 시간 -> 분
 *********************************************************/
function __time2min(asTime){
	var lsTime = asTime;
		lsTime = lsTime.split(':').join('');

	if (lsTime.length != 4){
		return asTime;
	}

	var liH = __str2num(lsTime.substring(0,2));
	var liM = __str2num(lsTime.substring(2,4));
	var liT = liH * 60 + liM;

	return liT;
}

/*********************************************************
 * 분 -> 시간
 *********************************************************/
function __min2time(aiMin){
	var liMin = __str2num(aiMin);
	var liH = Math.floor(liMin / 60);
	var liM = liMin % 60;
	var lsT = '';

	if (liH > 0){
		lsT += liH+'H';
	}

	if (liM > 0){
		lsT += liM+'M';
	}

	return lsT;
}
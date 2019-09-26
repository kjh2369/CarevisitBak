var modalWindow = null; //모달창

// -----------------------------------------------------------------------------
// 센터등록
function _centerReg(gubun,mCode,mKind,code1,cName){
	var page = null;
	var searchMcode = null;
	var searchMkind = null;
	var searchCode1 = null;
	var searchCname = null;

	try{
		page = document.center.page.value;

		if (gubun == 'reg'){
		}else{
			searchMcode = document.center.searchMcode.value;
			searchMkind = document.center.searchMkind.value;
			searchCode1 = document.center.searchCode1.value;
			searchCname = document.center.searchCname.value;
		}
	}catch(e){
	}

	var URL = '../center/center_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				code1:code1,
				cName:cName,
				searchMcode:searchMcode,
				searchMkind:searchMkind,
				searchCode1:searchCode1,
				searchCname:searchCname,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 센터리스트
function _centerList(page){
	var gubun = 'search';
	var mCode = null;
	var mKind = null;
	var code1 = null;
	var cName = null;

	try{
		mCode = document.center.searchMcode.value;
		mKind = document.center.searchMkind.value;
		code1 = document.center.searchCode1.value;
		cName = document.center.searchCname.value;
	}catch(e){
	}
	var URL = '../center/center_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				code1:code1,
				cName:cName,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 센터저장
function _centerSave(){
	if (__replace(document.center.mCode.value, ' ', '') == ''){
		alert('대표센터코드를 입력하여 주십시오.');
		document.center.mCode.focus();
		return;
	}

	if (__replace(document.center.code1.value, ' ', '') == ''){
		alert('승인번호를 입력하여 주십시오.');
		document.center.code1.focus();
		return;
	}

	if (__replace(document.center.cName.value, ' ', '') == ''){
		alert('기관명을 입력하여 주십시오.');
		document.center.cName.focus();
		return;
	}

	if (!checkDate(document.center.contDate.value)){
		alert('업무시작일자를 입력하여 주십시오.');
		document.center.contDate.focus();
		return;
	}

	if (document.center.insName.value != ''){
		if (document.center.insFromDate.value == ''){
			alert('보험 가입기간을 입력하여 주십시오.');
			document.center.insFromDate.focus();
			return;
		}
	//}else{
	//	alert('보험사를 선택하여 주십시오.');
	//	document.center.insName.focus();
	//	return;
	}

	document.center.submit();
}

// 센터삭제
function _centerDelete(){
	alert('test');
	return false;
}
// -----------------------------------------------------------------------------
// 직원등록
function _centerYoyReg(gubun,mCode,mKind,code1,cName,yKey,jName,cTel){
	var page = null;
	var searchMcode = null;
	var searchMkind = null;
	var searchCode1 = null;
	var searchCname = null;
	var searchJname = null;
	var searchCtel = null;

	try{
		page = document.center.page.value;

		if (gubun == 'reg'){
		}else{
			searchMcode = document.center.curMcode.value;
			searchMkind = document.center.curMkind.value;
			searchCode1 = document.center.curCode1.value;
			searchCname = document.center.curCname.value;
			searchJname = document.center.curJname.value;
			searchCtel  = document.center.curTel.value;
		}
	}catch(e){
	}

	var URL = '../yoyangsa/yoyangsa_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				code1:code1,
				cName:cName,
				jName:jName,
				cTel:cTel,
				key:yKey,
				searchMcode:searchMcode,
				searchMkind:searchMkind,
				searchCode1:searchCode1,
				searchCname:searchCname,
				searchJname:searchJname,
				searchCtel:searchCtel,
				currentMkind:mKind,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
				_setInsData();
			}
		}
	);
}

// 직원리스트
function _centerYoyList(page){
	if (page == undefined){
		try{
			document.center.searchMcode.value = '';
			document.center.searchMkind.value = '';
			document.center.searchCode1.value = '';
			document.center.searchCname.value = '';
			document.center.searchJname.value = '';
			document.center.searchTel.value = '';
		}catch(e){
		}
	}

	var gubun = 'search';
	var mCode = null;
	var mKind = null;
	var code1 = null;
	var cName = null;
	var jName = null;
	var cTel = null;
	var currentMkind = null;
	var stat = null;

	try{
		mCode = document.center.searchMcode.value;
		mKind = document.center.searchMkind.value;
		code1 = document.center.searchCode1.value;
		cName = document.center.searchCname.value;
		jName = document.center.searchJname.value;
		cTel  = document.center.searchTel.value;
		stat = document.center.searchStat.value;

		currentMkind = document.center.currentMkind.value;
	}catch(e){
	}

	var URL = '../yoyangsa/yoyangsa_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				code1:code1,
				cName:cName,
				jName:jName,
				cTel:cTel,
				stat:stat,
				currentMkind:currentMkind,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 직원조회
function _yoyangsaSearch(){
	alert('test');
}

// 직원저장
function _yoyangsaSave(){
	if (document.center.editMode.value == true){
		if (!_checkSSN('yoy', document.center.yJumin1, document.center.yJumin2, document.center.curMcode, document.center.curMkind)){
			alert('주민번호를 입력하여 주십시오.');
			return;
		}
	}

	if (document.center.yName.value.split(' ').join('') == ''){
		alert('요양보호사명을 입력하여 주십시오.');
		document.center.yName.focus();
		return;
	}

	if (document.center.yTel.value.split(' ').join('') == ''){
		alert('핸드폰번호를 입력하여 주십시오.');
		document.center.yTel.focus();
		return;
	}

	var payType = getPayType();

	switch(payType){
	case '1':
		var object = document.getElementsByName('yGibonKup[]');
			
		for(var i=0; i<object.length; i++){
			if (parseInt(__commaUnset(object[i].value)) < 1){
				alert('시급을 입력하여 주십시오.');
				object[i].focus();
				return;
			}
		}
		break;
	case '2':
		if (parseInt(__commaUnset(document.center.yGibonKup2.value)) < 1){
			alert('시급을 입력하여 주십시오.');
			document.center.yGibonKup2.focus();
			return;
		}
		break;
	case '3':
		if (parseInt(__commaUnset(document.center.yGibonKup3.value)) < 1){
			alert('기본급을 입력하여 주십시오.');
			document.center.yGibonKup3.focus();
			return;
		}
		break;
	case '4':
		if (parseInt(__commaUnset(document.center.ySugaYoyul.value)) < 1){
			alert('수가총액비 요율을 입력하여 주십시오.');
			document.center.ySugaYoyul.focus();
			return;
		}
		break;
	}

	// 친족케어유무
	if (document.getElementById('yFamCareUmu').value == 'Y'){
		if (parseInt(__commaUnset(document.getElementById('yFamCarePay').value), 10) == 0){
			alert('친족케어시급을 입력하여 주십시오.');
			document.getElementById('yFamCarePay').focus();
			return;
		}
	}

	// 입사일자
	if (!checkDate(document.center.yIpsail.value)){
		alert('입사일자를 입력하여 주십시오.');
		document.center.yIpsail.focus();
		return;
	}

	// 퇴사여부
	if (!checkCenterOut()){
		return;
	}

	// 배상책임보험 가입여부
	if (document.getElementById('insYN').value == 'Y'){
		if (!checkDate(document.getElementById('insFromDate').value)){
			alert('배상책임보험 가입기간을 입력하여 주십시오.');
			document.getElementById('insFromDate').focus();
			return;
		}
	}else{
		if (document.getElementById('insYN').tag == 'Y'){
			if (!confirm('선택하신 요양보호사의 배상책임보험을 해지신청합니다. 계속 진행하시겠습니까?')){
				return;
			}
		}
	}

	// 국민연금 신고 월급여액
	if (parseInt(__commaUnset(document.center.yKuksinMpay.value)) < 1){
		alert('국민연금 신고 월급여액을 입력하여 주십시오.');
		document.center.yKuksinMpay.focus();
		return;
	}

	document.center.submit();
}

// 직원삭제
function _yoyangsaDelete(){
	if (!confirm('삭제후 일정 데이타 및 요양사 데이타의 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')){
		return;
	}

	document.center.action = 'delete.php';
	document.center.submit();
}
// -----------------------------------------------------------------------------
// 수급자등록
function _sugupjaReg(gubun,mCode,mKind,mJumin,jName,cTel,mKey){
	var page = null;
	var searchMcode = null;
	var searchMkind = null;
	var searchCode1 = null;
	var searchCname = null;
	var searchJname = null;
	var searchCtel = null;
	
	try{
		page = document.center.page.value;

		if (gubun == 'reg'){
		}else{
			searchMcode = document.center.curMcode.value;
			searchMkind = document.center.curMkind.value;
			searchCode1 = document.center.curCode1.value;
			searchCname = document.center.curCname.value;
			searchJname = document.center.curJname.value;
			searchCtel  = document.center.curTel.value;
		}
	}catch(e){
	}

	var URL = '../sugupja/sugupja_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				mJumin:mJumin,
				currentMkind:mKind,
				jName:jName,
				cTel:cTel,
				mKey:mKey,
				searchMcode:searchMcode,
				searchMkind:searchMkind,
				searchCode1:searchCode1,
				searchCname:searchCname,
				searchJname:searchJname,
				searchCtel:searchCtel,
				currentMkind:mKind,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급자리스트
function _sugupjaList(page){
	if (page == undefined){
		try{
			document.center.searchMcode.value = '';
			document.center.searchMkind.value = '';
			document.center.searchCode1.value = '';
			document.center.searchCname.value = '';
			document.center.searchJname.value = '';
			document.center.searchTel.value = '';
		}catch(e){
		}
	}

	var gubun = 'search';
	var mCode = null;
	var mKind = null;
	var code1 = null;
	var cName = null;
	var jName = null;
	var cTel = null;
	var stat = null;
	var currentMkind = null;

	try{
		mCode = document.center.searchMcode.value;
		mKind = document.center.searchMkind.value;
		code1 = document.center.searchCode1.value;
		cName = document.center.searchCname.value;
		jName = document.center.searchJname.value;
		cTel = document.center.searchTel.value;
		stat = document.center.searchStat.value;

		currentMkind = document.center.currentMkind.value;
	//	mCode = document.center.curMcode.value;
	//	mKind = document.center.curMkind.value;
	//	code1 = document.center.curCode1.value;
	//	cName = document.center.curCname.value;
	}catch(e){
	}

	var URL = '../sugupja/sugupja_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				gubun:gubun,
				mCode:mCode,
				mKind:mKind,
				code1:code1,
				cName:cName,
				jName:jName,
				cTel:cTel,
				stat:stat,
				currentMkind:currentMkind,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				center_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수급자저장
function _sugupjaSave(){
	if (document.center.sDate.value.split('.').join('') < document.center.sDate.tag){
		alert('적용시작일은 현재의 적용시작일보다 커야 합니다. 확인하여 주십시오.');
		document.center.sDate.focus();
		return;
	}
	
	if (document.center.sDate.value.split('.').join('') == document.center.sDate.tag){
		document.center.historys.value = 'N';
	}
	
	if (document.center.editMode.value == true){
		if (!_checkSSN('su', document.center.jumin1, document.center.jumin2, document.center.curMcode, document.center.curMkind)){
			alert('주민번호가 올바르지 않습니다. 확인 후 다시 입력하여 주십시오.');
			return;
		}
	}
	
	if (document.center.name.value.split(' ').join('') == ''){
		alert('수급자명을 입력하여 주십시오.');
		document.center.name.focus();
		return;
	}

	if (document.center.tel.value.split(' ').join('') == '' && document.center.hp.value.split(' ').join('') == ''){
		alert('전화번호 및 휴대폰중 하나이상 입력하여 주십시오.');
		document.center.tel.focus();
		return;
	}

	if (document.center.postNo1.value == '' && document.center.postNo2.value == ''){
		alert('수급자의 주소를 입력하여 주십시오.');
		return;
	}

	if (document.center.gaeYakFm.value.length != 10){
		alert('계약시작일자를 입력하여 주십시오.');
		document.center.gaeYakFm.focus();
		return;
	}

	if (document.center.gaeYakTo.value.length != 10){
		alert('계약종료일자를 입력하여 주십시오.');
		document.center.gaeYakTo.focus();
		return;
	}

	if (document.center.gaeYakFm.value >= document.center.gaeYakTo.value){
		alert('계약종료일자는 시작일자보다 커야합니다. 확인하여 주십시오.');
		document.center.gaeYakTo.focus();
		return;
	}

	document.center.submit();
}

// -----------------------------------------------------------------------------

function _exist(p_code){
	var code = __getObject(p_code);
	var request = getHttpRequest('../inc/_check_class.php?check=exist_center&code='+code.value);

	if (request){
		alert('입력하신 코드는 이미 등록되어있는 코드입니다. 확인후 다시 입력하여 주십시오.');
		code.value = '';
		code.focus();
		return false;
	}else{
		return true;
	}
}

function _setMkind(){
	var mCode = document.getElementById('mCode');
	var mKind = document.getElementById('mKind');
	var select;
	var request = null;

	if (document.getElementById("mCode").value != ''){
		request = getHttpRequest('../inc/_check.php?gubun=findCenter&mCode='+mCode.value+'&mKind='+mKind.value);
		if (request != 'N'){
			_centerReg('reg',mCode.value,request);
			return;
		}else{
			_centerReg('reg',mCode.value,mKind.value);
		}
	}
	
	select = document.getElementById("kupyeoGbn");
	select.innerHTML  = '';

	if (mKind.value == '0'){
		select.innerHTML += '<input name="kupyeoGbn" type="hidden" value="N">';
		select.innerHTML += '<input name="kupyeo1" type="checkbox" value="Y" class="checkbox">방문요양';
		select.innerHTML += '<input name="kupyeo2" type="checkbox" value="Y" class="checkbox">방문목욕';
		select.innerHTML += '<input name="kupyeo3" type="checkbox" value="Y" class="checkbox">방문간호';
	}else{
		select.innerHTML += '<input name="kupyeoGbn" type="checkbox" value="Y" class="checkbox">'+mKind.options[mKind.selectedIndex].text;
		select.innerHTML += '<input name="kupyeo1" type="hidden" value="N">';
		select.innerHTML += '<input name="kupyeo2" type="hidden" value="N">';
		select.innerHTML += '<input name="kupyeo3" type="hidden" value="N">';
	}
}

function _setCenterName(gubun, selMcode, selMkind, curCode1, curMkind, serCode1, serMkind){
	var request = getHttpRequest('../inc/_check.php?gubun=getCenterName&mCode='+selMcode.value+'&mKind='+selMkind.value);
		request = request.split('//');

	try{
		curCode1.innerHTML = request[0];
		curMkind.innerHTML = request[1];
	}catch(e){}

	try{
		serCode1.value = request[0];
		serMkind.value = request[1];
	}catch(e){
		if (gubun == 'yoyReg'){
			_centerYoyReg('reg', selMcode.value, selMkind.value);
		}else if(gubun == 'suReg'){
			_sugupjaReg('reg', selMcode.value, selMkind.value);
		}
	}
}

function _checkSSN(gubun, jumin1, jumin2, curMcode, curMkind){
	var SSN1 = jumin1.value;
	var SSN2 = jumin2.value;

	try{
		if (document.getElementById('curMcode').value == '1234' ||
			document.getElementById('curMcode').value == '0627141516'){
			var skip = true;
		}else{
			var skip = false;
		}
	}catch(e){
		var skip = false;
	}

	if (SSN1.length != 6 || SSN2.length != 7){
		return false;
	}
	
	// 주민번호 체크디지트
	if (!__isSSN(SSN1,SSN2)){
		alert('입력하신 주민번호의 형식이 올바르지 않습니다. 다시 확인 후 입력하여 주십시오.');
		jumin1.value = '';
		jumin2.value = '';
		jumin1.focus();
		return false;
	}

	var mCode = curMcode.value;
	var mKind = curMkind.value;
	var yJumin = SSN1+SSN2;

	if (gubun == 'yoy'){
		var request = getHttpRequest('../inc/_yoy_check_ssn.php?mCode='+mCode+'&mKind='+mKind+'&yJumin='+yJumin);

		if (request != 'N'){
			var yoy1 = getHttpRequest('../inc/_check_class.php?check=getYoyNameAndMobile&jumin='+yJumin);

			if (yoy1 != ''){
				var yoy2 = yoy1.split('//');
				document.center.yName.value = yoy2[0];
				document.center.yName.readOnly = true;
				document.center.yName.style.backgroundColor = '#eeeeee';
				document.center.yName.onfocus = function(){
					this.blur();
				}
				
				if (yoy2[1] != ''){
					document.center.yTel.value = __getPhoneNo(yoy2[1]);
					document.center.yTel.readOnly = true;
					document.center.yTel.style.backgroundColor = '#eeeeee';
					document.center.yTel.onfocus = function(){
						this.blur();
					}
				}else{
					document.center.yTel.value = '';
					document.center.yTel.readOnly = false;
					document.center.yTel.style.backgroundColor = '#ffffff';
					document.center.yTel.onfocus = function(){
						__replace(this, '-', '');
					}
				}
			}
			return false;
		}else{
			/*
			document.center.yName.value = '';
			document.center.yName.readOnly = false;
			document.center.yName.style.backgroundColor = '#ffffff';
			document.center.yName.onfocus = function(){
				this.select();
			}
			*/
			
			/*
			document.center.yTel.value = '';
			document.center.yTel.readOnly = false;
			document.center.yTel.style.backgroundColor = '#ffffff';
			document.center.yTel.onfocus = function(){
				__replace(this, '-', '');
			}
			*/
			return true;
		}
	}else if(gubun == 'center'){
		var request = 'N';
	}else{
		var request = getHttpRequest('../inc/_su_check_ssn.php?mCode='+mCode+'&mKind='+mKind+'&yJumin='+yJumin);
	}

	if (request != 'N'){
		alert('입력하신 주민번호는 이미 등록된 주민번호입니다. 확인 후 다시 입력하여 주십시오.');
		jumin1.value = '';
		jumin2.value = '';
		jumin1.focus();
		return false;
	}

	return true;
}

function _setBoninYul(boninYul, code){
	var boninYul = document.getElementById('boninYul');

	if (code == '2' || code == '4'){
		boninYul.readOnly = false;
		boninYul.style.backgroundColor = '#ffffff';
		boninYul.onfocus = function(){
			this.select();
		}
	}else{
		boninYul.readOnly = true;
		boninYul.style.backgroundColor = 'eeeeee';
		boninYul.onfocus = function(){
			document.center.familyCare.focus();
		}
	}
	boninYul.value = getHttpRequest('../inc/_check.php?gubun=getBoninYul&code='+code);
	_setBoninKupyeo();
}

// 본인부담율에 금액
function _setPay(){
	var totalPay  = document.getElementById('kupyeoMax'); //급여한도액
	var boninRate = document.getElementById('boninYul').value; //본인부담율
	var givePay   = document.getElementById('kupyeo1'); //정부지원금
	var boninPay  = document.getElementById('kupyeo2'); //본인부담금

	givePay.value  = __commaSet(parseInt(__commaUnset(totalPay.value), 10) - (parseInt(__commaUnset(totalPay.value), 10) * (boninRate / 100)));
	boninPay.value = __commaSet(parseInt(__commaUnset(totalPay.value), 10) - parseInt(__commaUnset(givePay.value), 10));
	
	var temp1 = parseInt(__commaUnset(givePay.value), 10) - parseInt(cutOff(__commaUnset(givePay.value)), 10);

	givePay.value  = __commaSet(cutOff(__commaUnset(givePay.value)));
	boninPay.value = __commaSet(parseInt(__commaUnset(boninPay.value), 10) + temp1);
}

function _setKupyeoMax(object, code){
	object.value = __commaSet(getHttpRequest('../inc/_check.php?gubun=getMaxPay&code='+code));
	_setBoninKupyeo();
}

function _setBoninKupyeo(){
	var kypyeoMax = __commaUnset(document.center.kupyeoMax.value);
	var boninYul = document.center.boninYul.value;
	var kypyeo2 = cutOff(parseInt(kypyeoMax) * boninYul / 100);
	var kypyeo1 = cutOff(parseInt(kypyeoMax) - kypyeo2);

	document.center.kupyeo1.value = __commaSet(kypyeo1);
	document.center.kupyeo2.value = __commaSet(kypyeo2);
}

function _checkMuksuRate(object1, object2){
	var rate = __NaN(object1.value) + __NaN(object2.value);

	if (object1.value == '') object1.value = '0';
	if (object2.value == '') object2.value = '0';

	if (rate != 100){
		object2.value = 100 - __NaN(object1.value);
	}
}

// 요양사 핸드폰 번호 중복 체크
function checkHPno(mHP){
	/*
	var request = getHttpRequest('../inc/_check.php?gubun=checkHPNo&mHP='+mHP.value);

	if (request != 0){
		alert('입력하신 전화번호는 현재 활동중인 요양사의 전화번호 입니다. 확인 후 다시 입력하여 주십시오.');
		event.returnValue = false;
		mHP.value = '';
		mHP.focus();
		return;
	}
	*/
}

// 급여산정방식 고정급유무
function selectGupyeoType(){
	var payType = getPayType();

	if (payType == '1' || payType == '2'){
		document.getElementById('gupyeoType_span').style.display = '';
	}else{
		document.getElementById('gupyeoType_span').style.display = 'none';
	}

	document.getElementById('payTr1').style.display = 'none';
	document.getElementById('payTd1').style.display = 'none';
	document.getElementById('payTr2').style.display = 'none';
	document.getElementById('payTd2').style.display = 'none';
	document.getElementById('payTr3').style.display = 'none';
	document.getElementById('payTd3').style.display = 'none';
	document.getElementById('payTr4').style.display = 'none';
	document.getElementById('payTd4').style.display = 'none';

	document.getElementById('payTr'+payType).style.display = '';
	document.getElementById('payTd'+payType).style.display = '';
}

// 급여산정방식
function getPayType(){
	var gubun = document.center.gupyeoType.checked;
	var kind = document.center.yGupyeoKind.value;

	if (kind == '1' || kind == '2'){
		if (gubun != true){
			var payType = '1';
		}else{
			var payType = '2';
		}
	}else if (kind == '3'){
		var payType = '3';
	}else if (kind == '4'){
		var payType = '4';
	}else{
		var payType = '1';
	}

	return payType;
}

// 4대보험 유무
function setInsType(){
	var insType = document.getElementById('y4BohumUmu').value;

	if (insType == 'Y'){
		document.getElementById('bohum1').style.display = '';
		document.getElementById('bohum2').style.display = '';
		document.getElementById('bohum3').style.display = '';
		document.getElementById('bohum4').style.display = '';
		
		document.getElementById('yGoBohumUmu').style.display = 'none';
		document.getElementById('ySnBohumUmu').style.display = 'none';
		document.getElementById('yGnBohumUmu').style.display = 'none';
		document.getElementById('yKmBohumUmu').style.display = 'none';
	}else{
		document.getElementById('bohum1').style.display = 'none';
		document.getElementById('bohum2').style.display = 'none';
		document.getElementById('bohum3').style.display = 'none';
		document.getElementById('bohum4').style.display = 'none';

		document.getElementById('yGoBohumUmu').style.display = '';
		document.getElementById('ySnBohumUmu').style.display = '';
		document.getElementById('yGnBohumUmu').style.display = '';
		document.getElementById('yKmBohumUmu').style.display = '';
	}
}

// 퇴사여부
function checkCenterOut(){
	if (document.getElementById('yGoyongStat').value == '9'){
		if (document.getElementById('yToisail').value == ''){
			alert('퇴사일자를 입력하여 주십시오.');
			document.getElementById('yToisail').focus();
			return false;
		}
	}

	return true;
}

// 수급자 삭제
function _sugupjaDelete(){
	if (!confirm('수급자 삭제 후 수급자의 데이타 및 일정데이타를 복구 할 수 없습니다.\n정말로 삭제하시겠습니까?')){
		return;
	}

	document.center.action = 'delete.php';
	document.center.submit();
}

// 직원엑셀 업로드
function _excelUpload(form, file, action){
	if (file.value == ''){
		alert('업로드할 엑셀 파일을 선택하여 주십시오.');
		return;
	}

	var exp = file.value.split('.');
	
	if (exp[exp.length-1].toLowerCase() != 'xls'){
		alert('xls 파일 형식의 엑셀 파일만 업로드 가능합니다. 확인하여 주십시오.');
		return;
	}

	if (!confirm('선택하신 엑셀파일의 데이타를 저장하시겠습니까?')){
		return;
	}

	form.action = action;
	form.submit();
}

// 수급자현황 조회
function conditionSearch(p_body, p_code, p_kind, p_gubun){
	var URL = '../sugupja/condition_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mGubun:p_gubun
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 보험사 상품 리스트
function _insItemList(p_code){
	if (p_code != ''){
		document.getElementById('idInsItem').innerHTML = '배상책임보험';
	}else{
		document.getElementById('idInsItem').innerHTML = '-';
	}
}

// 보험료 단가
function _insMemberPrice(p_code, p_item, p_memberCount, p_price, p_amount){
	var code = (typeof(p_code) == 'object' ? p_code : document.getElementById(p_code));
	var item = (typeof(p_item) == 'object' ? p_item : document.getElementById(p_item));
	var memberCount = (typeof(p_memberCount) == 'object' ? p_memberCount : document.getElementById(p_memberCount));
	var price = (typeof(p_price) == 'object' ? p_price : document.getElementById(p_price));
	var amount = (typeof(p_amount) == 'object' ? p_amount : document.getElementById(p_amount));
	
	if (code.value == ''){
		alert('보험사를 선택하여 주십시오.');
		price.value = '0';
		amount.value = '0';
		return;
	}

	if (item.value == ''){
		alert('보험상품을 선택하여 주십시오.');
		price.value = '0';
		amount.value = '0';
		return;
	}
	
	var request = getHttpRequest('../inc/_check_class.php?check=insItemPrice&code='+code.value+'&item='+item.value+'&memberCount='+memberCount.value);
	
	if (request == '') request = '0';

	price.value = __commaSet(request);
	amount.value = __commaSet(parseInt(request) * parseInt(memberCount.value));
}

// 보험가입기간
function _insToDate(p_fromDate, p_toDate){
	var toDate = (typeof(p_toDate) == 'object' ? p_toDate : document.getElementById(p_toDate));
	var formDate = __getDate(p_fromDate);
	
	if (checkDate(formDate)){
		toDate.value = addDate('yyyy', 1, formDate);
	}else{
		toDate.value = '';
	}
	//toDate.value = addDate('d', -1, toDate.value);
}

// 요양보호사 적용보험 반영유무
function _yoyangsaInsYN(p_yn, p_name, p_item, p_price, p_from, p_to, id_name, id_item, id_price, id_from, id_to){
	var insName = (typeof(p_name) == 'object' ? p_name : document.getElementById(p_name));
	var insItem = (typeof(p_item) == 'object' ? p_item : document.getElementById(p_item));
	var insPrice = (typeof(p_price) == 'object' ? p_price : document.getElementById(p_price));
	var insForm = (typeof(p_from) == 'object' ? p_from : document.getElementById(p_from));
	var insTo = (typeof(p_to) == 'object' ? p_to : document.getElementById(p_to));
	var idName = (typeof(id_name) == 'object' ? id_name : document.getElementById(id_name));
	var idItem = (typeof(id_item) == 'object' ? id_item : document.getElementById(id_item));
	var idPrice = (typeof(id_price) == 'object' ? id_price : document.getElementById(id_price));
	var idFrom = (typeof(id_from) == 'object' ? id_from : document.getElementById(id_from));
	var idTo = (typeof(id_to) == 'object' ? id_to : document.getElementById(id_to));

	if (p_yn == 'Y'){
		idName.innerHTML = insName.value;
		idItem.innerHTML = insItem.value;
		idPrice.innerHTML = __commaSet(insPrice.value);
		idFrom.innerHTML = __getDate(insForm.value);
		idTo.innerHTML = __getDate(insTo.value);
	}else{
		idName.innerHTML = '';
		idItem.innerHTML = '';
		idPrice.innerHTML = '';
		idFrom.innerHTML = '';
		idTo.innerHTML = '';
	}
}

// 재가요양인 경우 보험유무를 판단한다.
/*
function _familyCareInsYN(p_care, p_stat, p_ins, p_name, p_item, p_price, p_from, p_to, id_name, id_item, id_price, id_from, id_to){
	var care = __getObject(p_care);
	var stat = __getObject(p_stat);
	var ins = __getObject(p_ins);
	var insName = __getObject(p_name);
	var insItem = __getObject(p_item);
	var insPrice = __getObject(p_price);
	var insForm = __getObject(p_from);
	var insTo = __getObject(p_to);
	var idName = __getObject(id_name);
	var idItem = __getObject(id_item);
	var idPrice = __getObject(id_price);
	var idFrom = __getObject(id_from);
	var idTo = __getObject(id_to);

	if (care.value == 'Y'){
		ins.value = 'N';

		idName.innerHTML = '';
		idItem.innerHTML = '';
		idPrice.innerHTML = '';
		idFrom.innerHTML = '';
		idTo.innerHTML = '';
	}else{
		ins.value = 'Y';

		idName.innerHTML = insName.value;
		idItem.innerHTML = insItem.value;
		idPrice.innerHTML = __commaSet(insPrice.value);
		idFrom.innerHTML = __getDate(insForm.value);
		idTo.innerHTML = __getDate(insTo.value);
	}
}
*/
function _familyCareInsYN(){
	var yFamCareUmu = document.getElementById('yFamCareUmu');
	var yFamCarePay = document.getElementById('yFamCarePay');

	// 친족케어를 실행하면 친족케어 시급을 입력받는다.
	if (yFamCareUmu.value == 'Y'){
		yFamCarePay.readOnly = false;
		yFamCarePay.value = yFamCarePay.tag;
		yFamCarePay.style.backgroundColor = '#fff';
		yFamCarePay.onfocus = function(){
			__commaUnset(this);
		}
	}else{
		yFamCarePay.readOnly = true;
		yFamCarePay.value = '0';
		yFamCarePay.style.backgroundColor = '#eee';
		yFamCarePay.onfocus = function(){
			this.blur();
		}
	}
}
function _family_care_yn(){
	var yn  = __get_value(document.getElementsByName('yFamCareUmu'));
	var pay = __getObject('yFamCarePay');

	// 친족케어를 실행하면 친족케어 시급을 입력받는다.
	if (yn == 'Y'){
		pay.readOnly = false;
		pay.value = pay.tag;
		pay.style.backgroundColor = '#fff';
		pay.onfocus = function(){
			__commaUnset(this);
			this.style.borderColor='#0e69b0';
		}
	}else{
		pay.readOnly = true;
		pay.value = '0';
		pay.style.backgroundColor = '#eee';
		pay.onfocus = function(){
			this.blur();
		}
	}
}

//
function _regInsMember(){
	bLayer.style.width = document.body.offsetWidth;

	if (document.body.scrollHeight > document.body.offsetHeight){
		bLayer.style.height = document.body.scrollHeight;
	}else{
		bLayer.style.height = document.body.offsetHeight;
	}
	
	var tableLeft = (parseInt(__replace(bLayer.style.width, 'px', '')) - parseInt(__replace(centerInMemberTable.style.width, 'px', ''))) / 2+'px';
	var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(centerInMemberTable.style.height, 'px', ''))) / 2+'px';

	centerInMemberLaber.style.top = tableTop;
	centerInMemberLaber.style.left = tableLeft;
	centerInMemberLaber.style.width = centerInMemberTable.style.width;
	centerInMemberLaber.style.height = centerInMemberTable.style.height;
	centerInMemberLaber.style.display = '';
	centerInMemberTable.style.display = '';

	menu_left.style.display = 'none';
}

function _regInsMemberCancel(){
	bLayer.style.width = 0;
	bLayer.style.height = 0;
	centerInMemberLaber.style.width = 0;
	centerInMemberLaber.style.height = 0;
	centerInMemberTable.style.display = 'none';

	menu_left.style.display = '';
}

function _regInsMemberOk(){
	var rowCount = __checkMyCount('insCheck[]');
	
	document.getElementById('insMemberCount').value = rowCount;

	_insMemberPrice('insName', 'insItem', 'insMemberCount', 'insMemberPrice', 'insAmount');
	_regInsMemberCancel();
}

function _regInsMemberIsChecks(target, checked){
	var target = document.getElementsByName(target);
	var checked = (typeof(checked) == 'object' ? checked.checked : checked);

	for(var i=0; i<target.length; i++){
		if (_regInsMemberCheck(i)){
			target[i].checked = checked;
		}else{
			target[i].checked = false;
		}
	}
}

function _regInsMemberIsCheck(target, index){
	if (!_regInsMemberCheck(index)){
		if (target.checked){
			target.checked = false;
			alert('퇴사한 요양보호사는 보험에 가입할 수 없습니다.');
			return;
		}
	}
}

function _regInsMemberCheck(index){
	var stat = document.getElementsByName('stat[]')[index].value;

	if (stat == '1'){
		return true;
	}else{
		return false;
	}
}

// 직원현황 조회
function _memberStatusList(){
	//myBody.innerHTML = __loading();

	var URL = 'status_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mFamily:document.f.familyCare.value,
				mEmployment:document.f.employment.value,
				mInsurance:document.f.insurance.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 요양보호사 일괄관리 조회
/*
function _manageSearch(p_code, p_kind, p_employ){
	var URL = 'manage_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mEmploy:p_employ
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}
*/
function _manageSearch(){
	var URL = 'manage_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mEmploy:document.f.employ.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 요양보호사 배상책임보험 반영여부
function _setInsData(){
	var insYN = document.getElementById('insYN');
	
	if (insYN.value == 'Y'){
		document.getElementById('id_insName').innerHTML         = document.getElementById('insName').value;
		document.getElementById('id_insItemName').innerHTML     = '배상책임보험';
		document.getElementById('insToDate').style.display      = 'none';
		document.getElementById('insFromDate').value            = document.getElementById('insFromDate').tag;
		document.getElementById('id_insToDate').innerHTML       = document.getElementById('insToDate').tag;
		document.getElementById('id_insToDate').style.display   = '';

		if (insYN.tag == 'Y'){
			document.getElementById('insFromDate').style.display    = 'none';
			document.getElementById('id_insFromDate').style.display = '';
		}else{
			document.getElementById('insFromDate').style.display    = '';
			document.getElementById('id_insFromDate').style.display = 'none';
		}
	}else{
		if (insYN.tag == 'Y'){
			document.getElementById('insFromDate').style.display    = 'none';
			document.getElementById('id_insFromDate').style.display = '';
			document.getElementById('insToDate').style.display      = '';
			document.getElementById('insToDate').value              = document.getElementById('insToDate').tag
			document.getElementById('id_insToDate').style.display   = 'none';
		}else{
			document.getElementById('id_insName').innerHTML      = '-';
			document.getElementById('id_insItemName').innerHTML  = '-';
			document.getElementById('insFromDate').style.display = 'none';
			document.getElementById('insToDate').style.display   = 'none';
			document.getElementById('id_insFromDate').innerHTML  = '';
			document.getElementById('id_insToDate').innerHTML    = '';
		}
	}
}

// 요양보호사 보험가입기간 체크
function _checkInsLimitDate(){
	// 보험가입 여부
	var insYN = document.getElementById('insYN').value;

	// 보험가입 시작일자
	var yFromDate = __getDate(document.getElementById('insFromDate').value);
	var cFromDate = __getDate(document.getElementById('centerInsFromDate').value);

	// 보험가입 종료일자
	var yToDate = __getDate(document.getElementById('insToDate').value);
	var cToDate = __getDate(document.getElementById('centerInsToDate').value);

	if (insYN == 'Y'){
		if (!checkDate(yFromDate)) return;
		if (!checkDate(cFromDate)) return;

		if (diffDate('d', yFromDate, cFromDate) > 0){
			alert('보험가입 시작일자는 '+cFromDate+'부터입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insFromDate').value = cFromDate;
			return;
		}
	}else{
		if (!checkDate(yToDate)) return;
		if (!checkDate(cToDate)) return;
		
		if (diffDate('d', yFromDate, yToDate) < 0){
			alert('보험가입 종료일자는 '+yFromDate+'~'+yToDate+'입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insToDate').value = yFromDate;
			return;
		}

		if (diffDate('d', yToDate, cToDate) < 0){
			alert('보험가입 종료일자는 '+cFromDate+'까지입니다. 확인후 다시 입력하여 주십시오.');
			document.getElementById('insToDate').value = cFromDate;
			return;
		}
	}
}

// 수급자본인부담율 설명
function _showBoninYulHelp(){
	var idHelpBoninYul = document.getElementById('idHelpBoninYul');
	var helpBoninYul   = document.getElementById('helpBoninYul');
	var top  = __getObjectTop(idHelpBoninYul) + 15;
	var left = __getObjectLeft(idHelpBoninYul) - 180;

	helpBoninYul.style.top  = top+'px';
	helpBoninYul.style.left = left+'px';
	helpBoninYul.style.display = '';
}

// 고용상태 변경시
function _goyongStatChanged(){
	if (document.getElementById('yGoyongStat').value == '1'){
		document.getElementById('yToisail').value = '';
	}else{
		document.getElementById('yToisail').value = document.getElementById('yToisail').tag;
	}
}

// 요양기관구분 활성화 레이어 여부
function _show_tbody_layer(target, layer, show){
	var target = __getObject(target);
	var layer  = __getObject(layer);

	if (!show){
		var x = __getObjectLeft(target);
		var y = __getObjectTop(target);
		var w = target.offsetWidth;
		var h = target.offsetHeight;

		layer.style.top     = y;
		layer.style.left    = x;
		layer.style.width   = w;
		layer.style.height  = h;
		layer.style.display = '';
	}else{
		layer.style.display = 'none';
	}
}

// 기관저장
function _save_center(){
	var f = document.f;

	if (!__alert(f.mCode)) return; //기관기호 입력여부
	if (!__alert(f.cTel)) return;

	if (__replace(f.cPostNo1.value, ' ', '') == ''){
		alert('주소를 입력하여 주십시오.');
		__helpAddress(f.cPostNo1, f.cPostNo2, f.cAddr1, f.cAddr2);
		return;
	}

	//if (!__alert(f.mName)) return; //대표자명
	//if (!__alert(f.contDate)) return; //사용일자

	// 요양기관구분 선택확인
	if (!f.kind_1.checked && !f.kind_2.checked && !f.kind_3.checked){
		alert('요양기관구분을 하나이상 선택하여 주십시오.');
		return;
	}

	// 제공급여선택확인
	if (f.kind_1.checked){
		if (!__alert(f.cName0)) return; //기관명 입력여부
		if (!__alert(f.code0)) return; //기관명 입력여부
		if (!f.kupyeo1.checked && !f.kupyeo2.checked && !f.kupyeo3.checked){
			alert('제공급여종류를 하나이상 선택하여 주십시오.');
			return;
		}
	}

	// 바우처 승인번호 입력확인
	if (f.kind_2.checked){
		if (!f.kind_2_1.checked &&
			!f.kind_2_2.checked &&
			!f.kind_2_3.checked &&
			!f.kind_2_4.checked){
			alert('하나이상의 바우처를 선택하여 주십시오.');
			return;
		}

		// 가사간병
		if (f.kind_2_1.checked){
			if (!__alert(f.code1)) return; //승인번호
			if (!__alert(f.cName1)) return; //명칭
		}

		// 노인돌봄
		if (f.kind_2_2.checked){
			if (!__alert(f.code2)) return; //승인번호
			if (!__alert(f.cName2)) return; //명칭
		}

		// 산모신생아
		if (f.kind_2_3.checked){
			if (!__alert(f.code3)) return; //승인번호
			if (!__alert(f.cName3)) return; //명칭
		}

		// 장애인보조
		if (f.kind_2_4.checked){
			if (!__alert(f.code4)) return; //승인번호
			if (!__alert(f.cName4)) return; //명칭
		}
	}

	// 시설
	if (f.kind_3.checked){
		if (!__alert(f.code5)) return; //승인번호
		if (!__alert(f.cName5)) return; //명칭
	}

	f.action = 'save.php';
	f.submit();
}

// 기관리스트
function _list_center(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'list.php';
	f.submit();
}

// 기관등록
function _reg_center(code){
	var f = document.f;

	f.mCode.value = code;
	f.action = 'reg.php';
	f.submit();
}

// 직원리스트
function _list_member(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'list.php';
	f.submit();
}

// 직원수정
function _reg_member(code, kind, jumin){
	var f = document.f;

	f.code.value  = code;
	f.kind.value  = kind;
	f.jumin.value = jumin;
	f.action = 'reg.php';
	f.submit();
}

// 직원저장
function _save_member(){
	var f = document.f;

	if (f.mode.value == 1){
		if (f.yJumin1.value.length != 6){
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.yJumin1.focus();
			return;
		}
		if (f.yJumin2.value.length != 7){
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.yJumin2.focus();
			return;
		}
		if (!_check_ssn('yoy', f.yJumin1, f.yJumin2, f.code)) return;
	}
	
	if (!__alert(f.yName)) return;
	if (!__alert(f.yTel)) return;
	
	if (__replace(f.yJuso1.value) == ''){
		alert('주소를 입력하여 주십시오.');
		__helpAddress(f.yPostNo1, f.yPostNo2, f.yJuso1, f.yJuso2);
	}

	var kind = document.getElementsByName('kind_list[]');
	var kind_sel = false;

	for(var i=0; i<kind.length; i++){
		if (kind[i].checked){
			switch(kind[i].value){
			case '0':
				if (!_save_menber_0(f)) return;
				break;
			case '1':
				break;
			case '2':
				break;
			case '3':
				break;
			case '4':
				break;
			case '5':
				break;
			}
			kind_sel = true;
			break;
		}
	}

	if (!kind_sel){
		alert('기관구분을 하나이상 선택하여 주십시오.');
		return;
	}

	f.action = 'save.php';
	f.submit();
}

// 재가요양 확인
function _save_menber_0(f){
	var pay_kind		= __get_value(f.yGupyeoKind);
	var ins_yn			= __get_value(f.insYN);
	var family_care_yn	= __get_value(f.yFamCareUmu);

	if (!__alert(f.yIpsail)) return false;

	if (__get_value(f.yGoyongStat) == '9'){
		if (!checkDate(f.yToisail.value)){
			alert('퇴사일자를 입력하여 주십시오.');
			f.yToisail.focus();
			return false;
		}

		if (f.yIpsail.value > f.yToisail.value){
			alert('퇴사일이 입사일보다 작습니다. 확인하여 주십시오.');
			f.yToisail.focus();
			return false;
		}

		if (ins_yn == 'Y'){
			alert('배상책임보험을 해지신청하여 주십시오.');
			return false;
		}
	}

	if (!f.yGunmuMon.checked &&
		!f.yGunmuTue.checked &&
		!f.yGunmuWed.checked &&
		!f.yGunmuThu.checked &&
		!f.yGunmuFri.checked &&
		!f.yGunmuSat.checked &&
		!f.yGunmuSun.checked){
		alert('근무가능요일을 하나 이상 선택하여 주십시오.');
		return false;
	}

	switch(pay_kind){
	case '1Y':
		if (!__alert(f.yGibonKup1)) return false;
		break;
	case '1N':
		var pay_list = document.getElementsByName('yGibonKup[]');

		for(var i=0; i<pay_list.length; i++){
			if (!__alert(pay_list[i])) return false;
		}
		break;
	case '3':
		if (!__alert(f.yGibonKup3)) return false;
		break;
	case '4':
		if (!__alert(f.ySugaYoyul)) return false;
		break;
	}

	if (family_care_yn == 'Y'){
		if (!__alert(f.yFamCarePay)) return false;
	}

	if (ins_yn == 'Y'){
		if (!checkDate(f.insFromDate.value)){
			alert('배상책임보험 가입일자를 입력하여 주십시오.');
			f.insFromDate.focus();
			return false;
		}

		if (!checkDate(f.insToDate.value)){
			alert('배상책임보험 해지일자를 입력하여 주십시오.');
			f.insToDate.focus();
			return false;
		}

		if (f.insFromDate.value < f.yIpsail.value){
			alert('배상책임보험 가입일자가 입사일자보다 작습니다 .확인하여 주십시오.');
			f.insFromDate.focus();
			return false;
		}
	}else{
		if (checkDate(f.insToDate.value)){
			if (f.insToDate.value > f.yToisail.value){
				alert('배상책임보험 해지일자 퇴사일자보다 큽니다. 확인하여 주십시오.');
				f.insToDate.focus();
				return false;
			}
		}
	}
	
	return true;
}

// 직원 보험가입여부
function _ins_join_yn(ins_yn, now_yn, f_date, t_date){
	f_dtae = __getObject(f_date);
	t_date = __getObject(t_date);
	
	if (now_yn == 'N'){
		if (ins_yn == 'Y'){
			f_dtae.disabled = false;
			f_dtae.style.backgroundColor = '#ffffff';
			f_dtae.value = f_dtae.tag;

			t_date.disabled = false;
			t_date.style.backgroundColor = '#eeeeee';
			t_date.value = t_date.tag;
		}else{
			f_dtae.disabled = true;
			f_dtae.style.backgroundColor = '#eeeeee';
			f_dtae.value = '';

			t_date.disabled = true;
			t_date.style.backgroundColor = '#eeeeee';
			t_date.value = '';
		}

		t_date.alt = 'not';
		t_date.style.backgroundColor = '#eeeeee';
		t_date.style.cursor = 'default';
		t_date.onfocus = function(){
			this.blur();
		}
		t_date.onclick = null;
		t_date.onchange = null;
	}else{
		if (ins_yn == 'Y'){
			f_dtae.disabled = false;
			f_dtae.style.backgroundColor = '#ffffff';
			f_dtae.value = f_dtae.tag;
			f_dtae.alt = '_checkInsLimitDate';
			f_dtae.style.cursor = '';
			f_dtae.onfocus = function(){
				__replace(this, '-', '');
			}
			f_dtae.onclick = function(){
				_carlendar(this);
			}
			f_dtae.onchange = function(){
				_checkInsLimitDate();
			}

			t_date.disabled = false;
			t_date.value = t_date.tag;
			t_date.alt = 'not';
			t_date.style.backgroundColor = '#eeeeee';
			t_date.style.cursor = 'default';
			t_date.onfocus = function(){
				this.blur();
			}
			t_date.onclick = null;
			t_date.onchange = null;
		}else{
			f_dtae.disabled = false;
			f_dtae.value = f_dtae.tag;
			f_dtae.alt = 'not';
			f_dtae.style.backgroundColor = '#eeeeee';
			f_dtae.style.cursor = 'default';
			f_dtae.onfocus = function(){
				this.blur();
			}
			f_dtae.onclick = null;
			f_dtae.onchange = null;

			t_date.disabled = false;
			t_date.style.backgroundColor = '#ffffff';
			t_date.value = t_date.tag;

			t_date.alt = '_checkInsLimitDate';
			t_date.style.cursor = '';
			t_date.onfocus = function(){
				__replace(this, '-', '');
			}
			t_date.onclick = function(){
				_carlendar(this);
			}
			t_date.onchange = function(){
				_checkInsLimitDate();
			}
		}
	}
}

function _check_ssn(gubun, jumin1, jumin2, code){
	var SSN1 = jumin1.value;
	var SSN2 = jumin2.value;

	try{
		if (code.value == '1234' ||
			code.value == '0627141516'){
			var skip = true;
		}else{
			var skip = false;
		}
	}catch(e){
		var skip = false;
	}

	if (SSN1.length != 6 || SSN2.length != 7){
		return false;
	}

	// 주민번호 체크디지트
	if (!skip){
		if (!__isSSN(SSN1,SSN2)){
			alert('입력하신 주민번호의 형식이 올바르지 않습니다. 다시 확인 후 입력하여 주십시오.');
			jumin1.value = '';
			jumin2.value = '';
			jumin1.focus();
			return false;
		}
	}

	var yJumin = SSN1+SSN2;

	if (gubun == 'yoy'){
		var request = getHttpRequest('../inc/_yoy_check_ssn.php?mCode='+code.value+'&yJumin='+yJumin);
		
		/*
		if (request != 'N'){
			var yoy1 = getHttpRequest('../inc/_check_class.php?check=getYoyNameAndMobile&jumin='+yJumin);

			if (yoy1 != ''){
				var yoy2 = yoy1.split('//');
				document.f.yName.value = yoy2[0];
				document.f.yName.readOnly = true;
				document.f.yName.style.backgroundColor = '#eeeeee';
				document.f.yName.onfocus = function(){
					this.blur();
				}
				
				if (yoy2[1] != ''){
					document.f.yTel.value = __getPhoneNo(yoy2[1]);
					document.f.yTel.readOnly = true;
					document.f.yTel.style.backgroundColor = '#eeeeee';
					document.f.yTel.onfocus = function(){
						this.blur();
					}
				}else{
					document.f.yTel.value = '';
					document.f.yTel.readOnly = false;
					document.f.yTel.style.backgroundColor = '#ffffff';
					document.f.yTel.onfocus = function(){
						__replace(this, '-', '');
					}
				}
			}
			return false;
		}else{
			return true;
		}
		*/
	}else if(gubun == 'center'){
		var request = 'N';
	}else{
		var request = getHttpRequest('../inc/_su_check_ssn.php?mCode='+code.value+'&yJumin='+yJumin);
	}

	if (request != 'N'){
		alert('입력하신 주민번호는 이미 등록된 주민번호입니다. 확인 후 다시 입력하여 주십시오.');
		//jumin1.value = '';
		//jumin2.value = '';
		jumin1.focus();
		return false;
	}

	return true;
}

// 직원 리포트
function _member_report_layer(p_target, p_index, p_code, p_kind, p_jumin, p_value1){
	var target	= __getObject(p_target);
	var x		= __getObjectLeft(target);
	var y		= __getObjectTop(target);
	var body	= __getObject('info_layer_body');
	var draw    = __getObject('info_draw_body');

	var URL = '../yoyangsa/manager_sub_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:p_code,
				kind:p_kind,
				jumin:p_jumin,
				index:p_index,
				value1:p_value1
			},
			onSuccess:function (responseHttpObj) {
				draw.innerHTML = responseHttpObj.responseText;
				
				if (x + 213 >= 1024){
					body.style.left = 1024 - 213;
				}else{
					body.style.left = x + 3;
				}

				body.style.top  = y + target.offsetHeight + 4;
				body.style.display = '';
			}
		}
	);
}

// 직원 리포트 메뉴 창 닫기
function _member_report_layer_close(){
	var body = __getObject('info_layer_body');
	body.style.display = 'none';
}

// 수급자 리스트
function _list_client(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'list.php';
	f.submit();
}

// 수급자 등록
function _reg_client(code, kind, jumin, modfify_type){
	var f = document.f;

	f.code.value		= code;
	f.kind.value		= kind;
	f.jumin.value		= jumin;
	f.modify_type.value = modfify_type;

	f.action = 'reg.php';
	f.submit();
}

// 수급자 저장
function _save_client(){
	var f = document.f;
	var c = document.sugupCheck;

	if (f.mode.value == 1){
		f.sDate.value = f.gaeYakFm.value;
		_save_client_sub(f);
	}else{
		if (__get_value(f.yLvl)			== __get_tag(f.yLvl)		&&
			__get_value(f.sKind)		== __get_tag(f.sKind)		&&
			f.yoyangsa1.value			== f.yoyangsa1.tag			&&
			f.gaeYakFm.value			== f.gaeYakFm.tag			&&
			f.gaeYakTo.value			== f.gaeYakTo.tag			&&
			__get_value(f.sugupStatus)	== __get_tag(f.sugupStatus)	&&
			f.boninYul.value			== f.boninYul.tag){
			_save_client_sub(f);
			return;
		}

		c.startDate.value = f.sDate.value;

		sugupLayer1.style.width  = document.body.offsetWidth;

		if (document.body.scrollHeight > document.body.offsetHeight){
			sugupLayer1.style.height = document.body.scrollHeight;
		}else{
			sugupLayer1.style.height = document.body.offsetHeight;
		}

		var tableLeft = (parseInt(__replace(sugupLayer1.style.width, 'px', '')) - parseInt(__replace(sugupTable.style.width, 'px', ''))) / 2+'px';
		var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace(sugupTable.style.height, 'px', ''))) / 2+'px';

		sugupLayer2.style.top     = tableTop;
		sugupLayer2.style.left    = tableLeft;
		sugupLayer2.style.width   = sugupTable.style.width;
		sugupLayer2.style.height  = sugupTable.style.height;
		sugupLayer2.style.display = '';
		sugupTable.style.display  = '';

		c.startDate.focus();
	}
}

function _save_client_sub(f){
	if (f.mode.value == 1){
		if (f.jumin1.value.length != 6){
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.jumin1.focus();
			return;
		}
		if (f.jumin2.value.length != 7){
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.jumin2.focus();
			return;
		}
		if (!_check_ssn('su', f.jumin1, f.jumin2, f.code)) return;
	}

	if (!__alert(f.name)) return;
	
	if (__replace(f.addr1.value) == ''){
		alert('주소를 입력하여 주십시오.');
		__helpAddress(f.postno1, f.postno2, f.addr1, f.addr2);
	}

	if (!checkDate(f.gaeYakFm.value)){
		alert('기관계약 시작일자 입력오류입니다. 확인후 다시 입력하여 주십시오.');
		f.gaeYakFm.focus();
		return;
	}

	if (__get_value(f.sugupStatus) != '1'){
		if (!checkDate(f.gaeYakTo.value)){
			alert('기관계약 종료일자 입력오류입니다. 확인후 다시 입력하여 주십시오.');
			f.gaeYakTo.focus();
			return;
		}
	}

	var kind = document.getElementsByName('kind_list[]');
	var kind_sel = false;

	for(var i=0; i<kind.length; i++){
		if (kind[i].checked){
			switch(kind[i].value){
			case '0':
				if (!_save_client_0(f)) return;
				break;
			case '1':
				break;
			case '2':
				break;
			case '3':
				break;
			case '4':
				break;
			case '5':
				break;
			}
			kind_sel = true;
			break;
		}
	}

	if (!kind_sel){
		alert('기관구분을 하나이상 선택하여 주십시오.');
		return;
	}

	if (f.startDate.value.split('.').join('') < f.startDate.tag){
		alert('적용시작일은 현재의 적용시작일보다 커야 합니다. 확인하여 주십시오.');
		document.sugupCheck.startDate.focus();
		return;
	}

	if (f.startDate.value.split('.').join('') == f.startDate.tag){
		f.history_yn.value = 'N';
	}

	f.action = 'save.php';
	f.submit();
}

function _show_save_client(){
	//document.getElementById('sDate').value = document.sugupCheck.startDate.value;
	document.f.startDate.value = document.sugupCheck.startDate.value;
	_save_client_sub(document.f);
}

function _show_cancel_client(){
	sugupLayer1.style.width  = 0;
	sugupLayer1.style.height = 0;
	sugupLayer2.style.width  = 0;
	sugupLayer2.style.height = 0;
	sugupTable.style.display = 'none';
}

function _set_kind(kind){
	var object = document.getElementById('kind_'+kind.value);

	if (kind.checked){
		object.style.display = '';

		if (kind.value == '0'){
			try{
				_show_pay_layer(__get_value(document.getElementsByName('yGupyeoKind')));
			}catch(e){}
		}
	}else{
		if (kind.value == '0'){
			try{
				for(var i=1; i<=4; i++){
					document.getElementById('layer_'+i+'_1').style.display = 'none';
					document.getElementById('layer_'+i+'_2').style.display = 'none';
				}
			}catch(e){}
		}

		object.style.display = 'none';
	}
}

function _show_pay_layer(gubun){
	var value = new Array();

	for(var i=1; i<=4; i++){
		value[i] = new Array();
		value[i][1] = false;
		value[i][2] = false;
	}

	if (gubun == '1Y'){
		value[1][1] = true;
		value[1][2] = true;
	}else if (gubun == '1N'){
		value[2][1] = true;
		value[2][2] = true;
	}else if (gubun == '3'){
		value[3][1] = true;
		value[3][2] = true;
	}else if (gubun == '4'){
		value[4][1] = true;
		value[4][2] = true;
	}

	_show_tbody_layer('tbody_1_1', 'layer_1_1', value[1][1]);
	_show_tbody_layer('tbody_1_2', 'layer_1_2', value[1][2]);

	_show_tbody_layer('tbody_2_1', 'layer_2_1', value[2][1]);
	_show_tbody_layer('tbody_2_2', 'layer_2_2', value[2][2]);

	_show_tbody_layer('tbody_3_1', 'layer_3_1', value[3][1]);
	_show_tbody_layer('tbody_3_2', 'layer_3_2', value[3][2]);

	_show_tbody_layer('tbody_4_1', 'layer_4_1', value[4][1]);
	_show_tbody_layer('tbody_4_2', 'layer_4_2', value[4][2]);
}

// 수급자 기관계약일자 확인
function _client_cont_date(client_stat, p_date_from, p_date_to){
	var f_date = __getObject(p_date_from);
	var t_date = __getObject(p_date_to);

	if (client_stat == '1'){
		f_date.style.backgroundColor = '#ffffff';
		f_date.value = f_date.tag;
		f_date.style.cursor = '';
		f_date.onfocus = function(){
			__replace(this, '-', '');
		}
		f_date.onclick = function(){
			_carlendar(this);
		}

		t_date.style.backgroundColor = '#eeeeee';
		t_date.value = '9999-99-99';
		t_date.style.cursor = 'default';
		t_date.onfocus = function(){
			this.blur();
		}
		t_date.onblur = null;
		t_date.onclick = null;
	}else{
		f_date.style.backgroundColor = '#eeeeee';
		f_date.value = f_date.tag;
		f_date.style.cursor = 'default';
		f_date.onfocus = function(){
			this.blur();
		}
		f_date.onblur = null;
		f_date.onclick = null;

		t_date.style.backgroundColor = '#ffffff';
		t_date.value = t_date.tag;
		t_date.style.cursor = '';
		t_date.onfocus = function(){
			__replace(this, '-', '');
		}
		t_date.onclick = function(){
			_carlendar(this);
		}
	}
}

// 급여한도액 설정
function _set_max_pay(object, code){
	object.value = __commaSet(getHttpRequest('../inc/_check.php?gubun=getMaxPay&code='+code));
	_set_my_pay();
}

function _set_my_yul(boninYul, code){
	var boninYul = document.getElementById('boninYul');

	if (code == '2' || code == '4'){
		boninYul.readOnly = false;
		boninYul.style.backgroundColor = '#ffffff';
		boninYul.onfocus = function(){
			this.select();
			this.style.borderColor='#0e69b0';
		}
	}else{
		boninYul.readOnly = true;
		boninYul.style.backgroundColor = '#eeeeee';
		boninYul.onfocus = function(){
			document.getElementById('injungNo').focus();
			this.style.borderColor='';
		}
	}
	boninYul.value = getHttpRequest('../inc/_check.php?gubun=getBoninYul&code='+code);
	_set_my_pay();
}

function _set_my_pay(){
	var kypyeoMax = __commaUnset(document.getElementById('kupyeoMax').value);
	var boninYul  = document.getElementById('boninYul').value;
	var kypyeo2 = cutOff(parseInt(kypyeoMax) * boninYul / 100);
	var kypyeo1 = cutOff(parseInt(kypyeoMax) - kypyeo2);

	document.getElementById('kupyeo1').value = __commaSet(kypyeo1);
	document.getElementById('kupyeo2').value = __commaSet(kypyeo2);
}

// 본인부담율에 금액
function _set_pay(){
	var totalPay  = document.getElementById('kupyeoMax'); //급여한도액
	var boninRate = document.getElementById('boninYul').value; //본인부담율
	var givePay   = document.getElementById('kupyeo1'); //정부지원금
	var boninPay  = document.getElementById('kupyeo2'); //본인부담금

	if (boninRate < 0 || boninRate > 15){
		alert('수급자본인부담율은 0~15까지 입력가능합니다. 확인하여 주십시오.');
		document.getElementById('boninYul').value = 0;
		document.getElementById('boninYul').focus();
		return false;
	}

	givePay.value  = __commaSet(parseInt(__commaUnset(totalPay.value), 10) - (parseInt(__commaUnset(totalPay.value), 10) * (boninRate / 100)));
	boninPay.value = __commaSet(parseInt(__commaUnset(totalPay.value), 10) - parseInt(__commaUnset(givePay.value), 10));
	
	var temp1 = parseInt(__commaUnset(givePay.value), 10) - parseInt(cutOff(__commaUnset(givePay.value)), 10);

	givePay.value  = __commaSet(cutOff(__commaUnset(givePay.value)));
	boninPay.value = __commaSet(parseInt(__commaUnset(boninPay.value), 10) + temp1);

	return true;
}

function _save_client_0(f){
	if (f.yoyangsa1Nm.value == ''){
		alert('주담당 요양보호사를 선택하여 주십시오.');
		__helpYoy(f.code.value,f.kind.value,document.getElementById('yoyangsa1'),document.getElementById('yoyangsa1Nm'));
		return false;
	}

	return true;
}

// 수급자 수정 선택
function _show_modify_client(p_target, p_code, p_kind, p_jumin){
	/*
	var body = __getObject('info_layer_body');
	_reg_client(p_code, p_kind, p_jumin, 'normal');
	*/
	var target	= __getObject(p_target);
	var x		= __getObjectLeft(target);
	var y		= __getObjectTop(target);
	var body	= __getObject('info_layer_body');
	var draw    = __getObject('info_draw_body');
	var table	= '	<table class="my_table my_green" style="width:330px; margin-top:2px;">'
				+ '		<colgroup>'
				+ '			<col width="70px">'
				+ '			<col width="40px">'
				+ '			<col width="60px">'
				+ '			<col width="40px">'
				+ '			<col width="70px">'
				+ '			<col>'
				+ '		</colgroup>'
				+ '		<tbody>'
				+ '			<tr>'
				+ '				<th class="center">수정구분</th>'
				+ '				<td class="center"><a href="#" onclick="_reg_client(\''+p_code+'\',\''+p_kind+'\',\''+p_jumin+'\',\'normal\');">일반</a></td>'
				+ '				<td class="center"><a href="#" onclick="_reg_client(\''+p_code+'\',\''+p_kind+'\',\''+p_jumin+'\',\'stat\');">수급상태</a></td>'
				+ '				<td class="center"><a href="#" onclick="_reg_client(\''+p_code+'\',\''+p_kind+'\',\''+p_jumin+'\',\'level\');">등급</a></td>'
				+ '				<td class="center"><a href="#" onclick="_reg_client(\''+p_code+'\',\''+p_kind+'\',\''+p_jumin+'\',\'gubun\');">수급자구분</a></td>'
				+ '				<td class="center"><a href="#" onclick="__hidden_object(\'info_layer_body\');">닫기</a></td>'
				+ '			</tr>'
				+ '		</tbody>'
				+ '	</table>';

	draw.innerHTML = table;
	body.style.left = x + 5;
	body.style.top  = y + target.offsetHeight + 4;
	body.style.display = '';
}

// 직원, 수급자 리포트 바디 레이어
//document.write('<div id="info_layer_body" style="position:absolute; display:none;"></div>');

document.write('<div id="info_layer_body" style="z-index:1100; position:absolute; width:auto; display:none;">');
document.write('	<div style="" class="ly_popup" style="border:none;">');
document.write('		<div class="shadow">');
document.write('			<div class="shadow_side">');
document.write('				<div class="shadow2">');
document.write('					<div class="shadow_side2">');
document.write('						<div id="info_draw_body">');
document.write('						</div>');
document.write('					</div>');
document.write('				</div>');
document.write('			</div>');
document.write('		</div>');
document.write('	</div>');
document.write('</div>');

document.write('<div id="layer_1"	style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_1_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_1_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="layer_2_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_2_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_3_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_3_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_3_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_3_4" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_3_5" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="layer_4_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="layer_4_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_layer_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_layer_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_layer_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_row_layer_1_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_1_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_row_layer_2_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_2_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_2_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_row_layer_3_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_3_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_3_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_row_layer_4_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_4_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_4_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

document.write('<div id="tbody_row_layer_5_1" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_5_2" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');
document.write('<div id="tbody_row_layer_5_3" style="z-index:1010; position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>');

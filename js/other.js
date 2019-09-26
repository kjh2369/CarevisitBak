// -----------------------------------------------------------------------------
// 공지사항 조회
function _getNoticeList(mPage){
	var mYear  = null;
	var mMonth = null;
	
	try{
		mYear  = document.f.curYear.value;
		mMonth = document.f.curMonth.value;
	}catch(e){}

	var URL = 'notice_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mPage:mPage,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				noticeBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 공지사항 등록
function _regNotice(mCode, mDate, mSeq, mPage){
	var URL = 'notice_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mDate:mDate,
				mSeq:mSeq,
				mPage:mPage
			},
			onSuccess:function (responseHttpObj) {
				noticeBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 공지사항 기관 전체 체크 구분
function _checkCenterKind(target, checked){
	var check = document.getElementsByName(target);
	
	for(var i=0; i<check.length; i++){
		check[i].checked = checked;
	}
}

function _checkKindAll(target1, target2){
	var check = document.getElementsByName(target1);
	var checked = true;
	
	for(var i=0; i<check.length; i++){
		if (!check[i].checked){
			checked = false;
			break;
		}
	}
	target2.checked = checked;
}

function _checkTarget(target, checked){
	for(var i=0; i<document.f.elements.length; i++){
		var eleCurr = document.f.elements[i];
		var eleName = eleCurr.getAttribute('name');
		var eleType = eleCurr.getAttribute('type');
		var eleValue = eleCurr.getAttribute('value');

		if (eleName.substring(0, target.length) == target){
			if (eleName.substring(eleName.length - 2, eleName.length) != '[]'){
				eleCurr.checked = checked;
				_checkCenterKind(eleName+'[]', checked);
			}
		}
	}
}

function _checkTargetChk(target){
	for(var i=0; i<document.f.elements.length; i++){
		var eleCurr = document.f.elements[i];
		var eleName = eleCurr.getAttribute('name');
		var eleType = eleCurr.getAttribute('type');
		var eleValue = eleCurr.getAttribute('value');
		var checked = true;

		if (eleName.substring(0, target.length) == target){
			if (eleName.substring(eleName.length - 2, eleName.length) != '[]'){
				alert(eleName);
				if (!eleCurr.checked){
					checked = false;
					break;
				}
			}
		}
	}

	document.getElementsByName(target).checked = checked;
}

// 공지사항 저장
function _setNoticeReg(){
	if (__checkRowNo('mYoy[]') == 0){
		alert('선택된 직원이 없습니다. 직원을 선택하여 주십시오.');
		return;
	}

	if (document.f.mSubject.value.split(' ').join('') == ''){
		alert('제목을 입력하여 주십시오.');
		document.f.mSubject.focus();
		return;
	}

	if (document.f.mContent.value.split(' ').join('') == ''){
		alert('내용을 입력하여 주십시오.');
		document.f.mContent.focus();
		return;
	}

	document.f.submit();
}

// 서비스건별 수당관리 리스트
function _getSudangList(mPage, object){
	var mAll = true;
	var mNursing = true;
	var mBath = true;
	var URL = 'sudang_list.php';

	try{
		if (object.name == 'chkAll'){
			mAll = true;
			mNursing = true;
			mBath = true;
		}else if (object.name == 'chkNursing'){
			mAll = false;
			mNursing = true;
			mBath = false;
		}else{
			mAll = false;
			mNursing = false;
			mBath = true;
		}
	}catch(e){}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mPage:mPage,
				mAll:mAll,
				mNursing:mNursing,
				mBath:mBath
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 서비스건별 수당관리 저장
function _setSudangList(mPage){
	var sudang = document.getElementsByName('sudangValue[]');
	var change = false;

	for(var i=0; i<sudang.length; i++){
		if (sudang[i].value != sudang[i].tag){
			change = true;
			break;
		}
	}

	if (!change){
		alert('변경된 수당이 없습니다.');
		return;
	}

	if (!confirm('수당을 변경하시겠습니까?')){
		return;
	}

	document.f.submit();
}

// 위치정보 화면 이동
function _setLocationInfo(){
	location.href = '../other/location.php';
}

// 위치정보 리스트 조회
function _getLocationInfo(mCode, mYear, mMonth, mDay, mYoyangsa){
	var URL = 'location_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mYear:mYear,
				mMonth:mMonth,
				mDay:mDay,
				mYoyangsa:mYoyangsa
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				myLocation.innerHTML = '';
			}
		}
	);

	__setInIljungYear(mCode, 'curYear', mYear);
	__setInInjungMonth('curMonth', mMonth);
	__setInInjungDay(mYear, mMonth, mDay, 'curDay');
	__setYoyIljungList(mCode, '', mYear+mMonth+mDay, 'yoyangsa', mYoyangsa, 'Y');
}

// 위치정보 상세내역 조회
function _getLocationDetail(mYoyangsa, mDate, mTime){
	myLocation.innerHTML = '준비중입니다.';
}

// 간이세액표 등록
function incomeReg(){
	if (!confirm("선택하신 엑셀파일을 업로드 하시겠습니까?")){
		document.f.reset();
		return;
	}

	document.f.action = "../other/income_upload.php";
	document.f.submit();
}

// 간이세액표 조회
function incomeSearch(){
	document.f.submit();
}

// 지사장수정
function _modifyManager(p_index){
	document.getElementById('newCode').value = document.getElementsByName('textCode[]')[p_index].innerHTML;
	document.getElementById('newPass').value = document.getElementsByName('textPass[]')[p_index].innerHTML;
	document.getElementById('newName').value = document.getElementsByName('textName[]')[p_index].innerHTML;
	_newManager();
}

// 지사중추가
function _newManager(){
	document.getElementById('newManager').style.display = '';
}

// 지사장저장
function _saveManager(){
	if (document.getElementById('newCode').value.split(' ').join('') == ''){
		alert('아이디를 입력하여 주십시오.');
		document.getElementById('newCode').focus();
		return;
	}

	/*
	var request = getHttpRequest('../inc/_check_class.php?check=isManager&code='+document.getElementById('newCode').value);

	if (request == 'Y'){
		alert('입력하신 아이디는 이미 사용중입니다. 다른 아이디를 입력하여 주십시오.');
		document.getElementById('newCode').value = '';
		document.getElementById('newCode').focus();
		return;
	}
	*/

	if (document.getElementById('newPass').value.split(' ').join('') == ''){
		alert('비밀번호를 입력하여 주십시오.');
		document.getElementById('newPass').focus();
		return;
	}

	if (document.getElementById('newName').value.split(' ').join('') == ''){
		alert('이름을 입력하여 주십시오.');
		document.getElementById('newName').focus();
		return;
	}

	var URL = '../inc/_check_class.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				check:'addManager',
				code:document.getElementById('newCode').value,
				pass:document.getElementById('newPass').value,
				name:document.getElementById('newName').value
			},
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'Y'){
					alert('입력하신 지사장을 저장하였습니다.');
					location.reload();
				}else{
					alert('데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}
			}
		}
	);
}

// 리스트
function _listCenter(p_page){
	document.f.page.value = p_page;
	document.f.submit();
}

// 기관 지사장 지정
function _saveCenterManager(index){
	var code    = document.getElementsByName('code[]')[index].value;
	var kind    = document.getElementsByName('kind[]')[index].value;
	var manager = document.getElementsByName('manager[]')[index].value;

	var URL = '../inc/_check_class.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				check:'appointManager',
				code:code,
				kind:kind,
				manager:manager
			},
			onSuccess:function (responseHttpObj) {
				//alert(responseHttpObj.responseText);
			}
		}
	);
}




/**************************************************

	바우처 목욕/간호 수당 리스트

**************************************************/
function _voucher_extra_pay_list(form, str_body){
	var obj_body = document.getElementById(str_body)
	var URL      = 'voucher_extra_pay_list.php';
	var params   = {'code':document.getElementById('code').value, 'find_svc':__object_get_value('find_svc')};
	var xmlhttp  = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function(responseHttpObj){
				obj_body.innerHTML = responseHttpObj.responseText;
				__init_form(form);
			},
			onFailure:function(responseHttpObj){
				
			}
		}
	);
}


/**************************************************

	직원조회 고객조회 전화번호조회 팝업

**************************************************/

function _find_pop(mode){
	
	var objModal = new Object();
	
	if(mode == 'M'){
		var url      = '../yoyangsa/mem_find_pop.php';
		var style    = 'dialogWidth:500px; dialogHeight:350px; dialogHide:yes; scroll:no; status:no';
	}else if(mode == 'C'){
		var url      = '../sugupja/client_find_pop.php';
		var style    = 'dialogWidth:330px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';
	}else {
		var url      = '../center/phone_find_pop.php';
		var style    = 'dialogWidth:400px; dialogHeight:310px; dialogHide:yes; scroll:no; status:no';
	}

	window.showModalDialog(url, objModal, style);

	var result = objModal.para;	

	if (!result) return;

	var arr = result.split('&'); 
	var val = new Array();

	for(var i=0; i<arr.length; i++){
		var tmp = arr[i].split('=');

		val[tmp[0]] = tmp[1];
	}
	
	var tmpForm = document.createElement('form');
	
	tmpForm.appendChild(__create_input('code', val['code']));
	tmpForm.appendChild(__create_input('jumin', val['jumin']));
	
	tmpForm.setAttribute('method', 'post');
	
	document.body.appendChild(tmpForm);

	tmpForm.target = '_self';
	if(mode == 'M'){
		tmpForm.action = '/yoyangsa/mem_reg.php?menuTopId=A';
	}else if(mode == 'C'){
		tmpForm.action = '/sugupja/client_reg.php?menuTopId=A';
	}

	tmpForm.submit();

}

function _find_body(mode){
	
	if(mode == 'M'){
		try{
			$.ajax({
				type: 'POST',
				url : '../yoyangsa/mem_find_body.php',
				data: {
					'name':$('#findName').val()
				,    'tel':$('#findTel').val()
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					$('#infoBody').html(xmlHttp);
				},
				error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}else if(mode == 'C'){
		try{
			$.ajax({
				type: 'POST',
				url : '../sugupja/client_find_body.php',
				data: {
					'name':$('#findName').val()
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					$('#infoBody').html(xmlHttp);
				},
				error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}else {
		try{
			$.ajax({
				type: 'POST',
				url : '../center/phone_find_body.php',
				data: {
					'tel':$('#findTel').val()
				},
				beforeSend: function (){
				},
				success: function (xmlHttp){
					$('#infoBody').html(xmlHttp);
				},
				error: function (){
				}
			}).responseXML;
		}catch(e){
		}
	}
}

//2014평가자료 신청 팝업
function _Report_pop(){
	var Top = 100;
	var Left = 100;
	window.open("../popup/report2014/","REPORT2014","width=600,height=347,Top="+Top+",left="+Left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
}


//설맞이 선물 세트
function _set_pop(){
	
	window.open('../../shop/set1.html','SHOP_PNG','left=0,top=0,width=757,height=790,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}


//설맞이 선물 세트
function _set_pop2(){
	
	window.open('../../shop/shop2.php','SHOP_PNG','left=0,top=0,width=757,height=805,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

//케어비지트질의응답 바로가기
function link_qna(){

	location.href = '../goodeos/board_list.php?board_type=1&menuTopId=I';

}

//방문간호지시서 요청 의료기관
function _nursingPop(){
	var width = 397;
	var height = 500;
	var top  = (window.screen.height - height) / 2;
	var left = (window.screen.width  - width)  / 2;

	window.open("../popup/nursing_request/","NURSING","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
}


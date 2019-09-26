var layerTargetCode  = null;
var layerTargetName  = null;
var layerTargetIndex = 0;

// -----------------------------------------------------------------------------
// 근무시간, 수당산정 조회
function getReportList(mIndex, mCode, mKind, mYear, mMonth, mYoy){
	if (mYoy == ''){
		alert('요양사를 선택하여 주십시오.');
		return;
	}
	
	if (mIndex == '1' || mIndex == '11'){
		var pageIndex = '1';
	}else{
		var pageIndex = mIndex;
	}
	
	var URL = 'report_list_'+pageIndex+'.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mIndex:mIndex,
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mYoy:mYoy
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function showReport(mIndex, mCode, mKind, mDate, mSuKey, mYoyKey){
	
	var URL = '../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	var popup = window.open(URL,'REPORT','width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');

	/*
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mDate:mDate,
				mJumin:mJumin,
				mYoy:mYoy
			},
			onSuccess:function (responseHttpObj) {
				//popup.document.write(responseHttpObj.responseText);
				//popip.setPos();
				//popup.myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
	*/
}

function showReport2(mIndex, mCode, mKind, mDate, mSuKey, mYoyKey){
	var URL = '../../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	var popup = window.open(URL,'REPORT','width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
	
	/*
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mDate:mDate,
				mJumin:mJumin,
				mYoy:mYoy
			},
			onSuccess:function (responseHttpObj) {
				//popup.document.write(responseHttpObj.responseText);
				//popip.setPos();
				//popup.myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
	*/
}

function showReport3(mIndex, mCode, mKind, mDate, mSuKey, mYoyKey){
	var URL = '../../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	
	location.href = URL;
}


// 리포트 탭메뉴
//function reportMenu(myBody, menu, tab){
	//document.getElementById('popMessage').style.display = '';
	//var URL = 'report_'+menu+'_'+tab+'.php';
function reportMenu(myBody, menu, tab){
	if (menu == '') return;

	var URL = 'report_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				menu:menu,
				tab:tab
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				//reportNavi.innerHTML = setReportNavi(menu, tab, 0);
				
				document.getElementById('popMessage').style.display = 'none';
				document.getElementById('view_download').style.display = '';

				if (document.getElementById('data_cnt').value == 0){
					alert('검색된 데이타가 없습니다.');
					reportMenu(myBody, menu, '');
					return;
				}
			}
		}
	);
}

// 리포트 네비게이션
function setReportNavi(navi){
	if (navi == undefined) return;

	reportNavi.innerHTML = navi;
}

function reportList(myBody, menu, tab, seq){
	if (seq == '0'){
		alert('준비중입니다.');
		return;
	}
}

// 리포트 탭메뉴 리스트
function reportDetailList(myBody, mMenu, mTab, mIndex, mCode, mKind, mYear, mMonth, mYoy,navi){
	if (mIndex == '1' || mIndex == '11'){
		if (mYoy == ''){
			alert('요양사를 선택하여 주십시오.');
			return;
		}
		var pageIndex = '1';
	}else{
		var pageIndex = mIndex;
	}

	//document.getElementById('popMessage').style.display = '';

	try{
		var mShow = document.getElementById('show').value;
	}catch(e){
		var mShow = false;
	}

	var URL = '../report/report_list_'+pageIndex+'.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mMenu:mMenu,
				mTab:mTab,
				mIndex:mIndex,
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mYoy:mYoy,
				mShow:mShow
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				//reportNavi.innerHTML = setReportNavi(mMenu, mTab, mIndex);
				setReportNavi(navi);
				
				//document.getElementById('popMessage').style.display = 'none';
				document.getElementById('view_download').style.display = '';
			}
		}
	);
}

// 리포트 팝업
function showMyReport(mIndex, mCode, mKind, mDate, mSuKey, mYoyKey, mSeq, mPrint){
	if (mIndex == '17' || 
		mIndex == '17_1' || 
		mIndex == '17_2' || 
		mIndex == '45' ||
		mIndex == '50'){
		var width = 900;
		var height = 700;
		var URL = '../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey+'&mSeq='+mSeq+'&mPrint='+mPrint;
	}else if(mIndex == '361'){
		var width = 700;
		var height = 900;
		var URL = '../report/report_show_36.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	}else if(mIndex == '761'){
		var width = 700;
		var height = 900;
		var URL = '../report/report_show_76.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	}else{
		var width = 700;
		var height = 900;
		var URL = '../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey+'&mSeq='+mSeq;
	}
	
	var popup = window.open(URL,'REPORT_POP','width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

/*
* mSugupja : 수급자 주민번호 
*/
// 리포트 입력
function inputReport(myBody, mMenu, mTab, mIndex, mCode, mKind, mYear, mMonth, mYoyangsa, mSugupja, mDate, mSeq){
	var URL = '../report/report_input_'+mIndex+'.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mMenu:mMenu,
				mTab:mTab,
				mIndex:mIndex,
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth,
				mYoyangsa:mYoyangsa,
				mSugupja:mSugupja,
				mDate:mDate,
				mSeq:mSeq
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;

				/*
				if (reportNavi.innerHTML.split('&nbsp;').join('') == ''){
					reportNavi.innerHTML = setReportNavi(mMenu, mTab, mIndex);
				}
				*/
				reportAddEvent(mIndex);

				document.getElementById('view_download').style.display = 'none';
			}
		}
	);
}

/*
 * 리포트 수정
 *   mYoyangsa : 요양사 주민번호
 */
function modifyReport(myBody, mMenu, mTab, mIndex, mCode, mKind, mSugupja, mWriteDate, mWriteSeq, mYoyangsa){
	var now = new Date();

	try{
		var mYear = document.getElementById('mYear').value;
	}catch(e){
		var mYear = now.getFullYear();
	}
	
	try{
		var mMonth = document.getElementById('mMonth').value;
	}catch(e){
		var mMonth = (now.getMonth()+1 < 10 ? '0' : '')+(now.getMonth()+1);
	}

	var URL = '../report/report_input_'+mIndex+'.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mMenu:mMenu,
				mTab:mTab,
				mIndex:mIndex,
				mCode:mCode,
				mKind:mKind,
				mSugupja:mSugupja,
				mWriteDate:mWriteDate,
				mWriteSeq:mWriteSeq,
				mYear:mYear,
				mMonth:mMonth,
				mYoyangsa:mYoyangsa

			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				reportAddEvent(mIndex);

				document.getElementById('view_download').style.display = 'none';
			}
		}
	);
}

/*
 * 리포트 삭제
 */

function deleteReport(myBody, mMenu, mTab, mIndex, mCode, mKind, mSugupja, mWriteDate, mWriteSeq, mYoyangsa, mCount){
	var now = new Date();
	var body  = __getObject(myBody);
	
	if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}

	try{
		var mYear = document.getElementById('mYear').value;
	}catch(e){
		var mYear = now.getFullYear();
	}
	
	try{
		var mMonth = document.getElementById('mMonth').value;
	}catch(e){
		var mMonth = (now.getMonth()+1 < 10 ? '0' : '')+(now.getMonth()+1);
	}

	var URL = '../report/report_delete.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mMenu:mMenu,
				mTab:mTab,
				mIndex:mIndex,
				mCode:mCode,
				mKind:mKind,
				mSugupja:mSugupja,
				mWriteDate:mWriteDate,
				mWriteSeq:mWriteSeq,
				mYear:mYear,
				mMonth:mMonth,
				mYoyangsa:mYoyangsa

			},
			onSuccess:function (responseHttpObj) {
				//responseHttpObj.responseText
				reportDetailList(myBody, mMenu, mMenu, mIndex);
				
			}
		}
	);

	if(mCount == '1'){
		body.innerHTML = '<a href="#" onclick="__my_modal(Array(\''+mKind+'\',\'\',\''+mYoyangsa+'\',\'\',\''+'report'+'\',\''+'input'+'\',\''+mIndex+'\', \''+'php'+'\',\''+'1'+'\',\''+'2'+'\'),\''+myBody+'\', \''+mIndex+'\', \''+mCode+'\', \''+mKind+'\', \''+mYoyangsa+'\' );">미작성</a>';
	}
}

// 리포트 삭제
function _report_del(code, report_menu, report_index, yymm, seq, ssn, year, month){
	var f = document.f;
	
	f.report_menu.value  = report_menu;
	f.report_index.value = report_index;
	f.yymm.value = yymm;
	f.seq.value  = seq;
	f.ssn.value  = ssn;
	f.Year.value = year;
	f.Month.value = month;
	
	f.action = '../reportMenu/report_delete.php';
	f.submit();
}

// 리포트 입력/수정시 추가사항
function reportAddEvent(mIndex){
	switch(mIndex){
	case '31':
		document.getElementById('pictureReg').style.top  = __getObjectTop(document.getElementById('pictureRegButton'));
		document.getElementById('pictureReg').style.left = __getObjectLeft(document.getElementById('pictureRegButton'));

		var f_me   = document.getElementsByName('family[]')[0];
		var f_body = document.getElementById('familyBody');
		var f_line = document.getElementById('son_line');
		var f_top  = document.getElementById('son_line_top');

		f_me.style.top    = __getObjectTop(f_body)  + (f_body.offsetHeight - f_me.offsetHeight) / 2;
		f_me.style.left   = __getObjectLeft(f_body) + (f_body.offsetWidth  - f_me.offsetWidth)  / 2;
		f_line.style.top  = f_me.offsetTop + f_me.offsetHeight - 15;
		f_line.style.left = f_me.offsetLeft + f_me.offsetWidth / 2;
		f_top.style.top   = f_line.offsetTop + f_line.offsetHeight;
		f_line.style.display = 'none';
		f_top.style.display  = 'none';

		document.getElementById('familyX_0').value = f_me.offsetLeft - __getObjectLeft(f_body);
		document.getElementById('familyY_0').value = f_me.offsetTop  - __getObjectTop(f_body);
		document.getElementById('familyTargetX_0').value = f_me.offsetLeft;
		document.getElementById('familyTargetY_0').value = f_me.offsetTop;

		if (document.getElementById('tempfamilyCount').value > 0){
			for(var i=1; i<=parseInt(document.getElementById('tempfamilyCount').value, 10); i++){
				if (document.getElementById('tempfamilyName_'+i).value == '부'){
					_make_div('familyBody', 'family[]', '부', 1);
				}else if (document.getElementById('tempfamilyName_'+i).value == '모'){
					_make_div('familyBody', 'family[]', '모', 0);
				}else if (document.getElementById('tempfamilyName_'+i).value == '배우자'){
					_make_div('familyBody', 'family[]', '배우자', 0);
				}else if (document.getElementById('tempfamilyName_'+i).value == '자녀(남)'){
					_make_div('familyBody', 'family[]', '자녀(남)', 1);
				}else if (document.getElementById('tempfamilyName_'+i).value == '자녀(여)'){
					_make_div('familyBody', 'family[]', '자녀(여)', 0);
				}
			}
		}
		
		break;
	case '77':
		_setBedsore(parseInt(document.getElementById('bedsoreX').value,10),parseInt(document.getElementById('bedsoreY').value, 10));
		break;
	}

	__init_form(document.f);
}

// 수급자 변경시 데이타 유무 확인
function checkSugupjaData(mType, mCode, mKind, mSugupja, mTarget){
	var request = getHttpRequest('../report/report_check.php?mGubun=1&mType='+mType+'&mCode='+mCode+'&mKind='+mKind+'&mSugupja='+mSugupja);

	switch(mType){
		case '29':
			if (request == 'Y'){
				mTarget[0].checked = false;
				mTarget[1].checked = true;
			}else{
				mTarget[0].checked = true;
				mTarget[1].checked = false;
			}
			break;
	}
}

// select list
function reportSugupjaList(mType, mCode, mKind, mTarget, mYear, mMonth){
	var request = getHttpRequest('../report/report_check.php?mGubun=2&mType='+mType+'&mCode='+mCode+'&mKind='+mKind+'&mYear='+mYear+'&mMonth='+mMonth);
	var list = request.split(';;');

	var select = mTarget;	
		select.innerHTML = '';
	
	__setSelectBox(select, '', '-수급자-');

	for(var i=0; i<list.length - 1; i++){
		var value = list[i].split('//');

		__setSelectBox(select, value[0], value[1]);
	}
}

// 리포트 입력 저장
function saveReport(myBody, mMenu, mTab, mIndex, mCode, mKind, form, action){
	if (action == ''){
		alert('준비중입니다.');
		return;
	}

	if (!checkReportForm(mIndex, form)){
		return;
	}
	
	form.action = action;
	form.submit();
}

function checkReportForm(mIndex, form){
	switch(mIndex){
	
	case '16':
		if (!checkDate(form.date.value)){
			alert('작성일자를 잘 못입력하셨습니다. 다시 입력하여 주십시오.');
			form.date.focus();
			return false;
		}

		if (form.writerJumin.value == ''){
			alert('작성자를 입력해 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'writerJumin','writer');
			return false;
		}

		break;


	case '18':
		if (!checkDate(form.date.value)){
			alert('발생일자를 잘 못입력하셨습니다. 다시 입력하여 주십시오.');
			form.date.focus();
			return false;
		}

		if (!checkDate(form.time.value)){
			alert('발생시간을 잘 못입력하셨습니다. 다시 입력하여 주십시오.');
			form.time.focus();
			return false;
		}

		if (form.yoyangsa.value == ''){
			alert('담당자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}

		if (form.sugupja.value == ''){
			alert('대상자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupja','strSugupName');
			return false;
		}
	
		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;

	case '19':
		if (!checkDate(form.writeDate.value)){
			alert('작성일자를 입력하여 주십시오.');
			form.writeDate.focus();
			return false;
		}

		if (form.sugupjaJumin.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupjaJumin','sugupja');
			return false;
		}

		if (form.takeoverJumin.value == ''){
			alert('인계자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'takeoverJumin','takeover');
			return false;
		}

		if (form.yoyangsaJumin.value == ''){
			alert('인수자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsaJumin','yoyangsa');
			return false;
		}
		break;

	case '24':
		if (!checkDate(form.writeDate.value)){
			alert('작성일자를 입력하여 주십시오.');
			form.writeDate.focus();
			return false;
		}

		if (!checkDate(form.writeTime.value)){
			alert('작성시간을 입력하여 주십시오.');
			form.writeDate.focus();
			return false;
		}
		
		if (form.place.value.split(' ').join('') == ''){
			alert('회의장소를 입력하여 주십시오.');
			form.place.focus();
			return false;
		}

		if (form.mcJumin.value == ''){
			alert('진행자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'mcJumin','mc');
			return false;
		}

		if (form.recorderJumin.value == ''){
			alert('기록자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'recorderJumin','recorder');
			return false;
		}

		break;

	case '27':
		if (!__alert(form.edu)) return;
		if (!__alert(form.date)) return;
		if (!__alert(form.time)) return;
		if (!__alert(form.subject)) return;
		if (!__alert(form.teacher)) return;
		break;

	case '29':

		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupja','strSugupName');
			return false;
		}

		if (form.yoyangsa.value == ''){
			alert('담당자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}

		if (!checkDate(form.writeDate.value)){
			alert('작성일자 오류입니다. 확인하여 주십시오.');
			form.writeDate.focus();
			return false;
		}
		
		var select_result=0;
		for( i=0; i<form.result1.length; i++){
			var e = form.result1[i];
			if(e.type == 'radio' && e.checked == true) {select_result = e.value;}
		}
		if(select_result == 0){
			alert("서비스 계획을 선택해주십시오.");
			return  false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;

	case '31':
		/*
		if (form.yoyangsa.value == ''){
			alert('작성자를 선택하여 주십시오.');
			form.yoyangsa.focus();
			return false;
		}
		if (form.sugupName.value == ''){
			alert('성명을 입력하여 주십시오.');
			form.sugupName.focus();
			return false;
		}
		if (form.sugupJumin.value == ''){
			alert('주민번호를 입력하여 주십시오.');
			form.sugupJumin.focus();
			return false;
		}
		*/
		if (form.yoyangsa.value == ''){
			alert('작성자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}

		if (form.sugupJumin.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupJumin','strSugupName');
			return false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupName.value    = __replace(str[0], ' ', '');
		form.gender.value    = __replace(str[1],' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;

	case '33':
		var select_janggi_party=0;
		var select_voucher_party=0;
		for( i=0; i<form.janggi_party.length; i++){
			var e = form.janggi_party[i];
			if(e.type == 'checkbox' && e.checked == true) {select_janggi_party = e.value;}
		}
		for( i=0; i<form.voucher_party.length; i++){
			var e = form.voucher_party[i];
			if(e.type == 'checkbox' && e.checked == true) {select_voucher_party = e.value;}
		}

		if(select_janggi_party == 0 && select_voucher_party == 0){
			alert("소속을 선택해주십시오.");
			return false;
		}
		
		if (!checkDate(form.mDate.value)){
			alert('작성일을 입력하여 주십시오.');
			form.mDate.focus();
			return false;
		}
		
		if (form.yoyCode.value == ''){
			alert('성명을 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyCode','strYoyangsaName');
			return false;
		}
		
		if (form.workFromDate.value == ''){
			alert('평가기간 시작일을 입력하여 주십시오.');
			form.workFromDate.focus();
			return false;
		}
		
		if (form.workToDate.value == ''){
			alert('평가기간 종료일을 입력하여 주십시오.');
			form.workToDate.focus();
			return false;
		}
		
		var mDate = form.mDate.value;
		var yipsail = form.yipsail.value;

		if (mDate < yipsail){
			alert('작성일이 입사일보다 작습니다. 확인하여 주십시오.');
			form.mDate.focus();
			return false;
		}

		if (form.workFromDate.value < form.yipsail.value){
			alert('근무시작일이 입사일보다 작습니다. 입력해주십시오.');
			form.workFromDate.focus();
			return false;
		}

		var workFromDate = form.workFromDate.value;
		var workToDate = form.workToDate.value;

		if (workToDate < workFromDate){
			alert('근무종료일이 근무시작일보다 작습니다. 확인하여 주십시오.');
			form.workToDate.focus();
			return false;
		}

		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;
		/*
		var inputs = document.getElementsByTagName("input");
		var radios = new Array();
		var idx = 0;
		
		for(var i=0; i<inputs.length;i++){
			if (inputs[i].type == "radio")
				radios[idx++] = inputs[i];
		}

		var result = 0;
		var flag = true;
		
		for(var i=0; i<radios.length;i++){
			if (radios[i].checked){
				flag = true;
				result += parseFloat(radios[i].value);
			}
			if(i%5==4){
				if(flag==true)
					flag = false;
				else {
					alert("모든 문항을 선택해 주세요.");
					return false;
				}
			}
		}
		*/
		break;
		
	case '37':
		if (form.yoyangsa.value == ''){
			alert('조사자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
		if (form.sugupja.value == ''){
			alert('성명을 입력하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupCode','strSugupName');
			return false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupName.value    = __replace(str[0], ' ', '');

		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;
	
	case '40':
		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupCode','strSugupName');
			return false;
		}
		if (form.yoyangsa.value == ''){
			alert('요양사를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
		if (form.address1.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address1.focus();
			return false;
		}
		if (form.address2.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address2.focus();
			return false;
		}
		
		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');

		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;


		break;
	
	case '41':
		if (form.sugupCode.value == ''){
			alert('대상자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupCode','strSugupName');
			return false;
		}
		
		if (form.yoyCode.value == ''){
			alert('평가자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyCode','strYoyangsaName');
			return false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;
		
		break;
	
	case '43':
		if (!checkDate(form.date.value)){
			alert('작성일을 입력하여 주십시오.');
			form.date.focus();
			return false;
		}

		var fromDate = form.fromDate.value;
		var toDate = form.toDate.value;

		if (toDate < fromDate){
			alert('조사시작일 이후로 입력해 주십시오.');
			form.toDate.focus();
			return false;
		}

		if (form.yoyCode.value == ''){
			alert('요양사를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyCode','strYoyangsaName');
			return false;
		}
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;

	case '45':

		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupja','strSugupName');
			return false;
		}

		if (form.yoyangsa.value == ''){
			alert('담당자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;
		break;

	case '47':
		if (!checkDate(form.mDate.value)){
			alert('상담일을 입력하여 주십시오.');
			form.mDate.focus();
			return false;
		}
		if (form.yoyCode.value == ''){
			alert('상담직원를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyCode','strYoyangsaName');
			return false;
		}
		if (form.manager.value == ''){
			alert('상담자를 선택하여 주십시오.');
			form.manager.focus();
			return false;
		}
		var mDate = form.mDate.value;
		var yipsail = form.yipsail.value;

		if (mDate < yipsail){
			alert('퇴사일이 입사일보다 작습니다. 확인하여 주십시오.');
			form.mDate.focus();
			return false;
		}
		
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;
	
	case'52':
		if (form.mDate.value == ''){
			alert('일자를 입력하여 주십시오.');
			form.mDate.focus();
			return false;
		}
		if (form.mTime.value == ''){
			alert('시간를 입력하여 주십시오.');
			form.mTime.focus();
			return false;
		}
		if (form.mPlace.value == ''){
			alert('장소를 입력하여 주십시오.');
			form.mPlace.focus();
			return false;
		}
		break;

	case '73':
		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupja','strSugupName');
			return false;
		}

		if (form.yoyangsa.value == ''){
			alert('담당자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
	
		if (!checkDate(form.writeDate.value)){
			alert('작성일자 오류입니다. 확인하여 주십시오.');
			form.writeDate.focus();
			return false;
		}

		var select_result=0;
		for( i=0; i<form.result1.length; i++){
			var e = form.result1[i];
			if(e.type == 'radio' && e.checked == true) {select_result = e.value;}
		}
		if(select_result == 0){
			alert("서비스 계획을 선택해주십시오.");
			return false;
		}
		
		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;
		
	case '74':
		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupCode','strSugupName');
			return false;
		}
		if (form.yoyangsa.value == ''){
			alert('요양사를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
		if (form.address1.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address1.focus();
			return false;
		}
		if (form.address2.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address2.focus();
			return false;
		}
		
		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');

		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		
		break;
		
	case '75':
		if (form.sugupja.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupCode','strSugupName');
			return false;
		}
		if (form.yoyangsa.value == ''){
			alert('요양사를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
		if (form.address1.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address1.focus();
			return false;
		}
		if (form.address2.value == ''){
			alert('주소를 입력하여 주십시오.');
			form.address2.focus();
			return false;
		}
		
		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');

		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		
		break;

	case '77':
		if (form.sugupjaJumin.value == ''){
			alert('수급자를 선택하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupjaJumin','sugupja');
			return false;
		}
		
		if (!checkDate(form.date.value)){
			alert('일자를 입력하여 주십시오.');
			form.date.focus();
			return false;
		}
		
		break;

	case '81':
		if (form.sugupja.value == ''){
			alert('수급자를 입력하여 주십시오.');
			__find_sugupja(form.mCode.value,form.mKind.value,'sugupja','strSugupName');
			return false;
		}

		if (form.yoyangsa.value == ''){
			alert('조사자를 선택하여 주십시오.');
			__find_yoyangsa(form.mCode.value,form.mKind.value,'yoyangsa','strYoyangsaName');
			return false;
		}
		
		if (!checkDate(form.writeDate.value)){
			alert('작성일자 오류입니다. 확인하여 주십시오.');
			form.writeDate.focus();
			return false;
		}

		var str = document.getElementById('strSugupName').innerHTML.split('/');

		form.sugupjaName.value    = __replace(str[0], ' ', '');
		form.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

		break;
	}
	return true;
}

// 텍스트 설정
function setMyTextBox(visible, target){
	if (visible){
		target.disabled=false;
		target.focus();
	}else{
		target.disabled=true;
	}
} 

function setMyTextBox2(visible, target1, target2){
	if (visible){
		target1.disabled=false;
		target2.disabled=false;
		target1.focus();
	}else{
		target1.disabled=true;
		target2.disabled=true;
	}
}

function setMyTextBox3(visible, target){
	if (visible){
		var value = false;
	}else{
		var value = true;
	}

	document.getElementById(target+'All').disabled = value;
	document.getElementById(target+'01').disabled = value;
	document.getElementById(target+'02').disabled = value;
	document.getElementById(target+'03').disabled = value;
	document.getElementById(target+'04').disabled = value;
	document.getElementById(target+'05').disabled = value;
	document.getElementById(target+'06').disabled = value;
	document.getElementById(target+'07').disabled = value;
	document.getElementById(target+'08').disabled = value;
	document.getElementById(target+'09').disabled = value;
	document.getElementById(target+'10').disabled = value;
	document.getElementById(target+'11').disabled = value;
	document.getElementById(target+'12').disabled = value;
}

// 주민번호판단 후 생년월일 및 성별 입력
function setJuminBS(jumin, birth, gender){
	jumin.value = __formatString(jumin.value,'######-#######');
	birth.value = __getBirthday(jumin.value);
	gender.value = __getGender(jumin.value);
}

// 로칼 사진 보여주기
function setPiture(object, pictureView){
	if (object.value != ''){
		var exp = object.value.split('.');

		if (exp[exp.length-1].toLowerCase() == 'jpg' || exp[exp.length-1].toLowerCase() == 'png' || exp[exp.length-1].toLowerCase() == 'gif'){
		}else{
			alert('jpg, png, gif의 이미지 파일을 선택하여 주십시오.');
			return;
		}
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

// 욕구평가 기록지 과겨병력
function setCareHistoryObject(gubun){
	if (gubun == 'Y'){
		document.getElementById('careHistory20').disabled = false;
		document.getElementById('careHistory30').disabled = false;
		document.getElementById('careHistory40').disabled = false;
		document.getElementById('careHistory50').disabled = false;
		document.getElementById('careHistory60').disabled = false;
		document.getElementById('careHistory70').disabled = false;
		document.getElementById('careHistory80').disabled = false;

		document.getElementById('careHistory20').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory30').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory40').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory50').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory60').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory70').style.backgroundColor = '#ffffff';
		document.getElementById('careHistory80').style.backgroundColor = '#ffffff';
	}else{
		document.getElementById('careHistory20').disabled = true;
		document.getElementById('careHistory30').disabled = true;
		document.getElementById('careHistory40').disabled = true;
		document.getElementById('careHistory50').disabled = true;
		document.getElementById('careHistory60').disabled = true;
		document.getElementById('careHistory70').disabled = true;
		document.getElementById('careHistory80').disabled = true;

		document.getElementById('careHistory20').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory30').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory40').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory50').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory60').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory70').style.backgroundColor = '#eeeeee';
		document.getElementById('careHistory80').style.backgroundColor = '#eeeeee';
	}
}


// 욕구평가 기록지 신규/기존 구분
function setOldAndNew(gubun){
	if (gubun == 'old'){
		document.getElementById('sugupja').style.display = '';
		document.getElementById('sugupLevel').style.display = 'none';
		document.getElementById('sugupLevelText').style.display = '';
		document.getElementById('sugupName').style.display = 'none';
		document.getElementById('injungNo').readOnly = true;
		document.getElementById('injungNo').onfocus = function(){document.getElementById('injungNo').blur();}
	}else{
		document.getElementById('sugupja').style.display = 'none';
		document.getElementById('sugupLevel').style.display = '';
		document.getElementById('sugupLevelText').style.display = 'none';
		document.getElementById('sugupName').style.display = '';
		document.getElementById('injungNo').readOnly = false;
		document.getElementById('injungNo').onfocus = function(){};
	}
	document.getElementById('sugupName').value = '';
	document.getElementById('injungNo').value = '';
}

function _findMonth(p_code, p_kind, p_year){
	var URL = '../inc/_check_class.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				check:'getFindPayMonth',
				code:p_code,
				kind:p_kind,
				year:p_year
			},
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
			}
		}
	);
}

// 인수인계 행 추가
function _addRow(index, id){
	var rowBody = document.getElementById("rowBody");
	var pos = 0;
	
	if (rowBody.childNodes.length == 1){
		pos = 3;
	}else{
		for (var i=0; i<rowBody.childNodes.length; i++){
			if (rowBody.childNodes[i].id == id){
				pos = i;
				break;
			}
		}
		pos += 3;
	}

	var newSeq = rowBody.childNodes.length+1;
	var id  = 'row_'+newSeq;
	var row_tr = document.getElementById('table_'+index).insertRow(pos);

	var row_td	= new Array();

	if (index == 19){
		var cols = 5;

		row_tr.id = id;

		for(var i=0; i<cols; i++){
			row_td[i] = document.createElement("td");
		}

		row_td[0].innerHTML = '<input name="content[]"	type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[1].innerHTML = '<input name="document[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[2].innerHTML = '<input name="other[]"	type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[3].innerHTML = '<input name="syncYN[]"	type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[4].innerHTML = '<span class="btn_pack m icon"><span class="add"></span><button type="button" onClick="_addRow('+index+',\''+id+'\');">추가</button></span> '
							+ '<span class="btn_pack m icon"><span class="delete"></span><button type="button" onClick="_deleteRow('+index+',\''+id+'\');">삭제</button></span>';
		row_td[0].className = 'td19';
		row_td[1].className = 'td19';
		row_td[2].className = 'td19';
		row_td[3].className = 'td19';
		
		for(var i=0; i<cols; i++){
			row_tr.appendChild(row_td[i]);
		}
	}else if (index == 77){
		var cols = 17;

		row_tr.id = id;

		for(var i=0; i<cols; i++){
			row_td[i] = document.createElement("td");
		}
		
		row_td[0].innerHTML = newSeq;
		row_td[0].style.textAlign = 'center';
		row_td[0].style.backgroundColor = '#fff';
		row_td[1].innerHTML = '<input name="date[]" type="text" value="" maxlength="8" class="date" style="background-color:#fbfbe7; border:0;" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, \'-\', \'\');" onBlu="_checkDate(this, \'date[]\');" onClick="_carlendar(this);" alt="report_77" tag="'+newSeq+'">';
		
		row_td[2].innerHTML = '<span id="str_bedsore_'+newSeq+'" onClick="_showLayer(\'bedsoreLayer\',\'bedsore\','+newSeq+');">--</span><input name="bedsoreX[]" type="hidden" value=""><input name="bedsoreY[]" type="hidden" value="">';
		row_td[2].style.textAlign = 'center';
		row_td[2].id = 'bedsore_'+newSeq;
		
		row_td[3].innerHTML = '<span id="str_degree_'+newSeq+'" onClick="_showLayer(\'degreeLayer\',\'degree\','+newSeq+');">--</span><input name="degree[]" type="hidden" value="">';
		row_td[3].style.textAlign = 'center';
		row_td[3].id = 'degree_'+newSeq;

		row_td[4].innerHTML = '<input name="size[]" type="text" value="" class="number" style="width:100%; background-color:#fbfbe7; border:0;" onKeyDown="__onlyNumber(this)">';

		row_td[5].innerHTML = '<span id="str_capacity_'+newSeq+'" onClick="_showLayer(\'capacityLayer\',\'capacity\','+newSeq+');">--</span><input name="capacity[]" type="hidden" value="">';
		row_td[5].style.textAlign = 'center';
		row_td[5].id = 'capacity_'+newSeq;

		row_td[6].innerHTML = '<span id="str_smell_'+newSeq+'" onClick="_showLayer(\'smellLayer\',\'smell\','+newSeq+');">--</span><input name="smell[]" type="hidden" value="">';
		row_td[6].style.textAlign = 'center';
		row_td[6].id = 'smell_'+newSeq;

		row_td[7].innerHTML = '<span id="str_handle_'+newSeq+'" onClick="_showLayer(\'handleLayer\',\'handle\','+newSeq+');">--</span><input name="handle[]" type="hidden" value="">';
		row_td[7].style.textAlign = 'center';
		row_td[7].id = 'handle_'+newSeq;

		row_td[8].innerHTML  = '<input name="position[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[9].innerHTML  = '<input name="skin[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[10].innerHTML = '<input name="nutrition[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[11].innerHTML = '<input name="hygiene[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[12].innerHTML = '<input name="edu[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[13].innerHTML = '<input name="other1[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[14].innerHTML = '<input name="other2[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		row_td[15].innerHTML = '<input name="other3[]" type="text" value="" style="width:100%; background-color:#fbfbe7; border:0;">';
		
		row_td[16].innerHTML = '<span class="btn_pack m icon"><span class="add"></span><button type="button" onClick="_addRow('+index+',\''+id+'\');">추가</button></span> '
							 + '<span class="btn_pack m icon"><span class="delete"></span><button type="button" onClick="_deleteRow('+index+',\''+id+'\');">삭제</button></span>';
		for(var i=0; i<cols-1; i++){
			row_td[i].className = 'td19';
		}
		
		for(var i=0; i<cols; i++){
			row_tr.appendChild(row_td[i]);
		}

		__makeDiv(document.getElementById('myBody'), 'mark[]', 'none', '<div style="position:absolute; margin:0; padding:0; left:12px; top:-8px; font-size:9px; color:#ff0000;">'+newSeq+'</div><img src="../image/ring.png">');
	}
}

// 행삭제
function _deleteRow(index, id){
	var row = document.getElementById(id);

	row.parentNode.removeChild(row);
}

// 욕창관리 부위 이미지
function _showLayer(p_layer, p_target, p_index){
	var layer  = __getObject(p_layer);
	var target = __getObject(p_target+'_'+p_index);

	_hiddenLayer();

	layerTargetCode  = document.getElementsByName(p_target+'[]')[p_index-1];
	layerTargetName  = document.getElementById('str_'+p_target+'_'+p_index);
	layerTargetIndex = p_index;

	layer.style.top  = (__getObjectTop(target) -7)+'px';
	layer.style.left = (__getObjectLeft(target)-6)+'px';
	layer.style.display = '';

	if (p_target == 'bedsore'){
		//document.getElementsByName('mark[]')[p_index-1].style.top = layer.style.top;
		//document.getElementsByName('mark[]')[p_index-1].style.left = layer.style.left;
		if (document.getElementsByName('mark[]')[p_index-1].tag == 'Y'){
			document.getElementsByName('mark[]')[p_index-1].style.display = '';
		}
	}
}

// 욕창 부위 선택
function _setBedsore(x, y){
	var offy = __getObjectTop(document.getElementById('bedsoreTD'));
	var offx = __getObjectLeft(document.getElementById('bedsoreTD'));
	var mark = document.getElementById('mark');
	var targetX = document.getElementById('bedsoreX');
	var targetY = document.getElementById('bedsoreY');

	if (x == 0 && y == 0) return;

	mark.style.top  = offy + y - 3;
	mark.style.left = offx + x + 1;
	mark.style.display = '';

	targetX.value = x;
	targetY.value = y;
}

// 욕창 레이어 보두 닫기
function _hiddenLayer(){
	document.getElementById('bedsoreLayer').style.display	= 'none';
	document.getElementById('degreeLayer').style.display	= 'none';
	document.getElementById('capacityLayer').style.display	= 'none';
	document.getElementById('smellLayer').style.display		= 'none';
	document.getElementById('handleLayer').style.display	= 'none';

	var mark = document.getElementsByName('mark[]');

	for(var i=0; i<mark.length; i++){
		mark[i].style.display = 'none';
	}
}

// 레이어 리스트 선택
function _selectLayerItem(code, name, tag){
	layerTargetCode.value = code;
	layerTargetName.innerHTML = name;
	layerTargetName.title = tag;

	_hiddenLayer();
}

function _checkDate(target, object){
	var object = document.getElementsByName(object);

	if (target.value == '') return false;

	for(var i=0; i<object.length; i++){
		if (target.tag != object[i].tag){
			if (__replace(target.value,'-','') == __replace(object[i].value,'-','')){
				alert('입력하신 일자는 이미 입력되어 있습니다. 다른 일자를 입력하여 주십시오.');
				target.focus();
				target.value = '';
				return false;
			}
		}
	}
	__getDate(target);

	return true;
}

// 
function _make_div(parent, id, text, gender){
	var parent = __getObject(parent);
	var object = document.getElementsByName(id);
	
	if (text == '부' || text == '모' || text == '배우자'){
		for(var i=0; i<object.length; i++){
			if (object[i].tag == text){
				alert(text+'는 이미 등록되어 있습니다. 확인하여 주십시오.');
				return;
			}
		}
	}
	
	var index = object.length;
	var me    = document.getElementsByName(id)[0];
	var div   = __makeDiv(parent, id, '', text+'<!--div style="position:absolute; top:-5px; right:-30px;" onClick="_remove_div(\''+index+'\');">X</div-->'
											  +'<input name="familyX_'		+index+'" type="hidden" value="0">'
											  +'<input name="familyY_'		+index+'" type="hidden" value="0">'
											  +'<input name="familyName_'	+index+'" type="hidden" value="">'
											  +'<input name="familyTargetX_'+index+'" type="hidden" value="0">'
											  +'<input name="familyTargetY_'+index+'" type="hidden" value="0">');

	div.style.width  = '80px';
	div.style.height = '60px';
	div.tag  = text;
	div.name = index;
	div.style.zIndex = 1100;

	if (text == '배우자'){
		div.className = 'f_p';
	}else{
		if (gender == 1){
			div.className = 'f_m';
		}else{
			div.className = 'f_f';
		}
	}

	var x = 0;
	var y = 0;
	var line_x1 = 0;
	var line_x2 = 0;

	if (text == '조부'){
		x = me.offsetLeft - div.offsetWidth / 2 - 10;
		y = me.offsetTop  - div.offsetHeight * 2 - 40;
	}else if (text == '조모'){
		x = me.offsetLeft + div.offsetWidth / 2 + 10;
		y = me.offsetTop  - div.offsetHeight * 2 - 40
	}else if (text == '부'){
		x = me.offsetLeft - div.offsetWidth / 2 - 10;
		y = me.offsetTop  - div.offsetHeight - 20;
	}else if (text == '모'){
		x = me.offsetLeft + div.offsetWidth / 2 + 10;
		y = me.offsetTop  - div.offsetHeight - 20;
	}else if (text == '배우자'){
		x = me.offsetLeft + me.offsetWidth + 10;
		y = me.offsetTop;
	}else if (text == '자녀(남)' || text == '자녀(여)'){
		object = document.getElementsByName(id);

		for(var i=0; i<object.length-1; i++){
			if (object[i].tag == '자녀(남)' || object[i].tag == '자녀(여)'){
				object[i].style.left = object[i].offsetLeft - (object[i].offsetWidth / 2) - 10;

				x = object[i].offsetLeft + object[i].offsetWidth + 20;
				y = object[i].offsetTop;

				document.getElementById('familyX_'		+object[i].name).value = object[i].offsetLeft - parseInt(__getObjectLeft(parent), 10);
				document.getElementById('familyY_'		+object[i].name).value = object[i].offsetTop  - parseInt(__getObjectTop(parent), 10);
				document.getElementById('familyTargetX_'+object[i].name).value = object[i].offsetLeft;
				document.getElementById('familyTargetY_'+object[i].name).value = object[i].offsetTop;

				var line = document.getElementsByName('f_line_'+object[i].name+'[]');
				
				for(var j=0; j<line.length; j++){
					line[j].style.left = object[i].offsetLeft + object[i].offsetWidth / 2;
					
					if (line_x1 == 0) line_x1 = object[i].offsetLeft + object[i].offsetWidth / 2;
					if (line_x1 > object[i].offsetLeft + object[i].offsetWidth / 2) line_x1 = object[i].offsetLeft + object[i].offsetWidth / 2;
				}
			}
		}
		
		if (x == 0 && y == 0){
			x = me.offsetLeft;
			y = me.offsetTop + div.offsetHeight + 20;
		}
	}

	div.style.left = x;
	div.style.top  = y;

	document.getElementById('familyX_'		+index).value = div.offsetLeft - parseInt(__getObjectLeft(parent), 10);
	document.getElementById('familyY_'		+index).value = div.offsetTop  - parseInt(__getObjectTop(parent), 10);
	document.getElementById('familyTargetX_'+index).value = div.offsetLeft;
	document.getElementById('familyTargetY_'+index).value = div.offsetTop;
	document.getElementById('familyName_'	+index).value = div.tag;

	div.onclick = function(){
		alert(this.offsetLeft+'/'+this.offsetTop+' - '+__getObjectLeft(parent)+'/'+__getObjectTop(parent)+' - '+document.getElementById('familyX_'+index).value+'/'+document.getElementById('familyY_'+index).value);
	}

	var f_line = document.getElementById('son_line');
	var f_top  = document.getElementById('son_line_top');
	var line = __makeDiv(parent, 'f_line_'+index+'[]', '', '');

	if (text == '부'){
		line.style.width  = 10;
		line.style.height = 50;
		line.style.left   = div.offsetLeft + div.offsetWidth;
		line.style.top    = div.offsetTop  + div.offsetHeight / 2;
		line.className    = 'border_tr';
	}else if (text == '모'){
		line.style.width  = 11;
		line.style.height = 50;
		line.style.left   = div.offsetLeft - line.offsetWidth;
		line.style.top    = div.offsetTop  + div.offsetHeight / 2;
		line.className    = 'border_tl';
	}else if (text == '배우자'){
		line.style.width  = 10;
		line.style.height = 25;
		line.style.left   = div.offsetLeft - line.offsetWidth;
		line.style.top    = div.offsetTop  + div.offsetHeight / 2;
		line.className    = 'border_t';
	}else if (text == '자녀(남)' || text == '자녀(여)'){
		f_line.style.display = '';
		
		line.style.width  = 1;
		line.style.height = 25;
		line.style.left   = div.offsetLeft + div.offsetWidth / 2;
		line.style.top    = div.offsetTop  - line.offsetHeight + 15;
		line.style.backgroundColor = '#000';

		line_x2 = div.offsetLeft + div.offsetWidth / 2 - line_x1;
	}
	line.style.zIndex = 1010;
	
	object = document.getElementsByName(id);

	var son_count = 0;

	for (var i=1; i<object.length; i++){
		if (object[i].tag == '자녀(남)' || object[i].tag == '자녀(여)'){
			son_count ++;
		}
	}

	if (son_count == 0){
		f_line.style.display = 'none';
		f_top.style.display  = 'none';
	}else if (son_count == 1){
		f_top.style.display  = 'none';
	}else{
		if (text == '자녀(남)' || text == '자녀(여)'){
			f_line.style.display = '';
			f_top.style.left  = line_x1;
			f_top.style.width = line_x2;
			f_top.style.display = '';
		}
	}

	document.getElementById('familyCount').value = parseInt(document.getElementById('familyCount').value, 10) + 1;

	/*
	div.style.width  = '104px';
	div.style.height = '181px';
	div.style.cursor = 'hand';

	if (gender == 'M'){
		div.className = 'gender_m';
	}else{
		div.className = 'gender_f';
	}

	div.onmousedown = function(){
		__move_on_off(this, 1, event);
	}

	div.onmouseup = function(){
		__move_on_off(this, 0, event);
	}

	div.onmousemove = function(){
		__move(parent, this, event);
	}
	*/
}

function _init_div(){
	var family = document.getElementsByName('family[]');
	var f_line = document.getElementById('son_line');
	var f_top  = document.getElementById('son_line_top');

	for(var i=family.length; i--; 1){
		_remove_div(family[i].name);
	}

	f_line.style.display = 'none';
	f_top.style.display  = 'none';
}

function _remove_div(index){
	var family = document.getElementsByName('family[]');
	var line   = document.getElementsByName('f_line_'+index+'[]');
	var x = 0;
	
	for (var i=0; i<line.length; i++){
		__removeDiv(line[i]);
	}

	for (var i=1; i<family.length; i++){
		if (family[i].name == index){
			__removeDiv(family[i]);
			return;
		}
	}
}

function _checkData(){
	var index = document.getElementById('mIndex').value;

	if (index == '77'){
		var URL = '../inc/_check_class.php';
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:{
					check:'getBedsoreYN',
					code:document.getElementById('mCode').value,
					kind:document.getElementById('mKind').value,
					jumin:document.getElementById('sugupjaJumin').value,
					date:document.getElementById('date').value
				},
				onSuccess:function (responseHttpObj) {
					var request = responseHttpObj.responseText;

					if (request == 'Y'){
						alert('입력하신 일자는 등록되어 있습니다. 다른 일자를 입력하여 주십시오.');
						_hiddenCarlendar();
						document.getElementById('date').value = '';
						document.getElementById('date').focus();
					}
				}
			}
		);
	}
}









//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


/*
 * 리포트 변수
 */
var __BODY__ = 'report_body';
var __REPORT_WIN__ = null;


/*
 * 리포트 리스트
 */
function _report_list(report_menu, find_report_nm){
	var URL     = '../reportMenu/report_list.php';
	var param   = {'report_menu':report_menu,'find_report_nm':find_report_nm};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:_response_http
		}
	);
	_report_show_close();
}

/*
 * 리포트별 리스트
 */
function _report_list_dtl(report_menu, report_index, code, navi, is_pop){
	if (is_pop == undefined) is_pop = 'N';
	
	var URL     = '../reportMenu/report_list_dtl.php';
	var param   = {'report_menu':report_menu,'report_index':report_index,'code':code,'is_pop':is_pop, 'year':document.getElementById('year').value, 'month':document.getElementById('month').value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				var body = document.getElementById(__BODY__);
					body.innerHTML = responseHttpObj.responseText;

				_set_report_navi(navi);
			}
		}
	);

	_report_show_close();
}

function _report_list_sub(code, report_menu, report_index, year, month){
	
	try{
		var is_pop = document.getElementById('is_pop').value;
	}catch(e){
		var is_pop = 'N';
	}
	
	if(report_index == '30_20_40_CLTLCMOR'){
		if(document.getElementById("inGbn_1").checked == true){
			var gbn = '1'
		}else {
			var gbn = '2'
		}
	}

	var URL     = '../reportMenu/report_list_dtl.php';
	var param   = {'code':code,'report_menu':report_menu,'report_index':report_index,'year':year,'month':month,'is_pop':is_pop,'in_gbn':gbn};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:_response_http
		}
	);

	_report_show_close();
}

function _report_search(code, report_menu, report_index, s_y_nm, counsel_nm, find_type, year, month){
	
	try{
		var is_pop = document.getElementById('is_pop').value;
	}catch(e){
		var is_pop = 'N';
	}
	
	var URL     = '../reportMenu/report_list_dtl.php';
	var param   = {'code':code,'report_menu':report_menu,'report_index':report_index,'find_name':s_y_nm,'find_counsel_name':counsel_nm,'find_type':find_type,'year':year, 'month':month, 'is_pop':is_pop};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:_response_http
		}
	);

	_report_show_close();
}


/*
 * 리포트 입력
 */
function _report_reg(code, report_menu, report_index, yymm, seq, ssn, year, month, copy_yn){
	var f    = document.f;
	var body = document.getElementById(__BODY__);

	try{
		var is_pop = document.getElementById('is_pop').value;
	}catch(e){
		var is_pop = 'N';
	}

	if (body == null){
		var width  = 875;
		var height = 700;
	
		var top  = (window.screen.height - height) / 2;
		var left = (window.screen.width  - width)  / 2;

		window.open('about:blank','REPORT_REG','top='+top+',left='+left+',width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
		
		f.report_menu.value  = report_menu;
		f.report_index.value = report_index;
		f.yymm.value = yymm;
		f.seq.value  = seq;
		f.ssn.value  = ssn;
		f.copy_yn.value = copy_yn;

		f.target = 'REPORT_REG';
		f.action = '../reportMenu/report_pop.php';
		f.submit();
		
		return;
	}

	if (ssn == undefined) ssn = '';
	
	/*
	var URL     = '../reportMenu/report_reg.php';
	var param   = {'code':code,'report_menu':report_menu,'report_index':report_index,'yymm':yymm,'seq':seq,'ssn':ssn,'year':year,'month':month,'is_pop':is_pop,'copy_yn':copy_yn};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				body.innerHTML = responseHttpObj.responseText;
				
				_set_report_menu(1);
				_init_form(report_index);
				__init_form(f);
			}
		}
	);
	
	_report_show_close();
	*/
	
	$.ajax({
		type :'POST'
	,	url  :'../reportMenu/report_reg.php'
	,	data :{
			'code':code
		,   'report_menu':report_menu
		,   'report_index':report_index
		,   'yymm':yymm
		,   'seq':seq
		,   'ssn':ssn
		,   'year':year
		,   'month':month
		,   'is_pop':is_pop
		,   'copy_yn':copy_yn
		}
	,	beforeSend:function(){
		}
	,	success:function(data){
			$(body).html(data);

			_set_report_menu(1);
			_init_form(report_index);
			__init_form(f);
			_report_show_close();
		}
	,	error:function(){
		}
	}).responseXML;
	
}

/*
 * 리포트 저장
 */
function _report_save(report_menu, report_id){
	var f = document.f;
	
	
	if (!_check_report(report_id)) return;
	
	f.target = '';
	f.action = '../reportMenu/report_save.php';
	f.submit();
}

function _report_show_list(report_menu, report_index, m_cd){
	var modal = showModalDialog('../reportMenu/report_pop_show_list.php?report_menu='+report_menu+'&report_index='+report_index+'&m_cd='+m_cd, window, 'dialogWidth:875px; dialogHeight:600px; dialogHide:yes; scroll:yes; status:no');

	if (modal == undefined){
		return;
	}else{
		alert(modal);
	}
	
	//window.open('../reportMenu/report_pop_show_list.php?report_menu='+report_menu+'&report_index='+report_index+'&m_cd='+m_cd, 'TEMP_POP');
}




/*
 * 리포트 출력
 */
function _report_show_pdf(paper_dir, params, report_id, mode){
	var f = document.f;
	
	switch(paper_dir){
		case 1: //세로
			var width  = 700;
			var height = 900;
			break;
		default: //가로
			var width  = 900;
			var height = 700;
	}
	
	var top  = (window.screen.height - height) / 2;
	var left = (window.screen.width  - width)  / 2;
	

	//장기요양급여비 납부 확인서 출력구분
	if(report_id == 'CLTLCMOR'){
		if(document.getElementById('inGbn_1').checked == true){
			var gbn = '1';
		}else if(document.getElementById('inGbn_2').checked == true){
			var gbn = '2';
		}else {
			var gbn = '1';
		}

		f.inGbn.value = gbn;
	}
	
	
	__REPORT_WIN__ = window.open('about:blank','REPORT_SHOW','top='+top+',left='+left+',width='+width+',height='+height+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	__REPORT_WIN__.onload = function(){
		alert('test');
	}
	
	if(report_id == 'HUREC'){ //인사기록카드
		if (params == 'blank'){
			var blank = params;
			var p     = '&report_id='+report_id;
		}else{
			var blank = '';
			var p     = '';
		
			document.getElementById('para_m_cd').value = params['m_cd'];
			
		}
		
		f.target = 'REPORT_SHOW';
		f.action = '../counsel/counsel_show.php?type='+'HUMAN2';
		f.submit();
		f.target = '_self';
	}else {
		if (params == 'blank'){
			var blank = params;
			var p     = '&report_id='+report_id;
		}else{
			var blank = '';
			var p     = '';
			
			for(var i in params){
				var obj = document.getElementById('para_'+i);
					obj.value = params[i];
			}
			
			if(report_id == '' || report_id == undefined){
			}else {
				//고객평가관리(서비스이용계약서)
				f.report_id.value = report_id; 
			}
			
			if(mode) f.para_mode.value = mode;
		}
		
		f.target = 'REPORT_SHOW';

		f.action = '../reportMenu/report_show.php?paper_dir='+paper_dir+'&blank='+blank+p;
		f.submit();
	}
	
}

function _report_show_close(){
	try{
		if (__REPORT_WIN__ != null){
			//__REPORT_WIN__.close();
			__REPORT_WIN__ = null;
		}
	}catch(e){
		//__show_error(e);
		__REPORT_WIN__ = null;
	}
}


function _report_show_word(report_id, params){
	var url = '../report/r_show_'+report_id+'.php';
	var gbn = '?';
	
	for(var i in params){
		url = url+gbn+i+'='+params[i];
		gbn = '&';
	}

	//var URL = '../../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	location.href = url;
}

function _report_show_excel(paper_dir){
	alert('준비중입니다.');
}

function _report_show_hwp(report_id, params){
	var url = '../report/r_show_'+report_id+'.php';
	var gbn = '?';
	
	for(var i in params){
		url = url+gbn+i+'='+params[i];
		gbn = '&';
	}

	//var URL = '../../report/report_show_'+mIndex+'.php?mIndex='+mIndex+'&mCode='+mCode+'&mKind='+mKind+'&mDate='+mDate+'&mSuKey='+mSuKey+'&mYoyKey='+mYoyKey;
	location.href = url;
}

//근로계약서(계약일자입력 레이어)
function _contract_dt_input_layer(p_target, params){
	
	var target	= __getObject(p_target);
	var x		= __getObjectLeft(target);
	var y		= __getObjectTop(target);
	var body	= __getObject('info_layer_body');
	var draw    = __getObject('info_draw_body');
	var url = '../yoyangsa/contract_dt_input.php';
	
	var xmlhttp = new Ajax.Request(
		url, {
			method:'post',
			parameters:params,
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
//근로계약서 리포트 출력
function _contract_report_show(report_id, dt, ssn){
	
	if(!dt){
	//	dt = document.getElementById('contract_dt').value
	}

	var param = {'m_cd':ssn, 'dt':dt};
	var body	= __getObject('info_layer_body');
	
	body.style.display = 'none';
	_report_show_word(report_id, param);
	
}

//서비스제공계약서(계약일자 레이어)
function _svc_contract_dt_get_layer(p_target, params){
	var target	= __getObject(p_target);
	var x		= __getObjectLeft(target);
	var y		= __getObjectTop(target);
	var body	= __getObject('info_layer_body');
	var draw    = __getObject('info_draw_body');
	var url = '../sugupja/svc_contract_dt_get.php';
	
	var xmlhttp = new Ajax.Request(
		url, {
			method:'post',
			parameters:params,
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

//서비스제공계약서 리포트 출력
function _svc_contract_report_show(report_id, kind, ssn, dt, svc_kind){
	

	if(report_id == 'CONTRACT'){
		//이용계약서(재가요양)
		var f = document.f;
		
		var w = 700;
		var h = 900;
		var l = (window.screen.width  - w) / 2;
		var t = (window.screen.height - h) / 2;
		
		var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
		var f   = document.f;
		
		f.report_id.value = report_id;
		f.kind.value = kind;
		f.ssn.value  = ssn;
		f.seq.value  = dt;
		
		f.target = 'SHOW_PDF';
		f.action = '../counsel/counsel_show.php?type='+svc_kind;
		f.submit();
		f.target = '_self';
	}else {
		//이용계약서(바우처);
		var param = {'c_cd':ssn, 'kind':kind, 'dt':dt};
		var body	= __getObject('info_layer_body');

		body.style.display = 'none';
	
		_report_show_pdf(1, param, report_id);	
	}
}


function _check_report(index){
	var f = document.f;

	switch(index){
		case 'QARR':
			if (f.reg_dt.value == ''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (__replace(f.business.value,' ','') == ''){
				alert('사업명을 입력하여 주십시오.');
				f.business.focus();
				return false;
			}
			break;

		case 'WTOTO':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.turn_cd.value == ''){
				alert('인계자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','turn_cd','turn_nm');
				return false;
			}

			if (f.take_cd.value == ''){
				alert('인수자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','take_cd','take_nm');
				return false;
			}

			if (f.turn_cd.value == f.take_cd.value){
				alert('인계.인수자를 서로 다르게 입력하여 주십시오.');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja(f.code.value,'','c_cd','c_nm');
				return false;
			}

			f.r_c_age.value   = document.getElementById('c_age').innerHTML;
			f.r_c_level.value = document.getElementById('c_level').innerHTML;

			break;

		case 'MONMR':
			if (f.reg_dt.value==''){
				alert('회의일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.reg_tm.value==''){
				alert('회의시간을 입력하여 주십시오.');
				f.reg_tm.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('진행자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.r_cd.value == ''){
				alert('기록자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','r_cd','r_nm');
				return false;
			}
			break;

		case 'MEMTR':
			/*
			if (!checkDate(f.reg_dt.value)){
				alert('상담일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('상담직원을 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.r_cd.value == ''){
				alert('진행자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','r_cd','r_nm');
				return false;
			}
			*/
			
			
			if (f.stress_ssn.value == ''){
				alert('직원을 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','stress_ssn','m_nm');
				return false;
			}
			
			if (f.stress_talker_cd.value == ''){
				alert('상담자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','stress_talker_cd','stress_talker_str');
				return false;
			}

			if (f.stress_date.value==''){
				alert('상담일자를 입력하여 주십시오.');
				f.stress_date.focus();
				return false;
			}
			
			if(f.copy_yn.value == 'Y'){
				if(f.temp_date.value == f.stress_date.value){
					alert('상담일자를 변경해주세요');
					f.stress_date.focus();
					return false;
				}
			}

			break;

		case 'MEMJAS':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}
			
			if(f.copy_yn.value == 'Y'){
				if(f.temp_dt.value == f.reg_dt.value){
					alert('작성일자를 변경해주세요');
					f.reg_dt.focus();
					return false;
				}
			}

			if (f.m_cd.value == ''){
				alert('직원을 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			f.r_m_nm.value  = document.getElementById('m_nm').innerHTML;
			f.r_j_nm.value  = document.getElementById('j_nm').innerHTML;
			f.r_rc_nm.value = document.getElementById('rc_nm').innerHTML;

			break;
		
		case 'MEMTAKE':
			
			if (f.reg_dt.value==''){
				alert('인수인계일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('인계자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}
			
			
			if (f.c_cd.value == ''){
				alert('수급자명을 입력하여 주십시오.');
				__find_client(f.code.value,["c_cd","s_nm","","","","","","","s_cd","s_addr"]);
				return false;
			}
			

			f.addr.value    = document.getElementById('s_addr').innerHTML;
		
			break;

		case 'MEMEDU':
			
			if (!__alert(f.edu)) return;
			if (!__alert(f.reg_dt)) return;
			if (!__alert(f.time)) return;
			if (!__alert(f.subject)) return;
			if (!__alert(f.teacher)) return;
			
			break;

		case 'CLTBSR':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if(f.copy_yn.value == 'Y'){
				if(f.temp_dt.value == f.reg_dt.value){
					alert('작성일자를 변경해주세요');
					f.reg_dt.focus();
					return false;
				}
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_client(f.code.value,['c_cd','c_nm','','','c_no','c_level','c_age']);
				return false;
			}

			f.r_c_no.value    = document.getElementById('c_no').innerHTML;
			f.r_c_level.value = document.getElementById('c_level').innerHTML;
			f.r_c_age.value   = document.getElementById('c_age').innerHTML;

			break;
		

		case 'CLTPST':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}
			
			if(f.copy_yn.value == 'Y'){
				if(f.temp_dt.value == f.reg_dt.value){
					alert('작성일자를 변경해주세요');
					f.reg_dt.focus();
					return false;
				}
			}

			if (f.m_cd.value == ''){
				alert('평가자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}
			break;

		case 'CLTDDT':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}
			
			if(f.copy_yn.value == 'Y'){
				if(f.temp_dt.value == f.reg_dt.value){
					alert('작성일자를 변경해주세요');
					f.reg_dt.focus();
					return false;
				}
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}
			break;

		case 'CLTMRRC':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}

			f.r_m_nm.value = document.getElementById('m_nm').innerHTML;
			f.r_c_no.value = document.getElementById('c_no').innerHTML;
			f.r_c_nm.value = document.getElementById('c_nm').innerHTML;

			break;

		case 'CLTMRRB':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}

			f.r_m_nm.value = document.getElementById('m_nm').innerHTML;
			f.r_c_no.value = document.getElementById('c_no').innerHTML;
			f.r_c_nm.value = document.getElementById('c_nm').innerHTML;

			break;

		case 'CLTPSR':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('작성자를 입력하여 주십시오.');
				__find_yoyangsa(f.code.value,'','m_cd','m_nm');
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}

			f.r_m_nm.value = document.getElementById('m_nm').innerHTML;
			f.r_c_no.value = document.getElementById('c_no').innerHTML;
			f.r_c_nm.value = document.getElementById('c_nm').innerHTML;

			break;
		
		case 'CLTSVCCTCC':
			
			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja3(f.code.value,'','c_cd','c_nm');
				return false;
			}
			
			
			f.r_c_nm.value = document.getElementById('c_nm').innerHTML;
			f.r_svc_dt.value = document.getElementById('svc_dt').innerHTML;
			
			break;

		case 'CLTREC':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}
			
			if (f.sugupjaJumin.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_sugupja(f.code.value,'','c_cd','c_nm');
				return false;
			}

			break;

		case 'CLTPLAN':
			if (f.reg_dt.value==''){
				alert('작성일자를 입력하여 주십시오.');
				f.reg_dt.focus();
				return false;
			}

			if (f.m_cd.value == ''){
				alert('담당자를 입력하여 주십시오.');
				_reportFindMember();
				return false;
			}

			if (f.c_cd.value == ''){
				alert('고객을 입력하여 주십시오.');
				__find_client( f.code.value ,['c_cd','c_nm','','','c_no','c_lvl','','','c_jumin']);
				return false;
			}
			
			var param = '';

			param +=  'memCd='+$('#m_cd').val();
			param += '&memNm='+$('#m_nm').text();
			param += '&memTel='+$('#m_tel').text();
			param += '&memEmail='+$('#m_email').text();
			param += '&cltCd='+$('#c_cd').val();
			param += '&cltNm='+$('#c_nm').text();
			param += '&cltLvl='+$('#c_lvl').text();
			param += '&cltNo='+$('#c_no').text();
			
			$('select[name="svcMemLc[]"]').each(function(){
				if ($(this).val() != ''){
					param += '&'+$(this).attr('id')+'='+$(this).val();
				}
			});

			var svcKind = '';

			$('input:hidden[name="svcKind[]"]').each(function(){
				if (svcKind.indexOf('/'+$(this).val()) < 0){
					svcKind += '/'+$(this).val();
				}
			});

			param += '&svcKind='+svcKind;

			$('#param').val(param);

			break;
		
		case 'CLTCRSRH' :
			if (f.sugupja.value == ''){
				alert('수급자를 선택하여 주십시오.');
				__find_sugupja(f.code.value,'','sugupja','strSugupName');
				return false;
			}
			if (f.yoyangsa.value == ''){
				alert('요양사를 선택하여 주십시오.');
				__find_yoyangsa(f.code.value,'','yoyangsa','strYoyangsaName');
				return false;
			}
			/*
			if (f.address1.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address1.focus();
				return false;
			}
			if (f.address2.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address2.focus();
				return false;
			}
			*/

			var str = document.getElementById('strSugupName').innerHTML.split('/');

			f.sugupjaName.value    = __replace(str[0], ' ', '');

			f.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

			break;
		
		case 'CLTBRSRH' :
			if (f.sugupja.value == ''){
				alert('수급자를 선택하여 주십시오.');
				__find_sugupja(f.code.value,'','sugupja','strSugupName');
				return false;
			}
			if (f.yoyangsa.value == ''){
				alert('요양사를 선택하여 주십시오.');
				__find_yoyangsa(f.code.value,'','yoyangsa','strYoyangsaName');
				return false;
			}
			/*
			if (f.address1.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address1.focus();
				return false;
			}
			if (f.address2.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address2.focus();
				return false;
			}
			*/
			var str = document.getElementById('strSugupName').innerHTML.split('/');
		
			f.sugupjaName.value    = __replace(str[0], ' ', '');

			f.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

			break;
		
		case 'CLTNRSRH' :
			if (f.sugupja.value == ''){
				alert('수급자를 선택하여 주십시오.');
				__find_sugupja(f.code.value,'','sugupja','strSugupName');
				return false;
			}
			if (f.yoyangsa.value == ''){
				alert('요양사를 선택하여 주십시오.');
				__find_yoyangsa(f.code.value,'','yoyangsa','strYoyangsaName');
				return false;
			}
			/*
			if (f.address1.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address1.focus();
				return false;
			}
			if (f.address2.value == ''){
				alert('주소를 입력하여 주십시오.');
				f.address2.focus();
				return false;
			}
			*/
			
			var str = document.getElementById('strSugupName').innerHTML.split('/');

			f.sugupjaName.value    = __replace(str[0], ' ', '');

			f.yoyangsaName.value = document.getElementById('strYoyangsaName').innerHTML;

			break;

		case 'CLTSTATRCD' :
			if (!$('#lblName').text()){
				alert('수급자를 선택하여 주십시오.');
				lfFindClient();
				return false;
			}
			/*
			var lbWrite = false;

			for(var i=1; i<=5; i++){
				if ($('#txtStat_'+i).val()){
					lbWrite = true;
				}
			}

			if (!lbWrite){
				alert('주 1회 이상 수급자의 상태변화, 특이사항, 급여제공에 따른 결과를 기록하여 주십시오.');
				return false;
			}
			*/

			var para =  'clientNm='+$('#lblName').text()
					 + '&clientCd='+$('#lblCode').text();

			for(var i=1; i<=5; i++){
				para += '&stat_'+i+'='+$('#txtStat_'+i).text()
					 +  '&result_'+i+'='+$('#txtResult_'+i).text()
					 +  '&memCd_'+i+'='+$('#lblCode_'+i).text()
					 +  '&memNm_'+i+'='+$('#lblName_'+i).text();
			}

			$('#txtPara').val(para);
			
			break;

		case 'CLTPLANCHN':
			if (!$('#lblName').text()){
				alert('수급자를 선택하여 주십시오.');
				lfFindClient();
				return false;
			}
			
			var i    = 1;
			var para = '0='+$('#lblName').text()+String.fromCharCode(2)+$('#lblCode').text();

			$('tr',$('#body')).each(function(){
				para += '&'+i+'='+$('#txtBDt_'+i).val()
					 +  String.fromCharCode(2)+$('#txtBFrom_'+i).val()
					 +  String.fromCharCode(2)+$('#txtBTo_'+i).val()
					 +  String.fromCharCode(2)+$('#txtADt_'+i).val()
					 +  String.fromCharCode(2)+$('#txtAFrom_'+i).val()
					 +  String.fromCharCode(2)+$('#txtATo_'+i).val()
					 +  String.fromCharCode(2)+$('#txtReason_'+i).val()
					 +  String.fromCharCode(2)+$('#lblName_'+i).text()
					 +  String.fromCharCode(2)+$('#lblCode_'+i).text();

				i ++;
			});

			$('#txtPara').val(para);

			break;

		default:
			alert('not find index : '+index);
			return false;
	}

	return true;
}

// 인수인계 행 추가
function _add_row(report_id, id){
	var rowBody = document.getElementById("row_body");
	var pos = 0;
	
	if (rowBody.childNodes.length == 1){
		pos = 2;
	}else{
		for (var i=0; i<rowBody.childNodes.length; i++){
			
			if (rowBody.childNodes[i].id == id){
				pos = i;
				break;
			}
		}
		pos += 2;
	}
	
	var newSeq = rowBody.childNodes.length+1;
	var id  = 'row_'+newSeq;
	var row_tr = document.getElementById('table_'+report_id).insertRow(pos);

	var row_td	= new Array();

	if (report_id == 'WTOTO'){
		var cols = 5;

		row_tr.id = id;

		for(var i=0; i<cols; i++){
			row_td[i] = document.createElement("td");
		}

		row_td[0].innerHTML = '<input name="content[]"	type="text" value="" style="width:100%;">';
		row_td[1].innerHTML = '<input name="document[]" type="text" value="" style="width:100%;">';
		row_td[2].innerHTML = '<input name="other[]"	type="text" value="" style="width:100%;">';
		row_td[3].innerHTML = '<input name="sync_yn[]"	type="text" value="" style="width:100%;">';
		row_td[4].innerHTML = '<span class="btn_pack m icon"><span class="add"></span><button type="button" onClick="_add_row(\''+report_id+'\',\''+id+'\');"></button></span> '
							+ '<span class="btn_pack m icon"><span class="delete"></span><button type="button" onClick="_delete_row(\''+id+'\');"></button></span>';

		row_td[0].className = 'center';
		row_td[1].className = 'center';
		row_td[2].className = 'center';
		row_td[3].className = 'center';
		row_td[4].className = 'left last';
		
		for(var i=0; i<cols; i++){
			row_tr.appendChild(row_td[i]);
		}
	}
}

// 행삭제
function _delete_row(id){
	var row = document.getElementById(id);

	row.parentNode.removeChild(row);
}

/*
 * 결과 출력
 */
function _response_http(responseHttpObj){
	var body = document.getElementById(__BODY__);
		body.innerHTML = responseHttpObj.responseText;

	_set_report_menu(2);

	__init_form(document.f);
}

/*
 * 욕창관리기록지 욕창부위 설정
 */
function _set_ps_position(x, y){
	var offy = __getObjectTop(document.getElementById('ps_body'));
	var offx = __getObjectLeft(document.getElementById('ps_body'));
	var mark = document.getElementById('mark');
	var targetX = document.getElementById('pos_x');
	var targetY = document.getElementById('pos_y');

	if (x == 0 && y == 0) return;

	mark.style.top  = offy + y + 23;
	mark.style.left = offx + x - 3;
	mark.style.display = '';

	targetX.value = x;
	targetY.value = y;
}

/*
 * 리포트 네비 설정
 */
function _set_report_navi(str){
	var navi = document.getElementById('report_navi');
		navi.innerHTML = str;
}

/*
 * 메뉴설정
 */
function _set_report_menu(type){
	var view_1 = document.getElementById('view_download');

	if (document.getElementById('opener').value == ''){
		var view_2 = document.getElementById('view_button');
	}else{
		var view_2 = document.getElementById('pop_button');
	}

	if (type == 1){
		view_1.style.display = 'none';
		view_2.style.display = '';
	}else{
		view_1.style.display = '';
		view_2.style.display = 'none';
	}
}

/*
 * 리포트별 초기화
 */
function _init_form(report_index){
	try{
		var tmp_id = report_index.split('_');
		var report_id = tmp_id[tmp_id.length-1];

		eval('_init_'+report_id+'()');
	}catch(e){
		//__show_error(e);
	}
}

/*
 * 욕구평가 기록지 초기화
 */
function _init_CLTBSR(){
	_set_enabled(['stat_other'],'stat','9');
	_set_enabled(['health_other'],'health','9');
	_set_enabled(['house_type_other'],'house_type','9');
	_set_enabled(['past_med_20','past_med_30','past_med_40','past_med_50','past_med_60','past_med_70','past_med_80'],'past_med_yn','Y');
	_set_enabled(['diagnosis_other_16'],'diagnosis_16','true');
	_set_enabled(['diagnosis_other_17'],'diagnosis_17','true');
	_set_enabled(['meal_other'],'meal_type','9');
	_set_enabled(['feces_stat_other'],'feces_stat','9');
}

function _init_MEMJAS(){
	//_curr_menu('menu[]','menu_sub[]',0);
	
	
	_set_enabled(document.getElementsByName('stress_other_1'), 'stress_1_9', 'Y');
	_set_enabled(document.getElementsByName('stress_other_2'), 'stress_2_13', 'Y');
	_set_enabled(document.getElementsByName('part_other_text'), 'part_99', 'Y');
}

function _init_CLTPST(){
	var point = 0;

	for(var i=1; i<=6; i++)	point += parseInt(__object_get_value('quest_'+i), 10);

	document.getElementById('total_point').innerHTML = point;
}

function _init_CLTPSR(){
	var pos_x = parseInt(document.getElementById('pos_x').value, 10);
	var pos_y = parseInt(document.getElementById('pos_y').value, 10);

	if (pos_x > 0 && pos_y){
		var offy = __getObjectTop(document.getElementById('ps_body'));
		var offx = __getObjectLeft(document.getElementById('ps_body'));
		var mark = document.getElementById('mark');
		
		mark.style.top  = offy + pos_y + 23;
		mark.style.left = offx + pos_x - 3;
		mark.style.display = '';
	}
}

/*
 * 객서 사용 설정
 */
function _set_enabled(target, object, value){
	for(var i=0; i<target.length; i++){
		if (value == 'true' || value == 'false'){
			__setEnabled(target[i], __object_is_checked(object) ? true : false);
		}else{
			__setEnabled(target[i], __object_get_value(object) == value ? true : false);		
		}
	}
}

function _set_m_over(id){
	var obj = document.getElementsByName(id);

	for(var i=0; i<obj.length; i++){
		obj[i].style.backgroundColor = '#f2f5ff';
	}
}

function _set_m_out(id){
	var obj = document.getElementsByName(id);

	for(var i=0; i<obj.length; i++){
		obj[i].style.backgroundColor = '#ffffff';
	}
}

function _set_m_click(id, index){
	var obj = document.getElementsByName('quest_'+id)[index];
		obj.checked = true;

	var pot = document.getElementById('point_'+id);
		pot.innerHTML = obj.value;

	var total_point = 0;

	for(var i=1; i<=12; i++){
		var pot = document.getElementById('point_'+i);

		total_point += parseInt(pot.innerHTML, 10);
	}

	var total_pot = document.getElementById('total_point');
		total_pot.innerHTML = total_point;

	return false;
}

function _curr_menu(m_menu, s_menu, index){
	var m = document.getElementsByName(m_menu);
	var s = document.getElementsByName(s_menu);

	for(var i=0; i<m.length; i++){
		if (i == index){
			m[i].style.fontWeight = 'bold';
			s[i].style.display    = '';
		}else{
			m[i].style.fontWeight = 'normal';
			s[i].style.display    = 'none';
		}
	}
}


/**************************************************

	평가관리 서브 리스트

**************************************************/
function _report_app_list(app_parent, app_body, app_report_menu, app_code, app_cd){
	var obj_body   = document.getElementById(app_body);
	
	var top  = __getObjectTop(app_parent) + app_parent.offsetHeight + 2;
	var left = __getObjectLeft(app_parent);
	
	var URL     = '../reportMenu/report_app_list.php';
	var param   = {'code':app_code,'report_menu':app_report_menu,'cd':app_cd,'body':app_body};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				obj_body.innerHTML     = responseHttpObj.responseText;
				obj_body.style.top     = top;
				obj_body.style.left    = left;
				obj_body.style.display = '';
			}
		}
	);
}


/*********************************************************

	급여제공계획서 수가

*********************************************************/
function _reportSetTime(idx){
	var fDt = $('#reg_dt').val();
	var fTm = $('#svcFTm_'+idx).val().split(':').join('');
	var fH = __str2num(fTm.substring(0,2));
	var fM = __str2num(fTm.substring(2,4));

	if (fTm == '') return;

	fTm = fH * 60 + fM;
	
	_reportFindSuga(idx, fTm, fDt);
}

function _reportFindSuga(idx, fromTime, date){
	var svc  = $('input:radio[name=\'svcYN\']:checked').val();
	var dt = (date != undefined ? date : false);
	var suga = __findSuga($('#code').val(),svc, dt, 'N');

	if (!suga) return;

	suga = __parseStr(suga);

	var tTm = __str2num(fromTime) + __str2num(suga['time']) * 30;
	var tH = Math.floor(tTm / 60);
	var tM = tTm % 60;

	tH = (tH < 10 ? '0' : '') + tH;
	tM = (tM < 10 ? '0' : '') + tM;

	var fD = __str2num($('#svcFDay_'+idx).val());
	var tD = __str2num($('#svcTDay_'+idx).val());
	var cnt = tD - fD + 1;

	if (cnt < 1) cnt = 1;

	$('#sugaCd_'+idx).val(suga['code']);
	$('#sugaNm_'+idx).val(suga['name']);
	$('#svcKind_'+idx).val(svc);
	$('#svcTTm_'+idx).val(tH+':'+tM);
	$('#svcCnt_'+idx).val(cnt);

	_reportSetSuga(idx, suga['cost']);
}

function _reportRemoveSuga(idx){
	$('#sugaCd_'+idx).val('');
	$('#sugaNm_'+idx).val('');
	$('#svcKind_'+idx).val('');
	$('#svcTTm_'+idx).val('');
	$('#svcCnt_'+idx).val(0);

	_reportSetSuga(idx, 0);
}

function _reportRemoveRow(para){
	var menu = $('#reportId').val();

	if (menu == 'CLTPLAN'){
		//장기요양급여 제공계획서
		if (para['gbn'] == 'svc'){
			$('#sugaCd_'+para['idx']).val('');
			$('#sugaNm_'+para['idx']).val('');
			$('#svcKind_'+para['idx']).val('');
			$('#svcTTm_'+para['idx']).val('');
			$('#svcCnt_'+para['idx']).val(0);

			$('#svcMon_'+para['idx']).val('');
			$('#svcFDay_'+para['idx']).val('');
			$('#svcTDay_'+para['idx']).val('');
			$('#svcFTm_'+para['idx']).val('');
			$('#svcTTm_'+para['idx']).val('');
			$('#svcSuga_'+para['idx']).val(0);
			$('#svcCnt_'+para['idx']).val(0);
			$('#svcAmt_'+para['idx']).val(0);
			
			$('#sugaCd_'+para['idx']).val('');
			$('#sugaNm_'+para['idx']).val('');
			$('#svcKind_'+para['idx']).val('');

			$('#svcMemNM_'+para['idx']).val('');
			$('#svcMemCD_'+para['idx']).val('');
			$('#svcMemRel_'+para['idx']).val('');
				
			_reportSetMemLicense({'idx':para['idx'],'jumin':''});
			_reportSumAmt($('#svcAmt_'+para['idx']));
		}else if (para['gbn'] == 'item'){
			$('#itemNm_'+para['idx']).val('');
			$('#itemCd_'+para['idx']).val('');
			$('#itemRent_'+para['idx']).attr('checked','checked');
			$('#itemDt_'+para['idx']).val('');
			$('#itemAmt_'+para['idx']).val(0);

			_reportSumAmt($('#itemAmt_'+para['idx']));
		}else if (para['gbn'] == 'bipay'){
			$('#expenseNm_'+para['idx']).val('');
			$('#expenseDt_'+para['idx']).val('');
			$('#expenseCt_'+para['idx']).val(0);
			$('#expenseCnt_'+para['idx']).val(0);
			$('#expenseAmt_'+para['idx']).val(0);

			_reportSumAmt($('#expenseAmt_'+para['idx']));
		}
	}
}

function _reportSetSuga(idx, suga){
	$('#svcSuga_'+idx).val(__num2str(suga));
	_reportSetAmount(idx);
	_reportSumAmt($('#svcAmt_'+idx));
}

function _reportSetAmount(idx){
	$('#svcAmt_'+idx).val(__num2str(__str2num($('#svcSuga_'+idx).val()) * __str2num($('#svcCnt_'+idx).val())));
}


/*********************************************************

	합계금액

*********************************************************/
function _reportObjSum(target, obj, cal, next){
	var amt = 0;

	for(var i=0; i<obj.length; i++){
		if (i == 0){
			amt = __str2num($('#'+obj[i]).val());	
		}else{
			switch(cal){
				case '*':
					amt *= __str2num($('#'+obj[i]).val());	
					break;
				case '/':
					amt /= __str2num($('#'+obj[i]).val());	
					break;
				case '-':
					amt -= __str2num($('#'+obj[i]).val());	
					break;
				default:
					amt += __str2num($('#'+obj[i]).val());	
					break;
			}
		}
	}

	$('#'+target).val(__num2str(amt));
}
function _reportSumAmt(obj){
	var target = $(obj).attr('tagName').toLowerCase()+':'+$(obj).attr('type')+'[name="'+$(obj).attr('name')+'"]';
	var amt = 0;

	$(target).each(function(){
		amt += __str2num($(this).val());
	});

	target = $(obj).attr('name').split('[]').join('')+'Tot';
	$('#'+target).text(__num2str(amt));
}


/*********************************************************

	급여제공계획서 담당자 조회

*********************************************************/
function _reportFindMember(idx){
	var val = __findMember($('#code').attr('value'));

	if (!val) return;
	
	if (!idx){
		$('#m_nm').text(val['name']);
		$('#m_cd').val(val['jumin']);
		$('#m_tel').text(__getPhoneNo(val['tel']));
		$('#m_email').text(val['email']);
	}else{
		$('#svcMemNM_'+idx).val(val['name']);
		$('#svcMemCD_'+idx).val(val['jumin']);
		
		_reportSetMemLicense({'idx':idx,'jumin':val['jumin']});

		$.ajax({
			type: 'POST'
		,	url : '../find/_find_mem_family.php'
		,	beforeSend: function(){
			}
		,	data: {
				'code' : $('#code').attr('value')
			,	'cCd'  : $('#c_cd').val()
			,	'mCd'  : val['jumin']
			}
		,	success: function(result){
				var val = __parseStr(result);

				if (!val){
					$('#svcMemRel_'+idx).val('');
					return;
				}
				
				$('#svcMemRel_'+idx).val(val['nm']);
			}
		,	complete: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}
}


/*********************************************************

	직원 자격증 리스트

*********************************************************/
function _reportSetMemLicense(para){
	var menu = $('#reportId').val();

	if (menu == 'CLTPLAN'){
		//장기요양급여 제공계획서
		$.ajax({
				type: 'POST'
			,	url : '../find/_find_mem_license.php'
			,	beforeSend: function(){
				}
			,	data: {
					'code'  : $('#code').attr('value')
				,	'jumin' : para['jumin']
				}
			,	success: function(result){
					var html = '<select id=\'svcMemLc_'+para['idx']+'\' name=\'svcMemLc[]\' style=\'width:110px;\'>'
							 + result
							 + '</select>';

					$('#svcMemLcBd_'+para['idx']).html(html);
					$('#svcMemLc_'+para['idx']+' option:eq(1)').attr('selected','selected');
				}
			,	complete: function(result){
				}
			,	error: function (){
				}
			}).responseXML;
	}
}


/*********************************************************

	급여제공계획서 고객정보

*********************************************************/
function _reportFindClient(){
	var val = __findClient($('#code').attr('value'));
	
	if (!val) return;

	val = __parseStr(val);
	
	var jumin = getHttpRequest('../inc/_ed_code.php?type=2&value='+val['jumin']).substring(0,7);

	jumin = jumin.substring(0.6)+'-'+jumin.substring(6,7)+'******';

	$('#c_nm').text(val['name']);
	$('#c_cd').val(val['jumin']);
	$('#c_jumin').text(jumin);
	$('#c_lvl').text(val['lvl_nm']);
	$('#c_no').text(val['app_no']);
}


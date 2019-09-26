// -----------------------------------------------------------------------------
// 수가 저장
function _setSuga(){
	if (document.suga.mCode2.value == ''){
		alert('수가코드를 입력하여 주십시오.');
		document.suga.mCode2.focus();
		return;
	}

	if (document.suga.sCode.value == ''){
		alert('기관수가코드를 입력하여 주십시오.');
		document.suga.sCode.focus();
		return;
	}

	if (document.suga.sugaCont.value == ''){
		alert('수가명을 입력하여 주십시오.');
		document.suga.sugaCont.focus();
		return;
	}

	if (document.suga.sDate.value.split('.').join('') < document.suga.sDate.tag){
		alert('적용시작일은 현재의 적용시작일보다 커야 합니다. 확인하여 주십시오.');
		document.suga.sDate.value = document.suga.sDate.tag;
		document.suga.sDate.focus();
		return;
	}

	if (document.suga.sDate.value.split('.').join('') != document.suga.sDate.tag){
		document.suga.flagChange.value = 'Y';
	}

	document.suga.submit();
}

// 관리자 수가 리스트
function _getAdminGugaList(mPage){
	document.suga.mCode.value = '';
	document.suga.mCenterName.value = '';
	
	_getSugaList(mPage);
}

// 수가 리스트
function _getSugaList(mPage){
	var mCode = null;
	var mCenterName = null;
	var URL = 'suga_list.php';
	
	try{
		mCode = document.suga.mCode.value;
		mCenterName = document.suga.mCenterName.value;
	}catch(e){}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mCenterName:mCenterName,
				mPage:mPage
			},
			onSuccess:function (responseHttpObj) {
				sugaBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
	sugaHistory.innerHTML = '';
}

// 수가 상세
function _sugaDetail(mCode, mSugaCode, mPage){
	var URL = 'suga_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mSugaCode:mSugaCode,
				mPage:mPage
			},
			onSuccess:function (responseHttpObj) {
				sugaBody.innerHTML = responseHttpObj.responseText;
				if (mSugaCode != ''){
					_getSugaHistory(mCode, mSugaCode);
				}
			}
		}
	);
}

// 수가 히스토리
function _getSugaHistory(mCode, mSugaCode){
	var URL = 'suga_history.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mSugaCode:mSugaCode
			},
			onSuccess:function (responseHttpObj) {
				sugaHistory.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수가 복사
function _setSugaCopy(){
	var help = showModalDialog('../inc/_help.php?r_gubun=centerList', window, 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no');

	if (typeof(help) != 'object'){
		return false;
	}

	if (!confirm('수가정보를 '+help[3]+'의 기관으로 복사하시겠습니까?')){
		return false;
	}

	var copy = showModalDialog('suga_copy.php?mCode='+help[0], window, 'dialogWidth:300px; dialogHeight:100px; dialogHide:yes; scroll:no; status:no');

//	bodyLayer.style.width = document.body.offsetWidth;
//	bodyLayer.style.height = document.body.offsetHeight;
}

function _add_suga_code(){
	var request = getHttpRequest('../inc/_check_class.php?check=exist_suga_to_copy');

	if (request == 1){
		alert('추가된 수가를 모두 북사하였습니다.');
	}else{
		alert('추가된 수가를 복사중 오루가 발생하였습니다.');
	}
}
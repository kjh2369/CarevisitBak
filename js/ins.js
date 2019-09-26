// -----------------------------------------------------------------------------
// 책임보험 가입내역
function _insJoinList(){
	/*
	var fromDate = document.f.mFromYear.value + "-"
				 + document.f.mFromMonth.value + "-"
				 + document.f.mFromDay.value;
	var toDate = document.f.mToYear.value + "-"
			   + document.f.mToMonth.value + "-"
			   + document.f.mToDay.value;
	var diff = diffDate("d", fromDate, toDate);

	if (diff < 0){
		alert('검색 종료일이 검색 시작일보다 작을 수 없습니다. 확인하여 주십시오.');
		return;
	}
	*/
	var URL = 'ins_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				stat:document.f.stat.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function _insExcel(){
	document.f.action = 'ins_excel.php';
	document.f.submit();
}

// 책임보험 교체신청 리스트
function _insChangeList(){
	var URL = 'change_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				fromDate:document.f.mFromYear.value + document.f.mFromMonth.value + document.f.mFromDay.value,
				toDate:document.f.mToYear.value + document.f.mToMonth.value + document.f.mToDay.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 책임보험 교체신청
function _insChangeReg(){
	var help = showModalDialog('change_reg.php?mCode='+document.getElementById('mCode').value, window, 'dialogWidth:450px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no');
	//var help = window.open('change_reg.php?mCode='+document.getElementById('mCode').value, '', 'dialogWidth:450px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no');
	if (help == 'Y') location.reload();
}

// 환급리스트
function _insRefundList(){
	var URL = 'refund_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				fromDate:document.f.mFromYear.value + document.f.mFromMonth.value + document.f.mFromDay.value,
				toDate:document.f.mToYear.value + document.f.mToMonth.value + document.f.mToDay.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 환급신청
function _insRefundReg(){
	var help = showModalDialog('refund_reg.php?mCode='+document.getElementById('mCode').value, window, 'dialogWidth:450px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no');
	//var help = window.open('refund_reg.php?mCode='+document.getElementById('mCode').value, '', 'dialogWidth:450px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no');
	if (help == 'Y') location.reload();
}

// 가입리스트
function _insRequestList(){
	var URL = 'request_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				fromDate:document.f.mFromYear.value + document.f.mFromMonth.value + document.f.mFromDay.value,
				toDate:document.f.mToYear.value + document.f.mToMonth.value + document.f.mToDay.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 가입신청
function _insRequestReg(){
	var help = showModalDialog('request_reg.php?mCode='+document.getElementById('mCode').value, window, 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no');
	//var help = window.open('request_reg.php?mCode='+document.getElementById('mCode').value, '', 'dialogWidth:450px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no');
	if (help == 'Y') location.reload();
}
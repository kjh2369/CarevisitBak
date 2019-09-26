var popupAccount = null;

// -----------------------------------------------------------------------------
// 미수금내역
function getPersonAccountList(myBody, mCode, mKind, mYear){
	var URL = 'person_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mYear:mYear
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 미수금 상세 내역
function getPersonAccountDetail(myDetail, mCode, mKind, mYear, mMonth){
	/*
	if (myTr.style.display != 'none'){
		myTr.style.display = 'none';
		return;
	}
	*/
	var URL = 'person_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mYear:mYear,
				mMonth:mMonth
			},
			onSuccess:function (responseHttpObj) {
				myDetail.innerHTML = responseHttpObj.responseText;
				
				/*
				myDiv.innerHTML = responseHttpObj.responseText;

				try{document.getElementById(myTr+'01').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'02').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'03').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'04').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'05').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'06').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'07').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'08').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'09').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'10').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'11').style.display = 'none';}catch(e){}
				try{document.getElementById(myTr+'12').style.display = 'none';}catch(e){}

				document.getElementById(myTr+mMonth).style.display = '';
				*/
			}
		}
	);
}

// 미수입금처리
function getNotAccountList(mCode, mKind, mSugup){
	var URL = 'not_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mSugup:mSugup
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				/*
				try{
					document.getElementById('totalAmount').innerHTML = __commaSet(parseInt(document.getElementById('inAmt').value) + parseInt(document.getElementById('noAmt').value));
					document.getElementById('inAmount').innerHTML    = __commaSet(document.getElementById('inAmt').value);
					document.getElementById('noAmount').innerHTML    = __commaSet(document.getElementById('noAmt').value);

					var row = document.getElementById("rowList");

					if (row.childNodes.length == 1){
						rowMaster.style.display = 'none';
					}else{
						rowMaster.style.display = '';
					}
				}catch(e){}
				*/
			}
		}
	);
}

// 미수금 입금처리
function popupDeposit(mCode, mKind, mKey){
	var width  = 500;
	var height = 400;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popupAccount = window.open('popup_deposit.php?mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey, 'POPUP_DEPOSIT', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 입금처리
function setDeposit(){
	var deposit = __commaUnset(document.f.deposit.value);
	var miAmt   = document.getElementsByName('miAmt[]');
	var amt     = document.getElementsByName('amt[]');
	var newAmt  = deposit;
	var setAmt  = 0;
	
	if (newAmt > 0){
		for(var i=0; i<miAmt.length; i++){
			if (newAmt > 0){
				if (newAmt >= parseInt(miAmt[i].value)){
					setAmt  = parseInt(miAmt[i].value);
				}else{
					setAmt  = newAmt;
				}
				newAmt = newAmt - setAmt;
				amt[i].value = __commaSet(setAmt);
			}else{
				break;
			}
		}
	}else{
		miAmt[0].value = parseInt(miAmt[0].value) + newAmt;
		amt[0].value = __commaSet(newAmt);
	}

	document.f.deposit.value = __commaSet(newAmt);
}

// 입금저장
function regDeposit(){
	var depositType = document.getElementById('depositType').value;
	var amt = document.getElementsByName('amt[]');
	var regFlag = false;

	for(var i=0; i<amt.length; i++){
		if (parseInt(__commaUnset(amt[i].value)) != 0){
			regFlag = true;
			break;
		}
	}

	if (depositType == '89'){
		if (!confirm('입력하신 금액을 선납입금으로 저정하시겠습니까?')){
			return;
		}
	}else{
		if (!regFlag){
			alert('입금내역이 없습니다.');
			return;
		}

		if (!confirm('입금내역을 저장하시겠습니까?')){
			return;
		}
	}

	document.f.submit();
}

// 입금내역리스트
function getDepositList(mCode, mKind, mGubun){
	var URL = 'deposit_list_20110527.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mGubun:mGubun
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;

				myUnpaid.innerHTML = '';
				myDetail.innerHTML = '';
			}
		}
	);
}

// 입금내역 상세
function getDeppsitDetailList(p_type, p_year, p_month){
	var URL = 'deposit_detail_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mType:p_type,
				mCode:document.getElementById('curCode').value,
				mKind:document.getElementById('curKind').value,
				mYear:p_year,
				mMonth:p_month
			},
			onSuccess:function (responseHttpObj) {
				myDetail.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 미수금내역리스트
function getNotDepositList(p_body, p_code, p_kind, p_year, p_month){
	var URL = 'not_deposit_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month
			},
			onSuccess:function (responseHttpObj) {
				myDetail.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 입금영수증 프린트
function printReceipt(p_code, p_kind, p_ym, p_date, p_type, p_sugupja){
	var width  = 600;
	var height = 300;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	popupAccount = window.open('print_deposit_bill.php?mCode='+p_code+'&mKind='+p_kind+'&mYM='+p_ym+'&mDate='+p_date+'&mType='+p_type+'&mSugupja='+p_sugupja, 'POPUP_DEPOSIT_BILL', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 선납금액 조회
function setDepositAmount(target, mCode, mKind, mKey, mType){
	if (mType != '81'){
		return;
	}

	var request = getHttpRequest('../inc/_check.php?gubun=getDepositAmount&pCode='+mCode+'&pKind='+mKind+'&pKey='+mKey);
		request = __commaSet(request);

	if (typeof(target) == 'object'){
		try{
			target.value = request;
		}catch(e){
			target.innerHTML = request;
		}
	}else{
		return request;
	}
}

// 미수금상세
function unpaidList(){
	var URL = 'deposit_unpaid_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.getElementById('curCode').value,
				mKind:document.getElementById('curKind').value
			},
			onSuccess:function (responseHttpObj) {
				myUnpaid.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}
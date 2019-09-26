// 거래처 등록
function _custReg(p_code){
	if (p_code.length == 7){
		_buttonShow('modify');
	}else{
		_buttonShow('reg');
	}
	
	var body = document.getElementById('body');
	var page = document.getElementById('page').value;
	var URL = 'cust_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:p_code,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 거래처 리스트
function _custList(){
	_buttonShow('list');

	var body		= document.getElementById('body');
	var page		= document.getElementById('page').value;
	var findName	= document.getElementById('findName').value;
	var URL = 'cust_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				findName:findName,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;

				if (document.getElementById('rowCount').value == '0'){
					document.getElementById('findName').value = '';
				}
			}
		}
	);
}

function _custSetPage(p_page){
	document.getElementById('page').value = p_page;
	_custList();
}

// 거래처 저장
function _custSave(){
	if (!__alert(document.f.name)) return;
	
	document.f.action = 'cust_reg_ok.php';
	document.f.submit();
}

// 거래처 삭제
function _custDelete(){
	if (!confirm('거래처를 삭제하시겠습니까?')){
		return;
	}
	var code = document.getElementById('code').value;
	var URL = 'cust_delete_ok.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:code
			},
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
				_custList();
			}
		}
	);
}

// 거래처 버튼활성화
function _buttonShow(p_type){
	try{
		switch(p_type){
		case 'reg':
			document.getElementById('terms').style.display		= 'none';
			document.getElementById('new').style.display		= 'none';
			document.getElementById('save').style.display		= '';
			document.getElementById('save').style.marginRight	= '-4px';
			document.getElementById('delete').style.display		= 'none';
			document.getElementById('list').style.display		= '';
			break;
		case 'modify':
			document.getElementById('terms').style.display		= 'none';
			document.getElementById('new').style.display		= 'none';
			document.getElementById('save').style.display		= '';
			document.getElementById('save').style.marginRight	= '';
			document.getElementById('delete').style.display		= '';
			document.getElementById('list').style.display		= '';
			break;
		case 'list':
			document.getElementById('terms').style.display		= '';
			document.getElementById('new').style.display		= '';
			document.getElementById('save').style.display		= 'none';
			document.getElementById('save').style.marginRight	= '';
			document.getElementById('delete').style.display		= 'none';
			document.getElementById('list').style.display		= 'none';
			break;
		}
	}catch(e){
	}
}

// 품목리스트
function _goodsList(){
	_buttonShow('list');

	var body = document.getElementById('body');
	var page = document.getElementById('page').value;
	//var findName	= document.getElementById('findName').value;
	var URL = 'goods_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				page:page
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;

				//if (document.getElementById('rowCount').value == '0'){
				//	document.getElementById('findName').value = '';
				//}
			}
		}
	);
}

// 품목등록
function _goodsReg(p_code){
	if (p_code.length == 7){
		_buttonShow('modify');
	}else{
		_buttonShow('reg');
	}
	
	var body = document.getElementById('body');
	var page = document.getElementById('page').value;
	var URL = 'goods_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:p_code,
				page:page
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 품목저장
function _goodsSave(){
	if (!__alert(document.f.name)) return;
	if (!__alert(document.f.custCode1)) return;
	if (!__alert(document.f.cost1)) return;
	
	document.f.action = 'goods_reg_ok.php';
	document.f.submit();
}

// 거래처 찾기
function _findCustomer(p_custCode, p_custName, p_code1, p_code2){
	var code1 = __getObject(p_code1);
	var code2 = __getObject(p_code2);
	var modal = showModalDialog('find_customer.php?code1='+code1.value+'&code2='+code2.value, window, 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');

	if (typeof(modal) != 'object'){
		return
	}

	var custCode = __getObject(p_custCode);
	var custName = __getObject(p_custName);

	custCode.value = modal[0];
	custName.value = modal[1];
}
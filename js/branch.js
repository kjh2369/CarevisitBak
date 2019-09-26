// 지사등록화면 출력
function _branchReg(p_code, p_type, p_mode){
	var URL = 'branch_reg_sub.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:p_code,
				type:p_type,
				mode:p_mode
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById('myBody').innerHTML = responseHttpObj.responseText;
				__init_form(document.f);
			}
		}
	);
}

// 지사코드유무판단
function _checkBranchCode(p_mark, p_code){
	for(var i=p_code.length+1; i<=3; i++){
		p_code = '0'+p_code;
	}

	var URL = 'find_branch_code.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_mark+p_code
			},
			onSuccess:function (responseHttpObj) {
				var count = responseHttpObj.responseText;

				if (count != 0){
					alert('입력하신 코드는 이미 사중입니다. 다른 코드를 입력하여 주십시오.');
					document.f.code.value = document.f.code.tag;
					document.f.code.focus();
					return false;
				}
				
				if (document.f.code.value != p_code){
					document.f.code.value = p_code;
				}
				return true;
			}
		}
	);
}

// 지사저장
function _branchRegOk(){
	/*
	if (document.f.pass.value == ''){
		alert('비밀번호를 입력하여 주십시오.');
		document.f.pass.focus();
		return;
	}
	*/

	if (document.f.name.value == ''){
		alert('지사명을 입력하여 주십시오.');
		document.f.name.focus();
		return;
	}

	if (document.f.manager.value == ''){
		alert('대표자명을 입력하여 주십시오.');
		document.f.manager.focus();
		return;
	}

	if (document.f.stat.value == '1'){
		if (!checkDate(document.f.joinDate.value)){
			alert('가입일자 오류입니다. 확인하여 주십시오.');
			document.f.joinDate.focus();
			return;
		}
	}else if (document.f.stat.value == '9'){
		if (!checkDate(document.f.quitDate.value)){
			alert('해지일자 오류입니다. 확인하여 주십시오.');
			document.f.quitDate.focus();
			return;
		}

		if (diffDate('d', document.f.joinDate.value, document.f.quitDate.value) < 0){
			alert('해지일이 가입일보다 작을 수 없습니다. 확인하여 주십시오.');
			document.f.quitDate.focus();
			return;
		}
	}

	document.f.action = 'branch_reg_ok.php';
	document.f.submit();
}

// 지사조회
function _branchList(){
	var URL = 'branch_list_sub.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById('myBody').innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 담당자등록
function _inchageReg(p_branch, p_person, p_type, p_mode){
	var temp = document.domain.split('.');
	var URL = 'inchange_reg_sub.php';
	var body = 'myBody';
	
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_branch,
				person:p_person,
				type:p_type,
				mode:p_mode
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById(body).innerHTML = responseHttpObj.responseText;
				__init_form(document.f);
			}
		}
	);
}

// 담장자저장
function _inchangeRegOk(){
	if (document.f.branch.value == ''){
		alert('지사를 선택하여 주십시오.');
		document.f.branch.focus();
		return;
	}

	if (document.f.id.value == ''){
		alert('아이디를 입력하여 주십시오..');
		document.f.id.focus();
		return;
	}

	if (document.f.pwd.value == ''){
		alert('비밀번호를 입력하여 주십시오..');
		document.f.pwd.focus();
		return;
	}

	if (document.f.personName.value == ''){
		alert('담당자명을 입력하여 주십시오..');
		document.f.personName.focus();
		return;
	}

	if (document.f.stat.value == '1'){
		if (!checkDate(document.f.joinDate.value)){
			alert('가입일자 오류입니다. 확인하여 주십시오.');
			document.f.joinDate.focus();
			return;
		}
	}else if (document.f.stat.value == '9'){
		if (!checkDate(document.f.quitDate.value)){
			alert('해지일자 오류입니다. 확인하여 주십시오.');
			document.f.quitDate.focus();
			return;
		}

		if (diffDate('d', document.f.joinDate.value, document.f.quitDate.value) < 0){
			alert('해지일이 가입일보다 작을 수 없습니다. 확인하여 주십시오.');
			document.f.quitDate.focus();
			return;
		}
	}

	document.f.action = '../branch/inchange_reg_ok.php';
	document.f.submit();
}

// 담당자코드조회
function _getPersonCode(p_branch){
	var URL = 'find_inchange_code.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_branch
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById('personCode').value = responseHttpObj.responseText;
				//document.getElementById('idPersonCode').innerHTML = p_branch+responseHttpObj.responseText;
				//document.getElementById('personPass').focus();

				_chk_id(document.getElementById('mode').value);
			}
		}
	);
}

// 담당자조회
function _inchangeList(mode){
	var temp = document.domain.split('.');
	var host = temp[0];

	if (host == 'manager'){
		var URL = 'person_list_sub.php';
		var body = 'personList';
	}else{
		var URL = 'inchange_list_sub.php';
		var body = 'myBody';
	}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mode:mode
			},
			onSuccess:function (responseHttpObj) {
				document.getElementById(body).innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 지사리스트
function _b2cBranchList(){
	var URL = 'branch_tree.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
			},
			onSuccess:function (responseHttpObj) {
				var request = responseHttpObj.responseText;

				if (request != ''){
					document.getElementById('myBranchTree').innerHTML = responseHttpObj.responseText;
				}else{
					alert('등록된 지사가 없습니다. 지사등록을 먼저 실행하여 주십시오.');
				}
			}
		}
	);
}

// 지사 연결 기관 리스트
function _b2cCenterList(p_body, p_window){
	var body = __getObject(p_body);
	var URL = 'b2c_center_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
			},
			onSuccess:function (responseHttpObj) {
				body.innerHTML = responseHttpObj.responseText;

				if (typeof(p_window) == 'object'){
					p_window.self.close();
				}
			}
		}
	);
}

// 지사 연결 기관 추가
function _b2cCenterAdd(p_center, p_kind){
	var center = (p_center != undefined ? p_center : '');
	var kind = (p_kind != undefined ? p_kind : '');

	var width  = 800;
	var height = 340;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	var URL = 'b2c_center_add.php?center='+center+'&kind='+kind;
	var popup = window.open(URL,'centerAdd','width='+width+',height='+height+',left='+left+',top='+top+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no');
}

// 지사 연결 기관 삭제
function _b2cCenterDelete(){
	if (!__checkRowCount()){
		return;
	}
	if (!confirm('선택하신 데이타를 삭제하시겠습니까?')){
		return;
	}
	document.f.action = 'b2c_center_delete_ok.php';
	document.f.submit();
}

// 선택지사 담당자 리스트
function _b2cPersonList(target, p_branch){
	var target = __getObject(target);
	var URL = '../branch/b2c_inchange_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_branch
			},
			onSuccess:function (responseHttpObj) {
				target.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function _chk_branch(com){
	var branch = document.getElementById('branch');
	var gbn    = (com == 'company' ? '본사' : '지사');

	if (branch.value == ''){
		alert(gbn+'를 선택하여 주십시오.');
		branch.focus();
		document.getElementById('id').value = '';
	}
}

/*
 * 아이디 체크
 */
function _chk_id(com){
	var branch = document.getElementById('branch').value;
	var id     = document.getElementById('id');
	var gbn    = (com == 'company' ? 510 : 610);
	var rst = getHttpRequest('../inc/_chk_ssn.php?id='+gbn+'&code='+branch+'&ssn='+id.value);

	if (rst == 'Y'){
		id.value = '';
		id.focus();

		return false;
	}

	return true;
}
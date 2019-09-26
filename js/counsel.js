/**************************************************

	리포트 리스트

**************************************************/
function _client_proc_counsel_list(body){
	var code  = document.getElementById('code').value;
	var year  = document.getElementById('year').value;
	var month = document.getElementById('month').value;

	var f_su_name = document.getElementById('find_su_name').value;
	var f_counsel_name = document.getElementById('find_counsel_name').value;
	var f_type = document.getElementById('find_type').value;

	var URL   = '../counsel/client_proc_counsel.php';
	var para  = {'mode':'list','code':code,'year':year,'month':month,'f_su_name':f_su_name,'f_counsel_name':f_counsel_name,'f_type':f_type};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:para,
			onSuccess:function (responseHttpObj) {
				body.style.margin = '0';
				body.innerHTML = responseHttpObj.responseText;
				
				var btn = document.getElementsByName('grp_btn[]');
					btn[0].style.display = 'none';
					btn[1].style.display = 'none';
			}
		}
	);
}


//직원 과정상담리스트
function _member_proc_counsel_list(body){
	var code  = document.getElementById('code').value;
	var year  = document.getElementById('year').value;
	var month = document.getElementById('month').value;
	var f_yoy_name = document.getElementById('find_yoy_name').value;
	var f_counsel_name = document.getElementById('find_counsel_name').value;
	var f_type = document.getElementById('find_type').value;
	
	var URL   = '../counsel/member_proc_counsel.php';
	var para  = {'code':code,'year':year,'month':month,'f_yoy_name':f_yoy_name,'f_counsel_name':f_counsel_name,'f_type':f_type};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:para,
			onSuccess:function (responseHttpObj) {
				body.style.margin = '0';
				body.innerHTML = responseHttpObj.responseText;
				
				var btn = document.getElementsByName('grp_btn[]');
					btn[0].style.display = 'none';
					btn[1].style.display = 'none';
			}
		}
	);
}


/**************************************************

	리포트 수정

**************************************************/
function _client_proc_counsel_reg(body, mode, yymm, seq, ssn, dt){
	var code  = document.getElementById('code').value;
	var param = {'code':code,'path':'center','yymm':yymm,'seq':seq,'ssn':ssn,'regDt':dt};
	
	if(mode == 'stat'){
		var URL = '../sugupja/'+mode+'_reg.php';
	}else {
		var URL = '../counsel/client_counsel_'+mode+'_reg.php';
	}
	
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById(body);
					obj.style.margin = '10px';
					obj.innerHTML = responseHttpObj.responseText;
					
				var btn = document.getElementsByName('grp_btn[]');
					btn[0].style.display = '';
					btn[1].style.display = '';
					
				var tmp = document.getElementsByName(mode+'_yymm');
				
				document.getElementById('mode').value = mode;
				document.getElementById('yymm').value = yymm;
				document.getElementById('seq').value  = seq;
				
				/*
				if(mode == 'stat'){
					document.getElementById('reg_dt').value  = dt;
				}
				*/

				__init_form(document.f);
			}
		}
	);
}

function _client_proc(year){
	location.replace('../sugupja/counsel_client.php?year='+year);
}

//직원 과정상담등록
function _member_proc_counsel_reg(body, yymm, seq, ssn){
	
	var code  = document.getElementById('code').value;
	var param = {'code':code,'yymm':yymm,'seq':seq,'ssn':ssn};
	var URL = '../counsel/mem_stress_reg.php';
	
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById(body);
				obj.style.margin = '10px';
				obj.innerHTML = responseHttpObj.responseText;
				
				var btn = document.getElementsByName('grp_btn[]');
					btn[0].style.display = '';
					btn[1].style.display = '';
				
				document.getElementById('yymm').value = yymm;
				document.getElementById('seq').value  = seq;
				document.getElementById('ssn').value  = ssn;
	
				__init_form(document.f);

			}
		}
	);
}

function _member_proc_counsel_reg2(body, mode, yymm, seq, ssn){
	
	var code  = document.getElementById('code').value;
	var param = {'code':code,'mode':mode,'yymm':yymm,'seq':seq,'ssn':ssn};
	if(mode == 'process'){
		var URL = '../counsel/mem_stress_reg.php';
	}else {
		var URL = '../counsel/client_counsel_'+mode+'_reg.php';
	}
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById(body);
				obj.style.margin = '10px';
				obj.innerHTML = responseHttpObj.responseText;
				
				var btn = document.getElementsByName('grp_btn[]');
					btn[0].style.display = '';
					btn[1].style.display = '';
				
				document.getElementById('mode').value = mode;
				document.getElementById('yymm').value = yymm;
				document.getElementById('seq').value  = seq;
				document.getElementById('ssn').value  = ssn;
	
				__init_form(document.f);

			}
		}
	);
}

function _member_proc(year){
	location.replace('../yoyangsa/counsel_member.php?year='+year);
}

/**************************************************

	리포트 출력

**************************************************/
function _client_proc_show(){
	var mode = document.getElementById('mode').value;
	var yymm = document.getElementById(mode+'_yymm').value;
	var seq  = document.getElementById(mode+'_seq').value;
	
	_client_proc_counsel_show(mode, yymm, seq);
}

function _client_proc_counsel_show(mode, yymm, seq, ssn, dt){
	
	if(mode == 'stat'){
		
		var	arguments = 'root=sugupja'
				  + '&dir=P'
				  + '&fileName=stat'
				  + '&fileType=pdf'
				  + '&target=show.php'
				  + '&showForm='
				  + '&code='+$('#code').val()
				  + '&jumin='+ssn
				  + '&regDt='+dt
				  + '&param=';

		__printPDF(arguments);

	}else {
		var w = 700;
		var h = 900;
		var l = (window.screen.width  - w) / 2;
		var t = (window.screen.height - h) / 2;
		
		var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
		var f   = document.f;
			
		if(yymm != '') document.getElementById(mode+'_yymm').value = yymm;
		if(seq != '') document.getElementById(mode+'_seq').value  = seq;

		f.target = 'SHOW_PDF';
		f.action = '../counsel/counsel_show.php?type='+mode.toUpperCase();
		f.submit();
		f.target = '_self';
		f.action = '../sugupja/counsel_client.php';

	}
}

/*
function _member_proc_counsel_all_show(mode){

	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	
	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;
		
	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type='+mode.toUpperCase();
	f.submit();
	f.target = '_self';
	
}
*/

//직원 과정상담출력
function _member_proc_show(seq, ssn, mode ,yymm){
	if(mode == 'process'){ //직원과정상담
		var param = {'m_cd':ssn, 'seq':seq};	
		_report_show_pdf(1, param, '');

	}else { //불만고충처리,사례관리
		_client_proc_counsel_show(mode, yymm, seq);
	}
}

/**************************************************

	리포트 저장

**************************************************/
function _client_proc_counsel_save(){
	f.target = '_self';
	f.action = '../center/counsel_client_save.php';
	f.submit();
}

//직원 과정상담 등록처리
function _member_proc_counsel_save(){
	var f = document.f;
	f.target = '_self';
	
	var stress_code = document.getElementById('code').value;
	var stress_seq = document.getElementById('seq').value;
	var stress_ssn = document.getElementById('ssn').value;
	
	f.action = '../yoyangsa/counsel_member_save.php';
	f.submit();
}

function _member_proc_counsel_save2(){
	var f = document.f;
	f.target = '_self';
	
	f.action = '../yoyangsa/counsel_member_save.php';
	f.submit();
}

/**************************************************

	리포트 삭제

**************************************************/
function _client_proc_counsel_delete(mode, yymm, seq, ssn, dt, is_pop){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;
	
	var code  = document.getElementById('code').value;
	
	if(mode == 'stat' || mode == 'state'){ 
		//상태변화일지 변수
		if (ssn == '') ssn = document.getElementById('statSsn').value;
		if (dt == '') dt = document.getElementById('statDt').value;
	}else {	
		//그외 과정상담 변수
		if (yymm == '') yymm = document.getElementById(mode+'_yymm').value;		
		if (seq == '') seq = document.getElementById(mode+'_seq').value;
	}
		
	var param = {'code':code,'yymm':yymm,'seq':seq,'jumin':ssn,'regDt':dt,'type':'DELETE' };
	
	if(mode == 'stat' || mode == 'state'){
		//상태변화일지
		var URL   = '../sugupja/stat_fun.php';
	}else {
		//그외 과정상담
		var URL   = '../counsel/client_counsel_'+mode+'_delete.php';
	}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'ok' || responseHttpObj.responseText == 1){
					alert('정상적으로 처리되었습니다.');
					if(is_pop == 'Y'){
						lfSearch(1);
					}else {
						_client_proc_counsel_list(document.getElementById('body'));
					}
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}


function _member_proc_counsel_delete(mode, yymm, seq, ssn, is_pop){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;
	
	var code  = document.getElementById('code').value;
	
	
	if (yymm == '') yymm = document.getElementById(mode+'_yymm').value;		
	if (seq == '') seq = document.getElementById(mode+'_seq').value;
			
	var param = {'code':code,'yymm':yymm,'seq':seq,'jumin':ssn};
	
	if(mode == 'process'){
		//직원과정상담일지
		var URL   = '../counsel/mem_stress_del.php';
	}else {
		//그외 과정상담
		var URL   = '../counsel/client_counsel_'+mode+'_delete.php';
	}

	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
				if (responseHttpObj.responseText == 'ok' || responseHttpObj.responseText == 'Y'){
					alert('정상적으로 처리되었습니다.');
					if(is_pop == 'Y'){
						lfSearch(1);
					}else {
						_member_proc_counsel_list(document.getElementById('body'));
					}
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}


/****************************************************

과정상담내역 검색

***************************************************/

function _member_search(){
	var f = document.f;
	
	f.submit();
}


function _client_search(){
	var f = document.f;
	
	f.submit();
}

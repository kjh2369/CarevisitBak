function go_visit_list(){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var ssn   = document.getElementById('jumin').value;
	var param = {'code':code,'ssn':ssn};
	var URL = '../counsel/client_counsel_visit_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_visit');
					obj.innerHTML = responseHttpObj.responseText;
				
				if (document.getElementById('svc_visit').style.display == ''){
					set_button(2);
				}
			}
		}
	);
}

function go_visit_reg(yymm, seq){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var jumin = document.getElementById('jumin').value;
	var param = {'code':code,'yymm':yymm,'seq':seq,'ssn':jumin};
	var URL = '../counsel/client_counsel_visit_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_visit');
					obj.innerHTML = responseHttpObj.responseText;
					
				__init_form(document.f);
					
				set_button(3);
			}
		}
	);
}

function go_visit_show(yymm, seq){
	var f = document.f;
	
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;
	
	f.visit_yymm.value = yymm;
	f.visit_seq.value  = seq;
	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type=VISIT';
	f.submit();
	f.target = '_self';
}

function go_visit_delete(yymm, seq){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	var code  = document.getElementById('code').value;
	var param = {'code':code,'yymm':yymm,'seq':seq};
	var URL = '../counsel/client_counsel_visit_delete.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'ok'){
					alert('정상적으로 처리되었습니다.');
					go_visit_list();
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}




function go_phone_list(){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var ssn   = document.getElementById('jumin').value;
	var param = {'code':code,'ssn':ssn};
	var URL = '../counsel/client_counsel_phone_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_phone');
					obj.innerHTML = responseHttpObj.responseText;
				
				if (document.getElementById('svc_phone').style.display == ''){
					set_button(2);
				}
			}
		}
	);
}

function go_phone_reg(yymm, seq){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var jumin = document.getElementById('jumin').value;
	var param = {'code':code,'yymm':yymm,'seq':seq,'ssn':jumin};
	var URL = '../counsel/client_counsel_phone_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_phone');
					obj.innerHTML = responseHttpObj.responseText;
					
				__init_form(document.f);
					
				set_button(3);
			}
		}
	);
}

function go_phone_show(yymm, seq){
	var f = document.f;
	
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;
	
	f.phone_yymm.value = yymm;
	f.phone_seq.value  = seq;
	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type=PHONE';
	f.submit();
	f.target = '_self';
}

function go_phone_delete(yymm, seq){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	var code  = document.getElementById('code').value;
	var param = {'code':code,'yymm':yymm,'seq':seq};
	var URL = '../counsel/client_counsel_phone_delete.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'ok'){
					alert('정상적으로 처리되었습니다.');
					go_phone_list();
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}





function go_stress_list(){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var ssn   = document.getElementById('jumin').value;
	var param = {'code':code,'ssn':ssn};
	var URL = '../counsel/client_counsel_stress_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_stress');
					obj.innerHTML = responseHttpObj.responseText;
				
				if (document.getElementById('svc_stress').style.display == ''){
					set_button(2);
				}
			}
		}
	);
}

function go_stress_reg(yymm, seq){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var jumin = document.getElementById('jumin').value;
	var param = {'code':code,'yymm':yymm,'seq':seq,'ssn':jumin};
	var URL = '../counsel/client_counsel_stress_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_stress');
					obj.innerHTML = responseHttpObj.responseText;
					
				init_stress_reg();
				
				__init_form(document.f);
					
				set_button(3);
			}
		}
	);
}

function go_stress_show(yymm, seq){
	var f = document.f;
	
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;
	
	f.stress_yymm.value = yymm;
	f.stress_seq.value  = seq;
	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type=STRESS';
	f.submit();
	f.target = '_self';
}

function go_stress_delete(yymm, seq){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	var code  = document.getElementById('code').value;
	var param = {'code':code,'yymm':yymm,'seq':seq};
	var URL = '../counsel/client_counsel_stress_delete.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'ok'){
					alert('정상적으로 처리되었습니다.');
					go_stress_list();
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}

function init_stress_reg(){
	var stress_kind = __object_get_value('stress_kind');
	
	__object_enabled(document.getElementById('stress_kind_family'), (stress_kind == '2' ? true : false));
	__object_enabled(document.getElementById('stress_kind_other'), (stress_kind == '9' ? true : false));
	
	var stress_path = __object_get_value('stress_path');
	
	__object_enabled(document.getElementsByName('stress_path_paper_yn')[0], (stress_path == '5' ? true : false));
	__object_enabled(document.getElementsByName('stress_path_paper_yn')[1], (stress_path == '5' ? true : false));
	
	__object_enabled(document.getElementById('stress_path_other'), (stress_path == '9' ? true : false));
}




function go_case_list(){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var ssn   = document.getElementById('jumin').value;
	var param = {'code':code,'ssn':ssn};
	var URL = '../counsel/client_counsel_case_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_case');
					obj.innerHTML = responseHttpObj.responseText;
				
				if (document.getElementById('svc_case').style.display == ''){
					set_button(2);
				}
			}
		}
	);
}

function go_case_reg(yymm, seq){
	if (document.getElementById('write_mode').value == 1) return;
	
	var code  = document.getElementById('code').value;
	var jumin = document.getElementById('jumin').value;
	var param = {'code':code,'yymm':yymm,'seq':seq,'ssn':jumin};
	var URL = '../counsel/client_counsel_case_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_case');
					obj.innerHTML = responseHttpObj.responseText;
					
				__init_form(document.f);
					
				set_button(3);
			}
		}
	);
}

function go_case_show(yymm, seq){
	var f = document.f;
	
	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;
	
	f.case_yymm.value = yymm;
	f.case_seq.value  = seq;
	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type=CASE';
	f.submit();
	f.target = '_self';
}

function go_case_delete(yymm, seq){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	var code  = document.getElementById('code').value;
	var param = {'code':code,'yymm':yymm,'seq':seq};
	var URL = '../counsel/client_counsel_case_delete.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'ok'){
					alert('정상적으로 처리되었습니다.');
					go_case_list();
				}else{
					alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
				}
			}
		}
	);
}

function _ccAttachAdd(){
	var cnt		= $('tr',$('#bodyAttachFile')).length;
	var html	= '';

	html	+= '<tr>';
	html	+= '<th class="center">제목</th>';
	html	+= '<td class="center"><input id="attachStr_'+cnt+'" name="attachStr_'+cnt+'" type="text" style="width:100%;" value=""></td>';
	html	+= '<th class="center">첨부파일</th>';
	html	+= '<td class="center"><input id="attachFile_'+cnt+'" name="attachFile_'+cnt+'" type="file" style="width:240px; margin-right:5px;"></td>';
	html	+= '<td class="left">';
	//html	+= '<span class="btn_pack m"><button type="button" onclick="_ccAttachDel($(this).parent().parent().parent());">삭제</button></span>';
	html	+= '</td>';
	html	+= '</tr>';

	if (cnt == 0){
		$('#bodyAttachFile').html(html);
	}else{
		$('#bodyAttachFile tr:last-child').after(html);
	}
}

function _ccAttachDel(obj,real){
	if (real){
		$.ajax({
			type	: 'POST'
		,	url		: '../counsel/client_counsel_case_file_delete.php'
		,	data	: {
				id	: $(obj).attr('id')
			,	yymm: $(obj).attr('yymm')
			,	seq	: $(obj).attr('seq')
			,	no	: $(obj).attr('no')
			}
		,	success	: function (result){
				if (result == 1){
					$(obj).remove();
					alert('첨부파일이 삭제되었습니다.');
				}else{
					alert('첨부피일 삭제 중 오류가 발생하였습니다.');
				}
			}
		,	error	: function (request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}else{
		$(obj).remove();
	}
}
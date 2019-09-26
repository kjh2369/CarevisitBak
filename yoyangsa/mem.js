/*********************************************************

	초기설정

*********************************************************/
function _memInit(){
	if ($('#code').attr('value') != '1234') return;

	_memSalaryDisplay('11');
	_memSalaryDisplay('12');
	_memSalaryDisplay('21');
	_memSalaryDisplay('22');
	_memSalaryDisplay('23');
	_memSalaryDisplay('24');
}


/*********************************************************

	기준근로시간 및 시급 변경

*********************************************************/
function _memFixedWorksChange(){
	var objModal = new Object();
	var url      = '../find/_find_fixedworks.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
	var jumin    = $(':input[name="ssn"]').attr('value');
	
	if (!jumin){
		//jumin = $(':input[name="ssn1"]').attr('value') + $(':input[name="ssn2"]').attr('value');
		alert('기준근로시간/시급정보는 데이타 저장 후 변경 가능합니다.');
		return;
	}

	objModal.code   = $(':input[name="code"]').attr('value');
	objModal.jumin  = jumin;
	objModal.hours  = $('#fixedHours').attr('value');
	objModal.hourly = $('#fixedHourly').attr('value');
	objModal.from   = $('#fixedFromDt').attr('value');
	objModal.to     = $('#fixedToDt').attr('value');
	
	window.showModalDialog(url, objModal, style);

	if (!objModal.result) return;

	$('#fixedHours').attr('value', objModal.hours);
	$('#fixedHourly').attr('value', objModal.hourly);
	$('#fixedFromDt').attr('value', objModal.from);
	$('#fixedToDt').attr('value', objModal.to);

	$('#strFixedHours').text( objModal.hours );
	$('#strFixedHourly').text( objModal.hourly );
}


/*********************************************************

	작,간접인건비 구분 변경

*********************************************************/
function _memDirectChange(){
	
	var objModal = new Object();
	var url      = '../find/_find_direct_gbn.php';
	var style    = 'dialogWidth:300px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';
	var jumin    = $(':input[name="ssn"]').attr('value');
	
	if (!jumin){
		//jumin = $(':input[name="ssn1"]').attr('value') + $(':input[name="ssn2"]').attr('value');
		alert('직,간접 인건비 구분 정보는 데이타 저장 후 변경 가능합니다.');
		return;
	}

	objModal.code   = $(':input[name="code"]').attr('value');
	objModal.jumin  = jumin;
	objModal.direct_gbn	= $(':radio[name="directGbn"]:checked').attr('value');
	objModal.from   = $('#fromDT').attr('value');
	objModal.to     = $('#toDT').attr('value');
	
	window.showModalDialog(url, objModal, style);

	if (!objModal.result) return;
	
	$('#directGbn').attr('value', objModal.direct_gbn);
	$('#FromDt').attr('value', objModal.from);
	$('#ToDt').attr('value', objModal.to);
	
	var gbnNm = (objModal.direct_gbn == '1' ? '직접인건비':'간접인건비');
		

	$('#strDirectGbn').text( gbnNm );
	$('#strFromDt').text( objModal.from );
	$('#strToDt').text( objModal.to );

}



/*********************************************************

	월급제 이력 변경

*********************************************************/
function _memSalaryMonSet(){
	var objModal = new Object();
	var url      = '../find/_find_salary.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
	var jumin    = $(':input[name="ssn"]').attr('value');
	
	if (!jumin){
		jumin = $('#jumin').attr('value');

		if (!jumin){
			//jumin = $(':input[name="ssn1"]').attr('value') + $(':input[name="ssn2"]').attr('value');
			alert('급여는 데이타 저장 후 변경 가능합니다.');
			return;
		}
	}

	$today = new Date();

	objModal.code    = $('#code').attr('value');
	objModal.jumin   = jumin;
	objModal.pay     = $('#salaryMonAmt').text();
	objModal.careYN  = $('#salaryMonCareYN').text() == '예' ? 'Y' : 'N';
	objModal.extraYN = $('#salaryMonExtraYN').text() == '예' ? 'Y' : 'N';
	objModal.day20YN = $('#salaryMonDay20YN').text() == '예' ? 'Y' : 'N';
	objModal.dealPay = $('#salaryMonDealpay').text();
	objModal.from    = $('#salaryMonFrom').attr('value');
	objModal.to      = $('#salaryMonTo').attr('value');
	
	if (!objModal.from)
		objModal.from = $today.getFullYear() + (($today.getMonth()+1 < 10 ? '-0' : '-') + ($today.getMonth()+1));

	if (!objModal.to)
		objModal.to = __addDate('m', 11, objModal.from+'-01').substring(0,7);
	
	window.showModalDialog(url, objModal, style);

	if (!objModal.result) return;

	if (objModal.pay == ''){
		$applyGbn = '무';
	}else{
		$applyGbn = '기본급';
	}
	
	$('#salaryApply').text( $applyGbn );
	$('#salaryMonAmt').text( __num2str(objModal.pay) );
	$('#salaryMonCareYN').text(objModal.careYN == 'Y' ? '예' : '아니오');
	$('#salaryMonExtraYN').text(objModal.extraYN == 'Y' ? '예' : '아니오');
	$('#salaryMonDay20YN').text(objModal.day20YN == 'Y' ? '예' : '아니오');
	$('#salaryMonFrom').attr('value', objModal.from);
	$('#salaryMonTo').attr('value', objModal.to);
}


/*********************************************************

	시급 이력 변경

*********************************************************/
function _memSalaryHourSet(svcID){
	var objModal = new Object();
	var url      = '../find/_find_hourly.php';
	var style    = 'dialogWidth:600px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
	var jumin    = $(':input[name="ssn"]').attr('value');
	
	if (!jumin){
		jumin = $('#jumin').attr('value');
		
		if (!jumin){
			//jumin = $(':input[name="ssn1"]').attr('value') + $(':input[name="ssn2"]').attr('value');
			alert('급여는 데이타 저장 후 변경 가능합니다.');
			return;
		}
	}

	$today = new Date();

	objModal.code  = $('#code').attr('value');
	objModal.jumin = jumin;
	objModal.svcID = svcID;
	objModal.seq   = $('#salarySeq_'+svcID).text();
	
	if (!objModal.from)
		objModal.from = $today.getFullYear() + (($today.getMonth()+1 < 10 ? '-0' : '-') + ($today.getMonth()+1));

	if (!objModal.to)
		objModal.to = __addDate('m', 11, objModal.from+'-01').substring(0,7);
	
	window.showModalDialog(url, objModal, style);

	if (!objModal.result) return;

	$('#salarySeq_'+svcID).text(objModal.seq);
	_memHourlyInfo($('#divHourly_'+svcID), objModal.code, objModal.jumin, objModal.svcID, 'auto', true);
}


/*********************************************************

	급여 설정에 따른 디스플레이

*********************************************************/
function _memSalaryDisplay(svcID){
	$kind = $('#salaryKind_'+svcID).text();

	$('#salaryAmt_'+svcID+'_1').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_1').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_2').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_3').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_4').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_5').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_6').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_7').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_8').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_2_9').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_3').css('font-weight','normal').css('color','#cccccc');
	$('#salaryAmt_'+svcID+'_4').css('font-weight','normal').css('color','#cccccc');

	if ($kind == '2'){
		$('#salaryAmt_'+svcID+'_'+$kind+'_1').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_2').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_3').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_4').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_5').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_6').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_7').css('font-weight','bold').css('color','#000000');
		$('#salaryAmt_'+svcID+'_'+$kind+'_8').css('font-weight','bold').css('color','#000000');

		$('#salaryAmt_'+svcID+'_'+$kind+'_9').css('font-weight','bold').css('color','#000000');
	}else{
		$('#salaryAmt_'+svcID+'_'+$kind).css('font-weight','bold').css('color','#000000');
	}
}


/*********************************************************
	
	급여 정보

*********************************************************/
function _memHourlyInfo(body, code, jumin, svcID, seq, text){
	try{
		$.ajax({
			type: "POST",
			url : "../find/_find_hourly_info.php",
			data: {
				"code":code
			,	"jumin":jumin
			,	"svcID":svcID
			,	"seq":seq
			,	"text":text
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				$(body).html(xmlHttp);
				
				if (text){
					_memSalaryDisplay(svcID);
				}else{
					_memSalarySetSvcSub(svcID);
					__init_form(document.f);
				}
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}


/*********************************************************

	급여 지급 방식에 따른 화면 디스플레이

*********************************************************/
/*
function _memSalarySetType(){
	$type = $(':radio[name="salaryPayType"]:checked').attr('value');

	if ($type == '1'){
		$('#divSalaryMon').show();
		$('#divSalarySvc').hide();
	}else{
		$('#divSalaryMon').hide();
		$('#divSalarySvc').show();
	}
}
*/


/*********************************************************

	월급제 케어금액 포함 여부에 따른 객처설정

*********************************************************/
/*
function _memSalaryCareYNSet(){
	$YN = $(':radio[name="careYN"]:checked').attr('value');
	$('.objSlyClsMon').each(function(){
		$(this).attr('disabled', $YN != 'Y' ? 'disabled' : '');
		$(this).css('background-color', $YN != 'Y' ? '#eeeeee' : '#ffffff');
	});
}
*/


/*********************************************************

	서비스별 지급방식 화면 설정

*********************************************************/
/*
function _memSalarySetSvc(){
	_memSalarySetSvcSub('11');
	_memSalarySetSvcSub('12');
	_memSalarySetSvcSub('21');
	_memSalarySetSvcSub('22');
	_memSalarySetSvcSub('23');
	_memSalarySetSvcSub('24');
}
*/

function _memSalarySetSvcSub(svcID){
	$kind = $(':radio[name="salaryKind_'+svcID+'"]:checked').attr('value');

	if (!$kind) return;

	$('.objSlyClsSvc_'+svcID).each(function(){
		$(this).attr('disabled', 'disabled');
		
		if ($(this).attr('type') == 'text')
			$(this).css('background-color', '#eeeeee');
	});

	$('.objSlyClsSvc_'+svcID+'_'+$kind).each(function(){
		$(this).attr('disabled', '');
		$(this).css('background-color', '#ffffff');
	});
}




/*********************************************************

	직원 바로 찾기

*********************************************************/
function _memFind(as_jumin, ai_seq, as_regYn){
	var ls_jumin = '';
	var li_seq   = 0;
	var ls_regYn = 'N';

	if (!as_jumin){
		$memIf = __find_member_if($("#code").attr('value'));

		if (!$memIf) return;

		ls_jumin = $memIf['jumin'];
	}else{
		ls_jumin = as_jumin;
		li_seq   = ai_seq;

		if (as_regYn == 'Y'){
			__popupHide();
			$('#memNm').focus();
			$('#memMode').val(1);
			$('#memHisSeq').val(li_seq);

			document.f.appendChild(__create_input('ssn', ls_jumin));

			if ($('#joinDt').val() != ''){
				_memChkJoinDt($('#joinDt'));
			}else{
				$.ajax({
					type: 'POST'
				,	url : './mem_apply.php'
				,	beforeSend: function(){
					}
				,	data: {
						'code'  : $("#code").attr('value')
					,	'jumin' : ls_jumin
					,	'mode'	: 1
					}
				,	success: function(result){
						var val = __parseStr(result);

						$('#memNm').val(val['memNm']);
						$('#txtPostNo').val(val['postNo']);
						//$('#postNo2').val(val['postNo2']);
						$('#txtAddr').val(val['addr']);
						$('#txtAddrDtl').val(val['addrDtl']);
						$('#mobile').val(val['mobile']);
						$('#phone').val(val['phone']);
						$('#email').val(val['email']);
						$('#bankNo').val(val['bankNo']);
						$('#bankCD').val(val['bankCd']);
						$('#bankAcct').val(val['bankAcct']);

						$('select[name="mobile_kind"] option[value="'+val['mobileKind']+'"]').attr('selected','selected');
						$('input:radio[name="rfid_yn"]:radio[value="'+val['rfid']+'"]').attr('checked','checked');
					}
				,	complete: function(html){
					}
				,	error: function (){
					}
				}).responseXML;
			}

			return;
		}
	}
	
	var tmpForm = document.createElement('form');
	
	tmpForm.appendChild(__create_input('code', $("#code").attr('value')));
	tmpForm.appendChild(__create_input('jumin', ls_jumin));
	tmpForm.appendChild(__create_input('seq', li_seq));

	tmpForm.setAttribute('method', 'post');
	
	document.body.appendChild(tmpForm);

	tmpForm.target = '_self';
	tmpForm.action = './mem_reg.php';
	tmpForm.submit();
}



/*********************************************************

	주민번호 확인

*********************************************************/
function _memCheckJumin(obj1, obj2){
	var SSN1 = $('#'+obj1).attr('value');
	var SSN2 = $('#'+obj2).attr('value');
	var code = $('#code').attr('value');

	try{
		if (code == '1234' ||
			code == '12345' ||
			code == '0627141516' ||
			SSN2.substring(0,1) == '5' ||
			SSN2.substring(0,1) == '6' ||
			SSN2.substring(0,1) == '7' ||
			SSN2.substring(0,1) == '8'){
			var skip = true;
		}else{
			var skip = false;
		}
	}catch(e){
		var skip = false;
	}

	var skip = true;
	
	if (SSN1.length == 6 && SSN2.length == 7){
	}else if (SSN2.length == 7){
		$('#name').focus();
		return false;
	}else if (SSN1.length == 6){
		$('#'+obj2).focus();
		return false;
	}else{
		return false;
	}

	// 주민번호 체크디지트
	if (!skip){
		if (!__isSSN(SSN1,SSN2)){
			alert('입력하신 주민번호의 형식이 올바르지 않습니다. 다시 확인 후 입력하여 주십시오.');
			document.getElementById('ssn1').value = '';
			document.getElementById('ssn2').value = '';
			document.getElementById('ssn1').focus();
			return false;
		}
	}
	
	var jumin = SSN1+SSN2;

	if (jumin.length != 13) return true;

	var name = getHttpRequest('../inc/_yoy_check_ssn.php?code='+code+'&jumin='+jumin);
	
	if (name != ''){
		//alert('입력하신 주민번호는 "'+name+'"의 이름으로 이미 등록된 주민번호입니다.\n\n확인 후 다시 입력하여 주십시오.');
		//$(obj1).focus();

		_memHistory(code, jumin, obj1, obj2);
		return false;
	}
	
	return true;
}


/*********************************************************

	직원 이력 내역

*********************************************************/
function _memHistory(code, jumin, obj1, obj2){
	var body = $('#divPopupBody');
	var cont = $('#divPopupLayer');

	var w  = $(document).width();
	var h  = $(document).height();
	
	//모드 복구
	$('#memMode').val($('#memMode').attr('value1'));

	//내역 리스트
	$.ajax({
		type: 'POST'
	,	url : './mem_his.php'
	,	beforeSend: function(){
			$('#loadingBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
		}
	,	data: {
			'code'  : code
		,	'jumin' : jumin
		,	'mode'	: $('#memMode').val()
		}
	,	success: function(html){
			$('#tempLodingBar').remove();
			body.css('width', w)
				.css('height', h)
				.click(function(){
					$('#'+obj1).val('');
					$('#'+obj2).val('');
					__popupHide();
				})
				.show();
			cont.css('width', 'auto')
				.css('margin', 0).css('padding', 0).css('border','none')
				.css('left', '250px').css('top', '250px')
				.css('cursor', 'default')
				.html(html)
				.show();
		}
	,	complete: function(html){
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	입사일자 확인

*********************************************************/
function _memChkJoinDt(obj){
	$.ajax({
		type: 'POST'
	,	url : './mem_his_chk.php'
	,	beforeSend: function(){
		}
	,	data: {
			'code'  : $('#code').val()
		,	'jumin' : $('#ssn1').val()+$('#ssn2').val()
		,	'mode'	: 1
		}
	,	success: function(data){
			//$(obj).val() && data && 
			if (!$(obj).val()){
				alert('입사일자를 입력하여 주십시오. 1901년 이후 부터 입력가능합니다.');
				return false;
			}
			if ($(obj).val() <= data){
				alert('과거 이력내역중 입력하신 입사일자의 재직기간이 있습니다.\n확인 후 다시 입력하여 주십시오.');
				$(obj).val('');
				$(obj).focus();
				return false;
			}
		}
	,	complete: function(html){
		}
	,	error: function (){
		}
	}).responseXML;

	return true;
}


/*********************************************************
 *	보험이력
 *********************************************************/
function _memInsuHis(jumin){
	var objModal = new Object();
	var url = '../yoyangsa/mem_his_insu.php';
	var style = 'dialogWidth:470px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
	
	if (!jumin){
		alert('기준근로시간/시급정보는 데이타 저장 후 변경 가능합니다.');
		return;
	}

	objModal.jumin = jumin;
	objModal.result = 9;
	
	window.showModalDialog(url, objModal, style);

	$('#lblInsuAnnuityYn').text(objModal.aYn);
	$('#lblInsuHealthYn').text(objModal.hYn);
	$('#lblInsuEmployYn').text(objModal.eYn);
	$('#lblInsuSanjeYn').text(objModal.sYn);
	$('#lblInsuPAYEYn').text(objModal.pYn);
	$('#lblInsuFrom').text(__getDate(objModal.from,'.'));
	$('#lblInsuTo').text(__getDate(objModal.to,'.'));
}

/*********************************************************
 *	현재 보험 내역 조회
 *********************************************************/
function _memInsuFind(jumin){
	$.ajax({
		type: 'POST'
	,	url : '../yoyangsa/mem_his_insu_list.php'
	,	beforeSend: function(){
		}
	,	data: {
			'jumin':jumin
		,	'seq':'MAX'
		}
	,	success: function(data){
			if (!data) return;
			var col = __parseStr(data);

			$('#lblInsuAnnuityYn').text(col['a']);
			$('#lblInsuHealthYn').text(col['h']);
			$('#lblInsuEmployYn').text(col['e']);
			$('#lblInsuSanjeYn').text(col['s']);
			$('#lblInsuPAYEYn').text(col['p']);
			$('#lblInsuFrom').text(__getDate(col['from'],'.'));
			$('#lblInsuTo').text(__getDate(col['to'],'.'));
		}
	,	error:function(request, status, error){
			alert('[ERROR No.03]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
			
			return false;
		}
	}).responseXML;
}

/*********************************************************
 *	보수신고급여이력
 *********************************************************/
function _memInsuHisMonthly(jumin){
	var objModal = new Object();
	var url = '../yoyangsa/mem_his_insu_monthly.php';
	var style = 'dialogWidth:300px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';
	
	if (!jumin){
		alert('기준근로시간/시급정보는 데이타 저장 후 변경 가능합니다.');
		return;
	}

	objModal.jumin = jumin;
	objModal.result = 9;
	
	window.showModalDialog(url, objModal, style);
	
	try{
		$('#lblInsuMonthly').text(objModal.pay);
		$('#lblInsuYYMM').text(objModal.yymm.split('-').join('.'));
	}catch(e){
	}
}


/*********************************************************
 *	현재 보수신고급여 내역 조회
 *********************************************************/
function _memInsuMonthlyFind(jumin){
	$.ajax({
		type: 'POST'
	,	url : '../yoyangsa/mem_his_insu_monthly_list.php'
	,	beforeSend: function(){
		}
	,	data: {
			'jumin':jumin
		,	'yymm':'NOW'
		}
	,	success: function(data){
			if (!data) return;
			var col = __parseStr(data);

			$('#lblInsuMonthly').text(col['pay']);
			$('#lblInsuYYMM').text(col['ym']);
		}
	,	error:function(request, status, error){
			alert('[ERROR No.03]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
			
			return false;
		}
	}).responseXML;
}
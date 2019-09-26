/*********************************************************

	고객 바로 찾기

*********************************************************/
function _clientFind(as_jumin, ai_seq, as_regYn){
	var ls_jumin = '';
	var li_seq   = 0;
	var ls_regYn = 'N';

	if (!as_jumin){
		$clientIf = __find_client($("#code").attr('value'));

		if (!$clientIf) return;

		ls_jumin = $clientIf[0];
	}else{
		ls_jumin = as_jumin;
		li_seq   = ai_seq;

		if (as_regYn == 'Y'){
			__popupHide();
			$('#name').focus();
			$('#writeMode').val(1);
			
			document.f.appendChild(__create_input('jumin', ls_jumin));

			$.ajax({
				type: 'POST'
			,	url : './client_apply.php'
			,	beforeSend: function(){
				}
			,	data: {
					'code'  : $("#code").attr('value')
				,	'jumin' : ls_jumin
				,	'mode'	: 3
				}
			,	success: function(result){
					var val = __parseStr(result);

					$('#name').val(val['nm']);
					$('#postNo1').val(val['postNo1']);
					$('#postNo2').val(val['postNo2']);
					$('#addr').val(val['addr']);
					$('#addrDtl').val(val['addrDtl']);
					$('#phone').val(val['phone']);
					$('#mobile').val(val['mobile']);
					$('#protectNm').val(val['protectNm']);
					$('#protectRel').val(val['protectRel']);
					$('#protectTel').val(val['protectPhon']);
					$('#clientNo').val(val['clientNo']);
					$('#memo').val(val['memo']);
					
					if (val['toDt_0']){
						$('#11_gaeYakFm').val(addDate('d',1,val['toDt_0']));
						$('#11_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#11_gaeYakFm').val())));
					}

					if (val['toDt_1']){
						$('#21_gaeYakFm').val(addDate('d',1,val['toDt_1']));
						$('#21_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#21_gaeYakFm').val())));
					}

					if (val['toDt_2']){
						$('#22_gaeYakFm').val(addDate('d',1,val['toDt_2']));
						$('#22_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#22_gaeYakFm').val())));
					}

					if (val['toDt_3']){
						$('#23_gaeYakFm').val(addDate('d',1,val['toDt_3']));
						$('#23_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#23_gaeYakFm').val())));
					}

					if (val['toDt_4']){
						$('#24_gaeYakFm').val(addDate('d',1,val['toDt_4']));
						$('#24_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#24_gaeYakFm').val())));
					}

					if (val['toDt_A']){
						$('#31_gaeYakFm').val(addDate('d',1,val['toDt_A']));
						$('#31_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#31_gaeYakFm').val())));
					}

					if (val['toDt_B']){
						$('#32_gaeYakFm').val(addDate('d',1,val['toDt_B']));
						$('#32_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#32_gaeYakFm').val())));
					}

					if (val['toDt_C']){
						$('#33_gaeYakFm').val(addDate('d',1,val['toDt_C']));
						$('#33_gaeYakTo').val(addDate('d',-1,addDate('yyyy',1,$('#33_gaeYakFm').val())));
					}
				}
			,	complete: function(html){
				}
			,	error: function (){
				}
			}).responseXML;
			
			return;
		}
	}

	var tmpForm = document.createElement('form');
	
	tmpForm.appendChild(__create_input('code', $("#code").attr('value')));
	tmpForm.appendChild(__create_input('jumin', ls_jumin));

	tmpForm.setAttribute('method', 'post');
	
	document.body.appendChild(tmpForm);

	if ($('#lbTestMode').val()){
		tmpForm.action = './client_new.php';
	}else{
		tmpForm.action = './client_reg.php';
	}

	tmpForm.target = '_self';
	tmpForm.submit();
}


/*********************************************************

	계약기간 확인

*********************************************************/
function _clientChkPeriod(svcId,svcCd,svcMode){
	try{
		var diffDt = diffDate('d',__getDate($('#'+svcId+'_gaeYakFm').val()),__getDate($('#'+svcId+'_gaeYakTo').val()));
	}catch(e){
		return true;
	}

	if (diffDt < 0){
		alert('계약기간 입력 오류입니다.\n계약종료일이 계약시작일보다 커야합니다.');
		$('#'+svcId+'_gaeYakTo').focus();
		return false;
	}

	if (svcMode){
		_clientApplyPeriod(svcId,svcCd,svcMode);
	}

	return true;
}


/*********************************************************

	수급상태 및 계약기간 변경

*********************************************************/
function _clientPeriodShow(svcId,svcCd){
	var code   = $('#code').val();
	var jumin  = $('#jumin').val();
	var stat   = $('#'+svcId+'_sugupStatus').val();
	var reason = $('#'+svcId+'_stopReason').val();
	var fromDt = $('#'+svcId+'_gaeYakFm').val();
	var toDt   = $('#'+svcId+'_gaeYakTo').val();
	var mode   = $('#writeMode_'+svcId).val();

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!stat) stat = '1';
	if (!reason) reason = '';
	if (!fromDt) fromDt = '';
	if (!toDt) toDt = '';

	var objModal = new Object();
	var url      = '../sugupja/client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code   = code;
	objModal.jumin  = jumin;
	objModal.svcId  = svcId;
	objModal.svcCd  = svcCd;
	objModal.stat   = stat;
	objModal.reason = reason;
	objModal.fromDt = fromDt;
	objModal.toDt   = toDt;
	objModal.mode   = mode;
	objModal.type   = 1;
	objModal.result = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result < 0) return;
	if (objModal.result == 0){
		$('#txtStat_'+svcId).text(objModal.statNm).attr('value',objModal.stat);
		$('#txtReason_'+svcId).text(objModal.reasonNm).attr('value',objModal.reason);
		$('#txtFrom_'+svcId).text(objModal.fromDt.split('-').join('.')).attr('value',objModal.fromDt);
		$('#txtTo_'+svcId).text(objModal.toDt.split('-').join('.')).attr('value',objModal.toDt);

		if (objModal.mpGbn){
			$('#lblMpGbnY').text('□');
			$('#lblMpGbnN').text('□');
			$('#lblMpGbn'+objModal.mpGbn).text('▣');
		}
			
		$('#'+svcId+'_sugupStatus').val(objModal.stat);
		$('#'+svcId+'_stopReason').val(objModal.reason);
		$('#'+svcId+'_gaeYakFm').val(objModal.fromDt);
		$('#'+svcId+'_gaeYakTo').val(objModal.toDt);

		if (objModal.stat == '1')
			$('#reasonTr_'+svcId).hide();
		else
			$('#reasonTr_'+svcId).show();

		_clientPeriod(svcCd,svcId);

		return;
	}
	
	if (svcId == 'S' || svcId == 'R'){
		$('#txtFrom_'+svcId).text(objModal.fromDt.split('-').join('.')).attr('value',objModal.fromDt);
		$('#txtTo_'+svcId).text(objModal.toDt.split('-').join('.')).attr('value',objModal.toDt);
		$('#'+svcId+'_gaeYakFm').val(objModal.fromDt);
		$('#'+svcId+'_gaeYakTo').val(objModal.toDt);
		$('span[id^="lblMpGbn"]').text('□');
		$('#lblMpGbn'+objModal.mp).text('▣');
	}else{
		$.ajax({
			type: 'POST',
			url : './client_breakdown.php',
			data: {
				code   : code
			,	jumin  : jumin		
			,	svcCd  : svcCd
			,	mode   : 1
			},
			beforeSend: function (){
			},
			success: function (result){
				var val = __parseStr(result);

				$('#txtStat_'+svcId).text(val['statNm']);
				$('#txtReason_'+svcId).text(val['reasonNm']);
				$('#txtFrom_'+svcId).text(val['from'].split('-').join('.'));
				$('#txtTo_'+svcId).text(val['to'].split('-').join('.'));
					
				$('#'+svcId+'_sugupStatus').val(val['statCd']);
				$('#'+svcId+'_stopReason').val(val['reasonCd']);
				$('#'+svcId+'_gaeYakFm').val(val['from']);
				$('#'+svcId+'_gaeYakTo').val(val['to']);

				if (val['statCd'] == '1')
					$('#reasonTr_'+svcId).hide();
				else
					$('#reasonTr_'+svcId).show();
				
				try{
					var today  = getToday();
					var liShow = 2;

					if (today <= val['from'] &&
						today >= val['to']){
						liShow = 1;
					}
					
					if (val['statCd']){
						lfSvcDisplay(svcCd,svcId,liShow);
					}
				}catch(e){
				}
			},
			error: function (){
			}
		}).responseXML;
	}
}


/*********************************************************

	수급상태 및 계약기간 적용

*********************************************************/
function _clientApplyPeriod(svcId,svcCd,svcMode){
	if (!_clientChkPeriod(svcId,svcCd)) return;

	var code   = $('#code').val();
	var stat   = $('input:radio[name="'+svcId+'_sugupStatus"]:checked').val();
	var reason = $('input:radio[name="'+svcId+'_stopReason"]:checked').val();
	var fromDt = $('#'+svcId+'_gaeYakFm').val();
	var toDt   = $('#'+svcId+'_gaeYakTo').val();
	var jumin  = $('#jumin').val();
	var mode   = $('#writeMode').val();

	if (svcMode) mode = svcMode;
	if (!reason) reason = '';

	$.ajax({
		type: 'POST',
		url : './client_apply.php',
		data: {
			code   : code
		,	jumin  : jumin		
		,	svcId  : svcId
		,	svcCd  : svcCd
		,	stat   : stat
		,	reason : reason
		,	fromDt : fromDt
		,	toDt   : toDt
		,	mode   : mode
		},
		beforeSend: function (){
		},
		success: function (result){
			if (!isNaN(result)){
				if (result == 101 || result == 102){
					alert('중복된 계약기간을 입력하셨습니다.\n확인 후 다시 입력하여 주십오.');
				}else if (result == 9){
					alert('데이타 저장중 오류가 발생하였습니다.\n확인 후 다시 입력하여 주십오.');
				}else if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
			}else{
				if (mode == 9){
					var val = __parseStr(result);

					if (val['fromDt'] != ''){
						alert('계약시작일자가 기존의 계약기간과 중복됩니다.\n확인 후 다시 입력하여 주십시오.');
					}else if (val['toDt'] != ''){
						alert('계약종료일자가 기존의 계약기간과 중복됩니다.\n확인 후 다시 입력하여 주십시오.');
					}
				}else{
					alert(result);
				}
			}
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	주민번호 확인

*********************************************************/
function _clientCheckJumin(obj1, obj2){
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

	if (SSN1.length != 6 || SSN2.length != 7){
		return false;
	}

	// 주민번호 체크디지트
	if (!skip){
		if (!__isSSN(SSN1,SSN2)){
			alert('입력하신 주민번호의 형식이 올바르지 않습니다. 다시 확인 후 입력하여 주십시오.');
			$('#'+obj1).val('');
			$('#'+obj2).val('');
			$('#'+obj1).focus();
			return false;
		}
	}
	
	var jumin = SSN1+SSN2;

	if (jumin.length != 13) return true;

	var result = getHttpRequest('../inc/_chk_ssn.php?id=220&code='+code+'&ssn='+jumin);
	
	if (result != 'N'){
		_clientHistory(code, jumin, obj1, obj2);
		return false;
	}
	
	return true;
}


/*********************************************************

	고객 이력 내역

*********************************************************/
function _clientHistory(code, jumin, obj1, obj2){
	var body = $('#divPopupBody');
	var cont = $('#divPopupLayer');

	var w  = $(document).width();
	var h  = $(document).height();
	
	//모드 복구
	$('#writeMode').val($('#writeMode').attr('value1'));
	
	//내역 리스트
	$.ajax({
		type: 'POST'
	,	url : './client_his.php'
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

	고객 서비스별 마지막 일정

*********************************************************/
function _clientChkSave(as_code, as_jumin){
	var lsCode  = as_code;
	var lsJumin = as_jumin;
	
	$.ajax({
		type: 'POST'
	,	url : './client_apply.php'
	,	beforeSend: function(){
		}
	,	data: {
			'code'  : lsCode
		,	'jumin' : lsJumin
		,	'mode'	: 4
		}
	,	success: function(result){
			var val = __parseStr(result);
			
			lfSvcSave(val);
		}
	,	complete: function(html){
		}
	,	error: function (){
		}
	}).responseXML;
}


/*********************************************************

	가족요양보호사

*********************************************************/
function _clientFamilyAddRow(memNM, memCD, memGbn, viewType){
	$rows = $("#tblFamily tr");
	$idx  = $rows.length;
	$id   = "tblFamilyRow_"+$idx;
	$code = $("#code").attr("value");

	if (!memNM) memNM = "";
	if (!memCD) memCD = "";
	if (!memGbn) memGbn = "";
	
	if (viewType != 'read'){
		$("#tblFamily").append(
			"<tr id=\'"+$id+"\' class=\'familyRow\'>"
				+"<td class=\'center\'>"+$idx+"</td>"
				+"<td class=\'left\'>"
					+"<div style=\'float:left; width:auto; height:100%;\'>"
						+"<span id=\'strFamilyNM_"+$idx+"\' style=\'font-weight:bold;\'>"+memNM+"</span>"
						+"<input id=\'objFamilyCD_"+$idx+"\' name=\'objFamilyCD[]\' type=\'hidden\' value=\'jumin="+memCD+"&name="+memNM+"\'>"
					+"</div>"
					+"<div style=\'float:right; width:auto; height:100%; padding-top:1px;\'><span class=\'btn_pack m find\' onclick=\'_clientFamilySetMem(__find_member_if(\""+$code+"\"),\""+$idx+"\");\'></span></div>"
				+"</td>"
				+"<td class=\'center\'>"
					+"<select id=\'objFamilyGbn_"+$idx+"\' name=\'objFamilyGbn[]\' style=\'width:100px;\'>"
						+"<option value=\'\' selected></option>"
						+"<option value=\'S031\'"+(memGbn == "S031" ? " selected " : " ")+">처</option>"
						+"<option value=\'S032\'"+(memGbn == "S032" ? " selected " : " ")+">남편</option>"
						+"<option value=\'S033\'"+(memGbn == "S033" ? " selected " : " ")+">자</option>"
						+"<option value=\'S034\'"+(memGbn == "S034" ? " selected " : " ")+">자부</option>"
						+"<option value=\'S035\'"+(memGbn == "S035" ? " selected " : " ")+">사위</option>"
						+"<option value=\'S036\'"+(memGbn == "S036" ? " selected " : " ")+">형제자매</option>"
						+"<option value=\'S037\'"+(memGbn == "S037" ? " selected " : " ")+">손</option>"
						+"<option value=\'S038\'"+(memGbn == "S038" ? " selected " : " ")+">배우자의형제자매</option>"
						+"<option value=\'S039\'"+(memGbn == "S039" ? " selected " : " ")+">외손</option>"
						+"<option value=\'S040\'"+(memGbn == "S040" ? " selected " : " ")+">부모</option>"
						+"<option value=\'S041\'"+(memGbn == "S041" ? " selected " : " ")+">기타</option>"
					+"</select>"
				+"</td>"
				+"<td class=\'center\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'_clientFamilyRemoveRow(\""+$id+"\");\'>삭제</button></span></td>"
			+"</tr>"
		);
	}else{
		$("#tblFamily").append(
			"<tr class=\'familyRow\'>"
				+"<td class=\'center\'>"+$idx+"</td>"
				+"<td class=\'left\'>"+memNM+"</td>"
				+"<td class=\'left\'>"
				+(memGbn == 'S031' ? '처' :
				  memGbn == 'S032' ? '남편' :
				  memGbn == 'S033' ? '자' :
				  memGbn == 'S034' ? '자부' :
				  memGbn == 'S035' ? '사위' :
				  memGbn == 'S036' ? '형제자매' :
				  memGbn == 'S037' ? '손' :
				  memGbn == 'S038' ? '배우자의형제자매' :
				  memGbn == 'S039' ? '외손' :
				  memGbn == 'S040' ? '부모' :
				  memGbn == 'S041' ? '기타' : '')
				+"</td>"
				+"<td class=\'center\'>&nbsp;</td>"
			+"</tr>"
		);
	}

	_clientFamilySetBorder();
}

function _clientFamilyRemoveRow(id){
	$row = $("#"+id);
	$row.remove();

	_clientFamilySetBorder();
}

function _clientFamilySetMem(memInfo, idx){
	$("#strFamilyNM_"+idx).text(memInfo["name"]);
	$("#objFamilyCD_"+idx).attr("value", "jumin="+memInfo["jumin"]+"&name="+memInfo["name"]);
}

function _clientFamilySetBorder(){
	$("#tblFamily td").css('border-bottom','1px solid');
	$("#tblFamily th").css('border-bottom','1px solid');
	$('td', $('.familyRow', $("#tblFamily")).eq($('.familyRow', $("#tblFamily")).length-1)).css('border-bottom','none');
}


/*********************************************************

	장기요양보험

*********************************************************/
function _clientCareShow(svcId,svcCd){
	var code     = $('#code').val();
	var jumin    = $('#jumin').val();
	var mode     = $('#writeMode_'+svcId).val();
	var mgmtNo   = $('#mgmtNo').attr('value');
	var mgmtFrom = $('#mgmtFrom').attr('value');
	var mgmtTo   = $('#mgmtTo').attr('value');
	var mgmtLvl  = $('#mgmtLvl').attr('value');
	var mgmtPay  = $('#mgmtPay').attr('value');
	var seq      = $('#mgmtSeq').val();

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!mgmtLvl) mgmtLvl = '3';
	if (!mgmtPay) mgmtPay = 0;

	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	if (mode == '1'){
		if (!mgmtFrom) mgmtFrom = $('#'+svcId+'_gaeYakFm').val();
		if (!mgmtTo) mgmtTo = $('#'+svcId+'_gaeYakTo').val();
	}

	objModal.code      = code;
	objModal.jumin     = jumin;
	objModal.svcId     = svcId;
	objModal.svcCd     = svcCd;
	objModal.mgmtNo    = mgmtNo;
	objModal.mgmtFrom  = mgmtFrom;
	objModal.mgmtTo    = mgmtTo;
	objModal.mgmtLvl   = mgmtLvl;
	objModal.mgmtLvlNm = '';
	objModal.mgmtPay   = mgmtPay;
	objModal.seq       = __str2num(seq);
	objModal.mode      = mode;
	objModal.type      = 2;
	objModal.result    = 1;

	window.showModalDialog(url, objModal, style);
	
	if (objModal.result < 0) return;
	if (objModal.result == 0){
		$('#mgmtNo').attr('value',objModal.mgmtNo).text(objModal.mgmtNo);
		$('#mgmtFrom').attr('value',objModal.mgmtFrom).text(objModal.mgmtFrom.split('-').join('.'));
		$('#mgmtTo').attr('value',objModal.mgmtTo).text((objModal.mgmtTo != '' ? ' ~ ' : '')+objModal.mgmtTo.split('-').join('.'));
		$('#mgmtLvl').attr('value',objModal.mgmtLvl).text(objModal.mgmtLvlNm);
		$('#mgmtPay').attr('value',objModal.mgmtPay).text(__num2str(objModal.mgmtPay));
		$('#mgmtSeq').attr('value',objModal.seq);
		
		_clientSetKindData(svcId);

		return;
	}
	
	_clientSetMgmtData(svcId);
}

function _clientSetMgmtData(svcId){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 2
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			if (!val['mgmtNo']) val['mgmtNo'] = $('#mgmtNo').attr('value');

			$('#mgmtNo').attr('value',val['mgmtNo']).text(val['mgmtNo']);
			$('#mgmtFrom').attr('value',__getDate(val['mgmtFrom'])).text(__getDate(val['mgmtFrom']).split('-').join('.'));
			$('#mgmtTo').attr('value',__getDate(val['mgmtTo'])).text((val['mgmtTo'] != '' ? ' ~ ' : '')+__getDate(val['mgmtTo']).split('-').join('.'));
			$('#mgmtLvl').attr('value',val['mgmtLvlCd']).text(val['mgmtLvlNm']);
			$('#mgmtPay').attr('value',val['mgmtPay']).text(__num2str(val['mgmtPay']));
			$('#mgmtSeq').val(val['seq']);
		},
		error: function (){
		}
	}).responseXML;

	_clientSetKindData(svcId);
}


/*********************************************************

	수급자구분

*********************************************************/
function _clientKindShow(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var mode  = $('#writeMode_'+svcId).val();
	var kind  = $('#expenseKind_'+svcId).attr('value');
	var rate  = $('#expenseRate_'+svcId).attr('value');
	var from  = $('#expenseFrom_'+svcId).attr('value');
	var to    = $('#expenseTo_'+svcId).attr('value');
	var seq   = $('#expenseSeq_'+svcId).attr('value');
	
	var mgmtLvl = $('#mgmtLvl').attr('value');
	var mgmtPay = $('#mgmtPay').attr('value');

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!kind) kind = '1';
	if (!rate){
		if (kind == '3')
			rate = '0.0';
		else if (kind == '2' || kind == '4')
			rate = '7.5';
		else
			rate = '15.0';
	}

	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	if (!from) from = $('#mgmtFrom').attr('value');
	if (!to) to = $('#mgmtTo').attr('value');
	
	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.kind    = kind;
	objModal.rate    = rate;
	objModal.from    = from;
	objModal.to      = to;
	objModal.seq     = __str2num(seq);
	objModal.mgmtLvl = mgmtLvl;
	objModal.mgmtPay = mgmtPay;
	objModal.mode    = mode;
	objModal.type    = 3;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);
	
	if (objModal.result < 0) return;
	if (objModal.result == 0){
		$('#expenseKind_11').attr('value',objModal.kind).text(objModal.kindNm);
		$('#expenseRate_11').attr('value',objModal.rate).text(objModal.rate+'%');
		$('#expenseAmt_11').attr('value',objModal.amt).text(__num2str(objModal.amt));
		$('#expenseFrom_11').attr('value',__getDate(objModal.from)).text(__getDate(objModal.from).split('-').join('.'));
		$('#expenseTo_11').attr('value',__getDate(objModal.to)).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#expenseSeq_11').attr('value',objModal.seq);

		return;
	}

	_clientSetKindData(svcId);
}

function _clientSetKindData(svcId){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 3
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);
			var mgmtPay = 0;

			if ($('#writeMode').val() != 1){
				$('#expenseKind_11').attr('value',val['kind']).text(val['kindNm']);
				$('#expenseRate_11').attr('value',val['rate']).text(val['rate']+'%');
				$('#expenseAmt_11').attr('value',val['amt']).text(__num2str(val['amt']));
				$('#expenseFrom_11').attr('value',__getDate(val['fromDt'])).text(__getDate(val['fromDt']).split('-').join('.'));
				$('#expenseTo_11').attr('value',__getDate(val['toDt'])).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
				$('#expenseSeq_11').attr('value',val['seq']);

				mgmtPay = __str2num(val['amt']);
			}else{
				mgmtPay = __str2num($('#mgmtPay').attr('value'));
				
				var expenseAmt = cutOff(__str2num($('#mgmtPay').attr('value')) * __str2num($('#expenseRate_11').attr('value')) / 100);
			
				$('#expenseAmt_11').attr('value',expenseAmt).text(__num2str(expenseAmt));
			}
			
			if (svcId == '11'){
				if ($('#writeMode').val() == 1 || __str2num($('#claimAmt_11').text()) == 0){
					$('#claimAmt_11').attr('value',$('#mgmtPay').attr('value')).text(__num2str($('#mgmtPay').attr('value')));
				}
			}
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************
	
	청구한도금액

*********************************************************/
function _clientClaimShow(svcId,svcCd){
	/*
	if(svcCd == '0'){
		if($('#expenseKind_'+svcId).val()=='1' || $('#expenseKind_'+svcId).val()=='4'){
			alert('기초,의료수급권자가만 등록이 가능합니다.');
			return;
		}
	}
	*/

	var code     = $('#code').val();
	var jumin    = $('#jumin').val();
	var mode     = $('#writeMode_'+svcId).val();
	
	if (!jumin || jumin == 'undefined') jumin = '';
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code   = code;
	objModal.jumin  = jumin;
	objModal.svcCd  = svcCd;

	objModal.from   = $('#claimFrom_11').attr('value');
	objModal.to     = $('#claimTo_11').attr('value');
	objModal.amt    = $('#claimAmt_11').attr('value');
	objModal.lvl    = $('#mgmtLvl').attr('value');
	objModal.maxPay = $('#mgmtPay').attr('value');
	objModal.kind   = $('#expenseKind_11').attr('value');
	objModal.rate   = $('#expenseRate_11').attr('value');

	if (!objModal.from) objModal.from = $('#mgmtFrom').attr('value');
	if (!objModal.to) objModal.to = $('#mgmtTo').attr('value');
	
	objModal.mode   = mode;
	objModal.type   = 4;
	objModal.result = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result < 0) return;
	if (objModal.result == 0){
		if (objModal.amt == 0){
			objModal.amt = __str2num($('#mgmtPay').attr('value'));
		}
		$('#claimAmt_11').attr('value',objModal.amt).text(objModal.amt);
		$('#claimFrom_11').attr('value',__getDate(objModal.from)).text(__getDate(objModal.from).split('-').join('.'));
		$('#claimTo_11').attr('value',__getDate(objModal.to)).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#claimSeq_11').attr('value',objModal.seq);

		return;
	}

	_clientSetLimitData();
}

function _clientSetLimitData(){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 4
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			if (__str2num(val['amt']) == 0){
				val['amt'] = __str2num($('#mgmtPay').attr('value'));
			}
			
			$('#claimAmt_11').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#claimFrom_11').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#claimTo_11').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#claimSeq_11').attr('value',val['seq']);
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	가사간병 서비스시간 이력내역

*********************************************************/
function _clientNurseShow(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var seq   = $('#nusreSeq').val();
	var val   = $('#nusreVal').val();
	var from  = $('#nusreFrom').val();
	var to    = $('#nusreTo').val();
	var mode  = $('#writeMode_'+svcId).val();

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.from    = from;
	objModal.to      = to;
	objModal.mode    = mode;
	objModal.type    = 5;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		$('#nusreSeq').attr('value',objModal.seq);
		$('#nusreVal').attr('value',objModal.val).text(objModal.time+'시간');
		$('#nurseAmt').attr('value',objModal.amt).text(objModal.amt);
		$('#nusreFrom').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
		$('#nusreTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
		$('#nusreStndDt').attr('value',__getDt(objModal.from,objModal.to));
		
		return;
	}

	_clientSetNurseData();
}
function _clientSetNurseData(){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 5
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			$('#nusreVal').attr('value',val['val']).text(val['time']+'시간');
			$('#nurseAmt').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#nusreFrom').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#nusreTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#nusreSeq').attr('value',val['seq']);
			$('#nusreStndDt').attr('value',__getDt(val['fromDt'],val['toDt']));
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	노인돌봄 이력내역

*********************************************************/
function _clientOldShow(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var seq   = $('#oldSeq').val();
	var val   = $('#oldVal').val();
	var time  = $('#oldTm').val();
	var from  = $('#oldFrom').val();
	var to    = $('#oldTo').val();
	var mode  = $('#writeMode_'+svcId).val();

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.time    = time;
	objModal.from    = from;
	objModal.to      = to;
	objModal.mode    = mode;
	objModal.type    = 7;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		$('#oldSeq').attr('value',objModal.seq);
		$('#oldVal').attr('value',objModal.val).text(objModal.valNm);
		$('#oldTm').attr('value',objModal.time).text('['+objModal.timeNm+']');
		$('#oldFrom').attr('value',objModal.from).text(__getDate(objModal.from).split('-').join('.'));
		$('#oldTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#oldAmt').attr('value',objModal.amt).text(__num2str(objModal.amt));
		
		return;
	}

	_clientSetOldData();
}
function _clientSetOldData(){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 7
		},
		beforeSend: function (){
		},
		success: function (result){
			//if ($('#code').val() == '34121000018') alert(result);
			var val = __parseStr(result);

			$('#oldVal').attr('value',val['val']).text(val['valNm']);
			$('#oldTm').attr('value',val['time']).text('['+val['timeNm']+']');
			$('#oldFrom').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#oldTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#oldAmt').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#oldSeq').attr('value',val['seq']);
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	산모신생아 이력내역

*********************************************************/
function _clientBabyShow(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var seq   = $('#babySeq').val();
	var val   = $('#babyVal').val();
	var from  = $('#babyFrom').val();
	var to    = $('#babyTo').val();
	var mode  = $('#writeMode_'+svcId).val();

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.from    = from;
	objModal.to      = to;
	objModal.mode    = mode;
	objModal.type    = 8;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		$('#babySeq').attr('value',objModal.seq);
		$('#babyVal').attr('value',objModal.val).text(objModal.valNm);
		$('#babyFrom').attr('value',objModal.from).text(__getDate(objModal.from).split('-').join('.'));
		$('#babyTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#babyAmt').attr('value',objModal.amt).text(__num2str(objModal.amt));
		
		return;
	}

	_clientSetBabyData();
}
function _clientSetBabyData(){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 8
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			$('#babyVal').attr('value',val['val']).text(val['valNm']);
			$('#babyFrom').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#babyTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#babyAmt').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#babySeq').attr('value',val['seq']);
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	장애인활동지원 이력내역

*********************************************************/
function _clientDisShow(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var seq   = $('#disSeq').val();
	var val   = $('#disVal').val();
	var lvl   = $('#disSpt').val();
	var from  = $('#disFrom').val();
	var to    = $('#disTo').val();
	var mode  = $('#writeMode_'+svcId).val();
	
	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.lvl     = lvl;
	objModal.from    = from;
	objModal.to      = to;
	objModal.mode    = mode;
	objModal.type    = 9;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		$('#disSeq').attr('value',objModal.seq);
		$('#disVal').attr('value',objModal.val).text(objModal.valNm);
		$('#disSpt').attr('value',objModal.lvl).text(objModal.lvl);
		$('#disFrom').attr('value',objModal.from).text(__getDate(objModal.from).split('-').join('.'));
		$('#disTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#stndTot').attr('value',objModal.amt).text(__num2str(objModal.amt));
		$('#stndTime').attr('value',objModal.time).text(__num2str(objModal.time));
		
		_clientSetDisExpense();
		
		return;
	}

	_clientSetDisData();
}


function _clientDisShow2(svcId,svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();
	var seq   = $('#disSeq').val();
	var val   = $('#disVal').val();
	var lvl   = $('#disSpt').val();
	var from  = $('#disFrom').val();
	var to    = $('#disTo').val();
	var mode  = $('#writeMode_'+svcId).val();
	
	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.lvl     = lvl;
	objModal.from    = from;
	objModal.to      = to;
	objModal.mode    = mode;
	objModal.type    = 10;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);
	
	if (objModal.result == 0){
		$('#disSeq2').attr('value',objModal.seq);
		$('#disVal2').attr('value',objModal.val).text(objModal.valNm);
		$('#disSpt2').attr('value',objModal.lvl).text(objModal.lvl);
		$('#disFrom2').attr('value',objModal.from).text(__getDate(objModal.from).split('-').join('.'));
		$('#disTo2').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+__getDate(objModal.to).split('-').join('.'));
		$('#stndTot2').attr('value',objModal.amt).text(__num2str(objModal.amt));
		$('#stndTime2').attr('value',objModal.time).text(__num2str(objModal.time));
		
		_clientSetDisExpense();
		
		return;
	}

	_clientSetDisDataNew();
}

function _clientSetDisData(){
	
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 9
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);

			$('#disVal').attr('value',val['val']).text(val['valNm']);
			$('#disSpt').attr('value',val['lvl']);
			$('#disFrom').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#disTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#stndTot').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#stndTime').attr('value',val['time']).text(val['time']);
			$('#disSeq').attr('value',val['seq']);

			_clientSetDisExpense();
		},
		error: function (){
		}
	}).responseXML;
}

function _clientSetDisDataNew(){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()		
		,	mode   : 10
		},
		beforeSend: function (){
		},
		success: function (result){
			var val = __parseStr(result);
			
			$('#disVal2').attr('value',val['val']).text(val['valNm']);
			$('#disSpt2').attr('value',val['lvl']);
			$('#disFrom2').attr('value',val['fromDt']).text(__getDate(val['fromDt']).split('-').join('.'));
			$('#disTo2').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+__getDate(val['toDt']).split('-').join('.'));
			$('#stndTot2').attr('value',val['amt']).text(__num2str(val['amt']));
			$('#stndTime2').attr('value',val['time']).text(val['time']);
			$('#disSeq2').attr('value',val['seq']);

			_clientSetDisExpenseNew();
		},
		error: function (){
		}
	}).responseXML;
}


function _clientSetDisExpense(){
	var expenseAmt = __str2num($('#stndExpense').attr('value'));
	var supportAmt = __str2num($('#stndTot').attr('value')) - expenseAmt;

	$('#stndSupport').attr('value',supportAmt).text(__num2str(supportAmt));
	$('#disLoadYn').attr('value1','Y');

	if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y') lfAddPayLoad();
}

function _clientSetDisExpenseNew(){
	var expenseAmt = __str2num($('#stndExpense2').attr('value'));
	var supportAmt = __str2num($('#stndTot2').attr('value')) - expenseAmt;

	$('#stndSupport2').attr('value',supportAmt).text(__num2str(supportAmt));
	$('#disLoadYn').attr('value1','Y');

	if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y') lfAddPayLoad();
}

/*********************************************************

	소득등급 변경 이력

*********************************************************/
function _clientLvlShow(svcId,svcCd){
	var code   = $('#code').val();
	var jumin  = $('#jumin').val();
	var mode   = $('#writeMode_'+svcId).val();
	var seq    = '0';
	var val    = '';
	var spt    = '';
	var time   = '';
	var lvl    = '';
	var from   = '';
	var to     = '';
	var stndDt = '';
	
	switch(svcCd){
		case '1':
			seq    = $('#nusreLvlSeq').val();	
			val    = $('#nusreVal').val();
			lvl    = $('#nusreLvl').val();
			from   = $('#nusreLvlFrom').val();
			to     = $('#nusreLvlTo').val();
			stndDt = $('#nusreStndDt').attr('value');
			break;

		case '2':
			seq  = $('#oldLvlSeq').val();	
			val  = $('#oldVal').val();
			time = $('#oldTm').val();
			lvl  = $('#oldLvl').val();
			from = $('#oldLvlFrom').val();
			to   = $('#oldLvlTo').val();
			break;

		case '3':
			seq  = $('#babyLvlSeq').val();	
			val  = $('#babyVal').val();
			lvl  = $('#babyLvl').val();
			from = $('#babyLvlFrom').val();
			to   = $('#babyLvlTo').val();
			break;

		case '4':
			seq  = $('#disLvlSeq').val();	
			val  = $('#disVal').val();
			spt  = $('#disSpt').val();
			lvl  = $('#disLvl').val();
			from = $('#disLvlFrom').val();
			to   = $('#disLvlTo').val();
			break;
	}

	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.time    = time;
	objModal.spt     = spt;
	objModal.lvl     = lvl;
	objModal.from    = from;
	objModal.to      = to;
	objModal.stndDt  = stndDt;
	objModal.mode    = mode;
	objModal.type    = 6;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		switch(svcCd){
			case '1':
				$('#nusreLvlSeq').attr('value',objModal.seq);
				$('#nusreLvl').attr('value',objModal.lvl).text(objModal.lvlNm);
				$('#nusreLvlFrom').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
				$('#nusreLvlTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
				break;

			case '2':
				$('#oldLvlSeq').attr('value',objModal.seq);
				$('#oldLvl').attr('value',objModal.lvl).text(objModal.lvlNm);
				$('#oldLvlFrom').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
				$('#oldLvlTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
				break;

			case '3':
				$('#babyLvlSeq').attr('value',objModal.seq);
				$('#babyLvl').attr('value',objModal.lvl).text(objModal.lvlNm);
				$('#babyLvlFrom').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
				$('#babyLvlTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
				break;

			case '4':
				$('#disLvlSeq').attr('value',objModal.seq);
				$('#disLvl').attr('value',objModal.lvl).text(objModal.lvlNm);
				$('#disLvlFrom').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
				$('#disLvlTo').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
				break;
		}

		if (svcCd == '4'){
			$('#stndExpense').attr('value',objModal.amt).text(__num2str(objModal.amt));
			$('#disLoadYn').attr('value2','Y');
			
			if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y'){
				lfAddPayLoad();
				_clientSetDisExpense();
			}
		}else{
			$('#expenseAmt_'+svcId).text(__num2str(objModal.amt));
		}
		
		return;
	}

	_clientSetLevelData(svcId,svcCd);
}


/*********************************************************

	장애인(신) 소득등급 변경 이력

*********************************************************/
function _clientLvlShowNew(svcId,svcCd){
	var code   = $('#code').val();
	var jumin  = $('#jumin').val();
	var mode   = $('#writeMode_'+svcId).val();
	var seq    = '0';
	var val    = '';
	var spt    = '';
	var time   = '';
	var lvl    = '';
	var from   = '';
	var to     = '';
	var stndDt = '';
	
	
	seq  = $('#disLvlSeq').val();	
	val  = $('#disVal2').val();
	spt  = $('#disSpt2').val();
	lvl  = $('#disLvl').val();
	from = $('#disLvlFrom').val();
	to   = $('#disLvlTo').val();


	if (!jumin || jumin == 'undefined') jumin = '';
	if (!seq) seq = 0;
	
	var objModal = new Object();
	var url      = './client_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code    = code;
	objModal.jumin   = jumin;
	objModal.svcId   = svcId;
	objModal.svcCd   = svcCd;
	objModal.seq     = seq;
	objModal.val     = val;
	objModal.time    = time;
	objModal.spt     = spt;
	objModal.lvl     = lvl;
	objModal.from    = from;
	objModal.to      = to;
	objModal.stndDt  = stndDt;
	objModal.mode    = mode;
	objModal.type    = 11;
	objModal.result  = 1;

	window.showModalDialog(url, objModal, style);

	if (objModal.result == 0){
		$('#disLvlSeq2').attr('value',objModal.seq);
		$('#disLvl2').attr('value',objModal.lvl).text(objModal.lvlNm);
		$('#disLvlFrom2').attr('value',objModal.from).text(objModal.from.split('-').join('.'));
		$('#disLvlTo2').attr('value',objModal.to).text((objModal.to != '' ? ' ~ ' : '')+objModal.to.split('-').join('.'));
	
		$('#stndExpense2').attr('value',objModal.amt).text(__num2str(objModal.amt));
		$('#disLoadYn').attr('value2','Y');
		
		if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y'){
			lfAddPayLoad();
			_clientSetDisExpense();
		}
	
		return;
	}

	_clientSetLevelDataNew(svcId,svcCd);
}


function _clientSetLevelData(svcId,svcCd){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code  : $('#code').val()
		,	jumin : $('#jumin').val()
		,	svcCd : svcCd
		,	mode  : 6
		},
		beforeSend: function (){
		},
		success: function (result){
			if ($('#code').val() == '34121000018'){
				//alert(result);
			}
			var val = __parseStr(result);

			switch(svcCd){
				case '1':
					$('#nusreLvlSeq').attr('value',val['seq']);
					$('#nusreLvl').attr('value',val['lvl']).text(val['lvlNm']);
					$('#nusreLvlFrom').attr('value',val['fromDt']).text(val['fromDt'].split('-').join('.'));
					$('#nusreLvlTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+val['toDt'].split('-').join('.'));
					break;

				case '2':
					$('#oldLvlSeq').attr('value',val['seq']);
					$('#oldLvl').attr('value',val['lvl']).text(val['lvlNm']);
					$('#oldLvlFrom').attr('value',val['fromDt']).text(val['fromDt'].split('-').join('.'));
					$('#oldLvlTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+val['toDt'].split('-').join('.'));
					break;

				case '3':
					$('#babyLvlSeq').attr('value',val['seq']);
					$('#babyLvl').attr('value',val['lvl']).text(val['lvlNm']);
					$('#babyLvlFrom').attr('value',val['fromDt']).text(val['fromDt'].split('-').join('.'));
					$('#babyLvlTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+val['toDt'].split('-').join('.'));
					break;

				case '4':
					$('#disLvlSeq').attr('value',val['seq']);
					$('#disLvl').attr('value',val['lvl']).text(val['lvlNm']);
					$('#disLvlFrom').attr('value',val['fromDt']).text(val['fromDt'].split('-').join('.'));
					$('#disLvlTo').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+val['toDt'].split('-').join('.'));
					break;
			}
			
			if (svcCd == '4'){
				$('#stndExpense').attr('value',val['amt']).text(__num2str(val['amt']));
				$('#disLoadYn').attr('value2','Y');
				
				if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y'){
					lfAddPayLoad();
					_clientSetDisExpense();
				}
			}else{
				$('#expenseAmt_'+svcId).text(__num2str(val['amt']));
			}
		},
		error: function (){
		}
	}).responseXML;
}

//장애인(신)
function _clientSetLevelDataNew(svcId,svcCd){
	$.ajax({
		type: 'POST',
		url : './client_breakdown.php',
		data: {
			code  : $('#code').val()
		,	jumin : $('#jumin').val()
		,	svcCd : svcCd
		,	mode  : 11
		},
		beforeSend: function (){
		},
		success: function (result){
			if ($('#code').val() == '34121000018'){
				//alert(result);
			}
			var val = __parseStr(result);

			$('#disLvlSeq2').attr('value',val['seq']);
			$('#disLvl2').attr('value',val['lvl']).text(val['lvlNm']);
			$('#disLvlFrom2').attr('value',val['fromDt']).text(val['fromDt'].split('-').join('.'));
			$('#disLvlTo2').attr('value',val['toDt']).text((val['toDt'] != '' ? ' ~ ' : '')+val['toDt'].split('-').join('.'));
					
			
			if (svcCd == '4'){
				$('#stndExpense2').attr('value',val['amt']).text(__num2str(val['amt']));
				$('#disLoadYn').attr('value2','Y');
				
				if ($('#disLoadYn').attr('value1') == 'Y' && $('#disLoadYn').attr('value2') == 'Y'){
					lfAddPayLoad();
					_clientSetDisExpense();
				}
			}else{
				$('#expenseAmt_'+svcId).text(__num2str(val['amt']));
			}
		},
		error: function (){
		}
	}).responseXML;
}


/*********************************************************

	고객 저장 파라메타

*********************************************************/
function _clientSetPara(){
	var para = '';
	
	$('.clsData').each(function(){
		para += (para ? '&' : '')+$(this).attr('id')+'='+$(this).attr('value');
	});

	para += (para ? '&' : '')+'addPay2=';
	$('input:checkbox[name="addPay2"]:checked').each(function(){
		para += '/'+$(this).attr('value');
	});

	para += (para ? '&' : '')+'svcList=';
	$('div[id^="loSvc_"]').each(function(){
		para += '/'+$(this).attr('value');
	});

	para += (para ? '&' : '')+'useList=';
	$('div[id^="loSvc_"]').each(function(){
		if ($(this).css('display') != 'none')
			para += '/'+$(this).attr('value');
	});

	//가족보호사
	para += (para ? '&' : '')+'familyList=';
	$('span[id^="strFamilyNM_"]').each(function(){
		var liIdx = $(this).attr('id').split('strFamilyNM_').join('');
		var lsVal = $('#objFamilyCD_'+liIdx).val();
		var lsGbn = $('#objFamilyGbn_'+liIdx).val();

		if (lsVal){
			lsVal = __parseStr(lsVal);
			para += '/'+lsVal['jumin']+';'+lsVal['name']+';'+lsGbn;
		}
	});

	return para;
}



/*********************************************************
 * 서비스 등록
 *********************************************************/
function _clientSvcReg(svcCd,svcId){
	var h = screen.availHeight;
    var w = 500; //screen.availWidth;
	var l = (screen.availWidth - w) / 2;
    var t = 100;
    
	if(w >= 800) w = 800;
    
	h = 200;

	var win = null;

	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
    var url    = './client_svc_reg.php';
		win = window.open('', 'SVCREG', option);
		win.opener = self;
		win.focus();

	var code  = $('#code').attr('value');
	var jumin = $('#jumin').attr('value');

	if (!jumin || jumin == 'undefined') jumin = '';

	var parm = new Array();
		parm = {
			code  : code
		,	jumin : jumin
		,	svcCd : svcCd
		,	svcId : svcId
		};

	var form = document.createElement('form');
    var objs;
    for(var key in parm){
        objs = document.createElement('input');
        objs.setAttribute('type', 'hidden');
        objs.setAttribute('name', key);
        objs.setAttribute('value', parm[key]);
        
		form.appendChild(objs);
    }

    form.setAttribute('target', 'SVCREG');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);

	window.onunload = function(){
		win.close();
	}

	form.submit();

	return false;
}


/*********************************************************
 * 서비스 계약기간 체크
 *********************************************************/
function _clientPeriod(svcCd,svcId){
	var ldFrom = $('#txtFrom_'+svcId).attr('value');
	var ldTo   = $('#txtTo_'+svcId).attr('value');
	var lsResult = '';

	$('div[id^="loSvc_"]').each(function(){
		var val = $(this).attr('value');
			val = val.split('_');

		var cd   = val[0];
		var id   = val[1];
		var is   = false;
		
		skip = false;

		if (cd == svcCd){
			skip = true;
		}else if (cd == 'A' || cd == 'B' || cd == 'C'){
			skip = true;
		}else if (cd == '3'){
			if (svcCd == '4')
				skip = true;
		}else if (cd == '4'){
			if (svcCd == '3')
				skip = true;
		}else if (lsResult != ''){
			skip = true;
		}

		if (!skip){
			var svcNm  = $('#svcNm_'+id).val();
			var fromDt = $('#txtFrom_'+id, this).attr('value');
			var toDt   = $('#txtTo_'+id, this).attr('value');

			if ($(this).css('display') != 'none'){
				if (ldFrom >= fromDt && ldFrom <= toDt){
					lsResult = svcNm+'의 계약내역과 중복됩니다.';
				}else if (ldTo >= fromDt && ldTo <= toDt){
					lsResult = svcNm+'의 계약내역과 중복됩니다.';
				}
			}
		}
	});

	if (lsResult != ''){
		$('#txtStat_'+svcId).attr('value','').text('');
		$('#txtReason_'+svcId).attr('value','').text('');
		$('#txtFrom_'+svcId).attr('value','').text('');
		$('#txtTo_'+svcId).attr('value','').text('');
	}
}


/*********************************************************
 *	주야간보호 비급여관리
 *********************************************************/
function _clientDanNpmt(){
	var jumin = $('#jumin').val();
	
	var objModal= new Object();
	var url		= '../dan/npmt.php';
	var style	= 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

	objModal.win	= window;
	objModal.jumin	= jumin;

	window.showModalDialog(url, objModal, style);

	$('#lblDanNpmtCnt').text(objModal.cnt);
	$('#lblDanNpmtAmt').text(__num2str(objModal.amt));
}
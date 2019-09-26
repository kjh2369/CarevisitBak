var g_PlanListWin = null; //일정팝업창
var g_Timer = null; //타이머
var g_TmpTimer = null;
var g_Int = 0;
var g_LongcareWin = {};
var g_Client = null;
var g_Admin = null;

var g_ErrorCnt = 0;
var g_WinID;

var g_chgSayu = '', g_chgSayuEtc = '';

var srcTgtPrsnCd    = new Array();
var srcAmdtGradeCd  = new Array();
var src986Cd = new Array();
var togatherExt = new Array();

var firstDn20Yn = '';
var addDn20Yn = '';
var firstDn20 = '';
var addDn20 = '';
var admtGradeCd = '';

/*********************************************************

	일정계획리스트

*********************************************************/
function _iljungPlanList(svcKind, uploadYN, asYYMM){
	/*
	$("#iljung_planlist").load('./iljung_planlist.php?code='+$('#code').attr('value')+'&jumin='+$('#jumin').attr('value')+'&yymm='+$('#year').attr('value')+''+$('#month').attr('value')+'&svcKind='+svcKind, function (){
		$pos = $('#tblCenterInfo').offset();

		$(this).css('background-color', '#ffffff').css('left', $pos.left - 2).css('top', $pos.top + 25);
		$(this).show();
	});
	*/

	var h = screen.availHeight;
    var w = screen.availWidth;
    var t = 0;
    
	if(w >= 800) w = 800;
    
	h = 200;

	if (g_PlanListWin != null){
		g_PlanListWin.close();
		g_PlanListWin = null;
	}

    var option      = 'left=0, top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
    var url         = './iljung_planlist.php';
		g_PlanListWin = window.open('', 'PLANLIST', option);
		g_PlanListWin.opener = self;
		g_PlanListWin.focus();

	if (!asYYMM) asYYMM = $('#year').attr('value')+''+$('#month').attr('value');

	var parm = new Array();
		parm = {
			'code'	  : $('#code').attr('value')
		,	'jumin'	  : $('#jumin').attr('value')
		,	'yymm'	  : asYYMM //$('#year').attr('value')+''+$('#month').attr('value')
		,	'svcKind' : svcKind
		,	'uploadYN': uploadYN
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

    form.setAttribute('target', 'PLANLIST');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
}
function _planListNew(svcKind, uploadYN, chgSayu, chgSayuEtc, lgPara){
	var h = screen.availHeight;
    var w = screen.availWidth;
    var t = 0;
    
	if(w >= 800) w = 800;
    
	h = 200;

	if (!chgSayu) chgSayu = '';
	if (!chgSayuEtc) chgSayuEtc = '';

	g_chgSayu = chgSayu;
	g_chgSayuEtc = chgSayuEtc;

	if (g_PlanListWin != null){
		g_PlanListWin.close();
		g_PlanListWin = null;
	}

    var option      = 'left=0, top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
    var url         = './iljung_planlist.php';
		g_PlanListWin = window.open('', 'PLANLIST', option);
		g_PlanListWin.opener = self;
		g_PlanListWin.focus();

	var parm = new Array();
		parm = {
			'code'		:$('#centerInfo').attr('value')
		,	'jumin'		:$('#clientInfo').attr('value')
		,	'yymm'		:$('#planInfo').attr('year')+''+$('#planInfo').attr('month')
		,	'svcKind'	:svcKind
		,	'uploadYN'	:uploadYN
		,	'lgPara'	:lgPara
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

    form.setAttribute('target', 'PLANLIST');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
}




/*********************************************************

	수급자 장기요양 인증번호
	
*********************************************************/
function _iljungGetLongTermMgmtNo(svcKind, uploadYN, paraNo, mgmtYn, juminNo, abAdmin, chgSayu, chgSayuEtc, winID){
	g_Client = juminNo;
	g_Admin  = abAdmin;

	if (!chgSayu) chgSayu = '';
	if (!chgSayuEtc) chgSayuEtc = '';
	if (chgSayu != '04') chgSayuEtc = '';

	g_chgSayu = chgSayu;
	g_chgSayuEtc = chgSayuEtc;

	if (!winID) winID = '';

	g_WinID = winID;
	
	if (mgmtYn == 'Y'){
		valNo = paraNo.split(' ').join('').toUpperCase().substring(0,11);

		if (valNo.substring(0,1) != 'L'){
			alert('수급자 인정번호오류입니다. 인정번호를 다시 확인하여 주십시오.');
			return;
		}
	}else{
		if (!paraNo){
			var valNo = getHttpRequest('../inc/_ed_code.php?type=2&value='+$('#jumin').attr('value'));
		}else{
			var valNo = paraNo;
		}
	}

	var longTermMgmtNo  = '';
	var longTermMgmtSeq = '';
	
	/*
	 * 32717000065 좋은인연
	 * 31141000055, 31141000126 징검다리요양센터
	 */

	try{
		$.ajax({
			type : 'POST'
		,	url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=TR' //TR->PR
		,	data : {
				'longTermAdminSym'	: $('#giho').attr('value')
			,	'longTermAdminNm'	: $('#strCenterName').text()
			,	'adminKindCd'		: 'C'
			,	'searchPayYyyy'		: '20%'
			,	'searchGbn'			: (mgmtYn == 'Y' ? 'searchMgmtNo' : 'searchJuminNo')
			,	'searchValue'		: valNo
			,	'searchDt'			: 'searchCtrDt'
			,	'fnc'				: 'select'
			}
		,	beforeSend: function(){
			}
		,	success: function (data){
				var selCheck = '';

				if (mgmtYn == 'Y'){
					selCheck = 'value2';
				}else{
					selCheck = 'value3';
				}

				var addFlag = false;

				var selectCheck = $('input[type="checkbox"][name="selectCheck"]',data).val();
				var scIdx = selectCheck.indexOf('|'+$('#clientAppNo').text());

				if (scIdx >= 0) addFlag = true;

				//if ($('input[type=\'checkbox\'][name=\'selectCheck\']['+selCheck+'=\''+valNo+'\']', data).val()){
				if (addFlag){
					//longTermMgmtNo  = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value2');
					//longTermMgmtSeq = $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value4');
					//payCtrNo		= $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value');
					//tgtDemoChasu	= $('input[type=\'checkbox\'][name=\'selectCheck\']', data).attr('value5');
					
					var addInfo = selectCheck.split('|');

					longTermMgmtNo	= addInfo[1].split(' ').join('');
					longTermMgmtSeq	= addInfo[3].split(' ').join('');
					jumin			= addInfo[2].split(' ').join('');
					payCtrNo		= addInfo[0].split(' ').join('');
					tgtDemoChasu	= addInfo[4].split(' ').join('');

					_iljungGetPayCtrNo(juminNo, longTermMgmtNo, svcKind, uploadYN, longTermMgmtSeq,payCtrNo,tgtDemoChasu);
				}else{
					alert('건보공단에서 수급자를 찾을 수 없습니다.\n\n\n- 건보공단에 수급자가 등록되어 있는지 확인하여 주십시오.');
					self.close();
				}
			}
		,	error: function (request, status, error){
				if (g_ErrorCnt == 0){
					g_ErrorCnt ++;
					_iljungGetLongTermMgmtNo(svcKind, uploadYN, paraNo, mgmtYn, juminNo, abAdmin, chgSayu, chgSayuEtc);
				}else{
					alert("CODE : " + request.status+"/"+status+"/"+error+"\nMESSAGE : " + request.responseText);
					self.close();
				}
			}
		});
	}catch(e){
		alert(e);
	}
}


/*********************************************************

	수급자 계약번호 가져오기

*********************************************************/
function _iljungGetPayCtrNo(jumin, longTermMgmtNo, svcKind, uploadYN, longTermMgmtSeq,payCtr,demoChasu){
	var YYMM = $('#year').attr('value')+''+$('#month').attr('value');
	var payCtrNo = '';
	var selCheck = '';
	var valNo    = '';
	var data = {};

	data['payCtrNo']		= payCtr;
	data['tgtJuminNo']		= jumin;
	data['longTermMgmtNo']	= longTermMgmtNo;
	data['longTermMgmtSeq'] = longTermMgmtSeq;
	data['tgtDemoChasu']	= demoChasu;
	data['fnc']				= 'select';

	//if ($('#code').attr('value') == '34824000054'){
	//	alert(payCtr+'/'+jumin+'/'+longTermMgmtNo+'/'+longTermMgmtSeq+'/'+tgtDemoChasu);
	//	return false;
	//}
	
	$.ajax({
		type : 'POST',
		url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=PR',  //TR->PR
		data: data/*{
			'tgtJuminNo'	: jumin
		,	'longTermMgmtNo': longTermMgmtNo
		,	'fnc'			: 'select'
		//,'adminKindCd'	: 'C'
		}*/,
		success: function (data){
			$('#npayTable td:nth-child(1)', data).each(function(){
				var tmpTerm = $(this).text().replace(/[^0-9]/g, '');
				var tmpYm_s = tmpTerm.substr(0,6);
				var tmpYm_e = tmpTerm.substr(8,6);

				if (tmpYm_s<=YYMM && tmpYm_e>=YYMM){
					var selectCheck = $('input[type="checkbox"][name="selectCheck"]',$(this).parent()).val();
					var addInfo = selectCheck.split('|');

					//payCtrNo	= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value3');
					//tgtDemoChasu= $('input[type=\'checkbox\'][name=\'selectCheck\'][value4=\''+svcKind+'\']', $(this).parent()).attr('value5');
					
					var tmpKind = addInfo[3].split(' ').join('');

					if (svcKind == tmpKind){
						payCtrNo	= addInfo[2].split(' ').join('');
						tgtDemoChasu= addInfo[7].split(' ').join('');
					}
					
					if (payCtrNo){
						if (longTermMgmtSeq != addInfo[5].split(' ').join('')) longTermMgmtSeq = addInfo[5].split(' ').join('');
						return false; //계약번호 있을경우 반복문 종료
					}
				}
			});

			if (payCtrNo) {
				if (uploadYN != 'Y'){
					_iljungCareResult(YYMM, payCtrNo, longTermMgmtNo, svcKind);
				}else{
					g_ErrorCnt = 0;
					_iljungLongcareGetMem(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin);
				}
			}else{
				alert('건보공단에서 수급자의 계약번호를 찾을 수 없습니다.\n\n\n1. 건보공단에 해당월('+YYMM+') 급여계약내역이 등록되어 있는지 확인하여 주십시오.');
				self.close();
			}
		},
		error: function (request, status, error){
			alert('[ERROR No.02]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
		}
	});
}


/*********************************************************

	건보공단 일정입력화면 열기

*********************************************************/
function _iljungLongcareWinOpen(jumin, YYMM, payCtrNo, longTermMgmtNo, svcKind){
	var h= screen.availHeight;
    var w = screen.availWidth;
    var t = 250;
   
	if(w>=1350) w = 1350;
    
	h = h - t - 150;

	var opt = 'left=0, top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=no';
	var url = 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU';
    var tmpWin = window.open('', 'LONGCARE_POSTWIN', opt);
		tmpWin.opener = self;
		tmpWin.focus();

	var parm = new Array();
		parm = {
			'payCtrNo'		: payCtrNo
		,	'payMm'			: YYMM
		,	'longTermMgmtNo': longTermMgmtNo
		,	'serviceKind'	: svcKind
		,	'fnc'			: 'select'
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

    form.setAttribute('target', 'LONGCARE_POSTWIN');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    document.body.appendChild(form);
    form.submit();

	g_Int = 0;
	g_Timer = setInterval('_longcareTest2()',1000);
}



/*********************************************************

	요양보호사 정보

*********************************************************/
function _iljungLongcareGetMem(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin){
	var data = {};

	//기존
	/*
		data['serviceKind']	= svcKind;
		data['payMm']		= YYMM;
		data['fnc']			= 'care';
	 */
	if (svcKind == '004'){
		setTimeout('_iljungSetGreadPrate("'+YYMM+'","'+payCtrNo+'","'+longTermMgmtNo+'","'+svcKind+'","'+longTermMgmtSeq+'","'+tgtDemoChasu+'","'+jumin+'")',100);
		return;
	}

	data['serviceKind']	= svcKind;
	data['payMm']		= YYMM;
	data['tgtDemoChasu']= tgtDemoChasu;
	data['fnc']			= 'care';

	$.ajax({
		type:'POST',
		url:'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=YR',
		data:data,
		success: function (data){
			var isMem = true;

			$('.planList').each(function(){
				isMem = _iljungLongcareGetMemSub(data, $(this), '1');
				if (!isMem) return false;

				if ($(this).attr('memCD2')) isMem = _iljungLongcareGetMemSub(data, $(this), '2');
				if (!isMem) return false;
			});

			//건보 업로드시 0.3초의 딜레이를 준다.
			g_ErrorCnt = 0;

			if (isMem) setTimeout('_iljungSetGreadPrate("'+YYMM+'","'+payCtrNo+'","'+longTermMgmtNo+'","'+svcKind+'","'+longTermMgmtSeq+'","'+tgtDemoChasu+'","'+jumin+'")',300);
		},
		error: function (request, status, error){
			if (g_ErrorCnt == 0){
				_iljungLongcareGetMem(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin);
				g_ErrorCnt ++;
			}else{
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}
	});
}


/*********************************************************

	일정정보 찾기

*********************************************************/
function _iljungLongcareGetMemSub(data, obj, idx){
	var memNM = $(obj).attr('memNM'+idx);
	var memCD = getHttpRequest('../inc/_ed_code.php?type=2&value='+$(obj).attr('memCD'+idx));
		memCD = memCD.substring(0,6)+'-'+memCD.substring(6,13);

	var memStrID = '';

	if ($('td:contains("'+memCD+'")', data).length > 1){
		var tmpNo = null;

		$('td:contains("'+memCD+'")', data).each(function(){
			tmpNo = $(this).attr('id').replace('careJuminNo','').replace('qlfNo','');

			if (memStrID == '' && $('#qlfNo'+tmpNo, data).text() != ''){
				memStrID = $(this).attr('id');
			}
		});	
	}else{
		memStrID = $('td:contains("'+memCD+'")', data).attr('id');
	}

	if (!memStrID){
		alert('건보공단에서 '+memNM+'요양보호사의 정보를 찾을 수 없습니다.\n\n확인하여 주십시오.');
		return false;
	}

	var rowNo   = memStrID.replace('careJuminNo','').replace('qlfNo','');
	var qlfNo   = $('#qlfNo'+rowNo, data).text();
	var qlfKind = $('#qlfKind'+rowNo, data).text();
	var careNm  = $('#careNm'+rowNo, data).text();

	//if (!qlfNo || !qlfKind || !careNm){
	//if (!qlfNo || !qlfKind || !careNm){

	if ($('#giho').attr('value') == '31138000102'){
		//사랑케어는 qlfNo를 체크에서 제한다.
		if (!qlfKind || !careNm){
			alert(memNM+'요양보호사의 정보를 찾을 수 없습니다.\n\n확인하여 주십시오.');
			return false;
		}
	}else{
		if (!qlfNo || !qlfKind || !careNm){
			alert(memNM+'요양보호사의 정보를 찾을 수 없습니다.\n\n확인하여 주십시오.');
			return false;
		}
	}
		
	$(obj).attr('qlfNo'+(idx != '1' ? '2' : ''), qlfNo);
	$(obj).attr('qlfKind'+(idx != '1' ? '2' : ''), qlfKind);
	$(obj).attr('careNm'+(idx != '1' ? '2' : ''), careNm);
	
	return true;
}


/*********************************************************

	일자별 등급 및 본인부담 구분

*********************************************************/
function _iljungSetGreadPrate(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin){  //일자별 등급 및 본인부담 구분 가져오기
	$.ajax({
		type:'POST',
		url:'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU',
		data:{
			'payCtrNo':payCtrNo
		,	'payMm':YYMM
		,	'longTermMgmtNo':longTermMgmtNo
		,	'longTermMgmtSeq':longTermMgmtSeq
		,	'tgtJuminNo':jumin
		,	'serviceKind':svcKind
		,	'tgtDemoChasu':tgtDemoChasu
		,	'fnc':'select'
		},
		success:function(data){
			//if ($('#giho').attr('value') == '34824000009'){
			//	document.write(data);
			//	return;
			//}

			firstDn20Yn = _iljungGetString(data, 'document.form1.firstDn20Yn.value');
			addDn20Yn = _iljungGetString(data, 'document.form1.addDn20Yn.value');
			firstDn20 = _iljungGetString(data, 'document.form1.firstDn20.value');
			addDn20 = _iljungGetString(data, 'document.form1.addDn20.value');
			admtGradeCd = _iljungGetString(data, 'document.form1.admtGradeCd.value');

			var i = 1;
			$('input[name=\'srcTgtPrsnCd\']', data).each(function (){
				srcTgtPrsnCd[i] = $(this).val(); i++;
			});

			var i = 1;
			$('input[name=\'srcAmdtGradeCd\']', data).each(function (){
				srcAmdtGradeCd[i] = $(this).val(); i++;
			});

			var i = 1;
			$('input[name=\'src986Cd\']',data).each(function(){
				src986Cd[i] = $(this).val(); i++;
			});

			var i = 1;
			$('input[name=\'togatherExt\']',data).each(function(){
				togatherExt[i] = $(this).val(); i++;
			});
			
			_iljungSetCareStr(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin);
		},
		error:function(request, status, error){
			if (g_ErrorCnt == 0){
				g_ErrorCnt ++;
				_iljungSetGreadPrate(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin);
			}else{
				alert("CODE : " + request.status+"/"+status+"/"+error+"\nMESSAGE : " + request.responseText+"\n건보공단에서 수급자 일자별 등급 및 본인부담율 페이지 로드 실패하였습니다.");
				self.close();
			}
		}
	});
}


function _iljungGetString(original, find){
	var i = original.indexOf(find);
	var i1 = original.indexOf('=',i+1);
	var i2 = original.indexOf(';',i1+1);
	var v = original.substr(i1+1,i2-i1);

	v = v.split('"').join('');
	v = v.split(';').join('');
	v = v.split(' ').join('');

	return v;
}

/*********************************************************

	계약내역 등록 설정

*********************************************************/
function _iljungSetCareStr(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin){
	//document.form1.careValue.value = "201108 =2009-1005720 =12 =김경화 =5909062791925 =1100 =1500 =B1024000 =39500 =B1024100 =51350 =B1109000 =21360 =B1206000 =16120 =240분이상(방문당)";
	var tmp = '', careValue = '', initCareValue = '';
	
	$('.planList').each(function (){
		var qlfNo	= $(this).attr('qlfNo');
		var qlfKind	= $(this).attr('qlfKind');
		var careNm	= $(this).attr('careNm');

		if (!qlfNo) qlfNo = '';
		if (!qlfKind) qlfKind = '';
		if (!careNm) careNm = '';
		
		var memCD2	= getHttpRequest('../inc/_ed_code.php?type=2&value='+$(this).attr('memCD2'));
		var careNm2	= $(this).attr('careNm2');
		var qlfNo2	= $(this).attr('qlfNo2');
		var qlfKind2= $(this).attr('qlfKind2');

		if (!memCD2) memCD2 = '';
		if (!careNm2) careNm2 = '';
		if (!qlfNo2) qlfNo2 = '';
		if (!qlfKind2) qlfKind2 = '';

		if (g_Admin == '1'){
			careNm = _longcareHex2Dec(careNm);
			
			if (careNm2){
				careNm2 = _longcareHex2Dec(careNm2);		
			}
		}
		
		$('div[@class=\'clsPlan\']', this).each(function(){
			var intAge = agechange(jumin.substring(0,6),jumin.substring(6,13),$(this).attr('date'));
			var tmpStr = $(this).attr('str');
				tmpStr = tmpStr.replace('#qlfNo', qlfNo);
				tmpStr = tmpStr.replace('#qlfKind', qlfKind);
				tmpStr = tmpStr.replace('#careNm', careNm);

				tmpStr = tmpStr.replace('#srcTgtPrsnCd', srcTgtPrsnCd[$(this).text()]);
				tmpStr = tmpStr.replace('#srcAmdtGradeCd', srcAmdtGradeCd[$(this).text()]);
				
				if (src986Cd[$(this).text()] == 'Y' || togatherExt[$(this).text()] == 'Y' || intAge >= 65){
					tmpStr = tmpStr.replace('#srcResultString', 'Y');
				}else{
					tmpStr = tmpStr.replace('#srcResultString', 'N');
				}

			if (memCD2){
				tmpStr = tmpStr.replace('#juminNo2', memCD2);
				tmpStr = tmpStr.replace('#careNm2', careNm2);
				tmpStr = tmpStr.replace('#qlfNo2', qlfNo2);
				tmpStr = tmpStr.replace('#qlfKind2', qlfKind2);
			}else{
				tmpStr = tmpStr.replace(' =#juminNo2', '');
				tmpStr = tmpStr.replace(' =#careNm2', '');
				tmpStr = tmpStr.replace(' =#qlfNo2', '');
				tmpStr = tmpStr.replace(' =#qlfKind2', '');
			}
			tmpStr += ' @';

			tmp += tmpStr;
		});
	});

	careValue = tmp.substring(0, tmp.length-1);
	initCareValue = tmp;

	if (g_Admin == '1'){
		//_iljungLongcareAjax(YYMM, payCtrNo, longTermMgmtNo, svcKind, careValue,longTermMgmtSeq,tgtDemoChasu,jumin);
		
		if (_longcareChkDuplicate(YYMM, payCtrNo, svcKind, longTermMgmtNo, longTermMgmtSeq, careValue, initCareValue)){
			_iljungSaveCare(YYMM, payCtrNo, longTermMgmtNo, svcKind, careValue, initCareValue, longTermMgmtSeq,tgtDemoChasu,jumin);
		}else{
			self.close();
		}
	}else{
		_iljungSaveCare(YYMM, payCtrNo, longTermMgmtNo, svcKind, careValue, initCareValue, longTermMgmtSeq,tgtDemoChasu,jumin);
	}
}


/*********************************************************

	건보공단에 저장

*********************************************************/
function _iljungSaveCare(YYMM, payCtrNo, longTermMgmtNo, svcKind, careValue, initCareValue, longTermMgmtSeq,tgtDemoChasu,jumin){
	//if ($('#code').attr('value') == '32817000202'){
	//	alert(careValue.split('@').join('\n'));
	//	return;
	//}

	//if ($('#giho').attr('value') == '34273000017'){
	//	document.write(YYMM+'<br>'+payCtrNo+'<br>'+longTermMgmtNo+'<br>'+svcKind+'<br>'+careValue.split('@').join('<br>')+'<br>'+longTermMgmtSeq+'<br>'+tgtDemoChasu);
	//	return;
	//}

	//if ($('#giho').attr('value') == '32917000014' && longTermMgmtNo == 'L0011328326'){
		//alert(careValue.split('@').join('\n'));
		//document.write(careValue.split('@').join('<br>'));
		//alert('테스트중입니다.');
		//return;
	//}

	if (!g_WinID) g_WinID = 'longcarePostWin';

	var h = screen.availHeight;
	var w = screen.availWidth;
	var t = 100;
	var l = 0;

	if(w >= 1350) w = 1350;
	h = h - t - 90;
	w = 0;
	h = 0;
	
	var option = 'left='+l+',top='+t+',width='+w+',height='+h+',scrollbars=yes,status=yes,resizable=yes';
    var url    = 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU';
	//if (g_chgSayu) url = 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU2';
	
	g_LongcareWin[g_WinID] = window.open('', g_WinID, option); //about:blank
	g_LongcareWin[g_WinID].opener = self;
	g_LongcareWin[g_WinID].focus();

	var entryMsg = 'msg_006';

	if (svcKind == '004') entryMsg = 'msg_011';

	var parm = new Array();
		parm = {
			'longTermAdminSym':$('#giho').attr('value')
		,	'longTermAdminNm':$('#strCenterName').text()
		,	'adminKindCd':'C'
		,	'serviceKind':svcKind
		,	'payMm':YYMM
		,	'payCtrNo':payCtrNo
		,	'longTermMgmtNo':longTermMgmtNo
		,	'longTermMgmtSeq':longTermMgmtSeq
		,	'careValue':careValue
			//,'initCareValue':initCareValue
		,	'tgtDemoChasu':tgtDemoChasu
		,	'tgtJuminNo':jumin
		,	'entryMsg':entryMsg
		
		,	'firstDn20Yn':firstDn20Yn//'N'
		,	'addDn20Yn':addDn20Yn//''

		,	'firstDn20':firstDn20//'0'
		,	'addDn20':addDn20//''
		
		,	'admtGradeCd':admtGradeCd//'3'

		,	'longTermMgmtNoFirstTotAmt':'0'
		,	'longTermMgmtNoFirstAddAmt':''
		,	'longTermMgmtNoFirstTotCnt':'0'
		,	'longTermMgmtNoFirstAddCnt':''
		,	'longTermMgmtNoFirstTotLimit':$('#infoClient').attr('limitAmt')//'878900'
		,	'longTermMgmtNoFirstAddLimit':''

		,	'longTermMgmtNoInitLimitAmt':$('#infoClient').attr('limitAmt')//'878900'

		,	'firstPctActFreq':'0'
		,	'addPctActFreqFreq':''
		
		,	'firstExcsDd15Freq':'0'
		,	'addExcsDd15Freq':''
		,	'firstExcsDd15TotFreq':'0'
		,	'addExcsDd15TotFreq':''
		,	'firstFmlyHldayFreq':'0'
		,	'addFmlyHldayFreq':''
		,	'firstFmlyHldayTotFreq':'0'
		,	'addFmlyHldayTotFreq':''
		,	'firstFmlyHldayAmt':'0'
		,	'addFmlyHldayAmt':''
		,	'firstHltMgmtSvcFreq':'0'
		,	'addHltMgmtSvcFreq':''
		,	'firstHltMgmtSvcAmt':'0'
		,	'addHltMgmtSvcAmt':''

		,	'fnc': 'saveJU' //'save'
		};
	
	/*
	if (g_chgSayu) parm['chgSayu'] = g_chgSayu;
	
	if (g_chgSayu){
		if (g_chgSayu == '04'){
			parm['tmpChgSayuEtc'] = g_chgSayuEtc;
		}else{
			parm['chgSayuEtc'] = '';
			parm['tmpChgSayuEtc'] = '';
		}	
	}
	*/

	var form = document.createElement('form');
    var objs;
    for(var key in parm){
        objs = document.createElement('input');
        objs.setAttribute('type', 'hidden');
        objs.setAttribute('name', key);
        objs.setAttribute('value', parm[key]);
        
		form.appendChild(objs);
    }

    form.setAttribute('target', g_WinID);
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
	//self.close();

	if ($('#code').attr('value') == '32729000072'){
	}else{
	}

	if (g_WinID == 'longcarePostWin'){
		_iljungCareResult(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin);
	}else{
		setTimeout('_iljungWinClose("'+g_WinID+'")',5000);
	}
}


function _iljungWinClose(id){
	try{
		g_LongcareWin[id].close();
	}catch(e){
	}
	
	/*
	try{
		var win = opener.uploadWin;

		for(var i in win){
			win[i].close();
		}
	}catch(e){
	}
	*/
}


/*********************************************************

	입력결과

*********************************************************/
function _iljungCareResult(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin){
	if (g_Admin == '1'){
	}else{
		g_TmpTimer = setInterval('_iljungCareResultOK(\''+YYMM+'\',\''+payCtrNo+'\',\''+longTermMgmtNo+'\',\''+svcKind+'\',\''+longTermMgmtSeq+'\',\''+tgtDemoChasu+'\',\''+jumin+'\')',1000);
	}
	
	setTimeout('_iljungSetHis("'+g_Client+'","'+svcKind+'","'+YYMM+'")',10);
}

function _iljungSetHis(jumin,svcKind,YYMM){
	$.ajax({
		type : 'POST'
	,	url  : './plan_lc_his.php'
	,	data : {
			'jumin':jumin
		,	'yymm' :YYMM
		,	'svc'  :svcKind
		}
	,	beforeSend: function(){
		}
	,	success: function (data){
			//alert(data);
		}
	,	error: function (){
		}
	});
}

function _iljungCareResultOK(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin){
	clearInterval(g_TmpTimer);
	g_TmpTimer = null;

	//if ($('#code').attr('value') == '34673000003'){
	//}else{
		g_LongcareWin[g_WinID].close();
	//}

	g_Int   = 0;
	g_Timer = setInterval('_iljungCareResultExe(\''+YYMM+'\',\''+payCtrNo+'\',\''+longTermMgmtNo+'\',\''+svcKind+'\',\''+longTermMgmtSeq+'\',\''+tgtDemoChasu+'\',\''+jumin+'\',\''+admtGradeCd+'\')',500);
}


function _longcareTest2(obj){
	g_Int ++;

	alert(1);

	if (g_Int > 0){
		clearInterval(g_Timer);
		g_Timer = null;
	}
}

//공단 업로드
function _iljungLongcareAjax(YYMM, payCtrNo, longTermMgmtNo, svcKind, careValue,longTermMgmtSeq,tgtDemoChasu,jumin){
	$.ajax({
		type : 'POST'
	,	url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU'
	,	data : {
			'longTermAdminSym'	: $('#giho').attr('value')
		,	'longTermAdminNm'	: $('#strCenterName').text()
		,	'adminKindCd'		: 'C'
		,	'serviceKind'		: svcKind
		,	'payMm'				: YYMM
		,	'payCtrNo'			: payCtrNo
		,	'longTermMgmtNo'	: longTermMgmtNo
		,	'longTermMgmtSeq'	: longTermMgmtSeq
		,	'careValue'			: careValue
		,	'tgtDemoChasu'		: tgtDemoChasu
		,	'tgtJuminNo'		: jumin
		,	'fnc'				: 'saveJU'
		}
	,	contentType: "application/x-www-form-urlencoded; charset=UTF-8"
	,	beforeSend: function(){
		}
	,	success: function (data){
			_iljungCareResultData(YYMM, payCtrNo, longTermMgmtNo, svcKind, data,longTermMgmtSeq,tgtDemoChasu);
			setTimeout('_iljungSetHis("'+g_Client+'","'+svcKind+'","'+YYMM+'")',10);
		}
	,	error: function (){
		}
	});
}

function _longcareHex2Dec(asStr){
	var strNm = '';

	for(var i=0; i<asStr.length; i++){
		strNm += escape(asStr.substring(i,i+1)).split('%').join(String.fromCharCode(1));
	}

	var rst = getHttpRequest('../fun/hex2dec.php?code='+strNm);

	return rst;
}

/*********************************************************

	요양보호사 일정 중복 검사

 *********************************************************/
function _longcareChkDuplicate(YYMM, payCtrNo, svcKind, longTermMgmtNo, longTermMgmtSeq, careValue, initCareValue){
	var url = "http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=selectServiceTmDupCnt";
	var params = {};
	var rst = false;
	
    params["longTermAdminSym"]	= $('#giho').attr('value');
    params["adminPttnCd"]		= '';
    params["adminKindCd"]		= 'C';
    params["serviceKind"]		= svcKind;
    params["payMm"]				= YYMM;
    params["size"]				= '31';
    params["payCtrNo"]			= payCtrNo;
    params["longTermMgmtNo"]	= longTermMgmtNo;
    params["longTermMgmtSeq"]	= longTermMgmtSeq;
    params["tgtJuminNo"]		= g_Client;
	params["careValue"]			= careValue;
	params["initCareValue"]		= initCareValue;

	$.ajax({
		type : 'POST'
	,	async: false
	,	url  : url
	,	data : params
	,	success: function (result){
			if (result == ''){
				rst = true;
			}else{
				alert(result);
			}
		}
	,	error: function (request, status, error){
			alert("CODE : " + request.status+"/"+status+"/"+error+"\nMESSAGE : " + request.responseText);
		}
	});

	return rst;
}

//테스트
function _iljungSaveCareTest(id){
	g_WinID = id;

	var h = 0;
    var w = 0;
    var t = 100;
	var l = 0;

	var url = 'http://www.carevisit.net/test/popup1.html';
	var option = 'left='+l+',top='+t+',width='+w+',height='+h+',scrollbars=yes,status=yes,resizable=yes';
	g_LongcareWin[g_WinID] = window.open('', g_WinID, option); //about:blank
	g_LongcareWin[g_WinID].opener = self;
	g_LongcareWin[g_WinID].focus();

	var form = document.createElement('form');
		form.setAttribute('target', g_WinID);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
	
	try{setTimeout('g_LongcareWin[\''+g_WinID+'\'].close()',5000);}catch(e){}
	try{setTimeout('self.close()',10000);}catch(e){}
}


function agechange(lno,rno,payDt) {
	var cy=Number(payDt.substr(0,4)); //계약년도
	var cm=Number(payDt.substr(4,2)); //계약월
	var cd=Number(payDt.substr(6,2)); //계약일
  
	var lastNo = lno.substring(0, 2);
	var gubunNo = rno.substring(0, 1);
  
	if (gubunNo=="0" || gubunNo=="9" ){
		firstNo = "18"+lastNo;
	}else if (gubunNo== "1" || gubunNo=="2" || gubunNo=="5" || gubunNo=="6" ){
		firstNo = "19"+lastNo;
	}else if (gubunNo=="3" || gubunNo=="4" || gubunNo=="7" || gubunNo=="8" ){
		firstNo = "20"+lastNo;
	}
  
	var by=Number(firstNo);
	var bm=Number(lno.substr(2,2)); //출생 월
	var bd=Number(lno.substr(4,2)); //출생 일
	
	if ( (cy - by) == 65 ){
		if (bm < cm){
			aged=cy-by;
		}else if (bm > cm) {
			aged=cy-by-1;
		}else if (bd <= cd){
			aged=cy-by;
		}else{
			aged=cy-by-1;
		}  
  	}else{
  		aged=cy-by-1;
  	}	
  	
	return aged; //만나이를 반환한다
 } 
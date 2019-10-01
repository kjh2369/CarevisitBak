<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	초기면접기록지
	 *********************************************************/

	//진입경로
	$entryPath = $myF->_self();

	if ($entryPath == 'hce_body'){
		//사례관리에서 진입
		$IsHCE = true;
	}else{
		//그외 진입
		$IsHCE = false;
	}

	$orgNo = $_SESSION['userCenterCode'];

	$ivDt = Date('Y-m-d');

	if ($IsHCE){
		//사례접수일자
		$sql = 'SELECT	rcpt_dt
				FROM	hce_receipt
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$rcptDt	= $conn->get_data($sql);
	}else{
		if ($type == 'INTERVIEW_REG'){
			$hce->rcpt = '0';
		}else{
			$hce->rcpt = '-1';
		}

		$hce->IPIN = $_POST['IPIN'];
		$hce->SR = $sr;
		$hce->let(true);

		//대상자명
		if ($hce->IPIN){
			if ($type == 'INTERVIEW_REG'){
				$sql = 'SELECT	m03_name
						FROM	m03sugupja
						WHERE	m03_ccode	= \''.$orgNo.'\'
						AND		m03_mkind	= \'6\'
						AND		m03_key		= \''.$hce->IPIN.'\'';
			}else{
				$sql = 'SELECT	name
						FROM	care_client_normal
						WHERE	org_no		= \''.$orgNo.'\'
						AND		normal_sr	= \''.$hce->SR.'\'
						AND		normal_seq	= \''.$hce->IPIN.'\'';
			}

			$TGer = $conn->get_data($sql);
		}
	}

	//이전 초기상담기록지 여부
	if ($type == 'INTERVIEW_REG'){
		$sql = 'SELECT	jumin
				,		name
				FROM	mst_jumin
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		cd_key	= \''.$hce->IPIN.'\'';

		$row = $conn->get_array($sql);

		$sql = 'SELECT	normal_seq
				FROM	care_client_normal
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$hce->SR.'\'
				AND		jumin		= \''.$row['jumin'].'\'
				AND		name		= \''.$row['name'].'\'';

		$tmpSeq = $conn->get_data($sql);

		Unset($row);

		$sql = 'SELECT	COUNT(*)
				FROM	hce_interview
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$tmpSeq.'\'
				AND		rcpt_seq= \'-1\'';

		$basicInterviewCnt = $conn->get_data($sql);
		
		$sql = 'SELECT  count(*)
				FROM    hce_receipt
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND     del_flag = \'N\'';
		$hceReceipt = $conn -> get_data($sql);

		$sql = 'SELECT	COUNT(*)
				FROM	hce_interview
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq != \'0\'';
		$hceInterviewCnt = $conn->get_data($sql);

	}else{
		$sql = 'SELECT	COUNT(*)
				FROM	hce_interview
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \'0\'';

		$basicInterviewCnt = $conn->get_data($sql);
	}
	
	//가족관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type  = \'HR\'
			AND		code  < \'99\'
			AND		use_yn= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	echo '<script type="text/javascript">';
	echo 'var familyReg = {};';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		echo 'familyReg["'.$row['code'].'"] = "'.$row['name'].'";';
	}

	echo '</script>';

	$conn->row_free();

	if (!$incomeGbn)	$incomeGbn	= '1';	//경제상황
	if (!$genGbn)		$genGbn		= '1';	//세대유형
	if (!$dwellingGbn)	$dwellingGbn= '1';	//주거형태
	if (!$houseGbn)		$houseGbn	= '1';	//주택구분
	if (!$healthGbn)	$healthGbn	= '1';	//거간상태
	if (!$handicap)		$handicap	= 'N';	//장애여부
	if (!$longLvlGbn)	$longLvlGbn	= '';	//장기요양등급
	if (!$svcOffer)		$svcOffer	= 'Y';	//적격,부적격
	if (!$svcRsnGbn)	$svcRsnGbn	= '1';	//서비스사유
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:radio').unbind('click').bind('click',function(){
			if ($(this).attr('id') == 'optSvcOfferN'){
				$('#txtNoOfferRsn').css('background-color','#ffffff').attr('disabled',false).focus();
			}else if ($(this).attr('id') == 'optSvcOfferY'){
				$('#txtNoOfferRsn').css('background-color','#efefef').attr('disabled',true);
			}else{
				if ($(this).val() == $(this).attr('otherVal')){
					$('#'+$(this).attr('otherObj')).css('background-color','#ffffff').attr('disabled',false).focus();
				}else{
					$('#'+$(this).attr('otherObj')).css('background-color','#efefef').attr('disabled',true);
				}
			}

			if ($(this).attr('name') == 'optDwellingGbn'){
				if ($(this).val() == '2'){
					$('#txtDepositAmt').css('background-color','#ffffff').attr('disabled',false).focus();
					$('#txtRentalAmt').css('background-color','#efefef').attr('disabled',true);
				}else if ($(this).val() == '3'){
					$('#txtDepositAmt').css('background-color','#ffffff').attr('disabled',false).focus();
					$('#txtRentalAmt').css('background-color','#ffffff').attr('disabled',false);
				}else{
					$('#txtDepositAmt').css('background-color','#efefef').attr('disabled',true);
					$('#txtRentalAmt').css('background-color','#efefef').attr('disabled',true);
				}
			}
		});

		$('input:checkbox').unbind('click').bind('click',function(){
			if ($(this).val() == $(this).attr('otherVal')){
				$('#'+$(this).attr('otherObj')).css('background-color','#ffffff').attr('disabled',false).focus();
			}else{
				$('#'+$(this).attr('otherObj')).css('background-color','#efefef').attr('disabled',true);
			}
		});

		if ('<?=$IsHCE;?>' == '1'){
			setTimeout('lfLoadBasic()',100);
		}else{
			setTimeout('lfTargetInfo()',100);
		}
		setTimeout('lfLoadFamily()',200);
		setTimeout('lfLoadInterview()',300);
	});

	function lfBasicInterviewLoad(seq){
		if (!confirm('작성된 초기상담기록지의 내용을 가져오시겠습니까?')) return;
		if (!seq) seq = '';

		setTimeout('lfLoadFamily(\'BASIC\',\''+seq+'\')',200);
		setTimeout('lfLoadInterview(\'BASIC\',\''+seq+'\')',300);
	}

	function lfMemFind(rtn){
		var jumin = $('#txtClient').attr('jumin');
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		if (!rtn) rtn = 'lfMemFindResult';

		var parm = new Array();
			parm = {
				'type':'member'
			,	'jumin':jumin
			,	'kind':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'return':rtn
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtIVer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	//대상자조회
	function lfTargetFind(){
		var jumin = $('#txtClient').attr('jumin');
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'sugupja'
			,	'jumin':jumin
			,	'svcCd':'<?=$sr;?>'
			,	'rtnType':'key'
			,	'wrkType':('<?=$type;?>' == 'INTERVIEW_REG' ? 'INTERVIEW_REG' : 'CARE_CLIENT_NORMAL')
			,	'return':'lfTargetFindResult'
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfTargetFindResult(obj){
		$('#txtTGer').attr('key',obj[0]).val(obj[1]);

		//성명
		$('#lblElderName').text(obj[1]);

		lfTargetInfo();
	}

	//대상자 정보조회
	function lfTargetInfo(){
		var svcCd = '6';

		if ('<?=$type;?>' == 'INTERVIEW_REG_N'){
			svcCd = '<?=$sr;?>';
		}

		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'TARGET_INFO'
			,	'svcCd':svcCd
			,	'key':$('#txtTGer').attr('key')
			,	'wrkType':('<?=$type;?>' == 'INTERVIEW_REG' ? 'INTERVIEW_REG' : 'CARE_CLIENT_NORMAL')
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseVal(data);

				if (col['age']) col['age'] += '세';

				$('#lblElderName').text(col['name']);		//성명
				$('#lblElderGender').text(col['gender']);	//	성별
				$('#lblElderAge').text(col['age']);			//	연령
				$('#lblElderSSN').text(col['jumin']);		//	주민번호
				$('#lblElderEdu').text(col['edu']);			//	학력
				$('#lblElderReg').text(col['rel']);			//	종교
				$('#lblElderAddr').text(col['addr']);		//	주소
				$('#lblElderPhone').text(col['telno']);		//	연락처
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//기본사항 조회
	function lfLoadBasic(){
		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'IV_BASIC'
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseStr(data);

				if (col['age']) col['age'] += '세';

				$('#lblElderName').text(col['name']);		//성명
				$('#lblElderGender').text(col['gender']);	//	성별
				$('#lblElderAge').text(col['age']);			//	연령
				$('#lblElderSSN').text(col['jumin']);		//	주민번호
				$('#lblElderEdu').text(col['edu']);			//	학력
				$('#lblElderReg').text(col['rel']);			//	종교
				$('#lblElderAddr').text(col['addr']);		//	주소
				$('#lblElderPhone').text(col['telno']);		//	연락처
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//가족사항 조회
	function lfLoadFamily(IsBasic, seq){
		var html = '';

		if (!seq) seq = '';

		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'FAMILY'
			,	'IsBasic':(IsBasic == 'BASIC' ? 'Y' : 'N')
			,	'seq':seq
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				
				var row = data.split(String.fromCharCode(11));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						lfAddFamily(col);
					}
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//면접내용
	function lfLoadInterview(IsBasic, seq){
		if (!seq) seq = '';

		$.ajax({
			type: 'POST'
		,	url : '../hce/hce_find.php'
		,	data: {
				'type':'INTERVIEW'
			,	'IsBasic':(IsBasic == 'BASIC' ? 'Y' : 'N')
			,	'seq':seq
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				if (!data) return;

				var col = __parseStr(data);

				$('#txtIVDt').val(__getDate(col['iverDt']));	//면접일
				$('#txtIVer').attr('jumin',col['iverSSN']).val(col['iverNm']);	//담당자
				$('#optIncomeGbn_'+col['icGbn']).attr('checked',true);	//경제상황
				$('#txtIncomeOther').val(col['icOther']);				//경제상황 기타

				$('#txtMonthly').val(col['icMonthly']);	//월소득
				$('#txtIncomeMain').val(col['icMain']);	//주소득원

				$('#optGenGbn_'+col['grGbn']).attr('checked',true);	//세대유형
				$('#txtGenOther').val(col['grOther']);				//세대유형 기타

				$('#optDwellingGbn_'+col['dlGbn']).attr('checked',true);//주거형태
				$('#txtDwellingOther').val(col['dlOther']);				//주거형태 기타

				$('#optHouseGbn_'+col['hsGbn']).attr('checked',true);	//주택구분
				$('#txtHouseOther').val(col['hsOther']);				//주택구분 기타

				$('#txtDepositAmt').val(col['dpAmt']);	//보증금
				$('#txtRentalAmt').val(col['rtAmt']);	//월세


				$('#optHealthGbn_'+col['hlGbn']).attr('checked',true);	//건강상태
				$('#txtHealthOther').val(col['hlOther']);				//건강상태 기타

				$('#optHandicap_'+col['hdGbn']).attr('checked',true);	//장애여부
				$('#txtHandicap').val(col['hdOther']);					//장애유형

				$('#txtDeviceOther').val(col['dcOther']);	//부장구 기타

				$('#optLongLvlGbn_'+col['llGbn']).attr('checked',true);	//장기요양등급
				$('#txtLongLvlOther').val(col['llOther']);				//등급 외

				$('#txtOtherSvcNm').val(col['orSvcNm']);//타 서비스명
				$('#txtOtherOrgNm').val(col['orOrgNm']);//타 서비스 기관명

				$('#optSvcOffer'+col['offGbn']).attr('checked',true);	//서비스 적격 여부
				$('#txtNoOfferRsn').val(col['noOffRsn']);				//부적격 사유

				$('#optSvcRsnGbn_'+col['svcRsnGbn']).attr('checked',true);	//서비스사유
				$('#txtSvcRsnOther').val(col['svcRsnOther']);				//서비스사유 기타

				$('#txtReqNm').val(col['reqNm']);		//의뢰인명
				$('#cboReqRel').val(col['reqRel']);		//대상자와의 관계
				$('#txtReqTel').val(col['reqTel']);		//연락처
				$('#cboReqRoute').val(col['reqGbn']);	//의뢰경로

				$('#txtRemark').val(col['remark']);//비고

				//만성질횐
				if (col['disGbn']){
					var gbn = col['disGbn'];
						gbn = __replace(gbn,':','=');
						gbn = __replace(gbn,'/','&');
						gbn = __parseVal(gbn);

					for(var i in gbn){
						$('#chkDisease_'+i).attr('checked',(gbn[i] == 'Y' ? true : false));
					}
				}

				//보장구
				if (col['dcGbn']){
					var gbn = col['dcGbn'];
						gbn = __replace(gbn,':','=');
						gbn = __replace(gbn,'/','&');
						gbn = __parseVal(gbn);

					for(var i in gbn){
						$('#chkDevice_'+i).attr('checked',(gbn[i] == 'Y' ? true : false));
					}
				}

				//신청서비스
				if (col['reqSvc']){
					var gbn = col['reqSvc'];
						gbn = __replace(gbn,':','=');
						gbn = __replace(gbn,'/','&');
						gbn = __parseVal(gbn);

					for(var i in gbn){
						$('#chkSvcReq_'+i).attr('checked',(gbn[i] == 'Y' ? true : false));
					}
				}

				//제공서비스
				if (col['offSvcGbn']){
					var gbn = col['offSvcGbn'];
						gbn = __replace(gbn,':','=');
						gbn = __replace(gbn,'/','&');
						gbn = __parseVal(gbn);

					for(var i in gbn){
						$('#chkSvcOff_'+i).attr('checked',(gbn[i] == 'Y' ? true : false));
					}
				}

				$('input:radio:checked').each(function(){
					$(this).click();
				});
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//가족추가
	function lfAddFamily(col){
		var html = '';
		var cnt = $('#tbodyFamily tr').length+1;

		if (col){
			if (!col['rel'])	col['rel']		= '';
			if (!col['name'])	col['name']		= '';
			if (!col['addr'])	col['addr']		= '';
			if (!col['age'])	col['age']		= '';
			if (!col['job'])	col['job']		= '';
			if (!col['cohabit'])col['cohabit']	= '';
			if (!col['monthly'])col['monthly']	= '';
			if (!col['remark'])	col['remark']	= '';
		}else{
			var col = {};

			col['rel']		= '';
			col['name']		= '';
			col['addr']		= '';
			col['age']		= '';
			col['job']		= '';
			col['cohabit']	= '';
			col['monthly']	= '';
			col['remark']	= '';
		}

		html += '<tr>';
		html += '<td>';
		html += '<select id="cboFamilyGbn" name="cbo" style="width:auto;" onchange="lfChkFamily(this);">';
		html += '<option value="">-</option>';

		for(var i in familyReg){
			html += '<option value="'+i+'" '+(col['rel'] == i ? 'selected' : '')+'>'+familyReg[i]+'</option>';
		}

		html += '</select>';
		html += '</td>';
		html += '<td><input id="txtFamilyName" name="txt" type="text" value="'+col['name']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyAddr" name="txt" type="text" value="'+col['addr']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyAge" name="txt" type="text" value="'+col['age']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyJob" name="txt" type="text" value="'+col['job']+'" style="width:100%;"></td>';
		html += '<td>';
		html += '<select id="cboFamilyCohabit" name="cbo" style="width:auto;">';
		html += '<option value="Y" '+(col['cohabit'] == 'Y' ? 'selected' : '')+'>예</option>';
		html += '<option value="N" '+(col['cohabit'] != 'Y' ? 'selected' : '')+'>아니오</option>';
		html += '</select>';
		html += '</td>';
		html += '<td><input id="txtFamilyMonthly" name="txt" type="text" value="'+col['monthly']+'" style="width:100%;"></td>';
		html += '<td><input id="txtFamilyRemark" name="txt" type="text" value="'+col['remark']+'" style="width:100%;"></td>';
		html += '<td class="last">';
		html += '<div class="left"><span class="btn_pack m"><span class="delete"></span><button type="button" onclick="lfDelFamily($(this).parent().parent().parent().parent());">삭제</button></span></div>';
		html += '</td>';
		html += '</tr>';

		$('.removeRow').remove();

		if ($('#tbodyFamily tr:last').length > 0){
			$('#tbodyFamily tr:last').after(html);
		}else{
			$('#tbodyFamily').html(html);
		}

		__init_form(document.f);
	}

	//가족삭제
	function lfDelFamily(obj){
		$(obj).remove();
	}

	//가족 추가가능여부
	function lfChkFamily(obj){
		var gbn = $(obj).val();
		var rst = true;

		if (gbn != '01' && gbn != '02') return true;

		$('select[id^="cboFamilyGbn_"]').each(function(){
			if ($(this).attr('id') != $(obj).attr('id')){
				if ($(this).val() == gbn){
					rst = false;
					return false;
				}
			}
		});

		var msg = '';

		if (!rst){
			if (gbn == '01') msg = '"부"는';
			if (gbn == '02') msg = '"모"는';

			msg += ' 한명만 등록할 수 있습니다.';

			alert(msg);

			$(obj).val('');
		}

		return rst;
	}

	//저장
	function lfSave(){
		if (!$('#txtIVDt').val()){
			alert('면접일자를 입력하여 주십시오.');
			$('#txtIVDt').focus();
			return;
		}

		if ('<?=$IsHCE;?>' != '1'){
			if (!$('#txtTGer').attr('key')){
				alert('대상자를 선택하여 주십시오.');
				lfTargetFind();
				return;
			}
		}

		if (!$('#txtIVer').attr('jumin')){
			alert('담당자를 선택하여 주십시오.');
			lfMemFind();
			return;
		}

		var data = {};

		if ('<?=$IsHCE;?>' == '1'){
			data['IsHCE'] = 'Y';
		}else{
			data['IsHCE'] = 'N';
			data['IPIN'] = $('#txtTGer').attr('key');
			data['type'] = '21';
			data['sr'] = '<?=$sr;?>';
			data['wrkType'] = '<?=$type;?>';
		}

		data['txtIVerJumin']= $('#txtIVer').attr('jumin');

		var family = '';
		var rstFlag = true;

		$('#tbodyFamily tr').each(function(){
			if ($('#cboFamilyGbn',this).val()){
				/*
				if (!$('#txtFamilyName',this).val()){
					 alert('가족의 성명을 입력하여 주십시오.');
					 $('#txtFamilyName',this).focus();
					 rstFlag = false;
					 return false;
				}
				*/

				family += 'rel='+$('#cboFamilyGbn',this).val();
				family += '&name='+$('#txtFamilyName',this).val();
				family += '&addr='+$('#txtFamilyAddr',this).val();
				family += '&age='+$('#txtFamilyAge',this).val();
				family += '&job='+$('#txtFamilyJob',this).val();
				family += '&cohabit='+$('#cboFamilyCohabit',this).val();
				family += '&monthly='+$('#txtFamilyMonthly',this).val();
				family += '&remark='+$('#txtFamilyRemark',this).val();
				family += String.fromCharCode(11);
			}
		});

		if (!rstFlag) return;

		data['family'] = family;

		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (id.substring(0,13) == 'txtFamilyName' ||
				id.substring(0,13) == 'txtFamilyAddr' ||
				id.substring(0,12) == 'txtFamilyAge' ||
				id.substring(0,12) == 'txtFamilyJob' ||
				id.substring(0,16) == 'txtFamilyMonthly' ||
				id.substring(0,15) == 'txtFamilyRemark'){
			}else{
				data[id] = val;
			}
		});

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();

			data[name] = val;
		});

		$('input:checkbox').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).attr('checked') ? 'Y' : 'N';

			data[id] = val;
		});

		$('select').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (id.substring(0,12) == 'cboFamilyGbn' ||
				id.substring(0,15) == 'cboFamilyCohabit'){
			}else{
				data[id] = val;
			}
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'../hce/hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					if ('<?=$IsHCE;?>' == '1'){
						top.frames['frmTop'].lfTarget();
					}
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfList(){
		location.href = "../care/care.php?sr=S&type=INTERVIEW_LIST";
	}

</script><?

if ($IsHCE){?>
	<div class="title title_border">
		<div style="float:left; width:auto;">초기면접기록지출력(빈양식)</div>
		<div style="float:right; width:auto; padding-top:10px;">
			<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave(); return false;">저장</button></span>
			<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>');">출력</button></span>
		</div>
	</div><?
}else{?>
	<div class="title title_border">
		<div style="float:left; width:auto;">초기면접기록</div>
		<div style="float:right; width:auto; padding-top:10px;"><?
			if($hceReceipt > 0){ 
				if($hceInterviewCnt == 0){?>
					<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave(); return false;">저장</button></span><?
				}
			}else { ?>
				<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave(); return false;">저장</button></span><?
			} ?>
			<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('21<?=$type == 'INTERVIEW_REG_N' ? '_N' : '';?>');">출력</button></span>
			<span class="btn_pack m"><span class="list"></span><button type="button" class="bold" onclick="lfList(); return false;">리스트</button></span>
		</div>
	</div><?
}

if ($IsHCE){?>
	<!-- <div class="my_border_blue" style="border-bottom:none;"> -->
	<?
}?>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="100px"><?
		if (!$IsHCE){?>
			<col width="50px">
			<col width="70px">
			<col width="20px"><?
		}?>
		<col width="60px">
		<col width="70px">
		<col width="20px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head"">면접일</th>
			<td class="left">
				<input id="txtIVDt" name="txt" type="text" value="<?=$ivDt;?>" class="date">
			</td><?

			if (!$IsHCE){?>
				<th class="center">대상자</th>
				<td class="center last">
					<input id="txtTGer" name="txt" type="text" value="<?=$TGer;?>" key="<?=$hce->IPIN;?>" style="width:100%; border:none;" alt="not" readonly>
				</td>
				<td class="center">
					<span class="btn_pack find" onclick="lfTargetFind();"></span>
				</td><?
			}?>

			<th class="head">담당자</th>
			<td class="left">
				<input id="txtIVer" name="txt" type="text" value="" jumin="" style="width:100%; border:none;" alt="not" readonly>
			</td>
			<td class="left">
				<span class="btn_pack find" onclick="lfMemFind();"></span>
			</td>
			<td class="left last"><?
				if ($IsHCE){
					if ($basicInterviewCnt > 0){?>
						<div class="right">
							<span class="btn_pack m"><button onclick="lfBasicInterviewLoad();">초기상담기록지 가져오기</button></span>
						</div><?
					}
				}else{
					if ($type == 'INTERVIEW_REG'){
						if ($basicInterviewCnt > 0){?>
							<div class="right">
								<span class="btn_pack m"><button onclick="lfBasicInterviewLoad('<?=$tmpSeq;?>');">초기상담기록지(일반접수) 가져오기</button></span>
							</div><?
						}
					}
				}?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col width="50px">
		<col width="60px">
		<col width="95px">
		<col width="40px">
		<col width="110px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold last" colspan="12">1. 기본사항</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center"><div id="lblElderName" class="left"></div></td>
			<th class="center">성별</th>
			<td class="center"><div id="lblElderGender"></div></td>
			<th class="center">연령</th>
			<td class="center"><div id="lblElderAge"></div></td>
			<th class="center">주민번호</th>
			<td class="center"><div id="lblElderSSN"></div></td>
			<th class="center">학력</th>
			<td class="center"><div id="lblElderEdu" class="left"></div></td>
			<th class="center">종교</th>
			<td class="center last"><div id="lblElderReg" class="left"></div></td>
		</tr>
		<tr>
			<th class="center">주소</th>
			<td class="center" colspan="9"><div id="lblElderAddr" class="left"></div></td>
			<th class="center">연락처</th>
			<td class="center last"><div id="lblElderPhone" class="left"></div></td>
		</tr>
	</tbody>
</table><?

if ($IsHCE){?>
	</div>
	<!-- <div id="divBody" class="my_border_blue" style="border-top:none; height:200px; overflow-x:hidden; overflow-y:scroll;"> -->
	<?
}?>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="71px">
		<col width="80px">
		<col width="150px">
		<col width="50px">
		<col width="100px">
		<col width="72px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="9">2. 가족사항</th>
		</tr>
		<tr>
			<th class="head">관계</th>
			<th class="head">성명</th>
			<th class="head">주소</th>
			<th class="head">연령</th>
			<th class="head">직업</th>
			<th class="head">동거여부</th>
			<th class="head">월소득액</th>
			<th class="head">비고</th>
			<th class="last">
				<span class="btn_pack m"><span class="add"></span><button type="button" onclick="lfAddFamily();">추가</button></span>
			</th>
		</tr>
	</tbody>
	<tbody id="tbodyFamily">
		<tr class="removeRow">
			<td class="right last" colspan="9">※추가버튼을 클릭하여 가족구성원을 추가하여 주십시오.</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">3. 생활상태</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" rowspan="2">경제상황</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'IG\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optIncomeGbn_<?=$row['code'];?>" name="optIncomeGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtIncomeOther" <?=($incomeGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtIncomeOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table my_border_blue" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom" colspan="4">월소득</th>
							<td class="bottom">
								<input id="txtMonthly" name="txt" type="text" value="0" style="width:70px;" class="number">
							</td>
							<th class="center bottom">주소득원</th>
							<td class="bottom last">
								<input id="txtIncomeMain" name="txt" type="text" value="" style="width:100%;">
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center">세대유형</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'GR\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optGenGbn_<?=$row['code'];?>" name="optGenGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtGenOther" <?=($genGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtGenOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center" rowspan="3">주거형태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DL\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optDwellingGbn_<?=$row['code'];?>" name="optDwellingGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtDwellingOther" <?=($dwellingGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtDwellingOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'HT\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optHouseGbn_<?=$row['code'];?>" name="optHouseGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtHouseOther" <?=($houseGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtHouseOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table my_border_blue" style="width:auto;">
					<colgroup>
						<col width="50px">
						<col width="120px">
						<col width="40px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom" colspan="4">보증금</th>
							<td class="bottom">
								<input id="txtDepositAmt" name="txt" type="text" value="0" style="width:70px; background-color:#efefef;" class="number" disabled="true">만원
							</td>
							<th class="center bottom">월세</th>
							<td class="bottom last">
								<input id="txtRentalAmt" name="txt" type="text" value="0" style="width:70px; background-color:#efefef;" class="number" disabled="true">만원
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">4. 신체상태</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">건강상태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'HS\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div style="float:left; width:47%;">
						<label><input id="optHealthGbn_<?=$row['code'];?>" name="optHealthGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtHealthOther" <?=($healthGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

						if ($row['code'] == '9'){?>
							(<input id="txtHealthOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
						}?>
					</div><?
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center">만성질환</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DT\'
						AND		use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div style="float:left; width:23%;">
						<label><input id="chkDisease_<?=$row['code'];?>" name="chkDisease" type="checkbox" class="checkbox" value="<?=$row['code'];?>"><?=$row['name'];?></label>
					</div><?
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center" rowspan="2">장애여부</th>
			<td class="last">
				<label><input id="optHandicap_Y" name="optHandicap" type="radio" class="radio" value="Y" otherVal="Y" otherObj="txtHandicap" <?=($handicap == 'Y' ? 'checked' : '');?>>유</label>
				<label><input id="optHandicap_N" name="optHandicap" type="radio" class="radio" value="N" otherVal="Y" otherObj="txtHandicap" <?=($handicap != 'Y' ? 'checked' : '');?>>무</label>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table my_border_blue" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom">장애유형</th>
							<td class="bottom last">
								<input id="txtHandicap" name="txt" type="text" value="" style="width:100%; background-color:#efefef;" disabled="true">
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center">보장구</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'DV\'
						AND	use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="chkDevice_<?=$row['code'];?>" name="chkDevice" type="checkbox" class="checkbox" value="<?=$row['code'];?>" otherVal="99" otherObj="txtDeviceOther"><?=$row['name'];?></label><?

					if ($row['code'] == '99'){?>
						(<input id="txtDeviceOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center">장기요양등급</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'LLV\'
						AND	use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optLongLvlGbn_<?=$row['code'];?>" name="optLongLvlGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtLongLvlOther" <?=($longLvlGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtLongLvlOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">5. 타 서비스 이용 현황(생태도 생성시 사용됩니다. ","로 구분하여 서비스명을 입력하여 주십시오.)</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">서비스명</th>
			<td class="last">
				<input id="txtOtherSvcNm" name="txt" type="text" value="" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">이용기관</th>
			<td class="last">
				<input id="txtOtherOrgNm" name="txt" type="text" value="" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="1">6. 신청서비스(영역별 구분까지)</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center bottom last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody><?
						$sql = 'SELECT	DISTINCT
										care.suga_cd AS cd
								,		suga.nm1 AS mst_nm
								,		suga.nm2 AS pro_nm
								,		suga.nm3 AS svc_nm
								FROM	care_suga AS care
								INNER	JOIN	suga_care AS suga
										ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
										AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
										AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	care.org_no	= \''.$orgNo.'\'
								AND		care.suga_sr= \''.$sr.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();
						$idx = 1;

						$tmpStr1 = '';
						$tmpStr2 = '';

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);

							if ($tmpStr1 != SubStr($row['cd'],0,1)){
								$tmpStr1  = SubStr($row['cd'],0,1);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:20px;"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></th>
								</tr><?
							}

							if ($tmpStr2 != SubStr($row['cd'],0,3)){
								$tmpStr2  = SubStr($row['cd'],0,3);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:35px;"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></th>
								</tr><?
							}

							if ($idx % 3 == 1){?>
								<tr><td class="last" style="padding-left:50px;"><?
							}?>
							<div style="float:left; width:30%;"><label><input id="chkSvcReq_<?=$row['cd'];?>" name="chkSvcReq" type="checkbox" class="checkbox" value="<?=$row['cd'];?>"><?=$row['svc_nm'];?></label></div><?

							if ($idx == 3){
								$idx = 1;?>
								</td></tr><?
							}else{
								$idx ++;
							}
						}

						$conn->row_free();?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">7. 서비스제공여부</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center" >서비스제공<br>여부</th>
			<td class="last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<td class="">
								<label><input id="optSvcOfferY" name="optSvcOffer" type="radio" class="radio" value="Y" <?=($svcOffer == 'Y' ? 'checked' : '');?>>적격</label>
							</td>
							<th class="bottom center" rowspan="2">부적격<br>사유</th>
							<td class="bottom last" rowspan="2">
								<textarea id="txtNoOfferRsn" style="width:100%; height:45px; background-color:#efefef;" disabled="true"></textarea>
							</td>
						</tr>
						<tr>
							<td class="bottom">
								<label><input id="optSvcOfferN" name="optSvcOffer" type="radio" class="radio" value="N" <?=($svcOffer != 'Y' ? 'checked' : '');?>>부적격</label>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center">서비스 사유</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'SRG\'
						AND	use_yn	= \'Y\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optSvcRsnGbn_<?=$row['code'];?>" name="optSvcRsnGbn" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtSvcRsnOther" <?=($svcRsnGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?

					if ($row['code'] == '9'){?>
						(<input id="txtSvcRsnOther" name="txt" type="text" value="" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}

				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="center">제공<br>서비스내용<br>(영역별<br>구분기재)</th>
			<td class="center bottom last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody><?
						$sql = 'SELECT	DISTINCT
										care.suga_cd AS cd
								,		suga.nm1 AS mst_nm
								,		suga.nm2 AS pro_nm
								,		suga.nm3 AS svc_nm
								FROM	care_suga AS care
								INNER	JOIN	suga_care AS suga
										ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
										AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
										AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	care.org_no	= \''.$orgNo.'\'
								AND		care.suga_sr= \''.$sr.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();
						$idx = 1;

						$tmpStr1 = '';
						$tmpStr2 = '';

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);

							if ($tmpStr1 != SubStr($row['cd'],0,1)){
								$tmpStr1  = SubStr($row['cd'],0,1);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:20px;"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></th>
								</tr><?
							}

							if ($tmpStr2 != SubStr($row['cd'],0,3)){
								$tmpStr2  = SubStr($row['cd'],0,3);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:35px;"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></th>
								</tr><?
							}

							if ($idx % 3 == 1){?>
								<tr><td class="last" style="padding-left:50px;"><?
							}?>
							<div style="float:left; width:30%;"><label><input id="chkSvcOff_<?=$row['cd'];?>" name="chkSvcOff" type="checkbox" class="checkbox" value="<?=$row['cd'];?>"><?=$row['svc_nm'];?></label></div><?

							if ($idx == 3){
								$idx = 1;?>
								</td></tr><?
							}else{
								$idx ++;
							}
						}

						$conn->row_free();?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="70px">
		<col width="30px">
		<col width="110px">
		<col width="60px">
		<col width="90px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="8">8. 의뢰인</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="center">
				<input id="txtReqNm" name="txt" type="text" value="" style="width:100%;">
			</td>
			<th class="center">관계</th>
			<td class="center">
				<select id="cboReqRel" name="cbo" style="width:auto;">
					<option value="">-</option><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type= \'HR\'
							AND	use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<th class="center">연락처</th>
			<td class="center">
				<input id="txtReqTel" name="txt" type="text" value="" class="phone">
			</td>
			<th class="center">의뢰경로</th>
			<td class="last">
				<select id="cboReqRoute" name="cbo" style="width:auto;">
					<option value="">-</option><?
					$sql = 'SELECT	code,name
							FROM	hce_gbn
							WHERE	type= \'CR\'
							AND	use_yn	= \'Y\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="1">9. 비고</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center bottom last">
				<textarea name="txtRemark" id="txtRemark" style="width:100%; height:100px;"></textarea>
			</td>
		</tr>
	</tbody>
</table><?

if ($IsHCE){?>
	</div><?
}?>
<input id="rcptDt" type="hidden" value="<?=$rcptDt;?>">
<?
	include_once('../inc/_db_close.php');
?>
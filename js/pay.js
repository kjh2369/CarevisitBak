// 급여항목 설명
function _payrollHelp(){
	var width  = 900;
	var height = 600;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;

	window.open('help.html', 'PAYROLL_HELP', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
}

// 수당리스트
function sudangSearch(p_code, p_kind, p_year, p_month, p_yoyangsa){
	_member_list(myYoyangsa,p_code, p_kind, p_year, p_month, 'S');
	getSudangList(myBody,p_code, p_kind, p_year, p_month, p_yoyangsa, '', '');
}

// 요양사리스트
function _member_list(p_body, p_code, p_kind, p_year, p_month, p_gubun, p_index, p_center){
	//var URL = 'yoyangsa_list.php';
	var URL = 'pay_member_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:p_code,
				kind:p_kind,
				year:p_year,
				month:p_month,
				gubun:p_gubun
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;

				_pay_conf(p_index, p_center);
			}
		}
	);
}

// 요양사별 일별 수당리스트
function getSudangList(p_body, p_code, p_kind, p_year, p_month, p_yoyangsa, p_conf, p_index, p_detail){
	p_detail = (p_detail == 'Y' ? 'Y' : 'N');

	var URL = 'sudang_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mYoyangsa:p_yoyangsa,
				mDetail:p_detail
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;

				if (p_detail != undefined && p_detail == 'Y'){
					return;
				}
				
				var count = document.getElementById('yoyCount').value;

				if (p_yoyangsa != '' && p_index == ''){
					var yoyCode = document.getElementsByName('yoyCodes[]');

					for(var i=0; i<yoyCode.length; i++){
						if (yoyCode[i].value == p_yoyangsa){
							p_index = i;
							p_conf = document.getElementsByName('yoyConfCount[]')[i].value;
							break;
						}
					}
				}
				
				for(var i=0; i<count; i++){
					//document.getElementById('yoy_'+i).style.color = '#000000';
					document.getElementById('yoy_'+i).style.textDecoration = '';
					//document.getElementById('yoy_'+i).style.fontWeight = 'normal';
				}
				//document.getElementById('yoy_'+p_index).style.color = '#0000ff';
				document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
				//document.getElementById('yoy_'+p_index).style.fontWeight = 'bold';

				document.getElementById('btnConf').disabled = false;

				switch(p_conf){
				case '1':
					document.getElementById('btnConf').disabled = true;
					document.getElementById('btnCancel').disabled = false;
					break;
				case '2':
					document.getElementById('btnConf').disabled = false;
					document.getElementById('btnCancel').disabled = false;
					break;
				case '3':
					document.getElementById('btnConf').disabled = false;
					document.getElementById('btnCancel').disabled = true;
					break;
				default:
					//document.getElementById('btnConf').disabled = true;
					document.getElementById('btnCancel').disabled = true;
				}
			}
		}
	);
}

// 소수점 체크
// 인자 target : 대상
//      pos : 허용할 자리수
function checkFloor(target, pos){
	var e = window.event;

	if(e.keyCode == 9 || e.keyCode == 32 || e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40) return;

	var value = (typeof(target) == 'object' ? target.value : target);
	var pointPos = value.indexOf('.');

	if (pointPos < 0) return (typeof(target) == 'object' ? null : target);

	if (e.keyCode == 110 || e.keyCode == 190){
		var length = value.length;

		if (length - pointPos > pos){
			value = value.substring(0, length - 1);
		}else{
			return (typeof(target) == 'object' ? null : target);
		}

		if (typeof(target) == 'object'){
			target.value = value;
		}else{
			return value;
		}
	}
}

// 선택한 객체 색상변경
function setSudangObjectColor(p_index, p_type){
	var target = document.getElementsByName('sudangIndex[]');
	var payTime = document.getElementsByName('payTime[]');
	var payRate = document.getElementsByName('payRate[]');
	var payPer = document.getElementsByName('payPer[]');
	var payPerYul = document.getElementsByName('payPerYul[]');
	var payType = document.getElementsByName('payType[]');
	var sugaValue = document.getElementsByName('sugaValue[]');
	var rate = document.getElementsByName('rate[]');
	var sumSudang = document.getElementsByName('sumSudang[]');
	var sudangValue = document.getElementsByName('sudangValue[]');
	var perSudangYN = document.getElementsByName('perSudangYN[]');

	for(var i=0; i<target.length; i++){
		if (target[i].value == p_index){
			payTime[i].style.borderColor = (p_type=='1'?'#008afd':'#cccccc');
			payRate[i].style.borderColor = (p_type=='2'?'#008afd':'#cccccc');
			payPer[i].style.borderColor = (p_type=='3'?'#008afd':'#cccccc');
			payPerYul[i].style.borderColor = (p_type=='3'?'#008afd':'#cccccc');

			payTime[i].style.backgroundColor = (p_type=='1'?'#ffffff':'#eeeeee');
			payRate[i].style.backgroundColor = (p_type=='2'?'#ffffff':'#eeeeee');
			payPer[i].style.backgroundColor = (p_type=='3'?'#ffffff':'#eeeeee');
			payPerYul[i].style.backgroundColor = (p_type=='3'?'#ffffff':'#eeeeee');

			if (payTime[i].value == '') payTime[i].value = 0;
			if (payRate[i].value == '') payRate[i].value = 0;
			if (payPer[i].value == '') payPer[i].value = 0;
			if (payPerYul[i].value == '') payPerYul[i].value = 0;

			payType[i].value = p_type;

			break;
		}
	}

	var totalSudang = 0;
	var sudang = 0;
	for(var i=0; i<target.length; i++){

		switch(payType[i].value){
		case '1':
			sudang = parseInt(__commaUnset(payTime[i].value)) * rate[i].value;
			break;
		case '2':
			sudang = (sugaValue[i].value * (payRate[i].value / 100));
			break;
		case '3':
			if (perSudangYN[i].value == 'Y'){
				sudang = (parseInt(__commaUnset(payPer[i].value)) * (payPerYul[i].value / 100));
			}else{
				sudang = parseInt(__commaUnset(payPer[i].value));
			}
			break;
		}
		totalSudang += parseInt(sudang);

		if (target[i].value == p_index){
			sudangValue[i].value = Math.round(sudang);
			sumSudang[i].innerHTML = '수당계 '+__commaSet(Math.round(sudang));
		}
	}
	
	document.getElementById('totalSudang').innerHTML = '수당합계 '+__commaSet(totalSudang);
}

// 수당확정
function sudangConf(){
	var rowCount = document.getElementById('rowCount').value;
	var year = document.getElementById('year').value;
	var month = document.getElementById('month').value;
	var yoyangsaName = document.getElementById('yoyangsaName').value;

	if (rowCount > 0){
		if (!confirm('요양보호사('+yoyangsaName+')의 '+year+'년 '+month+'월의 수당을 확정처리하시겠습니까?')){
			return;
		}
	}else{
		alert('확정할 데이타가 없습니다. 확인하여 주십시오.');
		return;
	}

	document.f.action = 'conf_ok.php';
	document.f.submit();
}

// 수당확정취소
function sudangConfCancel(){
	var rowCount = document.getElementById('rowCount').value;
	var year = document.getElementById('year').value;
	var month = document.getElementById('month').value;
	var yoyangsaName = document.getElementById('yoyangsaName').value;

	if (rowCount > 0){
		if (!confirm('요양보호사('+yoyangsaName+')의 '+year+'년 '+month+'월의 황정된 수당을 취소하시겠습니까?')){
			return;
		}
	}else{
		alert('확정취소할 데이타가 없습니다. 확인하여 주십시오.');
		return;
	}

	document.f.action = 'conf_cancel_ok.php';
	document.f.submit();
}

// 요양보호사별 수당리스트
function yoySudangList(p_body, p_code, p_kind, p_year, p_month){
	var URL = 'yoy_list.php';
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
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 수당항목저장
function subjectSave(){
	if (!confirm("수당항목을 변경하시겠습니까?")){
		return;
	}

	document.f.action = 'subject_ok.php';
	document.f.submit();
}

// 급여확정조회
function payrollSearch(p_code, p_kind, p_year, p_month, p_yoyangsa){
	_member_list(myYoyangsa, p_code, p_kind, p_year, p_month, 'P');
	getPayrollList(myBody, p_code, p_kind, p_year, p_month, p_yoyangsa, '', '');
}

function _pay_search(p_code, p_kind, p_year, p_month, p_yoyangsa, p_index, p_center){
	_member_list(document.getElementById('myYoyangsa'), p_code, p_kind, p_year, p_month, 'PP', p_index, p_center);
	if (p_index == '') getPayrollListTest(myBody, p_code, p_kind, p_year, p_month, p_yoyangsa, '', '');
}

// 급여리스트
function getPayrollList(p_body, p_code, p_kind, p_year, p_month, p_yoyangsa, p_index, p_type){
	var URL = 'payroll_1.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mYoyangsa:p_yoyangsa,
				mType:p_type
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;

				var count = document.getElementById('yoyCount').value;

				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
				}
				
				if (document.getElementById('rowData').value == 'Y'){
					var yoyCode = document.getElementsByName('yoyCode[]');
					for(var i=0; i<count; i++){
						if (yoyCode[i].value == p_yoyangsa){
							document.getElementById('yoy_'+i).style.textDecoration = 'underline';
							document.getElementById('yoy_'+i).style.color = '#000';
							break;
						}
					} 
				}else{
					document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
					calPay();
				}

				id_employRate.innerHTML  = payroll.MrateEmploy;  //고용보험
				id_healthRate.innerHTML  = payroll.MrateHealth;  //건강보험
				id_oldcareRate.innerHTML = payroll.MrateOldcare; //노인장기요양
				id_annuityRate.innerHTML = payroll.MrateAnnuity; //국민연금
			}
		}
	);
}

// 급여리스트
function getPayrollListTest(p_body, p_code, p_kind, p_year, p_month, p_yoyangsa, p_index, p_type){
	var URL = 'payroll_4.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mYoyangsa:p_yoyangsa,
				mType:p_type
			},
			onSuccess:function (responseHttpObj) {
				p_body.innerHTML = responseHttpObj.responseText;

				var count = document.getElementById('yoyCount').value;

				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
				}
				
				if (document.getElementById('rowData').value == 'Y'){
					var yoyCode = document.getElementsByName('yoyCode[]');
					for(var i=0; i<count; i++){
						if (yoyCode[i].value == p_yoyangsa){
							document.getElementById('yoy_'+i).style.textDecoration = 'underline';
							document.getElementById('yoy_'+i).style.color = '#000';
							break;
						}
					} 
				}else{
					document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
					calPay();
				}

				id_employRate.innerHTML  = payroll.MrateEmploy;  //고용보험
				id_healthRate.innerHTML  = payroll.MrateHealth;  //건강보험
				id_oldcareRate.innerHTML = payroll.MrateOldcare; //노인장기요양
				id_annuityRate.innerHTML = payroll.MrateAnnuity; //국민연금
			}
		}
	);
}

// 급여 일괄처리
function _pay_conf(p_index, p_center, p_type){
	var body		= '';
	var count		= document.getElementById('yoyCount').value;
	var objectCode	= document.getElementsByName('yoyCode[]');
	var objectType	= document.getElementsByName('yoyPayType[]');
	var yoyCode		= '';
	var yoyType		= '';

	if (parseInt(p_index, 10) < 0) p_index = count - 1;
	if (parseInt(p_index, 10) > count) p_index = 0;
	if (p_index == undefined) p_index = 0;
	
	if (p_center == '1234' || p_center == '0627141516'){
	}else{
		if (typeof(p_index) == 'number'){
			// 조회 달에 실적 확정하지않은 수급자를 확인한다.
			var code	= document.getElementById('mCode').value;
			var kind	= document.getElementById('mKind').value;
			var year	= document.getElementById('mYear').value;
			var month	= document.getElementById('mMonth').value;
			var URL		= 'check_conf.php';
			var xmlhttp = new Ajax.Request(
				URL, {
					method:'post',
					parameters:{
						code:document.getElementById('mCode').value,
						kind:document.getElementById('mKind').value,
						year:document.getElementById('mYear').value,
						month:document.getElementById('mMonth').value
					},
					onSuccess:function (responseHttpObj) {
						var request = responseHttpObj.responseText;
						if (request != ''){
							myBody.innerHTML = '<table class="view_type1" style="width:100%; height:100%; border-bottom:0;">'
											 + '<thead>'
											 + '	<tr>'
											 + '		<th style="height:25px;"></th>'
											 + '	</tr>'
											 + '	<tr>'
											 + '		<th style="height:51px;"></th>'
											 + '	</tr>'
											 + '</thead>'
											 + '<tbody>'
											 + '	<tr>'
											 + '		<td style="text-align:center; border-bottom:0;">'
											 + __loadingBar()
											 + '		</td>'
											 + '	</tr>'
											 + '</tbody>'
											 + '</table>';
							//alert('수급자('+request+')의 '+year+'년 '+month+'월 실적확정처리가 되지 않았습니다. 확정처리 후 급여를 실행하여 주십시오.');
							alert(year+'년 '+month+'월 실적확정처리 미실행 수급자가 있습니다. 확정처리 후 급여를 실행하여 주십시오.');
							location.href = '../work/month_conf.php?menuIndex=5&menuSeq=2&mKind='+kind+'&mYear='+year;
							return;
						}
					}
				}
			);
		}
	}
	
	/*
	var URL = 'payroll_4.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.getElementById('code').value,
				mKind:document.getElementById('kind').value,
				mYear:document.getElementById('year').value,
				mMonth:document.getElementById('month').value,
				mYoyangsa:objectCode[p_index].value+';;',
				mType:objectType[p_index].value+';;',
				mIndex:p_index
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;

				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
				}

				document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
				
				//calPay(0);
			}
		}
	);
	*/

	var URL = 'pay_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:document.getElementById('code').value,
				kind:document.getElementById('kind').value,
				year:document.getElementById('year').value,
				month:document.getElementById('month').value,
				member:objectCode[p_index].value,
				type:objectType[p_index].value,
				index:p_index
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				
				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
				}

				document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
				
				//calPay(0);
			}
		}
	);
}

// 급여 일괄처리 이동
function _movePayroll(pos){
	var divBody = document.getElementsByName('divBody[]');
	var count = divBody.length;
	
	if (pos < 0) pos = count - 1;
	if (pos >= count) pos = 0;
	
	for(var i=0; i<count; i++){
		divBody[i].style.display = 'none';
		document.getElementById('yoy_'+i).style.textDecoration = '';
	}
	divBody[pos].style.display = '';
	//document.getElementById('yoy_'+pos).style.color = '#000';
	document.getElementById('yoy_'+pos).style.textDecoration = 'underline';

	//if (document.getElementsByName('type[]')[pos].value == '3'){
	calPay(pos);
	//}
}

// 급여합계
function setPaySubSum(p_kind1, p_kind2){
	var object = document.getElementsByName("amount_"+p_kind1+"_"+p_kind2+"[]");
	var amount = 0;

	for(var i=0; i<object.length; i++){
		amount += parseInt(__commaUnset(object[i].value));
	}

	document.getElementById("totalAmount_"+p_kind1+"_"+p_kind2).value = __commaSet(amount);

	document.getElementById("payment").value = parseInt(__commaUnset(document.getElementById("totalAmount_1_1").value)) + parseInt(__commaUnset(document.getElementById("totalAmount_1_2").value));
	document.getElementById("deducted").value = parseInt(__commaUnset(document.getElementById("totalAmount_2_1").value)) + parseInt(__commaUnset(document.getElementById("totalAmount_2_2").value)) + parseInt(__commaUnset(document.getElementById("totalAmount_2_3").value));
	document.getElementById("diffPayment").value = parseInt(__commaUnset(document.getElementById("payment").value)) - parseInt(__commaUnset(document.getElementById("deducted").value));

	document.getElementById("payment").value = __commaSet(document.getElementById("payment").value);
	document.getElementById("deducted").value = __commaSet(document.getElementById("deducted").value);
	document.getElementById("diffPayment").value = __commaSet(document.getElementById("diffPayment").value);
}

// 요양사별 일별 수당리스트(월급제)
function getSalaryList(p_body, p_code, p_kind, p_year, p_month){
	var URL = 'salary_list.php';
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
				p_body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 보전수당이동
function bojeonMove(){
}

// 급여저장
function regSalaryOk1(){
	// 1.요양보호사 선택 확인
	if (document.f.yoyCode.value == ''){
		alert('요양보호사를 선택하여 주십시오.');
		return;
	}
	
	// 2.방문요양기준시급 확인
	if (document.f.type.value != '3' && __NaN(__commaUnset(document.f.hourly.value)) == 0){
		alert('방문요양기준시급이 입력되어 있지 않습니다. 확인하여 주십시오.');
		return;
	}
	
	// 3.계약시급확인
	if (document.f.type.value != '3' && __NaN(__commaUnset(document.f.minPay.value)) == 0){
		alert('방문요양기준시급이 입력되어 있지 않습니다. 확인하여 주십시오.');
		return;
	}

	// 4.계획근로시간 확인
	if (document.f.type.value != '3' && __NaN(document.f.planTime.value) == 0){
		alert('계획근로시간이 입력되어 있지 않습니다. 확인하여 주십시오.');
		return;
	}

	// 5.월주간근무시간 확인
	if (document.f.type.value != '3' && __NaN(document.f.workTime.value) == 0){
		alert('월주간근무시간이 입력되어 있지 않습니다. 확인하여 주십시오.');
		return;
	}

	// 6.국민연금 신고 월급여액 확인
	if (__NaN(__commaUnset(document.f.annuityPay.value)) == 0){
		alert('국민연금 신고 월급여액을 입력하여 주십시오.');
		return;
	}

	document.f.action = 'payroll_3_ok.php';
	document.f.submit();
}

// 급여삭제
function delSalaryOk1(){
	if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}
	document.f.action = 'payroll_1_del.php';
	document.f.submit();
}

// 급여저장
function regSalaryOk4(index){
	//if (index != 'all'){
	//	var object = document.getElementsByName('yoyNames[]');
	//	var start = index;
	//	var end = index + 1;
	//}else{
		var object = document.getElementsByName('yoyNames[]');
		var start = 0;
		var end = object.length;
	//}

	for(var i=start; i<end; i++){
		// 1.요양보호사 선택 확인
		//if (document.getElementsByName('yoyCodes[]')[i].value == ''){
		//	alert('요양보호사['+object[i].value+']를 선택하여 주십시오.');
		//	return;
		//}
		
		if (document.getElementsByName('type[]')[i].value != '3'){
			// 고정시급제이며 가족케어가 아닌경우
			if (document.getElementsByName('payType[]')[i].value == 'Y' && document.getElementsByName('familyYN[]')[i].value == 'N'){
				// 2.방문요양기준시급 확인
				if (__NaN(__commaUnset(document.getElementsByName('hourly[]')[i].value)) == 0){
					alert('요양보호사['+object[i].value+']의 "방문요양기준시급"이 입력되어 있지 않습니다. 확인하여 주십시오.');
					return;
				}
				
				// 3.계약시급확인
				if (__NaN(__commaUnset(document.getElementsByName('minPay[]')[i].value)) == 0){
					alert('요양보호사['+object[i].value+']의 "계약시급"이 입력되어 있지 않습니다. 확인하여 주십시오.');
					return;
				}

				// 4.계획근로시간 확인
				if (__NaN(document.getElementsByName('planTime[]')[i].value) == 0){
					alert('요양보호사['+object[i].value+']의 "계획근로시간"이 입력되어 있지 않습니다. 확인하여 주십시오.');
					return;
				}

				// 5.월주간근무시간 확인
				if (__NaN(document.getElementsByName('workTime[]')[i].value) == 0){
					alert('요양보호사['+object[i].value+']의 "월주간근무시간"이 입력되어 있지 않습니다. 확인하여 주십시오.');
					return;
				}
			}
		}

		// 6.국민연금 신고 월급여액 확인
		if (__NaN(__commaUnset(document.getElementsByName('annuityPay[]')[i].value)) == 0){
			alert('요양보호사['+object[i].value+']의 "국민연금 신고 월급여액"을 입력하여 주십시오.');
			document.getElementsByName('annuityPay[]')[i].focus();
			return;
		}
	}
	
	document.f.index.value = index;
	document.f.action = 'payroll_4_ok.php';
	document.f.submit();
}

// 급여삭제
function delSalaryOk4(index){
	if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}

	document.f.index.value = index;
	document.f.action = 'payroll_4_del.php';
	document.f.submit();
}

// 일괄처리
function allRegSalaryOk1(){
	if (!confirm('시급요양보호사의 급여 처리를 진행하시겠습니까?')){
		return;
	}
	var URL = 'payroll_2.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mYear:document.f.mYear.value,
				mMonth:document.f.mMonth.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;

				var count = document.getElementById('yoyCount').value;

				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
					calPay2(i)
				}
			}
		}
	);
}

// 전체삭제
function allDelSalaryOk1(){
	if (document.getElementById('dataCount').value == 0){
		alert('저장할 데이타가 없습니다.');
		return;
	}

	if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')){
		return;
	}
	document.f.action = 'payroll_1_del_all.php';
	document.f.submit();
}

// 일괄저장
function allRegSalaryOk2(){
	if (document.getElementById('dataCount').value == 0){
		alert('저장할 데이타가 없습니다.');
		return;
	}
	
	if (!confirm('시급요양보호사의 급여 처리를 저장하시겠습니까?')){
		return;
	}

	document.f.action = 'payroll_2_ok.php';
	document.f.submit();
}

// 급여명세서
function paymentTable(p_code, p_kind, p_year, p_month, p_target){
	var width  = 700;
	var height = 900;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	
	window.open('../salary/payroll_4_pdf.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&target='+p_target, 'POPUP', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');
}

// 월급여 클래스
var Payroll = function(){
	this.Init();
}

Payroll.prototype.Init = function(p_year){
	var date = new Date();

	this.year = p_year;
	this.type       = '';  //급여방식
	this.payType    = '0'; //급여 고정 및 등급별 여부
	this.familyCare = '0'; //가족케어여부
	this.familyType = '1'; //가족케어 급여방식 1:시급, 2:총액비율

	this.workWeekTime = 0; //주휴시간

	this.prolongRate = 0; //연장가산비율
	this.nightRate   = 0; //야간가산비율
	this.holidayRate = 0; //휴일가산비율
	
	this.hourly		= 0;	//시급
	this.minHourly	= 0;	//최소시급
	this.yoyul		= 0;	//총액비율
	this.suga		= 0;	//수가
	
	this.annuityPay = 0; //국민연금 신고 월급여액

	this.planTime    = 0; //계획시간
	this.workTime    = 0; //월주간근무시간
	this.prolongTime = 0; //월연장가산근로시간
	this.nightTime   = 0; //월야간가산근로시간
	this.holidayTime = 0; //월휴일가산근로시간

	this.hourlys      = new Array(); //시급
	this.planTimes    = new Array(); //계획시간
	this.workTimes    = new Array(); //월주간근무시간
	this.prolongTimes = new Array(); //월연장가산근로시간
	this.nightTimes   = new Array(); //월야간가산근로시간
	this.holidayTimes = new Array(); //월휴일가산근로시간
	this.sugas		  = new Array(); //수가
	this.yoyuls		  = new Array(); //총액비율

	this.totalHour   = 0; //월산출 총시간
	this.workHour    = 0; //월소정근로시간
	this.prolongHour = 0; //연장가산근로시간
	this.nightHour   = 0; //야간가산근로시간
	this.holidayHour = 0; //휴일가산근로시간

	this.totalPay = 0; //총급여
	this.basePay  = 0; //기본급

	this.bojeonSudang  = 0; //보전수당
	this.prolongSudang = 0; //연장가산수당
	this.nightSudang   = 0; //야간가산수당
	this.holidaySudang = 0; //휴일가산수당

	this.bathSudang    = 0; //목욕수당
	this.nursingSudang = 0; //간호수당

	this.foodPay  = 0; //식대비
	this.carPay   = 0; //차량유지비
	this.minusPay = 0; //차감액

	this.taxSudang     = new Array(); //과세수당 배열
	this.taxfreeSudang = new Array(); //비과세수당 배열

	this.totalTaxSudang     = 0; //과세총액
	this.totalTaxfreeSudang = 0; //비과세총액

	this.incomePay = new Array(); //소득세배열
	this.insPay    = new Array(); //보험배열
	this.otherPay  = new Array(); //기타배열

	this.totalIncomePay = 0; //소득세합계
	this.totalInsPay    = 0; //보험합계
	this.totalOtherPay  = 0; //기타합계

	this.taxPay = 0; //과세총액
	this.taxfreePay = 0; //비과세총액

	// 근로자 보험 가입여부
	this.employYN  = '0'; //고용보험
	this.healthYN  = '0'; //건강보험
	this.sanjeYN   = '0'; //산제보험
	this.annuityYN = '0'; //국민연금
	
	// 보험 근로자 부담 비율
	this.workerRateEmploy  = 0.45; //고용보험
	this.workerRateHealth  = this.year == 2011 ? 2.82 : 2.665; //건강보험
	this.workerRateOldcare = 6.55; //노인장기요양
	this.workerRateAnnuity = 4.5;  //국민연금

	// 보험 센터 부담비율
	this.centerRateEmploy  = 0.7;  //고용보험
	this.centerRateSanje   = 0.72; //산재보험
	this.centerRateHealth  = this.year == 2011 ? 2.82 : 2.665; //건강보험
	this.centerRateOldcare = 6.55; //노인장기요양
	this.centerRateAnnuity = 4.5;  //국민연금

	// 근로자 부담비율
	this.workerEmployRate  = this.workerRateEmploy  / 100; //고용보험
	this.workerHealthRate  = this.workerRateHealth  / 100; //건강보험
	this.workerOldcareRate = this.workerRateOldcare / 100; //노인장기요양
	this.workerAnnuityRate = this.workerRateAnnuity / 100; //국민연금

	// 고용자 부담비율
	this.centerEmployRate  = this.centerRateEmploy  / 100; //고용보험
	this.centerSanjeRate   = this.centerRateSanje   / 100; //산재보험
	this.centerHealthRate  = this.centerRateHealth  / 100; //건강보험
	this.centerOldcareRate = this.centerRateOldcare / 100; //노인장기요양
	this.centerAnnuityRate = this.centerRateAnnuity / 100; //국민연금

	// 공제전금액
	this.deductPay = 0;

	// 차인지급액
	this.diffPay = 0;
}

// 월소정근로시간 입력
Payroll.prototype.WorkHour = function(){
	/*
	 * 월 주간 근무시간 + 주휴시간
	 */
	if (this.payType == '1' && this.familyCare == '0'){
		this.workHour = parseFloat(this.workTime) + parseFloat(this.workWeekTime);
	}else if (this.type == '4' && this.familyCare != '0' && this.familyType == '1'){
		this.workHour = parseFloat(this.workTime) + parseFloat(this.workWeekTime);
	}else{
		this.workHour = 0;

		for(var i=0; i<this.workTimes.length; i++){
			this.workHour += parseFloat(this.workTimes[i]);
		}
		this.workHour += parseFloat(this.workWeekTime);
	}

	return this.workHour; 
}

// 연장가산근로시간
Payroll.prototype.ProlongHour = function(){
	/*
	 * 연장근로시간 * 가산비율 / 100
	 */
	if (this.payType == '1' && this.familyCare == '0'){
		//this.prolongHour = __round(parseFloat(this.prolongTime * (this.prolongRate / 100)), 1); //데이타 조회시 계산 안할 경우
		this.prolongHour = __round(parseFloat(this.prolongTime), 1); //데이타 조회시 계산한경우
	}else{
		this.prolongHour = 0;

		for(var i=0; i<this.prolongTimes.length; i++){
			this.prolongHour += parseFloat(this.prolongTimes[i]);
		}

		//this.prolongHour = __round(this.prolongHour * (this.prolongRate / 100), 1);
		this.prolongHour = __round(this.prolongHour, 1);
	}
	return this.prolongHour;
}

// 야간근로시간
Payroll.prototype.NightHour = function(){
	if (this.payType == '1' && this.familyCare == '0'){
		//this.nightHour = __round(parseFloat(this.nightTime * (this.nightRate / 100)), 1);
		this.nightHour = __round(parseFloat(this.nightTime), 1);
	}else{
		this.nightHour = 0;

		for(var i=0; i<this.nightTimes.length; i++){
			this.nightHour += parseFloat(this.nightTimes[i]);
		}

		//this.nightHour = __round(this.nightHour * (this.nightRate / 100), 1);
		this.nightHour = __round(this.nightHour, 1);
	}
	return this.nightHour;
}

// 휴일근로시간
Payroll.prototype.HolidayHour = function(){
	if (this.payType == '1' && this.familyCare == '0'){
		//this.holidayHour = __round(parseFloat(this.holidayTime * (this.holidayRate / 100)), 1); //요율을 있는되로 계산
		this.holidayHour = __round(parseFloat(this.holidayTime), 1); //입력받은 그대로 계산
		//this.holidayHour = __round(parseFloat(this.holidayTime * ((this.holidayRate - 100) / 100)), 1); //요율에서 100을 제외하고 계산
	}else{
		this.holidayHour = 0;

		for(var i=0; i<this.holidayTimes.length; i++){
			this.holidayHour += parseFloat(this.holidayTimes[i]);
		}

		//this.holidayHour = __round(this.holidayHour * (this.holidayRate / 100), 1);
		this.holidayHour = __round(this.holidayHour, 1);
		//this.holidayHour = __round(this.holidayHour * ((this.holidayRate - 100) / 100), 1);
	}
	return this.holidayHour;
}

// 월산출총시간
Payroll.prototype.TotalHour = function(){
	this.totalHour = __round(parseFloat(this.workHour) + parseFloat(this.prolongHour) + parseFloat(this.nightHour) + parseFloat(this.holidayHour), 1);
	return this.totalHour;
}

// 총급여(월주간근무시간 * 방문요양기준시급)
Payroll.prototype.TotalPay = function(){
	if (this.type == '1' || this.type == '2'){
		//this.totalPay = Math.floor(parseFloat(this.hourly) * (parseFloat(this.workHour) + parseFloat(this.prolongHour) + parseFloat(this.nightHour) + parseFloat(this.holidayHour)));
		if (this.payType == '1' && this.familyCare == '0'){
			this.totalPay = Math.floor(parseFloat(this.hourly) * parseFloat(this.workTime));
		}else{
			this.totalPay = 0;
			
			for(var i=0; i<this.hourlys.length; i++){
				this.totalPay += (parseFloat(this.hourlys[i]) * parseFloat(this.workTimes[i]));
			}

			if (this.familyCare != '0' && this.familyType == '2'){
				this.totalPay += Math.floor(parseFloat(this.suga) * parseFloat(this.yoyul) / 100);
			}

			this.totalPay = Math.floor(this.totalPay);
		}
		/*
		this.totalPay = parseFloat(this.totalPay)
					  + parseFloat(this.ProlongSudang()) 
					  + parseFloat(this.NightSudang()) 
					  + parseFloat(this.HolidaySudang()) 
					  + parseFloat(this.bathSudang) 
					  + parseFloat(this.nursingSudang) 
					  + parseFloat(this.totalTaxSudang);
		*/
	}else if (this.type == '4'){
		/*
		if (this.sugas.length > 0){
			for(var i=0; i<this.sugas.length; i++){
				this.totalPay += Math.floor(parseFloat(this.sugas[i]));
			}
		}else{
			this.totalPay = Math.floor(parseFloat(this.suga) * parseFloat(this.yoyul) / 100);
		}
		*/

		if (this.familyCare == '0'){ //동거가족이 없을 경우
			this.totalPay = Math.floor(parseFloat(this.suga) * parseFloat(this.yoyul) / 100);
		}else{
			if (this.familyType == '1'){ //동거가족이 시급일 경우
				this.totalPay = (Math.floor(parseFloat(this.suga) * parseFloat(this.yoyul) / 100))
							  + (Math.floor(parseFloat(this.hourly) * parseFloat(this.workTime)));
			}else{ //동거가족이 비율인 경우
				for(var i=0; i<this.sugas.length; i++){
					this.totalPay += Math.floor(parseFloat(this.sugas[i]) * parseFloat(this.yoyuls[i]) / 100);
				}
			}
		}

		this.totalPay = cutOff(this.totalPay); //절사
	}else{
		this.totalPay = Math.floor(parseFloat(this.basePay) + parseFloat(this.totalTaxSudang));
	}
	
	this.totalPay = cutOff(this.totalPay);

	return this.totalPay;
}

// 기본급(최소시급 * 월소정근로시간)
Payroll.prototype.BasePay = function(){
	if (this.type != '3'){
		this.basePay = Math.round(parseFloat(this.minHourly) * parseFloat(this.workHour));
		this.basePay = cutOff(this.basePay); //절사
	}
	return this.basePay;
}

// 야간근무수당
Payroll.prototype.ProlongSudang = function(){
	if (this.payType == '1' && this.familyCare == '0'){
		//this.prolongSudang = Math.floor(parseFloat(this.minHourly) * parseFloat(this.prolongHour));
		this.prolongSudang = Math.round(parseFloat(this.hourly) * parseFloat(this.prolongHour));
	}else{
		this.prolongSudang = 0;

		for(var i=0; i<this.prolongTimes.length; i++){
			this.prolongSudang += (parseFloat(this.hourlys[i]) * parseFloat(this.prolongTimes[i]));
		}

		this.prolongSudang = Math.round(this.prolongSudang);
	}
	return this.prolongSudang;
}

// 심야근무수당
Payroll.prototype.NightSudang = function(){
	if (this.payType == '1' && this.familyCare == '0'){
		//this.nightSudang = Math.floor(parseFloat(this.minHourly) * parseFloat(this.nightHour));
		this.nightSudang = Math.round(parseFloat(this.hourly) * parseFloat(this.nightHour));
	}else{
		this.nightSudang = 0;

		for(var i=0; i<this.nightTimes.length; i++){
			this.nightSudang += (parseFloat(this.hourlys[i]) * parseFloat(this.nightTimes[i]));
		}

		this.nightSudang = Math.round(this.nightSudang);
	}
	return this.nightSudang;
}

// 휴일근무수당
Payroll.prototype.HolidaySudang = function(){
	if (this.payType == '1' && this.familyCare == '0'){
		//this.holidaySudang = Math.floor(parseFloat(this.minHourly) * parseFloat(this.holidayHour));
		this.holidaySudang = Math.round(parseFloat(this.hourly) * parseFloat(this.holidayHour));
		
	}else{
		this.holidaySudang = 0;

		for(var i=0; i<this.holidayTimes.length; i++){
			this.holidaySudang += (parseFloat(this.hourlys[i]) * parseFloat(this.holidayTimes[i]));
		}

		this.holidaySudang = Math.round(this.holidaySudang);
	}
	return this.holidaySudang;
}

// 보전수당(총금액 - (기본급 + 식대비 + 차량유지비))
Payroll.prototype.BojeonSudang = function(){
	//this.bojeonSudang = parseFloat(this.totalPay) - parseFloat(parseFloat(this.basePay) + parseFloat(this.prolongSudang) + parseFloat(this.nightSudang) + parseFloat(this.holidaySudang) + parseFloat(this.foodPay) + parseFloat(this.carPay));
	this.bojeonSudang = parseFloat(this.totalPay) - parseFloat(parseFloat(this.basePay) + parseFloat(this.foodPay) + parseFloat(this.carPay));
	//this.bojeonSudang = parseFloat(this.totalPay) - (parseFloat(this.basePay) + parseFloat(this.totalTaxSudang) + parseFloat(this.totalTaxfreeSudang));
	return this.bojeonSudang;
}

// 주휴시간
Payroll.prototype.WorkWeekTime = function(workCount, weekCount){
	if (this.payType == '1' && this.familyCare == '0'){
		if (this.workTime >= 60){
			return __round((this.workTime / workCount) * weekCount, 1);
		}else{
			return 0;
		}
	}else{
		var weekTime = 0;

		for(var i=0; i<this.workTimes.length; i++){
			weekTime += parseFloat(this.workTimes[i]);
		}
		if (weekTime >= 60){
			return __round((weekTime / workCount)  * weekCount, 1);
		}else{
			return 0;
		}
	}
}

// 과세총액
Payroll.prototype.TaxPay = function(){
	this.taxPay = parseFloat(this.BasePay()) 
				+ parseFloat(this.bojeonSudang) 
				+ parseFloat(this.prolongSudang) 
				+ parseFloat(this.nightSudang) 
				+ parseFloat(this.holidaySudang) 
				+ parseFloat(this.bathSudang) 
				+ parseFloat(this.nursingSudang) 
				+ parseFloat(this.totalTaxSudang);
	return this.taxPay;
}

// 비과세총액
Payroll.prototype.TaxfreePay = function(){
	this.taxfreePay = parseFloat(this.totalTaxfreeSudang) - parseFloat(this.minusPay);
	return this.taxfreePay;
}

// 공제전금액
Payroll.prototype.DeductPay = function(){
	this.deductPay = parseFloat(this.taxPay) + parseFloat(this.taxfreePay) - parseFloat(this.minusPay);
	return this.deductPay;
}

// 차인지급액
Payroll.prototype.DiffPay = function(){
	this.diffPay = parseFloat(this.deductPay) - (parseFloat(this.totalIncomePay) + parseFloat(this.totalInsPay) + parseFloat(this.totalOtherPay));
	return this.diffPay;
}

// 수당합계
Payroll.prototype.TotalSudang = function(){
	this.totalSudang = parseFloat(this.bojeonPay) + parseFloat(this.prolongPay) + parseFloat(this.nightPay) + parseFloat(this.holidayPay) + parseFloat(this.bathSudang) + parseFloat(this.nursingSudang) + parseFloat(this.totalTaxSudang);
	return this.totalSudang;
}

var payroll = new Payroll();

function calPay(index){
	var year = document.getElementsByName('year[]')[index].value;
	
	payroll.Init(year);
	payroll.type       = document.getElementsByName('type[]')[index].value;			//급여형식
	payroll.payType    = document.getElementsByName('payType[]')[index].value;		//시급 고정여부
	payroll.familyCare = document.getElementsByName('familyYN[]')[index].value;		//가족케어여부
	payroll.familyType = document.getElementsByName('familyType[]')[index].value;	//가족케어급여방식

	payroll.annuityPay = __NaN(__commaUnset(document.getElementsByName('annuityPay[]')[index].value)); //국민여금 월신고 급여액
	payroll.minHourly  = __NaN(__commaUnset(document.getElementsByName('minPay[]')[index].value)); //최소시급입력

	if (payroll.type == '1' || payroll.type == '2'){
		if (payroll.payType == '1' && payroll.familyCare == '0'){
			payroll.hourly      = __NaN(__commaUnset(document.getElementsByName('hourly[]')[index].value)); //시급입력
			payroll.workTime    = __NaN(document.getElementsByName('workTime[]')[index].value);				//월주간근무시간
			payroll.prolongTime = __NaN(document.getElementsByName('prolongTime[]')[index].value);			//월연장가산근로시간
			payroll.nightTime   = __NaN(document.getElementsByName('nightTime[]')[index].value);			//월야간가산근로시간
			payroll.holidayTime = __NaN(document.getElementsByName('holidayTime[]')[index].value);			//월휴일가산근로시간
		}else{
			var count = document.getElementsByName('code_'+index+'[]').length;
			
			if (payroll.familyCare != 0 && payroll.familyType == 2) count --;

			for(var i=0; i<count; i++){
				payroll.hourlys[i]		= __NaN(__commaUnset(document.getElementsByName('hourly_'+index+'[]')[i].value));	//시급
				payroll.workTimes[i]	= __NaN(document.getElementsByName('workTime_'+index+'[]')[i].value);				//월주간근무시간
				payroll.prolongTimes[i] = __NaN(document.getElementsByName('prolongTime_'+index+'[]')[i].value);			//월연장가산근로시간
				payroll.nightTimes[i]	= __NaN(document.getElementsByName('nightTime_'+index+'[]')[i].value);				//월야간가산근로시간
				payroll.holidayTimes[i] = __NaN(document.getElementsByName('holidayTime_'+index+'[]')[i].value);			//월휴일가산근로시간
			}

			if (payroll.familyCare != 0 && payroll.familyType == 2){
				payroll.yoyul		= __NaN(document.getElementsByName('yoyul[]')[0].value);
				payroll.suga		= __NaN(document.getElementsByName('suga[]')[0].value.split(',').join(''));
				payroll.workTime    = __NaN(document.getElementsByName('workTime[]')[0].value); //월주간근무시간
			}
		}
	}else if (payroll.type == '4'){ // 총액요율제
		if (payroll.familyCare == '2'){
			if (payroll.familyType == '1'){ //동거가족 시급
				payroll.yoyul		= __NaN(document.getElementsByName('yoyul[]')[0].value);
				payroll.suga		= __NaN(document.getElementsByName('suga[]')[0].value.split(',').join(''));
				payroll.workTime    = __NaN(document.getElementsByName('workTime[]')[0].value); //월주간근무시간

				payroll.hourly		= __NaN(__commaUnset(document.getElementsByName('hourly_'+index+'[]')[0].value));	//시급
				payroll.workTime	= __NaN(document.getElementsByName('workTime_'+index+'[]')[0].value);				//월주간근무시간
				payroll.prolongTime = __NaN(document.getElementsByName('prolongTime_'+index+'[]')[0].value);			//월연장가산근로시간
				payroll.nightTime	= __NaN(document.getElementsByName('nightTime_'+index+'[]')[0].value);				//월야간가산근로시간
				payroll.holidayTime = __NaN(document.getElementsByName('holidayTime_'+index+'[]')[0].value);			//월휴일가산근로시간
			}else{ //동거가족 비율
				var count = document.getElementsByName('suga[]').length;

				for(var i=0; i<count; i++){
					payroll.yoyuls[i]	= __NaN(document.getElementsByName('yoyul[]')[i].value);
					payroll.sugas[i]	= __NaN(document.getElementsByName('suga[]')[i].value.split(',').join(''));
					payroll.workTimes[i]= __NaN(document.getElementsByName('workTime[]')[i].value); //월주간근무시간
				}
			}
		}else{ // 총액비율제
			payroll.yoyul		= __NaN(document.getElementsByName('yoyul[]')[index].value);
			payroll.suga		= __NaN(document.getElementsByName('suga[]')[index].value.split(',').join(''));
			payroll.workTime    = __NaN(document.getElementsByName('workTime[]')[index].value); //월주간근무시간
		}
	}else{ // 월급제
		payroll.hourly      = 0; //기본급
		payroll.workTime    = 0; //월주간근무시간
		payroll.prolongTime = 0; //월연장가산근로시간
		payroll.nightTime   = 0; //월야간가산근로시간
		payroll.holidayTime = 0; //월휴일가산근로시간
		payroll.basePay     = __NaN(__commaUnset(document.getElementsByName('1_1_01[]')[index].value)); //기본급
	}

	//주휴시간
	payroll.workWeekTime = payroll.WorkWeekTime(parseFloat(__NaN(__commaUnset(document.getElementsByName('workCount[]')[index].value))), 
												parseFloat(__NaN(__commaUnset(document.getElementsByName('weekCount[]')[index].value))));

	payroll.prolongRate   = __NaN(document.getElementsByName('prolongRate[]')[index].value); //연장가산비율
	payroll.nightRate     = __NaN(document.getElementsByName('nightRate[]')[index].value);   //야간가산비율
	payroll.holidayRate   = __NaN(document.getElementsByName('holidayRate[]')[index].value); //휴일가산비율
	payroll.bathSudang    = __NaN(__commaUnset(document.getElementsByName('bathSudang[]')[index].value));    //목욕수당
	payroll.nursingSudang = __NaN(__commaUnset(document.getElementsByName('nursingSudang[]')[index].value)); //간호수당
	payroll.minusPay      = __NaN(__commaUnset(document.getElementsByName('minusPay[]')[index].value)); //차감액

	//과세수당
	for(var i=2; i<=10; i++){
		var code = '1_1_'+(i<10?'0':'')+i+'[]';
		var object = document.getElementsByName(code)[index];
		
		if (object != null){
			payroll.taxSudang[code] = __NaN(__commaUnset(object.value));
			payroll.totalTaxSudang += parseFloat(payroll.taxSudang[code]);
		}else{
			payroll.taxSudang[code] = 0;
		}
	}
	
	//비과세수당
	for(var i=1; i<=10; i++){
		var code = '1_2_'+(i<10?'0':'')+i+'[]';
		var object = document.getElementsByName(code)[index];
		
		if (object != null){
			payroll.taxfreeSudang[code] = __NaN(__commaUnset(object.value));
			payroll.totalTaxfreeSudang += parseFloat(payroll.taxfreeSudang[code]);

			if (object.tag == '식대비'){
				payroll.foodPay = payroll.taxfreeSudang[code];
			}else if (object.tag == '차량유지비'){
				payroll.carPay = payroll.taxfreeSudang[code];
			}
		}else{
			payroll.taxfreeSudang[code] = 0;
		}
	}

	document.getElementsByName('workWeekTime[]')[index].value = payroll.workWeekTime; //주휴시간
	document.getElementsByName('weekAppTime[]')[index].value  = __round(payroll.workTime / parseFloat(__NaN(__commaUnset(document.getElementsByName('workCount[]')[index].value))) * 4.32, 1); //주소정근로시간
	
	document.getElementsByName('prolongHour[]')[index].value = payroll.ProlongHour(); //연장시간
	document.getElementsByName('nightHour[]')[index].value   = payroll.NightHour();   //야간시간
	document.getElementsByName('holidayHour[]')[index].value = payroll.HolidayHour(); //휴일시간

	document.getElementsByName('workHour[]')[index].value  = payroll.WorkHour();  //월소정근로시간
	document.getElementsByName('totalHour[]')[index].value = payroll.TotalHour(); //월산출총시간

	payroll.TotalPay(); //총급여계산

	document.getElementsByName('totalPay[]')[index].value   = __commaSet(payroll.totalPay);   //총급여
	document.getElementsByName('contHourly[]')[index].value = __commaSet(payroll.hourly);     //방문요양기준시급
	document.getElementsByName('baseHourly[]')[index].value = __commaSet(payroll.minHourly);  //최소시급
	document.getElementsByName('1_1_01[]')[index].value     = __commaSet(payroll.BasePay());  //기본급
	
	document.getElementsByName('bojeonSudang[]')[index].value  = __commaSet(payroll.BojeonSudang());  //보전수당
	document.getElementsByName('prolongSudang[]')[index].value = __commaSet(payroll.ProlongSudang()); //연장근무수당
	document.getElementsByName('nightSudang[]')[index].value   = __commaSet(payroll.NightSudang());   //야간근무수당
	document.getElementsByName('holidaySudang[]')[index].value = __commaSet(payroll.HolidaySudang()); //휴일근무수당

	document.getElementsByName('totalTax[]')[index].value     = __commaSet(payroll.TaxPay()); //과세총액
	document.getElementsByName('totalTaxfree[]')[index].value = __commaSet(payroll.TaxfreePay()); //비과세총액

	var incomeTax = 0;

	//소득세항목
	for(var i=1; i<=10; i++){
		var code = '2_1_'+(i<10?'0':'')+i+'[]';
		var object = document.getElementsByName(code)[index];
		
		if (object != null){
			if (object.tag == '갑근세' || object.tag == '소득세'){
				//갑근세
				if (payroll.totalPay > 0){
					var gongjeja = document.getElementsByName('gongjeja[]')[index].value; //공제가족수
					var gongjaye = document.getElementsByName('gongjaye[]')[index].value; //20세이하 자녀수
					
					incomeTax = getHttpRequest('../inc/_check_class.php?check=gapgeunse&year='+year+'&pay='+payroll.totalPay+'&deCnt='+gongjeja+'&chCnt='+gongjaye);
					object.value = __commaSet(incomeTax);
				}else{
					object.value = 0; 
				}
			}else if (object.tag == '주민세'){
				//주민세
				object.value = __commaSet(cutOff(incomeTax * 0.1));
			}else{
				
			}
			
			payroll.incomePay[code] = __NaN(__commaUnset(object.value));
			payroll.totalIncomePay += parseFloat(payroll.incomePay[code]);
		}else{
			payroll.incomePay[code] = 0;
		}
	}
	document.getElementsByName('2_1_total[]')[index].value = __commaSet(payroll.totalIncomePay);

	// 4대보험 가입여부
	payroll.employYN  = document.getElementsByName('employYN[]')[index].value;  //고용보험
	payroll.healthYN  = document.getElementsByName('healthYN[]')[index].value;  //건강보험
	payroll.sanjeYN   = document.getElementsByName('sanjeYN[]')[index].value;   //산제보험
	payroll.annuityYN = document.getElementsByName('annuityYN[]')[index].value; //국민연금

	payroll.taxPay = payroll.TaxPay();

	var healthPay = 0;

	//보험항목
	for(var i=1; i<=10; i++){
		var code = '2_2_'+(i<10?'0':'')+i+'[]';
		var object = document.getElementsByName(code)[index];
		
		if (object != null){
			if (object.tag == '고용보험'){
				object.value = __commaSet(payroll.employYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.workerEmployRate))) : 0);
				
				document.getElementById('rate_'+object.tag).innerHTML = '['+payroll.workerRateEmploy+'%]';

				document.getElementsByName('employAnnuity[]')[index].value       = (payroll.employYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.workerEmployRate))) : 0); //고용보험(국민연금 월 신고금액 계산)
				document.getElementsByName('employCenter[]')[index].value        = (payroll.employYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay)     * parseFloat(payroll.centerEmployRate))) : 0); //고용보험(센터부담)
				document.getElementsByName('employCenterAnnuity[]')[index].value = (payroll.employYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.centerEmployRate))) : 0); //고용보험(센터부담/국민연금 월 신고금액 계산)
			}else if (object.tag == '건강보험'){
				healthPay = (payroll.healthYN  == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.workerHealthRate))) : 0);
				
				object.value = __commaSet(healthPay);

				document.getElementById('rate_'+object.tag).innerHTML = '['+payroll.workerRateHealth+'%]';
				
				document.getElementsByName('healthAnnuity[]')[index].value       = (payroll.healthYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.workerHealthRate))) : 0); //건강보험(국민연금 월 신고금액 계산)
				document.getElementsByName('healthCenter[]')[index].value        = (payroll.healthYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay)     * parseFloat(payroll.centerHealthRate))) : 0); //건강보험(센터부담)
				document.getElementsByName('healthCenterAnnuity[]')[index].value = (payroll.healthYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.centerHealthRate))) : 0); //건강보험(센터부담/국민연금 월 신고금액 계산)
			
				document.getElementsByName('oldcareAnnuity[]')[index].value       = (payroll.healthYN  == '1' ? cutOff(Math.floor(parseFloat(document.getElementsByName('healthAnnuity[]')[index].value)       * parseFloat(payroll.workerOldcareRate))) : 0); //노인장기
				document.getElementsByName('oldcareCenter[]')[index].value        = (payroll.healthYN  == '1' ? cutOff(Math.floor(parseFloat(document.getElementsByName('healthCenter[]')[index].value)        * parseFloat(payroll.centerOldcareRate))) : 0); //노인장기보험센터부담
				document.getElementsByName('oldcareCenterAnnuity[]')[index].value = (payroll.healthYN  == '1' ? cutOff(Math.floor(parseFloat(document.getElementsByName('healthCenterAnnuity[]')[index].value) * parseFloat(payroll.centerOldcareRate))) : 0); //노인장기보험센터부담
			}else if (object.tag == '장기요양'){
				object.value = __commaSet(payroll.healthYN == '1' ? cutOff(Math.floor(parseFloat(healthPay) * parseFloat(payroll.workerOldcareRate))) : 0);

				document.getElementById('rate_'+object.tag).innerHTML = '['+payroll.workerRateOldcare+'%]';
			}else if (object.tag == '국민연금'){
				object.value = __commaSet(payroll.annuityYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.workerAnnuityRate))) : 0);

				document.getElementById('rate_'+object.tag).innerHTML = '['+payroll.workerRateAnnuity+'%]';

				document.getElementsByName('annuityAnnuity[]')[index].value       = (payroll.annuityYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.workerAnnuityRate))) : 0); //국민연금
				document.getElementsByName('annuityCenter[]')[index].value        = (payroll.annuityYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay)     * parseFloat(payroll.centerAnnuityRate))) : 0); //국민연금센터부담
				document.getElementsByName('annuityCenterAnnuity[]')[index].value = (payroll.annuityYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.centerAnnuityRate))) : 0); //국민연금센터부담
			}

			payroll.insPay[code] = __NaN(__commaUnset(object.value));
			payroll.totalInsPay += parseFloat(payroll.insPay[code]);
		}else{
			payroll.insPay[code] = 0;
		}
	}
	// 산재보험
	document.getElementsByName('sanjeCenter[]')[index].value        = (payroll.sanjeYN == '1' ? cutOff(Math.floor(parseFloat(payroll.taxPay)     * parseFloat(payroll.centerSanjeRate))) : 0); //산제보험센터부담
	document.getElementsByName('sanjeCenterAnnuity[]')[index].value = (payroll.sanjeYN == '1' ? cutOff(Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.centerSanjeRate))) : 0); //산제보험센터부담
	document.getElementsByName('2_2_total[]')[index].value = __commaSet(payroll.totalInsPay);

	//기타항목
	for(var i=1; i<=10; i++){
		var code = '2_3_'+(i<10?'0':'')+i+'[]';
		var object = document.getElementsByName(code)[index];
		
		if (object != null){
			payroll.otherPay[code] = __NaN(__commaUnset(object.value));
			payroll.totalOtherPay += parseFloat(payroll.otherPay[code]);
		}else{
			payroll.otherPay[code] = 0;
		}
	}
	document.getElementsByName('2_3_total[]')[index].value = __commaSet(payroll.totalOtherPay);

	// 공제전금액
	document.getElementsByName('deductPay[]')[index].value = __commaSet(payroll.DeductPay()); 
	
	// 차인지급액
	document.getElementsByName('diffPay[]')[index].value = __commaSet(payroll.DiffPay());
	
	if (payroll.TaxfreePay() > 300000){
		alert('비과세 금액이 한도(30만원)을 초과하였습니다. 다시 입력하여 주십시오.');
		//비과세수당
		for(var i=1; i<=10; i++){
			var code = '1_2_'+(i<10?'0':'')+i+'[]';
			var foodCode = null;
			var object = document.getElementsByName(code)[index];
			
			if (object != null){
				if (object.tag == '식대비'){
					foodCode = code;
				}
				object.value = 0;
			}
		}
		calPay();
		document.getElementsByName(foodCode).focus();
		return;
	}
}
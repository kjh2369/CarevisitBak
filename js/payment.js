// 월급여 클래스
var Payroll = function(){
	this.Init();
}

Payroll.prototype.Init = function(){
	var date = new Date();

	this.type = ""; //급여방식

	this.annuityPay = 0; //국민연금 신고 월급여액

	this.insEmploy  = 0; //고용보험근로자부담
	this.insHealth  = 0; //건강보험근로자부담
	this.insOldCare = 0; //노인장기근로자부담
	this.insAnnuity = 0; //국민연금근로자부담
	this.insTotal   = 0; //총합근로자부담

	this.centerInsEmploy  = 0; //고용보험센터부담
	this.centerInsSanje   = 0; //산제보험센터부담
	this.centerInsHealth  = 0; //건강보험센터부담
	this.centerInsOldCare = 0; //노인장기보험센터부담
	this.centerInsAnnuity = 0; //국민연금센터부담
	this.centerInsTotal   = 0; //센터부담함계

	this.MrateEmploy  = 0.45;
	this.MrateHealth  = date.getFullYear() == 2011 ? 2.82 : 2.665;
	this.MrateOldcare = 6.55;
	this.MrateAnnuity = 4.5;

	// 근로자 부담비율
	//this.mEmployRate  = 0.45 / 100;   //고용보험
	//this.mHealthRate  = 2.665 / 100; //건강보험
	//this.mOldCareRate = 6.55 / 100;  //노인장기요양
	//this.mAnnuityRate = 4.5 / 100;   //국민연금
	this.mEmployRate  = this.MrateEmploy / 100;   //고용보험
	this.mHealthRate  = this.MrateHealth / 100; //건강보험
	this.mOldCareRate = this.MrateOldcare / 100;  //노인장기요양
	this.mAnnuityRate = this.MrateAnnuity / 100;   //국민연금

	// 고용자 부담비율
	this.cEmployRate  = 0.7 / 100;   //고용보험
	this.cSanjeRate   = 0.72 / 100;  //산재보험
	this.cHealthRate  = 2.665 / 100; //건강보험
	this.cOldCareRate = 6.55 / 100;  //노인장기요양
	this.cAnnuityRate = 4.5 / 100;   //국민연금

	this.hourly    = 0; //시급
	this.minHourly = 0; //최소시급

	this.weekAppTime = 0; //주소정근로시간
	this.workTime    = 0; //월주간근무시간
	this.prolongTime = 0; //월연장가산근로시간
	this.nightTime   = 0; //월야간가산근로시간
	this.holidayTime = 0; //월휴일가산근로시간
	this.workWeek    = 0; //주휴시간
	this.bathSudang  = 0; //목욕수당
	this.nursSudang  = 0; //간호수당
	this.otherSudang = 0; //기타수당
	this.minusPay    = 0; //차감액

	this.prolongRate = 0; //연장가산비율
	this.nightRate   = 0; //야간가산비율
	this.holidayRate = 0; //휴일가산비율

	this.totalHour   = 0; //월산출총시간
	this.workAppHour = 0; //월소정근로시간
	this.prolongHour = 0; //연장가산근로시간
	this.nightHour   = 0; //야간가산근로시간
	this.holidayHour = 0; //휴일가산근로시간

	this.totalPay  = 0; //총급여
	this.basePay   = 0; //기본급
	this.bojeonPay = 0; //보전수당

	this.prolongPay = 0; //연장근무수당
	this.nightPay   = 0; //야간근무수당
	this.holidayPay = 0; //휴일근무수당

	this.weekCount = 0; //월주일수
	this.workCount = 0; //월근무가능일수

	this.foodPay = 0; //식대
	this.carPay  = 0; //차량유지비

	this.taxPay = 0; //공제금액
	this.taxfreePay = 0; //비공제금액
	this.payTax = 0; //공제전금액

	// 근로자부담(급여계산)
	this.insEmploy  = 0; //고용보험근로자부담
	this.insHealth  = 0; //건강보험근로자부담
	this.insOldCare = 0; //노인장기근로자부담
	this.insAnnuity = 0; //국민연금근로자부담
	this.insTotal   = 0; //총합근로자부담

	// 센터부담(급여계산)
	this.centerInsEmploy  = 0; //고용보험센터부담
	this.centerInsSanje   = 0; //산제보험센터부담
	this.centerInsHealth  = 0; //건강보험센터부담
	this.centerInsOldCare = 0; //노인장기보험센터부담
	this.centerInsAnnuity = 0; //국민연금센터부담
	this.centerInsTotal   = 0; //센터부담함계

	// 근로자부담(국민연금 월 신고금액 계산)
	this.AinsEmploy  = 0; //고용보험근로자부담
	this.AinsHealth  = 0; //건강보험근로자부담
	this.AinsOldCare = 0; //노인장기근로자부담
	this.AinsAnnuity = 0; //국민연금근로자부담
	this.AinsTotal   = 0; //총합근로자부담

	// 센터부담(국민연금 월 신고금액 계산)
	this.AcenterInsEmploy  = 0; //고용보험센터부담
	this.AcenterInsSanje   = 0; //산제보험센터부담
	this.AcenterInsHealth  = 0; //건강보험센터부담
	this.AcenterInsOldCare = 0; //노인장기보험센터부담
	this.AcenterInsAnnuity = 0; //국민연금센터부담
	this.AcenterInsTotal   = 0; //센터부담함계

	this.incomeTax = 0; //갑근세
	this.resiTax   = 0; //주민세
	this.realPay   = 0; //실지급액

	this.insEmployYN  = 'N'; //고용보험
	this.insHealthYN  = 'N'; //건강보험
	this.insSanjeYN   = 'N'; //산제보험
	this.insAnnuityYN = 'N'; //국민연금

	this.totalSudang = 0; //수당합계
}

// 월소정근로시간 입력
Payroll.prototype.WorkAppHour = function(){
	this.workAppHour = parseFloat(this.workTime) + parseFloat(this.workWeek);
	return this.workAppHour; 
}

// 연장가산근로시간
Payroll.prototype.ProlongHour = function(){
	//this.prolongHour = __round(parseFloat(this.prolongTime) + parseFloat(this.prolongTime * (this.prolongRate / 100)), 1);
	this.prolongHour = __round(parseFloat(this.prolongTime * (this.prolongRate / 100)), 1);
	return this.prolongHour;
}

// 야간근로시간
Payroll.prototype.NightHour = function(){
	//this.nightHour = __round(parseFloat(this.nightTime) + parseFloat(this.nightTime * (this.nightRate / 100)), 1);
	this.nightHour = __round(parseFloat(this.nightTime * (this.nightRate / 100)), 1);
	return this.nightHour;
}

// 휴일근로시간
Payroll.prototype.HolidayHour = function(){
	//this.holidayHour = __round(parseFloat(this.holidayTime) + parseFloat(this.holidayTime * (this.holidayRate / 100)), 1);
	this.holidayHour = __round(parseFloat(this.holidayTime * (this.holidayRate / 100)), 1);
	return this.holidayHour;
}

// 월산출총시간
Payroll.prototype.TotalHour = function(){
	this.totalHour = __round(parseFloat(this.workAppHour) + parseFloat(this.prolongHour) + parseFloat(this.nightHour) + parseFloat(this.holidayHour), 1);
	return this.totalHour;
}

// 총급여
Payroll.prototype.TotalPay = function(){
	if (this.type != '3'){
		this.totalPay = Math.floor(parseFloat(this.hourly) * (parseFloat(this.workAppHour) + parseFloat(this.prolongHour) + parseFloat(this.nightHour) + parseFloat(this.holidayHour)));
	}else{
		this.totalPay = Math.floor(parseFloat(this.basePay) + parseFloat(this.otherSudang));
	}
	return this.totalPay;
}

// 기본급(최소시급 * 월소정근로시간)
Payroll.prototype.BasePay = function(){
	if (this.type != '3'){
		this.basePay = Math.floor(parseFloat(this.minHourly) * parseFloat(this.workAppHour));
	}
	return this.basePay;
}

// 연장근무수당
Payroll.prototype.ProlongPay = function(){
	this.prolongPay = Math.floor(parseFloat(this.minHourly) * parseFloat(this.prolongHour));
	return this.prolongPay;
}

// 야간근무수당
Payroll.prototype.NightPay = function(){
	this.nightPay = Math.floor(parseFloat(this.minHourly) * parseFloat(this.nightHour));
	return this.nightPay;
}

// 휴일근무수당
Payroll.prototype.HolidayPay = function(){
	this.holidayPay = Math.floor(parseFloat(this.minHourly) * parseFloat(this.holidayHour));
	return this.holidayPay;
}

// 보전수당(총금액 - (기본급 + 연장 + 야간 + 휴일 수당))
Payroll.prototype.BojeonPay = function(){
	this.bojeonPay = Math.floor(parseFloat(this.totalPay) - parseFloat(parseFloat(this.basePay) + parseFloat(this.prolongPay) + parseFloat(this.nightPay) + parseFloat(this.holidayPay) + parseFloat(this.foodPay) + parseFloat(this.carPay)));
	return this.bojeonPay;
}

// 주휴시간
Payroll.prototype.WorkWeek = function(){
	if (this.workTime >= 60){
		this.workWeek = __round((this.workTime / this.workCount) * this.weekCount, 1);
	}else{
		this.workWeek = 0;
	}
	return this.workWeek;
}

// 과세총액
Payroll.prototype.TaxPay = function(){
	this.taxPay = parseFloat(this.basePay) + parseFloat(this.bojeonPay) + parseFloat(this.prolongPay) + parseFloat(this.nightPay) + parseFloat(this.holidayPay) + parseFloat(this.bathSudang) + parseFloat(this.nursSudang) + parseFloat(this.otherSudang);
	return this.taxPay;
}

// 비과세총액
Payroll.prototype.TaxfreePay = function(){
	this.taxfreePay = parseFloat(this.foodPay) + parseFloat(this.carPay);
	return this.taxfreePay;
}

// 공제전금액
Payroll.prototype.PayTax = function(){
	this.payTax = parseFloat(this.taxPay) + parseFloat(this.taxfreePay);
	return this.payTax;
}

// 실지급액
Payroll.prototype.RealPay = function(){
	this.realPay = parseFloat(this.payTax) - (parseFloat(this.minusPay) + parseFloat(this.insTotal) + parseFloat(this.incomeTax) + parseFloat(this.resiTax));
	return this.realPay;
}

// 수당합계
Payroll.prototype.TotalSudang = function(){
	this.totalSudang = parseFloat(this.bojeonPay) + parseFloat(this.prolongPay) + parseFloat(this.nightPay) + parseFloat(this.holidayPay) + parseFloat(this.bathSudang) + parseFloat(this.nursSudang) + parseFloat(this.otherSudang);
	return this.totalSudang;
}

var payroll = new Payroll();

function calPay(){
	var year = document.getElementById('year').value;
	
	payroll.Init();
	payroll.type = document.getElementById('type').value; //급여형식
	payroll.annuityPay = __NaN(__commaUnset(document.getElementById('annuityPay').value)); //국민여금 월신고 급여액

	if (payroll.type != "3"){
		payroll.hourly    = __NaN(__commaUnset(document.getElementById('hourly').value)); //시급입력
		payroll.minHourly = __NaN(__commaUnset(document.getElementById('minPay').value)); //최소시급입력

		payroll.weekAppTime = __NaN(document.getElementById('weekAppTime').value); //주소정근로시간
		payroll.workTime    = __NaN(document.getElementById('workTime').value); //월주간근무시간
		payroll.prolongTime = __NaN(document.getElementById('prolongTime').value); //월연장가산근로시간
		payroll.nightTime   = __NaN(document.getElementById('nightTime').value); //월야간가산근로시간
		payroll.holidayTime = __NaN(document.getElementById('holidayTime').value); //월휴일가산근로시간
		payroll.bathSudang  = __NaN(__commaUnset(document.getElementById('bathSudang').value)); //목욕수당
		payroll.nursSudang  = __NaN(__commaUnset(document.getElementById('nursSudang').value)); //간호수당
	}else{
		payroll.hourly    = 0; //기본급
		payroll.minHourly = __NaN(__commaUnset(document.getElementById('minPay').value)); //최소시급입력

		payroll.weekAppTime = 0; //주소정근로시간
		payroll.workTime    = 0; //월주간근무시간
		payroll.prolongTime = 0; //월연장가산근로시간
		payroll.nightTime   = 0; //월야간가산근로시간
		payroll.holidayTime = 0; //월휴일가산근로시간
		payroll.bathSudang  = 0; //목욕수당
		payroll.nursSudang  = 0; //간호수당
	}
	payroll.otherSudang = __NaN(__commaUnset(document.getElementById('otherSudang').value)); //기타수당
	payroll.minusPay    = __NaN(__commaUnset(document.getElementById('minusSudang').value)); //차감액

	payroll.prolongRate = __NaN(document.getElementById('prolongRate').value); //연장가산비율
	payroll.nightRate   = __NaN(document.getElementById('nightRate').value); //야간가산비율
	payroll.holidayRate = __NaN(document.getElementById('holidayRate').value); //휴일가산비율

	payroll.weekCount = __NaN(__commaUnset(document.getElementById('weekCount').value)); //월주일수
	payroll.workCount = __NaN(__commaUnset(document.getElementById('workCount').value)); //월근무가능일수

	payroll.foodPay = __NaN(__commaUnset(document.getElementById('foodPay').value)); //식대
	payroll.carPay  = __NaN(__commaUnset(document.getElementById('carPay').value)); //차량유지비

	payroll.workWeek    = payroll.WorkWeek(); //주휴시간
	payroll.workAppHour = payroll.WorkAppHour(); //월소정근로시간
	payroll.prolongHour = payroll.ProlongHour(); //연장가산근로시간
	payroll.nightHour   = payroll.NightHour(); //야간가산근로시간
	payroll.holidayHour = payroll.HolidayHour(); //휴일가산근로시간
	payroll.totalHour   = payroll.TotalHour(); //월총시간

	if (payroll.type != "3"){
		payroll.basePay  = payroll.BasePay();  //기본급
		payroll.totalPay = payroll.TotalPay(); //총급여
	}else{
		payroll.basePay  = __NaN(__commaUnset(document.getElementById('hourly').value));  //기본급
		payroll.totalPay = payroll.TotalPay(); //총급여
	}

	payroll.prolongPay = payroll.ProlongPay(); //연장근무수당
	payroll.nightPay   = payroll.NightPay(); //야간근무수당
	payroll.holidayPay = payroll.HolidayPay(); //휴일근무수당

	payroll.bojeonPay = payroll.BojeonPay(); //보전수당

	payroll.taxPay     = payroll.TaxPay(); //공제금액
	payroll.taxfreePay = payroll.TaxfreePay(); //비공제금액
	payroll.payTax     = payroll.PayTax(); //공제전금액

	payroll.insEmployYN  = document.getElementById('insEmployYN').value; //고용보험
	payroll.insHealthYN  = document.getElementById('insHealthYN').value; //건강보험
	payroll.insSanjeYN   = document.getElementById('insSanjeYN').value; //산제보험
	payroll.insAnnuityYN = document.getElementById('insAnnuityYN').value; //국민연금

	// 근로자 부담(급여계산)
	payroll.insEmploy  = (payroll.insEmployYN  == 'Y' ? Math.floor(parseFloat(payroll.taxPay)    * parseFloat(payroll.mEmployRate)) : 0); //고용보험
	payroll.insHealth  = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.taxPay)    * parseFloat(payroll.mHealthRate)) : 0); //건강보험
	payroll.insOldCare = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.insHealth) * parseFloat(payroll.mOldCareRate)) : 0); //노인장기
	payroll.insAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay)    * parseFloat(payroll.mAnnuityRate)) : 0); //국민연금
	payroll.insTotal   = parseFloat(payroll.insEmploy) + parseFloat(payroll.insHealth) + parseFloat(payroll.insOldCare) + parseFloat(payroll.insAnnuity); //합계

	// 근로자부담(국민연금 월 신고금액 계산)
	payroll.AinsEmploy  = (payroll.insEmployYN  == 'Y' ? Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.mEmployRate))  : 0); //고용보험
	payroll.AinsHealth  = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.mHealthRate))  : 0); //건강보험
	payroll.AinsOldCare = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.insHealth)  * parseFloat(payroll.mOldCareRate)) : 0); //노인장기
	payroll.AinsAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.annuityPay) * parseFloat(payroll.mAnnuityRate)) : 0); //국민연금
	payroll.AinsTotal   = parseFloat(payroll.AinsEmploy) + parseFloat(payroll.AinsHealth) + parseFloat(payroll.AinsOldCare) + parseFloat(payroll.AinsAnnuity); //합계
	
	//센터부담(급여계산)
	payroll.centerInsEmploy  = (payroll.insEmployYN  == 'Y' ? Math.floor(parseFloat(payroll.taxPay)          * parseFloat(payroll.cEmployRate))  : 0); //고용보험센터부담
	payroll.centerInsSanje   = (payroll.insSanjeYN   == 'Y' ? Math.floor(parseFloat(payroll.taxPay)          * parseFloat(payroll.cSanjeRate))   : 0); //산제보험센터부담
	payroll.centerInsHealth  = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.taxPay)          * parseFloat(payroll.cHealthRate))  : 0); //건강보험센터부담
	payroll.centerInsOldCare = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.centerInsHealth) * parseFloat(payroll.cOldCareRate)) : 0); //노인장기보험센터부담
	payroll.centerInsAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay)          * parseFloat(payroll.cAnnuityRate)) : 0); //국민연금센터부담
	payroll.centerInsTotal   = parseFloat(payroll.centerInsEmploy) + parseFloat(payroll.centerInsSanje) + parseFloat(payroll.centerInsHealth) + parseFloat(payroll.centerInsOldCare) + parseFloat(payroll.centerInsAnnuity); //센터부담함계

	// 센터부담(국민연금 월 신고금액 계산)
	payroll.AcenterInsEmploy  = (payroll.insEmployYN  == 'Y' ? Math.floor(parseFloat(payroll.annuityPay)      * parseFloat(payroll.cEmployRate))  : 0); //고용보험센터부담
	payroll.AcenterInsSanje   = (payroll.insSanjeYN   == 'Y' ? Math.floor(parseFloat(payroll.annuityPay)      * parseFloat(payroll.cSanjeRate))   : 0); //산제보험센터부담
	payroll.AcenterInsHealth  = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.annuityPay)      * parseFloat(payroll.cHealthRate))  : 0); //건강보험센터부담
	payroll.AcenterInsOldCare = (payroll.insHealthYN  == 'Y' ? Math.floor(parseFloat(payroll.centerInsHealth) * parseFloat(payroll.cOldCareRate)) : 0); //노인장기보험센터부담
	payroll.AcenterInsAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.annuityPay)      * parseFloat(payroll.cAnnuityRate)) : 0); //국민연금센터부담
	payroll.AcenterInsTotal   = parseFloat(payroll.AcenterInsEmploy) + parseFloat(payroll.AcenterInsSanje) + parseFloat(payroll.AcenterInsHealth) + parseFloat(payroll.AcenterInsOldCare) + parseFloat(payroll.AcenterInsAnnuity); //센터부담함계

	if (payroll.totalPay > 0){
		var gongjeja = document.getElementById('gongjeja').value; //공제가족수
		var gongjaye = document.getElementById('gongjaye').value; //20세이하 자녀수

		payroll.incomeTax = getHttpRequest('../inc/_check_class.php?check=gapgeunse&year='+year+'&pay='+payroll.totalPay+'&deCnt='+gongjeja+'&chCnt='+gongjaye); //갑근세
	}else{
		payroll.incomeTax = 0; //갑근세
	}
	
	payroll.resiTax    = cutOff(payroll.incomeTax * 0.1); //주민세
	payroll.realPay    = payroll.RealPay();

	document.getElementById('totalHour').value   = payroll.totalHour; //총근로시간
	document.getElementById('workAppHour').value = payroll.workAppHour; //월소정근로시간
	document.getElementById('prolongHour').value = payroll.prolongHour; //연장시간
	document.getElementById('nightHour').value   = payroll.nightHour; //야간시간
	document.getElementById('holidayHour').value = payroll.holidayHour; //휴일시간

	document.getElementById('totalPay').value   = __commaSet(payroll.totalPay); //총급여
	document.getElementById('contHourly').value = __commaSet(payroll.hourly); //계약서상시급
	document.getElementById('baseHourly').value = __commaSet(payroll.minHourly); //기본시급
	document.getElementById('basePay').value    = __commaSet(payroll.basePay); //기본급

	document.getElementById('bojeonPay').value  = __commaSet(payroll.bojeonPay); //보전수당
	document.getElementById('prolongPay').value = __commaSet(payroll.prolongPay); //연장근무수당
	document.getElementById('nightPay').value   = __commaSet(payroll.nightPay); //야간근무수당
	document.getElementById('holidayPay').value = __commaSet(payroll.holidayPay); //휴일근무수당

	document.getElementById('bathPay').value = __commaSet(payroll.bathSudang); //목욕수당
	document.getElementById('nursPay').value = __commaSet(payroll.nursSudang); //간호수당
	document.getElementById('otherPay').value = __commaSet(payroll.otherSudang); //기타수당

	document.getElementById('minusPay').value = __commaSet(payroll.minusPay); //간호수당

	document.getElementById('disTaxPay').value     = __commaSet(payroll.taxPay); //과세총액
	document.getElementById('disTaxfreePay').value = __commaSet(payroll.taxfreePay); //비과세총액
	document.getElementById('dedTotalPay').value   = __commaSet(payroll.payTax); //공제전총액

	document.getElementById('insEmploy').value  = __commaSet(payroll.insEmploy); //고용보험
	document.getElementById('insHealth').value  = __commaSet(payroll.insHealth); //건강보험
	document.getElementById('insOldCare').value = __commaSet(payroll.insOldCare); //노인장기
	document.getElementById('insAnnuity').value = __commaSet(payroll.insAnnuity); //국민연금
	document.getElementById('insTotal').value   = __commaSet(payroll.insTotal); //합계

	document.getElementById('centerInsEmploy').value  = __commaSet(payroll.centerInsEmploy); //고용보험센터부담
	document.getElementById('centerInsSanje').value   = __commaSet(payroll.centerInsSanje); //산제보험센터부담
	document.getElementById('centerInsHealth').value  = __commaSet(payroll.centerInsHealth); //건강보험센터부담
	document.getElementById('centerInsOldCare').value = __commaSet(payroll.centerInsOldCare); //노인장기요양센터부담
	document.getElementById('centerInsAnnuity').value = __commaSet(payroll.centerInsAnnuity); //국민연금센터부담
	document.getElementById('centerInsTotal').value   = __commaSet(payroll.centerInsTotal); //센터부담합계

	document.getElementById('AinsEmploy').value  = __commaSet(payroll.AinsEmploy); //고용보험
	document.getElementById('AinsHealth').value  = __commaSet(payroll.AinsHealth); //건강보험
	document.getElementById('AinsOldCare').value = __commaSet(payroll.AinsOldCare); //노인장기
	document.getElementById('AinsAnnuity').value = __commaSet(payroll.AinsAnnuity); //국민연금
	document.getElementById('AinsTotal').value   = __commaSet(payroll.AinsTotal); //합계

	document.getElementById('AcenterInsEmploy').value  = __commaSet(payroll.AcenterInsEmploy); //고용보험센터부담
	document.getElementById('AcenterInsSanje').value   = __commaSet(payroll.AcenterInsSanje); //산제보험센터부담
	document.getElementById('AcenterInsHealth').value  = __commaSet(payroll.AcenterInsHealth); //건강보험센터부담
	document.getElementById('AcenterInsOldCare').value = __commaSet(payroll.AcenterInsOldCare); //노인장기요양센터부담
	document.getElementById('AcenterInsAnnuity').value = __commaSet(payroll.AcenterInsAnnuity); //국민연금센터부담
	document.getElementById('AcenterInsTotal').value   = __commaSet(payroll.AcenterInsTotal); //센터부담합계

	document.getElementById('realPay').value = __commaSet(payroll.realPay); //실지급액

	document.getElementById('incomeTax').value = __commaSet(payroll.incomeTax); //갑근세
	document.getElementById('resiTax').value   = __commaSet(payroll.resiTax); //주민세
	
	if (payroll.TaxfreePay() > 300000){
		alert('비과세 금액이 한도(30만원)을 초과하였습니다. 다시 입력하여 주십시오.');
		document.getElementById('foodPay').value = 0;
		document.getElementById('carPay').value = 0;
		calPay();
		document.getElementById('foodPay').focus();
		return;
	}
}

function calPay2(index){
	var year = document.getElementById('year').value;
	
	payroll.Init();
	payroll.type = document.getElementsByName('type[]')[index].value; //급여형식

	if (payroll.type != "3"){
		payroll.hourly    = __NaN(__commaUnset(document.getElementsByName('hourly[]')[index].value)); //시급입력
		payroll.minHourly = __NaN(__commaUnset(document.getElementsByName('minPay[]')[index].value)); //최소시급입력

		payroll.weekAppTime = __NaN(document.getElementsByName('weekAppTime[]')[index].value); //주소정근로시간
		payroll.workTime    = __NaN(document.getElementsByName('workTime[]')[index].value); //월주간근무시간
		payroll.prolongTime = __NaN(document.getElementsByName('prolongTime[]')[index].value); //월연장가산근로시간
		payroll.nightTime   = __NaN(document.getElementsByName('nightTime[]')[index].value); //월야간가산근로시간
		payroll.holidayTime = __NaN(document.getElementsByName('holidayTime[]')[index].value); //월휴일가산근로시간
		payroll.bathSudang  = __NaN(__commaUnset(document.getElementsByName('bathSudang[]')[index].value)); //목욕수당
		payroll.nursSudang  = __NaN(__commaUnset(document.getElementsByName('nursSudang[]')[index].value)); //간호수당
	}else{
		payroll.hourly    = 0; //시급입력
		payroll.minHourly = __NaN(__commaUnset(document.getElementsByName('minPay[]')[index].value)); //최소시급입력

		payroll.weekAppTime = 0; //주소정근로시간
		payroll.workTime    = 0; //월주간근무시간
		payroll.prolongTime = 0; //월연장가산근로시간
		payroll.nightTime   = 0; //월야간가산근로시간
		payroll.holidayTime = 0; //월휴일가산근로시간
		payroll.bathSudang  = 0; //목욕수당
		payroll.nursSudang  = 0; //간호수당
	}

	payroll.otherSudang = __NaN(__commaUnset(document.getElementsByName('otherSudang[]')[index].value)); //기타수당
	payroll.minusPay    = __NaN(__commaUnset(document.getElementsByName('minusSudang[]')[index].value)); //차감액

	payroll.prolongRate = __NaN(document.getElementById('prolongRate').value); //연장가산비율
	payroll.nightRate   = __NaN(document.getElementById('nightRate').value); //야간가산비율
	payroll.holidayRate = __NaN(document.getElementById('holidayRate').value); //휴일가산비율

	payroll.weekCount = __NaN(__commaUnset(document.getElementById('weekCount').value)); //월주일수
	payroll.workCount = __NaN(__commaUnset(document.getElementById('workCount').value)); //월근무가능일수

	payroll.foodPay = __NaN(__commaUnset(document.getElementsByName('foodPay[]')[index].value)); //식대
	payroll.carPay  = __NaN(__commaUnset(document.getElementsByName('carPay[]')[index].value)); //차량유지비

	payroll.workWeek    = payroll.WorkWeek(); //주휴시간
	payroll.workAppHour = payroll.WorkAppHour(); //월소정근로시간
	payroll.prolongHour = payroll.ProlongHour(); //연장가산근로시간
	payroll.nightHour   = payroll.NightHour(); //야간가산근로시간
	payroll.holidayHour = payroll.HolidayHour(); //휴일가산근로시간
	payroll.totalHour   = payroll.TotalHour(); //월총시간

	if (payroll.type != "3"){
		payroll.basePay  = payroll.BasePay();  //기본급
		payroll.totalPay = payroll.TotalPay(); //총급여
	}else{
		payroll.basePay  = __NaN(__commaUnset(document.getElementsByName('hourly[]')[index].value));  //기본급
		payroll.totalPay = payroll.TotalPay(); //총급여
	}

	payroll.prolongPay = payroll.ProlongPay(); //연장근무수당
	payroll.nightPay   = payroll.NightPay(); //야간근무수당
	payroll.holidayPay = payroll.HolidayPay(); //휴일근무수당

	payroll.bojeonPay = payroll.BojeonPay(); //보전수당

	payroll.totalSudang = payroll.TotalSudang(); //수당합계

	payroll.taxPay     = payroll.TaxPay(); //공제금액
	payroll.taxfreePay = payroll.TaxfreePay(); //비공제금액
	payroll.payTax     = payroll.PayTax(); //공제전금액

	payroll.insEmployYN  = document.getElementsByName('insEmployYN[]')[index].value; //고용보험
	payroll.insHealthYN  = document.getElementsByName('insHealthYN[]')[index].value; //건강보험
	payroll.insSanjeYN   = document.getElementsByName('insSanjeYN[]')[index].value; //산제보험
	payroll.insAnnuityYN = document.getElementsByName('insAnnuityYN[]')[index].value; //국민연금
	
	payroll.insEmploy  = (payroll.insEmployYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.mEmployRate)) : 0); //고용보험
	payroll.insHealth  = (payroll.insHealthYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.mHealthRate)) : 0); //건강보험
	payroll.insOldCare = (payroll.insHealthYN == 'Y' ? Math.floor(parseFloat(payroll.insHealth) * parseFloat(payroll.mOldCareRate)) : 0); //노인장기
	payroll.insAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.mAnnuityRate)) : 0); //국민연금
	payroll.insTotal   = parseFloat(payroll.insEmploy) + parseFloat(payroll.insHealth) + parseFloat(payroll.insOldCare) + parseFloat(payroll.insAnnuity); //합계

	payroll.centerInsEmploy  = (payroll.insEmployYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.cEmployRate)) : 0); //고용보험센터부담
	payroll.centerInsSanje   = (payroll.insSanjeYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.cSanjeRate)) : 0); //산제보험센터부담
	payroll.centerInsHealth  = (payroll.insHealthYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.cHealthRate)) : 0); //건강보험센터부담
	payroll.centerInsOldCare = (payroll.insHealthYN == 'Y' ? Math.floor(parseFloat(payroll.centerInsHealth) * parseFloat(payroll.cOldCareRate)) : 0); //노인장기보험센터부담
	payroll.centerInsAnnuity = (payroll.insAnnuityYN == 'Y' ? Math.floor(parseFloat(payroll.taxPay) * parseFloat(payroll.cAnnuityRate)) : 0); //국민연금센터부담
	payroll.centerInsTotal   = parseFloat(payroll.centerInsEmploy) + parseFloat(payroll.centerInsSanje) + parseFloat(payroll.centerInsHealth) + parseFloat(payroll.centerInsOldCare) + parseFloat(payroll.centerInsAnnuity); //센터부담함계

	if (payroll.totalPay > 0){
		var gongjeja = document.getElementsByName('gongjeja[]')[index].value; //공제가족수
		var gongjaye = document.getElementsByName('gongjaye[]')[index].value; //20세이하 자녀수

		payroll.incomeTax = getHttpRequest('../inc/_check_class.php?check=gapgeunse&year='+year+'&pay='+payroll.totalPay+'&deCnt='+gongjeja+'&chCnt='+gongjaye); //갑근세
	}else{
		payroll.incomeTax = 0; //갑근세
	}
	
	payroll.resiTax = cutOff(payroll.incomeTax * 0.1); //주민세
	payroll.realPay = payroll.RealPay();

	document.getElementsByName('totalHour[]')[index].value   = payroll.totalHour; //총근로시간
	document.getElementsByName('workAppHour[]')[index].value = payroll.workAppHour; //월소정근로시간
	document.getElementsByName('prolongHour[]')[index].value = payroll.prolongHour; //연장시간
	document.getElementsByName('nightHour[]')[index].value   = payroll.nightHour; //야간시간
	document.getElementsByName('holidayHour[]')[index].value = payroll.holidayHour; //휴일시간

	document.getElementsByName('totalPay[]')[index].value   = __commaSet(payroll.totalPay); //총급여
	document.getElementsByName('basePay[]')[index].value    = __commaSet(payroll.basePay); //기본급

	document.getElementsByName('bojeonPay[]')[index].value  = __commaSet(payroll.bojeonPay); //보전수당
	document.getElementsByName('prolongPay[]')[index].value = __commaSet(payroll.prolongPay); //연장근무수당
	document.getElementsByName('nightPay[]')[index].value   = __commaSet(payroll.nightPay); //야간근무수당
	document.getElementsByName('holidayPay[]')[index].value = __commaSet(payroll.holidayPay); //휴일근무수당

	document.getElementsByName('bathPay[]')[index].value = __commaSet(payroll.bathSudang); //목욕수당
	document.getElementsByName('nursPay[]')[index].value = __commaSet(payroll.nursSudang); //간호수당
	document.getElementsByName('otherPay[]')[index].value = __commaSet(payroll.otherSudang); //기타수당

	document.getElementsByName('minusPay[]')[index].value = __commaSet(payroll.minusPay); //차감액

	document.getElementsByName('sudangPay[]')[index].value = __commaSet(payroll.totalSudang); //수당합계

	document.getElementsByName('disTaxPay[]')[index].value     = __commaSet(payroll.taxPay); //과세총액
	document.getElementsByName('disTaxfreePay[]')[index].value = __commaSet(payroll.taxfreePay); //비과세총액
	document.getElementsByName('dedTotalPay[]')[index].value   = __commaSet(payroll.payTax); //공제전총액

	document.getElementsByName('insEmploy[]')[index].value  = __commaSet(payroll.insEmploy); //고용보험
	document.getElementsByName('insHealth[]')[index].value  = __commaSet(payroll.insHealth); //건강보험
	document.getElementsByName('insOldCare[]')[index].value = __commaSet(payroll.insOldCare); //노인장기
	document.getElementsByName('insAnnuity[]')[index].value = __commaSet(payroll.insAnnuity); //국민연금
	document.getElementsByName('insTotal[]')[index].value   = __commaSet(payroll.insTotal); //합계

	document.getElementsByName('centerInsEmploy[]')[index].value  = __commaSet(payroll.centerInsEmploy); //고용보험센터부담
	document.getElementsByName('centerInsSanje[]')[index].value   = __commaSet(payroll.centerInsSanje); //산제보험센터부담
	document.getElementsByName('centerInsHealth[]')[index].value  = __commaSet(payroll.centerInsHealth); //건강보험센터부담
	document.getElementsByName('centerInsOldCare[]')[index].value = __commaSet(payroll.centerInsOldCare); //노인장기요양센터부담
	document.getElementsByName('centerInsAnnuity[]')[index].value = __commaSet(payroll.centerInsAnnuity); //국민연금센터부담
	document.getElementsByName('centerInsTotal[]')[index].value   = __commaSet(payroll.centerInsTotal); //센터부담합계

	document.getElementsByName('realPay[]')[index].value = __commaSet(payroll.realPay); //실지급액

	document.getElementsByName('incomeTax[]')[index].value = __commaSet(payroll.incomeTax); //갑근세
	document.getElementsByName('resiTax[]')[index].value   = __commaSet(payroll.resiTax); //주민세
}
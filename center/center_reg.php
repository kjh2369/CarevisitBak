<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	$year = Date('Y');

	//방문요양
	$is_use_center[1] = $gHostSvc['homecare'];

	//바우처 사용여부
	$is_use_center[2] = $gHostSvc['voucher'];

	//시설 사용여부
	$is_use_center[3] = $gHostSvc['center'];


	// 최저시급
	$sql = "select g07_pay_time
			  from g07minpay
			 where g07_year = '".date('Y')."'";
	$min_hourly = $conn->get_data($sql);

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else if ($_SESSION["userLevel"] == "B"){
		$mCode = '';
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}



	$code = $mCode;
	$find_center_code = $_POST["find_center_code"];
	$find_center_name = $_POST["find_center_name"];
	$find_center_addr = $_POST["find_center_addr"];
	$page = $_POST["page"];

	if ($gDomain == 'kacold.net'){
		if ($gHostNm == '' || $gHostNm == 'www'){
			$IsCare = true;
		}else{
			$IsCare = false;
		}
	}else{
		$IsCare = true;
	}

	$IsCare = false;

	/*********************************************************

		공통항목

	*********************************************************/
	$sql = 'SELECT	meal_amt
			,		salary_yn
			,		deal_day
			,		sub_code
			,		deal_bipay_yn
			,		deal_hourly_yn
			,		deal_bipay_in
			,		deal_limit_yn
			,		ins60_yn
			,		work160h_over_yn
			,		deal_base_yn
			,		insu_gbn
			,		salary_svc_exec
			,		insu_hp_dis_yn
			,		sms_send_no
			,		salary_min_yn
			,		weekpay_gbn
			,		family_extra_pay_yn
			,		family_bipay_yn
			,		not_prolong, not_night, not_holiday, not_holiday_prolong, not_holiday_night
			,		year_commpay_yn, family_addpay_yn
			,		insu_sanje_yn
			,		insu_sanje_rate
			,		kacold_time_yn
			,		lsep_yn
			,		fa_income_expense
			,	    annual_pay_gbn
			FROM	center_comm
			WHERE	org_no = \''.$code.'\'';

	$row = $conn->get_array($sql);

	$liMealAmt		= $row['meal_amt'];
	$lsSalaryYn		= $row['salary_yn'];
	$dealDay		= IntVal($row['deal_day']);
	$lsSubCD		= $row['sub_code'];
	$lsDealBipayYn	= $row['deal_bipay_yn'];
	$lsDealHourlyYn	= $row['deal_hourly_yn'];
	$lsDealBipayIn	= $row['deal_bipay_in'];
	$lsDealLimitYn	= $row['deal_limit_yn'];
	$lsIns60Yn		= $row['ins60_yn'];
	$wrk160HOverYn	= $row['work160h_over_yn'];
	$lsDealBaseYn	= $row['deal_base_yn'];
	$insuGbn		= $row['insu_gbn']; //1자리 국민연금, 2자리 건강보험, 3자리 고용보험, 4자리 산재보험
	$salarySvcExec	= $row['salary_svc_exec']; //급여실행 할 서비스
	$insuHpDisYn	= $row['insu_hp_dis_yn']; //건강보험료 경감여부
	$smsSendNo		= $myF->phoneStyle($row['sms_send_no']);
	$salaryMinYn	= $row['salary_min_yn']; //최저임금 미적용여부
	$weekpayGbn		= $row['weekpay_gbn']; //주휴수당 계산 기준구분
	$familyExtraYn	= $row['family_extra_pay_yn']; //가족케어 수당처리여부
	$familyBipayYn	= $row['family_bipay_yn']; //가족케어 비급여 여부
	$notProlong = $row['not_prolong']; //추가수당 미지급 연장
	$notNight = $row['not_night']; //야간
	$notHoliday = $row['not_holiday']; //휴일
	$notHolidayProling = $row['not_holiday_prolong']; //휴일연장
	$notHolidayNight = $row['not_holiday_night']; //휴일야간
	$yearCommpayYn = $row['year_commpay_yn'] != '' ? $row['year_commpay_yn'] : 'Y'; //연차수당통상시급 적용여부
	$familyAddpayYn = $row['family_addpay_yn']; //가족케어 초과근무수당 적용여부
	$insuSjYn		= $row['insu_sanje_yn']; //산재보험률 적용여부
	$insuSjRate     = $row['insu_sanje_rate']; //산재보험률
	$kacoldTimeYn	= $row['kacold_time_yn']; //시작시간 시간으로표시(재가지원, 자원연계)
	$lsepYn         = $row['lsep_yn']; //공단비율 급여계산 시 근속수당 포함여부(체크 시 미포함)
	$annualPayGbn   = $row['annual_pay_gbn']; //연차수당지급구분

	if ($lsDealBipayYn == 'Y'){
		$lsDealType	= 'B';
	}else if ($lsDealHourlyYn == 'Y'){
		$lsDealType	= 'H';
	}else if ($lsDealHourlyYn == 'X'){
		$lsDealType	= 'X';
	}else{
		$lsDealType	= '';
	}

	$temp_ie = explode('/',$row['fa_income_expense']);

	//수입원
	if($temp_ie[0]!=''){
		$sql = 'select m02_yjumin as cd, m02_yname as nm
				from   m02yoyangsa
				where  m02_ccode = \''.$code.'\'
				and    m02_key = \''.$temp_ie[0].'\'
				group  by m02_yjumin';
		$income = $conn -> get_array($sql);
	}
	//지출원
	if($temp_ie[1]!=''){
		$sql = 'select m02_yjumin as cd, m02_yname as nm
				from   m02yoyangsa
				where  m02_ccode = \''.$code.'\'
				and    m02_key = \''.$temp_ie[1].'\'
				group  by m02_yjumin';
		$expense = $conn -> get_array($sql);
	}
	unset($row);



	$kind = $conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$mCode' and m00_del_yn = 'N'");
	$sql = "select *
			  from m00center
			 where m00_mcode  = '$mCode'
			   and m00_del_yn = 'N'
			 order by m00_mkind";

	$conn->query($sql);
	$row = $conn->fetch();
	$rowCount = $conn->row_count();

	// 요양기관구분
	$centerGubun[0] = 'N';
	$centerGubun[1] = 'N';
	$centerGubun[2] = 'N';

	// 바우처 구분
	$gubun_1[1] = 'N';
	$gubun_1[2] = 'N';
	$gubun_1[3] = 'N';
	$gubun_1[4] = 'N';

	if ($rowCount > 0){
		$editMode = false;

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($row['m00_mkind'] == $kind){
				$storeNm	= $row['m00_store_nm'];					//가맹점명
				$openDt		= $myF->dateStyle($row['m00_open_dt']);	//개설일자
				$faxNo		= $myF->phoneStyle($row['m00_fax_no']);	//팩스
				$cTel		= $myF->phoneStyle($row["m00_ctel"]);	//기관전화번호
				$cPostNo1	= subStr($row["m00_cpostno"], 0, 3);	//우편번호
				$cPostNo2	= subStr($row["m00_cpostno"], 3, 3);	//우편번호
				$cPostNo	= $row["m00_cpostno"];
				$cAddr1		= $row["m00_caddr1"];					//주소
				$cAddr2		= $row["m00_caddr2"];					//상세주소
				$mName		= $row["m00_mname"];					//대표자명
				$cCode		= $myF->bizStyle($row["m00_ccode"]);	//사업자번호
				$inwonsu	= number_format($row["m00_inwonsu"]);	//직원수
				$homepage	= $row["m00_homepage"];					//홈페이지
				$email		= $row['m00_email'];
				$kupyeo1	= $row["m00_kupyeo_1"];					//방문요양 서비스 제공여부
				$kupyeo2	= $row["m00_kupyeo_2"];					//방문목욕 서비스 제공여부
				$kupyeo3	= $row["m00_kupyeo_3"];					//방문간호 서비스 제공여부
				$contDate	= $myF->dateStyle($row['m00_cont_date'],'.');	//케어비지트 계약일자
				$startDate	= $myF->dateStyle($row['m00_start_date'],'.');	//시작일자

				$sudangRenew	= $row['m00_sudang_renew'];
				$sudangNight	= $row['m00_sudang_night'];
				$sudangHoliday	= $row['m00_sudang_holiday'];
				$sudangMonth	= $row['m00_sudang_month'];

				$law_holiday_yn     = $row['m00_law_holiday_yn'];
				$law_holiday_pay_yn = $row['m00_law_holiday_pay_yn'];

				$day_work_hour = $row['m00_day_work_hour'];
				$day_hourly    = $row['m00_day_hourly'];

				$icon  = $row['m00_icon'];
				$jikin = $row['m00_jikin'];

				if ($day_hourly == 0) $day_hourly = $min_hourly;


				$comNo      = $myF->issNo($row['m00_com_no']);
				$salaryDay  = $row['m00_salary_day'];  //급여 지급일자
				$weeklyInYN = $row['m00_weeklyin_yn']; //급여에 주휴 포함여부
				$annualYN   = $row['m00_annual_yn'];   //년차수당 지급여부
				$annualInYN = $row['m00_annualin_yn']; //년차수당 시급에 포함여부

				$fixedDays = $row['m00_fixed_days']; //소정근로일수
			}

			$code1[$row['m00_mkind']]	= $row['m00_code1'];	//승인번호
			$cName[$row['m00_mkind']]	= $row["m00_cname"];					//기관명칭

			$mKind[$row['m00_mkind']]	= $row['m00_mkind'];	//요양기관구분
			$jDate[$row['m00_mkind']]	= $myF->dateStyle($row["m00_jdate"]);	//사업개시일자

			$muksuYul1[$row['m00_mkind']]	= $row['m00_muksu_yul1'];	//목욕수당배분율(정)
			$muksuYul2[$row['m00_mkind']]	= $row['m00_muksu_yul2'];	//목욕수당배분율(부)

			if ($muksuYul1[$row['m00_mkind']]+$muksuYul2[$row['m00_mkind']] != 100){
				$muksuYul1[$row['m00_mkind']] = 50;
				$muksuYul2[$row['m00_mkind']] = 50;
			}

			// 은행정보
			$bankNo[$row['m00_mkind']]		= $row['m00_bank_no'];		//입금계좌번호
			$bankName[$row['m00_mkind']]	= $row['m00_bank_name'];	//입금은행
			$bankDepos[$row['m00_mkind']]	= $row['m00_bank_depos'];	//예금주

			// 목욕
			$bathBankNo[$row['m00_mkind']]		= $row['m00_bank_no_bath'];		//입금계좌번호
			$bathBankName[$row['m00_mkind']]	= $row['m00_bank_name_bath'];	//입금은행
			$bathBankDepos[$row['m00_mkind']]	= $row['m00_bank_depos_bath'];	//예금주

			// 간호
			$nursBankNo[$row['m00_mkind']]		= $row['m00_bank_no_nurse'];	//입금계좌번호
			$nursBankName[$row['m00_mkind']]	= $row['m00_bank_name_nurse'];	//입금은행
			$nursBankDepos[$row['m00_mkind']]	= $row['m00_bank_depos_nurse'];	//예금주

			//급여유형
			$salaryType[$row['m00_mkind']] = explode('/', $row['m00_salary_type']);


			$carNo1[$row['m00_mkind']]	= $row['m00_car_no1'];	//차량번호
			$carNo2[$row['m00_mkind']]	= $row['m00_car_no2'];	//차량번호
			$carNo3[$row['m00_mkind']]	= $row['m00_car_no3'];	//차량번호

			$bath_add_yn    = $row['m00_bath_add_yn'];		//목욕할증여부
			$nursing_add_yn = $row['m00_nursing_add_yn'];	//간호할증여부

			// 요양기관구분
			if ($row['m00_mkind'] == '0'){
				$centerGubun[0] = 'Y';
			}else if ($row['m00_mkind'] == '1' || $row['m00_mkind'] == '2' || $row['m00_mkind'] == '3' || $row['m00_mkind'] == '4'){
				$centerGubun[1] = 'Y';
				$gubun_1[$row['m00_mkind']] = 'Y';
			}else if ($row['m00_mkind'] == '5'){
				$centerGubun[2] = 'Y';
			}
		}

	}else{

		$editMode = true;

		$inwonsu = 0;
		$insName = 0;

		$sudangRenew	= 150;
		$sudangNight	= 150;
		$sudangHoliday	= 150;
		$sudangMonth	= 150;

		$muksuYul1[0] = 50;
		$muksuYul2[0] = 50;

		$bath_add_yn	= 'Y';
		$nursing_add_yn	= 'Y';

		$day_work_hour  = '2.5';
		$day_hourly     = $min_hourly;

		$weeklyInYN = 'Y';	//급여에 주휴 포함여부
		$annualInYN = 'Y';  //급여에 년차 포함여부
		$annualYN = 'Y';	//년차수당 지급여부

		$law_holiday_yn     = 'N';  //법정공휴일 인정여부
		$law_holiday_pay_yn = 'N';

	}

	$conn->row_free();

	if ($kupyeo1 != 'Y' && $kupyeo2 != 'Y' && $kupyeo3 != 'Y') $kupyeo1 = 'Y';

	if ($editMode){
		$title = '기관정보등록';
	}else{
		$title = '기관정보수정';
	}

	/***********************************************

		배상책임보험

	***********************************************/
		//보험사 리스트
		$sql = 'SELECT g01_code AS cd
				,      g01_name AS nm
				  FROM g01ins
				 ORDER BY g01_code';
		$laInsuMst = $conn->_fetch_array($sql,'cd');

		//기관 보험사
		$sql = 'SELECT svc_cd
				,      seq
				,      insu_cd
				,      from_dt
				,      to_dt
				  FROM insu_center
				 WHERE org_no = \''.$mCode.'\'
				 ORDER BY svc_cd, seq';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$laInsu[$row['svc_cd']] = Array(
				'seq'=>$row['seq']
			,	'code'=>$row['insu_cd']
			,	'from'=>$row['from_dt']
			,	'to'=>$row['to_dt']);
		}

		$conn->row_free();

		/*
		$sql = "select g02_ins_code, g02_mkind, g02_ins_from_date, g02_ins_to_date
				  from g02inscenter
				 where g02_ccode = '$mCode'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			@$insName[$row['g02_mkind']]     = $row['g02_ins_code'];
			@$insFromDate[$row['g02_mkind']] = $row['g02_ins_from_date'];
			@$insToDate[$row['g02_mkind']]   = $row['g02_ins_to_date'];
		}

		$conn->row_free();
		*/
	/***********************************************/



	/***********************************************

		로그인정보

	***********************************************/
		$logID = $_SESSION['userCode'];
		//$pass  = $log_info['m97_pass'];
	/***********************************************/

	$button  = "";

	if ($_SESSION["userLevel"] == "A"){
		$button .= "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_list_center($page);'>리스트</button></span> ";
	}
	if ($editMode){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_center_save();'>등록</button></span> ";
	}else{
		if ($_SESSION["userLevel"] == "A"){
			//$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick='_centerDelete();'>삭제</button></span>";
		}else{
			$button .= "<span class='btn_pack m icon'><span class='save'></span><button type='button' onFocus='this.blur();' onClick='_center_save();'>저장</button></span> ";
		}
	}

	// 은행 리스트
	$bankList= $definition->GetBankList();
	$bankListCount = sizeOf($bankList);

	//연락처
	$sql = 'SELECT	mobile
			FROM	mst_manager
			WHERE	org_no = \''.$code.'\'';

	$mobile = $conn->get_data($sql);


	//홈페이지정보
	$sql = 'SELECT domain
			FROM   homepage_mg
			WHERE  org_no = \''.$code.'\'
			AND date_format(now(),\'%Y%m%d\') >= date_format(join_dt,\'%Y%m%d\')
			AND date_format(now(),\'%Y%m%d\') <= date_format(quit_dt,  \'%Y%m%d\')';
	$hpage = $conn->get_data($sql);

	//기관정보에서 등록된 홈페이지주소가 없으면 홈페이지관리(homepage_mg) 데이블에서 가져옴.
	$homepage = $homepage != '' ? $homepage : $hpage;

	if (StrPos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') > -1){
		//echo '호환성 사용';
	}else{
		//echo '호환성 미사용';
	}
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--

if (window.navigator.userAgent.search("MSIE 7") > -1){
	//호환성보기 설정 사용
}else if (window.navigator.userAgent.search("MSIE 7") < 0){
	//호환성보기 설정 미사용
}


var is_m = null;

window.onload = function(){
	is_m = document.getElementById('is_m').value;

	if (document.getElementById('kind_cnt_0').value == 0)
		set_kind('0', document.getElementById('kind_1').checked);
	else
		set_kind('0', true);

	if (document.getElementById('kind_cnt_1').value == 0)
		set_kind('1', document.getElementById('kind_2_1').checked);
	else
		set_kind('1', true);

	if (document.getElementById('kind_cnt_2').value == 0)
		set_kind('2', document.getElementById('kind_2_2').checked);
	else
		set_kind('2', true);

	if (document.getElementById('kind_cnt_3').value == 0)
		set_kind('3', document.getElementById('kind_2_3').checked);
	else
		set_kind('3', true);

	if (document.getElementById('kind_cnt_4').value == 0)
		set_kind('4', document.getElementById('kind_2_4').checked);
	else
		set_kind('4', true);

	__init_form(document.f);

	if (is_m == 'NO'){
		try{
			set_ins_from_to_dt('0', document.getElementById('insName0').value);
			set_ins_from_to_dt('1', document.getElementById('insName1').value);
			set_ins_from_to_dt('2', document.getElementById('insName2').value);
			set_ins_from_to_dt('3', document.getElementById('insName3').value);
			set_ins_from_to_dt('4', document.getElementById('insName4').value);
		}catch(e){
		}
	}

	set_care_service(2);
	set_care_service(3);

	if($('#chkInsuSjYn').is(':checked')==true){
		__setEnabled(document.f.insuSjRate, true);
	}else {
		__setEnabled(document.f.insuSjRate, false);
	}

	//년차수당 지급여부 활성화
	//$('#annualYN').attr('disabled', $('#annualInYN').attr('checked'));
}

function set_kind(svc_id, use){
	var svc = document.getElementById('svc_tbl_'+svc_id);
	var div = document.getElementById('svc_div_'+svc_id);
	var tmp = null;
	var idx = null;

	if (($('#kind_1').attr('type') == 'checkbox' && $('#kind_1').attr('checked')) || ($('#kind_1').attr('type') == 'hidden' && $('#kind_1').attr('value') == 'Y'))
		idx = 0;
	else if (($('#kind_2_1').attr('type') == 'checkbox' && $('#kind_2_1').attr('checked')) || ($('#kind_2_1').attr('type') == 'hidden' && $('#kind_2_1').attr('value') == 'Y'))
		idx = 1;
	else if (($('#kind_2_2').attr('type') == 'checkbox' && $('#kind_2_2').attr('checked')) || ($('#kind_2_2').attr('type') == 'hidden' && $('#kind_2_2').attr('value') == 'Y'))
		idx = 2;
	else if (($('#kind_2_3').attr('type') == 'checkbox' && $('#kind_2_3').attr('checked')) || ($('#kind_2_3').attr('type') == 'hidden' && $('#kind_2_3').attr('value') == 'Y'))
		idx = 3;
	else if (($('#kind_2_4').attr('type') == 'checkbox' && $('#kind_2_4').attr('checked')) || ($('#kind_2_4').attr('type') == 'hidden' && $('#kind_2_4').attr('value') == 'Y'))
		idx = 4;
	else
		idx = 5;

	for(var i=0; i<=4; i++){
		tmp = document.getElementById('svc_tbl_'+i);
		tmp.style.marginBottom = 0;
	}

	if (use){
		div.style.position = '';
		div.style.width    = '50%';
		svc.style.position = '';
		svc.style.width    = '100%';
	}else{
		div.style.position = 'absolute';
		div.style.width    = 0;
		svc.style.position = 'absolute';
		svc.style.width    = 0;
	}

	/*
	if (is_m != 'NO') return;

	var comm = document.getElementById('tbl_comm');
	var h    = comm.offsetHeight;

	if (svc_id == '0' && !use){
		var tmp = null;

		for(var i=1; i<=4; i++){
			tmp = document.getElementById('svc_tbl_'+i);

			if (tmp != null){
				if (tmp.style.position == ''){
					tmp.style.marginBottom = h - tmp.offsetHeight;
					break;
				}
			}
		}
	}else{
		var tmp = null;

		tmp = document.getElementById('svc_tbl_0');

		if (tmp.style.position != ''){
			var tmp = null;

			for(var i=1; i<=4; i++){
				tmp = document.getElementById('svc_tbl_'+i);

				if (tmp != null){
					if (tmp.style.position == ''){
						tmp.style.marginBottom = h - tmp.offsetHeight;
						break;
					}
				}
			}
		}

		var center_nm = document.getElementById('cName'+svc_id);

		if (__replace(center_nm.value, ' ', '') == ''){
			center_nm.value = document.getElementById('storeNm').value;
		}
	}
	*/

	/*
	for(var i=0; i<=4; i++){
		if (idx == i){
			var h = $('#tbl_comm').height() - $('#svc_div_'+idx).height() + 5;

			if (h < 0) h = 0;

			$('#svc_div_'+idx).css('margin-bottom', h+'px');
		}else{
			$('#svc_div_'+i).css('margin-bottom', '0');
		}
	}
	*/

	if (idx != 0){
		var h = $('#tbl_comm').height()+25;

		$('#svc_div_0').css('position', '').css('width', '50%').css('height', h).show();
		$('#svc_tbl_0').hide();
	}
}

function set_ins_from_to_dt(svc_id, val){
	var val = (val == '' ? false : true);

	__setEnabled('insFromDate'+svc_id, val);
	__setEnabled('insToDate'+svc_id, val);
}

function set_holiday_pay_yn(gubun){
	var holiday_yn     = __get_value(document.getElementsByName(gubun+'_holiday_yn'));
	var holiday_pay_yn = document.getElementsByName(gubun+'_holiday_pay_yn');

	if (holiday_yn == 'Y'){
		holiday_pay_yn[0].disabled = false;
		holiday_pay_yn[1].disabled = false;
	}else{
		holiday_pay_yn[1].checked  = true;
		holiday_pay_yn[0].disabled = true;
		holiday_pay_yn[1].disabled = true;
	}
}

function set_show_tbody_layer(id, len, show){
	for(var i=1; i<=len; i++)
		_show_tbody_layer('tbody_row_'+id+'_'+i, 'tbody_row_layer_'+id+'_'+i, show);
}

function set_care_service(id){
	var care = document.getElementById('kupyeo'+id);

	if (id == 2){
		__setEnabled('sudangYul1', care.checked);
		__setEnabled('sudangYul2', care.checked);

		__setEnabled('carNo1', care.checked);
		__setEnabled('carNo2', care.checked);
		__setEnabled('carNo3', care.checked);


		if($('#kupyeo2').is(':checked')==true){
			$('#bathBankName').show();
			$('#bathBankNo').show();
			$('#bathBankDep').show();
			$('#bathSalaryType').show();
		}else {
			$('#bathBankName').hide();
			$('#bathBankNo').hide();
			$('#bathBankDep').hide();
			$('#bathSalaryType').hide();
		}


	}else if (id == 3){

		if($('#kupyeo3').is(':checked')==true){
			$('#nursBankName').show();
			$('#nursBankNo').show();
			$('#nursBankDep').show();
			$('#nursSalaryType').show();
		}else {
			$('#nursBankName').hide();
			$('#nursBankNo').hide();
			$('#nursBankDep').hide();
			$('#nursSalaryType').hide();
		}

	}
}

function set_login_id(){
	var code = document.getElementById('mCode');
	var ssn  = document.getElementById('logID');

	if (code.value.split(' ').join('') == ''){
		alert('기관기호를 입력하여 주십시오.');
		code.focus();
		return false;
	}

	if (ssn == '') ssn = code;

	var result = getHttpRequest('../inc/_chk_ssn.php?id=10&code='+code.value+'&ssn='+ssn.value);

	if (result == 'Y'){
		alert('입력하신 아이디는 존재하는 아이디 입니다. 다른 아이디를 입력하여 주십시오.');
		ssn.value = '';
		return false;
	}

	return true;
}

function show_icon(obj, view){
	if (!__checkImageExp2(obj)){
		return;
	}

	var icon_view = document.getElementById(view+'_view');
	var icon_img  = document.getElementById(view+'_img');
	var path = __get_file_path(obj);

	icon_view.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='file://"+path+"', sizingMethod='scale')";
	icon_img.style.display = 'none';
}

function load_icon(obj){
	try{
		var w = obj.width;
		var h = obj.height;
		var r = 1;

		if (w > 20 || h > 20){
			if (w > h){
				r = h / w;
			}else{
				r = w / h;
			}

			if (w > 20){
				obj.width  = 20;
				obj.height = 20 * r;
			}else{
				obj.height = 20;
				obj.width  = 20 * r;
			}
		}
	}catch(e){
	}
}


/*********************************************************

	전 직원의 소정근로 정보를 수정한다.

*********************************************************/
function setMemFxiedTimePay(){
	var dt   = new Date();
	var time = $('#fixedHour').attr('value');
	var pay  = $('#fixedHourly').attr('value');

	if (!confirm('** 전 직원의 "'+dt.getFullYear()+'년도" 소정근로 시간/시급이 일괄적용됩니다. **\n\n - 직원의 시간 및 시급이 변경될 시간 및 시급보다 많으면 변경되지 않습니다.\n\n - 전 직원의 소정근로 시간/시급을 변경하시려면 "확인"을 클릭하여 주십시오.')) return;

	try{
		$.ajax({
			type: 'POST'
		,	url : './center_fixed_app.php'
		,	data: {
				'code':$('#code').attr('value')
			,	'time':time
			,	'pay' :pay
			}
		,	beforeSend: function(){}
		,	success   : function(result){
				if (result == 'ok'){
					alert('정상적으로 저장되었습니다.');
				}else if (result == 'error'){
					alsert('저장 중 오류가 발생하였습니다.\n\n잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error     : function(){}
		}).responseXML;
	}catch(e){
	}
}

// 2012. 4. 27 지사 기관연결 추가
function _b2cCenterAdd(p_center, p_kind){
	var center = (p_center != undefined ? p_center : '');
	var kind = (p_kind != undefined ? p_kind : '');

	var width  = 800;
	var height = 340;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	var URL = '../branch/b2c_center_add.php?center='+center+'&kind='+kind;
	var popup = window.open(URL,'centerAdd','width='+width+',height='+height+',left='+left+',top='+top+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no');
}


// 선택직원삭제
function clear_mem(cd, nm){
	var obj_cd = __getObject(cd);
	var obj_nm = __getObject(nm);

	obj_cd.value = '';
	obj_nm.value = '';
}

//-->
</script>
<form name="f" method="post" enctype="multipart/form-data">

<div class="title title_border">
	<div style="float:left; width:auto;"><?=$title;?></div>
	<div style="float:right; width:auto; margin-top:6px;"><button type='button' onClick='_center_save();'>저장</button></div>
</div>
<?
	/*********************************************************
		2012. 4. 27 기관연결버튼 추가
	**********************************************************/

	if($rowCount > 0){
		if ($_SESSION['userLevel'] == 'A'){
			echo '<div align="right" class="title_border" style="margin-top:1px;"><span class="btn_pack m"><button onFocus="this.blur();" onClick="_b2cCenterAdd('.$code.','.$kind.');">기관연결</button></span></div>';
		}
	}


	/******************************************************************

		기관 기본정보

	******************************************************************/
		include_once('center_reg_info.php');
	/******************************************************************/




	/******************************************************************

		기관구분 선택

	******************************************************************/
		if ($IsCare){
			echo '<div style=\'padding:10px 10px 5px 10px;\'>';
		}else{
			echo '<div style=\'display:none;\'>';
		}
		include_once('./center_reg_kind.php');
		echo '</div>';
	/******************************************************************/

	echo '<div style=\'clear:both;\'>';

	/******************************************************************

		공통항목 //position:absolute; top:0; left:-10000;

	******************************************************************/
		echo '<div style=\'width:50%; float:left; padding:10px;\'>';
		//include_once('./center_reg_comm.php');
		echo '</div>';
	/******************************************************************/


	/******************************************************************

		재가요양

	******************************************************************/
		$__SVC_ID__ = '0';
		include('./center_reg_service.php');
	/******************************************************************/


	/******************************************************************

		바우처 - 가사간병

	******************************************************************/
		$__SVC_ID__ = '1';
		include('./center_reg_service.php');
	/******************************************************************/


	/******************************************************************

		바우처 - 노인돌봄

	******************************************************************/
		$__SVC_ID__ = '2';
		include('./center_reg_service.php');
	/******************************************************************/


	/******************************************************************

		바우처 - 산모신생아

	******************************************************************/
		$__SVC_ID__ = '3';
		include('./center_reg_service.php');
	/******************************************************************/


	/******************************************************************

		바우처 - 장애인활동보조

	******************************************************************/
		$__SVC_ID__ = '4';
		include('./center_reg_service.php');
	/******************************************************************/


	echo '</div>';?>

	<div style="clear:both; padding:10px 0;">
		<table class="my_table my_border_blue" style="width:100%;">
			<colgroup>
				<col>
			</colgroup><?
			if ($IsCare){?>
				<!--thead>
					<tr>
						<th class="head bold">처우개선비 처리방법 선택</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<div class="bold">
								<input id="optDealIsExtra" name="chkDealIsType" type="radio" value="" class="radio" <?=$lsDealType == '' ? 'checked' : '';?>><label for="optDealIsExtra">처우개선비 수당처리(기본)</label><br>
								<input id="optDealIsBipay" name="chkDealIsType" type="radio" value="B" class="radio" <?=$lsDealType == 'B' ? 'checked' : '';?>><label for="optDealIsBipay">처우개선비 비급여처리(업무수당으로 적용)</label><br>
								<input id="optDealIsHourly" name="chkDealIsType" type="radio" value="H" class="radio" <?=$lsDealType == 'H' ? 'checked' : '';?>><label for="optDealIsHourly">처우개선비 시급(급여)에 포함(처우개선비를 포함한 시급(급여)을 입력)</label><br>
								<input id="optDealIsNot" name="chkDealIsType" type="radio" value="X" class="radio" <?=$lsDealType == 'X' ? 'checked' : '';?>><label for="optDealIsNot">처우개선비 계산하지 않음</label><br>
							</div>
						</td>
					</tr>
					<tr>
						<td class="bold">
							<div>
								<input id="chkDealInBipay" name="chkDealInBipay" type="checkbox" value="Y" class="checkbox" <?=$lsDealBipayIn == 'Y' ? 'checked' : '';?>><label for="chkDealInBipay">처우개선비에 비급여일정도 포함.</label>
							</div>
							<div>
								<div style="float:left; width:auto;">
									<input id="chkDealLimitYn" name="chkDealLimitYn" type="checkbox" value="Y" class="checkbox" <?=$lsDealLimitYn == 'Y' ? 'checked' : '';?>><label for="chkDealLimitYn">처우개선비 한도(<span style="color:#ff0000;">100,000원</span>)를 설정함.</label>
								</div>
								<div style="float:left; width:auto;">
									(<input id="chkDealLimitApply" name="chkDealLimitApply" type="checkbox" value="Y" class="checkbox"><label for="chkDealLimitApply">전직원적용</label>)
								</div>
							</div><?
							if ($debug){?>
								<div>
									<input id="chkDealInBase" name="chkDealInBase" type="checkbox" value="Y" class="checkbox" <?=$lsDealBaseYn == 'Y' ? 'checked' : '';?>><label for="chkDealInBase">처우개선비를 최저임금에 적용(TEST)</label>
								</div><?
							}?>
						</td>
					</tr>
				</tbody--><?
			}

			if ($IsCare){?>
				<thead>
					<tr>
						<th class="head bold">급여관리</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold" style="padding:5px 5px 10px 0;">
							<div class="left">※<span style="color:RED;">급여계산</span>을 <span style="color:RED;">실행할 서비스</span>를 선택하여 주십시오.</div>
							<div style="margin-left:13px;"><?
								parse_str($salarySvcExec, $salarySvcExec);

								if ($salarySvcExec['homecare'] != 'N') $salarySvcExec['homecare'] = 'Y';
								if ($salarySvcExec['nurse'] != 'N') $salarySvcExec['nurse'] = 'Y';
								if ($salarySvcExec['old'] != 'N') $salarySvcExec['old'] = 'Y';
								if ($salarySvcExec['baby'] != 'N') $salarySvcExec['baby'] = 'Y';
								if ($salarySvcExec['dis'] != 'N') $salarySvcExec['dis'] = 'Y';

								if ($gHostSvc['homecare']){
									//재가요양?>
									<label><input id="chkSalaryUse0" name="chkSalaryUse0" type="checkbox" class="checkbox" value="Y" <?=$salarySvcExec['homecare'] == 'Y' ? 'checked' : '';?>>재가요양</label><?
								}

								if ($gHostSvc['nurse']){
									//가사간병?>
									<label><input id="chkSalaryUse1" name="chkSalaryUse1" type="checkbox" class="checkbox" value="Y" <?=$salarySvcExec['nurse'] == 'Y' ? 'checked' : '';?>>가사간병</label><?
								}

								if ($gHostSvc['old']){
									//노인돌봄?>
									<label><input id="chkSalaryUse2" name="chkSalaryUse2" type="checkbox" class="checkbox" value="Y" <?=$salarySvcExec['old'] == 'Y' ? 'checked' : '';?>>노인돌봄</label><?
								}

								if ($gHostSvc['baby']){
									//산모신생아?>
									<label><input id="chkSalaryUse3" name="chkSalaryUse3" type="checkbox" class="checkbox" value="Y" <?=$salarySvcExec['baby'] == 'Y' ? 'checked' : '';?>>산모신생아</label><?
								}

								if ($gHostSvc['dis']){
									//장애인활동지원?>
									<label><input id="chkSalaryUse4" name="chkSalaryUse4" type="checkbox" class="checkbox" value="Y" <?=$salarySvcExec['dis'] == 'Y' ? 'checked' : '';?>>장애인활동지원</label><?
								}?>
							</div>
							<div class="left">- 체크를 제거한 서비스는 급여계산을 실행하지 않습니다.</div><?
							if ($debug || $gDomain == 'dolvoin.net'){?>
								<div class="left13">
									<label><input id="chkFamilyExtraPayYn" name="chkFamilyExtraPayYn" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$familyExtraYn == 'Y' ? 'checked' : '';?>>가족케어 급여를 수당으로 처리합니다.(가족케어 급여와 처우개선비를 수당으로 처리함.)</label><br>
									<label><input id="chkFamilyBipayYn" name="chkFamilyBipayYn" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$familyBipayYn == 'Y' ? 'checked' : '';?>>가족케어수당을 비과세로 처리합니다.</label>
								</div><?
							}?>

							<div class="left13">
								<label><input id="chkCommPayYn" name="chkCommPayYn" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$yearCommpayYn == 'Y' ? 'checked' : '';?>>연차수당 계산시 통상시급으로 적용합니다.(2017년 1월부터 적용)</label>
							</div>

							<div class="left" style="margin-top:20px;">※선택한 항목의 초과근무수당을 지급하지 않습니다.(미지급시 근로기준법에 의해 법칙금이 부과될 수 있습니다.)</div>
							<div class="left13">
								<label><input id="chkNotProlong" name="chkNotProlong" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$notProlong == 'Y' ? 'checked' : '';?>>연장수당</label>
								<label><input id="chkNotNight" name="chkNotNight" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$notNight == 'Y' ? 'checked' : '';?>>야간수당</label>
								<label><input id="chkNotHoliday" name="chkNotHoliday" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$notHoliday == 'Y' ? 'checked' : '';?>>휴일수당</label>
								<label><input id="chkNotHolidayProlong" name="chkNotHolidayProlong" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$notHolidayProling == 'Y' ? 'checked' : '';?>>휴일연장수당</label>
								<label><input id="chkNotHolidayNight" name="chkNotHolidayNight" type="checkbox" type="checkbox" class="checkbox" value="Y" <?=$notHolidayNight == 'Y' ? 'checked' : '';?>>휴일야간수당</label><br>
							</div>
							<!--div style="margin-left:13px;">
								<label><input id="chkFamAddPayYn" name="chkFamAddPayYn" type="checkbox" type="checkbox" class="checkbox" value="N" <?=$familyAddpayYn == 'N' ? 'checked' : '';?>>동거가족요양보호사의 초과근무수당을 지급하지 않습니다.(2017년 1월부터 적용)</label>
							</div-->

						</td>
					</tr>
					<tr>
						<td>
							<div class="bold" style="padding-left:5px;">
								※ 급여계산시 최저임금 적용하지 않습니다.
								<label><input name="chkSalaryMinYn" type="radio" class="radio" value="Y" <?=$salaryMinYn == 'Y' ? 'checked' : '';?>>예</label>
								<label><input name="chkSalaryMinYn" type="radio" class="radio" value="N" <?=$salaryMinYn != 'Y' ? 'checked' : '';?>>아니오</label>
							</div>
							<div class="bold" style="padding-left:5px;">
								※ 최저임금 미적용여부는 기관의 자율적인 판단에 의한 것입니다.
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="bold" style="padding-left:5px;">
								※ 주휴수당 계산시
								(<label><input name="optWeekPayGbn" type="radio" class="radio" value="1" <?=$weekpayGbn == '1' ? 'checked' : '';?>>월 근무가능시간</label> /
								<label><input name="optWeekPayGbn" type="radio" class="radio" value="2" <?=$weekpayGbn != '1' ? 'checked' : '';?>>근무시간</label> )
								으로 계산합니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class="bold" style="padding:5px 5px 10px 0;">
							<div class="left">
								1.국민연금 계산 기준 금액 :<label><input id="optInsu1_1" name="optInsu1" type="radio" class="radio" value="1" <?=$insuGbn[0] == '1' ? 'checked' : '';?>>과세금액</label>
								<label><input id="optInsu1_2" name="optInsu1" type="radio" class="radio" value="2" <?=$insuGbn[0] == '2' ? 'checked' : '';?>>보수신고급여</label> (미선택시 "보수신고급여")
							</div>
							<div class="left">
								2.건강보험 계산 기준 금액 :<label><input id="optInsu2_1" name="optInsu2" type="radio" class="radio" value="1" <?=$insuGbn[1] == '1' ? 'checked' : '';?>>과세금액</label>
								<label><input id="optInsu2_2" name="optInsu2" type="radio" class="radio" value="2" <?=$insuGbn[1] == '2' ? 'checked' : '';?>>보수신고급여</label> (미선택시 "과세금액")
							</div>
							<div class="left">
								3.고용보험 계산 기준 금액 :<label><input id="optInsu3_1" name="optInsu3" type="radio" class="radio" value="1" <?=$insuGbn[2] == '1' ? 'checked' : '';?>>과세금액</label>
								<label><input id="optInsu3_2" name="optInsu3" type="radio" class="radio" value="2" <?=$insuGbn[2] == '2' ? 'checked' : '';?>>보수신고급여</label> (미선택시 "과세금액")
							</div>
							<div class="left">
								4.산재보험 계산 기준 금액 :<label><input id="optInsu4_1" name="optInsu4" type="radio" class="radio" value="1" <?=$insuGbn[3] == '1' ? 'checked' : '';?>>과세금액</label>
								<label><input id="optInsu4_2" name="optInsu4" type="radio" class="radio" value="2" <?=$insuGbn[3] == '2' ? 'checked' : '';?>>보수신고급여</label> (미선택시 "과세금액")</br><font color="red"> ※산재보험 2018년까지 보수총액, 2019년부터 과세금액 적용</font>
							</div>
							<div class="left" style="margin-top:10px;">
								<label><input id="chkInsuHpDis" name="chkInsuHpDis" type="checkbox" class="checkbox" value="Y" <?=$insuHpDisYn == 'Y' ? 'checked' : '';?>>도서산간지역 건강보험 경감설정(50%)</label>
							</div>
							<div class="left" style="margin-top:10px;">
								<label><input id="chkInsuSjYn" name="chkInsuSjYn" type="checkbox" class="checkbox" value="Y" <?=$insuSjYn == 'Y' ? 'checked' : '';?> onclick="if($(this).is(':checked')==true){__setEnabled(document.f.insuSjRate, true);}else {__setEnabled(document.f.insuSjRate, false);};"></label>산재보험률(<input id="insuSjRate" name="insuSjRate" type="text" value="<?=$insuSjRate;?>" style="width:40px;" >% ) ※ 예 : <font color="red">1000 분의 67</font> 이면 <font color="red">0.67</font> (으)로 입력,  미입력 시 "<font color="red">기준요율</font>" 적용
							</div>
						</td>
					</tr>
					<tr>
						<td class="bold">
							<input id="chkIns60Yn" name="chkIns60Yn" type="checkbox" value="Y" class="checkbox" <?=$lsIns60Yn == 'Y' ? 'checked' : '';?>><label for="chkIns60Yn">월근무시간이 60시간미만인 근로자 4대보험 적용</label><br>
							<label><input id="chkWrk160HOverYn" name="chkWrk160HOverYn" type="checkbox" value="Y" class="checkbox" <?=$wrk160HOverYn == 'Y' ? 'checked' : '';?>>160시간(월 근무가능시간)이상 근로시간을 연장근로시간으로 적용</label><br>
							<label><input id="chkLsepYn" name="chkLsepYn" type="checkbox" value="Y" class="checkbox" <?=$lsepYn == 'Y' ? 'checked' : '';?>>직원급여설정을 공단비율로 계산 시 공단비율에 장기근속수당 미포함(미체크 시 포함)</label>
						</td>
					</tr>
					<tr>
						<td class="bold"><?
							//10인미만 사업장 보험지원
							include('./ins_support.php');?>
						</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th class="head bold">재무회계</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bold left iw150">
						수입/지출결의서 (
						수입원 :
						<?
							echo '<input name=\'fa_mem_cd1\' type=\'hidden\' value=\''.$ed->en($income['cd']).'\' tag=\''.$ed->en($income['cd']).'\'>';
							echo '<input name=\'fa_mem_nm1\' type=\'text\'   value=\''.$income['nm'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
							echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("fa_mem_cd1"),document.getElementById("fa_mem_nm1")); check_partner("fa_mem_cd1","fa_partner");\'></span>';
							echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("fa_mem_cd1","fa_mem_nm1"); check_partner("fa_mem_cd1","fa_partner");\'>삭제</button></span>';

						?>

						지출원 :
						<?
							echo '<input name=\'fa_mem_cd2\' type=\'hidden\' value=\''.$ed->en($expense['cd']).'\' tag=\''.$ed->en($expense['cd']).'\'>';
							echo '<input name=\'fa_mem_nm2\' type=\'text\'   value=\''.$expense['nm'].'\' style=\'background-color:#eeeeee; margin-top:3px;\' readOnly> ';
							echo '<span class=\'btn_pack find\' style=\'margin-top:1px; margin-left:-5px;\' onclick=\'__find_yoyangsa("'.$code.'","0",document.getElementById("fa_mem_cd2"),document.getElementById("fa_mem_nm2"));\'></span>';
							echo '<span class=\'btn_pack m\' style=\'margin-top:2px;\'><button type=\'button\' onclick=\'clear_mem("fa_mem_cd2","fa_mem_nm2");\'>삭제</button></span>';
						?>
						)
						</td>
					</tr>
				</tbody>
				<thead>
					<tr>
						<th class="head bold">기타</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<div class="bold">
								<input id="chkSalaryYn" name="chkSalaryYn" type="checkbox" value="Y" class="checkbox" <?=$lsSalaryYn == 'Y' ? 'checked' : '';?>><label for="chkSalaryYn">직원업무보기 프로그램에서 급여관리 출력여부</label>
							</div>
							<!--div class="bold">
								<input id="chkDealIsBipay" name="chkDealIsBipay" type="checkbox" value="Y" class="checkbox" <?=$lsDealBipayYn == 'Y' ? 'checked' : '';?>><label for="chkDealIsBipay">처우개선비 비급여처리(업무수당으로 적용)</label>
							</div--><?
							if($debug){ ?>
							<div class="bold">
								<input id="chkKacoldTimeYn" name="chkKacoldTimeYn" type="checkbox" value="Y" class="checkbox" <?=$kacoldTimeYn == 'Y' ? 'checked' : '';?>><label for="chkKacoldTimeYn">일정등록(재가지원,자원연계)에서 시작시간 시간으로 표시 (미체크 시 "오전,오후표시") </label>
							</div><?
							} ?>
						</td>
					</tr>
				</tbody><?
			}?>
		</table>

		<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
			<colgroup>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head bold">결재란 설정</th>
				</tr>
			</thead>
			<tbody>
				<tr><?
					$sql = 'SELECT	*
							FROM	signline_set
							WHERE	org_no	= \''.$code.'\'';
					$signline = $conn->get_array($sql);
					$signname = Explode('|',$signline['subject']);?>
					<td class="bold left">
						결재란수 :
						<select id="cboSignCnt" name="cboSignCnt" style="width:auto;">
							<option value="">기본</option>
							<option value="0" <?=$signline['line_cnt'] == '0' ? 'selected' : '';?>>없음</option>
							<option value="1" <?=$signline['line_cnt'] == '1' ? 'selected' : '';?>>1</option>
							<option value="2" <?=$signline['line_cnt'] == '2' ? 'selected' : '';?>>2</option>
							<option value="3" <?=$signline['line_cnt'] == '3' ? 'selected' : '';?>>3</option>
							<option value="4" <?=$signline['line_cnt'] == '4' ? 'selected' : '';?>>4</option>
							<option value="5" <?=$signline['line_cnt'] == '5' ? 'selected' : '';?>>5</option>
						</select>
						서명란 :
						<input id="txtSign1" name="txtSign1" type="text" value="<?=$signname[0];?>" style="width:70px;">,
						<input id="txtSign2" name="txtSign2" type="text" value="<?=$signname[1];?>" style="width:70px;">,
						<input id="txtSign3" name="txtSign3" type="text" value="<?=$signname[2];?>" style="width:70px;">,
						<input id="txtSign4" name="txtSign4" type="text" value="<?=$signname[3];?>" style="width:70px;">,
						<input id="txtSign5" name="txtSign5" type="text" value="<?=$signname[4];?>" style="width:70px;">
					</td><?
					Unset($signline);?>
				</tr>
			</tbody>
		</table>
	</div>

<input name="bath_add_yn"    type="hidden" value="Y">
<input name="nursing_add_yn" type="hidden" value="Y">

<input name="sudang_renew"	type="hidden" value="<?=$sudangRenew;?>">
<input name="sudang_night"	type="hidden" value="<?=$sudangNight;?>">
<input name="sudang_holiday"type="hidden" value="<?=$sudangHoliday;?>">
<input name="sudang_month"	type="hidden" value="<?=$sudangMonth;?>">

<input name="page"	type="hidden" value="<?=$page;?>">

<input name="edit_mode" type="hidden" value="<?=$editMode;?>">

<input name="find_center_code" type="hidden" value="<?=$find_center_code;?>">
<input name="find_center_name" type="hidden" value="<?=$find_center_name;?>">
<input name="find_center_addr" type="hidden" value="<?=$find_center_addr;?>">


<input name="is_m" type="hidden" value="<?=$_SESSION['userLevel'] == 'A' ? 'YES' : 'NO';?>">
<input id="code" name="code" type="hidden" value="<?=$mCode;?>">

<div style="width:100%; margin:0; padding:0; text-align:right; margin:5px;"><?=$button;?></div>

</form>

<script type="text/javascript">
$(document).ready(function(){
	load_icon(document.getElementById('icon_img'));
	load_icon(document.getElementById('jikin_img'));
});
function lfInsuChange(obj){
	var objModal = new Object();
	var url      = './insu_pop.php';
	var style    = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

	objModal.code   = $('#code').val();
	objModal.svcCd  = $(obj).attr('svcCd');
	objModal.seq    = $(obj).attr('seq');
	objModal.insuCd = $(obj).attr('insuCd');
	objModal.insuNm = $('#lblInsuNm').text();
	objModal.fromDt = $(obj).attr('from');
	objModal.toDt   = $(obj).attr('to');
	objModal.result = 0;

	window.showModalDialog(url, objModal, style);

	if (objModal.result < 1) return;

	$('#lblInsuNm').text(objModal.insuNm);
	$('#lblInsuFrom').text(objModal.fromDt.split('-').join('.'));
	$('#lblInsuTo').text(objModal.toDt.split('-').join('.'));
}
</script>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
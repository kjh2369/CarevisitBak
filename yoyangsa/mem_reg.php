<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_mySalary.php');
	include_once('../inc/_ed.php');

	define(__MENU_0__, '초기상담기록지');
	define(__MENU_1__, '직원정보');
	define(__MENU_5__, '과정상담');
	define(__MENU_6__, '인적자원관리');

	if ($gDomain == 'kacold.net'){
		if ($gHostNm == '' || $gHostNm == 'www'){
			$IsCare = true;
		}else{
			$IsCare = false;
		}
	}else{
		$IsCare = true;
	}

	$find_yoy_name  = $_POST['find_yoy_name'];
	$find_yoy_phone = $_POST['find_yoy_phone'];
	$find_yoy_stat  = $_POST['find_yoy_stat'];
	$find_dept      = $_POST['find_dept'];

	$code  = $_SESSION["userCenterCode"]; //$_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];			//기관번호
	$jumin = $ed->de($_REQUEST['jumin']);	//주민번호
	$kind  = $conn->_mem_kind_cd($code, $jumin); //$conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$code'"); //기관분류
	$page  = $_REQUEST['page'];

	// 기관분류 리스트
	$k_list = $conn->kind_list($code, true);
	$k_cnt  = sizeof($k_list);

	// 기관기준 데이타
	$center_stad = $conn->get_standard($code, $kind);

	// 기관정보
	$center_code = $conn->center_code($code, $kind);
	$center_name = $conn->center_name($code, $kind);

	// 기본기관구분
	$basic_kind = $kind; //$k_list[0]['code'];

	//수가정보
	$sql = 'select m01_mcode2 as cd
			,      m01_suga_cont as nm
			,      m01_suga_value as val
			  from m01suga
			 where m01_mcode          = \'goodeos\'
			   and left(m01_sdate,6) <= date_format(now(),\'%Y%m\')
			   and left(m01_edate,6) >= date_format(now(),\'%Y%m\')
			 union all
			select m11_mcode2
			,      m11_suga_cont
			,      m11_suga_value
			  from m11suga
			 where m11_mcode          = \'goodeos\'
			   and left(m11_sdate,6) <= date_format(now(),\'%Y%m\')
			   and left(m11_edate,6) >= date_format(now(),\'%Y%m\')';

	$laSuga = $conn->_fetch_array($sql,'cd');


	/*********************************************************
	 * 직원 이력내역
	 *********************************************************/
	$sql = 'select *
			  from mem_his
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by seq desc
			 limit 1';

	$memHis = $conn->get_array($sql);

	//기관 배상책임보험 정보
	$sql = 'SELECT from_dt
			,      to_dt
			  FROM insu_center
			 WHERE org_no   = \''.$code.'\'
			   AND from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			   AND to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
	$lcInsuDt = $conn->get_array($sql);


	/*********************************************************
	 * 지원별 옵션
 	 *********************************************************/
	$sql = 'select	mo_extrapay_yn as extrapay_yn
			,		mo_salary_yn as salary_yn
			,		family_yn
			,		extratime_yn
			,		insu_yn
			,		dealin_yn
			,		deal_limit_yn
			,		counsel_yn,support_yn,response_yn,withoutpay_yn
			,		day_night_yn
			,		baby_mg_yn
			,		baby_mg_area
			,		dan_prg_yn
			,		prg_cost_not_yn
			,	    dementia_yn
			,	    insu_new_yn
			,		retirement_not_yn
			,		jobfunds_not_yn
			,		weekly_pay_yn
			,		annual_pay_yn
			from	mem_option
			where	org_no   = \''.$code.'\'
			and		mo_jumin = \''.$jumin.'\'';

	$memOption = $conn->get_array($sql);

	/*
	//보험사코드
	$sql = 'SELECT g02_ins_code
			  FROM g02inscenter
			 WHERE g02_ccode = \''.$code.'\'
			   AND g02_mkind = \'0\'
			   AND CASE WHEN IFNULL(g02_ins_from_date,\'\') != \'\' THEN g02_ins_from_date ELSE \'19000101\' END <= DATE_FORMAT(NOW(),\'%Y%m%d\')
			   AND CASE WHEN IFNULL(g02_ins_to_date,  \'\') != \'\' THEN g02_ins_to_date   ELSE \'99991231\' END >= DATE_FORMAT(NOW(),\'%Y%m%d\')';
	*/


	// 직원정보 조회
	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode   = '$code'
			   and m02_yjumin  = '$jumin'
			   and m02_ccode  != ''
			   and m02_yjumin != ''
			   and m02_del_yn  = 'N'
			 order by m02_mkind";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mst[$row['m02_mkind']] = $row;
	}

	$conn->row_free();

	if (!isset($mst)){
		$mem_mode     = 0; //등록
		$counsel_mode = 1;
	}else{
		$mem_mode     = 1; //수정
		$counsel_mode = 2;
	}

	if ($mem_mode == 0){
		$sql = 'SELECT	deal_limit_yn
				FROM	center_comm
				WHERE	org_no = \''.$code.'\'';
		$memOption['deal_limit_yn'] = $conn->get_data($sql);
	}

	//최저시급
	$sql = 'SELECT	g07_pay_time
			FROM	g07minpay
			WHERE	g07_year = \''.Date('Y').'\'';
	$minpay = $conn->get_data($sql);

	switch($mem_mode){
		case 0:
			$mem_title = '직원등록';

			//등록시 기본설정
			$mst[$basic_kind]['m02_picture']        = '../image/no_img_bg.gif';
			$mst[$basic_kind]['m02_ma_yn']          = 'N';
			$mst[$basic_kind]['m02_ins_yn']         = 'N';
			$mst[$basic_kind]['m02_ygoyong_kind']   = '1';
			$mst[$basic_kind]['m02_ygoyong_stat']   = '1';
			$mst[$basic_kind]['m02_weekly_holiday'] = '0';
			$mst[$basic_kind]["m02_ygongjeja_no"]   = 1;
			$mst[$basic_kind]["m02_ygongjejaye_no"] = 0;
			$mst[$basic_kind]["m02_stnd_work_time"] = $center_stad['time'];
			$mst[$basic_kind]["m02_stnd_work_pay"]  = $center_stad['pay'];
			$mst[$basic_kind]['m02_mem_no']         = $myF->formatString($conn->get_data("select ifnull(max(m02_mem_no), 0) + 1 from m02yoyangsa where m02_ccode = '$code'"), '########');
			$mst[$basic_kind]['m02_bipay_yn']       = 'N';
			$mst[$basic_kind]['m02_bipay_rate']     = 0;

			$memHis['employ_type'] = '1';
			$memHis['employ_stat'] = '1';
			$memHis['weekly']      = '0';


			/*********************************************************
				기준시간 및 시급
			*********************************************************/
			$fixedWorks['hours']   = $center_stad['time'];
			$fixedWorks['hourly']  = $center_stad['pay'];
			$fixedWorks['from_dt'] = '';
			$fixedWorks['to_dt']   = '';

			if ($fixedWorks['hourly'] < $minpay) $fixedWorks['hourly'] = $minpay;

			if (strlen($mst[$basic_kind]['m02_mem_no']) != 8)
				$mst[$basic_kind]['m02_mem_no'] = date('Y', mktime()).$myF->zero_str($mst[$basic_kind]['m02_mem_no'], 4);

			$mst[$basic_kind]["m02_ytoisail"] = '99991231';

			// 사용자 로그인 정보
			$member['code'] = '--';
			$member['pswd'] = '--';
			break;
		case 1:
			$mem_title = '직원수정';

			if ($mst[$basic_kind]['m02_picture'] == '')
				$mst[$basic_kind]['m02_picture'] = '../image/no_img_bg.gif';

			// 사용자 로그인 정보
			$sql = "select code, pswd
					  from member
					 where org_no = '$code'
					   and jumin  = '$jumin'
					   and del_yn = 'N'";

			$member = $conn->get_array($sql);

			if ($member['code'] == '') $member['code'] = '미등록';
			if ($member['pswd'] == ''){
				$member['pswd'] = '미등록';
			}else{
				$member['pswd'] = '<span class="btn_pack small"><button onclick="lfInitPwd();">초기화</button></span>';
			}

			/*********************************************************
				기준시간 및 시급
			*********************************************************/
			$sql = 'select fw_hours as hours
					,      fw_hourly as hourly
					,	   fw_from_dt as from_dt
					,      fw_to_dt as to_dt
					  from fixed_works
					 where org_no      = \''.$code.'\'
					   and fw_jumin    = \''.$jumin.'\'
					   and fw_from_dt <= date_format(now(), \'%Y%m\')
					   and fw_to_dt   >= date_format(now(), \'%Y%m\')
					   and del_flag    = \'N\'';

			$fixedWorks = $conn->get_array($sql);

			if (!is_array($fixedWorks)){
				$sql = 'select min(m00_mkind) as kind
						,      m00_day_work_hour as hours
						,      m00_day_hourly as hourly
						  from m00center
						 where m00_mcode  = \''.$code.'\'
						   and m00_del_yn = \'N\'
						 group by m00_day_work_hour, m00_day_hourly';

				$tmpFixedWorks = $conn->get_array($sql);

				$fixedWorks['hours']   = $tmpFixedWorks['hours'];
				$fixedWorks['hourly']  = $tmpFixedWorks['hourly'];
				$fixedWorks['from_dt'] = '';
				$fixedWorks['to_dt']   = '';

				if ($fixedWorks['hourly'] < $minpay) $fixedWorks['hourly'] = $minpay;
			}

			break;

			if ($mst[$basic_kind]['m02_bipay_yn'] != 'Y') $mst[$basic_kind]['m02_bipay_yn'] = 'N';
			if ($mst[$basic_kind]['m02_bipay_yn'] == 'Y')
				$mst[$basic_kind]['m02_bipay_rate'] = intval($mst[$basic_kind]['m02_bipay_rate']);
			else
				$mst[$basic_kind]['m02_bipay_rate'] = 0;
	}


	$mem_menu[0]['cd'] = 0;
	$mem_menu[0]['nm'] = __MENU_0__;
	$mem_menu[1]['cd'] = 1;
	$mem_menu[1]['nm'] = __MENU_1__;
	$mm_cnt = 2;

	// 기본으로 보여줄 메뉴
	$menu_select = $_GET['menu_select'];
	$counselMenu = $_GET['counsel_menu'];

	if ($menu_select == '') $menu_select = 1;
	if (Empty($counselMenu)) $counselMenu = '1';

	$current_menu = 1;

	// 급여
	foreach($k_list as $i => $k){
		// 급여액 초기화
		$id = $k['code'];

		$hourly_1[$id] = 0;

		$hourly_2[$id][1] = 0;
		$hourly_2[$id][2] = 0;
		$hourly_2[$id][3] = 0;
		$hourly_2[$id][9] = 0;

		$hourly_3[$id] = 0;
		$hourly_4[$id] = 0;

		// 급여산정박식
		if ($mst[$id]["m02_ygupyeo_kind"] == '1' || $mst[$id]["m02_ygupyeo_kind"] == '2'){
			if ($mst[$id]['m02_pay_type'] == 'Y'){
				$pay_type[$id] = 1; //시급(고정급)
			}else{
				$pay_type[$id] = 2; //시급(변동급)
			}
		}else if ($mst[$id]["m02_ygupyeo_kind"] == '3'){
			$pay_type[$id] = 3; //월급

			if ($mst[$id]['m02_pay_type'] == 'Y'){
				$pay_com_type[$id] = 'Y';
			}
		}else if ($mst[$id]["m02_ygupyeo_kind"] == '4'){
			$pay_type[$id] = 4; //총액비율
		}else{
			$pay_type[$id] = 0;
		}

		switch($pay_type[$id]){
		case 1:
			$hourly_1[$id] = $mst[$id]["m02_ygibonkup"];
			break;
		case 2:
			$sql = "select m02_gubun
					,      m02_pay
					  from m02pay
					 where m02_ccode = '$code'
					   and m02_mkind = '$id'
					   and m02_jumin = '$jumin'";
			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$hourly_2[$id][$row['m02_gubun']] = $row['m02_pay'];
			}

			$conn->row_free();
			break;
		case 3:
			$hourly_3[$id] = $mst[$id]["m02_ygibonkup"];
			break;
		case 4:
			$hourly_4[$id] = $mst[$id]["m02_ysuga_yoyul"];
			break;
		}
	}

	// 동거가족 급여
	if($mst[$basic_kind]['m02_yfamcare_type'] == '1'){
		$famcare_type = 1; //고정급

		if($mst[$basic_kind]['m02_yfamcare_umu'] == 'N'){
			$famcare_type = 0;  //무
		}
	}else if($mst[$basic_kind]['m02_yfamcare_type'] == '2'){
		$famcare_type = 2; //수가총액
	}else if($mst[$basic_kind]['m02_yfamcare_type'] == '3'){
		$famcare_type = 3; //고정급
	}else {
		$famcare_type = 0;
	}

	// 동거가족 본인부담금 수당지급 여부
	/*
	$family_pay_yn = $mst[$basic_kind]['m02_family_pay_yn'];

	switch($famcare_type){
		case '1':
			$famcare_pay1 = $mst[$basic_kind]['m02_yfamcare_pay'];
			break;
		case '2':
			$famcare_pay2 = $mst[$basic_kind]['m02_yfamcare_pay'];
			break;
		case '3':
			$famcare_pay3 = $mst[$basic_kind]['m02_yfamcare_pay'];
			break;
		default:
			$family_pay_yn = 'N';
	}*/
	$ls_familyPayYN = $mst[$basic_kind]['m02_family_pay_yn'];

	// 배상책임보험 설정
	if ($mst[$basic_kind]['m02_ins_yn'] == 'Y'){
		$inc_code    = $mst[$basic_kind]['m02_ins_code'];
		$ins_from_dt = $mst[$basic_kind]['m02_ins_from_date'];
		$ins_to_dt   = $mst[$basic_kind]['m02_ins_to_date'];
	}else{
		// 기관의 보험가입정보
		$sql = 'select g02_ins_code as code
				,      g02_ins_from_date as fromDate
				,      g02_ins_to_date as toDate
				  from g02inscenter
				 where g02_ccode = \''.$code.'\'
				   and ifnull(g02_ins_from_date, \'\') != \'\'
				   and ifnull(g02_ins_to_date, \'\') != \'\'
				 order by g02_mkind
				 limit 1';
		$ins = $conn->get_array($sql);

		if (is_array($ins)){
			$inc_use = 'Y';
			$inc_code = $ins['code'];

			if ($mem_mode == 1){
				$ins_from_dt = $ins['fromDate'];
			}else{
				$sql = "select g03_ins_to_date
						  from g03insapply
						 where g03_jumin          = '".$mst[$basic_kind]["m02_yjumin"]."'
						   and g03_ins_from_date >= '".$ins['fromDate']."'
						 order by g03_ins_to_date desc
						 limit 1";
				$tempDate = $conn->get_data($sql);

				if (strLen($tempDate) == 8){
					$tempDate = $myF->dateStyle($tempDate);
					$tempDate = $myF->dateAdd('day', 1, $tempDate, 'Ymd');
					$ins_from_dt = ($ins['fromDate'] > $tempDate ? $ins['fromDate'] : $tempDate);
				}else{
					$ins_from_dt = ($ins['fromDate'] > $mst[$basic_kind]['m02_yipsail'] ? $ins['fromDate'] : $mst[$basic_kind]['m02_yipsail']);
				}
			}

			$ins[1] = $ins_from_dt;
			$ins_to_dt = $ins['toDate'];
		}else{
			$inc_use = 'N';
		}
	}

	// 스마트폰 업무 구분
	$smart_gbn['M'] = 'N'; //관리자
	$smart_gbn['Y'] = 'N'; //요양보호사
	$smart_gbn['W'] = 'N'; //사회복지사

	if ($mst[$basic_kind]['m02_jikwon_gbn'] == 'B'){
		$smart_gbn['M'] = 'Y';
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}if ($mst[$basic_kind]['m02_jikwon_gbn'] == 'A'){
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['M'] = 'Y';
	}if ($mst[$basic_kind]['m02_jikwon_gbn'] == 'C'){
		$smart_gbn['M'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}if ($mst[$basic_kind]['m02_jikwon_gbn'] == 'D'){
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['W'] = 'Y';
	}else{
		if ($mst[$basic_kind]['m02_jikwon_gbn'] != ''){
			$smart_gbn[$mst[$basic_kind]['m02_jikwon_gbn']] = 'Y';
		}
	}

	/*
	// 메뉴탭설정
	$voucher_add_flag = false;

	for($i=0; $i<$k_cnt; $i++){
		if ($k_list[$i]['code'] == 0){
			$mem_menu[$mm_cnt]['cd'] = 2;
			$mem_menu[$mm_cnt]['nm'] = __MENU_2__;
			$mm_cnt ++;
		}else if(!$voucher_add_flag && $k_list[$i]['code'] >= 1 && $k_list[$i]['code'] <= 4){
			$mem_menu[$mm_cnt]['cd'] = 3;
			$mem_menu[$mm_cnt]['nm'] = __MENU_3__;
			$mm_cnt ++;
			$voucher_add_flag = true;
		}
	}
	*/

	#########################################################
	#
	# 메뉴설정

		$use_menu[0] = false;
		$use_menu[1] = false;
		$use_menu[2] = false;
		$use_menu[3] = false;
		$use_menu[4] = false;

		for($i=0; $i<$k_cnt; $i++){
			if ($k_list[$i]['id'] > 10 && $k_list[$i]['id'] < 20){
				$use_menu[0] = true;
			}else if ($k_list[$i]['id'] == 21){
				if ($gHostSvc['nurse']){
					$use_menu[1] = true;
				}
			}else if ($k_list[$i]['id'] == 22){
				if ($gHostSvc['old']){
					$use_menu[2] = true;
				}
			}else if ($k_list[$i]['id'] == 23){
				if ($gHostSvc['baby']){
					$use_menu[3] = true;
				}
			}else if ($k_list[$i]['id'] == 24){
				if ($gHostSvc['dis']){
					$use_menu[4] = true;
				}
			}
		}
	#
	#########################################################

	if ($mem_mode == 1){
		$mem_menu[$mm_cnt]['cd'] = 5;
		$mem_menu[$mm_cnt]['nm'] = __MENU_5__; //고충상담
		$mm_cnt ++;
	}

	$mem_menu[$mm_cnt]['cd'] = 6;
	$mem_menu[$mm_cnt]['nm'] = __MENU_6__; //인적자원관리
	$mm_cnt ++;

	echo '<script>';
	echo 'var mem_menu = new Array();';

	for($i=0; $i<$mm_cnt; $i++){
		echo 'mem_menu['.$i.'] = new Array();';
		echo 'mem_menu['.$i.'][\'cd\'] = \''.$mem_menu[$i]['cd'].'\';';
		echo 'mem_menu['.$i.'][\'nm\'] = \''.$mem_menu[$i]['nm'].'\';';
	}

	echo '</script>';
?>
<script language='javascript' src='../js/report.js'></script>
<script language='javascript' src='../js/work.js'></script>
<script language='javascript' src='./mem.js'></script>
<script language='javascript'>
<!--

window.onload = function(){
	var f = document.f;

	__init_form(f);
	form_init();

	if (f.mem_mode.value == 1){
		setTimeout('lfRegIsDel()',1);
	}

	_memInit();
}

function lfInitPwd(){
	if (!confirm('비밀번호를 초기화 하시겠습니까?')) return;

	$.ajax({
		type:'POST',
		url:'./mem_chpwd.php',
		data:{
			'code':$('#lblMemCd').text()
		},
		success:function(result){
			if (result == 1){
				alert('정상적으로 처리되었습니다.\n변경된 비밀번호는 "1111"입니다.\n\n로그인 후 비밀번호를 변경하여 주십시오.');
			}else if (result == 9){
				alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}
		},
		error:function(request, status, error){
			alert('[ERROR No.03]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
		}
	});
}

function lfRegIsDel(){
	var f = document.f;

	$.ajax({
		type : 'POST',
		url  : './mem_reg_isdel.php',
		data : {
			'code':f.code.value
		,	'ssn':f.ssn.value
		},
		success: function (data){
			if (data){
				$('#mem_request').html(data).show();
				$('#lblMsg1').hide();
				$('#memLCRequest').hide();
				$('#memLCLayer').hide();
			}else{
			//	$('#memLCRequest').show();
			}
		},
		error: function (request, status, error){
			alert('[ERROR No.03]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
		}
	});
}

function form_init(){
	var f = document.f;

	__setEnabled(f.ma_dt, __object_get_value('ma_yn') == 'Y' ? true : false);
	__setEnabled(f.ins_from_dt, __object_get_value('ins_yn') == 'Y' ? true : false);
	__setEnabled(f.ins_to_dt, __object_get_value('ins_yn') == 'Y' ? true : false);
	__setEnabled(f.nurseNo, __object_get_value('optNurseYn') == 'Y' ? true : false);

	if (f.mem_mode.value == 0){
		set_4ins_yn('');
	}else{
		//__setEnabled('annuity_pay', __object_get_value('annuity_yn') == 'Y' ? true : false);
		//__setEnabled('health_pay',  __object_get_value('health_yn')  == 'Y' ? true : false);
		//__setEnabled('employ_pay',  __object_get_value('employ_yn')  == 'Y' ? true : false);
		//__setEnabled('sanje_pay',   __object_get_value('sanje_yn')   == 'Y' ? true : false);
	}

	set_out_date(__object_get_value('employ_stat'));

	set_pay_obj(0);
	set_family_obj();
	set_pay_obj(1);
	set_pay_obj(2);
	set_pay_obj(3);
	set_pay_obj(4);
	set_bipay_obj();

	//if (f.inc_use.value == 'N'){
	//	var ins_yn = document.getElementsByName('ins_yn');

	//	for(var i=0; i<ins_yn.length; i++){
	//		__setEnabled(ins_yn[i], false);
	//	}
	//}

 	if ($('input:radio[name="ins_yn"]:checked').val() == 'Y'){
		__setEnabled(document.getElementById('ins_from_dt'),true);
		__setEnabled(document.getElementById('ins_to_dt'),true);
	}else{
		__setEnabled(document.getElementById('ins_from_dt'),false);
		__setEnabled(document.getElementById('ins_to_dt'),false);
	}


	set_add_payrate();

	/*******************************************************************************************************

		초기상담기록지 초기화

	*******************************************************************************************************/
	_setDisabled(__object_check('counsel_religion'), document.getElementById('counsel_rel_other'));
	_setDisabled(__object_check('counsel_app_path'), document.getElementById('counsel_app_other'));
	__setEnabled('counsel_hope_other', document.getElementById('counsel_hope_work6').checked);
	__setEnabled('counsel_service_other', __object_get_value('counsel_service_work') == 'Y' ? true : false);
}

function find_counsel(ssn){
	var health_is = true;
	var modal     = showModalDialog('mem_find_counsel.php?ssn='+ssn, window, 'dialogWidth:600px; dialogHeight:300px; dialogHide:no; scroll:no; status:no');

	if (!modal){
		document.getElementById('ssn1').value            = '';
		document.getElementById('ssn2').value            = '';
		document.getElementById('counsel_name').value    = '';
		document.getElementById('mem_mobile').value      = '';
		document.getElementById('mem_phone').value       = '';
		document.getElementById('mem_email').value       = '';
		//document.getElementById('mem_postno1').value     = '';
		//document.getElementById('mem_postno2').value     = '';
		document.getElementById('txtPostNo').value     = '';
		document.getElementById('txtAddr').value        = '';
		document.getElementById('txtAddrDtl').value    = '';
		document.getElementById('mem_counsel_gbn').value = '';

		document.getElementById('ssn1').focus();
	}else{
		if (document.getElementById('mem_counsel_gbn').value == 'A')
			health_is = false;
	}

	set_health_is(health_is);
}

function set_health_is(is_yn){
	var yn = 'N';

	if (is_yn)
		yn = __object_get_value('4ins_yn');

	if (is_yn){
		//__object_set_value('health_yn', yn);
	}else{
		__object_set_value('health_yn', 'N');
	}

	__object_enabled(document.getElementsByName('health_yn')[0], is_yn);
	__object_enabled(document.getElementsByName('health_yn')[1], is_yn);
	//__object_enabled(document.getElementById('health_pay'), is_yn);
}

function current_menu(cd){
	var menu_obj = document.getElementById('menu_select');

	for(var i=0; i<mem_menu.length; i++){
		var menu_id = document.getElementById('menu_'+mem_menu[i]['cd']);
		var menu = document.getElementById('my_menu_'+mem_menu[i]['cd']);

		if (mem_menu[i]['cd'] == cd){
			menu_id.style.fontWeight = 'bold';
			menu_id.style.color = '#0000ff';
			menu.style.display = '';
			menu_obj.value = cd;
		}else{
			menu_id.style.fontWeight = 'normal';
			menu_id.style.color = '#000000';
			menu.style.display = 'none';
		}
	}
}

function set_4ins_yn(val){
	var yn = __object_get_value('4ins_yn');

	__setEnabled('annuity_yn', yn == 'Y' ? true : false);
	__setEnabled('health_yn',  yn == 'Y' ? true : false);
	__setEnabled('employ_yn',  yn == 'Y' ? true : false);
	__setEnabled('sanje_yn',   yn == 'Y' ? true : false);

	if (val == ''){
		__object_set_value('annuity_yn', yn);
		__object_set_value('health_yn',  yn);
		__object_set_value('employ_yn',  yn);
		__object_set_value('sanje_yn',   yn);
	}

	//__setEnabled('annuity_pay', __object_get_value('annuity_yn') == 'Y' ? true : false);
	//__setEnabled('health_pay',  __object_get_value('health_yn')  == 'Y' ? true : false);
	//__setEnabled('employ_pay',  __object_get_value('employ_yn')  == 'Y' ? true : false);
	//__setEnabled('sanje_pay',   __object_get_value('sanje_yn')   == 'Y' ? true : false);

	if (yn == 'Y'){
		var obj_gbn = document.getElementById('mem_counsel_gbn').value;

		if (obj_gbn == 'A'){
			set_health_is(false);
		}else{
			set_health_is(true);
		}
	}

	try{
		setPAYE();
	}catch(e){
	}
}


/*********************************************************

	원천징수대상자여부

*********************************************************/
function setPAYE(){
	if ($(':radio[name="annuity_yn"]:checked').attr('value') == 'Y' ||
		$(':radio[name="health_yn"]:checked').attr('value')  == 'Y' ||
		$(':radio[name="employ_yn"]:checked').attr('value')  == 'Y' ||
		$(':radio[name="sanje_yn"]:checked').attr('value')   == 'Y'){

		if ($(':radio[name="payeYN"]:checked').attr('value') != 'Y') return;

		alert('4대보험에 가입된 대상자는 원천징수 대상자가 될 수 없습니다.!!');
		$(':radio[name="payeYN"]:input[value="N"]').attr('checked', 'checked');
	}
}


function set_out_date(gubun){
	if (gubun == '1' || gubun == '2'){
		__setEnabled(document.f.out_dt, false);
		document.f.out_dt.style.backgroundColor = '#efefef';
	}else{
		__setEnabled(document.f.out_dt, true);
		document.f.out_dt.style.backgroundColor = '#ffffff';
	}
}

function set_nurse_yn(gubun){
	if (gubun == 'N'){
		__setEnabled(document.f.nurseNo, false);
		document.f.nurseNo.style.backgroundColor = '#efefef';
	}else{
		__setEnabled(document.f.nurseNo, true);
		document.f.nurseNo.style.backgroundColor = '#ffffff';
	}
}

function set_pay_obj(kind){
	try{
		var pay_kind = __object_get_value('pay_kind'+kind);

		__setEnabled(document.getElementById('hourly_pay'+kind), false);
		__setEnabled(document.getElementById('base_pay'+kind), false);
		__setEnabled(document.getElementById('ybnpay'+kind), false);
		__setEnabled(document.getElementById('suga_rate_pay'+kind), false);

		for(var i=0; i<4; i++)
			__setEnabled(document.getElementsByName('change_hourly_pay'+kind+'[]')[i], false);

		switch(pay_kind){
			case '1':
				__setEnabled(document.getElementById('hourly_pay'+kind), true);
				break;
			case '2':
				for(var i=0; i<4; i++)
					__setEnabled(document.getElementsByName('change_hourly_pay'+kind+'[]')[i], true);
				break;
			case '3':
				__setEnabled(document.getElementById('base_pay'+kind), true);
				__setEnabled(document.getElementById('ybnpay'+kind), true);
				break;
			case '4':
				__setEnabled(document.getElementById('suga_rate_pay'+kind), true);
				break;
		}
	}catch(e){
	}
}

function set_add_payrate(){
	var f = document.f;

	if ('<?=$IsCare;?>' != '1') return;

	if (f.sunday_payrate_yn.checked || f.holiday_payrate_yn.checked){
		__setEnabled('holiday_payrate', true);
	}else{
		__setEnabled('holiday_payrate', false);
	}
}

/************************************
	비급여수가
************************************/
function set_bipay_obj(){
	//var nopay_yn = __object_get_value('bipay_yn');
	//__setEnabled(document.getElementById('bipay_rate'), nopay_yn == 'Y' ? true : false);
}
function chk_bipay_rate(){
	var rate = document.getElementById('bipay_rate');

	if (rate.value > 100) rate.value = 100;
}

function set_family_obj(){
	try{
		var pay_kind = __object_get_value('family_pay_kind');

		__setEnabled(document.f.family_hourly_pay, false);
		__setEnabled(document.f.family_base_pay, false);
		__setEnabled(document.f.family_suga_rate_pay, false);

		switch(pay_kind){
			case '1':
				__setEnabled(document.f.family_hourly_pay, true);
				break;
			case '2':
				__setEnabled(document.f.family_suga_rate_pay, true);
				break;
			case '3':
				__setEnabled(document.f.family_base_pay, true);
				break;
		}
	}catch(e){
	}
}

function go_menu(index, target){
	current_menu(index);
	target.focus();
}

function chk_pay(pay_type, svc_type){
	switch(pay_type){
		case '1':
			var hourly_pay = document.getElementById('hourly_pay'+svc_type);

			if (!__alert(hourly_pay)){
				hourly_pay.focus();
				return false;
			}
			break;
		case '2':
			var change_hourly_pay = document.getElementsByName('change_hourly_pay'+svc_type+'[]');

			for(var j=0; j<change_hourly_pay.length; j++){
				if (!__alert(change_hourly_pay[j])){
					change_hourly_pay[j].focus();
					return false;
				}
			}
			break;
		case '3':
			var base_pay = document.getElementById('base_pay'+svc_type);

			if (!__alert(base_pay)){
				base_pay.focus();
				return false;
			}
			break;
		case '4':
			var suga_rate_pay = document.getElementById('suga_rate_pay'+svc_type);

			if (!__alert(suga_rate_pay)){
				suga_rate_pay.focus();
				return false;
			}
			break;
	}
	return true;
}

//사번확인
function chk_memno(mem){
	var code = document.getElementById('code').value;
	var rst  = getHttpRequest('../inc/_chk_ssn.php?id=130&code='+code+'&ssn='+mem.value);

	if(mem.value != ''){
		if (rst == 'Y'){
			alert('입력하신 사번은 사용중인 사번입니다. 다른 사번을 입력하여 주십시오.');
			mem.value = '';
			mem.focus();
			return false;
		}
	}

	return true;
}

function go_list(page){
	var f = document.f;

	f.page.value = page;
	f.target = '_self';
	f.action = 'mem_list.php';
	f.submit();
}

function go_save(){
	var f = document.f;

	// 주민번호
	if (f.mem_mode.value == 0){
		if (f.ssn1.value.length == 6 && f.ssn2.value.length == 7){
		}else{
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.ssn1.focus();
			return;
		}

		/*
		if (f.code.value == '1234' ||
			f.code.value == 'test'){
		}else{
			if (!_check_ssn('yoy', f.ssn1, f.ssn2, f.code)){
				//alert('등록할 대상자를 선택하여 주십시오.');
				//current_menu(1);
				//find_counsel();
				return;
			}
		}
		*/

		/*
		if ('<?=$debug;?>' == '1'){
			if (confirm('직원등록시 배상책임보험 가입을 진행하시겠습니까?')){
			}
		}
		*/

		/*
		try{
			if ('<?=$debug;?>' == '1'){
				if ($('input:radio[name="insYN"]:checked').val() == 'Y'){
					if ($('#joinDt').val() > $('#txtInsuFrom').val()){
						alert('배상책임보험 가입일자는 입사일부터 가능합니다.');
						$('#txtInsuFrom').focus();
						return;
					}
				}
			}
		}catch(e){
		}
		*/
	}

	// 성명
	/*
	if (__replace(f.counsel_name.value, ' ', '') == ''){
		alert('성명을 입력하여 주십시오.');
		go_menu(1, f.counsel_name);
		return;
	}
	*/

	// 사번
	//if (!__alert(f.member_no)){
	//	go_menu(1, f.member_no);
	//	return;
	//}
	
	// 입사일자
	if (!__alert(f.join_dt)){
		go_menu(1, f.join_dt);
		return;
	}
	
	// 식대보조비
	if (__str2num($('#mealPay').attr('value')) > 100000){
		alert('식대보조비는 10만원을 초과할 수 업습니다.\n\n확인 후 다시 입력하여 주십시오.');
		$('#mealPay').focus();
		return;
	}

	// 차량유지비
	if (__str2num($('#carPay').attr('value')) > 200000){
		alert('차량유지비는 20만원을 초과할 수 업습니다.\n\n확인 후 다시 입력하여 주십시오.');
		$('#carPay').focus();
		return;
	}

	// 퇴직금 중간정산인경우 중간정산일자
	var ma_yn = __object_get_value('ma_yn');

	if (ma_yn == 'Y'){
		if (!__alert(f.ma_dt)){
			go_menu(1, f.ma_dt);
			return;
		}
	}

	// 퇴사할 경우 퇴사일자 체크
	if (__object_get_value('employ_stat') == '9'){
		if (f.out_dt.value == ''){
			alert('퇴사일자를 입력하여 주십시오.');
			go_menu(1, f.out_dt);
			return;
		}

		if (f.join_dt.value > f.out_dt.value){
			alert('퇴사일이 입사일보다 작습니다. 확인하여 주십시오.');
			go_menu(1, f.out_dt);
			return;
		}
	}
	
	// 퇴사할 경우 퇴사일자, 배상책임보험 가입인경우 가입기간
	/*
	 * 배상책임보험 입력을 우선은 체크하지 않는다.
	var ins_yn = __object_get_value('ins_yn');
	
	if (__object_get_value('employ_stat') == '9'){
		if (!checkDate(f.out_dt.value)){
			alert('퇴사일자를 입력하여 주십시오.');
			go_menu(1, f.out_dt);
			return;
		}

		if (f.join_dt.value > f.out_dt.value){
			alert('퇴사일이 입사일보다 작습니다. 확인하여 주십시오.');
			go_menu(1, f.out_dt);
			return;
		}

		if (ins_yn == 'Y'){
			alert('배상책임보험을 해지신청하여 주십시오.');
			current_menu(1);
			return;
		}
	}else{
		if (ins_yn == 'Y'){
			if (!checkDate(f.ins_from_dt.value)){
				alert('배상책임보험 가입기간 입력오류입니다. 확인하여 주십시오.');
				go_menu(1, f.ins_from_dt);
				return;
			}

			if (f.join_dt.value > f.ins_from_dt.value){
				alert('배상책임보험 가입기간이 입사일보다 빠릅니다. 확인하여 주십시오.');
				go_menu(1, f.ins_from_dt);
				return;
			}

			if (!checkDate(f.ins_to_dt.value)){
				alert('배상책임보험 가입기간 입력오류입니다. 확인하여 주십시오.');
				go_menu(1, f.ins_to_dt);
				return;
			}
		}
	}
	*/

		if (f.menu_select.value == 5 &&
			f.txtCounselMode.value == '0'){
			/*
			if (f.stress_talker_cd.value == ''){
				alert('<?=__MENU_5__;?> 상담자를 입력하여 주십시오.');
				current_menu(5);
				__find_yoyangsa('<?=$code;?>','<?=$kind;?>','stress_talker_cd','stress_talker_str');
				return;
			}
			*/

			if (!checkDate(f.stress_dt.value)){
				alert('<?=__MENU_5__;?> 상담일 오류입니다. 확인하여 주십시오.');
				go_menu(5, f.stress_dt);
				return;
			}
		}

		// 고충상담 작성시 입력 확인한다.
		if (f.menu_select.value == 5 &&
			f.txtCounselMode.value == '2'){
			if (f.stress_m_cd.value == ''){
				alert('접수자를 입력하여 주십시오.');
				current_menu(5);
				__find_yoyangsa('<?=$code;?>','<?=$kind;?>','stress_m_cd','stress_m_nm');
				return;
			}

			if (f.stress_dt.value==''){
				alert('상담일자 입력하여 주십시오.');
				go_menu(5, f.stress_dt);
				return;
			}
		}


		// 사례관리 작성시 입력 확인한다.
		if (f.menu_select.value == 5 &&
			f.txtCounselMode.value == '3'){
			if (f.case_run_cd.value == ''){
				alert('주관자를 입력하여 주십시오.');
				current_menu(5);
				__find_yoyangsa('<?=$code;?>','<?=$kind;?>','case_run_cd','case_run_nm');
				return;
			}

			if (f.case_dt.value==''){
				alert('회의일자를 입력하여 주십시오.');
				go_menu(5, f.case_dt);
				return;
			}
		}


	f.action = 'mem_save.php';
	f.target = '_self';
	f.submit();
}


/**************************************************

	인적자원 출력

**************************************************/
function show_pdf(type){
	var f = document.f;

	var w = 700;
	var h = 900;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
	var f   = document.f;

	f.target = 'SHOW_PDF';
	f.action = '../counsel/counsel_show.php?type='+type;
	f.submit();
	f.target = '_self';
}

function lfShowCareer(seq){
	var arguments	= 'root=yoyangsa'
					+ '&dir=P'
					+ '&fileName=mem_career'
					+ '&fileType=pdf'
					+ '&target=show.php'
					+ '&jumin='+$('#memJumin').val()
					+ '&seq='+seq
					;

	__printPDF(arguments);
}

function lfSetSign(obj, key, gbn){
	try{
		__SetSign(obj, key, gbn);
	}catch(e){
		if ('<?=$debug;?>' == '1'){
			alert(e);
		}
	}
}
-->
</script>
<form name="f" method="post" enctype="multipart/form-data">

<div class="title"><?=$mem_title;?></div>

<table class="my_table my_border" style="<? if($mode > 1){?>margin-top:-1px;<?} ?>">
	<colgroup>
		<col width="70px">
		<col width="130px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last"><?=$center_name;?></td>
		</tr>
		<tr>
			<th>구분</th>
			<td class="left last" colspan="3">
			<?
				if (is_array($mem_menu)){
					foreach($mem_menu as $i => $m){
						if ($m['cd'] == $menu_select){
							$current_menu = $i;
							$font_style   = 'font-weight:bold; color:#0000ff;';
						}else{
							$font_style = 'font-weight:normal;';
						}

						if ($mem_mode == 0 && $m[$i]['cd'] >= 5){
							$link_text = '<span id=\'menu_'.$m['cd'].'\' style=\'color:#cccccc;'.$font_style.'\'>'.$m['nm'].'</span>';
						}else{
							$link_text = '<span class="btn_pack m" ><button id=\'menu_'.$m['cd'].'\' style=\''.$font_style.'\' href=\'#\' onclick=\'current_menu("'.$m['cd'].'"); return false;\'>'.$m['nm'].'</button></span>';
						}
						echo ($i > 0 ? '  ' : '').$link_text;
					}
				}

				/*********************************
				 2012.09.13 인사기록카드 출력
				*********************************/
				if ($mem_mode == 1){
					echo ' <span class="btn_pack m" ><button style=\'font-weight:normal;\' href=\'#\' onclick=\'show_pdf("HUMAN2"); return false;\'>인사기록카드</button></span>';
				}


				/*********************************
				 2012.09.27 본인부담금 급여 공제지급 동의서 출력
				*********************************/
				if ($mem_mode == 1){
					echo ' <span class="btn_pack m" ><button style=\'font-weight:normal;\' href=\'#\' onclick=\'show_pdf("AGREE"); return false;\'>급여공제동의서</button></span>';
				}

				if ($mem_mode == 1){
					//경력증명서?>
					<span class="btn_pack m"><button style="font-weight:normal;" onclick="lfShowCareer('<?=$memHis['seq'];?>'); return false;">경력증명서</button></span><?
				}
				
				if ($mem_mode == 1){
					if($memHis['employ_type']=='1'){
						$report_id = 'WR60M';
					}else if($memHis['employ_type']=='2'){
						$report_id = 'WR60M';
					}else if($memHis['employ_type']=='3'){
						$report_id = 'WR60U';
					}else if($memHis['employ_type']=='4'){
						$report_id = 'WR60D';
					}

				
					//경력증명서?>
					<span class="btn_pack m"><button style="font-weight:normal;" onclick="_contract_report_show('<?=$report_id?>','<?=$memHis['join_dt']?>','<?=$ed->en($jumin)?>'); return false;">근로계약서</button></span><?
				}else { ?>
					<span class="btn_pack m"><button style="font-weight:normal;" onclick="_contract_report_show('WR60M','<?=date('Y-m-d');?>',''); return false;">근로계약서(월급제)</button></span>
					<span class="btn_pack m"><button style="font-weight:normal;" onclick="_contract_report_show('WR60U','<?=date('Y-m-d');?>',''); return false;">근로계약서(60시간이상)</button></span>
					<span class="btn_pack m"><button style="font-weight:normal;" onclick="_contract_report_show('WR60D','<?=date('Y-m-d');?>',''); return false;">근로계약서(60시간미만)</button></span><?
				}
			

				for($i=0; $i<$k_cnt; $i++){
					echo '<input name="kind_temp[]" type="hidden" value="'.$k_list[$i]['code'].'">';
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<div>
<?
	//include_once('../counsel/mem_counsel_info.php');
	//include_once('./mem_comm_info_20150617.php');
	include_once('./mem_comm_info.php');
?>
</div>

<div id="my_menu_0" style="padding-bottom:10px; display:<?=$mem_menu[$current_menu]['nm']==__MENU_0__?'':'none';?>;">
<?
	include('../counsel/mem_counsel_reg_sub.php');
?>
</div>

<div id="my_menu_1" style="display:<?=$mem_menu[$current_menu]['nm']==__MENU_1__?'':'none';?>;">
	<div style="margin-left:10px; margin-top:10px;">
	<?
		include('../counsel/mem_counsel_btn.php');
	?>
	</div>
	<?
		include('./mem_basic_info.php');

		if ($_SESSION['userLevel'] == 'C'){
			if ($mem_mode == 1) include('./mem_4insure_discount.php');

			if ($IsCare){
				include('./mem_extra_info.php');
				include('./mem_service_info.php');
			}
		}

		if ($gHostSvc['careSvc']){
			include('./mem_care_other.php');
		}

		include_once('./mem_memo.php');
	?>
	<div style="margin-left:10px; margin-top:10px; padding-bottom:10px; clear:both;">
	<?
		include('../counsel/mem_counsel_btn.php');
	?>
	</div>
</div>

<!--
	과정상담
  -->
<div id="my_menu_5" style="display:<?=$mem_menu[$current_menu]['nm']==__MENU_5__?'':'none';?>;"><?
	$counselBtn = '<table class="my_table my_border_blue" style="width:100%;">
					<tbody>
						<tr>
							<th class="right">
								<span name="mnuCounselList" class="btn_pack m icon" style="display:none;"><span class="list"></span><button type="button" onFocus="this.blur();" onclick="go_list(); return false;">리스트</button></span>
								<span name="mnuCounselReg" class="btn_pack m icon" style="display:none;"><span class="download"></span><button type="button" onFocus="this.blur();" onclick="lfCounselReg(); return false;">작성</button></span>
								<span name="mnuCounselSave" class="btn_pack m icon" style="display:none;"><span class="save"></span><button type="button" onFocus="this.blur();" onclick="go_save(); return false;">저장</button></span>
								<span name="mnuCounselCancel" class="btn_pack m" style="display:none;"><button type="button" onFocus="this.blur();" onclick="lfCounselList(); return false;">취소</button></span>
							</th>
						</tr>
					</tbody>
				   </table>';?>
	<div id="divCounselBtn" style="margin-left:10px; margin-top:10px;"><?=$counselBtn;?></div>
	<div id="divCounselMenu" style="margin-left:10px; margin-top:10px;">
		<table class="my_table my_border_blue" style="width:100%;">
			<colgroup>
				<col width="20%" span="5">
			</colgroup>
			<tbody>
				<tr>
					<th class="center bold" colspan="5">과정상담 메뉴</th>
				</tr>
				<tr>
					<td class="center"><a href="#" onclick="lfCounselMenu('1');" onfocus="this.blur();" id="mnuCounsel_1" value="N">과정상담</a></td>
					<td class="center"><a href="#" onclick="lfCounselMenu('2');" onfocus="this.blur();" id="mnuCounsel_2" value="N">고충상담</a></td>
					<td class="center"><a href="#" onclick="lfCounselMenu('3');" onfocus="this.blur();" id="mnuCounsel_3" value="N">사례관리</a></td>
					<td class="center">&nbsp;</td>
					<td class="center">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="divCounselBody" style="margin-left:10px; margin-top:10px;"></div>
	<div id="divCounselBtn" style="margin-left:10px; margin-top:10px;"><?=$counselBtn;?></div>
	<input id="txtCounselMode" name="txtCounselMode" type="hidden" value="0">

	<script type="text/javascript">
		$(document).ready(function(){
			lfCounselMenu('<?=$counselMenu;?>');
		});

		function lfCounselMenu(asIdx){
			$('a[id^="mnuCounsel_"]').css('font-weight','normal').css('color','#000000').attr('value','N');
			$('#mnuCounsel_'+asIdx).css('font-weight','bold').css('color','#0000ff').attr('value','Y');

			$('span[name^="mnuCounselList"]').show();
			$('span[name^="mnuCounselReg"]').show();

			lfCounselList();
		}

		function lfCounselList(){
			var lsIdx = $('a[id^="mnuCounsel_"][value="Y"]').attr('id').split('mnuCounsel_').join('');

			$.ajax({
				type: 'POST'
			,	url : './mem_counsel.php'
			,	data: {
					'code':$('#code').val()
				,	'ssn':$('#memJumin').val()
				,	'mode':lsIdx
				,	'type':'LIST'
				}
			,	beforeSend: function (){
				}
			,	success: function (html){
					$('#divCounselBody').html(html);
					$('span[name^="mnuCounsel"]').hide();
					$('span[name^="mnuCounselList"]').show();
					$('span[name^="mnuCounselReg"]').show();

					$('#txtCounselMode').val('0');

					__init_form(document.f);
				}
			,	error: function (){
				}
			}).responseXML;
		}

		function lfCounselReg(seq, yymm){
			var lsIdx = $('a[id^="mnuCounsel_"][value="Y"]').attr('id').split('mnuCounsel_').join('');

			$.ajax({
				type: 'POST'
			,	url : './mem_counsel.php'
			,	data: {
					'code':$('#code').val()
				,	'ssn':$('#memJumin').val()
				,	'yymm':yymm
				,	'seq':seq
				,	'mode':lsIdx
				,	'type':'REG'
				}
			,	beforeSend: function (){
				}
			,	success: function (html){
					$('#divCounselBody').html(html);
					$('span[name^="mnuCounsel"]').hide();
					$('span[name^="mnuCounselSave"]').show();
					$('span[name^="mnuCounselCancel"]').show();

					$('#txtCounselMode').val(lsIdx);

					__init_form(document.f);
				}
			,	error: function (){
				}
			}).responseXML;
		}

		function lfCounselDel(seq,yymm){
			if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

			var lsIdx = $('a[id^="mnuCounsel_"][value="Y"]').attr('id').split('mnuCounsel_').join('');

			$.ajax({
				type: 'POST'
			,	url : './mem_counsel.php'
			,	data: {
					'code':$('#code').val()
				,	'ssn':$('#memJumin').val()
				,	'yymm':yymm
				,	'seq':seq
				,	'mode':lsIdx
				,	'type':'DEL'
				}
			,	beforeSend: function (){
				}
			,	success: function (result){
					if (result == 'Y' || result == 'ok'){
						alert('선택하신 상담이력이 삭제되었습니다.');
						lfCounselList();
					}else{
						alert('상담이력 삭제 중 오류가 발생되었습니다. 잠시 후 다시 시도하여 주십시오.');
					}
				}
			,	error: function (){
				}
			}).responseXML;
		}

		function lfCounselShow(seq,yymm){
			var lsIdx = $('a[id^="mnuCounsel_"][value="Y"]').attr('id').split('mnuCounsel_').join('');

			if (lsIdx == '1'){
				var param = {'m_cd':document.getElementById('para_m_cd').value,'seq':seq};

				_report_show_pdf(1, param, '');
			}else{
				var showType = '';

				if (lsIdx == '2'){
					showType = 'STRESS';
				}else if (lsIdx == '3'){
					showType = 'CASE';
				}

				var h = 900;
				var w = 700;
				var l = (window.screen.width  - w) / 2;
				var t = (window.screen.height - h) / 2;

				var option = 'top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no';
				var url    = '../counsel/counsel_show.php?type='+showType;
				var win = window.open('', 'SHOW_PDF', option);
					win.opener = self;
					win.focus();

				var parm = new Array();

				if (lsIdx == '2'){
					parm = {
							'code':$('#code').val()
						,	'ssn':$('#memJumin').val()
						,	'stress_yymm':yymm
						,	'stress_seq':seq
						,	'root':'MEMBER'
						};
				}else if (lsIdx == '3'){
					parm = {
							'code':$('#code').val()
						,	'ssn':$('#memJumin').val()
						,	'case_yymm':yymm
						,	'case_seq':seq
						,	'root':'MEMBER'
						};
				}

				var form = document.createElement('form');
				var objs;
				for(var key in parm){
					objs = document.createElement('input');
					objs.setAttribute('type', 'hidden');
					objs.setAttribute('name', key);
					objs.setAttribute('value', parm[key]);

					form.appendChild(objs);
				}

				form.setAttribute('target', 'SHOW_PDF');
				form.setAttribute('method', 'post');
				form.setAttribute('action', url);

				document.body.appendChild(form);

				form.submit();
			}
		}
	</script>
</div>

<!--
	인적자원관리
  -->
<div id="my_menu_6" style="display:<?=$mem_menu[$current_menu]['nm']==__MENU_6__?'':'none';?>;">
<?
	$menu_mode = 'HUMAN';

	echo '<div style=\'margin-left:10px; margin-top:10px;\'>';
	include('../counsel/mem_counsel_btn.php');
	echo '</div>';

	echo '<div style=\'margin-left:10px; margin-top:10px;\'>';
	include('../counsel/mem_human.php');
	echo '</div>';

	echo '<div style=\'margin-left:10px; margin-top:10px; margin-bottom:10px; clear:both;\'>';
	include('../counsel/mem_counsel_btn.php');
	echo '</div>';
?>
</div>

<input id="code" name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="page"	type="hidden" value="<?=$page;?>">
<input id="memMode" name="mem_mode" type="hidden" value="<?=$mem_mode;?>" value1="<?=$mem_mode;?>">

<input name="inc_use" type="hidden" value="<?=$inc_use;?>">
<input name="inc_code" type="hidden" value="<?=$inc_code;?>">
<input name="ins_no" type="hidden" value="<?=$mst[$basic_kind]['m02_ins_no'];?>">

<input name="find_yoy_name"	type="hidden" value="<?=$find_yoy_name;?>">
<input name="find_yoy_phone"type="hidden" value="<?=$find_yoy_phone;?>">
<input name="find_yoy_stat"	type="hidden" value="<?=$find_yoy_stat;?>">
<input name="find_dept"	    type="hidden" value="<?=$find_dept;?>">

<input id="insuYn" name="insuYn" type="hidden" value="N">
<input id="memHisSeq" name="memHisSeq" type="hidden" value="<?=(!empty($memHis['seq']) ? $memHis['seq'] : 1);?>">

<div id="loadingBody" style="position:absolute; left:0; top:0; widht:auto; height:auto;"></div>

<?
	###########################################################
	# 환경변수

	echo '<input name=\'menu_select\' type=\'hidden\' value=\''.$menu_select.'\'>';

	###########################################################
?>

</form>

<?
	unset($laSuga);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
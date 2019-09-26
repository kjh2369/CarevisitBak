<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code  = $_SESSION["userCenterCode"]; //$_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];			//기관번호
	$jumin = $ed->de($_REQUEST['jumin']);	//주민번호
	$kind  = $conn->get_data("select m03_mkind from m03sugupja where m03_ccode = '$code' and m03_jumin = '$jumin' and m03_del_yn = 'N' limit 1"); //기관분류
	$page  = $_REQUEST['page'];

	// 검색조건 인자
	$find_center_code   = $_POST['find_center_code'];
	$find_center_name   = $_POST['find_center_name'];
	$find_su_name		= $_POST['find_su_name'];
	$find_su_phone		= $_POST['find_su_phone'];
	$find_su_stat       = $_POST['find_su_stat'];
	$find_center_kind   = $_POST['find_center_kind'];
	$strTeam			= $_POST['strTeam'];

	// 선택메뉴
	$current_menu = $_REQUEST['current_menu'];
	$record_menu  = $_REQUEST['record_menu'];

	// 기관분류 리스트
	//$k_list = $conn->kind_list($code, $gHostSvc['voucher']);
	//$k_cnt  = sizeof($k_list);

	// 기관기준 데이타
	$center_stad = $conn->get_standard($code, $kind);

	// 기관정보
	$center_code = $code;
	$center_name = $conn->center_name($code);

	// 수급자의 이용서비스 리스트
	$sql = "select m03_mkind
	        ,      m03_del_yn
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_jumin  = '$jumin'";

	$conn->query($sql);
	$conn->fetch();

	$write_mode = 1; //등록

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$write_mode = 2; //수정
		$row = $conn->select_row($i);

		$client_kind_list[$row['m03_mkind']]['cd']  = $row['m03_mkind'];
		$client_kind_list[$row['m03_mkind']]['id']  = 0;
		$client_kind_list[$row['m03_mkind']]['del'] = $row['m03_del_yn'];
	}

	$conn->row_free();



	/*********************************************************

		추천인

	*********************************************************/
	$sql = 'select cr_kind as kind
			,      cr_name as nm
			,      cr_tel as tel
			,      cr_amt as amt
			  from client_recom
			 where org_no   = \''.$code.'\'
			   and cr_jumin = \''.$jumin.'\'';

	$arrRecomList = $conn->_fetch_array($sql, 'kind');


	$tbl_width = 427;

	$title = '고객 등록';

	function group_button($group_load, $page){
		$group_btn  = '<div id=\'grp_btn\' style=\'padding:'.(!$group_load ? '10px 10px 0 10px' : '0 10px 10px 10px').';\'>';
		$group_btn .= '<table class=\'my_table my_border_blue\' style=\'width:100%;\'>';
		$group_btn .= '<colgroup>';
		$group_btn .= '<col>';
		$group_btn .= '</colgroup>';
		$group_btn .= '<tbody>';
		$group_btn .= '<tr>';
		$group_btn .= '<th class=\'head my_border_blue\'>';
		$group_btn .= '<div class=\'right\' style=\'float:right; width:auto;\'>';
		$group_btn .= '<span id=\'btn_list\' class=\'btn_pack m icon\' style=\'display:;\'><span class=\'list\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'client_list('.$page.'); return false;\'>리스트</button></span> ';
		$group_btn .= '<span id=\'btn_save\' class=\'btn_pack m icon\' style=\'display:;\'><span class=\'save\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'lfSvcSave(); return false;\'>저장</button></span> ';
		$group_btn .= '<span id=\'btn_write\' class=\'btn_pack m icon\' style=\'display:none;\'><span class=\'download\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'go_record_reg("",0); return false;\'>작성</button></span> ';
		$group_btn .= '<span id=\'btn_cancel\' class=\'btn_pack m\' style=\'display:none;\'><button type=\'button\' onFocus=\'this.blur();\' onclick=\'go_record_list(); return false;\'>취소</button></span>';
		$group_btn .= '</div>';

		if (!$group_load){
			$group_btn .= '<div class=\'left\' style=\'float:left; width:auto;\'>';
			$group_btn .= '<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onclick="return lfSvcHistory();">서비스 이용내역</button></span> ';
			$group_btn .= '</div>';
		}

		$group_btn .= '</th>';
		$group_btn .= '</tr>';
		$group_btn .= '</tbody>';
		$group_btn .= '</table>';
		$group_btn .= '</div>';

		return $group_btn;
	}
?>
<script language='javascript' src='./client.js'></script>
<script language='javascript'>
<!--

var show_mode = 'service';

// 초기상담기록지 찾기
function find_counsel(ssn){
	var modal = showModalDialog('client_find_counsel.php?ssn='+ssn, window, 'dialogWidth:600px; dialogHeight:300px; dialogHide:no; scroll:no; status:no');

	if (!modal){
		document.getElementById('jumin1').value = '';
		document.getElementById('jumin2').value = '';
		document.getElementById('jumin1').focus();
	}else{
		set_counsel_kind( $('#counsel_kind').attr('value') );
	}
}

// 배우자여부
function check_partner(cd, partner){
	var obj_cd = __getObject(cd);
	var obj_partner = document.getElementsByName(partner);
	var enabled = true;

	if (obj_cd.value == '')
		enabled = false;

	__setEnabled(obj_partner[0], enabled);
	__setEnabled(obj_partner[1], enabled);
}

// 선택직원삭제
function clear_mem(cd, nm){
	var obj_cd = __getObject(cd);
	var obj_nm = __getObject(nm);

	obj_cd.value = '';
	obj_nm.value = '';
}

//선택체크
function check_obj(obj, idx, svc_id, svc_gbn, svc_cd, svc_val){
	var val = __object_get_value(obj);
	var obj = document.getElementsByName(obj);

	if (obj[idx].value == val){
		return;
	}

	obj[idx].checked = !obj[idx].checked;

	check_time(svc_id, svc_gbn, svc_cd, svc_val);
}

// 서비스시간
function check_time(svc_id, svc_gbn, svc_cd, svc_val){
	var code    = document.getElementById('code').value;
	var obj_lvl = document.getElementsByName(svc_id+'_lvl');
	var lvl     = __get_value(obj_lvl);
	var kind    = __get_value(document.getElementsByName(svc_id+'_kind'));
	var from_dt = document.getElementById(svc_id+'_gaeYakFm').value;
	var to_dt   = document.getElementById(svc_id+'_gaeYakTo').value;
	var obj_gbn = document.getElementsByName(svc_id+'_gbn2');
	var date    = new Date();
	var today   = date.getFullYear()+'-'+(date.getMonth()+1 < 10 ? '0' : '')+(date.getMonth()+1)+'-'+(date.getDate() < 10 ? '0' : '')+date.getDate();

	if (svc_id == '24'){
		/******************************************

			아동

		******************************************/
			//__object_enabled(obj_lvl[0], (svc_gbn == 'C' ? false : true));
			//__object_enabled(obj_lvl[1], (svc_gbn == 'C' ? false : true));

			if (svc_gbn == 'C'){
				$('#disLvl1').css('display', 'none');
				$('#disLvl2').css('display', 'none');

				$('#strLvl3').text('1등급');
				$('#strLvl4').text('2등급');
			}else{
				$('#disLvl1').css('display', '');
				$('#disLvl2').css('display', '');

				$('#strLvl3').text('3등급');
				$('#strLvl4').text('4등급');
			}

			if (svc_gbn == 'C' && (lvl == '1' || lvl == '2')){
				obj_lvl[2].checked = true;
				check_time(svc_id,__object_get_value(svc_id+'_gbn'),__object_get_value(svc_id+'_lvl'),__object_get_value(svc_id+'_gbn2'));
				return;
			}
		/*****************************************/


		/**************************************************

			2011.11.01부터 등급제한을 막는다.

		if (svc_gbn != 'C' && svc_cd == '1'){
			// 장애등급이 1등급이면서 성인 및 65세 도래자만 특레구분을 설정한다.
			for(var i=1; i<obj_gbn.length; i++)
				__setEnabled(obj_gbn[i], true);
		}else{
			for(var i=1; i<obj_gbn.length; i++)
				__setEnabled(obj_gbn[i], false);

			obj_gbn[0].checked = true;
		}
		**************************************************/

		svc_val = __object_get_value(svc_id+'_gbn2');
	}

	var suga_cost = getHttpRequest('client_reg_value.php?gbn=suga_cost&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&date='+today);
	var kind_code = __object_get_att(svc_id+'_kind', 'code');

	if (svc_id == '24'){
		var stnd_time = getHttpRequest('client_reg_value.php?gbn=dis_stnd_time&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&date='+today);
		var add_time  = getHttpRequest('client_reg_value.php?gbn=dis_add_time&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&date='+today);
		var suga_time = parseInt(stnd_time, 10) + parseInt(add_time, 10);
		document.getElementById(svc_id+'_sugaTime').value = suga_time;
	}else{
		var suga_time = __object_get_att(svc_id+'_lvl', 'time');
	}

	var suga_pay1 = 0;
	var suga_pay2 = 0;
	var max_pay   = 0;

	if (svc_id == '24'){
		var stnd_max_pay = getHttpRequest('client_reg_value.php?gbn=svc_stnd_max_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&date='+today);
		var add_max_pay  = getHttpRequest('client_reg_value.php?gbn=svc_add_max_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&date='+today);

		if (kind != 9){
			var stnd_self_pay = getHttpRequest('client_reg_value.php?gbn=svc_stnd_self_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&svc_kind='+kind+'&date='+today);
			var add_self_pay  = getHttpRequest('client_reg_value.php?gbn=svc_add_self_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+svc_val+'&svc_kind='+kind+'&date='+today);
		}else{
			suga_pay1 = max_pay;
			max_pay   = 0;
		}
	}else{
		if (kind != 9){
			max_pay   = suga_cost * suga_time;

			if (svc_id != 22) svc_gbn = '';

			suga_pay1 = getHttpRequest('client_reg_value.php?gbn=suga_pay1&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&time='+suga_time+'&level='+kind+'&date='+today/*from_dt*/);
		}else{
			max_pay   = 0;
			suga_pay1 = suga_cost * suga_time;
		}
	}



	/*********************************************************
		장애인활동지원
	*********************************************************/
	if (svc_id == '24'){
		/*********************************************************
			추가급여
		*********************************************************/
		var addTime    = 0;
		var addMaxPay  = 0;
		var addSelfPay = 0;

		$(':input[name="addPayGbn_'+svc_id+'[]"]').each(function(){
			if ($(this).attr('checked')){
				//추가시간
				addTime += parseInt(getHttpRequest('client_reg_value.php?gbn=dis_add_time&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+$(this).attr('value')+'&date='+today), 10);

				//추가급여
				addMaxPay += parseInt(getHttpRequest('client_reg_value.php?gbn=svc_add_max_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+$(this).attr('value')+'&date='+today), 10);

				//추가본인부담금
				addSelfPay += cut(parseInt(getHttpRequest('client_reg_value.php?gbn=svc_add_self_pay&code='+code+'&kind=4&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&svc_cd='+svc_cd+'&svc_val='+$(this).attr('value')+'&svc_kind='+kind+'&date='+today), 10), 100);
			}
		});
	}



	if (svc_id == '24'){
		/*********************************************************
			기본단가
		*********************************************************/
		var basic_cost = getHttpRequest('client_reg_value.php?gbn=svc_basic_cost&code='+code+'&svc_id='+svc_id+'&svc_gbn='+svc_gbn+'&date='+today);

		var strAddTime    = __str2num(add_time) + __str2num(addTime);
		var atrAddMaxPay  = __str2num(add_max_pay) + __str2num(addMaxPay);
		var strAddSelfPay = __str2num(add_self_pay) + __str2num(addSelfPay);



		/*********************************************************
			장애인활동지원 시간
		*********************************************************/
		document.getElementById('pay_stnd_time').value = __num2str(stnd_time);
		document.getElementById('pay_add_time').value  = __num2str(strAddTime);


		/*********************************************************
			장애인활동지원 이용금액
		*********************************************************/
		document.getElementById('pay_stnd_use').value = __num2str(__str2num(stnd_max_pay) - __str2num(stnd_self_pay));
		document.getElementById('pay_add_use').value  = __num2str(__str2num(atrAddMaxPay) - __str2num(strAddSelfPay));
		document.getElementById('pay_sido_use').value = __num2str(__str2num(basic_cost) * __str2num(document.getElementById('pay_sido_time').value));
		document.getElementById('pay_jach_use').value = __num2str(__str2num(basic_cost) * __str2num(document.getElementById('pay_jach_time').value));


		/*********************************************************
			장애인활동지원 본인부담금
		*********************************************************/
		document.getElementById('pay_stnd_self').value = __num2str(__str2num(stnd_self_pay));
		document.getElementById('pay_add_self').value  = __num2str(__str2num(strAddSelfPay));


		/*********************************************************
			장애활동지원 합계
		*********************************************************/
		document.getElementById('pay_stnd_tot').value = __num2str(stnd_max_pay);
		document.getElementById('pay_add_tot').value  = __num2str(atrAddMaxPay);
		document.getElementById('pay_sido_tot').value = document.getElementById('pay_sido_use').value;
		document.getElementById('pay_jach_tot').value = document.getElementById('pay_jach_use').value;


		/*********************************************************
			총이용합계
		*********************************************************/
		document.getElementById('pay_total_tot').value = __str2num(document.getElementById('pay_stnd_tot').value)
													   + __str2num(document.getElementById('pay_add_tot').value)
													   + __str2num(document.getElementById('pay_sido_tot').value)
													   + __str2num(document.getElementById('pay_jach_tot').value);
		document.getElementById('pay_total_tot').value = __num2str(document.getElementById('pay_total_tot').value);

		document.getElementById('pay_total_time').value = __str2num(document.getElementById('pay_stnd_time').value)
														+ __str2num(document.getElementById('pay_add_time').value)
														+ __str2num(document.getElementById('pay_sido_time').value)
														+ __str2num(document.getElementById('pay_jach_time').value);
		document.getElementById('pay_total_time').value = __num2str(document.getElementById('pay_total_time').value);

		document.getElementById('pay_total_use').value = __str2num(document.getElementById('pay_stnd_use').value)
													   + __str2num(document.getElementById('pay_add_use').value)
													   + __str2num(document.getElementById('pay_sido_use').value)
													   + __str2num(document.getElementById('pay_jach_use').value);
		document.getElementById('pay_total_use').value = __num2str(document.getElementById('pay_total_use').value);

		document.getElementById('pay_total_self').value = __str2num(document.getElementById('pay_stnd_self').value)
														+ __str2num(document.getElementById('pay_add_self').value)
														+ __str2num(document.getElementById('pay_sido_self').value)
														+ __str2num(document.getElementById('pay_jach_self').value);
		document.getElementById('pay_total_self').value = __num2str(document.getElementById('pay_total_self').value);


		var kupyeoMax = document.getElementById(svc_id+'_kupyeoMax'); //총지원금
		var kupyeo1   = document.getElementById(svc_id+'_kupyeo1');   //본인부담금
		var addTime1  = document.getElementById(svc_id+'_addTime1'); //시도비시간
		var addTime2  = document.getElementById(svc_id+'_addTime2'); //자치비시간

		kupyeoMax.value = __str2num(document.getElementById('pay_total_tot').value);
		kupyeo1.value   = __str2num(document.getElementById('pay_total_self').value);
		addTime1.value  = document.getElementById('pay_sido_time').value;
		addTime2.value  = document.getElementById('pay_jach_time').value;

		set_svc_tot_time(svc_id);
	}else{
		var kupyeoMax = document.getElementById(svc_id+'_kupyeoMax'); //총지원금
		var kupyeo1   = document.getElementById(svc_id+'_kupyeo1');   //본인부담금

		kupyeoMax.value = __num2str(cutOff(parseInt(max_pay, 10)));
		kupyeo1.value   = __num2str(cutOff(parseInt(suga_pay1, 10)));

		if (svc_id == '22'){
			set_svc_tot_time(svc_id);
		}
	}
}

// 노인돌봄 서비스시간 셋팅
function set_svc_time(obj, value, result_f){
	var svc_id   = __get_svc_code(obj);
	var code     = document.getElementById('code').value;
	var obj      = document.getElementsByName(obj);
	var val      = __object_get_value(obj);
	var from_dt  = document.getElementById(svc_id+'_gaeYakFm').value;
	var to_dt    = document.getElementById(svc_id+'_gaeYakTo').value;
	var today    = new Date();

	from_dt = today.getFullYear() + '-' + ((today.getMonth() + 1 < 10 ? '0' : '')+(today.getMonth() + 1)) + '-' + ((today.getDate() < 10 ? '0' : '')+today.getDate());

	if (!result_f) result_f = 'result_svc_time';

	var svc_time = __get_url_data('client_reg_value.php', svc_id, {'gbn':'svc_time','code':code,'val':val,'value':value,'svc_id':svc_id,'svc_gbn':'','date':from_dt}, result_f);
}

// 서비스시간 설정 결과
function result_svc_time(svc_id, result){
	var body = __getObject(svc_id+'_svc_time');
	var rst = result.split(';');
	var txt = '';

	for(var i=0; i<rst.length-1; i++){
		var val = rst[i].split('/');

		if (svc_id == '22'){
			if (val[0] == 'V')
				var str = '시간';
			else
				var str = '일';
		}else if (svc_id == '23'){
			var str = '일';
		}else{
			var str = '시간';
		}

		//산모신생아
		if (svc_id == 23) val[4] = val[2];
		if (val[2] == val[4])
			var chk = 'checked';
		else
			var chk = '';

		txt += '<input name=\''+svc_id+'_lvl\' type=\'radio\' class=\'radio\' value=\''+val[2]+'\' tag=\''+val[4]+'\' time=\''+val[3]+'\' onclick=\'check_time("'+svc_id+'",__object_get_value("'+svc_id+'_gbn"),"1","");\' '+chk+'>';
		txt += '<a href=\'#\' onclick=\'check_obj("'+svc_id+'_lvl", '+i+', "'+svc_id+'",__object_get_value("'+svc_id+'_gbn"),"1",""); return false;\'>'+val[3]+str+'</a>';
	}

	body.innerHTML = txt;

	check_time(svc_id,__object_get_value(svc_id+'_gbn'),'1','');
}

// 총서비스 시간
function set_svc_tot_time(svc_id){
	var mon_time = __getObject(svc_id+'_overTime');
	var tot_time = __getObject(svc_id+'_kupyeo2');
	var svc_gbn  = __object_get_value(svc_id+'_gbn');
	var time     = 0;

	time = parseInt(__str2num(mon_time.value), 10);

	if (svc_id == '22'){
		if (svc_gbn == 'V'){
			time += parseInt(__str2num(__object_get_att(svc_id+'_lvl', 'time')));
		}else{
			time += parseInt(__str2num(__object_get_att(svc_id+'_lvl', 'time')) * 3);
		}
	}else if (svc_id == '24'){
		time += parseInt(document.getElementById(svc_id+'_sugaTime').value, 10);
	}

	tot_time.value = time;
}

/*
 * 비급여 실비지급여부
 */
function set_expense_pay(svc_id){
	var yn  = __object_get_value(svc_id+'_expense_yn');
	var pay = document.getElementById(svc_id+'_expense_pay');

	__object_enabled(pay, (yn == 'Y' ? true : false));
}

function set_home_in_pay(svc_id){
	var yn  = __object_get_value(svc_id+'_home_in_yn');
	var pay = document.getElementById(svc_id+'_home_in_pay');

	__object_enabled(pay, (yn == 'Y' ? true : false));
}

// 리스트
function client_list(page){
	var f = document.f;

	page = __str2num(page);

	if (page < 1) page = 1;

	f.page.value = page;
	f.action = 'client_list.php';
	f.submit();
}

// 저장
function client_save(ab_temp){
	if (!ab_temp){
		if ($('#lbTestMode').val()){
			var lsCode  = $('#code').val();
			var lsJumin = '';
			if ($('#writeMode').val() == 1){
				lsJumin = $('#jumin1').val()+$('#jumin2').val();
			}else{
				lsJumin = $('#jumin').val();
			}

			_clientChkSave(lsCode,lsJumin);
			return;
		}
	}


	var f = document.f;
	var count = f.elements.length;
	var write_mode = f.write_mode.value;
	var svc_selected = false;
	var svc_save = f.svc_save;
	var rst_svc_save = svc_save.value;

	if (write_mode == 1){
		//if (!_check_ssn('su', f.jumin1, f.jumin2, f.code)) return;
		if (f.jumin1.value.length != 6 || f.jumin2.value.length != 7){
			//find_counsel();
			alert('수급자 주민번호를 입력하여 주십시오.');
			f.jumin1.focus();
			return;
		}
		if (!__alert(f.name)) return;
	}

	var k_list  = document.getElementsByName('kind_list[]');

	if ($('#lbTestMode').val()){
	}else{
		var lastDt = new Array();

		for(var i=0; i<k_list.length; i++){
			var val = k_list[i].value.split('_')[1];

			lastDt[i] = {'cd':val,'dt':document.getElementById(val+'_lastDt').value};
		}
	}

	for(var i=0; i<count; i++){
		var el = f.elements[i];

		if (el.name.indexOf('use_svc_') >= 0){
			var svc_cd = __get_svc_code(el); //서비스코드

			if (el.checked){
				var useCd   = document.getElementById('use_svc_'+svc_cd); //서비스코드
				var firstDt = document.getElementById(svc_cd+'_firstDt'); //일정첫등록일
				var fromDt  = document.getElementById(svc_cd+'_gaeYakFm');//계약시작일
				var toDt    = document.getElementById(svc_cd+'_gaeYakTo');//계약종료일

				if ($('#lbTestMode').val()){
					if (ab_temp['max_'+useCd.value]){
						if (__replace(ab_temp['max_'+useCd.value],'-','') > __replace(fromDt.value,'-','') &&
							__replace(fromDt.value,'-','') != __replace(fromDt.tag,'-','')){

							if (ab_temp['min_'+useCd.value]){
								if (__replace(ab_temp['min_'+useCd.value],'-','') < __replace(fromDt.value,'-','')){
									alert('계약일이전에 등록된 일정이 있어 계약기간을 수정할 수 없습니다. 확인하여 주십시오.');
									fromDt.focus();
									return;
								}
							}
						}
					}
				}else{
					for(var j=0; j<lastDt.length; j++){
						if (svc_cd == lastDt[j]['cd'] &&
							__replace(lastDt[j]['dt'],'-','') > __replace(fromDt.value,'-','') &&
							__replace(fromDt.value,'-','') != __replace(fromDt.tag,'-','')){

							var svc_nm1 = document.getElementById(svc_cd+'_svcNm').value;
							var svc_nm2 = document.getElementById(lastDt[j]['cd']+'_svcNm').value;

							if (__replace(firstDt.value,'-','') < __replace(fromDt.value,'-','')){
								alert('계약일('+fromDt.value+')이전에 등록된 일정('+firstDt.value+')이 있어 계약기간을 수정할 수 없습니다. 확인하여 주십시오.');
								fromDt.focus();
								return;
							}
						}
					}

					if (__replace(firstDt.value,'-','') < __replace(fromDt.value,'-','')){
						alert('계약일('+fromDt.value+')이전에 등록된 일정('+firstDt.value+')이 있어 계약기간을 수정할 수 없습니다. 확인하여 주십시오.');
						fromDt.focus();
						return;
					}
				}

				if (!checkDate(fromDt.value)){
					alert('계약기간 시작일자 입력오류입니다. 확인하여 주십시오.');
					fromDt.focus();
					return;
				}

				var startDt   = document.getElementById(svc_cd+'_startDt'); //적용일
				var historyYn = document.getElementById(svc_cd+'_historyYn'); //변경내역저장여부
				var writeMode = document.getElementById(svc_cd+'_writeMode'); //등록모드
				var stat      = __object_get_value(svc_cd+'_sugupStatus'); //상태

				if (writeMode.value == 1){
					var kupyeo1 = document.getElementById(svc_cd+'_kupyeo1');

					if (svc_cd > 30 && svc_cd < 40){
						if (kupyeo1 != null){
							if (kupyeo1.tag != undefined){
								if (__str2num(kupyeo1.value) == 0){
									alert('금액을 입력하여 주십시오.');
									kupyeo1.focus();
									return false;
								}
							}
						}
					}

					startDt.value   = fromDt.value;
					historyYn.value = 'N';
					//svc_save.value  = rst_svc_save;
				}else{
					var change_flag = false;

					if (stat == 1 || stat == 3 || stat == 6 || stat == 7){
					}else{
						if (!checkDate(toDt.value)){
							alert('계약기간 종료일자 입력오류입니다. 확인하여 주십시오.');
							toDt.focus();
							return false;
						}
					}

					if (useCd.value == 0){
						// 재가요양만 담당요양보호사 변경일을 확인한다.
						var mem_cd = document.getElementById(svc_cd+'_mem_cd1');

						if (mem_cd.value != mem_cd.tag){
							change_flag = true;
							//alert(svc_cd+'/'+0+'/'+(mem_cd.value == mem_cd.tag));
						}else{
							historyYn.value = 'N';
						}
					}else{
						if (stat == 2){
							if (!checkDate(toDt.value)){
								alert('계약기간 종료일자 입력오류입니다. 확인하여 주십시오.');
								toDt.focus();
								return false;
							}
						}

						if (__replace(startDt.value,'-','') == startDt.tag){
							historyYn.value = 'N';
						}
					}

					var lvl         = document.getElementsByName(svc_cd+'_lvl');         //
					var kind        = document.getElementsByName(svc_cd+'_kind');        //
					var gaeYakFm    = document.getElementById(svc_cd+'_gaeYakFm');       //계약기간
					var gaeYakTo    = document.getElementById(svc_cd+'_gaeYakTo');       //계약기간
					var sugupStatus = document.getElementsByName(svc_cd+'_sugupStatus'); //수급 및 이용상태
					var boninYul    = document.getElementById(svc_cd+'_boninYul');       //
					var kupyeoMax   = document.getElementById(svc_cd+'_kupyeoMax');
					var kupyeo1     = document.getElementById(svc_cd+'_kupyeo1');        //
					var kupyeo2     = document.getElementById(svc_cd+'_kupyeo2');        //
					var gbn         = document.getElementsByName(svc_cd+'_gbn');         //
					var gbn2        = document.getElementsByName(svc_cd+'_gbn2');

					if (lvl != null){
						if (__object_get_att(lvl,'value') != __object_get_att(lvl,'tag')){
							change_flag = true;
							//alert(svc_cd+'/'+1);
						}
					}

					if (kind != null){
						if (__object_get_att(kind,'value') != __object_get_att(kind,'tag')){
							change_flag = true;
							//alert(svc_cd+'/'+2);
						}
					}

					if (gaeYakFm != null){
						if (__replace(gaeYakFm.value,'-','') != __replace(gaeYakFm.tag,'-','')){
							change_flag = true;
							//alert(svc_cd+'/'+3);
						}
					}

					/*
					 - 계약기간은 이력내역으로 남기다.
					if (gaeYakTo != null){
						if (__replace(gaeYakTo.value,'-','') != __replace(gaeYakTo.tag,'-','')){
							change_flag = true;
							//alert(svc_cd+'/'+4);
						}
					}
					*/

					if (sugupStatus != null){
						if (__object_get_att(sugupStatus,'value') != __object_get_att(sugupStatus,'tag')){
							change_flag = true;
							//alert(__object_get_att(sugupStatus,'value') + '/' + __object_get_att(sugupStatus,'tag'));
							//alert(svc_cd+'/'+5);
						}
					}

					if (boninYul != null){
						if (boninYul.tag != undefined){
							if (__round(boninYul.value,1) != __round(boninYul.tag, 1)){
								change_flag = true;
								//alert(svc_cd+'/'+6);
							}
						}
					}

					if (kupyeo1 != null){
						if (kupyeo1.tag != undefined){
							if (svc_cd > 30 && svc_cd < 40){
								if (__str2num(kupyeo1.value) == 0){
									alert('금액을 입력하여 주십시오.');
									kupyeo1.focus();
									return false;
								}
							}
							if (__replace(kupyeo1.value,',','') != __replace(kupyeo1.tag,',','')){
								change_flag = true;
								//alert(__replace(kupyeo1.value,',','') + '/' + __replace(kupyeo1.tag,',',''));
								//alert(svc_cd+'/'+7);
							}

							if (!change_flag && svc_cd == '11'){
								//alert(__str2num(kupyeo1.value) + '/' + __str2num(kupyeoMax.value));
								if (__str2num(kupyeo1.value) == __str2num(kupyeoMax.value)){
									kupyeo1.value = '0';
								}
							}
						}
					}

					if (kupyeo2 != null){
						if (kupyeo2.tag != undefined){
							if (__replace(kupyeo2.value,',','') != __replace(kupyeo2.tag,',','')){
								change_flag = true;
								//alert(svc_cd+'/'+8);
							}
						}
					}

					if (gbn != null){
						if (__object_get_att(gbn,'value') != __object_get_att(gbn,'tag')){
							change_flag = true;
							//alert(svc_cd+'/'+9);
						}
					}

					if (gbn2 != null){
						if (__object_get_att(gbn2, 'value') != __object_get_att(gbn2, 'tag')){
							change_flag = true;
							//alert(svc_cd+'/'+10);
							//alert(svc_cd+'/'+__object_get_att(gbn2, 'value') + '/' + __object_get_att(gbn2, 'tag'));
						}
					}

					if (!change_flag){
						//변경내역이 없다.
					}else{
						//변동내역 저장이 필요한 경우 일자를 받을 레이어를 보여준다.
						show_layer(svc_cd, true);
						svc_save.value = 3;
						//return;
					}

					if (svc_save.value != 3)
						svc_save.value = 2;
				}

				/*
				 * 비급여 실비지급여부 및 실비금액을 확인한다.
				 */
				var yn  = __object_get_value(svc_cd+'_expense_yn');
				var pay = document.getElementById(svc_cd+'_expense_pay');

				if (yn == 'Y'){
					if (__str2num(pay.value) == 0){
						alert('비급여 실지지급금액을 입력하여 주십시오.');
						pay.focus();
						return false;
					}
				}

				svc_selected = true;
			}
		}
	}

	if (!svc_selected){
		alert('이용하실 서비스를 하나이상 선택하여 주십시오.');
		return;
	}

	if ((svc_save.value == rst_svc_save || historyYn.value == 'N') && svc_save.value != 3){
		$('#para').val(_clientSetPara());

		f.action = 'client_save.php';
		f.submit();
	}else{
		svc_save.value = 1;
	}
}

/*
function client_save_layer(svc_cd){
	var f = document.f;
	var count = f.elements.length;

	for(var i=0; i<count; i++){
		var el = f.elements[i];

		if (el.name.indexOf('use_svc_') >= 0){
			var el_code   = __get_svc_code(el);
			var startDt   = document.getElementById(el_code+'_startDt'); //적용일
			var historyYn = document.getElementById(el_code+'_historyYn');

			if (__replace(startDt.value, '-', '') < startDt.tag){
				alert('적용일은 현재의 적용일보다 커야합니다. 확인하여 주십시오.');
				startDt.focus();
				return;
			}

			if (__replace(startDt.value,'-','') == startDt.tag)
				historyYn.value = 'N';
			else
				historyYn.value = 'Y';
		}
	}

	f.action = 'client_reg_save.php';
	f.submit();
}
*/

//레이어를 띄운다.
/*
function show_layer(svc_cd, btn_show){
	var obj_body        = document.getElementById('svc_body_'  +svc_cd);
	var obj_layer_body  = document.getElementById('layer_body_'+svc_cd);
	var obj_layer_cont  = document.getElementById('layer_cont_'+svc_cd);
	var obj_leyer_close = document.getElementById(svc_cd+'_layerClose');
	var obj_leyer_btn   = document.getElementById(svc_cd+'_layerBtn');
	var obj_start_dt    = document.getElementById(svc_cd+'_startDt');
	var obj_end_dt      = document.getElementById(svc_cd+'_gaeYakTo');

	obj_leyer_close.style.display = (btn_show?'':'none');
	obj_leyer_btn.style.display   = (btn_show?'':'none');
	//obj_start_dt.readOnly         = (btn_show?false:true);

	//__init_object(obj_start_dt);

	if (!btn_show)
		obj_start_dt.value = obj_end_dt.value;

	obj_body.style.width    = <?=$tbl_width;?>;
	obj_body.style.display  = '';
	obj_body.style.position = '';

	obj_layer_body.style.width  = obj_body.offsetWidth  - 20;
	obj_layer_body.style.height = obj_body.offsetHeight - 11;
	obj_layer_body.style.display= '';

	obj_layer_cont.style.width  = obj_body.offsetWidth  - 20;
	obj_layer_cont.style.height = obj_body.offsetHeight - 11;
	obj_layer_cont.style.display= '';
}
*/

// 레이어 숨기기
/*
function hidden_layer(svc_cd){
	var obj_layer_body = document.getElementById('layer_body_'+svc_cd);
	var obj_layer_cont = document.getElementById('layer_cont_'+svc_cd);

	obj_layer_body.style.width  = 0;
	obj_layer_body.style.height = 0;
	obj_layer_body.style.display= 'none';

	obj_layer_cont.style.width  = 0;
	obj_layer_cont.style.height = 0;
	obj_layer_cont.style.display= 'none';
}
*/

//계약기간연결하기
/*
function check_from_to_dt(svc_cd, obj_end){
	return;

	var f = document.f;
	var count = f.elements.length;

	for(var i=0; i<count; i++){
		var el = f.elements[i];

		if (el.name.indexOf('use_svc_') >= 0){
			var el_code = __get_svc_code(el);

			if (svc_cd != el_code){
				var body = document.getElementById('svc_body_'+el_code);

				if (body.style.position == ''){
					var start_dt = document.getElementById(el_code+'_startDt');

					if (__replace(start_dt.tag , '-', '') < __replace(obj_end.value, '-', ''))
						start_dt.value = __getDate(obj_end.value);
					else
						start_dt.value = __getDate(start_dt.tag);

					break;
				}
			}
		}
	}
}
*/

function show_service(){
	show_svc_layer('service');
}

//function show_family(){
//	show_svc_layer('family');
//}


function show_svc_layer(type){
	var menu_1 = document.getElementById('menu_1');
	var menu_2 = document.getElementById('menu_2');
	var menu_3 = document.getElementById('menu_3');


	var menu_4 = document.getElementById('menu_4');


	var body_1 = document.getElementById('svc_counsel');
	var body_2 = document.getElementById('stnd_body');
	var body_3 = document.getElementById('svc_record_menu');


	var body_4 = document.getElementById('contract_body');


	menu_1.style.fontWeight = 'normal';
	menu_2.style.fontWeight = 'normal';
	menu_3.style.fontWeight = 'normal';


	menu_4.style.fontWeight = 'normal';


	menu_1.style.color = '#000000';
	menu_2.style.color = '#000000';
	menu_3.style.color = '#000000';


	menu_4.style.color = '#000000';


	if (type == 'counsel'){
		menu_1.style.fontWeight = 'bold';
		menu_1.style.color = '#0000FF';
	}else if (type == 'service'){
		menu_2.style.fontWeight = 'bold';
		menu_2.style.color = '#0000FF';
	}else if (type == 'record'){
		menu_3.style.fontWeight = 'bold';
		menu_3.style.color = '#0000FF';
	}else if (type == 'contract'){
		menu_4.style.fontWeight = 'bold';
		menu_4.style.color = '#0000FF';
	}else{
		return;
	}

	body_1.style.display = 'none';
	body_2.style.left = -10000;
	body_2.style.position = 'absolute';
	body_3.style.display = 'none';


	body_4.style.display = 'none';


	if (type == 'counsel'){
		body_1.style.display = '';
	}else if (type == 'service'){
		body_2.style.left = 0;
		body_2.style.position = '';
	}else if (type == 'record'){
		body_3.style.display = '';
	}else if (type == 'contract'){
		body_4.style.display = '';
	}else{
		return;
	}

	if (type == 'counsel' || type == 'contract'){
		set_button(1);
	}else if (type == 'service'){
		set_button(4);
	}else{
		set_button(2);
	}
}

function set_button(mode){
	var btn_1 = document.getElementsByName('btn_list');
	var btn_2 = document.getElementsByName('btn_save');
	var btn_3 = document.getElementsByName('btn_write');
	var btn_4 = document.getElementsByName('btn_cancel');

	/*
	btn_2[0].style.display = 'none';
	btn_2[1].style.display = 'none';
	btn_3[0].style.display = 'none';
	btn_3[1].style.display = 'none';
	//btn_4[0].style.display = 'none';
	//btn_4[1].style.display = 'none';

	if (mode == 1){
		btn_2[0].style.display = '';
		btn_2[1].style.display = '';
	}else if (mode == 2){
		btn_3[0].style.display = '';
		btn_3[1].style.display = '';
	}else if (mode == 3){
		btn_2[0].style.display = '';
		btn_2[1].style.display = '';
		//btn_4[0].style.display = '';
		//btn_4[1].style.display = '';
	}
	*/

	btn_1[0].style.display = 'none';
	btn_2[0].style.display = 'none';
	btn_3[0].style.display = 'none';
	btn_4[0].style.display = 'none';

	if (mode == 1){
		btn_2[0].style.display = '';
	}else if (mode == 2){
		btn_1[0].style.display = '';
		btn_2[0].style.display = '';
		btn_3[0].style.display = '';
	}else if (mode == 3){
		btn_1[0].style.display = '';
		btn_2[0].style.display = '';
		btn_4[0].style.display = '';
	}else{
		btn_1[0].style.display = '';
		btn_2[0].style.display = '';
	}
}

function chk_clientno(no){
	var code = document.getElementById('code').value;
	var rst  = getHttpRequest('../inc/_chk_ssn.php?id=140&code='+code+'&ssn='+no.value);

	if (rst == 'Y'){
		alert('입력하신 번호는 사용중인 번호입니다. 다른 번호를 입력하여 주십시오.');
		no.value = '';
		no.focus();
		return false;
	}
	return true;
}

function init_form(){
	var f = document.f;
	var count = f.elements.length;

	/*
	if (f.write_mode.value == 1){
		for(var i=0; i<count; i++){
			var el = f.elements[i];

			if (el.name.indexOf('use_svc_') >= 0){
				__setEnabled(el, false);
			}
		}
	}
	*/

	for(var i=0; i<count; i++){
		var el = f.elements[i];

		if (el.name.indexOf('use_svc_') >= 0){
			el_id = __get_svc_code(el);

			switch(el_id){
				case '11':
					// 재가요양
					var sick_val = __object_get_value('11_byungMung');

					check_sick('11_byungMung',sick_val);
					check_partner('11_mem_cd1', '11_partner');

					/*
					var lvl_cd = __object_get_value('11_lvl');

					set_max_pay('11', __getObject('11_kupyeoMax'), lvl_cd);
					set_my_yul('11', __get_value(document.getElementsByName('11_kind')), __get_tag(document.getElementsByName('11_kind')));
					set_expense_pay('11');
					*/
					break;

				case '21':
					//가사간병
					//check_time(el_id,'0','1','');
					//set_expense_pay('21');
					break;

				case '22':
					//노인돌봄
					//var tmp = document.getElementById(el_id+'_tmp_lvl').value;
					//set_svc_time(el_id+'_gbn',tmp,'');
					//set_expense_pay('22');
					break;

				case '23':
					//산모신생아
					//set_svc_time(el_id+'_gbn','1','');
					//set_expense_pay('23');
					//set_home_in_pay('23');
					break;

				case '24':
					//장애인보조
					//check_time(el_id,__object_get_value(el_id+'_gbn'),__object_get_value(el_id+'_lvl'),__object_get_value(el_id+'_gbn2'));
					//set_expense_pay('24');
					break;

				case '31':
					//산모유료
					//set_home_in_pay('31');
					break;

				default:
			}

			var stat = __object_get_value(el_id+'_sugupStatus');
				stat = (stat != '1' ? '1' : '2');

			//check_status(el_id,stat);
		}
	}
}

function show_ssn(){
	var ssn_body = document.getElementById('ssn_body');

	var URL   = '../inc/_show_ssn.php';
	var param = {'ssn':document.getElementById('jumin').value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				ssn_body.innerHTML = responseHttpObj.responseText;

				alert(document.getElementById('str_ssn').value);

				ssn_body.innerHTML = '';
			}
		}
	);
}

function lfSvcShow(svcCd){
	var menu = $('#loSvc_'+svcCd);
	var link = $('#loLink_'+svcCd);

	if ($(menu).css('display') != 'none'){
		$(menu).hide();
		$(link).css('font-weight','normal');
	}else{
		if ($('#writeMode').val() == 1){
			$(menu).css('margin','10px')
				   .css('width','407px').show();
		}else{
			$(menu).show();
		}

		$(link).css('font-weight','bold');
	}

	return false;
}

function lfSvcPop(svcCd){
	var menu = $('#loSvc_'+svcCd);

	var w = $(document).width();
	var h = $(document).height();
	var l = (w - $(menu).width()) / 2;
	var t = 300;

	$(menu).css('top',t)
		   .css('left',l)
		   .css('position','absolute').show();

	return false;
}

function lfSvcDisplay(svcCd,svcId,aiShow){
	if (!aiShow) aiShow = 1;

	if (aiShow == 1 || aiShow == 2){
		if ($('#loSvcCd_'+svcCd).attr('value') == 'Y'){
			lfSvcReload(svcCd,svcId);
		}else{
			lfSvcMenu(2, svcCd);
		}
	}else{
		if (svcCd == '0'){
			$('.familyRow').remove();
		}

		$('#loSvc_'+svcCd).remove();
	}
}

function lfSvcReload(svcCd,svcId){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();

	if (!jumin || jumin == 'undefined') jumin = '';

	$.ajax({
		type: 'POST'
	,	url : './client_menu_use.php'
	,	beforeSend: function(){
			$('#loadingBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
		}
	,	data: {
			'code'  : code
		,	'jumin' : jumin
		,	'svcCd' : svcCd
		,	'svcId' : svcId
		}
	,	success: function(html){
			$('#tempLodingBar').remove();
			if (svcCd == '0'){
				$('.familyRow').remove();
			}
			$('#loSvc_'+svcCd).after(html).remove();

			if (svcCd == '0'){
				lfLoadFamily();
			}
		}
	}).responseXML;

	return false;
}

function lfLoadFamily(){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();

	if (!jumin || jumin == 'undefined') jumin = '';

	$.ajax({
		type: 'POST'
	,	url : './client_load_family.php'
	,	data: {
			'code'  : code
		,	'jumin' : jumin
		}
	,	success: function(html){
			var list = html.split(';');

			for(var i=0; i<list.length-1; i++){
				var val = __parseStr(list[i]);
				_clientFamilyAddRow(val['nm'],val['cd'],val['kind']);
			}
		}
	}).responseXML;
}

function lfSvcDisplayExec(svcCd){
	/*
	$('div[id^="loSvcCd_"]').each(function(){
		if ($(this).attr('value') == 'Y'){
			$('#'+$(this).attr('id').split('Cd').join('')).show();
		}else{
			$('#'+$(this).attr('id').split('Cd').join('')).hide();
		}
	});
	*/

	var menu = $('#loSvc_'+svcCd);

	if ($('#loSvcCd_'+svcCd).attr('value') == 'Y'){
		if ($(menu).css('display') != 'none') return;
	}else{
		if ($(menu).css('display') == 'none') return;
	}

	lfSvcShow(svcCd);
}

function lfSvcMenu(mode, svcCd){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();

	if (!jumin || jumin == 'undefined') jumin = '';

	$.ajax({
		type: 'POST'
	,	url : './client_menu.php'
	,	beforeSend: function(){
			$('#loadingBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
		}
	,	data: {
			'code'  : code
		,	'jumin' : jumin
		,	'mode'  : mode
		}
	,	success: function(html){
			$('#tempLodingBar').remove();
			$('.familyRow').remove();
			$('div[id="loSvcHistory"]').remove();
			$('div[id^="loSvc_"]').remove();
			$('div[id="loSvcMenu"]').after(html).remove();

			if (mode == 2){
				lfSvcDisplayExec(svcCd);
			}

			if ($('#writeMode').val() == 1 && $('div[id^="loSvc_"]').length == 1){
				var lsSvcCd = $('div[id^="loSvc_"]:first').attr('id').split('loSvc_').join('');
				lfSvcShow(lsSvcCd);
			}
		}
	}).responseXML;

	return false;
}

function lfSvcHistory(){
	var code  = $('#code').val();
	var jumin = $('#jumin').val();

	if (!jumin || jumin == 'undefined') jumin = '';

	if (!$('#loSvcHistory').html()){
		$.ajax({
			type: 'POST'
		,	url : './client_svc_history.php'
		,	beforeSend: function(){
				//$('#loadingBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
			}
		,	data: {
				'code'  : code
			,	'jumin' : jumin
			}
		,	success: function(html){
				$('#loSvcHistory').css('margin','10px').after(html).remove();
			}
		}).responseXML;
	}else{
		$('#loSvcHistory').html('').hide();
	}

	return false;
}

function lfSvcSave(ab_temp){
	if (!ab_temp){
		var lsCode  = $('#code').val();
		var lsJumin = '';

		//if ('<?=$debug;?>' == '1'){
			lsJumin = $('#jumin').val();
		//}else{
		//	if ($('#writeMode').val() == 1){
		//		lsJumin = $('#jumin1').val()+$('#jumin2').val();
		//	}else{
		//		lsJumin = $('#jumin').val();
		//	}
		//}

		_clientChkSave(lsCode,lsJumin);
		return;
	}

	var f = document.f;
	var writeMode = f.write_mode.value;

	if (writeMode == 1){
		//if ('<?=$debug;?>' == '1'){
			if ($('#jumin1').val().length != 6){
				alert('수급자의 생년월일을 6자리로 입력하여 주십시오.');
				$('#jumin1').focus();
				return;
			}

			if($('input:radio[name=gender]').is(':checked')==false){
				alert('성별을 선택하여 주십시오.');
				$('#gender1').focus();
				return;
			}

		//}else{
		//	if (f.jumin1.value.length != 6 || f.jumin2.value.length != 7){
		//		alert('수급자 주민번호를 13자리 숫자로 입력하여 주십시오.');
		//		f.jumin1.focus();
		//		return;
		//	}
		//}
		if (!__alert(f.name)) return;
	}

	var liCnt  = 0;
	var lbExit = false;

	$('div[id^="loSvc_"]').each(function(){
		var lsCd = $(this).attr('value').split('_')[0];
		var lsId = $(this).attr('value').split('_')[1];

		if ($('#txtFrom_'+lsId).attr('value') != '' &&
			$('txtTo_'+lsId).attr('value') != ''){
			liCnt ++;
		}

		if ($(this).css('display') != 'none'){
			var lsSvcNm = '';

			switch(lsCd){
				case '0': lsSvcNm = '재가요양'; break;
				case '1': lsSvcNm = '가사간병'; break;
				case '2': lsSvcNm = '노인돌봄'; break;
				case '3': lsSvcNm = '산모신생아'; break;
				case '4': lsSvcNm = '장애인활동지원'; break;
				case 'A': lsSvcNm = '산모유료'; break;
				case 'B': lsSvcNm = '병원간병'; break;
				case 'C': lsSvcNm = '기타비급여'; break;
			}

			if (!$('#txtFrom_'+lsId).attr('value') || !$('#txtTo_'+lsId).attr('value')){
				alert('"'+lsSvcNm+'"의 계약기간을 입력하여 주십시오.');
				lbExit = true;
				return false;
			}

			if (lsCd == '0'){
				if (!$('#mgmtNo').attr('value')){
					alert('"재가요양"의 장기요양보험 인정번호를 입력하여 주십시오.');
					lbExit = true;
					return false;
				}

				if (!$('#expenseKind_'+lsId).attr('value')){
					alert('"'+lsSvcNm+'"의 수급자구분을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}
			}else if (lsCd == '1'){
				if (!$('#nusreSeq').attr('value')){
					alert('"'+lsSvcNm+'"의 서비스시간을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}

				if (!$('#nusreLvlSeq').attr('value')){
					alert('"'+lsSvcNm+'"의 소득등급을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}
			}else if (lsCd == '2'){
				if (!$('#oldSeq').attr('value')){
					alert('"'+lsSvcNm+'"의 서비스시간을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}

				if (!$('#oldLvlSeq').attr('value')){
					alert('"'+lsSvcNm+'"의 소득등급을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}
			}else if (lsCd == '3'){
				if (!$('#babySeq').attr('value')){
					alert('"'+lsSvcNm+'"의 서비스시간을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}

				if (!$('#babyLvlSeq').attr('value')){
					alert('"'+lsSvcNm+'"의 소득등급을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}
			}else if (lsCd == '4'){
				
				var disSeq = $('#disSeq').attr('value') + $('#disSeq2').attr('value');
				var disLvlSeq = $('#disLvlSeq').attr('value') + $('#disLvlSeq2').attr('value');
				
				if (!disSeq){	
					alert('"'+lsSvcNm+'"의 서비스시간을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}

				if (!disLvlSeq){
					alert('"'+lsSvcNm+'"의 소득등급을 입력하여 주십시오.');
					lbExit = true;
					return false;
				}
			}
		}
	});

	if (liCnt == 0){
		alert('등록할 서비스를 하나이상 선택하여 주십시오.');
		return;
	}

	if (lbExit){
		return;
	}

	try{
		$('input:file[id^="attachFile_"]').each(function(){
			if (!__checkImageExp2(this,true)){
				_ccAttachDel($(this).parent().parent(),false);
			}
		});
	}catch(e){
	}

	var para = _clientSetPara();

	$('#para').val(para);

	f.action = './client_save.php';
	f.submit();
}

function lfTeamPop(svcCd){
	var width = 500;
	var height = 400;
	var left = (screen.availWidth - width) / 2;
	var top = (screen.availHeight - height) / 2;

	var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
	var win = window.open('', 'TEAM_POP', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			'svcCd'	:svcCd
		,	'jumin'	:$('#jumin').val()
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

	form.setAttribute('target', 'TEAM_POP');
	form.setAttribute('method', 'post');
	form.setAttribute('action', 'team.php');

	document.body.appendChild(form);

	form.submit();
}

function lfTeamResult(teamNm){
	$('#ID_TEAM').text(teamNm);
}

function lfShowPlanRequest(){

	var arguments	= 'root=sugupja'
					+ '&dir=P'
					+ '&fileName=plan_request'
					+ '&fileType=pdf'
					+ '&target=show.php'
					+ '&jumin='+$('#jumin').val()
					;

	__printPDF(arguments);
}


$(document).ready(function(){
	lfSvcMenu(1);
});

window.onload = function(){
	init_counsel();

	__init_form(document.f);

	if (document.getElementById('write_mode').value == 2){
		$.ajax({
			type : 'POST',
			url  : './client_reg_isdel.php',
			data : {
				'code':document.getElementById('code').value
			,	'jumin':document.getElementById('jumin').value
			},
			success: function (data){
				if (data){
					$('#lcLayer').html(data).show();
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

	var current_menu = document.getElementById('current_menu').value;

	switch(current_menu){
		case 'counsel':
			show_mode = 'counsel';
			show_counsel();
			break;

		case 'record':
			show_mode = 'record';

			var record_menu = document.getElementById('record_menu').value;

			if (record_menu == '') record_menu = 'record_visit';

			show_record_menu(record_menu);
			break;

		case 'service':
			show_mode = 'service';
			document.getElementById('current_menu').value = 'service';
			show_service();
			break;

		case 'family':
			show_mode = 'family';
			document.getElementById('current_menu').value = 'family';
			show_family();
			break;

		case 'contract':
			show_mode = 'contract';
			document.getElementById('current_menu').value = 'contract';
			show_contract();
			break;

		default:
	}

	if ($('#writeMode').val() == '1') return;
};


-->
</script>
<form name="f" method="post" enctype="multipart/form-data">
<div class="title"><?=$title;?></div>

<table id="stnd_head" class="my_table my_border">
	<colgroup>
		<col width="70px">
		<col width="130px">
		<col width="70px">
		<col>
		<col width="150px">
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$center_code;?></td>
			<th>기관명</th>
			<td class="left"><?=$center_name;?></td>
			<td class="left top last" rowspan="2"><?
				include_once('./client_chk_longtermcare.php');?>
			</td>
		</tr>
		<tr>
			<th>구분</th>
			<td class="left" colspan="3">
				<span class="btn_pack m" ><button id="menu_1" onclick="show_mode = 'counsel'; document.getElementById('current_menu').value = 'counsel'; document.getElementsByName('grp_btn')[0].style.display = ''; document.getElementsByName('grp_btn')[1].style.display = ''; show_counsel(); return false;">초기상담기록지</button></span>
				<span class="btn_pack m" ><button id="menu_2" onclick="show_mode = 'service'; document.getElementById('current_menu').value = 'service'; document.getElementsByName('grp_btn')[0].style.display = ''; document.getElementsByName('grp_btn')[1].style.display = ''; show_service(); return false;" style="font-weight:bold; color:#0000ff;">이용서비스</button></span><?
				if ($write_mode == 2){
					echo ' <span class=\'btn_pack m\' ><button id=\'menu_3\' onclick=\'show_mode = "record"; document.getElementById("current_menu").value = "record"; document.getElementsByName("grp_btn")[0].style.display = ""; document.getElementsByName("grp_btn")[1].style.display = ""; show_record_menu(); return false;\'>과정상담</button></span>';
				}else{
					echo ' <span class=\'btn_pack m\' ><button id=\'menu_3\' disabled>과정상담</button></span>';
				}


				if ($write_mode == 2){ ?>
					<span class="btn_pack m" ><button id="menu_4" onclick="show_mode = 'contract'; document.getElementById('current_menu').value = 'contract'; document.getElementsByName('grp_btn')[0].style.display = 'none'; document.getElementsByName('grp_btn')[1].style.display = 'none'; show_contract(); return false;">표준약관</button></span>
					<span class="btn_pack m" ><button id="menu_4" onclick="lfShowPlanRequest(); return false;">계획서제공요청서 및 동의서</button></span><?
				}else {
					echo ' <span class=\'btn_pack m\' ><button id=\'menu_4\' disabled>표준약관</button></span>';
				}

				?>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('./client_reg_info.php');

	echo group_button(false, $page);
?>

<input name="find_center_code"	type="hidden" value="<?=$find_center_code;?>">
<input name="find_center_name"	type="hidden" value="<?=$find_center_name;?>">
<input name="find_su_name"		type="hidden" value="<?=$find_su_name;?>">
<input name="find_su_phone"		type="hidden" value="<?=$find_su_phone;?>">
<input name="find_su_stat"		type="hidden" value="<?=$find_su_stat;?>">
<input name="find_center_kind"	type="hidden" value="<?=$find_center_kind;?>">
<input name="strTeam"			type="hidden" value="<?=$strTeam;?>">

<input id="writeMode" name="write_mode" type="hidden" value="<?=$write_mode;?>" value1="<?=$write_mode;?>">

<input id="code" name="code"  type="hidden" value="<?=$code;?>">
<input name="kind"  type="hidden" value="<?=$kind;?>">
<input name="page"  type="hidden" value="<?=$page;?>">

<input id="current_menu" name="current_menu" type="hidden" value="<?=$current_menu;?>">

<input name="svc_save" type="hidden" value=1>

<div id="loadingBody" style="position:absolute;"></div>
<div id="stnd_body"><?
	if ($lbTestMode){?>
		<div id="loSvcMenu" style="padding:0 10px 10px 10px; display:none;"></div><?
	}else{
		include_once('./client_reg_service_menu.php');
	}?>
</div><?

	/**************************************************

		초기상담기록지

		**********************************************/
		include_once('./client_reg_counsel.php');
	/*************************************************/



	/**************************************************

		상담기록지 메뉴

		**********************************************/
		include_once('./client_reg_record.php');
	/*************************************************/


	/*********************************************************

		가족요양보호사

		*****************************************************/
		//include_once('./client_reg_family.php');
	/********************************************************/


	/*********************************************************
	 *	이용계약서
	 *********************************************************/

		include_once('./client_reg_contract.php');



	echo group_button(true, $page);
?>

<div id="ssn_body" style="display:none;"></div>

<input id="lbTestMode" name="lbTestMode" type="hidden" value="<?=$lbTestMode;?>">
<input id="para" name="para" type="hidden">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
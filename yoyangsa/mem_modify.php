<?php
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_body_header.php');

	$code = $_SESSION['userCenterCode'];
	$kind_list = $conn->kind_list($code);

	/************************************************************************************

		기관의 근로기준시간 및 시급을 조회한다.

	************************************************************************************/

		$sql = 'select m00_day_work_hour
				,      m00_day_hourly
				  from m00center
				 where m00_mcode = \''.$code.'\'
				   and m00_mkind =   '.$conn->_center_kind();

		$tmp_array = $conn->get_array($sql);

		$stnd_hour = $tmp_array[0]; //기준시간
		$stnd_pay  = $tmp_array[1]; //시준금액

		unset($tmp_array);

	/************************************************************************************

		배상책임보험사 리스트

	************************************************************************************/

		$sql = 'select g02_mkind as kind
				,      g02_ins_code as cd
				,      g01_name as nm
				,      g02_ins_from_date as from_dt
				,      g02_ins_to_date as to_dt
				  from g02inscenter
				 inner join g01ins
				    on g01_code = g02_ins_code
				 where g02_ccode = \''.$code.'\'';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$ins_list[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$ins_cnt = sizeof($ins_list);

	/************************************************************************************/

	$col_menu[0]  = array('id'=>0,  'nm'=>'입사일/핸드폰번호',      'use'=>'Y');
	$col_menu[1]  = array('id'=>1,  'nm'=>'4대보험',                'use'=>'Y');
	$col_menu[2]  = array('id'=>2,  'nm'=>'근로기준시간/시급',      'use'=>'Y');
	$col_menu[3]  = array('id'=>3,  'nm'=>'직급수당/연장특별수당',  'use'=>'Y');
	$col_menu[4]  = array('id'=>4,  'nm'=>'배상책임보험',           'use'=>'Y');
	$col_menu[5]  = array('id'=>5,  'nm'=>'일반수급자케어',         'use'=>'N');
	$col_menu[6]  = array('id'=>6,  'nm'=>'동거가족케어',           'use'=>'N');
	$col_menu[7]  = array('id'=>7,  'nm'=>'가사간병[바우처]',       'use'=>'N');
	$col_menu[8]  = array('id'=>8,  'nm'=>'노인돌봄[바우처]',       'use'=>'N');
	$col_menu[9]  = array('id'=>9,  'nm'=>'산모신생아[바우처]',     'use'=>'N');
	$col_menu[10] = array('id'=>10, 'nm'=>'장애인활동보조[바우처]', 'use'=>'N');
	$col_menu[11] = array('id'=>11, 'nm'=>'비급여수가급여',         'use'=>'Y');

	for($i=0; $i<sizeof($kind_list); $i++){
		switch($kind_list[$i]['code']){
			case '0':
				$col_menu[5]['use'] = 'Y';
				$col_menu[6]['use'] = 'Y';
				break;
			case '1':
				$col_menu[7]['use'] = 'Y';
				break;
			case '2':
				$col_menu[8]['use'] = 'Y';
				break;
			case '3':
				$col_menu[9]['use'] = 'Y';
				break;
			case '4':
				$col_menu[10]['use'] = 'Y';
				break;
		}
	}

	$col_width[0]  = 40;
	$col_width[1]  = 100;
	$col_width[2]  = 95;
	$col_width[3]  = 280;
	$col_width[4]  = 70;
	$col_width[5]  = 70;
	$col_width[6]  = 174;
	$col_width[7]  = 380;
	$col_width[8]  = 300;
	$col_width[9]  = 260;
	$col_width[10] = 260;
	$col_width[11] = 260;
	$col_width[12] = 260;
	$col_width[13] = 130;

	$row_height = 26;

	for($i=0; $i<sizeof($col_width); $i++){
		if ($i < 2){
			$col_widths[0] += $col_width[$i];
		}else if ($i < 7){
			$col_widths[1] += $col_width[$i];
		}

		if ($i >= 7 && $i <= 12){
			if ($col_menu[$i-2]['use'] == 'Y'){
				$col_widths[1] += $col_width[$i];
				$col_widths[2] += $col_width[$i];
			}
		}

		if ($i == 13){
			$col_widths[1] += $col_width[$i];
			$col_widths[2] += $col_width[$i];
		}
	}

	$col_max   = 854;
	$max_width = $col_widths[0] + $col_widths[1];
?>

<style>
.w100 {
	width:100px;
}
.head {
	float:left;
	height:26px;
	font-size:9pt;
	border-top:none;
	border-left:none;
	border-right:1px solid #a6c0f3;
	border-bottom:1px solid #a6c0f3;
	background-color:#ffffff;
	line-height:26px;
}

.head_bg{
	background-color:#f7faff;
}

.head_top{
	border-top:1px solid #a6c0f3;
}

.temp_top{
	border-top:1px solid #f7faff;
}

.text {
	float:left;
	height:26px;
	font-size:9pt;
	text-align:left;
	border-top:none;
	border-left:none;
	border-right:1px solid #d4d4d4;
	border-bottom:1px solid #d4d4d4;
	background-color:#ffffff;
}

.text_top{
	border-top:1px solid #d4d4d4;
}

.left {
	text-align:left;
	padding-left:5px;
}

.center {
	text-align:center;
}

.right {
	text-align:right;
	padding-right:5px;
}

input {
	margin-top:2px;
	margin-left:4px;
	margin-right:4px;
}
</style>

<script language='javascript'>
<!--

var f = null;

function init(){
	var index = document.getElementsByName('index[]');

	var annuity_yn = document.getElementsByName('annuity_yn[]');
	var health_yn  = document.getElementsByName('health_yn[]');
	var employ_yn  = document.getElementsByName('employ_yn[]');
	var sanje_yn   = document.getElementsByName('sanje_yn[]');

	var annuity_pay = document.getElementsByName('annuity_pay[]');
	var health_pay  = document.getElementsByName('health_pay[]');
	var employ_pay  = document.getElementsByName('employ_pay[]');
	var sanje_pay   = document.getElementsByName('sanje_pay[]');

	var ins_yn      = document.getElementsByName('ins_yn[]');
	var ins_from_dt = document.getElementsByName('ins_from_dt[]');
	var ins_to_dt   = document.getElementsByName('ins_to_dt[]');

	for(var i=0; i<index.length; i++){
		/***********************************************************************************************
			4대보험
		***********************************************************************************************/
		__object_enabled(annuity_pay[i], (annuity_yn[i].value == 'Y' ? true : false));
		__object_enabled(health_pay[i],  (health_yn[i].value  == 'Y' ? true : false));
		__object_enabled(employ_pay[i],  (employ_yn[i].value  == 'Y' ? true : false));
		__object_enabled(sanje_pay[i],   (sanje_yn[i].value   == 'Y' ? true : false));

		/***********************************************************************************************
			배상책임보험
		***********************************************************************************************/
		__object_enabled(ins_from_dt[i], (ins_yn[i].value == 'Y' ? true : false));
		__object_enabled(ins_to_dt[i],   (ins_yn[i].value == 'Y' ? true : false));

		/***********************************************************************************************
			비급여수가급여
		***********************************************************************************************/
		var bipay_yn   = document.getElementsByName('bipay_yn_'+index[i].value);
		var bipay_rate = document.getElementById('bipay_rate_'+index[i].value);

		__object_enabled(bipay_rate, (__object_get_value(bipay_yn) == 'Y' ? true : false));
	}
}

function set_4ins_yn(menu, object, id){
	var yn  = document.getElementsByName(object+'_yn[]')[id];
	var pay = document.getElementsByName(object+'_pay[]')[id];

	if (yn.value == 'Y'){
		yn.value = 'N';
		menu.innerHTML = '미가입';
		menu.style.fontWeight = 'normal';
	}else{
		yn.value = 'Y';
		menu.innerHTML = '가입';
		menu.style.fontWeight = 'bold';
	}

	__object_enabled(pay, (yn.value == 'Y' ? true : false));

	if (yn.value == 'Y') pay.focus();
}

function set_ins_yn(menu, id){
	var ins_yn      = document.getElementsByName('ins_yn[]')[id];
	var ins_from_dt = document.getElementsByName('ins_from_dt[]')[id];
	var ins_to_dt   = document.getElementsByName('ins_to_dt[]')[id];

	if (ins_yn.value == 'Y'){
		ins_yn.value = 'N';
		menu.innerHTML = '미가입';
		menu.style.fontWeight = 'normal';
	}else{
		ins_yn.value = 'Y';
		menu.innerHTML = '가입';
		menu.style.fontWeight = 'bold';
	}

	__object_enabled(ins_from_dt, (ins_yn.value == 'Y' ? true : false));
	__object_enabled(ins_to_dt,   (ins_yn.value == 'Y' ? true : false));

	if (ins_yn.value != 'N'){
		ins_from_dt.focus();
	}
}

function set_normal_care(id){
	var kind = document.getElementsByName('pay_kind_0_'+id);
	var kind_val = __object_get_value(kind);

	for(var i=1; i<=4; i++){
		var obj  = document.getElementById('pay_type_'+i+'_'+id);

		if (i == kind_val)
			obj.style.display = '';
		else
			obj.style.display = 'none';
	}
}

function set_family_care(id){
	var kind = document.getElementsByName('family_pay_kind_'+id);
	var kind_val = __object_get_value(kind);

	for(var i=1; i<=3; i++){
		var obj  = document.getElementById('family_pay_type_'+i+'_'+id);

		if (i == kind_val)
			obj.style.display = '';
		else
			obj.style.display = 'none';
	}
}

function set_voucher_care(svc, id){
	var kind = document.getElementsByName('voucher_pay_kind_'+svc+'_'+id);
	var kind_val = __object_get_value(kind);

	for(var i=1; i<=4; i++){
		var obj  = document.getElementById('voucher_pay_type_'+i+'_'+svc+'_'+id);

		if (obj != null){
			if (i == kind_val)
				obj.style.display = '';
			else
				obj.style.display = 'none';
		}
	}
}

function set_bipay_care(id){
	var kind = document.getElementsByName('bipay_yn_'+id);
	var rate = document.getElementById('bipay_rate_'+id);
	var kind_val = __object_get_value(kind);

	__object_enabled(rate, (kind_val == 'Y' ? true : false));
}

function current_menu(menu, id){
	var list = document.getElementsByName('list_menu_'+id+'[]');
	var menu_tab = document.getElementsByName('menu_tab[]');
	var current_cnt = 0;

	if (menu.tag == 'Y'){
		menu.style.fontWeight = 'normal';
		menu.tag = 'N';
	}else{
		menu.style.fontWeight = 'bold';
		menu.tag = 'Y';
	}

	for(var i=0; i<menu_tab.length; i++){
		if (menu_tab[i].tag == 'Y') current_cnt ++;
	}

	if (current_cnt < 1){
		menu.style.fontWeight = 'bold';
		menu.tag = 'Y';
		alert('메뉴는 하나이상 선택하셔야 합니다.');
		return;
	}

	for(var i=0; i<list.length; i++){
		list[i].style.display = (menu.tag == 'Y' ? '' : 'none');
	}
}

function modify(){
	var center_stnd_hour = document.getElementById('center_stnd_hour').value; //기준시간
	var center_stnd_pay  = document.getElementById('center_stnd_pay').value;  //기준시급

	var index     = document.getElementsByName('index[]');
	var join_dt   = document.getElementsByName('join_dt[]');
	var stnd_time = document.getElementsByName('stnd_time[]');
	var stnd_pay  = document.getElementsByName('stnd_pay[]');

	var ins_yn      = document.getElementsByName('ins_yn[]');
	var ins_from_dt = document.getElementsByName('ins_from_dt[]');
	var ins_to_dt   = document.getElementsByName('ins_to_dt[]');

	for(var i=0; i<index.length; i++){
		if (!checkDate(join_dt[i].value)){
			alert('입사일을 입력하여 주십시오.');
			join_dt[i].focus();
			return;
		}

		if (stnd_time[i].value < center_stnd_hour){
			alert('근로기준시간은 최소 '+center_stnd_hour+'시간 이상 입력하여 주십시오.');
			stnd_time[i].focus();
			return;
		}

		if (__str2num(stnd_pay[i].value) < center_stnd_pay){
			alert('근로기준시급은 최소 '+center_stnd_pay+'원 이상 입력하여 주십시오.');
			stnd_pay[i].focus();
			return;
		}

		if (ins_yn[i].value == 'Y'){
			if (!checkDate(ins_from_dt[i].value)){
				alert('배상책임보험가입기간을 입력하여 주십시오.');
				ins_from_dt[i].focus();
				return;
			}

			if (!checkDate(ins_to_dt[i].value)){
				alert('배상책임보험가입기간을 입력하여 주십시오.');
				ins_to_dt[i].focus();
				return;
			}
		}

		var pay_kind           = document.getElementsByName('pay_kind_0_'+index[i].value);
		var family_pay         = document.getElementsByName('family_pay_kind_'+index[i].value);
		var voucher_pay_kind_1 = document.getElementsByName('voucher_pay_kind_1_'+index[i].value);
		var voucher_pay_kind_2 = document.getElementsByName('voucher_pay_kind_2_'+index[i].value);
		var voucher_pay_kind_3 = document.getElementsByName('voucher_pay_kind_3_'+index[i].value);
		var voucher_pay_kind_4 = document.getElementsByName('voucher_pay_kind_4_'+index[i].value);
		var bipay_yn           = document.getElementsByName('bipay_yn_'+index[i].value);

		if (__object_get_value(pay_kind)           == '0' &&
			__object_get_value(family_pay)         == '0' &&
			__object_get_value(voucher_pay_kind_1) == '0' &&
			__object_get_value(voucher_pay_kind_2) == '0' &&
			__object_get_value(voucher_pay_kind_3) == '0' &&
			__object_get_value(voucher_pay_kind_4) == '0'){
			alert('급여산정방식을 하나이상 선택하여 주십시오.');
			pay_kind[pay_kind.length-1].focus();
			return;
		}

		switch(__object_get_value(pay_kind)){
			case '1':
				if (!chk_value('hourly_pay_0_'+index[i].value,'시급을 입력하여 주십시오.')) return;
				break;
			case '2':
				if (!chk_value('change_hourly_pay_0_1_'+index[i].value,'변동시급을 입력하여 주십시오.')) return;
				if (!chk_value('change_hourly_pay_0_2_'+index[i].value,'변동시급을 입력하여 주십시오.')) return;
				if (!chk_value('change_hourly_pay_0_3_'+index[i].value,'변동시급을 입력하여 주십시오.')) return;
				if (!chk_value('change_hourly_pay_0_9_'+index[i].value,'변동시급을 입력하여 주십시오.')) return;
				break;
			case '3':
				if (!chk_value('base_pay_0_'+index[i].value,'월급을 입력하여 주십시오.')) return;
				break;
			case '4':
				if (!chk_value('suga_rate_pay_0_'+index[i].value,'총액비율을 입력하여 주십시오.')) return;
				break;
		}

		switch(__object_get_value(family_pay)){
			case '1':
				if (!chk_value('family_hourly_pay_'+index[i].value,'시급을 입력하여 주십시오.')) return;
				break;
			case '2':
				if (!chk_value('family_suga_rate_pay_'+index[i].value,'총액비율을 입력하여 주십시오.')) return;
				break;
			case '3':
				if (!chk_value('family_base_pay_'+index[i].value,'고정급을 입력하여 주십시오.')) return;
				break;
		}

		for(var j=1; j<=4; j++){
			switch(__object_get_value(document.getElementsByName('voucher_pay_kind_'+j+'_'+index[i].value))){
				case '1':
					if (!chk_value('hourly_pay_'+j+'_'+index[i].value,'시급을 입력하여 주십시오.')) return;
					break;
				case '4':
					if (!chk_value('suga_rate_pay_'+j+'_'+index[i].value,'총액비율을 입력하여 주십시오.')) return;
					break;
				case '3':
					if (!chk_value('base_pay_'+j+'_'+index[i].value,'월급을 입력하여 주십시오.')) return;
					break;
			}
		}

		if (__object_get_value(bipay_yn) == 'Y'){
			if (!chk_value('bipay_rate_'+index[i].value,'비급여수가급여 지급율을 입력하여 주십시오.')) return;
		}
	}

	if (!confirm('입력하신 데이타를 일괄수정하시겠습니까?')) return;

	f.action = 'mem_modify_ok.php';
	f.submit();
}

function chk_value(obj_nm,tag){
	try{
		var obj = document.getElementById(obj_nm);

		if (__str2num(obj.value) <= 0){
			alert(tag);
			obj.focus();
			return false;
		}

		return true;
	}catch(e){
		alert(obj_nm);

		__show_error(e);

		return false;
	}
}

function toresize(){
	var left_box  = document.getElementById('left_box');
	var div       = document.getElementById('body_div');
	var mun       = document.getElementById('menu_div');
	var cot       = document.getElementById('cont_div');
	var src       = document.getElementById('menu_src');
	var fot       = document.getElementById('main_copy');
	var list      = document.getElementById('list_div');
	var body_main = document.getElementById('body_main');
	var t = __getObjectTop(div) + fot.offsetHeight + 65;

	div.style.height = document.body.offsetHeight - t;
	body_main.style.height = parseInt(div.style.height, 10) - fot.offsetHeight + 3;
	list.style.height = parseInt(body_main.style.height, 10);
	cot.style.height  = parseInt(body_main.style.height, 10);

	cot.onscroll = function(){
		src.scrollLeft  = cot.scrollLeft;
		list.scrollTop = cot.scrollTop;
	}
}

window.onload = function(){
	f = document.f;

	document.body.scroll = 'no';

	toresize();

	__init_form(f);
	init();
}

window.onresize = toresize;

-->
</script>

<div class="title title_border">직원정보수정</div>

<form name="f" method="post">

<div id="body_div">
<div id="menu_div">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="34px">
			<col>
			<col width="100px">
		</colgroup>
		<tbody>
			<tr>
				<th>선택</th>
				<td style="">
					<table style="width:100%;">
					<?
						for($i=0; $i<sizeof($col_menu); $i++){
							if ($col_menu[$i]['use'] == 'Y'){
								$col_cnt ++;
							}
						}

						$mod     = 5;
						$tr_cnt  = 0;
						$i_cnt   = 0;

						for($i=0; $i<$col_cnt; $i++){
							if ($i_cnt % $mod == 0){
								if ($tr_start) echo '</tr>';

								$tr_start = true;
								$tr_cnt ++;
								echo '<tr>';
							}

							if ($col_menu[$i]['use'] == 'Y'){
								echo '<td class=\'left'.($i_cnt % $mod == $mod - 1 ? ' last ' : '').($tr_cnt > $col_cnt / 5 ? ' bottom ' : '').'\'><span id=\'menu_tab[]\' onclick=\'current_menu(this,'.$col_menu[$i]['id'].');\' style=\'cursor:pointer; font-weight:bold;\' tag=\'Y\'>'.$col_menu[$i]['nm'].'</span></td>';
								$i_cnt ++;
							}
						}
					?>
					</table>
				</td>
				<td class="left top last" style="padding-top:5px;">
					<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="modify();">저장</button></span>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="width:100%; height:100%;">
		<div style="width:<?=$col_widths[0];?>px; height:100%; float:left;">
			<div>
				<div class='head head_bg' style='width:<?=$col_width[0];?>px; height:54px; line-height:53px;'>No</div>
				<div class='head head_bg' style='width:<?=$col_width[1];?>px;'>직원명</div>
			</div>
			<div style='margin-left:<?=$col_width[0];?>px; margin-top:-28px;'>
				<div class='head head_bg head_top' style='width:<?=$col_width[1];?>px;'>주민번호</div>
			</div>
		</div>
		<div id="menu_src" style="width:<?=$col_max - $col_widths[0];?>px; height:100%; float:left; overflow-x:hidden; overflow-y:scroll;">
			<div style="width:<?=$col_widths[1]+1;?>px;">
				<div>
					<div id="list_menu_0[]"  class='head head_bg' style='width:<?=$col_width[2];?>px;'>입사일</div>
					<div id="list_menu_1[]"  class='head head_bg' style='width:<?=$col_width[3];?>px;'>4대보험가입여부</div>
					<div id="list_menu_2[]"  class='head head_bg' style='width:<?=$col_width[4];?>px;'>기준시간</div>
					<div id="list_menu_3[]"  class='head head_bg' style='width:<?=$col_width[5];?>px;'>직급수당</div>
					<div id="list_menu_4[]"  class='head head_bg' style='width:<?=$col_width[6];?>px;'>배상가입여부</div>
					<div id="list_menu_5[]"  class='head head_bg' style='width:<?=$col_widths[2];?>px;'>급여산정</div>
				</div>
				<div style='position:relative; top:-1px;'>
					<div id="list_menu_0[]"  class='head head_bg head_top' style='width:<?=$col_width[2];?>px;'>핸드폰번호</div>
					<div id="list_menu_1[]"  class='head head_bg head_top' style='width:<?=$col_width[3]/4;?>px;'>국민연금</div>
					<div id="list_menu_1[]"  class='head head_bg head_top' style='width:<?=$col_width[3]/4;?>px;'>건간보험</div>
					<div id="list_menu_1[]"  class='head head_bg head_top' style='width:<?=$col_width[3]/4;?>px;'>고용보험</div>
					<div id="list_menu_1[]"  class='head head_bg head_top' style='width:<?=$col_width[3]/4;?>px;'>산재보험</div>
					<div id="list_menu_2[]"  class='head head_bg head_top' style='width:<?=$col_width[4];?>px;'>기준시급</div>
					<div id="list_menu_3[]"  class='head head_bg head_top' style='width:<?=$col_width[5];?>px;'>특별수당</div>
					<div id="list_menu_4[]"  class='head head_bg head_top' style='width:<?=$col_width[6];?>px;'>배상가입기간</div>
					<?
						if ($col_menu[5]['use'] == 'Y'){?>
							<div id="list_menu_5[]"  class='head head_bg head_top' style='width:<?=$col_width[7];?>px;'>일반수급자케어</div><?
						}

						if ($col_menu[6]['use'] == 'Y'){?>
							<div id="list_menu_6[]"  class='head head_bg head_top' style='width:<?=$col_width[8];?>px;'>동거가족케어</div><?
						}

						if ($col_menu[7]['use'] == 'Y'){?>
							<div id="list_menu_7[]"  class='head head_bg head_top' style='width:<?=$col_width[9];?>px;'>가사간병[바우처]</div><?
						}

						if ($col_menu[8]['use'] == 'Y'){?>
							<div id="list_menu_8[]"  class='head head_bg head_top' style='width:<?=$col_width[10];?>px;'>노인돌봄[바우처]</div><?
						}

						if ($col_menu[9]['use'] == 'Y'){?>
							<div id="list_menu_9[]"  class='head head_bg head_top' style='width:<?=$col_width[11];?>px;'>산모신생아[바우처]</div><?
						}

						if ($col_menu[10]['use'] == 'Y'){?>
							<div id="list_menu_10[]" class='head head_bg head_top' style='width:<?=$col_width[12];?>px;'>장애인활동보조[바우처]</div><?
						}
					?>
					<div id="list_menu_11[]" class='head head_bg head_top' style='width:<?=$col_width[13];?>px;'>비급여수가급여</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="body_main" style="position:relative; width:100%; top:-1px;">
<?
	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode        = '$code'
			   and m02_ygoyong_stat = '1'
			   and m02_del_yn       = 'N'
			 order by m02_yname, m02_yjumin, m02_mkind";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$mst[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$mst_cnt = $row_count;

	for($i=0; $i<$mst_cnt; $i++){
		$kind  = $mst[$i]['m02_mkind'];
		$jumin = $mst[$i]['m02_yjumin'];

		/********************************************************************

			급여산정방식

		********************************************************************/
			if ($mst[$i]["m02_ygupyeo_kind"] == '1' || $mst[$i]["m02_ygupyeo_kind"] == '2'){
				if ($mst[$i]['m02_pay_type'] == 'Y'){
					$pay_type[$jumin][$kind] = 1; //시급(고정급)
				}else{
					$pay_type[$jumin][$kind] = 2; //시급(변동급)
				}
			}else if ($mst[$i]["m02_ygupyeo_kind"] == '3'){
				$pay_type[$jumin][$kind] = 3; //월급

				if ($mst[$i]['m02_pay_type'] == 'Y'){
					$pay_com_type[$jumin][$kind] = 'Y';
				}
			}else if ($mst[$i]["m02_ygupyeo_kind"] == '4'){
				$pay_type[$jumin][$kind] = 4; //총액비율
			}else{
				$pay_type[$jumin][$kind] = 0;
			}

			switch($pay_type[$jumin][$kind]){
				case 1:
					$hourly_1[$jumin][$kind] = $mst[$i]["m02_ygibonkup"];
					break;
				case 2:
					$sql = "select m02_gubun
							,      m02_pay
							  from m02pay
							 where m02_ccode = '$code'
							   and m02_mkind = '$kind'
							   and m02_jumin = '$jumin'";
					$conn->query($sql);
					$conn->fetch();
					$row_count = $conn->row_count();

					for($j=0; $j<$row_count; $j++){
						$row = $conn->select_row($j);
						$hourly_2[$jumin][$kind][$row['m02_gubun']] = $row['m02_pay'];
					}

					$conn->row_free();
					break;
				case 3:
					$hourly_3[$jumin][$kind] = $mst[$i]["m02_ygibonkup"];
					break;
				case 4:
					$hourly_4[$jumin][$kind] = $mst[$i]["m02_ysuga_yoyul"];
					break;
			}
		/********************************************************************/


		/********************************************************************

			동거가족급여

		********************************************************************/
			if ($kind == '0'){
				if($mst[$i]['m02_yfamcare_type'] == '1'){
					$famcare_type[$jumin] = 1; //고정급

					if($mst[$i]['m02_yfamcare_umu'] == 'N'){
						$famcare_type[$jumin] = 0;  //무
					}
				}else if($mst[$i]['m02_yfamcare_type'] == '2'){
					$famcare_type[$jumin] = 2; //수가총액
				}else if($mst[$i]['m02_yfamcare_type'] == '3'){
					$famcare_type[$jumin] = 3; //고정급
				}else {
					$famcare_type[$jumin] = 0;
				}

				// 동거가족 본인부담금 수당지급 여부
				$family_pay_yn[$jumin] = $mst[$i]['m02_family_pay_yn'];;

				switch($famcare_type[$jumin]){
					case '1':
						$famcare_pay1[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					case '2':
						$famcare_pay2[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					case '3':
						$famcare_pay3[$jumin] = $mst[$i]['m02_yfamcare_pay'];
						break;
					default:
						$family_pay_yn[$jumin] = 'N';
				}
			}
		/********************************************************************/
	}

	unset($tmp_jumin);

	for($i=0; $i<$mst_cnt; $i++){
		$kind  = $mst[$i]['m02_mkind'];
		$jumin = $mst[$i]['m02_yjumin'];

		if ($tmp_jumin != $jumin){
			$tmp_jumin  = $jumin;
			$r          = $i;

			$no ++;
			$ii = $no - 1;

			/************************************************

				스타일설정

			************************************************/
				if ($no % 2 == 1){
					$style = 'background-color:#ffffff;';
				}else{
					$style = 'background-color:#f9f9f9;';
				}
			/***********************************************/

			if ($no == 1){
				$html[0][0] = '<div id=\'list_div\' style=\'width:'.$col_widths[0].'px; float:left; overflow-x:hidden; overflow-y:hidden\'>';
				$html[0][1] = '</div>';

				$html[1][0] = '<div id=\'cont_div\' style=\'width:'.($col_max - $col_widths[0]).'px; float:left; overflow:scroll;\'>';
				$html[1][1] = '</div>';
			}


			/********************************************************************************************************************************************************************************************

				직원명과 주민번호

			********************************************************************************************************************************************************************************************/
				$html[2][0] .= '<div style=\'position:relative; top:-'.($ii * 26).'px;\'>';
					$html[2][0] .= '<div class=\'text center\' style=\''.$style.' width:'.($col_width[0]).'px; height:53px; line-height:53px;\'>'.$no.'</div>';
					$html[2][0] .= '<div class=\'text left\' style=\''.$style.' width:'.($col_width[1]).'px; height:28px;\'>'.$mst[$r]['m02_yname'].'</div>';
				$html[2][0] .= '</div>';

				$html[2][0] .= '<div style=\'position:relative; left:'.($col_width[0]).'px; top:-'.($ii * 26 + 26).'px;\'>';
					$html[2][0] .= '<div class=\'text left\' style=\''.$style.' width:'.($col_width[1]).'px;\'>'.$myF->issStyle($mst[$r]['m02_yjumin']).'</div>';
				$html[2][0] .= '</div>';
			/*******************************************************************************************************************************************************************************************/

			/********************************************************************************************************************************************************************************************

				리스트

			********************************************************************************************************************************************************************************************/
				$html[2][1] .= '<div style=\'position:relative; width:'.$col_widths[1].'px;\'>';

					#입사일
					$html[2][1] .= '<div id=\'list_menu_0[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[2]).'px; height:28px;\'><input name=\'join_dt[]\' type=\'text\' class=\'date\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 1).'\' value=\''.$myF->dateStyle($mst[$r]['m02_yipsail']).'\' onclick=\'\'></div>';

					#4대보험 가입여부
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span tabindex=\''.($ii * 1000 + 3).'\' onclick=\'set_4ins_yn(this, "annuity", '.$ii.');\' onkeypress=\'if(event.keyCode == 13 || event.keyCode == 32){set_4ins_yn(this, "annuity", '.$ii.');};\' style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span tabindex=\''.($ii * 1000 + 4).'\' onclick=\'set_4ins_yn(this, "health", '.$ii.');\'  onkeypress=\'if(event.keyCode == 13 || event.keyCode == 32){set_4ins_yn(this, "health", '.$ii.');};\'  style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span tabindex=\''.($ii * 1000 + 5).'\' onclick=\'set_4ins_yn(this, "employ", '.$ii.');\'  onkeypress=\'if(event.keyCode == 13 || event.keyCode == 32){set_4ins_yn(this, "employ", '.$ii.');};\'  style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px; height:28px;\'><span tabindex=\''.($ii * 1000 + 6).'\' onclick=\'set_4ins_yn(this, "sanje", '.$ii.');\'   onkeypress=\'if(event.keyCode == 13 || event.keyCode == 32){set_4ins_yn(this, "sanje", '.$ii.');};\'   style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? '가입' : '미가입').'</span></div>';

					#근로기준 시간
					$html[2][1] .= '<div id=\'list_menu_2[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[4]).'px; height:28px;\'><input name=\'stnd_time[]\' type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 7).'\' value=\''.number_format($mst[$r]['m02_stnd_work_time'], 1).'\' onkeydown=\'__onlyNumber(this, ".");\'></div>';

					#직급수당
					$html[2][1] .= '<div id=\'list_menu_3[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[5]).'px; height:28px;\'><input name=\'rank_pay[]\' type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 8).'\' value=\''.number_format($mst[$r]['m02_rank_pay']).'\'></div>';

					#배상책임보험 가입여부
					$html[2][1] .= '<div id=\'list_menu_4[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[6]).'px; height:28px;\'><span tabindex=\''.($ii * 1000 + 9).'\' onclick=\'set_ins_yn(this, '.$ii.');\' onkeypress=\'if(event.keyCode == 13 || event.keyCode == 32){set_ins_yn(this, '.$ii.');};\' style=\'cursor:pointer; font-weight:'.($mst[$r]['m02_ins_yn'] == 'Y' ? 'bold' : 'normal').';\'>'.($mst[$r]['m02_ins_yn'] == 'Y' ? '가입' : '미가입').'</span></div>';

					#일반수급자케어 급여산정방식
					if ($col_menu[5]['use'] == 'Y'){
						$html[2][1] .= '<div id=\'list_menu_5[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[7]).'px; height:28px;\'>
											<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 10).'\' value=\'0\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
											<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 10).'\' value=\'1\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
											<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 10).'\' value=\'2\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 2 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>변동시급</span>
											<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 10).'\' value=\'4\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 4 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>총액비율</span>
											<input name=\'pay_kind_0_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 10).'\' value=\'3\' onclick=\'set_normal_care('.$ii.');\' '.($pay_type[$jumin][0] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>월급</span>
										</div>';
					}

					#동거가족케어 급여산정방식
					if ($col_menu[6]['use'] == 'Y'){
						$html[2][1] .= '<div id=\'list_menu_6[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[8]).'px; height:28px;\'>
											<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 11).'\' value=\'0\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
											<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 11).'\' value=\'1\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
											<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 11).'\' value=\'2\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 2 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>수가총액비율</span>
											<input name=\'family_pay_kind_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 11).'\' value=\'3\' onclick=\'set_family_care('.$ii.');\' '.($famcare_type[$jumin] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정급</span>
										</div>';
					}

					#바우처 급여산정방식
					for($j=1; $j<=4; $j++){
						if ($col_menu[$j+6]['use'] == 'Y'){
							$html[2][1] .= '<div id=\'list_menu_'.($j+6).'[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[$j+9-1]).'px; height:28px;\'>
												<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' tabindex=\''.($ii * 1000 + 11 + $j).'\' class=\'radio\' value=\'0\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 0 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>무</span>
												<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' tabindex=\''.($ii * 1000 + 11 + $j).'\' class=\'radio\' value=\'1\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 1 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>고정시급</span>
												<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' tabindex=\''.($ii * 1000 + 11 + $j).'\' class=\'radio\' value=\'4\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 4 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>총액비율</span>
												<input name=\'voucher_pay_kind_'.$j.'_'.$ii.'\' type=\'radio\' tabindex=\''.($ii * 1000 + 11 + $j).'\' class=\'radio\' value=\'3\' onclick=\'set_voucher_care('.$j.','.$ii.');\' '.($pay_type[$jumin][$j] == 3 ? 'checked' : '').'><span style=\'margin-left:-5px;\'>월급</span>
											</div>';
						}
					}

					#비급여수가급여
					$html[2][1] .= '<div id=\'list_menu_11[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[13]).'px; height:28px;\'>
										<input name=\'bipay_yn_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 16).'\' value=\'Y\' onclick=\'set_bipay_care('.$ii.');\' '.($mst[$r]['m02_bipay_yn'] == 'Y' ? 'checked' : '').'><span style=\'margin-left:-5px;\'>지급</span>
										<input name=\'bipay_yn_'.$ii.'\' type=\'radio\' class=\'radio\' tabindex=\''.($ii * 1000 + 16).'\' value=\'N\' onclick=\'set_bipay_care('.$ii.');\''.($mst[$r]['m02_bipay_yn'] != 'Y' ? 'checked' : '').'><span style=\'margin-left:-5px;\'>미지급</span>
									</div>';

				$html[2][1] .= '</div>';

				$html[2][1] .= '<div style=\'position:relative; width:'.$col_widths[1].'px;\'>';

					#모바일
					$html[2][1] .= '<div id=\'list_menu_0[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[2]).'px;\'><input name=\'mobile[]\' type=\'text\' class=\'phone\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 2).'\' value=\''.$myF->phoneStyle($mst[$r]['m02_ytel']).'\'></div>';

					#4대보험 가입금액
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'annuity_pay[]\' type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 3).'\' value=\''.number_format($mst[$r]['m02_ykuksin_mpay']).'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'health_pay[]\'  type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 4).'\' value=\''.number_format($mst[$r]['m02_health_mpay']).'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'employ_pay[]\'  type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 5).'\' value=\''.number_format($mst[$r]['m02_employ_mpay']).'\'></div>';
					$html[2][1] .= '<div id=\'list_menu_1[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[3]/4).'px;\'><input name=\'sanje_pay[]\'   type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 6).'\' value=\''.number_format($mst[$r]['m02_sanje_mpay']).'\'></div>';

					#근로기준 시급
					$html[2][1] .= '<div id=\'list_menu_2[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[4]).'px;\'><input name=\'stnd_pay[]\' type=\'text\' class=\'number\' style=\'width:100%;\' tabindex=\''.($ii * 1000 + 7).'\' value=\''.number_format($mst[$r]['m02_stnd_work_pay']).'\'></div>';

					#연장특별수당
					$html[2][1] .= '<div id=\'list_menu_3[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[5]).'px;\'><input name=\'add_payrate[]\' type=\'text\' class=\'number\' style=\'width:80%;\' tabindex=\''.($ii * 1000 + 8).'\' value=\''.number_format($mst[$r]['m02_add_payrate'],1).'\' onkeydown=\'__onlyNumber(this, ".");\'>%</div>';

					#배상책임보험 가입기간
					$html[2][1] .= '<div id=\'list_menu_4[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[6]).'px;\'>
										<input name=\'ins_from_dt[]\' type=\'text\' class=\'date\' tabindex=\''.($ii * 1000 + 9).'\' value=\''.$myF->dateStyle($mst[$r]['m02_ins_from_date']).'\' onclick=\'\'> ~
										<input name=\'ins_to_dt[]\'   type=\'text\' class=\'date\' tabindex=\''.($ii * 1000 + 9).'\' value=\''.$myF->dateStyle($mst[$r]['m02_ins_to_date']).'\' onclick=\'\'>
									</div>';

					#일반수급자케어 급여금액
					if ($col_menu[5]['use'] == 'Y'){
						$html[2][1] .= '<div id=\'list_menu_5[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[7]).'px;\'>
											<div id=\'pay_type_1_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 1 ? '' : 'none').';\'>
												시급 <input name=\'hourly_pay_0_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_1[$jumin][0]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											</div>
											<div id=\'pay_type_2_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 2 ? '' : 'none').';\'>
												1등급 <input name=\'change_hourly_pay_0_1_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_2[$jumin][0][1]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
												2등급 <input name=\'change_hourly_pay_0_2_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_2[$jumin][0][2]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
												3등급 <input name=\'change_hourly_pay_0_3_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_2[$jumin][0][3]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
												일반  <input name=\'change_hourly_pay_0_9_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_2[$jumin][0][9]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											</div>
											<div id=\'pay_type_4_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 4 ? '' : 'none').';\'>
												총액비율 <input name=\'suga_rate_pay_0_'.$ii.'\' type=\'text\' value=\''.number_format($hourly_4[$jumin][0],1).'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
											</div>
											<div id=\'pay_type_3_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][0] == 3 ? '' : 'none').';\'>
												월급 <input name=\'base_pay_0_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 10).'\' tabindex=\''.($ii * 1000 + 10).'\' value=\''.number_format($hourly_3[$jumin][0]).'\' maxlength=\'8\' class=\'number\'>
													 <input name=\'ybnpay_0_'.$ii.'\' type=\'checkbox\' class=\'checkbox\' tabindex=\''.($ii * 1000 + 10).'\' value=\'Y\' '.($mst[$r]['m02_bnpay_yn'] == 'Y' ? 'checked' : '').'>목욕,간호수당포함
											</div>
										</div>';
					}

					#동거가족케어 급여금액
					if ($col_menu[6]['use'] == 'Y'){
						$html[2][1] .= '<div id=\'list_menu_6[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[8]).'px;\'>
											<div id=\'family_pay_type_1_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 1 ? '' : 'none').';\'>
												고정시급 <input name=\'family_hourly_pay_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11).'\' value=\''.number_format($famcare_pay1[$jumin]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
											</div>
											<div id=\'family_pay_type_2_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 2 ? '' : 'none').';\'>
												수가총액비율 <input name=\'family_suga_rate_pay_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11).'\' value=\''.number_format($famcare_pay2[$jumin],1).'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
											</div>
											<div id=\'family_pay_type_3_'.$ii.'\' class=\'left\' style=\'display:'.($famcare_type[$jumin] == 3 ? '' : 'none').';\'>
												고정급 <input name=\'family_base_pay_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11).'\' value=\''.number_format($famcare_pay3[$jumin]).'\' maxlength=\'8\' class=\'number\'>
											</div>
										</div>';
					}

					#바우처 급여금액
					for($j=1; $j<=4; $j++){
						if ($col_menu[$j+6]['use'] == 'Y'){
							$html[2][1] .= '<div id=\'list_menu_'.($j + 6).'[]\' class=\'text center\' style=\''.$style.' width:'.($col_width[$j+9-1]).'px;\'>
												<div id=\'voucher_pay_type_1_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 1 ? '' : 'none').';\'>
													시급 <input name=\'hourly_pay_'.$j.'_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11 + $j).'\' value=\''.number_format($hourly_1[$jumin][$j]).'\' maxlength=\'8\' class=\'number\' style=\'width:50px;\'>
												</div>
												<div id=\'voucher_pay_type_4_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 4 ? '' : 'none').';\'>
													총액비율 <input name=\'suga_rate_pay_'.$j.'_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11 + $j).'\' value=\''.number_format($hourly_4[$jumin][$j],1).'\' maxlength=\'4\' class=\'number\' onKeyDown=\'__onlyNumber(this,".");\' style=\'width:50px;\'>%
												</div>
												<div id=\'voucher_pay_type_3_'.$j.'_'.$ii.'\' class=\'left\' style=\'display:'.($pay_type[$jumin][$j] == 3 ? '' : 'none').';\'>
													월급 <input name=\'base_pay_'.$j.'_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 11 + $j).'\' value=\''.number_format($hourly_3[$jumin][$j]).'\' maxlength=\'8\' class=\'number\'>
												</div>
											</div>';
						}
					}

					#비급여 지급비율
					$html[2][1] .= '<div id=\'list_menu_11[]\' class=\'text left\' style=\''.$style.' width:'.($col_width[13]).'px;\'>
										지급율 <input name=\'bipay_rate_'.$ii.'\' type=\'text\' tabindex=\''.($ii * 1000 + 16).'\' value=\''.number_format($mst[$r]['m02_bipay_rate']).'\' class=\'number\' maxlength=\'3\' style=\'width:50px;\'> %
									</div>';

				$html[2][1] .= '</div>';
			/*******************************************************************************************************************************************************************************************/


			$html[2][1] .= '<input name=\'index[]\' type=\'hidden\' value=\''.$ii.'\'>'; //국민연금 가입여부

			$html[2][1] .= '<input name=\'annuity_yn[]\' type=\'hidden\' value=\''.($mst[$r]['m02_ykmbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //국민연금 가입여부
			$html[2][1] .= '<input name=\'health_yn[]\'  type=\'hidden\' value=\''.($mst[$r]['m02_ygnbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //건강보험 가입여부
			$html[2][1] .= '<input name=\'employ_yn[]\'  type=\'hidden\' value=\''.($mst[$r]['m02_ygobohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //고용보험 가입여부
			$html[2][1] .= '<input name=\'sanje_yn[]\'   type=\'hidden\' value=\''.($mst[$r]['m02_ysnbohum_umu'] == 'Y' ? 'Y' : 'N').'\'>'; //산재보험 가입여부

			$html[2][1] .= '<input name=\'ins_yn[]\'   type=\'hidden\' value=\''.($mst[$r]['m02_ins_yn'] == 'Y' ? 'Y' : 'N').'\'>'; //배상책임 가입여부

			$html[2][1] .= '<input name=\'change_hourly_cd_0_1_'.$ii.'\' type=\'hidden\' value=\'1\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_2_'.$ii.'\' type=\'hidden\' value=\'2\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_3_'.$ii.'\' type=\'hidden\' value=\'3\'>';
			$html[2][1] .= '<input name=\'change_hourly_cd_0_9_'.$ii.'\' type=\'hidden\' value=\'9\'>';

			$html[2][1] .= '<input name=\'jumin[]\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';  //주민번호';
		}
	}

	$html[2][1] .= '<input name=\'center_stnd_hour\' type=\'hidden\' value=\''.$stnd_hour.'\'>'; //기준시간
	$html[2][1] .= '<input name=\'center_stnd_pay\'  type=\'hidden\' value=\''.$stnd_pay.'\'>';  //기준시급

	$html[2][1] .= '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';  //기관기호

	echo $html[0][0];
		echo $html[2][0];
	echo $html[0][1];

	echo $html[1][0];
		echo $html[2][1];
	echo $html[1][1];

	unset($pay_type);
	unset($hourly_1);
	unset($hourly_2);
	unset($hourly_3);
	unset($hourly_4);
	unset($html);
?>
</div>
</div>

</form>

<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
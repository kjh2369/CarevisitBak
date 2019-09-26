<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once('../work/salary_const.php');

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);
	$page	= $_POST['page'];
	$name	= $conn->member_name($code, $jumin);

	$sql = "select salary_basic.work_cnt
			,      salary_basic.work_time
			,      salary_basic.weekly_cnt
			,      salary_basic.paid_cnt
			,      salary_basic.bath_cnt
			,      salary_basic.nursing_cnt

			,      salary_basic.prolong_hour
			,      salary_basic.night_hour
			,      salary_basic.holiday_hour
			,      salary_basic.holiday_prolong_hour
			,      salary_basic.holiday_night_hour

			,      salary_basic.base_pay
			,      salary_basic.weekly_pay
			,      salary_basic.paid_pay
			,      salary_basic.bath_pay
			,      salary_basic.nursing_pay
			,      salary_basic.meal_pay
			,      salary_basic.car_keep_pay
			,      salary_basic.bojeon_pay
			,      salary_basic.base_pay + salary_basic.weekly_pay + salary_basic.paid_pay + salary_basic.bath_pay + salary_basic.nursing_pay + salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay as tot_basic_pay

			,      salary_basic.prolong_pay
			,      salary_basic.night_pay
			,      salary_basic.holiday_pay
			,      salary_basic.holiday_prolong_pay
			,      salary_basic.holiday_night_pay
			,      salary_detail.rank_pay
			,      salary_detail.long_pay
			,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay + salary_detail.rank_pay + salary_detail.long_pay as tot_sudang_pay

			,      salary_basic.pension_amt
			,      salary_basic.health_amt
			,      salary_basic.care_amt
			,      salary_basic.employ_amt
			,      salary_basic.pension_amt + salary_basic.health_amt + salary_basic.care_amt + salary_basic.employ_amt as tot_ins_pay

			,      salary_basic.tax_amt_1
			,      salary_basic.tax_amt_2
			,      salary_basic.tax_amt_1 + salary_basic.tax_amt_2 as tot_tax_pay

			,      salary_detail.last_month_pay
			,      salary_detail.yearly_pay
			,      salary_detail.traffic_pay
			,      salary_detail.other_pay_name_1
			,      salary_detail.other_pay_1
			,      salary_detail.other_pay_name_2
			,      salary_detail.other_pay_2
			,      salary_detail.last_month_pay + salary_detail.yearly_pay + salary_detail.traffic_pay + salary_detail.other_pay_name_1 + salary_detail.other_pay_1 + salary_detail.other_pay_name_2 + salary_detail.other_pay_2 as tot_other_pay

			,      salary_detail.kabul_amt
			,      salary_detail.expense_amt
			,      salary_detail.other_amt_name_1
			,      salary_detail.other_amt_1
			,      salary_detail.other_amt_name_2
			,      salary_detail.other_amt_2
			,      salary_detail.other_amt_name_3
			,      salary_detail.other_amt_3
			,      salary_detail.other_amt_name_4
			,      salary_detail.other_amt_4
			,      salary_detail.other_amt_name_5
			,      salary_detail.other_amt_5
			,      salary_detail.kabul_amt + salary_detail.expense_amt + salary_detail.other_amt_1 + salary_detail.other_amt_2 + salary_detail.other_amt_3 + salary_detail.other_amt_4 + salary_detail.other_amt_5 as tot_deduct_pay

			,      salary_amt.basic_total_amt
			,      salary_amt.addon_total_amt
			,      salary_amt.total_amt
			,      salary_amt.basic_deduct_amt
			,      salary_amt.addon_deduct_amt
			,      salary_amt.deduct_amt
			,      salary_amt.diff_amt

			,      salary_addon.extra_name_1
			,      salary_addon.extra_pay_1
			,      salary_addon.extra_name_2
			,      salary_addon.extra_pay_2
			,      salary_addon.extra_name_3
			,      salary_addon.extra_pay_3
			,      salary_addon.extra_name_4
			,      salary_addon.extra_pay_4
			,      salary_addon.extra_name_5
			,      salary_addon.extra_pay_5
			,      salary_addon.extra_name_6
			,      salary_addon.extra_pay_6
			,      salary_addon.extra_name_7
			,      salary_addon.extra_pay_7
			,      salary_addon.extra_total_pay

			,      salary_addon.deduct_name_1
			,      salary_addon.deduct_pay_1
			,      salary_addon.deduct_name_2
			,      salary_addon.deduct_pay_2
			,      salary_addon.deduct_name_3
			,      salary_addon.deduct_pay_3
			,      salary_addon.deduct_name_4
			,      salary_addon.deduct_pay_4
			,      salary_addon.deduct_name_5
			,      salary_addon.deduct_pay_5
			,      salary_addon.deduct_name_6
			,      salary_addon.deduct_pay_6
			,      salary_addon.deduct_name_7
			,      salary_addon.deduct_pay_7
			,      salary_addon.deduct_total_pay

			  from salary_basic
			  left join salary_detail
				on salary_detail.org_no       = salary_basic.org_no
			   and salary_detail.salary_yymm  = salary_basic.salary_yymm
			   and salary_detail.salary_jumin = salary_basic.salary_jumin
			  left join salary_amt
				on salary_amt.org_no       = salary_basic.org_no
			   and salary_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_amt.salary_jumin = salary_basic.salary_jumin
			  left join salary_addon
			    on salary_addon.org_no       = salary_basic.org_no
			   and salary_addon.salary_yymm  = salary_basic.salary_yymm
			   and salary_addon.salary_jumin = salary_basic.salary_jumin
			 where salary_basic.org_no       = '$code'
			   and salary_basic.salary_yymm  = '$year$month'
			   and salary_basic.salary_jumin = '$jumin'";

	$salary = $conn->get_array($sql);

	if (!$salary['other_pay_name_1']) $salary['other_pay_name_1'] = '기타1';
	if (!$salary['other_pay_name_2']) $salary['other_pay_name_2'] = '기타2';

	if (!$salary['other_amt_name_1']) $salary['other_amt_name_1'] = '기타공제1';
	if (!$salary['other_amt_name_2']) $salary['other_amt_name_2'] = '기타공제2';
	if (!$salary['other_amt_name_3']) $salary['other_amt_name_3'] = '기타공제3';
	if (!$salary['other_amt_name_4']) $salary['other_amt_name_4'] = '기타공제4';
	if (!$salary['other_amt_name_5']) $salary['other_amt_name_5'] = '기타공제5';
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function list(){
	var f = document.f;

	f.action = 'salary_edit_list.php?year=<?=$year;?>&month=<?=$month;?>&page=<?=$page;?>';
	f.submit();
}

function limit_no(object, limit, msg, next){
	if (parseInt(__commaUnset(object.value), 10) > limit){
		alert(msg);
		object.value = object.tag;
		object.focus();
		object.select();
		return false;
	}

	var sum_result = eval(next);

	if (!sum_result){
		object.value = object.tag;
		object.focus();
		object.select();
	}

	return sum_result;
}

// 기본급여 보전수당
function sum_pay(){
	var f = document.f;

	var	base_pay		= __str2num(f.base_pay.value);
	var	weekly_pay		= __str2num(f.weekly_pay.value);
	var	paid_pay		= __str2num(f.paid_pay.value);
	var	bath_pay		= __str2num(f.bath_pay.value);
	var	nursing_pay		= __str2num(f.nursing_pay.value);
	var meal_pay		= __str2num(f.meal_pay.value);
	var car_keep_pay	= __str2num(f.car_keep_pay.value);
	var bojeon_pay		= __str2num(f.bojeon_pay.value);
	var bojeon_max		= __str2num(f.bojeon_max.value);

	if (bojeon_max < meal_pay + car_keep_pay){
		alert('식대보조비와 차량유지비의 합이 보전수당인 '+__commaSet(bojeon_max)+'을 넘을 수 없습니다. 확인하여 주십시오.');
		return false;
	}

	f.bojeon_pay.value = __num2str(bojeon_max - (meal_pay + car_keep_pay));

	return true;
}

// 지급내역합계
function sum_give_pay(){
	var f = document.f;

	var prolong_pay			= __str2num(f.prolong_pay.value);
	var night_pay			= __str2num(f.night_pay.value);
	var holiday_pay			= __str2num(f.holiday_pay.value);
	var holiday_prolong_pay	= __str2num(f.holiday_prolong_pay.value);
	var holiday_night_pay	= __str2num(f.holiday_night_pay.value);
	var rank_pay			= __str2num(f.rank_pay.value);
	var long_pay			= __str2num(f.long_pay.value);

	f.tot_sudang_pay.value	= __num2str(prolong_pay + night_pay + holiday_pay + holiday_prolong_pay + holiday_night_pay + rank_pay + long_pay);

	sum_diff_pay();
}

// 기타수당합계
function sum_other_pay(){
	var f = document.f;

	var last_month_pay	= __str2num(f.last_month_pay.value);
	var yearly_pay		= __str2num(f.yearly_pay.value);
	var traffic_pay		= __str2num(f.traffic_pay.value);
	var other_pay_1		= __str2num(f.other_pay_1.value);
	var other_pay_2		= __str2num(f.other_pay_2.value);

	f.tot_other_pay.value   = __num2str(last_month_pay + yearly_pay + other_pay_1 + other_pay_2);
	//f.basic_total_amt.value = __num2str(__str2num(f.tot_basic_pay.value) + __str2num(f.tot_other_pay.value));
	//f.basic_total_amt_temp.value = f.basic_total_amt.value;

	sum_diff_pay();
}

// 기타공제합계
function sum_other_deduct_pay(){
	var f = document.f;

	var kabul_amt	= __str2num(f.kabul_amt.value);
	var expense_amt	= __str2num(f.expense_amt.value);
	var other_amt_1	= __str2num(f.other_amt_1.value);
	var other_amt_2	= __str2num(f.other_amt_2.value);
	var other_amt_3	= __str2num(f.other_amt_3.value);
	var other_amt_4	= __str2num(f.other_amt_4.value);
	var other_amt_5	= __str2num(f.other_amt_5.value);
	//var tot_ins_pay	= __str2num(f.tot_ins_pay.value);
	//var tot_tax_pay	= __str2num(f.tot_tax_pay.value);

	f.tot_deduct_pay.value   = __num2str(kabul_amt + expense_amt + other_amt_1 + other_amt_2 + other_amt_3 + other_amt_4 + other_amt_5);
	//f.basic_deduct_amt.value = __num2str(__str2num(f.tot_deduct_pay.value) + tot_ins_pay + tot_tax_pay);
	//f.basic_deduct_amt_temp.value = f.basic_deduct_amt.value;

	sum_diff_pay();
}

// 추가 기타수당, 공제합계
function sum_addon_pay(object, target, sum_object){
	var f = document.f;

	var pay      = document.getElementsByName(target);
	var temp_pay = 0;

	object.value = cutOff(__str2num(object.value));

	for(var i=0; i<pay.length; i++){
		temp_pay += __str2num(pay[i].value);
	}

	sum_object.value = __num2str(temp_pay);

	sum_diff_pay();
}

// 차인지급액
function sum_diff_pay(){
	var f = document.f;

	var basic_total_amt		= __str2num(f.basic_total_amt.value);
	var basic_deduct_amt	= __str2num(f.basic_deduct_amt.value);
	var tot_ins_pay			= __str2num(f.tot_ins_pay.value);
	var tot_tax_pay			= __str2num(f.tot_tax_pay.value);

	f.basic_total_amt.value = __num2str(__str2num(f.tot_basic_pay.value) + __str2num(f.tot_sudang_pay.value) + __str2num(f.tot_other_pay.value));
	f.basic_total_amt_temp.value = f.basic_total_amt.value;

	f.basic_deduct_amt.value = __num2str(__str2num(f.tot_deduct_pay.value) + tot_ins_pay + tot_tax_pay);
	f.basic_deduct_amt_temp.value = f.basic_deduct_amt.value;

	f.tot_pay_amt.value    = __num2str(__str2num(f.extra_total_pay.value)  + basic_total_amt);
	f.tot_deduct_amt.value = __num2str(__str2num(f.deduct_total_pay.value) + basic_deduct_amt);

	f.diff_amt.value     = __num2str(basic_total_amt - basic_deduct_amt);
	f.total_amount.value = __num2str(__str2num(f.tot_pay_amt.value) - __str2num(f.tot_deduct_amt.value));
}

window.onload = function(){
	__init_form(document.f);
}
-->
</script>

<form name="f" method="post">

<div class="title">급여조정</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="50px">
		<col width="80px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년월</th>
			<td class="left"><?=$year;?>.<?=$month;?></td>
			<th>요양보호사명</th>
			<td class="left"><?=$name;?></td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="list();">이전</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="30px">
		<col width="250px">
		<col width="30px">
		<col width="250px">
		<col width="30px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head last" colspan="6">근무내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center border_top border_bottom">기<br>본<br>근<br>무</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>근무일수</th>
							<td class="last"><input name="work_cnt" type="text" value="<?=$salary['work_cnt'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>근무시간</th>
							<td class="last"><input name="work_time" type="text" value="<?=$salary['work_time'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>주휴일수</th>
							<td class="last"><input name="weekly_cnt" type="text" value="<?=$salary['weekly_cnt'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>유급일수</th>
							<td class="last"><input name="paid_cnt" type="text" value="<?=$salary['paid_cnt'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>목욕횟수</th>
							<td class="last"><input name="bath_cnt" type="text" value="<?=$salary['bath_cnt'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th class="bottom">간호횟수</th>
							<td class="bottom last"><input name="nursing_cnt" type="text" value="<?=$salary['nursing_cnt'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom">초<br>과<br>근<br>무</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>연장근무시간</th>
							<td class="last"><input name="prolong_hour" type="text" value="<?=$salary['prolong_hour'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>야간근무시간</th>
							<td class="last"><input name="night_hour" type="text" value="<?=$salary['night_hour'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일근무시간</th>
							<td class="last"><input name="holiday_hour" type="text" value="<?=$salary['holiday_hour'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일연장시간</th>
							<td class="last"><input name="holiday_prolong_hour" type="text" value="<?=$salary['holiday_prolong_hour'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일야간시간</th>
							<td class="last"><input name="holiday_night_hour" type="text" value="<?=$salary['holiday_night_hour'];?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom"></th>
			<td class="top last border_top border_bottom"></td>
		</tr>
	</tbody>
	<thead>
		<tr>
			<th class="head last" colspan="6">지급내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center border_top border_bottom">기<br>본<br>급<br>여</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>기본급</th>
							<td class="last"><input name="base_pay" type="text" value="<?=number_format($salary['base_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>주휴수당</th>
							<td class="last"><input name="weekly_pay" type="text" value="<?=number_format($salary['weekly_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>유급수당</th>
							<td class="last"><input name="paid_pay" type="text" value="<?=number_format($salary['paid_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>목욕수당</th>
							<td class="last"><input name="bath_pay" type="text" value="<?=number_format($salary['bath_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>간호수당</th>
							<td class="last"><input name="nursing_pay" type="text" value="<?=number_format($salary['nursing_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>식대보조비</th>
							<td class="last"><input name="meal_pay" type="text" value="<?=number_format($salary['meal_pay']);?>" tag="<?=$salary['meal_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_MEAL_AMT__;?>, '식대보조비 최대금액은 <?=__MAX_MEAL_AMT__;?>입니다.','sum_pay()');"></td>
						</tr>
						<tr>
							<th>차량유지비</th>
							<td class="last"><input name="car_keep_pay" type="text" value="<?=number_format($salary['car_keep_pay']);?>" tag="<?=$salary['car_keep_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_CARKEEP_AMT__;?>, '차량유지비 최대금액은 <?=__MAX_CARKEEP_AMT__;?>입니다.','sum_pay()');"></td>
						</tr>
						<tr>
							<th>보전수당</th>
							<td class="last">
								<input name="bojeon_pay" type="text"   value="<?=number_format($salary['bojeon_pay']);?>" class="number readonly" alt="not" readonly>
								<input name="bojeon_max" type="hidden" value="<?=$salary['bojeon_pay']+$salary['meal_pay']+$salary['car_keep_pay'];?>">
							</td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_basic_pay" type="text" value="<?=number_format($salary['tot_basic_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom">초<br>과<br>근<br>무<br>수<br>당</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>연장근무수당</th>
							<td class="last"><input name="prolong_pay" type="text" value="<?=number_format($salary['prolong_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>야간근무수당</th>
							<td class="last"><input name="night_pay" type="text" value="<?=number_format($salary['night_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일근무수당</th>
							<td class="last"><input name="holiday_pay" type="text" value="<?=number_format($salary['holiday_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일연장수당</th>
							<td class="last"><input name="holiday_prolong_pay" type="text" value="<?=number_format($salary['holiday_prolong_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>휴일야간수당</th>
							<td class="last"><input name="holiday_night_pay" type="text" value="<?=number_format($salary['holiday_night_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>직급수당</th>
							<td class="last"><input name="rank_pay" type="text" value="<?=number_format($salary['rank_pay']);?>" tag="<?=$salary['rank_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give_pay();"></td>
						</tr>
						<tr>
							<th>근속수당</th>
							<td class="last"><input name="long_pay" type="text" value="<?=number_format($salary['long_pay']);?>" tag="<?=$salary['long_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give_pay();"></td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td class="last">&nbsp;</td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_sudang_pay" type="text" value="<?=number_format($salary['tot_sudang_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom">기<br>타<br>수<br>당</th>
			<td class="top last border_top border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>전월미지급</th>
							<td class="last"><input name="last_month_pay" type="text" value="<?=number_format($salary['last_month_pay']);?>" tag="<?=$salary['last_month_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_pay();"></td>
						</tr>
						<tr>
							<th>년차/휴가</th>
							<td class="last"><input name="yearly_pay" type="text" value="<?=number_format($salary['yearly_pay']);?>" tag="<?=$salary['yearly_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_pay();"></td>
						</tr>
						<tr>
							<th>교통비</th>
							<td class="last"><input name="traffic_pay" type="text" value="<?=number_format($salary['traffic_pay']);?>" tag="<?=$salary['traffic_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_pay_name_1" type="text" value="<?=$salary['other_pay_name_1'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_pay_1" type="text" value="<?=number_format($salary['other_pay_1']);?>" tag="<?=$salary['other_pay_1'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_pay_name_2" type="text" value="<?=$salary['other_pay_name_2'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_pay_2" type="text" value="<?=number_format($salary['other_pay_2']);?>" tag="<?=$salary['other_pay_2'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_pay();"></td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td class="last">&nbsp;</td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td class="last">&nbsp;</td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td class="last">&nbsp;</td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_other_pay" type="text" value="<?=number_format($salary['tot_other_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	<thead>
		<tr>
			<th class="head last" colspan="6">공제내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center border_top border_bottom">보<br>험<br>항<br>목</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>국민연금</th>
							<td class="last"><input name="pension_amt" type="text" value="<?=number_format($salary['pension_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>건강보험</th>
							<td class="last"><input name="health_amt" type="text" value="<?=number_format($salary['health_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>장기요양</th>
							<td class="last"><input name="care_amt" type="text" value="<?=number_format($salary['care_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>고용보험</th>
							<td class="last"><input name="employ_amt" type="text" value="<?=number_format($salary['employ_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_ins_pay" type="text" value="<?=number_format($salary['tot_ins_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom" rowspan="2">기<br>타<br>공<br>제</th>
			<td class="top border_top border_right border_bottom" rowspan="2">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>가불금</th>
							<td class="last"><input name="kabul_amt" type="text" value="<?=number_format($salary['kabul_amt']);?>" tag="<?=$salary['kabul_amt'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th>통신비</th>
							<td class="last"><input name="expense_amt" type="text" value="<?=number_format($salary['expense_amt']);?>" tag="<?=$salary['expense_amt'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_amt_name_1" type="text" value="<?=$salary['other_amt_name_1'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_amt_1" type="text" value="<?=number_format($salary['other_amt_1']);?>" tag="<?=$salary['other_amt_1'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_amt_name_2" type="text" value="<?=$salary['other_amt_name_2'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_amt_2" type="text" value="<?=number_format($salary['other_amt_2']);?>" tag="<?=$salary['other_amt_2'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_amt_name_3" type="text" value="<?=$salary['other_amt_name_3'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_amt_3" type="text" value="<?=number_format($salary['other_amt_3']);?>" tag="<?=$salary['other_amt_3'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_amt_name_4" type="text" value="<?=$salary['other_amt_name_4'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_amt_4" type="text" value="<?=number_format($salary['other_amt_4']);?>" tag="<?=$salary['other_amt_4'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th><input name="other_amt_name_5" type="text" value="<?=$salary['other_amt_name_5'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="other_amt_5" type="text" value="<?=number_format($salary['other_amt_5']);?>" tag="<?=$salary['other_amt_5'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_other_deduct_pay();"></td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_deduct_pay" type="text" value="<?=number_format($salary['tot_deduct_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom" rowspan="2">급<br>여</th>
			<td class="top last border_top border_bottom" rowspan="2">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>총지급액</th>
							<td class="last"><input name="basic_total_amt" type="text" value="<?=number_format($salary['basic_total_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>공제금액</th>
							<td class="last"><input name="basic_deduct_amt" type="text" value="<?=number_format($salary['basic_deduct_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>차인지급액</th>
							<td class="last"><input name="diff_amt" type="text" value="<?=number_format($salary['diff_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center border_top border_bottom">소<br>득<br>세</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>갑근세</th>
							<td class="last"><input name="tax_amt_1" type="text" value="<?=number_format($salary['tax_amt_1']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>주민세</th>
							<td class="last"><input name="tax_amt_2" type="text" value="<?=number_format($salary['tax_amt_2']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="tot_tax_pay" type="text" value="<?=number_format($salary['tot_tax_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	<thead>
		<tr>
			<th class="head last" colspan="6">추가 및 공제 내역 등록</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center border_top border_bottom">기<br>타<br>수<br>당</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">지급항목</th>
							<th class="head last">금액</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_1'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_1']);?>" tag="<?=$salary['extra_pay_1'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_2'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_2']);?>" tag="<?=$salary['extra_pay_2'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_3'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_3']);?>" tag="<?=$salary['extra_pay_3'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_4'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_4']);?>" tag="<?=$salary['extra_pay_4'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_5'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_5']);?>" tag="<?=$salary['extra_pay_5'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_6'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_6']);?>" tag="<?=$salary['extra_pay_6'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="extra_name[]" type="text" value="<?=$salary['extra_name_7'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="extra_pay[]" type="text" value="<?=number_format($salary['extra_pay_7']);?>" tag="<?=$salary['extra_pay_7'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'extra_pay[]', document.f.extra_total_pay);"></td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="extra_total_pay" type="text" value="<?=number_format($salary['extra_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom">기<br>타<br>공<br>제</th>
			<td class="top border_top border_right border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">공제항목</th>
							<th class="head last">금액</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_1'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_1']);?>" tag="<?=$salary['deduct_pay_1'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_2'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_2']);?>" tag="<?=$salary['deduct_pay_2'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_3'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_3']);?>" tag="<?=$salary['deduct_pay_3'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_4'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_4']);?>" tag="<?=$salary['deduct_pay_4'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_5'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_5']);?>" tag="<?=$salary['deduct_pay_5'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_6'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_6']);?>" tag="<?=$salary['deduct_pay_6'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th><input name="deduct_name[]" type="text" value="<?=$salary['deduct_name_7'];?>" class="readonly" style="background-color:#f7faff;"></th>
							<td class="last"><input name="deduct_pay[]" type="text" value="<?=number_format($salary['deduct_pay_7']);?>" tag="<?=$salary['deduct_pay_7'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_addon_pay(this, 'deduct_pay[]', document.f.deduct_total_pay);"></td>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="deduct_total_pay" type="text" value="<?=number_format($salary['deduct_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<th class="center border_top border_bottom">급<br>여<br>내<br>역</th>
			<td class="top last border_top border_bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40%">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>지급액</th>
							<td class="last"><input name="basic_total_amt_temp" type="text" value="<?=number_format($salary['basic_total_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>추가지급액</th>
							<td class="last"><input name="extra_temp_pay" type="text" value="<?=number_format($salary['extra_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>지급합계</th>
							<td class="last"><input name="tot_pay_amt" type="text" value="<?=number_format($salary['basic_total_amt']+$salary['extra_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th class="last" colspan="2">&nbsp;</th>
						</tr>
						<tr>
							<th>공제금액</th>
							<td class="last"><input name="basic_deduct_amt_temp" type="text" value="<?=number_format($salary['basic_deduct_amt']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>추가공제</th>
							<td class="last"><input name="deduct_temp_pay" type="text" value="<?=number_format($salary['deduct_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th>공제합계</th>
							<td class="last"><input name="tot_deduct_amt" type="text" value="<?=number_format($salary['basic_deduct_amt']+$salary['deduct_total_pay']);?>" class="number readonly" alt="not" readonly></td>
						</tr>
						<tr>
							<th class="last" colspan="2">&nbsp;</th>
						</tr>
						<tr>
							<th class="bottom">합계</th>
							<td class="bottom last"><input name="total_amount" type="text" value="<?=number_format(($salary['basic_total_amt']+$salary['extra_total_pay'])-($salary['basic_deduct_amt']+$salary['deduct_total_pay']));?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="6">&nbsp;</td>
		</tr>
	</tbody>
</table>

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
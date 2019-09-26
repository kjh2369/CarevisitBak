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

	$sql = "select m02_yname as name
			,      m02_rank_pay as pay
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_mkind  = '$kind'
			   and m02_yjumin = '$jumin'";

	$member = $conn->get_array($sql);

	$name	  = $member['name'];
	$rank_pay = $member['pay'];

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
			,      salary_basic.prolong_hour + salary_basic.night_hour + salary_basic.holiday_hour + salary_basic.holiday_prolong_hour + salary_basic.holiday_night_hour as tot_sudang_hour

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
			,      salary_basic.rank_pay
			,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay + salary_basic.rank_pay as tot_sudang_pay

			,      salary_basic.pension_amt
			,      salary_basic.health_amt
			,      salary_basic.care_amt
			,      salary_basic.employ_amt
			,      salary_basic.pension_amt + salary_basic.health_amt + salary_basic.care_amt + salary_basic.employ_amt as tot_ins_pay

			,      salary_basic.tax_amt_1
			,      salary_basic.tax_amt_2
			,      salary_basic.tax_amt_1 + salary_basic.tax_amt_2 as tot_tax_pay

			,      salary_amt.basic_total_amt
			,      salary_amt.addon_total_amt
			,      salary_amt.total_amt
			,      salary_amt.basic_deduct_amt
			,      salary_amt.addon_deduct_amt
			,      salary_amt.deduct_amt
			,      salary_amt.diff_amt

			  from salary_basic
			  left join salary_amt
				on salary_amt.org_no       = salary_basic.org_no
			   and salary_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_amt.salary_jumin = salary_basic.salary_jumin
			 where salary_basic.org_no       = '$code'
			   and salary_basic.salary_yymm  = '$year$month'
			   and salary_basic.salary_jumin = '$jumin'";

	$salary = $conn->get_array($sql);

	if (!$salary['rank_pay']) $salary['rank_pay'] = $rank_pay;

	$sql = "select salary_type
			,      salary_index
			,      salary_subject
			,      salary_pay
			  from salary_addon
			 where org_no = '$code'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$index[1] = -1;
	$index[2] = -1;

	$addon_pay[1] = $salary['rank_pay'];
	$addon_pay[2] = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$index[intval($row['salary_type'])] ++;

		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['index']	= $row['salary_index'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['subject']	= $row['salary_subject'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['pay']		= $row['salary_pay'];
	}

	$conn->row_free();

	$sql = "select salary_type
			,      salary_index
			,      salary_pay
			  from salary_addon_pay
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$temp_addon_count[1] = sizeof($salary_addon[1]);
	$temp_addon_count[2] = sizeof($salary_addon[2]);

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		for($j=0; $j<$temp_addon_count[$row['salary_type']]; $j++){
			if ($salary_addon[$row['salary_type']][$j]['index'] == $row['salary_index']){
				$salary_addon[$row['salary_type']][$j]['pay'] = $row['salary_pay'];
				break;
			}
		}
	}

	for($i=1; $i<=2; $i++){
		for($j=0; $j<$temp_addon_count[$i]; $j++){
			$addon_pay[$i] += $salary_addon[$i][$j]['pay'];
		}
	}

	$conn->row_free();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function list(){
	var f = document.f;

	f.action = 'salary_edit_list.php?year=<?=$year;?>&month=<?=$month;?>&page=<?=$page;?>';
	f.submit();
}

function save(){
	var f = document.f;

	if (!confirm('<?=$year;?>년 <?=$month;?>월 요양보호사(<?=$name;?>)의 급여내역을 수정하시겠습니까?')) return;

	f.action = 'salary_edit_conf.php';
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
function sum_bojeon(){
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

// 지급항목 합계
function sum_give(){
	var f = document.f;

	var pay	= __str2num(f.rank_pay.value) + sum_sub('1');

	f.tot_1_addon_pay.value = __num2str(pay);

	sum_diff();
}

// 공제항목 합계
function sum_deduct(){
	var f = document.f;

	var pay	= sum_sub('2');

	f.tot_2_addon_pay.value = __num2str(pay);

	sum_diff();
}

function sum_sub(type){
	var object	= document.getElementsByName(type+'_pay[]');
	var pay		= 0;

	for(var i=0; i<object.length; i++){
		pay += __str2num(object[i].value);
	}

	return pay;
}

// 합계금액
function sum_diff(){
	var f = document.f;

	var tot_basic_pay	= __str2num(f.tot_basic_pay.value);
	var tot_sudang_pay	= __str2num(f.tot_sudang_pay.value);
	var tot_1_addon_pay	= __str2num(f.tot_1_addon_pay.value);

	var tot_ins_pay		= __str2num(f.tot_ins_pay.value);
	var tot_tax_pay		= __str2num(f.tot_tax_pay.value);
	var tot_2_addon_pay	= __str2num(f.tot_2_addon_pay.value);

	f.tot_pay.value		= __num2str(tot_basic_pay + tot_sudang_pay + tot_1_addon_pay)
	f.tot_deduct.value	= __num2str(tot_ins_pay + tot_tax_pay + tot_2_addon_pay)
	f.tot_diff.value	= __num2str(__str2num(f.tot_pay.value) - __str2num(f.tot_deduct.value));
}

window.onload = function(){
	__init_form(document.f);
	sum_diff();
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
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="save();">저장</button></span>
				<span class="btn_pack m icon"><span class="pdf"></span><button type="button" onclick="_payslip(document.f.code.value, document.f.kind.value, document.f.year.value, document.f.month.value, document.f.jumin.value);">명세서</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px; border-bottom:none;">
	<colgroup>
		<col width="70px">
		<col width="75px" span="9">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">&nbsp;</th>
			<th class="head bold" colspan="9">기본근무(기본급여)</th>
			<th class="head last border_top border_left border_right" rowspan="2">합계(A)</th>
		</tr>
		<tr>
			<th class="head">근무일수</th>
			<th class="head">근무시간</th>
			<th class="head">주휴일수</th>
			<th class="head">유급일수</th>
			<th class="head">목욕횟수</th>
			<th class="head">간호횟수</th>
			<th class="head">식대보조</th>
			<th class="head">차량유지비</th>
			<th class="head">보전수당</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="head">횟수/시간</th>
			<td class="center"><input name="work_cnt" type="text" value="<?=$salary['work_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="work_time" type="text" value="<?=$salary['work_time'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="weekly_cnt" type="text" value="<?=$salary['weekly_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="paid_cnt" type="text" value="<?=$salary['paid_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="bath_cnt" type="text" value="<?=$salary['bath_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="nursing_cnt" type="text" value="<?=$salary['nursing_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right last border_left border_right">&nbsp;</td>
		</tr>
		<tr>
			<th class="head">금액</th>
			<td class="right border_b">-</td>
			<td class="center border_b"><input name="base_pay" type="text" value="<?=number_format($salary['base_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="weekly_pay" type="text" value="<?=number_format($salary['weekly_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="paid_pay" type="text" value="<?=number_format($salary['paid_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="bath_pay" type="text" value="<?=number_format($salary['bath_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="nursing_pay" type="text" value="<?=number_format($salary['nursing_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="meal_pay" type="text" value="<?=number_format($salary['meal_pay']);?>" tag="<?=$salary['meal_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_MEAL_AMT__;?>, '식대보조비 최대금액은 <?=__MAX_MEAL_AMT__;?>입니다.','sum_bojeon()');"></td>
			<td class="center border_b"><input name="car_keep_pay" type="text" value="<?=number_format($salary['car_keep_pay']);?>" tag="<?=$salary['car_keep_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_CARKEEP_AMT__;?>, '차량유지비 최대금액은 <?=__MAX_CARKEEP_AMT__;?>입니다.','sum_bojeon()');"></td>
			<td class="center border_b">
				<input name="bojeon_pay" type="text"   value="<?=number_format($salary['bojeon_pay']);?>" class="number readonly" alt="not" readonly>
				<input name="bojeon_max" type="hidden" value="<?=$salary['bojeon_pay']+$salary['meal_pay']+$salary['car_keep_pay'];?>">
			</td>
			<td class="center last border_left border_bottom border_right"><input name="tot_basic_pay" type="text" value="<?=number_format($salary['tot_basic_pay']);?>" class="number readonly" alt="not" readonly></td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:25px; border-bottom:none;">
	<colgroup>
		<col width="70px" span="12">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">&nbsp;</th>
			<th class="head bold" colspan="5">초과근무</th>
			<th class="head border_left border_top border_right" rowspan="2">합계(B)</th>
			<th class="head" colspan="4">보험항목</th>
			<th class="head last border_left border_top border_right" rowspan="2">합계(C)</th>
		</tr>
		<tr>
			<th class="head">연장</th>
			<th class="head">야간</th>
			<th class="head">휴일</th>
			<th class="head">휴일연장</th>
			<th class="head">휴일야간</th>
			<th class="head">국민연금</th>
			<th class="head">건강보험</th>
			<th class="head">장기요양</th>
			<th class="head">고용보험</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="head">시간</th>
			<td class="center"><input name="prolong_hour" type="text" value="<?=$salary['prolong_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center"><input name="night_hour" type="text" value="<?=$salary['night_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center"><input name="holiday_hour" type="text" value="<?=$salary['holiday_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center"><input name="holiday_prolong_hour" type="text" value="<?=$salary['holiday_prolong_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center"><input name="holiday_night_hour" type="text" value="<?=$salary['holiday_night_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_left border_right"><input name="tot_sudang_hour" type="text" value="<?=$salary['tot_sudang_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right last border_left border_right">-</td>
		</tr>
		<tr>
			<th class="head">금액</th>
			<td class="center border_b"><input name="prolong_pay" type="text" value="<?=number_format($salary['prolong_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="night_pay" type="text" value="<?=number_format($salary['night_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="holiday_pay" type="text" value="<?=number_format($salary['holiday_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="holiday_prolong_pay" type="text" value="<?=number_format($salary['holiday_prolong_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="holiday_night_pay" type="text" value="<?=number_format($salary['holiday_night_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_left border_right border_bottom"><input name="tot_sudang_pay" type="text" value="<?=$salary['tot_sudang_pay'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="pension_amt" type="text" value="<?=number_format($salary['pension_amt']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="health_amt" type="text" value="<?=number_format($salary['health_amt']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_b"><input name="care_amt" type="text" value="<?=number_format($salary['care_amt']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center border_bottom"><input name="employ_amt" type="text" value="<?=number_format($salary['employ_amt']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			<td class="center last border_left border_right border_bottom"><input name="tot_ins_pay" type="text" value="<?=number_format($salary['tot_ins_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
		</tr>
	</tbody>
</table>

<div style="width:auto; text-align:right; padding:10px; float:left;">
	<table class="my_table my_border" style="width:auto; border:2px solid #0e69b0;">
		<colgroup>
			<col width="152px" span="3">
		</colgroup>
		<thead>
			<tr>
				<th class="head bold">급여총액<span style="font-weight:normal;">(A + B + E)</span></th>
				<th class="head bold">공제금액<span style="font-weight:normal;">(C + D + F)</span></th>
				<th class="head bold">차인지급액</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center"><input name="tot_pay"	type="text" value="0" class="number readonly bold" style="text-align:center;" style="width:98%;" alt="not" readonly></td>
				<td class="center"><input name="tot_deduct" type="text" value="0" class="number readonly bold" style="text-align:center;" style="width:98%;" alt="not" readonly></td>
				<td class="center"><input name="tot_diff"	type="text" value="0" class="number readonly bold" style="text-align:center;" style="width:98%;" alt="not" readonly></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:auto; text-align:right; float:right;">
	<table class="my_table" style="border-bottom:none;">
		<colgroup>
			<col width="73px">
			<col width="73px">
			<col width="73px">
			<col width="146px">
		</colgroup>
		<tbody>
			<tr>
				<th class="head" rowspan="2" style="border-left:1px solid #a6c0f3;">소득세</th>
				<th class="head">갑근세</th>
				<th class="head">주민세</th>
				<th class="head last border_left border_right">합계(D)</th>
			</tr>
			<tr>
				<td class="center border_b"><input name="tax_amt_1" type="text" value="<?=number_format($salary['tax_amt_1']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
				<td class="center border_b"><input name="tax_amt_2" type="text" value="<?=number_format($salary['tax_amt_2']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
				<td class="center last border_left border_right border_bottom"><input name="tot_tax_pay" type="text" value="<?=number_format($salary['tot_tax_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
			</tr>
		</tbody>
	</table>
</div>

<table class="my_table my_border" style="margin-top:25px; border-bottom:none;">
	<colgroup>
		<col width="50%">
	</colgroup>
	<thead>
		<tr>
			<th class="head bold">지 급 수 당(+)</th>
			<th class="head bold last">공 제 항 목(-)</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top bottom">
				<table class="my_table my_border" style="width:100%; border-top:none;">
					<colgroup>
						<col width="40px">
						<col>
						<col width="100px">
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">명칭</th>
							<th class="head last">금액</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th class="center">1</th>
							<td class="left">직급수당</td>
							<td class="center last"><input name="rank_pay" type="text" value="<?=number_format($salary['rank_pay']);?>" tag="<?=$salary['rank_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give();"></td>
						</tr><?
						$count = sizeof($salary_addon[1]);

						for($i=0; $i<$count; $i++){?>
							<tr>
								<th class="center"><?=$i+2;?></th>
								<td class="left"><?=$salary_addon[1][$i]['subject'];?></td>
								<td class="center last"><input name="1_pay[]" type="text" value="<?=number_format($salary_addon[1][$i]['pay']);?>" tag="<?=$salary_addon[1][$i]['pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give();"></td>
								<input type="hidden" name="1_index[]" value="<?=$salary_addon[1][$i]['index'];?>">
							</tr><?
						}?>
						<tr>
							<th class="center">-</th>
							<td class="left">합계(E)</td>
							<td class="center last"><input name="tot_1_addon_pay" type="text" value="<?=number_format($addon_pay[1]);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="top bottom last">
				<table class="my_table my_border" style="width:100%; border-top:none;">
					<colgroup>
						<col width="40px">
						<col>
						<col width="100px">
					</colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">명칭</th>
							<th class="head last">금액</th>
						</tr>
					</thead>
					<tbody>
					<?
						$count = sizeof($salary_addon[2]);

						for($i=0; $i<$count; $i++){?>
							<tr>
								<th class="center"><?=$i+1;?></th>
								<td class="left"><?=$salary_addon[2][$i]['subject'];?></td>
								<td class="center last"><input name="2_pay[]" type="text" value="<?=number_format($salary_addon[2][$i]['pay']);?>" tag="<?=$salary_addon[2][$i]['pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_deduct();"></td>
								<input type="hidden" name="2_index[]" value="<?=$salary_addon[2][$i]['index'];?>">
							</tr><?
						}?>
						<tr>
							<th class="center">-</th>
							<td class="left">합계(F)</td>
							<td class="center last"><input name="tot_2_addon_pay" type="text" value="<?=number_format($addon_pay[2]);?>" class="number readonly" alt="not" readonly></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<br>

<input type="hidden" name="code"  value="<?=$code;?>">
<input type="hidden" name="kind"  value="<?=$kind;?>">
<input type="hidden" name="year"  value="<?=$year;?>">
<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="jumin" value="<?=$ed->en($jumin);?>">
<input type="hidden" name="page"  value="<?=$page;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
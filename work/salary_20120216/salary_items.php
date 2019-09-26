<?
	if ($is_preview == true){
		$edit_mode   = null;
	}
?>
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
			<th class="head">식대보조</th>
			<th class="head">차량유지비</th>
			<th class="head">보전수당</th>
			<th class="head">유급일수</th>
			<th class="head">목욕횟수</th>
			<th class="head">간호횟수</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="head">횟수/시간</th>
			<td class="center"><input name="work_cnt" type="text" value="<?=$salary['work_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="work_time" type="text" value="<?=$salary['work_time'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="weekly_cnt" type="text" value="<?=$salary['weekly_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="right">-</td>
			<td class="center"><input name="paid_cnt" type="text" value="<?=$salary['paid_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="bath_cnt" type="text" value="<?=$salary['bath_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="center"><input name="nursing_cnt" type="text" value="<?=$salary['nursing_cnt'];?>" class="number readonly" alt="not" readonly></td>
			<td class="right last border_left border_right">-</td>
		</tr>
		<tr>
			<th class="head">금액</th>
			<td class="right border_b">-</td>
			<td class="center border_b"><input name="base_pay" type="text" value="<?=number_format($salary['base_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="weekly_pay" type="text" value="<?=number_format($salary['weekly_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="meal_pay" type="text" value="<?=number_format($salary['meal_pay']);?>" tag="<?=$salary['meal_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_MEAL_AMT__;?>, '식대보조비 최대금액은 <?=__MAX_MEAL_AMT__;?>입니다.','sum_bojeon()');" <? if(!$edit_mode){?>readonly<?} ?>></td>
			<td class="center border_b"><input name="car_keep_pay" type="text" value="<?=number_format($salary['car_keep_pay']);?>" tag="<?=$salary['car_keep_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="return limit_no(this, <?=__MAX_CARKEEP_AMT__;?>, '차량유지비 최대금액은 <?=__MAX_CARKEEP_AMT__;?>입니다.','sum_bojeon()');" <? if(!$edit_mode){?>readonly<?} ?>></td>
			<td class="center border_b">
				<input name="bojeon_pay" type="text"   value="<?=number_format($salary['bojeon_pay']);?>" class="number readonly" alt="not" readonly>
				<input name="bojeon_max" type="hidden" value="<?=$salary['bojeon_pay']+$salary['meal_pay']+$salary['car_keep_pay'];?>">
			</td>
			<td class="center border_b"><input name="paid_pay" type="text" value="<?=number_format($salary['paid_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="bath_pay" type="text" value="<?=number_format($salary['bath_pay']);?>" class="number readonly" alt="not" readonly></td>
			<td class="center border_b"><input name="nursing_pay" type="text" value="<?=number_format($salary['nursing_pay']);?>" class="number readonly" alt="not" readonly></td>
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
			<th class="head bold" colspan="4">보험항목</th>
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
			<td class="right border_left border_right">-<input name="tot_sudang_hour" type="hidden" value="<?=$salary['tot_sudang_hour'];?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
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
			<td class="center border_left border_right border_bottom"><input name="tot_sudang_pay" type="text" value="<?=number_format($salary['tot_sudang_pay']);?>" class="number readonly" style="width:98%;" alt="not" readonly></td>
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
							<td class="center last"><input name="rank_pay" type="text" value="<?=number_format($salary['rank_pay']);?>" tag="<?=$salary['rank_pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give();" <? if(!$edit_mode){?>readonly<?} ?>></td>
						</tr><?
						$count = sizeof($salary_addon[1]);

						for($i=0; $i<$count; $i++){?>
							<tr>
								<th class="center"><?=$i+2;?></th>
								<td class="left"><?=$salary_addon[1][$i]['subject'];?></td>
								<td class="center last"><input name="1_pay[]" type="text" value="<?=number_format($salary_addon[1][$i]['pay']);?>" tag="<?=$salary_addon[1][$i]['pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_give();" <? if(!$edit_mode){?>readonly<?} ?>></td>
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
								<td class="center last"><input name="2_pay[]" type="text" value="<?=number_format($salary_addon[2][$i]['pay']);?>" tag="<?=$salary_addon[2][$i]['pay'];?>" class="number readonly" style="background-color:#f6f4d3;" onchange="sum_deduct();" <? if(!$edit_mode){?>readonly<?} ?>></td>
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
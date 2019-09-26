<table id="tbl_comm" class="my_table my_border_blue" style="width:100%;">
	<colgroup><?
		if ($IsCare){?>
			<col width="50px">
			<col width="60px">
			<col width="90px">
			<col width="75px">
			<col><?
		}else{?>
			<col width="80px">
			<col width="70px">
			<col width="80px">
			<col><?
		}?>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" colspan="5">공통항목</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="2">가맹가입일자</th>
			<td><input name="openDt" type="text" value="<?=$openDt;?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"></td>
			<th>법인번호</th>
			<td><input id="comNo" name="comNo" type="text" value="<?=$comNo;?>" maxlength="13" class="no_string"></td>
		</tr>
		<tr>
			<th colspan="2">&nbsp;</th>
			<td>&nbsp;</td>
			<th>급여지급일</th>
			<td>
				<select id="salaryDay" name="salaryDay" style="width:auto;"><?
				for($i=1; $i<=31; $i++){
					echo '<option value=\''.$i.'\' '.($salaryDay == $i ? 'selected' : '').'>'.$i.'</option>';
				}?>
				</select>일
			</td>
		</tr><?
		if ($IsCare){?>
			<tr>
				<th colspan="2">직원수</th>
				<td><input name="inwonsu" type="text" value="<?=$inwonsu;?>" maxlength="5" class="number" style="width:50px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}"></td>
				<th>처개비지급일</th>
				<td>
					<select id="dealDay" name="dealDay" style="width:auto;">
						<option value="">--</option><?
						for($i=1; $i<=31; $i++){
							echo '<option value=\''.$i.'\' '.($dealDay == $i ? 'selected' : '').'>'.$i.'</option>';
						}?>
					</select>일
				</td>
			</tr>
			<tr>
				<th class="center" rowspan="3">소정<br>근로</th>
				<th>시간</th>
				<td><input id="fixedHour" name="day_work_hour" type="text" value="<?=$day_work_hour;?>" maxlength="3" class="number" style="width:50px;" onKeyDown="__onlyNumber(this,'.');" onFocus="this.select();" tag="일근무기준시간을 입력하여 주십시오."></td>
				<td class="center last" rowspan="2" colspan="2">
					<button type="button" style="width:170px; height:45px; font-size:11px; font-weight:bold; line-height:15px;" onclick="setMemFxiedTimePay();"><?=date('Y');?>년도 전 직원 소정근로<br>시간/시급 일괄적용</button>
				</td>
			</tr>
			<tr>
				<th>시급</th>
				<td><input id="fixedHourly" name="day_hourly" type="text" value="<?=number_format($day_hourly);?>" tag="<?=$min_hourly;?>" maxlength="8" class="number" style="width:50px;" onKeyDown="__onlyNumber(this,'.');" onFocus="this.select();" tag="일근무기준시급을 입력하여 주십시오."></td>
			</tr>
			<tr>
				<th>일수(1주)</th>
				<td>
					<input id="fixedDays_5" name="fixedDays" type="radio" value="5" class="radio" style="margin-right:0;" <?=$fixedDays == 5 ? 'checked' : '';?>><label for="fixedDays_5">5일</label>
					<input id="fixedDays_6" name="fixedDays" type="radio" value="6" class="radio" style="margin-right:0;" <?=$fixedDays == 6 ? 'checked' : '';?>><label for="fixedDays_6">6일</label>
				</td>
				<th>일식대보조비</th>
				<td><input id="mealAmt" name="mealAmt" type="text" value="<?=number_format($liMealAmt);?>" maxlength="8" class="number" style="width:50px;"></td>
			</tr>

			<tr>
				<th>주휴</th>
				<td colspan="4">
					<input id="weeklyInYN" name="weeklyInYN" type="checkbox" value="Y" class="checkbox" <?=$weeklyInYN == 'Y' ? 'checked' : '';?>><label for="weeklyInYN">약정시급에 주휴수당 포함</label>
				</td>
			</tr>
			<?
			if($lsAnnualChange){ ?>
				<tr>
					<th rowspan="2">년차</th>
					<td colspan="4">
						<input id="annualInYN" name="annualInYN" type="checkbox" value="Y" class="checkbox" onclick="$('#annualYN').attr('checked', $(this).attr('checked')); /*$('#annualYN').attr('disabled', $(this).attr('checked'));*/" <?=$annualInYN == 'Y' ? 'checked' : '';?>><label for="annualInYN">약정시급에 연차수당 포함</label>
						<input id="annualYN" name="annualYN" type="checkbox" value="Y" class="checkbox" <?=$annualYN == 'Y' ? 'checked' : '';?>><label for="annualYN">월지급여부</label>
					</td>
				</tr>
				<tr>
					<th>지급구분</th>
					<td colspan="4">
						<input id="annualPayGbn_1" name="annualPayGbn" type="radio" class="radio" value="1" <?=$annualPayGbn != '' ? $annualPayGbn == '1' ? 'checked' : '' : 'checked'; ?>><label for="annualPayGbn_1">기존(1.25)</label>
						<input id="annualPayGbn_2" name="annualPayGbn" type="radio" class="radio" value="2" <?=$annualPayGbn == '2' ? 'checked' : ''; ?>><label for="annualPayGbn_2">차등지급</label></br>
						※ <span style="font-weight:bold;">차등지급 선택 시</span></br>&nbsp;&nbsp;-시급직원중 <span style="font-weight:bold; color:red;">"단시간(60시간이상)"</span> 선택된 사람</br>&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">(직원조회 -> 직원선택 교용형태)만 적용함.</span>
					</td>
				</tr><?
			}else { ?>
				<tr>
					<th>년차</th>
					<td colspan="4">
						<input id="annualInYN" name="annualInYN" type="checkbox" value="Y" class="checkbox" onclick="$('#annualYN').attr('checked', $(this).attr('checked')); /*$('#annualYN').attr('disabled', $(this).attr('checked'));*/" <?=$annualInYN == 'Y' ? 'checked' : '';?>><label for="annualInYN">약정시급에 연차수당 포함</label>
						<input id="annualYN" name="annualYN" type="checkbox" value="Y" class="checkbox" <?=$annualYN == 'Y' ? 'checked' : '';?>><label for="annualYN">월지급여부</label>
					</td>
				</tr><?
			} ?>
			<tr>
				<th rowspan="2">법정<br>공휴일</th>
				<th>인정여부</th>
				<td colspan="3">
					<input id="lawHolidayY" name="law_holiday_yn" type="radio" class="radio" value="Y" onclick="set_holiday_pay_yn('law');" <? if($law_holiday_yn == 'Y'){?>checked<?} ?>><label for="lawHolidayY">유</label>
					<input id="lawHolidayN" name="law_holiday_yn" type="radio" class="radio" value="N" onclick="set_holiday_pay_yn('law');" <? if($law_holiday_yn != 'Y'){?>checked<?} ?>><label for="lawHolidayN">무</label>
				</td>
			</tr>
			<tr>
				<th>급여여부</th>
				<td colspan="3">
					<input id="lawHolidayPayY" name="law_holiday_pay_yn" type="radio" class="radio" value="Y" <? if($law_holiday_yn != 'Y'){?>disabled="true"<?} if($law_holiday_pay_yn == 'Y'){?>checked<?} ?>><label for="lawHolidayPayY">유급</label>
					<input id="lawHolidayPayN" name="law_holiday_pay_yn" type="radio" class="radio" value="N" <? if($law_holiday_yn != 'Y'){?>disabled="true"<?} if($law_holiday_pay_yn != 'Y'){?>checked<?} ?>><label for="lawHolidayPayN">무급</label>
				</td>
			</tr>
			<tr>
				<th colspan="2">기관약정휴일</th>
				<td colspan="3" class="left"><span class="btn_pack m"><button id="btn_holiday" type="button" onclick="_show_center_holiday();">등록/수정/삭제</button></span></td>
			</tr><?
		}?>
	</tbody>
</table><?
//지시서요청 완료 조회
if ($debug && !$IsCare){
	$sql= 'SELECT count(*)
			 FROM medical_request
			WHERE org_no = \''.$_SESSION['userCenterCode'].'\'
			  AND complete_yn = \'Y\'
			  AND cancel_yn = \'N\'
			  AND del_flag = \'N\'';
	$rqCnt = $conn -> get_data($sql);

	//if($kupyeo3 && $rqCnt==0){
	if($rqCnt==0){ ?>
		<div style="position:absolute; top:322px; left:854px; cursor:pointer; width:100px;" onclick="_nursingPop();">
			<img src="../popup/nursing_request/img/btn_medical.png" alt="방문간호지시서 의료기관신청" title="방문간호지시서 의료기관신청">
		</div><?
	}
}
?>
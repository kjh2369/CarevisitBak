<?
	switch($voucher_id){
		case 0:
			$voucher_title = '일<br>반<br>수<br>급<br>자<br>케<br>어';
			break;
		case 1:
			$voucher_title = '가<br>사<br>간<br>병';
			break;
		case 2:
			$voucher_title = '노<br>인<br>돌<br>봄';
			break;
		case 3:
			$voucher_title = '산<br>모<br>신<br>생<br>아';
			break;
		case 4:
			$voucher_title = '장<br>애<br>인<br>보<br>조';
			break;
	}
?>
<table class="my_table" style="width:100%">
	<colgroup>
		<col width="25px">
		<col width="82px">
		<col span="4">
	</colgroup>
	<tbody>
		<tr>
			<th class="head bottom" rowspan="6"><?=$voucher_title;?></th>
			<th>급여산정방식</th>
			<td class="last" colspan="4">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGupyeoKind<?=$voucher_id;?>" type="radio" class="radio" value="0" onclick="set_pay_obj(<?=$voucher_id;?>);" <? if($pay_type[$voucher_id] == '0'){echo "checked";}?>><span style="margin-left:-5px;">무</span>
					<input name="yGupyeoKind<?=$voucher_id;?>" type="radio" class="radio" value="1Y" onclick="set_pay_obj(<?=$voucher_id;?>);" <? if($pay_type[$voucher_id] == '1'){echo "checked";}?>><span style="margin-left:-5px;">고정시급</span>
					<input name="yGupyeoKind<?=$voucher_id;?>" type="radio" class="radio" value="1N" onclick="set_pay_obj(<?=$voucher_id;?>);" <? if($pay_type[$voucher_id] == '2'){echo "checked";}?>><span style="margin-left:-5px;">변동시급</span>
					<input name="yGupyeoKind<?=$voucher_id;?>" type="radio" class="radio" value="4" onclick="set_pay_obj(<?=$voucher_id;?>);" <? if($pay_type[$voucher_id] == '4'){echo "checked";}?>><span style="margin-left:-5px;">총액비율</span>
					<input name="yGupyeoKind<?=$voucher_id;?>" type="radio" class="radio" value="3" onclick="set_pay_obj(<?=$voucher_id;?>);" <? if($pay_type[$voucher_id] == '3'){echo "checked";}?>><span style="margin-left:-5px;">월급</span><?
				}else{
					switch($pay_type[$voucher_id]){
						case '0': $pay_type_str = '무'; break;
						case '1': $pay_type_str = '고정시급'; break;
						case '2': $pay_type_str = '변동시급'; break;
						case '3': $pay_type_str = '월급'; break;
						case '4': $pay_type_str = '총액비율'; break;
					}?>
					<div class="left"><?=$pay_type_str;?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>고정시급</th>
			<td class="last" colspan="4">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup1<?=$voucher_id;?>" type="text" value="<?=number_format($hourly_1[$voucher_id]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정시급을 입력하여 주십시오."><?
				}else{?>
					<div class="left"><?=number_format($hourly_1[$voucher_id]);?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>기본급</th>
			<td class="last" colspan="4">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup3<?=$voucher_id;?>" type="text" value="<?=number_format($hourly_3[$voucher_id]);?>" maxlength="8" class="number" style="margin-right:0;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="기본급을 입력하여 주십시오.">
					<!--input name="yGibonKupCom<?=$voucher_id;?>" type="checkbox" class="checkbox" value="Y" style="margin-right:0;" <? if($pay_com_type[$voucher_id] == 'Y'){?>checked<?} ?>>포괄임금제--><?
				}else{?>
					<div class="left"><?=number_format($hourly_3[$voucher_id]).($pay_com_type == 'Y' ? ' (포괄임금제)' : '');?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th rowspan="2">변동시급</th>
			<th>1등급&nbsp;</th>
			<td>
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup<?=$voucher_id;?>[]" type="text" value="<?=number_format($hourly_2[$voucher_id][1]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오.">
					<input name="yGibonKupCode<?=$voucher_id;?>[]" type="hidden" value="1"><?
				}else{?>
					<div class="left"><?=number_format($hourly_2[$voucher_id][1]);?></div><?
				}
			?>
			</td>
			<th>2등급&nbsp;</th>
			<td class="last">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup<?=$voucher_id;?>[]" type="text" value="<?=number_format($hourly_2[$voucher_id][2]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오.">
					<input name="yGibonKupCode<?=$voucher_id;?>[]" type="hidden" value="1"><?
				}else{?>
					<div class="left"><?=number_format($hourly_2[$voucher_id][2]);?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>3등급</th>
			<td>
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup<?=$voucher_id;?>[]" type="text" value="<?=number_format($hourly_2[$voucher_id][3]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오.">
					<input name="yGibonKupCode<?=$voucher_id;?>[]" type="hidden" value="1"><?
				}else{?>
					<div class="left"><?=number_format($hourly_2[$voucher_id][3]);?></div><?
				}
			?>
			</td>
			<th>일반</th>
			<td class="last">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yGibonKup<?=$voucher_id;?>[]" type="text" value="<?=number_format($hourly_2[$voucher_id][9]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오.">
					<input name="yGibonKupCode<?=$voucher_id;?>[]" type="hidden" value="1"><?
				}else{?>
					<div class="left"><?=number_format($hourly_2[$voucher_id][9]);?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th class="bottom">수가총액비율</th>
			<td class="bottom last" colspan="4">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="ySugaYoyul<?=$voucher_id;?>" type="text" value="<?=$hourly_4[$voucher_id];?>" maxlength="4" class="no_string" style="text-align:right;" onKeyDown="__onlyNumber(this,'.');" onBlur="if(this.value == ''){this.value = '0';}" tag="수가총액비율을 입력하여 주십시오.">%<?
				}else{?>
					<div class="left"><?=number_format($hourly_4[$voucher_id]);?>%</div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
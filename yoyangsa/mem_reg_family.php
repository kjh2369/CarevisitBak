<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="25px">
		<col width="82px">
		<col span="4">
	</colgroup>
	<tbody>
		<tr>
			<th class="head bottom" rowspan="4">동<br>거<br>가<br>족<br>케<br>어</th>
			<th>급여산정방식</th>
			<td class="last" colspan="3">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yFamCareType" type="radio" class="radio" value="0" onclick="set_family_obj();" <? if($famcare_type == "0"){echo "checked";}?>><span style="margin-left:-5px;">무</span>
					<input name="yFamCareType" type="radio" class="radio" value="Y" onclick="set_family_obj();" <? if($famcare_type == "1"){echo "checked";}?>><span style="margin-left:-5px;">고정시급</span>
					<input name="yFamCareType" type="radio" class="radio" value="2Y" onclick="set_family_obj();" <? if($famcare_type == "2"){echo "checked";}?>><span style="margin-left:-5px;">수가총액비율</span>
					<input name="yFamCareType" type="radio" class="radio" value="3Y" onclick="set_family_obj();" <? if($famcare_type == "3"){echo "checked";}?>><span style="margin-left:-5px;">고정급</span><?
				}else{
					switch($famcare_type){
						case '0': $fam_type_str = '무'; break;
						case '1': $fam_type_str = '고정시급'; break;
						case '2': $fam_type_str = '수가총액비율'; break;
						case '3': $fam_type_str = '고정급'; break;
					}?>
					<div class="left"><?=$fam_type_str;?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th id="tbody_5_1">고정시급</th>
			<td id="tbody_5_2" class="last" colspan="3">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yFamCarePay1" type="text" value="<?=number_format($famcare_pay1);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정시급을 입력하여 주십시오."><?
				}else{?>
					<div class="left"><?=number_format($famcare_pay1);?></div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th id="tbody_6_1">수가총액비율</th>
			<td id="tbody_6_2" class="last" colspan="3">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yFamCarePay2" type="text" value="<?=number_format($famcare_pay2, $famcare_type == '2' ? 1 : 0)?>" maxlength="4" class="no_string" style="text-align:right;" onKeyDown="__onlyNumber(this,'.');" onBlur="if(this.value==''){this.value='0';}" tag="수가총액비율을 입력하여 주십시오.">%<?
				}else{?>
					<div class="left"><?=number_format($famcare_pay2, $famcare_type == '2' ? 1 : 0)?>%</div><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th id="tbody_7_1" class="bottom">고정급</th>
			<td id="tbody_7_2" class="bottom last" colspan="3">
			<?
				if ($mode == 1 || $mode == 2){?>
					<input name="yFamCarePay3" type="text" value="<?=number_format($famcare_pay3);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정급을 입력하여 주십시오."><?
				}else{?>
					<div class="left"><?=number_format($famcare_pay3);?></div><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
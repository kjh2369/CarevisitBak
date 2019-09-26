<?
	/**************************************************

		산모신생아 비급여 항목

	**************************************************/
	$sql = 'select school_not_cnt
			,      school_not_pay
			,      school_cnt
			,      school_pay
			,      family_cnt
			,      family_pay
			,      home_in_yn
			,      home_in_pay
			,      holiday_pay
			  from client_svc_addpay
			 where org_no   = \''.$code.'\'
			   and svc_kind = \''.$__CURRENT_SVC_CD__.'\'
			   and svc_ssn  = \''.$jumin.'\'
			   and del_flag = \'N\'';

	$addpay = $conn->get_array($sql);

	if ($__CURRENT_SVC_ID__ > 20 && $__CURRENT_SVC_ID__ < 30){
		$class = '';
	}else{
		$class = '';
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="<?=$class;?>" rowspan="5">추가요금항목</th>
			<th>미취 아동수</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="notSchoolCnt_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_not_school_cnt" type="text" value="<?=number_format($addpay['school_not_cnt']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['school_not_cnt']);?></div><?
				}?>
			</td>
			<th>추가요금단가</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="notSchoolPay_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_not_school_pay" type="text" value="<?=number_format($addpay['school_not_pay']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['school_not_pay']);?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th>취학 아동수</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="schoolCnt_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_school_cnt" type="text" value="<?=number_format($addpay['school_cnt']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['school_cnt']);?></div><?
				}?>
			</td>
			<th>추가요금단가</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="schoolPay_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_school_pay" type="text" value="<?=number_format($addpay['school_pay']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['school_pay']);?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th>동거 가족수</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="familyCnt_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_family_cnt" type="text" value="<?=number_format($addpay['family_cnt']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['family_cnt']);?></div><?
				}?>
			</td>
			<th>추가요금단가</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="familyPay_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_family_pay" type="text" value="<?=number_format($addpay['family_pay']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['family_pay']);?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="<?=$class;?>" rowspan="2">입주여부</th>
			<td class="<?=$class;?>" rowspan="2"><?
				if ($view_type != 'read'){?>
					<input id="homeInY_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_home_in_yn" type="radio" value="Y" class="radio clsObjData" onclick="set_home_in_pay('<?=$__CURRENT_SVC_ID__;?>');" <? if($addpay['home_in_yn'] == 'Y'){echo 'checked';} ?>><label for="homeInY_<?=$__CURRENT_SVC_CD__;?>">예</label><br>
					<input id="homeInN_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_home_in_yn" type="radio" value="N" class="radio clsObjData" onclick="set_home_in_pay('<?=$__CURRENT_SVC_ID__;?>');" <? if($addpay['home_in_yn'] != 'Y'){echo 'checked';} ?>><label for="homeInN_<?=$__CURRENT_SVC_CD__;?>">아니오</label><?
				}else{?>
					<div class="left"><?=$addpay['home_in_yn'] == 'Y' ? '예' : '아니오';?></div><?
				}?>
			</td>
			<th>입주추가단가</th>
			<td><?
				if ($view_type != 'read'){?>
					<input id="homeInPay_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_home_in_pay" type="text" value="<?=number_format($addpay['home_in_pay']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['home_in_pay']);?></div><?
				}?>
			</td>
		</tr>
		<tr>
			<th class="<?=$class;?>">공/휴일 단가</th>
			<td class="<?=$class;?>"><?
				if ($view_type != 'read'){?>
					<input id="holidayPay_<?=$__CURRENT_SVC_CD__;?>" name="<?=$__CURRENT_SVC_ID__;?>_holiday_pay" type="text" value="<?=number_format($addpay['holiday_pay']);?>" class="number clsObjData" style="width:100%;"><?
				}else{?>
					<div class="left"><?=number_format($addpay['holiday_pay']);?></div><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<?
	unset($addpay);
?>
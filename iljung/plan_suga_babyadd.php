<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$svcCd = $_POST['svcCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

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
			   and svc_kind = \''.$svcCd.'\'
			   and svc_ssn  = \''.$jumin.'\'
			   and del_flag = \'N\'
			 order by svc_seq desc
			 limit 1';

	$row = $conn->get_array($sql);?>
	<table class="my_table my_border_blue" style="width:auto; background-color:#ffffff;">
		<colgroup>
			<col width="60px" span="3">
			<col width="60px" span="3">
			<col width="60px" span="3">
			<col width="60px" span="2">
			<col width="60px">
			<col width="60px">
		</colgroup>
		<thead>
			<tr>
				<th class="head last" colspan="13">
					<div style="float:right; width:auto; margin-right:5px;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfBabyAddHide();"></div>
					<div class="bold" style="float:center; width:auto;">산모신생아 추가 요금 등록</div>
				</th>
			</tr>
			<tr>
				<th class="head" colspan="3">미취학아동</th>
				<th class="head" colspan="3">취학아동</th>
				<th class="head" colspan="3">동거가족</th>
				<th class="head" colspan="2">입주</th>
				<th class="head" rowspan="2">공/휴일<br>추가요금</th>
				<th class="head" rowspan="2">합계</th>
			</tr>
			<tr>
				<th class="head">아동수</th>
				<th class="head">단가</th>
				<th class="head">추가요금</th>
				<th class="head">아동수</th>
				<th class="head">단가</th>
				<th class="head">추가요금</th>
				<th class="head">가족수</th>
				<th class="head">단가</th>
				<th class="head">추가요금</th>
				<th class="head">입주여부</th>
				<th class="head">추가요금</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center"><input id="txtNotSchoolCnt" name="txtNotSchool" type="text" value="<?=number_format($row['school_not_cnt']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtNotSchoolCost" name="txtNotSchool" type="text" value="<?=number_format($row['school_not_pay']);?>" class="number" style="width:100%;"></td>
				<td class="center"><div id="txtNotSchoolAmt" class="right" value="0">0</div></td>
				<td class="center"><input id="txtSchoolCnt" name="txtSchool" type="text" value="<?=number_format($row['school_cnt']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtSchoolCost" name="txtSchool" type="text" value="<?=number_format($row['school_pay']);?>" class="number" style="width:100%;"></td>
				<td class="center"><div id="txtSchoolAmt" class="right" value="0">0</div></td>
				<td class="center"><input id="txtFamilyCnt" name="txtFamily" type="text" value="<?=number_format($row['family_cnt']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtFamilyCost" name="txtFamily" type="text" value="<?=number_format($row['family_pay']);?>" class="number" style="width:100%;"></td>
				<td class="center"><div id="txtFamilyAmt" class="right" value="0">0</div></td>
				<td class="center"><input id="ynInHome" name="ynBabyAdd" type="checkbox" value="Y" class="checkbox" <? if($row['home_in_yn'] == 'Y'){?>checked<?} ?>></td>
				<td class="center"><input id="txtInHomeCost" name="txtBabyAddCost" type="text" value="<?=number_format($row['home_in_pay']);?>" class="number" style="width:100%;"></td>
				<td class="center"><input id="txtHolidayCost" name="txtBabyAddCost" type="text" value="<?=number_format($row['holiday_pay']);?>" class="number" style="width:100%;"></td>
				<td class="center"><div id="txtAddTotAmt" class="right" value="0">0</div></td>
			</tr>
		</tbody>
	</table><?
	unset($row);
	include_once('../inc/_db_close.php');?>
	<script type="text/javascript">
	$(document).ready(function(){
		//미취학아동 추가요금
		$('input:text[name="txtNotSchool"]').unbind('change').change(function(){
			var liCnt  = __str2num($('#txtNotSchoolCnt').val());
			var liCost = __str2num($('#txtNotSchoolCost').val());
			var liAmt  = liCnt * liCost;

			$('#txtNotSchoolAmt').attr('value',liAmt).text(__num2str(liAmt));

			lfGetBabyAddTot();
		});

		//취학아동 추가요금
		$('input:text[name="txtSchool"]').unbind('change').change(function(){
			var liCnt  = __str2num($('#txtSchoolCnt').val());
			var liCost = __str2num($('#txtSchoolCost').val());
			var liAmt  = liCnt * liCost;

			$('#txtSchoolAmt').attr('value',liAmt).text(__num2str(liAmt));

			lfGetBabyAddTot();
		});

		//동거가족 추가요금
		$('input:text[name="txtFamily"]').unbind('change').change(function(){
			var liCnt  = __str2num($('#txtFamilyCnt').val());
			var liCost = __str2num($('#txtFamilyCost').val());
			var liAmt  = liCnt * liCost;

			$('#txtFamilyAmt').attr('value',liAmt).text(__num2str(liAmt));

			lfGetBabyAddTot();
		});

		//입주 추가 요금
		$('input:text[name="txtInHomeCost"]').unbind('change').change(function(){
			lfGetBabyAddTot();
		});
		$('#ynInHome').unbind('click').click(function(){
			lfSetInHome();
			lfGetBabyAddTot();
		});

		//공/휴일 추가요금
		$('input:text[name="txtHolidayCost"]').unbind('change').change(function(){
			lfGetBabyAddTot();
		});

		$('#txtNotSchoolCnt').change();
		$('#txtSchoolCnt').change();
		$('#txtFamilyCnt').change();

		lfSetInHome();
		lfGetBabyAddTot();
	});

	function lfSetInHome(){
		if ($('#ynInHome').attr('checked')){
			var lbDisabled = false;
			var lsColor = '#ffffff';
		}else{
			var lbDisabled = true;
			var lsColor = '#efefef';
		}

		$('#txtInHomeCost').attr('disabled',lbDisabled).css('background-color',lsColor);
	}

	function lfGetBabyAddTot(){
		var liAmt1 = __str2num($('#txtNotSchoolAmt').attr('value'));
		var liAmt2 = __str2num($('#txtSchoolAmt').attr('value'));
		var liAmt3 = __str2num($('#txtFamilyAmt').attr('value'));
		var liAmt4 = $('#ynInHome').attr('checked') ? __str2num($('#txtInHomeCost').val()) : 0;
		var liAmt5 = __str2num($('#txtHolidayCost').val());

		var liAmtTot = liAmt1 + liAmt2 + liAmt3 + liAmt4 + liAmt5;

		$('#txtAddTotAmt').attr('value',liAmtTot).text(__num2str(liAmtTot));
	}
	</script>
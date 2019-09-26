<?
	include("../inc/_header.php");

	$mCode    = $_POST['mCode'];
	$mKind    = $_POST['mKind'];
	$mSugup   = Trim($_POST['mSugup']);
	$mKey     = Trim($_POST['mKey']);
	$mSvcCode = $_POST['mSvcCode'];
	$mYear    = $_POST['mYear'];
	$mMonth   = $_POST['mMonth'];
	$mRate    = $_POST['mRate'];

	if ($mSugup == '' and $mKey != ''){
		$sql = "select m03_jumin"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_key   = '".$mKey
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$mSugup = $row[0];
		$conn->row_free();
	}

	$setYear = $conn->get_iljung_year($mCode);
	
	if ($mYear == ''){
		$mYear = date('Y', mkTime());
	}

	if ($mMonth == ''){
		$mMonth = date('m', mkTime());
	}

	$sql = "select count(*)"
		 . "  from t13sugupja"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_pay_date = '".$mYear.$mMonth
		 . "'";
	$confFlag = $conn->get_data($sql);
?>
<form name="f" method="post" action="sugupconf_save_ok.php">
<table style="width:100%;">
<tr>
<td class="noborder" style="width:55%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
	<input name="mCode" type="hidden" value="<?=$mCode;?>">
	<select name="mKind" style="width:150px;">
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mYear" tag="<?=$mYear;?>" style="width:65px;">
	<?
		for($i=$setYear[0]; $i<=$setYear[1]; $i++){
		?>
			<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
		<?
		}
	?>
	</select>
	<select name="mMonth" tag="<?=$mMonth;?>" style="width:55px;">
	<option value="01"<? if($mMonth == "01"){echo "selected";}?>>1월</option>
	<option value="02"<? if($mMonth == "02"){echo "selected";}?>>2월</option>
	<option value="03"<? if($mMonth == "03"){echo "selected";}?>>3월</option>
	<option value="04"<? if($mMonth == "04"){echo "selected";}?>>4월</option>
	<option value="05"<? if($mMonth == "05"){echo "selected";}?>>5월</option>
	<option value="06"<? if($mMonth == "06"){echo "selected";}?>>6월</option>
	<option value="07"<? if($mMonth == "07"){echo "selected";}?>>7월</option>
	<option value="08"<? if($mMonth == "08"){echo "selected";}?>>8월</option>
	<option value="09"<? if($mMonth == "09"){echo "selected";}?>>9월</option>
	<option value="10"<? if($mMonth == "10"){echo "selected";}?>>10월</option>
	<option value="11"<? if($mMonth == "11"){echo "selected";}?>>11월</option>
	<option value="12"<? if($mMonth == "12"){echo "selected";}?>>12월</option>
	</select>
	<select name="mRate" style="width:150px;">
	<option value="">전체</option>
	<?
		$sql = "select m92_code"
			 . ",      m92_bonin_yul"
			 . ",      m92_cont"
			 . "  from m92boninyul"
			 . " where date_format(now(), '%Y%m%d') between m92_sdate and m92_edate"
			 . " order by m92_code";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			?>
			<option value="<?=$row['m92_code'];?>" <? if ($mRate == $row['m92_code']){echo 'selected';} ?>><?=$row['m92_cont'];?>(<?=$row['m92_bonin_yul'];?>)</option>
			<?
		}

		$conn->row_free();
	?>
	</select>
	<input type="button" onClick="getSugupConfList(document.f.mCode.value, document.f.mKind.value, document.f.mYear.value, document.f.mMonth.value, document.f.mRate.value);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;">
</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<th style="width:5%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">No</th>
<th style="width:7%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">수급자</th>
<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">등급</th>
<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">부담율</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">급여한도액</th>
<th style="width:8%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
<th style="width:9%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">급여총액</th>
<th style="width:30%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">본인부담액</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">공단청구액</th>
<th style="width:9%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">수급시간<br>확정조회</th>
</tr>
<tr style="height:24px;">
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">초과+비급여</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">순수</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">계</th>
</tr>
<tbody id="listTotal">
<tr>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center;"></td>
<td style="text-align:center; font-weight:bold;">계</td>
<td style="text-align:center; font-weight:bold;" id="amtTotalPay">0</td>
<td style="text-align:center; font-weight:bold;" id="amtBoninPay1">0</td>
<td style="text-align:center; font-weight:bold;" id="amtBoninPay2">0</td>
<td style="text-align:center; font-weight:bold;" id="amtBoninPay3">0</td>
<td style="text-align:center; font-weight:bold;" id="amtCenterPay">0</td>
<td style="text-align:center;">
<?
	if ($confFlag == 0){
	?>
		<input type="button" onClick="sugupConfOk();" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn_conf_all_2.png') no-repeat; cursor:pointer;">
	<?
	}else{
	?>
		<input type="button" onClick="sugupConfCancel();" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn_conf_cancel.png') no-repeat; cursor:pointer;">
	<?
	}
?>
</td>
</tr>
</tbody>
<tbody id="listDetail">
<?
	$sql = "select t01_svc_subcode"
		 . ",      m03_name"
		 . ",      LVL.m81_name as lvlName"
		 . ",      m03_skind"
		 . ",      m03_bonin_yul"
		 . ",      m03_kupyeo_max"
		 . ",      m03_key"
		 . ",      m03_jumin"
		 . ",      t01_svc_subcode"
		 . ",      SUB.m81_name as subName"
		 #. ",      sum(case when t01_conf_soyotime >= t01_sugup_soyotime then t01_conf_suga_value else 0 end) as totalPay"
		 #. ",      sum(case when t01_conf_soyotime >= t01_sugup_soyotime and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as boninPay1"
		 #. ",      sum(case when t01_conf_soyotime >= t01_sugup_soyotime and t01_bipay_umu  = 'Y' then t01_conf_suga_value else 0 end) as biPay"
		 . ",      sum(case when t01_conf_soyotime >= 30 then t01_conf_suga_value else 0 end) as totalPay"
		 . ",      sum(case when t01_conf_soyotime >= 30 and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as boninPay1"
		 . ",      sum(case when t01_conf_soyotime >= 30 and t01_bipay_umu  = 'Y' then t01_conf_suga_value else 0 end) as biPay"
		 . "  from t01iljung"
		 . " inner join ("
		 . "       select m03_ccode as m03_ccode"
		 . "       ,      m03_mkind as m03_mkind"
		 . "       ,      m03_jumin as m03_jumin"
		 . "       ,      m03_name as m03_name"
		 . "       ,      m03_ylvl as m03_ylvl"
		 . "       ,      m03_skind as m03_skind"
		 . "       ,      m03_bonin_yul as m03_bonin_yul"
		 . "       ,      m03_kupyeo_max as m03_kupyeo_max"
		 . "       ,      m03_sdate as m03_sdate"
		 . "       ,      m03_edate as m03_edate"
		 . "       ,      m03_key   as m03_key"
		 . "         from m03sugupja"
		 . "        where m03_ccode = '".$mCode
		 . "'         and m03_mkind = '".$mKind
		 . "'       union all"
		 . "       select m31_ccode as m03_ccode"
		 . "       ,      m31_mkind as m03_mkind"
		 . "       ,      m31_jumin as m03_jumin"
		 . "       ,      m03_name as m03_name"
		 . "       ,      m31_level as m03_ylvl"
		 . "       ,      m31_kind as m03_skind"
		 . "       ,      m31_bonin_yul as m03_bonin_yul"
		 . "       ,      m31_kupyeo_max as m03_kupyeo_max"
		 . "       ,      m31_sdate as m03_sdate"
		 . "       ,      m31_edate as m03_edate"
		 . "       ,      m03_key   as m03_key"
		 . "         from m31sugupja"
		 . "        inner join m03sugupja"
		 . "           on m03_ccode = m31_ccode"
		 . "          and m03_mkind = m31_mkind"
		 . "          and m03_jumin = m31_jumin"
		 . "        where m31_ccode = '".$mCode
		 . "'         and m31_mkind = '".$mKind
		 . "'       ) as sugupja"
		 . "    on t01_ccode = m03_ccode"
		 . "   and t01_mkind = m03_mkind"
		 . "   and t01_jumin = m03_jumin"
		 . "   and t01_conf_date between m03_sdate and m03_edate"
		 . " inner join m81gubun as LVL"
		 . "    on LVL.m81_gbn  = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " inner join m81gubun as SUB"
		 . "    on SUB.m81_gbn  = 'SUB'"
		 . "   and SUB.m81_code = t01_svc_subcode"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
		 . "'  and ifnull(t01_conf_date,   '') != ''"
		 . "   and ifnull(t01_conf_fmtime, '') != ''"
		 . "   and ifnull(t01_conf_totime, '') != ''";

	if ($mRate != ''){
		$sql .= " and m03_skind = '".$mRate."'";
	}
   
	$sql .= " group by t01_svc_subcode"
		 .  ",         m03_key"
		 .  ",         m03_jumin"
		 .  ",         m03_name"
		 .  ",         LVL.m81_name"
		 .  ",         SUB.m81_name"
		 .  ",         m03_bonin_yul"
		 .  ",         m03_skind"
		 .  ",         m03_kupyeo_max"
		 .  ",         t01_svc_subcode"
		 .  " order by m03_name"
		 .  ",         m03_skind"
		 .  ",         t01_svc_subcode";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	
	$seq = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$boninPay1 = $row['boninPay1'] - $row['totalPay'];
		$overPay   = ($boninPay1 > 0 ? $boninPay1 : 0); //초과액
		$biPay     = $row['biPay']; //비급여
		$boninPay1 = $overPay + $biPay;
		$boninPay2 = $row['boninPay1'] * ($row['m03_bonin_yul'] / 100); //순수본인부담액
		$boninPay3 = $boninPay1 + $boninPay2;
		$centerPay = $row['totalPay'] - $boninPay3;

		$gabPay = $centerPay - cutOff($centerPay);
		$boninPay2 += ($centerPay - cutOff($centerPay));
		$boninPay3  = $boninPay1 + $boninPay2;
		$centerPay  = cutOff($centerPay);

		if ($tempText != $row['m03_name'].$row['lvlName'].$row['m03_bonin_yul'].$row['m03_kupyeo_max']){
			$tempText  = $row['m03_name'].$row['lvlName'].$row['m03_bonin_yul'].$row['m03_kupyeo_max'];
			$seq ++;
			$newSeq = $seq;
			$newName  = $row['m03_name'];
			$newLevel = $row['lvlName'];
			$newBoninYul = $row['m03_bonin_yul'].'%';
			$newMaxPay   = number_format($row['m03_kupyeo_max']);
			$borderTop = 'border-top:1px solid #cccccc';
			$borderTop2 = 'border-top:1px solid #cccccc';
		}else{
			$newSeq = '';
			$newName  = '';
			$newLevel = '';
			$newBoninYul = '';
			$newMaxPay   = '';
			$borderTop = 'border-top:1px solid #ffffff;';
			$borderTop2 = 'border-top:1px solid #e5e5e5';
		}
		?>
		<tr>
		<td style="text-align:center; <?=$borderTop;?>"><?=$newSeq;?></td>
		<td style="text-align:center; <?=$borderTop;?>"><?=$newName;?></td>
		<td style="text-align:center; <?=$borderTop;?>"><?=$newLevel;?></td>
		<td style="text-align:center; <?=$borderTop;?>"><?=$newBoninYul;?></td>
		<td style="text-align:center; <?=$borderTop;?>"><?=$newMaxPay;?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=$row['subName'];?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=number_format($row['totalPay']);?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=number_format($boninPay1);?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=number_format($boninPay2);?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=number_format($boninPay3);?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><?=number_format($centerPay);?></td>
		<td style="text-align:center; <?=$borderTop2;?>"><input type="button" onClick="popupSugupTimeList('<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear;?>', '<?=$mMonth;?>', '<?=$row['t01_svc_subcode'];?>', '<?=$row['m03_key'];?>');" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn12.gif') no-repeat; cursor:pointer;"></td>
		</tr>
		<input name="subCode[]"   type="hidden" value="<?=$row['t01_svc_subcode'];?>">
		<input name="jumin[]"     type="hidden" value="<?=$row['m03_jumin'];?>">
		<input name="boninYul[]"  type="hidden" value="<?=$row['m03_skind'];?>">
		<input name="maxPay[]"    type="hidden" value="<?=$row['m03_kupyeo_max'];?>">
		<input name="totalPay[]"  type="hidden" value="<?=$row['totalPay'];?>">
		<input name="overPay[]"   type="hidden" value="<?=$overPay;?>">
		<input name="biPay[]"     type="hidden" value="<?=$biPay;?>">
		<input name="boninPay1[]" type="hidden" value="<?=$boninPay1;?>">
		<input name="boninPay2[]" type="hidden" value="<?=$boninPay2;?>">
		<input name="boninPay3[]" type="hidden" value="<?=$boninPay3;?>">
		<input name="centerPay[]" type="hidden" value="<?=$centerPay;?>">
		<?
	}

	$conn->row_free();
?>
</tbody>
</table>
</form>
<?
	include("../inc/_footer.php");
?>
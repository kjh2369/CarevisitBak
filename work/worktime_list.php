<?
	include("../inc/_header.php");

	$mCode    = $_POST['mCode'];
	$mKind    = $_POST['mKind'];
	$mSvcCode = $_POST['mSvcCode'];
	$mYoy     = $_POST['mYoy'];
	$mYear    = $_POST['mYear'];
	$mMonth   = $_POST['mMonth'];
	$mPage    = $_POST['mPage'];

	$setYear = $conn->get_iljung_year($mCode);
	
	if ($mYear == ''){
		$mYear = date('Y', mkTime());
	}

	if ($mMonth == ''){
		$mMonth = date('m', mkTime());
	}
?>
<form name="f" method="post">
<table style="width:100%;">
<tr>
<td class="noborder" style="width:62%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
	<input name="mCode" type="hidden" value="<?=$mCode;?>">
	<select name="mKind" style="width:150px;" onChange="setYoyList('<?=$mCode;?>', 'mYoy');">
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mSvcCode" style="width:50px;" onChange="setYoyList('<?=$mCode;?>', 'mYoy');">
	<option value="200" <? if($mSvcCode == '200'){echo 'selected';} ?>>요양</option>
	<option value="500" <? if($mSvcCode == '500'){echo 'selected';} ?>>목욕</option>
	<option value="800" <? if($mSvcCode == '800'){echo 'selected';} ?>>간호</option>
	</select>
	<select name="mYoy">
	<option value="">-요양사선택-</option>
	<?
		$sql = "select distinct"
			 . "       m02_yjumin"
			 . ",      m02_yname"
			 . "  from t01iljung"
			 . " inner join m02yoyangsa"
			 . "    on t01_ccode = m02_ccode"
			 . "   and t01_mkind = m02_mkind"
			 . "   and t01_yoyangsa_id1 = m02_yjumin"
			 . " where t01_ccode = '".$mCode
			 . "'  and t01_mkind = '".$mKind
			 . "'  and t01_svc_subcode = '".$mSvcCode
			 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
			 . "'"
			 . " order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			if ($mYoy == $row['m02_yjumin']){
				echo '<option value="'.$row['m02_yjumin'].'" selected>'.$row['m02_yname'].'</option>';
			}else{
				echo '<option value="'.$row['m02_yjumin'].'">'.$row['m02_yname'].'</option>';
			}
		}
	?>
	</select>
	<select name="mYear" style="width:65px;">
	<?
		for($i=$setYear[0]; $i<=$setYear[1]; $i++){
		?>
			<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
		<?
		}
	?>
	</select>
	<select name="mMonth" style="width:55px;">
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
	<input name="btnSearch" type="button" onClick="getWorkTimetList(document.f.mCode.value, document.f.mKind.value, document.f.mSvcCode.value, document.f.mYoy.value,document.f.mYear.value,document.f.mMonth.value);" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;">
</td>
<td class="noborder" style="width:38%; text-align:right; vertical-align:bottom; padding-bottom:1px;">
	<select name="mSudangType" style="width:70px;" onKeyDown="__enterFocus();">
	<?
		$sql = $conn->get_gubun('GYT');
		$conn->query($sql);
		$row2 = $conn->fetch();
		$row_count = $conn->row_count();
		
		for($i=0; $i<$row_count; $i++){
			$row2 = $conn->select_row($i);
		?>
			<option value="<?=$row2[0];?>"<? if($row["m02_ygupyeo_kind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
		<?
		}

		$conn->row_free();
	?>
	</select>
	<input name="mTimePrice" type="text" value="0" title="시급" class="number" style="width:50px; height:21px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);">
	<input name="mSudangRate" type="text" value="0" title="수가요율" class="number" style="width:50px; height:21px;" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);">
	<input name="btnExec" type="button" onClick="alert('재계산처리');" value="재계산" class="btn">
	<input name="btnConf" type="button" onClick="alert('확정');" value="확정" class="btn">
</td>
</tr>
</table>
<table class="view_type1" style="width:100%;">
<tr style="height:24px;">
<th style="width:5%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">No</th>
<th style="width:7%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">수급자<br>성명</th>
<th style="width:17%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">계획</th>
<th style="width:19%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">실적</th>
<th style="width:13%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" >서비스명</th>
<th style="width:30%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="3">근무수당 산정방식</th>
<th style="width:9%; padding:0px; text-align:center;" rowspan="2">비고</th>
</tr>
<tr>
<th style="width:7%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">계획일자</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">계획시간</th>
<th style="width:7%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">실시일자</th>
<th style="width:12%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">실시시간</th>
<th style="width:13%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수가단가</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">서비스건별</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">단순시급</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">수가총액비율</th>
</tr>
<tbody id="listTotal">
	<tr>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold; line-height:1.2em;" id="planTime"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold; line-height:1.2em;" id="realTime"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold; line-height:1.2em;" id="totalSuga"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"><input name="btnSave[]" type="button" value="" onClick="saveSugupConfirm('all');" style="width:59px; height:21px; border:0px; background:url('../image/btn10.gif') no-repeat; cursor:pointer;"></td>
	</tr>
</tbody>
<tbody id="listDetail">
<?
	$sql = "select m03_name"
		 . ",      t01_sugup_date"
		 . ",      t01_sugup_fmtime"
		 . ",      t01_sugup_totime"
		 . ",      t01_sugup_soyotime"
		 . ",      t01_sugup_date"
		 . ",      t01_sugup_fmtime"
		 . ",      t01_conf_date"
		 . ",      t01_conf_fmtime"
		 . ",      t01_conf_totime"
		 . ",      t01_conf_date"
		 . ",      ifnull(t01_conf_soyotime, 0) as workProcTime"
		 . ",      t01_sugup_seq"
		 . ",      t01_conf_suga_code"
		 . ",      t01_conf_suga_value"
		 . ",      t01_status_gbn"
		 . ",      t01_sugup_yoil"
		 . ",      holiday_name"
		 . ",      m02_ygupyeo_kind"
		 . ",      m02_ygibonkup"
		 . ",      m02_ysuga_yoyul"
		 . "  from t01iljung"
		 . " inner join m02yoyangsa"
		 . "    on m02_ccode  = t01_ccode"
		 . "   and m02_mkind  = t01_mkind"
		 . "   and m02_yjumin = t01_yoyangsa_id1"	
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . "  left join tbl_holiday"
		 . "    on mdate = t01_sugup_date"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_yoyangsa_id1 = '".$mYoy
		 . "'  and t01_svc_subcode  = '".$mSvcCode
		 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
		 . "'  and t01_del_yn = 'N'"
		 . " order by t01_sugup_date"
		 . ",         t01_sugup_fmtime"
		 . ",         m03_name";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i); 
		$sugaName = $conn->get_suga($mCode, $row['t01_conf_suga_code']);
		$sumPlanTime += $row['t01_sugup_soyotime'];
		$sugupDate = ceil(subStr($row['t01_sugup_date'],4,2)).'/'.ceil(subStr($row['t01_sugup_date'],6,2));

		if ($row['holiday_name'] != ''){
			$sugupDate = '<font color="#ff0000">'.$sugupDate.'</font>';
			$dateTitle = $row['holiday_name'];
		}else{
			if ($row['t01_sugup_yoil'] == '6'){
				$sugupDate = '<font color="#0000ff">'.$sugupDate.'</font>';
				$dateTitle = '토요일';
			}else if ($row['t01_sugup_yoil'] == '7' or $row['t01_sugup_yoil'] == '0'){
				$sugupDate = '<font color="#ff0000">'.$sugupDate.'</font>';
				$dateTitle = '일요일';
			}
		}

		$sugupTime = subStr($row['t01_sugup_fmtime'],0,2).':'
				   . subStr($row['t01_sugup_fmtime'],2,2).'-'
				   . subStr($row['t01_sugup_totime'],0,2).':'
				   . subStr($row['t01_sugup_totime'],2,2).'<br>('
				   . $row['t01_sugup_soyotime'].'분)';

		$confDate = ceil(subStr($row['t01_conf_date'],4,2)).'/'.ceil(subStr($row['t01_conf_date'],6,2));

		if ($confDate == '0/0'){
			$confDate = '';
		}

		if ($row['m02_ygupyeo_kind'] == '1'){
			$servicePrice = number_format($row['m02_ygibonkup'] * ($row['workProcTime'] / 60));
		}else{
			$servicePrice = 0;
		}
		if ($row['m02_ygupyeo_kind'] == '2'){
			$defaultPrice = number_format($row['m02_ygibonkup'] * ($row['workProcTime'] / 60));
		}else{
			$defaultPrice = 0;
		}
		
		$sugaRatePrice = number_format(cutOff(floor($row['t01_conf_suga_value'] * ($row['workProcTime'] / 100))));

		if ($i+1 == $row_count){
			$nextIndex = '';
		}else{
			$nextIndex = 'if(this.value.length > 3){document.getElementsByName(\'workFmTime[]\')['.($i+1).'].focus();}';
		}
		?>
		<tr>
		<td style="text-align:center;"><?=$i+1;?></td>
		<td style="text-align:center;"><?=$row['m03_name'];?></td>
		<td style="text-align:center;" title="<?=$dateTitle;?>"><?=$sugupDate;?></td>
		<td style="text-align:center; line-height:1.3em;"><?=$sugupTime;?></td>
		<td style="text-align:center;"><?=$confDate;?></td>
		<td style="text-align:center;">
		<input name="workFmTime[]" type="text" value="<?=subStr($row['t01_conf_fmtime'],0,2);?>:<?=subStr($row['t01_conf_fmtime'],2,2);?>" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName('workToTime[]')[<?=$i;?>].focus();}" onFocus="__replace(this, ':', '');" onBlur="__styleTime(this);" onChange="setWorkPorcTime(<?=$i;?>); setWorkSudang(<?=$i;?>);">
		<input name="workToTime[]" type="text" value="<?=subStr($row['t01_conf_totime'],0,2);?>:<?=subStr($row['t01_conf_totime'],2,2);?>" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="<?=$nextIndex;?>" onFocus="__replace(this, ':', '');" onBlur="__styleTime(this);" onChange="setWorkPorcTime(<?=$i;?>); setWorkSudang(<?=$i;?>);">
		<?
			if ($mSvcCode == '200'){
			?>
				(<input name="workProcTime[]" type="text" value="<?=$row['workProcTime'];?>" maxlength="4" class="number" style="text-align:center; width:40px; background-color:#eeeeee;" readOnly>분)
			<?
			}else{
			?>
				<input name="workProcTime[]" type="hidden" value="0">
			<?
			}
		?>
		</td>
		<td style="text-align:left; line-height:1.3em;">
			<div style="text-align:left;" id="sugaName[]"><?=$sugaName;?></div>
			<div style="text-align:center;" id="sugaValue[]"><?=number_format($row['t01_conf_suga_value']);?></div>
		</td>
		<td style="text-align:center;" id="servicePrice[]"><?=$servicePrice;?></td>
		<td style="text-align:center;" id="defaultPrice[]"><?=$defaultPrice;?></td>
		<td style="text-align:center;" id="sugaRatePrice[]"><?=$sugaRatePrice;?></td>
		<td style="text-align:center;"><input name="btnSave[]" type="button" value="" onClick="saveSugupConfirm(<?=$i;?>);" style="width:59px; height:21px; border:0px; background:url('../image/btn10.gif') no-repeat; cursor:pointer;"></td>
		</tr>
		<input name="mDate[]" type="hidden" value="<?=$row['t01_sugup_date'];?>">
		<input name="mFmTime[]" type="hidden" value="<?=$row['t01_sugup_fmtime'];?>">
		<input name="mSeq[]" type="hidden" value="<?=$row['t01_sugup_seq'];?>">
		<input name="workDate[]" type="hidden" value="<?=$row['t01_sugup_date'];?>">
		<input name="sugaCode[]" type="hidden" value="<?=$row['t01_conf_suga_code'];?>">
		<input name="planProcTime[]" type="hidden" value="<?=$row['t01_sugup_soyotime'];?>">
		<input name="sugaPrice[]" type="hidden" value="<?=$row['t01_conf_suga_value'];?>">
		
		<input name="payKind[]" type="hidden" value="<?=$row['m02_ygupyeo_kind'];?>">
		<input name="pay[]" type="hidden" value="<?=$row['m02_ygibonkup'];?>">

		<input name="changeFlag[]" type="hidden" value="N">
	<?
	}

	$conn->row_free();
?>
<input name="mKey" type="hidden" value="<?=$mKey;?>">
</tbody>
</table>
</form>
<?
	include("../inc/_footer.php");
?>
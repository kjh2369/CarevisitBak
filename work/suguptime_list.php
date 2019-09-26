<?
	include("../inc/_header.php");

	$con2 = new connection();

	$_PARAM = $_REQUEST;
	
	$mPopup   = $_PARAM['mPopup']; 
	$mCode    = $_PARAM['mCode'];
	$mKind    = $_PARAM['mKind'];
	$mSugup   = Trim($_PARAM['mSugup']);
	$mKey     = Trim($_PARAM['mKey']);
	$mYear    = $_PARAM['mYear'];
	$mMonth   = $_PARAM['mMonth'];
	$mSvcCode = $_PARAM['mSvcCode'];
	$mPage    = $_PARAM['mPage'];

	if ($mSugup == '' and $mKey != ''){
		$sql = "select m03_jumin"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_key   = '".$mKey
			 . "'";
		$mSugup = $conn->get_data($sql);
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
		 . "'  and t13_jumin = '".$mSugup
		 . "'  and t13_pay_date = '".$mYear.$mMonth
		 . "'";
	$confFlag = $conn->get_data($sql);
?>
<form name="f" method="post" action="suguptime_save_ok.php">
<?
	if ($mPopup != 'Y'){
	?>
		<table style="width:100%;">
		<tr>
		<td class="noborder" style="width:55%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
			<input name="mCode" type="hidden" value="<?=$mCode;?>">
			<select name="mKind" style="width:150px;" onChange="setSugupList('<?=$mCode;?>', 'mSugup');">
			<?
				for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
				?>
					<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_PARAM ["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
				<?
				}
			?>
			</select>
			<select name="mYear" style="width:65px;" tag="<?=$mYear;?>" onChange="setSugupList('<?=$mCode;?>', 'mSugup');">
			<?
				for($i=$setYear[0]; $i<=$setYear[1]; $i++){
				?>
					<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
				<?
				}
			?>
			</select>
			<select name="mMonth" style="width:55px;" tag="<?=$mMonth;?>" onChange="setSugupList('<?=$mCode;?>', 'mSugup');">
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
			<select name="mSugup">
			<option value="">-수급자선택-</option>
			<?
				$sql = "select distinct"
					 . "       t01_jumin"
					 . ",      m03_name"
					 . "  from t01iljung"
					 . " inner join m03sugupja"
					 . "    on t01_ccode = m03_ccode"
					 . "   and t01_mkind = m03_mkind"
					 . "   and t01_jumin = m03_jumin"
					 . " where t01_ccode = '".$mCode
					 . "'  and t01_mkind = '".$mKind
					 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
					 . "'"
					 . " order by m03_name";
					 
				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);
					if ($mSugup == $row['t01_jumin'] and $mSugup != ''){
						echo '<option value="'.$row['t01_jumin'].'" selected>'.$row['m03_name'].'</option>';
					}else{
						echo '<option value="'.$row['t01_jumin'].'">'.$row['m03_name'].'</option>';
					}
				}
			?>
			</select>
			<input name="btnSearch" type="button" onClick="getSugupTimetList('<?=$mCode;?>',document.f.mKind.value,document.f.mYear.value,document.f.mMonth.value,document.f.mSugup.value,'<?=$mKey;?>');" value="" style="width:59px; height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;">
		</td>
		</tr>
		</table>
	<?
	}else{
		$sql = "select m03_jumin"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and m03_key   = '".$mKey
			 . "'";
		$mSugup = $conn->get_data($sql); ?>
		<style>
			body{
				margin-left:0px;
				margin-top:0px;
			}
		</style>
		<input name="mCode"  type="hidden" value="<?=$mCode;?>">
		<input name="mKind"  type="hidden" value="<?=$mKind;?>">
		<input name="mYear"  type="hidden" value="<?=$mYear;?>"  tag="<?=$mYear;?>">
		<input name="mMonth" type="hidden" value="<?=$mMonth;?>" tag="<?=$mMonth;?>">
		<input name="mKey"   type="hidden" value="<?=$mKey;?>">
		<input name="mSugup" type="hidden" value="<?=$mSugup;?>">
		<table style="width:100%;">
		<tr>
		<td class="noborder" style="width:100%; text-align:left; padding-left:10px;">
		<?
			for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
				if ($_SESSION["userCenterKind"][$r] == $_PARAM ["mKind"]){
					echo '<span style="font-weight:bold;">[기관명 : '. $_SESSION["userCenterKindName"][$r].']</span>';
					break;
				}				
			}

			$sql = "select m81_name"
			     . "  from m81gubun"
				 . " where m81_gbn  = 'SUB'"
				 . "   and m81_code = '".$mSvcCode
				 . "'";
			$mSvcName = $conn->get_data($sql);
			echo '<span style="padding-left:10px; font-weight:bold;">[서비스 : '.$mSvcName .']</span>';

			echo '<span style="padding-left:10px; font-weight:bold;">[년월 : '.$mYear.'년'.$mMonth.'월]</span>';

			$sql = "select m03_name"
			     . "  from m03sugupja"
				 . " where m03_ccode = '".$mCode
				 . "'  and m03_mkind = '".$mKind
				 . "'  and m03_key   = '".$mKey
				 . "'";
			$sugupjaName = $conn->get_data($sql);

			echo '<span style="padding-left:10px; font-weight:bold;">[수급자명 : '.$sugupjaName.']</span>';
		?>
		</td>
		</tr>
		</table>
	<?
	}
?>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr style="height:24px;">
<th style="width:6%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">No</th>
<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
<th style="width:23%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">계획</th>
<th style="width:25%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">실적</th>
<th style="width:14%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">제공서비스</th>
<th style="width:7%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">실적<br>급여액</th>
<th style="width:7%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">계획<br>급여액</th>
<th style="width:10%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">비고</th>
</tr>
<tr style="height:24px;">
<th style="width:9%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">일자</th>
<th style="width:14%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시간</th>
<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">일자</th>
<th style="width:19%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시간</th>
</tr>
<tbody id="listTotal">
	<tr>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="planTime"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="realTime"></td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="realService"></td>
	<td style="text-align:right; background-color:#f7fbfd; font-weight:bold;" id="totalSuga">0</td>
	<td style="text-align:right; background-color:#f7fbfd; font-weight:bold;" id="totalSugaPlan">0</td>
	<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">
	<!--<input name="btnSaveAll" type="button" value="" onClick="saveSugupConfirm('all');" style="width:59px; height:21px; border:0px; background:url('../image/btn10.gif') no-repeat; cursor:pointer;">-->
	<?
		if ($confFlag == '0'){
			echo '미확정';
		}else{
			echo '확정';
		}
	?>
	</td>
	</tr>
</tbody>
<tbody id="listDetail">
<?
	$sql = "select concat(cast(substring(t01_sugup_date,5,2) as unsigned),'/',cast(substring(t01_sugup_date,7,2) as unsigned)) as sugupDate"
		 . ",      case t01_sugup_yoil when '1' then '(월)'"
		 . "                           when '2' then '(화)'"
		 . "                           when '3' then '(수)'"
		 . "                           when '4' then '(목)'"
		 . "                           when '5' then '(금)'"
		 . "                           when '6' then '(토)'"
		 . "                           else '(일)' end as weekDay"
		 . ",      case when t01_svc_subcode = '200' then concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2),'-',left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2),'(',t01_sugup_soyotime,'분)')"
		 . "                                         else concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2),'-',left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2)) end as planTime"
		 . ",      t01_sugup_soyotime"
		 . ",      t01_sugup_date"
	     . ",      t01_sugup_fmtime"
		 . ",      concat(cast(substring(t01_conf_date,5,2) as unsigned),'/',cast(substring(t01_conf_date,7,2) as unsigned)) as workDate"
		 . ",      concat(left(ifnull(t01_conf_fmtime, ''),2),':',substring(ifnull(t01_conf_fmtime, ''),3,2)) as workFmTime"
		 . ",      concat(left(ifnull(t01_conf_totime, ''),2),':',substring(ifnull(t01_conf_totime, ''),3,2)) as workToTime"
		 . ",      t01_conf_date"
		 . ",      ifnull(t01_conf_soyotime, 0) - (ifnull(t01_conf_soyotime, 0) mod 30) as workProcTime"
		 . ",      t01_sugup_seq"
		 #. ",      t01_suga"
		 . ",      t01_suga_tot"
		 . ",      t01_conf_suga_code"
		 . ",      case when length(t01_conf_fmtime) = 4 and length(t01_conf_totime) = 4 and t01_conf_soyotime > 0 then t01_conf_suga_value else 0 end as confSugaValue"
		 . ",      t01_status_gbn"
		 . ",      t01_sugup_yoil"
		 . ",      holiday_name"
		 . ",      t01_svc_subcode"
		 . ",      SUB.m81_name as svcName"
		 . "  from t01iljung"
		 . "  left join tbl_holiday"
		 . "    on mdate = t01_sugup_date"
		 . " inner join m81gubun as SUB"
		 . "    on SUB.m81_gbn  = 'SUB'"
		 . "   and SUB.m81_code = t01_svc_subcode"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mSugup
		 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
		 . "'  and t01_del_yn = 'N'";
	
	if ($mPopup == 'Y'){
		$sql .= " and t01_svc_subcode = '".$mSvcCode."'";
	}

	$sql .= " order by t01_sugup_date"
		 .  ",         t01_sugup_fmtime";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$today = date('Ymd', mkTime());

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i); 

		$sugaCode = $row['t01_conf_suga_code'];
		$sugaName = $conn->get_suga($mCode, $sugaCode);
		$sugaValue = $row['confSugaValue'];
		$sumPlanTime += $row['t01_sugup_soyotime'];
		$sugupDate = $row['sugupDate'].$row['weekDay'];

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
			}else{
				$dateTitle = '';
			}
		}

		if ($row['t01_svc_subcode'] == '200'){
			if ($row['workProcTime'] == '0'){
				//$sugaCode = '';
				$sugaName = '';
				$sugaValue = 0;
			}
		}else{
			if (strLen($row['workFmTime']) != 5 or strLen($row['workToTime']) != 5){
				//$sugaCode = '';
				$sugaName = '';
				$sugaValue = 0;
			}
		}

		# 실적일자
		$confDate = subStr($row['t01_conf_date'], 4, 2).'/'.subStr($row['t01_conf_date'], 6, 2);
		
		if ($i+1 == $row_count){
			$nextIndex = '';
		}else{
			$nextIndex = 'if(this.value.length > 3){document.getElementsByName(\'workDate[]\')['.($i+1).'].focus();}';
		}
		?>
		<tr>
		<td style="text-align:center;"><?=$i+1;?></td>
		<td style="text-align:center;"><?=$row['svcName'];?></td>
		<td style="text-align:center;" title="<?=$dateTitle;?>"><?=$sugupDate;?></td>
		<td style="text-align:left; line-height:1.3em;"><?=$row['planTime'];?></td>
		<td style="text-align:left;">
		<input name="workDate[]" type="text" value="<?=$confDate;?>" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName('workFmTime[]')[<?=$i;?>].focus();}" onFocus="__replace(this, '/', '');" onBlur="__styleDate(this);">
		</td>
		<td style="text-align:left;">
		<input name="workFmTime[]" type="text" value="<?=$row['workFmTime'];?>" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName('workToTime[]')[<?=$i;?>].focus();}" onFocus="__replace(this, ':', '');" onBlur="__styleTime(this);" onChange="setWorkPorcTime(<?=$i;?>);">
		<input name="workToTime[]" type="text" value="<?=$row['workToTime'];?>" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="<?=$nextIndex;?>" onFocus="__replace(this, ':', '');" onBlur="__styleTime(this);" onChange="setWorkPorcTime(<?=$i;?>);">
		<?
			if ($row['t01_svc_subcode'] == '200'){
			?>
				<input name="workProcTime[]" type="text" value="<?=$row['workProcTime'];?>" maxlength="4" class="number" style="text-align:center; width:40px; background-color:#eeeeee;" readOnly>분
			<?
			}else{
			?>
				<input name="workProcTime[]" type="text" value="<?=$row['workProcTime'];?>" class="number" style="text-align:center; width:40px; background-eeeeee:#;" disabled="true">분
			<?
			}
		?>
		</td>
		<td style="text-align:left;" id="sugaName[]" title="<?=$sugaName;?>"><?=left($sugaName, 7);?></td>
		<td style="text-align:right;" id="sugaValue[]"><?=number_format($sugaValue);?></td>
		<td style="text-align:right;" id="sugaValuePlan[]"><?=number_format($row['t01_suga_tot']);?></td>
		<td style="text-align:center; padding-top:1px;">
		<?
			if ($confFlag == '0'){
			?>
				<input name="btnSave[]" type="button" value="수정" onClick="saveSugupConfirm(<?=$i;?>);" style="width:48px; height:24px; border:0px; background:url('../image/btn_small_1.png') no-repeat; cursor:pointer;">
			<?
			}
		?>
		</td>
		</tr>
		<input name="mDate[]"        type="hidden" value="<?=$row['t01_sugup_date'];?>">
		<input name="mFmTime[]"      type="hidden" value="<?=$row['t01_sugup_fmtime'];?>">
		<input name="mSeq[]"         type="hidden" value="<?=$row['t01_sugup_seq'];?>">
		<input name="mSvcCode[]"     type="hidden" value="<?=$row['t01_svc_subcode'];?>">
		<!--<input name="workDate[]"     type="hidden" value="<?=$row['t01_sugup_date'];?>">-->
		<input name="sugaCode[]"     type="hidden" value="<?=$row['t01_conf_suga_code'];?>">
		<input name="planProcTime[]" type="hidden" value="<?=$row['t01_sugup_soyotime'];?>">
		<input name="sugaPrice[]"    type="hidden" value="<?=$sugaValue;?>">
		<input name="sugaPricePlan[]"type="hidden" value="<?=$row['t01_suga_tot'];?>">
		<input name="changeFlag[]"   type="hidden" value="N">
	<?
	}

	$conn->row_free();
?>
<input name="mKey" type="hidden" value="<?=$mKey;?>">
</tbody>
</table>
</form>
<?
	$con2->close();

	include("../inc/_footer.php");
?>
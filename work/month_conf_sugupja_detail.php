<?
	include('../inc/_header.php');
	include('../inc/_ed.php');

	$con2 = new connection();

	$mYear = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
	$mDay = $_POST['mDay'];
	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mType = $_POST['mType'];

	if ($mType == 'DAY'){
		$confFlag = 0;
		?>
		<table style="width:100%;">
		<tr>
			<td class="title">일별마감(<?=$mYear.'년'.$mMonth.'월'.$mDay.'일';?>)</td>
			<td class="title_td" style="text-align:right;">
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onClick="dayDiaryOk();">저장</button></span>
				<span class="btn_pack m"><span class="before"></span><button type="button" onClick="setDayConfCalendar(document.getElementById('myBody'), '<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear;?>', '<?=$mMonth;?>');">이전</button></span>
			</td>
		</tr>
		</table>
		<input name="mKind"		type="hidden" value="<?=$mKind;?>">
		<input name="mYear"		type="hidden" value="<?=$mYear;?>">
		<input name="mMonth"	type="hidden" value="<?=$mMonth;?>">
		<input name="mDay"		type="hidden" value="<?=$mDay;?>">
	<?
	}else{
		$mSugupja = $ed->decode(urlDecode($_POST['mSugupja']));

		$sql = "select count(*)"
			 . "  from t13sugupja"
			 . " where t13_ccode = '".$mCode
			 . "'  and t13_mkind = '".$mKind
			 . "'  and t13_jumin = '".$mSugupja
			 . "'  and t13_pay_date = '".$mYear.$mMonth
			 . "'  and t13_type = '2'";
		$confFlag = $conn->get_data($sql);
	}
?>
<table class="view_type1" style="width:100%;">
<?
	if ($mType == 'DAY'){
	?>
	<tr>
		<th style="width:7%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">수급자</th>
		<th style="width:9%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
		<th style="width:14%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">계획시간</th>
		<th style="width:21%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">실적</th>
		<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">제공서비스</th>
		<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">실적<br>급여액</th>
		<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">계획<br>급여액</th>
		<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">담당자</th>
		<th style="width:10%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">비고</th>
	</tr>
	<tr>
		<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:1px solid #e5e5e5;">일자</th>
		<th style="width:19%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:1px solid #e5e5e5;">시간</th>
	</tr>
	<?
	}else{
	?>
	<tr style="height:24px;">
		<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
		<th style="width:23%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">계획</th>
		<th style="width:25%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">실적</th>
		<th style="width:14%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">제공서비스</th>
		<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">실적<br>급여액</th>
		<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">계획<br>급여액</th>
		<th style="width:8%;  padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">담당자</th>
		<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">비고</th>
	</tr>
	<tr style="height:24px;">
		<th style="width:9%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">일자</th>
		<th style="width:14%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시간</th>
		<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">일자</th>
		<th style="width:19%; padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시간</th>
	</tr>
	<?
	}
?>
<tbody id="listTotal">
	<tr>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;"></td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="planTime"></td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;">계</td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="realTime"></td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold;" id="realService"></td>
		<td style="text-align:right;  background-color:#f7fbfd; font-weight:bold;" id="totalSuga">0</td>
		<td style="text-align:right;  background-color:#f7fbfd; font-weight:bold;" id="totalSugaPlan">0</td>
		<td style="text-align:center; background-color:#f7fbfd; font-weight:bold; text-align:right; padding-top:1px; padding-right:5px;" colspan="2">
		<?
			if ($confFlag == 0){
			?>
				<span class="btn_pack m"><button type="button" onClick="setPlanTimeToWorkTime('all');">일괄복사</button></span>
			<?
			}
		?>
		</td>
	</tr>
</tbody>
<tbody id="listDetail">
<?
	$sql = "select concat(substring(t01_sugup_date,5,2),'/',substring(t01_sugup_date,7,2)) as sugupDate"
		 . ",      case t01_sugup_yoil when '1' then '(월)'"
		 . "                           when '2' then '(화)'"
		 . "                           when '3' then '(수)'"
		 . "                           when '4' then '(목)'"
		 . "                           when '5' then '(금)'"
		 . "                           when '6' then '(토)'"
		 . "                           else '(일)' end as weekDay"
		 . ",      case when t01_svc_subcode = '200' then concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2),'-',left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2),'(',t01_sugup_soyotime,'분)')"
		 . "                                         else concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2),'-',left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2)) end as planTime"
		 . ",      concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2)) as sugupFromTime"
		 . ",      concat(left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2)) as sugupToTime"
		 . ",     (case when ((hour(concat(left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2))) * 60 + minute(concat(left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2)))) -"
		 . "                  (hour(concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2))) * 60 + minute(concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2))))) < 0 then (24 * 60) else 0 end) +"
		 . "     ((hour(concat(left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2))) * 60 + minute(concat(left(t01_sugup_totime,2),':',substring(t01_sugup_totime,3,2)))) -"
		 . "      (hour(concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2))) * 60 + minute(concat(left(t01_sugup_fmtime,2),':',substring(t01_sugup_fmtime,3,2))))) as planSoyoTime"
		 . ",      t01_sugup_soyotime"
		 . ",      t01_sugup_date"
	     . ",      t01_sugup_fmtime"
		 . ",      concat(cast(substring(t01_conf_date,5,2) as unsigned),'/',cast(substring(t01_conf_date,7,2) as unsigned)) as workDate"
		 . ",      concat(left(ifnull(t01_conf_fmtime, ''),2),':',substring(ifnull(t01_conf_fmtime, ''),3,2)) as workFmTime"
		 . ",      concat(left(ifnull(t01_conf_totime, ''),2),':',substring(ifnull(t01_conf_totime, ''),3,2)) as workToTime"
		 . ",      t01_conf_date"
		 . ",      case when t01_svc_subcode = '200' then ifnull(t01_conf_soyotime, 0) - (ifnull(t01_conf_soyotime, 0) mod 30) else ifnull(t01_conf_soyotime, 0) end as workProcTime"
		 . ",      t01_sugup_seq"
		 . ",      t01_suga_tot"
		 . ",      t01_conf_suga_code"
		 . ",      case when length(t01_conf_fmtime) = 4 and length(t01_conf_totime) = 4 and t01_conf_soyotime > 0 then t01_conf_suga_value else 0 end as confSugaValue"
		 . ",      t01_status_gbn as status"
		 . ",      t01_sugup_yoil"
		 . ",      holiday_name"
		 . ",      t01_svc_subcode"
		 . ",      SUB.m81_name as svcName"
		 . ",      t01_yoyangsa_id1"
		 . ",      t01_yoyangsa_id2"
		 . ",      t01_yoyangsa_id3"
		 . ",      t01_yoyangsa_id4"
		 . ",      t01_yoyangsa_id5"
		 . ",      t01_yname1"
		 . ",      t01_yname2"
		 . ",      t01_yname3"
		 . ",      t01_yname4"
		 . ",      t01_yname5";

	if ($mType == 'DAY'){
		$sql .=", t01_jumin as sugupjaCode"
			 . ", m03_name as sugupjaName"
			 . ", m03_key as sugupjaKey"
			 . ", (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = left(t01_sugup_date, 6)) as confCount";
	}

	$sql .="  from t01iljung"
		 . "  left join tbl_holiday"
		 . "    on mdate = t01_sugup_date"
		 . " inner join m81gubun as SUB"
		 . "    on SUB.m81_gbn  = 'SUB'"
		 . "   and SUB.m81_code = t01_svc_subcode";

	if ($mType == 'DAY'){
		$sql .=" inner join m03sugupja"
			 . "    on m03_ccode = t01_ccode"
			 . "   and m03_mkind = t01_mkind"
			 . "   and m03_jumin = t01_jumin";
	}

	$sql .=" where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'";

	if ($mType == 'DAY'){
		$sql .=" and t01_sugup_date = '".$mYear.$mMonth.$mDay
			 . "'  and t01_del_yn = 'N'"
			 . " order by m03_name, t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime";
	}else{
		$sql .="  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
			 . "' and t01_jumin = '".$mSugupja
			 . "'  and t01_del_yn = 'N'"
			 . " order by t01_svc_subcode, t01_sugup_date, t01_sugup_fmtime";
	}

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	//$today = date('Ymd', mkTime());

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		// 다음 이동 인덱스
		if ($i+1 == $row_count){
			$nextIndex = '';
		}else{
			$nextIndex = 'if(this.value.length > 3){document.getElementsByName(\'workDate[]\')['.($i+1).'].focus();}';
		}

		echo '<tr>';

		if ($mType == 'DAY'){
			// 수급자
			if ($sugupjaCode != $row['sugupjaCode']){
				$sugupjaCode  = $row['sugupjaCode'];
				echo '<td style="text-align:left;">'.$row['sugupjaName'].'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;">&nbsp;</td>';
			}

			if ($serviceName != $row['sugupjaCode'].$row['svcName']){
				$serviceName  = $row['sugupjaCode'].$row['svcName'];
				echo '<td style="text-align:left;">'.$row['svcName'].'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;">&nbsp;</td>';
			}
		}else{
			// 서비스명
			if ($serviceName != $row['svcName']){
				$serviceName  = $row['svcName'];
				echo '<td style="text-align:left;">'.$serviceName.'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;">&nbsp;</td>';
			}

			// 계획일자
			if ($sugupDate != $row['svcName'].$row['sugupDate'].$row['weekDay']){
				$sugupDate  = $row['svcName'].$row['sugupDate'].$row['weekDay'];

				if ($row['holiday_name'] != ''){
					$dateString = '<font color="#ff0000">'.$row['sugupDate'].$row['weekDay'].'</font>';
					$dateTitle = $row['holiday_name'];
				}else{
					if ($row['t01_sugup_yoil'] == '6'){
						$dateString = '<font color="#0000ff">'.$row['sugupDate'].$row['weekDay'].'</font>';
						$dateTitle = '토요일';
					}else if ($row['t01_sugup_yoil'] == '7' or $row['t01_sugup_yoil'] == '0'){
						$dateString = '<font color="#ff0000">'.$row['sugupDate'].$row['weekDay'].'</font>';
						$dateTitle = '일요일';
					}else{
						$dateString = $row['sugupDate'].$row['weekDay'];
						$dateTitle = '';
					}
				}
				echo '<td style="text-align:left;" title="'.$dateTitle.'">'.$dateString.'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;">&nbsp;</td>';
			}
		}

		// 계획시간
		echo '<td style="text-align:left; line-height:1.3em;">'.$row['planTime'].'</td>';

		// 실적일자
		$confDate = subStr($row['t01_conf_date'], 4, 2).'/'.subStr($row['t01_conf_date'], 6, 2);

		echo '<td style="text-align:left;">';
		if ($confFlag == 0){
			if ($row['confCount'] > 0){
				if ($confDate != '/'){
					echo $confDate;
				}else{
					echo '';
				}
				echo '<input name="workDate[]" type="hidden" value="'.$confDate.'">';
			}else{
				echo '<input name="workDate[]" type="text" value="'.$confDate.'" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName(\'workFmTime[]\')['.$i.'].focus();}" onFocus="__replace(this, \'/\', \'\');" onBlur="__styleDate(this);">';  // onChange=\'document.getElementsByName("changeFlag[]")['.$i.'].value = "Y";\'
			}
		}else{
			if ($confDate != '/'){
				echo $confDate;
			}else{
				echo '';
			}
			echo '<input name="workDate[]" type="hidden" value="'.$confDate.'">';
		}
		echo '</td>';

		// 실적시간
		echo '<td style="text-align:left;">';
		if ($confFlag == 0){
			if ($row['confCount'] > 0){
				echo ($row['workFmTime'] != ':' ? $row['workFmTime'].'~' : '').($row['workToTime'] != ':' ? $row['workToTime'].'['.$row['workProcTime'].'분]' : '');
				echo '<input name="workFmTime[]" type="hidden" value="'.$row['workFmTime'].'">';
				echo '<input name="workToTime[]" type="hidden" value="'.$row['workToTime'].'">';
				echo '<input name="workProcTime[]" type="hidden" value="'.$row['workProcTime'].'">';
			}else{
				echo '<input name="workFmTime[]" type="text" value="'.$row['workFmTime'].'" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName(\'workToTime[]\')['.$i.'].focus();}" onFocus="__replace(this, \':\', \'\');" onBlur="__styleTime(this);" onChange="setWorkPorcTime('.$i.');">';
				echo '<input name="workToTime[]" type="text" value="'.$row['workToTime'].'" maxlength="4" class="number" style="text-align:center; width:40px; margin-left:2px; margin-right:2px;" onKeyDown="__onlyNumber(this);" onKeyUp="'.$nextIndex.'" onFocus="__replace(this, \':\', \'\');" onBlur="__styleTime(this);" onChange="setWorkPorcTime('.$i.');">';

				if ($row['t01_svc_subcode'] == '200'){
					echo '<input name="workProcTime[]" type="text" value="'.$row['workProcTime'].'" maxlength="4" class="number" style="text-align:center; width:40px; background-color:#eeeeee;" readOnly>분';
				}else{
					echo '<input name="workProcTime[]" type="text" value="'.$row['workProcTime'].'" class="number" style="text-align:center; width:40px; background-eeeeee:#;" onFocus="this.blur();" readOnly>분';
				}
			}
		}else{
			if ($row['workFmTime'] != ':'){
				echo $row['workFmTime'];

				if ($row['workToTime'] != ':'){
					echo '~';
					echo $row['workToTime'];
					echo '['.$row['workProcTime'].'분]';
				}else{
					echo '';
				}
			}else{
				echo '';
			}
			echo '<input name="workFmTime[]" type="hidden" value="'.$row['workFmTime'].'">';
			echo '<input name="workToTime[]" type="hidden" value="'.$row['workToTime'].'">';
			echo '<input name="workProcTime[]" type="hidden" value="'.$row['workProcTime'].'">';
		}
		echo '</td>';

		// 수가정보
		$sugaCode = $row['t01_conf_suga_code']; //수가코드
		$sugaName = $conn->get_suga($mCode, $sugaCode); //수가명
		$sugaValue = $row['confSugaValue']; //수가단가
		$sumPlanTime += $row['t01_sugup_soyotime']; //총계획시간

		if ($row['t01_svc_subcode'] == '200'){
			// 요양인 경우 실행시간이 없으면 수가 및 금액을 0처리한다.
			if ($row['workProcTime'] == '0'){
				$sugaName = '';
				$sugaValue = 0;
			}
		}else{
			// 요양이 아닌 경우 실행시간이 없으면 수가 및 금액을 0처리한다.
			if (strLen($row['workFmTime']) != 5 or strLen($row['workToTime']) != 5){
				$sugaName = '';
				$sugaValue = 0;
			}
		}

		// 제공서비스
		echo '<td style="text-align:left;" id="sugaName[]" title="'.$sugaName.'">'.left($sugaName, 8).'</td>';

		// 실적금액
		echo '<td style="text-align:right;" id="sugaValue[]">'.number_format($sugaValue).'</td>';

		// 계획급여액
		echo '<td style="text-align:right;" id="sugaValuePlan[]">'.number_format($row['t01_suga_tot']).'</td>';

		// 담당자
		echo '<td style="text-align:left;">'.left($row['t01_yname1'], 3).'</td>';

		// 비고
		if ($confDate == '/' and $row['workFmTime'] == ':' and $row['workToTime'] == ':'){
			if ($confFlag == 0){
				if ($mType == 'DAY'){
					echo '<td style="text-align:center;">';
					if ($row['confCount'] > 0){
						echo '확정';
						$planToWork = 'N';
					}else{
					?>
						<span class="btn_pack small" title="일정복사"><button type="button" onClick="setPlanTimeToWorkTime('<?=$i;?>');">C</button></span>
						<span class="btn_pack small" title="일정변경"><button type="button" onClick="_setSugupjaDayReg('<?=$mCode;?>','<?=$mKind;?>','<?=$mYear;?>','<?=$mMonth;?>','<?=$mDay;?>','<?=$ed->en($row['sugupjaCode']);?>','<?=$ed->en($row['t01_yoyangsa_id1']);?>','<?=$row['sugupjaKey'];?>');">D</button></span>
						<?
						$planToWork = 'Y';
					}
					echo '</td>';
				}else{
				//	echo '<td style="text-align:center; padding-top:1px;"><input type="button" onClick="setPlanTimeToWorkTime('.$i.');" value="복사" class="btnSmall1" onFocus="this.blur();"></td>';
				?>
					<td style="text-align:center; padding-top:1px;"><span class="btn_pack small"><button type="button" onClick="setPlanTimeToWorkTime('<?=$i;?>');">복사</button></span></td>
					<?
					$planToWork = 'Y';
				}
			}else{
				if ($row['confCount'] > 0){
					echo '<td style="text-align:center; padding-top:1px;">확정</td>';
					$planToWork = 'Y';
				}else{
					echo '<td style="text-align:center; padding-top:1px;">&nbsp;</td>';
					$planToWork = 'N';
				}
			}
		}else{
			if ($row['confCount'] > 0){
				echo '<td style="text-align:center; padding-top:1px;">확정</td>';
				$planToWork = 'Y';
			}else{
			?>
				<td style="text-align:center; padding-top:1px;">
				<?
				if ($row['status'] == 'C'){
				?>
					<span style="color:#ff0000;">error</span>
				<?
				}else{
				?>
					&nbsp;
				<?
				}
				?>
				</td>
				<?

				$planToWork = 'N';
			}
		}

		//echo $row['sugupjaCode'].'/'.$ed->en($row['sugupjaCode']);

		echo '</tr>';
	?>
		<input name="planToWork[]"		type="hidden" value="<?=$planToWork;?>">
		<input name="planDate[]"		type="hidden" value="<?=$row['sugupDate'];?>">
		<input name="planFromTime[]"	type="hidden" value="<?=$row['sugupFromTime'];?>">
		<input name="planToTime[]"		type="hidden" value="<?=$row['sugupToTime'];?>">
		<input name="planSoyoTime[]"	type="hidden" value="<?=$row['planSoyoTime'];?>">

		<input name="mDate[]"			type="hidden" value="<?=$row['t01_sugup_date'];?>">
		<input name="mFmTime[]"			type="hidden" value="<?=$row['t01_sugup_fmtime'];?>">
		<input name="mSeq[]"			type="hidden" value="<?=$row['t01_sugup_seq'];?>">
		<input name="mSvcCode[]"		type="hidden" value="<?=$row['t01_svc_subcode'];?>">
		<input name="sugaCode[]"		type="hidden" value="<?=$row['t01_conf_suga_code'];?>">
		<input name="planProcTime[]"	type="hidden" value="<?=$row['t01_sugup_soyotime'];?>">
		<input name="sugaPrice[]"		type="hidden" value="<?=$sugaValue;?>">
		<input name="sugaPricePlan[]"	type="hidden" value="<?=$row['t01_suga_tot'];?>">
		<input name="changeFlag[]"		type="hidden" value="N">

		<input name="sugupDate[]"		type="hidden" value="<?=$row['t01_sugup_date'];?>">
		<input name="sugupFmTime[]"		type="hidden" value="<?=$row['t01_sugup_fmtime'];?>">
		<input name="sugupSeq[]"		type="hidden" value="<?=$row['t01_sugup_seq'];?>">

		<input name="statusGubun[]"		type="hidden" value="<?=$row['status'];?>">
		<?
		if ($mType == 'DAY'){
		?>
			<input name="sugupja[]" type="hidden" value="<?=$ed->en($row['sugupjaCode']);?>">
		<?
		}
	}
	$conn->row_free();
?>
</tbody>
</table>
<?
	$con2->close();

	include("../inc/_footer.php");
?>
<?
	include("../inc/_header.php");
	include("../inc/_ed.php");

	//print_r($_PARAM);

	$con2     = new connection();
	$_PARAM   = $_REQUEST;
	$mCode    = $_PARAM["mCode"];
	$mKind    = $_PARAM["mKind"];
	$mKey     = $_PARAM["mKey"];
	$mJuminNo = $_PARAM["mJuminNo"];
	$calYear  = $_PARAM["calYear"];
	$calMonth = $_PARAM["calMonth"];
	$calDay   = $_PARAM["calDay"];
	$gubun    = $_PARAM["gubun"];

	// 수급자의 급여한도를 조회한다.
	$sql = "select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind, m03_ylvl
			  from (
				   select m03_kupyeo_max, m03_kupyeo_1, m03_bonin_yul, m03_skind, m03_ylvl
				   ,      m03_sdate
				   ,      m03_edate
				     from m03sugupja
					where m03_ccode = '$mCode'
					  and m03_mkind = '$mKind'
					  and m03_jumin = '$mJuminNo'
					union all
				   select m31_kupyeo_max, m31_kupyeo_1, m31_bonin_yul, m31_kind, m31_level
				   ,      m31_sdate
				   ,      m31_edate
					 from m31sugupja
					where m31_ccode = '$mCode'
					  and m31_mkind = '$mKind'
				      and m31_jumin = '$mJuminNo'
				   ) as t
			 where '$calYear$calMonth' between left(m03_sdate, 6) and left(m03_edate, 6)
			 order by m03_sdate desc, m03_edate desc
			 limit 1";
	$client_array = $conn->get_array($sql);
	$max_amount   = $client_array[0];	//한도금액
	$max_group    = $client_array[1];	//정부지원금한도액
	$client_kind  = $client_array[3];	//수급자구분
	$client_lvl   = $client_array[4];	//등급

	unset($client_array);

	/*
	 * 청구한도금액을 한도급액으로 설정한다.
	 */
	$max_amount = $max_group;

	$suga_total = 0; //수가총금액

	$nowYM = date("Ym", mktime()); // 현재 년월
	$workType = $_PARAM['workType'];
	if ($workType == "modify"){
		// 확정처리시 일정을 수정하는 경우
		$mJuminNo = $ed->de($_PARAM["mJuminNo"]);
		$confStartDate = getPMonth();
	}else if ($workType == "dayModify"){
		$mJuminNo = $ed->de($_PARAM["mJuminNo"]); //수급자
		$mYoyangsa = $ed->de($_PARAM['mYoyangsa']); //요양사(아직은 요양사별 처리는 하지 않았다.)
		$confStartDate = $calYear.$calMonth.$calDay;
	}else{
		// 센터의 계약시작일을 가져온다.
		$sql = "select m00_cont_date"
			 . "  from m00center"
			 . " where m00_mcode = '".$mCode
			 . "'  and m00_mkind = '".$mKind
			 . "'";
		$centerStartDate = $conn->get_data($sql);
		$centerStartDate = subStr($centerStartDate, 0, 6);
		$confStartDate = '999999';
	}

	if ($centerStartDate != $nowYM) $centerStartDate = '999999';

	if (strLen($calMonth) == 1){
		$calMonth = "0".$calMonth;
	}

	$calTime  = mkTime(0, 0, 1, $calMonth, 1, $calYear);
	$boninYul = $conn->get_bonin_yul($mCode, $mKind, $mJuminNo);

	// 수급자의 등급을 찾는다.
	$sugupjaLevel = $conn->get_sugupja_level($mCode, $mKind, $mJuminNo);

	$sql = "select *"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mJuminNo
		 . "'  and left(t01_sugup_date, 6) = '".$calYear.$calMonth
		 . "'  and t01_del_yn = 'N'"
		 . " order by t01_sugup_date"
		 . ",         t01_sugup_fmtime";
	$conn->query($sql);
	$row = $conn->fetch();
	$row_count = $conn->row_count();
?>
<input name="mWorkType" type="hidden" value="<?=$workType;?>">
<table style="width:900px;">
	<tr>
		<td style="text-align:left; background-color:#eeeeee; font-weight:bold;" colspan="14">
		<table style="width:100%;">
		<tr>
			<td style="width:50%; text-align:left; padding-left:5px; border:0;">
			<?
				if ($workType == "modify" || ($workType != 'dayModify' && $gubun == 'reg')){
				?>
					<table width="100%">
					<tr>
						<td style="border:none; text-align:left; padding-left:5px; font-weight:bold;">
							<?=$calYear;?>년<?=$calMonth;?>월
							<input name="calYear" type="hidden" value="<?=$calYear;?>">
							<input name="calMonth" type="hidden" value="<?=$calMonth;?>">
						</td>
						<td style="border:none; text-align:right; padding-right:5px;">
						<?
							if ($workType == 'modify'){?>
								<input type="button" onClick="_iljungModify();" value="저장" class="btnSmall2" onFocus="this.blur();"><?
							}
						?>
						</td>
					</tr>
					</table>
				<?
				}else if ($workType == "dayModify"){
				?>
					<table width="100%">
					<tr>
						<td style="border:none; text-align:left; padding-left:5px; font-weight:bold;">
							<?=$calYear;?>년<?=$calMonth;?>월<?=$calDay;?>일
							<input name="calYear" type="hidden" value="<?=$calYear;?>">
							<input name="calMonth" type="hidden" value="<?=$calMonth;?>">
							<input name="calDay" type="hidden" value="<?=$calDay;?>">
						</td>
						<td style="border:none; text-align:right; padding-right:5px;">
							<input type="button" onClick="_iljungModify();" value="저장" class="btnSmall2" onFocus="this.blur();">
						</td>
					</tr>
					</table>
				<?
				}else{
				?>
					<select name="calYear" style="width:65px;" onChange="_setCalendar();">
					<?
						for($i=2010; $i<=2011; $i++){
							echo '<option value="'.$i.'" '.($calYear == $i ? "selected" : "").'>'.$i.'년</option>';
						}
					?>
					</select>
					<select name="calMonth" style="width:55px;" onChange="_setCalendar();">
						<option value="01"<? if($calMonth == "01"){echo "selected";}?>>1월</option>
						<option value="02"<? if($calMonth == "02"){echo "selected";}?>>2월</option>
						<option value="03"<? if($calMonth == "03"){echo "selected";}?>>3월</option>
						<option value="04"<? if($calMonth == "04"){echo "selected";}?>>4월</option>
						<option value="05"<? if($calMonth == "05"){echo "selected";}?>>5월</option>
						<option value="06"<? if($calMonth == "06"){echo "selected";}?>>6월</option>
						<option value="07"<? if($calMonth == "07"){echo "selected";}?>>7월</option>
						<option value="08"<? if($calMonth == "08"){echo "selected";}?>>8월</option>
						<option value="09"<? if($calMonth == "09"){echo "selected";}?>>9월</option>
						<option value="10"<? if($calMonth == "10"){echo "selected";}?>>10월</option>
						<option value="11"<? if($calMonth == "11"){echo "selected";}?>>11월</option>
						<option value="12"<? if($calMonth == "12"){echo "selected";}?>>12월</option>
					</select>
				<?
				}
			?>
			<span id="spanYear" style="display:none;"></span>
			<span id="spanMonth" style="display:none;"></span>
			</td>
			<td style="width:50%; border:0; text-align:right; padding-right:5px;">
			<?
				if ($gubun == "search"){
				?>	<span class='btn_pack m icon'><span id='spanIcon' class='pdf'></span><button id='btnIljungPrint' type='button' onFocus='this.blur();' onClick="serviceCalendarShow('<?=$mCode;?>','<?=$mKind;?>','<?=$calYear;?>','<?=$calMonth;?>','<?=$mKey;?>', 's','n','pdf','y')/*_printIljung();*/">일정출력</button></span>	<?
				}
			?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#ff0000;" colspan="2">일</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">월</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">화</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">수</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">목</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">금</td>
		<td style="width:132px; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#0000ff;" colspan="2">토</td>
	</tr>
	<?
		$today     = date("Ymd", mktime());
		$lastDay   = date("t", $calTime); //총일수 구하기
		$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
		$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
		$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay)); //마지막 요일 구하기
		$day=1; //화면에 표시할 화면의 초기값을 1로 설정
		$index = 1;
		$dbStart = 0;
		$addFlag = false;

		$new_total_suga = 0;

	// 요양보호사 일정리스트
		for($t=1; $t<=5; $t++){
			if ($_PARAM['yoy'.$t] != ''){
				if (ceil($_PARAM['fmTime']) > ceil($_PARAM['ttTime'])){
					$ToTime = ceil($_PARAM['ttTime']) + 2400;
				}else{
					$ToTime = $_PARAM['ttTime'];
				}

				# 일정 검사시 센터구분을 제외한다.
				$sql = "select t01_sugup_date"
					 . "  from t01iljung"
					 . " where t01_sugup_date like '".substr($iljungDate,0,6)
					 . "%' and '".$_PARAM['yoy'.$t]."' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)"
					 . "   and ((t01_sugup_fmtime <= '".$_PARAM['fmTime']."'"
					 . "   and   case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$_PARAM['fmTime']."')"
					 . "    or  (t01_sugup_fmtime <  '".$ToTime."'"
					 . "   and   case when t01_sugup_fmtime > t01_sugup_totime then cast(t01_sugup_totime as unsigned) + 2400 else t01_sugup_totime end > '".$ToTime."'))"
					 . "   and t01_del_yn = 'N'";
				$con2->query($sql);
				$con2->fetch();
				$row2_count = $con2->row_count();

				for($l=0; $l<$row2_count; $l++){
					$row2 = $con2->select_row($l);
					$temp_yoy_iljung[$t][$l] = $row2[0];
				}

				$con2->row_free();
			}
		}

	// 휴일리스트
		$sql = "select mdate, ifnull(holiday_name, '')
				  from tbl_holiday
				 where mdate like '".substr($iljungDate,0,6)."%'
				 order by mdate";
		$con2->query($sql);
		$con2->fetch();
		$row2_count = $con2->row_count();

		for($l=0; $l<$row2_count; $l++){
			$row2 = $con2->select_row($l);
			$temp_holiday_list[$l]['date'] = $row2[0];
			$temp_holiday_list[$l]['name'] = $row2[1];
		}
		$temp_holiday_list_count = sizeof($temp_holiday_list);

		$con2->row_free();

		for($i=1; $i<=$lastDay; $i++){
			$dayIndex[$i] = 1;
		}

		ob_start();

		// 총 주 수에 맞춰서 세로줄 만들기
		for($i=1; $i<=$totalWeek; $i++){
			echo "<tr>";
			// 총 가로칸 만들기
			for ($j=0; $j<7; $j++){
				echo "<td style='width:20px; vertical-align:top; line-height:1.5em; background-color:#f8f9e3;'>";
				// 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않아야하므로
				// 그 반대의 경우 -  ! 으로 표현 - 에만 날자를 표시한다.
				$subject = '';
				$subjectID = '';
				$subjectPrint = '';
				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
					$index = $dayIndex[$day];
					$iljungDate = date("Ymd", mkTime(0, 0, 1, $calMonth, $day, $calYear));

					/*
					$sql = "select ifnull(holiday_name, '')"
						 . "  from tbl_holiday"
						 . " where mdate = '".$iljungDate
						 . "'";
					$con2->query($sql);
					$row = $con2->fetch();
					if ($row[0] != ''){
						$holidayName = $row[0];
						$holiday = 'Y';
					}else{
						$holidayName = '';
						$holiday = 'N';
					}
					$con2->row_free();
					*/
					$holidayName = '';
					$holiday	 = 'N';
					for($l=0; $l<$temp_holiday_list_count; $l++){
						if ($temp_holiday_list[$l]['date'] == $iljungDate){
							$holidayName = $temp_holiday_list[$l]['name'];
							$holiday	 = 'Y';
							break;
						}
					}

					if ($holidayName == ''){
						if($j == 0){
							echo "<font color='#FF0000'>".$day."</font>";
						}else if($j == 6){
							echo "<font color='#0000FF'>".$day."</font>";
						}else{
							echo "<font color='#000000'>".$day."</font>";
						}
					}else{
						echo "<font color='#FF0000' title='".$holidayName."'>".$day."</font>";
					}


					//if ($today <= $iljungDate and $gubun == 'reg'){
					if ($workType == "dayModify"){
						//if ($confStartDate == $iljungDate){
						echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_PARAM['mCode'].'\',\''.$_PARAM['mKind'].'\',\''.$_PARAM['mKey'].'\',\''.$day.'\',\''.$iljungDate.'\',\''.$j.'\');">';
						//}
					}else{
						if (($centerStartDate == subStr($iljungDate, 0, 6)) or
							($confStartDate <= subStr($iljungDate, 0, 6)) or
							($today <= $iljungDate and $gubun == 'reg')){
							// 계약시작월 이거나 일자 및 시간이 현재보다 미래인경우 일정을 등록할 수 있도록 풀어준다.
							echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_PARAM['mCode'].'\',\''.$_PARAM['mKind'].'\',\''.$_PARAM['mKey'].'\',\''.$day.'\',\''.$iljungDate.'\',\''.$j.'\');">';
						}
					}

					$subjectID = "txtSubject_".$day;

					for($k=0; $k<$row_count; $k++){
						$row = $conn->select_row($k);

						if ($row['t01_sugup_date'] == $iljungDate){
							$index = $dayIndex[$day];

							$mSugaName = GetSugaName($con2, $mCode, $row['t01_suga_code1']);

							if ($row['t01_suga_over'] > 0){
								$Egubun = 'Y';
							}else{
								$Egubun = 'N';
							}

							if ($row['t01_suga_night'] > 0){
								$Ngubun = 'Y';
							}else{
								$Ngubun = 'N';
							}

							$rowData[$index]['date']     = $row['t01_sugup_date'];
							$rowData[$index]['FmTime']   = $row['t01_sugup_fmtime'];
							$rowData[$index]['ToTime']   = $row['t01_sugup_totime'];
							$rowData[$index]['yoy1']     = $row['t01_yoyangsa_id1'];
							$rowData[$index]['yoy2']     = $row['t01_yoyangsa_id2'];
							$rowData[$index]['yoy3']     = $row['t01_yoyangsa_id3'];
							$rowData[$index]['yoy4']     = $row['t01_yoyangsa_id4'];
							$rowData[$index]['yoy5']     = $row['t01_yoyangsa_id5'];
							$rowData[$index]['sugaCode'] = $row['t01_suga_code1'];

							$subjectParam = array('svcSubCode'=> $row['t01_svc_subcode'],
								                  'FmTime'    => $row['t01_sugup_fmtime'],
												  'ToTime'    => $row['t01_sugup_totime'],
												  'yoy1'      => $row['t01_yoyangsa_id1'],
												  'yoy2'      => $row['t01_yoyangsa_id2'],
												  'yoy3'      => $row['t01_yoyangsa_id3'],
												  'yoy4'      => $row['t01_yoyangsa_id4'],
												  'yoy5'      => $row['t01_yoyangsa_id5'],
												  'yoyName1'  => $row['t01_yname1'],
												  'yoyName2'  => $row['t01_yname2'],
												  'yoyName3'  => $row['t01_yname3'],
												  'yoyName4'  => $row['t01_yname4'],
												  'yoyName5'  => $row['t01_yname5'],
												  'sugaName'  => $mSugaName,
												  'statusGbn' => $row['t01_status_gbn'],
												  'transYn'   => $row['t01_trans_yn'],
												  'modifyPos' => $row['t01_modify_yn'],
												  'centerCode'=> $mCode,
												  'centerKind'=> $mKind,
												  'maxAmt'	  => 0,
												  'sugaAmt'	  => 0,
												  'addAmt'    => 0,
												  'clientLvl' => '');
							$subjectTemp = GetSubject($conn, $centerStartDate, $confStartDate, 'Y', $gubun, $subjectPrint == '' ? '0' : '1', $subjectID, $day, $index, 'N', 'N', $today, $iljungDate, $subjectParam);
							$subjectPrint .= $subjectTemp[0];

							$yoyangsaTimePay = $conn->get_time_pay($mCode, $mKind, $row['t01_yoyangsa_id1'], $sugupjaLevel);
							if ($yoyangsaTimePay == ''){
								$yoyangsaTimePay = $row['t01_ysigup'];
							}

							$old_date = $row['t01_sugup_date'].$row['t01_sugup_fmtime'].$row['t01_sugup_totime'];

							$inputParam = array('centerStartDate'	=> $centerStartDate,
												'confStartDate'		=> $confStartDate,
												'iljungDate'		=> $row['t01_sugup_date'],
												'svcSubCode'		=> $row['t01_svc_subcode'],
												'svcSubCD'			=> $row['t01_svc_subcd'],
												'fmTime'			=> $row['t01_sugup_fmtime'],
												'ttTime'			=> $row['t01_sugup_totime'],
												'procTime'			=> $row['t01_sugup_soyotime'],
												'togeUmu'			=> $row['t01_toge_umu'],
												'bipayUmu'			=> $row['t01_bipay_umu'],
												'timeDoub'			=> $row['t01_time_doub'],
												'yoy1'				=> $row['t01_yoyangsa_id1'],
												'yoy2'				=> $row['t01_yoyangsa_id2'],
												'yoy3'				=> $row['t01_yoyangsa_id3'],
												'yoy4'				=> $row['t01_yoyangsa_id4'],
												'yoy5'				=> $row['t01_yoyangsa_id5'],
												'yoyNm1'			=> $row['t01_yname1'],
												'yoyNm2'			=> $row['t01_yname2'],
												'yoyNm3'			=> $row['t01_yname3'],
												'yoyNm4'			=> $row['t01_yname4'],
												'yoyNm5'			=> $row['t01_yname5'],
												'yoyTA1'			=> $yoyangsaTimePay,
												'yoyTA2'			=> '0',
												'yoyTA3'			=> '0',
												'yoyTA4'			=> '0',
												'yoyTA5'			=> '0',
												'sPrice'			=> $row['t01_suga'],
												'ePrice'			=> $row['t01_suga_over'],
												'nPrice'			=> $row['t01_suga_night'],
												'tPrice'			=> $row['t01_suga_tot'],
												'sugaCode'			=> $row['t01_suga_code1'],
												'sugaName'			=> $mSugaName,
												'Egubun'			=> $Egubun,
												'Ngubun'			=> $Ngubun,
												'Etime'				=> $row['t01_e_time'],
							                    'Ntime'				=> $row['t01_n_time'],
												'duplicate'			=> 'N',
												'weekDay'			=> $row['t01_sugup_yoil'],
												'subject'			=> $subjectTemp,
												'use'				=> 'Y',
												'seq'				=> $row['t01_sugup_seq'],
												'sugupja'			=> 'N',
												'statusGbn'			=> $row['t01_status_gbn'],
												'transYn'			=> $row['t01_trans_yn'],
												'carNo'				=> $row['t01_car_no'],
												'sudangYN'			=> $row['t01_ysudang_yn'],
												'sudang'			=> $row['t01_ysudang'],
												'sudangYul1'		=> $row['t01_ysudang_yul1'],
												'sudangYul2'		=> $row['t01_ysudang_yul2'],
												'holiday'			=> $holiday,
												'oldDate'			=> $old_date,
												'modifyPos'			=> $row['t01_modify_pos']);
							$inputString = GetInputString($gubun, $day, $index, $inputParam);
							echo $inputString;

							// 수가총금액
							$suga_total += $row['t01_suga_tot'];

							$dayIndex[$day]++;
						}
					}

					$index    = $dayIndex[$day];
					$newTime  = date('Hi', mkTime());
					$old_date = '';

					//if (($today > $iljungDate) or ($today == $iljungDate and $_PARAM["fmTime"] < $newTime)){
					//}else{
					if (($centerStartDate == subStr($iljungDate, 0, 6)) or
						($confStartDate <= subStr($iljungDate, 0, 6)) or
						($today < $iljungDate) or
						($today == $iljungDate and ($_PARAM["fmTime"] != null ? $_PARAM["fmTime"] : ($newTime + 1))> $newTime)){
						if ($_PARAM["weekDay".$j] == "Y"){
							$subject = "Y";
							$tempWeekDay = $j != 0 ? $j : 7;
						}else{
							$subject = "N";
							$tempWeekDay = "";
						}

						$sugaCode = $_PARAM["sugaCode"];
						$sugaCode1 = subStr($sugaCode,0,2);
						$sugaCode2 = subStr($sugaCode,3,2);

						if ($_PARAM['svcSubCode'] != '500'){
							//if ($tempWeekDay == 6 or $tempWeekDay == 7 or $holidayName != ''){
							if (($tempWeekDay == 7 or $holidayName != '') and $_PARAM['togeUmu'] != 'Y'){
								$sugaCode = $sugaCode1.'H'.$sugaCode2;
								//$holiday = 'Y';
							}else{
								$sugaCode = $sugaCode1.'W'.$sugaCode2;
								//$holiday = 'N';
							}
						}else{
							//$holiday = 'N';
						}

						if ($sugaCode != $_PARAM["sugaCode"]){
							$sugaName  = GetSugaName($con2, $mCode, $sugaCode);
							$sugaValue = GetSugaValue($con2, $mCode, $sugaCode);
							$sPrice = $sugaValue;
							$ePrice = $_PARAM['ePrice'];
							$nPrice = $_PARAM['nPrice'];
							$tPrice = $_PARAM['tPrice'];

							if ($holiday == 'Y'){
								if ($sPrice > $tPrice){
									// 수가가 수가총합보다 크면...
									$tPrice = $sPrice;
								}
							}else{
								if ($_PARAM['Egubun'] == 'Y') $ePrice = $sPrice * 0.2;
								if ($_PARAM['Ngubun'] == 'Y') $nPrice = $sPrice * 0.3;

								$ePrice = $ePrice - ($ePrice % 10);
								$nPrice = $nPrice - ($nPrice % 10);

								$tPrice = $sPrice + $ePrice + $nPrice;
							}
						}else{
							$sugaName = $_PARAM["sugaName"];
							$sPrice = $_PARAM['sPrice'];
							$ePrice = $_PARAM['ePrice'];
							$nPrice = $_PARAM['nPrice'];
							$tPrice = $_PARAM['tPrice'];
						}

						$duplicate = 'N';
						$sugupja = 'N';

						// 중복확인
						if (sizeOf($rowData) > 0){
							$rowCount = sizeOf($rowData);
						}else{
							$rowCount = 1;
						}
						for($k=1; $k<=$rowCount; $k++){
							for($t=1; $t<=2; $t++){
								if ($_PARAM['yoy'.$t] != ''){
									if (ceil($_PARAM['fmTime']) > ceil($_PARAM['ttTime'])){
										$ToTime = ceil($_PARAM['ttTime']) + 2400;
									}else{
										$ToTime = $_PARAM['ttTime'];
									}

									$temp_yoy_iljung_count = sizeof($temp_yoy_iljung[$t]);

									for($l=0; $l<$temp_yoy_iljung_count; $l++){
										if ($temp_yoy_iljung[$t][$l] == $iljungDate){
											$sugupja = 'Y';
											break;
										}
									}
								}

								if (sizeOf($rowData) > 0){
									if ($rowData[$k]['date'] == $iljungDate){
										if ($rowData[$k]['FmTime'] >= $_PARAM["fmTime"] && $rowData[$k]['ToTime'] > $_PARAM["fmTime"] && $rowData[$k]['FmTime'] < $_PARAM["ttTime"]){
											$duplicate = 'Y';
											break;
										}
									}
								}
							}
						}

						$subjectParam = array('svcSubCode'=> $_PARAM['svcSubCode'],
							                  'FmTime'    => $_PARAM["fmTime"],
											  'ToTime'    => $_PARAM["ttTime"],
											  'yoy1'      => $_PARAM['yoy1'],
											  'yoy2'      => $_PARAM['yoy2'],
											  'yoy3'      => $_PARAM['yoy3'],
											  'yoy4'      => $_PARAM['yoy4'],
											  'yoy5'      => $_PARAM['yoy5'],
											  'yoyName1'  => $_PARAM['yoyNm1'],
											  'yoyName2'  => $_PARAM['yoyNm2'],
											  'yoyName3'  => $_PARAM['yoyNm3'],
											  'yoyName4'  => $_PARAM['yoyNm4'],
											  'yoyName5'  => $_PARAM['yoyNm5'],
											  'sugaName'  => $sugaName,
											  'statusGbn' => '9',
											  'transYn'   => 'N',
											  'modifyPos' => '',
											  'centerCode'=> $mCode,
											  'centerKind'=> $mKind,
											  'maxAmt'	  => $max_amount,
											  'sugaAmt'	  => $suga_total,
											  'addAmt'	  => $tPrice,
											  'clientLvl' => $client_lvl);
						$subjectTemp = GetSubject($conn, $centerStartDate, $confStartDate, $subject, $gubun, $subjectPrint == '' ? '0' : '1', $subjectID, $day, $index, $duplicate, $sugupja, $today, $iljungDate, $subjectParam);
						$subject     = $subjectTemp[0];
						$suga_total  = $subjectTemp[2];

						// 수가 총금액이 한도금액보다 넘으면 중복처리한다.
						if ($max_amount < $suga_total) $duplicate = 'Y';

						if ($_PARAM["sugaCode"] != ""){
							//if ($today <= $iljungDate){
							if (($centerStartDate == subStr($iljungDate, 0, 6)) or ($today <= $iljungDate)){
								if ($subject != ""){
									$mUse = "Y";
								}else{
									$mUse = "N";
								}
							}else{
								$mUse = "N";
							}
							$dayIndex[$day]++;
						}else{
							$mUse = "N";
						}

						if ($mUse == "N"){
							$subject  = "<div id='".$subjectID."_".$index."' style='display:; width:108px;'></div>";
							$subject .= "<div id='checkDuplicate_".$day."_".$index."' style='display:none;'>중복</div>";
						}

						if ($mUse == 'Y'){
							$eTime = $_PARAM['Etime'];
							$nTime = $_PARAM['Ntime'];
							$visitSudangCheck = $_PARAM['visitSudangCheck'];
							$visitSudang = $_PARAM['visitSudang'];
							$sudangYul1 = $_PARAM['sudangYul1'];
							$sudangYul2 = $_PARAM['sudangYul2'];
						}else{
							$eTime = 0;
							$nTime = 0;
							$visitSudangCheck = 'N';
							$visitSudang = 0;
							$sudangYul1 = 0;
							$sudangYul2 = 0;
							$sPrice = 0;
							$ePrice = 0;
							$nPrice = 0;
							$tPrice = 0;
						}

						if ($_PARAM['svcSubCode'] == '200' and $_PARAM['procTime'] == '0'){
							// 요양중 수행시간이 없을 경우 수행시간을 계산한다.
							$tempFH = subStr($_PARAM['fmTime'],0,2);
							$tempFM = subStr($_PARAM['fmTime'],2,2);
							$tempTH = subStr($_PARAM['ttTime'],0,2);
							$tempTM = subStr($_PARAM['ttTime'],2,2);

							if ($tempFH > $tempTH) $tempTH += 24;


							$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 - $tempFM);
						}else{
							$procTime = $_PARAM['procTime'];

							// 목욕일 경우 수행시간을 계산한다.
							if ($procTime == 'K' or $procTime == 'F'){
								$tempFH = subStr($_PARAM['fmTime'],0,2);
								$tempFM = subStr($_PARAM['fmTime'],2,2);
								$tempTH = subStr($_PARAM['ttTime'],0,2);
								$tempTM = subStr($_PARAM['ttTime'],2,2);

								if ($tempFH > $tempTH) $tempTH += 24;

								$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM);
							}
						}

						$inputParam = array('centerStartDate'	=> $centerStartDate,
											'confStartDate'		=> $confStartDate,
											'iljungDate'		=> $iljungDate,
											'svcSubCode'		=> $_PARAM['svcSubCode'],
											'svcSubCD'			=> $_PARAM['svcSubCD'],
											'fmTime'			=> $_PARAM['fmTime'],
											'ttTime'			=> $_PARAM['ttTime'],
											'procTime'			=> $procTime,
											'togeUmu'			=> $_PARAM['togeUmu'],
											'bipayUmu'			=> $_PARAM['bipayUmu'],
											'timeDoub'			=> $_PARAM['timeDoub'],
											'yoy1'				=> $_PARAM['yoy1'],
											'yoy2'				=> $_PARAM['yoy2'],
											'yoy3'				=> $_PARAM['yoy3'],
											'yoy4'				=> $_PARAM['yoy4'],
											'yoy5'				=> $_PARAM['yoy5'],
											'yoyNm1'			=> $_PARAM['yoyNm1'],
											'yoyNm2'			=> $_PARAM['yoyNm2'],
											'yoyNm3'			=> $_PARAM['yoyNm3'],
											'yoyNm4'			=> $_PARAM['yoyNm4'],
											'yoyNm5'			=> $_PARAM['yoyNm5'],
											'yoyTA1'			=> $_PARAM['yoyTA1'],
											'yoyTA2'			=> $_PARAM['yoyTA2'],
											'yoyTA3'			=> $_PARAM['yoyTA3'],
											'yoyTA4'			=> $_PARAM['yoyTA4'],
											'yoyTA5'			=> $_PARAM['yoyTA5'],
											'sPrice'			=> $sPrice,
											'ePrice'			=> $ePrice,
											'nPrice'			=> $nPrice,
											'tPrice'			=> $tPrice,
											'sugaCode'			=> $sugaCode,
											'sugaName'			=> $sugaName,
											'Egubun'			=> $_PARAM['Egubun'],
											'Ngubun'			=> $_PARAM['Ngubun'],
											'Etime'				=> $eTime,
							                'Ntime'				=> $nTime,
											'duplicate'			=> $duplicate,
											'weekDay'			=> $tempWeekDay,
											'subject'			=> $subject,
											'use'				=> $mUse,
											'seq'				=> '0',
											'sugupja'			=> $sugupja,
											'statusGbn'			=> '9',
											'transYn'			=> 'N',
							                'carNo'				=> $_PARAM['carNo'],
											'sudangYN'			=> $visitSudangCheck,
											'sudang'			=> $visitSudang,
											'sudangYul1'		=> $sudangYul1,
											'sudangYul2'		=> $sudangYul2,
											'holiday'			=> $holiday,
											'oldDate'			=> $old_date,
											'modifyPos'			=> 'N');
						$inputString = GetInputString($gubun, $day, $index, $inputParam);
						echo $inputString;
					}

					if ($old_date == ''){
						// 수급자 월수급 총금액
						$new_total_suga += $tPrice;
					}

					$subjectPrint .= $subject;

					$day++;
				}

				echo "</td>";
				echo "<td style='width:108px; text-align:left; vertical-align:top; line-height:1.3em;".($iljungDate == $calYear.$calMonth.$calDay ? 'border:2px solid #0000ff;' : '')."' id='".$subjectID."'>";

				// 일별마감시 선택된 수급자와 요양사의 데이타만 표시한다.
				//if ($workType == "dayModify"){
				//	if ($confStartDate == $iljungDate){
				//		echo $subjectPrint;
				//	}
				//}else{
				//	echo $subjectPrint;
				//}
				echo $subjectPrint;
				echo "</td>";
			}
			echo "</tr>";
		}
		//echo "<br><br><br>";
		//print_r($iljung[2]);

		$ob_value = ob_get_contents();
		ob_clean();
		echo $ob_value;

		unset($temp_holiday_list);
		unset($temp_yoy_iljung);
	?>
</table>
<div id="addCalendar" style="width:900px; display:;"></div>
<input name="mLastDay"		 type="hidden" value="<?=$lastDay;?>">
<input name="boninYul"		 type="hidden" value="<?=$boninYul;?>">
<input name="new_total_suga" type="hidden" value="<?=$new_total_suga;?>">
<input name="suga_total"	 type="hidden" value="<?=$suga_total;?>">
<?
	$conn->row_free();
	$con2->close();

	include("../inc/_footer.php");

	function GetInputString($pGubun, $pDay, $pIndex, $pParam){
		if ($pGubun == 'search' and $pParam['statusGbn'] != '1'){
			$newInputString  = '';
			$newInputString .= '<input name="mUse_'       .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['use'].'">';
			$newInputString .= '<input name="mDelete_'    .$pDay.'_'.$pIndex.'" type="hidden" value="N">';
			$newInputString .= '<input name="mDuplicate_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['duplicate'].'">';
			$newInputString .= '<input name="mYoy1_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy1'].'">';
			$newInputString .= '<input name="mStatusGbn_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['statusGbn'].'">';
		}else{
			if ($pParam['centerStartDate'] == subStr($pParam['iljungDate'], 0, 6) or
				$pParam['confStartDate'] <= subStr($pParam['confStartDate'], 0, 6)){
				$statusGbn = $pParam['statusGbn'];

				if ($statusGbn == '0') $statusGbn = '9';
			}else{
				$statusGbn = $pParam['statusGbn'];
			}
			$newInputString  = '';
			$newInputString .= '<input name="mDate_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['iljungDate'].'">';
			$newInputString .= '<input name="mSvcSubCode_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['svcSubCode'].'">';
			$newInputString .= '<input name="mSvcSubCD_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['svcSubCD'].'">';
			$newInputString .= '<input name="mFmTime_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['fmTime'].'">';
			$newInputString .= '<input name="mToTime_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['ttTime'].'">';
			$newInputString .= '<input name="mProcTime_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['procTime'].'">';
			$newInputString .= '<input name="mTogeUmu_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['togeUmu'].'">';
			$newInputString .= '<input name="mBiPayUmu_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['bipayUmu'].'">';
			$newInputString .= '<input name="mTimeDoub_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['timeDoub'].'">';
			$newInputString .= '<input name="mYoy1_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy1'].'">';
			$newInputString .= '<input name="mYoy2_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy2'].'">';
			$newInputString .= '<input name="mYoy3_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy3'].'">';
			$newInputString .= '<input name="mYoy4_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy4'].'">';
			$newInputString .= '<input name="mYoy5_'      .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoy5'].'">';
			$newInputString .= '<input name="mYoyNm1_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm1'].'">';
			$newInputString .= '<input name="mYoyNm2_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm2'].'">';
			$newInputString .= '<input name="mYoyNm3_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm3'].'">';
			$newInputString .= '<input name="mYoyNm4_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm4'].'">';
			$newInputString .= '<input name="mYoyNm5_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyNm5'].'">';
			$newInputString .= '<input name="mYoyTA1_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA1'].'">';
			$newInputString .= '<input name="mYoyTA2_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA2'].'">';
			$newInputString .= '<input name="mYoyTA3_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA3'].'">';
			$newInputString .= '<input name="mYoyTA4_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA4'].'">';
			$newInputString .= '<input name="mYoyTA5_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['yoyTA5'].'">';
			$newInputString .= '<input name="mSValue_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sPrice'].'">';
			$newInputString .= '<input name="mEValue_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['ePrice'].'">';
			$newInputString .= '<input name="mNValue_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['nPrice'].'">';
			$newInputString .= '<input name="mTValue_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['tPrice'].'">';
			$newInputString .= '<input name="mSugaCode_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugaCode'].'">';
			$newInputString .= '<input name="mSugaName_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugaName'].'">';
			$newInputString .= '<input name="mEGubun_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Egubun'].'">';
			$newInputString .= '<input name="mNGubun_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Ngubun'].'">';
			$newInputString .= '<input name="mETime_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Etime'].'">';
			$newInputString .= '<input name="mNTime_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['Ntime'].'">';
			$newInputString .= '<input name="mDuplicate_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['duplicate'].'">';
			$newInputString .= '<input name="mWeekDay_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['weekDay'].'">';
			$newInputString .= '<input name="mSubject_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['subject'].'">';
			$newInputString .= '<input name="mUse_'       .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['use'].'">';
			$newInputString .= '<input name="mSeq_'       .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['seq'].'">';
			$newInputString .= '<input name="mSugupja_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sugupja'].'">';
			$newInputString .= '<input name="mDelete_'    .$pDay.'_'.$pIndex.'" type="hidden" value="N">';
			$newInputString .= '<input name="mTrans_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['transYn'].'">';
			$newInputString .= '<input name="mStatusGbn_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$statusGbn.'">';
			$newInputString .= '<input name="mCarNo_'     .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['carNo'].'">';
			$newInputString .= '<input name="mSudangYN_'  .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYN'].'">';
			$newInputString .= '<input name="mSudang_'    .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudang'].'">';
			$newInputString .= '<input name="mSudangYul1_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYul1'].'">';
			$newInputString .= '<input name="mSudangYul2_'.$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['sudangYul2'].'">';
			$newInputString .= '<input name="mHoliday_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['holiday'].'">';
			$newInputString .= '<input name="mOldDate_'   .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['oldDate'].'">';
			$newInputString .= '<input name="mModifyPos_' .$pDay.'_'.$pIndex.'" type="hidden" value="'.$pParam['modifyPos'].'">';
		}
		return $newInputString;
	}

	function GetSubject($conn, $pCenterStartDate, $pConfStartDate, $gubun, $pGubun, $topBorder, $subjectID, $pDay, $pIndex, $pDuplicate, $pSugupja, $pToday, $pIljungDate, $pParam){
		$fontColor = '#000000';
		$showBtn = 'Y';

		if (($pCenterStartDate == subStr($pIljungDate, 0, 6) or $pConfStartDate <= subStr($pIljungDate, 0, strLen($pConfStartDate))) && $pParam['statusGbn'] != '1' && $pParam['statusGbn'] != '5'){
			// 계약월인 경우 일자 및 시간 관계없이 수정 및 삭제가 가능하도록 풀어준다.
			// 단 수행중이거나 완료된 일정은 수정 및 삭제가 불가능하다.
		}else{
			if ($pToday <= $pIljungDate){
				if ($pToday == $pIljungDate){
					$newTime = date('Hi', mkTime());
					if ($pParam["FmTime"] < $newTime){
						$fontColor = '#cccccc';
						$showBtn = 'N';
					}
				}
			}else{
				$fontColor = '#cccccc';
				$showBtn = 'N';
			}
		}

		$subject  = '';

		// 초과금액 체크를 해제한다.
		/*
		if ($pParam['clientLvl'] != '9'){
			if ($gubun == 'Y'){
				$amt_max  = intval($pParam["maxAmt"]);
				$amt_suga = intval($pParam["sugaAmt"]) + intval($pParam["addAmt"]);
			}else{
				$amt_suga = intval($pParam["sugaAmt"]);
			}

			if ($amt_max < $amt_suga){
				$pDuplicate = 'Y';
				$over_amt = 'Y';
			}else{
				$over_amt = 'N';
			}
		}else{
			$over_amt = 'N';
		}
		*/

		if ($pDuplicate == 'Y'){
			$backGroundColor = '#ff0000';
			$duplicateDisplay = '';
		}else{
			$backGroundColor = '';
			$duplicateDisplay = 'none';
		}

		if ($pSugupja == 'Y'){
			$backGroundColor = '#ff0000';
			$displaySugupja = '';
		}else{
			$displaySugupja = 'none';
		}

		if ($pGubun == 'search'){
			switch($pParam['statusGbn']){
				case '0': //미수행
					$fontColor = '#ff0000';
					break;
				case '1': //완료
					$fontColor = '#1b8830;';
					break;
				case '5': //수행중
					$fontColor = '#0000ff';
					break;
				case '9': //준비중
					if ($showBtn == 'Y'){
						$fontColor = '#000000';
					}else{
						$fontColor = '#ff0000';
					}
					break;
			}
		}else{
			if ($pParam['statusGbn'] == '1'){
				/*
				  D : 실적관리/일실적 수동입력에서 저장
				  M : 실적관리/월실적 확정처리에서 저장
				  N : 실적관리에서 수정하지 않은데이타
				 */
				if ($pParam['modifyPos'] != 'D'){
					$showBtn = 'N';
				}
			}
		}

		if ($gubun == 'Y'){
			$subject = "";
			/*
			if ($pParam['svcSubCode'] == '500'){
				$subject .= substr($pParam["FmTime"],0,2).":".substr($pParam["FmTime"],2,2)."<br>";
			}else{
				$subject .= substr($pParam["FmTime"],0,2).":".substr($pParam["FmTime"],2,2)."~";
				$subject .= substr($pParam["ToTime"],0,2).":".substr($pParam["ToTime"],2,2)."<br>";
			}
			*/
			$subject .= substr($pParam["FmTime"],0,2).":".substr($pParam["FmTime"],2,2)."~";
			$subject .= substr($pParam["ToTime"],0,2).":".substr($pParam["ToTime"],2,2)."<br>";

			// 요양사의 활동여부를 조회한다.
			for($i=1; $i<=5; $i++){
				$yoyStat[$i] = '1';
			}

			if ($pParam["yoy1"] != '') $yoyStat[1] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_mkind = '".$pParam["centerKind"]."' and m02_yjumin = '".$pParam["yoy1"]."'");
			if ($pParam["yoy2"] != '') $yoyStat[2] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_mkind = '".$pParam["centerKind"]."' and m02_yjumin = '".$pParam["yoy2"]."'");
			if ($pParam["yoy3"] != '') $yoyStat[3] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_mkind = '".$pParam["centerKind"]."' and m02_yjumin = '".$pParam["yoy3"]."'");
			if ($pParam["yoy4"] != '') $yoyStat[4] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_mkind = '".$pParam["centerKind"]."' and m02_yjumin = '".$pParam["yoy4"]."'");
			if ($pParam["yoy5"] != '') $yoyStat[5] = $conn->get_array("select m02_ygoyong_stat, m02_ytoisail from m02yoyangsa where m02_ccode = '".$pParam["centerCode"]."' and m02_mkind = '".$pParam["centerKind"]."' and m02_yjumin = '".$pParam["yoy5"]."'");

			for($i=1; $i<=5; $i++){
				if ($yoyStat[$i][0] == '1' || $yoyStat[$i][0] == '2'){
					$cancelLine[$i][1] = '';
					$cancelLine[$i][2] = '';
				}else{
					if ($yoyStat[$i][1] <= $pIljungDate){
						$cancelLine[$i][1] = '<s style=\'font-weight:bold; color:#7608e7;\'>';
						$cancelLine[$i][2] = '</s>';
					}else{
						$cancelLine[$i][1] = '';
						$cancelLine[$i][2] = '';
					}
				}
			}

			$subject .= $pParam["yoyName1"] != "" ? $cancelLine[1][1].$pParam["yoyName1"].$cancelLine[1][2]."," : "";
			$subject .= $pParam["yoyName2"] != "" ? $cancelLine[2][1].$pParam["yoyName2"].$cancelLine[2][2]."," : "";
			$subject .= $pParam["yoyName3"] != "" ? $cancelLine[3][1].$pParam["yoyName3"].$cancelLine[3][2]."," : "";
			$subject .= $pParam["yoyName4"] != "" ? $cancelLine[4][1].$pParam["yoyName4"].$cancelLine[4][2]."," : "";
			$subject .= $pParam["yoyName5"] != "" ? $cancelLine[5][1].$pParam["yoyName5"].$cancelLine[5][2]."," : "";
			$subject  = mb_substr($subject, 0, mb_strlen($subject,"UTF-8") - 1, "UTF-8")."<br>";
			$subject .= $pParam["sugaName"];
		}else{
			$subject = '';
		}
		if ($subject != ""){
			$tempSubject = $subject;
			$subject  = '';
			$subject .= "<div style='display:; background-color:".$backGroundColor."; width:108px; border-top:".$topBorder."px dotted #cccccc;' id='".$subjectID."_".$pIndex."'>";
			$subject .= "<table>";
			$subject .= "<tr>";
			$subject .= "<td class='noborder' style='width:100%; text-align:left; vertical-align:top; line-height:1.3em;'>";
			$subject .= "    <div style='position:absolute; width:100%; height:100%;'>";
			$subject .= "        <div style='position:absolute; top:1px; left:80px;'>";

			if ($pGubun == 'reg'){
				//if ($pToday <= $pIljungDate and $pParam['statusGbn'] == '9' and $pParam['transYn'] == 'N'){
				if ($showBtn == 'Y'){
					$subject .= " <img src='../image/btn_edit.png' style='cursor:pointer;' onClick='_modifyDiary(".$pDay.",".$pIndex.");'>";
					$subject .= " <img src='../image/btn_del.png' style='cursor:pointer;' onClick='_clearDiary(".$pDay.",".$pIndex.");'>";
				}
				//}
			}

			$subject .= "        </div>";
			$subject .= "    </div>";
			$subject .= "    <div style='color:".$fontColor.";'>".$tempSubject."</div>";

			if ($over_amt == 'Y'){
				$subject .= "    <div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'>한도초과</div>";
				$subject .= "    <div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'>한도초과</div>";
			}else{
				$subject .= "    <div id='checkDuplicate_".$pDay."_".$pIndex."' style='display:".$duplicateDisplay.";'>중복</div>";
				$subject .= "    <div id='checkSugupja_".$pDay."_".$pIndex."' style='display:".$displaySugupja.";'>타수급자중복</div>";
			}

			$subject .= "</td>";
			$subject .= "</tr>";
			$subject .= "</table>";
			$subject .= "</div>";
		}

		$arraySubject[0] = $subject;
		$arraySubject[1] = $showBtn;
		$arraySubject[2] = $amt_suga;

		return $arraySubject;
	}
?>
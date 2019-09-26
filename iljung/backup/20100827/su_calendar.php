<?
	include("../inc/_header.php");
	
	//print_r($_POST);

	$mCode    = $_POST["mCode"];
	$mKind    = $_POST["mKind"];
	$mKey     = $_POST["mKey"];
	$mJuminNo = $_POST["mJuminNo"];
	$calYear  = $_POST["calYear"];
	$calMonth = $_POST["calMonth"];
	$calTime  = mkTime(0, 0, 1, $calMonth, 1, $calYear);

	$sql = "select *"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mJuminNo
		 . "'  and left(t01_sugup_date, 6) = '".$calYear.$calMonth
		 . "'"
		 . " order by t01_sugup_date"
		 . ",         t01_sugup_fmtime";
	echo $sql;
	$conn->query($sql);
	$conn->fetch();
?>
<table style="width:900px;">
	<tr>
		<td style="text-align:left; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="14">
			<select name="calYear" style="width:65px;" onChange="_setCalendar();">
				<option value="2010"<? if($calYear == "2010"){echo "selected";}?>>2010년</option>
			</select>
			<select name="calMonth" style="width:55px;" onChange="_setCalendar();">
				<option value="1"<? if($calMonth == "1"){echo "selected";}?>>1월</option>
				<option value="2"<? if($calMonth == "2"){echo "selected";}?>>2월</option>
				<option value="3"<? if($calMonth == "3"){echo "selected";}?>>3월</option>
				<option value="4"<? if($calMonth == "4"){echo "selected";}?>>4월</option>
				<option value="5"<? if($calMonth == "5"){echo "selected";}?>>5월</option>
				<option value="6"<? if($calMonth == "6"){echo "selected";}?>>6월</option>
				<option value="7"<? if($calMonth == "7"){echo "selected";}?>>7월</option>
				<option value="8"<? if($calMonth == "8"){echo "selected";}?>>8월</option>
				<option value="9"<? if($calMonth == "9"){echo "selected";}?>>9월</option>
				<option value="10"<? if($calMonth == "10"){echo "selected";}?>>10월</option>
				<option value="11"<? if($calMonth == "11"){echo "selected";}?>>11월</option>
				<option value="12"<? if($calMonth == "12"){echo "selected";}?>>12월</option>
			</option>
		</td>
	</tr>
	<tr>
		<td style="width:130px; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#ff0000;" colspan="2">일</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">월</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">화</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">수</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">목</td>
		<td style="width:128px; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">금</td>
		<td style="width:130px; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#0000ff;" colspan="2">토</td>
	</tr>
	<?
		$iljung["mKey"]     = $mKey;
		$iljung["mJuminNo"] = $mJuminNo;
		
		$today     = date("Ymd", mktime());
		$lastDay   = date("t", $calTime); //총일수 구하기
		$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
		$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
		$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay)); //마지막 요일 구하기
		$day=1; //화면에 표시할 화면의 초기값을 1로 설정
		$index = 1;
		$dbStart = 0;
		
		for($i=1; $i<=$lastDay; $i++){
			$dayIndex[$i] = 1;
		}

		// 총 주 수에 맞춰서 세로줄 만들기
		for($i=1; $i<=$totalWeek; $i++){
			echo "<tr>";
			// 총 가로칸 만들기
			for ($j=0; $j<7; $j++){
				echo "<td style='width:20px; vertical-align:top; line-height:1.5em; background-color:#f8f9e3;'>";
				// 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않아야하므로
				// 그 반대의 경우 -  ! 으로 표현 - 에만 날자를 표시한다.
				$subject = "";
				$subjectID = "";
				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
					$index = $dayIndex[$day];
					$iljung[$day]["mDate"] = date("Ymd", mkTime(0, 0, 1, $calMonth, $day, $calYear));

					if ($today > $iljung[$day]["mDate"]){
					}else{
						?>	
						<input name="mDate_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$iljung[$day]["mDate"];?>">
						<input name="mSvcSubCode_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["svcSubCode"];?>">
						<input name="mSvcSubCD_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["svcSubCD"];?>">
						<input name="mFmTime_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["fmTime"];?>">
						<input name="mToTime_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["ttTime"];?>">
						<input name="mProcTime_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["procTime"];?>">
						<input name="mTogeUmu_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["togeUmu"];?>">
						<input name="mBiPayUmu_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["bipayUmu"];?>">
						<input name="mTimeDoub_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["timeDoub"];?>">
						<input name="mYoy1_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoy1"];?>">
						<input name="mYoy2_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoy2"];?>">
						<input name="mYoy3_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoy3"];?>">
						<input name="mYoy4_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoy4"];?>">
						<input name="mYoy5_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoy5"];?>">
						<input name="mYoyNm1_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyNm1"];?>">
						<input name="mYoyNm2_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyNm2"];?>">
						<input name="mYoyNm3_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyNm3"];?>">
						<input name="mYoyNm4_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyNm4"];?>">
						<input name="mYoyNm5_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyNm5"];?>">
						<input name="mYoyTA1_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyTA1"];?>">
						<input name="mYoyTA2_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyTA2"];?>">
						<input name="mYoyTA3_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyTA3"];?>">
						<input name="mYoyTA4_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyTA4"];?>">
						<input name="mYoyTA5_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["yoyTA5"];?>">
						<input name="mSValue_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["sPrice"];?>">
						<input name="mEValue_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["ePrice"];?>">
						<input name="mNValue_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["nPrice"];?>">
						<input name="mTValue_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["tPrice"];?>">
						<input name="mSugaCode_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["sugaCode"];?>">
						<input name="mSugaName_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["sugaName"];?>">
						<input name="mEGubun_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["Egubun"];?>">
						<input name="mNGubun_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$_POST["Ngubun"];?>">
						<input name="mDuplicate_<?=$day;?>_<?=$index;?>" type="hidden" value="N">
						<?
						if ($_POST["weekDay".$j] == "Y"){?>
							<input name="mWeekDay_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$j != 0 ? $j : 7;?>"><?
							$subject = "Y";
						}else{?>
							<input name="mWeekDay_<?=$day;?>_<?=$index;?>" type="hidden" value=""><?
							$subject = "N";
						}

						$subjectID = "txtSubject_".$day;

						if ($subject == "Y"){
							$subject  = "";
							$subject .= substr($_POST["fmTime"],0,2).":".substr($_POST["fmTime"],2,2)."~";
							$subject .= substr($_POST["ttTime"],0,2).":".substr($_POST["ttTime"],2,2)."<br>";
							$subject .= $_POST["yoyNm1"] != "" ? $_POST["yoyNm1"]."," : "";
							$subject .= $_POST["yoyNm2"] != "" ? $_POST["yoyNm2"]."," : "";
							$subject .= $_POST["yoyNm3"] != "" ? $_POST["yoyNm3"]."," : "";
							$subject .= $_POST["yoyNm4"] != "" ? $_POST["yoyNm4"]."," : "";
							$subject .= $_POST["yoyNm5"] != "" ? $_POST["yoyNm5"]."," : "";
							$subject  = mb_substr($subject, 0, mb_strlen($subject,"UTF-8") - 1, "UTF-8")."<br>";
							$subject .= $_POST["sugaName"];
						}else{
							$subject = "";
						}

						if ($subject != ""){
							$tempSubject = $subject;
							$subject  = "";
							$subject .= "<div style='display:;' id='".$subjectID."_".$index."'>";
							$subject .= "<table>";
							$subject .= "  <tr>";
							$subject .= "    <td class='noborder' style='width:100%; text-align:left; vertical-align:top; line-height:1.3em;'>";
							$subject .= "      <div style='position:absolute; width:100%; height:100%;'>";
							$subject .= "        <div style='position:absolute; top:1px; left:80px;'>";
							$subject .= "          <img src='../image/btn_edit.png' style='cursor:pointer;' onClick='_modifyDiary(".$day.",".$index.");'>";
							$subject .= "          <img src='../image/btn_del.png' style='cursor:pointer;' onClick='_clearDiary(".$day.",".$index.");'>";
							$subject .= "        </div>";
							$subject .= "      </div>";
							$subject .= "      <div>".$tempSubject."</div>";
							$subject .= "      <div id='checkDuplicate_".$day."_".$index."' style='display:none;'>중복</div>";
							$subject .= "    </td>";
							$subject .= "  </tr>";
							$subject .= "</table>";
							$subject .= "</div>";
						}else{
						}
						?>
						<input name="mSubject_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$subject;?>">
						<?
					}

					if($j == 0){
						echo "<font color='#FF0000'>".$day."</font>";
					}else if($j == 6){
						echo "<font color='#0000FF'>".$day."</font>";
					}else{
						echo "<font color='#000000'>".$day."</font>";
					}

					if ($_POST["sugaCode"] != ""){
						if ($today <= $iljung[$day]["mDate"]){
							if ($subject != ""){
								$mUse = "Y";
							}else{
								$mUse = "N";
								$subject  = "<div id='".$subjectID."_".$index."'></div>";
								$subject .= "<div id='checkDuplicate_".$day."_".$index."' style='display:none;'>중복</div>";
							}
							echo '<br><img src="../image/btn_add.png" style="cursor:pointer;" onClick="_addDiary(\''.$_POST['mCode'].'\',\''.$_POST['mKind'].'\',\''.$_POST['mKey'].'\',\''.$day.'\',\''.$iljung[$day]["mDate"].'\',\''.$j.'\');">';
						}else{
							$mUse = "N";
						}
						$dayIndex[$day]++;
					}else{
						$mUse = "N";
					}
					?><input name="mUse_<?=$day;?>_<?=$index;?>" type="hidden" value="<?=$mUse;?>"><?
					$day++;
				}
				echo "</td>";
				echo "<td style='width:108px; text-align:left; vertical-align:top; line-height:1.3em;' id='".$subjectID."'>";
				echo $subject;
				echo "</td>";
			}
			echo "</tr>";
		}
		//echo "<br><br><br>";
		//print_r($iljung[2]);
	?>
</table>
<input name="mLastDay" type="hidden" value="<?=$lastDay;?>">
<div id="addCalendar" style="display:;"></div>
<?
	$conn->row_free();

	include("../inc/_footer.php");
?>

<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$mCode    = $_POST['mCode'];
	$mKind    = $_POST['mKind'];
	$mSugupja = $_POST['mJumin'];
	$mYear    = $_POST["mYear"];
	$mMonth   = $_POST["mMonth"];

	if (!is_numeric($mSugupja)){
		$mSugupja = $ed->de($mSugupja);
	}

	switch($mMonth){
	case "01":
		$mYear = intVal($mYear) - 1;
		$mMonth = "12";
		break;
	default:
		$mMonth --;
		$mMonth = (intVal($mMonth) < 10 ? "0" : "").$mMonth;
	}
	$mYM = $mYear.$mMonth;
?>
<style>
.myTable thead th{
	font-size:9pt;
	height:30px;
	color:#194685;
	padding:0px;
	text-align:center;
	font-weight:bold;
	background:#a3bee3;
	border-left:1px solid #194685;
	border-right:1px solid #194685;
}
.myTable tbody td{
	font-size:9pt;
	height:30px;
	color:#000000;
	padding:0px;
	text-align:left;
	border-top:1px solid #194685;
	border-left:1px solid #194685;
	border-right:1px solid #194685;
	border-bottom:1px solid #194685;
}
</style>
<table class="myTable" style="width:100%; height:100%; border-bottom:0;">
<colGroup>
	<col width="5%">
	<col width="23%">
	<col width="20%">
	<col width="22%">
	<col width="23%">
	<col width="7%">
</colGroup>
<thead>
	<tr>
		<th style="">No.</th>
		<th style="">서비스</th>
		<th style="">방문시간</th>
		<th style="">제공요일</th>
		<th style="">담당요양사</th>
		<th style="">
			<input type="button" value="닫기" class="btnSmall2" onFocus="this.blur();" onClick="_patternClose(pattern);">
		</th>
	</tr>
</thead>
<tbody>
<?
	$sql = "select *
			  from t03pattern
			 where t03_ccode = '$mCode'
			   and t03_mkind = '$mKind'
			   and t03_jumin = '$mSugupja'
			   and t03_datetime like '$mYM%'
			 order by t03_sugup_fmtime, t03_sugup_totime";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$no = $i+1;
			$sugaName = $row["t03_suga_name"];
			$time = subStr($row["t03_sugup_fmtime"],0,2).":".subStr($row["t03_sugup_fmtime"],2,2)."~".subStr($row["t03_sugup_totime"],0,2).":".subStr($row["t03_sugup_totime"],2,2)."[".$row["t03_sugup_soyotime"]."분]";
			$weekDay1 = ($row["t03_week_day1"] == "Y" ? "월" : "");
			$weekDay2 = ($row["t03_week_day2"] == "Y" ? "화" : "");
			$weekDay3 = ($row["t03_week_day3"] == "Y" ? "수" : "");
			$weekDay4 = ($row["t03_week_day4"] == "Y" ? "목" : "");
			$weekDay5 = ($row["t03_week_day5"] == "Y" ? "금" : "");
			$weekDay6 = ($row["t03_week_day6"] == "Y" ? "<font color='#0000ff'>토</font>" : "");
			$weekDay0 = ($row["t03_week_day0"] == "Y" ? "<font color='#ff0000'>일</font>" : "");

			$weekDay = trim($weekDay1.($weekDay1 != "" ? ", " : "").$weekDay2.($weekDay2 != "" ? ", " : "").$weekDay3.($weekDay3 != "" ? ", " : "").$weekDay4.($weekDay4 != "" ? ", " : "").$weekDay5.($weekDay5 != "" ? ", " : "").$weekDay6.($weekDay6 != "" ? ", " : "").$weekDay0);

			$yoy = $row["t03_yoy_name1"].($row["t03_yoy_name2"] != "" ? " / " : "").$row["t03_yoy_name2"];

			$dt = $row["t03_datetime"];

			if (subStr($weekDay, strLen($weekDay) - 1, 1) == ","){
				$weekDay = subStr($weekDay, 0, strLen($weekDay) - 1);
			}

			if (empty($weekDay)){
				$weekDay = '일자등록';
			}

			echo '<tr>
					<td style=\'text-align:center;\'>'.$no.'</td>
					<td style=\'padding-left:5px;\'>'.$sugaName.'</td>
					<td style=\'padding-left:5px;\'>'.$time.'</td>
					<td style=\'padding-left:5px;\'>'.$weekDay.'</td>
					<td style=\'padding-left:5px;\'>'.$yoy.'</td>
					<td style=\'text-align:center;\'>';

			if ($mKind == '0'){
				echo '<input type=\'button\' value=\'입력\' class=\'btnSmall2\' onFocus=\'this.blur();\' onClick=\'_patternInput(pattern,"'.$i.'");\'>';
			}else{
				echo '<input type=\'button\' value=\'입력\' class=\'btnSmall2\' onFocus=\'this.blur();\' onClick=\'_putPattern("pattern","'.$i.'");\'>';
			}

			echo '	</td>
				  </tr>';

			if ($mKind == '0'){
				echo "<input name='p_svc_subcode[]' type='hidden' value='".$row["t03_svc_subcode"]."'>";
				echo "<input name='p_svc_subcd[]' type='hidden' value='".$row["t03_svc_subcd"]."'>";
				echo "<input name='p_car_no[]' type='hidden' value='".$row["t03_car_no"]."'>";
				echo "<input name='p_sugup_fmtime[]' type='hidden' value='".$row["t03_sugup_fmtime"]."'>";
				echo "<input name='p_sugup_totime[]' type='hidden' value='".$row["t03_sugup_totime"]."'>";
				echo "<input name='p_sugup_soyotime[]' type='hidden' value='".$row["t03_sugup_soyotime"]."'>";
				echo "<input name='p_family_gbn[]' type='hidden' value='".$row["t03_family_gbn"]."'>";
				echo "<input name='p_bipay_gbn[]' type='hidden' value='".$row["t03_bipay_gbn"]."'>";
				echo "<input name='p_week_day1[]' type='hidden' value='".$row["t03_week_day1"]."'>";
				echo "<input name='p_week_day2[]' type='hidden' value='".$row["t03_week_day2"]."'>";
				echo "<input name='p_week_day3[]' type='hidden' value='".$row["t03_week_day3"]."'>";
				echo "<input name='p_week_day4[]' type='hidden' value='".$row["t03_week_day4"]."'>";
				echo "<input name='p_week_day5[]' type='hidden' value='".$row["t03_week_day5"]."'>";
				echo "<input name='p_week_day6[]' type='hidden' value='".$row["t03_week_day6"]."'>";
				echo "<input name='p_week_day0[]' type='hidden' value='".$row["t03_week_day0"]."'>";
				echo "<input name='p_week_use1[]' type='hidden' value='".$row["t03_week_use1"]."'>";
				echo "<input name='p_week_use2[]' type='hidden' value='".$row["t03_week_use2"]."'>";
				echo "<input name='p_week_use3[]' type='hidden' value='".$row["t03_week_use3"]."'>";
				echo "<input name='p_week_use4[]' type='hidden' value='".$row["t03_week_use4"]."'>";
				echo "<input name='p_week_use5[]' type='hidden' value='".$row["t03_week_use5"]."'>";
				echo "<input name='p_week_use6[]' type='hidden' value='".$row["t03_week_use6"]."'>";
				echo "<input name='p_week_use0[]' type='hidden' value='".$row["t03_week_use0"]."'>";
				echo "<input name='p_yoy_jumin1[]' type='hidden' value='".$ed->en($row["t03_yoy_jumin1"])."'>";
				echo "<input name='p_yoy_jumin2[]' type='hidden' value='".$ed->en($row["t03_yoy_jumin2"])."'>";
				echo "<input name='p_yoy_jumin3[]' type='hidden' value='".$row["t03_yoy_jumin3"]."'>";
				echo "<input name='p_yoy_jumin4[]' type='hidden' value='".$row["t03_yoy_jumin4"]."'>";
				echo "<input name='p_yoy_jumin5[]' type='hidden' value='".$row["t03_yoy_jumin5"]."'>";
				echo "<input name='p_yoy_name1[]' type='hidden' value='".$row["t03_yoy_name1"]."'>";
				echo "<input name='p_yoy_name2[]' type='hidden' value='".$row["t03_yoy_name2"]."'>";
				echo "<input name='p_yoy_name3[]' type='hidden' value='".$row["t03_yoy_name3"]."'>";
				echo "<input name='p_yoy_name4[]' type='hidden' value='".$row["t03_yoy_name4"]."'>";
				echo "<input name='p_yoy_name5[]' type='hidden' value='".$row["t03_yoy_name5"]."'>";
				echo "<input name='p_yoy_ta1[]' type='hidden' value='".$row["t03_yoy_ta1"]."'>";
				echo "<input name='p_yoy_ta2[]' type='hidden' value='".$row["t03_yoy_ta2"]."'>";
				echo "<input name='p_yoy_ta3[]' type='hidden' value='".$row["t03_yoy_ta3"]."'>";
				echo "<input name='p_yoy_ta4[]' type='hidden' value='".$row["t03_yoy_ta4"]."'>";
				echo "<input name='p_yoy_ta5[]' type='hidden' value='".$row["t03_yoy_ta5"]."'>";
				echo "<input name='p_visit_chk[]' type='hidden' value='".$row["t03_visit_chk"]."'>";
				echo "<input name='p_visit_amt[]' type='hidden' value='".$row["t03_visit_amt"]."'>";
				echo "<input name='p_sudang_yul1[]' type='hidden' value='".$row["t03_sudang_yul1"]."'>";
				echo "<input name='p_sudang_yul2[]' type='hidden' value='".$row["t03_sudang_yul2"]."'>";
				echo "<input name='p_price_s[]' type='hidden' value='".$row["t03_price_s"]."'>";
				echo "<input name='p_price_e[]' type='hidden' value='".$row["t03_price_e"]."'>";
				echo "<input name='p_price_n[]' type='hidden' value='".$row["t03_price_n"]."'>";
				echo "<input name='p_price_t[]' type='hidden' value='".$row["t03_price_t"]."'>";
				echo "<input name='p_suga_code[]' type='hidden' value='".$row["t03_suga_code"]."'>";
				echo "<input name='p_suga_name[]' type='hidden' value='".$row["t03_suga_name"]."'>";
				echo "<input name='p_gubun_e[]' type='hidden' value='".$row["t03_gubun_e"]."'>";
				echo "<input name='p_gubun_n[]' type='hidden' value='".$row["t03_gubun_n"]."'>";
				echo "<input name='p_time_e[]' type='hidden' value='".$row["t03_time_e"]."'>";
				echo "<input name='p_time_n[]' type='hidden' value='".$row["t03_time_n"]."'>";

				for($j=1; $j<=31; $j++){
					echo '<input name=\'p_svc_dt_'.$j.'[]\' type=\'hidden\' value=\''.$row['t03_days'][$j-1].'\'>';
				}
			}else{
				$str  = '<input id=\'pattern_'.$i.'\' name=\'pattern[]\' type=\'\' value=\'';
				$str .=  'svcCD='.$row['t03_svc_subcd'];
				$str .= '&carNo='.$row['t03_car_no'];
				$str .= '&from=' .$row['t03_sugup_fmtime'];
				$str .= '&to='   .$row['t03_sugup_totime'];
				$str .= '&proc=' .$row['t03_sugup_soyotime'];
				$str .= '&bipay='.$row['t03_bipay_gbn'];

				$str .= '&weekDay1='.$row['t03_week_day1'];
				$str .= '&weekDay2='.$row['t03_week_day2'];
				$str .= '&weekDay3='.$row['t03_week_day3'];
				$str .= '&weekDay4='.$row['t03_week_day4'];
				$str .= '&weekDay5='.$row['t03_week_day5'];
				$str .= '&weekDay6='.$row['t03_week_day6'];
				$str .= '&weekDay0='.$row['t03_week_day0'];

				$str .= '&weekUse1='.$row['t03_week_use1'];
				$str .= '&weekUse2='.$row['t03_week_use2'];
				$str .= '&weekUse3='.$row['t03_week_use3'];
				$str .= '&weekUse4='.$row['t03_week_use4'];
				$str .= '&weekUse5='.$row['t03_week_use5'];
				$str .= '&weekUse6='.$row['t03_week_use6'];
				$str .= '&weekUse0='.$row['t03_week_use0'];

				$str .= '&memCD1='.$ed->en($row["t03_yoy_jumin1"]);
				$str .= '&memCD2='.$ed->en($row["t03_yoy_jumin2"]);

				$str .= '&memNM1='.$row['t03_yoy_name1'];
				$str .= '&memNM2='.$row['t03_yoy_name2'];

				$str .= '&memTA1='.$row['t03_yoy_ta1'];
				$str .= '&memTA2='.$row['t03_yoy_ta2'];

				$str .= '&visitYN=' .$row['t03_visit_chk'];
				$str .= '&visitAmt='.$row['t03_visit_amt'];

				$str .= '&sudangRate1='.$row['t03_sudang_yul1'];
				$str .= '&sudangRate2='.$row['t03_sudang_yul2'];

				$str .= '&suga='      .$row['t03_price_s'];
				$str .= '&sugaEveing='.$row['t03_price_s'];
				$str .= '&sugaNight=' .$row['t03_price_s'];
				$str .= '&sugaTot='   .$row['t03_price_s'];

				$str .= '&sugaCD='.$row['t03_suga_code'];
				$str .= '&sugaNM='.$row['t03_suga_name'];

				$str .= '&gubunEveing='.$row['t03_gubun_e'];
				$str .= '&gubunNight=' .$row['t03_gubun_n'];
				$str .= '&timeEveing=' .$row['t03_time_e'];
				$str .= '&timeNight='  .$row['t03_time_n'];

				$str .= 'svcDT=';

				for($j=1; $j<=31; $j++){
					$str .= $row['t03_days'][$j-1];
				}

				$str .= '\'>';

				echo $str;
			}
		}
	}else{
		echo "
			<tr>
				<td style='text-align:center;' colspan='6'>::등록된 패턴이 없습니다.::</td>
			</tr>
			 ";
	}
	$conn->row_free();
?>
</tbody>
</table>
<?
	include_once("../inc/_footer.php");
?>
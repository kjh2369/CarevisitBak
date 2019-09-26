<?
	include('../inc/_header.php');
	include('../inc/_ed.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
	$mDay = $_POST['mDay'];
?>
<table style="width:100%;">
<tr>
	<td class="title">일별마감(<?=$mYear.'년'.$mMonth.'월'.$mDay.'일';?>)</td>
</tr>
</table>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr>
	<th style="width:7%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">수급자</th>
	<th style="width:9%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스</th>
	<th style="width:14%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">계획시간</th>
	<th style="width:25%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">실적</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">제공서비스</th>
	<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">실적<br>급여액</th>
	<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">계획<br>급여액</th>
	<th style="width:8%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">담당자</th>
	<th style="width:6%; height:24px; padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">비고</th>
</tr>
<tr>
	<th style="width:6%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:1px solid #e5e5e5;">일자</th>
	<th style="width:19%; padding:0px; text-align:center; border-right:1px solid #e5e5e5; border-top:1px solid #e5e5e5;">시간</th>
</tr>
<?
	$sql = "select t01_jumin as sugupjaCode"
		 . ",      m03_name as sugupjaName"
		 . ",      t01_svc_subcode as serviceCode"
		 . ",      t01_sugup_fmtime as sugupFromTime"
		 . ",      t01_sugup_totime as sugupToTime"
		 . ",      t01_sugup_soyotime as sugupSoyoTime"
		 . ",      concat(substring(t01_sugup_fmtime, 1, 2), ':', substring(t01_sugup_fmtime, 3, 2), '-', substring(t01_sugup_totime, 1, 2), ':', substring(t01_sugup_totime, 3, 2), '(', t01_sugup_soyotime, '분)') as sugupTime"
		 . ",      t01_conf_date as workDate"
		 . ",      t01_conf_fmtime as workFromTime"
		 . ",      t01_conf_totime as workToTime"
		 . ",      case when t01_svc_subcode = '200' then ifnull(t01_conf_soyotime, 0) - (ifnull(t01_conf_soyotime, 0) mod 30) else ifnull(t01_conf_soyotime, 0) end as workSoyoTime"
		 . ",      t01_suga_code1 as sugaCode"
		 . ",      t01_suga as sugaPrice"
		 . ",      t01_yoyangsa_id1 as yoyangsaCode1"
		 . ",      t01_yoyangsa_id2 as yoyangsaCode2"
		 . ",      t01_yoyangsa_id3 as yoyangsaCode3"
		 . ",      t01_yoyangsa_id4 as yoyangsaCode4"
		 . ",      t01_yoyangsa_id5 as yoyangsaCode5"
		 . ",      t01_yname1 as yoyangsaName1"
		 . ",      t01_yname2 as yoyangsaName2"
		 . ",      t01_yname3 as yoyangsaName3"
		 . ",      t01_yname4 as yoyangsaName4"
		 . ",      t01_yname5 as yoyangsaName5"
		 . ",     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = left(t01_sugup_date, 6)) as confCount"
		 . "  from t01iljung"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_sugup_date = '".$mYear.$mMonth.$mDay
		 . "'  and t01_del_yn = 'N'"
		 . " order by t01_jumin, t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';

			if ($sugupjaCode != $row['sugupjaCode']){
				$sugupjaCode  = $row['sugupjaCode'];

				echo '<td>'.$row['sugupjaName'].'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;"></td>';
			}

			if ($serviceCode != $sugupjaCode.$row['serviceCode']){
				$serviceCode  = $sugupjaCode.$row['serviceCode'];

				switch($row['serviceCode']){
				case '200':
					$serviceName = '방문요양';
					break;
				case '500':
					$serviceName = '방문목욕';
					break;
				case '800':
					$serviceName = '방문간호';
					break;
				}
				echo '<td>'.$serviceName.'</td>';
			}else{
				echo '<td style="border-top:1px solid #ffffff;"></td>';
			}
			echo '<td>'.$row['sugupTime'].'</td>';

			$workDate = subStr($row['workDate'], 4, 2).'/'.subStr($row['workDate'], 6, 2);
			echo '<td><input name="workDate[]" type="text" value="'.$workDate.'" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName(\'workFmTime[]\')['.$i.'].focus();}" onFocus="__replace(this, \'/\', \'\');" onBlur="__styleDate(this);"></td>';
			echo '<td>';

			$workFromTime = subStr($row['workFromTime'], 0, 2).':'.subStr($row['workFromTime'], 2, 2);
			$workToTime = subStr($row['workToTime'], 0, 2).':'.subStr($row['workToTime'], 2, 2);
			echo '<input name="workFmTime[]" type="text" value="'.$workFromTime.'" maxlength="4" class="number" style="text-align:center; width:40px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length > 3){document.getElementsByName(\'workToTime[]\')['.$i.'].focus();}" onFocus="__replace(this, \':\', \'\');" onBlur="__styleTime(this);" onChange="setWorkPorcTime('.$i.');">';
			echo '<input name="workToTime[]" type="text" value="'.$workToTime.'" maxlength="4" class="number" style="text-align:center; width:40px; margin-left:2px; margin-right:2px;" onKeyDown="__onlyNumber(this);" onKeyUp="'.$nextIndex.'" onFocus="__replace(this, \':\', \'\');" onBlur="__styleTime(this);" onChange="setWorkPorcTime('.$i.');">';

			if ($row['serviceCode'] == '200'){
				echo '<input name="workSoyoTime[]" type="text" value="'.$row['workSoyoTime'].'" maxlength="4" class="number" style="text-align:center; width:40px; background-color:#eeeeee;" onFocus="this.blur();" readOnly>분';
			}else{
				echo '<input name="workSoyoTime[]" type="text" value="'.$row['workSoyoTime'].'" class="number" style="text-align:center; width:40px; background-eeeeee:#;" disabled="true">분';
			}
			echo '</td>';

			// 수가정보
			$sugaCode = $row['sugaCode']; //수가코드
			$sugaName = $conn->get_suga($mCode, $sugaCode); //수가명
			$sugaValue = $row['sugaPrice']; //수가단가
			$sumPlanTime += $row['sugupSoyoTime']; //총계획시간

			if ($row['serviceCode'] == '200'){
				// 요양인 경우 실행시간이 없으면 수가 및 금액을 0처리한다.
				if ($row['workSoyoTime'] == '0'){
					$sugaName = '';
					$sugaValue = 0;
				}
			}else{
				// 요양이 아닌 경우 실행시간이 없으면 수가 및 금액을 0처리한다.
				if (strLen($row['workFromTime']) != 4 or strLen($row['workToTime']) != 4){
					$sugaName = '';
					$sugaValue = 0;
				}
			}

			// 제공서비스
			echo '<td style="text-align:left;" id="sugaName[]" title="'.$sugaName.'">'.left($sugaName, 8).'</td>';

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
			<?
		}
	}else{
		echo '<tr>';
		echo '<td style="padding:0; text-align:center;" colspan="10">::검색된 데이타가 없습니다.::</td>';
		echo '</tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>
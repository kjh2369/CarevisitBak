<?
	$subject   = '';
	$contents  = '';
	$fromDate  = $myF->dateStyle($date);
	$fromTime  = floor($myF->time2min('09:00') / 30);
	$toDate    = $fromDate;
	$toTime    = $fromTime + 2;
	$fulltime  = 'Y';
	$btnCancel = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_regPopupClose();\'>닫기</button></span>';

	if ($time != ''){
		$fromTime	= $time / 30;
		$toTime		= ($time + 60) / 30;
		$fulltime	= 'N';
	}

	if ($seq > 0){
		$sql = 'select min(cld_dt) as min_dt
				,      max(cld_dt) as max_dt
				,      cld_from as from_time
				,      cld_to as to_time
				,      cld_fulltime as fulltime
				,      cld_subject as subject
				,      cld_contents as contents
				,      cld_reg_nm as reg_nm
				  from calendar
				 where org_no   = \''.$code.'\'
				   and cld_yymm = \''.$yymm.'\'
				   and cld_seq  = \''.$seq.'\'
				   and del_flag = \'N\'
				 group by cld_from, cld_to, cld_fulltime, cld_subject, cld_reg_nm';

		$row = $conn->get_array($sql);

		$subject  = stripslashes($row['subject']);
		$contents = stripslashes($row['contents']);
		$fromDate = $row['min_dt'];
		$toDate   = $row['max_dt'];
		$fulltime = $row['fulltime'];

		$fromTime	= $myF->time2min($row['from_time']) / 30;
		$toTime		= $myF->time2min($row['to_time']) / 30;

		$btnCancel = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_regPopupClose();\'>취소</button></span>';
	}


	echo '<div id=\'this_popup\' style=\'width:460px; height:240px; background:url("../image/calendar_bg.gif") no-repeat;\'>

			<div>
				<div style=\'float:left; width:auto; font-weight:bold;\'><div style=\'width:auto; margin-top:20px; margin-left:30px;\'>일정등록</div></div>
				<div style=\'float:right; width:auto;\'><img src=\'../image/close.gif\' style=\'margin-top:10px; margin-right:10px;\' onclick=\'_regPopupClose();\'></div>
			</div>

			<div style=\'margin-left:30px; margin-top:15px;\'>
				<div style=\'float:left; width:auto;\'>제목</div>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:1px;\'><input name=\'regSubject\' type=\'text\' value=\''.$subject.'\' style=\'width:350px;\'></div>
			</div>

			<div style=\'margin-left:30px; margin-top:5px;\'>
				<div style=\'float:left; width:auto;\'>일시</div>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:1px;\'>
					<input name=\'regFromDate\' type=\'text\' value=\''.$fromDate.'\' class=\'date\' style=\'height:21px;\'>
					<select name=\'regFromTime\' style=\'width:auto;\'>'.getTimeList($fromTime).'</select> ~
					<input name=\'regToDate\' type=\'text\' value=\''.$toDate.'\' class=\'date\' style=\'height:21px;\'>
					<select name=\'regToTime\' style=\'width:auto;\'>'.getTimeList($toTime).'</select>
					<input name=\'regFullTime\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' onclick=\'_regFullTimeSet();\' '.($fulltime == 'Y' ? 'checked' : '').'>종일
				</div>
			</div>';

	if ($gDomain == 'carevisit.net' && $_SESSION['userLevel'] == 'A'){
		echo '<div style=\'margin-left:30px; margin-top:5px;\'>
				<div style=\'float:left; width:auto;\'>반복</div>
				<div style=\'float:left; width:auto; margin-left:20px; margin-top:1px;\'>
					<select name="cboLoopGbn" style="width:auto;">
						<option value="">반복없음</option>
						<option value="A">매주</option>
						<option value="1">매달 1주</option>
						<option value="2">매달 2주</option>
						<option value="3">매달 3주</option>
						<option value="4">매달 4주</option>
					</select>
					<label><input name="chkWeekly1" type="checkbox" value="1" class="checkbox">월</label>
					<label><input name="chkWeekly2" type="checkbox" value="2" class="checkbox">화</label>
					<label><input name="chkWeekly3" type="checkbox" value="3" class="checkbox">수</label>
					<label><input name="chkWeekly4" type="checkbox" value="4" class="checkbox">목</label>
					<label><input name="chkWeekly5" type="checkbox" value="5" class="checkbox">금</label>
					<label><input name="chkWeekly6" type="checkbox" value="6" class="checkbox"><span style="color:BLUE;">토</span></label>
					<label><input name="chkWeekly0" type="checkbox" value="0" class="checkbox"><span style="color:RED;">일</span></label>
				</div>
			</div>';
	}

	echo '	<div style=\'margin-left:30px; margin-top:5px;\'>
				<div style=\'float:left; width:auto;\'>내용</div>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:1px;\'><textarea name=\'regContents\' style=\'width:350px; height:60px;\'>'.$contents.'</textarea></div>
			</div>

			<div style=\'margin-top:10px; text-align:center;\'>
				<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_regSave();\'>저장</button></span> '.$btnCancel.'
			</div>

		  </div>';
?>
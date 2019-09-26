<?
	$sql = 'select min(cld_dt) as min_dt
			,      max(cld_dt) as max_dt
			,      cld_from as from_time
			,      cld_to as to_time
			,      timediff(cld_to, cld_from) as proctime
			,      cld_fulltime as fulltime
			,      cld_subject as subject
			,      cld_contents as contents
			,      cld_reg_nm as reg_nm
			,	   cld_reg_id as reg_id
			  from calendar
			 where org_no   = \''.$code.'\'
			   and cld_yymm = \''.$yymm.'\'
			   and cld_seq  = \''.$seq.'\'
			   and del_flag = \'N\'
			 group by cld_from, cld_to, cld_fulltime, cld_subject, cld_reg_nm';

	$row = $conn->get_array($sql);

	if ($row['fulltime'] == 'Y'){
		if ($row['min_dt'] == $row['max_dt']){
			$datetime = $myF->dateStyle($row['min_dt'], '.');
		}else{
			$datetime = $myF->dateStyle($row['min_dt'], '.').' ~ '.$myF->dateStyle($row['max_dt'], '.');
		}
	}else{
		$temptime = explode(':', $row['proctime']);
		$proctime = (intval($temptime[0]) > 0 ? intval($temptime[0]).'시간 ' : '').(intval($temptime[1]) > 0 ? intval($temptime[1]).'분' : '');
		$datetime = substr($row['from_time'], 0, 5).' ~ '.substr($row['to_time'], 0, 5).' ('.$proctime.')';
	}



	echo '<div id=\'this_popup\' style=\'width:460px; height:240px; background:url("../image/calendar_bg.gif") no-repeat;\'>

			<div>
				<div style=\'float:left; width:auto; font-weight:bold;\'>
					<div style=\'float:left; width:auto; margin-left:10px; margin-top:12px;\'><img src=\'../image/bg_icon_1.gif\'></div>
					<div class=\'nowrap\' style=\'float:left; width:85%; margin-left:5px; margin-top:8px;\'>'.$row['subject'].'</span>
				</div>
				<div style=\'float:right; width:auto;\'><img src=\'../image/close.gif\' style=\'margin-top:10px; margin-right:10px;\' onclick=\'_regPopupClose();\'></div>
			</div>

			<div style=\'margin-left:10px; margin-top:5px; margin-right:10px; border-top:1px solid #cccccc;\'>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:10px;\'>일시</div>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:10px;\'>'.$datetime.'</div>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:10px;\'>/ 작성자 : '.$row['reg_nm'].'</div>
			</div>

			<div style=\'margin-left:10px; margin-top:-10px; margin-right:10px;\'>
				<div style=\'float:left; width:auto; margin-left:10px; margin-top:10px;\'>내용</div>
				<div style=\'float:left; width:89%; height:100px; margin-left:10px; margin-top:10px; padding-left:5px; overflow-x:hidden; overflow-y:scroll; border:1px solid #cccccc; background-color:#ffffff;\'>'.nl2br(stripslashes($row['contents'])).'</div>
			</div>

			<div style=\'margin-top:10px; text-align:center;\'>';

	if ($_SESSION['userCode'] == $row['reg_id']){
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_modifyCalendar("'.$code.'","'.$yymm.'","'.$seq.'","'.$no.'","popup");\'>수정</button></span>
			  <span class=\'btn_pack m\'><button type=\'button\' onclick=\'_deleteCalendar("'.$code.'","'.$yymm.'","'.$seq.'","'.$no.'");\'>삭제</button></span>';
	}

	echo '		<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_regPopupClose();\'>닫기</button></span>
			</div>

		  </div>';
?>
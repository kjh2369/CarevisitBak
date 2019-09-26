<?
	if (!isset($code)) include_once('../inc/_http_home.php');

	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('iljung_config.php');

	if ($mode == 'IN'){
		$temp_date = $year.$month;
	}else{
		$temp_date = $year.$month.$day;
	}

	$wrt_mode = $myF->get_iljung_mode();

	$closing_yn  = $conn->get_closing_act($code, $year.$month); // 마감처리여부
	$flagHistory = ($mode == "IN" ? "Y" : "N");               // 히스토리 관리 플래그

	$sql = "select m00_kupyeo_1"
		 . ",      m00_kupyeo_2"
		 . ",      m00_kupyeo_3"
		 . ",      m00_muksu_yul1"
		 . ",      m00_muksu_yul2"
		 . ",      m00_cont_date"
		 . "  from m00center"
		 . " where m00_mcode = '".$code
		 . "'  and m00_mkind = '".$kind
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$svcSubCode[1] = $row['m00_kupyeo_1'];
	$svcSubCode[2] = $row['m00_kupyeo_2'];
	$svcSubCode[3] = $row['m00_kupyeo_3'];
	$sudangYul[1]  = $row['m00_muksu_yul1'];
	$sudangYul[2]  = $row['m00_muksu_yul2'];
	$centerStartDate = subStr($row['m00_cont_date'], 0, 6);
	$conn->row_free();

	if ($svcSubCode[1] == 'Y'){
		$visit[0] = 'true';
		$visit[1] = '#eeeeee';
	}else{
		if ($svcSubCode[2] != 'Y' and $svcSubCode[3] != 'Y'){
			$visit[0] = 'true';
			$visit[1] = '#eeeeee';
		}else{
			$visit[0] = 'false';
			$visit[1] = '#ffffff';
		}
	}

	$sugupjaLevel = $conn->get_sugupja_level($code, $kind, $jumin); //수급자 등급

	$sql = "select m03_yoyangsa1"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1)"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa2"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2)"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa3"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa4"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",      m03_yoyangsa5"
		 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa2 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa3 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa4 and m02_ygoyong_stat = '1')"
		 . ",     (select m02_ygupyeo_kind from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa5 and m02_ygoyong_stat = '1')"
		 . ",      m03_familycare
		    ,      m03_partner
			,      m03_stat_nogood
			,      m03_bath_add_yn"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$code
		 . "'  and m03_mkind = '".$kind
		 . "'  and m03_key   = '".$key
		 . "'";

	$conn->query($sql);
	$row = $conn->fetch();
	$yoy1   = $row[0];
	$yoyNm1 = $row[1];
	$yoyTA1 = $row[2];
	$yoy2   = ''; //$row[3];
	$yoyNm2 = ''; //$row[4];
	$yoyTA2 = ''; //$row[5];
	$yoy3   = ''; //$row[6];
	$yoyNm3 = ''; //$row[7];
	$yoyTA3 = ''; //$row[8];
	$yoy4   = ''; //$row[9];
	$yoyNm4 = ''; //$row[10];
	$yoyTA4 = ''; //$row[11];
	$yoy5   = ''; //$row[12];
	$yoyNm5 = ''; //$row[13];
	$yoyTA5 = ''; //$row[14];
	//$chkFamilyCare = ($row['m03_familycare']=='Y'?'checked':'');

	##################################################################
	# 주요양보호사 배우자이거나 수급자(65세이상)가 상태이상인 경우
	# 1일 90분 31일 가능하며
	# 그렇지 않을 경우 1일 60분 20일만 가능하다.
	##################################################################
	$member_age = $myF->issToAge($yoy1);

	// 동거가족 제한
	if ($temp_date >= '201108'){
		if (($row['m03_partner'] == 'Y' && $member_age >=65) || $row['m03_stat_nogood'] == 'Y'){
			$family_min = 90;
			$family_cnt = $myF->lastDay(substr($temp_date, 0, 4), substr($temp_date, 4, 2));
		}else{
			$family_min = 60;
			$family_cnt = 20;
		}
		$care_limit_cnt = 1;

		$back_family_min = 60;
		$back_family_cnt = 20;
	}else{
		$family_min     = 24 * 60;
		$family_cnt     = 31;
		$care_limit_cnt = 99;

		$back_family_min = $family_min;
		$back_family_cnt = $family_cnt;
	}

	// 주간 목욕횟수 제한
	if ($temp_date >= '201107'){
		if ($row['m03_bath_add_yn'] == 'Y')
			$bath_week_cnt = 99;
		else
			$bath_week_cnt = 1;
	}else{
		$bath_week_cnt = 99;
	}

	$yoyKN1 = $row[15];
	$yoyKN2 = $row[16];
	$yoyKN3 = $row[17];
	$yoyKN4 = $row[18];
	$yoyKN5 = $row[19];
	$conn->row_free();

	if ($mode == 'IN' || $mode == 'PATTERN'){
		$visitSudang = 0;
	}

	ob_start();

	########################################################
	#
	# 일정등록
	#
	########################################################

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-bottom:none;'.($wrt_mode == 1 ? '' : 'margin-top:-2px;').'\'>';
	echo '	<colgroup>
				<col width=\'170px\'>
				<col width=\'90px\'>
				<col width=\'100px\'>
				<col width=\'30px\'>
				<col width=\'120px\'>
				<col width=\'90px\'>
				<col width=\'90px\'>
				<col width=\'80px\'>
				<col>
			</colgroup>';
	echo '	<tbody>';

	if ($wrt_mode == 1){
		echo '<tr>';
		echo '	<th class=\'head bold\' colspan=\'9\'>'.$kind_nm.'</th>';
		echo '</tr>';
	}

	echo '		<tr>
					<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\'>제공서비스</th>
					<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\'>비용구분</th>
					<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\'>요양보호사</th>
					<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\' colspan=\'2\'>방문시간</th>
					<th class=\'head\' style=\''.__BORDER_T__.'\'>소요시간</th>
					<th class=\'head\' style=\''.__BORDER_T__.'\'>입욕선택</th>
					<th class=\'head\' style=\''.__BORDER_T__.__BORDER_R__.'\'>차량선택</th>
					<th class=\'head\' style=\''.__BORDER_T__.'\'></th>
				</tr>';
	echo '		<tr>';
	echo '			<td rowspan=\'2\' style=\''.__BORDER_B__.__BORDER_R__.' line-height:1em;\'>
						<input name=\'svcSubCode\' type=\'radio\' class=\'radio\' value=\'200\' '.($svcSubCode[1] != 'Y' ? 'disabled=true' : '').' onClick=\'_setSvc1(); _set_bipay_pay();\' checked>'.($svcSubCode[1] == 'Y' ? '<a href="#" onclick="if(!document.getElementsByName(\'svcSubCode\')[0].disabled){document.getElementsByName(\'svcSubCode\')[0].checked=true; _setSvc1(); _set_bipay_pay();}">' : '').'요양'.($svcSubCode[1] == 'Y' ? '</a>' : '').'
						<input name=\'svcSubCode\' type=\'radio\' class=\'radio\' value=\'500\' '.($svcSubCode[2] != 'Y' ? 'disabled=true' : '').' onClick=\'_setSvc2(); _set_bipay_pay();\'>'.($svcSubCode[2] == 'Y' ? '<a href="#" onclick="if(!document.getElementsByName(\'svcSubCode\')[1].disabled){document.getElementsByName(\'svcSubCode\')[1].checked=true; _setSvc2(); _set_bipay_pay();}">' : '').'목욕'.($svcSubCode[2] == 'Y' ? '</a>' : '').'
						<input name=\'svcSubCode\' type=\'radio\' class=\'radio\' value=\'800\' '.($svcSubCode[3] != 'Y' ? 'disabled=true' : '').' onClick=\'_setSvc3(); _set_bipay_pay();\'>'.($svcSubCode[3] == 'Y' ? '<a href="#" onclick="if(!document.getElementsByName(\'svcSubCode\')[2].disabled){document.getElementsByName(\'svcSubCode\')[2].checked=true; _setSvc3(); _set_bipay_pay();}">' : '').'간호'.($svcSubCode[3] == 'Y' ? '</a>' : '').'
					</td>';
	echo '			<td style=\''.__BORDER_R__.'\'>
						<input name=\'togeUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onClick=\'_chk_family_bipay(this); _setNeedTime(); _setIljungSuga();\'>동거가족
					</td>';
	echo '			<td style=\''.__BORDER_R__.'\'>
						<input name=\'yoyNm1\' type=\'text\'   value=\''.$yoyNm1.'\' style=\'width:70px; background-color:#eeeeee;\' onClick=\'_helpSuYoyPA("'.$code.'","0","'.$key.'",document.f.yoy1,document.f.yoyNm1,document.f.yoyTA1)\' readOnly><a onClick=\'_yoyNot("1");\'><span class=\'bold\'>X</span></a>
						<input name=\'yoy1\'   type=\'hidden\' value=\''.$ed->en($yoy1).'\'>
						<input name=\'yoyTA1\' type=\'hidden\' value=\''.$yoyTA1.'\'>
					</td>';
	echo '			<th>시작</th>';
	echo '			<td style=\''.__BORDER_R__.'\'>
						<input name=\'ftHour\' type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.($mode == 'MODIFY' ? 'background:#eeeeee;' : '').'\' onKeyDown=\'__onlyNumber(this);\' onKeyUp=\'if(this.value.length == 2){document.f.ftMin.focus();}\' onFocus=\''.($mode != 'MODIFY' ? 'this.select();' : 'this.blur();').'\' '.($mode == 'MODIFY' ? 'readOnly' : '').' onBlur=\'_checkTimeH();\'>시
						<input name=\'ftMin\'  type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;'.($mode == 'MODIFY' ? 'background:#eeeeee;' : '').'\' onKeyDown=\'__onlyNumber(this);\' onKeyUp=\'_setEntTimeFocus();\' onFocus=\''.($mode != 'MODIFY' ?  'this.select();' : 'this.blur();').'\' '.($mode == 'MODIFY' ? 'readOnly' : '').'\' onBlur=\'_checkTimeM();\'>분
					</td>';
	echo '			<td style=\''.__BORDER_B__.'\'>
						<select name=\'procTime\' style=\'width:auto;\' onChange=\'_setEndTime();\'>
							<option value=\'30\'>30분</option>
							<option value=\'60\'>60분</option>
							<option value=\'90\'>90분</option>
							<option value=\'120\'>120분</option>
							<option value=\'150\'>150분</option>
							<option value=\'180\'>180분</option>
							<option value=\'210\'>210분</option>
							<option value=\'240\'>240분</option>
							<option value=\'0\'>270분이상</option>
						</select>
					</td>';
	echo '			<td style=\''.__BORDER_B__.'\'>
						<select name=\'svcSubCD\' style=\'width:auto;\' onChange=\'_setEndTimeSub();\' disabled=\'true\'>
							<option value=\'1\'>차량입욕</option>
							<option value=\'2\'>가정내입욕</option>
						</select>
					</td>';
	echo '			<td style=\''.__BORDER_B__.__BORDER_R__.'\'>
						<select name=\'carNo\' style=\'width:auto; margin-right:3px;\' disabled=\'true\'>';
						$sql = "select m00_car_no1, m00_car_no2
								  from m00center
								 where m00_mcode = '$code'
								   and m00_mkind = '$kind'";

						$car_arr = $conn->get_array($sql);

						echo '<option value=\''.$car_arr[0].'\'>'.$car_arr[0].'</option>';
						echo '<option value=\''.$car_arr[1].'\'>'.$car_arr[1].'</option>';

						unset($car_arr);
						echo '</select>
					</td>';
	echo '			<td style=\''.__BORDER_B__.'\' class=\'center\'>';
						if ($mode == 'IN'){
							echo '<a href="#" onclick="_show_guide();" style="font-weight:bold;">[변경사항안내]</a>';
						}
			echo '	</td>';
	echo '		</tr>';
	echo '		<tr>';
	echo '			<td style=\''.__BORDER_B__.__BORDER_R__.'\'>
						<input id=\'bipayUmu\' name=\'bipayUmu\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' onclick=\'_chk_family_bipay(this); _set_bipay_pay(); _setNeedTime(); _setIljungSuga();\'>비급여
					</td>';
	echo '			<td style=\''.__BORDER_B__.__BORDER_R__.'\'>
						<input name=\'yoyNm2\' type=\'text\'   value=\''.$yoyNm2.'\' style=\'width:70px; background-color:#eeeeee; display:none;\' onClick=\'_helpSuYoyPA("'.$code.'","'.$kind.'","'.$key.'",document.f.yoy2,document.f.yoyNm2,document.f.yoyTA2)\' readOnly><span id=\'delete_yoy2\' onClick=\'_yoyNot("2");\' style=\'display:none;\'><span class=\'bold\'>X</span></span>
						<input name=\'yoy2\'   type=\'hidden\' value=\''.$ed->en($yoy2).'\'>
						<input name=\'yoyTA2\' type=\'hidden\' value=\''.$yoyTA2.'\'>
					</td>';
	echo '			<th style=\''.__BORDER_B__.'\'>종료</th>';
	echo '			<td style=\''.__BORDER_B__.__BORDER_R__.'\'>
						<input name=\'ttHour\' type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px; background-color:#eeeeee;\' onKeyDown=\'__onlyNumber(this);\' onKeyUp=\'if(this.value.length == 2){document.f.ttMin.focus();}\'  onChange=\'if(parseInt(this.value) >= 24){this.value = "00";}\' onBlur=\'_setEndTimeM();\' readOnly>시
						<input name=\'ttMin\'  type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px; background-color:#eeeeee;\' onKeyDown=\'__onlyNumber(this);\' onKeyUp=\'if(this.value.length == 2){document.f.yoyNm1.focus();}\' onChange=\'if(this.value == ""){this.value = "00";}\' onBlur=\'_setEndTimeM();\' readOnly>분
					</td>';
	echo '			<td colspan=\'4\' style=\''.__BORDER_B__.'\'>
						<input name=\'visitSudangCheck\' type=\'checkbox\' class=\'checkbox\' value=\'Y\' disabled=\''.$visit[0].'\' onClick=\'checkVisitSugang(this.checked);\'>방문건별 수당
						<input name=\'visitSudang\' type=\'text\' value=\''.number_format($visitSudang).'\' tag=\''.$visitSudang.'\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:60px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this);\' onFocus=\'__commaUnset(this);\' onBlur=\'__commaSet(this);\'>원&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						수당비율(<input name=\'sudangYul1\' type=\'text\' value=\''.$sudangYul[1].'\' maxlength=\'5\' tag=\''.$sudangYul[1].'\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:40px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this, ".");\' onFocus=\'this.select();\' onChange=\'return _setBathRate("1");\'> /
						<input name=\'sudangYul2\' type=\'text\' value=\''.$sudangYul[2].'\' maxlength=\'5\' tag=\''.$sudangYul[2].'\' disabled=\''.$visit[0].'\' class=\'number\' style=\'width:40px; background-color:'.$visit[1].';\' onKeyDown=\'__onlyNumber(this, ".");\' onFocus=\'this.select();\' onChange=\'return _setBathRate("2");\'>)
					</td>';
	echo '		</tr>';
	echo '	</tbody>';
	echo '</table>';



	/**************************************************

		비급여 설정

	**************************************************/
	include('iljung_reg_expense.php');
	/*************************************************/



	########################################################
	#
	# 제공요일 및 일자
	#
	include_once('iljung_svc_date.php');
	########################################################

	########################################################
	#
	# 적용수가
	#
	include_once('iljung_svc_suga.php');
	########################################################

	echo '<input name=\'Egubun\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'Ngubun\' type=\'hidden\' value=\'\'>';

	echo '<input name=\'Etime\' type=\'hidden\' value=\'0\'>';
	echo '<input name=\'Ntime\' type=\'hidden\' value=\'0\'>';

	//echo '<input name=\'addDay\' type=\'text\' value=\''.$_GET['mDay'].'\'>';
	//echo '<input name=\'addIndex\' type=\'text\' value=\''.$_GET['mIndex'].'\'>';
	//echo '<input name=\'addDate\' type=\'text\' value=\''.$_GET['mDate'].'\'>';

	echo '<input name=\'contDate\' type=\'hidden\' value=\''.$centerStartDate.'\'>';
	echo '<input name=\'oldDate\' type=\'hidden\' value=\'\'>';

	echo '<input name=\'flagHistory\' type=\'hidden\' value=\''.$flagHistory.'\'>';

	echo '<input name=\'closing_yn\' type=\'hidden\' value=\''.$closing_yn.'\'>';

	echo '<input name=\'family_min\' type=\'hidden\' value=\''.$family_min.'\' tag=\''.$family_min.'\'>';
	echo '<input name=\'family_cnt\' type=\'hidden\' value=\''.$family_cnt.'\' tag=\''.$family_cnt.'\'>';

	echo '<input name=\'back_family_min\' type=\'hidden\' value=\''.$back_family_min.'\'>';
	echo '<input name=\'back_family_cnt\' type=\'hidden\' value=\''.$back_family_cnt.'\'>';

	echo '<input name=\'bath_week_cnt\' type=\'hidden\' value=\''.$bath_week_cnt.'\'>';
	echo '<input name=\'care_limit_cnt\' type=\'hidden\' value=\''.$care_limit_cnt.'\'>';

	echo '<input name=\'voucher_make_yn\' type=\'hidden\' value=\'Y\'>';

	$html = ob_get_contents();
	ob_end_clean();

	echo $html;

	include_once('../inc/_db_close.php');
?>
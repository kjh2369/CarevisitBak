<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_REQUEST['year'];
	$month = $_REQUEST['month'];
	$month = (intval($month) < 10 ? '0' : '').intval($month);
	$init_year = $myF->year();
	
	if (empty($year))  $year  = date('Y', mktime());
	if (intval($month) == 0) $month = date('m', mktime());
	
	$group_btn  = '<div id=\'grp_btn[]\' style=\'margin:10px; display:none;\'>';
	$group_btn .= '<table class=\'my_table my_border_blue\' style=\'width:100%;\'>';
	$group_btn .= '<colgroup>';
	$group_btn .= '<col>';
	$group_btn .= '</colgroup>';
	$group_btn .= '<tbody>';
	$group_btn .= '<tr>';
	$group_btn .= '<th class=\'right my_border_blue\'>';
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'list\'></span><button type=\'button\' onFocus=\'_client_proc_counsel_list(document.getElementById("body")); this.blur();\' onclick=\'return false;\'>리스트</button></span> ';
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'save\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_counsel_save(); return false;\'>저장</button></span> ';
	//$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'pdf\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_show(); return false;\'>출력</button></span> ';
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'delete\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_counsel_delete(document.getElementById("mode").value,"","","",""); return false;\'>삭제</button></span> ';
	$group_btn .= '</th>';
	$group_btn .= '</tr>';
	$group_btn .= '</tbody>';
	$group_btn .= '</table>';
	$group_btn .= '</div>';
	
	
	echo '<script type=\'text/javascript\' src=\'../js/counsel.js\'></script>';
	echo '<script type=\'text/javascript\' src=\'../js/counsel.client.js\'></script>';
	echo '<form name=\'f\' method=\'post\'>';
	echo '<div class=\'title title_border\'>과정상담 리스트</div>';

	echo '<table class=\'my_table\' style=\'width:100%; \'>';
	echo '<colgroup>';
	echo '<col width=\'60px\'>';
	echo '<col width=\'110px\'>';
	echo '<col width=\'60px\'>';
	echo '<col width=\'110px\'>';
	echo '<col width=\'60px\'>';
	echo '<col width=\'45px\'>';
	echo '<col >';
	echo '<tbody>';
	echo '<tr>';
	echo '<th>기관기호</th>
		  <td><span style="padding-left:5px;">'.$_SESSION["userCenterCode"].'</span></td>
		  <th>기관명</th>
		  <td colspan="4"><span style="padding-left:5px;">'.$_SESSION["userCenterName"].'</span></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th>고객명</th>
			<td>
				<input name="find_su_name" type="text" value=\''.$find_su_name.'\' style="width:100%;" onkeyup="if(event.keyCode==13){_client_search();}" onFocus="this.select();">
			</td>';
	echo '<th>상담자</th>
			<td>
				<input name="find_counsel_name" type="text" value=\''.$find_counsel_name.'\' style="width:100%;" onkeyup="if(event.keyCode==13){_client_search();}" onFocus="this.select();">
			</td>';
	echo '<th>상담유형</th>
			<td>
				<select name=\'find_type\' style=\'width:auto;\' >
				<option value=\'all\' '.($find_type == 'all' ? 'selected' : '').'>전체</option>
				<option value=\'VISIT\' '.($find_type == 'VISIT' ? 'selected' : '').'>고객방문상담</option>
				<option value=\'PHONE\' '.($find_type == 'PHONE' ? 'selected' : '').'>전화상담</option>
				<option value=\'STRESS\' '.($find_type == 'STRESS' ? 'selected' : '').'>불만 및 고충처리</option>
				<option value=\'CASE\' '.($find_type == 'CASE' ? 'selected' : '').'>사례관리회의</option>
				<option value=\'STAT\' '.($find_type == 'STAT' ? 'selected' : '').'>상태변화일지</option>
				</select>
			</td>';
	echo '<td class="other" style="padding-left:5px; vertical-align:top; padding-top:2px;">
			<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_client_search();">조회</button></span>';
	echo ' <span class="btn_pack m "><button type="button" onclick=\'_client_proc_counsel_show("","","");\' >전체출력</button></span>';	
	echo '</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '<table class=\'my_table my_border\' style=\'margin-top:-1px;" width:100%;\'>';
	echo '<colgroup>';
	echo '<col width=\'65px\'>';
	echo '<col width=\'50px\'>';
	echo '<col>';
	echo '</colgroup>';
	echo '<tbody>';
	echo '<tr>';
	echo '<th class=\'head\'>년도</th>';
	echo '<td class=\'last\'>';

	echo '<select name=\'year\' style=\'width:auto;\' onchange=\'set_year(this.value)\'>';
	for($i=$init_year[0]; $i<=$init_year[1]; $i++){
		echo '<option value=\''.$i.'\' '.($year == $i ? 'selected' : '').'>'.$i.'</option>';
	}
	echo '</select>년';
	unset($init_year);
	echo '</td>';
	
	$sql = 'select type_cd, org_no, cast(right(yymm, 2) as unsigned) as mon, c_cd
			  from (
				   select \'VISIT\' as type_cd
				   ,	  org_no
				   ,	  visit_yymm as yymm
				   ,      visit_c_cd as c_cd
					 from counsel_client_visit
					where org_no               = \''.$code.'\'
					  and left(visit_yymm, 4)  = \''.$year.'\'
					  and del_flag             = \'N\'
					union all
				   select \'PHONE\' as type_cd
				   ,	  org_no
				   ,	  phone_yymm as yymm
				   ,      phone_c_cd as c_cd
					 from counsel_client_phone
					where org_no               = \''.$code.'\'
					  and left(phone_yymm, 4)  = \''.$year.'\'
					  and del_flag             = \'N\'
					union all
				   select \'STRESS\' as type_cd
				   ,	  org_no
				   ,	  stress_yymm as yymm
				   ,      stress_c_cd as c_cd
					 from counsel_client_stress
					where org_no               = \''.$code.'\'
					  and left(stress_yymm, 4) = \''.$year.'\'
					  and del_flag             = \'N\'
					union all
				   select \'CASE\' as type_cd
				   ,	  org_no
				   ,	  case_yymm as yymm
				   ,      case_c_cd as c_cd
					 from counsel_client_case
					where org_no             = \''.$code.'\'
					  and left(case_yymm, 4) = \''.$year.'\'
					  and del_flag           = \'N\'
					UNION ALL
				   SELECT \'STAT\' as type_cd
				   ,	  org_no
				   ,	  DATE_FORMAT(reg_dt,\'%Y%m\')
				   ,	  jumin as c_cd
				     FROM counsel_client_state
					WHERE org_no = \''.$code.'\'
					  AND DATE_FORMAT(reg_dt,\'%Y\') = \''.$year.'\'
				   ) as t';

	$sql .=	' inner join m03sugupja
				 on m03_ccode = org_no
			    and m03_jumin = c_cd
			    and m03_mkind = '.$conn->_client_kind().'';
	
	if ($find_type == '' or $find_type == 'all'){
	}else{
		$sql .= ' where type_cd = \''.$find_type.'\'';
	}

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$mon[$row['mon']] = 1;
	}


	$conn->row_free();

	echo '<td class=\'left last\' style=\'padding-top:1px;\'>';

		if ($month == 'A'){
			$class = 'my_month my_month_y';
			$color  = 'color:#000000;';
		}else{
			$class = 'my_month my_month_1';
			$color  = 'color:#666666;';
		}

		$text  = '<a href=\'#\' onclick=\'set_month("A");\'><span style=\''.$color.'\'>전체</span></a>';
		$html .= '<div id=\'btnMonth_A\' class=\''.$class.'\' style=\'float:left; margin-right:3px;\'>'.$text.'</div>';

		for($i=1; $i<=12; $i++){
			$class = 'my_month ';

			if ($i == intval($month)){
				$class .= 'my_month_y ';
				$color  = 'color:#000000;';
			}else{
				$class .= 'my_month_1 ';
				$color  = 'color:#666666;';
			}

			if (is_array($mon)){
				if (intval($mon[$i]) < 1) $color = 'color:#cccccc';
			}else {
				$color = 'color:#cccccc';
			}

			$text = '<a href=\'#\' onclick=\'set_month('.$i.');\'><span style=\''.$color.'\'>'.$i.'월</span></a>';

			if ($i == 12){
				$style = 'float:left;';
			}else{
				$style = 'float:left; margin-right:3px;';
			}
			$html .= '<div id=\'btnMonth_'.$i.'\' class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
		}
	echo $html;
	echo '</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';

	echo $group_btn;
	echo '<div id=\'body\'></div>';
	echo $group_btn;

	//기관기호
	echo '<input id=\'code\' name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';
	
	//월
	echo '<input name=\'month\' type=\'hidden\' value=\''.$month.'\'>';
	
	
	
	//리포트 키
	echo '<input name=\'visit_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'visit_seq\'  type=\'hidden\' value=\'\'>';

	echo '<input name=\'phone_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'phone_seq\'  type=\'hidden\' value=\'\'>';

	echo '<input name=\'stress_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'stress_seq\'  type=\'hidden\' value=\'\'>';

	echo '<input name=\'case_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'case_seq\'  type=\'hidden\' value=\'\'>';
	
	echo '<input name=\'mode\' type=\'hidden\' value=\'\'>';


	//상태변화일지 주민,작성일
	echo '<input name=\'jumin\'  type=\'hidden\' value=\'\'>';
	echo '<input name=\'dt\'  type=\'hidden\' value=\'\'>';

	

	echo '</form>';

	echo '<script language=\'javascript\'>';
	echo 'function set_year(year){';
	echo '_client_proc(year);';
	echo 'set_month("A");';
	echo '_client_proc_counsel_list(document.getElementById("body"));';
	echo '}';
	echo 'function set_month(month){';
	echo '	if (month == "A"){
				var obj = document.getElementById("btnMonth_A");
					obj.className="my_month my_month_y";
			}else{
				var obj = document.getElementById("btnMonth_A");
					obj.className="my_month my_month_1";
			}

			for(var i=1; i<=12; i++){
				var obj = document.getElementById("btnMonth_"+i);
				if(month == i){obj.className="my_month my_month_y";}else{obj.className="my_month my_month_1";}
			}

			if (month != "A")
				month = (parseInt(month, 10) < 10 ? \'0\' : \'\') + parseInt(month, 10);';
	echo 'document.getElementById("month").value = month;';
	echo '_client_proc_counsel_list(document.getElementById("body"));';
	echo '}';
	echo 'window.onload = function(){';
	echo 'set_month("A");';
	echo '}';
	echo '</script>';

	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
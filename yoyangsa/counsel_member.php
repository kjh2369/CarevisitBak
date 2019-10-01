<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_REQUEST['year'];
	$month = $_REQUEST['month'];
	$find_yoy_name = $_REQUEST['find_yoy_name'];			//직원명
	$find_counsel_name = $_REQUEST['find_counsel_name'];	//상담자
	$find_type = $_REQUEST['find_type'];					//상담유형
	
	//$month = (intval($month) < 10 ? '0' : '').intval($month);
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
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'list\'></span><button type=\'button\' onFocus=\'_member_proc_counsel_list(document.getElementById("body")); this.blur();\' onclick=\'return false;\'>리스트</button></span> ';
	
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'save\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_member_proc_counsel_save2(); return false;\'>저장</button></span> ';
	
	//$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'pdf\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_member_proc_show(); return false;\'>출력</button></span> ';
	//$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'delete\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_counsel_delete(document.getElementById("mode").value,"",""); return false;\'>삭제</button></span> ';
	$group_btn .= '</th>';
	$group_btn .= '</tr>';
	$group_btn .= '</tbody>';
	$group_btn .= '</table>';
	$group_btn .= '</div>';
	
	echo '<script type=\'text/javascript\' src=\'../js/counsel.js\'></script>';
	echo '<script type=\'text/javascript\' src=\'../js/report.js\'></script>';
	echo '<form name=\'f\' method=\'post\'>';
	echo '<div class=\'title\'><div>직원 과정상담내역</div></div>';
	
	echo '<table class=\'my_table my_border\' style=\'width:100%; \'>';
	echo '<colgroup>';
	echo '<col width=\'80px\'>';
	echo '<col width=\'110px\'>';
	echo '<col width=\'80px\'>';
	echo '<col width=\'110px\'>';
	echo '<col width=\'80px\'>';
	echo '<col width=\'150px\'>';
	echo '<col >';
	echo '<tbody>';
	echo '<tr>';
	echo '<th>기관기호</th>
		  <td><span style="padding-left:5px;">'.$_SESSION["userCenterCode"].'</span></td>
		  <th>기관명</th>
		  <td colspan="4"><span style="padding-left:5px;">'.$_SESSION["userCenterName"].'</span></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th>직원명</th>
			<td>
				<input name="find_yoy_name" type="text" value=\''.$find_yoy_name.'\' style="width:100%;" onkeyup="if(event.keyCode==13){_member_search();}" onFocus="this.select();">
			</td>';
	echo '<th>상담자</th>
			<td>
				<input name="find_counsel_name" type="text" value=\''.$find_counsel_name.'\' style="width:100%;" onkeyup="if(event.keyCode==13){_member_search();}" onFocus="this.select();">
			</td>';
	/*
	echo '<th>상담유형</th>
			<td>
				<select name=\'find_type\' style=\'width:auto;\' onchange=\'_member_search();\'>
				<option value=\'all\' '.($find_type == 'all' ? 'selected' : '').'>전체</option>
				<option value=\'1\' '.($find_type == 1 ? 'selected' : '').'>내방</option>
				<option value=\'2\' '.($find_type == 2 ? 'selected' : '').'>방문</option>
				<option value=\'3\' '.($find_type == 3 ? 'selected' : '').'>전화</option>
				</select>
			</td>';
	*/
	echo '<th>상담유형</th>
			<td>
				<select name=\'find_type\' style=\'width:auto;\' >
				<option value=\'all\' '.($find_type == 'all' ? 'selected' : '').'>전체</option>
				<option value=\'PROCESS\' '.($find_type == 'PROCESS' ? 'selected' : '').'>과정상담</option>
				<option value=\'STRESS\' '.($find_type == 'STRESS' ? 'selected' : '').'>불만 및 고충처리</option>
				<option value=\'CASE\' '.($find_type == 'CASE' ? 'selected' : '').'>사례관리회의</option>
				</select>
			</td>';
	echo '<td class="other">
			<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_member_search();">조회</button></span>';
	
	if($debug) echo '  <span class="btn_pack m "><button type="button" onclick=\'_client_proc_counsel_show("","","");\' >전체출력</button></span>';

	echo '</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '<table class=\'my_table my_border\' width:100%;\'>';
	echo '<colgroup>';
	echo '<col width=\'80px\'>';
	echo '<col width=\'110px\'>';
	echo '<col>';
	echo '</colgroup>';
	echo '<tbody>';
	echo '<tr>';
	echo '<th class=\'head\'>년도</th>';
	echo '<td class=\'last\'>';
	
	echo '<select name=\'year\' style=\'width:auto;\' onchange=\'set_year(this.value);\'>';
	for($i=$init_year[0]; $i<=$init_year[1]; $i++){
		echo '<option value=\''.$i.'\' '.($year == $i ? 'selected' : '').'>'.$i.'</option>';
	}
	echo '</select>년';
	unset($init_year);
	echo '</td>';
	
	
		
	$sql = 'select type_cd, org_no, cast(right(yymm, 2) as unsigned) as mon, c_cd
			  from (
				   select \'PROCESS\' as type_cd
				   ,	  org_no
				   ,	  date_format(stress_dt,\'%Y%m\') as yymm
				   ,      stress_ssn as c_cd
					 from counsel_stress
					where org_no					    = \''.$code.'\'
					  and DATE_FORMAT(stress_dt,\'%Y\') = \''.$year.'\'
					  and del_flag            = \'N\'
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
					) t
				inner join m02yoyangsa
						on m02_ccode  = org_no
					   and m02_yjumin = c_cd
					   and m02_mkind  = '.$conn->_member_kind().'';

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

	
	/*
		$sql = 'select sum(case date_format(stress_dt,\'%m\') when \'01\' then 1 else 0 end) as \'01\'
				,      sum(case date_format(stress_dt,\'%m\') when \'02\' then 1 else 0 end) as \'02\'
				,      sum(case date_format(stress_dt,\'%m\') when \'03\' then 1 else 0 end) as \'03\'
				,      sum(case date_format(stress_dt,\'%m\') when \'04\' then 1 else 0 end) as \'04\'
				,      sum(case date_format(stress_dt,\'%m\') when \'05\' then 1 else 0 end) as \'05\'
				,      sum(case date_format(stress_dt,\'%m\') when \'06\' then 1 else 0 end) as \'06\'
				,      sum(case date_format(stress_dt,\'%m\') when \'07\' then 1 else 0 end) as \'07\'
				,      sum(case date_format(stress_dt,\'%m\') when \'08\' then 1 else 0 end) as \'08\'
				,      sum(case date_format(stress_dt,\'%m\') when \'09\' then 1 else 0 end) as \'09\'
				,      sum(case date_format(stress_dt,\'%m\') when \'10\' then 1 else 0 end) as \'010\'
				,      sum(case date_format(stress_dt,\'%m\') when \'11\' then 1 else 0 end) as \'011\'
				,      sum(case date_format(stress_dt,\'%m\') when \'12\' then 1 else 0 end) as \'012\'
				  from counsel_stress
				 where org_no             = \''.$code.'\'
				   and left(stress_dt, 4) = \''.$year.'\'';

		$mon = $conn->get_array($sql);
	*/
	
	
	echo '<td class=\'left last\' style=\'padding-top:1px;\'>';
			if ($month == 'A'){
				$class = 'my_month my_month_y';
				$color  = 'color:#000000;';
			}else{
				$class = 'my_month my_month_1';
				$color  = 'color:#666666;';
			}

			$text  = '<a href=\'#\' onclick=\'set_month("A")\'><span style=\''.$color.'\'>전체</span></a>';
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
				
				$text = '<a href=\'#\' onclick=\'set_month('.$i.')\'><span style=\''.$color.'\'>'.$i.'월</span></a>';

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
	echo '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';
	
	echo '<input name=\'mode\'  type=\'hidden\' value=\'\'>';
	
	//키
	echo '<input name=\'stress_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'stress_seq\'  type=\'hidden\' value=\'\'>';

	echo '<input name=\'case_yymm\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'case_seq\'  type=\'hidden\' value=\'\'>';
	
	//년
	echo '<input name=\'year\' type=\'hidden\' value=\''.$year.'\'>';
	//월
	echo '<input name=\'month\' type=\'hidden\' value=\''.$month.'\'>';

	//echo '<input name=\'seq\' type=\'hidden\' value=\'\'>';
	//echo '<input name=\'ssn\' type=\'hidden\' value=\'\'>';

	echo '</form>';

	echo '<script language=\'javascript\'>';
	echo 'function set_year(year){';
	echo '_member_proc(year);';
	echo 'set_month("A");';
	echo '_member_proc_counsel_list(document.getElementById("body"));';
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
				if(month == i){obj.className="my_month my_month_y";}else{obj.className="my_month my_month_1"; }
			}

			if (month != "A")
				month = (parseInt(month, 10) < 10 ? \'0\' : \'\') + parseInt(month, 10);';
	echo 'document.getElementById("month").value = month;';
	echo '_member_proc_counsel_list(document.getElementById("body"));';
	echo '}';
	echo 'window.onload = function(){';
	echo 'set_month("A");';
	
	#저장하고나서 다시 등록창로드
	if($seq != ''){
		
		$ssn = $ed->de($ssn);					
		echo '_member_proc_counsel_reg(\'body\', "'.$year.$month.'", "'.$seq.'", "'.$ed->en($ssn).'");';
	}

	echo '}';
	echo '</script>';
	
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
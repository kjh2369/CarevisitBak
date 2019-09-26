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
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'pdf\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_show(); return false;\'>출력</button></span> ';
	$group_btn .= '<span class=\'btn_pack m icon\'><span class=\'delete\'></span><button type=\'button\' onFocus=\'this.blur();\' onclick=\'_client_proc_counsel_delete(document.getElementById("mode").value,"",""); return false;\'>삭제</button></span> ';
	$group_btn .= '</th>';
	$group_btn .= '</tr>';
	$group_btn .= '</tbody>';
	$group_btn .= '</table>';
	$group_btn .= '</div>';

	echo '<script type=\'text/javascript\' src=\'../js/counsel.js\'></script>';
	echo '<script type=\'text/javascript\' src=\'../js/counsel.client.js\'></script>';
	echo '<form name=\'f\' method=\'post\'>';
	echo '<div class=\'title title_border\'>과정상담 리스트</div>';

	echo '<table class=\'my_table\' style=\'width:100%;\'>';
	echo '<colgroup>';
	echo '<col width=\'40px\'>';
	echo '<col width=\'50px\'>';
	echo '<col>';
	echo '</colgroup>';
	echo '<tbody>';
	echo '<tr>';
	echo '<th class=\'head\'>년도</th>';
	echo '<td class=\'last\'>';

	echo '<select name=\'year\' style=\'width:auto;\'>';
	for($i=$init_year[0]; $i<=$init_year[1]; $i++){
		echo '<option value=\''.$i.'\' '.($year == $i ? 'selected' : '').'>'.$i.'</option>';
	}
	echo '</select>년';
	unset($init_year);
	echo '</td>';

	$sql = 'select cast(right(yymm, 2) as unsigned) as mon
			  from (
				   select visit_yymm as yymm
				    from counsel_client_visit
					where org_no              = \''.$code.'\'
					  and left(visit_yymm, 4) = \''.$year.'\'
					  and del_flag            = \'N\'
					union all
				   select phone_yymm as yymm
				     from counsel_client_phone
					where org_no              = \''.$code.'\'
					  and left(phone_yymm, 4) = \''.$year.'\'
					  and del_flag            = \'N\'
					union all
				   select stress_yymm as yymm
					 from counsel_client_stress
					where org_no               = \''.$code.'\'
					  and left(stress_yymm, 4) = \''.$year.'\'
					  and del_flag             = \'N\'
					union all
				   select case_yymm as yymm
					 from counsel_client_case
					where org_no             = \''.$code.'\'
					  and left(case_yymm, 4) = \''.$year.'\'
					  and del_flag           = \'N\'
				   ) as t';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mon[$row['mon']] = 1;
	}
	

	$conn->row_free();

	echo '<td class=\'left last\' style=\'padding-top:1px;\'>'.$myF->_btn_month($month, 'set_month(', ')', $mon, true).'</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';

	echo $group_btn;
	echo '<div id=\'body\'></div>';
	echo $group_btn;

	//기관기호
	echo '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';

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

	echo '</form>';

	echo '<script language=\'javascript\'>';
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
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<table class=\'my_table\' style=\'width:100%;\'>';
	
	/**************************************************

		col group

	**************************************************/
	echo '<colgroup>';
	echo '<col width=\'40px\'>';
	echo '<col width=\'70px\'>';
	echo '<col width=\'70px\'>';
	echo '<col width=\'70px\'>';
	echo '<col width=\'70px\'>';
	echo '<col width=\'150px\'>';
	echo '<col width=\'100px\'>';
	echo '<col>';
	echo '</colgroup>';


	/**************************************************

		head

	**************************************************/
	echo '<thead>';
	echo '<tr>';
	echo '<th class=\'head\'>No</th>';
	echo '<th class=\'head\'>일자</th>';
	echo '<th class=\'head\'>직원명</th>';
	echo '<th class=\'head\'>상담유형</th>';
	echo '<th class=\'head\'>상담자</th>';
	echo '<th class=\'head\'>처리결과</th>';
	echo '<th class=\'head last\'>비고</th>';
	echo '</tr>';
	echo '</thead>';


	/**************************************************

		body

	**************************************************/
	echo '<tbody>';

	if ($month == 'A'){
		$yymm = $year;
	}else{
		$yymm = $year.$month;
	}
	

	$sql = 'select stress_dt as dt
							,      stress_ssn as m_cd
							,      stress_seq as seq
							,      m02_yname as m_nm
							,      stress_talker_nm as r_nm
							,      case stress_type when \'1\' then \'내방\'
													when \'2\' then \'방문\'
													when \'3\' then \'전화\' else \'\' end as r_type
							,      stress_result as r_str
							  from counsel_stress
							  left join m02yoyangsa
								on m02_ccode  = org_no
							   and m02_yjumin = stress_ssn
							   and m02_mkind  = '.$conn->_member_kind().'
							 where org_no     = \''.$code.'\'';

					if ($f_yoy_name != '') $sql .= 'and m02_yname like \'%'.$f_yoy_name.'%\''; 
					if ($f_counsel_name != '') $sql .= 'and stress_talker_nm like \'%'.$f_counsel_name.'%\''; 
					if ($f_type != 'all') $sql .= 'and stress_type = \''.$f_type.'\''; 
					
					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){		   
							$sql .= ' and date_format(stress_dt,\'%Y\') = \''.substr($yymm,0,4).'\'';
						}else {
							$sql .= ' and date_format(stress_dt,\'%Y%m\') = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and stress_ssn = \''.$ssn.'\'';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0)
			$class = ' m_out';
		else
			$class = ' bg_gray';

		echo '<tr class=\''.$class.'\' onmouseover=\'this.className="m_over";\' onmouseout=\'this.className="'.$class.'";\'>';
		echo '<td class=\'center\'>'.($i+1).'</td>';
		echo '<td class=\'center\'>'.$myF->dateStyle($row['dt'],'.').'</td>';
		echo '<td class=\'left\'>'.$row['m_nm'].'</td>';
		echo '<td class=\'left\'>'.$row['r_type'].'</td>';
		echo '<td class=\'left\'>'.$row['r_nm'].'</td>';
		echo '<td class=\'left\'><div class=\'nowrap\' style=\'width:330px;\'>'.$row['r_str'].'</div></td>';
		echo '<td class=\'left last\'>';
			
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_member_proc_counsel_reg("body","'.$yymm.'","'.$row['seq'].'","'.$ed->en($row['m_cd']).'");\'>수정</button></span> ';
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_member_proc_show("'.$row['seq'].'","'.$ed->en($row['m_cd']).'");\'>출력</button></span> ';

		echo '</td>';
		echo '</tr>';
	}

	$conn->row_free();

	echo '</tbody>';
	echo '<tfoot>';
	echo '<tr>';
	
	if ($row_count > 0)
		echo '<td class=\'left last bottom\' colspan=\'6\'>'.$myF->message($row_count, 'N').'</td>';
	else
		echo '<td class=\'center last\' colspan=\'7\'>'.$myF->message('nodata', 'N').'</td>';

	echo '</tr>';
	echo '</tfoot>';
	echo '</table>';
	echo '<input name=\'para_seq\'  type=\'hidden\' value=\'\'>'; //
	echo '<input name=\'para_m_cd\' type=\'hidden\' value=\'\'>'; //직원
	echo '<input name=\'report_id\'   type=\'hidden\' value=\'MEMTR\'>';

	include_once('../inc/_db_close.php');
?>
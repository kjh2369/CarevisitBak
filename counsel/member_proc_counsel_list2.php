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
	echo '<th class=\'head\'>상담자</th>';
	echo '<th class=\'head\'>상담유형</th>';
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
	

	$sql = 'select lower(type) as type_cd
			,      case type when \'PROCESS\' then \'과정상담\'
							 when \'STRESS\' then \'불만 및 고충처리\'
							 when \'CASE\'   then \'사례관리회의\' else \'-\' end as type_nm
			,      yymm
			,      seq
			,      dt
			,      c_cd
			,      m02_yname as m_nm
			,      r_nm

			  from (
					select \'PROCESS\' as type
					,	   org_no
					,      DATE_FORMAT(stress_dt,\'%Y%m\') as yymm
					,      stress_seq as seq
					,	   stress_dt as dt
					,      stress_ssn as c_cd
					,      stress_talker_nm as r_nm
					  from counsel_stress
					 where org_no      = \''.$code.'\'
					  and del_flag    = \'N\'
					  and left(DATE_FORMAT(stress_dt, \'%Y%m\'), '.strlen($yymm).') = \''.$yymm.'\'';
					  
	if (!empty($f_counsel_name)) $sql .= 'and stress_talker_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'PROCESS\' = \''.$f_type.'\'';
	
	/*
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
	*/

	$sql .=			'union all
				   /*
					* 불만 및 고충처리 리스트
					*/
				   select \'STRESS\' as type
				   ,      org_no
				   ,      stress_yymm as yymm
				   ,      stress_seq as seq
				   ,      stress_dt as dt
				   ,      stress_c_cd as c_cd
				   ,      stress_m_nm as r_nm
					 from counsel_client_stress
					where org_no      = \''.$code.'\'
					  and del_flag    = \'N\'
					  and left(stress_yymm, '.strlen($yymm).') = \''.$yymm.'\'';
	
	if (!empty($f_counsel_name)) $sql .= 'and stress_m_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'STRESS\' = \''.$f_type.'\'';
	
	$sql .=			'union all
				   /*
					* 사례관리 회의 리스트
					*/
				   select \'CASE\' as type
				   ,      org_no
				   ,      case_yymm as yymm
				   ,      case_seq as seq
				   ,      case_dt as dt
				   ,      case_c_cd as c_cd
				   ,      case_m_nm as r_nm
					 from counsel_client_case
					where org_no    = \''.$code.'\'
					  and del_flag  = \'N\'
					  and left(case_yymm, '.strlen($yymm).') = \''.$yymm.'\'';

	if (!empty($f_counsel_name)) $sql .= 'and case_m_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'CASE\' = \''.$f_type.'\'';
	
	$sql .= 		'	) t
					inner join m02yoyangsa
							on m02_ccode  = org_no
						   and m02_yjumin = c_cd
						   and m02_mkind  = \''.$conn->_member_kind().'\'';

	if ($f_yoy_name != '') $sql .= 'and m02_yname like \'%'.$f_yoy_name.'%\''; 
	//if ($f_type != 'all') $sql .= 'and stress_type = \''.$f_type.'\''; 
	
	$sql .=	' order by dt desc, m_nm';
	
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
		echo '<td class=\'left\'>'.$row['r_nm'].'</td>';
		echo '<td class=\'left\'>'.$row['type_nm'].'</td>';
		echo '<td class=\'left last\'>';
			
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_member_proc_counsel_reg2("body","'.$row['type_cd'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$ed->en($row['c_cd']).'");\'>수정</button></span> ';
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_member_proc_show("'.$row['seq'].'","'.$ed->en($row['c_cd']).'","'.$row['type_cd'].'","'.$row['yymm'].'");\'>출력</button></span> ';
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_member_proc_counsel_delete("'.$row['type_cd'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$ed->en($row['c_cd']).'");\'>삭제</button></span> ';
		echo '</td>';
		echo '</tr>';


		/*************************************
			전체출력을위한 환경변수
		*************************************/
		echo '<input type=\'hidden\' id=\'type_'.$i.'\' name=\'type_'.$i.'\' value=\''.$row['type_cd'].'\'>';
		echo '<input type=\'hidden\' id=\'yymm_'.$i.'\' name=\'yymm_'.$i.'\' value=\''.$row['yymm'].'\'>';
		echo '<input type=\'hidden\' id=\'seq_'.$i.'\' name=\'seq_'.$i.'\' value=\''.$row['seq'].'\'>';
		echo '<input type=\'hidden\' id=\'jumin_'.$i.'\' name=\'jumin_'.$i.'\' value=\''.$ed->en($row['c_cd']).'\'>';
	}

	$conn->row_free();

	//전체출력을 위한 행 카운트
	echo '<input type=\'hidden\' id=\'row_cnt\' name=\'row_cnt\' value=\''.$row_count.'\'>';
	echo '<input type=\'hidden\' id=\'root\' name=\'root\' value=\'MEMBER\'>';


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
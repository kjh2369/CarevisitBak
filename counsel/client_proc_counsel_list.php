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
	echo '<th class=\'head\'>고객</th>';
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

	/*
	$sql = 'select lower(type) as type_cd
			,      case type when \'VISIT\'  then \'고객방문상담\'
							 when \'PHONE\'  then \'전화상담\'
							 when \'STRESS\' then \'불만 및 고충처리\'
							 when \'CASE\'   then \'사례관리회의\' else \'-\' end as type_nm
			,      yymm
			,      seq
			,      dt
			,      c_cd
			,      m03_name as c_nm
			,      m_nm
			  from (

				   select \'VISIT\' as type
				   ,      org_no
				   ,      visit_yymm as yymm
				   ,      visit_seq as seq
				   ,      visit_dt as dt
				   ,      visit_c_cd as c_cd
				   ,      visit_m_nm as m_nm
					 from counsel_client_visit
					where org_no     = \''.$code.'\'
					  and del_flag   = \'N\'
					  and left(visit_yymm, '.strlen($yymm).') = \''.$yymm.'\'
					union all

				   select \'PHONE\' as type
				   ,      org_no
				   ,      phone_yymm as yymm
				   ,      phone_seq as seq
				   ,      phone_dt as dt
				   ,      phone_c_cd as c_cd
				   ,      phone_m_nm as m_nm
					 from counsel_client_phone
					where org_no     = \''.$code.'\'
					  and del_flag   = \'N\'
					  and left(phone_yymm, '.strlen($yymm).') = \''.$yymm.'\'
					union all

				   select \'STRESS\' as type
				   ,      org_no
				   ,      stress_yymm as yymm
				   ,      stress_seq as seq
				   ,      stress_dt as dt
				   ,      stress_c_cd as c_cd
				   ,      stress_m_nm as m_nm
					 from counsel_client_stress
					where org_no      = \''.$code.'\'
					  and del_flag    = \'N\'
					  and left(stress_yymm, '.strlen($yymm).') = \''.$yymm.'\'
					union all

				   select \'CASE\' as type
				   ,      org_no
				   ,      case_yymm as yymm
				   ,      case_seq as seq
				   ,      case_dt as dt
				   ,      case_c_cd as c_cd
				   ,      case_m_nm as m_nm
					 from counsel_client_case
					where org_no    = \''.$code.'\'
					  and del_flag  = \'N\'
					  and left(case_yymm, '.strlen($yymm).') = \''.$yymm.'\'
				   ) as t
			 inner join m03sugupja
				on m03_ccode = org_no
			   and m03_jumin = c_cd
			   and m03_mkind = '.$conn->_client_kind().'
			 order by dt desc, c_nm';
	*/

	$sql = 'select lower(type) as type_cd
			,      case type when \'VISIT\'  then \'고객방문상담\'
							 when \'PHONE\'  then \'전화상담\'
							 when \'STRESS\' then \'불만 및 고충처리\'
							 when \'CASE\'   then \'사례관리회의\'
							 WHEN \'STAT\'   THEN \'상태변화일지\' else \'-\' end as type_nm
			,      yymm
			,      seq
			,      dt
			,      c_cd
			,      m03_name as c_nm
			,      m_nm
			  from (
				   /*
					* 고객방문상담 리스트
					*/
				   select \'VISIT\' as type
				   ,      org_no
				   ,      visit_yymm as yymm
				   ,      visit_seq as seq
				   ,      visit_dt as dt
				   ,      visit_c_cd as c_cd
				   ,      visit_m_nm as m_nm
					 from counsel_client_visit
					where org_no     = \''.$code.'\'
					  and del_flag   = \'N\'
					  and left(visit_yymm, '.strlen($yymm).') = \''.$yymm.'\'';

	if (!empty($f_counsel_name)) $sql .= 'and visit_m_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'VISIT\' = \''.$f_type.'\'';

	$sql .=			'union all
				   /*
					* 전화상담 리스트
					*/
				   select \'PHONE\' as type
				   ,      org_no
				   ,      phone_yymm as yymm
				   ,      phone_seq as seq
				   ,      phone_dt as dt
				   ,      phone_c_cd as c_cd
				   ,      phone_m_nm as m_nm
					 from counsel_client_phone
					where org_no     = \''.$code.'\'
					  and del_flag   = \'N\'
					  and left(phone_yymm, '.strlen($yymm).') = \''.$yymm.'\'';

	if (!empty($f_counsel_name)) $sql .= 'and phone_m_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'PHONE\' = \''.$f_type.'\'';

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
				   ,      stress_m_nm as m_nm
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
				   ,      case_m_nm as m_nm
					 from counsel_client_case
					where org_no    = \''.$code.'\'
					  and del_flag  = \'N\'
					  and left(case_yymm, '.strlen($yymm).') = \''.$yymm.'\'';

	if (!empty($f_counsel_name)) $sql .= 'and case_m_nm like \'%'.$f_counsel_name.'%\'';
	if ($f_type != 'all') $sql .= 'and \'CASE\' = \''.$f_type.'\'';

	
	$sql .=			 ' UNION ALL
					/*
					 * 상태변화일지 리스트
					 */
					 SELECT \'STAT\' AS type
					 ,      org_no
					 ,      DATE_FORMAT(reg_dt,\'%Y%m\')
					 ,      0
					 ,      reg_dt as dt
					 ,      jumin
					 ,      reg_nm
					   FROM counsel_client_state
					  WHERE org_no = \''.$code.'\'
						AND LEFT(DATE_FORMAT(reg_dt,\'%Y%m\'),'.StrLen($yymm).') = \''.$yymm.'\'';

	if ($f_type != 'all') $sql .= ' AND \'STAT\' = \''.$f_type.'\'';
	

	$sql .=		  ') as t
			 inner join m03sugupja
				on m03_ccode = org_no
			   and m03_jumin = c_cd
			   and m03_mkind = '.$conn->_client_kind().'';

	if (!empty($f_su_name)) $sql .= 'and m03_name like \'%'.$f_su_name.'%\'';

	$sql .=	'order by dt desc, c_nm';

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
		echo '<td class=\'left\'>'.$row['c_nm'].'</td>';
		echo '<td class=\'left\'>'.$row['m_nm'].'</td>';
		echo '<td class=\'left\'>'.$row['type_nm'].'</td>';
		echo '<td class=\'left last\'>';

		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_client_proc_counsel_reg("body","'.$row['type_cd'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$ed->en($row['c_cd']).'","'.str_replace('-','',$row['dt']).'");\'>수정</button></span> ';
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_client_proc_counsel_show("'.$row['type_cd'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$ed->en($row['c_cd']).'","'.str_replace('-','',$row['dt']).'");\'>출력</button></span> ';
		echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_client_proc_counsel_delete("'.$row['type_cd'].'","'.$row['yymm'].'","'.$row['seq'].'","'.$ed->en($row['c_cd']).'","'.str_replace('-','',$row['dt']).'");\'>삭제</button></span> ';
		echo '</td>';
		echo '</tr>';

		/*************************************
			전체출력을위한 환경변수
		*************************************/
		echo '<input type=\'hidden\' id=\'type_'.$i.'\' name=\'type_'.$i.'\' value=\''.$row['type_cd'].'\'>';
		echo '<input type=\'hidden\' id=\'yymm_'.$i.'\' name=\'yymm_'.$i.'\' value=\''.$row['yymm'].'\'>';
		echo '<input type=\'hidden\' id=\'seq_'.$i.'\' name=\'seq_'.$i.'\' value=\''.$row['seq'].'\'>';
		echo '<input type=\'hidden\' id=\'jumin_'.$i.'\' name=\'jumin_'.$i.'\' value=\''.$ed->en($row['c_cd']).'\'>';
		echo '<input type=\'hidden\' id=\'regDt_'.$i.'\' name=\'regDt_'.$i.'\' value=\''.$row['dt'].'\'>';
	}

	$conn->row_free();
	
	//전체출력을 위한 행 카운트
	echo '<input type=\'hidden\' id=\'row_cnt\' name=\'row_cnt\' value=\''.$row_count.'\'>';
	echo '<input type=\'hidden\' id=\'root\' name=\'root\' value=\'CLIENT\'>';


	echo '</tbody>';
	echo '<tfoot>';
	echo '<tr>';

	if ($row_count > 0)
		echo '<td class=\'left last bottom\' colspan=\'6\'>'.$myF->message($row_count, 'N').'</td>';
	else
		echo '<td class=\'center last\' colspan=\'6\'>'.$myF->message('nodata', 'N').'</td>';

	echo '</tr>';
	echo '</tfoot>';
	echo '</table>';

	include_once('../inc/_db_close.php');
?>
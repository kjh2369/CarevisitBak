<?php
	// Include the main TCPDF library (search for installation path).
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	require_once('tcpdf_include.php');


	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	//$pdf->setHeaderFont(array('nanumbarungothicyethangul','','10'));
	$pdf->SetHeaderData('', 0, '', '', array(255,255,255), array(255,255,255));
	//$pdf->setFooterData(array(0,64,0), array(0,64,128));
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

	$pdf->font_name_kor = 'nanumbarungothicyethangul';

	$code	= $_GET['code'];			//기관코드
	$kind	= '0';						//기관분류
	$year	= $_GET['year'];			//년
	$month	= $_GET['month'];			//월
	//$height_1 = 22;					//Line 시작지점 높이
	//$heigth_2 = 28;      				//Line 끝지점 높이
	$mobileYn = $_GET['mobileYn'];		//모바일출력여부


	$dept  = $_GET['dept'];				//부서


	if ($year == 'undefined') $year = '';
	if ($month == 'undefined') $month = '';
	
	if($mobileYn == 'Y'){
		$sql = 'select m02_yjumin as jumin
				  from m02yoyangsa
				 where m02_ccode  = \''.$code.'\'
				   and m02_key	  = \''.$_GET['key'].'\'
				 order by m02_mkind
				 limit 1';
		$mem = $conn -> get_data($sql);
	}

	$pdf->year	= $year;
	$pdf->month	= $month;
	$pdf->center_name	= $myF->euckr($_SESSION['userCenterName']);


	//서비스별 내역
	$sql = 'SELECT	salary_jumin AS jumin
			,		salary_kind AS svc_cd
			,		salary_work_days AS work_days
			,		salary_work_hours AS work_time
			,		salary_work_pay AS work_pay
			,		salary_deal_pay AS deal_pay
			,		salary_work_date AS work_date
			FROM	salary_basic_dtl
			WHERE	org_no		= \''.$code.'\'
			AND		salary_yymm = \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$basicDtl[$row['jumin']][$row['svc_cd']]['workDays'] = $row['work_days']; //근무일수
		$basicDtl[$row['jumin']][$row['svc_cd']]['workTime'] = $row['work_time']; //근무시간

		if ($row['svc_cd'] == '12'){
			$basicDtl[$row['jumin']]['familyWorkDate'] = $row['work_date'];
		}else{
			$basicDtl[$row['jumin']]['otherWorkDate'] .= $row['work_date'];
		}
	}

	$conn->row_free();

	/*
	//가족케어 근무일수
	$familyWorkDate = Explode('/', $familyWorkDate);
	$familyWorkDate = array_unique($familyWorkDate);
	$familyWorkDate = count($familyWorkDate) - 1;

	if ($familyWorkDate < 0) $familyWorkDate = 0;

	//일반케어 근무일수
	$otherWorkDate = Explode('/', $otherWorkDate);
	$otherWorkDate = array_unique($otherWorkDate);
	$otherWorkDate = count($otherWorkDate) - 1;

	if ($otherWorkDate < 0) $otherWorkDate = 0;
	*/

	$sql = "select m00_store_nm, m00_ctel
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";

	@$temp_center = $conn->get_array($sql);
	$center_name = $temp_center['m00_store_nm'];
	$center_tel  = $myF->phoneStyle($temp_center['m00_ctel']);
	unset($temp_center);

	$sql = 'select dept_cd as cd
					,      dept_nm as nm
					  from dept
					 where org_no   = \''.$code.'\'
					   and del_flag = \'N\'
					 order by order_seq';

	if ($mem == 'all' || $_GET['member'] == 'dtl_all'){


		$sql = "select salary_jumin
				  from salary_basic

				 inner join (
					   select m02_ccode as code
					   ,      min(m02_mkind) as kind
					   ,      m02_yjumin as jumin
					   ,      m02_yname as name
					   ,      m02_yipsail as join_dt
					   ,      m02_ytoisail as out_dt
					   ,	  m02_dept_cd as dept_cd
						 from m02yoyangsa
						where m02_ccode    = '$code'
						  and left(m02_yipsail, 6) <= '$year$month'
						  and case when left(m02_ytoisail, 6) != '' then m02_ytoisail else '999999' end >= '$year$month'
						  and m02_ygoyong_stat != '2'
						group by m02_ccode, m02_yjumin, m02_yname
					   ) as mem
					on mem.code  = salary_basic.org_no
				   and mem.jumin = salary_basic.salary_jumin

				 where org_no      = '$code'
				   and salary_yymm = '$year$month'";

		if (!empty($dept)) $sql .= "  and dept_cd = '$dept'";

		$sql .=	"order by name";
	
		@$conn->query($sql);
		@$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$member_list[$i] = $row['salary_jumin'];
		}

		$conn->row_free();

		$member_count = $row_count;
	}else{
		$member_list[0] = $mem;
		$member_count	= 1;

		if (!$year && !$month){
			$member_list[1] = $mem;
			$member_count	= 2;
		}
	}



	if ($mem == 'all' || $_GET['member'] == 'dtl_all' || Is_Numeric(str_replace('-','',$mem))){

		//직원 일정 정보
		for($i=1; $i<=2; $i++){
			if (!Empty($sl)){
				$sl .= ' UNION ALL ';
			}

			$sl .= 'SELECT t01_mkind AS kind
					,      t01_sugup_date AS dt
					,      t01_svc_subcode AS svc_cd
					,      t01_conf_fmtime AS conf_from
					,      t01_conf_totime AS conf_to
					,      t01_conf_soyotime AS conf_time
					,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end
												when \'500\' then \'목욕\'
												when \'800\' then \'간호\'
												when \'21\'  then \'간병\'
												when \'22\'  then \'노인\'
												when \'23\'  then \'산모\'
												when \'24\'  then \'장애\'
												when \'31\'  then \'산유\'
												when \'32\'  then \'병원\'
												when \'33\'  then \'기타\' else \'-\' end AS svc_nm
					,      t01_jumin AS c_cd
					,      case t01_status_gbn when \'1\' then t01_conf_suga_value else 0 end AS conf_pay
					,      t01_yoyangsa_id'.$i.' AS mem_cd
					  FROM t01iljung
					 WHERE t01_ccode               = \''.$code.'\'
					   AND left(t01_sugup_date, 6) = \''.$year.$month.'\'
					   AND t01_del_yn              = \'N\'
					   AND t01_status_gbn = \'1\'';

			if (Is_Numeric($mem)){
				$sl .= ' AND t01_yoyangsa_id'.$i.' = \''.$mem.'\'';
			}else{
				if ($i > 1){
					$sl .= ' AND t01_yoyangsa_id'.$i.' != \'\'';
				}
			}
		}

		$sql = 'SELECT kind
				,	   mem_cd
				,      svc_cd
				,      dt, conf_from, conf_to, conf_time, conf_pay
				,      svc_nm
				,      c_cd
				,      m03_name AS c_nm
				  FROM ('.$sl.') AS t
				 INNER JOIN m03sugupja
					ON m03_ccode = \''.$code.'\'
				   AND m03_mkind = t.kind
				   AND m03_jumin = t.c_cd
				 ORDER BY mem_cd, dt, conf_from';

		//if($debug) echo nl2br($sql); 
		@$conn->query($sql);
		@$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($key1 != $row['mem_cd']){
				$key1 = $row['mem_cd'];
				$seq = 0;
			}

			$arrMemIljung[$key1][$seq] = Array(
					'mem_cd'	=>	$row['mem_cd']
				,	'kind'		=>	$row['kind']
				,	'svc_cd'	=>	$row['svc_cd']
				,	'dt'		=>	$row['dt']
				,	'conf_from'	=>	$row['conf_from']
				,	'conf_to'	=>	$row['conf_to']
				,	'conf_time'	=>	$row['conf_time']
				,	'conf_pay'	=>	$row['conf_pay']
				,	'svc_nm'	=>	$row['svc_nm']
				,	'c_cd'		=>	$row['c_cd']
				,	'c_nm'		=>	$row['c_nm']
			);

			$seq ++;
		}

		$conn->row_free();
	}

	for($m_index=0; $m_index<$member_count; $m_index++){

		if($_GET['member'] == 'dtl_all'){
			$pdf->pos = 0;
		}else {
			$pdf->pos = $m_index % 2;
		}

		$jumin    = $member_list[$m_index];
		$sql = "select m02_yname as name
				,      m02_rank_pay as pay
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_yjumin = '$jumin'
				 order by m02_mkind
				 limit 1";

		@$member = $conn->get_array($sql);

		$pdf->member_name	= $member['name'];
		$rank_pay			= $member['pay'];

		if (!$pdf->member_name) $pdf->member_name = '          ';


		/*****************************
		2012.08.20 김주완
		포괄임금제 적용여부 조회
		*****************************/
		$sql = 'SELECT	extra_yn
				,		deal_bipay_yn
				,		deal_hourly_yn
				,		family_extra_yn
				,		family_bipay_yn
				FROM	salary_option
				WHERE	org_no       = \''.$code.'\'
				AND		salary_yymm  = \''.$year.$month.'\'
				AND		salary_jumin = \''.$jumin.'\'';

		@$row	= $conn->get_array($sql);

		$lsExtraYn		= $row['extra_yn'];			//초과근무수당 포함여부
		$lsDealBipayYn	= $row['deal_bipay_yn'];	//처우개선비 비급여 처리 여부
		$lsDealHourlyYn	= $row['deal_hourly_yn'];	//처우개선비 급여에 포함여부
		$familyExtraPayYn	= $row['family_extra_yn']; //가족케어 수당여부
		$familyBipayYn		= $row['family_bipay_yn']; //가족케어 비급여 여부

		if ($lsDealBipayYn != 'Y' && $lsDealHourlyYn != 'Y'){
			$lsDealInYn	= 'N';
		}else{
			$lsDealInYn	= 'Y';
		}

		if ($lsDealHourlyYn == 'X'){
			$lsDealInYn	= 'X';
		}

		// 급여데이타
		$sql = "select salary_basic.work_cnt
				,      salary_basic.work_time
				,      salary_basic.weekly_cnt
				,      salary_basic.paid_cnt
				,      salary_basic.bath_cnt
				,      salary_basic.nursing_cnt
				,	   salary_basic.annual_days

				,      salary_basic.prolong_hour
				,      salary_basic.night_hour
				,      salary_basic.holiday_hour
				,      salary_basic.holiday_prolong_hour
				,      salary_basic.holiday_night_hour
				,      salary_basic.prolong_hour + salary_basic.night_hour + salary_basic.holiday_hour + salary_basic.holiday_prolong_hour + salary_basic.holiday_night_hour as tot_sudang_hour

				,      salary_basic.base_pay
				,      salary_basic.weekly_pay
				,      salary_basic.paid_pay
				,      salary_basic.bath_pay
				,      salary_basic.nursing_pay
				,      salary_basic.meal_pay
				,      salary_basic.car_keep_pay
				,      salary_basic.bojeon_pay
				,      salary_basic.annual_pay
				,      salary_basic.base_pay
					 + salary_basic.weekly_pay
					 + salary_basic.paid_pay
					 + salary_basic.bath_pay
					 + salary_basic.nursing_pay
					 + salary_basic.meal_pay
					 + salary_basic.car_keep_pay
					 + salary_basic.bojeon_pay
					 + salary_basic.annual_pay
					 + salary_basic.dementia_pay
					 + salary_basic.family_extra_pay
					 + salary_basic.family_deal_pay
					 + ".($lsDealInYn != "Y" ? "salary_basic.deal_pay" : "0")." as tot_basic_pay

				,		salary_basic.family_extra_pay
				,		salary_basic.family_deal_pay

				,      salary_basic.prolong_pay
				,      salary_basic.night_pay
				,      salary_basic.holiday_pay
				,      salary_basic.holiday_prolong_pay
				,      salary_basic.holiday_night_pay
				,      salary_basic.rank_pay
				,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay as tot_sudang_pay

				,      salary_basic.pension_amt
				,      salary_basic.health_amt
				,      salary_basic.care_amt
				,      salary_basic.employ_amt
				,      salary_basic.pension_amt + salary_basic.health_amt + salary_basic.care_amt + salary_basic.employ_amt as tot_ins_pay

				,      salary_basic.tax_amt_1
				,      salary_basic.tax_amt_2
				,      salary_basic.tax_amt_1 + salary_basic.tax_amt_2 as tot_tax_pay

				,      salary_basic.expense_days
				,      salary_basic.expense_hours
				,      salary_basic.expense_pay

				,      salary_basic.bipay_addpay
				,	   salary_basic.real_pay

				,      salary_amt.basic_total_amt
				,      salary_amt.addon_total_amt
				,      salary_amt.total_amt
				,      salary_amt.basic_deduct_amt
				,      salary_amt.addon_deduct_amt
				,      salary_amt.deduct_amt
				,      salary_amt.diff_amt

				,	   salary_basic.paye_yn

				,      salary_basic.deal_pay
				,	   salary_basic.dementia_pay
				,	   salary_other.min_apply_yn
				  from salary_basic
				  left join salary_amt
					on salary_amt.org_no       = salary_basic.org_no
				   and salary_amt.salary_yymm  = salary_basic.salary_yymm
				   and salary_amt.salary_jumin = salary_basic.salary_jumin
				  LEFT	JOIN	salary_other
					ON salary_other.org_no       = salary_basic.org_no
				   AND salary_other.salary_yymm  = salary_basic.salary_yymm
				   AND salary_other.salary_jumin = salary_basic.salary_jumin
				 where salary_basic.org_no       = '$code'
				   and salary_basic.salary_yymm  = '$year$month'
				   and salary_basic.salary_jumin = '$jumin'";

		@$salary = $conn->get_array($sql);

		//if (!$salary['rank_pay']) $salary['rank_pay'] = $rank_pay;

		//2012.08.20 김주완 재수당 체크 시 근무수당 합계(A)에 합산
		if ($lsExtraYn == 'Y'){
			$salary['tot_basic_pay'] += $salary['tot_sudang_pay'];
		}

		$sql = "select salary_type
				,      salary_index
				,      salary_pay
				,	   salary_subject
				  from salary_addon_pay
				 where org_no       = '$code'
				   and salary_yymm  = '$year$month'
				   and salary_jumin = '$jumin'";
		@$conn->query($sql);
		@$conn->fetch();
		$row_count = $conn->row_count();

		$index[1] = 0;
		$index[2] = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$salary_addon[$row['salary_type']][$index[$row['salary_type']]]['subject'] = $row['salary_subject'];
			$salary_addon[$row['salary_type']][$index[$row['salary_type']]]['pay']     = $row['salary_pay'];

			$index[$row['salary_type']] ++;
		}

		$addon_pay[1] = intval($salary['rank_pay']);
		$addon_pay[2] = 0;

		for($i=1; $i<=2; $i++){
			$tmpAddonCnt = sizeof($salary_addon[$i]);

			for($j=0; $j<$tmpAddonCnt; $j++){
				$addon_pay[$i] += $salary_addon[$i][$j]['pay'];
			}
		}

		$conn->row_free();
		

		$ww = 124.2;

		if ($pdf->pos == 0) $pdf->AddPage('L', 'A4');
		$pdf->SetFillColor(220,220,220);

		$pdf->SetFont($pdf->font_name_kor,'B',17);

		$pdf->set_default_xy();
		
		$pdf->Cell($ww, $pdf->row_height, ($year ? IntVal($year) : '   ').'년 '.($month ? intval($month) : '    ').'월 급여명세서', 0, 1, 'C');

		$pdf->set_default_xy();
		$pdf->set_font(11);
		$pdf->Cell($ww / 2, $pdf->text_height, $center_name.'('.$center_tel.')', 0, 0, 'L');
		$pdf->Cell($ww / 2, $pdf->text_height, '성명 : '.$pdf->member_name, 0, 1, 'R');

		$pdf->set_default_xy();
		$pdf->set_caption_font('B');
		$pdf->Cell($ww * 0.36, $pdf->row_height, '급여총액(A'.($lsExtraYn != 'Y' ? '+B' : '').'+E+G)', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.32, $pdf->row_height, '공제금액(C+D+F)', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.32, $pdf->row_height, '차인지급액', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.36, $pdf->text_height, number_format($salary['tot_basic_pay']+$salary['tot_sudang_pay']+$addon_pay[1]) != 0 ? number_format($salary['tot_basic_pay']+($lsExtraYn != 'Y' ? $salary['tot_sudang_pay'] : 0) +$salary['expense_pay']+$salary['bipay_addpay']+$salary['real_pay']+$addon_pay[1]) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.32, $pdf->text_height, number_format($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2]) != 0 ? number_format($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2]) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.32, $pdf->text_height, number_format(($salary['tot_basic_pay']+$salary['tot_sudang_pay']+$salary['real_pay']+$addon_pay[1])-($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2])) != 0 ? number_format($salary['tot_basic_pay']+($lsExtraYn != 'Y' ? $salary['tot_sudang_pay'] : 0) +$salary['expense_pay']+$salary['bipay_addpay']+$salary['real_pay']+$addon_pay[1]-($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2])) : '', 1, 1, 'C');

		$pdf->set_default_xy(0, 2);
		$pdf->set_caption_font('B');
		$pdf->Cell($ww, $pdf->row_height, '기본근무(기본급여)(A)', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_caption_font();
		$pdf->Cell($ww * 0.12, $pdf->row_height, '근무일수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.14, $pdf->row_height, '근무시간', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.14, $pdf->row_height, '주휴일수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.14, $pdf->row_height, '연차일수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.14, $pdf->row_height, '유급일수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '목욕횟수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '간호횟수', 1, 1, 'C', true);

		if($IsDementiaPay){
			if ($familyExtraPayYn == 'Y'){
				$workCnt = $basicDtl[$jumin]['otherWorkDate'];
				$workCnt = Explode('/', $workCnt);
				$workCnt = array_unique($workCnt);
				$workCnt = count($workCnt) - 1;

				if ($workCnt < 0) $workCnt = 0;
			}else{
				$workCnt = $salary['work_cnt'];
			}

			$pdf->set_default_xy();
			$pdf->set_text_font();
			$pdf->Cell($ww * 0.12, $pdf->text_height + $pdf->row_height, $workCnt ? $workCnt : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['work_time'], 1) != 0 ? number_format($salary['work_time'], 1) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['weekly_cnt']) != 0 ? number_format($salary['weekly_cnt']) : '', 1, 0, 'C');

			//if($salary['min_apply_yn'] == 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['annual_days'],2) != 0 ? number_format($salary['annual_days'],2) : '', 1, 0, 'C');
			//}else {
			//	$pdf->Cell($ww * 0.14, $pdf->text_height, '', 1, 0, 'C');
			//}

			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['paid_cnt']) != 0 ? number_format($salary['paid_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['bath_cnt']) != 0 ? number_format($salary['bath_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['nursing_cnt']) != 0 ? number_format($salary['nursing_cnt']) : '', 1, 1, 'C');

			$pdf->set_default_xy($ww * 0.12);
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['base_pay']) != 0 ? number_format($salary['base_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['weekly_pay']) != 0 ? number_format($salary['weekly_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['annual_pay']) != 0 ? number_format($salary['annual_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['paid_pay']) != 0 ? number_format($salary['paid_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['bath_pay']) != 0 ? number_format($salary['bath_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['nursing_pay']) != 0 ? number_format($salary['nursing_pay']) : '', 1, 1, 'R');

			$pdf->set_default_xy();
			$pdf->set_caption_font();
			$pdf->Cell($ww * 0.12, $pdf->row_height, '식대보조', 1, 0, 'C', true);
			$pdf->Cell($ww * 0.14, $pdf->row_height, '차량유지', 1, 0, 'C', true);
			$pdf->Cell($ww * 0.14, $pdf->row_height, '업무수당', 1, 0, 'C', true);

			if ($lsDealInYn != 'Y' && $lsDealInYn != 'X'){
				$pdf->Cell($ww * 0.14, $pdf->row_height, '처우개선비', 1, 0, 'C', true);
			}

			/*************************
				2012.08.20 추가
				재수당 유무일때 근무수당 항목 출력유무
			**************************/
			if($lsExtraYn == 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->row_height, '근무수당', 1, 0, 'C', true);
				$w = 0.18;
				$str = '';
			}else {
				$w = 0.32;
				$str = '(B)';
			}

			if($IsDementiaPay){
				$pdf->Cell($ww * ($lsExtraYn == 'Y' ? 0.16 : 0.14), $pdf->row_height, '치매수당', 1, 0, 'C', true);
				$w = 0.16;
				$str = '';
			}else {
				$w = 0.32;
				$str = '(B)';
			}

			if ($lsDealInYn == 'Y' || $lsDealInYn == 'X'){
				$w	+= 0.14;
			}

			$pdf->Cell($ww * $w, $pdf->row_height, '합계(A)', 1, 1, 'C', true);

			$pdf->set_default_xy();
			$pdf->set_text_font();
			$pdf->Cell($ww * 0.12, $pdf->text_height, number_format($salary['meal_pay']) != 0 ? number_format($salary['meal_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['car_keep_pay']) != 0 ? number_format($salary['car_keep_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['bojeon_pay']) != 0 ? number_format($salary['bojeon_pay']) : '', 1, 0, 'R');

			if ($lsDealInYn != 'Y' && $lsDealInYn != 'X'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, !Empty($salary['deal_pay']) ? Number_Format($salary['deal_pay']) : '', 1, 0, 'R');
			}

			/*************************
				2012.08.20 추가
				재수당 유무일때 근무수당 데이터 출력유무
			**************************/
			if($lsExtraYn == 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['tot_sudang_pay']) != 0 ? ($lsExtraYn == 'Y' ? number_format($salary['tot_sudang_pay']) : '') : '', 1, 0, 'R');
			}

			if($IsDementiaPay){
				$pdf->Cell($ww * ($lsExtraYn == 'Y' ? 0.16 : 0.14), $pdf->text_height, number_format($salary['dementia_pay']) != 0 ? ($salary['dementia_pay'] != '' ? number_format($salary['dementia_pay']) : '') : '', 1, 0, 'R');
			}

			$pdf->Cell($ww * $w, $pdf->text_height * ($familyExtraPayYn == 'Y' ? 3 : 1), number_format($salary['tot_basic_pay']) != 0 ? number_format($salary['tot_basic_pay']) : '', 1, 1, 'R');
		}else {
			$pdf->set_default_xy();
			$pdf->set_text_font();
			$pdf->Cell($ww * 0.12, $pdf->text_height * 3 + $pdf->row_height, number_format($salary['work_cnt']) != 0 ? number_format($salary['work_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['work_time'], 1) != 0 ? number_format($salary['work_time'], 1) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['weekly_cnt']) != 0 ? number_format($salary['weekly_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['annual_days'],2) != 0 ? number_format($salary['annual_days'],2) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['paid_cnt']) != 0 ? number_format($salary['paid_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['bath_cnt']) != 0 ? number_format($salary['bath_cnt']) : '', 1, 0, 'C');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['nursing_cnt']) != 0 ? number_format($salary['nursing_cnt']) : '', 1, 1, 'C');

			$pdf->set_default_xy($ww * 0.12);
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['base_pay']) != 0 ? number_format($salary['base_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['weekly_pay']) != 0 ? number_format($salary['weekly_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['annual_pay']) != 0 ? number_format($salary['annual_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['paid_pay']) != 0 ? number_format($salary['paid_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['bath_pay']) != 0 ? number_format($salary['bath_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['nursing_pay']) != 0 ? number_format($salary['nursing_pay']) : '', 1, 1, 'R');

			$pdf->set_default_xy($ww * 0.12);
			$pdf->set_caption_font();
			$pdf->Cell($ww * 0.14, $pdf->row_height, '식대보조', 1, 0, 'C', true);
			$pdf->Cell($ww * 0.14, $pdf->row_height, '차량유지', 1, 0, 'C', true);
			$pdf->Cell($ww * 0.14, $pdf->row_height, '업무수당', 1, 0, 'C', true);

			if ($lsDealInYn != 'Y' && $lsDealInYn != 'X'){
				$pdf->Cell($ww * 0.14, $pdf->row_height, '처우개선비', 1, 0, 'C', true);
			}

			/*************************
				2012.08.20 추가
				재수당 유무일때 근무수당 항목 출력유무
			**************************/
			if($lsExtraYn == 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->row_height, '근무수당', 1, 0, 'C', true);
				$w = 0.18;
				$str = '';
			}else {
				$w = 0.32;
				$str = '(B)';
			}
			if ($lsDealInYn == 'Y' || $lsDealInYn == 'X'){
				$w	+= 0.14;
			}

			$pdf->Cell($ww * $w, $pdf->row_height, '합계(A)', 1, 1, 'C', true);

			$pdf->set_default_xy($ww * 0.12);
			$pdf->set_text_font();
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['meal_pay']) != 0 ? number_format($salary['meal_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['car_keep_pay']) != 0 ? number_format($salary['car_keep_pay']) : '', 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['bojeon_pay']) != 0 ? number_format($salary['bojeon_pay']) : '', 1, 0, 'R');

			if ($lsDealInYn != 'Y' && $lsDealInYn != 'X'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, !Empty($salary['deal_pay']) ? Number_Format($salary['deal_pay']) : '', 1, 0, 'R');
			}

			/*************************
				2012.08.20 추가
				재수당 유무일때 근무수당 데이터 출력유무
			**************************/
			if($lsExtraYn == 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, number_format($salary['tot_sudang_pay']) != 0 ? ($lsExtraYn == 'Y' ? number_format($salary['tot_sudang_pay']) : '') : '', 1, 0, 'R');
			}
			$pdf->Cell($ww * $w, $pdf->text_height * ($familyExtraPayYn == 'Y' ? 3 : 1), number_format($salary['tot_basic_pay']) != 0 ? number_format($salary['tot_basic_pay']) : '', 1, 1, 'R');
		}

		if ($familyExtraPayYn == 'Y'){
			$pdf->set_default_xy(0, $pdf->text_height * 2 * -1);
			$pdf->Cell($ww * 0.12, $pdf->text_height, '가족일수', 1, 0, 'C', 1);
			$pdf->Cell($ww * 0.14, $pdf->text_height, '가족시간', 1, 0, 'C', 1);
			$pdf->Cell($ww * 0.14, $pdf->text_height, ' ', 1, 0, 'C', 1);

			if ($lsDealBipayYn != 'Y' && $lsDealHourlyYn != 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, '가족처개비', 1, 0, 'C', 1);
			}else{
				$pdf->Cell($ww * 0.14, $pdf->text_height, '', 1, 0, 'C', 1);
			}
			$pdf->Cell($ww * 0.14, $pdf->text_height, '가족수당', 1, 1, 'C', 1);

			$pdf->set_default_xy();
			$pdf->Cell($ww * 0.12, $pdf->text_height, $basicDtl[$jumin]['12']['workDays'], 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, $basicDtl[$jumin]['12']['workTime'], 1, 0, 'R');
			$pdf->Cell($ww * 0.14, $pdf->text_height, ' ', 1, 0, 'R');

			if ($lsDealBipayYn != 'Y' && $lsDealHourlyYn != 'Y'){
				$pdf->Cell($ww * 0.14, $pdf->text_height, $salary['family_deal_pay'] ? number_format($salary['family_deal_pay']) : '', 1, 0, 'R');
			}else{
				$salary['family_extra_pay'] += $salary['family_deal_pay'];
			}
			$pdf->Cell($ww * 0.14, $pdf->text_height, $salary['family_extra_pay'] ? number_format($salary['family_extra_pay']) : '', 1, 1, 'R');
		}

		$pdf->set_default_xy(0, 2);
		$pdf->set_caption_font('B');
		$pdf->Cell($ww, $pdf->row_height, '초과근무'.$str, 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_caption_font();
		$pdf->Cell($ww * 0.16, $pdf->row_height, '연장', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '야간', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '휴일', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '휴일연장', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '휴일야간', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '합계'.$str, 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['prolong_hour'], 1) != 0 ? number_format($salary['prolong_hour'], 1) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['night_hour'], 1) != 0 ? number_format($salary['night_hour'], 1) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['holiday_hour'], 1) != 0 ? number_format($salary['holiday_hour'], 1) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['holiday_prolong_hour'], 1) != 0 ? number_format($salary['holiday_prolong_hour'], 1) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['holiday_night_hour'], 1) != 0 ? number_format($salary['holiday_night_hour'], 1) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['tot_sudang_hour'], 1) != 0 ? number_format($salary['tot_sudang_hour'], 1) : '', 1, 1, 'C');

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['prolong_pay']) != 0 ? number_format($salary['prolong_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['night_pay']) != 0 ? number_format($salary['night_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['holiday_pay']) != 0 ? number_format($salary['holiday_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['holiday_prolong_pay']) != 0 ? number_format($salary['holiday_prolong_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['holiday_night_pay']) != 0 ? number_format($salary['holiday_night_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['tot_sudang_pay']) != 0 ? number_format($salary['tot_sudang_pay']) : '', 1, 1, 'R');

		$pdf->set_default_xy(0, 2);
		$pdf->set_caption_font('B');
		$pdf->Cell($ww * 0.16, $pdf->row_height + $pdf->text_height, "보험(C)", 1, 0, 'C', true);
		$pdf->set_caption_font('');
		$pdf->Cell($ww * 0.16, $pdf->row_height, '국민연금', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '건강보험', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '장기요양', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '고용보험', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '합계(C)', 1, 1, 'C', true);

		$pdf->set_default_xy($ww * 0.16);
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['pension_amt']) != 0 ? number_format($salary['pension_amt']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['health_amt']) != 0 ? number_format($salary['health_amt']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['care_amt']) != 0 ? number_format($salary['care_amt']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['employ_amt']) != 0 ? number_format($salary['employ_amt']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['tot_ins_pay']) != 0 ? number_format($salary['tot_ins_pay']) : '', 1, 1, 'R');

		$pdf->set_default_xy(0, 2);
		$pdf->set_caption_font('B');
		$pdf->Cell($ww * 0.16, $pdf->row_height + $pdf->text_height, $salary['paye_yn'] == '1' ? '원천징수' : '소득세(D)', 1, 0, 'C', true);
		$pdf->set_caption_font('');
		$pdf->Cell($ww * 0.16, $pdf->row_height, '갑근세', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '주민세', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '합계(D)', 1, 1, 'C', true);

		if ($lsDealInYn == 'Y' && $salary['deal_pay'] > 0){
			$pdf->set_default_xy();
			$pdf->Text($pdf->GetX() + $ww * 0.16 * 4 + 1, $pdf->GetY() - $pdf->row_height + 3, "※급여에 처우개선비");
			$pdf->SetTextColor(255,0,0);
			$pdf->Text($pdf->GetX() + $ww * 0.16 * 4 + 5, $pdf->GetY() + 1, Number_Format($salary['deal_pay'])."원");
			$pdf->SetTextColor(0,0,0);
			$pdf->Text($pdf->GetX() + $ww * 0.16 * 4 + 5, $pdf->GetY() + $pdf->row_height - 1, "이 포함되어 있습니다.");
		}

		$pdf->set_default_xy($ww * 0.16);
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['tax_amt_1']) != 0 ? number_format($salary['tax_amt_1']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['tax_amt_2']) != 0 ? number_format($salary['tax_amt_2']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['tot_tax_pay']) != 0 ? number_format($salary['tot_tax_pay']) : '', 1, 1, 'R');

		$pdf->set_default_xy(0, 2);
		$pdf->set_caption_font('B');
		$pdf->Cell($ww * 0.16, $pdf->row_height + $pdf->text_height, '비과세(G)', 1, 0, 'C', true);
		$pdf->set_caption_font('');
		$pdf->Cell($ww * 0.16, $pdf->row_height, '일수', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '시간', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.16, $pdf->row_height, '단가', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '금액', 1, 0, 'C', true);
		$pdf->Cell($ww * 0.18, $pdf->row_height, '실비지급', 1, 1, 'C', true);

		$pdf->set_default_xy($ww * 0.16);
		$pdf->set_text_font();
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['expense_days']) != 0 ? number_format($salary['expense_days']) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['expense_hours']) != 0 ? number_format($salary['expense_hours']) : '', 1, 0, 'C');
		$pdf->Cell($ww * 0.16, $pdf->text_height, number_format($salary['expense_pay']) != 0 ? number_format($salary['expense_pay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['bipay_addpay']) != 0 ? number_format($salary['bipay_addpay']) : '', 1, 0, 'R');
		$pdf->Cell($ww * 0.18, $pdf->text_height, number_format($salary['real_pay']) != 0 ? number_format($salary['real_pay']) : '', 1, 1, 'R');

		$pdf->set_default_xy(0, 2);
		$temp_y = $pdf->GetY();
		$pdf->set_caption_font('B');
		$pdf->Cell($ww * 0.48, $pdf->row_height, '지 급 수 당(+)(E)', 1, 1, 'C', true);

		$pdf->set_text_font();
		$pdf->set_default_xy();
		$pdf->Cell($ww * 0.48 * 0.6, $pdf->text_height, '합계(E)', 1, 0, 'L', true);
		$pdf->Cell($ww * 0.48 * 0.4, $pdf->text_height, number_format($addon_pay[1]) != 0 ? number_format($addon_pay[1]) : '', 1, 1, 'R');

		$pdf->set_default_xy();
		$pdf->Cell($ww * 0.48 * 0.6, $pdf->text_height, '직급수당', 1, 0, 'L');
		$pdf->Cell($ww * 0.48 * 0.4, $pdf->text_height, number_format($salary['rank_pay']) != 0 ? number_format($salary['rank_pay']) : '', 1, 1, 'R');

		$count = sizeof($salary_addon[1]);

		for($i=0; $i<$count; $i++){
			if ($salary_addon[1][$i]['pay'] != 0){
				$pdf->set_default_xy();
				$pdf->Cell($ww * 0.48 * 0.6, $pdf->text_height, $salary_addon[1][$i]['subject'], 1, 0, 'L');
				$pdf->Cell($ww * 0.48 * 0.4, $pdf->text_height, number_format($salary_addon[1][$i]['pay']) != 0 ? number_format($salary_addon[1][$i]['pay']) : '', 1, 1, 'R');
			}
		}

		$pdf->set_caption_font('B');
		$pdf->set_default_xy($ww * 0.52, $temp_y - $pdf->GetY());
		$pdf->Cell($ww * 0.48, $pdf->row_height, '공 제 항 목(-)(F)', 1, 1, 'C', true);

		$pdf->set_text_font();
		$pdf->set_default_xy($ww * 0.52);
		$pdf->Cell($ww * 0.48 * 0.6, $pdf->text_height, '합계(F)', 1, 0, 'L', true);
		$pdf->Cell($ww * 0.48 * 0.4, $pdf->text_height, number_format($addon_pay[2]) != 0 ? number_format($addon_pay[2]) : '', 1, 1, 'R');

		$count = sizeof($salary_addon[2]);

		for($i=0; $i<$count; $i++){
			if ($salary_addon[2][$i]['pay'] != 0){
				$pdf->set_default_xy($ww * 0.52);
				$pdf->Cell($ww * 0.48 * 0.6, $pdf->text_height, $salary_addon[2][$i]['subject'], 1, 0, 'L');
				$pdf->Cell($ww * 0.48 * 0.4, $pdf->text_height, number_format($salary_addon[2][$i]['pay']) != 0 ? number_format($salary_addon[2][$i]['pay']) : '', 1, 1, 'R');
			}
		}

		unset($salary_addon);

		$pdf->SetY($pdf->top);

		$pdf->draw_border();

		if ($year && $month && ($_GET['member'] == 'dtl_all' || $mem != 'all')){
			$pdf->pos = 1;

			$pdf->SetFont($pdf->font_name_kor,'B',17);

			$pdf->set_default_xy();
			$pdf->Cell($ww, $pdf->row_height, IntVal($pdf->year).'년 '.intval($pdf->month).'월 근무현황', 0, 1, 'C');

			$pdf->set_default_xy(0, 2);
			$pdf->set_font(9);
			$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 0, 'C', true);
			$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 0, 'C', true);
			$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
			$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 1, 'C', true);

			if (Is_Array($arrMemIljung[$jumin])){
				$listL = 0;
				$idx = 0;
				$listCnt = SizeOf($arrMemIljung[$jumin]);
				$totCnt = 37;

				foreach($arrMemIljung[$jumin] as $i => $row){

					if ($i >= $totCnt * 3){
						//다음 페이지 일 경우
						$totCnt += 37;
						$pdf->AddPage('L', 'A4');
						$pdf->SetY($pdf->top);
						$pdf->pos = 0;

						$pdf->SetFont($pdf->font_name_kor,'B',17);

						$pdf->set_default_xy();
						$pdf->Cell($ww, $pdf->row_height, IntVal($pdf->year).'년 '.intval($pdf->month).'월 근무현황', 0, 1, 'C');

						$pdf->set_default_xy(0, 2);
						$pdf->set_font(9);
						$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 0, 'C', true);
						$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 0, 'C', true);
						$pdf->Cell($ww*0.06, $pdf->text_height, '일자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.14, $pdf->text_height, '수급자', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '구분', 1, 0, 'C', true);
						$pdf->Cell($ww*0.07, $pdf->text_height, '시간', 1, 1, 'C', true);


						$listL = 0;
						$idx = 0;
					}

					//소요시간
					$time = $myF->cutOff(IntVal($row['conf_time']),30);

					if($row['kind'] == 0){
						if($row['dt'] >= '201603'){
							if ($time >= 270 && $time < 480){
								$time -= 30;
							}
						}else {
							if ($time >= 270){
								$time -= 30;
							}
						}
					}


					if($row['kind'] == 4 && $row['dt'] >= '201701'){
						/*
						$time = $row['conf_time'] / 60;
						$tmpTime = $time % 60;

						if ($tmpTime >= 1  && $tmpTime < 29){
							$time += 30;
						}else {
							$time = $myF->cutOff(($time*60),30);
						}
						*/


					}


					$time /= 60;
					$time  = Number_Format($time,1);

					if ($listCnt >= 37 * 3){
						if ($idx >= 37){
							$listL += 42.2;
							$idx = 0;
							$pdf->SetY(28);
						}
					}else{
						if ($idx >= $listCnt / 3){
							$listL += 42.2;
							$idx = 0;
							$pdf->SetY(28);
						}
					}


					$pdf->set_default_xy($listL,0);

					$pdf->Cell($ww*0.06, 4.5, Number_Format(substr($row['dt'], -2)), 1, 0, 'C');
					$pdf->Cell($ww*0.14, 4.5, $row['c_nm'], 1, 0, 'L');
					$pdf->Cell($ww*0.07, 4.5, $row['svc_nm'], 1, 0, 'L');
					$pdf->Cell($ww*0.07, 4.5, $time, 1, 1, 'R');

					$idx ++;

						/*
					$row_cnt = SizeOf($arrMemIljung[$jumin])/3;

					if($i < ceil($row_cnt)){	//첫번째라인 정렬
						$height_1 = $height_1 + 5;
						$height_2 = $height_2 + 5;

						$pdf->set_default_xy();
						$pdf->Cell($ww*0.06, 5, Number_Format(substr($row['dt'], -2)), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');

					}else if($i < (round($row_cnt)+ceil($row_cnt))){ //두번째라인 정렬

						if($i == ceil($row_cnt)){
							$pdf->setY(28);
						}
						$pdf->set_default_xy(42.2,0);
						$pdf->Cell($ww*0.06, 5, substr($row['dt'], -2), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');
					}else { //세번째라인 정렬
						if($i == (round($row_cnt)+ceil($row_cnt))){
							$pdf->setY(28);
						}
						$pdf->set_default_xy(84.4,0);
						$pdf->Cell($ww*0.06, 5, substr($row['dt'], -2), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');
					}
					*/
				}
			}else{
				$pdf->set_default_xy();
				$pdf->Cell($ww*1.02, $pdf->text_height, '검색된데이터가없습니다.', 1, 1, 'C');
			}

			/*
			$sql = 'select t01_sugup_date as dt
					,      t01_svc_subcode as svc_cd
					,      t01_conf_fmtime as conf_from
					,      t01_conf_totime as conf_to
					,      t01_conf_soyotime as conf_time
					,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end
														 when \'500\' then \'목욕\'
														 when \'800\' then \'간호\'
														 when \'21\'  then \'간병\'
														 when \'22\'  then \'노인\'
														 when \'23\'  then \'산모\'
														 when \'24\'  then \'장애\'
														 when \'31\'  then \'산유\'
														 when \'32\'  then \'병원\'
														 when \'33\'  then \'기타\' else \'-\' end as svc_nm
					,      t01_jumin as c_cd
					,      m03_name as c_nm
					,      case t01_status_gbn when \'1\' then t01_conf_suga_value else 0 end as conf_pay
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 where t01_ccode               = \''.$code.'\'
					   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
					   and t01_yoyangsa_id1		   = \''.$jumin.'\'
					   and t01_del_yn              = \'N\'
					 union all
					select t01_sugup_date as dt
					,      t01_svc_subcode as svc_cd
					,      t01_conf_fmtime as conf_from
					,      t01_conf_totime as conf_to
					,      t01_conf_soyotime as conf_time
					,      case t01_svc_subcode when \'200\' then case when t01_mkind = \'0\' then \'요양\' else \'장애\' end
														 when \'500\' then \'목욕\'
														 when \'800\' then \'간호\'
														 when \'21\'  then \'간병\'
														 when \'22\'  then \'노인\'
														 when \'23\'  then \'산모\'
														 when \'24\'  then \'장애\'
														 when \'31\'  then \'산유\'
														 when \'32\'  then \'병원\'
														 when \'33\'  then \'기타\' else \'-\' end as svc_nm
					,      t01_jumin as c_cd
					,      m03_name as c_nm
					,      case t01_status_gbn when \'1\' then t01_conf_suga_value else 0 end as conf_pay
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 where t01_ccode               = \''.$code.'\'
					   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
					   and t01_yoyangsa_id2		   = \''.$jumin.'\'
					   and t01_del_yn              = \'N\'
					 order by dt, conf_from, conf_to';

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			if($row_count > 0 ){
				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					//소요시간
					$time = $myF->cutOff(IntVal($row['conf_time']),30);

					if ($time > 270){
						$time -= 30;
					}

					$time /= 60;
					$time  = Number_Format($time,1);

					$row_cnt = $row_count/3;

					if($i < ceil($row_cnt)){	//첫번째라인 정렬
						$height_1 = $height_1 + 5;
						$height_2 = $height_2 + 5;

						$pdf->set_default_xy();
						$pdf->Cell($ww*0.06, 5, Number_Format(substr($row['dt'], -2)), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						//$pdf->Cell($ww*0.16, 5, $myF->euckr($myF->_min2timeKor($row['conf_time'])), 1, 1, 'R');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');
					}else if($i < (round($row_cnt)+ceil($row_cnt))){ //두번째라인 정렬

						if($i == ceil($row_cnt)){
							$pdf->setY(28);
						}
						$pdf->set_default_xy(42.2,0);
						$pdf->Cell($ww*0.06, 5, substr($row['dt'], -2), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						//$pdf->Cell($ww*0.16, 5, $myF->euckr($myF->_min2timeKor($row['conf_time'])), 1, 1, 'R');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');
					}else { //세번째라인 정렬
						if($i == (round($row_cnt)+ceil($row_cnt))){
							$pdf->setY(28);
						}
						$pdf->set_default_xy(84.4,0);
						$pdf->Cell($ww*0.06, 5, substr($row['dt'], -2), 1, 0, 'C');
						$pdf->Cell($ww*0.14, 5, $row['c_nm'], 1, 0, 'L');
						$pdf->Cell($ww*0.07, 5, $row['svc_nm'], 1, 0, 'L');
						//$pdf->Cell($ww*0.16, 5, $myF->euckr($myF->_min2timeKor($row['conf_time'])), 1, 1, 'R');
						$pdf->Cell($ww*0.07, 5, $time, 1, 1, 'R');
					}
				}
			}else {
				$pdf->set_default_xy();
				$pdf->Cell($ww*1.02, $pdf->text_height, '검색된데이터가없습니다.', 1, 1, 'C');
			}

			$conn -> row_free();
			*/


			$pdf->SetY($pdf->top);
		}
	}
	
	
	$pdf->Output('급여명세서('.$year.'년'.$month.'월)','I');
	
	include_once('../inc/_db_close.php');
?>

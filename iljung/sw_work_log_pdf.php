<?
	//include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $var['jumin'];
	$yymm = $var['yymm'];
	$seq = $var['seq'];

	/*
	//수급자명
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);

	//등급 및 유효기간
	$sql = 'SELECT	level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		svc_cd = \'0\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'';

	$lvl = $conn->get_data($sql);

	if ($lvl){
		$lvl = $lvl.'등급';
	}else{
		$lvl = '';
	}
	*/

	
	//일지
	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);
	
	$dt = $row['date'] != '' ? $row['date'] : date('Ymd');

	//등급 및 유효기간
	$sql = 'SELECT	level
			,		app_no
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		jumin  = \''.$jumin.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m%d\') <= \''.$dt.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m%d\') >= \''.$dt.'\'';

	$lvl = $conn->get_data($sql);
	

	//요양보호사
	$sql = 'SELECT	t01_mem_cd1 AS mem_cd1
			,		t01_mem_nm1 AS mem_nm1
			,		t01_mem_cd2 AS mem_cd2
			,		t01_mem_nm2 AS mem_nm2
			,		t01_status_gbn AS stat
			,		t01_sugup_fmtime AS plan_from
			,		t01_sugup_totime AS plan_to
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_conf_fmtime AS conf_from
			,		t01_conf_totime AS conf_to
			,	    t01_toge_umu as toge_yn
			,		t01_sugup_soyotime as soyotime
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \'0\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$row['date'].'\'
			AND		t01_del_yn = \'N\'';
	$memRow = $conn->_fetch_array($sql);

	$rCnt = SizeOf($memRow);

	for($i=0; $i<$rCnt; $i++){
		$r = $memRow[$i];

		if ($r['stat'] == '1'){
			$stat = '완료';
			$from = $r['conf_from'];
			$to = $r['conf_to'];
		}else if ($r['stat'] == '5'){
			$stat = '진행중';
			$from = $r['work_from'];
			$to = '9999';
		}else{
			$stat = '대기';
			$from = $r['plan_from'];
			$to = $r['plan_to'];
		}
		
		//5등급 인지활동 가족케어일 경우 시간 60분
		if($r['mem_nm2']!='' && $r['toge_yn']=='Y'){
			$soyoTime = $myF->time2min($to) - $myF->time2min($from);

			if($soyoTime==60){
				$to = $myF->min2time($myF->time2min($to) + 60);
			}else {
				$to = $myF->min2time($myF->time2min($to) + 30);
			}
		}

		if (($row['time'] >= $from && $row['time'] <= $to) || ($row['to_time'] >= $from && $row['to_time'] <= $to)){
			$idx = SizeOf($memList);
			$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

			if ($r['mem_nm2']){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
			}
		}
	}

	if (is_array($memList)){
		$memList = $myF->sortArray($memList, 'name', 1);
	}


	$rowH = $pdf->row_height;

	/*
	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->Cell($pdf->width, $rowH, "[별지 제14호서식]", 0, 1);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$pdf->SetXY($X, $Y + 2);
	$pdf->SetFont($pdf->font_name_kor, "B", 17);
	$pdf->MultiCell($pdf->width * 0.6, 7, "방문요양기관 사회복지사\n업무수행 일지", 0, "C");

	$pdf->SetFont($pdf->font_name_kor, '', 11);
	$pdf->SetXY($X, $Y);
	$pdf->Cell($pdf->width * 0.6, $rowH * 3, "", 1);
	$pdf->Cell($pdf->width * 0.1, $rowH * 3, "확인", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.15, $rowH, "사회복지사", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.15, $rowH, "관리책임자", 1, 1, "C");

	$pdf->SetX($pdf->left + $pdf->width * 0.7);
	$pdf->Cell($pdf->width * 0.15, $rowH * 2, "", 1);
	$pdf->Cell($pdf->width * 0.15, $rowH * 2, "", 1, 1);

	$X = $pdf->left;
	$Y = $pdf->GetY() + 3;

	$pdf->SetXY($X, $Y);
	$pdf->Cell($pdf->width * 0.3, $rowH, "수급자 성명", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.3, $rowH, "장기요양등급", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.4, $rowH, "업무수행일시", 1, 1, "C");

	$pdf->SetX($X);
	$pdf->Cell($pdf->width * 0.3, $rowH, $name, 1, 0, "C");
	$pdf->Cell($pdf->width * 0.3, $rowH, $lvl, 1, 0, "C");
	$pdf->Cell($pdf->width * 0.4, $rowH, $myF->dateStyle($row['date'],'.').' '.$myF->timeStyle($row['time']), 1, 1, "C");

	$pdf->SetFont($pdf->font_name_kor, '', 9);
	*/
	$pdf->SetFont($pdf->font_name_kor, '', 9);

	$rowH = $rowH * (9 / 11);
	$mH = $rowH * 2;

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.19;
	$col[] = $pdf->width * 0.68;

	//신체상태
	$bodyStatNote = StripSlashes($row['body_stat_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$bodyStatNote) + 2;
	$H[0] = ($tmpH > $mH ? $tmpH : $mH);

	//질병
	$diseaseStr = "질병명 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['disease'] ? $row['disease'] : "                         ")), $col[2] * 0.4);

	//약복용
	if ($row['medication'] == 'Y'){
		$diseaseStr .= "(약복용)";
	}
	

	if($orgNo == '34119000298'){

		//진단명
		if($row['diagnosis']){
			$diseaseStr .= "   진단명 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['diagnosis'])), $col[2] * 0.5)."\n";
		}

		//장애명
		if($row['disabled']){
			$diseaseStr .= "장애명 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['disabled'])), $col[2] * 0.7);
			
			if ($row['disabled_lvl'] > 0){
				$diseaseStr .= "(".$row['disabled_lvl']."등급)";
			}
		}

		if($row['diagnosis'] || $row['disabled']){
			$diseaseStr .= "\n";
		}

	}else {
		$diseaseStr .= "   진단명 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['diagnosis'])), $col[2] * 0.5)."\n";
		$diseaseStr .= "장애명 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['disabled'])), $col[2] * 0.7);

		$diseaseStr .= "\n";
	}

	
	//시력
	if ($row['eyesight']){
		$val[0] = lfSetMark($row['eyesight'],'1');
		$val[1] = lfSetMark($row['eyesight'],'2');
		$val[2] = lfSetMark($row['eyesight'],'3');

		$diseaseStr .= "시력 : ".$val[0]."양호   ".$val[1]."보통   ".$val[2]."나쁨";
	}

	//청력
	if ($row['hearing']){
		$val[0] = lfSetMark($row['hearing'],'1');
		$val[1] = lfSetMark($row['hearing'],'2');
		$val[2] = lfSetMark($row['hearing'],'3');

		if ($row['eyesight']){
			$diseaseStr .= " / ";
		}

		$diseaseStr .= "청력 : ".$val[0]."양호   ".$val[1]."보통   ".$val[2]."나쁨";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$diseaseStr) + 2;
	$H[1] = ($tmpH > $mH ? $tmpH : $mH);

	//질병비고
	$diseaseNote = StripSlashes($row['disease_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$diseaseNote) + 2;
	$H[2] = ($tmpH > $mH ? $tmpH : $mH);

	//인지,기억력
	$memoryStr = "인지,기억력 : ";

	if ($row['memory'] == '1'){
		$memoryStr .= "명확";
	}else if ($row['memory'] == '2'){
		$memoryStr .= "부분도움";
	}else if ($row['memory'] == '3'){
		$memoryStr .= "불가능";
	}else{
		$memoryStr .= "                       ";
	}

	$memoryStr .= "           표현력 : ";

	if ($row['express'] == '1'){
		$memoryStr .= "명확";
	}else if ($row['express'] == '2'){
		$memoryStr .= "부분도움";
	}else if ($row['express'] == '3'){
		$memoryStr .= "불가능";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$memoryStr) + 2;
	$H[3] = ($tmpH > $rowH ? $tmpH : $rowH);

	//인지,기억력 비고
	$memoryNote = StripSlashes($row['memory_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$memoryNote) + 2;
	$H[4] = ($tmpH > $mH ? $tmpH : $mH);

	//정서적상태
	$commStr = "정서적상태 : ";
	if ($row['feel_stat'] == '1'){
		$commStr .= "활발/적극";
	}else if ($row['feel_stat'] == '2'){
		$commStr .= "조용/내성";
	}else if ($row['feel_stat'] == '3'){
		$commStr .= "흥분/우울";
	}
	$commStr .= "\n";

	//의사소통기타
	$commStr .= "기타 : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($row['comm_other'])), $col[2] * 0.9);

	$tmpH = $pdf->_getStrY($pdf,$col[2],$commStr) + 2;
	$H[5] = ($tmpH > $rowH ? $tmpH : $rowH);

	//의사소통비고
	$commNote = StripSlashes($row['comm_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$commNote) + 2;
	$H[6] = ($tmpH > $mH ? $tmpH : $mH);

	//식하형태
	$nutritionStr = "식사형태 : ";
	if ($row['meal_type'] == '1'){
		$nutritionStr .= "일반식";
	}else if ($row['meal_type'] == '2'){
		$nutritionStr .= "당뇨식";
	}else if ($row['meal_type'] == '3'){
		$nutritionStr .= "죽";
	}else if ($row['meal_type'] == '3'){
		$nutritionStr .= "경관급식";
	}

	//섭취패턴
	if ($row['intake_type'] == '1'){
		$nutritionStr .= "(3식을 규칙적으로 먹는다.)";
	}else if ($row['intake_type'] == '2'){
		$nutritionStr .= "(평균 2식을 먹는다.)";
	}else if ($row['intake_type'] == '3'){
		$nutritionStr .= "(1식만 먹는다.)";
	}

	$nutritionStr .= "\n";

	//수분섭취
	$nutritionStr .= "수분섭취 : ";
	if ($row['water_type'] == '1'){
		$nutritionStr .= "1일 5컵이상";
	}else if ($row['water_type'] == '2'){
		$nutritionStr .= "1일 2~4컵";
	}else if ($row['water_type'] == '3'){
		$nutritionStr .= "1일 1~2컵";
	}else if ($row['water_type'] == '4'){
		$nutritionStr .= "1일 1컵";
	}else if ($row['water_type'] == '9'){
		$nutritionStr .= "거의 드시지 않음";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$nutritionStr) + 2;
	$H[7] = ($tmpH > $rowH ? $tmpH : $rowH);

	//영양상태비고
	$nutritionNote = StripSlashes($row['nutrition_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$nutritionNote) + 2;
	$H[8] = ($tmpH > $mH ? $tmpH : $mH);

	//가족 및 환경
	$envNote = StripSlashes($row['env_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$envNote) + 2;
	$H[9] = ($tmpH > $mH ? $tmpH : $mH);

	//종합
	$totalNote = StripSlashes($row['total_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$totalNote) + 2;
	$H[10] = ($tmpH > $mH ? $tmpH : $mH);

	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;

	//욕구사정 - 신체상태
	$val[0] = lfSetMark($row['body_stat'],'1');
	$val[1] = lfSetMark($row['body_stat'],'2');
	$val[2] = lfSetMark($row['body_stat'],'3');

	/*
	if ($Y >= $pdf->height - $totH){
		//63/270/222
		//echo $Y.'/'.$pdf->height.'/'.$totH;
		//$totH1 = $totH - $Y;
		$totH1 = $Y + ($pdf->height - $totH);
	}else{
		$totH1 = $totH;
	}
	*/

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $rowH + $totH1, "욕구사정", 1, 0, "C");
	$pdf->Cell($col[1], $rowH + $H[0], "① 신체상태", 1);
	$pdf->Cell($col[2], $rowH, $val[0].'완전자립   '.$val[1].'부분자립   '.$val[2].'전적인 도움', 1, 1);

	$X1 = $X + $col[0] + $col[1];
	$pdf->SetX($X1);
	$pdf->Cell($col[2], $H[0], '', 1, 1);

	$Y = $Y + $rowH;

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $bodyStatNote);
	$Y = $Y + $H[0];


	//욕구사정 - 질병
	$pdf->SetXY($X + $col[0], $Y);
	$pdf->Cell($col[1], $H[1]+$H[2], "② 질병", 1);
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $diseaseStr);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0] + $col[1];


	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[2], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $diseaseNote);

	$Y = $Y + $H[2];


	if ($Y >= $pdf->height - $H[3]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[3], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//욕구사정 - 인지상태
	$pdf->SetXY($X + $col[0], $Y);
	$pdf->Cell($col[1], $H[3]+$H[4], "③ 인지상태", 1);
	$pdf->Cell($col[2], $H[3], $memoryStr, 1, 1);

	$Y = $Y + $H[3];
	$X1 = $X + $col[0] + $col[1];


	if ($Y >= $pdf->height - $H[4]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[4], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[4], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $memoryNote);

	$Y = $Y + $H[4];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[5]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[5], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//욕구사정 - 의사소통
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[5]+$H[6], "④ 의사소통", 1);
	$pdf->Cell($col[2], $H[5], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $commStr);

	$Y = $Y + $H[5];


	if ($Y >= $pdf->height - $H[6]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[6], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[6], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $commNote);

	$Y = $Y + $H[6];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[7]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[7], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//영양상태
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[7]+$H[8], "⑤ 영양상태", 1);
	$pdf->Cell($col[2], $H[7], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $nutritionStr);

	$Y = $Y + $H[7];


	if ($Y >= $pdf->height - $H[8]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[8], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//영양상태 비고
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[8], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $nutritionNote);

	$Y = $Y + $H[8];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[9]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[9], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//가족 및 환경
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[9], "⑥ 가족 및 환경", 1);
	$pdf->Cell($col[2], $H[9], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $envNote);

	$Y = $Y + $H[9];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[10]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[10], "욕구사정", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//종합
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[10], "종합", 1, 0, "C");
	$pdf->Cell($col[2], $H[10], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $totalNote);

	$Y = $Y + $H[10];

	Unset($H);


	//2.급여제공계획
	//2-1.급여목표
	$targetNote = StripSlashes($row['target_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$targetNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//2-2.필요 급여내용
	$contNote = StripSlashes($row['cont_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$contNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//2-3.제공방법
	$provideNote = StripSlashes($row['provide_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$provideNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	$totH = 0;
	$totH1 = 0;
	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}
	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}


	//2-1.급여목표
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "급여제공계획", 1, 0, "C");
	$pdf->Cell($col[1], $H[0], "급여목표", 1, 0, "C");
	$pdf->Cell($col[2], $H[0], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $targetNote);

	$Y = $Y + $H[0];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[1]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[1], "급여제공계획", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//2-2.필요 급여내용
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[1], "필요 급여내용", 1, 0, "C");
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $contNote);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2], "급여제공계획", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//2-3.제공방법
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[2], "제공방법", 1, 0, "C");
	$pdf->Cell($col[2], $H[2], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $provideNote);

	$Y = $Y + $H[2];


	Unset($H);



	//3.인지활동 프로그램 제공계획
	//3-1-1.인지자극 필요내용
	$planRecText = StripSlashes($row['plan_rec_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planRecText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-1-2.인지자극 제공방법
	$planRecWay = StripSlashes($row['plan_rec_way']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planRecWay) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2-1.신체능력 필요내용
	$planBodyText = StripSlashes($row['plan_body_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planBodyText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2-2.신체능력 제공방법
	$planBodyWay = StripSlashes($row['plan_body_way']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planBodyWay) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	if ($Y >= $pdf->height - $H[0] - $H[1]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}

	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
			//if ($i % 2 == 1) $totH1 -= $tmpH;
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	
	
	if($orgNo == '34119000298'){
		if($lvl == '5'){
			//3.인지활동 프로그램 제공계획
			//3-1.인지자극
			//3-1-1.필요내용
			$pdf->SetXY($X, $Y);
			$pdf->Cell($col[0], $totH1, "", 1, 0, "C");
			$pdf->Cell($col[1] / 2, $H[0] + $H[1], "인지자극", 1, 0, "C");
			$pdf->Cell($col[1] / 2, $H[0], "필요내용", 1, 0, "C");
			$pdf->Cell($col[2], $H[0], "", 1, 2);

			$pdf->SetX($X + $col[0] + $col[1] / 2);
			$pdf->Cell($col[1] / 2, $H[1], "제공방법", 1, 0, "C");
			$pdf->Cell($col[2], $H[1], "", 1, 1);

			$X1 = $X + $col[0] + $col[1];
			
			$tmpH = ($totH1 - $pdf->_getStrY($pdf,$col[0],"인지활동\n프로그램\n제공계획")) / 2;
			$pdf->SetXY($X, $Y + $tmpH);
			$pdf->MultiCell($col[0], 4, "인지활동\n프로그램\n제공계획", 0, "C");

			$pdf->SetXY($X1, $Y + 1);
			$pdf->MultiCell($col[2], 4, $planRecText);

			$pdf->SetXY($X1, $Y + $H[0] + 1);
			$pdf->MultiCell($col[2], 4, $planRecWay);

			$Y = $Y + $H[0] + $H[1];
			$X1 = $X + $col[0];


			if ($Y >= $pdf->height - $H[2] - $H[3]){
				$pdf->MY_ADDPAGE();
				$pdf->SetFont($pdf->font_name_kor, '', 9);
				$pdf->SetX($X);
				$pdf->Cell($col[0], $H[2] + $H[3], "", 1, 0, "C");
				$Y = $pdf->GetY();

				$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[0],"인지활동\n프로그램\n제공계획")) / 2;
				$pdf->SetXY($X, $Y + $tmpH);
				$pdf->MultiCell($col[0], 4, "인지활동\n프로그램\n제공계획", 0, "C");
			}

			//3-2.신체능력 잔존.유지
			$pdf->SetXY($X1, $Y);
			$pdf->Cell($col[1] / 2, $H[2] + $H[3], "", 1, 0, "C");
			$pdf->Cell($col[1] / 2, $H[2], "필요내용", 1, 0, "C");
			$pdf->Cell($col[2], $H[2], "", 1, 2);

			$pdf->SetX($X + $col[0] + $col[1] / 2);
			$pdf->Cell($col[1] / 2, $H[3], "제공방법", 1, 0, "C");
			$pdf->Cell($col[2], $H[3], "", 1, 1);

			$X1 = $X + $col[0] + $col[1];

			$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[1] / 2,"신체능력\n잔존.유지")) / 2;
			$pdf->SetXY($X + $col[0], $Y + $tmpH);
			$pdf->MultiCell($col[1] / 2, 4, "신체능력\n잔존.유지", 0, "C");

			$pdf->SetXY($X1, $Y + 1);
			$pdf->MultiCell($col[2], 4, $planBodyText);

			$pdf->SetXY($X1, $Y + $H[2] + 1);
			$pdf->MultiCell($col[2], 4, $planBodyWay);

			$Y = $Y + $H[2] + $H[3];
			$X1 = $X + $col[0];
		}
	}else {
		//3.인지활동 프로그램 제공계획
		//3-1.인지자극
		//3-1-1.필요내용
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0], $totH1, "", 1, 0, "C");
		$pdf->Cell($col[1] / 2, $H[0] + $H[1], "인지자극", 1, 0, "C");
		$pdf->Cell($col[1] / 2, $H[0], "필요내용", 1, 0, "C");
		$pdf->Cell($col[2], $H[0], "", 1, 2);

		$pdf->SetX($X + $col[0] + $col[1] / 2);
		$pdf->Cell($col[1] / 2, $H[1], "제공방법", 1, 0, "C");
		$pdf->Cell($col[2], $H[1], "", 1, 1);

		$X1 = $X + $col[0] + $col[1];

		$tmpH = ($totH1 - $pdf->_getStrY($pdf,$col[0],"인지활동\n프로그램\n제공계획")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "인지활동\n프로그램\n제공계획", 0, "C");

		$pdf->SetXY($X1, $Y + 1);
		$pdf->MultiCell($col[2], 4, $planRecText);

		$pdf->SetXY($X1, $Y + $H[0] + 1);
		$pdf->MultiCell($col[2], 4, $planRecWay);

		$Y = $Y + $H[0] + $H[1];
		$X1 = $X + $col[0];


		if ($Y >= $pdf->height - $H[2] - $H[3]){
			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor, '', 9);
			$pdf->SetX($X);
			$pdf->Cell($col[0], $H[2] + $H[3], "", 1, 0, "C");
			$Y = $pdf->GetY();

			$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[0],"인지활동\n프로그램\n제공계획")) / 2;
			$pdf->SetXY($X, $Y + $tmpH);
			$pdf->MultiCell($col[0], 4, "인지활동\n프로그램\n제공계획", 0, "C");
		}

		//3-2.신체능력 잔존.유지
		$pdf->SetXY($X1, $Y);
		$pdf->Cell($col[1] / 2, $H[2] + $H[3], "", 1, 0, "C");
		$pdf->Cell($col[1] / 2, $H[2], "필요내용", 1, 0, "C");
		$pdf->Cell($col[2], $H[2], "", 1, 2);

		$pdf->SetX($X + $col[0] + $col[1] / 2);
		$pdf->Cell($col[1] / 2, $H[3], "제공방법", 1, 0, "C");
		$pdf->Cell($col[2], $H[3], "", 1, 1);

		$X1 = $X + $col[0] + $col[1];

		$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[1] / 2,"신체능력\n잔존.유지")) / 2;
		$pdf->SetXY($X + $col[0], $Y + $tmpH);
		$pdf->MultiCell($col[1] / 2, 4, "신체능력\n잔존.유지", 0, "C");

		$pdf->SetXY($X1, $Y + 1);
		$pdf->MultiCell($col[2], 4, $planBodyText);

		$pdf->SetXY($X1, $Y + $H[2] + 1);
		$pdf->MultiCell($col[2], 4, $planBodyWay);

		$Y = $Y + $H[2] + $H[3];
		$X1 = $X + $col[0];
	}


	Unset($H);


	//4.보호자 상담
	$guardText = StripSlashes($row['guard_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$guardText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0] + $col[1], $totH1, "보호자 상담", 1, 0, "C");
	$pdf->Cell($col[2], $H[0], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $guardText);

	$Y = $Y + $H[0];
	$X1 = $X + $col[0];

	Unset($H);



	//3.급여제공확인
	//3-1.확인내용
	$checkNote = StripSlashes($row['check_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$checkNote) + 2;
	$H[0] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2.조치사항
	$actionNote = StripSlashes($row['action_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$actionNote) + 2;
	$H[1] = ($tmpH > $mH ? $tmpH : $mH);

	//3-3.요양보호사 성명
	/*
	$tmpArr = Explode('/',$row['mem_name']);

	if (is_array($tmpArr)){
		foreach($tmpArr as $tmpA){
			$memStr .= ($tmpA."   ");
		}
	}
	*/
	//$row['svcporc_yn'] = '';
	if (is_array($memList)){
		foreach($memList as $idx => $mem){
			$memStr .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");

			//if ($mem['stat'] == '완료' || $mem['stat'] == '진행중'){
			//	$row['svcporc_yn'] = 'Y';
			//}
		}
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$memStr) + 2;
	$H[2] = ($tmpH > $rowH ? $tmpH : $rowH);

	//3-4 방문불가사유
	$str = '';

	if (!$row['notvisit_cd'] && $row['notvisit_reason']) $row['notvisit_cd'] = '9';
	if ($row['notvisit_cd'] == '1'){
		$str = '사망';
	}else if ($row['notvisit_cd'] == '2'){
		$str = '병원';
	}else if ($row['notvisit_cd'] == '3'){
		$str = '해지';
	}else if ($row['notvisit_cd'] == '9'){
		$str = '기타';
	}

	if ($str) $str .= ' - ';

	$notvisitReason = $str.StripSlashes($row['notvisit_reason']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$notvisitReason) + 2;
	$H[4] = ($tmpH > $rowH ? $tmpH : $rowH);

	//3-4.확인선택사항
	$checkStr .= "근무일지작성 : ".lfSetMark($row['write_log_yn'],'Y')."예 ".lfSetMark($row['write_log_yn'],'N')."아니오      ";
	$checkStr .= "제공시간준수 : ".lfSetMark($row['provide_chk_yn'],'Y')."예 ".lfSetMark($row['provide_chk_yn'],'N')."아니오\n";

	$checkStr .= "적절서비스 : ".lfSetMark($row['right_svc_yn'],'Y')."예 ".lfSetMark($row['right_svc_yn'],'N')."아니오      ";
	$checkStr .= "유니폼착용 : ".lfSetMark($row['uniform_yn'],'Y')."예 ".lfSetMark($row['uniform_yn'],'N')."아니오\n";

	$checkStr .= "주거환경청결 : ".lfSetMark($row['house_env_yn'],'1')."상 ".lfSetMark($row['house_env_yn'],'2')."증 ".lfSetMark($row['house_env_yn'],'3')."하      ";
	$checkStr .= "업무태도친절 : ".lfSetMark($row['work_mind_yn'],'1')."상 ".lfSetMark($row['work_mind_yn'],'2')."증 ".lfSetMark($row['work_mind_yn'],'3')."하\n";

	$tmpH = $pdf->_getStrY($pdf,$col[2],$checkStr) + 2;
	$H[3] = ($tmpH > $mH ? $tmpH : $mH);


	//방문장소
	$visitPlace = StripSlashes($row['visit_place']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$visitPlace) + 2;
	$H[5] = ($tmpH > $rowH ? $tmpH : $rowH);


	$totH = 0;
	$totH1 = 0;
	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}
	foreach($H as $i => $tmpH){
		if ($i == 3) continue;
		if ($i == 0) $tmpH += $H[3];

		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){

		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;

	if ($Y >= $pdf->height - ($H[0]+$H[3])){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}


	//3-1.확인내용
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "", 1, 0, "C");
	$pdf->Cell($col[1], $H[0]+$H[3], "확인내용", 1, 0, "C");
	$pdf->Cell($col[2], $H[3], "", 1, 1);

	$tmpH = ($totH1 - $pdf->_getStrY($pdf,$col[0],"급여 및\n인지활동\n프로그램\n제공확인")) / 2;
	$pdf->SetXY($X, $Y + $tmpH);
	$pdf->MultiCell($col[0], 4, "급여 및\n인지활동\n프로그램\n제공확인", 0, "C");

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $checkStr);

	$Y = $Y + $H[3];


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[0], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $checkNote);

	$Y = $Y + $H[0];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[1]){

		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[1]+$H[2]+$H[4]+$H[4], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = (($H[1]+$H[2]+$H[4]+$H[4]) - $pdf->_getStrY($pdf,$col[0],"급여 및\n인지활동\n프로그램\n제공확인")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "급여 및\n인지활동\n프로그램\n제공확인", 0, "C");
	}


	//3-2.조치사항
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[1], "조치사항", 1, 0, "C");
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $actionNote);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2]+$H[4]+$H[4], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[2]+$H[4]+$H[4] - $pdf->_getStrY($pdf,$col[0],"급여 및\n인지활동\n프로그램\n제공확인")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "급여 및\n인지활동\n프로그램\n제공확인", 0, "C");
	}


	//3-3.요양보호사 성명
	$pdf->SetXY($X1, $Y);
	if($yymm > '201701'){
		$pdf->Cell($col[1], $H[2], "요양보호사 성명", 1, 0, "C");
	}else {
		$pdf->Cell($col[1], $H[2], "급여제공자 성명(인)*", 1, 0, "C");
	}

	$pdf->Cell($col[2], $H[2], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[2], "급여제공 중 방문(".($row['svcporc_yn'] == 'Y' ? ' V ' : '    ').")", 0, 0, "R");

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $memStr);

	$Y = $Y + $H[2];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[4]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[4]+$H[4], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[2] - $pdf->_getStrY($pdf,$col[0],"급여및인지활동\n프로그램제공확인")) / 2;
		$pdf->SetXY($X, $Y + $tmpH+5);
		$pdf->MultiCell($col[0], 4, "급여 및 인지\n활동 프로그램\n제공확인", 0, "C");
	}


	//방문장소
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[4], "방문장소", 1, 0, "C");
	$pdf->Cell($col[2], $H[4], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $visitPlace);


	$Y = $Y + $H[4];
	$X1 = $X + $col[0];


	//3-4.방문불가 사유
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[4], "방문불가 사유", 1, 0, "C");
	$pdf->Cell($col[2], $H[4], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $notvisitReason);


	$Y = $Y + $H[4];
	$X1 = $X + $col[0];


	Unset($H);


	//4.총평
	//4-1.총평
	$commentStr = StripSlashes($row['comment']);
	$tmpH = $pdf->_getStrY($pdf,$col[1]+$col[2],$commentStr) + 2;
	$H[0] = ($tmpH > $mH ? $tmpH : $mH);


	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;

	
	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}


	if($orgNo == '34119000298'){
		/*
		if($commentStr){ 		
			$pdf->SetXY($X, $Y);
			$pdf->Cell($col[0], $totH1, "총평", 1, 0, "C");
			$pdf->Cell($col[1]+$col[2], $H[0], "", 1, 1);

			$X1 = $X + $col[0];

			$pdf->SetXY($X1, $Y + 1);
			$pdf->MultiCell($col[1]+$col[2], 4, $commentStr);
		}
		*/
	}else {
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0], $totH1, "총평", 1, 0, "C");
		$pdf->Cell($col[1]+$col[2], $H[0], "", 1, 1);

		$X1 = $X + $col[0];

		$pdf->SetXY($X1, $Y + 1);
		$pdf->MultiCell($col[1]+$col[2], 4, $commentStr);
	}


	Unset($row);
	Unset($checkStr);
	Unset($H);

	//include_once('../inc/_db_close.php');

	
?>
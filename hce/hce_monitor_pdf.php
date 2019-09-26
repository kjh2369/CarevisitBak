<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	모니터링 기록지
	 *********************************************************/
	#$pdf->AddUHCFont('돋움', 'Dotum');
	#$pdf->AddUHCFont('바탕', 'Batang');
	#$pdf->AddUHCFont('궁서', 'Gungsuh');
	#$pdf->AddUHCFont('굴림', 'Gulim');
	#$pdf->AddUHCFont('돋움', 'Dotum');
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	//대상자명
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'EV5\'
			AND		use_yn	= \'Y\'';

	$evaluation = $conn->_fetch_array($sql,'code');

	
	//모니터링
	$sql = 'SELECT	*
			FROM	hce_monitor
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		mntr_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$mntrGbn	= $row['mntr_gbn'];		//작성구분
		$mntrType	= $row['mntr_type'];	//모니터링 구분
		$perNm		= $row['per_nm'];		//전담담당자
		$ispNm		= $row['inspector_nm'];	//조사자
		$mntrDate	= $myF->dateStyle($row['mntr_dt'],'.');

		//서비스 제공 일정 지키는 정도
		$scheduleSat = $row['schedule_sat'];
		$scheduleStr = StripSlashes($row['schedule_svc']);
		
		//서비스 내용 정확성 및 충실도
		$fullnessSat = $row['fullness_sat'];
		$fullnessStr = StripSlashes($row['fullness_svc']);

		//수발 담당자에 대한 만족도
		$perinchargeSat = $row['perincharge_sat'];
		$perinchargeStr = StripSlashes($row['perincharge_svc']);

		//기능상태 변화
		$abilityStr = StripSlashes($row['ability_change']);

		//생활환경 및 수발능력변화
		$lifeEnvStr = StripSlashes($row['life_env_change']);

		//기타 불편사항 모니터링
		$extDiscomfortStr = StripSlashes($row['ext_discomfort']);

		//
		$monitorRst		= $row['monitor_rst'];
		$extDetailStr	= StripSlashes($row['ext_detail']);
	}

	Unset($row);


	//작성구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'CT\'
			AND		use_yn	= \'Y\'';

	$mntrGbn = lfGetGbnStr($pdf,$myF,$conn,$sql,$mntrGbn);


	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->Cell($pdf->width, $pdf->row_height, $mntrGbn, 0, 1);

	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.31;
	$rowH = $pdf->row_height * 2;

	$X = $pdf->left;
	$Y1 = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "".$hce->IPIN, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "성        명", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, ($mntrType == '1' ? '■' : '□')."최초    ".($mntrType == '2' ? '■' : '□')."정기", 1, 1, "C");

	$Y2 = $pdf->GetY();

	$H = lfGetStringHeight($pdf,$col[0],"서 비 스\n관리번호");

	$pdf->SetXY($X, $Y1 + ($rowH - $H) / 2);
	$pdf->MultiCell($col[0], 4, "서 비 스\n관리번호", 0, "C");

	$H = lfGetStringHeight($pdf,$col[0],"모니터링\n구분");

	$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3], $Y1 + ($rowH - $H) / 2);
	$pdf->MultiCell($col[4], 4, "모니터링\n구      분", 0, "C");

	$pdf->SetXY($X, $Y2);
	$pdf->Cell($col[0], $rowH, "전담담당자", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $perNm, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "조  사  자", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $ispNm, 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "작성일자", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, $mntrDate, 1, 1, "C");

	Unset($col);


	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $pdf->row_height, "1. 이용자 의견조사", 0, 1);
	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "1) 서비스 모니터링", 0, 1);


	$col[] = $pdf->width * 0.25;
	$col[] = $pdf->width * 0.75;

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $pdf->row_height, "문     항", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "구체적인 내용", 1, 1, "C", 1);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"서비스 제공 일정\n지키는 정도");
	$H2 = lfGetStringHeight($pdf,$col[1],$scheduleStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "서비스 제공 일정\n지키는 정도");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $scheduleStr);
	

	foreach($evaluation as $ev){
		if($ev['code'] == $scheduleSat){ 
			$schName .= ' ●'.$ev['name'];
		}else {
			$schName .= ' ○'.$ev['name'];
		}
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $schName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($schName);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"서비스 내용\n정확성 및 충실도");
	$H2 = lfGetStringHeight($pdf,$col[1],$fullnessStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "서비스 내용\n정확성 및 충실도");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $fullnessStr);
	
	foreach($evaluation as $ev){
		if($ev['code'] == $fullnessSat){ 
			$fullName .= ' ●'.$ev['name'];
		}else {
			$fullName .= ' ○'.$ev['name'];
		}
	}
	
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $fullName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($fullName);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"수발 담당자에\n대한 만족도");
	$H2 = lfGetStringHeight($pdf,$col[1],$perinchargeStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "수발 담당자에\n대한 만족도");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $perinchargeStr);

	foreach($evaluation as $ev){
		if($ev['code'] == $perinchargeSat){ 
			$peName .= ' ●'.$ev['name'];
		}else {
			$peName .= ' ○'.$ev['name'];
		}
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $peName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($peName);

	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "2) 욕구변화 모니터링", 0, 1);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $pdf->row_height, "문     항", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "구체적인 내용", 1, 1, "C", 1);



	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"기능상태 변화");
	$H2 = lfGetStringHeight($pdf,$col[1],$abilityStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "기능상태 변화");

	$pdf->SetXY($X + $col[0], $Y + ($H - $H2) / 2);
	$pdf->MultiCell($col[1], 4, $abilityStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C");
	$pdf->Cell($col[1], $H, "", 1, 1, "C");



	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"생활환경 및\n수발능력변화");
	$H2 = lfGetStringHeight($pdf,$col[1],$lifeEnvStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "생활환경 및\n수발능력변화");

	$pdf->SetXY($X + $col[0], $Y + ($H - $H2) / 2);
	$pdf->MultiCell($col[1], 4, $lifeEnvStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C");
	$pdf->Cell($col[1], $H, "", 1, 1, "C");



	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "3) 기타 불편사항 모니터링", 0, 1);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H = lfGetStringHeight($pdf,$pdf->width,$extDiscomfortStr) + 1.6;

	$pdf->SetXY($X, $Y+1);
	$pdf->MultiCell($pdf->width, 4, $extDiscomfortStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($pdf->width, $H, "", 1, 1, "C");



	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width, $pdf->row_height, "2. 서비스 종합계획", 0, 1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "모니터링 결과 반영", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, ($monitorRst == '1' ? '■' : '□')."서비스 유지   ".($monitorRst == '2' ? '■' : '□')."서비스 계획 변경   ".($monitorRst == '3' ? '■' : '□')."재 방문 면담", 1, 1, "L");

	$X = $pdf->left;
	$Y = $pdf->GetY();
	$H = $pdf->height+10 - $Y;

	$pdf->SetXY($X + $col[0], $Y + 0.8);
	$pdf->MultiCell($col[1], 4, $extDetailStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "기타 구체적인 기록", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1, "L");

	Unset($col);
?>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	서비스계획서
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];
	$orgNm = $_SESSION["userCenterName"];
	$userArea = $_SESSION['userArea'];

	//대상자
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);


	//동의서
	$sql = 'SELECT	cont_dt, per_nm
			FROM	hce_consent_form
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$cotnDt = $row['cont_dt'];
	$perNm	= $row['per_nm'];

	Unset($row);
	
	//충남협회 20180313부터 사례회의록 서비스제공내역에서 서비스계획서 내용으로 변경
	if($userArea == '05'){
		$sql = 'SELECT	count(*)
				FROM	hce_consent_form
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND     (REPLACE(cont_dt,\'-\',\'\') <= \'20180312\'
				OR     REPLACE(update_dt,\'-\',\'\') <= \'20180312\')';
		$cnt = $conn -> get_data($sql);
	}else {
		$sql = 'SELECT	count(*)
				FROM	hce_consent_form
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';
		$cnt = $conn -> get_data($sql);
	}
	
	$sql = 'SELECT	IFNULL(MAX(plan_seq),0)
			FROM	hce_plan_sheet
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag = \'N\'';

	$planSeq = $conn->get_data($sql);
	
	$sql = 'SELECT	plan_idx
			,		contents
			FROM	hce_plan_sheet_item
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		org_type = \''.$hce->SR.'\'
			AND		IPIN	 = \''.$hce->IPIN.'\'
			AND		rcpt_seq = \''.$hce->rcpt.'\'
			AND     plan_seq = \''.$planSeq.'\'
			AND		del_flag = \'N\'';
	
	$plan = $conn->_fetch_array($sql, 'plan_idx');	


	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width, $pdf->row_height, "1. 서비스 종류", 0, 1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$col[] = $pdf->width * 0.100;
	$col[] = $pdf->width * 0.268;
	$col[] = $pdf->width * 0.130;
	$col[] = $pdf->width * 0.002;

	$rowH = $pdf->row_height * 1.5;

	$X1 = $pdf->left;
	$Y1 = $pdf->GetY() + 2;

	$pdf->SetXY($X1, $Y1);
	$pdf->Cell($col[0], $pdf->row_height, "서비스명", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "내용", 1, 0, "C", 1);
	$pdf->Cell($col[2], $pdf->row_height, "비고", 1, 0, "C", 1);
	$pdf->Cell($col[3], $pdf->row_height, "", "LR", 0, "C");
	$pdf->Cell($col[0], $pdf->row_height, "서비스명", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "내용", 1, 0, "C", 1);
	$pdf->Cell($col[2], $pdf->row_height, "비고", 1, 1, "C", 1);
	$pdf->SetLineWidth(0.2);
	
	
	$sql = 'SELECT	decision_svc
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag= \'N\'
			ORDER	BY meet_seq DESC
			LIMIT	1';

	$tmpSvc = $conn->get_data($sql);
	$tmpSvc = Str_Replace('/','&',$tmpSvc);
	$tmpSvc = Str_Replace(':','=',$tmpSvc);

	parse_str($tmpSvc, $arrSvc);
	
	
	if($cnt>0){
		$sql = 'SELECT	svc.svc_nm AS svc_cd
				,		svc.content
				,		svc.remark
				,		suga.nm3 AS svc_nm
				FROM	hce_consent_svc AS svc
				INNER	JOIN	suga_care AS suga
						ON		suga.cd1 = SUBSTR(svc.svc_nm,1,1)
						AND		suga.cd2 = SUBSTR(svc.svc_nm,2,2)
						AND		suga.cd3 = SUBSTR(svc.svc_nm,4,2)
				WHERE	svc.org_no	= \''.$orgNo.'\'
				AND		svc.org_type= \''.$hce->SR.'\'
				AND		svc.IPIN	= \''.$hce->IPIN.'\'
				AND		svc.rcpt_seq= \''.$hce->rcpt.'\'
				ORDER	BY svc_cd, svc_nm';	
		
	}else{
		$sql = 'SELECT	cont_seq
			,		svc_nm
			,		content
			,		remark
			FROM	hce_consent_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER   BY cont_seq';
		
	}
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	
	$y = $pdf->GetY();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		//if ($arrSvc[$row['svc_cd']] != 'Y') continue;

		$Y = $pdf->GetY() + $pdf->row_height;

		if ($Y > $pdf->height){
			$X2 = $pdf->width - $pdf->width * 0.002;
			$Y2 = $pdf->GetY() - $Y1;

			$pdf->Line($X1 + $col[0], $Y1, $X1 + $col[0], $y);
			$pdf->Line($X1 + $col[0] + $col[1], $Y1, $X1 + $col[0] + $col[1], $y);
			$pdf->Line($X1 + $col[0] + $col[1] + $col[2], $Y1, $X1 + $col[0] + $col[1] + $col[2], $y);
			$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3], $y);
			$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0], $y);
			$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0] + $col[1], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0] + $col[1], $y);

			$pdf->SetLineWidth(0.6);
			$pdf->Rect($X1, $Y1, $X2, $Y2);
			$pdf->SetLineWidth(0.2);

			$pdf->MY_ADDPAGE();
			$y = $pdf->GetY();

			$X1 = $pdf->left;
			$Y1 = $y;

			$pdf->SetFont($pdf->font_name_kor,'',9);
			$pdf->SetXY($X1, $Y1);
			$pdf->Cell($col[0], $pdf->row_height, "서비스명", 1, 0, "C", 1);
			$pdf->Cell($col[1], $pdf->row_height, "내용", 1, 0, "C", 1);
			$pdf->Cell($col[2], $pdf->row_height, "비고", 1, 0, "C", 1);
			$pdf->Cell($col[3], $pdf->row_height, "", "LR", 0, "C");
			$pdf->Cell($col[0], $pdf->row_height, "서비스명", 1, 0, "C", 1);
			$pdf->Cell($col[1], $pdf->row_height, "내용", 1, 0, "C", 1);
			$pdf->Cell($col[2], $pdf->row_height, "비고", 1, 1, "C", 1);

			$y = $pdf->GetY();
		}

		if ($i % 2 == 0){
			$pdf->SetX($pdf->left);
			$h1 = 0;
		}

		$x = $pdf->GetX();
		
		if($userArea == '05'){ 
			if($cnt>0){ 
				$pdf->SetXY($x, $y + 0.8);
				$pdf->MultiCell($col[0], 3.5, $row['svc_nm']);
			}else {
				$pdf->SetXY($x, $y + 0.8);
				$pdf->MultiCell($col[0], 3.5, $plan[$row['svc_nm']]['contents']);
			}
		}else {
			
			$pdf->SetXY($x, $y + 0.8);
			$pdf->MultiCell($col[0], 3.5, $row['svc_nm']);
		}

		$tmpH = $pdf->GetY() - $y + 0.8;
		if ($tmpH < $rowH) $tmpH = $rowH;
		if ($h1 < $tmpH) $h1 = $tmpH;


		$pdf->SetXY($x + $col[0], $y + 0.8);
		$pdf->MultiCell($col[1], 3.5, $row['content']);

		$tmpH = $pdf->GetY() - $y + 0.8;
		if ($tmpH < $rowH) $tmpH = $rowH;
		if ($h1 < $tmpH) $h1 = $tmpH;

		$pdf->SetXY($x + $col[0] + $col[1], $y + 0.8);
		$pdf->MultiCell($col[2], 3.5, $row['remark']);

		$tmpH = $pdf->GetY() - $y + 0.8;
		if ($tmpH < $rowH) $tmpH = $rowH;
		if ($h1 < $tmpH) $h1 = $tmpH;

		$pdf->SetXY($x, $y);
		$pdf->Cell($col[0], $h1, "", "T", 0, "C");
		$pdf->Cell($col[1], $h1, "", "T", 0, "C");
		$pdf->Cell($col[2], $h1, "", "T", ($i % 2), "C");

		if ($i % 2 == 0){
			$pdf->Cell($col[3], $h1, "", "", 0, "C");
		}else{
			$y += $h1;
		}
	}

	$conn->row_free();

	if ($i % 2 == 1){
		$pdf->Cell($col[0], $h1, "", "T", 0, "C");
		$pdf->Cell($col[1], $h1, "", "T", 0, "C");
		$pdf->Cell($col[2], $h1, "", "T", 1, "C");
		$y += $h1;
	}

	$pdf->Line($X1 + $col[0], $Y1, $X1 + $col[0], $y);
	$pdf->Line($X1 + $col[0] + $col[1], $Y1, $X1 + $col[0] + $col[1], $y);
	$pdf->Line($X1 + $col[0] + $col[1] + $col[2], $Y1, $X1 + $col[0] + $col[1] + $col[2], $y);
	$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3], $y);
	$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0], $y);
	$pdf->Line($X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0] + $col[1], $Y1, $X1 + $col[0] + $col[1] + $col[2] + $col[3] + $col[0] + $col[1], $y);

	$X2 = $pdf->width - $pdf->width * 0.002;
	$Y2 = $pdf->GetY() - $Y1;

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($X1, $Y1, $X2, $Y2);
	$pdf->SetLineWidth(0.2);


	$Y = $pdf->GetY();

	$str1 = "ㆍ서비스 이용자에게 적절하지 않거나 서비스 제공 목적에 어긋날 때\nㆍ서비스 이용자의 부적절한 서비스 요구가 있을 경우";
	$str2 = "ㆍ서비스 이용자가 서비스를 중단의 의사가 있을 경우\nㆍ다른 지역으로 이주를 하였을 경우\nㆍ3개월 이상 연락이 끊겼을 경우\nㆍ타 기관과 서비스가 중복되었을 경우";
	$H1 = lfGetStringHeight($pdf,$pdf->width - 2,$str1) + 2;
	$H2 = lfGetStringHeight($pdf,$pdf->width - 2,$str2);
	$H3 = $H1 + $H2 + $pdf->row_height + 10;
	
	
	if ($Y + $H3 > $pdf->height){
		$pdf->MY_ADDPAGE();
	}


	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width, $pdf->row_height, "2. 서비스 조정 및 중단", 0, 1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$X1 = $pdf->left;
	$Y1 = $pdf->GetY() + 2;

	$pdf->SetXY($X1, $Y1 + 0.8);
	$pdf->MultiCell($col[0], 5, "서비스\n조정", 0, "C");

	$pdf->SetXY($X1 + $col[0], $Y1 + 0.8);
	$pdf->MultiCell($pdf->width - $col[0], 5, $str1);


	$pdf->SetXY($X1, $Y1);
	$pdf->Cell($col[0], $rowH * 1.3, "", 1, 0);
	$pdf->Cell($pdf->width - $col[0], $rowH * 1.3, "", 1, 1);


	$Y3 = $pdf->GetY();

	$pdf->SetXY($X1, $Y3 + 0.8);
	$pdf->MultiCell($col[0], 5, "서비스\n중단", 0, "C");

	$pdf->SetXY($X1 + $col[0], $Y3 + 0.8);
	$pdf->MultiCell($pdf->width - $col[0], 5, $str2);

	$pdf->SetXY($X1, $Y3);
	$pdf->Cell($col[0], $rowH * 2.5, "", 1, 0);
	$pdf->Cell($pdf->width - $col[0], $rowH * 2.5, "", 1, 1);

	$X2 = $pdf->width;
	$Y2 = $pdf->GetY() - $Y1;

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($X1, $Y1, $X2, $Y2);
	$pdf->SetLineWidth(0.2);

	$Y = $pdf->GetY();

	$str1 = "  서비스 이용자는 신상의 어려움이나 경제적인 변동이 있을 경우 기관에 알려야 하며, 어려움을 해결하기 위해 같이 노력하여야 한다.\n서비스 이용자는 본 기관이 이용자와 상호 협의한 서비스를 실시하기 위하여 개인정보를 수집.활용하는 것에 동의한다.";
	$str2 = "  본 동의서는 \"".$myF->euckr($orgNm)."\"기관에서 제공되는 서비스에 대하여 본 기관과 (".$name.")님이 상호 협의한 내용이며, 서비스 제공에 있어 문제 및 어려움이 있을 경우 서비스 이용자와 기관과의 상호 협의를 통하여 조정이 가능하다.";
	$H1 = lfGetStringHeight($pdf,$pdf->width - 2,$str1);
	$H2 = lfGetStringHeight($pdf,$pdf->width - 2,$str2) + 3;
	$H3 = $H1 + $H2 + $pdf->row_height * 5 + 11;
	
	
	

	if ($Y + $H3 > $pdf->height){
		$pdf->MY_ADDPAGE();
	}


	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width, $pdf->row_height, "3. 서비스 이용 동의서", 0, 1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$X1 = $pdf->left;
	$Y1 = $pdf->GetY() + 2;

	$pdf->SetXY($X1 + 1, $Y1 + 0.8);
	$pdf->MultiCell($pdf->width - 2, 5, $str1);

	$X = $X1;
	$Y = $pdf->GetY() + 1;

	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(93,93,93);

	while(true){
		if ($X+0.5 >= $pdf->left+$pdf->width) break;

		$pdf->Line($X, $Y, $X+0.5, $Y);

		$X += 1;
	}

	$pdf->SetDrawColor(0,0,0);
	$pdf->SetLineWidth(0.2);

	$Y = $pdf->GetY() + 2;

	$pdf->SetXY($X1 + 1, $Y + 3);
	$pdf->MultiCell($pdf->width - 2, 5, $str2);

	$X2 = $pdf->width;
	$Y2 = $pdf->GetY() - $Y1;
	$Y3 = $pdf->height - $Y1+8;

	$Y = $pdf->GetY() + ($pdf->height - $pdf->GetY()) / 2 - $pdf->row_height;

	$pdf->SetXY($X1, $Y);
	$pdf->Cell($pdf->width, $pdf->row_height, $myF->euckr($myF->dateStyle($cotnDt,'KOR')), 0, 1, "C");

	$pdf->SetXY($X1, $Y + $pdf->row_height * 2);
	$pdf->Cell($pdf->width * 0.95, $pdf->row_height, "담당 사회복지사 : ".$perNm."     (인)", 0, 1, "R");

	$pdf->SetX($X1);
	$pdf->Cell($pdf->width * 0.95, $pdf->row_height, "이  용  자 : ".$name."     (인)", 0, 1, "R");

	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetXY($X1, $pdf->height - $pdf->row_height * 2);
	$pdf->Cell($pdf->width, $pdf->row_height, $pdf->ctName, 0, 1, "C");

	if ($Y2 < $Y3) $Y2 = $Y3;

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($X1, $Y1, $X2, $Y2);
	$pdf->SetLineWidth(0.2);

	Unset($col);
?>
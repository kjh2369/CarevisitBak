<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	재사정 기록지
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];
	

	//사례접수 및 초기면접 내용
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		RG.name AS rel_gbn
			,		iv.income_gbn
			,		iv.income_other
			,		iv.handicap_gbn
			,		iv.handicap_other
			,		iv.generation_gbn
			,		iv.generation_other
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.phone
			,		rcpt.mobile
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			INNER	JOIN	hce_interview AS iv
					ON		iv.org_no	= rcpt.org_no
					AND		iv.org_type = rcpt.org_type
					AND		iv.IPIN		= rcpt.IPIN
					AND		iv.rcpt_seq = rcpt.rcpt_seq
			LEFT	JOIN	hce_gbn AS RG
					ON		RG.type	= \'RG\'
					AND		RG.code	= rcpt.rel_gbn
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];	//대상자명
	$jumin	= $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);

	if (!$jumin) $jumin = $row['jumin'];
	$jumin = $myF->issStyle(SubStr($jumin.'0000000',0,13));	//대상자 주민번호

	$addr			= $row['addr'].' '.$row['addr_dtl'];	//주소
	$telno			= $myF->phoneStyle(($row['phone'] ? $row['phone'] : $row['mobile']),'.');
	$relGbn			= $row['rel_gbn'];	//종교
	$incomeGbn		= $row['income_gbn'];	//경제사항
	$incomeOther	= $row['income_other'];
	$handicapGbn	= $row['handicap_gbn'];	//장애여부
	$handicapOther	= $row['handicap_other'];
	$generationGbn	= $row['generation_gbn'];	//세대유형
	$generationOther= $row['generation_other'];

	Unset($row);

	//경제사항
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'IG\'
			AND		use_yn	= \'Y\'';
	$incomeGbn = lfGetGbnStr($pdf,$myF,$conn,$sql,$incomeGbn,'9',$incomeOther,$otherLen=30);

	//세대유형
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'GR\'
			AND		use_yn	= \'Y\'';
	$generationGbn = lfGetGbnStr($pdf,$myF,$conn,$sql,$generationGbn,'9',$generationOther,$otherLen=30);


	//재사정기록지
	$sql = 'SELECT	ispt_dt,per_nm,per_jumin,ispt_gbn,ispt_rsn,client_need_change,svc_offer_problem,wer_opion,ispt_rst,after_plan
			FROM	hce_re_ispt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		/*
		$data .= 'date='	.$row['ispt_dt'];
		$data .= '&name='	.$row['per_nm'];
		$data .= '&jumin='	.$ed->en($row['per_jumin']);
		$data .= '&gbn='	.$row['ispt_gbn'];
		$data .= '&rsn='	.$row['ispt_rsn'];
		$data .= '&need='	.StripSlashes($row['client_need_change']);
		$data .= '&offer='	.StripSlashes($row['svc_offer_problem']);
		$data .= '&wer='	.StripSlashes($row['wer_opion']);
		$data .= '&rst='	.$row['ispt_rst'];
		$data .= '&plan='	.StripSlashes($row['after_plan']);
		*/

		//재사정일자
		$isptDt = $myF->euckr($myF->dateStyle($row['ispt_dt'],'KOR'));

		//담당자
		$perNm = $row['per_nm'];

		//재사정 유형
		$isptGbn = $row['ispt_gbn'];

		//재사정 요인
		$isptRsn = $row['ispt_rsn'];

		//Client 변화욕구
		$isptNeed = StripSlashes($row['client_need_change']);

		//서비스 제공 및 문제
		$isptOffer = StripSlashes($row['svc_offer_problem']);

		//wer'의 의견
		$isptWer = StripSlashes($row['wer_opion']);

		//재사정 결과
		$isptRst = $row['ispt_rst'];

		//향후계획
		$isptPlan = StripSlashes($row['after_plan']);
	}

	Unset($row);
	Unset($col);


	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "재사정년월일 : ".$isptDt);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "담 당 자 : ".$perNm, 0, 1, "R");


	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.07;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.07;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.24;
	
	$rowH = $pdf->row_height * 1.2;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
	$pdf->Cell($pdf->width, $rowH, "클 라 이 언 트 개 인 사 항", 1, 1, "C", 1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "관리번호", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "".$hce->IPIN, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "성 명", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "종 교", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, $relGbn, 1, 0, "C");
	$pdf->Cell($col[6], $rowH, "주민등록번호", 1, 0, "C", 1);
	$pdf->Cell($col[7], $rowH, $jumin, 1, 1, "C");


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH * 3.5, "", 1, 0, "C", 1);
	$pdf->Cell($pdf->width - $col[0], $rowH, $incomeGbn, 1, 1);

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell(($pdf->width - $col[0]) * 0.5, $rowH, ($handicapGbn == 'Y' ? '■' : '□')."유(장애유형:".($handicapGbn == 'Y' ? $handicapOther : '                                           ').")", "LTB", 0);
	$pdf->Cell(($pdf->width - $col[0]) * 0.5, $rowH, ($handicapGbn == 'N' ? '■' : '□')."무", "RTB", 1);

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($pdf->width - $col[0], $rowH * 1.5, $generationGbn, 1, 1);

	$Y = $pdf->GetY();
	$H = lfGetStringHeight($pdf, $col[0], "대상자\n분류");

	$pdf->SetXY($pdf->left, $Y - $rowH * 3.5 + ($rowH * 3.5 - $H) / 2);
	$pdf->MultiCell($col[0], 4, "대상자\n분   류", 0, "C");

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH, "주   소", 1, 0, "C", 1);
	$pdf->Cell(lfGetCol($col,1,5), $rowH, $addr, 1, 0);
	$pdf->Cell($col[6], $rowH, "전 화 번 호", 1, 0, "C", 1);
	$pdf->Cell($col[7], $rowH, $telno, 1, 1, "C");


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH * 5, "", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, "관 계", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.15, $rowH, "성 명", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, "연 령", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.20, $rowH, "직  업", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, "종 교", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, "동거여부", 1, 0, "C", 1);
	$pdf->Cell(($pdf->width - $col[0]) * 0.25, $rowH, "비고", 1, 1, "C", 1);

	//가족관계
	$sql = 'SELECT	HR.name AS rel
			,		family_nm AS nm
			,		family_addr AS addr
			,		family_age AS age
			,		family_job AS job
			,		family_cohabit AS cohabit
			,		family_monthly AS monthly
			,		family_remark AS remark
			FROM	hce_family
			INNER	JOIN  hce_gbn AS HR
					ON  HR.type = \'HR\'
					AND HR.code = family_rel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY family_rel, family_age DESC
			LIMIT	4';

	$conn->query($sql);
	$conn->fetch();

	for($i=0; $i<4; $i++){
		@$row = $conn->select_row($i);

		$pdf->SetX($pdf->left + $col[0]);
		$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, $row['rel'], 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.15, $rowH, $row['nm'], 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, $row['age'], 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.20, $rowH, $row['job'], 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, "", 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.10, $rowH, $row['cohabit'], 1, 0, "C");
		$pdf->Cell(($pdf->width - $col[0]) * 0.25, $rowH, $row['remark'], 1, 1, "C");
	}

	$conn->row_free();

	Unset($row);
	Unset($col);


	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.37;

	$Y = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH * 3, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "( ".($isptGbn == '1' ? '√' : '   ').") 새로운 욕구가 발생", "LR", 2, "L");
	$pdf->Cell($col[1], $rowH, "( ".($isptGbn == '2' ? '√' : '   ').") 긴급한 상황이 발생", "LR", 2, "L");
	$pdf->Cell($col[1], $rowH, "( ".($isptGbn == '9' ? '√' : '   ').") 기타", "LR", 2, "L");

	$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
	$pdf->Cell($col[0], $rowH * 3, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "( ".($isptRsn == '1' ? '√' : '   ').") 재가노인에 의한 요인", "LR", 2, "L");
	$pdf->Cell($col[1], $rowH, "( ".($isptRsn == '2' ? '√' : '   ').") 기관과 사회복지사에 의한 요인", "LR", 2, "L");
	$pdf->Cell($col[1], $rowH, "( ".($isptRsn == '3' ? '√' : '   ').") 자원과 환경에 의한 요인", "LR", 1, "L");

	$Y = $pdf->GetY();

	$H = lfGetStringHeight($pdf, $col[0], "재사정\n유형");

	$pdf->SetXY($pdf->left, $Y - $rowH * 3 + ($rowH * 3 - $H) / 2);
	$pdf->MultiCell($col[0], 4, "재사정\n유   형", 0, "C");

	$H = lfGetStringHeight($pdf, $col[0], "재사정\n요인");

	$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y - $rowH * 3 + ($rowH * 3 - $H) / 2);
	$pdf->MultiCell($col[0], 4, "재사정\n요   인", 0, "C");

	Unset($col);


	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.87;

	$H1 = lfGetStringHeight($pdf, $col[0], "Client\n변화욕구");
	$H2 = lfGetStringHeight($pdf, $col[1], $isptNeed) + 1.6;
	$H  = ($H1 > $H2 ? $H1 : $H2);

	if ($H < $rowH * 2) $H = $rowH * 2;

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1);

	$Y = $pdf->GetY();

	$pdf->SetXY($pdf->left, $Y - $H + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "Client\n변화욕구", 0, "C");

	$pdf->SetXY($pdf->left + $col[0], $Y - $H + 0.8);
	$pdf->MultiCell($col[1], 4, $isptNeed);




	$H1 = lfGetStringHeight($pdf, $col[0], "서비스\n제공 및\n문제");
	$H2 = lfGetStringHeight($pdf, $col[1], $isptOffer) + 1.6;
	$H  = ($H1 > $H2 ? $H1 : $H2);

	if ($H < $rowH * 2) $H = $rowH * 2;

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1);

	$Y = $pdf->GetY();

	$pdf->SetXY($pdf->left, $Y - $H + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "서비스\n제공 및\n문제", 0, "C");

	$pdf->SetXY($pdf->left + $col[0], $Y - $H + 0.8);
	$pdf->MultiCell($col[1], 4, $isptOffer);



	$H1 = lfGetStringHeight($pdf, $col[0], "w/r'의\n의견");
	$H2 = lfGetStringHeight($pdf, $col[1], $isptWer) + 1.6;
	$H  = ($H1 > $H2 ? $H1 : $H2);

	if ($H < $rowH * 2) $H = $rowH * 2;

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1);

	$Y = $pdf->GetY();

	$pdf->SetXY($pdf->left, $Y - $H + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "wer'의\n의견", 0, "C");

	$pdf->SetXY($pdf->left + $col[0], $Y - $H + 0.8);
	$pdf->MultiCell($col[1], 4, $isptWer);



	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 2, "재사정 결과", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH * 2, ($isptRst == '1' ? '( √ )' : '(     )')."종   결      "
								  .($isptRst == '2' ? '( √ )' : '(     )')."서비스 재계획      "
								  .($isptRst == '3' ? '( √ )' : '(     )')."의   뢰      "
								  .($isptRst == '4' ? '( √ )' : '(     )')."현 상태 유지", 1, 1);



	$Y = $pdf->GetY();
	$H = $pdf->height - $Y;

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left + $col[0], $Y + 0.8);
	$pdf->MultiCell($col[1], 4, $isptPlan);
	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H, "향후계획", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1);
?>
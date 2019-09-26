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

	//대상자
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	$sql = 'SELECT	*
			FROM	hce_plan_sheet
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		plan_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$planer = $row['planer'];	//작성자
	$planDt	= $myF->dateStyle($row['plan_dt'],'.');	//일자
	$needs	= StripSlashes($row['needs']);		//재가노인 욕구
	$problem= StripSlashes($row['problem']);	//재가노인 문제
	$goal	= StripSlashes($row['goal']);		//목표
	$period	= StripSlashes($row['svc_period']);	//서비스기간
	$content= StripSlashes($row['svc_content']);//서비스내용
	$method	= StripSlashes($row['svc_method']);	//서비스방법
	$remark	= StripSlashes($row['remark']);		//비고

	Unset($row);

	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.35;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.35;

	$rowH = $pdf->row_height * 1.5;

	$pdf->SetFont($pdf->font_name_kor,'B',9);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0], $rowH, "대상자명", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "작성자", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $planer, 1, 1, "C");

	Unset($col);

	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.12;
	$col[] = $pdf->width * 0.73;

	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($col[0], $rowH, "날   짜", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $rowH, $planDt, 1, 1, "L");

	//재가노인의 욕구
	$X  = $pdf->left + $col[0];
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $needs);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "재가노인의 욕구", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");


	//재가노인의 문제
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $problem);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "재가노인의 문제", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");


	//재가노인 목표 $goal
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $goal);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "목   표", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");

	Unset($col);


	//비고
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.85;

	$Y = $pdf->GetY();
	$H = lfGetStringHeight($pdf,$col[1],$remark);

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left + $col[0], $Y + 0.7);
	$pdf->MultiCell($col[1], 3.5, $remark);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H, "비고", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	Unset($col);


	$Y = $pdf->GetY();

	if ($Y + $rowH > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}


	//서비스 제공계획
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width, $rowH, "서비스 제공계획", 1, 1, "L", 1);

	$col[] = $pdf->width * 0.05;
	$col[] = $pdf->width * 0.3;
	$col[] = $pdf->width * 0.2;
	$col[] = $pdf->width * 0.1;
	$col[] = $pdf->width * 0.35;

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "No", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "서비스내용", 1, 0, "C", 1);
	$pdf->Cell($col[2], $rowH, "기간", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, "횟수", 1, 0, "C", 1);
	$pdf->Cell($col[4], $rowH, "방법", 1, 1, "C", 1);


	$sql = 'SELECT	contents
			,		period
			,		times
			,		method
			FROM	hce_plan_sheet_item
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		org_type = \''.$hce->SR.'\'
			AND		IPIN	 = \''.$hce->IPIN.'\'
			AND		rcpt_seq = \''.$hce->rcpt.'\'
			AND		plan_seq = \''.$var['idx'].'\'
			AND		del_flag = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	if ($rowCnt > 0){
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$RH[] = lfGetStringHeight($pdf,$col[1],StripSlashes($row['contents']));
			$RH[] = lfGetStringHeight($pdf,$col[2],StripSlashes($row['period']));
			$RH[] = lfGetStringHeight($pdf,$col[3],StripSlashes($row['times']));
			$RH[] = lfGetStringHeight($pdf,$col[4],StripSlashes($row['method']));

			$tmpH = 0;

			for($j=0; $j<SizeOf($RH); $j++){
				if ($tmpH < $RH[$j]) $tmpH = $RH[$j];
			}

			Unset($RH);

			$Y = $pdf->GetY();

			if ($Y + $tmpH > $pdf->height){
				$pdf->MY_ADDPAGE();
				$pdf->SetFont($pdf->font_name_kor,'',9);

				$Y = $pdf->GetY();
			}

			$pdf->SetXY($pdf->left + $col[0], $Y + 0.5);
			$pdf->MultiCell($col[1], 3.7, StripSlashes($row['contents']));

			$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y + 0.5);
			$pdf->MultiCell($col[2], 3.7, StripSlashes($row['period']));

			$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2], $Y + 0.5);
			$pdf->MultiCell($col[3], 3.7, StripSlashes($row['times']));

			$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y + 0.5);
			$pdf->MultiCell($col[4], 3.7, StripSlashes($row['method']));

			$pdf->SetXY($pdf->left, $Y);
			$pdf->Cell($col[0], $tmpH, number_format($no), 1, 0, "C");
			$pdf->Cell($col[1], $tmpH, "", 1, 0, "L");
			$pdf->Cell($col[2], $tmpH, "", 1, 0, "L");
			$pdf->Cell($col[3], $tmpH, "", 1, 0, "L");
			$pdf->Cell($col[4], $tmpH, "", 1, 1, "L");

			$no ++;
		}
	}else{
		$RH[] = lfGetStringHeight($pdf,$col[1],$content);
		$RH[] = lfGetStringHeight($pdf,$col[2],$period);
		$RH[] = lfGetStringHeight($pdf,$col[3],"");
		$RH[] = lfGetStringHeight($pdf,$col[4],$method);

		$tmpH = 0;

		for($j=0; $j<SizeOf($RH); $j++){
			if ($tmpH < $RH[$j]) $tmpH = $RH[$j];
		}

		Unset($RH);

		$Y = $pdf->GetY();

		if ($Y + $tmpH > $pdf->height){
			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor,'',9);

			$Y = $pdf->GetY();
		}

		$pdf->SetXY($pdf->left + $col[0], $Y + 0.5);
		$pdf->MultiCell($col[1], 3.7, $content);

		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y + 0.5);
		$pdf->MultiCell($col[2], 3.7, $period);

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2], $Y + 0.5);
		$pdf->MultiCell($col[3], 3.7, "");

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y + 0.5);
		$pdf->MultiCell($col[4], 3.7, $method);

		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell($col[0], $tmpH, number_format($no), 1, 0, "C");
		$pdf->Cell($col[1], $tmpH, "", 1, 0, "L");
		$pdf->Cell($col[2], $tmpH, "", 1, 0, "L");
		$pdf->Cell($col[3], $tmpH, "", 1, 0, "L");
		$pdf->Cell($col[4], $tmpH, "", 1, 1, "L");
	}

	$conn->row_free();

	Unset($col);




	/*
	//서비스 제공계획 - 서비스기간
	$X	= $pdf->left + $col[0] + $col[1];
	$Y1 = $pdf->GetY();
	$Y3 = $Y1;

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $period);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LTR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "서비스기간", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//서비스 제공계획 - 서비스내용
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $content);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "서비스내용", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//서비스 제공계획 - 서비스방법
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $method);

	$Y2 = $pdf->GetY() + 1;
	$Y4 = $Y2;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LBR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "서비스방법", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//비고
	$H = $pdf->height - ($Y1 + $H);

	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y2,'width'=>$col[1] + $col[2],'text'=>$remark);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $H, "비고", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");

	$Y = $Y3 + ($Y4 - $Y3 - 10) / 2;

	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y,'width'=>$col[0],'align'=>'C','text'=>"서비스\n제공계획");
	*/


	if (is_array($pos)){
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
		}
	}

	Unset($pos);
	Unset($col);
?>
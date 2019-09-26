<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-욕구
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];

	//약도 파일
	$userMap = '../hce/user_map/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$hce->rcpt.'.jpg';

	$isptSeq = '1';

	$sql = 'SELECT	lifedays
			,		faircopy
			,		dwelling
			,		leisure
			,		interview
			,		local
			,		link
			,		educ
			,		emergency
			,		ext
			,		social_opinion
			,		rough_text
			FROM	hce_inspection_needs
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$lifedays		= StripSlashes($row['lifedays']);
	$faircopy		= StripSlashes($row['faircopy']);
	$dwelling		= StripSlashes($row['dwelling']);
	$leisure		= StripSlashes($row['leisure']);
	$interview		= StripSlashes($row['interview']);
	$local			= StripSlashes($row['local']);
	$link			= StripSlashes($row['link']);
	$educ			= StripSlashes($row['educ']);
	$emergency		= StripSlashes($row['emergency']);
	$ext			= StripSlashes($row['ext']);
	$socialOpinion	= StripSlashes($row['social_opinion']);
	$roughText		= StripSlashes($row['rough_text']);

	Unset($row);


	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.72;


	$rowHeight = $pdf->row_height * 0.8;


	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 욕구",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$Y = $pdf->GetY();

	//텍스트별 높이
	$txtH[0] = lfGetStringHeight($pdf, $col[2], $lifedays);
	$txtH[1] = lfGetStringHeight($pdf, $col[2], $faircopy);
	$txtH[2] = lfGetStringHeight($pdf, $col[2], $dwelling);
	$txtH[3] = lfGetStringHeight($pdf, $col[2], $leisure);
	$txtH[4] = lfGetStringHeight($pdf, $col[2], $interview);
	$txtH[5] = lfGetStringHeight($pdf, $col[2], $local);
	$txtH[6] = lfGetStringHeight($pdf, $col[2], $link);
	$txtH[7] = lfGetStringHeight($pdf, $col[2], $educ);
	$txtH[8] = lfGetStringHeight($pdf, $col[2], $emergency);
	$txtH[9] = lfGetStringHeight($pdf, $col[2], $ext);

	$txtCnt = SizeOf($txtH);

	for($i=0; $i<$txtCnt; $i++){
		if ($txtH[$i] < $rowHeight * 2) $txtH[$i] = $rowHeight * 2;
	}

	$txtHeight[0] = $txtH[0] + $txtH[1] + $txtH[2] + $txtH[3] + $txtH[4] + $txtH[5];
	$txtHeight[1] = $txtH[6] + $txtH[7];

	$h = $pdf->GetStringWidth("예 서");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y + ($txtHeight[0] - $h) / 2,'width'=>$col[0],'text'=>"예  방  적\n서  비  스");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$lifedays);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$txtHeight[0],"",1,0,'C',1);
	$pdf->Cell($col[1],$txtH[0],"일상생활지원",1,0,'C');
	$pdf->Cell($col[2],$txtH[0],"",1,1,'L');


	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$faircopy);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[1],"정  서  지  원",1,0,'C');
	$pdf->Cell($col[2],$txtH[1],"",1,1,'L');


	$Y = $pdf->GetY();
	$h = $pdf->GetStringWidth("주 개");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + ($txtH[2] - $h) / 2,'width'=>$col[1],'text'=>"주  거  환  경\n개  선  지  원");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$dwelling);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[2],"",1,0,'C');
	$pdf->Cell($col[2],$txtH[2],"",1,1,'L');


	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$leisure);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[3],"여가활동지원",1,0,'C');
	$pdf->Cell($col[2],$txtH[3],"",1,1,'L');


	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$interview);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[4],"상  담  지  원",1,0,'C');
	$pdf->Cell($col[2],$txtH[4],"",1,1,'L');


	$Y = $pdf->GetY();
	$h = $pdf->GetStringWidth("지 자");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + ($txtH[5] - $h) / 2,'width'=>$col[1],'text'=>"지  역  사  회\n자  원  개  발");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$local);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[5],"",1,0,'C');
	$pdf->Cell($col[2],$txtH[5],"",1,1,'L');


	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$link);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$txtHeight[1],"사회안전망구축",1,0,'C',1);
	$pdf->Cell($col[1],$txtH[6],"연  계  지  원",1,0,'C');
	$pdf->Cell($col[2],$txtH[6],"",1,1,'L');

	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$educ);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$txtH[7],"교  육  지  원",1,0,'C');
	$pdf->Cell($col[2],$txtH[7],"",1,1,'L');


	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$emergency);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$txtH[8],"긴  급  지  원",1,0,'C',1);
	$pdf->Cell($col[1],$txtH[8],"",1,0,'C');
	$pdf->Cell($col[2],$txtH[8],"",1,1,'L');

	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$Y + 0.5,'width'=>$col[2],'align'=>'L','text'=>$ext);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$txtH[9],"기             타",1,0,'C',1);
	$pdf->Cell($col[1],$txtH[9],"",1,0,'C');
	$pdf->Cell($col[2],$txtH[9],"",1,1,'L');


	$tH = lfGetStringHeight($pdf, $col[1]+$col[2], $socialOpinion);

	$Y = $pdf->GetY();
	$tH = $pdf->height - $Y;
	$h = $pdf->GetStringWidth("사 소");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y + ($tH - $h) / 2,'width'=>$col[0],'text'=>"사 회 복 지 사\n소             견");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 0.5,'width'=>$col[1]+$col[2],'align'=>'L','text'=>$socialOpinion);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$tH,"",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$tH,"",1,1,'L');


	/*
		$Y = $pdf->GetY();
		$tH = $pdf->height - $Y;
		if (Is_File($userMap)){
			$tmpImg = getImageSize($userMap);

			$W = $col[1]+$col[2] * 0.5;
			$H = $tH;

			if ($tmpImg[0] > $tmpImg[1]){
				$H = 0;
			}else{
				$W = 0;
			}
			$pdf->Image($userMap, $pdf->left + $col[0], $Y, $W, $H);
		}

		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1]+$col[2] * 0.5,'Y'=>$Y + 0.5,'width'=>$col[2] * 0.5,'align'=>'L','text'=>$roughText);

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$tH,"약도 및 교통편",1,0,'C',1);
		$pdf->Cell($col[1]+$col[2] * 0.5,$tH,"",1,0,'L');
		$pdf->Cell($col[2] * 0.5,$tH,"",1,1,'L');
	*/



	/*
	$h = $pdf->GetStringWidth("예 서");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY() + ($rowHeight * 12 - $h) / 2,'width'=>$col[0],'text'=>"예  방  적\n서  비  스");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$lifedays);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 12,"",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight * 2,"일상생활지원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');

	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$faircopy);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight * 2,"정  서  지  원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');

	$h = $pdf->GetStringWidth("주 개");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY() + ($rowHeight * 3 - $h) / 2,'width'=>$col[1],'text'=>"주  거  환  경\n개  선  지  원");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$dwelling);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight * 3,"",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 3,"",1,1,'L');

	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$leisure);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight * 2,"여가활동지원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight,"상  담  지  원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight,$interview,1,1,'L');

	$h = $pdf->GetStringWidth("지 자");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY() + ($rowHeight * 2 - $h) / 2,'width'=>$col[1],'text'=>"지  역  사  회\n자  원  개  발");
	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$local);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight * 2,"",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');



	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$link);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 4,"사회안전망구축",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight * 2,"연  계  지  원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');

	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$educ);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$rowHeight * 2,"교  육  지  원",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');



	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$emergency);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 2,"긴  급  지  원",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight * 2,"",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');

	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2],'align'=>'L','text'=>$ext);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 2,"기             타",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight * 2,"",1,0,'C');
	$pdf->Cell($col[2],$rowHeight * 2,"",1,1,'L');



	$h = $pdf->GetStringWidth("사 소");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY() + ($rowHeight * 10 - $h) / 2,'width'=>$col[0],'text'=>"사 회 복 지 사\n소             견");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY() + 0.5,'width'=>$col[1]+$col[2],'align'=>'L','text'=>$socialOpinion);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 10,"",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$rowHeight * 10,"",1,1,'L');



	if (Is_File($userMap)){
		$tmpImg = getImageSize($userMap);

		$W = $col[1]+$col[2] * 0.5;
		$H = $rowHeight * 22;

		if ($tmpImg[0] > $tmpImg[1]){
			$H = 0;
		}else{
			$W = 0;
		}
		$pdf->Image($userMap, $pdf->left + $col[0], $pdf->GetY(), $W, $H);
	}

	$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1]+$col[2] * 0.5,'Y'=>$pdf->GetY() + 0.5,'width'=>$col[2] * 0.5,'align'=>'L','text'=>$roughText);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight * 22,"약도 및 교통편",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2] * 0.5,$rowHeight * 22,"",1,0,'L');
	$pdf->Cell($col[2] * 0.5,$rowHeight * 22,"",1,1,'L');
	*/



	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 3.7, $row['text'], 0, ($row['align'] ? $row['align'] : 'C'));
	}

	Unset($pos);
	Unset($col);
?>
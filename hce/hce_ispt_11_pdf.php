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
	$isptSeq = $var['idx'];


	//약도 파일
	$userMap = '../hce/user_map/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$hce->rcpt.'.jpg';

	if ($isptSeq){
		$sql = 'SELECT	rough_text
				FROM	hce_inspection_needs
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		ispt_seq= \''.$isptSeq.'\'';
	}else{
		$sql = 'SELECT	rough_text
				FROM	hce_inspection_needs
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				ORDER	BY ispt_seq
				LIMIT	1';
	}

	$row = $conn->get_array($sql);

	$roughText = StripSlashes($row['rough_text']);

	Unset($row);


	$rowHeight = $pdf->row_height * 0.8;


	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 약도",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$Y = $pdf->GetY();

	$tH = $pdf->height - $Y - 40;
	if (Is_File($userMap)){
		$tmpImg = getImageSize($userMap);

		$W = $pdf->width;
		$H = $tH;

		if ($tmpImg[0] > $tmpImg[1]){
			$H = 0;
		}else{
			$W = 0;
		}
		$pdf->Image($userMap, $pdf->left, $Y, $W, $H);
	}

	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y + $tH + 1,'width'=>$pdf->width,'align'=>'L','text'=>$roughText);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$tH,'',1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,40,'',1,1,'L');


	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 3.7, $row['text'], 0, ($row['align'] ? $row['align'] : 'C'));
	}

	Unset($pos);
	Unset($col);
?>
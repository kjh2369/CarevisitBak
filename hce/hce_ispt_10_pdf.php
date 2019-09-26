<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-사진
	 *********************************************************/
	$conn->fetch_type = 'assoc';
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];
	$isptSeq = '1';

	$rowHeight = $pdf->row_height * 0.8;


	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 사진",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$sql = 'SELECT	pic_name
			,		pic_file
			,		pic_path
			FROM	hce_inspection_pic
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'
			AND		del_flag= \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			if ($i > 0){
				$pdf->MY_ADDPAGE();
				$pdf->SetXY($pdf->left,$pdf->top);
				$pdf->SetFont($pdf->font_name_kor,'B',13);
				$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 사진",1,1,'L',1);
				$pdf->SetFont($pdf->font_name_kor,'',9);
			}

			$Y = $pdf->GetY();
			$H = ($pdf->height+50 - $Y) / 2;
		}

		$Y = $pdf->GetY();
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width,$H,'',1,1,'L');
		$tmpY = $pdf->GetY();

		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell($pdf->width, $rowHeight, '제목 : '.$row['pic_name'], 0, 1, 'L');
		#$pdf->SetX($pdf->left);
		#$pdf->Cell($pdf->width, $rowHeight, '파일명 : '.$row['pic_file'], 0, 1, 'L');

		$Y = $pdf->GetY();

		//$row['pic_path'] = '../hce/img/1234/S/89/1_1_3.png';

		if (Is_File($row['pic_path'])){
			$tmpImg = getImageSize($row['pic_path']);

			$imgW = $pdf->width * 0.95;
			$imgH = ($H - $rowHeight) * 0.95;
			
			//if($debug) echo $tmpImg[0].'/'.$tmpImg[1].'//';

			if ($tmpImg[0] > $tmpImg[1]){
				$imgH = 0;
			}else{
				$imgW = 0;
			}

			$pdf->Image($row['pic_path'], $pdf->left + 2, $Y + 2, $imgW, $imgH);
		}

		$pdf->SetY($tmpY);
	}

	$conn->row_free();
?>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���ȸ�Ƿ�-����
	 *********************************************************/
	$conn->fetch_type = 'assoc';
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];
	$meetSeq = $var['idx'];

	$sql = 'SELECT	pic_name
			,		pic_file
			,		pic_path
			FROM	hce_meeting_pic
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_seq= \''.$meetSeq.'\'
			AND		del_flag= \'N\'';
	//if($debug) echo nl2br($sql);
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	if($rowCnt > 0){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$pdf->SetXY($pdf->left, $pdf->top);

		$rowHeight = $pdf->row_height * 0.7;

		$pdf->SetXY($pdf->left,$pdf->top+15);
		$pdf->SetFont($pdf->font_name_kor,'B',13);
		$pdf->Cell($pdf->width,$rowHeight * 1.5,"�� ����",1,1,'L',1);
		$pdf->SetFont($pdf->font_name_kor,'',9);	
	}
	

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			if ($i > 0){
				$pdf->MY_ADDPAGE();
				$pdf->SetXY($pdf->left,$pdf->top+15);
				$pdf->SetFont($pdf->font_name_kor,'B',13);
				$pdf->Cell($pdf->width,$rowHeight * 1.5,"�� ����",1,1,'L',1);
				$pdf->SetFont($pdf->font_name_kor,'',9);
			}

			$Y = $pdf->GetY();
			$H = ($pdf->height - $Y) / 2;
		}

		$Y = $pdf->GetY();
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width,$H,'',1,1,'L');
		$tmpY = $pdf->GetY();

		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell($pdf->width, $rowHeight, '���� : '.$row['pic_name'], 0, 1, 'L');
		#$pdf->SetX($pdf->left);
		#$pdf->Cell($pdf->width, $rowHeight, '���ϸ� : '.$row['pic_file'], 0, 1, 'L');

		$Y = $pdf->GetY();

		//$row['pic_path'] = '../hce/img/1234/S/89/1_1_3.png';

		if (Is_File($row['pic_path'])){
			$tmpImg = getImageSize($row['pic_path']);

			$imgW = $pdf->width * 0.80;
			$imgH = ($H - $rowHeight) * 0.80;
			
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
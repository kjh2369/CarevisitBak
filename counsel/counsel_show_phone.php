<?
	/**************************************************
		상담내용
	**************************************************/
	$pos_x = $pdf->left + $pdf->width * 0.62 + 1.5;

	switch($data['phone_kind']){
		case '1':
			$pos_x += $pdf->GetStringWidth(' ');
			break;
		case '2':
			$pos_x += $pdf->GetStringWidth(' □고객     ');
			break;
		case '3':
			$pos_x += $pdf->GetStringWidth(' □고객     □직원     ');
			break;
	}

	$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
	
	$getY = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height * 2, '상담이력', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상 담 자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.28, $pdf->row_height, $data['phone_m_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상담유형', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.38, $pdf->row_height, ' □고객     □직원     □관리자', 1, 1, 'L');
	
	
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상담일자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.28, $pdf->row_height, $myF->dateStyle($data['phone_dt'],'.'), 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상담시간', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.08, $pdf->row_height, '시 작', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.11, $pdf->row_height, substr($data['phone_start'],0,5), 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.08, $pdf->row_height, '종 료', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.11, $pdf->row_height, substr($data['phone_end'],0,5), 1, 1, 'L');

	$col_width_1 = $pdf->width * 0.76;
	$col_width_2 = $pdf->width * 0.875;
	$row_height_1 = $pdf->row_height * 8;
	$row_height_2 = $pdf->row_height * 9;
	
	$high_0 = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['phone_contents']);
	$high_1 = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['phone_result']);
	$high_2 = get_row_cnt($pdf, $col_width_2, $row_height_2, $data['phone_other']);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['phone_contents'])));
	
	$height = $pdf->GetY();
	
	

	$gY1 = $height + $high_0;
	$gY2 = $gY1 + $high_1;
	$gY3 = $gY2 + $high_2;
		
	
	$result_getY = $high_0+$high_1;
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $result_getY, '상담내용', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $high_0, '내   용', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_0, '', 1, 1, 'L');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['phone_result'])));

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_1, '결   과', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_1, '', 1, 1, 'L');
	
	$HAll = $high_0+$high_1+$high_2;


	
	if($HAll>165){
			
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, ($result_getY+$pdf->row_height*2));
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		$page = 2;
		$getY = $pdf->getY();
		
	}else {
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, ($result_getY+$pdf->row_height*2));
		$pdf->SetLineWidth(0.2);
		
		
	}

	$rect_h = $high_2;
	$gY = $rect_h;

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.90, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['phone_other'])));

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $high_2, '기   타', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.90, $high_2, '', 1, 1, 'L');
	
	/*
	if($gY3>272){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY3);
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.10, $high_2, '기   타', 1, 0, 'C', 1);
		$pdf->Cell($pdf->width * 0.90, $high_2, '', 1, 1, 'L');

		$rect_h = $high_2;
	}
	*/
	/*
	if($gY3<272){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, ($high_0+$high_1+$high_2+$pdf->row_height*2));
		$pdf->SetLineWidth(0.2);
	}
	*/
	if($page == 2){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
		$pdf->SetLineWidth(0.2);
	}

	
	



	/**************************************************

		기타 텍스트 출력 부분

		x         : X좌표
		y         : Y좌표
		type      : 출력형식
		width     :
		height    :
		font_size :
		align     :
		border	  :
		text      : 출력텍스트

	**************************************************/
	set_array_text($pdf, $pos);
	unset($pos);
	unset($data);
?>

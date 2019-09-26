<?
	$getY = $pdf->GetY();
	
	/**************************************************
		상담내용
	**************************************************/
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, '상담이력', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상 담 자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.28, $pdf->row_height, $data['visit_m_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '상담일자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.38, $pdf->row_height, $myF->dateStyle($data['visit_dt'],'.').' '.SubStr($data['visit_tm'],0,5), 1, 1, 'L');
	
	$col_width_1 = $pdf->width * 0.875;
	$col_width_2 = $pdf->width * 0.76;
	$row_height_1 = $pdf->row_height * 4;
	$row_height_2 = $pdf->row_height * 4.5;
	$row_height_3 = $pdf->row_height * 7;

	$high_0 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['visit_h_nh']);
	$high_1 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['visit_h_cf']);
	$high_2 = get_row_cnt($pdf, $col_width_2, $row_height_2, $data['visit_h_body']);
	$high_3 = get_row_cnt($pdf, $col_width_2, $row_height_2, $data['visit_h_soul']);
	$high_4 = get_row_cnt($pdf, $col_width_1, $row_height_3, $data['visit_other']);
	
	//$height = $pdf->GetY();

	$gY1 = $pdf->row_height + $high_0;
	$gY2 = $gY1 + $high_1;
	$gY3 = $gY2 + $high_2;
	$gY4 = $gY3 + $high_3;
	$gY5 = $gY4 + $high_4;
	
	
	if($gY2>272){
		$result_getY = $high_1+$high_2+$high_3;
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $gY4, '건강상태', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '혈     압', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, $data['visit_h_bp'], 'LTB', 0, 'R');
	$pdf->Cell($pdf->width * 0.08, $pdf->row_height, 'mmHg', 'RTB', 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '혈     당', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.18, $pdf->row_height, '식 전 '.$data['visit_h_bf_bs'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, '식후 '.$data['visit_h_af_time'].'시간 '.$data['visit_h_af_bs'], 1, 1, 'L');
	
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_h_nh'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_0, '영     양', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_0, '', 1, 1, 'L');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_h_cf'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_1, '배     설', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_1, '', 1, 1, 'L');
	
	/*
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height * 18, '건강상태', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '혈     압', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $data['visit_h_bp'], 'LTB', 0, 'R');
	$pdf->Cell($pdf->width * 0.08, $pdf->row_height, 'mmHg', 'RTB', 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height * 2, '혈     당', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.18, $pdf->row_height, '식 전', 1, 0, 'L', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $data['visit_h_bf_bs'], 1, 1, 'R');

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '영     양', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.28, $pdf->row_height, $data['visit_h_nh'], 1, 0, 'R');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '', 0, 0, 'C');
	$pdf->Cell($pdf->width * 0.18, $pdf->row_height, '식후 '.$data['visit_h_af_time'].'시간', 1, 0, 'L', 1);
	$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $data['visit_h_af_bs'], 1, 1, 'R');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>3.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_h_cf'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height * 4, '배     설', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $pdf->row_height * 4, '', 1, 1, 'L');
	*/

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_h_body'])));
	$pdf->Cell($pdf->width * 0.12, $high_2, '신체적상태', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_2, '', 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_h_soul'])));
	$pdf->Cell($pdf->width * 0.12, $high_3, '정신적상태', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_3, '', 1, 1, 'L');
	

	if($gY5>165){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY4 + $pdf->row_height);
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		
		$getY2 = $pdf->GetY();
	
	}else {
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY5 + $pdf->row_height);
		$pdf->SetLineWidth(0.2);
	}

	
	$pdf->SetX($pdf->left);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.90, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['visit_other'])));
	$pdf->Cell($pdf->width * 0.10, $high_4, '기   타', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.90, $high_4, '', 1, 1, 'L');
	


	if($getY2 != ''){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY2, $pdf->width, $get+$high_4);
		$pdf->SetLineWidth(0.2);
	}
	
	/*
	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->row_height * 12, $pdf->width, $rect_h);
	$pdf->SetLineWidth(0.2);
	*/

	/*
	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 28, $pdf->width, $pdf->row_height * 28);
	$pdf->SetLineWidth(0.2);
	*/




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
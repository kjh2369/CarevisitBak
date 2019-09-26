<?
	$getY = $pdf->GetY();

	/**************************************************
		상담내용
	**************************************************/
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height * 3, '상담이력', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, '접 수 자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $data['stress_m_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '접수일자', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.38, $pdf->row_height, $myF->dateStyle($data['stress_dt'],'.'), 1, 1, 'L');

	$pos_x = $pdf->left + $pdf->width * 0.20 + 1.5;

	switch($data['stress_rct_path']){
		case '1':
			break;
		case '2':
			$pos_x += $pdf->GetStringWidth('□내방  ');
			break;
		case '3':
			$pos_x += $pdf->GetStringWidth('□내방  □방문  ');
			break;
		case '4':
			$pos_x += $pdf->GetStringWidth('□내방  □방문  □전화  ');
			break;
		case '5':
			$pos_x += $pdf->GetStringWidth('□내방  □방문  □전화  □홈페이지  ');
			break;
		default:
			$pos_x += $pdf->GetStringWidth('□내방  □방문  □전화  □홈페이지  □서면(내용증면여부(□Yes/□No))  ');
	}

	$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');

	if ($data['stress_rct_path'] == '5'){
		$pos_x = $pdf->left + $pdf->width * 0.20 + 1.5;

		if ($data['stress_rct_path_paper_yn'] == 'Y'){
			$pos_x += $pdf->GetStringWidth('□내방  □방문  □전화  □홈페이지  □서면(내용증면여부(');
			$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
		}else{
			$pos_x += $pdf->GetStringWidth('□내방  □방문  □전화  □홈페이지  □서면(내용증면여부(□Yes/');
			$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
		}
	}

	if (empty($data['stress_rct_path_other'])) $data['stress_rct_path_other'] = '        ';

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, '접수경로', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $pdf->row_height, '□내방  □방문  □전화  □홈페이지  □서면(내용증면여부(□Yes/□No))  □기타('.$data['stress_rct_path_other'].')', 1, 1, 'L');





	$pos_x = $pdf->left + $pdf->width * 0.20 + 1.5;

	if (empty($data['stress_rct_kind_family'])) $data['stress_rct_kind_family'] = '          ';
	if (empty($data['stress_rct_kind_other'])) $data['stress_rct_kind_other'] = '          ';

	switch($data['stress_rct_kind']){
		case '1':
			break;
		case '2':
			$pos_x += $pdf->GetStringWidth('□본인  ');
			break;
		case '3':
			$pos_x += $pdf->GetStringWidth('□본인  □가족('.$data['stress_rct_kind_family'].')  ');
			break;
		default:
			$pos_x += $pdf->GetStringWidth('□본인  □가족('.$data['stress_rct_kind_family'].')  □직원  ');
	}

	$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, '접 수 인', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $pdf->row_height, '□본인  □가족('.$data['stress_rct_kind_family'].')  □직원  □기타('.$data['stress_rct_kind_other'].')', 1, 1, 'L');





	$pos_x = $pdf->left + $pdf->width * 0.10 + 1.5;

	switch($data['stress_cont_kind']){
		case '1':
			break;
		case '2':
			$pos_x += $pdf->GetStringWidth('□직원불친절     ');
			break;
		case '3':
			$pos_x += $pdf->GetStringWidth('□직원불친절     □서비스내용     ');
			break;
		default:
			$pos_x += $pdf->GetStringWidth('□직원불친절     □서비스내용     □서비스비용     ');
	}

	/*
	$str_text =  explode("\n", stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_proc_text'])));

	$str_cnt = sizeof($str_text);
	$row_cnt = 1;
	$row_high = 5;
	for($i=0; $i<$str_cnt; $i++){
		$str_wid = $pdf->GetStringWidth($str_text[$i]);

		if($str_wid > 163.5){
			$row_cnt += ceil($str_wid/163.5);
		}else {
			$row_cnt += 1;
		}

	}

	$row_high = 3.5*$row_cnt;
	echo $row_high;
	$high = ($row_cnt-5)*3.6;
	*/
	/*
	$col_width_1 = $pdf->width * 0.90;
	$col_width_2 = $pdf->width * 0.80;

	$high_0 = get_row_cnt($pdf, $col_width_1, $data['stress_cont_text']);
	$high_1 = get_row_cnt($pdf, $col_width_2, $data['stress_proc_text']);
	$high_2 = get_row_cnt($pdf, $col_width_2, $data['stress_rst_obj']);
	$high_3 = get_row_cnt($pdf, $col_width_2, $data['stress_rst_sub']);
	$high_4 = get_row_cnt($pdf, $col_width_2, $data['stress_rst_app']);
	$high_5 = get_row_cnt($pdf, $col_width_2, $data['stress_rst_otr']);
	$high_6 = get_row_cnt($pdf, $col_width_1, $data['stress_after_plan']);
	*/

	$col_width_1 = $pdf->width * 0.875;
	$col_width_2 = $pdf->width * 0.76;
	$row_height_1 = $pdf->row_height * 2.35;
	$row_height_2 = $pdf->row_height * 5;

	$high_0 = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['stress_cont_text']);
	$high_1 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['stress_proc_text']);
	$high_2 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['stress_rst_obj']);
	$high_3 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['stress_rst_sub']);
	$high_4 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['stress_rst_app']);
	$high_5 = get_row_cnt($pdf, $col_width_2, $row_height_1, $data['stress_rst_otr']);
	$high_6 = get_row_cnt($pdf, $col_width_1, $row_height_2, $data['stress_after_plan']);


	$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY() + ($high_0 + $pdf->row_height - $pdf->GetStringWidth('▒▒')) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.10, 'height'=>3.5, 'align'=>'C', 'text'=>"불만 및\n고충 내용");
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $high_0+$pdf->row_height, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.90, $pdf->row_height, '□직원불친절     □서비스내용     □서비스비용     □기타', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.90, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_cont_text'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.90, $high_0, '', 1, 1, 'L');


	$pos_x = $pdf->left + $pdf->width * 0.10 + 1.5;

	switch($data['stress_proc_kind']){
		case '1':
			break;
		case '2':
			$pos_x += $pdf->GetStringWidth('□경청 등 효과적 대화     ');
			break;
		case '3':
			$pos_x += $pdf->GetStringWidth('□경청 등 효과적 대화     □이용자 방문     ');
			break;
		default:
			$pos_x += $pdf->GetStringWidth('□경청 등 효과적 대화     □이용자 방문     □서면     ');
	}

	$pos[sizeof($pos)] = array('x'=>$pos_x, 'y'=>$pdf->GetY() + 1.8, 'width'=>2, 'height'=>0, 'type'=>'image', 'text'=>'../image/check.gif');
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $high_1+$pdf->row_height, '처리방법', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.90, $pdf->row_height, '□경청 등 효과적 대화     □이용자 방문     □서면     □기타', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.90, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_proc_text'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.90, $high_1, '', 1, 1, 'L');


	$height = $pdf->GetY();

	$gY1 = $height + $high_2;
	$gY2 = $gY1 + $high_3;
	$gY3 = $gY2 + $high_4;
	$gY4 = $gY3 + $high_5;
	$gY5 = $gY4 + $high_6;

	if($gY4 > 283){
		
	}else {
		
	}
	

	if($gY2>283){
		$result_getY = $high_2;
		$gY6 = $high_0 + $high_1 + $high_2 + $pdf->row_height * 5;
	}else if($gY3>283){
		$result_getY = $high_2+$high_3;
		$gY6 = $high_0 + $high_1 + $high_2 + $high_3 + $pdf->row_height * 5;
	}else if($gY4>283){
		$result_getY = $high_2+$high_3+$high_4;
		$gY6 = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $pdf->row_height * 5;
	}else {
		$result_getY = $high_2+$high_3+$high_4+$high_5;
		if($gY5 > 283){
			$gY6 = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $pdf->row_height * 5;
		}else {
			$gY6 = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $pdf->row_height * 5;
		}
	}

	
	//$rect_h = $gY6;

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + ($high_2 - $pdf->GetStringWidth('▒▒')) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.10, 'height'=>3.5, 'align'=>'C', 'text'=>"객관적\n결   과");
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.20, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_rst_obj'])));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $result_getY, '처리결과', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.10, $high_2, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $high_2, '', 1, 1, 'L');
	
	
	if($gY2>283){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY6);
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.10, $high_3 + $high_4 + $high_5, '처리결과', 1, 0, 'C', 1);

		$rect_h = $high_3 + $high_4 + $high_5 + $high_6;
	}

	$gY3 = $pdf->GetY() + $high_3 + $high_4 + $high_5;

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + ($high_3 - $pdf->GetStringWidth('▒▒▒')) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.10, 'height'=>3.5, 'align'=>'C', 'text'=>"접수인의\n주 관 적\n반   응");
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.20, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_rst_sub'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.10, $high_3, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $high_3, '', 1, 1, 'L');

	
	if($gY3>283){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY6);
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.10, $high_4 + $high_5, '처리결과', 1, 0, 'C', 1);

		$rect_h = $high_4 + $high_5 + $high_6;
	}
	
	$gY4 = $pdf->GetY() + $high_4 + $high_5;

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + ($high_4 - $pdf->GetStringWidth('▒▒')) / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.10, 'height'=>3.5, 'align'=>'C', 'text'=>"직원의\n평   과");
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.20, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_rst_app'])));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.10, $high_4, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $high_4, '', 1, 1, 'L');

	if($gY4>283){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY6);
		$pdf->SetLineWidth(0.2);

		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.10, $high_5, '처리결과', 1, 0, 'C', 1);

		$rect_h = $high_5 + $high_6;
	}
	
	$gY5 = $pdf->GetY() + $high_5;

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.20, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_rst_otr'])));
	$pdf->Cell($pdf->width * 0.10, $high_5, '기   타', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.80, $high_5, '', 1, 1, 'L');

	if($gY5>283){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY6);
		$pdf->SetLineWidth(0.2);
		set_array_text($pdf, $pos);
		unset($pos);
		$pdf->AddPage(strtoupper('p'), 'A4');

		$rect_h = $high_6;
	}

	
	$gY6 = $pdf->GetY() + $high_6;
	
	
	$pdf->SetX($pdf->left);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.10, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.90, 'height'=>4.5, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", $data['stress_after_plan'])));
	$pdf->Cell($pdf->width * 0.10, $high_6, '이후계획', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.90, $high_6, '', 1, 1, 'L');
	
	if($gY6>283){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY6);
		$pdf->SetLineWidth(0.2);
	}
	
	if(!$rect_h) $rect_h = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $pdf->row_height * 10;
	 
	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->row_height * 14, $pdf->width, $rect_h);
	$pdf->SetLineWidth(0.2);


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
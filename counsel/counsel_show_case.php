<?
	$getY = $pdf->GetY();

	/**************************************************

	**************************************************/
	$str_present = $pdf->_splitTextWidth($myF->utf($data['case_present_nm']), $pdf->width * 0.24);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height, 'ȸ���̷�', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, 'ȸ������', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $myF->dateStyle($data['case_dt'],'.'), 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '�� �� ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['case_run_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '�� �� ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.24, $pdf->row_height, $str_present, 1, 1, 'L');




	/**************************************************

	**************************************************/
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY() + $pdf->row_height * 2 - $pdf->GetStringWidth('��  ��'), 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.10, 'height'=>3.5, 'align'=>'C', 'text'=>"����\n������");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $pdf->row_height * 2, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '��   ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['case_m_nm'], 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '��   ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $data['case_m_age'].'��', 1, 0, 'L');
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '��   ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.24, $pdf->row_height, $data['case_m_gender'], 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '�������', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($data['case_m_career']), $pdf->width * 0.78), 1, 1, 'L');

	$col_width_1 = $pdf->width * 0.757;
	$row_height_1 = $pdf->row_height * 2.5;
	$row_height_2 = $pdf->row_height * 2.2;
	$row_height_3 = $pdf->row_height * 3.7;

	$high_0  = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['case_economy']);
	$high_1  = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['case_family']);
	$high_2  = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['case_soul']);
	$high_3  = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['case_body']);
	$high_4  = get_row_cnt($pdf, $col_width_1, $row_height_1, $data['case_other']);
	$high_5  = get_row_cnt($pdf, $col_width_1, $row_height_2, $data['case_main_quest']);
	$high_6  = get_row_cnt($pdf, $col_width_1, $row_height_3, $data['case_present_talk']);
	$high_7  = get_row_cnt($pdf, $col_width_1, $row_height_2, $data['case_later_plan']);
	$high_8  = get_row_cnt($pdf, $col_width_1, $row_height_2, $data['case_proc_period']);
	$high_9  = get_row_cnt($pdf, $col_width_1, $row_height_2, $data['case_after_plan']);

	$height = $pdf->GetY();
	
	$gY1  = $height + $high_0;
	$gY2  = $gY1 + $high_1;
	$gY3  = $gY2 + $high_2;
	$gY4  = $gY3 + $high_3;
	$gY5  = $gY4 + $high_4;
	$gY6  = $gY5 + $high_5;
	$gY7  = $gY6 + $high_6;
	$gY8  = $gY7 + $high_7;
	$gY9  = $gY8 + $high_8;
	$gY10 = $gY9 + $high_9;

	if($gY6 > 272){
		$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $pdf->row_height * 3;
	}else {
		if($gY7 > 272){
			$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $pdf->row_height * 3;
		}else {
			if($gY8 > 272){
				$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $pdf->row_height * 3;
			}else {
				if($gY9 > 272){
					$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $high_7 + $pdf->row_height * 3;
				}else {
					if($gY10 > 272){
						$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $high_7 + $high_8 + $pdf->row_height * 3;
					}else {
						$gY = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $high_7 + $high_8 + $high_9 + $pdf->row_height * 3;
					}
				}
			}
		}
	}


	//$gY6 = $high_0 + $high_1 + $high_2 + $high_3 + $high_4 + $high_5 + $high_6 + $high_7 + $high_8 + $high_9 + $high_10 + $pdf->row_height * 3;

	/*
	if($gY5>272){
		$result_getY = $high_0+$high_1+$high_2;
	}else {
		$result_getY = $high_2+$high_3+$high_4+$high_5;
	}
	*/

	$result_getY = $high_0+$high_1+$high_2+$high_3+$high_4;

	/**************************************************

	**************************************************/
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_economy']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.10, $result_getY, '�䱸����', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $high_0, '�� �� ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_0, '', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_family']));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_1, '����ȯ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_1, '', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_soul']));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_2, '�� �� ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_2, '', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_body']));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_3, '�� ü ��', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_3, '', 1, 1, 'L');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_other']));
	$pdf->SetX($pdf->left + $pdf->width * 0.10);
	$pdf->Cell($pdf->width * 0.12, $high_4, '��   Ÿ', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_4, '', 1, 1, 'L');
	
	if($page == 2){
		$gY6 = $pdf->getY();
	}
	
	if($gY5<272){
		if($gY6>272){
			if($page == 2){
				$gY =  $gY-$high_5-$high_6-$high_7-$high_8-$high_9;
			}
			
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
			$pdf->SetLineWidth(0.2);
			set_array_text($pdf, $pos);
			unset($pos);
			$pdf->AddPage(strtoupper('p'), 'A4');
			$pdf->SetY(72);
			$page = 2;
			$rect_h = $high_5  + $high_6  + $high_7 + $high_8 + $high_9;
			$getY = $pdf->getY();
			$gY = $rect_h;
		}
	}
	
	/**************************************************

	**************************************************/
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_main_quest']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.22, $high_5, '�ֿ� ������', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_5, '', 1, 1, 'L');
	
	if($page == 2){
		$gY7 = $pdf->getY();
	}

	if($gY6<272){
		if($gY7>272){
			if($page == 2){
				$gY =  $gY-$high_6-$high_7-$high_8-$high_9;
			}
			
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
			$pdf->SetLineWidth(0.2);
			set_array_text($pdf, $pos);
			unset($pos);
			$pdf->AddPage(strtoupper('p'), 'A4');
			$pdf->SetY(72);
			$page = 2;
			$rect_h = $high_6  + $high_7 + $high_8 + $high_9;
			$getY = $pdf->getY();
			$gY = $rect_h;
		}
	}


	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_present_talk']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.22, $high_6, '������ �ǰ�', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_6, '', 1, 1, 'L');
	
	if($page == 2){
		$gY8 = $pdf->getY();
	}

	if($gY7<272){
		if($gY8>272){
			if($page == 2){
				$gY =  $gY-$high_7-$high_8-$high_9;
			}
			
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
			$pdf->SetLineWidth(0.2);
			set_array_text($pdf, $pos);
			unset($pos);
			$pdf->AddPage(strtoupper('p'), 'A4');
			$pdf->SetY(72);
			$page = 2;
			$rect_h = $high_7 + $high_8 + $high_9;
			$getY = $pdf->getY();
			$gY = $rect_h;
		}
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_later_plan']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.22, $high_7, '���� ��ȹ', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_7, '', 1, 1, 'L');
	
	if($page == 2){
		$gY9 = $pdf->getY();
	}

	if($gY8<272){
		if($gY9>272){
			if($page == 2){
				$gY =  $gY-$high_8-$high_9;
			}
			
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
			$pdf->SetLineWidth(0.2);
			set_array_text($pdf, $pos);
			unset($pos);
			$pdf->AddPage(strtoupper('p'), 'A4');
			$pdf->SetY(72);
			$page = 2;
			$rect_h = $high_8 + $high_9;
			$getY = $pdf->getY();
			$gY = $rect_h;
		}
	}
	
	if($page == 2){
		$gY9 = $pdf->getY();
	}

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_proc_period']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.22, $high_8, '���� ����', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_8, '', 1, 1, 'L');
	
	if($page == 2){
		$gY10 = $pdf->getY();
	}

	if($gY9<272){
		if($gY10>272){
			if($page == 2){
				$gY =  $gY-$high_9;
			}
			
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
			$pdf->SetLineWidth(0.2);
			set_array_text($pdf, $pos);
			unset($pos);
			$pdf->AddPage(strtoupper('p'), 'A4');
			$pdf->SetY(72);
			$page = 2;
			$rect_h = $high_9;
			$getY = $pdf->getY();
			$gY = $rect_h;
		}
	}

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.22, 'y'=>$pdf->GetY() + 1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.78, 'height'=>4.5, 'align'=>'L', 'text'=>str_replace(chr(13).chr(10), "\n", $data['case_after_plan']));
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.22, $high_9, '���� ��ȹ', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.78, $high_9, '', 1, 1, 'L');

	
	/**************************************************

	**************************************************/
	if($gY10<272){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $getY, $pdf->width, $gY);
		$pdf->SetLineWidth(0.2);
	}


	//$pdf->SetLineWidth(0.6);
	//$pdf->Rect($pdf->left, $pdf->row_height * 12, $pdf->width, $rect_h);
	//$pdf->SetLineWidth(0.2);

	/*
	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 28, $pdf->width, $pdf->row_height * 28);
	$pdf->SetLineWidth(0.2);
	*/




	/**************************************************

		��Ÿ �ؽ�Ʈ ��� �κ�

		x         : X��ǥ
		y         : Y��ǥ
		type      : �������
		width     :
		height    :
		font_size :
		align     :
		border	  :
		text      : ����ؽ�Ʈ

	**************************************************/
	set_array_text($pdf, $pos);
	unset($pos);
	unset($data);


	//÷�� �̹��� ���
	$counselId	= '0040';

	$sql = 'SELECT	file_attch
			,		subject
			FROM	counsel_file
			WHERE	org_no		= \''.$code.'\'
			AND		counsel_id	= \''.$counselId.'\'
			AND		yymm		= \''.$yymm.'\'
			AND		seq			= \''.$seq.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);

		if ($i % 2 == 0){
			$pdf->AddPage();
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($pdf->left, $pdf->GetY() + 3, $pdf->width, $pdf->height - $pdf->GetY() - 3);
			$pdf->Line($pdf->left, $pdf->GetY() + ($pdf->height - $pdf->GetY() - 3) / 2, $pdf->left + $pdf->width, $pdf->GetY() + ($pdf->height - $pdf->GetY() - 3) / 2);
			$pdf->SetLineWidth(0.2);
		}

		$pic	= '../file/'.$counselId.'/'.$row['file_attch'];
		$picIf	= GetImageSize($pic);
		$picW	= ($pdf->width - 1)*3.8;

		if ($i % 2 == 0){
			$picH	= ($pdf->height - $pdf->GetY() - 12) / 2 * 3.8;
		}else{
			$picH	= ($pdf->height - $pdf->GetY()) * 3.8;
		}

		$drawW	= 0;
		$drawH	= 0;

		if ($picIf[0] > $picIf[1]){
			$drawW	= $picIf[0];

			if ($drawW > $picW){
				$drawW	= $picW;
			}
		}else if ($picIf[0] < $picIf[1]){
			$drawH	= $picIf[1];

			if ($drawH > $picH){
				$drawH	= $picH;
			}
		}

		if ($drawW > 0){
			$drawW	= $drawW / 3.8;
		}

		if ($drawH > 0){
			$drawH	= $drawH / 3.8;
		}

		$img	= $pdf->Image($pic, $pdf->left + 0.5, $pdf->GetY() + 3.5, $drawW, $drawH);
		$pdf->SetXY($pdf->left, $pdf->GetY() + ($pdf->height - $pdf->GetY() - 8) / 2);
	}

	$conn->row_free();
?>
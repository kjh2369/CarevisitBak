<?
	$fontType1 = array('name'=>$pdf->font_name_kor,'bold'=>'','size'=>$font_l+1);
	$fontType2 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>$font_l+1);

	/**************************************************
		항목
	**************************************************/
	$tempY1 = $pdf->GetY();
	$pdf->SetX($left);
	$pdf->Cell($draw_w * 0.25,$row_h,'항목',1,0,'C');
	$pdf->Cell($draw_w * 0.25,$row_h,'금액',1,0,'C');
	$pdf->Cell($draw_w * 0.50,$row_h,'금액산정내역',1,1,'C');



	/**************************************************
		금액계산
	**************************************************/
	/*
	foreach($kind_list as $i => $k){
		if ($k['id'] == 11){
			$amt['tot'] = $svc_dt['svc0_suga']+$svc_dt['svc0_over'];
			$amt['my']  = $svc_dt['svc0_bonin']+$svc_dt['svc0_over'];
		}else{
			$amt['tot'] += $svc_dt['svc'.$k['code'].'_bonin'];
			$amt['my']  += $svc_dt['svc'.$k['code'].'_bonin'];
		}
	}
	*/

	if ($unpaid_yn == 'Y')
		$amt['unpaid'] = number_format($svc_dt['unpaid']);
	else
		$amt['unpaid'] = '';


	if($svc_dt['c_cd'] == ''){
		$svc_bonin = '';
		$tot = '';
		$svc_public = '';
		$svc_suga = '';
		$my = '';
		$in_pay1 = '';
		$in_pay2 = '';
		$in_pay3 = '';
		$in_pay_tot = '';
	}else {
		$svc_bonin = number_format($svc_dt['svc0_bonin']);
		$tot = number_format($amt['tot']);
		$svc_public = number_format($svc_dt['svc0_public']);
		$svc_suga = number_format($svc_dt['svc0_suga']);
		$my = number_format($amt['my']);
		$in_pay1 = number_format($in_pay[1]['pay']);
		$in_pay2 = number_format($in_pay[2]['pay']);
		$in_pay3 = number_format($in_pay[3]['pay']);
		$in_pay_tot = number_format($in_pay[1]['pay'] + $in_pay[2]['pay'] + $in_pay[3]['pay']);
	}

	$pos[sizeof($pos)] = array('x'=>$left, 'y'=>$pdf->GetY() + ($row_h * 3 - $pdf->GetStringWidth('급  여')) / 2, 'type'=>'multi_text', 'width'=>$draw_w * 0.05, 'height'=>5 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"급\n여");
	$pos[sizeof($pos)] = array('x'=>$left + $draw_w * 0.50, 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.25, 'height'=>4 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"총액(급여+비급여)\n⑨(③+⑧)");
	$pdf->SetX($left);
	#$pdf->Cell($draw_w * 0.05,$row_h * 3,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.20,$row_h,'본인부담금①',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_dt['svc0_bonin']),1,0,'R'); #본인부담금
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($amt['tot']),1,2,'R'); #총액
	$pdf->_Cell($draw_w * 0.05, $row_h * 3, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '본인부담금①', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $svc_bonin, $fontType2, 1, 0, 'R');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $tot, $fontType2, 1, 2, 'R');



	$pos[sizeof($pos)] = array('x'=>$left + $draw_w * 0.50, 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.25, 'height'=>4 * $rate, 'font_size'=>$font_l+2, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"본인부담총액\n⑩(①+⑧)");
	$pdf->SetX($left + $draw_w * 0.05);
	#$pdf->Cell($draw_w * 0.20,$row_h,'공단부담금②',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_dt['svc0_public']),1,0,'R'); #공단부담금
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($amt['my']),1,1,'R'); #본인부담총액
	$pdf->_Cell($draw_w * 0.20, $row_h, '공단부담금②', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $svc_public, $fontType2, 1, 0, 'R');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $my, $fontType2, 1, 2, 'R');



	$pdf->SetX($left + $draw_w * 0.05);
	#$pdf->Cell($draw_w * 0.20,$row_h,'급여 계③(①+②)',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_dt['svc0_suga']),1,0,'R'); #급여계
	#$pdf->Cell($draw_w * 0.25,$row_h,'이미 납부한 금액⑪',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,1,'R'); #이미 납부한 금액 //number_format($deposit)
	$pdf->_Cell($draw_w * 0.20, $row_h, '급여 계③(①+②)', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, number_format($svc_dt['svc0_suga']), $fontType2, 1, 0, 'R');
	$pdf->_Cell($draw_w * 0.25, $row_h, '이미 납부한 금액⑪', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType2, 1, 2, 'R');



	$pos[sizeof($pos)] = array('x'=>$left, 'y'=>$pdf->GetY() + ($row_h * 9 - $pdf->GetStringWidth('비  급  여')) / 2, 'type'=>'multi_text', 'width'=>$draw_w * 0.05, 'height'=>5 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"비\n급\n여");
	$pdf->SetX($left);
	#$pdf->Cell($draw_w * 0.05,$row_h * 9,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.20,$row_h,'식사재료비④',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,'미수금',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,$amt['unpaid'],1,1,'R'); #미수금
	$pdf->_Cell($draw_w * 0.05, $row_h * 9, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '식사재료비④', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType2, 1, 0, 'R');
	$pdf->_Cell($draw_w * 0.25, $row_h, '미수금', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $_GET['minapPay'], $fontType2, 1, 2, 'R');


	$tempY3 = $pdf->GetY();
	$pos[sizeof($pos)] = array('x'=>$left + $draw_w * 0.05, 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.20, 'height'=>4 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"상급침실 이용에\n따른 추가비용⑤");
	$pdf->SetX($left + $draw_w * 0.05);
	#$pdf->Cell($draw_w * 0.20,$row_h,'',1,0,'C'); //상급침실 이용에 따른 추가비용⑤
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,2,'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType1, 1, 2, 'C');

	$pdf->SetX($left + $draw_w * 0.05);
	#$pdf->Cell($draw_w * 0.20,$row_h,'이.미용비⑥',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,'',1,2,'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '이.미용비⑥', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType1, 1, 2, 'C');
	$pdf->SetX($left + $draw_w * 0.05);


	$pos[sizeof($pos)] = array('x'=>$left + $draw_w * 0.05, 'y'=>$pdf->GetY() + ($row_h * 5 - $pdf->GetStringWidth('기  ⑦')) / 2, 'type'=>'multi_text', 'width'=>$draw_w * 0.07, 'height'=>4 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"기타\n⑦");
	#$pdf->Cell($draw_w * 0.07,$draw_h * 0.1825,'',1,2,'C'); //기타
	$pdf->_Cell($draw_w * 0.07, $draw_h * 0.1825, '', $fontType1, 0, 2, 'C');

	$pdf->SetY($pdf->GetY()-$draw_h * 0.1825);
	$tmp_cnt = 0;
	$svc_pay = 0;
	
	for($i=0; $i<=7; $i++){
		if ($i == 0){
			$id = 0;
		}else{
			if ($i >= 1 && $i <= 4){
				$id = $i;
			}else{
				$id = chr(60 + $i);
			}

			if ($svc_dt['svc'.$id.'_bonin'] > 0){
				$svc_nm   = $myF->euckr($conn->kind_name($kind_list, $id));
				$svc_pay += $svc_dt['svc'.$id.'_bonin'];

				$pdf->SetX($left + $draw_w * 0.12);
				#$pdf->Cell($draw_w * 0.13,$row_h,$svc_nm,1,0,'L');
				#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_dt['svc'.$id.'_bonin']),1,2,'R');
				$pdf->_Cell($draw_w * 0.13, $row_h, $svc_nm, $fontType1, 1, 0, 'L');
				$pdf->_Cell($draw_w * 0.25, $row_h, number_format($svc_dt['svc'.$id.'_bonin']), $fontType2, 1, 2, 'R');

				$tmp_cnt ++;
			}
		}

		if ($svc_dt['svc'.$id.'_over'] > 0){
			$svc_nm   = $myF->euckr($conn->kind_name($kind_list, $id));
			$svc_pay += $svc_dt['svc'.$id.'_over'];

			$pdf->SetX($left + $draw_w * 0.12);
			#$pdf->Cell($draw_w * 0.13,$row_h,$svc_nm,1,0,'L');
			#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_dt['svc'.$id.'_bonin']),1,2,'R');
			$pdf->_Cell($draw_w * 0.13, $row_h, $svc_nm, $fontType1, 1, 0, 'L');
			$pdf->_Cell($draw_w * 0.25, $row_h, number_format($svc_dt['svc'.$id.'_over']), $fontType2, 1, 2, 'R');

			$tmp_cnt ++;
		}
	}

	if ($tmp_cnt < 5){
		for($i=$tmp_cnt+1; $i<=5; $i++){
			$pdf->SetX($left + $draw_w * 0.12);
			#$pdf->Cell($draw_w * 0.13,$row_h,'',1,0,'L');
			#$pdf->Cell($draw_w * 0.25,$row_h,'',1,2,'R');
			$pdf->_Cell($draw_w * 0.13, $row_h, '', $fontType1, 1, 0, 'L');
			$pdf->_Cell($draw_w * 0.25, $row_h, '', $fontType1, 1, 2, 'R');
		}
	}

	$pdf->SetX($left + $draw_w * 0.05);
	$pos[sizeof($pos)] = array('x'=>$pdf->GetX() - $draw_w * 0.01, 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.22, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"비급여 계\n⑧(④+⑤+⑥+⑦)");
	#$pdf->Cell($draw_w * 0.20,$row_h,'',1,0,'L');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($svc_pay),1,2,'R'); //비급여계
	$pdf->_Cell($draw_w * 0.20, $row_h, '', $fontType1, 1, 0, 'L');
	$pdf->_Cell($draw_w * 0.25, $row_h, number_format($svc_pay), $fontType2, 1, 2, 'R');



	$tempY2 = $pdf->GetY();
	$pdf->SetLineWidth(0.6);
	$pdf->Line($left + $draw_w / 2, $tempY1, $left + $draw_w / 2, $tempY2);
	$pdf->SetLineWidth(0.2);



	$pdf->SetXY($left + $draw_w * 0.5, $tempY3);
	$pos[sizeof($pos)] = array('x'=>$pdf->GetX() - $draw_w * 0.01, 'y'=>$pdf->GetY() + ($row_h * 4 - $pdf->GetStringWidth('수 ⑫ ⑩')) / 2, 'type'=>'multi_text', 'width'=>$draw_w * 0.13, 'height'=>4 * $rate, 'font_size'=>$font_l+1, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"수납금액\n⑫\n(⑩-⑪)");
	#$pdf->Cell($draw_w * 0.11,$row_h * 4,'',1,0,'C');
	#$pdf->Cell($draw_w * 0.14,$row_h,'카드',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($in_pay[1]['pay']),1,2,'R');
	$pdf->_Cell($draw_w * 0.11, $row_h * 4, '', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.14, $row_h, '카드', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $in_pay1, $fontType2, 1, 2, 'R');



	$pdf->SetX($left + $draw_w * 0.61);
	#$pdf->Cell($draw_w * 0.14,$row_h,'현금영수증',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($in_pay[2]['pay']),1,2,'R');
	$pdf->_Cell($draw_w * 0.14, $row_h, '현금영수증', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $in_pay2, $fontType2, 1, 2, 'R');



	$pdf->SetX($left + $draw_w * 0.61);
	#$pdf->Cell($draw_w * 0.14,$row_h,'현금',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($in_pay[3]['pay']),1,2,'R');
	$pdf->_Cell($draw_w * 0.14, $row_h, '현금', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $in_pay3, $fontType2, 1, 2, 'R');



	$pdf->SetX($left + $draw_w * 0.61);
	#$pdf->Cell($draw_w * 0.14,$row_h,'합계',1,0,'C');
	#$pdf->Cell($draw_w * 0.25,$row_h,number_format($in_pay[1]['pay'] + $in_pay[2]['pay'] + $in_pay[3]['pay']),1,2,'R');
	$pdf->_Cell($draw_w * 0.14, $row_h, '합계', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.25, $row_h, $in_pay_tot, $fontType2, 1, 2, 'R');



	$pdf->SetX($left + $draw_w * 0.50);
	#$pdf->Cell($draw_w * 0.50,$row_h,'현금영수증',1,2,'C');
	$pdf->_Cell($draw_w * 0.50, $row_h, '현금영수증', $fontType1, 1, 2, 'C');



	$pdf->SetX($left + $draw_w * 0.50);
	#$pdf->Cell($draw_w * 0.20,$row_h,'신분확인번호',1,0,'C');
	#$pdf->Cell($draw_w * 0.30,$row_h,'',1,2,'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '신분확인번호', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.30, $row_h, '', $fontType1, 1, 2, 'C');



	$pdf->SetX($left + $draw_w * 0.50);
	#$pdf->Cell($draw_w * 0.20,$row_h,'현금승인번호',1,0,'C');
	#$pdf->Cell($draw_w * 0.30,$row_h,'',1,2,'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, '현금승인번호', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.30, $row_h, '', $fontType1, 1, 2, 'C');



	$pdf->SetX($left + $draw_w * 0.50);
	#$pdf->Cell($draw_w * 0.50,$row_h,'※비고',1,1,'L');
	$pdf->_Cell($draw_w * 0.50, $row_h, '※비고', $fontType1, 1, 1, 'L');



	$tempY1 = $pdf->GetY();

	$pdf->SetLineWidth(0.6);
	$pdf->Line($left, $tempY1, $left + $draw_w, $tempY1);
	$pdf->SetLineWidth(0.2);


	# 테두리
	$pdf->SetLineWidth(0.6);
	$pdf->Line($left, $pdf->GetY(), $left + $draw_w, $pdf->GetY());
	$pdf->SetLineWidth(0.2);
?>
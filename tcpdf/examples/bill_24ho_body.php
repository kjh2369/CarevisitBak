<?
	switch($svcKind){
		case '200':
			$lsSvcNm  = '(방문요양)';
			$lsBillNo = '-1';
			break;

		case '500':
			$lsSvcNm  = '(방문목욕)';
			$lsBillNo = '-2';
			break;

		case '800':
			$lsSvcNm  = '(방문간호)';
			$lsBillNo = '-3';
			break;

		default:
			$lsSvcNm  = '';
			$lsBillNo = '';
	}
	
	/**************************************************
		타이틀
	**************************************************/
	$pdf->SetX($left);
	$pdf->SetFont($pdf->font_name_kor,'',$font_l-1);
	$pdf->Cell($draw_w, $draw_h * 0.02, '노인장기요양보험법 시행규칙 [별지 제24호서식] <개정 2013.6.10>', 0, 1, 'L');

	$pdf->SetX($left);
	$pdf->SetFont($pdf->font_name_kor,'B',$font_l+3);
	$pdf->Cell($draw_w * 0.8, $draw_h * 0.05, '장기요양급여비용 명세서'.$lsSvcNm, 1, 0, 'C');

	$pdf->SetFont($pdf->font_name_kor,'',$font_l);
	$pdf->Cell($draw_w * 0.2, $draw_h * 0.025, '□퇴소', 1, 2, 'C');

	if ($side_show){
		$pdf->SetX($left);
		$pdf->Cell($draw_w * 0.80, $draw_h * 0.025, ($draw_pos == 1 ? '공급자/시설 용' : '공급받는자/수급자 용'), 0, 0, 'R');
		$pdf->Cell($draw_w * 0.20, $draw_h * 0.025, '□중간', 1, 1, 'C');
	}else{
		$pdf->Cell($draw_w * 0.2, $draw_h * 0.025, '□중간', 1, 1, 'C');
	}

	/**************************************************
		폰트 설정
	**************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',$font_l);

	include_once('./show_pdf.php');

	/**************************************************
		기관정보
	**************************************************/
	$pdf->SetX($left);
	#$pdf->Cell($draw_w * 0.10, $row_h, '기관기호', 1, 0, 'C');
	#$pdf->Cell($draw_w * 0.20, $row_h, $ct_if['cd'], 1, 0, 'L');
	#$pdf->Cell($draw_w * 0.15, $row_h, '장기요양기관명', 1, 0, 'C');
	#$pdf->Cell($draw_w * 0.55, $row_h, $ct_if['nm'], 1, 1, 'L');

	$fontType1 = array('name'=>$pdf->font_name_kor,'bold'=>'','size'=>$font_l);
	$fontType2 = array('name'=>$pdf->font_name_kor,'bold'=>'B','size'=>$font_l+1);

	$pdf->_Cell($draw_w * 0.10, $row_h, '기관기호', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.20, $row_h, $ct_if['cd'], $fontType2, 1, 0, 'L');
	$pdf->_Cell($draw_w * 0.15, $row_h, '장기요양기관명', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.55, $row_h, $myF->utf($ct_if['nm']), $fontType2, 1, 1, 'L');

	
	if(strlen($ct_if['postno']) == '6'){
		$ct_if['postno'] = substr($ct_if['postno'], 0, 3).'-'.substr($ct_if['postno'], 3, 6);
	}


	$pos[sizeof($pos)] = array('x'=>$left + $draw_w * 0.10, 'y'=>$pdf->GetY() + (($draw_h * 0.052 - $pdf->GetStringWidth('▒▒▒')) / 2), 'type'=>'multi_text', 'width'=>$draw_w * 0.55, 'height'=>5 * $rate, 'font_bold'=>'B', 'font_size'=>$font_l+1, 'align'=>'L', 'border'=>0, 'text_color'=>$text_color, 'text'=>$ct_if['postno']."\n".$myF->utf($ct_if['addr']));
	$pdf->SetX($left);
	#$pdf->Cell($draw_w * 0.10, $draw_h * 0.052, '주소', 1, 0, 'C');
	#$pdf->Cell($draw_w * 0.55, $draw_h * 0.052, '', 1, 0, 'L');
	#$pdf->Cell($draw_w * 0.15, $draw_h * 0.052, '사업자등록번호', 1, 0, 'C');
	#$pdf->Cell($draw_w * 0.20, $draw_h * 0.052, $myF->bizStyle($ct_if['no']), 1, 1, 'C'); #사업자등록번호

	$pdf->_Cell($draw_w * 0.10, $draw_h * 0.052, '주소', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.55, $draw_h * 0.052, '', $fontType2, 1, 0, 'L');
	$pdf->_Cell($draw_w * 0.15, $draw_h * 0.052, '사업자등록번호', $fontType1, 1, 0, 'C');
	$pdf->_Cell($draw_w * 0.20, $draw_h * 0.052, $myF->bizStyle($ct_if['no']), $fontType2, 1, 1, 'C');


	$pdf->SetFont($pdf->font_name_kor,'',$font_l);



	/**************************************************
		테두리
	**************************************************/
	$pdf->SetLineWidth(0.6);
	$pdf->Line($left, $pdf->GetY(), $draw_w + $left, $pdf->GetY());
	$pdf->SetLineWidth(0.2);



	/**************************************************
		고객정보
	**************************************************/
	$pdf->SetX($left);
	$pdf->SetFont($pdf->font_name_kor,'',$font_l+1);
	$pdf->Cell($draw_w * 0.15, $row_h, '성명', 1, 0, 'C');
	$pdf->Cell($draw_w * 0.25, $row_h, '장기요양인정번호', 1, 0, 'C');
	$pdf->Cell($draw_w * 0.35, $row_h, '급여제공기간', 1, 0, 'C');
	$pdf->Cell($draw_w * 0.25, $row_h, '영수증 번호', 1, 1, 'C');

	$pdf->SetX($left);
	$pdf->SetFont($pdf->font_name_kor,'B',$font_l+1);
	$pdf->Cell($draw_w * 0.15, $row_h, $myF->utf($svc_dt['c_nm']), 1, 0, 'C'); #수급자성명
	$pdf->Cell($draw_w * 0.25, $row_h, $svc_dt['c_no'], 1, 0, 'C'); #수급자 장기요양인정번호
	$pdf->Cell($draw_w * 0.35, $row_h, $myF->dateStyle($svc_dt['min_dt'],'.').'~'.$myF->dateStyle($svc_dt['max_dt'],'.'), 1, 0, 'C'); #수급자 급여제공기간
	$pdf->Cell($draw_w * 0.25, $row_h, $svc_dt['bill_no'].$lsBillNo, 1, 1, 'C'); #영수증번호
	$pdf->SetFont($pdf->font_name_kor,'',$font_l+1);




	/**************************************************
		테두리
	**************************************************/
	$pdf->SetLineWidth(0.6);
	$pdf->Line($left, $pdf->GetY(), $draw_w + $left, $pdf->GetY());
	$pdf->SetLineWidth(0.2);



	/**************************************************
		항목
	**************************************************/
	//parse_str($_POST['para'], $val);

	include_once('./bill_pdf_detail.php');
	
	$tmp_dt = '';

	if (is_array($in_pay)){
		foreach($in_pay as $i => $pay){
			if ($tmp_dt < $pay['dt']){
				$tmp_dt = $pay['dt'];
			}
		}
	}

	# 신용카드를 사용하실 떄 시작
	$pdf->SetX($left);
	$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_h * 0.60 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.13, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"신용카드를\n사용하실때");
	$pdf->Cell($draw_w * 0.13,$row_h * 2,'',1,0,'C');

	$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_h * 0.15 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.07, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"회원\n번호");
	$pdf->Cell($draw_w * 0.07,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');

	$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_h * 0.15 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.07, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"승인\n번호");
	$pdf->Cell($draw_w * 0.07,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.07,$row_h,'할부',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.18,$row_h,'사용금액',1,2,'C');

	$pdf->SetX($left + $draw_w * 0.13);
	$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.07, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"카드\n종류");
	$pdf->Cell($draw_w * 0.07,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');

	$pos[sizeof($pos)] = array('x'=>$pdf->GetX(), 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.07, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color,'text'=>"유호\n기간");
	$pdf->Cell($draw_w * 0.07,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');

	$pos[sizeof($pos)] = array('x'=>$pdf->GetX() - $draw_w * 0.015, 'y'=>$pdf->GetY() + $row_h * 0.10 * $rate, 'type'=>'multi_text', 'width'=>$draw_w * 0.10, 'height'=>4 * $rate, 'font_size'=>$font_l, 'align'=>'C', 'text_color'=>$text_color, 'text'=>"가맹점\n번호");
	$pdf->Cell($draw_w * 0.07,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.16,$row_h,'',1,0,'C');
	$pdf->Cell($draw_w * 0.18,$row_h,'',1,2,'C');



	# 테두리
	$pdf->SetLineWidth(0.6);
	$pdf->Line($left, $pdf->GetY(), $left + $draw_w, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	# 서명
	$pdf->SetXY($left, $pdf->GetY());
	$pdf->Line($left, $pdf->GetY(), $left + $draw_w, $pdf->GetY());


	/*********************************************************
		직인
	*********************************************************/
	$sql = 'select m00_jikin
			  from m00center
			 where m00_mcode = \''.$var['code'].'\'
			 order by m00_mkind
			 limit 1';
	$iconJikin = $conn->get_data($sql);

	//기관 로고
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$var['code'].'\'';
	$icon = $conn -> get_data($sql);
	
	$H = 5;
	$W = 5;

	if (is_file('../../mem_picture/'.$iconJikin)){
		$exp = explode('.',$iconJikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if ($exp != 'bmp'){
			$tmpImg = getImageSize('../../mem_picture/'.$iconJikin);
			//$pdf->Image('../mem_picture/'.$iconJikin, $left + $draw_w + ($side_show ? 3 : -9) - $pdf->left, $pdf->GetY() + ($side_show ? 5 : 7), ($side_show ? 10 : 25));
			$pdf->Image('../../mem_picture/'.$iconJikin, $left + $draw_w + $W + ($side_show ? -1 : -9) - $pdf->left, $pdf->GetY()-$H + ($side_show ? 5 : 7), ($side_show ? 16.5 : 25), null, $exp, '', '', false, 300, '', false, false, 0, $fitbox, false, false);
			//$pdf->Image('images/image_demo.jpg', $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);

		}
	}

	
	$pdf->SetX($left);

	if ($code == '31141000043' /*예사랑노인복지센터*/){
		//입금일자 출력
		$pdf->Cell($draw_w, $row_h * 0.7, $myF->dateStyle($tmp_dt,'.').'     ', 0, 2, 'R');
	}else if($code == 'GG310C01'){
		//입금일자 출력
		if($tmp_dt != ''){
			$pdf->Cell($draw_w, $row_h * 0.7, $myF->dateStyle($tmp_dt,'.').'     ', 0, 2, 'R');
		}else {
			$pdf->Cell($draw_w, $row_h * 0.7, $myF->dateStyle($printDT, '.').'     ', 0, 1, 'R');
		}
	}else{
		//if($in_pay[3]['dt'] != ''){
			//입금일자 출력
		//	$pdf->Cell($draw_w, $row_h * 0.7, $myF->dateStyle($in_pay[3]['dt'], '.').'     ', 0, 1, 'R');
		//}else {
			//출력일자 출력
			$pdf->Cell($draw_w, $row_h * 0.7, $myF->dateStyle($printDT, '.').'     ', 0, 1, 'R');
		//}
	}


	//기관명 대표자명
	$pdf->SetX($left);
	$pdf->Cell($draw_w * 0.5, $row_h * 0.7, '     장기요양기관명 : ', 'B', 0, 'L');
	$pdf->Cell($draw_w * 0.5, $row_h * 0.7, '대표자명 :                            (인)', 'B', 0, 'R');



	$pdf->SetFont($pdf->font_name_kor,'b',$font_l+1);
	$pdf->SetX($left + $pdf->GetStringWidth('     장기요양기관명 : '));
	$pdf->Cell($draw_w * 0.5, $row_h * 0.7, $myF->utf($ct_if['nm']), 'B', 0, 'L');
	$pdf->SetX($left + $draw_w - $pdf->GetStringWidth('대표자명 :                       (인)') + $pdf->GetStringWidth($myF->utf($ct_if['mm'])));
	$pdf->Cell($draw_w * 0.1, $row_h * 0.7, $myF->utf($ct_if['mm']), 'B', 1, 'L');


	# 설명
	$pdf->SetXY($left + 1, $pdf->GetY() + $row_h * 0.15);
	$pdf->SetFont($pdf->font_name_kor,'',$font_l - ($side_show ? 1 : 0));
	$pdf->MultiCell($draw_w, 4.5 * $rate, "* 이 명세서(영수증)는 「소득세법」에 따른 의료비 또는 「조세특례제한법」에 따른 현금영수증 승인번호가 기재된 경우 공제신청에\n   사용할 수 있습니다. 다만,  지출증빙용으로 발급된 현금영수증(지출증빙)은 공제신청에 사용할 수 없습니다.\n* 이 명세서(영수증)에 대한 세부내역을 요구할 수 있습니다.\n* 비고란은 장기요양기관의 임의활용란으로 사용합니다. 다만, 복지용구의 경우 품목과 구입. 대여를 구분하여 적으시기 바랍니다.\n");
	
	$T = 5;
	$T2 = 0;

	# 푸터
	//if (!$side_show){
		if($code == '34728000127' || $code == '34311000399'){ //스마일재가
			$pdf->SetFont($pdf->font_name_kor,'b',$font_l+2);
			$pdf->Text($left, $height + $top-$T, '본인부담금 납입계좌: '.$myF->utf($ct_if['bank_nm'].' '.$ct_if['bank_no']));
			$pdf->Text($left, $height + $top-$T2, '예금주 : '.$myF->utf($ct_if['bank_owner']).' 전화 : '.$myF->phoneStyle($ct_if['tel'],'.'));
		}else {
			$pdf->SetFont($pdf->font_name_kor,'b',$font_l+2);
			if($ct_if['bank_nm']) $pdf->Text($left, $height + $top-$T, '본인부담금 납입계좌: '.$myF->utf($ct_if['bank_nm'].' '.$ct_if['bank_no']).' 예금주 : '.$myF->utf($ct_if['bank_owner']));
			if($ct_if['tel']) $pdf->Text($left, $height + $top-$T2, '전화 : '.$myF->phoneStyle($ct_if['tel'],'.'));
		}
	//}
	/*
	if (!$side_show){
		$pdf->SetFont($pdf->font_name_kor,'',$font_l);
		$pdf->Text($left+125, $height + $top * 0.5, '210mm×297mm[백상지 80g/㎡]');
	}
	*/

	if($gDomain == 'dolvoin.net'){
		if($icon != ''){
			$exp = explode('.',$icon);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				if($side_show){
					$pdf->Image('../mem_picture/'.$icon, 270, 195, 20, null);	//기관 로고
				}else {
					$pdf->Image('../mem_picture/'.$icon, 180, 280, 20, null);	//기관 로고
				}
			}
		}
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
	//unset($in_pay);


	function set_array_text($pdf, $pos){
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
		if (is_array($pos)){
			foreach($pos as $i => $p){
				$tmp_x = $pdf->GetX();
				$tmp_y = $pdf->GetY();

				if ($p['type'] == 'multi_text' ||
					$p['type'] == 'text'){
					if (!empty($p['font_size']))
						$pdf->SetFont($pdf->font_name_kor, $p['font_bold'].$p['font_style'], $p['font_size']);
					else
						$pdf->SetFont($pdf->font_name_kor, '', 10);

					$pdf->SetTextColor($p['text_color']['r'], $p['text_color']['g'], $p['text_color']['b']);
				}

				if ($p['type'] == 'multi_text'){
					$pdf->SetXY($p['x'], $p['y']);
					$pdf->MultiCell($p['width'], $p['height'], $p['text'], $p['border'], $p['align']);
				}else if ($p['type'] == 'text'){
					$pdf->Text($p['x'], $p['y'], $p['text']);
				}
			}
		}
	}

	function get_pos_y($pdf, $row_h, $text){
		$h = $pdf->GetY() + ( $row_h - $pdf->GetStringWidth($text) ) / 2;

		return  $h;
	}

	
?>
<?
	
	$jumin = $ed->de($_POST['jumin']);
	
	/**************************************************

		기관정보

	**************************************************/
	
	
	$sql = 'select m00_store_nm as c_nm
			  from m00center
			 where m00_mcode  = \''.$code.'\'
			   and m00_del_yn = \'N\'
			 order by m00_mkind
			 limit 1';

	$center = $conn->get_array($sql);

	/**************************************************

		직원정보

	**************************************************/
	
	$sql = 'select m02_yname as m_nm
			,      m02_ytel as m_mobile
			,	   m02_ytel2 as m_tel
			,	   m02_ypostno as m_postno
			,	   m02_yjuso1 as m_addr
			,	   m02_yjuso2 as m_add_dtl
			  from m02yoyangsa
			 where m02_ccode  = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'
			   and m02_del_yn = \'N\'
			 order by m02_mkind
			 limit 1';
	
	$tmp = $conn->get_array($sql);
	
	$pdf->type       = $_POST['type'];
	$pdf->m_nm       = $tmp['m_nm'];
	$pdf->m_jumin = $myF->issNo($jumin,'.');
	$pdf->m_mobile   = $myF->phoneStyle($tmp['m_mobile'],'.');
	$pdf->m_tel   = $myF->phoneStyle($tmp['m_tel'],'.');
	$pdf->m_addr  = '('.substr($tmp['m_postno'],0,3).'-'.substr($tmp['m_postno'],3,3).') '.$tmp['m_addr'].' '.$tmp['m_addr_dtl'];
	
	
	/**************************************************

		인사기록카드 PDF 출력

	**************************************************/
	$pdf->SetAutoPageBreak(false);
	$pdf->MY_ADDPAGE();
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 1, $pdf->height * 0.8 , '', 1, 1, 'L');

	
	$pdf->SetFont($pdf->font_name_kor, 'B', 14);
	$pdf->SetXY($pdf->left*2, $pdf->top+67);
	$pdf->MultiCell($pdf->width*1, 9, "  본인은 가족인 요양보호사로서 본인이 본인과 가족인 수급자에게\n제공한 방문요양에 대한 급여비용중 가족인 수급자가 부담하여야할\n본인부담금을 매월 본인의 급여에서 공제하는 것에 동의합니다.", 0 ,'L');
	
	$pdf->SetXY($pdf->left, $pdf->top+125);
	$pdf->MultiCell($pdf->width*1, 5, '            년      월      일', 0, "C");
	
	$pdf->SetXY($pdf->left, $pdf->top+150);
	$pdf->MultiCell($pdf->width*1, 7, "동의인:                       서명(인)", 0, 'C');

	$pdf->SetXY($pdf->left, $pdf->top+190);
	$pdf->MultiCell($pdf->width*1, 7, $center['c_nm']."  귀중    ", 0, 'R');
	

	//$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);

	unset($center);
	unset($tmp);
?>
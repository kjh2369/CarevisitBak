<?
	
	$jumin = $ed->de($_POST['jumin']);
	
	/**************************************************

		�������

	**************************************************/
	
	
	$sql = 'select m00_store_nm as c_nm
			  from m00center
			 where m00_mcode  = \''.$code.'\'
			   and m00_del_yn = \'N\'
			 order by m00_mkind
			 limit 1';

	$center = $conn->get_array($sql);

	/**************************************************

		��������

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

		�λ���ī�� PDF ���

	**************************************************/
	$pdf->SetAutoPageBreak(false);
	$pdf->MY_ADDPAGE();
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 1, $pdf->height * 0.8 , '', 1, 1, 'L');

	
	$pdf->SetFont($pdf->font_name_kor, 'B', 14);
	$pdf->SetXY($pdf->left*2, $pdf->top+67);
	$pdf->MultiCell($pdf->width*1, 9, "  ������ ������ ��纸ȣ��μ� ������ ���ΰ� ������ �����ڿ���\n������ �湮��翡 ���� �޿������ ������ �����ڰ� �δ��Ͽ�����\n���κδ���� �ſ� ������ �޿����� �����ϴ� �Ϳ� �����մϴ�.", 0 ,'L');
	
	$pdf->SetXY($pdf->left, $pdf->top+125);
	$pdf->MultiCell($pdf->width*1, 5, '            ��      ��      ��', 0, "C");
	
	$pdf->SetXY($pdf->left, $pdf->top+150);
	$pdf->MultiCell($pdf->width*1, 7, "������:                       ����(��)", 0, 'C');

	$pdf->SetXY($pdf->left, $pdf->top+190);
	$pdf->MultiCell($pdf->width*1, 7, $center['c_nm']."  ����    ", 0, 'R');
	

	//$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);

	unset($center);
	unset($tmp);
?>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���� ���� �ȳ���
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];


	//������� �� �ʱ���� ����
	$sql = 'SELECT	mst.m03_name AS name
			,		rcpt.addr
			,		rcpt.addr_dtl
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];	//����ڸ�
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//�ּ�

	Unset($row);

	//����
	$sql = 'SELECT	end_dt
			,		end_rsn
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		end_flag= \'Y\'';

	$row = $conn->get_array($sql);

	$endDt	= $myF->euckr($myF->dateStyle($row['end_dt'],'KOR'));
	$endRsn	= $row['end_rsn'];

	Unset($row);


	$pdf->SetXY($pdf->left, $pdf->top + 20);
	$pdf->SetFont($pdf->font_name_kor,'B',18);
	$pdf->Cell($pdf->width,$pdf->row_height*2,"���� ���� �ȳ���",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'B',11);


	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 20);
	$pdf->Cell($pdf->width, $pdf->row_height, "�� ��  �� : ".$name, 0, 1);

	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width, $pdf->row_height, "�� ��  �� : ".$addr, 0, 1);

	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 20);
	if($hce->SR == 'R'){
		$pdf->Cell($pdf->width, $pdf->row_height, "  �Ʒ��� ���� ������ ���������̴� ������ȸ�ڿ������� ����Ǿ�����", 0, 1);
	}else {
		$pdf->Cell($pdf->width, $pdf->row_height, "  �Ʒ��� ���� ������ ���������̴� �簡�����������񽺰� ����Ǿ�����", 0, 1);
	}
	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $pdf->row_height, "�ȳ��帳�ϴ�.", 0, 1);

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 20);
	$pdf->Cell($pdf->width, $pdf->row_height, "- ��     �� -", 0, 1, "C");

	/*
	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 20);
	$pdf->Cell($pdf->width, $pdf->row_height, "1. �� �� �� �� : ".$endRsn, 0, 1, "L");

	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width, $pdf->row_height, "2. �� �� �� �� : ".$endDt, 0, 1, "L");
	*/

	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width, $pdf->row_height, "1. �� �� �� �� : ".$endDt, 0, 1, "L");
	$pdf->SetXY($pdf->left + 15, $pdf->GetY() + 10);
	$pdf->Cell($pdf->width * 0.16, $pdf->row_height, "2. �� �� �� �� : ", 0, 1, "L");

	$pdf->SetXY($pdf->left + 15 + $pdf->width * 0.16, $pdf->GetY() - $pdf->row_height);
	$pdf->MultiCell($pdf->width * 0.7, 5, $endRsn);

	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetXY($pdf->left, $pdf->height - 20);
	$pdf->Cell($pdf->width, $pdf->row_height, $pdf->ctName, 0, 1, "C");

	$X1 = $pdf->left;
	$Y1 = $pdf->top;
	$X2 = $pdf->width;
	$Y2 = $pdf->height - $Y1;

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($X1, $Y1, $X2, $Y2);
?>
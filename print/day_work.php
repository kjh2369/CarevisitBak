<?
	/*
	require('../pdf/fpdf.php');

	$pdf=new FPDF('L');
	$pdf->AddPage();
	$pdf->SetFont('GulimChe','B',16);
	$pdf->Cell(0,0,'������',1,2,'C');
	$pdf->Cell(10,10,'Title2',1,0,'C');
	$pdf->Output();
	*/
	
	/*
	$pdf->Image('bill_001.jpg', 0, 0, 297, 210, 'jpg');
	$pdf->Cell(130,31,'�����޿����(���κδ��) ������',1,0,'C');
	$pdf->Cell(130,31,'�����޿����(���κδ��) ������',1,0,'C');
	//$pdf->Text(130,31,'�����޿����(���κδ��) ������');
	$pdf->SetFont('����','',9);
	//$pdf->Cell(130,50,'No.',1,0,'L');
	*/
	require('../pdf/korean.php');

	$pdf=new PDF_Korean('L');
	$pdf->AddUHCFont('����','Gulim');
	$pdf->Open();
	$pdf->SetFont('����','b',12);
	$pdf->AddPage();
	$pdf->SetXY(14, 21);
	
	$pdf->Output();
?>
<script>self.focus();</script>
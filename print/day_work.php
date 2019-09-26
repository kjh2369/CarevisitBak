<?
	/*
	require('../pdf/fpdf.php');

	$pdf=new FPDF('L');
	$pdf->AddPage();
	$pdf->SetFont('GulimChe','B',16);
	$pdf->Cell(0,0,'영수증',1,2,'C');
	$pdf->Cell(10,10,'Title2',1,0,'C');
	$pdf->Output();
	*/
	
	/*
	$pdf->Image('bill_001.jpg', 0, 0, 297, 210, 'jpg');
	$pdf->Cell(130,31,'장기요양급여비용(본인부담금) 영수증',1,0,'C');
	$pdf->Cell(130,31,'장기요양급여비용(본인부담금) 영수증',1,0,'C');
	//$pdf->Text(130,31,'장기요양급여비용(본인부담금) 영수증');
	$pdf->SetFont('굴림','',9);
	//$pdf->Cell(130,50,'No.',1,0,'L');
	*/
	require('../pdf/korean.php');

	$pdf=new PDF_Korean('L');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	$pdf->SetFont('굴림','b',12);
	$pdf->AddPage();
	$pdf->SetXY(14, 21);
	
	$pdf->Output();
?>
<script>self.focus();</script>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지
	 *********************************************************/

	include_once('../hce/hce_ispt_1_pdf.php');
	
	if($nextPg != 1){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$pdf->SetXY($pdf->left, $pdf->top);
	}
	
	include_once('../hce/hce_ispt_2_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_3_4_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_5_6_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_7_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_8_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_9_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_10_pdf.php');

	$pdf->MY_ADDPAGE();
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->top);

	include_once('../hce/hce_ispt_11_pdf.php');
?>
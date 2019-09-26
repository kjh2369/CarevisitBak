<?php
// Include the main TCPDF library (search for installation path).
include_once('../../inc/_db_open.php');
include_once('../../inc/_myFun.php');
require_once('tcpdf_include.php');


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//$pdf->setHeaderFont(array('nanumbarungothicyethangul','','10'));
$pdf->SetHeaderData('', 0, '', '', array(255,255,255), array(255,255,255));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

$pdf->left	= 14;
$pdf->top	= 11;
$pdf->width	= 182;
$pdf->height= 270;

$top    = $pdf->top;
$left   = $pdf->left;
$width  = $pdf->width;
$height = $pdf->height;
$side_w = $width;
$draw_w = $width;
$draw_h = $height;

$font_l = 9.5;
$font_s = 8;

$rate  = $font_l / 10;
$row_h = $draw_h * 0.038;


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

$pdf->SetXY($left, $top);
$pdf->SetLineWidth(0.6);
$pdf->Rect($left, $top+5, $side_w, $height - $top);
$pdf->SetLineWidth(0.2);

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->font_name_kor = 'nanumbarungothicyethangul';

include_once('./bill_24ho_body.php');


// Set some content to print
/*
$html = <<<EOD
<table border="1" cellpadding="3px">
	<colgroup>
	<col width="50px">
	<col width="150px">
	<col width="150px">
	<col width="150px">
	<col width="150px">
	<col width="150px">
	</colgroup>
	<thead>
	<tr>
		<th colspan="5" rowspan="2" style="text-align:center;"><span style="margin-top:10px;">장기요양급여비용 명세서</span></th>
		<td style="text-align:center;">□퇴소</td>
	</tr>
	<tr>
		<td style="text-align:center;">□중간</td>
	</tr>
	</thead>
</table>
EOD;
*/
// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('급여비용명세서('.$var['year'].'년'.$var['month'].'월)'.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

include_once('../../inc/_db_close.php');


?>
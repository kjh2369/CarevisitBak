<?
	/**************************************************
		새 페이지 추가
	**************************************************/
	$pdf->MY_ADDPAGE();
	$pdf->SetAutoPageBreak(false);
	$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_szie);

	if ($var['root'] == 'showPDF'){
		$strPath = './';
	}else{
		$strPath = '../'.$var['root'].'/'.$var['root'].'_'.$var['mode'].'_'.$var['fileType'].'.php';
	}

	include_once($strPath);
?>
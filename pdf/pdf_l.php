<?php
require_once('../pdf/pdf_function.php');

class MY_PDF extends PDF_FUNCTION{
	var $direction	= 'L';
	var $paper_size	= 'A4';
	var $font_size  = 11;
	var $left		= 14;
	var $top		= 21;
	var $width		= 270;
	var $height		= 180;
	var $row_height	= 6;

	//function MY_PDF(){
	//	$this->PDF_FUNCTION($this->direction);
	//}

	function MY_ADDPAGE(){
		$this->AddPage($this->direction, $this->paper_size);
		$this->SetFont($this->font_name_kor, '', $this->font_size);
	}
}
?>
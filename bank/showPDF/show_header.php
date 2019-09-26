<?php
#include_once('../../inc/_http_uri.php');
require_once('../../pdf/pdf_'.$paperDir.'.php');

class MYPDF extends MY_PDF{
	function Header(){

	}

	function Footer(){
		//$this->SetXY($this->left, -20);
		//$this->Cell($this->width, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'C');
	}
}
?>
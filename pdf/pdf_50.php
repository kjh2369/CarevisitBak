<?php
require('pdf_l.php');

class MYPDF extends MY_PDF{
	var $center_name;

	function Header(){
	}

	function Footer(){
		$this->SetFont('╧ыеа','B',15);
		$this->SetXY($this->left,-$this->top);
		$this->Cell($this->widht, 0, $this->center_name, 0, 1, 'C');
	}
}
?>
<?php
require('pdf_p.php');

class MYPDF extends MY_PDF{
	function Header(){
	}

	function Footer(){
		$this->SetLineWidth(0.6);
		$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top);
		$this->SetLineWidth(0.2);
	}
}
?>

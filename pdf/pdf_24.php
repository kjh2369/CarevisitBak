<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	//var $left		= 14;
	//var $top		= 21;
	//var $width		= 182;
	//var $height		= 270;

	var $firstCount	= 14;
	var $listCount	= 31;

	function Header(){
		$this->SetXY($this->left, $this->top);
	}

	function Footer(){
		$this->SetLineWidth(0.6);

		if ($this->PageNo() == 1){
			$this->Rect($this->left, $this->top+10, $this->width, $this->height-$this->top-10);
		}else if ($this->PageNo() == 2){
			$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top);
		}else{
			$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top-1);
		}

		$this->SetLineWidth(0.2);
	}
}
?>
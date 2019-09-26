<?php
require('pdf_p.php');

class MYPDF extends MY_PDF{
	var $title;
	var $maxPage	= 1;
	var $attend		= 0;
	var $firstCount	= 15;
	var $listCount	= 29;
	var $listIndex	= 0;
	var $thisAdd	= false;
	var $pic1;
	var $pic2;

	function Header(){
		$this->SetXY($this->left, $this->top);

		if ($this->PageNo() == 1){
			$this->SetFont('¹ÙÅÁ','B',20);
			$this->Cell($this->width*0.59, 18, '('.$this->title.')±³À° °á°úº¸°í', 1, 0, 'C');
			$this->Cell($this->width*0.05, 18, "", 1, 0, 'C');
			$this->Cell($this->width*0.12, 6, "", 1, 0, 'C');
			$this->Cell($this->width*0.12, 6, "", 1, 0, 'C');
			$this->Cell($this->width*0.12, 6, "", 1, 1, 'C');
			$Y = $this->GetY();
			$this->SetXY($this->left+$this->width*0.59, $Y-2);
			$this->SetFont('¹ÙÅÁ','B',11);
			$this->MultiCell($this->width*0.05, 5, "°á\nÀç", 0, 'C');
			$this->SetXY($this->left+$this->width*0.64, $Y);
			$this->Cell($this->width*0.12, 12, "", 1, 0, 'C');
			$this->Cell($this->width*0.12, 12, "", 1, 0, 'C');
			$this->Cell($this->width*0.12, 12, "", 1, 1, 'C');

			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->top, $this->width, 18);
			$this->SetLineWidth(0.2);

			$this->SetXY($this->left, $this->top+22);
		}else{
			$this->SetFont('¹ÙÅÁ', 'B', 15);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width, 17, '('.$this->title.')±³À° Âü¼®ÀÚ ¸í´Ü', 1, 1, 'C');
		}
	}

	function Footer(){
		$this->SetLineWidth(0.6);

		if ($this->PageNo() == 1){
			$this->Rect($this->left, $this->top+22, $this->width, $this->height-$this->top-22);
		}else if ($this->PageNo() == 2){
			$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top);
		}else{
			$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top);
		}

		$this->SetLineWidth(0.2);
	}

	function drawTemp($start, $end){
		for($i=$start; $i<$end; $i++){
			$this->listIndex ++;
			$this->SetX($this->left);
			$this->Cell($this->width*0.2, 8, '', 1, 0, 'C');
			$this->Cell($this->width*0.2, 8, '', 1, 0, 'C');
			$this->Cell($this->width*0.2, 8, '', 1, 0, 'C');
			$this->Cell($this->width*0.2, 8, '', 1, 0, 'C');
			$this->Cell($this->width*0.2, 8, '', 1, 1, 'C');
		}
	}
}
?>
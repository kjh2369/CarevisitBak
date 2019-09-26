<?php
require_once('pdf_l.php');

class MYPDF extends MY_PDF{
	var $year;
	var $month;
	var $top = 10;
	var $left = 10;
	var $width = 278;
	var $height = 202;

	function Header(){
		$this->_showTop($this->year.'�� '.$this->month.'�� �ٹ���Ȳǥ', 12, false, true);
		$this->set_font(7, 'B');

		$col = $this->default_col();

		$this->Cell($col[0],  $this->row_height, 'No', 1, 0, 'C', true);
		$this->Cell($col[1],  $this->row_height, '�̸�', 1, 0, 'C', true);
		$this->Cell($col[2],  $this->row_height, '���', 1, 0, 'C', true);
		$this->Cell($col[3],  $this->row_height, '�ּ�', 1, 0, 'C', true);
		$this->Cell($col[4],  $this->row_height, '����ó', 1, 0, 'C', true);
		$this->Cell($col[5],  $this->row_height, '����', 1, 0, 'C', true);
		$this->Cell($col[6],  $this->row_height, 'Ƚ��/��', 1, 0, 'C', true);
		$this->Cell($col[7],  $this->row_height, '�ð�/ȸ��', 1, 0, 'C', true);
		$this->Cell($col[8],  $this->row_height, '��纸ȣ��', 1, 0, 'C', true);
		$this->Cell($col[9],  $this->row_height, '�ٹ��ð�', 1, 0, 'C', true);
		$this->Cell($col[10], $this->row_height, '����ó', 1, 0, 'C', true);
		$this->Cell($col[11], $this->row_height, '���', 1, 1, 'C', true);
	}

	function Footer(){
		$this->SetY(-10);
		$this->set_font();
		$this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 1, 'R');
	}

	function default_col(){
		$w = $this->width;

		$col[0]  = $w * 0.03;
		$col[1]  = $w * 0.05;
		$col[2]  = $w * 0.04;
		$col[3]  = $w * 0.20;
		$col[4]  = $w * 0.09;
		$col[5]  = $w * 0.05;
		$col[6]  = $w * 0.05;
		$col[7]  = $w * 0.06;
		$col[8]  = $w * 0.08;
		$col[9]  = $w * 0.19;
		$col[10] = $w * 0.09;
		$col[11] = $w * 0.07;

		return $col;
	}
}
?>
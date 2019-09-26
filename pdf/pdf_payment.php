<?php
require('korean.php');

class MYPDF extends PDF_Korean{
	// 페이지설정
	var $left   = 14;
	var $top    = 21;
	var $width  = 182;
	var $height = 262;

	// 행 높이 설정
	var $rowHeight = 6;

	// 설정
	var $year;		//년
	var $month;		//월
	var	$name;		//요양보호사명
	var $no;		//요양보호사번호
	var $workDate;	//산정기간

	function Header(){
		$title = intVal($this->year).'년 '.intVal($this->month).'월 급여명세서('.$this->name.')';

		// 배경색 설정
		$this->SetFillColor(220,220,220);

		// 타이틀
		$this->SetXY($this->left, $this->left);
		$this->SetFont('굴림','B',15);
		$this->Cell($this->width, $this->rowHeight, $title, 0, 1, 'C');

		// 요양보호사 정보
		$this->SetXY($this->left, $this->top);
		$this->SetFont('굴림','B',9);
		$this->Cell(50, $this->rowHeight, '성명 : '.$this->name,			'LTB',	0, 'L', true);
		$this->Cell(50, $this->rowHeight, '요양보호사 번호 : '.$this->no,	'TB',	0, 'L', true);
		$this->Cell(82, $this->rowHeight, '근무기간 : '.$this->workDate,	'TBR',	1, 'R', true);
	}

	function Footer(){

	}
}
?>
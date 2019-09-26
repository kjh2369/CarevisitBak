<?php
//require('korean.php');
require('pdf_G.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	var $year;	//년도
	var $month;	//월
	var $type;
	var $name;
	var $jumin;

	var $auto_draw_head = true;

	// 행 높이 설정
	var $rowHeight = 6;

	function Header(){
	if($this->month == ''){
		$title = intVal($this->year).'년 4대보험료내역';
	}else {
		$title = intVal($this->year).'년 '.intVal($this->month).'월 4대보험료내역';
	}
		// 타이틀
		$this->SetXY($this->left, $this->left-4);
		$this->SetFont('굴림','B',15);

		$this->Cell($this->width, 5, $title, 0, 1, 'C');

		$this->SetFont('굴림','',10);
		$this->SetXY($this->left, $this->left);
		$this->Cell($this->width, 7, '출력일 : '.date('Y.m.d',mktime()), 0, 1, 'R');
		if ($this->auto_draw_head){
			$this->SetXY($this->left, $this->top);
			$this->drawHeader();
		}
	}

	function Footer(){
		$this->SetFont('굴림','',10);
		$this->SetXY($this->left, $this->left);
		$this->Cell(0,7,'Page '.$this->PageNo().'/{nb}',0,0,'L');

		$this->SetXY($this->left, -20);
	//	$this->SetFont('굴림','B',15);
		$this->Cell($this->left,5,'검색된전체갯수 : '.$this->rowCount,0,0,'L');

	}

	function drawHeader(){
		$headCol = $this->headColWidth();

		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY()+3);
		}

		$this->SetFont('굴림','B',9);
		/*
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['t'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C', true);
		}
		*/
		if($this->month == ''){
			$this->Cell($headCol['w'][0], $this->rowHeight*2, $headCol['t'][0], 1, 0, 'C', true);
			$this->Cell($headCol['w'][1], $this->rowHeight, $headCol['t'][1], 1, 0, 'C', true);
			$this->Cell($headCol['w'][2], $this->rowHeight, $headCol['t'][2], 1, 1, 'C', true);

			$this->SetX($this->left);

			$this->Cell($headCol['w'][0], 0, '', 0, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][3], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][4], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][5], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][6], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][7], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][8], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][9], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][10], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][11], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][12], 1, 0, 'C', true);
			$this->Cell($headCol['w'][3], $this->rowHeight, $headCol['t'][13], 1, 1, 'C', true);

			$this->SetXY(263, $this->GetY()-12);

			$this->Cell($headCol['w'][3], $this->rowHeight*2, $headCol['t'][14], 1, 1, 'C', true);

		}else {
			$this->Cell($headCol['ww'][0], $this->rowHeight*2, $headCol['tt'][0], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][1], $this->rowHeight, $headCol['tt'][1], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][2], $this->rowHeight, $headCol['tt'][2], 1, 1, 'C', true);

			$this->SetX($this->left);

			$this->Cell($headCol['ww'][0], 0, '', 0, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][3], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][4], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][5], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][6], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][7], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][8], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][9], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][10], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][11], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][12], 1, 0, 'C', true);
			$this->Cell($headCol['ww'][3], $this->rowHeight, $headCol['tt'][13], 1, 1, 'C', true);


			$this->SetXY(264, $this->GetY()-12);

			$this->Cell($headCol['ww'][3], $this->rowHeight*2, $headCol['t'][14], 1, 1, 'C', true);
		}

			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->top, $this->width, $this->rowHeight * 2);
			$this->SetLineWidth(0.2);


	}

	function headColWidth(){

		$col['w'][0] = 18;
		$col['w'][1] = 105;
		$col['w'][2] = 126;
		$col['w'][3] = 21;

		$col['t'][0] = "월";
		$col['t'][1] = "요양보호사부담";
		$col['t'][2] = "기관부담";
		$col['t'][3] = "국민연금";
		$col['t'][4] = "건강보험";
		$col['t'][5] = "장기요양";
		$col['t'][6] = "고용보험";
		$col['t'][7] = "소계";
		$col['t'][8] = "국민연금";
		$col['t'][9] = "건강보험";
		$col['t'][10] = "장기요양";
		$col['t'][11] = "고용보험";
		$col['t'][12] = "산재보험";
		$col['t'][13] = "소계";
		$col['t'][14] = "합계";


		$col['ww'][0] = 30;
		$col['ww'][1] = 100;
		$col['ww'][2] = 120;
		$col['ww'][3] = 20;

		$col['tt'][0] = "요양사명";
		$col['tt'][1] = "요양보호사부담";
		$col['tt'][2] = "기관부담";
		$col['tt'][3] = "국민연금";
		$col['tt'][4] = "건강보험";
		$col['tt'][5] = "장기요양";
		$col['tt'][6] = "고용보험";
		$col['tt'][7] = "합계";
		$col['tt'][8] = "국민연금";
		$col['tt'][9] = "건강보험";
		$col['tt'][10] = "장기요양";
		$col['tt'][11] = "고용보험";
		$col['tt'][12] = "산재보험";
		$col['tt'][13] = "소계";
		$col['tt'][14] = "합계";

		return $col;
	}
}
?>
<?php
//require('korean.php');
require('pdf_l.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	var $debug = false;
	var $acctBox = false;
	var $year;	//�⵵
	var $month;	//��
	//var $print_dt = date('Y.m.d', mktime());
	var $centerName;

	var $auto_draw_head = true;

	// ����������
	//var $left   = 14;
	//var $top    = 21;
	//var $width  = 182;
	//var $height = 182;

	// �� ���� ����
	var $rowHeight = 5;
	
	

	function Header(){
		
		$title = intVal($this->year).'�� '.intVal($this->month).'�� �ٹ���Ȳ';
		
		$this->SetFont($this->font_name_kor, "B", 13);

		$this->SetX($this->left);
		$this->Cell($this->width*0.5, $this->rowHeight*9, $this->centerName, 0, 1, 'L');
		
		$this->SetFont($this->font_name_kor, "B", 16);
		$this->SetXY($this->left, $this->top-3);
		$this->Cell($this->width, $this->rowHeight * 3, $title, 0, 1, 'C');

		$this->SetXY($this->left, $this->top+18);
		$this->drawHeader();
	}


	function drawHeader(){
		$headCol = $this->headColWidth();

		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY()+3);
		}

		//$liTop = $this->GetY();

		$this->SetFont('����','B',7);
		$this->SetFillColor(220,220,220);
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['t'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : ($i==39 ? 1 : 0), "C", true);
		}
		
		//$this->Cell($this->width, 3, "", 0, 1);
	}
	
	function Footer(){
		$this->SetFont('����','',10);
		$this->SetXY($this->left, $this->height+15);
		$this->Cell(0,7,'Page '.$this->PageNo().'/{nb}',0,0,'L');
		
		$this->SetXY($this-left, -20);
	}


	function headColWidth(){

		$col['w'][0] = $this->width*0.025;
		$col['w'][1] = $this->width*0.10;
		$col['w'][2] = $this->width*0.05;
		$col['w'][3] = $this->width*0.10;
		
		for($i=1; $i<=31; $i++){
			$col['w'][($i+3)] = $this->width*0.02;
		}
		
		$col['w'][35] = $this->width*0.04;
		$col['w'][36] = $this->width*0.04;	
		$col['w'][37] = $this->width*0.02;

		$col['t'][0] = 'No';
		$col['t'][1] = '����';
		$col['t'][2] = '������';
		$col['t'][3] = '�ð�';
		
		for($i=1; $i<=31; $i++){
			$col['t'][($i+3)] = ''.$i;
		}

		$col['t'][35] = '�ٹ��ϼ�';
		$col['t'][36] = '�ѽð�';
		$col['t'][37] = '���';

		return $col;
	}
}
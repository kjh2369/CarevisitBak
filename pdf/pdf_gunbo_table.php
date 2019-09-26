<?php
//require('korean.php');
require('pdf_G.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	var $year;	//�⵵
	var $month;	//��

	var $name;
	var $jumin;
	var $no;
	var $level;
	var $rate;

	var $type;
	var $useType;

	var $centerName;
	var $centerTel;

	var $bankName;
	var $bankNo;
	var $bankHolder;

	var $auto_draw_head = true;

	// �� ���� ����
	var $rowHeight = 6;

	function Header(){
			
		$title = intVal($this->year).'�� '.intVal($this->month).'�� �Ǻ����� ��ȹ����';
			
		// Ÿ��Ʋ
		$this->SetXY($this->left, $this->left-4);
		$this->SetFont('����','B',15);
		
		$this->Cell($this->width, 5, $title, 0, 1, 'C');
		$this->SetFont('����','',10);
		$this->Cell($this->width+5, 5, '����� : '.date('Y.m.d',mktime()), 0, 1, 'C');
		$this->SetFont('����','',10);
		$this->SetXY($this->left, $this->left);
		$this->Cell($this->width, 5, $this->centerName."(".$this->centerTel.")", 0, 1, 'R');
		

		if ($this->auto_draw_head){
			$this->SetXY($this->left, $this->top);
			$this->drawHeader();
		}
	}

	function Footer(){
		$this->SetFont('����','',10);
		$this->SetXY($this->left, $this->left);
		$this->Cell(0,7,'Page '.$this->PageNo().'/{nb}',0,0,'L');
		
		$this->SetXY($this-left, -20);
	//	$this->SetFont('����','B',15);
	}

	function drawHeader(){
		$headCol = $this->headColWidth();

		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY()+3);
		}

		$this->SetFont('����','B',9);
		/*
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['t'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C', true);	
		}
		*/

		$this->Cell($headCol['w'][0], $this->rowHeight, $headCol['t'][0], 'LTR', 0, 'L', true);
		$this->Cell($headCol['w'][1], $this->rowHeight, $headCol['t'][1], 'LTR', 0, 'L', true);
		$this->Cell($headCol['w'][2], $this->rowHeight*2, $headCol['t'][2], 'LTR', 0, 'C', true);
		$this->Cell($headCol['w'][3], $this->rowHeight*2, $headCol['t'][3], 'LTR', 0, 'C', true);
		$this->Cell($headCol['w'][4], $this->rowHeight*2, $headCol['t'][4], 1, 1, 'C', true);
			
		$this->SetXY($this->left, $this->GetY()-$this->rowHeight);
		
		$this->Cell($headCol['w'][0], $this->rowHeight, $headCol['t'][5], "LBR", 0, 'L', true);
		$this->Cell($headCol['w'][1], $this->rowHeight, $headCol['t'][6], "LBR", 1, 'R', true);
		/*
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['d'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C', true);	
		}
		*/
	
		
		$this->SetLineWidth(0.6);
		$this->Rect($this->left, $this->top, $this->width, $this->rowHeight * 2);
		$this->SetLineWidth(0.2);
	
	}
	
	function headColWidth(){
		
		$col['w'][0] = 30;
		$col['w'][1] = 40;
		$col['w'][2] = 30;
		$col['w'][3] = 150;
		$col['w'][4] = 20;

		$col['t'][0] = "�����ڸ�";
		$col['t'][1] = "��纸ȣ��";
		$col['t'][2] = "�����ð�";
		$col['t'][3] = "��������/������";
		$col['t'][4] = "Ƚ��";
		$col['t'][5] = "�����������ȣ";
		$col['t'][6] = "�������";
		

		return $col;
	}
}
?>
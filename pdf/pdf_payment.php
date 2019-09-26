<?php
require('korean.php');

class MYPDF extends PDF_Korean{
	// ����������
	var $left   = 14;
	var $top    = 21;
	var $width  = 182;
	var $height = 262;

	// �� ���� ����
	var $rowHeight = 6;

	// ����
	var $year;		//��
	var $month;		//��
	var	$name;		//��纸ȣ���
	var $no;		//��纸ȣ���ȣ
	var $workDate;	//�����Ⱓ

	function Header(){
		$title = intVal($this->year).'�� '.intVal($this->month).'�� �޿�����('.$this->name.')';

		// ���� ����
		$this->SetFillColor(220,220,220);

		// Ÿ��Ʋ
		$this->SetXY($this->left, $this->left);
		$this->SetFont('����','B',15);
		$this->Cell($this->width, $this->rowHeight, $title, 0, 1, 'C');

		// ��纸ȣ�� ����
		$this->SetXY($this->left, $this->top);
		$this->SetFont('����','B',9);
		$this->Cell(50, $this->rowHeight, '���� : '.$this->name,			'LTB',	0, 'L', true);
		$this->Cell(50, $this->rowHeight, '��纸ȣ�� ��ȣ : '.$this->no,	'TB',	0, 'L', true);
		$this->Cell(82, $this->rowHeight, '�ٹ��Ⱓ : '.$this->workDate,	'TBR',	1, 'R', true);
	}

	function Footer(){

	}
}
?>
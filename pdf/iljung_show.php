<?php
require_once('pdf_l.php');

class MYPDF extends MY_PDF{
	var $year       = null;
	var $month      = null;
	var $svc_text   = null;
	var $row_height = 6;

	function Header(){
		$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����ǥ';

		$this->SetFont($this->font_name_kor,'B',15);

		// Ÿ��Ʋ
		$this->SetXY($this->left, $this->top);
		$this->Cell($this->width, 5, $title, 0, 1, 'L');

		$this->SetXY($this->left, $this->top);
		$this->Cell($this->width, 5, '���� ���� : '.$this->svc_text, 0, 1, 'R');
	}

	function Footer(){
	}



	/**************************

		�޷� ����

	**************************/
	function calrander_width(){
		$array[0] = $this->width*0.1428;
		$array[1] = $this->width*0.1428;
		$array[2] = $this->width*0.1428;
		$array[3] = $this->width*0.1428;
		$array[4] = $this->width*0.1428;
		$array[5] = $this->width*0.1428;
		$array[6] = $this->width*0.1428;

		return $array;
	}

	function calrander_weekday(){
		$array[0] = '��';
		$array[1] = '��';
		$array[2] = 'ȭ';
		$array[3] = '��';
		$array[4] = '��';
		$array[5] = '��';
		$array[6] = '��';

		return $array;
	}
}
?>
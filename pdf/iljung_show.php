<?php
require_once('pdf_l.php');

class MYPDF extends MY_PDF{
	var $year       = null;
	var $month      = null;
	var $svc_text   = null;
	var $row_height = 6;

	function Header(){
		$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 일정표';

		$this->SetFont($this->font_name_kor,'B',15);

		// 타이틀
		$this->SetXY($this->left, $this->top);
		$this->Cell($this->width, 5, $title, 0, 1, 'L');

		$this->SetXY($this->left, $this->top);
		$this->Cell($this->width, 5, '서비스 구분 : '.$this->svc_text, 0, 1, 'R');
	}

	function Footer(){
	}



	/**************************

		달력 설정

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
		$array[0] = '일';
		$array[1] = '월';
		$array[2] = '화';
		$array[3] = '수';
		$array[4] = '목';
		$array[5] = '금';
		$array[6] = '토';

		return $array;
	}
}
?>
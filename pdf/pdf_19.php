<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	//var $left   = 14;
	//var $top    = 21;
	//var $width  = 182;
	//var $height = 270;

	var $pageFirstALast = 11;
	var $pageFirst		= 17;
	var $pageLast		= 13;
	var $pageElse		= 20;

	var $rowHeight		= 12;

	var $listIndex	= 0;
	var $listCount	= 0;
	var $listSplit	= 0;
	var $maxPage	= 0;

	var $date;		//����
	var $weekday;	//����
	var $time;		//�ð�
	var $writer;	//�ۼ���

	var $sugupja;	//�����
	var $gender;	//����
	var	$age;		//����
	var $level;		//���

	var $take;		//�ΰ���
	var $yoy;		//�μ���
	var $man;		//Ȯ����
	var $center;	//�����

	function Header(){
		if ($this->PageNo() == 1){
			$this->SetXY($this->left, $this->top);
			$this->SetFont('����','B',30);
			$this->Cell(114,18,	'���� �ΰ�  ���μ���',	1,	0,	'C');
			$this->Cell(8,	18,	'',	1,	0,	'C');
			$this->Cell(20,	6,	'',	1,	0,	'C');
			$this->Cell(20,	6,	'',	1,	0,	'C');
			$this->Cell(20,	6,	'',	1,	1,	'C');

			$this->SetX($this->left);
			$this->Cell(114,0,	'',	0,	0,	'C');
			$this->Cell(8,	0,	'',	0,	0,	'C');
			$this->Cell(20,	12,	'',	1,	0,	'C');
			$this->Cell(20,	12,	'',	1,	0,	'C');
			$this->Cell(20,	12,	'',	1,	1,	'C');

			$croodY = $this->GetY();

			$this->SetXY($this->left+115, $this->top+3);
			$this->SetFont('����','B',11);
			$this->MultiCell(8,	6,	"��\n��");

			$this->SetXY($this->left, $croodY);
			$this->SetFont('����','B',11);
			$this->Cell(28,	8,	'�� ��',	1,	0,	'C');
			$this->SetFont('����','',11);
			$this->Cell(86,	8,	$this->date.($this->weekday != '' ? '('.$this->weekday.')����' : '').$this->time,	1,	0,	'C');
			$this->SetFont('����','B',11);
			$this->Cell(28,	8,	'�ۼ���',	1,	0,	'C');
			$this->SetFont('����','',11);
			$this->Cell(40,	8,	$this->writer,	1,	1,	'C');

			$coordY = $this->GetY();

			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->top, $this->width, $coordY - $this->top);
			$this->SetLineWidth(0.2);

			$this->SetXY($this->left, $coordY + 5);
		}else{
			$this->SetXY($this->left, $this->top);
		}

		$this->Cell(31,	8,	'�����',	1,	0,	'C');
		$this->Cell(26,	8,	$this->sugupja,	1,	0,	'C');
		if($this->writer != ''){
			$this->Cell(19,	8,	'����',	1,	0,	'C');
			$this->Cell(17,	8,	$this->gender,	1,	0,	'C');
			$this->Cell(19,	8,	'����',	1,	0,	'C');
			$this->Cell(27,	8,	$this->age,	1,	0,	'C');
		}else {
			$this->Cell(19,	8,	'����',	1,	0,	'C');
			$this->Cell(17,	8,	'',	1,	0,	'C');
			$this->Cell(19,	8,	'����',	1,	0,	'C');
			$this->Cell(27,	8,	'',	1,	0,	'C');
		}
		$this->Cell(23,	8,	'���',	1,	0,	'C');		
		$this->Cell(20,	8,	$this->level,	1,	1,	'C');

		$this->SetFont('����',	'',	11);

		$col = $this->_col();
		$cols = sizeOf($col);

		$this->SetX($this->left);

		for($i=0; $i<$cols; $i++){
			if ($i == $cols - 1){
				$nextFocus = 1;
			}else{
				$nextFocus = 0;
			}
			if ($col[$i]['m'] == 'Y'){
				$coordX1 = $this->GetX();
				$coordY1 = $this->GetY()+1;

				$this->Cell($col[$i]['w'],	$this->rowHeight,	'',	1,	$nextFocus,	'C');

				$coordX2 = $this->GetX();
				$coordY2 = $this->GetY();

				$this->SetXY($coordX1, $coordY1);
				$this->MultiCell($col[$i]['w'], 5, $col[$i]['t'], 0, 'C');

				$this->SetXY($coordX2, $coordY2);
			}else{
				$this->Cell($col[$i]['w'],	$this->rowHeight,	$col[$i]['t'],	1,	$nextFocus,	'C');
			}
		}
	}

	function Footer(){
		if ($this->PageNo() == $this->maxPage){
			$col = $this->_col();
			$cols = sizeOf($col);

			if ($this->listIndex < $this->listSplit){
				for($i=$this->listIndex; $i<$this->listSplit; $i++){
					$this->SetX($this->left);

					for($j=0; $j<$cols; $j++){
						if ($j == $cols - 1){
							$next = 1;
						}else{
							$next = 0;
						}
						$this->Cell($col[$j]['w'], $this->rowHeight, "", 1, $next, 'L');
					}
				}
			}

			$this->SetFont('����','',11);
			$this->SetX($this->left);
			$this->Cell($this->width, 60, '', 1, 1, 'C');

			$coordY = $this->GetY() - 60;

			$this->SetX($this->left);
			$this->Cell($this->width, 15, $this->center, 1, 1, 'C');
			$this->SetXY($coordX, $coordY);

			$date = explode('.', $this->date);

			$this->SetXY($this->left, $coordY);
			$this->Cell($this->width, 15, '���� ���� ���� �ΰ�.�μ��� Ȯ����.', 0, 1, 'C');
			$this->SetX($this->left);
			$this->Cell($this->width, 12, $date[0].'��   '.$date[1].'��   '.$date[2].'��', 0, 1, 'C');

			$this->SetX($this->left);
			$this->Cell(100, 8, '�� �� �� : ', 0, 0, 'R');
			$this->Cell(25, 8, $this->take, 0, 0, 'L');
			$this->Cell(10, 8, '  (��)', 0, 1, 'L');

			$this->SetX($this->left);
			$this->Cell(100, 8, '�� �� �� : ', 0, 0, 'R');
			$this->Cell(25, 8, $this->yoy, 0, 0, 'L');
			$this->Cell(10, 8, '  (��)', 0, 1, 'L');

			$this->SetX($this->left);
			$this->Cell(100, 8, 'Ȯ �� �� : ', 0, 0, 'R');
			$this->Cell(25, 8, $this->man, 0, 0, 'L');
			$this->Cell(10, 8, '  (��)', 0, 1, 'L');
		}
	}

	function _setListSplit(){
		if ($this->PageNo() == $this->maxPage){
			if ($this->PageNo() == 1){
				$this->listSplit = $this->pageFirstALast;
			}else{
				$this->listSplit = $this->pageLast;
			}
		}else{
			if ($this->PageNo() == 1){
				$this->listSplit = $this->pageFirst;
			}else{
				if($this->writer != ''){
					$this->listSplit = $this->pageElse;
				}else {
					$this->listSplit = $this->pageFirstALast;
				}

			}
		}
	}

	function _col(){
		$col[0]['w'] = 62;
		$col[1]['w'] = 55;
		$col[2]['w'] = 32;
		$col[3]['w'] = 33;

		$col[0]['t'] = "�� �� �� ��";
		$col[1]['t'] = "�� �� �� ��";
		$col[2]['t'] = "�� ��";
		$col[3]['t'] = "�޿���������\n���Ͽ���";

		$col[0]['m'] = 'N';
		$col[1]['m'] = 'N';
		$col[2]['m'] = 'N';
		$col[3]['m'] = 'Y';

		return $col;
	}

	function AcceptPageBreak(){
		$this->AddPage('P',A4);
		$this->maxPage = $this->PageNo();
		$this->SetX($this->left);

		return false;
	}

	function MaxPageNo(){
		return '{nb}';
	}
}
?>

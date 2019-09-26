<?php
require('pdf_p.php');

class MYPDF extends MY_PDF{
	var $centerCode;
	var $centerName;

	var $sugupjaLevel;
	var $sugupjaName;
	var $sugupjaJumin;
	var $sugupjaNo;

	var $imageName = '../image/yokchang.png';

	function Header(){
		$this->_showTop('��â���� �����', 24);

		$this->SetFont('����', '', 10);
		$this->SetX($this->left);
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->centerCode, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.52, 10, $this->centerName, 1, 1, 'L');
		//$this->Cell($this->width*0.15, 10, '�������', 1, 0, 'C');
		//$this->Cell($this->width*0.18, 10, $this->sugupjaLevel, 1, 1, 'C');

		$Y = $this->GetY();
		$this->SetXY($this->left, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "�����\n�����ȣ", 0, 'C');

		$this->SetXY($this->left+$this->width*0.15+$this->width*0.18, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "�����\n�����", 0, 'C');

		$this->SetXY($this->left, $Y);
		$this->Cell($this->width*0.15, 10, '������ ����', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->sugupjaName, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '�ֹε�Ϲ�ȣ', 1, 0, 'C');
		$this->Cell($this->width*0.19, 10, $this->sugupjaJumin, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->sugupjaNo, 1, 1, 'C');

		$Y = $this->GetY();
		$this->SetXY($this->left+$this->width*0.15+$this->width*0.18+$this->width*0.15+$this->width*0.19, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "�����\n������ȣ", 0, 'C');

		$this->SetY($Y);
	}

	function Footer(){
		$this->SetX($this->left);
		$this->Cell($this->width, 8, '* �ش��ȣ�� �����ϵ� �ش������ ���� ��� ������ ���', 1, 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '1) ����(���� ������ ������ ǥ��)', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '2) ����', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '�� �Ǻλ���ȭ', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '�� ����, ǥ���ı�', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '�� ������������', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '�� �������� ������', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '3) ���', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� ������׻�', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '4) ����', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� O����', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� ȭ��', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� M����(mild)', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� �̹���', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '�� F����(foul)', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '5)óġ', "TB", 0, 'L');
		$this->Cell($this->width*0.25, 8, 'D:Debridement', "TB", 0, 'L');
		$this->Cell($this->width*0.25, 8, 'C:Curettage dressing', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.25, 8, 'S/D:Soaking dressing', "TB", 0, 'L');
		$this->Cell($this->width*0.25, 8, 'G/D:Gauze dressing', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LT", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "T", 0, 'L');
		$this->Cell($this->width*0.5, 8, 'M:Massage      I:Irrigation      P:Packing', "RT", 1, 'L');

		$this->Image($this->imageName, $this->left, $this->GetY()-71, 0, 70);

		$this->_drawBorder();
	}

	function _col(){
		$col[0]['w'] = $this->width*0.15;
		$col[1]['w'] = $this->width*0.12;
		$col[2]['w'] = $this->width*0.12;
		$col[3]['w'] = $this->width*0.12;
		$col[4]['w'] = $this->width*0.12;
		$col[5]['w'] = $this->width*0.12;
		$col[6]['w'] = $this->width*0.12;
		$col[7]['w'] = $this->width*0.13;

		return $col;
	}
}
?>
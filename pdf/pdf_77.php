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
		$this->_showTop('욕창관리 기록지', 24);

		$this->SetFont('바탕', '', 10);
		$this->SetX($this->left);
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->centerCode, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.52, 10, $this->centerName, 1, 1, 'L');
		//$this->Cell($this->width*0.15, 10, '장기요양등급', 1, 0, 'C');
		//$this->Cell($this->width*0.18, 10, $this->sugupjaLevel, 1, 1, 'C');

		$Y = $this->GetY();
		$this->SetXY($this->left, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "장기요양\n기관기호", 0, 'C');

		$this->SetXY($this->left+$this->width*0.15+$this->width*0.18, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "장기요양\n기관명", 0, 'C');

		$this->SetXY($this->left, $Y);
		$this->Cell($this->width*0.15, 10, '수급자 성명', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->sugupjaName, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '주민등록번호', 1, 0, 'C');
		$this->Cell($this->width*0.19, 10, $this->sugupjaJumin, 1, 0, 'C');
		$this->Cell($this->width*0.15, 10, '', 1, 0, 'C');
		$this->Cell($this->width*0.18, 10, $this->sugupjaNo, 1, 1, 'C');

		$Y = $this->GetY();
		$this->SetXY($this->left+$this->width*0.15+$this->width*0.18+$this->width*0.15+$this->width*0.19, $Y-9);
		$this->MultiCell($this->width*0.15, 4, "장기요양\n인정번호", 0, 'C');

		$this->SetY($Y);
	}

	function Footer(){
		$this->SetX($this->left);
		$this->Cell($this->width, 8, '* 해당번호를 기입하되 해당사항이 없는 경우 간단히 기술', 1, 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '1) 부위(붉은 펜으로 부위를 표시)', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '2) 정도', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '① 피부색변화', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '② 물집, 표피파괴', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '③ 피하조직노출', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.5, 8, '④ 근조직과 뼈노출', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '3) 배액', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '① 장액혈액상', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '4) 냄새', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '① O없음', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '② 화농성', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '② M보통(mild)', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '③ 이물질', "TB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '', "TB", 0, 'L');
		$this->Cell($this->width*0.2, 8, '③ F심함(foul)', "RTB", 1, 'L');

		$this->SetX($this->left);
		$this->Cell($this->width*0.4, 8, '', "LTB", 0, 'L');
		$this->Cell($this->width*0.1, 8, '5)처치', "TB", 0, 'L');
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
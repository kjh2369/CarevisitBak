<?php
require('pdf_p.php');

class MYPDF extends MY_PDF{
	var $type = 'person';
	var $salary_mm;
	var $mem_cd;		//직원코드
	var $mem_nm;		//직원명
	var $mem_join_dt;	//입사일
	var $mem_out_dt;	//퇴사일
	var $mem_no;		//직원번호
	var $addon_caption;
	var $addon_pay;
	var $col = 0;

	function Header(){
	}

	function Footer(){
	}

	function drawTitle(){
		switch($this->type){
		case 'person':
			$this->_showTop('개인급여대장', 20, false, true);
			break;
		case 'center':
			$this->_showTop('기관급여대장', 20, false, true);
			break;
		case 'month':
			$this->_showTop($this->salary_mm.'월 급여대장', 20, false, true);
			break;
		}

		$this->set_font();

		$this->set_default_xy();

		switch($this->type){
		case 'person':
			$this->Cell($this->width * 0.10, $this->row_height, "성명", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_nm, 1, 0, "L");
			$this->Cell($this->width * 0.15, $this->row_height, "입사일", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_join_dt, 1, 0, "C");
			$this->Cell($this->width * 0.15, $this->row_height, "퇴사일", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_out_dt, 1, 1, "C");
			break;
		default:
			$this->Cell($this->width * 0.10, $this->row_height, "기관명", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_nm, 1, 1, "L");
		}
	}

	function drawCaption(){
		$w = $this->width * 0.20;
		$h = $this->row_height;

		$this->set_default_xy();
		$basic_y = $this->GetY();
		$this->Cell($w, $h, "", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "급여총액(A + B + E)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "공제총액(C + D + F)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "차인지급액", 1, 1, "L", true);

		$temp_y = $this->GetY();

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(A)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "근무일수", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "근무시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "기본급", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "주휴일수", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "유급일수", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "목욕횟수", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "간호횟수", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "식대보조비", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "차량유지비", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.8, $h, "업무수당", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 18);
		$this->MultiCell($w * 0.2, $h, "기\n본\n근\n무");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(B)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "연장시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "야간시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "휴일시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "휴연시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.45, $h, "휴야시간", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "수당", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 6);
		$this->MultiCell($w * 0.2, $h, "초\n과\n근\n무");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(C)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "국민연금", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "건강보험", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "장기요양", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.80, $h, "고용보험", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 3);
		$this->MultiCell($w * 0.2, $h, "보\n험\n항\n목");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(D)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "갑근세", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.80, $h, "주민세", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 0.5);
		$this->MultiCell($w * 0.2, $h, "소\n득\n세");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(E)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "직급수당", 1, 1, "L", true);

		$addon_count = sizeof($this->addon_caption[1]);

		for($i=0; $i<$addon_count; $i++){
			$this->set_default_xy();
			$this->Cell($w * 0.20, $h, "", "LR".($i +1 == $addon_count ? "B" : ""), 0, "C", true);
			$this->Cell($w * 0.80, $h, $this->addon_caption[1][$i]['subject'], 1, 1, "L", true);
		}

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + (($h * ($addon_count + 2) - ($h * 2)) / 2));
		$this->MultiCell($w * 0.2, $h, "수\n당");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "합계(F)", 1, 1, "L", true);

		$addon_count = sizeof($this->addon_caption[2]);

		for($i=0; $i<$addon_count; $i++){
			$this->set_default_xy();
			$this->Cell($w * 0.20, $h, "", "LR".($i +1 == $addon_count ? "B" : ""), 0, "C", true);
			$this->Cell($w * 0.80, $h, $this->addon_caption[2][$i]['subject'], 1, 1, "L", true);
		}

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + (($h * ($addon_count + 1) - ($h * 2)) / 2));
		$this->MultiCell($w * 0.2, $h, "공\n제");

		$this->SetXY($this->left + $w, $basic_y);
	}
}
?>

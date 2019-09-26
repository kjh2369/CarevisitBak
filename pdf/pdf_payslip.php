<?php
require_once('../pdf/pdf_l.php');

class MYPDF extends MY_PDF{
	var $year			= null;
	var $month			= null;
	var $member_name	= null;
	var $center_name	= null;

	var $x	= 0;
	var $y	= 0;
	var $w	= 0;
	var $pos= 0;

	var $top			= 14;
	var $title_height	= 8;
	var $row_height		= 6;
	var $text_height	= 6;

	var $title_x	= 0;
	var $title_y	= 0;
	var $title_w	= 0;
	var $title_h	= 0;

	var $basic_x	= 0;
	var $basic_y	= 0;
	var $basic_w	= 0;
	var $basic_h	= 0;

	var $over_x	= 0;
	var $over_y	= 0;
	var $over_w	= 0;
	var $over_h	= 0;

	var $ins_x	= 0;
	var $ins_y	= 0;
	var $ins_w	= 0;
	var $ins_h	= 0;

	var $amt_x	= 0;
	var $amt_y	= 0;
	var $amt_w	= 0;
	var $amt_h	= 0;

	var $tax_x	= 0;
	var $tax_y	= 0;
	var $tax_w	= 0;
	var $tax_h	= 0;

	var $give_x	= 0;
	var $give_y	= 0;
	var $give_w	= 0;
	var $give_h	= 0;

	var $deduct_x	= 0;
	var $deduct_y	= 0;
	var $deduct_w	= 0;
	var $deduct_h	= 0;

	function Header(){
		$this->x = $this->left;
		$this->y = $this->top;
		$this->w = ($this->width / 2) - ($this->width / 2 * 0.08);
	}

	function Footer(){
		$this->SetXY($this->left, -13);
		$this->set_font(12);
		$this->Cell($this->w, $this->text_height, '수고하셨습니다.', 0, 1, 'C');

		if ($_GET['member'] == 'dtl_all'){
			//상세명세
		}else {
			if ($this->pos == 0){
				$this->SetXY($this->left + ($this->width / 2) + ($this->width / 2 * 0.08), -13);
				$this->Cell($this->w, $this->text_height, '수고하셨습니다.', 0, 1, 'C');
			}
		}
	}

	function draw_border(){
		if ($this->pos == 0){
			$this->Rect($this->left,
						$this->top + $this->text_height + $this->row_height,
						$this->w,
						$this->height - $this->text_height - 6);
		}else{
			$this->Rect($this->left + ($this->width / 2) + ($this->width / 2 * 0.08),
						$this->top + $this->text_height + $this->row_height,
						$this->w,
						$this->height - $this->text_height - 6);
		}
		$this->Line($this->left + ($this->width / 2), $this->top, $this->left + ($this->width / 2), $this->height + 15);
	}

	function set_default_xy($coord_x = 0, $coord_y = 0){
		if ($this->pos == 1){
			$add_x = ($this->width / 2) + ($this->width / 2 * 0.08);
		}else{
			$add_x = 0;
		}

		$this->x = $this->left + $coord_x + $add_x;
		$this->y = $this->GetY() + $coord_y;

		$this->SetXY($this->x, $this->y);
	}
}
?>
<?php
include_once('../inc/_http_uri.php');
require_once('../pdf/pdf_'.$paper_dir.'.php');

class MYPDF extends MY_PDF{
	var $mode  = null;
	
	var $cp_nm = null;
	var $cp_ci = null;
	
	var $ct_nm = null;
	var $ct_ci = null;
	
	var $title = null;
	
	var $year    = null;
	var $month   = null;
	var $lastday = null;
	var $val_w   = null;
	
	var $top    = 10;
	var $left   = 10;
	var $width  = 10;
	var $height = 10;
	
	function Header(){
		if (!is_null($this->title)){
			/**************************************************
				타이틀 출력
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 15);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width, 15, $this->title, 0, 1, 'C');
			
			
			
			/**************************************************
				기본 폰트 설정
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 11);
		}
		
		if ($this->mode == 100){
			/**************************************************
				1일의 요일
			**************************************************/
			$week_nm = array(0=>'일', 1=>'월', 2=>'화', 3=>'수', 4=>'목', 5=>'금', 6=>'토');
			$week_id = date('w', strtotime($this->year.'-'.$this->month.'-01'));
			$lastday = $this->lastday;
			
			$this->val_w = (31 - $lastday) * 0.02;
			


			$this->SetFont($this->font_name_kor, '', 7);
			$this->SetX($this->left);
			$this->Cell($this->width * 0.03, $this->row_height * 2, '순번', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.07, $this->row_height * 2, '수급자', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.07, $this->row_height * 2, '보호사', 1, 0, 'C', 1);
			$this->Cell($this->width * (0.08 + $this->val_w), $this->row_height, '요일', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.03, $this->row_height * 2, '시간', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.03, $this->row_height * 2, '일수', 1, 0, 'C', 1);
			
			$weekday = $week_id;
			
			for($i=1; $i<=$lastday; $i++){
				switch($weekday){
					case 0:
						$this->SetTextColor(255, 0, 0);
						break;
					case 6:
						$this->SetTextColor(0, 0, 255);
						break;
					default:
						$this->SetTextColor(0, 0, 0);
				}
				
				$this->Cell($this->width * 0.02, $this->row_height, $week_nm[$weekday], 1, 0, 'C', 1);
				$weekday ++;
				
				if ($weekday > 6) $weekday = 0;
			}
			
			$this->SetTextColor(0, 0, 0);
			$this->Cell($this->width * 0.07, $this->row_height * 2, '급여총액', 1, 0, 'C', 1);
			
			$this->SetXY($this->left + $this->width * 0.17, $this->GetY() + $this->row_height);
			$this->Cell($this->width * (0.08 + $this->val_w), $this->row_height, '근여현황', 1, 0, 'C', 1);
			$this->SetX($this->GetX() + $this->width * 0.06);
			
			$weekday = $week_id;
			
			for($i=1; $i<=$lastday; $i++){
				switch($weekday){
					case 0:
						$this->SetTextColor(255, 0, 0);
						break;
					case 6:
						$this->SetTextColor(0, 0, 255);
						break;
					default:
						$this->SetTextColor(0, 0, 0);
				}
				
				$this->Cell($this->width * 0.02, $this->row_height, number_format($i), 1, 0, 'C', 1);
				$weekday ++;
				
				if ($weekday > 6) $weekday = 0;
			}
			
			$this->SetY($this->GetY() + $this->row_height);
		}
	}

	function Footer(){
		/*
		$this->SetFont($this->font_name_kor, '', 12);
		$this->SetXY($this->left, -7);
		
		$pos_y = $this->GetY();
		
		if (!empty($this->ct_ci)){
			$this->Image('../mem_picture/'.$this->ct_ci, $this->left, $this->GetY() - (10 - $ci_img[1] / 3.8) / 2);
			$ci_img = getimagesize('../mem_picture/'.$this->ct_ci);
			$this->SetXY($this->left + $ci_img[0] / 3.8 + 2, $pos_y - 5);
		}
		
		$this->Cell($this->width, 10, $this->ct_nm, 0, 0, 'L');
		
		if (!empty($this->cp_ci)){
			$ci_img = getimagesize($this->cp_ci);
			$this->Image($this->cp_ci, $this->width - $this->GetStringWidth($this->cp_nm) - $ci_img[0] / 3.8 + 3, $pos_y - $ci_img[1] / 3.8);
		}
		
		$this->SetX($this->left);
		$this->Cell($this->width, 10, $this->cp_nm, 0, 0, 'R');
		*/
		//$this->_drawCi();
		
		if($this->domain == 'dolvoin.net'){
			$this->SetXY($this->left, -15);
			$this->SetFont($this->font_name_kor,'B',20);
			$this->Cell($this->width, 5, $this->ct_nm, 0, 1, 'C');

			$this->_drawIcon();
		}
	}
}
?>
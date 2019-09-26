<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	
	/*
	var $code;
	var $cname;
	var $mname;
	var $mjumin;
	var $marry_yn;
	var $tel;
	var $mobile;
	var $email;
	*/
	var $auto_draw_head = true;

	// 행 높이 설정
	var $rowHeight = 6;

	function Header(){
		// 타이틀
	
		$this->SetXY($this->left, $this->top);
		$this->SetFont('굴림','B',12);
		$this->SetLineWidth(0.2);
		$this->Cell($this->width, 12, '직원초기상담기록지', 1, 1, 'C');

		if ($this->auto_draw_head){
			$this->drawHeader();
		}
	}

	function Footer(){
		/*
		if ($this->useType == 'y'){
			// 관리자
		}else{
			if ($this->type == 's'){
				// 수급자
			}else if ($this->tupe == 'y'){
				// 요양보호사
			}else{
			}
		}

		$this->SetXY($this-left, -20);
		$this->SetFont('굴림','B',15);
		$this->Cell($this->left+$this->width, 5, $this->centerName."(".$this->centerTel.")", 0, 1, 'C');
		$this->SetXY($this-left, -13);
		$this->SetFont('굴림','',11);
		$this->Cell($this->left+$this->width, 5, "입금계좌:".$this->bankName."(".$this->bankNo.") 예금주:".$this->bankDepos, 0, 1, 'C');
		*/
	}

	function drawHeader(){
		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY());
		}
		
		$this->SetFont('굴림','',9);
		$this->SetFillColor(220,220,220);

		$this->SetX($this->left);
		$this->Cell(22,11,'기관기호',1,0,'C',true);
		$this->Cell(50,11,$this->code,1,0,'L');
		$this->Cell(25,11,'기관명',1,0,'C',true);
		$this->Cell(85,11,$this->cname,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,39,'사 진',1,0,'C');
		$this->Cell(20,6.5,'성명',1,0,'C',true);
		$this->Cell(35,6.5,$this->mname,1,0,'L');
		$this->Cell(20,19.5,'연락처',1,0,'C',true);
		$this->Cell(20,6.5,'유선',1,0,'C',true);
		$this->Cell(57,6.5,$this->tel,1,1,'C');

		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,6.5,'주민번호',1,0,'C',true);
		$this->Cell(35,6.5,$this->mjumin,1,0,'L');
		$this->Cell(20,0,'',0,0,'L');
		$this->Cell(20,6.5,'무선',1,0,'C',true);
		$this->Cell(57,6.5,$this->mobile,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,6.5,'결혼여부',1,0,'C',true);
		$this->Cell(35,6.5,'□YES   □NO',1,0,'L');
		$this->Cell(20,0,'',0,0,'L');
		$this->Cell(20,6.5,'e-mail',1,0,'C',true);
		$this->Cell(57,6.5,$this->email,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,19.5,'주소',1,0,'C',true);
		$this->Cell(20,6.5,'우편번호',1,0,'C',true);
		$this->Cell(112,6.5,$this->postno,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,0,'',0,0,'C');
		$this->Cell(132,6.5,$this->addr,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,0,'',0,0,'C');
		$this->Cell(132,6.5,$this->addr_dtl,1,1,'L');


		if($this->picture != ''){
			$this->Image('../mem_picture/'.$this->picture, 14.7, 44.5, 28.5, 38.1);
		}
		
		//결혼여부
		if($this->marry_yn == 'Y'){
			$this->text(65.5,61,'v');
		}else {
			$this->text(78.5,61,'v');
		}

		/*
		$this->SetLineWidth(0.5);
		$this->Line(14,43,44,43);
		$this->Line(14,43,14,83);
		$this->Line(44,43,44,83);
		$this->Line(14,83,44,83);
		*/

		// 테두리
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.2);
			$this->Rect($this->left, $this->top, $this->width, 62);
		}else{
			$this->SetY($this->GetY());
		}
	}
		
}
?>
<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{

	var $auto_draw_head = true;

	var $rowHeight = 7;

	function Header(){
		// 타이틀

		$this->SetXY($this->left, $this->top);
		$this->SetFont('바탕','B',12);
		$this->SetLineWidth(0.2);
		$this->Cell($this->width, $this->rowHeight+5, '초기상담(욕구사정)기록지', 1, 1, 'C');

		if ($this->auto_draw_head){
			$this->drawHeader();
		}
	}

	function Footer(){
		$this->_drawIcon();
	}

	function drawHeader(){
		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY());
		}
		
		
		$this->SetFont('바탕','',9);
		$this->SetFillColor(220,220,220);
		$this->SetTextColor(0,0,0);

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'기관기호',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->code,1,0,'L');
		$this->Cell(20,$this->rowHeight,'기관명',1,0,'C',true);
		$this->Cell(97,$this->rowHeight,$this->cname,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'상담구분',1,0,'C',true);
		$this->Cell(162,$this->rowHeight,$this->counsel_gbn,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'상담자',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->talker_nm,1,0,'L');
		$this->Cell(20,$this->rowHeight,'상담유형',1,0,'C',true);
		$this->Cell(40,$this->rowHeight,' □내방  □방문  □전화',1,0,'L');
		$this->Cell(20,$this->rowHeight,'상담일자',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->client_dt,1,1,'L');

		$this->SetFont('바탕','',9);
		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'성명',1,0,'C',true);
		$this->Cell(65,$this->rowHeight,$this->client_nm,1,0,'L');
		$this->Cell(20,14,'연락처',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'유선',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->tel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'주민번호',1,0,'C',true);
		$this->Cell(65,$this->rowHeight,$this->client_ssn,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'무선',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->mobile,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,21,'소재',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'우편번호',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->postno,1,0,'L');
		$this->Cell(20,21,'보호자',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'성명',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_nm,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(65,$this->rowHeight,$this->addr,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'관계',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_rel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(65,$this->rowHeight,$this->addr_dtl,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'전화번호',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_tel,1,1,'L');


		if($this->gbn == '3'){
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'보호구분',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'□일반  □차상위  □기초생활수급자',1,1,'L');
		}else {
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'보호구분',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'□수급자1종  □수급자2종  □일반  □기타 (                )',1,1,'L');
		}
		
		if($this->gbn == '0'){
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'요양등급',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'□1등급  □2등급  □3등급  □4등급  □5등급  □일반',1,1,'L');
		}

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'가족형태',1,0,'C',true);
		$this->Cell(162,$this->rowHeight,'□독거  □노인부부  □아들가족  □딸가족  □기타',1,1,'L');				
		//상담유형
		switch($this->talker_type){
			case 1:
				$this->Text(101.7, 51, 'v');
				break;
			case 2:
				$this->Text(113.3, 51, 'v');
				break;
			case 3:
				$this->Text(125, 51, 'v');
				break;
		}

		if($this->gbn == '3'){
			//보호구분
			switch($this->protect_gbn){
				case 1:
					$this->Text(35.6, 93.2, 'v');
					break;
				case 2:
					$this->Text(47.3, 93.2, 'v');
					break;
				case 9:
					$this->Text(62.2, 93.2, 'v');
					break;
			}
		}else{
			switch($this->protect_gbn){
				case 1:
					$this->Text(35.6, 93.2, 'v');
					break;
				case 2:
					$this->Text(55.7, 93.2, 'v');
					break;
				case N:
					$this->Text(75.6, 93.2, 'v');
					break;
				case 9:
					$this->Text(87.3, 93.2, 'v');
					$this->Text(99, 93.2, $this->protect_other);
					break;
			}
			
			if($this->gbn == '0'){
				switch($this->level_gbn){
					case 1:
						$this->Text(35.6, 100.2, 'v');
						break;
					case 2:
						$this->Text(48.9, 100.2, 'v');
						break;
					case 3:
						$this->Text(62.6, 100.2, 'v');
						break;
					case 4:
						$this->Text(76.3, 100.2, 'v');
						break;
					case 5:
						$this->Text(90, 100.2, 'v');
						break;
					case 9:
						$this->Text(103.7, 100.2, 'v');
						break;	
				}
			}
		}

		if($this->gbn == '0'){
			//가족형태
			switch($this->family_gbn){
				case 1:
					$this->Text(35.6, 107, 'v');
					break;
				case 2:
					$this->Text(47.3, 107, 'v');
					break;
				case 3:
					$this->Text(65.2, 107, 'v');
					break;
				case 4:
					$this->Text(83.3, 107, 'v');
					break;
				case 9:
					$this->Text(98, 107, 'v');
					break;
			}
		}else {	
			//가족형태
			switch($this->family_gbn){
				case 1:
					$this->Text(35.6, 100.2, 'v');
					break;
				case 2:
					$this->Text(47.3, 100.2, 'v');
					break;
				case 3:
					$this->Text(65.2, 100.2, 'v');
					break;
				case 4:
					$this->Text(83.3, 100.2, 'v');
					break;
				case 9:
					$this->Text(98, 100.2, 'v');
					break;
			}
		}

		// 테두리
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.2);
			$this->Rect($this->left, $this->top, $this->width, 82);
		}else{
			$this->SetY($this->GetY());
		}
	}

}
?>
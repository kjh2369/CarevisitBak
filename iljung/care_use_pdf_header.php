<?php
include_once('../inc/_http_uri.php');
require_once('../pdf/pdf_'.$paper_dir.'.php');

class MYPDF extends MY_PDF{
	var $mode = null;
	var $name = null;
	var $address = null;
	var $svcGbn = null;
	
	function Header(){
		
		if($this->mode == 'statement'){
			$report_kind = '노인장기요양보험법 시행규칙[별지 제3호서식]'; 
			$title = '장기요양급여 이용료 세부내역서';
		}else if($this->mode == 'plan'){
			$report_kind = '노인장기요양보험법 시행규칙[별지 제1호서식]';
			$title = '장기요양급여 이용(제공) 계획서';
		}else {
			$report_kind = '노인장기요양보험법 시행규칙[별지 제4호서식]';
			$title = '장기요양급여 납부확인서';
		}

		$this->SetXY($this->left+5, $this->top+9);
		$this->SetFont('바탕','',11);
		$this->Cell(150,5, $report_kind ,0,1,'L');

		$this->SetXY($this->left, $this->top);
		$this->SetLineWidth(0.6);
		//$this->SetFillColor('255');
		$this->Rect($this->left+5, $this->top+15, $this->width-10, $this->height-45);
		$this->SetLineWidth(0.2);
		
		$this->SetFont($this->font_name_kor, "B", 18);
		$this->SetXY($this->left, $this->top+25);
		$this->Cell($this->width, $this->row_height * 20 / $this->font_size, $title, 0, 1, "C");
		
		$this->SetXY($this->left+15, $this->GetY()+10);
		$this->SetFont($this->font_name_kor, "", 13);
		$this->Cell($this->width * 0.12, $this->row_height, "○ 성      명 :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->name, 0, 1, "L");

		$this->SetXY($this->left+15, $this->GetY()+2);
		$this->Cell($this->width * 0.12, $this->row_height, "○ 주      소 :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->address, 0, 1, "L");
		
		if($this->mode == 'statement' ||
		   $this->mode == 'confirm'   ){
			
			if($this->mode != 'confirm'){
				$this->SetXY($this->left+15, $this->GetY()+2);
				$this->Cell($this->width * 0.12, $this->row_height, "○ 계약기간 :", 0, 0, "C");
				$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->contDt, 0, 1, "L");
			}
			$this->SetXY($this->left+15, $this->GetY()+2);
			$this->Cell($this->width * 0.12, $this->row_height, "○ 이용기간 :", 0, 0, "C");
			$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->useDt, 0, 1, "L");
		}

		$this->SetXY($this->left+15, $this->GetY()+2);
		$this->Cell($this->width * 0.12, $this->row_height, "○ 급여종류 :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, "  □ 방문요양 □ 방문목욕 □ 방문간호 ", 0, 1, "L");

		$this->SetX($this->left+15);
		$this->Cell($this->width * 0.12, $this->row_height, "", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, "  □ 주,야간보호 □ 단기보호", 0, 1, "L");
		
		if($this->mode == 'statement') $y_H = 16;
		if($this->mode == 'confirm') $y_H = 8;

		foreach($this->svcGbn as $svcIdx => $svc){
			if ($svc){
				if (Is_Numeric(StrPos($svc,'_'))){
					$tmp = Explode('_',$svc);
					$svcCd = $tmp[0];
					$subCd = $tmp[1];
				}else{
					$svcCd = $svc;
					$subCd = '';
				}
			}
			
			
			if($svcCd != 'all'){
				if ($svcCd == '200'){
					//방문요양
					$pos_x = 56;
					$pos_y = 84.5 + $y_H;
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}else if ($svcCd == '500'){
					//방문목욕
					$pos_x = 82;
					$pos_y = 84.5 + $y_H;
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}else if ($svcCd == '800'){
					//방문간호
					$pos_x = 108;
					$pos_y = 84.5 + $y_H;	
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}
			}else {
				
				//방문요양
				$pos_x = 56;
				$pos_y = 84.5 + $y_H;
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				
				//방문목욕
				$pos_x = 82;
				$pos_y = 84.5 + $y_H;
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
			
				//방문간호
				$pos_x = 108;
				$pos_y = 84.5 + $y_H;	
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');

			}
		}
		
		$p_year = substr($this->printDT,0,4);
		$p_month = substr($this->printDT,5,2);

		if($this->mode == 'statement'){
			$this->SetXY($this->left+10, $this->GetY()+15);
			$this->MultiCell($this->width*0.9, $this->row_height+1, $this->year."년 ".$this->month."월  장기요양급여 이용내역을 아래와 같이 통보합니다.", 0, "L");
		}else if($this->mode == 'plan'){
			$this->SetXY($this->left+15, $this->GetY()+10);
			$this->MultiCell($this->width*0.9, $this->row_height+1, $this->year."년 ".$this->month."월  장기요양급여 이용(제공)계획을 아래와 같이 통보합니다.", 0, "L");
		}else {
			$this->SetXY($this->left+15, $this->GetY()+10);
			$this->MultiCell($this->width*0.9, $this->row_height+1, '     '.$this->year."년 ".$this->month."월분 장기요양급여비용을 아래와 같이 납부하였기에\n 납부확인서를 발급합니다.", 0, "L");
		}

	}

	function Footer(){
		/*
		if($this->report_id == 'CLTPLANCHN'){
			$this->Text($this->left, $this->highY+6, '·급여계획 변경 시 사유를 상세하게 적어 주세요.');
			$this->Text($this->left, $this->highY+12, '·요양보호사나 대상자의 사유로 시간이 바뀐 경우와 서비스 제공 내용이 바뀐 경우 작성함');	
			
			$this->SetFont($this->font_name_kor, "B", 18);

			$this->SetXY($this->left, 275);
			$this->Cell($this->width, $this->rowHeight,$this->c_nm, 0, 0, 'C');

		}
		
		*/
		
		$this->_drawIcon();

	}
}
?>
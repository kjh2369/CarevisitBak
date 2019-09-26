<?php
require('korean.php');

class MYPDF extends PDF_Korean{
	var $month;

	var $centerAnnuity	= 0;
	var $centerHealth	= 0;
	var $centerOldcare	= 0;
	var $centerEmploy	= 0;
	var $centerSanje	= 0;

	var $left   = 14;
	var $top    = 21;
	var $width  = 270;
	var $height = 168;

	var $rowHeight = 6;
	var $listCount = 25;
	var $totalPage = 0;

	function Header(){
		$detailCol	= $this->detailCol();
		$tempCol	= $this->tempCol();

		// 타이틀
		$this->SetXY($this->left, $this->left);
		$this->SetFont('굴림','B',25);

		$this->Cell($this->width, 5, intVal($this->month).'월 임 금 대 장', 0, 1, 'C');

		$this->SetFont('굴림','',9);

		// 출력일
		$printDate = date('Y.m.d', mkTime());
		$this->Text($this->width - $this->GetStringWidth($printDate), $this->top - 3, '출력일 : '.$printDate);

		// 상세 타이틀
		$this->SetXY($this->left, $this->top);
		$this->Cell($detailCol[0], $this->rowHeight*2, '번호', 1, 0, 'C', true);
		$this->Cell($detailCol[1], $this->rowHeight*2, '성명', 1, 0, 'C', true);
		$this->Cell($detailCol[2], $this->rowHeight*2, '종사업무', 1, 0, 'C', true);
		$this->Cell($detailCol[3], $this->rowHeight*2, ' ', 1, 0, 'C', true);

		$this->Cell($detailCol[4], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[5], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[6], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[7], $this->rowHeight*2, ' ', 1, 0, 'C', true);

		$this->Cell($detailCol[8], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[9], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[10], $this->rowHeight*2, ' ', 1, 0, 'C', true);

		$this->Cell($detailCol[11], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[12], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[13], $this->rowHeight*2, ' ', 1, 0, 'C', true);

		$this->Cell($detailCol[14], $this->rowHeight*2, '지급총액', 1, 0, 'C', true);
		$this->Cell($detailCol[15]+$detailCol[16]+$detailCol[17]+$detailCol[18]+$detailCol[19], $this->rowHeight, '공제내역', 1, 0, 'C', true);
		$this->Cell($detailCol[20], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[21], $this->rowHeight*2, '실수령액', 1, 0, 'C', true);
		$this->Cell($detailCol[22], $this->rowHeight*2, '영수인', 1, 1, 'C', true);

		$this->SetXY($this->left+$tempCol[14], $this->top+$this->rowHeight);

		$this->SetFont('굴림','',8);

		$this->Cell($detailCol[15], $this->rowHeight, '국민연금', 1, 0, 'C', true);
		$this->Cell($detailCol[16], $this->rowHeight, '건강보험', 1, 0, 'C', true);
		$this->Cell($detailCol[17], $this->rowHeight, '장기요양', 1, 0, 'C', true);
		$this->Cell($detailCol[18], $this->rowHeight, '고용보험', 1, 0, 'C', true);
		$this->Cell($detailCol[19], $this->rowHeight, '기타공제', 1, 0, 'C', true);

		// 타이틀
		$this->SetXY($this->left+$tempCol[2]-1, $this->top+2);
		$this->MultiCell($detailCol[3]+2, 4, "근로\n일수", 0, 'C');

		$this->SetXY($this->left+$tempCol[3]-1, $this->top+1);
		$this->MultiCell($detailCol[4]+2, 3.5, "근로\n시간\n수", 0, 'C');

		$this->SetXY($this->left+$tempCol[4]-1, $this->top+1);
		$this->MultiCell($detailCol[5]+2, 3.5, "야간\n근로\n시간", 0, 'C');

		$this->SetXY($this->left+$tempCol[5]-1, $this->top+1);
		$this->MultiCell($detailCol[6]+2, 3.5, "휴일\n근로\n시간", 0, 'C');

		$this->SetXY($this->left+$tempCol[6]-1, $this->top+1);
		$this->MultiCell($detailCol[7]+2, 3.5, "심야\n근로\n시간", 0, 'C');

		$this->SetXY($this->left+$tempCol[7], $this->top+1);
		$this->MultiCell($detailCol[8], 3.5, "야간\n근로\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[8], $this->top+1);
		$this->MultiCell($detailCol[9], 3.5, "휴일\n근로\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[9], $this->top+1);
		$this->MultiCell($detailCol[10], 3.5, "심야\n근로\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[10], $this->top+2);
		$this->MultiCell($detailCol[11], 4, "기타\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[11], $this->top+2);
		$this->MultiCell($detailCol[12], 4, "목욕\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[12], $this->top+2);
		$this->MultiCell($detailCol[13], 4, "간호\n수당", 0, 'C');

		$this->SetXY($this->left+$tempCol[19], $this->top+2);
		$this->MultiCell($detailCol[20], 4, "공제\n총액", 0, 'C');
	}

	function Footer(){
		$detailCol	= $this->detailCol();
		$tempCol	= $this->tempCol();

		// 구분 라인
		$this->SetLineWidth(0.6);
		$this->line($this->left, $this->top+$this->rowHeight*2, $this->width+$this->left, $this->top+$this->rowHeight*2);
		$this->line($this->left+$tempCol[7], $this->top, $this->left+$tempCol[7], $this->top+$this->height);
		$this->line($this->left+$tempCol[14], $this->top, $this->left+$tempCol[14], $this->top+$this->height);
		$this->line($this->left, $this->top+$this->height-$this->rowHeight-1, $this->left+$this->width, $this->top+$this->height-$this->rowHeight-1);
		$this->SetLineWidth(0.2);

		// 전체 테두리
		$this->SetLineWidth(0.6);
		$this->Rect($this->left, $this->top, $this->width, $this->height);
		$this->SetLineWidth(0.2);

		$this->SetFont('굴림','',9);
		$this->SetXY($this->left,-16);

		if ($this->totalPage == $this->PageNo()){
			$this->Cell($this->width/2, 0, '기관 부담 공제금액 : 국민연금('.number_format($this->centerAnnuity).'), 건강보험('.number_format($this->centerHealth).'), 장기요양('.number_format($this->centerOldcare).'), 고용보험('.number_format($this->centerEmploy).'), 산재보험('.number_format($this->centerSanje).')', 0, 0, 'L');
			$this->Cell($this->width/2, 0, 'Page '.$this->PageNo().' / '.$this->totalPage, 0, 1, 'R');
		}else{
			$this->Cell($this->width, 0, 'Page '.$this->PageNo().' / '.$this->totalPage, 0, 1, 'R');
		}
	}

	function detailCol(){
		$detailCol[0]	= 8;	//번호
		$detailCol[1]	= 11;	//성명
		$detailCol[2]	= 16;	//종사업무
		$detailCol[3]	= 7;	//근로일수

		$detailCol[4]	= 9;	//근로시간수
		$detailCol[5]	= 7;	//연장근로시간
		$detailCol[6]	= 7;	//휴일근로시간
		$detailCol[7]	= 7;	//야간근로시간

		$detailCol[8]	= 12;	//연장근로수당
		$detailCol[9]	= 12;	//휴일근로수당
		$detailCol[10]	= 12;	//야간근로수당

		$detailCol[11]	= 13;	//기타수당
		$detailCol[12]	= 13;	//목욕수당
		$detailCol[13]	= 13;	//간호수당

		$detailCol[14]	= 17;	//지급총액

		$detailCol[15]	= 13;	//국민연금
		$detailCol[16]	= 13;	//건강보험
		$detailCol[17]	= 13;	//장기요양보험
		$detailCol[18]	= 13;	//고용보험
		$detailCol[19]	= 13;	//기타공제

		$detailCol[20]	= 13;	//공제총액
		$detailCol[21]	= 17;	//실수령액
		$detailCol[22]	= 11;	//영수인

		return $detailCol;
	}

	function tempCol(){
		$detailCol = $this->detailCol();

		for($i=0; $i<sizeOf($detailCol); $i++){
			$tempCol[$i] = 0;
			for($j=0; $j<=$i; $j++){
				$tempCol[$i] += $detailCol[$j];
			}
		}
		return $tempCol;
	}
}
?>

<?php
//require('korean.php');
require('pdf_'.$page_pl.'.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	var $debug = false;
	var $acctBox = false;
	var $year;	//년도
	var $month;	//월

	var $kind;
	var $name;
	var $jumin;
	var $no;
	var $level;
	var $lvlCd;
	var $rate;

	var $type;
	var $family;
	var $useType;

	var $centerName;
	var $centerTel;

	var $bankName;
	var $bankNo;
	var $bankHolder;

	var $bank_Name;
	var $bank_No;
	var $bank_Depos;

	var $auto_draw_head = true;

	// 페이지설정
	//var $left   = 14;
	//var $top    = 21;
	//var $width  = 182;
	//var $height = 182;

	// 행 높이 설정
	var $rowHeight = 6;

	function Header(){
		if ($this->type == 's'){
			if($this->family == 'W'){
				$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 실적(수급자)';
			}else {
				$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 일정표(수급자)';
			}
		}else if ($this->type == 'c'){
			//$title = intVal($this->year).'년 '.intVal($this->month).'월 수급자 급여제공 계획표';
			$title = intVal($this->year).'년 '.intVal($this->month).'월 장기요양 급여이용(제공) 계획서';
		}else{

			if($this->workGbn == '2'){
				/************************************************
				장애인활동지원일 경우 타이틀 활동보조인으로 변경
				************************************************/
				if($this->family == 'W'){
					$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 실적(활동보조인)';
				}else {
					$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 일정표(활동보조인)';
				}
			}else {
				if($this->family == 'W'){
					$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 실적(요양보호사)';
				}else {
					$title = intVal($this->year).'년 '.intVal($this->month).'월 서비스 일정표(요양보호사)';
				}
			}
		}

		// 타이틀
		//$this->SetXY($this->left, $this->left);
		$this->SetXY($this->left, $this->top);

		//$this->SetFont('바탕','B',15);
		$this->SetFont($this->font_name_kor, "B", 15);

		//$this->Cell($this->width, 5, $title, 0, 1, 'C');

		if ($this->acctBox){
			if ($_SESSION['userCenterCode'] == '1234'){
				$this->Cell($this->width * 0.6, $this->rowHeight * 4, $title, 0, 1, 'C');
				$this->_SignlineSet();
			}else{
				if ($_SESSION['userCenterCode'] == '31141000005' || //어르신을편안하게돌보는사람들
					$_SESSION['userCenterCode'] == '31141000159' ){ //여민복지협동조합
					$this->SetLineWidth(0.2);
					$this->Rect($this->width-$this->left*2, $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.1), $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.2), $this->top, $this->width*0.1, $this->rowHeight * 4);
					//$this->Rect($this->width-($this->left*2+$this->width*0.3), $this->top, $this->width*0.1, $this->rowHeight * 4);

					$this->Line($this->width-($this->left*2+$this->width*0.1)
							  , $this->top + $this->rowHeight * 1.3
							  , $this->width + 5.5
							  , $this->top + $this->rowHeight * 1.3);

					$this->Cell($this->width * 0.6, $this->rowHeight * 4, $title, 0, 1, 'C');

					$this->SetFont($this->font_name_kor, "", 13);
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+9, "결");
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+18, "재");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.1)+(($this->width*0.1 - $this->GetStringWidth("담  당")) / 2), $this->top+5.5, "담  당");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("기관장")) / 2), $this->top+5.5, "기관장");

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//수급자 출력
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "수급자");
					}
				}else if ($_SESSION['userCenterCode'] == 'CN13C003' ){ //아우내
					$this->SetLineWidth(0.2);
					$this->Rect($this->width-$this->left*2, $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.1), $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.2), $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.3), $this->top, $this->width*0.1, $this->rowHeight * 4);

					$this->Line($this->width-($this->left*2+$this->width*0.2)
							  , $this->top + $this->rowHeight * 1.3
							  , $this->width + 5.5
							  , $this->top + $this->rowHeight * 1.3);

					$this->Cell($this->width * 0.6, $this->rowHeight * 4, $title, 0, 1, 'C');

					$this->SetFont($this->font_name_kor, "", 13);
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+9, "결");
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+18, "재");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("담  당")) / 2), $this->top+5.5, "담  당");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("센터장")) / 2), $this->top+5.5, "센터장");

					if ($_SESSION['userCenterCode'] == 'CN13C003'){
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "과  장");
					}
				}else {
					$this->SetLineWidth(0.2);
					$this->Rect($this->width-$this->left*2, $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.1), $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.2), $this->top, $this->width*0.1, $this->rowHeight * 4);
					$this->Rect($this->width-($this->left*2+$this->width*0.3), $this->top, $this->width*0.1, $this->rowHeight * 4);

					$this->Line($this->width-($this->left*2+$this->width*0.2)
							  , $this->top + $this->rowHeight * 1.3
							  , $this->width + 5.5
							  , $this->top + $this->rowHeight * 1.3);

					$this->Cell($this->width * 0.6, $this->rowHeight * 4, $title, 0, 1, 'C');

					$this->SetFont($this->font_name_kor, "", 13);
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+9, "결");
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("결")) / 2), $this->top+18, "재");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("담  당")) / 2), $this->top+5.5, "담  당");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("기관장")) / 2), $this->top+5.5, "기관장");

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//수급자 출력
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "수급자");
					}
				}
			}
		}else{
			$this->Cell($this->width, 10, $title, 0, 1, 'C');
		}

		if ($this->auto_draw_head){
			//$this->SetXY($this->left, $this->top);
			$this->SetXY($this->left, $this->GetY()+2);
			$this->drawHeader();
		}
	}

	function Footer(){
		if ($this->useType == 'y'){
			// 관리자
		}else{
			if ($this->type == 's'){
				// 수급자
			}else if ($this->type == 'y'){
				// 요양보호사
			}else{
			}
		}

		$this->SetXY($this-left, -20);
		$this->SetFont('바탕','B',15);
		$this->Cell($this->left+$this->width, 5, $this->centerName."(".$this->centerTel.")", 0, 1, 'C');

		if($this->centerCode == '24613000160'){
			//여수(큰사랑노인재가복지센터)
		}else {
			if($this->kind == '0'){
				$this->SetXY($this-left, -13);
				$this->SetFont('바탕','',11);
				$this->Cell($this->left+$this->width, 5, "입금계좌:".$this->bankName."(".$this->bankNo.") 예금주:".$this->bankDepos, 0, 1, 'C');
			}
		}
	}

	function drawHeader(){
		$headCol = $this->headColWidth();

		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY()+3);
		}

		$liTop = $this->GetY();

		$this->SetFont('바탕','B',9);
		$this->SetFillColor(220,220,220);
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['t'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C', true);
		}

		$this->SetFont('바탕','',11);
		$this->SetX($this->left);
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['c'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C');
		}

		// 테두리
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.6);
			//$this->Rect($this->left, $this->top, $this->width, $this->rowHeight * 2);
			$this->Rect($this->left, $liTop, $this->width, $this->rowHeight * 2);
			$this->SetLineWidth(0.2);
		}else{
			$this->SetY($this->GetY() - 4);
		}

		$this->Cell($this->width, 3, "", 0, 1);
	}

	function headColWidth(){
		if ($this->type == 's' || $this->type == 'c'){
			$col['w'][0] = $this->width*0.17;
			$col['w'][1] = $this->width*0.25;
			$col['w'][2] = $this->width*0.25;
			$col['w'][3] = $this->width*0.12;
			$col['w'][4] = $this->width*0.21;

			$col['t'][0] = "수급자명";
			$col['t'][1] = "주민등록번호";

			if ($this->kind == '4'){
				$col['t'][2] = "급여종류";
			}else{
				$col['t'][2] = "장기요양인정번호";
			}

			$col['t'][3] = "등급";
			$col['t'][4] = "본인부담율";

			$col['c'][0] = $this->name;
			$col['c'][1] = $this->jumin;

			if ($this->kind == '4'){
				$col['c'][2] = '장애인활동지원';
			}else{
				$col['c'][2] = $this->no;
			}

			$col['c'][3] = $this->level;
			$col['c'][4] = $this->rate;
		}else{
			$col['w'][0] = $this->width*0.20;
			$col['w'][1] = $this->width*0.25;
			$col['w'][2] = $this->width*0.25;
			$col['w'][3] = $this->width*0.30;

			if($this->workGbn == '2'){
				$col['t'][0] = "활동보조인";
				$col['t'][1] = "활동보조인번호";
			}else {
				$col['t'][0] = "요양보호사명";
				$col['t'][1] = "요양보호사번호";
			}

			$col['t'][2] = "연락처";
			$col['t'][3] = "비고";

			$col['c'][0] = $this->name;
			$col['c'][1] = $this->jumin;
			$col['c'][2] = $this->no;
			$col['c'][3] = "";
		}

		return $col;
	}

	function calranderColWidth(){

		$col['w'][0] = $this->width*0.1428;
		$col['w'][1] = $this->width*0.1428;
		$col['w'][2] = $this->width*0.1428;
		$col['w'][3] = $this->width*0.1428;
		$col['w'][4] = $this->width*0.1428;
		$col['w'][5] = $this->width*0.1428;
		$col['w'][6] = $this->width*0.1428;


		$col['t'][0] = '일';
		$col['t'][1] = '월';
		$col['t'][2] = '화';
		$col['t'][3] = '수';
		$col['t'][4] = '목';
		$col['t'][5] = '금';
		$col['t'][6] = '토';

		return $col;
	}
}
?>
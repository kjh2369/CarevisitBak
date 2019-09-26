<?php

if($var['hompageYn'] == 'Y' || $var['mobileYn'] == 'Y'){
	//개인홈페이지에서 출력 시
}else {
	include_once('../inc/_http_uri.php');
}


require_once('../pdf/pdf_'.$paperDir.'.php');

class MYPDF extends MY_PDF{
	var $showForm	= null;
	var $printDT	= null;
	var $orderBY	= null;
	var $svcGbn		= null;
	var $year		= null;
	var $month		= null;
	var $cpIcon		= null;
	var $cpName		= null;
	var $ctIcon		= null;
	var $ctName		= null;
	var $svcCd		= null;
	var $mode		= null;
	var $para		= null;
	var $tempVal	= null;
	var $showGbn    = null;
	var $type		= 1;
	var $domain		= null;
	var $sginCnt	= null;
	var $sginTxt	= null;
	var $debug		= false;


	function Header(){
		if (empty($this->printDT)) $this->printDT = date('Y.m.d', mktime());

		$this->SetTextColor(0,0,0);


		/*************************************
		2012.08.28 출력일자 없앰
		*************************************/
		/*
		if (!empty($this->showForm)){
			$this->SetFont($this->font_name_kor, '', 9);
			$this->SetXY($this->left, 10);
			$this->Cell($this->width, $this->row_height, '출력일자 : '.$this->printDT, 0, 1, 'R');
		}
		*/
		
		//재무회계(세입세출결산서, 총계정원장)
		if($this->subCd == '200'){
			$faTitle = '(방문요양)';
		}else if($this->subCd == '500'){
			$faTitle = '(방문목욕)';
		}else if($this->subCd == '800'){
			$faTitle = '(방문간호)';
		}else if($this->subCd == '900'){
			$faTitle = '(주야간보호)';
		}else if($this->subCd == '300'){
			$faTitle = '(복지용구)';
		}


		if ($this->svcGbn == '200'){
			$subSubject = '(방문요양)';
		}else if ($this->svcGbn == '500'){
			$subSubject = '(방문목욕)';
		}else if ($this->svcGbn == '800'){
			$subSubject = '(방문간호)';
		}else{
			$subSubject = '';
		}

		if ($this->showForm == 'ReceiveBook'){
			$col = $this->_colWidth();

			$this->_showTop2('본인부담금 수납대장'.$subSubject, 20, false);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height, $this->year.'년 '.intval($this->month).'월('.($this->orderBY == '1' ? '수급자별' : '수납일자별').')', 0, 1, 'L');

			$this->SetXY($this->left, $this->GetY());

			$this->Cell($col[0], $this->row_height * 2, 'No', 1, 0, 'C', true);

			if ($this->orderBY == '1'){
				$this->Cell($col[1], $this->row_height * 2, '성명', 1, 0, 'C', true);
				$this->Cell($col[2], $this->row_height * 2, '일자', 1, 0, 'C', true);
			}else{
				$this->Cell($col[2], $this->row_height * 2, '일자', 1, 0, 'C', true);
				$this->Cell($col[1], $this->row_height * 2, '성명', 1, 0, 'C', true);
			}

			$this->Cell($col[3], $this->row_height * 2, '대상자구분', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height * 2, '입금금액', 1, 0, 'C', true);
			$this->Cell($col[5]+$col[6]+$col[7], $this->row_height, '본인부담금', 1, 0, 'C', true);
			$this->Cell($col[8], $this->row_height * 2, '비고', 1, 2, 'C', true);

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4], $this->GetY()-$this->row_height);
			$this->Cell($col[5], $this->row_height, '계', 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, '급여', 1, 0, 'C', true);
			$this->Cell($col[7], $this->row_height, '비급여', 1, 1, 'C', true);

		}else if ($this->showForm == 'ReceiveBook2'){
			$this->left = 20;
			$this->width = 170;
			$this->height = 38;
			$this->row_height = 8.5;

			$col = $this->_colWidth();

			$this->SetFont($this->font_name_kor, '', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height, "■ 노인장기요양보험법 시행규칙[별지 제34호서식] <개정 2013.6.10>", 0, 1, 'L');
			$this->SetX($this->left);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size+7);
			$this->Cell($this->width, $this->row_height * 2.2, "본인부담금 수납대장".$subSubject, "LTR", 1, 'C');
			$this->SetFont($this->font_name_kor, '', $this->font_size+3);
			$this->SetXY($this->left, $this->height);
			$this->Cell($this->width*0.25, $this->row_height, "  ".$this->year."년   ".IntVal($this->month)."월", 1, 0, 'L');
			$this->Cell($this->width*0.75, $this->row_height, $this->ctName.'  ', "TBR", 1, 'R');

			$this->SetFont($this->font_name_kor, 'B', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height * 3, "연번", 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height * 3, "월   일", 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height * 3, "성   명", 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height * 3, "", 1, 0, 'C', true);
			$this->Cell($col[4]+$col[5]+$col[6]+$col[7], $this->row_height * 1, "수납금액(원)", 1, 1, 'C', true);
			$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]);
			$this->Cell($col[4], $this->row_height * 2, "계", 1, 0, 'C', true);
			$this->Cell($col[5], $this->row_height, "급   여", 1, 0, 'C', true);
			$this->Cell($col[6]+$col[7], $this->row_height, "비급여", 1, 1, 'C', true);
			$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]);
			$this->Cell($col[5], $this->row_height, "본인부담금", 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, "금   액", 1, 0, 'C', true);
			$this->Cell(+$col[7], $this->row_height, "항   목", 1, 1, 'C', true);

			$liY = $this->GetY();

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2],$this->GetY()-$this->row_height*2);
			$this->MultiCell($col[3], 5, "대상자\n구분", 0, "C");

			$this->SetXY($this->left,$liY);

		}else if ($this->showForm == 'IssueList'){

			if ($this->sginCnt == 0){

				$this->row_height = 8;

				$col = $this->_colWidth();

				$this->_showTop2($this->year.'년 '.$this->month.'월 장기요양급여비용명세서 발급대장', 20, false);
				$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);

			}else {

				$this->row_height = 6;

				$this->SetFont($this->font_name_kor, 'B', $this->font_size+5);

				$this->SetY($this->GetY()+5);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $this->year.'년 '.$this->month.'월', 0, 1, 'C');
				$this->SetY($this->GetY()-15);
				$this->Cell($this->width * 0.6, $this->row_height * 4,'장기요양급여비용명세서 발급대장', 0, 1, 'C');
				$this->_SignlineSet();


				$this->row_height = 8;
				$this->font_size = 11;

				$col = $this->_colWidth();

				$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);

			}

			$this->SetXY($this->left, $this->GetY());
			$this->Cell($col[0], $this->row_height, '순번', 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height, '영수증 번호', 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height, '수급자', 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height, '구분', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height, '급여총액', 1, 0, 'C', true);
			$this->Cell($col[5], $this->row_height, '본인부담', 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, '발급일자', 1, 0, 'C', true);
			$this->Cell($col[7], $this->row_height, '전달방법', 1, 0, 'C', true);
			$this->Cell($col[8], $this->row_height, '전달일자', 1, 0, 'C', true);
			$this->Cell($col[9], $this->row_height, '수령확인', 1, 1, 'C', true);

			$this->font_size = 9;

		}else if ($this->showForm == 'Iljung'){
			//일정출력
			$this->_showTop2(intval($this->year).'년 '.intval($this->month).'월 서비스 일정표(수급자)', 20, false);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size);

			$col = $this->_colWidth();

			$this->SetXY($this->left, $this->GetY());
			$this->Cell($col[0], $this->row_height, '수급자명', 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height, '주민등록번호', 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height, '장기요양인정번호', 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height, '등급', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height, '본인부담율', 1, 1, 'C', true);

			parse_str($this->para, $val);

			#if ($_SESSION['userCenterCode'] == '1234'){
			#	print_r($val);
			#}



			$this->SetFont($this->font_name_kor, '', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height, $val['name'], 1, 0, 'C');
			$this->Cell($col[1], $this->row_height, $val['jumin'], 1, 0, 'C');
			$this->Cell($col[2], $this->row_height, $val['appno'], 1, 0, 'C');
			$this->Cell($col[3], $this->row_height, $val['level'], 1, 0, 'C');
			$this->Cell($col[4], $this->row_height, $val['kind'].' / '.$val['rate'], 1, 1, 'C');

			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->GetY()-$this->row_height*2, $this->width, $this->row_height*2);
			$this->SetLineWidth(0.2);

			if ($this->type == 1){
				//요일
				$laWeekly = array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');
				$liWidth = $this->width / 7; //요일별 넓이

				$this->SetXY($this->left, $this->GetY()+1);
				$this->SetFont($this->font_name_kor, 'B', $this->font_size);

				//요일
				for($i=0; $i<7; $i++){
					switch($i){
						case 0:
							$this->SetTextColor(255,0,0);
							break;

						case 6:
							$this->SetTextColor(0,0,255);
							break;

						default:
							$this->SetTextColor(0,0,0);
					}
					$this->Cell($liWidth, $this->row_height, $laWeekly[$i], 1, ($i == 6 ? 1 : 0), 'C', true);
				}
			}else if ($this->type == 2){
				$this->_iljungTitle('2');
			}else if ($this->type == 3){
				$this->_iljungTitle('3');
			}

		}else if ($this->showForm == 'DEAL_REPORT'){
			if ($this->direction == 'P'){
				$liTmp1 = 1;
				$liTmp2 = 2.9;
			}else{
				$liTmp1 = 0.6;
				$liTmp2 = 1;
			}

			if ($this->PageNo() == 1){
				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width, $this->row_height, "[별지 제13호 서식] <신설 2012. 12. >", 0, 1, "L");

				/*********************************************************

					기본정보

				 *********************************************************/
				$this->SetFont($this->font_name_kor, "B", $this->font_size*1.7);
				$this->SetX($this->left);
				$this->Cell($this->width*0.7,$this->row_height*3,"요양보호사 처우개선 지급명세서",1,0,"C");

				$liY1 = $this->GetY();

				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->Cell($this->width*0.04,$this->row_height*3,"",1,0,"C");
				$this->Cell($this->width*0.26,$this->row_height*3,"",1,1,"C");

				$liY2 = $this->GetY();

				$this->SetXY($this->left+$this->width*0.7, $liY1+1);
				$this->MultiCell($this->width*0.04, 4, "청\n구\n구\n분", 0, "C");

				$this->SetXY($this->left+$this->width*0.74, $liY1+1);
				$this->MultiCell($this->width*0.26, 5.3, "□ 1. 원청구\n□ 2. 추가청구\n□ 3. 보완청구", 0, "L");

				$this->SetXY($this->left, $liY2);
				$this->Cell($this->width*0.12, $this->row_height*5.2*$liTmp1,"기관기호",1,0,"C");
				$this->Cell($this->width*0.24, $this->row_height*5.2*$liTmp1,$_SESSION['userCenterGiho'],1,0,"C");
				$this->Cell($this->width*0.06, $this->row_height*5.2*$liTmp1,"",1,0,"C");
				$this->Cell($this->width*0.28, $this->row_height*5.2*$liTmp1,"",1,0,"C");
				$this->Cell($this->width*0.04, $this->row_height*2.6*$liTmp1,"",1,2,"C");
				$this->Cell($this->width*0.04, $this->row_height*2.6*$liTmp1,"",1,0,"C");

				$this->SetXY($this->GetX(),$liY2);
				$this->Cell($this->width*0.26, $this->row_height*2.6*$liTmp1,"",1,2,"C");
				$this->Cell($this->width*0.26, $this->row_height*2.6*$liTmp1,"",1,1,"C");

				$liY1 = $this->GetY();

				$this->SetXY($this->left+$this->width*0.42, $liY2+($this->row_height*5.2*$liTmp1-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($this->width*0.28, 5.3, $this->ctName, 0, "C");

				$this->SetXY($this->left+$this->width*0.36, $liY2+($this->row_height*5.2*$liTmp1-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($this->width*0.06, 5.3, "기관\n명칭", 0, "C");

				$this->SetXY($this->left+$this->width*0.70, $liY2+($this->row_height*2.6*$liTmp1-$this->GetStringWidth($this->direction == 'P' ? "▦ ▦ ▦" : "▦▦"))/2);
				$this->MultiCell($this->width*0.04, ($this->direction == 'P' ? 3.5 : 4), ($this->direction == 'P' ? "접\n수\n번\n호" : "접수\n번호"), 0, "C");

				$this->SetXY($this->left+$this->width*0.70, $liY2+$this->row_height*2.6*$liTmp1+($this->row_height*2.6*$liTmp1-$this->GetStringWidth($this->direction == 'P' ? "▦ ▦ ▦" : "▦▦"))/2);
				$this->MultiCell($this->width*0.04, ($this->direction == 'P' ? 3.5 : 4), ($this->direction == 'P' ? "급\n여\n종\n류" : "급여\n종류"), 0, "C");

				/*********************************************************

					지급내용

				 *********************************************************/
				Parse_Str($this->para,$val);

				$this->SetXY($this->left, $liY1);
				$this->SetLineWidth(0.6);
				$this->Line($this->left, $this->GetY(), $this->left+$this->width, $this->GetY());
				$this->SetLineWidth(0.2);

				$this->SetFont($this->font_name_kor, "B", $this->font_size*1.1);
				$this->SetX($this->left);
				$this->Cell($this->width, $this->row_height*1.7, "처우개선 지급내용", 1, 1, "C");

				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->SetX($this->left);
				$this->Cell($this->width*0.12, $this->row_height*1.5, "코드", 1, 0, "C");
				$this->Cell($this->width*0.20, $this->row_height*1.5, "명칭", 1, 0, "C");
				$this->Cell($this->width*0.42, $this->row_height*1.5, "총 급여제공시간", 1, 0, "C");
				$this->Cell($this->width*0.26, $this->row_height*1.5, "총 지급액", 1, 1, "C");

				$this->SetX($this->left);
				$this->Cell($this->width*0.12, $this->row_height*1.5, "", 1, 0, "C");
				$this->Cell($this->width*0.20, $this->row_height*1.5, "", 1, 0, "C");
				$this->Cell($this->width*0.42, $this->row_height*1.5, $val['time'], 1, 0, "C");
				$this->Cell($this->width*0.26, $this->row_height*1.5, $val['pay'], 1, 1, "C");

				$this->SetLineWidth(0.6);
				$this->Line($this->left, $this->GetY(), $this->left+$this->width, $this->GetY());
				$this->SetLineWidth(0.2);
			}else{
				$this->SetXY($this->left,$this->top);
			}

			/*********************************************************

				상세내용

			 *********************************************************/
			$col = $this->_colWidth();

			$this->SetFont($this->font_name_kor, "B", $this->font_size*1.1);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height*1.7, "요양보호사별 처우개선 지급 상세내용", 1, 1, "C");

			$liY3 = $this->GetY()+$this->row_height*1.8;

			$this->SetFont($this->font_name_kor, "", $this->font_size*0.9);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height*($this->direction == 'P' ? 4.7 : 2.8), "", 1, 0, "C");
			$this->Cell($col[1]+$col[2], $this->row_height*1.8, "요양보호사", 1, 0, "C");
			$this->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9], $this->row_height*0.9, "급여", 1, 0, "C");
			$this->Cell($col[10]+$col[11]+$col[12]+$col[13]+$col[14], $this->row_height*1.8, "처우개선내용", 1, 0, "C");
			$this->Cell($col[15], $this->row_height*($this->direction == 'P' ? 4.7 : 2.8), "합계", 1, 1, "C");

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2],$liY3-$this->row_height*0.9);
			$this->Cell($col[3], $this->row_height*0.9, "증감", 1, 0, "C");
			$this->Cell($col[4]+$col[5]+$col[6], $this->row_height*0.9, "전년도", 1, 0, "C");
			$this->Cell($col[7]+$col[8]+$col[9], $this->row_height*0.9, "당년도", 1, 1, "C");

			$liY1 = $this->GetY();

			$this->SetX($this->left+$col[0]);
			$this->Cell($col[1], $this->row_height*$liTmp2, "성명", 1, 0, "C");
			$this->Cell($col[2], $this->row_height*$liTmp2, $this->direction != 'P' ? "주민번호" : "", 1, 0, "C");

			$this->Cell($col[3], $this->row_height*$liTmp2, $this->direction != 'P' ? "청구여부" : "", 1, 0, "C");
			$this->Cell($col[4], $this->row_height*$liTmp2, $this->direction != 'P' ? "급여형태" : "", 1, 0, "C");
			$this->Cell($col[5], $this->row_height*$liTmp2, "시급", 1, 0, "C");
			$this->Cell($col[6], $this->row_height*$liTmp2, $this->direction != 'P' ? "월평균급여" : "", 1, 0, "C");
			$this->Cell($col[7], $this->row_height*$liTmp2, $this->direction != 'P' ? "급여형태" : "", 1, 0, "C");
			$this->Cell($col[8], $this->row_height*$liTmp2, "시급", 1, 0, "C");
			$this->Cell($col[9], $this->row_height*$liTmp2, $this->direction != 'P' ? "월평균급여" : "", 1, 0, "C");

			$this->Cell($col[10], $this->row_height*$liTmp2, $this->direction != 'P' ? "근무시간" : "", 1, 0, "C");
			$this->Cell($col[11], $this->row_height*$liTmp2, $this->direction != 'P' ? "단가" : "", 1, 0, "C");
			$this->Cell($col[12], $this->row_height*$liTmp2, $this->direction != 'P' ? "산출금액" : "", 1, 0, "C");
			$this->Cell($col[13], $this->row_height*$liTmp2, $this->direction != 'P' ? "지급금액" : "", 1, 0, "C");
			$this->Cell($col[14], $this->row_height*$liTmp2, $this->direction != 'P' ? "지급일자" : "", 1, 1, "C");

			if ($this->direction == 'P'){
				$liY2 = $this->GetY();
				$liX1 = $this->left;

				$this->SetXY($liX1, $liY3);
				$this->MultiCell($col[0], 2, "연\n번", 0, "C");

				$liX1 += $col[0];
				$liX1 += $col[1];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦ ▦"))/2);
				$this->MultiCell($col[2], 4, "주민\n등록\n번호", 0, "C");

				$liX1 += $col[2];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[3], 4, "청구\n여부", 0, "C");

				$liX1 += $col[3];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[4], 4, "급여\n형태", 0, "C");

				$liX1 += $col[4];
				$liX1 += $col[5];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[6], 4, "월평균\n급여", 0, "C");

				$liX1 += $col[6];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[7], 4, "급여\n형태", 0, "C");

				$liX1 += $col[7];
				$liX1 += $col[8];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[9], 4, "월평균\n급여", 0, "C");

				$liX1 += $col[9];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[10], 4, "근무\n시간", 0, "C");

				$liX1 += $col[10];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦  "))/2);
				$this->MultiCell($col[11], 2, "단\n가", 0, "C");

				$liX1 += $col[11];
				$liX3  = $liX1;

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[12], 4, "산출\n금액", 0, "C");

				$liX1 += $col[12];
				$liX4  = $liX1;

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[13], 4, "지급\n금액", 0, "C");

				$liX1 += $col[13];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2);
				$this->MultiCell($col[14], 4, "지급\n일자", 0, "C");

				$liY4 = $liY1+($this->row_height*2.9-$this->GetStringWidth("▦ ▦"))/2;

				$this->SetFontSize($this->font_size*0.5);
				$this->Text($liX3+1, $liY4+2, "1)");
				$this->Text($liX4+1.5, $liY4+2, "2)");
				$this->Text($liX1+$col[14]+1.5, $liY4-2, "3)");
				$this->SetFontSize($this->font_size*0.9);

				$this->SetY($liY2);
			}else{
				$liY2 = $this->GetY();
				$liX1 = $this->left;

				$this->SetXY($liX1, $liY2-11.5);
				$this->MultiCell($col[0], 2, "연\n번", 0, "C");

				$liX2 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11];
				$liX3 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11]+$col[12];
				$liX4 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11]+$col[12]+$col[13]+$col[14];

				$this->SetFontSize($this->font_size*0.5);
				$this->Text($liX2+0.5, $liY2-3, "1)");
				$this->Text($liX3+1.5, $liY2-3, "2)");
				$this->Text($liX4+5, $liY2-8.2, "3)");
				$this->SetFontSize($this->font_size*0.9);

				$this->SetY($liY2);
			}

		}else if ($this->showForm == 'ILJUNG_CALN'){
			$this->_header_ILJUNG_CALN();

		}else if ($this->showForm == 'ILJUNG_WEEKLY'){
			$this->_header_ILJUNG_WEEKLY();

		}else if ($this->showForm == 'CLIENT_STATE'){
			$this->_header_CLIENT_STATE();

		}else if ($this->showForm == 'CALN_LIST'){
			$this->_header_CALN_LIST();

		}else if ($this->showForm == 'CALN_WEEKLY'){
			$this->_header_CALN_WEEKLY();

		}else if ($this->showForm == 'HCE'){
			$this->_header_HCE();

		}else if ($this->showForm == 'REPORT2014'){

			if($this->mode == '3'){
				if($this->type == '8_1'){

					//급여계획 변경 일지
					$this->SetFont($this->font_name_kor, "B", 20);
					$this->SetXY($this->left, $this->top);
					$this->Cell($this->width, $this->row_height * 20 / $this->font_size, $this->year."년 급여계획 변경 일지", 0, 1, "C");

					$liY = $this->GetY()+2;
					$this->SetXY($this->left, $liY);
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "수급자명", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.20, $this->row_height * 15 / $this->font_size, $this->name, 1, 0, "C");
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "성별", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.15, $this->row_height * 15 / $this->font_size, $this->gender, 1, 0, "C");
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "생년월일", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.29, $this->row_height * 15 / $this->font_size, $this->birthday, 1, 1, "C");



					$liY = $this->GetY()+5;

					$this->SetFont($this->font_name_kor, "B", 13);

					$this->SetXY($this->left+$this->width * 0.88, $liY);
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->MultiCell($this->width * 0.12, 5, "요양\n보호사\n서명", 1, "C", 1);
					$this->SetFont($this->font_name_kor, "B", 13);

					$colW = $this->width * 0.40;
					$rowY = $this->row_height * 2.5;

					$this->SetXY($this->left, $liY);
					$this->Cell($this->width * 0.08, $rowY, "연번", 1, 0, "C", 1);
					$this->Cell($this->width * 0.20, $rowY, "변경 전", 1, 0, "C", 1);
					$this->Cell($this->width * 0.20, $rowY, "변경 후", 1, 0, "C", 1);
					$this->Cell($this->width * 0.40, $rowY, "변경사유", 1, 1, "C", 1);

				}
			}

		}else if ($this->showForm == 'BUDGET'){
			global $myF;
			
			if ($this->re_gbn == 'R'){
				$title = '세입';
			}else{
				$title = '세출';
			}

			$title .= '예산서('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
			$this->_SignlineSet();
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);
			

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .75, $rowH, '과목', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * .25, $rowH, '관', 1, 0, 'C', 1);
			$this->Cell($this->width * .25, $rowH, '항', 1, 0, 'C', 1);
			$this->Cell($this->width * .25, $rowH, '목', 1, 0, 'C', 1);
			$this->SetXY($this->left + $this->width * .75, $y);
			$this->Cell($this->width * .25, $rowH * 2, '예산액', 1, 1, 'C', 1);
		}else if ($this->showForm == 'SPEC'){
			global $myF;
			
			if ($this->re_gbn == 'R'){
				$title = '세입';
			}else{
				$title = '세출';
			}

			$title .= '명세서('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
			$this->_SignlineSet();
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .52, $rowH, '과목', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * .16, $rowH, '관', 1, 0, 'C', 1);
			$this->Cell($this->width * .16, $rowH, '항', 1, 0, 'C', 1);
			$this->Cell($this->width * .20, $rowH, '목', 1, 0, 'C', 1);

			$this->SetXY($this->left + $this->width * .52, $y);
			$this->Cell($this->width * .12, $rowH * 2, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '증  감', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '산 출 근 거', 1, 1, 'C', 1);

			$y2 = $this->GetY();

			$this->SetXY($this->left + $this->width * .52, $y + 2.5);
			$this->MultiCell($this->width * .12, 4, "전년도\n예산액", 0, 'C');

			$this->SetXY($this->left + $this->width * .64, $y + 2.5);
			$this->MultiCell($this->width * .12, 4, "당해년도\n예산액", 0, 'C');

			$this->SetXY($this->left, $y2);
		}else if ($this->showForm == 'AR'){
			global $myF, $line_name, $sign_cd, $signer, $item, $apprq, $col;
			
			$col[0] = 0.2;
			$col[1] = 0.15;
			$col[2] = 0.07;
			$col[3] = 0.07;
			$col[4] = 0.13;
			$col[5] = 0.13;
			$col[6] = 0.25;

			$title .= '품 의 서';

			$rowH = $this->row_height;

			$this->SetFillColor(234, 234, 234);
			$this->SetY($this->top);
			$link_cd = explode('/', $this->sign_cd);
			
			if ($this->pageNo() == 1){
				
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				
				$x = 127;
				$y = 32;
				
				for($i=0; $i<count($link_cd); $i++){
					$file = '../sign/sign/MEM/'.$_SESSION['userCenterCode'].'/MEM_'.$link_cd[$i].'.jpg';
					
					if(file_exists($file) and is_file($file)){
						$this->Image($file, $x, $y, '20');	//서명
					}

					$x += 17;
				}

				

				$this->_SignlineSet();

				//$this->font['size'] = 21;
				//$this->FontStyle('B');
				//$this->SetXY($this->left, $this->top);
				//$this->Cell($lx, $rowH, $title, false, 1, 'C');

				//$this->font['size'] = 9;
				//$this->FontStyle();

				$rowH = $this->row_height;

				$this->SetXY($this->left, $this->GetY()+2);
				$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

				$this->SetXY($this->left, $this->GetY() + 5);
				$this->Cell($this->width * .07, $rowH, '관', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->gwan_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '지출원', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->exp_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '발의일자', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->mov_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '항', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->hang_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '품의종류', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->ar_type, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '결제일자', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->app_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '목', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->mog_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '자금원천', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->sof_type, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '출납일자', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->rct_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, '', 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '품의금액', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->ar_amt, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '등기일자', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->reg_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .15, $rowH * 5, '원인 및 용도', 1, 0, 'C', 1);
				$this->Cell($this->width * .85, $rowH * 5, '', 1, 1, 'C');

				$y = $this->GetY();

				$this->SetXY($this->left + $this->width * .15, $y - $rowH * 5);
				$this->MultiCell($this->width * .85, 4, $this->cause, 0);

				$this->SetXY($this->left, $y + 2.2);
				$this->MultiCell($this->width, 4, "상기의 원인 및 용도로 아래와 같이 품의하고자 하오니 ".$this->per_dt." 까지\n지출 할 수 있도록 하여 주시길 바랍니다.", 0, 'C');

				$this->SetXY($this->left, $y);
				$this->Cell($this->width, $rowH * 2, '', 1, 1);
			}else{
				$lx = $this->width;
			}

			$this->SetXY($this->left, $this->GetY() + 2);
			$this->Cell($this->width * $col[0], $rowH, '품목', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '규격', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '단위', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[3], $rowH, '수량', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH, '단가', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH, '금액', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[6], $rowH, '비고', 1, 1, 'C', 1);	
		}else if ($this->showForm == 'BS'){
			global $myF, $year, $re_gbn, $line_name, $sign_cd, $col;

	
			$col[0] = 0.13;
			$col[1] = 0.13;
			$col[2] = 0.13;
			$col[3] = 0.05;
			$col[4] = 0.14;
			$col[5] = 0.14;
			$col[6] = 0.14;
			$col[7] = 0.14;

			
			if ($re_gbn == 'R'){
				$title = $this->year.' 세입';
			}else{
				$title = $this->year.' 세출';
			}
			$title .= ' 결산서'.$faTitle;
			
			$rowH = $this->row_height;

			$this->SetY($this->top);

			
			$link_cd = explode('/', $this->sign_cd);
			
			if ($this->pageNo() == 1){
				//$lx = $this->AppLineSign($line_name, $sign_cd, $signer, -5) - $this->left;
				
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				
				$x = 127;
				$y = 32;
				
				for($i=0; $i<count($link_cd); $i++){
					$file = '../sign/sign/MEM/'.$_SESSION['userCenterCode'].'/MEM_'.$link_cd[$i].'.jpg';
					
					if(file_exists($file) and is_file($file)){
						$this->Image($file, $x, $y, '20');	//서명
					}

					$x += 17;
				}

				$this->_SignlineSet();

			}else{
				$lx = $this->width;
			}

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * ($col[0]+$col[1]+$col[2]), $rowH, '과목', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * $col[0], $rowH, '관', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '항', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '목', 1, 0, 'C', 1);

			$this->SetXY($this->left + $this->width * ($col[0]+$col[1]+$col[2]), $y);
			$this->Cell($this->width * $col[3], $rowH * 2, '구분', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH * 2, $re_gbn == 'R' ? '정부보조금' : '보조금', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH * 2, '시설부담금', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[6], $rowH * 2, '후원금', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[7], $rowH * 2, '합계', 1, 1, 'C', 1);

		}else if ($this->showForm == 'GL'){
			global $myF, $year, $line_name, $sign_cd, $col;

			$col[0] = 0.2;
			$col[1] = 0.13;
			$col[2] = 0.31;
			$col[3] = 0.12;
			$col[4] = 0.12;
			$col[5] = 0.12;
			
			
			$title .= '총계정원장'.$faTitle;

			$rowH = $this->row_height;

			$this->SetY($this->top);

			/*if ($this->pageNo() == 1){
			}else{
				$lx = $this->width;
			}*/
			$this->_SignlineSet();

			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($lx, $rowH, '계정과목 : '.$this->acct_name, false, 1);

			$this->SetX($this->left);
			$this->Cell($this->width * $col[0], $rowH, '계정명', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '일자', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '적요', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[3], $rowH, '수입', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH, '지출', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH, '차인잔액', 1, 1, 'C', 1);
		}else if ($this->showForm == 'ACCTBK'){
			global $myF, $year, $month, $line_name, $sign_cd;

			$title .= '현금출납부('.$this->year.'년 '.$this->month.'월)';

			$rowH = $this->row_height;

			$this->SetY($this->top);

			/*if ($this->pageNo() == 1){
			}else{
				$lx = $this->width;
			}*/
			$this->_SignlineSet();

			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '시설명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .11, $rowH, '연월일', 1, 0, 'C', 1);
			$this->Cell($this->width * .2, $rowH, '계정과목', 1, 0, 'C', 1);
			$this->Cell($this->width * .3, $rowH, '적요', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '수입금액', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '지출금액', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '차인잔액', 1, 1, 'C', 1);
		}else if ($this->showForm == 'SALARY_1'){
			
			global $myF, $year, $month;
			
			if($this->subCd == '200'){
				$subTitle = '(방문요양)';
			}else if($this->subCd == '500'){
				$subTitle = '(방문목욕)';
			}else if($this->subCd == '800'){
				$subTitle = '(방문간호)';
			}

			$title = $this->year.'년 '.$this->month.'월 임ㆍ직원보수'.$subTitle.'일람표';
			
			$this->SetY($this->top);
			$this->SetFont($this->font_name_kor, "B", 21);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->top);
			$this->Cell($lx, $rowH, $title, false, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height * 1.5;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '시설명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY();

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($this->width * .05, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .15, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '성명', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '본봉', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '각종수당', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '계', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '공제액', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '차감지급액', 1, 1, 'C', 1);

			$this->SetXY($this->left, $y + .9);
			$this->MultiCell($this->width * .05, 4, "순\n번", 0, 'C');
			$this->SetXY($this->left + $this->width * .05, $y + .9);
			$this->MultiCell($this->width * .13, 4, "직종 또는\n직위(급)", 0, 'C');
			$this->SetXY($this->left, $y + $rowH);
			
		}else if ($this->showForm == 'SALARY_2'){
			global $myF, $org_name, $year, $month;
			
			if($this->subCd == '200'){
				$subTitle = '(방문요양)';
			}else if($this->subCd == '500'){
				$subTitle = '(방문목욕)';
			}else if($this->subCd == '800'){
				$subTitle = '(방문간호)';
			}

			$title = $this->year.'년 '.$this->month.'월 임직원 보수'.$subTitle.' 일람표(인건비명세서)';

			$this->SetY($this->top);
			$this->SetFont($this->font_name_kor, "B", 21);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->top);
			$this->Cell($lx, $rowH, $title, false, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height * 1.5;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '시설명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY();

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($this->width * .04, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '직종', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '인건비 구분', 1, 0, 'C', 1);
			$this->Cell($this->width * .09, $rowH, '성명', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '급여', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '각종 수당', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '일용잡급', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '계', 1, 1, 'C', 1);

			$this->SetXY($this->left, $y + .9);
			$this->MultiCell($this->width * .04, 4, "순\n번", 0, 'C');
			$this->SetXY($this->left + $this->width * .7, $y + .9);
			$this->MultiCell($this->width * .1, 4, "퇴지금 및\n퇴직적립금", 0, 'C');
			$this->SetXY($this->left + $this->width * .8, $y + .9);
			$this->MultiCell($this->width * .1, 4, "사회보험\n부담금", 0, 'C');
			$this->SetXY($this->left, $y + $rowH);
		}else if ($this->showForm == 'BUDGET_R'){
			global $myF;

			$title = '예산서 ('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			if($this->PageNo() == 1){
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				$this->_SignlineSet();
			}
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '기관명 : '.$myF->euckr($_SESSION['userCenterName']), false, 1);
			
			$this->SetFont($this->font_name_kor, "", 9);

			$y = $this->GetY() + 5;
			$this->SetXY($this->left, $this->getY());
			
			$this->SetFillColor(234, 234, 234);
				
			if($this->PageNo() == 1){
				$this->Cell($this->width, $rowH, '예산서 수입', 1, 1, 'C', 1);
				$this->SetX($this->left);
				$this->Cell($this->width * .09, $rowH, '서비스', 1, 0, 'C', 1);
				$this->Cell($this->width * .16, $rowH, '분류', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '개월수', 1, 0, 'C', 1);
				$this->Cell($this->width * .09, $rowH, '단가', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '일반건수', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '가족건수', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '합 계', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '공단부담금', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '개인부담금', 1, 1, 'C', 1);
			}else {
				$this->Cell($this->width, $rowH, '예산서 지출', 1, 1, 'C', 1);
	
				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '순번', 1, 0, 'C', 1);
				$this->Cell($this->width * .25, $rowH, '항목', 1, 0, 'C', 1);
				$this->Cell($this->width * .18, $rowH, '금액', 1, 0, 'C', 1);
				$this->Cell($this->width * .20, $rowH, $var['monthCnt'].'개월 x 금액', 1, 0, 'C', 1);
				$this->Cell($this->width * .30, $rowH, '비고', 1, 1, 'C', 1);
			}

		}else{
		}

		if ($this->showForm == 'SW_WORK_LOG'){
			//$this->_header_SW_WORK_LOG();
			$this->_header_SW_WORK_LOG_24ho();
		}

		//업무수행일지 2회방문출력
		if ($this->showForm == 'SW_WORK_LOG2'){
			$this->_header_SW_WORK_LOG2();
		}

		//업무수행일지 결재(사회복지사 보고서)
		if ($this->showForm == 'SW_WORK_LOG_SIGN'){
			$this->_header_SW_WORK_LOG_SIGN();
		}
	}

	function Footer(){
		if ($this->showForm == 'REPORT_INSU' ||
			$this->showForm == 'CALN_WEEKLY'){
			return;
		}

		if ($this->showForm == 'SW_WORK_LOG'){
			parse_str($this->para, $val);
			$this->SetXY($this-left, -20);
			$this->SetFont($this->font_name_kor,'B',15);
			$this->Cell($this->left+$this->width, 5, $this->ctName."(".$val['phone'].")", 0, 1, 'C');
			return;
		}

		if ($this->showForm == 'HCE'){
			if ($this->mode == '71') return; //이용 안내 및 동의서
			if ($this->mode == '92') return; //연계 및 의뢰서
			if ($this->mode == '121') return; //서비스 종결 안내서

			$this->SetXY($this-left, -10);
			$this->SetFont($this->font_name_kor,'B',10);
			$this->Cell($this->left+$this->width, 5, $this->ctName, 0, 1, 'C');

			if ($this->mode == '21') return;
		}

		if ($this->showForm == 'ReceiveBook2'){
			$this->SetXY($this->left, -20);
			$this->Cell($this->width, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'C');

			if($this->domain == 'dolvoin.net'){
				$this->_drawIcon();
			}

			return;

		}else if ($this->showForm == 'ReceiveBook'){

			if($this->domain == 'dolvoin.net'){
				$this->_drawIcon();
			}

			return;

		}else if ($this->showForm == 'IssueList'){

			if($this->domain == 'dolvoin.net'){

				$this->SetXY($this->left, -20);
				$this->SetFont($this->font_name_kor,'B',20);
				$this->Cell($this->width, 5, $this->ctName, 0, 1, 'C');

				if($this->icon != ''){
					$this->_drawIcon();
				}
			}

			return;

		}else if ($this->showForm == 'DEAL_REPORT'){
			$this->SetLineWidth(0.6);

			if ($this->PageNo() == 1){
				$this->Rect($this->left, $this->top+$this->row_height, $this->width, $this->height-$this->top-1);
			}else{
				$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top+$this->row_height-1);
			}

			$this->SetLineWidth(0.2);

		}else if ($this->showForm == 'REPORT2014'){
			if($this->mode == '3'){
				if($this->type == '8_1'){
					if($this->report_id == 'CLTPLANCHN'){
						$this->Text($this->left, $this->highY+6, '·급여계획 변경 시 사유를 상세하게 적어 주세요.');
						$this->Text($this->left, $this->highY+12, '·요양보호사나 대상자의 사유로 시간이 바뀐 경우와 서비스 제공 내용이 바뀐 경우 작성함');

						$this->SetFont($this->font_name_kor, "B", 18);

						$this->SetXY($this->left, 275);
						$this->Cell($this->width, $this->rowHeight,$this->c_nm, 0, 0, 'C');

					}
				}
			}

		}else{
		}

		if ($this->showForm == 'DEAL_REPORT'){
			$this->SetXY($this->left, -20);
			$this->SetFont($this->font_name_kor, "", $this->font_size);

			if ($this->direction == 'P'){
				$this->Cell($this->width, $this->row_height, "1) 급여제공시간에 따라 전산에서 자동으로 산출된 금액    2) 기관이 종사자에게 실 지급한 금액",0,1);
				$this->SetX($this->left);
				$this->Cell($this->width, $this->row_height, "3) 월급여와 처우개선 지급금액을 합한 금액",0,1);
				$this->SetXY($this->left, -7);
			}else{
				$this->Cell($this->width, $this->row_height, "1) 급여제공시간에 따라 전산에서 자동으로 산출된 금액        2) 기관이 종사자에게 실 지급한 금액        3) 월급여와 처우개선 지급금액을 합한 금액",0,1);
				$this->SetXY($this->left, -13);
			}
			$this->Cell($this->width, $this->row_height, "- ".$this->PageNo()." -", 0, 0, 'C');

		}else if ($this->showForm == 'ILJUNG_CALN'){
			parse_str($this->para, $val);

			$this->SetXY($this-left, -20);
			$this->SetFont($this->font_name_kor,'B',15);
			$this->Cell($this->left+$this->width, 5, $this->ctName."(".$val['phone'].")", 0, 1, 'C');

			if ($this->mode == '101'){
				if ($_SESSION['userCenterCode'] == '24613000160' || //여수(큰사랑노인재가복지센터)
				   ($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //사회적협동조합 도우누리

				}else {
					$this->SetXY($this-left, -13);
					$this->SetFont($this->font_name_kor,'',11);
					$this->Cell($this->left+$this->width, 5, $val[$this->svcCd], 0, 1, 'C');
				}

				if ($this->tempVal){
					$this->SetFont($this->font_name_kor,'',11);
					$this->SetXY($this->left, -12);
					$this->MultiCell($this->width, 4, $this->tempVal[$this->svcCd]['str']);
				}
			}else if ($this->mode == '102'){
				if ($this->tempVal){
					$this->SetFont($this->font_name_kor,'',11);
					$this->SetXY($this->left, -12);
					$this->MultiCell($this->width, 4, $this->tempVal['M']['str']);
				}
			}

		}else if ($this->showForm == 'ILJUNG_WEEKLY'){
		}else if ($this->showForm == 'REPORT2014'){
		}else if ($this->showForm == 'SW_WORK_LOG_SIGN'){
		}else if ($this->showForm == 'BUDGET' || $this->showForm == 'SPEC'){
			$this->Line($this->left, $this->GetY(), $this->width, $this->GetY());
			$this->AliasNbPages();
			$this->SetLineWidth(0.2);
			$this->line(0,285,210,285);
			$this->SetY(-12);
			$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}else if ($this->showForm == 'BS'){
			$this->Line($this->left, $this->GetY(), $this->width, $this->GetY());
			$this->SetLineWidth(0.2);
			$this->line(0,285,210,285);
			$this->SetY(-12);
			$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}else{
			if (!empty($this->showForm)){
				if ($this->showForm == 'HCE'){
					$this->SetFont($this->font_name_kor, 'B', $this->font_size);
					$this->SetXY($this->left, -10);
					$this->Cell($this->width+7, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'R');
				}else{
					$this->SetLineWidth(0.6);
					$this->Line($this->left, $this->height + 5, $this->left + $this->width, $this->height + 5);
					$this->SetLineWidth(0.2);

					$this->SetFont($this->font_name_kor, 'B', $this->font_size);
					$this->SetXY($this->left, -20);
					$this->Cell($this->width, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'C');
				}
			}

			//$this->_drawIcon();
		}
	}



	/*********************************************************

		col width

	*********************************************************/
	function _colWidth(){
		if ($this->showForm == 'ReceiveBook'){
			$col[0] = $this->width * 0.06;
			$col[1] = $this->width * 0.10;
			$col[2] = $this->width * 0.13;
			$col[3] = $this->width * 0.13;
			$col[4] = $this->width * 0.12;
			$col[5] = $this->width * 0.12;
			$col[6] = $this->width * 0.12;
			$col[7] = $this->width * 0.12;
			$col[8] = $this->width * 0.10;

		}else if ($this->showForm == 'ReceiveBook2'){
			$col[0] = $this->width * 0.07;
			$col[1] = $this->width * 0.11;
			$col[2] = $this->width * 0.13;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.17;
			$col[5] = $this->width * 0.14;
			$col[6] = $this->width * 0.14;
			$col[7] = $this->width * 0.14;

		}else if ($this->showForm == 'IssueList'){
			$col[0] = $this->width * 0.04;
			$col[1] = $this->width * 0.16;
			$col[2] = $this->width * 0.09;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.11;
			$col[5] = $this->width * 0.10;
			$col[6] = $this->width * 0.11;
			$col[7] = $this->width * 0.09;
			$col[8] = $this->width * 0.11;
			$col[9] = $this->width * 0.09;

		}else if ($this->showForm == 'Iljung'){
			$col[0] = $this->width * 0.20;
			$col[1] = $this->width * 0.25;
			$col[2] = $this->width * 0.25;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.20;

		}else if ($this->showForm == 'DEAL_REPORT'){
			if ($this->direction == 'P'){
				$col[0] = $this->width * 0.03;
				$col[1] = $this->width * 0.05;
				$col[2] = $this->width * 0.06;

				$col[3] = $this->width * 0.06;
				$col[4] = $this->width * 0.06;
				$col[5] = $this->width * 0.06;
				$col[6] = $this->width * 0.08;
				$col[7] = $this->width * 0.06;
				$col[8] = $this->width * 0.06;
				$col[9] = $this->width * 0.08;

				$col[10] = $this->width * 0.06;
				$col[11] = $this->width * 0.03;
				$col[12] = $this->width * 0.07;
				$col[13] = $this->width * 0.08;
				$col[14] = $this->width * 0.08;
				$col[15] = $this->width * 0.08;
			}else{
				$col[0] = $this->width * 0.02;
				$col[1] = $this->width * 0.05;
				$col[2] = $this->width * 0.09;

				$col[3] = $this->width * 0.06;
				$col[4] = $this->width * 0.06;
				$col[5] = $this->width * 0.05;
				$col[6] = $this->width * 0.07;
				$col[7] = $this->width * 0.06;
				$col[8] = $this->width * 0.05;
				$col[9] = $this->width * 0.07;

				$col[10] = $this->width * 0.06;
				$col[11] = $this->width * 0.05;
				$col[12] = $this->width * 0.07;
				$col[13] = $this->width * 0.08;
				$col[14] = $this->width * 0.08;
				$col[15] = $this->width * 0.08;
			}
		}else if ($this->showForm == 'HCE'){
			if ($this->mode == '1'){
				$col[] = $this->width*0.04;	//연번
				$col[] = $this->width*0.04;	//접수방법
				$col[] = $this->width*0.09;	//접수일자
				$col[] = $this->width*0.10;	//대상자명
				$col[] = $this->width*0.10;	//대상자주소
				$col[] = $this->width*0.37;	//상담내용
				$col[] = $this->width*0.10;	//의뢰인
				$col[] = $this->width*0.10;	//접수자
				$col[] = $this->width*0.06;	//초기면접
			}else if ($this->mode == '51'){
				//사례회의록
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.22;
			}else if ($this->mode == '61'){
				//서비스계획서
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.66;
			}else if ($this->mode == '81'){
				//과정상담일지
				$col[] = $this->width * 0.13;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.57;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
			}else if ($this->mode == '91' || $this->mode == '92'){
				//서비스 연계 및 의뢰서
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.18;
				$col[] = $this->width * 0.08;
				$col[] = $this->width * 0.18;
				$col[] = $this->width * 0.15;
				$col[] = $this->width * 0.31;
			}else{
				return null;
			}

		}else{
			return null;
		}

		return $col;
	}

	//서비스일정표(실적) 타이틀
	function _iljungTitle($asType){
		$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);
		$this->SetXY($this->left,$this->GetY()+2);

		if ($asType == '2'){
			$this->Cell($this->width*0.12,$this->row_height,'제공서비스',1,0,'C',true);
			$this->Cell($this->width*0.12,$this->row_height,'직원명',1,0,'C',true);
			$this->Cell($this->width*0.71,$this->row_height,'제공일',1,0,'C',true);
			$this->Cell($this->width*0.05,$this->row_height,'횟수',1,1,'C',true);
		}else if ($asType == '3'){
			$this->Cell($this->width*0.10,$this->row_height,'급여종류',1,0,'C',true);
			$this->Cell($this->width*0.20,$this->row_height,'서비스명',1,0,'C',true);
			$this->Cell($this->width*0.10,$this->row_height,'횟수',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'시간',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'수가',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'총급여비용',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'본인부담액',1,1,'C',true);
		}

		$this->SetFont($this->font_name_kor, '', $this->font_size-2);
	}

	//일정표출력 헤더
	function _header_ILJUNG_CALN(){
		$subject = IntVal($this->year).'년 '.IntVal($this->month).'월 ';

		parse_str($this->para, $val);

		//if($this->debug){
			//	$this->_drawJikin();
		//}

		if($this->domain == 'dolvoin.net'){
			if($this->icon != ''){
				$exp = explode('.',$this->icon);
				$exp = strtolower($exp[sizeof($exp)-1]);
				if($exp != 'bmp'){
					$this->Image('../mem_picture/'.$this->icon, 180, 10, 20, null);	//기관 로고
				}
			}
		}


		if ($this->mode == '101'){

			if ($this->showGbn == 'conf'){
				$subject .= '서비스 실적(수급자)';
			}else{
				if (($this->svcCd == '2' || $this->svcCd == '4') && $_SESSION['userCenterCode'] == '1234'){
					$subject = '서비스 일정표('.IntVal($this->year).'년 '.IntVal($this->month).'월 )';
				}else {
					$subject .= '서비스 일정표(수급자)';
				}
			}
		}else if ($this->mode == '102'){
			if ($this->svcCd == '4'){
				$str = '활동보조인';
			}else{
				$str = '요양보호사';
			}
			if ($this->showGbn == 'conf'){
				$subject .= '근무현황 실적('.$str.')';
			}else{
				$subject .= '근무현황 일정표('.$str.')';
			}
		}else{
			$subject .= '상담지원';
		}

		if($this->mode == '101' && $_SESSION['userCenterCode'] == '1234' && ($this->svcCd == '2' || $this->svcCd == '4')){
			$top = 15;
		}

		$this->SetXY($this->left, $this->top-$top);
		$this->SetFont($this->font_name_kor, "B", 15);

		if (($_SESSION['userCenterCode'] == '1234' && $this->mode == '101' && ($this->svcCd == '2' || $this->svcCd == '4'))){ //사회적협동조합 도우누리
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
	    }else if (($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //사회적협동조합 도우누리
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
		}else if (($_SESSION['userCenterCode'] == '24511000073' && $this->mode == '101')){ //엠마오
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
		}else{
			if (is_null($this->sginCnt)){
				$liWidth	= 196;
				$liLeft		= 7;

				if (($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //사회적협동조합 도우누리
					$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
				}else{
					if ($_SESSION['userCenterCode'] == '32820000004'){
						$tmpStr	= '대  표';
					}else if ($_SESSION['userCenterCode'] == '24824000066' ||	//100세돌봄
							  $_SESSION['userCenterCode'] == 'CN13C003'    ||   //아우내노인복지센터
							  $_SESSION['userCenterCode'] == '24872000003' ||   //의령노인복지센터
							  $_SESSION['userCenterCode'] == '34872000051' ||	//의령노인복지센터
							  $_SESSION['userCenterCode'] == '32915500129' ){ //나누리노인복지센터
						$tmpStr	= '센터장';
					}else if ($_SESSION['userCenterCode'] == '24413000019'){
						//천안노인종합복지관
						$tmpStr	= '부  장';
					}else if ($_SESSION['userCenterCode'] == '34211000101'){
						//강원재가복지센터
						$tmpStr	= '원  장';
					}else{
						$tmpStr	= '기관장';
					}

					$this->SetLineWidth(0.2);
					$this->Rect($this->width-$liLeft*2, $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.1), $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.2), $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.3), $this->top, $liWidth*0.1, $this->row_height * 4);

					$this->Line($this->width-($liLeft*2+$liWidth*0.2)
							  , $this->top + $this->row_height * 1.3
							  , $this->width + 5.5
							  , $this->top + $this->row_height * 1.3);

					$this->Cell($this->width * 0.6, $this->row_height * 4, $subject, 0, 1, 'C');

					$this->SetFont($this->font_name_kor, "", 13);
					$this->Text($this->width-($liLeft*2+$liWidth*0.3)+(($liWidth*0.1 - $this->GetStringWidth("결")) / 2), $this->top+9, "결");
					$this->Text($this->width-($liLeft*2+$liWidth*0.3)+(($liWidth*0.1 - $this->GetStringWidth("결")) / 2), $this->top+18, "재");

					if ($_SESSION['userCenterCode'] == '24872000003' ||
						$_SESSION['userCenterCode'] == '34872000051' ){
						//의령노인복지센터
						$this->SetFont($this->font_name_kor, "", 11);
						$this->Text($this->width-($liLeft*2+$liWidth*0.2)+(($liWidth*0.1 - $this->GetStringWidth("요양보호사")) / 2), $this->top+5.5, "요양보호사");
						$this->Text($this->width-$liLeft*2+(($liWidth*0.1 - $this->GetStringWidth($tmpStr)) / 2), $this->top+5.5, $tmpStr);
					}else {
						$this->SetFont($this->font_name_kor, "", 11);
						$this->Text($this->width-($liLeft*2+$liWidth*0.2)+(($liWidth*0.1 - $this->GetStringWidth("담  당")) / 2), $this->top+5.5, "담  당");
						$this->Text($this->width-$liLeft*2+(($liWidth*0.1 - $this->GetStringWidth($tmpStr)) / 2), $this->top+5.5, $tmpStr);
					}

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//수급자 출력
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "수급자");
					}else if ($_SESSION['userCenterCode'] == '24824000066'){
						//100세돌봄
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "실  장");
					}else if ($_SESSION['userCenterCode'] == '24413000019' || //천안노인종합복지관
							  $_SESSION['userCenterCode'] == 'CN13C001'	   || //느티나무노인복지센터
							  $_SESSION['userCenterCode'] == 'CN13C003'    ){ //아우내노인복지센터

						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "과  장");
					}else if ($_SESSION['userCenterCode'] == '24872000003' ||
							  $_SESSION['userCenterCode'] == '34872000051' ){
						//의령노인복지센터
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "관리자");
					}else if ($_SESSION['userCenterCode'] == '24213000019'){
						//명륜재가노인복지센터
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "팀  장");
					}else if ($_SESSION['userCenterCode'] == '34211000101'){
						//강원재가복지센터
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "국  장");
					}else if ($_SESSION['userCenterCode'] == '24420000005' || //온양노인복지센터
							  $_SESSION['userCenterCode'] == '32915500129' ){ //나누리노인복지센터

						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+1, $this->top+5.5, "사무국장");
					}else if ($_SESSION['userCenterCode'] == '31129000140'){
						//비지팅엔젤스(성북)
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)-1, $this->top+5.5, "관리책임자");
					}else if ($_SESSION['userCenterCode'] == 'KN88C002'){
						//거창인애노인통합지원센터
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)-1, $this->top+5.5, "사회복지사");
					}
				}
			}else{
				if ($this->sginCnt == 0){
					$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
				}else{
					$this->Cell($this->width * 0.6, $this->row_height * 4, $subject, 0, 1, 'C');
					$this->_SignlineSet();
				}
			}
		}

		$this->SetY($this->GetY()+3);
	}


	//주간별 일정표출력 헤더
	function _header_ILJUNG_WEEKLY(){

		parse_str($_POST['para'], $val);

		//일정표
		$col['calnWidth'][0]	= $this->width*0.1428;
		$col['calnWidth'][1]	= $this->width*0.1428;
		$col['calnWidth'][2]	= $this->width*0.1428;
		$col['calnWidth'][3]	= $this->width*0.1428;
		$col['calnWidth'][4]	= $this->width*0.1428;
		$col['calnWidth'][5]	= $this->width*0.1428;
		$col['calnWidth'][6]	= $this->width*0.1428;


		$cname   = iconv("UTF-8","EUC-KR", $_SESSION['userCenterKindName'][0]);	//기관명
		$yymm    = substr(str_replace('.','', $val['from']),0,6);				//년월
		$year    = substr($yymm, 0,4);											//년
		$month   = substr($yymm, 4,2);											//월

		$fromDay = intval(substr($val['from'],8,2));							//시작일자
		$toDay = intval(substr($val['to'],8,2));								//종료일자
		$calTime	= mktime(0, 0, 1, $month, 1, $year);
		//$today		= date('Ymd', mktime());
		$lastDay	= date('t', $calTime);										//총일수 구하기
		$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//시작요일 구하기

		$subject = '주간 서비스일정표 ('.$val['from'].'~'.$val['to'].')';

		$this->SetXY($this->left, $this->top-10);
		$this->SetFont($this->font_name_kor, "B", 15);

		$this->Cell($this->width, $this->row_height * 4, $cname, 0, 1, 'C');

		$this->SetXY($this->left, $this->top-2);
		$this->SetFont($this->font_name_kor, "B", 13);

		$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'L');

		$this->SetXY($this->left, $this->top+14);
		$this->SetFont($this->font_name_kor,'B',10);

		$k = 0;

		$weekly = array("일","월","화","수","목","금","토");

		for($i=$fromDay; $i<=$toDay; $i++){

			if($i == 1){
				for($j=0; $j<$startWeek; $j++){
					$this->Cell($col['calnWidth'][$k], $this->row_height, "", 1, $k < 6 ? 0 : 1, 'C', true);
				}
			}

			$day = ($i < 10 ? '0' : '').$i;

			$date = $yymm.$day;


			//요일
			$w = date('w', strtotime($date));
			$week = $weekly[$w];

			if ($week == '일'){//일요일
				$this->SetTextColor(255,0,0); //붉은색
			}else if ($week == '토'){//토요일
				$this->SetTextColor(0,0,255); //파란색
			}else{//평일
				$this->SetTextColor(0,0,0); //검정색
			}
			$this->Cell($col['calnWidth'][$k], $this->row_height, Number_Format($day).'('.$week.')', 1,  $w == 6 ? 1 : 0, 'C', true);

			$k++;
		}

		$empty = 6-$k;
		if($empty>0){
			for($i=0; $i<=$empty; $i++){
				$this->Cell($this->width*0.1428, $this->row_height, '', 1, $i == $empty ? 1 : 0 , 'C', true);
			}
		}

		$this->tempVal = $this->GetY();
	}

	function _header_CLIENT_STATE(){
		parse_str($this->para, $val);

		$cname   = iconv("UTF-8","EUC-KR", $_SESSION['userCenterKindName'][0]);	//기관명

		$subject = $this->year.'년 '.intval($this->month).'월 수급자현황(재가요양)';

		$this->SetXY($this->left, $this->top-10);
		$this->SetFont($this->font_name_kor, "B", 18);

		$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');


		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor, "B", 15);

		$this->Cell($this->width, $this->row_height * 4, $cname, 0, 1, 'L');
		$height = $this->row_height;

		$this->SetFont($this->font_name_kor, "B", 11);

		$this->SetX($this->left);
		$this->Cell($this->width*0.04,$height,'순번',1,0,'C',true);
		$this->Cell($this->width*0.07,$height,'성명',1,0,'C',true);
		$this->Cell($this->width*0.125,$height,'주민번호',1,0,'C',true);
		$this->Cell($this->width*0.105,$height,'인정번호',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'구분',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'성별',1,0,'C',true);
		$this->Cell($this->width*0.17,$height,'계약기간',1,0,'C',true);
		$this->Cell($this->width*0.18,$height,'주소',1,0,'C',true);
		$this->Cell($this->width*0.12, $height,'연락처',1,0,'C',true);
		$this->Cell($this->width*0.07,$height,'보호자',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'비고',1,1,'C',true);

	}


	function _header_CALN_LIST(){
		$subject = $subject = IntVal($this->year).'년 '.IntVal($this->month).'월 일정';

		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,$subject,0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
		$this->SetX($this->left);
		$this->Cell($this->width*0.1,$this->row_height,'일자',1,0,'C',1);
		$this->Cell($this->width*0.2,$this->row_height,'시간',1,0,'C',1);
		$this->Cell($this->width*0.15,$this->row_height,'작성자',1,0,'C',1);
		$this->Cell($this->width*0.55,$this->row_height,'제목',1,1,'C',1);
		$this->SetFont($this->font_name_kor,'',9);
	}


	function _header_CALN_WEEKLY(){
		parse_str($this->para, $val);

		$subject = $subject = IntVal($this->year).'년 '.IntVal($this->month).'월 '.$val['weekly'].'주 일정';

		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,$subject,0,1,'C');
	}


	//사례관리 해더
	function _header_HCE(){
		switch($this->mode){
			case '1':
				//사례관리 일지
				$this->_header_HCE_ReceiptList();
				break;

			case '21':
				//초기면접기록지
				$this->_header_HCE_Interview();
				break;

			case '31':
				//사정기록지 - 욕구
				break;

			case '41':
				//선정기준표
				break;

			case '51':
				//사례회의록
				//$this->_header_HCE_CaseMeetingList();
				break;

			case '52':
				//사례회의록
				//$this->_header_HCE_CaseMeeting();
				break;

			case '61':
				//서비스계획서
				//$this->_header_HCE_SvcPlanList();
				break;

			case '62':
				//서비스계획서
				//$this->_header_HCE_SvcPlan();
				break;

			case '71':
				//서비스 이용 안내 및 동의서
				//$this->_header_HCE_ConsentForm();
				break;

			case '81':
				//과정상담
				$this->_header_HCE_ProcCounsel();
				break;

			case '91':
				//서비스 연계 및 의뢰서
				$this->_header_HCE_SvcConnection();
				break;

			case '92':
				//서비스 연계 및 의뢰서
				$this->_header_HCE_SvcConnection();
				break;

			case '101':
				//모니터링 기록지
				//$this->_header_HCE_Monitor();
				break;

			case '102':
				//모니터링 기록지
				//$this->_header_HCE_Monitor();
				break;

			case '111':
				//재사정기록지
				$this->_header_HCE_ReIspt();
				break;

			case '112':
				//재사정기록지
				$this->_header_HCE_ReIspt();
				break;

			case '131':
				//사례평가서
				//$this->_header_HCE_Evaluation();
				break;
		}
	}

	function _header_HCE_ReceiptList(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"사 레 접 수 일 지",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();

		$this->SetX($this->left);
		$this->Cell($col[0],$this->row_height*2,'연번',1,0,'C',1);
		$this->Cell($col[1],$this->row_height,'접수',"LTR",0,'C',1);
		$this->Cell($col[2],$this->row_height*2,'접수일자',1,0,'C',1);
		$this->Cell($col[3],$this->row_height,'대상자명',"LTR",0,'C',1);
		$this->Cell($col[4],$this->row_height,'대상자주소',"LTR",0,'C',1);
		$this->Cell($col[5],$this->row_height*2,'상담내용',1,0,'C',1);
		$this->Cell($col[6],$this->row_height,'의뢰인',"LTR",0,'C',1);
		$this->Cell($col[7],$this->row_height*2,'접수자',1,0,'C',1);
		$this->Cell($col[8],$this->row_height,'초기면접',"LTR",1,'C',1);

		$this->SetX($this->left+$col[0]);
		$this->Cell($col[1],$this->row_height,'방법',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]);
		$this->Cell($col[3],$this->row_height,'(성별/나이)',"LBR",0,'C',1);
		$this->Cell($col[4],$this->row_height,'(연락처)',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]);
		$this->Cell($col[6],$this->row_height,'(연락처)',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]);
		$this->Cell($col[8],$this->row_height,'필요여부',"LBR",1,'C',1);

		$this->SetFont($this->font_name_kor,'',9);
	}

	function _header_HCE_Interview(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"초 기 면 접 지",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_CaseMeeting(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"사 례 회 의 록",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_CaseMeetingList(){
		
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"사 례 회 의 록",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "회차", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "판정구분", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "회의일자", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "조사자", 1, 0, "C", 1);
		$this->Cell($col[4], $rowH, "참석자", 1, 0, "C", 1);
		$this->Cell($col[5], $rowH, "제공여부", 1, 0, "C", 1);
		$this->Cell($col[6], $rowH, "판정일자", 1, 0, "C", 1);
		$this->Cell($col[7], $rowH, "비고", 1, 1, "C", 1);
		
	}

	function _header_HCE_SvcPlan(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"서 비 스 계 획 서",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_SvcPlanList(){
		$this->_header_HCE_SvcPlan();

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "회차", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "작성일자", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "작성자", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "비고", 1, 1, "C", 1);
	}

	function _header_HCE_ConsentForm(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"서비스 이용 안내 및 동의서",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_ProcCounsel(){
		
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"과 정 상 담 일 지",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',11);



		$this->SetXY($this->left, $this->top+8);
		$this->Cell($this->width,$this->row_height,'대상자 : '.$this->client,0,1,'L');

		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "일자", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "상담방법", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "내용", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "상담자", 1, 0, "C", 1);
		$this->Cell($col[4], $rowH, "비고", 1, 1, "C", 1);
	}

	function _header_HCE_SvcConnection(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"서비스 연계 및 의뢰서",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_Monitor(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"모니터링 기록지",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_ReIspt(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"재 사 정 기 록 지",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_Evaluation(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"사 례 평 가 서",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_SW_WORK_LOG(){
		parse_str($this->para, $val);

		$this->SetXY($this->left, $this->top-12);
		$this->SetFont($this->font_name_kor, '', 11);
		$this->Cell($this->width, $this->row_height, "[별지 제14호서식]", 0, 1);

		$X = $this->left;
		$Y = $this->GetY();

		$this->SetXY($X, $Y + 2);
		$this->SetFont($this->font_name_kor, "B", 17);
		$this->MultiCell($this->width * 0.54, 7, "방문요양기관 사회복지사\n업무수행 일지", 0, "C");

		$this->SetFont($this->font_name_kor, '', 11);
		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.54, $this->row_height * 3, "", 1);
		$this->Cell($this->width * 0.07, $this->row_height * 3, "확인", 1, 0, "C");
		$this->Cell($this->width * 0.13, $this->row_height, "사회복지사", 1, 0, "C");

		if($_SESSION['userCenterCode'] == 'CN13C003' || //아우내
		   $_SESSION['userCenterCode'] == 'CN13C001' ){ //느티나무
			$this->Cell($this->width * 0.13, $this->row_height, "과장", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "센터장", 1, 1, "C");
		}else {
			$this->Cell($this->width * 0.13, $this->row_height, "관리책임자", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "", 1, 1, "C");
		}

		$this->SetX($this->left + $this->width * 0.61);
		$this->Cell($this->width * 0.13, $this->row_height * 2, $val['regName'], 1, 0, "C");
		$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0);
		$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 1);

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.3, $this->row_height, "수급자 성명", 1, 0, "C");
		$this->Cell($this->width * 0.3, $this->row_height, "장기요양등급", 1, 0, "C");
		$this->Cell($this->width * 0.4, $this->row_height, "방문일시", 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width * 0.3, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.3, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.4, $this->row_height, $val['datetime'], 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
	}

	function _header_SW_WORK_LOG_24ho(){
		
		if($_SESSION['userCenterCode'] == '32823700100'){ //아인재가
			if($this->PageNo() == 1){ 
				$pageYN = 'Y';
			}else {
				$pageYN = 'N';
			}
		}else {
			$pageYN = 'Y';
		}
		
		
		parse_str($this->para, $val);
		
		if ($pageYN == 'Y'){	
			$this->SetXY($this->left, $this->top-12);
			$this->SetFont($this->font_name_kor, '', 11);
			$this->Cell($this->width, $this->row_height, "[별지 제24호서식]", 0, 1);

			$X = $this->left;
			$Y = $this->GetY();
			
			$this->SetXY($X, $Y + 2);
			$this->SetFont($this->font_name_kor, "B", 17);
			if($va['regYymm'] > '201701'){
				$this->MultiCell($this->width * 0.54, 7, "프로그램 관리자 및 방문요양기관\n사회복지사 업무수행 일지", 0, "C");
			}else {
				$this->MultiCell($this->width * 0.54, 7, "프로그램 관리자 및 \n사회복지사 업무수행 일지", 0, "C");
			}
			$this->SetFont($this->font_name_kor, '', 11);
			$this->SetXY($X, $Y);
			$this->Cell($this->width * 0.54, $this->row_height * 3, "", 1);
			$this->Cell($this->width * 0.07, $this->row_height * 3, "확인", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "방문자", 1, 0, "C");

			if($_SESSION['userCenterCode'] == 'CN13C003' || //아우내
			   $_SESSION['userCenterCode'] == 'CN13C001' ){ //느티나무
				$this->Cell($this->width * 0.13, $this->row_height, "과장", 1, 0, "C");
				$this->Cell($this->width * 0.13, $this->row_height, "센터장", 1, 1, "C");
			}else {
				$this->Cell($this->width * 0.13, $this->row_height, "요양보호사", 1, 0, "C");
				$this->Cell($this->width * 0.13, $this->row_height, "관리책임자", 1, 1, "C");
			}

			//if ($_SESSION['userCenterCode'] == '1234'){
				//$sign = '../mm/sign/member/'.$_SESSION['userCenterCode'].'/'.$val['regKey'].'_r.jpg';
				

				
				
				//방문자
				$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-3.jpg';
				if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-3.jpg';

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.61 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}
				
				

				//요양보호사
				$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-2.jpg';
				if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-2.jpg';

				//if($debug) echo $sign.'/';

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.74 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}

				//관리책임자
				if (is_numeric($val['signManager'])){
					$sign = '../sign/sign/manager/'.$_SESSION['userCenterCode'].'/'.$val['signManager'].'.jpg';
				}else{
					$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$_SESSION['regYymm'].'/'.$_SESSION['regKey'].'/'.$_SESSION['regSeq'].'_7-4.jpg';
				}

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.87 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}
			//}

			$this->SetX($this->left + $this->width * 0.61);
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0);
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 1);
		}	

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.15, $this->row_height, "수급자 성명", 1, 0, "C");
		$this->Cell($this->width * 0.17, $this->row_height, "장기요양등급", 1, 0, "C");
		$this->Cell($this->width * 0.21, $this->row_height, "장기요양인정번호", 1, 0, "C");
		$this->Cell($this->width * 0.28, $this->row_height, "방문일시", 1, 0, "C");
		$this->Cell($this->width * 0.19, $this->row_height, "수급자(보호자)", 1, 1, "C");

		$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-1.jpg';
		if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-1.jpg';

		if (is_file($sign)){
			$tmpImg = getImageSize($sign);
			$picW = $tmpImg[0] * 0.04 * 0.1;
			$picH = $tmpImg[1] * 0.04 * 0.1;

			$prtW = $this->width * 0.13 - 2;
			$prtH = $this->row_height * 2 - 2;

			if ($picW > $cpsW || $picH > $cpsH){
				$picR = 1;

				if ($picW > $picH){
					$picR = $picH / $picW;
					$picW = $prtW;
					$picH = $prtH * $picR;
				}else{
					$picR = $picW / $picH;
					$picH = $prtH;
					$picW = $prtW * $picR;
				}
			}

			$gabL = ($this->width * 0.13 - $picW) / 2;
			$gabT = ($this->row_height * 2 - $picH) / 2;

			if ($gabL < 0) $gabL = 0;
			if ($gabT < 0) $gabT = 0;

			$this->Image($sign, $X + $this->width * 0.81 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
		}

		$this->SetX($X);
		$this->Cell($this->width * 0.15, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.17, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.21, $this->row_height, $val['appNo'], 1, 0, "C");
		$this->Cell($this->width * 0.28, $this->row_height, $val['datetime'], 1, 0, "C");
		$this->Cell($this->width * 0.19, $this->row_height * 2, "", 1, 1, "C");

		$this->SetXY($X, $this->GetY() - $this->row_height);
		$this->Cell($this->width * 0.81, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width * 0.81), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
		
	}

	function _header_SW_WORK_LOG2(){
	
		if($_SESSION['userCenterCode'] == '32823700100'){ //아인재가
			if($this->PageNo() == 1){ 
				$pageYN = 'Y';
			}else {
				$pageYN = 'N';
			}
		}else {
			$pageYN = 'Y';
		}

		parse_str($this->para, $val);
		
		if ($pageYN == 'Y'){
			
			$this->SetXY($this->left, $this->top-12);
			$this->SetFont($this->font_name_kor, '', 11);
			$this->Cell($this->width, $this->row_height, "[별지 제14호서식]", 0, 1);

			$X = $this->left;
			$Y = $this->GetY();


			$this->SetFont($this->font_name_kor, "B", 15);
			
			$this->SetXY($X, $Y);
			
			if($va['regYymm'] > '201701'){
				$this->Cell($this->width, $this->row_height * 2, "프로그램 관리자 및 방문요양기관 사회복지사 업무수행 일지", 1, 1,'C');
			}else {
				$this->Cell($this->width, $this->row_height * 2, "프로그램 관리자 및 사회복지사 업무수행 일지", 1, 1,'C');
			}
		}

		$this->SetFont($this->font_name_kor, '', 11);

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.20, $this->row_height, "수급자 성명", 1, 0, "C");
		$this->Cell($this->width * 0.20, $this->row_height, "장기요양등급", 1, 0, "C");
		$this->Cell($this->width * 0.36, $this->row_height, "장기요양인정번호", 1, 0, "C");
		$this->Cell($this->width * 0.24, $this->row_height, "수급자(보호자)", 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width * 0.20, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.20, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.36, $this->row_height, $val['appNo'], 1, 0, "C");
		$this->Cell($this->width * 0.24, $this->row_height * 2, "", 1, 1, "C");

		$this->SetXY($X, $this->GetY() - $this->row_height);
		$this->Cell($this->width * 0.76, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width * 0.76), 1, 1, "L");

		//$this->SetX($X);
		//$this->Cell($this->width, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
		
	}


	function _header_SW_WORK_LOG_SIGN(){
		$subject = IntVal($this->year).'년 '.IntVal($this->month).'월 ';

		parse_str($this->para, $val);


		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor, "B", 15);

		$liWidth	= 196;
		$liLeft		= 0;


		$tmpStr	= '기관장';

		if ($this->PageNo() == 1){
			$this->SetLineWidth(0.2);
			//$this->Rect($this->width-$liLeft*2, $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.03), $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.13), $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.23), $this->top, $liWidth*0.1, $this->row_height * 4);

			$this->Line($this->width-($liLeft*2+$liWidth*0.13)
					  , $this->top + $this->row_height * 1.3
					  , $this->width+13.5
					  , $this->top + $this->row_height * 1.3);

			$this->Cell($this->width * 0.6, $this->row_height * 4, '', 0, 1, 'C');

			$this->SetXY($this->left,$this->GetY()-18);
			$this->MultiCell(100, 7, "방문사회복지사 보고서\n(기간 : ".str_replace('-','.',$val['fromDt'])." ~ ".str_replace('-','.',$val['toDt']).")", 0, "C");

		}else {
			/*
			$this->SetXY($this->left+40,$this->GetY()-5);
			$this->MultiCell(100, 7, "방문사회복지사 보고서\n(기간 : ".str_replace('-','.',$val['fromDt'])." ~ ".str_replace('-','.',$val['toDt']).")", 0, "C");
			*/
		}


		if ($this->PageNo() == 1){
			$this->SetFont($this->font_name_kor, "", 13);
			$this->Text($this->width-($liLeft*2+$liWidth*0.23)+(($liWidth*0.1 - $this->GetStringWidth("결")) / 2), $this->top+9, "결");
			$this->Text($this->width-($liLeft*2+$liWidth*0.23)+(($liWidth*0.1 - $this->GetStringWidth("결")) / 2), $this->top+18, "재");


			$this->SetFont($this->font_name_kor, "", 11);
			$this->Text($this->width-($liLeft*2+$liWidth*0.13)+(($liWidth*0.1 - $this->GetStringWidth("복지사")) / 2), $this->top+5.5, "복지사");
			//$this->Text(($this->width+$liLeft*2)-($liWidth*0.2), $this->top+5.5, "팀  장");
			$this->Text($this->width-($liLeft*2+$liWidth*0.03)+(($liWidth*0.1 - $this->GetStringWidth("기관장")) / 2), $this->top+5.5, "기관장");

			if($val['printDt'] != ''){
				$this->SetXY($this->left*10, $this->GetY()+7);
				$this->Cell($this->width*0.15, $this->row_height, '출력일자: ', 0, 0, 'R');
				$this->Cell($this->width*0.15, $this->row_height, str_replace('-','.',$val['printDt']), 0, 0, 'C');
			}
		}

		$this->SetXY($this->left, $this->GetY()+10);
		$this->Cell($this->width*0.15, $this->row_height, '일자', 1, 0, 'C', true);
		$this->Cell($this->width*0.15, $this->row_height, '수급자', 1, 0, 'C', true);
		$this->Cell($this->width*0.35, $this->row_height, '총평', 1, 0, 'C', true);
		$this->Cell($this->width*0.35, $this->row_height, '지시사항', 1, 1, 'C', true);

	}
}
?>
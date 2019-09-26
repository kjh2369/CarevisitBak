<?php
include_once('../pdf/pdf_p.php');

class MYPDF extends MY_PDF{
	var $type = null;
	var $root = null;

	var $k_cd = null; #기관기호
	var $k_nm = null; #기관명

	var $m_nm       = null;
	var $m_birthday = null;
	var $m_mobile   = null;

	var $c_cd = null;
	var $c_nm = null;
	var $c_phone  = null;
	var $c_mobile = null;
	var $c_postno  = null;
	var $c_addr = null;
	var $c_addr_dtl = null;
	var $c_parent_nm  = null;
	var $c_parent_rel = null;
	var $c_parent_phone  = null;
	var $eduLvl  = null;

	function Header(){
		if ($this->type == 'HUMAN'){
			/**************************************************
				타이틀 출력
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 15);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width, 15, '인적자원관리', 0, 1, 'C');



			/**************************************************
				기본 폰트 설정
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);



			/**************************************************
				기관정보
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.13, $this->row_height, '기관기호', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->k_cd, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '기관명', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.54, $this->row_height, $this->k_nm, 1, 1, 'L');



			/**************************************************
				직원정보
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.13, $this->row_height, '직원명', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->m_nm, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '생년월일', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.21, $this->row_height, $this->m_birthday, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '연락처', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->m_mobile, 1, 1, 'L');
		}else if ($this->type == 'VISIT' ||
				  $this->type == 'PHONE' ||
				  $this->type == 'STRESS' ||
				  $this->type == 'CASE'){

			if ($this->sginCnt > 0){
				$this->SetXY($this->left, $this->top-5);
				$this->SetFont('바탕','B',15);
				$this->SetLineWidth(0.2);

				//$this->SetY($this->GetY()+5);
				//$this->Cell($this->width * 0.6, $this->row_height * 4, $this->year.'년 '.$this->month.'월', 0, 1, 'C');
				$this->SetY($this->GetY());
				switch($this->type){
					case 'VISIT':
						$this->Cell($this->width*0.6, 32, '고객 방문상담 기록지', 0, 1, 'C');
						break;

					case 'PHONE':
						$this->Cell($this->width*0.6, 32, '전화 방문상담 기록지', 0, 1, 'C');
						break;

					case 'STRESS':
						$this->Cell($this->width*0.6, 32, '불만 및 고충처리기록지', 0, 1, 'C');
						break;

					case 'CASE':
						$this->Cell($this->width*0.6, 32, '사례관리 회의', 0, 1, 'C');
						break;
				}

				$this->_SignlineSet();

				$this->SetY($this->GetY());

			}else{

				/**************************************************
					타이틀 출력
				**************************************************/
				$this->SetFont($this->font_name_kor, 'B', 15);
				$this->SetXY($this->left, $this->top);

				switch($this->type){
					case 'VISIT':
						$this->Cell($this->width, 15, '고객 방문상담 기록지', 0, 1, 'C');
						break;

					case 'PHONE':
						$this->Cell($this->width, 15, '전화 방문상담 기록지', 0, 1, 'C');
						break;

					case 'STRESS':
						$this->Cell($this->width, 15, '불만 및 고충처리기록지', 0, 1, 'C');
						break;

					case 'CASE':
						$this->Cell($this->width, 15, '사례관리 회의', 0, 1, 'C');
						break;
				}
			}


			/**************************************************
				기본 폰트 설정
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);



			/**************************************************
				기관정보
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.15, $this->row_height, '기관기호', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->k_cd, 1, 0, 'L');
			$this->Cell($this->width * 0.15, $this->row_height, '기 관 명', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.45, $this->row_height, $this->k_nm, 1, 1, 'L');
			
			if(str_replace('-','', $this->c_postno)==''){
				$post = '';
			}else {
				$post = "(".str_replace('-','', $this->c_postno).") ";
			}
			$juso =  explode('<br />',nl2br($this->c_addr));
			
			/**************************************************
				고객정보
			**************************************************/
			if ($this->root == 'MEMBER'){
				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '직 원 명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_nm, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height * 2, '연 락 처', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '유   선', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_phone, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '주민번호', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_cd, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height, '', 0, 0, 'C');
				$this->Cell($this->width * 0.10, $this->row_height, '무   선', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_mobile, 1, 1, 'L');

				$pos[sizeof($pos)] = array('x'=>$this->left + $this->width * 0.15, 'y'=>$this->GetY() + $this->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$this->width * 0.40, 'height'=>5, 'align'=>'L', 'text'=>$post.$juso[0]." ".$this->c_addr_dtl);

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height * 3, '주   소', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height * 3, '', 1, 0, 'C');
				$this->Cell($this->width * 0.15, $this->row_height * 3, '비   고', 1, 0, 'C', 1);

				$this->SetX($this->left + $this->width * 0.55);
				$this->Cell($this->width * 0.45, $this->row_height * 3, '', 1, 1, 'L');

				
				$this->SetLineWidth(0.6);
				$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);
				$this->SetLineWidth(0.2);

			}else{
				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '고 객 명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_nm, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height * 2, '연 락 처', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '유   선', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_phone, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '주민번호', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_cd, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height, '', 0, 0, 'C');
				$this->Cell($this->width * 0.10, $this->row_height, '무   선', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_mobile, 1, 1, 'L');

				$pos[sizeof($pos)] = array('x'=>$this->left + $this->width * 0.15, 'y'=>$this->GetY() + $this->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$this->width * 0.40, 'height'=>5, 'align'=>'L', 'text'=>$post.$juso[0]." ".$this->c_addr_dtl);

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height * 3, '주   소', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height * 3, '', 1, 0, 'C');
				$this->Cell($this->width * 0.15, $this->row_height * 3, '보 호 자', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '성   명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_nm, 1, 1, 'L');

				$this->SetX($this->left + $this->width * 0.70);
				$this->Cell($this->width * 0.10, $this->row_height, '관   계', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_rel, 1, 1, 'L');

				$this->SetX($this->left + $this->width * 0.70);
				$this->Cell($this->width * 0.10, $this->row_height, '연 락 처', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_phone, 1, 1, 'L');
					
				$this->SetLineWidth(0.6);
				$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);
				$this->SetLineWidth(0.2);

			}



			$tmp_Y = $this->getY();	//고객정보테이블다음높이가져오기

			set_array_text($this, $pos);
			unset($pos);

			$this->setY($tmp_Y);

		}else if ($this->type == 'HUMAN2'){

			/**************************************************
				기본 폰트 설정
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);


			/**************************************************
				직원정보
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height*7, '<사 진>', 1, 0, 'C');

			/**************************************************
				타이틀 폰트 설정
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 25);

			$this->Cell($this->width * 0.84, $this->row_height*3.5, '인 사 기 록 카 드', 1, 1, 'C');


			$this->SetFont($this->font_name_kor, '', 10);

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '', 'LR', 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '성 명', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_nm, 1, 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '주민등록번호', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_jumin, 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '', 'LR', 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '연락처', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, ($this->m_tel != '' ? $this->m_tel : $this->m_mobile), 1, 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '휴대폰', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_mobile, 1, 1, 'C');

			$this->SetX($this->left + $this->width * 0.16);
			$this->Cell($this->width * 0.17, $this->row_height, '주 소', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.67, $this->row_height, ' '.$this->m_addr, 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '부양가족', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 0, 'C');
			$this->Cell($this->width * 0.16, $this->row_height, '종사업무', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height*4, '이력', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.11, $this->row_height, '최종 학력', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '  '.$this->eduLvl , 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '경      력', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '병      력', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetXY($this->left+$this->width*0.50, $this->getY()-$this->row_height*4);
			$this->Cell($this->width * 0.05, $this->row_height*4, '퇴직', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.11, $this->row_height, '해고일', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '퇴직일', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, ' '.$this->m_retire_dt, 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '사   유', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '금품청산 등', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '고용일(계약기간)', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '      '.$this->m_from_dt.' ~ ', 1, 0, 'L');
			$this->Cell($this->width * 0.16, $this->row_height, '근로계약 갱신일', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			if (Is_File($this->m_picture)){
				$this->Image('../mem_picture/'.$this->m_picture, 14.3, 10.5, 28.5, 38.1);
			}

		}else if ($this->type == 'AGREE'){

			/**********************************************

				2012.09.27 본인부담금 급여 공제지급 동의서

			************************************************/

			/**************************************************
				타이틀 출력
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 15);
			$this->SetXY($this->left, $this->top);

			$this->Cell($this->width, 15, '본인부담금 급여 공제지급 동의서', 0, 1, 'C');


			$this->SetFont($this->font_name_kor, '', 11);


			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '요양보호사명', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, $this->m_nm, 1, 0, 'C');
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '주민등록번호', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, $this->m_jumin, 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '자격번호', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '', 1, 0, 'C');
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '전    화', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, ($this->m_mobile != '' ? $this->m_mobile : $this->m_tel), 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '주    소', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.75, $this->row_height*1.2, '   '.$this->m_addr, 1, 1, 'L');


			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->GetY()-$this->row_height*3.6, $this->width, $this->height*0.88);
			$this->SetLineWidth(0.2);

		}
	}

	function Footer(){
		$this->_drawIcon();
	}

	function draw_header($type, $pos_h){

		if ($this->type == 'HUMAN'){
			if ($type == 'edu'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.435, $this->row_height, '돌봄관련 교육', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.435, $this->row_height, '기타 교육', 1, 1, 'C', 1);

				$this->SetX($this->left + $this->width * 0.130);
				$this->Cell($this->width * 0.180, $this->row_height, '교육기관', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '교육명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.075, $this->row_height, '시간', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '교육기관', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '교육명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.075, $this->row_height, '시간', 1, 1, 'C', 1);
			}else if ($type == 'lcs'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.220, $this->row_height, '자격증종류', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.215, $this->row_height, '자격증번호', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.290, $this->row_height, '발급기관', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '발급일자', 1, 1, 'C', 1);
			}else if ($type == 'rnp'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '일자', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.100, $this->row_height, '구분', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.625, $this->row_height, '내용', 1, 1, 'C', 1);
			}
		}

		if ($this->type == 'HUMAN2'){

			if ($type == 'rec'){ //입사전기록
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.260, $this->row_height, '근무기간', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.200, $this->row_height, '직장명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '직 위', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.170, $this->row_height, '담당업무', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '급 여', 1, 1, 'C', 1);
			}else if ($type == 'edu'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '교육구분', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '교육기관', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.200, $this->row_height, '교육명', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.260, $this->row_height, '교육기간', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.110, $this->row_height, '시간', 1, 1, 'C', 1);
			}else if ($type == 'lcs'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.220, $this->row_height, '자격증종류', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.215, $this->row_height, '자격증번호', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.290, $this->row_height, '발급기관', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '발급일자', 1, 1, 'C', 1);
			}else if ($type == 'rnp'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '일자', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.100, $this->row_height, '구분', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.625, $this->row_height, '내용', 1, 1, 'C', 1);
			}

		}

	}
}
?>
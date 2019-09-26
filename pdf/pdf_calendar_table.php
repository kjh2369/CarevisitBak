<?php
//require('korean.php');
require('pdf_l2.php');

class MYPDF extends MY_PDF /*PDF_Korean*/
{	
	var $mode;    //구분
	var $year;	  //년도
	var $month;	  //월
	var $week;    //주차
	var $fromDt;  //주간시작일
	var $toDt;    //주간종료일
	var $center_nm; //기관명
	
	var $auto_draw_head = true;

	// 행 높이 설정
	var $rowHeight = 6;

	function Header(){
		
		if($this->mode == 'week'){
			$title = intVal($this->year).'년 '.intVal($this->month).'월 '.intVal($this->week).'주차 '.$this->center_nm.' 일정';
		}else {
			$title = intVal($this->year).'년 '.intVal($this->month).'월 '.$this->center_nm.' 일정';
		}
		// 타이틀
		$this->SetXY($this->left, $this->left);
		$this->SetFont('굴림','B',15);
		$this->Cell($this->width, 5, $title, 0, 1, 'C');
		
		$col = $this->calranderColWidth();

		if($this->mode == 'week'){
			// 일정 변수 설정
			$calTime	= mktime(0, 0, 1, $this->month-1, 1, $this->year);
			$lastDay	= date('t', $calTime);			//총일수 구하기
			
			$this->SetXY($this->left, $this->top);

			if($this->fromDt == 1){
				
			}else {
				if($this->week == 1){
					for($i=$this->fromDt; $i<=$lastDay; $i++){
						$date = $this->year.(($this->month-1)<10?0:'').($this->month-1).($i<10?0:'').$i;
						$w = date('w', strtotime($date));
						
						switch($w){
						case 0:
							$this->SetTextColor(255,0,0);
							break;
						case 6:
							$this->SetTextColor(0,0,255);
							break;
						default:
							$this->SetTextColor(0,0,0);
							break;
						}
						
						$this->Cell($col['w'][$w], $this->row_height, $col['t'][$w], 1, $w < 6 ? 0 : 1, 'C', true);
					}
					
					$this->fromDt = 1;
				}
			}

			$calTime2	= mktime(0, 0, 1, $month, 1, $year);
			$lastDay2	= date('t', $calTime2);			//총일수 구하기
			
			if($this->fromDt > $this->toDt){
				$toDt = $lastDay2;
			}

			for($i=$fromDt; $i<=$toDt; $i++){		
				$date = $year.$month.($i<10?0:'').$i;
				$w = date('w', strtotime($date));
				
				$this->Cell($col['w'][$w], $this->row_height, $col['t'][$w], 1, $w < 6 ? 0 : 1, 'C', true);
			}
			
			if($fromDt > $_POST['toDt']){
				
				$fromDt = 1;

				for($i=$fromDt; $i<=$_POST['toDt']; $i++){
					$date = $year.(($month+1)<10?0:'').($month+1).($i<10?0:'').$i;
					$w = date('w', strtotime($date));
					
					$this->Cell($col['w'][$w], $this->row_height, $col['t'][$w], 1, $w < 6 ? 0 : 1, 'C', true);
				}
			}
		}
	}

	function Footer(){
		$this->_drawIcon();
	}

	function drawHeader(){
	
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
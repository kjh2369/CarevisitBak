<?php
//require('korean.php');
require('pdf_l2.php');

class MYPDF extends MY_PDF /*PDF_Korean*/
{	
	var $mode;    //����
	var $year;	  //�⵵
	var $month;	  //��
	var $week;    //����
	var $fromDt;  //�ְ�������
	var $toDt;    //�ְ�������
	var $center_nm; //�����
	
	var $auto_draw_head = true;

	// �� ���� ����
	var $rowHeight = 6;

	function Header(){
		
		if($this->mode == 'week'){
			$title = intVal($this->year).'�� '.intVal($this->month).'�� '.intVal($this->week).'���� '.$this->center_nm.' ����';
		}else {
			$title = intVal($this->year).'�� '.intVal($this->month).'�� '.$this->center_nm.' ����';
		}
		// Ÿ��Ʋ
		$this->SetXY($this->left, $this->left);
		$this->SetFont('����','B',15);
		$this->Cell($this->width, 5, $title, 0, 1, 'C');
		
		$col = $this->calranderColWidth();

		if($this->mode == 'week'){
			// ���� ���� ����
			$calTime	= mktime(0, 0, 1, $this->month-1, 1, $this->year);
			$lastDay	= date('t', $calTime);			//���ϼ� ���ϱ�
			
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
			$lastDay2	= date('t', $calTime2);			//���ϼ� ���ϱ�
			
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


		$col['t'][0] = '��';
		$col['t'][1] = '��';
		$col['t'][2] = 'ȭ';
		$col['t'][3] = '��';
		$col['t'][4] = '��';
		$col['t'][5] = '��';
		$col['t'][6] = '��';

		return $col;
	}
}


?>
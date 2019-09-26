<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	
	var $auto_draw_head = true;
	var $sginCnt	= null;

	var $rowHeight = 7;

	function Header(){
		// Ÿ��Ʋ

		if ($this->sginCnt > 0){
			$this->SetXY($this->left, $this->top);
			$this->SetFont('����','B',13);
			$this->SetLineWidth(0.2);

			//$this->SetY($this->GetY()+5);
			//$this->Cell($this->width * 0.6, $this->row_height * 4, $this->year.'�� '.$this->month.'��', 0, 1, 'C');
			$this->SetY($this->GetY());
			$this->Cell($this->width * 0.6, $this->row_height * 4,'�ʱ���(�屸����)�����', 0, 1, 'C');
			$this->_SignlineSet();

			$this->SetY($this->GetY());
			
		}else{
			$this->SetXY($this->left, $this->top);
			$this->SetFont('����','B',15);
			$this->SetLineWidth(0.2);
			
			$this->Cell($this->width, $this->rowHeight+17, '�ʱ���(�屸����)�����', 1, 1, 'C');
		}

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
		
		
		$this->SetFont('����','',9);
		$this->SetFillColor(220,220,220);
		$this->SetTextColor(0,0,0);

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'��㱸��',1,0,'C',true);
		$this->Cell(35,$this->rowHeight,$this->counsel_gbn,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�����ȣ',1,0,'C',true);
		$this->Cell(30,$this->rowHeight,$this->code,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�����',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->cname,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'�����',1,0,'C',true);
		$this->Cell(35,$this->rowHeight,$this->talker_nm,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�������',1,0,'C',true);
		$this->Cell(30,$this->rowHeight,$this->client_dt,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�������',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,' �೻��  ��湮  ����ȭ',1,1,'L');

		$this->SetFont('����','',9);
		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(85,$this->rowHeight,$this->client_nm,1,0,'L');
		$this->Cell(20,14,'����ó',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->tel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'�ֹι�ȣ',1,0,'C',true);
		$this->Cell(85,$this->rowHeight,$this->client_ssn,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->mobile,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,21,'����',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'�����ȣ',1,0,'C',true);
		$this->Cell(65,$this->rowHeight,$this->postno,1,0,'L');
		$this->Cell(20,21,'��ȣ��',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->protect_nm,1,1,'L');
		
		$orgY = $this->getY();
		
		$juso =  explode('<br />',nl2br($this->addr));

		$this->SetXY($this->left+20,$orgY+2);
		$this->MultiCell (80,5,$juso[0].' '.$this->addr_dtl);

		$this->SetXY($this->left, $orgY);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(85,$this->rowHeight,'','TRL',0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->protect_rel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(85,$this->rowHeight,'','BRL',0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'��ȭ��ȣ',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->protect_tel,1,1,'L');


		if($this->gbn == '3'){
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'��ȣ����',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'���Ϲ�  ��������  ����ʻ�Ȱ������',1,1,'L');
		}else {
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'��ȣ����',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'�������1��  �������2��  ���Ϲ�  ���Ÿ (                )',1,1,'L');
		}
		
		if($this->gbn == '0'){
			$this->SetX($this->left);
			$this->Cell(20,$this->rowHeight,'�����',1,0,'C',true);
			$this->Cell(162,$this->rowHeight,'��1���  ��2���  ��3���  ��4���  ��5���  ���Ϲ�',1,1,'L');
		}

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'��������',1,0,'C',true);
		$this->Cell(162,$this->rowHeight,'�൶��  ����κκ�  ��Ƶ鰡��  �������  ���Ÿ',1,1,'L');				
		//�������
		switch($this->talker_type){
			case 1:
				$this->Text(141.7, 56, 'v');
				break;
			case 2:
				$this->Text(153.1, 56, 'v');
				break;
			case 3:
				$this->Text(165, 56, 'v');
				break;
		}

		if($this->gbn == '3'){
			//��ȣ����
			switch($this->protect_gbn){
				case 1:
					$this->Text(35.6, 98.2, 'v');
					break;
				case 2:
					$this->Text(47.3, 98.2, 'v');
					break;
				case 9:
					$this->Text(62.2, 98.2, 'v');
					break;
			}
		}else{
			switch($this->protect_gbn){
				case 1:
					$this->Text(35.6, 98.2, 'v');
					break;
				case 2:
					$this->Text(55.7, 98.2, 'v');
					break;
				case N:
					$this->Text(75.6, 98.2, 'v');
					break;
				case 9:
					$this->Text(87.3, 98.2, 'v');
					$this->Text(99, 99.2, $this->protect_other);
					break;
			}
			
			if($this->gbn == '0'){
				switch($this->level_gbn){
					case 1:
						$this->Text(35.6, 105.2, 'v');
						break;
					case 2:
						$this->Text(48.9, 105.2, 'v');
						break;
					case 3:
						$this->Text(62.6, 105.2, 'v');
						break;
					case 4:
						$this->Text(76.3, 105.2, 'v');
						break;
					case 5:
						$this->Text(90, 105.2, 'v');
						break;
					case 9:
						$this->Text(103.7, 105.2, 'v');
						break;	
				}
			}
		}

		if($this->gbn == '0'){
			//��������
			switch($this->family_gbn){
				case 1:
					$this->Text(35.6, 112, 'v');
					break;
				case 2:
					$this->Text(47.3, 112, 'v');
					break;
				case 3:
					$this->Text(65.2, 112, 'v');
					break;
				case 4:
					$this->Text(83.3, 112, 'v');
					break;
				case 9:
					$this->Text(98, 112, 'v');
					break;
			}
		}else {	
			//��������
			switch($this->family_gbn){
				case 1:
					$this->Text(35.6, 105.2, 'v');
					break;
				case 2:
					$this->Text(47.3, 105.2, 'v');
					break;
				case 3:
					$this->Text(65.2, 105.2, 'v');
					break;
				case 4:
					$this->Text(83.3, 105.2, 'v');
					break;
				case 9:
					$this->Text(98, 105.2, 'v');
					break;
			}
		}

		// �׵θ�
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.2);
			$this->Rect($this->left, $this->top, $this->width, 87);
		}else{
			$this->SetY($this->GetY());
		}
	}

}
?>
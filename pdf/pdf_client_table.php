<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{

	var $auto_draw_head = true;

	var $rowHeight = 7;

	function Header(){
		// Ÿ��Ʋ

		$this->SetXY($this->left, $this->top);
		$this->SetFont('����','B',12);
		$this->SetLineWidth(0.2);
		$this->Cell($this->width, $this->rowHeight+5, '�ʱ���(�屸����)�����', 1, 1, 'C');

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
		$this->Cell(20,$this->rowHeight,'�����ȣ',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->code,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�����',1,0,'C',true);
		$this->Cell(97,$this->rowHeight,$this->cname,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'��㱸��',1,0,'C',true);
		$this->Cell(162,$this->rowHeight,$this->counsel_gbn,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'�����',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->talker_nm,1,0,'L');
		$this->Cell(20,$this->rowHeight,'�������',1,0,'C',true);
		$this->Cell(40,$this->rowHeight,' �೻��  ��湮  ����ȭ',1,0,'L');
		$this->Cell(20,$this->rowHeight,'�������',1,0,'C',true);
		$this->Cell(37,$this->rowHeight,$this->client_dt,1,1,'L');

		$this->SetFont('����','',9);
		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(65,$this->rowHeight,$this->client_nm,1,0,'L');
		$this->Cell(20,14,'����ó',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->tel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,$this->rowHeight,'�ֹι�ȣ',1,0,'C',true);
		$this->Cell(65,$this->rowHeight,$this->client_ssn,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->mobile,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,21,'����',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'�����ȣ',1,0,'C',true);
		$this->Cell(45,$this->rowHeight,$this->postno,1,0,'L');
		$this->Cell(20,21,'��ȣ��',1,0,'C',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_nm,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(65,$this->rowHeight,$this->addr,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'����',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_rel,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(65,$this->rowHeight,$this->addr_dtl,1,0,'L');
		$this->Cell(20,0,'',0,0,'L',true);
		$this->Cell(20,$this->rowHeight,'��ȭ��ȣ',1,0,'C',true);
		$this->Cell(57,$this->rowHeight,$this->protect_tel,1,1,'L');


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
			//��ȣ����
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
			//��������
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
			//��������
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

		// �׵θ�
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.2);
			$this->Rect($this->left, $this->top, $this->width, 82);
		}else{
			$this->SetY($this->GetY());
		}
	}

}
?>
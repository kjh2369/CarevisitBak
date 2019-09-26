<?php
//require('korean.php');
require('pdf_p.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	
	/*
	var $code;
	var $cname;
	var $mname;
	var $mjumin;
	var $marry_yn;
	var $tel;
	var $mobile;
	var $email;
	*/
	var $auto_draw_head = true;

	// �� ���� ����
	var $rowHeight = 6;

	function Header(){
		// Ÿ��Ʋ
	
		$this->SetXY($this->left, $this->top);
		$this->SetFont('����','B',12);
		$this->SetLineWidth(0.2);
		$this->Cell($this->width, 12, '�����ʱ�������', 1, 1, 'C');

		if ($this->auto_draw_head){
			$this->drawHeader();
		}
	}

	function Footer(){
		/*
		if ($this->useType == 'y'){
			// ������
		}else{
			if ($this->type == 's'){
				// ������
			}else if ($this->tupe == 'y'){
				// ��纸ȣ��
			}else{
			}
		}

		$this->SetXY($this-left, -20);
		$this->SetFont('����','B',15);
		$this->Cell($this->left+$this->width, 5, $this->centerName."(".$this->centerTel.")", 0, 1, 'C');
		$this->SetXY($this-left, -13);
		$this->SetFont('����','',11);
		$this->Cell($this->left+$this->width, 5, "�Աݰ���:".$this->bankName."(".$this->bankNo.") ������:".$this->bankDepos, 0, 1, 'C');
		*/
	}

	function drawHeader(){
		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY());
		}
		
		$this->SetFont('����','',9);
		$this->SetFillColor(220,220,220);

		$this->SetX($this->left);
		$this->Cell(22,11,'�����ȣ',1,0,'C',true);
		$this->Cell(50,11,$this->code,1,0,'L');
		$this->Cell(25,11,'�����',1,0,'C',true);
		$this->Cell(85,11,$this->cname,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,39,'�� ��',1,0,'C');
		$this->Cell(20,6.5,'����',1,0,'C',true);
		$this->Cell(35,6.5,$this->mname,1,0,'L');
		$this->Cell(20,19.5,'����ó',1,0,'C',true);
		$this->Cell(20,6.5,'����',1,0,'C',true);
		$this->Cell(57,6.5,$this->tel,1,1,'C');

		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,6.5,'�ֹι�ȣ',1,0,'C',true);
		$this->Cell(35,6.5,$this->mjumin,1,0,'L');
		$this->Cell(20,0,'',0,0,'L');
		$this->Cell(20,6.5,'����',1,0,'C',true);
		$this->Cell(57,6.5,$this->mobile,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,6.5,'��ȥ����',1,0,'C',true);
		$this->Cell(35,6.5,'��YES   ��NO',1,0,'L');
		$this->Cell(20,0,'',0,0,'L');
		$this->Cell(20,6.5,'e-mail',1,0,'C',true);
		$this->Cell(57,6.5,$this->email,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,19.5,'�ּ�',1,0,'C',true);
		$this->Cell(20,6.5,'�����ȣ',1,0,'C',true);
		$this->Cell(112,6.5,$this->postno,1,1,'L');
		
		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,0,'',0,0,'C');
		$this->Cell(132,6.5,$this->addr,1,1,'L');

		$this->SetX($this->left);
		$this->Cell(30,0,'',0,0,'C');
		$this->Cell(20,0,'',0,0,'C');
		$this->Cell(132,6.5,$this->addr_dtl,1,1,'L');


		if($this->picture != ''){
			$this->Image('../mem_picture/'.$this->picture, 14.7, 44.5, 28.5, 38.1);
		}
		
		//��ȥ����
		if($this->marry_yn == 'Y'){
			$this->text(65.5,61,'v');
		}else {
			$this->text(78.5,61,'v');
		}

		/*
		$this->SetLineWidth(0.5);
		$this->Line(14,43,44,43);
		$this->Line(14,43,14,83);
		$this->Line(44,43,44,83);
		$this->Line(14,83,44,83);
		*/

		// �׵θ�
		if ($this->auto_draw_head){
			$this->SetLineWidth(0.2);
			$this->Rect($this->left, $this->top, $this->width, 62);
		}else{
			$this->SetY($this->GetY());
		}
	}
		
}
?>
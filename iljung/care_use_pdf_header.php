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
			$report_kind = '��������纸��� �����Ģ[���� ��3ȣ����]'; 
			$title = '�����޿� �̿�� ���γ�����';
		}else if($this->mode == 'plan'){
			$report_kind = '��������纸��� �����Ģ[���� ��1ȣ����]';
			$title = '�����޿� �̿�(����) ��ȹ��';
		}else {
			$report_kind = '��������纸��� �����Ģ[���� ��4ȣ����]';
			$title = '�����޿� ����Ȯ�μ�';
		}

		$this->SetXY($this->left+5, $this->top+9);
		$this->SetFont('����','',11);
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
		$this->Cell($this->width * 0.12, $this->row_height, "�� ��      �� :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->name, 0, 1, "L");

		$this->SetXY($this->left+15, $this->GetY()+2);
		$this->Cell($this->width * 0.12, $this->row_height, "�� ��      �� :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->address, 0, 1, "L");
		
		if($this->mode == 'statement' ||
		   $this->mode == 'confirm'   ){
			
			if($this->mode != 'confirm'){
				$this->SetXY($this->left+15, $this->GetY()+2);
				$this->Cell($this->width * 0.12, $this->row_height, "�� ���Ⱓ :", 0, 0, "C");
				$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->contDt, 0, 1, "L");
			}
			$this->SetXY($this->left+15, $this->GetY()+2);
			$this->Cell($this->width * 0.12, $this->row_height, "�� �̿�Ⱓ :", 0, 0, "C");
			$this->Cell($this->width * 0.88, $this->row_height, '  '.$this->useDt, 0, 1, "L");
		}

		$this->SetXY($this->left+15, $this->GetY()+2);
		$this->Cell($this->width * 0.12, $this->row_height, "�� �޿����� :", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, "  �� �湮��� �� �湮��� �� �湮��ȣ ", 0, 1, "L");

		$this->SetX($this->left+15);
		$this->Cell($this->width * 0.12, $this->row_height, "", 0, 0, "C");
		$this->Cell($this->width * 0.88, $this->row_height, "  �� ��,�߰���ȣ �� �ܱ⺸ȣ", 0, 1, "L");
		
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
					//�湮���
					$pos_x = 56;
					$pos_y = 84.5 + $y_H;
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}else if ($svcCd == '500'){
					//�湮���
					$pos_x = 82;
					$pos_y = 84.5 + $y_H;
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}else if ($svcCd == '800'){
					//�湮��ȣ
					$pos_x = 108;
					$pos_y = 84.5 + $y_H;	
					$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				}
			}else {
				
				//�湮���
				$pos_x = 56;
				$pos_y = 84.5 + $y_H;
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
				
				//�湮���
				$pos_x = 82;
				$pos_y = 84.5 + $y_H;
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');
			
				//�湮��ȣ
				$pos_x = 108;
				$pos_y = 84.5 + $y_H;	
				$this->Image('../image/check.gif',$pos_x,$pos_y,'gif');

			}
		}
		
		$p_year = substr($this->printDT,0,4);
		$p_month = substr($this->printDT,5,2);

		if($this->mode == 'statement'){
			$this->SetXY($this->left+10, $this->GetY()+15);
			$this->MultiCell($this->width*0.9, $this->row_height+1, $this->year."�� ".$this->month."��  �����޿� �̿볻���� �Ʒ��� ���� �뺸�մϴ�.", 0, "L");
		}else if($this->mode == 'plan'){
			$this->SetXY($this->left+15, $this->GetY()+10);
			$this->MultiCell($this->width*0.9, $this->row_height+1, $this->year."�� ".$this->month."��  �����޿� �̿�(����)��ȹ�� �Ʒ��� ���� �뺸�մϴ�.", 0, "L");
		}else {
			$this->SetXY($this->left+15, $this->GetY()+10);
			$this->MultiCell($this->width*0.9, $this->row_height+1, '     '.$this->year."�� ".$this->month."���� �����޿������ �Ʒ��� ���� �����Ͽ��⿡\n ����Ȯ�μ��� �߱��մϴ�.", 0, "L");
		}

	}

	function Footer(){
		/*
		if($this->report_id == 'CLTPLANCHN'){
			$this->Text($this->left, $this->highY+6, '���޿���ȹ ���� �� ������ ���ϰ� ���� �ּ���.');
			$this->Text($this->left, $this->highY+12, '����纸ȣ�糪 ������� ������ �ð��� �ٲ� ���� ���� ���� ������ �ٲ� ��� �ۼ���');	
			
			$this->SetFont($this->font_name_kor, "B", 18);

			$this->SetXY($this->left, 275);
			$this->Cell($this->width, $this->rowHeight,$this->c_nm, 0, 0, 'C');

		}
		
		*/
		
		$this->_drawIcon();

	}
}
?>
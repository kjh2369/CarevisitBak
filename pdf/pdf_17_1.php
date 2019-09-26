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

		// Ÿ��Ʋ
		$this->SetXY($this->left, $this->left);
		$this->SetFont('����','B',25);

		$this->Cell($this->width, 5, intVal($this->month).'�� �� �� �� ��', 0, 1, 'C');

		$this->SetFont('����','',9);

		// �����
		$printDate = date('Y.m.d', mkTime());
		$this->Text($this->width - $this->GetStringWidth($printDate), $this->top - 3, '����� : '.$printDate);

		// �� Ÿ��Ʋ
		$this->SetXY($this->left, $this->top);
		$this->Cell($detailCol[0], $this->rowHeight*2, '��ȣ', 1, 0, 'C', true);
		$this->Cell($detailCol[1], $this->rowHeight*2, '����', 1, 0, 'C', true);
		$this->Cell($detailCol[2], $this->rowHeight*2, '�������', 1, 0, 'C', true);
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

		$this->Cell($detailCol[14], $this->rowHeight*2, '�����Ѿ�', 1, 0, 'C', true);
		$this->Cell($detailCol[15]+$detailCol[16]+$detailCol[17]+$detailCol[18]+$detailCol[19], $this->rowHeight, '��������', 1, 0, 'C', true);
		$this->Cell($detailCol[20], $this->rowHeight*2, ' ', 1, 0, 'C', true);
		$this->Cell($detailCol[21], $this->rowHeight*2, '�Ǽ��ɾ�', 1, 0, 'C', true);
		$this->Cell($detailCol[22], $this->rowHeight*2, '������', 1, 1, 'C', true);

		$this->SetXY($this->left+$tempCol[14], $this->top+$this->rowHeight);

		$this->SetFont('����','',8);

		$this->Cell($detailCol[15], $this->rowHeight, '���ο���', 1, 0, 'C', true);
		$this->Cell($detailCol[16], $this->rowHeight, '�ǰ�����', 1, 0, 'C', true);
		$this->Cell($detailCol[17], $this->rowHeight, '�����', 1, 0, 'C', true);
		$this->Cell($detailCol[18], $this->rowHeight, '��뺸��', 1, 0, 'C', true);
		$this->Cell($detailCol[19], $this->rowHeight, '��Ÿ����', 1, 0, 'C', true);

		// Ÿ��Ʋ
		$this->SetXY($this->left+$tempCol[2]-1, $this->top+2);
		$this->MultiCell($detailCol[3]+2, 4, "�ٷ�\n�ϼ�", 0, 'C');

		$this->SetXY($this->left+$tempCol[3]-1, $this->top+1);
		$this->MultiCell($detailCol[4]+2, 3.5, "�ٷ�\n�ð�\n��", 0, 'C');

		$this->SetXY($this->left+$tempCol[4]-1, $this->top+1);
		$this->MultiCell($detailCol[5]+2, 3.5, "�߰�\n�ٷ�\n�ð�", 0, 'C');

		$this->SetXY($this->left+$tempCol[5]-1, $this->top+1);
		$this->MultiCell($detailCol[6]+2, 3.5, "����\n�ٷ�\n�ð�", 0, 'C');

		$this->SetXY($this->left+$tempCol[6]-1, $this->top+1);
		$this->MultiCell($detailCol[7]+2, 3.5, "�ɾ�\n�ٷ�\n�ð�", 0, 'C');

		$this->SetXY($this->left+$tempCol[7], $this->top+1);
		$this->MultiCell($detailCol[8], 3.5, "�߰�\n�ٷ�\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[8], $this->top+1);
		$this->MultiCell($detailCol[9], 3.5, "����\n�ٷ�\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[9], $this->top+1);
		$this->MultiCell($detailCol[10], 3.5, "�ɾ�\n�ٷ�\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[10], $this->top+2);
		$this->MultiCell($detailCol[11], 4, "��Ÿ\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[11], $this->top+2);
		$this->MultiCell($detailCol[12], 4, "���\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[12], $this->top+2);
		$this->MultiCell($detailCol[13], 4, "��ȣ\n����", 0, 'C');

		$this->SetXY($this->left+$tempCol[19], $this->top+2);
		$this->MultiCell($detailCol[20], 4, "����\n�Ѿ�", 0, 'C');
	}

	function Footer(){
		$detailCol	= $this->detailCol();
		$tempCol	= $this->tempCol();

		// ���� ����
		$this->SetLineWidth(0.6);
		$this->line($this->left, $this->top+$this->rowHeight*2, $this->width+$this->left, $this->top+$this->rowHeight*2);
		$this->line($this->left+$tempCol[7], $this->top, $this->left+$tempCol[7], $this->top+$this->height);
		$this->line($this->left+$tempCol[14], $this->top, $this->left+$tempCol[14], $this->top+$this->height);
		$this->line($this->left, $this->top+$this->height-$this->rowHeight-1, $this->left+$this->width, $this->top+$this->height-$this->rowHeight-1);
		$this->SetLineWidth(0.2);

		// ��ü �׵θ�
		$this->SetLineWidth(0.6);
		$this->Rect($this->left, $this->top, $this->width, $this->height);
		$this->SetLineWidth(0.2);

		$this->SetFont('����','',9);
		$this->SetXY($this->left,-16);

		if ($this->totalPage == $this->PageNo()){
			$this->Cell($this->width/2, 0, '��� �δ� �����ݾ� : ���ο���('.number_format($this->centerAnnuity).'), �ǰ�����('.number_format($this->centerHealth).'), �����('.number_format($this->centerOldcare).'), ��뺸��('.number_format($this->centerEmploy).'), ���纸��('.number_format($this->centerSanje).')', 0, 0, 'L');
			$this->Cell($this->width/2, 0, 'Page '.$this->PageNo().' / '.$this->totalPage, 0, 1, 'R');
		}else{
			$this->Cell($this->width, 0, 'Page '.$this->PageNo().' / '.$this->totalPage, 0, 1, 'R');
		}
	}

	function detailCol(){
		$detailCol[0]	= 8;	//��ȣ
		$detailCol[1]	= 11;	//����
		$detailCol[2]	= 16;	//�������
		$detailCol[3]	= 7;	//�ٷ��ϼ�

		$detailCol[4]	= 9;	//�ٷνð���
		$detailCol[5]	= 7;	//����ٷνð�
		$detailCol[6]	= 7;	//���ϱٷνð�
		$detailCol[7]	= 7;	//�߰��ٷνð�

		$detailCol[8]	= 12;	//����ٷμ���
		$detailCol[9]	= 12;	//���ϱٷμ���
		$detailCol[10]	= 12;	//�߰��ٷμ���

		$detailCol[11]	= 13;	//��Ÿ����
		$detailCol[12]	= 13;	//������
		$detailCol[13]	= 13;	//��ȣ����

		$detailCol[14]	= 17;	//�����Ѿ�

		$detailCol[15]	= 13;	//���ο���
		$detailCol[16]	= 13;	//�ǰ�����
		$detailCol[17]	= 13;	//����纸��
		$detailCol[18]	= 13;	//��뺸��
		$detailCol[19]	= 13;	//��Ÿ����

		$detailCol[20]	= 13;	//�����Ѿ�
		$detailCol[21]	= 17;	//�Ǽ��ɾ�
		$detailCol[22]	= 11;	//������

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

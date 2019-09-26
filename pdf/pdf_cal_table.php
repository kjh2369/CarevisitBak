<?php
//require('korean.php');
require('pdf_'.$page_pl.'.php');

class MYPDF extends MY_PDF/*PDF_Korean*/
{
	var $debug = false;
	var $acctBox = false;
	var $year;	//�⵵
	var $month;	//��

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

	// ����������
	//var $left   = 14;
	//var $top    = 21;
	//var $width  = 182;
	//var $height = 182;

	// �� ���� ����
	var $rowHeight = 6;

	function Header(){
		if ($this->type == 's'){
			if($this->family == 'W'){
				$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����(������)';
			}else {
				$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����ǥ(������)';
			}
		}else if ($this->type == 'c'){
			//$title = intVal($this->year).'�� '.intVal($this->month).'�� ������ �޿����� ��ȹǥ';
			$title = intVal($this->year).'�� '.intVal($this->month).'�� ����� �޿��̿�(����) ��ȹ��';
		}else{

			if($this->workGbn == '2'){
				/************************************************
				�����Ȱ�������� ��� Ÿ��Ʋ Ȱ������������ ����
				************************************************/
				if($this->family == 'W'){
					$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����(Ȱ��������)';
				}else {
					$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����ǥ(Ȱ��������)';
				}
			}else {
				if($this->family == 'W'){
					$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����(��纸ȣ��)';
				}else {
					$title = intVal($this->year).'�� '.intVal($this->month).'�� ���� ����ǥ(��纸ȣ��)';
				}
			}
		}

		// Ÿ��Ʋ
		//$this->SetXY($this->left, $this->left);
		$this->SetXY($this->left, $this->top);

		//$this->SetFont('����','B',15);
		$this->SetFont($this->font_name_kor, "B", 15);

		//$this->Cell($this->width, 5, $title, 0, 1, 'C');

		if ($this->acctBox){
			if ($_SESSION['userCenterCode'] == '1234'){
				$this->Cell($this->width * 0.6, $this->rowHeight * 4, $title, 0, 1, 'C');
				$this->_SignlineSet();
			}else{
				if ($_SESSION['userCenterCode'] == '31141000005' || //���������ϰԵ����»����
					$_SESSION['userCenterCode'] == '31141000159' ){ //���κ�����������
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
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+9, "��");
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+18, "��");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.1)+(($this->width*0.1 - $this->GetStringWidth("��  ��")) / 2), $this->top+5.5, "��  ��");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("�����")) / 2), $this->top+5.5, "�����");

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//������ ���
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "������");
					}
				}else if ($_SESSION['userCenterCode'] == 'CN13C003' ){ //�ƿ쳻
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
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+9, "��");
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+18, "��");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("��  ��")) / 2), $this->top+5.5, "��  ��");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("������")) / 2), $this->top+5.5, "������");

					if ($_SESSION['userCenterCode'] == 'CN13C003'){
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "��  ��");
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
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+9, "��");
					$this->Text($this->width-($this->left*2+$this->width*0.3)+(($this->width*0.1 - $this->GetStringWidth("��")) / 2), $this->top+18, "��");

					$this->SetFont($this->font_name_kor, "", 11);
					$this->Text($this->width-($this->left*2+$this->width*0.2)+(($this->width*0.1 - $this->GetStringWidth("��  ��")) / 2), $this->top+5.5, "��  ��");
					$this->Text($this->width-$this->left*2+(($this->width*0.1 - $this->GetStringWidth("�����")) / 2), $this->top+5.5, "�����");

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//������ ���
						$this->Text(($this->width+$this->left)-($this->width*0.2)+2.7, $this->top+5.5, "������");
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
			// ������
		}else{
			if ($this->type == 's'){
				// ������
			}else if ($this->type == 'y'){
				// ��纸ȣ��
			}else{
			}
		}

		$this->SetXY($this-left, -20);
		$this->SetFont('����','B',15);
		$this->Cell($this->left+$this->width, 5, $this->centerName."(".$this->centerTel.")", 0, 1, 'C');

		if($this->centerCode == '24613000160'){
			//����(ū��������簡��������)
		}else {
			if($this->kind == '0'){
				$this->SetXY($this-left, -13);
				$this->SetFont('����','',11);
				$this->Cell($this->left+$this->width, 5, "�Աݰ���:".$this->bankName."(".$this->bankNo.") ������:".$this->bankDepos, 0, 1, 'C');
			}
		}
	}

	function drawHeader(){
		$headCol = $this->headColWidth();

		if (!$this->auto_draw_head){
			$this->SetXY($this->left, $this->GetY()+3);
		}

		$liTop = $this->GetY();

		$this->SetFont('����','B',9);
		$this->SetFillColor(220,220,220);
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['t'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C', true);
		}

		$this->SetFont('����','',11);
		$this->SetX($this->left);
		for($i=0; $i<sizeOf($headCol[t]); $i++){
			$this->Cell($headCol['w'][$i], $this->rowHeight, $headCol['c'][$i], 1, $i == sizeOf($headCol[t]) - 1 ? 1 : 0, 'C');
		}

		// �׵θ�
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

			$col['t'][0] = "�����ڸ�";
			$col['t'][1] = "�ֹε�Ϲ�ȣ";

			if ($this->kind == '4'){
				$col['t'][2] = "�޿�����";
			}else{
				$col['t'][2] = "�����������ȣ";
			}

			$col['t'][3] = "���";
			$col['t'][4] = "���κδ���";

			$col['c'][0] = $this->name;
			$col['c'][1] = $this->jumin;

			if ($this->kind == '4'){
				$col['c'][2] = '�����Ȱ������';
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
				$col['t'][0] = "Ȱ��������";
				$col['t'][1] = "Ȱ�������ι�ȣ";
			}else {
				$col['t'][0] = "��纸ȣ���";
				$col['t'][1] = "��纸ȣ���ȣ";
			}

			$col['t'][2] = "����ó";
			$col['t'][3] = "���";

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
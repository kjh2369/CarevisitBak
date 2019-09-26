<?php
require('pdf_p.php');

class MYPDF extends MY_PDF{
	var $type = 'person';
	var $salary_mm;
	var $mem_cd;		//�����ڵ�
	var $mem_nm;		//������
	var $mem_join_dt;	//�Ի���
	var $mem_out_dt;	//�����
	var $mem_no;		//������ȣ
	var $addon_caption;
	var $addon_pay;
	var $col = 0;

	function Header(){
	}

	function Footer(){
	}

	function drawTitle(){
		switch($this->type){
		case 'person':
			$this->_showTop('���α޿�����', 20, false, true);
			break;
		case 'center':
			$this->_showTop('����޿�����', 20, false, true);
			break;
		case 'month':
			$this->_showTop($this->salary_mm.'�� �޿�����', 20, false, true);
			break;
		}

		$this->set_font();

		$this->set_default_xy();

		switch($this->type){
		case 'person':
			$this->Cell($this->width * 0.10, $this->row_height, "����", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_nm, 1, 0, "L");
			$this->Cell($this->width * 0.15, $this->row_height, "�Ի���", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_join_dt, 1, 0, "C");
			$this->Cell($this->width * 0.15, $this->row_height, "�����", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_out_dt, 1, 1, "C");
			break;
		default:
			$this->Cell($this->width * 0.10, $this->row_height, "�����", 1, 0, "C", true);
			$this->Cell($this->width * 0.20, $this->row_height, $this->mem_nm, 1, 1, "L");
		}
	}

	function drawCaption(){
		$w = $this->width * 0.20;
		$h = $this->row_height;

		$this->set_default_xy();
		$basic_y = $this->GetY();
		$this->Cell($w, $h, "", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "�޿��Ѿ�(A + B + E)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "�����Ѿ�(C + D + F)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w, $h, "�������޾�", 1, 1, "L", true);

		$temp_y = $this->GetY();

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(A)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�ٹ��ϼ�", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�ٹ��ð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "�⺻��", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�����ϼ�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�����ϼ�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "���Ƚ��", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "��ȣȽ��", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�Ĵ뺸����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "����������", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.8, $h, "��������", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 18);
		$this->MultiCell($w * 0.2, $h, "��\n��\n��\n��");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(B)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "����ð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�߰��ð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "���Ͻð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�޿��ð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.45, $h, "�޾߽ð�", 1, 0, "L", true);
		$this->Cell($w * 0.35, $h, "����", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 6);
		$this->MultiCell($w * 0.2, $h, "��\n��\n��\n��");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(C)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "���ο���", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "�ǰ�����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "�����", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.80, $h, "��뺸��", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 3);
		$this->MultiCell($w * 0.2, $h, "��\n��\n��\n��");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(D)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "���ټ�", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LRB", 0, "C", true);
		$this->Cell($w * 0.80, $h, "�ֹμ�", 1, 1, "L", true);

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + 0.5);
		$this->MultiCell($w * 0.2, $h, "��\n��\n��");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(E)", 1, 1, "L", true);

		$this->set_default_xy();
		$this->Cell($w * 0.20, $h, "", "LR", 0, "C", true);
		$this->Cell($w * 0.80, $h, "���޼���", 1, 1, "L", true);

		$addon_count = sizeof($this->addon_caption[1]);

		for($i=0; $i<$addon_count; $i++){
			$this->set_default_xy();
			$this->Cell($w * 0.20, $h, "", "LR".($i +1 == $addon_count ? "B" : ""), 0, "C", true);
			$this->Cell($w * 0.80, $h, $this->addon_caption[1][$i]['subject'], 1, 1, "L", true);
		}

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + (($h * ($addon_count + 2) - ($h * 2)) / 2));
		$this->MultiCell($w * 0.2, $h, "��\n��");

		$temp_y = $temp_y2;

		$this->SetY($temp_y2);
		$this->set_default_xy();
		$this->Cell($w * 0.2, $h, "", "LTR", 0, "C", true);
		$this->Cell($w * 0.8, $h, "�հ�(F)", 1, 1, "L", true);

		$addon_count = sizeof($this->addon_caption[2]);

		for($i=0; $i<$addon_count; $i++){
			$this->set_default_xy();
			$this->Cell($w * 0.20, $h, "", "LR".($i +1 == $addon_count ? "B" : ""), 0, "C", true);
			$this->Cell($w * 0.80, $h, $this->addon_caption[2][$i]['subject'], 1, 1, "L", true);
		}

		$temp_y2 = $this->GetY();

		$this->SetXY($this->left + 1, $temp_y + (($h * ($addon_count + 1) - ($h * 2)) / 2));
		$this->MultiCell($w * 0.2, $h, "��\n��");

		$this->SetXY($this->left + $w, $basic_y);
	}
}
?>

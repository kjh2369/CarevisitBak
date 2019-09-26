<?php

if($var['hompageYn'] == 'Y' || $var['mobileYn'] == 'Y'){
	//����Ȩ���������� ��� ��
}else {
	include_once('../inc/_http_uri.php');
}


require_once('../pdf/pdf_'.$paperDir.'.php');

class MYPDF extends MY_PDF{
	var $showForm	= null;
	var $printDT	= null;
	var $orderBY	= null;
	var $svcGbn		= null;
	var $year		= null;
	var $month		= null;
	var $cpIcon		= null;
	var $cpName		= null;
	var $ctIcon		= null;
	var $ctName		= null;
	var $svcCd		= null;
	var $mode		= null;
	var $para		= null;
	var $tempVal	= null;
	var $showGbn    = null;
	var $type		= 1;
	var $domain		= null;
	var $sginCnt	= null;
	var $sginTxt	= null;
	var $debug		= false;


	function Header(){
		if (empty($this->printDT)) $this->printDT = date('Y.m.d', mktime());

		$this->SetTextColor(0,0,0);


		/*************************************
		2012.08.28 ������� ����
		*************************************/
		/*
		if (!empty($this->showForm)){
			$this->SetFont($this->font_name_kor, '', 9);
			$this->SetXY($this->left, 10);
			$this->Cell($this->width, $this->row_height, '������� : '.$this->printDT, 0, 1, 'R');
		}
		*/
		
		//�繫ȸ��(���Լ����꼭, �Ѱ�������)
		if($this->subCd == '200'){
			$faTitle = '(�湮���)';
		}else if($this->subCd == '500'){
			$faTitle = '(�湮���)';
		}else if($this->subCd == '800'){
			$faTitle = '(�湮��ȣ)';
		}else if($this->subCd == '900'){
			$faTitle = '(�־߰���ȣ)';
		}else if($this->subCd == '300'){
			$faTitle = '(�����뱸)';
		}


		if ($this->svcGbn == '200'){
			$subSubject = '(�湮���)';
		}else if ($this->svcGbn == '500'){
			$subSubject = '(�湮���)';
		}else if ($this->svcGbn == '800'){
			$subSubject = '(�湮��ȣ)';
		}else{
			$subSubject = '';
		}

		if ($this->showForm == 'ReceiveBook'){
			$col = $this->_colWidth();

			$this->_showTop2('���κδ�� ��������'.$subSubject, 20, false);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height, $this->year.'�� '.intval($this->month).'��('.($this->orderBY == '1' ? '�����ں�' : '�������ں�').')', 0, 1, 'L');

			$this->SetXY($this->left, $this->GetY());

			$this->Cell($col[0], $this->row_height * 2, 'No', 1, 0, 'C', true);

			if ($this->orderBY == '1'){
				$this->Cell($col[1], $this->row_height * 2, '����', 1, 0, 'C', true);
				$this->Cell($col[2], $this->row_height * 2, '����', 1, 0, 'C', true);
			}else{
				$this->Cell($col[2], $this->row_height * 2, '����', 1, 0, 'C', true);
				$this->Cell($col[1], $this->row_height * 2, '����', 1, 0, 'C', true);
			}

			$this->Cell($col[3], $this->row_height * 2, '����ڱ���', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height * 2, '�Աݱݾ�', 1, 0, 'C', true);
			$this->Cell($col[5]+$col[6]+$col[7], $this->row_height, '���κδ��', 1, 0, 'C', true);
			$this->Cell($col[8], $this->row_height * 2, '���', 1, 2, 'C', true);

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4], $this->GetY()-$this->row_height);
			$this->Cell($col[5], $this->row_height, '��', 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, '�޿�', 1, 0, 'C', true);
			$this->Cell($col[7], $this->row_height, '��޿�', 1, 1, 'C', true);

		}else if ($this->showForm == 'ReceiveBook2'){
			$this->left = 20;
			$this->width = 170;
			$this->height = 38;
			$this->row_height = 8.5;

			$col = $this->_colWidth();

			$this->SetFont($this->font_name_kor, '', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height, "�� ��������纸��� �����Ģ[���� ��34ȣ����] <���� 2013.6.10>", 0, 1, 'L');
			$this->SetX($this->left);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size+7);
			$this->Cell($this->width, $this->row_height * 2.2, "���κδ�� ��������".$subSubject, "LTR", 1, 'C');
			$this->SetFont($this->font_name_kor, '', $this->font_size+3);
			$this->SetXY($this->left, $this->height);
			$this->Cell($this->width*0.25, $this->row_height, "  ".$this->year."��   ".IntVal($this->month)."��", 1, 0, 'L');
			$this->Cell($this->width*0.75, $this->row_height, $this->ctName.'  ', "TBR", 1, 'R');

			$this->SetFont($this->font_name_kor, 'B', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height * 3, "����", 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height * 3, "��   ��", 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height * 3, "��   ��", 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height * 3, "", 1, 0, 'C', true);
			$this->Cell($col[4]+$col[5]+$col[6]+$col[7], $this->row_height * 1, "�����ݾ�(��)", 1, 1, 'C', true);
			$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]);
			$this->Cell($col[4], $this->row_height * 2, "��", 1, 0, 'C', true);
			$this->Cell($col[5], $this->row_height, "��   ��", 1, 0, 'C', true);
			$this->Cell($col[6]+$col[7], $this->row_height, "��޿�", 1, 1, 'C', true);
			$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]);
			$this->Cell($col[5], $this->row_height, "���κδ��", 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, "��   ��", 1, 0, 'C', true);
			$this->Cell(+$col[7], $this->row_height, "��   ��", 1, 1, 'C', true);

			$liY = $this->GetY();

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2],$this->GetY()-$this->row_height*2);
			$this->MultiCell($col[3], 5, "�����\n����", 0, "C");

			$this->SetXY($this->left,$liY);

		}else if ($this->showForm == 'IssueList'){

			if ($this->sginCnt == 0){

				$this->row_height = 8;

				$col = $this->_colWidth();

				$this->_showTop2($this->year.'�� '.$this->month.'�� �����޿������� �߱޴���', 20, false);
				$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);

			}else {

				$this->row_height = 6;

				$this->SetFont($this->font_name_kor, 'B', $this->font_size+5);

				$this->SetY($this->GetY()+5);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $this->year.'�� '.$this->month.'��', 0, 1, 'C');
				$this->SetY($this->GetY()-15);
				$this->Cell($this->width * 0.6, $this->row_height * 4,'�����޿������� �߱޴���', 0, 1, 'C');
				$this->_SignlineSet();


				$this->row_height = 8;
				$this->font_size = 11;

				$col = $this->_colWidth();

				$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);

			}

			$this->SetXY($this->left, $this->GetY());
			$this->Cell($col[0], $this->row_height, '����', 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height, '������ ��ȣ', 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height, '������', 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height, '����', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height, '�޿��Ѿ�', 1, 0, 'C', true);
			$this->Cell($col[5], $this->row_height, '���κδ�', 1, 0, 'C', true);
			$this->Cell($col[6], $this->row_height, '�߱�����', 1, 0, 'C', true);
			$this->Cell($col[7], $this->row_height, '���޹��', 1, 0, 'C', true);
			$this->Cell($col[8], $this->row_height, '��������', 1, 0, 'C', true);
			$this->Cell($col[9], $this->row_height, '����Ȯ��', 1, 1, 'C', true);

			$this->font_size = 9;

		}else if ($this->showForm == 'Iljung'){
			//�������
			$this->_showTop2(intval($this->year).'�� '.intval($this->month).'�� ���� ����ǥ(������)', 20, false);
			$this->SetFont($this->font_name_kor, 'B', $this->font_size);

			$col = $this->_colWidth();

			$this->SetXY($this->left, $this->GetY());
			$this->Cell($col[0], $this->row_height, '�����ڸ�', 1, 0, 'C', true);
			$this->Cell($col[1], $this->row_height, '�ֹε�Ϲ�ȣ', 1, 0, 'C', true);
			$this->Cell($col[2], $this->row_height, '�����������ȣ', 1, 0, 'C', true);
			$this->Cell($col[3], $this->row_height, '���', 1, 0, 'C', true);
			$this->Cell($col[4], $this->row_height, '���κδ���', 1, 1, 'C', true);

			parse_str($this->para, $val);

			#if ($_SESSION['userCenterCode'] == '1234'){
			#	print_r($val);
			#}



			$this->SetFont($this->font_name_kor, '', $this->font_size);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height, $val['name'], 1, 0, 'C');
			$this->Cell($col[1], $this->row_height, $val['jumin'], 1, 0, 'C');
			$this->Cell($col[2], $this->row_height, $val['appno'], 1, 0, 'C');
			$this->Cell($col[3], $this->row_height, $val['level'], 1, 0, 'C');
			$this->Cell($col[4], $this->row_height, $val['kind'].' / '.$val['rate'], 1, 1, 'C');

			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->GetY()-$this->row_height*2, $this->width, $this->row_height*2);
			$this->SetLineWidth(0.2);

			if ($this->type == 1){
				//����
				$laWeekly = array(0=>'��',1=>'��',2=>'ȭ',3=>'��',4=>'��',5=>'��',6=>'��');
				$liWidth = $this->width / 7; //���Ϻ� ����

				$this->SetXY($this->left, $this->GetY()+1);
				$this->SetFont($this->font_name_kor, 'B', $this->font_size);

				//����
				for($i=0; $i<7; $i++){
					switch($i){
						case 0:
							$this->SetTextColor(255,0,0);
							break;

						case 6:
							$this->SetTextColor(0,0,255);
							break;

						default:
							$this->SetTextColor(0,0,0);
					}
					$this->Cell($liWidth, $this->row_height, $laWeekly[$i], 1, ($i == 6 ? 1 : 0), 'C', true);
				}
			}else if ($this->type == 2){
				$this->_iljungTitle('2');
			}else if ($this->type == 3){
				$this->_iljungTitle('3');
			}

		}else if ($this->showForm == 'DEAL_REPORT'){
			if ($this->direction == 'P'){
				$liTmp1 = 1;
				$liTmp2 = 2.9;
			}else{
				$liTmp1 = 0.6;
				$liTmp2 = 1;
			}

			if ($this->PageNo() == 1){
				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width, $this->row_height, "[���� ��13ȣ ����] <�ż� 2012. 12. >", 0, 1, "L");

				/*********************************************************

					�⺻����

				 *********************************************************/
				$this->SetFont($this->font_name_kor, "B", $this->font_size*1.7);
				$this->SetX($this->left);
				$this->Cell($this->width*0.7,$this->row_height*3,"��纸ȣ�� ó�찳�� ���޸���",1,0,"C");

				$liY1 = $this->GetY();

				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->Cell($this->width*0.04,$this->row_height*3,"",1,0,"C");
				$this->Cell($this->width*0.26,$this->row_height*3,"",1,1,"C");

				$liY2 = $this->GetY();

				$this->SetXY($this->left+$this->width*0.7, $liY1+1);
				$this->MultiCell($this->width*0.04, 4, "û\n��\n��\n��", 0, "C");

				$this->SetXY($this->left+$this->width*0.74, $liY1+1);
				$this->MultiCell($this->width*0.26, 5.3, "�� 1. ��û��\n�� 2. �߰�û��\n�� 3. ����û��", 0, "L");

				$this->SetXY($this->left, $liY2);
				$this->Cell($this->width*0.12, $this->row_height*5.2*$liTmp1,"�����ȣ",1,0,"C");
				$this->Cell($this->width*0.24, $this->row_height*5.2*$liTmp1,$_SESSION['userCenterGiho'],1,0,"C");
				$this->Cell($this->width*0.06, $this->row_height*5.2*$liTmp1,"",1,0,"C");
				$this->Cell($this->width*0.28, $this->row_height*5.2*$liTmp1,"",1,0,"C");
				$this->Cell($this->width*0.04, $this->row_height*2.6*$liTmp1,"",1,2,"C");
				$this->Cell($this->width*0.04, $this->row_height*2.6*$liTmp1,"",1,0,"C");

				$this->SetXY($this->GetX(),$liY2);
				$this->Cell($this->width*0.26, $this->row_height*2.6*$liTmp1,"",1,2,"C");
				$this->Cell($this->width*0.26, $this->row_height*2.6*$liTmp1,"",1,1,"C");

				$liY1 = $this->GetY();

				$this->SetXY($this->left+$this->width*0.42, $liY2+($this->row_height*5.2*$liTmp1-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($this->width*0.28, 5.3, $this->ctName, 0, "C");

				$this->SetXY($this->left+$this->width*0.36, $liY2+($this->row_height*5.2*$liTmp1-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($this->width*0.06, 5.3, "���\n��Ī", 0, "C");

				$this->SetXY($this->left+$this->width*0.70, $liY2+($this->row_height*2.6*$liTmp1-$this->GetStringWidth($this->direction == 'P' ? "�� �� ��" : "�ˢ�"))/2);
				$this->MultiCell($this->width*0.04, ($this->direction == 'P' ? 3.5 : 4), ($this->direction == 'P' ? "��\n��\n��\nȣ" : "����\n��ȣ"), 0, "C");

				$this->SetXY($this->left+$this->width*0.70, $liY2+$this->row_height*2.6*$liTmp1+($this->row_height*2.6*$liTmp1-$this->GetStringWidth($this->direction == 'P' ? "�� �� ��" : "�ˢ�"))/2);
				$this->MultiCell($this->width*0.04, ($this->direction == 'P' ? 3.5 : 4), ($this->direction == 'P' ? "��\n��\n��\n��" : "�޿�\n����"), 0, "C");

				/*********************************************************

					���޳���

				 *********************************************************/
				Parse_Str($this->para,$val);

				$this->SetXY($this->left, $liY1);
				$this->SetLineWidth(0.6);
				$this->Line($this->left, $this->GetY(), $this->left+$this->width, $this->GetY());
				$this->SetLineWidth(0.2);

				$this->SetFont($this->font_name_kor, "B", $this->font_size*1.1);
				$this->SetX($this->left);
				$this->Cell($this->width, $this->row_height*1.7, "ó�찳�� ���޳���", 1, 1, "C");

				$this->SetFont($this->font_name_kor, "", $this->font_size);
				$this->SetX($this->left);
				$this->Cell($this->width*0.12, $this->row_height*1.5, "�ڵ�", 1, 0, "C");
				$this->Cell($this->width*0.20, $this->row_height*1.5, "��Ī", 1, 0, "C");
				$this->Cell($this->width*0.42, $this->row_height*1.5, "�� �޿������ð�", 1, 0, "C");
				$this->Cell($this->width*0.26, $this->row_height*1.5, "�� ���޾�", 1, 1, "C");

				$this->SetX($this->left);
				$this->Cell($this->width*0.12, $this->row_height*1.5, "", 1, 0, "C");
				$this->Cell($this->width*0.20, $this->row_height*1.5, "", 1, 0, "C");
				$this->Cell($this->width*0.42, $this->row_height*1.5, $val['time'], 1, 0, "C");
				$this->Cell($this->width*0.26, $this->row_height*1.5, $val['pay'], 1, 1, "C");

				$this->SetLineWidth(0.6);
				$this->Line($this->left, $this->GetY(), $this->left+$this->width, $this->GetY());
				$this->SetLineWidth(0.2);
			}else{
				$this->SetXY($this->left,$this->top);
			}

			/*********************************************************

				�󼼳���

			 *********************************************************/
			$col = $this->_colWidth();

			$this->SetFont($this->font_name_kor, "B", $this->font_size*1.1);
			$this->SetX($this->left);
			$this->Cell($this->width, $this->row_height*1.7, "��纸ȣ�纰 ó�찳�� ���� �󼼳���", 1, 1, "C");

			$liY3 = $this->GetY()+$this->row_height*1.8;

			$this->SetFont($this->font_name_kor, "", $this->font_size*0.9);
			$this->SetX($this->left);
			$this->Cell($col[0], $this->row_height*($this->direction == 'P' ? 4.7 : 2.8), "", 1, 0, "C");
			$this->Cell($col[1]+$col[2], $this->row_height*1.8, "��纸ȣ��", 1, 0, "C");
			$this->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9], $this->row_height*0.9, "�޿�", 1, 0, "C");
			$this->Cell($col[10]+$col[11]+$col[12]+$col[13]+$col[14], $this->row_height*1.8, "ó�찳������", 1, 0, "C");
			$this->Cell($col[15], $this->row_height*($this->direction == 'P' ? 4.7 : 2.8), "�հ�", 1, 1, "C");

			$this->SetXY($this->left+$col[0]+$col[1]+$col[2],$liY3-$this->row_height*0.9);
			$this->Cell($col[3], $this->row_height*0.9, "����", 1, 0, "C");
			$this->Cell($col[4]+$col[5]+$col[6], $this->row_height*0.9, "���⵵", 1, 0, "C");
			$this->Cell($col[7]+$col[8]+$col[9], $this->row_height*0.9, "��⵵", 1, 1, "C");

			$liY1 = $this->GetY();

			$this->SetX($this->left+$col[0]);
			$this->Cell($col[1], $this->row_height*$liTmp2, "����", 1, 0, "C");
			$this->Cell($col[2], $this->row_height*$liTmp2, $this->direction != 'P' ? "�ֹι�ȣ" : "", 1, 0, "C");

			$this->Cell($col[3], $this->row_height*$liTmp2, $this->direction != 'P' ? "û������" : "", 1, 0, "C");
			$this->Cell($col[4], $this->row_height*$liTmp2, $this->direction != 'P' ? "�޿�����" : "", 1, 0, "C");
			$this->Cell($col[5], $this->row_height*$liTmp2, "�ñ�", 1, 0, "C");
			$this->Cell($col[6], $this->row_height*$liTmp2, $this->direction != 'P' ? "����ձ޿�" : "", 1, 0, "C");
			$this->Cell($col[7], $this->row_height*$liTmp2, $this->direction != 'P' ? "�޿�����" : "", 1, 0, "C");
			$this->Cell($col[8], $this->row_height*$liTmp2, "�ñ�", 1, 0, "C");
			$this->Cell($col[9], $this->row_height*$liTmp2, $this->direction != 'P' ? "����ձ޿�" : "", 1, 0, "C");

			$this->Cell($col[10], $this->row_height*$liTmp2, $this->direction != 'P' ? "�ٹ��ð�" : "", 1, 0, "C");
			$this->Cell($col[11], $this->row_height*$liTmp2, $this->direction != 'P' ? "�ܰ�" : "", 1, 0, "C");
			$this->Cell($col[12], $this->row_height*$liTmp2, $this->direction != 'P' ? "����ݾ�" : "", 1, 0, "C");
			$this->Cell($col[13], $this->row_height*$liTmp2, $this->direction != 'P' ? "���ޱݾ�" : "", 1, 0, "C");
			$this->Cell($col[14], $this->row_height*$liTmp2, $this->direction != 'P' ? "��������" : "", 1, 1, "C");

			if ($this->direction == 'P'){
				$liY2 = $this->GetY();
				$liX1 = $this->left;

				$this->SetXY($liX1, $liY3);
				$this->MultiCell($col[0], 2, "��\n��", 0, "C");

				$liX1 += $col[0];
				$liX1 += $col[1];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� �� ��"))/2);
				$this->MultiCell($col[2], 4, "�ֹ�\n���\n��ȣ", 0, "C");

				$liX1 += $col[2];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[3], 4, "û��\n����", 0, "C");

				$liX1 += $col[3];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[4], 4, "�޿�\n����", 0, "C");

				$liX1 += $col[4];
				$liX1 += $col[5];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[6], 4, "�����\n�޿�", 0, "C");

				$liX1 += $col[6];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[7], 4, "�޿�\n����", 0, "C");

				$liX1 += $col[7];
				$liX1 += $col[8];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[9], 4, "�����\n�޿�", 0, "C");

				$liX1 += $col[9];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[10], 4, "�ٹ�\n�ð�", 0, "C");

				$liX1 += $col[10];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("��  "))/2);
				$this->MultiCell($col[11], 2, "��\n��", 0, "C");

				$liX1 += $col[11];
				$liX3  = $liX1;

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[12], 4, "����\n�ݾ�", 0, "C");

				$liX1 += $col[12];
				$liX4  = $liX1;

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[13], 4, "����\n�ݾ�", 0, "C");

				$liX1 += $col[13];

				$this->SetXY($liX1, $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2);
				$this->MultiCell($col[14], 4, "����\n����", 0, "C");

				$liY4 = $liY1+($this->row_height*2.9-$this->GetStringWidth("�� ��"))/2;

				$this->SetFontSize($this->font_size*0.5);
				$this->Text($liX3+1, $liY4+2, "1)");
				$this->Text($liX4+1.5, $liY4+2, "2)");
				$this->Text($liX1+$col[14]+1.5, $liY4-2, "3)");
				$this->SetFontSize($this->font_size*0.9);

				$this->SetY($liY2);
			}else{
				$liY2 = $this->GetY();
				$liX1 = $this->left;

				$this->SetXY($liX1, $liY2-11.5);
				$this->MultiCell($col[0], 2, "��\n��", 0, "C");

				$liX2 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11];
				$liX3 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11]+$col[12];
				$liX4 = $liX1+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9]+$col[10]+$col[11]+$col[12]+$col[13]+$col[14];

				$this->SetFontSize($this->font_size*0.5);
				$this->Text($liX2+0.5, $liY2-3, "1)");
				$this->Text($liX3+1.5, $liY2-3, "2)");
				$this->Text($liX4+5, $liY2-8.2, "3)");
				$this->SetFontSize($this->font_size*0.9);

				$this->SetY($liY2);
			}

		}else if ($this->showForm == 'ILJUNG_CALN'){
			$this->_header_ILJUNG_CALN();

		}else if ($this->showForm == 'ILJUNG_WEEKLY'){
			$this->_header_ILJUNG_WEEKLY();

		}else if ($this->showForm == 'CLIENT_STATE'){
			$this->_header_CLIENT_STATE();

		}else if ($this->showForm == 'CALN_LIST'){
			$this->_header_CALN_LIST();

		}else if ($this->showForm == 'CALN_WEEKLY'){
			$this->_header_CALN_WEEKLY();

		}else if ($this->showForm == 'HCE'){
			$this->_header_HCE();

		}else if ($this->showForm == 'REPORT2014'){

			if($this->mode == '3'){
				if($this->type == '8_1'){

					//�޿���ȹ ���� ����
					$this->SetFont($this->font_name_kor, "B", 20);
					$this->SetXY($this->left, $this->top);
					$this->Cell($this->width, $this->row_height * 20 / $this->font_size, $this->year."�� �޿���ȹ ���� ����", 0, 1, "C");

					$liY = $this->GetY()+2;
					$this->SetXY($this->left, $liY);
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "�����ڸ�", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.20, $this->row_height * 15 / $this->font_size, $this->name, 1, 0, "C");
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "����", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.15, $this->row_height * 15 / $this->font_size, $this->gender, 1, 0, "C");
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->Cell($this->width * 0.12, $this->row_height * 15 / $this->font_size, "�������", 1, 0, "C", 1);
					$this->SetFont($this->font_name_kor, "", 11);
					$this->Cell($this->width * 0.29, $this->row_height * 15 / $this->font_size, $this->birthday, 1, 1, "C");



					$liY = $this->GetY()+5;

					$this->SetFont($this->font_name_kor, "B", 13);

					$this->SetXY($this->left+$this->width * 0.88, $liY);
					$this->SetFont($this->font_name_kor, "B", 11);
					$this->MultiCell($this->width * 0.12, 5, "���\n��ȣ��\n����", 1, "C", 1);
					$this->SetFont($this->font_name_kor, "B", 13);

					$colW = $this->width * 0.40;
					$rowY = $this->row_height * 2.5;

					$this->SetXY($this->left, $liY);
					$this->Cell($this->width * 0.08, $rowY, "����", 1, 0, "C", 1);
					$this->Cell($this->width * 0.20, $rowY, "���� ��", 1, 0, "C", 1);
					$this->Cell($this->width * 0.20, $rowY, "���� ��", 1, 0, "C", 1);
					$this->Cell($this->width * 0.40, $rowY, "�������", 1, 1, "C", 1);

				}
			}

		}else if ($this->showForm == 'BUDGET'){
			global $myF;
			
			if ($this->re_gbn == 'R'){
				$title = '����';
			}else{
				$title = '����';
			}

			$title .= '���꼭('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
			$this->_SignlineSet();
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);
			

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .75, $rowH, '����', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * .25, $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * .25, $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * .25, $rowH, '��', 1, 0, 'C', 1);
			$this->SetXY($this->left + $this->width * .75, $y);
			$this->Cell($this->width * .25, $rowH * 2, '�����', 1, 1, 'C', 1);
		}else if ($this->showForm == 'SPEC'){
			global $myF;
			
			if ($this->re_gbn == 'R'){
				$title = '����';
			}else{
				$title = '����';
			}

			$title .= '����('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
			$this->_SignlineSet();
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .52, $rowH, '����', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * .16, $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * .16, $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * .20, $rowH, '��', 1, 0, 'C', 1);

			$this->SetXY($this->left + $this->width * .52, $y);
			$this->Cell($this->width * .12, $rowH * 2, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '��  ��', 1, 0, 'C', 1);
			$this->Cell($this->width * .12, $rowH * 2, '�� �� �� ��', 1, 1, 'C', 1);

			$y2 = $this->GetY();

			$this->SetXY($this->left + $this->width * .52, $y + 2.5);
			$this->MultiCell($this->width * .12, 4, "���⵵\n�����", 0, 'C');

			$this->SetXY($this->left + $this->width * .64, $y + 2.5);
			$this->MultiCell($this->width * .12, 4, "���س⵵\n�����", 0, 'C');

			$this->SetXY($this->left, $y2);
		}else if ($this->showForm == 'AR'){
			global $myF, $line_name, $sign_cd, $signer, $item, $apprq, $col;
			
			$col[0] = 0.2;
			$col[1] = 0.15;
			$col[2] = 0.07;
			$col[3] = 0.07;
			$col[4] = 0.13;
			$col[5] = 0.13;
			$col[6] = 0.25;

			$title .= 'ǰ �� ��';

			$rowH = $this->row_height;

			$this->SetFillColor(234, 234, 234);
			$this->SetY($this->top);
			$link_cd = explode('/', $this->sign_cd);
			
			if ($this->pageNo() == 1){
				
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				
				$x = 127;
				$y = 32;
				
				for($i=0; $i<count($link_cd); $i++){
					$file = '../sign/sign/MEM/'.$_SESSION['userCenterCode'].'/MEM_'.$link_cd[$i].'.jpg';
					
					if(file_exists($file) and is_file($file)){
						$this->Image($file, $x, $y, '20');	//����
					}

					$x += 17;
				}

				

				$this->_SignlineSet();

				//$this->font['size'] = 21;
				//$this->FontStyle('B');
				//$this->SetXY($this->left, $this->top);
				//$this->Cell($lx, $rowH, $title, false, 1, 'C');

				//$this->font['size'] = 9;
				//$this->FontStyle();

				$rowH = $this->row_height;

				$this->SetXY($this->left, $this->GetY()+2);
				$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

				$this->SetXY($this->left, $this->GetY() + 5);
				$this->Cell($this->width * .07, $rowH, '��', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->gwan_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '�����', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->exp_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '��������', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->mov_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '��', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->hang_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, 'ǰ������', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->ar_type, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '��������', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->app_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '��', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, $this->mog_name, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '�ڱݿ�õ', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->sof_type, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '�ⳳ����', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->rct_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '', 1, 0, 'C', 1);
				$this->Cell($this->width * .33, $rowH, '', 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, 'ǰ�Ǳݾ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->ar_amt, 1, 0, 'L');
				$this->Cell($this->width * .1, $rowH, '�������', 1, 0, 'C', 1);
				$this->Cell($this->width * .2, $rowH, $this->reg_dt, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * .15, $rowH * 5, '���� �� �뵵', 1, 0, 'C', 1);
				$this->Cell($this->width * .85, $rowH * 5, '', 1, 1, 'C');

				$y = $this->GetY();

				$this->SetXY($this->left + $this->width * .15, $y - $rowH * 5);
				$this->MultiCell($this->width * .85, 4, $this->cause, 0);

				$this->SetXY($this->left, $y + 2.2);
				$this->MultiCell($this->width, 4, "����� ���� �� �뵵�� �Ʒ��� ���� ǰ���ϰ��� �Ͽ��� ".$this->per_dt." ����\n���� �� �� �ֵ��� �Ͽ� �ֽñ� �ٶ��ϴ�.", 0, 'C');

				$this->SetXY($this->left, $y);
				$this->Cell($this->width, $rowH * 2, '', 1, 1);
			}else{
				$lx = $this->width;
			}

			$this->SetXY($this->left, $this->GetY() + 2);
			$this->Cell($this->width * $col[0], $rowH, 'ǰ��', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '�԰�', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[3], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH, '�ܰ�', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH, '�ݾ�', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[6], $rowH, '���', 1, 1, 'C', 1);	
		}else if ($this->showForm == 'BS'){
			global $myF, $year, $re_gbn, $line_name, $sign_cd, $col;

	
			$col[0] = 0.13;
			$col[1] = 0.13;
			$col[2] = 0.13;
			$col[3] = 0.05;
			$col[4] = 0.14;
			$col[5] = 0.14;
			$col[6] = 0.14;
			$col[7] = 0.14;

			
			if ($re_gbn == 'R'){
				$title = $this->year.' ����';
			}else{
				$title = $this->year.' ����';
			}
			$title .= ' ��꼭'.$faTitle;
			
			$rowH = $this->row_height;

			$this->SetY($this->top);

			
			$link_cd = explode('/', $this->sign_cd);
			
			if ($this->pageNo() == 1){
				//$lx = $this->AppLineSign($line_name, $sign_cd, $signer, -5) - $this->left;
				
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				
				$x = 127;
				$y = 32;
				
				for($i=0; $i<count($link_cd); $i++){
					$file = '../sign/sign/MEM/'.$_SESSION['userCenterCode'].'/MEM_'.$link_cd[$i].'.jpg';
					
					if(file_exists($file) and is_file($file)){
						$this->Image($file, $x, $y, '20');	//����
					}

					$x += 17;
				}

				$this->_SignlineSet();

			}else{
				$lx = $this->width;
			}

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * ($col[0]+$col[1]+$col[2]), $rowH, '����', 1, 1, 'C', 1);
			$this->SetX($this->left);
			$this->Cell($this->width * $col[0], $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '��', 1, 0, 'C', 1);

			$this->SetXY($this->left + $this->width * ($col[0]+$col[1]+$col[2]), $y);
			$this->Cell($this->width * $col[3], $rowH * 2, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH * 2, $re_gbn == 'R' ? '���κ�����' : '������', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH * 2, '�ü��δ��', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[6], $rowH * 2, '�Ŀ���', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[7], $rowH * 2, '�հ�', 1, 1, 'C', 1);

		}else if ($this->showForm == 'GL'){
			global $myF, $year, $line_name, $sign_cd, $col;

			$col[0] = 0.2;
			$col[1] = 0.13;
			$col[2] = 0.31;
			$col[3] = 0.12;
			$col[4] = 0.12;
			$col[5] = 0.12;
			
			
			$title .= '�Ѱ�������'.$faTitle;

			$rowH = $this->row_height;

			$this->SetY($this->top);

			/*if ($this->pageNo() == 1){
			}else{
				$lx = $this->width;
			}*/
			$this->_SignlineSet();

			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($lx, $rowH, '�������� : '.$this->acct_name, false, 1);

			$this->SetX($this->left);
			$this->Cell($this->width * $col[0], $rowH, '������', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[1], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[2], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[3], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[4], $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * $col[5], $rowH, '�����ܾ�', 1, 1, 'C', 1);
		}else if ($this->showForm == 'ACCTBK'){
			global $myF, $year, $month, $line_name, $sign_cd;

			$title .= '�����ⳳ��('.$this->year.'�� '.$this->month.'��)';

			$rowH = $this->row_height;

			$this->SetY($this->top);

			/*if ($this->pageNo() == 1){
			}else{
				$lx = $this->width;
			}*/
			$this->_SignlineSet();

			$this->SetFont($this->font_name_kor, "B", 21);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '�ü��� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY() + 5;

			$this->SetFillColor(234, 234, 234);
			$this->SetXY($this->left, $y);
			$this->Cell($this->width * .11, $rowH, '������', 1, 0, 'C', 1);
			$this->Cell($this->width * .2, $rowH, '��������', 1, 0, 'C', 1);
			$this->Cell($this->width * .3, $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '���Աݾ�', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '����ݾ�', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '�����ܾ�', 1, 1, 'C', 1);
		}else if ($this->showForm == 'SALARY_1'){
			
			global $myF, $year, $month;
			
			if($this->subCd == '200'){
				$subTitle = '(�湮���)';
			}else if($this->subCd == '500'){
				$subTitle = '(�湮���)';
			}else if($this->subCd == '800'){
				$subTitle = '(�湮��ȣ)';
			}

			$title = $this->year.'�� '.$this->month.'�� �Ӥ���������'.$subTitle.'�϶�ǥ';
			
			$this->SetY($this->top);
			$this->SetFont($this->font_name_kor, "B", 21);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->top);
			$this->Cell($lx, $rowH, $title, false, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height * 1.5;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '�ü��� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY();

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($this->width * .05, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .15, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '��������', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '��', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '������', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '�������޾�', 1, 1, 'C', 1);

			$this->SetXY($this->left, $y + .9);
			$this->MultiCell($this->width * .05, 4, "��\n��", 0, 'C');
			$this->SetXY($this->left + $this->width * .05, $y + .9);
			$this->MultiCell($this->width * .13, 4, "���� �Ǵ�\n����(��)", 0, 'C');
			$this->SetXY($this->left, $y + $rowH);
			
		}else if ($this->showForm == 'SALARY_2'){
			global $myF, $org_name, $year, $month;
			
			if($this->subCd == '200'){
				$subTitle = '(�湮���)';
			}else if($this->subCd == '500'){
				$subTitle = '(�湮���)';
			}else if($this->subCd == '800'){
				$subTitle = '(�湮��ȣ)';
			}

			$title = $this->year.'�� '.$this->month.'�� ������ ����'.$subTitle.' �϶�ǥ(�ΰǺ����)';

			$this->SetY($this->top);
			$this->SetFont($this->font_name_kor, "B", 21);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->top);
			$this->Cell($lx, $rowH, $title, false, 1, 'C');

			$this->SetFont($this->font_name_kor, "", 9);

			$rowH = $this->row_height * 1.5;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '�ü��� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);

			$y = $this->GetY();

			$this->SetFillColor(234, 234, 234);
			$this->SetX($this->left);
			$this->Cell($this->width * .04, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .14, $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * .13, $rowH, '�ΰǺ� ����', 1, 0, 'C', 1);
			$this->Cell($this->width * .09, $rowH, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '�޿�', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '���� ����', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '�Ͽ����', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '', 1, 0, 'C', 1);
			$this->Cell($this->width * .1, $rowH, '��', 1, 1, 'C', 1);

			$this->SetXY($this->left, $y + .9);
			$this->MultiCell($this->width * .04, 4, "��\n��", 0, 'C');
			$this->SetXY($this->left + $this->width * .7, $y + .9);
			$this->MultiCell($this->width * .1, 4, "������ ��\n����������", 0, 'C');
			$this->SetXY($this->left + $this->width * .8, $y + .9);
			$this->MultiCell($this->width * .1, 4, "��ȸ����\n�δ��", 0, 'C');
			$this->SetXY($this->left, $y + $rowH);
		}else if ($this->showForm == 'BUDGET_R'){
			global $myF;

			$title = '���꼭 ('.$this->year.')';
			
			
			$rowH = $this->row_height;
			
			//$this->SetY($this->top);
			//$lx = $this->AppLineSign($line_name, $sign_cd, null, -5) - $this->left;

			//$this->font['size'] = 21;
			//$this->FontStyle('B');
			if($this->PageNo() == 1){
				$this->SetFont($this->font_name_kor, "B", 21);
				$this->SetXY($this->left, $this->top);
				$this->Cell($this->width * 0.6, $this->row_height * 4, $title, 0, 1, 'C');
				$this->_SignlineSet();
			}
			//$this->Cell($lx, $rowH, $title, false, 1, 'C');
			
			//$this->font['size'] = $this->fontsize;
			//$this->FontStyle();
			
			$this->SetFont($this->font_name_kor, "", 11);

			$rowH = $this->row_height;

			$this->SetXY($this->left, $this->GetY()+2);
			$this->Cell($lx, $rowH, '����� : '.$myF->euckr($_SESSION['userCenterName']), false, 1);
			
			$this->SetFont($this->font_name_kor, "", 9);

			$y = $this->GetY() + 5;
			$this->SetXY($this->left, $this->getY());
			
			$this->SetFillColor(234, 234, 234);
				
			if($this->PageNo() == 1){
				$this->Cell($this->width, $rowH, '���꼭 ����', 1, 1, 'C', 1);
				$this->SetX($this->left);
				$this->Cell($this->width * .09, $rowH, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * .16, $rowH, '�з�', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '������', 1, 0, 'C', 1);
				$this->Cell($this->width * .09, $rowH, '�ܰ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '�ϹݰǼ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .08, $rowH, '�����Ǽ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '�� ��', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '���ܺδ��', 1, 0, 'C', 1);
				$this->Cell($this->width * .14, $rowH, '���κδ��', 1, 1, 'C', 1);
			}else {
				$this->Cell($this->width, $rowH, '���꼭 ����', 1, 1, 'C', 1);
	
				$this->SetX($this->left);
				$this->Cell($this->width * .07, $rowH, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * .25, $rowH, '�׸�', 1, 0, 'C', 1);
				$this->Cell($this->width * .18, $rowH, '�ݾ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .20, $rowH, $var['monthCnt'].'���� x �ݾ�', 1, 0, 'C', 1);
				$this->Cell($this->width * .30, $rowH, '���', 1, 1, 'C', 1);
			}

		}else{
		}

		if ($this->showForm == 'SW_WORK_LOG'){
			//$this->_header_SW_WORK_LOG();
			$this->_header_SW_WORK_LOG_24ho();
		}

		//������������ 2ȸ�湮���
		if ($this->showForm == 'SW_WORK_LOG2'){
			$this->_header_SW_WORK_LOG2();
		}

		//������������ ����(��ȸ������ ����)
		if ($this->showForm == 'SW_WORK_LOG_SIGN'){
			$this->_header_SW_WORK_LOG_SIGN();
		}
	}

	function Footer(){
		if ($this->showForm == 'REPORT_INSU' ||
			$this->showForm == 'CALN_WEEKLY'){
			return;
		}

		if ($this->showForm == 'SW_WORK_LOG'){
			parse_str($this->para, $val);
			$this->SetXY($this-left, -20);
			$this->SetFont($this->font_name_kor,'B',15);
			$this->Cell($this->left+$this->width, 5, $this->ctName."(".$val['phone'].")", 0, 1, 'C');
			return;
		}

		if ($this->showForm == 'HCE'){
			if ($this->mode == '71') return; //�̿� �ȳ� �� ���Ǽ�
			if ($this->mode == '92') return; //���� �� �Ƿڼ�
			if ($this->mode == '121') return; //���� ���� �ȳ���

			$this->SetXY($this-left, -10);
			$this->SetFont($this->font_name_kor,'B',10);
			$this->Cell($this->left+$this->width, 5, $this->ctName, 0, 1, 'C');

			if ($this->mode == '21') return;
		}

		if ($this->showForm == 'ReceiveBook2'){
			$this->SetXY($this->left, -20);
			$this->Cell($this->width, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'C');

			if($this->domain == 'dolvoin.net'){
				$this->_drawIcon();
			}

			return;

		}else if ($this->showForm == 'ReceiveBook'){

			if($this->domain == 'dolvoin.net'){
				$this->_drawIcon();
			}

			return;

		}else if ($this->showForm == 'IssueList'){

			if($this->domain == 'dolvoin.net'){

				$this->SetXY($this->left, -20);
				$this->SetFont($this->font_name_kor,'B',20);
				$this->Cell($this->width, 5, $this->ctName, 0, 1, 'C');

				if($this->icon != ''){
					$this->_drawIcon();
				}
			}

			return;

		}else if ($this->showForm == 'DEAL_REPORT'){
			$this->SetLineWidth(0.6);

			if ($this->PageNo() == 1){
				$this->Rect($this->left, $this->top+$this->row_height, $this->width, $this->height-$this->top-1);
			}else{
				$this->Rect($this->left, $this->top, $this->width, $this->height-$this->top+$this->row_height-1);
			}

			$this->SetLineWidth(0.2);

		}else if ($this->showForm == 'REPORT2014'){
			if($this->mode == '3'){
				if($this->type == '8_1'){
					if($this->report_id == 'CLTPLANCHN'){
						$this->Text($this->left, $this->highY+6, '���޿���ȹ ���� �� ������ ���ϰ� ���� �ּ���.');
						$this->Text($this->left, $this->highY+12, '����纸ȣ�糪 ������� ������ �ð��� �ٲ� ���� ���� ���� ������ �ٲ� ��� �ۼ���');

						$this->SetFont($this->font_name_kor, "B", 18);

						$this->SetXY($this->left, 275);
						$this->Cell($this->width, $this->rowHeight,$this->c_nm, 0, 0, 'C');

					}
				}
			}

		}else{
		}

		if ($this->showForm == 'DEAL_REPORT'){
			$this->SetXY($this->left, -20);
			$this->SetFont($this->font_name_kor, "", $this->font_size);

			if ($this->direction == 'P'){
				$this->Cell($this->width, $this->row_height, "1) �޿������ð��� ���� ���꿡�� �ڵ����� ����� �ݾ�    2) ����� �����ڿ��� �� ������ �ݾ�",0,1);
				$this->SetX($this->left);
				$this->Cell($this->width, $this->row_height, "3) ���޿��� ó�찳�� ���ޱݾ��� ���� �ݾ�",0,1);
				$this->SetXY($this->left, -7);
			}else{
				$this->Cell($this->width, $this->row_height, "1) �޿������ð��� ���� ���꿡�� �ڵ����� ����� �ݾ�        2) ����� �����ڿ��� �� ������ �ݾ�        3) ���޿��� ó�찳�� ���ޱݾ��� ���� �ݾ�",0,1);
				$this->SetXY($this->left, -13);
			}
			$this->Cell($this->width, $this->row_height, "- ".$this->PageNo()." -", 0, 0, 'C');

		}else if ($this->showForm == 'ILJUNG_CALN'){
			parse_str($this->para, $val);

			$this->SetXY($this-left, -20);
			$this->SetFont($this->font_name_kor,'B',15);
			$this->Cell($this->left+$this->width, 5, $this->ctName."(".$val['phone'].")", 0, 1, 'C');

			if ($this->mode == '101'){
				if ($_SESSION['userCenterCode'] == '24613000160' || //����(ū��������簡��������)
				   ($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //��ȸ���������� ���촩��

				}else {
					$this->SetXY($this-left, -13);
					$this->SetFont($this->font_name_kor,'',11);
					$this->Cell($this->left+$this->width, 5, $val[$this->svcCd], 0, 1, 'C');
				}

				if ($this->tempVal){
					$this->SetFont($this->font_name_kor,'',11);
					$this->SetXY($this->left, -12);
					$this->MultiCell($this->width, 4, $this->tempVal[$this->svcCd]['str']);
				}
			}else if ($this->mode == '102'){
				if ($this->tempVal){
					$this->SetFont($this->font_name_kor,'',11);
					$this->SetXY($this->left, -12);
					$this->MultiCell($this->width, 4, $this->tempVal['M']['str']);
				}
			}

		}else if ($this->showForm == 'ILJUNG_WEEKLY'){
		}else if ($this->showForm == 'REPORT2014'){
		}else if ($this->showForm == 'SW_WORK_LOG_SIGN'){
		}else if ($this->showForm == 'BUDGET' || $this->showForm == 'SPEC'){
			$this->Line($this->left, $this->GetY(), $this->width, $this->GetY());
			$this->AliasNbPages();
			$this->SetLineWidth(0.2);
			$this->line(0,285,210,285);
			$this->SetY(-12);
			$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}else if ($this->showForm == 'BS'){
			$this->Line($this->left, $this->GetY(), $this->width, $this->GetY());
			$this->SetLineWidth(0.2);
			$this->line(0,285,210,285);
			$this->SetY(-12);
			$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}else{
			if (!empty($this->showForm)){
				if ($this->showForm == 'HCE'){
					$this->SetFont($this->font_name_kor, 'B', $this->font_size);
					$this->SetXY($this->left, -10);
					$this->Cell($this->width+7, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'R');
				}else{
					$this->SetLineWidth(0.6);
					$this->Line($this->left, $this->height + 5, $this->left + $this->width, $this->height + 5);
					$this->SetLineWidth(0.2);

					$this->SetFont($this->font_name_kor, 'B', $this->font_size);
					$this->SetXY($this->left, -20);
					$this->Cell($this->width, $this->row_height, $this->PageNo().'/{nb}', 0, 0, 'C');
				}
			}

			//$this->_drawIcon();
		}
	}



	/*********************************************************

		col width

	*********************************************************/
	function _colWidth(){
		if ($this->showForm == 'ReceiveBook'){
			$col[0] = $this->width * 0.06;
			$col[1] = $this->width * 0.10;
			$col[2] = $this->width * 0.13;
			$col[3] = $this->width * 0.13;
			$col[4] = $this->width * 0.12;
			$col[5] = $this->width * 0.12;
			$col[6] = $this->width * 0.12;
			$col[7] = $this->width * 0.12;
			$col[8] = $this->width * 0.10;

		}else if ($this->showForm == 'ReceiveBook2'){
			$col[0] = $this->width * 0.07;
			$col[1] = $this->width * 0.11;
			$col[2] = $this->width * 0.13;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.17;
			$col[5] = $this->width * 0.14;
			$col[6] = $this->width * 0.14;
			$col[7] = $this->width * 0.14;

		}else if ($this->showForm == 'IssueList'){
			$col[0] = $this->width * 0.04;
			$col[1] = $this->width * 0.16;
			$col[2] = $this->width * 0.09;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.11;
			$col[5] = $this->width * 0.10;
			$col[6] = $this->width * 0.11;
			$col[7] = $this->width * 0.09;
			$col[8] = $this->width * 0.11;
			$col[9] = $this->width * 0.09;

		}else if ($this->showForm == 'Iljung'){
			$col[0] = $this->width * 0.20;
			$col[1] = $this->width * 0.25;
			$col[2] = $this->width * 0.25;
			$col[3] = $this->width * 0.10;
			$col[4] = $this->width * 0.20;

		}else if ($this->showForm == 'DEAL_REPORT'){
			if ($this->direction == 'P'){
				$col[0] = $this->width * 0.03;
				$col[1] = $this->width * 0.05;
				$col[2] = $this->width * 0.06;

				$col[3] = $this->width * 0.06;
				$col[4] = $this->width * 0.06;
				$col[5] = $this->width * 0.06;
				$col[6] = $this->width * 0.08;
				$col[7] = $this->width * 0.06;
				$col[8] = $this->width * 0.06;
				$col[9] = $this->width * 0.08;

				$col[10] = $this->width * 0.06;
				$col[11] = $this->width * 0.03;
				$col[12] = $this->width * 0.07;
				$col[13] = $this->width * 0.08;
				$col[14] = $this->width * 0.08;
				$col[15] = $this->width * 0.08;
			}else{
				$col[0] = $this->width * 0.02;
				$col[1] = $this->width * 0.05;
				$col[2] = $this->width * 0.09;

				$col[3] = $this->width * 0.06;
				$col[4] = $this->width * 0.06;
				$col[5] = $this->width * 0.05;
				$col[6] = $this->width * 0.07;
				$col[7] = $this->width * 0.06;
				$col[8] = $this->width * 0.05;
				$col[9] = $this->width * 0.07;

				$col[10] = $this->width * 0.06;
				$col[11] = $this->width * 0.05;
				$col[12] = $this->width * 0.07;
				$col[13] = $this->width * 0.08;
				$col[14] = $this->width * 0.08;
				$col[15] = $this->width * 0.08;
			}
		}else if ($this->showForm == 'HCE'){
			if ($this->mode == '1'){
				$col[] = $this->width*0.04;	//����
				$col[] = $this->width*0.04;	//�������
				$col[] = $this->width*0.09;	//��������
				$col[] = $this->width*0.10;	//����ڸ�
				$col[] = $this->width*0.10;	//������ּ�
				$col[] = $this->width*0.37;	//��㳻��
				$col[] = $this->width*0.10;	//�Ƿ���
				$col[] = $this->width*0.10;	//������
				$col[] = $this->width*0.06;	//�ʱ����
			}else if ($this->mode == '51'){
				//���ȸ�Ƿ�
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.22;
			}else if ($this->mode == '61'){
				//���񽺰�ȹ��
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.14;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.66;
			}else if ($this->mode == '81'){
				//�����������
				$col[] = $this->width * 0.13;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.57;
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.10;
			}else if ($this->mode == '91' || $this->mode == '92'){
				//���� ���� �� �Ƿڼ�
				$col[] = $this->width * 0.10;
				$col[] = $this->width * 0.18;
				$col[] = $this->width * 0.08;
				$col[] = $this->width * 0.18;
				$col[] = $this->width * 0.15;
				$col[] = $this->width * 0.31;
			}else{
				return null;
			}

		}else{
			return null;
		}

		return $col;
	}

	//��������ǥ(����) Ÿ��Ʋ
	function _iljungTitle($asType){
		$this->SetFont($this->font_name_kor, 'B', $this->font_size-1);
		$this->SetXY($this->left,$this->GetY()+2);

		if ($asType == '2'){
			$this->Cell($this->width*0.12,$this->row_height,'��������',1,0,'C',true);
			$this->Cell($this->width*0.12,$this->row_height,'������',1,0,'C',true);
			$this->Cell($this->width*0.71,$this->row_height,'������',1,0,'C',true);
			$this->Cell($this->width*0.05,$this->row_height,'Ƚ��',1,1,'C',true);
		}else if ($asType == '3'){
			$this->Cell($this->width*0.10,$this->row_height,'�޿�����',1,0,'C',true);
			$this->Cell($this->width*0.20,$this->row_height,'���񽺸�',1,0,'C',true);
			$this->Cell($this->width*0.10,$this->row_height,'Ƚ��',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'�ð�',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'����',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'�ѱ޿����',1,0,'C',true);
			$this->Cell($this->width*0.15,$this->row_height,'���κδ��',1,1,'C',true);
		}

		$this->SetFont($this->font_name_kor, '', $this->font_size-2);
	}

	//����ǥ��� ���
	function _header_ILJUNG_CALN(){
		$subject = IntVal($this->year).'�� '.IntVal($this->month).'�� ';

		parse_str($this->para, $val);

		//if($this->debug){
			//	$this->_drawJikin();
		//}

		if($this->domain == 'dolvoin.net'){
			if($this->icon != ''){
				$exp = explode('.',$this->icon);
				$exp = strtolower($exp[sizeof($exp)-1]);
				if($exp != 'bmp'){
					$this->Image('../mem_picture/'.$this->icon, 180, 10, 20, null);	//��� �ΰ�
				}
			}
		}


		if ($this->mode == '101'){

			if ($this->showGbn == 'conf'){
				$subject .= '���� ����(������)';
			}else{
				if (($this->svcCd == '2' || $this->svcCd == '4') && $_SESSION['userCenterCode'] == '1234'){
					$subject = '���� ����ǥ('.IntVal($this->year).'�� '.IntVal($this->month).'�� )';
				}else {
					$subject .= '���� ����ǥ(������)';
				}
			}
		}else if ($this->mode == '102'){
			if ($this->svcCd == '4'){
				$str = 'Ȱ��������';
			}else{
				$str = '��纸ȣ��';
			}
			if ($this->showGbn == 'conf'){
				$subject .= '�ٹ���Ȳ ����('.$str.')';
			}else{
				$subject .= '�ٹ���Ȳ ����ǥ('.$str.')';
			}
		}else{
			$subject .= '�������';
		}

		if($this->mode == '101' && $_SESSION['userCenterCode'] == '1234' && ($this->svcCd == '2' || $this->svcCd == '4')){
			$top = 15;
		}

		$this->SetXY($this->left, $this->top-$top);
		$this->SetFont($this->font_name_kor, "B", 15);

		if (($_SESSION['userCenterCode'] == '1234' && $this->mode == '101' && ($this->svcCd == '2' || $this->svcCd == '4'))){ //��ȸ���������� ���촩��
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
	    }else if (($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //��ȸ���������� ���촩��
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
		}else if (($_SESSION['userCenterCode'] == '24511000073' && $this->mode == '101')){ //������
			$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
		}else{
			if (is_null($this->sginCnt)){
				$liWidth	= 196;
				$liLeft		= 7;

				if (($_SESSION['userCenterCode'] == '31121500010' && $this->mode == '101' && $this->svcCd == '4')){ //��ȸ���������� ���촩��
					$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
				}else{
					if ($_SESSION['userCenterCode'] == '32820000004'){
						$tmpStr	= '��  ǥ';
					}else if ($_SESSION['userCenterCode'] == '24824000066' ||	//100������
							  $_SESSION['userCenterCode'] == 'CN13C003'    ||   //�ƿ쳻���κ�������
							  $_SESSION['userCenterCode'] == '24872000003' ||   //�Ƿɳ��κ�������
							  $_SESSION['userCenterCode'] == '34872000051' ||	//�Ƿɳ��κ�������
							  $_SESSION['userCenterCode'] == '32915500129' ){ //���������κ�������
						$tmpStr	= '������';
					}else if ($_SESSION['userCenterCode'] == '24413000019'){
						//õ�ȳ������պ�����
						$tmpStr	= '��  ��';
					}else if ($_SESSION['userCenterCode'] == '34211000101'){
						//�����簡��������
						$tmpStr	= '��  ��';
					}else{
						$tmpStr	= '�����';
					}

					$this->SetLineWidth(0.2);
					$this->Rect($this->width-$liLeft*2, $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.1), $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.2), $this->top, $liWidth*0.1, $this->row_height * 4);
					$this->Rect($this->width-($liLeft*2+$liWidth*0.3), $this->top, $liWidth*0.1, $this->row_height * 4);

					$this->Line($this->width-($liLeft*2+$liWidth*0.2)
							  , $this->top + $this->row_height * 1.3
							  , $this->width + 5.5
							  , $this->top + $this->row_height * 1.3);

					$this->Cell($this->width * 0.6, $this->row_height * 4, $subject, 0, 1, 'C');

					$this->SetFont($this->font_name_kor, "", 13);
					$this->Text($this->width-($liLeft*2+$liWidth*0.3)+(($liWidth*0.1 - $this->GetStringWidth("��")) / 2), $this->top+9, "��");
					$this->Text($this->width-($liLeft*2+$liWidth*0.3)+(($liWidth*0.1 - $this->GetStringWidth("��")) / 2), $this->top+18, "��");

					if ($_SESSION['userCenterCode'] == '24872000003' ||
						$_SESSION['userCenterCode'] == '34872000051' ){
						//�Ƿɳ��κ�������
						$this->SetFont($this->font_name_kor, "", 11);
						$this->Text($this->width-($liLeft*2+$liWidth*0.2)+(($liWidth*0.1 - $this->GetStringWidth("��纸ȣ��")) / 2), $this->top+5.5, "��纸ȣ��");
						$this->Text($this->width-$liLeft*2+(($liWidth*0.1 - $this->GetStringWidth($tmpStr)) / 2), $this->top+5.5, $tmpStr);
					}else {
						$this->SetFont($this->font_name_kor, "", 11);
						$this->Text($this->width-($liLeft*2+$liWidth*0.2)+(($liWidth*0.1 - $this->GetStringWidth("��  ��")) / 2), $this->top+5.5, "��  ��");
						$this->Text($this->width-$liLeft*2+(($liWidth*0.1 - $this->GetStringWidth($tmpStr)) / 2), $this->top+5.5, $tmpStr);
					}

					if ($_SESSION['userCenterCode'] == '31121500010'){
						//������ ���
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "������");
					}else if ($_SESSION['userCenterCode'] == '24824000066'){
						//100������
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "��  ��");
					}else if ($_SESSION['userCenterCode'] == '24413000019' || //õ�ȳ������պ�����
							  $_SESSION['userCenterCode'] == 'CN13C001'	   || //��Ƽ�������κ�������
							  $_SESSION['userCenterCode'] == 'CN13C003'    ){ //�ƿ쳻���κ�������

						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "��  ��");
					}else if ($_SESSION['userCenterCode'] == '24872000003' ||
							  $_SESSION['userCenterCode'] == '34872000051' ){
						//�Ƿɳ��κ�������
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "������");
					}else if ($_SESSION['userCenterCode'] == '24213000019'){
						//����簡���κ�������
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "��  ��");
					}else if ($_SESSION['userCenterCode'] == '34211000101'){
						//�����簡��������
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+2.7, $this->top+5.5, "��  ��");
					}else if ($_SESSION['userCenterCode'] == '24420000005' || //�¾���κ�������
							  $_SESSION['userCenterCode'] == '32915500129' ){ //���������κ�������

						$this->Text(($this->width+$liLeft)-($liWidth*0.2)+1, $this->top+5.5, "�繫����");
					}else if ($_SESSION['userCenterCode'] == '31129000140'){
						//�����ÿ�����(����)
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)-1, $this->top+5.5, "����å����");
					}else if ($_SESSION['userCenterCode'] == 'KN88C002'){
						//��â�ξֳ���������������
						$this->Text(($this->width+$liLeft)-($liWidth*0.2)-1, $this->top+5.5, "��ȸ������");
					}
				}
			}else{
				if ($this->sginCnt == 0){
					$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');
				}else{
					$this->Cell($this->width * 0.6, $this->row_height * 4, $subject, 0, 1, 'C');
					$this->_SignlineSet();
				}
			}
		}

		$this->SetY($this->GetY()+3);
	}


	//�ְ��� ����ǥ��� ���
	function _header_ILJUNG_WEEKLY(){

		parse_str($_POST['para'], $val);

		//����ǥ
		$col['calnWidth'][0]	= $this->width*0.1428;
		$col['calnWidth'][1]	= $this->width*0.1428;
		$col['calnWidth'][2]	= $this->width*0.1428;
		$col['calnWidth'][3]	= $this->width*0.1428;
		$col['calnWidth'][4]	= $this->width*0.1428;
		$col['calnWidth'][5]	= $this->width*0.1428;
		$col['calnWidth'][6]	= $this->width*0.1428;


		$cname   = iconv("UTF-8","EUC-KR", $_SESSION['userCenterKindName'][0]);	//�����
		$yymm    = substr(str_replace('.','', $val['from']),0,6);				//���
		$year    = substr($yymm, 0,4);											//��
		$month   = substr($yymm, 4,2);											//��

		$fromDay = intval(substr($val['from'],8,2));							//��������
		$toDay = intval(substr($val['to'],8,2));								//��������
		$calTime	= mktime(0, 0, 1, $month, 1, $year);
		//$today		= date('Ymd', mktime());
		$lastDay	= date('t', $calTime);										//���ϼ� ���ϱ�
		$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//���ۿ��� ���ϱ�

		$subject = '�ְ� ��������ǥ ('.$val['from'].'~'.$val['to'].')';

		$this->SetXY($this->left, $this->top-10);
		$this->SetFont($this->font_name_kor, "B", 15);

		$this->Cell($this->width, $this->row_height * 4, $cname, 0, 1, 'C');

		$this->SetXY($this->left, $this->top-2);
		$this->SetFont($this->font_name_kor, "B", 13);

		$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'L');

		$this->SetXY($this->left, $this->top+14);
		$this->SetFont($this->font_name_kor,'B',10);

		$k = 0;

		$weekly = array("��","��","ȭ","��","��","��","��");

		for($i=$fromDay; $i<=$toDay; $i++){

			if($i == 1){
				for($j=0; $j<$startWeek; $j++){
					$this->Cell($col['calnWidth'][$k], $this->row_height, "", 1, $k < 6 ? 0 : 1, 'C', true);
				}
			}

			$day = ($i < 10 ? '0' : '').$i;

			$date = $yymm.$day;


			//����
			$w = date('w', strtotime($date));
			$week = $weekly[$w];

			if ($week == '��'){//�Ͽ���
				$this->SetTextColor(255,0,0); //������
			}else if ($week == '��'){//�����
				$this->SetTextColor(0,0,255); //�Ķ���
			}else{//����
				$this->SetTextColor(0,0,0); //������
			}
			$this->Cell($col['calnWidth'][$k], $this->row_height, Number_Format($day).'('.$week.')', 1,  $w == 6 ? 1 : 0, 'C', true);

			$k++;
		}

		$empty = 6-$k;
		if($empty>0){
			for($i=0; $i<=$empty; $i++){
				$this->Cell($this->width*0.1428, $this->row_height, '', 1, $i == $empty ? 1 : 0 , 'C', true);
			}
		}

		$this->tempVal = $this->GetY();
	}

	function _header_CLIENT_STATE(){
		parse_str($this->para, $val);

		$cname   = iconv("UTF-8","EUC-KR", $_SESSION['userCenterKindName'][0]);	//�����

		$subject = $this->year.'�� '.intval($this->month).'�� ��������Ȳ(�簡���)';

		$this->SetXY($this->left, $this->top-10);
		$this->SetFont($this->font_name_kor, "B", 18);

		$this->Cell($this->width, $this->row_height * 4, $subject, 0, 1, 'C');


		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor, "B", 15);

		$this->Cell($this->width, $this->row_height * 4, $cname, 0, 1, 'L');
		$height = $this->row_height;

		$this->SetFont($this->font_name_kor, "B", 11);

		$this->SetX($this->left);
		$this->Cell($this->width*0.04,$height,'����',1,0,'C',true);
		$this->Cell($this->width*0.07,$height,'����',1,0,'C',true);
		$this->Cell($this->width*0.125,$height,'�ֹι�ȣ',1,0,'C',true);
		$this->Cell($this->width*0.105,$height,'������ȣ',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'����',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'����',1,0,'C',true);
		$this->Cell($this->width*0.17,$height,'���Ⱓ',1,0,'C',true);
		$this->Cell($this->width*0.18,$height,'�ּ�',1,0,'C',true);
		$this->Cell($this->width*0.12, $height,'����ó',1,0,'C',true);
		$this->Cell($this->width*0.07,$height,'��ȣ��',1,0,'C',true);
		$this->Cell($this->width*0.04,$height,'���',1,1,'C',true);

	}


	function _header_CALN_LIST(){
		$subject = $subject = IntVal($this->year).'�� '.IntVal($this->month).'�� ����';

		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,$subject,0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
		$this->SetX($this->left);
		$this->Cell($this->width*0.1,$this->row_height,'����',1,0,'C',1);
		$this->Cell($this->width*0.2,$this->row_height,'�ð�',1,0,'C',1);
		$this->Cell($this->width*0.15,$this->row_height,'�ۼ���',1,0,'C',1);
		$this->Cell($this->width*0.55,$this->row_height,'����',1,1,'C',1);
		$this->SetFont($this->font_name_kor,'',9);
	}


	function _header_CALN_WEEKLY(){
		parse_str($this->para, $val);

		$subject = $subject = IntVal($this->year).'�� '.IntVal($this->month).'�� '.$val['weekly'].'�� ����';

		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,$subject,0,1,'C');
	}


	//��ʰ��� �ش�
	function _header_HCE(){
		switch($this->mode){
			case '1':
				//��ʰ��� ����
				$this->_header_HCE_ReceiptList();
				break;

			case '21':
				//�ʱ���������
				$this->_header_HCE_Interview();
				break;

			case '31':
				//��������� - �屸
				break;

			case '41':
				//��������ǥ
				break;

			case '51':
				//���ȸ�Ƿ�
				//$this->_header_HCE_CaseMeetingList();
				break;

			case '52':
				//���ȸ�Ƿ�
				//$this->_header_HCE_CaseMeeting();
				break;

			case '61':
				//���񽺰�ȹ��
				//$this->_header_HCE_SvcPlanList();
				break;

			case '62':
				//���񽺰�ȹ��
				//$this->_header_HCE_SvcPlan();
				break;

			case '71':
				//���� �̿� �ȳ� �� ���Ǽ�
				//$this->_header_HCE_ConsentForm();
				break;

			case '81':
				//�������
				$this->_header_HCE_ProcCounsel();
				break;

			case '91':
				//���� ���� �� �Ƿڼ�
				$this->_header_HCE_SvcConnection();
				break;

			case '92':
				//���� ���� �� �Ƿڼ�
				$this->_header_HCE_SvcConnection();
				break;

			case '101':
				//����͸� �����
				//$this->_header_HCE_Monitor();
				break;

			case '102':
				//����͸� �����
				//$this->_header_HCE_Monitor();
				break;

			case '111':
				//����������
				$this->_header_HCE_ReIspt();
				break;

			case '112':
				//����������
				$this->_header_HCE_ReIspt();
				break;

			case '131':
				//����򰡼�
				//$this->_header_HCE_Evaluation();
				break;
		}
	}

	function _header_HCE_ReceiptList(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();

		$this->SetX($this->left);
		$this->Cell($col[0],$this->row_height*2,'����',1,0,'C',1);
		$this->Cell($col[1],$this->row_height,'����',"LTR",0,'C',1);
		$this->Cell($col[2],$this->row_height*2,'��������',1,0,'C',1);
		$this->Cell($col[3],$this->row_height,'����ڸ�',"LTR",0,'C',1);
		$this->Cell($col[4],$this->row_height,'������ּ�',"LTR",0,'C',1);
		$this->Cell($col[5],$this->row_height*2,'��㳻��',1,0,'C',1);
		$this->Cell($col[6],$this->row_height,'�Ƿ���',"LTR",0,'C',1);
		$this->Cell($col[7],$this->row_height*2,'������',1,0,'C',1);
		$this->Cell($col[8],$this->row_height,'�ʱ����',"LTR",1,'C',1);

		$this->SetX($this->left+$col[0]);
		$this->Cell($col[1],$this->row_height,'���',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]);
		$this->Cell($col[3],$this->row_height,'(����/����)',"LBR",0,'C',1);
		$this->Cell($col[4],$this->row_height,'(����ó)',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]);
		$this->Cell($col[6],$this->row_height,'(����ó)',"LBR",0,'C',1);
		$this->SetX($this->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]);
		$this->Cell($col[8],$this->row_height,'�ʿ俩��',"LBR",1,'C',1);

		$this->SetFont($this->font_name_kor,'',9);
	}

	function _header_HCE_Interview(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_CaseMeeting(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� ȸ �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_CaseMeetingList(){
		
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� ȸ �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "ȸ��", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "��������", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "ȸ������", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "������", 1, 0, "C", 1);
		$this->Cell($col[4], $rowH, "������", 1, 0, "C", 1);
		$this->Cell($col[5], $rowH, "��������", 1, 0, "C", 1);
		$this->Cell($col[6], $rowH, "��������", 1, 0, "C", 1);
		$this->Cell($col[7], $rowH, "���", 1, 1, "C", 1);
		
	}

	function _header_HCE_SvcPlan(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� ȹ ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_SvcPlanList(){
		$this->_header_HCE_SvcPlan();

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "ȸ��", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "�ۼ�����", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "�ۼ���", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "���", 1, 1, "C", 1);
	}

	function _header_HCE_ConsentForm(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"���� �̿� �ȳ� �� ���Ǽ�",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_ProcCounsel(){
		
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',11);



		$this->SetXY($this->left, $this->top+8);
		$this->Cell($this->width,$this->row_height,'����� : '.$this->client,0,1,'L');

		$this->SetFont($this->font_name_kor,'B',9);

		$col = $this->_colWidth();
		$rowH = $this->row_height * 1.5;

		$this->SetX($this->left);
		$this->Cell($col[0], $rowH, "����", 1, 0, "C", 1);
		$this->Cell($col[1], $rowH, "�����", 1, 0, "C", 1);
		$this->Cell($col[2], $rowH, "����", 1, 0, "C", 1);
		$this->Cell($col[3], $rowH, "�����", 1, 0, "C", 1);
		$this->Cell($col[4], $rowH, "���", 1, 1, "C", 1);
	}

	function _header_HCE_SvcConnection(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"���� ���� �� �Ƿڼ�",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_Monitor(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"����͸� �����",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_ReIspt(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_HCE_Evaluation(){
		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor,'B',18);
		$this->Cell($this->width,$this->row_height*2,"�� �� �� �� ��",0,1,'C');
		$this->SetFont($this->font_name_kor,'B',9);
	}

	function _header_SW_WORK_LOG(){
		parse_str($this->para, $val);

		$this->SetXY($this->left, $this->top-12);
		$this->SetFont($this->font_name_kor, '', 11);
		$this->Cell($this->width, $this->row_height, "[���� ��14ȣ����]", 0, 1);

		$X = $this->left;
		$Y = $this->GetY();

		$this->SetXY($X, $Y + 2);
		$this->SetFont($this->font_name_kor, "B", 17);
		$this->MultiCell($this->width * 0.54, 7, "�湮����� ��ȸ������\n�������� ����", 0, "C");

		$this->SetFont($this->font_name_kor, '', 11);
		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.54, $this->row_height * 3, "", 1);
		$this->Cell($this->width * 0.07, $this->row_height * 3, "Ȯ��", 1, 0, "C");
		$this->Cell($this->width * 0.13, $this->row_height, "��ȸ������", 1, 0, "C");

		if($_SESSION['userCenterCode'] == 'CN13C003' || //�ƿ쳻
		   $_SESSION['userCenterCode'] == 'CN13C001' ){ //��Ƽ����
			$this->Cell($this->width * 0.13, $this->row_height, "����", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "������", 1, 1, "C");
		}else {
			$this->Cell($this->width * 0.13, $this->row_height, "����å����", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "", 1, 1, "C");
		}

		$this->SetX($this->left + $this->width * 0.61);
		$this->Cell($this->width * 0.13, $this->row_height * 2, $val['regName'], 1, 0, "C");
		$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0);
		$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 1);

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.3, $this->row_height, "������ ����", 1, 0, "C");
		$this->Cell($this->width * 0.3, $this->row_height, "�������", 1, 0, "C");
		$this->Cell($this->width * 0.4, $this->row_height, "�湮�Ͻ�", 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width * 0.3, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.3, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.4, $this->row_height, $val['datetime'], 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
	}

	function _header_SW_WORK_LOG_24ho(){
		
		if($_SESSION['userCenterCode'] == '32823700100'){ //�����簡
			if($this->PageNo() == 1){ 
				$pageYN = 'Y';
			}else {
				$pageYN = 'N';
			}
		}else {
			$pageYN = 'Y';
		}
		
		
		parse_str($this->para, $val);
		
		if ($pageYN == 'Y'){	
			$this->SetXY($this->left, $this->top-12);
			$this->SetFont($this->font_name_kor, '', 11);
			$this->Cell($this->width, $this->row_height, "[���� ��24ȣ����]", 0, 1);

			$X = $this->left;
			$Y = $this->GetY();
			
			$this->SetXY($X, $Y + 2);
			$this->SetFont($this->font_name_kor, "B", 17);
			if($va['regYymm'] > '201701'){
				$this->MultiCell($this->width * 0.54, 7, "���α׷� ������ �� �湮�����\n��ȸ������ �������� ����", 0, "C");
			}else {
				$this->MultiCell($this->width * 0.54, 7, "���α׷� ������ �� \n��ȸ������ �������� ����", 0, "C");
			}
			$this->SetFont($this->font_name_kor, '', 11);
			$this->SetXY($X, $Y);
			$this->Cell($this->width * 0.54, $this->row_height * 3, "", 1);
			$this->Cell($this->width * 0.07, $this->row_height * 3, "Ȯ��", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height, "�湮��", 1, 0, "C");

			if($_SESSION['userCenterCode'] == 'CN13C003' || //�ƿ쳻
			   $_SESSION['userCenterCode'] == 'CN13C001' ){ //��Ƽ����
				$this->Cell($this->width * 0.13, $this->row_height, "����", 1, 0, "C");
				$this->Cell($this->width * 0.13, $this->row_height, "������", 1, 1, "C");
			}else {
				$this->Cell($this->width * 0.13, $this->row_height, "��纸ȣ��", 1, 0, "C");
				$this->Cell($this->width * 0.13, $this->row_height, "����å����", 1, 1, "C");
			}

			//if ($_SESSION['userCenterCode'] == '1234'){
				//$sign = '../mm/sign/member/'.$_SESSION['userCenterCode'].'/'.$val['regKey'].'_r.jpg';
				

				
				
				//�湮��
				$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-3.jpg';
				if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-3.jpg';

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.61 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}
				
				

				//��纸ȣ��
				$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-2.jpg';
				if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-2.jpg';

				//if($debug) echo $sign.'/';

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.74 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}

				//����å����
				if (is_numeric($val['signManager'])){
					$sign = '../sign/sign/manager/'.$_SESSION['userCenterCode'].'/'.$val['signManager'].'.jpg';
				}else{
					$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$_SESSION['regYymm'].'/'.$_SESSION['regKey'].'/'.$_SESSION['regSeq'].'_7-4.jpg';
				}

				if (is_file($sign)){
					$tmpImg = getImageSize($sign);
					$picW = $tmpImg[0] * 0.04 * 0.1;
					$picH = $tmpImg[1] * 0.04 * 0.1;

					$prtW = $this->width * 0.13 - 2;
					$prtH = $this->row_height * 2 - 2;

					if ($picW > $cpsW || $picH > $cpsH){
						$picR = 1;

						if ($picW > $picH){
							$picR = $picH / $picW;
							$picW = $prtW;
							$picH = $prtH * $picR;
						}else{
							$picR = $picW / $picH;
							$picH = $prtH;
							$picW = $prtW * $picR;
						}
					}

					$gabL = ($this->width * 0.13 - $picW) / 2;
					$gabT = ($this->row_height * 2 - $picH) / 2;

					if ($gabL < 0) $gabL = 0;
					if ($gabT < 0) $gabT = 0;

					$this->Image($sign, $X + $this->width * 0.87 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
				}
			//}

			$this->SetX($this->left + $this->width * 0.61);
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0, "C");
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 0);
			$this->Cell($this->width * 0.13, $this->row_height * 2, "", 1, 1);
		}	

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.15, $this->row_height, "������ ����", 1, 0, "C");
		$this->Cell($this->width * 0.17, $this->row_height, "�������", 1, 0, "C");
		$this->Cell($this->width * 0.21, $this->row_height, "�����������ȣ", 1, 0, "C");
		$this->Cell($this->width * 0.28, $this->row_height, "�湮�Ͻ�", 1, 0, "C");
		$this->Cell($this->width * 0.19, $this->row_height, "������(��ȣ��)", 1, 1, "C");

		$sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/'.$val['regSeq'].'_7-1.jpg';
		if (!is_file($sign)) $sign = '../sign/sw_log/'.$_SESSION['userCenterCode'].'/'.$val['regYymm'].'/'.$val['regKey'].'/_7-1.jpg';

		if (is_file($sign)){
			$tmpImg = getImageSize($sign);
			$picW = $tmpImg[0] * 0.04 * 0.1;
			$picH = $tmpImg[1] * 0.04 * 0.1;

			$prtW = $this->width * 0.13 - 2;
			$prtH = $this->row_height * 2 - 2;

			if ($picW > $cpsW || $picH > $cpsH){
				$picR = 1;

				if ($picW > $picH){
					$picR = $picH / $picW;
					$picW = $prtW;
					$picH = $prtH * $picR;
				}else{
					$picR = $picW / $picH;
					$picH = $prtH;
					$picW = $prtW * $picR;
				}
			}

			$gabL = ($this->width * 0.13 - $picW) / 2;
			$gabT = ($this->row_height * 2 - $picH) / 2;

			if ($gabL < 0) $gabL = 0;
			if ($gabT < 0) $gabT = 0;

			$this->Image($sign, $X + $this->width * 0.81 + 0.3 + $gabL, $Y + $this->row_height + $gabT, $picW, $picH);
		}

		$this->SetX($X);
		$this->Cell($this->width * 0.15, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.17, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.21, $this->row_height, $val['appNo'], 1, 0, "C");
		$this->Cell($this->width * 0.28, $this->row_height, $val['datetime'], 1, 0, "C");
		$this->Cell($this->width * 0.19, $this->row_height * 2, "", 1, 1, "C");

		$this->SetXY($X, $this->GetY() - $this->row_height);
		$this->Cell($this->width * 0.81, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width * 0.81), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
		
	}

	function _header_SW_WORK_LOG2(){
	
		if($_SESSION['userCenterCode'] == '32823700100'){ //�����簡
			if($this->PageNo() == 1){ 
				$pageYN = 'Y';
			}else {
				$pageYN = 'N';
			}
		}else {
			$pageYN = 'Y';
		}

		parse_str($this->para, $val);
		
		if ($pageYN == 'Y'){
			
			$this->SetXY($this->left, $this->top-12);
			$this->SetFont($this->font_name_kor, '', 11);
			$this->Cell($this->width, $this->row_height, "[���� ��14ȣ����]", 0, 1);

			$X = $this->left;
			$Y = $this->GetY();


			$this->SetFont($this->font_name_kor, "B", 15);
			
			$this->SetXY($X, $Y);
			
			if($va['regYymm'] > '201701'){
				$this->Cell($this->width, $this->row_height * 2, "���α׷� ������ �� �湮����� ��ȸ������ �������� ����", 1, 1,'C');
			}else {
				$this->Cell($this->width, $this->row_height * 2, "���α׷� ������ �� ��ȸ������ �������� ����", 1, 1,'C');
			}
		}

		$this->SetFont($this->font_name_kor, '', 11);

		$X = $this->left;
		$Y = $this->GetY() + 3;

		$this->SetXY($X, $Y);
		$this->Cell($this->width * 0.20, $this->row_height, "������ ����", 1, 0, "C");
		$this->Cell($this->width * 0.20, $this->row_height, "�������", 1, 0, "C");
		$this->Cell($this->width * 0.36, $this->row_height, "�����������ȣ", 1, 0, "C");
		$this->Cell($this->width * 0.24, $this->row_height, "������(��ȣ��)", 1, 1, "C");

		$this->SetX($X);
		$this->Cell($this->width * 0.20, $this->row_height, $val['name'], 1, 0, "C");
		$this->Cell($this->width * 0.20, $this->row_height, $val['level'], 1, 0, "C");
		$this->Cell($this->width * 0.36, $this->row_height, $val['appNo'], 1, 0, "C");
		$this->Cell($this->width * 0.24, $this->row_height * 2, "", 1, 1, "C");

		$this->SetXY($X, $this->GetY() - $this->row_height);
		$this->Cell($this->width * 0.76, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width * 0.76), 1, 1, "L");

		//$this->SetX($X);
		//$this->Cell($this->width, $this->row_height, $this->_splitTextWidth($val['addr'],$this->width), 1, 1, "L");

		$this->SetFont($this->font_name_kor, '', 9);
		$this->SetY($this->GetY() + 3);
		
	}


	function _header_SW_WORK_LOG_SIGN(){
		$subject = IntVal($this->year).'�� '.IntVal($this->month).'�� ';

		parse_str($this->para, $val);


		$this->SetXY($this->left, $this->top);
		$this->SetFont($this->font_name_kor, "B", 15);

		$liWidth	= 196;
		$liLeft		= 0;


		$tmpStr	= '�����';

		if ($this->PageNo() == 1){
			$this->SetLineWidth(0.2);
			//$this->Rect($this->width-$liLeft*2, $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.03), $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.13), $this->top, $liWidth*0.1, $this->row_height * 4);
			$this->Rect($this->width-($liLeft*2+$liWidth*0.23), $this->top, $liWidth*0.1, $this->row_height * 4);

			$this->Line($this->width-($liLeft*2+$liWidth*0.13)
					  , $this->top + $this->row_height * 1.3
					  , $this->width+13.5
					  , $this->top + $this->row_height * 1.3);

			$this->Cell($this->width * 0.6, $this->row_height * 4, '', 0, 1, 'C');

			$this->SetXY($this->left,$this->GetY()-18);
			$this->MultiCell(100, 7, "�湮��ȸ������ ����\n(�Ⱓ : ".str_replace('-','.',$val['fromDt'])." ~ ".str_replace('-','.',$val['toDt']).")", 0, "C");

		}else {
			/*
			$this->SetXY($this->left+40,$this->GetY()-5);
			$this->MultiCell(100, 7, "�湮��ȸ������ ����\n(�Ⱓ : ".str_replace('-','.',$val['fromDt'])." ~ ".str_replace('-','.',$val['toDt']).")", 0, "C");
			*/
		}


		if ($this->PageNo() == 1){
			$this->SetFont($this->font_name_kor, "", 13);
			$this->Text($this->width-($liLeft*2+$liWidth*0.23)+(($liWidth*0.1 - $this->GetStringWidth("��")) / 2), $this->top+9, "��");
			$this->Text($this->width-($liLeft*2+$liWidth*0.23)+(($liWidth*0.1 - $this->GetStringWidth("��")) / 2), $this->top+18, "��");


			$this->SetFont($this->font_name_kor, "", 11);
			$this->Text($this->width-($liLeft*2+$liWidth*0.13)+(($liWidth*0.1 - $this->GetStringWidth("������")) / 2), $this->top+5.5, "������");
			//$this->Text(($this->width+$liLeft*2)-($liWidth*0.2), $this->top+5.5, "��  ��");
			$this->Text($this->width-($liLeft*2+$liWidth*0.03)+(($liWidth*0.1 - $this->GetStringWidth("�����")) / 2), $this->top+5.5, "�����");

			if($val['printDt'] != ''){
				$this->SetXY($this->left*10, $this->GetY()+7);
				$this->Cell($this->width*0.15, $this->row_height, '�������: ', 0, 0, 'R');
				$this->Cell($this->width*0.15, $this->row_height, str_replace('-','.',$val['printDt']), 0, 0, 'C');
			}
		}

		$this->SetXY($this->left, $this->GetY()+10);
		$this->Cell($this->width*0.15, $this->row_height, '����', 1, 0, 'C', true);
		$this->Cell($this->width*0.15, $this->row_height, '������', 1, 0, 'C', true);
		$this->Cell($this->width*0.35, $this->row_height, '����', 1, 0, 'C', true);
		$this->Cell($this->width*0.35, $this->row_height, '���û���', 1, 1, 'C', true);

	}
}
?>
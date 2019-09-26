<?php
include_once('../pdf/pdf_p.php');

class MYPDF extends MY_PDF{
	var $type = null;
	var $root = null;

	var $k_cd = null; #�����ȣ
	var $k_nm = null; #�����

	var $m_nm       = null;
	var $m_birthday = null;
	var $m_mobile   = null;

	var $c_cd = null;
	var $c_nm = null;
	var $c_phone  = null;
	var $c_mobile = null;
	var $c_postno  = null;
	var $c_addr = null;
	var $c_addr_dtl = null;
	var $c_parent_nm  = null;
	var $c_parent_rel = null;
	var $c_parent_phone  = null;
	var $eduLvl  = null;

	function Header(){
		if ($this->type == 'HUMAN'){
			/**************************************************
				Ÿ��Ʋ ���
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 15);
			$this->SetXY($this->left, $this->top);
			$this->Cell($this->width, 15, '�����ڿ�����', 0, 1, 'C');



			/**************************************************
				�⺻ ��Ʈ ����
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);



			/**************************************************
				�������
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.13, $this->row_height, '�����ȣ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->k_cd, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '�����', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.54, $this->row_height, $this->k_nm, 1, 1, 'L');



			/**************************************************
				��������
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.13, $this->row_height, '������', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->m_nm, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '�������', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.21, $this->row_height, $this->m_birthday, 1, 0, 'L');
			$this->Cell($this->width * 0.13, $this->row_height, '����ó', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.20, $this->row_height, $this->m_mobile, 1, 1, 'L');
		}else if ($this->type == 'VISIT' ||
				  $this->type == 'PHONE' ||
				  $this->type == 'STRESS' ||
				  $this->type == 'CASE'){

			if ($this->sginCnt > 0){
				$this->SetXY($this->left, $this->top-5);
				$this->SetFont('����','B',15);
				$this->SetLineWidth(0.2);

				//$this->SetY($this->GetY()+5);
				//$this->Cell($this->width * 0.6, $this->row_height * 4, $this->year.'�� '.$this->month.'��', 0, 1, 'C');
				$this->SetY($this->GetY());
				switch($this->type){
					case 'VISIT':
						$this->Cell($this->width*0.6, 32, '�� �湮��� �����', 0, 1, 'C');
						break;

					case 'PHONE':
						$this->Cell($this->width*0.6, 32, '��ȭ �湮��� �����', 0, 1, 'C');
						break;

					case 'STRESS':
						$this->Cell($this->width*0.6, 32, '�Ҹ� �� ����ó�������', 0, 1, 'C');
						break;

					case 'CASE':
						$this->Cell($this->width*0.6, 32, '��ʰ��� ȸ��', 0, 1, 'C');
						break;
				}

				$this->_SignlineSet();

				$this->SetY($this->GetY());

			}else{

				/**************************************************
					Ÿ��Ʋ ���
				**************************************************/
				$this->SetFont($this->font_name_kor, 'B', 15);
				$this->SetXY($this->left, $this->top);

				switch($this->type){
					case 'VISIT':
						$this->Cell($this->width, 15, '�� �湮��� �����', 0, 1, 'C');
						break;

					case 'PHONE':
						$this->Cell($this->width, 15, '��ȭ �湮��� �����', 0, 1, 'C');
						break;

					case 'STRESS':
						$this->Cell($this->width, 15, '�Ҹ� �� ����ó�������', 0, 1, 'C');
						break;

					case 'CASE':
						$this->Cell($this->width, 15, '��ʰ��� ȸ��', 0, 1, 'C');
						break;
				}
			}


			/**************************************************
				�⺻ ��Ʈ ����
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);



			/**************************************************
				�������
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.15, $this->row_height, '�����ȣ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->k_cd, 1, 0, 'L');
			$this->Cell($this->width * 0.15, $this->row_height, '�� �� ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.45, $this->row_height, $this->k_nm, 1, 1, 'L');
			
			if(str_replace('-','', $this->c_postno)==''){
				$post = '';
			}else {
				$post = "(".str_replace('-','', $this->c_postno).") ";
			}
			$juso =  explode('<br />',nl2br($this->c_addr));
			
			/**************************************************
				������
			**************************************************/
			if ($this->root == 'MEMBER'){
				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '�� �� ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_nm, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height * 2, '�� �� ó', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_phone, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '�ֹι�ȣ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_cd, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height, '', 0, 0, 'C');
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_mobile, 1, 1, 'L');

				$pos[sizeof($pos)] = array('x'=>$this->left + $this->width * 0.15, 'y'=>$this->GetY() + $this->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$this->width * 0.40, 'height'=>5, 'align'=>'L', 'text'=>$post.$juso[0]." ".$this->c_addr_dtl);

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height * 3, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height * 3, '', 1, 0, 'C');
				$this->Cell($this->width * 0.15, $this->row_height * 3, '��   ��', 1, 0, 'C', 1);

				$this->SetX($this->left + $this->width * 0.55);
				$this->Cell($this->width * 0.45, $this->row_height * 3, '', 1, 1, 'L');

				
				$this->SetLineWidth(0.6);
				$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);
				$this->SetLineWidth(0.2);

			}else{
				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '�� �� ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_nm, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height * 2, '�� �� ó', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_phone, 1, 1, 'L');

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height, '�ֹι�ȣ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height, $this->c_cd, 1, 0, 'L');
				$this->Cell($this->width * 0.15, $this->row_height, '', 0, 0, 'C');
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_mobile, 1, 1, 'L');

				$pos[sizeof($pos)] = array('x'=>$this->left + $this->width * 0.15, 'y'=>$this->GetY() + $this->row_height * 0.2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$this->width * 0.40, 'height'=>5, 'align'=>'L', 'text'=>$post.$juso[0]." ".$this->c_addr_dtl);

				$this->SetX($this->left);
				$this->Cell($this->width * 0.15, $this->row_height * 3, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.40, $this->row_height * 3, '', 1, 0, 'C');
				$this->Cell($this->width * 0.15, $this->row_height * 3, '�� ȣ ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_nm, 1, 1, 'L');

				$this->SetX($this->left + $this->width * 0.70);
				$this->Cell($this->width * 0.10, $this->row_height, '��   ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_rel, 1, 1, 'L');

				$this->SetX($this->left + $this->width * 0.70);
				$this->Cell($this->width * 0.10, $this->row_height, '�� �� ó', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.20, $this->row_height, $this->c_parent_phone, 1, 1, 'L');
					
				$this->SetLineWidth(0.6);
				$this->Rect($this->left, $this->GetY() - $this->row_height * 6, $this->width, $this->row_height * 6);
				$this->SetLineWidth(0.2);

			}



			$tmp_Y = $this->getY();	//���������̺�������̰�������

			set_array_text($this, $pos);
			unset($pos);

			$this->setY($tmp_Y);

		}else if ($this->type == 'HUMAN2'){

			/**************************************************
				�⺻ ��Ʈ ����
			**************************************************/
			$this->SetFont($this->font_name_kor, '', 10);


			/**************************************************
				��������
			**************************************************/
			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height*7, '<�� ��>', 1, 0, 'C');

			/**************************************************
				Ÿ��Ʋ ��Ʈ ����
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 25);

			$this->Cell($this->width * 0.84, $this->row_height*3.5, '�� �� �� �� ī ��', 1, 1, 'C');


			$this->SetFont($this->font_name_kor, '', 10);

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '', 'LR', 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '�� ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_nm, 1, 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '�ֹε�Ϲ�ȣ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_jumin, 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '', 'LR', 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '����ó', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, ($this->m_tel != '' ? $this->m_tel : $this->m_mobile), 1, 0, 'C');
			$this->Cell($this->width * 0.17, $this->row_height, '�޴���', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height, $this->m_mobile, 1, 1, 'C');

			$this->SetX($this->left + $this->width * 0.16);
			$this->Cell($this->width * 0.17, $this->row_height, '�� ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.67, $this->row_height, ' '.$this->m_addr, 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '�ξ簡��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 0, 'C');
			$this->Cell($this->width * 0.16, $this->row_height, '�������', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height*4, '�̷�', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.11, $this->row_height, '���� �з�', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '  '.$this->eduLvl , 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '��      ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '��      ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.05, $this->row_height, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			$this->SetXY($this->left+$this->width*0.50, $this->getY()-$this->row_height*4);
			$this->Cell($this->width * 0.05, $this->row_height*4, '����', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.11, $this->row_height, '�ذ���', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '������', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, ' '.$this->m_retire_dt, 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '��   ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left+$this->width*0.50);
			$this->Cell($this->width * 0.05, $this->row_height*4, '', 0, 0, 'C');
			$this->Cell($this->width * 0.11, $this->row_height, '��ǰû�� ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'L');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.16, $this->row_height, '�����(���Ⱓ)', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '      '.$this->m_from_dt.' ~ ', 1, 0, 'L');
			$this->Cell($this->width * 0.16, $this->row_height, '�ٷΰ�� ������', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.34, $this->row_height, '', 1, 1, 'C');

			if (Is_File($this->m_picture)){
				$this->Image('../mem_picture/'.$this->m_picture, 14.3, 10.5, 28.5, 38.1);
			}

		}else if ($this->type == 'AGREE'){

			/**********************************************

				2012.09.27 ���κδ�� �޿� �������� ���Ǽ�

			************************************************/

			/**************************************************
				Ÿ��Ʋ ���
			**************************************************/
			$this->SetFont($this->font_name_kor, 'B', 15);
			$this->SetXY($this->left, $this->top);

			$this->Cell($this->width, 15, '���κδ�� �޿� �������� ���Ǽ�', 0, 1, 'C');


			$this->SetFont($this->font_name_kor, '', 11);


			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '��纸ȣ���', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, $this->m_nm, 1, 0, 'C');
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '�ֹε�Ϲ�ȣ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, $this->m_jumin, 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '�ڰݹ�ȣ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '', 1, 0, 'C');
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '��    ȭ', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, ($this->m_mobile != '' ? $this->m_mobile : $this->m_tel), 1, 1, 'C');

			$this->SetX($this->left);
			$this->Cell($this->width * 0.25, $this->row_height*1.2, '��    ��', 1, 0, 'C', 1);
			$this->Cell($this->width * 0.75, $this->row_height*1.2, '   '.$this->m_addr, 1, 1, 'L');


			$this->SetLineWidth(0.6);
			$this->Rect($this->left, $this->GetY()-$this->row_height*3.6, $this->width, $this->height*0.88);
			$this->SetLineWidth(0.2);

		}
	}

	function Footer(){
		$this->_drawIcon();
	}

	function draw_header($type, $pos_h){

		if ($this->type == 'HUMAN'){
			if ($type == 'edu'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.435, $this->row_height, '�������� ����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.435, $this->row_height, '��Ÿ ����', 1, 1, 'C', 1);

				$this->SetX($this->left + $this->width * 0.130);
				$this->Cell($this->width * 0.180, $this->row_height, '�������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.075, $this->row_height, '�ð�', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '�������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.075, $this->row_height, '�ð�', 1, 1, 'C', 1);
			}else if ($type == 'lcs'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.220, $this->row_height, '�ڰ�������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.215, $this->row_height, '�ڰ�����ȣ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.290, $this->row_height, '�߱ޱ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '�߱�����', 1, 1, 'C', 1);
			}else if ($type == 'rnp'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.100, $this->row_height, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.625, $this->row_height, '����', 1, 1, 'C', 1);
			}
		}

		if ($this->type == 'HUMAN2'){

			if ($type == 'rec'){ //�Ի������
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.260, $this->row_height, '�ٹ��Ⱓ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.200, $this->row_height, '�����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '�� ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.170, $this->row_height, '������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '�� ��', 1, 1, 'C', 1);
			}else if ($type == 'edu'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.120, $this->row_height, '��������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.180, $this->row_height, '�������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.200, $this->row_height, '������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.260, $this->row_height, '�����Ⱓ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.110, $this->row_height, '�ð�', 1, 1, 'C', 1);
			}else if ($type == 'lcs'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.220, $this->row_height, '�ڰ�������', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.215, $this->row_height, '�ڰ�����ȣ', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.290, $this->row_height, '�߱ޱ��', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '�߱�����', 1, 1, 'C', 1);
			}else if ($type == 'rnp'){
				$this->Cell($this->width * 0.130, $pos_h, '', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.145, $this->row_height, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.100, $this->row_height, '����', 1, 0, 'C', 1);
				$this->Cell($this->width * 0.625, $this->row_height, '����', 1, 1, 'C', 1);
			}

		}

	}
}
?>
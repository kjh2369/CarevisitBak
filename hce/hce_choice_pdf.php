<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	����� ��������ǥ
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$G1 = $pdf->GetStringWidth("��");
	$G2 = $pdf->GetStringWidth("   ");
	

	$orgNo = $_SESSION['userCenterCode'];
	$userArea = $_SESSION['userArea'];
	$date  = $var['subId'];
	
	
	//����ڸ�
	$sql = 'SELECT	m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	//����������
	$sql = 'SELECT	addr
			,		addr_dtl
			,		phone
			,		mobile
			,		rcver_nm
			FROM	hce_receipt
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$hce->SR.'\'
			AND		IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$addr	= $row['addr'].' '.$row['addr_dtl'];
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$mobile	= $myF->phoneStyle($row['mobile'],'.');
	$manager= $row['rcver_nm'];

	Unset($row);
	

	//����� ��������
	$sql = 'SELECT	count(*)
			FROM	hce_choice_cn
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$hce->SR.'\'
			AND		IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt_seq	= \''.$hce->rcpt.'\'
			AND		chic_seq	= \''.$var['idx'].'\'';
	 
	$count = $conn->get_data($sql);
	
	
	if($userArea == '05' && $count > 0 && str_replace('-','',$date) >= '20180101'){
		//����� ��������
		$sql = 'SELECT	*
				FROM	hce_choice_cn
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$hce->SR.'\'
				AND		IPIN		= \''.$hce->IPIN.'\'
				AND		rcpt_seq	= \''.$hce->rcpt.'\'
				AND		chic_seq	= \''.$var['idx'].'\'';
		
		$choice = $conn->get_array($sql);
		
		

	}else {
		//����� ��������
		$sql = 'SELECT	*
				FROM	hce_choice
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$hce->SR.'\'
				AND		IPIN		= \''.$hce->IPIN.'\'
				AND		rcpt_seq	= \''.$hce->rcpt.'\'
				AND		chic_seq	= \''.$var['idx'].'\'';

		$choice = $conn->get_array($sql);
	}
	
	if($userArea == '05' && $choice['chic_dt'] >= '20180101'){
	
		
		$pdf->row_height = 5;


		$point = Array(
				//��������
				'A'=>Array(	'1'=>Array('1'=>5,'2'=>3,'3'=>2,'4'=>0)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2,'4'=>2.5))

				//��������
			,	'B'=>Array(	'1'=>Array('1'=>0,'2'=>1,'3'=>2,'4'=>3,'5'=>4)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2))

				//���������� �Ǽ��ɾ�
			,	'C'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1,'7'=>0)
						,	'2'=>Array('1'=>1.5))

				//�Ŀ��ڼ�
			,	'D'=>Array(	'2'=>Array('1'=>-0.5,'2'=>-1,'3'=>-2))

				//�ǰ����� ��ü��
			,	'E'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//�ǰ����� ������
			,	'F'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//��ֵ��
			,	'G'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1)
						,	'2'=>Array('1'=>1.5)
						,	'3'=>Array('1'=>1.5))

				//ADL���
			,	'H'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1))

				//�����
			,	'I'=>Array(	'1'=>Array('1'=>4,'2'=>3,'3'=>2,'4'=>1))

				//�緮
			,	'J'=>Array(	'1'=>Array('1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5))
		);

		
		//Ÿ��Ʋ
		//$pdf->SetXY($pdf->left, $pdf->top);
		//$pdf->SetFont($pdf->font_name_kor,'B',18);
		//$pdf->Cell($pdf->width,$pdf->row_height*2,"����� ��������ǥ",0,1,'C');
		//$pdf->SetFont($pdf->font_name_kor,'',9);

		$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
		$pdf->Cell($pdf->width,$pdf->row_height,"��ʰ����� : ".$manager,0,1,'L');

		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"CLIENT",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$name,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"�� �� �� ��",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$myF->dateStyle($choice['chic_dt'],'.'),1,1,'C');

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"��     ��",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$addr,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"�� ȭ �� ȣ",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$phone.' / '.$mobile,1,1,'C');

		Unset($col);


		$col[] = $pdf->width * 0.12;
		$col[] = $pdf->width * 0.18;
		$col[] = $pdf->width * 0.27;
		$col[] = $pdf->width * 0.08;
		$col[] = $pdf->width * 0.06;
		$col[] = $pdf->width * 0.29;

		$rowH = $pdf->row_height;

		$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
		$pdf->Cell($col[0],$rowH,"����",1,0,'C',1);
		$pdf->Cell($col[1],$rowH,"����",1,0,'C',1);
		$pdf->Cell($col[2],$rowH,"�򰡱���",1,0,'C',1);
		$pdf->Cell($col[3],$rowH,"��������",1,0,'C',1);
		$pdf->Cell($col[4],$rowH,"��",1,0,'C',1);
		$pdf->Cell($col[5],$rowH,"���",1,1,'C',1);
		
		
		$Y = $pdf->GetY();

		$h = $G1 * 2 + $G2;
		
		

		$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*13 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"1  ��  ��\n�������");
		
		/*
		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 13,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 4,"��������",1,0,'C');
		$pdf->Cell($col[2],$rowH * 1,"���ʼ�����",1,0,'C');
		$pdf->Cell($col[3],$rowH * 1,"",1,0,'C');
		$pdf->Cell($col[4],$rowH * 1,"",'LTB',0,'C');
		$pdf->Cell($col[5],$rowH * 1,"",'LTB',1,'C');
		*/

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 13,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 4,"��������",1,2,'C');
		$pdf->Cell($col[1],$rowH * 5,"��������",1,2,'C');

		$h = $G1 * 4 + $G2 * 3;
		$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY()+($pdf->row_height*5.5 - $h) / 2,'width'=>$col[1],'align'=>'C','text'=>"����������\n�Ǽ��ɾ� �� �Ѽҵ�(������������)");
		
		
		$pdf->Cell($col[1],$rowH * 4,"",1,2,'C');
		
		//��������
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '1' ? "��" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"���ʼ�����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '2' ? "��" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����������",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '3' ? "��" : ""));

		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"���ҵ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '4' ? "��" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�� ��",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		

		//��������
		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '5' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '4' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����,�����Ӵ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�����Ӵ�,��Ź",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�ڰ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		//���������� �Ǽ��ɾ� �� �Ѽҵ�(������������)
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '4' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����������� 150% �ʰ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"0",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '4' ? "��" : ""));
		
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�����������150% ����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');

		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����������� 120% ����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"��������� ��������",1,0,'L');
		$pdf->Cell($col[3],$rowH,"6",1,2,'C');


		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4],'Y'=>$Y+($pdf->row_height*6 - $h) / 2,'width'=>$col[5],'align'=>'L','text'=>" ����������, ����������,\n �ǰ�������Ȯ�μ� Ȯ��");

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['income_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');
		
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*5,number_format($choice['dwelling_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*5,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['gross_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');
		

		//2. ���� �ǰ����
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 10 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  ��  ��\n�ǰ����");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 10,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"��������",1,2,'C');
		$pdf->Cell($col[1],$rowH * 2,"��ֵ��",1,2,'C');
		$pdf->Cell($col[1],$rowH * 3,"ADL���",1,2,'C');
		$pdf->Cell($col[1],$rowH * 2,"�����",1,2,'C');

		//��������
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '1' ? "��" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"������ȯ 5���̻�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"������ȯ 3���̻�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"������ȯ 2������",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//��ֵ��
		//lfDrawCell($pdf,$col[3],$rowH,($choice['handicap_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"1��-3��",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['handicap_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"4��-6��",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//adl���
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"��������",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�κе���",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�����ڸ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		//�����
		//lfDrawCell($pdf,$col[3],$rowH,($choice['care_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�����1-5���",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['care_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"��޿� A,B",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['disease_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');
		
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*2,number_format($choice['handicap_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*2," ����ε���� Ȯ��",1,2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['adl_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*2,number_format($choice['care_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*2," ����������� Ȯ��",1,2,'L');

		//3. ���� �ɸ�����ȸ���
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 3 + $G2 *2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 10 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  ��  ��\n�ɸ�����ȸ\n���");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 10,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"��Ȱ����",1,2,'C');
		$pdf->Cell($col[1],$rowH * 4,"��ȸ�����",1,2,'C');
		$pdf->Cell($col[1],$rowH * 3,"�ɸ�����������",1,2,'C');
		

		//��Ȱ����
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '1' ? "��" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"��ȣ�� �ʿ��� ���Ű���",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"���Ű���",1,0,'L');
		$pdf->Cell($col[3],$rowH,"0",1,2,'C');
		
		//��ȸ�����
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"���� ����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '2' ? "��" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"���������ü�踸 ����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '3' ? "��" : ""));

		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"��������ü�踸 ����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '4' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����+���������ü��",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//�ɸ���������
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '1' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�ſ� �Ҿ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '2' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"�Ҿ�",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '3' ? "��" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"����",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['life_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');
		
		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4],'Y'=>$Y+($pdf->row_height*11.2 - $h) / 2,'width'=>$col[5],'align'=>'L','text'=>" ��������ü��(�̿�, ���� ��)\n ���������ü��(��ü ���, ����)");

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['social_rel_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['feel_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3," �ɾ����Ʈ �� ���ô�� Ȱ��",1,2,'L');
		
		//��ʰ����� �緮
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 3 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"��ʰ�����\n��         ��");
		

		$pnt['J']['1'] = $point['J']['1'][$choice['free_gbn']];
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"�緮",1,0,'C');
		$pdf->Cell($col[2]+$col[3],$rowH * 3,"1 ~ 5",1,0,'C');
		$pdf->Cell($col[4],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,0,'C');
		$pdf->Cell($col[5],$rowH,"��������� �ʿ��� ����� 6��","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"������ ���� �ʿ��� ����� 3~5��","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"�ܼ� ���񽺰� �ʿ��� ����� 1~2��","BLR",1,'L');

		//$pdf->Cell($col[6],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,1,'C');	//�緮
		
		//����
		//$totPnt = 0;
		
		$totPnt = $choice['income_point']+$choice['dwelling_point']+$choice['gross_point']+$choice['disease_point']+$choice['handicap_point']+$choice['adl_point']+$choice['care_point']+$choice['life_point']+$choice['social_rel_point']+$choice['feel_point']+$choice['free_point'];



		
		if ($totPnt >= 30){
			$totGbn1 = '��';
			$totGbn2 = '  ';
			$totGbn3 = '  ';
		}else if ($totPnt >= 25 && $totPnt < 30){
			$totGbn1 = '  ';
			$totGbn2 = '��';
			$totGbn3 = '  ';
		}else {
			$totGbn1 = '  ';
			$totGbn2 = '  ';
			$totGbn3 = '��';
		}

		$Y = $pdf->GetY();
		$X = $pdf->left;
		
		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"����",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3],$rowH * 3,"��ʰ���( ".$totGbn1." )   �⺻��( ".$totGbn2." )   ��������( ".$totGbn3." )",1,0,'C');
		$pdf->Cell($col[4],$rowH * 3,number_format($totPnt),'LR',0,'C');
		$pdf->Cell($col[5],$rowH,"��ʰ����� : 30���̻�","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"��   ��   �� : 25���̻� 30�� �̸�","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"�� �� �� �� : 25�� �̸�","BLR",1,'L');

		//$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		//$pdf->Cell($col[6],$rowH * 3,"".$totPnt,1,1,'C');

		//������ �ڸ�Ʈ
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 4 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"��ʰ��� �ǰ�");
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$Y + 1,'width'=>$col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],'text'=>$choice['comment']);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 4,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],$rowH * 4,"",1,1,'C');
	

	}else {
		$point = Array(
				//��������
				'A'=>Array(	'1'=>Array('1'=>5,'2'=>3,'3'=>2,'4'=>0)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2,'4'=>2.5))

				//��������
			,	'B'=>Array(	'1'=>Array('1'=>0,'2'=>1,'3'=>2,'4'=>3,'5'=>4)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2))

				//���������� �Ǽ��ɾ�
			,	'C'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1,'7'=>0)
						,	'2'=>Array('1'=>1.5))

				//�Ŀ��ڼ�
			,	'D'=>Array(	'2'=>Array('1'=>-0.5,'2'=>-1,'3'=>-2))

				//�ǰ����� ��ü��
			,	'E'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//�ǰ����� ������
			,	'F'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//��ֵ��
			,	'G'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1)
						,	'2'=>Array('1'=>1.5)
						,	'3'=>Array('1'=>1.5))

				//ADL���
			,	'H'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1))

				//�����
			,	'I'=>Array(	'1'=>Array('1'=>4,'2'=>3,'3'=>2,'4'=>1))

				//�緮
			,	'J'=>Array(	'1'=>Array('1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5))
		);

		
		//Ÿ��Ʋ
		//$pdf->SetXY($pdf->left, $pdf->top);
		//$pdf->SetFont($pdf->font_name_kor,'B',18);
		//$pdf->Cell($pdf->width,$pdf->row_height*2,"����� ��������ǥ",0,1,'C');
		//$pdf->SetFont($pdf->font_name_kor,'',9);

		$pdf->SetXY($pdf->left, $pdf->GetY());
		$pdf->Cell($pdf->width,$pdf->row_height,"��ʰ����� : ".$manager,0,1,'L');

		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"CLIENT",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$name,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"�� �� �� ��",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$myF->dateStyle($choice['chic_dt'],'.'),1,1,'C');

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"��     ��",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$addr,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"�� ȭ �� ȣ",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$phone.' / '.$mobile,1,1,'C');

		Unset($col);


		$col[] = $pdf->width * 0.12;
		$col[] = $pdf->width * 0.05;
		$col[] = $pdf->width * 0.07;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.20;
		$col[] = $pdf->width * 0.06;

		$rowH = $pdf->row_height;

		$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
		$pdf->Cell($col[0],$rowH,"����",1,0,'C',1);
		$pdf->Cell($col[1] + $col[2],$rowH,"����",1,0,'C',1);
		$pdf->Cell($col[3],$rowH,"�⺻����",1,0,'C',1);
		$pdf->Cell($col[4] + $col[5],$rowH,"������",1,0,'C',1);
		$pdf->Cell($col[6],$rowH,"����",1,1,'C',1);

		$Y = $pdf->GetY();

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*17 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"1  ��  ��\n�������");

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 17,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 4,"��������",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"��������",1,2,'C');

		$h = $G1 * 4 + $G2 * 3;
		$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY()+($pdf->row_height*7 - $h) / 2,'width'=>$col[1] + $col[2],'align'=>'C','text'=>"����������\n�Ǽ��ɾ� �� �Ѽҵ�(������������)");

		$pdf->Cell($col[1] + $col[2],$rowH * 7,"",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"�Ŀ����Ǽ�",1,1,'C');

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2], $Y);
		$pdf->Cell($col[3],$rowH,"","R",2,'L');

		//�������� income_gbn
		$pnt['A']['1'] = $point['A']['1'][$choice['income_gbn']];
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '1' ? "��" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.55,$rowH,"���ʼ�����",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.35,$rowH,"(5)",0,0,'L');

		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '2' ? "��" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.60,$rowH,"�Ƿ�޿�2��",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"(3)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.55,$rowH,"����������",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.35,$rowH,"(2)",0,0,'L');

		$pdf->SetFont($pdf->font_name_kor,'B',9);
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.60,$rowH,"��           ��",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"(0)","R",2,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"","R",2,'L');



		//�������� dwelling_gbn
		$pnt['B']['1'] = $point['B']['1'][$choice['dwelling_gbn']];
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		$pdf->Cell($col[3],$rowH * 0.5,"","TR",2,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '1' ? "��" : ""));
		$pdf->Cell($col[3] * 0.20,$rowH,"�ڰ�(0)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '2' ? "��" : ""));
		$pdf->Cell($col[3] * 0.20,$rowH,"����(1)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"�����Ӵ�,��Ź(2)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"����,�����Ӵ�(3)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '5' ? "��" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"����             (4)","R",2,'L');
		$pdf->Cell($col[3] * 0.45,$rowH * 0.5,"","R",2,'L');


		//���������� �Ǽ��ɾ� �� �Ѽҵ�(������������) gross_gbn
		$pnt['C']['1'] = $point['C']['1'][$choice['gross_gbn']];
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.60,$rowH,"50�����̸�","T",0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(6)","TR",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '2' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"80����~85�����̸�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(5)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"85����~110�����̸�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(4)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"110����~135�����̸�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(3)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '5' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"135����~160�����̸�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(2)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '6' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"160����~185�����̸�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(1)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '7' ? "��" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"185�����̻�",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(0)","R",2,'L');

		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2],'Y'=>$pdf->GetY() + 1,'width'=>$col[3],'text'=>" ��ȸ�������, ������ü, ģ��ô, �����Ŀ��ڷκ��� ��ǰ�Ŀ������� �ش� �������� ����(-1)���� �ο�");

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		$pdf->Cell($col[3],$rowH * 3,"",1,0,'L');

		$h = $G1 * 3 + $G2 *2;
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pos[] = Array('X'=>$pdf->GetX(),'Y'=>$pdf->GetY() + ($pdf->row_height * 4 - $h) / 2,'width'=>$col[4],'align'=>'C','text'=>"����Ȱ��\n�ɷ��̾���\n������");
		$pdf->Cell($col[4],$rowH * 4,"",1,2,'L');

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$pdf->GetX(),'Y'=>$pdf->GetY() + ($pdf->row_height * 3 - $h) / 2,'width'=>$col[4],'align'=>'C','text'=>"������\n������ �ο�");
		$pdf->Cell($col[4],$rowH * 3,"",1,2,'L');

		$X = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4];


		//����Ȱ�� �ɷ��̾��� ������ nonfamily_gbn
		$pnt['A']['2'] = $point['A']['2'][$choice['nonfamily_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[5] * 0.45,$rowH,"1��",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(1)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '2' ? "��" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"2��",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"3��",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(2)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"4���̻�",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(2.5)","R",2,'L');


		//������ ������ �ο� rental_gbn
		$pnt['B']['2'] = $point['B']['2'][$choice['rental_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[5] * 0.65,$rowH,"20���� �̸�","T",0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(1)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '2' ? "��" : ""));
		$pdf->Cell($col[5] * 0.65,$rowH,"20����~30����",0,0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[5] * 0.65,$rowH,"30���� �̻�",0,0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(2)","R",2,'L');

		$X = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3];

		//�������� �̿ܿ� �ҵ��� ������ ������ public_gbn
		$pnt['C']['2'] = $point['C']['2'][$choice['public_gbn']];
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2.5,"","TR",2,'L');
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['public_gbn'] == '1' ? "��" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"�������� �̿ܿ� �ҵ��� ������ ������","R",2,'L');

		$pdf->SetX($X + ($col[4] + $col[5]) * 0.05);
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2.5,"","R",2,'L');

		//�Ŀ����Ǽ� - ����� help_gbn
		$pnt['D']['2'] = 2 + $point['D']['2'][$choice['help_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"�Ŀ���ǰ �� 1����~5����","T",0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-0.5)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '2' ? "��" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"�Ŀ���ǰ �� 5�����̻�",0,0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-1)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '3' ? "��" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"�Ŀ���ǰ �� 10�����̻�","B",0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-2)","BR",2,'L');

		$X += ($col[4] + $col[5]);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[6],$rowH * 4,"".($pnt['A']['1'] + $pnt['A']['2']),1,2,'C');	//1���� - �������� - ����
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['B']['1'] + $pnt['B']['2']),1,2,'C');	//1���� - �������� - ����
		$pdf->Cell($col[6],$rowH * 7,"".($pnt['C']['1'] + $pnt['C']['2']),1,2,'C');	//1���� - ���������� �Ƿɾ� - ����
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['D']['1'] + $pnt['D']['2']),1,2,'C');	//1���� - �Ŀ����Ǽ� - ����


		//2. ���� �ǰ����
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 8 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  ��  ��\n�ǰ����");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 8,"",1,0,'C');

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$pdf->GetY() + ($rowH * 2 - $h) / 2,'width'=>$col[1],'align'=>'C','text'=>"�ǰ�\n����");
		$pdf->Cell($col[1],$rowH * 2,"",1,0,'C');
		$pdf->Cell($col[2],$rowH,"��ü��",1,2,'C');
		$pdf->Cell($col[2],$rowH,"������",1,2,'C');

		$pdf->SetX($X + $col[0]);
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"��ֵ��",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"ADL���",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"�����",1,2,'C');

		$X += ($col[0] + $col[1] + $col[2]);

		//�ǰ����� - ��ü�� body_gbn
		$pnt['E']['1'] = $point['E']['1'][$choice['body_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.37,$rowH,"�ſ������(3)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '2' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.27,$rowH,"������(2)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '3' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.21,$rowH,"��ȣ(1)","RBT",2,'L');

		//�ǰ����� - ������ feel_gbn
		$pnt['F']['1'] = $point['F']['1'][$choice['feel_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.37,$rowH,"�ſ������(3)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '2' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.27,$rowH,"������(2)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '3' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.21,$rowH,"��ȣ(1)","RBT",2,'L');

		//��ֵ�� handicap_gbn
		$pnt['G']['1'] = $point['G']['1'][$choice['handicap_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"1��(6)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '2' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"2��(5)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '3' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.35,$rowH,"3��(4)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[3] * 0.25,$rowH,"4��(3)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '5' ? "��" : ""));
		$pdf->Cell($col[3] * 0.25,$rowH,"5��(2)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '6' ? "��" : ""));
		$pdf->Cell($col[3] * 0.35,$rowH,"6��(1)","R",2,'L');

		//ADL��� adl_gbn
		$pnt['H']['1'] = $point['H']['1'][$choice['adl_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.45,$rowH,"��������(3)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '2' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.45,$rowH,"�κе���(2)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '3' ? "��" : ""));
		$pdf->Cell($col[3] * 0.95,$rowH,"�����ڸ�(1)","R",2,'L');

		//����� care_gbn
		$pnt['I']['1'] = $point['I']['1'][$choice['care_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"1��(4)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '2' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"2��(3)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '3' ? "��" : ""),"T");
		$pdf->Cell($col[3] * 0.35,$rowH,"3��(2)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '4' ? "��" : ""));
		$pdf->Cell($col[3] * 0.95,$rowH,"��޿� A,B(1)","BR",2,'L');

		$X += $col[3];

		//�ǰ����� - ��ü�� - ������ body_patient_gbn
		$pnt['E']['2'] = $point['E']['2'][$choice['body_patient_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['body_patient_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"��ȯ�� 2�� �̻��� ���(1.5)","TBR",2,'L');

		//�ǰ����� - ������ - ������ feel_patient_gbn
		$pnt['F']['2'] = $point['F']['2'][$choice['feel_patient_gbn']];
		$pdf->SetX($X);
		//$pdf->Cell(($col[4] + $col[5]) * 0.05,$rowH,"��","TB",0,'L');
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['feel_patient_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"��ȯ�� 2�� �̻��� ���(1.5)","TBR",2,'L');

		//2���� - ��ֵ�� - ������ handi_dup_gbn
		$pnt['G']['2'] = $point['G']['2'][$choice['handi_dup_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['handi_dup_gbn'] == '1' ? "��" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"�ߺ����(1.5)","TR",2,'L');

		//2���� - ��ֵ�� - ������ handi_2per_gbn
		$pnt['G']['3'] = $point['G']['3'][$choice['handi_2per_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['handi_2per_gbn'] == '1' ? "��" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"������� 2�� �̻��� ���(1.5)","BR",2,'L');

		//2 ���� - ADL��� - ������
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2,"",1,2,'C');

		//2 ���� - ����� - ������
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2,"",1,2,'C');

		$X += ($col[4] + $col[5]);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[6],$rowH,"".($pnt['E']['1'] + $pnt['E']['2']),1,2,'C');		//2 ���� - �ǰ����� - ��ü�� - ����
		$pdf->Cell($col[6],$rowH,"".($pnt['F']['1'] + $pnt['F']['2']),1,2,'C');		//2 ���� - �ǰ����� - ������ - ����
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['G']['1'] + $pnt['G']['2'] + $pnt['G']['3']),1,2,'C');	//2 ���� - ��ֵ�� - ����
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['H']['1'] + $pnt['H']['2']),1,2,'C');	//2 ���� - ADL��� - ����
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['I']['1'] + $pnt['I']['2']),1,2,'C');	//2 ���� - ����� - ����


		//��ʰ����� �緮
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 3 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"��ʰ�����\n��         ��");

		$pnt['J']['1'] = $point['J']['1'][$choice['free_gbn']];
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"�緮",1,0,'C');
		$pdf->Cell($col[3],$rowH * 3,"1 ~ 5",1,0,'C');
		$pdf->Cell($col[4] + $col[5],$rowH,"5��     : ��������� �ʿ��� �����","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"4~3�� : ������ ���� �ʿ��� �����","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"2~1�� : �ܼ� ���񽺰� �ʿ��� �����","BLR",0,'L');

		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,1,'C');	//�緮


		//����
		$totPnt = 0;

		foreach($pnt as $p){
			$totPnt += ($p['1']+$p['2']+$p['3']);
		}

		if ($totPnt >= 25){
			$totGbn1 = '��';
			$totGbn2 = '  ';
		}else if ($totPnt >= 20 && $totPnt < 25){
			$totGbn1 = '  ';
			$totGbn2 = '��';
		}

		$Y = $pdf->GetY();
		$X = $pdf->left;

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"����",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3],$rowH * 3,"��ʰ���( ".$totGbn1." )   �⺻��( ".$totGbn2." )",1,0,'C');
		$pdf->Cell($col[4] + $col[5],$rowH,"��ʰ����� : 25���̻�","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"��   ��   �� : 20���̻� 25�� �̸�","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"�� �� �� �� : 20�� �̸�","BLR",0,'L');

		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		$pdf->Cell($col[6],$rowH * 3,"".$totPnt,1,1,'C');

		//������ �ڸ�Ʈ
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 4 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"��ʰ�����\nCOMMENT");
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$Y + 1,'width'=>$col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],'text'=>$choice['comment']);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 4,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],$rowH * 4,"",1,1,'C');
	}

	Unset($choice);


	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
	}


	function lfDrawCell($pdf,$col,$rowH,$str,$border='0'){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTexTColor(0,0,255);
		$pdf->Cell($col,$rowH,$str,$border,0,'L');
		$pdf->SetTexTColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
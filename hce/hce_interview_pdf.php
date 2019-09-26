<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	�ʱ���������
	 *********************************************************/
	$pdf->font_size = 9;
	$pdf->SetFont($pdf->font_name_kor,'',$pdf->font_size);

	$orgNo = $_SESSION['userCenterCode'];
	$hce->IPIN = $var['key'] != '' ? $var['key'] : $hce->IPIN;


	//����ڸ�
	if ($var['mode'] == '21_N'){
		$sql = 'SELECT	name
				,		jumin
				FROM	care_client_normal
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$hce->SR.'\'
				AND		normal_seq	= \''.$hce->IPIN.'\'';
	}else{
		$sql = 'SELECT	m03_name AS name
				,		m03_jumin AS jumin
				FROM	m03sugupja
				WHERE	m03_ccode	= \''.$orgNo.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_key		= \''.$hce->IPIN.'\'';
		
	}

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);

	if (!$jumin) $jumin = $row['jumin'];
	$jumin = SubStr($jumin.'0000000',0,13);

	$gender	= $myF->euckr($myF->issToGender($jumin));
	$juminNo= $myF->issStyle($jumin);

	if (StrLen($jumin) == 13){
		$age = Number_Format($myF->issToAge($jumin));
	}

	Unset($row);

	//����������
	if ($var['mode'] == '21_0'){
		$sql = 'SELECT	m03_juso1 AS addr
				,		m03_juso2 AS addr_dtl
				,		m03_tel AS phone
				,		m03_hp AS mobile
				,		EL.name AS edu_gbn
				,		RG.name AS rel_gbn
				FROM	m03sugupja
				LEFT	JOIN	hce_gbn AS EL
						ON		EL.type	= \'EL\'
						AND		EL.code	= SUBSTR(m03_yoyangsa5_nm,3,2)
				LEFT	JOIN	hce_gbn AS RG
						ON		RG.type	= \'RG\'
						AND		RG.code	= SUBSTR(m03_yoyangsa5_nm,5,1)
				WHERE	m03_ccode	= \''.$orgNo.'\'
				AND		m03_mkind	= \'6\'
				AND		m03_key		= \''.$hce->IPIN.'\'';
	}else if ($var['mode'] == '21_N'){
		$sql = 'SELECT	addr
				,		addr_dtl
				,		phone
				,		mobile
				,		EL.name AS edu_gbn
				,		RG.name AS rel_gbn
				FROM	care_client_normal
				LEFT	JOIN	hce_gbn AS EL
						ON		EL.type	= \'EL\'
						AND		EL.code	= care_client_normal.edu_gbn
				LEFT	JOIN	hce_gbn AS RG
						ON		RG.type	= \'RG\'
						AND		RG.code	= care_client_normal.rel_gbn
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$hce->SR.'\'
				AND		normal_seq	= \''.$hce->IPIN.'\'';
	}else{
		if($var['gbn'] == 'care'){
			if($var['wrkType'] == 'INTERVIEW_REG'){
				$sql = 'SELECT	m03_jumin AS jumin
					,		m03_name AS name
					,		m03_juso1 AS addr
					,		m03_juso2 AS addr_dtl
					,		m03_tel AS phone
					,		m03_hp AS mobile
					,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
					,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
					,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
					FROM	m03sugupja
					LEFT	JOIN	mst_jumin AS jumin
							ON		jumin.org_no= m03_ccode
							AND		jumin.gbn	= \'1\'
							AND		jumin.code	= m03_jumin
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \'6\'
					AND		m03_key		= \''.$hce->IPIN.'\'';
			}else {
				$sql = 'SELECT	jumin AS real_jumin,name,addr,addr_dtl,phone,mobile,edu_gbn,rel_gbn
					FROM	care_client_normal
					WHERE	org_no		= \''.$orgNo.'\'
					AND		normal_sr	= \''.$var['sr'].'\'
					AND		normal_seq	= \''.$hce->IPIN.'\'';

			}

		}else {
			$sql = 'SELECT	EL.name AS edu_gbn
					,		RG.name AS rel_gbn
					,		addr
					,		addr_dtl
					,		phone
					,		mobile
					FROM	hce_receipt
					LEFT	JOIN	hce_gbn AS EL
							ON		EL.type	= \'EL\'
							AND		EL.code	= edu_gbn
					LEFT	JOIN	hce_gbn AS RG
							ON		RG.type	= \'RG\'
							AND		RG.code	= rel_gbn
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'';
		}
	}
	
	$row = $conn->get_array($sql);

	if($var['gbn'] == 'care'){
		//�з�
		$sql = 'SELECT	name
				FROM	hce_gbn
				WHERE	type	= \'EL\'
				AND		use_yn	= \'Y\'
				AND		code	= \''.$row['edu_gbn'].'\'';

		$row['edu_gbn'] = $conn->get_data($sql);

		//����
		$sql = 'SELECT	name
				FROM	hce_gbn
				WHERE	type	= \'RG\'
				AND		use_yn	= \'Y\'
				AND		code	= \''.$row['rel_gbn'].'\'';

		$row['rel_gbn'] = $conn->get_data($sql);
	}


	$eduGbn	= $pdf->_splitTextWidth($myF->utf($row['edu_gbn']),$pdf->width*0.09);
	$relGbn	= $pdf->_splitTextWidth($myF->utf($row['rel_gbn']),$pdf->width*0.09);
	$addr	= $pdf->_splitTextWidth($myF->utf($row['addr'].' '.$row['addr_dtl']),$pdf->width*0.52);
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$mobile	= $myF->phoneStyle($row['mobile'],'.');

	if (!$phone) $phone = '                    ';
	if (!$mobile) $mobile = '                    ';

	//Unset($row);

	$sql = 'SELECT	*
			FROM	hce_interview
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';
	
	$iv = $conn->get_array($sql);

	//1.�⺻����
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height*2,"",1,0,'L',1);

	$h = $pdf->GetStringWidth("�� ��");
	$w = $pdf->GetStringWidth("1. ");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"1. ");
	$pos[] = Array('X'=>$pdf->left+$w,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 2,'width'=>$pdf->width*0.08-$w,'text'=>"�⺻\n����");

	$pdf->Cell($pdf->width*0.07,$pdf->row_height,"��   ��",1,0,'C',1);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height,$name,1,0,'C');
	$pdf->Cell($pdf->width*0.05,$pdf->row_height,"����",1,0,'C',1);
	$pdf->Cell($pdf->width*0.05,$pdf->row_height,$gender,1,0,'C');
	$pdf->Cell($pdf->width*0.05,$pdf->row_height,"����",1,0,'C',1);
	$pdf->Cell($pdf->width*0.05,$pdf->row_height,$age,1,0,'C');
	$pdf->Cell($pdf->width*0.12,$pdf->row_height,"�ֹε�Ϲ�ȣ",1,0,'C',1);
	$pdf->Cell($pdf->width*0.17,$pdf->row_height,$juminNo,1,0,'C');
	$pdf->Cell($pdf->width*0.05,$pdf->row_height,"�з�",1,0,'C',1);

	//$pdf->Cell($pdf->width*0.09,$pdf->row_height,$eduGbn,1,0,'L');
	$orgsize = $pdf->font_size;
	$fontsize = $pdf->TestSize($pdf->width*0.09*0.9, $row['edu_gbn']);
	$pdf->SetFontSize($fontsize);
	$pdf->Cell($pdf->width*0.09,$pdf->row_height,$row['edu_gbn'],1,0,'L');
	$pdf->SetFontSize($orgsize);

	$pdf->Cell($pdf->width*0.05,$pdf->row_height,"����",1,0,'C',1);
	$pdf->Cell($pdf->width*0.09,$pdf->row_height,$relGbn,1,1,'L');

	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.07,$pdf->row_height,"���ּ�",1,0,'C',1);
	$pdf->Cell($pdf->width*0.52,$pdf->row_height,$addr,"LTB",0,'L');
	$pdf->Cell($pdf->width*0.33,$pdf->row_height,"(�� ".$phone." / ".$mobile.")","RTB",1,'R');

	//2.��������
	$col[] = $pdf->width*0.07;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.30;
	$col[] = $pdf->width*0.07;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.12;
	
	//��������
	$sql = 'SELECT	HR.name AS rel
			,		family_nm AS nm
			,		family_addr AS addr
			,		family_age AS age
			,		family_job AS job
			,		family_cohabit AS cohabit
			,		family_monthly AS monthly
			,		family_remark AS remark
			FROM	hce_family
			INNER	JOIN  hce_gbn AS HR
					ON  HR.type = \'HR\'
					AND HR.code = family_rel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			/*LIMIT	4*/';

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn -> row_count();
	
	$rowDis = $rowCount-4;
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height*($rowCount+1),"",1,0,'C',1);

	$h = $pdf->GetStringWidth("�� ��");
	$w = $pdf->GetStringWidth("2. ");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*($rowCount+1) - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"2. ");
	$pos[] = Array('X'=>$pdf->left+$w,'Y'=>$pdf->GetY()+($pdf->row_height*($rowCount+1) - $h) / 2,'width'=>$pdf->width*0.08-$w,'text'=>"����\n����");

	$pdf->Cell($col[0],$pdf->row_height,"��   ��",1,0,'C',1);
	$pdf->Cell($col[1],$pdf->row_height,"��   ��",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,"��   ��",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height,"�� ��",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,"��   ��",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height,"���ſ���",1,0,'C',1);
	$pdf->Cell($col[6],$pdf->row_height,"���ҵ��",1,0,'C',1);
	$pdf->Cell($col[7],$pdf->row_height,"���",1,1,'C',1);

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$pdf->SetX($pdf->left+$pdf->width*0.08);
		$pdf->Cell($col[0],$pdf->row_height,$row['rel'],1,0,'C');
		$pdf->Cell($col[1],$pdf->row_height,$row['nm'],1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,$row['addr'],1,0,'C');
		$pdf->Cell($col[3],$pdf->row_height,$row['age'],1,0,'C');
		$pdf->Cell($col[4],$pdf->row_height,$row['job'],1,0,'C');
		$pdf->Cell($col[5],$pdf->row_height,$row['cohabit'],1,0,'C');
		$pdf->Cell($col[6],$pdf->row_height,$row['monthly'],1,0,'C');
		$pdf->Cell($col[7],$pdf->row_height,$row['remark'],1,1,'L');
	}
	
	$conn->row_free();

	
	//if($debug) echo $rowCount;

	Unset($col);

	//3.��Ȱ����
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height*6,"",1,0,'C',1);

	$h = $pdf->GetStringWidth("�� ��");
	$w = $pdf->GetStringWidth("3. ");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*6 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"3. ");
	$pos[] = Array('X'=>$pdf->left+$w,'Y'=>$pdf->GetY()+($pdf->row_height*6 - $h) / 2,'width'=>$pdf->width*0.08-$w,'text'=>"��Ȱ\n����");

	#income_gbn		//������Ȳ
	#income_other	//������Ȳ ��Ÿ
	#income_monthly	//���ҵ�
	#income_main	//�ּҵ��

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'IG\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $iv['income_gbn']){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '9'){
			$str .= "(";

			if ($iv['income_gbn'] == '9'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['income_other']),30);
			}else{
				$str .= "                         ";
			}

			$str .= ")";
		}

		$str .= "     ";
	}

	$conn->row_free();

	$pdf->Cell($pdf->width*0.15,$pdf->row_height*2,"1) ������Ȳ",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,"LTR",1,'L');

	//���ҵ�, �ּҵ��
	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,"","LBR",0,'L');
	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height,"����ҵ�",0,0,'L');
	$pdf->SetFont($pdf->font_name_kor,"U",$pdf->font_size);
	$pdf->Cell($pdf->width*0.14,$pdf->row_height,($iv['income_monthly'] > 0 ? number_format($iv['income_monthly']) : "                ")."��",0,0,'L');
	$pdf->SetFont($pdf->font_name_kor,"",$pdf->font_size);
	$pdf->Cell($pdf->width*0.10,$pdf->row_height,"���ּҵ��",0,0,'L');
	$pdf->Cell($pdf->width*0.45,$pdf->row_height,"(".($iv['income_main'] ? $pdf->_splitTextWidth($myF->utf($iv['income_main']),$pdf->width*0.42) : "                                    ").")",0,1,'L');

	Unset($str);


	#generation_gbn		//��������
	#generation_other	//�������� ��Ÿ

	//��������
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'GR\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $iv['generation_gbn']){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '9'){
			$str .= "(";

			if ($iv['generation_gbn'] == '9'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['generation_other']),25);
			}else{
				$str .= "                  ";
			}

			$str .= ")";
		}

		$str .= "     ";
	}

	$conn->row_free();
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,"2) ��������",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,1,1,'L');

	Unset($str);


	//dwelling_gbn		//�ְ�����
	//dwelling_other	//�ְ����� ��Ÿ
	//deposit_amt		//������
	//rental_amt		//����

	//�ְ�����
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height*3,"3) �ְ�����",1,0,'L');
	$pdf->Cell($pdf->width*0.07,$pdf->row_height,($iv['dwelling_gbn'] == '1' ? '��' : '��')."�ڰ�","LT",0,'L');
	$pdf->Cell($pdf->width*0.23,$pdf->row_height,($iv['dwelling_gbn'] == '2' ? '��' : '��')."����(������ ".($iv['deposit_amt'] ? number_format($iv['deposit_amt']) : "       ")."����)","T",0,'L');
	$pdf->Cell($pdf->width*0.47,$pdf->row_height,($iv['dwelling_gbn'] == '3' ? '��' : '��')."����(������ ".($iv['deposit_amt'] ? number_format($iv['deposit_amt']) : "       ")."���� / ���� ".($iv['rental_amt'] ? number_format($iv['rental_amt']) : "       ")."����)","RT",1,'L');

	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.07,$pdf->row_height,($iv['dwelling_gbn'] == '4' ? '��' : '��')."�Ӵ�","L",0,'L');
	$pdf->Cell($pdf->width*0.18,$pdf->row_height,($iv['dwelling_gbn'] == '5' ? '��' : '��')."�����Ӵ�/��Ź����",0,0,'L');
	$pdf->Cell($pdf->width*0.09,$pdf->row_height,($iv['dwelling_gbn'] == '6' ? '��' : '��')."���㰡",0,0,'L');
	$pdf->Cell($pdf->width*0.43,$pdf->row_height,($iv['dwelling_gbn'] == '9' ? '��' : '��')."��Ÿ(".($iv['dwelling_gbn'] == '9' ? $pdf->_splitTextWidth($myF->utf($iv['dwelling_other']),30) : "                                  ").")","R",2,'L');
	$pdf->SetX($pdf->left+$pdf->width*0.23);

	$X = $pdf->GetX();
	$Y = $pdf->GetY();

	$pdf->SetLineWidth(0.1);
	$pdf->SetDrawColor(93,93,93);

	while(true){
		if ($X+0.5 >= $pdf->left+$pdf->width) break;

		$pdf->Line($X, $Y, $X+0.5, $Y);

		$X += 1;
	}

	$pdf->SetDrawColor(0,0,0);
	$pdf->SetLineWidth(0.2);

	//��������
	//house_gbn		//���ñ���
	//house_other	//���ñ��� ��Ÿ
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HT\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $iv['house_gbn']){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '9'){
			$str .= "(";

			if ($iv['house_gbn'] == '9'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['house_other']),25);
			}else{
				$str .= "                  ";
			}

			$str .= ")";
		}

		$str .= "     ";
	}

	$conn->row_free();

	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,"LBR",1,'L');

	Unset($str);

	//4.��ü����
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height*7.1,"",1,0,'C',1);

	$h = $pdf->GetStringWidth("�� ��");
	$w = $pdf->GetStringWidth("4. ");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*7 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"4. ");
	$pos[] = Array('X'=>$pdf->left+$w,'Y'=>$pdf->GetY()+($pdf->row_height*7 - $h) / 2,'width'=>$pdf->width*0.08-$w,'text'=>"��ü\n����");


	#health_gbn		//�ǰ�����
	#health_other	//�ǰ����� ��Ÿ
	//�ǰ�����
	$pdf->Cell($pdf->width*0.15,$pdf->row_height*2.4,"1) �ǰ�����",1,0,'L');
	$pdf->Cell($pdf->width*0.45,$pdf->row_height*0.8,($iv['health_gbn'] == '1' ? '��' : '��')."�ǰ��ϴ�.","LT",0,'L');
	$pdf->Cell($pdf->width*0.32,$pdf->row_height*0.8,($iv['health_gbn'] == '2' ? '��' : '��')."��ȯ�� ������ �ǰ��� ���̴�.","TR",1,'L');

	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.45,$pdf->row_height*0.8,($iv['health_gbn'] == '3' ? '��' : '��')."Ư���� ��ȯ�� ������ ��ȯ���� �ǰ����� ���ϴ�.","L",0,'L');
	$pdf->Cell($pdf->width*0.32,$pdf->row_height*0.8,($iv['health_gbn'] == '4' ? '��' : '��')."��ȯ���� �ǰ��� ���ڴ�.","R",1,'L');

	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.77,$pdf->row_height*0.8,($iv['health_gbn'] == '9' ? '��' : '��')."��Ÿ(".($iv['health_gbn'] == '9' ? $iv['health_other'] : '').")","LBR",1,'L');


	#disease_gbn	//������ȯ
	//������ȯ
	$disGbn = $iv['disease_gbn'];
	$disGbn = str_replace('/','&',$disGbn);
	$disGbn = str_replace(':','=',$disGbn);

	parse_str($disGbn,$disArr);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'DT\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();
	$str == '';

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($disArr[$row['code']] == 'Y'){
			$str .= ($str ? ',  ' : '').$row['name'];
		}
	}

	$conn->row_free();
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,"2) ������ȯ",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,1,1,'L');

	Unset($str);

	#handicap_gbn	//��ֿ���
	#handicap_other	//�������
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,"3) ��ֿ���",1,0,'L');
	$pdf->Cell($pdf->width*0.40,$pdf->row_height,($iv['handicap_gbn'] == 'Y' ? '��' : '��')."��(".($iv['handicap_other'] ? $iv['handicap_other'] : "                                      ").")","LTB",0,'L');
	$pdf->Cell($pdf->width*0.37,$pdf->row_height,($iv['handicap_gbn'] == 'N' ? '��' : '��')."��","RTB",1,'L');


	#device_gbn		//���屸
	#device_other	//���屸 ��Ÿ
	//���屸
	$gbn = $iv['device_gbn'];
	$gbn = str_replace('/','&',$gbn);
	$gbn = str_replace(':','=',$gbn);

	parse_str($gbn,$arr);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'DV\'
			AND	use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == '99') $str .= "\n";
		if ($arr[$row['code']] == 'Y'){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '99'){
			$str .= "(";

			if ($arr[$row['code']] == 'Y'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['device_other']),25);
			}else{
				$str .= "                  ";
			}

			$str .= ")";
		}

		$str .= "     ";
	}

	$conn->row_free();
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height*1.7,"4) ���屸",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height*1.7,"",1,1,'L');

	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.23,'Y'=>$pdf->GetY()-$pdf->row_height*1.5,'width'=>$pdf->width*0.77,'text'=>$str);

	Unset($str);


	#longlvl_gbn	//�������
	#longlvl_other	//��� ��
	//�������
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'LLV\'
			AND	use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $iv['longlvl_gbn']){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '9'){
			$str .= "(";

			if ($iv['longlvl_gbn'] == '9'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['longlvl_other']),25);
			}else{
				$str .= "                  ";
			}

			$str .= ")";
		}

		$str .= "   ";
	}

	$conn->row_free();
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,"5) �������",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,1,1,'L');

	Unset($str);

	//5.Ÿ ���� �̿� ��Ȳ
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.23,$pdf->row_height,"5. Ÿ ���� �̿� ��Ȳ",1,0,'L',1);
	$pdf->Cell($pdf->width*0.11,$pdf->row_height,"���񽺸� : ","LTB",0,'L');
	$pdf->Cell($pdf->width*0.29,$pdf->row_height,$pdf->_splitTextWidth($myF->utf($iv['other_svc_nm']),$pdf->width*0.29),"TB",0,'L');
	$pdf->Cell($pdf->width*0.11,$pdf->row_height,"�̿��� : ","TB",0,'L');
	$pdf->Cell($pdf->width*0.26,$pdf->row_height,$pdf->_splitTextWidth($myF->utf($iv['other_org_nm']),$pdf->width*0.26),"RTB",1,'L');

	//6.��û����
	$gbn = $iv['req_svc_gbn'];
	$gbn = str_replace('/','&',$gbn);
	$gbn = str_replace(':','=',$gbn);

	parse_str($gbn,$arr);

	$sql = 'SELECT	DISTINCT
					care.suga_cd AS cd
			,		suga.nm1 AS mst_nm
			,		suga.nm2 AS pro_nm
			,		suga.nm3 AS svc_nm
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
					AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
					AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no	= \''.$orgNo.'\'
			AND		care.suga_sr= \''.$hce->SR.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($arr[$row['cd']] == 'Y'){
			$str .= ($str ? ',  ' : '').$row['svc_nm'];
		}
	}

	$conn->row_free();

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.23,$pdf->row_height*2,"6. ��û����",1,0,'L',1);
	$pdf->Cell($pdf->width*0.77,$pdf->row_height*2,"",1,1,'L');

	$str = $pdf->_splitTextWidth($myF->utf($str), $pdf->width*0.77*2.95);

	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.23,'Y'=>$pdf->GetY()-$pdf->row_height*1.8,'width'=>$pdf->width*0.77,'text'=>$str);

	Unset($str);

	//7.������������
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height*5,"",1,0,'C',1);

	$h = $pdf->GetStringWidth("�� ��");
	$w = $pdf->GetStringWidth("7. ");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*5 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"7. ");
	$pos[] = Array('X'=>$pdf->left+$w,'Y'=>$pdf->GetY()+($pdf->row_height*5 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"����");
	$pos[] = Array('X'=>$pdf->left+1,'Y'=>$pdf->GetY()+($pdf->row_height*5 - $h) / 2+3.5,'width'=>$pdf->width*0.09,'text'=>"��������");

	$pdf->Cell($pdf->width*0.15,$pdf->row_height*2,"",1,0,'L');

	$h = $pdf->GetStringWidth("��");
	$w = $pdf->GetStringWidth("1) ");
	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.08,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"1) ");
	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.08+$w,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 4,'width'=>$pdf->width*0.12,'text'=>"�������� ����");

	$Y = $pdf->GetY();

	$pdf->Cell($pdf->width*0.15,$pdf->row_height,($iv['offer_gbn'] == 'Y' ? '��' : '��')."����","LTR",1,'L');
	$pdf->SetX($pdf->left+$pdf->width*0.23);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,($iv['offer_gbn'] == 'N' ? '��' : '��')."������","LBR",1,'L');

	$pdf->SetXY($pdf->left+$pdf->width*0.38,$Y);
	$pdf->Cell($pdf->width*0.10,$pdf->row_height*2,"�����ݻ���",1,0,'L',1);
	$pdf->Cell($pdf->width*0.52,$pdf->row_height*2,"",1,1,'L');

	if ($iv['offer_gbn'] != 'Y'){
		$pos[] = Array('X'=>$pdf->left+$pdf->width*0.48,'Y'=>$pdf->GetY()-$pdf->row_height*1.9,'width'=>$pdf->width*0.52,'text'=>StripSlashes($iv['nooffer_rsn']));
	}


	#svc_rsn_gbn	//���񽺻���
	#svc_rsn_other	//���񽺻��� ��Ÿ
	//���񽺻���
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'SRG\'
			AND	use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $iv['svc_rsn_gbn']){
			$str .= "��";
		}else{
			$str .= "��";
		}

		$str .= $row['name'];

		if ($row['code'] == '9'){
			$str .= "(";

			if ($iv['svc_rsn_gbn'] == '9'){
				$str .= $pdf->_splitTextWidth($myF->utf($iv['svc_rsn_other']),25);
			}else{
				$str .= "                  ";
			}

			$str .= ")";
		}

		$str .= "     ";
	}

	$conn->row_free();
	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,"2) ���� ����",1,0,'L');
	$pdf->Cell($pdf->width*0.77,$pdf->row_height,$str,1,1,'L');

	Unset($str);


	$pdf->SetX($pdf->left+$pdf->width*0.08);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height*2,"",1,0,'L');

	$h = $pdf->GetStringWidth("��");
	$w = $pdf->GetStringWidth("3) ");
	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.08,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 2,'width'=>$pdf->width*0.08,'text'=>"3) ");
	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.08+$w,'Y'=>$pdf->GetY()+($pdf->row_height*2 - $h) / 4,'width'=>$pdf->width*0.12,'text'=>"���� ���� ����");

	$pdf->Cell($pdf->width*0.77,$pdf->row_height*2,"",1,1,'L');

	$gbn = $iv['offer_svc_gbn'];
	$gbn = str_replace('/','&',$gbn);
	$gbn = str_replace(':','=',$gbn);

	parse_str($gbn,$arr);

	$sql = 'SELECT	DISTINCT
					care.suga_cd AS cd
			,		suga.nm1 AS mst_nm
			,		suga.nm2 AS pro_nm
			,		suga.nm3 AS svc_nm
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
					AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
					AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no	= \''.$orgNo.'\'
			AND		care.suga_sr= \''.$hce->SR.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($arr[$row['cd']] == 'Y'){
			$str .= ($str ? ',  ' : '').$row['svc_nm'];
		}
	}

	$conn->row_free();

	$str = $pdf->_splitTextWidth($myF->utf($str), $pdf->width*0.77*2.95);

	$pos[] = Array('X'=>$pdf->left+$pdf->width*0.23,'Y'=>$pdf->GetY()-$pdf->row_height*1.9,'width'=>$pdf->width*0.77,'text'=>$str);

	Unset($str);

	//8.�Ƿ���
	#req_nm			//�Ƿ��θ�
	#req_rel		//����ڿ��� ����
	#req_telno		//����ó
	#req_route_gbn	//�Ƿڰ��

	//����ڿ��� ����
	$sql = 'SELECT	name
			FROM	hce_gbn
			WHERE	type	= \'HR\'
			AND		use_yn	= \'Y\'
			AND		code	= \''.$iv['req_rel'].'\'';

	$reqRel = $conn->get_data($sql);

	//�Ƿڰ��
	$sql = 'SELECT	name
			FROM	hce_gbn
			WHERE	type	= \'CR\'
			AND		use_yn	= \'Y\'
			AND		code	= \''.$iv['req_route_gbn'].'\'';

	$reqRoute = $conn->get_data($sql);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height,"8. �Ƿ���",1,0,'C',1);
	$pdf->Cell($pdf->width*0.07,$pdf->row_height,"�� ��",1,0,'C',1);
	$pdf->Cell($pdf->width*0.08,$pdf->row_height,$iv['req_nm'],1,0,'C');
	$pdf->Cell($pdf->width*0.14,$pdf->row_height,"����ڿ��� ����",1,0,'C',1);
	$pdf->Cell($pdf->width*0.12,$pdf->row_height,$reqRel,1,0,'C');
	$pdf->Cell($pdf->width*0.09,$pdf->row_height,"��ȭ��ȣ",1,0,'C',1);
	$pdf->Cell($pdf->width*0.15,$pdf->row_height,$myF->phoneStyle($iv['req_telno'],'.'),1,0,'C');
	$pdf->Cell($pdf->width*0.09,$pdf->row_height,"�Ƿڰ��",1,0,'C',1);
	$pdf->Cell($pdf->width*0.18,$pdf->row_height,$reqRoute,1,1,'C');

	//���
	#remark	//���
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height,"9. ���","LTR",1,'L');
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height*(8-$rowDis),"","LBR",1,'C');
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()-$pdf->row_height*(7.9-$rowDis),'width'=>$pdf->width,'text'=>StripSlashes($iv['remark']));

	//������ / ���
	#iver_dt	//������
	#iver_nm	//����ڸ�
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.5,$pdf->row_height,"10. ������ : ".$myF->dateStyle($iv['iver_dt'],'.'),"LTB",0,'L');
	$pdf->Cell($pdf->width*0.5,$pdf->row_height,"��� : ".$iv['iver_nm'],"RTB",1,'L');


	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 3.5, $row['text'], 0, 'L');
	}

	Unset($pos);
	Unset($iv);
?>
<?
	include('../inc/_login.php');

	if (!Is_Array($var)){
		exit;
	}

	$code		= $_SESSION['userCenterCode'];
	
	$agreeDt    = date('Y');
	
	$conn->set_name('euckr');

	
	$code = $_SESSION['userCenterCode'];	//�����ȣ
	$kind = 0;								//���񽺱���
	$ssn = $var['jumin'];					//�������ֹι�ȣ
	
	$sql =  ' select from_dt 
			  ,		 to_dt 
				from client_his_svc
			   where org_no = \''.$code.'\'
				 and jumin  = \''.$ssn.'\'
				 and svc_cd = \'0\'
			   order by seq desc
			   limit 1';
	$svc = $conn->get_array($sql);
			
	$from_dt = ($ct['from_dt'] != '' ? $ct['from_dt'] : $svc['from_dt']);
	$to_dt = ($ct['to_dt'] != '' ? $ct['to_dt'] : $svc['to_dt']);
	

	$sql = "select m03_jumin as jumin
			,	   m03_key
			,	   m03_name as name
			,	   m03_tel as tel
			,	   m03_hp as hp
			,	   m03_yboho_name as bohoName
			,	   m03_yboho_juminno as bohoJumin
			,	   m03_yboho_gwange as gwange
			,	   m03_yboho_phone as bohoPhone
			,	   m03_yboho_addr as bohoAddr
			,	   lvl.app_no as injungNo
			,	   case lvl.level when '9' then '�Ϲ�' else lvl.level end as level
			,	   case kind.kind when '3' then '���ʼ��ޱ���' when '2' then '�Ƿ���ޱ���' when '4' then '�氨�����' else '�Ϲ�' end as m92_cont
			,	   kind.kind
			,	   concat(m03_juso1, ' ', m03_juso2) as juso
			,	   max(lvl.from_dt)
			,	   max(lvl.to_dt)
			,	   max(kind.from_dt)
			,	   max(kind.to_dt)
			  from m03sugupja
			  left join ( select jumin
						  ,		 from_dt 
						  ,		 to_dt 
						  ,		 app_no
						  ,		 level
							from client_his_lvl
						   where org_no = '".$code."'
							 and (from_dt between '".$from_dt."' and '".$to_dt."'
							  or to_dt between '".$from_dt."' and '".$to_dt."')
						   order by from_dt desc
							 ) as lvl
					 on lvl.jumin = m03_jumin
			  left join ( select jumin
						  ,		 from_dt 
						  ,		 to_dt 
						  ,		 kind
							from client_his_kind
						   where org_no = '".$code."'
							 and (from_dt between '".$from_dt."' and '".$to_dt."'
							  or to_dt between '".$from_dt."' and '".$to_dt."')
						   order by from_dt desc 
						   ) as kind
					 on kind.jumin = m03_jumin
			 where m03_ccode = '".$code."'
			   and m03_mkind = '".$kind."'
			   and m03_jumin = '".$ssn."'
			   and m03_del_yn = 'N'";
	
	$su = $conn->get_array($sql);

	//�������
	$sql = 'SELECT	m00_store_nm	AS nm
			,		m00_mname		AS manager
			,		m00_ctel		AS phone
			,		m00_fax_no      AS fax
			,		m00_jikin		AS jikin
			,		m00_code1       AS code
			FROM	m00center
			WHERE	m00_mcode = \''.$code.'\'
			  AND   m00_del_yn = \'N\'
			LIMIT	1';

	$row	= $conn->get_array($sql);

	$center		= $row['nm'];
	$manager	= $row['manager'];
	$phone		= $myF->phoneStyle($row['phone'],'.');
	$fax		= $myF->phoneStyle($row['fax'],'.');
	$jikin		= $row['jikin'];


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+5, $pdf->width-10, $pdf->height-25);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 16);
	$pdf->SetXY($pdf->left, $pdf->top+10);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "ǥ��������̿��ȹ�� ���� ��û�� [   ] ���������", 0, 1, "C");
		
	$pdf->SetFont($pdf->font_name_kor, "U", 12);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+7);
	$pdf->Cell($pdf->width, $pdf->row_height, "���ΰǰ�������� �̻��� ����", 0, 1, "L");

	$pdf->SetXY($pdf->left+10, $pdf->GetY()+2);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width*0.9, 6, "  �Ʒ� �������� ����� ǥ��������̿��ȹ�� ������ ����������纸��� �����Ģ����13���� ���� ���� ��û�մϴ�.", 0, "L");
	
	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "1. ������ ���� : ".$su['name']."
2. ����� ������ȣ : ".$su['injungNo']." 
3. �޿�������� : ".$from_dt."
4. �����Ⱓ : �����޿���� �Ⱓ( ".($from_dt != '' ? $from_dt : '                   ')." ~ ".($to_dt != '' ? $to_dt : '                   ')." )", 0, "L");

	
	$pdf->SetXY($pdf->width*0.65, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");
	
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 165, 110, '20', '20');	//��� ����
		}
	}

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "������� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height, $center, 0, 0, "L");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, "(���� �Ǵ� ��)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "������� ��ȣ :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, $row['code'], 0, 0, "L");


	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "��ȭ��ȣ :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $phone, 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "�ѽ� :  ".$myF->phoneStyle($fax,'.'), 0, 0, "L");


	$pdf->SetFont($pdf->font_name_kor, "B", 16);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "ǥ��������̿��ȹ�� ���� ���Ǽ�", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+2);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width*0.9, 6, "  ������ ����� ǥ��������̿��ȹ�� ������ ����������纸��� �����Ģ����13���� ���� ��������� �����Կ� �־ ������������ȣ������17���� ���� �����մϴ�", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "1. ���� ���� : ǥ��������̿��ȹ�� ����
2. ����� ������ȣ : ����� �޿�������ȹ����
3. �����Ⱓ : �����޿���� �Ⱓ( ".($from_dt != '' ? $from_dt : '                   ')." ~ ".($to_dt != '' ? $to_dt : '                   ')." )
4. ���ǻ��� : �ళ������ ���� �̿� �� �ΰ����� ó��\n     �� �����ĺ����� ó�� �� �������� ��3�� ������ ���� ����
�� ���ϲ����� ���Ǹ� �ź��� �� �ֽ��ϴ�.", 0, "L");
	
	$pdf->SetXY($pdf->width*0.65, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "�� �� �� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, "  ".$su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height, "(���� �Ǵ� ��)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "����� ������ȣ :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "  ".$su['injungNo'], 0, 0, "L");


	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "��ȭ��ȣ :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "  ".$myF->phoneStyle($su['hp'],'.'), 0, 0, "L");
	
	$pdf->SetFont($pdf->font_name_kor, "U", 12);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+7);
	$pdf->Cell($pdf->width, $pdf->row_height, "���ΰǰ�������� �̻��� ����", 0, 1, "L");

	Unset($row);
?>
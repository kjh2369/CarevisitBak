<?

	$conn->set_name('euckr');
	
	
	$code = $_SESSION['userCenterCode'];	//�����ȣ
	$kind = $_POST['kind'];					//���񽺱���
	$ssn = $ed->de($_POST['jumin']);		//�������ֹι�ȣ
	$svc_seq   = $_POST['seq'];				//���򰡰���(���Ű)	

	$report_id = $_POST['report_id'];		//���򰡰���(�̿��༭)
	$seq  = $_POST['seq'];
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		if($report_id != ''){
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,	   pay_day1
					,	   pay_day2
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and del_flag = \'N\'';
			
			if($svc_seq != ''){
				$sql .=	'  and svc_seq  = \''.$svc_seq.'\'';
			}
			
			$ct = $conn->get_array($sql);
		}else {
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,	   pay_day1
					,	   pay_day2
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and seq      = \''.$seq.'\'
					   and del_flag = \'N\'';
			$ct = $conn->get_array($sql);
		}

		$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $svc_seq;

		$sql =  ' select from_dt 
				  ,		 to_dt 
					from client_his_svc
				   where org_no = \''.$code.'\'
					 and jumin  = \''.$ssn.'\'
					 and seq    = \''.$svc_seq.'\'';
		$svc = $conn->get_array($sql);
		

		$sql = "select m03_jumin as jumin
				,	   m03_name as name
				,	   m03_tel as tel
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
								 and (from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."')
							   order by from_dt desc
								 ) as lvl
						 on lvl.jumin = m03_jumin
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 kind
								from client_his_kind
							   where org_no = '".$code."'
								 and (from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."')
							   order by from_dt desc 
							   ) as kind
						 on kind.jumin = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		$su = $conn->get_array($sql);
		
		
	}
	
	$sql = "select m00_mname as manager"
			 . ",      concat(m00_caddr1, ' ', m00_caddr2) as address"
			 . ",      m00_cname as centerName"
			 . ",      m00_code1 as centerCode"
			 . ",      m00_ctel as centerTel"
			 . ",      m00_fax_no as centerFax"
			 . ",      m00_bank_no as bankNo"
			 . ",      m00_bank_name as bankCode"
			 . ",      m00_jikin as jikin"
			 . "  from m00center"
			 . " where m00_mcode = '".$code
			 . "'  and m00_mkind = '".$kind."'";
			
	$center = $conn->get_array($sql);

	$bank = $center['bankNo'] != '' ? iconv('utf-8','euc-kr', $definition->GetBankName($center['bankCode']))."(".$center['bankNo'].")��" : " ";



	$from_year = $svc['from_dt'] != '' ? substr($svc['from_dt'],0,4) : '           ';	//�����۱Ⱓ(��)
	$from_month = $svc['from_dt'] != '' ? substr($svc['from_dt'],5,2) : '     ';		//�����۱Ⱓ(��)
	$from_day = $svc['from_dt'] != '' ? substr($svc['from_dt'],8,2) : '     ';			//�����۱Ⱓ(��)
	
	$to_year = $svc['to_dt'] != '' ? substr($svc['to_dt'],0,4) : '           ';			//�������Ⱓ(��)
	$to_month = $svc['to_dt'] != '' ? substr($svc['to_dt'],5,2) : '     ';				//�������Ⱓ(��)
	$to_day = $svc['to_dt'] != '' ? substr($svc['to_dt'],8,2) : '     ';				//�������Ⱓ(��)
		

	//�̿����
	$use_yoil1 = $ct['use_yoil1'];
	$use_yoil2 = $ct['use_yoil2'];
	
	//�̿�ð�
	$fm_h1 = $ct['from_time1'] != '' ? substr($ct['from_time1'],0,2) : '     ';
	$fm_m1 = $ct['from_time1'] != '' ? substr($ct['from_time1'],2,2) : '     ';
	$to_h1 = $ct['from_time1'] != '' ? substr($ct['to_time1'],0,2) : '     ';
	$to_m1 = $ct['from_time1'] != '' ? substr($ct['to_time1'],2,2) : '     ';
	$fm_h2 = $ct['from_time1'] != '' ? substr($ct['from_time2'],0,2) : '     ';
	$fm_m2 = $ct['from_time1'] != '' ? substr($ct['from_time2'],2,2) : '     ';
	$to_h2 = $ct['from_time1'] != '' ? substr($ct['to_time2'],0,2) : '     ';
	$to_m2 = $ct['from_time1'] != '' ? substr($ct['to_time2'],2,2) : '     ';

	$jikin = $center['jikin'];
	
	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '��';	//�̿볳����1
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';		//�̿볳����2

	$pdf->MY_ADDPAGE();
	
	$st_getY = $pdf -> GetY();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "�����޿� �̿� ǥ�ؾ��\n(�湮���)", 1,"C");
	
	//$pdf->Image('../image/standard_mark.jpg', 140, 45, '41', '35');	//�����ŷ�����ȸ �ΰ�

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+3);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "    �̿���, ������ �� �븮��(��ȣ��)�� �����޿� �̿뿡 ���Ͽ�\n ������ ���� �������� ����� ü���Ѵ�.");

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+2);
	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->Cell($pdf->width, $pdf->row_height*1.5, "�������", 1, 1, "L" ,true);


	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2.2*3.1, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*2, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*2, "", 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.15, 'y'=>$pdf->GetY()*1.47, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"������\n  (��)");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�������", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->issStyle($su['jumin']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($su['tel'],'.'), 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.15, 'y'=>$pdf->GetY()*1.56, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"�븮��\n  �Ǵ�\n��ȣ��\n  (��)");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, ' '.$su['juso'], 1, 1, "L");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, " �� �Ϲ�  �� �氨�����  �� �Ƿ������ �� ���ʼ��ޱ���", 1, 1, "L");

	if(strlen($center['centerName']) > 26){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.22, 'y'=>$pdf->GetY()*1, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.30, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.22, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.30, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*1.5*3, "", 'TL', 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, "", 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�����ȣ", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $center['centerCode'], 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$center['manager']);

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "����� ����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($center['centerTel'],'.'), 1, 1, "C");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, ' '.$center['address'], 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoName']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*1.5*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $su['gwange'], 1, 1, "C");
	
	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�������", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " ".$myF->issToBirthday($su['bohoJumin']), 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($su['bohoPhone'],'.'), 1, 1, "C");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, $su['bohoAddr'], 1, 1, "L");
	
	
	
	if ($su['kind'] == '1'){
		//�Ϲ�
		$pos_x = 61;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '3'){ 
		//���ʼ��ޱ���
		$pos_x = 142;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '2'){
		//�Ƿ���ޱ���
		$pos_x = 111.5;
		$pos_y = 100;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '4'){
		//�氨�����
		$pos_x = 80;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+2, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��1��(����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+7, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "����̳� ���μ����� ������ ���Ͽ� ȥ�ڼ� �ϻ��Ȱ�� �����ϱ� ����� ���ε� �� ��������� ���� �е鿡�� �湮���޿��� �����Ͽ� ������ �ǰ����� �� ��Ȱ������ �����ϰ� �� ������ �δ��� ���������ν� ���� ���� ����Ű���� �Ѵ�. ")));
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+16, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��2��(���Ⱓ)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+21, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� ���Ⱓ�� ".$from_year."�� ".$from_month."�� ".$from_day."�Ϻ��� ".$to_year."�� ".$to_month."�� ".$to_day."�ϱ����� �Ѵ�.\n�� ��1���� ���Ⱓ�� ����� ���� ���ǿ� ���� ������ �� �ִ�.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+30, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��3��(�޿�����)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+35, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "�湮���޿��� ��������� '��'  �� ���� ���� �湮�Ͽ� ��üȰ�� �� ����Ȱ�� ���� �����ϴ� �����޿��� �Ѵ�.")));
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+40, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��4��(�޿��̿� �� ����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+45, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� �湮���޿� �̿� �� ������ �����޿� �̿�(����)��ȹ���� ���Ѵ�.\n�� '��'  �� �湮���޿� �̿�ð��� �Ʒ��� ���� �Ѵ�.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);
	
	#�̿�ð�ǥ
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+63, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>"���  ��ȭ  ���  ���\n ���  ����  ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+74, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>"���  ��ȭ  ���  ���\n ���  ����  ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+65, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>$fm_h1."�� ".$fm_m1."��    ~".$to_h1."�� ".$to_m1."��");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+76, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>$fm_h2."�� ".$fm_m2."��    ~".$to_h2."�� ".$to_m2."��");
	
	$pdf->SetXY($pdf->left, $pdf->getY()+55);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿�ð�", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*2, "�̿�ð�(1)", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*2, "�̿�ð�(2)", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 1, "C");
	
	$pos_x = 66.9;
	$pos_y = 224; 

	for($i=0; $i<strlen($use_yoil1); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 5.5;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil1[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}

	$pos_x = 66.9;
	$pos_y = 235; 
	
	for($i=0; $i<strlen($use_yoil2); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 5.5;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil2[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.02, 'y'=>$pdf->GetY()+1, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� ���Ͽ� ���� �̿�ð��� �ٸ� ��� �̿�ð� ������� �÷��� �����\n�� '��'   �Ǵ� '��' �� ������ ���� �Ͻ������� �̿�ð��� ��Ű�� ����� ��� ����  �̿���� �ּ� 1�ð� ���� '��' ���� ������ ���ؾ� ��.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+10, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� �������� �����Ͽ� ���� �������� ���� �����Ͽ� �޿��� �����ϴ� ��쿡�� '��' �� 30%�� ��������� û���� �� �ִ�.\n�� �߰�(18:00~22:00), �ɾ�(22:00~06:00)�� �޿��� �����ϴ� ��쿡�� '��' �� �߰� 20%, �ɾ� 30%�� ��������� û���� �� �ִ�.\n�� �߰��ɾ����ϰ����� ���ÿ� ����Ǵ� ��쿡�� �ߺ��Ͽ� �������� �ʴ´�.\n�� '��' �� �Ϳ� �����޿� ������ �ϰ����ϴ� ��쿡��'��'  (�Ǵ� '��' )�� �����Ͽ� ��� ?�ϱ��� ���� ��1ȣ������ �����޿� �̿��ȹ\n   ���� �ۼ��ϰ� �����޿� �̿��ȹ���� ���� �����޿��� ���� �Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+40, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��5��(����� �ǹ�)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+5, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� '��'  �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �� �̿�� ����\n   2. �湮���޿� ������ �޿��̿�\n   3. �����޿� �̿��Ģ �ؼ�\n   4. ��Ÿ '��' �� ������ ��Ģ ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �湮���޿� ���� ��೻�� �ؼ�\n   2. �޿����� �� '��'  ���� �ź� �̻��� ����� ��� ��� '��' ���� �뺸\n   3. �޿������ð��� '��'  �� �ֺ� �� ������� û�� �� ��������\n   4. �޿����� �� �˰� �� '��'  �� �Ż� �� ��ȯ ���� ���� �������\n  (��, ġ�� ���� ��ġ�� �ʿ��� ���� ����)\n   5. �̿���, ������ȸ �ٸ� ���� �̿� ��������\n   6. �����д� ���� �� �����α� ��ȣ �ؼ�\n   7. ��Ÿ '��'  (�Ǵ� '��' )�� ��û�� ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. '��'  �� ���� �ǰ� �� �ʿ��� �ڷ�����\n   2. '��'  �� �� �̿�� �� ��� �δ�\n   3. ���� ���� �� ����纸�� ��� ���� �� ��� '��' ���� �뺸\n   4. '��'  �� ���� �ǹ������� ������ �븮�� ���� �� '��' ���� �뺸\n   5. ��Ÿ '��' �� ������û ����");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+86, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��6��(������� ���)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+91, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ� '��' )�� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��' �� �����Ͽ� ����� ���� �� �� �ִ�.
	  1. ��2���� ���Ⱓ�� ����� ���
	  2. ��3���� �湮���޿� ������ �ش��ϴ� ���񽺸� �������� �ƴ��� ���
	  3. ��4����2���� �湮���޿� �����ð���'��'  (�Ǵ�'��' )�� ���� ���� '��' �� ���Ƿ� �����ϰų� ��ġ�� ��������� ���Ƿ� ���� ����\n      ���
	  4. ��Ÿ '��'  �� ������� ������ �߻��� ��� �� '��' �� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� �����Ͽ� ����� ���� �� �� �ִ�.
	  1. ��2���� ���Ⱓ�� ����ǰų� ����� ���
	  2. '��'  �� ����纸�� ��޿��ڷ� ��޺����� �߻��� ���
	  3. '��'  �� �ǰ����� ������������ǿ���װ��������ѹ������� ���� ������ ȯ�ڷμ� ������ ���輺�� �ִ� ���� ������ ��
	  4. '��'  �� �ǰ����� ������ ���� �̿��� ����� ��
	  5. �̿���� ���õ� �̿�ȳ��� '��'  �� ������ ���� ���� ������ �ʴ� �� ���� ������ �ɰ��� ������ �� ��
	  6. '��'  �� �� 5ȸ �̻� �������� �湮���޿� �̿�ð��� ��Ҹ� ��Ű�� �ƴ��Ͽ��� ��");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+140, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��7��(����� ����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+145, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" �� '��'  (�Ǵ�'��' )�� ��6����1���� ������� ����� �߻��� ��쿡�� �ش��� �Ǵ� ���Ⱓ ���������� ���� ��2ȣ������ �����޿� ��\n    �� ��û���� �����Ͽ��� �Ѵ�. �ٸ�, ��Ÿ�ε����� ��쿡�� �켱 �������� �� �� �ִ�.\n �� '��' �� ��6����2�׿� ���� ������� ����� �߻��� ��쿡�� ������� �ǻ縦 ���� ��2ȣ������ �����޿� ����ȳ��� �� ���� ������\n    ���� �Բ� '��'  �� '��' ���� �뺸�ϰ� ����� �����ؾ� �Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+162, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��8��(�̿�� ����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+167, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" �� '��' �� ���� 1�Ϻ��� ���ϱ����� �̿�Ḧ �ſ�  ".$pay_day1."�Ͽ� �����ϰ� '��'  (�Ǵ� '��' )�� ��  ".$pay_day2."�ϱ��� ���� ��3ȣ������ �����޿� �̿�\n    �� ���γ������� �뺸�Ѵ�.\n �� '��'  �� �ſ� ".$bank." ���κδ���� ���� �Ѵ�. �ٸ�, �������� �������� ��쿡�� �� ���Ϸ� �Ѵ�.\n �� '��' �� '��'  �� ������ ��뿡 ���ؼ��� ��������纸��� �����Ģ[���� ��4ȣ����]�� �����޿� ����Ȯ�μ��� �߱��Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+184, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��9��(����)", 'font_bold'=>'B');
	
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+189, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"  ���� ��ȣ�� �ش��ϴ� ��쿡�� �̸� �ݿ��� ��༭�� ���ۼ��Ѵ�.
   1. ��2���� ���Ⱓ�� ����� ���
   2. ����� ��������� ����� ���
   3. �湮��� �޿���� �� ���κδ� ����� ����� ���
   4. ��Ÿ '��'  �� '��' �� �ʿ��� ���");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+210, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��10��(�ǰ�����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+215, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"��'��' ��'��'  �� �ǰ� �� ������ ������ ���Ͽ� �����ڵ鿡�� �� 1ȸ �̻� �ǰ������� �ǽ� �Ͽ��� �Ѵ�.\n��'��' �� ��������� �湮���޿� �������� '��'  ���� ���ظ� ������ ��� ������ ��ġ�� ���ؾ� �Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+224, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��11��(���� �� ��ġ)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+229, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" �� '��' �� �湮���޿� �����ð��� '��'  �� ������ ������ ���¶�� �Ǵܵ� ������ '��'  (�Ǵ� '��' )�� ������ ���� �Ǵ� ���� �Ƿ������� ��� �ļ��ϰ� '��' ���� ��� �뺸�Ͽ��� �Ѵ�.\n �� '��' �� ��1���� ������ ���� �뺸�� �޾��� ������ �ż��ϰ� ��ó�Ͽ��� �Ѵ�. �ٸ�, ��ó�� ����� ��쿡�� �켱 ���Ḧ ���� �� �ֵ��� ��ġ�Ͽ��� �Ѵ�.\n �� '��'  �� ���� �̿뵵�� ����Ͽ��� ���'��' �� ���'��' ���� �뺸�Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+249, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"��12��(�������� ��ȣ�ǹ�)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+254, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" �� '��'  �� ������ ���������� ���� �� �Ǹ��� �ִ�.\n �� '��' �� '��'  �� ���������� ��������� ���� ��ȣ�Ͽ��� �Ѵ�.\n �� '��' �� ����缭�� ������ �ʿ��� '��'  �� ���� ���� �ڷḦ �����ϰ� Ȱ���ϸ� ���ڷḦ ��������纸�� ���ü ��� �������\n   �� ���� ������ �� �ִ�.\n �� '��' �� ������������ �� Ȱ���� �ϰ��� �ϴ� ��쿡�� '��'  ���� ���� ��5ȣ������ ������������ �� Ȱ�� ���Ǽ��� �޾ƾ� �Ѵ�.\n �� '��' �� '��'  �� ���Ȱ�� �����ϰ�, ������ �˰� �� ���������� ö���� ����� �����Ѵ�. ");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+13, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��13��(��� �� ����)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+18, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"'��' �� '��'  �� ��Ȱ�� ����缭�񽺿� ���� ��� ������ ���� �����Ͽ� ��Ȯ�� ����ϰ�, '��'  (�Ǵ� '��' )�� �䱸�� ��쿡�� ǥ�ؾ�Ŀ� �ǰ��� ����� �����Ͽ��� �Ѵ�.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+27, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��14��(���å��)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+32, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" �� '��' �� ���� ��ȣ�� ��쿡��'��'  (�Ǵ�'��' )���� ����ǹ��� ������ ���å���� ��������� ������.\n   1. �������(�Ǵ� '��' )�� ���ǳ� ���Ƿ� ���Ͽ� '��'  �� �λ��� �ϴ� �� �ǰ��� ���ϰ� �ϰų� ����� �̸��� �Ͽ��� ��
	  2. �������(�Ǵ� '��' )�� �д�(���κ����� ��1����2 ��4ȣ�� �����д� �� ���� ����39����9�� ���������� ���Ѵ�)�� ���Ͽ� '��'  ��\n       �ǰ��� ���ϰ� �ϰų�, ����� �̸��� �Ͽ��� ��\n �� ���� �� ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� '��' ���� ����� �䱸�� �� ����.
	  1. �ڿ��� �Ǵ� ��ȯ�� ���Ͽ� ��� �Ͽ��� ��
	  2.'��' �� ������ �����ǹ��� ���������� ���Ƿ� �����Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��
	  3. õ���������� ���Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��
	  4. '��'  �� ���� �Ǵ� �߰��Ƿ� ���Ͽ� ���ظ� ���߰ų� ����Ͽ��� ��");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+69, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"��15��(��Ÿ)", 'font_bold'=>'B');

	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+74, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"�� �� ��༭���� �������� ���� ������ �ι��̳� ��ȸ��Կ� ������.\n�� �ε����� �������� �Ҽ��� ����� ��� '��'  (�Ǵ� '��' ) �Ǵ� �ü��� ���� ����������\n    ���ҹ������� �Ѵ�.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+95, 'font_size'=>14, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  ���� ���� ����� ü���ϰ� �� ���ü���� �����ϱ� ���Ͽ� �ֹ��� ��༭�� �ۼ� ���� �� ���� 1�ξ� ����Ű�� �Ѵ�.");
	
	set_array_text($pdf, $pos);
	unset($pos);
	unset($pos_x);
	unset($pos_y);

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+45,$st_getY+125);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, substr($ct['reg_dt'],0,4)."��", 0, 0, "R");
	$pdf->Cell($pdf->width*0.125, $pdf->row_height, substr($ct['reg_dt'],5,2)."��", 0, 0, "R");
	$pdf->Cell($pdf->width*0.125, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2))."��", 0, 1, "R");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
	$pdf->Text($pdf->left+5,$pdf->getY()+15,"��� ���뿡 ���� ����� ������ '��'  �� '��' ���� �����Ͽ����ϴ�.");
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 177, 170, '20', '20');	//��� ����
		}
	}

	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+25);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �����", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $center['manager']."   (��)", 0, 1, "R");
	

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
	$pdf->Text($pdf->left+5,$pdf->getY()+15,'��� ������ �а� �� ���뿡 �����մϴ�.');
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+30);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �̿���", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['name']."   (��)", 0, 1, "R");
	
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+10);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �븮��", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['bohoName']."   (��)", 0, 1, "R");
	
	set_array_text($pdf, $pos);
	unset($pos);
	unset($pos_x);
	unset($pos_y);
	
	
	# �̿��༭
	
	$pdf->MY_ADDPAGE();

	$pdf->SetXY($pdf->left+5, $pdf->top+5);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[���� ��5ȣ����]',0,1,'L');

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+11, $pdf->width-10, $pdf->height-30);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 18);
	$pdf->SetXY($pdf->left, $pdf->top+25);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "�������� ���� �� Ȱ�� ���Ǽ�", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "�� ��:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, $su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, "(������� :    ".$myF->issToBirthday($su['jumin'],'.'), 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, ")", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height+3, "�� ��:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $juso[0], 0, 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "1. ���� �� �̿����", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "�� �����޿� ���� ����
�� �̿����� �������� ���� ����
�� ���ñ�� �������� ��û�� �ʿ��� ����
�� ��Ÿ ������� ���࿡ �ʿ��� ����
�� ����� �޿� ���ÿ� �ʿ��� ������ Ȱ��
�� ������� ���� ���� ����� ���û��׿� ���� ����� ���� ����
�� ���ñ�� �������� ��û�� ����
�� ������ȹ, �屸����, �����缭�� �� ���� ��� � Ȱ��", 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "2. �̿�Ⱓ �� �����Ⱓ", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "�� �̿�Ⱓ : �޿������Ϻ��� �޿����Ⱓ ����(����)�ϱ����� ��
�� �����Ⱓ : �޿������Ϻ��� �޿����Ⱓ ����(����) �� 5������� ��", 0, "L");

	$pdf->SetXY($pdf->left+15, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "3. �����׸�", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "�� ���νĺ�����(����, �ֹε�Ϲ�ȣ, �ܱ��ε�Ϲ�ȣ)
�� ��������(�ּ�, ����ó, ��������)
�� ����", 0, "L");

	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "��� ������ ���������� �����ϰ� Ȱ���ϴ� �Ϳ� �����մϴ�.", 0, "L");
	
	$pdf->SetXY($pdf->width*0.43, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");
	
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "�� �� �� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['name'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (��)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "�� ȣ �� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['bohoName'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (��)", 0, 0, "L");

	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
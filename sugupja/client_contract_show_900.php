<?
	include_once("../inc/_definition.php");


	$conn->set_name('euckr');

	$code = $_SESSION['userCenterCode'];	//�����ȣ
	$kind = $_POST['kind'];					//���񽺱���
	$ssn = $ed->de($_POST['jumin']);		//�������ֹι�ȣ
	$svc_seq   = $_POST['svc_seq'];			//���򰡰���(���Ű)	

	$report_id = $_POST['report_id'];		//���򰡰���(�̿��༭)
	$seq  = $_POST['seq'];
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		if($report_id != ''){
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,	   from_dt
					,	   to_dt
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,      use_yoil3
					,      from_time3
					,      to_time3
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text1
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
					,	   from_dt
					,	   to_dt
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,      use_yoil3
					,      from_time3
					,      to_time3
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text1
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and seq		= \''.$seq.'\'
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
				,	   m03_juso1 as juso
				,	   m03_juso2 as juso_dtl
				,	   max(lvl.from_dt)
				,	   max(lvl.to_dt)
				,	   max(kind.from_dt)
				,	   max(kind.to_dt)
				,	   real_jumin
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
				 LEFT JOIN client_option as opt
						ON   opt.org_no = m03_ccode
						AND  opt.jumin  = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		$su = $conn->get_array($sql);
		
		$juso =  explode('<br />',nl2br($su['juso']));

		$jumin = $su['real_jumin'] != '' ? str_replace('-','', $ed->de($su['real_jumin'])) : $su['jumin'];
	}

	if(!$su['jumin']) $jumin = '';
	
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
	

	//���, ������ȣ
	$sql = 'SELECT	app_no, level
			FROM	client_his_lvl
			WHERE	org_no	 = \''.$code.'\'
			AND		svc_cd	 = \''.$kind.'\'
			AND		jumin	 = \''.$ssn.'\'
			AND		from_dt	<= \''.str_replace('-','',$ct['from_dt']).'\'
			AND		to_dt	>= \''.str_replace('-','',$ct['from_dt']).'\'';
	
	$tmpR = $conn->get_array($sql);

	$appNo = $tmpR['app_no'];
	//$level = $tmpR['level'].' '.$tmpR['addr_dtl'];
	
	$level = $myF->_lvlNm($tmpR['level']);
	

	//�����ڱ���
	$sql = 'SELECT	kind, rate
			FROM	client_his_kind
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$ssn.'\'
			AND		from_dt <= \''.str_replace('-','',$ct['from_dt']).'\'
			AND		to_dt	>= \''.str_replace('-','',$ct['from_dt']).'\'';
	
	$sKind = $conn->get_array($sql);
	
	$svcKind = $sKind['kind'];
	$svcRate = $sKind['rate'];

	//����ó
	$tel = $su['tel'] != '' ? $su['tel'] : $su['hp'];

	$bank = $center['bankNo'] != '' ? iconv('utf-8','euc-kr', $definition->GetBankName($center['bankCode']))."(".$center['bankNo'].")�� " : " ";
	

	$from_year = $from_dt != '' ? substr($from_dt,0,4) : '           ';	//�����۱Ⱓ(��)
	$from_month = $from_dt != '' ? substr($from_dt,5,2) : '     ';		//�����۱Ⱓ(��)
	$from_day = $from_dt != '' ? substr($from_dt,8,2) : '     ';			//�����۱Ⱓ(��)
	
	$to_year = $to_dt != '' ? substr($to_dt,0,4) : '           ';			//�������Ⱓ(��)
	$to_month = $to_dt != '' ? substr($to_dt,5,2) : '     ';				//�������Ⱓ(��)
	$to_day = $to_dt != '' ? substr($to_dt,8,2) : '     ';				//�������Ⱓ(��)
		

	//�̿����
	$use_yoil1 = $ct['use_yoil3'];
	
	//�̿�ð�
	$fm_h1 = $ct['from_time3'] != '' ? substr($ct['from_time3'],0,2) : '     ';
	$fm_m1 = $ct['from_time3'] != '' ? substr($ct['from_time3'],2,2) : '     ';
	$to_h1 = $ct['to_time3'] != '' ? substr($ct['to_time3'],0,2) : '     ';
	$to_m1 = $ct['to_time3'] != '' ? substr($ct['to_time3'],2,2) : '     ';

	$jikin = $center['jikin'];
	
	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '��';
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';
	$pay_day3  = $ct['pay_day3'] != '' ? $ct['pay_day3'] : '15';	//���κδ�ݳ�����

	$pdf->MY_ADDPAGE();
	
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg';

	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 93, $pdf->getY()+115, '20', '20');	//�� ����
	}


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "�����޿� �̿� ǥ�ؾ��\n(�־߰���ȣ)", 1,"C");
	
	
	$pdf->Image('../image/standard_mark.jpg', 140, 45, '41', '35');	//�����ŷ�����ȸ �ΰ�
	$pdf->Image('../image/standard_mark2.jpg', 141, 45, '38', '28');	//�����ŷ�����ȸ �ΰ�	
	

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+45);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "    �̿���, ������ �� �븮��(��ȣ��)�� �����޿� �̿뿡 ���Ͽ�\n ������ ���� �������� ����� ü���Ѵ�.");

	if(str_replace('-','', $from_dt) >= '20180101'){
		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+0.5);
	}else {
		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+2);
	}
	
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+15);
	$pdf->Cell($pdf->width*0.9, $pdf->row_height*2, "�������", 1, 1, "L" ,true);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.198, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"�̿���\n  (��)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 7.98, 'y'=>$pdf->GetY()*1.04, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.14, 'height'=>4.5, 'align'=>'C', 'text'=>"��  ��/\n������ȣ");
	
	
	$lvlNo = $tmpR['level'] != '' ? $myF->euckr($level)."/\n".$appNo : '';

	$pos[sizeof($pos)] = array('x'=>$pdf->left * 10.1, 'y'=>$pdf->GetY()*1.04, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'C', 'text'=>$lvlNo);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.055 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['name']);

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2.2*4.1, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*3, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*3, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*3, "", 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.34, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"������\n  (��)");

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�ֹι�ȣ", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, $myF->issStyle($jumin), 1, 0, "C");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $myF->phoneStyle($tel,'.'), 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.447, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"�븮��\n  �Ǵ�\n��ȣ��\n  (��)");
	
	if(strlen($su['juso']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$juso[0].' '.$su['juso_dtl']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$juso[0].' '.$su['juso_dtl']);
	}

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	if(str_replace('-','', $from_dt) >= '20180101'){
		$pdf->SetX($pdf->left+28.2);
		$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
		$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, " �� �Ϲ�(15%)  �� �Ƿ�(6%) �� �氨(9%) �� �氨(6%) �� ����", 1, 1, "L");

		if(strlen($center['centerName']) > 18){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}
		
		if($jikin != ''){
			$exp = explode('.',$jikin);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$jikin, 92, 186, '20', '20');	//��� ����
			}
		}
	}else {
		$pdf->SetX($pdf->left+28.2);
		$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
		$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, " �� �Ϲ�  �� �氨�����  �� �Ƿ������ �� ���ʼ��ޱ���", 1, 1, "L");

		if(strlen($center['centerName']) > 18){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}
		
		if($jikin != ''){
			$exp = explode('.',$jikin);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$jikin, 92, 186, '20', '20');	//��� ����
			}
		}
	}

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2*3, "", 'TL', 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, "", 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�����ȣ", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $center['centerCode'], 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$center['manager']);
	

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����� ����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $myF->phoneStyle($center['centerTel'],'.'), 1, 1, "C");
	
	if(strlen($su['address']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$center['address']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$center['address']);
	}
	
	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoName']);

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $su['gwange'], 1, 1, "C");
	
	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�������", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, " ".$myF->issToBirthday($su['bohoJumin']), 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $myF->phoneStyle($su['bohoPhone'],'.'), 1, 1, "C");
	
	if(strlen($su['bohoAddr']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoAddr']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoAddr']);
	}

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	
	if(str_replace('-','', $from_dt) >= '20180101'){
		if ($svcKind == '1'){
			//�Ϲ�
			$pos_x = 71;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '3'){ 
			//���ʼ��ޱ���
			$pos_x = 175;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '2'){
			//�Ƿ���ޱ���
			$pos_x = 100;
			$pos_y = 171;	
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '4'){
			//�氨�����
			if($svcRate == 9.0){
				$pos_x = 125;
			}else {
				$pos_x = 150;	
			}
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}else {
		if ($svcKind == '1'){
			//�Ϲ�
			$pos_x = 71;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '3'){ 
			//���ʼ��ޱ���
			$pos_x = 152;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '2'){
			//�Ƿ���ޱ���
			$pos_x = 121.5;
			$pos_y = 171;	
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '4'){
			//�氨�����
			$pos_x = 90;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	

	

	set_array_text($pdf, $pos);
	unset($pos);
	unset($pos_x);
	unset($pos_y);

	$pdf->MY_ADDPAGE();
	
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+10,'��1��(����)');
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+12, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "����̳� ���μ����� ������ ���Ͽ� ȥ�ڼ� �ϻ��Ȱ�� �����ϱ� ����� ���ε� �� ��������� ���� �е鿡�� �־߰���ȣ�޿��� �����Ͽ� ������ �ǰ����� �� ��Ȱ������ �����ϰ� �� ������ �δ��� ���������ν� ���� ���� ����Ű���� �Ѵ�. ")));

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+38,'��2��(���Ⱓ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+40, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ���Ⱓ�� ".$from_year."�� ".$from_month."�� ".$from_day."�Ϻ��� ".$to_year."�� ".$to_month."�� ".$to_day."�ϱ����� �Ѵ�.\n�� ��1���� ���Ⱓ�� ����� ���� ���ǿ� ���� ������ �� �ִ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+61,"��3��(�޿�����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+64, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "�־߰���ȣ�޿��� �����ڸ� �Ϸ� �� ������ �ð� ���� ��������� ��ȣ�Ͽ� ��üȰ�� ���� �� �ɽű���� ��������� ���� �����Ʒ� ���� �����ϴ� �����޿��� �Ѵ�.")));
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+85,"��4��(�޿��̿� �� ����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+88, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �־߰���ȣ�޿� �̿� �� ������ �����޿� �̿�(����)��ȹ���� ���Ѵ�.\n�� '��'  �� �־߰���ȣ�޿� �̿�ð��� �Ʒ��� ���� �Ѵ�. �ٸ�, �� �ð��� ��������� �������� ������ �������� ������ ������ ��Ŵ� �帰�ð������� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY()+116, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.2, 'height'=>6, 'align'=>'C', 'text'=>"�̿�\n�ð�\n(1)");

	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+119.5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>"���  ��ȭ  ���  ���\n ���  ����  ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+121, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>$fm_h1."�� ".$fm_m1."��    ~".$to_h1."�� ".$to_m1."��");
	
	
	$pdf->SetXY($pdf->left, $pdf->getY()+107);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿�ð�", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*4, "", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 1, "C");
	
	$pos_x = 67;
	$pos_y = 131; 
	
	
	for($i=0; $i<strlen($use_yoil1); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 6;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil1[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}

	$pos_x = 67;
	$pos_y = 150; 
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+2, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ������ ���� �Ͻ������� �̿�ð��� ��Ű�� ����� ��� ���� �̿����( 1�ð� ���� '��' ���� ������ ���ؾ���.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+17, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� �־߰���ȣ�޿� �����ð��� �Ʒ��� ����.");
	
	$pdf->SetXY($pdf->left, $pdf->getY()+25);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�ð�", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "���", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "��~��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "(����) 07:00~20:00", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "(����)", "TLR", 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "(����) 08:00~18:00", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "������ ��������", "BLR", 1, "C");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+3, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� �Ϳ� �����޿� ������ �ϰ����ϴ� ��쿡�� '��' *�Ǵ� '��') �� �����Ͽ� \n     �޿����� ������ �޿���ȹ���� �ۼ��ϰ� ������(��ȣ��)Ȯ�ι޾� �޿����񽺸� �ǽ��Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+30,'��5��(����� �ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+33, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� ���� �� ȣ�� �ǹ��� �����ϰ� �����Ͽ��� �Ѵ�.\n  1. �� �̿�� �����ǹ�\n  2. �־߰���ȣ�޿� ������ ���� �̿�\n  3. �����޿� �̿��Ģ �ؼ�\n  4. ��Ÿ '��' �� ������ ��Ģ ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n  1. �־߰���ȣ�޿� ���� ��೻�� �ؼ�\n  2. �޿� �����ð��� '��' ���� �Ͼ �ź��̻� ���Ͽ� ��� '��' ���� �뺸");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY(), 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"  3. '��' �� �ǰ����¸� ����� �־߰��޿� ������ȹ�� �����Ͽ� '��'(�Ǵ� '��')���� �����ϰ�\n    ������ ����\n  4. �޿����� �� �˰� �� '��' �� �Ż� �� ��ȯ�߿� ���� �������\n    (��, ġ�� ���� ��ġ�� �ʿ��� ���� ����)\n  5. '��' �� �Ļ�����, �̿���, �̿����� ����\n  6. '��' �� �ǰ����� ���α׷� �� Ȱ�� ����\n  7. �����д� ���� �� �����α� ��ȣ �ؼ�\n  8. �ǹ� �� �δ�ü��� û�� �� ��������\n  9. ��Ÿ '��' (�Ǵ� '��' )�� ��û�� ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n  1.'��' �� ���� �ǰ� �� �ʿ��� �ڷ�����\n  2. '��' �� �� �̿�� �� �Լ��̿� �δ�\n  3. ���� ���� �� ����纸�� ��� ���� �� ��� '��' ���� �뺸\n  4. '��' �� ���� �ǹ������� ������ �븮�� ���� �� '��' ���� �뺸\n  5. ��Ÿ '��' �� ������û ����");


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+99,'��6��(������� ���)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+102, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ� '��' )�� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��' �� �����Ͽ� ����� ���� �� �� �ִ�.
	  1. ��2���� ���Ⱓ�� ����� ���
	  2. ��3���� �־߰���ȣ�޿� ������ �ش��ϴ� ���񽺸� �������� �ƴ��� ���
	  3. ��4����2���� �־߰���ȣ�޿� �����ð���'��'  (�Ǵ�'��' )�� ���� ���� '��' �� ����\n       �� �����ϰų� ��ġ�� ��������� ���Ƿ� ���� ���� ���
	�� '��' �� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� �����Ͽ� ����� ���� �� �� �ִ�.
	  1. ��2���� ���Ⱓ�� ����ǰų� ����� ���
	  2. '��'  �� ����纸�� ��޿��ڷ� ��޺����� �߻��� ���
	  3. '��'  �� �ǰ����� ������������ǿ���װ��������ѹ������� ���� ������ ȯ�ڷμ� ��\n       ���� ���輺�� �ִ� ���� ������ ��
	  4. '��'  �� �ǰ����� ������ ���� �̿��� ����� ��
	  5. �̿���� ���õ� �̿�ȳ��� '��'  �� ������ ���� ���� ������ �ʴ� �� ���� ������\n       �ɰ��� ������ �� ��
	  6. '��'  �� �� 5ȸ �̻� �������� �־߰���ȣ�޿� �̿�ð��� ��Ҹ� ��Ű�� �ƴ��Ͽ��� ��");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+194,'��7��(����� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+197, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ�'��' )�� ��6����1���� ������� ����� �߻��� ��쿡�� �ش��� �Ǵ� ����\n    �� ���������� ���� ��2ȣ������ �����޿� ���� ��û���� �����Ͽ��� �Ѵ�. �ٸ�, ��Ÿ\n    �ε����� ��쿡�� �켱 �������� �� �� �ִ�.\n�� '��' �� ��6����2�׿� ���� ������� ����� �߻��� ��쿡�� ������� �ǻ縦 ���� ��2ȣ\n    ������ �����޿� ����ȳ��� �� ���� ���������� �Բ� '��'  �� '��' ���� �뺸�ϰ�\n    ����� �����ؾ� �Ѵ�.\n�� '��' (�Ǵ� '��' )�� ��1�� �� ��2������ ��������� �߻��ϴ� ��쿡�� �־߰���ȣ�ü� ����\n    '��' �� ���Ϲ�ǰ�� �μ��Ͼ߾� �Ѵ�. �ٸ�, ���ι�ǰ�� 1���� �̳��� �μ����� ���� ��쿡\n    �� '��' �� ���, �ù� �� ������ Ȯ���� �� �ִ� ����� ���Ͽ� ��ǰ�� '��' (�Ǵ� '��' )���� \n    �۴�ó�� �Ѵ�. ");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��8��(���̿� ������)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '������ �� 15�� �̻� �޿������ ü���� �� '������ �������� �̿����� �ƴ��� ��쿡��\n    �� 3���� ���� �ȿ��� �����޿� ������ȹ�� �� �̿뿹�� �޿������ 50%�� û����\n    �� �ִ�.\n�� ��1���� ���̿��Ͽ� ���� ������ ����(�����ݿ���)�������� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+40,'��9��(�̿�� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+43, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �־߰���ȣ �޿���� �� ���κδ� ������ ��ǥ 1�� ����.\n�� �־߰���ȣ ��޿�����׸� �� ��Ÿ �Ǻ� ���������� ��ǥ 2�� ����.�� '��' �� ���� 1�Ϻ��� ���ϱ����� �̿�Ḧ �ſ�  ".$pay_day1."�Ͽ� �����ϰ� '��'  (�Ǵ� '��' )��\n   ��  ".$pay_day2."�ϱ��� ���� ��3ȣ������ �����޿� �̿�� ���γ������� �뺸�Ѵ�.\n�� '��'  �� �ſ� ".$pay_day3."�ϱ��� ".$bank."���κδ���� ���� �Ѵ�. �ٸ�, �������� �������� ��쿡�� �� ���Ϸ� �Ѵ�.\n�� '��' �� '��'  �� ������ ��뿡 ���ؼ��� ��������纸��� �����Ģ[���� ��4ȣ����]�� �����޿� ����Ȯ�μ���\n      �߱��Ѵ�.");


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+100,'��10��(����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+103, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ��ȣ�� �ش��ϴ� ��쿡�� �̸� �ݿ��� ��༭�� ���ۼ��Ѵ�.
   1. ��2���� ���Ⱓ�� ����� ���
   2. ����� ��������� ����� ���
   3. �־߰���ȣ �޿���� �� ���κδ� ����� ����� ���
   4. ��Ÿ '��'  �� '��' �� �ʿ��� ���");

   $pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+142,'��11��(���ι�ǰ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+145, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'�� �־߰���ȣ�ü� �̿�ÿ��� �ʿ��� ���ι�ǰ���������� �����Ͽ��� �Ѵ�.\n�� '��'�� ������ ��ǰ�̿��� ���ι�ǰ�� ����ϰ��� �� ������ '��'�� ���� �Ͽ� ����Ͽ�\n    �� �Ѵ�.");

	 $pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+172,'��12��(�ü�����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+175, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'�� '��' �Ǵ� '��'�� �����޿��� ���õ� ��� �Ǵ� ��Ÿ ����ó���� ���� �ü��� ��\n    ������ ���� �������� ������ ������ �����Ͽ��� �Ѵ�.\n�� '��'�� ����, ����, ���, ��ȭ ��Ÿ �ü��̿뿡 �ʿ��� ���� ������ ���Ͽ� �ü��� ������\n    ������ ���ؾ� �Ѵ�. ");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+208,'��13��(�ǰ�����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+211, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� '��'  �� �ǰ� �� ������ ������ ���Ͽ� �����ڵ鿡�� �� 1ȸ �̻� �ǰ������� �ǽ�\n     �Ͽ��� �Ѵ�.\n�� '��'�� �������� ���Ͽ� ���ᰡ �ʿ��ϴٰ� �Ǵ� �� ������ '��' ���� ��� �뺸�ϰ� ������\n    ��ġ�� ���Ͽ��� �Ѵ�.\n�� '��' �� ��2���� ������ ���� �뺸�� �޾��� ��쿡�� �ż��ϰ� ��ó�Ͽ��� �Ѵ�.\n �� '��' �� �־߰���ȣ�޿� �������� '��' ���� ���ظ� ������ ��쿡�� '��' ���� ������ ��ġ��\n    ���ؾ� �Ѵ�.");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��14��(�ü��� ���)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� '��' �� �ü����� ���Ͽ� �� ������ �뵵�� ����ؾ� �ϸ�, '��' �� ���� �ļ� �Ǵ� ���\n    �� ���Ͽ��� '��' (�Ǵ� '��' )�� ����ȸ�� �Ͽ��� �Ѵ�.\n �� '��' �Ǵ� '��' �� ����ȸ���� �� �� ���� ������ '��' �� �ü����� ������ġ ���� ����Ͽ� ��\n    ��� ������ ����� �� ������ �Բ� �����ϰ� '��' �Ǵ� '��' �� �̿� ���Ͽ� �����Ͽ��� �Ѵ�. ");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+42,'��15��(���� �� ��ġ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+45, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� �־߰���ȣ�޿� �����ð��� '��'  �� ������ ������ ���¶�� �Ǵܵ� ������ '��'  (\n    �Ǵ� '��' )�� ������ ���� �Ǵ� ���� �Ƿ������� ��� �ļ��ϰ� '��' ���� ��� �뺸\n    �Ͽ��� �Ѵ�.\n�� '��' �� ��1���� ������ ���� �뺸�� �޾��� ������ �ż��ϰ� ��ó�Ͽ��� �Ѵ�. �ٸ�, ��ó\n    �� ����� ��쿡�� �켱 ���Ḧ ���� �� �ֵ��� ��ġ�Ͽ��� �Ѵ�.\n�� '��'�� ���� �̿뵵�� ����Ͽ��� ��쿡�� '��' �� ��� '��' ���� �뺸�ϰ� �ٸ� �̿��ڵ�\n    �� �������� �ʵ��� ��ġ�� ���Ͽ��� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+95,'��16��(�������� ��ȣ�ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+98, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  �� ������ ���������� ���� �� �Ǹ��� �ִ�.\n�� '��' �� '��'  �� ���������� ��������� ���� ��ȣ�Ͽ��� �Ѵ�.\n�� '��' �� ����缭�� ������ �ʿ��� '��'  �� ���� ���� �ڷḦ �����ϰ� Ȱ���ϸ� ��\n    �ڷḦ ��������纸�� ���ü ��� ��������� ���� ������ �� �ִ�.\n�� '��' �� ������������ �� Ȱ���� �ϰ��� �ϴ� ��쿡�� '��'  ���� ���� ��5ȣ������ ����\n    �������� �� Ȱ�� ���Ǽ��� �޾ƾ� �Ѵ�.\n�� '��' �� '��'  �� ���Ȱ�� �����ϰ�, ������ �˰� �� ���������� ö���� ����� �����Ѵ�. ");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+148,'��17��(��� �� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+151, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�������� ��Ȱ�� ����缭�񽺿� ���� ��� ������ ���� �����Ͽ� ��Ȯ�� ����ϰ�, '��'(�Ǵ� '��') �� �䱸�� ��쿡�� ǥ�ؾ�Ŀ� �ǰ��� ����� �����Ͽ��� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+171,'��18��(���å��)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+174, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� ���� ��ȣ�� ��쿡��'��'  (�Ǵ�'��' )���� ����ǹ��� ������ ���å���� ��\n    ������� ������.\n  1. �������(�Ǵ� '��') �� ���ǳ� ���Ƿ� ���Ͽ� '��' �� �λ��� �ϴ� �� �ǰ��� ���ϰ� ��\n      �ų� ����� �̸��� �Ͽ��� ��\n  2. �������(�Ǵ¡�����)�� �д�(���κ����� ��1����2 ��4ȣ�� �����д� �� ���� ��\n      ��39����9�� ���������� ���Ѵ�)�� ���Ͽ� '��' �� �ǰ����°� ��ȭ�ǰų�, ����� �̸���\n      �Ͽ��� ��\n  3. �ü���� �Ǵ� �ü������� �ν��Ͽ� '��' �� �λ��� �ϰų� ����� �̸��� �Ͽ��� ��\n  4. ���ü����� ��ȣ�ϰ� �ִ� �� ���� ������ �����ϴ� �� '��' �� �ǰ��� ���ϰ� �ϰų� ��\n    ���� �̸��� �Ͽ��� ��\n�� ���� �� ȣ�� �ش�Ǵ� ��쿡�� '��' (�Ǵ� ������)�� '��' ���� ����� �䱸�� �� ����.\n  1. �ڿ��� �Ǵ� ��ȯ�� ���Ͽ� ��� �Ͽ��� ��\n  2. '��' �� ������ �����ǹ��� ���������� ���Ƿ� �����Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��\n  3. õ���������� ���Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��\n  4. '��' �� ���� �Ǵ� �߰��Ƿ� ���Ͽ� ���ظ� ���߰ų� ����Ͽ��� ��");

	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��19��(��Ÿ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �� ��༭���� �������� ���� ������ �ι��̳� ��ȸ��Կ� ������.\n�� �ε����� �������� �Ҽ��� ����� ��� '��'  (�Ǵ� '��' ) �Ǵ� �ü��� ���� ����������\n    ���ҹ������� �Ѵ�.");
	
	
	if($ct['other_text1'] != ''){
		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
		$pdf->Text($pdf->left,$pdf->getY()+102,'��20��(��÷����)');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+105, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ������� ���� Ư�̻����� #��÷1 �� ���� �Ǿ� �ִ�.");

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
		$pdf->SetXY($pdf->left+45,$pdf->GetY()+117);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, substr($ct['reg_dt'],0,4)."��", 0, 0, "R");
		$pdf->Cell($pdf->width*0.125, $pdf->row_height, substr($ct['reg_dt'],5,2)."��", 0, 0, "R");
		$pdf->Cell($pdf->width*0.125, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2))."��", 0, 1, "R");
		
		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
		$pdf->Text($pdf->left+5,$pdf->getY()+15,"��� ���뿡 ���� ����� ������ '��'  �� '��' ���� �����Ͽ����ϴ�.");
		
		if($jikin != ''){
			$exp = explode('.',$jikin);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$jikin, 177, 150, '20', '20');	//��� ����
			}
		}

		
		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
		$pdf->SetXY($pdf->left+70,$pdf->GetY()+25);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �����", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $center['manager']."   (��)", 0, 1, "R");
		

		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
		$pdf->Text($pdf->left+5,$pdf->getY()+15,'��� ������ �а� �� ���뿡 �����մϴ�.');
		
		if(file_exists($file) and is_file($file)){
			$pdf->Image($file, 175, $pdf->getY()+22, '20', '20');	//�� ����
		}

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
		$pdf->SetXY($pdf->left+70,$pdf->GetY()+30);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �̿���", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['name']."   (��)", 0, 1, "R");
		
		$pdf->SetXY($pdf->left+70,$pdf->GetY()+10);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �븮��", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['bohoName']."   (��)", 0, 1, "R");
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+105, 'font_size'=>15, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  ���� ���� ����� ü���ϰ� �� ���ü���� �����ϱ� ���Ͽ� �ֹ��� ��༭�� �ۼ� ���� �� ���� 1�ξ� ����Ű�� �Ѵ�.");
	
		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
		$pdf->SetXY($pdf->left+45,$pdf->GetY()+135);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, substr($ct['reg_dt'],0,4)."��", 0, 0, "R");
		$pdf->Cell($pdf->width*0.125, $pdf->row_height, (substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2))."��", 0, 0, "R");
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
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �ü���", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $center['manager']."   (��)", 0, 1, "R");
		

		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
		$pdf->Text($pdf->left+5,$pdf->getY()+15,'��� ������ �а� �� ���뿡 �����մϴ�.');
		
		if(file_exists($file) and is_file($file)){
			$pdf->Image($file, 175, $pdf->getY()+22, '20', '20');	//�� ����
		}

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
		$pdf->SetXY($pdf->left+70,$pdf->GetY()+30);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �̿���", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['name']."   (��)", 0, 1, "R");
		
		$pdf->SetXY($pdf->left+70,$pdf->GetY()+10);
		$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'��'  �븮��", 0, 0, "R");
		$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['bohoName']."   (��)", 0, 1, "R");
	}
	
	

	set_array_text($pdf, $pos);
	unset($pos);
	
	
	if($ct['other_text1'] != ''){
		$pdf->MY_ADDPAGE();

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 5);
		$pdf->Text($pdf->left,$pdf->getY()+15,'#��÷1');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+22, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes($ct['other_text1']));
		set_array_text($pdf, $pos);
		unset($pos);
	}

	/*
	$pdf->MY_ADDPAGE();

	$pdf->SetXY($pdf->left+5, $pdf->top);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[��ǥ 1]',0,1,'L');
	
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 15);
	$pdf->SetXY($pdf->left, $pdf->top+15);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "�־߰���ȣ �޿���� �� ���κδ� ����(         .     .      )", 0, 1, "C");
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width, $pdf->row_height, "�� �־߰���ȣ �̿�ð��� �޿����(��)", 0, 0, "L");
	
	$rowHigh = 10;

	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "�� ��", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "����� 1���", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "����� 2���", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "����� 3���" , 1, 1, "C", true);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "3�ð� �̻�~6�ð��̸�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "" , 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "6�ð� �̻� ~ 8�ð� �̸�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "" , 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "8�ð� �̻� ~ 10�ð� �̸�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "" , 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "10�ð� �̻� ~ 12�ð� �̸�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "" , 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.31, $rowHigh, "12�ð� �̻�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.23, $rowHigh, "" , 1, 1, "C");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width, $pdf->row_height, "�� ��޺� �簡�޿� �� �ѵ���(��)", 0, 0, "L");

	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "1���", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "2���", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "3���", 1, 1, "C", true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.33, $rowHigh, "" , 1, 1, "C");
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ���޿������ �ų� ���������ȸ(������ : ���Ǻ����� ����)�� ����, ����ϴ� �����޿���� � ���� ���(���Ǻ����� ���)�� ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+65, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.7, 'height'=>6, 'align'=>'L', 'text'=>"��Ÿ �Ƿ���ޱ���\n������ �Ƿ�޿� �ǰ����� �ڰ���ȯ�� (��ͳ�ġ��, ������ȯ��) ���ҵ��� (�����Ϻκδ�� ������ ���� �ҵ�.��� ���� �����ݾ� ������ �ڿ� ���� ��� �ش���)");

	$pdf->SetXY($pdf->left, $pdf->GetY()+22);
	$pdf->Cell($pdf->width, $pdf->row_height, "�� ������ �ڰݺ� �޿���� �����Ϻκδ� ����", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.70, $rowHigh, "����", 1, 0, "C", true);
	$pdf->Cell($pdf->width * 0.30, $rowHigh, "�簡�޿�", 1, 1, "C", true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.70, $rowHigh, "�Ϲ�", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.30, $rowHigh, "15%", 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.70, $rowHigh, "���ʼ��ޱ���", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.30, $rowHigh, "0%", 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.70, $rowHigh*3, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.30, $rowHigh*3, "7.5%", 1, 1, "C");


	set_array_text($pdf, $pos);
	unset($pos);
	

	$pdf->MY_ADDPAGE();


	$pdf->SetXY($pdf->left+5, $pdf->top);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[��ǥ 2]',0,1,'L');
	
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 17);
	$pdf->SetXY($pdf->left, $pdf->top+15);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "��޿� ��� �׸� �� ��Ÿ �Ǻ� ���� ����", 0, 1, "C");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>14, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'text'=>"1. ��޿� �׸� ���α���\n  1) �⺻��Ģ : ��޿� �׸��� �Ļ�����, ���ħ�� �̿뿡 ���� �߰����, �̡�\n     �̿���̸�, ���� �ҿ��븸�� �����ؾ� ��. �� ���� ����� ������� ���Ƿ�\n     ������ �� ����.\n  2) ���α���\n    (1) �Ļ����� : ������� ������, ���ĵ� �������� ������\n    (2) �̡��̿�� : ������ ����� ���� �̡��̿�� �ʺ��Ͽ� ���� �޴� ���\n        ��޿� ����, �� �ü������ڡ��ڿ������ڿ� ���� �����Ǵ� ���񽺴� ���\n        �� �׸����� ���� �Ұ�, �ա����� ���� ���� ������� ���� ������ �Ұ�\n\n2. ��޿� �׸� �� �Ǻ� �������� \n  1) �⺻��Ģ : �����ڰ� ���������� �䱸�ϴ� ��ǰ �� �뿪�� �ü����� ������\n     �� �����ϴ� ��� �Ǻ� ������ �� ������, ����� �Ǻ� �̿ܿ� �߰������\n      �������� ����\n  2) ���α���\n    (1) �־߰���ȣ�� �̿��ϴ� �������� ������ ��� : ��뷮�� ���� �Ǻ����\n        ����, �Ǵ� �����ڰ� ���� ��� �̿��ڰ� ���� ������ �����͸� �̿���� ��\n    (2) ��ȣǰ �� �������� ����� ���� �ϻ��ǰ ���Ժ�� : �Ϸ������� ������\n        �� �ϻ��ǰ(����, ��, ����, �ǳ�ȭ ��)�� ���ؼ��� ������ �Ұ� \n    (3) ���� ���α׷� ��� : ��Ģ������ ���α׷� ��� �����޿��� ��ȯ��\n        �� �����Ǵ� �⺻ ���� ���ֿ� �ش��ϹǷ� ���� �������� �Ұ�. ��,\n        �������� ������ ����� ���� �ܺ��� ���� �����ڰ� ������ ������� ��\n        ���ϴ� �Ϳ� ���ؼ� �����ڰ� �Ǻ� �δ��ϴ� ���� ����");

	set_array_text($pdf, $pos);
	unset($pos);

	*/

	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+225, '20', '20');	//�� ����
	}
	

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
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, "(������� :    ".$myF->issToBirthday($jumin,'.'), 0, 0, "C");
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
<?
	include_once("../inc/_definition.php");
	
	$conn->set_name('euckr');

	$code = $_SESSION['userCenterCode'];	//�����ȣ
	$kind = $_POST['kind'];				//���񽺱���
	$ssn = $ed->de($_POST['jumin']);	//�������ֹι�ȣ
	$svc_seq   = $_POST['seq'];			//���򰡰���(���Ű)	
	$seq   = $_POST['seq'];			//���򰡰���(���Ű)	
	
	$report_id = $_POST['report_id'];	//���򰡰���(�̿��༭)
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		if($report_id != ''){
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,	   from_dt
					,	   to_dt
					,      use_yoil1_nurse
					,      from_time1_nurse
					,      to_time1_nurse
					,      use_yoil2_nurse
					,      from_time2_nurse
					,      to_time2_nurse
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
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
					,      use_yoil1_nurse
					,      from_time1_nurse
					,      to_time1_nurse
					,      use_yoil2_nurse
					,      from_time2_nurse
					,      to_time2_nurse
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
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
	
	//����
	$jikin = $center['jikin'];

	if($ct['seq'] == 0 && $code == '34311000305'){
		//�ູ���簡 
		unset($center);
	}
	
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
	$use_yoil1 = $ct['use_yoil1_nurse'];
	$use_yoil2 = $ct['use_yoil2_nurse'];
	
	//�̿�ð�
	$fm_h1 = $ct['from_time1_nurse'] != '' ? substr($ct['from_time1_nurse'],0,2) : '     ';
	$fm_m1 = $ct['from_time1_nurse'] != '' ? substr($ct['from_time1_nurse'],2,2) : '     ';
	$to_h1 = $ct['to_time1_nurse'] != '' ? substr($ct['to_time1_nurse'],0,2) : '     ';
	$to_m1 = $ct['to_time1_nurse'] != '' ? substr($ct['to_time1_nurse'],2,2) : '     ';
	$fm_h2 = $ct['from_time2_nurse'] != '' ? substr($ct['from_time2_nurse'],0,2) : '     ';
	$fm_m2 = $ct['from_time2_nurse'] != '' ? substr($ct['from_time2_nurse'],2,2) : '     ';
	$to_h2 = $ct['to_time2_nurse'] != '' ? substr($ct['to_time2_nurse'],0,2) : '     ';
	$to_m2 = $ct['to_time2_nurse'] != '' ? substr($ct['to_time2_nurse'],2,2) : '     ';

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
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "�����޿� �̿� ǥ�ؾ��\n(�湮��ȣ)", 1,"C");
	
	
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
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*3, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*3, "", 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.34, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"������\n  (��)");

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�ֹι�ȣ", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->issStyle($jumin), 1, 0, "C");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($tel,'.'), 1, 1, "C");
	
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

		if(strlen($center['centerName']) > 22){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
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

		if(strlen($center['centerName']) > 22){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
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
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, "", 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�����ȣ", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $center['centerCode'], 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$center['manager']);
	

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����� ����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($center['centerTel'],'.'), 1, 1, "C");
	
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
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " ".$myF->issToBirthday($su['bohoJumin']), 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "����ó", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($su['bohoPhone'],'.'), 1, 1, "C");
	
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
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+10,'��1��(����)');
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+12, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "����̳� ���μ����� ������ ���Ͽ� ȥ�ڼ� �ϻ��Ȱ�� �����ϱ� ����� ���ε� �� ��������� ���� �е鿡�� �湮��ȣ�޿��� �����Ͽ� ������ �ǰ����� �� ��Ȱ������ �����ϰ� �� ������ �δ��� ���������ν� ���� ���� ����Ű���� �Ѵ�. ")));

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+38,'��2��(���Ⱓ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+40, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ���Ⱓ�� ".$from_year."�� ".$from_month."�� ".$from_day."�Ϻ��� ".$to_year."�� ".$to_month."�� ".$to_day."�ϱ����� �Ѵ�.\n�� ��1���� ���Ⱓ�� ����� ���� ���ǿ� ���� ������ �� �ִ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+61,"��3��(�޿�����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+64, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "�湮��ȣ�޿��� ��������� ��ȣ�� ���� �ǻ�, ���ǻ� �Ǵ� ġ���ǻ��� ���ü��� ���� �������� ���� ���� �湮�Ͽ� ��ȣ ������ ����, ��翡 ���� ��� �Ǵ� �������� ���� �����ϴ� �����޿��� �Ѵ�.")));
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+89,"��4��(�޿��̿� �� ����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+92, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �湮��ȣ�޿� �̿� �� ������ �����޿� �̿�(����)��ȹ���� ���Ѵ�.\n�� '��'  �� �湮��ȣ�޿� �̿�ð��� �Ʒ��� ���� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY()+113, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.2, 'height'=>6, 'align'=>'C', 'text'=>"�̿�\n�ð�\n(1)");

	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY()+137, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.2, 'height'=>6, 'align'=>'C', 'text'=>"�̿�\n�ð�\n(2)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+116.5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>"���  ��ȭ  ���  ���\n ���  ����  ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+140.5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>"���  ��ȭ  ���  ���\n ���  ����  ����");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+118, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>$fm_h1."�� ".$fm_m1."��    ~".$to_h1."�� ".$to_m1."��");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+142, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>$fm_h2."�� ".$fm_m2."��    ~".$to_h2."�� ".$to_m2."��");

	$pdf->SetXY($pdf->left, $pdf->getY()+105);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿�ð�", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*4, "", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*4, "", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 1, "C");
	
	$pos_x = 67;
	$pos_y = 128; 
	
	
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
	$pos_y = 152; 
	
	for($i=0; $i<strlen($use_yoil2); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 6;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil2[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+2, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ���Ͽ� ���� �̿�ð��� �ٸ� ��� �̿�ð� ������� �÷��� �����\n�� '��'   �Ǵ� '��' �� ������ ���� �Ͻ������� �̿�ð��� ��Ű�� ����� ��� ����  �̿�\n     ���� �ּ� 1�ð� ���� '��' ���� ������ ���ؾ� ��.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+25, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �������� �����Ͽ� ���� �������� ���� �����Ͽ� �޿��� �����ϴ� ��쿡�� '��' ��\n     30%�� ��������� û���� �� �ִ�.\n�� �߰�(18:00~22:00), �ɾ�(22:00~06:00)�� �޿��� �����ϴ� ��쿡�� '��' �� �߰�\n     20%, �ɾ� 30%�� ���� ����� û���� �� �ִ�.\n�� �߰��ɾ����ϰ����� ���ÿ� ����Ǵ� ��쿡�� �ߺ��Ͽ� �������� �ʴ´�.\n�� '��' �� �Ϳ� �����޿� ������ �ϰ����ϴ� ��쿡��'��'  (�Ǵ� '��' )�� �����Ͽ�\n     �޿����� ������ �޿���ȹ���� �ۼ��ϰ� ������(��ȣ��)Ȯ�ι޾� �޿����񽺸�\n     �ǽ��Ѵ�.");
		//��� ?�ϱ��� ���� ��1ȣ������ �����޿� �̿��ȹ���� �ۼ��ϰ� �����޿� �̿�\n     ��ȹ���� ���� �����޿��� ���� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+80,'��5��(����� �ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+83, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �� �̿�� ����\n   2. �湮��ȣ�޿� ������ �޿��̿�\n   3. �����޿� �̿��Ģ �ؼ�\n");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"   4. ��Ÿ '��' �� ������ ��Ģ ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �湮��ȣ�޿� ���� ��೻�� �ؼ�\n   2. �޿����� �� '��'  ���� �ź� �̻��� ����� ��� ��� '��' ���� �뺸\n   3. �޿������ð��� '��'  �� �ֺ� �� ������� û�� �� ��������\n   4. �޿����� �� �˰� �� '��'  �� �Ż� �� ��ȯ ���� ���� �������\n  (��, ġ�� ���� ��ġ�� �ʿ��� ���� ����)\n   5. �̿���, ������ȸ �ٸ� ���� �̿� ��������\n   6. �����д� ���� �� �����α� ��ȣ �ؼ�\n   7. ��Ÿ '��'  (�Ǵ� '��' )�� ��û�� ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. '��'  �� ���� �ǰ� �� �ʿ��� �ڷ�����\n   2. '��'  �� �� �̿�� �� ��� �δ�\n   3. ���� ���� �� ����纸�� ��� ���� �� ��� '��' ���� �뺸\n   4. '��'  �� ���� �ǹ������� ������ �븮�� ���� �� '��' ���� �뺸\n   5. ��Ÿ '��' �� ������û ����");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+109,'��6��(������� ���)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+113, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ� '��' )�� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��' �� �����Ͽ� ����� ���� ��\n      �� �ִ�.
	1. ��2���� ���Ⱓ�� ����� ���
	2. ��3���� �湮��ȣ�޿� ������ �ش��ϴ� ���񽺸� �������� �ƴ��� ���
	3. ��4����2���� �湮��ȣ�޿� �����ð���'��'  (�Ǵ�'��' )�� ���� ���� '��' �� ����\n     �� �����ϰų� ��ġ�� ��������� ���Ƿ� ���� ���� ���
	4. ��Ÿ '��'  �� ������� ������ �߻��� ���\n�� '��' �� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� �����Ͽ� ����� ���� ��\n       �� �ִ�.
	1. ��2���� ���Ⱓ�� ����ǰų� ����� ���
	2. '��'  �� ����纸�� ��޿��ڷ� ��޺����� �߻��� ���
	3. '��'  �� �ǰ����� ������������ǿ���װ��������ѹ������� ���� ������ ȯ�ڷμ� ��\n     ���� ���輺�� �ִ� ���� ������ ��
	4. '��'  �� �ǰ����� ������ ���� �̿��� ����� ��
	5. �̿���� ���õ� �̿�ȳ��� '��'  �� ������ ���� ���� ������ �ʴ� �� ���� ������\n     �ɰ��� ������ �� ��
	6. '��'  �� �� 5ȸ �̻� �������� �湮��ȣ�޿� �̿�ð��� ��Ҹ� ��Ű�� �ƴ��Ͽ��� ��");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+223,'��7��(����� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+225, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ�'��' )�� ��6����1���� ������� ����� �߻��� ��쿡�� �ش��� �Ǵ� ����\n    �� ���������� ���� ��2ȣ������ �����޿� ���� ��û���� �����Ͽ��� �Ѵ�. �ٸ�, ��Ÿ\n    �ε����� ��쿡�� �켱 �������� �� �� �ִ�.\n�� '��' �� ��6����2�׿� ���� ������� ����� �߻��� ��쿡�� ������� �ǻ縦 ���� ��2ȣ\n    ������ �����޿� ����ȳ��� �� ���� ���������� �Բ� '��'  �� '��' ���� �뺸�ϰ�\n    ����� �����ؾ� �Ѵ�.");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��8��(�̿�� ����)');
	
	if($code == '32817000163' || //õ��湮
	   $code == '34812000297' ){ //�Ƹ�����
		$str = '��������纸��� �����Ģ[���� ��24ȣ����]�� �����޿������� �Ǵ� ��������纸��� �����Ģ[���� ��3ȣ����]�� �����޿� �̿�� ���γ�����';
		$str2 = '������4ȣ������ �����޿� ����Ȯ�μ� �Ǵ� �����޿����(���κδ��)��������';
	}else {
		$str = '��������纸��� �����Ģ[���� ��3ȣ����]�� �����޿� �̿�� ���γ�����';
		$str2 = '������4ȣ������ �����޿� ����Ȯ�μ���';
	}


	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �湮��ȣ�޿���� �� ���κδ� ������ ��ǥ 1�� ����.\n�� '��' �� ���� 1�Ϻ��� ���ϱ����� �̿�Ḧ �ſ�  ".$pay_day1."�Ͽ� �����ϰ� '��'  (�Ǵ� '��' )����  ".$pay_day2."�ϱ��� ".$str."�� �뺸�Ѵ�.\n�� '��'  �� �ſ� ".$pay_day3."�ϱ��� ".$bank."���κδ���� ���� �Ѵ�. �ٸ�, �������� �������� ��쿡�� �� ���Ϸ� �Ѵ�.\n�� '��' �� '��'  �� ������ ��뿡 ���ؼ��� ".$str2." �߱��Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+63,'��9��(����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+66, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ��ȣ�� �ش��ϴ� ��쿡�� �̸� �ݿ��� ��༭�� ���ۼ��Ѵ�.
   1. ��2���� ���Ⱓ�� ����� ���
   2. ����� ��������� ����� ���
   3. �湮��ȣ �޿���� �� ���κδ� ����� ����� ���
   4. ��Ÿ '��'  �� '��' �� �ʿ��� ���");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+102,'��10��(�ǰ�����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+105, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"��'��' ��'��'  �� �ǰ� �� ������ ������ ���Ͽ� �����ڵ鿡�� �� 1ȸ �̻� �ǰ������� �ǽ�\n    �Ͽ��� �Ѵ�.\n��'��' �� ��������� �湮��ȣ�޿� �������� '��'  ���� ���ظ� ������ ��� ������ ��\n      ġ�� ���ؾ� �Ѵ�.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+135,'��11��(���� �� ��ġ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+137, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� �湮��ȣ�޿� �����ð��� '��'  �� ������ ������ ���¶�� �Ǵܵ� ������ '��'  (\n     �Ǵ� '��' )�� ������ ���� �Ǵ� ���� �Ƿ������� ��� �ļ��ϰ� '��' ���� ��� �뺸\n      �Ͽ��� �Ѵ�.\n�� '��' �� ��1���� ������ ���� �뺸�� �޾��� ������ �ż��ϰ� ��ó�Ͽ��� �Ѵ�. �ٸ�, ��ó\n      �� ����� ��쿡�� �켱 ���Ḧ ���� �� �ֵ��� ��ġ�Ͽ��� �Ѵ�.\n�� '��'  �� ���� �̿뵵�� ����Ͽ��� ���'��' �� ���'��' ���� �뺸�Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+181,'��12��(�������� ��ȣ�ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+184, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  �� ������ ���������� ���� �� �Ǹ��� �ִ�.\n�� '��' �� '��'  �� ���������� ��������� ���� ��ȣ�Ͽ��� �Ѵ�.\n�� '��' �� ����缭�� ������ �ʿ��� '��'  �� ���� ���� �ڷḦ �����ϰ� Ȱ���ϸ� ��\n      �ڷḦ ��������纸�� ���ü ��� ��������� ���� ������ �� �ִ�.\n�� '��' �� ������������ �� Ȱ���� �ϰ��� �ϴ� ��쿡�� '��'  ���� ���� ��5ȣ������ ����\n      �������� �� Ȱ�� ���Ǽ��� �޾ƾ� �Ѵ�.\n�� '��' �� '��'  �� ���Ȱ�� �����ϰ�, ������ �˰� �� ���������� ö���� ����� �����Ѵ�. ");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+234,'��13��(��� �� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+237, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"'��' �� '��'  �� ��Ȱ�� ����缭�񽺿� ���� ��� ������ ���� �����Ͽ� ��Ȯ�� ����ϰ�, '��'  (�Ǵ� '��' )�� �䱸�� ��쿡�� ǥ�ؾ�Ŀ� �ǰ��� ����� �����Ͽ��� �Ѵ�.");
	
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��14��(���å��)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� ���� ��ȣ�� ��쿡��'��'  (�Ǵ�'��' )���� ����ǹ��� ������ ���å���� ��\n      ������� ������.\n 1. �������(�Ǵ� '��' )�� ���ǳ� ���Ƿ� ���Ͽ� '��'  �� �λ��� �ϴ� �� �ǰ��� ����\n     �� �ϰų� ����� �̸��� �Ͽ��� ��
	2. �������(�Ǵ� '��' )�� �д�(���κ����� ��1����2 ��4ȣ�� �����д� �� ���� ��\n     ��39����9�� ���������� ���Ѵ�)�� ���Ͽ� '��'  �� �ǰ��� ���ϰ� �ϰų�, ����� �̸���\n     �Ͽ��� ��\n�� ���� �� ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� '��' ���� ����� �䱸�� �� ����.
	1. �ڿ��� �Ǵ� ��ȯ�� ���Ͽ� ��� �Ͽ��� ��
	2.'��' �� ������ �����ǹ��� ���������� ���Ƿ� �����Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��
	3. õ���������� ���Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��
	4. '��'  �� ���� �Ǵ� �߰��Ƿ� ���Ͽ� ���ظ� ���߰ų� ����Ͽ��� ��");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+89,'��15��(��Ÿ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+92, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �� ��༭���� �������� ���� ������ �ι��̳� ��ȸ��Կ� ������.\n�� �ε����� �������� �Ҽ��� ����� ��� '��'  (�Ǵ� '��' ) �Ǵ� �ü��� ���� ����������\n    ���ҹ������� �Ѵ�.");
	
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+121, 'font_size'=>15, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  ���� ���� ����� ü���ϰ� �� ���ü���� �����ϱ� ���Ͽ� �ֹ��� ��༭�� �ۼ� ���� �� ���� 1�ξ� ����Ű�� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+45,$pdf->GetY()+151);
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


		
	

	set_array_text($pdf, $pos);
	unset($pos);
	
	/*
	if($ct['other_text1'] != ''){
		$pdf->MY_ADDPAGE();

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 5);
		$pdf->Text($pdf->left,$pdf->getY()+15,'#��÷1');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+22, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes($ct['other_text1']));
		set_array_text($pdf, $pos);
		unset($pos);
	}
	*/
	
	$pdf->MY_ADDPAGE();
	

	$from_dt = $from_dt != '' ? $from_dt : date('Y-m-d');
	$to_dt   = $to_dt != '' ? $to_dt : date('Y-m-d');
	
	$agreeDt = $ct['reg_dt'] != '' ? $ct['reg_dt'] : '';
	$ct['reg_dt'] = $ct['reg_dt'] != '' ? $ct['reg_dt'] : date('Y-m-d');
	
	$sql = 'select m11_mcode2 as mcode
			,	   m11_suga_value as suga_val
			,	   m11_sdate as from_dt   
			  from m11suga
			 where m11_mcode = \'goodeos\'
			   and m11_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m11_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'
			 union all
			select m01_mcode2 as mcode
			,	   m01_suga_value as suga_val
			,	   m01_sdate as from_dt     
			  from m01suga
			 where m01_mcode = \'goodeos\'
			   and m01_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m01_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'';
	
	$conn -> query($sql); 
	$conn -> fetch();
	$rowCount = $conn -> row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn -> select_row($i);

		$arr[$row['mcode']]['val'] = $row['suga_val'];
		$arr[$row['mcode']]['date'] = $row['from_dt'];
		
	}
	

	$sql = 'select m91_sdate, m91_code, m91_kupyeo
			  from m91maxkupyeo
			 where m91_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m91_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'';
			
	$conn -> query($sql); 
	$conn -> fetch();
	$rowCount = $conn -> row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn -> select_row($i);

		$maxPay[$row['m91_code']]['m91_kupyeo'] = $row['m91_kupyeo'];
		$maxPay[$row['m91_code']]['date'] = $row['m91_sdate'];
		
	}


	$pdf->SetXY($pdf->left+5, $pdf->top+9);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[��ǥ 1]',0,1,'L');


	$pdf->SetFont($pdf->font_name_kor, "B", 15);
	$pdf->SetXY($pdf->left, $pdf->top+25);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "�湮��ȣ �޿���� �� ���κδ� ����(".$myF->dateStyle($maxPay[$row['m91_code']]['date'],'.').")", 0, 1, "C");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "�� �湮��ȣ�� 1ȸ�� �̿�ð��� �޿����");
	
	$rowH = $pdf->row_height+3;
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "�ݾ�(��)", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "30�� �̸�", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS1']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "30�� �̻� ~ 60�� �̸�", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS2']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "60�� �̻�", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS3']['val']), 1, 1, "C");


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "�� ��޺� �簡�޿� �� �ѵ���(��)");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.20, $rowH, "1���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "2���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "3���", 1, 0, "C", true);
	$pdf->Cell($pdf->width*0.20, $rowH, "4���", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "5���", 1, 1, "C", true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['1']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['2']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['3']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['4']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['5']['m91_kupyeo']), 1, 1, "C");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size -1);
	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->MultiCell($pdf->width, $pdf->row_height, "�� ���޿������ �ų� ���������ȸ(������ : ���Ǻ����� ����)�� ����, ����ϴ� �����޿���� �\n     ���� ���(���Ǻ����� ���)�� ����");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "�� ������ �ڰݺ� �޿���� �����Ϻκδ� ����");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);


	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "����", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "�簡�޿�", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "�Ϲ�", 1, 0, "L");
	$pdf->Cell($pdf->width*0.35, $rowH, "15%", 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "���ʼ��ޱ���", 1, 0, "L");
	$pdf->Cell($pdf->width*0.35, $rowH, "0%", 1, 1, "C");

	$orgY = $pdf->GetY();
	
	if($maxPay[$row['m91_code']]['date'] >= '20180101' && $maxPay[$row['m91_code']]['date'] < '20190101'){
		$pdf->SetXY($pdf->left+$pdf->width*0.65, $orgY+6);
		$pdf->MultiCell($pdf->width*0.35, $pdf->row_height, "7.5%(2018.07.31����)\n6%, 9%(2018.08.01����)",'','C');

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "", 1, 1, "C");
	}else if($maxPay[$row['m91_code']]['date'] >= '20190101'){

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "6%, 9%", 1, 1, "C");
	}else {

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "7.5%", 1, 1, "C");
	}
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()-27);
	$pdf->MultiCell($pdf->width*0.65, $pdf->row_height, "��Ÿ �Ƿ���ޱ���\n������ �Ƿ�޿� �ǰ����� �ڰ���ȯ�� (��ͳ�ġ��, ������ȯ��)\n ���ҵ��� (�����Ϻκδ�� ������ ���� �ҵ�.��� ���� �����ݾ� ������ �ڿ� ���� ��� �ش���)");
	

	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+200, '20', '20');	//�� ����
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
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $juso[0] , 0, 0, "L");
	
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
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
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
	
	
	

	/*

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��1��(����)\n  ����ڴ� �̿��ڿ� ���� ��纸�� ������ ������ ���� �̿��ڰ� ������ �� �� ���ÿ� ���Ͽ� �� ������ �ɷ¿� ���� �ڸ��� �ϻ��Ȱ�� ������ ���� �ֵ��� �̿����� ��� ��Ȱ�� �����Ͽ� �ɽ��� ��� ���� ȸ���� ��ǥ�� �ϴ� ���� �������� �湮 ��ȣ ���񽺸� �����ϸ� �̿��ڴ� ����ڿ� ���� �� ���񽺿� ���� ����� �����մϴ�.
	
	��2�� (���Ⱓ)
	  1. �� ����� ���Ⱓ��  ".$from_year." ��  ".$from_month." �� ".$from_day." �Ϻ��� �̿����� ��� ��ȣ ������ ��ȿ          �Ⱓ �����ϱ����� �մϴ�.
	  2. ��� ������ 2�������� �̿��ڷκ��� ����ڿ� ���ؼ�, ������ ���� ��� ������ ��û��          ���� ��� ����� �ڵ� ���ŵǴ� ������ �մϴ�.
	  
	��3�� (�湮��ȣ ��ȹ�� �ۼ�������)
	  1. ����ڴ� �̿��ڿ� ���õǴ� ǥ������̿��ȹ�� �ۼ��Ǿ� �ִ� ��쿡�� �ű⿡ ����           �̿����� �湮 ��ȣ ��ȹ�� �ۼ��ϴ� ������ �մϴ�.
	  2. ����ڴ� ��ġ���� ���� �̿����� �ϻ��Ȱ ������ ��Ȳ �� ����� �ٰŷ� �Ͽ� ���湮           ��ȣ ��ȹ���� �ۼ��մϴ�. ����ڴ� �̡��湮 ��ȣ ��ȹ���� ������ �̿��� �� �� ����         ���� �����Ͽ� �� ���Ǹ� ��� ������ �մϴ�.
	  3. ����ڴ� ������ ��� ���ΰ��� �ش��ϴ� ��쿡�� ��1���� ���� �ϴ� �湮 ��ȣ ����         �� ������ ���� �湮 ��ȣ ��ȹ�� ������ �ǽ��մϴ�.
	   ���̿����� �ɽ��� ��Ȳ, �� ó���� �ִ� ȯ�� ���� ��ȭ�� ���� �ش� �湮 ��ȣ ��ȹ�� ��          ���� �ʿ䰡 �ִ� ���.
	   ���̿��ڰ� �湮 ��ȣ ������ �����̳� ���� ��� ���� ������ ����ϴ� ���.
	  4. ����ڴ� �湮 ��ȣ ��ȹ�� �������� ��쿡�� �̿��ڿ� ���ؼ� �������� �����Ͽ� �� ��        ���� Ȯ���ϴ� ������ �մϴ�.

   ��4�� (��ġ�ǿ��� ����)
	  1. ����ڴ� �湮 ��ȣ ������ ������ �����Ϸ��� ��ġ���� ���ø� ������ �޽��ϴ�.
	  2. ����ڴ� ��ġ�ǿ� �湮 ��ȣ ��ȹ�� �� �湮 ��ȣ ������ �����Ͽ� ��ġ�ǿ��� ������         ���޸� ���մϴ�.

	��5�� (�湮��ȣ ������ ����)
	  1. �̿��ڰ� ������ �޴� �湮 ��ȣ ������ ���������湮��ȣ ���ü����� ���ߴ� �ٿ� �����ϴ�. ����ڴ�,���湮��ȣ ���ü����� ���� ���뿡 ���Ͽ� �̿��� �� �� �������� �����մϴ�.

	��6�� (���� ������ ���)
  1. ����ڴ� �湮 ��ȣ ������ �ǽø��� ���� ���� ���� ���񽺽ǽ� ��Ϻο� �����ϰ�        ������ ����ÿ� �̿����� Ȯ���� �޴°����� �մϴ�. �� ����� �̿����� ����� ����       �� ������ �̿�  �ڿ��� �����մϴ�.
  2. ����ڴ� ���� �ǽ� ��Ϻθ� �ۼ��ϴ� ������ �ϸ�, �� ����� ���� �� 2�Ⱓ ������        �ϴ�.
  3. �̿��ڴ� ������� ���� �ð����� �� ����ҿ��� �ش� �̿��ڿ� ���� ��2���� ���� ��       �� ��Ϻθ� ������ �� �ֽ��ϴ�.
  4. �̿��ڴ� ����� ������ ������ �ش� �̿��ڿ� ���� ��2���� ���� �ǽ� ��Ϻ��� ��       �繰������ ���� ���� �ֽ��ϴ�.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��7�� (�湮��ȣ���� ��ü ��)
  1. �̿��ڴ� ���ӵ� �湮 ��ȣ���� ��ü�� ����ϴ� ��쿡�� �ش�  �湮 ��ȣ�簡 ������         �������̶�� �����Ǵ� ���� �� �� ��ü�� ����ϴ� ������ �и��� �Ͽ� ����ڿ� ���ؼ�        �湮 ��ȣ���� ��  ü�� ��û�� ���� �ֽ��ϴ�.
  2. ����ڴ� �湮 ��ȣ���� ��ü�� ���� �̿��� �� �� ���� � ���ؼ� ���� �̿���� ��        ������ ������ �ʰ� ����� ����ϴ� ������ �մϴ�.
  3. �湮 ��ȣ�簡 ����� �ҷ� ���� ���� �湮 �� �� ���� �Ǿ��� ������ ��ü �ο��� �μ�       �ϰ� �μ� �� ���� �̿��� �� �� �������� �����ϰڽ��ϴ�.

��8�� (���)
  1. �̿��ڴ� ������ �밡�� ����������纸�衽�� ���ϴ� �̿� ���������� ����� �⺻         ���� ���� �ſ��� �հ� �ݾ��� �����մϴ�.
  2. ����ڴ� ��� ����� �հ���� û������ ���� �����Ͽ� ���� �� 10�ϱ��� �̿��ڿ���       �ۺ��մϴ�.
  3. �̿��ڴ� ��� ����� �հ���� ������ 15�ϱ��� ������� �����ϴ� ������� �����մ�        ��.
  4. �̿��ڴ� ���ÿ� ���� ���� �������� ���� �ǽø� ���ؼ� ����ϴ� ����, ����, ����,       ��ȭ�� ����� �δ��մϴ�.

��9�� (������ ����)
  1. �̿��ڴ� ����ڿ� ���ؼ� ���� �ǽ����� �������� ���� 6�ñ��� ������ �ϴ� ������        �� ����� �δ��ϴ� �� ���� ���� �̿��� ������ ���� �ֽ��ϴ�.
  
��10�� (����� ����)
  1. ����ڴ� �̿��ڿ� ���ؼ� 1���������� ������ �����ϴ� �����ν� �̿� ���������� ��         ���� ����(���� �Ǵ� ����)�� ��û�� ���� �ֽ��ϴ�.

��11�� (����� ����)
	 1. �̿��ڴ� ����ڿ� ���ؼ� 1�ְ��� ���� �Ⱓ�� �ξ� ������ ������ �ϴ� �����ν�, ��\n     ����� �ؾ��� ���� �ֽ��ϴ�. 
     �ٸ�, �̿����� ����, ���۽����� �Կ� �� ��¿ �� ���� ������ �ִ� ���� ���� �Ⱓ��\n     1�ְ� �̳��� ���������� �� ����� �ؾ��� ���� �ֽ��ϴ�.
  2. ����ڴ� �ε����� ������ �ִ� ��� �̿��ڿ� ���ؼ� 1�������� ���� �Ⱓ�� �ξ� ����       �� ��Ÿ�� ������ �����ϴ� �����ν� �� ����� �ؾ��� ���� �ֽ��ϴ�.
  3. ������ �� 1���� ������ �ش����� ���� �̿��ڴ� ������ �����ϴ� �����ν� ��� ��\n     ����� �ؾ��� ���� �ֽ��ϴ�.
    �����ڰ� ������ ���� ���� ���񽺸� �������� �ʴ� ���.
    �����ڰ� ����� ��ų �ǹ��� ������ ���
    �����ڰ� �̿��ڳ� �� ���� ��� ���� ��ȸ ����� ��Ż�ϴ� ������ �ǽ����� ���
    �����ڰ� �Ļ����� ���
  4. ������ ��1���� ������ �ش����� ���� ����ڴ� ������ �����ϴ� �����ν� ��� �� ��       ���� �ؾ��� ���� �ֽ��ϴ�.
    ���̿����� ���� �̿����� ������ 3���� �̻� ���� ��, ����� �����ϵ��� �ְ� ����         ���� �ұ��ϰ� 10�� �̳��� ���ҵ��� �ʴ� ���
    ���̿��� �Ǵ� �� ������ ����ڳ� ���� �������� ���ؼ�, �� ����� ��� �ϱ� �����          ��ŭ�� ��������� �ǽ����� ���.");

	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"     
  5. ������ ��1���� ������ �ش����� ���� �� ����� �ڵ������� �����մϴ�.
    ���̿��ڰ� ��纸�� �ü��� �Լ� ���� ���
    ���̿����� ��� ��ȣ ���� ������ ��޿ܶ�� �����Ǿ��� ���.
    ���̿��ڰ� ������� ���.
	
��12�� (��� ���� ����)
  1. ����� �� ������� ����ϴ� ����� ���� ������ �ϴµ� �־ �ľ��� �̿��� �� ��        ������ ���� ����� ������ ���� ���� �����ڿ��� �긮�� �ʽ��ϴ�. �� ����� ��ų �ǹ�        �� ��� ���� �ĵ�  �����ϴ�.
  2. ����ڴ� �̿��� �� �� ������ ������ ������ �ذ��ؾ� �� ���� � ���� ���� �����        ȸ�ǿ� ���� ������ �����ϱ� ���ؼ� �̿��� �� ������ ���������� ���� ����� ȸ�ǿ�      �� �̿��ϴ� ���� �� ����� ������ ���Ƿ� �����մϴ�.

��13�� (���å��)
  ����ڴ� ������ ������ ���� ������� ������ ������ ���� �̿����� ������ü����꿡 ���ظ� ������ ���� �̿��ڿ� ���ؼ� �� ���ظ� ����մϴ�.

��14�� (��޽��� ����)
  ����ڴ� ������ �湮��ȣ�� ������ �ǽ��ϰ� ���� �� �̿����� ������ �޺��� ������ ��� �� �� �ʿ��� ���� �ż��ϰ� ��ġ�� �ǻ� �Ǵ� ġ�� �ǻ翡�� ������ �ϴ� �� �ʿ��� ��ġ�� �����մϴ�.

��15�� (�ź��� �޴� �ǹ�)
  ���� �������� �׻� �ź����� �޴� �� ù ȸ �湮�� �� �̿��� �Ǵ� �̿����� �������κ��� ���ð� �䱸�Ǿ��� ���� ������ �ź����� �����մϴ�.
��16�� (����)
  ����ڴ� �湮��ȣ�� ������ ������������� �� �����Ƿ� ���� �Ǵ� �������񽺸� �����ϴ� ������� ������ ���޿� ��� �մϴ�.

��17�� (��㡤���� ����)
  ����ڴ� �̿��ڷκ����� ���, ���� � �����ϴ� â���� ��ġ�� �湮 ��ȣ�� ���� �̿����� ���, ���� ���� �ż��� �����մϴ�.

��18�� (�� ��࿡ ������ ���� ����)
  1. �̿��� �� ����ڴ� ���Ǽ����� ������ �� ����� �����ϴ� ������ �մϴ�.
  2. �� ��࿡ ������ ���� ���׿� ���ؼ��� ��纸�� ���� �� �� �� ������ ���ϴ� ���� ������ �ֹ��� ���Ǹ� ���� ���� �� ���մϴ�.

��19�� (���ǰ���)
  �� ��࿡ ���ؼ� ��¿�� ���� �Ҽ��� �Ǵ� ��쿡 �̿��ڿ� ����ڴ� ������� �ּ����� �����ϴ� ���ǼҸ� ������ �������Ǽҷ� �ϴ� �Ϳ� �̸� �����մϴ�.

  ����� ����� �����ϱ� ���Ͽ� ���� 2���� �ۼ��ϰ� �̿��� �� ����ڰ� ���� ������ ��  ���� 1�뾿 �����ϴ� ������ �մϴ�.");
	
	$pdf->MY_ADDPAGE();

	$pdf->SetXY($pdf->width*0.38, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, '���ü����' , 0, 0, "R");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[�̿���]", 0, 1, "R");
		
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 80, $pdf->getY(), '20', '20');	//�� ����
	}

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� ��:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(��)", 0, 1, "L");

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"���� ������
      ���� ������ ��� �ǻ縦 Ȯ���� ���� �����Ͽ����ϴ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"�̿��ڿ��� ����      
*���ǣ���Ģ���μ� �ξ��ڷ� �մϴ�.");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[�����]", 0, 1, "R");
	
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�����:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, '  '.$center['centerName'], 0, 1, "L");
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 77, 110, '20', '20');	//��� ����
		}
	}

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� ǥ:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$center['manager'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(��)", 0, 1, "L");
	
	
	# �̿��༭
	
	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+200, '20', '20');	//�� ����
	}

	$pdf->SetXY($pdf->left+5, $pdf->top+9);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[���� ��5ȣ����]',0,1,'L');

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+15, $pdf->width-10, $pdf->height-45);
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
	
	$pdf->SetX($pdf->left+5);
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
	*/


	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
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
					,      bath_weekly
					,      from_time
					,      to_time
					,	   use_type
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text2
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
					,      bath_weekly
					,      from_time
					,      to_time
					,	   use_type
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text2
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
	
	if($ct['seq'] == 0 && $code == '34311000305'){
		//�ູ�簡
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
	for($i=0; $i<7; $i++){
		if($ct['bath_weekly'][$i] == 'Y' and ($i == 0)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 1)){
			$yoil .= 'ȭ';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 2)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 3)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 4)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 5)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 6)){
			$yoil .= '��';
		}

	}
	
	//����
	$jikin = $center['jikin'];

	

	$yoil1 = substr($yoil, 0, 2);
	$yoil2 = substr($yoil, 2, 2);
	
	$yoil1 = $yoil1 != '' ? $yoil1 : '    ';
	$yoil2 = $yoil2 != '' ? $yoil2 : '    ';

	$from_time = $ct['from_time'] != '' ?  $myF->timeStyle($ct['from_time']) : '         ';	//�̿���۽ð�
	$to_time = $ct['to_time'] != '' ?  $myF->timeStyle($ct['to_time']) : '         ';		//�̿�����ð�

	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '��';	//�̿볳����1
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';		//�̿볳����2
	$pay_day3  = $ct['pay_day3'] != '' ? $ct['pay_day3'] : '15';	//���κδ�ݳ�����

	$pdf->MY_ADDPAGE();
	
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg';

	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 93, $pdf->getY()+115, '20', '20');	//�� ����
	}


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "�����޿� �̿� ǥ�ؾ��\n(�湮���)", 1,"C");
	
	
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
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'C', 'text'=>$center['centerName']);
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
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'C', 'text'=>$center['centerName']);
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
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " (��)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $su['gwange'], 1, 1, "C");
	
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
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+12, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "����̳� ���μ����� ������ ���Ͽ� ȥ�ڼ� �ϻ��Ȱ�� �����ϱ� ����� ���ε� �� ��������� ���� �е鿡�� �湮���޿��� �����Ͽ� ������ �ǰ����� �� ��Ȱ������ �����ϰ� �� ������ �δ��� ���������ν� ���� ���� ����Ű���� �Ѵ�. ")));

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+38,'��2��(���Ⱓ)');
	
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+40, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� ���Ⱓ�� ".$from_year."�� ".$from_month."�� ".$from_day."�Ϻ��� ".$to_year."�� ".$to_month."�� ".$to_day."�ϱ����� �Ѵ�.\n�� ��1���� ���Ⱓ�� ����� ���� ���ǿ� ���� ������ �� �ִ�.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+61,"��3��(�޿�����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+64, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "�湮���޿��� ��������� '��'  �� ���� ���� �湮�Ͽ� ��üȰ�� �� ����Ȱ�� ���� �����ϴ� �����޿��� �Ѵ�.")));
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+85,"��4��(�޿��̿� �� ����)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+88, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �湮���޿� �̿� �� ������ �����޿� �̿�(����)��ȹ���� ���Ѵ�.\n�� '��'�� �湮���޿� �̿�ð��� �Ʒ��� ���� �Ѵ�. �ٸ�, �湮���޿��� �� 1ȸ���� ��\n     ���� �����ϸ� ���Ǳ� �� ��Ǳ� ������ ���Ͽ� �Ǻ��� �ǰ����������� �Ұ����� ��쿡��\n    �ʰ��Ͽ� �̿��� �� �ִ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left, $pdf->getY()+120);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "�̿���", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿�ð�", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "�̿���/��", 1, 1, "C");
	
	if ($ct['use_type'] == '1'){
		//�̿���(����)
		$pos_x = 134;
		$pos_y = 138;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '2'){
		//�̿���(����)
		$pos_x = 147;
		$pos_y = 138;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '3'){
		//�̿���(�ſ�)
		$pos_x = 160;
		$pos_y = 138;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '4'){
		//�̿���(��3ȸ)
		$pos_x = 172.5;
		$pos_y = 138;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "(".$yoil1.", ".$yoil2.") ����", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, $from_time." ~ ".$to_time, 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "����� ����� ��ſ� ���3ȸ", 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+6, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� '��' ���� �湮���޿��� ������ ���� �ݵ�� 2���� ��纸ȣ�縦 ��ġ�Ѵ�.\n�� '��' �� �Ϳ� �����޿� ������ �ϰ����ϴ� ��쿡�� '��' (�Ǵ� '��')�� �����Ͽ�  �޿�\n     �޿����� ������ �޿���ȹ���� �ۼ��ϰ� ������(��ȣ��)Ȯ�ι޾� �޿����񽺸� �ǽ��Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+40,'��5��(����� �ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+43, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �� �̿�� ����\n   2. �湮���޿� ������ �޿��̿�\n   3. �����޿� �̿��Ģ �ؼ�\n4. ��Ÿ '��' �� ������ ��Ģ ����\n�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. �湮���޿� ���� ��೻�� �ؼ�\n   2. �޿����� �� '��'  ���� �ź� �̻��� ����� ��� ��� '��' ���� �뺸\n   3. �޿������ð��� '��'  �� �ֺ� �� ������� û�� �� ��������\n   4. �޿����� �� �˰� �� '��'  �� �Ż� �� ��ȯ ���� ���� �������\n  (��, ġ�� ���� ��ġ�� �ʿ��� ���� ����)\n   5. �̿���, ������ȸ �ٸ� ���� �̿� ��������\n   6. �����д� ���� �� �����α� ��ȣ �ؼ�\n   7. ��Ÿ '��'  (�Ǵ� '��' )�� ��û�� ����");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� ���� �� ȣ�� �����ϰ� �����Ͽ��� �Ѵ�.\n   1. '��'  �� ���� �ǰ� �� �ʿ��� �ڷ�����\n   2. '��'  �� �� �̿�� �� ��� �δ�\n   3. ���� ���� �� ����纸�� ��� ���� �� ��� '��' ���� �뺸\n   4. '��'  �� ���� �ǹ������� ������ �븮�� ���� �� '��' ���� �뺸\n   5. ��Ÿ '��' �� ������û ����");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+50,'��6��(������� ���)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+53, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ� '��' )�� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��' �� �����Ͽ� ����� ���� ��\n     �� �ִ�.
	1. ��2���� ���Ⱓ�� ����� ���
	2. ��3���� �湮���޿� ������ �ش��ϴ� ���񽺸� �������� �ƴ��� ���
	3. ��4����2���� �湮���޿� �����ð���'��'  (�Ǵ�'��' )�� ���� ���� '��' �� ����\n     �� �����ϰų� ��ġ�� ��������� ���Ƿ� ���� ���� ���
	4. ��Ÿ '��'  �� ������� ������ �߻��� ���\n�� '��' �� ���� ��ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� �����Ͽ� ����� ���� ��\n     �� �ִ�.
	1. ��2���� ���Ⱓ�� ����ǰų� ����� ���
	2. '��'  �� ����纸�� ��޿��ڷ� ��޺����� �߻��� ���
	3. '��'  �� �ǰ����� ������������ǿ���װ��������ѹ������� ���� ������ ȯ�ڷμ� ��\n     ���� ���輺�� �ִ� ���� ������ ��
	4. '��'  �� �ǰ����� ������ ���� �̿��� ����� ��
	5. �̿���� ���õ� �̿�ȳ��� '��'  �� ������ ���� ���� ������ �ʴ� �� ���� ������\n     �ɰ��� ������ �� ��
	6. '��'  �� �� 5ȸ �̻� �������� �湮���޿� �̿�ð��� ��Ҹ� ��Ű�� �ƴ��Ͽ��� ��");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+165,'��7��(����� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+168, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  (�Ǵ�'��' )�� ��6����1���� ������� ����� �߻��� ��쿡�� �ش��� �Ǵ� ����\n    �� ���������� ���� ��2ȣ������ �����޿� ���� ��û���� �����Ͽ��� �Ѵ�. �ٸ�, ��Ÿ\n    �ε����� ��쿡�� �켱 �������� �� �� �ִ�.\n�� '��' �� ��6����2�׿� ���� ������� ����� �߻��� ��쿡�� ������� �ǻ縦 ���� ��2ȣ\n    ������ �����޿� ����ȳ��� �� ���� ���������� �Բ� '��'  �� '��' ���� �뺸�ϰ�\n    ����� �����ؾ� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+212,'��8��(�̿�� ����)');
	
	if($code == '32817000163' || //õ��湮
	   $code == '34812000297' ){ //�Ƹ�����
		$str = '��������纸��� �����Ģ[���� ��24ȣ����]�� �����޿������� �Ǵ� ��������纸��� �����Ģ[���� ��3ȣ����]�� �����޿� �̿�� ���γ�����';
		$str2 = '������4ȣ������ �����޿� ����Ȯ�μ� �Ǵ� �����޿����(���κδ��)��������';
	}else {
		$str = '��������纸��� �����Ģ[���� ��3ȣ����]�� �����޿� �̿�� ���γ�����';
		$str2 = '������4ȣ������ �����޿� ����Ȯ�μ���';
	}

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+215, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �湮���޿���� �� ���κδ� ������ ��ǥ 1�� ����.\n�� '��' �� ���� 1�Ϻ��� ���ϱ����� �̿�Ḧ �ſ� ".$pay_day1."�Ͽ� �����ϰ� '��'  (�Ǵ� '��' )���� ".$pay_day2."�ϱ��� ".$str."�� �뺸�Ѵ�.\n�� '��'  �� �ſ� ".$pay_day3."�ϱ��� ".$bank." ���κδ���� ���� �Ѵ�. �ٸ�, �������� �������� ��쿡�� �� ���Ϸ� �Ѵ�.\n�� '��' �� '��'  �� ������ ��뿡 ���ؼ��� ".$str2." �߱��Ѵ�.");
	
	/*
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+256,'��9��(����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+258, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ��ȣ�� �ش��ϴ� ��쿡�� �̸� �ݿ��� ��༭�� ���ۼ��Ѵ�.");
	*/

	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'��9��(����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.18, 'y'=>$pdf->GetY()+1, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ��ȣ�� �ش��ϴ� ��쿡�� �̸� �ݿ��� ��༭�� ���ۼ��Ѵ�.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"   1. ��2���� ���Ⱓ�� ����� ���
   2. ����� ��������� ����� ���
   3. �湮��� �޿���� �� ���κδ� ����� ����� ���
   4. ��Ÿ '��'  �� '��' �� �ʿ��� ���");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+40,'��10��(�ǰ�����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+43, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"��'��' ��'��'  �� �ǰ� �� ������ ������ ���Ͽ� �����ڵ鿡�� �� 1ȸ �̻� �ǰ������� �ǽ�\n      �Ͽ��� �Ѵ�.\n��'��' �� ��������� �湮���޿� �������� '��'  ���� ���ظ� ������ ��� ������ ��\n      ġ�� ���ؾ� �Ѵ�.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+75,'��11��(���� �� ��ġ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+78, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"��'��' �� �湮���޿� �����ð��� '��'  �� ������ ������ ���¶�� �Ǵܵ� ������ '��'  (\n      �Ǵ� '��' )�� ������ ���� �Ǵ� ���� �Ƿ������� ��� �ļ��ϰ� '��' ���� ��� �뺸\n      �Ͽ��� �Ѵ�.\n��'��' �� ��1���� ������ ���� �뺸�� �޾��� ������ �ż��ϰ� ��ó�Ͽ��� �Ѵ�. �ٸ�, ��ó\n      �� ����� ��쿡�� �켱 ���Ḧ ���� �� �ֵ��� ��ġ�Ͽ��� �Ѵ�.\n��'��'  �� ���� �̿뵵�� ����Ͽ��� ���'��' �� ���'��' ���� �뺸�Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+122,'��12��(�������� ��ȣ�ǹ�)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+125, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��'  �� ������ ���������� ���� �� �Ǹ��� �ִ�.\n�� '��' �� '��'  �� ���������� ��������� ���� ��ȣ�Ͽ��� �Ѵ�.\n�� '��' �� ����缭�� ������ �ʿ��� '��'  �� ���� ���� �ڷḦ �����ϰ� Ȱ���ϸ� ��\n      �ڷḦ ��������纸�� ���ü ��� ��������� ���� ������ �� �ִ�.\n�� '��' �� ������������ �� Ȱ���� �ϰ��� �ϴ� ��쿡�� '��'  ���� ���� ��5ȣ������ ����\n      �������� �� Ȱ�� ���Ǽ��� �޾ƾ� �Ѵ�.\n�� '��' �� '��'  �� ���Ȱ�� �����ϰ�, ������ �˰� �� ���������� ö���� ����� �����Ѵ�. ");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+175,'��13��(��� �� ����)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+178, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"'��' �� '��'  �� ��Ȱ�� ����缭�񽺿� ���� ��� ������ ���� �����Ͽ� ��Ȯ�� ����ϰ�, '��'  (�Ǵ� '��' )�� �䱸�� ��쿡�� ǥ�ؾ�Ŀ� �ǰ��� ����� �����Ͽ��� �Ѵ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+200,'��14��(���å��)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+203, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� '��' �� ���� ��ȣ�� ��쿡��'��'  (�Ǵ�'��' )���� ����ǹ��� ������ ���å���� ��\n      ������� ������.\n  1. �������(�Ǵ� '��' )�� ���ǳ� ���Ƿ� ���Ͽ� '��'  �� �λ��� �ϴ� �� �ǰ��� ����\n     �� �ϰų� ����� �̸��� �Ͽ��� ��
	2. �������(�Ǵ� '��' )�� �д�(���κ����� ��1����2 ��4ȣ�� �����д� �� ���� ��\n     ��39����9�� ���������� ���Ѵ�)�� ���Ͽ� '��'  �� �ǰ��� ���ϰ� �ϰų�, ����� �̸���\n     �Ͽ��� ��\n�� ���� �� ȣ�� �ش�Ǵ� ��쿡�� '��'  (�Ǵ� '��' )�� '��' ���� ����� �䱸�� �� ����.
	1. �ڿ��� �Ǵ� ��ȯ�� ���Ͽ� ��� �Ͽ��� ��
	2.'��' �� ������ �����ǹ��� ���������� ���Ƿ� �����Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"
	3. õ���������� ���Ͽ� ���ظ� ���߰ų� ��� �Ͽ��� ��
	4. '��'  �� ���� �Ǵ� �߰��Ƿ� ���Ͽ� ���ظ� ���߰ų� ����Ͽ��� ��");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+33,'��15��(��Ÿ)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+36, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"�� �� ��༭���� �������� ���� ������ �ι��̳� ��ȸ��Կ� ������.\n�� �ε����� �������� �Ҽ��� ����� ��� '��'  (�Ǵ� '��' ) �Ǵ� �ü��� ���� ����������\n    ���ҹ������� �Ѵ�.");
	
	if($ct['other_text2'] != ''){
		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
		$pdf->Text($pdf->left,$pdf->getY()+65,'��17��(��÷����)');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+69, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"���� ������� ���� Ư�̻����� #��÷1 �� ���� �Ǿ� �ִ�.");
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+85, 'font_size'=>15, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  ���� ���� ����� ü���ϰ� �� ���ü���� �����ϱ� ���Ͽ� �ֹ��� ��༭�� �ۼ� ���� �� ���� 1�ξ� ����Ű�� �Ѵ�.");
	
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

	if($ct['other_text2'] != ''){
		$pdf->MY_ADDPAGE();

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 5);
		$pdf->Text($pdf->left,$pdf->getY()+15,'#��÷1');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+22, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes($ct['other_text2']));
		set_array_text($pdf, $pos);
		unset($pos);
	}
	
	

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
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "�湮��� �޿���� �� ���κδ� ����(".$myF->dateStyle($maxPay[$row['m91_code']]['date'],'.').")", 0, 1, "C");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "�� �湮����� 1ȸ�� �̿�ð��� �޿����");
	
	$rowH = $pdf->row_height+3;
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "�� ��", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "�ݾ�(��)", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "�湮��� ������ �̿��� ���(������ ���)", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBKD1']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "�湮��� ������ �̿��� ���(������ ���)", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBKD2']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "�湮��� ������ �̿����� �ƴ��� ���", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBFD1']['val']), 1, 1, "C");


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


	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
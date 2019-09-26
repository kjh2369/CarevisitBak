<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_function.php');
	require_once('../pdf/korean.php');
	require_once('../pdf/pdf_client_table_test.php');

	$conn->set_name('euckr');

	$code = $_GET['code'];
	$ssn = $ed->de($_GET['ssn']);
	$dt = $_GET['dt'];
	$seq = $_GET['seq'];
	$gbn = $_GET['gbn'];

	$kind = $conn->_client_kind();

	$sql = "select client_dt
			,      client_seq
			,      client_nm
			,	   client_ssn
			,	   client_counsel as level_gbn
			,      case client_counsel when '0' then '�簡���'
									   when '1' then '���簣��'
									   when '2' then '���ε���'
									   when '3' then '���Ż���'
									   when '4' then '�����Ȱ������' else '��Ÿ' end as counsel_gbn
			,      client_phone
			,      client_mobile
			,      client_postno
			,      client_addr
			,      client_addr_dtl
			,      client_protect_nm
			,      client_protect_rel
			,      client_protect_tel
			,      client_family_gbn
			,      client_family_other
			,	   client_text_1
			,	   client_text_2
			,	   client_text_3
			,	   m00_cname
			  from counsel_client
		inner join m00center
		        on m00_mcode = org_no
			 where org_no   = '$code'
			   and client_dt  = '$dt'
			   and client_seq = '$seq'
			   and client_counsel = '$gbn'
			   and del_flag = 'N'";

	$client = $conn->get_array($sql);
	
	

	//��� �簡/���ε���/���簣��/���Ȱ��
	$sql = "select *
			  from counsel_client_normal
			 where org_no     = '$code'
			   and client_dt  = '$dt'
			   and client_seq = '$seq'";

	$normal = $conn->get_array($sql);

	//��� ���Ż���
	$sql = "select *
			  from counsel_client_baby
			 where org_no     = '$code'
			   and client_dt  = '$dt'
			   and client_seq = '$seq'";

	$baby = $conn->get_array($sql);

	//�����϶� ���͸� ��ȸ
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '0'";
	$c_nm = $conn->get_data($sql);
	
	/*
	if($var['fileName'] == 'care_client_find_log'){
		$sql = 'SELECT	*
				FROM	apprline_set
				WHERE	org_no	= \''.$_SESSION['userCenterCode'].'\'
				AND		gbn		= \'01\'';
		$apprline = $conn->get_array($sql);
		
		$sginCnt = $apprline['line_cnt'];
		$sginTxt = Explode('|',$apprline['line_name']);
		$sginPrt = $apprline['prt_yn'];
		
	}else {
	//}
	*/
	
	//������ ����
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'';

	$row = $conn->get_array($sql);

	$sginCnt = $row['line_cnt'];
	$sginTxt = Explode('|',$row['subject']);

	

	$pdf = new MYPDF(strtoupper('P'));
	//$pdf->cpIcon   = '../ci/ci_'.$gDomainNM.'.jpg';
	//$pdf->cpName   = null;
	$pdf->ctIcon   = $conn->center_icon($code);
	$pdf->ctName   = $conn->center_name($code);
	$pdf->font_name_kor = '����';
	$pdf->font_name_eng = 'batang';
	$pdf->AddUHCFont('����','batang');
	$pdf->Open();
	
	$pdf->sginCnt	= $sginCnt;
	$pdf->sginTxt	= $sginTxt;
	$pdf -> code = $_SESSION['userCenterGiho'];										//����ڵ�
	$pdf -> cname = $client['m00_cname'] != '' ? $client['m00_cname'] : $c_nm;		//�����
	$pdf -> client_nm = $client['client_nm'];										//����
	$pdf -> client_ssn = $myF->issStyle($client['client_ssn']);						//�ֹι�ȣ
	$pdf -> counsel_gbn = $client['counsel_gbn'];									//��㱸��
	$pdf -> tel = $myF->phoneStyle($client['client_phone'],'.');					//��ȭ(����)
	$pdf -> mobile = $myF->phoneStyle($client['client_mobile'],'.');				//�޴�����ȣ(����)
	$pdf -> postno = $client['client_postno'];										//�����ȣ
	$pdf -> addr = $client['client_addr'];											//�ּ�
	$pdf -> addr_dtl = $client['client_addr_dtl'];									//���ּ�
	$pdf -> protect_nm = $client['client_protect_nm'];								//��ȣ�ڸ�
	$pdf -> protect_rel = $client['client_protect_rel'];							//��ȣ�ڰ���
	$pdf -> protect_tel = $myF->phoneStyle($client['client_protect_tel'],'.');		//��ȣ�ڿ���ó
	$pdf -> family_gbn = $client['client_family_gbn'];								//��������
	$pdf -> family_other = $client['client_family_other'];							//�������� ��Ÿ
	if($gbn == '3'){
		//���Ż���
		$pdf -> talker_nm = $baby['talker_nm'];										 //�����
		$pdf -> talker_type = $baby['talker_type'];									//�������
		$pdf -> client_dt = str_replace('-','.',$baby['talker_dt']);				//�����
		$pdf -> protect_gbn = $baby['protect_gbn'];									//��ȣ����
	}else {
		//�簡/���ε���/���簣��/���Ȱ��
		$pdf -> talker_nm = $normal['talker_nm'];									 //�����
		$pdf -> talker_type = $normal['talker_type'];								//�������
		$pdf -> client_dt = str_replace('-','.',$normal['talker_dt']);				//�����
		$pdf -> protect_gbn = $normal['protect_gbn'];								//��ȣ����
		$pdf -> protect_other = $normal['protect_other'];							//��ȣ�ڱ��б�Ÿ

		if($gbn == '0'){
			//�簡����ϰ�츸
			$pdf -> level_gbn = $normal['level_gbn'];								//�����
		}

	}
	$pdf -> gbn = $gbn;								//��㱸��

	$pdf->SetFillColor(222,222,222);
	
	
	$pdf->AddPage(strtoupper('p'), 'A4');
	if($client['client_ssn'] != ''){
		if($gbn == 0){
			$pdf->SetXY($pdf->left,$pdf->top+94);
		}else {
			$pdf->SetXY($pdf->left,$pdf->top+87);
		}
	}else {	
		$pdf->SetXY($pdf->left,$pdf->top+82);
	}
	
	$high = 6; 
	$rowHeight = 6;

	$sql = "select count(*)
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '2'
			   and family_ssn = '".$client['client_ssn']."'";
	$family = $conn->get_data($sql);

	$high = $family != 0 ? $family * $rowHeight + $high : '12';
	
	$pdf->SetX(14);
	$pdf->Cell(20,$high,'��������',1,0,'C',true);
	$pdf->Cell(25,$rowHeight,'����',1,0,'C',true);
	$pdf->Cell(25,$rowHeight,'����',1,0,'C',true);
	$pdf->Cell(25,$rowHeight,'����',1,0,'C',true);
	$pdf->Cell(27,$rowHeight,'����',1,0,'C',true);
	$pdf->Cell(20,$rowHeight,'���ſ���',1,0,'C',true);
	$pdf->Cell(40,$rowHeight,'������',1,1,'C',true);
	
	if($gbn == 0){
		$high += 12;
	}else {
		$high += 5;
	}
	
	$sql = "select family_nm
			,	   family_rel
			,	   family_age
			,	   family_job
			,	   case family_with when 'Y' then '��' else '�ƴϿ�' end as fm_with
			,	   family_monthly
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '2'
			   and family_ssn = '".$client['client_ssn']."'";
	
	$conn->query($sql);

	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(20,0,'',0,0,'C');
			$pdf->Cell(25,$rowHeight,$row['family_nm'],1,0,'L');
			$pdf->Cell(25,$rowHeight,$row['family_rel'],1,0,'L');
			if($row['family_age'] != 0){
				$pdf->Cell(25,$rowHeight,$row['family_age'],1,0,'L');
			}else {
				$pdf->Cell(25,$rowHeight,'',1,0,'L');
			}
			$pdf->Cell(27,$rowHeight,$row['family_job'],1,0,'L');
			$pdf->Cell(20,$rowHeight,$row['fm_with'],1,0,'C');
			$pdf->Cell(40,$rowHeight,$row['family_month'],1,1,'L');

		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C');
		$pdf->Cell(25,$rowHeight,'',1,0,'L');
		$pdf->Cell(25,$rowHeight,'',1,0,'L');
		$pdf->Cell(25,$rowHeight,'',1,0,'L');
		$pdf->Cell(27,$rowHeight,'',1,0,'L');
		$pdf->Cell(20,$rowHeight,'',1,0,'L');
		$pdf->Cell(40,$rowHeight,'',1,1,'L');
	}

	if($gbn == '3'){
		$pdf->SetX(14);
		$pdf->Cell(20,36,'�ǰ�����',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�����',1,0,'C', true);
		$pdf->Cell(25,$rowHeight,$baby['health_delivery_dt'],1,0,'L');
		$pdf->Cell(25,$rowHeight,'�������',1,0,'C', true);
		$pdf->Cell(87,$rowHeight,$baby['health_delivery_kind'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��ֵ��',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'�����  ��1���  ��2���  ��3���  ��4���  ��5���  ��6���',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�������',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'��������ȯ  ��ȣ������  ����ü���  ��ð����  ��û�����  �������  ���Ÿ',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��������',1,0,'C', true);
		$pdf->Cell(50,$rowHeight,$baby['health_nurse'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�ɸ�����',1,0,'C', true);
		$pdf->Cell(50,$rowHeight,$baby['health_mind'],1,1,'L');

		$pdf->SetXY(109,$pdf->GetY()-12);
		$pdf->Cell(25,12,'�๰����',1,0,'C', true);
		$pdf->Cell(62,12,'',1,1,'C');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��ü���ǰ�����',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,$baby['health_body'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,18,'',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'������Ȳ',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,$baby['family_status'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�ְŻ�Ȳ',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,$baby['family_abode'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��Ÿ',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,$baby['family_other'],1,1,'L');

		$family_high1 = 152 + $high - $rowHeight;
		$family_high2 = 157 + $high - $rowHeight;

		$pdf->Text(18, $family_high1, '���� ��');
		$pdf->Text(16, $family_high2, 'ȯ���� �屸');

		$height = $pdf->GetY();

		if ($pdf->GetY()+36 > $pdf->height){
			$pdf->AddPage(strtoupper('p'), 'A4');
		}
		$pdf->SetX(14);
		$pdf->Cell(20,36,'�������',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'���',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'���𿵾����(�Ļ�)  ���������  �����ü��  ���¿� ������������ �� ����',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�Ż���',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'���������  ��Ʊ���  ��ǰ����� �� ��������  ��Ż��� ������',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'����',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'�༼Ź������  ��Ļ������  ��丮  ���û��',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��Ÿ',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'��ū���̵�����',1,1,'L');

		$svc_dt = str_replace('-','.',$baby['svc_dt']);

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'��������',1,0,'C', true);
		$pdf->Cell(25,$rowHeight,$baby['svc_dt'] != '0000-00-00' ? $svc_dt : '',1,0,'L');
		$pdf->Cell(112,$rowHeight,'',1,1,'C');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'���񽺱Ⱓ',1,0,'C', true);
		$pdf->Cell(50,$rowHeight,str_replace('-','.',$baby['svc_period']),1,0,'L');
		$pdf->Cell(25,$rowHeight,'���񽺽ð�',1,0,'C', true);
		$pdf->Cell(18,$rowHeight,$baby['svc_time'] != '' ? $baby['svc_time'].'�ð�' : '',1,0,'R');
		$pdf->Cell(25,$rowHeight,'�̿�ݾ�',1,0,'C', true);
		$pdf->Cell(19,$rowHeight,$baby['svc_use_amt'],1,1,'R');

		$pdf->SetX(14);
		$pdf->Cell(20,$rowHeight,'��Ÿ',1,0,'C',true);
		$pdf->Cell(162,$rowHeight,$baby['order'],1,1,'C');

		$pdf->SetXY(135,$height-35);
		$pdf->MultiCell (60,4,$baby['health_drug']);


		$row_lvl = 124.6 + $high - $rowHeight;
		$row_kind = 130.6 + $high - $rowHeight;
		$row_service1 = 172.7 + $high - $rowHeight;
		$row_service2 = 178.7 + $high - $rowHeight;
		$row_service3 = 184.7 + $high - $rowHeight;
		$row_service4 = 192.7 + $high - $rowHeight;

		//��ֵ��
		switch($baby['health_dis_lvl']){
			case N:
				$pdf->Text(60.6, $row_lvl, 'v');
				break;
			case 1:
				$pdf->Text(72.2, $row_lvl, 'v');
				break;
			case 2:
				$pdf->Text(85.9, $row_lvl, 'v');
				break;
			case 3:
				$pdf->Text(99.5, $row_lvl, 'v');
				break;
			case 4:
				$pdf->Text(113, $row_lvl, 'v');
				break;
			case 5:
				$pdf->Text(126.8, $row_lvl, 'v');
				break;
			case 6:
				$pdf->Text(140.4, $row_lvl, 'v');
				break;

		}

		//�������
		switch($baby['health_dis_kind']){
			case 1:
				$pdf->Text(60.6, $row_kind, 'v');
				break;
			case 2:
				$pdf->Text(78.7, $row_kind, 'v');
				break;
			case 3:
				$pdf->Text(99.7, $row_kind, 'v');
				break;
			case 4:
				$pdf->Text(117.8, $row_kind, 'v');
				break;
			case 5:
				$pdf->Text(135.8, $row_kind, 'v');
				break;
			case 6:
				$pdf->Text(153.8, $row_kind, 'v');
				break;
			case 9:
				$pdf->Text(171.8, $row_kind, 'v');
				break;

		}

		//�������
		if($baby['hope_service'][0]){
			$pdf->Text(60.6, $row_service1, 'v');
		}
		if($baby['hope_service'][1]){
			$pdf->Text(94.5, $row_service1, 'v');
		}
		if($baby['hope_service'][2]){
			$pdf->Text(112.5, $row_service1, 'v');
		}
		if($baby['hope_service'][3]){
			$pdf->Text(130.5, $row_service1, 'v');
		}
		if($baby['hope_service'][4]){
			$pdf->Text(141, $row_service1, 'v');
		}
		if($baby['hope_service'][5]){
			$pdf->Text(60.6, $row_service2, 'v');
		}
		if($baby['hope_service'][6]){
			$pdf->Text(78.7, $row_service2, 'v');
		}
		if($baby['hope_service'][7]){
			$pdf->Text(96.6,$row_service2, 'v');
		}
		if($baby['hope_service'][8]){
			$pdf->Text(132.6, $row_service2, 'v');
		}
		if($baby['hope_service'][9]){
			$pdf->Text(60.6, $row_service3, 'v');
		}
		if($baby['hope_service'][10]){
			$pdf->Text(81.8, $row_service3, 'v');
		}
		if($baby['hope_service'][11]){
			$pdf->Text(103, $row_service3, 'v');
		}
		if($baby['hope_service'][12]){
			$pdf->Text(114.6, $row_service3, 'v');
		}
		if($baby['hope_service'][13]){
			$pdf->Text(60.6, $row_service4, 'v');
		}
	}else {
		
		$height = $pdf->GetY();
		
		$high1 = 138 + $high - $rowHeight;
		$high2 = 142 + $high - $rowHeight;
		$high3 = 161 + $high - $rowHeight;
		$high4 = 165 + $high - $rowHeight;
		$high5 = 213 + $high - $rowHeight;
		$high6 = 217 + $high - $rowHeight;
		$high7 = 237 + $high - $rowHeight;
		$high8 = 241 + $high - $rowHeight;
		$high9 = 245 + $high - $rowHeight;
		$high10 = 273 + $high - $rowHeight;
		$high11 = 277 + $high - $rowHeight;
		
		

		$row_kind = 124.7 + $high - $rowHeight;
		$row_body = 130.7 + $high - $rowHeight;
		$row_eat_exc = 136.7 + $high - $rowHeight;
		$row_hygiene = 142.7 + $high - $rowHeight;
		$row_mind = 148.7 + $high - $rowHeight;
		$row_remember = 176.7 + $high - $rowHeight;
		$row_express = 182.7 + $high - $rowHeight;
		$row_use = 188.7 + $high - $rowHeight;


		$pdf->SetX(14);
		$pdf->Cell(20,18,'�ǰ�����',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'������',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,$normal['health_sick_nm'],1,0,'L');
		$pdf->Cell(25,$rowHeight,'�ູ��',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,$normal['health_drug_nm'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'���ܸ�',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,$normal['health_diag_nm'],1,0,'L');
		$pdf->Cell(25,$rowHeight,'��ָ�',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,$normal['health_dis_nm'],1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�÷�',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,'���ȣ  �ຸ��  �೪��',1,0,'L');
		$pdf->Cell(25,$rowHeight,'û��',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,'���ȣ  �ຸ��  �೪��',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,6,'��ü����',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'� �� Ȱ��',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'������ڸ� ��κ��ڸ� �������ε���',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,12,'',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�Ļ� �� ����',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,'������ڸ� ��κ��ڸ� �������ε���',1,0,'L');
		$pdf->Cell(25,$rowHeight,'�輳',1,0,'C', true);
		$pdf->Cell(56,$rowHeight,'������ڸ� ��κ��ڸ� �������ε���',1,1,'L');
		
		$pdf->Text(15.5,$high1,'������� ��');
		$pdf->Text(17.5,$high2,'��������');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(81,$rowHeight,'��������(����/����/����/��/�������/���)',1,0,'C', true);
		$pdf->Cell(81,$rowHeight,'������ڸ� ��κ��ڸ� �������ε��� ���Ÿ�ü�',1,1,'L');

		/*
			$pdf->SetX(14);
			$pdf->Cell(20,30,'�ǻ�������',1,0,'C',true);
			$pdf->Cell(25,$rowHeight,'����������',1,0,'C', true);
			$pdf->Cell(137,$rowHeight,'��Ȱ��/����  ������/������  �����/���  ���Ÿ(                                               )',1,1,'L');

			$pdf->SetX(14);
			$pdf->Cell(20,0,'',0,0,'C',true);
			$pdf->Cell(25,$rowHeight*3.7,'',1,0,'C',true);
			$pdf->Cell(137,$rowHeight*3.7,'',1,1,'L');

			$pdf->Text(38,$high3,'�ǻ���� ��');
			$pdf->Text(40,$high4,'�ǽĻ���');
		 */
		$pdf->SetX(14);
		$pdf->Cell(20,$rowHeight,'�ǻ�������',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'����������',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'��Ȱ��/����  ������/������  �����/���  ���Ÿ(                                               )',1,1,'L');
		

		$col_width_3 = 156;
		$row_height_3 = $rowHeight*4-2;
		
		$high_4 = get_row_cnt($pdf, $col_width_3, $row_height_3, $normal['talk_status']);
		
		if($high_4 > 22){
			$add_high = $high_4 - 22;
		}


		$pdf->SetX(14);
		$pdf->Cell(20,$high_4,'',1,0,'C',true);
		$pdf->Cell(162,$high_4,'',1,1,'L');

		$pdf->Text(16,$high3-5+($add_high/2),'��ü,�輳');
		$pdf->Text(16,$high3+($add_high/2),'����,����');
		$pdf->Text(16,$high3+4.5+($add_high/2),'�ǻ����');
		$pdf->Text(16,$high3+9+($add_high/2),'�ǽĻ���');

		$pdf->SetX(14);
		$pdf->Cell(20,12,'��������',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'������ �� ����',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'���Ȯ  ��κе���  ��Ұ���',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'��������',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'ǥ����',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'���Ȯ  ��κе���  ��Ұ���',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,12,'�ڿ��̿�',1,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�ڿ��̿���Ȳ',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,'�����  ���Ƿ���  ���ȸ�������  ���Ÿ',1,1,'L');

		$pdf->SetX(14);
		$pdf->Cell(20,0,'',0,0,'C',true);
		$pdf->Cell(25,$rowHeight,'�����',1,0,'C', true);
		$pdf->Cell(137,$rowHeight,$normal['center_use_other'],1,1,'L');

		$getY = $pdf->GetY();

		/*
			$pdf->SetXY(60,$height+43);
			$pdf->MultiCell (137,4,$normal['talk_status']);
		 */


		$pdf->SetXY(35,$height+42);
		$pdf->MultiCell (160,4,$normal['talk_status']);

		//�÷�
		switch($normal['health_eye_kind']){
			case 1:
				$pdf->Text(60.6, $row_kind, 'v');
				break;
			case 2:
				$pdf->Text(72.3, $row_kind, 'v');
				break;
			case 3:
				$pdf->Text(84, $row_kind, 'v');
				break;
		}

		//û��
		switch($normal['health_ear_kind']){
			case 1:
				$pdf->Text(141.7, $row_kind, 'v');
				break;
			case 2:
				$pdf->Text(153.4, $row_kind, 'v');
				break;
			case 3:
				$pdf->Text(165.1, $row_kind, 'v');
				break;
		}

		//� �� Ȱ��
		switch($normal['body_activate_kind']){
			case 1:
				$pdf->Text(60.6, $row_body, 'v');
				break;
			case 2:
				$pdf->Text(77.6, $row_body, 'v');
				break;
			case 3:
				$pdf->Text(94.6, $row_body, 'v');
				break;
		}

		//�Ļ� �� ����
		switch($normal['nutr_eat_kind']){
			case 1:
				$pdf->Text(60.6, $row_eat_exc, 'v');
				break;
			case 2:
				$pdf->Text(77.6, $row_eat_exc, 'v');
				break;
			case 3:
				$pdf->Text(94.6, $row_eat_exc, 'v');
				break;
		}

		//�輳
		switch($normal['nutr_excreta_kind']){
			case 1:
				$pdf->Text(141.6, $row_eat_exc, 'v');
				break;
			case 2:
				$pdf->Text(158.6, $row_eat_exc, 'v');
				break;
			case 3:
				$pdf->Text(175.6, $row_eat_exc, 'v');
				break;
		}

		//��������
		switch($normal['nutr_hygiene_kind']){
			case 1:
				$pdf->Text(116.7, $row_hygiene, 'v');
				break;
			case 2:
				$pdf->Text(133.5, $row_hygiene, 'v');
				break;
			case 3:
				$pdf->Text(150.5, $row_hygiene, 'v');
				break;
			case 4:
				$pdf->Text(170.6, $row_hygiene, 'v');
				break;
		}

		//����������
		switch($normal['talk_mind_kind']){
			case 1:
				$pdf->Text(60.6, $row_mind, 'v');
				break;
			case 2:
				$pdf->Text(79.9, $row_mind, 'v');
				break;
			case 3:
				$pdf->Text(102.2, $row_mind, 'v');
				break;
			case 9:
				$pdf->Text(121.3, $row_mind, 'v');
				$pdf->Text(132, $row_mind, $normal['talk_mind_other']);
				break;
		}
		
		
		//������ �� ����
		switch($normal['rec_remember_kind']){
			case 1:
				$pdf->Text(60.6, $row_remember+$add_high, 'v');
				break;
			case 2:
				$pdf->Text(72.3, $row_remember+$add_high, 'v');
				break;
			case 3:
				$pdf->Text(90.3, $row_remember+$add_high, 'v');
				break;
		}

		//ǥ����
		switch($normal['rec_express_kind']){
			case 1:
				$pdf->Text(60.6, $row_express+$add_high, 'v');
				break;
			case 2:
				$pdf->Text(72.3, $row_express+$add_high, 'v');
				break;
			case 3:
				$pdf->Text(90.3, $row_express+$add_high, 'v');
				break;
		}

		//Ÿ����̿���Ȳ
		switch($normal['center_use_kind']){
			case 1:
				$pdf->Text(60.6, $row_use+$add_high, 'v');
				break;
			case 2:
				$pdf->Text(72.3, $row_use+$add_high, 'v');
				break;
			case 3:
				$pdf->Text(90.3, $row_use+$add_high, 'v');
				break;
			case 4:
				$pdf->Text(114.5, $row_use+$add_high, 'v');
				break;
		}

		$pdf->row_height = 6;
		$col_width_1 = 156;
		if($gbn == 0){
			$row_height_1 = $rowHeight*3.5-2;
		}else {
			$row_height_1 = $rowHeight*3.5;
		}

		$high_0 = get_row_cnt($pdf, $col_width_1, $row_height_1, $client['client_text_1']);
		$high_1 = get_row_cnt($pdf, $col_width_1, $row_height_1, $client['client_text_2']);
		$high_2 = get_row_cnt($pdf, $col_width_1, $row_height_1, $client['client_text_3']);

		//���̺񱳺���
		$gY0 = $height + $add_high;
		$gY1 = $height + $high_0 + $add_high;
		$gY2 = $gY1 + $high_1 + $add_high;
		$gY3 = $gY2 + $high_2 + $add_high;
		
		$height0 = $height+88+$add_high;

		if($gY0 < 187){
			if($gY1 > 187){
				$pdf->AddPage(strtoupper('p'), 'A4');
				$height0 = $pdf->GetY();
			}
		}

		$pdf->SetXY(14, $height0);
		$pdf->Cell(20,$high_0,'',1,0,'C',true);
		$pdf->Cell(162,$high_0,'',1,1,'C');
		$height1 = $pdf->GetY();
		
		$pdf->SetXY(34, $height0);
		$pdf->MultiCell (160,4,$client['client_text_1']);
		$pdf->SetXY(14, $height0+($high_0*0.45));
		$pdf->MultiCell(20, 3.5,"���� ��\nȯ���� �屸", 0 , 'C');

		//$pdf->Text(16,$height1-14,'���� ��');
		//$pdf->Text(16,$height1-10,'ȯ���� �屸');



		if($gY1 < 187){
			if($gY2 > 187){
				$pdf->AddPage(strtoupper('p'), 'A4');
				$height1 = $pdf->GetY();
			}
		}

		$pdf->SetXY(14, $height1);
		$pdf->Cell(20,$high_1,'�ְ����屸',1,0,'C',true);
		$pdf->Cell(162,$high_1,'',1,1,'C');
		$height2 = $pdf->GetY();


		$pdf->SetXY(34,$height1);
		$pdf->MultiCell (160,4,$client['client_text_2']);

		
		if($gY2 < 187){
			if($gY3 > 187){
				
				$pdf->AddPage(strtoupper('p'), 'A4');
				$height2 = $pdf->GetY();

			}
		}

		/*
		$pdf->Text(16,$height-16,'�ǰ����� ��');
		$pdf->Text(16,$height-12,'�ֿ乮��');
		$pdf->Text(16,$height-8,'/�屸');
		*/
		if($pdf->GetY() > 250){
			$pdf->AddPage(strtoupper('p'), 'A4');
			$height2 = $pdf->GetY();
		}

		$pdf->SetXY(14, $height2);
		$pdf->Cell(20,$high_2,'����',1,0,'C',true);
		$pdf->Cell(162,$high_2,'',1,1,'C');
		
		
		$pdf->SetXY(34,$height2);
		$pdf->MultiCell (160,4,$client['client_text_3']);


		/*
		$pdf->Text(16,$height-14,'������ġ �� ');
		$pdf->Text(16,$height-10,'����� �ǰ�');
		*/

	}


	$pdf->Output();

	include_once('../inc/_db_close.php');


	//�� ���̸� ���Ѵ�.
	function get_row_cnt($pdf, $col_w, $row_h, $text){

		$row_high = $pdf->row_height;
		$str_text =  explode("\n", stripslashes(str_replace(chr(13).chr(10), "\n", $text)));
		$str_cnt = sizeof($str_text);

		for($i=0; $i<$str_cnt; $i++){
			$str_wid = $pdf->GetStringWidth($str_text[$i]);

			if($str_wid > $col_w){
				$row_cnt += ceil($str_wid/$col_w);
			}else {
				$row_cnt += 1;
			}
		}

		$row_high = $row_cnt*4;

		if($row_h > $row_high){
			$high = $row_h;
		}else {
			$high = $row_high;
		}

		return $high;
	}

?>
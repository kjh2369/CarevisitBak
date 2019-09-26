<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_function.php');
	require_once('../pdf/korean.php');
	require_once('../pdf/pdf_counsel_table.php');

	$conn->set_name('euckr');

	$code = $_GET['code'];
	$ssn = $ed->de($_GET['ssn']);
	
	if($debug){
		$rows = 5;
	}else {
		$rows = 5;
	}

	$sql = "select mem_ssn
			,      mem_counsel_dt
			,      mem_counsel_gbn
			,	   mem_edu_lvl
			,	   mem_gbn
			,	   mem_abode
			,	   mem_religion
			,	   mem_rel_other
			,      mem_app_path
			,	   mem_app_other
			,	   mem_svc_work
			,	   mem_svc_other
			,	   mem_hope_work
			,      mem_hope_other
			,	   mem_work_time
			,	   mem_salary
			,	   mem_hourly
			,      mem_talker_id
			,      mem_talker_nm
			,      mem_counsel_gbn
			,	   mem_counsel_dt
			,	   mem_counsel_content
			,	   mem_counsel_action
			,	   mem_counsel_result
			,	   mem_counsel_other
			,      mem_nm
			,      mem_phone
			,	   mem_mobile
			,      mem_email
			,	   mem_postno
			,      mem_addr
			,      mem_addr_dtl
			,      mem_marry
			,      mem_picture
			,      m00_cname
			  from counsel_mem
		inner join m00center
		        on m00_mcode = org_no
			 where org_no   = '$code'
			   and mem_ssn  = '$ssn'
			   and del_flag = 'N'";
	
	
	$mem = $conn->get_array($sql);
	
	
	$pdf = new MYPDF(strtoupper('P'));
	$pdf->font_name_kor = '����';
	$pdf->font_name_eng = 'Gulim';
	$pdf->AddUHCFont('����','Gulim');
	$pdf->Open();

	$pdf -> code = $_SESSION['userCenterGiho'];						//����ڵ�
	$pdf -> cname = $mem['m00_cname'];			//�����
	$pdf -> picture = $mem['mem_picture'];		//�������
	$pdf -> mname = $mem['mem_nm'];				//����
	$pdf -> mjumin = $myF->issStyle($mem['mem_ssn']);			//�ֹ�
	$pdf -> marry_yn = $mem['mem_marry'];		//��ȥ����
	$pdf -> tel = $myF->phoneStyle($mem['mem_phone'],'.');			//����
	$pdf -> mobile = $myF->phoneStyle($mem['mem_mobile'],'.');		//����(�޴���)
	$pdf -> email = $mem['mem_email'];			//�̸���
	$pdf -> postno = getPostNoStyle($mem['mem_postno']);		//�����ȣ
	$pdf -> addr = $mem['mem_addr'];			//�ּ�
	$pdf -> addr_dtl = $mem['mem_addr_dtl'];	//���ּ�

	$pdf->SetFillColor(222,222,222);

	$pdf->AddPage(strtoupper('p'), 'A4');

	$pdf->SetXY($pdf->left,$pdf->top+62);

	//����
	switch($mem['mem_religion']){
		case N:
			$pdf->Text(37.7, $rows+81, 'v');
			break;
		case 1:
			$pdf->Text(46.2, $rows+81, 'v');
			break;
		case 2:
			$pdf->Text(61, $rows+81, 'v');
			break;
		case 3:
			$pdf->Text(75.7, $rows+81, 'v');
			break;
		case 9:
			$pdf->Text(87.5, $rows+81, 'v');
			$pdf->Text(99, $rows+81.5, $mem['mem_rel_other']);
			break;
	}

	//����
	switch($mem['mem_gbn']){
		case 'A':
			$pdf->Text(112.5, $rows+96, 'v');
			break;
		case 'B':
			$pdf->Text(124.3, $rows+96, 'v');
			break;
		case 'C':
			$pdf->Text(124.3, $rows+96, 'v');
			break;
	}

	//�ְ�
	switch($mem['mem_abode']){
		case 1:
			$pdf->Text(112.5, $rows+86, 'v');
			break;
		case 3:
			$pdf->Text(124.3, $rows+86, 'v');
			break;
		case 5:
			$pdf->Text(136, $rows+86, 'v');
			break;
	}
	//�з�
	switch($mem['mem_edu_lvl']){
		case 1:
			$pdf->Text(112.5, $rows+91, 'v');
			break;
		case 3:
			$pdf->Text(130.5, $rows+91, 'v');
			break;
		case 5:
			$pdf->Text(142.3, $rows+91, 'v');
			break;
		case 7:
			$pdf->Text(160.3, $rows+91, 'v');
			break;
	}


	//��û���
	switch($mem['mem_app_path']){
		case 1:
			$pdf->Text(37.7, $rows+101, 'v');
			break;
		case 2:
			$pdf->Text(55.7, $rows+101, 'v');
			break;
		case 3:
			$pdf->Text(86.3, $rows+101, 'v');
			break;
		case 4:
			$pdf->Text(101, $rows+101, 'v');
			break;
		case 5:
			$pdf->Text(112.8, $rows+101, 'v');
			break;
		case 9:
			$pdf->Text(134, 106, 'v');
			$pdf->Text(145, $rows+101.5, $mem['mem_app_other']);
			break;
	}


	//�ڿ��������
	switch($mem['mem_svc_work']){
		case N:
			$pdf->Text(37.7, $rows+106, 'v');
			break;
		case Y:
			$pdf->Text(46, $rows+106, 'v');
			$pdf->Text(63, $rows+106.5, $mem['mem_svc_other']);
			break;
	}

	//Ȱ���������
	if($mem['mem_hope_work'][0] == 'Y'){
		$pdf->Text(37.7, $rows+111, 'v');
	}

	if($mem['mem_hope_work'][1] == 'Y'){
		$pdf->Text(55.7, $rows+111, 'v');
	}

	if($mem['mem_hope_work'][2] == 'Y'){
		$pdf->Text(73.7, $rows+111, 'v');
	}

	if($mem['mem_hope_work'][3] == 'Y'){
		$pdf->Text(91.7, $rows+111, 'v');
	}

	if($mem['mem_hope_work'][4] == 'Y'){
		$pdf->Text(112.9, $rows+111, 'v');
	}

	if($mem['mem_hope_work'][$rows] == 'Y'){
		$pdf->Text(137.2, $rows+111, 'v');
		$pdf->Text(148, $rows+111.5, $mem['mem_hope_other']);
	}

	//�ٹ����ɽð�
	switch($mem['mem_work_time']){
		case 1:
			$pdf->Text(37.7, $rows+116, 'v');
			break;
		case 2:
			$pdf->Text(49.3, $rows+116, 'v');
			break;
		case 3:
			$pdf->Text(61, $rows+116, 'v');
			break;
	}

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'����',1,0,'L',true);
	$pdf->Cell(160,$rows,'�๫  ��⵶��  ��õ�ֱ�  ��ұ�  ���Ÿ(                    )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'���/Ư��',1,0,'L',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'�ְ�',1,0,'L',true);
	$pdf->Cell(85,$rows,'������  �����  ���ڰ�',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'�������',1,0,'L',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'�з�',1,0,'L',true);
	$pdf->Cell(85,$rows,'����������  �����  ���������  ������̻�',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'ġ����������',1,0,'L',true);
	$pdf->Cell(50,$rows,'',1,0,'L');
	$pdf->Cell(25,$rows,'����',1,0,'L',true);
	$pdf->Cell(85,$rows,'���Ϲ�  ��������(����,���� ����)',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'��û���',1,0,'L',true);
	$pdf->Cell(160,$rows,'�������Ź�  �����ͳ��������Ʈ  ��ȫ����  ��Ұ�  ��Ÿ����Ƿ�  ���Ÿ(                             ) ',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'�ڿ��������',1,0,'L',true);
	$pdf->Cell(160,$rows,'�๫  ���� (����:                                                                                )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'Ȱ���������',1,0,'L',true);
	$pdf->Cell(160,$rows,'�������  ����ε���  �డ�簣��  ����Ż���  �������Ȱ����  ���Ÿ(                           )',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,$rows,'�ٹ����ɽð�',1,0,'L',true);
	$pdf->Cell(50,$rows,'������  �����  �����',1,0,'L');
	$pdf->Cell(25,$rows,'����ҵ�',1,0,'L',true);
	$pdf->Cell(85,$rows,'�� '.number_format($mem['mem_salary']).'��(�ñ�: '.number_format($mem['mem_hourly']).'��)',1,1,'L');

	$high = $rows;

	$sql = "select count(*)
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '1'
			   and family_ssn = '$ssn'";
	$family = $conn->get_data($sql);

    $high = $family != 0 ? $family * $rows + $high : $rows*2;

	$pdf->SetX(14);
	$pdf->Cell(22,$high,'��������',1,0,'C',true);
	$pdf->Cell(25,$rows,'����',1,0,'C',true);
	$pdf->Cell(25,$rows,'����',1,0,'C',true);
	$pdf->Cell(25,$rows,'����',1,0,'C',true);
	$pdf->Cell(25,$rows,'����',1,0,'C',true);
	$pdf->Cell(20,$rows,'���ſ���',1,0,'C',true);
	$pdf->Cell(40,$rows,'������',1,1,'C',true);

	$sql = "select family_nm
			,	   family_rel
			,	   family_age
			,	   family_job
			,	   case family_with when 'Y' then '��' else '�ƴϿ�' end as fm_with
			,	   family_monthly
			  from counsel_family
			 where org_no = '$code'
			   and family_type = '1'
			   and family_ssn = '$ssn'";
	$conn->query($sql);

	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(25,$rows,$row['family_nm'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_rel'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_age'],1,0,'L');
			$pdf->Cell(25,$rows,$row['family_job'],1,0,'L');
			$pdf->Cell(20,$rows,$row['fm_with'],1,0,'C');
			$pdf->Cell(40,$rows,$row['family_month'],1,1,'L');

			$high += $rows;
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(20,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}


	$sql = "select count(*)
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '1'";
	$edu_dol = $conn->get_data($sql);

	$sql = "select count(*)
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '9'";
	$edu_order = $conn->get_data($sql);

	$high = $rows*2;

	if($edu_dol != 0 and $edu_order != 0){
		$high = ($edu_dol+$edu_order) * 5 + $rows*4;
	}else if($edu_dol == 0 and $edu_order == 0){
		$high = (($edu_dol+$edu_order) * 5 + $high)+$rows*4;
	}else if($edu_dol == 0 or $edu_order == 0){
		$high = (($edu_dol+$edu_order) * 5 + $high)+$rows*3;
	}


	$pdf->SetX(14);
	$pdf->Cell(22,$high,'�����̼�',1,0,'C',true);
	$pdf->Cell(160,$rows,'�������� ����',1,1,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(50,$rows,'�������',1,0,'C',true);
	$pdf->Cell(70,$rows,'������',1,0,'C',true);
	$pdf->Cell(40,$rows,'�����ð�',1,1,'C',true);

	$sql = "select *
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '1'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['edu_center'],1,0,'L');
			$pdf->Cell(70,$rows,$row['edu_nm'],1,0,'L');
			$pdf->Cell(40,$rows,$row['edu_time'],1,1,'L');
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(70,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(160,$rows,'��Ÿ����',1,1,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',0,0,'C',true);
	$pdf->Cell(50,$rows,'�������',1,0,'C',true);
	$pdf->Cell(70,$rows,'������',1,0,'C',true);
	$pdf->Cell(40,$rows,'�����ð�',1,1,'C',true);

	$sql = "select *
			  from counsel_edu
			 where org_no = '$code'
			   and edu_ssn = '$ssn'
			   and edu_gbn = '9'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['edu_center'],1,0,'L');
			$pdf->Cell(70,$rows,$row['edu_nm'],1,0,'L');
			$pdf->Cell(40,$rows,$row['edu_time'],1,1,'L');
		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(70,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}
	/*
	$pdf->SetX(14);
	$pdf->Cell(182,$rows,'',1,1,'L');
	*/
	$sql = "select count(*)
			  from counsel_license
			 where org_no = '$code'
			   and license_ssn = '$ssn'";
	$license = $conn->get_data($sql);

	$high = $rows;

	$high = $license != 0 ? $license * $rows + $high : $rows*2;

	//�ڰ���
	$pdf->SetX(14);
	$pdf->Cell(22,$high,'�ڰ�',1,0,'C',true);

	$pdf->SetX(14);
	$pdf->Cell(22,0,'',1,0,'C',true);
	$pdf->Cell(50,$rows,'�ڰ�������',1,0,'C',true);
	$pdf->Cell(25,$rows,'�ڰ�����ȣ',1,0,'C',true);
	$pdf->Cell(45,$rows,'�߱ޱⰣ',1,0,'C',true);
	$pdf->Cell(40,$rows,'�߱�����',1,1,'C',true);

	$sql = "select *
			  from counsel_license
			 where org_no = '$code'
			   and license_ssn = '$ssn'";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->SetX(14);
			$pdf->Cell(22,0,'',0,0,'C');
			$pdf->Cell(50,$rows,$row['license_gbn'],1,0,'L');
			$pdf->Cell(25,$rows,$row['license_no'],1,0,'L');
			$pdf->Cell(45,$rows,$row['license_center'],1,0,'L');
			if($row['license_dt'] != '0000-00-00'){
				$pdf->Cell(40,$rows,$row['license_dt'],1,1,'C');
			}else {
				$pdf->Cell(40,$rows,'',1,1,'C');
			}

		}
	}else {
		$pdf->SetX(14);
		$pdf->Cell(22,0,'',0,0,'C');
		$pdf->Cell(50,$rows,'',1,0,'L');
		$pdf->Cell(25,$rows,'',1,0,'L');
		$pdf->Cell(45,$rows,'',1,0,'L');
		$pdf->Cell(40,$rows,'',1,1,'L');
	}

	/*
	$pdf->SetX(14);
	$pdf->Cell(182,$rows,'',1,1,'L');
	*/



	if ($pdf->GetY()+68 > $pdf->height){
		$pdf->AddPage(strtoupper('p'), 'A4');
	}
	$height = $pdf->GetY();

	$pdf->SetX(14);
	$pdf->Cell(22,7,'�����',1,0,'L',true);
	$pdf->Cell(50,7,$mem['mem_talker_nm'],1,0,'L');
	$pdf->Cell(25,7,'�������',1,0,'L',true);
	$pdf->Cell(85,7,str_replace('-','.', $mem['mem_counsel_dt']),1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,7,'�������',1,0,'L',true);
	$pdf->Cell(160,7,'�೻�� ��湮 ����ȭ',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,13,'��㳻��',1,0,'L',true);
	$pdf->Cell(160,13,'',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,13,'��ġ����',1,0,'L',true);
	$pdf->Cell(160,13,'',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,13,'ó�����',1,0,'L',true);
	$pdf->Cell(160,13,'',1,1,'L');

	$pdf->SetX(14);
	$pdf->Cell(22,13,'��Ÿ',1,0,'L',true);
	$pdf->Cell(160,13,'',1,1,'L');

	$pdf->SetXY(36,$height+15);
	$pdf->MultiCell (158,4,$mem['mem_counsel_content']);

	$pdf->SetXY(36,$height+28);
	$pdf->MultiCell (158,4,$mem['mem_counsel_action']);

	$pdf->SetXY(36,$height+41);
	$pdf->MultiCell (158,4,$mem['mem_counsel_result']);

	$pdf->SetXY(36,$height+54);
	$pdf->MultiCell (158,4,$mem['mem_counsel_other']);

	//�������
	switch($mem['mem_counsel_gbn']){
		case 1:
			$pdf->SetXY(36.5,$height+7.5);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
		case 2:
			$pdf->SetXY(47.2,$height+7.5);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
		case 3:
			$pdf->SetXY(57.8,$height+7.5);
			$pdf->MultiCell($rows, $rows, 'v');
			break;
	}


	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
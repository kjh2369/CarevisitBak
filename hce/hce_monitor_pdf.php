<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	����͸� �����
	 *********************************************************/
	#$pdf->AddUHCFont('����', 'Dotum');
	#$pdf->AddUHCFont('����', 'Batang');
	#$pdf->AddUHCFont('�ü�', 'Gungsuh');
	#$pdf->AddUHCFont('����', 'Gulim');
	#$pdf->AddUHCFont('����', 'Dotum');
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	//����ڸ�
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'EV5\'
			AND		use_yn	= \'Y\'';

	$evaluation = $conn->_fetch_array($sql,'code');

	
	//����͸�
	$sql = 'SELECT	*
			FROM	hce_monitor
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		mntr_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$mntrGbn	= $row['mntr_gbn'];		//�ۼ�����
		$mntrType	= $row['mntr_type'];	//����͸� ����
		$perNm		= $row['per_nm'];		//��������
		$ispNm		= $row['inspector_nm'];	//������
		$mntrDate	= $myF->dateStyle($row['mntr_dt'],'.');

		//���� ���� ���� ��Ű�� ����
		$scheduleSat = $row['schedule_sat'];
		$scheduleStr = StripSlashes($row['schedule_svc']);
		
		//���� ���� ��Ȯ�� �� ��ǵ�
		$fullnessSat = $row['fullness_sat'];
		$fullnessStr = StripSlashes($row['fullness_svc']);

		//���� ����ڿ� ���� ������
		$perinchargeSat = $row['perincharge_sat'];
		$perinchargeStr = StripSlashes($row['perincharge_svc']);

		//��ɻ��� ��ȭ
		$abilityStr = StripSlashes($row['ability_change']);

		//��Ȱȯ�� �� ���ߴɷº�ȭ
		$lifeEnvStr = StripSlashes($row['life_env_change']);

		//��Ÿ ������� ����͸�
		$extDiscomfortStr = StripSlashes($row['ext_discomfort']);

		//
		$monitorRst		= $row['monitor_rst'];
		$extDetailStr	= StripSlashes($row['ext_detail']);
	}

	Unset($row);


	//�ۼ�����
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'CT\'
			AND		use_yn	= \'Y\'';

	$mntrGbn = lfGetGbnStr($pdf,$myF,$conn,$sql,$mntrGbn);


	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->Cell($pdf->width, $pdf->row_height, $mntrGbn, 0, 1);

	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.31;
	$rowH = $pdf->row_height * 2;

	$X = $pdf->left;
	$Y1 = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, "".$hce->IPIN, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "��        ��", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, ($mntrType == '1' ? '��' : '��')."����    ".($mntrType == '2' ? '��' : '��')."����", 1, 1, "C");

	$Y2 = $pdf->GetY();

	$H = lfGetStringHeight($pdf,$col[0],"�� �� ��\n������ȣ");

	$pdf->SetXY($X, $Y1 + ($rowH - $H) / 2);
	$pdf->MultiCell($col[0], 4, "�� �� ��\n������ȣ", 0, "C");

	$H = lfGetStringHeight($pdf,$col[0],"����͸�\n����");

	$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3], $Y1 + ($rowH - $H) / 2);
	$pdf->MultiCell($col[4], 4, "����͸�\n��      ��", 0, "C");

	$pdf->SetXY($X, $Y2);
	$pdf->Cell($col[0], $rowH, "��������", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $perNm, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "��  ��  ��", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $ispNm, 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "�ۼ�����", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, $mntrDate, 1, 1, "C");

	Unset($col);


	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $pdf->row_height, "1. �̿��� �ǰ�����", 0, 1);
	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "1) ���� ����͸�", 0, 1);


	$col[] = $pdf->width * 0.25;
	$col[] = $pdf->width * 0.75;

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $pdf->row_height, "��     ��", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "��ü���� ����", 1, 1, "C", 1);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"���� ���� ����\n��Ű�� ����");
	$H2 = lfGetStringHeight($pdf,$col[1],$scheduleStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "���� ���� ����\n��Ű�� ����");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $scheduleStr);
	

	foreach($evaluation as $ev){
		if($ev['code'] == $scheduleSat){ 
			$schName .= ' ��'.$ev['name'];
		}else {
			$schName .= ' ��'.$ev['name'];
		}
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $schName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($schName);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"���� ����\n��Ȯ�� �� ��ǵ�");
	$H2 = lfGetStringHeight($pdf,$col[1],$fullnessStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "���� ����\n��Ȯ�� �� ��ǵ�");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $fullnessStr);
	
	foreach($evaluation as $ev){
		if($ev['code'] == $fullnessSat){ 
			$fullName .= ' ��'.$ev['name'];
		}else {
			$fullName .= ' ��'.$ev['name'];
		}
	}
	
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $fullName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($fullName);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"���� ����ڿ�\n���� ������");
	$H2 = lfGetStringHeight($pdf,$col[1],$perinchargeStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + (($H - $H1)+6) / 2);
	$pdf->MultiCell($col[0], 4, "���� ����ڿ�\n���� ������");

	$pdf->SetXY($X + $col[0], $Y + (($H - $H2)+12) / 2);
	$pdf->MultiCell($col[1], 4, $perinchargeStr);

	foreach($evaluation as $ev){
		if($ev['code'] == $perinchargeSat){ 
			$peName .= ' ��'.$ev['name'];
		}else {
			$peName .= ' ��'.$ev['name'];
		}
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H+6, "", 1, 0, "C");
	$pdf->Cell($col[1], 6, $peName, 1, 1, "L");
	
	$pdf->SetX($X+$col[0]);
	$pdf->Cell($col[1], $H, "", 1, 1, "C");

	unset($peName);

	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "2) �屸��ȭ ����͸�", 0, 1);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $pdf->row_height, "��     ��", 1, 0, "C", 1);
	$pdf->Cell($col[1], $pdf->row_height, "��ü���� ����", 1, 1, "C", 1);



	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"��ɻ��� ��ȭ");
	$H2 = lfGetStringHeight($pdf,$col[1],$abilityStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "��ɻ��� ��ȭ");

	$pdf->SetXY($X + $col[0], $Y + ($H - $H2) / 2);
	$pdf->MultiCell($col[1], 4, $abilityStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C");
	$pdf->Cell($col[1], $H, "", 1, 1, "C");



	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0],"��Ȱȯ�� ��\n���ߴɷº�ȭ");
	$H2 = lfGetStringHeight($pdf,$col[1],$lifeEnvStr);
	$H	= ($H1 > $H2 ? $H1 : $H2) + 1.6;

	$pdf->SetXY($X, $Y + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "��Ȱȯ�� ��\n���ߴɷº�ȭ");

	$pdf->SetXY($X + $col[0], $Y + ($H - $H2) / 2);
	$pdf->MultiCell($col[1], 4, $lifeEnvStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C");
	$pdf->Cell($col[1], $H, "", 1, 1, "C");



	$pdf->SetX($pdf->left + $pdf->width * 0.02);
	$pdf->Cell($pdf->width * 0.98, $pdf->row_height, "3) ��Ÿ ������� ����͸�", 0, 1);

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H = lfGetStringHeight($pdf,$pdf->width,$extDiscomfortStr) + 1.6;

	$pdf->SetXY($X, $Y+1);
	$pdf->MultiCell($pdf->width, 4, $extDiscomfortStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($pdf->width, $H, "", 1, 1, "C");



	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width, $pdf->row_height, "2. ���� ���հ�ȹ", 0, 1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "����͸� ��� �ݿ�", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, ($monitorRst == '1' ? '��' : '��')."���� ����   ".($monitorRst == '2' ? '��' : '��')."���� ��ȹ ����   ".($monitorRst == '3' ? '��' : '��')."�� �湮 ���", 1, 1, "L");

	$X = $pdf->left;
	$Y = $pdf->GetY();
	$H = $pdf->height+10 - $Y;

	$pdf->SetXY($X + $col[0], $Y + 0.8);
	$pdf->MultiCell($col[1], 4, $extDetailStr);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "��Ÿ ��ü���� ���", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H, "", 1, 1, "L");

	Unset($col);
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $var['jumin'];
	$yymm = $var['yymm'];
	$seq = $var['seq'];
	$cnt = str_replace('ȸ��','', iconv('utf-8','euc-kr', $var['cnt']));
	
	//�����ڸ�
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);

	//��� �� ��ȿ�Ⱓ
	$sql = 'SELECT	level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'';

	$lvl = $conn->get_data($sql);
	
	/*
	if ($lvl){
		$lvl = $lvl.'���';
	}else{
		$lvl = '';
	}
	*/


	$lvl = $myF->_lvlNm($lvl);


	//����
	$sql = 'SELECT	date, time, seq
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND     seq     = \''.$seq.'\'
			AND		del_flag = \'N\'';

	$data = $conn->get_array($sql);


	//����
	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND     concat(date,time)  < \''.$data['date'].$data['time'].'\'
			AND		del_flag = \'N\'
		    order   by date, time desc
			limit   1';

	$row = $conn->get_array($sql);


	$sql = 'SELECT	count(*)
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND     concat(date,time)   < \''.$data['date'].$data['time'].'\'
			AND		del_flag = \'N\'';
	$Cnt = $conn->get_data($sql);

	$sql = 'SELECT	count(*)
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND     concat(date,time)   <= \''.$data['date'].$data['time'].'\'
			AND		del_flag = \'N\'';
	$Cnt2 = $conn->get_data($sql);

	if($row != ''){
		//����
		$sql = 'SELECT	*
				FROM	sw_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'
				AND	    seq     = \''.$seq.'\'
				AND		del_flag = \'N\'';
		$row2 = $conn->get_array($sql);

		$info = $row2;

	}else {
		//����
		$sql = 'SELECT	*
				FROM	sw_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'
				AND	    seq     = \''.$seq.'\'
				AND		del_flag = \'N\'';

		$row =  $conn->get_array($sql);

		//����
		$sql = 'SELECT	*
				FROM	sw_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'
				AND     concat(date,time)   > \''.$data['date'].$data['time'].'\'
				AND		del_flag = \'N\'';

		$row2 =  $conn->get_array($sql);

		if($row2 == ''){
			$info = $row;
		}else {
			$info = $row2;
		}

		$Cnt = '1';
		$Cnt2 = '2';
	}


	//��纸ȣ��
	$sql = 'SELECT	t01_mem_cd1 AS mem_cd1
			,		t01_mem_nm1 AS mem_nm1
			,		t01_mem_cd2 AS mem_cd2
			,		t01_mem_nm2 AS mem_nm2
			,		t01_status_gbn AS stat
			,		t01_sugup_fmtime AS plan_from
			,		t01_sugup_totime AS plan_to
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_conf_fmtime AS conf_from
			,		t01_conf_totime AS conf_to
			,	    t01_toge_umu as toge_yn
			,		t01_sugup_soyotime as soyotime
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \'0\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$row['date'].'\'
			AND		t01_del_yn = \'N\'';
	$memRow = $conn->_fetch_array($sql);

	$rCnt = SizeOf($memRow);

	for($i=0; $i<$rCnt; $i++){
		$r = $memRow[$i];

		if ($r['stat'] == '1'){
			$stat = '�Ϸ�';
			$from = $r['conf_from'];
			$to = $r['conf_to'];
		}else if ($r['stat'] == '5'){
			$stat = '������';
			$from = $r['work_from'];
			$to = '9999';
		}else{
			$stat = '���';
			$from = $r['plan_from'];
			$to = $r['plan_to'];
		}
		
		//5��� ����Ȱ�� �����ɾ��� ��� �ð� 60��
		if($r['mem_nm2']!='' && $r['toge_yn']=='Y'){
			$soyoTime = $myF->time2min($to) - $myF->time2min($from);

			if($soyoTime==60){
				$to = $myF->min2time($myF->time2min($to) + 60);
			}else {
				$to = $myF->min2time($myF->time2min($to) + 30);
			}
		}

		if ($row['time'] >= $from && $row['time'] <= $to){
			$idx = SizeOf($memList);
			$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

			if ($r['mem_nm2']){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
			}
		}
	}

	if (is_array($memList)){
		$memList = $myF->sortArray($memList, 'name', 1);
	}


	//��纸ȣ��2
	$sql = 'SELECT	t01_yoyangsa_id1 AS mem_cd1
			,		t01_yname1 AS mem_nm1
			,		t01_yoyangsa_id2
			,		t01_yname2 AS mem_nm2
			,		t01_status_gbn AS stat
			,		t01_sugup_fmtime AS plan_from
			,		t01_sugup_totime AS plan_to
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_conf_fmtime AS conf_from
			,		t01_conf_totime AS conf_to
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \'0\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$row2['date'].'\'
			AND		t01_del_yn = \'N\'';

	$memRow = $conn->_fetch_array($sql);

	$rCnt = SizeOf($memRow);

	for($i=0; $i<$rCnt; $i++){
		$r = $memRow[$i];

		if ($r['stat'] == '1'){
			$stat = '�Ϸ�';
			$from = $r['conf_from'];
			$to = $r['conf_to'];
		}else if ($r['stat'] == '5'){
			$stat = '������';
			$from = $r['work_from'];
			$to = '9999';
		}else{
			$stat = '���';
			$from = $r['plan_from'];
			$to = $r['plan_to'];
		}

		if ($row2['time'] >= $from && $row2['time'] <= $to){
			$idx = SizeOf($memList2);
			$memList2[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

			if ($r['mem_nm2']){
				$idx = SizeOf($memList2);
				$memList2[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
			}
		}
	}


	if (is_array($memList2)){
		$memList2 = $myF->sortArray($memList2, 'name', 1);
	}


	$rowH = $pdf->row_height;


	$pdf->SetFont($pdf->font_name_kor, '', 9);

	$rowH = $rowH * (9 / 11);
	$mH = $rowH * 2;

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$col[] = $pdf->width * 0.13;
	$col[] = $pdf->width * 0.19;
	$col[] = $pdf->width * 0.68;

	//��ü����
	$bodyStatNote = StripSlashes($info['body_stat_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$bodyStatNote) + 2;
	$H[0] = ($tmpH > $rowH ? $tmpH : $rowH);

	//����
	$diseaseStr = "������ : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($info['disease'] ? $info['disease'] : "                         ")), $col[2] * 0.4);

	//�ູ��
	if ($info['medication'] == 'Y'){
		$diseaseStr .= "(�ູ��)";
	}

	//���ܸ�
	$diseaseStr .= "   ���ܸ� : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($info['diagnosis'])), $col[2] * 0.5)."\n";

	//��ָ�
	$diseaseStr .= "��ָ� : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($info['disabled'])), $col[2] * 0.7);

	if ($row['disabled_lvl'] > 0){
		$diseaseStr .= "(".$info['disabled_lvl']."���)";
	}

	$diseaseStr .= "\n";
	
	//�÷�
	if ($row['eyesight']){
		$val[0] = lfSetMark($info['eyesight'],'1');
		$val[1] = lfSetMark($info['eyesight'],'2');
		$val[2] = lfSetMark($info['eyesight'],'3');

		$diseaseStr .= "�÷� : ".$val[0]."��ȣ   ".$val[1]."����   ".$val[2]."����";
	}
	
	
	//û��
	if ($row['hearing']){
		$val[0] = lfSetMark($info['hearing'],'1');
		$val[1] = lfSetMark($info['hearing'],'2');
		$val[2] = lfSetMark($info['hearing'],'3');

		if ($row['eyesight']){
			$diseaseStr .= " / ";
		}

		$diseaseStr .= "û�� : ".$val[0]."��ȣ   ".$val[1]."����   ".$val[2]."����";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$diseaseStr) + 2;
	$H[1] = ($tmpH > $mH ? $tmpH : $mH);

	//�������
	$diseaseNote = StripSlashes($info['disease_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$diseaseNote) + 2;
	$H[2] = ($tmpH > $rowH ? $tmpH : $rowH);

	//����,����
	$memoryStr = "����,���� : ";

	if ($info['memory'] == '1'){
		$memoryStr .= "��Ȯ";
	}else if ($info['memory'] == '2'){
		$memoryStr .= "�κе���";
	}else if ($info['memory'] == '3'){
		$memoryStr .= "�Ұ���";
	}else{
		$memoryStr .= "                       ";
	}

	$memoryStr .= "           ǥ���� : ";

	if ($info['express'] == '1'){
		$memoryStr .= "��Ȯ";
	}else if ($info['express'] == '2'){
		$memoryStr .= "�κе���";
	}else if ($info['express'] == '3'){
		$memoryStr .= "�Ұ���";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$memoryStr) + 2;
	$H[3] = ($tmpH > $rowH ? $tmpH : $rowH);

	//����,���� ���
	$memoryNote = StripSlashes($info['memory_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$memoryNote) + 2;
	$H[4] = ($tmpH > $rowH ? $tmpH : $rowH);

	//����������
	$commStr = "���������� : ";
	if ($row['feel_stat'] == '1'){
		$commStr .= "Ȱ��/����";
	}else if ($row['feel_stat'] == '2'){
		$commStr .= "����/����";
	}else if ($row['feel_stat'] == '3'){
		$commStr .= "���/���";
	}
	$commStr .= "           ";

	//�ǻ�����Ÿ
	$commStr .= "��Ÿ : ".$pdf->_splitTextWidth($myF->utf(StripSlashes($info['comm_other'])), $col[2] * 0.9);

	$tmpH = $pdf->_getStrY($pdf,$col[2],$commStr) + 2;
	$H[5] = ($tmpH > $rowH ? $tmpH : $rowH);

	//�ǻ������
	$commNote = StripSlashes($info['comm_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$commNote) + 2;
	$H[6] = ($tmpH > $rowH ? $tmpH : $rowH);

	//�Ļ�����
	$nutritionStr = "�Ļ����� : ";
	if ($info['meal_type'] == '1'){
		$nutritionStr .= "�Ϲݽ�";
	}else if ($info['meal_type'] == '2'){
		$nutritionStr .= "�索��";
	}else if ($info['meal_type'] == '3'){
		$nutritionStr .= "��";
	}else if ($info['meal_type'] == '3'){
		$nutritionStr .= "����޽�";
	}

	//��������
	if ($info['intake_type'] == '1'){
		$nutritionStr .= "(3���� ��Ģ������ �Դ´�.)";
	}else if ($info['intake_type'] == '2'){
		$nutritionStr .= "(��� 2���� �Դ´�.)";
	}else if ($info['intake_type'] == '3'){
		$nutritionStr .= "(1�ĸ� �Դ´�.)";
	}

	$nutritionStr .= "\n";

	//���м���
	$nutritionStr .= "���м��� : ";
	if ($info['water_type'] == '1'){
		$nutritionStr .= "1�� 5���̻�";
	}else if ($info['water_type'] == '2'){
		$nutritionStr .= "1�� 2~4��";
	}else if ($info['water_type'] == '3'){
		$nutritionStr .= "1�� 1~2��";
	}else if ($info['water_type'] == '4'){
		$nutritionStr .= "1�� 1��";
	}else if ($info['water_type'] == '9'){
		$nutritionStr .= "���� ����� ����";
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$nutritionStr) + 2;
	$H[7] = ($tmpH > $rowH ? $tmpH : $rowH);

	//������º��
	$nutritionNote = StripSlashes($info['nutrition_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$nutritionNote) + 2;
	$H[8] = ($tmpH > $rowH ? $tmpH : $rowH);

	//���� �� ȯ��
	$envNote = StripSlashes($info['env_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$envNote) + 2;
	$H[9] = ($tmpH > $rowH ? $tmpH : $rowH);

	//����
	$totalNote = StripSlashes($info['total_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$totalNote) + 2;
	$H[10] = ($tmpH > $rowH ? $tmpH : $rowH);

	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;

	//�屸���� - ��ü����
	$val[0] = lfSetMark($info['body_stat'],'1');
	$val[1] = lfSetMark($info['body_stat'],'2');
	$val[2] = lfSetMark($info['body_stat'],'3');

	/*
	if ($Y >= $pdf->height - $totH){
		//63/270/222
		//echo $Y.'/'.$pdf->height.'/'.$totH;
		//$totH1 = $totH - $Y;
		$totH1 = $Y + ($pdf->height - $totH);
	}else{
		$totH1 = $totH;
	}
	*/

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $rowH + $totH1, "�屸����", 1, 0, "C");
	$pdf->Cell($col[1], $rowH + $H[0], "�� ��ü����", 1);
	$pdf->Cell($col[2], $rowH, $val[0].'�����ڸ�   '.$val[1].'�κ��ڸ�   '.$val[2].'������ ����', 1, 1);

	$X1 = $X + $col[0] + $col[1];
	$pdf->SetX($X1);
	$pdf->Cell($col[2], $H[0], '', 1, 1);

	$Y = $Y + $rowH;

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $bodyStatNote);
	$Y = $Y + $H[0];


	//�屸���� - ����
	$pdf->SetXY($X + $col[0], $Y);
	$pdf->Cell($col[1], $H[1]+$H[2], "�� ����", 1);
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $diseaseStr);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0] + $col[1];


	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[2], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $diseaseNote);

	$Y = $Y + $H[2];


	if ($Y >= $pdf->height - $H[3]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[3], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//�屸���� - ��������
	$pdf->SetXY($X + $col[0], $Y);
	$pdf->Cell($col[1], $H[3]+$H[4], "�� ��������", 1);
	$pdf->Cell($col[2], $H[3], $memoryStr, 1, 1);

	$Y = $Y + $H[3];
	$X1 = $X + $col[0] + $col[1];


	if ($Y >= $pdf->height - $H[4]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[4], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[4], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $memoryNote);

	$Y = $Y + $H[4];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[5]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[5], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//�屸���� - �ǻ����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[5]+$H[6], "�� �ǻ����", 1);
	$pdf->Cell($col[2], $H[5], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $commStr);

	$Y = $Y + $H[5];


	if ($Y >= $pdf->height - $H[6]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[6], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[6], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $commNote);

	$Y = $Y + $H[6];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[7]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[7], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//�������
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[7]+$H[8], "�� �������", 1);
	$pdf->Cell($col[2], $H[7], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $nutritionStr);

	$Y = $Y + $H[7];


	if ($Y >= $pdf->height - $H[8]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[8], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//������� ���
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2], $H[8], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $nutritionNote);

	$Y = $Y + $H[8];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[9]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[9], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//���� �� ȯ��
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[9], "�� ���� �� ȯ��", 1);
	$pdf->Cell($col[2], $H[9], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $envNote);

	$Y = $Y + $H[9];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[10]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[10], "�屸����", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[10], "����", 1, 0, "C");
	$pdf->Cell($col[2], $H[10], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $totalNote);

	$Y = $Y + $H[10];

	Unset($H);


	//2.�޿�������ȹ
	//2-1.�޿���ǥ
	$targetNote = StripSlashes($info['target_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$targetNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $rowH);

	//2-2.�ʿ� �޿�����
	$contNote = StripSlashes($info['cont_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$contNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $rowH);

	//2-3.�������
	$provideNote = StripSlashes($info['provide_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$provideNote) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $rowH);

	$totH = 0;
	$totH1 = 0;
	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}
	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}


	//2-1.�޿���ǥ
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "�޿�������ȹ", 1, 0, "C");
	$pdf->Cell($col[1], $H[0], "�޿���ǥ", 1, 0, "C");
	$pdf->Cell($col[2], $H[0], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $targetNote);

	$Y = $Y + $H[0];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[1]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[1], "�޿�������ȹ", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//2-2.�ʿ� �޿�����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[1], "�ʿ� �޿�����", 1, 0, "C");
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $contNote);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2], "�޿�������ȹ", 1, 0, "C");
		$Y = $pdf->GetY();
	}


	//2-3.�������
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[2], "�������", 1, 0, "C");
	$pdf->Cell($col[2], $H[2], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $provideNote);

	$Y = $Y + $H[2];

	Unset($H);


	//3.����Ȱ�� ���α׷� ������ȹ
	//3-1-1.�����ڱ� �ʿ䳻��
	$planRecText = StripSlashes($info['plan_rec_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planRecText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-1-2.�����ڱ� �������
	$planRecWay = StripSlashes($info['plan_rec_way']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planRecWay) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2-1.��ü�ɷ� �ʿ䳻��
	$planBodyText = StripSlashes($info['plan_body_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planBodyText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2-2.��ü�ɷ� �������
	$planBodyWay = StripSlashes($info['plan_body_way']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$planBodyWay) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	if ($Y >= $pdf->height - $H[0] - $H[1]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}

	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
			if ($i % 2 == 1) $totH1 -= $tmpH;
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	//3.����Ȱ�� ���α׷� ������ȹ
	//3-1.�����ڱ�
	//3-1-1.�ʿ䳻��
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "", 1, 0, "C");
	$pdf->Cell($col[1] / 2, $H[0] + $H[1], "�����ڱ�", 1, 0, "C");
	$pdf->Cell($col[1] / 2, $H[0], "�ʿ䳻��", 1, 0, "C");
	$pdf->Cell($col[2], $H[0], "", 1, 2);

	$pdf->SetX($X + $col[0] + $col[1] / 2);
	$pdf->Cell($col[1] / 2, $H[1], "�������", 1, 0, "C");
	$pdf->Cell($col[2], $H[1], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$tmpH = ($totH1 - $pdf->_getStrY($pdf,$col[0],"����Ȱ��\n���α׷�\n������ȹ")) / 2;

	$pdf->SetXY($X, $Y + $tmpH);
	$pdf->MultiCell($col[0], 4, "����Ȱ��\n���α׷�\n������ȹ", 0, "C");

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $planRecText);

	$pdf->SetXY($X1, $Y + $H[0] + 1);
	$pdf->MultiCell($col[2], 4, $planRecWay);

	$Y = $Y + $H[0] + $H[1];
	$X1 = $X + $col[0];


	if ($Y >= $pdf->height - $H[2] - $H[3]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2] + $H[3], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[0],"����Ȱ��\n���α׷�\n������ȹ")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "����Ȱ��\n���α׷�\n������ȹ", 0, "C");
	}

	//3-2.��ü�ɷ� ����.����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1] / 2, $H[2] + $H[3], "", 1, 0, "C");
	$pdf->Cell($col[1] / 2, $H[2], "�ʿ䳻��", 1, 0, "C");
	$pdf->Cell($col[2], $H[2], "", 1, 2);

	$pdf->SetX($X + $col[0] + $col[1] / 2);
	$pdf->Cell($col[1] / 2, $H[3], "�������", 1, 0, "C");
	$pdf->Cell($col[2], $H[3], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$tmpH = (($H[2] + $H[3]) - $pdf->_getStrY($pdf,$col[1] / 2,"��ü�ɷ�\n����.����")) / 2;
	$pdf->SetXY($X + $col[0], $Y + $tmpH);
	$pdf->MultiCell($col[1] / 2, 4, "��ü�ɷ�\n����.����", 0, "C");

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $planBodyText);

	$pdf->SetXY($X1, $Y + $H[2] + 1);
	$pdf->MultiCell($col[2], 4, $planBodyWay);

	$Y = $Y + $H[2] + $H[3];
	$X1 = $X + $col[0];


	Unset($H);


	//4.��ȣ�� ���
	$guardText = StripSlashes($info['guard_text']);
	$tmpH = $pdf->_getStrY($pdf,$col[2],$guardText) + 2;
	$H[] = ($tmpH > $mH ? $tmpH : $mH);

	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0] + $col[1], $totH1, "��ȣ�� ���", 1, 0, "C");
	$pdf->Cell($col[2], $H[0], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $guardText);

	$Y = $Y + $H[0]+6;
	$X1 = $X + $col[0];
	$Yg = $Y-6;

	Unset($H);

	$pdf->SetXY($X, $Yg);
	$pdf->Cell($col[0]+$col[1], 6, "", 1, 0, "C");
	$pdf->Cell($col[2]/2, 6, $Cnt.'ȸ', 1, 0,'C');
	$pdf->Cell($col[2]/2, 6, $Cnt2.'ȸ', 1, 1,'C');


	//3.�޿�����Ȯ��
	//3-1.Ȯ�γ���
	$checkNote = StripSlashes($row['check_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$checkNote) + 2;
	$H[0] = ($tmpH > $mH ? $tmpH : $mH);

	//3-2.��ġ����
	$actionNote = StripSlashes($row['action_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$actionNote) + 2;
	$H[1] = ($tmpH > $mH ? $tmpH : $mH);

	if (is_array($memList)){
		foreach($memList as $idx => $mem){
			$memStr .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");

			if ($debug){
				if ($mem['stat'] == '�Ϸ�' || $mem['stat'] == '������'){
					$row['svcporc_yn'] = 'Y';
				}
			}
		}
	}


	$tmpH = $pdf->_getStrY($pdf,$col[2],$memStr) + 2;
	$H[2] = ($tmpH > $rowH ? $tmpH : $rowH);

	$datetime = $myF->dateStyle($row['date'],'.').' '.$myF->timeStyle($row['time']);

	//3-4.Ȯ�μ��û���
	if(!empty($row['write_log_yn'])){
		$checkStr .= "�ٹ������ۼ� : ".($row['write_log_yn'] == 'Y' ? "Y  " : "N  ");
	}else {
		$checkStr .= "�ٹ������ۼ� :     ";
	}
	if(!empty($row['provide_chk_yn'])){
		$checkStr .= "�����ð��ؼ� : ".($row['provide_chk_yn'] == 'Y' ? "Y\n" : "N\n");
	}else {
		$checkStr .= "�����ð��ؼ� :   \n";
	}
	if(!empty($row['right_svc_yn'])){
		$checkStr .= "�������� : ".($row['right_svc_yn'] == 'Y' ? "Y     " : "N     ");
	}else {
		$checkStr .= "�������� :        ";
	}
	if(!empty($row['uniform_yn'])){
		$checkStr .= "���������� : ".($row['uniform_yn'] == 'Y' ? "Y\n" : "N\n");
	}else {
		$checkStr .= "���������� :     \n";
	}


	if($row['house_env_yn'] == '1'){
		$checkStr .= "�ְ�ȯ��û�� : ��  ";
	} else if($row['house_env_yn'] == '2'){
		$checkStr .= "�ְ�ȯ��û�� : ��  ";
	} else if($row['house_env_yn'] == '3'){
		$checkStr .= "�ְ�ȯ��û�� : ��  ";
	} else {
		$checkStr .= "�ְ�ȯ��û�� :      ";
	};

	if($row['work_mind_yn'] == '1'){
		$checkStr .= "�����µ�ģ�� : ��";
	} else if($row['work_mind_yn'] == '2'){
		$checkStr .= "�����µ�ģ�� : ��";
	}else if($row['work_mind_yn'] == '3'){
		$checkStr .= "�����µ�ģ�� : ��\n";
	}else {
		$checkStr .= "�����µ�ģ�� :    \n";
	};

	$tmpH = $pdf->_getStrY($pdf,$col[2],$checkStr) + 2;
	$H[3] = ($tmpH > $mH ? $tmpH : $mH);

	//3-4 �湮�Ͻ�
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$datetime) + 2;
	$H[4] = ($tmpH > $rowH ? $tmpH : $rowH);

	//3-4 ��ȸ�������
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$row['reg_name']) + 2;
	$H[5] = ($tmpH > $rowH ? $tmpH : $rowH);

	//3-4 �湮����
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$row['svcporc_yn']) + 2;
	$H[6] = ($tmpH > $rowH ? $tmpH : $rowH);

	//3-4 �湮�Ұ�����
	$notvisitReason = StripSlashes($row['notvisit_reason']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$notvisitReason) + 2;
	$H[7] = ($tmpH > $rowH ? $tmpH : $rowH);

	//�湮���
	$visitPlace = StripSlashes($row['visit_place']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$visitPlace) + 2;
	$H[8] = ($tmpH > $rowH ? $tmpH : $rowH);



	//3.�޿�����Ȯ��
	//3-1.Ȯ�γ���
	$checkNote2 = StripSlashes($row2['check_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$checkNote2) + 2;
	$H[0] = ($H[0] > ($tmpH > $mH ? $tmpH : $mH) ? $H[0] : ($tmpH > $mH ? $tmpH : $mH));

	//3-2.��ġ����
	$actionNote2 = StripSlashes($row2['action_note']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$actionNote2) + 2;
	$H[1] = ($H[1] > ($tmpH > $mH ? $tmpH : $mH) ? $H[1] : ($tmpH > $mH ? $tmpH : $mH));

	if (is_array($memList2)){
		foreach($memList2 as $idx => $mem){
			$memStr2 .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");

			if ($debug){
				if ($mem['stat'] == '�Ϸ�' || $mem['stat'] == '������'){
					$row2['svcporc_yn'] = 'Y';
				}
			}
		}
	}

	$tmpH = $pdf->_getStrY($pdf,$col[2],$memStr2) + 2;
	$H[2] = ($H[2] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[2] : ($tmpH > $rowH ? $tmpH : $rowH));

	//3-4.Ȯ�μ��û���


	if(!empty($row2['write_log_yn'])){
		$checkStr2 .= "�ٹ������ۼ� : ".($row2['write_log_yn'] == 'Y' ? "Y  " : "N  ");
	}else {
		$checkStr2 .= "�ٹ������ۼ� :     ";
	}
	if(!empty($row2['provide_chk_yn'])){
		$checkStr2 .= "�����ð��ؼ� : ".($row2['provide_chk_yn'] == 'Y' ? "Y\n" : "N\n");
	}else {
		$checkStr2 .= "�����ð��ؼ� :   \n";
	}
	if(!empty($row2['right_svc_yn'])){
		$checkStr2 .= "�������� : ".($row2['right_svc_yn'] == 'Y' ? "Y     " : "N     ");
	}else {
		$checkStr2 .= "�������� :        ";
	}
	if(!empty($row2['uniform_yn'])){
		$checkStr2 .= "���������� : ".($row2['uniform_yn'] == 'Y' ? "Y\n" : "N\n");
	}else {
		$checkStr2 .= "���������� :     \n";
	}


	if($row2['house_env_yn'] == '1'){
		$checkStr2 .= "�ְ�ȯ��û�� : ��  ";
	} else if($row2['house_env_yn'] == '2'){
		$checkStr2 .= "�ְ�ȯ��û�� : ��  ";
	} else if($row2['house_env_yn'] == '3'){
		$checkStr2 .= "�ְ�ȯ��û�� : ��  ";
	} else {
		$checkStr2 .= "�ְ�ȯ��û�� :      ";
	};

	if($row2['work_mind_yn'] == '1'){
		$checkStr2 .= "�����µ�ģ�� : ��";
	} else if($row2['work_mind_yn'] == '2'){
		$checkStr2 .= "�����µ�ģ�� : ��";
	}else if($row2['work_mind_yn'] == '3'){
		$checkStr2 .= "�����µ�ģ�� : ��\n";
	}else {
		$checkStr2 .= "�����µ�ģ�� :    \n";
	};


	$tmpH = $pdf->_getStrY($pdf,$col[2],$checkStr2) + 2;
	$H[3] = ($H[3] > ($tmpH > $mH ? $tmpH : $mH) ? $H[3] : ($tmpH > $mH ? $tmpH : $mH));

	$datetime2 = $myF->dateStyle($row2['date'],'.').' '.$myF->timeStyle($row2['time']);

	//3-4 �湮�Ͻ�
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$datetime2) + 2;
	$H[4] = ($H[4] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[4] : ($tmpH > $rowH ? $tmpH : $rowH));

	//3-4 ��ȸ�������
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$row2['reg_name']) + 2;
	$H[5] = ($H[5] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[5] : ($tmpH > $rowH ? $tmpH : $rowH));

	//3-4 �湮����
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$row['svcporc_yn']) + 2;
	$H[6] = ($H[6] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[6] : ($tmpH > $rowH ? $tmpH : $rowH));

	//3-4 �湮�Ұ�����
	$notvisitReason2 = StripSlashes($row2['notvisit_reason']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$notvisitReason2) + 2;
	$H[7] = ($H[7] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[7] : ($tmpH > $rowH ? $tmpH : $rowH));

	//�湮���
	$visitPlace2 = StripSlashes($row2['visit_place']);
	$tmpH = $pdf->_getStrY($pdf,$col[2]/2,$visitPlace2) + 2;
	$H[8] = ($H[8] > ($tmpH > $rowH ? $tmpH : $rowH) ? $H[8] : ($tmpH > $rowH ? $tmpH : $rowH));


	$totH = 0;
	$totH1 = 0;
	#foreach($H as $i => $tmpH){
	#	$totH += $tmpH;
	#}

	foreach($H as $i => $tmpH){

		if ($i == 3) continue;
		if ($i == 0) $tmpH += $H[3];

		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){

		}else{
			$totH1 += $tmpH;
		}

	}


	if (!$totH1) $totH1 = $totH;

	if ($Y >= $pdf->height - ($H[0]+$H[3])){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY()+6;
		$Yg = $pdf->GetY();
	}



	//3-1.Ȯ�γ���
	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "", 1, 0, "C");
	$pdf->Cell($col[1], $H[0]+$H[3], "Ȯ�γ���", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[3], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[3], "", 1, 1);

	$tmpH = ($totH1 - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
	$pdf->SetXY($X, $Y + $tmpH);
	$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");

	$X1 = $X + $col[0] + $col[1];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $checkStr);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $checkStr2);

	$Y = $Y + $H[3];


	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[2]/2, $H[0], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[0], "", 1, 1);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $checkNote);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $checkNote2);

	$Y = $Y + $H[0];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	if ($Y >= $pdf->height - $H[1]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[1]+$H[2]+$H[4]+$H[5]+$H[6]+$H[7]+$H[8], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[1]+$H[2]+$H[4]+$H[5]+$H[6]+$H[7]+$H[8] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");
	}


	//3-2.��ġ����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[1], "��ġ����", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[1], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[1], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $actionNote);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $actionNote2);

	$Y = $Y + $H[1];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	if ($Y >= $pdf->height - $H[2]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2]+$H[4]+$H[5]+$H[6]+$H[7]+$H[8], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[2]+$H[4]+$H[5]+$H[6]+$H[7]+$H[8] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");
	}

	//3-3.��纸ȣ�� ����
	$pdf->SetXY($X1, $Y);
	if($yymm > '201701'){
		$pdf->Cell($col[1], $H[2], "��纸ȣ�� ����", 1, 0, "C");
	}else {
		$pdf->Cell($col[1], $H[2], "�޿������� ����(��)*", 1, 0, "C");
	}
	
	$pdf->Cell($col[2]/2, $H[2], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[2], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	//$pdf->SetXY($X1, $Y);
	//$pdf->Cell($col[2]/2, $H[2], "�޿����� �� �湮(".($row['svcporc_yn'] == 'Y' ? ' V ' : '    ').")", 0, 0, "R");

	//$pdf->SetXY($X2, $Y);
	//$pdf->Cell($col[2]/2, $H[2], "�޿����� �� �湮(".($row2['svcporc_yn'] == 'Y' ? ' V ' : '    ').")", 0, 0, "R");

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $memStr);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $memStr2);

	$Y = $Y + $H[2];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0]+$col[2]/2;

	if ($Y >= $pdf->height - $H[4]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[4]+$H[5]+$H[6]+$H[7]+$H[8], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[4]+$H[5]+$H[6]+$H[7]+$H[8] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");

	}

	//3-4.�湮�Ͻ�
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[4], "�湮�Ͻ�", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[4], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[4], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $datetime);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $datetime2);

	$Y = $Y + $H[4];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[2]/2;

	if ($Y >= $pdf->height - $H[5]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[5]+$H[6]+$H[7]+$H[8], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[5]+$H[6]+$H[7]+$H[8] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");
	}

	//3-4.��ȸ������ ����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[5], "��ȸ������ ����", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[5], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[5], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $row['reg_name']);

	$pdf->SetXY($X2, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $row2['reg_name']);

	$Y = $Y + $H[5];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[2]/2;

	if ($Y >= $pdf->height - $H[6]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[6]+$H[7], "�޿�����Ȯ��", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[6]+$H[7] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");
	}

	//3-4.�޿����� �� �湮����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[6], "�޿����� �� �湮����", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[6], $row['svcporc_yn'] == 'Y' ? ' V ' : '    ', 1, 0,'L');
	$pdf->Cell($col[2]/2, $H[6], $row2['svcporc_yn'] == 'Y' ? ' V ' : '    ', 1, 1,'L');

	$X1 = $X + $col[0] + $col[1];
	$X2 = $X + $col[0] + $col[1]+$col[2]/2;

	$Y = $Y + $H[6];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[2]/2;

	if ($Y >= $pdf->height - $H[7]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$pdf->SetX($X);
		$pdf->Cell($col[0], $H[2], "", 1, 0, "C");
		$Y = $pdf->GetY();

		$tmpH = ($H[2] - $pdf->_getStrY($pdf,$col[0],"�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��")) / 2;
		$pdf->SetXY($X, $Y + $tmpH);
		$pdf->MultiCell($col[0], 4, "�޿� ��\n����Ȱ��\n���α׷�\n����Ȯ��", 0, "C");
	}

	//�湮���
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[4], "�湮���", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[7], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[7], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2], 4, $visitPlace);


	$Y = $Y + $H[4];
	$X1 = $X + $col[0];

	//3-4.�湮�Ұ� ����
	$pdf->SetXY($X1, $Y);
	$pdf->Cell($col[1], $H[7], "�湮�Ұ� ����", 1, 0, "C");
	$pdf->Cell($col[2]/2, $H[7], "", 1, 0);
	$pdf->Cell($col[2]/2, $H[7], "", 1, 1);

	$X1 = $X + $col[0] + $col[1];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $notvisitReason);

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[2]/2, 4, $notvisitReason2);


	$Y = $Y + $H[7];
	$X1 = $X + $col[0];
	$X2 = $X + $col[0] + $col[2]/2;


	Unset($H);


	//4.����
	//4-1.����
	$commentStr = StripSlashes($info['comment']);
	$tmpH = $pdf->_getStrY($pdf,$col[1]+$col[2],$commentStr) + 2;
	$H[0] = ($tmpH > $mH ? $tmpH : $mH);


	$totH = 0;
	$totH1 = 0;

	foreach($H as $i => $tmpH){
		$totH += $tmpH;

		if ($Y >= $pdf->height - $totH){
		}else{
			$totH1 += $tmpH;
		}
	}

	if (!$totH1) $totH1 = $totH;


	if ($Y >= $pdf->height - $H[0]){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor, '', 9);
		$Y = $pdf->GetY();
	}


	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $totH1, "����", 1, 0, "C");
	$pdf->Cell($col[1]+$col[2], $H[0], "", 1, 1);

	$X1 = $X + $col[0];

	$pdf->SetXY($X1, $Y + 1);
	$pdf->MultiCell($col[1]+$col[2], 4, $commentStr);


	Unset($row);

	include_once('../inc/_db_close.php');

?>
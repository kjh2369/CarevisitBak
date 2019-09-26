<?
	if (!Is_Array($var)){
		exit;
	}

	$name = $myF->euckr($var['name']);

	//���ϸ���Ʈ
	$sql = 'SELECT	mdate AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6)	= \''.$var['year'].$var['month'].'\'';

	$arrHoliday = $conn->_fetch_array($sql,'date');

	if ($var['month'] == '05'){
		$arrHoliday[$var['year'].$var['month'].'01']['name']	= '�ٷ����ǳ�';
	}

	//������
	$sql = 'SELECT	DISTINCT
					m02_yjumin AS jumin
			,		m02_yname AS name
			,		m02_ytel AS phone
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$var['code'].'\'';

	$arrMem = $conn->_fetch_array($sql,'jumin');

	//����
	$sql = 'SELECT	DISTINCT
					m03_jumin AS jumin
			,		m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$var['code'].'\'';

	$arrClt = $conn->_fetch_array($sql,'jumin');

	//����
	$sql = 'SELECT	DISTINCT
					jumin AS jumin
			,		m02_yname AS name
			,		m02_ytel AS phone
			,		iljung_jumin AS cd
			,		iljung_dt AS date
			,		iljung_from AS from_time
			,		iljung_to AS to_time
			,		iljung_proc AS proc_time
			,		iljung_stat AS stat
			FROM	care_counsel_iljung AS iljung
			INNER	JOIN m02yoyangsa
					ON	m02_ccode = iljung.org_no
					AND	m02_yjumin = iljung.jumin
			WHERE	iljung.org_no = \''.$var['code'].'\'
			AND		iljung.iljung_sr = \''.$var['sr'].'\'';

	if ($var['jumin']){
		$sql .= ' AND	iljung.jumin = \''.$var['jumin'].'\'';
	}

	$sql .= '
			AND		LEFT(iljung.iljung_dt,6) = \''.$var['year'].$var['month'].'\'
			AND		iljung.del_flag = \'N\'
			ORDER	BY jumin,date,from_time';

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	//����ǥ(��)
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$jumin = $row['jumin'];
		$name = $row['name'];
		$phone = $myF->phoneStyle($row['phone'],'.');
		$client = $arrClt[$row['cd']]['name'];
		$time = $myF->timeStyle($row['from_time']).'~'.$myF->timeStyle($row['to_time']);
		$sugaNm = '�������';

		$day = IntVal(Date('d',StrToTime($row['date'])));

		if ($tmpDay != $day){
			$tmpDay  = $day;
			$liSeq   = 0;
		}

		//������ Ȯ��(�ֹι�ȣ/����/����/����)
		$arrCaln[$jumin][$day][$liSeq] = Array(
				'client'=>$client
			,	'time'=>$time
			,	'proc'=>$row['proc_time']
			,	'sugaNm'=>$sugaNm
		);

		$liSeq ++;
	}

	$conn->row_free();

	//��/���� ���� ����
	$col['infoWidth'][0]	= $pdf->width*0.20;
	$col['infoWidth'][1]	= $pdf->width*0.25;
	$col['infoWidth'][2]	= $pdf->width*0.25;
	$col['infoWidth'][3]	= $pdf->width*0.30;

	//����ǥ
	$col['calnWidth'][0]	= $pdf->width*0.1428;
	$col['calnWidth'][1]	= $pdf->width*0.1428;
	$col['calnWidth'][2]	= $pdf->width*0.1428;
	$col['calnWidth'][3]	= $pdf->width*0.1428;
	$col['calnWidth'][4]	= $pdf->width*0.1428;
	$col['calnWidth'][5]	= $pdf->width*0.1428;
	$col['calnWidth'][6]	= $pdf->width*0.1428;

	$col['calnWeek'][0]		= '��';
	$col['calnWeek'][1]		= '��';
	$col['calnWeek'][2]		= 'ȭ';
	$col['calnWeek'][3]		= '��';
	$col['calnWeek'][4]		= '��';
	$col['calnWeek'][5]		= '��';
	$col['calnWeek'][6]		= '��';

	//���� ���� ����
	$calTime	= mktime(0, 0, 1, $pdf->month, 1, $pdf->year);
	$today		= date('Ymd', mktime());
	$lastDay	= date('t', $calTime);										//���ϼ� ���ϱ�
	$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//���ۿ��� ���ϱ�
	$totalWeek	= ceil(($lastDay + $startWeek) / 7);						//�� �� ������ ���ϱ�
	$lastWeek	= date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay));	//������ ���� ���ϱ�

	$height = $pdf->row_height;

	//�ֺ� ����
	foreach($arrCaln as $jumin => $caln1){
		foreach($arrCaln[$jumin] as $date => $caln2){
			$day = 1;

			for($i=1; $i<=$totalWeek; $i++){
				for($j=0; $j<7; $j++){
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						$liCnt = SizeOf($arrCaln[$jumin][$day]);

						if ($liCheckCnt[$jumin][$i] < $liCnt){
							$liCheckCnt[$jumin][$i] = $liCnt;
						}

						$day++;
					}
				}
			}
		}
	}

	foreach($arrCaln as $jumin => $caln){
		$pdf->SetFontSize(10);

		if ($tmpJumin != $jumin){
			if (!Empty($tmpJumin)){
				$pdf->AddPage(strtoupper($var['dir']), 'A4');
			}

			$tmpJumin = $jumin;
		}

		//��������
		$col['infoTitle'][0] = "������";
		$col['infoTitle'][1] = "����ó";
		$col['infoTitle'][2] = "";
		$col['infoTitle'][3] = "";

		$col['clientValue'][0] = $arrMem[$jumin]['name'];
		$col['clientValue'][1] = $myF->phoneStyle($arrMem[$jumin]['phone'],'.');

		$pdf->SetXY($pdf->left, $pdf->GetY());

		$liTop = $pdf->GetY();

		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetFillColor(220,220,220);
		for($i=0; $i<sizeOf($col['infoTitle']); $i++){
			$pdf->Cell($col['infoWidth'][$i], $pdf->row_height, $col['infoTitle'][$i], 1, $i == sizeOf($col['infoTitle']) - 1 ? 1 : 0, 'C', true);
		}

		$pdf->SetFont($pdf->font_name_kor,'',11);
		$pdf->SetX($pdf->left);
		for($i=0; $i<sizeOf($col['infoTitle']); $i++){
			$pdf->Cell($col['infoWidth'][$i], $pdf->row_height, $col['clientValue'][$i], 1, $i == sizeOf($col['infoTitle']) - 1 ? 1 : 0, 'C');
		}

		// �׵θ�
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $liTop, $pdf->width, $pdf->row_height * 2);
		$pdf->SetLineWidth(0.2);

		$pdf->Cell($pdf->width, 3, "", 0, 1);

		$liFirstY = $pdf->GetY();	//�׵θ� ���� ����
		$liLastY  = 0;				//�׵θ� ���� ����

		$pdf->SetXY($pdf->left,$pdf->GetY());
		//$pdf->SetFont($pdf->font_name_kor,'B',9);

		$day = 1; //ȭ�鿡 ǥ���� ȭ���� �ʱⰪ�� 1�� ����

		for($i=1; $i<=$totalWeek; $i++){
			if ($i == 1){
				lfGetWeekString($pdf,$col,$height);
			}

			$pdf->SetX($pdf->left);
			$pdf->SetTextColor(0,0,0);

			$liGbnH = 2;

			//�����
			$liHg = $height * 0.75;
			$liH = (($liCheckCnt[$jumin][$i]) * $liGbnH * $liHg) + $height;

			if ($liH <= $height){
				$liH  = $liHg * 4;
			}

			$liHg = $height * 0.7;

			if (Empty($liLastY)){
				$liLastY = $height;
			}

			if ($pdf->GetY()+$liH > $pdf->height){
				$pdf->SetLineWidth(0.6);
				$pdf->Rect($pdf->left, $liFirstY, $pdf->width, $liLastY);
				$pdf->SetLineWidth(0.2);

				$pdf->AddPage(strtoupper($var['dir']), 'A4');
				lfGetWeekString($pdf,$col,$height);

				$liFirstY = $pdf->GetY() - $height;
				$liLastY  = $height;
			}

			$liLastY += $liH;

			//�� ����ĭ �����
			for($j=0; $j<7; $j++){
				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
					$date = $var['year'].$var['month'].($day < 10 ? '0' : '').$day;

					if ($j == 0){//�Ͽ���
						$pdf->SetTextColor(255,0,0); //������
					}else if ($j == 6){//�����
						$pdf->SetTextColor(0,0,255); //�Ķ���
					}else{//����
						$pdf->SetTextColor(0,0,0); //������
					}

					//�����
					if (!Empty($arrHoliday[$date]['name'])){
						if ($date == $var['year'].$var['month'].'01'){
							$pdf->SetTextColor(0,0,255); //�Ķ���
						}else{
							$pdf->SetTextColor(255,0,0); //������
						}
					}

					//��������
					$pdf->Cell($col['calnWidth'][$j], $liH, "", 1, 0, 'L');

					$liX = $pdf->GetX();
					$liY = $pdf->GetY();

					$pdf->SetX($liX - $col['calnWidth'][$j]);

					//����
					$pdf->Cell($col['calnWidth'][$j]*0.1, $height, Number_Format($day), 0, 0, 'L');

					//�����
					$pdf->Cell($col['calnWidth'][$j]*0.9, $height, $arrHoliday[$date]['name'], 0, 2, 'R');

					//�⺻���ڻ�
					$pdf->SetTextColor(0,0,0); //������

					if (Is_Array($arrCaln[$jumin][$day])){
						foreach($arrCaln[$jumin][$day] as $seq => $caln){
							$pdf->SetX($liX - $col['calnWidth'][$j]);

							//���񽺸�
							//$svcNm = '�������';

							//����
							//$pdf->Cell($col['calnWidth'][$j], $liHg, $pdf->_splitTextWidth($myF->utf($svcNm),$col['calnWidth'][$j]), 0, 2, 'L');

							//�ٹ��ð�
							$pdf->Cell($col['calnWidth'][$j], $liHg, $caln['time'], 0, 2, 'L');

							//����
							$pdf->Cell($col['calnWidth'][$j], $liHg, $pdf->_splitTextWidth($myF->utf($caln['client']),$col['calnWidth'][$j]), 0, 2, 'R');
						}
					}

					$pdf->SetXY($liX,$liY);
					$pdf->SetTextColor(0,0,0);

					$day ++;
				}else{
					$pdf->Cell($col['calnWidth'][$j], $liH, "", 1, $j < 6 ? 0 : 1, 'C');
				}
			}

			$pdf->SetXY($pdf->left,$pdf->GetY() + $liH);
		}

		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $liFirstY, $pdf->width, $liLastY);
		$pdf->SetLineWidth(0.2);
	}

	function lfGetWeekString($pdf,$col,$height){
		//����
		$pdf->SetX($pdf->left);
		$pdf->SetFont($pdf->font_name_kor,'B',10);
		for($j=0; $j<7; $j++){
			if ($j == 0){//�Ͽ���
				$pdf->SetTextColor(255,0,0); //������
			}else if ($j == 6){//�����
				$pdf->SetTextColor(0,0,255); //�Ķ���
			}else{//����
				$pdf->SetTextColor(0,0,0); //������
			}
			$pdf->Cell($col['calnWidth'][$j], $height, $col['calnWeek'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
		}
		$pdf->SetFont($pdf->font_name_kor,'',10);
		$pdf->SetFillColor(238,238,238);
		$pdf->SetX($pdf->left);
	}
?>
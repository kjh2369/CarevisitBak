<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	require('../pdf/pdf_cal_table.php');

	$conn->set_name('euckr');

	$code		= $_GET['code'];
	$kind		= $_GET['kind'];
	$year		= $_GET['year'];
	$month		= $_GET['month'];
	$type		= $_GET['type'];
	$useType	= $_GET['useType'];

	if ($_GET['target'] == 'all'){
	}else if (!is_numeric($_GET['target'])){
		$target = $ed->de($_GET['target']); //iconv("EUC-KR","UTF-8",$_GET['target'])
	}else{
		if ($type == 's'){
			$sql = "select m03_jumin
					  from m03sugupja
					 where m03_ccode = '".$code."'
					   and m03_mkind = '".$kind."'
					   and m03_key   = '".$_GET['target']."'";
		}else{
			$sql = "select m02_yjumin
					  from m02yoyangsa
					 where m02_ccode = '".$code."'
					   and m02_mkind = '".$kind."'
					   and m02_key   = '".$_GET['target']."'";
		}
		$target = $conn->get_data($sql);
	}

	//if (strLen($target) == 0) $target = $ed->de($_GET['target']);

	// ��������
	$sql = "select m00_cname, m00_ctel
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$row = $conn->get_array($sql);
	$centerName = $row[0];
	$centerTel	= $myF->phoneStyle($row[1]);

	$pdf = new MYPDF('P');
	$pdf->AddUHCFont('����','Gulim');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	// ����� ����
	$pdf->year			= $year;		//��
	$pdf->month			= $month;		//��
	$pdf->type			= $type;		//������, ��纸ȣ�� ����
	$pdf->useType		= $useType;		//������, ���� ����
	$pdf->centerName	= $centerName;	//���͸�
	$pdf->centerTel		= $centerTel;	//������ȭ��ȣ

	if ($type == 's'){
		$sql = "select m03_name
				,      m03_injung_no
				,      LVL.m81_name
				,      STP.m81_name
				,      m03_bonin_yul
				  from m03sugupja
				 inner join m81gubun as LVL
					on LVL.m81_gbn  = 'LVL'
				   and LVL.m81_code = m03_ylvl
				 inner join m81gubun as STP
					on STP.m81_gbn  = 'STP'
				   and STP.m81_code = m03_skind
				 where m03_ccode = '$code'
				   and m03_mkind = '$kind'
				   and m03_jumin = '$target'";
		$row = $conn->get_array($sql);

		$pdf->name	= $row[0];
		$pdf->jumin	= $myF->issStyle($target);
		$pdf->no	= $row[1];
		$pdf->level	= $row[2];
		$pdf->rate	= $row[3].' / '.$row[4];
	}else{
		$sql = "select m02_yname, m02_ycode, m02_ytel
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_mkind  = '$kind'
				   and m02_yjumin = '$target'";
		$row = $conn->get_array($sql);

		$pdf->name	= $row[0];
		$pdf->jumin	= $row[1];
		$pdf->no	= $myF->phoneStyle($row[2]);
	}

	// ������ �߰�
	$pdf->AddPage('P', 'A4');

	// ���� ���� ����
	$calTime   = mkTime(0, 0, 1, $pdf->month, 1, $pdf->year);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //���ϼ� ���ϱ�
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //���ۿ��� ���ϱ�
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //�� �� ������ ���ϱ�
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //������ ���� ���ϱ�

	$date = $pdf->year.$pdf->month;

	// �Ϻ� ����Ÿ �ʱ�ȭ
	for($i=1; $i<=$lastDay; $i++){
		$cal[$i][0]['service']	= ''; //���񽺸�
		$cal[$i][0]['time']		= ''; //��ȹ�ð�
		$cal[$i][0]['cost']		= ''; //�ҿ�ð�
		$cal[$i][0]['worker']	= ''; //��纸ȣ��
	}

	// �Ϻ� ����Ÿ
	if ($type == 's'){
		$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
				,      date_format(concat(t01_sugup_date, t01_sugup_fmtime,'00'), '%H:%i')
				,      date_format(concat(t01_sugup_date, t01_sugup_totime,'00'), '%H:%i')
				,      t01_sugup_soyotime
				,      case t01_svc_subcode when '200' then '�湮���' when '500' then '�湮���' when '800' then '�湮��ȣ' else '-' end
				,      t01_yname1
				,      concat(case when t01_yname2 != '' then '/' else '' end, left(t01_yname2, 3))
				  from t01iljung
				 where t01_ccode  = '$code'
				   and t01_mkind  = '$kind'
				   and t01_jumin  = '$target'
				   and t01_sugup_date like '$date%'
				   and t01_del_yn = 'N'
				 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	}else{
		$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
				,      date_format(concat(t01_sugup_date, t01_sugup_fmtime,'00'), '%H:%i')
				,      date_format(concat(t01_sugup_date, t01_sugup_totime,'00'), '%H:%i')
				,      t01_sugup_soyotime
				,      case t01_svc_subcode when '200' then '���' when '500' then '���' when '800' then '��ȣ' else '-' end
				,      m03_name
				,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(��)' else '(��)' end else '' end
				  from t01iljung
				 inner join m03sugupja
				    on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 where t01_ccode = '$code'
				   and t01_mkind = '$kind'
				   and '$target' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				   and t01_sugup_date like '$date%'
				   and t01_del_yn = 'N'
				 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	}

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	$day = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($day != $row[0]){
			$day = $row[0];
			$seq = 0;
		}

		$cal[$day][$seq]['service']	= $row[4];
		$cal[$day][$seq]['cost']	= $row[3];

		if ($type == 's'){
			$cal[$day][$seq]['time']	= $row[1].'~'.$row[2];
			$cal[$day][$seq]['worker']	= $row[5].$row[6];
		}else{
			$cal[$day][$seq]['time']	= $row[1].'~'.$row[2].$row[6];
			$cal[$day][$seq]['worker']	= $row[5];
		}

		$seq ++;
	}
	$conn->row_free();

	$day = 1;
	$height	= $pdf->rowHeight;

	// ������ ���̸� ����Ѵ�.
	for($i=1; $i<=$totalWeek; $i++){
		for($j=0; $j<7; $j++){
			$checkTop[$i] = 0;
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if (sizeOf($cal[$day]) > 0){
					for($k=0; $k<sizeOf($cal[$day]); $k++){
						if (strLen($cal[$day][$k]['service']) != ''){
							if ($type == 's'){
								$checkTop[$i] += ($height * 2);
							}else{
								$checkTop[$i] += ($height * 1.5);
							}
						}
					}
				}
				$day++;
			}
		}
	}

	//print_r($checkTop);

	$col = $pdf->calranderColWidth();

	$top	= $pdf->top+15;
	$left	= $pdf->left;
	$height	= $pdf->rowHeight;

	$pdf->SetXY($left, $top);

	$day = 1; //ȭ�鿡 ǥ���� ȭ���� �ʱⰪ�� 1�� ����
	for($i=1; $i<=$totalWeek; $i++){
		// ����
		if ($i == 1){
			$pdf->SetFont('����','B',9);
			for($j=0; $j<7; $j++){
				$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
			}
			$pdf->SetFont('����','',9);
			$top += $height;
			$pdf->SetFillColor(238,238,238);
		}

		$left = $pdf->left;

		// �� ����ĭ �����
		for($j=0; $j<7; $j++){
			$tempTop[$j] = 0;
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if (sizeOf($cal[$day]) > 0){
					// ������ ���̿� ��ǥ�� �ٽ� ���
					$checkHeight = $top + $height + $checkTop[$i] + $height * ($type == 's' ? 2 : 1.5) + $height + 3;

					// ������ ���̰� �������� �Ѿ�� �������� �߰��Ѵ�.
					if ($checkHeight >= $pdf->height){
						// ���Ϻ� ����
						drawLine($pdf, $col, $top);

						// �׵θ�
						drawBorder($pdf, $top-$height*6);

						$pdf->AddPage('P', 'A4');
						$pdf->SetX($pdf->left);
						$top = $pdf->top+15;
						$tempTop[$j] = 0;
					}

					for($k=0; $k<sizeOf($cal[$day]); $k++){
						if ($k == 0){
							$pdf->SetXY($left, $top);
							$pdf->SetFont('����','B',9);
							$pdf->Cell($col['w'][$j], $height-0.5, "$day", "T", 1, 'L', true);
							$pdf->SetFont('����','',8);
						}

						if (strLen($cal[$day][$k]['service']) != ''){
							$pdf->SetXY($left, $top + $height + $tempTop[$j]);

							if ($type == 's'){
								$pdf->MultiCell($col['w'][$j], 4, "[".$cal[$day][$k]['service']."]\n".$cal[$day][$k]['time']."\n".$cal[$day][$k]['worker']);
								$tempTop[$j] += ($height * 2);
							}else{
								$pdf->MultiCell($col['w'][$j], 4, "[".$cal[$day][$k]['service']."]".$cal[$day][$k]['worker']."\n".$cal[$day][$k]['time']);
								$tempTop[$j] += ($height * 1.5);
							}
						}
					}
				}else{
					$pdf->SetXY($left, $top);
					$pdf->Cell($col['w'][$j], $height, " ", 1, 1);
				}
				$left += $col['w'][$j];
				$day ++;
			}else{
				$pdf->SetXY($left, $top);
				$pdf->Cell($col['w'][$j], $height, " ", "T", 1);
				$left += $col['w'][$j];
			}
		}

		$tempHeight = 0;
		for($j=0; $j<7; $j++){
			if ($tempHeight < $tempTop[$j]) $tempHeight = $tempTop[$j];
		}

		if ($tempHeight > 0){
			$top += ($height + $tempHeight);
		}else{
			$top += ($height * 2.5);
		}
	}

	$cal = array();

	// ���Ϻ� ����
	drawLine($pdf, $col, $top);

	// �׵θ�
	drawBorder($pdf, $top-$height*6);

	if ($type == 's'){
		if ($pdf->height - $pdf->GetY() > 50){
		}else{
			// ������ �߰�
			$pdf->AddPage('P', 'A4');
			$top = 33;
		}
	}

	// ������ ����
	$pdf->SetFillColor(200,200,200);

	if ($type == 's'){
		// ��纸ȣ��
		$sql = "select distinct m02_yname, m02_ytel
				  from (
					   select t01_yoyangsa_id1 as yoy
						 from t01iljung
						where t01_ccode  = '$code'
						  and t01_mkind  = '$kind'
						  and t01_jumin  = '$target'
						  and t01_sugup_date like '$date%'
						  and t01_del_yn = 'N'
						union all
					   select t01_yoyangsa_id2 as yoy
						 from t01iljung
						where t01_ccode  = '$code'
						  and t01_mkind  = '$kind'
						  and t01_jumin  = '$target'
						  and t01_sugup_date like '$date%'
						  and t01_del_yn = 'N'
					   ) as y
				 inner join m02yoyangsa
					on m02_ccode = '$code'
				   and m02_mkind = '$kind'
				   and m02_yjumin = yoy
				 order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		$pdf->SetFont('����','',10);
		$pdf->SetXY($pdf->left, $top+2);

		$yoyInfo = '';

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$yoyInfo .= ($yoyInfo != '' ? '  /  ' : '').$row[0].'('.$myF->phoneStyle($row[1],'.').')';
		}

		$pdf->Cell($pdf->width, $height+2, " ��� ��纸ȣ�� : $yoyInfo", 1, 1, 'L', true);

		$conn->row_free();
	}else{
		$pdf->SetY($top);
	}

	// �������񽺳���
	if ($type == 's'){
		$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
				,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i')
				,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i')
				,      t01_yname1
				,      t01_yname2
				,      m01_suga_cont
				  from t01iljung
				 inner join (
					   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
						 from m01suga
						where m01_mcode = '$code'
						union all
					   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
						 from m11suga
						where m11_mcode = '$code'
					   ) as suga
					on t01_suga_code1 = m01_mcode2
				   and t01_sugup_date between m01_sdate and m01_edate
				 where t01_ccode  = '$code'
				   and t01_mkind  = '$kind'
				   and t01_jumin  = '$target'
				   and t01_sugup_date like '$date%'
				   and t01_del_yn = 'N'
				 order by t01_sugup_fmtime, t01_sugup_totime, t01_sugup_date";
	}else{
		$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
				,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i')
				,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i')
				,      m03_name
				,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(��)' else '(��)' end else '' end
				,      m01_suga_cont
				  from t01iljung
				 inner join m03sugupja
				    on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 inner join (
					   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
						 from m01suga
						where m01_mcode = '$code'
						union all
					   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
						 from m11suga
						where m11_mcode = '$code'
					   ) as suga
					on t01_suga_code1 = m01_mcode2
				   and t01_sugup_date between m01_sdate and m01_edate
				 where t01_ccode  = '$code'
				   and t01_mkind  = '$kind'
				   and '$target' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				   and t01_sugup_date like '$date%'
				   and t01_del_yn = 'N'
				 order by t01_sugup_fmtime, t01_sugup_totime, t01_sugup_date";
	}
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	$pdf->SetFont('����','B',9);
	$pdf->SetXY($pdf->left, $pdf->GetY()+2);

	$pdf->Cell(15,	$height, "�����ð�", 1, 0, 'C', true);
	$pdf->Cell(25,	$height, $type == 's' ? "��纸ȣ��" : "������", 1, 0, 'C', true);
	$pdf->Cell(132, $height, "��������/������", 1, 0, 'C', true);
	$pdf->Cell(10,	$height, "Ƚ��", 1, 1, 'C', true);

	$pdf->SetFont('����','',9);

	$tempDate = '';
	$seq = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tempData != $row[1].'_'.$row[2]){
			$tempData  = $row[1].'_'.$row[2];

			$svc[$seq]['start']	= $row[1];
			$svc[$seq]['end']	= $row[2];
			$svc[$seq]['yoy1']	= $row[3];
			$svc[$seq]['yoy2']	= $row[4];
			$svc[$seq]['svc']	= $row[5];
			$svc[$seq]['count']	= 0;
			$svc[$seq]['days']	= '/';
			$seq ++;
		}
		$svc[$seq-1]['count'] ++;
		//$svc[$seq-1]['days'] .= ($svc[$seq-1]['days'] != '' ? ',' : '').$row[0];
		$svc[$seq-1]['days'] .= $row[0].'/';
	}

	$conn->row_free();

	$height = 5;

	for($i=0; $i<sizeOf($svc); $i++){
		$pdf->SetTextColor(0,0,0);
		$pdf->SetX($pdf->left);

		if ($pdf->GetY()+$height> $pdf->height){
			$pdf->AddPage('P', 'A4');
			$pdf->SetX($pdf->left);
			$pdf->Cell(15,	$height, "�����ð�",								1, 0, 'C', true);
			$pdf->Cell(25,	$height, $type == 's' ? "��纸ȣ��" : "������",	1, 0, 'C', true);
			$pdf->Cell(132, $height, "��������/������",						1, 0, 'C', true);
			$pdf->Cell(10,	$height, "Ƚ��",									1, 1, 'C', true);
			$pdf->SetX($pdf->left);
			$top = 33;
		}

		$pdf->Cell(15,	$height,	$svc[$i]['start'],	"LTR",	0, 'L');

		$Y = $pdf->GetY()+$height;

		if ($type == 's'){
			if ($svc[$i]['yoy2'] == ''){
				$pdf->Cell(25,	$height*2,	$svc[$i]['yoy1'],	"LTR",	0, 'L');
			}else{
				$pdf->Cell(25,	$height,	$svc[$i]['yoy1'],	"LTR",	0, 'L');
			}
		}else{
			$pdf->Cell(25,	$height*2,	$svc[$i]['yoy1'].$svc[$i]['yoy2'],	"LTR",	0, 'L');
		}

		$pdf->Cell(132,	$height,	$svc[$i]['svc'],	"LTR",	0, 'L');
		$pdf->Cell(10,	$height*2,	number_format($svc[$i]['count']),	1,	1, 'C');
		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell(15,	$height,	'~'.$svc[$i]['end'],	"LBR",	0, 'L');

		if ($type == 's'){
			$pdf->Cell(25,	$height,	($svc[$i]['yoy2'] != '' ? ', ' : '').$svc[$i]['yoy2'],	"LBR",	0, 'L');
		}else{
			$pdf->Cell(25,	$height,	"",	"LBR",	0, 'L');
		}
		//$pdf->Cell(112,	$height,	$svc[$i]['days'],	"LBR",	1, 'L');

		for($j=1; $j<=31; $j++){
			if (strVal(strPos($svc[$i]['days'], "/$j/")) == ''){
				$pdf->SetTextColor(220,220,220);
			}else{
				$pdf->SetTextColor(0,0,0);
			}

			if ($j < 10){
				$cellWidth = 3.5;
			}else if ($j == $lastDay){
				$cellWidth = 5;
			}else{
				$cellWidth = 4.55;
			}

			if ($j <= $lastDay){
				if ($j == 1){
					$pdf->Cell($cellWidth, $height, "$j", "LB", 0, 'C');
				}else if ($j == 31){
					$pdf->Cell($cellWidth, $height, "$j", "RB", 1, 'C');
				}else{
					$pdf->Cell($cellWidth, $height, "$j", "B", 0, 'C');
				}
			}else{
				if ($j == 31){
					$pdf->Cell($cellWidth, $height, "", "RB", 1, 'C');
				}else{
					$pdf->Cell($cellWidth, $height, "", "B", 0, 'C');
				}
			}
		}
	}

	$pdf->SetTextColor(0,0,0);

	if ($useType == 'y'){
		// �����ڿ� ��ºκ�
		if ($pdf->GetY()+$height> $pdf->height){
			$pdf->AddPage('P', 'A4');
			$top = 35;
		}else{
			$top = $pdf->GetY()+2;
		}

		if ($type == 's'){
			// ������
			$pdf->SetXY($pdf->left, $top);
			$pdf->Cell(22,	$height, "�޿�����",				1, 0, 'C', true);
			$pdf->Cell(70,	$height, "����(���񽺸� / Ƚ��)", 1, 0, 'C', true);
			$pdf->Cell(30,	$height, "����",					1, 0, 'C', true);
			$pdf->Cell(30,	$height, "��޿����",				1, 0, 'C', true);
			$pdf->Cell(30,	$height, "���κδ��",				1, 1, 'C', true);

			$sql = "select case t01_svc_subcode when '200' then '�湮���'
										when '500' then '�湮���'
										when '800' then '�湮��ȣ' else '-' end
					,      m01_suga_cont
					,      count(t01_suga_code1)
					,      t01_suga_tot
					,      sum(t01_suga_tot)
					,      case when t01_bipay_umu = 'Y' then t01_suga_tot else 0 end + case when t01_bipay_umu = 'Y' then 0 else (t01_suga_tot * m03_bonin_yul / 100) * count(t01_suga_code1) end
					  from t01iljung
					 inner join (
						   select m03_sdate, m03_edate, m03_bonin_yul
							 from m03sugupja
							where m03_ccode = '$code'
							  and m03_mkind = '$kind'
							  and m03_jumin = '$target'
							union all
						   select m31_sdate, m31_edate, m31_bonin_yul
							 from m31sugupja
							where m31_ccode = '$code'
							  and m31_mkind = '$kind'
							  and m31_jumin = '$target'
						   ) as sugupja
						on t01_sugup_date between m03_sdate and m03_edate
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on t01_suga_code1 = m01_mcode2
					   and t01_sugup_date between m01_sdate and m01_edate
					 where t01_ccode = '$code'
					   and t01_mkind = '$kind'
					   and t01_jumin = '$target'
					   and t01_sugup_date like '$date%'
					   and t01_del_yn = 'N'
					 group by t01_svc_subcode, m01_suga_cont, t01_suga_code1, t01_suga_tot
					 order by t01_svc_subcode";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$pdf->SetX($pdf->left);
				$pdf->Cell(22,	$height, $row[0],	1, 0, 'C');
				$pdf->Cell(55,	$height, $row[1],	"LTB", 0, 'L');
				$pdf->Cell(15,	$height, $row[2],	"RTB", 0, 'R');
				$pdf->Cell(30,	$height, number_format($row[3]),	1, 0, 'R');
				$pdf->Cell(30,	$height, number_format($row[4]),	1, 0, 'R');
				$pdf->Cell(30,	$height, number_format($myF->cutOff($row[5])),	1, 1, 'R');
			}
			$conn->row_free();
		}else{
			// ��纸ȣ��
			$pdf->SetXY($pdf->left, $top);
			$pdf->Cell(22,	$height, "�޿�����",				1, 0, 'C', true);
			//$pdf->Cell(30,	$height, "������",					1, 0, 'C', true);
			$pdf->Cell(100,	$height, "����(���񽺸� / Ƚ��)", 1, 0, 'C', true);
			$pdf->Cell(30,	$height, "����",					1, 0, 'C', true);
			$pdf->Cell(30,	$height, "��޿����",				1, 1, 'C', true);

			$sql = "select case t01_svc_subcode when '200' then '�湮���'
												when '500' then '�湮���'
												when '800' then '�湮��ȣ' else '-' end
					/*,      m03_name*/
					,      m01_suga_cont
					,      count(t01_suga_code1)
					,      t01_suga_tot
					,      sum(t01_suga_tot)
					  from t01iljung
					 /*
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 */
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on t01_suga_code1 = m01_mcode2
					   and t01_sugup_date between m01_sdate and m01_edate
					 where t01_ccode = '$code'
					   and t01_mkind = '$kind'
					   and t01_sugup_date like '$date%'
					   and '$target' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					   and t01_del_yn = 'N'
					 group by t01_svc_subcode/*, m03_name*/, t01_suga_code1
					 order by t01_svc_subcode/*, m03_name*/, m01_suga_cont";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$pdf->SetX($pdf->left);
				$pdf->Cell(22,	$height, $row[0],	1, 0, 'C');
				//$pdf->Cell(30,	$height, $row[1],	1, 0, 'L');
				$pdf->Cell(85,	$height, $row[1],	"LTB", 0, 'L');
				$pdf->Cell(15,	$height, $row[2],	"RTB", 0, 'R');
				$pdf->Cell(30,	$height, number_format($row[3]),	1, 0, 'R');
				$pdf->Cell(30,	$height, number_format($row[4]),	1, 1, 'R');
			}
			$conn->row_free();
		}
	}

	$pdf->Output();

	include('../inc/_db_close.php');

	// �޷��� ���Ϻ� ������ �׸���.
	function drawLine($pdf, $col, $top){
		$pdf->SetLineWidth(0.2);

		$left = $pdf->left;

		for($i=0; $i<7; $i++){
			$left += $col['w'][$i];
			$pdf->Line($left, $pdf->top+15, $left, $top);
		}
	}

	// �޷��� ��ü �׵θ��� �׸���.
	function drawBorder($pdf, $height){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->top+15, $pdf->width, $height);
		$pdf->SetLineWidth(0.2);
	}
?>
<script>self.focus();</script>
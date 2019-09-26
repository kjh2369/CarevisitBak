<?
	if (!Is_Array($var)){
		exit;
	}

	$name = $myF->euckr($var['name']);

	//휴일리스트
	$sql = 'SELECT	mdate AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6)	= \''.$var['year'].$var['month'].'\'';

	$arrHoliday = $conn->_fetch_array($sql,'date');

	if ($var['month'] == '05'){
		$arrHoliday[$var['year'].$var['month'].'01']['name']	= '근로자의날';
	}

	//직원명
	$sql = 'SELECT	DISTINCT
					m02_yjumin AS jumin
			,		m02_yname AS name
			,		m02_ytel AS phone
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$var['code'].'\'';

	$arrMem = $conn->_fetch_array($sql,'jumin');

	//고객명
	$sql = 'SELECT	DISTINCT
					m03_jumin AS jumin
			,		m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$var['code'].'\'';

	$arrClt = $conn->_fetch_array($sql,'jumin');

	//일정
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

	//일정표(고객)
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$jumin = $row['jumin'];
		$name = $row['name'];
		$phone = $myF->phoneStyle($row['phone'],'.');
		$client = $arrClt[$row['cd']]['name'];
		$time = $myF->timeStyle($row['from_time']).'~'.$myF->timeStyle($row['to_time']);
		$sugaNm = '상담지원';

		$day = IntVal(Date('d',StrToTime($row['date'])));

		if ($tmpDay != $day){
			$tmpDay  = $day;
			$liSeq   = 0;
		}

		//고객변경 확인(주민번호/서비스/일자/순번)
		$arrCaln[$jumin][$day][$liSeq] = Array(
				'client'=>$client
			,	'time'=>$time
			,	'proc'=>$row['proc_time']
			,	'sugaNm'=>$sugaNm
		);

		$liSeq ++;
	}

	$conn->row_free();

	//고객/직원 정보 넓이
	$col['infoWidth'][0]	= $pdf->width*0.20;
	$col['infoWidth'][1]	= $pdf->width*0.25;
	$col['infoWidth'][2]	= $pdf->width*0.25;
	$col['infoWidth'][3]	= $pdf->width*0.30;

	//일정표
	$col['calnWidth'][0]	= $pdf->width*0.1428;
	$col['calnWidth'][1]	= $pdf->width*0.1428;
	$col['calnWidth'][2]	= $pdf->width*0.1428;
	$col['calnWidth'][3]	= $pdf->width*0.1428;
	$col['calnWidth'][4]	= $pdf->width*0.1428;
	$col['calnWidth'][5]	= $pdf->width*0.1428;
	$col['calnWidth'][6]	= $pdf->width*0.1428;

	$col['calnWeek'][0]		= '일';
	$col['calnWeek'][1]		= '월';
	$col['calnWeek'][2]		= '화';
	$col['calnWeek'][3]		= '수';
	$col['calnWeek'][4]		= '목';
	$col['calnWeek'][5]		= '금';
	$col['calnWeek'][6]		= '토';

	//일정 변수 설정
	$calTime	= mktime(0, 0, 1, $pdf->month, 1, $pdf->year);
	$today		= date('Ymd', mktime());
	$lastDay	= date('t', $calTime);										//총일수 구하기
	$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//시작요일 구하기
	$totalWeek	= ceil(($lastDay + $startWeek) / 7);						//총 몇 주인지 구하기
	$lastWeek	= date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay));	//마지막 요일 구하기

	$height = $pdf->row_height;

	//주별 높이
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

		//직원정보
		$col['infoTitle'][0] = "직원명";
		$col['infoTitle'][1] = "연락처";
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

		// 테두리
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $liTop, $pdf->width, $pdf->row_height * 2);
		$pdf->SetLineWidth(0.2);

		$pdf->Cell($pdf->width, 3, "", 0, 1);

		$liFirstY = $pdf->GetY();	//테두리 시작 높이
		$liLastY  = 0;				//테두리 종료 높이

		$pdf->SetXY($pdf->left,$pdf->GetY());
		//$pdf->SetFont($pdf->font_name_kor,'B',9);

		$day = 1; //화면에 표시할 화면의 초기값을 1로 설정

		for($i=1; $i<=$totalWeek; $i++){
			if ($i == 1){
				lfGetWeekString($pdf,$col,$height);
			}

			$pdf->SetX($pdf->left);
			$pdf->SetTextColor(0,0,0);

			$liGbnH = 2;

			//행높이
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

			//총 가로칸 만들기
			for($j=0; $j<7; $j++){
				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
					$date = $var['year'].$var['month'].($day < 10 ? '0' : '').$day;

					if ($j == 0){//일요일
						$pdf->SetTextColor(255,0,0); //붉은색
					}else if ($j == 6){//토요일
						$pdf->SetTextColor(0,0,255); //파란색
					}else{//평일
						$pdf->SetTextColor(0,0,0); //검정색
					}

					//기념일
					if (!Empty($arrHoliday[$date]['name'])){
						if ($date == $var['year'].$var['month'].'01'){
							$pdf->SetTextColor(0,0,255); //파란색
						}else{
							$pdf->SetTextColor(255,0,0); //붉은색
						}
					}

					//공간지정
					$pdf->Cell($col['calnWidth'][$j], $liH, "", 1, 0, 'L');

					$liX = $pdf->GetX();
					$liY = $pdf->GetY();

					$pdf->SetX($liX - $col['calnWidth'][$j]);

					//일자
					$pdf->Cell($col['calnWidth'][$j]*0.1, $height, Number_Format($day), 0, 0, 'L');

					//기념일
					$pdf->Cell($col['calnWidth'][$j]*0.9, $height, $arrHoliday[$date]['name'], 0, 2, 'R');

					//기본글자색
					$pdf->SetTextColor(0,0,0); //검정색

					if (Is_Array($arrCaln[$jumin][$day])){
						foreach($arrCaln[$jumin][$day] as $seq => $caln){
							$pdf->SetX($liX - $col['calnWidth'][$j]);

							//서비스명
							//$svcNm = '상담지원';

							//서비스
							//$pdf->Cell($col['calnWidth'][$j], $liHg, $pdf->_splitTextWidth($myF->utf($svcNm),$col['calnWidth'][$j]), 0, 2, 'L');

							//근무시간
							$pdf->Cell($col['calnWidth'][$j], $liHg, $caln['time'], 0, 2, 'L');

							//고객명
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
		//요일
		$pdf->SetX($pdf->left);
		$pdf->SetFont($pdf->font_name_kor,'B',10);
		for($j=0; $j<7; $j++){
			if ($j == 0){//일요일
				$pdf->SetTextColor(255,0,0); //붉은색
			}else if ($j == 6){//토요일
				$pdf->SetTextColor(0,0,255); //파란색
			}else{//평일
				$pdf->SetTextColor(0,0,0); //검정색
			}
			$pdf->Cell($col['calnWidth'][$j], $height, $col['calnWeek'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
		}
		$pdf->SetFont($pdf->font_name_kor,'',10);
		$pdf->SetFillColor(238,238,238);
		$pdf->SetX($pdf->left);
	}
?>
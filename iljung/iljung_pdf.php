<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//설정
	$liLineCnt = 3;

	//휴일리스트
	$sql = 'select mdate as date
			,      holiday_name as name
			  from tbl_holiday
			 where left(mdate,6) = \''.$var['year'].$var['month'].'\'';
	$laHolidayList = $conn->_fetch_array($sql, 'date');

	//직원리스트
	$sql = 'select distinct
				   m02_yjumin as jumin
			,      m02_yname as name
			,      m02_ytel as mobile
			,      m02_ytel2 as phone
			  from m02yoyangsa
			 where m02_ccode = \''.$var['code'].'\'';
	$laMemList = $conn->_fetch_array($sql,'jumin');

	//수가리스트
	$sql = 'select m01_mcode2 as code
			,      m01_suga_cont as name
			,      m01_suga_value as cost
			  from m01suga
			 where m01_mcode  = \'goodeos\'
			   and left(m01_sdate,6) <= \''.$var['year'].$var['month'].'\'
			   and left(m01_edate,6) >= \''.$var['year'].$var['month'].'\'
			 union all
			select m11_mcode2
			,      m11_suga_cont
			,      m11_suga_value
			  from m11suga
			 where m11_mcode  = \'goodeos\'
			   and left(m11_sdate,6) <= \''.$var['year'].$var['month'].'\'
			   and left(m11_edate,6) >= \''.$var['year'].$var['month'].'\'
			 union all
			select service_code
			,      concat(service_gbn, case when service_kind = \'4\' and left(service_code,3) != \'VAA\' and left(service_code,3) != \'VAC\' then concat(\'/\',service_lvl) else \'\' end)
			,      service_cost
			  from suga_service
			 where org_no       = \'goodeos\'
			   and left(service_from_dt,6) <= \''.$var['year'].$var['month'].'\'
			   and left(service_to_dt,  6) >= \''.$var['year'].$var['month'].'\'';
	$laSugaList = $conn->_fetch_array($sql,'code');

	//일정조회
	$sql = 'select cast(right(CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_date ELSE t01_sugup_date END,2) as unsigned) as day
			,      week(CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_date ELSE t01_sugup_date END,5) - week(date_sub(CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_date ELSE t01_sugup_date END,interval dayofmonth(CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_date ELSE t01_sugup_date END)-1 day),5) + 1 as week
			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_date ELSE t01_sugup_date END as date

			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_fmtime ELSE t01_sugup_fmtime END as f_time
			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_totime ELSE t01_sugup_totime END as t_time
			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_soyotime ELSE t01_sugup_soyotime END as time
			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_suga_code ELSE t01_suga_code1 END as suga_cd
			,      CASE WHEN t01_status_gbn = \'1\' THEN t01_conf_suga_value ELSE t01_suga_tot END as suga_val

			,      t01_svc_subcode as svc_cd
			,      t01_toge_umu as family_yn
			,      t01_bipay_umu as bipay_yn
			,      t01_yoyangsa_id1 as mem_cd1
			,      t01_yoyangsa_id2 as mem_cd2
			,      t01_yname1 as mem_nm1
			,      t01_yname2 as mem_nm2
			  from t01iljung
			 where t01_ccode      = \''.$var['code'].'\'
			   and t01_mkind      = \''.$var['svcCd'].'\'
			   and t01_jumin      = \''.$var['jumin'].'\'';

	if ($var['code'] == '31138000044' /*엔젤*/ ||
		$var['code'] == '31174000065' /*웃음드림방문요양센터*/){
	}else{
		$sql .= ' and t01_status_gbn = \'1\'';
	}

	$sql .= '
			   and t01_del_yn     = \'N\'
			   and left(t01_sugup_date,6) = \''.$var['year'].$var['month'].'\'
			 order by date, f_time, t_time';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$liIdx = sizeof($loData[$row['day']]);

		if ($row['svc_cd'] == '200'){
			if ($row['family_yn'] == 'Y'){
				$liSeq = 11;
			}else{
				if ($row['bipay_yn'] != 'Y'){
					$liSeq = 12;
				}else{
					$liSeq = 13;
				}
			}
		}else if ($row['svc_cd'] == '500'){
			if ($row['bipay_yn'] != 'Y'){
				$liSeq = 21;
			}else{
				$liSeq = 22;
			}
		}else if ($row['svc_cd'] == '800'){
			if ($row['bipay_yn'] != 'Y'){
				$liSeq = 31;
			}else{
				$liSeq = 32;
			}
		}else{
			$liSeq = 99;
		}

		//서비스명
		if ($var['svcCd'] == '0' || $var['svcCd'] == '4'){
			if ($row['svc_cd'] == '200'){
				$lsSvcNm = ($var['svcCd'] == '0' ? '요양' : '장애');
			}else if ($row['svc_cd'] == '500'){
				$lsSvcNm = '목욕';
			}else if ($row['svc_cd'] == '800'){
				$lsSvcNm = '간호';
			}
		}else if ($var['svcCd'] == '1'){
			$lsSvcNm = '가사';
		}else if ($var['svcCd'] == '2'){
			$lsSvcNm = '노인';
		}else if ($var['svcCd'] == '3'){
			$lsSvcNm = '산모';
		}else if ($var['svcCd'] == 'A'){
			$lsSvcNm = '산모유료';
		}else if ($var['svcCd'] == 'B'){
			$lsSvcNm = '간병';
		}else if ($var['svcCd'] == 'C'){
			$lsSvcNm = '기타';
		}

		//주차구하기
		$timestamp = mktime(0, 0, 0, $var['month'], $row['day'], $var['year']);
		$w = date('w',mktime(0,0,0,date('n',$timestamp),1,date('Y',$timestamp)));

		$liWeek = ceil(($w + date('j',$timestamp) - 1)/7);

		$loData[$row['day']][$liIdx] = array(
				'day'		=>$row['day']
			,	'week'		=>$liWeek //$row['week']
			,	'from'		=>$row['f_time']
			,	'to'		=>$row['t_time']
			,	'time'		=>$row['time']
			,	'sugaCd'	=>$row['suga_cd']
			,	'sugaNm'	=>$laSugaList[$row['suga_cd']]['name']
			,	'sugaVal'	=>$row['suga_val']
			,	'svcKindCd'	=>$row['svc_cd']
			,	'svcNm'     =>$lsSvcNm
			,	'familyYn'	=>$row['family_yn']
			,	'bipayYn'	=>$row['bipay_yn']
			,	'memCd1'	=>$row['mem_cd1']
			,	'memCd2'	=>$row['mem_cd2']
			,	'memNm1'	=>$laMemList[$row['mem_cd1']]['name']
			,	'memNm2'	=>$laMemList[$row['mem_cd2']]['name']
			,	'seq'		=>$liSeq
		);
	}

	$conn->row_free();

	//행간 기본높이 설정
	$laWeeklyHeight = array(1=>1.175,2=>1.175,3=>1.175,4=>1.175,5=>1.175);

	//주간별 높이
	if (is_array($loData)){
		foreach($loData as $laData){
			$liDayCnt = sizeof($laData) * (($pdf->row_height-0.45) / $pdf->row_height);

			foreach($laData as $laSvc){
				if ($laWeeklyHeight[$laSvc['week']] < $liDayCnt){
					$laWeeklyHeight[$laSvc['week']] = $liDayCnt;
				}
			}
			unset($laSvc);
		}
		unset($laData);
	}

	//요일
	#$laWeekly = array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');
	$liWidth = $pdf->width / 7; //요일별 넓이

	#$pdf->SetXY($pdf->left, $pdf->GetY()+1);
	#$pdf->SetFont($pdf->font_name_kor, 'B', $pdf->font_size);

	#//요일
	#for($i=0; $i<7; $i++){
	#	switch($i){
	#		case 0:
	#			$pdf->SetTextColor(255,0,0);
	#			break;
	#
	#		case 6:
	#			$pdf->SetTextColor(0,0,255);
	#			break;
	#
	#		default:
	#			$pdf->SetTextColor(0,0,0);
	#	}
	#	$pdf->Cell($liWidth, $pdf->row_height, $laWeekly[$i], 1, ($i == 6 ? 1 : 0), 'C', true);
	#}

	$pdf->type = 1;

	//폰트와 글색상
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_size);

	//달력을 그리기 위한 변수
	$liFirstWeekly = date('w', strtotime($var['year'].$var['month'].'01'));
	$liLastDay = intval($myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $var['year'].$var['month'].'01', 'Y-m-d'), 'd'));
	$liChkWeek = ceil(($liLastDay + $liFirstWeekly) / 7);
	$liWeekday = 0;
	$liWeekly  = 0;
	$lbLastWeek= false;

	//전월 영역
	for($i=0; $i<$liFirstWeekly; $i++){
		if ($liWeekday % 7 == 0){
			$pdf->SetX($pdf->left);
		}
		$pdf->Cell($liWidth, $pdf->row_height * ($laWeeklyHeight[1] * $liLineCnt), '', 1, ($i == 6 ? 1 : 0), 'C');
		$liWeekday ++;
	}

	if ($liWeekday > 0)
		$liWeekly = 1;
	else
		$liWeekly = 0;

	//금월영역
	for($i=1; $i<=$liLastDay; $i++){
		if ($liWeekday % 7 == 0){
			$liWeekday = 0;
			$liWeekly ++;

			if ($liChkWeek <= $liWeekly) $lbLastWeek = true;

			$pdf->SetX($pdf->left);
		}

		switch($liWeekday){
			case 0:
				$pdf->SetTextColor(255,0,0);
				break;

			case 6:
				$pdf->SetTextColor(0,0,255);
				break;

			default:
				$pdf->SetTextColor(0,0,0);
		}

		if (!empty($laHolidayList[$var['year'].$var['month'].($i<10?'0':'').$i]['name'])){
			$pdf->SetTextColor(255,0,0);
			$lsHoliday = $laHolidayList[$var['year'].$var['month'].($i<10?'0':'').$i]['name'];
		}

		$liW = $liWidth;
		$liH = $pdf->row_height * ($laWeeklyHeight[$liWeekly] * $liLineCnt);

		$pdf->Cell($liW, $liH, '', 1, 0, 'L');

		$liX = $pdf->GetX();
		$liY = $pdf->GetY();

		$pdf->SetXY($liX-$liW,$liY);
		$pdf->SetFont($pdf->font_name_kor, 'B', $pdf->font_size-2);
		$pdf->Cell($liW*0.2, $pdf->row_height-1, number_format($i), 1, 0, 'C', true);
		$pdf->Cell($liW*0.8, $pdf->row_height-1, $lsHoliday, 0, 2, 'R');
		$pdf->SetTextColor(0,0,0);

		if (is_array($loData[$i])){
			$liRowH  = $pdf->row_height-1.7;
			$lbFirst = true;
			foreach($loData[$i] as $laSvc){
				if (!$lbFirst){
					$pdf->Cell($liW, 2, '', 0, 2, 'C');
					$pdf->Line($pdf->GetX(), $pdf->GetY()-1, $pdf->GetX()+$liW, $pdf->GetY()-1) ;
					$pdf->SetX($liX-$liW);
				}else{
					$pdf->SetXY($liX-$liW,$pdf->GetY()+1);
				}

				//서비스명
				if ($laSvc['familyYn'] == 'Y'){
					$lsSvcNm = '동거';
				}else{
					$lsSvcNm = $laSvc['svcNm']; //$myF->euckr($conn->_svcSubNm($laSvc['svcKindCd']));
				}

				#if ($laSvc['svcKindCd'] == '200'){
				$lsSvcNm .= ' ('.$laSvc['time'].'분)';
				#}

				$pdf->SetFont($pdf->font_name_kor, 'B', $pdf->font_size-1);
				$pdf->Cell($liW, $liRowH, $lsSvcNm, 0, 2, 'L');

				if ($laSvc['svcKindCd'] == '500'){
					//목욕 아이콘
					$pdf->Image('../image/icon_bath.jpg',$pdf->GetX()+$pdf->GetStringWidth($lsSvcNm.'  '),$pdf->GetY()-$liRowH,4,4);
				}else if ($laSvc['svcKindCd'] == '800'){
					//간호 아이콘
					$pdf->Image('../image/icon_nurs.jpg',$pdf->GetX()+$pdf->GetStringWidth($lsSvcNm.'  '),$pdf->GetY()-$liRowH,4,4);
				}

				#$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_size-2.5);
				$pdf->SetXY($liX-$liW, $pdf->GetY());
				$pdf->Cell($liW, $liRowH, $myF->timeStyle($laSvc['from']).'~'.$myF->timeStyle($laSvc['to']), 0, 2, 'R');

				if ($laSvc['bipayYn'] == 'Y'){
					//비급여
					$pdf->Image('../image/btn/btn_bipay.gif',$pdf->GetX()+1,$pdf->GetY()+1.5,3,3);
				}

				$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_size-2);
				$pdf->SetXY($liX-$liW, $pdf->GetY());
				$pdf->Cell($liW, $liRowH, $laSvc['memNm1'].($laSvc['memNm2'] ? '/'.$laSvc['memNm2'] : ''), 0, 2, 'R');

				//담당직원
				$laMemStr[$laSvc['memCd1']] = array('name'=>$laSvc['memNm1'], 'tel'=>($laMemList[$laSvc['memCd1']]['mobile'] ? $laMemList[$laSvc['memCd1']]['mobile'] : $laMemList[$laSvc['memCd1']]['phone']));

				if ($laSvc['memCd2']){
					$laMemStr[$laSvc['memCd2']] = array('name'=>$laSvc['memNm2'], 'tel'=>($laMemList[$laSvc['memCd2']]['mobile'] ? $laMemList[$laSvc['memCd2']]['mobile'] : $laMemList[$laSvc['memCd2']]['phone']));
				}

				$lbFirst = false;
			}
		}

		if ($liWeekday > 0 && $liWeekday % 6 == 0){
			$liY += $liH;
		}

		$pdf->SetXY($liX,$liY);

		$pdf->SetFont($pdf->font_name_kor, '', $pdf->font_size-2);

		$liWeekday ++;

		unset($lsTxt);
		unset($lsHoliday);
	}

	unset($laHolidayList);
	unset($laMemList);

	if ($liWeekday % 7 == 0){
		$pdf->SetX($pdf->left);
	}else{
		//익월영역
		for($i=$liWeekday+1; $i<=7; $i++){
			$pdf->Cell($liW, $pdf->row_height * ($laWeeklyHeight[$liWeekly] * $liLineCnt), '', 1, ($liWeekday > 0 && $liWeekday % 6 == 0 ? 1 : 0), 'C');
			$liWeekday ++;
		}
	}

	//담당요양보호사
	if (is_array($laMemStr)){
		foreach($laMemStr as $laMem){
			$lsTel  = ($laMem['mobile'] ? $laMem['mobile'] : $laMem['phone']);
			$lsMem .= ($lsMem ? ' / ' : '');
			$lsMem .= $laMem['name'].($lsTel ? '('.$lsTel.')' : '');
		}
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width, $pdf->row_height+2, '  담당 : '.$lsMem, 1, 1, 'L', true);
	}
	unset($laMemStr);

	//일자배열
	for($i=1;$i<=$liLastDay; $i++){
		$laDay[$i] = 0;
	}

	//제공서비스 내역
	$pdf->type = 2;

	if (is_array($loData)){
		foreach($loData as $laRow){
			foreach($laRow as $laSvc){
				if ($laSvc['familyYn'] == 'Y'){
					$lsSvcNm = '동거';
				}else{
					$lsSvcNm = $laSvc['svcNm']; //$myF->euckr($conn->_svcSubNm($laSvc['svcKindCd']));
				}

				if ($laSvc['svcKindCd'] == '200'){
					$lsSvcNm .= ' ('.$laSvc['time'].'분)';
				}

				$lsKey = $lsSvcNm.'_'.$laSvc['memCd1'].'_'.$laSvc['memCd2'];

				if (!isset($laIdx[$lsKey])){
					$laIdx[$lsKey] = sizeof($laData);
					$laData[$laIdx[$lsKey]]['date'] = $laDay;
				}

				$laData[$laIdx[$lsKey]]['code']  = $laSvc['sugaCd'];
				$laData[$laIdx[$lsKey]]['name']  = $lsSvcNm;
				$laData[$laIdx[$lsKey]]['mem']   = $laSvc['memNm1'].($laSvc['memNm2'] ? ' 외1' : '');
				$laData[$laIdx[$lsKey]]['bipay'] = $laSvc['bipayYn'];
				$laData[$laIdx[$lsKey]]['seq']   = $laSvc['seq'];
				$laData[$laIdx[$lsKey]]['count'] ++;
				$laData[$laIdx[$lsKey]]['date'][$laSvc['day']] ++;
			}
		}
		$laData = $myF->sortArray($laData,'seq',1);
	}

	unset($laIdx);

	$pdf->_iljungTitle('2');

	if (is_array($laData)){
		foreach($laData as $laSvc){
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width*0.12,$pdf->row_height,$laSvc['name'],1,0,'L');
			$pdf->Cell($pdf->width*0.12,$pdf->row_height,$laSvc['mem'],1,0,'L');

			for($i=1;$i<=31;$i++){
				if ($laSvc['date'][$i] > 0){
					$pdf->SetTextColor(0,0,0);
				}else{
					$pdf->SetTextColor(220,220,220);
				}

				$pdf->Cell($pdf->GetStringWidth(number_format($i))+1.15,$pdf->row_height,number_format($i),0,0,'L');
			}

			$pdf->SetTextColor(0,0,0);
			$pdf->SetX($pdf->left+$pdf->width*0.24);
			$pdf->Cell($pdf->width*0.71,$pdf->row_height,'',1,0,'L');
			$pdf->Cell($pdf->width*0.05,$pdf->row_height,number_format($laSvc['count']),1,1,'C');
		}
		unset($laSvc);
	}
	unset($laData);

	//제공서비스 금액내역
	$pdf->type = 3;

	if (is_array($loData)){
		foreach($loData as $laRow){
			foreach($laRow as $laSvc){
				$lsKey = $laSvc['sugaCd'];

				if ($var['svcCd'] == '0' || $var['svcCd'] == '4'){
					if ($row['svc_cd'] == '200'){
						$lsSvcNm = ($var['svcCd'] == '0' ? '방문요양' : '활동지원');
					}else if ($row['svc_cd'] == '500'){
						$lsSvcNm = '방문목욕';
					}else if ($row['svc_cd'] == '800'){
						$lsSvcNm = '방문간호';
					}
				}else if ($var['svcCd'] == '1'){
					$lsSvcNm = '가사간병';
				}else if ($var['svcCd'] == '2'){
					$lsSvcNm = '노인돌봄';
				}else if ($var['svcCd'] == '3'){
					$lsSvcNm = '산모신생아';
				}else if ($var['svcCd'] == 'A'){
					$lsSvcNm = '산모유료';
				}else if ($var['svcCd'] == 'B'){
					$lsSvcNm = '병원간병';
				}else if ($var['svcCd'] == 'C'){
					$lsSvcNm = '기타유료';
				}

				if (!isset($laIdx[$lsKey])){
					$laIdx[$lsKey] = sizeof($laData);
					$laData[$laIdx[$lsKey]]['date']    = $laDay;
					$laData[$laIdx[$lsKey]]['svcCd']   = $laSvc['svcKindCd'];
					$laData[$laIdx[$lsKey]]['svcNm']   = $lsSvcNm; //$myF->euckr($conn->_svcNm($laSvc['svcKindCd']));
					$laData[$laIdx[$lsKey]]['sugaCd']  = $laSvc['sugaCd'];
					$laData[$laIdx[$lsKey]]['sugaNm']  = $laSugaList[$laSvc['sugaCd']]['name'];
					$laData[$laIdx[$lsKey]]['sugaVal'] = $laSvc['sugaVal'];
					$laData[$laIdx[$lsKey]]['time']    = $myF->euckr($myF->getMinToHM($laSvc['time']));
				}
				$laData[$laIdx[$lsKey]]['expense'] += ($laSvc['sugaVal']*$lsRate*0.01);
				$laData[$laIdx[$lsKey]]['sugaTot'] += $laSvc['sugaVal'];
				$laData[$laIdx[$lsKey]]['count'] ++;
			}
		}
		$laData = $myF->sortArray($laData,'seq',1);
	}

	$pdf->_iljungTitle('3');

	if (is_array($laData)){
		foreach($laData as $laSvc){
			$pdf->SetX($pdf->left);
			$pdf->Cell($pdf->width*0.10,$pdf->row_height,$laSvc['svcNm'],1,0,'C');
			$pdf->Cell($pdf->width*0.20,$pdf->row_height,$laSvc['sugaNm'],1,0,'L');
			$pdf->Cell($pdf->width*0.10,$pdf->row_height,number_format($laSvc['count']),1,0,'C');
			$pdf->Cell($pdf->width*0.15,$pdf->row_height,$laSvc['time'],1,0,'C');
			$pdf->Cell($pdf->width*0.15,$pdf->row_height,number_format($laSvc['sugaVal']),1,0,'R');
			$pdf->Cell($pdf->width*0.15,$pdf->row_height,number_format($laSvc['sugaTot']),1,0,'R');
			$pdf->Cell($pdf->width*0.15,$pdf->row_height,number_format($laSvc['expense']),1,1,'R');

			$liSugaTot += $laSvc['sugaTot'];
			$liExpense += $laSvc['expense'];
		}
		unset($laSvc);

		$liExpense = $myF->cutOff($liExpense,10);

		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width*0.10,$pdf->row_height,'합      계',1,0,'C',true);
		$pdf->Cell($pdf->width*0.60,$pdf->row_height,'',1,0,'L',true);
		$pdf->Cell($pdf->width*0.15,$pdf->row_height,number_format($liSugaTot),1,0,'R',true);
		$pdf->Cell($pdf->width*0.15,$pdf->row_height,number_format($liExpense),1,1,'R',true);
	}
	unset($laData);

	unset($loData);
	unset($laSugaList);

	include_once('../inc/_db_close.php');
?>
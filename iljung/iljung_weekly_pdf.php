<?

	if (!Is_Array($var)){
		exit;
	}

	//일정표
	$col['calnWidth'][0]	= $pdf->width*0.1428;
	$col['calnWidth'][1]	= $pdf->width*0.1428;
	$col['calnWidth'][2]	= $pdf->width*0.1428;
	$col['calnWidth'][3]	= $pdf->width*0.1428;
	$col['calnWidth'][4]	= $pdf->width*0.1428;
	$col['calnWidth'][5]	= $pdf->width*0.1428;
	$col['calnWidth'][6]	= $pdf->width*0.1428;

	$col['calnWeek'][0]		= $pdf->width*0.1428-12;
	$col['calnWeek'][1]		= $pdf->width*0.1428*2-12;
	$col['calnWeek'][2]		= $pdf->width*0.1428*3-12;
	$col['calnWeek'][3]		= $pdf->width*0.1428*4-12;
	$col['calnWeek'][4]		= $pdf->width*0.1428*5-12;
	$col['calnWeek'][5]		= $pdf->width*0.1428*6-12;
	$col['calnWeek'][6]		= $pdf->width*0.1428*7-12;


	//일정 변수 설정

	$yymm    = substr(str_replace('.','', $var['from']),0,6);				//년월
	$year    = substr($yymm, 0,4);											//년
	$month   = substr($yymm, 4,2);											//월
	
	
	#출력옵션
	$fontSize = $var['fontSize'];											//폰트크기
	$svcChk = $var['svcChk'];												//서비스명출력여부


	$fromDay = intval(substr($var['from'],8,2));							//시작일자
	$toDay = intval(substr($var['to'],8,2));								//종료일자
	$calTime	= mktime(0, 0, 1, $month, 1, $year);
	//$today		= date('Ymd', mktime());
	$lastDay	= date('t', $calTime);										//총일수 구하기
	$startWeek	= date('w', strtotime(date('Y-m', $calTime).'-01'));		//시작요일 구하기
	$totalWeek	= ceil(($lastDay + $startWeek) / 7);						//총 몇 주인지 구하기
	$lastWeek	= date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay));	//마지막 요일 구하기

	
	$height = $pdf->row_height;


	$pdf->SetXY($pdf->left,$pdf->GetY());
	//$pdf->SetFont($pdf->font_name_kor,'B',9);

	$pdf->SetFont($pdf->font_name_kor,'',$fontSize);
	$pdf->SetFillColor(238,238,238);
	$pdf->SetXY($pdf->left, $pdf->GetY());

	$code	= $_SESSION['userCenterCode'];
	$from	= Str_Replace('.','',$var['from']);
	$to		= Str_Replace('.','',$var['to']);
	$svc	= Explode(chr(1),$var['svc']);

	$k = 0;

	$sql = '';

	foreach($svc as $tmp){
		if (Is_Numeric(StrPos($tmp,'_'))){
			$var = Explode('_',$tmp);
			$svcCd = $var[0];
			$subCd = $var[1];
		}else{
			$svcCd = $tmp;
			$subCd = '';
		}

		$sql .= ($sql ? ' UNION ALL ' : '');
		$sql .= 'SELECT	t01_mkind AS svc_cd
				,		t01_sugup_date AS date
				,		m03_name AS name
				,		t01_sugup_fmtime AS from_time
				,		t01_sugup_totime AS to_time
				,		t01_yname1 AS mem_nm1
				,		t01_yname2 AS mem_nm2
				,		t01_suga_code1
				FROM	t01iljung
				INNER	JOIN	m03sugupja
						ON		m03_ccode = t01_ccode
						AND		m03_mkind = t01_mkind
						AND		m03_jumin = t01_jumin
				WHERE	t01_ccode	= \''.$code.'\'
				AND		t01_mkind	= \''.$svcCd.'\'
				AND		t01_sugup_date >= \''.$from.'\'
				AND		t01_sugup_date <= \''.$to.'\'
				AND		t01_sugup_fmtime != \'\'
				AND		t01_del_yn	= \'N\'';

		if ($subCd){
			$sql .= '
				AND		t01_svc_subcode = \''.$subCd.'\'';
		}
	}

	$sql .= '
			ORDER	BY date,from_time,to_time,name';


	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	

	for($t=0; $t<$rowCnt; $t++){
		$row = $conn->select_row($t);

		$weekly = Date('w',StrToTime($row['date']));

		$arr[$weekly][SizeOf($arr[$weekly])] = Array('week'=>$weekly,'date'=>$row['date'],'svcNm'=>$myF->euckr($conn->_svcSubNm($row['svc_cd'])),'from'=>$myF->timeStyle($row['from_time']),'to'=>$myF->timeStyle($row['to_time']),'name'=>$row['name'],'mem'=>$row['mem_nm1'].($row['mem_nm2'] ? '/'.$row['mem_nm2'] : ''),'link'=>'N');
	}

	$conn->row_free();

	$tempRow = 0;

	for($i=0;$i<=6; $i++){

		if($tempRow < SizeOf($arr[$i])){
			$tempRow = SizeOf($arr[$i]);
		}
	}


	for($i=0;$i<=6; $i++){

		for($j=0; $j<$tempRow; $j++){
			$data[$i][$j] = $arr[$i][$j];
		}
	}

	//행높이
	if($fontSize == '5'){
		$liHg = $height * 0.45;
	}else if($fontSize == '7'){
		$liHg = $height * 0.58;
	}else if($fontSize == '9'){
		$liHg = $height * 0.67;
	}else if($fontSize == '11'){
		$liHg = $height * 0.80;
	}
	
	$temp_H = 0;

	for($i=0; $i<$tempRow; $i++){
		$coordY = $pdf->GetY();
		$coordX = $pdf->left;

		if ($coordY > $pdf->height){
			$pdf->Line($pdf->left,$coordY2,$pdf->left+$pdf->width,$coordY2);
			$pdf->addPage();
			$coordY = $pdf->tempVal;
		}

		for($j=0;$j<=6; $j++){

			$pdf->SetXY($coordX,$coordY);


			if ($data[$j][$i]){
				if($svcChk != 'true'){
					$pdf->Cell($col['calnWidth'][$j]*0.65,$liHg,$data[$j][$i]['from']."~".$data[$j][$i]['to'],'L',0,'L');
					$pdf->Cell($col['calnWidth'][$j]*0.35,$liHg,$data[$j][$i]['name'],"R",2,'L');
					
					$pdf->SetXY($coordX,$pdf->GetY());
					$pdf->Cell($col['calnWidth'][$j],$liHg,$data[$j][$i]['mem'],"LR",($j == 6 ? 1 : 2),'L');
					//$pdf->_splitTextWidth($myF->utf($data[$j][$i]['mem']),$col['calnWidth'][$j])
					$x = $coordX;

					while(true){
						if ($x > $coordX+$col['calnWidth'][$j]) break;

						$pdf->Line($x,$coordY,$x+0.5,$coordY);

						$x = $x + 1;
					}
				}else {
					$pdf->Cell($col['calnWidth'][$j]*0.3,$liHg,$data[$j][$i]['svcNm'],"L",0,'L');
					$pdf->Cell($col['calnWidth'][$j]*0.7,$liHg,$data[$j][$i]['name'],"R",2,'R');

					$pdf->SetXY($coordX,$pdf->GetY());
					$pdf->Cell($col['calnWidth'][$j],$liHg,$data[$j][$i]['from']."~".$data[$j][$i]['to'],"LR",2,'L');
					$pdf->Cell($col['calnWidth'][$j],$liHg,$data[$j][$i]['mem'],"LR",($j == 6 ? 1 : 2),'L');
					//$pdf->_splitTextWidth($myF->utf($data[$j][$i]['mem']),$col['calnWidth'][$j])
					$x = $coordX;

					while(true){
						if ($x > $coordX+$col['calnWidth'][$j]) break;

						$pdf->Line($x,$coordY,$x+0.5,$coordY);

						$x = $x + 1;
					}
				}
			}else{
				$pdf->Cell($col['calnWidth'][$j],$liHg,"","LR",2,'C');
				if($svcChk == 'true'){
					$pdf->Cell($col['calnWidth'][$j],$liHg,"","LR",2,'C');
				}
				$pdf->Cell($col['calnWidth'][$j],$liHg,"","LR",($j == 6 ? 1 : 2),'C');
			}

			$coordX += $col['calnWidth'][$j];
			$coordY2 = $pdf->GetY();
		}
	}

	$pdf->Line($pdf->left,$coordY2,$pdf->left+$pdf->width,$coordY2);
?>
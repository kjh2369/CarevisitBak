<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../pdf/korean.php');

	$pdf=new PDF_Korean('L');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();

	$pdf->SetFillColor(255,255,255);

	$conn->set_name('euckr');
	$con2 = new connection();
	$con2->set_name('euckr');

	$mCode    = $_GET['mCode'];
	$mKind    = $_GET['mKind'];
	$mYM      = $_GET['mYM'];
	$mRate    = $_GET['mRate'];
	$mSugupja = ($_GET['mSugupja'] != 'all' ? $ed->de($_GET['mSugupja']) : $_GET['mSugupja']);
	

	$col[0] = 14;
	$col[1] = 30;
	$col[2] = 50;
	$col[3] = 10;
	$col[4] = 30;
	$col[5] = 10;
	$col[6] = 140;

	$row[0] = 14;
	$rowHeight[0] = 10;
	
	
	//기관 로고
	$sql = 'select m00_icon
			  from m00center
			 where m00_mcode = \''.$mCode.'\'';
	$icon = $conn -> get_data($sql);

	// 일정조회
	$sql = "select t01_jumin as sugupjaJumin"
		 . ",      m03_name as sugupjaName"
		 . ",      t01_yname1 as yoyangsaName1"
		 . ",      t01_yname2 as yoyangsaName2"
		 . ",      sugaName"
		 . ",      t01_svc_subcode as serviceCode"
		 . ",      case t01_svc_subcode when '200' then '방문요양'"
		 . "						    when '500' then '방문목욕'"
		 . "						    when '800' then '방문간호' else '' end as serviceName"
		 . ",      t01_toge_umu as togeUmu"
		 . ",      case t01_toge_umu when 'Y' then '√' else '' end as togeYN"
		 . ",      t01_sugup_fmtime as workFormTime"
		 . ",      t01_sugup_totime as workToTime"
		 . ",      t01_sugup_date as workDate"
		 . ",      t01_conf_suga_value as sugaValue"
		 . "  from t01iljung"
		 . " inner join ("
		 . "	   select m01_mcode2 as sugaCode, m01_suga_cont as sugaName, m01_sdate as sDate, m01_edate as eDate"
		 . "	 	 from m01suga"
		 . "	    where m01_mcode = '".$mCode
		 . "'       union all"
		 . "	   select m11_mcode2 as sugaCode, m11_suga_cont as sugaName, m11_sdate as sDate, m11_edate as eDate"
		 . "		 from m11suga"
		 . "	    where m11_mcode = '".$mCode
		 . "'	   ) as suga"
		 . "    on sugaCode = t01_suga_code1"
		 . "   and t01_conf_date between sDate and eDate"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'";

	if ($mSugupja != 'all'){
		$sql .= " and t01_jumin = '".$mSugupja
			 .  "'";
	}

	$sql .="   and t01_conf_date like '".$mYM
		 . "%' and t01_del_yn = 'N'"
		 //. "   and t01_conf_soyotime > case t01_svc_subcode when '200' then 29 else 0 end";
		 . "   and t01_conf_soyotime >= case t01_svc_subcode when '200' then 30 else 0 end"
		 . "   and t01_status_gbn = '1'";

	if ($mSugupja != 'all'){
		$sql .= " order by t01_yname1, t01_yname2, sugaName, t01_svc_subcode, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	}else{
		$sql .= " order by m03_name, t01_yname1, t01_yname2, sugaName, t01_svc_subcode, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	}
	
	//if($debug) echo nl2br($sql); exit;

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	$listCount = 15; //한페이지에 출력할 리스트 갯수
	$index = 0;
	$page = 0;

	$rowData['count'] = 0;
	$rowData['chungAmt'] = 0;
	$rowData['overAmt'] = 0;

	$sugupja = Array();
	$confData = Array();

	for($i=0; $i<$rowCount; $i++){
		$var = $conn->select_row($i);

		if ($tempSugupja != $var['sugupjaJumin']){
			$tempSugupja  = $var['sugupjaJumin'];

			if($lbTestMode){
			
				$sql = "select kind.kind as kind
									, m03_jumin as jumin
									, kind.rate as boninYul
									, replace(kind.from_dt, '-', '') as sdate
									, replace(kind.to_dt, '-', '') as edate
							   from m03sugupja
						left join client_his_lvl as lvl
						on lvl.org_no = m03_ccode
						and lvl.jumin = m03_jumin
						and left(lvl.from_dt, 7) <=  '".substr($mYM,0,4).'-'.substr($mYM,4,2)."'
						and left(lvl.to_dt, 7) >= '".substr($mYM,0,4).'-'.substr($mYM,4,2)."'
						left join client_his_kind as kind
						on kind.org_no = m03_ccode
						and kind.jumin = m03_jumin";
				
				if ($mRate != 'all'){
					$sql.=" and kind.kind  = '$mRate'";
				}
						
				$sql.=" and left(kind.from_dt, 7) <= '".substr($mYM,0,4).'-'.substr($mYM,4,2)."'
						and left(kind.to_dt, 7) >= '".substr($mYM,0,4).'-'.substr($mYM,4,2)."'
						where m03_ccode = '$mCode'
						and m03_jumin  = '".$var['sugupjaJumin']."'
						and m03_del_yn = 'N'
						group by m03_jumin
						order by sdate, edate";
				
			}else{
			
				// 수급자 정보
				$sql = "select m03_skind as kind, m03_bonin_yul as boninYul, m03_kupyeo_max as maxPay, m03_sdate as sdate, m03_edate as edate"
					 . "  from m03sugupja"
					 . " where m03_ccode = '".$mCode
					 . "'  and m03_mkind = '".$mKind
					 . "'  and m03_jumin = '".$var['sugupjaJumin']
					 . "'  and '".$mYM."' between left(m03_sdate, 6) and left(m03_edate, 6)";

				if ($mRate != 'all'){
					$sql .= " and m03_skind = '".$mRate
						 .  "'";
				}

				$sql .=" union all "
					 . "select m31_kind as kind, m31_bonin_yul as boninYul, m31_kupyeo_max as maxPay, m31_sdate as sdate, m31_edate as edate"
					 . "  from m31sugupja"
					 . " where m31_ccode = '".$mCode
					 . "'  and m31_mkind = '".$mKind
					 . "'  and m31_jumin = '".$var['sugupjaJumin']
					 . "'  and '".$mYM."' between left(m31_sdate, 6) and left(m31_edate, 6)";

				if ($mRate != 'all'){
					$sql .= " and m31_kind = '".$mRate
						 .  "'";
				}
				
				$sql .=" order by sdate, edate";
			}

			$con2->query($sql);
			$con2->fetch();
			$suCount = $con2->row_count();

			$sugupja = Array();

			for($j=0; $j<$suCount; $j++){
				$su = $con2->select_row($j);
				
				$sugupja[$j]['kind'] = $su['kind'];
				$sugupja[$j]['boninYul'] = $su['boninYul'];
				//$sugupja[$j]['maxPay'] = $su['maxPay'];
				$sugupja[$j]['sdate'] = $su['sdate'];
				$sugupja[$j]['edate'] = $su['edate'];
			}
			$con2->row_free();
		}

		$sugupCount = sizeOf($sugupja);

		for($j=0; $j<$sugupCount; $j++){
			
			if ($var['workDate'] >= $sugupja[$j]['sdate'] &&
				$var['workDate'] <= $sugupja[$j]['edate']){
				
				if ($var['yoyangsaName2'] != ''){
					$yoyangsaName = $var['yoyangsaName1']."\n/".$var['yoyangsaName2'];
					$yoyangsaCount = 2;
				}else{
					$yoyangsaName = $var['yoyangsaName1'];
					$yoyangsaCount = 1;
				}
				$sugaName = $var['sugaName'];
				$togeYN = $var['togeYN'];
				$workTime = subStr($var['workFormTime'], 0, 2).':'.subStr($var['workFormTime'], 2, 2).'~'.subStr($var['workToTime'], 0, 2).':'.subStr($var['workToTime'], 2, 2);
				
				if ($tempData != $tempSugupja.$yoyangsaName.$sugaName.$togeYN.$workTime){
					$tempData  = $tempSugupja.$yoyangsaName.$sugaName.$togeYN.$workTime;
					$index ++;
					
					$rowData[$index]['sugupjaName'] = $var['sugupjaName'];
					$rowData[$index]['sugupja'] = $var['sugupjaJumin'];
					$rowData[$index]['yoy'] = $yoyangsaName;
					$rowData[$index]['yoyCount'] = $yoyangsaCount;
					$rowData[$index]['suga'] = $sugaName;
					$rowData[$index]['toge'] = $togeYN;
					$rowData[$index]['time'] = $workTime;
					$rowData[$index]['count'] = 0;
					$rowData[$index]['date'] = '';

					if ($mSugupja == 'all'){
						if ($tempSugupjaJumin != $var['sugupjaJumin']){
							$tempSugupjaJumin  = $var['sugupjaJumin'];
							$newPage = true;
							$allIndex = 0;

							// 확정정보
							$confData[$var['sugupjaJumin']] = getConfData($con2, $mSugupja, $mCode, $mKind, $var['sugupjaJumin'], $mYM, $sugupja[$j]['boninYul']);
						}else{
							$newPage = false;
						}

						if ($allIndex > $listCount){
							$allIndex = 0;
							$newPage = true;
						}

						$allIndex ++;
					}else{
						if ($tempSugupjaJumin != $var['sugupjaJumin'].$sugupja[$j]['kind']){
							$tempSugupjaJumin  = $var['sugupjaJumin'].$sugupja[$j]['kind'];

							// 확정정보
							$confData[$var['sugupjaJumin']] = getConfData($con2, $mSugupja, $mCode, $mKind, $var['sugupjaJumin'], $mYM, $sugupja[$j]['boninYul']);
						}
						if ($index % $listCount == 1){
							$newPage = true;
						}else{
							$newPage = false;
						}
					}

					if (($newPage == true)){
						$page ++;
						$pageData[$page]['count'] = 0;
						$pageData[$page]['sugaAmt']['200'] = 0;
						$pageData[$page]['sugaAmt']['500'] = 0;
						$pageData[$page]['sugaAmt']['800'] = 0;
						$pageData[$page]['boninAmt']['200'] = 0;
						$pageData[$page]['boninAmt']['500'] = 0;
						$pageData[$page]['boninAmt']['800'] = 0;
						$pageData[$page]['chungAmt']['200'] = 0;
						$pageData[$page]['chungAmt']['500'] = 0;
						$pageData[$page]['chungAmt']['800'] = 0;
						$pageData[$page]['overAmt'] = 0;
						$pageData[$page]['sugupja'] = $var['sugupjaName'];
					}
					
				}

				$confData[$var['sugupjaJumin']]['count'] ++;

				$rowData[$index]['count'] ++;
				$rowData[$index]['date'] .= ($rowData[$index]['date'] != '' ? ',' : '').intVal(subStr($var['workDate'], 6, 2));

				$pageData[$page]['count'] ++;
				$pageData[$page]['sugaAmt'][$var['serviceCode']] += ($var['togeUmu'] != 'Y' ? $var['sugaValue'] : 0);
				$pageData[$page]['boninAmt'][$var['serviceCode']] += (($var['togeUmu'] != 'Y' ? $var['sugaValue'] : 0) * ($sugupja[$j]['boninYul'] / 100));
				
				
				break;
			}
		}
	}

	$addFlag = false;
	$amtFlag = false;
	$newPage = false;
	$totalPageNo = $page;
	$pageNo = 0;
	$pageIndex = 0;
	$sugupjaJumin = '';
	
	for($i=1; $i<=$index; $i++){
		$pageIndex ++;

		// 수급자주민번호
		if ($sugupjaJumin != $rowData[$i]['sugupja']){
			$sugupjaJumin  = $rowData[$i]['sugupja'];
			$pageIndex = 0;

			if ($mSugupja != 'all'){
				if ($i % $listCount == 1){
					$newPage = true;
				}else{
					$newPage = false;
				}
				$amtFlag = false;
			}else{
				$amtFlag = true;
				$newPage = true;
			}
		}else{
			if ($backSugupjaJumin == '') $backSugupjaJumin = $sugupjaJumin;

			if ($mSugupja != 'all'){
				$pageNo = ceil($i / $listCount);

				if ($i % $listCount == 1){
					$newPage = true;
				}else{
					$newPage = false;
				}
				$amtFlag = false;
			}else{
				$newPage = false;

				if ($pageIndex >= $listCount){
					$pageIndex = 0;
					$newPage = true;
				}
			}
		}
		
		if ($newPage == true){
			if ($mSugupja != 'all'){
				$pageNo = ceil($i / $listCount);
			}else{
				$pageNo ++;
			}
			if ($addFlag == true){
				// 타이틀(동거여부)
				$pdf->SetXY($col[0]+$col[1]+$col[2], 25);
				$pdf->SetFont('굴림','',10);
				$pdf->MultiCell($col[3], 4, "동거\n여부", 0, 'C');
				$pdf->SetXY($col[0]+$col[1]+$col[2]+$col[3]+$col[4], 25);
				$pdf->MultiCell($col[5], 4, "총\n횟수", 0, 'C');

				$pdf->SetLineWidth(0.6);
				$pdf->Rect($col[0], $row[0]+$rowHeight[0], 270, 160);
				$pdf->SetLineWidth(0.2);

				// 타이틀선
				$pdf->SetLineWidth(0.6);
				$pdf->Line($col[0], $row[0]+$rowHeight[0]*2, $col[0]+270, $row[0]+$rowHeight[0]*2);
				$pdf->SetLineWidth(0.2);

				if ($amtFlag == true){
					$amtFlag = false;

					$pdf->Text(14, 190, '총횟수 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['count'], '건').'          '
									   .'청구금액 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['chungAmt'], '원').'          '
									   .'초과금액 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['overAmt'], '원'));
					$backSugupjaJumin = $sugupjaJumin;
				}
			}

			$addFlag = true;
			
			if($gDomain == 'dolvoin.net'){
				if($icon != ''){
					$exp = explode('.',$icon);
					$exp = strtolower($exp[sizeof($exp)-1]);
					if($exp != 'bmp'){
						$pdf->Image('../mem_picture/'.$icon, 270, 190, 20, null);	//기관 로고
					}
				}
			}


			$pdf->AddPage();

			// 폰트설정
			$pdf->SetFont('굴림', '', 12);

			// 수급자명
			$pdf->SetXY($col[0], $row[0]);
			$pdf->Cell(50, 10, '수급자명 : '.$pageData[$pageNo]['sugupja'], 0, 0, 'L');
			$pdf->Cell(170, 10, '일별수급내역 명세서', 0, 0, 'C');
			$pdf->Cell(50, 10, subStr($mYM, 0, 4).'년 '.subStr($mYM, 4, 2).'월', 0, 1, 'R');

			// 타이틀
			$pdf->SetX($col[0]);
			$pdf->Cell($col[1], $rowHeight[0], '요양보호사명', 1, 0, 'C');
			$pdf->Cell($col[2], $rowHeight[0], '수가명', 1, 0, 'C');
			$pdf->Cell($col[3], $rowHeight[0], '', 1, 0, 'C');
			$pdf->Cell($col[4], $rowHeight[0], '계획시간', 1, 0, 'C');
			$pdf->Cell($col[5], $rowHeight[0], '', 1, 0, 'C');
			$pdf->Cell($col[6], $rowHeight[0], '일자', 1, 1, 'C');

			$pdf->Text(280 - $pdf->GetStringWidth($pageNo.'/'.$totalPageNo), 190, ' '.$pageNo.'/'.$totalPageNo.' ');
		}

		$tempDate = explode(',', $rowData[$i]['date']);
		asort($tempDate);
		$rowData[$i]['date'] = implode(',', $tempDate);

		$rowData[$i]['count'] = ' '.$rowData[$i]['count'].' ';
		$pdf->SetFont('굴림', '', 11);
		$pdf->SetX($col[0]);
		$pdf->Cell($col[1], $rowHeight[0], ($rowData[$i]['yoyCount'] == 1 ? $rowData[$i]['yoy'] : ''), 1, 0, 'L'); //$rowData[$i]['yoy']
		$pdf->Cell($col[2], $rowHeight[0], $rowData[$i]['suga'], 1, 0, 'L');
		$pdf->Cell($col[3], $rowHeight[0], $rowData[$i]['toge'], 1, 0, 'C');
		$pdf->Cell($col[4], $rowHeight[0], $rowData[$i]['time'], 1, 0, 'C');
		$pdf->Cell($col[5], $rowHeight[0], $rowData[$i]['count'], 1, 0, 'C');
		$pdf->SetFont('굴림','',9.4);
		$pdf->Cell($col[6], $rowHeight[0], $rowData[$i]['date'], 1, 1, 'L');

		if ($rowData[$i]['yoyCount'] > 1){
			$tempX = $pdf->GetX();
			$tempY = $pdf->GetY();
			$pdf->SetXY($col[0], $tempY - $rowHeight[0] + 1);
			$pdf->MultiCell($col[1], 4, $rowData[$i]['yoy'], 0, 'L');
			$pdf->SetXY($tempX, $tempY);
		}
	}

	if ($addFlag == true){
		// 타이틀(동거여부)
		$pdf->SetXY($col[0]+$col[1]+$col[2], 25);
		$pdf->SetFont('굴림','',10);
		$pdf->MultiCell($col[3], 4, "동거\n여부", 0, 'C');
		$pdf->SetXY($col[0]+$col[1]+$col[2]+$col[3]+$col[4], 25);
		$pdf->MultiCell($col[5], 4, "총\n횟수", 0, 'C');

		$pdf->SetLineWidth(0.6);
		$pdf->Rect($col[0], $row[0]+$rowHeight[0], 270, 160);
		$pdf->SetLineWidth(0.2);

		// 타이틀선
		$pdf->SetLineWidth(0.6);
		$pdf->Line($col[0], $row[0]+$rowHeight[0]*2, $col[0]+270, $row[0]+$rowHeight[0]*2);
		$pdf->SetLineWidth(0.2);

		// 총횟수, 청구금액, 초과금액
		$c[$k]['bonbuTot1'] = $c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1'];
		$c[$k]['bonbuTot2'] = $c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2'];
		$c[$k]['bonbuTot3'] = $c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3'];

		$c[$k]['chungAmt1'] = $c[$k]['sugaTot1'] - $c[$k]['bonbuTot1'];
		$c[$k]['chungAmt2'] = $c[$k]['sugaTot2'] - $c[$k]['bonbuTot2'];
		$c[$k]['chungAmt3'] = $c[$k]['sugaTot3'] - $c[$k]['bonbuTot3'];

		$c[$k]['boninAmt1'] += ($c[$k]['chungAmt1'] - cutOff($c[$k]['chungAmt1']));
		$c[$k]['boninAmt2'] += ($c[$k]['chungAmt2'] - cutOff($c[$k]['chungAmt2']));
		$c[$k]['boninAmt3'] += ($c[$k]['chungAmt3'] - cutOff($c[$k]['chungAmt3']));

		$c[$k]['bonbuTot1'] = $c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1'];
		$c[$k]['bonbuTot2'] = $c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2'];
		$c[$k]['bonbuTot3'] = $c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3'];

		$c[$k]['chungAmt1'] = cutOff($c[$k]['chungAmt1']);
		$c[$k]['chungAmt2'] = cutOff($c[$k]['chungAmt2']);
		$c[$k]['chungAmt3'] = cutOff($c[$k]['chungAmt3']);

		/*
		$pageData[$pageNo]['chungAmt']['200'] = $pageData[$pageNo]['sugaAmt']['200'] - $pageData[$pageNo]['boninAmt']['200'];
		$pageData[$pageNo]['chungAmt']['500'] = $pageData[$pageNo]['sugaAmt']['500'] - $pageData[$pageNo]['boninAmt']['500'];
		$pageData[$pageNo]['chungAmt']['800'] = $pageData[$pageNo]['sugaAmt']['800'] - $pageData[$pageNo]['boninAmt']['800'];

		$pageData[$pageNo]['boninAmt']['200'] += ($pageData[$pageNo]['chungAmt']['200'] - $myF->cutOff($pageData[$pageNo]['chungAmt']['200']));
		$pageData[$pageNo]['boninAmt']['500'] += ($pageData[$pageNo]['chungAmt']['500'] - $myF->cutOff($pageData[$pageNo]['chungAmt']['500']));
		$pageData[$pageNo]['boninAmt']['800'] += ($pageData[$pageNo]['chungAmt']['800'] - $myF->cutOff($pageData[$pageNo]['chungAmt']['800']));

		$pageData[$pageNo]['chungAmt']['200'] = $myF->cutOff($pageData[$pageNo]['chungAmt']['200']);
		$pageData[$pageNo]['chungAmt']['500'] = $myF->cutOff($pageData[$pageNo]['chungAmt']['500']);
		$pageData[$pageNo]['chungAmt']['800'] = $myF->cutOff($pageData[$pageNo]['chungAmt']['800']);

		$chungAmt = $pageData[$pageNo]['chungAmt']['200'] + $pageData[$pageNo]['chungAmt']['500'] + $pageData[$pageNo]['chungAmt']['800'];
		*/

		$pdf->Text(14, 190, '총횟수 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['count'], '건').'          '
						   .'청구금액 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['chungAmt'], '원').'          '
						   .'초과금액 : '.$myF->numberFormat($confData[($backSugupjaJumin != '' ? $backSugupjaJumin :$sugupjaJumin)]['overAmt'], '원'));
		$backSugupjaJumin = '';
	}
	
	if($gDomain == 'dolvoin.net'){
		if($icon != ''){
			$exp = explode('.',$icon);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$icon, 270, 190, 20, null);	//기관 로고
			}
		}
	}

	$pdf->Output();

	include_once '../inc/_db_close.php';

	function getConfData($p_conn, $p_gubun, $p_code, $p_kind, $p_jumin, $p_date, $p_sugupjaKind){
		$sql = "select sum(t13_over_amt4) as overAmt, sum(t13_chung_amt4) as chungAmt"
			 . "  from t13sugupja"
			 . " where t13_ccode = '".$p_code
			 . "'  and t13_mkind = '".$p_kind
			 . "'  and t13_jumin = '".$p_jumin
			 . "'  and t13_pay_date = '".$p_date
			 . "'";

		if ($p_gubun != 'all'){
			$sql .= " and t13_bonin_yul = '".$p_sugupjaKind
				 .  "'";
		}

		$sql .= "   and t13_type = '2'";
		$conf = $p_conn->get_array($sql);

		$array['count'] = 0;
		$array['overAmt'] = $conf['overAmt'];
		$array['chungAmt'] = $conf['chungAmt'];

		return $array;
	}
?>
<script>self.focus();</script>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_function.php');
	require_once('../pdf/korean.php');
	require_once('../pdf/pdf_works_table.php');

	$conn->set_name('euckr');
	
	$mService = $_POST['mService'];
	$plan  = $_POST['mPlan'];
	$code  = $_POST['mCode'];
	$kind  = $_POST['mKind'];
	$year = $_POST['mYear'];
	$month = $_POST['mMonth'];
	$lastDay = $myF->lastDay($year, $month);
	
	define('__ROW_LIMIT__',184);

	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$centerName = $conn -> get_data($sql);

	$pdf = new MYPDF(strtoupper('l'));
	$pdf->AliasNbPages();
	$pdf->debug = $debug;
	$pdf->acctBox = true;
	$pdf->font_name_kor = '바탕';
	$pdf->font_name_eng = 'Batang';
	$pdf->AddUHCFont('바탕','Batang');
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	$pdf->centerName    = $centerName;		//기관명
	$pdf->year			= $year;			//년
	$pdf->month			= $month;			//월
	

	
	if ($plan == "plan"){
		$filed = "sugup";
	}else{
		$filed = "conf";
	}

	$sql = "select t01_svc_subcode as serviceCode
			,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName
			,      concat(t01_yname1, case when t01_yname2 != '' then concat('\n/', t01_yname2) else '' end) as yoyangsa
			,      m03_name as sugupja
			,      t01_sugup_date as sugupDate
			,      t01_sugup_fmtime as fromTime
			,      t01_sugup_totime as toTime
			,      t01_sugup_soyotime as soyoTime

			,      t01_conf_date as confDate
			,      t01_conf_fmtime as confFrom
			,      t01_conf_totime as confTo
			,	   CASE WHEN t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
							 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
						ELSE t01_conf_soyotime END as confSoyo
			  from t01iljung
			 inner join m03sugupja
				on m03_ccode = t01_ccode
			   and m03_mkind = t01_mkind
			   and m03_jumin = t01_jumin
			 where t01_ccode = '$code'
			   and t01_mkind = '$kind'
			   and t01_".$filed."_date like '$year$month%'
			   and t01_del_yn = 'N'";

	if ($mService != 'all'){
		$sql .= "
			   and t01_svc_subcode = '$mService'";
	}

	$sql .= "
			 order by serviceCode, yoyangsa, sugupja, fromTime, toTime, sugupDate";
	
	
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	$rows = 0;
	
	$pdf->AddPage();
	
	$pdf->SetLineWidth(0.2);
	$pdf->Rect($pdf->width-$pdf->left+13.5, $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-270), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-256.5), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);
	$pdf->Rect($pdf->width-($pdf->left+$pdf->width-243), $pdf->top, $pdf->width*0.05, $pdf->rowHeight * 3);

	$pdf->Line($pdf->width-($pdf->left+$pdf->width-256.5), $pdf->top + $pdf->rowHeight * 1, $pdf->width+13, $pdf->top + $pdf->rowHeight * 1);


	$pdf->SetFont($pdf->font_name_kor, "", 10);
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.125)+(($pdf->width*0.1 - $pdf->GetStringWidth("결")) / 2), $pdf->top+4.5, "결");
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.125)+(($pdf->width*0.1 - $pdf->GetStringWidth("결")) / 2), $pdf->top+12, "재");

	$pdf->SetFont($pdf->font_name_kor, "", 9);
	$pdf->Text($pdf->width-($pdf->left+$pdf->width*0.075)+(($pdf->width*0.1 - $pdf->GetStringWidth("담  당")) / 2), $pdf->top+4, "담  당");
	$pdf->Text($pdf->width-$pdf->left+(($pdf->width*0.150 - $pdf->GetStringWidth("기관장")) / 2), $pdf->top+4, "기관장");

	$pdf->SetFont($pdf->font_name_kor, "", 7);
	$pdf->SetXY($pdf->left, $pdf->GetY());

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			if ($tempRow != $row['serviceCode']."/".$row["yoyangsa"]."/".$row["sugupja"]."/".$row["fromTime"]."/".$row["toTime"]){
				$tempRow  = $row['serviceCode']."/".$row["yoyangsa"]."/".$row["sugupja"]."/".$row["fromTime"]."/".$row["toTime"];
				
				$rows ++;
					
				if ($cell[$rows-1]["yoy"].$cell[$rows-1]["su"] != $row["yoyangsa"].$row["sugupja"]){						
					$row_no ++;
					$cell[$rows]["no"] = $row_no;
					$cell[$rows]["yoy"] = $row["yoyangsa"];
					$cell[$rows]["su"] = $row["sugupja"];
				}else{
					$cell[$rows]["no"] = "";
					$cell[$rows]["yoy"] = "";
					$cell[$rows]["su"] = "";
				}
				
				$cell[$rows]["service"] = $row["serviceName"];
				$cell[$rows]["soyo"] = ($plan == 'plan' ? $row["soyoTime"] : $row["confSoyo"]);;
				if($plan == 'plan'){
					$cell[$rows]["time"] = subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2);
				}else {
					$cell[$rows]["time"] = subStr($row["confFrom"], 0, 2).":".subStr($row["confFrom"], 2, 2)."~".subStr($row["confTo"], 0, 2).":".subStr($row["confTo"], 2, 2);
				}
				for($j=1; $j<=$lastDay; $j++){
					$cell[$rows][$j] = 0;
				}
			}

			//$cell[$rows][intVal(subStr($row["sugupDate"], 6, 2))] ++;
			$cell[$rows][intVal(subStr($row["sugupDate"], 6, 2))] = ($plan == 'plan' ? number_format($row["soyoTime"] / 60, 1) : number_format($row["confSoyo"] / 60, 1));

			if ($tempWorkDay != $row["sugupDate"]){
				$cell[$rows]["workDay"] ++;
			}

			$cell[$rows]["workTime"] += ($plan == 'plan' ? $row["soyoTime"] : $row["confSoyo"]);
		}
	}
	
	$totalTime = 0;
	$rows = sizeOf($cell);
	
	for($row=1; $row<=$rows; $row++){
		
		/*
		if ($mService == 'all'){
			if ($tempService != $cell[$row]["service"]){
				$tempService  = $cell[$row]["service"];
			
				
				$cell[$row]["service"];
				
			
			}
		}	
		*/
		
		$headCol = $pdf->headColWidth();
			
		//마지막줄 라인그리기
		if($row == $rows or $pdf->getY() == __ROW_LIMIT__){
			$td = 'LRB';
		}else {
			$td = 'LR';
		}
		

		$pdf->SetXY($pdf->left,$pdf->GetY());
		
			
		//$pdf->SetXY($pdf->left+$headCol['w'][0], $get_Y+1.5);
		//$pdf->MultiCell($headCol['w'][1], 3.5, $cell[$row-1]["yoy"]."\n".substr($myF->issStyle($cell[$row-1]["su"]),0,8)."", 0 , 'C');
		
		if($pdf->getY() == 189 && $cell[$row]["no"] == ''){	//첫라인 순번이 없으면 
			$pdf->Cell($headCol['w'][0], $pdf->rowHeight, $cell[$row-1]["no"].' ',$td,0,'C');
			$pdf->Cell($headCol['w'][1], $pdf->rowHeight, ' '.$cell[$row-1]["yoy"],$td,0,'L');
			$pdf->Cell($headCol['w'][2], $pdf->rowHeight, $cell[$row-1]["su"],$td,0,'C');
		}else {
			$pdf->Cell($headCol['w'][0], $pdf->rowHeight, $cell[$row]["no"].' ',$td,0,'C');
			$pdf->Cell($headCol['w'][1], $pdf->rowHeight, ' '.$cell[$row]["yoy"],$td,0,'L');
			$pdf->Cell($headCol['w'][2], $pdf->rowHeight, $cell[$row]["su"],$td,0,'C');
		}
		$pdf->Cell(($headCol['w'][3]*0.25), $pdf->rowHeight, number_format($cell[$row]["soyo"] / 60, 1),1,0,'C');
		$pdf->Cell(($headCol['w'][3]*0.75), $pdf->rowHeight, $cell[$row]["time"],1,0,'C');
				
		
		for($i=1; $i<=31; $i++){
			$pdf->Cell($headCol['w'][$i+3], $pdf->rowHeight, ($cell[$row][$i] > 0 ? $cell[$row][$i] : ""),1,0,'C');
			$Time[$i] += $cell[$row][$i];
		}
		
		$pdf->Cell($headCol['w'][35], $pdf->rowHeight, ' '.$cell[$row]["workDay"],1,0,'C');
		$pdf->Cell($headCol['w'][36], $pdf->rowHeight, number_format($cell[$row]["workTime"] / 60, 1),1,0,'C');
		$pdf->Cell($headCol['w'][37], $pdf->rowHeight, '',1,1,'C');
		
		$totalworkDay += $cell[$row]["workDay"];
		$totalTime += $cell[$row]["workTime"];

		if ($row == $rows || $cell[$row+1]["no"] != "" && $cell[$row]["yoy"].$cell[$row]["su"] != $cell[$row+1]["yoy"].$cell[$row+1]["su"]){
			
			$pdf->SetXY($pdf->left,$pdf->GetY());
			$pdf->Cell($headCol['w'][0], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][1], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][2], $pdf->rowHeight, '',1,0,'C');
			$pdf->Cell($headCol['w'][3], $pdf->rowHeight, '계',1,0,'C');
			for($i=1; $i<=31; $i++){
				$pdf->Cell($headCol['w'][4], $pdf->rowHeight, ($Time[$i] != 0 ? number_format($Time[$i], 1) : ''),1,0,'C');
				$Time[$i] = 0;
			}
			$pdf->Cell($headCol['w'][35], $pdf->rowHeight, ' '.$totalworkDay,1,0,'C');
			$pdf->Cell($headCol['w'][36], $pdf->rowHeight, number_format($totalTime / 60, 1),1,0,'C');
			$pdf->Cell($headCol['w'][37], $pdf->rowHeight, '',1,1,'C');
			
			
			$totalworkDay = 0;
			$totalTime = 0;
		}
	}
	
	$pdf->Output();
	
	include_once('../inc/_db_close.php');
?>
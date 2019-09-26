<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');

	$mCode = $_GET["mCode"];
	$mKind = $_GET["mKind"];
	$mYear = $_GET["mYear"];
	$mMonth = $_GET["mMonth"];
	$mDate = $mYear.$mMonth;
	$mSugupja = ($_GET["mSugupja"] != "" ? $ed->de($_GET["mSugupja"]) : "");

	// 센터정보
	$sql = "select m00_cname"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";
	$centerName = $conn->get_data($sql);

	// 수급자명
	$sql = "select m03_name, m03_yoyangsa1_nm"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$mCode
		 . "'  and m03_mkind = '".$mKind
		 . "'  and m03_jumin = '".$mSugupja
		 . "'";
	$row = $conn->get_array($sql);
	$sugupjaName = $row[0];
	$yoyangsaName = $row[1];
	
	$sql = "select substring(t01_sugup_date, 7, 2) as sugupDate
			,      t01_yname1 as yname1
			,      t01_yname2 as yname2
			,      t01_yname3 as yname3
			,      t01_yname4 as yname4
			,      t01_yname5 as yname5
			,      t01_sugup_fmtime as fromTime
			,      t01_sugup_totime as toTime
			,      suga.name as sugaName
			  from t01iljung
			 inner join (
				   select m01_mcode2 as code, m01_suga_cont as name, m01_sdate as startDate, m01_edate as endDate
					 from m01suga
					 where m01_mcode = '$mCode'
					union all
				   select m11_mcode2 as code, m11_suga_cont as name, m11_sdate as startDate, m11_edate as endDate
					 from m11suga
					where m11_mcode = '$mCode'
				   ) as suga
			    on t01_suga_code1 = suga.code
			   and t01_sugup_date between suga.startDate and suga.endDate
			 where t01_ccode = '$mCode'
			   and t01_mkind = '$mKind'
			   and t01_jumin = '$mSugupja'
			   and t01_sugup_date like '$mDate%'
			   and t01_del_yn = 'N'
		  order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$service = 0;
	$serviceDay = '';
	$serviceTime = 0;
	
	// 데이타 배열 초기화
	for($i=1; $i<=31; $i++){
		$dataString[$i] = '';
	}

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$tempDay = ceil($row['sugupDate']);

		$service ++;
		$serviceDay .= $tempDay.',';
		$yoyangsaCount = 0;
			
		$dataString[$tempDay] .= subStr($row['fromTime'],0,2).':'.subStr($row['fromTime'],2,2)."~";
		$dataString[$tempDay] .= subStr($row['toTime'],0,2).':'.subStr($row['toTime'],2,2);
		$dataString[$tempDay] .= "\n";
		$dataString[$tempDay] .= $row["yname1"];

		if ($row["yname2"] != "") $yoyangsaCount ++;
		if ($row["yname3"] != "") $yoyangsaCount ++;
		if ($row["yname4"] != "") $yoyangsaCount ++;
		if ($row["yname5"] != "") $yoyangsaCount ++;
		if ($yoyangsaCount > 0) $dataString[$tempDay] .= " 외 ".$yoyangsaCount."명";
		
		$dataString[$tempDay] .= "\n";
		$dataString[$tempDay] .= $row["sugaName"];
		$dataString[$tempDay] .= "\n";
	}
	$conn->row_free();

	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	
	$pdf->SetFillColor(220,220,220);

	$pdf->AddPage('P','A4');
	$pdf->SetXY(14, 35);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect(14, 35, 182, 18);
	$pdf->SetLineWidth(0.2);

	$pdf->SetFont('굴림','',11);
	$pdf->SetX(14);
	$pdf->Cell(40,9,'장기요양대상자',1,0,'C');
	$pdf->Cell(55, 9, $sugupjaName, 1, 0, 'C');
	$pdf->Cell(36,9,'',1,0,'C');
	$pdf->Cell(51, 9, '', 1, 1, 'C');

	$pdf->SetX(14);
	$pdf->Cell(40,9,'담당자',1,0,'C');
	$pdf->Cell(55, 9, $yoyangsaName, 1, 0, 'C');
	$pdf->Cell(36,9,'출력일자',1,0,'C');
	$pdf->Cell(51, 9, date('Y-m-d', mkTime()), 1, 1, 'C');

	$pdf->SetXY(14, 58);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect(14, 58, 182, 164);
	$pdf->SetLineWidth(0.2);
	$pdf->SetFont('굴림','B',13);

	$pdf->Line(14,66.5,196,66.5);

	$pdf->Line(21,66,21,71);
	$pdf->Line(14,71,21,71);
	$pdf->Line(47,66,47,71);
	$pdf->Line(40,71,47,71);
	$pdf->Line(73,66,73,71);
	$pdf->Line(66,71,73,71);
	$pdf->Line(99,66,99,71);
	$pdf->Line(92,71,99,71);
	$pdf->Line(125,66,125,71);
	$pdf->Line(118,71,125,71);
	$pdf->Line(151,66,151,71);
	$pdf->Line(144,71,151,71);
	$pdf->Line(177,66,177,71);
	$pdf->Line(170,71,177,71);

	$pdf->Line(21,92,21,97);
	$pdf->Line(14,97,21,97);
	$pdf->Line(47,92,47,97);
	$pdf->Line(40,97,47,97);
	$pdf->Line(73,92,73,97);
	$pdf->Line(66,97,73,97);
	$pdf->Line(99,92,99,97);
	$pdf->Line(92,97,99,97);
	$pdf->Line(125,92,125,97);
	$pdf->Line(118,97,125,97);
	$pdf->Line(151,92,151,97);
	$pdf->Line(144,97,151,97);
	$pdf->Line(177,92,177,97);
	$pdf->Line(170,97,177,97);

	$pdf->Line(21,118,21,123);
	$pdf->Line(14,123,21,123);
	$pdf->Line(47,118,47,123);
	$pdf->Line(40,123,47,123);
	$pdf->Line(73,118,73,123);
	$pdf->Line(66,123,73,123);
	$pdf->Line(99,118,99,123);
	$pdf->Line(92,123,99,123);
	$pdf->Line(125,118,125,123);
	$pdf->Line(118,123,125,123);
	$pdf->Line(151,118,151,123);
	$pdf->Line(144,123,151,123);
	$pdf->Line(177,118,177,123);
	$pdf->Line(170,123,177,123);

	$pdf->Line(21,144,21,149);
	$pdf->Line(14,149,21,149);
	$pdf->Line(47,144,47,149);
	$pdf->Line(40,149,47,149);
	$pdf->Line(73,144,73,149);
	$pdf->Line(66,149,73,149);
	$pdf->Line(99,144,99,149);
	$pdf->Line(92,149,99,149);
	$pdf->Line(125,144,125,149);
	$pdf->Line(118,149,125,149);
	$pdf->Line(151,144,151,149);
	$pdf->Line(144,149,151,149);
	$pdf->Line(177,144,177,149);
	$pdf->Line(170,149,177,149);

	$pdf->Line(21,170,21,175);
	$pdf->Line(14,175,21,175);
	$pdf->Line(47,170,47,175);
	$pdf->Line(40,175,47,175);
	$pdf->Line(73,170,73,175);
	$pdf->Line(66,175,73,175);
	$pdf->Line(99,170,99,175);
	$pdf->Line(92,175,99,175);
	$pdf->Line(125,170,125,175);
	$pdf->Line(118,175,125,175);
	$pdf->Line(151,170,151,175);
	$pdf->Line(144,175,151,175);
	$pdf->Line(177,170,177,175);
	$pdf->Line(170,175,177,175);
	
	$pdf->Line(21,196,21,201);
	$pdf->Line(14,201,21,201);
	$pdf->Line(47,196,47,201);
	$pdf->Line(40,201,47,201);
	$pdf->Line(73,196,73,201);
	$pdf->Line(66,201,73,201);
	$pdf->Line(99,196,99,201);
	$pdf->Line(92,201,99,201);
	$pdf->Line(125,196,125,201);
	$pdf->Line(118,201,125,201);
	$pdf->Line(151,196,151,201);
	$pdf->Line(144,201,151,201);
	$pdf->Line(177,196,177,201);
	$pdf->Line(170,201,177,201);

	$pdf->SetX(14);
	$pdf->SetTextColor(255, 0, 0); 
	$pdf->Cell(26,8,'일',1,0,'C',true);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->Cell(26,8,'월',1,0,'C',true);
	$pdf->Cell(26,8,'화',1,0,'C',true);
	$pdf->Cell(26,8,'수',1,0,'C',true);
	$pdf->Cell(26,8,'목',1,0,'C',true);
	$pdf->Cell(26,8,'금',1,0,'C',true);
	$pdf->SetTextColor(0, 0, 255); 
	$pdf->Cell(26,8,'토',1,1,'C',true);
	$pdf->SetTextColor(0, 0, 0);
	
	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');

	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');

	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');
	
	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');
	
	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');

	$pdf->SetX(14);
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,0,'C');
	$pdf->Cell(26,26,'',1,1,'C');

	$pdf->AddUHCFont('바탕','Batang');
	$pdf->SetFont('바탕','B',20);
	$pdf->Text(60, 25, $mYear.'년 [ '.$mMonth.'월] 근무현황표');
	//$pdf->Text(88,275,'재가장기요양센터');
	$pdf->SetFont('바탕','B',13);
	$pdf->Text(14,235,'■ 서비스 제공현황');
	
	// 서비스 제공현황 영역
	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, 238, 196, 238); //Top
	$pdf->Line(14, 238, 14, 248); //Left
	$pdf->Line(196, 238, 196, 248); //Right
	$pdf->SetLineWidth(0.2);
	$pdf->Line(70, 238, 70, 248); //Center
	$pdf->Line(90, 238, 90, 248); //Center
	$pdf->Line(14, 248, 196, 248); //Bottom

	$pdf->SetXY(14, 241);
	$pdf->MultiCell(56, 4, '담당대상자', 0, 'C');
	$pdf->SetXY(70, 241);
	$pdf->MultiCell(20, 4, '주기', 0, 'C');
	$pdf->SetXY(90, 241);
	$pdf->MultiCell(106, 4, '제공일자', 0, 'C');

	$pdf->SetFont('바탕','',11);

	$top1 = 248;
	$top2 = 258;

	for($i=0; $i<=2; $i++){
		if ($row_count > 0){
			if ($service[$i] > 0){
				$pdf->SetLineWidth(0.6);
				$pdf->Line(14, $top1, 14, $top2); //Left
				$pdf->Line(196, $top1, 196, $top2); //Right
				$pdf->SetLineWidth(0.2);
				$pdf->Line(70, $top1, 70, $top2); //Center
				$pdf->Line(90, $top1, 90, $top2); //Center
				$pdf->Line(14, $top2, 196, $top2); //Top

				switch($i){
				case 0:
					$pdf->Text(17, $top1+6, '방문요양 ('.$serviceTime[$i].'분)');
					break;
				case 1:
					$pdf->Text(17, $top1+6, '방문목욕 ('.$serviceTime[$i].'분)');
					break;
				case 2:
					$pdf->Text(17, $top1+6, '방문간호 ('.$serviceTime[$i].'분)');
					break;
				}
				$pdf->SetXY(70, $top1+3);
				$pdf->MultiCell(20, 4, $service[$i].'회', 0, 'C');

				if (subStr($serviceDay[$i], strLen($serviceDay[$i]) - 1, 1) == ','){
					$serviceDay[$i] = subStr($serviceDay[$i], 0, strLen($serviceDay[$i]) - 1);
				}

				

				if ($pdf->GetStringWidth($serviceDay[$i]) > 106){
					$pdf->SetXY(90, $top1+1);
				}else{
					$pdf->SetXY(90, $top1+3);
				}
				$pdf->MultiCell(106, 4, ($row_count > 0 ? $serviceDay[$i] : ''));

				$top1 += 10;
				$top2 += 10;
			}
		}else{
			$pdf->SetLineWidth(0.6);
			$pdf->Line(14, $top1, 14, $top2); //Left
			$pdf->Line(196, $top1, 196, $top2); //Right
			$pdf->SetLineWidth(0.2);
			$pdf->Line(70, $top1, 70, $top2); //Center
			$pdf->Line(90, $top1, 90, $top2); //Center
			$pdf->Line(14, $top2, 196, $top2); //Top
			$top1 += 10;
			$top2 += 10;
		}
	}

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $top2 - 10, 196, $top2 - 10);
	
	// 센터명 출력
	$pdf->SetFont('바탕','B',13);
	$pdf->Text(196 - $pdf->GetStringWidth($centerName), $top2 - 5, $centerName);

	// 달력의 일자를 셋팅한다.
	$top = 70;

	$calTime   = mkTime(0, 0, 1, $mMonth, 1, $mYear);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //총일수 구하기
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
	$day       = 1; //화면에 표시할 화면의 초기값을 1로 설정

	for($i=1; $i<=$totalWeek; $i++){
		// 총 가로칸 만들기
		for ($j=0; $j<7; $j++){
			switch($j){
				case 0: $left = 15; break;
				case 1: $left = 41; break;
				case 2: $left = 67; break;
				case 3: $left = 93; break;
				case 4: $left = 119; break;
				case 5: $left = 145; break;
				case 6: $left = 171; break;
			}
			
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if ($day < 10){
					$dayString = ' '.$day;
				}else{
					$dayString = $day;
				}
				switch($j){
					case 0: $pdf->SetTextColor(255, 0, 0); break;
					case 6: $pdf->SetTextColor(0, 0, 255); break;
					default: $pdf->SetTextColor(0, 0, 0); break;
				}
				$pdf->SetFont('굴림', '', 11);
				$pdf->Text($left, $top, ($row_count > 0 ? $dayString : ''));
				
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('굴림', '', 9);
				$pdf->SetXY($left - 1, $top + 2);
				$pdf->MultiCell(26, 4, $dataString[$day], 0);
				$day ++;
			}else{
				$pdf->Text($left, $top, '');
			}
		}
		$top += 26;
	}

	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
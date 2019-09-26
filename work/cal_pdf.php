<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');

	$mCode = $_GET["code"];
	$mKind = $_GET["kind"];
	$mYear = $_GET["year"];
	$mMonth = $_GET["month"];
	$mDate = $mYear.$mMonth;
	$mJumin = $ed->de($_GET["sugupja"]);

	// 수급자정보 
	$sql = "select m03_name, m03_yoyangsa1_nm
			  from m03sugupja
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'
			   and m03_jumin = '$mJumin'";
	$sugupja = $conn->get_array($sql);
	
	// 데이타 배열 초기화
	for($i=1; $i<=31; $i++){
		$data[$i]["line"] = 0;
		$data[$i]["cont"] = '';
	}

	$sql = "select case t01_svc_subcode when '200' then '[요양]' when '500' then '[목욕]' when '800' then '[간호]' else '' end as serviceType
			,      t01_sugup_date as sugupDate
			,      t01_sugup_fmtime as fromTime
			,      t01_sugup_totime as toTime
			,      t01_sugup_soyotime as soyoTime
			,      t01_yname1 as yName1
			,      case when t01_yname2 != '' then 1 else 0 end + case when t01_yname3 != '' then 1 else 0 end + case when t01_yname4 != '' then 1 else 0 end + case when t01_yname5 != '' then 1 else 0 end as yoyCount
			  from t01iljung
			 where t01_ccode = '$mCode'
			   and t01_mkind = '$mKind'
			   and t01_jumin = '$mJumin'
			   and t01_sugup_date like '$mDate%'
			   and t01_del_yn = 'N'
			 order by sugupDate, fromTime, toTime";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		if ($day != intVal(subStr($row["sugupDate"], 6, 2))){
			$data[$day]["line"] = 2;
		}else{
			$data[$day]["line"] += 2;
		}
		$day = intVal(subStr($row["sugupDate"], 6, 2));
		$data[$day]["cont"] .= $row["serviceType"]." ".$row["yName1"].($row["yoyCount"] > 0 ? "외 ".$row["yoyCount"]."명" : "")."<br>"
					        .  subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2)."[".$row["soyoTime"]."분]<br>";
	}
	$conn->row_free();

	// 센터정보
	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	
	$pdf->SetFillColor(220,220,220);

	$pdf->AddPage('P','A4');
	$pdf->SetFont('굴림', 'B', 15);
	$pdf->SetXY(14, 21);
	//$pdf->Rect(14, 26, 182, 250);
	$pdf->Cell(182, 9, "【".$mYear."년 『".intVal($mMonth)."월』 서비스제공일정표】", 0, 1, 'C');
	$pdf->SetFont('굴림', 'B', 9);
	$pdf->Cell(3, 9, "", 0, 0);
	$pdf->Cell(88, 9, "▶ 수급자 : ".$sugupja[0], 0, 0, 'L');
	$pdf->Cell(91, 9, "▶ 담당자 : ".$sugupja[1], 0, 1, 'L');

	$calTime   = mkTime(0, 0, 1, $mMonth, 1, $mYear);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //총일수 구하기
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
	$day       = 1; //화면에 표시할 화면의 초기값을 1로 설정

	$top = 40;
	$height = 7;
	//MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]]) 

	$pdf->SetXY(14, $top);
	$pdf->SetTextColor(255, 0, 0);
	$pdf->setFillColor(241, 241, 241);
	$pdf->Cell(26, $height, "일", 1, 0, "C", true);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->Cell(26, $height, "월", 1, 0, "C", true);
	$pdf->Cell(26, $height, "화", 1, 0, "C", true);
	$pdf->Cell(26, $height, "수", 1, 0, "C", true);
	$pdf->Cell(26, $height, "목", 1, 0, "C", true);
	$pdf->Cell(26, $height, "금", 1, 0, "C", true);
	$pdf->SetTextColor(0, 0, 255);
	$pdf->Cell(26, $height, "토", 1, 0, "C", true);

	$top = 47;
	
	for($i=1; $i<=$totalWeek; $i++){
		$height = 0;

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

			$pdf->SetXY($left - 1, $top + 2);

			//if ($height == 0){
			//}

			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				$pdf->MultiCell(26, 4, $data[$day]["cont"], 1);
				$day ++;
			}else{
				$pdf->MultiCell(26, 4, "", 1);
			}
		}
		$top += 26;
	}

	/*
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
				$pdf->MultiCell(26, 4, $data[$day], 0);
				$day ++;
			}else{
				$pdf->Text($left, $top, '');
			}
		}
		$top += 26;
	}
	*/

	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
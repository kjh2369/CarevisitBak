<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	
	$code = $_GET['code'];		//기관코드
	$year = $_GET['year'];		//select 년
	$month = $_GET['month'];	//select 월
	
	require("../pdf/pdf_calendar_table.php");
	
	
	$pdf = new MYPDF(strtoupper('l'));	
		
	/**************************************************

		기본설정

	**************************************************/
	#케릭터변경
	$conn->set_name('euckr');
	#기관명
	$center_nm   = $conn->center_name($code);
	
	//$pdf->cpIcon   = '../ci/ci_'.$gDomainNM.'.jpg';
	//$pdf->cpName   = null;
	$pdf->ctIcon   = $conn->center_icon($code);			//기관아이콘
	$pdf->ctName   = $conn->center_name($code);			//기관명

	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);

	$pdf->font_name_kor = '굴림';
	$pdf->font_name_eng = 'Gulim';
	$pdf->AddUHCFont('굴림','Gulim');

	$pdf->Open();
	
	$pdf->SetFillColor(220,220,220);
	
	$pdf->year			= $year;		
	$pdf->month			= $month;		
	$pdf->center_nm     = $center_nm;  

	$pdf->AddPage(strtoupper('l'), 'A4');
	$pdf->SetFont('굴림','',11);
	
	$conn->set_name('euckr');
	
	
	// 일정 변수 설정
	$calTime   = mkTime(0, 0, 1, $pdf->month, 1, $pdf->year);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //총일수 구하기
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
	
	$preYYMM    = explode('-', $myF->dateAdd('month', -1, $year.'-'.$month.'-01', 'Y-m-d'));
	$preLastday = $myF->lastDay($preYYMM[0], $preYYMM[1]);
	$nextYYMM   = explode('-', $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d'));

	$date = $pdf->year.$pdf->month;
	

	// 일별 데이타 초기화
	for($i=1; $i<=$lastDay; $i++){
		$cal[$i][0]['subject']	= ''; //서비스명
	}

	$day = 1;
	$height	= $pdf->rowHeight;
	
	// 휴일일정
	$sql = 'select mdate as date
		    ,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate, 6) in (\''.$year.$month.'\', \''.$preYYMM[0].$preYYMM[1].'\', \''.$nextYYMM[0].$nextYYMM[1].'\')';
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
	//	echo $row['date'].'//';
		$holiday[$row['date']] = $row['nm'];
	}

	$conn->row_free();

	$sql = 'select cld_seq
			,      cld_no
			,	   cld_dt
			,	   cld_from
			,      cld_to
			,      cld_fulltime
			,      cld_subject
			,      cld_contents
			,      cld_reg_nm
			  from calendar
			 where org_no   = \''.$code.'\'
			   and cld_yymm in (\''.$year.$month.'\', \''.$preYYMM[0].$preYYMM[1].'\', \''.$nextYYMM[0].$nextYYMM[1].'\')
			   and del_flag = \'N\'
			 order by cld_from';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
		if ($day != str_replace('-','',$row[2])){
			$day = str_replace('-','',$row[2]);
			$seq = 0;
		}
		
		$tmp      = explode(':', $row['cld_from']);
		$fromTime = ($tmp[0] * 60 + $tmp[1]) / 30;

		if ($row['cld_fulltime'] == 'Y'){
			$time   = '';
			$style .= 'font-weight:bold;';
		}else{
			$time = $fromTime * 30;
			$hour = floor($time / 60);
			$hour = ($hour < 10 ? '0' : '').$hour;
			$min  = $time % 60;
			$min  = ($min < 10 ? '0' : '').$min;
			$time = $hour.':'.$min.' ';
		}
		
		//$cal[$day][$seq]['subject'] .= $pdf->_splitTextWidth($myF->utf($time.' '.$row['cld_subject']), $pdf->width*0.16)."\n";
		$cal[$day][$seq]['subject'] .= $time.' '.$row['cld_subject']."\n";
		
	}
	
	// 주차별 높이를 계산한다.
	/*
	for($i=1; $i<=$totalWeek; $i++){
		for($j=0; $j<7; $j++){
			$checkTop[$i] = 0;
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if (sizeOf($cal[$day]) > 0){
					for($k=0; $k<sizeOf($cal[$day]); $k++){	
						$checkTop[$i] += ($height * 2);
					}
				}
				$day++;
			}
		}
	}
	*/
	//print_r($checkTop);
	
	$col = $pdf->calranderColWidth();
	
	$top	= $pdf->top+2;
	$left	= $pdf->left;
	$height	= $pdf->rowHeight;

	$pdf->SetXY($left, $top);

	$day = 1; //화면에 표시할 화면의 초기값을 1로 설정
	$last_day = 0;

	for($i=1; $i<=$totalWeek; $i++){
		// 요일
		if ($i == 1){
			$pdf->SetFont('굴림','B',9);
			for($j=0; $j<7; $j++){
				$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
			}
			$pdf->SetFont('굴림','',9);
			$top += $height;
			$pdf->SetFillColor(238,238,238);
		}
		
		$left = $pdf->left;
		
		// 총 가로칸 만들기
		for($j=0; $j<7; $j++){
			$tempTop[$j] = 1;
			
			//전달 마지막 날을 빈칸에 보여주기
			if(($i == 1 && $j < $startWeek)){
				$str_day  = $preLastday - $startWeek + ($j + 1);
				
				$pdf->SetXY($left, $top);
				$pdf->SetFont('굴림','B',10);
				$pdf->SetTextColor(200,200,200);
				$pdf->Cell($col['w'][$j], $height-0.5, "$str_day", "T", 0, 'L');
				
				if ($holiday[$preYYMM[0].$preYYMM[1].($str_day<10?'0':'').$str_day] != ''){
					$pdf->SetFont('굴림','',9);
					$pdf->SetX($left+$pdf->GetStringWidth(" $str_day"));
					$pdf->Cell($col['w'][$j]-5, $height-0.5, $holiday[$preYYMM[0].$preYYMM[1].($str_day<10?'0':'').$str_day], "T", 1, 'L');
				}
				
				$pdf->SetFont('굴림','B',9);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetXY($left, $top + $height + $tempTop[$j] - 2);
				$pdf->MultiCell($col['w'][$j], 4.5, $cal[$preYYMM[0].$preYYMM[1].($str_day<10?'0':'').$str_day][0]['subject']);
				
			}

			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if (sizeOf($cal[$day]) > 0){
					if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
						$str_day = $day; 
						$color_yn = 'Y';
					}else{
						$str_day = $day;
						$color_yn = 'N';
					}
					
					for($k=0; $k<sizeOf($cal[$day]); $k++){
						switch($j){
						case 0:
							$pdf->SetTextColor(255,0,0);
						    break;
						case 6:
							$pdf->SetTextColor(0,0,255);
						    break;
						default:
							$pdf->SetTextColor(0,0,0);
						    break;
						}
						
						if($color_yn == 'Y'){
							$pdf->SetTextColor(255,0,0);
						}
						
						if ($k == 0){
							
							$pdf->SetXY($left, $top);
							$pdf->SetFont('굴림','B',10);
							$pdf->Cell($col['w'][$j], $height-0.5, "$str_day", "T", 0, 'L');
		
							if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
								$pdf->SetFont('굴림','',9);
								$pdf->SetX($left+$pdf->GetStringWidth(" $str_day"));
								$pdf->Cell($col['w'][$j]-5, $height-0.5, $holiday[$year.$month.($day<10?'0':'').$day], "T", 1, 'L');
							}

							$pdf->SetFont('굴림','B',9);
						

							$pdf->SetTextColor(0,0,0);
							
							$pdf->SetXY($left, $top + $height + $tempTop[$j] - 2);
							$pdf->MultiCell($col['w'][$j], 4.5, $cal[$year.$month.($day<10?'0':'').$day][$k]['subject']);
							$tempTop[$j] += ($height * 3.27);
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
			
			//다음달 첫날부터 빈칸에 보여주기
			if($i == $totalWeek && $j >= $lastWeek && $j < 6){
				
				$last_day ++ ;
				
				$str_day  = $last_day;
				$pdf->SetXY($left, $top);
				$pdf->SetFont('굴림','B',10);
				$pdf->SetTextColor(200,200,200);
				$pdf->Cell($col['w'][$j], $height-0.5, "$str_day", "T", 0, 'L');

				if ($holiday[$nextYYMM[0].$nextYYMM[1].($str_day<10?'0':'').$str_day] != ''){
					$pdf->SetFont('굴림','',9);
					$pdf->SetX($left+$pdf->GetStringWidth(" $str_day"));
					$pdf->Cell($col['w'][$j]-5, $height-0.5, $holiday[$nextYYMM[0].$nextYYMM[1].($str_day<10?'0':'').$str_day], "T", 1, 'L');
				}
				$pdf->SetFont('굴림','B',9);
				$pdf->SetTextColor(0,0,0);
				
				$pdf->SetXY($left, $top + $height+1);
				$pdf->MultiCell($col['w'][$j], 4.5, $cal[$nextYYMM[0].$nextYYMM[1].($str_day<10?'0':'').$str_day][0]['subject']);
			}
			
		}
		
		
		$tempHeight = 0;
		for($j=0; $j<7; $j++){
			if ($tempHeight < $tempTop[$j]) $tempHeight = $tempTop[$j];
		}
	
		//if ($tempHeight < $minimum_height) $tempHeight = $minimum_height;

		if ($tempHeight > 0){
			$top += ($height + $tempHeight);
		}else{
			$top += ($height * 2.5);
		}
	}
	
	$cal = array();
	
	// 요일별 라인
	drawLine($pdf, $col, $top);

	// 테두리
	drawBorder($pdf, $top-$height*6+13);

	$pdf->Output();

	include('../inc/_db_close.php');
	
	
	// 달력의 요일별 라인을 그린다.
	function drawLine($pdf, $col, $top){
		$pdf->SetLineWidth(0.2);

		$left = $pdf->left;

		for($i=0; $i<7; $i++){
			$left += $col['w'][$i];
			$pdf->Line($left, $pdf->top+2, $left, $top);
		}
	}

	// 달력의 전체 테두리를 그린다.
	function drawBorder($pdf, $height){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->top+2, $pdf->width, $height);
		$pdf->SetLineWidth(0.2);
	}
?>

<script>self.focus();</script>

<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../pdf/iljung_show.php');

	$conn->set_name('euckr');

	/*************************

		파라메타

	*************************/
	$code     = $_GET['code'];
	$kind     = $_GET['kind'];
	$year     = $_GET['year'];
	$month    = $_GET['month'];
	$jumin    = $_GET['target'];
	$svc_kind = $_GET['service'];



	/*************************

		휴일리스트

	*************************/
	$sql = 'select *
			  from tbl_holiday
			 where left(mdate, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();
	$holiday_count = $conn->row_count();

	for($i=0; $i<$holiday_count; $i++){
		$row = $conn->select_row($i);
		$holiday[$row['mdate']] = $row['holiday_name'];
	}

	$conn->row_free();



	/*************************

		PDF 작성

	*************************/
	$pdf = new MYPDF('L');
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);



	#
	$pdf->year     = $year;
	$pdf->month    = $month;
	$pdf->svc_text = $myF->euckr($svc_kind);




	#페이지 추가
	$pdf->MY_ADDPAGE();

	#일정 변수 설정
	$calTime   = mkTime(0, 0, 1, $month, 1, $year);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //총일수 구하기
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
	$day       = 1; #화면에 표시할 화면의 초기값을 1로 설정

	#넓이
	$col_width = $pdf->calrander_width();

	#요일
	$col_weekday = $pdf->calrander_weekday();

	$top	= $pdf->top+15;
	$left	= $pdf->left;
	$height	= $pdf->row_height;

	$pdf->SetXY($left, $top);

	for($i=1; $i<=$totalWeek; $i++){
		// 요일
		if ($i == 1){
			$pdf->SetFont($pdf->font_name_kor,'B',9);
			for($j=0; $j<7; $j++){
				switch($j){
					case 0:  $pdf->SetTextColor(255,0,0); break;
					case 6:  $pdf->SetTextColor(0,0,255); break;
					default: $pdf->SetTextColor(0,0,0);
				}

				$pdf->Cell($col_width[$j], $height, $col_weekday[$j], 1, $j < 6 ? 0 : 1, 'C', true);

				$pdf->SetTextColor(0,0,0);
			}
			$pdf->SetFont($pdf->font_name_kor,'',9);
			$top += $height;
			$pdf->SetFillColor(238,238,238);
		}

		$left = $pdf->left;

		for($j=0; $j<7; $j++){
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				$day ++;
			}else{
			}
		}
	}



	/*************************

		PDF 출력

	*************************/
	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>
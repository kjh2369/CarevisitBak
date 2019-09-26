<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	$code   = $_GET['code'];		//기관코드
	$year   = $_GET['year'];		//년
	$month  = $_GET['month'];		//월
	$week   = $_GET['week'];		//요일구분
	$fromDt = $_GET['fromDt'];		//주간 시작일
	$toDt   = $_GET['toDt'];		//주간 종료일
	$mode   = $_GET['mode'];		//구분

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

	$pdf->mode			= $mode;
	$pdf->year			= $year;
	$pdf->month			= $month;
	$pdf->week			= $week;
	$pdf->fromDt		= $fromDt;
	$pdf->toDt			= $toDt;
	$pdf->center_nm     = $center_nm;

	$pdf->AddPage(strtoupper('l'), 'A4');
	$pdf->SetFont('굴림','',11);

	$conn->set_name('euckr');


	// 일정 변수 설정
	//$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
	$calTime	= mktime(0, 0, 1, $month-1, 1, $year);
	$lastDay	= date('t', $calTime);			//총일수 구하기
	//$weekly = array("일","월","화","수","목","금","토");
	//$color = array("red","black","black","black","black","black","blue");



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

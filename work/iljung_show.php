<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../pdf/iljung_show.php');

	$conn->set_name('euckr');

	/*************************

		�Ķ��Ÿ

	*************************/
	$code     = $_GET['code'];
	$kind     = $_GET['kind'];
	$year     = $_GET['year'];
	$month    = $_GET['month'];
	$jumin    = $_GET['target'];
	$svc_kind = $_GET['service'];



	/*************************

		���ϸ���Ʈ

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

		PDF �ۼ�

	*************************/
	$pdf = new MYPDF('L');
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);



	#
	$pdf->year     = $year;
	$pdf->month    = $month;
	$pdf->svc_text = $myF->euckr($svc_kind);




	#������ �߰�
	$pdf->MY_ADDPAGE();

	#���� ���� ����
	$calTime   = mkTime(0, 0, 1, $month, 1, $year);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //���ϼ� ���ϱ�
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //���ۿ��� ���ϱ�
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //�� �� ������ ���ϱ�
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //������ ���� ���ϱ�
	$day       = 1; #ȭ�鿡 ǥ���� ȭ���� �ʱⰪ�� 1�� ����

	#����
	$col_width = $pdf->calrander_width();

	#����
	$col_weekday = $pdf->calrander_weekday();

	$top	= $pdf->top+15;
	$left	= $pdf->left;
	$height	= $pdf->row_height;

	$pdf->SetXY($left, $top);

	for($i=1; $i<=$totalWeek; $i++){
		// ����
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

		PDF ���

	*************************/
	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>
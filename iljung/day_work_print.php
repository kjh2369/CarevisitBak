<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	require('../pdf/korean.php');

	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mDate = $_GET['mDate'];
	$mService = $_GET['mService'];

	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();

	$pdf->SetFillColor(238,238,238);

	$sql = "select t01_sugup_date
			,      t01_sugup_fmtime"
		 . ",      t01_sugup_totime
			,      t01_suga_code1"
		 . ",      m03_name"
		 . ",      t01_mem_nm1 as yoyangsaName1"
		 . ",      t01_mem_nm2 as yoyangsaName2"
		 . "  from t01iljung"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_sugup_date = '".$mDate
		 . "'  and t01_del_yn = 'N'";

	if ($mService == 'all'){
	}else if ($mService == '20'){
		$sql .= " and cast(t01_svc_subcode as unsigned) > 20
				  and cast(t01_svc_subcode as unsigned) < 30";
	}else if ($mService == '30'){
		$sql .= " and cast(t01_svc_subcode as unsigned) > 30
				  and cast(t01_svc_subcode as unsigned) < 40";
	}else{
		$sql .= " and t01_svc_subcode = '".$mService."'";
	}

	$sql.= " order by t01_sugup_fmtime"
		 . ",         t01_sugup_totime"
		 . ",         m03_name";

	$conn->set_name('euckr');
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	$page_count = 0;
	$total_page = ceil($row_count / 30);

	switch($mService){
	case 'all': $titleGubun = '전체'; break;
	case '200': $titleGubun = '방문요양'; break;
	case '500': $titleGubun = '방문목욕'; break;
	case '800': $titleGubun = '방문간호'; break;
	case '20' : $titleGubun = '바우처'; break;
	case '30' : $titleGubun = '기타유료'; break;
	}

	if ($row_count == 0){
		$pdf->AddPage();
		$pdf->SetXY(14, 21);

		$pdf->SetFont('굴림','b',12);
		$pdf->Cell(150,5,'['.$titleGubun.'] '.SubStr($mDate,0,4).'년 '.Ceil(SubStr($mDate,4,2)).'월 '.Ceil(SubStr($mDate,6,2)).'일 서비스 방문일정(1/1)',0,0,'L');

		$pdf->SetFont('굴림','',9);
		$pdf->Cell(32,5,'전체:0건',0,1,'R');

		$pdf->SetX(14);
		$pdf->SetFont('굴림','b',9);
		$pdf->Cell(10,8,'No',1,0,'C', true);
		$pdf->Cell(30,8,'시간',1,0,'C', true);
		$pdf->Cell(20,8,'수급자',1,0,'C', true);
		$pdf->Cell(41,8,'서비스',1,0,'C', true);
		$pdf->Cell(30,8,'요양보호사',1,0,'C', true);
		$pdf->Cell(51,8,'비고',1,1,'C', true);
	}else{
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($i % 30 == 0){
				$page_count ++;

				$pdf->AddPage();
				$pdf->SetXY(14, 21);

				$pdf->SetFont('굴림','b',12);
				$pdf->Cell(150,5,'['.$titleGubun.'] '.SubStr($mDate,0,4).'년 '.Ceil(SubStr($mDate,4,2)).'월 '.Ceil(SubStr($mDate,6,2)).'일 서비스 방문일정('.$page_count.'/'.$total_page.')',0,0,'L');

				$pdf->SetFont('굴림','',9);
				$pdf->Cell(32,5,'전체:'.$row_count.'건',0,1,'R');

				$pdf->SetX(14);
				$pdf->SetFont('굴림','b',9);
				$pdf->Cell(10,8,'No',1,0,'C', true);
				$pdf->Cell(30,8,'시간',1,0,'C', true);
				$pdf->Cell(20,8,'수급자',1,0,'C', true);
				$pdf->Cell(41,8,'서비스',1,0,'C', true);
				$pdf->Cell(30,8,'요양보호사',1,0,'C', true);
				$pdf->Cell(51,8,'비고',1,1,'C', true);
			}

			$seq      = $i + 1;
			$timeRang = subStr($row['t01_sugup_fmtime'],0,2).':'.subStr($row['t01_sugup_fmtime'],2,2).'~'.subStr($row['t01_sugup_totime'],0,2).':'.subStr($row['t01_sugup_totime'],2,2);
			$sugupja  = $row['m03_name'];
			$sugaName = $conn->get_suga($mCode, $row['t01_suga_code1'], $row['t01_sugup_date']); //수가명
			$yoyangsa = $row['yoyangsaName1'];

			if ($row['yoyangsaName2'] != ''){
				$yoyangsa .= '/'.$row['yoyangsaName2'];
			}

			$pdf->SetX(14);
			$pdf->SetFont('굴림','',9);
			$pdf->Cell(10,8,''.$seq.'',1,0,'C', false);
			$pdf->Cell(30,8,$timeRang,1,0,'C', false);
			$pdf->Cell(20,8,$sugupja,1,0,'C', false);
			$pdf->Cell(41,8,$sugaName,1,0,'L', false);
			$pdf->Cell(30,8,$yoyangsa,1,0,'C', false);
			$pdf->Cell(51,8,'',1,1,'C', false);
		}
	}

	$conn->row_free();
	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
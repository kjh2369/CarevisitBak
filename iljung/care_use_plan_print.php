<?
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+20, $pdf->GetY()+3);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "일자", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "이용여부", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "일자", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "이용여부", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "일자", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "이용여부", 1, 1, "C");
	
	$sl = '';

	foreach($svcGbn as $svcIdx => $svc){
		
		if ($svc){
			if (Is_Numeric(StrPos($svc,'_'))){
				$tmp = Explode('_',$svc);
				$svcCd = $tmp[0];
				$subCd = $tmp[1];
			}else{
				$svcCd = $svc;
				$subCd = '';
			}
		}	
		
		if($svcCd == 'all' ||
		   $svcCd == ''    ){
		}else {
			if (!Empty($sl)){
				$sl .= ' UNION ALL ';
			}
			
			$sl .= 'SELECT right(t01_sugup_date, 2) as day
					  FROM t01iljung
					 WHERE t01_ccode = \''.$code.'\'
					   AND t01_mkind = \'0\'
					   AND t01_svc_subcode = \''.$svcCd.'\'
					   AND t01_del_yn = \'N\'
					   AND LEFT(t01_sugup_date, 6) = \''.$year.$month.'\'
					   AND t01_jumin = \''.$jumin.'\'';
		}
		
		
		unset($svcCd);
	}
	
	$sql = 'SELECT DISTINCT iljung.day
			  FROM ( '.$sl.' ) AS iljung';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$iljung[$row['day']] = $row['day'];
		//echo $iljung[$row['day']].'//';
		
	}
	

	$conn -> row_free();
	
	$row_h = 125.5;

	for($i=1; $i<=10; $i++){
		
		$day = $i<10?'0'.$i:$i;
		
		if($i < 11){
			if($iljung[$day] == $day){
				$pdf->Text($pdf->left+53,$row_h, '○');
			}else {
				$pdf->Text($pdf->left+54,$row_h, 'x');
			}
		}
		
		if(($i+10) < 21){
			if($iljung[$day+10] == ($day+10)){
				$pdf->Text($pdf->left+100,$row_h, '○');
			}else {
				$pdf->Text($pdf->left+101,$row_h, 'x');
			}
		}
		
		if($i < 31){
			if($iljung[$day+20] == ($day+20)){
				$pdf->Text($pdf->left+147,$row_h, '○');
			}else {
				$pdf->Text($pdf->left+148,$row_h, 'x');
			}
		}
		
		$pdf->SetX($pdf->left+20);
		$pdf->Cell($pdf->width * 0.12, $pdf->row_height, ''.$i, 1, 0, "C");
		$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.12, $pdf->row_height, ''.($i+10), 1, 0, "C");
		$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.12, $pdf->row_height, ''.($i+20), 1, 0, "C");
		$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "", 1, 1, "C");

		$row_h += $pdf->row_height;	
		
	}

	if($iljung['31'] == 31){
		$pdf->Text($pdf->left+147,$row_h, '○');
	}else {
		$pdf->Text($pdf->left+148,$row_h, 'x');
	}

	$p_year = substr($printDT,0,4);
	$p_month = substr($printDT,5,2);
	$p_day = substr($printDT,8,2);

	$pdf->SetX($pdf->left+20+$pdf->width*0.52);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '31', 1, 0, "C");
	$pdf->Cell($pdf->width * 0.14, $pdf->row_height, "", 1, 1, "C");
	
	$pdf->SetXY($pdf->left+20, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '※ 기타 :', 0, 0, "L");
	$pdf->Cell($pdf->width * 0.63, $pdf->row_height, '', 0, 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY()+7);
	$pdf->Cell($pdf->width * 0.78, $pdf->row_height, '※ 예) 이용(O), 미이용(x)', 0, 0, "L");
	
	$pdf->SetXY($pdf->width*0.43, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, $p_year, 0, 0, "C");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, ($p_month<10 ?str_replace('0','',$p_month) : $p_month), 0, 0, "C");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, ($p_day<10 ?str_replace('0','',$p_day) : $p_day), 0, 0, "C");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "C");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 15);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "이   용   자", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['name'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(인)", 0, 0, "L");

	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 15);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "장기요양기관장", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['manager'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(인)", 0, 0, "L");

	set_array_text($pdf, $pos);
	unset($pos);
	unset($iljung);
?>
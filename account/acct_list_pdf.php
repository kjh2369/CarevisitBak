<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$name = $conn->_storeName($code);
	
	//입,출금내역집계
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);


	if ($var['gbn'] == 'I'){
		$field = 'income';
		$title = '수 입';
	}else{
		$field = 'outgo';
		$title = '지 출';
	}

	

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor, "B", 30);
	$pdf->Cell($pdf->width, $pdf->row_height * 3, $title." 결 의 서", 0, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width, $pdf->row_height * 1.5, "기관명 : ".$name, 0, 1, "L");

	$liY = $pdf->GetY();
	
	$pdf->SetXY($pdf->left + $pdf->width * 0.2, $liY);
	$pdf->Cell($pdf->width * 0.4, $pdf->row_height * 7, "No", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.4, $pdf->row_height * 7, "일자", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height * 7, "증빙서번호", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "항목", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "금액", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "부과세", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "합계", 1, 1, "C");
	
	/*

	$sql = 'SELECT '.$field.'_acct_dt AS acct_dt
			,      proof_no
			,      '.$field.'_item_cd AS item_cd
			,      cate.nm3 AS item_nm
			,      SUM('.$field.'_amt) AS amt
			,      SUM('.$field.'_vat) AS vat
			  FROM center_'.$field.' AS ie
			 INNER JOIN ie_category AS cate
				ON cate.gbn = \''.$gbn.'\'
			   AND CONCAT(cate.cd1,cate.cd2,cate.cd3) = ie.'.$field.'_item_cd
			 WHERE org_no = \''.$code.'\'
			   AND DATE_FORMAT('.$field.'_acct_dt,\'%Y%m\') = \''.$year.$month.'\'
			   AND del_flag = \'N\'
			 GROUP BY '.$field.'_acct_dt, proof_no, '.$field.'_item_cd
			 ORDER BY CASE WHEN IFNULL(proof_no,\'\') != \'\' THEN 1 ELSE 2 END, proof_no, item_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['acct_dt'].chr(2)
			  .  $row['proof_no'].chr(2)
			  .  $row['item_nm'].chr(2)
			  .  $row['amt'].chr(2)
			  .  $row['vat'].chr(1);

		$pdf->SetXY($pdf->left + $pdf->width * 0.2, $liY);
		$pdf->Cell($pdf->width * 0.4, $pdf->row_height * 7, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.4, $pdf->row_height * 7, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.06, $pdf->row_height * 7, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "", 1, 0, "C");
		$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "", 1, 1, "C");

	}
	

	$conn->row_free();

	*/
	
	include_once('../inc/_db_close.php');
?>
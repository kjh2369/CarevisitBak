<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$name = $conn->_storeName($code);

	if ($var['gbn'] == 'I'){
		$field = 'income';
		$title = '수 입';
	}else{
		$field = 'outgo';
		$title = '지 출';
	}

	if (!Empty($var['docNo'])){
		$sql = 'SELECT '.$field.'_acct_dt
				,      '.$field.'_item_cd
				,      '.$field.'_item
				,      '.$field.'_amt
				,      '.$field.'_vat
				,      proof_year
				,      proof_no
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND CONCAT(DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\'),proof_no) = \''.$var['docNo'].'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (Empty($tmp[''.$field.'_acct_dt'])){
				$tmp[''.$field.'_acct_dt'] = $row[''.$field.'_acct_dt'];
			}

			if (Empty($tmp[''.$field.'_item_cd'])){
				$tmp[''.$field.'_item_cd'] = $row[''.$field.'_item_cd'];
			}

			if (Empty($tmp['proof_year'])){
				$tmp['proof_year'] = $row['proof_year'];
			}

			if (Empty($tmp['proof_no'])){
				$tmp['proof_no'] = $row['proof_no'];
			}

			$tmp[''.$field.'_amt'] += $row[''.$field.'_amt'];
			$tmp[''.$field.'_vat'] += $row[''.$field.'_vat'];
			$tmp[''.$field.'_item'] .= (!Empty($tmp[''.$field.'_item']) ? ',' : '').$row[''.$field.'_item'];
		}

		$conn->row_free();

		$row = $tmp;
		$row['cnt'] = $rowCount;
		UnSet($tmp);
	}else{
		$sql = 'SELECT *
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND '.$field.'_ent_dt = \''.$var['entDt'].'\'
				   AND '.$field.'_seq    = \''.$var['seq'].'\'';
		$row = $conn->get_array($sql);
	}

	$sql = 'SELECT cd1 AS cd
			,      nm1 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$var['gbn'].'\'
			   AND cd1 = \''.SubStr($row[$field.'_item_cd'],0,2).'\'
			 LIMIT 1';
	$cate1 = $conn->get_array($sql);

	$sql = 'SELECT cd2 AS cd
			,      nm2 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$var['gbn'].'\'
			   AND cd2 = \''.SubStr($row[$field.'_item_cd'],2,2).'\'
			 LIMIT 1';
	$cate2 = $conn->get_array($sql);

	$sql = 'SELECT cd3 AS cd
			,      nm3 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$var['gbn'].'\'
			   AND cd3 = \''.SubStr($row[$field.'_item_cd'],4,3).'\'';
	$cate3 = $conn->get_array($sql);

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor, "B", 30);
	$pdf->Cell($pdf->width, $pdf->row_height * 3, $title." 결 의 서", 0, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width, $pdf->row_height * 1.5, "기관명 : ".$name, 0, 1, "L");

	$liY = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->SetFont($pdf->font_name_kor, "B", 20);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, "증빙서번호", 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 5, "", 1, 1, "C");

	$pdf->SetXY($pdf->left + $pdf->width * 0.2, $liY);
	$pdf->Cell($pdf->width * 0.4, $pdf->row_height * 7, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height * 7, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, "", 1, 1, "C");

	$liY = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->MultiCell($pdf->width * 0.2, 10, $row[$field.'_acct_dt']."\n".$row['proof_no'], 0, "C");

	$pdf->SetXY($pdf->left + $pdf->width * 0.23, $liY);
	$pdf->MultiCell($pdf->width * 0.37, 10, $row['proof_year']."년\n아래와 같이 ".Str_Replace(' ', '', $title)."함.", 0, "L");

	$pdf->SetXY($pdf->left + $pdf->width * 0.6, $liY);
	$pdf->MultiCell($pdf->width * 0.06, 10, "결\n재", 0, "C");

	$pdf->SetXY($pdf->left + $pdf->width * 0.66, $liY);
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 5, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 5, "", 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, "관", 1, 0, "C");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height * 2, $cate1['nm'], 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);
	$pdf->Cell($pdf->width * 0.33, $pdf->row_height * 2, "발의", 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, $row[$field.'_acct_dt'], 1, 1, "C");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, "항", 1, 0, "C");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height * 2, $cate2['nm'], 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);
	$pdf->Cell($pdf->width * 0.33, $pdf->row_height * 2, "현금출납부등재", 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, $row[$field.'_acct_dt'], 1, 1, "C");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, "목", 1, 0, "C");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height * 2, $cate3['nm'], 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);
	$pdf->Cell($pdf->width * 0.33, $pdf->row_height * 2, "총계정원장등재", 1, 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.17, $pdf->row_height * 2, $row[$field.'_acct_dt'], 1, 1, "C");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, Str_Replace(' ', '', $title)."금액", 1, 0, "C");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.6, $pdf->row_height * 2, "금".$myF->euckr($myF->no2Kor($row[$field.'_amt']+$row[$field.'_vat']))."원정(".Number_Format($row[$field.'_amt']+$row[$field.'_vat']).")", "LTB", 0, "L");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, (!Empty($row['cnt']) ? $row['cnt']."건" : ""), "RTB", 1, "R");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 6, "적   요", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height * 6, "", 1, 1, "C");

	$liY = $pdf->GetY();

	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->SetXY($pdf->left + $pdf->width * 0.2, $liY - $pdf->row_height * 6 + 0.5);
	$pdf->MultiCell($pdf->width * 0.8, 6, $row[$field.'_item'], 0, "L");
	$pdf->SetFont($pdf->font_name_kor, "B", 20);

	$pdf->SetXY($pdf->left, $liY);
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height * 2, "비   고", 1, 0, "C");
	$pdf->SetFont($pdf->font_name_kor, "", 15);
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height * 2, $row['other'], 1, 1, "L");

	#$liY = $pdf->GetY();

	#$pdf->SetFont($pdf->font_name_kor, "", 15);
	#$pdf->SetXY($pdf->left + $pdf->width * 0.2, $liY - $pdf->row_height * 2);
	#$pdf->MultiCell($pdf->width * 0.8, 6, $row['other'], 0, "L");

	include_once('../inc/_db_close.php');
?>
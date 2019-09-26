<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	서비스계획서
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$fromDt	= '';
	$toDt	= '';

	if ($var['def']){
		if (is_numeric(StrPos($var['def'],'/'))){
			$tmpDt = Explode('/',$var['def']);
			$fromDt	= str_replace('-','',$tmpDt[0]);
			$toDt	= str_replace('-','',$tmpDt[1]);
		}
	}
	
	$orgNo = $_SESSION['userCenterCode'];

	$col = $pdf->_colWidth();
	$rowH = $pdf->row_height * 3;

	if (!$var['order']) $var['order'] = 'DESC';

	$sql = 'SELECT	proc.proc_seq
			,		proc.counsel_dt
			,		CT.name AS counsel_gbn
			,		proc.counsel_text AS counsel_text
			,		proc.counsel_nm
			FROM	hce_proc_counsel AS proc
			INNER	JOIN	hce_gbn AS CT
					ON		CT.type		= \'CT\'
					AND		CT.code		= proc.counsel_gbn
					AND		CT.use_yn	= \'Y\'
			WHERE	proc.org_no		= \''.$orgNo.'\'
			AND		proc.org_type	= \''.$hce->SR.'\'
			AND		proc.IPIN		= \''.$hce->IPIN.'\'
			AND		proc.rcpt_seq	= \''.$hce->rcpt.'\'
			AND		proc.del_flag	= \'N\'';

	if ($fromDt && $toDt){
		$sql .= '
			AND		proc.counsel_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
	}

	$sql .= '
			ORDER	BY counsel_dt '.$var['order'];

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$X = $pdf->GetX();
		$Y = $pdf->GetY();

		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y + 0.8);
		$pdf->MultiCell($col[2], 3.5, stripslashes($row['counsel_text']));

		$H = $pdf->GetY() + 0.8;
		$h = $H - $Y;

		//$month = IntVal(Date("m",StrToTime($row['counsel_dt'])));
		//$day   = IntVal(Date("d",StrToTime($row['counsel_dt'])));

		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell($col[0], $h, $myF->dateStyle($row['counsel_dt'],'.'), 1, 0, "C");
		$pdf->Cell($col[1], $h, $row['counsel_gbn'], 1, 0, "C");
		$pdf->Cell($col[2], $h, "", 1, 0, "C");
		$pdf->Cell($col[3], $h, $row['counsel_nm'], 1, 0, "C");
		$pdf->Cell($col[4], $h, "", 1, 1, "C");

		if ($H + $rowH > $pdf->height){
			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor,'',9);
		}
	}

	$conn->row_free();

	while(true){
		$Y = $pdf->GetY() + $rowH;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0], $rowH, "", 1, 0, "C");
		$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
		$pdf->Cell($col[2], $rowH, "", 1, 0, "C");
		$pdf->Cell($col[3], $rowH, "", 1, 0, "C");
		$pdf->Cell($col[4], $rowH, "", 1, 1, "C");

		if ($Y + $rowH > $pdf->height) break;
	}
?>
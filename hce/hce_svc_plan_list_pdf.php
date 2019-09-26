<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사례회의록
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	$col = $pdf->_colWidth();
	$rowH = $pdf->row_height * 1.5;

	//사례회의록
	$sql = 'SELECT	plan_seq,plan_dt,planer
			FROM	hce_plan_sheet
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag= \'N\'
			ORDER	BY plan_seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		@$row = $conn->select_row($i);

		if ($i > 0 && $i % 24 == 0) $pdf->MY_ADDPAGE();

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0], $rowH, $row['plan_seq']."회차", 1, 0, "C");
		$pdf->Cell($col[1], $rowH, $myF->dateStyle($row['plan_dt'],'.'), 1, 0, "C");
		$pdf->Cell($col[2], $rowH, $row['planer'], 1, 0, "C");
		$pdf->Cell($col[3], $rowH, "", 1, 1, "C");
	}

	$conn->row_free();

	Unset($col);
?>
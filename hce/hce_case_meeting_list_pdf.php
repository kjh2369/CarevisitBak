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

	//판정구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CMT\'';

	$gbn = $conn->_fetch_array($sql,'code');

	//사례회의록
	$sql = 'SELECT	*
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		del_flag= \'N\'
			ORDER	BY meet_seq DESC';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = $rowCnt;

	for($i=0; $i<$rowCnt; $i++){
		@$row = $conn->select_row($i);

		if ($i > 0 && $i % 24 == 0) $pdf->MY_ADDPAGE();

		$attendeeCnt = SizeOf(Explode('&',$row['attendee']));

		if ($row['decision_gbn'] == '1') $decisionGbn = '제공';
		if ($row['decision_gbn'] == '2') $decisionGbn = '종결';

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0], $rowH, $no."회차", 1, 0, "C");
		$pdf->Cell($col[1], $rowH, $gbn[$row['meet_gbn']]['name'], 1, 0, "C");
		$pdf->Cell($col[2], $rowH, $myF->dateStyle($row['meet_dt'],'.'), 1, 0, "C");
		$pdf->Cell($col[3], $rowH, $row['examiner'], 1, 0, "C");
		$pdf->Cell($col[4], $rowH, $attendeeCnt."명", 1, 0, "C");
		$pdf->Cell($col[5], $rowH, $decisionGbn, 1, 0, "C");
		$pdf->Cell($col[6], $rowH, $myF->dateStyle($row['decision_dt'],'.'), 1, 0, "C");
		$pdf->Cell($col[7], $rowH, "", 1, 1, "C");

		$no --;
	}

	$conn->row_free();

	Unset($col);
?>
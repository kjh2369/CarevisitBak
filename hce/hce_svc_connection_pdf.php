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

	$orgNo = $_SESSION['userCenterCode'];

	$col = $pdf->_colWidth();
	$rowH = $pdf->row_height * 2;


	//대상자명
	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);

	if (!$jumin) $jumin = $row['jumin'];
	$jumin = SubStr($jumin.'0000000',0,13);

	$gender	= $myF->euckr($myF->issToGender($jumin));
	$juminNo= $myF->issStyle($jumin);

	Unset($row);

	//사례접수기록
	$sql = 'SELECT	addr
			,		addr_dtl
			,		phone
			,		mobile
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$addr	= $pdf->_splitTextWidth($myF->utf($row['addr'].' '.$row['addr_dtl']),$pdf->width*0.52);
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$mobile	= $myF->phoneStyle($row['mobile'],'.');
	$telno	= $phone;

	if ($telno) $telno .= "\n";

	$telno .= $mobile;

	Unset($row);


	//관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'HR\'';

	$arrRel = $conn->_fetch_array($sql,'code');


	$sql = 'SELECT	conn_orgno
			,		conn_orgnm
			,		per_nm
			,		per_jumin
			,		req_dt
			,		reqor_nm
			,		reqor_rel
			,		req_rsn
			,		req_text
			FROM	hce_svc_connect
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		conn_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$connOrgNm	= $row['conn_orgnm'];
	$reqorNm	= $row['reqor_nm'];
	$reqorRel	= $arrRel[$row['reqor_rel']]['name'];
	$reqRsn		= StripSlashes($row['req_rsn']);
	$reqText	= StripSlashes($row['req_text']);
	$reqDate	= $myF->euckr($myF->dateStyle($row['req_dt'],'KOR'));
	$perJumin	= $row['per_jumin'];
	$perNm		= $row['per_nm'];

	Unset($row);
	Unset($arrRel);

	//담당자 연락처
	$sql = 'SELECT	m02_ytel
			FROM	m02yoyangsa
			WHERE	m02_ccode	= \''.$orgNo.'\'
			AND		m02_yjumin	= \''.$perJumin.'\'
			ORDER	BY m02_mkind
			LIMIT	1';

	$perTelno = $myF->phoneStyle($conn->get_data($sql),'.');


	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width, $rowH, "수신 : ".($connOrgNm ? $connOrgNm : "                                        ")."기관 담당자 귀하", 0, 1);

	$X1 = $pdf->left;
	$Y1 = $pdf->GetY();

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "성명", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "성별", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, ($gender == "남" ? "■" : "□")."남     ".($gender == "여" ? "■" : "□")."여", 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "주민번호", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, $juminNo, 1, 1, "C");


	$X = $pdf->left;
	$Y = $pdf->GetY();
	$H = lfGetStringHeight($pdf,$col[1] + $col[2] + $col[3] + $col[4],$addr);

	$pdf->SetXY($X + $col[0], $Y + ($rowH - $H) / 2);
	$pdf->MultiCell($col[1] + $col[2] + $col[3] + $col[4], 4, $addr);

	$H = lfGetStringHeight($pdf,$col[5],$telno);
	$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4], $Y + ($rowH - $H) / 2);
	$pdf->MultiCell($col[5], 4, $telno, 0, "C");

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $rowH, "주소", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2] + $col[3], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[4], $rowH, "연락처", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, "", 1, 1, "C");


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "의뢰인", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $rowH, $reqorNm, 1, 0, "C");
	$pdf->Cell($col[3] + $col[4], $rowH, "대상자와의 관계", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, $reqorRel, 1, 1, "C");


	$X = $pdf->left;
	$Y = $pdf->GetY();
	$H = lfGetStringHeight($pdf,$col[1] + $col[2] + $col[3] + $col[4] + $col[5], $reqRsn);

	if ($H < $rowH * 3) $H = $rowH * 3;

	$pdf->SetXY($X + $col[0], $Y + 0.8);
	$pdf->MultiCell($col[1] + $col[2] + $col[3] + $col[4] + $col[5], 4, $reqRsn);

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "의뢰사유", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5], $H, "", 1, 1, "C");

	$X = $pdf->left;
	$Y = $pdf->GetY();

	$H1 = lfGetStringHeight($pdf,$col[0], "의뢰자의\n요청사항");
	$H2 = lfGetStringHeight($pdf,$col[1] + $col[2] + $col[3] + $col[4] + $col[5], $reqText);

	$H = ($H1 > $H2 ? $H1 : $H2);

	if ($H < $rowH * 3) $H = $rowH * 3;

	$pdf->SetXY($X, $Y);
	$pdf->Cell($col[0], $H, "", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5], $H, "", 1, 1, "C");

	$pdf->SetXY($X, $Y + ($H - $H1) / 2);
	$pdf->MultiCell($col[0], 4, "의뢰자의\n요청사항", 0, "C");

	$pdf->SetXY($X + $col[0], $Y + 0.8);
	$pdf->MultiCell($col[1] + $col[2] + $col[3] + $col[4] + $col[5], 4, $reqText);

	$Y += $H;

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($pdf->width, $pdf->height - $Y, "", 1, 1);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($pdf->width, $rowH, "", 0, 1, "C");
	$pdf->Cell($pdf->width, $pdf->row_height, "상기인을 귀 기관에 의뢰하오니 적극 협조 부탁드립니다.", 0, 1, "C");
	$pdf->Cell($pdf->width, $pdf->row_height, $reqDate, 0, 1, "C");

	$Y = $pdf->GetY();

	$pdf->SetXY($pdf->left, $pdf->height - $pdf->row_height * 6);
	$pdf->Cell($pdf->width * 0.70, $pdf->row_height, "담당자 : ", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $perNm, 0, 1, "L");
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.70, $pdf->row_height, "연락처 : ", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $perTelno, 0, 1, "L");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width, $pdf->row_height, "", 0, 1, "L");

	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width, $rowH, $pdf->ctName, 0, 1, "C");
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$X2 = $pdf->width;
	$Y2 = $pdf->height - $Y1;

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($X1, $Y1, $X2, $Y2);
	$pdf->SetLineWidth(0.2);

	Unset($col);
?>
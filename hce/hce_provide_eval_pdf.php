<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];

	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			,		m03_tel AS phone, m03_hp AS mobile
			,		m03_juso1 AS addr, m03_juso2 AS addr_dtl
			FROM	m03sugupja
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= SubStr($row['real_jumin'].'0000000',0,13);
	$gender	= $myF->issToGender($jumin);
	$jumin	= $myF->issStyle($jumin);
	$phone	= $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');
	$addr	= $row['addr'].' '.$row['addr_dtl'];

	Unset($row);


	$sql = 'SELECT	*
			FROM	hce_provide_evl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		evl_seq	= \''.$var['subId'].'\'';

	$R = $conn->get_array($sql);

	if ($R['evl_cd']){
		$sql = 'SELECT	DISTINCT m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode = \''.$orgNo.'\'
				AND		m02_yjumin= \''.$R['evl_cd'].'\'';

		$evlNm = $conn->get_data($sql);
	}


	$rowH = $pdf->row_height * 2;

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',15);
	$pdf->Cell($pdf->width, $rowH, "「재가노인지원서비스」제공 평가서", 1, 1, "C");


	$pdf->SetFont($pdf->font_name_kor,'',11);


	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width * 0.12, $rowH * 4, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.18, $rowH, "사례관리자", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $evlNm, 1, 0, "C");
	$pdf->Cell($pdf->width * 0.18, $rowH, "평    가    일", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $myF->euckr($myF->dateStyle($R['evl_dt'],'KOR')), 1, 1, "C");

	$Y = $pdf->GetY();
	$str = "기본\n사항";
	$H = lfGetStringHeight($pdf, $pdf->width * 0.12, $str, 5);
	$pdf->SetXY($pdf->left, ($Y - $rowH) + ($rowH * 4 - $H) / 2);
	$pdf->MultiCell($pdf->width * 0.12, 5, $str, false, "C");


	$pdf->SetXY($pdf->left + $pdf->width * 0.12, $Y);
	$pdf->Cell($pdf->width * 0.18, $rowH, "성        명", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $name, 1, 0, "C");
	$pdf->Cell($pdf->width * 0.18, $rowH, "주민등록번호", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $jumin, 1, 1, "C");

	$pdf->SetX($pdf->left + $pdf->width * 0.12);
	$pdf->Cell($pdf->width * 0.18, $rowH, "전  화  번  호", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $phone, 1, 0, "C");
	$pdf->Cell($pdf->width * 0.18, $rowH, "성별(남, 여)", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.26, $rowH, $myF->euckr($gender), 1, 1, "C");

	$pdf->SetX($pdf->left + $pdf->width * 0.12);
	$pdf->Cell($pdf->width * 0.18, $rowH, "주        소", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.70, $rowH, $addr, 1, 1, "C");


	$rowH = $pdf->row_height * 3;


	$rtH[0] = lfGetStringHeight($pdf, $pdf->width * 0.88, $R['svc_cont']) + 2; //제공서비스내용
	$rtH[1] = lfGetStringHeight($pdf, $pdf->width * 0.88, $R['evl_cont']) + 2; //사례평가내용
	$rtH[2] = lfGetStringHeight($pdf, $pdf->width * 0.88, $R['after_plan']) + 2; //향후계획

	if ($rowH > $rtH[0]) $rH = $rowH; else $rH = $rtH[0];

	$Y = $pdf->GetY();
	$pdf->SetXY($pdf->left + $pdf->width * 0.12, $Y + 1);
	$pdf->MultiCell($pdf->width * 0.88, 4, $R['svc_cont']);


	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($pdf->width * 0.12, $rH, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $rH, "", 1, 1, "C");

	$Y = $pdf->GetY();
	$str = "제공\n서비스내용";
	$H = lfGetStringHeight($pdf, $pdf->width * 0.12, $str, 5);
	$pdf->SetXY($pdf->left, ($Y - $rH) + ($rH - $H) / 2);
	$pdf->MultiCell($pdf->width * 0.12, 5, $str, false, "C");


	if ($rowH > $rtH[1]) $rH = $rowH; else $rH = $rtH[1];

	if ($pdf->height - $Y - $rtH[2] > $rH) $rH = $pdf->height - $Y - $rtH[2];

	$pdf->SetXY($pdf->left + $pdf->width * 0.12, $Y + 1);
	$pdf->MultiCell($pdf->width * 0.88, 4, $R['evl_cont']);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($pdf->width * 0.12, $rH, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $rH, "", 1, 1, "C");

	$Y = $pdf->GetY();
	$str = "사례평가\n내용";
	$H = lfGetStringHeight($pdf, $pdf->width * 0.12, $str, 5);
	$pdf->SetXY($pdf->left, ($Y - $rH) + ($rH - $H) / 2);
	$pdf->MultiCell($pdf->width * 0.12, 5, $str, false, "C");


	if ($rowH > $rtH[2]) $rH = $rowH; else $rH = $rtH[2];

	$pdf->SetXY($pdf->left + $pdf->width * 0.12, $Y + 1);
	$pdf->MultiCell($pdf->width * 0.88, 4, $R['after_plan']);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($pdf->width * 0.12, $rH, "", 1, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $rH, "", 1, 1, "C");

	$Y = $pdf->GetY();
	$str = "향후계획";
	$H = lfGetStringHeight($pdf, $pdf->width * 0.12, $str, 5);
	$pdf->SetXY($pdf->left, ($Y - $rH) + ($rH - $H) / 2);
	$pdf->MultiCell($pdf->width * 0.12, 5, $str, false, "C");

	Unset($R);
?>
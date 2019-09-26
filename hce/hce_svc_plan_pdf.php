<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���񽺰�ȹ��
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	//�����
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	$sql = 'SELECT	*
			FROM	hce_plan_sheet
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		plan_seq= \''.$var['idx'].'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$planer = $row['planer'];	//�ۼ���
	$planDt	= $myF->dateStyle($row['plan_dt'],'.');	//����
	$needs	= StripSlashes($row['needs']);		//�簡���� �屸
	$problem= StripSlashes($row['problem']);	//�簡���� ����
	$goal	= StripSlashes($row['goal']);		//��ǥ
	$period	= StripSlashes($row['svc_period']);	//���񽺱Ⱓ
	$content= StripSlashes($row['svc_content']);//���񽺳���
	$method	= StripSlashes($row['svc_method']);	//���񽺹��
	$remark	= StripSlashes($row['remark']);		//���

	Unset($row);

	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.35;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.35;

	$rowH = $pdf->row_height * 1.5;

	$pdf->SetFont($pdf->font_name_kor,'B',9);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0], $rowH, "����ڸ�", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "�ۼ���", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $planer, 1, 1, "C");

	Unset($col);

	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.12;
	$col[] = $pdf->width * 0.73;

	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($col[0], $rowH, "��   ¥", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $rowH, $planDt, 1, 1, "L");

	//�簡������ �屸
	$X  = $pdf->left + $col[0];
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $needs);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "�簡������ �屸", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");


	//�簡������ ����
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $problem);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "�簡������ ����", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");


	//�簡���� ��ǥ $goal
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[1] + $col[2], 3.5, $goal);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "��   ǥ", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");


	//���� ������ȹ - ���񽺱Ⱓ
	$X	= $pdf->left + $col[0] + $col[1];
	$Y1 = $pdf->GetY();
	$Y3 = $Y1;

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $period);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LTR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "���񽺱Ⱓ", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//���� ������ȹ - ���񽺳���
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $content);

	$Y2 = $pdf->GetY() + 1;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "���񽺳���", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//���� ������ȹ - ���񽺹��
	$Y1 = $pdf->GetY();

	$pdf->SetXY($X, $Y1 + 1);
	$pdf->MultiCell($col[2], 3.5, $method);

	$Y2 = $pdf->GetY() + 1;
	$Y4 = $Y2;

	$H = $Y2 - $Y1;

	if ($H < $rowH) $H = $rowH;

	$pdf->SetXY($pdf->left, $Y1);
	$pdf->Cell($col[0], $H, "", "LBR", 0, "C", 1);
	$pdf->Cell($col[1], $H, "���񽺹��", 1, 0, "C", 1);
	$pdf->Cell($col[2], $H, "", 1, 1, "L");


	//���
	$H = $pdf->height - ($Y1 + $H);

	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y2,'width'=>$col[1] + $col[2],'text'=>$remark);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $H, "���", 1, 0, "C", 1);
	$pdf->Cell($col[1] + $col[2], $H, "", 1, 1, "L");

	$Y = $Y3 + ($Y4 - $Y3 - 10) / 2;

	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y,'width'=>$col[0],'align'=>'C','text'=>"����\n������ȹ");

	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
	}

	Unset($pos);
	Unset($col);
?>
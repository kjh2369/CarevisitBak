<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	����򰡼�
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	//�����
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
	$jumin = $myF->issStyle(SubStr($jumin.'0000000',0,13));	//����� �ֹι�ȣ

	Unset($row);

	//�����
	$sql = 'SELECT	*
			FROM	hce_evaluation
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND     del_flag= \'N\'';

	$row = $conn->get_array($sql);

	$evDt		= $myF->dateStyle($row['ev_dt'],'.');
	$evHm		= $myF->timeStyle($row['ev_hm']);
	$ever		= $row['ever'];
	$quest1		= $row['quest_1'];
	$quest2		= $row['quest_2'];
	$quest3		= $row['quest_3'];
	$quest4		= $row['quest_4'];
	$quest5		= $row['quest_5'];
	$quest6		= $row['quest_6'];
	$quest7		= $row['quest_7'];
	$quest8		= $row['quest_8'];
	$quest9		= $row['quest_9'];
	$quest10	= $row['quest_10'];
	$quest11	= $row['quest_11'];
	$quest12	= $row['quest_12'];
	$quest13	= $row['quest_13'];
	$quest14	= $row['quest_14'];
	$text1		= StripSlashes($row['text_1']);
	$text2		= StripSlashes($row['text_2']);
	$text3		= StripSlashes($row['text_3']);
	$text4		= StripSlashes($row['text_4']);
	$text5		= StripSlashes($row['text_5']);

	Unset($row);


	$col[] = $pdf->width * 0.2;
	$col[] = $pdf->width * 0.3;
	$col[] = $pdf->width * 0.2;
	$col[] = $pdf->width * 0.3;

	$rowH = $pdf->row_height;

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "�� �� �� ��", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $name, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "�ֹε�Ϲ�ȣ", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $jumin, 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "�� �� �� ��", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH, $evDt." ".$evHm, 1, 0, "C");
	$pdf->Cell($col[2], $rowH, "��    ��    ��", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, $ever, 1, 1, "C");

	Unset($col);

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $rowH, "1. ���� ��ǥ�޼� ��", 0, 1);


	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.40;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;

	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH * 2, "�ܰ�", 1, 0, "C", 1);
	$pdf->Cell($col[1], $rowH * 2, "���׸�", 1, 0, "C", 1);
	$pdf->Cell($pdf->width - ($col[0] + $col[1]), $rowH, "��    ��", 1, 2, "C", 1);
	$pdf->Cell($col[2], $rowH, "�ſ�׷���", 1, 0, "C", 1);
	$pdf->Cell($col[3], $rowH, "�׷���", 1, 0, "C", 1);
	$pdf->Cell($col[4], $rowH, "����", 1, 0, "C", 1);
	$pdf->Cell($col[5], $rowH, "�׷����ʴ�", 1, 0, "C", 1);
	$pdf->Cell($col[6], $rowH, "", 1, 1, "C", 1);

	$Y = $pdf->GetY();

	$pdf->SetXY($pdf->left + $pdf->width - $col[6], $Y - $rowH + 0.7);
	$pdf->MultiCell($col[6], 2.5, "�ſ�\n�׷����ʴ�", 0, "C");

	//$rowH = $pdf->row_height * 1.8;
	$rowH = $pdf->row_height * 1.3;


	/*********************************************************
	 *********************************************************/
	$str = "�Ƿ����� Ȯ�� �� �ʱ���� ���࿩�ΰ� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 2, "�ƿ���ġ", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest1 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest1 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest1 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest1 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest1 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "�簡���� �Ż� ���� �⺻�����ľ��� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest2 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest2 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest2 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest2 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest2 == '5' ? '��' : ''), 1, 1, "C");


	/*********************************************************
	 *********************************************************/
	$Y = $pdf->GetY();

	$str = "�簡������ �屸������ �ǰ��� �屸������ �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 2, "��      ��", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest3 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest3 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest3 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest3 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest3 == '5' ? '��' : ''), 1, 1, "C");

	$Y = $pdf->GetY();

	$str = "�簡���μ����������ο� ���� ���ȸ�� ������ �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest4 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest4 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest4 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest4 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest4 == '5' ? '��' : ''), 1, 1, "C");


	/*********************************************************
	 *********************************************************/
	$Y = $pdf->GetY();

	$str = "��  ��  ��\n��       ȹ";
	$H = lfGetStringHeight($pdf,$col[0],$str);
	$pdf->SetXY($pdf->left, $Y + ($rowH * 4 - $H) / 2);
	$pdf->MultiCell($col[0], 4, $str, 0, "C");

	$str = "���񽺰�ȹ������ �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 4, "", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest5 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest5 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest5 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest5 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest5 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "�����̿�ȳ� �� ����� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest6 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest6 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest6 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest6 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest6 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "��༭�� ��õ� ���뿡 ���� ���� ��ȹ��� ����Ǿ��°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest7 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest7 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest7 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest7 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest7 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "��ȹ�� �´� �ڿ��� ��� ���� Ȯ���Ǿ��°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest8 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest8 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest8 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest8 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest8 == '5' ? '��' : ''), 1, 1, "C");



	/*********************************************************
	 *********************************************************/
	$Y = $pdf->GetY();

	$str = "��  ��  ��\n��       ��\n��  ��  ��";
	$H = lfGetStringHeight($pdf,$col[0],$str);
	$pdf->SetXY($pdf->left, $Y + ($rowH * 2 - $H) / 2);
	$pdf->MultiCell($col[0], 4, $str, 0, "C");

	$str = "�������ȸ�ǰ� ������ ����Ǿ��°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 2, "", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest9 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest9 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest9 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest9 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest9 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "���������������� ������������ ����� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest10 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest10 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest10 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest10 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest10 == '5' ? '��' : ''), 1, 1, "C");


	/*********************************************************
	 *********************************************************/
	$Y = $pdf->GetY();

	$str = "��  ��  ��\n��  ��  ��";
	$H = lfGetStringHeight($pdf,$col[0],$str);
	$pdf->SetXY($pdf->left, $Y + ($rowH * 3 - $H) / 2);
	$pdf->MultiCell($col[0], 4, $str, 0, "C");

	$str = "���񽺿��� �� �Ƿڰ� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $rowH * 3, "", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest11 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest11 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest11 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest11 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest11 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "����� ������� �����ϰ� �ۼ��Ǿ��°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest12 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest12 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest12 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest12 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest12 == '5' ? '��' : ''), 1, 1, "C");


	$Y = $pdf->GetY();

	$str = "���� ���� �� ���� ���� �ȳ��� �����ߴ°�?";
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y + ($rowH - $H) / 2 + 0.3);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->Cell($col[1], $rowH, "", 1, 0, "C");
	$pdf->Cell($col[2], $rowH, ($quest13 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest13 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest13 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest13 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest13 == '5' ? '��' : ''), 1, 1, "C");



	/*********************************************************
	 *********************************************************/
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "��       Ÿ", 1, 0, "C");
	$pdf->Cell($col[1], $rowH, "�������� ���� ������ ��Ѱ�?", 1, 0, "L");
	$pdf->Cell($col[2], $rowH, ($quest14 == '1' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[3], $rowH, ($quest14 == '2' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[4], $rowH, ($quest14 == '3' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[5], $rowH, ($quest14 == '4' ? '��' : ''), 1, 0, "C");
	$pdf->Cell($col[6], $rowH, ($quest14 == '5' ? '��' : ''), 1, 1, "C");

	Unset($col);


	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $rowH, "2. �簡���ο��� ��ģ ����", 0, 1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	/*
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.25;
		$col[] = $pdf->width * 0.25;

		$rowH = $pdf->row_height;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0], $rowH, "����", 1, 0, "C", 1);
		$pdf->Cell($col[1], $rowH, "��  ��  ��", 1, 0, "C", 1);
		$pdf->Cell($col[2], $rowH, "��  ��  ��", 1, 0, "C", 1);
		$pdf->Cell($col[3], $rowH, "��ȭ�� �屸", 1, 1, "C", 1);

		$pdf->SetX($pdf->left);

		#$pdf->Cell($col[0], $rowH, $text1, 1, 0);
		#$pdf->Cell($col[1], $rowH, $text2, 1, 0);
		#$pdf->Cell($col[2], $rowH, $text3, 1, 0);
		#$pdf->Cell($col[3], $rowH, $text4, 1, 1);

		$Y = $pdf->GetY();

		$H1 = lfGetStringHeight($pdf,$col[0],$text1);
		$H2 = lfGetStringHeight($pdf,$col[1],$text2);
		$H3 = lfGetStringHeight($pdf,$col[2],$text3);
		$H4 = lfGetStringHeight($pdf,$col[3],$text4);

		$H = 0;

		if ($H < $H1) $H = $H1;
		if ($H < $H2) $H = $H2;
		if ($H < $H3) $H = $H3;
		if ($H < $H4) $H = $H4;

		$pdf->SetXY($pdf->left, $Y);
		$pdf->MultiCell($col[0], 4, $text1);

		$pdf->SetXY($pdf->left + $col[0], $Y);
		$pdf->MultiCell($col[1], 4, $text2);

		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->MultiCell($col[2], 4, $text3);

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2], $Y);
		$pdf->MultiCell($col[3], 4, $text4);

		$pdf->SetXY($pdf->left, $Y);
		$pdf->Cell($col[0], $H, "", 1, 0);
		$pdf->Cell($col[1], $H, "", 1, 0);
		$pdf->Cell($col[2], $H, "", 1, 0);
		$pdf->Cell($col[3], $H, "", 1, 1);

		$pdf->SetXY($pdf->left, $Y + $H);

		Unset($col);
	 */


	$col[] = $pdf->width * 0.1;
	$col[] = $pdf->width * 0.9;

	$rowH = $pdf->row_height;

	$Y = $pdf->GetY();
	$str = $text1;
	$H = lfGetStringHeight($pdf,$col[1],$str);
	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H > $rowH ? $H : $rowH, "����", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H > $rowH ? $H : $rowH, "", 1, 1, "L");


	$Y = $pdf->GetY();
	$str = $text2;
	$H = lfGetStringHeight($pdf,$col[1],$str);

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H > $rowH ? $H : $rowH, "������", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H > $rowH ? $H : $rowH, "", 1, 1, "L");


	$Y = $pdf->GetY();
	$str = $text3;
	$H = lfGetStringHeight($pdf,$col[1],$str);

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H > $rowH ? $H : $rowH, "������", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H > $rowH ? $H : $rowH, "", 1, 1, "L");


	$Y = $pdf->GetY();
	$str = $text4;
	$H = lfGetStringHeight($pdf,$col[1],$str);

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}

	$pdf->SetXY($pdf->left + $col[0], $Y);
	$pdf->MultiCell($col[1], 4, $str);

	$pdf->SetXY($pdf->left, $Y);
	$pdf->Cell($col[0], $H > $rowH ? $H : $rowH, "��ȭ�ȿ屸", 1, 0, "C", 1);
	$pdf->Cell($col[1], $H > $rowH ? $H : $rowH, "", 1, 1, "L");

	Unset($col);


	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 3);
	$pdf->Cell($pdf->width, $rowH, "3. ���ռҰ�(���İ�ȹ)", 0, 1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$Y = $pdf->GetY();
	$H = lfGetStringHeight($pdf,$pdf->width,$text5);

	if ($Y + $H > $pdf->height){
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
	}


	$pdf->SetX($pdf->left);
	$pdf->MultiCell($pdf->width, 4, $text5, 1);



	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width, $rowH, "��  ��  �� : ".($ever ? $ever : "              ")."    (��)", 0, 1, "R");
?>
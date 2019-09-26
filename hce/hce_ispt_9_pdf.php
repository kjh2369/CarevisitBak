<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���������-���ο��ô��
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];


	//������� �� �ʱ���� ����
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.marry_gbn
			,		rcpt.cohabit_gbn
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$jumin = $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);
	$tmpJumin = $jumin;
	$jumin = SubStr($jumin.'0000000',0,13);

	$name	= $row['name'];	//����
	$age	= $tmpJumin != '' ? number_format($myF->issToAge($jumin)) : '';	//����
	$jumin	= $tmpJumin != '' ? $myF->issStyle($jumin) : '';	//�ֹι�ȣ
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//�ּ�
	$phone	= $myF->phoneStyle($row['phone'],'.');	//����ó
	$mobile	= $myF->phoneStyle($row['mobile'],'.');	//�ڵ���

	$birthDay = $myF->euckr($myF->dateStyle($myF->issToBirthday($jumin),'KOR'));

	Unset($row);


	//���������ɷ���
	$sql = 'SELECT	*
			FROM	hce_inspection_sgds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$sgds = $conn->get_array($sql);


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',18);
	$pdf->Cell($pdf->width,$pdf->row_height*2,"���ο��ô��",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height,"(Short Form of Geriatric Depression Scale : SGDS)",0,1,'C');

	$col[] = $pdf->width * 0.08;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.09;
	$col[] = $pdf->width * 0.20;
	$col[] = $pdf->width * 0.06;
	$col[] = $pdf->width * 0.08;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.29;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0],$pdf->row_height * 2,"��  ��",1,0,'C',1);
	$pdf->Cell($col[1],$pdf->row_height * 2,$name,1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height * 2,"�������",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height * 2,$birthDay,1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height * 2,"��  ��",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height * 2,$age,1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"�� �� ȭ","LTR",0,'C',1);
	$pdf->Cell($col[7],$pdf->row_height,$phone,"LTR",1,'C');

	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]);
	$pdf->Cell($col[6],$pdf->row_height,"(�̵���ȭ)","LBR",0,'C',1);
	$pdf->Cell($col[7],$pdf->row_height,$mobile,"LBR",1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"��  ��",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7],$pdf->row_height * 2,$addr,1,1,'L');

	Unset($col);


	$quest[]	= Array('id'=>'Q1','no'=>'1.','quest'=>'������ ��Ȱ�� ��ü������ �����Ͻʴϱ�?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q2','no'=>'2.','quest'=>'������ ��� Ȱ�����̳� �ǿ��� ���� �������̽��ϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q3','no'=>'3.','quest'=>'�ڽ��� ����� ��� �ִٰ� �����ʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q4','no'=>'4.','quest'=>'��Ȱ�� �����ϰ� ������ ���� �����ϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q5','no'=>'5.','quest'=>'��ҿ� ����� ������ ���̽ʴϱ�?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q6','no'=>'6.','quest'=>'�ڽſ��� �ұ��� ���� ��ĥ �� ���� �Ҿ��Ͻʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q7','no'=>'7.','quest'=>'��ü�� ������ ��ſ� ���̽ʴϱ�?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q8','no'=>'8.','quest'=>'�������̶�� ������ ���� ��ʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q9','no'=>'9.','quest'=>'�ٱ��� �����Ⱑ �Ȱ� ������ �ְ� �ͽ��ϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q10','no'=>'10.','quest'=>'����� ������ �ٸ� ���ε麸�� ������ �� ���ڴٰ� �����ʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q11','no'=>'11.','quest'=>'���� ��� �ִٴ� ���� ��̰� �����ǽʴϱ�?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q12','no'=>'12.','quest'=>'������ �� �ڽ��� �ƹ� ���� ���� ����̶�� �����ʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q13','no'=>'13.','quest'=>'����� ������ ���Դϱ�?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q14','no'=>'14.','quest'=>'���� �ڽ��� ó���� �ƹ��� ����� ���ٰ� �����ʴϱ�?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q15','no'=>'15.','quest'=>'�ڽ��� �ٸ� ������� ó������ �� ���ϴٰ� �����ʴϱ�?','Y'=>'1','N'=>'0');


	$col[] = $pdf->width * 0.05;
	$col[] = $pdf->width * 0.75;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;

	$rowH = $pdf->row_height * 1.5;

	$pdf->SetXY($pdf->left,$pdf->GetY() + 10);
	$pdf->Cell($pdf->width,$rowH,"���� ������ �� �����ð� �� �� �� ���� �׸� üũ�� �ּ���.",0,1,'L');

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0] + $col[1],$rowH,"��          ��",1,0,'C',1);
	$pdf->Cell($col[2],$rowH,"��",1,0,'C',1);
	$pdf->Cell($col[3],$rowH,"�ƴϿ�",1,1,'C',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$pnt = 0;

	foreach($quest as $row){
		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH,$row['no'],"LTB",0,'R');
		$pdf->Cell($col[1],$rowH,$row['quest'],"TBR",0,'L');
		$pdf->Cell($col[2],$rowH,($sgds[$row['id']] == 'Y' ? $row['Y'] : ''),1,0,'C');
		$pdf->Cell($col[3],$rowH,($sgds[$row['id']] == 'N' ? $row['N'] : ''),1,1,'C');

		if ($sgds[$row['id']] == 'Y') $pnt += IntVal($row['Y']);
		if ($sgds[$row['id']] == 'N') $pnt += IntVal($row['N']);
	}

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0] + $col[1],$rowH,"��        ��",1,0,'C',1);
	$pdf->Cell($col[2] + $col[3],$rowH,$pnt." ��",1,1,'C',1);

	$pdf->SetXY($pdf->left,$pdf->GetY() + 5);
	$pdf->Cell($col[0] + $col[1],$rowH,"�� �˻��� �ؼ� : ������ 10�� �̻�� ������ ����� �ʿ��մϴ�.",0,1,'L');

	Unset($col);
	Unset($quest);
?>
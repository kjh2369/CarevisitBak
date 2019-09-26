<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');

	# �������
	$printYear  = date('Y', mkTime());
	$printMonth = date('m', mkTime());
	$printDay   = date('d', mkTime());

	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mDate = $_GET['mDate'];
	$mKey  = $_GET['mKey'];
	$mBoninYul = $_GET['mBoninYul'];

	# ������� ��ȸ
	$sql = "select m00_code1, m00_cname, m00_mname, m00_cpostno, m00_caddr1, m00_caddr2, m00_ccode"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$centerNo   = $row['m00_code1']; //�����ȣ
	$centerName = $row['m00_cname']; //�����
	$masterName = $row['m00_mname']; //��ǥ�ڸ�
	$regNo      = $row['m00_ccode']; //����ڵ�Ϲ�ȣ
	$postNo     = subStr($row['m00_cpostno'],0,3).'-'.subStr($row['m00_cpostno'],3,3); //�����ȣ
	$address    = $row['m00_caddr1'].' '.$row['m00_caddr2']; //�ּ�
	$conn->row_free();

	# ������ �ֹι�ȣ ��ȸ
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);

	# ������ ������ �޿����� ��ȸ
	$sql = "select m03_name"
		 . ",      m03_injung_no"
		 . ",      concat(t13_pay_date,'-',t13_bill_no) as t13_bill_no"
		 . ",      sum(t13_bonbu_tot4) as t13_bonbu_tot4"
		 . ",      sum(t13_chung_amt4) as t13_chung_amt4"
		 . ",      sum(t13_suga_tot4) as t13_suga_tot4"
		 . ",      sum(t13_misu_amt - t13_misu_inamt) as t13_misu_amt"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_jumin = '".$mJumin
		 . "'  and t13_pay_date = '".$mDate
		 . "'  and t13_bonin_yul = '".$mBoninYul
		 . "'  and t13_type = '2'"
		 . " group by m03_name, m03_injung_no, t13_pay_date, t13_bill_no";
	$conn->query($sql);
	$row = $conn->fetch();
	$sugupjaName = $row['m03_name'];
	$sugupjaNo   = $row['m03_injung_no'];
	//$sugupFmDate = getDateStyle($mDate.'01','.');
	//$sugupToDate = getLastYMD(subStr($mDate,0,4), subStr($mDate,4,2));
	$billNo      = $row['t13_bill_no'];
	$boninPay    = $row['t13_bonbu_tot4'];
	$gongdanPay  = $row['t13_chung_amt4'];
	$totalPay    = $row['t13_suga_tot4'];
	$misuPay     = $row['t13_misu_amt'];

	if ($misuPay == 0) $misuPay = '';

	# ���γ��� ��ȸ
	$sql = "select sum(t14_amount) as t14_amount"
		 . "  from t14deposit"
		 . " where t14_ccode    = '".$mCode
		 . "'  and t14_mkind    = '".$mKind
		 . "'  and t14_jumin    = '".$mJumin
		 . "'  and t14_pay_date = '".$mDate
		 . "'";
	$deposit = $conn->get_data($sql);
	
	if ($deposit == 0) $deposit = '';
	
	$conn->row_free();

	//�޿������Ⱓ
	$sql = "select m03_sdate, m03_edate
			  from m03sugupja  
			 where m03_ccode = '$mCode' 
			   and m03_mkind = '$mKind'   
			   and m03_jumin = '$mJumin'   
			   and m03_skind = '$mBoninYul'
			   and '$mDate' between left(m03_sdate, 6) and left(m03_edate, 6)
			 union all
			select m31_sdate, m31_edate
			  from m31sugupja  
			 where m31_ccode = '$mCode' 
			   and m31_mkind = '$mKind'   
			   and m31_jumin = '$mJumin'  
			   and m31_kind = '$mBoninYul'
			   and '$mDate' between left(m31_sdate, 6) and left(m31_edate, 6)
			 order by m03_sdate, m03_edate";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	$tempS = $mDate.'01';
	$tempE = $mDate.'01';
	$tempLastDay = str_replace('.', '', getLastYMD(subStr($mDate,0,4), subStr($mDate,4,2)));

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
		if ($i == 0 && $tempS < $row[0]){
			$tempS = $row[0];
		}

		if ($tempE < $row[0] && $tempS < $row[0]) $tempE = $row[0];
		if ($tempE < $row[1] && $tempS < $row[1]) $tempE = $row[1];

		if ($tempE > $tempLastDay){
			$tempE = $tempLastDay;
			break;
		}
	}

	$sugupFmDate = $myF->dateStyle($tempS, '.');
	$sugupToDate = $myF->dateStyle($tempE, '.');

	$conn->row_free();

	# ���� �߱��� ����
	setPaymentIssu($conn, $mCode, $mKind, $mJumin, $mDate, $mBoninYul, $billNo, date('Ymd',mkTime()));

	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('����','Gulim');
	$pdf->Open();
	
	$pdf->SetFillColor(238,238,238);
	
	$pdf->AddPage();
	$pdf->SetXY(14, 21);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect(14, 26, 182, 250);
	$pdf->SetLineWidth(0.2);

	$pdf->SetFont('����','',9);
	$pdf->Cell(182,5,'[���� ��24ȣ����]',0,1,'L');
	
	$pdf->SetX(14);
	$pdf->SetFont('����','',12);
	$pdf->Cell(150,14,'�����޿���� ����',1,0,'C');
	$pdf->SetFont('����','',10);
	$pdf->Cell(32,7,'�����',1,2,'C');
	$pdf->Cell(32,7,'���߰�',1,1,'C');

	$pdf->SetFont('����','',11);

	$pdf->SetX(14);
	$pdf->Cell(20,10,'',1,0,'C'); //���������ȣ
	$pdf->SetFont('����','b',11);
	$pdf->Cell(40,10,$centerNo,1,0,'L');
	$pdf->SetFont('����','',10);
	$pdf->Cell(30,10,'���������',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(92,10,$centerName,1,1,'L');
	$pdf->SetFont('����','',10);

	$pdf->SetX(14);
	$pdf->Cell(20,14,'�ּ�',1,0,'C');
	$pdf->Cell(94,14,'',1,0,'L');
	$pdf->Cell(30,14,'����ڵ�Ϲ�ȣ',1,0,'C');
	$pdf->SetFont('����','b',10);
	$pdf->Cell(38,14,getBizStyle($regNo),1,1,'L'); #����ڵ�Ϲ�ȣ
	$pdf->SetFont('����','',10);

	# ���������ȣ
	$pdf->Text(16,44,'�����');
	$pdf->Text(16,48,'�����ȣ');

	#�ּ�
	$pdf->SetFont('����','b',10);
	$pdf->Text(35,55, '['.$postNo.']');
	$pdf->Text(35,61, $address);
	$pdf->SetFont('����','',10);

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	$pdf->SetX(14);
	$pdf->Cell(32,10,'����',1,0,'C');
	$pdf->Cell(45,10,'�����������ȣ',1,0,'C');
	$pdf->Cell(60,10,'�޿������Ⱓ',1,0,'C');
	$pdf->Cell(45,10,'������ ��ȣ',1,1,'C');

	$pdf->SetX(14);
	$pdf->SetFont('����','b',11);
	$pdf->Cell(32,10,$sugupjaName,1,0,'C'); #�����ڼ���
	$pdf->Cell(45,10,$sugupjaNo,1,0,'C'); #������ �����������ȣ
	$pdf->Cell(60,10,$sugupFmDate.'~'.$sugupToDate,1,0,'C'); #������ �޿������Ⱓ
	$pdf->Cell(45,10,$billNo,1,1,'C'); #��������ȣ

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	$tempY1 = $pdf->GetY();
	$pdf->SetX(14);
	$pdf->SetFont('����','',11);
	$pdf->Cell(45,10,'�׸�',1,0,'C');
	$pdf->Cell(46,10,'�ݾ�',1,0,'C');
	$pdf->Cell(91,10,'�ݾ׻�������',1,1,'C');

	$pdf->SetX(14);
	$pdf->SetFont('����','',11);
	$pdf->Cell(10,30,'',1,0,'C');
	$pdf->Cell(35,10,'���κδ�ݨ�',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(46,10,number_format($boninPay),1,0,'R'); #���κδ��
	$pdf->Cell(45,10,'',1,0,'C');
	$pdf->Cell(46,10,number_format($totalPay),1,2,'R'); #�Ѿ�
	$pdf->SetFont('����','',11);

	$pdf->SetX(24);
	$pdf->SetFont('����','',11);
	$pdf->Cell(35,10,'���ܺδ�ݨ�',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(46,10,number_format($gongdanPay),1,0,'R'); #���ܺδ��
	$pdf->Cell(45,10,'',1,0,'C');
	$pdf->Cell(46,10,number_format($boninPay),1,1,'R'); #���κδ��Ѿ�
	$pdf->SetFont('����','',11);

	$pdf->SetX(24);
	$pdf->SetFont('����','',11);
	$pdf->Cell(35,10,'�޿� ���(��+��)',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(46,10,number_format($totalPay),1,0,'R'); #�޿���
	$pdf->SetFont('����','',11);
	$pdf->Cell(45,10,'�̹� ������ �ݾר�',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(46,10,'',1,1,'R'); #�̹� ������ �ݾ� //number_format($deposit)
	$pdf->SetFont('����','',11);

	$pdf->SetX(14);
	$pdf->SetFont('����','',11);
	$pdf->Cell(10,90,'',1,0,'C');
	$pdf->Cell(35,10,'�Ļ������',1,0,'C');
	$pdf->Cell(46,10,'',1,0,'C');
	$pdf->Cell(45,10,'�̼���',1,0,'C');
	$pdf->SetFont('����','b',11);
	$pdf->Cell(46,10,number_format($misuPay),1,1,'R'); #�̼���
	$pdf->SetFont('����','',11);

	$tempY3 = $pdf->GetY();

	$pdf->SetX(24);
	$pdf->Cell(35,10,'',1,0,'C'); //���ħ�� �̿뿡 ���� �߰�����
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(24);
	$pdf->Cell(35,10,'��.�̿���',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(24);
	$pdf->Cell(13,50,'',1,2,'C'); //��Ÿ
	$pdf->SetXY(37, $pdf->GetY()-50);
	$pdf->Cell(22,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(22,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(22,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(22,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(22,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(24);
	$pdf->Cell(35,10,'',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$tempY2 = $pdf->GetY();
	$pdf->SetLineWidth(0.6);
	$pdf->Line(105, $tempY1, 105, $tempY2);
	$pdf->SetLineWidth(0.2);

	$pdf->SetXY(105, $tempY3);
	$pdf->Cell(20,40,'',1,0,'C');
	$pdf->Cell(25,10,'ī��',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'���ݿ�����',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'����',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'�հ�',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(91,10,'���ݿ�����',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(45,10,'�ź�Ȯ�ι�ȣ',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(45,10,'���ݽ��ι�ȣ',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(91,10,'�غ��',1,1,'L');

	$tempY1 = $pdf->GetY();

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $tempY1, 196, $tempY1);
	$pdf->SetLineWidth(0.2);

	$pdf->Text(17,108,'��');
	$pdf->Text(17,112,'��');

	$pdf->Text(110,98,'�Ѿ�(�޿�+��޿�)');
	$pdf->Text(118,102.5,'��(��+��)');

	$pdf->Text(115,108,'���κδ��Ѿ�');
	$pdf->Text(118,112.5,'��(��+��)');
	
	$pdf->Text(17,163,'��');
	$pdf->Text(17,168,'��');
	$pdf->Text(17,172,'��');

	$pdf->Text(27,138.5,'���ħ�� �̿뿡');
	$pdf->Text(27,142.5,'���� �߰�����');

	$pdf->Text(26.5,180,'��Ÿ');
	$pdf->Text(28.5,184.5,'��');

	$pdf->Text(33,208,'��޿� ��');
	$pdf->Text(25.5,212.5,'��(��+��+��+��)');

	$pdf->Text(107,152,'�����ݾ�');
	$pdf->Text(113,156,'��');
	$pdf->Text(107.5,160,'(��-��)');

	# �ſ�ī�带 ����Ͻ� �� ����
	$pdf->SetX(14);
	$pdf->Cell(23,20,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'�Һ�',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(33,10,'���ݾ�',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(33,10,'',1,2,'C');

	$pdf->Text(15.5, 223, '�ſ�ī�带');
	$pdf->Text(15.5, 227, '����ϽǶ�');
	$pdf->Text(39, 218.5, 'ȸ��');
	$pdf->Text(39, 222.5, '��ȣ');
	$pdf->Text(81, 218.5, '����');
	$pdf->Text(81, 222.5, '��ȣ');
	$pdf->Text(39, 228, 'ī��');
	$pdf->Text(39, 232, '����');
	$pdf->Text(81, 228, '��ȣ');
	$pdf->Text(81, 232, '�Ⱓ');
	$pdf->Text(121.2, 228, '������');
	$pdf->Text(123, 232, '��ȣ');
	
	# �׵θ�
	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	# ����
	$pdf->SetXY(14, $pdf->GetY() + 14);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetFont('����','b',13);
	//$pdf->Text(81,  239, $printYear.'.');   //��
	//$pdf->Text(121, 239, $printMonth.'.');  //��
	$pdf->Text(161, 239, $printYear.'.'.$printMonth.'.'.$printDay);    //��
	$pdf->SetFont('����','',11);
	$pdf->Text(21, 245, '��������� : ');
	$pdf->SetFont('����','b',11);
	$pdf->Text(52, 245, $centerName); //�����
	$pdf->SetFont('����','',11);
	$pdf->Text(140, 245, '��ǥ�ڸ� : ');
	$pdf->SetFont('����','b',11);
	$pdf->Text(160, 245, $masterName); //��ǥ�ڸ�
	$pdf->SetFont('����','',8);
	$pdf->Text(180, 245, '(��)');

	# ����
	$pdf->SetFont('����','',11);
	$pdf->Text(16, 253, '* �� ����(������)�� ���ҵ漼������ ���� �Ƿ�� �Ǵ� ������Ư�����ѹ����� ���� ���ݿ�����(����');
	$pdf->Text(19.1, 257, '������ ���ι�ȣ�� �ⰳ�� ���) ������û�� ����� �� �ֽ��ϴ�. �ٸ�,  �������������� �߱޵� ����');
	$pdf->Text(19.1, 261, '������(��������)�� ������û�� ����� �� �����ϴ�.');
	$pdf->Text(16, 265, '* �� ����(������)�� ���� ���γ����� �䱸�� �� �ֽ��ϴ�.');
	$pdf->Text(16, 269, '* ������ ��������� ����Ȱ������� ����մϴ�. �ٸ�, �����뱸�� ��� ǰ��� ����. �뿩�� ��');
	$pdf->Text(19.1, 273, '���Ͽ� �����Ͻñ� �ٶ��ϴ�.');
	
	# Ǫ��
	$pdf->SetFont('����','b',10);
	$pdf->Text(14, 280, '���κδ�� ���԰���: ');
	$pdf->Text(14, 284, '��ȭ : ');
	
	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
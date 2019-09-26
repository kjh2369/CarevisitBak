<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	require_once('../pdf/korean.php');

	$conn->set_name('euckr');

	# �������
	$printYear  = date('Y', mkTime());
	$printMonth = date('m', mkTime());
	$printDay   = date('d', mkTime());

	$mCode		= $_GET['mCode'];
	$mKind		= $_GET['mKind'];
	$mDate		= $_GET['mDate'];
	$mKey		= $_GET['mKey'];
	$mBoninYul	= $_GET['mBoninYul'];
	$misu_yn	= $_GET['misy_yn'];

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
	$address1   = $row['m00_caddr1']; //�ּ�1
	$address2   = $row['m00_caddr2']; //�ּ�2
	$conn->row_free();

	# ������ �ֹι�ȣ ��ȸ
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);

	# ��Ʈ����
		$fontNameK = '����';  //����
		$fontNameE = 'Gulim'; //Dotum
		$paperDir     = 'L';


		$pdf=new PDF_Korean($paperDir);
		$pdf->AddUHCFont($fontNameK, $fontNameE);
		$pdf->Open();

		$pdf->SetCreator('GoodEos', true);

	# ������ ������ �޿����� ��ȸ
	$sql = "select m03_name"
		 . ",      m03_jumin"
		 . ",      m03_injung_no"
		 . ",      concat(t13_pay_date,'-',t13_bill_no) as t13_bill_no"
		 . ",      sum(t13_bonbu_tot4) as t13_bonbu_tot4"
		 . ",      sum(t13_chung_amt4) as t13_chung_amt4"
		 . ",      sum(t13_suga_tot4) as t13_suga_tot4"
		 . "/*,      sum(t13_misu_amt - t13_misu_inamt) as t13_misu_amt*/

			,     (select ifnull(sum(t13_bonbu_tot4 /*t13_misu_amt*/), 0)
					 from t13sugupja
				    where t13_ccode = m03_ccode
					  and t13_mkind = m03_mkind
					  and t13_jumin = m03_jumin
					  and t13_type  = '2'
					  and t13_pay_date <= '".$mDate."')
		    -     (select ifnull(sum(deposit_amt), 0)
					 from unpaid_deposit
				    where org_no        = m03_ccode
					  and deposit_jumin = m03_jumin
					  and del_flag      = 'N') as t13_misu_amt
					  "
		 . ",      m03_gaeyak_fm, m03_gaeyak_to"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " where t13_ccode    = '".$mCode
		 . "'  and t13_mkind    = '".$mKind
		 . "'  and t13_pay_date = '".$mDate
		 . "'";
	if($mKey != ''){
		$sql .=	  "   and t13_jumin = '".$mJumin
				. "'  and t13_bonin_yul = '".$mBoninYul
				. "'";
	}
	$sql .=	  "   and t13_type     = '2'"
			. " group by m03_name, m03_injung_no, t13_pay_date, t13_bill_no";
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($j=0; $j<$row_count; $j++){
		$row = $conn->select_row($j);

		# ��������
		$marginLeft   = 14;
		$marginTop    = 14;
		$marginRight  = 14;
		$marginBottom = 21;
		$paperWidth   = 297;
		$paperHeight  = 210;

		$bodyWidth    = $paperWidth  - $marginLeft;
		$bodyHeight   = $paperHeight - ($marginTop + $marginBottom);
		$bodyT        = $marginTop + 5;
		$bodyW        = $bodyWidth / 2 - $marginLeft;

		$sugupjaJumin = $row['m03_jumin'];
		$sugupjaName = $row['m03_name'];
		$sugupjaNo   = str_replace('-', '', $row['m03_injung_no']);

		if (substr($row['m03_gaeyak_fm'],0,6) == $mDate){
			$sugupFmDate = $row['m03_gaeyak_fm'];
		}else{
			$sugupFmDate = $mDate.'01';
		}

		$sugupToDate = getLastYMD(subStr($sugupFmDate,0,4), subStr($sugupFmDate,4,2), '');

		if ($sugupToDate > $row['m03_gaeyak_to']){
			$sugupToDate = $row['m03_gaeyak_to'];
		}

		$sugupFmDate = $myF->dateStyle($sugupFmDate,'.');
		$sugupToDate = $myF->dateStyle($sugupToDate,'.');

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

		# ���� �߱��� ����
		setPaymentIssu($conn, $mCode, $mKind, $sugupjaJumin, $mDate, $mBoninYul, $billNo, date('Ymd',mkTime()));

		$pdf->AddPage();
		$pdf->SetFont($fontNameK, '', 12);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetDrawColor(0, 0, 0);

		# �߾� ���뼱
		for($i=$marginTop; $i<=($paperHeight * 0.45); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3, '��');
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3 + 7, '��');
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3 + 14, '��');
		$pdf->SetFont($fontNameK, '', 8);
		$pdf->Text($paperWidth / 2 - 2.5, $paperHeight / 2 - 3 + 21, '(��)');
		for($i=($marginTop+($paperHeight * 0.55)); $i<=($bodyHeight + $bodyT); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}

		# ���� ���� �ۼ�
		# ����
		for($i=0; $i<2; $i++){
			if ($i > 0){
				$pdf->SetDrawColor(0, 0, 255);
				$pdf->SetTextColor(0, 0, 255);
				$marginLeft = $paperWidth / 2 + $marginLeft / 2;
			}
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop);
			$pdf->Cell($bodyW, 5, '[���� ��24ȣ����]<���� 2008.6.11>');

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($marginLeft, $bodyT, $bodyW, $bodyHeight);
			$pdf->SetLineWidth(0.2);

			# �ۼ�
			# Ÿ��Ʋ
			$pdf->SetFont($fontNameK, '', 11);
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW * 0.75, 10, '', 1, 0, 'C'); #�����޿���� ���� ����
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW * 0.75, 6, '�����޿���� ����', 0, 0, 'C');
			$pdf->SetFont($fontNameK, '', 9);
			$pdf->Cell($bodyW * 0.25, 5, '�����', 1, 2, 'C');
			$pdf->Cell($bodyW * 0.25, 5, '���߰�', 1, 1, 'C');
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop + 11);
			$pdf->Cell($bodyW * 0.75, 4, '����'.($i==0?'':'�޴�').'��/'.($i==0?'�ü�':'������').' ��', 0, 0, 'R');

			# �������
			$pdf->SetXY($marginLeft, $marginTop + 15);
			$pdf->SetFont($fontNameK, '', 9);
			//$pdf->SetTextColor(0, 0, 0); #���ڻ��� ���������� ����
			$pdf->Cell($bodyW * 0.15, 9, '', 1, 0, 'C'); #�����ȣ
			$pdf->Cell($bodyW * 0.25, 9, $centerNo, 1, 0, 'L'); #�����ȣ
			$pdf->Cell($bodyW * 0.15, 9, '', 1, 0, 'C'); #�����
			$pdf->Cell($bodyW * 0.45, 9, $centerName, 1, 1, 'L'); #�����

			#������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# ��ǥ ���
			$tempY = $pdf->GetY();
			$pdf->SetXY($marginLeft, $marginTop + 16);
			$pdf->Cell($bodyW * 0.15, 3.5, '�����', 0, 2, 'C');
			$pdf->Cell($bodyW * 0.15, 3.5, '�����ȣ', 0, 2, 'C');

			$pdf->SetXY($marginLeft + $bodyW * 0.4, $marginTop + 16);
			$pdf->Cell($bodyW * 0.15, 3.5, '�����', 0, 2, 'C');
			$pdf->Cell($bodyW * 0.15, 3.5, '�����', 0, 2, 'C');

			# ��ǥ ����
			$pdf->SetXY($marginLeft, $tempY);
			$tempY2 = $tempY;

			# ��� �ּ� �� ����ڵ�Ϲ�ȣ
			$pdf->Cell($bodyW * 0.15, 9, '�ּ�', 1, 0, 'C');
			$pdf->Cell($bodyW * 0.45, 9, '', 1, 0, 'C'); #�ּ�
			$pdf->Cell($bodyW * 0.17, 9, '', 1, 0, 'C'); #����ڵ�Ϲ�ȣ
			//$pdf->SetTextColor(0, 0, 0); #���ڻ��� ���������� ����
			$pdf->Cell($bodyW * 0.23, 9, getBizStyle($regNo), 1, 1, 'L'); #����ڵ�Ϲ�ȣ

			# ��ǥ ���
			$tempY = $pdf->GetY();

			# ��� �ּ� �� ����ڵ�Ϲ�ȣ �Է�
			# ��ǥ����
			$pdf->SetXY($marginLeft + $bodyW * 0.15, $tempY2 + 1);
			$pdf->Cell($bodyW * 0.45, 3.5, $address1, 0, 2, 'L'); #�ּ�1
			$pdf->Cell($bodyW * 0.45, 3.5, $address2, 0, 0, 'L'); #�ּ�2

			#������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}else{
				$pdf->SetTextColor(0, 0, 0);
			}

			# ��ǥ����
			$pdf->SetXY($marginLeft + $bodyW * 0.6, $tempY2 + 1);
			$pdf->Cell($bodyW * 0.17, 3.5, '�����', 0, 2, 'C'); #����ڵ�Ϲ�ȣ
			$pdf->Cell($bodyW * 0.17, 3.5, '��Ϲ�ȣ', 0, 0, 'C'); #����ڵ�Ϲ�ȣ


			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# ��ǥ ����
			$pdf->SetXY($marginLeft, $tempY);

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $tempY, $marginLeft + $bodyW, $tempY);
			$pdf->SetLineWidth(0.2);

			# ����������
			$pdf->Cell($bodyW * 0.2,  5, '����', 1, 0, 'C');             #����
			$pdf->Cell($bodyW * 0.23, 5, '�����������ȣ', 1, 0, 'C'); #�����������ȣ
			$pdf->Cell($bodyW * 0.34, 5, '�޿������Ⱓ', 1, 0, 'C');     #�޿������Ⱓ
			$pdf->Cell($bodyW * 0.23, 5, '��������ȣ', 1, 1, 'C');       #��������ȣ

			# ���������� ����Ÿ �Է�
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.2,  6, $sugupjaName, 1, 0, 'C');                  #����
			$pdf->Cell($bodyW * 0.23, 6, $sugupjaNo, 1, 0, 'L');                    #�����������ȣ
			$pdf->Cell($bodyW * 0.34, 6, $sugupFmDate.'~'.$sugupToDate, 1, 0, 'C'); #�޿������Ⱓ
			$pdf->Cell($bodyW * 0.23, 6, $billNo, 1, 1, 'C');                       #��������ȣ

			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# �޿��׸�
			$tempY = $pdf->GetY();
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.25, 7, '�׸�', 1, 0, 'C');
			$pdf->Cell($bodyW * 0.25, 7, '�ݾ�', 1, 0, 'C');
			$tempX = $pdf->GetX();
			$pdf->Cell($bodyW * 0.5, 7, '�ݾ׻�������', 1, 1, 'C');
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.05, 21, '', 1, 0, 'C'); #�޿�
			$pdf->Cell($bodyW * 0.20, 7, '���κδ�ݨ�', 1, 0, 'C'); #���κδ��
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 0, 'R'); #���κδ��
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'C'); #�Ѿ�
			$pdf->Cell($bodyW * 0.25, 7, number_format($totalPay), 1, 1, 'R'); #�Ѿ�

			# �޿�Ÿ��Ʋ �Է�
			$pdf->Text($marginLeft + 1.7, $tempY + 16.5, '��');
			$pdf->Text($marginLeft + 1.7, $tempY + 20.5, '��');

			# �Ѿ�Ÿ��Ʋ �Է�
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2, $tempY + 10, '�Ѿ�(�޿�+��޿�)');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9, $tempY + 13.2, '��(��+��)');

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '���ܺδ�ݨ�', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($gongdanPay), 1, 0, 'R'); #
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 1, 'R'); #���κδ��

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '�޿����(��+��)', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($totalPay), 1, 0, 'R'); #
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '�̹� ������ �ݾר�', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); # number_format($boninPay)
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# ���κδ��Ѿ� Ÿ��Ʋ �Է�
			$pdf->Text($marginLeft + $bodyW * 0.5 + 7, $tempY + 17, '���κδ��Ѿ�');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9, $tempY + 20.1, '��(��+��)');

			# X��ǥ ����
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.05, 63, '', 1, 0, 'C'); #��޿�
			$pdf->Cell($bodyW * 0.20, 7, '���������', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '�̼���', 1, 0, 'C'); #
			if($misu_yn == 'Y'){
				$pdf->Cell($bodyW * 0.25, 7, number_format($misuPay), 1, 1, 'R'); #�̼���
			}else {
				$pdf->Cell($bodyW * 0.25, 7,'', 1, 1, 'R'); #�̼���
			}
			# ��޿�Ÿ��Ʋ �Է�
			$pdf->Text($marginLeft + 1.7, $tempY + 50, '��');
			$pdf->Text($marginLeft + 1.7, $tempY + 54, '��');
			$pdf->Text($marginLeft + 1.7, $tempY + 58, '��');

			# X��ǥ ����
			$tempY2 = $pdf->GetY();
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7,  '', 1, 0, 'C'); #���ħ�� �̿뿡....
			$pdf->Cell($bodyW * 0.25, 7,  '', 1, 0, 'R'); #�ݾ�
			$pdf->Cell($bodyW * 0.09, 28, '', 1, 0, 'C'); #�����ݾ�
			$pdf->Cell($bodyW * 0.16, 7,  'ī��', 1, 0, 'C'); #ī��
			$pdf->Cell($bodyW * 0.25, 7,  '', 1, 1, 'R'); #�ݾ�

			# Ÿ��Ʋ�Է�
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 3, '���ħ�� �̿뿡');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 6, '�����߰�����');

			#Ÿ��Ʋ�Է�
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2.5, $tempY2 + 8, '����');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2.5, $tempY2 + 12, '�ݾ�');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 4,   $tempY2 + 16, '��');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 0.5, $tempY2 + 20, '(');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 1.4, $tempY2 + 20, '��-');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 6.5, $tempY2 + 20, '��');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9.5, $tempY2 + 20, ')');

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '��.�̿���', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '���ݿ�����', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 35, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '����', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, $misu_yn != 'Y' ? number_format($boninPay) : '', 1, 1, 'R'); #

			# Ÿ��Ʋ�Է�
			$pdf->Text($marginLeft + $bodyW * 0.05 + 0.5, $tempY2 + 32, '��Ÿ');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 2,   $tempY2 + 36, '��');

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '�հ�', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, $misu_yn != 'Y' ? number_format($boninPay) : '', 1, 1, 'R'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.5,  7, '���ݿ�����', 1, 1, 'C'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '�ź�Ȯ�ι�ȣ', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '���ݽ��ι�ȣ', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.5,  7, '* ���', 1, 1, 'L'); #

			# Ÿ��Ʋ�Է�
			$pdf->Text($marginLeft + $bodyW * 0.05 + 4, $tempY2 + 52, '��޿� ���');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 55, '(��+��+��+��)');

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Line($tempX, $tempY, $tempX, $pdf->GetY());
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# �ſ�ī�� ����
			# X��ǥ ����
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.14, 14, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.08, 7, '�Һ�', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.11, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '���ݾ�', 1, 1, 'C'); #

			# Ÿ��Ʋ �Է�
			$pdf->Text($marginLeft + 1, $pdf->GetY() - 1.5,   '�ſ�ī�带');
			$pdf->Text($marginLeft + 1, $pdf->GetY() + 3, '����ϽǶ�');

			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() - 4,   'ȸ��');
			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() - 0.5, '��ȣ');

			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() - 4,   '����');
			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() - 0.5, '��ȣ');

			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() + 3,   'ī��');
			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() + 6.3, '����');

			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() + 3,   '��ȿ');
			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() + 6.3, '�Ⱓ');

			$pdf->Text($marginLeft + $bodyW * 0.67 - 0.5, $pdf->GetY() + 3,   '������');
			$pdf->Text($marginLeft + $bodyW * 0.67 + 0.5, $pdf->GetY() + 6.3, '��ȣ');

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.14);
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.08, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.11, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '', 1, 1, 'C'); #

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# X��ǥ ����
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW, 10, '', 1, 0, 'C'); #
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.3, 5, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2, 5, $printYear.'.', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.1, 5, $printMonth.'.', 0, 0, 'L'); #
			$pdf->Cell($bodyW * 0.1, 5, $printDay.'.', 0, 1, 'L'); #
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.2, 5, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.3, 5, $centerName, 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.5, 5, '��ǥ�ڸ� : '.$masterName, 0, 1, 'C'); #

			# X��ǥ ����
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 3,    '* �� ����(������)�� ���ҵ漼������ ���� �Ƿ�� �Ǵ� ������Ư�����ѹ����� ���� ��');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 6.3,  '�ݿ�����(���ݿ����� ���ι�ȣ�� ����� ���) ������û�� ����� �� �ֽ��ϴ�. �ٸ�,');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 9.6,   '�������������� �߱޵� ���ݿ�����(��������)�� ������û�� ����� �� �����ϴ�.');
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 12.9, '* �� ����(������)�� ���� ���γ����� �䱸�� �� �ֽ��ϴ�.');
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 16.2,   '* ������ ��������� ����Ȱ������� ����մϴ�.');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 19.5, '�ٸ�, �����뱸�� ��� ǰ��� ����.�뿩�� �����Ͽ� �����Ͻñ� �ٶ��ϴ�.');
		}
	}
	$conn->row_free();


	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>
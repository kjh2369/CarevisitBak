<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');
	$conn2 = new connection();
	$conn2->set_name('euckr');
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
	$sql = "select m00_code1, m00_cname, m00_mname, m00_cpostno, m00_caddr1, m00_caddr2, m00_ccode, m00_ctel, m00_bank_name, m00_bank_no, m00_bank_depos"
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
	$centerTel = $myF->phoneStyle($row['m00_ctel']);
	$bankName = iconv("UTF-8","EUCKR",$definition->GetBankName($row['m00_bank_name']));
	$bankNo = $row['m00_bank_no'];
	$bankDepos = $row['m00_bank_depos'];
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
		 . ",      m03_injung_no"
		 . ",      concat(t13_pay_date,'-',t13_bill_no) as t13_bill_no"

		 . ",      sum(t13_suga_tot1) as t13_suga_tot1"
		 . ",      sum(t13_bonin_amt1) as t13_bonin_amt1"
		 . ",      sum(t13_over_amt1 + t13_bipay1) as t13_over_amt1"
		 . ",      sum(t13_bonbu_tot1) as t13_bonbu_tot1"

		 . ",      sum(t13_suga_tot2) as t13_suga_tot2"
		 . ",      sum(t13_bonin_amt2) as t13_bonin_amt2"
		 . ",      sum(t13_over_amt2 + t13_bipay2) as t13_over_amt2"
		 . ",      sum(t13_bonbu_tot2) as t13_bonbu_tot2"

		 . ",      sum(t13_suga_tot3) as t13_suga_tot3"
		 . ",      sum(t13_bonin_amt3) as t13_bonin_amt3"
		 . ",      sum(t13_over_amt3 + t13_bipay3) as t13_over_amt3"
		 . ",      sum(t13_bonbu_tot3) as t13_bonbu_tot3"

		 . ",      sum(t13_bonbu_tot4) as t13_bonbu_tot4"
		 . ",      sum(t13_chung_amt4) as t13_chung_amt4"
		 . ",      sum(t13_suga_tot4) as t13_suga_tot4"
		 . ",      sum(t13_bonin_amt4) as t13_bonin_amt4"
		 . ",      sum(t13_over_amt4 + t13_bipay4) as t13_over_amt4"
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
					  and del_flag      = 'N') as t13_misu_amt"

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
		$bodyWidth    = $paperWidth - $marginLeft;
		$bodyHeight   = $paperHeight - ($marginTop + $marginBottom);
		$bodyT        = $marginTop + 5;
		$bodyW        = $bodyWidth / 2 - $marginLeft;
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);

		$sugupjaName = $row['m03_name'];
		$sugupjaNo   = $row['m03_injung_no'];
		$sugupFmDate = getDateStyle($mDate.'01','.');
		$sugupToDate = getLastYMD(subStr($mDate,0,4), subStr($mDate,4,2));
		$billNo      = $row['t13_bill_no'];
		$boninPay    = $row['t13_bonbu_tot4'];
		$gongdanPay  = $row['t13_chung_amt4'];
		$totalPay    = $row['t13_suga_tot4'];
		$misuPay     = $row['t13_misu_amt'];

		$t13_suga_tot1 = $row['t13_suga_tot1'];
		$t13_bonin_amt1 = $row['t13_bonin_amt1'];
		$t13_over_amt1 = $row['t13_over_amt1'];
		$t13_bonbu_tot1 = $row['t13_bonbu_tot1'];

		$t13_suga_tot2 = $row['t13_suga_tot2'];
		$t13_bonin_amt2 = $row['t13_bonin_amt2'];
		$t13_over_amt2 = $row['t13_over_amt2'];
		$t13_bonbu_tot2 = $row['t13_bonbu_tot2'];

		$t13_suga_tot3 = $row['t13_suga_tot3'];
		$t13_bonin_amt3 = $row['t13_bonin_amt3'];
		$t13_over_amt3 = $row['t13_over_amt3'];
		$t13_bonbu_tot3 = $row['t13_bonbu_tot3'];

		$t13_suga_tot4 = $row['t13_suga_tot4'];
		$t13_bonin_amt4 = $row['t13_bonin_amt4'];
		$t13_over_amt4 = $row['t13_over_amt4'];
		$t13_bonbu_tot4 = $row['t13_bonbu_tot4'];

		if ($misuPay == 0) $misuPay = '';

		# ���γ��� ��ȸ
		$sql = "select case t14_type when '01' then 'cash'
									 when '03' then 'cash'
									 when '05' then 'cash'
									 when '81' then 'cash'
									 when '02' then 'vari'
									 when '04' then 'card'
									 else 'other' end as type
				,      sum(t14_amount) as amt"
			 . "  from t14deposit"
			 . " where t14_ccode    = '".$mCode
			 . "'  and t14_mkind    = '".$mKind
			 . "'  and t14_pay_date = '".$mDate
			 . "'";

			$sql .=	  "   and t14_jumin = '".$mJumin
					. "'  and t14_bonin_yul = '".$mBoninYul
					. "'";


		$sql .=	" group by case t14_type when '01' then 'cash'
										 when '02' then 'cash'
										 when '03' then 'cash'
										 when '05' then 'cash'
										 when '81' then 'cash'
										 when '04' then 'card'
										 else 'other' end";

		$conn2->query($sql);
		$conn2->fetch();
		$rowCount2 = $conn2->row_count();

		$inAmt['cash'] = 0;
		$inAmt['vari'] = 0;
		$inAmt['card'] = 0;

		for($i=0; $i<$rowCount2; $i++){
			$row2 = $conn2->select_row($i);
			$inAmt[$row['type']] += $row2['amt'];

		}
		$conn2->row_free();

		$pdf->SetFont($fontNameK, '', 12);
		$pdf->SetFillColor(238,238,238);

		$pdf->AddPage();
		$pdf->Setmargins(0, 0);


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
			$pdf->Rect($marginLeft, $bodyT, $bodyW, $bodyHeight - $marginBottom - 1);
			$pdf->SetLineWidth(0.2);

			# �ۼ�
			# Ÿ��Ʋ
			$pdf->SetFont($fontNameK, '', 11);
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW, 10, '', 1, 0, 'C'); #�����޿���� ���� ����
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW, 6, '�����޿���� (���κδ��)������', 0, 1, 'C');
			$pdf->SetFont($fontNameK, '', 9);
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop + 11);
			$pdf->Cell($bodyW * 0.25, 4, 'No.'.$billNo, 1, 0, 'L');
			$pdf->Cell($bodyW * 0.75, 4, '����'.($i==0?'':'�޴�').'��/'.($i==0?'�ü�':'������').' ��', 0, 1, 'R');

			# �������
			$pdf->SetXY($marginLeft, $marginTop + 15);
			$pdf->SetFont($fontNameK, '', 9);
			//$pdf->SetTextColor(0, 0, 0); #���ڻ��� ���������� ����
			$pdf->Cell($bodyW * 0.07,  28, '', 1, 0, 'C'); #������
			$pdf->Cell($bodyW * 0.15,  7,  '', 1, 0, 'C'); #����ڵ�Ϲ�ȣ Ÿ��Ʋ
			$pdf->Cell($bodyW * 0.315, 7,  getBizStyle($regNo), 1, 0, 'L'); #����ڵ�Ϲ�ȣ ����Ÿ
			$pdf->Cell($bodyW * 0.15,  7,  '', 1, 0, 'C'); #���������ȣ Ÿ��Ʋ
			$pdf->Cell($bodyW * 0.315, 7,  $centerNo, 1, 1, 'L'); #���������ȣ ����Ÿ

			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			# ������ Ÿ��Ʋ
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 25, '��');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 30, '��');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 35, '��');
			# ����ڵ�Ϲ�ȣ Ÿ��Ʋ
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 18, '��   ��   ��');
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 21, '�� �� �� ȣ');
			# ���������ȣ Ÿ��Ʋ
			$pdf->Text($bodyW * 0.535 + $marginLeft + 1.5, $marginTop + 18, '�� �� �� ��');
			$pdf->Text($bodyW * 0.535 + $marginLeft + 1.5, $marginTop + 21, '�� �� �� ȣ');

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15,  7, '��          ȣ', 1, 0, 'C'); #��ȣ Ÿ��Ʋ
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.465, 7, $centerName, 1, 0, 'L');      #��ȣ ����Ÿ
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15,  7, '��ǥ�ڸ�', 1, 0, 'C');       #��ǥ�ڸ� Ÿ��Ʋ
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.165, 7, $masterName, 1, 1, 'L');      #��ǥ�ڸ� ����Ÿ
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15, 7, '', 1, 0, 'C'); #�ּ� Ÿ��Ʋ
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.78, 7, $address1.' '.$address2, 1, 1, 'L'); #�ּ� ����Ÿ
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# �ּ� Ÿ��Ʋ
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 32, '��   ��   ��');
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 35, '��   ��   ��');

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15,  7, '��          ��', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, '����', 1, 0, 'L');         #
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15,  7, '��          ��', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, '�湮���', 1, 1, 'L');       #
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# �׵θ�
			$pdf->SetLineWidth(0.4);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# ���޹޴���(������)
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.07, 14, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '��   ��   ��', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.235, 7, $sugupjaName, 1, 0, 'L'); #
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.23, 7, '�����������ȣ', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, $sugupjaNo, 1, 1, 'L'); #

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15, 7, '�޿�������', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.235, 7, subStr($mDate, 0, 4).'.'.subStr($mDate, 4, 2), 1, 0, 'L'); #
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.23, 7, '�� �� �� �� �� ��', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, $sugupFmDate.'~'.$sugupToDate, 1, 1, 'L'); #
			# ������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			# ���޹޴��� Ÿ��Ʋ �Է�
			$pdf->Text($bodyW * 0.07 + $marginLeft - 7.5, $marginTop + 47, '����');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 7.5, $marginTop + 51, '�޴�');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6,   $marginTop + 55, '��');

			#������ �ۼ��� ���ڻ��� �Ķ������� ����
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# ��ǥ ���
			$tempY = $pdf->GetY();

			# ��ǥ ����
			$pdf->SetXY($marginLeft, $tempY);

			# �׵θ�
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $tempY, $marginLeft + $bodyW, $tempY);
			$pdf->SetLineWidth(0.2);

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 1, 'R'); #

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
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #number_format($boninPay)
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
			$pdf->Cell($bodyW * 0.25, 7, number_format($misuPay), 1, 1, 'R'); #�̼���

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['card']), 1, 1, 'R'); #�ݾ�

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
			$pdf->Cell($bodyW * 0.16, 7, '����', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['cash']), 1, 1, 'R'); #

			# X��ǥ ����
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 35, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '������ü', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['vari']), 1, 1, 'R'); #

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['cash']+$inAmt['card']), 1, 1, 'R'); #

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

			// �������� ��
			$count = 2;
			$coordY = $pdf->GetY()+2;
			$width = $bodyW*0.55;
			$pdf->SetXY($marginLeft, $coordY);
			$pdf->Cell($width, 4, '���������� ��', 0, 1, 'L');

			$pdf->SetFont($fontNameK, '', 7);

			if ($t13_suga_tot1 > 0) $count ++;
			if ($t13_suga_tot2 > 0) $count ++;
			if ($t13_suga_tot3 > 0) $count ++;

			$height = 3.5;
			$coordY = $pdf->GetY() + 1;
			$pdf->Line($marginLeft, $coordY, $marginLeft +$width, $coordY);
			$pdf->Line($marginLeft, $coordY, $marginLeft, $coordY + $height * $count);
			$pdf->Line($marginLeft+$width*0.12, $coordY, $marginLeft+$width*0.12, $coordY + $height * $count);
			$pdf->Line($marginLeft+$width*0.34, $coordY, $marginLeft+$width*0.34, $coordY + $height * $count);
			$pdf->Line($marginLeft+$width*0.56, $coordY, $marginLeft+$width*0.56, $coordY + $height * $count);
			$pdf->Line($marginLeft+$width*0.78, $coordY, $marginLeft+$width*0.78, $coordY + $height * $count);
			$pdf->Line($marginLeft+$width, $coordY, $marginLeft+$width, $coordY + $height * $count);

			$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('����')) / 2, $coordY + $height - 1, '����');
			$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth('����')) / 2, $coordY + $height - 1, '����');
			$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth('���κδ�')) / 2, $coordY + $height - 1, '���κδ�');
			$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth('�ʰ�/��޿�')) / 2, $coordY + $height - 1, '�ʰ�/��޿�');
			$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth('���κδ� ��')) / 2, $coordY + $height - 1, '���κδ� ��');

			if ($t13_suga_tot1 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('���')) / 2, $coordY + $height - 1, '���');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot1))) - 1, $coordY + $height - 1, number_format($t13_suga_tot1));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt1))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt1));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt1))) - 1, $coordY + $height - 1, number_format($t13_over_amt1));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot1))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot1));
			}

			if ($t13_suga_tot2 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('���')) / 2, $coordY + $height - 1, '���');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot2))) - 1, $coordY + $height - 1, number_format($t13_suga_tot2));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt2))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt2));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt2))) - 1, $coordY + $height - 1, number_format($t13_over_amt2));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot2))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot2));
			}

			if ($t13_suga_tot3 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('��ȣ')) / 2, $coordY + $height - 1, '��ȣ');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot3))) - 1, $coordY + $height - 1, number_format($t13_suga_tot3));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt3))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt3));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt3))) - 1, $coordY + $height - 1, number_format($t13_over_amt3));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot3))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot3));
			}

			$coordY += $height;
			$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

			$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('��'))/ 2, $coordY + $height - 1, '��');
			$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot4))) - 1, $coordY + $height - 1, number_format($t13_suga_tot4));
			$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt4))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt4));
			$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt4))) - 1, $coordY + $height - 1, number_format($t13_over_amt4));
			$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot4))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot4));

			$coordY += $height;
			$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

			$coordX = $marginLeft + $width+2;
			$coordY = $pdf->GetY() - $height;
			$width = $bodyW * 0.45 - 2;

			$pdf->SetFont($fontNameK, '', 9);

			$pdf->Text($coordX, $coordY + $height - 1, '�º��κδ�� ���԰���');

			$pdf->SetFont($fontNameK, '', 7);

			$coordY += $height;
			$coordY += $height;
			$pdf->Text($coordX + 3, $coordY - 1, $bankName.'/'.$bankNo.'/'.$bankDepos);

			$pdf->SetFont($fontNameK, '', 9);

			$coordY += $height;
			$coordY += $height;
			$pdf->Text($coordX, $coordY - 1, '����ȭ');

			$coordY += $height;
			$pdf->Text($coordX + 3, $coordY - 1, $centerTel);

			$pdf->SetFont($fontNameK, '', 7);

		}
	}

	$conn->row_free();

	$pdf->Output();
	$conn2 -> close();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
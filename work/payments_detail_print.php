<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');
	$conn2 = new connection();
	$conn3 = new connection();

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
	$year = substr($mDate,0,4);
	$mon = substr($mDate,4,2);
	$month = $mon < 10 ? substr($mDate,5,1) :$mon;
	$date = date('t', $month);
	$day = date("t",mktime(0,0,0,$month,1,$year));	
	$year = date("Y",strtotime("-1 month", mktime(0,0,0,$month,1,$year)));
	$lastMon = date("m",strtotime("-1 month", mktime(0,0,0,$month,1,$year)));
	$lastMonth = $lastMon < 10 ? substr($lastMon,1,1) : $lastMon;
		
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
	$centerTel = $myF->phoneStyle($row['m00_ctel']); //�����ȭ��ȣ
	$bankName = iconv("UTF-8","EUCKR",$definition->GetBankName($row['m00_bank_name'])); //�����
	$bankNo = $row['m00_bank_no']; //���¹�ȣ
	$bankDepos = $row['m00_bank_depos']; //������
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
	
	$sql = "select t13_misu_amt as unpaid_amt
				  from t13sugupja
				 inner join (
					   select m03.c_code as c_code
					   ,      m03.c_kind as c_kind
					   ,      m03.jumin as jumin
					   ,      m03.name as name
					   ,      lvl.m81_name as lvl
					   ,      stp.m81_name as stp
					   ,      m03.kind as kind
					   ,      m03.bonin_yul as bonin_yul
					   ,      m03.sdate as sdt
                       ,      m03.edate as edt
						 from (
							  select m03_ccode as c_code
							  ,      m03_mkind as c_kind
							  ,      m03_jumin as jumin
							  ,      m03_name as name
							  ,      m03_ylvl as lvl
							  ,      m03_skind as kind
							  ,      m03_bonin_yul as bonin_yul
							  ,      m03_sdate as sdate
							  ,      m03_edate as edate
								from m03sugupja
							   where m03_ccode = '$mCode'
								 and m03_mkind = '$mKind'
								 and m03_jumin = '$mJumin'
							   union all
							  select m31_ccode as c_code
							  ,      m31_mkind as c_kind
							  ,      m03_jumin as jumin
							  ,      m03_name as name
							  ,      m31_level as lvl
							  ,      m31_kind as kind
							  ,      m31_bonin_yul as bonin_yul
							  ,      m31_sdate as sdate
							  ,      m31_edate as edate
								from m31sugupja
							   inner join m03sugupja
								  on m03_ccode = m31_ccode
								 and m03_mkind = m31_mkind
								 and m03_jumin = m31_jumin
							   where m31_ccode = '$mCode'
								 and m31_mkind = '$mKind'
								 and m31_jumin = '$mJumin'
							  ) as m03
						inner join m81gubun as lvl
						   on lvl.m81_gbn  = 'LVL'
						  and lvl.m81_code = m03.lvl
						inner join m81gubun as stp
						   on stp.m81_gbn  = 'STP'
						  and stp.m81_code = m03.kind
					   ) as t
					on t13_ccode = t.c_code
				   and t13_mkind = t.c_kind
				   and t13_jumin = t.jumin
				   and t13_pay_date between left(t.sdt, 6) and left(t.edt, 6)
				   and t13_bonin_yul = case when t13_bonin_yul = '1' then t.kind
											when t13_bonin_yul = '2' then t.kind
											when t13_bonin_yul = '3' then t.kind
											when t13_bonin_yul = '9' then t.kind
											else t.bonin_yul end
				   and t13_type  = '2'";

			$sql.= " union all
					select sum(t13_misu_amt) as unpaid_amt
					  from t13sugupja
					 where t13_ccode = '$mCode'
					   and t13_mkind = '$mKind'
					   and t13_jumin = '$mJumin'
					   and t13_pay_date < '$mDate'
					   and t13_type  = '2'";
			
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();
	
	for($j=0; $j<$row_count; $j++){
		$row = $conn->select_row($j);
		
		$misu_pay = $row['unpaid_amt']; //�̳���
	}

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

		$sugupjaName = $row['m03_name'];
		$sugupjaNo   = $row['m03_injung_no'];
		$sugupFmDate = getDateStyle($mDate.'01','.');
		$sugupToDate = getLastYMD(subStr($mDate,0,4), subStr($mDate,4,2));
		$billNo      = $row['t13_bill_no'];
		$boninPay    = $row['t13_bonbu_tot4']; 
		$gongdanPay  = $row['t13_chung_amt4'];
		$totalPay    = $row['t13_suga_tot4'];
		$bonin_tot = $boninPay + $misu_pay; //���κδ���Ѿ�

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

		# ��������
		$marginLeft   = 10;
		$marginTop    = 10;
		$marginRight  = 10;
		$marginBottom = 10;
		$paperWidth   = 297;
		$paperHeight  = 170;
		$bodyWidth    = $paperWidth - $marginLeft - $marginRight;

		$bodyHeight   = $paperHeight - ($marginTop + $marginBottom);
		$bodyT        = $marginTop + 5;
		$bodyW        = $bodyWidth / 2 - $marginRight;


		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetTextColor(0, 0, 0);
		
		$pdf->AddPage();
		for($j=0; $j<2; $j++){
			$pdf->SetXY($marginLeft, $marginTop);
			$pdf->SetLineWidth(0.2);
			$pdf->Rect($marginLeft, $marginTop, $bodyW, $bodyHeight+4);
			
			$pdf->SetFont('����','B',10);
	
			$pdf -> SetX($marginLeft);
			if($j == 0){
				$pdf -> Cell(107, 10,$month.'�� �����޿���� ����(���������)',1,0,'C');
			}else {
				$pdf -> Cell(107, 10,$month.'�� �����޿���� ����(�����ڿ�)',1,0,'C');
			}
			$pdf->SetFont('����','',8);
			$pdf -> Cell(21.5, 5,'�� ����',1,1,'C');
			$pdf -> SetX($marginLeft+107);
			$pdf -> Cell(21.5, 5,'�� �߰�',1,1,'C');
			
			$pdf -> text($marginLeft+113.5, $marginTop+3, 'v');

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(20, 9,'',1,0,'C');
			$pdf -> Cell(56, 9,$centerNo,1,0,'L');
			$pdf -> Cell(31, 9,'���������',1,0,'C');
			$pdf -> Cell(21.5, 9,'',1,1,'L');	

			$pdf -> text($marginLeft+5.5, $marginTop+13.5, '�����');
			$pdf -> text($marginLeft+5.5, $marginTop+17.5, '�����ȣ');

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(20, 9,'�ּ�',1,0,'C');
			$pdf -> Cell(56, 9,'',1,0,'C');
			$pdf -> Cell(31, 9,'����ڵ�Ϲ�ȣ',1,0,'C');
			$pdf -> Cell(21.5, 9,$regNo,1,1,'L');

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(20, 9,'����',1,0,'C');
			$pdf -> Cell(23, 9,'',1,0,'C');
			$pdf -> Cell(33, 9,'�޿������Ⱓ',1,0,'C');
			$pdf -> Cell(52.5, 9,'������ ��ȣ',1,1,'C');
			
			$pdf -> text($marginLeft+26, $marginTop+31.5, '�����');
			$pdf -> text($marginLeft+26, $marginTop+35.5, '������ȣ');

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(20, 6,$sugupjaName,1,0,'C');
			$pdf->SetFont('����','',7);
			$pdf -> Cell(23, 6,$sugupjaNo,1,0,'C');
			$pdf -> Cell(33, 6,$sugupFmDate.' ~ '.$sugupToDate,1,0,'C');
			$pdf->SetFont('����','',8);
			$pdf -> Cell(52.5, 6,$billNo,1,1,'C');
			
			$pdf -> SetX($marginLeft);
			$pdf -> Cell(43, 6,'�׸�',1,0,'C');
			$pdf -> Cell(33, 6,'�ݾ�',1,0,'C');
			$pdf -> Cell(52.5, 6,'�ݾ׻�������',1,1,'C');

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(10, 16,'',1,0,'C');
			$pdf -> Cell(33, 4,'���κδ�ݨ�',1,0,'C');
			$pdf -> Cell(33, 4,number_format($boninPay),1,0,'R');

			if($j == 0){
				$pdf -> Cell(31.5, 8,'�̳��ר�',1,0,'C');
			}else {
				$pdf -> Cell(31.5, 8,'�̳��ר�',1,0,'C');
			}
			$pdf -> Cell(21, 8,number_format($misu_pay),1,1,'R'); //�̳���
			
			$pdf -> line($marginLeft+43, $marginTop+53, $marginLeft+43, $marginTop+57);
			$pdf -> text($marginLeft+3.5, $marginTop+56, '��');
			$pdf -> text($marginLeft+3.5, $marginTop+61, '��');
			$pdf -> text($marginLeft+18.5, $marginTop+56, '���ܺδ�ݨ�');


			$pdf -> SetX($marginLeft+10);
			$pdf -> Cell(33, 8, '�޿� ���(��+��)', 1, 0, 'C');
			$pdf -> Cell(33, 8, number_format($totalPay), 1, 0, 'R');
			$pdf -> Cell(31.5, 8, '���κδ��Ѿר�(��+��)', 1, 0 , 'C');
			$pdf -> Cell(21, 8,number_format($bonin_tot), 1, 1 , 'R');
			
			$pdf -> SetX($marginLeft);
			$pdf -> Cell(22, 5, '��系��(��)', 1, 0, 'C');
			$pdf -> Cell(21, 5, '��Ƚ��(ȸ)', 1, 0, 'C');
			$pdf -> Cell(33, 5, '�޿�����(��)', 1, 0 , 'C');
			$pdf -> Cell(31.5, 5, '�հ�(��)', 1, 0 , 'C');
			$pdf -> Cell(21, 5, '���', 1, 1 , 'C');
			

			$sql = "select	   m01_suga_cont
						,      count(t01_suga_code1)
						,      t01_suga_tot
						,      sum(t01_suga_tot)
						  from t01iljung
						 inner join (
							   select m03_sdate, m03_edate, m03_bonin_yul
								 from m03sugupja
								where m03_ccode = '$mCode'
								  and m03_mkind = '$mKind'
								  and m03_jumin = '$mJumin'
								union all
							   select m31_sdate, m31_edate, m31_bonin_yul
								 from m31sugupja
								where m31_ccode = '$mCode'
								  and m31_mkind = '$mKind'
								  and m31_jumin = '$mJumin'
							   ) as sugupja
							on t01_sugup_date between m03_sdate and m03_edate
						 inner join (
							   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
								 from m01suga
								where m01_mcode = '$mCode'
								union all
							   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
								 from m11suga
								where m11_mcode = '$mCode'
							   ) as suga
							on t01_suga_code1 = m01_mcode2
						   and t01_sugup_date between m01_sdate and m01_edate
						 where t01_ccode = '$mCode'
						   and t01_mkind = '$mKind'
						   and t01_jumin = '$mJumin'
						   and t01_sugup_date like '$mDate%'
						   and t01_del_yn = 'N'
						 group by t01_svc_subcode, m01_suga_cont, t01_suga_code1, t01_suga_tot
						 order by t01_svc_subcode";
				$conn2->query($sql);
				$conn2->fetch();
				$row_count2 = $conn2->row_count();

				$total	= 0;
				
				for($i=0; $i<$row_count2; $i++){
					$row2 = $conn2->select_row($i);
					$pdf->SetFont('����','',7);
					$pdf -> SetX($marginLeft);
					$pdf -> Cell(22, 5, $row2['m01_suga_cont'], 1, 0, 'L');
					$pdf -> Cell(21, 5, $row2['count(t01_suga_code1)'], 1, 0, 'R');
					$pdf -> Cell(33, 5, number_format($row2['t01_suga_tot']), 1, 0 , 'R');
					$pdf -> Cell(31.5, 5, number_format($row2['sum(t01_suga_tot)']), 1, 0 , 'R');
					$pdf -> Cell(21, 5, '', 1, 1 , 'C');

					$total += $row2['sum(t01_suga_tot)'];
				}
	
				for($k=$i; $k<11; $k++){
					$pdf -> SetX($marginLeft);
					$pdf -> Cell(22, 5, '', 1, 0, 'L');
					$pdf -> Cell(21, 5, '', 1, 0, 'R');
					$pdf -> Cell(33, 5, '', 1, 0 , 'R');
					$pdf -> Cell(31.5, 5, '', 1, 0 , 'R');
					$pdf -> Cell(21, 5, '', 1, 1 , 'C');
				}
				
				$conn2 -> row_free();

			$pdf -> SetX($marginLeft);
			$pdf -> Cell(76, 4, '��  ��', 1, 0, 'C');
			$pdf -> Cell(31.5, 4, number_format($total), 1, 0 , 'R');
			$pdf -> Cell(21, 4, '', 1, 1 , 'C');
			
			$pdf->SetFont('����','',9);
			$pdf -> SetX($marginLeft);
			$pdf -> Cell($bodyW, 16, '', 1, 1, 'C');
			
			$pdf->SetFont('����','B',9);
			$pdf -> text($marginLeft+2, $marginTop+135, $bankName.':'.$bankNo.'(������ : '.$bankDepos.'/'.$centerName.')');
			$pdf -> text($marginLeft+17, $marginTop+140, '�ſ� 10��');
			
			$pdf->SetFont('����','B',8);
			$pdf -> text($marginLeft+32, $marginTop+140, '���� ����� ����');

			$pdf->SetFont('����','',8);
			$pdf -> text($marginLeft+2, $marginTop+140, '������ü��');
			$pdf -> text($marginLeft+56.5, $marginTop+140, '���� �Ա� ��Ź�帳�ϴ�.');
			
			$pdf -> text($marginLeft+65, $marginTop+148, $year.'  .     '.$month.'  .     '.$day.'.');
			$pdf -> text($marginLeft+6, $marginTop+153, '��������� :   '.$centerName);
			$pdf -> text($marginLeft+80, $marginTop+153, '��ǥ�ڸ� :   '.$masterName);
		
			$pdf -> SetX($marginLeft);
			$pdf -> Cell($bodyW, 9, '', 1, 1, 'C');
			
			$sql = "select max(deposit_reg_dt) as deposit_reg_dt, sum(deposit_amt) as deposit_amt
					  from unpaid_deposit
					 where org_no ='$mCode'
					   and deposit_jumin = '$mJumin'
					   and deposit_reg_dt like '".$year.'-'.$lastMon."%'"; 
			$lastbonin = $conn3 -> get_array($sql);

			$pdf->SetFont('����','',10);
			$pdf -> text($marginLeft+46, $marginTop+161, $lastMonth.'�� ���κδ�� ������');
			$pdf->SetFont('����','',9);
			$pdf -> text($marginLeft+43, $marginTop+170, '�ϱ�                      ��(��      )');
			$pdf -> text($marginLeft+17, $marginTop+175, '���ݾ��� _______��____��___�� ����,                    �� �����Ͽ���.');
			$pdf -> text($marginLeft+70, $marginTop+183, $printYear.'�� '.$printMonth.'�� '.$printDay.'�� ');
			$pdf -> text($marginLeft+59, $marginTop+188, $centerName);
			$pdf->SetFont('����','BU',10);
			$pdf -> text($marginLeft+77, $marginTop+175, '�������Ա�');	

			$pdf -> SetXY($marginLeft, $marginTop+157);
			$pdf -> Rect($marginLeft, $marginTop+157, $bodyW, 33);	

			
			# ��������
			$marginLeft   = 158.5;
			$paperWidth   = 297;
			$paperHeight  = 170;
			$bodyWidth    = $paperWidth - 10 - $marginRight;
			$bodyHeight   = $paperHeight - ($marginTop + $marginBottom);


		}
		
		$pdf->SetFont('����','',8);
		//���ܺδ��
		$pdf -> SetXY(52, $marginTop+53);
		$pdf -> Cell(34, 4, number_format($gongdanPay),0,0,'R');

		$pdf -> SetXY($marginLeft+42, $marginTop+53);
		$pdf -> Cell(34, 4, number_format($gongdanPay),0,0,'R');
		
		//�ּ�
		if(strlen($address1.$address2) <= 36 ){
			$pdf->SetFont('����','',8);
			$pdf -> SetXY(32, $marginTop+19);
			$pdf -> Cell(54, 9,$address1.$address2,0,0,'L');
		}else {
			$pdf->SetFont('����','',7);
			$pdf -> SetXY(32, $marginTop+19.5);
			$pdf -> multiCell(60, 4,$address1.$address2);
		}
		
		if(strlen($address1.$address2) <= 36 ){
			$pdf->SetFont('����','',8);
			$pdf -> SetXY($marginLeft+22, $marginTop+19);
			$pdf -> Cell(54, 9, $address1.$address2,0,0,'L');
		}else {
			$pdf->SetFont('����','',7);
			$pdf -> SetXY($marginLeft+22, $marginTop+19.5);
			$pdf -> multiCell(60, 4,$address1.$address2);
		}
		
		$pdf->SetFont('����','',7);

		//�����
		if(strlen($centerName) <= 16 ){
			$pdf -> SetXY(117, $marginTop+10);
			$pdf -> Cell(21.5, 9,$centerName,0,0,'L');
		}else {
			$pdf -> SetXY(117, 24.5);
			$pdf -> multiCell(22, 4,$centerName);
		}

		if(strlen($centerName) <= 16 ){
			$pdf -> SetXY($marginLeft+107, $marginTop+10);
			$pdf -> Cell(21.5, 9,$centerName,0,0,'L');
		}else {
			$pdf -> SetXY($marginLeft+107, $marginTop+10.5);
			$pdf -> multiCell(22, 4,$centerName);
		}

		//���� ���κδ�� ������
		$pdf -> SetXY(61, $marginTop+166.5);
		$pdf -> Cell(21, 5, $lastbonin['deposit_amt'] != '' ? $lastbonin['deposit_amt'] : '0', 0,0,'R');

		$pdf -> SetXY(61, $marginTop+166.5);
		$pdf -> Cell($marginLeft+11, 5, $lastbonin['deposit_amt'] != '' ? $lastbonin['deposit_amt'] : '0', 0,0,'R');

		$pdf -> SetXY(44, $marginTop+171);
		$pdf -> Cell(29, 5, substr($lastbonin['deposit_reg_dt'],0,4).'        '.substr($lastbonin['deposit_reg_dt'],5,2).'      '.substr($lastbonin['deposit_reg_dt'],8,2), 0,0,'R');
		
		$pdf -> SetXY($marginLeft+34, $marginTop+171);
		$pdf -> Cell(29, 5, substr($lastbonin['deposit_reg_dt'],0,4).'        '.substr($lastbonin['deposit_reg_dt'],5,2).'      '.substr($lastbonin['deposit_reg_dt'],8,2), 0,0,'R');
		
		# �߾� ���뼱
		for($i=$marginTop; $i<=($paperHeight * 0.57); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}
		$pdf->SetFont($fontNameK, '', 8);
		$pdf->Text($paperWidth / 2 - 2.5, $paperHeight / 2 - 3 + 21, '(��)');
		for($i=($marginTop+($paperHeight * 0.57)); $i<=($bodyHeight+35 + $bodyT); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}
		
		$pdf->SetLineWidth(0.6);
		$pdf -> Rect(117.5, 67, 21, 8);
		$pdf -> Rect($marginLeft+107.5, 67, 21, 8);
		$pdf->SetLineWidth(0.2);
	}
	$pdf->Output();
	
	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
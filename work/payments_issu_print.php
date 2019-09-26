<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	require_once('../pdf/korean.php');

	$conn->set_name('euckr');

	$mCode     = $_GET['mCode'];
	$mDate     = $_GET['mDate'];
	$mHomecare = $_GET['mHomecare'];
	$mVoucher  = $_GET['mVoucher'];

	$marginLeft = 14;
	$marginTop  = 21;

	$f=new PDF_Korean('P');
	$f->AddUHCFont('����','Gulim');
	$f->Open();
	$f->SetFillColor(238,238,238);

	$sql = "select t15_mkind as kind
			,      t15_billno"
		 . ",      m03_name"
		 . ",      m03_skind
			,      t13_suga_tot4"
		 . ",      t13_bonbu_tot4"
		 . ",      t13_bonin_amt4"
		 . ",      t15_date"
		 . "  from t15paymentissu"
		 . " inner join t13sugupja"
		 . "    on t13_ccode = t15_ccode"
		 . "   and t13_mkind = t15_mkind"
		 . "   and t13_jumin = t15_jumin"
		 . "   and t13_pay_date  = t15_pay_date"
		 . "   and t13_bonin_yul = t15_boninyul"
		 . "   and t13_type = '2'"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t15_ccode"
		 . "   and m03_mkind = t15_mkind"
		 . "   and m03_jumin = t15_jumin"
		 . " where t15_ccode    = '".$mCode
		 . "'  and t15_pay_date = '".$mDate
		 . "'";

	if ($mHomecare == 'Y' && $mVoucher == 'Y'){
	}else if ($mHomecare == 'Y'){
		$sql .= " and t13_mkind = '0'";
	}else{
		$sql .= " and t13_mkind != '0'";
	}

	$sql .= " order by m03_name";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	$page_count = 0;
	$total_page = ceil($row_count / 30);

	$sumBoninPay = 0;
	$sumPay = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($i % 30 == 0){
			$page_count ++;

			drawPageTitle($f, $marginLeft, $marginTop, $mDate, $total_page, $page_count, $row_count);
		}

		/*
		switch($row['m03_skind']){
			case '1': $boninGbn = '�Ϲ�(15%)'; break;
			case '2': $boninGbn = '�Ƿ�(7.5%)'; break;
			case '3': $boninGbn = '����(0%)'; break;
			case '4': $boninGbn = '�氨(7.5%)'; break;

		}
		*/
		$boninGbn = $myF->euckr($conn->kind_name_svc($row['kind']));

		drawPageList($f, $marginLeft, $i + 1, $row['t15_billno'], $row['m03_name'], $boninGbn, $row['t13_suga_tot4'], $row['t13_bonbu_tot4'], $row['t15_date']);

		$sumBoninPay += $row['t13_bonin_amt4'];
		$sumPay += $row['t13_bonbu_tot4'];
	}

	drawPageTotal($f, $marginLeft, $sumBoninPay, $sumPay, $sumBoninPay);

	$conn->row_free();

	$f->Output();

	include('../inc/_db_close.php');

	# Ÿ��Ʋ
	function drawPageTitle($f, $marginLeft, $marginTop, $mDate, $pTotalPage, $pNowPage, $pCount){
		$f->AddPage();
		$f->SetXY($marginLeft, $marginTop);
		$f->SetX($marginLeft);
		$f->SetFont('����', '', 12);
		$f->Cell(182, 8, '�����޿���� ���� �߱޴���('.$pNowPage.'/'.$pTotalPage.')', 0, 1, 'C');
		$f->SetX($marginLeft);
		$f->SetFont('����', '', 9);
		$f->Cell(82, 6, '�޿������� : '.SubStr($mDate, 0, 4).'��'.SubStr($mDate, 4, 2).'�� / �ѹ߱ޱǼ� : '.$pCount, 0, 1, 'L');

		$f->SetX($marginLeft);
		$f->SetFont('����', 'b', 11);
		$f->Cell(15, 8, '����',       1, 0, 'C', true);
		$f->Cell(30, 8, '��������ȣ', 1, 0, 'C', true);
		$f->Cell(20, 8, '�����ڸ�',   1, 0, 'C', true);
		$f->Cell(25, 8, '���񽺱���', 1, 0, 'C', true);
		$f->Cell(23, 8, '�޿��Ѿ�',   1, 0, 'C', true);
		$f->Cell(23, 8, '���κδ��', 1, 0, 'C', true);
		$f->Cell(23, 8, '�߱���',     1, 0, 'C', true);
		$f->Cell(23, 8, '���',       1, 1, 'C', true);
	}

	# ����Ʈ ���
	function drawPageList($f, $marginLeft, $pNo, $pBillNo, $pName, $pGubun, $pSumPay, $pBoninPay, $pIssuDate){
		$f->SetX($marginLeft);
		$f->SetFont('����', '', 9);
		$f->Cell(15, 7.5, ''.$pNo.'', 1, 0, 'C');
		$f->Cell(30, 7.5, $pBillNo, 1, 0, 'C');
		$f->Cell(20, 7.5, $pName, 1, 0, 'C');
		$f->Cell(25, 7.5, $pGubun, 1, 0, 'L');
		$f->Cell(23, 7.5, ''.number_format($pSumPay).'', 1, 0, 'R');
		$f->Cell(23, 7.5, ''.number_format($pBoninPay).'', 1, 0, 'R');
		$f->Cell(23, 7.5, getDateStyle($pIssuDate, '.'), 1, 0, 'C');
		$f->Cell(23, 7.5, $pBiSub, 1, 1, 'R');
	}

	# ������ ��
	function drawPageTotal($f, $marginLeft, $pSumPay, $pSumBoninPay){
		$f->SetX($marginLeft);
		$f->SetFont('����', 'b', 11);
		$f->Cell(90, 8, '��', 1, 0, 'C', true);
		$f->Cell(23, 8, ''.number_format($pSumPay).'', 1, 0, 'R', true);
		$f->Cell(23, 8, ''.number_format($pSumBoninPay).'', 1, 0, 'R', true);
		$f->Cell(23, 8, '', 1, 0, 'R', true);
		$f->Cell(23, 8, '', 1, 1, 'R', true);
	}
?>
<script>self.focus();</script>
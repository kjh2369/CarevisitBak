<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');

	$mCode	= $_GET['mCode'];
	$mKind	= $_GET['mKind'];
	$mDate	= $_GET['mDate'];
	$mType	= $_GET['mType'];


	$marginLeft = 14;
	$marginTop  = 21;

	$f=new PDF_Korean('P');
	$f->AddUHCFont('����','Gulim');
	$f->Open();
	$f->SetFillColor(238,238,238);

	$sql = "select t14_date"
		 . ",      t14_jumin"
		 . ",      m03_name"
		 . ",      m03_skind"
		 . ",      t14_type"
		 . ",      t14_amount"
		 . "  from t14deposit"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t14_ccode"
		 . "   and m03_mkind = t14_mkind"
		 . "   and m03_jumin = t14_jumin"
		 . " where t14_ccode = '".$mCode
		 . "'  and t14_mkind = '".$mKind
		 . "'  and t14_pay_date = '".$mDate
		 . "'  and t14_amount > 0";

	switch($mType){
		case '1': $sql .= " order by t14_date, m03_name"; break;
		case '2': $sql .= " order by m03_name, t14_date"; break;
		case '3': $sql .= " order by t14_date, m03_name"; break;
	}

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	$page_count = 0;
	$total_page = ceil($row_count / 30);

	$sumBoninPay = 0;
	$sumBiPay = 0;

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($i % 30 == 0){
				# ������ ��
				if ($page_count > 0){
					drawPageTotal($f, $marginLeft, $sumBoninPay, $sumBiPay);
				}

				$page_count ++;

				drawPageTitle($f, $marginLeft, $marginTop, $mType, $mDate, $total_page, $page_count);
			}

			switch($row['m03_skind']){
				case '1': $boninGbn = '�Ϲ�(15%)'; break;
				case '2': $boninGbn = '�Ƿ�(7.5%)'; break;
				case '3': $boninGbn = '����(0%)'; break;
				case '4': $boninGbn = '�氨(7.5%)'; break;
			}

			drawPageList($f, $marginLeft, $i + 1, $row['t14_date'], $row['m03_name'], $boninGbn, $row['t14_amount'], 0, '');

			$sumBoninPay += $row['t14_amount'];
		}
	}else{
		drawPageTitle($f, $marginLeft, $marginTop, $mType, $mDate, 1, 1);
	}

	drawPageTotal($f, $marginLeft, $sumBoninPay, $sumBiPay);

	$conn->row_free();

	$f->Output();

	include('../inc/_db_close.php');

	# Ÿ��Ʋ
	function drawPageTitle($f, $marginLeft, $marginTop, $mType, $mDate, $pTotalPage, $pNowPage){
		$f->AddPage();
		$f->SetXY($marginLeft, $marginTop);
		$f->SetFont('����', '', 7);
		$f->Cell(182, 5, '[���� ��34ȣ����]', 0, 1, 'L');
		$f->SetX($marginLeft);
		$f->SetFont('����', '', 12);
		$f->Cell(182, 14, '', 1, 0, 'C');
		$f->SetX($marginLeft);
		$f->Cell(182, 8, '���κδ�ݼ�������('.$pNowPage.'/'.$pTotalPage.')', 0, 1, 'C');
		$f->SetX(20);

		switch($mType){
			case '1': $f->Cell(182, 6, subStr($mDate, 0, 4).'��'.subStr($mDate, 4, 2).'��(�������ں�)', 0, 1, 'L'); break;
			case '2': $f->Cell(182, 6, subStr($mDate, 0, 4).'��'.subStr($mDate, 4, 2).'��(û������[�����ټ���])', 0, 1, 'L'); break;
			case '3': $f->Cell(182, 6, subStr($mDate, 0, 4).'��'.subStr($mDate, 4, 2).'��(û������[�����Ϻ�])', 0, 1, 'L'); break;
		}

		$f->SetX($marginLeft);
		$f->SetFont('����', 'b', 11);
		$f->Cell(15, 24, '����', 1, 0, 'C', true);
		$f->Cell(25, 24, '����', 1, 0, 'C', true);
		$f->Cell(25, 24, '����', 1, 0, 'C', true);
		$f->Cell(25, 24, '', 1, 0, 'C', true);
		$f->Cell(92, 8, '�����ݾ�(��)', 1, 1, 'C', true);
		$tempY1 = $f->GetY();
		$f->SetXY($marginLeft + 65, $f->GetY() - 3);
		$f->Cell(25, 7, '�����', 0, 2, 'C');
		$f->Cell(25, 7, '����', 0, 2, 'C');
		$f->SetXY($marginLeft + 90, $tempY1);
		$f->Cell(23, 16, '��',     1, 0, 'C', true);
		$f->Cell(23, 8,  '�޿�',   1, 0, 'C', true);
		$f->Cell(46, 8,  '��޿�', 1, 2, 'C', true);
		$tempY1 = $f->GetY();
		$f->SetXY($marginLeft + 113, $tempY1);
		$f->Cell(23, 8,  '���κδ��', 1, 0, 'C', true);
		$f->Cell(23, 8,  '�޾�', 1, 0, 'C', true);
		$f->Cell(23, 8,  '�׸�', 1, 1, 'C', true);
	}

	# ����Ʈ ���
	function drawPageList($f, $marginLeft, $pNo, $pDate, $pName, $pGubun, $pBoninPay, $pBiPay, $pBiSub){
		$f->SetX($marginLeft);
		$f->SetFont('����', '', 9);
		$f->Cell(15, 8, ''.$pNo.'', 1, 0, 'C');
		$f->Cell(25, 8, getDateStyle($pDate, '.'), 1, 0, 'C');
		$f->Cell(25, 8, $pName, 1, 0, 'C');
		$f->Cell(25, 8, $pGubun, 1, 0, 'C');
		$f->Cell(23, 8, ''.number_format($pBoninPay + $pBiPay).'', 1, 0, 'R');
		$f->Cell(23, 8, ''.number_format($pBoninPay).'', 1, 0, 'R');
		$f->Cell(23, 8, ''.number_format($pBiPay).'', 1, 0, 'R');
		$f->Cell(23, 8, $pBiSub, 1, 1, 'R');
	}

	# ������ ��
	function drawPageTotal($f, $marginLeft, $pBoninPay, $pBiPay){
		$f->SetX($marginLeft);
		$f->SetFont('����', 'b', 11);
		$f->Cell(90, 8, '��', 1, 0, 'C', true);
		$f->Cell(23, 8, ''.number_format($pBoninPay + $pBiPay).'', 1, 0, 'R', true);
		$f->Cell(23, 8, ''.number_format($pBoninPay).'', 1, 0, 'R', true);
		$f->Cell(23, 8, ''.number_format($pBiPay).'', 1, 0, 'R', true);
		$f->Cell(23, 8, '', 1, 1, 'R', true);
	}
?>
<script>self.focus();</script>
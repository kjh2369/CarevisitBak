<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');

	# 출력일자
	$printYear  = date('Y', mkTime());
	$printMonth = date('m', mkTime());
	$printDay   = date('d', mkTime());

	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mDate = $_GET['mDate'];
	$mKey  = $_GET['mKey'];
	$mBoninYul = $_GET['mBoninYul'];

	# 기관정보 조회
	$sql = "select m00_code1, m00_cname, m00_mname, m00_cpostno, m00_caddr1, m00_caddr2, m00_ccode"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$centerNo   = $row['m00_code1']; //기관기호
	$centerName = $row['m00_cname']; //기관명
	$masterName = $row['m00_mname']; //대표자명
	$regNo      = $row['m00_ccode']; //사업자등록번호
	$postNo     = subStr($row['m00_cpostno'],0,3).'-'.subStr($row['m00_cpostno'],3,3); //우편번호
	$address    = $row['m00_caddr1'].' '.$row['m00_caddr2']; //주소
	$conn->row_free();

	# 수급자 주민번호 조회
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);

	# 수급자 정보및 급여내역 조회
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

	# 납부내역 조회
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

	//급여제공기간
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

	# 명세서 발급일 저장
	setPaymentIssu($conn, $mCode, $mKind, $mJumin, $mDate, $mBoninYul, $billNo, date('Ymd',mkTime()));

	$pdf=new PDF_Korean('P');
	$pdf->AddUHCFont('굴림','Gulim');
	$pdf->Open();
	
	$pdf->SetFillColor(238,238,238);
	
	$pdf->AddPage();
	$pdf->SetXY(14, 21);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect(14, 26, 182, 250);
	$pdf->SetLineWidth(0.2);

	$pdf->SetFont('굴림','',9);
	$pdf->Cell(182,5,'[별지 제24호서식]',0,1,'L');
	
	$pdf->SetX(14);
	$pdf->SetFont('굴림','',12);
	$pdf->Cell(150,14,'장기요양급여비용 명세서',1,0,'C');
	$pdf->SetFont('굴림','',10);
	$pdf->Cell(32,7,'□퇴소',1,2,'C');
	$pdf->Cell(32,7,'□중간',1,1,'C');

	$pdf->SetFont('굴림','',11);

	$pdf->SetX(14);
	$pdf->Cell(20,10,'',1,0,'C'); //장기요양기관기호
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(40,10,$centerNo,1,0,'L');
	$pdf->SetFont('굴림','',10);
	$pdf->Cell(30,10,'장기요양기관명',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(92,10,$centerName,1,1,'L');
	$pdf->SetFont('굴림','',10);

	$pdf->SetX(14);
	$pdf->Cell(20,14,'주소',1,0,'C');
	$pdf->Cell(94,14,'',1,0,'L');
	$pdf->Cell(30,14,'사업자등록번호',1,0,'C');
	$pdf->SetFont('굴림','b',10);
	$pdf->Cell(38,14,getBizStyle($regNo),1,1,'L'); #사업자등록번호
	$pdf->SetFont('굴림','',10);

	# 장기요양기관기호
	$pdf->Text(16,44,'장기요양');
	$pdf->Text(16,48,'기관기호');

	#주소
	$pdf->SetFont('굴림','b',10);
	$pdf->Text(35,55, '['.$postNo.']');
	$pdf->Text(35,61, $address);
	$pdf->SetFont('굴림','',10);

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	$pdf->SetX(14);
	$pdf->Cell(32,10,'성명',1,0,'C');
	$pdf->Cell(45,10,'장기요양인정번호',1,0,'C');
	$pdf->Cell(60,10,'급여제공기간',1,0,'C');
	$pdf->Cell(45,10,'영수증 번호',1,1,'C');

	$pdf->SetX(14);
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(32,10,$sugupjaName,1,0,'C'); #수급자성명
	$pdf->Cell(45,10,$sugupjaNo,1,0,'C'); #수급자 장기요양인정번호
	$pdf->Cell(60,10,$sugupFmDate.'~'.$sugupToDate,1,0,'C'); #수급자 급여제공기간
	$pdf->Cell(45,10,$billNo,1,1,'C'); #영수증번호

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	$tempY1 = $pdf->GetY();
	$pdf->SetX(14);
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(45,10,'항목',1,0,'C');
	$pdf->Cell(46,10,'금액',1,0,'C');
	$pdf->Cell(91,10,'금액산정내역',1,1,'C');

	$pdf->SetX(14);
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(10,30,'',1,0,'C');
	$pdf->Cell(35,10,'본인부담금①',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(46,10,number_format($boninPay),1,0,'R'); #본인부담금
	$pdf->Cell(45,10,'',1,0,'C');
	$pdf->Cell(46,10,number_format($totalPay),1,2,'R'); #총액
	$pdf->SetFont('굴림','',11);

	$pdf->SetX(24);
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(35,10,'공단부담금②',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(46,10,number_format($gongdanPay),1,0,'R'); #공단부담금
	$pdf->Cell(45,10,'',1,0,'C');
	$pdf->Cell(46,10,number_format($boninPay),1,1,'R'); #본인부담총액
	$pdf->SetFont('굴림','',11);

	$pdf->SetX(24);
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(35,10,'급여 계③(①+②)',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(46,10,number_format($totalPay),1,0,'R'); #급여계
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(45,10,'이미 납부한 금액⑪',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(46,10,'',1,1,'R'); #이미 납부한 금액 //number_format($deposit)
	$pdf->SetFont('굴림','',11);

	$pdf->SetX(14);
	$pdf->SetFont('굴림','',11);
	$pdf->Cell(10,90,'',1,0,'C');
	$pdf->Cell(35,10,'식사재료비④',1,0,'C');
	$pdf->Cell(46,10,'',1,0,'C');
	$pdf->Cell(45,10,'미수금',1,0,'C');
	$pdf->SetFont('굴림','b',11);
	$pdf->Cell(46,10,number_format($misuPay),1,1,'R'); #미수금
	$pdf->SetFont('굴림','',11);

	$tempY3 = $pdf->GetY();

	$pdf->SetX(24);
	$pdf->Cell(35,10,'',1,0,'C'); //상급침실 이용에 따른 추가비용⑤
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(24);
	$pdf->Cell(35,10,'이.미용비⑥',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');
	$pdf->SetX(24);
	$pdf->Cell(13,50,'',1,2,'C'); //기타
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
	$pdf->Cell(25,10,'카드',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'현금영수증',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'현금',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(125);
	$pdf->Cell(25,10,'합계',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(91,10,'현금영수증',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(45,10,'신분확인번호',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(45,10,'현금승인번호',1,0,'C');
	$pdf->Cell(46,10,'',1,2,'C');

	$pdf->SetX(105);
	$pdf->Cell(91,10,'※비고',1,1,'L');

	$tempY1 = $pdf->GetY();

	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $tempY1, 196, $tempY1);
	$pdf->SetLineWidth(0.2);

	$pdf->Text(17,108,'급');
	$pdf->Text(17,112,'여');

	$pdf->Text(110,98,'총액(급여+비급여)');
	$pdf->Text(118,102.5,'⑨(③+⑧)');

	$pdf->Text(115,108,'본인부담총액');
	$pdf->Text(118,112.5,'⑩(①+⑧)');
	
	$pdf->Text(17,163,'비');
	$pdf->Text(17,168,'급');
	$pdf->Text(17,172,'여');

	$pdf->Text(27,138.5,'상급침실 이용에');
	$pdf->Text(27,142.5,'따른 추가비용⑤');

	$pdf->Text(26.5,180,'기타');
	$pdf->Text(28.5,184.5,'⑦');

	$pdf->Text(33,208,'비급여 계');
	$pdf->Text(25.5,212.5,'⑧(④+⑤+⑥+⑦)');

	$pdf->Text(107,152,'수납금액');
	$pdf->Text(113,156,'⑫');
	$pdf->Text(107.5,160,'(⑩-⑪)');

	# 신용카드를 사용하실 떄 시작
	$pdf->SetX(14);
	$pdf->Cell(23,20,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'할부',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(33,10,'사용금액',1,2,'C');
	$pdf->SetX(37);
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(12,10,'',1,0,'C');
	$pdf->Cell(30,10,'',1,0,'C');
	$pdf->Cell(33,10,'',1,2,'C');

	$pdf->Text(15.5, 223, '신용카드를');
	$pdf->Text(15.5, 227, '사용하실때');
	$pdf->Text(39, 218.5, '회원');
	$pdf->Text(39, 222.5, '번호');
	$pdf->Text(81, 218.5, '승인');
	$pdf->Text(81, 222.5, '번호');
	$pdf->Text(39, 228, '카드');
	$pdf->Text(39, 232, '종류');
	$pdf->Text(81, 228, '유호');
	$pdf->Text(81, 232, '기간');
	$pdf->Text(121.2, 228, '가맹점');
	$pdf->Text(123, 232, '번호');
	
	# 테두리
	$pdf->SetLineWidth(0.6);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetLineWidth(0.2);

	# 서명
	$pdf->SetXY(14, $pdf->GetY() + 14);
	$pdf->Line(14, $pdf->GetY(), 196, $pdf->GetY());
	$pdf->SetFont('굴림','b',13);
	//$pdf->Text(81,  239, $printYear.'.');   //년
	//$pdf->Text(121, 239, $printMonth.'.');  //월
	$pdf->Text(161, 239, $printYear.'.'.$printMonth.'.'.$printDay);    //일
	$pdf->SetFont('굴림','',11);
	$pdf->Text(21, 245, '장기요양기관명 : ');
	$pdf->SetFont('굴림','b',11);
	$pdf->Text(52, 245, $centerName); //기관명
	$pdf->SetFont('굴림','',11);
	$pdf->Text(140, 245, '대표자명 : ');
	$pdf->SetFont('굴림','b',11);
	$pdf->Text(160, 245, $masterName); //대표자명
	$pdf->SetFont('굴림','',8);
	$pdf->Text(180, 245, '(인)');

	# 설명
	$pdf->SetFont('굴림','',11);
	$pdf->Text(16, 253, '* 이 명세서(영수증)는 「소득세법」에 따른 의료비 또는 「조세특례제한법」에 따른 현금영수증(현금');
	$pdf->Text(19.1, 257, '영수증 승인번호가 기개된 경우) 공제신청에 사용할 수 있습니다. 다만,  지출증빙용으로 발급된 현금');
	$pdf->Text(19.1, 261, '영수증(지출증빙)은 공제신청에 사용할 수 없습니다.');
	$pdf->Text(16, 265, '* 이 명세서(영수증)에 대한 세부내역을 요구할 수 있습니다.');
	$pdf->Text(16, 269, '* 비고란은 장기요양기관의 임의활용란으로 사용합니다. 다만, 복지용구의 경우 품목과 구입. 대여를 구');
	$pdf->Text(19.1, 273, '분하여 기재하시기 바랍니다.');
	
	# 푸터
	$pdf->SetFont('굴림','b',10);
	$pdf->Text(14, 280, '본인부담금 납입계좌: ');
	$pdf->Text(14, 284, '전화 : ');
	
	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
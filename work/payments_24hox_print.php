<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	require_once('../pdf/korean.php');

	$conn->set_name('euckr');

	# 출력일자
	$printYear  = date('Y', mkTime());
	$printMonth = date('m', mkTime());
	$printDay   = date('d', mkTime());

	$mCode		= $_GET['mCode'];
	$mKind		= $_GET['mKind'];
	$mDate		= $_GET['mDate'];
	$mKey		= $_GET['mKey'];
	$mBoninYul	= $_GET['mBoninYul'];
	$misu_yn	= $_GET['misy_yn'];

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
	$address1   = $row['m00_caddr1']; //주소1
	$address2   = $row['m00_caddr2']; //주소2
	$conn->row_free();

	# 수급자 주민번호 조회
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);

	# 폰트설정
		$fontNameK = '굴림';  //돋음
		$fontNameE = 'Gulim'; //Dotum
		$paperDir     = 'L';


		$pdf=new PDF_Korean($paperDir);
		$pdf->AddUHCFont($fontNameK, $fontNameE);
		$pdf->Open();

		$pdf->SetCreator('GoodEos', true);

	# 수급자 정보및 급여내역 조회
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

		# 문서설정
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

		# 명세서 발급일 저장
		setPaymentIssu($conn, $mCode, $mKind, $sugupjaJumin, $mDate, $mBoninYul, $billNo, date('Ymd',mkTime()));

		$pdf->AddPage();
		$pdf->SetFont($fontNameK, '', 12);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetDrawColor(0, 0, 0);

		# 중앙 절취선
		for($i=$marginTop; $i<=($paperHeight * 0.45); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3, '절');
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3 + 7, '취');
		$pdf->Text($paperWidth / 2 - 2, $paperHeight / 2 - 3 + 14, '선');
		$pdf->SetFont($fontNameK, '', 8);
		$pdf->Text($paperWidth / 2 - 2.5, $paperHeight / 2 - 3 + 21, '(인)');
		for($i=($marginTop+($paperHeight * 0.55)); $i<=($bodyHeight + $bodyT); $i++){
			$pdf->Line($paperWidth / 2, $i, $paperWidth / 2, $i + 0.2);
		}

		# 왼쪽 명세서 작성
		# 별지
		for($i=0; $i<2; $i++){
			if ($i > 0){
				$pdf->SetDrawColor(0, 0, 255);
				$pdf->SetTextColor(0, 0, 255);
				$marginLeft = $paperWidth / 2 + $marginLeft / 2;
			}
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop);
			$pdf->Cell($bodyW, 5, '[별지 제24호서식]<개정 2008.6.11>');

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Rect($marginLeft, $bodyT, $bodyW, $bodyHeight);
			$pdf->SetLineWidth(0.2);

			# 작성
			# 타이틀
			$pdf->SetFont($fontNameK, '', 11);
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW * 0.75, 10, '', 1, 0, 'C'); #장기요양급여비용 명세서 공간
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW * 0.75, 6, '장기요양급여비용 명세서', 0, 0, 'C');
			$pdf->SetFont($fontNameK, '', 9);
			$pdf->Cell($bodyW * 0.25, 5, '□퇴소', 1, 2, 'C');
			$pdf->Cell($bodyW * 0.25, 5, '□중간', 1, 1, 'C');
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop + 11);
			$pdf->Cell($bodyW * 0.75, 4, '공급'.($i==0?'':'받는').'자/'.($i==0?'시설':'수급자').' 용', 0, 0, 'R');

			# 기관정보
			$pdf->SetXY($marginLeft, $marginTop + 15);
			$pdf->SetFont($fontNameK, '', 9);
			//$pdf->SetTextColor(0, 0, 0); #글자색을 검정색으로 변경
			$pdf->Cell($bodyW * 0.15, 9, '', 1, 0, 'C'); #기관기호
			$pdf->Cell($bodyW * 0.25, 9, $centerNo, 1, 0, 'L'); #기관기호
			$pdf->Cell($bodyW * 0.15, 9, '', 1, 0, 'C'); #기관명
			$pdf->Cell($bodyW * 0.45, 9, $centerName, 1, 1, 'L'); #기관명

			#오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 좌표 백업
			$tempY = $pdf->GetY();
			$pdf->SetXY($marginLeft, $marginTop + 16);
			$pdf->Cell($bodyW * 0.15, 3.5, '장기요양', 0, 2, 'C');
			$pdf->Cell($bodyW * 0.15, 3.5, '기관기호', 0, 2, 'C');

			$pdf->SetXY($marginLeft + $bodyW * 0.4, $marginTop + 16);
			$pdf->Cell($bodyW * 0.15, 3.5, '장기요양', 0, 2, 'C');
			$pdf->Cell($bodyW * 0.15, 3.5, '기관명', 0, 2, 'C');

			# 좌표 복귀
			$pdf->SetXY($marginLeft, $tempY);
			$tempY2 = $tempY;

			# 기관 주소 및 사업자등록번호
			$pdf->Cell($bodyW * 0.15, 9, '주소', 1, 0, 'C');
			$pdf->Cell($bodyW * 0.45, 9, '', 1, 0, 'C'); #주소
			$pdf->Cell($bodyW * 0.17, 9, '', 1, 0, 'C'); #사업자등록번호
			//$pdf->SetTextColor(0, 0, 0); #글자색을 검정색으로 변경
			$pdf->Cell($bodyW * 0.23, 9, getBizStyle($regNo), 1, 1, 'L'); #사업자등록번호

			# 좌표 백업
			$tempY = $pdf->GetY();

			# 기관 주소 및 사업자등록번호 입력
			# 좌표설정
			$pdf->SetXY($marginLeft + $bodyW * 0.15, $tempY2 + 1);
			$pdf->Cell($bodyW * 0.45, 3.5, $address1, 0, 2, 'L'); #주소1
			$pdf->Cell($bodyW * 0.45, 3.5, $address2, 0, 0, 'L'); #주소2

			#오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}else{
				$pdf->SetTextColor(0, 0, 0);
			}

			# 좌표설정
			$pdf->SetXY($marginLeft + $bodyW * 0.6, $tempY2 + 1);
			$pdf->Cell($bodyW * 0.17, 3.5, '사업자', 0, 2, 'C'); #사업자등록번호
			$pdf->Cell($bodyW * 0.17, 3.5, '등록번호', 0, 0, 'C'); #사업자등록번호


			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 좌표 복귀
			$pdf->SetXY($marginLeft, $tempY);

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $tempY, $marginLeft + $bodyW, $tempY);
			$pdf->SetLineWidth(0.2);

			# 수급자정보
			$pdf->Cell($bodyW * 0.2,  5, '성명', 1, 0, 'C');             #성명
			$pdf->Cell($bodyW * 0.23, 5, '장기요양인정번호', 1, 0, 'C'); #장기요양인정번호
			$pdf->Cell($bodyW * 0.34, 5, '급여제공기간', 1, 0, 'C');     #급여제공기간
			$pdf->Cell($bodyW * 0.23, 5, '영수증번호', 1, 1, 'C');       #영수증번호

			# 수급자정보 데이타 입력
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.2,  6, $sugupjaName, 1, 0, 'C');                  #성명
			$pdf->Cell($bodyW * 0.23, 6, $sugupjaNo, 1, 0, 'L');                    #장기요양인정번호
			$pdf->Cell($bodyW * 0.34, 6, $sugupFmDate.'~'.$sugupToDate, 1, 0, 'C'); #급여제공기간
			$pdf->Cell($bodyW * 0.23, 6, $billNo, 1, 1, 'C');                       #영수증번호

			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# 급여항목
			$tempY = $pdf->GetY();
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.25, 7, '항목', 1, 0, 'C');
			$pdf->Cell($bodyW * 0.25, 7, '금액', 1, 0, 'C');
			$tempX = $pdf->GetX();
			$pdf->Cell($bodyW * 0.5, 7, '금액산정내역', 1, 1, 'C');
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.05, 21, '', 1, 0, 'C'); #급여
			$pdf->Cell($bodyW * 0.20, 7, '본인부담금①', 1, 0, 'C'); #본인부담금
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 0, 'R'); #본인부담금
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'C'); #총액
			$pdf->Cell($bodyW * 0.25, 7, number_format($totalPay), 1, 1, 'R'); #총액

			# 급여타이틀 입력
			$pdf->Text($marginLeft + 1.7, $tempY + 16.5, '급');
			$pdf->Text($marginLeft + 1.7, $tempY + 20.5, '여');

			# 총액타이틀 입력
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2, $tempY + 10, '총액(급여+비급여)');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9, $tempY + 13.2, '⑨(③+⑧)');

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '공단부담금②', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($gongdanPay), 1, 0, 'R'); #
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 1, 'R'); #본인부담금

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '급여계③(①+②)', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, number_format($totalPay), 1, 0, 'R'); #
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.25, 7, '이미 납부한 금액⑪', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); # number_format($boninPay)
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 본인부담총액 타이틀 입력
			$pdf->Text($marginLeft + $bodyW * 0.5 + 7, $tempY + 17, '본인부담총액');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9, $tempY + 20.1, '⑩(①+⑧)');

			# X좌표 설정
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.05, 63, '', 1, 0, 'C'); #비급여
			$pdf->Cell($bodyW * 0.20, 7, '식자재료비④', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '미수금', 1, 0, 'C'); #
			if($misu_yn == 'Y'){
				$pdf->Cell($bodyW * 0.25, 7, number_format($misuPay), 1, 1, 'R'); #미수금
			}else {
				$pdf->Cell($bodyW * 0.25, 7,'', 1, 1, 'R'); #미수금
			}
			# 비급여타이틀 입력
			$pdf->Text($marginLeft + 1.7, $tempY + 50, '비');
			$pdf->Text($marginLeft + 1.7, $tempY + 54, '급');
			$pdf->Text($marginLeft + 1.7, $tempY + 58, '여');

			# X좌표 설정
			$tempY2 = $pdf->GetY();
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7,  '', 1, 0, 'C'); #상급침대 이용에....
			$pdf->Cell($bodyW * 0.25, 7,  '', 1, 0, 'R'); #금액
			$pdf->Cell($bodyW * 0.09, 28, '', 1, 0, 'C'); #수납금액
			$pdf->Cell($bodyW * 0.16, 7,  '카드', 1, 0, 'C'); #카드
			$pdf->Cell($bodyW * 0.25, 7,  '', 1, 1, 'R'); #금액

			# 타이틀입력
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 3, '상급침실 이용에');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 6, '따른추가비용⑤');

			#타이틀입력
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2.5, $tempY2 + 8, '수납');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 2.5, $tempY2 + 12, '금액');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 4,   $tempY2 + 16, '⑫');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 0.5, $tempY2 + 20, '(');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 1.4, $tempY2 + 20, '⑩-');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 6.5, $tempY2 + 20, '⑪');
			$pdf->Text($marginLeft + $bodyW * 0.5 + 9.5, $tempY2 + 20, ')');

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.20, 7, '이.미용비⑥', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '현금영수증', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 35, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '현금', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, $misu_yn != 'Y' ? number_format($boninPay) : '', 1, 1, 'R'); #

			# 타이틀입력
			$pdf->Text($marginLeft + $bodyW * 0.05 + 0.5, $tempY2 + 32, '기타');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 2,   $tempY2 + 36, '⑦');

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '합계', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, $misu_yn != 'Y' ? number_format($boninPay) : '', 1, 1, 'R'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.5,  7, '현금영수증', 1, 1, 'C'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '신분확인번호', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.25, 7, '현금승인번호', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.5,  7, '* 비고', 1, 1, 'L'); #

			# 타이틀입력
			$pdf->Text($marginLeft + $bodyW * 0.05 + 4, $tempY2 + 52, '비급여 계⑧');
			$pdf->Text($marginLeft + $bodyW * 0.05 + 1, $tempY2 + 55, '(④+⑤+⑥+⑦)');

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Line($tempX, $tempY, $tempX, $pdf->GetY());
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# 신용카드 정보
			# X좌표 설정
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.14, 14, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.08, 7, '할부', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.11, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '사용금액', 1, 1, 'C'); #

			# 타이틀 입력
			$pdf->Text($marginLeft + 1, $pdf->GetY() - 1.5,   '신용카드를');
			$pdf->Text($marginLeft + 1, $pdf->GetY() + 3, '사용하실때');

			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() - 4,   '회원');
			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() - 0.5, '번호');

			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() - 4,   '승인');
			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() - 0.5, '번호');

			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() + 3,   '카드');
			$pdf->Text($marginLeft + $bodyW * 0.14 + 0.5, $pdf->GetY() + 6.3, '종류');

			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() + 3,   '유효');
			$pdf->Text($marginLeft + $bodyW * 0.4 + 0.5, $pdf->GetY() + 6.3, '기간');

			$pdf->Text($marginLeft + $bodyW * 0.67 - 0.5, $pdf->GetY() + 3,   '가맹점');
			$pdf->Text($marginLeft + $bodyW * 0.67 + 0.5, $pdf->GetY() + 6.3, '번호');

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.14);
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.06, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.2,  7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.08, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.11, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '', 1, 1, 'C'); #

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# X좌표 설정
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
			$pdf->Cell($bodyW * 0.5, 5, '대표자명 : '.$masterName, 0, 1, 'C'); #

			# X좌표 설정
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 3,    '* 이 명세서(영수증)는 「소득세법」에 따른 의료비 또는 「조세특례제한법」에 따른 현');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 6.3,  '금영수증(현금영수증 승인번호가 기재된 경우) 공제신청에 사용할 수 있습니다. 다만,');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 9.6,   '지출증빙용으로 발급된 현금영수증(지출증빙)은 공제신청에 사용할 수 없습니다.');
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 12.9, '* 이 명세서(영수증)에 대한 세부내역을 요구할 수 있습니다.');
			$pdf->Text($marginLeft + 1,   $pdf->GetY() + 16.2,   '* 비고란은 장기요양기관의 임의활용란으로 사용합니다.');
			$pdf->Text($marginLeft + 3.3, $pdf->GetY() + 19.5, '다만, 복지용구의 경우 품목과 구입.대여를 구분하여 기재하시기 바랍니다.');
		}
	}
	$conn->row_free();


	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>
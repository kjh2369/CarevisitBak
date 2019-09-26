<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_myFun.php');
	require('../pdf/korean.php');

	$conn->set_name('euckr');
	$conn2 = new connection();
	$conn2->set_name('euckr');
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
	$sql = "select m00_code1, m00_cname, m00_mname, m00_cpostno, m00_caddr1, m00_caddr2, m00_ccode, m00_ctel, m00_bank_name, m00_bank_no, m00_bank_depos"
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
	$centerTel = $myF->phoneStyle($row['m00_ctel']);
	$bankName = iconv("UTF-8","EUCKR",$definition->GetBankName($row['m00_bank_name']));
	$bankNo = $row['m00_bank_no'];
	$bankDepos = $row['m00_bank_depos'];
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

		# 문서설정
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

		# 납부내역 조회
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
			$pdf->Rect($marginLeft, $bodyT, $bodyW, $bodyHeight - $marginBottom - 1);
			$pdf->SetLineWidth(0.2);

			# 작성
			# 타이틀
			$pdf->SetFont($fontNameK, '', 11);
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW, 10, '', 1, 0, 'C'); #장기요양급여비용 명세서 공간
			$pdf->SetXY($marginLeft, $marginTop + 5);
			$pdf->Cell($bodyW, 6, '장기요양급여비용 (본인부담금)영수증', 0, 1, 'C');
			$pdf->SetFont($fontNameK, '', 9);
			$pdf->SetFont($fontNameK, '', 7);
			$pdf->SetXY($marginLeft, $marginTop + 11);
			$pdf->Cell($bodyW * 0.25, 4, 'No.'.$billNo, 1, 0, 'L');
			$pdf->Cell($bodyW * 0.75, 4, '공급'.($i==0?'':'받는').'자/'.($i==0?'시설':'수급자').' 용', 0, 1, 'R');

			# 기관정보
			$pdf->SetXY($marginLeft, $marginTop + 15);
			$pdf->SetFont($fontNameK, '', 9);
			//$pdf->SetTextColor(0, 0, 0); #글자색을 검정색으로 변경
			$pdf->Cell($bodyW * 0.07,  28, '', 1, 0, 'C'); #공급자
			$pdf->Cell($bodyW * 0.15,  7,  '', 1, 0, 'C'); #사업자등록번호 타이틀
			$pdf->Cell($bodyW * 0.315, 7,  getBizStyle($regNo), 1, 0, 'L'); #사업자등록번호 데이타
			$pdf->Cell($bodyW * 0.15,  7,  '', 1, 0, 'C'); #장기요양기관기호 타이틀
			$pdf->Cell($bodyW * 0.315, 7,  $centerNo, 1, 1, 'L'); #장기요양기관기호 데이타

			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			# 공급자 타이틀
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 25, '공');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 30, '급');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6, $marginTop + 35, '자');
			# 사업자등록번호 타이틀
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 18, '사   업   자');
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 21, '등 록 번 호');
			# 장기요양기관기호 타이틀
			$pdf->Text($bodyW * 0.535 + $marginLeft + 1.5, $marginTop + 18, '장 기 요 양');
			$pdf->Text($bodyW * 0.535 + $marginLeft + 1.5, $marginTop + 21, '기 관 기 호');

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15,  7, '상          호', 1, 0, 'C'); #상호 타이틀
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.465, 7, $centerName, 1, 0, 'L');      #상호 데이타
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15,  7, '대표자명', 1, 0, 'C');       #대표자명 타이틀
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.165, 7, $masterName, 1, 1, 'L');      #대표자명 데이타
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15, 7, '', 1, 0, 'C'); #주소 타이틀
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.78, 7, $address1.' '.$address2, 1, 1, 'L'); #주소 데이타
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 주소 타이틀
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 32, '사   업   장');
			$pdf->Text($bodyW * 0.07 + $marginLeft + 1.5, $marginTop + 35, '소   재   지');

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			$pdf->Cell($bodyW * 0.15,  7, '업          태', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, '서비스', 1, 0, 'L');         #
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15,  7, '종          목', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, '방문요양', 1, 1, 'L');       #
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 테두리
			$pdf->SetLineWidth(0.4);
			$pdf->Line($marginLeft, $pdf->GetY(), $marginLeft + $bodyW, $pdf->GetY());
			$pdf->SetLineWidth(0.2);

			# 공급받는자(수급자)
			$pdf->SetX($marginLeft);
			$pdf->Cell($bodyW * 0.07, 14, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.15, 7, '수   급   자', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.235, 7, $sugupjaName, 1, 0, 'L'); #
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.23, 7, '장기요양인정번호', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, $sugupjaNo, 1, 1, 'L'); #

			$pdf->SetX($marginLeft + $bodyW * 0.07);
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.15, 7, '급여제공월', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.235, 7, subStr($mDate, 0, 4).'.'.subStr($mDate, 4, 2), 1, 0, 'L'); #
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			$pdf->Cell($bodyW * 0.23, 7, '급 여 제 공 기 간', 1, 0, 'C'); #
			//$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell($bodyW * 0.315, 7, $sugupFmDate.'~'.$sugupToDate, 1, 1, 'L'); #
			# 오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}
			# 공급받는자 타이틀 입력
			$pdf->Text($bodyW * 0.07 + $marginLeft - 7.5, $marginTop + 47, '공급');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 7.5, $marginTop + 51, '받는');
			$pdf->Text($bodyW * 0.07 + $marginLeft - 6,   $marginTop + 55, '자');

			#오른쪽 작성시 글자색을 파란색으로 변경
			if ($i > 0){
				$pdf->SetTextColor(0, 0, 255);
			}

			# 좌표 백업
			$tempY = $pdf->GetY();

			# 좌표 복귀
			$pdf->SetXY($marginLeft, $tempY);

			# 테두리
			$pdf->SetLineWidth(0.6);
			$pdf->Line($marginLeft, $tempY, $marginLeft + $bodyW, $tempY);
			$pdf->SetLineWidth(0.2);

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($boninPay), 1, 1, 'R'); #

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
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 1, 'R'); #number_format($boninPay)
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
			$pdf->Cell($bodyW * 0.25, 7, number_format($misuPay), 1, 1, 'R'); #미수금

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['card']), 1, 1, 'R'); #금액

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
			$pdf->Cell($bodyW * 0.16, 7, '현금', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['cash']), 1, 1, 'R'); #

			# X좌표 설정
			$pdf->SetX($marginLeft + $bodyW * 0.05);
			$pdf->Cell($bodyW * 0.06, 35, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.14, 7, '', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, '', 1, 0, 'R'); #
			$pdf->Cell($bodyW * 0.09, 7, '', 0, 0, 'C'); #
			$pdf->Cell($bodyW * 0.16, 7, '계좌이체', 1, 0, 'C'); #
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['vari']), 1, 1, 'R'); #

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
			$pdf->Cell($bodyW * 0.25, 7, number_format($inAmt['cash']+$inAmt['card']), 1, 1, 'R'); #

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

			// 제공서비스 계
			$count = 2;
			$coordY = $pdf->GetY()+2;
			$width = $bodyW*0.55;
			$pdf->SetXY($marginLeft, $coordY);
			$pdf->Cell($width, 4, '◈제공서비스 계', 0, 1, 'L');

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

			$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('구분')) / 2, $coordY + $height - 1, '구분');
			$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth('수가')) / 2, $coordY + $height - 1, '수가');
			$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth('본인부담')) / 2, $coordY + $height - 1, '본인부담');
			$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth('초과/비급여')) / 2, $coordY + $height - 1, '초과/비급여');
			$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth('본인부담 계')) / 2, $coordY + $height - 1, '본인부담 계');

			if ($t13_suga_tot1 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('요양')) / 2, $coordY + $height - 1, '요양');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot1))) - 1, $coordY + $height - 1, number_format($t13_suga_tot1));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt1))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt1));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt1))) - 1, $coordY + $height - 1, number_format($t13_over_amt1));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot1))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot1));
			}

			if ($t13_suga_tot2 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('목욕')) / 2, $coordY + $height - 1, '목욕');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot2))) - 1, $coordY + $height - 1, number_format($t13_suga_tot2));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt2))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt2));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt2))) - 1, $coordY + $height - 1, number_format($t13_over_amt2));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot2))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot2));
			}

			if ($t13_suga_tot3 > 0){
				$coordY += $height;
				$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

				$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('간호')) / 2, $coordY + $height - 1, '간호');
				$pdf->Text(($marginLeft + $width * 0.12) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_suga_tot3))) - 1, $coordY + $height - 1, number_format($t13_suga_tot3));
				$pdf->Text(($marginLeft + $width * 0.34) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonin_amt3))) - 1, $coordY + $height - 1, number_format($t13_bonin_amt3));
				$pdf->Text(($marginLeft + $width * 0.56) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_over_amt3))) - 1, $coordY + $height - 1, number_format($t13_over_amt3));
				$pdf->Text(($marginLeft + $width * 0.78) + ($width * 0.22 - $pdf->GetStringWidth(number_format($t13_bonbu_tot3))) - 1, $coordY + $height - 1, number_format($t13_bonbu_tot3));
			}

			$coordY += $height;
			$pdf->Line($marginLeft, $coordY, $marginLeft+$width, $coordY);

			$pdf->Text($marginLeft + ($width * 0.12 - $pdf->GetStringWidth('계'))/ 2, $coordY + $height - 1, '계');
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

			$pdf->Text($coordX, $coordY + $height - 1, '◈본인부담금 납입계좌');

			$pdf->SetFont($fontNameK, '', 7);

			$coordY += $height;
			$coordY += $height;
			$pdf->Text($coordX + 3, $coordY - 1, $bankName.'/'.$bankNo.'/'.$bankDepos);

			$pdf->SetFont($fontNameK, '', 9);

			$coordY += $height;
			$coordY += $height;
			$pdf->Text($coordX, $coordY - 1, '◈전화');

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
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-기본
	 *********************************************************/
	$conn->fetch_type = 'assoc';
	$pdf->font_size = 9;
	$pdf->SetFont($pdf->font_name_kor,'',$pdf->font_size);

	$orgNo = $_SESSION['userCenterCode'];

	//대상자와의 관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HR\'
			AND		use_yn	= \'Y\'';

	$reqRel = $conn->_fetch_array($sql,'code');

	//대상자명
	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);

	if (!$jumin) $jumin = $row['jumin'];
	$tmpJumin = $jumin;
	$jumin = SubStr($jumin.'0000000',0,13);

	$gender	= $tmpJumin != '' ? $myF->euckr($myF->issToGender($jumin)) : '';
	$age	= $tmpJumin != '' ? Number_Format($myF->issToAge($jumin)) : '';
	$juminNo= $tmpJumin != '' ? $myF->issStyle($jumin) : '';

	Unset($row);

	//사례접수기록
	$sql = 'SELECT	EL.name AS edu_gbn
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.grd_rel
			,		rcpt.grd_tel
			,		rcpt.marry_gbn
			,		rcpt.cohabit_gbn
			,		iv.income_gbn
			,		iv.income_other
			,		iv.income_other
			,		iv.dwelling_gbn
			,		iv.dwelling_other
			,		iv.deposit_amt
			,		iv.rental_amt
			,		iv.house_gbn
			,		iv.house_other
			,		iv.health_gbn
			,		iv.health_other
			,		iv.disease_gbn
			,		iv.handicap_gbn
			,		iv.handicap_other
			,		iv.longlvl_gbn
			,		iv.longlvl_other
			,		iv.device_gbn
			,		iv.device_other
			FROM	hce_receipt AS rcpt
			LEFT	JOIN	hce_gbn AS EL
					ON		EL.type	= \'EL\'
					AND		EL.code	= rcpt.edu_gbn
			INNER	JOIN	hce_interview AS iv
					ON		iv.org_no	= rcpt.org_no
					AND		iv.org_type = rcpt.org_type
					AND		iv.IPIN		= rcpt.IPIN
					AND		iv.rcpt_seq = rcpt.rcpt_seq
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$eduGbn	= $row['edu_gbn'];
	$addr	= $row['addr'].' '.$row['addr_dtl'];
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$mobile	= $myF->phoneStyle($row['mobile'],'.');
	$grdRel	= $reqRel[$row['grd_rel']]['name'] != '' ? $reqRel[$row['grd_rel']]['name'] : '            ';
	$grdTel	= $myF->phoneStyle($row['grd_tel'],'.');
	$marry	= $row['marry_gbn'];
	$cohabit= $row['cohabit_gbn'];

	//보호형태
	$incomeGbn	= $row['income_gbn'];
	$incomeOther= $row['income_other'];

	//주택소유상태
	$dwellingGbn	= $row['dwelling_gbn'];		//주택소유상태
	$dwellingOther	= $row['dwelling_other'];	//기타내용
	$depositAmt		= $row['deposit_amt'];		//보증금
	$rentalAmt		= $row['rental_amt'];		//월세

	//주택형태
	$houseGbn	= $row['house_gbn'];
	$houseOther	= $row['house_other'];

	//건강상태
	$healthGbn	= $row['health_gbn'];
	$healthOther= $row['health_other'];

	//만성질환
	$diseaseGbn	= $row['disease_gbn'];

	//장애여부
	$handicapGbn	= $row['handicap_gbn'];
	$handicapOther	= $row['handicap_other'];

	//장애요양등급
	$longLvlGbn	= $row['longlvl_gbn'];
	$longLvlOther	= $row['longlvl_other'];

	//보장구
	$diviceGbn	= $row['device_gbn'];
	$diviceOther	= $row['device_other'];

	Unset($row);

	//사정기록지_기본
	$sql = 'SELECT	*
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$ispt = $conn->get_array($sql);

	
	//if($debug){
	$pdf->font_size = 18;
	$pdf->SetFont($pdf->font_name_kor,'B',$pdf->font_size);
	$subject = '사 정 기 록 지';
	$disH = 0;
	$pdf->SetXY($pdf->left+$pdf->width*0.03, $pdf->top);
	$pdf->_SignHcelineSet($subject,$disH);
	/*
	}else{
		//타이틀
		$pdf->SetXY($pdf->left, $pdf->top);
		$pdf->font_size = 18;
		$pdf->SetFont($pdf->font_name_kor,'B',$pdf->font_size);
		$pdf->Cell($pdf->width,$pdf->row_height*2,"사 정 기 록 지",0,1,'C');
	}
	*/

	$rowRate = 0.8;
	$pdf->row_height *= 0.9;	
	$pdf->font_size = 9;
	$pdf->SetFont($pdf->font_name_kor,'',$pdf->font_size);

	//기본
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.08;
	$col[] = $pdf->width*0.10;
	$col[] = $pdf->width*0.18;

	//상담일시
	$date = ($ispt['ispt_dt'] ? $myF->euckr($myF->dateStyle($ispt['ispt_dt'],'KOR')) : "                    ").' ('.($ispt['ispt_from'] ? $myF->timeStyle($ispt['ispt_from']) : "               ").'~'.($ispt['ispt_to'] ?  $myF->timeStyle($ispt['ispt_to']) : "               ").')';

	$pdf->SetXY($pdf->left, $pdf->getY());
	$pdf->Cell($col[0],$pdf->row_height,"상담일시",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2]+$col[3]+$col[4]+$col[5],$pdf->row_height,$date,1,0);
	$pdf->Cell($col[6],$pdf->row_height,"담당자",1,0,'C',1);
	$pdf->Cell($col[7]+$col[8]+$col[9],$pdf->row_height,$ispt['iver_nm'],1,1);

	//상담방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$ispt['counsel_type']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height,"상담방법",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height,$str,1,0);
	$pdf->Cell($col[9],$pdf->row_height*6,"",1,0);

	//사진
	$pic = '../sugupja/picture/'.$orgNo.'_'.$hce->IPIN.'.jpg';

	if (is_file($pic)){
		$tmpImg = getImageSize($pic);
		$picW = $tmpImg[0] * 0.04 * 0.1;
		$picH = $tmpImg[1] * 0.04 * 0.1;

		$prtW = $col[9] - 2;
		$prtH = $pdf->row_height*6 - 2;

		if ($picW > $cpsW || $picH > $cpsH){
			$picR = 1;

			if ($picW > $picH){
				$picR = $picH / $picW;
				$picW = $prtW;
				$picH = $prtH * $picR;
			}else{
				$picR = $picW / $picH;
				$picH = $prtH;
				$picW = $prtW * $picR;
			}
		}

		$gabL = ($col[9] - $picW) / 2;
		$gabT = ($pdf->row_height*6 - $picH) / 2;

		if ($gabL < 0) $gabL = 0;
		if ($gabT < 0) $gabT = 0;

		$pdf->Image($pic,$pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$gabL,$pdf->GetY()+$gabT, $picW, $picH);
	}

	$h = $pdf->GetStringWidth("대 기");
	$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*11 - $h) / 2,'width'=>$col[0],'text'=>"대 상 자\n기본사항");

	$pdf->SetXY($pdf->left,$pdf->GetY()+$pdf->row_height);
	$pdf->Cell($col[0],$pdf->row_height*9,"",1,0,'C',1);
	$pdf->Cell($col[1],$pdf->row_height,"성명",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,$name,1,0);
	$pdf->Cell($col[3],$pdf->row_height,"성별",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,$gender,1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"연령",1,0,'C',1);
	$pdf->Cell($col[6]+$col[7]+$col[8],$pdf->row_height,$age,1,1,'L');
	//$pdf->Cell($col[7],$pdf->row_height,"학력",1,0,'C',1);
	//$pdf->Cell($col[8],$pdf->row_height,$pdf->_splitTextWidth($myF->utf($eduGbn),$col[8]),1,1);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"주민등록번호",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4],$pdf->row_height,$juminNo,1,0);
	$pdf->Cell($col[5],$pdf->row_height,"학력",1,0,'C',1);
	$pdf->Cell($col[6]+$col[7]+$col[8],$pdf->row_height,$pdf->_splitTextWidth($myF->utf($eduGbn),$col[6]+$col[7]+$col[8]),1,1);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"주      소",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height,$pdf->_splitTextWidth($myF->utf($addr),$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]),1,1);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*2,"연  락  처",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5],$pdf->row_height,"자택 : ".$phone,"LTB");
	$pdf->Cell($col[6]+$col[7]+$col[8],$pdf->row_height,"핸드폰 : ".$mobile,"RTB",1);

	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5],$pdf->row_height,"비상연락처 : ".$grdTel,"LTB");
	$pdf->Cell($col[6]+$col[7]+$col[8],$pdf->row_height,"(관계 : ".$grdRel.")","RTB",1);

	//결혼구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'MR\'';

	$marryStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$marry);

	//동거구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CB\'';

	$cohabitStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$cohabit);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"결혼여부/동거여부",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$marryStr.' / '.$cohabitStr,1,1);

	$w = $col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9];
	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*3,"가족사항",1,0,'C',1);
	$pdf->Cell($w*0.2,$pdf->row_height,"성명",1,0,'C',1);
	$pdf->Cell($w*0.1,$pdf->row_height,"관계",1,0,'C',1);
	$pdf->Cell($w*0.27,$pdf->row_height,"주소",1,0,'C',1);
	$pdf->Cell($w*0.23,$pdf->row_height,"직업",1,0,'C',1);
	$pdf->Cell($w*0.1,$pdf->row_height,"동거",1,0,'C',1);
	$pdf->Cell($w*0.1,$pdf->row_height,"비고",1,1,'C',1);

	//가족관계
	$sql = 'SELECT	HR.name AS rel
			,		family_nm AS nm
			,		family_addr AS addr
			,		family_age AS age
			,		family_job AS job
			,		family_cohabit AS cohabit
			,		family_monthly AS monthly
			,		family_remark AS remark
			FROM	hce_family
			INNER	JOIN  hce_gbn AS HR
					ON  HR.type = \'HR\'
					AND HR.code = family_rel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY seq, family_rel, family_age DESC
			LIMIT	2';

	$conn->query($sql);
	$conn->fetch();

	for($i=0; $i<2; $i++){
		@$row = $conn->select_row($i);

		$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
		$pdf->Cell($w*0.2,$pdf->row_height,$row['nm'],1,0);
		$pdf->Cell($w*0.1,$pdf->row_height,$row['rel'],1,0);
		$pdf->Cell($w*0.27,$pdf->row_height,$myF->splits($row['addr'],15),1,0);
		$pdf->Cell($w*0.23,$pdf->row_height,$row['job'],1,0);
		$pdf->Cell($w*0.1,$pdf->row_height,$row['cohabit'],1,0, 'C');
		$pdf->Cell($w*0.1,$pdf->row_height,$row['remark'],1,1);
	}

	$conn->row_free();

	//보호형태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'IG\'
			AND		use_yn	= \'Y\'';
	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$incomeGbn,'9',$incomeOther);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height*(7*$rowRate+1),"경제상항",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"보호형태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);

	//월총수입
	$monthly = 0;

	//근로소득
	if ($ispt['work_amt'] > 0){
		$workAmt = "■근로소득   (월   ".number_format($ispt['work_amt'])." 원)";
	}else{
		$workAmt = "□근로소득   (월                   원)";
	}

	$monthly += $ispt['work_amt'];

	if ($ispt['live_aid_amt'] + $ispt['basic_old_amt'] + $ispt['ext_aid_amt'] > 0){
		$str = "■정부지원금";
	}else{
		$str = "□정부지원금";
	}

	//생계.주거비
	if ($ispt['live_aid_amt'] > 0){
		$liveAidAmt = "   -생계.주거비      (월   ".number_format($ispt['live_aid_amt'])." 원)";
	}else{
		$liveAidAmt = "   -생계.주거비      (월                   원)";
	}

	$monthly += $ispt['live_aid_amt'];

	//기초노령연금
	if ($ispt['basic_old_amt'] > 0){
		$basicOldAmt = "   -기초노령연금    (월   ".number_format($ispt['basic_old_amt'])." 원)";
	}else{
		$basicOldAmt = "   -기초노령연금    (월                   원)";
	}

	$monthly += $ispt['basic_old_amt'];

	//기타
	if ($ispt['ext_aid_amt'] > 0){
		$extAidAmt = "   -기타                (월   ".number_format($ispt['ext_aid_amt'])." 원)";
	}else{
		$extAidAmt = "   -기타                (월                   원)";
	}

	$monthly += $ispt['ext_aid_amt'];

	//후원금
	if ($ispt['support_amt'] > 0){
		$supportAmt = "■후원금   (월   ".number_format($ispt['support_amt'])." 원)";
	}else{
		$supportAmt = "□후원금   (월                   원)";
	}

	$monthly += $ispt['support_amt'];

	//부양자지원
	if ($ispt['support_aid_amt'] > 0){
		$supportAidAmt = "■부양자지원   (월   ".number_format($ispt['support_aid_amt'])." 원)";
	}else{
		$supportAidAmt = "□부양자지원   (월                   원)";
	}

	$monthly += $ispt['support_aid_amt'];

	if ($monthly > 0){
		$monthly = "   ".number_format($monthly)." 원";
	}else{
		$monthly = "                 원";
	}

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*7*$rowRate,"소득상황",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$workAmt,"LTR"); //
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"","LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$str,"LR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$liveAidAmt,"LR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"   월 총수입","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$basicOldAmt,"LR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$extAidAmt,"LR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,$monthly,"LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$supportAmt,"LR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8],$pdf->row_height*$rowRate,$supportAidAmt,"BLR");
	$pdf->Cell($col[9],$pdf->row_height*$rowRate,"","BLR",1);

	//주택소유상태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'DL\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['code'] == $dwellingGbn){
			$gbn = "■";
		}else{
			$gbn = "□";
		}

		if ($row['code'] == '1' || $row['code'] == '2'){
			$str1 .= $gbn.$row['name'];

			if ($row['code'] == '2'){
				if ($row['code'] == $dwellingGbn){
					$str1 .= "(보증금  ".($depositAmt > 0 ? number_format($depositAmt) : "        ")." 만원)";
				}else{
					$str1 .= "(보증금           만원)";
				}
			}else{
				$str1 .= "   ";
			}
		}else if ($row['code'] == '3'){
			if ($row['code'] == $dwellingGbn){
				$str2 .= $gbn.$row['name']."(보증금  ".($depositAmt > 0 ? number_format($depositAmt) : "        ")." 만원 / 월세  ".($rentalAmt > 0 ? number_format($rentalAmt) : "        ")." 만원)   ";
			}else{
				$str2 .= $gbn.$row['name']."(보증금           만원 / 월세           만원)   ";
			}
		}else{
			$str3 .= $gbn.$row['name'];

			if ($row['code'] == '9'){
				if ($row['code'] == $dwellingGbn){
					$str3 .= $pdf->_splitTextWidth($myF->utf($dwellingOther),20);
				}else{
					$str3 .= "(                )";
				}
			}else{
				$str3 .= "   ";
			}
		}
	}

	$conn->row_free();
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height*(9*$rowRate+2),"주거사항",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*3*$rowRate,"주택소유상태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str1,"LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str2,"LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str3,"LBR",1);

	//주택형태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HT\'
			AND		use_yn	= \'Y\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$houseGbn,'9',$houseOther);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"주택형태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);

	//주거환경
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'DLE\'
			AND		use_yn	= \'Y\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$ispt['dwelling_env'],'9',$ispt['dwelling_env_other']);
	$str = str_replace("2층이상","2층이상(승강기 ".($ispt['elv_yn'] == 'Y' ? "■유 □무" : "□유 ■무").")",$str);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*3*$rowRate,"주거형태 및 환경",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str,"LTR",1);

	//주택상태
	$str = "⊙주택상태   ";

	if ($ispt['house_stat'] == '1'){
		$str .= "■양호";
	}else{
		$str .= "□양호";
	}

	if ($ispt['house_stat'] == '2'){
		$str .= "■불량(".$ispt['house_stat_fault'].")";
	}else{
		$str .= "□불량(                                 )";
	}
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str,"LR",1);

	//위생상태
	$str = "⊙위생상태   ";

	if ($ispt['clean_stat'] == '1'){
		$str .= "■양호";
	}else{
		$str .= "□양호";
	}

	if ($ispt['clean_stat'] == '2'){
		$str .= "■불량(".$ispt['clean_stat_fault'].")";
	}else{
		$str .= "□불량(                                 )";
	}
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,$str,"LBR",1);

	//난방형태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HTG\'
			AND		use_yn	= \'Y\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$ispt['heat_gbn'],'9',$ispt['heat_other']);
	$str = str_replace("아궁이   ","아궁이 (".($ispt['heat_material'] == '1' ? "■" : "")."연탄 / ".($ispt['heat_material'] == '2' ? "■" : "")."장작)\n",$str);

	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()+1,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$str);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*2*$rowRate,"난방형태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LBR",1);

	//화장실형태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'TLG\'
			AND		use_yn	= \'Y\'';

	$str1 = lfGetGbnStr($pdf,$myF,$conn,$sql,$ispt['toilet_gbn']);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'TLT\'
			AND		use_yn	= \'Y\'';

	$str2 = lfGetGbnStr($pdf,$myF,$conn,$sql,$ispt['toilet_type']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"화장실형태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str1.' / '.$str2,1,1);

	//전체적 건강상태
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HS\'
			AND		use_yn	= \'Y\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$healthGbn,'9',$healthOther);
	$str = str_replace("편이다   ","편이다\n",$str);
	$str = str_replace("못하다   ","못하다\n",$str);

	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()+1,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$str);

	//신체적문제
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'PP\'
			AND	use_yn	= \'Y\'';

	$S1 = lfGetGbnList($conn,$sql,$code,$ispt['physical_problem_gbn'],'9',$ispt['physical_problem_other'],$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8], true);
	$H1 = lfGetStringHeight($pdf,$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$S1);

	//정신적문제
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'MP\'
			AND	use_yn	= \'Y\'';

	$S2 = lfGetGbnList($conn,$sql,$code,$ispt['mental_problem_gbn'],'9',$ispt['mental_problem_other'],$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8], true);
	$H2 = lfGetStringHeight($pdf,$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$S2);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height*(7*$rowRate+8)+$H1+$H2-2.3,"건강상황",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*3*$rowRate,"",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LBR",1);

	$h = $pdf->GetStringWidth("전 건");
	$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$pdf->GetY()-$pdf->row_height*3*$rowRate+($pdf->row_height*3*$rowRate - $h) / 2,'width'=>$col[1]+$col[2],'text'=>"전체적\n건강상태");

	//만성질환
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'DT\'
			AND		use_yn	= \'Y\'';

	$str = lfGetGbnText($conn,$sql,$code,$diseaseGbn);

	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()+1,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$str);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*4*$rowRate,"만성질환",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LBR",1);

	//거동상태
	if ($ispt['moving_stat'] == '1') $str  = "■"; else $str  = "□"; $str .= "자립가능   ";
	if ($ispt['moving_stat'] == '2') $str .= "■"; else $str .= "□"; $str .= "도움필요   ";
	if ($ispt['moving_stat'] == '3') $str .= "■"; else $str .= "□"; $str .= "완전도움필요   ";

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"거동상태",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);

	//장애여부
	if ($handicapGbn == 'Y') $str1 = "■유(".$handicapOther.")"; else $str1 = "□유(                       )";
	if ($handicapGbn == 'N') $str2 = "■무"; else $str2 = "□무";

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"장애유무",1,0,'C',1);
	$pdf->Cell(($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9])*0.5,$pdf->row_height,$str1,"LTB",0);
	$pdf->Cell(($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9])*0.5,$pdf->row_height,$str2,"RTB",1);

	//장기요양등급
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'LLV\'
			AND	use_yn	= \'Y\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$longLvlGbn);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"장기요양등급",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);

	//보장구
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'DV\'
			AND	use_yn	= \'Y\'';

	$str = lfGetGbnList($conn,$sql,$code,$diviceGbn,'99',$diviceOther);
	$str = str_replace("돋보기   ","돋보기\n",$str);

	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()+1,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$str);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height*2*$rowRate,"보장구",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LTR",1);
	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height*$rowRate,"","LBR",1);

	//신체적문제
	/*
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'PP\'
			AND	use_yn	= \'Y\'';

	$str = lfGetGbnList($conn,$sql,$code,$ispt['physical_problem_gbn'],'9',$ispt['physical_problem_other'],20);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"신체적문제",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);
	*/
	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$H1,"신체적문제",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$H1,"",1,1);
	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()-$H1+0.5,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$S1);

	/*
	//정신적문제
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type= \'MP\'
			AND	use_yn	= \'Y\'';

	$str = lfGetGbnList($conn,$sql,$code,$ispt['mental_problem_gbn'],'9',$ispt['mental_problem_other'],20);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"정신적문제",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,$str,1,1);
	*/
	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$H2,"정신적문제",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$H2,"",1,1);
	$pos[] = Array('X'=>$pdf->left+$col[0]+$col[1]+$col[2],'Y'=>$pdf->GetY()-$H2+0.5,'width'=>$col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],'align'=>'L','text'=>$S2);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"과거병력",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,StripSlashes($ispt['past_medi_his']),1,1);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"현재병력",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,StripSlashes($ispt['curr_medi_his']),1,1);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"비고",1,0,'C',1);
	$pdf->Cell($col[3]+$col[4]+$col[5]+$col[6]+$col[7]+$col[8]+$col[9],$pdf->row_height,StripSlashes($ispt['remark']),1,1);
	

	if($ispt['hsp_nm_3'] != ''|| $ispt['hsp_nm_4'] != ''){
		
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 3.5, $row['text'], 0, ($row['align'] ? $row['align'] : 'C'));
		}

		Unset($pos);

		$pdf->MY_ADDPAGE();

		$nextPg = 1;
		$totrH = ($totrH-5);
	}else {
		$nextPg = 0;
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height,"의료기관명",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"질병",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"통원상황",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"빈도",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"복약(치료상황)",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2],$pdf->row_height,"연락처",1,1,'C',1);

	$pdf->SetX($pdf->left);
	
	/*
	$orgsize = $pdf->font_size;
	$fontsize = $pdf->TestSize($col[0]*0.9, $ispt['hsp_nm']);
	$pdf->SetFontSize($fontsize);
	$pdf->Cell($col[0],$pdf->row_height,$ispt['hsp_nm'],1,0);
	$pdf->SetFontSize($orgsize);
	*/
	
	/*
	$orgY = $pdf->GetY();

	$pdf->SetXY($pdf->left,$orgY);
	$pdf->MultiCell($col[0], 3.5, $ispt['hsp_nm'], 0, 'L');
	*/
	
	
	$orgY = $pdf->GetY();

	
	if(strlen($ispt['hsp_nm']) > 10){
		
		$pos[] = Array('X'=>$pdf->left,'Y'=>$orgY,'width'=>$col[0],'text'=>$ispt['hsp_nm']);
		$rH = $pdf->row_height+2;
		$totrH += $rH;
	}else {
		$hspNm = $ispt['hsp_nm'];
		
		$rH = $pdf->row_height;
		$totrH += $rH;
	}
	

	//$pdf->Cell($col[0],$pdf->row_height,$ispt['hsp_nm'] ? $pdf->_splitText(stripslashes($ispt['hsp_nm']), $col[0]) : '',1,0,'C');
	$pdf->SetXY($pdf->left,$orgY);
	$pdf->Cell($col[0],$rH,$hspNm,1,0);
	$pdf->Cell($col[1]+$col[2],$rH,$ispt['dis_nm'] ? $pdf->_splitText(stripslashes($ispt['dis_nm']), $col[1]+$col[2]) : '',1,0,'C');
	$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_go'] ? $pdf->_splitText(stripslashes($ispt['hsp_go']), $col[1]+$col[2]) : '',1,0,'C');
	$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_fre'] ? $pdf->_splitText(stripslashes($ispt['hsp_fre']), $col[1]+$col[2]) : '',1,0,'C');
	$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_med'] ? $pdf->_splitText(stripslashes($ispt['hsp_med']), $col[1]+$col[2]) : '',1,0,'C');
	$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_tel'] ? $pdf->_splitText(stripslashes($ispt['hsp_tel']), $col[1]+$col[2]) : '',1,1,'C');

	
	if($ispt['hsp_nm_2'] != ''){

		$orgY = $pdf->GetY();

		if(strlen($ispt['hsp_nm_2']) > 10){
			$pos[] = Array('X'=>$pdf->left,'Y'=>$orgY,'width'=>$col[0],'text'=>$ispt['hsp_nm_2']);
			$rH = $pdf->row_height+2;
			$totrH += $rH;
		}else {
			$hspNm2 = $ispt['hsp_nm_2'];

			$rH = $pdf->row_height;
			$totrH += $rH;
		}

		$pdf->SetXY($pdf->left,$orgY);
		$pdf->Cell($col[0],$rH,$hspNm2,1,0);
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['dis_nm_2'] ? $pdf->_splitText(stripslashes($ispt['dis_nm_2']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_go_2'] ? $pdf->_splitText(stripslashes($ispt['hsp_go_2']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_fre_2'] ? $pdf->_splitText(stripslashes($ispt['hsp_fre_2']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_med_2'] ? $pdf->_splitText(stripslashes($ispt['hsp_med_2']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_tel_2'] ? $pdf->_splitText(stripslashes($ispt['hsp_tel_2']), $col[1]+$col[2]) : '',1,1,'C');
	}
	
	if($ispt['hsp_nm_3'] != ''){
		$orgY = $pdf->GetY();

		if(strlen($ispt['hsp_nm_3']) > 10){
			$pos[] = Array('X'=>$pdf->left,'Y'=>$orgY,'width'=>$col[0],'text'=>$ispt['hsp_nm_3']);
			$rH = $pdf->row_height+2;
			$totrH += $rH;
		}else {
			$hspNm3 = $ispt['hsp_nm_3'];

			$rH = $pdf->row_height;
			$totrH += $rH;
		}

		$pdf->SetXY($pdf->left,$orgY);
		$pdf->Cell($col[0],$rH,$hspNm3,1,0);
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['dis_nm_3'] ? $pdf->_splitText(stripslashes($ispt['dis_nm_3']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_go_3'] ? $pdf->_splitText(stripslashes($ispt['hsp_go_3']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_fre_3'] ? $pdf->_splitText(stripslashes($ispt['hsp_fre_3']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_med_3'] ? $pdf->_splitText(stripslashes($ispt['hsp_med_3']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_tel_3'] ? $pdf->_splitText(stripslashes($ispt['hsp_tel_3']), $col[1]+$col[2]) : '',1,1,'C');
	}

	if($ispt['hsp_nm_4'] != ''){
		$orgY = $pdf->GetY();

		if(strlen($ispt['hsp_nm_4']) > 10){
			$pos[] = Array('X'=>$pdf->left,'Y'=>$orgY,'width'=>$col[0],'text'=>$ispt['hsp_nm_4']);
			$rH = $pdf->row_height+2;
			$totrH += $rH;
		}else {
			$hspNm4 = $ispt['hsp_nm_4'];

			$rH = $pdf->row_height;
			$totrH += $rH;
		}

		$pdf->SetXY($pdf->left,$orgY);
		$pdf->Cell($col[0],$rH,$hspNm4,1,0);
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['dis_nm_4'] ? $pdf->_splitText(stripslashes($ispt['dis_nm_4']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_go_4'] ? $pdf->_splitText(stripslashes($ispt['hsp_go_4']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_fre_4'] ? $pdf->_splitText(stripslashes($ispt['hsp_fre_4']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_med_4'] ? $pdf->_splitText(stripslashes($ispt['hsp_med_4']), $col[1]+$col[2]) : '',1,0,'C');
		$pdf->Cell($col[1]+$col[2],$rH,$ispt['hsp_tel_4'] ? $pdf->_splitText(stripslashes($ispt['hsp_tel_4']), $col[1]+$col[2]) : '',1,1,'C');
	}
	

	/*********************************************************
	 *********************************************************/

	if($pos != ''){
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 3.5, $row['text'], 0, ($row['align'] ? $row['align'] : 'C'));
		}
	}

	Unset($col);
	Unset($pos);

	//$pdf->row_height = 6;

?>
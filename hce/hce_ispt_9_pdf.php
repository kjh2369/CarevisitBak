<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-노인우울척도
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];


	//사례접수 및 초기면접 내용
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.marry_gbn
			,		rcpt.cohabit_gbn
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$jumin = $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);
	$tmpJumin = $jumin;
	$jumin = SubStr($jumin.'0000000',0,13);

	$name	= $row['name'];	//성명
	$age	= $tmpJumin != '' ? number_format($myF->issToAge($jumin)) : '';	//연령
	$jumin	= $tmpJumin != '' ? $myF->issStyle($jumin) : '';	//주민번호
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//주소
	$phone	= $myF->phoneStyle($row['phone'],'.');	//연락처
	$mobile	= $myF->phoneStyle($row['mobile'],'.');	//핸드폰

	$birthDay = $myF->euckr($myF->dateStyle($myF->issToBirthday($jumin),'KOR'));

	Unset($row);


	//노인인지능력평가
	$sql = 'SELECT	*
			FROM	hce_inspection_sgds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$sgds = $conn->get_array($sql);


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',18);
	$pdf->Cell($pdf->width,$pdf->row_height*2,"노인우울척도",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height,"(Short Form of Geriatric Depression Scale : SGDS)",0,1,'C');

	$col[] = $pdf->width * 0.08;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.09;
	$col[] = $pdf->width * 0.20;
	$col[] = $pdf->width * 0.06;
	$col[] = $pdf->width * 0.08;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.29;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0],$pdf->row_height * 2,"이  름",1,0,'C',1);
	$pdf->Cell($col[1],$pdf->row_height * 2,$name,1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height * 2,"생년월일",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height * 2,$birthDay,1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height * 2,"연  령",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height * 2,$age,1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"집 전 화","LTR",0,'C',1);
	$pdf->Cell($col[7],$pdf->row_height,$phone,"LTR",1,'C');

	$pdf->SetX($pdf->left+$col[0]+$col[1]+$col[2]+$col[3]+$col[4]+$col[5]);
	$pdf->Cell($col[6],$pdf->row_height,"(이동전화)","LBR",0,'C',1);
	$pdf->Cell($col[7],$pdf->row_height,$mobile,"LBR",1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"주  소",1,0,'C',1);
	$pdf->Cell($col[1]+$col[2]+$col[3]+$col[4]+$col[5]+$col[6]+$col[7],$pdf->row_height * 2,$addr,1,1,'L');

	Unset($col);


	$quest[]	= Array('id'=>'Q1','no'=>'1.','quest'=>'현재의 생활에 대체적으로 만족하십니까?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q2','no'=>'2.','quest'=>'요즈음 들어 활동량이나 의욕이 많이 떨어지셨습니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q3','no'=>'3.','quest'=>'자신이 헛되이 살고 있다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q4','no'=>'4.','quest'=>'생활이 지루하게 느껴질 때가 많습니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q5','no'=>'5.','quest'=>'평소에 기분은 상쾌한 편이십니까?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q6','no'=>'6.','quest'=>'자신에게 불길한 일이 닥칠 것 같아 불안하십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q7','no'=>'7.','quest'=>'대체로 마음이 즐거운 편이십니까?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q8','no'=>'8.','quest'=>'절망적이라는 느낌이 자주 드십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q9','no'=>'9.','quest'=>'바깥에 나가기가 싫고 집에만 있고 싶습니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q10','no'=>'10.','quest'=>'비슷한 나이의 다른 노인들보다 기억력이 더 나쁘다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q11','no'=>'11.','quest'=>'현재 살아 있다는 것이 즐겁게 생각되십니까?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q12','no'=>'12.','quest'=>'지금의 내 자신이 아무 쓸모 없는 사람이라고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q13','no'=>'13.','quest'=>'기력이 좋으신 편입니까?','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q14','no'=>'14.','quest'=>'지금 자신의 처지가 아무런 희망도 없다고 느끼십니까?','Y'=>'1','N'=>'0');
	$quest[]	= Array('id'=>'Q15','no'=>'15.','quest'=>'자신이 다른 사람들의 처지보다 더 못하다고 느끼십니까?','Y'=>'1','N'=>'0');


	$col[] = $pdf->width * 0.05;
	$col[] = $pdf->width * 0.75;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;

	$rowH = $pdf->row_height * 1.5;

	$pdf->SetXY($pdf->left,$pdf->GetY() + 10);
	$pdf->Cell($pdf->width,$rowH,"다음 문항을 잘 읽으시고 둘 중 한 가지 항목에 체크해 주세요.",0,1,'L');

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0] + $col[1],$rowH,"문          항",1,0,'C',1);
	$pdf->Cell($col[2],$rowH,"예",1,0,'C',1);
	$pdf->Cell($col[3],$rowH,"아니오",1,1,'C',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$pnt = 0;

	foreach($quest as $row){
		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH,$row['no'],"LTB",0,'R');
		$pdf->Cell($col[1],$rowH,$row['quest'],"TBR",0,'L');
		$pdf->Cell($col[2],$rowH,($sgds[$row['id']] == 'Y' ? $row['Y'] : ''),1,0,'C');
		$pdf->Cell($col[3],$rowH,($sgds[$row['id']] == 'N' ? $row['N'] : ''),1,1,'C');

		if ($sgds[$row['id']] == 'Y') $pnt += IntVal($row['Y']);
		if ($sgds[$row['id']] == 'N') $pnt += IntVal($row['N']);
	}

	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0] + $col[1],$rowH,"총        점",1,0,'C',1);
	$pdf->Cell($col[2] + $col[3],$rowH,$pnt." 점",1,1,'C',1);

	$pdf->SetXY($pdf->left,$pdf->GetY() + 5);
	$pdf->Cell($col[0] + $col[1],$rowH,"※ 검사결과 해석 : 총점이 10점 이상시 전문가 상담이 필요합니다.",0,1,'L');

	Unset($col);
	Unset($quest);
?>
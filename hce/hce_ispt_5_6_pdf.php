<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-정서적측면, 사회적측면
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];


	//정서적측면
	$sql = 'SELECT	*
			FROM	hce_inspection_feel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$feel1Yn	= $row['feel1_yn'];
	$feel2Yn	= $row['feel2_yn'];
	$feel2Rsn	= StripSlashes($row['feel2_rsn']);
	$feel3Yn	= $row['feel3_yn'];
	$feel4Yn	= $row['feel4_yn'];
	$feel4Rsn	= StripSlashes($row['feel4_rsn']);
	$feel5Yn	= $row['feel5_yn'];
	$feel6Yn	= $row['feel6_yn'];
	$feel6Eft	= StripSlashes($row['feel6_eft']);
	$feel7Yn	= $row['feel7_yn'];
	$feel7Cnt	= StripSlashes($row['feel7_cnt']);
	$feel7Whn	= StripSlashes($row['feel7_whn']);
	$feel7Rsn	= StripSlashes($row['feel7_rsn']);

	Unset($row);


	//사화적측면
	$sql = 'SELECT	*
			FROM	hce_inspection_social
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$social1	= $row['social1'];
	$social2	= $row['social2'];
	$social2Rsn	= StripSlashes($row['social2_rsn']);
	$social3	= $row['social3'];
	$social4	= $row['social4'];
	$social4Rsn	= StripSlashes($row['social4_rsn']);
	$social5	= $row['social5'];
	$social6	= $row['social6'];
	$social6Rsn	= StripSlashes($row['social6_rsn']);
	$social7	= $row['social7'];
	$social7Nm	= StripSlashes($row['social7_nm']);
	$social7Tel	= $row['social7_tel'];
	$social8	= $row['social8'];
	$social8Str	= StripSlashes($row['social8_other']);
	$social9	= $row['social9'];
	$social9Str	= StripSlashes($row['social9_other']);

	Unset($row);



	$col[] = $pdf->width * 0.86;
	$col[] = $pdf->width * 0.07;
	$col[] = $pdf->width * 0.07;

	$rowHeight = $pdf->row_height * 0.9;


	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 정서적 측면",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"질문",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight,"예",1,0,'C',1);
	$pdf->Cell($col[2],$rowHeight,"아니오",1,1,'C',1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"이전에 비해 요즘 더 잘 잊어버리십니까?[기억력]",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel1Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"안정되지 않거나 화가난 적이 자주 있습니까?[정서상태](예라면 이유를 작성하여 주십시오.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel2Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유 : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel2Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"소지품 등을 자주 잃어버리십니까?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel3Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"슬픔을 느끼는 적이 종종 있으십니까?[정서상태](예라면 이유를 작성하여 주십시오.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel4Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel4Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"잠은 편안히 잘 주무십니까?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel5Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"작년 한 해 동안 혹은 근래에 친구나 친척을 잃은 일이 있습니까? 있다면, 어떤 영향을 끼쳤습니까?","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel6Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel6Eft,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"정신 질환으로 입원하신 적이 있습니까?(예라면 이유를 작성하여 주십시오.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel7Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       횟수","BL",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,$feel7Cnt,"B",0);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       언제","B",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,$feel7Whn,"B",0);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       이유","B",0,'R');
	$pdf->Cell($col[0] * 0.27,$rowHeight,$feel7Rsn,"RB",1);




	//-------------------------------------------//


	$pdf->SetX($pdf->left);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"■ 사회적 측면",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"질문",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight,"예",1,0,'C',1);
	$pdf->Cell($col[2],$rowHeight,"아니오",1,1,'C',1);


	/*
	$social1	= $row['social1'];
	$social2	= $row['social2'];
	$social2Rsn	= StripSlashes($row['social2_rsn']);
	$social3	= $row['social3'];
	$social4	= $row['social4'];
	$social4Rsn	= StripSlashes($row['social4_rsn']);
	$social5	= $row['social5'];
	$social6	= $row['social6'];
	$social6Rsn	= StripSlashes($row['social6_rsn']);
	$social7	= $row['social7'];
	$social7Nm	= StripSlashes($row['social7_nm']);
	$social7Tel	= $row['social7_tel'];
	$social8	= $row['social8'];
	$social8Str	= StripSlashes($row['social8_other']);
	$social9	= $row['social9'];
	$social9Str	= StripSlashes($row['social9_other']);
	 */


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"최근 1년 사이에 귀하는 출가 또는 분가한 자녀들과 교류는 어떻습니까?","LTR");
	$pdf->Cell($col[1],$rowHeight * 4,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 4,"","TRB",2,'C');


	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 3);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '1' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"①거의 연락없이 지낸다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '2' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"②명절, 생일 등에 가끔씩 만나거나 연락한다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '3' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"③2달~3달에 한 두 번 정도 연락한다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '4' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"④1달에 한 두 번 정도 연락한다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '5' ? ' √' : '','LB');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑤1주 일회 이상 연락한다.","B",0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '6' ? ' √' : '','B');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑥출가한 자녀가 없다.","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"가족과의 관계에 문제가 있습니까?(예라면 이유를 작성하여 주십시오.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social2);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social2Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"귀하는 대체로 이웃과 어느 정도 친하게 지내고 계십니까?","LTR");
	$pdf->Cell($col[1],$rowHeight * 4,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 4,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 3);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '1' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"①거의 모르고 지낸다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '2' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"②인사하는 정도이다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '3' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"③말벗하는 정도이다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '4' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"④말벗과 여러 도움을 주고받는다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '5' ? ' √' : '','LB');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑤절대적으로 믿고 가족처럼 지낸다.","B",0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"이웃과 어울리는데 어려움이 있으십니까?(예라면 이유를 작성하여 주십시오.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social4);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social4Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"친구들이 있어서 정기적으로 만나십니까?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$social5);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"교회, 지역사회활동 그 밖의 사회활동에 참여하십니까? ","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social6);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       예라면, 이유  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social6Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"종교생활을 하신다면 어떻게 하십니까?","LTR");
	$pdf->Cell($col[1],$rowHeight * 5,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 5,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 4);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '1' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"①전혀 하지 않는다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '2' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"②가끔씩 한다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '3' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"③자주 하는 편이다(월1~2회).",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '4' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"④항상 참여한다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '5' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑤종교가 없다.",0,0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","R",1);

	$pdf->SetX($pdf->left);
	//$pdf->Cell($col[0],$rowHeight,"  종교기관명                             전화번호","RBL",1);
	$pdf->Cell($col[0] * 0.20,$rowHeight,"  종교기관명","LB",0,'R');
	$pdf->Cell($col[0] * 0.30,$rowHeight,$social7Nm,"B",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,"전화번호","B",0,'R');
	$pdf->Cell($col[0] * 0.30,$rowHeight,$social7Tel,"RB",1,'R');


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"좋아하는 소일거리(여가시간)는?","LTR");
	$pdf->Cell($col[1],$rowHeight * 5,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 5,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 4);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '1' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"①노인정, 양로원에서 시간을 보낸다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '2' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"②놀이터, 공원, 산책로에서 보낸다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '3' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"③집에서 TV를 보거나 그냥 지낸다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '4' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"④친구의 집이나 내 집에서 친구와 지낸다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '5' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑤노인대학에 가거나 취미활동을 한다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '6' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑥취로사업이나 일, 부업을 한다.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '7' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑦종교활동이나 봉사활동을 한다.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '8' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑧기타(".$social8Str.")","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"귀하는 어려움이 생기거나 아플 때 누구로부터 도움을 가장 많이 받습니까?","LTR");
	$pdf->Cell($col[1],$rowHeight * 6,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 6,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 5);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '1' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"①배우자",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '2' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"②자녀","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '3' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"③형제자매,부모",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '4' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"④이웃","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '5' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑤사회복지관",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '6' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑥동사무소 전문요원","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '7' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑦종교단체",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '8' ? ' √' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑧없다","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '9' ? ' √' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"⑨기타(".$social9Str.")","B",0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","BR",1);

	Unset($col);


	function lfDrawCell56($pdf,$col,$rowHeight,$val){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($col[1],$rowHeight,($val == 'Y' ? '√' : ''),1,0,'C');
		$pdf->Cell($col[2],$rowHeight,($val == 'N' ? '√' : ''),1,1,'C');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}

	function lfDrawEx($pdf,$width,$height,$val,$border='0'){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($width,$height,$val,$border,0,'L');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
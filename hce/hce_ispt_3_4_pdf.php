<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-ADL,IADL
	 *********************************************************/

	$conn->fetch_type = 'assoc';
	$pdf->SetFont($pdf->font_name_kor,'B',13);

	$orgNo = $_SESSION['userCenterCode'];


	//ADL
	$sql = 'SELECT	*
			FROM	hce_inspection_adl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$ADL = $conn->get_array($sql);

	//IADL
	$sql = 'SELECT	*
			FROM	hce_inspection_iadl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$IADL = $conn->get_array($sql);


	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.25;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;

	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->Cell($pdf->width,$pdf->row_height * 1.5,"■ 일상생활 동작정도(ADL)",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"일상생활 동작",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,"자립가능",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height,"약간불편",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,"도와주면 가능",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height,"완전도움필요",1,1,'C',1);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 4,"기본동작",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"문 열고 닫기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_door']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 신발 벗기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_shoes']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"신발을 신장에 넣기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_shoes_put']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"의자를 착상에 넣고 빼기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_chair']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"신변처리",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"욕조에 들어가 목욕하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_bath']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"세수하고 양치질하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_wash']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"기본적인 몸단장 하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_groom']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 옷 입기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_in_dress']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 옷 벗기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_out_dress']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 4,"용변처리",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 변기에 앉기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_bedpan']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"용변 후 뒷처리 하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_after']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"대변 조절하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_feces']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"소변 조절하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_urine']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"식사",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"수저로 먹기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_spoon']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"젓가락 사용하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_stick']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"포크 사용하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_poke']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"컵으로 물 마시기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_cup']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"손잡이 있는 컵 사용하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_grip_cup']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 3,"보행",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 100m이상 걷기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_100m']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"난간잡고 계단 오르내리기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_hand']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"난간없이 계단 오르내리기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_stair']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"침상활동",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"선 상태에서 앉기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_sitdown']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"앉은 상태에서 일어나기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_standup']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 눕고 일어나기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_lie']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"혼자서 뒤척이기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_turn']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"기상 후 침구류 정리하기",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_tidy']);



	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height * 1.5,"■ 도구적 일상생활 동작(IADL)",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"항목",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,"자립가능",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height,"약간불편",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,"도와주면 가능",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height,"완전도움필요",1,1,'C',1);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"전화사용",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['phone']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"외출 또는 여행",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['outdoor']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"물건구입",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['buying']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"식사준비",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['eating']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"집안일(청소나 정리정돈)",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['homework']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"빨래",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['cleaning']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"제시간에 정확한 용량의 약 복용",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['medicine']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"금전관리",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['money']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"집 수공일(바느질이나 못질)",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['repair']);

	Unset($col);



	function lfDrawCell34($pdf,$col,$val){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($col[2],$pdf->row_height,($val == '1' ? '√' : ''),1,0,'C');
		$pdf->Cell($col[3],$pdf->row_height,($val == '2' ? '√' : ''),1,0,'C');
		$pdf->Cell($col[4],$pdf->row_height,($val == '3' ? '√' : ''),1,0,'C');
		$pdf->Cell($col[5],$pdf->row_height,($val == '4' ? '√' : ''),1,1,'C');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
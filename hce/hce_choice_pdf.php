<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	대상자 선정기준표
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$G1 = $pdf->GetStringWidth("▩");
	$G2 = $pdf->GetStringWidth("   ");
	

	$orgNo = $_SESSION['userCenterCode'];
	$userArea = $_SESSION['userArea'];
	$date  = $var['subId'];
	
	
	//대상자명
	$sql = 'SELECT	m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$name = $conn->get_data($sql);

	//사례접수기록
	$sql = 'SELECT	addr
			,		addr_dtl
			,		phone
			,		mobile
			,		rcver_nm
			FROM	hce_receipt
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$hce->SR.'\'
			AND		IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$addr	= $row['addr'].' '.$row['addr_dtl'];
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$mobile	= $myF->phoneStyle($row['mobile'],'.');
	$manager= $row['rcver_nm'];

	Unset($row);
	

	//대상자 선정기준
	$sql = 'SELECT	count(*)
			FROM	hce_choice_cn
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$hce->SR.'\'
			AND		IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt_seq	= \''.$hce->rcpt.'\'
			AND		chic_seq	= \''.$var['idx'].'\'';
	 
	$count = $conn->get_data($sql);
	
	
	if($userArea == '05' && $count > 0 && str_replace('-','',$date) >= '20180101'){
		//대상자 선정기준
		$sql = 'SELECT	*
				FROM	hce_choice_cn
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$hce->SR.'\'
				AND		IPIN		= \''.$hce->IPIN.'\'
				AND		rcpt_seq	= \''.$hce->rcpt.'\'
				AND		chic_seq	= \''.$var['idx'].'\'';
		
		$choice = $conn->get_array($sql);
		
		

	}else {
		//대상자 선정기준
		$sql = 'SELECT	*
				FROM	hce_choice
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$hce->SR.'\'
				AND		IPIN		= \''.$hce->IPIN.'\'
				AND		rcpt_seq	= \''.$hce->rcpt.'\'
				AND		chic_seq	= \''.$var['idx'].'\'';

		$choice = $conn->get_array($sql);
	}
	
	if($userArea == '05' && $choice['chic_dt'] >= '20180101'){
	
		
		$pdf->row_height = 5;


		$point = Array(
				//수급형태
				'A'=>Array(	'1'=>Array('1'=>5,'2'=>3,'3'=>2,'4'=>0)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2,'4'=>2.5))

				//주택형태
			,	'B'=>Array(	'1'=>Array('1'=>0,'2'=>1,'3'=>2,'4'=>3,'5'=>4)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2))

				//가족구성원 실수령액
			,	'C'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1,'7'=>0)
						,	'2'=>Array('1'=>1.5))

				//후원자수
			,	'D'=>Array(	'2'=>Array('1'=>-0.5,'2'=>-1,'3'=>-2))

				//건강상태 신체적
			,	'E'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//건강상태 정서적
			,	'F'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//장애등급
			,	'G'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1)
						,	'2'=>Array('1'=>1.5)
						,	'3'=>Array('1'=>1.5))

				//ADL기능
			,	'H'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1))

				//요양등급
			,	'I'=>Array(	'1'=>Array('1'=>4,'2'=>3,'3'=>2,'4'=>1))

				//재량
			,	'J'=>Array(	'1'=>Array('1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5))
		);

		
		//타이틀
		//$pdf->SetXY($pdf->left, $pdf->top);
		//$pdf->SetFont($pdf->font_name_kor,'B',18);
		//$pdf->Cell($pdf->width,$pdf->row_height*2,"대상자 선정기준표",0,1,'C');
		//$pdf->SetFont($pdf->font_name_kor,'',9);

		$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
		$pdf->Cell($pdf->width,$pdf->row_height,"사례관리자 : ".$manager,0,1,'L');

		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"CLIENT",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$name,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"작 성 일 자",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$myF->dateStyle($choice['chic_dt'],'.'),1,1,'C');

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"주     소",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$addr,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"전 화 번 호",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$phone.' / '.$mobile,1,1,'C');

		Unset($col);


		$col[] = $pdf->width * 0.12;
		$col[] = $pdf->width * 0.18;
		$col[] = $pdf->width * 0.27;
		$col[] = $pdf->width * 0.08;
		$col[] = $pdf->width * 0.06;
		$col[] = $pdf->width * 0.29;

		$rowH = $pdf->row_height;

		$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
		$pdf->Cell($col[0],$rowH,"영역",1,0,'C',1);
		$pdf->Cell($col[1],$rowH,"내용",1,0,'C',1);
		$pdf->Cell($col[2],$rowH,"평가기준",1,0,'C',1);
		$pdf->Cell($col[3],$rowH,"배점기준",1,0,'C',1);
		$pdf->Cell($col[4],$rowH,"평가",1,0,'C',1);
		$pdf->Cell($col[5],$rowH,"비고",1,1,'C',1);
		
		
		$Y = $pdf->GetY();

		$h = $G1 * 2 + $G2;
		
		

		$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*13 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"1  영  역\n경제기능");
		
		/*
		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 13,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 4,"수급형태",1,0,'C');
		$pdf->Cell($col[2],$rowH * 1,"기초수급자",1,0,'C');
		$pdf->Cell($col[3],$rowH * 1,"",1,0,'C');
		$pdf->Cell($col[4],$rowH * 1,"",'LTB',0,'C');
		$pdf->Cell($col[5],$rowH * 1,"",'LTB',1,'C');
		*/

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 13,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 4,"수급형태",1,2,'C');
		$pdf->Cell($col[1],$rowH * 5,"주택형태",1,2,'C');

		$h = $G1 * 4 + $G2 * 3;
		$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY()+($pdf->row_height*5.5 - $h) / 2,'width'=>$col[1],'align'=>'C','text'=>"가족구성원\n실수령액 및 총소득(공적부조포함)");
		
		
		$pdf->Cell($col[1],$rowH * 4,"",1,2,'C');
		
		//수급형태
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '1' ? "√" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"기초수급자",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '2' ? "√" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"차상위계층",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '3' ? "√" : ""));

		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"저소득",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['income_gbn'] == '4' ? "√" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"일 반",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		

		//주택형태
		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '5' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"월세",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '4' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"영구,국민임대",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"무료임대,의탁",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"전세",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['dwelling_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"자가",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		//가족구성원 실수령액 및 총소득(공적부조포함)
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '4' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"최저생계비의 150% 초과",1,0,'L');
		$pdf->Cell($col[3],$rowH,"0",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '4' ? "√" : ""));
		
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"최저생계비의150% 이하",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');

		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"최저생계비의 120% 이하",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');

		//lfDrawCell($pdf,$col[3],$rowH,($choice['gross_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"최저생계비 기준이하",1,0,'L');
		$pdf->Cell($col[3],$rowH,"6",1,2,'C');


		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4],'Y'=>$Y+($pdf->row_height*6 - $h) / 2,'width'=>$col[5],'align'=>'L','text'=>" 수급자증명서, 차상위증명서,\n 건강보험득실확인서 확인");

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['income_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');
		
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*5,number_format($choice['dwelling_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*5,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['gross_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');
		

		//2. 영영 건강기능
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 10 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  영  역\n건강기능");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 10,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"질병정도",1,2,'C');
		$pdf->Cell($col[1],$rowH * 2,"장애등급",1,2,'C');
		$pdf->Cell($col[1],$rowH * 3,"ADL기능",1,2,'C');
		$pdf->Cell($col[1],$rowH * 2,"요양등급",1,2,'C');

		//질병정도
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '1' ? "√" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"만성질환 5개이상",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"만성질환 3개이상",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['disease_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"만성질환 2개이하",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//장애등급
		//lfDrawCell($pdf,$col[3],$rowH,($choice['handicap_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"1급-3급",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['handicap_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"4급-6급",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//adl기능
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"완전도움",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"부분도움",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['adl_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"완전자립",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		//요양등급
		//lfDrawCell($pdf,$col[3],$rowH,($choice['care_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"장기요양1-5등급",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['care_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"등급외 A,B",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['disease_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');
		
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*2,number_format($choice['handicap_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*2," 장애인등록증 확인",1,2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['adl_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*2,number_format($choice['care_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*2," 장기요양인정서 확인",1,2,'L');

		//3. 영영 심리·사회기능
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 3 + $G2 *2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 10 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  영  역\n심리·사회\n기능");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 10,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"생활상태",1,2,'C');
		$pdf->Cell($col[1],$rowH * 4,"사회관계망",1,2,'C');
		$pdf->Cell($col[1],$rowH * 3,"심리·정서상태",1,2,'C');
		

		//생활상태
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '1' ? "√" : ""));
		$pdf->SetXY($pdf->left + $col[0] + $col[1], $Y);
		$pdf->Cell($col[2],$rowH,"독거",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"보호가 필요한 동거가족",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"동거가족",1,0,'L');
		$pdf->Cell($col[3],$rowH,"0",1,2,'C');
		
		//사회관계망
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"전혀 없음",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '2' ? "√" : ""));
		
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"비공식지원체계만 있음",1,0,'L');
		$pdf->Cell($col[3],$rowH,"4",1,2,'C');
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '3' ? "√" : ""));

		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"공식지원체계만 있음",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['social_rel_gbn'] == '4' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"공식+비공식지원체계",1,0,'L');
		$pdf->Cell($col[3],$rowH,"2",1,2,'C');
		
		//심리정서상태
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '1' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"매우 불안",1,0,'L');
		$pdf->Cell($col[3],$rowH,"5",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '2' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"불안",1,0,'L');
		$pdf->Cell($col[3],$rowH,"3",1,2,'C');
		
		//lfDrawCell($pdf,$col[3],$rowH,($choice['life_gbn'] == '3' ? "√" : ""));
		$pdf->SetX($pdf->left + $col[0] + $col[1]);
		$pdf->Cell($col[2],$rowH,"안정",1,0,'L');
		$pdf->Cell($col[3],$rowH,"1",1,2,'C');
		
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['life_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3,"",1,2,'C');
		
		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4],'Y'=>$Y+($pdf->row_height*11.2 - $h) / 2,'width'=>$col[5],'align'=>'L','text'=>" 공식지원체계(이웃, 지인 등)\n 비공식지원체계(단체 기업, 정부)");

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*4,number_format($choice['social_rel_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*4,"",1,2,'C');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2] + $col[3]);
		$pdf->Cell($col[4],$rowH*3,number_format($choice['feel_point']),1,0,'C');
		$pdf->Cell($col[5],$rowH*3," 케어비지트 내 우울척도 활용",1,2,'L');
		
		//사례관리자 재량
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 3 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"사례관리자\n재         량");
		

		$pnt['J']['1'] = $point['J']['1'][$choice['free_gbn']];
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"",1,0,'C');
		$pdf->Cell($col[1],$rowH * 3,"재량",1,0,'C');
		$pdf->Cell($col[2]+$col[3],$rowH * 3,"1 ~ 5",1,0,'C');
		$pdf->Cell($col[4],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,0,'C');
		$pdf->Cell($col[5],$rowH,"긴급지원이 필용한 대상자 6점","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"다중적 서비스 필요한 대상자 3~5점","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"단순 서비스가 필요한 대상자 1~2점","BLR",1,'L');

		//$pdf->Cell($col[6],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,1,'C');	//재량
		
		//총점
		//$totPnt = 0;
		
		$totPnt = $choice['income_point']+$choice['dwelling_point']+$choice['gross_point']+$choice['disease_point']+$choice['handicap_point']+$choice['adl_point']+$choice['care_point']+$choice['life_point']+$choice['social_rel_point']+$choice['feel_point']+$choice['free_point'];



		
		if ($totPnt >= 30){
			$totGbn1 = '●';
			$totGbn2 = '  ';
			$totGbn3 = '  ';
		}else if ($totPnt >= 25 && $totPnt < 30){
			$totGbn1 = '  ';
			$totGbn2 = '●';
			$totGbn3 = '  ';
		}else {
			$totGbn1 = '  ';
			$totGbn2 = '  ';
			$totGbn3 = '●';
		}

		$Y = $pdf->GetY();
		$X = $pdf->left;
		
		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"총점",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3],$rowH * 3,"사례관리( ".$totGbn1." )   기본형( ".$totGbn2." )   선정보류( ".$totGbn3." )",1,0,'C');
		$pdf->Cell($col[4],$rowH * 3,number_format($totPnt),'LR',0,'C');
		$pdf->Cell($col[5],$rowH,"사례관리형 : 30점이상","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"기   본   형 : 25점이상 30점 미만","LR",2,'L');
		$pdf->Cell($col[5],$rowH,"선 정 보 류 : 25점 미만","BLR",1,'L');

		//$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		//$pdf->Cell($col[6],$rowH * 3,"".$totPnt,1,1,'C');

		//관리자 코멘트
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 4 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"사례관리 의견");
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$Y + 1,'width'=>$col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],'text'=>$choice['comment']);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 4,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],$rowH * 4,"",1,1,'C');
	

	}else {
		$point = Array(
				//수급형태
				'A'=>Array(	'1'=>Array('1'=>5,'2'=>3,'3'=>2,'4'=>0)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2,'4'=>2.5))

				//주택형태
			,	'B'=>Array(	'1'=>Array('1'=>0,'2'=>1,'3'=>2,'4'=>3,'5'=>4)
						,	'2'=>Array('1'=>1,'2'=>1.5,'3'=>2))

				//가족구성원 실수령액
			,	'C'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1,'7'=>0)
						,	'2'=>Array('1'=>1.5))

				//후원자수
			,	'D'=>Array(	'2'=>Array('1'=>-0.5,'2'=>-1,'3'=>-2))

				//건강상태 신체적
			,	'E'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//건강상태 정서적
			,	'F'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1)
						,	'2'=>Array('1'=>1.5))

				//장애등급
			,	'G'=>Array(	'1'=>Array('1'=>6,'2'=>5,'3'=>4,'4'=>3,'5'=>2,'6'=>1)
						,	'2'=>Array('1'=>1.5)
						,	'3'=>Array('1'=>1.5))

				//ADL기능
			,	'H'=>Array(	'1'=>Array('1'=>3,'2'=>2,'3'=>1))

				//요양등급
			,	'I'=>Array(	'1'=>Array('1'=>4,'2'=>3,'3'=>2,'4'=>1))

				//재량
			,	'J'=>Array(	'1'=>Array('1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5))
		);

		
		//타이틀
		//$pdf->SetXY($pdf->left, $pdf->top);
		//$pdf->SetFont($pdf->font_name_kor,'B',18);
		//$pdf->Cell($pdf->width,$pdf->row_height*2,"대상자 선정기준표",0,1,'C');
		//$pdf->SetFont($pdf->font_name_kor,'',9);

		$pdf->SetXY($pdf->left, $pdf->GetY());
		$pdf->Cell($pdf->width,$pdf->row_height,"사례관리자 : ".$manager,0,1,'L');

		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.35;

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"CLIENT",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$name,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"작 성 일 자",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$myF->dateStyle($choice['chic_dt'],'.'),1,1,'C');

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$pdf->row_height,"주     소",1,0,'C',1);
		$pdf->Cell($col[1],$pdf->row_height,$addr,1,0,'C');
		$pdf->Cell($col[2],$pdf->row_height,"전 화 번 호",1,0,'C',1);
		$pdf->Cell($col[3],$pdf->row_height,$phone.' / '.$mobile,1,1,'C');

		Unset($col);


		$col[] = $pdf->width * 0.12;
		$col[] = $pdf->width * 0.05;
		$col[] = $pdf->width * 0.07;
		$col[] = $pdf->width * 0.35;
		$col[] = $pdf->width * 0.15;
		$col[] = $pdf->width * 0.20;
		$col[] = $pdf->width * 0.06;

		$rowH = $pdf->row_height;

		$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
		$pdf->Cell($col[0],$rowH,"영역",1,0,'C',1);
		$pdf->Cell($col[1] + $col[2],$rowH,"기준",1,0,'C',1);
		$pdf->Cell($col[3],$rowH,"기본점수",1,0,'C',1);
		$pdf->Cell($col[4] + $col[5],$rowH,"가산점",1,0,'C',1);
		$pdf->Cell($col[6],$rowH,"점수",1,1,'C',1);

		$Y = $pdf->GetY();

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$pdf->left,'Y'=>$pdf->GetY()+($pdf->row_height*17 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"1  영  역\n경제기능");

		$pdf->SetX($pdf->left);
		$pdf->Cell($col[0],$rowH * 17,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 4,"수급형태",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"주택형태",1,2,'C');

		$h = $G1 * 4 + $G2 * 3;
		$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$pdf->GetY()+($pdf->row_height*7 - $h) / 2,'width'=>$col[1] + $col[2],'align'=>'C','text'=>"가족구성원\n실수령액 및 총소득(공적부조포함)");

		$pdf->Cell($col[1] + $col[2],$rowH * 7,"",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"후원자의수",1,1,'C');

		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2], $Y);
		$pdf->Cell($col[3],$rowH,"","R",2,'L');

		//수급형태 income_gbn
		$pnt['A']['1'] = $point['A']['1'][$choice['income_gbn']];
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '1' ? "√" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.55,$rowH,"기초수급자",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.35,$rowH,"(5)",0,0,'L');

		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '2' ? "√" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.60,$rowH,"의료급여2종",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"(3)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.55,$rowH,"차상위계층",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.35,$rowH,"(2)",0,0,'L');

		$pdf->SetFont($pdf->font_name_kor,'B',9);
		lfDrawCell($pdf,$col[3] * 0.5 * 0.10,$rowH,($choice['income_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[3] * 0.5 * 0.60,$rowH,"일           반",0,0,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"(0)","R",2,'L');
		$pdf->Cell($col[3] * 0.5 * 0.30,$rowH,"","R",2,'L');



		//주택형태 dwelling_gbn
		$pnt['B']['1'] = $point['B']['1'][$choice['dwelling_gbn']];
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		$pdf->Cell($col[3],$rowH * 0.5,"","TR",2,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '1' ? "√" : ""));
		$pdf->Cell($col[3] * 0.20,$rowH,"자가(0)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '2' ? "√" : ""));
		$pdf->Cell($col[3] * 0.20,$rowH,"전세(1)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"무료임대,의탁(2)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"영구,국민임대(3)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['dwelling_gbn'] == '5' ? "√" : ""));
		$pdf->Cell($col[3] * 0.45,$rowH,"월세             (4)","R",2,'L');
		$pdf->Cell($col[3] * 0.45,$rowH * 0.5,"","R",2,'L');


		//가족구성원 실수령액 및 총소득(공적부조포함) gross_gbn
		$pnt['C']['1'] = $point['C']['1'][$choice['gross_gbn']];
		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.60,$rowH,"50만원미만","T",0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(6)","TR",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '2' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"80만원~85만원미만",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(5)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"85만원~110만원미만",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(4)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"110만원~135만원미만",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(3)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '5' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"135만원~160만원미만",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(2)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '6' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"160만원~185만원미만",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(1)","R",2,'L');

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['gross_gbn'] == '7' ? "√" : ""));
		$pdf->Cell($col[3] * 0.60,$rowH,"185만원이상",0,0,'L');
		$pdf->Cell($col[3] * 0.35,$rowH,"(0)","R",2,'L');

		$pos[] = Array('X'=>$pdf->left + $col[0] + $col[1] + $col[2],'Y'=>$pdf->GetY() + 1,'width'=>$col[3],'text'=>" 사회복지기관, 종교단체, 친인척, 개인후원자로부터 금품후원제공시 해당 영역별로 각각(-1)점씩 부여");

		$pdf->SetX($pdf->left + $col[0] + $col[1] + $col[2]);
		$pdf->Cell($col[3],$rowH * 3,"",1,0,'L');

		$h = $G1 * 3 + $G2 *2;
		$pdf->SetXY($pdf->left + $col[0] + $col[1] + $col[2] + $col[3], $Y);
		$pos[] = Array('X'=>$pdf->GetX(),'Y'=>$pdf->GetY() + ($pdf->row_height * 4 - $h) / 2,'width'=>$col[4],'align'=>'C','text'=>"경제활동\n능력이없는\n가족수");
		$pdf->Cell($col[4],$rowH * 4,"",1,2,'L');

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$pdf->GetX(),'Y'=>$pdf->GetY() + ($pdf->row_height * 3 - $h) / 2,'width'=>$col[4],'align'=>'C','text'=>"월세는\n가산점 부여");
		$pdf->Cell($col[4],$rowH * 3,"",1,2,'L');

		$X = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4];


		//경제활동 능력이없는 가족수 nonfamily_gbn
		$pnt['A']['2'] = $point['A']['2'][$choice['nonfamily_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[5] * 0.45,$rowH,"1인",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(1)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '2' ? "√" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"2인",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"3인",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(2)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['nonfamily_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[5] * 0.45,$rowH,"4인이상",0,0,'L');
		$pdf->Cell($col[5] * 0.45,$rowH,"(2.5)","R",2,'L');


		//월세는 가산점 부여 rental_gbn
		$pnt['B']['2'] = $point['B']['2'][$choice['rental_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[5] * 0.65,$rowH,"20만원 미만","T",0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(1)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '2' ? "√" : ""));
		$pdf->Cell($col[5] * 0.65,$rowH,"20만원~30만원",0,0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[5] * 0.10,$rowH,($choice['rental_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[5] * 0.65,$rowH,"30만원 이상",0,0,'L');
		$pdf->Cell($col[5] * 0.25,$rowH,"(2)","R",2,'L');

		$X = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3];

		//공적부조 이외에 소득이 없으면 가산점 public_gbn
		$pnt['C']['2'] = $point['C']['2'][$choice['public_gbn']];
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2.5,"","TR",2,'L');
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['public_gbn'] == '1' ? "√" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"공적부조 이외에 소득이 없으면 가산점","R",2,'L');

		$pdf->SetX($X + ($col[4] + $col[5]) * 0.05);
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"(1.5)","R",2,'L');

		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2.5,"","R",2,'L');

		//후원자의수 - 가산금 help_gbn
		$pnt['D']['2'] = 2 + $point['D']['2'][$choice['help_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"후원금품 월 1만원~5만원","T",0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-0.5)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '2' ? "√" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"후원금품 월 5만원이상",0,0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-1)","R",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['help_gbn'] == '3' ? "√" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.60,$rowH,"후원금품 월 10만원이상","B",0,'L');
		$pdf->Cell(($col[4] + $col[5]) * 0.35,$rowH,"(-2)","BR",2,'L');

		$X += ($col[4] + $col[5]);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[6],$rowH * 4,"".($pnt['A']['1'] + $pnt['A']['2']),1,2,'C');	//1영역 - 수급형태 - 점수
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['B']['1'] + $pnt['B']['2']),1,2,'C');	//1영역 - 주택형태 - 점수
		$pdf->Cell($col[6],$rowH * 7,"".($pnt['C']['1'] + $pnt['C']['2']),1,2,'C');	//1영역 - 가족구성원 실령액 - 점수
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['D']['1'] + $pnt['D']['2']),1,2,'C');	//1영역 - 후원자의수 - 점수


		//2. 영영 건강기능
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 8 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"2  영  역\n건강기능");

		$pdf->SetX($X);
		$pdf->Cell($col[0],$rowH * 8,"",1,0,'C');

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$pdf->GetY() + ($rowH * 2 - $h) / 2,'width'=>$col[1],'align'=>'C','text'=>"건강\n상태");
		$pdf->Cell($col[1],$rowH * 2,"",1,0,'C');
		$pdf->Cell($col[2],$rowH,"신체적",1,2,'C');
		$pdf->Cell($col[2],$rowH,"정서적",1,2,'C');

		$pdf->SetX($X + $col[0]);
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"장애등급",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"ADL기능",1,2,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 2,"요양등급",1,2,'C');

		$X += ($col[0] + $col[1] + $col[2]);

		//건강상태 - 신체적 body_gbn
		$pnt['E']['1'] = $point['E']['1'][$choice['body_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.37,$rowH,"매우안좋음(3)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '2' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.27,$rowH,"안좋음(2)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['body_gbn'] == '3' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.21,$rowH,"양호(1)","RBT",2,'L');

		//건강상태 - 정서적 feel_gbn
		$pnt['F']['1'] = $point['F']['1'][$choice['feel_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.37,$rowH,"매우안좋음(3)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '2' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.27,$rowH,"안좋음(2)","BT",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['feel_gbn'] == '3' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.21,$rowH,"양호(1)","RBT",2,'L');

		//장애등급 handicap_gbn
		$pnt['G']['1'] = $point['G']['1'][$choice['handicap_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"1급(6)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '2' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"2급(5)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '3' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.35,$rowH,"3급(4)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[3] * 0.25,$rowH,"4급(3)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '5' ? "√" : ""));
		$pdf->Cell($col[3] * 0.25,$rowH,"5급(2)",0,0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['handicap_gbn'] == '6' ? "√" : ""));
		$pdf->Cell($col[3] * 0.35,$rowH,"6급(1)","R",2,'L');

		//ADL기능 adl_gbn
		$pnt['H']['1'] = $point['H']['1'][$choice['adl_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.45,$rowH,"완전도움(3)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '2' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.45,$rowH,"부분도움(2)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['adl_gbn'] == '3' ? "√" : ""));
		$pdf->Cell($col[3] * 0.95,$rowH,"완전자립(1)","R",2,'L');

		//요양등급 care_gbn
		$pnt['I']['1'] = $point['I']['1'][$choice['care_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"1급(4)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '2' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.25,$rowH,"2급(3)","T",0,'L');
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '3' ? "√" : ""),"T");
		$pdf->Cell($col[3] * 0.35,$rowH,"3급(2)","TR",2,'L');

		$pdf->SetX($X);
		lfDrawCell($pdf,$col[3] * 0.05,$rowH,($choice['care_gbn'] == '4' ? "√" : ""));
		$pdf->Cell($col[3] * 0.95,$rowH,"등급외 A,B(1)","BR",2,'L');

		$X += $col[3];

		//건강상태 - 신체적 - 가산점 body_patient_gbn
		$pnt['E']['2'] = $point['E']['2'][$choice['body_patient_gbn']];
		$pdf->SetXY($X, $Y);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['body_patient_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"중환자 2인 이상인 경우(1.5)","TBR",2,'L');

		//건강상태 - 정서적 - 가산점 feel_patient_gbn
		$pnt['F']['2'] = $point['F']['2'][$choice['feel_patient_gbn']];
		$pdf->SetX($X);
		//$pdf->Cell(($col[4] + $col[5]) * 0.05,$rowH,"√","TB",0,'L');
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['feel_patient_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"중환자 2인 이상인 경우(1.5)","TBR",2,'L');

		//2영역 - 장애등급 - 가산점 handi_dup_gbn
		$pnt['G']['2'] = $point['G']['2'][$choice['handi_dup_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['handi_dup_gbn'] == '1' ? "√" : ""),"T");
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"중복장애(1.5)","TR",2,'L');

		//2영역 - 장애등급 - 가산점 handi_2per_gbn
		$pnt['G']['3'] = $point['G']['3'][$choice['handi_2per_gbn']];
		$pdf->SetX($X);
		lfDrawCell($pdf,($col[4] + $col[5]) * 0.05,$rowH,($choice['handi_2per_gbn'] == '1' ? "√" : ""));
		$pdf->Cell(($col[4] + $col[5]) * 0.95,$rowH,"장애인이 2인 이상인 경우(1.5)","BR",2,'L');

		//2 영역 - ADL기능 - 가산점
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2,"",1,2,'C');

		//2 영역 - 요양등급 - 가산점
		$pdf->SetX($X);
		$pdf->Cell($col[4] + $col[5],$rowH * 2,"",1,2,'C');

		$X += ($col[4] + $col[5]);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[6],$rowH,"".($pnt['E']['1'] + $pnt['E']['2']),1,2,'C');		//2 영역 - 건강상태 - 신체적 - 점수
		$pdf->Cell($col[6],$rowH,"".($pnt['F']['1'] + $pnt['F']['2']),1,2,'C');		//2 영역 - 건강상태 - 정서적 - 점수
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['G']['1'] + $pnt['G']['2'] + $pnt['G']['3']),1,2,'C');	//2 영역 - 장애등급 - 점수
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['H']['1'] + $pnt['H']['2']),1,2,'C');	//2 영역 - ADL기능 - 점수
		$pdf->Cell($col[6],$rowH * 2,"".($pnt['I']['1'] + $pnt['I']['2']),1,2,'C');	//2 영역 - 요양등급 - 점수


		//사례관리자 재량
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 3 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"사례관리자\n재         량");

		$pnt['J']['1'] = $point['J']['1'][$choice['free_gbn']];
		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2],$rowH * 3,"재량",1,0,'C');
		$pdf->Cell($col[3],$rowH * 3,"1 ~ 5",1,0,'C');
		$pdf->Cell($col[4] + $col[5],$rowH,"5점     : 긴급지원이 필용한 대상자","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"4~3점 : 다중적 서비스 필요한 대상자","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"2~1점 : 단순 서비스가 필요한 대상자","BLR",0,'L');

		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		$pdf->Cell($col[6],$rowH * 3,"".($pnt['J']['1'] + $pnt['J']['2']),1,1,'C');	//재량


		//총점
		$totPnt = 0;

		foreach($pnt as $p){
			$totPnt += ($p['1']+$p['2']+$p['3']);
		}

		if ($totPnt >= 25){
			$totGbn1 = '●';
			$totGbn2 = '  ';
		}else if ($totPnt >= 20 && $totPnt < 25){
			$totGbn1 = '  ';
			$totGbn2 = '●';
		}

		$Y = $pdf->GetY();
		$X = $pdf->left;

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 3,"총점",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3],$rowH * 3,"사례관리( ".$totGbn1." )   기본형( ".$totGbn2." )",1,0,'C');
		$pdf->Cell($col[4] + $col[5],$rowH,"사례관리형 : 25점이상","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"기   본   형 : 20점이상 25점 미만","LR",2,'L');
		$pdf->Cell($col[4] + $col[5],$rowH,"선 정 보 류 : 20점 미만","BLR",0,'L');

		$pdf->SetXY($X + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5],$Y);
		$pdf->Cell($col[6],$rowH * 3,"".$totPnt,1,1,'C');

		//관리자 코멘트
		$Y = $pdf->GetY();
		$X = $pdf->left;

		$h = $G1 * 2 + $G2;
		$pos[] = Array('X'=>$X,'Y'=>$Y + ($rowH * 4 - $h) / 2,'width'=>$col[0],'align'=>'C','text'=>"사례관리자\nCOMMENT");
		$pos[] = Array('X'=>$X + $col[0],'Y'=>$Y + 1,'width'=>$col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],'text'=>$choice['comment']);

		$pdf->SetXY($X, $Y);
		$pdf->Cell($col[0],$rowH * 4,"",1,0,'C');
		$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4] + $col[5] + $col[6],$rowH * 4,"",1,1,'C');
	}

	Unset($choice);


	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
	}


	function lfDrawCell($pdf,$col,$rowH,$str,$border='0'){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTexTColor(0,0,255);
		$pdf->Cell($col,$rowH,$str,$border,0,'L');
		$pdf->SetTexTColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
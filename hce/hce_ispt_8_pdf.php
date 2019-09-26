<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지-노인인지능력평가(MMES-DS)
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

	$year	= $myF->issToYear($row['jumin']);	//생년
	$name	= $row['name'];	//성명
	$gender = $myF->euckr($myF->issToGender($jumin));	//성별
	$age	= $tmpJumin != '' ? $myF->issToAge($jumin) : '       ';//연령
	$jumin	= $tmpJumin != '' ? $myF->issStyle($jumin) : '';	//주민번호
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//주소
	$phone	= $myF->phoneStyle($row['phone'],'.');	//연락처
	$mobile	= $myF->phoneStyle($row['mobile'],'.');	//핸드폰
	$ddi	= $tmpJumin != '' ? $myF->euckr($myF->getGapJaDdi('DDI',$year)) : '      ';

	$birthDay	= $myF->dateStyle($myF->issToBirthday($jumin),'KOR');

	$marry	= $row['marry_gbn'];	//결혼
	$cohabit= $row['cohabit_gbn'];	//동거

	Unset($row);


	$sql = 'SELECT	per_family_cnt
			,		per_cost_gbn
			,		per_medical_gbn
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$costGbn	= $row['per_cost_gbn'];	//최저생계구분
	$familyCnt	= $row['per_family_cnt'];	//가족수
	$medicalGbn	= $row['per_medical_gbn'];

	Unset($row);


	$sql = 'SELECT	iver_nm
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';
	$iverNm = $conn->get_data($sql);


	$sql = 'SELECT	*
			FROM	hce_inspection_mmseds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$mmse = $conn->get_array($sql);


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',18);
	$pdf->Cell($pdf->width,$pdf->row_height*2,"노인인지기능평가(MMSE-DS)검사",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'B',9);

	$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"◎ 인구학적 정보",0,1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"성           명",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,$name,1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"연락처",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.23,$pdf->row_height,"집) ".$phone,1,0,'L');
	$pdf->Cell($pdf->width * 0.23,$pdf->row_height,"H.P) ".$mobile,1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"담당자",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,$iverNm,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"성           별",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,$gender,1,0,'L');
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,"주민등록번호",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.54,$pdf->row_height,$jumin,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"생  년  월  일",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.34,$pdf->row_height,$birthDay,1,0,'L');
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"나이 / 띠",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.42,$pdf->row_height,$age."세 / ".$ddi."띠",1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"정규교육연수",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,StripSlashes($mmse['edu_training']),1,0,'L');
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,"한글해독능력",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.54,$pdf->row_height,StripSlashes($mmse['kor_decode']),1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"거    주    지",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$addr,1,1,'L');

	//결혼구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'MR\'';

	$marryStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$marry);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"겨  혼  상  태",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$marryStr,1,1,'L');

	//동거구분
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CB\'';

	$cohabitStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$cohabit);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"동           거",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$cohabitStr,1,1,'L');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'MCL\'
			AND		use_yn	= \'Y\'';

	$costStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$costGbn);

	$conn->row_free();

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height * 2,"월    수    입",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$costStr,1,2,'L');
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,"○ 이 수입에 의존하는 가족수는? (     ".$familyCnt."  )",1,1,'L');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'MDC\'
			AND		use_yn	= \'Y\'';

	$medicalStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$medicalGbn);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"의  료  보  장",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$medicalStr,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"주된평생직업",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,StripSlashes($mmse['life_job']),1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"검진일",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.18,$pdf->row_height,$myF->euckr($myF->dateStyle($mmse['check_dt'],'KOR')),1,0,'L');
	$pdf->Cell($pdf->width * 0.10,$pdf->row_height,"평가장소",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.31,$pdf->row_height,StripSlashes($mmse['evl_place']),1,1,'L');



	$quest[]	= Array('id'=>'Q1','no'=>'1.','quest'=>'올해는 몇 년도 입니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q2','no'=>'2.','quest'=>'지금은 무슨 계절입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q3','no'=>'3.','quest'=>'오늘은 며칠입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q4','no'=>'4.','quest'=>'오늘은 무슨 요일입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q5','no'=>'5.','quest'=>'지금은 몇 월입니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q6','no'=>'6.','quest'=>'우리가 있는 이곳은 무슨 도/특별시/광역시 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q7','no'=>'7.','quest'=>'여기는 무슨 시/군/구 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q8','no'=>'8.','quest'=>'여기는 무슨 구/동/읍 입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q9','no'=>'9.','quest'=>'우리는 지금 이 건물의 몇 층에 있습니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q10','no'=>'10.','quest'=>'이 장소의 이름이 무엇입니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'11.','quest'=>" 제가 세 가지 물건의 이름을 말씀드리겠습니다. 끝까지 다 들으신 다음에 세 가지 물건의 이름을 모두 말씀해 보십시오. 그리고 몇 분 후에는 그 세가지 물건의 이름들을 다시 물어볼 것이니 들으신 물건의 이름을 잘 기억하고 계십시오.\n\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'','quest'=>"나무          자동차          모자",'rst'=>'N','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'','quest'=>"이제 ○○○님께서 방금 들으신 3가지 물건 이름을 모두 말씀해 보세요.",'rst'=>'N','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q11','no'=>'','quest'=>'나무','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q12','no'=>'','quest'=>'자동차','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q13','no'=>'','quest'=>'모자','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q14','no'=>'12.','quest'=>'100에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q15','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q16','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q17','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q18','no'=>'','quest'=>'거기에서 7을 빼면 얼마가 됩니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'13.','quest'=>'조금 전에 제가 기억하라고 말씀드렸던 세 가지 물건의 이름이 무엇인지 말씀하여 주십시오?','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q19','no'=>'','quest'=>'나무','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q20','no'=>'','quest'=>'자동차','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q21','no'=>'','quest'=>'모자','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q22','no'=>'14.','quest'=>'(실제 시계를 보여주며) 이것을 무엇이라고 합니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q23','no'=>'','quest'=>'(실제 연필을 보여주며) 이것을 무엇이라고 합니까?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'15.','quest'=>'제가 하는 말을 끝까지 듣고 따라해 보십시오. 한 번만 말씀드릴 것이니 잘 듣고 따라 하십시오.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q24','no'=>'','quest'=>'간장공장공장장','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'16.','quest'=>"지금부터 제가 말씀드리는 대로 해 보십시오. 한 번만 말씀드릴 것이니 잘 들으시고 그대로 해 보십시오.\n제가 종이를 한 장 드릴 것입니다. 그러면 그 종이를 오른손으로 받아, 반으로 접은 다음, 무릎 위에 올려놓으십시오.\n\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q25','no'=>'','quest'=>'오른손으로 받는다.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q26','no'=>'','quest'=>'반으로 접는다.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q27','no'=>'','quest'=>'무릎 위에 놓는다.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'17.','quest'=>"(겹친 오각형 그림을 가리키며) 여기에 오각형이 겹쳐져 있는 그림이 있습니다. 이 그림을 아래 빈 곳에 그대로 그려보십시오.\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q28','no'=>'','quest'=>"\n\n\n\n\n\n",'path'=>'../hce/img/test_17.GIF','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q29','no'=>'18.','quest'=>'옷은 왜 빨아서 입습니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q30','no'=>'19.','quest'=>'"티끌 모아 태산"은 무슨 뜻 입니까?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');



	$col[] = $pdf->width * 0.04;
	$col[] = $pdf->width * 0.76;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 10);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"◎ MMSE-DS","B",1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetFillColor(234,234,234);

	$pnt = 0;

	foreach($quest as $row){
		if ($row['no'] == '13.'){
			$pdf->Line($pdf->left,$pdf->GetY(),$pdf->left+$pdf->width,$pdf->GetY());

			lfDrawPos($pdf,$pos);

			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor,'',9);
			$pdf->SetXY($pdf->left, $pdf->top);

			Unset($pos);

			$border = "T";
		}else{
			$border = "";
		}

		if ($row['border']) $border = "T";

		$tmp = Explode("\n",$row['quest']);
		$cnt = SizeOf($tmp);

		if ($row['path']){
			$path = $row['path'];

			if (is_file($path)){
				$pdf->Image($path, $pdf->left+$col[0], $pdf->GetY());
			}
		}else{
			$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$pdf->GetY() + 0.3,'width'=>$col[1],'align'=>$row['align'],'text'=>$row['quest']);
		}

		for($i=0; $i<$cnt; $i++){
			$Y = $pdf->GetY();

			$pdf->SetX($pdf->left);
			$pdf->Cell($col[0],$pdf->row_height,($i == 0 ? $row['no'] : ""),($i == 0 ? $border : "")."L",0,'R');
			$pdf->Cell($col[1],$pdf->row_height,"",($i == 0 ? $border : ""),0,'L');

			if ($i == 0){
				$pdf->Cell($col[2],$pdf->row_height * $cnt,($mmse[$row['id']] == 'Y' ? $row['Y'] : ''),($i == 0 ? $border : "")."L",0,'C',($row['rst'] == 'Y' ? '1' : '0'));
				$pdf->Cell($col[3],$pdf->row_height * $cnt,($mmse[$row['id']] == 'N' ? $row['N'] : ''),($i == 0 ? $border : "")."LR",1,'C',($row['rst'] == 'Y' ? '1' : '0'));
				$pdf->SetY($Y + $pdf->row_height);
			}else{
				$pdf->Cell($col[2],$pdf->row_height,"",($i == 0 ? $border : "")."L",0);
				$pdf->Cell($col[3],$pdf->row_height,"",($i == 0 ? $border : "")."LR",1);
			}
		}

		if ($mmse[$row['id']] == 'Y') $pnt += IntVal($row['Y']);
		if ($mmse[$row['id']] == 'N') $pnt += IntVal($row['N']);
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15,$pdf->row_height,"총   점",1,0,'C');
	$pdf->Cell($pdf->width * 0.85,$pdf->row_height,"(".$pnt.") / 30점",1,1,'L');

	$Y = $pdf->GetY();

	lfDrawPos($pdf,$pos);

	Unset($pos);
	Unset($col);



	$col[] = $pdf->width * 0.120;
	$col[] = $pdf->width * 0.120;
	$col[] = $pdf->width * 0.003;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;

	$pdf->SetXY($pdf->left, $Y + 10);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"◎ MMSE-DS 진단검사 의뢰 점수","B",1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',11);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"연   력",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height * 2,"성   별",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height * 2,"","T",0,'C');
	$pdf->Cell($col[3] + $col[4] + $col[5] + $col[6],$pdf->row_height,"교  육  연  수",1,2,'C');
	$pdf->Cell($col[3],$pdf->row_height,"0~3년",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"4~6년",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"7~12년",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"≥13년",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$col[2],"",0,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"60~69세",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"남",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"여",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"19",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"23",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"70~74세",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"남",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"23",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"여",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"18",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"75~79세",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"남",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"22",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"25",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"여",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"17",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"≥80세",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"남",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"18",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"22",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"25",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"여",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"","B",0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"16",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"27",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height,"※ 위 표에 제시된 점수 이하일 경우 진단검사로 의뢰함.",0,1,'L');

	Unset($mmse);
	Unset($col);
	Unset($quest);


	function lfDrawPos($pdf,$pos){
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
		}
	}
?>
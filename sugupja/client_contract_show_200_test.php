<?

	$conn->set_name('euckr');
	
	
	$code = $_SESSION['userCenterCode'];	//기관기호
	$kind = $_POST['kind'];					//서비스구분
	$ssn = $ed->de($_POST['jumin']);		//수급자주민번호
	$svc_seq   = $_POST['seq'];				//고객평가관리(계약키)	

	$report_id = $_POST['report_id'];		//고객평가관리(이용계약서)
	$seq  = $_POST['seq'];
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		if($report_id != ''){
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,	   pay_day1
					,	   pay_day2
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and del_flag = \'N\'';
			
			if($svc_seq != ''){
				$sql .=	'  and svc_seq  = \''.$svc_seq.'\'';
			}
			
			$ct = $conn->get_array($sql);
		}else {
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,      use_yoil1
					,      from_time1
					,      to_time1
					,      use_yoil2
					,      from_time2
					,      to_time2
					,	   pay_day1
					,	   pay_day2
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and seq      = \''.$seq.'\'
					   and del_flag = \'N\'';
			$ct = $conn->get_array($sql);
		}

		$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $svc_seq;

		$sql =  ' select from_dt 
				  ,		 to_dt 
					from client_his_svc
				   where org_no = \''.$code.'\'
					 and jumin  = \''.$ssn.'\'
					 and seq    = \''.$svc_seq.'\'';
		$svc = $conn->get_array($sql);
		

		$sql = "select m03_jumin as jumin
				,	   m03_name as name
				,	   m03_tel as tel
				,	   m03_yboho_name as bohoName
				,	   m03_yboho_juminno as bohoJumin
				,	   m03_yboho_gwange as gwange
				,	   m03_yboho_phone as bohoPhone
				,	   m03_yboho_addr as bohoAddr
				,	   lvl.app_no as injungNo
				,	   case lvl.level when '9' then '일반' else lvl.level end as level
				,	   case kind.kind when '3' then '기초수급권자' when '2' then '의료수급권자' when '4' then '경감대상자' else '일반' end as m92_cont
				,	   kind.kind
				,	   concat(m03_juso1, ' ', m03_juso2) as juso
				,	   max(lvl.from_dt)
				,	   max(lvl.to_dt)
				,	   max(kind.from_dt)
				,	   max(kind.to_dt)
				  from m03sugupja
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 app_no
							  ,		 level
								from client_his_lvl
							   where org_no = '".$code."'
								 and (from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."')
							   order by from_dt desc
								 ) as lvl
						 on lvl.jumin = m03_jumin
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 kind
								from client_his_kind
							   where org_no = '".$code."'
								 and (from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."')
							   order by from_dt desc 
							   ) as kind
						 on kind.jumin = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		$su = $conn->get_array($sql);
		
		
	}
	
	$sql = "select m00_mname as manager"
			 . ",      concat(m00_caddr1, ' ', m00_caddr2) as address"
			 . ",      m00_cname as centerName"
			 . ",      m00_code1 as centerCode"
			 . ",      m00_ctel as centerTel"
			 . ",      m00_fax_no as centerFax"
			 . ",      m00_bank_no as bankNo"
			 . ",      m00_bank_name as bankCode"
			 . ",      m00_jikin as jikin"
			 . "  from m00center"
			 . " where m00_mcode = '".$code
			 . "'  and m00_mkind = '".$kind."'";
			
	$center = $conn->get_array($sql);

	$bank = $center['bankNo'] != '' ? iconv('utf-8','euc-kr', $definition->GetBankName($center['bankCode']))."(".$center['bankNo'].")로" : " ";



	$from_year = $svc['from_dt'] != '' ? substr($svc['from_dt'],0,4) : '           ';	//계약시작기간(년)
	$from_month = $svc['from_dt'] != '' ? substr($svc['from_dt'],5,2) : '     ';		//계약시작기간(월)
	$from_day = $svc['from_dt'] != '' ? substr($svc['from_dt'],8,2) : '     ';			//계약시작기간(일)
	
	$to_year = $svc['to_dt'] != '' ? substr($svc['to_dt'],0,4) : '           ';			//계약종료기간(년)
	$to_month = $svc['to_dt'] != '' ? substr($svc['to_dt'],5,2) : '     ';				//계약종료기간(월)
	$to_day = $svc['to_dt'] != '' ? substr($svc['to_dt'],8,2) : '     ';				//계약종료기간(일)
		

	//이용요일
	$use_yoil1 = $ct['use_yoil1'];
	$use_yoil2 = $ct['use_yoil2'];
	
	//이용시간
	$fm_h1 = $ct['from_time1'] != '' ? substr($ct['from_time1'],0,2) : '     ';
	$fm_m1 = $ct['from_time1'] != '' ? substr($ct['from_time1'],2,2) : '     ';
	$to_h1 = $ct['from_time1'] != '' ? substr($ct['to_time1'],0,2) : '     ';
	$to_m1 = $ct['from_time1'] != '' ? substr($ct['to_time1'],2,2) : '     ';
	$fm_h2 = $ct['from_time1'] != '' ? substr($ct['from_time2'],0,2) : '     ';
	$fm_m2 = $ct['from_time1'] != '' ? substr($ct['from_time2'],2,2) : '     ';
	$to_h2 = $ct['from_time1'] != '' ? substr($ct['to_time2'],0,2) : '     ';
	$to_m2 = $ct['from_time1'] != '' ? substr($ct['to_time2'],2,2) : '     ';

	$jikin = $center['jikin'];
	
	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '말';	//이용납부일1
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';		//이용납부일2

	$pdf->MY_ADDPAGE();
	
	$st_getY = $pdf -> GetY();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "장기요양급여 이용 표준약관\n(방문요양)", 1,"C");
	
	//$pdf->Image('../image/standard_mark.jpg', 140, 45, '41', '35');	//공정거래위원회 로고

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+3);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "    이용자, 제공자 및 대리인(보호자)은 장기요양급여 이용에 대하여\n 다음과 같은 조건으로 계약을 체결한다.");

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+2);
	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->Cell($pdf->width, $pdf->row_height*1.5, "계약당사자", 1, 1, "L" ,true);


	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2.2*3.1, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "성 명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*2, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*2, "", 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.15, 'y'=>$pdf->GetY()*1.47, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"제공자\n  (을)");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "생년월일", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->issStyle($su['jumin']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($su['tel'],'.'), 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.15, 'y'=>$pdf->GetY()*1.56, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"대리인\n  또는\n보호자\n  (병)");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, ' '.$su['juso'], 1, 1, "L");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "구 분", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, " □ 일반  □ 경감대상자  □ 의료수급자 □ 기초수급권자", 1, 1, "L");

	if(strlen($center['centerName']) > 26){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.22, 'y'=>$pdf->GetY()*1, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.30, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.22, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.30, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*1.5*3, "", 'TL', 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "기관명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, "", 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "기관기호", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $center['centerCode'], 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$center['manager']);

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "기관장 성명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($center['centerTel'],'.'), 1, 1, "C");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, ' '.$center['address'], 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoName']);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*1.5*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "성 명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "관 계", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $su['gwange'], 1, 1, "C");
	
	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "생년월일", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, " ".$myF->issToBirthday($su['bohoJumin']), 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.31, $pdf->row_height*1.5, $myF->phoneStyle($su['bohoPhone'],'.'), 1, 1, "C");

	$pdf->SetX($pdf->left+18.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*1.5, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.76, $pdf->row_height*1.5, $su['bohoAddr'], 1, 1, "L");
	
	
	
	if ($su['kind'] == '1'){
		//일반
		$pos_x = 61;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '3'){ 
		//기초수급권자
		$pos_x = 142;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '2'){
		//의료수급권자
		$pos_x = 111.5;
		$pos_y = 100;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($su['kind'] == '4'){
		//경감대상자
		$pos_x = 80;
		$pos_y = 100;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+2, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제1조(목적)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+7, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "고령이나 노인성질병 등으로 인하여 혼자서 일상생활을 수행하기 어려운 노인들 중 장기요양등급을 받은 분들에게 방문요양급여를 제공하여 노후의 건강증진 및 생활안정을 도모하고 그 가족의 부담을 덜어줌으로써 삶의 질을 향상시키고자 한다. ")));
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+16, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제2조(계약기간)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+21, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"① 계약기간은 ".$from_year."년 ".$from_month."월 ".$from_day."일부터 ".$to_year."년 ".$to_month."월 ".$to_day."일까지로 한다.\n② 제1항의 계약기간은 당사자 간의 협의에 따라 변경할 수 있다.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+30, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제3조(급여범위)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+35, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "방문요양급여는 장기요양요원이 '갑'  의 가정 등을 방문하여 신체활동 및 가사활동 등을 지원하는 장기요양급여로 한다.")));
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+40, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제4조(급여이용 및 제공)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+45, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"① 방문요양급여 이용 및 제공은 장기요양급여 이용(제공)계획서에 의한다.\n② '갑'  의 방문요양급여 이용시간은 아래와 같이 한다.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);
	
	#이용시간표
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+63, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>"□월  □화  □수  □목\n □금  □토  □일");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+74, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>"□월  □화  □수  □목\n □금  □토  □일");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+65, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>$fm_h1."시 ".$fm_m1."분    ~".$to_h1."시 ".$to_m1."분");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+76, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>5, 'align'=>'C', 'text'=>$fm_h2."시 ".$fm_m2."분    ~".$to_h2."시 ".$to_m2."분");
	
	$pdf->SetXY($pdf->left, $pdf->getY()+55);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "구분", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용일", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용시간", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*2, "이용시간(1)", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*2, "이용시간(2)", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*2, "", 1, 1, "C");
	
	$pos_x = 66.9;
	$pos_y = 224; 

	for($i=0; $i<strlen($use_yoil1); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 5.5;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil1[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}

	$pos_x = 66.9;
	$pos_y = 235; 
	
	for($i=0; $i<strlen($use_yoil2); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 5.5;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil2[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.02, 'y'=>$pdf->GetY()+1, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"※ 요일에 따라 이용시간이 다른 경우 이용시간 기재란을 늘려서 기록함\n※ '갑'   또는 '병' 은 사정에 의해 일시적으로 이용시간을 지키기 어려운 경우 서비스  이용시작 최소 1시간 전에 '을' 에게 연락을 취해야 함.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+10, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"③ 관공서의 공휴일에 관한 규정’에 의한 공휴일에 급여를 제공하는 경우에는 '을' 은 30%의 할증비용을 청구할 수 있다.\n④ 야간(18:00~22:00), 심야(22:00~06:00)에 급여를 제공하는 경우에는 '을' 은 야간 20%, 심야 30%의 할증비용을 청구할 수 있다.\n⑤ 야간심야휴일가산이 동시에 적용되는 경우에는 중복하여 가산하지 않는다.\n⑥ '을' 은 익월 장기요양급여 제공을 하고자하는 경우에는'갑'  (또는 '병' )과 협의하여 당월 ?일까지 별지 제1호서식의 장기요양급여 이용계획\n   서를 작성하고 장기요양급여 이용계획서에 따라 장기요양급여를 제공 한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$pdf->GetY()+40, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제5조(계약자 의무)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+5, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"① '갑'  은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 월 이용료 납부\n   2. 방문요양급여 범위내 급여이용\n   3. 장기요양급여 이용수칙 준수\n   4. 기타 '을' 과 협의한 규칙 이행\n② '을' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 방문요양급여 제공 계약내용 준수\n   2. 급여제공 중 '갑'  에게 신병 이상이 생기는 경우 즉시 '병' 에게 통보\n   3. 급여제공시간에 '갑'  의 주변 및 집기류의 청결 및 유지관리\n   4. 급여제공 중 알게 된 '갑'  의 신상 및 질환 증에 관한 비밀유지\n  (단, 치료 등의 조치가 필요한 경우는 예외)\n   5. 이용상담, 지역사회 다른 서비스 이용 정보제공\n   6. 노인학대 예방 및 노인인권 보호 준수\n   7. 기타 '갑'  (또는 '병' )의 요청에 협조\n③ '병' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. '갑'  에 관한 건강 및 필요한 자료제공\n   2. '갑'  의 월 이용료 등 비용 부담\n   3. 인적 사항 및 장기요양보험 등급 변경 시 즉시 '을' 에게 통보\n   4. '갑'  에 대한 의무이행이 어려울시 대리인 선정 및 '을' 에게 통보\n   5. 기타 '을' 의 협조요청 이행");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+86, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제6조(계약해지 요건)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+91, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"① '갑'  (또는 '병' )은 다음 각호에 해당되는 경우에는 '을' 과 협의하여 계약을 해지 할 수 있다.
	  1. 제2조의 계약기간이 만료된 경우
	  2. 제3조의 방문요양급여 범위에 해당하는 서비스를 이행하지 아니한 경우
	  3. 제4조제2항의 방문요양급여 제공시간을'갑'  (또는'병' )의 동의 없이 '을' 이 임의로 변경하거나 배치된 장기요양요원을 임의로 변경 했을\n      경우
	  4. 기타 '갑'  의 계약해지 사유가 발생한 경우 ② '을' 은 다음 각호에 해당되는 경우에는 '갑'  (또는 '병' )과 협의하여 계약을 해지 할 수 있다.
	  1. 제2조의 계약기간이 만료되거나 사망한 경우
	  2. '갑'  이 장기요양보험 등급외자로 등급변경이 발생한 경우
	  3. '갑'  의 건강진단 결과「감염병의예방및관리에대한법률」에 따른 감염병 환자로서 감염의 위험성이 있는 경우로 판정될 때
	  4. '갑'  의 건강상의 이유로 서비스 이용이 어려울 때
	  5. 이용계약시 제시된 이용안내를 '갑'  이 정당한 이유 없이 따르지 않는 등 서비스 제공에 심각한 지장을 줄 때
	  6. '갑'  이 월 5회 이상 무단으로 방문요양급여 이용시간과 장소를 지키지 아니하였을 때");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+140, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제7조(계약의 해지)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+145, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" ① '갑'  (또는'병' )은 제6조제1항의 계약해지 요건이 발생한 경우에는 해당일 또는 계약기간 만료일전에 별지 제2호서식의 장기요양급여 종\n    결 신청서를 제출하여야 한다. 다만, 기타부득이한 경우에는 우선 유선으로 할 수 있다.\n ② '을' 은 제6조제2항에 의한 계약해지 요건이 발생한 경우에는 계약해지 의사를 별지 제2호서식의 장기요양급여 종결안내서 및 관련 증빙서\n    류와 함께 '갑'  과 '병' 에게 통보하고 충분히 설명해야 한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+162, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제8조(이용료 납부)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+167, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" ① '을' 은 전월 1일부터 말일까지의 이용료를 매월  ".$pay_day1."일에 정산하고 '갑'  (또는 '병' )에 게  ".$pay_day2."일까지 별지 제3호서식의 장기요양급여 이용\n    료 세부내역서를 통보한다.\n ② '갑'  은 매월 ".$bank." 본인부담금을 납부 한다. 다만, 납부일이 공휴일인 경우에는 그 익일로 한다.\n ③ '을' 은 '갑'  이 납부한 비용에 대해서는 노인장기요양보험법 시행규칙[별지 제4호서식]의 장기요양급여 납부확인서를 발급한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+184, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제9조(재계약)", 'font_bold'=>'B');
	
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+189, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"  다음 각호에 해당하는 경우에는 이를 반영한 계약서를 재작성한다.
   1. 제2조의 계약기간이 만료된 경우
   2. 장기요양 인정등급이 변경된 경우
   3. 방문요양 급여비용 및 본인부담 비용이 변경된 경우
   4. 기타 '갑'  과 '을' 이 필요한 경우");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+210, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제10조(건강관리)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+215, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"①'을' 은'갑'  의 건강 및 감염병 예방을 위하여 종사자들에게 연 1회 이상 건강진단을 실시 하여야 한다.\n②'을' 은 장기요양요원이 방문요양급여 제공도중 '갑'  에게 상해를 입혔을 경우 적절한 조치를 취해야 한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+224, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제11조(위급 시 조치)", 'font_bold'=>'B');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+229, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" ① '을' 은 방문요양급여 제공시간에 '갑'  의 생명이 위급한 상태라고 판단된 때에는 '갑'  (또는 '병' )이 지정한 병원 또는 관련 의료기관으로 즉시 후송하고 '병' 에게 즉시 통보하여야 한다.\n ② '병' 은 제1항의 규정에 의한 통보를 받았을 때에는 신속하게 대처하여야 한다. 다만, 대처가 어려울 경우에는 우선 진료를 받을 수 있도록 조치하여야 한다.\n ③ '갑'  이 서비스 이용도중 사망하였을 경우'을' 은 즉시'병' 에게 통보한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+249, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"제12조(개인정보 보호의무)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+254, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" ① '갑'  은 본인의 개인정보에 대해 알 권리가 있다.\n ② '을' 은 '갑'  의 개인정보를 관계규정에 따라 보호하여야 한다.\n ③ '을' 은 장기요양서비스 제공에 필요한 '갑'  의 개인 정보 자료를 수집하고 활용하며 동자료를 노인장기요양보험 운영주체 등에게 관계규정\n   에 따라 제출할 수 있다.\n ④ '을' 은 개인정보수집 및 활용을 하고자 하는 경우에는 '갑'  에게 별지 제5호서식의 개인정보제공 및 활용 동의서를 받아야 한다.\n ⑤ '을' 은 '갑'  의 사생활을 존중하고, 업무상 알게 된 개인정보는 철저히 비밀을 보장한다. ");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+13, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제13조(기록 및 공개)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+18, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"'을' 은 '갑'  의 생활과 장기요양서비스에 관한 모든 내용을 상세히 관찰하여 정확히 기록하고, '갑'  (또는 '병' )이 요구할 경우에는 표준양식에 의거한 기록을 공개하여야 한다.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+27, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제14조(배상책임)", 'font_bold'=>'B');

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+32, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>" ① '을' 은 다음 각호의 경우에는'갑'  (또는'병' )에게 배상의무가 있으며 배상책임은 관계규정에 따른다.\n   1. 장기요양요원(또는 '을' )의 고의나 과실로 인하여 '갑'  을 부상케 하는 등 건강을 상하게 하거나 사망에 이르게 하였을 때
	  2. 장기요양요원(또는 '을' )의 학대(노인복지법 제1조의2 제4호의 노인학대 및 같은 법제39조의9의 금지행위를 말한다)로 인하여 '갑'  의\n       건강을 상하게 하거나, 사망에 이르게 하였을 때\n ② 다음 각 호에 해당되는 경우에는 '갑'  (또는 '병' )은 '을' 에게 배상을 요구할 수 없다.
	  1. 자연사 또는 질환에 의하여 사망 하였을 때
	  2.'을' 이 선량한 주의의무를 다했음에도 임의로 외출하여 상해를 당했거나 사망 하였을 때
	  3. 천재지변으로 인하여 상해를 당했거나 사망 하였을 때
	  4. '갑'  의 고의 또는 중과실로 인하여 상해를 당했거나 사망하였을 때");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.001, 'y'=>$st_getY+69, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>5, 'align'=>'L', 'text'=>"제15조(기타)", 'font_bold'=>'B');

	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+74, 'font_size'=>8, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>4, 'align'=>'L', 'text'=>"① 이 계약서에서 규정하지 않은 사항은 민법이나 사회상규에 따른다.\n② 부득이한 사정으로 소송이 제기될 경우 '갑'  (또는 '병' ) 또는 시설이 속한 소재지역의\n    관할법원으로 한다.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$st_getY+95, 'font_size'=>14, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  위와 같이 계약을 체결하고 본 계약체결을 증명하기 위하여 쌍방이 계약서를 작성 날인 후 각각 1부씩 보관키로 한다.");
	
	set_array_text($pdf, $pos);
	unset($pos);
	unset($pos_x);
	unset($pos_y);

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+45,$st_getY+125);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, substr($ct['reg_dt'],0,4)."년", 0, 0, "R");
	$pdf->Cell($pdf->width*0.125, $pdf->row_height, substr($ct['reg_dt'],5,2)."월", 0, 0, "R");
	$pdf->Cell($pdf->width*0.125, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2))."일", 0, 1, "R");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
	$pdf->Text($pdf->left+5,$pdf->getY()+15,"상기 내용에 대한 충분한 설명을 '갑'  과 '병' 에게 제공하였습니다.");
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 177, 170, '20', '20');	//기관 직인
		}
	}

	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+25);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'을'  기관장", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $center['manager']."   (인)", 0, 1, "R");
	

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 4);
	$pdf->Text($pdf->left+5,$pdf->getY()+15,'상기 내용을 읽고 그 내용에 동의합니다.');
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+30);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'갑'  이용자", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['name']."   (인)", 0, 1, "R");
	
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+10);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'병'  대리인", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['bohoName']."   (인)", 0, 1, "R");
	
	set_array_text($pdf, $pos);
	unset($pos);
	unset($pos_x);
	unset($pos_y);
	
	
	# 이용계약서
	
	$pdf->MY_ADDPAGE();

	$pdf->SetXY($pdf->left+5, $pdf->top+5);
	$pdf->SetFont('바탕','',11);
	$pdf->Cell(150,5,'[별지 제5호서식]',0,1,'L');

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+11, $pdf->width-10, $pdf->height-30);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 18);
	$pdf->SetXY($pdf->left, $pdf->top+25);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "개인정보 제공 및 활용 동의서", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "성 명:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, $su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, "(생년월일 :    ".$myF->issToBirthday($su['jumin'],'.'), 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, ")", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height+3, "주 소:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $juso[0], 0, 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "1. 수집 및 이용목적", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "○ 장기요양급여 관련 정보
○ 이용자의 지역연계 관련 정보
○ 관련기관 정보제공 요청시 필요한 정보
○ 기타 목적사업 수행에 필요한 정보
○ 대상자 급여 관련에 필요한 정보의 활용
○ 제공기관 간의 서비스 연계와 관련사항에 관한 대상자 정보 제공
○ 관련기관 정보제공 요청시 제공
○ 장기요양계획, 욕구조사, 정기요양서비스 질 수준 향상 등에 활용", 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "2. 이용기간 및 보유기간", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "○ 이용기간 : 급여개시일부터 급여계약기간 만료(해지)일까지로 함
○ 보유기간 : 급여개시일부터 급여계약기간 만료(해지) 후 5년까지로 함", 0, "L");

	$pdf->SetXY($pdf->left+15, $pdf->GetY()+5);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "3. 수집항목", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "○ 개인식별정보(성명, 주민등록번호, 외국인등록번호)
○ 개인정보(주소, 연락처, 가족사항)
○ 사진", 0, "L");

	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "상기 본인은 개인정보를 제공하고 활용하는 것에 동의합니다.", 0, "L");
	
	$pdf->SetXY($pdf->width*0.43, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "L");
	
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "이 용 자 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['name'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (인)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "보 호 자 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['bohoName'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (인)", 0, 0, "L");

	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
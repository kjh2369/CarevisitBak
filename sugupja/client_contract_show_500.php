<?
	include_once("../inc/_definition.php");

	$conn->set_name('euckr');

	$code = $_SESSION['userCenterCode'];	//기관기호
	$kind = $_POST['kind'];				//서비스구분
	$ssn = $ed->de($_POST['jumin']);	//수급자주민번호
	$svc_seq   = $_POST['seq'];			//고객평가관리(계약키)	
	$seq   = $_POST['seq'];			//고객평가관리(계약키)	

	$report_id = $_POST['report_id'];	//고객평가관리(이용계약서)
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		if($report_id != ''){
			$sql = 'select svc_cd
					,	   seq
					,	   reg_dt
					,	   svc_seq
					,	   from_dt
					,	   to_dt
					,      bath_weekly
					,      from_time
					,      to_time
					,	   use_type
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text2
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
					,	   from_dt
					,	   to_dt
					,      bath_weekly
					,      from_time
					,      to_time
					,	   use_type
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
					,      other_text2
					  from client_contract
					 where org_no   = \''.$code.'\'
					   and svc_cd   = \''.$kind.'\'
					   and jumin    = \''.$ssn.'\'
					   and seq		= \''.$seq.'\'
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
				
		$from_dt = ($ct['from_dt'] != '' ? $ct['from_dt'] : $svc['from_dt']);
		$to_dt = ($ct['to_dt'] != '' ? $ct['to_dt'] : $svc['to_dt']);

		$sql = "select m03_jumin as jumin
				,	   m03_key
				,	   m03_name as name
				,	   m03_tel as tel
				,	   m03_hp as hp
				,	   m03_yboho_name as bohoName
				,	   m03_yboho_juminno as bohoJumin
				,	   m03_yboho_gwange as gwange
				,	   m03_yboho_phone as bohoPhone
				,	   m03_yboho_addr as bohoAddr
				,	   lvl.app_no as injungNo
				,	   case lvl.level when '9' then '일반' else lvl.level end as level
				,	   case kind.kind when '3' then '기초수급권자' when '2' then '의료수급권자' when '4' then '경감대상자' else '일반' end as m92_cont
				,	   kind.kind
				,	   m03_juso1 as juso
				,	   m03_juso2 as juso_dtl
				,	   max(lvl.from_dt)
				,	   max(lvl.to_dt)
				,	   max(kind.from_dt)
				,	   max(kind.to_dt)
				,	   real_jumin
				  from m03sugupja
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 app_no
							  ,		 level
								from client_his_lvl
							   where org_no = '".$code."'
								 and (from_dt between '".$from_dt."' and '".$to_dt."'
								  or to_dt between '".$from_dt."' and '".$to_dt."')
							   order by from_dt desc
								 ) as lvl
						 on lvl.jumin = m03_jumin
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 kind
								from client_his_kind
							   where org_no = '".$code."'
								 and (from_dt between '".$from_dt."' and '".$to_dt."'
								  or to_dt between '".$from_dt."' and '".$to_dt."')
							   order by from_dt desc 
							   ) as kind
						 on kind.jumin = m03_jumin
				   LEFT JOIN client_option as opt
						ON   opt.org_no = m03_ccode
						AND  opt.jumin  = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		$su = $conn->get_array($sql);
		
		$juso =  explode('<br />',nl2br($su['juso']));

		$jumin = $su['real_jumin'] != '' ? str_replace('-','', $ed->de($su['real_jumin'])) : $su['jumin'];
	}
	
	if(!$su['jumin']) $jumin = '';

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
	
	if($ct['seq'] == 0 && $code == '34311000305'){
		//행복재가
		unset($center);
	}
	

	//등급, 인정번호
	$sql = 'SELECT	app_no, level
			FROM	client_his_lvl
			WHERE	org_no	 = \''.$code.'\'
			AND		svc_cd	 = \''.$kind.'\'
			AND		jumin	 = \''.$ssn.'\'
			AND		from_dt	<= \''.str_replace('-','',$ct['from_dt']).'\'
			AND		to_dt	>= \''.str_replace('-','',$ct['from_dt']).'\'';
	
	$tmpR = $conn->get_array($sql);

	$appNo = $tmpR['app_no'];
	//$level = $tmpR['level'].' '.$tmpR['addr_dtl'];
	
	$level = $myF->_lvlNm($tmpR['level']);
	

	//수급자구분
	$sql = 'SELECT	kind, rate
			FROM	client_his_kind
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$ssn.'\'
			AND		from_dt <= \''.str_replace('-','',$ct['from_dt']).'\'
			AND		to_dt	>= \''.str_replace('-','',$ct['from_dt']).'\'';
	
	$sKind = $conn->get_array($sql);
	
	$svcKind = $sKind['kind'];
	$svcRate = $sKind['rate'];


	//연락처
	$tel = $su['tel'] != '' ? $su['tel'] : $su['hp'];

	$bank = $center['bankNo'] != '' ? iconv('utf-8','euc-kr', $definition->GetBankName($center['bankCode']))."(".$center['bankNo'].")로 " : " ";
	

	$from_year = $from_dt != '' ? substr($from_dt,0,4) : '           ';	//계약시작기간(년)
	$from_month = $from_dt != '' ? substr($from_dt,5,2) : '     ';		//계약시작기간(월)
	$from_day = $from_dt != '' ? substr($from_dt,8,2) : '     ';			//계약시작기간(일)
	
	$to_year = $to_dt != '' ? substr($to_dt,0,4) : '           ';			//계약종료기간(년)
	$to_month = $to_dt != '' ? substr($to_dt,5,2) : '     ';				//계약종료기간(월)
	$to_day = $to_dt != '' ? substr($to_dt,8,2) : '     ';				//계약종료기간(일)
		

	//이용요일
	for($i=0; $i<7; $i++){
		if($ct['bath_weekly'][$i] == 'Y' and ($i == 0)){
			$yoil .= '월';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 1)){
			$yoil .= '화';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 2)){
			$yoil .= '수';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 3)){
			$yoil .= '목';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 4)){
			$yoil .= '금';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 5)){
			$yoil .= '토';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 6)){
			$yoil .= '일';
		}

	}
	
	//직인
	$jikin = $center['jikin'];

	

	$yoil1 = substr($yoil, 0, 2);
	$yoil2 = substr($yoil, 2, 2);
	
	$yoil1 = $yoil1 != '' ? $yoil1 : '    ';
	$yoil2 = $yoil2 != '' ? $yoil2 : '    ';

	$from_time = $ct['from_time'] != '' ?  $myF->timeStyle($ct['from_time']) : '         ';	//이용시작시간
	$to_time = $ct['to_time'] != '' ?  $myF->timeStyle($ct['to_time']) : '         ';		//이용종료시간

	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '말';	//이용납부일1
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';		//이용납부일2
	$pay_day3  = $ct['pay_day3'] != '' ? $ct['pay_day3'] : '15';	//본인부담금납부일

	$pdf->MY_ADDPAGE();
	
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg';

	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 93, $pdf->getY()+115, '20', '20');	//고객 서명
	}


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "장기요양급여 이용 표준약관\n(방문목욕)", 1,"C");
	
	
	$pdf->Image('../image/standard_mark.jpg', 140, 45, '41', '35');	//공정거래위원회 로고
	$pdf->Image('../image/standard_mark2.jpg', 141, 45, '38', '28');	//공정거래위원회 로고
		
	

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+45);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "    이용자, 제공자 및 대리인(보호자)은 장기요양급여 이용에 대하여\n 다음과 같은 조건으로 계약을 체결한다.");
	
	if(str_replace('-','', $from_dt) >= '20180101'){
		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+0.5);
	}else {
		$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size+2);
	}
	
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+15);
	$pdf->Cell($pdf->width*0.9, $pdf->row_height*2, "계약당사자", 1, 1, "L" ,true);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.198, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"이용자\n  (갑)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 7.98, 'y'=>$pdf->GetY()*1.04, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.14, 'height'=>4.5, 'align'=>'C', 'text'=>"등  급/\n인정번호");
	
	
	$lvlNo = $tmpR['level'] != '' ? $myF->euckr($level)."/\n".$appNo : '';

	$pos[sizeof($pos)] = array('x'=>$pdf->left * 10.1, 'y'=>$pdf->GetY()*1.04, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'C', 'text'=>$lvlNo);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.055 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['name']);

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2.2*4.1, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*3, "성 명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*3, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*3, "", 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.34, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"제공자\n  (을)");

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "주민번호", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->issStyle($jumin), 1, 0, "C");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($tel,'.'), 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 1.895, 'y'=>$pdf->GetY()*1.447, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.1, 'height'=>4.5, 'align'=>'L', 'text'=>"대리인\n  또는\n보호자\n  (병)");
	
	if(strlen($su['juso']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$juso[0].' '.$su['juso_dtl']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$juso[0].' '.$su['juso_dtl']);
	}

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	if(str_replace('-','', $from_dt) >= '20180101'){
		$pdf->SetX($pdf->left+28.2);
		$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "구 분", 1, 0, "C" ,true);
		$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, " □ 일반(15%)  □ 의료(6%) □ 경감(9%) □ 경감(6%) □ 기초", 1, 1, "L");

		if(strlen($center['centerName']) > 22){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'C', 'text'=>$center['centerName']);
		}
		
		if($jikin != ''){
			$exp = explode('.',$jikin);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$jikin, 92, 186, '20', '20');	//기관 직인
			}
		}
	}else {
		$pdf->SetX($pdf->left+28.2);
		$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "구 분", 1, 0, "C" ,true);
		$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, " □ 일반  □ 경감대상자  □ 의료수급자 □ 기초수급권자", 1, 1, "L");

		if(strlen($center['centerName']) > 22){
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.26, 'height'=>4.5, 'align'=>'C', 'text'=>$center['centerName']);
		}
		
		if($jikin != ''){
			$exp = explode('.',$jikin);
			$exp = strtolower($exp[sizeof($exp)-1]);
			if($exp != 'bmp'){
				$pdf->Image('../mem_picture/'.$jikin, 92, 186, '20', '20');	//기관 직인
			}
		}
	}

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2*3, "", 'TL', 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "기관명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, "", 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "기관기호", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $center['centerCode'], 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$center['manager']);
	

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "기관장 성명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($center['centerTel'],'.'), 1, 1, "C");
	
	if(strlen($su['address']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$center['address']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$center['address']);
	}
	
	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.22, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoName']);

	$pdf->SetX($pdf->left+10);
	$pdf->Cell($pdf->width*0.1, $pdf->row_height*2*3, "", 1, 0, "L" ,true);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "성 명", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "관 계", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $su['gwange'], 1, 1, "C");
	
	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "생년월일", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, " ".$myF->issToBirthday($su['bohoJumin']), 1, 0, "L");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "연락처", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.26, $pdf->row_height*2, $myF->phoneStyle($su['bohoPhone'],'.'), 1, 1, "C");
	
	if(strlen($su['bohoAddr']) > 118){
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoAddr']);
	}else {
		$pos[sizeof($pos)] = array('x'=>$pdf->left * 5, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.65, 'height'=>4.5, 'align'=>'L', 'text'=>$su['bohoAddr']);
	}

	$pdf->SetX($pdf->left+28.2);
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "주 소", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.66, $pdf->row_height*2, '', 1, 1, "L");
	
	
	if(str_replace('-','', $from_dt) >= '20180101'){
		if ($svcKind == '1'){
			//일반
			$pos_x = 71;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '3'){ 
			//기초수급권자
			$pos_x = 175;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '2'){
			//의료수급권자
			$pos_x = 100;
			$pos_y = 171;	
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '4'){
			//경감대상자
			if($svcRate == 9.0){
				$pos_x = 125;
			}else {
				$pos_x = 150;	
			}
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}else {
		if ($svcKind == '1'){
			//일반
			$pos_x = 71;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '3'){ 
			//기초수급권자
			$pos_x = 152;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '2'){
			//의료수급권자
			$pos_x = 121.5;
			$pos_y = 171;	
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}else if ($svcKind == '4'){
			//경감대상자
			$pos_x = 90;
			$pos_y = 171;
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+10,'제1조(목적)');
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+12, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "고령이나 노인성질병 등으로 인하여 혼자서 일상생활을 수행하기 어려운 노인들 중 장기요양등급을 받은 분들에게 방문목욕급여를 제공하여 노후의 건강증진 및 생활안정을 도모하고 그 가족의 부담을 덜어줌으로써 삶의 질을 향상시키고자 한다. ")));

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+38,'제2조(계약기간)');
	
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+40, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 계약기간은 ".$from_year."년 ".$from_month."월 ".$from_day."일부터 ".$to_year."년 ".$to_month."월 ".$to_day."일까지로 한다.\n② 제1항의 계약기간은 당사자 간의 협의에 따라 변경할 수 있다.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+61,"제3조(급여범위)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+64, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "방문목욕급여는 장기요양요원이 '갑'  의 가정 등을 방문하여 신체활동 및 가사활동 등을 지원하는 장기요양급여로 한다.")));
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+85,"제4조(급여이용 및 제공)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+88, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 방문목욕급여 이용 및 제공은 장기요양급여 이용(제공)계획서에 의한다.\n② '갑'의 방문목욕급여 이용시간은 아래와 같이 한다. 다만, 방문목욕급여는 주 1회까지 이\n     용이 가능하며 변실금 및 요실금 등으로 인하여 피부의 건강유지관리가 불가피한 경우에는\n    초과하여 이용할 수 있다.");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left, $pdf->getY()+120);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "이용일", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용시간", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용방법/월", 1, 1, "C");
	
	if ($ct['use_type'] == '1'){
		//이용방법(매주)
		$pos_x = 134;
		$pos_y = 138;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '2'){
		//이용방법(격주)
		$pos_x = 147;
		$pos_y = 138;
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '3'){
		//이용방법(매월)
		$pos_x = 160;
		$pos_y = 138;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}else if ($ct['use_type'] == '4'){
		//이용방법(월3회)
		$pos_x = 172.5;
		$pos_y = 138;	
		$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "(".$yoil1.", ".$yoil2.") 요일", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, $from_time." ~ ".$to_time, 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "□매주 □격주 □매월 □월3회", 1, 1, "C");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+6, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"③ '을' 은 '갑' 에게 방문목욕급여를 제공할 때에 반드시 2명의 요양보호사를 배치한다.\n④ '을' 은 익월 장기요양급여 제공을 하고자하는 경우에는 '갑' (또는 '을')과 협의하여  급여\n     급여개시 전까지 급여계획서를 작성하고 수급자(보호자)확인받아 급여서비스를 실시한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+40,'제5조(계약자 의무)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+43, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 월 이용료 납부\n   2. 방문목욕급여 범위내 급여이용\n   3. 장기요양급여 이용수칙 준수\n4. 기타 '을' 과 협의한 규칙 이행\n② '을' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 방문목욕급여 제공 계약내용 준수\n   2. 급여제공 중 '갑'  에게 신병 이상이 생기는 경우 즉시 '병' 에게 통보\n   3. 급여제공시간에 '갑'  의 주변 및 집기류의 청결 및 유지관리\n   4. 급여제공 중 알게 된 '갑'  의 신상 및 질환 증에 관한 비밀유지\n  (단, 치료 등의 조치가 필요한 경우는 예외)\n   5. 이용상담, 지역사회 다른 서비스 이용 정보제공\n   6. 노인학대 예방 및 노인인권 보호 준수\n   7. 기타 '갑'  (또는 '병' )의 요청에 협조");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"③ '병' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. '갑'  에 관한 건강 및 필요한 자료제공\n   2. '갑'  의 월 이용료 등 비용 부담\n   3. 인적 사항 및 장기요양보험 등급 변경 시 즉시 '을' 에게 통보\n   4. '갑'  에 대한 의무이행이 어려울시 대리인 선정 및 '을' 에게 통보\n   5. 기타 '을' 의 협조요청 이행");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+50,'제6조(계약해지 요건)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+53, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  (또는 '병' )은 다음 각호에 해당되는 경우에는 '을' 과 협의하여 계약을 해지 할\n     수 있다.
	1. 제2조의 계약기간이 만료된 경우
	2. 제3조의 방문목욕급여 범위에 해당하는 서비스를 이행하지 아니한 경우
	3. 제4조제2항의 방문목욕급여 제공시간을'갑'  (또는'병' )의 동의 없이 '을' 이 임의\n     로 변경하거나 배치된 장기요양요원을 임의로 변경 했을 경우
	4. 기타 '갑'  의 계약해지 사유가 발생한 경우\n② '을' 은 다음 각호에 해당되는 경우에는 '갑'  (또는 '병' )과 협의하여 계약을 해지 할\n     수 있다.
	1. 제2조의 계약기간이 만료되거나 사망한 경우
	2. '갑'  이 장기요양보험 등급외자로 등급변경이 발생한 경우
	3. '갑'  의 건강진단 결과「감염병의예방및관리에대한법률」에 따른 감염병 환자로서 감\n     염의 위험성이 있는 경우로 판정될 때
	4. '갑'  의 건강상의 이유로 서비스 이용이 어려울 때
	5. 이용계약시 제시된 이용안내를 '갑'  이 정당한 이유 없이 따르지 않는 등 서비스 제공에\n     심각한 지장을 줄 때
	6. '갑'  이 월 5회 이상 무단으로 방문목욕급여 이용시간과 장소를 지키지 아니하였을 때");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+165,'제7조(계약의 해지)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+168, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  (또는'병' )은 제6조제1항의 계약해지 요건이 발생한 경우에는 해당일 또는 계약기\n    간 만료일전에 별지 제2호서식의 장기요양급여 종결 신청서를 제출하여야 한다. 다만, 기타\n    부득이한 경우에는 우선 유선으로 할 수 있다.\n② '을' 은 제6조제2항에 의한 계약해지 요건이 발생한 경우에는 계약해지 의사를 별지 제2호\n    서식의 장기요양급여 종결안내서 및 관련 증빙서류와 함께 '갑'  과 '병' 에게 통보하고\n    충분히 설명해야 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+212,'제8조(이용료 납부)');
	
	if($code == '32817000163' || //천사방문
	   $code == '34812000297' ){ //아름노인
		$str = '노인장기요양보험법 시행규칙[별지 제24호서식]의 장기요양급여비용명세서 또는 노인장기요양보험법 시행규칙[별지 제3호서식]의 장기요양급여 이용료 세부내역서';
		$str2 = '별지제4호서식의 장기요양급여 납부확인서 또는 장기요양급여비용(본인부담금)영수증을';
	}else {
		$str = '노인장기요양보험법 시행규칙[별지 제3호서식]의 장기요양급여 이용료 세부내역서';
		$str2 = '별지제4호서식의 장기요양급여 납부확인서를';
	}

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+215, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 방문목욕급여비용 및 본인부담 기준은 별표 1과 같다.\n② '을' 은 전월 1일부터 말일까지의 이용료를 매월 ".$pay_day1."일에 정산하고 '갑'  (또는 '병' )에게 ".$pay_day2."일까지 ".$str."를 통보한다.\n③ '갑'  은 매월 ".$pay_day3."일까지 ".$bank." 본인부담금을 납부 한다. 다만, 납부일이 공휴일인 경우에는 그 익일로 한다.\n④ '을' 은 '갑'  이 납부한 비용에 대해서는 ".$str2." 발급한다.");
	
	/*
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+256,'제9조(재계약)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+258, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"다음 각호에 해당하는 경우에는 이를 반영한 계약서를 재작성한다.");
	*/

	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'제9조(재계약)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.18, 'y'=>$pdf->GetY()+1, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"다음 각호에 해당하는 경우에는 이를 반영한 계약서를 재작성한다.");

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"   1. 제2조의 계약기간이 만료된 경우
   2. 장기요양 인정등급이 변경된 경우
   3. 방문목욕 급여비용 및 본인부담 비용이 변경된 경우
   4. 기타 '갑'  과 '을' 이 필요한 경우");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+40,'제10조(건강관리)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+43, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"①'을' 은'갑'  의 건강 및 감염병 예방을 위하여 종사자들에게 연 1회 이상 건강진단을 실시\n      하여야 한다.\n②'을' 은 장기요양요원이 방문목욕급여 제공도중 '갑'  에게 상해를 입혔을 경우 적절한 조\n      치를 취해야 한다.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+75,'제11조(위급 시 조치)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+78, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"①'을' 은 방문목욕급여 제공시간에 '갑'  의 생명이 위급한 상태라고 판단된 때에는 '갑'  (\n      또는 '병' )이 지정한 병원 또는 관련 의료기관으로 즉시 후송하고 '병' 에게 즉시 통보\n      하여야 한다.\n②'병' 은 제1항의 규정에 의한 통보를 받았을 때에는 신속하게 대처하여야 한다. 다만, 대처\n      가 어려울 경우에는 우선 진료를 받을 수 있도록 조치하여야 한다.\n③'갑'  이 서비스 이용도중 사망하였을 경우'을' 은 즉시'병' 에게 통보한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+122,'제12조(개인정보 보호의무)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+125, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  은 본인의 개인정보에 대해 알 권리가 있다.\n② '을' 은 '갑'  의 개인정보를 관계규정에 따라 보호하여야 한다.\n③ '을' 은 장기요양서비스 제공에 필요한 '갑'  의 개인 정보 자료를 수집하고 활용하며 동\n      자료를 노인장기요양보험 운영주체 등에게 관계규정에 따라 제출할 수 있다.\n④ '을' 은 개인정보수집 및 활용을 하고자 하는 경우에는 '갑'  에게 별지 제5호서식의 개인\n      정보제공 및 활용 동의서를 받아야 한다.\n⑤ '을' 은 '갑'  의 사생활을 존중하고, 업무상 알게 된 개인정보는 철저히 비밀을 보장한다. ");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+175,'제13조(기록 및 공개)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+178, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"'을' 은 '갑'  의 생활과 장기요양서비스에 관한 모든 내용을 상세히 관찰하여 정확히 기록하고, '갑'  (또는 '병' )이 요구할 경우에는 표준양식에 의거한 기록을 공개하여야 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+200,'제14조(배상책임)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+203, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '을' 은 다음 각호의 경우에는'갑'  (또는'병' )에게 배상의무가 있으며 배상책임은 관\n      계규정에 따른다.\n  1. 장기요양요원(또는 '을' )의 고의나 과실로 인하여 '갑'  을 부상케 하는 등 건강을 상하\n     게 하거나 사망에 이르게 하였을 때
	2. 장기요양요원(또는 '을' )의 학대(노인복지법 제1조의2 제4호의 노인학대 및 같은 법\n     제39조의9의 금지행위를 말한다)로 인하여 '갑'  의 건강을 상하게 하거나, 사망에 이르게\n     하였을 때\n② 다음 각 호에 해당되는 경우에는 '갑'  (또는 '병' )은 '을' 에게 배상을 요구할 수 없다.
	1. 자연사 또는 질환에 의하여 사망 하였을 때
	2.'을' 이 선량한 주의의무를 다했음에도 임의로 외출하여 상해를 당했거나 사망 하였을 때");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"
	3. 천재지변으로 인하여 상해를 당했거나 사망 하였을 때
	4. '갑'  의 고의 또는 중과실로 인하여 상해를 당했거나 사망하였을 때");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+33,'제15조(기타)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+36, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 이 계약서에서 규정하지 않은 사항은 민법이나 사회상규에 따른다.\n② 부득이한 사정으로 소송이 제기될 경우 '갑'  (또는 '병' ) 또는 시설이 속한 소재지역의\n    관할법원으로 한다.");
	
	if($ct['other_text2'] != ''){
		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
		$pdf->Text($pdf->left,$pdf->getY()+65,'제17조(별첨사항)');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+69, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"위에 기술되지 않은 특이사항은 #별첨1 에 정의 되어 있다.");
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+85, 'font_size'=>15, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  위와 같이 계약을 체결하고 본 계약체결을 증명하기 위하여 쌍방이 계약서를 작성 날인 후 각각 1부씩 보관키로 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+45,$pdf->GetY()+135);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, substr($ct['reg_dt'],0,4)."년", 0, 0, "R");
	$pdf->Cell($pdf->width*0.125, $pdf->row_height, (substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2))."월", 0, 0, "R");
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
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+22, '20', '20');	//고객 서명
	}

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+30);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'갑'  이용자", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['name']."   (인)", 0, 1, "R");
	
	$pdf->SetXY($pdf->left+70,$pdf->GetY()+10);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "'병'  대리인", 0, 0, "R");
	$pdf->Cell($pdf->width*0.38, $pdf->row_height, $su['bohoName']."   (인)", 0, 1, "R");
	

	set_array_text($pdf, $pos);
	unset($pos);

	if($ct['other_text2'] != ''){
		$pdf->MY_ADDPAGE();

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 5);
		$pdf->Text($pdf->left,$pdf->getY()+15,'#별첨1');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+22, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes($ct['other_text2']));
		set_array_text($pdf, $pos);
		unset($pos);
	}
	
	

	$pdf->MY_ADDPAGE();
	

	$from_dt = $from_dt != '' ? $from_dt : date('Y-m-d');
	$to_dt   = $to_dt != '' ? $to_dt : date('Y-m-d');
	
	$agreeDt = $ct['reg_dt'] != '' ? $ct['reg_dt'] : '';
	$ct['reg_dt'] = $ct['reg_dt'] != '' ? $ct['reg_dt'] : date('Y-m-d');
	

	$sql = 'select m11_mcode2 as mcode
			,	   m11_suga_value as suga_val
			,	   m11_sdate as from_dt   
			  from m11suga
			 where m11_mcode = \'goodeos\'
			   and m11_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m11_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'
			 union all
			select m01_mcode2 as mcode
			,	   m01_suga_value as suga_val
			,	   m01_sdate as from_dt     
			  from m01suga
			 where m01_mcode = \'goodeos\'
			   and m01_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m01_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'';
	
	$conn -> query($sql); 
	$conn -> fetch();
	$rowCount = $conn -> row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn -> select_row($i);

		$arr[$row['mcode']]['val'] = $row['suga_val'];
		$arr[$row['mcode']]['date'] = $row['from_dt'];
		
	}
	

	$sql = 'select m91_sdate, m91_code, m91_kupyeo
			  from m91maxkupyeo
			 where m91_sdate <= \''.str_replace('-','',$ct['reg_dt']).'\'
			   and m91_edate >= \''.str_replace('-','',$ct['reg_dt']).'\'';
			
	$conn -> query($sql); 
	$conn -> fetch();
	$rowCount = $conn -> row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn -> select_row($i);

		$maxPay[$row['m91_code']]['m91_kupyeo'] = $row['m91_kupyeo'];
		$maxPay[$row['m91_code']]['date'] = $row['m91_sdate'];
		
	}

	$pdf->SetXY($pdf->left+5, $pdf->top+9);
	$pdf->SetFont('바탕','',11);
	$pdf->Cell(150,5,'[별표 1]',0,1,'L');


	$pdf->SetFont($pdf->font_name_kor, "B", 15);
	$pdf->SetXY($pdf->left, $pdf->top+25);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "방문목욕 급여비용 및 본인부담 기준(".$myF->dateStyle($maxPay[$row['m91_code']]['date'],'.').")", 0, 1, "C");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "○ 방문목욕의 1회당 이용시간별 급여비용");
	
	$rowH = $pdf->row_height+3;
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "분 류", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "금액(원)", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "방문목욕 차량을 이용한 경우(차량내 목욕)", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBKD1']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "방문목욕 차량을 이용한 경우(가정내 목욕)", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBKD2']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "방문목욕 차량을 이용하지 아니한 경우", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CBFD1']['val']), 1, 1, "C");


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "○ 등급별 재가급여 월 한도액(원)");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.20, $rowH, "1등급", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "2등급", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "3등급", 1, 0, "C", true);
	$pdf->Cell($pdf->width*0.20, $rowH, "4등급", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.20, $rowH, "5등급", 1, 1, "C", true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['1']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['2']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['3']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['4']['m91_kupyeo']), 1, 0, "C");
	$pdf->Cell($pdf->width*0.20, $rowH, number_format($maxPay['5']['m91_kupyeo']), 1, 1, "C");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size -1);
	$pdf->SetXY($pdf->left, $pdf->GetY()+3);
	$pdf->MultiCell($pdf->width, $pdf->row_height, "※ 요양급여비용은 매년 장기요양위원회(위원장 : 보건복지부 차관)가 결정, 고시하는 장기요양급여비용 등에\n     관한 고시(보건복지부 고시)에 따름");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "○ 수급자 자격별 급여비용 본인일부부담 비율");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);


	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "구분", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "재가급여", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "일반", 1, 0, "L");
	$pdf->Cell($pdf->width*0.35, $rowH, "15%", 1, 1, "C");

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "기초수급권자", 1, 0, "L");
	$pdf->Cell($pdf->width*0.35, $rowH, "0%", 1, 1, "C");

	$orgY = $pdf->GetY();
	
	if($maxPay[$row['m91_code']]['date'] >= '20180101' && $maxPay[$row['m91_code']]['date'] < '20190101'){
		$pdf->SetXY($pdf->left+$pdf->width*0.65, $orgY+6);
		$pdf->MultiCell($pdf->width*0.35, $pdf->row_height, "7.5%(2018.07.31까지)\n6%, 9%(2018.08.01부터)",'','C');

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "", 1, 1, "C");
	}else if($maxPay[$row['m91_code']]['date'] >= '20190101'){

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "6%, 9%", 1, 1, "C");
	}else {

		$pdf->SetXY($pdf->left, $orgY);
		$pdf->Cell($pdf->width*0.65, $rowH*3, "", 1, 0, "L");
		$pdf->Cell($pdf->width*0.35, $rowH*3, "7.5%", 1, 1, "C");
	}
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()-27);
	$pdf->MultiCell($pdf->width*0.65, $pdf->row_height, "기타 의료수급권자\n차상위 의료급여 건강보험 자격전환자 (희귀난치성, 만성질환자)\n 저소득층 (본인일부부담금 감경을 위한 소득.재산 등이 일정금액 이하인 자에 관한 고시 해당자)");
	
	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+200, '20', '20');	//고객 서명
	}


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
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, "(생년월일 :    ".$myF->issToBirthday($jumin,'.'), 0, 0, "C");
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
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
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
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
					,      use_yoil1_nurse
					,      from_time1_nurse
					,      to_time1_nurse
					,      use_yoil2_nurse
					,      from_time2_nurse
					,      to_time2_nurse
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
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
					,      use_yoil1_nurse
					,      from_time1_nurse
					,      to_time1_nurse
					,      use_yoil2_nurse
					,      from_time2_nurse
					,      to_time2_nurse
					,	   pay_day1
					,	   pay_day2
					,	   pay_day3
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
	
	//직인
	$jikin = $center['jikin'];

	if($ct['seq'] == 0 && $code == '34311000305'){
		//행복한재가 
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
	$use_yoil1 = $ct['use_yoil1_nurse'];
	$use_yoil2 = $ct['use_yoil2_nurse'];
	
	//이용시간
	$fm_h1 = $ct['from_time1_nurse'] != '' ? substr($ct['from_time1_nurse'],0,2) : '     ';
	$fm_m1 = $ct['from_time1_nurse'] != '' ? substr($ct['from_time1_nurse'],2,2) : '     ';
	$to_h1 = $ct['to_time1_nurse'] != '' ? substr($ct['to_time1_nurse'],0,2) : '     ';
	$to_m1 = $ct['to_time1_nurse'] != '' ? substr($ct['to_time1_nurse'],2,2) : '     ';
	$fm_h2 = $ct['from_time2_nurse'] != '' ? substr($ct['from_time2_nurse'],0,2) : '     ';
	$fm_m2 = $ct['from_time2_nurse'] != '' ? substr($ct['from_time2_nurse'],2,2) : '     ';
	$to_h2 = $ct['to_time2_nurse'] != '' ? substr($ct['to_time2_nurse'],0,2) : '     ';
	$to_m2 = $ct['to_time2_nurse'] != '' ? substr($ct['to_time2_nurse'],2,2) : '     ';

	$pay_day1  = $ct['pay_day1'] != '' ? $ct['pay_day1'] : '말';
	$pay_day2  = $ct['pay_day2'] != '' ? $ct['pay_day2'] : '5';
	$pay_day3  = $ct['pay_day3'] != '' ? $ct['pay_day3'] : '15';	//본인부담금납부일

	$pdf->MY_ADDPAGE();
	
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg';

	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 93, $pdf->getY()+115, '20', '20');	//고객 서명
	}


	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+12);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height*1.5, "장기요양급여 이용 표준약관\n(방문간호)", 1,"C");
	
	
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
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
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
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.01 , 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
		}else {
			$pos[sizeof($pos)] = array('x'=>$pdf->left * 4.8, 'y'=>$pdf->GetY()*1.02 , 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width * 0.24, 'height'=>4.5, 'align'=>'L', 'text'=>$center['centerName']);
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
	$pdf->Cell($pdf->width*0.24, $pdf->row_height*2, " (인)", 1, 0, "R");
	$pdf->Cell($pdf->width*0.14, $pdf->row_height*2, "관 계", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.28, $pdf->row_height*2, $su['gwange'], 1, 1, "C");
	
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
	

	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+12, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "고령이나 노인성질병 등으로 인하여 혼자서 일상생활을 수행하기 어려운 노인들 중 장기요양등급을 받은 분들에게 방문간호급여를 제공하여 노후의 건강증진 및 생활안정을 도모하고 그 가족의 부담을 덜어줌으로써 삶의 질을 향상시키고자 한다. ")));

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+38,'제2조(계약기간)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+40, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 계약기간은 ".$from_year."년 ".$from_month."월 ".$from_day."일부터 ".$to_year."년 ".$to_month."월 ".$to_day."일까지로 한다.\n② 제1항의 계약기간은 당사자 간의 협의에 따라 변경할 수 있다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+61,"제3조(급여범위)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+64, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes(str_replace(chr(13).chr(10), "\n", "방문간호급여는 장기요양요원인 간호사 등이 의사, 한의사 또는 치과의사의 지시서에 따라 수급자의 가정 등을 방문하여 간호 진료의 보조, 요양에 관한 상담 또는 구강위생 등을 제공하는 장기요양급여로 한다.")));
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+89,"제4조(급여이용 및 제공)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+92, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 방문간호급여 이용 및 제공은 장기요양급여 이용(제공)계획서에 의한다.\n② '갑'  의 방문간호급여 이용시간은 아래와 같이 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY()+113, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.2, 'height'=>6, 'align'=>'C', 'text'=>"이용\n시간\n(1)");

	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY()+137, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.2, 'height'=>6, 'align'=>'C', 'text'=>"이용\n시간\n(2)");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+116.5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>"□월  □화  □수  □목\n □금  □토  □일");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.1, 'y'=>$pdf->GetY()+140.5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>"□월  □화  □수  □목\n □금  □토  □일");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+118, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>$fm_h1."시 ".$fm_m1."분    ~".$to_h1."시 ".$to_m1."분");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.5, 'y'=>$pdf->GetY()+142, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width*0.6, 'height'=>6, 'align'=>'C', 'text'=>$fm_h2."시 ".$fm_m2."분    ~".$to_h2."시 ".$to_m2."분");

	$pdf->SetXY($pdf->left, $pdf->getY()+105);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height, "구분", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용일", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height, "이용시간", 1, 1, "C" ,true);
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*4, "", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.2, $pdf->row_height*4, "", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 0, "C");
	$pdf->Cell($pdf->width*0.4, $pdf->row_height*4, "", 1, 1, "C");
	
	$pos_x = 67;
	$pos_y = 128; 
	
	
	for($i=0; $i<strlen($use_yoil1); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 6;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil1[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}

	$pos_x = 67;
	$pos_y = 152; 
	
	for($i=0; $i<strlen($use_yoil2); $i++){
		if ($i > 0){
			if ($i % 4 == 0){
				$pos_x = 72.7;
				$pos_y += 6;
			}else{
				$pos_x += 11;
			}
		}

		if($use_yoil2[$i] == 'Y'){
			$pdf->Image('../image/check.gif',$pos_x,$pos_y,'gif');
		}
	}
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+2, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"※ 요일에 따라 이용시간이 다른 경우 이용시간 기재란을 늘려서 기록함\n※ '갑'   또는 '병' 은 사정에 의해 일시적으로 이용시간을 지키기 어려운 경우 서비스  이용\n     시작 최소 1시간 전에 '을' 에게 연락을 취해야 함.");
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+25, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"③ 관공서의 공휴일에 관한 규정’에 의한 공휴일에 급여를 제공하는 경우에는 '을' 은\n     30%의 할증비용을 청구할 수 있다.\n④ 야간(18:00~22:00), 심야(22:00~06:00)에 급여를 제공하는 경우에는 '을' 은 야간\n     20%, 심야 30%의 할증 비용을 청구할 수 있다.\n⑤ 야간심야휴일가산이 동시에 적용되는 경우에는 중복하여 가산하지 않는다.\n⑥ '을' 은 익월 장기요양급여 제공을 하고자하는 경우에는'갑'  (또는 '병' )과 협의하여\n     급여개시 전까지 급여계획서를 작성하고 수급자(보호자)확인받아 급여서비스를\n     실시한다.");
		//당월 ?일까지 별지 제1호서식의 장기요양급여 이용계획서를 작성하고 장기요양급여 이용\n     계획서에 따라 장기요양급여를 제공 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+80,'제5조(계약자 의무)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+83, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 월 이용료 납부\n   2. 방문간호급여 범위내 급여이용\n   3. 장기요양급여 이용수칙 준수\n");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+5, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"   4. 기타 '을' 과 협의한 규칙 이행\n② '을' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. 방문간호급여 제공 계약내용 준수\n   2. 급여제공 중 '갑'  에게 신병 이상이 생기는 경우 즉시 '병' 에게 통보\n   3. 급여제공시간에 '갑'  의 주변 및 집기류의 청결 및 유지관리\n   4. 급여제공 중 알게 된 '갑'  의 신상 및 질환 증에 관한 비밀유지\n  (단, 치료 등의 조치가 필요한 경우는 예외)\n   5. 이용상담, 지역사회 다른 서비스 이용 정보제공\n   6. 노인학대 예방 및 노인인권 보호 준수\n   7. 기타 '갑'  (또는 '병' )의 요청에 협조\n③ '병' 은 다음 각 호를 성실하게 이행하여야 한다.\n   1. '갑'  에 관한 건강 및 필요한 자료제공\n   2. '갑'  의 월 이용료 등 비용 부담\n   3. 인적 사항 및 장기요양보험 등급 변경 시 즉시 '을' 에게 통보\n   4. '갑'  에 대한 의무이행이 어려울시 대리인 선정 및 '을' 에게 통보\n   5. 기타 '을' 의 협조요청 이행");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+109,'제6조(계약해지 요건)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+113, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  (또는 '병' )은 다음 각호에 해당되는 경우에는 '을' 과 협의하여 계약을 해지 할\n      수 있다.
	1. 제2조의 계약기간이 만료된 경우
	2. 제3조의 방문간호급여 범위에 해당하는 서비스를 이행하지 아니한 경우
	3. 제4조제2항의 방문간호급여 제공시간을'갑'  (또는'병' )의 동의 없이 '을' 이 임의\n     로 변경하거나 배치된 장기요양요원을 임의로 변경 했을 경우
	4. 기타 '갑'  의 계약해지 사유가 발생한 경우\n② '을' 은 다음 각호에 해당되는 경우에는 '갑'  (또는 '병' )과 협의하여 계약을 해지 할\n       수 있다.
	1. 제2조의 계약기간이 만료되거나 사망한 경우
	2. '갑'  이 장기요양보험 등급외자로 등급변경이 발생한 경우
	3. '갑'  의 건강진단 결과「감염병의예방및관리에대한법률」에 따른 감염병 환자로서 감\n     염의 위험성이 있는 경우로 판정될 때
	4. '갑'  의 건강상의 이유로 서비스 이용이 어려울 때
	5. 이용계약시 제시된 이용안내를 '갑'  이 정당한 이유 없이 따르지 않는 등 서비스 제공에\n     심각한 지장을 줄 때
	6. '갑'  이 월 5회 이상 무단으로 방문간호급여 이용시간과 장소를 지키지 아니하였을 때");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+223,'제7조(계약의 해지)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+225, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  (또는'병' )은 제6조제1항의 계약해지 요건이 발생한 경우에는 해당일 또는 계약기\n    간 만료일전에 별지 제2호서식의 장기요양급여 종결 신청서를 제출하여야 한다. 다만, 기타\n    부득이한 경우에는 우선 유선으로 할 수 있다.\n② '을' 은 제6조제2항에 의한 계약해지 요건이 발생한 경우에는 계약해지 의사를 별지 제2호\n    서식의 장기요양급여 종결안내서 및 관련 증빙서류와 함께 '갑'  과 '병' 에게 통보하고\n    충분히 설명해야 한다.");
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'제8조(이용료 납부)');
	
	if($code == '32817000163' || //천사방문
	   $code == '34812000297' ){ //아름노인
		$str = '노인장기요양보험법 시행규칙[별지 제24호서식]의 장기요양급여비용명세서 또는 노인장기요양보험법 시행규칙[별지 제3호서식]의 장기요양급여 이용료 세부내역서';
		$str2 = '별지제4호서식의 장기요양급여 납부확인서 또는 장기요양급여비용(본인부담금)영수증을';
	}else {
		$str = '노인장기요양보험법 시행규칙[별지 제3호서식]의 장기요양급여 이용료 세부내역서';
		$str2 = '별지제4호서식의 장기요양급여 납부확인서를';
	}


	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 방문간호급여비용 및 본인부담 기준은 별표 1과 같다.\n② '을' 은 전월 1일부터 말일까지의 이용료를 매월  ".$pay_day1."일에 정산하고 '갑'  (또는 '병' )에게  ".$pay_day2."일까지 ".$str."를 통보한다.\n③ '갑'  은 매월 ".$pay_day3."일까지 ".$bank."본인부담금을 납부 한다. 다만, 납부일이 공휴일인 경우에는 그 익일로 한다.\n④ '을' 은 '갑'  이 납부한 비용에 대해서는 ".$str2." 발급한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+63,'제9조(재계약)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+66, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"다음 각호에 해당하는 경우에는 이를 반영한 계약서를 재작성한다.
   1. 제2조의 계약기간이 만료된 경우
   2. 장기요양 인정등급이 변경된 경우
   3. 방문간호 급여비용 및 본인부담 비용이 변경된 경우
   4. 기타 '갑'  과 '을' 이 필요한 경우");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+102,'제10조(건강관리)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+105, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"①'을' 은'갑'  의 건강 및 감염병 예방을 위하여 종사자들에게 연 1회 이상 건강진단을 실시\n    하여야 한다.\n②'을' 은 장기요양요원이 방문간호급여 제공도중 '갑'  에게 상해를 입혔을 경우 적절한 조\n      치를 취해야 한다.");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+135,'제11조(위급 시 조치)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+137, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '을' 은 방문간호급여 제공시간에 '갑'  의 생명이 위급한 상태라고 판단된 때에는 '갑'  (\n     또는 '병' )이 지정한 병원 또는 관련 의료기관으로 즉시 후송하고 '병' 에게 즉시 통보\n      하여야 한다.\n② '병' 은 제1항의 규정에 의한 통보를 받았을 때에는 신속하게 대처하여야 한다. 다만, 대처\n      가 어려울 경우에는 우선 진료를 받을 수 있도록 조치하여야 한다.\n③ '갑'  이 서비스 이용도중 사망하였을 경우'을' 은 즉시'병' 에게 통보한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+181,'제12조(개인정보 보호의무)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+184, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '갑'  은 본인의 개인정보에 대해 알 권리가 있다.\n② '을' 은 '갑'  의 개인정보를 관계규정에 따라 보호하여야 한다.\n③ '을' 은 장기요양서비스 제공에 필요한 '갑'  의 개인 정보 자료를 수집하고 활용하며 동\n      자료를 노인장기요양보험 운영주체 등에게 관계규정에 따라 제출할 수 있다.\n④ '을' 은 개인정보수집 및 활용을 하고자 하는 경우에는 '갑'  에게 별지 제5호서식의 개인\n      정보제공 및 활용 동의서를 받아야 한다.\n⑤ '을' 은 '갑'  의 사생활을 존중하고, 업무상 알게 된 개인정보는 철저히 비밀을 보장한다. ");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+234,'제13조(기록 및 공개)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+237, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"'을' 은 '갑'  의 생활과 장기요양서비스에 관한 모든 내용을 상세히 관찰하여 정확히 기록하고, '갑'  (또는 '병' )이 요구할 경우에는 표준양식에 의거한 기록을 공개하여야 한다.");
	
	
	set_array_text($pdf, $pos);
	unset($pos);

	$pdf->MY_ADDPAGE();
	

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+5,'제14조(배상책임)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+8, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① '을' 은 다음 각호의 경우에는'갑'  (또는'병' )에게 배상의무가 있으며 배상책임은 관\n      계규정에 따른다.\n 1. 장기요양요원(또는 '을' )의 고의나 과실로 인하여 '갑'  을 부상케 하는 등 건강을 상하\n     게 하거나 사망에 이르게 하였을 때
	2. 장기요양요원(또는 '을' )의 학대(노인복지법 제1조의2 제4호의 노인학대 및 같은 법\n     제39조의9의 금지행위를 말한다)로 인하여 '갑'  의 건강을 상하게 하거나, 사망에 이르게\n     하였을 때\n② 다음 각 호에 해당되는 경우에는 '갑'  (또는 '병' )은 '을' 에게 배상을 요구할 수 없다.
	1. 자연사 또는 질환에 의하여 사망 하였을 때
	2.'을' 이 선량한 주의의무를 다했음에도 임의로 외출하여 상해를 당했거나 사망 하였을 때
	3. 천재지변으로 인하여 상해를 당했거나 사망 하였을 때
	4. '갑'  의 고의 또는 중과실로 인하여 상해를 당했거나 사망하였을 때");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 2);
	$pdf->Text($pdf->left,$pdf->getY()+89,'제15조(기타)');
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+92, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>"① 이 계약서에서 규정하지 않은 사항은 민법이나 사회상규에 따른다.\n② 부득이한 사정으로 소송이 제기될 경우 '갑'  (또는 '병' ) 또는 시설이 속한 소재지역의\n    관할법원으로 한다.");
	
	
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+121, 'font_size'=>15, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>8, 'align'=>'L', 'font_bold'=>'B', 'text'=>"  위와 같이 계약을 체결하고 본 계약체결을 증명하기 위하여 쌍방이 계약서를 작성 날인 후 각각 1부씩 보관키로 한다.");
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 4);
	$pdf->SetXY($pdf->left+45,$pdf->GetY()+151);
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
	
	/*
	if($ct['other_text1'] != ''){
		$pdf->MY_ADDPAGE();

		$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 5);
		$pdf->Text($pdf->left,$pdf->getY()+15,'#별첨1');
		
		$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.005, 'y'=>$pdf->GetY()+22, 'font_size'=>12, 'type'=>'multi_text', 'width'=>$pdf->width, 'height'=>6, 'align'=>'L', 'text'=>stripslashes($ct['other_text1']));
		set_array_text($pdf, $pos);
		unset($pos);
	}
	*/
	
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
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "방문간호 급여비용 및 본인부담 기준(".$myF->dateStyle($maxPay[$row['m91_code']]['date'],'.').")", 0, 1, "C");

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 1.5);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "○ 방문간호의 1회당 이용시간별 급여비용");
	
	$rowH = $pdf->row_height+3;
	
	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size);

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width*0.65, $rowH, "분 류", 1, 0, "C" ,true);
	$pdf->Cell($pdf->width*0.35, $rowH, "금액(원)", 1, 1, "C" ,true);
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "30분 미만", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS1']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "30분 이상 ~ 60분 미만", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS2']['val']), 1, 1, "C");
	
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width*0.65, $rowH, "60분 이상", 1, 0, "C");
	$pdf->Cell($pdf->width*0.35, $rowH, number_format($arr['CNWS3']['val']), 1, 1, "C");


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
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $juso[0] , 0, 0, "L");
	
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
	
	
	

	/*

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"제1조(목적)\n  사업자는 이용자에 대해 요양보험 법령의 취지에 따라 이용자가 가능한 한 그 주택에 대하여 그 가지는 능력에 따라 자립한 일상생활을 영위할 수가 있도록 이용자의 요양 생활을 지원하여 심신의 기능 유지 회복을 목표로 하는 것을 목적으로 방문 간호 서비스를 제공하며 이용자는 사업자에 대해 그 서비스에 대한 요금을 지불합니다.
	
	제2조 (계약기간)
	  1. 이 계약의 계약기간은  ".$from_year." 년  ".$from_month." 월 ".$from_day." 일부터 이용자의 요양 간호 인정의 유효          기간 만료일까지로 합니다.
	  2. 계약 만료의 2일전까지 이용자로부터 사업자에 대해서, 문서에 의한 계약 종료의 신청이          없는 경우 계약은 자동 갱신되는 것으로 합니다.
	  
	제3조 (방문간호 계획의 작성·변경)
	  1. 사업자는 이용자와 관련되는 표준장기이용계획이 작성되어 있는 경우에는 거기에 따라           이용자의 방문 간호 계획을 작성하는 것으로 합니다.
	  2. 사업자는 주치의의 지시 이용자의 일상생활 전반의 상황 및 희망을 근거로 하여 「방문           간호 계획」을 작성합니다. 사업자는 이「방문 간호 계획」의 내용을 이용자 및 그 가족         에게 설명하여 그 동의를 얻는 것으로 합니다.
	  3. 사업자는 다음의 어느 쪽인가에 해당하는 경우에는 제1조에 규정 하는 방문 간호 서비스         의 목적에 따라 방문 간호 계획의 변경을 실시합니다.
	   ①이용자의 심신의 상황, 그 처해져 있는 환경 등의 변화에 의해 해당 방문 간호 계획을 변          경할 필요가 있는 경우.
	   ②이용자가 방문 간호 서비스의 내용이나 제공 방법 등의 변경을 희망하는 경우.
	  4. 사업자는 방문 간호 계획을 변경했을 경우에는 이용자에 대해서 서면으로 교부하여 그 내        용을 확인하는 것으로 합니다.

   제4조 (주치의와의 관계)
	  1. 사업자는 방문 간호 서비스의 제공을 개시하려면 주치의의 지시를 문서로 받습니다.
	  2. 사업자는 주치의에 방문 간호 계획서 및 방문 간호 보고서를 제출하여 주치의와의 밀접한         제휴를 꾀합니다.

	제5조 (방문간호 서비스의 내용)
	  1. 이용자가 제공을 받는 방문 간호 서비스의 내용은【방문간호 지시서】에 정했던 바와 같습니다. 사업자는,【방문간호 지시서】에 정한 내용에 대하여 이용자 및 그 가족에게 설명합니다.

	제6조 (서비스 제공의 기록)
  1. 사업자는 방문 간호 서비스의 실시마다 서비스 내용 등을 서비스실시 기록부에 기입하고        서비스의 종료시에 이용자의 확인을 받는것으로 합니다. 그 기록은 이용자의 희망이 있으       면 언제라도 이용  자에게 교부합니다.
  2. 사업자는 서비스 실시 기록부를 작성하는 것으로 하며, 이 계약의 종료 후 2년간 보관합        니다.
  3. 이용자는 사업자의 영업 시간내에 그 사업소에서 해당 이용자에 관한 제2항의 서비스 실       시 기록부를 열람할 수 있습니다.
  4. 이용자는 희망이 있으면 언제라도 해당 이용자에 관한 제2항의 서비스 실시 기록부의 복       사물을교부 받을 수가 있습니다.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"제7조 (방문간호사의 교체 등)
  1. 이용자는 선임된 방문 간호사의 교체를 희망하는 경우에는 해당  방문 간호사가 업무상         부적당이라고 인정되는 사정 그 외 교체를 희망하는 이유를 분명히 하여 사업자에 대해서        방문 간호사의 교  체를 신청할 수가 있습니다.
  2. 사업자는 방문 간호사의 교체에 의해 이용자 및 그 가족 등에 대해서 서비스 이용상의 불        이익이 생기지 않게 충분히 배려하는 것으로 합니다.
  3. 방문 간호사가 컨디션 불량 등의 경우로 방문 할 수 없게 되었을 때에는 대체 인원을 인선       하고 인선 후 재차 이용자 및 그 가족에게 연락하겠습니다.

제8조 (요금)
  1. 이용자는 서비스의 대가로 【노인장기요양보험】에 정하는 이용 단위마다의 요금을 기본         으로 계산된 매월의 합계 금액을 지불합니다.
  2. 사업자는 당월 요금의 합계액의 청구서에 명세를 교부하여 다음 달 10일까지 이용자에게       송부합니다.
  3. 이용자는 당월 요금의 합계액을 다음달 15일까지 사업자의 지정하는 방법으로 지불합니        다.
  4. 이용자는 주택에 대해 서비스 종업원이 서비스 실시를 위해서 사용하는 수도, 가스, 전기,       전화의 비용을 부담합니다.

제9조 (서비스의 중지)
  1. 이용자는 사업자에 대해서 서비스 실시일의 전영업일 오후 6시까지 통지를 하는 것으로        써 요금을 부담하는 일 없이 서비스 이용을 중지할 수가 있습니다.
  
제10조 (요금의 변경)
  1. 사업자는 이용자에 대해서 1개월전까지 문서로 통지하는 것으로써 이용 단위마다의 요         금의 변경(증액 또는 감액)을 신청할 수가 있습니다.

제11조 (계약의 종료)
	 1. 이용자는 사업자에 대해서 1주간의 예고 기간을 두어 문서로 통지를 하는 것으로써, 이\n     계약을 해약할 수가 있습니다. 
     다만, 이용자의 병변, 갑작스러운 입원 등 어쩔 수 없는 사정이 있는 경우는 예고 기간이\n     1주간 이내의 통지에서도 이 계약을 해약할 수가 있습니다.
  2. 사업자는 부득이한 사정이 있는 경우 이용자에 대해서 1개월간의 예고 기간을 두어 이유       를 나타낸 문서로 통지하는 것으로써 이 계약을 해약할 수가 있습니다.
  3. 다음의 각 1항의 사정에 해당했을 경우는 이용자는 문서로 통지하는 것으로써 즉시 이\n     계약을 해약할 수가 있습니다.
    ①사업자가 정당한 이유 없게 서비스를 제공하지 않는 경우.
    ②사업자가 비밀을 지킬 의무에 반했을 경우
    ③사업자가 이용자나 그 가족 등에게 대해 사회 통념을 일탈하는 행위를 실시했을 경우
    ④사업자가 파산했을 경우
  4. 다음의 각1항의 사정에 해당했을 경우는 사업자는 문서로 통지하는 것으로써 즉시 이 계       약을 해약할 수가 있습니다.
    ①이용자의 서비스 이용요금의 지불이 3개월 이상 지연 해, 요금을 지불하도록 최고 했음         에도 불구하고 10일 이내에 지불되지 않는 경우
    ②이용자 또는 그 가족이 사업자나 서비스 종업원에 대해서, 이 계약을 계속 하기 어려울          만큼의 배신행위를 실시했을 경우.");

	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"     
  5. 다음의 각1항의 사정에 해당했을 경우는 이 계약은 자동적으로 종료합니다.
    ①이용자가 요양보험 시설에 입소 했을 경우
    ②이용자의 요양 간호 인정 구분이 등급외라고 인정되었을 경우.
    ③이용자가 사망했을 경우.
	
제12조 (비밀 보관 유지)
  1. 사업자 및 사업자의 사용하는 사람은 서비스 제공을 하는데 있어서 파악한 이용자 및 그        가족에 관한 비밀을 정당한 이유 없게 제삼자에게 흘리지 않습니다. 이 비밀을 지킬 의무        는 계약 종료 후도  같습니다.
  2. 사업자는 이용자 및 그 가족이 가지는 문제나 해결해야 할 과제 등에 대한 서비스 담당자        회의에 대해 정보를 공유하기 위해서 이용자 및 가족의 개인정보를 서비스 담당자 회의에      서 이용하는 것을 본 계약을 가지고 동의로 간주합니다.

제13조 (배상책임)
  사업자는 서비스의 제공에 따라 사업자의 고의의 사유에 의해 이용자의 생명·신체·재산에 손해를 미쳤을 경우는 이용자에 대해서 그 손해를 배상합니다.

제14조 (긴급시의 대응)
  사업자는 실제로 방문간호의 제공을 실시하고 있을 때 이용자의 병상의 급변이 생겼을 경우 그 외 필요한 경우는 신속하게 주치의 의사 또는 치과 의사에게 연락을 하는 등 필요한 조치를 강구합니다.

제15조 (신분증 휴대 의무)
  서비스 종업원은 항상 신분증을 휴대 해 첫 회 방문시 및 이용자 또는 이용자의 가족으로부터 제시가 요구되었을 때는 언제라도 신분증을 제시합니다.
제16조 (제휴)
  사업자는 방문간호의 제공에 요양지원전문원 및 보건의료 서비스 또는 복지서비스를 제공하는 사람과의 밀접한 제휴에 노력 합니다.

제17조 (상담·불평 대응)
  사업자는 이용자로부터의 상담, 불평 등에 대응하는 창구를 설치해 방문 간호에 관한 이용자의 요망, 불평등에 대해 신속히 대응합니다.

제18조 (본 계약에 정함이 없는 사항)
  1. 이용자 및 사업자는 신의성실을 가지고 이 계약을 이행하는 것으로 합니다.
  2. 본 계약에 정함이 없는 사항에 대해서는 요양보험 법령 그 외 제 법령이 정하는 것을 존중해 쌍방이 성의를 가져 협의 후 정합니다.

제19조 (재판관할)
  이 계약에 관해서 어쩔수 없이 소송이 되는 경우에 이용자와 사업자는 사업자의 주소지를 관할하는 재판소를 제１심 관할재판소로 하는 것에 미리 합의합니다.

  상기의 계약을 증명하기 위하여 본서 2통을 작성하고 이용자 및 사업자가 서명 날인한 후  각각 1통씩 보유하는 것으로 합니다.");
	
	$pdf->MY_ADDPAGE();

	$pdf->SetXY($pdf->width*0.38, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, '계약체결일' , 0, 0, "R");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "L");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[이용자]", 0, 1, "R");
		
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 80, $pdf->getY(), '20', '20');	//고객 서명
	}

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "성 명:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(인)", 0, 1, "L");

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"서명 대행자
      나는 본인의 계약 의사를 확인해 서명 대행하였습니다.");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"이용자와의 관계      
*주의：원칙으로서 부양자로 합니다.");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[사업자]", 0, 1, "R");
	
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "기관명:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, '  '.$center['centerName'], 0, 1, "L");
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 77, 110, '20', '20');	//기관 직인
		}
	}

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "대 표:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$center['manager'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(인)", 0, 1, "L");
	
	
	# 이용계약서
	
	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+200, '20', '20');	//고객 서명
	}

	$pdf->SetXY($pdf->left+5, $pdf->top+9);
	$pdf->SetFont('바탕','',11);
	$pdf->Cell(150,5,'[별지 제5호서식]',0,1,'L');

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+15, $pdf->width-10, $pdf->height-45);
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
	
	$pdf->SetX($pdf->left+5);
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
	*/


	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
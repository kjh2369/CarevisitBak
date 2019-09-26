<?
	include('../inc/_login.php');

	if (!Is_Array($var)){
		exit;
	}

	$code		= $_SESSION['userCenterCode'];
	
	$agreeDt    = date('Y');
	
	$conn->set_name('euckr');

	
	$code = $_SESSION['userCenterCode'];	//기관기호
	$kind = 0;								//서비스구분
	$ssn = $var['jumin'];					//수급자주민번호
	
	$sql =  ' select from_dt 
			  ,		 to_dt 
				from client_his_svc
			   where org_no = \''.$code.'\'
				 and jumin  = \''.$ssn.'\'
				 and svc_cd = \'0\'
			   order by seq desc
			   limit 1';
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
			 where m03_ccode = '".$code."'
			   and m03_mkind = '".$kind."'
			   and m03_jumin = '".$ssn."'
			   and m03_del_yn = 'N'";
	
	$su = $conn->get_array($sql);

	//기관정뵤
	$sql = 'SELECT	m00_store_nm	AS nm
			,		m00_mname		AS manager
			,		m00_ctel		AS phone
			,		m00_fax_no      AS fax
			,		m00_jikin		AS jikin
			,		m00_code1       AS code
			FROM	m00center
			WHERE	m00_mcode = \''.$code.'\'
			  AND   m00_del_yn = \'N\'
			LIMIT	1';

	$row	= $conn->get_array($sql);

	$center		= $row['nm'];
	$manager	= $row['manager'];
	$phone		= $myF->phoneStyle($row['phone'],'.');
	$fax		= $myF->phoneStyle($row['fax'],'.');
	$jikin		= $row['jikin'];


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+5, $pdf->width-10, $pdf->height-25);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 16);
	$pdf->SetXY($pdf->left, $pdf->top+10);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "표준장기요양이용계획서 제공 요청시 [   ] 장기요양기관용", 0, 1, "C");
		
	$pdf->SetFont($pdf->font_name_kor, "U", 12);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+7);
	$pdf->Cell($pdf->width, $pdf->row_height, "국민건강보험공단 이사장 귀하", 0, 1, "L");

	$pdf->SetXY($pdf->left+10, $pdf->GetY()+2);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width*0.9, 6, "  아래 수급자의 장기요양 표준장기요양이용계획서 내용을 「노인장기요양보험법 시행규칙」제13조에 따라 제공 요청합니다.", 0, "L");
	
	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "1. 수급자 성명 : ".$su['name']."
2. 장기요양 인정번호 : ".$su['injungNo']." 
3. 급여계약일자 : ".$from_dt."
4. 제공기간 : 장기요양급여계약 기간( ".($from_dt != '' ? $from_dt : '                   ')." ~ ".($to_dt != '' ? $to_dt : '                   ')." )", 0, "L");

	
	$pdf->SetXY($pdf->width*0.65, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "L");
	
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 165, 110, '20', '20');	//기관 직인
		}
	}

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "장기요양기관 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height, $center, 0, 0, "L");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, "(서명 또는 인)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "장기요양기관 기호 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, $row['code'], 0, 0, "L");


	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "전화번호 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $phone, 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "팩스 :  ".$myF->phoneStyle($fax,'.'), 0, 0, "L");


	$pdf->SetFont($pdf->font_name_kor, "B", 16);
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "표준장기요양이용계획서 제공 동의서", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+2);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width*0.9, 6, "  본인의 장기요양 표준장기요양이용계획서 내용을 「노인장기요양보험법 시행규칙」제13조에 따라 장기요양기관에 제공함에 있어서 「개인정보보호법」제17조에 따라 동의합니다", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "1. 제공 내용 : 표준장기요양이용계획서 내용
2. 장기요양 인정번호 : 장기요양 급여제공계획수립
3. 제공기간 : 장기요양급여계약 기간( ".($from_dt != '' ? $from_dt : '                   ')." ~ ".($to_dt != '' ? $to_dt : '                   ')." )
4. 동의사항 : □개인정보 수집 이용 □ 민감정보 처리\n     □ 고유식별정보 처리 □ 개인정보 제3사 제공에 관한 사항
※ 귀하께서는 동의를 거부할 수 있습니다.", 0, "L");
	
	$pdf->SetXY($pdf->width*0.65, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($agreeDt,0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "년", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($agreeDt,5,2) < 10 ? substr($agreeDt,6,1) : substr($agreeDt,5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "월", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($agreeDt,8,2) < 10 ? substr($agreeDt,9,1) : substr($agreeDt,8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "일", 0, 1, "L");

	$pdf->SetXY($pdf->left, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "수 급 자 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, "  ".$su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.3, $pdf->row_height, "(서명 또는 인)", 0, 0, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "장기요양 인정번호 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "  ".$su['injungNo'], 0, 0, "L");


	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "전화번호 :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.5, $pdf->row_height, "  ".$myF->phoneStyle($su['hp'],'.'), 0, 0, "L");
	
	$pdf->SetFont($pdf->font_name_kor, "U", 12);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+7);
	$pdf->Cell($pdf->width, $pdf->row_height, "국민건강보험공단 이사장 귀하", 0, 1, "L");

	Unset($row);
?>
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
		
		$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $svc_seq;
		
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

		
		$sql =  ' select from_dt 
				  ,		 to_dt 
					from client_his_svc
				   where org_no = \''.$code.'\'
					 and jumin  = \''.$ssn.'\'
					 and seq    = \''.$svc_seq.'\'';
		
		$svc = $conn->get_array($sql);


		$sql = "select m03_jumin as jumin
				,	   m03_key
				,	   m03_name as name
				,	   m03_tel as tel
				,	   m03_yboho_name as bohoName
				,	   m03_yboho_juminno as bohoJumin
				,	   m03_yboho_gwange as gwange
				,	   m03_yboho_phone as bohoPhone
				,	   m03_yboho_addr as bohoAddr
				,	   lvl.level as level
				,	   lvl.app_no as injungNo
				,	   case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end as level
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
								 and from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
							   order by from_dt desc
								 ) as lvl
						 on lvl.jumin = m03_jumin
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 kind
								from client_his_kind
							   where org_no = '".$code."'
								 and from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
							   order by from_dt desc 
							   ) as kind
						 on kind.jumin = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		//echo nl2br($sql); exit;
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
		
	$jikin = $center['jikin'];
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg'; //서명 

	$from_year = $svc['from_dt'] != '' ? substr($svc['from_dt'],0,4) : '           ';	//계약시작기간(년)
	$from_month = $svc['from_dt'] != '' ? substr($svc['from_dt'],5,2) : '     ';		//계약시작기간(월)
	$from_day = $svc['from_dt'] != '' ? substr($svc['from_dt'],8,2) : '     ';		//계약시작기간(일)
	
	$to_year = $svc['to_dt'] != '' ? substr($svc['to_dt'],0,4) : '           ';		//계약종료기간(년)
	$to_month = $svc['to_dt'] != '' ? substr($svc['to_dt'],5,2) : '     ';			//계약종료기간(월)
	$to_day = $svc['to_dt'] != '' ? substr($svc['to_dt'],8,2) : '     ';			//계약종료기간(일)
	
	$yoil = '';
	
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
	

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "장기요양급여 이용 계약서(방문간호)", 0,"C");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 1);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	if($su['name']!=''){
		$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, $su['name']." (이하 [이용자]라고 합니다)와 ".$center['centerName']."(이하 [사업자]라고 합니다)는 사업자가 이용자에 대해서 실시하는 방문 간호에 대해 다음과 같이 계약합니다.");
	}else {
		$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "_____________________ (이하 [이용자]라고 합니다)와 ".$center['centerName']."(이하 [사업자]라고 합니다)는 사업자가 이용자에 대해서 실시하는 방문 간호에 대해 다음과 같이 계약합니다.");
	}
	
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
	  1. 이용자가 제공을 받는 방문 간호 서비스의 내용은【계약서 별지】에 정했던 바와 같습니         다. 사업자는,【계약자 별지】에 정한 내용에 대하여 이용자 및 그 가족에게 설명합니다.
	  2. 사업자는 서비스 종업원을 이용자의 주택에 파견해 방문 간호 계획에 따라【계약서 별지         】에 정한 내용의 방문 간호 서비스를 제공합니다.
	  3. 방문 간호 계획이 이용자와의 합의를 가지고 변경되어 사업자가 제공하는 서비스의 내용         또는 요양보험 적용의 범위가 변경이 되는  경우는 이용자의 승낙을 얻어 새로운 내용의        【계약서 별지】를 작성하여 그것을 가지고 방문 간호 서비스의 내용으로 합니다.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"제6조 (서비스 제공의 기록)
  1. 사업자는 방문 간호 서비스의 실시마다 서비스 내용 등을 서비스실시 기록부에 기입하고        서비스의 종료시에 이용자의 확인을 받는것으로 합니다. 그 기록은 이용자의 희망이 있으       면 언제라도 이용  자에게 교부합니다.
  2. 사업자는 서비스 실시 기록부를 작성하는 것으로 하며, 이 계약의 종료 후 2년간 보관합        니다.
  3. 이용자는 사업자의 영업 시간내에 그 사업소에서 해당 이용자에 관한 제2항의 서비스 실       시 기록부를 열람할 수 있습니다.
  4. 이용자는 희망이 있으면 언제라도 해당 이용자에 관한 제2항의 서비스 실시 기록부의 복       사물을교부 받을 수가 있습니다.

제7조 (방문간호사의 교체 등)
  1. 이용자는 선임된 방문 간호사의 교체를 희망하는 경우에는 해당  방문 간호사가 업무상         부적당이라고 인정되는 사정 그 외 교체를 희망하는 이유를 분명히 하여 사업자에 대해서        방문 간호사의 교  체를 신청할 수가 있습니다.
  2. 사업자는 방문 간호사의 교체에 의해 이용자 및 그 가족 등에 대해서 서비스 이용상의 불        이익이 생기지 않게 충분히 배려하는 것으로 합니다.
  3. 방문 간호사가 컨디션 불량 등의 경우로 방문 할 수 없게 되었을 때에는 대체 인원을 인선       하고 인선 후 재차 이용자 및 그 가족에게 연락하겠습니다.

제8조 (요금)
  1. 이용자는 서비스의 대가로 해서【계약서 별지】에 정하는 이용 단위마다의 요금을 기본         으로 계산된 매월의 합계 금액을 지불합니다.
  2. 사업자는 당월 요금의 합계액의 청구서에 명세를 교부하여 다음 달 10일까지 이용자에게       송부합니다.
  3. 이용자는 당월 요금의 합계액을 다음달 15일까지 사업자의 지정하는 방법으로 지불합니        다.
  4. 이용자는 주택에 대해 서비스 종업원이 서비스 실시를 위해서 사용하는 수도, 가스, 전기,       전화의 비용을 부담합니다.

제9조 (서비스의 중지)
  1. 이용자는 사업자에 대해서 서비스 실시일의 전영업일 오후 6시까지 통지를 하는 것으로        써 요금을 부담하는 일 없이 서비스 이용을 중지할 수가 있습니다.
  2. 이용자가 서비스 실시일의 전영업일의 오후 5시까지 통지하는 일 없이 서비스의 중지를        희망했을 경우에는 사업자는 이용자에 대해서,【계약서 별지】에 정하는 계산방법에\n     의해, 요금의 전부 또는 일부를 캔슬료로 해서 청구할 수가 있습니다. 이 경우의 요금은\n     제6조에 정하는 다른 요금의 지불과 합하여 청구합니다.

제10조 (요금의 변경)
   1. 사업자는 이용자에 대해서 1개월전까지 문서로 통지하는 것으로써 이용 단위마다의 요         금의 변경(증액 또는 감액)을 신청할 수가 있습니다.
   2. 이용자가 요금의 변경을 승낙하는 경우, 새로운 요금에 근거하는【계약서 별지】를 작성        해, 서로 주고 받습니다.
   3. 이용자가 요금의 변경을 승낙하지 않는 경우, 사업자에 대해, 문서로 통지하는 것으로써,        이 계약을 해약할 수가 있습니다.");

	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"제11조 (계약의 종료)
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
    ②이용자 또는 그 가족이 사업자나 서비스 종업원에 대해서, 이 계약을 계속 하기 어려울          만큼의 배신행위를 실시했을 경우.
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
  서비스 종업원은 항상 신분증을 휴대 해 첫 회 방문시 및 이용자 또는 이용자의 가족으로부터 제시가 요구되었을 때는 언제라도 신분증을 제시합니다.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"제16조 (제휴)
  사업자는 방문간호의 제공에 요양지원전문원 및 보건의료 서비스 또는 복지서비스를 제공하는 사람과의 밀접한 제휴에 노력 합니다.

제17조 (상담·불평 대응)
  사업자는 이용자로부터의 상담, 불평 등에 대응하는 창구를 설치해 방문 간호에 관한 이용자의 요망, 불평등에 대해 신속히 대응합니다.

제18조 (본 계약에 정함이 없는 사항)
  1. 이용자 및 사업자는 신의성실을 가지고 이 계약을 이행하는 것으로 합니다.
  2. 본 계약에 정함이 없는 사항에 대해서는 요양보험 법령 그 외 제 법령이 정하는 것을 존중해 쌍방이 성의를 가져 협의 후 정합니다.

제19조 (재판관할)
  이 계약에 관해서 어쩔수 없이 소송이 되는 경우에 이용자와 사업자는 사업자의 주소지를 관할하는 재판소를 제１심 관할재판소로 하는 것에 미리 합의합니다.

  상기의 계약을 증명하기 위하여 본서 2통을 작성하고 이용자 및 사업자가 서명 날인한 후  각각 1통씩 보유하는 것으로 합니다.");

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
	
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "주 소:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height, '  '.$su['juso'], 0, 1, "L");
	
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
			$pdf->Image('../mem_picture/'.$jikin, 77, 248, '20', '20');	//기관 직인
		}
	}


	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "소재지:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height, '  '.$center['address'], 0, 1, "L");

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
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $su['juso'], 0, 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "1. 수집대상정보", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "○ 장기요양급여 관련 정보
○ 이용자의 지역연계 관련 정보
○ 관련기관 정보제공 요청시 필요한 정보
○ 기타 목적사업 수행에 필요한 정보", 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "2. 수집정보의 활용범위", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "○ 대상자 급여 관련에 필요한 정보의 활용
○ 제공기관 간의 서비스 연계와 관련사항에 관한 대상자 정보 제공
○ 관련기관 정보제공 요청시 제공
○ 장기요양계획, 욕구조사, 정기요양서비스 질 수준 향상 등에 활용", 0, "L");

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
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+30);
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
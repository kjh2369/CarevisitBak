<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');
	require_once '../excel/PHPExcel.php';
	
		
	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=test.xls" );
	

	$code = $_SESSION['userCenterCode'];	//기관기호
	$kind = '0';							//서비스구분
	$ssn = $ed->de($_POST['jumin']);		//수급자주민번호
	$seq = $_POST['conSeq'];				//키
	
	//$ctIcon   = $conn->center_icon($mCode);
		
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
			,	   from_dt
			,	   to_dt
			,      bath_weekly
			,	   use_type
			,      from_time
			,      to_time
			,	   pay_day1
			,	   pay_day2
			  from client_contract
			 where org_no   = \''.$code.'\'
			   and svc_cd   = \''.$kind.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	
	$ct = $conn->get_array($sql);

	$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $seq;

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
			,	   m03_name as name
			,	   m03_tel as tel
			,      m03_hp  as hp
			,	   m03_yboho_name as bohoName
			,	   m03_yboho_juminno as bohoJumin
			,	   m03_yboho_gwange as gwange
			,	   m03_yboho_phone as bohoPhone
			,	   m03_yboho_addr as bohoAddr
			,	   lvl.level as level
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
	
	$jikin = $center['jikin'];
	

	$lvl = $myF->_lvlNm($su['level']);


	//기관장 휴대폰번호
	$sql = 'select mobile
			  from mst_manager
			 where org_no = \''.$code.'\'';
	$mg_hp = $conn -> get_data($sql); 

	
	if($su['kind'] == '3'){
		$kindChk = ' ■ 기초수급자          □ 기타 의료급여수급자';
	}else if($su['kind'] == '2'){
		$kindChk = ' □ 기초수급자          ■ 기타 의료급여수급자';
	}else {
		$kindChk = ' □ 기초수급자          □ 기타 의료급여수급자';
	}

	//신청인(센터) 전화번호/휴대전화번호
	$cTel = $center['centerTel'] != '' ? $myF->phoneStyle($center['centerTel']) : '           ';
	$cHp = $mg_hp != '' ? '( '.$myF->phoneStyle($mg_hp).' )' : '(            ) ';
	
	
	$hTel = $center['centerTel'] != '' ? $centerName.'(T. '.$myF->phoneStyle($center['centerTel']).' )' : $centerName.'(T.               )';
	$hFax = $center['centerFax'] != '' ? '(FAX. '.$myF->phoneStyle($center['centerFax']).' )' : '(fax.              )';

	//수급자(유선/무선)
	$suTel = $su['tel'] != '' ? ' (집전화: '.$myF->phoneStyle($su['tel']).' )' : ' (집전화:               )';
	$suHp = $su['hp'] != '' ? 'H.P: '.$myF->phoneStyle($su['hp']) : 'H.P: ';


	$objPHPExcel = new PHPExcel();
	$sheetIndex = 0;
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
	$sheet = $objPHPExcel->getActiveSheet();
	
	$title = '입소.이용 신청서 및 재가서비스 이용내역서';

	$sheet->setTitle($title);
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.5);
	$sheet->getPageMargins()->setRight(0.0);
	$sheet->getPageMargins()->setLeft(0.0);
	$sheet->getPageMargins()->setBottom(0.5);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getHeaderFooter()->setOddFooter("&C&14&\"Bold\"(주)케어비지트(070.4044.1312)\n&C&9입금계좌:기업은행(803-215-00151-2) 예금주:(주)굿이오스1");
	$sheet->getHeaderFooter()->setOddFooter($footer);

	//스타일
	$lastCol = 40;
	$widthCol= 2.4;
	include("../excel/init.php");
	include_once("../excel/style.php");
	//$sheet->getColumnDimension("T")->setWidth(4.5);

	
	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕
	
	//$rH = $rowH * $fontSize / $defFontSize;
	
	//초기화
	$defFontSize = 11;
	$rowH = 15;
	$rowNo = 0;

	
	$fontSize = 8;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	
	$cellT = 'AH';
	
	$rowNo ++;
	/*
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>'[별지 제5호 서식]', 'border'=>'TNRNBNLN', 'H'=>'L' ) );

	$fontSize = 16;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$fontSize = $defFontSize;
	$rH = $rowH * $fontSize / $defFontSize;


	//타이틀
	$rowNo ++;
	$rowNo ++;
	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>$title, 'border'=>'TNRNBNLN' ) );
	
	//구분공란
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+1)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+2)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+3)->setRowHeight($rH);
	
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	
	$fontSize = $defFontSize;
	$rH = $rowH * $fontSize / $defFontSize;
	
	$rowNo ++;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AC'.($rowNo+1), 'val'=>"입소이용 신청서 ( □신청 □변경 □해지 )") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"처리기간") );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"7일이내") );
	

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*4.4);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+2), 'val'=>"신청인") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.8);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"성명") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>$su['bohoName']) );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'Q'.$rowNo, 'val'=>"주민등록\n번호") );
	$sheet->SetData( Array('F'=>'R'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"수급자와\n의관계") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$su['gwange']) );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"주소") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$su['bohoAddr'], 'H'=>'L') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"전화번호") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$su['bohoPhone'], 'H'=>'L') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*12.4);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+5), 'val'=>"수급자") );
	
	
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.8);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"성명") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>$su['name']) );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'Q'.$rowNo, 'val'=>"주민등록\n번호") );
	$sheet->SetData( Array('F'=>'R'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$myF->issNo($su['jumin']), 'H'=>'L') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.8);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"장기요양\n등급") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>$su['level'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'Q'.$rowNo, 'val'=>"장기요양\n인정번호") );
	$sheet->SetData( Array('F'=>'R'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$su['injungNo'], 'H'=>'L') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"주소") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$su['juso'], 'H'=>'L' ) );
	
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"전화번호") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$suTel.$suHp, 'H'=>'L' ));

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*3.6);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"입소이용\n희망\n장기요양\n기관") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" ".$hTel.'       '.$hFax, 'H'=>'L' ));

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"구 분") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$kindChk, 'H'=>'L' ) );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"장기요양기관 입소 이용을 위해 위와 같이 신청합니다.", 'border'=>'BN' ) );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'TNBN') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>substr($ct['reg_dt'],0,4).' 년 '.intval(substr($ct['reg_dt'],5,2)).' 월 '.substr($ct['reg_dt'],8,2).' 일 ', 'border'=>'TNBN') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.3);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'TNBN') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"신청인 :  ".$su['bohoName']." (서명 또는 인)", 'border'=>'TNBN', 'H'=>'R', 'indent'=>'4') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'TNBN') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"※ 신청인이 수급자 본인가족, 사회복지전담공무원, 시장군수구청장이 지정한 자 이외의 이해관계인인 경우에는 수급자 또는 보호자의 동의를 받아야 함", 'border'=>'TNBN', 'H'=>'L', 'color'=>'FF5E00') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"수급자(또는 보호자)  ".$su['name']." (서명 또는 인)", 'border'=>'TNBN', 'H'=>'R', 'color'=>'FF1212', 'indent'=>'4') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'TNBN') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"시장 군수 구청장 귀하", 'border'=>'TN', 'H'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*7);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+3), 'val'=>"구비\n서류") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" 1. 주민등록증 1부(행정정보공동이용시스템을 통해 대체 확인 가능)", 'border'=>'BN', 'H'=>'L') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" 2. 기초생활 수급대상자의 경우 수급자증명서, 의료급여수급권자의 경우 의료보호증 1부\n   (행정정보공동이용시스템을 통해 대체 확인 가능)", 'border'=>'TNBN', 'H'=>'L') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'L'.$rowNo, 'val'=>" 3. 장기요양인정서 사본", 'border'=>'TNRNBN', 'H'=>'L') );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'M'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"('표준장기이용계획서 사본 포함')", 'border'=>'TNLNBN', 'H'=>'L', 'color'=>'0054FF') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>" 4. 재가이용희망자는 재가서비스 이용내역서를 붙임으로 첨부 ", 'border'=>'TN', 'H'=>'L') );
	
	//구분공란
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+1)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+2)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+3)->setRowHeight($rH);
	*/
	
	$fontSize = 16;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$fontSize = $defFontSize;
	$rH = $rowH * $fontSize / $defFontSize;
	
	//타이틀
	$rowNo ++;
	$rowNo ++;
	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>"재가서비스 이용내역서", 'border'=>'TLR' ) );
	
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	//구분공란
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+1)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+2)->setRowHeight($rH);

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*4.8);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+1), 'val'=>"수급자", 'border'=>'L') );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"성명") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$su['name']) );
	
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"주민등록번호") );
	$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$myF->issStyle($su['jumin']), 'border'=>'R') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"장기요양 등급") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$lvl) );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"장기요양인정번호") );
	$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$su['injungNo'], 'border'=>'R') );
	
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*21.6);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+8), 'val'=>"급여\n이용\n신청\n내용", 'border'=>'L') );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"급여종류") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"재가요양") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"이용기간") );
	$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>$from_dt."~\n".$to_dt, 'border'=>'R') );
	
	#수가정보
	$suga = $mySuga->findSugaCare($code, '200', $ct['reg_dt'], $ct['from_time1'], $ct['to_time1'], '', '1');

	$suga2 = $mySuga->findSugaCare($code, '200', $ct['reg_dt'], $ct['from_time2'], $ct['to_time2'], '', '1');

	$suga3 = $mySuga->findSugaCare($code, '500', $ct['reg_dt'], $ct['from_time'], $ct['to_time'], '', '1');
		
	$count = 0;
	$count2 = 0;
	
	for($i=0; $i<7; $i++){
		if($ct['use_yoil1'][$i] == 'Y'){
			$count ++;
		}

		if($ct['use_yoil2'][$i] == 'Y'){
			$count2 ++;
		}

		if($ct['use_type']=='1'){
			$count3 = 4;
		}else if($ct['use_type']=='2'){
			$count3 = 2;
		}else if($ct['use_type']=='3'){
			$count3 = 1;
		}

	}
	
	$count = $count * 4;
	$count2 = $count2 * 4;

	$sugaTot = $suga['cost'] * $count;
	$sugaTot2 = $suga2['cost'] * $count2;
	$sugaTot3 = $suga3['cost'] * $count3;
	
	$svcName = explode('/',$suga['name']);
	$svcName2 = explode('/',$suga2['name']);
	$svcName3 = explode('/',$suga3['name']);
	
	$sugaTotol = $sugaTot + $sugaTot2 + $sugaTot3;

	if($ct['from_time1'] == '' && $ct['from_time2'] == ''){
		$suga['name'] = $suga3['name'];
		$suga['cost'] = $suga3['cost'];
		$count = $count3;
		$sugaTot = $sugaTot3;
		$svcName = $svcName3;

		$suga2['name'] = '';
		$suga2['cost'] = '';
		$count2 = '';
		$sugaTot2 = '';
		$svcName2 = '';
		$suga3['name'] = '';
		$suga3['cost'] = '';
		$count3 = '';
		$sugaTot3 = '';
		$svcName3 = '';
	}
	
	if($ct['from_time1'] != ''){
		if($ct['from_time2'] == ''){
			$suga2['name'] = $suga3['name'];
			$suga2['cost'] = $suga3['cost'];
			$count2 = $count3;
			$sugaTot2 = $sugaTot3;
			$svcName2 = $svcName3;

			$suga3['name'] = '';
			$suga3['cost'] = '';
			$count3 = '';
			$sugaTot3 = '';
			$svcName3 = '';
		}
	}
	
	if($ct['from_time'] == ''){
		$suga3['name'] = '';
		$suga3['cost'] = '';
		$count3 = '';
		$sugaTot3 = '';
		$svcName3 = '';
	}


	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*4.8);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.($rowNo+1), 'val'=>"서비스\n종류") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.($rowNo+1), 'val'=>"서비스\n내용") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.($rowNo+1), 'val'=>"수가") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.($rowNo+1), 'val'=>"횟수/월") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.($rowNo+1), 'val'=>"금액/월") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"이용희망기관", 'border'=>'R') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"장기요양\n기관명") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"장기요양\n기관기호", 'border'=>'R') );
	

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$svcName[0]) );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$suga['name']) );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>$suga['cost'], 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$count) );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>$sugaTot, 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.($rowNo+1), 'val'=>$center['centerName']) );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.($rowNo+1), 'val'=>$code, 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$svcName2[0]) );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$suga2['name']) );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>$suga2['cost'], 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$count2) );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>$sugaTot2, 'H'=>'R', 'format'=>'#,##0') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$svcName3[0]) );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$suga3['name']) );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>$suga3['cost'], 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>$count3) );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>$sugaTot3, 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"합 계") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>number_format($sugaTotol)."(원)", 'H'=>'R') );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*14.4);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.($rowNo+5), 'val'=>"복지\n용구\n이용\n신청\n내용", 'border'=>'L') );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*4.8);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.($rowNo+1), 'val'=>"품목명") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.($rowNo+1), 'val'=>"제품\n코드") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"급여방식") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*4.8);
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.($rowNo+1), 'val'=>"대여기간") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.($rowNo+1), 'val'=>"금  액") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"이용희망기관", 'border'=>'R') );
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>"구입") );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"대여") );
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"장기요양\n기관명") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"장기요양\n기관기호", 'border'=>'R') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.5);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'M'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'S'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'T'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AC'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'R') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2.4);	
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"합 계", 'border'=>'B') );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'X'.$rowNo, 'val'=>"(원)", 'border'=>'B') );
	$sheet->SetData( Array('F'=>'Y'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"", 'border'=>'BR') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"※ 서비스 종류 : 급여계약내역서상", 'border'=>'LNBNRN', 'H'=>'L') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"급여종류(방문요양,방문목욕,주야간보호,단기보호)", 'border'=>'TNLNBNRN', 'H'=>'L') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AH'.$rowNo, 'val'=>"※ 서비스 내용 : 급여계약내역서상 서비스 이용시간", 'border'=>'TNLNBNRN', 'H'=>'L') );
	

	
	/*
	$exp = explode('.',$jikin);
	$exp = strtolower($exp[sizeof($exp)-1]);
	
	if($exp != 'bmp'){
		if($exp == 'jpg'){
			$gdImage = imagecreatefromjpeg('../mem_picture/'.$jikin);
		}else if($exp == 'png'){
			$gdImage = imagecreatefrompng('../mem_picture/'.$jikin);
		}else if($exp == 'gif'){
			$gdImage = imagecreatefromgif('../mem_picture/'.$jikin);
		}
		
		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('직인');
		$objDrawing->setDescription('직인');
		$objDrawing->setImageResource($gdImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		
		$objDrawing->setWidth(90);
		$objDrawing->setCoordinates('AB22');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
	}
	*/

	//$objRichText = new PHPExcel_RichText();
	//$objRichText->createText('(성명 또는 인)');    //Write text
	//Add text and set the text bold italics and text color
	//$objPayable = $objRichText->createTextRun( 'payable within thirty days after the end of the month');
	//$objPayable->getFont()->setBold(true);
	//$objPayable->getFont()->setItalic(true);
	//$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
	//$objRichText->createText(', unless specified otherwise on the invoice.');

	//The text written in AA24 cell
	//$objPHPExcel->getActiveSheet()->getStyle( 'AA24:AH24')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
	//$objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->getFill()->getStartColor()->setARGB('FF808080');
	//$objPHPExcel->getActiveSheet()->getCell( 'AA24')->setValue($objRichText);
	//$objPHPExcel->getActiveSheet()->mergeCells( 'AA24:AH24');      
	


	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');

?>
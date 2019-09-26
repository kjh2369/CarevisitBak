<?php
	// Include the main TCPDF library (search for installation path).
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');
	require_once('tcpdf_include.php');

	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	//$pdf->setHeaderFont(array('nanumbarungothicyethangul','','10'));
	$pdf->SetHeaderData('', 0, '', '', array(255,255,255), array(255,255,255));
	//$pdf->setFooterData(array(0,64,0), array(0,64,128));
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

	$pdf->left	= 14;
	$pdf->top	= 11;
	$pdf->width	= 182;
	$pdf->height= 270;
	
	$pdf->font_name_kor = 'nanumbarungothicyethangul';
	$pdf->SetFillColor(220,220,220);
		
	$mobileYn = $_GET['mobileYn'];
	$code = $_GET['code'];
	$dt = $_GET['yymm'];
	$mode = 'cv';
	
	$pdf->ctIcon = $conn->center_icon($code);
	$pdf->ctName = $conn->center_name($code);


	$sql = 'select m03_jumin
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_key   = \''.$_GET['key'].'\'';

	$c_cd = $conn -> get_data($sql);


	$mCode = $code != '' ? $code : $_POST['para_code'];
	$mKind = $kind != '' ? $kind : 0;
	$mJumin = $c_cd != '' ? $c_cd : $ed->de($_POST['para_c_cd']);
	$mode  = $mode != '' ? $mode : $_POST['mode'];
	$dt  = $_POST['para_yymm'] ? $_POST['para_yymm'] : $dt;
	

	function setArrayText($pdf, $pos){
		/**************************************************

			기타 텍스트 출력 부분

			x         : X좌표
			y         : Y좌표
			type      : 출력형식
			width     :
			height    :
			font_size :
			align     :
			border	  :
			text      : 출력텍스트

		**************************************************/
		if (is_array($pos)){
			foreach($pos as $i => $p){
				$tmp_x = $pdf->GetX();
				$tmp_y = $pdf->GetY();

				if ($p['type'] == 'multi_text' ||
					$p['type'] == 'text'){
					if (!empty($p['font_size']))
						$pdf->SetFont($pdf->font_name_kor, $p['font_bold'].$p['font_style'], $p['font_size']);
					else
						$pdf->SetFont($pdf->font_name_kor, '', 10);

					$pdf->SetTextColor($p['text_color']['r'], $p['text_color']['g'], $p['text_color']['b']);
				}

				if ($p['type'] == 'multi_text'){
					$pdf->SetXY($p['x'], $p['y']);
					$pdf->MultiCell($p['width'], $p['height'], $p['text'], $p['border'], $p['align']);
				}else if ($p['type'] == 'text'){
					$pdf->Text($p['x'], $p['y'], $p['text']);
				}
			}
		}
	}


	// 수급자 정보

	$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
			,      mst.name AS c_nm
			,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
			,      CASE kind.kind WHEN \'1\' THEN \'일반\'
								  WHEN \'2\' THEN \'의료\'
								  WHEN \'3\' THEN \'기초\'
								  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
			,	   lvl.app_no 
			  FROM (
			SELECT t01_jumin AS jumin
			,	   t01_sugup_date
			  FROM t01iljung
			 WHERE t01_ccode = \''.$mCode.'\'
			   AND t01_mkind = \'0\'
			   AND t01_svc_subcode = \'200\'
			   AND t01_del_yn = \'N\'
			   AND LEFT(t01_sugup_date, 6) = \''.$dt.'\'
			   AND LEFT(t01_sugup_date, 6) >= \'201407\'
				   ) AS iljung
			 INNER JOIN (
				   SELECT m03_jumin AS jumin
				   ,      m03_name AS name
				   ,      m03_tel AS tel
					 FROM m03sugupja
					WHERE m03_ccode = \''.$mCode.'\'
					  AND m03_mkind = \'0\'';
    if($mode != 'all'){
		$sql .= 'and m03_jumin = \''.$mJumin.'\'';
	}
	$sql .=	 '	   ) AS mst
				ON mst.jumin = iljung.jumin
			  LEFT JOIN (
				   SELECT jumin
				   ,      level
				   ,	  app_no
					 FROM client_his_lvl
					WHERE org_no = \''.$mCode.'\'
					  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$dt.'\'
					  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$dt.'\'
				    ORDER BY from_dt desc
				   ) AS lvl
				ON lvl.jumin = iljung.jumin
			  LEFT JOIN (
				   SELECT jumin
				   ,      kind
					 FROM client_his_kind
					WHERE org_no = \''.$mCode.'\'
					  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$dt.'\'
					  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$dt.'\'
					ORDER BY from_dt desc
				   ) AS kind
				ON kind.jumin = iljung.jumin
			 ORDER BY name';
	
	if($mode == 'all'){
		$conn -> query($sql);
		$conn -> fetch();
		$rowCount = $conn -> row_count();
	
		for( $i=0; $i<$rowCount; $i++){
			$row[$i] = $conn -> select_row($i);
		
			$client_list[$i]	 = $row[$i]['c_cd'];
			$mSuName[$i]		 = $row[$i]['c_nm'];
			$mSuLevel[$i]		 = $row[$i]['l_nm'];
			$mSuInjungNo[$i]     = $row[$i]['app_no'];
			
		}
	}else {
	
		$row = $conn->get_array($sql);
		
		$client_list[0] = $mJumin;
		$mSuName[0]     = ($row['c_nm'] != '' ? $row['c_nm'] : iconv('utf8','euc-kr',$_POST['para_name']));
		$mSuLevel[0]    = ($row['l_nm'] != '' ? $row['l_nm'] : $_POST['para_level'].'등급');
		$mSuInjungNo[0] = ($row['app_no'] != '' ? $row['app_no'] : $_POST['para_appNo']);
		//$mClientNo[0]   = $row['m03_client_no'];
	}
	
	$conn->row_free();
	
	$cnt = 0;
	$cnt2 = 0;

	// 센터정보
	$sql = "select m00_code1 as code, m00_cname as name"
		 . "  from m00center"
		 . " where m00_mcode = '".$mCode
		 . "'  and m00_mkind = '".$mKind
		 . "'";
	$center = $conn->get_array($sql);
	
	
	foreach($client_list as $client_i => $client){
		
		$appNo = $mSuInjungNo[$cnt];
		
		$sql = 'SELECT	from_dt
				,		from_tm
				,		to_dt
				,		to_tm
				,		proc_time
				,		mem_nm
				,		dtl_1, dtl_2, dtl_3, dtl_4, dtl_5, dtl_6, dtl_7, dtl_8, dtl_9, dtl_10
				FROM	lg2cv
				WHERE	org_no = \''.$mCode.'\'
				AND		app_no = \''.$appNo.'\'
				AND		svc_gbn= \'200\'
				AND		left(reg_dt, 6) = \''.$dt.'\'
				AND		del_flag = \'N\'
				ORDER	BY svc_gbn, name, app_no, from_dt, from_tm, to_dt, to_tm';
		
		
		$conn -> query($sql);
		$conn -> fetch();
		$rowCount = $conn -> row_count();
		
		if($rowCount>0 && $mode){
			
			for($i=0; $i<$rowCount; $i++){
				$R = $conn->select_row($i);
				
				if ($i % 7 == 0){
					$rowIdx = SizeOf($data);
					$idx = 1;
				}
				
				$data[$rowIdx][$idx]['day']			= substr($R['from_dt'],4,2).'/'.substr($R['from_dt'],6,2);
				$data[$rowIdx][$idx]['fromTm']		= $myF->timeStyle($R['from_tm']);
				$data[$rowIdx][$idx]['toTm']		= $myF->timeStyle($R['to_tm']);
				$data[$rowIdx][$idx]['dtl1']		= str_replace('-','',$R['dtl_1']);		//정서지원
				$data[$rowIdx][$idx]['dtl2']		= str_replace('-','',$R['dtl_2']);		//신체활동
				$data[$rowIdx][$idx]['dtl3']		= str_replace('-','',$R['dtl_3']);		//인지활동
				$data[$rowIdx][$idx]['dtl4']		= str_replace('-','',$R['dtl_4']);		//가사 및 일상생활
				$data[$rowIdx][$idx]['dtl5']		= str_replace('-','',$R['dtl_5']);		//신체기능
				$data[$rowIdx][$idx]['dtl6']		= str_replace('-','',$R['dtl_6']);		//식사기능
				$data[$rowIdx][$idx]['dtl7']		= str_replace('-','',$R['dtl_7']);		//인지기능
				$data[$rowIdx][$idx]['dtl8']		= str_replace('0','',$R['dtl_8']);		//배변변화(대변실수)
				$data[$rowIdx][$idx]['dtl9']		= str_replace('0','',$R['dtl_9']);		//배변변화(소변실수)
				$data[$rowIdx][$idx]['dtl10']		= $R['dtl_10'];		//특이사항
				$data[$rowIdx][$idx]['procTm']		= $R['proc_time'];	//총시간
				$data[$rowIdx][$idx]['memNm']       = $R['mem_nm'];		//요양요원

				$idx ++;
			}
		}else {
			$data[$rowIdx][0]['day']		= '';
			$data[$rowIdx][0]['fromTm']		= '';
			$data[$rowIdx][0]['toTm']		= '';
			$data[$rowIdx][0]['dtl1']		= ''; //정서지원
			$data[$rowIdx][0]['dtl2']		= ''; //신체활동
			$data[$rowIdx][0]['dtl3']		= ''; //인지활동
			$data[$rowIdx][0]['dtl4']		= ''; //가사 및 일상생활
			$data[$rowIdx][0]['dtl5']		= ''; //신체기능
			$data[$rowIdx][0]['dtl6']		= ''; //식사기능
			$data[$rowIdx][0]['dtl7']		= ''; //인지기능
			$data[$rowIdx][0]['dtl8']		= ''; //배변변화(대변실수)
			$data[$rowIdx][0]['dtl10']		= ''; //특이사항
			$data[$rowIdx][0]['procTm']		= ''; //총시간
			$data[$rowIdx][0]['memNm']      = ''; //요양요원
		}

		$conn->row_free();

		foreach($data as $row){
			
			$pdf->AddPage();
			$pdf->SetXY(14, 21);
			//$pdf->SetLineWidth(0.6);
			//$pdf->Rect(14, 26, 182, 250);
			$pdf->SetLineWidth(0.2);
			
			$pdf->SetFont($pdf->font_name_kor,'',8);
			$pdf->SetX(10);
			$pdf->Cell(100,5,'■ 노인장기요양보험법 시행규칙 [별지 제12호서식]',0,0,'L');

			$pdf->SetFont($pdf->font_name_kor,'B',18);
			$pdf->SetXY(10, $pdf->getY()+6);
			$pdf->Cell(186,6,'장기요양급여제공기록지(방문요양)',0,1,'C');
			
			$pdf->SetFont($pdf->font_name_kor,'',9);

			$pdf->SetX(190);
			//$pdf->Cell(10,6,'(앞쪽)',0,1,'C');
			$pdf->Cell(10,6,'',0,1,'C');

			$pdf->SetFont($pdf->font_name_kor,'',10);

			/*
			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.015, 'y'=>$pdf->GetY()+3.5, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.7, 'align'=>'L', 'text'=>"장기요양\n기관기호");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.235, 'y'=>$pdf->GetY()+3.5, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.7, 'align'=>'C', 'text'=>"장기요양\n기관명");
			*/

			//$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.45, 'y'=>$pdf->GetY()+5.5, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.7, 'align'=>'C', 'text'=>$center['name']);


			//$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.643, 'y'=>$pdf->GetY()+3.5, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.7, 'align'=>'C', 'text'=>"장기요양\n등급");

			$pdf->SetX(10);
			$pdf->Cell(45,$pdf->row_height,'수급자 성명','TR',0,'L'); 
			$pdf->Cell(45,$pdf->row_height,'생년월일','LTR',0,'L'); 
			$pdf->Cell(50,$pdf->row_height,'장기요양등급','LTR',0,'L'); 
			$pdf->Cell(50,$pdf->row_height,'장기요양인정번호','LT',1,'L'); 
			
			$pdf->SetX(10);
			$pdf->Cell(45,$pdf->row_height,$mSuName[$cnt],'RB',0,'C'); //수급자성명
			$pdf->Cell(45,$pdf->row_height,$myF->issToBirthday($client),'LRB',0,'C'); //생년월일
			$pdf->Cell(50,$pdf->row_height,$mSuLevel[$cnt],'LRB',0,'C'); //장기요양등급
			$pdf->Cell(50,$pdf->row_height,$mSuInjungNo[$cnt],'LB',1,'C'); //장기요양등급

			$pdf->SetX(10);
			$pdf->Cell(95,$pdf->row_height,'장기요양기관명','TR',0,'L'); //장기요양기관기호
			$pdf->Cell(95,$pdf->row_height,'장기요양기관기호','LT',1,'L'); //장기요양기관명
			
			$pdf->SetX(10);
			$pdf->Cell(95,$pdf->row_height,$center['name'],'RB',0,'C'); //장기요양기관기호
			$pdf->Cell(95,$pdf->row_height,$center['code'],'LB',1,'C'); //장기요양기관명

			//$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.645, 'y'=>$pdf->GetY()+3.5, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.7, 'align'=>'C', 'text'=>"장기요양\n인정번호");

			$pos[sizeof($pos)] = array('x'=>$pdf->left-4, 'y'=>$pdf->GetY()+17, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.055, 'height'=>4.5, 'align'=>'C', 'text'=>"일정\n관리");

			$pdf->SetXY(10,$pdf->getY()+3);
			$pdf->Cell(10,36,'','TB',0,'C');
			$pdf->Cell(61,9,substr($dt, 0, 4).'년    월/일',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, ($row[$i]['day']!=''?$row[$i]['day']:'/'), ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "C");
			}
			

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,27,'제공시간',1,0,'C');
			$pdf->Cell(39,9,'총 시 간',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, $row[$i]['procTm'].'분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pdf->SetXY(42,$pdf->getY());
			$pdf->Cell(39,9,'시작시간',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, ($row[$i]['fromTm']!=''?$myF->timeStyle(substr($row[$i]['fromTm'],0,5)):':'), ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "C");
			}

			$pdf->SetXY(42,$pdf->getY());
			$pdf->Cell(39,9,'종료시간',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, ($row[$i]['toTm']!=''?$myF->timeStyle(substr($row[$i]['toTm'],0,5)):':'), ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "C");
			}

		
			$pdf->SetFont($pdf->font_name_kor,'',10);

			$pos[sizeof($pos)] = array('x'=>$pdf->left-3, 'y'=>$pdf->GetY()+9, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.04, 'height'=>4.5, 'align'=>'C', 'text'=>"서비스\n\n제공");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.06, 'y'=>$pdf->GetY()+1, 'font_size'=>9, 'type'=>'multi_text', 'width'=>$pdf->width * 0.4, 'height'=>4.5, 'align'=>'C', 'text'=>"세면,구강,식사,옷갈아입기\n머리감기,목욕,화장실이용\n하기,이동도움,체위변경 등");

			$pdf->SetX(10);
			$pdf->Cell(10,46,'','TB',0,'C');
			$pdf->Cell(22,16,'신체활동지원',1,0,'C');
			$pdf->Cell(39,16,'',1,0,'R');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height*2+4, $row[$i]['dtl2'].'분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}
			
			if($mSuLevel[$cnt] != '5등급'){
				if($code == '34729028713'){ //안심재가장기요양기관
					$pdf->Line(20,123,202,123);
					$pdf->Line(20,125,202,125);
				}	
			}

			//$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.06, 'y'=>$pdf->GetY()+1, 'font_size'=>9, 'type'=>'multi_text', 'width'=>$pdf->width * 0.4, 'height'=>4.5, 'align'=>'C', 'text'=>"인지자극활동");

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,10,'인지활동지원',1,0,'C');
			$pdf->Cell(39,5,'인지자극활동',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height-1, $row[$i]['dtl3'].'분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pdf->SetXY(42,$pdf->getY());
			$pdf->Cell(39,5,'일상생활 함께하기',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height-1, '분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,10,'정서지원',1,0,'C');
			$pdf->Cell(39,10,'말벗, 격려',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+4, $row[$i]['dtl1'].'분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pos[sizeof($pos)] = array('x'=>$pdf->left-10, 'y'=>$pdf->GetY()+0.5, 'font_size'=>9, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.5, 'align'=>'C', 'text'=>"가사및\n일상생활지원");


			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,10,'',1,0,'C');
			$pdf->Cell(39,10,'식사준비, 청소, 세탁 등',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+4, $row[$i]['dtl4'].'분', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}


			$pos[sizeof($pos)] = array('x'=>$pdf->left-4, 'y'=>$pdf->GetY()+18, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.055, 'height'=>4.5, 'align'=>'C', 'text'=>"변화상태");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.168, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.229, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.289, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

			$wd = 0;

			for($i=0; $i<7; $i++){

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.378+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.400+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.421+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

				$wd += 0.0936;
			}

		
			$pdf->SetX(10);
			$pdf->Cell(10,45,'','TB',0,'C');
			$pdf->Cell(22,9,'신체기능',1,0,'C');
			$pdf->Cell(39,9,'□호전  □유지  □악화',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □','TB',1,'C');


			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.168, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.229, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.289, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

			$wd = 0;

			for($i=0; $i<7; $i++){

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.378+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.400+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.421+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

				$wd += 0.0936;
			}

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,9,'식사기능',1,0,'C');
			$pdf->Cell(39,9,'□호전  □유지  □악화',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □','TB',1,'C');


			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.168, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.229, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.289, 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

			$wd = 0;

			for($i=0; $i<7; $i++){

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.378+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"1");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.400+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"2");

				$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*(0.421+$wd), 'y'=>$pdf->GetY()+3, 'font_size'=>7, 'type'=>'multi_text', 'width'=>$pdf->width * 0.03, 'height'=>4.5, 'align'=>'C', 'text'=>"3");

				$wd += 0.0936;
			}

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,9,'인지기능',1,0,'C');
			$pdf->Cell(39,9,'□호전  □유지  □악화',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □',1,0,'C');
			$pdf->Cell(17,9,'□ □ □','TB',1,'C');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,18,'배변변화',1,0,'C');
			$pdf->Cell(39,9,'대변 실수 횟수',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, $row[$i]['dtl8'].'회', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pdf->SetXY(42,$pdf->getY());
			$pdf->Cell(39,9,'소변 실수 횟수',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+3, $row[$i]['dtl9'].'회', ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "R");
			}

			$pos[sizeof($pos)] = array('x'=>$pdf->left-4, 'y'=>$pdf->GetY()+24, 'font_size'=>11, 'type'=>'multi_text', 'width'=>$pdf->width * 0.055, 'height'=>4.5, 'align'=>'C', 'text'=>"특이사항");

			$pdf->SetX(10);
			$pdf->Cell(10,56,'','TB',0,'C');
			$pdf->Cell(22,8,($row[1]['day']!=''?$row[1]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[1]['dtl10'],'TB',1,'L');


			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[2]['day']!=''?$row[2]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[2]['dtl10'],'TB',1,'L');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[3]['day']!=''?$row[3]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[3]['dtl10'],'TB',1,'L');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[4]['day']!=''?$row[4]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[4]['dtl10'],'TB',1,'L');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[5]['day']!=''?$row[5]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[5]['dtl10'],'TB',1,'L');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[6]['day']!=''?$row[6]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[6]['dtl10'],'TB',1,'L');

			$pdf->SetXY(20,$pdf->getY());
			$pdf->Cell(22,8,($row[7]['day']!=''?$row[7]['day']:'/'),1,0,'C');
			$pdf->Cell(158,8,$row[7]['dtl10'],'TB',1,'L');

			$pdf->SetFont($pdf->font_name_kor,'',9);

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.11, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.5, 'align'=>'C', 'text'=>"장기요양요원 성명\n(서명)");

			$pdf->SetX(10);
			$pdf->Cell(32,20,'서 명','TB',0,'C');
			$pdf->Cell(39,10,'',1,0,'C');
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+4, $row[$i]['memNm'], ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "C");
			}

			$pos[sizeof($pos)] = array('x'=>$pdf->left+$pdf->width*0.11, 'y'=>$pdf->GetY()+1, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.3, 'height'=>4.5, 'align'=>'C', 'text'=>"수급자또는보호자성명\n(서명)");

			$pdf->SetXY(42,$pdf->getY());
			$pdf->Cell(39,10,'',1,0,'C');
			
			if($mCode == '34872000051') $suName = $mSuName[$cnt];
			
			for($i=1; $i<=7; $i++){
				$pdf->Cell(17, $pdf->row_height+4, $suName, ($i == 7 ? 'TB' : 1), ($i == 7 ? 1 : 0), "L");
			}

			//$pos[sizeof($pos)] = array('x'=>$pdf->left-5, 'y'=>$pdf->GetY()+2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.9, 'height'=>4.5, 'align'=>'L', 'text'=>"* 서비스 별로 제공된 급여제공 시간을 분으로 표기합니다.\n* 요양보호사 서명란에는 자필로 이름을 적는다. \n* 특이사항에는 일자별로 특이사항이 있는 경우 특이내용을 기록한다.(병원진료 등..)");

			$pdf->SetFont($pdf->font_name_kor,'',8);
			$pdf->SetX(10);
			$pdf->Cell($pdf->width+7,5,'210mm x 297mm[백상지 80g/㎡(재활용품)]',0,0,'R');

			setArrayText($pdf, $pos);
			unset($pos);

			$cnt2 ++;
		}
		
		$cnt ++;
		unset($data);
	}
	
	unset($client_list);
	
	
	if($_POST['inGbn'] == '1'){
		$pdf->Image('../report/img/care_info.jpg',0,0,210,305,'jpg');
	}
	
	
	$pdf->Output('급여제공기록지('.$myF->dateStyle($dt).')','I');
	
?>
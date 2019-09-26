<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$fromDt = str_replace('-','', $_POST['fromDt']);
	$toDt = str_replace('-','', $_POST['toDt']);

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("업무일지").".xls" );
	header( "Content-Description: ".$myF->euckr("업무일지"));
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$rowH = 15;
	$sheetIndex = 0;

	$objPHPExcel = new PHPExcel();
	
	//결제란설정
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$orgNo.'\'';
	$row = $conn->get_array($sql);

	$signCnt = $row['line_cnt'];
	$signTxt = Explode('|',$row['subject']);

	Unset($row);


	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("방문사회복지사 보고서").".xls" );
	header( "Content-Description: ".$myF->euckr("방문사회복지사 보고서"));
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$rowH = 15;

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("방문사회복지사 보고서");
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(1);
	$sheet->getPageMargins()->setRight(0.0);
	$sheet->getPageMargins()->setLeft(0.0);
	$sheet->getPageMargins()->setBottom(0.5);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getHeaderFooter()->setOddFooter("&C&14&\"Bold\"(주)케어비지트(070.4044.1312)\n&C&9입금계좌:기업은행(803-215-00151-2) 예금주:(주)굿이오스1");
	//$sheet->getHeaderFooter()->setOddFooter($footer);

	//스타일
	$lastCol = 40;
	$widthCol= 2.5;
	include("../excel/style.php");
	include("../excel/init.php");
	
	
	$sheet->getColumnDimension("AO")->setWidth(4.5);


	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim

	//초기화
	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;


	$fontSize = 13;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$fontSize = $defFontSize;
	$rH = $rowH * $fontSize / $defFontSize;


	//타이틀 및 결재란
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+1)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+2)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo+3)->setRowHeight($rH);

	if ($signCnt == 0){
		$cellT = 'AO';
	}else{
		$cellT = 'X';
	}

	$title = "방문사회복지사 보고서\n(기간 : ".str_replace('-','.',$_POST['fromDt'])." ~ ".str_replace('-','.',$_POST['toDt']).")";

	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>$title, 'border'=>'TNRNBNLN' ) );

	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	if ($signCnt != ''){
		if ($signCnt == 1){
			$firstF = 'AI';
		}else if ($signCnt == 2){
			$firstF = 'AD';
		}else if ($signCnt == 3 || $signCnt == 4){
			$firstF = 'AB';
		}else{
			$firstF = 'Y';
		}

		if ($signCnt == 3){
			$cellCnt = 3;
		}else if ($signCnt == 4 || $signCnt == 5){
			$cellCnt = 2;
		}else{
			$cellCnt = 4;
		}

		$cellF = $firstF;
		$cellT = GetNextCellId($cellF, 1);
		$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>"결\n재" ) );

		for($i=0; $i<$signCnt; $i++){
			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF, $cellCnt);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>$signTxt[$i] ) );
		}

		$rowNo ++;
		$cellT = GetNextCellId($firstF, 1);
		for($i=0; $i<$signCnt; $i++){
			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF, $cellCnt);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+2) ) );
		}
	}else{
		$sheet->SetData( Array('F'=>'Z'.$rowNo, 'T'=>'AC'.($rowNo+3), 'val'=>"결\n\n재") );
		$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AG'.$rowNo, 'val'=>"담  당") );
		$sheet->SetData( Array('F'=>'AH'.$rowNo, 'T'=>'AK'.$rowNo, 'val'=>"") );
		$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"기관장") );

		$rowNo ++;
		$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AG'.($rowNo+2), 'val'=>"") );
		$sheet->SetData( Array('F'=>'AH'.$rowNo, 'T'=>'AK'.($rowNo+2), 'val'=>"") );
		$sheet->SetData( Array('F'=>'AL'.$rowNo, 'T'=>'AO'.($rowNo+2), 'val'=>"") );
	}


	//구분공란
	$rowNo ++;
	$rowNo ++;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.3);
	

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'E'.$rowNo, 'val'=>"일자", 'border'=>'LT', 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'L'.$rowNo, 'val'=>"수급자", 'border'=>'T', 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'M'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>"총평", 'border'=>'T', 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"지시사항", 'border'=>'T', 'backcolor'=>'EAEAEA') );
	
	$sql = 'SELECT	date
			,		jumin
			,	    comment
			,		command
			FROM	sw_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		reg_jumin	= \''.$jumin.'\'
			AND		date		>= \''.$fromDt.'\'
			AND		date		<= \''.$toDt.'\'
			AND		del_flag= \'N\'
			ORDER   BY date desc, time desc';
	//echo nl2br($sql); exit;
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		

		//고객정보(주민,이름,연락처);
		$sql = 'select m03_name as name
				  from m03sugupja
				 where m03_ccode = \''.$orgNo.'\'
				   and m03_jumin   = \''.$row['jumin'].'\'';
		$clt_name = $conn -> get_data($sql);

		$tmpRh = GetRowHeight(GetRowcount($row['comment'],55));
		$tmpRh2 = GetRowHeight(GetRowcount($row['command'],60));
		
		if($tmpRh2 > $tmpRh){
			$tmpRh = $tmpRh2;
		}

		if ($tmpRh < $rH * 1.5) $tmpRh = $rH * 1.5;

		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($tmpRh);
		$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'E'.$rowNo, 'val'=>$myF->dateStyle($row['date'],'.'), 'border'=>'LB') ); //수급자명
		$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'L'.$rowNo, 'val'=>$clt_name, 'border'=>'B') );
		$sheet->SetData( Array('F'=>'M'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>$row['comment'], 'H'=>'L', 'border'=>'B') );
		$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row['command'], 'H'=>'L', 'border'=>'B') );
	

	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>
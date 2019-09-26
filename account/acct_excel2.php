<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once '../excel/PHPExcel.php';
	
	$code = $_SESSION['userCenterCode'];
	$name = $conn->_storeName($code);

	if ($_GET['gubun'] == 'I'){
		$field = 'income';
		$title = '수입결의서';
	}else{
		$field = 'outgo';
		$title = '지출결의서';
	}
	
	if (!Empty($_GET['docNo'])){
		$sql = 'SELECT '.$field.'_acct_dt
				,      '.$field.'_item_cd
				,      '.$field.'_item
				,      '.$field.'_amt
				,      '.$field.'_vat
				,      proof_year
				,      proof_no
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND CONCAT(DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\'),proof_no) = \''.$_GET['docNo'].'\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			
			if (Empty($tmp[''.$field.'_acct_dt'])){
				$tmp[''.$field.'_acct_dt'] = $row[''.$field.'_acct_dt'];
			}

			if (Empty($tmp[''.$field.'_item_cd'])){
				$tmp[''.$field.'_item_cd'] = $row[''.$field.'_item_cd'];
			}

			if (Empty($tmp['proof_year'])){
				$tmp['proof_year'] = $row['proof_year'];
			}

			if (Empty($tmp['proof_no'])){
				$tmp['proof_no'] = $row['proof_no'];
			}

			$tmp[''.$field.'_amt'] += $row[''.$field.'_amt'];
			$tmp[''.$field.'_vat'] += $row[''.$field.'_vat'];
			$tmp[''.$field.'_item'] .= (!Empty($tmp[''.$field.'_item']) ? ',' : '').$row[''.$field.'_item'];
		}

		$conn->row_free();

		$row = $tmp;
		$row['cnt'] = $rowCount;
		UnSet($tmp);
	}else{
		$sql = 'SELECT *
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND '.$field.'_ent_dt = \''.$_GET['entDt'].'\'
				   AND '.$field.'_seq    = \''.$_GET['seq'].'\'';
		
		$row = $conn->get_array($sql);
	}
	
	$sql = 'SELECT cd1 AS cd
			,      nm1 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd1 = \''.SubStr($row[$field.'_item_cd'],0,2).'\'
			 LIMIT 1';
	$cate1 = $conn->get_array($sql);

	$sql = 'SELECT cd2 AS cd
			,      nm2 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd2 = \''.SubStr($row[$field.'_item_cd'],2,2).'\'
			 LIMIT 1';
	$cate2 = $conn->get_array($sql);

	$sql = 'SELECT cd3 AS cd
			,      nm3 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd3 = \''.SubStr($row[$field.'_item_cd'],4,3).'\'';
	$cate3 = $conn->get_array($sql);
	
	
	//결제란설정
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$code.'\'';
	$sign = $conn->get_array($sql);
	
	$signCnt = $sign['line_cnt'];
	$signTxt = Explode('|',$sign['subject']);
	
	
	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=test.xlsx" );
	

	
	
	$objPHPExcel = new PHPExcel();
	$sheetIndex = 0;
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
	$sheet = $objPHPExcel->getActiveSheet();


	$sheet->setTitle($title);
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(1.5);
	$sheet->getPageMargins()->setRight(0.0);
	$sheet->getPageMargins()->setLeft(0.0);
	$sheet->getPageMargins()->setBottom(0.5);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getHeaderFooter()->setOddFooter("&C&14&\"Bold\"(주)케어비지트(070.4044.1312)\n&C&9입금계좌:기업은행(803-215-00151-2) 예금주:(주)굿이오스1");
	$sheet->getHeaderFooter()->setOddFooter($footer);


	//스타일
	$lastCol = 40;
	$widthCol= 2.2;
	include("../excel/init.php");
	include_once("../excel/style.php");
	//$sheet->getColumnDimension("AO")->setWidth(4.5);

	
	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕
	
	//$rH = $rowH * $fontSize / $defFontSize;
	
	//초기화
	$defFontSize = 10;
	$rowH = 15;
	$rowNo = 0;


	$fontSize = 20;
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

	
	$cellT = 'X';
	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$cellT.($rowNo+3), 'val'=>$title, 'border'=>'TNRNBNLN' ) );

	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	if ($signCnt > 0){
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
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*1.5);
	
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);	
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"결의서 번호:".str_replace('-','', $row[$field.'_acct_dt']).'-'.$row['proof_no'], 'H'=>'L') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row['proof_year']."년도( 일반 )회계", 'border'=>'TNRNBNLN', 'H'=>'R') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"세입과목", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>"") );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>"발의", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row[$field.'_acct_dt'], 'H'=>'C') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>"관", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$cate1['nm'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"지 출 원", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>"", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>"결재", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row[$field.'_acct_dt'], 'H'=>'C') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>"항", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$cate2['nm'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"사   업", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>$cate3['nm'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>"출납", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row[$field.'_acct_dt'], 'H'=>'C') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>"목", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>$cate3['nm'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'T'=>'O'.$rowNo, 'val'=>"작 성 자", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'P'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>"", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AE'.$rowNo, 'val'=>"등기", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AF'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>$row[$field.'_acct_dt'], 'H'=>'C') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>"No", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'I'.$rowNo, 'val'=>"계정과목(세목)", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'J'.$rowNo, 'T'=>'T'.$rowNo, 'val'=>"적요(거래처)", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'U'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>"금액", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AI'.$rowNo, 'val'=>"자금원천", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AJ'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"상대계정", 'H'=>'C') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>1, 'H'=>'C') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'I'.$rowNo, 'val'=>$cate3['nm'], 'H'=>'C') );
	$sheet->SetData( Array('F'=>'J'.$rowNo, 'T'=>'T'.$rowNo, 'val'=>nl2br($row[$field.'_item']), 'H'=>'L') );
	$sheet->SetData( Array('F'=>'U'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>Number_Format($row[$field.'_amt']+$row[$field.'_vat']), 'H'=>'R') );
	$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AI'.$rowNo, 'val'=>"", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'AJ'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"", 'H'=>'C') );

	for($i=0; $i<9; $i++){
		
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
		$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.$rowNo, 'val'=>"", 'H'=>'C') );
		$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'I'.$rowNo, 'val'=>"", 'H'=>'C') );
		$sheet->SetData( Array('F'=>'J'.$rowNo, 'T'=>'T'.$rowNo, 'val'=>"", 'H'=>'C') );
		$sheet->SetData( Array('F'=>'U'.$rowNo, 'T'=>'AB'.$rowNo, 'val'=>"", 'H'=>'C') );
		$sheet->SetData( Array('F'=>'AC'.$rowNo, 'T'=>'AI'.$rowNo, 'val'=>"", 'H'=>'C') );
		$sheet->SetData( Array('F'=>'AJ'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>"", 'H'=>'C') );
	}

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*2);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"금액", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>Number_Format($row[$field.'_amt']+$row[$field.'_vat']).'원('.$myF->no2Kor($row[$field.'_amt']+$row[$field.'_vat']).'원)', 'H'=>'C') );
	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH*5);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"비고", 'H'=>'C') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'AO'.$rowNo, 'val'=>nl2br($row['other']), 'H'=>'L') );


	function lfDrawText($sheet, $cellId, $rowNo, $text = "", $para = null){
		$fromCell = $cellId.$rowNo;

		if ($para['toCell']){
			$toCell = $para['toCell'];
		}else{
			$toCell = $cellId.($rowNo+$para['toRow']);
		}

		
		$sheet->getCell($fromCell)->setValue($text);
		$sheet->getStyle($fromCell)->getAlignment()->setWrapText(true);

		if ($para['H'] == 'L'){
			$para['H'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		}else if ($para['H'] == 'R'){
			$para['H'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
		}else{
			$para['H'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
		}
		
		$sheet->getStyle($fromCell.':'.$toCell)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal($para['H']);
		$sheet->mergeCells($fromCell.':'.$toCell);
		$sheet->getStyle($fromCell.':'.$toCell)->applyFromArray(fnStyle($para['border']));

		if ($para['color']) $sheet->setFontColor($fromCell, $para['color']);
		if ($para['bgcolor']) $sheet->SetBGColor($fromCell,$para['bgcolor']);
		if ($para['format']) $sheet->getStyle($cellF.$rowNo)->getNumberFormat()->setFormatCode($para['format']);
	}


	//$objPHPExcel->getActiveSheet()->freezePane('A4');

	//$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	//$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	//$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>
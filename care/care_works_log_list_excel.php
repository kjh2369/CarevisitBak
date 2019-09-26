<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("실적내용").'_'.Date('Ymdims').".xlsx" );

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle($orgNm." 실적내용");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	$sheet->getPageMargins()->setTop(0.5);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.5);

	//스타일
	include("../excel/init.php");
	include_once("../excel/style.php");

	$sheetIndex = 0;
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
	$sheet = $objPHPExcel->getActiveSheet();

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕

	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$sheet->getColumnDimension('A')->setWidth(6); //No
	$sheet->getColumnDimension('B')->setWidth(15); //일시
	$sheet->getColumnDimension('C')->setWidth(12); //대상자
	$sheet->getColumnDimension('D')->setWidth(12); //생년월일
	$sheet->getColumnDimension('E')->setWidth(30); //서비스
	$sheet->getColumnDimension('F')->setWidth(30); //자원
	$sheet->getColumnDimension('G')->setWidth(10); //담당
	$sheet->getColumnDimension('H')->setWidth(50); //업무내용
	$sheet->getColumnDimension('I')->setWidth(30); //비고

	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);


	$rH = $rowH * $fontSize / $defFontSize;

	$cellF = 'A';
	$cellT = 'I';
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(40);
	$sheet->getCell($cellF.$rowNo)->setValue($orgNm." 실적내용");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);


	$rowNo ++;

	$rH = $rowH * $fontSize / $defFontSize;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);


	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'No', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>'일시', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>'대상자', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>'생년월일', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>'서비스', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>'자원', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>'담당', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>'업무내용', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'I'.$rowNo, 'val'=>'비고', 'backcolor'=>'EEECE1') );


	$fontSize = 9;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$IsExcelClass = true;
	include_once('./care_works_log_list_search.php');

	$objPHPExcel->getActiveSheet()->freezePane('A3');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");


	/*
		header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
		header( "Pragma: public" );
		header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );?>
		<div style="text-align:center; font-size:20px;"><?=$orgNm;?> 실적내용</div>
		<div style="text-align:right; font-size:13px;">조회기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></div>
		<table border="1">
			<tr style="height:30px;">
				<th style="width:50px; background-color:#EAEAEA;">No</th>
				<th style="width:100px; background-color:#EAEAEA;">일시</th>
				<th style="width:100px; background-color:#EAEAEA;">대상자</th>
				<th style="width:100px; background-color:#EAEAEA;">생년월일</th>
				<th style="width:150px; background-color:#EAEAEA;">서비스</th>
				<th style="width:150px; background-color:#EAEAEA;">자원</th>
				<th style="width:100px; background-color:#EAEAEA;">담당</th>
				<th style="width:200px; background-color:#EAEAEA;">업무내용</th>
				<th style="width:100px; background-color:#EAEAEA;">비고</th>
			</tr><?
			$IsExcel = true;
			include_once('./care_works_log_list_search.php');?>
		</table><?
	*/

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$type   = $_POST['type'];
	$SR		= $_POST['sr'];
	$year	= str_replace('-','',$_POST['year']);
	$month	= str_replace('-','',$_POST['month']);
	$title	= '사업계획서';

	require_once("../excel/PHPExcel.php");

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("사업계획서")."_".Date('Ymdims').".xlsx" );


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle($year."년 사업계획서");
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
	
	$sheet->getColumnDimension('A')->setWidth(3); //사업분류
	$sheet->getColumnDimension('B')->setWidth(3); //사업분류
	$sheet->getColumnDimension('C')->setWidth(12); //세부사업명
	$sheet->getColumnDimension('D')->setWidth(6); //계획(목표)
	$sheet->getColumnDimension('E')->setWidth(6); //계획(예산)
	$sheet->getColumnDimension('F')->setWidth(18); //계획(횟수)
	$sheet->getColumnDimension('G')->setWidth(20); //사업내용
	$sheet->getColumnDimension('H')->setWidth(20); //기대효과
	$sheet->getColumnDimension('I')->setWidth(15); //수행 및 평가도구
	
	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'H'.$rowNo, 'val'=>$title, 'bold'=>true, 'border'=>'TNRNBNLN') );

	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;

	
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'B'.($rowNo+1), 'val'=>'사업분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+1), 'val'=>'세부사업명', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>'계획', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.($rowNo+1), 'val'=>'목표', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'E'.($rowNo+1), 'val'=>'예산', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'F'.($rowNo+1), 'val'=>'횟수', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'T'=>'G'.($rowNo+1),'val'=>'사업내용', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'T'=>'H'.($rowNo+1),'val'=>'기대효과', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'I'.$rowNo, 'T'=>'I'.($rowNo+1),'val'=>'수행 및 평가도구', 'backcolor'=>'EEECE1') );

	$fontSize = $defFontSize;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;

	//데이타
	$IsExcelClass = true;
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	
	$rowNo ++;

	include_once('./care_find.php');
	//$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	//$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'테스트', 'backcolor'=>'EEECE1') );

	$fontSize = 15;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.($rowNo+1), 'val'=>$orgNm, 'bold'=>true, 'border'=>'TNRNBNLN') );


	$objPHPExcel->getActiveSheet()->freezePane('A4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");
	
	include_once('../inc/_db_close.php');
?>
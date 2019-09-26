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
	$title	= '소속기관별현황';

	require_once("../excel/PHPExcel.php");

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("소속기관별")."_".Date('Ymdims').".xlsx" );


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("소속기관별");
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
	
	$sheet->getColumnDimension('A')->setWidth(7); //No
	$sheet->getColumnDimension('B')->setWidth(15); //소속기관
	$sheet->getColumnDimension('C')->setWidth(15); //대분류
	$sheet->getColumnDimension('D')->setWidth(20); //중분류
	$sheet->getColumnDimension('E')->setWidth(20); //소분류
	$sheet->getColumnDimension('F')->setWidth(12); //서비스명
	$sheet->getColumnDimension('G')->setWidth(7); //서비스횟수
	
	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>$title, 'bold'=>true, 'border'=>'TNRNBNLN') );

	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.$rowNo, 'val'=>$year.' 년 '.$month.' 월', 'H'=>'L', 'border'=>'TNRNBNLN') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>'출력일 : '.Date('Y.m.d'), 'H'=>'R', 'border'=>'TNRNBNLN') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'NO', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>'소속기관', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>'대분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>'중분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>'소분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>'서비스명', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>'횟수', 'backcolor'=>'EEECE1') );


	$fontSize = $defFontSize;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;


	//데이타
	$IsExcelClass = true;
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	
	include_once('./care_find.php');
	//$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	//$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'테스트', 'backcolor'=>'EEECE1') );

	$fontSize = 15;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>$orgNm, 'bold'=>true, 'border'=>'TNRNBNLN') );


	$objPHPExcel->getActiveSheet()->freezePane('A4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");
	/*
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=".$myF->euckr("서비스이용현황").".xls" );
		header( "Content-Description: ".$myF->euckr("서비스이용현황"));
		header( "Pragma: no-cache" );
		header( "Expires: 0" );?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<div style="font-size:17px; text-align:center;"><?=$title;?></div>
		<table border="0">
			<tr>
				<td colspan="4" style="border:none; text-align:left;">기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></td>
				<td colspan="2" style="border:none; text-align:right;">출력일 : <?=Date('Y.m.d');?></td>
			</tr>
			<tr>
				<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">대분류</th>
				<th style="width:140px; background-color:#EAEAEA; border:0.5pt solid BLACK;">중분류</th>
				<th style="width:140px; background-color:#EAEAEA; border:0.5pt solid BLACK;">소분류</th>
				<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">서비스명</th>
				<th style="width:75px; background-color:#EAEAEA; border:0.5pt solid BLACK;">이용자수</th>
				<th style="width:76px; background-color:#EAEAEA; border:0.5pt solid BLACK;">서비스횟수</th>
			</tr><?
			$IsExcel = true;
			include_once('./care_svc_use_stat_search.php');?>
		</table><?
	*/

	include_once('../inc/_db_close.php');
?>
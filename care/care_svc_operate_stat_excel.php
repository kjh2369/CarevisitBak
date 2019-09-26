<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$title	= $year.'년 '.$month.'월 서비스운영현황';

	if ($SR == 'S'){
		$title .= '(재가지원)';
	}else if ($SR == 'R'){
		$title .= '(자원연계)';
	}else{
		exit;
	}

	require_once("../excel/PHPExcel.php");

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("서비스이용현황")."_".Date('Ymdims').".xlsx" );


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("기관계약현황");
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

	$sheet->getColumnDimension('A')->setWidth(10); //일자
	$sheet->getColumnDimension('B')->setWidth(25); //서비스
	$sheet->getColumnDimension('C')->setWidth(13); //대상자수
	$sheet->getColumnDimension('D')->setWidth(50); //비고

	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$title, 'bold'=>true, 'border'=>'TNRNBNLN') );

	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>'출력일 : '.Date('Y.m.d'), 'H'=>'R', 'border'=>'TNRNBNLN') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'일자', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>'서비스', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>'대상자수', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>'비고', 'backcolor'=>'EEECE1') );


	$fontSize = $defFontSize;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;


	//데이타
	$IsExcelClass = true;
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	include_once('./care_svc_operate_stat_search.php');


	$fontSize = 15;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$orgNm, 'bold'=>true, 'border'=>'TNRNBNLN') );


	$objPHPExcel->getActiveSheet()->freezePane('A4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");
	/*
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=".$myF->euckr("서비스운영현황(일자별)").".xls" );
		header( "Content-Description: ".$myF->euckr("서비스운영현황(일자별)"));
		header( "Pragma: no-cache" );
		header( "Expires: 0" );?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<div style="font-size:17px; text-align:center;"><?=$title;?></div>
		<div style="text-align:right;">출력일 : <?=Date('Y.m.d');?></div>
		<table border="1">
			<tr>
				<th style="width:100px; background-color:#EAEAEA;">일자</th>
				<th style="width:200px; background-color:#EAEAEA;">서비스</th>
				<th style="width:100px; background-color:#EAEAEA;">대상자수</th>
				<th style="width:230px; background-color:#EAEAEA;">비고</th>
			</tr><?
			$IsExcel = true;
			include_once('./care_svc_operate_stat_search.php');?>
		</table><?
	*/

	include_once('../inc/_db_close.php');
?>
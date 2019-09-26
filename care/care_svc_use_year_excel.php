<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$title	= $year.'년 서비스이용현황';

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

	$sheet->getColumnDimension('A')->setWidth(15); //대분류
	$sheet->getColumnDimension('B')->setWidth(15); //중분류
	$sheet->getColumnDimension('C')->setWidth(20); //소분류
	$sheet->getColumnDimension('D')->setWidth(20); //서비스명

	$cellF = 'D';
	for($i=0; $i<=12; $i++){
		$cellF = GetNextCellId($cellF);
		$sheet->getColumnDimension($cellF)->setWidth(8); //서비스수
	}

	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'Q'.$rowNo, 'val'=>$title, 'bold'=>true, 'border'=>'TNRNBNLN') );

	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'F'.$rowNo, 'val'=>'출력일 : '.Date('Y.m.d'), 'H'=>'R', 'border'=>'TNRNBNLN') );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>'대분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>'중분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>'소분류', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>'서비스명', 'backcolor'=>'EEECE1') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>'합계', 'backcolor'=>'EEECE1') );

	$cellF = 'E';
	for($i=1; $i<=12; $i++){
		$cellF = GetNextCellId($cellF);
		$sheet->SetData( Array('F'=>$cellF.$rowNo, 'val'=>$i.'월', 'backcolor'=>'EEECE1') );
	}


	$fontSize = $defFontSize;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);

	$rH = $rowH * $fontSize / $defFontSize;


	//데이타
	$IsExcelClass = true;
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	include_once('./care_svc_use_year_search.php');


	$fontSize = 15;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'Q'.$rowNo, 'val'=>$orgNm, 'bold'=>true, 'border'=>'TNRNBNLN') );


	$objPHPExcel->getActiveSheet()->freezePane('A4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");
	/*
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=".$myF->euckr($title).".xls" );
		header( "Content-Description: ".$myF->euckr($title));
		header( "Pragma: no-cache" );
		header( "Expires: 0" );

		$style = 'border:0.5pt solid BLACK; background-color:#EAEAEA;';?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<div style="font-size:17px; text-align:center;"><?=$title;?></div>
		<table border="0">
			<tr>
				<td colspan="17" style="border:none; text-align:right;">출력일 : <?=Date('Y.m.d');?></td>
			</tr>
			<tr>
				<th style="<?=$style;?>">대분류</th>
				<th style="<?=$style;?>">중분류</th>
				<th style="<?=$style;?>">소분류</th>
				<th style="<?=$style;?>">서비스명</th>
				<th style="<?=$style;?>">합계</th><?
				for($i=1; $i<=12; $i++){?>
					<th style="<?=$style;?>"><?=$i;?>월</th><?
				}?>
			</tr><?
			$IsExcel = true;
			include_once('./care_svc_use_year_search.php');?>
		</table><?
	*/
	include_once('../inc/_db_close.php');
?>
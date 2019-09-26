<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");



	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("기관계약현황").".xlsx" );


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
	$objPHPExcel->getDefaultStyle()->getFont()->setName("맑은고딕"); //Gulim //Batang //맑은고딕

	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;

	$sheet->getColumnDimension('A')->setWidth(6); //No
	$sheet->getColumnDimension('B')->setWidth(12); //기관기호
	$sheet->getColumnDimension('C')->setWidth(25); //기관명
	$sheet->getColumnDimension('D')->setWidth(15); //도메인
	$sheet->getColumnDimension('E')->setWidth(10); //대표자
	$sheet->getColumnDimension('F')->setWidth(12); //연락처
	$sheet->getColumnDimension('G')->setWidth(50); //주소
	$sheet->getColumnDimension('H')->setWidth(10); //연결일
	$sheet->getColumnDimension('I')->setWidth(10); //계약일
	$sheet->getColumnDimension('J')->setWidth(10); //적용일
	$sheet->getColumnDimension('K')->setWidth(10); //종료일
	$sheet->getColumnDimension('L')->setWidth(12); //계약회사
	$sheet->getColumnDimension('M')->setWidth(12); //CMS/무통장_지케어
	$sheet->getColumnDimension('N')->setWidth(12); //CMS/무통장_굿이오스
	$sheet->getColumnDimension('O')->setWidth(12); //CMS/무통장_케어비지트
	$sheet->getColumnDimension('P')->setWidth(12); //CMS/무통장_무통장
	$sheet->getColumnDimension('Q')->setWidth(12); //기관기호
	$sheet->getColumnDimension('R')->setWidth(12); //기관기호_문자포맷
	$sheet->getColumnDimension('S')->setWidth(12); //사업자등록번호
	$sheet->getColumnDimension('T')->setWidth(12); //일정등록 수급자_합계
	$sheet->getColumnDimension('U')->setWidth(12); //일정등록 수급자_요양
	$sheet->getColumnDimension('V')->setWidth(12); //일정등록 수급자_가족
	$sheet->getColumnDimension('W')->setWidth(12); //일정등록 수급자_목욕
	$sheet->getColumnDimension('X')->setWidth(12); //일정등록 수급자_간호
	$sheet->getColumnDimension('Y')->setWidth(12); //일정등록 수급자_SMS
	$sheet->getColumnDimension('Z')->setWidth(12); //일정등록 수급자_주야간보호
	$sheet->getColumnDimension('AA')->setWidth(12); //바우처 사용여부_가사
	$sheet->getColumnDimension('AB')->setWidth(12); //바우처 사용여부_노인
	$sheet->getColumnDimension('AC')->setWidth(12); //바우처 사용여부_산모
	$sheet->getColumnDimension('AD')->setWidth(12); //바우처 사용여부_장애
	$sheet->getColumnDimension('AE')->setWidth(12); //재가노인 사용여부_재가
	$sheet->getColumnDimension('AF')->setWidth(12); //재가노인 사용여부_지원
	$sheet->getColumnDimension('AG')->setWidth(12); //스마트폰
	$sheet->getColumnDimension('AH')->setWidth(12); //현재요금
	$sheet->getColumnDimension('AI')->setWidth(12); //계산요금
	$sheet->getColumnDimension('AJ')->setWidth(12); //요금차액
	$sheet->getColumnDimension('AK')->setWidth(12); //과금구분
	$sheet->getColumnDimension('AL')->setWidth(12); //조정요금관리기관
	$sheet->getColumnDimension('AM')->setWidth(30); //이메일
	$sheet->getColumnDimension('AN')->setWidth(15); //세금계산서 발행여부


	$fontSize = 20;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);


	$rH = $rowH * $fontSize / $defFontSize;

	$cellF = 'A';
	$cellT = 'AM';
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(40);
	$sheet->getCell($cellF.$rowNo)->setValue("기관계약현황");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);


	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);


	$rowNo ++;

	$rH = $rowH * $fontSize / $defFontSize;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->getRowDimension($rowNo + 1)->setRowHeight($rH);

	lfDrawText($sheet, "A", $rowNo, "No", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "B", $rowNo, "기관기호", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "C", $rowNo, "기관명", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "D", $rowNo, "도메인", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "E", $rowNo, "대표자", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "F", $rowNo, "연락처", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "G", $rowNo, "주소", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "H", $rowNo, "연결일", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "I", $rowNo, "계약일", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "J", $rowNo, "적용기간", Array('bgcolor'=>'EEECE1', 'toCell'=>'K'.$rowNo));
	lfDrawText($sheet, "J", $rowNo+1, "적용일", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "K", $rowNo+1, "종료일", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "L", $rowNo, "계약회사", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "M", $rowNo, "CMS / 무통장", Array('bgcolor'=>'EEECE1', 'toCell'=>'P'.$rowNo));
	lfDrawText($sheet, "M", $rowNo+1, "지케어", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "N", $rowNo+1, "굿이오스", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "O", $rowNo+1, "케어비지트", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "P", $rowNo+1, "무통장", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "Q", $rowNo, "기관기호", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "R", $rowNo, "기관기호", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "S", $rowNo, "사업자\n등록번호", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "T", $rowNo, "일정등록 수급자", Array('bgcolor'=>'EEECE1', 'toCell'=>'V'.$rowNo));
	lfDrawText($sheet, "T", $rowNo+1, "합계", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "U", $rowNo+1, "요양", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "V", $rowNo+1, "가족", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "W", $rowNo, "제외일정", Array('bgcolor'=>'EEECE1', 'toCell'=>'X'.$rowNo));
	lfDrawText($sheet, "W", $rowNo+1, "목욕", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "X", $rowNo+1, "간호", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "Y", $rowNo, "SMS", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "Z", $rowNo, "주야간\n보호", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AA", $rowNo, "바우처 사용여부", Array('bgcolor'=>'EEECE1', 'toCell'=>'AD'.$rowNo));
	lfDrawText($sheet, "AA", $rowNo+1, "가사", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AB", $rowNo+1, "노인", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AC", $rowNo+1, "산모", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AD", $rowNo+1, "장애", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AE", $rowNo, "재가노인 사용여부", Array('bgcolor'=>'EEECE1', 'toCell'=>'AF'.$rowNo));
	lfDrawText($sheet, "AE", $rowNo+1, "재가", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AF", $rowNo+1, "지원", Array('bgcolor'=>'EEECE1'));
	lfDrawText($sheet, "AG", $rowNo, "스마트폰", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AH", $rowNo, "현재요금", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AI", $rowNo, "계산요금", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AJ", $rowNo, "요금차액", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AK", $rowNo, "과금구분", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AL", $rowNo, "조정요금\n관리기관", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AM", $rowNo, "이메일", Array('bgcolor'=>'EEECE1', 'toRow'=>1));
	lfDrawText($sheet, "AN", $rowNo, "세금계산\n발행여부", Array('bgcolor'=>'EEECE1', 'toRow'=>1));

	$rowNo += 1;


	$fontSize = 9;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);



	$IsExcelClass = true;
	include_once('./center_CONT_search.php');



	function lfDrawText($sheet, $cellId, $rowNo, $text = "", $para = null){
		$fromCell = $cellId.$rowNo;

		if ($para['toCell']){
			$toCell = $para['toCell'];
		}else{
			$toCell = $cellId.($rowNo+$para['toRow']);
		}


		if ($para['format'] == 'text'){
			$para['format'] = '';
			$sheet->setCellValueExplicit($fromCell, $text, PHPExcel_Cell_DataType::TYPE_STRING);
		}else{
			$sheet->getCell($fromCell)->setValue($text);
		}
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


	$objPHPExcel->getActiveSheet()->freezePane('A4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save("php://output");


	include_once('../inc/_db_close.php');
?>
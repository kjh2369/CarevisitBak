<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../excel/PHPExcel.php');

	// Create new PHPExcel object

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.4);
	$sheet->getPageMargins()->setRight(0.2);
	$sheet->getPageMargins()->setLeft(0.2);
	$sheet->getPageMargins()->setBottom(0.4);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-type: charset=euc-kr');
	//header('Content-Disposition: attachment;filename="report_'.Date('YmdHis').'.xlsx"');
	header('Content-Disposition: attachment;filename="report_'.Date('YmdHis').'.xls"');
	header('Content-Description: test');
	header('Cache-Control: max-age=0');

	//ini_set("memory_limit","32M");

	$orgNo		= $_SESSION['userCenterCode'];
	$SR			= $_POST['SR'];
	$type		= $_POST['type'];
	$area		= $_POST['area'];
	$year		= $_POST['year'];
	$month		= IntVal($_POST['month']);
	$yymm		= $year.($month < 10 ? '0' : '').$month;
	$prtGbn		= $_POST['prtGbn']; //1:기본, 2:상세
	$quarter	= $_POST['quarter'];
	$storeNm	= $conn->_storeName($orgNo);

	if ($area){
		$sql = 'SELECT	area_nm
				FROM	care_area
				WHERE	area_cd = \''.$area.'\'';

		$areaNm = $conn->get_data($sql);
	}

	$sql = 'SELECT	suga_cd AS cd
			,		unit_gbn AS gbn
			FROM	care_suga_unit
			WHERE	year = \''.$year.'\'';

	$unit = $conn->_fetch_array($sql,'cd');

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Batang'); //Gulim
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	include_once('../excel/style.php');

	if ($type == '51_M'){
		//분기(중분류)
		include_once('./care_report_excel_M.php');
	}else if ($type == '51_M_S' || $type == '51_M_D'){
		//분기 세부사업
		include_once('./care_report_excel_M_S.php');
	}else if ($type == '51_D'){
		//월보고서
		include_once('./care_report_excel_D.php');
	}

	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	require_once('../inc/_db_close.php');
?>
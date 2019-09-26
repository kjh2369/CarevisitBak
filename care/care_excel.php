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
	header('Content-Disposition: attachment;filename="report_'.Date('YmdHis').'.xls"');
	header('Content-Description: test');
	header('Cache-Control: max-age=0');

	//ini_set("memory_limit","32M");

	$code		= $_SESSION['userCenterCode'];
	$type		= $_POST['type'];
	$area		= $_POST['area'];
	$year		= $_POST['year'];
	$month		= $myF->monthStr($_POST['month']);
	$prtGbn		= $_POST['prtGbn']; //1:기본, 2:상세
	$storeNm	= $conn->_storeName($code);
	$isExcel	= true;

	if ($area){
		$sql = 'SELECT	area_nm
				FROM	care_area
				WHERE	area_cd = \''.$area.'\'';

		$areaNm = $conn->get_data($sql);
	}

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Batang'); //Gulim
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(17);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	//스타일설정
	$style[1] = array(
		'borders' => array(
			'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
				'color' => array('argb' => '000000'),
			),
			'vertical' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
		),
	);

	$style[2] = array(
		'borders' => array(
			'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'vertical' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => '000000'),
			),
		),
	);

	if ($type == '51_MONTH'){
		include_once('./care_excel_51_month.php');
	}else if (SubStr($type,0,9) == '51_MIDDLE'){
		$prtGbn = '1';
		include_once('./care_excel_51_middle.php');
	}else if (SubStr($type,0,10) == '51_QUARTER'){
		if ($prtGbn == '1'){
			include_once('./care_excel_51_quarter.php');
		}else{
			include_once('./care_excel_51_quarter_detail.php');
		}
	}else if ($type == '53_ASS_MIDDLE'){
		$prtGbn = '1';
		include_once('./care_excel_51_middle.php');
	}else if ($type == '52_ASS_SVC'){
		$prtGbn = '1';
		include_once('./care_excel_51_quarter.php');
	}else if ($type == '55_AREA'){
		$prtGbn = '1';
		include_once('./care_excel_55_area.php');
	}else{
		echo 'ERROR';
		exit;
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	require_once('../inc/_db_close.php');
?>
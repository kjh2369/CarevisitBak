<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$target	= $ed->de($_POST['target']);
	$date	= $_POST['date'];

	if ($SR == 'S'){
		$title = '재가지원';
	}else if ($SR == 'R'){
		$title = '자원연계';
	}else{
		exit;
	}

	//직원명
	$sql = 'SELECT	m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode	= \''.$orgNo.'\'
			AND		m02_yjumin	= \''.$jumin.'\'
			ORDER	BY m02_mkind
			LIMIT	1';

	$memNm = $conn->get_data($sql);

	//대상자
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$target.'\'';

	$tgNm = $conn->get_data($sql);

	$sql = 'SELECT	mp_gbn
			FROM	client_his_svc
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$SR.'\'
			AND		jumin	 = \''.$target.'\'
			AND		from_dt	<= \''.$date.'\'
			AND		to_dt	>= \''.$date.'\'';

	$mpGbn = $conn->get_data($sql);

	//제공일시
	$sql = 'SELECT	from_time
			,		to_time
			FROM	care_work_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$sugaCd.'\'
			AND		mem_cd	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		jumin	= \''.$target.'\'';

	$row = $conn->get_array($sql);

	$fromTime = $myF->timeStyle($row['from_time']);
	$toTime = $myF->timeStyle($row['to_time']);

	Unset($row);

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("업무일지($date)").".xls" );
	header( "Content-Description: ".$myF->euckr("업무일지($date)"));
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("업무일지($title)");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.8);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.8);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	//스타일
	include("../excel/style.php");
	include("../excel/init.php");

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim
	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(17);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$sheet->getRowDimension(1)->setRowHeight(30);
	$sheet->getCell('A1')->setValue('업무일지('.$title.')');
	$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A1:AF1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A1:AF1');
	//$sheet->getStyle('A1:AF1')->applyFromArray(fnStyle());

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$sheet->getRowDimension(2)->setRowHeight(15);
	$sheet->getCell('A2')->setValue('출력일자 : '.Date('Y.m.d'));
	$sheet->getStyle('A2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A2:AF2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$sheet->mergeCells('A2:AF2');

	$sheet->getRowDimension(3)->setRowHeight(15);
	$sheet->getCell('A3')->setValue('직원명');
	$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A3:C3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A3:C3');
	$sheet->getStyle('A3:C3')->applyFromArray(fnStyle('LT'));

	$sheet->getCell('D3')->setValue($memNm);
	$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
	$sheet->getStyle('D3:AF3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('D3:AF3');
	$sheet->getStyle('D3:AF3')->applyFromArray(fnStyle('TR'));

	$sheet->getRowDimension(4)->setRowHeight(15);
	$sheet->getCell('A4')->setValue('대상자');
	$sheet->getStyle('A4')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A4:C4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A4:C4');
	$sheet->getStyle('A4:C4')->applyFromArray(fnStyle('L'));

	$sheet->getCell('D4')->setValue($tgNm.($mpGbn == 'Y' ? '[중점관리]' : '[일반]'));
	$sheet->getStyle('D4')->getAlignment()->setWrapText(true);
	$sheet->getStyle('D4:AF4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('D4:AF4');
	$sheet->getStyle('D4:AF4')->applyFromArray(fnStyle('R'));

	$sheet->getRowDimension(5)->setRowHeight(15);
	$sheet->getCell('A5')->setValue('제공일시');
	$sheet->getStyle('A5')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A5:C5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A5:C5');
	$sheet->getStyle('A5:C5')->applyFromArray(fnStyle('LB'));

	$sheet->getCell('D5')->setValue($myF->dateStyle($date,'.').' '.$fromTime.' ~ '.$toTime);
	$sheet->getStyle('D5')->getAlignment()->setWrapText(true);
	$sheet->getStyle('D5:AF5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('D5:AF5');
	$sheet->getStyle('D5:AF5')->applyFromArray(fnStyle('RB'));


	$sheet->getRowDimension(6)->setRowHeight(3);
	$sheet->getCell('A6')->setValue('');
	$sheet->mergeCells('A6:AF6');


	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$sheet->getRowDimension(7)->setRowHeight(20);
	$sheet->getCell('A7')->setValue('서비스');
	$sheet->getStyle('A7')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A7:I7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A7:I7');
	$sheet->getStyle('A7:I7')->applyFromArray(fnStyle('TL'));

	$sheet->getCell('J7')->setValue('제공내용');
	$sheet->getStyle('J7')->getAlignment()->setWrapText(true);
	$sheet->getStyle('J7:AF7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('J7:AF7');
	$sheet->getStyle('J7:AF7')->applyFromArray(fnStyle('TR'));

	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$sql = 'SELECT	c.sub_key
			,		a.seq
			,		a.name
			,		c.contents
			,		c.pic_path
			,		c.pic_name
			FROM	care_work_log_item AS a
			LEFT	JOIN	care_work_log AS b
					ON		b.org_no	= a.org_no
					AND		b.org_type	= a.org_type
					AND		b.suga_cd	= a.suga_cd
					AND		b.mem_cd	= \''.$jumin.'\'
					AND		b.date		= \''.$date.'\'
					AND		b.jumin		= \''.$target.'\'
					AND		b.del_flag	= \'N\'
			LEFT	JOIN	care_work_log_sub AS c
					ON		c.org_no	= a.org_no
					AND		c.org_type	= a.org_type
					AND		c.sub_key	= b.sub_key
					AND		c.seq		= a.seq
			WHERE	a.org_no	 = \''.$orgNo.'\'
			AND		a.org_type	 = \''.$SR.'\'
			AND		a.suga_cd	 = \''.$sugaCd.'\'
			AND		a.from_dt	<= \''.$date.'\'
			AND		a.to_dt		>= \''.$date.'\'
			AND     a.del_flag   = \'N\'
			ORDER	BY a.order_no';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	/*
	$tmpWidth['A'] = $sheet->getColumnDimension('A')->getWidth();
	$tmpWidth['J'] = $sheet->getColumnDimension('J')->getWidth();

	$sheet->getColumnDimension('A')->setWidth(24.3);
	$sheet->getColumnDimension('J')->setWidth(62.1);
	*/

	$firstNo = 8;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$no = $i * 2 + $firstNo;

		$brCnt['A'] = GetRowcount($row['name']) * 12.75 + 2.25;
		$brCnt['J'] = GetRowcount(StripSlashes($row['contents'])) * 12.75 + 2.25;

		if ($brCnt['A'] > $brCnt['J']){
			$cellH = $brCnt['A'];
		}else{
			$cellH = $brCnt['J'];
		}

		//$sheet->getCell('A'.$no)->setValue($row['name']);
		$sheet->mergeCells('A'.$no.':I'.($no+1));
		$sheet->getStyle('A'.$no.':I'.($no+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A'.$no.':I'.($no+1))->getAlignment()->setWrapText(true);
		$sheet->getStyle('A'.$no.':I'.($no+1))->applyFromArray(fnStyle('L'));
		$sheet->setCellValue('A'.$no,$row['name']);

		//$sheet->getCell('J'.$no)->setValue(StripSlashes($row['contents']));
		$sheet->mergeCells('J'.$no.':AF'.$no);
		$sheet->getStyle('J'.$no.':AF'.$no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('J'.$no.':AF'.$no)->getAlignment()->setWrapText(true);
		$sheet->getStyle('J'.$no.':AF'.$no)->applyFromArray(fnStyle('R'));
		$sheet->setCellValue('J'.$no,StripSlashes($row['contents']));

		$sheet->getRowDimension($no)->setRowHeight($cellH);

		$no ++;

		$sheet->mergeCells('J'.$no.':AF'.$no);
		$sheet->getStyle('J'.$no.':AF'.$no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('J'.$no.':AF'.$no)->getAlignment()->setWrapText(true);
		$sheet->getStyle('J'.$no.':AF'.$no)->applyFromArray(fnStyle('R'));

		if ($row['pic_path']){
			$tmpH = '';

			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('IMAGE');
			$objDrawing->setDescription('IMAGE');
			$objDrawing->setPath($row['pic_path']);	// filesystem reference for the image file //'./images/phpexcel_logo.gif'
			#$objDrawing->setHeight(70);	// sets the image height to 36px (overriding the actual image height);
			$tmpH = $objDrawing->getHeight();
			$objDrawing->setWidth(380);
			$tmpH .= '/'.$objDrawing->getHeight();
			$objDrawing->setCoordinates('J'.$no);	// pins the top-left corner of the image to cell D24
			$objDrawing->setOffsetX(5);	// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
			$objDrawing->setOffsetY(5);
			$objDrawing->setWorksheet($sheet);

			$sheet->getRowDimension($no)->setRowHeight(($objDrawing->getHeight() + 10) * 0.7528125);
		}
	}

	$conn->row_free();

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(17);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);
	$sheet->getCell('A'.($no+1))->setValue($_SESSION['userCenterName']);
	$sheet->getStyle('A'.($no+1))->getAlignment()->setWrapText(true);
	$sheet->getStyle('A'.($no+1).':AF'.($no+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A'.($no+1).':AF'.($no+1));
	$sheet->getRowDimension($no+1)->setRowHeight(30);
	$sheet->getStyle('A'.($no+1).':AF'.($no+1))->applyFromArray(fnStyle('TLNRNBN'));

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>
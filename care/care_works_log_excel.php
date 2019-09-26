<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");

	//ini_set('memory_limit', '3072M');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$gbn	= $_POST['gbn'];
	$weekly	= Array(0=>'<span style="color:RED;">일</span>',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'<span style="color:BLUE;">토</span>');
	$gender	= Array('남'=>'<span style="color:BLUE;">남</span>', '여'=>'<span style="color:RED;">여</span>');

	
	$sql = 'SELECT	a.date
			,		a.time
			,		a.seq
			,		a.jumin AS jumin_cd
			,		m03_name AS name
			,		a.suga_cd
			,		c.suga_nm
			,		a.res_cd
			,		a.res_nm
			,		a.mem_cd
			,		a.mem_nm
			,		a.week
			FROM	(
					SELECT	t01_sugup_date AS date
					,		t01_sugup_fmtime AS time
					,		t01_sugup_seq AS seq
					,		t01_jumin AS jumin
					,		t01_suga_code1 AS suga_cd
					,		t01_yoyangsa_id1 AS res_cd
					,		t01_yname1 AS res_nm
					,		t01_yoyangsa_id2 AS mem_cd
					,		t01_yname2 AS mem_nm
					,		t01_sugup_yoil AS week
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_mkind	= \''.$SR.'\'
					AND		t01_del_yn	= \'N\'
					AND		t01_sugup_date BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
					) AS a
			INNER	JOIN	m03sugupja AS b
					ON		m03_ccode = \''.$orgNo.'\'
					AND		m03_mkind = \'6\'
					AND		m03_jumin = a.jumin
			INNER	JOIN	care_suga AS c
					ON		c.org_no	= \''.$orgNo.'\'
					AND		c.suga_sr	= \''.$SR.'\'
					AND		CONCAT(c.suga_cd, c.suga_sub)	 = a.suga_cd
					AND		REPLACE(c.from_dt,\'-\',\'\')	<= a.date
					AND		REPLACE(c.to_dt,\'-\',\'\')		>= a.date
			ORDER	BY date, CASE WHEN mem_nm != \'\' THEN 1 ELSE 2 END, mem_nm, name';
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		//대상자 주민번호조회
		$sql = 'SELECT	LEFT(CONCAT(IFNULL(jumin, '.$row['jumin_cd'].'), \'0000000000000\'), 13) AS jumin
				FROM    mst_jumin AS d
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$row['jumin_cd'].'\'';
		$re_cd   = $conn->get_data($sql); 
		
		$key = $row['date'].'_'.$row['mem_cd'];
		$cd	 = $row['jumin_cd'];
	
		if (!$data[$key]){
			 $data[$key]['date'] = $row['date'];
			 $data[$key]['name'] = $row['mem_nm'];
			 $data[$key]['cnt']	 = 0;
		}

		if (!$data[$key]['list'][$cd]){
			 $data[$key]['list'][$cd]['name']	= $row['name'];
			 $data[$key]['list'][$cd]['gender']	= $myF->issToGender($re_cd);
			 $data[$key]['list'][$cd]['cnt']	= 0;

			 $data[$key]['cnt'] ++;
		}

		
		$sql = 'SELECT	contents AS cont_new, pic_nm AS pic_new, file_path AS path_new
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		jumin		= \''.$row['jumin_cd'].'\'
				AND		suga_cd		= \''.$row['suga_cd'].'\'
				AND		resource_cd	= \''.$row['res_cd'].'\'
				AND		mem_cd		= \''.$row['mem_cd'].'\'
				AND		date		= \''.$row['date'].'\'
				';
		
		$R = $conn->get_array($sql);

		
		if ($R['cont_new']){
			$cont = StripSlashes($R['cont_new']);
			$pic = $R['pic_new'];
			$path = $R['path_new'];
		}else{
			$sql = 'SELECT	content AS cont_old
				,		picture AS pic_old
				FROM    care_result
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		jumin		= \''.$row['jumin_cd'].'\'
				AND		date		= \''.$row['date'].'\'
				AND		time		= \''.$row['time'].'\'
				AND		seq			= \''.$row['seq'].'\'
				AND		no			= \'1\'';
			$rst = $conn->get_array($sql);
			
			$cont = StripSlashes($rst['cont_old']);
			$pic = $rst['pic_old'];
			$path = '../care/pic/'.$pic;
		}

		unset($R);


		$data[$key]['list'][$cd]['list'][] = Array(
			'suga'	=>$row['suga_nm']
		,	'cont'	=>$cont
		,	'pic'	=>$pic
		,	'path'	=>$path
		);

		$data[$key]['list'][$cd]['cnt'] ++;
	}

	$conn->row_free();




	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("업무일지").".xls" );

	// Create new PHPExcel object

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("업무일지");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getPageSetup()->setFitToPage(true);
	//$sheet->getPageSetup()->setFitToWidth(1);
	//$sheet->getPageSetup()->setFitToHeight(0);

	$sheet->getPageMargins()->setTop(0.5);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.5);

	//스타일
	//$lastCol = 47;
	//$widthCol= 2;
	//include("../excel/init.php");
	include_once("../excel/style.php");

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);




	$sheetIndex = 0;

	foreach($data as $key => $R){
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		$sheet = $objPHPExcel->getActiveSheet();


		//초기화
		$defFontSize = 9;
		$rowH = 15;
		$rowNo = 0;


		//간격
		$sheet->getColumnDimension('A')->setWidth(5.8);
		$sheet->getColumnDimension('B')->setWidth(8.4);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(40.4);
		$sheet->getColumnDimension('E')->setWidth(4.9);
		$sheet->getColumnDimension('F')->setWidth(9);
		$sheet->getColumnDimension('G')->setWidth(9);


		$sheet->setTitle($myF->dateStyle($R['date'],'.').' '.$R['name']);
		//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(1);
		$sheet->getPageMargins()->setRight(0.0);
		$sheet->getPageMargins()->setLeft(0.0);
		$sheet->getPageMargins()->setBottom(0.5);
		$sheet->getPageSetup()->setHorizontalCentered(true);


		$fontSize = $defFontSize;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		$rH = $rowH * $fontSize / $defFontSize * 1.3;

		$cellF = 'E';
		$cellT = 'E';
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->getCell($cellF.$rowNo)->setValue("결\n재");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'F';
		$cellT = 'F';
		$sheet->getCell($cellF.$rowNo)->setValue("담당자");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'G';
		$cellT = 'G';
		$sheet->getCell($cellF.$rowNo)->setValue("기관장");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');


		$fontSize = 20;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		$rH = $rowH * $fontSize / $defFontSize;

		$cellF = 'A';
		$cellT = 'D';
		$sheet->getCell($cellF.$rowNo)->setValue("업 무 일 지");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));


		$cellF = 'F';
		$cellT = 'F';
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->getCell($cellF.$rowNo)->setValue("");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

		$cellF = 'G';
		$cellT = 'G';
		$sheet->getCell($cellF.$rowNo)->setValue("");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());


		$rowNo ++;


		$fontSize = $defFontSize;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		$rH = $rowH * $fontSize / $defFontSize * 1.3;


		$cellF = 'A';
		$cellT = 'C';
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->getCell($cellF.$rowNo)->setValue("일자");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');


		$cellF = 'D';
		$cellT = 'D';
		$sheet->getCell($cellF.$rowNo)->setValue("방문자");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'E';
		$cellT = 'G';
		$sheet->getCell($cellF.$rowNo)->setValue("대상인원");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');


		$fontSize = $defFontSize;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

		$rH = $rowH * $fontSize / $defFontSize * 1.3;


		$cellF = 'A';
		$cellT = 'C';
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->getCell($cellF.$rowNo)->setValue($myF->dateStyle($R['date'],'KOR'));
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());


		$cellF = 'D';
		$cellT = 'D';
		$sheet->getCell($cellF.$rowNo)->setValue($R['name']);
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

		$cellF = 'E';
		$cellT = 'G';
		$sheet->getCell($cellF.$rowNo)->setValue($R['cnt'].'명');
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());


		$fontSize = $defFontSize;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		$rH = $rowH * $fontSize / $defFontSize * 1.3;


		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight(5);


		$cellF = 'A';
		$cellT = $cellF;
		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->getCell($cellF.$rowNo)->setValue("No");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'B';
		$cellT = $cellF;
		$sheet->getCell($cellF.$rowNo)->setValue("대상자");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'C';
		$cellT = $cellF;
		$sheet->getCell($cellF.$rowNo)->setValue("제공서비스");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'D';
		$cellT = $cellF;
		$sheet->getCell($cellF.$rowNo)->setValue("내용");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');

		$cellF = 'E';
		$cellT = 'G';
		$sheet->getCell($cellF.$rowNo)->setValue("사진");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
		$sheet->setBGColor($cellF.$rowNo, 'EAEAEA');


		$fontSize = $defFontSize;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

		$rH = $rowH * $fontSize / $defFontSize * 1.3;

		$no = 1;
		$rowNo ++;

		foreach($R['list'] as $cd => $R){
			$cellF = 'A';
			$cellT = $cellF;

			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->getCell($cellF.$rowNo)->setValue($no);
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)));
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)))->applyFromArray(fnStyle());

			$cellF = 'B';
			$cellT = $cellF;
			$sheet->getCell($cellF.$rowNo)->setValue($R['name'].'('.$R['gender'].')');
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)));
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+($R['cnt']-1)))->applyFromArray(fnStyle());

			$IsFirst = true;

			foreach($R['list'] as $idx => $R){
				$tmpRh = GetRowHeight(GetRowcount($R['cont'],70));

				//if ($tmpRh < 82) $tmpRh = 82;
				if ($tmpRh < $rH * 1.5) $tmpRh = $rH * 1.5;

				$cellF = 'C';
				$cellT = $cellF;
				$sheet->getRowDimension($rowNo)->setRowHeight($tmpRh);
				$sheet->getCell($cellF.$rowNo)->setValue($R['suga']);
				$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

				$cellF = 'D';
				$cellT = $cellF;
				$sheet->getCell($cellF.$rowNo)->setValue($R['cont']);
				$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

				$cellF = 'E';
				$cellT = 'G';
				$sheet->getCell($cellF.$rowNo)->setValue("");
				$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

				if (is_file($R['path'])){
					ResizeImage($R['pic'],$R['path'], $debug);
					SetImage($sheet, $R['path'], $cellF.$rowNo);
				}

				$rowNo ++;
			}

			//$rowNo += $R['cnt'];
			$no ++;
		}



		$sheetIndex ++;
	}

	$objPHPExcel->setActiveSheetIndex(0);


	function SetImage($sheet, $fileImg, $coord){
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('IMAGE');
		$objDrawing->setDescription('IMAGE');
		$objDrawing->setPath($fileImg);	// filesystem reference for the image file //'./images/phpexcel_logo.gif'

		if ($objDrawing->getWidth() > 142){
			$objDrawing->setWidth(142);
		}

		if ($objDrawing->getHeight() > 107){
			$objDrawing->setHeight(107);
		}

		$objDrawing->setCoordinates($coord);	// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX(2);	// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		$objDrawing->setOffsetY(2);
		$objDrawing->setWorksheet($sheet);
	}

	function ResizeImage($fileName,$filePath,$debug = false){
		$tmp_info = pathinfo($fileName);
		$exp_nm = strtolower($tmp_info['extension']);

		$original_path = $filePath;
		$img_w = 800;
		$img_h = 600;
		$img_s = getimagesize($original_path);

		/**************************************************

			����, ���� ������ �°� ����Ѵ�.

		**************************************************/
		if ($img_w < $img_s[0] || $img_h < $img_s[1]){
			if ($img_s[0] > $img_s[1]){
				$img_h = $img_s[1] * ($img_w / $img_s[0]);
			}else{
				$img_w = $img_s[0] * ($img_h / $img_s[1]);
			}

			switch($exp_nm){
				case 'jpg':
					$original_img = imageCreateFromJpeg($original_path);
					break;
				case 'jpeg':
					$original_img = imageCreateFromJpeg($original_path);
					break;
				case 'png':
					$original_img = imageCreateFromPng($original_path);
					break;
				case 'gif':
					$original_img = imageCreateFromGif($original_path);
					break;
				case 'bmp':
					$original_img = imageCreateFromBmp($original_path);
					break;
			}

			// �� �̹�Ʈ Ʋ�ۼ�
			$new_img = imageCreateTrueColor($img_w, $img_h);

			// ����� �Ͼ������ ����
			$trans_colour = imageColorAllocate($new_img, 255,255,255);
			imageFill($new_img, 0, 0, $trans_colour);

			// �̹��� ����
			if (imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1])){
				// �̹��� ����
				switch($exp_nm){
					case 'jpg':
						imageJpeg($new_img, $original_path);
						break;
					case 'jpeg':
						imageJpeg($new_img, $original_path);
						break;
					case 'png':
						imagePng($new_img, $original_path);
						break;
					case 'gif':
						imageGif($new_img, $original_path);
						break;
					case 'bmp':
						imageBmp($new_img, $original_path);
						break;
				}
			}else{
				if ($debug) echo $original_path.chr(13).chr(10);
			}

			// ����
			imageDestroy($new_img);
		}
	}

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	Unset($data);

	include_once("../inc/_db_close.php");
?>
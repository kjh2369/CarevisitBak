<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	require_once("../excel/PHPExcel.php");

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$title	= $year."년 청구내역(본사)";


	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($title).".xls" );


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle($title);
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.8);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.8);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	//스타일
	include_once("../excel/style.php");
	//include("../excel/init.php");

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim


	//초기화
	$defFontSize = 8;
	$rowH = 15;
	$rowNo = 0;


	//폰트설정
	$fontSize = 9;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;


	$rowNo = 1;
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$cellId = GetNextCellId();
	$sheet->getColumnDimension($cellId)->setWidth(8);
	$sheet->SetData(Array('F'=>$cellId.$rowNo,'T'=>$cellId.($rowNo+1),'val'=>"월",'backcolor'=>'EAEAEA'));
	$cellId = GetNextCellId($cellId);
	$sheet->getColumnDimension($cellId)->setWidth(8);
	$sheet->SetData(Array('F'=>$cellId.$rowNo,'T'=>$cellId.($rowNo+1),'val'=>"기관수",'backcolor'=>'EAEAEA'));
	$cellF = GetNextCellId($cellId);
	$cellT = GetNextCellId($cellF,2);
	$sheet->SetData(Array('F'=>$cellF.$rowNo,'T'=>$cellT.$rowNo,'val'=>"청구내역",'backcolor'=>'EAEAEA'));
	$cellF = GetNextCellId($cellT);
	$cellT = GetNextCellId($cellF,1);
	$sheet->SetData(Array('F'=>$cellF.$rowNo,'T'=>$cellT.$rowNo,'val'=>"입금내역",'backcolor'=>'EAEAEA'));
	$cellId = GetNextCellId($cellT);
	$sheet->getColumnDimension($cellId)->setWidth(10);
	$sheet->SetData(Array('F'=>$cellId.$rowNo,'T'=>$cellId.($rowNo+1),'val'=>"합계",'backcolor'=>'EAEAEA'));


	//서비스 리스트
	$sql = 'SELECT	a.svc_gbn
			,		a.svc_cd AS main_cd
			,		a.svc_nm AS main_nm
			,		b.svc_cd AS sub_cd
			,		b.svc_nm AS sub_nm
			FROM	(
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_main
					WHERE	parent_cd IS NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_sub
					WHERE	parent_cd IS NULL
					) AS a
			INNER	JOIN (
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_main
					WHERE	parent_cd IS NOT NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_sub
					WHERE	parent_cd IS NOT NULL
					) AS b
					ON		b.svc_gbn	= a.svc_gbn
					AND		b.parent_cd = a.svc_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$arrSvc[$row['svc_gbn'].'_'.$row['main_cd']]){
			$arrSvc[$row['svc_gbn'].'_'.$row['main_cd']]['name'] = $row['main_nm'];
		}
		$arrSvc[$row['svc_gbn'].'_'.$row['main_cd']]['list'][] = Array('gbn'=>$row['svc_gbn'], 'cd'=>$row['sub_cd'], 'nm'=>$row['sub_nm']);
		$arrSvc[$row['svc_gbn'].'_'.$row['main_cd']]['cnt'] ++;
	}

	$conn->row_free();

	if (is_array($arrSvc)){
		$cellT = $cellId;
		foreach($arrSvc as $tmpCD => $R){
			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF,$R['cnt']-1);
			$sheet->SetData(Array('F'=>$cellF.$rowNo,'T'=>$cellT.$rowNo,'val'=>$R['name'],'backcolor'=>'EAEAEA'));
		}

		$rowNo ++;
		foreach($arrSvc as $tmpCD => $R1){
			foreach($R1['list'] as $tmpIdx => $R){
				$cellId = GetNextCellId($cellId);
				$sheet->getColumnDimension($cellId)->setWidth(10);
				$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>$R['nm'],'backcolor'=>'EAEAEA'));
			}
		}

		$cellLast = $cellId;

		$cellId = 'B';
		$cellId = GetNextCellId($cellId);
		$sheet->getColumnDimension($cellId)->setWidth(10);
		$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"합계",'backcolor'=>'EAEAEA'));
		$cellId = GetNextCellId($cellId);
		$sheet->getColumnDimension($cellId)->setWidth(10);
		$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"당월분",'backcolor'=>'EAEAEA'));
		$cellId = GetNextCellId($cellId);
		$sheet->getColumnDimension($cellId)->setWidth(10);
		$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"미납분",'backcolor'=>'EAEAEA'));
		$cellId = GetNextCellId($cellId);
		$sheet->getColumnDimension($cellId)->setWidth(10);
		$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"입금",'backcolor'=>'EAEAEA'));
		$cellId = GetNextCellId($cellId);
		$sheet->getColumnDimension($cellId)->setWidth(10);
		$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"미납",'backcolor'=>'EAEAEA'));
	}


	//폰트설정
	$fontSize = 18;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;


	$rowBankNo = $rowNo;
	$rowNo = 1;


	$cellF = GetNextCellId();
	$cellT = $cellLast;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData(Array('F'=>$cellF.$rowNo,'T'=>$cellT.$rowNo,'val'=>$title,'H'=>'L','border'=>'TNRNBNLN'));


	/** 데이타 ****************************************/

		$sql = '';

	/******************************************/


	$rowNo = $rowBankNo;


	//폰트설정
	$fontSize = 9;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$rH = $rowH * $fontSize / $defFontSize;


	for($i=0; $i<=13; $i++){
		$rowNo ++;

		$sheet->getRowDimension($rowNo)->setRowHeight($rH);

		$cellId = '';
		while(true){
			$cellId = GetNextCellId($cellId);
			$sheet->SetData(Array('F'=>$cellId.$rowNo,'val'=>"",'border'=>($i > 0 ? 'THBH' : 'TH')));

			if ($cellId == $cellLast) break;
		}
	}

	$rowNo ++;
	$sheet->SetData(Array('F'=>'A'.$rowNo,'T'=>$cellLast.$rowNo,'val'=>"",'border'=>'RNBNLN'));


	/*
	public function SetData($val){
		//세로
		if ($val['V'] == 'T'){
			$val['V'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
		}else if ($val['V'] == 'B'){
			$val['V'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
		}else{
			$val['V'] = PHPExcel_Style_Alignment::VERTICAL_CENTER;
		}

		//가로
		if ($val['H'] == 'L'){
			$val['H'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		}else if ($val['H'] == 'R'){
			$val['H'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
		}else{
			$val['H'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
		}

		if (!$val['F']) $val['F'] = 'A1';
		if (!$val['T']) $val['T'] = $val['F'];

		//값입력
		$this->getCell($val['F'])->setValue($val['val']);
		$this->getStyle($val['F'])->getAlignment()->setWrapText(true);
		$this->getStyle($val['F'].':'.$val['T'])->getAlignment()->setVertical($val['V'])->setHorizontal($val['H']);
		$this->mergeCells($val['F'].':'.$val['T']);

		//테두리
		$this->getStyle($val['F'].':'.$val['T'])->applyFromArray(fnStyle($val['border']));

		//배경색
		if ($val['backcolor']) $this->SetBGColor($val['F'],$val['backcolor']);

		//형태
		if ($val['format']) $this->getStyle($val['F'])->getNumberFormat()->setFormatCode($val['format']);
	}
	*/


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	//고정
	$objPHPExcel->getActiveSheet()->freezePane('I5');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");


	Unset($arrSvc);


	include_once("../inc/_db_close.php");
?>
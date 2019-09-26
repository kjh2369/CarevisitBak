<?
	//기본폰트설정
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	//마지막 행
	if (!$lastCol) $lastCol = 32;

	//행 넓이
	if (!$widthCol) $widthCol = 2.7;

	//행넓이 설정
	for($tmpIdx199=0; $tmpIdx199<=$lastCol; $tmpIdx199++){
		if ($tmpIdx199 > 25){
			$tmpIdx198 = Floor($tmpIdx199 / 26) - 1;
			$s = chr(65 + $tmpIdx198);
			$tmpIdx198 = 0;
		}else{
			$s = '';
		}

		$tmpIdx198 = $tmpIdx199 % 26;

		$sheet->getColumnDimension($s.chr(65 + $tmpIdx198))->setWidth($widthCol);
	}

	//마지막행
	//$lastCol = "AF";
?>
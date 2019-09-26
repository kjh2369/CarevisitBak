<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	$subject = $year.'년 지역별 보고서';
	$lastCol = 'N'; //마지막 열

	//제목
	$sheet->getCell('A1')->setValue($subject);
	$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A1:A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheet->mergeCells('A1:'.$lastCol.'1');
	$sheet->getRowDimension(1)->setRowHeight(30);

	//기관명
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(13);
	$sheet->getCell('A3')->setValue('한국재가노인복지협회');

	$sheet->getStyle('A3:A3')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$sheet->mergeCells('A3:'.$lastCol.'3');
	$sheet->getRowDimension(3)->setRowHeight(20);

	//기본폰트설정
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	//행넓이 설정
	$cnt = ord($lastCol)+1;

	for($i=65; $i<$cnt; $i++)
		$sheet->getColumnDimension(chr($i))->setWidth(7);

	//타이틀
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A5','지역')
				->setCellValue('B5','합계')
				->setCellValue('C5','1월')
				->setCellValue('D5','2월')
				->setCellValue('E5','3월')
				->setCellValue('F5','4월')
				->setCellValue('G5','5월')
				->setCellValue('H5','6월')
				->setCellValue('I5','7월')
				->setCellValue('J5','8월')
				->setCellValue('K5','9월')
				->setCellValue('L5','10월')
				->setCellValue('M5','11월')
				->setCellValue('N5','12월');
	$sheet->getStyle('A5:'.$lastCol.'5')->applyFromArray($style[2]);
	$sheet->getStyle('A5:'.$lastCol.'5')->getFill()
			->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFE5E0EC');
	$sheet->getStyle('A5:'.$lastCol.'5')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getRowDimension(5)->setRowHeight(30);

	//데이타
	include('./care_find.php');

	if ($data){
		$data = Explode(chr(11),$data);

		$first = true;
		$firstRowIdx = 6;
		$rowIdx = $firstRowIdx;

		$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

		foreach($data as $row){
			if ($row){
				parse_str($row,$col);

				if ($first){
					$first = false;

					$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
					$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->getFill()
							->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							->getStartColor()->setARGB('FFDDEEF3');
					$sheet->getRowDimension($rowIdx)->setRowHeight(30);

					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$rowIdx,'총계');
					$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
							->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet->getStyle('A'.$rowIdx)->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->getStyle('B'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
							->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->getFont()->setBold(true);

					$totRowIdx = $rowIdx;
					$rowIdx ++;
				}

				$sheet->getRowDimension($rowIdx)->setRowHeight(30);
				$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
				$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
						->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sheet->getStyle('A'.$rowIdx)->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle('B'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyle('A'.$rowIdx.':B'.$rowIdx)->getFont()->setBold(true);
				$sheet->getStyle('B'.$rowIdx)->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setARGB('FFFFFF00');

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$rowIdx,$col['areaNm']);	//지역
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowIdx,'=SUM(C'.$rowIdx.':'.$lastCol.$rowIdx.')');	//합계

				for($i=1; $i<=12; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(66+$i).$rowIdx,($col[$i] > 0 ? $col[$i] : ''));	//월별
				}

				$rowIdx ++;
			}
		}

		//총계
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$totRowIdx,'=SUM(C'.$totRowIdx.':'.$lastCol.$totRowIdx.')');	//합계

		for($i=1; $i<=12; $i++){
			$chr = chr(66+$i);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($chr.$totRowIdx,'=IF(SUM('.$chr.($firstRowIdx+1).':'.$chr.$rowIdx.') > 0,SUM('.$chr.($firstRowIdx+1).':'.$chr.$rowIdx.'),"")');	//월별
		}
	}

	//위치고정
	$sheet->freezePaneByColumnAndRow(2,7);
?>
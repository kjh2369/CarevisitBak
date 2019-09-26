<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	//말일
	$lastDay = $myF->lastDay($year,$month);

	//요일
	$weekly = Array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');

	if ($lastDay == 31){
		$lastCol = 'AI';
	}else if ($lastDay == 30){
		$lastCol = 'AH';
	}else if ($lastDay == 29){
		$lastCol = 'AG';
	}else{
		$lastCol = 'AF';
	}

	//제목
	$sheet->getCell('A1')->setValue($year.'년 '.IntVal($month).'월 재가노인지원서비스 현황('.$storeNm.')');
	$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A1:A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheet->mergeCells('A1:'.$lastCol.'1');
	$sheet->getRowDimension(1)->setRowHeight(30);

	//기본폰트설정
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	//행넓이 설정
	$sheet->getColumnDimension('A')->setWidth(13);
	$sheet->getColumnDimension('B')->setWidth(16);
	$sheet->getColumnDimension('C')->setWidth(23);
	$sheet->getColumnDimension('D')->setWidth(10);

	$j = 0;

	for($i=0; $i<$lastDay; $i++){
		if ($i > 21){
			$str = 'A'.chr(65+$i-$j);
		}else{
			$str = chr(69+$i);
			$j ++;
		}

		$sheet->getColumnDimension($str)->setWidth(5);
	}

	$sheet->getRowDimension(2)->setRowHeight(20);
	$sheet->getRowDimension(3)->setRowHeight(10);

	//타이틀 스타일
	$sheet->getStyle('A4:'.$lastCol.'4')->applyFromArray($style[1]);
	$sheet->getStyle('A5:'.$lastCol.'5')->applyFromArray($style[2]);
	$sheet->getStyle('A4:'.$lastCol.'5')->getFill()
			->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFE5E0EC');
	$sheet->mergeCells('A4:A5');
	$sheet->mergeCells('B4:B5');
	$sheet->mergeCells('C4:C5');
	$sheet->mergeCells('D4:D5');
	$sheet->getRowDimension(4)->setRowHeight(20);
	$sheet->getRowDimension(5)->setRowHeight(20);
	$sheet->getStyle('A4:'.$lastCol.'5')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A4','대분류')
				->setCellValue('B4','중분류')
				->setCellValue('C4','세부사업명')
				->setCellValue('D4','합계');

	$j = 0;
	for($i=0; $i<$lastDay; $i++){
		if ($i > 21){
			$str = 'A'.chr(65+$i-$j);
		}else{
			$str = chr(69+$i);
			$j ++;
		}

		$day = $i + 1;
		$date = $year.$month.($day < 10 ? '0' : '').$day;
		$week = $weekly[Date('w',StrToTime($date))];

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($str.'4',$day)
					->setCellValue($str.'5',$week);

		if ($week == '일'){
			$sheet->getStyle($str.'4:'.$str.'5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		}else if ($week == '토'){
			$sheet->getStyle($str.'4:'.$str.'5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
		}
	}


	//데이타
	include('./care_find.php');

	$data = Explode(chr(11),$data);

	$first = true;
	$rowIdx = 6;
	$no = 1;

	foreach($data as $row){
		parse_str($row,$col);

		If (!$col['svcNm']){
			break;
		}

		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		if ($first){
			$first = false;

			//총계
			$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
			$sheet->getStyle('A'.$rowIdx.':'.$lastCol.$rowIdx)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFDDEEF3');
			$sheet->mergeCells('A'.$rowIdx.':C'.$rowIdx);
			$sheet->getRowDimension($rowIdx)->setRowHeight(20);

			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$rowIdx,'총  계')
						->setCellValue('D'.$rowIdx,lfGetMonthVal($rowIdx,$lastDay));

			$sheet->getStyle('A'.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');

			$totRowIdx = $rowIdx;
			$rowIdx ++;
		}

		if ($col['mstCnt'] > 0){
			$sheet->getRowDimension($rowIdx)->setRowHeight(20);
			$sheet->getStyle('A'.$rowIdx.':A'.($rowIdx+$col['mstCnt']))->applyFromArray($style[2]);
			$sheet->mergeCells('A'.$rowIdx.':A'.($rowIdx+$col['mstCnt']));
			$sheet->getStyle('A'.$rowIdx)->getAlignment()->setWrapText(true);
			$sheet->getStyle('A'.$rowIdx.':B'.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$rowIdx,Str_Replace('<br>',chr(13).chr(10),$col['mstNm']));

			$sheet->getStyle('B'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
			$sheet->mergeCells('B'.$rowIdx.':C'.$rowIdx);
			$sheet->getStyle('A'.$rowIdx)->getAlignment()->setWrapText(true);
			$sheet->getStyle('B'.$rowIdx.':'.$lastCol.$rowIdx)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFFDE9D9');
			$sheet->getStyle('B'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowIdx,'합  계')->setCellValue('D'.$rowIdx,lfGetMonthVal($rowIdx,$lastDay));

			$sumRowIdx = $rowIdx;
			$rowIdx ++;
		}

		if ($col['proCnt'] > 0){
			$sheet->getRowDimension($rowIdx)->setRowHeight(20);
			$sheet->getStyle('B'.$rowIdx.':B'.($rowIdx+$col['proCnt']))->applyFromArray($style[2]);
			$sheet->mergeCells('B'.$rowIdx.':B'.($rowIdx+$col['proCnt']));
			$sheet->getStyle('B'.$rowIdx)->getAlignment()->setWrapText(true);
			$sheet->getStyle('B'.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowIdx,Str_Replace('<br>',chr(13).chr(10),$col['proNm']));

			$sheet->getStyle('C'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
			$sheet->getStyle('C'.$rowIdx.':'.$lastCol.$rowIdx)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFFFFF00');
			$sheet->getStyle('C'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowIdx,'소  계')->setCellValue('D'.$rowIdx,lfGetMonthVal($rowIdx,$lastDay));

			$j = 0;
			for($i=0; $i<$lastDay; $i++){
				if ($i > 21){
					$str = 'A'.chr(65+$i-$j);
				}else{
					$str = chr(69+$i);
					$j ++;
				}

				$strCnt = '=SUM('.$str.($rowIdx+1).':'.$str.($rowIdx+$col['proCnt']).')';
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($str.$rowIdx,$strCnt);
			}
			$sumRow[$sumRowIdx][SizeOf($sumRow[$sumRowIdx])] = $rowIdx;
			$rowIdx ++;
		}

		$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

		$sheet->getRowDimension($rowIdx)->setRowHeight(20);
		$sheet->getStyle('C'.$rowIdx)->applyFromArray($style[2]);
		$sheet->getStyle('C'.$rowIdx)->getAlignment()->setWrapText(true);
		$sheet->getStyle('C'.$rowIdx)->getAlignment()
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowIdx,Str_Replace('<br>',chr(13).chr(10),$col['svcNm']));

		$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->applyFromArray($style[2]);
		$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowIdx,lfGetMonthVal($rowIdx,$lastDay));

		$j = 0;
		for($i=0; $i<$lastDay; $i++){
			if ($i > 21){
				$str = 'A'.chr(65+$i-$j);
			}else{
				$str = chr(69+$i);
				$j ++;
			}

			$cnt = Explode('/',$col['cnt']);
			$strCnt = $cnt[$i+1];

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($str.$rowIdx,$strCnt);
		}
		$rowIdx ++;

		Unset($cnt);
	}


	//합계
	if (Is_Array($sumRow)){
		foreach($sumRow as $rowIdx => $sum){
			$cnt = SizeOf($sum);
			$strCnt = '';

			for($i=0; $i<$cnt; $i++){
				if ($strCnt) $strCnt .= '+';
				$strCnt .= '_STR_'.$sum[$i];
			}

			$strCnt = '='.$strCnt;

			$j = 0;
			for($i=0; $i<$lastDay; $i++){
				if ($i > 21){
					$str = 'A'.chr(65+$i-$j);
				}else{
					$str = chr(69+$i);
					$j ++;
				}

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($str.$rowIdx,Str_Replace('_STR_',$str,$strCnt));
			}

			$totRow[$totRowIdx][SizeOf($totRow[$totRowIdx])] = $rowIdx;
		}
	}

	//총계
	if (Is_Array($totRow)){
		foreach($totRow as $rowIdx => $sum){
			$cnt = SizeOf($sum);
			$strCnt = '';

			for($i=0; $i<$cnt; $i++){
				if ($strCnt) $strCnt .= '+';
				$strCnt .= '_STR_'.$sum[$i];
			}

			$strCnt = '='.$strCnt;

			$j = 0;
			for($i=0; $i<$lastDay; $i++){
				if ($i > 21){
					$str = 'A'.chr(65+$i-$j);
				}else{
					$str = chr(69+$i);
					$j ++;
				}

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($str.$rowIdx,Str_Replace('_STR_',$str,$strCnt));
			}
		}
	}


	//위치고정
	$sheet->freezePaneByColumnAndRow(4,6);

	function lfGetMonthVal($rowIdx,$lastDay){
		$j = 0;
		for($i=0; $i<$lastDay; $i++){
			if ($i > 21){
				$str = 'A'.chr(65+$i-$j);
			}else{
				$str = chr(69+$i);
				$j ++;
			}

			if ($tmp) $tmp .= '+';

			$tmp .= $str.$rowIdx;
		}

		$tmp = '='.$tmp;

		return $tmp;
	}
?>
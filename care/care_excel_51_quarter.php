<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	$subject = $year.'년 ';

	if ($type == '52_ASS_SVC'){
		//마지막 열
		$lastCol = 'J';
	}else{
		$quarter = SubStr($type,StrLen($type)-1,StrLen($type));
		$subject = $subject.$quarter.'분기 ';

		//마지막 열
		$lastCol = 'G';
	}
	$subject .= '재가노인지원서비스 사업보고서';

	//제목
	$sheet->getCell('A1')->setValue($subject);
	$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A1:A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$sheet->mergeCells('A1:'.$lastCol.'1');
	$sheet->getRowDimension(1)->setRowHeight(30);

	//기관명
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(13);

	if ($type == '52_ASS_SVC'){
		if ($area){
			$sheet->getCell('A3')->setValue('한국재가노인복지협회('.$areaNm.')');
		}else{
			$sheet->getCell('A3')->setValue('한국재가노인복지협회');
		}
	}else{
		$sheet->getCell('A3')->setValue($storeNm);
	}

	$sheet->getStyle('A3:A3')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$sheet->mergeCells('A3:'.$lastCol.'3');
	$sheet->getRowDimension(3)->setRowHeight(20);

	//기본폰트설정
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$sheet->getCell('A4')->setValue('1.사업별현황');
	$sheet->getStyle('A4')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('A4:C4');
	$sheet->getRowDimension(4)->setRowHeight(20);

	//행넓이 설정
	$sheet->getColumnDimension('A')->setWidth(13);
	$sheet->getColumnDimension('B')->setWidth(16);
	$sheet->getColumnDimension('C')->setWidth(23);
	$sheet->getColumnDimension('D')->setWidth(13);
	$sheet->getColumnDimension('E')->setWidth(13);
	$sheet->getColumnDimension('F')->setWidth(13);
	$sheet->getColumnDimension('G')->setWidth(13);

	//타이틀
	if ($type == '52_ASS_SVC'){
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A5','대분류')
					->setCellValue('B5','중분류')
					->setCellValue('C5','세부사업명')
					->setCellValue('D5','목표')
					->setCellValue('E5','1/4분기')
					->setCellValue('F5','2/4분기')
					->setCellValue('G5','3/4분기')
					->setCellValue('H5','4/4분기')
					->setCellValue('I5','누계')
					->setCellValue('J5','달성률');
	}else{
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A5','대분류')
					->setCellValue('B5','중분류')
					->setCellValue('C5','세부사업명')
					->setCellValue('D5','목표')
					->setCellValue('E5',$quarter.'분기')
					->setCellValue('F5','누계')
					->setCellValue('G5','달성률');
	}
	$sheet->getStyle('A5:'.$lastCol.'5')->applyFromArray($style[2]);
	$sheet->getStyle('A5:'.$lastCol.'5')->getFill()
			->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFE5E0EC');
	$sheet->getStyle('A5:'.$lastCol.'5')->getAlignment()
			->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getRowDimension(5)->setRowHeight(20);


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
						->setCellValue('D'.$rowIdx,'');

			$sheet->getStyle('A'.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getAlignment()
					->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
					->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');
			$sheet->getStyle($lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###.0');

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
			$sheet->getStyle($lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###.0');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowIdx,'합  계')->setCellValue('D'.$rowIdx,'');

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
			$sheet->getStyle($lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###.0');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowIdx,'소  계')->setCellValue('D'.$rowIdx,'');

			$strCnt = '=SUM(_STR_'.($rowIdx+1).':_STR_'.($rowIdx+$col['proCnt']).')';
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowIdx,Str_Replace('_STR_','D',$strCnt));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowIdx,Str_Replace('_STR_','E',$strCnt));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$rowIdx,Str_Replace('_STR_','F',$strCnt));

			if ($type == '52_ASS_SVC'){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,Str_Replace('_STR_','G',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowIdx,Str_Replace('_STR_','H',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$rowIdx,Str_Replace('_STR_','I',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowIdx,'=I'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,'=F'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
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
		$sheet->getStyle(chr(ord($lastCol)-1).$rowIdx)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFFFFF00');
		$sheet->getStyle('D'.$rowIdx.':'.$lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###');
		$sheet->getStyle($lastCol.$rowIdx)->getNumberFormat()->setFormatCode('#,###.0');
		$sheet->getStyle(chr(ord($lastCol)-1).$rowIdx,':J'.$rowIdx)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowIdx,$col['target']); //목표

		if ($type == '52_ASS_SVC'){
			$cnt = Explode('/',$col['cnt']);

			for($i=0; $i<4; $i++){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(69+$i).$rowIdx,$cnt[$i+1]); //값
			}

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$rowIdx,'=SUM(E'.$rowIdx.':H'.$rowIdx.')'); //누계
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowIdx,'=I'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률

			Unset($cnt);
		}else{
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowIdx,$col['cnt']); //값
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$rowIdx,$col['cntTot']); //누계
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,'=F'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
		}

		$rowIdx ++;
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

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowIdx,Str_Replace('_STR_','D',$strCnt));//목표
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowIdx,Str_Replace('_STR_','E',$strCnt));//값
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$rowIdx,Str_Replace('_STR_','F',$strCnt));//누계

			if ($type == '52_ASS_SVC'){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,Str_Replace('_STR_','G',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowIdx,Str_Replace('_STR_','H',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$rowIdx,Str_Replace('_STR_','I',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowIdx,'=I'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,'=F'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
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

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowIdx,Str_Replace('_STR_','D',$strCnt));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowIdx,Str_Replace('_STR_','E',$strCnt));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$rowIdx,Str_Replace('_STR_','F',$strCnt));

			if ($type == '52_ASS_SVC'){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,Str_Replace('_STR_','G',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowIdx,Str_Replace('_STR_','H',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$rowIdx,Str_Replace('_STR_','I',$strCnt));
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowIdx,'=I'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
			}else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowIdx,'=F'.$rowIdx.'/IF(D'.$rowIdx.'=0,1,D'.$rowIdx.')*100'); //달성률
			}
		}
	}

	//위치고정
	$sheet->freezePaneByColumnAndRow(4,7);
?>
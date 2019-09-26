<?
	//스타일설정
	function fnStyle($val = '', $dd = ''){
		$val = StrToUpper($val);
		$dd  = StrToUpper($dd);

		$left	  = PHPExcel_Style_Border::BORDER_THIN;
		$right	  = PHPExcel_Style_Border::BORDER_THIN;
		$top	  = PHPExcel_Style_Border::BORDER_THIN;
		$bottom   = PHPExcel_Style_Border::BORDER_THIN;
		$ddborder = PHPExcel_Style_Border::BORDER_NONE;

		if (is_numeric(StrPos($val,'L'))) $left		= PHPExcel_Style_Border::BORDER_MEDIUM;
		if (is_numeric(StrPos($val,'R'))) $right	= PHPExcel_Style_Border::BORDER_MEDIUM;
		if (is_numeric(StrPos($val,'T'))) $top		= PHPExcel_Style_Border::BORDER_MEDIUM;
		if (is_numeric(StrPos($val,'B'))) $bottom	= PHPExcel_Style_Border::BORDER_MEDIUM;

		if (is_numeric(StrPos($val,'LD'))) $left	= PHPExcel_Style_Border::BORDER_DASHED;
		if (is_numeric(StrPos($val,'RD'))) $right	= PHPExcel_Style_Border::BORDER_DASHED;
		if (is_numeric(StrPos($val,'TD'))) $top		= PHPExcel_Style_Border::BORDER_DASHED;
		if (is_numeric(StrPos($val,'BD'))) $bottom	= PHPExcel_Style_Border::BORDER_DASHED;

		if (is_numeric(StrPos($val,'LH'))) $left	= PHPExcel_Style_Border::BORDER_HAIR;
		if (is_numeric(StrPos($val,'RH'))) $right	= PHPExcel_Style_Border::BORDER_HAIR;
		if (is_numeric(StrPos($val,'TH'))) $top		= PHPExcel_Style_Border::BORDER_HAIR;
		if (is_numeric(StrPos($val,'BH'))) $bottom	= PHPExcel_Style_Border::BORDER_HAIR;

		if (is_numeric(StrPos($val,'LU'))) $left	= PHPExcel_Style_Border::BORDER_DOUBLE;
		if (is_numeric(StrPos($val,'RU'))) $right	= PHPExcel_Style_Border::BORDER_DOUBLE;
		if (is_numeric(StrPos($val,'TU'))) $top		= PHPExcel_Style_Border::BORDER_DOUBLE;
		if (is_numeric(StrPos($val,'BU'))) $bottom	= PHPExcel_Style_Border::BORDER_DOUBLE;

		if (is_numeric(StrPos($val,'LN'))) $left	= PHPExcel_Style_Border::BORDER_NONE;
		if (is_numeric(StrPos($val,'RN'))) $right	= PHPExcel_Style_Border::BORDER_NONE;
		if (is_numeric(StrPos($val,'TN'))) $top		= PHPExcel_Style_Border::BORDER_NONE;
		if (is_numeric(StrPos($val,'BN'))) $bottom	= PHPExcel_Style_Border::BORDER_NONE;

		if (is_numeric(StrPos($dd,'B'))) $ddborder = PHPExcel_Style_Border::BORDER_MEDIUM;
		if (is_numeric(StrPos($dd,'D'))) $ddborder = PHPExcel_Style_Border::BORDER_DASHED;
		if (is_numeric(StrPos($dd,'H'))) $ddborder = PHPExcel_Style_Border::BORDER_HAIR;
		if (is_numeric(StrPos($dd,'N'))) $ddborder = PHPExcel_Style_Border::BORDER_THIN;

		if (is_numeric(StrPos($dd,'1'))) $ddstyle = PHPExcel_Style_Borders::DIAGONAL_DOWN;
		if (is_numeric(StrPos($dd,'2'))) $ddstyle = PHPExcel_Style_Borders::DIAGONAL_UP;
		if (is_numeric(StrPos($dd,'3'))) $ddstyle = PHPExcel_Style_Borders::DIAGONAL_BOTH;

		$style = array(
			'borders' => array(
				'top' => array(
					'style' => $top,
					'color' => array('argb' => '000000'),
				),
				'vertical' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => '000000'),
				),
				'left' => array(
					'style' => $left,
					'color' => array('argb' => '000000'),
				),
				'right' => array(
					'style' => $right,
					'color' => array('argb' => '000000'),
				),
				'bottom' => array(
					'style' => $bottom,
					'color' => array('argb' => '000000'),
				),
				'diagonal' => array(
					'style' => $ddborder,
					'color' => array('argb' => '000000'),
				),
				'diagonaldirection' => $ddstyle,
			),
		);

		return $style;
	}

	//셀의 라인 수
	function GetRowcount($text, $width=100){
		$rc = 0;
		$line = explode("\n", $text);

		foreach($line as $source){
			$rc += intval((strlen($source) / $width) +1);
		}

		return $rc;
	}

	function GetRowHeight($cnt, $height = 12.75, $gab = 2.25){
		$h = $cnt * $height + $gab;
		return $h;
	}

	//색상
	//function SetBGColor($sheet, $cell, $RGB){
	//	$sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	//	$sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('00'.$RGB);
	//}

	//다음 행 찾기
	function GetNextCellId($id = '', $step = 1){
		if (!$id) return 'A';

		$cellID = $id;

		for($i=0; $i<Abs($step); $i++){
			$cellID = GetNextCellIdSub($cellID, $step > 0 ? 1 : -1);
		}

		return $cellID;
	}

	function GetNextCellIdSub($id = '', $step = 1){
		if (!$id) return 'A';

		$minID = ord('A');
		$maxID = ord('Z');
		$cellID = '';

		$i = StrLen($id) - 1;
		$j = $step;

		while(true){
			if ($i < 0) break;

			$curID = ord($id[$i]) + $j;

			if ($curID > $maxID){
				$curID = $minID;
				$j = 1;
			}else if ($curID < $minID){
				$curID = $maxID;
				$j = -1;
			}else{
				$j = 0;
			}

			if ($j < 0 && $i == 1 && $curID == $maxID && ord($id[$i-1]) == $minID){
				$i --;
			}

			$cellID = chr($curID).$cellID;

			$i --;
		}

		if ($j > 0) $cellID = 'A'.$cellID;

		return $cellID;
	}
?>
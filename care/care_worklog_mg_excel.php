<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION["userCenterCode"];
	$orgNm	= $_SESSION["userCenterName"];
	$SR		= $_POST['SR'];
	$date	= $_POST['date'];
	$weekly	= Date('w', StrToTime($date));

	switch($weekly){
		case 0:
			$weekly = '일';
			break;

		case 1:
			$weekly = '월';
			break;

		case 2:
			$weekly = '화';
			break;

		case 3:
			$weekly = '수';
			break;

		case 4:
			$weekly = '목';
			break;

		case 5:
			$weekly = '금';
			break;

		case 6:
			$weekly = '토';
			break;
	}


	//결제란설정
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$orgNo.'\'';
	$row = $conn->get_array($sql);

	$signCnt = $row['line_cnt'];
	$signTxt = Explode('|',$row['subject']);

	Unset($row);

	if ($signCnt == ''){
		$signCnt = 3;
		$signTxt[0] = '담당';
		$signTxt[1] = '팀장';
		$signTxt[2] = '센터장';
	}


	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("재가지원 운영일지").".xls" );

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("재가지원 운영일지");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getPageSetup()->setFitToPage(true);
	//$sheet->getPageSetup()->setFitToWidth(1);
	//$sheet->getPageSetup()->setFitToHeight(0);

	$sheet->getPageMargins()->setTop(0.5);
	$sheet->getPageMargins()->setRight(0.1);
	$sheet->getPageMargins()->setLeft(0.1);
	$sheet->getPageMargins()->setBottom(0.5);

	$lastCol = 51;
	$widthCol = 1.8;

	//스타일
	include_once("../excel/style.php");
	include_once("../excel/init.php");

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕


	//초기화
	$defFontSize = 9;
	$rowH = 15;
	$rowNo = 0;


	//폰트설정
	$fontSize = 13;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);


	$rH = $rowH * $fontSize / $defFontSize;

	$sheet->getRowDimension(1)->setRowHeight($rH);
	$sheet->getRowDimension(2)->setRowHeight($rH);
	$sheet->getRowDimension(3)->setRowHeight($rH);


	/** 타이틀 *******************************************************/
		$cellF = 'A';

		if ($signCnt > 0){
			$cellT = 'AC';
		}else{
			$cellT = 'AZ';
		}

		$rowNo = 1;
		$sheet->getCell($cellF.$rowNo)->setValue($orgNm."\n재가복지사업 운영일지");
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	/*********************************************************/


	$fontSize = $defFontSize;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$rH = $rowH * $fontSize / $defFontSize;


	/** 일자 *******************************************************/
		$cellF = 'A';

		if ($signCnt > 0){
			$cellT = 'AC';
		}else{
			$cellT = 'AZ';
		}

		$rowNo += 2;
		$sheet->getCell($cellF.$rowNo)->setValue($myF->dateStyle($date,'KOR').'('.$weekly.'요일)');
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

	/** 결재란 *******************************************************/
		if ($signCnt > 0){
			if ($signCnt == 1){
				$firstF = 'AR';
			}else if ($signCnt == 2){
				$firstF = 'AK';
			}else if ($signCnt == 3){
				$firstF = 'AD';
			}else{
				$firstF = 'AE';
			}

			if ($signCnt == 4){
				$cellCnt = 4;
			}else if ($signCnt == 5){
				$cellCnt = 3;
			}else{
				$cellCnt = 6;
			}
			//GetNextCellId($id = '', $step = 1)

			$rowNo = 1;

			$cellF = $firstF;
			$cellT = GetNextCellId($cellF, 1);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+2), 'val'=>"결\n재" ) );

			for($i=0; $i<$signCnt; $i++){
				$cellF = GetNextCellId($cellT);
				$cellT = GetNextCellId($cellF, $cellCnt);
				$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>$signTxt[$i] ) );
			}

			$rowNo ++;
			$cellT = GetNextCellId($firstF, 1);
			for($i=0; $i<$signCnt; $i++){
				$cellF = GetNextCellId($cellT);
				$cellT = GetNextCellId($cellF, $cellCnt);
				$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.($rowNo+1) ) );
			}

			/*
			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF, $cellCnt);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>"담당" ) );

			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF, $cellCnt);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>"팀장" ) );

			$cellF = GetNextCellId($cellT);
			$cellT = GetNextCellId($cellF, $cellCnt);
			$sheet->SetData( Array( 'F'=>$cellF.$rowNo, 'T'=>$cellT.$rowNo, 'val'=>"센터장" ) );

			$rowNo ++;
			$sheet->SetData( Array( 'F'=>'AD'.$rowNo, 'T'=>'AE'.($rowNo+1) ) );
			$sheet->SetData( Array( 'F'=>'AF'.$rowNo, 'T'=>'AL'.($rowNo+1) ) );
			$sheet->SetData( Array( 'F'=>'AM'.$rowNo, 'T'=>'AS'.($rowNo+1) ) );
			$sheet->SetData( Array( 'F'=>'AT'.$rowNo, 'T'=>'AZ'.($rowNo+1) ) );
			*/
		}else{
			$cellF = 'AD';
			$cellT = 'AE';
			$rowNo = 1;
			$sheet->getCell($cellF.$rowNo)->setValue("결\n재");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+2))->applyFromArray(fnStyle());
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+2));

			$cellF = 'AF';
			$cellT = 'AL';
			$sheet->getCell($cellF.$rowNo)->setValue("담당");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('RHBHLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

			$cellF = 'AM';
			$cellT = 'AS';
			$sheet->getCell($cellF.$rowNo)->setValue("팀장");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('RHBHLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

			$cellF = 'AT';
			$cellT = 'AZ';
			$sheet->getCell($cellF.$rowNo)->setValue("센터장");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BHLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

			$cellF = 'AF';
			$cellT = 'AL';
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->getCell($cellF.$rowNo)->setValue("");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle('THRHLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));

			$cellF = 'AM';
			$cellT = 'AS';
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->getCell($cellF.$rowNo)->setValue("");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle('THRHLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));

			$cellF = 'AT';
			$cellT = 'AZ';
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->getCell($cellF.$rowNo)->setValue("");
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle('THLH'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
		}

	/** 공란 *******************************************************/
		$cellF = 'A';
		$cellT = 'AZ';
		$rowNo = 4;
		$sheet->getRowDimension($rowNo)->setRowHeight(5);
		$sheet->getCell($cellF.$rowNo)->setValue('');
		$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
		$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);

	/** 데이타 유형 *******************************************************/
		/*
		$data['TITLE'] = Array(
			0=>Array('from'=>'A', 'to'=>'Q', 'type'=>'text', 'val'=>'재가노인지원사업', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TBL', 'cont'=>0)
		,	1=>Array('from'=>'R', 'to'=>'V', 'type'=>'text', 'val'=>'계획', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	2=>Array('from'=>'W', 'to'=>'AA', 'type'=>'text', 'val'=>'1분기', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	3=>Array('from'=>'AB', 'to'=>'AF', 'type'=>'text', 'val'=>'2분기', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	4=>Array('from'=>'AG', 'to'=>'AK', 'type'=>'text', 'val'=>'3분기', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	5=>Array('from'=>'AL', 'to'=>'AP', 'type'=>'text', 'val'=>'4분기', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	6=>Array('from'=>'AQ', 'to'=>'AU', 'type'=>'text', 'val'=>'일계', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TB')
		,	7=>Array('from'=>'AV', 'to'=>'AZ', 'type'=>'text', 'val'=>'소계', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TBR')
		);

		$data['SUM'] = Array(
			0=>Array('from'=>'A', 'to'=>'Q', 'type'=>'text', 'val'=>'총계', 'align'=>'C', 'bgcolor'=>'EAEAEA', 'border'=>'TBL')
		,	1=>Array('from'=>'R', 'to'=>'V', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	2=>Array('from'=>'W', 'to'=>'AA', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	3=>Array('from'=>'AB', 'to'=>'AF', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	4=>Array('from'=>'AG', 'to'=>'AK', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	5=>Array('from'=>'AL', 'to'=>'AP', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	6=>Array('from'=>'AQ', 'to'=>'AU', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TB')
		,	7=>Array('from'=>'AV', 'to'=>'AZ', 'type'=>'number', 'val'=>'', 'align'=>'R', 'bgcolor'=>'', 'border'=>'TBR')
		);
		*/

		function DefaultData($val = null){
			if (!$val['from']) $val['from'] = 'A';
			if (!$val['to']) $val['to'] = 'A';
			if (!$val['type']) $val['type'] = 'text';
			if (!$val['align']) $val['align'] = 'C';
			if (!$val['count']) $val['count'] = 0;

			$tmpRow = Array(
				'from'	=>$val['from']
			,	'to'	=>$val['to']
			,	'type'	=>$val['type']
			,	'val'	=>$val['val']
			,	'align'	=>$val['align']
			,	'bgcolor'=>$val['bgcolor']
			,	'border'=>$val['border']
			,	'count'	=>$val['count']
			);

			return $tmpRow;
		}

		$data['TITLE'][] = DefaultData(Array('from'=>'A','to'=>'V','val'=>'재가노인지원사업','bgcolor'=>'EAEAEA','border'=>'TBL'));
		//$data['TITLE'][] = DefaultData(Array('from'=>'R','to'=>'V','val'=>'계획','bgcolor'=>'EAEAEA','border'=>'TB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'W','to'=>'AA','val'=>'1분기','bgcolor'=>'EAEAEA','border'=>'TB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'AB','to'=>'AF','val'=>'2분기','bgcolor'=>'EAEAEA','border'=>'TB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'AG','to'=>'AK','val'=>'3분기','bgcolor'=>'EAEAEA','border'=>'TB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'AL','to'=>'AP','val'=>'4분기','bgcolor'=>'EAEAEA','border'=>'TB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'AQ','to'=>'AU','val'=>'일계','bgcolor'=>'EAEAEA','border'=>'LTB'));
		$data['TITLE'][] = DefaultData(Array('from'=>'AV','to'=>'AZ','val'=>'소계','bgcolor'=>'EAEAEA','border'=>'TBR'));

		$data['SUM'][] = DefaultData(Array('from'=>'A','to'=>'V','val'=>'총   계','bgcolor'=>'EAEAEA','border'=>'TBL'));
		//$data['SUM'][] = DefaultData(Array('from'=>'R','to'=>'V','val'=>'','border'=>'TB','type'=>'number'));
		$data['SUM'][] = DefaultData(Array('from'=>'W','to'=>'AA','val'=>'','border'=>'TB','type'=>'number', 'align'=>'R'));
		$data['SUM'][] = DefaultData(Array('from'=>'AB','to'=>'AF','val'=>'','border'=>'TB','type'=>'number', 'align'=>'R'));
		$data['SUM'][] = DefaultData(Array('from'=>'AG','to'=>'AK','val'=>'','border'=>'TB','type'=>'number', 'align'=>'R'));
		$data['SUM'][] = DefaultData(Array('from'=>'AL','to'=>'AP','val'=>'','border'=>'TB','type'=>'number', 'align'=>'R'));
		$data['SUM'][] = DefaultData(Array('from'=>'AQ','to'=>'AU','val'=>'','border'=>'LTB','type'=>'number', 'align'=>'R'));
		$data['SUM'][] = DefaultData(Array('from'=>'AV','to'=>'AZ','val'=>'','border'=>'TBR','type'=>'number', 'align'=>'R'));


		//중분류 수가
		$sql = 'SELECT	CONCAT(cd1, cd2) AS cd
				,		nm2 AS nm
				FROM	suga_care
				ORDER	BY from_dt, to_dt
				';
		$suga = $conn->_fetch_array($sql,'cd');

		//서비스 횟수
		$sql = 'SELECT	t01_sugup_date AS date, t01_suga_code1 AS dtl_cd
				,		DATE_FORMAT(t01_sugup_date, \'%c\') AS month
				,		COUNT(t01_suga_code1) AS svc_cnt
				,		SUM(CASE WHEN t01_sugup_date = \''.str_replace('-','',$date).'\' THEN 1 ELSE 0 END) AS today_cnt
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$orgNo.'\'
				AND		t01_mkind		= \''.$SR.'\'
				AND		t01_status_gbn	= \'1\'
				AND		t01_del_yn		= \'N\'
				AND		t01_sugup_date <= \''.str_replace('-','',$date).'\'
				AND		LEFT(t01_sugup_date, 4) = \''.SubStr($date,0,4).'\'
				GROUP	BY t01_sugup_date, t01_suga_code1
				';
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$dt = $row['date'];
			$cd = $row['dtl_cd'];

			if ($row['month'] >= '1' && $row['month'] <= '3'){
				$quart = 1;
			}else if ($row['month'] >= '4' && $row['month'] <= '6'){
				$quart = 2;
			}else if ($row['month'] >= '7' && $row['month'] <= '9'){
				$quart = 3;
			}else{
				$quart = 4;
			}

			$svc[$cd][$quart]['svc_cnt'] += $row['svc_cnt'];
			$svc[$cd][$quart]['today_cnt'] += $row['today_cnt'];
		}

		$conn->row_free();



		//데이타
		$sql = 'SELECT	suga_sr AS org_type
				,		suga_nm AS dtl_nm
				,		SUBSTR(suga_cd,1,3) AS pro_cd
				,		CONCAT(suga_cd, suga_sub) AS dtl_cd
				FROM	care_suga
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		suga_sr	 = \''.$SR.'\'
				AND		from_dt	<= \''.str_replace('-','',$date).'\'
				AND		to_dt	>= \''.str_replace('-','',$date).'\'
				ORDER	BY dtl_cd
				';
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$tmpR[$row['dtl_cd']]['proNm'] = $suga[$row['pro_cd']]['nm'];
			$tmpR[$row['dtl_cd']]['proCd'] = $row['pro_cd'];
			$tmpR[$row['dtl_cd']]['dtlNm'] = $row['dtl_nm'];
			$tmpR[$row['dtl_cd']]['plan'] = 0;

			for($j=1; $j<=4; $j++){
				$tmpR[$row['dtl_cd']]['q'.$j] = $svc[$row['dtl_cd']][$j]['svc_cnt'];
				$tmpR[$row['dtl_cd']]['today'] += $svc[$row['dtl_cd']][$j]['today_cnt'];
				$tmpSum[$row['pro_cd']]['val'] += $svc[$row['dtl_cd']][$j]['svc_cnt'];

				$data['SUM'][$j]['val'] += $svc[$row['dtl_cd']][$j]['svc_cnt'];
				$data['SUM'][5]['val'] += $svc[$row['dtl_cd']][$j]['today_cnt'];
				$data['SUM'][6]['val'] += $svc[$row['dtl_cd']][$j]['svc_cnt'];
			}
		}

		$conn->row_free();

		unset($svc);

		if (is_array($tmpR)){
			$i = 0;

			foreach($tmpR as $dtlCd => $row){
				if ($proCd != $row['proCd']){
					$proCd = $row['proCd'];
					$border = 'TBH';
					$proI = $i;
				}else{
					$border = 'THBH';
				}

				$row['proNm'] = str_replace("<br>","\n",$row['proNm']);

				$data['ROW_'.$i][] = DefaultData(Array('from'=>'A','to'=>'I','val'=>$row['proNm'],'bgcolor'=>'EAEAEA','border'=>'L'.$border)); //중분류명
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'J','to'=>'V','val'=>$row['dtlNm'],'border'=>'L'.$border, 'align'=>'L')); //상세명
				//$data['ROW_'.$i][] = DefaultData(Array('from'=>'R','to'=>'V','val'=>'','border'=>$border, 'align'=>'R', 'type'=>'number')); //계획
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'W','to'=>'AA','val'=>$row['q1'],'border'=>$border, 'align'=>'R', 'type'=>'number')); //1분기
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'AB','to'=>'AF','val'=>$row['q2'],'border'=>$border, 'align'=>'R', 'type'=>'number')); //2분기
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'AG','to'=>'AK','val'=>$row['q3'],'border'=>$border, 'align'=>'R', 'type'=>'number')); //3분기
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'AL','to'=>'AP','val'=>$row['q4'],'border'=>$border, 'align'=>'R', 'type'=>'number')); //4분기
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'AQ','to'=>'AU','val'=>$row['today'],'border'=>'L'.$border, 'align'=>'R', 'type'=>'number')); //일계
				$data['ROW_'.$i][] = DefaultData(Array('from'=>'AV','to'=>'AZ','val'=>$tmpSum[$row['proCd']]['val'],'border'=>'R'.$border, 'align'=>'R', 'type'=>'number')); //소계

				if ($i != $proI){
					$data['ROW_'.$proI][0]['count'] ++;
					$data['ROW_'.$proI][SizeOf($data['ROW_'.$proI])-1]['count'] ++;
				}

				$i ++;
			}
			Unset($tmpR);
			Unset($tmpSum);
		}


		$data['REPORT_1'][] = DefaultData(Array('from'=>'A','to'=>'I','val'=>"업 무\n보 고",'bgcolor'=>'EAEAEA','border'=>'TBRL', 'count'=>1));
		$data['REPORT_1'][] = DefaultData(Array('from'=>'J','to'=>'AD','val'=>'직원1','bgcolor'=>'EAEAEA','border'=>'TBRL'));
		$data['REPORT_1'][] = DefaultData(Array('from'=>'AE','to'=>'AZ','val'=>'직원2','bgcolor'=>'EAEAEA','border'=>'TBRL'));
		$data['REPORT_2'][] = DefaultData(Array('from'=>'J','to'=>'AD','val'=>'','border'=>'TBRL'));
		$data['REPORT_2'][] = DefaultData(Array('from'=>'AE','to'=>'AZ','val'=>'','border'=>'TBRL'));


	/** 출력 *******************************************************/
		foreach($data as $gbn => $row){
			$rowNo ++;

			foreach($row as $idx => $col){
				$cellF = $col['from'];
				$cellT = $col['to'];

				switch($col['align']){
					case 'L':
						$align = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
						break;

					case 'R':
						$align = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
						break;

					default:
						$align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
				}

				$sheet->getRowDimension($rowNo)->setRowHeight($rH);
				$sheet->getCell($cellF.$rowNo)->setValue($col['val'] ? $col['val'] : '');
				$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal($align);
				$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+$col['count']))->applyFromArray(fnStyle($col['border']));
				$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+$col['count']));

				if ($col['bgcolor']) $sheet->setBGColor($cellF.$rowNo, $col['bgcolor']);
				if ($col['type'] == 'number') $sheet->getStyle($cellF.$rowNo)->getNumberFormat()->setFormatCode('#,##0');
			}
		}


	/*********************************************************/


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once("../inc/_db_close.php");
?>
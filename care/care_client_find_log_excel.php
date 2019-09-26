<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION["userCenterCode"];
	$orgNm	= $_SESSION["userCenterName"];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);
	$svcStatPrtYn = $_POST['svcStatPrtYn'];
	$align  = $_POST['align'];
	
	$sql = 'SELECT	*
			FROM	apprline_set
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'01\'';
	$apprline = $conn->get_array($sql);

	if ($apprline){
		$apprline['name'] = Explode('|',$apprline['line_name']);
	}else{
		$apprline['prt_yn'] = 'Y';
		$apprline['line_cnt'] = 3;
		$apprline['name'][] = '담당';
		$apprline['name'][] = '팀장';
		$apprline['name'][] = '센터장';
	}

	$sql = 'SELECT	DISTINCT m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$jumin.'\'';
	$name = $conn->get_data($sql);
	
	

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($name.'대상자 '.$year.'년 '.$month.'월 서비스내역').".xls" );

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("");
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


	if ($month){
		$fromM	= $month;
		$toM	= $month;
	}else{
		$fromM	= 1;
		$toM	= 12;
	}


	function lfSetArr($arr, $cd){
		$cnt = SizeOf($arr);
		$sel = -1;

		for($i=0; $i<$cnt; $i++){
			if ($arr[$i]['V']['cd'] == $cd){
				$sel = $i;
				break;
			}
		}

		return $sel;
	}


	$weekly = Array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');
	$loopIdx = 0;

	for($m=$fromM; $m<=$toM; $m++){
		//기본설정
		$defFontSize = 9;
		$rowH = 15;
		$rowNo = 0;

		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex($loopIdx);
		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->setTitle($m."월");
		$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(1);
		$sheet->getPageMargins()->setRight(0.1);
		$sheet->getPageMargins()->setLeft(0.1);
		$sheet->getPageMargins()->setBottom(0.5);
		$sheet->getPageSetup()->setHorizontalCentered(true);

		$lastCol = 51;
		$widthCol = 1.8;

		//스타일
		include_once("../excel/style.php");
		include("../excel/init.php");


		//기본설정
		$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕


		//폰트설정
		$fontSize = 13;
		$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

		$rH = $rowH * $fontSize / $defFontSize;


		/** 타이틀 *******************************************************/
			$cellF = 'A';

			if ($apprline['prt_yn'] == 'Y'){
				$cellT = 'AC';
			}else{
				$cellT = 'AY';
			}

			$rowNo = 1;
			$sheet->getCell($cellF.$rowNo)->setValue($name.'대상자 '.$year.'년 '.$m.'월 서비스 내역');
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));


		/*********************************************************/
			//폰트설정
			$fontSize = 9;
			$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
			$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

			$rH = $rowH * $fontSize / $defFontSize;


		/** 결재란 *******************************************************/
			if ($apprline['prt_yn'] == 'Y'){
				$rowNo = 1;
				$sheet->SetData( Array('F'=>'AD'.$rowNo, 'T'=>'AE'.($rowNo+2),'val'=>"결\n재") );

				if ($apprline['line_cnt'] == 5){
					$cellPos[0]['F'] = 'AF';
					$cellPos[0]['T'] = GetNextCellId($cellPos[0]['F'],3);
					$cellPos[1]['F'] = GetNextCellId($cellPos[0]['T']);
					$cellPos[1]['T'] = GetNextCellId($cellPos[1]['F'],3);
					$cellPos[2]['F'] = GetNextCellId($cellPos[1]['T']);
					$cellPos[2]['T'] = GetNextCellId($cellPos[2]['F'],3);
					$cellPos[3]['F'] = GetNextCellId($cellPos[2]['T']);
					$cellPos[3]['T'] = GetNextCellId($cellPos[3]['F'],3);
					$cellPos[4]['F'] = GetNextCellId($cellPos[3]['T']);
					$cellPos[4]['T'] = GetNextCellId($cellPos[4]['F'],3);
				}else if ($apprline['line_cnt'] == 4){
					$cellPos[0]['F'] = 'AF';
					$cellPos[0]['T'] = GetNextCellId($cellPos[0]['F'],4);
					$cellPos[1]['F'] = GetNextCellId($cellPos[0]['T']);
					$cellPos[1]['T'] = GetNextCellId($cellPos[1]['F'],4);
					$cellPos[2]['F'] = GetNextCellId($cellPos[1]['T']);
					$cellPos[2]['T'] = GetNextCellId($cellPos[2]['F'],4);
					$cellPos[3]['F'] = GetNextCellId($cellPos[2]['T']);
					$cellPos[3]['T'] = GetNextCellId($cellPos[3]['F'],4);
				}else{
					$cellPos[0]['F'] = 'AF';
					$cellPos[0]['T'] = GetNextCellId($cellPos[0]['F'],6);
					$cellPos[1]['F'] = GetNextCellId($cellPos[0]['T']);
					$cellPos[1]['T'] = GetNextCellId($cellPos[1]['F'],6);
					$cellPos[2]['F'] = GetNextCellId($cellPos[1]['T']);
					$cellPos[2]['T'] = GetNextCellId($cellPos[2]['F'],5);
				}

				$cellPosCnt = SizeOf($cellPos);

				for($k=0; $k<$cellPosCnt; $k++) $sheet->SetData( Array('F'=>$cellPos[$k]['F'].$rowNo, 'T'=>$cellPos[$k]['T'].$rowNo, 'val'=>$apprline['name'][$k], 'border'=>'BHLH') );

				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 2);
				for($k=0; $k<$cellPosCnt; $k++) $sheet->SetData( Array('F'=>$cellPos[$k]['F'].$rowNo, 'T'=>$cellPos[$k]['T'].($rowNo+1), 'border'=>'THLH') );
			}

		/** 공란 *******************************************************/
			$cellF = 'A';
			$cellT = 'AY';
			$rowNo = 4;
			$sheet->getRowDimension($rowNo)->setRowHeight(5);
			$sheet->getCell($cellF.$rowNo)->setValue('');
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);


		/** 데이타 *******************************************************/
			$data['TITLE'][] = DefaultData(Array('from'=>'A','to'=>'D','val'=>'일자','bgcolor'=>'EAEAEA'));
			$data['TITLE'][] = DefaultData(Array('from'=>'E','to'=>'P','val'=>'서비스','bgcolor'=>'EAEAEA'));
			$data['TITLE'][] = DefaultData(Array('from'=>'Q','to'=>'Z','val'=>'자원','bgcolor'=>'EAEAEA'));
			$data['TITLE'][] = DefaultData(Array('from'=>'AA','to'=>'AF','val'=>'담당자','bgcolor'=>'EAEAEA'));
			$data['TITLE'][] = DefaultData(Array('from'=>'AG','to'=>'AY','val'=>'비고','bgcolor'=>'EAEAEA'));
			/*
				$data['TITLE'][] = DefaultData(Array('from'=>'A','to'=>'D','val'=>'일자','bgcolor'=>'EAEAEA'));
				$data['TITLE'][] = DefaultData(Array('from'=>'E','to'=>'R','val'=>'서비스','bgcolor'=>'EAEAEA'));
				$data['TITLE'][] = DefaultData(Array('from'=>'S','to'=>'AF','val'=>'자원','bgcolor'=>'EAEAEA'));
				$data['TITLE'][] = DefaultData(Array('from'=>'AG','to'=>'AM','val'=>'담당자','bgcolor'=>'EAEAEA'));
				$data['TITLE'][] = DefaultData(Array('from'=>'AN','to'=>'AY','val'=>'비고','bgcolor'=>'EAEAEA'));
			*/
			
			$sql = 'SELECT jumin, org_type as mkind, date, suga_cd, resource_cd as mem_cd1, mem_cd as mem_cd2, contents
					FROM   care_works_log
					WHERE  org_no	= \''.$orgNo.'\'
					AND	   org_type	= \''.$SR.'\'';
			if($month){
				$sql .= ' AND		LEFT(date,6) = \''.$year.($m < 10 ? '0' : '').$m.'\'';
			}else {
				$sql .= ' AND		LEFT(date,4) = \''.$year.'\'';
			}
			
			
			//$log = $conn->_fetch_array($sql, 'jumin');

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				
				$log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['mem_cd1']][$row['mem_cd2']]['contents'] = $row['contents'];


			}

			$sql = 'SELECT	t01_sugup_date AS date
					,		t01_mkind as mkind
					,		LEFT(t01_sugup_date,4) AS year
					,		MID(t01_sugup_date,5,2) AS month
					,		MID(t01_sugup_date,7) AS day
					,		t01_sugup_yoil AS week
					,		t01_suga_code1 AS suga_cd
					,		a.suga_nm
					,	    t01_jumin AS jumin
					,		t01_yoyangsa_id1 AS res_cd
					,		t01_yoyangsa_id2 AS mem_cd
					,		t01_yname1 AS res_nm
					,		t01_yname2 AS mem_nm
					/*,		IFNULL(b.contents,c.content) AS contents*/
					FROM	t01iljung
					INNER	JOIN (
							SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt, to_dt
							FROM	care_suga
							WHERE	org_no	= \''.$orgNo.'\'
							AND		suga_sr = \''.$SR.'\'
							UNION	ALL
							SELECT	\''.$orgNo.'\', \''.$SR.'\', LEFT(code,5), MID(code,6), name, from_dt, to_dt
							FROM	care_suga_comm
							) AS a
							ON		a.org_no = t01_ccode
							AND		a.suga_sr = t01_mkind
							AND		CONCAT(a.suga_cd,a.suga_sub)	 = t01_suga_code1
							AND		REPLACE(a.from_dt,\'-\',\'\')	<= t01_sugup_date
							AND		REPLACE(a.to_dt,\'-\',\'\')		>= t01_sugup_date
					/*
					LEFT	JOIN	care_works_log AS b
							ON		b.org_no	= t01_ccode
							AND		b.org_type	= t01_mkind
							AND		b.date		= t01_sugup_date
							AND		b.jumin		= t01_jumin
							AND		b.suga_cd	= t01_suga_code1
							AND		b.resource_cd = t01_yoyangsa_id1
							AND		b.mem_cd	= t01_yoyangsa_id2
					*/
					LEFT	JOIN	care_result AS c
							ON		c.org_no	= t01_ccode
							AND		c.org_type	= t01_mkind
							AND		c.jumin		= t01_jumin
							AND		c.date		= t01_sugup_date
							AND		c.time		= t01_sugup_fmtime
							AND		c.seq		= t01_sugup_seq
							AND		c.no		= \'1\'
					WHERE	t01_ccode		= \''.$orgNo.'\'
					AND		t01_mkind		= \''.$SR.'\'
					AND		t01_jumin		= \''.$jumin.'\'
					/*AND		t01_status_gbn	= \'1\'*/
					AND		t01_del_yn		= \'N\'';
					
					
					$sql .= ' AND		LEFT(t01_sugup_date,6) = \''.$year.($m < 10 ? '0' : '').$m.'\'';
					

					if($_POST['align'] == 1){
						$sql .= ' ORDER	BY date DESC, suga_nm, res_nm, mem_nm';
					}else {
						$sql .= ' ORDER	BY date ASC, suga_nm, res_nm, mem_nm';
					}
			
		
			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($date != $row['date']){
					$date = $row['date'];
					$border = 'BH';
					$dtI = $i;
				}else{
					$border = 'THBH';
				}


				$data[$i][] = DefaultData(Array('from'=>'A','to'=>'D','align'=>'R','border'=>$border,'val'=>IntVal($row['day']).'('.$weekly[$row['week']].')'));
				$data[$i][] = DefaultData(Array('from'=>'E','to'=>'P','align'=>'L','border'=>$border,'val'=>$row['suga_nm']));
				$data[$i][] = DefaultData(Array('from'=>'Q','to'=>'Z','align'=>'L','border'=>$border,'val'=>$row['res_nm']));
				$data[$i][] = DefaultData(Array('from'=>'AA','to'=>'AF','align'=>'L','border'=>$border,'val'=>$row['mem_nm']));
				$data[$i][] = DefaultData(Array('from'=>'AG','to'=>'AY','align'=>'L','border'=>$border,'val'=>StripSlashes($log[$row['jumin']][$row['mkind']][$row['date']][$row['suga_cd']][$row['res_cd']][$row['mem_cd']]['contents'])));
				/*
					$data[$i][] = DefaultData(Array('from'=>'A','to'=>'D','align'=>'R','border'=>$border,'val'=>IntVal($row['day']).'('.$weekly[$row['week']].')'));
					$data[$i][] = DefaultData(Array('from'=>'E','to'=>'R','align'=>'L','border'=>$border,'val'=>$row['suga_nm']));
					$data[$i][] = DefaultData(Array('from'=>'S','to'=>'AF','align'=>'L','border'=>$border,'val'=>$row['res_nm']));
					$data[$i][] = DefaultData(Array('from'=>'AG','to'=>'AM','align'=>'L','border'=>$border,'val'=>$row['mem_nm']));
					$data[$i][] = DefaultData(Array('from'=>'AN','to'=>'AY','align'=>'L','border'=>$border,'val'=>StripSlashes($row['contents'])));
				*/

				if ($i != $dtI){
					$data[$dtI][0]['count'] ++;
					$data[$dtI][SizeOf($data[$dtI])-1]['count'] ++;
				}


				//서비스 현황 집계
				$cur1 = lfSetArr($SUM, $row['suga_cd']);
				if ($cur1 < 0){
					$SUM[]['V'] = Array('cd'=>$row['suga_cd'], 'nm'=>$row['suga_nm'], 'cnt'=>0);
					$cur1 = SizeOf($SUM) - 1;
				}

				$cur2 = lfSetArr($SUM[$cur1]['L'], $row['res_cd']);
				if ($cur2 < 0){
					$SUM[$cur1]['L'][]['V'] = Array('cd'=>$row['res_cd'], 'nm'=>$row['res_nm'], 'cnt'=>0);
					$cur2 = SizeOf($SUM[$cur1]['L']) - 1;
				}

				$cur3 = lfSetArr($SUM[$cur1]['L'][$cur2]['L'], $row['mem_cd']);
				if ($cur3 < 0){
					$SUM[$cur1]['L'][$cur2]['L'][]['V'] = Array('cd'=>$row['mem_cd'], 'nm'=>$row['mem_nm'], 'cnt'=>0);
					$cur3 = SizeOf($SUM[$cur1]['L'][$cur2]['L']) - 1;
				}

				$SUM[$cur1]['L'][$cur2]['L'][$cur3]['V']['cnt'] ++;
			}

			$conn->row_free();


		/** 출력 *******************************************************/
			foreach($data as $gbn => $row){
				$rowNo ++;

				foreach($row as $idx => $col){
					if ($idx > 0) $col['count'] = 0;

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

					//$rowCnt = GetRowcount($col['val'],43);
					$rowCnt = GetRowcount($col['val'],70);

					$val['height'] = $rowCnt * $rH;

					$sheet->getRowDimension($rowNo)->setRowHeight($val['height'] > 0 ? $val['height'] : $rH);
					$sheet->getCell($cellF.$rowNo)->setValue($col['val'] ? $col['val'] : '');
					$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
					$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal($align);
					$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+$col['count']))->applyFromArray(fnStyle($col['border']));
					$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+$col['count']));

					if ($col['bgcolor']) $sheet->setBGColor($cellF.$rowNo, $col['bgcolor']);
					if ($col['type'] == 'number') $sheet->getStyle($cellF.$rowNo)->getNumberFormat()->setFormatCode('#,##0');
				}
			}

			Unset($data);


		/** 서비스 이용현황 집계 ****************************************/
			if ($svcStatPrtYn == 'Y'){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH * 0.5);
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'AY'.$rowNo, 'border'=>'RNBNLN') );


				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight($rH);

				$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>'No', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>'서비스', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>'자원', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>'담당자', 'backcolor'=>'EAEAEA') );
				$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>'서비스 횟수', 'backcolor'=>'EAEAEA') );
				/*
					$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>'No', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'R'.$rowNo, 'val'=>'서비스', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'S'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>'자원', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>'담당자', 'backcolor'=>'EAEAEA') );
					$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>'서비스 횟수', 'backcolor'=>'EAEAEA') );
				*/


				if (is_array($SUM)){
					$sumCnt = SizeOf($SUM);

					for($i=0; $i<$sumCnt-1; $i++){
						for($j=$i+1; $j<$sumCnt; $j++){
							if ($SUM[$i]['V']['nm'] > $SUM[$j]['V']['nm']){
								$tmpSUM = $SUM[$i];
								$SUM[$i] = $SUM[$j];
								$SUM[$j] = $tmpSUM;
							}
						}
					}

					$no = 1;

					foreach($SUM as $tmp1 => $R1){
						foreach($R1['L'] as $tmp2 => $R2){
							foreach($R2['L'] as $tmp3 => $R3){
								$rowNo ++;
								$sheet->getRowDimension($rowNo)->setRowHeight($rH);

								$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$no, 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'P'.$rowNo, 'val'=>$R1['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'Q'.$rowNo, 'T'=>'Z'.$rowNo, 'val'=>$R2['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AA'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>$R3['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>$R3['V']['cnt'], 'H'=>'R', 'border'=>'THBH') );
								/*
									$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$no, 'border'=>'THBH') );
									$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'R'.$rowNo, 'val'=>$R1['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
									$sheet->SetData( Array('F'=>'S'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>$R2['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
									$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>$R3['V']['nm'], 'H'=>'L', 'border'=>'THBH') );
									$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>$R3['V']['cnt'], 'H'=>'R', 'border'=>'THBH') );
								*/

								$no ++;
							}
						}
					}


					/*
					$sumCnt1 = SizeOf($SUM);
					$no = 1;

					for($i=0; $i<$sumCnt1-1; $i++){
						for($j=$i+1; $j<$sumCnt1; $j++){
							if ($SUM[$i]['nm'] > $SUM[$j]['nm']){
								$tmpSUM = $SUM[$i];
								$SUM[$i] = $SUM[$j];
								$SUM[$j] = $tmpSUM;
							}
						}
					}

					#foreach($SUM as $tmp1 => $R1){
					#	foreach($R1 as $tmp2 => $R2){
					for($i=0; $i<SizeOf($SUM); $i++){
						for($j=0; $j<SizeOf($SUM[$i]); $j++){
							for($k=0; $k<SizeOf($SUM[$i][$j]); $j++){
								$rowNo ++;
								$sheet->getRowDimension($rowNo)->setRowHeight($rH);
								$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$no, 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'R'.$rowNo, 'val'=>$SUM[$i]['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'S'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>$SUM[$i][$j]['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>$SUM[$i][$j][$k]['nm'], 'H'=>'L', 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>$SUM[$i][$j][$k]['svc'], 'H'=>'R', 'border'=>'THBH') );
							}
						}
					}
					*/

					/*
					foreach($sum as $sugaCd => $R1){
						foreach($R1['L'] as $resCd => $R2){
							foreach($R2['L'] as $memCd => $R3){
								$rowNo ++;
								$sheet->getRowDimension($rowNo)->setRowHeight($rH);
								$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>$no, 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'R'.$rowNo, 'val'=>$R1['name'], 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'S'.$rowNo, 'T'=>'AF'.$rowNo, 'val'=>$R2['name'], 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AG'.$rowNo, 'T'=>'AM'.$rowNo, 'val'=>$R3['name'], 'border'=>'THBH') );
								$sheet->SetData( Array('F'=>'AN'.$rowNo, 'T'=>'AY'.$rowNo, 'val'=>$R3['svc'], 'border'=>'THBH') );

								$no ++;
							}
						}
					}
					*/

					Unset($SUM);
				}
			}


		/** 기관명 *******************************************************/
			$fontSize = 13;
			$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
			$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

			$rH = $rowH * $fontSize / $defFontSize;


			$cellF = 'A';
			$cellT = 'AY';
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->getCell($cellF.$rowNo)->setValue($orgNm);
			$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('LNRNBN'));
			$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);


		/*********************************************************/


		//시트인덱스
		$loopIdx ++;
	}


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
		,	'height'=>$val['height']
		);

		return $tmpRow;
	}


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once("../inc/_db_close.php");
?>
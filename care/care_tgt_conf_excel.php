<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../excel/PHPExcel.php');

	// Create new PHPExcel object

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.4);
	$sheet->getPageMargins()->setRight(0.2);
	$sheet->getPageMargins()->setLeft(0.2);
	$sheet->getPageMargins()->setBottom(0.4);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-type: charset=euc-kr');
	header('Content-Disposition: attachment;filename="report_'.Date('YmdHis').'.xls"');
	header('Content-Description: test');
	header('Cache-Control: max-age=0');

	//ini_set("memory_limit","32M");

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);


	include_once('../excel/style.php');


	$sheet->getColumnDimension('A')->setWidth(20);
	$sheet->getColumnDimension('B')->setWidth(20);
	$sheet->getColumnDimension('C')->setWidth(15);
	$sheet->getColumnDimension('D')->setWidth(15);
	$sheet->getColumnDimension('E')->setWidth(10);
	$sheet->getColumnDimension('F')->setWidth(10);
	$sheet->getColumnDimension('G')->setWidth(10);
	$sheet->getColumnDimension('H')->setWidth(10);


	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);
	$birthday = $myF->issToBirthday($jumin,'.');


	//타이틀
	$rowNo = 1;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'H'.$rowNo, 'val'=>"대상자별 실적", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>17, 'bold'=>true) );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.$rowNo, 'val'=>'대상자명 : '.$name.'('.$birthday.')', 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'H'.$rowNo, 'val'=>'조회기간 : '.$myF->dateStyle($fromDt,'.').' ~ '.$myF->dateStyle($toDt,'.'), 'H'=>'R', 'border'=>'TNRNBNLN', 'size'=>9, 'bold'=>true) );

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"대분류", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>"중분류", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>"소분류", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"서비스명", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"년", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"월", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"일", 'H'=>'C', 'size'=>9, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"횟수", 'H'=>'C', 'size'=>9, 'bold'=>true) );


	$sql = 'SELECT	b.cd1 AS mst_cd, b.cd2 AS pro_cd, b.cd3 AS svc_cd, a.suga_sub AS sub_cd
			,		b.nm1 AS mst_nm, b.nm2 AS pro_nm, b.nm3 AS svc_nm, a.suga_nm AS sub_nm
			,		t01_sugup_date AS date
			FROM	t01iljung
			INNER	JOIN	care_suga AS a
					ON		a.org_no  = t01_ccode
					AND		a.suga_sr = t01_mkind
					AND		CONCAT(a.suga_cd,a.suga_sub) = t01_suga_code1
					AND		DATE_FORMAT(a.from_dt, \'%Y%m%d\') <= t01_sugup_date
					AND		DATE_FORMAT(a.to_dt,   \'%Y%m%d\') >= t01_sugup_date
			INNER	JOIN	suga_care AS b
					ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
					AND		DATE_FORMAT(b.from_dt, \'%Y%m%d\') <= t01_sugup_date
					AND		DATE_FORMAT(b.to_dt,   \'%Y%m%d\') >= t01_sugup_date
			WHERE	t01_ccode  = \''.$orgNo.'\'
			AND		t01_mkind  = \''.$SR.'\'
			AND		t01_jumin  = \''.$jumin.'\'
			AND		t01_del_yn = \'N\'
			AND		t01_status_gbn = \'1\'
			AND		t01_sugup_date BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_cd, date';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$y = IntVal(SubStr($row['date'],0,4));
		$m = IntVal(SubStr($row['date'],4,2));
		$d = IntVal(SubStr($row['date'],6,2));

		if (!$data[$row['mst_cd']])
			 $data[$row['mst_cd']]['name'] = str_replace('<br>',chr(13).chr(10),$row['mst_nm']);
		if (!$data[$row['mst_cd']]['LIST'][$row['pro_cd']])
			 $data[$row['mst_cd']]['LIST'][$row['pro_cd']]['name'] = str_replace('<br>',chr(13).chr(10),$row['pro_nm']);
		if (!$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']])
			 $data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['name'] = str_replace('<br>',chr(13).chr(10),$row['svc_nm']);
		if (!$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']])
			 $data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['name'] = str_replace('<br>',chr(13).chr(10),$row['sub_nm']);

		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['LIST'][$m]['LIST'][$d]['value'] ++;
		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['LIST'][$m]['value'] ++;
		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['value'] ++;
		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['value'] ++;
		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['value'] ++;
		$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['value'] ++;
		$data[$row['mst_cd']]['value'] ++;

		if ($tmpMPSUYMD != $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y.$m.$d){
			$tmpMPSUYMD = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y.$m.$d;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['LIST'][$m]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['rows'] ++;
			$data[$row['mst_cd']]['rows'] ++;
		}

		if ($tmpMPSUYM != $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y.$m){
			$tmpMPSUYM = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y.$m;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['LIST'][$y]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['rows'] ++;
			$data[$row['mst_cd']]['rows'] ++;
		}

		if ($tmpMPSUY != $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y){
			$tmpMPSUY = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'].$y;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['rows'] ++;
			$data[$row['mst_cd']]['rows'] ++;
		}

		if ($tmpMPSU != $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd']){
			$tmpMPSU = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd'];
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['rows'] ++;
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['rows'] ++;
			$data[$row['mst_cd']]['rows'] ++;
		}

		if ($tmpMPS != $row['mst_cd'].$row['pro_cd'].$row['svc_cd']){
			$tmpMPS = $row['mst_cd'].$row['pro_cd'].$row['svc_cd'];
			$data[$row['mst_cd']]['LIST'][$row['pro_cd']]['rows'] ++;
			$data[$row['mst_cd']]['rows'] ++;
		}

		if ($tmpMP != $row['mst_cd'].$row['pro_cd']){
			$tmpMP = $row['mst_cd'].$row['pro_cd'];
			$data[$row['mst_cd']]['rows'] ++;
		}
	}

	$conn->row_free();


	if (is_array($data)){
		$weekClr = Array(0=>'FF0000', 6=>'0000FF');
		$weekStr = Array(0=>'일', 1=>'월', 2=>'화', 3=>'수', 4=>'목', 5=>'금', 6=>'토');

		foreach($data as $mstCd => $R1){ $IsFirst[0] = true;
			foreach($R1['LIST'] as $proCd => $R2){ $IsFirst[1] = true;
				foreach($R2['LIST'] as $svcCd => $R3){ $IsFirst[2] = true;
					foreach($R3['LIST'] as $subCd => $R4){ $IsFirst[3] = true;
						foreach($R4['LIST'] as $year => $R5){ $IsFirst[4] = true;
							foreach($R5['LIST'] as $month => $R6){ $IsFirst[5] = true;
								foreach($R6['LIST'] as $day => $R7){
									$rowNo ++;
									$sheet->getRowDimension($rowNo)->setRowHeight(20);

									$w = Date('w',StrToTime($year.($month < 10 ? '0' : '').$month.($day < 10 ? '0' : '').$day));

									if ($IsFirst[0]){
										$IsFirst[0] = false;
										$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+$R1['rows']-1), 'val'=>$R1['name'], 'H'=>'L', 'V'=>'T', 'size'=>9) );
									}

									if ($IsFirst[1]){
										$IsFirst[1] = false;
										$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+$R2['rows']-1), 'val'=>$R2['name'], 'H'=>'L', 'V'=>'T', 'size'=>9) );
									}

									if ($IsFirst[2]){
										$IsFirst[2] = false;
										$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+$R3['rows']-1), 'val'=>$R3['name'], 'H'=>'L', 'V'=>'T', 'size'=>9) );
									}

									if ($IsFirst[3]){
										$IsFirst[3] = false;
										$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'D'.($rowNo+$R4['rows']-1), 'val'=>$R4['name'], 'H'=>'L', 'V'=>'T', 'size'=>9) );
									}

									if ($IsFirst[4]){
										$IsFirst[4] = false;
										$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'E'.($rowNo+$R5['rows']-1), 'val'=>$year.'년', 'H'=>'R', 'V'=>'T', 'size'=>9) );
									}

									if ($IsFirst[5]){
										$IsFirst[5] = false;
										$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'F'.($rowNo+$R6['rows']-1), 'val'=>$month.'월', 'H'=>'R', 'V'=>'T', 'size'=>9) );
									}

									$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$day.'일('.$weekStr[$w].')', 'H'=>'R', 'V'=>'T', 'size'=>9, 'color'=>$weekClr[$w]) );
									$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R7['value'], 'H'=>'R', 'V'=>'T', 'size'=>9) );
								}

								$rowNo ++;
								$sheet->getRowDimension($rowNo)->setRowHeight(20);
								$sheet->SetData( Array('F'=>'F'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>$year.'년 '.$month.'월 계', 'H'=>'R', 'backcolor'=>'E4F7BA', 'size'=>9, 'bold'=>true) );
								$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R6['value'], 'H'=>'R', 'backcolor'=>'E4F7BA', 'size'=>9, 'bold'=>true) );
							}

							$rowNo ++;
							$sheet->getRowDimension($rowNo)->setRowHeight(20);
							$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>$year.'년 '.count($R5['LIST']).'개월 계', 'H'=>'R', 'backcolor'=>'FFFF00', 'size'=>9, 'bold'=>true) );
							$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R5['value'], 'H'=>'R', 'backcolor'=>'FFFF00', 'size'=>9, 'bold'=>true) );
						}

						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight(20);
						$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>str_replace(chr(13).chr(10),'',$R4['name']).' 계', 'H'=>'R', 'backcolor'=>'FDE9D9', 'size'=>9, 'bold'=>true) );
						$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R4['value'], 'H'=>'R', 'backcolor'=>'FDE9D9', 'size'=>9, 'bold'=>true) );
					}

					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight(20);
					$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>str_replace(chr(13).chr(10),'',$R3['name']).' 계', 'H'=>'R', 'backcolor'=>'CEFBC9', 'size'=>9, 'bold'=>true) );
					$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R3['value'], 'H'=>'R', 'backcolor'=>'CEFBC9', 'size'=>9, 'bold'=>true) );
				}

				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight(20);
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>str_replace(chr(13).chr(10),'',$R2['name']).' 계', 'H'=>'R', 'backcolor'=>'D4F4FA', 'size'=>9, 'bold'=>true) );
				$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R2['value'], 'H'=>'R', 'backcolor'=>'D4F4FA', 'size'=>9, 'bold'=>true) );
			}

			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight(20);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>str_replace(chr(13).chr(10),'',$R1['name']).' 계', 'H'=>'R', 'backcolor'=>'DAD9FF', 'size'=>9, 'bold'=>true) );
			$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$R1['value'], 'H'=>'R', 'backcolor'=>'DAD9FF', 'size'=>9, 'bold'=>true) );
		}

		Unset($data);
		Unset($IsFirst);
	}

	//위치고정
	$sheet->freezePaneByColumnAndRow(0,4);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	require_once('../inc/_db_close.php');
?>
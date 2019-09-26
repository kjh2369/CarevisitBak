<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	//말일
	$lastday = $myF->lastDay($year,$month);

	//요일
	$weekly = Array(0=>'일',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'토');
	$weekClr= Array(0=>'FF0000',6=>'0000FF');


	$sheet->getColumnDimension('A')->setWidth(11);
	$sheet->getColumnDimension('B')->setWidth(14);
	$sheet->getColumnDimension('C')->setWidth(15);
	$sheet->getColumnDimension('D')->setWidth(15);
	$sheet->getColumnDimension('E')->setWidth(8);

	$nextCell = GetNextCellId('E');

	for($i=1; $i<=$lastday; $i++){
		$sheet->getColumnDimension($nextCell)->setWidth(4.5);

		$dayCellNo[$i] = $nextCell;
		$lastCol = $nextCell;
		$nextCell = GetNextCellId($nextCell);
	}


	//타이틀
	$rowNo = 1;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$lastCol .$rowNo, 'val'=>$year."년 ".$month."월 재가노인지원서비스 현황($storeNm)", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>17, 'bold'=>true) );

	//공란
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(10);

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
	//$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);


	//타이틀
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+1), 'val'=>"대분류", 'bold'=>true, 'border'=>'T', 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+1), 'val'=>"중분류", 'bold'=>true, 'border'=>'T', 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+1), 'val'=>"소분류", 'bold'=>true, 'border'=>'T', 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'D'.($rowNo+1), 'val'=>"세부사업명", 'bold'=>true, 'border'=>'T', 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'E'.($rowNo+1), 'val'=>"합계", 'border'=>'T', 'bold'=>true, 'backcolor'=>'E5E0EC') );

	for($i=1; $i<=$lastday; $i++){
		$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>$i, 'bold'=>true, 'border'=>'T', 'backcolor'=>'E5E0EC') );
	}

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);

	for($i=1; $i<=$lastday; $i++){
		$w = Date('w',StrToTime($yymm.($i < 10 ? '0' : '').$i));
		$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>$weekly[$w], 'bold'=>true, 'backcolor'=>'E5E0EC', 'color'=>$weekClr[$w]) );
	}


	//총계
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20); //
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"총   계", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"=SUM(".$dayCellNo[1].$rowNo.":".$dayCellNo[$lastday].$rowNo.")", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );

	for($i=1; $i<=$lastday; $i++){
		$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	}


	//수가
	$sql = 'SELECT	b.cd1 AS mst_cd, b.cd2 AS pro_cd, b.cd3 AS svc_cd, a.suga_sub AS sub_cd
			,		b.nm1 AS mst_nm, b.nm2 AS pro_nm, b.nm3 AS svc_nm, a.suga_nm AS sub_nm
			FROM	care_suga AS a
			INNER	JOIN	suga_care AS b
					ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
					AND		\''.$year.'\' BETWEEN DATE_FORMAT(b.from_dt,\'%Y\') AND DATE_FORMAT(b.to_dt,\'%Y\')
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.suga_sr	= \''.$SR.'\'
			AND		\''.$year.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y\') AND DATE_FORMAT(a.to_dt,\'%Y\')
			UNION	ALL
			SELECT	\'Y\', \'OY\', \'01\', \'01\'
			,		\'\', \'\', \'공통수가\', \'요양보호사 일업무\'
			ORDER	BY mst_cd, pro_cd, svc_cd, sub_cd';
	
	if($debug){ 
		echo nl2br($sql); 
		exit;
	}
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$mst = $row['mst_cd'];
		$pro = $row['pro_cd'];
		$svc = $row['svc_cd'];
		$sub = $row['sub_cd'];

		if (!$DATA[$mst]) $DATA[$mst] = Array('name'=>$row['mst_nm'],'rows'=>0);
		if (!$DATA[$mst]['LIST'][$pro]) $DATA[$mst]['LIST'][$pro] = Array('name'=>$row['pro_nm'],'rows'=>0);
		if (!$DATA[$mst]['LIST'][$pro]['LIST'][$svc]) $DATA[$mst]['LIST'][$pro]['LIST'][$svc] = Array('name'=>$row['svc_nm'],'rows'=>0);

		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['LIST'][$sub] = Array('name'=>$row['sub_nm']);

		$DATA[$mst]['rows'] ++;
		$DATA[$mst]['LIST'][$pro]['rows'] ++;
		$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['rows'] ++;

		if ($tmpCd1 != $mst.$pro.$svc){
			$DATA[$mst]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['LIST'][$svc]['rows'] ++;
			$tmpCd1 = $mst.$pro.$svc;
		}

		if ($tmpCd2 != $mst.$pro){
			$DATA[$mst]['rows'] ++;
			$DATA[$mst]['LIST'][$pro]['rows'] ++;
			$tmpCd2 = $mst.$pro;
		}

		if ($tmpCd3 != $mst){
			$DATA[$mst]['rows'] ++;
			$tmpCd3 = $mst;
		}
	}

	$conn->row_free();


	//데이타
	/*$sql = 'SELECT	CAST(MID(t01_sugup_date,7,2) AS unsigned) AS day
			,		MID(t01_suga_code1,1,1) AS mst_cd
			,		MID(t01_suga_code1,2,2) AS pro_cd
			,		MID(t01_suga_code1,4,2) AS svc_cd
			,		MID(t01_suga_code1,6,2) AS sub_cd
			,		COUNT(t01_jumin) AS itm_cnt
			,		COUNT(DISTINCT t01_jumin) AS per_cnt
			FROM	t01iljung
			INNER	JOIN	(
					SELECT	CONCAT(a.suga_cd, a.suga_sub) AS suga_cd, DATE_FORMAT(a.from_dt, \'%Y%m%d\') AS from_dt, DATE_FORMAT(a.to_dt, \'%Y%m%d\') AS to_dt
					FROM	care_suga AS a
					WHERE	a.org_no	= \''.$orgNo.'\'
					AND		a.suga_sr	= \''.$SR.'\'
					AND		\''.$year.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y\') AND DATE_FORMAT(a.to_dt,\'%Y\')
					UNION	ALL
					SELECT	\'YOY0101\', \'19990101\', \'99991231\'
					) AS a
					ON		a.suga_cd	 = t01_suga_code1
					AND		a.from_dt	<= t01_sugup_date
					AND		a.to_dt		>= t01_sugup_date
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
			GROUP	BY t01_sugup_date, t01_suga_code1
			ORDER	BY t01_sugup_date, t01_suga_code1';*/
	$sql = 'SELECT	CAST(MID(a.date,7,2) AS unsigned) AS day
			,		MID(a.suga_cd,1,1) AS mst_cd
			,		MID(a.suga_cd,2,2) AS pro_cd
			,		MID(a.suga_cd,4,2) AS svc_cd
			,		MID(a.suga_cd,6,2) AS sub_cd
			,		SUM(a.itm_cnt) AS itm_cnt
			,		SUM(a.per_cnt) AS per_cnt
			FROM	(
					SELECT	t01_suga_code1 AS suga_cd
					,		t01_sugup_date AS date
					,		COUNT(t01_jumin) AS itm_cnt
					,		COUNT(DISTINCT t01_jumin) AS per_cnt
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_mkind	= \''.$SR.'\'
					AND		t01_del_yn	= \'N\'
					AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
					GROUP	BY t01_sugup_date, t01_suga_code1
					UNION	ALL
					SELECT	suga_cd, reg_dt
					,		COUNT(suga_cd)
					,		SUM(att_cnt)
					FROM	care_rpt
					WHERE	org_no = \''.$orgNo.'\'
					AND		org_sr = \''.$SR.'\'
					AND		del_flag = \'N\'
					AND		LEFT(reg_dt, 6) = \''.$yymm.'\'
					GROUP	BY reg_dt, suga_cd
					) AS a
			INNER	JOIN	(
					SELECT	CONCAT(suga_cd, suga_sub) AS suga_cd
					,		DATE_FORMAT(from_dt, \'%Y%m%d\') AS from_dt
					,		DATE_FORMAT(to_dt, \'%Y%m%d\') AS to_dt
					FROM	care_suga
					WHERE	org_no	= \''.$orgNo.'\'
					AND		suga_sr	= \''.$SR.'\'
					AND		\''.$year.'\' BETWEEN DATE_FORMAT(from_dt,\'%Y\') AND DATE_FORMAT(to_dt,\'%Y\')
					UNION	ALL
					SELECT	\'YOY0101\', \'19990101\', \'99991231\'
					) AS b
					ON		b.suga_cd	 = a.suga_cd
					AND		b.from_dt	<= a.date
					AND		b.to_dt		>= a.date
			GROUP	BY a.date, a.suga_cd
			ORDER	BY a.date, a.suga_cd
			';
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($unit[$row['mst_cd'].$row['pro_cd'].$row['svc_cd']]['gbn']== '1'){
			$cnt = $row['per_cnt'];
			$str = '명';
		}else{
			$cnt = $row['itm_cnt'];
			$str = '회';
		}

		$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['V'][$row['day']] = $cnt;
	}

	$conn->row_free();


	//소계
	if (is_array($DATA)){
		$mstNo = $rowNo;
		$mstRows = "";

		foreach($DATA as $mstCd => $R1){
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight(20);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+$R1['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R1['name']), 'size'=>9, 'bold'=>true) );
			$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"합   계", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true) );
			$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"=SUM(".$dayCellNo[1].$rowNo.":".$dayCellNo[$lastday].$rowNo.")", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );

			for($i=1; $i<=$lastday; $i++){
				$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'format'=>'#,###') );
			}

			$mstRows .= ($mstRows ? "+" : "=")."_CELL_".$rowNo;
			$proNo = $rowNo;
			$proRows = "";

			foreach($R1['LIST'] as $proCd => $R2){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight(20);
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+$R2['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R2['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"소   계", 'H'=>'R', 'backcolor'=>'FFFF00', 'bold'=>true) );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"=SUM(".$dayCellNo[1].$rowNo.":".$dayCellNo[$lastday].$rowNo.")", 'H'=>'R', 'backcolor'=>'FFFF00', 'format'=>'#,###', 'bold'=>true) );

				for($i=1; $i<=$lastday; $i++){
					$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FFFF00', 'format'=>'#,###') );
				}

				$proRows .= ($proRows ? "+" : "=")."_CELL_".$rowNo;
				$svcNo = $rowNo;
				$svcRows = "";

				foreach($R2['LIST'] as $svcCd => $R3){
					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight(20);
					$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+$R3['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R3['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
					$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"계", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true) );
					$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"=SUM(".$dayCellNo[1].$rowNo.":".$dayCellNo[$lastday].$rowNo.")", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true, 'format'=>'#,###') );

					for($i=1; $i<=$lastday; $i++){
						$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'E4F7BA', 'format'=>'#,###') );
					}

					$svcRows .= ($svcRows ? "+" : "=")."_CELL_".$rowNo;
					$subNo = $rowNo;

					foreach($R3['LIST'] as $subCd => $R4){
						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight(20);
						$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>str_replace('<br>',chr(13).chr(10),$R4['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
						$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"=SUM(".$dayCellNo[1].$rowNo.":".$dayCellNo[$lastday].$rowNo.")", 'H'=>'R', 'format'=>'#,###') );

						for($i=1; $i<=$lastday; $i++){
							$sheet->SetData( Array('F'=>$dayCellNo[$i].$rowNo, 'val'=>$R4['V'][$i], 'H'=>'R', 'format'=>'#,###') );
						}
					}

					//계
					for($i=1; $i<=$lastday; $i++) $sheet->getCell($dayCellNo[$i].$subNo)->setValue("=SUM(".$dayCellNo[$i].($subNo+1).":".$dayCellNo[$i].$rowNo.")");
				}

				//소계
				for($i=1; $i<=$lastday; $i++) $sheet->getCell($dayCellNo[$i].$svcNo)->setValue(str_replace("_CELL_",$dayCellNo[$i],$svcRows));
			}

			//합계
			for($i=1; $i<=$lastday; $i++) $sheet->getCell($dayCellNo[$i].$proNo)->setValue(str_replace("_CELL_",$dayCellNo[$i],$proRows));
		}

		//총계
		for($i=1; $i<=$lastday; $i++) $sheet->getCell($dayCellNo[$i].$mstNo)->setValue(str_replace("_CELL_",$dayCellNo[$i],$mstRows));
	}

	Unset($target);
	Unset($DATA);


	//위치고정
	$sheet->freezePaneByColumnAndRow(5,6);

	Unset($dayCellNo);
	Unset($weekly);
	Unset($weekClr);
?>
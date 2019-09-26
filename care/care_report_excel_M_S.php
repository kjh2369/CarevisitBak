<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');


	$sheet->getColumnDimension('A')->setWidth(15);
	$sheet->getColumnDimension('B')->setWidth(15);
	$sheet->getColumnDimension('C')->setWidth(20);
	$sheet->getColumnDimension('D')->setWidth(20);

	if ($prtGbn == '1'){
		$sheet->getColumnDimension('E')->setWidth(13);
		$sheet->getColumnDimension('F')->setWidth(13);
		$sheet->getColumnDimension('G')->setWidth(13);
		$sheet->getColumnDimension('H')->setWidth(13);
		$lastCol = 'H';
	}else{
		$sheet->getColumnDimension('E')->setWidth(13);
		$sheet->getColumnDimension('F')->setWidth(10);
		$sheet->getColumnDimension('G')->setWidth(10);
		$sheet->getColumnDimension('H')->setWidth(10);

		$nextCell = GetNextCellId('H');
		for($i=1; $i<=$quarter+2; $i++){
			$lastCol = $nextCell;
			$sheet->getColumnDimension($nextCell)->setWidth(13);
			$nextCell = GetNextCellId($nextCell);
		}
	}

	//타이틀
	$rowNo = 1;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$lastCol.$rowNo, 'val'=>$year."년 ".$quarter."/4분기 재가노인지원서비스 사업보고서", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>17, 'bold'=>true) );

	//공란
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(10);

	//기관명
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$lastCol.$rowNo, 'val'=>$storeNm, 'H'=>'R', 'border'=>'TNRNBNLN', 'size'=>13, 'bold'=>true) );

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
	//$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(17);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>$lastCol.$rowNo, 'val'=>"1.사업별현황", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>11, 'bold'=>true) );


	$fromYm = $year;
	$toYm = $year;

	if ($quarter == 1){
		$fromYm .= '01';
		$toYm .= '03';
		$months = Array(1,2,3);
	}else if ($quarter == 2){
		$fromYm .= '04';
		$toYm .= '06';
		$months = Array(4,5,6);
	}else if ($quarter == 3){
		$fromYm .= '07';
		$toYm .= '09';
		$months = Array(7,8,9);
	}else{
		$fromYm .= '10';
		$toYm .= '12';
		$months = Array(10,11,12);
	}


	//타이틀
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"대분류", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>"중분류", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>"소분류", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"세부사업명", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"목표", 'bold'=>true, 'backcolor'=>'E5E0EC') );

	if ($prtGbn == '1'){
		$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$quarter."분기", 'bold'=>true, 'backcolor'=>'E5E0EC') );
		$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"누계", 'bold'=>true, 'backcolor'=>'E5E0EC') );
		$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"달성률", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	}else{
		$nextCell = GetNextCellId('E');
		for($i=0; $i<3; $i++){
			$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>$months[$i]."월", 'bold'=>true, 'backcolor'=>'E5E0EC') );
			$nextCell = GetNextCellId($nextCell);
		}

		for($i=0; $i<$quarter; $i++){
			$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>($quarter - $i)."/4합계", 'bold'=>true, 'backcolor'=>'E5E0EC') );
			$nextCell = GetNextCellId($nextCell);
		}

		$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>$quarter."/4누계", 'bold'=>true, 'backcolor'=>'E5E0EC') );
		$nextCell = GetNextCellId($nextCell);

		$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>"달성률", 'bold'=>true, 'backcolor'=>'E5E0EC') );
		$nextCell = GetNextCellId($nextCell);
	}



	//총계
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"총   계", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );

	if ($prtGbn == '1'){
		$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'percent'=>true) );
	}else{
		$nextCell = GetNextCellId('G');
		$IsBreak = false;

		while(true){
			if ($lastCol == $nextCell) $IsBreak = true;

			$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###', 'percent'=>$lastCol == $nextCell ? true : false) );
			$nextCell = GetNextCellId($nextCell);

			if ($IsBreak) break;
		}
	}

	//총계
	$Cell['T']= $rowNo;

	//목표
	$sql = 'SELECT	plan_cd AS cd, plan_target As target
			FROM	care_year_plan
			WHERE	org_no		= \''.$orgNo.'\'
			AND		plan_year	= \''.$year.'\'
			AND		plan_sr		= \''.$SR.'\'
			AND		LENGTH(plan_cd) = 7';

	$target = $conn->_fetch_array($sql, 'cd');


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
	/*
	$sql = 'SELECT	LEFT(t01_sugup_date,6) AS yymm
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
			AND		LEFT(t01_sugup_date,6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
			GROUP	BY LEFT(t01_sugup_date,6), t01_suga_code1';
	 */
	$sql = 'SELECT	LEFT(a.date,6) AS yymm
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
					AND		LEFT(t01_sugup_date,6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
					GROUP	BY t01_sugup_date, t01_suga_code1
					UNION	ALL
					SELECT	suga_cd, reg_dt
					,		COUNT(suga_cd)
					,		SUM(att_cnt)
					FROM	care_rpt
					WHERE	org_no = \''.$orgNo.'\'
					AND		org_sr = \''.$SR.'\'
					AND		del_flag = \'N\'
					AND		LEFT(reg_dt, 6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
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
			GROUP	BY LEFT(a.date,6), a.suga_cd
			';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($unit[$row['mst_cd'].$row['pro_cd'].$row['svc_cd']]['gbn'] == '1'){
			$cnt = $row['per_cnt'];
			$str = '명';
		}else{
			$cnt = $row['itm_cnt'];
			$str = '회';
		}

		if ($prtGbn == '1'){
			if ($row['yymm'] >= $fromYm){
				$DATA[$row['mst_cd']]['cnt'] += $cnt;
				$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['cnt'] += $cnt;
				$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['cnt'] += $cnt;
				$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['cnt'] += $cnt;
			}

			$DATA[$row['mst_cd']]['tot'] += $cnt;
			$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['tot'] += $cnt;
			$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['tot'] += $cnt;
			$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['tot'] += $cnt;
		}else{
			$m = IntVal(SubStr($row['yymm'],4,2));

			for($j=0; $j<3; $j++){
				if ($months[$j] == $m){
					$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['M'][$m] += $cnt;
					break;
				}
			}

			if ($m >= 1 && $m <= 3){
				$q = 1;
			}else if ($m >= 4 && $m <= 6){
				$q = 2;
			}else if ($m >= 7 && $m <= 9){
				$q = 3;
			}else{
				$q = 4;
			}

			$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['LIST'][$row['svc_cd']]['LIST'][$row['sub_cd']]['Q'][$q] += $cnt;
		}
	}

	$conn->row_free();


	//소계
	if (is_array($DATA)){
		$totNo = $rowNo;
		$nextCell = GetNextCellId('D');

		while(true){
			$totVal[$nextCell] = "";
			$nextCell = GetNextCellId($nextCell);

			if ($lastCol == $nextCell) break;
		}

		foreach($DATA as $mstCd => $R1){
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight(20);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+$R1['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R1['name']), 'size'=>9, 'bold'=>true) );
			$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"합   계", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true) );
			$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );
			$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );
			$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );

			if ($prtGbn == '1'){
				$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'percent'=>true) );
			}else{
				$nextCell = GetNextCellId('G');
				$IsBreak = false;

				while(true){
					if ($lastCol == $nextCell) $IsBreak = true;

					$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'FDE9D9', 'format'=>'#,###', 'percent'=>$lastCol == $nextCell ? true : false) );
					$nextCell = GetNextCellId($nextCell);

					if ($IsBreak) break;
				}
			}

			$mstNo = $rowNo;
			$nextCell = GetNextCellId('D');

			while(true){
				$totVal[$nextCell] .= ($totVal[$nextCell] ? '+' : '=').$nextCell.$rowNo;
				$mstVal[$nextCell] = "";
				$nextCell = GetNextCellId($nextCell);

				if ($lastCol == $nextCell) break;
			}

			foreach($R1['LIST'] as $proCd => $R2){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight(20);
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+$R2['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R2['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"소   계", 'H'=>'R', 'backcolor'=>'FFFF00', 'bold'=>true) );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FFFF00', 'format'=>'#,###', 'bold'=>true) );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FFFF00', 'format'=>'#,###', 'bold'=>true) );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FFFF00', 'format'=>'#,###', 'bold'=>true) );

				if ($prtGbn == '1'){
					$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FFFF00', 'percent'=>true, 'bold'=>true) );
				}else{
					$nextCell = GetNextCellId('G');
					$IsBreak = false;

					while(true){
						if ($lastCol == $nextCell) $IsBreak = true;

						$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'FFFF00', 'format'=>'#,###', 'percent'=>$lastCol == $nextCell ? true : false) );
						$nextCell = GetNextCellId($nextCell);

						if ($IsBreak) break;
					}
				}

				$proNo = $rowNo;
				$nextCell = GetNextCellId('D');

				while(true){
					$mstVal[$nextCell] .= ($mstVal[$nextCell] ? '+' : '=').$nextCell.$rowNo;
					$proVal[$nextCell] = "";
					$nextCell = GetNextCellId($nextCell);

					if ($lastCol == $nextCell) break;
				}

				foreach($R2['LIST'] as $svcCd => $R3){
					$rowNo ++;
					$sheet->getRowDimension($rowNo)->setRowHeight(20);
					$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+$R3['rows']-1), 'val'=>str_replace('<br>',chr(13).chr(10),$R3['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
					$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"계", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true) );
					$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true, 'format'=>'#,###') );
					$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true, 'format'=>'#,###') );
					$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true, 'format'=>'#,###') );

					if ($prtGbn == '1'){
						$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'E4F7BA', 'bold'=>true, 'percent'=>true) );
					}else{
						$nextCell = GetNextCellId('G');
						$IsBreak = false;

						while(true){
							if ($lastCol == $nextCell) $IsBreak = true;

							$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'E4F7BA', 'format'=>'#,###', 'percent'=>$lastCol == $nextCell ? true : false) );
							$nextCell = GetNextCellId($nextCell);

							if ($IsBreak) break;
						}
					}

					$subNo = $rowNo;
					$nextCell = GetNextCellId('D');

					while(true){
						$proVal[$nextCell] .= ($proVal[$nextCell] ? '+' : '=').$nextCell.$rowNo;
						$nextCell = GetNextCellId($nextCell);

						if ($lastCol == $nextCell) break;
					}

					foreach($R3['LIST'] as $subCd => $R4){
						$rowNo ++;
						$sheet->getRowDimension($rowNo)->setRowHeight(20);
						$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>str_replace('<br>',chr(13).chr(10),$R4['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
						$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$target[$mstCd.$proCd.$svcCd.$subCd]['target'], 'H'=>'R', 'format'=>'#,###') );

						if ($prtGbn == '1'){
							$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$R4['cnt'] ? $R4['cnt'] : "", 'H'=>'R', 'format'=>'#,###') );
							$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$R4['tot'] ? $R4['tot'] : "", 'H'=>'R', 'format'=>'#,###') );
							$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"=G".$rowNo."/IF(E".$rowNo.">0,E".$rowNo.",1)", 'H'=>'R', 'percent'=>true) );
						}else{
							$nextCell = GetNextCellId('E');
							$IsBreak = false;
							$idxM = 0;
							$idxQ = $quarter;
							$strQ = "";
							$celQ = "";

							while(true){
								if ($lastCol == $nextCell) $IsBreak = true;

								if ($idxM < 3){
									$val = $R4['M'][$months[$idxM]] ? number_format($R4['M'][$months[$idxM]]) : "";
								}else if ($idxQ > 0){
									$val = $R4['Q'][$idxQ];
									$idxQ --;
									$strQ .= ($strQ ? "+" : "=")."IF($nextCell$rowNo > 0, $nextCell$rowNo, 0)";
								}else if ($idxQ == 0){
									$val = $strQ;
									$celQ = $nextCell.$rowNo;
									$idxQ --;
								}else{
									$val = "=$celQ / IF(E$rowNo > 0, E$rowNo, 1)";
								}

								$idxM ++;

								$sheet->SetData( Array('F'=>$nextCell.$rowNo, 'val'=>$val, 'H'=>'R', 'format'=>'#,###', 'percent'=>$lastCol == $nextCell ? true : false) );
								$nextCell = GetNextCellId($nextCell);

								if ($IsBreak) break;
							}
						}
					}

					//계
					$nextCell = GetNextCellId('D');

					while(true){
						$sheet->getCell($nextCell.$subNo)->setValue("=SUM($nextCell".($subNo+1).":$nextCell$rowNo)");
						$nextCell = GetNextCellId($nextCell);

						if ($lastCol == $nextCell) break;

						$percentCol = $nextCell;
					}

					$sheet->getCell($lastCol.$subNo)->setValue("=$percentCol$subNo / IF(E$subNo > 0, E$subNo, 1)");
				}

				//소계
				$nextCell = GetNextCellId('D');

				while(true){
					$sheet->getCell($nextCell.$proNo)->setValue($proVal[$nextCell]);
					$nextCell = GetNextCellId($nextCell);

					if ($lastCol == $nextCell) break;

					$percentCol = $nextCell;
				}

				$sheet->getCell($lastCol.$proNo)->setValue("=$percentCol$proNo / IF(E$proNo > 0, E$proNo, 1)");
			}

			//합계
			$nextCell = GetNextCellId('D');

			while(true){
				$sheet->getCell($nextCell.$mstNo)->setValue($mstVal[$nextCell]);
				$nextCell = GetNextCellId($nextCell);

				if ($lastCol == $nextCell) break;

				$percentCol = $nextCell;
			}

			$sheet->getCell($lastCol.$mstNo)->setValue("=$percentCol$mstNo / IF(E$mstNo > 0, E$mstNo, 1)");
		}

		//총계
		$nextCell = GetNextCellId('D');

		while(true){
			$sheet->getCell($nextCell.$totNo)->setValue($totVal[$nextCell]);
			$nextCell = GetNextCellId($nextCell);

			if ($lastCol == $nextCell) break;

			$percentCol = $nextCell;
		}

		$sheet->getCell($lastCol.$totNo)->setValue("=$percentCol$totNo / IF(E$totNo > 0, E$totNo, 1)");
	}

	Unset($target);
	Unset($DATA);


	//위치고정
	$sheet->freezePaneByColumnAndRow(5,7);
?>
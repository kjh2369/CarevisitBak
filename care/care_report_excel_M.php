<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');

	$sheet->getColumnDimension('A')->setWidth(15);
	$sheet->getColumnDimension('B')->setWidth(5);
	$sheet->getColumnDimension('C')->setWidth(25);
	$sheet->getColumnDimension('D')->setWidth(13);
	$sheet->getColumnDimension('E')->setWidth(13);
	$sheet->getColumnDimension('F')->setWidth(13);
	$sheet->getColumnDimension('G')->setWidth(13);

	//타이틀
	$rowNo = 1;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>$year."년 ".$quarter."/4분기 재가노인지원서비스 사업보고서", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>17, 'bold'=>true) );

	//공란
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(10);

	//기관명
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(20);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>$storeNm, 'H'=>'R', 'border'=>'TNRNBNLN', 'size'=>13, 'bold'=>true) );

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
	//$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(17);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'D'.$rowNo, 'val'=>"1.재가노인지원서비스 사업별현황", 'H'=>'L', 'border'=>'TNRNBNLN', 'size'=>11, 'bold'=>true) );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"(단위:명,회,%)", 'H'=>'R', 'border'=>'TNRNBNLN', 'size'=>11, 'bold'=>true) );


	//타이틀
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"대분류", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>"번호", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>"중분류", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"목표", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$quarter."분기", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"누계", 'bold'=>true, 'backcolor'=>'E5E0EC') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"달성률", 'bold'=>true, 'backcolor'=>'E5E0EC') );


	//총계
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight(30);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'C'.$rowNo, 'val'=>"총   계", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'format'=>'#,###') );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'bold'=>true, 'backcolor'=>'DDEEF3', 'percent'=>true) );

	//총계
	$Cell['T']= $rowNo;


	//목표
	$sql = 'SELECT	LEFT(plan_cd,3) AS cd, SUM(plan_target) As target
			FROM	care_year_plan
			WHERE	org_no		= \''.$orgNo.'\'
			AND		plan_year	= \''.$year.'\'
			AND		plan_sr		= \''.$SR.'\'
			AND		LENGTH(plan_cd) = 7
			GROUP	BY LEFT(plan_cd,3)';

	$target = $conn->_fetch_array($sql, 'cd');


	//수가
	$sql = 'SELECT	DISTINCT b.cd1 AS mst_cd, b.cd2 AS pro_cd, b.nm1 AS mst_nm, b.nm2 AS pro_nm
			FROM	care_suga AS a
			INNER	JOIN	suga_care AS b
					ON		CONCAT(b.cd1, b.cd2, b.cd3) = a.suga_cd
					AND		\''.$year.'\' BETWEEN DATE_FORMAT(b.from_dt,\'%Y\') AND DATE_FORMAT(b.to_dt,\'%Y\')
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.suga_sr	= \''.$SR.'\'
			AND		\''.$year.'\' BETWEEN DATE_FORMAT(a.from_dt,\'%Y\') AND DATE_FORMAT(a.to_dt,\'%Y\')';

	$sql .= '
			UNION	ALL
			SELECT	\'Y\', \'OY\', \'\', \'공통수가\'';

	$sql .= '
			ORDER	BY mst_cd, pro_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$mst = $row['mst_cd'];
		$pro = $row['pro_cd'];

		if (!$DATA[$mst]) $DATA[$mst] = Array('name'=>$row['mst_nm'],'rows'=>0,'cnt'=>0);

		$DATA[$mst]['LIST'][$pro] = Array('name'=>$row['pro_nm'],'cnt'=>0);
		$DATA[$mst]['rows'] ++;
	}

	$conn->row_free();


	$fromYm = $year;
	$toYm = $year;

	if ($quarter == 1){
		$fromYm .= '01';
		$toYm .= '03';
	}else if ($quarter == 2){
		$fromYm .= '04';
		$toYm .= '06';
	}else if ($quarter == 3){
		$fromYm .= '07';
		$toYm .= '09';
	}else{
		$fromYm .= '10';
		$toYm .= '12';
	}


	//데이타
	/*$sql = 'SELECT	LEFT(t01_sugup_date,6) AS yymm
			,		MID(t01_suga_code1,1,1) AS mst_cd
			,		MID(t01_suga_code1,2,2) AS pro_cd
			,		MID(t01_suga_code1,4,2) AS svc_cd
			,		MID(t01_suga_code1,6,2) AS sub_cd
			,		COUNT(t01_jumin) AS itm_cnt
			,		COUNT(DISTINCT t01_jumin) AS per_cnt
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
			GROUP	BY LEFT(t01_sugup_date,6), t01_suga_code1
			ORDER	BY t01_sugup_date, t01_suga_code1
			';*/

	$sql = 'SELECT	t01_suga_code1 AS suga_cd
			,		LEFT(t01_sugup_date,6) AS yymm
			,		MID(t01_suga_code1,1,1) AS mst_cd
			,		MID(t01_suga_code1,2,2) AS pro_cd
			,		MID(t01_suga_code1,4,2) AS svc_cd
			,		MID(t01_suga_code1,6,2) AS sub_cd
			,		COUNT(t01_jumin) AS itm_cnt
			,		COUNT(DISTINCT t01_jumin) AS per_cnt
			FROM	t01iljung
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \''.$SR.'\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
			GROUP	BY LEFT(t01_sugup_date,6), t01_suga_code1
			UNION	ALL
			SELECT	suga_cd
			,		LEFT(reg_dt, 6)
			,		MID(suga_cd, 1, 1)
			,		MID(suga_cd, 2, 2)
			,		MID(suga_cd, 4, 2)
			,		MID(suga_cd, 6, 2)
			,		COUNT(suga_cd)
			,		SUM(att_cnt)
			FROM	care_rpt
			WHERE	org_no = \''.$orgNo.'\'
			AND		org_sr = \''.$SR.'\'
			AND		del_flag = \'N\'
			AND		LEFT(reg_dt, 6) BETWEEN \''.$year.'01\' AND \''.$toYm.'\'
			GROUP	BY LEFT(reg_dt, 6), suga_cd
			ORDER	BY yymm, suga_cd
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

		if ($row['yymm'] >= $fromYm){
			$DATA[$row['mst_cd']]['cnt'] += $cnt;
			$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['cnt'] += $cnt;
		}

		$DATA[$row['mst_cd']]['tot'] += $cnt;
		$DATA[$row['mst_cd']]['LIST'][$row['pro_cd']]['tot'] += $cnt;
	}

	$conn->row_free();


	//소계
	if (is_array($DATA)){
		$no = 1;

		foreach($DATA as $mstCd => $R1){
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight(30);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+$R1['rows']), 'val'=>str_replace('<br>',chr(13).chr(10),$R1['name']), 'size'=>9, 'bold'=>true) );
			$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'C'.$rowNo, 'val'=>"소   계", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true) );
			$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );
			$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );
			$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'format'=>'#,###') );
			$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"", 'H'=>'R', 'backcolor'=>'FDE9D9', 'bold'=>true, 'percent'=>true) );

			//소계
			$Cell['S'] = $rowNo;
			$Cell['D'] .= ($Cell['D'] ? "+" : "=")."D".$rowNo; //목표
			$Cell['E'] .= ($Cell['E'] ? "+" : "=")."E".$rowNo; //분기
			$Cell['F'] .= ($Cell['F'] ? "+" : "=")."F".$rowNo; //누계

			foreach($R1['LIST'] as $proCd => $R2){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight(30);
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$no, 'bold'=>true) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>str_replace('<br>',chr(13).chr(10),$R2['name']), 'H'=>'L', 'size'=>9, 'bold'=>true) );
				$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$target[$mstCd.$proCd]['target'], 'H'=>'R', 'format'=>'#,###') );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$R2['cnt'] ? $R2['cnt'] : "", 'H'=>'R', 'format'=>'#,###') );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$R2['tot'] ? $R2['tot'] : "", 'H'=>'R', 'format'=>'#,###') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"=F".$rowNo."/D".$rowNo, 'H'=>'R', 'percent'=>true) );

				$no ++;
			}

			$sheet->getCell('D'.$Cell['S'])->setValue("=SUM(D".($Cell['S']+1).":D".$rowNo.")");
			$sheet->getCell('E'.$Cell['S'])->setValue("=SUM(E".($Cell['S']+1).":E".$rowNo.")");
			$sheet->getCell('F'.$Cell['S'])->setValue("=SUM(F".($Cell['S']+1).":F".$rowNo.")");
			$sheet->getCell('G'.$Cell['S'])->setValue("=F".($Cell['S']+1)."/D".($Cell['S']+1));
		}

		$sheet->getCell('D'.$Cell['T'])->setValue($Cell['D']);
		$sheet->getCell('E'.$Cell['T'])->setValue($Cell['E']);
		$sheet->getCell('F'.$Cell['T'])->setValue($Cell['F']);
		$sheet->getCell('G'.$Cell['T'])->setValue("=F".$Cell['T']."/D".$Cell['T']);
	}


	Unset($target);
	Unset($DATA);


	//위치고정
	$sheet->freezePaneByColumnAndRow(3,7);
?>
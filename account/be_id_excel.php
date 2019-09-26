<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_login.php');
	include_once("../inc/_myFun.php");
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $year.$month;

	if (!$orgNo || !$year || !$month){
		die('PARA ERROR');
		exit;
	}

	//기관명
	$sql = 'SELECT	m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_mkind = \'0\'';

	$orgNm = $conn->get_data($sql);

	//수급자구분
	$sql = 'SELECT	jumin
			,		DATE_FORMAT(from_dt,\'%Y%m%d\') AS from_dt
			,		DATE_FORMAT(to_dt,\'%Y%m%d\') AS to_dt
			,		CASE kind WHEN \'3\' THEN \'기초\'
							  WHEN \'2\' THEN \'의료\'
							  WHEN \'4\' THEN \'경감\'
							  WHEN \'1\' THEN \'일반\' ELSE \'-\' END AS kind
			FROM	client_his_kind
			WHERE	org_no = \''.$orgNo.'\'
			AND		\''.$yymm.'\' BETWEEN DATE_FORMAT(from_dt,\'%Y%m\') AND DATE_FORMAT(to_dt,\'%Y%m\')';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$tgtGbn[$row['jumin']][] = Array('fromDt'=>$row['from_dt'],'toDt'=>$row['to_dt'],'gbn'=>$row['kind']);
	}

	$conn->row_free();


	//인정등급
	$sql = 'SELECT	jumin, level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		\''.$yymm.'\' BETWEEN DATE_FORMAT(from_dt, \'%Y%m\') AND DATE_FORMAT(to_dt, \'%Y%m\')
			ORDER	BY jumin, from_dt
			';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		if (!is_numeric(StrPos($tgtLvl[$row['jumin']], $row['level'].'등급'))){
			$tgtLvl[$row['jumin']] .= ($tgtLvl[$row['jumin']] ? ', ' : '').$row['level'].'등급';
		}
	}

	$conn->row_free();

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($year."년 ".IntVal($month)."월 수입 지출 정산서").".xls" );


	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("대체전표");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setHorizontalCentered(true);
	//$sheet->getPageSetup()->setFitToPage(true);
	//$sheet->getPageSetup()->setFitToWidth(1);
	//$sheet->getPageSetup()->setFitToHeight(0);

	$sheet->getPageMargins()->setTop(0.5);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.5);

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim //Batang //맑은고딕


	//초기화
	$defFontSize = 8;
	$rowH = 15;
	$rowNo = 0;


	//스타일
	include_once("../excel/style.php");

	$sheet->getColumnDimension('A')->setWidth(15);
	$sheet->getColumnDimension('B')->setWidth(28);
	$sheet->getColumnDimension('C')->setWidth(15);
	$sheet->getColumnDimension('D')->setWidth(18);
	$sheet->getColumnDimension('E')->setWidth(15);
	$sheet->getColumnDimension('F')->setWidth(15);
	$sheet->getColumnDimension('G')->setWidth(15);
	$sheet->getColumnDimension('H')->setWidth(15);
	$sheet->getColumnDimension('I')->setWidth(15);
	$sheet->getColumnDimension('J')->setWidth(15);
	$sheet->getColumnDimension('K')->setWidth(15);
	$sheet->getColumnDimension('L')->setWidth(15);
	$sheet->getColumnDimension('M')->setWidth(15);
	$sheet->getColumnDimension('N')->setWidth(15);



	//폰트설정
	$fontSize = 18;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);


	$rH = $rowH * $fontSize / $defFontSize;


	//$rowNo ++;
	//$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	//$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'G'.$rowNo, 'val'=>"") );


	$fontSize = 10;
	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rH = $rowH * $fontSize / $defFontSize;

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"거래처코드") );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>"업체명") );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>"사업본부코드") );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"사업본부") );
	$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"부서코드") );
	$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"부서") );
	$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>"적요1(종류)") );
	$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>"적요2(이름)") );
	$sheet->SetData( Array('F'=>'I'.$rowNo, 'val'=>"적요3(날짜)") );
	$sheet->SetData( Array('F'=>'J'.$rowNo, 'val'=>"경감구분") );
	$sheet->SetData( Array('F'=>'K'.$rowNo, 'val'=>"인정등급") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'val'=>"금액") );
	$sheet->SetData( Array('F'=>'M'.$rowNo, 'val'=>"가산") );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'val'=>"합계금액") );


	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);


	$sql = 'SELECT	sub_cd, jumin, add_amt
			FROM	longterm_paydmd
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$addAmt[$row['sub_cd']][$row['jumin']] = $row['add_amt'];
	}

	$conn->row_free();


	$sql = 'SELECT	a.sub_cd, a.name, a.svc_amt, a.jumin, a.kind
			,		CASE a.sub_cd WHEN \'200\' THEN \'방문요양\' WHEN \'500\' THEN \'방문목욕\' WHEN \'800\' THEN \'방문간호\' ELSE a.sub_cd END AS sub_nm
			,		(SELECT MIN(t01_sugup_date) FROM t01iljung WHERE t01_ccode = \''.$orgNo.'\' AND t01_mkind = \'0\' AND t01_jumin = a.jumin AND t01_svc_subcode = a.sub_cd AND t01_sugup_date BETWEEN a.from_dt AND a.to_dt AND IFNULL(t01_bipay_umu,\'N\') != \'Y\' AND t01_del_yn = \'N\') AS date
			FROM	(
					SELECT	a.jumin, a.kind, a.sub_cd, m03_name AS name, a.svc_amt
					,		CASE WHEN b.from_dt < \''.$yymm.'01\' THEN \''.$yymm.'01\' ELSE b.from_dt END AS from_dt
					,		CASE WHEN b.to_dt > \''.$yymm.'31\' THEN \''.$yymm.'31\' ELSE b.to_dt END AS to_dt
					FROM	(
							SELECT	t13_jumin AS jumin, t13_bonin_yul AS kind, \'200\' AS sub_cd, t13_suga_tot1 AS svc_amt
							FROM	t13sugupja
							WHERE	t13_ccode	= \''.$orgNo.'\'
							AND		t13_mkind	= \'0\'
							AND		t13_pay_date= \''.$yymm.'\'
							AND		t13_type	= \'2\'
							AND		t13_suga_tot1 > 0
							UNION	ALL
							SELECT	t13_jumin, t13_bonin_yul, \'500\', t13_suga_tot2
							FROM	t13sugupja
							WHERE	t13_ccode	= \''.$orgNo.'\'
							AND		t13_mkind	= \'0\'
							AND		t13_pay_date= \''.$yymm.'\'
							AND		t13_type	= \'2\'
							AND		t13_suga_tot2 > 0
							UNION	ALL
							SELECT	t13_jumin, t13_bonin_yul, \'800\', t13_suga_tot3
							FROM	t13sugupja
							WHERE	t13_ccode	= \''.$orgNo.'\'
							AND		t13_mkind	= \'0\'
							AND		t13_pay_date= \''.$yymm.'\'
							AND		t13_type	= \'2\'
							AND		t13_suga_tot3 > 0
							) AS a
					INNER	JOIN	m03sugupja
							ON		m03_ccode = \''.$orgNo.'\'
							AND		m03_mkind = \'0\'
							AND		m03_jumin = a.jumin
					INNER	JOIN	(
									SELECT	a.jumin, REPLACE(a.from_dt,\'-\',\'\') AS from_dt, REPLACE(a.to_dt,\'-\',\'\') AS to_dt
									,		CONCAT(REPLACE(a.rate,\'.\',\'\'),\'_\',a.seq - (SELECT MIN(seq) FROM client_his_kind WHERE org_no = a.org_no AND jumin = a.jumin AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\' AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\') + 1) AS kind
									FROM	client_his_kind AS a
									WHERE	a.org_no = \''.$orgNo.'\'
									AND		DATE_FORMAT(a.from_dt,\'%Y%m\') <= \''.$yymm.'\'
									AND		DATE_FORMAT(a.to_dt,\'%Y%m\') >= \''.$yymm.'\'
									) AS b
									ON		b.jumin = a.jumin
									AND		b.kind = a.kind
							) AS a
			ORDER	BY name, jumin, sub_cd, date, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);

		if ($orgNo == 'dolvoin01'){
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"0003-000") );
		}else{
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>"1000-000") );
		}
		$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$orgNm) );
		$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>"04", 'format'=>'00') ); //18
		$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>"마케팅1본부") ); //마케팅전략사업부
		$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>"9110") ); //9074
		$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>"시니어사업팀") );
		$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$row['sub_nm']) );
		$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$row['name']) );
		$sheet->SetData( Array('F'=>'I'.$rowNo, 'val'=>$row['date']) );
		$sheet->SetData( Array('F'=>'K'.$rowNo, 'val'=>$tgtLvl[$row['jumin']]) );
		$sheet->SetData( Array('F'=>'L'.$rowNo, 'val'=>$row['svc_amt'], 'H'=>'R', 'format'=>'#,##0') );
		$sheet->SetData( Array('F'=>'M'.$rowNo, 'val'=>$addAmt[$row['sub_cd']][$row['jumin']], 'H'=>'R', 'format'=>'#,##0') );
		$sheet->SetData( Array('F'=>'N'.$rowNo, 'val'=>'=L'.$rowNo.'+'.'M'.$rowNo, 'H'=>'R', 'format'=>'#,##0') );

		if (is_array($tgtGbn[$row['jumin']])){
			foreach($tgtGbn[$row['jumin']] as $tmpIdx => $R){
				if ($R['fromDt'] <= $row['date'] && $R['toDt'] >= $row['date']){
					$sheet->SetData( Array('F'=>'J'.$rowNo, 'val'=>$R['gbn']) );
				}
			}
		}

		$addAmt[$row['sub_cd']][$row['jumin']] = 0;
	}

	$conn->row_free();

	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'K'.$rowNo, 'val'=>"합계") );
	$sheet->SetData( Array('F'=>'L'.$rowNo, 'val'=>'=SUM(L2:L'.($rowNo-1).')', 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'M'.$rowNo, 'val'=>'=SUM(M2:M'.($rowNo-1).')', 'H'=>'R', 'format'=>'#,##0') );
	$sheet->SetData( Array('F'=>'N'.$rowNo, 'val'=>'=SUM(N2:N'.($rowNo-1).')', 'H'=>'R', 'format'=>'#,##0') );


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	Unset($data);
	unset($tgtGbn);
	unset($tgtLvl);

	include_once("../inc/_db_close.php");
?>
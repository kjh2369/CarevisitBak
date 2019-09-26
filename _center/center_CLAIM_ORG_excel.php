<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	require_once("../excel/PHPExcel.php");

	$company= $_POST['company'];
	$year	= $_POST['year'];

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr("기관별청구내역").".xls" );

	// Create new PHPExcel object

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle("기관별청구내역");
	//$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$sheet->getPageMargins()->setTop(0.8);
	$sheet->getPageMargins()->setRight(0.5);
	$sheet->getPageMargins()->setLeft(0.5);
	$sheet->getPageMargins()->setBottom(0.8);
	$sheet->getPageSetup()->setHorizontalCentered(true);

	//스타일
	include("../excel/style.php");
	//include("../excel/init.php");


	/*
	$rowNo ++;
		$sheet->getRowDimension($rowNo)->setRowHeight($rH);
		$sheet->SetData( Array('F'=>'A'.$rowNo, 'H'=>'R', 'val'=>"1. ", 'border'=>'RNBN') );
		$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'AF'.$rowNo, 'H'=>'L', 'val'=>"노인장기요양보험법의 제 규정에 따라 사용자와 장기요양기관(사업소) 쌍방은 다음과 같이 복지용구 공급계약을 체결합니다.", 'border'=>'LNBN') );
	 */


	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(17);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$defFontSzie = 10;
	$rowH = 15;
	$rowNo = 0;


	$fontSize = 9;
	$rH = $rowH * $fontSize / $defFontSzie;


	$objPHPExcel->getDefaultStyle()->getFont()->setSize($fontSize);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);


	//타이틀
	$rowNo ++;
	$sheet->getRowDimension($rowNo)->setRowHeight($rH);
	$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo+2), 'val'=>"No", 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'B'.$rowNo, 'T'=>'B'.($rowNo+2), 'val'=>"기관명", 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'C'.$rowNo, 'T'=>'C'.($rowNo+2), 'val'=>"기관기호", 'backcolor'=>'EAEAEA') );
	$sheet->SetData( Array('F'=>'D'.$rowNo, 'T'=>'D'.($rowNo+2), 'val'=>"대표자명", 'backcolor'=>'EAEAEA') );

	$CellF = 'E';
	for($i=1; $i<=12; $i++){
		$CellT = GetNextCellId($CellF,7);
		$sheet->SetData( Array('F'=>$CellF.$rowNo, 'T'=>$CellT.$rowNo, 'val'=>$i."월", 'backcolor'=>'EAEAEA') );
		$CellF = GetNextCellId($CellT);
	}


	$tmpArr = Array(0=>Array('청구내역',2),1=>Array('입금내역',2),2=>Array('세금계산서',1));
	$rowNo ++;

	$CellF = 'E';
	for($i=1; $i<=12; $i++){
		for($j=0; $j<SizeOf($tmpArr); $j++){
			$CellT = GetNextCellId($CellF,$tmpArr[$j][1]);
			$sheet->SetData( Array('F'=>$CellF.$rowNo, 'T'=>$CellT.$rowNo, 'val'=>$tmpArr[$j][0], 'backcolor'=>'EAEAEA') );
			$CellF = GetNextCellId($CellT);
		}
	}

	$tmpArr = Array('합계','당월분','미납분','일자','구분','금액','발행일자','구분');
	$rowNo ++;

	$CellF = 'E';
	for($i=1; $i<=12; $i++){
		for($j=0; $j<SizeOf($tmpArr); $j++){
			$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$tmpArr[$j], 'backcolor'=>'EAEAEA') );
			$CellF = GetNextCellId($CellF);
		}
	}


	//데이타
	$sql = 'SELECT	a.org_no, a.org_nm, a.mg_nm
			FROM	(
					SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm
					FROM	m00center
					INNER	JOIN	cv_svc_acct_list AS b
							ON		b.org_no	= m00_mcode
					WHERE	m00_domain = \''.$company.'\'
					) AS a
			GROUP	BY a.org_no
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo	= $row['org_no'];
		$data[$orgNo] = Array('orgNm'=>$row['org_nm'], 'mgNm'=>$row['mg_nm']);
	}

	$conn->row_free();


	if (is_array($data)){
		$taxCrGbn = Array('C'=>'청구', 'R'=>'영수');

		foreach($data as $orgNo => $R){
			//당월 청구금액
			$sql = 'SELECT	CAST(MID(acct_ym,5) AS unsigned) AS month, SUM(acct_amt) AS amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		LEFT(acct_ym, 4) = \''.$year.'\'
					GROUP	BY acct_ym';

			$data[$orgNo]['acctAmt'] = $conn->_fetch_array($sql,'month');


			//전월까지 미납분
			for($i=1; $i<=12; $i++){
				$yymm = $year.($i < 10 ? '0' : '').$i;

				$sql = 'SELECT	SUM(acct_amt) AS acct_amt
						FROM	cv_svc_acct_list
						WHERE	org_no	= \''.$orgNo.'\'
						AND		acct_ym < \''.$yymm.'\'';

				$data[$orgNo]['unpaid'][$i] = $conn->get_data($sql);


				//입금금액
				$sql = 'SELECT	SUM(CASE WHEN acct_ym = \''.$yymm.'\' THEN link_amt ELSE 0 END) AS now_amt
						,		SUM(CASE WHEN acct_ym < \''.$yymm.'\' THEN link_amt ELSE 0 END) AS old_amt
						,		MIN(cms_dt) AS cms_dt
						,		MIN(bank_dt) AS bank_dt
						FROM	cv_cms_link AS a
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		acct_ym <= \''.$yymm.'\'
						AND		del_flag = \'N\'
						AND		IFNULL(link_stat,\'1\') = \'1\'';

				$row = $conn->get_array($sql);

				$data[$orgNo]['DPT'][$i]['amt'] = $row['now_amt']; //당월 입금액

				if ($data[$orgNo]['DPT'][$i]['amt'] > 0){
					$data[$orgNo]['DPT'][$i]['dt']	= ($row['cms_dt'] ? $row['cms_dt'] : $row['bank_dt']); //입금일자
					$data[$orgNo]['DPT'][$i]['gbn']	= ($row['cms_dt'] ? 'CMS' : '무통장'); //입금구분
				}

				$data[$orgNo]['oldDpt'][$i]	 = $row['old_amt']; //전월까지 입금액
				$data[$orgNo]['unpaid'][$i]	-= $data[$orgNo]['oldDpt'][$i]; //전월까지 미납금액
				$data[$orgNo]['nonPay'][$i]	 = $R['acctAmt'] - $data[$orgNo]['oldDpt'][$i];
			}


			//세금계산서 발행이력
			$sql = 'SELECT	CAST(MID(acct_ym,5) AS unsigned) AS month, iss_dt AS dt, cr_gbn AS gbn
					FROM	cv_tax_his
					WHERE	org_no	= \''.$orgNo.'\'
					AND		LEFT(acct_ym, 4)= \''.$year.'\'';

			$data[$orgNo]['TAX'] = $conn->_fetch_array($sql, 'month');
		}



		$no = 1;

		foreach($data as $orgNo => $R){
			$rowNo ++;
			$sheet->getRowDimension($rowNo)->setRowHeight($rH);
			$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>$no) );
			$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$R['orgNm'], 'H'=>'L') );
			$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$orgNo, 'H'=>'L') );
			$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$R['mgNm']) );

			$CellF = 'E';
			for($i=1; $i<=12; $i++){
				//합계
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$R['acctAmt'][$i]['amt']+$R['unpaid'][$i], 'H'=>'R', 'format'=>'#,##0') );
				$CellF = GetNextCellId($CellF);

				//당월분
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$R['acctAmt'][$i]['amt'], 'H'=>'R', 'format'=>'#,##0') );
				$CellF = GetNextCellId($CellF);

				//미납분
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$R['unpaid'][$i], 'H'=>'R', 'format'=>'#,##0') );
				$CellF = GetNextCellId($CellF);

				//입금일자
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$myF->dateStyle($R['DPT'][$i]['dt'],'.')) );
				$CellF = GetNextCellId($CellF);

				//입금구분
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$R['DPT'][$i]['gbn']) );
				$CellF = GetNextCellId($CellF);

				//입금금액
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$R['DPT'][$i]['amt'], 'H'=>'R', 'format'=>'#,##0') );
				$CellF = GetNextCellId($CellF);

				//세금계산서 발행일자
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$myF->dateStyle($R['TAX'][$i]['dt'],'.')) );
				$CellF = GetNextCellId($CellF);

				//세금계산서 구분
				$sheet->SetData( Array('F'=>$CellF.$rowNo, 'val'=>$taxCrGbn[$R['TAX'][$i]['gbn']]) );
				$CellF = GetNextCellId($CellF);
			}

			$no ++;
		}
	}


	$sheet->freezePane('E4');

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>
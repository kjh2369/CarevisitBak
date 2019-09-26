<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	//@ini_set('memory_limit', '512M');
	//define('WP_MAX_MEMORY_LIMIT', '512M');

	$f = $_FILES['filename'];
	$file = $f['tmp_name'];
	$acctType = $_POST['acctType'];
	$deleteFlag = $_POST['deleteFlag'];
	$year = $_GET['yeawr'];
	$month = $_GET['month'];
	$yymm = $year.($month < 10 ? '0' : '').$month;
	$useYm = $myF->dateAdd('month', -1, $yymm.'01', 'Ym');

	/*
	if ($f['tmp_name'] != ''){
		if (move_uploaded_file($f['tmp_name'], $file)){
			// 업로드 성공
			$result = 1;
		}else{
			// 업로드 실패
			$result = 7;
		}
	}else{
		// 업로드 실패
		$result = 9;
	}
	*/

	error_reporting(E_ALL ^ E_NOTICE);

	include_once('../excel/PHPExcel.php');           //라이브러리 업로드 경로
	include_once('../excel/PHPExcel/IOFactory.php'); //라이브러리 업로드 경로

	@$lo_reader = PHPExcel_IOFactory::createReaderForFile($file);//읽기객체생성
	@$lo_reader->setReadDataOnly(true);							//읽기전용설정
	@$lo_excel = $lo_reader->load($file);						//읽기
	@$lo_excel->setActiveSheetIndex(0);							//시트선택
	@$lo_sheet = $lo_excel->getActiveSheet();					//시트활성화
	@$lo_rowIterator = $lo_sheet->getRowIterator();				//모든 행

	foreach($lo_rowIterator as $row){
		@$lo_cell = $row->getCellIterator();
		@$lo_cell->setIterateOnlyExistingCells(false); //해당 행의 모든 열

		foreach($lo_cell as $cell){
			//echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().'<br>';
			$rowNo = $cell->getRow();
			$column = $cell->getColumn();
			$val = $cell->getValue();

			if ($rowNo <= 1) continue;

			/*
				청구내역
				A 기관기호
				B 청구금액
				C 미납금액

				미납내역
				A 기관기호
				B 년월
				C 미납금액
			 */

			if ($acctType == '1'){ //청구내역
				switch($column){
					case 'A':
						$orgNo = $val;
						break;

					case 'B':
						$data[$orgNo]['acctPay'] = $val;
						break;

					case 'C':
						$data[$orgNo]['dftPay'] = $val;
						break;
				}
			}else if ($acctType == '2'){ //미납내역
				switch($column){
					case 'A':
						$orgNo = $val;
						break;

					case 'B':
						$data[$orgNo]['yymm'] = $val;
						break;

					case 'C':
						$data[$orgNo]['dftPay'] = $val;
						break;
				}
			}else{
				continue;
			}
		}
	}

	@unlink($file);

	if ($deleteFlag == 'Y'){
		if ($acctType == '1'){
			$sql = 'DELETE
					FROM	cv_svc_acct_amt
					WHERE	yymm = \''.$useYm.'\'
					';
			$query[] = $sql;
		}else if ($acctType == '2'){
		}
	}

	if (is_array($data)){
		foreach($data as $orgNo => $R){
			if ($acctType == '1'){
				$sql = 'REPLACE INTO cv_svc_acct_amt VALUES (
						 \''.$orgNo.'\'
						,\''.$useYm.'\'
						,\''.$R['acctPay'].'\'
						,\''.$R['dftPay'].'\'
						,\'0\'
						)';
				$query[] = $sql;
			}else if ($acctType == '2'){
				$sql = 'REPLACE INTO tmp_dft_amt VALUES (
						 \''.$orgNo.'\'
						,\''.$R['yymm'].'\'
						,\''.$R['dftPay'].'\'
						)';
				$query[] = $sql;
			}
		}
	}

	include_once('../inc/_db_close.php');
?>
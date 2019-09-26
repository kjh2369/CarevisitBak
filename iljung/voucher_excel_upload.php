<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$f = $_FILES['filename'];
	$file = '../tempFile/'.mktime();

	echo $file;
	exit;

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

	if ($result == 1){
		error_reporting(E_ALL ^ E_NOTICE);

		include_once('../excel/PHPExcel.php');           //라이브러리 업로드 경로
		include_once('../excel/PHPExcel/IOFactory.php'); //라이브러리 업로드 경로

		$lo_reader = PHPExcel_IOFactory::createReaderForFile($file);//읽기객체생성
		$lo_reader->setReadDataOnly(true);							//읽기전용설정
		$lo_excel = $lo_reader->load($file);						//읽기
		$lo_excel->setActiveSheetIndex(0);							//시트선택
		$lo_sheet = $lo_excel->getActiveSheet();					//시트활성화
		$lo_rowIterator = $lo_sheet->getRowIterator();				//모든 행

		$rowId = 0;  //데이타 인덱스
		$yymm  = ''; //년월

		foreach($lo_rowIterator as $row){
			$lo_cell = $row->getCellIterator();
			$lo_cell->setIterateOnlyExistingCells(false); //해당 행의 모든 열

			foreach($lo_cell as $cell){
				echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().' | ';
				echo '<br>';
			}
		}
	}

	@unlink($file);

	echo $result;

	include_once('../inc/_db_close.php');
?>
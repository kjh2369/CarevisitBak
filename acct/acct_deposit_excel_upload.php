<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$f    = $_FILES['filename'];
	$file = '../tempFile/'.Date('YmdHis');

	if ($f['tmp_name'] != ''){
		if (Move_Uploaded_File($f['tmp_name'], $file)){
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
		//기관별 CMS번호
		$sql = 'SELECT DISTINCT
				       b02_center AS code
				,      cms_cd AS cms
				  FROM b02center
				 WHERE IFNULL(cms_cd,\'\') != \'\'
				 ORDER BY cms';
		$cms = $conn->_fetch_array($sql, 'cms');

		error_reporting(E_ALL ^ E_NOTICE);

		include_once('../excel/PHPExcel.php');           //라이브러리 업로드 경로
		include_once('../excel/PHPExcel/IOFactory.php'); //라이브러리 업로드 경로

		$lo_reader = PHPExcel_IOFactory::createReaderForFile($file);//읽기객체생성
		$lo_reader->setReadDataOnly(true);							//읽기전용설정
		$lo_excel = $lo_reader->load($file);						//읽기
		$lo_excel->setActiveSheetIndex(0);							//시트선택
		$lo_sheet = $lo_excel->getActiveSheet();					//시트활성화
		$lo_rowIterator = $lo_sheet->getRowIterator();				//모든 행

		$conn->begin();

		foreach($lo_rowIterator as $row){
			$lo_cell = $row->getCellIterator();
			$lo_cell->setIterateOnlyExistingCells(false); //해당 행의 모든 열

			UnSet($val);

			foreach($lo_cell as $cell){
				/*********************************************************
					Column 인덱스

					A : 예정일
					B : 출금일
					C : 회원번호(CMS번호)
					D : 회원명
					E : 출금상태
					F : 출금금액
					G : 비고
					H : 은행명
					I : 계좌번호
					J : 예금주명
					K : 예금주주민번호
					L : 회원구분

					A : No
					B : 회원번호(CMS번호)
					C : 회원명
					D : 결제일
					E : 결제금액
					F : 결제상태
					G : 결제구분
					H : 결제번호
					I : 비고

				*********************************************************/

				if ($cell->getColumn() == 'A'){
				}else if ($cell->getColumn() == 'B'){
					//$val['date'] = $myF->dateStyle($cell->getValue());
					$val['cms'] = $cell->getValue();
				}else if ($cell->getColumn() == 'C'){
					//$val['cms'] = $cell->getValue();
				}else if ($cell->getColumn() == 'D'){
					$val['date'] = $myF->dateStyle($cell->getValue());
				}else if ($cell->getColumn() == 'E'){
					$val['amt'] = $cell->getValue();
				}else if ($cell->getColumn() == 'F'){
					//$val['amt'] = $cell->getValue();
					$val['stat'] = $cell->getValue();
				}else if ($cell->getColumn() == 'G'){
				}else if ($cell->getColumn() == 'H'){
				}else if ($cell->getColumn() == 'I'){
				}else if ($cell->getColumn() == 'J'){
				}else if ($cell->getColumn() == 'K'){
				}else if ($cell->getColumn() == 'L'){
				}

				#echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue();
				#echo '<br>---------------------------------------------------------------------------------------<br>';
			}

			if ($val['stat'] != '출금성공'){
				$val['amt']   = 0;
			}

			if (IntVal($val['amt']) > 0){
				if (Empty($cms[$val['cms']]['code'])){
					$cms[$val['cms']]['code'] = $val['cms'];
				}

				$sql = 'INSERT INTO center_deposit_'.$gDomainID.' (
						 org_no
						,reg_dt
						,cms_cd
						,amt
						,type
						,other
						,insert_dt) VALUES (
						 \''.$cms[$val['cms']]['code'].'\'
						,\''.$val['date'].'\'
						,\''.$val['cms'].'\'
						,\''.$val['amt'].'\'
						,\'1\'
						,\'\'
						,NOW())';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}
		}

		$conn->commit();
	}

	UnLink($file);

	echo $result;

	include_once('../inc/_db_close.php');
?>
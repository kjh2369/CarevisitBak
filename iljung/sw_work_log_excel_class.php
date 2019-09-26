<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	m03_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$key = $conn->get_data($sql);

	//수급자명 및 주소
	$sql = 'SELECT	m03_name AS name
			,		m03_juso1 AS addr
			,		m03_juso2 AS addr_dtl
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'0\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$name = $row['name'];
	$addr = $row['addr'].' '.$row['addr_dtl'];


	//등급 및 인정번호
	$sql = 'SELECT	app_no
			,		level
			FROM	client_his_lvl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \'0\'
			AND		jumin	= \''.$jumin.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'
			ORDER	BY from_dt DESC, to_dt DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	$appNo = $row['app_no'];
	$lvl = $row['level'];
	
	/*
	if ($lvl >= '1' && $lvl <= '5'){
		$lvl .= '등급';
	}else{
		$lvl = '';
	}
	*/

	$lvl = $myF->_lvlNm($lvl);

	Unset($row);

	//일지
	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	if (is_numeric($row['sign_manager'])){
		$signManagerFile = '../sign/sign/manager/'.$orgNo.'/'.$row['sign_manager'].'.jpg';

		$sql = 'SELECT	DISTINCT m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode	= \''.$orgNo.'\'
				AND		m02_key		= \''.$row['sign_manager'].'\'';

		$row['sign_manager'] = $conn->get_data($sql);
	}else{
		$signManagerFile = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-4.jpg';
	}

	//요양보호사
	$sql = 'SELECT	t01_mem_cd1 AS mem_cd1
			,		t01_mem_nm1 AS mem_nm1
			,		t01_mem_cd2 AS mem_cd2
			,		t01_mem_nm2 AS mem_nm2
			,		t01_status_gbn AS stat
			,		t01_sugup_fmtime AS plan_from
			,		t01_sugup_totime AS plan_to
			,		t01_wrk_fmtime AS work_from
			,		t01_wrk_totime AS work_to
			,		t01_conf_fmtime AS conf_from
			,		t01_conf_totime AS conf_to
			,	    t01_toge_umu as toge_yn
			,		t01_sugup_soyotime as soyotime
			FROM	t01iljung
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \'0\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$row['date'].'\'
			AND		t01_del_yn = \'N\'';
	$memRow = $conn->_fetch_array($sql);

	$rCnt = SizeOf($memRow);

	for($i=0; $i<$rCnt; $i++){
		$r = $memRow[$i];

		if ($r['stat'] == '1'){
			$stat = '완료';
			$from = $r['conf_from'];
			$to = $r['conf_to'];
		}else if ($r['stat'] == '5'){
			$stat = '진행중';
			$from = $r['work_from'];
			$to = '9999';
		}else{
			$stat = '대기';
			$from = $r['plan_from'];
			$to = $r['plan_to'];
		}

		//5등급 인지활동 가족케어일 경우 시간 60분
		if($r['mem_nm2']!='' && $r['toge_yn']=='Y'){
			$soyoTime = $myF->time2min($to) - $myF->time2min($from);

			if($soyoTime==60){
				$to = $myF->min2time($myF->time2min($to) + 60);
			}else {
				$to = $myF->min2time($myF->time2min($to) + 30);
			}
		}

		if (($row['time'] >= $from && $row['time'] <= $to) || ($row['to_time'] >= $from && $row['to_time'] <= $to)){
			$idx = SizeOf($memList);
			$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd1']),'name'=>$r['mem_nm1'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));

			if ($r['mem_nm2']){
				$idx = SizeOf($memList);
				$memList[$idx] = Array('jumin'=>$ed->en($r['mem_cd2']),'name'=>$r['mem_nm2'],'stat'=>$stat,'from'=>$myF->timeStyle($from),'to'=>$myF->timeStyle($to));
			}
		}
	}

	if (is_array($memList)){
		$memList = $myF->sortArray($memList, 'name', 1);

		foreach($memList as $idx => $mem){
			$memStr .= ($mem['name']."[".$mem['stat']."/".$mem['from']."~".$mem['to']."]   ");
		}
	}

	Unset($memList);

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("업무일지").".xls" );
	header( "Content-Description: ".$myF->euckr("업무일지"));
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$rowH = 15;

	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle('업무수행일지');
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
	include("../excel/init.php");

	//기본설정
	$objPHPExcel->getDefaultStyle()->getFont()->setName("Batang"); //Gulim
	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);

	$sheet->getRowDimension(1)->setRowHeight(12);
	$sheet->getCell('A1')->setValue('[별지 제24호 서식]');
	$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A1:AF1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('A1:AF1');


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(14);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	//타이틀
	$sheet->getRowDimension(2)->setRowHeight(15);
	if($yymm < '201701'){
		$sheet->getCell('A2')->setValue("프로그램 관리자 및 방문요양기관\n사회복지사 업무수행 일지");
	}else {
		$sheet->getCell('A2')->setValue("프로그램 관리자 및 \n사회복지사 업무수행 일지");
	}
	$sheet->getStyle('A2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('A2:Q4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('A2:Q4');
	$sheet->getStyle('A2:Q4')->applyFromArray(fnStyle());

	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$sheet->getCell('R2')->setValue('확인');
	$sheet->getStyle('R2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('R2:T4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('R2:T4');
	$sheet->getStyle('R2:T4')->applyFromArray(fnStyle());

	$sheet->getCell('U2')->setValue('방문자');
	$sheet->getStyle('U2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('U2:X2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('U2:X2');
	$sheet->getStyle('U2:X2')->applyFromArray(fnStyle());

	$sheet->getCell('Y2')->setValue('요양보호사');
	$sheet->getStyle('Y2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('Y2:AB2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('Y2:AB2');
	$sheet->getStyle('Y2:AB2')->applyFromArray(fnStyle());

	$sheet->getCell('AC2')->setValue('관리책임자');
	$sheet->getStyle('AC2')->getAlignment()->setWrapText(true);
	$sheet->getStyle('AC2:AF2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('AC2:AF2');
	$sheet->getStyle('AC2:AF2')->applyFromArray(fnStyle());

	$sheet->getRowDimension(3)->setRowHeight(15);
	$sheet->getCell('U3')->setValue($row['sign_visit']); //방문자
	$sheet->getStyle('U3')->getAlignment()->setWrapText(true);
	$sheet->getStyle('U3:X3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('U3:X3');
	$sheet->getStyle('U3:X3')->applyFromArray(fnStyle('BN'));

	$sheet->getCell('Y3')->setValue($row['sign_yoy']); //요양보호사
	$sheet->getStyle('Y3')->getAlignment()->setWrapText(true);
	$sheet->getStyle('Y3:AB3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('Y3:AB3');
	$sheet->getStyle('Y3:AB3')->applyFromArray(fnStyle('BN'));

	$sheet->getCell('AC3')->setValue($row['sign_manager']); //관리책임자
	$sheet->getStyle('AC3')->getAlignment()->setWrapText(true);
	$sheet->getStyle('AC3:AF3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells('AC3:AF3');
	$sheet->getStyle('AC3:AF3')->applyFromArray(fnStyle('BN'));

	$sheet->getRowDimension(4)->setRowHeight(30);
	$sheet->getCell('U4')->setValue('');
	$sheet->getStyle('U4')->getAlignment()->setWrapText(true);
	$sheet->getStyle('U4:X4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('U4:X4');
	$sheet->getStyle('U4:X4')->applyFromArray(fnStyle('TN'));

	$sheet->getCell('Y4')->setValue('');
	$sheet->getStyle('Y4')->getAlignment()->setWrapText(true);
	$sheet->getStyle('Y4:AB4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('Y4:AB4');
	$sheet->getStyle('Y4:AB4')->applyFromArray(fnStyle('TN'));

	$sheet->getCell('AC4')->setValue('');
	$sheet->getStyle('AC4')->getAlignment()->setWrapText(true);
	$sheet->getStyle('AC4:AF4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells('AC4:AF4');
	$sheet->getStyle('AC4:AF4')->applyFromArray(fnStyle('TN'));

	$rowNo = 5;
	$cellF = 'A';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight(5);
	$sheet->getCell($cellF.$rowNo)->setValue('');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);


	//서명 - 수급자 및 보호자
	$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-1.jpg';
	if (is_file($fileImg)){
		SetSign($sheet, $fileImg, 'AA7');
	}else{
		$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-1.jpg';

		if (is_file($fileImg)){
			SetSign($sheet, $fileImg, 'AA7');
		}
	}

	//서명 - 요양보호사
	$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-2.jpg';
	if (is_file($fileImg)){
		SetSign($sheet, $fileImg, 'Y4');
	}else{
		$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-2.jpg';

		if (is_file($fileImg)){
			SetSign($sheet, $fileImg, 'Y4');
		}
	}

	//서명 - 방문자
	$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-3.jpg';
	if (is_file($fileImg)){
		SetSign($sheet, $fileImg, 'U4');
	}else{
		$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/_7-3.jpg';

		if (is_file($fileImg)){
			SetSign($sheet, $fileImg, 'U4');
		}
	}

	//서명 - 관리자
	//$fileImg = '../sign/sw_log/'.$orgNo.'/'.$yymm.'/'.$key.'/'.$seq.'_7-4.jpg';
	$fileImg = $signManagerFile;
	if (is_file($fileImg)){
		SetSign($sheet, $fileImg, 'AC4');
	}

	function SetSign($sheet, $fileImg, $coord){
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('IMAGE');
		$objDrawing->setDescription('IMAGE');
		$objDrawing->setPath($fileImg);	// filesystem reference for the image file //'./images/phpexcel_logo.gif'

		if ($objDrawing->getWidth() > 65){
			$objDrawing->setWidth(65);
		}

		if ($objDrawing->getHeight() > 35){
			$objDrawing->setHeight(35);
		}

		$objDrawing->setCoordinates($coord);	// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX(2);	// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		$objDrawing->setOffsetY(2);
		$objDrawing->setWorksheet($sheet);
	}


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);

	$rowNo = 6;
	$cellF = 'A';
	$cellT = 'D';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('수급자');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'E';
	$cellT = 'J';
	$sheet->getCell($cellF.$rowNo)->setValue('장기요양등급');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'K';
	$cellT = 'P';
	$sheet->getCell($cellF.$rowNo)->setValue('장기요양인정번호');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'Q';
	$cellT = 'W';
	$sheet->getCell($cellF.$rowNo)->setValue('방문일시');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'X';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('수급자(보호자) 서명');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$rowNo = 7;
	$cellF = 'A';
	$cellT = 'D';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue($name); //수급자명
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'E';
	$cellT = 'J';
	$sheet->getCell($cellF.$rowNo)->setValue($lvl); //장기요양등급
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'K';
	$cellT = 'P';
	$sheet->getCell($cellF.$rowNo)->setValue($appNo); //장기요양인정번호
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'Q';
	$cellT = 'W';
	$sheet->getCell($cellF.$rowNo)->setValue($myF->dateStyle($row['date'],'.').' '.$myF->timeStyle($row['time']).'~'.$myF->timeStyle($row['to_time'])); //방문일시
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'X';
	$cellT = 'Z';
	$sheet->getCell($cellF.$rowNo)->setValue($row['sign_target']); //수급자(보호자) 서명
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle('RN'));

	$cellF = 'AA';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(''); //수급자(보호자) 서명
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle('LN'));

	$rowNo = 8;
	$cellF = 'A';
	$cellT = 'W';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue($addr); //주소
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());



	$rowNo = 9;
	$cellF = 'A';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight(5);
	$sheet->getCell($cellF.$rowNo)->setValue('');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);



	//욕구사정
	$rowNo = 10;
	$cellF = 'A';
	$cellT = 'C';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('욕구사정');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+14))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+14));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+14))->applyFromArray(fnStyle());
	//신체상태
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getCell($cellF.$rowNo)->setValue('①신체상태');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(($row['body_stat'] == '1' ? '▣' : '□').'완전자립 '.($row['body_stat'] == '2' ? '▣' : '□').'부분자립 '.($row['body_stat'] == '3' ? '▣' : '□').'전적인 도움'); //

	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BD'));

	$rowNo = 11;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['body_stat_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['body_stat_note']))));

	//질병
	$rowNo = 12;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('②질병');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+2));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+2))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'Q';
	$sheet->getCell($cellF.$rowNo)->setValue('질병명:'.$row['disease'].($row['medication'] == 'Y' ? '(약복용)' : '')); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNRN'));

	$cellF = 'R';
	$cellT = 'X';
	$sheet->getCell($cellF.$rowNo)->setValue('진단명:'.$row['diagnosis']); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNLNRN'));

	$cellF = 'Y';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('장애명:'.$row['disabled'].($row['disabled'] ? '('.$row['disabled_lvl'].'등급)' : '')); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNLN'));

	$rowNo = 13;
	$cellF = 'J';
	$cellT = 'U';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('시력 : '.($row['eyesight'] == '1' ? '▣' : '□').'양호 '.($row['eyesight'] == '2' ? '▣' : '□').'보통 '.($row['eyesight'] == '3' ? '▣' : '□').'나쁨'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNRN'));

	$cellF = 'V';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('청력 : '.($row['hearing'] == '1' ? '▣' : '□').'양호 '.($row['hearing'] == '2' ? '▣' : '□').'보통 '.($row['hearing'] == '3' ? '▣' : '□').'나쁨'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNLN'));

	$rowNo = 14;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['disease_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['disease_note']))));


	//인지상태
	$rowNo = 15;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('③인지상태');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'U';
	$sheet->getCell($cellF.$rowNo)->setValue('인지,기억력 : '.($row['memory'] == '1' ? '▣' : '□').'명확 '.($row['memory'] == '2' ? '▣' : '□').'부분도움 '.($row['memory'] == '3' ? '▣' : '□').'불가능'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNRN'));

	$cellF = 'V';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('표현력 : '.($row['express'] == '1' ? '▣' : '□').'명확 '.($row['express'] == '2' ? '▣' : '□').'부분도움 '.($row['express'] == '3' ? '▣' : '□').'불가능'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNLN'));

	$rowNo = 16;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['memory_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['memory_note']))));

	//의사소통
	$rowNo = 17;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('④의사소통');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'W';
	$sheet->getCell($cellF.$rowNo)->setValue('정서적상태 : '.($row['feel_stat'] == '1' ? '▣' : '□').'활발/적극 '.($row['feel_stat'] == '2' ? '▣' : '□').'조용/내성 '.($row['feel_stat'] == '3' ? '▣' : '□').'흥분/우울'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNRN'));

	$cellF = 'X';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('기타 : '.Trim(StripSlashes($row['comm_other']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTNLN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['comm_other']))));

	$rowNo = 18;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['comm_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['comm_note']))));

	//영양상태
	$rowNo = 19;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('⑤영양상태');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+3));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('식사형태 : '.($row['meal_type'] == '1' ? '▣' : '□').'일반식 '.($row['meal_type'] == '2' ? '▣' : '□').'당뇨식 '.($row['meal_type'] == '3' ? '▣' : '□').'죽 '.($row['meal_type'] == '4' ? '▣' : '□').'경관급식'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNTN'));

	$rowNo = 20;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('수분섭취 : '.($row['water_type'] == '1' ? '▣' : '□').'1일 5컵이상 '.($row['water_type'] == '2' ? '▣' : '□').'1일 2~4컵 '.($row['water_type'] == '3' ? '▣' : '□').'1일 1~2컵 '.($row['water_type'] == '4' ? '▣' : '□').'1일 1컵 '.($row['water_type'] == '9' ? '▣' : '□').'거의 드시지 않음'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNTN'));

	$rowNo = 21;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('섭취패턴 : '.($row['intake_type'] == '1' ? '▣' : '□').'3식을 규칙적으로 먹는다. '.($row['intake_type'] == '2' ? '▣' : '□').'평균 2식을 먹는다. '.($row['intake_type'] == '3' ? '▣' : '□').'1식만 먹는다.'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDTN'));

	$rowNo = 22;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['nutrition_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['nutrition_note']))));

	//가족 및 환경
	$rowNo = 23;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('⑥가족 및 환경');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['env_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['env_note']))));

	//종합
	$rowNo = 24;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('종 합');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['total_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['total_note']))));


	//급여제공계획
	$rowNo = 25;
	$cellF = 'A';
	$cellT = 'C';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue("급여제공\n계획");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+2));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+2))->applyFromArray(fnStyle());

	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('급여목표');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['target_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['target_note']))));

	$rowNo = 26;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('필요 급여내용');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['cont_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['cont_note']))));

	$rowNo = 27;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('제공방법');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['provide_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['provide_note']))));


	//인지활동 프로그램 제공계획
	$rowNo = 28;
	$cellF = 'A';
	$cellT = 'C';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue("인지활동\n프로그램\n제공계획");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+3));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->applyFromArray(fnStyle());

	$cellF = 'D';
	$cellT = 'F';
	$sheet->getCell($cellF.$rowNo)->setValue('인지자극');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());

	$cellF = 'G';
	$cellT = 'I';
	$sheet->getCell($cellF.$rowNo)->setValue('필요내용');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['plan_rec_text']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['plan_rec_text']))));

	$rowNo = 29;
	$cellF = 'G';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('제공방법');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['plan_rec_way']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['plan_rec_way']))));

	$rowNo = 30;
	$cellF = 'D';
	$cellT = 'F';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue("신체능력\n잔존.유지");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+1));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+1))->applyFromArray(fnStyle());

	$cellF = 'G';
	$cellT = 'I';
	$sheet->getCell($cellF.$rowNo)->setValue('필요내용');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['plan_body_text']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['plan_body_text']))));

	$rowNo = 31;
	$cellF = 'G';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('제공방법');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['plan_body_way']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['plan_body_way']))));


	//보호자 상담
	$rowNo = 32;
	$cellF = 'A';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('보호자 상담');
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['guard_text']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['guard_text']))));


	//급여제공확인
	$rowNo = 33;
	$cellF = 'A';
	$cellT = 'C';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue("급여 및\n인지활동\n프로그램\n제공확인");
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+8))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+8));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+8))->applyFromArray(fnStyle());

	$cellF = 'D';
	$cellT = 'I';
	$sheet->getCell($cellF.$rowNo)->setValue('확인내용'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.($rowNo+3));
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.($rowNo+3))->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'U';
	$sheet->getCell($cellF.$rowNo)->setValue('근무일지작성 : '.($row['write_log_yn'] == 'Y' ? '▣' : '□').'예 '.($row['write_log_yn'] == 'N' ? '▣' : '□').'아니오'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNRN'));

	$cellF = 'V';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('제공시간준수 : '.($row['provide_chk_yn'] == 'Y' ? '▣' : '□').'예 '.($row['provide_chk_yn'] == 'N' ? '▣' : '□').'아니오'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNLN'));

	$rowNo = 34;
	$cellF = 'J';
	$cellT = 'U';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('적절 서비스 : '.($row['right_svc_yn'] == 'Y' ? '▣' : '□').'예 '.($row['right_svc_yn'] == 'N' ? '▣' : '□').'아니오'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNRN'));

	$cellF = 'V';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('주거환경청결 : '.($row['house_env_yn'] == '1' ? '▣' : '□').'상 '.($row['house_env_yn'] == '2' ? '▣' : '□').'중 '.($row['house_env_yn'] == '3' ? '▣' : '□').'하'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BNLN'));

	$rowNo = 35;
	$cellF = 'J';
	$cellT = 'U';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('업무태도친절 : '.($row['work_mind_yn'] == '1' ? '▣' : '□').'상 '.($row['work_mind_yn'] == '2' ? '▣' : '□').'중 '.($row['work_mind_yn'] == '2' ? '▣' : '□').'하'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDRN'));

	$cellF = 'V';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue('유니폼 착용 : '.($row['uniform_yn'] == 'Y' ? '▣' : '□').'예 '.($row['uniform_yn'] == 'N' ? '▣' : '□').'아니오'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('BDLN'));

	$rowNo = 36;
	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['check_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle('TN'));
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['check_note']))));

	$rowNo = 37;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('조치사항'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['action_note']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['action_note']))));

	$rowNo = 38;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	if($yymm < '201701'){
		$sheet->getCell($cellF.$rowNo)->setValue('요양보호사 성명');
	}else {
		$sheet->getCell($cellF.$rowNo)->setValue('급여제공자 성명(인)*');
	}
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue($memStr); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$rowNo = 39;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('급여제공 중 방문여부'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(($row['svcporc_yn'] == 'Y' ? '▣' : '□').'예 '.($row['svcporc_yn'] == 'N' ? '▣' : '□').'아니오'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$rowNo = 40;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('방문장소'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['visit_place']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['visit_place']))));

	$rowNo = 41;
	$cellF = 'D';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('방문불가 사유'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['notvisit_reason']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['notvisit_reason']))));

	$rowNo = 42;
	$cellF = 'A';
	$cellT = 'I';
	$sheet->getRowDimension($rowNo)->setRowHeight($rowH);
	$sheet->getCell($cellF.$rowNo)->setValue('총 평'); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());

	$cellF = 'J';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue(Trim(StripSlashes($row['comment']))); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->applyFromArray(fnStyle());
	$sheet->getRowDimension($rowNo)->setRowHeight(12 * GetRowcount(Trim(StripSlashes($row['comment']))));


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(17);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(true);

	$rowNo = 43;
	$cellF = 'A';
	$cellT = 'AF';
	$sheet->getCell($cellF.$rowNo)->setValue($_SESSION['userCenterName']); //
	$sheet->getStyle($cellF.$rowNo)->getAlignment()->setWrapText(true);
	$sheet->getStyle($cellF.$rowNo.':'.$cellT.$rowNo)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->mergeCells($cellF.$rowNo.':'.$cellT.$rowNo);


	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>
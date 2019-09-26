w<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$file = $_FILES['upladFile'];
	$filePath = '../tempFile/PAY_IN_'.mktime();

	if (move_uploaded_file($file['tmp_name'], $filePath)){
		error_reporting(E_ALL ^ E_NOTICE);

		include_once('../excel/PHPExcel.php'); //라이브러리 업로드 경로
		include_once('../excel/PHPExcel/IOFactory.php'); //라이브러리 업로드 경로

		@$lo_reader = PHPExcel_IOFactory::createReaderForFile($filePath); //읽기객체생성
		@$lo_reader->setReadDataOnly(true); //읽기전용설정
		@$lo_excel = $lo_reader->load($filePath); //읽기
		@$lo_excel->setActiveSheetIndex(0); //시트선택
		@$lo_sheet = $lo_excel->getActiveSheet(); //시트활성화
		@$lo_rowIterator = $lo_sheet->getRowIterator(); //모든 행

		foreach($lo_rowIterator as $row){
			@$lo_cell = $row->getCellIterator();
			@$lo_cell->setIterateOnlyExistingCells(false); //해당 행의 모든 열

			/*
			$excelCol = Array(
				'A'=>'no'			//no
			,	'B'=>'org_no'		//기관기호
			,	'C'=>'claim_dt'		//출금(청구)예정일자
			,	'D'=>'issue_dt'		//입금/출금일자
			,	'E'=>'cms_no'		//CMS번호
			,	'F'=>'issue_time'	//거래시간
			,	'G'=>'orgName'		//기관명
			,	'H'=>'in_gbn'		//입금구분
			,	'I'=>'in_amt'		//입금금액
			,	'J'=>'out_stat'		//출금 상태
			,	'K'=>'cont_com'		//계약회사
			,	'L'=>'out_bank'		//출금은행
			,	'M'=>'out_acct_no'	//출금계좌
			,	'N'=>'in_bank'		//입금은행
			,	'O'=>'in_acct_no'	//입금계좌
			,	'P'=>'reg_gbn'		//등록구분
			,	'Q'=>'acct_log'		//거래기록사항
			,	'R'=>'remark'		//비고
			,	'S'=>'claim_amt'	//청구금액
			,	'T'=>'use_ym'		//사용원월
			);
			*/
			$excelCol = Array(
				'A'=>'no'			//no
			,	'B'=>'org_no'		//기관기호
			,	'C'=>'claim_dt'		//출금(청구)예정일자
			,	'D'=>'issue_dt'		//입금/출금일자
			,	'E'=>'cms_no'		//CMS번호
			,	'F'=>'orgName'		//기관명
			,	'G'=>'issue_time'	//거래시간
			,	'H'=>'use_ym'		//사용원월
			,	'I'=>'tbl_gbn'		//테이블구분
			,	'J'=>'claim_amt'	//청구금액
			,	'K'=>'in_gbn'		//입금구분
			,	'L'=>'out_stat'		//출금 상태
			,	'M'=>'in_amt'		//입금금액
			,	'N'=>'out_bank'		//출금은행
			,	'O'=>'out_acct_no'	//출금계좌
			,	'P'=>'remark'		//비고
			,	'Q'=>'acct_log'		//거래기록사항
			,	'R'=>'in_bank'		//입금은행
			,	'S'=>'in_acct_no'	//입금계좌
			,	'T'=>'reg_gbn'		//등록구분
			,	'U'=>'cont_com'		//계약회사
			);

			foreach($lo_cell as $cell){
				//echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().' | ';
				$rowIdx = $cell->getRow();
				$colIdx = $cell->getColumn();

				if ($rowIdx == 1) continue;

				switch($excelCol[$colIdx]){
					case 'no':
					case 'orgName':
						continue;
						break;

					case 'use_ym':
					case 'claim_dt':
					case 'issue_dt':
						$v = $cell->getValue();
						$v = str_replace('/', '', $v);
						$v = str_replace('-', '', $v);
						$v = str_replace('.', '', $v);
						break;

					case 'issue_time':
						$v = $cell->getValue();
						$v = str_replace(':', '', $v).'000000';
						$v = SubStr($v, 0, 6);
						break;

					case 'in_amt':
					case 'claim_amt':
						$v = $cell->getValue();
						$v = str_replace(',', '', $v);
						break;

					case 'in_gbn':
						$v  = $cell->getValue();
						if ($v == 'CMS'){
							$v = '1';
						}else{
							$v = '2';
						}
						break;

					case 'tbl_gbn':
						$v  = $cell->getValue();

						if ($v == '공통'){
							$v = 'COMMON';
						}else{
							$v = 'DETAIL';
						}
						break;

					default:
						$v = $cell->getValue();
				}

				$data[$rowIdx][$excelCol[$colIdx]] = $v;
			}

			if ($rowIdx > 1){
				if ($seq[$data[$rowIdx]['org_no']][$data[$rowIdx]['issue_dt']]){
					$seq[$data[$rowIdx]['org_no']][$data[$rowIdx]['issue_dt']] ++;
				}else{
					$sql = 'SELECT	IFNULL(MAX(issue_seq), 0) + 1
							FROM	cv_pay_in
							WHERE	org_no	 = \''.$data[$rowIdx]['org_no'].'\'
							AND		issue_dt = \''.$data[$rowIdx]['issue_dt'].'\'';

					//$seq[$data[$rowIdx]['org_no']][$data[$rowIdx]['issue_dt']] = 1;
					$seq[$data[$rowIdx]['org_no']][$data[$rowIdx]['issue_dt']] = $conn->get_data($sql);
				}

				$data[$rowIdx]['issue_seq'] = $seq[$data[$rowIdx]['org_no']][$data[$rowIdx]['issue_dt']];
			}
		}

		@unlink($filePath);

		if (is_array($data)){
			$sql = 'SELECT	IFNULL(MAX(show_key), 0)
					FROM	cv_pay_in
					WHERE	LEFT(show_key, 8) = \''.Date('Ymd').'\'';

			$showKey = $conn->get_data($sql);

			if ($showKey < 1) $showKey = Date('Ymd').'0000';

			$showKey ++;

			foreach($data as $tmpIdx => $R){
				if (!$R['org_no']) continue;

				$sql = 'INSERT INTO cv_pay_in VALUES (
						 \''.$R['org_no'].'\'
						,\''.$R['issue_dt'].'\'
						,\''.$R['issue_seq'].'\'
						,\''.$R['issue_time'].'\'
						,\''.$R['claim_dt'].'\'
						,\''.$R['claim_amt'].'\'
						,\''.$R['cms_no'].'\'
						,\''.$R['in_gbn'].'\'
						,\''.$R['in_amt'].'\'
						,\''.$R['out_stat'].'\'
						,\''.$R['cont_com'].'\'
						,\''.$R['out_bank'].'\'
						,\''.$R['out_acct_no'].'\'
						,\''.$R['in_bank'].'\'
						,\''.$R['in_acct_no'].'\'
						,\''.$R['reg_gbn'].'\'
						,\''.$R['acct_log'].'\'
						,\''.$R['remark'].'\'
						,\''.$showKey.'\'
						,\'N\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						)';

				$query[] = $sql;
			}


			$conn->begin();

			foreach($query as $sql){
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();

					 echo $conn->error_msg.'<br>'.$conn->error_query;
					 exit;
				}
			}

			$conn->commit();
		}

		Unset($data);?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="80px">
				<col width="100px">
				<col width="140px">
				<col width="60px">
				<col width="70px">
				<col width="100px">
				<col width="70px">
				<col width="70px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">기관기호</th>
					<th class="head">기관명</th>
					<th class="head">입/출금일시</th>
					<th class="head">입금구분</th>
					<th class="head">입금금액</th>
					<th class="head">출금상태</th>
					<th class="head">출금은행</th>
					<th class="head">입금은행</th>
					<th class="head last">비고</th>
				</tr>
			</thead>
			<tbody><?
				$sql = 'SELECT	DISTINCT a.org_no, m00_store_nm AS org_name, a.issue_dt, a.issue_time, a.in_gbn, a.in_bank, a.in_amt, a.out_stat, a.out_bank, a.remark
						FROM	cv_pay_in AS a
						INNER	JOIN	m00center
								ON		m00_mcode = a.org_no
						WHERE	show_key = \''.$showKey.'\'
						ORDER	BY org_name, org_no, issue_dt, issue_time';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();
				$no = 1;

				$inGbn = Array('1'=>'CMS', '2'=>'무통장');

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<tr>
					<td class="center"><?=$no;?></td>
					<td class="center"><div class="left"><?=$row['org_no'];?></div></td>
					<td class="center"><div class="left"><?=$row['org_name'];?></div></td>
					<td class="center"><?=$myF->dateStyle($row['issue_dt'], '.');?> <?=$myF->timeStyle($row['issue_time']);?></td>
					<td class="center"><?=$inGbn[$row['in_gbn']];?></td>
					<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
					<td class="center"><div class="left"><?=$row['out_stat'];?></div></td>
					<td class="center"><div class="left"><?=$row['out_bank'];?></div></td>
					<td class="center"><div class="left"><?=$row['in_bank'];?></div></td>
					<td class="center last"><div class="left"><?=$row['remark'];?></div></td>
					</tr><?
				}

				$conn->row_free();?>
			</tbody>
		</table><?
	}

	include_once('../inc/_db_close.php');
?>
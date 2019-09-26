<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$f = $_FILES['filename'];
	$file = '../tempFile/'.mktime();
	$removeYn = $_POST['chkRemoveYn'];
	$dateYn = $_POST['chkDateYn'];
	$allMonDelYn = $_POST['chkAllMonDelYn'];
	$danBipayDelYn = $_POST['chkDanBipayYn'];
	$gbn = $_GET['gbn'];
	$year = $_GET['year'];
	$month = IntVal($_GET['month']);
	$yymm = $year.($month < 10 ? '0' : '').$month;
	
	if ($dateYn == 'Y') $removeYn = 'Y';

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

		/*
		$sql = 'SELECT	LEFT(m02_yjumin, 6) AS birthday, m02_yjumin AS jumin, m02_yname AS name
				FROM	m02yoyangsa
				WHERE	m02_ccode = \''.$orgNo.'\'
				AND		m02_mkind = \'0\'
				ORDER	BY jumin, name';
		*/

		$sql = 'SELECT	LEFT(jumin, 6) AS birthday, jumin AS jumin, m02_yname AS name
				FROM	m02yoyangsa
				INNER   JOIN mem_his
				ON      org_no = m02_ccode
				AND     jumin  = m02_yjumin
				AND date_format(join_dt,\'%Y%m\') <= \''.$yymm.'\'
				AND date_format(ifnull(quit_dt,\'9999-12-31\'),\'%Y%m\') >= \''.$yymm.'\'
				WHERE	m02_ccode = \''.$orgNo.'\'
				AND		m02_mkind = \'0\'
				ORDER	BY jumin, name';
		$mem = $conn->_fetch_array($sql);

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
				if ($cell->getRow() == 1) continue;

				$rowIdx = $cell->getRow();
				
				if ($cell->getColumn() == 'A'){ //일자
					$rowInfo[$rowIdx]['date'] = date('Ymd', (($cell->getValue()- 25569) * 86400));
				}
			}

			foreach($lo_cell as $cell){
				//echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().'<br>';
				/*
					A : 일자
					B : 시작시간
					C : 종료시간
					D : 수급자명
					E : 수급자인정번호
					F : 요양보호사명
					G : 생년월일
					H : 요양보호사번호
					I : 요양보호사구분
					J : 가족여부
					K : 가족관계
					L : 서비스구분
					M : 수가코드
					N : 수가명
					O : 수가
					P : _ROW_STATUS

				 */

				if ($cell->getRow() == 1) continue;
				
				switch($cell->getColumn()){
					case 'A':
						$tgt[$cell->getRow()]['date'] = date('Ymd', (($cell->getValue()- 25569) * 86400));
						$tgt[$cell->getRow()]['saveDate'] = true;

						if ($yymm != SubStr($tgt[$cell->getRow()]['date'], 0, 6)){
							$conn->close();
							echo '업로드 하신 일정계획은 '.$year.'년 '.$month.'월 일정계획이 아닙니다. 확인 후 다시 업로드 하여 주십시오.';
							exit;
						}
						break;

					case 'B':
						$tgt[$cell->getRow()]['from'] = '0'.str_replace(':','', $cell->getValue());
						$tgt[$cell->getRow()]['from'] = SubStr($tgt[$cell->getRow()]['from'], StrLen($tgt[$cell->getRow()]['from']) - 4, StrLen($tgt[$cell->getRow()]['from']));
						break;

					case 'C':
						$tgt[$cell->getRow()]['to'] = '0'.str_replace(':','', $cell->getValue());
						$tgt[$cell->getRow()]['to'] = SubStr($tgt[$cell->getRow()]['to'], StrLen($tgt[$cell->getRow()]['to']) - 4, StrLen($tgt[$cell->getRow()]['to']));
						break;

					case 'D':
						$tgt[$cell->getRow()]['name'] = $cell->getValue();
						break;

					case 'E':
						$tgt[$cell->getRow()]['appNo'] = $cell->getValue();

						break;

					case 'F':
						if ($iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '200' ||
							$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '500'){

							if ($rowInfo[$cell->getRow()]['subcd'] == $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd']){
								$rowIdx = $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['rowIdx'];
							}else{
								$rowIdx = $cell->getRow();
							}
						}else{
							$rowIdx = $cell->getRow();
						}

						if ($rowIdx == $cell->getRow()){
							$memIdx = '1';
						}else{
							$memIdx = '2';
						}
						$tgt[$rowIdx]['mem'.$memIdx]['name'] = $cell->getValue();
						break;

					case 'G':
						if ($iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '200' ||
							$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '500'){

							if ($rowInfo[$cell->getRow()]['subcd'] == $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd']){
								$rowIdx = $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['rowIdx'];
								$memIdx = '2';
							}else{
								$rowIdx = $cell->getRow();
								$memIdx = '1';
							}
						}else{
							$rowIdx = $cell->getRow();
							$memIdx = '1';
						}

						$tgt[$rowIdx]['mem'.$memIdx]['birthday'] = SubStr(date('Ymd', (($cell->getValue()- 25569) * 86400)), 2);

						for($i=0; $i<count($mem); $i++){
							if ($tgt[$rowIdx]['mem'.$memIdx]['birthday'] == $mem[$i]['birthday'] &&
								$tgt[$rowIdx]['mem'.$memIdx]['name'] == $myF->mid($mem[$i]['name'], 0, $myF->len($tgt[$rowIdx]['mem'.$memIdx]['name']))){
								$tgt[$rowIdx]['mem'.$memIdx]['jumin'] = $mem[$i]['jumin'];
								break;
							}
						}
						break;

					case 'I':
						if ($iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '200' ||
							$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '500'){

							if ($rowInfo[$cell->getRow()]['subcd'] == $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd']){
								$rowIdx = $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['rowIdx'];
								$memIdx = '2';
							}else{
								$rowIdx = $cell->getRow();
								$memIdx = '1';
							}
						}else{
							$rowIdx = $cell->getRow();
							$memIdx = '1';
						}
						$yoyGbn = Explode('.', $cell->getValue());

						$tgt[$rowIdx]['mem'.$memIdx]['gbn'] = $yoyGbn[0];

						if ($memIdx == '2'){
							$tmpYoy[1] = Array('name'=>$tgt[$rowIdx]['mem1']['name'], 'birthday'=>$tgt[$rowIdx]['mem1']['birthday'], 'jumin'=>$tgt[$rowIdx]['mem1']['jumin'], 'gbn'=>$tgt[$rowIdx]['mem1']['gbn']);
							$tmpYoy[2] = Array('name'=>$tgt[$rowIdx]['mem2']['name'], 'birthday'=>$tgt[$rowIdx]['mem2']['birthday'], 'jumin'=>$tgt[$rowIdx]['mem2']['jumin'], 'gbn'=>$tgt[$rowIdx]['mem2']['gbn']);

							if ($tmpYoy[1]['gbn'] == '11' || $tmpYoy[1]['gbn'] == '12'){
							}else{
								$tgt[$rowIdx]['mem1']['name']	  = $tmpYoy[2]['name'];
								$tgt[$rowIdx]['mem1']['birthday'] = $tmpYoy[2]['birthday'];
								$tgt[$rowIdx]['mem1']['jumin']	  = $tmpYoy[2]['jumin'];
								$tgt[$rowIdx]['mem1']['gbn']	  = $tmpYoy[2]['gbn'];

								$tgt[$rowIdx]['mem2']['name']	  = $tmpYoy[1]['name'];
								$tgt[$rowIdx]['mem2']['birthday'] = $tmpYoy[1]['birthday'];
								$tgt[$rowIdx]['mem2']['jumin']	  = $tmpYoy[1]['jumin'];
								$tgt[$rowIdx]['mem2']['gbn']	  = $tmpYoy[1]['gbn'];
							}

							if ($tgt[$rowIdx]['mem1']['jumin'] == $tgt[$rowIdx]['mem2']['jumin']){
								$tgt[$rowIdx]['mem2']['name']	  = '';
								$tgt[$rowIdx]['mem2']['birthday'] = '';
								$tgt[$rowIdx]['mem2']['jumin']	  = '';
								$tgt[$rowIdx]['mem2']['gbn']	  = '';
							}

							$tgt[$cell->getRow()]['saveDate'] = false;
						}
						break;

					case 'J':
						if ($iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '200' ||
							$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '500'){

							if ($rowInfo[$cell->getRow()]['subcd'] == $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd']){
								$rowIdx = $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['rowIdx'];
							}else{
								$rowIdx = $cell->getRow();
							}
						}else{
							$rowIdx = $cell->getRow();
						}

						if ($tgt[$rowIdx]['family']['yn'] != 'Y') $tgt[$rowIdx]['family']['yn'] = $cell->getValue();

						if ($tgt[$rowIdx]['family']['yn'] == 'Y'){
							$tmpProcTime = $myF->time2min($tgt[$cell->getRow()]['to']) - $myF->time2min($tgt[$cell->getRow()]['from']);

							if ($tmpProcTime > 90){
								$tgt[$cell->getRow()]['to'] = str_replace(':', '', $myF->min2time($myF->time2min($tgt[$cell->getRow()]['from']) + 90));
							}
						}

						break;

					case 'K':
						$tgt[$cell->getRow()]['family']['gbn'] = $cell->getValue();
						break;

					case 'L':
						if ($iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '200' ||
							$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] == '500'){

							if ($rowInfo[$cell->getRow()]['subcd'] == $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd']){
								$rowIdx = $iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['rowIdx'];
							}else{
								$rowIdx = $cell->getRow();
							}
						}else{
							$rowIdx = $cell->getRow();
						}

						$tgt[$cell->getRow()]['svc']['gbn'] = $cell->getValue();

						if ($tgt[$cell->getRow()]['svc']['gbn'] == '방문요양' ||
							$tgt[$cell->getRow()]['svc']['gbn'] == '방문목욕' ||
							$tgt[$cell->getRow()]['svc']['gbn'] == '방문간호'){
							$tgt[$cell->getRow()]['svc']['kind'] = '0';

							if ($tgt[$cell->getRow()]['svc']['gbn'] == '방문요양'){
								$tgt[$cell->getRow()]['svc']['code'] = '200';
							}else if ($tgt[$cell->getRow()]['svc']['gbn'] == '방문목욕'){
								$tgt[$cell->getRow()]['svc']['code'] = '500';
							}else if ($tgt[$cell->getRow()]['svc']['gbn'] == '방문간호'){
								$tgt[$cell->getRow()]['svc']['code'] = '800';
							}
						}else if ($tgt[$cell->getRow()]['svc']['gbn'] == '주야간보호'){
							$tgt[$cell->getRow()]['svc']['kind'] = '5';
							$tgt[$cell->getRow()]['svc']['code'] = '';
						}

						$iljung[$tgt[$cell->getRow()]['appNo']][$tgt[$cell->getRow()]['date']][$tgt[$cell->getRow()]['from']]['subCd'] = $tgt[$cell->getRow()]['svc']['code'];

						break;

					case 'M':
						$tgt[$cell->getRow()]['longterm']['suga']['code'] = $cell->getValue();
						break;

					case 'N':
						$tgt[$cell->getRow()]['longterm']['suga']['name'] = $cell->getValue();
						break;

					case 'O':
						$tgt[$cell->getRow()]['longterm']['suga']['amt'] = str_replace(',','',$cell->getValue());
						break;
				}
			}
		}
	}
	
	@unlink($file);
	
	if($debug){
		exit;
	}

	if (is_array($tgt)){
		if ($gbn == 'UPLOAD'){
			$rstStr = '';

			foreach($tgt as $tmpIdx => $R){
				if (!$R['saveDate']) continue;
				if ($allMonDelYn == 'Y'){
					if (!$removeSqlYm[SubStr($R['date'], 0, 6)][$R['svc']['kind']]){
						$sql = 'DELETE
								FROM	t01iljung
								WHERE	t01_ccode		= \''.$orgNo.'\'
								AND		t01_mkind		= \''.$R['svc']['kind'].'\'
								AND		LEFT(t01_sugup_date, 6) = \''.SubStr($R['date'], 0, 6).'\'
								AND		IFNULL(t01_bipay_umu, \'N\') != \'Y\'';
						$query[] = $sql;
						$removeSqlYm[SubStr($R['date'], 0, 6)][$R['svc']['kind']] = 'DELETE';
					}
				}

				if (!$R['jumin'] || ($R['svc']['kind'] != '5' && !($R['mem1']['jumin'].$R['mem2']['jumin']))){
					$rstStr .= '<tr>
								<td class="center">'.$myF->dateStyle($R['date'], '.').'</td>
								<td class="center">'.$myF->timeStyle($R['from']).'~'.$myF->timeStyle($R['to']).'</td>
								<td class="center">'.$R['name'].'</td>
								<td class="center">'.$R['appNo'].'</td>
								<td class="center">'.$R['svc']['gbn'].'</td>
								<td class="center">'.$R['mem1']['name'].'</td>
								<td class="center">'.$myF->dateStyle('19'.$R['mem1']['birthday'], '.').'</td>
								<td class="center">'.$R['mem2']['name'].'</td>
								<td class="center">'.$myF->dateStyle('19'.$R['mem2']['birthday'], '.').'</td>
								<td class="center last"><div class="left" style="color:red;">';

					if (!$R['jumin']){
						$rstStr .= '수급자 정보를 찾을 수 없음.<br>';
					}else if (!($R['mem1']['jumin'].$R['mem2']['jumin'])){
						$rstStr .= '요양보호사 정보를 찾을 수 없음.<br>';
					}

					$rstStr .= '</div></td></tr>';
					continue;
				}

				if ($removeYn == 'Y' && !$removeStr[$R['appNo']]){
					$sql = 'DELETE
							FROM	t01iljung
							WHERE	t01_ccode		= \''.$orgNo.'\'
							AND		t01_mkind		= \''.$R['svc']['kind'].'\'
							AND		t01_jumin		= \''.$R['jumin'].'\'
							AND		LEFT(t01_sugup_date, 6) = \''.SubStr($R['date'], 0, 6).'\'
							AND		IFNULL(t01_bipay_umu, \'N\') != \'Y\'';

					$removeStr[$R['appNo']] = 'DELETE';
					$query[] = $sql;

					if ($R['svc']['kind'] == '5' && $danBipayDelYn == 'Y'){
						$sql = 'DELETE
								FROM	dan_nonpayment_iljung
								WHERE	org_no	= \''.$orgNo.'\'
								AND		jumin	= \''.$R['jumin'].'\'
								AND		LEFT(date, 6) = \''.SubStr($R['date'], 0, 6).'\'
								AND		CONCAT(time,\'_\',seq) NOT IN (	SELECT	CONCAT(t01_sugup_fmtime,\'_\',t01_sugup_seq)
																		FROM	t01iljung
																		WHERE	t01_ccode		 = \''.$orgNo.'\'
																		AND		t01_mkind		 = \''.$R['svc']['kind'].'\'
																		AND		t01_jumin		 = \''.$R['jumin'].'\'
																		AND		t01_status_gbn	!= \'1\'
																		AND		t01_status_gbn	!= \'5\'
																		AND		t01_del_yn		 = \'N\'
																		AND		LEFT(t01_sugup_date, 6) = \''.SubStr($R['date'], 0, 6).'\')';
						$query[] = $sql;
					}
				}

				if ($seq[$R['jumin']][$R['date']]){
					$seq[$R['jumin']][$R['date']] ++;
				}else{
					if ($R['svc']['kind'] == '5'){
						$seq[$R['jumin']][$R['date']] = 1;
					}else{
						$seq[$R['jumin']][$R['date']] = 101;
					}
				}

				$loopCnt = 1;
				$timeDouble = false;
				$dmtaYn = '';
				
				
				if ($R['svc']['kind'] == '0'){
					if ($R['svc']['code'] == '200'){
						if ($R['mem2']['gbn'] == '11' || $R['mem2']['gbn'] == '12'){
							$loopCnt = 2;
							$timeDouble = true;
						}else if ($R['mem2']['gbn'] == '3' || $R['mem2']['gbn'] == '1' || $R['mem2']['gbn'] == '7'){
							if ($R['family']['yn'] != 'Y') $dmtaYn = 'Y';
						}
					}
				}else if ($R['svc']['kind'] == '5'){
					if ($R['mem2']['gbn'] == '3' || $R['mem2']['gbn'] == '1' || $R['mem2']['gbn'] == '7'){
						if ($R['family']['yn'] != 'Y') $dmtaYn = 'Y';
					}
				}
				
				if(substr($R['longterm']['suga']['name'], -21) == '치매가족휴가제'){
					$R['svc']['code'] = '210';
					$seq[$R['jumin']][$R['date']] = 1;
					$bipayKind = '1';
				}else {
					$bipayKind = '';
				}

				for($i=1; $i<=$loopCnt; $i++){
					if ($R['svc']['code'] == '200'){
						$disTm = $myF->time2min($R['to']) - $myF->time2min($R['from']);
							
						if($R['longterm']['suga']['name']=='60분이상(인지_가족, 방문당)' || $R['longterm']['suga']['name']=='60분이상(가족, 방문당)'){
							if($disTm >= 90){
								$R['to'] = str_replace(":","", $myF->min2time($myF->time2min($R['from'])+89));
							}
						}else if($R['longterm']['suga']['name']=='90분이상(인지_가족, 방문당)' || $R['longterm']['suga']['name']=='90분이상(가족, 방문당)'){
							
							if($disTm >= 120){
								$R['to'] = str_replace(":","", $myF->min2time($myF->time2min($R['from'])+119));
							}
						}
					}
					
					
					$sql = 'INSERT INTO t01iljung (
							 t01_ccode			/*기관코드*/
							,t01_mkind			/*서비스구분*/
							,t01_jumin			/*주민번호*/
							,t01_sugup_date		/*일자*/
							,t01_sugup_fmtime	/*시작시간*/
							,t01_sugup_totime	/*종료시간*/
							,t01_sugup_seq		/*순번*/
							,t01_sugup_soyotime	/*제공시간*/
							,t01_sugup_yoil		/*요일*/
							,t01_svc_subcode	/*서비스종류*/
							,t01_status_gbn		/*상태*/
							,t01_toge_umu		/*동거여부*/

							,t01_yoyangsa_id1	/*실행 주요양보호사*/
							,t01_yoyangsa_id2	/*실행 부요양보호사*/
							,t01_yname1			/*요양보호사명*/
							,t01_yname2			/*요양보호사명*/

							,t01_mem_cd1		/*계획 주요양보호사*/
							,t01_mem_cd2		/*계획 부요양보호사*/
							,t01_mem_nm1		/*요양보호사명*/
							,t01_mem_nm2		/*요양보호사명*/

							,t01_suga_code1		/*수가코드*/
							,t01_suga			/*수가*/
							,t01_suga_over		/*야간할증금액*/
							,t01_suga_night		/*심야할증금액*/
							,t01_suga_tot		/*수가총액*/

							,t01_e_time			/*야간시간*/
							,t01_n_time			/*심야시간*/
							,t01_holiday		/*휴일여부*/

							,t01_yname5
							,t01_ysudang_yn

							,t01_time_doub
							,t01_request
							,t01_dementia_yn
							,t01_bipay_kind
							) VALUES(
							 \''.$orgNo.'\'
							,\''.$R['svc']['kind'].'\'
							,\''.$R['jumin'].'\'
							,\''.$R['date'].'\'
							,\''.$R['from'].'\'
							,\''.$R['to'].'\'
							,\''.$seq[$R['jumin']][$R['date']].'\'';

					//재공시간
					$procTime = $myF->time2min($R['to']) - $myF->time2min($R['from']);
					
					//치매가족 24시간일 경우
					if($R['svc']['code'] && $R['from'] == $R['to']){
						$procTime = 1440;
					}
					
					$sql .= ',\''.$procTime.'\'';

					//요일
					$weekday = Date('w', StrToTime($R['date']));
					$sql .= ',\''.$weekday.'\'';

					//서비스종류, 상태
					$sql .= ',\''.$R['svc']['code'].'\', \'9\'';

					//동거여부
					$sql .= ',\''.$R['family']['yn'].'\'';

					//실행 주요양보호사
					$sql .= ',\''.$R['mem'.$i]['jumin'].'\'';

					//실행 부요양보호사
					if ($timeDouble){
						$sql .= ',\'\'';
					}else{
						$sql .= ',\''.$R['mem2']['jumin'].'\'';
					}

					//요양보호사명
					$sql .= ',\''.$R['mem'.$i]['name'].'\'';

					if ($timeDouble){
						$sql .= ',\'\'';
					}else{
						$sql .= ',\''.$R['mem2']['name'].'\'';
					}

					//계획 주요양보호사
					$sql .= ',\''.$R['mem'.$i]['jumin'].'\'';

					//계획 부요양보호사
					if ($timeDouble){
						$sql .= ',\'\'';
					}else{
						$sql .= ',\''.$R['mem2']['jumin'].'\'';
					}

					//요양보호사명
					$sql .= ',\''.$R['mem'.$i]['name'].'\'';

					if ($timeDouble){
						$sql .= ',\'\'';
					}else{
						$sql .= ',\''.$R['mem2']['name'].'\'';
					}

					//수가
					if ($R['svc']['kind'] == '0'){
					
						if ($R['svc']['code'] == '500'){
							if($R['longterm']['suga']['code'] != ''){
								if (SubStr($R['longterm']['suga']['code'],0,3) == 'B23'){ //목욕/차량(입욕)
									$bathGbn = '1';
								}else if (SubStr($R['longterm']['suga']['code'],0,3) == 'B24'){ //목욕/차량(가정내입욕)
									$bathGbn = '2';
								}else if (SubStr($R['longterm']['suga']['code'],0,3) == 'B25'){ //목욕/미차량(입욕)
									$bathGbn = '3';
								}else{
									$bathGbn = '3';
								}
							}else {

								$tmpSugaNm = str_replace(' 60분이상', '',$R['longterm']['suga']['name']);
								$sugaNm = str_replace(' 40분이상 60분미만', '',$tmpSugaNm);

								if($sugaNm == '방문목욕 차량을 이용한 경우(차량내 목욕)'){
									$bathGbn = '1';
								}else if($sugaNm == '방문목욕 차량을 이용한 경우(가정내 목욕)'){
									$bathGbn = '2';
								}else {
									$bathGbn = '3';
								}
							}
						}else{
							$bathGbn = '';
						}



						$suga = $mySuga->findSugaCare($orgNo, $R['svc']['code'], $R['date'], $R['from'], $R['to'], $R['family']['yn'], $bathGbn);
						$sql .= ',\''.$suga['code'].'\'
								 ,\''.$suga['cost'].'\'
								 ,\''.$suga['costEvening'].'\'
								 ,\''.$suga['costNight'].'\'
								 ,\''.$suga['costTotal'].'\'
								 ,\''.$suga['timeEvening'].'\'
								 ,\''.$suga['timeNight'].'\'
								 ,\''.$suga['ynHoliday'].'\'';
					}else if ($R['svc']['kind'] == '5'){
						$suga = $mySuga->findSugaDayNight($R['date'], $R['from'], $R['to'], $R['level']);
						$sql .= ',\''.$suga['code'].'\'
								 ,\''.$suga['cost'].'\'
								 ,\''.$suga['costEvening'].'\'
								 ,\''.$suga['costNight'].'\'
								 ,\''.$suga['costTotal'].'\'

								 ,\''.$suga['timeEvening'].'\'
								 ,\''.$suga['timeNight'].'\'
								 ,\''.$suga['ynHoliday'].'\'';
					}

					//수가
					$sql .= ',\''.($R['svc']['code'] != '200' ? 'PERSON' : '').'\'
							 ,\''.($R['svc']['code'] != '200' ? 'Y' : 'N').'\'
							 ,\''.($timeDouble ? 'Y' : '').'\'
							 ,\'EXCEL\'
							 ,\''.$dmtaYn.'\' /*t01_dementia_yn*/
							 ,\''.$bipayKind.'\')';

					$query[] = $sql;
					

					$seq[$R['jumin']][$R['date']] ++;
					
					$sql = 'select count(*)
							from   footing_mg
							where  org_no = \''.$orgNo.'\'
							and    yymm   = \''.$yymm.'\'';
					$footCnt = $conn -> get_data($sql);


					if($footCnt > 0){
						$sql = 'update footing_mg
								set    plan_dt = now()
								where  org_no = \''.$orgNo.'\'
								and    yymm   = \''.$yymm.'\'';
						
					}else {
						$sql = 'insert into footing_mg (
								org_no, 
								yymm,   
								plan_dt ) VALUES( 
								\''.$orgNo.'\'
								,\''.$yymm.'\'
								, now())';
					}	
					
					$query[] = $sql;

				}
				
			}

			if (is_array($query)){
				$conn->begin();

				foreach($query as $sql){
					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();

						 if ($debug){
							 echo 'ERROR MSG<br>'.$conn->error_msg.'<br>'.nl2br($conn->error_query);
						 }else{
							 echo 'ERROR';
						 }
						 exit;
					}
				}

				$conn->commit();
			}
		}
	}

	include_once('../inc/_db_close.php');
?>
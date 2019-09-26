<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$code = $_SESSION['userCenterCode'];
	$kind = '0';

	$ls_duplicate = $_POST['duplicateYn'];

	$f = $_FILES['filename'];
	$file = '../tempFile/'.mktime();

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
				/*********************************************************
					Column 인덱스

					A : 고유번호
					B : 수급자명
					C : 등급
					D : 서비스
					E : 시작시간
					F : 종료시간
					G : 횟수
					H : 수가
					I : 수가계
					J : 수가총액
					K : 요양보호사
					L : 수당
					M : 수당계
					N : 서비스일자

					echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().' | ';
					echo '<br>';
				*********************************************************/

				if (empty($yymm) && $cell->getColumn() == 'A'){
					$str = $cell->getValue();

					for($i=0; $i<strlen($str); $i++){
						if (is_numeric($str[$i])){
							$yymm .= $str[$i];
						}
					}

					if (substr($yymm,0,2) != '20') $yymm = '20'.$yymm;
					if (strlen($yymm) < 6){
						$yymm = substr($yymm,0,4).'0'.substr($yymm,4,1);
					}
				}

				$ls_column = $cell->getColumn();
				$ls_value  = $cell->getValue();

				if ($ls_column == 'E' || $ls_column == 'F'){
					$ls_value = $ls_value * 24;
					$li_value = explode('.', $ls_value);

					if (!empty($li_value[1]))
						$li_value[1] = round(floatval('0.'.$li_value[1]) * 60);
					else
						$li_value[1] = 0;

					$li_value[0] = (intval($li_value[0]) < 10 ? '0' : '').intval($li_value[0]);
					$li_value[1] = (intval($li_value[1]) < 10 ? '0' : '').intval($li_value[1]);

					$ls_value = $li_value[0].':'.$li_value[1];
				}

				$data[$cell->getColumn()] = $ls_value;
			}


			/*********************************************************

				수급자 정보

			*********************************************************/
			if (!isset($arrClient)){
				if ($lbTestMode){
					$sql = 'select min(m03_mkind) as kind
							,      m03_jumin as cd
							,      m03_name as nm
							,      case ifnull(lvl.lvl, \'9\') when \'9\' then \'일반\' else concat(lvl.lvl, \'등급\') end as lvl
							  from m03sugupja as mst
							 inner join (
								   select jumin
								   ,      date_format(min(from_dt), \'%Y%m%d\') as from_dt
								   ,      date_format(max(to_dt),   \'%Y%m%d\') as to_dt
									 from client_his_svc
									where org_no = \''.$code.'\'
									  and svc_cd = \'0\'
									  and date_format(from_dt, \'%Y%m\') <= \''.$yymm.'\'
									  and date_format(to_dt,   \'%Y%m\') >= \''.$yymm.'\'
									group by jumin
								   ) as svc
								on svc.jumin = mst.m03_jumin
							  left join (
								   select jumin
								   ,      min(level) as lvl
								  from client_his_lvl
								 where org_no = \''.$code.'\'
								   and date_format(from_dt, \'%Y%m\') <= \''.$yymm.'\'
								   and date_format(to_dt,   \'%Y%m\') >= \''.$yymm.'\'
									group by jumin
								   ) as lvl
								on lvl.jumin = mst.m03_jumin
							 where m03_ccode = \''.$code.'\'
							 group by m03_jumin';
				}else{
					$sql = 'select cd, nm, m81_name as lvl
							  from (
								   select m03_jumin as cd
								   ,      m03_name as nm
								   ,      m03_ylvl as lvl
								   ,      m03_sdate as f_dt
								   ,      m03_edate as t_dt
									 from m03sugupja
									where m03_ccode  = \''.$code.'\'
									  and m03_mkind  = \''.$kind.'\'
									  and m03_del_yn = \'N\'
									union all
								   select m31_jumin
								   ,      m03_name
								   ,      m31_level
								   ,      m31_sdate as f_dt
								   ,      m31_edate as t_dt
									 from m31sugupja
									inner join m03sugupja
									   on m03_ccode  = m31_ccode
									  and m03_mkind  = m31_mkind
									  and m03_jumin  = m31_jumin
									  and m03_del_yn = \'N\'
									where m31_ccode  = \''.$code.'\'
									  and m31_mkind  = \''.$kind.'\'
									order by cd
								   ) as t
							  left join m81gubun
								on m81_gbn        = \'LVL\'
							   and m81_code       = lvl
							 where left(f_dt, 6) <= \''.$yymm.'\'
							   and left(t_dt, 6) >= \''.$yymm.'\'';
				}

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$nm  = $row['nm'];
					$len = $myF->len($nm);
					$str = $myF->mid($nm, $len-1, 1);

					if ($myF->_isKor($nm) && !$myF->_isKor($str)){
						$nm = $myF->mid($nm, 0, $len - 1);
					}

					$id = $nm.'_'.$row['lvl'];

					$arrClient[$id] = array(
							'jumin' => $row['cd']
						,	'name'  => $row['nm']
						,	'level' => $row['lvl']
					);
				}

				$conn->row_free();
			}


			/*********************************************************

				직원정보

			*********************************************************/
			if (!isset($arrMember)){
				$sql = 'select distinct m02_yjumin as jumin, m02_yname as nm
						  from m02yoyangsa
						 where m02_ccode = \''.$code.'\'';

				$arrMember = $conn->_fetch_array($sql, 'nm');
			}


			/*********************************************************

				수가정보

			*********************************************************/
			if (!isset($arrSuga)){
				$sql = 'select svc, cd, nm, val
						  from (
							   select case left(m01_mcode2, 2) when \'CC\' then \'200\' when \'CB\' then \'500\' else \'800\' end as svc
							   ,      m01_mcode2 as cd
							   ,      m01_suga_cont as nm
							   ,      m01_suga_value as val
							   ,      m01_sdate as f_dt
							   ,      m01_edate as t_dt
								 from m01suga
								where m01_mcode = \'goodeos\'
								union all
							   select case left(m11_mcode2, 2) when \'CC\' then \'200\' when \'CB\' then \'500\' else \'800\' end as svc
							   ,      m11_mcode2
							   ,      m11_suga_cont
							   ,      m11_suga_value
							   ,      m11_sdate
							   ,      m11_edate
								 from m11suga
								where m11_mcode = \'goodeos\'
							   ) as t
						 where left(f_dt, 6) <= \''.$yymm.'\'
						   and left(t_dt, 6) >= \''.$yymm.'\'
						 order by svc, cd';

				$arrSuga = $conn->_fetch_array($sql);
			}


			if (is_numeric(substr($data['N'],0,1))){
				$svcIf = explode('/', $data['D']);
				$svcNm = trim($svcIf[0]);
				$suga  = str_replace(',', '', $data['H']);

				switch($svcNm){
					case '방문요양':
						$svcCd  = '200';
						break;
					case '방문목욕':
						$svcCd  = '500';
						break;
					case '방문간호':
						$svcCd  = '800';
						break;
				}

				foreach($arrSuga as $j => $row){
					$lb_add    = false;
					$ls_family = 'N';

					if ($svcCd == '200' && is_numeric(strpos($svcIf[1], '가족케어'))){
						if (is_numeric(strpos($row['nm'], '동거')))
							$lb_add = true;

						$ls_family = 'Y';
					}else{
						$lb_add = true;
					}

					if ($row['svc'] == $svcCd && $row['val'] == $suga && $lb_add){
						$sugaCd = $row['cd'];
						$sugaNm = $row['nm'];
						break;
					}
				}

				$memIf = explode(',', $data['K']);

				if (!isset($memIf[0])) $memIf[0] = '';
				if (!isset($memIf[1])) $memIf[1] = '';

				$from    = str_replace(':', '', $data['E']);
				$to      = str_replace(':', '', $data['F']);
				$diffMin = $myF->dateDiff('n', $data['E'], $data['F']);

				if (strlen($from) == 3) $from = '0'.$from;
				if (strlen($to) == 3) $to = '0'.$to;

				//수급자정보
				if (!empty($data['B']) && !empty($data['C'])){
					$cNm  = $data['B'];
					$cLvl = $data['C'];
					$cId  = $arrClient[$cNm.'_'.$cLvl]['jumin'];
				}

				$csvData[$rowId]['cId']     = $cId;
				$csvData[$rowId]['cNm']     = $cNm;
				$csvData[$rowId]['cLvl']    = $cLvl;
				$csvData[$rowId]['svcNm']   = $svcNm;
				$csvData[$rowId]['svcCd']   = $svcCd;
				$csvData[$rowId]['sugaNm']  = $sugaNm;
				$csvData[$rowId]['family']  = $ls_family;
				$csvData[$rowId]['from']    = $from;
				$csvData[$rowId]['to']      = $to;
				$csvData[$rowId]['diffMin'] = $diffMin;
				$csvData[$rowId]['suga']    = $suga;
				$csvData[$rowId]['sugaCd']  = $sugaCd;
				$csvData[$rowId]['sugaNm']  = $sugaNm;
				$csvData[$rowId]['mCd1']    = $arrMember[$memIf[0]]['jumin'];
				$csvData[$rowId]['mNm1']    = $memIf[0];
				$csvData[$rowId]['mCd2']    = (!empty($memIf[1]) ? $arrMember[$memIf[1]]['jumin'] : '');
				$csvData[$rowId]['mNm2']    = $memIf[1];
				$csvData[$rowId]['extra']   = str_replace(',', '', $data['L']);
				$csvData[$rowId]['dt']      = $data['N'];
				$csvData[$rowId]['fullNm']  = $data['D'];
				$rowId ++;
			}

			unset($data);
		}
	}

	@unlink($file);

	echo $myF->header_script();

	if (is_array($csvData)){
		$defaultQuery = 'insert into t01iljung (
						  t01_ccode
						 ,t01_mkind
						 ,t01_jumin
						 ,t01_sugup_date
						 ,t01_sugup_fmtime
						 ,t01_sugup_seq
						 ,t01_sugup_totime
						 ,t01_sugup_soyotime
						 ,t01_sugup_proctime
						 ,t01_sugup_yoil
						 ,t01_svc_subcode
						 ,t01_status_gbn
						 ,t01_toge_umu
						 ,t01_yoyangsa_id1
						 ,t01_yoyangsa_id2
						 ,t01_yname1
						 ,t01_yname2
						 ,t01_mem_cd1
						 ,t01_mem_cd2
						 ,t01_mem_nm1
						 ,t01_mem_nm2
						 ,t01_suga_code1
						 ,t01_suga
						 ,t01_suga_over
						 ,t01_suga_night
						 ,t01_suga_tot
						 ,t01_plan_work
						 ,t01_plan_sudang
						 ,t01_plan_cha
						 ,t01_e_time
						 ,t01_n_time
						 ,t01_ysudang_yn
						 ,t01_ysudang
						 ,t01_ysudang_yul1
						 ,t01_ysudang_yul2
						 ,t01_conf_suga_code
						 ,t01_conf_suga_value
						 ,t01_holiday
						 ,t01_modify_pos) values ';

		foreach($csvData as $i => $row){
			//일정 일자
			$ls_dt = explode(',', $row['dt']);

			foreach($ls_dt as $j => $dt){
				if (!empty($dt)){
					$dt = (intval($dt) < 10 ? '0' : '').intval($dt);

					switch($row['sugaCd']){
						case 'CBKD1':
							$bathKind = '2';
							break;

						case 'CBKD2':
							$bathKind = '3';
							break;

						case 'CBFD1':
							$bathKind = '4';
							break;

						default:
							$bathKind = '';
					}

					if ($tmpSuga != $row['svcCd'].'_'.$row['from'].'_'.$row['to'].'_'.$row['family'].'_'.$row['fullNm']){
						$tmpSuga  = $row['svcCd'].'_'.$row['from'].'_'.$row['to'].'_'.$row['family'].'_'.$row['fullNm'];
						$sugaIf = $conn->_find_suga_(
								'goodeos'
							,	$row['svcCd']
							,	$yymm.$dt
							,	$row['from']
							,	$row['to']
							,	$row['diffMin']
							,	$row['family']
							,	$bathKind
						);
					}


					if ($row['svcCd'] == '500'){
						$ls_extraYn    = 'Y';
						$li_extraPay   = $row['extra'] * 2;
						$li_extraRate1 = 50;
						$li_extraRate2 = 50;
					}else if ($row['svcCd'] == '800'){
						$ls_extraYn    = 'Y';
						$li_extraPay   = $row['extra'];
						$li_extraRate1 = 100;
						$li_extraRate2 = 0;
					}else{
						$ls_extraYn    = 'N';
						$li_extraPay   = 0;
						$li_extraRate1 = 50;
						$li_extraRate2 = 50;
					}

					$queryId = sizeof($query);

					if ($ls_duplicate != 'Y'){
						$sql = 'select ifnull(max(t01_sugup_seq), 0)
								  from t01iljung
								 where t01_ccode        = \''.$code.'\'
								   and t01_mkind        = \''.$kind.'\'
								   and t01_jumin        = \''.$row['cId'].'\'
								   and t01_sugup_date   = \''.$yymm.$dt.'\'
								   and t01_sugup_fmtime = \''.$row['from'].'\'
								   and t01_svc_subcode  = \''.$row['svcCd'].'\'';

						$li_no  = $conn->get_data($sql);

						$query[$queryId]['del'] = 'update t01iljung
													  set t01_del_yn = \'Y\'
													where t01_ccode        = \''.$code.'\'
													  and t01_mkind        = \''.$kind.'\'
													  and t01_jumin        = \''.$row['cId'].'\'
													  and t01_sugup_date   = \''.$yymm.$dt.'\'
													  and t01_sugup_fmtime = \''.$row['from'].'\'
													  and t01_sugup_seq    = \''.$li_no.'\'';

						$sql = 'select ifnull(max(t01_sugup_seq), 0)
								  from t01iljung
								 where t01_ccode        = \''.$code.'\'
								   and t01_mkind        = \''.$kind.'\'
								   and t01_jumin        = \''.$row['cId'].'\'
								   and t01_sugup_date   = \''.$yymm.$dt.'\'
								   and t01_sugup_fmtime = \''.$row['from'].'\'';

						$li_no  = $conn->get_data($sql);
					}else{
						$query[$queryId]['del'] = '';
						$li_no = 0;
					}

					$li_seq = 1;

					if (is_array($query)){
						foreach($query as $j => $tmpQuery){
							if ($tmpQuery['key'] == $row['cId'].'_'.$yymm.$dt.'_'.$row['from']){
								$li_seq ++;
								break;
							}
						}
					}

					$li_seq = $li_seq + $li_no;

					$query[$queryId]['key']   = $row['cId'].'_'.$yymm.$dt.'_'.$row['from'];
					$query[$queryId]['seq']   = $li_seq;

					if (!empty($row['cId']) && !empty($row['mCd1'])){
						#echo $row['cId'].'/'.$row['cNm'].'/'.$yymm.$dt.'/'.$row['from'].'/'.$row['to'].'/'.$sugaIf['code'].'<br>';
						$query[$queryId]['query'] = $defaultQuery.'(
								\''.$code.'\'
							,	\''.$kind.'\'
							,	\''.$row['cId'].'\'
							,	\''.$yymm.$dt.'\'
							,	\''.$row['from'].'\'
							,	\''.$li_seq.'\'
							,	\''.$row['to'].'\'
							,	\''.$row['diffMin'].'\'
							,	\''.$row['diffMin'].'\'
							,	\''.date('w', strtotime($yymm.$dt)).'\'
							,	\''.$row['svcCd'].'\'
							,	\'9\'
							,	\''.$row['family'].'\'
							,	\''.$row['mCd1'].'\'
							,	\''.$row['mCd2'].'\'
							,	\''.$row['mNm1'].'\'
							,	\''.$row['mNm2'].'\'
							,	\''.$row['mCd1'].'\'
							,	\''.$row['mCd2'].'\'
							,	\''.$row['mNm1'].'\'
							,	\''.$row['mNm2'].'\'
							,	\''.$sugaIf['code'].'\'
							,	\''.$sugaIf['cost'].'\'
							,	\''.$sugaIf['evening_cost'].'\'
							,	\''.$sugaIf['night_cost'].'\'
							,	\''.$sugaIf['total_cost'].'\'
							,	\''.$row['diffMin'].'\'
							,	\''.$li_extraPay.'\'
							,	\''.($sugaIf['total_cost'] - $li_extraPay).'\'
							,	\''.$sugaIf['evening_time'].'\'
							,	\''.$sugaIf['night_time'].'\'
							,	\''.$ls_extraYn.'\'
							,	\''.$li_extraPay.'\'
							,	\''.$li_extraRate1.'\'
							,	\''.$li_extraRate2.'\'
							,	\''.$sugaIf['code'].'\'
							,	\''.$sugaIf['total_cost'].'\'
							,	\''.$sugaIf['holiday_yn'].'\'
							,	\'N\'
						)';
					}
				}
			}
		}

		$conn->begin();

		foreach($query as $i => $sql){
			if (!empty($sql['del']))
				$conn->execute($sql['del']);

			$conn->execute($sql['query']);
		}

		if ($result == 1)
			$conn->commit();
	}

	echo $result;

	include_once('../inc/_db_close.php');
?>
		/*********************************************************

			CSV 읽기

		*********************************************************/
		$rowId  = 0;
		$handle = fopen($file, "r");
		$yymm   = '';

		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if (empty($yymm)){
				$str = $myF->utf($data[0]);

				for($i=0; $i<strlen($str); $i++){
					if (is_numeric($str[$i])){
						$yymm .= $str[$i];
					}
				}

				if (strlen($yymm) < 6){
					$yymm = substr($yymm,0,4).'0'.substr($yymm,4,1);
				}
			}


			/*********************************************************

				수급자 정보

			*********************************************************/
			if (!isset($arrClient)){
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


			if (is_numeric(substr($data[13],0,1))){
				/*
					index 구분
					0 : X
					1 : 수급자명
					2 : 장기요양등급
					3 : 서비스 및 수가명
					4 : 시작시간
					5 : 종료시간
					6 : 횟수
					7 : 수가
					8 : 수가합계
					9 : 수가총액
					10 : 요양보호사
					11 : 수당
					12 : 수당계
					13 : 서비스 제공 일자

					Ex) Array ( [0] => [1] => 강군순 [2] => 3등급 [3] => 방문요양 / 120분 [4] => 8:00 [5] => 10:00 [6] => 17 [7] => 26,700 [8] => 453,900 [9] => 739,060 [10] => 이정이 [11] => 14,000 [12] => 238,000 [13] => 4, 5, 6, 7, 10, 11, 12, 13, 18, 19, 20, 24, 25, 26, 27, 28, 31, ) 1
				*/

				$svcIf = explode('/', $myF->utf($data[3]));

				$svcNm = trim($svcIf[0]);
				$suga  = str_replace(',', '', $data[7]);

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

				$memIf = explode(',', $myF->utf($data[10]));

				$from    = str_replace(':', '', $data[4]);
				$to      = str_replace(':', '', $data[5]);
				$diffMin = $myF->dateDiff('n', $data[4], $data[5]);

				if (strlen($from) == 3) $from = '0'.$from;
				if (strlen($to) == 3) $to = '0'.$to;

				//수급자정보
				if (!empty($data[1]) && !empty($data[2])){
					$cNm  = $myF->utf($data[1]);
					$cLvl = $myF->utf($data[2]);
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
				$csvData[$rowId]['mCd2']    = $arrMember[$memIf[1]]['jumin'];
				$csvData[$rowId]['mNm2']    = $memIf[1];
				$csvData[$rowId]['extra']   = str_replace(',', '', $data[11]);
				$csvData[$rowId]['dt']      = $data[13];

				$rowId ++;
			}
		}
		fclose($handle);
<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$calGbn = $_POST['calGbn'];
	$calTarget = $ed->de($_POST['calTarget']);



	if ($month < 10){
		$month = '0'.$month;
	}

	if ($calGbn == '1'){
		$isPerson = false;
	}else{
		$isPerson = true;
	}

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	//고객별 옵션
	$sql = 'SELECT jumin
			,      limit_yn
			  FROM client_option
			 WHERE org_no = \''.$code.'\'';

	if ($isPerson){
		$sql .= ' AND jumin = \''.$calTarget.'\'';
	}

	$laOption = $conn->_fetch_array($sql, 'jumin');

	//재가요양 청구한도
	$sql = 'SELECT jumin
			,      seq
			,      amt_care
			,      amt_bath
			,      amt_nurse
			,      amt
			  FROM client_his_limit
			 WHERE org_no   = \''.$code.'\'
			   AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

	if ($isPerson){
		$sql .= ' AND jumin = \''.$calTarget.'\'';
	}

	$sql .= ' ORDER BY jumin, seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$laLimit[$row['jumin']] = Array('care'=>$row['amt_care'],'bath'=>$row['amt_bath'],'nurse'=>$row['amt_nurse'],'tot'=>$row['amt']);
	}

	$conn->row_free();

	$conn->begin();

	//로그내역 복귀
	$sql = 'SELECT *
			  FROM plan_conf_his
			 WHERE org_no       = \''.$code.'\'
			   AND LEFT(date,6) = \''.$year.$month.'\'';

	if ($isPerson){
		$sql .= ' AND jumin = \''.$calTarget.'\'';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		//일정확정 데이타 수정
		$sql = 'UPDATE t01iljung
				   SET t01_conf_fmtime     = \''.$row['conf_from'].'\'
				,      t01_conf_totime     = \''.$row['conf_to'].'\'
				,      t01_conf_soyotime   = \''.$row['conf_time'].'\'
				,      t01_conf_suga_code  = \''.$row['conf_suga'].'\'
				,      t01_conf_suga_value = \''.$row['conf_value'].'\'
				 WHERE t01_ccode = \''.$row['org_no'].'\'
				   AND t01_mkind = \''.$row['svc_kind'].'\'
				   AND t01_jumin = \''.$row['jumin'].'\'
				   AND t01_sugup_date   = \''.$row['date'].'\'
				   AND t01_sugup_fmtime = \''.$row['time'].'\'
				   AND t01_sugup_seq    = \''.$row['seq'].'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo 9;
			 exit;
		}
	}

	$conn->row_free();

	//로그내역 삭제
	$sql = 'DELETE
			  FROM plan_conf_his
			 WHERE org_no       = \''.$code.'\'
			   AND LEFT(date,6) = \''.$year.$month.'\'';

	if ($isPerson){
		$sql .= ' AND jumin = \''.$calTarget.'\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
		 echo 9;
		 exit;
	}

	$conn->commit();

	// 수급자
	#############################################################
	#
		UnSet($client);

		//재가데이타
		$sql = 'select m03_ccode as code
				,      min(m03_mkind) as kind
				,      m03_jumin as jumin
				,      m03_name as name
				,      m03_key as client_key
				,      ifnull(lvl.lvl, \'9\') as lvl
				,      ifnull(amt.amt, 0) as kupyeo_max
				/*,      ifnull(kind.rate, case when lvl.lvl is null then 100 else 15 end) as bonin_yul*/
				,      svc.from_dt as date_from
				,      svc.to_dt as date_to
				  from m03sugupja as mst
				 inner join (
					   select jumin
					   ,      date_format(min(from_dt), \'%Y%m%d\') as from_dt
					   ,      date_format(max(to_dt),   \'%Y%m%d\') as to_dt
						 from client_his_svc
						where org_no = \''.$code.'\'
						  and svc_cd = \'0\'
						  and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
						  and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
						group by jumin
					   ) as svc
					on svc.jumin = mst.m03_jumin
				  left join (
					   select jumin
					   ,      min(level) as lvl
						 from client_his_lvl
						where org_no = \''.$code.'\'
						  and svc_cd = \'0\'
						  and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
						  and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
						group by jumin
					   ) as lvl
					on lvl.jumin = mst.m03_jumin
				  left join (
					   select m91_code as cd
					   ,      m91_kupyeo as amt
						 from m91maxkupyeo
						where left(m91_sdate, 6) <= \''.$year.$month.'\'
						  and left(m91_edate, 6) >= \''.$year.$month.'\'
					   ) as amt
					on amt.cd = lvl.lvl
				/*
				  left join (
					   select jumin
					   ,      seq
					   ,      kind
					   ,      rate
						 from client_his_kind
						where org_no = \''.$code.'\'
						  and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
						  and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
						order by jumin, seq desc
					   ) as kind
					on kind.jumin = mst.m03_jumin
				*/
				 where m03_ccode = \''.$code.'\'';

		if ($isPerson){
			$sql .= ' AND m03_jumin = \''.$calTarget.'\'';
		}

		$sql .= ' group by m03_jumin, m03_name, m03_key
				  order by name';

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (!Empty($laLimit[$row['jumin']]['tot'])){
				$row['kupyeo_max'] = $laLimit[$row['jumin']]['tot'];
				$row['limitCare']  = $laLimit[$row['jumin']]['care'];
				$row['limitBath']  = $laLimit[$row['jumin']]['bath'];
				$row['limitNurse'] = $laLimit[$row['jumin']]['nurse'];
			}

			$liIdx = sizeof($client);
			$client[$liIdx] = $row;
		}

		$conn->row_free();

		//수급자구분
		$sql = 'select jumin
				,      seq
				,      kind
				,      rate
				,      from_dt
				,      to_dt
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'';

		if ($isPerson){
			$sql .= ' AND jumin = \''.$calTarget.'\'';
		}

		$sql .= ' order by jumin, seq';

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$liIdx = sizeof($laClientKind[$row['jumin']]);

			$laClientKind[$row['jumin']][$liIdx] = array(
					'kind'=>$row['kind']
				,	'rate'=>$row['rate']
				,	'from'=>str_replace('-','',$row['from_dt'])
				,	'to'  =>str_replace('-','',$row['to_dt'])
			);
		}

		$conn->row_free();

		$conf_index   = 0;
		$client_count = sizeof($client);

		//한도 초과제한
		for($i=0; $i<$client_count; $i++){
			if ($client[$i]['kind'] == '0' && $laOption[$client[$i]['jumin']]['limit_yn'] == 'N'){
				$laSvcLimit['200'] = $client[$i]['limitCare'];  //방문요양한도
				$laSvcLimit['500'] = $client[$i]['limitBath'];  //방문목욕한도
				$laSvcLimit['800'] = $client[$i]['limitNurse']; //방문간호한도
				$laSvcSuga         = Array('200'=>0,'500'=>0,'800'=>0);
				$laOverAmt         = Array('200'=>0,'500'=>0,'800'=>0);

				$liMaxAmt   = $client[$i]['kupyeo_max']; //한도금액
				$liSugaTot  = 0;
				$liSugaVal  = 0;
				$liOverAmt  = 0;
				$liTmpSuga  = 0;
				$liProcTime = 0;
				$liOverVal  = 0;

				/*
					$sql = "select *
							  from t01iljung
							 where t01_ccode               = '".$client[$i]['code']."'
							   and t01_mkind               = '".$client[$i]['kind']."'
							   and t01_jumin               = '".$client[$i]['jumin']."'
							   and left(t01_sugup_date, 6) = '".$year.$month."'
							   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
							   and t01_status_gbn          = '1'
							   and t01_del_yn              = 'N'
							 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
				 */

				$sql = 'select	client_his_kind.rate
						,		t01iljung.*
						from	t01iljung
						left	join client_his_kind
								on client_his_kind.org_no = t01_ccode
								and client_his_kind.jumin = t01_jumin
								and REPLACE(client_his_kind.from_dt,\'-\',\'\') <= t01_sugup_date
								and REPLACE(client_his_kind.to_dt,\'-\',\'\') >= t01_sugup_date
						where	t01_ccode               = \''.$client[$i]['code'].'\'
						and		t01_mkind               = \''.$client[$i]['kind'].'\'
						and		t01_jumin               = \''.$client[$i]['jumin'].'\'
						and		left(t01_sugup_date, 6) = \''.$year.$month.'\'
						and		t01_sugup_date    between \''.$client[$i]['date_from'].'\' and \''.$client[$i]['date_to'].'\'
						and		t01_del_yn              = \'N\'
						and		t01_status_gbn          = \'1\'
						order	by rate, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime';


				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($ii=0; $ii<$rowCount; $ii++){
					$row = $conn->select_row($ii);

					if ($row['t01_bipay_umu'] != 'Y'){
						//$row['t01_conf_suga_value'] = Round(($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01))) / 10) * 10;
						//$liSugaVal = $row['t01_conf_suga_value'];
						//감액된 수가 적용
						if ($year.$month >= '201401'){
							//$liSugaVal = Round(($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01))) / 10) * 10;
							$liSugaVal = $myF->cutOff($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01)));
						}else{
							$liSugaVal = $myF->cutOff($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01)));
						}
					}else{
						$liSugaVal = 0;
					}

					//추가금
					if ($lbLimitSet){
						if ($laSvcLimit['200'] > 0 ||
							$laSvcLimit['500'] > 0 ||
							$laSvcLimit['800'] > 0){

							if ($laSvcLimit[$row['t01_svc_subcode']] < $laSvcSuga[$row['t01_svc_subcode']] + $liSugaVal){
								//총급여액보다 계획총금액이 넘어갔다.
								if ($laOverAmt[$row['t01_svc_subcode']] == 0){
									$liTmpSuga  = $laSvcLimit[$row['t01_svc_subcode']] - $laSvcSuga[$row['t01_svc_subcode']];
									$laOverAmt[$row['t01_svc_subcode']]  = $liSugaVal - $liTmpSuga;
									$liOverVal  = $liSugaVal - $laOverAmt[$row['t01_svc_subcode']];
									$laResult   = Explode(chr(1), $mySuga->getSugaTime($row['t01_conf_date'], SubStr($row['t01_conf_suga_code'],0,4), $liOverVal));
									$liProcTime = IntVal($laResult[2]);
									$lsSugaCd   = $laResult[0];
								}else{
									$laOverAmt[$row['t01_svc_subcode']] += $liSugaVal;
									$liProcTime = 0;
									$liOverVal  = 0;
									$lsSugaCd   = '';
								}

								$lsHisQuery[$row['t01_mkind']][$row['t01_jumin']][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']] = Array(
										'date'	=>$row['t01_conf_date']
									,	'from'	=>$row['t01_conf_fmtime']
									,	'to'	=>$row['t01_conf_totime']
									,	'time'	=>$row['t01_conf_soyotime']
									,	'suga'	=>$row['t01_conf_suga_code']
									,	'value'	=>$row['t01_conf_suga_value']
									,	'svcCd'	=>$row['t01_svc_subcode']
									,	'code'	=>$lsSugaCd
									,	'proc'	=>$liProcTime
									,	'val'	=>$liOverVal
								);
							}

							#echo $laSvcLimit[$row['t01_svc_subcode']].' / '
							#	.$laSvcSuga[$row['t01_svc_subcode']].' / '
							#	.$liSugaVal.' / '
							#	.$liOverVal.' / '
							#	.chr(13).chr(10);

							//총 수가금액
							$laSvcSuga[$row['t01_svc_subcode']] += $liSugaVal;
						}else{
							if ($liMaxAmt < $liSugaTot + $liSugaVal){
								//총급여액보다 계획총금액이 넘어갔다.
								if ($liOverAmt == 0){
									$liTmpSuga  = $liMaxAmt - $liSugaTot;
									$liOverAmt  = $liSugaVal - $liTmpSuga;
									$liOverVal  = $liSugaVal - $liOverAmt;
									$laResult   = Explode(chr(1), $mySuga->getSugaTime($row['t01_conf_date'], SubStr($row['t01_conf_suga_code'],0,4), $liOverVal));
									$liProcTime = IntVal($laResult[2]);
									$lsSugaCd   = $laResult[0];
								}else{
									$liOverAmt += $liSugaVal;
									$liProcTime = 0;
									$liOverVal  = 0;
									$lsSugaCd   = '';
								}

								$lsHisQuery[$row['t01_mkind']][$row['t01_jumin']][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']] = Array(
										'date'	=>$row['t01_conf_date']
									,	'from'	=>$row['t01_conf_fmtime']
									,	'to'	=>$row['t01_conf_totime']
									,	'time'	=>$row['t01_conf_soyotime']
									,	'suga'	=>$row['t01_conf_suga_code']
									,	'value'	=>$row['t01_conf_suga_value']
									,	'svcCd'	=>$row['t01_svc_subcode']
									,	'code'	=>$lsSugaCd
									,	'proc'	=>$liProcTime
									,	'val'	=>$liOverVal
								);
							}

							//총 수가금액
							$liSugaTot += $liSugaVal;
						}
					}else{
						if ($liMaxAmt < $liSugaTot + $liSugaVal){
							//총급여액보다 계획총금액이 넘어갔다.
							if ($liOverAmt == 0){
								$liTmpSuga  = $liMaxAmt - $liSugaTot;
								$liOverAmt  = $liSugaVal - $liTmpSuga;
								$liOverVal  = $liSugaVal - $liOverAmt;
								$laResult   = Explode(chr(1), $mySuga->getSugaTime($row['t01_conf_date'], SubStr($row['t01_conf_suga_code'],0,4), $liOverVal));
								$liProcTime = IntVal($laResult[2]);
								$lsSugaCd   = $laResult[0];
							}else{
								$liOverAmt += $liSugaVal;
								$liProcTime = 0;
								$liOverVal  = 0;
								$lsSugaCd   = '';
							}

							$lsHisQuery[$row['t01_mkind']][$row['t01_jumin']][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']] = Array(
									'date'	=>$row['t01_conf_date']
								,	'from'	=>$row['t01_conf_fmtime']
								,	'to'	=>$row['t01_conf_totime']
								,	'time'	=>$row['t01_conf_soyotime']
								,	'suga'	=>$row['t01_conf_suga_code']
								,	'value'	=>$row['t01_conf_suga_value']
								,	'svcCd'	=>$row['t01_svc_subcode']
								,	'code'	=>$lsSugaCd
								,	'proc'	=>$liProcTime
								,	'val'	=>$liOverVal
							);
						}

						//총 수가금액
						$liSugaTot += $liSugaVal;
					}
				}

				$conn->row_free();
			}
		}

		for($i=0; $i<$client_count; $i++){
			if ($client[$i]['kind'] == '0'){
				//2014.01.24 재가요양 쿼리를 본인부담율 정렬로 변경함
				$sql = 'select	client_his_kind.rate
						,		t01iljung.*
						from	t01iljung
						left	join client_his_kind
								on client_his_kind.org_no = t01_ccode
								and client_his_kind.jumin = t01_jumin
								and REPLACE(client_his_kind.from_dt,\'-\',\'\') <= t01_sugup_date
								and REPLACE(client_his_kind.to_dt,\'-\',\'\') >= t01_sugup_date
						where	t01_ccode               = \''.$client[$i]['code'].'\'
						and		t01_mkind               = \''.$client[$i]['kind'].'\'
						and		t01_jumin               = \''.$client[$i]['jumin'].'\'
						and		left(t01_sugup_date, 6) = \''.$year.$month.'\'
						and		t01_sugup_date    between \''.$client[$i]['date_from'].'\' and \''.$client[$i]['date_to'].'\'
						and		t01_del_yn              = \'N\'
						and		t01_status_gbn          = \'1\'
						order	by rate, t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime';
			}else{
				$sql = "select *
						  from t01iljung
						 where t01_ccode               = '".$client[$i]['code']."'
						   and t01_mkind               = '".$client[$i]['kind']."'
						   and t01_jumin               = '".$client[$i]['jumin']."'
						   and left(t01_sugup_date, 6) = '".$year.$month."'
						   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
						   and t01_del_yn              = 'N'
						   and t01_status_gbn          = '1'
						 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
			}

			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			$y = 0;
			$client_kind = '';

			// 수급자 일정 확정
			for($j=0; $j<$row_count; $j++){
				$row = $conn->select_row($j);

				//감액된 수가 적용
				//$row['t01_conf_suga_value'] = Round(($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01))) / 10) * 10;
				if ($year.$month >= '201401'){
					$liSugaVal = $row['t01_conf_suga_value'];
				}else{
					$liSugaVal = $myF->cutOff($row['t01_conf_suga_value'] - ($row['t01_conf_suga_value'] * (IntVal($row['t01_yoyangsa_id5']) * 0.01)));
				}

				if ($lsHisQuery[$row['t01_mkind']][$row['t01_jumin']][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']]){
					//한도 초과 일정의 확정금액을 한도에 맞게끔 수정한다.
					$row['t01_conf_suga_value'] = $lsHisQuery[$row['t01_mkind']][$row['t01_jumin']][$row['t01_sugup_date']][$row['t01_sugup_fmtime']][$row['t01_sugup_seq']]['val'];
				}

				if (is_array($laClientKind[$client[$i]['jumin']])){
					foreach($laClientKind[$client[$i]['jumin']] as $laRow){
						if ($laRow['form'] <= $row['t01_sugup_date'] &&
							$laRow['to']   >= $row['t01_sugup_date']){
							$liRate = $laRow['rate'];
							//echo $client[$i]['jumin'].'/'.$row['t01_sugup_date'].'/'.$laRow['form'].'/'.$laRow['to'].'/'.$liRate.'<br>';
							break;
						}
					}
				}else{
					$liRate = 100;
				}

				$liExpense = $row['t01_conf_suga_value'] * ($liRate / 100); //본인부담금액

				if ($year.$month >= '201401'){
					$liExpense = 0;
				}
				$liSugaVal = $row['t01_conf_suga_value'] - $liExpense; //본인부담금액을 제한 수가
				$liDownVal = $liSugaVal * (IntVal($row['t01_yoyangsa_id5']) * 0.01); //감액금액
				$liSugaVal = $row['t01_conf_suga_value'] - $liDownVal; //감액을 제한 수가

				// 수급자의 의료등급
				if ($client_kind != $liRate){
					$client_kind  = $liRate;

					//수급자 월중간 정보 변경시 다음 내역에 총 서비스금액을 넘긴다.
					$tmpSugaP = 0;
					$tmpSugaC = 0;

					if (Is_Array($p)){
						foreach($p as $tmpArr){
							if ($tmpArr['jumin'] == $row['t01_jumin']){
								$tmpSugaP = $tmpArr['sugaTotal'];
								break;
							}
						}
					}

					if (Is_Array($c)){
						foreach($c as $tmpArr){
							if ($tmpArr['jumin'] == $row['t01_jumin']){
								$tmpSugaC = $tmpArr['sugaTotal'];
								break;
							}
						}
					}

					// 계획 데이타 초기화
					$p[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $liRate, '1', $client[$i]['kupyeo_max'], $client[$i]['limitCare'], $client[$i]['limitBath'], $client[$i]['limitNurse']);

					// 실적 데이타 초기화
					$c[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $liRate, '2', $client[$i]['kupyeo_max'], $client[$i]['limitCare'], $client[$i]['limitBath'], $client[$i]['limitNurse']);

					//수급자 월중간 금액을 넘겨받는다.
					$p[$y]['sugaTotal'] = $tmpSugaP;
					$c[$y]['sugaTotal'] = $tmpSugaC;

					$y ++;
					$k = $y - 1;

					// 계획총금액
					$p[$k]['realTotal'] = get_total_amt($conn, 't01_suga', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to'], $year.$month);

					// 확정총금액
					$c[$k]['realTotal'] = get_total_amt($conn, 't01_conf_suga_value', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to'], $year.$month);
				}

				switch($row['t01_svc_subcode']){
					case '200':
						$svcIndex = '1';
						break;
					case '500':
						$svcIndex = '2';
						break;
					case '800':
						$svcIndex = '3';
						break;
				}

				//계획 총 수가
				$p[$k]['sugaTot'.$svcIndex] = $p[$k]['sugaTot'.$svcIndex] + $row['t01_suga'];

				// 감액
				$p[$k]['downAmt'.$svcIndex] = 0;

				//추가금
				if ($p[$k]['maxAmt'] < $p[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0)){
					//총급여액보다 계획총금액이 넘어갔다.
					if ($p[$k]['overAmt'] == 0){
						$tempSuga = $p[$k]['maxAmt'] - $p[$k]['sugaTotal'];
						$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + $tempSuga * ($liRate / 100);
						$p[$k]['overAmt'.$svcIndex] = $row['t01_suga'] - $tempSuga;
					}else{
						$p[$k]['overAmt'.$svcIndex] = $p[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);
					}
					$p[$k]['overAmt'] = $p[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);
				}else{
					//계획 본인부담금
					$p[$k]['boninAmt'.$svcIndex] = $p[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0) * ($liRate / 100));
				}

				//총 수가금액
				$p[$k]['sugaTotal'] = $p[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_suga'] : 0);

				//비급여
				$p[$k]['biPay'.$svcIndex] = $p[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_suga'] : 0);


				if ($year.$month >= '201401'){
					$liSugaVal = Round($liSugaVal / 10) * 10;
					$row['t01_conf_suga_value'] = $liSugaVal;
				}

				if ($row['t01_conf_soyotime'] > (($row['t01_svc_subcode'] == '200') ? 29 : 0)){
					//확정 총 수가
					//$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
					$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + $row['t01_conf_suga_value'];

					#if ($debug){
						$c[$k]['valTot'.$svcIndex] = $c[$k]['valTot'.$svcIndex] + $liSugaVal;
					#}else{
					#	$c[$k]['valTot'.$svcIndex] = $c[$k]['valTot'.$svcIndex] + $row['t01_conf_suga_value'];
					#}

					// 2011년 7월부터 감액적용
					if (($year.$month) >= '201107' && $row['t01_svc_subcode'] == '500'){
						// 2011년 7월 부터 목욕 시간비율 감액 금액을 추가한다.
						if ($row['t01_conf_soyotime'] < 40){
							// 40분 미만시 전액 감액한다.
							//$c[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'];
						}else if ($row['t01_conf_soyotime'] >= 40 && $row['t01_conf_soyotime'] < 60){
							// 40분이상 60분 미만시 80%만 산정한다.
							//$c[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'] * 0.2;
						}else{
							// 60분이상시 감액은 없다.
							//$c[$k]['downAmt'.$svcIndex] = 0;
						}
					}

					//감액금액
					$c[$k]['downAmt'.$svcIndex] += FloatVal($liDownVal);

					//추가금
					if ($lbLimitSet){
						if ($c[$k]['limitAmt']['200'] > 0 ||
							$c[$k]['limitAmt']['500'] > 0 ||
							$c[$k]['limitAmt']['800'] > 0){

							#if ($row['t01_jumin'] == '3501202142455'){
							#	echo $c[$k]['limitAmt'][$row['t01_svc_subcode']].' / '
							#		.$c[$k]['svcSuga'][$row['t01_svc_subcode']].' / '
							#		.$row['t01_conf_suga_value'].' / '
							#		.$liSugaVal.' / '
							#		.$laTmpSuga[$row['t01_svc_subcode']].' / '
							#		.chr(13).chr(10);
							#}

							if ($c[$k]['limitAmt'][$row['t01_svc_subcode']] < $c[$k]['svcSuga'][$row['t01_svc_subcode']] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0)){
								//총급여액보다 계획총금액이 넘어갔다.
								if ($c[$k]['overAmt'] == 0){
									$laTmpSuga[$row['t01_svc_subcode']] = $c[$k]['limitAmt'][$row['t01_svc_subcode']] - $c[$k]['svcSuga'][$row['t01_svc_subcode']];

									$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ($laTmpSuga[$row['t01_svc_subcode']] * ($liRate / 100));
									$c[$k]['overAmt'.$svcIndex] = (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) - $laTmpSuga[$row['t01_svc_subcode']];
								}else{
									$c[$k]['overAmt'.$svcIndex] = $c[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
								}
								$c[$k]['overAmt'] = $c[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
							}else{
								//확정 본인부담금
								$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) * ($liRate / 100));
							}

							//총 수가금액
							//$c[$k]['sugaTotal'] = $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
							$c[$k]['svcSuga'][$row['t01_svc_subcode']] = $c[$k]['svcSuga'][$row['t01_svc_subcode']] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);

							//비급여
							$c[$k]['biPay'.$svcIndex] = $c[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_conf_suga_value'] : 0);
						}else{
							if ($c[$k]['maxAmt'] < $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0)){
								//총급여액보다 계획총금액이 넘어갔다.
								if ($c[$k]['overAmt'] == 0){
									$tempSuga = $c[$k]['maxAmt'] - $c[$k]['sugaTotal'];

									$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ($tempSuga * ($liRate / 100));
									$c[$k]['overAmt'.$svcIndex] = (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) - $tempSuga;
								}else{
									$c[$k]['overAmt'.$svcIndex] = $c[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
								}
								$c[$k]['overAmt'] = $c[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
							}else{
								//확정 본인부담금
								$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) * ($liRate / 100));
							}

							//총 수가금액
							$c[$k]['sugaTotal'] = $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);

							//비급여
							$c[$k]['biPay'.$svcIndex] = $c[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_conf_suga_value'] : 0);
						}
					}else{
						if ($c[$k]['maxAmt'] < $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0)){
							//총급여액보다 계획총금액이 넘어갔다.
							if ($c[$k]['overAmt'] == 0){
								$tempSuga = $c[$k]['maxAmt'] - $c[$k]['sugaTotal'];

								$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ($tempSuga * ($liRate / 100));
								$c[$k]['overAmt'.$svcIndex] = (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) - $tempSuga;
							}else{
								$c[$k]['overAmt'.$svcIndex] = $c[$k]['overAmt'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
							}
							$c[$k]['overAmt'] = $c[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
						}else{
							//확정 본인부담금
							$c[$k]['boninAmt'.$svcIndex] = $c[$k]['boninAmt'.$svcIndex] + ((($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0) * ($liRate / 100));
						}

						//총 수가금액
						$c[$k]['sugaTotal'] = $c[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);

						//비급여
						$c[$k]['biPay'.$svcIndex] = $c[$k]['biPay'.$svcIndex] + (($row['t01_bipay_umu'] == 'Y') ? $row['t01_conf_suga_value'] : 0);
					}
				}
			}

			$conn->row_free();

			if ($row_count > 0){
				//$bill_no = get_billno($bill_no);
				$bill_no = $myF->zero_str($client[$i]['client_key'], 6);

				$p_count = sizeOf($p);

				for($k=0; $k<$p_count; $k++){
					$p[$k]['overAmt'] = $p[$k]['overAmt1'] + $p[$k]['overAmt2'] + $p[$k]['overAmt3'];

					$p[$k]['boninAmt1'] = $myF->cutOff($p[$k]['boninAmt1']);
					$p[$k]['boninAmt2'] = $myF->cutOff($p[$k]['boninAmt2']);
					$p[$k]['boninAmt3'] = $myF->cutOff($p[$k]['boninAmt3']);

					$p[$k]['bonbuTot1'] = $myF->cutOff($p[$k]['boninAmt1'] + $p[$k]['overAmt1'] + $p[$k]['biPay1']);
					$p[$k]['bonbuTot2'] = $myF->cutOff($p[$k]['boninAmt2'] + $p[$k]['overAmt2'] + $p[$k]['biPay2']);
					$p[$k]['bonbuTot3'] = $myF->cutOff($p[$k]['boninAmt3'] + $p[$k]['overAmt3'] + $p[$k]['biPay3']);

					$p[$k]['chungAmt1'] = $p[$k]['sugaTot1'] - $p[$k]['bonbuTot1'];
					$p[$k]['chungAmt2'] = $p[$k]['sugaTot2'] - $p[$k]['bonbuTot2'];
					$p[$k]['chungAmt3'] = $p[$k]['sugaTot3'] - $p[$k]['bonbuTot3'];

					$p[$k]['sugaTot4']  = $p[$k]['sugaTot1']  + $p[$k]['sugaTot2']  + $p[$k]['sugaTot3'];
					$p[$k]['boninAmt4'] = $p[$k]['boninAmt1'] + $p[$k]['boninAmt2'] + $p[$k]['boninAmt3'];
					$p[$k]['overAmt4']  = $p[$k]['overAmt1']  + $p[$k]['overAmt2']  + $p[$k]['overAmt3'];
					$p[$k]['biPay4']    = $p[$k]['biPay1']    + $p[$k]['biPay2']    + $p[$k]['biPay3'];
					$p[$k]['bonbuTot4'] = $p[$k]['bonbuTot1'] + $p[$k]['bonbuTot2'] + $p[$k]['bonbuTot3'];
					$p[$k]['chungAmt4'] = $p[$k]['chungAmt1'] + $p[$k]['chungAmt2'] + $p[$k]['chungAmt3'];

					$p[$k]['downAmt4'] = $p[$k]['downAmt1'] + $p[$k]['downAmt2'] + $p[$k]['downAmt3'];

					$p[$k]['resultAmt'] = $p[$k]['maxAmt'] - $p[$k]['sugaTot4'];
					$p[$k]['billNo']    = $bill_no;
				}

				$c_count = sizeOf($c);

				for($k=0; $k<$c_count; $k++){
					$c[$k]['overAmt'] = $c[$k]['overAmt1'] + $c[$k]['overAmt2'] + $c[$k]['overAmt3'];

					if ($debug){
						$c[$k]['sugaTot1'] = $myF->cutOff($c[$k]['sugaTot1']);
						$c[$k]['sugaTot2'] = $myF->cutOff($c[$k]['sugaTot2']);
						$c[$k]['sugaTot3'] = $myF->cutOff($c[$k]['sugaTot3']);

						$c[$k]['boninAmt1'] = $myF->cutOff($c[$k]['boninAmt1']);
						$c[$k]['boninAmt2'] = $myF->cutOff($c[$k]['boninAmt2']);
						$c[$k]['boninAmt3'] = $myF->cutOff($c[$k]['boninAmt3']);

						$c[$k]['bonbuTot1'] = $myF->cutOff($c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1']);
						$c[$k]['bonbuTot2'] = $myF->cutOff($c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2']);
						$c[$k]['bonbuTot3'] = $myF->cutOff($c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3']);

						$c[$k]['chungAmt1'] = $myF->cutOff($c[$k]['valTot1']) - $c[$k]['bonbuTot1'];
						$c[$k]['chungAmt2'] = $myF->cutOff($c[$k]['valTot2']) - $c[$k]['bonbuTot2'];
						$c[$k]['chungAmt3'] = $myF->cutOff($c[$k]['valTot3']) - $c[$k]['bonbuTot3'];
					}else{
						$c[$k]['sugaTot1'] = $myF->cutOff($c[$k]['sugaTot1']);
						$c[$k]['sugaTot2'] = $myF->cutOff($c[$k]['sugaTot2']);
						$c[$k]['sugaTot3'] = $myF->cutOff($c[$k]['sugaTot3']);

						$c[$k]['boninAmt1'] = $myF->cutOff($c[$k]['boninAmt1']);
						$c[$k]['boninAmt2'] = $myF->cutOff($c[$k]['boninAmt2']);
						$c[$k]['boninAmt3'] = $myF->cutOff($c[$k]['boninAmt3']);

						$c[$k]['bonbuTot1'] = $myF->cutOff($c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1']);
						$c[$k]['bonbuTot2'] = $myF->cutOff($c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2']);
						$c[$k]['bonbuTot3'] = $myF->cutOff($c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3']);

						$c[$k]['chungAmt1'] = $myF->cutOff($c[$k]['valTot1']) - $c[$k]['bonbuTot1'];
						$c[$k]['chungAmt2'] = $myF->cutOff($c[$k]['valTot2']) - $c[$k]['bonbuTot2'];
						$c[$k]['chungAmt3'] = $myF->cutOff($c[$k]['valTot3']) - $c[$k]['bonbuTot3'];
					}

					$c[$k]['sugaTot4']  = $c[$k]['sugaTot1']  + $c[$k]['sugaTot2']  + $c[$k]['sugaTot3'];
					$c[$k]['boninAmt4'] = $c[$k]['boninAmt1'] + $c[$k]['boninAmt2'] + $c[$k]['boninAmt3'];
					$c[$k]['overAmt4']  = $c[$k]['overAmt1']  + $c[$k]['overAmt2']  + $c[$k]['overAmt3'];
					$c[$k]['biPay4']    = $c[$k]['biPay1']    + $c[$k]['biPay2']    + $c[$k]['biPay3'];
					$c[$k]['bonbuTot4'] = $c[$k]['bonbuTot1'] + $c[$k]['bonbuTot2'] + $c[$k]['bonbuTot3'];
					$c[$k]['chungAmt4'] = $c[$k]['chungAmt1'] + $c[$k]['chungAmt2'] + $c[$k]['chungAmt3'];

					$c[$k]['downAmt4'] = $c[$k]['downAmt1'] + $c[$k]['downAmt2'] + $c[$k]['downAmt3'];

					$c[$k]['resultAmt'] = $c[$k]['maxAmt'] - $c[$k]['sugaTot4'];

					if ($debug){
						$c[$k]['misuAmt']   = $myF->cutOff($c[$k]['bonbuTot4'] - $c[$k]['downAmt4']);
					}else{
						$c[$k]['misuAmt']   = $myF->cutOff($c[$k]['bonbuTot4'] - $c[$k]['downAmt4']);
					}

					$c[$k]['billNo']    = $bill_no;
				}

				$conf_data[$conf_index]['p'] = $p;
				$conf_data[$conf_index]['c'] = $c;

				$conf_index ++;
			}

			if (is_array($p)) unset($p);
			if (is_array($c)) unset($c);
		}

		unset($client);
	#
	#############################################################




	#############################################################
	#
	# 바우처 데이타
		$sql = 'select distinct t.code as code
				,      t.svc_cd as kind
				,      t.jumin as jumin
				,      t.name as name
				,      t.val1 as vlvl
				,      t.cost * t.cnt as kupyeo_max
				,      t.expense as kupyeo_1
				,      date_format(t.from_dt,\'%Y%m%d\') as date_from
				,      date_format(t.to_dt  ,\'%Y%m%d\') as date_to
				,      t.c_key as client_key
				,      voucher_totaltime as tot_time
				,      voucher_totaltime * voucher_suga_cost as tot_amt
				,      voucher_suga_cost as cost
				,      voucher_overtime + voucher_maketime as maketime
				,      voucher_overpay + voucher_makepay as makepay
				  from (
						select mst.code
						,      mst.jumin
						,      mst.name
						,      mst.c_key
						,      lvl.level
						,      /*his.svc_cd*/
							   CASE svc.tbl_id WHEN \'OTHER_A\' THEN \'A\'
											   WHEN \'OTHER_B\' THEN \'B\'
											   WHEN \'OTHER_C\' THEN \'C\' ELSE his.svc_cd END AS svc_cd
						,      ifnull(svc.val1,\'\') as val1
						,      ifnull(svc.val2,\'\') as val2
						,      ifnull(svc.cnt,0) as cnt
						,      ifnull(suga.cost,0) as cost
						,      his.from_dt
						,      his.to_dt
						,      case lvl.level when 1 then person.amt1
											  when 2 then person.amt2
											  when 3 then person.amt3
											  when 4 then person.amt4
											  when 5 then person.amt5
											  when 6 then person.amt6 else 0 end as expense
						  from (
							   select m03_ccode as code
							   ,      min(m03_mkind) as kind
							   ,      m03_jumin as jumin
							   ,      m03_name as name
							   ,      m03_key as c_key
								 from m03sugupja
								where m03_ccode = \''.$code.'\'';

						if ($isPerson){
							$sql .= ' AND m03_jumin = \''.$calTarget.'\'';
						}

						$sql .= ' group by m03_jumin
							   ) as mst
						 inner join (
							   select org_no
							   ,      jumin
							   ,      svc_cd
							   ,      seq
							   ,      from_dt
							   ,      to_dt
								 from client_his_svc
								where org_no = \''.$code.'\'
								  and svc_cd >= \'1\'
								  and svc_cd != \'4\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as his
							on his.jumin = mst.jumin
						  left join (
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      svc_val as val1
							   ,      \'\' as val2
							   ,      case svc_val when \'1\' then 18
												   when \'2\' then 24 else 0 end as cnt
							   ,      \'NURSE\' as tbl_id
								 from client_his_nurse
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
								union all
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      case svc_val when \'1\' then \'V\'
												   when \'2\' then \'D\' else \'\' end
							   ,      svc_tm
							   ,      case svc_val when \'1\' then
															  case svc_tm when \'1\' then 27
																		  when \'2\' then 36 else 0 end
												   when \'2\' then
															case svc_tm when \'1\' then 9
																		when \'2\' then 12 else 0 end
												   else 0 end
							   ,      \'OLD\'
								 from client_his_old
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
								union all
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      svc_val
							   ,      \'\'
							   ,      case svc_val when \'1\' then 12
												   when \'2\' then 18
												   when \'3\' then 24 else 0 end
							   ,      \'BABY\'
								 from client_his_baby
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
								union all
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      \'\'
							   ,      \'\'
							   ,      0
							   ,      \'OTHER_A\'
								 from client_his_other
								where org_no = \''.$code.'\'
								  and svc_cd = \'A\'
								union all
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      \'\'
							   ,      \'\'
							   ,      0
							   ,      \'OTHER_B\'
								 from client_his_other
								where org_no = \''.$code.'\'
								  and svc_cd = \'B\'
								union all
							   select org_no
							   ,      jumin
							   ,      seq
							   ,      \'\'
							   ,      \'\'
							   ,      0
							   ,      \'OTHER_C\'
								 from client_his_other
								where org_no = \''.$code.'\'
								  and svc_cd = \'C\'
							   ) as svc
							on svc.jumin  = his.jumin
						   and svc.tbl_id = concat(\'OTHER_\',his.svc_cd)
						   and case svc.tbl_id when \'OTHER_A\' then svc.seq
											   when \'OTHER_B\' then svc.seq
											   when \'OTHER_C\' then svc.seq else his.seq end = his.seq
						  left join (
							   select jumin
							   ,      svc_cd
							   ,      level
								 from client_his_lvl
								where org_no = \''.$code.'\'
								  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as lvl
							on lvl.jumin = his.jumin
						   and lvl.svc_cd = his.svc_cd
						  left join (
							   select service_kind as kind
							   ,      service_code as cd
							   ,      service_gbn
							   ,      service_lvl
							   ,      service_cost as cost
							   ,      service_conf_time as cnt
								 from suga_service
								where org_no = \'goodeos\'
								  and date_format(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as suga
							on suga.kind = his.svc_cd
						   and suga.cd   = case his.svc_cd when \'1\' then \'VH001\'
														   when \'2\' then concat(\'VO\',svc.val1,\'01\')
														   when \'3\' then concat(\'VM\',svc.val1,\'01\') else \'\' end
						  left join (
							   select person_code as cd
							   ,      person_id as id
							   ,      person_amt1 as amt1
							   ,      person_amt2 as amt2
							   ,      person_amt3 as amt3
							   ,      person_amt4 as amt4
							   ,      person_amt5 as amt5
							   ,      person_amt6 as amt6
								 from suga_person
								where org_no = \'goodeos\'
								  and date_format(person_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								  and date_format(person_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							   ) as person
							on left(person.cd,3) = case his.svc_cd when \'1\' then \'VH0\'
																   when \'2\' then concat(\'VO\',svc.val1)
																   when \'3\' then \'VM0\' else \'\' end
						   and person.id = case his.svc_cd when \'1\' then svc.val1
														   when \'2\' then svc.val2
														   when \'3\' then svc.val1 else \'\' end
					   ) as t
				  left join voucher_make as vou
					on org_no        = t.code
				   and voucher_kind  = t.svc_cd
				   and voucher_jumin = t.jumin
				   and voucher_yymm  = \''.$year.$month.'\'
				   and del_flag      = \'N\'
				 order by t.jumin, t.svc_cd';

		//if ($debug){
		//	echo nl2br($sql);
		//	exit;
		//}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$client[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$client_count = sizeof($client);
		$k = 0;

		for($i=0; $i<$client_count; $i++){
			$sql = "select *
					  from t01iljung
					 where t01_ccode               = '".$client[$i]['code']."'
					   and t01_mkind               = '".$client[$i]['kind']."'
					   and t01_jumin               = '".$client[$i]['jumin']."'
					   and left(t01_sugup_date, 6) = '".$year.$month."'
					   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
					   and t01_conf_soyotime       > '0'
					   and t01_del_yn              = 'N'
					 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			if ($row_count > 0){
				$vou[$k]['code']     = $client[$i]['code'];       //기관코드
				$vou[$k]['kind']     = $client[$i]['kind'];       //바우처분류
				$vou[$k]['yymm']     = $year.$month;              //실적년월
				$vou[$k]['jumin']    = $client[$i]['jumin'];      //주민번호
				$vou[$k]['key']      = $client[$i]['client_key']; //키
				$vou[$k]['maketime'] = $client[$i]['maketime'];   //생성시간
				$vou[$k]['makepay']  = $client[$i]['makepay'];    //생성금액
				$vou[$k]['my_pay']   = 0;
				$vou[$k]['add_pay']  = 0;

				if ($client[$i]['kind'] >= '1' && $client[$i]['kind'] <= '4'){
					$vou[$k]['cost'] = $client[$i]['cost']; //단가

					#############################################################################
					# 가사간병 및 산모신생아는 본인부담금을 관리한다.
					if ($client[$i]['kind'] == '1' || $client[$i]['kind'] == '3')
						$vou[$k]['my_pay'] = $client[$i]['kupyeo_1'];
					#############################################################################
				}else{
					$vou[$k]['cost'] = $client[$i]['kupyeo_1']; //단가
				}

				if ($client[$i]['kind'] == '2' && $client[$i]['vlvl'] == 'D'){
					$vou[$k]['tot_time'] = $client[$i]['tot_time'] * 3 * 60; //노인돌봄 주간은 하루 3시간으로 계산
					$vou[$k]['maketime'] = $vou[$k]['maketime'] * 3 * 60;
					$vou[$k]['cal_time'] = 3;
					$vou[$k]['str_time'] = 'date';
				}else if ($client[$i]['kind'] == '3'){
					$vou[$k]['tot_time'] = $client[$i]['tot_time'] * 8 * 60; //산모신생아는 하루 8시간으로 계산
					$vou[$k]['maketime'] = $vou[$k]['maketime'] * 8 * 60;
					$vou[$k]['cal_time'] = 8;
					$vou[$k]['str_time'] = 'date';
				}else{
					$vou[$k]['tot_time'] = $client[$i]['tot_time'] * 60; //가능총시간(분으로 전환)
					$vou[$k]['maketime'] = $vou[$k]['maketime'] * 60;
					$vou[$k]['cal_time'] = 1;
					$vou[$k]['str_time'] = 'time';
				}

				$vou[$k]['tot_amt'] = $client[$i]['tot_amt'];       //가능총금액

				for($j=0; $j<$row_count; $j++){
					$row = $conn->select_row($j);


					/*********************************************************
						추가금액
					*********************************************************/
						if ($row['t01_sugup_yoil'] == '0' || $row['t01_sugup_yoil'] == '7'){
							$vou[$k]['add_pay'] += $row['t01_holiday_cost'];
						}

						$vou[$k]['add_pay'] += $row['t01_not_school_pay'];
						$vou[$k]['add_pay'] += $row['t01_school_pay'];
						$vou[$k]['add_pay'] += $row['t01_family_pay'];
						$vou[$k]['add_pay'] += $row['t01_home_in_cost'];
					/********************************************************/


					if ($row['t01_mkind'] > '0' && $row['t01_mkind'] < '10'){
						$bipya_yn = $row['t01_bipay_umu'];
					}else{
						$bipya_yn = 'Y';
					}

					if ($bipya_yn != 'Y'){
						if ($vou[$k]['use_time'] + $row['t01_conf_soyotime'] > $vou[$k]['tot_time']){
							if ($vou[$k]['use_time'] != $vou[$k]['tot_time']){
								$tmp_time = $vou[$k]['use_time'] + $row['t01_conf_soyotime']   - $vou[$k]['tot_time'];
								$tmp_amt  = $vou[$k]['use_amt']  + $row['t01_conf_suga_value'] - $vou[$k]['tot_amt'];

								$vou[$k]['use_time'] = $vou[$k]['tot_time'];
								$vou[$k]['use_amt']  = $vou[$k]['tot_amt'];
							}else{
								$tmp_time = $row['t01_conf_soyotime'];
								$tmp_amt  = $row['t01_conf_suga_value'];
							}

							$vou[$k]['over_time'] += $tmp_time;
							$vou[$k]['over_amt']  += $tmp_amt;
						}else{
							$vou[$k]['use_time'] += $row['t01_conf_soyotime'];
							$vou[$k]['use_amt']  += $row['t01_conf_suga_value'];
						}
					}else{
						$vou[$k]['bi_time'] += $row['t01_conf_soyotime'];
						$vou[$k]['bi_amt']  += $row['t01_conf_suga_value'];
					}
				}

				// 남은시간
				$vou[$k]['savetime'] = $vou[$k]['maketime'] - $vou[$k]['use_time'];
				$vou[$k]['savepay']  = $vou[$k]['makepay']  - $vou[$k]['use_amt'];

				if ($vou[$k]['savetime'] < 0) $vou[$k]['savetime'] = 0;
				if ($vou[$k]['savepay']  < 0) $vou[$k]['savepay']  = 0;

				$k ++;
			}

			$conn->row_free();
		}

		unset($client);
	#
	#############################################################



	#############################################################
	#
	#	장애인활동지원
		$sql = 'select t.code as code
				,      t.svc_cd as kind
				,      t.jumin as jumin
				,      t.name as name
				,      t.val1 as vlvl
				,      t.val2 as ylvl
				,      0 as kupyeo_max
				,      0 as kupyeo_1
				,      date_format(t.from_dt,\'%Y%m%d\') as date_from
				,      date_format(t.to_dt  ,\'%Y%m%d\') as date_to
				,      t.c_key as client_key
				,      voucher_totaltime as tot_time
				,      voucher_totaltime * voucher_suga_cost as tot_amt
				,      voucher_suga_cost as cost
				,      voucher_overpay as overpay
				,      voucher_makepay as makepay
				,      voucher_addpay as addpay
				,      voucher_addtime1 * voucher_suga_cost as sidopay
				,      voucher_addtime2 * voucher_suga_cost as jachpay
				  from (
					   select mst.code
					   ,      mst.jumin
					   ,      mst.name
					   ,      mst.c_key
					   ,      lvl.level
					   ,      his.svc_cd
					   ,      concat(\'VA\',svc.val1,svc.val2,\'0\')
					   ,      ifnull(svc.val1,\'\') as val1
					   ,      ifnull(svc.val2,\'\') as val2
					   ,      ifnull(suga.cost,0) as cost
					   ,      his.from_dt
					   ,      his.to_dt
					   ,      case lvl.level when 1 then person.amt1
											 when 2 then person.amt2
											 when 3 then person.amt3
											 when 4 then person.amt4
											 when 5 then person.amt5
											 when 6 then person.amt6 else 0 end as expense
					   ,      income.rate
					   ,      income.pay
						 from (
							  select m03_ccode as code
							  ,      min(m03_mkind) as kind
							  ,      m03_jumin as jumin
							  ,      m03_name as name
							  ,      m03_key as c_key
								from m03sugupja
							   where m03_ccode = \''.$code.'\'';

						if ($isPerson){
							$sql .= ' AND m03_jumin = \''.$calTarget.'\'';
						}

						$sql .=' group by m03_jumin
							  ) as mst
						inner join (
							  select org_no
							  ,      jumin
							  ,      svc_cd
							  ,      seq
							  ,      from_dt
							  ,      to_dt
								from client_his_svc
							   where org_no = \''.$code.'\'
								 and svc_cd = \'4\'
								 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as his
						   on his.jumin = mst.jumin
						 left join (
							  select org_no
							  ,      jumin
							  ,      seq
							  ,      case svc_val when \'1\' then \'A\'
												  when \'2\' then \'C\' else \'\' end as val1
							  ,      svc_lvl as val2
								from client_his_dis
							   where org_no = \''.$code.'\'
								 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as svc
						   on svc.jumin = his.jumin
						 left join (
							  select jumin
							  ,      svc_cd
							  ,      level
								from client_his_lvl
							   where org_no = \''.$code.'\'
								 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as lvl
						   on lvl.jumin = his.jumin
						  and lvl.svc_cd = his.svc_cd
						 left join (
							  select service_kind as kind
							  ,      service_code as cd
							  ,      service_gbn
							  ,      service_lvl
							  ,      service_cost as cost
							  ,      service_conf_time as cnt
							  ,      service_conf_amt as amt
								from suga_service
							   where org_no = \'goodeos\'
								 and date_format(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as suga
						   on suga.kind = his.svc_cd
						  and suga.cd   = concat(\'VA\',svc.val1,svc.val2,\'0\')
						 left join (
							  select person_code as cd
							  ,      person_id as id
							  ,      person_amt1 as amt1
							  ,      person_amt2 as amt2
							  ,      person_amt3 as amt3
							  ,      person_amt4 as amt4
							  ,      person_amt5 as amt5
							  ,      person_amt6 as amt6
								from suga_person
							   where org_no = \'goodeos\'
								 and date_format(person_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(person_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as person
						   on left(person.cd,3) = \'VA0\'
						  and person.id = CASE WHEN svc.val1 = \'A\' THEN
													CASE svc.val2 WHEN \'1\' THEN \'4\'
																  WHEN \'2\' THEN \'3\'
																  WHEN \'3\' THEN \'2\'
																  WHEN \'4\' THEN \'1\' ELSE svc.val2 END
											   ELSE svc.val2 END
						 left join (
							  select lvl_id as id
							  ,      lvl_rate as rate
							  ,      lvl_pay as pay
								from income_lvl_self_pay
							   where lvl_kind = \'4\'
								 and lvl_gbn  = \'2\'
								 and date_format(lvl_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
								 and date_format(lvl_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							  ) as income
						   on income.id = lvl.level
					   ) as t
				  left join voucher_make as vou
					on org_no        = t.code
				   and voucher_kind  = t.svc_cd
				   and voucher_jumin = t.jumin
				   and voucher_yymm  = \''.$year.$month.'\'
				   and del_flag      = \'N\'
				 order by t.jumin, t.svc_cd';

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$client[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$client_count = sizeof($client);
		$k = 0;

		for($i=0; $i<$client_count; $i++){
			$sql = "select *
					  from t01iljung
					 where t01_ccode               = '".$client[$i]['code']."'
					   and t01_mkind               = '".$client[$i]['kind']."'
					   and t01_jumin               = '".$client[$i]['jumin']."'
					   and left(t01_sugup_date, 6) = '".$year.$month."'
					   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
					   and t01_conf_soyotime       > '0'
					   and t01_del_yn              = 'N'
					 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			// 수급자 일정 확정
			for($j=0; $j<$row_count; $j++){
				$row = $conn->select_row($j);

				#배열초기화
				if ($tmp_jumin != $row['t01_jumin']){
					$tmp_jumin  = $row['t01_jumin'];
					$k = sizeof($dis);
					$dis[$k] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $client[$i]['ylvl'], '2', $client[$i]['kupyeo_max']);
					$dis[$k]['savepay']   = $client[$i]['makepay']+$client[$i]['addpay']+$client[$i]['overpay'];
					$dis[$k]['str_time']  = 'time';
					$dis[$k]['str_other'] = 'overpay='.$client[$i]['overpay'].'&makepay='.$client[$i]['makepay'].'&addpay='.$client[$i]['addpay'].'&sidopay='.$client[$i]['sidopay'].'&jachpay='.$client[$i]['jachpay'];
				}


				#서비스인덱스
				switch($row['t01_svc_subcode']){
					case '200':
						$svcIndex = '1';
						break;
					case '500':
						$svcIndex = '2';
						break;
					case '800':
						$svcIndex = '3';
						break;
				}


				#확정 총 수가
				$dis[$k]['sugaTot'.$svcIndex] = $dis[$k]['sugaTot'.$svcIndex] + $row['t01_conf_suga_value'];

				// 목욕 시간비율 감액 금액을 추가한다.
				if ($row['t01_conf_soyotime'] < 40){
					// 40분 미만시 전액 감액한다.
					$dis[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'];
				}else if ($row['t01_conf_soyotime'] >= 40 && $row['t01_conf_soyotime'] < 60){
					// 40분이상 60분 미만시 80%만 산정한다.
					$dis[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'] * 0.2;
				}else{
					// 60분이상시 감액은 없다.
					$dis[$k]['downAmt'.$svcIndex] = 0;
				}

				//추가금
				#if ($dis[$k]['maxAmt'] < $dis[$k]['sugaTotal'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0)){
					//총급여액보다 계획총금액이 넘어갔다.
					#if ($dis[$k]['overAmt'] == 0){
					#	$tempSuga = $dis[$k]['maxAmt'] - $dis[$k]['sugaTotal'];
						#$dis[$k]['boninAmt'.$svcIndex] = $dis[$k]['boninAmt'.$svcIndex] + $tempSuga;
					#	$dis[$k]['overAmt'.$svcIndex] = ($row['t01_bipay_umu'] != 'Y' ? $row['t01_conf_suga_value'] : 0) - $tempSuga;
					#}else{
					#	$dis[$k]['overAmt'.$svcIndex] = $dis[$k]['overAmt'.$svcIndex] + ($row['t01_bipay_umu'] != 'Y' ? $row['t01_conf_suga_value'] : 0);
					#}
					#$dis[$k]['overAmt'] = $dis[$k]['overAmt'] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
				#}else{
					#확정 본인부담금
					#$dis[$k]['boninAmt'.$svcIndex] = $dis[$k]['boninAmt'.$svcIndex] + ($row['t01_bipay_umu'] != 'Y' ? $row['t01_conf_suga_value'] : 0);
				#}


				if ($row['t01_bipay_umu'] != 'Y'){
					//총 수가금액
					$dis[$k]['sugaTotal'] = $dis[$k]['sugaTotal'] + $row['t01_conf_suga_value'];

					// 남은금액
					$dis[$k]['savepay'] -= $row['t01_conf_suga_value'];
				}else{
					//비급여
					$dis[$k]['biPay'.$svcIndex] = $dis[$k]['biPay'.$svcIndex] + $row['t01_conf_suga_value'];
				}


				if ($dis[$k]['savepay'] < 0){
					$dis[$k]['savepay']  = 0;
					$dis[$k]['savetime'] = 0;
				}else{
					if($row['t01_bipay_umu'] != 'Y'){
						$dis[$k]['savetime'] = floor($dis[$k]['savepay'] / $client[$i]['cost']);
					}
				}


			}

			$conn->row_free();

			if ($row_count > 0){
				//$bill_no = get_billno($bill_no);
				$bill_no = $myF->zero_str($client[$i]['client_key'], 6);

				$c_count = sizeOf($dis);

				for($k=0; $k<$c_count; $k++){
					$dis[$k]['overAmt'] = $dis[$k]['overAmt1'] + $dis[$k]['overAmt2'] + $dis[$k]['overAmt3'];

					$dis[$k]['boninAmt1'] = $myF->cutOff($dis[$k]['boninAmt1']);
					$dis[$k]['boninAmt2'] = $myF->cutOff($dis[$k]['boninAmt2']);
					$dis[$k]['boninAmt3'] = $myF->cutOff($dis[$k]['boninAmt3']);

					$dis[$k]['bonbuTot1'] = $myF->cutOff($dis[$k]['boninAmt1'] + $dis[$k]['overAmt1'] + $dis[$k]['biPay1']);
					$dis[$k]['bonbuTot2'] = $myF->cutOff($dis[$k]['boninAmt2'] + $dis[$k]['overAmt2'] + $dis[$k]['biPay2']);
					$dis[$k]['bonbuTot3'] = $myF->cutOff($dis[$k]['boninAmt3'] + $dis[$k]['overAmt3'] + $dis[$k]['biPay3']);

					$dis[$k]['chungAmt1'] = $dis[$k]['sugaTot1'] - $dis[$k]['bonbuTot1'];
					$dis[$k]['chungAmt2'] = $dis[$k]['sugaTot2'] - $dis[$k]['bonbuTot2'];
					$dis[$k]['chungAmt3'] = $dis[$k]['sugaTot3'] - $dis[$k]['bonbuTot3'];

					$dis[$k]['sugaTot4']  = $dis[$k]['sugaTot1']  + $dis[$k]['sugaTot2']  + $dis[$k]['sugaTot3'];
					$dis[$k]['boninAmt4'] = $dis[$k]['boninAmt1'] + $dis[$k]['boninAmt2'] + $dis[$k]['boninAmt3'];
					$dis[$k]['overAmt4']  = $dis[$k]['overAmt1']  + $dis[$k]['overAmt2']  + $dis[$k]['overAmt3'];
					$dis[$k]['biPay4']    = $dis[$k]['biPay1']    + $dis[$k]['biPay2']    + $dis[$k]['biPay3'];
					$dis[$k]['bonbuTot4'] = $dis[$k]['bonbuTot1'] + $dis[$k]['bonbuTot2'] + $dis[$k]['bonbuTot3'];
					$dis[$k]['chungAmt4'] = $dis[$k]['chungAmt1'] + $dis[$k]['chungAmt2'] + $dis[$k]['chungAmt3'];

					$dis[$k]['downAmt4'] = $dis[$k]['downAmt1'] + $dis[$k]['downAmt2'] + $dis[$k]['downAmt3'];

					$dis[$k]['resultAmt'] = $dis[$k]['maxAmt'] - $dis[$k]['sugaTot4'];
					$dis[$k]['misuAmt']   = $myF->cutOff($dis[$k]['bonbuTot4'] - $dis[$k]['downAmt4']);
					$dis[$k]['billNo']    = $bill_no;
				}
			}
		}

		unset($client);
	#
	#############################################################







	#############################################################
	#
	# 테이타 저장

		/*********************************************************

			재가요양

		*********************************************************/
		$conf_count = sizeof($conf_data);

		$conn->begin();

		//기존데이타 삭제
		$sql = "delete
				  from t13sugupja
				 where t13_ccode    = '$code'
				   and t13_pay_date = '$year$month'";

		if ($isPerson){
			$sql .= ' AND t13_jumin = \''.$calTarget.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo 9;
			 exit;
		}

		$sql = "delete
				  from t15paymentissu
				 where t15_ccode    = '$code'
				   and t15_pay_date = '$year$month'";

		if ($isPerson){
			$sql .= ' AND t15_jumin = \''.$calTarget.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->batch_log('3', $year.$month, $batch_start_sec, $start_dt, $start_tm, '실적일괄확정 취소중 오류발생');
			 echo 9;
			 exit;
		}

		for($i=0; $i<$conf_count; $i++){
			$p_count = sizeof($conf_data[$i]['p']);
			$c_count = sizeof($conf_data[$i]['c']);

			for($j=0; $j<$p_count; $j++){
				$result = set_data($conn, $conf_data[$i]['p'][$j]);

				if (!$result){
					$conn->rollback();
					$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(계획)');
					echo 9;
					exit;
				}
			}

			for($j=0; $j<$c_count; $j++){
				$result = set_data($conn, $conf_data[$i]['c'][$j], $debug);

				if (!$result){
					$conn->rollback();
					$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
					echo 9;
					exit;
				}
			}
		}

		if (Is_Array($lsHisQuery)){
			//한도초과 리스트 수정
			foreach($lsHisQuery as $svcCd => $arr1){
				if (Is_Array($arr1)){
					foreach($arr1 as $jumin => $arr2){
						if (Is_Array($arr2)){
							foreach($arr2 as $date => $arr3){
								if (Is_Array($arr3)){
									foreach($arr3 as $from => $arr4){
										if (Is_Array($arr4)){
											foreach($arr4 as $seq => $val){
												$lsDate = $val['date'];

												if ($val['val'] > 0){
													$lsFrom = $val['from'];
													$liProc = $val['proc'] * 30;
													$lsTo   = Str_Replace(':','',$myF->min2time($myF->time2min($lsFrom) + $liProc));
													$lsSuga = $val['code'];
													$liSuga = $val['val'];
												}else{
													$lsFrom = '';
													$lsTo   = '';
													$liProc = 0;
													$lsSuga = '';
													$liSuga = 0;
												}

												//일정확정 데이타 로그내역
												$sql = 'INSERT INTO plan_conf_his (
														 org_no
														,svc_kind
														,jumin
														,date
														,time
														,seq
														,conf_date
														,conf_from
														,conf_to
														,conf_time
														,conf_suga
														,conf_value) VALUES (
														 \''.$code.'\'
														,\''.$svcCd.'\'
														,\''.$jumin.'\'
														,\''.$date.'\'
														,\''.$from.'\'
														,\''.$seq.'\'
														,\''.$val['date'].'\'
														,\''.$val['from'].'\'
														,\''.$val['to'].'\'
														,\''.$val['time'].'\'
														,\''.$val['suga'].'\'
														,\''.$val['value'].'\')';

												if (!$conn->execute($sql)){
													 $conn->rollback();
													 $conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
													 echo 9;
													 exit;
												}

												//일정확정 데이타 수정
												if ($val['svcCd'] == '500'){
													$sql = 'UPDATE t01iljung
															   SET t01_conf_suga_value	= \''.$liSuga.'\'
															 WHERE t01_ccode			= \''.$code.'\'
															   AND t01_mkind			= \''.$svcCd.'\'
															   AND t01_jumin			= \''.$jumin.'\'
															   AND t01_sugup_date		= \''.$date.'\'
															   AND t01_sugup_fmtime		= \''.$from.'\'
															   AND t01_sugup_seq		= \''.$seq.'\'';
												}else{
													$sql = 'UPDATE t01iljung
															   SET t01_conf_fmtime		= \''.$lsFrom.'\'
															,      t01_conf_totime		= \''.$lsTo.'\'
															,      t01_conf_soyotime	= \''.$liProc.'\'
															,      t01_conf_suga_code	= \''.$lsSuga.'\'
															,      t01_conf_suga_value	= \''.$liSuga.'\'
															 WHERE t01_ccode			= \''.$code.'\'
															   AND t01_mkind			= \''.$svcCd.'\'
															   AND t01_jumin			= \''.$jumin.'\'
															   AND t01_sugup_date		= \''.$date.'\'
															   AND t01_sugup_fmtime		= \''.$from.'\'
															   AND t01_sugup_seq		= \''.$seq.'\'';
												}

												if (!$conn->execute($sql)){
													 $conn->rollback();
													 $conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
													 echo 9;
													 exit;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}



		/*********************************************************

			바우처 및 기타유료

		*********************************************************/
		$vou_cnt = sizeof($vou);

		$sql = '';

		for($i=0; $i<$vou_cnt; $i++){
			if (!empty($sql)) $sql .= ',';

			if (empty($vou[$i]['tot_time']))  $vou[$i]['tot_time']  = 0;
			if (empty($vou[$i]['tot_amt']))   $vou[$i]['tot_amt']   = 0;
			if (empty($vou[$i]['use_time']))  $vou[$i]['use_time']  = 0;
			if (empty($vou[$i]['use_amt']))   $vou[$i]['use_amt']   = 0;
			if (empty($vou[$i]['over_time'])) $vou[$i]['over_time'] = 0;
			if (empty($vou[$i]['over_amt']))  $vou[$i]['over_amt']  = 0;
			if (empty($vou[$i]['bi_time']))   $vou[$i]['bi_time']   = 0;
			if (empty($vou[$i]['bi_amt']))    $vou[$i]['bi_amt']    = 0;
			if (empty($vou[$i]['savetime']))  $vou[$i]['savetime']  = 0;
			if (empty($vou[$i]['savepay']))   $vou[$i]['savepay']   = 0;

			$vou[$i]['tot_time']  = $vou[$i]['tot_time']  / $vou[$i]['cal_time'] / 60;
			$vou[$i]['use_time']  = $vou[$i]['use_time']  / $vou[$i]['cal_time'] / 60;
			$vou[$i]['over_time'] = $vou[$i]['over_time'] / $vou[$i]['cal_time'] / 60;
			$vou[$i]['bi_time']   = $vou[$i]['bi_time']   / $vou[$i]['cal_time'] / 60;
			$vou[$i]['savetime']  = $vou[$i]['savetime']  / $vou[$i]['cal_time'] / 60;

			$vou[$i]['tot_amt']   = $myF->cutOff($vou[$i]['tot_amt']);
			$vou[$i]['use_amt']   = $myF->cutOff($vou[$i]['use_amt']);
			$vou[$i]['over_amt']  = $myF->cutOff($vou[$i]['over_amt']);
			$vou[$i]['bi_amt']    = $myF->cutOff($vou[$i]['bi_amt']);
			$vou[$i]['savepay']   = $myF->cutOff($vou[$i]['savepay']);

			$sql .= '(\''.$vou[$i]['code'].'\'
					 ,\''.$vou[$i]['kind'].'\'
					 ,\''.$vou[$i]['jumin'].'\'
					 ,\''.$vou[$i]['yymm'].'\'
					 ,\'2\'
					 ,\''.$vou[$i]['cost'].'\'

					 ,\''.$vou[$i]['tot_time'].'\'
					 ,\''.$vou[$i]['use_time'].'\'
					 ,\''.$vou[$i]['over_time'].'\'
					 ,\''.$vou[$i]['bi_time'].'\'

					 ,\''.$vou[$i]['tot_amt'].'\'
					 ,\''.$vou[$i]['use_amt'].'\'
					 ,\''.$vou[$i]['over_amt'].'\'
					 ,\''.$vou[$i]['bi_amt'].'\'

					 ,\''.$vou[$i]['use_amt'].'\'
					 ,\''.($vou[$i]['my_pay'] + $vou[$i]['add_pay']).'\'
					 ,\''.$vou[$i]['over_amt'].'\'
					 ,\''.$vou[$i]['bi_amt'].'\'
					 ,\''.($vou[$i]['my_pay'] + $vou[$i]['add_pay']).'\'
					 ,\''.($vou[$i]['my_pay'] + $vou[$i]['over_amt'] + $vou[$i]['bi_amt'] + $vou[$i]['add_pay']).'\'
					 ,\''.($vou[$i]['my_pay'] + $vou[$i]['over_amt'] + $vou[$i]['bi_amt'] + $vou[$i]['add_pay']).'\'
					 ,\''.$vou[$i]['savetime'].'\'
					 ,\''.$vou[$i]['savepay'].'\'
					 ,\''.$vou[$i]['str_time'].'\'
					 ,\''.$myF->zero_str($vou[$i]['key'], 6).'\')';
		}

		/******************************************************************

		 - 바우처 실적확정 내역을 저장한다.
		   - 필드내용
		     - t13_ccode      : 기관코드
			 - t13_mkind      : 서비스 구분
			 - t13_jumin      : 고객 주민번호
			 - t13_pay_date   : 실적년월
			 - t13_type       : 2를 사용(실적)
			 - t13_result_amt : 서비스 단가

			 - t13_suga_tot1  : 총사용시간
			 - t13_bonin_amt1 : 사용시간
			 - t13_over_amt1  : 초과시간
			 - t13_bipay1     : 비급여시간

			 - t13_suga_tot2  : 총사용금액
			 - t13_bonin_amt2 : 사용금액
			 - t13_over_amt2  : 초과금액
			 - t13_bipay2     : 비급여금액

			 - t13_suga_tot4  : 총사용금액
			 - t13_bonin_amt4 : 본인부담금액
			 - t13_over_amt4  : 초과금액
			 - t13_bipay4     : 비급여금액
			 - t13_bonbu_tot2 : 본인부담금액
			 - t13_bonbu_tot4 : 총본인부담금액(본인부담금액 + 초과금액 + 비급여금액)
			 - t13_misu_amt   : 미수금액(초기에는 총본인부담금액과 같다.)
			 - t13_save_time  : 사용후 남은 시간
			 - t13_save_pay   : 사용후 남은 금액
			 - t13_time_gbn   : 남은 시간이 시간인지 일자인지 구분
			 - t13_bill_no    : 청구서 번호

		******************************************************************/

		if (!empty($sql)) $sql = 'insert into t13sugupja (t13_ccode,t13_mkind,t13_jumin,t13_pay_date,t13_type,t13_result_amt
														 ,t13_suga_tot1,t13_bonin_amt1,t13_over_amt1,t13_bipay1
														 ,t13_suga_tot2,t13_bonin_amt2,t13_over_amt2,t13_bipay2
														 ,t13_suga_tot4,t13_bonin_amt4,t13_over_amt4,t13_bipay4,t13_bonbu_tot2,t13_bonbu_tot4,t13_misu_amt,t13_save_time,t13_save_pay,t13_time_gbn,t13_bill_no) values '.$sql;

		if (!empty($sql)){
			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '바우처 일괄확정중 오류 발생(실적)', $sql);
				echo 9;
				exit;
			}
		}



		/*********************************************************

			장애인활동보조 저장

		*********************************************************/
		$dis_cnt = sizeof($dis);
		for($i=0; $i<$dis_cnt; $i++){
			$result = set_data($conn, $dis[$i]);

			if (!$result){
				$conn->rollback();
				$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '장애인활동지원 일괄확정중 오류 발생(실적)');
				echo 9;
				exit;
			}
		}



		// 로그기록
		$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정 완료');
		$conn->commit();

		unset($conf_data);

		echo 1;
	#
	#############################################################

	// 배열초기화
	function init_array($p_ccode, $p_mkind, $p_jumin, $p_payDate, $p_boninYul, $p_type, $p_maxAmt, $p_limitCare = 0, $p_limitBath = 0, $p_limitNurse = 0){
		$d['ccode']		= $p_ccode;		//기관코드
		$d['mkind']		= $p_mkind;		//기관분류코드
		$d['jumin']		= $p_jumin;		//수급자
		$d['payDate']	= $p_payDate;	//확정년월
		$d['boninYul']	= $p_boninYul;	//본인부담율
		$d['type']		= $p_type;		//계획구분자
		$d['maxAmt']	= $p_maxAmt;	//급여한도
		$d['resultAmt']	= 0;
		$d['overAmt']	= 0;
		$d['realTotal']	= 0;
		$d['sugaTotal']	= 0;

		//서비스별 한도금액
		$d['limitAmt'] = Array('200'=>$p_limitCare, '500'=>$p_limitBath, '800'=>$p_limitNurse);
		$d['svcSuga']  = Array('200'=>0, '500'=>0, '800'=>0);

		for($i=1; $i<=4; $i++){
			$d['sugaTot'.$i]	= 0;
			$d['boninAmt'.$i]	= 0;
			$d['overAmt'.$i]	= 0;
			$d['biPay'.$i]		= 0;
			$d['bonbuTot'.$i]	= 0;
			$d['chungAmt'.$i]	= 0;
			$d['downAmt'.$i]	= 0;
		}

		$d['misuAmt']	= 0;
		$d['misuInAmt']	= 0;
		$d['savetime']	= 0;
		$d['savepay']	= 0;
		$d['str_time']	= '';
		$d['str_other'] = '';
		$d['billNo']	= '000000';

		return $d;
	}

	// 총사용금액
	function get_total_amt($p_conn, $p_filed, $p_ccode, $p_mkind, $p_jumin, $p_sdate, $p_edate, $yymm){
		/*
			$sql = "select sum(".$p_filed.")"
				 . "  from t01iljung"
				 . " where t01_ccode = '".$p_ccode
				 . "'  and t01_mkind = '".$p_mkind
				 . "'  and t01_jumin = '".$p_jumin
				 . "'  and t01_sugup_date between '".$p_sdate
				 . "'                         and '".$p_edate
				 . "'  and t01_conf_soyotime > case when t01_svc_subcode = '200' then 29 else 0 end"
				 . "   and t01_del_yn = 'N'";
			return $p_conn->get_data($sql);
		 */
		if ($p_filed == 't01_conf_suga_value'){
			if ($yymm >= '201401'){
				$sql = 'SELECT ROUND(SUM(t01_conf_suga_value - FLOOR(t01_conf_suga_value * CAST(IFNULL(t01_yoyangsa_id5,0) AS unsigned) * 0.01)) / 10) * 10 AS val';
			}else{
				$sql = 'SELECT SUM(ROUND((t01_conf_suga_value - (t01_conf_suga_value * (CAST(IFNULL(t01_yoyangsa_id5,\'\') AS unsigned) * 0.01))) / 10) * 10) AS val';
			}
		}else{
			$sql = 'SELECT SUM(t01_suga) AS val';
		}

		$sql .= ' FROM t01iljung
				 WHERE t01_ccode = \''.$p_ccode.'\'
				   AND t01_mkind = \''.$p_mkind.'\'
				   AND t01_jumin = \''.$p_jumin.'\'
				   AND t01_sugup_date >= \''.$p_sdate.'\'
				   AND t01_sugup_date <= \''.$p_edate.'\'
				   AND t01_conf_soyotime > CASE WHEN t01_svc_subcode = \'200\' THEN 29 ELSE 0 END
				   AND t01_del_yn = \'N\'';

		return $p_conn->get_data($sql);
	}

	// 영수증번호
	function get_billno($p_billNo){
		$billNo = $p_billNo;

		if ($billNo == ''){
			$billNo = '1';
			$newBillNo = '000001';
		}else{
			$billNo = intVal($billNo) + 1;
			$newBillNo = '';
			for($j=strLen($billNo)+1; $j<=6; $j++){
				$newBillNo .= '0';
			}
			$newBillNo .= $billNo;
		}

		return $newBillNo;
	}

	// 데이타 저장
	function set_data($conn, $a, $admin = false){
		if (!Empty($a['ccode']) &&
			!Empty($a['jumin']) &&
			!Empty($a['payDate'])){
			$sql = "insert into t13sugupja values ("
				 . "  '".$a['ccode']
				 . "','".$a['mkind']
				 . "','".$a['jumin']
				 . "','".$a['payDate']
				 . "','".$a['boninYul']
				 . "','".$a['type']
				 . "','".$a['maxAmt']
				 . "','".$a['resultAmt']
				 . "','".$a['overAmt']
				 . "','".$a['sugaTot1']
				 . "','".$a['boninAmt1']
				 . "','".$a['overAmt1']
				 . "','".$a['biPay1']
				 . "','".$a['bonbuTot1']
				 . "','".$a['chungAmt1']
				 . "','".$a['downAmt1']
				 . "','".$a['sugaTot2']
				 . "','".$a['boninAmt2']
				 . "','".$a['overAmt2']
				 . "','".$a['biPay2']
				 . "','".$a['bonbuTot2']
				 . "','".$a['chungAmt2']
				 . "','".$a['downAmt2']
				 . "','".$a['sugaTot3']
				 . "','".$a['boninAmt3']
				 . "','".$a['overAmt3']
				 . "','".$a['biPay3']
				 . "','".$a['bonbuTot3']
				 . "','".$a['chungAmt3']
				 . "','".$a['downAmt3']
				 . "','".$a['sugaTot4']
				 . "','".$a['boninAmt4']
				 . "','".$a['overAmt4']
				 . "','".$a['biPay4']
				 . "','".$a['bonbuTot4']
				 . "','".$a['chungAmt4']
				 . "','".$a['downAmt4']
				 . "','".$a['misuAmt']
				 . "','0'
					 ,'".$a['savetime']."'
					 ,'".$a['savepay']."'
					 ,'".$a['str_time']."'
					 ,'".$a['str_other']."'
					 ,'".$a['billNo']."')";

			$conn->error_query = $sql;

			if (!$conn->execute($sql)){
				return false;
			}
		}

		return true;
	}

	function set_message($id, $gbn, $rst, $msg){
		$sql = "update closing_result
				   set closing_dt_t = now()
				,      closing_rst  = '$rst'
				,      closing_msg  = '$msg'
				 where id           = '$id'";
		return $sql;
	}
?>
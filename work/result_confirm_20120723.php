<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");

	/*
	 * pos
	 * 1 : Ajax로 실행
	 * 2 : window.open으로 실행
	 */

	$pos	= $_REQUEST['pos'];
	$code	= $_REQUEST['code'];
	$year	= $_REQUEST['year'];
	$month	= ($_REQUEST['month'] < 10 ? '0' : '').intval($_REQUEST['month']);
	$gubun	= $_REQUEST['gubun'];
	$today	= date('Y-m-d', mktime());

	#if ($debug) $conn->mode = 2;

	if ($pos == 2){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	}

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$sql = "select act_cls_dt_from
			,      act_bat_conf_flag
			,      act_bat_conf_dt
			  from closing_progress
			 where org_no       = '$code'
			   and closing_yymm = '$year$month'";
	$temp_data = $conn->get_array($sql);

	if ($temp_data[1] == 'Y'){
		// 일괄확정이 진행되었다.
		if ($pos == 1){
			echo '이미 일괄확정이 진행된 년월입니다.';
		}else{
			echo $myF->message('이미 일괄확정이 진행된 년월입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	if ($pos == 1 && $temp_data[2] > $today){
		// 일괄확정 진행일이 아직 되지 앟았다.
		if ($pos == 1){
			echo '아직 일괄확정 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 일괄확정진행일은 '.$temp_data[1].'입니다.';
		}else{
			echo $myF->message('아직 일괄확정 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 일괄확정진행일은 '.$temp_data[1].'입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	unset($temp_data);

	// 저장전 데이타 무결성 확인
	$sql = "select t01_jumin, m03_name
			  from t01iljung
			 inner join m03sugupja
				on m03_ccode = t01_ccode
			   and m03_mkind = t01_mkind
			   and m03_jumin = t01_jumin
			 where t01_ccode         = '$code'
			   and t01_sugup_date like '$year$month%'
			   and t01_del_yn        = 'N'
			   and ((t01_status_gbn = '1' and length(t01_conf_fmtime) != 4)
				or  (t01_status_gbn = '1' and length(t01_conf_totime) != 4)
				or  (t01_status_gbn = '1' and t01_svc_subcode  = '200' and t01_conf_soyotime < 30)
				or  (t01_status_gbn = '1' and t01_svc_subcode != '200' and t01_conf_soyotime = 0)
				or   t01_status_gbn = 'C')
			 order by m03_name";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	if ($row_count > 0){
		// 에러가 있다면...
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$temp_client .= '\n'.($i+1).' '.$row['m03_name'];
		}

		$temp_client = '실적데이타의 오류가 발견되었습니다.\n다음 수급자를 확인하여 주십시오.\n\n'.$temp_client;

		if ($pos == 1){
			echo $temp_client;
		}else{
			echo $myF->message($temp_client, 'Y', 'N', 'Y');
		}
		exit;
	}

	$conn->row_free();

	//고객별 옵션
	$sql = 'SELECT jumin
			,      limit_yn
			  FROM client_option
			 WHERE org_no = \''.$code.'\'';
	$laOption = $conn->_fetch_array($sql, 'jumin');

	// 수급자
	#############################################################
	#
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
				 where m03_ccode = \''.$code.'\'
				 group by m03_jumin, m03_name, m03_key
				 order by name';

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$client[sizeof($client)] = $conn->select_row($i);
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
				   and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				 order by jumin, seq';

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

		for($i=0; $i<$client_count; $i++){
			$sql = "select *
					  from t01iljung
					 where t01_ccode               = '".$client[$i]['code']."'
					   and t01_mkind               = '".$client[$i]['kind']."'
					   and t01_jumin               = '".$client[$i]['jumin']."'
					   and left(t01_sugup_date, 6) = '".$year.$month."'
					   and t01_sugup_date    between '".$client[$i]['date_from']."' and '".$client[$i]['date_to']."'
					   and t01_del_yn              = 'N'
					 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			$y = 0;
			$client_kind = '';

			// 수급자 일정 확정
			for($j=0; $j<$row_count; $j++){
				$row = $conn->select_row($j);

				if ($lbTestMode){
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
				}else{
					$liRate = $client[$i]['bonin_yul'];
				}

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
					$p[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $liRate, '1', $client[$i]['kupyeo_max']);

					// 실적 데이타 초기화
					$c[$y] = init_array($row['t01_ccode'], $row['t01_mkind'], $row['t01_jumin'], $year.$month, $liRate, '2', $client[$i]['kupyeo_max']);

					//수급자 월중간 금액을 넘겨받는다.
					$p[$y]['sugaTotal'] = $tmpSugaP;
					$c[$y]['sugaTotal'] = $tmpSugaC;

					$y ++;
					$k = $y - 1;

					// 계획총금액
					$p[$k]['realTotal'] = get_total_amt($conn, 't01_suga', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to']);

					// 확정총금액
					$c[$k]['realTotal'] = get_total_amt($conn, 't01_conf_suga_value', $client[$i]['code'], $client[$i]['kind'], $client[$i]['jumin'], $client[$i]['date_from'], $client[$i]['date_to']);
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




				if ($row['t01_conf_soyotime'] > (($row['t01_svc_subcode'] == '200') ? 29 : 0)){
					//확정 총 수가
					//$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + (($row['t01_bipay_umu'] != 'Y') ? $row['t01_conf_suga_value'] : 0);
					$c[$k]['sugaTot'.$svcIndex] = $c[$k]['sugaTot'.$svcIndex] + $row['t01_conf_suga_value'];

					// 2011년 7월부터 감액적용
					if (($year.$month) >= '201107' && $row['t01_svc_subcode'] == '500'){
						// 2011년 7월 부터 목욕 시간비율 감액 금액을 추가한다.
						if ($row['t01_conf_soyotime'] < 40){
							// 40분 미만시 전액 감액한다.
							$c[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'];
						}else if ($row['t01_conf_soyotime'] >= 40 && $row['t01_conf_soyotime'] < 60){
							// 40분이상 60분 미만시 80%만 산정한다.
							$c[$k]['downAmt'.$svcIndex] += $row['t01_conf_suga_value'] * 0.2;
						}else{
							// 60분이상시 감액은 없다.
							$c[$k]['downAmt'.$svcIndex] = 0;
						}
					}

					//추가금
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

					$c[$k]['boninAmt1'] = $myF->cutOff($c[$k]['boninAmt1']);
					$c[$k]['boninAmt2'] = $myF->cutOff($c[$k]['boninAmt2']);
					$c[$k]['boninAmt3'] = $myF->cutOff($c[$k]['boninAmt3']);

					$c[$k]['bonbuTot1'] = $myF->cutOff($c[$k]['boninAmt1'] + $c[$k]['overAmt1'] + $c[$k]['biPay1']);
					$c[$k]['bonbuTot2'] = $myF->cutOff($c[$k]['boninAmt2'] + $c[$k]['overAmt2'] + $c[$k]['biPay2']);
					$c[$k]['bonbuTot3'] = $myF->cutOff($c[$k]['boninAmt3'] + $c[$k]['overAmt3'] + $c[$k]['biPay3']);

					$c[$k]['chungAmt1'] = $c[$k]['sugaTot1'] - $c[$k]['bonbuTot1'];
					$c[$k]['chungAmt2'] = $c[$k]['sugaTot2'] - $c[$k]['bonbuTot2'];
					$c[$k]['chungAmt3'] = $c[$k]['sugaTot3'] - $c[$k]['bonbuTot3'];

					$c[$k]['sugaTot4']  = $c[$k]['sugaTot1']  + $c[$k]['sugaTot2']  + $c[$k]['sugaTot3'];
					$c[$k]['boninAmt4'] = $c[$k]['boninAmt1'] + $c[$k]['boninAmt2'] + $c[$k]['boninAmt3'];
					$c[$k]['overAmt4']  = $c[$k]['overAmt1']  + $c[$k]['overAmt2']  + $c[$k]['overAmt3'];
					$c[$k]['biPay4']    = $c[$k]['biPay1']    + $c[$k]['biPay2']    + $c[$k]['biPay3'];
					$c[$k]['bonbuTot4'] = $c[$k]['bonbuTot1'] + $c[$k]['bonbuTot2'] + $c[$k]['bonbuTot3'];
					$c[$k]['chungAmt4'] = $c[$k]['chungAmt1'] + $c[$k]['chungAmt2'] + $c[$k]['chungAmt3'];

					$c[$k]['downAmt4'] = $c[$k]['downAmt1'] + $c[$k]['downAmt2'] + $c[$k]['downAmt3'];

					$c[$k]['resultAmt'] = $c[$k]['maxAmt'] - $c[$k]['sugaTot4'];
					$c[$k]['misuAmt']   = $myF->cutOff($c[$k]['bonbuTot4'] - $c[$k]['downAmt4']);
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
		if ($lbTestMode){
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
									where m03_ccode = \''.$code.'\'
									group by m03_jumin
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
								on svc.jumin = his.jumin
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

		}else{
			$sql = "select code
					,      kind
					,      jumin
					,      name
					,      vlvl
					,      kupyeo_max
					,      kupyeo_1
					,      min(date_from) as date_from
					,      max(date_to) as date_to
					,      client_key
					,      voucher_totaltime as tot_time
					,      voucher_totaltime * voucher_suga_cost as tot_amt
					,      voucher_suga_cost as cost
					,      voucher_overtime + voucher_maketime as maketime
					,      voucher_overpay + voucher_makepay as makepay
					  from (
						   select m03_ccode as code
						   ,      m03_mkind as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as name
						   ,      m03_vlvl as vlvl
						   ,      max(m03_kupyeo_max) as kupyeo_max
						   ,      max(m03_kupyeo_1) as kupyeo_1
						   ,      min(m03_sdate) as date_from
						   ,      max(m03_edate) as date_to
						   ,      m03_key as client_key
							 from (
								  select m03_ccode as m03_ccode
								  ,      m03_mkind as m03_mkind
								  ,      m03_jumin as m03_jumin
								  ,      m03_name as m03_name
								  ,      m03_skind as m03_skind
								  ,      m03_vlvl as m03_vlvl
								  ,      m03_kupyeo_max as m03_kupyeo_max
								  ,      m03_kupyeo_1 as m03_kupyeo_1
								  ,      m03_sdate as m03_sdate
								  ,      m03_edate as m03_edate
								  ,      m03_key   as m03_key
									from m03sugupja
								   where m03_ccode  = '$code'
									 and m03_mkind >= '1'
									 and m03_mkind != '4'
									 and m03_del_yn = 'N'
								   union all
								  select m31_ccode as m03_ccode
								  ,      m31_mkind as m03_mkind
								  ,      m31_jumin as m03_jumin
								  ,      m03_name as m03_name
								  ,      m31_kind as m03_skind
								  ,      m31_vlvl as m03_vlvl
								  ,      m31_kupyeo_max as m03_kupyeo_max
								  ,      m31_kupyeo_1 as m03_kupyeo_1
								  ,      m31_sdate as m03_sdate
								  ,      m31_edate as m03_edate
								  ,      m03_key   as m03_key
									from m31sugupja
								   inner join m03sugupja
									  on m03_ccode  = m31_ccode
									 and m03_mkind  = m31_mkind
									 and m03_jumin  = m31_jumin
									 and m03_del_yn = 'N'
								   where m31_ccode  = '$code'
									 and m31_mkind >= '1'
									 and m31_mkind != '4'
								 ) as t
							where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
							group by m03_ccode,m03_mkind, m03_jumin, m03_name, m03_vlvl, m03_key
						  ) as t
					left join voucher_make
					   on org_no        = code
					  and voucher_kind  = kind
					  and voucher_jumin = jumin
					  and voucher_yymm  = '$year$month'
					  and del_flag      = 'N'
					group by code, kind, jumin, name, kupyeo_max, client_key
					order by jumin, kind, date_from, date_to";
		}

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

			#if ($debug){
			#	echo nl2br($sql);
			#	echo '<br>---------------------------------------------------------------------------------------<br>';
			#}

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
		if ($lbTestMode){
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
								   where m03_ccode = \''.$code.'\'
								   group by m03_jumin
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
							  and person.id = svc.val2
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

		}else{
			$sql = "select code
					,      kind
					,      jumin
					,      name
					,      ylvl
					,      vlvl
					,      kupyeo_max
					,      kupyeo_1
					,      min(date_from) as date_from
					,      max(date_to) as date_to
					,      client_key
					,      voucher_totaltime as tot_time
					,      voucher_totaltime * voucher_suga_cost as tot_amt
					,      voucher_suga_cost as cost
					,      voucher_overpay as overpay
					,      voucher_makepay as makepay
					,      voucher_addpay as addpay
					,      voucher_addtime1 * voucher_suga_cost as sidopay
					,      voucher_addtime2 * voucher_suga_cost as jachpay
					  from (
						   select m03_ccode as code
						   ,      m03_mkind as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as name
						   ,      m03_vlvl as vlvl
						   ,      m03_ylvl as ylvl
						   ,      max(m03_kupyeo_max) as kupyeo_max
						   ,      max(m03_kupyeo_1) as kupyeo_1
						   ,      min(m03_sdate) as date_from
						   ,      max(m03_edate) as date_to
						   ,      m03_key as client_key
							 from (
								  select m03_ccode as m03_ccode
								  ,      m03_mkind as m03_mkind
								  ,      m03_jumin as m03_jumin
								  ,      m03_name as m03_name
								  ,      m03_skind as m03_skind
								  ,      m03_vlvl as m03_vlvl
								  ,      m03_ylvl as m03_ylvl
								  ,      m03_kupyeo_max as m03_kupyeo_max
								  ,      m03_kupyeo_1 as m03_kupyeo_1
								  ,      m03_sdate as m03_sdate
								  ,      m03_edate as m03_edate
								  ,      m03_key   as m03_key
									from m03sugupja
								   where m03_ccode  = '$code'
									 and m03_mkind  = '4'
									 and m03_del_yn = 'N'
								   union all
								  select m31_ccode as m03_ccode
								  ,      m31_mkind as m03_mkind
								  ,      m31_jumin as m03_jumin
								  ,      m03_name as m03_name
								  ,      m31_kind as m03_skind
								  ,      m31_vlvl as m03_vlvl
								  ,      m31_level as m03_ylvl
								  ,      m31_kupyeo_max as m03_kupyeo_max
								  ,      m31_kupyeo_1 as m03_kupyeo_1
								  ,      m31_sdate as m03_sdate
								  ,      m31_edate as m03_edate
								  ,      m03_key   as m03_key
									from m31sugupja
								   inner join m03sugupja
									  on m03_ccode  = m31_ccode
									 and m03_mkind  = m31_mkind
									 and m03_jumin  = m31_jumin
									 and m03_del_yn = 'N'
								   where m31_ccode  = '$code'
									 and m31_mkind  = '4'
								 ) as t
							where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
							group by m03_ccode,m03_mkind, m03_jumin, m03_name, m03_vlvl, m03_key
						  ) as t
					left join voucher_make
					   on org_no        = code
					  and voucher_kind  = kind
					  and voucher_jumin = jumin
					  and voucher_yymm  = '$year$month'
					  and del_flag      = 'N'
					group by code, kind, jumin, name, kupyeo_max, client_key
					order by jumin, kind, date_from, date_to";
		}

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
					$dis[$k]['savetime'] = floor($dis[$k]['savepay'] / $client[$i]['cost']);
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

		// 기존데이타 삭제
		$sql = "delete
				  from t13sugupja
				 where t13_ccode    = '$code'
				   and t13_pay_date = '$year$month'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, 't13sugupja 입력위한 삭제중 오류발생', $sql);
			echo $myF->message('error', 'Y', 'Y');
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
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			}

			for($j=0; $j<$c_count; $j++){
				$result = set_data($conn, $conf_data[$i]['c'][$j]);

				if (!$result){
					$conn->rollback();
					$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정중오류 발생(실적)');
					echo $myF->message('error', 'Y', 'Y');
					exit;
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

		#if ($debug){
		#	echo '<br>---------------------------------------------------------------------------------------<br>';
		#	echo nl2br($sql);
		#	$conn->rollback();
		#	$conn->close();
		#	exit;
		#}

		if (!empty($sql)){
			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '바우처 일괄확정중 오류 발생(실적)', $sql);
				echo $myF->message('error', 'Y', 'Y');
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
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}
		}



		// 마감 저장 ----------------------------------

		$sql = "update closing_progress
				   set act_bat_conf_flag = 'Y'
				,      act_bat_can_flag  = 'N'";

		if ($pos == 2){
			$sql .= ", act_bat_conf_dt = '$today'";
		}

		// 마감
		$sql .= ",      act_cls_flag        = 'Y'
				 ,      act_cls_dt_from		= '$today'
				 ,      act_cls_ent_dt		= '$today'";

		$sql .= "
				 where org_no       = '$code'
				   and closing_yymm = '$year$month'
				   and del_flag     = 'N'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'closing_progress 플래그 변경중 오류발생', $sql);
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		// 로그기록
		$conn->batch_log('1', $year.$month, $batch_start_sec, $start_dt, $start_tm, '수급자 일괄확정 완료');
		$conn->commit();

		unset($conf_data);

		if ($conn->mode == 1){
			if ($pos == 2) echo $myF->message('ok', 'Y', 'N', 'Y');
		}
	#
	#############################################################

	// 배열초기화
	function init_array($p_ccode, $p_mkind, $p_jumin, $p_payDate, $p_boninYul, $p_type, $p_maxAmt){
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
	function get_total_amt($p_conn, $p_filed, $p_ccode, $p_mkind, $p_jumin, $p_sdate, $p_edate){
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
	function set_data($conn, $a){
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

	include_once("../inc/_db_close.php");
?>
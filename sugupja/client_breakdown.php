<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$seq   = $_POST['seq'];
	$mode  = $_POST['mode'];

	parse_str($_POST['para'], $para);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$stat = array('1'=>'이용','9'=>'중지');

	if ($mode == 1){
		/*********************************************************

			최근 계약내역

		*********************************************************/
		/*
		$reason[0] = array(
					'01'=>'계약해지'
				,	'02'=>'보류'
				,	'03'=>'사망'
				,	'04'=>'타기관이전'
				,	'05'=>'등외판정'
				,	'06'=>'입원'
				,	'99'=>'기타'
			);
		 */
		$reason[0] = array(
				'01'=>'계약해지'
			,	'02'=>'보류'
			,	'03'=>'사망'
			,	'04'=>'타기관이동'
			,	'05'=>'등외판정'
			,	'06'=>'입원'
			,	'07'=>'무리한서비스요구'
			,	'08'=>'단순서비스종료'
			,	'09'=>'근무자미투입'
			,	'10'=>'거주지이전'
			,	'11'=>'건강호전'
			,	'12'=>'부담금미납'
			,	'13'=>'지점이동'
			,	'14'=>'요양입소'
			,	'99'=>'기타'
		);

		$reason[1] = array(
				'01'=>'본인포기'
			,	'02'=>'사망'
			,	'03'=>'말소'
			,	'04'=>'전출'
			,	'05'=>'미사용'
			,	'06'=>'본인부담금미납'
			,	'07'=>'사업종료'
			,	'08'=>'자격종료'
			,	'09'=>'판정결과반영'
			,	'10'=>'자격정지'
			,	'99'=>'기타'
		);

		$sql = 'select from_dt
				,      to_dt
				,      svc_stat
				,      svc_reason
				  from client_his_svc
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				 order by seq desc
				 limit 1';

		$row = $conn->get_array($sql);

		$idx = ($svcCd == '0' ? 0 : 1);

		echo 'from='.$row['from_dt'].
			'&to='.$row['to_dt'].
			'&statCd='.$row['svc_stat'].
			'&statNm='.$stat[$row['svc_stat']].
			'&reasonCd='.$row['svc_reason'].
			'&reasonNm='.$reason[$idx][$row['svc_reason']];

		unset($row);


	}else if ($mode == 2){
		$level = array(
				'1'=>'1등급'
			,	'2'=>'2등급'
			,	'3'=>'3등급'
			,	'4'=>'4등급'
			,	'5'=>'5등급'
			,	'9'=>'일반'
			,	'A'=>'인지지원'
		);

		$sql = 'select m91_code as cd
				,      m91_kupyeo as pay
				,      m91_sdate as f_dt
				,      m91_edate as t_dt
				  from m91maxkupyeo';

		$arrLimitPay = $conn->_fetch_array($sql);

		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \'0\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      app_no
				,      date_format(from_dt,\'%Y%m%d\') as from_dt
				,      date_format(to_dt,\'%Y%m%d\') as to_dt
				,      level
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \'0\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$dt  = str_replace('-','',$dt);

		if (is_array($arrLimitPay)){
			foreach($arrLimitPay as $pay){
				if ($pay['cd'] == $row['level'] && $pay['f_dt'] <= $dt && $pay['t_dt'] >= $dt){
					$limitPay = $pay['pay'];
					break;
				}
			}
		}

		echo 'mgmtNo='.$row['app_no'].
			'&mgmtFrom='.$row['from_dt'].
			'&mgmtTo='.$row['to_dt'].
			'&mgmtLvlCd='.$row['level'].
			'&mgmtLvlNm='.$level[$row['level']].
			'&mgmtPay='.$limitPay.
			'&seq='.$row['seq'];

		unset($row);


	}else if ($mode == 3){
		$sql = 'select m91_code as cd
				,      m91_kupyeo as pay
				,      m91_sdate as f_dt
				,      m91_edate as t_dt
				  from m91maxkupyeo';

		$arrLimitPay = $conn->_fetch_array($sql);

		$sql = 'select level
				,      from_dt
				,      to_dt
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \'0\'';

		$arrLvl = $conn->_fetch_array($sql);

		$sql = 'select count(*)
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      kind
				,	   case kind when \'3\' then \'기초수급권자\'
								 when \'2\' then \'의료수급권자\'
								 when \'4\' then \'경감대상자\' else \'일반\' end as kind_nm
				,      rate
				,      from_dt
				,      to_dt
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if (is_array($arrLvl)){
			foreach($arrLvl as $lvl){
				if ($lvl['from_dt'] <= $dt && $lvl['to_dt'] >= $dt){
					$lvlCd = $lvl['level'];
					break;
				}
			}
		}

		if (is_array($arrLimitPay)){
			$dt  = str_replace('-','',$dt);

			foreach($arrLimitPay as $pay){
				if ($pay['cd'] == $lvlCd && $pay['f_dt'] <= $dt && $pay['t_dt'] >= $dt){
					$limitPay = $pay['pay'];
					break;
				}
			}
		}

		$expenseAmt = $myF->cutOff($limitPay * $row['rate'] * 0.01);

		echo 'kind='.$row['kind'].
			'&kindNm='.$row['kind_nm'].
			'&rate='.$row['rate'].
			'&amt='.$expenseAmt.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'];

		unset($row);

	}else if ($mode == 4){
		/*
		$sql = 'select m91_code as cd
				,      m91_kupyeo as pay
				,      m91_sdate as f_dt
				,      m91_edate as t_dt
				  from m91maxkupyeo';

		$arrLimitPay = $conn->_fetch_array($sql);

		$dt  = date('Ymd');
		$sql = 'select level
				  from client_his_lvl
				 where org_no   = \''.$code.'\'
				   and jumin    = \''.$jumin.'\'
				   and from_dt <= \''.$dt.'\'
				   and to_dt   >= \''.$dt.'\'';

		$lvl = $conn->get_data($sql);

		foreach($arrLimitPay as $pay){
			if ($pay['cd'] == $lvl && $pay['f_dt'] <= $dt && $pay['t_dt'] >= $dt){
				$limitPay = $pay['pay'];
				break;
			}
		}
		*/

		$sql = 'select count(*)
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      amt
				  from client_his_limit
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);

		//if (empty($row['amt'])) $row['amt'] = $limitPay;

		echo 'amt='.$row['amt'].
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'];

		unset($row);

	}else if ($mode == 5){
		//서비스 리스트
		$sql = 'select person_code as cd
				,      person_id as val
				,      person_conf_time as time
				,      person_from_dt as from_dt
				,      person_to_dt as to_dt
				  from suga_person
				 where org_no              = \'goodeos\'
				   and left(person_code,3) = \'VH0\'';

		$laSvcList = $conn->_fetch_array($sql);

		//서비스단가
		$sql = 'select service_cost as cost
				,      service_from_dt as from_dt
				,      service_to_dt as to_dt
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \'1\'
				   and service_code = \'VH001\'';

		$laSvcCost = $conn->_fetch_array($sql);

		$sql = 'select count(*)
				  from client_his_nurse
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		//가사간병 이력
		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				  from client_his_nurse
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if (is_array($laSvcList)){
			foreach($laSvcList as $svc){
				if ($svc['val'] == $row['svc_val'] && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$time = $svc['time'];
					break;
				}
			}
		}

		if (is_array($laSvcCost)){
			foreach($laSvcCost as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$cost = $svc['cost'];
					break;
				}
			}
		}

		echo 'val='.$row['svc_val'].
			'&time='.$time.
			'&amt='.($cost * $time).
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'];

		unset($laSvcList);
		unset($laSvcCost);
		unset($row);

	}else if ($mode == 6){
		//고객 서비스 리스트
		switch ($svcCd){
			case 1:
				$sql = 'select seq
						,      from_dt
						,      to_dt
						,      svc_val
						  from client_his_nurse
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';
				break;

			case 2:
				$sql = 'select seq
						,      from_dt
						,      to_dt
						,      svc_val
						,      svc_tm
						  from client_his_old
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';
				break;

			case 3:
				$sql = 'select seq
						,      from_dt
						,      to_dt
						,      svc_val
						  from client_his_baby
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'';
				break;

			case 4:
				$sql = 'select seq
						,      from_dt
						,      to_dt
						,      svc_val
						,      svc_lvl
						  from client_his_dis
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_val = \'1\'';
				break;
		}

		$sl .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				 and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';

		$liCnt = $conn->get_data($sql.$sl);

		if ($liCnt > 0){
			$sql = $sql.$sl;
		}else{
			$sql .= ' order by seq desc
					  limit 1';
		}

		$laSvcList = $conn->_fetch_array($sql);

		//본인부담금 리스트
		$sql = 'select left(person_code,3) as cd
				,      person_id as val
				,      person_amt1 as amt1
				,      person_amt2 as amt2
				,      person_amt3 as amt3
				,      person_amt4 as amt4
				,      person_amt5 as amt5
				,      person_amt6 as amt6
				,      person_from_dt as from_dt
				,      person_to_dt as to_dt
				  from suga_person
				 where org_no = \'goodeos\'';

		$laExpense = $conn->_fetch_array($sql);

		//고객 소득등급 리스트
		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		
		if($svcCd == '4'){
			$sql .= '  and (app_no = \'1\' or app_no is null)';
		}

		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      level
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}
		
		if($svcCd == '4'){
			$sql .= '  and (app_no = \'1\' or app_no is null)';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if (is_array($laSvcList)){
			foreach($laSvcList as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					switch($svcCd){
						case 1:
							$val  = $svc['svc_val'];
							$suga = 'VH0';
							break;

						case 2:
							#$val = $svc['svc_tm'];
							#$suga = 'VO'.($val == '1' ? 'V' : 'D');
							$val = $svc['svc_val'];

							if ($val == '1'){
								$suga = 'VOV';
							}else if ($val == '2'){
								$suga = 'VOD';
							}else if ($val == '3'){
								$suga = 'VOS';
							}

							$val = $svc['svc_tm'];

							break;

						case 3:
							$val  = $svc['svc_val'];
							$suga = 'VM0';
							break;

						case 4:
							$val  = $svc['svc_lvl'];

							if ($val == '1') $val = '4';
							else if ($val == '2') $val = '3';
							else if ($val == '3') $val = '2';
							else $val = '1';

							$suga = 'VA0';
							break;
					}
					break;
				}
			}
		}

		if (is_array($laExpense)){
			foreach($laExpense as $svc){
				if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$msg = $suga.'/'.$val.'/'.$dt;
					$amt = $svc['amt'.$row['level']];
					break;
				}
			}
		}

		$lvlNm = $myF->_lvlNm($row['level'],$svcCd);

		echo 'lvl='.$row['level'].
			'&lvlNm='.$lvlNm.
			'&amt='.$amt.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'].
			'&msg='.$msg;

		unset($row);

	}else if ($mode == 7){
		//서비스 단가
		$sql = 'select	service_code as cd
				,		service_cost as cost
				,		service_from_dt as from_dt
				,		service_to_dt as to_dt
				from	suga_service
				where	org_no       = \'goodeos\'
				and		service_kind = \'2\'';

		$laSugaList = $conn->_fetch_array($sql, 'cd');

		//노인돌봄 이력
		$sql = 'select count(*)
				  from client_his_old
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				,      svc_tm
				  from client_his_old
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		//$cd  = 'VO'.($row['svc_val'] == '1' ? 'V' : 'D').'01';

		if ($row['svc_val'] == '1'){
			$cd = 'VOV01';
		}else if ($row['svc_val'] == '2'){
			$cd = 'VOD01';
		}else if ($row['svc_val'] == '3'){
			$cd = 'VOS01';
		}

		if (is_array($laSugaList)){
			foreach($laSugaList as $val){
				if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
					$cost = $val['cost'];
					break;
				}
			}
		}

		if (empty($cost)) $cost = 0;

		if ($row['svc_val'] == '1'){
			$svcVal = '방문';
		}else if ($row['svc_val'] == '2'){
			$svcVal = '주간보호';
		}else if ($row['svc_val'] == '3'){
			$svcVal = '단기가사';
		}else{
			$svcVal = '';
		}

		if ($row['svc_val'] == '3'){
			if ($row['svc_tm'] == '1'){
				$svcTm   = '24시간(1개월)';
				$svcTime = 24;
			}else{
				$svcTm   = '48시간(2개월)';
				$svcTime = 48;
			}
		}else{
			if ($row['svc_tm'] == '1'){
				$svcTm   = ($row['svc_val'] == '1' ? '27시간' : '9일');
				$svcTime = ($row['svc_val'] == '1' ? 27 : 9);
			}else if ($row['svc_tm'] == '2'){
				$svcTm   = ($row['svc_val'] == '1' ? '36시간' : '12일');
				$svcTime = ($row['svc_val'] == '1' ? 36 : 12);
			}else{
				$svcTm   = '';
				$svcTime = 0;
			}
		}

		$amt = $cost * $svcTime;

		echo 'val='.$row['svc_val'].
			'&valNm='.$svcVal.
			'&time='.$row['svc_tm'].
			'&timeNm='.$svcTm.
			'&amt='.$amt.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'];

		unset($laSugaList);
		unset($row);

	}else if ($mode == 8){
		//서비스 단가
		$sql = 'select service_code as cd
				,      concat(service_gbn,\'[\',service_conf_time,\'일]\') as nm
				,      service_cost as cost
				,      service_conf_time as cnt
				,      service_from_dt as from_dt
				,      service_to_dt as to_dt
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \'3\'';

		$laSugaList = $conn->_fetch_array($sql, 'cd');

		$sql = 'select count(*)
				  from client_his_baby
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				  from client_his_baby
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$cd  = 'VM'.$row['svc_val'].'01';

		if (is_array($laSugaList)){
			foreach($laSugaList as $val){
				if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
					$suga = $val['nm'];
					$cost = $val['cost'];
					$cnt  = $val['cnt'];
					break;
				}
			}
		}

		$amt = $myF->cutOff($cost * $cnt);

		if (empty($amt)) $amt = 0;

		echo 'val='.$row['svc_val'].
			'&valNm='.$suga.
			'&amt='.$amt.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'];

		unset($laSugaList);
		unset($row);

	}else if ($mode == 9){
		//서비스 단가
		$sql = 'select service_code as cd
				,      service_lvl as nm
				,      service_conf_time as cnt
				,      service_conf_amt as amt
				,      service_from_dt as from_dt
				,      service_to_dt as to_dt
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \'4\'';

		$laSugaList = $conn->_fetch_array($sql);

		$sql = 'select count(*)
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_val != \'3\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				,      svc_lvl
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_val != \'3\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$cd  = 'VA'.($row['svc_val'] == '1' ? 'A' : 'C').$row['svc_lvl'].'0';

		if (is_array($laSugaList)){
			foreach($laSugaList as $val){
				if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
					$suga = $val['nm'];
					$amt  = $val['amt'];
					$time = $val['cnt'];
					break;
				}
			}
		}

		if (empty($amt)) $amt = 0;

		echo 'val='.$row['svc_val'].
			'&valNm='.$suga.
			'&lvl='.$row['svc_lvl'].
			'&lvlNm='.$time.'시간'.
			'&amt='.$amt.
			'&time='.$time.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'].
			'&dt='.$dt;

		unset($laSugaList);
		unset($row);


	}else if ($mode == 10){
		//서비스 단가
		$sql = 'select service_code as cd
				,      service_lvl as nm
				,      service_conf_time as cnt
				,      service_conf_amt as amt
				,      service_from_dt as from_dt
				,      service_to_dt as to_dt
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \'4\'';

		$laSugaList = $conn->_fetch_array($sql);

		$sql = 'select count(*)
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_val = \'3\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				,      svc_lvl
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_val = \'3\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$cd  = 'VA'.($row['svc_val'] == '3' ? 'D' : 'C').($row['svc_lvl'] < 10 ? '0'.$row['svc_lvl'] : $row['svc_lvl']);

		if (is_array($laSugaList)){
			foreach($laSugaList as $val){
				if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
					$suga = $val['nm'];
					$amt  = $val['amt'];
					$time = $val['cnt'];
					break;
				}
			}
		}

		if (empty($amt)) $amt = 0;

		echo 'val='.$row['svc_val'].
			'&valNm='.$suga.
			'&lvl='.$row['svc_lvl'].
			'&lvlNm='.$time.'시간'.
			'&amt='.$amt.
			'&time='.$time.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'].
			'&dt='.$dt;

		unset($laSugaList);
		unset($row);


	}else if ($mode == 11){
		//고객 서비스 리스트
		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      svc_val
				,      svc_lvl
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';
		

		$sl .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				 and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';

		$liCnt = $conn->get_data($sql.$sl);

		if ($liCnt > 0){
			$sql = $sql.$sl;
		}else{
			$sql .= ' order by seq desc
					  limit 1';
		}

		$laSvcList = $conn->_fetch_array($sql);

		//본인부담금 리스트
		$sql = 'select left(person_code,3) as cd
				,      person_id as val
				,      person_amt1 as amt1
				,      person_amt2 as amt2
				,      person_amt3 as amt3
				,      person_amt4 as amt4
				,      person_amt5 as amt5
				,      person_amt6 as amt6
				,      person_from_dt as from_dt
				,      person_to_dt as to_dt
				  from suga_person
				 where org_no = \'goodeos\'';

		$laExpense = $conn->_fetch_array($sql);

		//고객 소득등급 리스트
		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				   and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		$liCnt = $conn->get_data($sql);

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      level
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'';

		if ($liCnt > 0){
			$sql .= ' and date_format(from_dt,\'%Y-%m-%d\') <= date_format(now(),\'%Y-%m-%d\')
				      and date_format(to_dt,  \'%Y-%m-%d\') >= date_format(now(),\'%Y-%m-%d\')';
		}

		$sql .= ' order by seq desc
				  limit 1';

		$row = $conn->get_array($sql);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if (is_array($laSvcList)){
			foreach($laSvcList as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					
					$val  = $svc['svc_lvl'];
					$suga = 'VB0';
					
					break;
				}
			}
		}

		if (is_array($laExpense)){
			foreach($laExpense as $svc){
				if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$msg = $suga.'/'.$val.'/'.$dt;
					$amt = $svc['amt'.$row['level']];
					break;
				}
			}
		}

		if($row['level'] == '1'){
			$lvlNm = '생계의료급여수급자';
		}else if($row['level'] == '2'){
			$lvlNm = '차상위계층';
		}else if($row['level'] == '3'){
			$lvlNm = '70%이하';
		}else if($row['level'] == '4'){
			$lvlNm = '120%이하';
		}else if($row['level'] == '5'){
			$lvlNm = '180%이하';
		}else if($row['level'] == '6'){
			$lvlNm = '180%초과';
		}

		echo 'lvl='.$row['level'].
			'&lvlNm='.$lvlNm.
			'&amt='.$amt.
			'&fromDt='.$row['from_dt'].
			'&toDt='.$row['to_dt'].
			'&seq='.$row['seq'].
			'&msg='.$msg;

		unset($row);

	}else if ($mode == 71){
		//재가지원
		$sql = 'SELECT	care_org_no
				,		care_org_nm
				,		care_no
				,		care_lvl
				,		care_gbn
				,		care_cost
				,		care_pic_nm
				,		care_telno
				FROM	client_his_care
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';

		$row = $conn->get_array($sql);

		$data .= 'kind='	.$row['care_kind'];
		$data .= '&orgNo='	.$row['care_org_no'];
		$data .= '&orgNm='	.$row['care_org_nm'];
		$data .= '&no='		.SubStr($row['care_no'],1);
		$data .= '&lvl='	.$row['care_lvl'];
		$data .= '&gbn='	.$row['care_gbn'];
		$data .= '&cost='	.$row['care_cost'];
		$data .= '&picnm='	.$row['care_pic_nm'];
		$data .= '&telno='	.$row['care_telno'];

		echo $data;


	}else if ($mode == 99){
		//기타유료 데이타
		$sql = 'select svc_val
				,      svc_cost
				,      svc_cnt
				,      recom_nm
				,      recom_tel
				,      recom_amt
				  from client_his_other
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and seq    = \''.$seq.'\'';

		$row = $conn->get_array($sql);

		echo 'val='.$row['svc_val'].
			'&cost='.$row['svc_cost'].
			'&cnt='.$row['svc_cnt'].
			'&recomNm='.$row['recom_nm'].
			'&recomTel='.$row['recom_tel'].
			'&recomAmt='.$row['recom_amt'];

		unset($row);
	}


	include_once('../inc/_db_close.php');
?>
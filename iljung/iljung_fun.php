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
	$year  = $_POST['year'];
	$month = $_POST['month'];

	parse_str($_POST['para'], $para);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($mode == 1){
		//고객정보
		$sql = 'select m03_name as nm
				,      m03_tel as phone
				,      m03_hp as mobile
				,      m03_yboho_name as p_name
				,      m03_yboho_gwange as p_rel
				,      m03_yboho_phone as p_mobile
				,      m03_post_no as post_no
				,      m03_juso1 as addr
				,      m03_juso2 as addr_dtl
				  from m03sugupja
				 where m03_ccode = \''.$code.'\'
				   and m03_jumin = \''.$jumin.'\'
				 order by m03_mkind
				 limit 1';

		$row = $conn->get_array($sql);

		echo 'name='.$row['nm'].
			'&jumin='.$myF->issStyle($jumin).
			'&phone='.$myF->phoneStyle($row['phone'],'.').
			'&mobile='.$myF->phoneStyle($row['mobile'],'.').
			'&parent='.$row['p_name'].'/'.$row['p_rel'].
			'&parentTel='.$myF->phoneStyle($row['p_mobile'],'.').
			'&addr='.$row['addr'].' '.$row['addr_dtl'];

		unset($row);

	}else if ($mode == 2){
		//고객소득등급 및 본인부담금
		$year  = $_POST['year'];
		$month = $_POST['month'];

		switch($svcCd){
			case 1:
				$sql = 'select seq
						,      svc_val
						,      from_dt
						,      to_dt
						  from client_his_nurse';
				break;

			case 2:
				$sql = 'select seq
						,      svc_val
						,      svc_tm
						,      from_dt
						,      to_dt
						  from client_his_old';
				break;

			case 3:
				$sql = 'select seq
						,      svc_val
						,      from_dt
						,      to_dt
						  from client_his_baby';
				break;

			case 4:
				$sql = 'select seq
						,      svc_val
						,      svc_lvl
						,      from_dt
						,      to_dt
						  from client_his_dis';
				break;
		}

		$sql .= ' where org_no = \''.$code.'\'
				    and jumin  = \''.$jumin.'\'
					and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
					and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				  order by seq desc
				  limit 1';

		$laSvcList = $conn->_fetch_array($sql);

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

		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      level
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				 order by seq desc';

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
							$val  = $svc['svc_tm'];
							$suga = 'VO'.($val == '1' ? 'V' : 'D');
							break;

						case 3:
							$val  = $svc['svc_val'];
							$suga = 'VM0';
							break;

						case 4:
							if ($svc['svc_val'] == '1'){
								//성인
								if ($svc['svc_lvl'] == '1'){
									$val = '4';
								}else if ($svc['svc_lvl'] == '2'){
									$val = '3';
								}else if ($svc['svc_lvl'] == '3'){
									$val = '2';
								}else if ($svc['svc_lvl'] == '4'){
									$val = '1';
								}

								$suga = 'VA0';
							}else if ($svc['svc_val'] == '2'){
								//아동
								$val = $svc['svc_lvl'];

								$suga = 'VA0';
							}else if ($svc['svc_val'] == '3'){
								//성인
								$val = $svc['svc_lvl'];
								
								$svcVal = $svc['svc_val'];
								$suga = 'VB0';
							}
							//$val  = $svc['svc_lvl'];
							
							break;
					}
					break;
				}
			}
		}

		if (is_array($laExpense)){
			foreach($laExpense as $svc){
				if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$expense = $svc['amt'.$row['level']];
					break;
				}
			}
		}

		if (empty($expense)) $expense = -1;

		$lvlNm = $myF->_lvlNm($row['level'],$svcCd, $svcVal);

		echo 'lvl='.$row['level'].
			'&lvlNm='.$lvlNm.
			'&expense='.$expense;

	}else if ($mode == 3){
		//서비스 등록 가능 년월
		$year  = $_POST['year'];
		$month = $_POST['month'];

		$sql = 'select seq
				,      date_format(from_dt, \'%Y%m\') as from_dt
				,      date_format(to_dt  , \'%Y%m\') as to_dt
				  from client_his_svc
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and date_format(from_dt, \'%Y\') <= \''.$year.'\'
				   and date_format(to_dt,   \'%Y\') >= \''.$year.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();
		$result   = '';

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			for($j=1; $j<=12; $j++){
				$mon = ($j < 10 ? '0' : '').$j;

				$result .= (!empty($result) ? '&' : '').$j.'=';

				if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon)
					$result .= 'Y';
				else
					$result .= 'N';
			}
		}

		$conn->row_free();

		echo $result;



	}else if ($mode == 41){
		//장애인활동지원 설정값
		$sql = 'select m03_sgbn as add_pay1
				,      m03_add_pay_gbn as add_pay2
				,      m03_add_time1 as sido_time
				,      m03_add_time2 as jach_time
				  from m03sugupja
				 where m03_ccode  = \''.$code.'\'
				   and m03_mkind  = \''.$svcCd.'\'
				   and m03_jumin  = \''.$jumin.'\'
				   and m03_del_yn = \'N\'';
		
		$row = $conn->get_array($sql);

		echo 'add1='.$row['add_pay1'].
			'&add2='.$row['add_pay2'].
			'&sido='.$row['sido_time'].
			'&jach='.$row['jach_time'];

	}else if ($mode == 42){
		$year  = $_POST['year'];
		$month = $_POST['month'];

		//장애인활동지원
		$sql = 'select svc_val
				,      svc_lvl
				,      from_dt
				,      to_dt
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';

		$laSvcList = $conn->_fetch_array($sql);


		//서비스 단가
		$sql = 'select service_code as cd
				,      service_lvl as nm
				,      service_conf_time as cnt
				,      service_conf_amt as amt
				,      service_from_dt as from_dt
				,      service_to_dt as to_dt
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \'4\'
				   and date_format(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

		//$laSugaList = $conn->_fetch_array($sql, 'cd');
		$laSugaList = $conn->_fetch_array($sql);


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
				 where org_no = \'goodeos\'
				   and date_format(person_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(person_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

		$laExpenseList = $conn->_fetch_array($sql);


		//고객 소득등급 리스트
		$sql = 'select seq
				,      from_dt
				,      to_dt
				,      level
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';

		$laLvlList = $conn->_fetch_array($sql);

		$dt = $myF->_getDt($laLvlList[0]['from_dt'],$laLvlList[0]['to_dt']);

		if (is_array($laSvcList)){
			foreach($laSvcList as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					if ($svc['svc_val'] == '1'){
						//성인
						if ($svc['svc_lvl'] == '1'){
							$val = '4';
						}else if ($svc['svc_lvl'] == '2'){
							$val = '3';
						}else if ($svc['svc_lvl'] == '3'){
							$val = '2';
						}else if ($svc['svc_lvl'] == '4'){
							$val = '1';
						}

						$suga = 'VA0';

					}else if ($svc['svc_val'] == '2'){
						//아동
						$val = $svc['svc_lvl'];
						$suga = 'VA0';

					}else if ($svc['svc_val'] == '3'){
						$val = $svc['svc_lvl'];
						$suga = 'VB0';
					}
					
					break;
				}
			}
		}

		if (is_array($laExpenseList)){
			foreach($laExpenseList as $svc){
				//if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
				if ($svc['cd'] == $suga && $svc['val'] == $val &&
					SubStr(Str_Replace('-','',$svc['from_dt']),0,6) <= $year.$month &&
					SubStr(Str_Replace('-','',$svc['to_dt']),0,6) >= $year.$month){
					$expense = $svc['amt'.$laLvlList[0]['level']];
					break;
				}
			}
		}

		$dt = $myF->_getDt($laSvcList[0]['from_dt'],$laSvcList[0]['to_dt']);
		
		if($laSvcList[0]['svc_val'] == '3'){
			$cd = 'VA'.($laSvcList[0]['svc_val'] == '3' ? 'D' : 'C').($laSvcList[0]['svc_lvl']<10?'0'.$laSvcList[0]['svc_lvl']:$laSvcList[0]['svc_lvl']);
		}else {
			$cd = 'VA'.($laSvcList[0]['svc_val'] == '1' ? 'A' : 'C').$laSvcList[0]['svc_lvl'].'0';
		}
		

		if (is_array($laSugaList)){
			foreach($laSugaList as $val){
				if ($val['cd'] == $cd && SubStr(Str_Replace('-','',$val['from_dt']),0,6) <= $year.$month && SubStr(Str_Replace('-','',$val['to_dt']),0,6) >= $year.$month){
					$suga = $val['nm'];
					$amt  = $val['amt'];
					$time = $val['cnt'];
					break;
				}
			}
		}

		$support = $amt - $expense;

		echo 'val='.$laSvcList[0]['svc_val'].
			'&lvl='.$laSvcList[0]['svc_lvl'].
			'&amt='.$amt.
			'&time='.$time.
			'&support='.$support.
			'&expense='.$expense;

	}else if ($mode == 43){
		$year  = $_POST['year'];
		$month = $_POST['month'];

		$sql = 'select svc_val as gbn
				,      svc_lvl as lvl
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';
		
		$hisDis = $conn->get_array($sql);
		
		
		$idx  = 1;

		if($hisDis['gbn'] == '1' || $hisDis['gbn'] == '2'){
			//장애활동지원 추가급여
			$sql = 'select svc_gbn_cd as cd
					,      svc_gbn_nm as nm
					,      svc_pay as pay
					,      svc_time as time
					  from suga_service_add
					 where svc_kind  = \''.$svcCd.'\'
					   and svc_group = \'R\'
					   and date_format(svc_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(svc_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
			
			
			$html = '0=1/0/해당없음/0/0';
			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();
			
			
			for($i=0; $i<$rowCount; $i++){
				$row   = $conn->select_row($i);
				$html .= '&'.$idx.'=1/'.$row['cd'].'/'.$row['nm'].'/'.$row['pay'].'/'.$row['time'];
				$idx ++;
			}
		}else {
			$html = '0=/0/1/';
		}
		
		$conn->row_free();
		


		$sql = 'select svc_gbn_cd as cd
				,      svc_gbn_nm as nm
				,      svc_pay as pay
				,      svc_time as time
				  from suga_service_add
				 where svc_kind  = \''.$svcCd.'\'
				   and svc_group = \'C\'
				   and date_format(svc_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(svc_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		
		if($hisDis['gbn'] == '1' || $hisDis['gbn'] == '2'){
			$sql .= ' and svc_gbn_cd < 13';
		}else {
			$sql .= ' and svc_gbn_cd >= 13';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row   = $conn->select_row($i);
			$html .= '&'.$idx.'=2/'.$row['cd'].'/'.$row['nm'].'/'.$row['pay'].'/'.$row['time'];
			$idx ++;
		}

		$conn->row_free();

		echo $html;


	/*********************************************************
	 * 바우처 생성 조회
	 *********************************************************/
	}else if ($mode == 201){
		$year = $_POST['year'];
		$month = $_POST['month'];

		//조회
		switch($svcCd){
			case '1':
				//가사간병
				$sql = 'select voucher_seq as seq
						,      voucher_lvl as lvl
						,      voucher_overtime as overtime
						,      voucher_maketime as maketime
						,      voucher_totaltime as totaltime
						,      voucher_month_time as monthtime
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$svcCd.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'
						   and del_flag      = \'N\'';

				$row = $conn->get_array($sql);

				if (!is_array($row)){
					$sql = 'select svc_val as lvl
							  from client_his_nurse
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							 order by seq desc
							 limit 1';

					$row = $conn->get_array($sql);
					$row['seq'] = '0';

					//이월시간
					$liYear  = intval($year);
					$liMonth = intval($month) - 1;

					if ($liMonth < 1){
						$liMonth = 12;
						$liYear -= 1;
					}

					$liMonth = ($liMonth < 10 ? '0' : '').$liMonth;

					$sql = 'select voucher_maketime + voucher_addtime + CASE SUBSTR(voucher_yymm,5,2) WHEN \'02\' THEN 0 ELSE voucher_overtime END
							  from voucher_make
							 where org_no        = \''.$code.'\'
							   and voucher_kind  = \''.$svcCd.'\'
							   and voucher_jumin = \''.$jumin.'\'
							   and voucher_yymm  = \''.$liYear.$liMonth.'\'
							   and del_flag      = \'N\'';

					#전월 생성금액 및 시간
					$liMonthMake = intval($conn->get_data($sql));

					$sql = 'select sum(t01_conf_soyotime)
							  from t01iljung
							 where t01_ccode      = \''.$code.'\'
							   and t01_mkind      = \''.$svcCd.'\'
							   and t01_jumin      = \''.$jumin.'\'
							   and t01_status_gbn = \'1\'
							   and t01_del_yn     = \'N\'
							   and left(t01_sugup_date, 6) = \''.$liYear.$liMonth.'\'';

					#전월 사용금액 및 시간
					$liMonthUse = intval($conn->get_data($sql));
					$liMonthUse = floor($liMonthUse / 60);

					$row['overtime'] = $liMonthMake - $liMonthUse;
					$ynLoad = 'N';
				}else{
					$ynLoad = 'Y';
				}

				if (intval($month) == 2){
					$row['overtime'] = 0;
				}

				echo 'seq='.$row['seq'].
					'&lvl='.$row['lvl'].
					'&overtime='.$row['overtime'].
					'&makeTime='.$row['maketime'].
					'&totalTime='.$row['totaltime'].
					'&monthtime='.$row['monthtime'];

				break;

			case '2':
				//노인돌봄
				$sql = 'select voucher_seq as seq
						,      case voucher_gbn when \'V\' then \'1\'
												when \'D\' then \'2\'
												when \'S\' then \'3\' else \'9\' end as gbn
						,      voucher_lvl as lvl
						,      voucher_overtime as overtime
						,      voucher_maketime as maketime
						,      voucher_totaltime as totaltime
						,      voucher_month_time as monthtime
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$svcCd.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'
						   and del_flag      = \'N\'';

				$row = $conn->get_array($sql);

				if (!is_array($row)){
					$sql = 'select svc_val as gbn
							,      svc_tm as lvl
							  from client_his_old
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							 order by seq desc
							 limit 1';

					$row = $conn->get_array($sql);
					$row['seq'] = '0';

					//이월시간
					$liYear  = intval($year);
					$liMonth = intval($month) - 1;

					if ($liMonth < 1){
						$liMonth = 12;
						$liYear -= 1;
					}

					$liMonth = ($liMonth < 10 ? '0' : '').$liMonth;

					$sql = 'select voucher_maketime + voucher_addtime + CASE SUBSTR(voucher_yymm,5,2) WHEN \'02\' THEN 0 ELSE voucher_overtime END
							  from voucher_make
							 where org_no        = \''.$code.'\'
							   and voucher_kind  = \''.$svcCd.'\'
							   and voucher_jumin = \''.$jumin.'\'
							   and voucher_yymm  = \''.$liYear.$liMonth.'\'
							   and del_flag      = \'N\'';

					#전월 생성금액 및 시간
					$liMonthMake = intval($conn->get_data($sql));

					$sql = 'select sum(t01_conf_soyotime)
							  from t01iljung
							 where t01_ccode      = \''.$code.'\'
							   and t01_mkind      = \''.$svcCd.'\'
							   and t01_jumin      = \''.$jumin.'\'
							   and t01_status_gbn = \'1\'
							   and t01_del_yn     = \'N\'
							   and left(t01_sugup_date, 6) = \''.$liYear.$liMonth.'\'';

					#전월 사용금액 및 시간
					$liMonthUse = intval($conn->get_data($sql));
					$liMonthUse = floor($liMonthUse / 60);

					$row['overtime'] = $liMonthMake - $liMonthUse;
					$ynLoad = 'N';
				}else{
					$ynLoad = 'Y';
				}

				if (intval($month) == 2){
					$row['overtime'] = 0;
				}

				echo 'seq='.$row['seq'].
					'&gbn='.$row['gbn'].
					'&lvl='.$row['lvl'].
					'&overtime='.$row['overtime'].
					'&makeTime='.$row['maketime'].
					'&totalTime='.$row['totaltime'].
					'&monthtime='.$row['monthtime'];
				break;

			case '3':
				//산모신생아
				$sql = 'select voucher_seq as seq
						,      voucher_gbn as gbn
						,      voucher_maketime as makedays
						,      voucher_totaltime as totaldays
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$svcCd.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'
						   and del_flag      = \'N\'';

				$row = $conn->get_array($sql);

				if (!is_array($row)){
					$sql = 'select svc_val as gbn
							  from client_his_baby
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							 order by seq desc
							 limit 1';

					$row = $conn->get_array($sql);
					$row['seq'] = '0';
					$ynLoad = 'N';
				}else{
					$ynLoad = 'Y';
				}

				echo 'seq='.$row['seq'].
					'&gbn='.$row['gbn'].
					'&makeDays='.$row['makedays'].
					'&totalDays='.$row['totaldays'];
				break;

			case '4':
				//장애인활동지원
				$sql = 'select voucher_seq as seq
						,      voucher_gbn as gbn
						,      voucher_lvl as lvl
						,      voucher_svc_kind as svc_kind
						,      voucher_gbn2 as addpay1
						,      voucher_add_pay_gbn as addpay2
						,      voucher_overtime as overtime
						,      voucher_overpay as overpay
						,      voucher_addtime1 as addtime1
						,      voucher_addtime2 as addtime2
						,      voucher_maketime as maketime
						,      voucher_makepay as makepay
						,      voucher_addtime as addtime
						,      voucher_addpay as addpay
						,      voucher_totaltime as totaltime
						,      voucher_totalpay as totalpay
						,      voucher_month_time as monthtime
						,      voucher_month_pay as monthpay
						,      voucher_suga_cost as cost
						  from voucher_make
						 where org_no        = \''.$code.'\'
						   and voucher_kind  = \''.$svcCd.'\'
						   and voucher_jumin = \''.$jumin.'\'
						   and voucher_yymm  = \''.$year.$month.'\'
						   and del_flag      = \'N\'';

				$row = $conn->get_array($sql);

				if (!is_array($row)){
					$sql = 'select svc_val as gbn
							,      svc_lvl as lvl
							  from client_his_dis
							 where org_no = \''.$code.'\'
							   and jumin  = \''.$jumin.'\'
							   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
							 order by seq desc
							 limit 1';
					
					$row = $conn->get_array($sql);
					$row['seq'] = '0';

					$sql = 'select m03_sgbn as addpay1
						    ,      m03_add_pay_gbn as addpay2
							,      m03_add_time1 as addtime1
						    ,      m03_add_time2 as addtime2
						      from m03sugupja
						     where m03_ccode = \''.$code.'\'
							   and m03_jumin = \''.$jumin.'\'
							   and m03_mkind = \''.$svcCd.'\'';

					$tmp = $conn->get_array($sql);
					$row['addpay1']  = $tmp['addpay1'];
					$row['addpay2']  = $tmp['addpay2'];
					$row['addtime1'] = $tmp['addtime1'];
					$row['addtime2'] = $tmp['addtime2'];
					unset($tmp);

					//이월금액
					$liYear  = intval($year);
					$liMonth = intval($month) - 1;

					if ($liMonth < 1){
						$liMonth = 12;
						$liYear -= 1;
					}

					$liMonth = ($liMonth < 10 ? '0' : '').$liMonth;

					/*********************************************************
						사용한 금액 및 시간
					*********************************************************/
					$sql = 'select voucher_maketime + (voucher_overpay / voucher_suga_cost) + voucher_addtime as time0, voucher_makepay + voucher_overpay + voucher_addpay as pay0
							,      voucher_addtime1 + voucher_addtime2 as time1, (voucher_addtime1 + voucher_addtime2) * voucher_suga_cost as pay1
							,      voucher_suga_cost as suga_cost
							,      insert_dt
							,      update_dt
							  from voucher_make
							 where org_no        = \''.$code.'\'
							   and voucher_kind  = \''.$svcCd.'\'
							   and voucher_jumin = \''.$jumin.'\'
							   and voucher_yymm  = \''.$liYear.$liMonth.'\'
							   and del_flag      = \'N\'';
					$laVouInfo = $conn->get_array($sql);

					if (is_array($laVouInfo)){
						/*********************************************************
							장애인활동지원(금액)
						*********************************************************/
						$sql = 'select sum(t01_conf_suga_value)
								  from t01iljung
								 where t01_ccode      = \''.$code.'\'
								   and t01_mkind      = \''.$svcCd.'\'
								   and t01_jumin      = \''.$jumin.'\'
								   and t01_status_gbn = \'1\'
								   and t01_bipay_umu != \'Y\'
								   and t01_del_yn     = \'N\'
								   and left(t01_sugup_date, 6) = \''.$liYear.$liMonth.'\'';

						$liCost   = $laVouInfo['suga_cost'];
						$liUsePay  = intval($conn->get_data($sql));
						$liUseTime = round($liUsePay / $liCost, 1);

						$laTmpPay = array(0, 0);

						for($i=0; $i<2; $i++){
							$leftPay  = $laVouInfo['pay'.$i];
							$leftTime = $leftPay / $liCost;

							if ($leftTime - $liUseTime > 0){
								$time = $leftTime - $liUseTime;
								$laTmpPay[$i] = round($time * $liCost);

								break;
							}else{
								$liUseTime   -= floor($leftTime);
								$pay          = floor($leftTime) * $liCost;
								$laTmpPay[$i] = $leftPay - $pay;
							}
						}

						$liMonthUse = $laTmpPay[0];

						/*********************************************************
							장애인활동지원(금액)
						*********************************************************/
						$row['overpay']  = $liMonthUse;
						$row['overtime'] = $liMonthUse / $liCost;
					}
					$ynLoad = 'N';
				}else{
					$ynLoad = 'Y';
				}

				if ($row['gbn'] == 'A')
					$row['gbn'] = '1';
				else if ($row['gbn'] == 'C')
					$row['gbn'] = '2';
				else if ($row['gbn'] == 'D')
					$row['gbn'] = '3';

				if (intval($month) == 2){
					$row['overpay']  = 0;
					$row['overtime'] = 0;
				}

				echo 'seq='.$row['seq'].
					'&gbn='.$row['gbn'].
					'&lvl='.$row['lvl'].
					'&svcKind='.$row['svc_kind'].
					'&addPay1='.$row['addpay1'].
					'&addPay2='.$row['addpay2'].
					'&overTime='.$row['overtime'].
					'&overPay='.$row['overpay'].
					'&addTime1='.$row['addtime1'].
					'&addTime2='.$row['addtime2'].
					'&makeTime='.$row['maketime'].
					'&makePay='.$row['makepay'].
					'&addTime='.$row['addtime'].
					'&addPay='.$row['addpay'].
					'&totalTime='.$row['totaltime'].
					'&totalPay='.$row['totalpay'].
					'&monthTime='.$row['monthtime'].
					'&monthPay='.$row['monthpay'].
					'&cost='.$row['cost'];
				break;
		}

		//일정등록 갯수
		$sql = 'select count(*)
				  from t01iljung
				 where t01_ccode  = \''.$code.'\'
				   and t01_mkind  = \''.$svcCd.'\'
				   and t01_jumin  = \''.$jumin.'\'
				   and t01_del_yn = \'N\'
				   and left(t01_sugup_date,6) = \''.$year.$month.'\'';

		$liCalendarCnt = $conn->get_data($sql);

		echo '&calendarCnt='.$liCalendarCnt;
		echo '&ynLoad='.$ynLoad;



	/*********************************************************
	 * 바우처 생성
	 *********************************************************/
	}else if ($mode == 301){
		parse_str($_POST['para'],$val);

		switch($svcCd){
			case '1':
				$sugaCd = 'VH001';
				break;

			case '2':
				if ($val['gbn'] == '1')
					$val['gbn'] = 'V';
				else if ($val['gbn'] == '2')
					$val['gbn'] = 'D';
				else if ($val['gbn'] == '3')
					$val['gbn'] = 'S';
				else
					$val['gbn'] = 'X';

				if ($val['gbn'] != 'X'){
					$lsGbn = $val['gbn'];
				}else{
					if ($val['val'] == '1'){
						$lsGbn = 'V';
					}else{
						$lsGbn = 'D';
					}
				}

				$sugaCd = 'VO'.$lsGbn.'01';
				break;

			case '3':
				$sugaCd = 'VM'.$val['gbn'].'01';
				break;

			case '4':
				
				if ($val['gbn'] == '1')
					$val['gbn'] = 'A';
				else if ($val['gbn'] == '2')
					$val['gbn'] = 'C';
				else if ($val['gbn'] == '3')
					$val['gbn'] = 'D';
				else
					$val['gbn'] = 'X';
				
				if ($val['gbn'] != 'X'){
					$lsGbn = $val['gbn'];
				}else{
					if ($val['val'] == '1'){
						$lsGbn = 'A';
					}else if ($val['val'] == '3'){
						$lsGbn = 'D';
					}else{
						$lsGbn = 'C';
					}
				}
				
				
				//소득등급
				$sql = 'select level
						  from client_his_lvl
						 where org_no = \''.$code.'\'
						   and jumin  = \''.$jumin.'\'
						   and svc_cd = \''.$svcCd.'\'
						   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
						   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						 order by seq desc';

				$val['svcKind'] = $conn->get_data($sql);
				
				if($lsGbn == 'D'){
					$sugaCd = 'VA'.$lsGbn.($val['lvl']<10 ? '0'.$val['lvl'] : $val['lvl']);
				}else {
					$sugaCd = 'VA'.$lsGbn.$val['lvl'].'0';
				}
				
				break;
		}

		//수가 및 단가
		$sql = 'select service_code as code
				,      service_cost as cost
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_kind = \''.$svcCd.'\'
				   and service_code = \''.$sugaCd.'\'
				   and date_format(service_from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(service_to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by service_from_dt desc, service_to_dt desc
				 limit 1';

		$laSugaList = $conn->get_array($sql);

		$conn->begin();

		if ($seq > 0){
			$sql = 'update voucher_make';

			switch($svcCd){
				case '1':
					$sql .= ' set voucher_lvl   = \''.$val['lvl'].'\'
							,     voucher_overtime  = \''.$val['over'].'\'
							,     voucher_maketime  = \''.$val['time'].'\'
							,     voucher_totaltime = \''.($val['time']+$val['over']).'\'
							,     voucher_month_time = case when voucher_kind = \'1\' then voucher_overtime+voucher_maketime else 0 end';
					break;

				case '2':
					$sql .= ' set voucher_gbn        = \''.$val['gbn'].'\'
							,     voucher_lvl        = \''.$val['lvl'].'\'
							,     voucher_overtime   = \''.$val['over'].'\'
							,     voucher_maketime   = \''.$val['time'].'\'
							,     voucher_totaltime  = \''.($val['time']+$val['over']).'\'
							,     voucher_month_time = case when voucher_kind = \'2\' or voucher_kind = \'4\' then voucher_overtime+voucher_maketime else 0 end';
					break;

				case '3':
					$sql .= ' set voucher_gbn   = \''.$val['gbn'].'\'
							,     voucher_maketime  = \''.$val['days'].'\'
							,     voucher_totaltime = \''.$val['days'].'\'';
					break;

				case '4':
					$sql .= ' set voucher_gbn         = \''.$val['gbn'].'\'
							,     voucher_lvl         = \''.$val['lvl'].'\'
							,     voucher_svc_kind    = \''.$val['svcKind'].'\'
							,     voucher_gbn2        = \''.$val['addPay1'].'\'
							,     voucher_add_pay_gbn = \''.$val['addPay2'].'\'
							,     voucher_overtime    = \''.$val['overTime'].'\'
							,     voucher_overpay     = \''.$val['overPay'].'\'
							,     voucher_addtime1    = \''.$val['sidoTime'].'\'
							,     voucher_addtime2    = \''.$val['jachTime'].'\'
							,     voucher_maketime    = \''.$val['makeTime'].'\'
							,     voucher_makepay     = \''.$val['makePay'].'\'
							,     voucher_addtime     = \''.$val['addTime'].'\'
							,     voucher_addpay      = \''.$val['addPay'].'\'
							,     voucher_totaltime   = \''.$val['totalTime'].'\'
							,     voucher_totalpay    = \''.$val['totalPay'].'\'
							,     voucher_month_time  = \''.$val['totalTime'].'\'
							,     voucher_month_pay   = \''.$val['totalPay'].'\'';
					break;

			}

			$sql .= ',       voucher_suga_cd   = \''.$laSugaList['code'].'\'
					 ,       voucher_suga_cost = \''.$laSugaList['cost'].'\'
					 ,       update_id         = \''.$_SESSION['userCode'].'\'
					 ,       update_dt         = now()
					   where org_no            = \''.$code.'\'
						 and voucher_kind      = \''.$svcCd.'\'
						 and voucher_jumin     = \''.$jumin.'\'
						 and voucher_yymm      = \''.$year.$month.'\'
						 and voucher_seq       = \''.$seq.'\'';
			
		}else{
			$sql = 'select ifnull(max(voucher_seq),0)+1
					  from voucher_make
					 where org_no        = \''.$code.'\'
					   and voucher_kind  = \''.$svcCd.'\'
					   and voucher_jumin = \''.$jumin.'\'
					   and voucher_yymm  = \''.$year.$month.'\'';

			$seq = $conn->get_data($sql);

			$sql = 'insert into voucher_make (
					 org_no
					,voucher_kind
					,voucher_jumin
					,voucher_yymm
					,voucher_seq';

			switch($svcCd){
				case '1':
					$sql .= ',voucher_lvl
							 ,voucher_overtime
							 ,voucher_maketime
							 ,voucher_totaltime
							 ,voucher_month_time';
					break;

				case '2':
					$sql .= ',voucher_gbn
							 ,voucher_lvl
							 ,voucher_overtime
							 ,voucher_maketime
							 ,voucher_totaltime
							 ,voucher_month_time';
					break;

				case '3':
					$sql .= ',voucher_gbn
							 ,voucher_maketime
							 ,voucher_totaltime';
					break;

				case '4':
					$sql .= ',voucher_gbn
							 ,voucher_lvl
							 ,voucher_svc_kind
							 ,voucher_gbn2
							 ,voucher_add_pay_gbn
							 ,voucher_overtime
							 ,voucher_overpay
							 ,voucher_addtime1
							 ,voucher_addtime2
							 ,voucher_maketime
							 ,voucher_makepay
							 ,voucher_addtime
							 ,voucher_addpay
							 ,voucher_totaltime
							 ,voucher_totalpay
							 ,voucher_month_time
							 ,voucher_month_pay';
					break;
			}

			$sql .= ',voucher_suga_cd
					 ,voucher_suga_cost
					 ,insert_id
					 ,insert_dt) values (
					 \''.$code.'\'
					,\''.$svcCd.'\'
					,\''.$jumin.'\'
					,\''.$year.$month.'\'
					,\''.$seq.'\'';

			switch($svcCd){
				case '1':
					$sql .= ',\''.$val['lvl'].'\'
							 ,\''.$val['over'].'\'
							 ,\''.$val['time'].'\'
							 ,\''.($val['time']+$val['over']).'\'
							 ,\''.($val['time']+$val['over']).'\'';
					break;

				case '2':
					$sql .= ',\''.$val['gbn'].'\'
							 ,\''.$val['lvl'].'\'
							 ,\''.$val['over'].'\'
							 ,\''.$val['time'].'\'
							 ,\''.($val['time']+$val['over']).'\'
							 ,\''.($val['time']+$val['over']).'\'';
					break;

				case '3':
					$sql .= ',\''.$val['gbn'].'\'
							 ,\''.$val['days'].'\'
							 ,\''.$val['days'].'\'';
					break;

				case '4':
					$sql .= ',\''.$val['gbn'].'\'
							 ,\''.$val['lvl'].'\'
							 ,\''.$val['svcKind'].'\'
							 ,\''.$val['addPay1'].'\'
							 ,\''.$val['addPay2'].'\'
							 ,\''.$val['overTime'].'\'
							 ,\''.$val['overPay'].'\'
							 ,\''.$val['sidoTime'].'\'
							 ,\''.$val['jachTime'].'\'
							 ,\''.$val['makeTime'].'\'
							 ,\''.$val['makePay'].'\'
							 ,\''.$val['addTime'].'\'
							 ,\''.$val['addPay'].'\'
							 ,\''.$val['totalTime'].'\'
							 ,\''.$val['totalPay'].'\'
							 ,\''.$val['totalTime'].'\'
							 ,\''.$val['totalPay'].'\'';
					break;
			}

			$sql .= ',\''.$laSugaList['code'].'\'
					 ,\''.$laSugaList['cost'].'\'
					 ,\''.$_SESSION['userCode'].'\'
					 ,now())';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo 9;
			 if ($conn->mode == 1) exit;
		}

		$sql = 'UPDATE	voucher_make
				SET		del_flag		= \'Y\'
				WHERE	org_no			= \''.$code.'\'
				AND		voucher_kind	= \''.$svcCd.'\'
				AND		voucher_jumin	= \''.$jumin.'\'
				AND		voucher_yymm	= \''.$year.$month.'\'
				AND		voucher_seq		< \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo 9;
			 if ($conn->mode == 1) exit;
		}

		$conn->commit();

		echo 1;

	}else if ($mode == 901){
		//삭제
		$sql = 'delete
				  from voucher_make
				 where org_no        = \''.$code.'\'
				   and voucher_kind  = \''.$svcCd.'\'
				   and voucher_jumin = \''.$jumin.'\'
				   and voucher_yymm  = \''.$year.$month.'\'
				   and voucher_seq   = \''.$seq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 echo 9;
			 if ($conn->mode == 1) exit;
		}

		$conn->commit();

		echo 1;
	}

	include_once('../inc/_db_close.php');
?>
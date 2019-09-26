<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	define(__ROW_CUTER__, ';;');
	define(__COL_CUTER__, '//');
	define(_HOUR_, 60);

	$type = $_REQUEST['type']; //작업구분

	switch($type){
		case 'suga_info':
			/*
			$code  = $_REQUEST['code'];
			$kind  = $_REQUEST['svc_kind'];
			$suga  = $_REQUEST['suga_cd'];
			$date  = $_REQUEST['date'] != '' ? $_REQUEST['date'] : date('Ymd', mktime());
			$c_cd  = $ed->de($_REQUEST['c_cd']);
			$bipay = $_REQUEST['bipay_yn'];

			$sql = 'select service_gbn
					,      service_lvl
					,      service_cost
					,      service_cost_night
					,      service_cost_holiday
					  from suga_service
					 where org_no           = \''.$code.'\'
					   and service_kind     = \''.$kind.'\'
					   and service_code     = \''.$suga.'\'
					   and service_from_dt <= \''.$date.'\'
					   and service_to_dt   >= \''.$date.'\'';

			$value = $conn->get_array($sql);

			if ($bipay == 'Y'){
				$sql = 'select m03_bipay1
						  from m03sugupja
						 where m03_ccode = \''.$code.'\'
						   and m03_mkind = \''.$kind.'\'
						   and m03_jumin = \''.$c_cd.'\'';

				$tmp_cost = $conn->get_data($sql);

				if (!empty($tmp_cost)){
					$value[2] = $tmp_cost;
					$value[3] = $tmp_cost;
					$value[4] = $tmp_cost;
				}

				unset($tmp_cost);
			}

			echo $value[0].__COL_CUTER__.$value[1].__COL_CUTER__.$value[2].__COL_CUTER__.$value[3].__COL_CUTER__.$value[4];
			*/
			$code    = $_REQUEST['code'];
			$kind    = $_REQUEST['svc_kind'];
			$suga    = $_REQUEST['suga_cd'];
			$date    = $_REQUEST['date'] != '' ? $_REQUEST['date'] : date('Ymd', mktime());
			$c_cd    = $ed->de($_REQUEST['c_cd']);
			$bipay   = $_REQUEST['bipay_yn'];
			$holiday = $conn->_is_holiday($date);

			if (date('w', strtotime($date)) == 0) $holiday = true;

			$value = get_suga_info($conn, $code, $kind, $suga, $date, $c_cd, $bipay);
			echo $value[0].__COL_CUTER__.$value[1].__COL_CUTER__.$value[2].__COL_CUTER__.$value[3].__COL_CUTER__.$value[4].__COL_CUTER__.$holiday.__COL_CUTER__;

			break;

		case 'suga_care':
			$code = $_REQUEST['code'];
			$suga = $_REQUEST['suga_cd'];
			$date = $_REQUEST['date'] != '' ? $_REQUEST['date'] : date('Ymd', mktime());

			/*
			$sql = 'select nm, suga, bipay
					  from (
						   select m01_suga_cont as nm
						   ,      m01_suga_value as suga
						   ,      m01_suga_cvalue1 as bipay
						   ,      m01_sdate as from_dt
						   ,      m01_edate as to_dt
							 from m01suga
							where m01_mcode  = \''.$code.'\'
							  and m01_mcode2 = \''.$suga.'\'
							union all
						   select m11_suga_cont
						   ,      m11_suga_value
						   ,      m11_suga_cvalue1
						   ,      m11_sdate
						   ,      m11_edate
							 from m11suga
							where m11_mcode  = \''.$code.'\'
							  and m11_mcode2 = \''.$suga.'\'
							union all
						   select service_gbn
						   ,      service_cost
						   ,      service_bipay
						   ,      service_from_dt
						   ,      service_to_dt
							from suga_service
						   where org_no       = \''.$code.'\'
							 and service_code = \''.$suga.'\'
						   ) as t
					 where from_dt <= \''.$date.'\'
					   and to_dt   >= \''.$date.'\'';

			$value = $conn->get_array($sql);
			*/
			$value = get_suga_care($conn, $code, $suga, $date);

			echo $value[0].__COL_CUTER__.$value[1].__COL_CUTER__.$value[2].__COL_CUTER__;

			break;


		/**************************************************

			바우처 수가

		**************************************************/
		case 'suga_voucher':
			$para = array('mode'		=>	$_REQUEST['mode']
						 ,'code'		=>	$_REQUEST['code']
						 ,'kind'		=>	$_REQUEST['kind']
						 ,'svc_id'		=>	$conn->kind_code($conn->kind_list($_REQUEST['code'], true), $_REQUEST['kind'], 'id')
						 ,'svc_cd'		=>	$_REQUEST['svc_cd']
						 ,'suga_cd'		=>	$_REQUEST['suga_cd']
						 ,'c_cd'		=>	$ed->de($_REQUEST['c_cd'])
						 ,'bipay_yn'	=>	$_REQUEST['bipay_yn']
						 ,'date'		=>	$_REQUEST['date']
						 ,'from_time'	=>	$myF->time2min($_REQUEST['from_time'])
						 ,'to_time'		=>	$myF->time2min($_REQUEST['to_time'])
						 ,'m_cnt'		=>	$_REQUEST['m_cnt']
						 ,'bipay_pay'	=>	$_REQUEST['bipay_pay']);

			$value = get_suga_voucher($conn, $para);
			echo $value[0].__COL_CUTER__.$value[1].__COL_CUTER__.$value[2].__COL_CUTER__;

			break;
	}

	include_once('../inc/_db_close.php');



	/**************************************************

		수가정보

	**************************************************/
	function get_suga_info($conn, $code, $kind, $suga, $date, $c_cd, $bipay){
		$sql = 'select service_gbn
				,      service_lvl
				,      service_cost
				,      service_cost_night
				,      service_cost_holiday
				  from suga_service
				 where org_no       = \''.$code.'\'
				   and service_kind = \''.$kind.'\'
				   and service_code = \''.$suga.'\'
				   and left(service_from_dt,'.strlen($date).') <= \''.$date.'\'
				   and left(service_to_dt,  '.strlen($date).') >= \''.$date.'\'';

		$value = $conn->get_array($sql);

		if ($bipay == 'Y'){
			$sql = 'select m03_bipay1
					  from m03sugupja
					 where m03_ccode = \''.$code.'\'
					   and m03_mkind = \''.$kind.'\'
					   and m03_jumin = \''.$c_cd.'\'';

			$tmp_cost = $conn->get_data($sql);

			if (!empty($tmp_cost)){
				$value[2] = $tmp_cost;
				$value[3] = $tmp_cost;
				$value[4] = $tmp_cost;
			}

			unset($tmp_cost);
		}

		return $value;
	}



	/*********************************************************



	*********************************************************/
	function get_suga_care($conn, $code, $suga, $date){
		$sql = 'select nm, suga, bipay
				  from (
					   select m01_suga_cont as nm
					   ,      m01_suga_value as suga
					   ,      m01_suga_cvalue1 as bipay
					   ,      replace(m01_sdate,\'-\',\'\') as from_dt
					   ,      replace(m01_edate,\'-\',\'\') as to_dt
						 from m01suga
						where m01_mcode  = \''.$code.'\'
						  and m01_mcode2 = \''.$suga.'\'
						union all
					   select m11_suga_cont
					   ,      m11_suga_value
					   ,      m11_suga_cvalue1
					   ,      replace(m11_sdate,\'-\',\'\')
					   ,      replace(m11_edate,\'-\',\'\')
						 from m11suga
						where m11_mcode  = \''.$code.'\'
						  and m11_mcode2 = \''.$suga.'\'
						union all
					   select service_gbn
					   ,      service_cost
					   ,      service_bipay
					   ,      replace(service_from_dt,\'-\',\'\')
					   ,      replace(service_to_dt,\'-\',\'\')
						from suga_service
					   where org_no       = \''.$code.'\'
						 and service_code = \''.$suga.'\'
					   ) as t
				 where left(from_dt,'.strlen($date).') <= \''.$date.'\'
				   and left(to_dt,'.strlen($date).')   >= \''.$date.'\'';

		$value = $conn->get_array($sql);

		return $value;
	}



	/**************************************************

		바우처 수가정보

	**************************************************/
	function get_suga_voucher($conn, $para){
		$mode	   = $para['mode'];
		$code	   = $para['code'];
		$kind	   = $para['kind'];
		$svc_id    = $para['svc_id'];
		$svc_cd    = $para['svc_cd'];
		$suga_cd   = $para['suga_cd'];
		$c_cd	   = $para['c_cd'];
		$bipay_yn  = $para['bipay_yn'];
		$date	   = $para['date'];
		$from_time = $para['from_time'];
		$to_time   = $para['to_time'];
		$m_cnt	   = $para['m_cnt'];
		$bipay_pay = $para['bipay_pay'];


		/*********************************************************
			수가기본정보
		*********************************************************/
		$suga_if    = get_suga_info($conn, $code, $kind, $suga_cd, $date, $c_cd, $bipay);
		$suga_nm    = $suga_if[0].($svc_cd == '500' || $svc_cd == '800' ? '('.$suga_if[1].')' : ''); //수가명
		$suga_stnd  = $suga_if[2]; //수가 기본단가
		$suga_night = $suga_if[3]; //수가 연장단가

		if (empty($suga_night)) $suga_night = $suga_stnd;



		$suga_cnt_night = 0; //연장시간
		$extrapay       = 0; //방문수당

		if ($bipay_yn != 'Y'){
			if ($svc_cd == '500' ||
				$svc_cd == '800'){
				/**************************************************
					방문목욕, 방문간호는 회당 수가로 처리한다.
				**************************************************/
				$suga_cnt  = 1;          //단가를 맞출 횟수
				$suga_cost = $suga_stnd; //수가 단가

				//방문수당
				$sql = 'select ifnull(m21_svalue, 0)
						  from m21sudang
						 where m21_mcode  = \''.$code.'\'
						   and m21_mcode2 = \''.$suga_cd.'\'';
				$extrapay = $conn->get_data($sql);

			}else{
				if ($svc_id == 24){
					/**************************************************
						장애활동지원
					**************************************************/
					$proctimes = $to_time - $from_time; //진행시간

					$hour_stnd    = 0; //기준시간
					$hour_prolong = 0; //연장시간

					if ($proctimes < 0) $proctimes += (24 * _HOUR_);

					$time_list[0] = array(6 * _HOUR_, 22 * _HOUR_);
					$time_list[1] = array(22 * _HOUR_, 6 * _HOUR_ + 24 * _HOUR_);
					$time_list[2] = array(0, 6 * _HOUR_);

					if (($from_time >= $time_list[1][0] && $from_time < $time_list[1][1]) ||
						($from_time >= $time_list[2][0] && $from_time < $time_list[2][1])){
						/**************************************************
							시작이 22시 이후부터 연장수가를 적용한다.
						**************************************************/
						if ($from_time >= $time_list[1][0] && $from_time < $time_list[1][1]){
							$index = 1;
						}else{
							$index = 2;
						}

						if ($from_time + $proctimes > $time_list[$index][1]){
							$hour_prolong = $time_list[$index][1] - $from_time;
						}else{
							$hour_prolong = $proctimes;
						}

					}else if (($to_time >= $time_list[1][0] && $to_time < $time_list[1][1]) ||
							  ($to_time >= $time_list[2][0] && $to_time < $time_list[2][1])){
						/**************************************************
							종료가 22시를 넘어가면 연장수가를 적용한다.
						**************************************************/
						if ($to_time >= $time_list[1][0]){
							$tmp_time = $to_time;
						}else{
							$tmp_time = $to_time + 24 * _HOUR_;
						}

						$hour_prolong = $tmp_time - $time_list[1][0];

					}else{
						$hour_stnd = $proctimes;
					}

					/**************************************************
						연장 최대시간은 4시간으로 제한한다.
					**************************************************/
					if ($hour_prolong > 4 * _HOUR_) $hour_prolong = 4 * _HOUR_;

					$hour_stnd = $proctimes - $hour_prolong;

					$suga_cnt       = round($hour_stnd / _HOUR_);    //기준시간
					$suga_cnt_night = round($hour_prolong / _HOUR_); //연장시간

					if ($m_cnt > 1){
						$suga_stnd = $suga_stnd * 1.5; //수가 단가
					}

					$suga_cost = $suga_stnd * $suga_cnt;

					if ($suga_cnt_night > 0){
						$suga_cost += $suga_night * $suga_cnt_night;
					}
				}else{
					$sql = 'select voucher_suga_cost as svc_cost
							  from voucher_make
							 where org_no        = \''.$code.'\'
							   and voucher_kind  = \''.$kind.'\'
							   and voucher_jumin = \''.$c_cd.'\'
							   and voucher_yymm  = \''.substr($date, 0, 6).'\'
							   and del_flag      = \'N\'';
					$svc_cost  = $conn->get_data($sql); //단가
					$suga_cnt  = $svc_cost / $suga_stnd; //단가를 맞출 횟수
					$suga_cost = $suga_stnd * $suga_cnt;        //수가 단가
				}
			}
		}else{
			$suga_stnd = $bipay_pay;

			if ($svc_cd == '500' ||
				$svc_cd == '800'){
				$suga_cnt  = 1; //단가를 맞출 횟수
				$suga_cost = $suga_stnd * $suga_cnt; //수가 단가
			}else{
				$suga_cnt  = round(($to_time - $from_time) / _HOUR_);
				$suga_cost = $suga_stnd * $suga_cnt; //수가 단가
			}
		}

		$value[0] = $suga_cd;
		$value[1] = $suga_nm;
		$value[2] = $suga_cost;

		return $value;
	}
?>












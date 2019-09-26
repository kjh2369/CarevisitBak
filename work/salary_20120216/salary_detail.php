<?
	// 말일
	$month_lastday = $myF->lastDay($year, $month);

	/*
	// 요양보호사 월급제
	$sql = "select m02_yjumin
			,      m02_yname
			,      m02_ygibonkup
			  from m02yoyangsa
			 where m02_ccode        = '$code'
			   and m02_mkind        = '$kind'
			   and m02_ygupyeo_kind = '3'
			   and m02_del_yn       = 'N'
			 order by m02_yjumin";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$mem_index = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mem[$mem_index] = init_array($row['m02_yjumin'], $year.$month.'01'); //배열 초기화
	}

	$conn->row_free();

	$mem_index = sizeof($mem);
	*/

	// 요양보호사 급여내역
	$sql = "select center_code
			,      center_kind
			,      mem_main
			,      mem_sub
			,      work_date
			,      weekly
			,      service_cd
			,      family_yn
			,      normal_work_cnt
			,      fimaly_work_cnt
			,      work_times as work_times
			,      bath_cnt
			,      nursing_cnt
			,      hour(from_time)    * 60 + minute(from_time)    as from_time
			,     (hour(end_time)     * 60 + minute(end_time)) + case when (hour(from_time) * 60 + minute(from_time)) > (hour(end_time) * 60 + minute(end_time)) then 24 * 60 else 0 end as to_time
			,      hour(end_time)     * 60 + minute(end_time)     as end_time
			,      hour(from_prolong) * 60 + minute(from_prolong) as from_prolong
			,      hour(to_prolong)   * 60 + minute(to_prolong)   as to_prolong
			,      hour(from_night)   * 60 + minute(from_night)   as from_night
			,      hour(to_night)     * 60 + minute(to_night)     as to_night
			,      extra_yn
			,      extra_pay
			,      extra_rate_1
			,      extra_rate_2
			,      client_rate
			,      suga_amt
			,      client_kind
			  from (
				   select t01_ccode as center_code
				   ,      t01_mkind as center_kind
				   ,      t01_yoyangsa_id1 as mem_main
				   ,      t01_yoyangsa_id2 as mem_sub
				   ,      t01_conf_date as work_date
				   ,      dayofweek(date_format(t01_conf_date, '%Y-%m-%d')) - 1 as weekly
				   ,      t01_svc_subcode as service_cd
				   ,      t01_toge_umu as family_yn
				   ,      case when t01_toge_umu = 'Y' then 0 else 1 end as normal_work_cnt
				   ,      case when t01_toge_umu = 'Y' then 1 else 0 end as fimaly_work_cnt
				   ,      t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then 30 else 0 end as work_times
				   ,      case when t01_svc_subcode = '500' then 1 else 0 end bath_cnt
				   ,      case when t01_svc_subcode = '800' then 1 else 0 end nursing_cnt
				   ,      /*date_format(concat(t01_conf_date, t01_conf_fmtime, '00'), '%Y-%m-%d %H:%i') as from_time*/
				          addtime(date_format(concat(t01_conf_date, t01_conf_fmtime, '00'), '%Y-%m-%d %H:%i'), case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then '0:30:0' else '0:0:0' end) as from_time
				   ,      date_format(concat(t01_conf_date, t01_conf_totime, '00'), '%Y-%m-%d %H:%i') as to_time
				   ,      date_add(date_format(concat(t01_conf_date, t01_conf_fmtime, '00'), '%Y-%m-%d %H:%i'), interval t01_conf_soyotime minute) as end_time
				   ,      date_format(concat(t01_conf_date, '180000'), '%Y-%m-%d %H:%i') as from_prolong
				   ,      date_format(concat(t01_conf_date, '215900'), '%Y-%m-%d %H:%i') as to_prolong
				   ,      date_format(concat(t01_conf_date, '220000'), '%Y-%m-%d %H:%i') as from_night
				   ,      date_add(date_format(concat(t01_conf_date, '060000'), '%Y-%m-%d %H:%i'), interval 1 day) as to_night
				   ,      t01_ysudang_yn as extra_yn
				   ,      case t01_ysudang_yn when 'Y' then t01_ysudang else 0 end as extra_pay
				   ,      t01_ysudang_yul1 as extra_rate_1
				   ,      t01_ysudang_yul2 as extra_rate_2
				   ,      sugupja.yul as client_rate
				   ,	  t01_conf_suga_value as suga_amt  /*t01_suga_tot as suga_amt*/
				   ,      sugupja.kind as client_kind
					 from t01iljung
					inner join (
						  select m03_jumin as jumin, m03_ylvl as kind, m03_bonin_yul as yul, m03_sdate as sdate, m03_edate as edate
							from m03sugupja
						   where m03_ccode  = '$code'
							 and m03_del_yn = 'N'
						   union all
						  select m31_jumin as jumin, m31_level as kind, m31_bonin_yul as yul, m31_sdate as sdate, m31_edate as edate
							from m31sugupja
						   where m31_ccode = '$code') as sugupja
					   on t01_jumin = sugupja.jumin
					  and t01_conf_date between sugupja.sdate and sugupja.edate
					where t01_ccode         = '$code'
					  and t01_conf_date like  '$year$month%'
					  and t01_status_gbn    = '1'
					  and t01_del_yn        = 'N'";

	if ($is_preview == true){
	}else{
		$sql .= "	  and (select count(*)
							 from t13sugupja
						    where t13_ccode = '$code'
							  and t13_mkind = '$kind'
							  and t13_jumin = t01_jumin
							  and t13_pay_date like '$year$month%'
							  and t13_type = '2') > 0";
	}


	for($i=1; $i<=$month_lastday; $i++){
		$temp_day = ($i<10?'0':'').$i;
		$temp_dt = $year.$month.$temp_day;
		if ($myF->weekday($temp_dt) != '일'){
			$holiday_flag = false;
			for($j=0; $j<$holiday_index; $j++){
				if ($holiday_list[$j]['date'] == $temp_dt){
					$holiday_flag = true;
					break;
				}
			}

			if(!$holiday_flag){
				$sql .= "	    union all
							   select m02_ccode as center_code
							   ,      m02_mkind as center_kind
							   ,      m02_yjumin as mem_main
							   ,      '' as mem_sub
							   ,      '".$year.$month.$temp_day."' as work_date
							   ,      dayofweek(date_format('".$year.$month.$temp_day."', '%Y-%m-%d')) - 1 as weekly
							   ,      'mon' as service_cd
							   ,      'N' as family_yn
							   ,      0 as normal_work_cnt
							   ,      0 as fimaly_work_cnt
							   ,      ".$day_work_hour." as work_times
							   ,      0 bath_cnt
							   ,      0 nursing_cnt
							   ,      '".$year."-".$month."-".$temp_day." 23:59' as from_time
							   ,      '".$year."-".$month."-".$temp_day." 23:59' as to_time
							   ,      0 as end_time
							   ,      0 as from_prolong
							   ,      0 as to_prolong
							   ,      0 as from_night
							   ,      0 as to_night
							   ,      'N' as extra_yn
							   ,      0 as extra_pay
							   ,      0 as extra_rate_1
							   ,      0 as extra_rate_2
							   ,      0.0 as client_rate
							   ,	  0 as suga_amt
							   ,      'mon' as client_kind
								 from m02yoyangsa
								where m02_ccode        = '$code'
								  and m02_mkind        = '$kind'
								  and m02_ygupyeo_kind = '3'
								  and m02_ygoyong_stat = '1'
								  and m02_del_yn       = 'N'
								  and left(m02_yipsail, 6) <= '".$year.$month."'
								  and left(case when ifnull(m02_ytoisail, '') != '' then m02_ytoisail else '99991231' end, 6) >= '".$year.$month."'";
				/*
				$sql .= "	    union all
							   select m02_ccode as center_code
							   ,      m02_mkind as center_kind
							   ,      m02_yjumin as mem_main
							   ,      '' as mem_sub
							   ,      '".$year.$month.$temp_day."' as work_date
							   ,      dayofweek(date_format('".$year.$month.$temp_day."', '%Y-%m-%d')) - 1 as weekly
							   ,      'mon' as service_cd
							   ,      'N' as family_yn
							   ,      0 as normal_work_cnt
							   ,      0 as fimaly_work_cnt
							   ,      ".$day_work_hour." as work_times
							   ,      0 bath_cnt
							   ,      0 nursing_cnt
							   ,      '".$year."-".$month."-".$temp_day." 23:59' as from_time
							   ,      '".$year."-".$month."-".$temp_day." 23:59' as to_time
							   ,      0 as end_time
							   ,      0 as from_prolong
							   ,      0 as to_prolong
							   ,      0 as from_night
							   ,      0 as to_night
							   ,      'N' as extra_yn
							   ,      0 as extra_pay
							   ,      0 as extra_rate_1
							   ,      0 as extra_rate_2
							   ,      0.0 as client_rate
							   ,	  0 as suga_amt
							   ,      'mon' as client_kind
								 from m02yoyangsa
								inner join mem_salary
								   on org_no = m02_ccode
								  and ms_jumin = m02_yjumin
								  and ms_from_dt <= '".$year.$month."'
								  and ms_to_dt >= '".$year.$month."'
								  and del_flag    = 'N'
								where m02_ccode        = '$code'
								  and m02_mkind        = '$kind'
								  and m02_del_yn       = 'N'
								  and left(m02_yipsail, 6) <= '".$year.$month."'
								  and left(case when ifnull(m02_ytoisail, '') != '' then m02_ytoisail else '99991231' end, 6) >= '".$year.$month."'";
				*/
			}
		}
	}

	$sql .= "	   ) as t
			 order by mem_main, work_date, from_time, to_time";

	//if ($debug) echo nl2br($sql);

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$mem_index = 0;
	$sub_index = 0;

	$work_time_list = array();
	$holiday_rate	= array();
	$holiday_date	= array();
	$holiday_add_amt_flag = array();

	// 요양보호사 근무시간 계산
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($is_preview == true){
			if ($row['mem_main'] == $member_code || $row['mem_sub'] == $member_code){
				$is_add_falg = true;
			}else{
				$is_add_falg = false;
			}
		}else{
			$is_add_falg = true;
		}

		if ($is_add_falg == true){
			if ($temp_mem != $row['mem_main'].'_'.$row['work_date']){
				$temp_mem  = $row['mem_main'].'_'.$row['work_date'];
				$mem[$mem_index] = init_array($row['mem_main'], $row['work_date']); //배열 초기화

				$m = $mem_index;
				$mem_index ++;
			}

			if ($row['family_yn'] == 'Y'){
				$mem[$m]['client_gbn'] = 'family';
				$gbn  = 'family';
				$gbn2 = 'family';
			}else{
				if ($row['client_kind'] == 'mon'){
					$mem[$m]['client_gbn'] = 'normal';
					$gbn  = $row['client_kind'];
					$gbn2 = $row['client_kind'];
				}else{
					$mem[$m]['client_gbn'] = 'normal';
					$gbn  = $row['client_kind'].'_normal';
					$gbn2 = 'normal';
				}
			}

			if ($row['service_cd'] == '200' || ($row['service_cd'] == '500' && $bath_add_yn == 'Y') || ($row['service_cd'] == '800' && $nursing_add_yn)){ //요양 및 목욕, 간호는 할증이 인정된 경우만
				// 연장시간 범위
				/*
				$prolong_limit_time = (8 - (($row['from_prolong'] / 60) - ($row['from_time'] / 60)));

				if ($prolong_limit_time >= 8 || $prolong_limit_time <= 0) $prolong_limit_time = 0;

				$prolong_limit_time = $prolong_limit_time * 60;
				*/
				// 18시~22시 특별수당을 추가하면서 연장시간범위를 조정하던 부분을 제거함.
				$prolong_limit_time = 0;

				$prolong_from = $row['from_time'] - $row['from_prolong']; //연장시작시간
				$prolong_to   = $row['to_time']   - $row['from_prolong']; //연장종료시간

				if ($prolong_from < 0) $prolong_from = 0;
				if ($prolong_to   < 0) $prolong_to   = 0;

				// 야간시간 범위
				if ($row['from_time'] < $row['to_night']){
					$night_from = $row['to_night'] - $row['from_time']; //야간시작시간
					$night_to   = $row['to_night'] - $row['to_time'];   //야간종료시간

					if ($night_from < 0) $night_from = 0;
					if ($night_to   < 0) $night_to   = 0;

					$night_hour = $night_from - $night_to; //야간시간
				}else{
					$night_from = $row['from_time'] - $row['from_night']; //야간시작시간
					$night_to   = $row['to_time']   - $row['from_night']; //야간종료시간

					if ($night_from < 0) $night_from = 0;
					if ($night_to   < 0) $night_to   = 0;

					$night_hour = $night_to - $night_from; //야간시간
				}

				$prolong_hour = $prolong_to - $prolong_from /*- $night_hour*/; //연장시간
				$prolong_hour = $prolong_hour - $prolong_limit_time;

				if ($prolong_hour < 0) $prolong_hour = 0;

				if ($row['service_cd'] == '200'){
					$work_times = $row['work_times'];
				}else{
					$work_times	= 0;
				}
			}else if ($row['service_cd'] == 'mon'){
				//$work_times = $row['work_times'] * 60;
				$work_times = 0;
			}else{
				$work_times	  = 0;
				$prolong_hour = 0;
				$night_hour   = 0;
			}

			$work_time_list[$row['mem_main']][$row['work_date']] += $work_times;

			$holiday_flag = false; //휴일여부
			$holiday_pay  = false; //휴일 유급여부

			if ($holiday_index > 0){
				/*********************************************************
					법정공휴일을 인정한다.
				*********************************************************/
				for($j=0; $j<$holiday_index; $j++){
					if ($holiday_list[$j]['date'] == $row['work_date']){
						$holiday_flag = true;

						if ($holiday_list[$j]['pay'] == 'Y') $holiday_pay = true;

						break;
					}
				}
			}else{
				/*********************************************************
					법정공휴일을 인정하지 않는다.
				*********************************************************/
				$tmp_holiday_flag = false;
				$tmp_holiday_pay  = false;

				for($j=0; $j<$tmp_holiday_index; $j++){
					if ($tmp_holiday_list[$j]['date'] == $row['work_date']){
						$tmp_holiday_flag = true;

						if ($tmp_holiday_list[$j]['pay'] == 'Y') $tmp_holiday_pay = true;

						break;
					}
				}
			}

			// 휴일이면 휴일일수를 추가한다.
			if ($holiday_flag){
				$mem[$m]['holiday_yn']	= 'Y';	//휴일플래그
				// 아래서 유급일수 계산위해 주석으로 막는다.
				//$mem[$m]['paid_yn']		= $holiday_pay ? 'Y' : 'N';
				$mem[$m][$gbn.'_work_time']				+= $work_times;	//근무시간(휴일근무시간도 포함)
				$mem[$m][$gbn.'_holiday_hour']			+= $work_times;	//휴일시간
				$mem[$m][$gbn.'_holiday_prolong_hour']	+= $prolong_hour;	//휴일연장시간
				$mem[$m][$gbn.'_holiday_night_hour']	+= $night_hour;		//휴일야간시간

				if ($holiday_pay){
					$holiday_rate[$row['work_date']] = 'Y';
				}else{
					$holiday_rate[$row['work_date']] = 'N';
				}

				$holiday_date[$row['work_date']] = 'Y';
			}else{
				$mem[$m][$gbn.'_work_time']		+= $work_times;	//근무시간
				$mem[$m][$gbn.'_prolong_hour']	+= $prolong_hour;	//연장시간
				$mem[$m][$gbn.'_night_hour']	+= $night_hour;		//야간시간

				$holiday_rate[$row['work_date']] = 'N';
				$holiday_date[$row['work_date']] = 'N';


				/*********************************************************
					법정공휴일을 인정하지 않을 때 휴일이 오면
				*********************************************************/
				if ($tmp_holiday_flag){
					$mem[$m]['holiday_yn_not'] = 'Y';

					if ($tmp_holiday_pay){
						$holiday_rate[$row['work_date']] = 'Y';
					}else{
						$holiday_rate[$row['work_date']] = 'N';
					}

					$holiday_date[$row['work_date']] = 'Y';
				}
			}

			$mem[$m]['weekday'] = $row['weekly']; //요일을 담는다.

			$mem[$m]['bath_cnt']	+= $row['bath_cnt'];	//목욕 횟수
			$mem[$m]['nursing_cnt']	+= $row['nursing_cnt'];	//간호 횟수


			/*********************************************************
				목욕, 간호시간 여부
			*********************************************************/
			$mem[$m]['bath_time']	 += ($row['bath_cnt'] > 0 ? 60 : 0);	//목욕시간
			$mem[$m]['nursing_time'] += ($row['nursing_cnt'] > 0 ? $row['work_times'] : 0);	//간호 횟수

			if ($row['extra_yn'] == 'Y'){
				/*********************************************************
					2011년 7월부터 목욕 수당 감액을 적용한다.
				*********************************************************/
				if ($row['work_date'] >= '20110701'){
					if ($row['work_times'] < 40){
						// 40분 미만시 전액 감액한다.
						$extra_pay = 0;
					}else if ($row['work_times'] >= 40 && $row['work_times'] < 60){
						// 40분이상 60분 미만시 80%만 산정한다.
						$extra_pay = $row['extra_pay'] * 0.8;
					}else{
						// 60분이상시 감액은 없다.
						$extra_pay = $row['extra_pay'];
					}
				}else{
					$extra_pay = $row['extra_pay'];
				}

				/*********************************************************
					차감여부가 아니오 이면 다시 수당을 돌린다.
				*********************************************************/
				if ($bath_cut_yn != 'Y') $extra_pay = $row['extra_pay'];

				if ($row['service_cd'] == '500') $mem[$m]['bath_amt']		+= ($extra_pay * ($row['extra_rate_1'] / 100));	//목욕수당
				if ($row['service_cd'] == '800') $mem[$m]['nursing_amt']	+=  $row['extra_pay'];	//간호수당

				if ($row['service_cd'] == '500'){
					if ($row['mem_sub'] != ''){
						$sub[$sub_index]['jumin']     = $row['mem_sub'];
						$sub[$sub_index]['date']	  = $row['work_date'];
						$sub[$sub_index]['bath_amt'] += ($extra_pay * ($row['extra_rate_2'] / 100));
						$sub_index ++;
					}
				}
			}

			$mem[$m]['client_rate']	= $row['client_rate'];	//본인부담율

			if ($row['service_cd'] == '200'){
				$mem[$m][$gbn2.'_suga']			+= $row['suga_amt'];	//총수가금액
				//$mem[$m][$gbn2.'_suga_except']	+= ($row['suga_amt'] - ($row['suga_amt'] * $row['client_rate'] / 100));
				$mem[$m][$gbn2.'_suga_except']	+= $row['suga_amt'];
			}
		}
	}

	$conn->row_free();

	unset($temp_mem);

	$mem_index = 0;
	$mem_count = sizeof($mem);
	$sub_count = sizeof($sub);

	// 유급일수
	$temp_paid_cnt = 0;
	for($k=0; $k<$holiday_index; $k++){
		if ($holiday_list[$k]['pay'] == 'Y'){
			$temp_paid_cnt ++;
		}
	}

	if ($sub_count > 0){
		for($i=0; $i<$sub_count; $i++){
			$add_flag = false;
			for($j=0; $j<$mem_count; $j++){
				if ($mem[$j]['jumin'] == $sub[$i]['jumin'] &&
					$mem[$j]['date']  == $sub[$i]['date']){
					$mem[$j]['bath_cnt']  ++;
					$mem[$j]['bath_amt']  += $sub[$i]['bath_amt']; //목욕기본급(기준시급)을 뺀다
					$mem[$j]['bath_time'] += 60;
					$add_flag = true;
					break;
				}
			}

			if (!$add_flag){
				//$j = sizeof($mem) - 1;
				$j = sizeof($mem);
				$mem[$j] = init_array($sub[$i]['jumin'], $sub[$i]['date']);
				$mem[$j]['bath_cnt']  ++;
				$mem[$j]['bath_amt']  += $sub[$i]['bath_amt'];
				$mem[$j]['bath_time'] += 60;
				$mem_count = sizeof($mem);
			}
		}
	}

	$mem_temp = $myF->sortArray($mem, 'jumin', 1);
	$mem = $mem_temp;

	unset($mem_temp);

	for($i=0; $i<$mem_count; $i++){
		if ($temp_mem != $mem[$i]['jumin']){
			if ($temp_mem != ''){
				// 이전 보양보호사의 근무분을 시간으로 변경
				$member[$m] = set_hour2minute($member[$m], $pay_list_my[$temp_mem]['kind'], $pay_list_my[$temp_mem]['family_yn'], $day_work_hour);
			}

			$temp_mem = $mem[$i]['jumin'];

			$member[$mem_index] = init_array($mem[$i]['jumin']);	//배열 초기화
			$member[$mem_index]['week_cnt'] = $week_count;			//주휴갯수

			$m = $mem_index;
			$mem_index ++;
		}

		// 월급제 시간
		//$member[$m]['mon_work_time'] += $mem[$i]['mon_work_time'];

		if ($mem[$i]['holiday_yn'] == 'Y'){	//휴일근무일수
			$member[$m][$mem[$i]['client_gbn'].'_holiday_cnt'] ++;
			$holiday_add_amt_flag[$m] = 'Y';
		}else{	//평일근무일수
			$member[$m][$mem[$i]['client_gbn'].'_work_cnt'] ++;
			$holiday_add_amt_flag[$m] = 'N';
		}

		// 유급휴일근무일수
		//if ($mem[$i]['paid_yn'] == 'Y') $member[$m]['paid_cnt'] ++;

		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			if ($mem[$i]['holiday_yn']     == 'Y' || /*법정공휴일을 인정한 휴일*/
				$mem[$i]['holiday_yn_not'] == 'Y' || /*법정공휴일을 인정하지 않지만 휴일수당을 등록하기위해*/
				$mem[$i]['weekday']        == 0)     /*일요일*/{
				/*********************************************************
					공휴일 및 일요일 특별수당
				*********************************************************/
				#if ($mem[$i]['holiday_yn'] == 'Y'){
				#	$member[$m][$lvl.'_holiday_add_hour'] += $mem[$i][$lvl.'_normal_holiday_prolong_hour'];
				#}else{
				#	$member[$m][$lvl.'_sunday_add_hour'] += $mem[$i][$lvl.'_normal_holiday_prolong_hour'];
				#}


				/*********************************************************
					2011.11.18
					- 공휴일 및 일요일 특별수당 수정
				*********************************************************/


				/*********************************************************
					일요일 법정공휴일로 인정하면 근무시간을 휴일수당으로
					계산하므로 특별수당을 계산할 필요가 없다.

					하지만, 일요일을 법정공휴일로 인정하지 않으면
					일요일 특별수당인정 비율로 일요일 특별수당을 계산한다.
				*********************************************************/
				if ($pay_list_my[$mem[$i]['jumin']]['holiday_pay_yn'] == 'Y' ||
					$pay_list_my[$mem[$i]['jumin']]['holiday_pay_yn'] == 'H'){

					if ($mem[$i]['holiday_yn'] == 'Y'){
					}else{
						$member[$m][$lvl.'_holiday_add_hour'] += $mem[$i][$lvl.'_normal_work_time'];
						$member[$m][$lvl.'_holiday_add_hour'] += $mem[$i][$lvl.'_normal_prolong_hour'];
						$member[$m][$lvl.'_holiday_add_hour'] += $mem[$i][$lvl.'_normal_night_hour'];
					}
				}else if ($pay_list_my[$mem[$i]['jumin']]['holiday_pay_yn'] == 'Y' ||
						  $pay_list_my[$mem[$i]['jumin']]['holiday_pay_yn'] == 'S'){

					if ($mem[$i]['holiday_yn'] == 'Y'){
					}else{
						$member[$m][$lvl.'_sunday_add_hour'] += $mem[$i][$lvl.'_normal_work_time'];
						$member[$m][$lvl.'_sunday_add_hour'] += $mem[$i][$lvl.'_normal_prolong_hour'];
						$member[$m][$lvl.'_sunday_add_hour'] += $mem[$i][$lvl.'_normal_night_hour'];
					}
				}
			}else{
				/*********************************************************
					평일 18~22시 특별수당시간을 추가한다.
				*********************************************************/
				if ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60){
					if ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] - (8 * 60) >= $mem[$i][$lvl.'_normal_prolong_hour']){
						//일근무시간이 8시간 이상 근무후면 모두 연장시간으로 돌린다.
					}else{
						// 연장시간중 18~22시의 근무시간의 연장에 포함되지 않은 시간만 특별연장으로 돌린다.
						$member[$m][$lvl.'_add_hour'] += ($mem[$i][$lvl.'_normal_prolong_hour'] - ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] - (8 * 60)) - $mem[$i][$lvl.'_normal_night_hour']);

						// 특별연장시간을 제외한 시간만 연장시간으로 돌린다.
						$mem[$i][$lvl.'_normal_prolong_hour'] = $work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] - (8 * 60);
					}
				}else{
					// 일근무시간이 8시간이 안되면 특별연장시간으로 돌린다.
					$member[$m][$lvl.'_add_hour'] += $mem[$i][$lvl.'_normal_prolong_hour'];
				}
			}

			$member[$m][$lvl.'_normal_work_time']	+= $mem[$i][$lvl.'_normal_work_time'];
			$member[$m][$lvl.'_normal_prolong_hour']+= ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60 ? $mem[$i][$lvl.'_normal_prolong_hour'] : 0);
			$member[$m][$lvl.'_normal_night_hour']	+= $mem[$i][$lvl.'_normal_night_hour'];

			$member[$m][$lvl.'_normal_holiday_hour']		+= $mem[$i][$lvl.'_normal_holiday_hour'];
			$member[$m][$lvl.'_normal_holiday_prolong_hour']+= ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60 ? $mem[$i][$lvl.'_normal_holiday_prolong_hour'] : 0);
			$member[$m][$lvl.'_normal_holiday_night_hour']	+= $mem[$i][$lvl.'_normal_holiday_night_hour'];
		}

		$member[$m]['bath_cnt']		+= $mem[$i]['bath_cnt'];
		$member[$m]['nursing_cnt']	+= $mem[$i]['nursing_cnt'];

		$member[$m]['bath_amt']		+= $mem[$i]['bath_amt'];
		$member[$m]['nursing_amt']	+= $mem[$i]['nursing_amt'];

		$member[$m]['bath_time']	+= $mem[$i]['bath_time'];
		$member[$m]['nursing_time']	+= $mem[$i]['nursing_time'];

		// 18~22시 추가수당시간
		if ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60){
		}else{
			// 동거는 특별수당을 하지 않는다.
			//$member[$m]['add_hour'] += $mem[$i]['family_prolong_hour'];
		}

		$member[$m]['family_work_time']				+= $mem[$i]['family_work_time'];
		//$member[$m]['family_prolong_hour']			+= ($mem[$i]['family_work_time'] > 8 * 60 ? $mem[$i]['family_prolong_hour'] : 0);
		$member[$m]['family_prolong_hour']			+= ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60 ? $mem[$i]['family_prolong_hour'] : 0);
		$member[$m]['family_night_hour']			+= $mem[$i]['family_night_hour'];

		$member[$m]['family_holiday_hour']			+= $mem[$i]['family_holiday_hour'];
		//$member[$m]['family_holiday_prolong_hour']	+= ($mem[$i]['family_holiday_hour'] > 8 * 60 ? $mem[$i]['family_holiday_prolong_hour'] : 0);
		$member[$m]['family_holiday_prolong_hour']	+= ($work_time_list[$mem[$i]['jumin']][$mem[$i]['date']] > 8 * 60 ? $mem[$i]['family_holiday_prolong_hour'] : 0);
		$member[$m]['family_holiday_night_hour']	+= $mem[$i]['family_holiday_night_hour'];

		$member[$m]['normal_suga']	+= $mem[$i]['normal_suga'];
		$member[$m]['family_suga']	+= $mem[$i]['family_suga'];
		$member[$m]['normal_suga_except'] += $mem[$i]['normal_suga_except'];
		$member[$m]['family_suga_except'] += $mem[$i]['family_suga_except'];

		$member[$m]['client_rate']	= $mem[$i]['client_rate'];

		//$holiday_add_amt_flag[$m] = $holiday_rate[$mem[$i]['date']];
		//if ($holiday_add_amt_flag[$m] != 'Y') $holiday_add_amt_flag[$m] = $holiday_date[$mem[$i]['date']];
	}

	$member[$m] = set_hour2minute($member[$m], $pay_list_my[$temp_mem]['kind'], $pay_list_my[$temp_mem]['family_yn'], $day_work_hour);

	unset($mem);

	// 추가 수당 및 공제
	for($j=1; $j<=2; $j++){
		$tmp_addon_amt[$j] = 0;
		for($k=0; $k<sizeof($salary_addon[$j]); $k++){
			$tmp_addon_amt[$j] += $salary_addon[$j][$k]['pay'];
		}
	}

	$mem_count = sizeof($member);

	for($i=0; $i<$mem_count; $i++){
		$jumin	  = $member[$i]['jumin'];
		$pay_gbn  = $pay_list_my[$jumin];	//급여구분

		//최소시급
		$member[$i]['min_hourly'] = $day_hourly;

		//일근무시간
		$member[$i]['day_work_hour'] = $day_work_hour;

		$member[$i]['paid_cnt'] = $temp_paid_cnt; //유급일수
		$member[$i]['ins_yn']   = $pay_gbn['ins_yn']; //4대보험여부

		if ($pay_gbn['bn_yn'] == 'Y'){
			$member[$i]['bath_amt']		= 0;	//목욕과 간호수당이 급여에 포함이면 수당을 주지 않는다.
			$member[$i]['nursing_amt']	= 0;	//
		}else{
			/*********************************************************
				목욕, 간호 수당에서 기본금을 제한다.
			*********************************************************/
			if ($member[$i]['bn_time_yn'] == 'Y'){
				$member[$i]['bath_amt']    -= ($member[$i]['bath_time']    / 60 * $member[$i]['min_hourly']);
				$member[$i]['nursing_amt'] -= ($member[$i]['nursing_time'] / 60 * $member[$i]['min_hourly']);
			}

			$member[$i]['bath_amt']		= round($member[$i]['bath_amt'], -1);		//목욕수당반올림
			$member[$i]['nursing_amt']	= round($member[$i]['nursing_amt'], -1);	//간호수당반올림
		}

		if ($pay_gbn['kind'] == '1' || $pay_gbn['kind'] == '2'){	//시급
			if ($pay_gbn['type'] == 'Y'){	//고정급
				$member[$i]['salary_type'] = 1;
			}else{	//변동급
				$member[$i]['salary_type'] = 2;
			}
		}else if ($pay_gbn['kind'] == '3'){	//월급
			$member[$i]['salary_type'] = 3;

			if ($pay_gbn['type'] == 'Y'){
				/*********************************************************
					포괄임금제 사용시 아래 주석 해제할것.
				*********************************************************/
				//$member[$i]['salary_com'] = 1; //포괄임금제
			}
		}else if ($pay_gbn['kind'] == '3'){	//총액비율
			$member[$i]['salary_type'] = 4;
		}else{
			$member[$i]['min_hourly']    = 0;
			$member[$i]['day_work_hour'] = 0;
		}

		// 월급제 시간
		$member[$i]['mon_work_time'] = $member[$i]['mon_work_time'] / 60;

		//직급수당
		$member[$i]['rank_pay'] = $pay_gbn['rank_pay'];



		//기본급 = 최소시급 * 근무시간
		//$member[$i]['base_pay'] = round($member[$i]['min_hourly'] * $member[$i]['total_work_time'], -1);

		// 포괄임금제 여부
		if ($member[$i]['salary_com'] == 1){
			// 초과근무 시간 초기화
			for($j=1; $j<=4; $j++){
				$lvl = ($j == 4 ? 9 : $j);

				$member[$i][$lvl.'_normal_work_time']				= 0;
				$member[$i][$lvl.'_normal_prolong_hour']			= 0;
				$member[$i][$lvl.'_normal_night_hour']				= 0;

				$member[$i][$lvl.'_normal_holiday_hour']			= 0;
				$member[$i][$lvl.'_normal_holiday_prolong_hour']	= 0;
				$member[$i][$lvl.'_normal_holiday_night_hour']		= 0;
			}

			// 포괄임금제
			$member[$i]['total_work_time'] += $member[$i]['total_com_time']; //초과근무 시간을 더한다.

			// 기본급
			$member[$i]['base_pay']  = round($member[$i]['min_hourly'] * $member[$i]['total_work_time'], -1);
			$member[$i]['base_pay'] += $mem[$i]['bath_amt'];	//목욕수당
			$member[$i]['base_pay'] += $mem[$i]['nursing_amt'];	//간호수당

			$member[$i]['bath_cnt']		= 0;
			$member[$i]['nursing_cnt']	= 0;

			$member[$i]['bath_amt']		= 0;
			$member[$i]['nursing_amt']	= 0;
		}else{
			/*********************************************************

				기본급
				- 최소시급 * 근무시간

			*********************************************************/
			$member[$i]['base_pay'] = round($member[$i]['min_hourly'] * $member[$i]['total_work_time'], -1);
		}

		//echo $holiday_add_amt_flag[$i].'<br>';

		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			$member[$i][$lvl.'_normal_prolong_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_prolong_hour']		* __PROLONG_RATE__,			-1);	//연장수당
			$member[$i][$lvl.'_normal_night_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_night_hour']			* __NIGHT_RATE__,			-1);	//야간수당
			//$member[$i][$lvl.'_normal_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_hour']		* (($holiday_add_amt_flag[$i] == 'Y' ? 1 : 0) + __HOLIDAY_RATE__),			-1);	//휴일수당
			$member[$i][$lvl.'_normal_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_hour']		* __HOLIDAY_RATE__,			-1);	//휴일수당
			$member[$i][$lvl.'_normal_holiday_prolong_pay']	= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_prolong_hour']* __HOLIDAY_PROLONG_RATE__, -1);	//휴연수당
			$member[$i][$lvl.'_normal_holiday_night_pay']	= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_night_hour']	* __HOLIDAY_NIGHT_RATE__,	-1);	//휴야수당

			$member[$i]['normal_sudang_amt']	+= ($member[$i][$lvl.'_normal_prolong_pay']
												+   $member[$i][$lvl.'_normal_night_pay']
												+   $member[$i][$lvl.'_normal_holiday_pay']
												+   $member[$i][$lvl.'_normal_holiday_prolong_pay']
												+   $member[$i][$lvl.'_normal_holiday_night_pay']);
		}

		//echo $member[$i]['1_normal_holiday_hour'].'/'.$member[$i]['2_normal_holiday_hour'].'/'.$member[$i]['3_normal_holiday_hour'].'/'.$member[$i]['9_normal_holiday_hour'].'<br>';
		//echo $member[$i]['family_holiday_hour'].'<br>';

		/*
		$member[$i]['family_prolong_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_prolong_pay']			* __PROLONG_RATE__,			-1);	//연장수당
		$member[$i]['family_night_pay']				= round($member[$i]['min_hourly'] * $member[$i]['family_night_pay']				* __NIGHT_RATE__,			-1);	//야간수당
		//$member[$i]['family_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_pay']			* (($holiday_add_amt_flag[$i] == 'Y' ? 1 : 0) + __HOLIDAY_RATE__),			-1);	//휴일수당
		$member[$i]['family_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_pay']			* (1 + __HOLIDAY_RATE__),			-1);	//휴일수당
		$member[$i]['family_holiday_prolong_pay']	= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_prolong_pay']	* __HOLIDAY_PROLONG_RATE__,	-1);	//휴연수당
		$member[$i]['family_holiday_night_pay']		= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_night_pay']		* __HOLIDAY_NIGHT_RATE__,	-1);	//휴야수당
		*/
		$member[$i]['family_prolong_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_prolong_hour']			* __PROLONG_RATE__,			-1);	//연장수당
		$member[$i]['family_night_pay']				= round($member[$i]['min_hourly'] * $member[$i]['family_night_hour']			* __NIGHT_RATE__,			-1);	//야간수당
		//$member[$i]['family_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_hour']			* (($holiday_add_amt_flag[$i] == 'Y' ? 1 : 0) + __HOLIDAY_RATE__),			-1);	//휴일수당
		$member[$i]['family_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_hour']			* __HOLIDAY_RATE__,			-1);	//휴일수당
		$member[$i]['family_holiday_prolong_pay']	= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_prolong_hour']	* __HOLIDAY_PROLONG_RATE__,	-1);	//휴연수당
		$member[$i]['family_holiday_night_pay']		= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_night_hour']	* __HOLIDAY_NIGHT_RATE__,	-1);	//휴야수당

		$member[$i]['family_sudang_amt']	+= ($member[$i]['family_prolong_pay']
											+   $member[$i]['family_night_pay']
											+   $member[$i]['family_holiday_pay']
											+   $member[$i]['family_holiday_prolong_pay']
											+   $member[$i]['family_holiday_night_pay']);

		// 18~22시 추가수당
		if (($pay_gbn['add_rate'] > 0 || $pay_gbn['holiday_rate'] > 0) && ($member[$i]['salary_type'] == 1 || $member[$i]['salary_type'] == 2)){
			//$member[$i]['add_pay'] = round($member[$i]['min_hourly'] * ($member[$i]['add_hour'] / 60) * $pay_gbn['add_rate'] / 100, -1);
			for($j=1; $j<=4; $j++){
				$lvl = ($j == 4 ? 9 : $j);

				// 추가수당은 요양보호사의 시급으로 계산한다.(그외는 기준시급으로 계산함.)
				if ($member[$i]['salary_type'] == 1){
					$tmp_add_hourly = $pay_gbn['pay'];
				}else{
					$tmp_add_hourly = $pay_list_lvl[$jumin][$lvl];
				}

				#$member[$i]['add_pay'] += round($tmp_add_hourly * ($member[$i][$lvl.'_add_hour'] / 60) * $pay_gbn['add_rate'] / 100, -1);

				/************************************************************
					평일 특별근무수당
				************************************************************/
				$member[$i]['add_pay'] += round($tmp_add_hourly * ($member[$i][$lvl.'_add_hour'] / 60) * $pay_gbn['add_rate'] / 100, -1);

				/************************************************************
					공휴일 특별근무수당
				************************************************************/
				if ($pay_gbn['holiday_pay_yn'] == 'Y' || $pay_gbn['holiday_pay_yn'] == 'H'){
					$member[$i]['holiday_add_pay'] += round($tmp_add_hourly * ($member[$i][$lvl.'_holiday_add_hour'] / 60) * $pay_gbn['holiday_rate'] / 100, -1);
				}

				/************************************************************
					일요일 특별근무수당
				************************************************************/
				if ($pay_gbn['holiday_pay_yn'] == 'Y' || $pay_gbn['holiday_pay_yn'] == 'S'){
					$member[$i]['sunday_add_pay'] += round($tmp_add_hourly * ($member[$i][$lvl.'_sunday_add_hour'] / 60) * $pay_gbn['holiday_rate'] / 100, -1);
				}
			}
		}



		/*********************************************************

			급여계산

		*********************************************************/
		switch($member[$i]['salary_type']){
		case 1:	//고정시급
			$member[$i]['normal_hourly']	= $pay_gbn['pay'];
			$member[$i]['normal_pay']		= round($member[$i]['normal_hourly'] * $member[$i]['normal_work_time'], -1);
			break;
		case 2:	//변동시급
			$member[$i]['1_normal_hourly']	= $pay_list_lvl[$jumin]['1'];
			$member[$i]['2_normal_hourly']	= $pay_list_lvl[$jumin]['2'];
			$member[$i]['3_normal_hourly']	= $pay_list_lvl[$jumin]['3'];
			$member[$i]['9_normal_hourly']	= $pay_list_lvl[$jumin]['9'];
			$member[$i]['normal_pay'] = round(($member[$i]['1_normal_hourly'] * $member[$i]['1_normal_work_time'])
									  +		  ($member[$i]['2_normal_hourly'] * $member[$i]['2_normal_work_time'])
									  +		  ($member[$i]['3_normal_hourly'] * $member[$i]['3_normal_work_time'])
									  +		  ($member[$i]['9_normal_hourly'] * $member[$i]['9_normal_work_time']), -1);	//타수급 총급여
			break;
		case 3:	//월급
			$member[$i]['normal_salary']	= $pay_gbn['pay'];	//급여
			$member[$i]['normal_pay']		= $member[$i]['normal_salary'];

			/*
			if ($member[$i]['1_normal_work_time']+$member[$i]['2_normal_work_time']+$member[$i]['3_normal_work_time']+$member[$i]['9_normal_work_time']+$member[$i]['family_work_time'] < $month_lastday * $member[$i]['day_work_hour']){
				$member[$i]['1_normal_work_time'] = $month_lastday * $member[$i]['day_work_hour'];
			}
			*/
			break;
		case 4:	//총액비율
			$member[$i]['normal_rate']	= $pay_gbn['rate'];	//총액비율
			$member[$i]['normal_pay']	= round($member[$i]['normal_suga_except'] * ($member[$i]['normal_rate'] / 100), -1);
			break;
		}



		/*********************************************************

			동거가족 급여

		*********************************************************/
		if ($pay_gbn['family_yn'] == 'Y'){	//동거가족 총급여
			$member[$i]['family_yn']	= $pay_gbn['family_yn'];
			$member[$i]['family_type']	= $pay_gbn['family_type'];
			//$member[$i]['family_pay']	= $pay_gbn['family_pay'];
			/*
			 * family_type 설정
			 * 1 : 시급
			 * 2 : 총액비율
			 * 3 : 월급
			 */
			switch($member[$i]['family_type']){
			case 1:
				$member[$i]['family_pay'] = round($member[$i]['family_work_time'] * $pay_gbn['family_pay'], -1);
				break;
			case 2:
				$member[$i]['family_rate']= $pay_gbn['family_pay'];
				$member[$i]['family_pay'] = round($member[$i]['family_suga_except'] * ($member[$i]['family_rate'] / 100), -1);
				break;
			case 3:
				//if ($member[$i]['family_work_time'] > 0) $member[$i]['family_pay'] = round($pay_gbn['family_pay'], -1);
				//break;
			}
		}else{
			/*********************************************************
				동거가족 급여 지급방식이 없을 경우
			*********************************************************/
			//$member[$i]['normal_pay'] += round($member[$i]['family_work_time'] * $member[$i]['min_hourly']);
		}



		/*********************************************************

			동거가족 고정급은 근무여부 상관없이 지급한다.

			*****************************************************/

			if ($member[$i]['family_type'] == 3){
				$member[$i]['family_pay'] = round($pay_gbn['family_pay'], -1);
			}

		/********************************************************/




		// 포괄임금제 여부
		if ($member[$i]['salary_com'] == 1){
			$member[$i]['week_cnt'] = 0; //주휴일수
			$member[$i]['paid_cnt'] = 0; //유급일수
		}

		$member[$i]['week_amt']	= round($member[$i]['week_cnt'] * ($member[$i]['total_work_time'] / 24 * $member[$i]['min_hourly']), -1);	//주휴수당(주휴일수 * (총근무시간 / 24(한달 평균 근로일수) * 일근무기준시급))
		$member[$i]['paid_amt']	= round($member[$i]['paid_cnt'] * $member[$i]['day_work_hour'] * $member[$i]['min_hourly'], -1);			//유급수당(유급일수 * 일근무기준시간 * 일근무기준시급)

		//총급여
		$member[$i]['gross_pay'] = $member[$i]['normal_pay']
								 + $member[$i]['family_pay']
								 + $member[$i]['paid_amt'];

		// 최저임금(기본급 + 주휴수당)을 맞춘다.
		if ($member[$i]['gross_pay'] < $member[$i]['base_pay'] + $member[$i]['week_amt']){
			$member[$i]['gross_pay'] = $member[$i]['base_pay'] + $member[$i]['week_amt'];
		}

		// 보전수당 = 총급여 - (기본급 + 식대보조비 + 차량유지비)
		$temp_bojeon = round($member[$i]['gross_pay'] - ($member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['paid_amt']), -1);

		if ($temp_bojeon < 0) $temp_bojeon = 0;


		if ($pay_list_my[$jumin]['PAYE'] != 'Y'){
			/*********************************************************
				원천징수 대상자가 아닌 경우만 식대를 계산한다.
			*********************************************************/
			if ($temp_bojeon > __MAX_MEAL_AMT__){
				$member[$i]['meal_amt']	= __MAX_MEAL_AMT__;
			}else{
				$member[$i]['meal_amt']	= $temp_bojeon;
			}
		}

		// 보전수당
		$member[$i]['bojeon_pay']	= round($member[$i]['gross_pay'] - ($member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['meal_amt'] + $member[$i]['paid_amt']), -1);

		if ($member[$i]['bojeon_pay'] < 0) $member[$i]['bojeon_pay'] = 0;

		if ($is_preview == true){
			$tmp_addon_amt[1] = $addon_pay[1];
			$tmp_addon_amt[2] = $addon_pay[2];
		}

		// 계산 총급여 재계산
		$member[$i]['gross_pay'] = $member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['paid_amt'] + $member[$i]['bath_amt'] + $member[$i]['nursing_amt'] + $member[$i]['bojeon_pay'];

		//수당을 더한다.
		//$member[$i]['gross_pay'] += ($member[$i]['rank_pay'] + ($tmp_addon_amt[1] - $tmp_addon_amt[2]) + $member[$i]['add_pay']);
		$member[$i]['gross_pay'] += ($member[$i]['rank_pay'] + $tmp_addon_amt[1] + $member[$i]['add_pay'] + $member[$i]['holiday_add_pay'] + $member[$i]['sunday_add_pay']);



		/*
		 * 수당
		 */
		$gross_sudang = 0;

		//타수급
		$gross_sudang += ($member[$i]['1_normal_prolong_pay']			+ $member[$i]['2_normal_prolong_pay']			+ $member[$i]['3_normal_prolong_pay']			+ $member[$i]['9_normal_prolong_pay']);			//연장수당
		$gross_sudang += ($member[$i]['1_normal_night_pay']				+ $member[$i]['2_normal_night_pay']				+ $member[$i]['3_normal_night_pay']				+ $member[$i]['9_normal_night_pay']);			//야간수당
		$gross_sudang += ($member[$i]['1_normal_holiday_pay']			+ $member[$i]['2_normal_holiday_pay']			+ $member[$i]['3_normal_holiday_pay']			+ $member[$i]['9_normal_holiday_pay']);			//휴일수당
		$gross_sudang += ($member[$i]['1_normal_holiday_prolong_pay']	+ $member[$i]['2_normal_holiday_prolong_pay']	+ $member[$i]['3_normal_holiday_prolong_pay']	+ $member[$i]['9_normal_holiday_prolong_pay']);	//휴연수당
		$gross_sudang += ($member[$i]['1_normal_holiday_night_pay']		+ $member[$i]['2_normal_holiday_night_pay']		+ $member[$i]['3_normal_holiday_night_pay']		+ $member[$i]['9_normal_holiday_night_pay']);	//휴야수당

		// 동거가족
		$gross_sudang += $member[$i]['family_prolong_pay'];
		$gross_sudang += $member[$i]['family_night_pay'];
		$gross_sudang += $member[$i]['family_holiday_pay'];
		$gross_sudang += $member[$i]['family_holiday_prolong_pay'];
		$gross_sudang += $member[$i]['family_holiday_night_pay'];



		/*********************************************************
			원천징수 대상자 및 갑근세
		*********************************************************/
		if ($pay_list_my[$jumin]['PAYE'] == 'Y'){
			//원천징수
			$member[$i]['gapgeunse'] = $myF->cutOff(($member[$i]['gross_pay'] + $member[$i]['meal_amt'] + $gross_sudang) * __PAYE_RATE__ * 0.01);
			$member[$i]['PAYE']      = 1;
		}else{
			// 갑근세
			$member[$i]['gapgeunse'] = $check->gapgeunse($year, $member[$i]['gross_pay'], $pay_gbn['deduct1'], $pay_gbn['deduct2']);
			$member[$i]['PAYE']      = 0;
		}

		// 주민세
		$member[$i]['juminse'] = $myF->cutOff($member[$i]['gapgeunse'] * 0.1);

		if ($member[$i]['ins_yn'] == 'Y'){
			/*********************************************************
				국민연금
			*********************************************************/
			if ($mon_pay[$jumin]['annuity_yn'] == 'Y'){
				if ($mon_pay[$jumin]['annuity'] > 0){
					$annuity_pay = $mon_pay[$jumin]['annuity'];
				}else{
					$annuity_pay = $member[$i]['gross_pay'] + $gross_sudang;
				}
			}else{
				$annuity_pay = 0;
			}

			/*********************************************************
				만60세이상부터 국민연금을 내지않는다.
			*********************************************************/
			$m_age = $myF->man_age($jumin, $month, $year);
			if ($m_age >= 60) $annuity_pay = 0;



			if ($mon_pay[$jumin]['health_yn'] == 'Y'){
				if ($mon_pay[$jumin]['health'] > 0){
					$health_pay  = $mon_pay[$jumin]['health'];
				}else{
					$health_pay  = $member[$i]['gross_pay'] + $gross_sudang;
				}
			}else{
				$health_pay = 0;
			}

			if ($mon_pay[$jumin]['employ_yn'] == 'Y'){
				if ($mon_pay[$jumin]['employ'] > 0){
					$employ_pay  = $mon_pay[$jumin]['employ'];
				}else{
					$employ_pay  = $member[$i]['gross_pay'] + $gross_sudang;
				}
			}else{
				$employ_pay = 0;
			}

			if ($mon_pay[$jumin]['sanje_yn'] == 'Y'){
				if ($mon_pay[$jumin]['sanje'] > 0){
					$sanje_pay   = $mon_pay[$jumin]['sanje'];
				}else{
					$sanje_pay   = $member[$i]['gross_pay'] + $gross_sudang + $member[$i]['meal_amt'];
				}
			}else{
				$sanje_pay = 0;
			}

			/*
			 * 2011.06.07 / 국민연금 월 신고급여액으로 계산하기 위해 변경됨.
			$member[$i]['worker_employ']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['worker_employ']	* 0.01);	//고용보험 본인부담
			$member[$i]['worker_health']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['worker_health']	* 0.01);	//건강보험 본인부담
			$member[$i]['worker_oldcare']	= $myF->cutOff($member[$i]['worker_health'] * $ins_rate['worker_oldcare']	* 0.01);	//노인장기요양 본인부담
			$member[$i]['worker_annuity']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['worker_annuity']	* 0.01);	//국민연금 본인부담
			$member[$i]['center_employ']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['center_employ']	* 0.01);	//고용보험 기관부담
			$member[$i]['center_health']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['center_health']	* 0.01);	//건강보험 기관부담
			$member[$i]['center_oldcare']	= $myF->cutOff($member[$i]['center_health'] * $ins_rate['center_oldcare']	* 0.01);	//노인장기요양 기관부담
			$member[$i]['center_annuity']	= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['center_annuity']	* 0.01);	//국민연금 기관부담
			$member[$i]['center_sanje']		= $myF->cutOff($member[$i]['gross_pay']		* $ins_rate['center_sanje']		* 0.01);	//산재보험 기관부담
			$member[$i]['deduct_amt']		= $member[$i]['worker_employ']+$member[$i]['worker_health']+$member[$i]['worker_oldcare']+$member[$i]['worker_annuity'];
			*/

			$member[$i]['worker_employ']	= $myF->cutOff($employ_pay	* $ins_rate['worker_employ']	* 0.01);	//고용보험 본인부담
			$member[$i]['worker_health']	= $myF->cutOff($health_pay	* $ins_rate['worker_health']	* 0.01);	//건강보험 본인부담
			$member[$i]['worker_oldcare']	= $myF->cutOff($member[$i]['worker_health'] * $ins_rate['worker_oldcare']	* 0.01);	//노인장기요양 본인부담
			$member[$i]['worker_annuity']	= $myF->cutOff($annuity_pay	* $ins_rate['worker_annuity']	* 0.01);	//국민연금 본인부담
			$member[$i]['center_employ']	= $myF->cutOff($employ_pay	* $ins_rate['center_employ']	* 0.01);	//고용보험 기관부담
			$member[$i]['center_health']	= $myF->cutOff($health_pay	* $ins_rate['center_health']	* 0.01);	//건강보험 기관부담
			$member[$i]['center_oldcare']	= $myF->cutOff($member[$i]['center_health'] * $ins_rate['center_oldcare']	* 0.01);	//노인장기요양 기관부담
			$member[$i]['center_annuity']	= $myF->cutOff($annuity_pay	* $ins_rate['center_annuity']	* 0.01);	//국민연금 기관부담
			$member[$i]['center_sanje']		= $myF->cutOff($sanje_pay	* $ins_rate['center_sanje']		* 0.01);	//산재보험 기관부담
			$member[$i]['deduct_amt']		= $member[$i]['worker_employ']+$member[$i]['worker_health']+$member[$i]['worker_oldcare']+$member[$i]['worker_annuity'];
		}
	}

	if ($is_preview == true){
		$salary['work_cnt']		= 0;
		$salary['work_time']	= 0;
		$salary['weekly_cnt']	= 0;
		$salary['paid_cnt']		= 0;
		$salary['bath_cnt']		= 0;
		$salary['nursing_cnt']	= 0;

		$salary['base_pay']		= 0;
		$salary['weekly_pay']	= 0;
		$salary['paid_pay']		= 0;
		$salary['bath_pay']		= 0;
		$salary['nursing_pay']	= 0;
		$salary['meal_pay']		= 0;
		$salary['car_keep_pay']	= 0;
		$salary['bojeon_pay']	= 0;
		$salary['tot_basic_pay']= 0;


		$salary['prolong_hour']			= 0;
		$salary['night_hour']			= 0;
		$salary['holiday_hour']			= 0;
		$salary['holiday_prolong_hour']	= 0;
		$salary['holiday_night_hour']	= 0;
		$salary['tot_sudang_hour']		= 0;

		$salary['prolong_pay']			= 0;
		$salary['night_pay']			= 0;
		$salary['holiday_pay']			= 0;
		$salary['holiday_prolong_pay']	= 0;
		$salary['holiday_night_pay']	= 0;
		$salary['tot_sudang_pay']		= 0;
		$salary['pension_amt']			= 0;
		$salary['health_amt']			= 0;
		$salary['care_amt']				= 0;
		$salary['employ_amt']			= 0;
		$salary['tot_ins_pay']			= 0;

		$salary['tax_amt_1']	= 0;
		$salary['tax_amt_2']	= 0;
		$salary['tot_tax_pay']	= 0;

		$salary['rank_pay'] = $rank_pay;

		$member_cnt = sizeof($member);

		for($i=0; $i<$member_cnt; $i++){
			if ($member[$i]['jumin'] == $member_code){
				$member_index = $i;
				break;
			}
		}

		//$i = 0;
		$i = $member_index;


		// 특별수당 인덱스 할당
		$sql = 'select count(*)
				  from salary_addon
				 where org_no       = \''.$code.'\'
				   and salary_type  = \'1\'
				   and salary_index < \'0\'';
		$add_pay_id = $conn->get_data($sql);

		// 18~20시 추가수당
		$salary_addon[1][$add_pay_id]['pay'] = $member[$i]['add_pay'] + $member[$i]['holiday_add_pay'] + $member[$i]['sunday_add_pay'];
		$addon_pay[1] += $salary_addon[1][$add_pay_id]['pay'];

		switch($member[$i]['salary_type']){
		case 1: //고정시급
			$hourly					= $member[$i]['normal_hourly'];			//시급
			$work_time				= $member[$i]['1_normal_work_time']				+ $member[$i]['2_normal_work_time']				+ $member[$i]['3_normal_work_time']				+ $member[$i]['9_normal_work_time'];			//근무시간
			$prolong_hour			= $member[$i]['1_normal_prolong_hour']			+ $member[$i]['2_normal_prolong_hour']			+ $member[$i]['3_normal_prolong_hour']			+ $member[$i]['9_normal_prolong_hour'];			//연장근무시간
			$prolong_pay			= $member[$i]['1_normal_prolong_pay']			+ $member[$i]['2_normal_prolong_pay']			+ $member[$i]['3_normal_prolong_pay']			+ $member[$i]['9_normal_prolong_pay'];			//연장수당
			$night_hour				= $member[$i]['1_normal_night_hour']			+ $member[$i]['2_normal_night_hour']			+ $member[$i]['3_normal_night_hour']			+ $member[$i]['9_normal_night_hour'];			//야간근무시간
			$night_pay				= $member[$i]['1_normal_night_pay']				+ $member[$i]['2_normal_night_pay']				+ $member[$i]['3_normal_night_pay']				+ $member[$i]['9_normal_night_pay'];			//야간수당
			$holiday_hour			= $member[$i]['1_normal_holiday_hour']			+ $member[$i]['2_normal_holiday_hour']			+ $member[$i]['3_normal_holiday_hour']			+ $member[$i]['9_normal_holiday_hour'];			//휴일근무시간
			$holiday_pay			= $member[$i]['1_normal_holiday_pay']			+ $member[$i]['2_normal_holiday_pay']			+ $member[$i]['3_normal_holiday_pay']			+ $member[$i]['9_normal_holiday_pay'];			//휴일수당
			$holiday_prolong_hour	= $member[$i]['1_normal_holiday_prolong_hour']	+ $member[$i]['2_normal_holiday_prolong_hour']	+ $member[$i]['3_normal_holiday_prolong_hour']	+ $member[$i]['9_normal_holiday_prolong_hour'];	//휴일연장시간
			$holiday_prolong_pay	= $member[$i]['1_normal_holiday_prolong_pay']	+ $member[$i]['2_normal_holiday_prolong_pay']	+ $member[$i]['3_normal_holiday_prolong_pay']	+ $member[$i]['9_normal_holiday_prolong_pay'];	//휴연수당
			$holiday_night_hour		= $member[$i]['1_normal_holiday_night_hour']	+ $member[$i]['2_normal_holiday_night_hour']	+ $member[$i]['3_normal_holiday_night_hour']	+ $member[$i]['9_normal_holiday_night_hour'];	//휴일야간시간
			$holiday_night_pay		= $member[$i]['1_normal_holiday_night_pay']		+ $member[$i]['2_normal_holiday_night_pay']		+ $member[$i]['3_normal_holiday_night_pay']		+ $member[$i]['9_normal_holiday_night_pay'];	//휴야수당

			break;
		case 2: //변동시급
			$hourly = $member[$i]['normal_hourly'];			//시급

			for($j=1; $j<=4; $j++){
				$lvl = $j == 4 ? 9 : $j;

				$work_time				+= $member[$i][$lvl.'_normal_work_time'];			//근무시간
				$prolong_hour			+= $member[$i][$lvl.'_normal_prolong_hour'];			//연장근무시간
				$prolong_pay			+= $member[$i][$lvl.'_normal_prolong_pay'];			//연장수당
				$night_hour				+= $member[$i][$lvl.'_normal_night_hour'];			//야간근무시간
				$night_pay				+= $member[$i][$lvl.'_normal_night_pay'];			//야간수당
				$holiday_hour			+= $member[$i][$lvl.'_normal_holiday_hour'];			//휴일근무시간
				$holiday_pay			+= $member[$i][$lvl.'_normal_holiday_pay'];			//휴일수당
				$holiday_prolong_hour	+= $member[$i][$lvl.'_normal_holiday_prolong_hour'];	//휴일연장시간
				$holiday_prolong_pay	+= $member[$i][$lvl.'_normal_holiday_prolong_pay'];	//휴연수당
				$holiday_night_hour		+= $member[$i][$lvl.'_normal_holiday_night_hour'];	//휴일야간시간
				$holiday_night_pay		+= $member[$i][$lvl.'_normal_holiday_night_pay'];	//휴야수당
			}
			break;
		case 3: //월급
			$monthly = $member[$i]['normal_pay'];

			break;
		case 4: //총액비율
			$suga_rate		= $member[$i]['normal_rate'];
			$suga_total		= $member[$i]['normal_suga'];
			$suga_except	= $member[$i]['normal_suga_except'];
			$suga_pay		= $member[$i]['normal_pay'];

			break;
		}

		// 동거가족
		if ($member[$i]['family_yn'] == 'Y'){
			$member[$i]['family_type']	= $pay_gbn['family_type'];
			$member[$i]['family_pay']	= $pay_gbn['family_pay'];

			switch($member[$i]['family_type']){
			case 1: //시급
				$hourly					= $member[$i]['family_pay'];
				$work_time				= $member[$i]['family_work_time'];
				$prolong_hour			= $member[$i]['family_prolong_hour'];
				$prolong_pay			= $member[$i]['family_prolong_pay'];
				$night_hour				= $member[$i]['family_night_hour'];
				$night_pay				= $member[$i]['family_night_pay'];
				$holiday_hour			= $member[$i]['family_holiday_hour'];
				$holiday_pay			= $member[$i]['family_holiday_pay'];
				$holiday_prolong_hour	= $member[$i]['family_holiday_prolong_hour'];
				$holiday_prolong_pay	= $member[$i]['family_holiday_prolong_pay'];
				$holiday_night_hour		= $member[$i]['family_holiday_night_hour'];
				$holiday_night_pay		= $member[$i]['family_holiday_night_pay'];

				break;
			case 2: //총액비율
				$client_rate	= $member[$i]['client_rate'];
				$suga_rate		= $member[$i]['family_rate'];
				$suga_total		= $member[$i]['family_suga'];
				$suga_except	= $member[$i]['family_suga_except'];
				$suga_pay		= $member[$i]['family_pay'];

				break;
			case 3: //월급
				$monthly = $member[$i]['family_pay'];

				break;
			}
		}

		// 2.목욕, 간호 횟수 및 수당(salary_bn)
		//if ($member[$m]['bath_cnt'] > 0 || $member[$m]['nursing_cnt'] > 0){
		$bath_cnt		= $member[$i]['bath_cnt'];
		$bath_amt		= $member[$i]['bath_amt'];
		$nursing_cnt	= $member[$i]['nursing_cnt'];
		$nursing_amt	= $member[$i]['nursing_amt'];

		// 3.급여 기본 데이타
		$salary['work_cnt']		= $member[$i]['normal_work_cnt']+$member[$i]['family_work_cnt']+$member[$i]['normal_holiday_cnt']+$member[$i]['family_holiday_cnt'];
		//$salary['work_time']	= $member[$i]['1_normal_work_time']+$member[$i]['2_normal_work_time']+$member[$i]['3_normal_work_time']+$member[$i]['9_normal_work_time']+$member[$i]['family_work_time'];
		//$salary['work_time']	= $member[$i]['normal_work_time']+$member[$i]['family_work_time'];
		$salary['work_time']	= $member[$i]['total_work_time'];
		$salary['weekly_cnt']	= $member[$i]['week_cnt'];
		$salary['paid_cnt']		= $member[$i]['paid_cnt'];
		$salary['bath_cnt']		= $member[$i]['bath_cnt'];
		$salary['nursing_cnt']	= $member[$i]['nursing_cnt'];

		$salary['base_pay']		= $member[$i]['base_pay'];
		$salary['weekly_pay']	= $member[$i]['week_amt'];
		$salary['paid_pay']		= $member[$i]['paid_amt'];
		$salary['bath_pay']		= $member[$i]['bath_amt'];
		$salary['nursing_pay']	= $member[$i]['nursing_amt'];
		$salary['meal_pay']		= $member[$i]['meal_amt'];
		$salary['car_keep_pay']	= 0;
		$salary['bojeon_pay']	= $member[$i]['bojeon_pay'];
		$salary['tot_basic_pay']= $salary['base_pay']+$salary['weekly_pay']+$salary['paid_pay']+$salary['bath_pay']+$salary['nursing_pay']+$salary['meal_pay']+$salary['car_keep_pay']+$salary['bojeon_pay'];

		$salary['prolong_hour']			= $member[$i]['1_normal_prolong_hour']+$member[$i]['2_normal_prolong_hour']+$member[$i]['3_normal_prolong_hour']+$member[$i]['9_normal_prolong_hour']+$member[$i]['family_prolong_hour'];
		$salary['night_hour']			= $member[$i]['1_normal_night_hour']+$member[$i]['2_normal_night_hour']+$member[$i]['3_normal_night_hour']+$member[$i]['9_normal_night_hour']+$member[$i]['family_night_hour'];
		$salary['holiday_hour']			= $member[$i]['1_normal_holiday_hour']+$member[$i]['2_normal_holiday_hour']+$member[$i]['3_normal_holiday_hour']+$member[$i]['9_normal_holiday_hour']+$member[$i]['family_holiday_hour'];
		$salary['holiday_prolong_hour']	= $member[$i]['1_normal_holiday_prolong_hour']+$member[$i]['2_normal_holiday_prolong_hour']+$member[$i]['3_normal_holiday_prolong_hour']+$member[$i]['9_normal_holiday_prolong_hour']+$member[$i]['family_holiday_prolong_hour'];
		$salary['holiday_night_hour']	= $member[$i]['1_normal_holiday_night_hour']+$member[$i]['2_normal_holiday_night_hour']+$member[$i]['3_normal_holiday_night_hour']+$member[$i]['9_normal_holiday_night_hour']+$member[$i]['family_holiday_night_hour'];
		$salary['tot_sudang_hour']		= $salary['prolong_hour']+$salary['night_hour']+$salary['holiday_hour']+$salary['holiday_prolong_hour']+$salary['holiday_night_hour'];

		$salary['prolong_pay']			= $member[$i]['1_normal_prolong_pay']+$member[$i]['2_normal_prolong_pay']+$member[$i]['3_normal_prolong_pay']+$member[$i]['9_normal_prolong_pay']+$member[$i]['family_prolong_pay'];
		$salary['night_pay']			= $member[$i]['1_normal_night_pay']+$member[$i]['2_normal_night_pay']+$member[$i]['3_normal_night_pay']+$member[$i]['9_normal_night_pay']+$member[$i]['family_night_pay'];
		$salary['holiday_pay']			= $member[$i]['1_normal_holiday_pay']+$member[$i]['2_normal_holiday_pay']+$member[$i]['3_normal_holiday_pay']+$member[$i]['9_normal_holiday_pay']+$member[$i]['family_holiday_pay'];
		$salary['holiday_prolong_pay']	= $member[$i]['1_normal_holiday_prolong_pay']+$member[$i]['2_normal_holiday_prolong_pay']+$member[$i]['3_normal_holiday_prolong_pay']+$member[$i]['9_normal_holiday_prolong_pay']+$member[$i]['family_holiday_prolong_pay'];
		$salary['holiday_night_pay']	= $member[$i]['1_normal_holiday_night_pay']+$member[$i]['2_normal_holiday_night_pay']+$member[$i]['3_normal_holiday_night_pay']+$member[$i]['9_normal_holiday_night_pay']+$member[$i]['family_holiday_night_pay'];
		$salary['tot_sudang_pay']		= $salary['prolong_pay']+$salary['night_pay']+$salary['holiday_pay']+$salary['holiday_prolong_pay']+$salary['holiday_night_pay'];

		$salary['pension_amt']			= $member[$i]['worker_annuity'];
		$salary['health_amt']			= $member[$i]['worker_health'];
		$salary['care_amt']				= $member[$i]['worker_oldcare'];
		$salary['employ_amt']			= $member[$i]['worker_employ'];
		$salary['tot_ins_pay']			= $salary['pension_amt']+$salary['health_amt']+$salary['care_amt']+$salary['employ_amt'];

		$salary['tax_amt_1']	= $member[$i]['gapgeunse'];
		$salary['tax_amt_2']	= $member[$i]['juminse'];
		$salary['tot_tax_pay']	= $salary['tax_amt_1']+$salary['tax_amt_2'];
	}
?>
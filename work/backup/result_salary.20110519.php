<?
	include_once("../inc/_db_open.php");
	//include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_check_class.php');
	include_once('../work/salary_const.php');

	/*
	 * pos
	 * 1 : Ajax로 실행
	 * 2 : window.open으로 실행
	 */

	$pos	= $_REQUEST['pos'];
	$code	= $_REQUEST['code'];
	$kind	= $conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$code'");
	$year	= $_REQUEST['year'];
	$month	= ($_REQUEST['month'] < 10 ? '0' : '').intval($_REQUEST['month']);
	//$gubun	= $_REQUEST['gubun'];

	if ($pos == 2){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	}

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$sql = "select act_bat_conf_flag
			,      act_bat_conf_dt
			,      salary_bat_calc_flag
			,      salary_bat_calc_dt
			  from closing_progress
			 where org_no       = '$code'
			   and closing_yymm = '$year$month'";
	$temp_data = $conn->get_array($sql);

	if ($temp_data[0] != 'Y'){
		// 수급자 일괄확정이 진행되지 않았다.
		if ($pos == 1){
			echo '급여계산은 수급자 일괄확정이 선행되어야 합니다. 확인하여 주십시오.';
		}else{
			echo $myF->message('급여계산은 수급자 일괄확정이 선행되어야 합니다. 확인하여 주십시오.', 'Y', 'N', 'Y');
		}
		exit;
	}

	if ($temp_data[2] == 'Y'){
		// 급여계산이 진행되었다.
		if ($pos == 1){
			echo '이미 급여계산이 진행된 년월입니다.';
		}else{
			echo $myF->message('이미 급여계산이 진행된 년월입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	if ($pos == 1 && $temp_data[3] > date('Y-m-d', mktime())){
		// 일괄확정 진행일이 아직 되지 앟았다.
		if ($pos == 1){
			echo '아직 급여계산 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 급여계산진행일은 '.$temp_data[3].'입니다.';
		}else{
			echo $myF->message('아직 급여계산 진행일이 되지 않았습니다.\n'.$year.'년 '.$month.'월의 급여계산진행일은 '.$temp_data[3].'입니다.', 'Y', 'N', 'Y');
		}
		exit;
	}

	unset($temp_data);

	// 추가수당 및 공제항목
	$sql = "select salary_type
			,      salary_index
			,      salary_subject
			,      salary_pay
			  from salary_addon
			 where org_no = '$code'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$index[1] = -1;
	$index[2] = -1;

	$addon_pay[1] = $salary['rank_pay'];
	$addon_pay[2] = 0;

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$index[intval($row['salary_type'])] ++;

		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['index']	= $row['salary_index'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['subject']	= $row['salary_subject'];
		$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['pay']		= $row['salary_pay'];
	}

	$conn->row_free();

	$addon_1 = sizeof($salary_addon[1]);
	$addon_2 = sizeof($salary_addon[2]);

	// 최소시급
	/*
	$sql = "select g07_pay_time
			  from g07minpay
			 where g07_year = '$year'";
	$min_hourly_pay = $conn->get_data($sql);
	*/

	// 4대보험 부담비율
	include_once('../work/salary_ins.php');

	// 요양보호사 등급별 시급
	$sql = "select m02_jumin as jumin
			,      m02_gubun as lvl
			,      m02_pay as pay
			  from m02pay
			 where m02_ccode = '$code'
			   and m02_mkind = '$kind'
			 order by m02_gubun";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$pay_list_lvl[$row['jumin']][$row['lvl']] = $row['pay'];

	}

	$conn->row_free();

	// 요양보호사 급여 방법 및 시급, 총액비율
	$sql = "select m02_yjumin as jumin
			,      m02_ygupyeo_kind as kind
			,      m02_pay_type as type
			,      m02_ygibonkup as pay
			,      m02_ysuga_yoyul as rate
			,	   m02_yfamcare_umu as family_yn
			,      m02_yfamcare_type as family_type
			,      m02_yfamcare_pay as family_pay
			,      m02_ygongjeja_no as deduct1
			,      m02_ygongjejaye_no as deduct2
			,      m02_y4bohum_umu as ins_yn
			,      m02_rank_pay as rank_pay
			  from m02yoyangsa
			 where m02_ccode = '$code'
			   and m02_mkind = '$kind'
			 order by m02_yjumin";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$pay_list_my[$row['jumin']]['kind'] = $row['kind'];	//급여방식
		$pay_list_my[$row['jumin']]['type'] = $row['type'];	//고정시급여부
		$pay_list_my[$row['jumin']]['pay']  = $row['pay'];	//급여
		$pay_list_my[$row['jumin']]['rate'] = $row['rate'];	//총액비율

		// 동거가족 유무
		if ($row['family_yn'] == 'Y'){
			$pay_list_my[$row['jumin']]['family_yn']   = 'Y';
			$pay_list_my[$row['jumin']]['family_type'] = $row['family_type'];
			$pay_list_my[$row['jumin']]['family_pay']  = $row['family_pay'];
		}else{
			$pay_list_my[$row['jumin']]['family_yn']   = 'N';
			$pay_list_my[$row['jumin']]['family_type'] = '0';
			$pay_list_my[$row['jumin']]['family_pay']  = 0;
		}

		$pay_list_my[$row['jumin']]['deduct1'] = $row['deduct1'];	//공제대상 수
		$pay_list_my[$row['jumin']]['deduct2'] = $row['deduct2'];	//20세이하 자녀수

		$pay_list_my[$row['jumin']]['ins_yn'] = $row['ins_yn'];	//4대보험 여부
		$pay_list_my[$row['jumin']]['rank_pay'] = $row['rank_pay']; //직급수당
	}

	$conn->row_free();


	// 기관의 법정휴일 유무와 유급여부
	$sql = "select m00_bath_add_yn
			,      m00_nursing_add_yn
			,      m00_law_holiday_yn
			,      m00_law_holiday_pay_yn
			,      m00_day_work_hour
			,      m00_day_hourly
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$temp_data = $conn->get_array($sql);

	$bath_add_yn	= $temp_data[0]; //목욕 연.야 할증여부
	$nursing_add_yn	= $temp_data[1]; //간호 연.야 할증여부
	$holiday_yn     = $temp_data[2]; //법정휴일 여부
	$holiday_pay_yn = $temp_data[3]; //법정휴일 유무급 여부
	$day_work_hour	= $temp_data[4]; //일근무시간
	$day_hourly     = $temp_data[5]; //일기준시급

	if ($holiday_yn != 'Y') $holiday_pay_yn = 'N';

	// 법정휴일 리스트
	if ($holiday_yn == 'Y'){
		$sql = "select mdate, holiday_name
				  from tbl_holiday
				 where mdate like '$year$month%'
				 order by mdate";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$holiday_list[$i]['date'] = $row['mdate'];
			$holiday_list[$i]['pay']  = $holiday_pay_yn;
		}

		$conn->row_free();

		$sunday_list   = $myF->sunday_list($year, $month);
		$holiday_index = sizeof($holiday_list);

		// 일요일은 유급에서 제외한다.(주휴에 포함되어 있으므로...)
		for($i=0; $i<sizeof($sunday_list); $i++){
			$holiday_list[$holiday_index]['date'] = str_replace('-', '', $sunday_list[$i]);
			$holiday_list[$holiday_index]['pay']  = 'N';
			$holiday_index ++;
		}

		$holiday_index = sizeof($holiday_list);
	}else{
		$holiday_index = 0;
	}

	if ($month == '05'){//근로자의 날은 무조건 휴일처리한다.
		$holiday_list[$holiday_index]['date'] = $year.'0501';
		$holiday_list[$holiday_index]['pay']  = 'Y';
	}

	$holiday_index = sizeof($holiday_list); // 휴일 다음 인덱스

	// 기관약정휴일
	$sql = "select m06_date, m06_pay_yn
			  from m06holiday
			 where m06_ccode   = '$code'
			   and m06_date like '$year$month%'";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$holiday_list[$holiday_index]['date'] = $row['m06_date'];
		$holiday_list[$holiday_index]['pay']  = $row['m06_pay_yn'];

		$holiday_index ++;
	}

	$holiday_index = sizeof($holiday_list); // 휴일 다음 인덱스

	$conn->row_free();

	// 주일수
	$week_count = $myF->weekCount($year, $month);

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
				   ,      t01_conf_soyotime as work_times
				   ,      case when t01_svc_subcode = '500' then 1 else 0 end bath_cnt
				   ,      case when t01_svc_subcode = '800' then 1 else 0 end nursing_cnt
				   ,      date_format(concat(t01_conf_date, t01_conf_fmtime, '00'), '%Y-%m-%d %H:%i') as from_time
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
				   ,	  t01_suga_tot as suga_amt
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
					  and t01_del_yn        = 'N'
					  and t01_yoyangsa_id1 != ''
					  and (select count(*)
							 from t13sugupja
						    where t13_ccode = '$code'
							  and t13_mkind = '$kind'
							  and t13_jumin = t01_jumin
							  and t13_pay_date like '$year$month%'
							  and t13_type = '2') > 0
				   ) as t
			 order by mem_main, work_date, from_time, to_time";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$mem_index = 0;
	$sub_index = 0;

	// 요양보호사 근무시간 계산
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

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
			$mem[$m]['client_gbn'] = 'normal';
			$gbn  = $row['client_kind'].'_normal';
			$gbn2 = 'normal';
		}

		if ($row['service_cd'] == '200' || ($row['service_cd'] == '500' && $bath_add_yn == 'Y') || ($row['service_cd'] == '800' && $nursing_add_yn)){ //요양 및 목욕, 간호는 할증이 인정된 경우만
			// 연장시간 범위
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

			if ($prolong_hour < 0) $prolong_hour = 0;

			$work_times = $row['work_times'];
		}else{
			$work_times	  = 0;
			$prolong_hour = 0;
			$night_hour   = 0;
		}

		$holiday_flag = false; //휴일여부
		$holiday_pay  = false; //휴일 유급여부

		for($j=0; $j<$holiday_index; $j++){
			if ($holiday_list[$j]['date'] == $row['work_date']){
				$holiday_flag = true;

				if ($holiday_list[$j]['pay'] == 'Y') $holiday_pay = true;

				break;
			}
		}

		// 휴일이면 휴일일수를 추가한다.
		if ($holiday_flag){
			$mem[$m]['holiday_yn']	= 'Y';	//휴일플래그
			// 아래서 유급일수 계산위해 주석으로 막는다.
			//$mem[$m]['paid_yn']		= $holiday_pay ? 'Y' : 'N';
			$mem[$m][$gbn.'_holiday_hour']			+= $work_times;	//휴일시간
			$mem[$m][$gbn.'_holiday_prolong_hour']	+= $prolong_hour;	//휴일연장시간
			$mem[$m][$gbn.'_holiday_night_hour']	+= $night_hour;		//휴일야간시간
		}else{
			$mem[$m][$gbn.'_work_time']		+= $work_times;	//근무시간
			$mem[$m][$gbn.'_prolong_hour']	+= $prolong_hour;	//연장시간
			$mem[$m][$gbn.'_night_hour']	+= $night_hour;		//야간시간
		}

		$mem[$m]['bath_cnt']	+= $row['bath_cnt'];	//목욕 횟수
		$mem[$m]['nursing_cnt']	+= $row['nursing_cnt'];	//간호 횟수

		if ($row['extra_yn'] == 'Y'){
			if ($row['service_cd'] == '500') $mem[$m]['bath_amt']		+= ($row['extra_pay'] * ($row['extra_rate_1'] / 100));	//목욕수당
			if ($row['service_cd'] == '800') $mem[$m]['nursing_amt']	+=  $row['extra_pay'];	//간호수당

			if ($row['service_cd'] == '500'){
				$sub[$sub_index]['jumin']     = $row['mem_sub'];
				$sub[$sub_index]['bath_amt'] += ($row['extra_pay'] * ($row['extra_rate_2'] / 100));
				$sub_index ++;
			}
		}

		$mem[$m]['client_rate']	= $row['client_rate'];	//본인부담율

		if ($row['service_cd'] == '200'){
			$mem[$m][$gbn2.'_suga']			+= $row['suga_amt'];	//총수가금액
			$mem[$m][$gbn2.'_suga_except']	+= ($row['suga_amt'] - ($row['suga_amt'] * $row['client_rate'] / 100));
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
			for($j=0; $j<$mem_count; $j++){
				if ($mem[$j]['jumin'] == $sub[$i]['jumin']){
					$mem[$j]['bath_cnt'] ++;
					$mem[$j]['bath_amt'] += $sub[$i]['bath_amt'];
					break;
				}
			}
		}
	}

	for($i=0; $i<$mem_count; $i++){
		if ($temp_mem != $mem[$i]['jumin']){
			if ($temp_mem != ''){
				// 이전 보양보호사의 근무분을 시간으로 변경
				$member[$m] = set_hour2minute($member[$m]);
			}

			$temp_mem = $mem[$i]['jumin'];
			$member[$mem_index] = init_array($mem[$i]['jumin']);	//배열 초기화
			$member[$mem_index]['week_cnt'] = $week_count;			//주휴갯수

			$m = $mem_index;
			$mem_index ++;
		}

		if ($mem[$i]['holiday_yn'] == 'Y'){	//휴일근무일수
			$member[$m][$mem[$i]['client_gbn'].'_holiday_cnt'] ++;
		}else{	//평일근무일수
			$member[$m][$mem[$i]['client_gbn'].'_work_cnt'] ++;
		}

		// 유급휴일근무일수
		//if ($mem[$i]['paid_yn'] == 'Y') $member[$m]['paid_cnt'] ++;

		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			$member[$m][$lvl.'_normal_work_time']	+= $mem[$i][$lvl.'_normal_work_time'];
			$member[$m][$lvl.'_normal_prolong_hour']+= ($mem[$i][$lvl.'_normal_work_time'] > 8 * 60 ? $mem[$i][$lvl.'_normal_prolong_hour'] : 0);
			$member[$m][$lvl.'_normal_night_hour']	+= $mem[$i][$lvl.'_normal_night_hour'];

			$member[$m][$lvl.'_normal_holiday_hour']		+= $mem[$i][$lvl.'_normal_holiday_hour'];
			$member[$m][$lvl.'_normal_holiday_prolong_hour']+= ($mem[$i][$lvl.'_normal_holiday_hour'] > 8 * 60 ? $mem[$i][$lvl.'_normal_holiday_prolong_hour'] : 0);
			$member[$m][$lvl.'_normal_holiday_night_hour']	+= $mem[$i][$lvl.'_normal_holiday_night_hour'];
		}

		$member[$m]['bath_cnt']		+= $mem[$i]['bath_cnt'];
		$member[$m]['nursing_cnt']	+= $mem[$i]['nursing_cnt'];

		$member[$m]['bath_amt']		+= $mem[$i]['bath_amt'];
		$member[$m]['nursing_amt']	+= $mem[$i]['nursing_amt'];

		$member[$m]['family_work_time']				+= $mem[$i]['family_work_time'];
		$member[$m]['family_prolong_hour']			+= ($mem[$i]['family_work_time'] > 8 * 60 ? $mem[$i]['family_prolong_hour'] : 0);
		$member[$m]['family_night_hour']			+= $mem[$i]['family_night_hour'];
		$member[$m]['family_holiday_hour']			+= $mem[$i]['family_holiday_hour'];
		$member[$m]['family_holiday_prolong_hour']	+= ($mem[$i]['family_holiday_hour'] > 8 * 60 ? $mem[$i]['family_holiday_prolong_hour'] : 0);
		$member[$m]['family_holiday_night_hour']	+= $mem[$i]['family_holiday_night_hour'];

		$member[$m]['normal_suga']	+= $mem[$i]['normal_suga'];
		$member[$m]['family_suga']	+= $mem[$i]['family_suga'];
		$member[$m]['normal_suga_except'] += $mem[$i]['normal_suga_except'];
		$member[$m]['family_suga_except'] += $mem[$i]['family_suga_except'];

		$member[$m]['client_rate']	= $mem[$i]['client_rate'];
	}

	$member[$m] = set_hour2minute($member[$m]);

	unset($mem);

	$mem_count = sizeof($member);

	for($i=0; $i<$mem_count; $i++){
		$jumin	  = $member[$i]['jumin'];
		$pay_gbn  = $pay_list_my[$jumin];	//급여구분

		$member[$i]['paid_cnt'] = $temp_paid_cnt; //유급일수
		$member[$i]['ins_yn']   = $pay_gbn['ins_yn']; //4대보험여부

		$member[$i]['bath_amt']		= round($member[$i]['bath_amt'], -1);		//목욕수당반올림
		$member[$i]['nursing_amt']	= round($member[$i]['nursing_amt'], -1);	//간호수당반올림

		if ($pay_gbn['kind'] == '1' || $pay_gbn['kind'] == '2'){	//시급
			if ($pay_gbn['type'] == 'Y'){	//고정급
				$member[$i]['salary_type'] = 1;
			}else{	//변동급
				$member[$i]['salary_type'] = 2;
			}
		}else if ($pay_gbn['kind'] == '3'){	//월급
			$member[$i]['salary_type'] = 3;
		}else{	//총액비율
			$member[$i]['salary_type'] = 4;
		}

		//최소시급
		$member[$i]['min_hourly'] = $day_hourly;

		//일근무시간
		$member[$i]['day_work_hour'] = $day_work_hour;

		//직급수당
		$member[$i]['rank_pay'] = $pay_gbn['rank_pay'];

		//기본급 = 최소시급 * 근무시간
		$member[$i]['base_pay'] = round($member[$i]['min_hourly'] * $member[$i]['total_work_time'], -1);

		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			$member[$i][$lvl.'_normal_prolong_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_prolong_hour']		* __PROLONG_RATE__,			-1);	//연장수당
			$member[$i][$lvl.'_normal_night_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_night_hour']			* __NIGHT_RATE__,			-1);	//야간수당
			$member[$i][$lvl.'_normal_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_hour']		* __HOLIDAY_RATE__,			-1);	//휴일수당
			$member[$i][$lvl.'_normal_holiday_prolong_pay']	= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_prolong_hour']* __HOLIDAY_PROLONG_RATE__, -1);	//휴연수당
			$member[$i][$lvl.'_normal_holiday_night_pay']	= round($member[$i]['min_hourly'] * $member[$i][$lvl.'_normal_holiday_night_hour']	* __HOLIDAY_NIGHT_RATE__,	-1);	//휴야수당

			$member[$i]['normal_sudang_amt']	+= ($member[$i][$lvl.'_normal_prolong_pay']
												+   $member[$i][$lvl.'_normal_night_pay']
												+   $member[$i][$lvl.'_normal_holiday_pay']
												+   $member[$i][$lvl.'_normal_holiday_prolong_pay']
												+   $member[$i][$lvl.'_normal_holiday_night_pay']);
		}

		$member[$i]['family_prolong_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_prolong_pay']			* __PROLONG_RATE__,			-1);	//연장수당
		$member[$i]['family_night_pay']				= round($member[$i]['min_hourly'] * $member[$i]['family_night_pay']				* __NIGHT_RATE__,			-1);	//야간수당
		$member[$i]['family_holiday_pay']			= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_pay']			* __HOLIDAY_RATE__,			-1);	//휴일수당
		$member[$i]['family_holiday_prolong_pay']	= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_prolong_pay']	* __HOLIDAY_PROLONG_RATE__,	-1);	//휴연수당
		$member[$i]['family_holiday_night_pay']		= round($member[$i]['min_hourly'] * $member[$i]['family_holiday_night_pay']		* __HOLIDAY_NIGHT_RATE__,	-1);	//휴야수당

		$member[$i]['family_sudang_amt']	+= ($member[$i]['family_prolong_pay']
											+   $member[$i]['family_night_pay']
											+   $member[$i]['family_holiday_pay']
											+   $member[$i]['family_holiday_prolong_pay']
											+   $member[$i]['family_holiday_night_pay']);

		// 급여계산
		switch($member[$i]['salary_type']){
		case 1:	//고정시급
			$member[$i]['normal_hourly']	= $pay_gbn['pay'];
			$member[$i]['normal_pay']		= round($member[$i]['normal_hourly'] * $member[$i]['total_work_time'], -1);
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
			break;
		case 4:	//총액비율
			$member[$i]['normal_rate']	= $pay_gbn['rate'];	//총액비율
			$member[$i]['normal_pay']	= round($member[$i]['normal_suga_except'] * ($member[$i]['normal_rate'] / 100), -1);
			break;
		}

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
				$member[$i]['family_pay'] = round($pay_gbn['family_pay'], -1);
				break;
			}
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

		// 보전수당 = 총급여 - (기본급 + 식대보조비 + 차량유지비 + 주휴수당 + 유급수당)
		$temp_bojeon = round($member[$i]['gross_pay'] - ($member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['paid_amt']), -1);

		if ($temp_bojeon < 0) $temp_bojeon = 0;

		if ($temp_bojeon > __MAX_MEAL_AMT__){
			$member[$i]['meal_amt']	= __MAX_MEAL_AMT__;
		}else{
			$member[$i]['meal_amt']	= $temp_bojeon;
		}

		// 보전수당
		$member[$i]['bojeon_pay']	= round($member[$i]['gross_pay'] - ($member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['meal_amt'] + $member[$i]['paid_amt']), -1);

		if ($member[$i]['bojeon_pay'] < 0) $member[$i]['bojeon_pay'] = 0;

		// 계산 총급여 재계산
		$member[$i]['gross_pay'] = $member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['paid_amt'] + $member[$i]['bath_amt'] + $member[$i]['nursing_amt'] + $member[$i]['bojeon_pay'];

		// 갑근세
		$member[$i]['gapgeunse']	= $check->gapgeunse($year, $member[$i]['gross_pay'], $pay_gbn['deduct1'], $pay_gbn['deduct2']);

		// 주민세
		$member[$i]['juminse']		= $myF->cutOff($member[$i]['gapgeunse'] * 0.1);

		if ($member[$i]['ins_yn'] == 'Y'){
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
		}
	}

	$conn->begin();

	/*
	$sql = "insert into closing_result (id, org_no, closing_yymm, closing_gbn, closing_dt_f) values (null, '$code', '$year$month', '$gubun', now())";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$new_id = $conn->get_data("select max(id) from closing_result where org_no = '$code' and closing_yymm = '$year$month'");
	*/

	// 기존데이타 삭제
	if (!$conn->execute("delete from salary_basic where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_basic 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_bn where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_bn 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_amt where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_amt 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_hourly where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_monthly where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_monthly 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_rate where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_rate 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_addon_pay where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_addon_pay 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// 급여저장
	for($i=0; $i<$mem_count; $i++){
		$jumin = $member[$i]['jumin'];	//직원주민번호

		// 1.급여지급 방식에 따른 처리(동거도 포함)
		/*
		 * 급여방식 salary_type
		 * 1 : 고정급여방식 salary_hourly
		 * 2 : 변동급여방식 salary_hourly
		 * 3 : 월급 salary_monthly
		 * 4 : 총액비율 salary_rate
		 */
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

			$sql = "insert into salary_hourly values (
					 '$code'
					,'$year$month'
					,'$jumin'
					,'1'
					,'0'
					,'$hourly'
					,'$work_time'
					,'$prolong_hour'
					,'$prolong_pay'
					,'$night_hour'
					,'$night_pay'
					,'$holiday_hour'
					,'$holiday_pay'
					,'$holiday_prolong_hour'
					,'$holiday_prolong_pay'
					,'$holiday_night_hour'
					,'$holiday_night_pay')";

			//echo '1<br>'.$sql.'<br>';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

			break;
		case 2: //변동시급
			$hourly = $member[$i]['normal_hourly'];			//시급

			for($j=1; $j<=4; $j++){
				$lvl = $j == 4 ? 9 : $j;

				$work_time				= $member[$i][$lvl.'_normal_work_time'];			//근무시간
				$prolong_hour			= $member[$i][$lvl.'_normal_prolong_hour'];			//연장근무시간
				$prolong_pay			= $member[$i][$lvl.'_normal_prolong_pay'];			//연장수당
				$night_hour				= $member[$i][$lvl.'_normal_night_hour'];			//야간근무시간
				$night_pay				= $member[$i][$lvl.'_normal_night_pay'];			//야간수당
				$holiday_hour			= $member[$i][$lvl.'_normal_holiday_hour'];			//휴일근무시간
				$holiday_pay			= $member[$i][$lvl.'_normal_holiday_pay'];			//휴일수당
				$holiday_prolong_hour	= $member[$i][$lvl.'_normal_holiday_prolong_hour'];	//휴일연장시간
				$holiday_prolong_pay	= $member[$i][$lvl.'_normal_holiday_prolong_pay'];	//휴연수당
				$holiday_night_hour		= $member[$i][$lvl.'_normal_holiday_night_hour'];	//휴일야간시간
				$holiday_night_pay		= $member[$i][$lvl.'_normal_holiday_night_pay'];	//휴야수당

				$sql = "insert into salary_hourly values (
					 '$code'
					,'$year$month'
					,'$jumin'
					,'1'
					,'$lvl'
					,'$hourly'
					,'$work_time'
					,'$prolong_hour'
					,'$prolong_pay'
					,'$night_hour'
					,'$night_pay'
					,'$holiday_hour'
					,'$holiday_pay'
					,'$holiday_prolong_hour'
					,'$holiday_prolong_pay'
					,'$holiday_night_hour'
					,'$holiday_night_pay')";

				//echo '2<br>'.$sql.'<br>';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			}
			break;
		case 3: //월급
			$monthly = $member[$i]['normal_pay'];

			$sql = "insert into salary_monthly values (
					 '$code'
					,'$year$month'
					,'$jumin'
					,'1'
					,'$monthly')";

			//echo '3<br>'.$sql.'<br>';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_monthly 입력중 오류발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

			break;
		case 4: //총액비율
			$suga_rate		= $member[$i]['normal_rate'];
			$suga_total		= $member[$i]['normal_suga'];
			$suga_except	= $member[$i]['normal_suga_except'];
			$suga_pay		= $member[$i]['normal_pay'];

			$sql = "insert into salary_rate values (
					 '$code'
					,'$year$month'
					,'$jumin'
					,'1'
					,'$suga_rate'
					,'0'
					,'$suga_total'
					,'$suga_except'
					,'$suga_pay')";

			//echo '4<br>'.$sql.'<br>';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_rate 입력중 오류발생');
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}

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

				$sql = "insert into salary_hourly values (
						 '$code'
						,'$year$month'
						,'$jumin'
						,'2'
						,'0'
						,'$hourly'
						,'$work_time'
						,'$prolong_hour'
						,'$prolong_pay'
						,'$night_hour'
						,'$night_pay'
						,'$holiday_hour'
						,'$holiday_pay'
						,'$holiday_prolong_hour'
						,'$holiday_prolong_pay'
						,'$holiday_night_hour'
						,'$holiday_night_pay')";

				//echo '5<br>'.$sql.'<br>';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}

				break;
			case 2: //총액비율
				$client_rate	= $member[$i]['client_rate'];
				$suga_rate		= $member[$i]['family_rate'];
				$suga_total		= $member[$i]['family_suga'];
				$suga_except	= $member[$i]['family_suga_except'];
				$suga_pay		= $member[$i]['family_pay'];

				$sql = "insert into salary_rate values (
						 '$code'
						,'$year$month'
						,'$jumin'
						,'2'
						,'$suga_rate'
						,'$client_rate'
						,'$suga_total'
						,'$suga_except'
						,'$suga_pay')";

				//echo '6<br>'.$sql.'<br>';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_monthly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}

				break;
			case 3: //월급
				$monthly = $member[$i]['family_pay'];

				$sql = "insert into salary_monthly values (
						 '$code'
						,'$year$month'
						,'$jumin'
						,'2'
						,'$monthly')";

				//echo '7<br>'.$sql.'<br>';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_monthly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}

				break;
			}
		}

		// 2.목욕, 간호 횟수 및 수당(salary_bn)
		//if ($member[$m]['bath_cnt'] > 0 || $member[$m]['nursing_cnt'] > 0){
		$bath_cnt		= $member[$i]['bath_cnt'];
		$bath_amt		= $member[$i]['bath_amt'];
		$nursing_cnt	= $member[$i]['nursing_cnt'];
		$nursing_amt	= $member[$i]['nursing_amt'];

		$sql = "insert into salary_bn values (
				 '$code'
				,'$year$month'
				,'$jumin'
				,'1'
				,'$bath_cnt'
				,'$bath_amt'
				,'$nursing_cnt'
				,'$nursing_amt')";

		//echo '8<br>'.$sql.'<br>';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_bn 입력중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
		//}

		// 3.급여 기본 데이타(salary_basic)
		$sql = "insert into salary_basic values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($member[$i]['salary_type'])."'
				,'".($member[$i]['normal_work_cnt']+$member[$i]['family_work_cnt'])."'
				,'".($member[$i]['1_normal_work_time']+$member[$i]['2_normal_work_time']+$member[$i]['3_normal_work_time']+$member[$i]['9_normal_work_time']+$member[$i]['family_work_time'])."'
				,'".($member[$i]['week_cnt'])."'
				,'".($member[$i]['week_amt'])."'
				,'".($member[$i]['paid_cnt'])."'
				,'".($member[$i]['paid_amt'])."'
				,'".($member[$i]['bath_cnt'])."'
				,'".($member[$i]['bath_amt'])."'
				,'".($member[$i]['nursing_cnt'])."'
				,'".($member[$i]['nursing_amt'])."'
				,'".($member[$i]['1_normal_prolong_hour']+$member[$i]['2_normal_prolong_hour']+$member[$i]['3_normal_prolong_hour']+$member[$i]['9_normal_prolong_hour']+$member[$i]['family_prolong_hour'])."'
				,'".($member[$i]['1_normal_prolong_pay']+$member[$i]['2_normal_prolong_pay']+$member[$i]['3_normal_prolong_pay']+$member[$i]['9_normal_prolong_pay']+$member[$i]['family_prolong_pay'])."'
				,'".($member[$i]['1_normal_night_hour']+$member[$i]['2_normal_night_hour']+$member[$i]['3_normal_night_hour']+$member[$i]['9_normal_night_hour']+$member[$i]['family_night_hour'])."'
				,'".($member[$i]['1_normal_night_pay']+$member[$i]['2_normal_night_pay']+$member[$i]['3_normal_night_pay']+$member[$i]['9_normal_night_pay']+$member[$i]['family_night_pay'])."'
				,'".($member[$i]['1_normal_holiday_hour']+$member[$i]['2_normal_holiday_hour']+$member[$i]['3_normal_holiday_hour']+$member[$i]['9_normal_holiday_hour']+$member[$i]['family_holiday_hour'])."'
				,'".($member[$i]['1_normal_holiday_pay']+$member[$i]['2_normal_holiday_pay']+$member[$i]['3_normal_holiday_pay']+$member[$i]['9_normal_holiday_pay']+$member[$i]['family_holiday_pay'])."'
				,'".($member[$i]['1_normal_holiday_prolong_hour']+$member[$i]['2_normal_holiday_prolong_hour']+$member[$i]['3_normal_holiday_prolong_hour']+$member[$i]['9_normal_holiday_prolong_hour']+$member[$i]['family_holiday_prolong_hour'])."'
				,'".($member[$i]['1_normal_holiday_prolong_pay']+$member[$i]['2_normal_holiday_prolong_pay']+$member[$i]['3_normal_holiday_prolong_pay']+$member[$i]['9_normal_holiday_prolong_pay']+$member[$i]['family_holiday_prolong_pay'])."'
				,'".($member[$i]['1_normal_holiday_night_hour']+$member[$i]['2_normal_holiday_night_hour']+$member[$i]['3_normal_holiday_night_hour']+$member[$i]['9_normal_holiday_night_hour']+$member[$i]['family_holiday_night_hour'])."'
				,'".($member[$i]['1_normal_holiday_night_pay']+$member[$i]['2_normal_holiday_night_pay']+$member[$i]['3_normal_holiday_night_pay']+$member[$i]['9_normal_holiday_night_pay']+$member[$i]['family_holiday_night_pay'])."'
				,'".($member[$i]['base_pay'])."'
				,'".($member[$i]['meal_amt'])."'
				,'0'
				,'".($member[$i]['bojeon_pay'])."'
				,'".($member[$i]['worker_annuity'])."'
				,'".($member[$i]['worker_health'])."'
				,'".($member[$i]['worker_oldcare'])."'
				,'".($member[$i]['worker_employ'])."'
				,'".($member[$i]['gapgeunse'])."'
				,'".($member[$i]['juminse'])."'
				,'0')";

		//echo '9<br>'.$sql.'<br>';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_basic 입력중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		// 4.추가 수당 및 공제
		if ($addon_1 > 0){
			$add_pay_1 = 0;
			$first = false;
			$sql = "insert into salary_addon_pay values ";
			for($j=0; $j<$addon_1; $j++){
				if ($salary_addon[1][$j]['pay'] > 0){
					if ($first == true) $sql .= ",";
					//$sql .= "('".$code."', '".$year.$month."', '".$jumin."', '1', '".$salary_addon[1][$j]['index']."' ,'".$salary_addon[1][$j]['pay']."')";
					$sql .= "('".$code."', '".$year.$month."', '".$jumin."', '1', '".$salary_addon[1][$j]['index']."', '".$salary_addon[1][$j]['subject']."' ,'".$salary_addon[1][$j]['pay']."')";
					$first = true;
					$add_pay_1 += intval($salary_addon[1][$j]['pay']);
				}
			}

			if ($first == true){
				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_addon_pay 1 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			}
		}

		if ($addon_2 > 0){
			$add_pay_2 = 0;
			$first = false;
			$sql = "insert into salary_addon_pay values ";
			for($j=0; $j<$addon_2; $j++){
				if ($salary_addon[2][$j]['pay'] > 0){
					if ($first == true) $sql .= ",";
					//$sql .= "('".$code."', '".$year.$month."', '".$jumin."', '2', '".$salary_addon[2][$j]['index']."' ,'".$salary_addon[2][$j]['pay']."')";
					$sql .= "('".$code."', '".$year.$month."', '".$jumin."', '2', '".$salary_addon[2][$j]['index']."', '".$salary_addon[2][$j]['subject']."' ,'".$salary_addon[2][$j]['pay']."')";
					$first = true;
					$add_pay_2 += intval($salary_addon[2][$j]['pay']);
				}
			}

			if ($first == true){
				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_addon_pay 2 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			}
		}

		$basic_amt	= ($member[$i]['base_pay'] + $member[$i]['week_amt'] + $member[$i]['paid_amt'] + $member[$i]['bath_amt'] + $member[$i]['nursing_amt'] + $member[$i]['meal_amt'] + $member[$i]['bojeon_pay']) +
					  ($member[$i]['1_normal_prolong_pay']+$member[$i]['2_normal_prolong_pay']+$member[$i]['3_normal_prolong_pay']+$member[$i]['9_normal_prolong_pay']+$member[$i]['family_prolong_pay']) +
					  ($member[$i]['1_normal_night_pay']+$member[$i]['2_normal_night_pay']+$member[$i]['3_normal_night_pay']+$member[$i]['9_normal_night_pay']+$member[$i]['family_night_pay']) +
					  ($member[$i]['1_normal_holiday_pay']+$member[$i]['2_normal_holiday_pay']+$member[$i]['3_normal_holiday_pay']+$member[$i]['9_normal_holiday_pay']+$member[$i]['family_holiday_pay']) +
					  ($member[$i]['1_normal_holiday_prolong_pay']+$member[$i]['2_normal_holiday_prolong_pay']+$member[$i]['3_normal_holiday_prolong_pay']+$member[$i]['9_normal_holiday_prolong_pay']+$member[$i]['family_holiday_prolong_pay']) +
					  ($member[$i]['1_normal_holiday_night_pay']+$member[$i]['2_normal_holiday_night_pay']+$member[$i]['3_normal_holiday_night_pay']+$member[$i]['9_normal_holiday_night_pay']+$member[$i]['family_holiday_night_pay']) +
					  ($member[$i]['rank_pay']);
		$basic_add	= $add_pay_1;
		$basic_tot	= $basic_amt + $basic_add;

		$deduct_amt	= $member[$i]['worker_annuity'] + $member[$i]['worker_health'] + $member[$i]['worker_oldcare'] + $member[$i]['worker_employ'] + $member[$i]['juminse'] + $member[$i]['gapgeunse'];
		$deduct_add	= $add_pay_2;
		$deduct_tot	= $deduct_amt + $deduct_add;

		$tot_diff	= $basic_tot - $deduct_tot;

		// 4.급여 합계(salary_amt)
		/*
		$sql = "insert into salary_amt values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($member[$i]['gross_pay']+$member[$i]['normal_sudang_amt']+$member[$i]['family_sudang_amt']+$member[$i]['bath_amt']+$member[$i]['nursing_amt'])."'
				,'".($add_pay_1)."'
				,'".($member[$i]['gross_pay']+$member[$i]['normal_sudang_amt']+$member[$i]['family_sudang_amt']+$member[$i]['bath_amt']+$member[$i]['nursing_amt']+$add_pay_1)."'
				,'".($member[$i]['deduct_amt'])."'
				,'".($add_pay_2)."'
				,'".($member[$i]['deduct_amt']+$add_pay_2)."'
				,'".(($member[$i]['gross_pay']+$member[$i]['normal_sudang_amt']+$member[$i]['family_sudang_amt']+$member[$i]['bath_amt']+$member[$i]['nursing_amt']+$add_pay_1)-($member[$i]['deduct_amt']-$add_pay_2))."')";
		*/
		//echo '10<br>'.$sql.'<br>';
		$sql = "insert into salary_amt values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($basic_amt)."'
				,'".($basic_add)."'
				,'".($basic_tot)."'
				,'".($deduct_amt)."'
				,'".($deduct_add)."'
				,'".($deduct_tot)."'
				,'".($tot_diff)."')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_amt 입력중 오류발생');
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}
	}

	$today = date('Y-m-d', mktime());

	$sql = "update closing_progress
			   set salary_bat_calc_flag = 'Y'
			,      salary_bat_can_flag  = 'N'";

	if ($pos == 2){
		$sql .= ", salary_bat_calc_dt = '$today'";
	}

	$sql .= "
			 where org_no       = '$code'
			   and closing_yymm = '$year$month'
			   and del_flag     = 'N'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'closing_progress 플래그 변경중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// 로그기록
	$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, '요양보호사 급여계산 완료');

	// 종료
	if ($pos == 2) echo $myF->message('ok', 'Y', 'N', 'Y');

	$conn->commit();

	unset($member);

	include_once("../inc/_db_close.php");

	// 배열 초기화
	function init_array($jumin, $date = ''){
		$array['jumin']	= $jumin;	//주민번호

		$array['salary_type']	= 0;	//급여지급방식
		$array['min_hourly']	= 0;	//최소시급
		$array['day_work_hour']	= 0;	//일근무시간
		$array['rank_pay']		= 0;	//직급수당

		// 타수급은 등급별로 계산한다.
		for($i=1; $i<=4; $i++){
			if ($i == 4){
				$lvl = 9;
			}else{
				$lvl = $i;
			}

			$array[$lvl.'_normal_work_time']			= 0;	//타수급자 근무시간
			$array[$lvl.'_normal_prolong_hour']			= 0;	//타수급자 연장시간
			$array[$lvl.'_normal_night_hour']			= 0;	//타수급자 야간시간
			$array[$lvl.'_normal_holiday_hour']			= 0;	//타수급자 휴일시간
			$array[$lvl.'_normal_holiday_prolong_hour']	= 0;	//타수급자 휴연시간
			$array[$lvl.'_normal_holiday_night_hour']	= 0;	//타수급자 휴야시간

			$array[$lvl.'_normal_prolong_pay']			= 0;	//연장수당
			$array[$lvl.'_normal_night_pay']			= 0;	//야간수당
			$array[$lvl.'_normal_holiday_pay']			= 0;	//휴일수당
			$array[$lvl.'_normal_holiday_prolong_pay']	= 0;	//휴연수당
			$array[$lvl.'_normal_holiday_night_pay']	= 0;	//휴야수당

			$array[$lvl.'_normal_hourly']	= 0;	//등급별시급
		}

		$array['normal_sudang_amt']	= 0;	//타수급자 수당합계
		$array['family_sudang_amt']	= 0;	//동거가족 수당합계

		$array['bath_cnt']		= 0;	//타수급자 목욕횟수
		$array['nursing_cnt']	= 0;	//타수급자 간호횟수

		$array['bath_amt']		= 0;	//타수급자 목욕수당
		$array['nursing_amt']	= 0;	//타수급자 간호수당

		$array['normal_work_cnt']		= 0;	//타수급자 근무일수
		$array['normal_holiday_cnt']	= 0;	//타수급자 휴일근무일수
		$array['normal_work_time']		= 0;	//타수급자 총근무시간
		$array['normal_hourly']			= 0;	//고정시급
		$array['normal_salary']			= 0;	//급여
		$array['normal_rate']			= 0;	//총액비율

		// 동거는 통으로 계산한다.
		$array['family_work_cnt']				= 0;	//동거가족 근무일수
		$array['family_work_time']				= 0;	//동거가족 총근무시간
		$array['family_prolong_hour']			= 0;	//동거가족 연장시간
		$array['family_night_hour']				= 0;	//동거가족 야간시간
		$array['family_holiday_cnt']			= 0;	//동거가족 휴일일수
		$array['family_holiday_hour']			= 0;	//동거가족 휴일시간
		$array['family_holiday_prolong_hour']	= 0;	//동거가족 휴연시간
		$array['family_holiday_night_hour']		= 0;	//동거가족 휴야시간

		$array['family_prolong_pay']			= 0;	//연장수당
		$array['family_night_pay']				= 0;	//야간수당
		$array['family_holiday_pay']			= 0;	//휴일수당
		$array['family_holiday_prolong_pay']	= 0;	//휴연수당
		$array['family_holiday_night_pay']		= 0;	//휴야수당

		$array['family_yn']		= 'N';	//동거여부
		$array['family_type']	= '0';	//동거급여방식
		$array['family_pay']	= 0;	//동거급여
		$array['family_rate']	= 0;	//총액비율

		$array['total_work_time'] = 0;	//총근무시간

		if ($date != ''){
			$array['client_gbn']	= 'normal';	//타, 동거 구분
			$array['date']			= $date;	//일자
			$array['holiday_yn']	= 'N';		//휴일여부
			$array['paid_yn']		= 'N';		//유무급 여부
			$array['client_rate']	= 0;		//본인부담율
		}

		$array['gross_pay']		= 0;	//총급여
		$array['normal_pay']	= 0;	//타수급 총급여
		$array['family_pay']	= 0;	//동거 총급여

		$array['base_pay']		= 0;	//기본급

		$array['week_cnt']		= 0;	//주휴갯수
		$array['week_amt']		= 0;	//주휴수당

		$array['paid_cnt']		= 0;	//유급휴일갯수
		$array['paid_amt']		= 0;	//유급휴일수당

		$array['normal_suga']			= 0;	//타수급 수가금액
		$array['family_suga']			= 0;	//동거가족 수가금액
		$array['normal_suga_except']	= 0;	//본인부담금을 제한 수가금액
		$array['family_suga_except']	= 0;	//본인부담금을 제한 수가금액
		$array['total_suga']			= 0;	//총수가금액

		$array['bojeon_pay']	= 0;	//보전수당

		$array['meal_amt']		= 0;	//식대보조비
		$array['carkeep_amt']	= 0;	//차량유지비

		$array['gapgeunse']	= 0;	//갑근세
		$array['juminse']	= 0;	//주민세

		$array['ins_yn']			= 'Y';	//4대보험여부
		$array['worker_employ']		= 0;	//고용보험 본인부담
		$array['worker_health']		= 0;	//건강보험 본인부담
		$array['worker_oldcare']	= 0;	//노인장기요양 본인부담
		$array['worker_annuity']	= 0;	//국민연금 본인부담
		$array['center_employ']		= 0;	//고용보험 기관부담
		$array['center_health']		= 0;	//건강보험 기관부담
		$array['center_oldcare']	= 0;	//노인장기요양 기관부담
		$array['center_annuity']	= 0;	//국민연금 기관부담
		$array['center_sanje']		= 0;	//산재보험 기관부담

		$array['deduct_amt']		= 0;	//공제총액

		return $array;
	}

	// 분을 시간으로 변경
	function set_hour2minute($mem){
		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			$mem['total_work_time']	+= $mem[$lvl.'_normal_work_time'];

			$mem[$lvl.'_normal_work_time']				= round($mem[$lvl.'_normal_work_time']		/ 60, 1);
			$mem[$lvl.'_normal_prolong_hour']			= round($mem[$lvl.'_normal_prolong_hour']	/ 60, 1);
			$mem[$lvl.'_normal_night_hour']				= round($mem[$lvl.'_normal_night_hour']		/ 60, 1);

			$mem[$lvl.'_normal_holiday_hour']			= round($mem[$lvl.'_normal_holiday_hour']			/ 60, 1);
			$mem[$lvl.'_normal_holiday_prolong_hour']	= round($mem[$lvl.'_normal_holiday_prolong_hour']	/ 60, 1);
			$mem[$lvl.'_normal_holiday_night_hour']		= round($mem[$lvl.'_normal_holiday_night_hour']		/ 60, 1);
		}

		$mem['total_work_time']	+= $mem['family_work_time'];

		$mem['family_work_time']			= round($mem['family_work_time']			/ 60, 1);
		$mem['family_prolong_hour']			= round($mem['family_prolong_hour']			/ 60, 1);
		$mem['family_night_hour']			= round($mem['family_night_hour']			/ 60, 1);
		$mem['family_holiday_hour']			= round($mem['family_holiday_hour']			/ 60, 1);
		$mem['family_holiday_prolong_hour']	= round($mem['family_holiday_prolong_hour'] / 60, 1);
		$mem['family_holiday_night_hour']	= round($mem['family_holiday_night_hour']	/ 60, 1);

		$mem['total_work_time']	= round($mem['total_work_time']	/ 60, 1);

		if ($mem['total_work_time'] < 60){
			$mem['week_cnt'] = round($mem['total_work_time'] / (60 / $mem['week_cnt']));
		}

		return $mem;
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
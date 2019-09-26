<?
	// 배열 초기화
	function init_array($jumin, $date = ''){
		$array['jumin']	= $jumin;	//주민번호

		$array['salary_type']	= 0;	//급여지급방식
		$array['salary_com']	= 0;	//포괄임금여부
		$array['min_hourly']	= 0;	//최소시급
		$array['day_work_hour']	= 0;	//일근무시간
		$array['rank_pay']		= 0;	//직급수당

		$array['add_rate']		= 0;	//추가수당비율
		$array['add_pay']		= 0;	//추가수당금액

		$array['holiday_add_rate'] = 0; //휴일추가수당비율
		$array['holiday_add_pay']  = 0; //휴일추가수당금액

		$array['sunday_add_rate'] = 0; //일요일추가수당비율
		$array['sunday_add_pay']  = 0; //일요일추가수당금액


		//$array['add_hour']		= 0;	//추가수당시간

		/*
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

			$array[$lvl.'_add_hour']		= 0;	//추가수당시간
		}
		*/

		for($ii=0; $ii<=2; $ii++){
			$svc = get_svc_gbn($ii);
			$end = get_end_cnt($ii);

			for($j=1; $j<=$end; $j++){
				$lvl = get_lvl_gbn($j, $ii);

				$array[$lvl.'_'.$svc.'_work_time']				= 0;	//타수급자 근무시간
				$array[$lvl.'_'.$svc.'_prolong_hour']			= 0;	//타수급자 연장시간
				$array[$lvl.'_'.$svc.'_night_hour']				= 0;	//타수급자 야간시간
				$array[$lvl.'_'.$svc.'_holiday_hour']			= 0;	//타수급자 휴일시간
				$array[$lvl.'_'.$svc.'_holiday_prolong_hour']	= 0;	//타수급자 휴연시간
				$array[$lvl.'_'.$svc.'_holiday_night_hour']		= 0;	//타수급자 휴야시간

				$array[$lvl.'_'.$svc.'_prolong_pay']			= 0;	//연장수당
				$array[$lvl.'_'.$svc.'_night_pay']				= 0;	//야간수당
				$array[$lvl.'_'.$svc.'_holiday_pay']			= 0;	//휴일수당
				$array[$lvl.'_'.$svc.'_holiday_prolong_pay']	= 0;	//휴연수당
				$array[$lvl.'_'.$svc.'_holiday_night_pay']		= 0;	//휴야수당

				if ($svc == '0'){
					$array[$lvl.'_hourly']		= 0;	//등급별시급
					$array[$lvl.'_add_hour']	= 0;	//추가수당시간

					$array[$lvl.'_holiday_add_hour'] = 0; //휴일추가시간
					$array[$lvl.'_sunday_add_hour']  = 0; //일요일추가시간
				}else{
					$array[$svc.'_hourly']		= 0;	//등급별시급
					$array[$svc.'_add_hour']	= 0;	//추가수당시간

					$array[$svc.'_holiday_add_hour'] = 0; //휴일추가시간
					$array[$svc.'_sunday_add_hour']  = 0; //일요일추가시간
				}
			}


			$array[$svc.'_sudang_amt']	= 0;	//타수급자 수당합계

			$array[$svc.'_work_count']		= 0;	//타수급자 근무일수
			$array[$svc.'_holiday_count']	= 0;	//타수급자 휴일근무일수
			$array[$svc.'_work_time']		= 0;	//타수급자 총근무시간
			$array[$svc.'_hourly']			= 0;	//고정시급
			$array[$svc.'_salary']			= 0;	//급여
			$array[$svc.'_rate']			= 0;	//총액비율

			$array[$svc.'_svc_pay']			= 0;	//타수급 총급여
			$array[$svc.'_suga']			= 0;	//타수급 수가금액
			$array[$svc.'_suga_except']		= 0;	//본인부담금을 제한 수가금액
			$array[$svc.'_salary_type']		= 0;	//급여지급방식
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

		$array['mon_work_time']		= 0;	//월급 총근무시간

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
		$array['total_com_time']  = 0;  //초과근무시간 총합(포괄임금제)

		if ($date != ''){
			$array['client_gbn']	= 'normal';	//타, 동거 구분
			$array['date']			= $date;	//일자
			$array['holiday_yn']	= 'N';		//휴일여부
			$array['paid_yn']		= 'N';		//유무급 여부
			$array['client_rate']	= 0;		//본인부담율
			$array['weekday']       = 0;        //요일
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
		/*
		for($j=1; $j<=4; $j++){
			$lvl = ($j == 4 ? 9 : $j);

			$mem['total_work_time']	+= $mem[$lvl.'_normal_work_time'];
			$mem['total_com_time']	+= $mem[$lvl.'_normal_prolong_hour'];			//포괄임금제
			$mem['total_com_time']	+= $mem[$lvl.'_normal_night_hour'];				//포괄임금제
			$mem['total_com_time']	+= $mem[$lvl.'_normal_holiday_hour'];			//포괄임금제
			$mem['total_com_time']	+= $mem[$lvl.'_normal_holiday_prolong_hour'];	//포괄임금제
			$mem['total_com_time']	+= $mem[$lvl.'_normal_holiday_night_hour'];		//포괄임금제

			$mem[$lvl.'_normal_work_time']				= round($mem[$lvl.'_normal_work_time']		/ 60, 1);
			$mem[$lvl.'_normal_prolong_hour']			= round($mem[$lvl.'_normal_prolong_hour']	/ 60, 1);
			$mem[$lvl.'_normal_night_hour']				= round($mem[$lvl.'_normal_night_hour']		/ 60, 1);

			$mem[$lvl.'_normal_holiday_hour']			= round($mem[$lvl.'_normal_holiday_hour']			/ 60, 1);
			$mem[$lvl.'_normal_holiday_prolong_hour']	= round($mem[$lvl.'_normal_holiday_prolong_hour']	/ 60, 1);
			$mem[$lvl.'_normal_holiday_night_hour']		= round($mem[$lvl.'_normal_holiday_night_hour']		/ 60, 1);
		}

		$mem['normal_work_time'] = $mem['1_normal_work_time'] + $mem['2_normal_work_time'] + $mem['3_normal_work_time'] + $mem['9_normal_work_time'];
		*/

		for($ii=0; $ii<=2; $ii++){
			$svc = get_svc_gbn($ii);
			$end = get_end_cnt($ii);

			for($j=1; $j<=$end; $j++){
				$lvl = get_lvl_gbn($j, $ii);

				$mem['total_work_time']	+= $mem[$lvl.'_'.$svc.'_work_time'];
				$mem['total_com_time']	+= $mem[$lvl.'_'.$svc.'_prolong_hour'];			//포괄임금제
				$mem['total_com_time']	+= $mem[$lvl.'_'.$svc.'_night_hour'];				//포괄임금제
				$mem['total_com_time']	+= $mem[$lvl.'_'.$svc.'_holiday_hour'];			//포괄임금제
				$mem['total_com_time']	+= $mem[$lvl.'_'.$svc.'_holiday_prolong_hour'];	//포괄임금제
				$mem['total_com_time']	+= $mem[$lvl.'_'.$svc.'_holiday_night_hour'];		//포괄임금제

				$mem[$lvl.'_'.$svc.'_work_time']			= round($mem[$lvl.'_'.$svc.'_work_time']		/ 60, 1);
				$mem[$lvl.'_'.$svc.'_prolong_hour']			= round($mem[$lvl.'_'.$svc.'_prolong_hour']	/ 60, 1);
				$mem[$lvl.'_'.$svc.'_night_hour']			= round($mem[$lvl.'_'.$svc.'_night_hour']		/ 60, 1);

				$mem[$lvl.'_'.$svc.'_holiday_hour']			= round($mem[$lvl.'_'.$svc.'_holiday_hour']			/ 60, 1);
				$mem[$lvl.'_'.$svc.'_holiday_prolong_hour']	= round($mem[$lvl.'_'.$svc.'_holiday_prolong_hour']	/ 60, 1);
				$mem[$lvl.'_'.$svc.'_holiday_night_hour']	= round($mem[$lvl.'_'.$svc.'_holiday_night_hour']		/ 60, 1);

				$mem[$svc.'_work_time'] += $mem[$lvl.'_'.$svc.'_work_time'];
			}
		}

		/*
		if ($mem['total_work_time'] < $mem['mon_work_time']){
			$mem['normal_work_time'] = $mem['mon_work_time'] / 60;
			$mem['total_work_time']  = $mem['mon_work_time'];
		}

		if ($mem['mon_work_time'] == 0){
			$mem['total_work_time']	+= $mem['family_work_time'];
		}
		*/
		$mem['total_work_time']	+= $mem['family_work_time'];

		$mem['family_work_time'] = round($mem['family_work_time'] / 60, 1);

		/*
		$mem['family_prolong_hour']			= round($mem['family_prolong_hour']			/ 60, 1);
		$mem['family_night_hour']			= round($mem['family_night_hour']			/ 60, 1);
		$mem['family_holiday_hour']			= round($mem['family_holiday_hour']			/ 60, 1);
		$mem['family_holiday_prolong_hour']	= round($mem['family_holiday_prolong_hour'] / 60, 1);
		$mem['family_holiday_night_hour']	= round($mem['family_holiday_night_hour']	/ 60, 1);
		*/

		// 동거가족은 추가수당을 계산하지 않는다.
		$mem['family_prolong_hour']			= 0;
		$mem['family_night_hour']			= 0;
		$mem['family_holiday_hour']			= 0;
		$mem['family_holiday_prolong_hour']	= 0;
		$mem['family_holiday_night_hour']	= 0;

		$mem['total_work_time']	= round($mem['total_work_time']	/ 60, 1);

		/*****************************************************************

			- 주휴일수 계산
			  - 총근무시간이 60시간 미만인 경우 근무시간에 따른
			    주휴일수를 계산한다.

		    **************************************************************/

			if ($mem['total_work_time'] > 0 && $mem['total_work_time'] < 60){
				$mem['week_cnt'] = round($mem['total_work_time'] / (60 / ($mem['week_cnt'] > 0 ? $mem['week_cnt'] : 1)));
			}

		/*****************************************************************/

		$mem['total_com_time'] = round($mem['total_com_time'] / 60, 1);	//포괄임금제

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

	// 변경 설정
	function change_db($conn, $code){
		$rst = true;

		$conn->begin();

		// 18~20시 추가공제항목 추가
		$sql = "select count(*)
				  from salary_addon
				 where org_no       = '$code'
				   and salary_type  = '1'
				   and salary_index = '0'";

		if ($conn->get_data($sql) == 0){
			$sql = "insert into salary_addon values (
					 '$code'
					,'1'
					,'0'
					,'특별수당'
					,'0'
					,'1'
					,'N')";

			if (!$conn->execute($sql)){
				$conn->rollback();
				$rst = false;
			}
		}

		$conn->commit();

		return $rst;
	}

	function init_pt(){
		$a['prolong_hour']			= 0;
		$a['night_hour']			= 0;
		$a['holiday_hour']			= 0;
		$a['holiday_prolong_hour']	= 0;
		$a['holiday_night_hour']	= 0;

		$a['prolong_pay']			= 0;
		$a['night_pay']				= 0;
		$a['holiday_pay']			= 0;
		$a['holiday_prolong_pay']	= 0;
		$a['holiday_night_pay']		= 0;

		return $a;
	}

	function get_svc_gbn($svc){
		if ($svc == '0'){
			return 'normal';
		}else if ($svc == '1'){
			return 'voucher';
		}else if ($svc == 'f'){
			return 'family';
		}else{
			return 'other';
		}
	}

	function get_kind_gbn($kind, $svc){
		if ($svc == '0'){
			return '0';
		}else if ($svc == '1'){
			return $kind;
		}else{
			return chr(65+intval($kind)-1);
		}
	}

	function get_lvl_gbn($lvl, $svc){
		if ($svc == '0'){
			if ($lvl == 1 || $lvl == 2 || $lvl == 3){
				return $lvl;
			}else{
				return 9;
			}
		}else{
			return get_kind_gbn($lvl, $svc);
		}
	}

	function get_end_cnt($svc, $limit = 4){
		if ($svc == '0'){
			return $limit;
		}else if ($svc == '1'){
			return $limit;
		}else{
			if ($limit > 3) $limit = 3;
			return $limit;
		}
	}

	function is_weekday($sunday_list, $date){
		$list_cnt = sizeof($sunday_list);
		$result   = false;

		for($i=0; $i<$list_cnt; $i++){
			if (str_replace('-', '', $sunday_list[$i]) == $date){
				$result = true;
				break;
			}
		}

		return $result;
	}
?>
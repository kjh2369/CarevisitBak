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

			$array[$lvl.'_normal_hourly']	 = 0;	//등급별시급
			$array[$lvl.'_add_hour']		 = 0;	//추가수당시간
			$array[$lvl.'_holiday_add_hour'] = 0; //휴일추가시간
			$array[$lvl.'_sunday_add_hour']  = 0; //일요일추가시간
		}

		$array['normal_sudang_amt']	= 0;	//타수급자 수당합계
		$array['family_sudang_amt']	= 0;	//동거가족 수당합계

		$array['bath_cnt']		= 0;	//타수급자 목욕횟수
		$array['nursing_cnt']	= 0;	//타수급자 간호횟수

		$array['bath_amt']		= 0;	//타수급자 목욕수당
		$array['nursing_amt']	= 0;	//타수급자 간호수당


		/*********************************************************
			bn_time_yn
			- Y : 횟수 * 기준시급으로 기본금에 포함되며 수당에서 기본금에 포함된 금액만큼 제한다.
			      횟수 * 근로시간이 근무시간에 포함된다.
		*********************************************************/
		$array['bn_time_yn']   = 'N'; //목욕,간호 시간계산여부
		$array['bath_time']    = 0; //목욕시간
		$array['nursing_time'] = 0; //간호시간

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
			$array['client_gbn']	 = 'normal';	//타, 동거 구분
			$array['date']			 = $date;	//일자
			$array['holiday_yn']	 = 'N';		//휴일여부
			$array['holiday_yn_not'] = 'N';     //법정공휴일을 인정하지 않을 경우의 휴일여부
			$array['paid_yn']		 = 'N';		//유무급 여부
			$array['client_rate']	 = 0;		//본인부담율
			$array['weekday']        = 0;        //요일
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

		$array['PAYE'] = 0; //원천소득세

		$array['deduct_amt']		= 0;	//공제총액

		return $array;
	}

	// 분을 시간으로 변경
	function set_hour2minute($mem, $salary_kind, $family_yn, $day_work_hour){
		if ($salary_kind == '3'){
			/*********************************************************
				일소정근로시간
				- (일기준근로시간 * 주소정근로일수 + 주휴) / 7일

				월소정근로시간
				- 일소정근로시간 * 365일 /12개월
			*********************************************************/
			$int_workhour = ($day_work_hour * 6 + $day_work_hour) / 7;
			$mem['normal_work_time'] = $int_workhour * 365 / 12 * 60;
			$mem['total_work_time']  = $mem['normal_work_time'];
			$mem['total_com_time']   = $mem['normal_work_time'];

			/*********************************************************
				월급제는 초과근무수당을 발생하지 않는다.
			*********************************************************/
			for($j=1; $j<=4; $j++){
				$lvl = ($j == 4 ? 9 : $j);

				$mem[$lvl.'_normal_prolong_hour']			= 0;
				$mem[$lvl.'_normal_night_hour']				= 0;

				$mem[$lvl.'_normal_holiday_hour']			= 0;
				$mem[$lvl.'_normal_holiday_prolong_hour']	= 0;
				$mem[$lvl.'_normal_holiday_night_hour']		= 0;
			}
		}else{
			/*********************************************************
				월급제를 제외한 근무시간
			*********************************************************/
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


		/*********************************************************

			목욕, 간호 시간도 근무시간에 포함한다.

		*********************************************************/
		if ($mem['bn_time_yn'] == 'Y'){
			$mem['total_work_time']	+= $mem['bath_time'];
			$mem['total_work_time']	+= $mem['nursing_time'];
		}


		/*********************************************************

			동거가족시간

		*********************************************************/
		if ($family_yn == 'Y'){
			$mem['total_work_time']	+= $mem['family_work_time'];
			$mem['family_work_time'] = round($mem['family_work_time'] / 60, 1);
		}

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

		// 총근무시간 60시간보다 작으면.
		if ($mem['total_work_time'] < 60){
			$mem['week_cnt'] = round($mem['total_work_time'] / (60 / ($mem['week_cnt'] > 0 ? $mem['week_cnt'] : 1)));
		}

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

		/*********************************************************
			18~20시 추가공제항목 추가
		*********************************************************/
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
?>
<?
	// 기관의 법정휴일 유무와 유급여부
	$sql = "select m00_bath_add_yn
			,      m00_nursing_add_yn
			,      m00_law_holiday_yn
			,      m00_law_holiday_pay_yn
			,      m00_day_work_hour
			,      m00_day_hourly
			,      m00_bath_cut_yn
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
	$bath_cut_yn    = $temp_data[6]; //목욕수당 차감여부



	/*********************************************************

		년별 최소 시급(임시방편)

	*********************************************************/
		$nowYear = date('Y', mktime());
		if ($year != $nowYear){
			$sql = 'select g07_pay_time
					  from g07minpay
					 where g07_year = \''.$nowYear.'\'';

			$nowMinPay = $conn->get_data($sql);

			if ($year == '2010' && $nowMinPay == '4580')
				$day_hourly = 4110;
			else if ($year == '2011' && $nowMinPay == '4580')
				$day_hourly = 4320;
		}
	/********************************************************/












	if ($holiday_yn != 'Y') $holiday_pay_yn = 'N';

	// 일요일 리스트
	$sunday_list = $myF->sunday_list($year, $month);

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

		$holiday_index = sizeof($holiday_list);

		// 일요일은 유급에서 제외한다.(주휴에 포함되어 있으므로...)
		for($i=0; $i<sizeof($sunday_list); $i++){
			if (!check_in_date($holiday_list, str_replace('-', '', $sunday_list[$i]))){
				$holiday_list[$holiday_index]['date'] = str_replace('-', '', $sunday_list[$i]);
				$holiday_list[$holiday_index]['pay']  = 'N';
				$holiday_index ++;
			}
		}

		$holiday_index = sizeof($holiday_list);
	}else{
		/*********************************************************
			임시 휴일리스트
			- 법정공휴일을 인정하지 않을경우의 휴일리스트
		*********************************************************/
		$sql = "select mdate, holiday_name
				  from tbl_holiday
				 where mdate like '$year$month%'
				 order by mdate";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$tmp_holiday_list[$i]['date'] = $row['mdate'];
			$tmp_holiday_list[$i]['pay']  = 'Y';
		}

		$conn->row_free();

		$tmp_holiday_index = sizeof($tmp_holiday_list);

		// 일요일은 유급에서 제외한다.(주휴에 포함되어 있으므로...)
		for($i=0; $i<sizeof($sunday_list); $i++){
			if (!check_in_date($tmp_holiday_list, str_replace('-', '', $sunday_list[$i]))){
				$tmp_holiday_list[$tmp_holiday_index]['date'] = str_replace('-', '', $sunday_list[$i]);
				$tmp_holiday_list[$tmp_holiday_index]['pay']  = 'N';
				$tmp_holiday_index ++;
			}
		}

		$holiday_index = 0;
	}

	if ($month == '05'){//근로자의 날은 무조건 휴일처리한다.
		if (!check_in_date($holiday_list, $year.'0501')){
			$holiday_list[$holiday_index]['date'] = $year.'0501';
			$holiday_list[$holiday_index]['pay']  = 'Y';
		}
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

		if (!check_in_date($holiday_list, $row['m06_date'])){
			$holiday_list[$holiday_index]['date'] = $row['m06_date'];
			$holiday_list[$holiday_index]['pay']  = $row['m06_pay_yn'];

			$holiday_index ++;
		}
	}

	$holiday_index = sizeof($holiday_list); // 휴일 다음 인덱스

	$conn->row_free();

	function check_in_date($array, $date){
		if (is_array($array)){
			$count = sizeof($array);
			$in_date = false;

			for($i=0; $i<$count; $i++){
				if ($array[$i]['date'] == $date){
					$in_date = true;
					break;
				}
			}
		}else{
			$in_date = false;
		}

		return $in_date;
	}
?>
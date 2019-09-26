<?
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

	/******************************************************************************

		- 직원별 주휴요일

	******************************************************************************/
		$sql = "select m02_yjumin, m02_weekly_holiday
				  from m02yoyangsa
				 where m02_ccode = '$code'
				   and m02_mkind = ".$conn->_mem_kind();

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$weekly_holiday[$row['m02_yjumin']] = $row['m02_weekly_holiday'];
		}

		$conn->row_free();

	/******************************************************************************/

	// 일요일 리스트
	//$sunday_list = $myF->sunday_list($year, $month);

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

		/*
		// 일요일은 유급에서 제외한다.(주휴에 포함되어 있으므로...)
		for($i=0; $i<sizeof($sunday_list); $i++){
			if (!check_in_date($holiday_list, str_replace('-', '', $sunday_list[$i]))){
				$holiday_list[$holiday_index]['date'] = str_replace('-', '', $sunday_list[$i]);
				$holiday_list[$holiday_index]['pay']  = 'N';
				$holiday_index ++;
			}
		}

		$holiday_index = sizeof($holiday_list);
		*/
	}else{
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
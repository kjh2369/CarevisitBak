<?

	// 요양보호사 등급별 시급
	$sql = "select m02_mkind as kind
			,      m02_jumin as jumin
			,      m02_gubun as lvl
			,      m02_pay as pay
			  from m02pay
			 where m02_ccode = '$code'
			   and m02_mkind = '0'";

	if ($is_preview == true){
		$sql .= " and m02_jumin = '$member_code'";
	}

	$sql .= "
			 order by m02_jumin, m02_gubun";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$pay_list_lvl[$row['jumin']][$row['lvl']] = $row['pay'];
	}

	$conn->row_free();

	// 요양보호사별 국민연금 신고 월급여액
	$sql = "select m02_yjumin as jumin
			,      m02_ykuksin_mpay as annuity_pay
			,      m02_health_mpay as health_pay
			,      m02_employ_mpay as employ_pay
			,      m02_sanje_mpay as sanje_pay

			,      m02_ykmbohum_umu	as annuity_yn
			,      m02_ygnbohum_umu	as health_yn
			,      m02_ygobohum_umu	as employ_yn
			,      m02_ysnbohum_umu	as sanje_yn

			  from m02yoyangsa
			 where m02_ccode = '$code'
			   and m02_mkind = ".$conn->_mem_kind();

	if ($is_preview == true){
		$sql .= " and m02_yjumin = '$member_code'";
	}

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$mon_pay[$row['jumin']]['annuity'] = $row['annuity_pay'];
		$mon_pay[$row['jumin']]['health']  = $row['health_pay'];
		$mon_pay[$row['jumin']]['employ']  = $row['employ_pay'];
		$mon_pay[$row['jumin']]['sanje']   = $row['sanje_pay'];

		$mon_pay[$row['jumin']]['annuity_yn'] = $row['annuity_yn'];
		$mon_pay[$row['jumin']]['health_yn']  = $row['health_yn'];
		$mon_pay[$row['jumin']]['employ_yn']  = $row['employ_yn'];
		$mon_pay[$row['jumin']]['sanje_yn']   = $row['sanje_yn'];
	}

	$conn->row_free();

	// 요양보호사 급여 방법 및 시급, 총액비율
	$sql = "select m02_mkind as svc_kind
			,      m02_yjumin as jumin
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
			,      m02_add_payrate as add_payrate
			,      m02_holiday_payrate_yn as holiday_pay_yn
			,      m02_holiday_payrate as holiday_payrate
			,      m02_bnpay_yn as bn_yn
			,      m02_bipay_yn as bipay_yn
			,      m02_bipay_rate as bipay_rate
			,      m02_stnd_work_time as stnd_time
			,      m02_stnd_work_pay as stnd_pay
			  from m02yoyangsa
			 where m02_ccode = '$code'";

	if ($is_preview == true){
		$sql .= " and m02_yjumin = '$member_code'";
	}

	$sql .= "
			 order by m02_yjumin, m02_mkind";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$pay_list_my[$row['jumin']][$row['svc_kind']]['kind'] = $row['kind'];	//급여방식
		$pay_list_my[$row['jumin']][$row['svc_kind']]['type'] = $row['type'];	//고정시급여부
		$pay_list_my[$row['jumin']][$row['svc_kind']]['pay']  = $row['pay'];	//급여
		$pay_list_my[$row['jumin']][$row['svc_kind']]['rate'] = $row['rate'];	//총액비율

		if ($row['svc_kind'] >= 'A' && $row['svc_kind'] <= 'C'){
			#if ($row['bipay_yn'] == 'Y'){
			#	$pay_list_my[$row['jumin']][$row['svc_kind']]['kind'] = 4;	 //급여방식
			#	$pay_list_my[$row['jumin']][$row['svc_kind']]['type'] = 'N'; //고정시급여부
			#	$pay_list_my[$row['jumin']][$row['svc_kind']]['pay']  = 0;	 //급여
			#	$pay_list_my[$row['jumin']][$row['svc_kind']]['rate'] = $row['bipay_rate'];	//총액비율
			#}else{
				$pay_list_my[$row['jumin']][$row['svc_kind']]['kind'] = 1;	 //급여방식
				$pay_list_my[$row['jumin']][$row['svc_kind']]['type'] = 'Y'; //고정시급여부
				$pay_list_my[$row['jumin']][$row['svc_kind']]['pay']  = $row['stnd_pay'];	//급여
				$pay_list_my[$row['jumin']][$row['svc_kind']]['rate'] = 0; //총액비율
			#}
		}

		if ($row['svc_kind'] == '0'){
			$pay_list_my[$row['jumin']]['bn_yn']= $row['bn_yn'];//목욕간호수당 포함여부

			// 동거가족 유무
			if ($row['family_yn'] != '0'){
				$pay_list_my[$row['jumin']]['family_yn']   = 'Y';
				$pay_list_my[$row['jumin']]['family_type'] = $row['family_type'];
				$pay_list_my[$row['jumin']]['family_pay']  = $row['family_pay'];
			}else{
				$pay_list_my[$row['jumin']]['family_yn']   = 'N';
				$pay_list_my[$row['jumin']]['family_type'] = '0';
				$pay_list_my[$row['jumin']]['family_pay']  = 0;
			}
		}

		$pay_list_my[$row['jumin']]['deduct1'] = $row['deduct1'];	//공제대상 수
		$pay_list_my[$row['jumin']]['deduct2'] = $row['deduct2'];	//20세이하 자녀수

		$pay_list_my[$row['jumin']]['ins_yn']   = $row['ins_yn'];		//4대보험 여부
		$pay_list_my[$row['jumin']]['rank_pay'] = $row['rank_pay'];		//직급수당
		$pay_list_my[$row['jumin']]['add_rate']	= $row['add_payrate'];	//추가수당비율

		$pay_list_my[$row['jumin']]['holiday_pay_yn'] = $row['holiday_pay_yn'];  //휴일추가수당여부
		$pay_list_my[$row['jumin']]['holiday_rate']   = $row['holiday_payrate']; //휴일추가수당비율

		$pay_list_my[$row['jumin']]['stnd_time'] = $row['stnd_time'];	//기준시간
		$pay_list_my[$row['jumin']]['stnd_pay']  = $row['stnd_pay'];	//기준시급

	}

	$conn->row_free();
?>
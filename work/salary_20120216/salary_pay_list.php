<?
	/*********************************************************

		시급

	*********************************************************/
		$sql = 'select mh_jumin as cd
				,      mh_kind as kind
				,      mh_svc as svc
				,      mh_type as type
				,      mh_hourly as hourly
				,      mh_vary_hourly_1 as hourly_1
				,      mh_vary_hourly_2 as hourly_2
				,      mh_vary_hourly_3 as hourly_3
				,      mh_vary_hourly_9 as hourly_9
				,      mh_hourly_rate as rate
				,      mh_fixed_pay as fixed
				,      mh_extra_yn as extra
				  from mem_hourly
				 where org_no      = \''.$code.'\'
				   and mh_from_dt <= \''.$year.$month.'\'
				   and mh_to_dt   >= \''.$year.$month.'\'
				   and del_flag    = \'N\'';

		if ($is_preview)
			$sql .= ' and mh_jumin = \''.$member_code.'\'';

		$sql .= ' order by cd, svc';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$memHourlyIf[$row['cd']][$row['svc']] = array(
				 'type'=>$row['type']
				,'hourly'=>$row['hourly']
				,'hourly_1'=>$row['hourly_1']
				,'hourly_2'=>$row['hourly_2']
				,'hourly_3'=>$row['hourly_3']
				,'hourly_9'=>$row['hourly_9']
				,'rate'=>$row['rate']
				,'fixed'=>$row['fixed']
				,'extraYN'=>$row['extra']
				,'yn'=>'Y');
		}

		$conn->row_free();



	/*********************************************************

		월급

	*********************************************************/
		$sql = 'select ms_jumin as cd
				,      ms_salary as pay
				,      ms_care_yn as care
				,      ms_extra_yn as extra
				  from mem_salary
				 where org_no      = \''.$code.'\'
				   and ms_from_dt <= \''.$year.$month.'\'
				   and ms_to_dt   >= \''.$year.$month.'\'
				   and del_flag    = \'N\'';

		if ($is_preview)
			$sql .= ' and ms_jumin = \''.$member_code.'\'';

		$sql .= ' order by cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$memSalaryIf[$row['cd']] = array('yn'=>'Y', 'pay'=>$row['pay'], 'careYN'=>$row['care'], 'extraYN'=>$row['extra']);
		}

		$conn->row_free();



	// 요양보호사 등급별 시급
	$sql = "select m02_jumin as jumin
			,      m02_gubun as lvl
			,      m02_pay as pay
			  from m02pay
			 where m02_ccode = '$code'
			   and m02_mkind = '$kind'";

	if ($is_preview == true){
		$sql .= " and m02_jumin = '$member_code'";
	}

	$sql .= "
			 order by m02_gubun";

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
			   and m02_mkind = '$kind'";

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
			,      m02_add_payrate as add_payrate
			,      m02_holiday_payrate_yn as holiday_pay_yn
			,      m02_holiday_payrate as holiday_payrate
			,      m02_bnpay_yn as bn_yn
			,      m02_paye_yn as paye_yn
			  from m02yoyangsa
			 where m02_ccode = '$code'
			   and m02_mkind = '$kind'";

	if ($is_preview == true){
		$sql .= " and m02_yjumin = '$member_code'";
	}

	$sql .= "
			 order by m02_yjumin";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$pay_list_my[$row['jumin']]['kind']    = $row['kind'];	  //급여방식
		$pay_list_my[$row['jumin']]['type']    = $row['type'];	  //고정시급여부
		$pay_list_my[$row['jumin']]['pay']     = $row['pay'];	  //급여
		$pay_list_my[$row['jumin']]['rate']    = $row['rate'];	  //총액비율
		$pay_list_my[$row['jumin']]['bn_yn']   = $row['bn_yn'];   //목욕간호수당 포함여부

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

		$pay_list_my[$row['jumin']]['ins_yn']   = $row['ins_yn'];	//4대보험 여부
		$pay_list_my[$row['jumin']]['rank_pay'] = $row['rank_pay']; //직급수당
		$pay_list_my[$row['jumin']]['add_rate']	= $row['add_payrate'];	//추가수당비율

		$pay_list_my[$row['jumin']]['holiday_pay_yn'] = $row['holiday_pay_yn'];  //휴일추가수당여부
		$pay_list_my[$row['jumin']]['holiday_rate']   = $row['holiday_payrate']; //휴일추가수당비율

		//원천징수대상자 여부
		$pay_list_my[$row['jumin']]['PAYE'] = $row['paye_yn'];


		//급여 계산을 위한 임시 적용
			$pay_list_my[$row['jumin']]['kind'] = $memHourlyIf[$row['jumin']]['11']['type'];

			if ($pay_list_my[$row['jumin']]['kind'] == '1'){
				$pay_list_my[$row['jumin']]['type'] = 'Y';
			}else if ($pay_list_my[$row['jumin']]['kind'] == '2'){
				$pay_list_my[$row['jumin']]['type'] = 'N';
			}else{
				$pay_list_my[$row['jumin']]['type'] = ' ';
			}

			switch($memHourlyIf[$row['jumin']]['11']['type']){
				case '1':
					$pay_list_my[$row['jumin']]['pay'] = $memHourlyIf[$row['jumin']]['11']['hourly'];
					$pay_list_lvl[$row['jumin']]['1'] = 0;
					$pay_list_lvl[$row['jumin']]['2'] = 0;
					$pay_list_lvl[$row['jumin']]['3'] = 0;
					$pay_list_lvl[$row['jumin']]['9'] = 0;
					$pay_list_my[$row['jumin']]['rate']  = 0;
					$pay_list_my[$row['jumin']]['bn_yn'] = $memHourlyIf[$row['jumin']]['11']['extraYN'];

					break;

				case '2':
					$pay_list_my[$row['jumin']]['pay'] = 0;
					$pay_list_lvl[$row['jumin']]['1'] = $memHourlyIf[$row['jumin']]['11']['hourly_1'];
					$pay_list_lvl[$row['jumin']]['2'] = $memHourlyIf[$row['jumin']]['11']['hourly_2'];
					$pay_list_lvl[$row['jumin']]['3'] = $memHourlyIf[$row['jumin']]['11']['hourly_3'];
					$pay_list_lvl[$row['jumin']]['9'] = $memHourlyIf[$row['jumin']]['11']['hourly_9'];
					$pay_list_my[$row['jumin']]['rate']  = 0;
					$pay_list_my[$row['jumin']]['bn_yn'] = $memHourlyIf[$row['jumin']]['11']['extraYN'];

					break;

				case '3':
					$pay_list_my[$row['jumin']]['pay'] = $memHourlyIf[$row['jumin']]['11']['fixed'];
					$pay_list_lvl[$row['jumin']]['1'] = 0;
					$pay_list_lvl[$row['jumin']]['2'] = 0;
					$pay_list_lvl[$row['jumin']]['3'] = 0;
					$pay_list_lvl[$row['jumin']]['9'] = 0;
					$pay_list_my[$row['jumin']]['rate']  = 0;
					$pay_list_my[$row['jumin']]['bn_yn'] = $memHourlyIf[$row['jumin']]['11']['extraYN'];

					break;

				case '4':
					$pay_list_my[$row['jumin']]['pay'] = 0;
					$pay_list_lvl[$row['jumin']]['1'] = 0;
					$pay_list_lvl[$row['jumin']]['2'] = 0;
					$pay_list_lvl[$row['jumin']]['3'] = 0;
					$pay_list_lvl[$row['jumin']]['9'] = 0;
					$pay_list_my[$row['jumin']]['rate']  = $memHourlyIf[$row['jumin']]['11']['rate'];
					$pay_list_my[$row['jumin']]['bn_yn'] = $memHourlyIf[$row['jumin']]['11']['extraYN'];

					break;

				default:
					if ($memSalaryIf[$row['jumin']]['pay'] > 0){
						$pay_list_my[$row['jumin']]['kind'] = '3';
						$pay_list_my[$row['jumin']]['pay'] = $memSalaryIf[$row['jumin']]['pay'];
						$pay_list_lvl[$row['jumin']]['1'] = 0;
						$pay_list_lvl[$row['jumin']]['2'] = 0;
						$pay_list_lvl[$row['jumin']]['3'] = 0;
						$pay_list_lvl[$row['jumin']]['9'] = 0;
						$pay_list_my[$row['jumin']]['rate']  = 0;
						$pay_list_my[$row['jumin']]['bn_yn'] = 'N';
					}
			}

			if (is_array($memHourlyIf[$row['jumin']]['12'])){
				$pay_list_my[$row['jumin']]['family_yn']   = 'Y';
				$pay_list_my[$row['jumin']]['family_type'] = $memHourlyIf[$row['jumin']]['12']['type'];

				switch($memHourlyIf[$row['jumin']]['12']['type']){
					case '1':
						$pay_list_my[$row['jumin']]['family_pay'] = $memHourlyIf[$row['jumin']]['12']['hourly'];
						break;

					case '3':
						$pay_list_my[$row['jumin']]['family_pay'] = $memHourlyIf[$row['jumin']]['12']['fixed'];
						break;

					case '4':
						$pay_list_my[$row['jumin']]['family_pay'] = $memHourlyIf[$row['jumin']]['12']['rate'];
						break;
				}
			}else{
				$pay_list_my[$row['jumin']]['family_yn']   = 'N';
				$pay_list_my[$row['jumin']]['family_type'] = '0';
				$pay_list_my[$row['jumin']]['family_pay']  = 0;
			}

	}

	$conn->row_free();
?>
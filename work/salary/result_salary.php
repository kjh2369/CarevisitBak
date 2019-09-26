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

	// 급여함수
	include_once('../work/salary_function.php');

	$rst = change_db($conn, $code);

	// 추가수당 및 공제항목
	include_once('../work/salary_addon.php');

	// 4대보험 부담비율
	include_once('../work/salary_ins.php');

	// 요양보호사 등급별 시급 / 요양보호사 급여 방법 및 시급, 총액비율
	include_once('../work/salary_pay_list.php');

	// 기관의 법정휴일 유무와 유급여부
	include_once('../work/salary_holiday.php');

	// 주일수
	//$week_count = $myF->weekCount($year, $month);

	/*
	$week_count = 0;

	for($i=0; $i<sizeof($sunday_list); $i++){
		$is_duplicate = false;
		for($j=0; $j<sizeof($holiday_list); $j++){
			if (str_replace('-', '', $sunday_list[$i]) == $holiday_list[$j]['date'] && $holiday_list[$j]['pay'] == 'Y'){
				$is_duplicate = true;
				break;
			}
		}

		if (!$is_duplicate) $week_count ++;
	}
	*/

	// 요양보호사 급여내역
	include_once('../work/salary_detail.php');

	$conn->begin();

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
	if (!$conn->execute("delete from salary_center_amt where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_center_amt 입력위한 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}
	if (!$conn->execute("delete from salary_detail where org_no = '$code' and salary_yymm = '$year$month'")){
		$conn->rollback();
		$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_center_amt 입력위한 삭제중 오류발생');
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
		 
		
		// 특별수당 인덱스 할당
		$sql = 'select count(*)
				  from salary_addon
				 where org_no       = \''.$code.'\'
				   and salary_type  = \'1\'
				   and salary_index < \'0\'';
		$add_pay_id = $conn->get_data($sql);
		

		// 18~20시 추가수당
		$salary_addon[1][$add_pay_id]['pay'] = $member[$i]['add_pay'] + $member[$i]['holiday_add_pay'] + $member[$i]['sunday_add_pay'];

		/*
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
		*/

		/*******************************************************

			타수급급여계산

			***************************************************/
			for($ii=0; $ii<=2; $ii++){
				$svc = get_svc_gbn($ii);
				$end = get_end_cnt($ii);

				for($j=1; $j<=$end; $j++){
					$lvl  = get_lvl_gbn($j, $ii);
					$kind = get_kind_gbn($j, $ii);

					if ($kind == '0'){
						$id = 'salary_type';
					}else{
						$id = $kind.'_salary_type';
					}

					switch($member[$i][$id]){
						case 1: //고정시급
							$hourly					= $member[$i][$svc.'_hourly'];				//시급
							$work_time				= $member[$i][$svc.'_work_time'];			//근무시간
							$prolong_hour			= $member[$i][$svc.'_prolong_hour'];		//연장근무시간
							$prolong_pay			= $member[$i][$svc.'_prolong_pay'];			//연장수당
							$night_hour				= $member[$i][$svc.'_night_hour'];			//야간근무시간
							$night_pay				= $member[$i][$svc.'_night_pay'];			//야간수당
							$holiday_hour			= $member[$i][$svc.'_holiday_hour'];		//휴일근무시간
							$holiday_pay			= $member[$i][$svc.'_holiday_pay'];			//휴일수당
							$holiday_prolong_hour	= $member[$i][$svc.'_holiday_prolong_hour'];//휴일연장시간
							$holiday_prolong_pay	= $member[$i][$svc.'_holiday_prolong_pay'];	//휴연수당
							$holiday_night_hour		= $member[$i][$svc.'_holiday_night_hour'];	//휴일야간시간
							$holiday_night_pay		= $member[$i][$svc.'_holiday_night_pay'];	//휴야수당

							$sql = "insert into salary_hourly values (
									 '$code'
									,'$year$month'
									,'$jumin'
									,'$kind'
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

							#echo $sql.'<br>';

							if (!$conn->execute($sql)){
								$conn->rollback();
								$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
								echo $myF->message('error', 'Y', 'Y');
								exit;
							}

							break;
						case 2: //변동시급
							$hourly					= $member[$i][$lvl.'_'.$svc.'_hourly'];			//시급
							$work_time				= $member[$i][$lvl.'_'.$svc.'_work_time'];			//근무시간
							$prolong_hour			= $member[$i][$lvl.'_'.$svc.'_prolong_hour'];			//연장근무시간
							$prolong_pay			= $member[$i][$lvl.'_'.$svc.'_prolong_pay'];			//연장수당
							$night_hour				= $member[$i][$lvl.'_'.$svc.'_night_hour'];			//야간근무시간
							$night_pay				= $member[$i][$lvl.'_'.$svc.'_night_pay'];			//야간수당
							$holiday_hour			= $member[$i][$lvl.'_'.$svc.'_holiday_hour'];			//휴일근무시간
							$holiday_pay			= $member[$i][$lvl.'_'.$svc.'_holiday_pay'];			//휴일수당
							$holiday_prolong_hour	= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_hour'];	//휴일연장시간
							$holiday_prolong_pay	= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_pay'];	//휴연수당
							$holiday_night_hour		= $member[$i][$lvl.'_'.$svc.'_holiday_night_hour'];	//휴일야간시간
							$holiday_night_pay		= $member[$i][$lvl.'_'.$svc.'_holiday_night_pay'];	//휴야수당

							$sql = "insert into salary_hourly values (
								 '$code'
								,'$year$month'
								,'$jumin'
								,'$kind'
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
							break;
						case 3: //월급
							$monthly = $member[$i][$svc.'_salary'];

							$sql = "insert into salary_monthly values (
									 '$code'
									,'$year$month'
									,'$jumin'
									,'$kind'
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
							$suga_rate		= $member[$i][$svc.'_rate'];
							$suga_total		= $member[$i][$svc.'_suga'];
							$suga_except	= $member[$i][$svc.'_suga_except'];
							$suga_pay		= $member[$i][$svc.'_svc_pay'];

							$sql = "insert into salary_rate values (
									 '$code'
									,'$year$month'
									,'$jumin'
									,'$kind'
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

					if ($member[$i][$id] != 2) break;
				}
			}

		/*******************************************************/



		/***********************************************************
			상세급여내역
			*******************************************************/
			for($ii=0; $ii<=2; $ii++){
				$svc = get_svc_gbn($ii);
				$end = get_end_cnt($ii);

				$hourly					= 0;	//시급
				$work_day				= 0;	//근무일
				$work_time				= 0;	//근무시간
				$prolong_hour			= 0;	//연장근무시간
				$prolong_pay			= 0;	//연장수당
				$night_hour				= 0;	//야간근무시간
				$night_pay				= 0;	//야간수당
				$holiday_hour			= 0;	//휴일근무시간
				$holiday_pay			= 0;	//휴일수당
				$holiday_prolong_hour	= 0;	//휴일연장시간
				$holiday_prolong_pay	= 0;	//휴연수당
				$holiday_night_hour		= 0;	//휴일야간시간
				$holiday_night_pay		= 0;	//휴야수당

				$hourly			= $member[$i]['min_hourly'];		//시급
				$work_day		= $member[$i][$svc.'_work_count'];	//근무일

				for($j=1; $j<=$end; $j++){
					$lvl  = get_lvl_gbn($j, $ii);
					$kind = get_kind_gbn($j, $ii);

					$work_time				+= $member[$i][$lvl.'_'.$svc.'_work_time'];				//근무시간
					$prolong_hour			+= $member[$i][$lvl.'_'.$svc.'_prolong_hour'];			//연장근무시간
					$prolong_pay			+= $member[$i][$lvl.'_'.$svc.'_prolong_pay'];			//연장수당
					$night_hour				+= $member[$i][$lvl.'_'.$svc.'_night_hour'];			//야간근무시간
					$night_pay				+= $member[$i][$lvl.'_'.$svc.'_night_pay'];				//야간수당
					$holiday_hour			+= $member[$i][$lvl.'_'.$svc.'_holiday_hour'];			//휴일근무시간
					$holiday_pay			+= $member[$i][$lvl.'_'.$svc.'_holiday_pay'];			//휴일수당
					$holiday_prolong_hour	+= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_hour'];	//휴일연장시간
					$holiday_prolong_pay	+= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_pay'];	//휴연수당
					$holiday_night_hour		+= $member[$i][$lvl.'_'.$svc.'_holiday_night_hour'];	//휴일야간시간
					$holiday_night_pay		+= $member[$i][$lvl.'_'.$svc.'_holiday_night_pay'];		//휴야수당
				}

				#echo $jumin.'/'.$kind.'/'.$member[$i][$id].'<br>';
				#echo '- hourly : '.$hourly.'<br>';
				#echo '- work_day : '.$work_day.'<br>';
				#echo '- work_time : '.$work_time.'<br>';
				#echo '- prolong_hour : '.$prolong_hour.'<br>';
				#echo '- prolong_pay : '.$prolong_pay.'<br>';
				#echo '- night_hour : '.$night_hour.'<br>';
				#echo '- night_pay : '.$night_pay.'<br>';
				#echo '- holiday_hour : '.$holiday_hour.'<br>';
				#echo '- holiday_pay : '.$holiday_pay.'<br>';
				#echo '- holiday_prolong_hour : '.$holiday_prolong_hour.'<br>';
				#echo '- holiday_prolong_pay : '.$holiday_prolong_pay.'<br>';
				#echo '- holiday_night_hour : '.$holiday_night_hour.'<br>';
				#echo '- holiday_night_pay : '.$holiday_night_pay.'<br>';

				$sql = "insert into salary_detail values (
						 '$code'
						,'$year$month'
						,'$jumin'
						,'$kind'
						,'1'
						,'$hourly'
						,'$work_day'
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

				#echo nl2br($sql).'<br>';
				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			}
		/***********************************************************/




		// 동거가족
		if ($member[$i]['family_yn'] == 'Y'){
			#$member[$i]['family_type']	= $pay_gbn['family_type'];
			#$member[$i]['family_pay']	= $pay_gbn['family_pay'];

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
						,'0'
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

				#echo '5<br>'.$sql.'<br>';

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
						,'0'
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
						,'0'
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

			/***********************************************************
				상세급여내역
				*******************************************************/
				$hourly			= $member[$i]['min_hourly'];		//시급
				$work_day		= $member[$i]['family_work_cnt'];	//근무일

				$work_time				= $member[$i]['family_work_time'];				//근무시간
				$prolong_hour			= $member[$i]['family_prolong_hour'];			//연장근무시간
				$prolong_pay			= $member[$i]['family_prolong_pay'];			//연장수당
				$night_hour				= $member[$i]['family_night_hour'];			//야간근무시간
				$night_pay				= $member[$i]['family_night_pay'];				//야간수당
				$holiday_hour			= $member[$i]['family_holiday_hour'];			//휴일근무시간
				$holiday_pay			= $member[$i]['family_holiday_pay'];			//휴일수당
				$holiday_prolong_hour	= $member[$i]['family_holiday_prolong_hour'];	//휴일연장시간
				$holiday_prolong_pay	= $member[$i]['family_holiday_prolong_pay'];	//휴연수당
				$holiday_night_hour		= $member[$i]['family_holiday_night_hour'];	//휴일야간시간
				$holiday_night_pay		= $member[$i]['family_holiday_night_pay'];		//휴야수당

				#echo $jumin.'/'.$kind.'/'.$member[$i][$id].'<br>';
				#echo '- hourly : '.$hourly.'<br>';
				#echo '- work_day : '.$work_day.'<br>';
				#echo '- work_time : '.$work_time.'<br>';
				#echo '- prolong_hour : '.$prolong_hour.'<br>';
				#echo '- prolong_pay : '.$prolong_pay.'<br>';
				#echo '- night_hour : '.$night_hour.'<br>';
				#echo '- night_pay : '.$night_pay.'<br>';
				#echo '- holiday_hour : '.$holiday_hour.'<br>';
				#echo '- holiday_pay : '.$holiday_pay.'<br>';
				#echo '- holiday_prolong_hour : '.$holiday_prolong_hour.'<br>';
				#echo '- holiday_prolong_pay : '.$holiday_prolong_pay.'<br>';
				#echo '- holiday_night_hour : '.$holiday_night_hour.'<br>';
				#echo '- holiday_night_pay : '.$holiday_night_pay.'<br>';

				$sql = "insert into salary_detail values (
						 '$code'
						,'$year$month'
						,'$jumin'
						,'0'
						,'2'
						,'$hourly'
						,'$work_day'
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

				#echo nl2br($sql).'<br>';
				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_hourly 입력중 오류발생');
					echo $myF->message('error', 'Y', 'Y');
					exit;
				}
			/***********************************************************/
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

		/*
		// 3.급여 기본 데이타(salary_basic)
		//,'".($member[$i]['1_normal_work_time']+$member[$i]['2_normal_work_time']+$member[$i]['3_normal_work_time']+$member[$i]['9_normal_work_time']+$member[$i]['family_work_time'])."'
		//,'".($member[$i]['normal_work_time']+$member[$i]['family_work_time'])."'
		$sql = "insert into salary_basic values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($member[$i]['salary_type'])."'
				,'".($member[$i]['normal_work_cnt']+$member[$i]['family_work_cnt'])."'
				,'".($member[$i]['total_work_time'])."'
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
				,'".($member[$i]['1_normal_holiday_pay']+$member[$i]['2_normal_holi123day_pay']+$member[$i]['3_normal_holiday_pay']+$member[$i]['9_normal_holiday_pay']+$member[$i]['family_holiday_pay'])."'
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
				,'".($member[$i]['rank_pay'])."'
				,'".($mon_pay[$jumin])."')";

		//echo '9<br>'.$sql.'<br>';
		*/

		$salary = init_pt();

		for($ii=0; $ii<=2; $ii++){
			$svc = get_svc_gbn($ii);
			$end = get_end_cnt($ii);

			for($j=1; $j<=$end; $j++){
				$lvl  = get_lvl_gbn($j, $ii);
				$kind = get_kind_gbn($j, $ii);

				if ($kind == '0'){
					$id = 'salary_type';
				}else{
					$id = $kind.'_salary_type';
				}

				$salary['prolong_hour']			+= $member[$i][$lvl.'_'.$svc.'_prolong_hour'];
				$salary['night_hour']			+= $member[$i][$lvl.'_'.$svc.'_night_hour'];
				$salary['holiday_hour']			+= $member[$i][$lvl.'_'.$svc.'_holiday_hour'];
				$salary['holiday_prolong_hour']	+= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_hour'];
				$salary['holiday_night_hour']	+= $member[$i][$lvl.'_'.$svc.'_holiday_night_hour'];

				$salary['prolong_pay']			+= $member[$i][$lvl.'_'.$svc.'_prolong_pay'];
				$salary['night_pay']			+= $member[$i][$lvl.'_'.$svc.'_night_pay'];
				$salary['holiday_pay']			+= $member[$i][$lvl.'_'.$svc.'_holiday_pay'];
				$salary['holiday_prolong_pay']	+= $member[$i][$lvl.'_'.$svc.'_holiday_prolong_pay'];
				$salary['holiday_night_pay']	+= $member[$i][$lvl.'_'.$svc.'_holiday_night_pay'];
			}
		}

		$sql = "insert into salary_basic values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($member[$i][$id])."'
				,'".($member[$i]['normal_work_cnt']+$member[$i]['family_work_cnt']+$member[$i]['normal_holiday_count']+$member[$i]['family_holiday_count'])."'
				,'".($member[$i]['total_work_time'])."'
				,'".($member[$i]['week_cnt'])."'
				,'".($member[$i]['week_amt'])."'
				,'".($member[$i]['paid_cnt'])."'
				,'".($member[$i]['paid_amt'])."'
				,'".($member[$i]['bath_cnt'])."'
				,'".($member[$i]['bath_amt'])."'
				,'".($member[$i]['nursing_cnt'])."'
				,'".($member[$i]['nursing_amt'])."'
				,'".($salary['prolong_hour'])."'
				,'".($salary['prolong_pay'])."'
				,'".($salary['night_hour'])."'
				,'".($salary['night_pay'])."'
				,'".($salary['holiday_hour'])."'
				,'".($salary['holiday_pay'])."'
				,'".($salary['holiday_prolong_hour'])."'
				,'".($salary['holiday_prolong_pay'])."'
				,'".($salary['holiday_night_hour'])."'
				,'".($salary['holiday_night_pay'])."'
				,'".($member[$i]['base_pay'])."'
				,'".($member[$i]['base_pay'])."'
				,'".($member[$i]['meal_amt'])."'
				,'0'
				,'".($member[$i]['bojeon_pay'])."'
				,'".($member[$i]['bojeon_pay']+$member[$i]['meal_amt'])."'
				,'".($member[$i]['worker_annuity'])."'
				,'".($member[$i]['worker_health'])."'
				,'".($member[$i]['worker_oldcare'])."'
				,'".($member[$i]['worker_employ'])."'
				,'".($member[$i]['gapgeunse'])."'
				,'".($member[$i]['juminse'])."'
				,'".($member[$i]['rank_pay'])."'
				,'".($mon_pay[$jumin])."')";

		#echo $jumin.'<br>';
		#echo nl2br($sql).'<br>';
		#echo '<br>----------------------------------------------------------<br><br>';

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
					  ($salary['prolong_pay']+$member[$i]['family_prolong_pay']) +
					  ($salary['night_pay']+$member[$i]['family_night_pay']) +
					  ($salary['holiday_pay']+$member[$i]['family_holiday_pay']) +
					  ($salary['holiday_prolong_pay']+$member[$i]['family_holiday_prolong_pay']) +
					  ($salary['holiday_night_pay']+$member[$i]['family_holiday_night_pay']) +
					  ($member[$i]['rank_pay']);
		$basic_add	= $add_pay_1;
		$basic_tot	= $basic_amt + $basic_add;

		$deduct_amt	= $member[$i]['worker_annuity'] + $member[$i]['worker_health'] + $member[$i]['worker_oldcare'] + $member[$i]['worker_employ'] + $member[$i]['juminse'] + $member[$i]['gapgeunse'];
		$deduct_add	= $add_pay_2;
		$deduct_tot	= $deduct_amt + $deduct_add;

		$tot_diff	= $basic_tot - $deduct_tot;

		// 4.급여 합계(salary_amt)
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

		// 5.기관부담보험내역 저장
		$sql = "insert into salary_center_amt values (
				 '".($code)."'
				,'".($year.$month)."'
				,'".($jumin)."'
				,'".($member[$i]['center_annuity'])."'
				,'".($member[$i]['center_health'])."'
				,'".($member[$i]['center_oldcare'])."'
				,'".($member[$i]['center_employ'])."'
				,'".($member[$i]['center_sanje'])."'
				,'".($mon_pay[$jumin])."')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->batch_log('2', $year.$month, $batch_start_sec, $start_dt, $start_tm, 'salary_center_amt 입력중 오류발생');
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
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_check_class.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	//Array ( [work_cnt] => 1 [work_time] => 2 [weekly_cnt] => 0 [paid_cnt] => 0 [bath_cnt] => 0 [nursing_cnt] => 1 [base_pay] => 8,640 [weekly_pay] => 0 [paid_pay] => 0 [bath_pay] => 0 [nursing_pay] => 22,000 [meal_pay] => 5,360 [car_keep_pay] => 0 [bojeon_pay] => 0 [bojeon_max] => 5360 [tot_basic_pay] => 36,000 [prolong_hour] => 0.0 [night_hour] => 0.0 [holiday_hour] => 0.0 [holiday_prolong_hour] => 0.0 [holiday_night_hour] => 0.0 [tot_sudang_hour] => 0.0 [prolong_pay] => 0 [night_pay] => 0 [holiday_pay] => 0 [holiday_prolong_pay] => 0 [holiday_night_pay] => 0 [tot_sudang_pay] => 0 [pension_amt] => 630 [health_amt] => 390 [care_amt] => 20 [employ_amt] => 60 [tot_ins_pay] => 1,100 [tot_pay] => 86,000 [tot_deduct] => 1,100 [tot_diff] => 84,900 [tax_amt_1] => 0 [tax_amt_2] => 0 [tot_tax_pay] => 0 [rank_pay] => 50,000 [1_pay] => Array ( [0] => 0 [1] => 0 ) [1_index] => Array ( [0] => 1 [1] => 2 ) [tot_1_addon_pay] => 50,000 [2_pay] => Array ( [0] => 0 [1] => 0 [2] => 0 ) [2_index] => Array ( [0] => 1 [1] => 2 [2] => 3 ) [tot_2_addon_pay] => 0 [code] => 1234 [kind] => 0 [year] => 2011 [month] => 03 [jumin] => %A7D%E6%82%D1%E3%9Au%AD%0D%DE0%CD [page] => 1 )

	$code	= $_POST['code'];			//기관코드
	$kind	= $_POST['kind'];			//기관분류
	$year	= $_POST['year'];			//년도
	$month	= $_POST['month'];			//월
	$jumin	= $ed->de($_POST['jumin']);	//직원주민번호
	$page	= $_POST['page'];			//페이지

	//
	$is_preview  = true;
	$member_code = $jumin;

	// 4대보험 부담비율
	include_once('../work/salary_ins.php');

	// 요양보호사 등급별 시급 / 요양보호사 급여 방법 및 시급, 총액비율
	include_once('../work/salary_pay_list.php');

	/*
	print_r($pay_list_lvl);
	echo '<br><br>';

	print_r($mon_pay);
	echo '<br><br>';

	print_r($pay_list_my);
	echo '<br><br>';

	print_r($ins_employ);
	echo '<br><br>';

	print_r($ins_health);
	echo '<br><br>';

	print_r($ins_oldcare);
	echo '<br><br>';

	print_r($ins_annuity);
	echo '<br><br>';

	print_r($ins_sanje);
	echo '<br><br>';

	print_r($ins_rate);
	echo '<br><br>';
	*/

	$sql = "select distinct *
			  from (
				   select salary_type
				   ,      salary_index
				   ,      salary_subject
				     from salary_addon
				    where org_no = '$code'
				    union all
				   select salary_type
				   ,      salary_index
				   ,      salary_subject
				     from salary_addon_pay
				    where org_no = '$code'
				      and salary_yymm  = '$year$month'
				      and salary_jumin = '$jumin'
				   ) as t";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$salary_addon[$row['salary_type']][$row['salary_index']]['subject']	= $row['salary_subject'];
	}

	$conn->row_free();

	// salary_basic(기본)
	$base_pay       = str_replace(',', '', $_POST['base_pay']);		//기본급
	$meal_pay		= str_replace(',', '', $_POST['meal_pay']);		//식대보조비
	$car_keep_pay	= str_replace(',', '', $_POST['car_keep_pay']);	//차량유지비
	$bojeon_pay		= str_replace(',', '', $_POST['bojeon_pay']);	//보전수당
	$rank_pay		= str_replace(',', '', $_POST['rank_pay']);		//직급수당

	// 과세급여합계
	/*
	$sql = "select base_pay + weekly_pay + paid_pay + bath_pay + nursing_pay as amt
			  from salary_basic
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";
	$tot_tax_amt  = $conn->get_data($sql);
	*/

	$tot_tax_amt  = $base_pay;
	$tot_tax_amt += $weekly_pay;
	$tot_tax_amt += $paid_pay;
	$tot_tax_amt += $bath_pay;
	$tot_tax_amt += $nursing_pay;

	$tot_tax_amt += $bojeon_pay;
	$tot_tax_amt += $rank_pay;

	$tmp_addon_cnt[1] = sizeof($_POST['1_pay']);
	$tmp_addon_cnt[2] = sizeof($_POST['2_pay']);

	for($i=1; $i<=2; $i++){
		for($j=0; $j<$tmp_addon_cnt[$i]; $j++){
			$tmp_addon_pay[$i] += str_replace(',', '', $_POST[$i.'_pay'][$j]);
		}
	}

	//$tot_tax_amt += ($tmp_addon_pay[1] - $tmp_addon_pay[2]);
	$tot_tax_amt += $tmp_addon_pay[1];

	// 4대보험 적용여부
	if ($pay_list_my[$jumin]['ins_yn'] == 'Y'){
		if ($mon_pay[$jumin]['annuity'] > 0){
			$annuity_pay = $mon_pay[$jumin]['annuity'];
		}else{
			$annuity_pay = $tot_tax_amt;
		}

		if ($mon_pay[$jumin]['health'] > 0){
			$health_pay  = $mon_pay[$jumin]['health'];
		}else{
			$health_pay  = $tot_tax_amt;
		}

		if ($mon_pay[$jumin]['employ'] > 0){
			$employ_pay  = $mon_pay[$jumin]['employ'];
		}else{
			$employ_pay  = $tot_tax_amt;
		}

		if ($mon_pay[$jumin]['sanje'] > 0){
			$sanje_pay   = $mon_pay[$jumin]['sanje'];
		}else{
			$sanje_pay   = $tot_tax_amt;
		}

		$worker_employ	= $myF->cutOff($employ_pay	* $ins_rate['worker_employ']	* 0.01);	//고용보험 본인부담
		$worker_health	= $myF->cutOff($health_pay	* $ins_rate['worker_health']	* 0.01);	//건강보험 본인부담
		$worker_oldcare	= $myF->cutOff($worker_health * $ins_rate['worker_oldcare']	* 0.01);	//노인장기요양 본인부담
		$worker_annuity	= $myF->cutOff($annuity_pay	* $ins_rate['worker_annuity']	* 0.01);	//국민연금 본인부담
		$center_employ	= $myF->cutOff($employ_pay	* $ins_rate['center_employ']	* 0.01);	//고용보험 기관부담
		$center_health	= $myF->cutOff($health_pay	* $ins_rate['center_health']	* 0.01);	//건강보험 기관부담
		$center_oldcare	= $myF->cutOff($center_health * $ins_rate['center_oldcare']	* 0.01);	//노인장기요양 기관부담
		$center_annuity	= $myF->cutOff($annuity_pay	* $ins_rate['center_annuity']	* 0.01);	//국민연금 기관부담
		$center_sanje	= $myF->cutOff($sanje_pay	* $ins_rate['center_sanje']		* 0.01);	//산재보험 기관부담
		$deduct_amt		= $worker_employ+$worker_health+$worker_oldcare+$worker_annuity;
	}else{
		$worker_employ	= 0;	//고용보험 본인부담
		$worker_health	= 0;	//건강보험 본인부담
		$worker_oldcare	= 0;	//노인장기요양 본인부담
		$worker_annuity	= 0;	//국민연금 본인부담
		$center_employ	= 0;	//고용보험 기관부담
		$center_health	= 0;	//건강보험 기관부담
		$center_oldcare	= 0;	//노인장기요양 기관부담
		$center_annuity	= 0;	//국민연금 기관부담
		$center_sanje	= 0;	//산재보험 기관부담
		$deduct_amt		= 0;
	}

	// 갑근세
	$gapgeunse	= $check->gapgeunse($year, $tot_tax_amt, $pay_list_my[$jumin]['deduct1'], $pay_list_my[$jumin]['deduct2']);

	// 주민세
	$juminse	= $myF->cutOff($gapgeunse * 0.1);

	$tot_ins_pay = $worker_employ + $worker_health + $worker_oldcare + $worker_annuity;
	$tot_tax_pay = $gapgeunse + $juminse;

	$conn->begin();

	// 기본정보 수정
	$sql = "update salary_basic
			   set base_pay     = '$base_pay'
			,      meal_pay     = '$meal_pay'
			,      car_keep_pay = '$car_keep_pay'
			,      bojeon_pay   = '$bojeon_pay'
			,      rank_pay     = '$rank_pay'
			,      pension_amt  = '$worker_annuity'
			,      health_amt   = '$worker_health'
			,      care_amt     = '$worker_oldcare'
			,      employ_amt   = '$worker_employ'
			,      tax_amt_1    = '$gapgeunse'
			,      tax_amt_2    = '$juminse'
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";

	//echo nl2br($sql).'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '1';
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// 센터부담금액 수정
	$sql = "update salary_center_amt
			   set pension_amt  = '$center_annuity'
			,      health_amt   = '$center_health'
			,      care_amt     = '$center_oldcare'
			,      employ_amt   = '$center_employ'
			,      sanje_amt    = '$center_sanje'
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";

	//echo nl2br($sql).'<br><br>';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '1';
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// salary_addon_pay(추가 수당 및 공제)
	$sql = "delete
			  from salary_addon_pay
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$addon_count = sizeof($_POST['1_pay']);

	if ($addon_count > 0){
		$first = false;
		$sql = "insert into salary_addon_pay values ";

		for($i=0; $i<$addon_count; $i++){
			$addon_pay		= str_replace(',', '', $_POST['1_pay'][$i]);
			$addon_index	= $_POST['1_index'][$i];
			$addon_subject	= $salary_addon[1][$addon_index]['subject'];

			if ($addon_pay > 0){
				if ($first == true) $sql .= ",";
				$first = true;
				$sql .= "('$code'
						 ,'$year$month'
						 ,'$jumin'
						 ,'1'
						 ,'$addon_index'
						 ,'$addon_subject'
						 ,'$addon_pay')";
			}
		}

		if ($first == true){
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '2';
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}
		}
	}

	$addon_count = sizeof($_POST['2_pay']);

	if ($addon_count > 0){
		$first = false;
		$sql = "insert into salary_addon_pay values ";

		for($i=0; $i<$addon_count; $i++){
			$addon_pay		= str_replace(',', '', $_POST['2_pay'][$i]);
			$addon_index	= $_POST['2_index'][$i];
			$addon_subject	= $salary_addon[2][$addon_index]['subject'];

			if ($addon_pay > 0){
				if ($first == true) $sql .= ",";
				$first = true;
				$sql .= "('$code'
						 ,'$year$month'
						 ,'$jumin'
						 ,'2'
						 ,'$addon_index'
						 ,'$addon_subject'
						 ,'$addon_pay')";
			}
		}

		if ($first == true){
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo '3';
				echo $myF->message('error', 'Y', 'Y');
				exit;
			}
		}
	}

	// salary_amt(총금액)
	$basic_amt	= str_replace(',', '', $_POST['tot_basic_pay']) + str_replace(',', '', $_POST['tot_sudang_pay']);
	$basic_add	= str_replace(',', '', $_POST['tot_1_addon_pay']);
	$basic_tot	= $basic_amt + $basic_add;

	//$deduct_amt	= str_replace(',', '', $_POST['tot_ins_pay']) + str_replace(',', '', $_POST['tot_tax_pay']);
	$deduct_amt	= $tot_ins_pay + $tot_tax_pay;
	$deduct_add	= str_replace(',', '', $_POST['tot_2_addon_pay']);
	$deduct_tot	= $deduct_amt + $deduct_add;

	//$tot_diff	= str_replace(',', '', $_POST['tot_diff']);
	$tot_diff	= $basic_tot - $deduct_tot;

	$sql = "update salary_amt
			   set basic_total_amt  = '$basic_amt'
			,      addon_total_amt  = '$basic_add'
			,      total_amt        = '$basic_tot'
			,      basic_deduct_amt = '$deduct_amt'
			,      addon_deduct_amt = '$deduct_add'
			,      deduct_amt       = '$deduct_tot'
			,      diff_amt         = '$tot_diff'
			 where org_no           = '$code'
			   and salary_yymm      = '$year$month'
			   and salary_jumin     = '$jumin'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo '4';
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('../work/salary_edit_2.php?code=<?=$code;?>&kind=<?=$kind;?>&year=<?=$year;?>&month=<?=$month;?>&jumin=<?=$ed->en($jumin);?>&page=<?=$page;?>');
</script>
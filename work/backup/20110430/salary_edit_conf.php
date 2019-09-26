<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	//Array ( [work_cnt] => 1 [work_time] => 2 [weekly_cnt] => 0 [paid_cnt] => 0 [bath_cnt] => 0 [nursing_cnt] => 1 [base_pay] => 8,640 [weekly_pay] => 0 [paid_pay] => 0 [bath_pay] => 0 [nursing_pay] => 22,000 [meal_pay] => 5,360 [car_keep_pay] => 0 [bojeon_pay] => 0 [bojeon_max] => 5360 [tot_basic_pay] => 36,000 [prolong_hour] => 0.0 [night_hour] => 0.0 [holiday_hour] => 0.0 [holiday_prolong_hour] => 0.0 [holiday_night_hour] => 0.0 [tot_sudang_hour] => 0.0 [prolong_pay] => 0 [night_pay] => 0 [holiday_pay] => 0 [holiday_prolong_pay] => 0 [holiday_night_pay] => 0 [tot_sudang_pay] => 0 [pension_amt] => 630 [health_amt] => 390 [care_amt] => 20 [employ_amt] => 60 [tot_ins_pay] => 1,100 [tot_pay] => 86,000 [tot_deduct] => 1,100 [tot_diff] => 84,900 [tax_amt_1] => 0 [tax_amt_2] => 0 [tot_tax_pay] => 0 [rank_pay] => 50,000 [1_pay] => Array ( [0] => 0 [1] => 0 ) [1_index] => Array ( [0] => 1 [1] => 2 ) [tot_1_addon_pay] => 50,000 [2_pay] => Array ( [0] => 0 [1] => 0 [2] => 0 ) [2_index] => Array ( [0] => 1 [1] => 2 [2] => 3 ) [tot_2_addon_pay] => 0 [code] => 1234 [kind] => 0 [year] => 2011 [month] => 03 [jumin] => %A7D%E6%82%D1%E3%9Au%AD%0D%DE0%CD [page] => 1 )

	$code	= $_POST['code'];			//기관코드
	$kind	= $_POST['kind'];			//기관분류
	$year	= $_POST['year'];			//년도
	$month	= $_POST['month'];			//월
	$jumin	= $ed->de($_POST['jumin']);	//직원주민번호
	$page	= $_POST['page'];			//페이지

	$conn->begin();

	// salary_basic(기본)
	$meal_pay		= str_replace(',', '', $_POST['meal_pay']);		//식대보조비
	$car_keep_pay	= str_replace(',', '', $_POST['car_keep_pay']);	//차량유지비
	$bojeon_pay		= str_replace(',', '', $_POST['bojeon_pay']);	//보전수당

	$sql = "update salary_basic
			   set meal_pay     = '$meal_pay'
			,      car_keep_pay = '$car_keep_pay'
			,      bojeon_pay   = '$bojeon_pay'
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";

	if (!$conn->execute($sql)){
		$conn->rollback();
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

	$addon_count	= sizeof($_POST['1_pay']);

	$sql = "insert into salary_addon_pay values ";

	for($i=0; $i<$addon_count; $i++){
		$addon_pay		= str_replace(',', '', $_POST['1_pay'][$i]);
		$addon_index	= $_POST['1_index'][$i];

		if ($i > 0) $sql .= ",";

		$sql .= "('$code'
				 ,'$year$month'
				 ,'$jumin'
				 ,'1'
				 ,'$addon_index'
				 ,'$addon_pay')";
	}

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$addon_count	= sizeof($_POST['2_pay']);

	$sql = "insert into salary_addon_pay values ";

	for($i=0; $i<$addon_count; $i++){
		$addon_pay		= str_replace(',', '', $_POST['2_pay'][$i]);
		$addon_index	= $_POST['2_index'][$i];

		if ($i > 0) $sql .= ",";

		$sql .= "('$code'
				 ,'$year$month'
				 ,'$jumin'
				 ,'2'
				 ,'$addon_index'
				 ,'$addon_pay')";
	}

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	// salary_amt(총금액)
	$basic_amt	= str_replace(',', '', $_POST['tot_basic_pay']) + str_replace(',', '', $_POST['tot_sudang_pay']);
	$basic_add	= str_replace(',', '', $_POST['tot_1_addon_pay']);
	$basic_tot	= $basic_amt + $basic_add;

	$deduct_amt	= str_replace(',', '', $_POST['tot_ins_pay']) + str_replace(',', '', $_POST['tot_tax_pay']);
	$deduct_add	= str_replace(',', '', $_POST['tot_2_addon_pay']);
	$deduct_tot	= $deduct_amt + $deduct_add;

	$tot_diff	= str_replace(',', '', $_POST['tot_diff']);

	$sql = "update salary_amt
			   set basic_total_amt  = '$basic_amt'
			,      addon_total_amt  = '$basic_add'
			,      total_amt        = '$basic_tot'
			,      basic_deduct_amt = '$deduct_amt'
			,      addon_deduct_amt = '$deduct_add'
			,      deduct_amt       = '$deduct_tot'
			,      diff_amt         = '$tot_diff'
			 where org_no       = '$code'
			   and salary_yymm  = '$year$month'
			   and salary_jumin = '$jumin'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
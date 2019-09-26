<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_ed.php");

	$p_code			= $_POST['p_code'];
	$p_kind			= $_POST['p_kind'];
	$p_jumin		= $ed->de($_POST['p_jumin']);
	$p_svc_subcode	= $_POST['p_svc_subcode'];
	$p_svc_subcd	= $_POST['p_svc_subcd'];
	$p_car_no		= $_POST['p_car_no'];
	$p_sugup_fmtime = $_POST['p_sugup_fmtime'];
	$p_sugup_totime = $_POST['p_sugup_totime'];
	$p_sugup_soyotime = $_POST['p_sugup_soyotime'];
	$p_family_gbn	= $_POST['p_family_gbn'];
	$p_bipay_gbn	= $_POST['p_bipay_gbn'];
	$p_week_day1	= $_POST['p_week_day1'];
	$p_week_day2	= $_POST['p_week_day2'];
	$p_week_day3	= $_POST['p_week_day3'];
	$p_week_day4	= $_POST['p_week_day4'];
	$p_week_day5	= $_POST['p_week_day5'];
	$p_week_day6	= $_POST['p_week_day6'];
	$p_week_day0	= $_POST['p_week_day0'];
	$p_week_use1	= $_POST['p_week_use1'];
	$p_week_use2	= $_POST['p_week_use2'];
	$p_week_use3	= $_POST['p_week_use3'];
	$p_week_use4	= $_POST['p_week_use4'];
	$p_week_use5	= $_POST['p_week_use5'];
	$p_week_use6	= $_POST['p_week_use6'];
	$p_week_use0	= $_POST['p_week_use0'];
	$p_yoy_jumin1	= $ed->de($_POST['p_yoy_jumin1']);
	$p_yoy_jumin2	= $ed->de($_POST['p_yoy_jumin2']);
	$p_yoy_jumin3	= $_POST['p_yoy_jumin3 '];
	$p_yoy_jumin4	= $_POST['p_yoy_jumin4'];
	$p_yoy_jumin5	= $_POST['p_yoy_jumin5'];
	$p_yoy_name1	= $_POST['p_yoy_name1'];
	$p_yoy_name2	= $_POST['p_yoy_name2'];
	$p_yoy_name3	= $_POST['p_yoy_name3'];
	$p_yoy_name4	= $_POST['p_yoy_name4'];
	$p_yoy_name5	= $_POST['p_yoy_name5'];
	$p_yoy_ta1		= $_POST['p_yoy_ta1'];
	$p_yoy_ta2		= $_POST['p_yoy_ta2'];
	$p_yoy_ta3		= $_POST['p_yoy_ta3'];
	$p_yoy_ta4		= $_POST['p_yoy_ta4'];
	$p_yoy_ta5		= $_POST['p_yoy_ta5'];
	$p_visit_chk	= $_POST['p_visit_chk'];
	$p_visit_amt	= $_POST['p_visit_amt'];
	$p_sudang_yul1	= $_POST['p_sudang_yul1'];
	$p_sudang_yul2	= $_POST['p_sudang_yul2'];
	$p_price_s		= $_POST['p_price_s'];
	$p_price_e		= $_POST['p_price_e'];
	$p_price_n		= $_POST['p_price_n'];
	$p_price_t		= $_POST['p_price_t'];
	$p_suga_code	= $_POST['p_suga_code'];
	$p_suga_name	= $_POST['p_suga_name'];
	$p_gubun_e		= $_POST['p_gubun_e'];
	$p_gubun_n		= $_POST['p_gubun_n'];
	$p_time_e		= $_POST['p_time_e'];
	$p_time_n		= $_POST['p_time_n'];

	if (empty($p_code)){
		$conn->close();
		exit;
	}

	$p_svc_dt = '';

	for($i=1; $i<=31; $i++){
		$p_svc_dt .= (!empty($_POST['p_svc_dt_'.$i])?$_POST['p_svc_dt_'.$i]:'N');
	}

	//$datetime = date("YmdHis", mkTime());
	$datetime = $p_ym.date("dHis", mkTime());
	$ym = date("Ym", mkTime());

	$sql = "delete
			  from t03pattern
			 where t03_ccode = '$p_code'
			   and t03_mkind = '$p_kind'
			   and t03_jumin = '$p_jumin'
			   and t03_datetime like '$ym%'
			   and t03_sugup_fmtime = '$p_sugup_fmtime'
			   and t03_sugup_totime = '$p_sugup_totime'
			   and t03_suga_code = '$p_suga_code'";
	$conn->execute($sql);

	$sql = "insert into t03pattern values (
			 '$p_code'
			,'$p_kind'
			,'$p_jumin'
			,'$datetime'
			,'$p_svc_subcode'
			,'$p_svc_subcd'
			,'$p_car_no'
			,'$p_sugup_fmtime'
			,'$p_sugup_totime'
			,'$p_sugup_soyotime'
			,'$p_family_gbn'
			,'$p_bipay_gbn'
			,'$p_week_day1'
			,'$p_week_day2'
			,'$p_week_day3'
			,'$p_week_day4'
			,'$p_week_day5'
			,'$p_week_day6'
			,'$p_week_day0'
			,'$p_week_use1'
			,'$p_week_use2'
			,'$p_week_use3'
			,'$p_week_use4'
			,'$p_week_use5'
			,'$p_week_use6'
			,'$p_week_use0'
			,'$p_yoy_jumin1'
			,'$p_yoy_jumin2'
			,'$p_yoy_jumin3'
			,'$p_yoy_jumin4'
			,'$p_yoy_jumin5'
			,'$p_yoy_name1'
			,'$p_yoy_name2'
			,'$p_yoy_name3'
			,'$p_yoy_name4'
			,'$p_yoy_name5'
			,'$p_yoy_ta1'
			,'$p_yoy_ta2'
			,'$p_yoy_ta3'
			,'$p_yoy_ta4'
			,'$p_yoy_ta5'
			,'$p_visit_chk'
			,'$p_visit_amt'
			,'$p_sudang_yul1'
			,'$p_sudang_yul2'
			,'$p_price_s'
			,'$p_price_e'
			,'$p_price_n'
			,'$p_price_t'
			,'$p_suga_code'
			,'$p_suga_name'
			,'$p_gubun_e'
			,'$p_gubun_n'
			,'$p_time_e'
			,'$p_time_n'
			,'$p_svc_dt')";
	if ($conn->execute($sql)){
		echo "Y";
	}else{
		echo "N";
		//echo $conn->error_query;
	}

	include_once("../inc/_db_close.php");
?>
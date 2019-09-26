<?
	include("../inc/_db_open.php");
	
	$p_code = $_POST['p_code'];
	$p_kind = $_POST['p_kind'];
	$p_jumin = $_POST['p_jumin'];
	$p_svc_subcode = $_POST['p_svc_subcode'];
	$p_svc_subcd = $_POST['p_svc_subcd'];
	$p_car_no = $_POST['p_car_no'];
	$p_sugup_fmtime = $_POST['p_sugup_fmtime'];
	$p_sugup_totime = $_POST['p_sugup_totime'];
	$p_sugup_soyotime = $_POST['p_sugup_soyotime'];
	$p_family_gbn = $_POST['p_family_gbn'];
	$p_bipay_gbn = $_POST['p_bipay_gbn'];
	$p_week_day1 = $_POST['p_week_day1'];
	$p_week_day2 = $_POST['p_week_day2'];
	$p_week_day3 = $_POST['p_week_day3'];
	$p_week_day4 = $_POST['p_week_day4'];
	$p_week_day5 = $_POST['p_week_day5'];
	$p_week_day6 = $_POST['p_week_day6'];
	$p_week_day0 = $_POST['p_week_day0'];
	$p_yoy_jumin1 = $_POST['p_yoy_jumin1'];
	$p_yoy_jumin2 = $_POST['p_yoy_jumin2'];
	$p_yoy_jumin3 = $_POST['p_yoy_jumin3 '];
	$p_yoy_jumin4 = $_POST['p_yoy_jumin4'];
	$p_yoy_jumin5 = $_POST['p_yoy_jumin5'];
	$p_yoy_name1 = $_POST['p_yoy_name1'];
	$p_yoy_name2 = $_POST['p_yoy_name2'];
	$p_yoy_name3 = $_POST['p_yoy_name3'];
	$p_yoy_name4 = $_POST['p_yoy_name4'];
	$p_yoy_name5 = $_POST['p_yoy_name5'];
	$p_yoy_ta1 = $_POST['p_yoy_ta1'];
	$p_yoy_ta2 = $_POST['p_yoy_ta2'];
	$p_yoy_ta3 = $_POST['p_yoy_ta3'];
	$p_yoy_ta4 = $_POST['p_yoy_ta4'];
	$p_yoy_ta5 = $_POST['p_yoy_ta5'];
	$p_visit_chk = $_POST['p_visit_chk'];
	$p_visit_amt = $_POST['p_visit_amt'];
	$p_sudang_yul1 = $_POST['p_sudang_yul1'];
	$p_sudang_yul2 = $_POST['p_sudang_yul2'];
	$p_price_s = $_POST['p_price_s '];
	$p_price_e = $_POST['p_price_e'];
	$p_price_n = $_POST['p_price_n'];
	$p_price_t = $_POST['p_price_t'];
	$p_suga_code = $_POST['p_suga_code'];
	$p_suga_name = $_POST['p_suga_name'];
	$p_gubun_e = $_POST['p_gubun_e'];
	$p_gubun_n = $_POST['p_gubun_n'];
	$p_time_e = $_POST['p_time_e'];
	$p_time_n = $_POST['p_time_n'];

	$date = date("Ymd", mkTime());
	$time = date("His", mkTime());

	$postData = $p_code.$p_kind.$p_jumin.$p_svc_subcode.$p_svc_subcd.$p_car_no.$p_sugup_fmtime.$p_sugup_totime.$p_sugup_soyotime.$p_family_gbn.$p_bipay_gbn.$p_week_day1.$p_week_day2.$p_week_day3.$p_week_day4.$p_week_day5.$p_week_day6.$p_week_day0.$p_yoy_jumin1.$p_yoy_jumin2.$p_yoy_jumin3.$p_yoy_jumin4.$p_yoy_jumin5.$p_yoy_name1.$p_yoy_name2.$p_yoy_name3.$p_yoy_name4.$p_yoy_name5.$p_yoy_ta1.$p_yoy_ta2.$p_yoy_ta3.$p_yoy_ta4.$p_yoy_ta5.$p_visit_chk.$p_visit_amt.$p_sudang_yul1.$p_sudang_yul2.$p_price_s.$p_price_e.$p_price_n.$p_price_t.$p_suga_code.$p_suga_name.$p_gubun_e.$p_gubun_n.$p_time_e.$p_time_n;

	$sql = "select *
			  from t03iljunghitory
			 where t03_ccode = '$p_code'
			   and t03_mkind = '$p_kind'
			   and t03_jumin = '$p_jumin'
			 order by t03_date, t03_time";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$rowData = $row['t03_svc_subcode'].$row['t03_svc_subcd'].$row['t03_car_no'].$row['t03_sugup_fmtime'].$row['t03_sugup_totime'].$row['t03_sugup_soyotime'].$row['t03_family_gbn'].$row['t03_bipay_gbn'].$row['t03_week_day1'].$row['t03_week_day2'].$row['t03_week_day3'].$row['t03_week_day4'].$row['t03_week_day5'].$row['t03_week_day6'].$row['t03_week_day7'].$row['t03_yoy_jumin1'].$row['t03_yoy_jumin2'].$row['t03_yoy_jumin3'].$row['t03_yoy_jumin4'].$row['t03_yoy_jumin5'].$row['t03_yoy_name1'].$row['t03_yoy_name2'].$row['t03_yoy_name3'].$row['t03_yoy_name4'].$row['t03_yoy_name5'].$row['t03_yoy_ta1'].$row['t03_yoy_ta2'].$row['t03_yoy_ta3'].$row['t03_yoy_ta4'].$row['t03_yoy_ta5'].$row['t03_visit_chk'].$row['t03_visit_amt'].$row['t03_sudang_yul1'].$row['t03_sudang_yul2'].$row['t03_price_s'].$row['t03_price_e'].$row['t03_price_n'].$row['t03_price_t'].$row['t03_suga_code'].$row['t03_suga_name'].$row['t03_gubun_e'].$row['t03_gubun_n'].$row['t03_time_e'].$row['t03_time_n'];

			if ($rowData != $postData){
				$sql = "insert into t03pattern values (
						 '$p_code'
						,'$p_kind'
						,'$p_jumin'
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
						,'$p_time_n')";
				//$conn->execute($sql);
				break;
			}
		}
	}else{
		$sql = "insert into t03pattern values (
				 '$p_code'
				,'$p_kind'
				,'$p_jumin'
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
				,'$p_time_n')";
		//$conn->execute($sql);
	}

	$conn->row_free();
	
	include("../inc/_db_close.php");

	//echo "<script>location.replace('su_reg.php?mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."&mKey=".$_POST["mKey"]."');</script>";
?>
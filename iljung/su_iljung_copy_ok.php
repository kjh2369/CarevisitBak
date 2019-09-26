<?
	include("../inc/_header.php");

	$con2 = new connection();
	$con3 = new connection();
	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mKey = $_POST['mKey'];
	$mJumin = $_POST['mJuminNo'];
	$mCopyYear = $_POST['copyYear'];
	$mCopyMonth = $_POST['copyMonth'];
	$mCopyDate = $mCopyYear.$mCopyMonth;
	$mTargetYear = $_POST['calYear'];
	$mTargetMonth = $_POST['calMonth'];

	//$mTargetDate = explode('-', date('Y-m-d', mkTime(0, 0, 1, date(m)+1, 1, date(Y))));

	$con3->begin();

	/*
	$sql = 'insert into t01iljung (t01_ccode, t01_mkind, t01_jumin, t01_sugup_date, t01_sugup_fmtime, t01_sugup_seq, t01_sugup_totime, t01_sugup_soyotime, t01_sugup_yoil, t01_svc_subcode, t01_svc_subcd, t01_status_gbn, t01_svc_name, t01_toge_umu, t01_bipay_umu, t01_time_doub, t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5, t01_yname1, t01_yname2, t01_yname3, t01_yname4, t01_yname5, t01_suga_code1, t01_suga, t01_suga_over, t01_suga_night, t01_suga_tot, t01_ysigup, t01_plan_work, t01_plan_sudang, t01_plan_cha) '
		 . 'select t01_ccode, t01_mkind, t01_jumin, concat(\''.$mTargetYear.$mTargetMonth.'\',right(t01_sugup_date,2)), t01_sugup_fmtime, t01_sugup_seq, t01_sugup_totime, t01_sugup_soyotime, t01_sugup_yoil, t01_svc_subcode, t01_svc_subcd, \'9\', t01_svc_name, t01_toge_umu, t01_bipay_umu, t01_time_doub, t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5, t01_yname1, t01_yname2, t01_yname3, t01_yname4, t01_yname5, t01_suga_code1, t01_suga, t01_suga_over, t01_suga_night, t01_suga_tot, t01_ysigup, t01_plan_work, t01_plan_sudang, t01_plan_cha'
		 . '  from t01iljung'
		 . ' where t01_ccode = \''.$mCode
		 . '\' and t01_mkind = \''.$mKind
		 . '\' and t01_jumin = \''.$mJumin
		 . '\' and left(t01_sugup_date, 6) = \''.$mCopyDate
		 . '\'';
	if (!$conn->query($sql)){
		$conn->rollback();
		echo "<script>
				alert('데이타 저장 중 오류가 발생하였습니다.');
				history.back();
			  </script>";
		exit;
	}
	*/

	$sql = "select *"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mJumin
		 . "'  and left(t01_sugup_date, 6) = '".$mCopyDate
		 . "'"
		 . " order by t01_sugup_fmtime"
		 . ",         t01_sugup_seq";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
	
		$weekDay = date("w", strtotime($mTargetYear."-".$mTargetMonth."-".subStr($row['t01_sugup_date'], strLen($row['t01_sugup_date']) - 2, 2)));
		$sugaCode = $row['t01_suga_code1'];
		$sugaCode1 = subStr($sugaCode,0,2);
		$sugaCode2 = subStr($sugaCode,3,2);

		if ($row['t01_svc_subcode'] != '500'){
			//if ($weekDay == 6 or $weekDay == 0){
			if ($weekDay == 0){
				$sugaCode = $sugaCode1.'H'.$sugaCode2;
			}else{
				$sugaCode = $sugaCode1.'W'.$sugaCode2;
			}
		}

		if ($sugaCode != $row['t01_suga_code1']){
			$sugaName  = GetSugaName($con2, $mCode, $sugaCode);
			$sugaValue = GetSugaValue($con2, $mCode, $sugaCode);
			$sPrice = $sugaValue;
			$ePrice = $row['t01_suga_over'];
			$nPrice = $row['t01_suga_night'];
			$tPrice = $row['t01_suga_tot'];

			if ($row['t01_suga_over'] > 0) $ePrice = cutOff($sPrice * 0.2);
			if ($row['t01_suga_night'] > 0) $nPrice = cutOff($sPrice * 0.3);

			$tPrice = $sPrice + $ePrice + $nPrice;
		}else{
			$sugaName = GetSugaName($con2, $mCode, $sugaCode);;
			$sPrice = $row['t01_suga'];
			$ePrice = $row['t01_suga_over'];
			$nPrice = $row['t01_suga_night'];
			$tPrice = $row['t01_suga_tot'];
		}

		$sql = "insert into t01iljung ("
			 . "  t01_ccode"
			 . ", t01_mkind"
			 . ", t01_jumin"
			 . ", t01_sugup_date"
			 . ", t01_sugup_fmtime"
			 . ", t01_sugup_seq"
			 . ", t01_sugup_totime"
			 . ", t01_sugup_soyotime"
			 . ", t01_sugup_yoil"
			 . ", t01_svc_subcode"
			 . ", t01_svc_subcd"
			 . ", t01_status_gbn"
			 . ", t01_svc_name"
			 . ", t01_toge_umu"
			 . ", t01_bipay_umu"
			 . ", t01_time_doub"
			 . ", t01_yoyangsa_id1"
			 . ", t01_yoyangsa_id2"
			 . ", t01_yoyangsa_id3"
			 . ", t01_yoyangsa_id4"
			 . ", t01_yoyangsa_id5"
			 . ", t01_yname1"
			 . ", t01_yname2"
			 . ", t01_yname3"
			 . ", t01_yname4"
			 . ", t01_yname5"
			 . ", t01_suga_code1"
			 . ", t01_suga"
			 . ", t01_suga_over"
			 . ", t01_suga_night"
			 . ", t01_suga_tot"
			 . ", t01_ysigup"
			 . ", t01_plan_work"
			 . ", t01_plan_sudang"
			 . ", t01_plan_cha"
			 . ", t01_car_no"
			 . ", t01_e_time"
			 . ", t01_n_time"
			 . ", t01_ysudang_yn"
			 . ", t01_ysudang"
			 . ", t01_ysudang_yul1"
			 . ", t01_ysudang_yul2"
			 . ") values ("
			 . "  '".$row['t01_ccode']
			 . "','".$row['t01_mkind']
			 . "','".$row['t01_jumin']
			 . "','".$mTargetYear.$mTargetMonth.subStr($row['t01_sugup_date'], strLen($row['t01_sugup_date']) - 2, 2)
			 . "','".$row['t01_sugup_fmtime']
			 . "','".$row['t01_sugup_seq']
			 . "','".$row['t01_sugup_totime']
			 . "','".$row['t01_sugup_soyotime']
			 . "','".$weekDay
			 . "','".$row['t01_svc_subcode']
			 . "','".$row['t01_svc_subcd']
			 . "','9"
			 . "','".$row['t01_svc_name']
			 . "','".$row['t01_toge_umu']
			 . "','".$row['t01_bipay_umu']
			 . "','".$row['t01_time_doub']
			 . "','".$row['t01_yoyangsa_id1']
			 . "','".$row['t01_yoyangsa_id2']
			 . "','".$row['t01_yoyangsa_id3']
			 . "','".$row['t01_yoyangsa_id4']
			 . "','".$row['t01_yoyangsa_id5']
			 . "','".$row['t01_yname1']
			 . "','".$row['t01_yname2']
			 . "','".$row['t01_yname3']
			 . "','".$row['t01_yname4']
			 . "','".$row['t01_yname5']
			 . "','".$sugaCode
			 . "','".$sPrice
			 . "','".$ePrice
			 . "','".$nPrice
			 . "','".$tPrice
			 . "','".$row['t01_ysigup']
			 . "','".$row['t01_plan_work']
			 . "','".$row['t01_plan_sudang']
			 . "','".$row['t01_plan_cha']
			 . "','".$row['t01_car_no']
			 . "','".$row['t01_e_time']
			 . "','".$row['t01_n_time']
			 . "','".$row['t01_ysudang_yn']
			 . "','".$row['t01_ysudang']
			 . "','".$row['t01_ysudang_yul1']
			 . "','".$row['t01_ysudang_yul2']
			 . "')";
		//echo $sql.'<br>';
		if (!$con3->query($sql)){
			$con3->rollback();
			echo "<script>
					alert('데이타 저장 중 오류가 발생하였습니다.');
					history.back();
				  </script>";
			exit;
		}
	}

	$con3->commit();
	$con2->close();
	$con3->close();
	include("../inc/_footer.php");
?>
<form name="f" method="post" action="su_reg.php">
	<input name="mCode" type="hidden" value="<?=$mCode;?>">
	<input name="mKind" type="hidden" value="<?=$mKind;?>">
	<input name="mKey" type="hidden" value="<?=$mKey;?>">
	<input name="calYear" type="hidden" value="<?=$mTargetYear;?>">
	<input name="calMonth" type="hidden" value="<?=$mTargetMonth;?>">
</form>
<script>
	document.f.submit();
</script>
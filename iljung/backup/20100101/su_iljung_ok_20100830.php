<?
	include("../inc/_db_open.php");
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
	<script src="../js/iljung.js" type="text/javascript"></script>
<?
	//print_r($_REQUEST);

	$conn->begin();

	$mLastDay = $_POST["mLastDay"];
	$mStatusGbn = "9";
	$mSudang = 0;

	$sql = "delete"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$_POST["mCode"]
		 . "'  and t01_mkind = '".$_POST["mKind"]
		 . "'  and t01_jumin = '".$_POST["mJuminNo"]
		 . "'  and left(t01_sugup_date, 6) = '".$_POST["calYear"].$_POST["calMonth"]
		 . "'";
	$result = $conn->query($sql);

	for($mDay=1; $mDay<=$mLastDay; $mDay++){
		$checkLoop = true;
		$checkIndex = 1;

		if ($result){
		}else{
			$conn->rollback();
			echo "<script>
					alert('데이타 저장 중 오류가 발생하였습니다.');
					history.back();
				  </script>";
			exit;
		}

		while($checkLoop){
			$mUse        = $_POST["mUse_".$mDay."_".$checkIndex];
			$mDuplicate  = $_POST["mDuplicate_".$mDay."_".$checkIndex];
			$mSurplusAmt = $_POST["mTValue_".$mDay."_".$checkIndex] - $mSudang;

			if ($_POST["mDate_".$mDay."_".$checkIndex] != ""){
				if ($mDuplicate != "Y") $mDuplicate = "N";
				if ($mUse == "Y" and $mDuplicate == "N"){
					$sql = "insert into t01iljung ("
						 . "  t01_ccode"
						 . ", t01_mkind"
						 . ", t01_jumin"
						 . ", t01_sugup_date"
						 . ", t01_sugup_fmtime"
						 . ", t01_sugup_totime"
						 . ", t01_sugup_soyotime"
						 . ", t01_sugup_yoil"
						 . ", t01_svc_subcode"
						 . ", t01_svc_subcd"
						 . ", t01_status_gbn"
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
						 . ") values ("
						 . "  '".$_POST["mCode"]
						 . "','".$_POST["mKind"]
						 . "','".$_POST["mJuminNo"]
						 . "','".$_POST["mDate_".$mDay."_".$checkIndex]
						 . "','".$_POST["mFmTime_".$mDay."_".$checkIndex]
						 . "','".$_POST["mToTime_".$mDay."_".$checkIndex]
						 . "','".$_POST["mProcTime_".$mDay."_".$checkIndex]
						 . "','".$_POST["mWeekDay_".$mDay."_".$checkIndex]
						 . "','".$_POST["mSvcSubCode_".$mDay."_".$checkIndex]
						 . "','".$_POST["mSvcSubCD_".$mDay."_".$checkIndex]
						 . "','".$mStatusGbn
						 . "','".$_POST["mTogeUmu_".$mDay."_".$checkIndex]
						 . "','".$_POST["mBiPayUmu_".$mDay."_".$checkIndex]
						 . "','".$_POST["mTimeDoub_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoy1_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoy2_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoy3_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoy4_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoy5_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyNm1_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyNm2_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyNm3_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyNm4_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyNm5_".$mDay."_".$checkIndex]
						 . "','".$_POST["mSugaCode_".$mDay."_".$checkIndex]
						 . "','".$_POST["mSValue_".$mDay."_".$checkIndex]
						 . "','".$_POST["mEValue_".$mDay."_".$checkIndex]
						 . "','".$_POST["mNValue_".$mDay."_".$checkIndex]
						 . "','".$_POST["mTValue_".$mDay."_".$checkIndex]
						 . "','".$_POST["mYoyTA1_".$mDay."_".$checkIndex]
						 . "','".$_POST["mProcTime_".$mDay."_".$checkIndex]
						 . "','".$mSudang
						 . "','".$mSurplusAmt
						 . "')";
					$result = $conn->query($sql);
					
					if ($result){
					}else{
						$conn->rollback();
						echo "<script>
								alert('데이타 저장 중 오류가 발생하였습니다.');
								history.back();
							  </script>";
						exit;
					}
				}
			}else{
				break;
			}

			$checkIndex++;
		}
	}

	$conn->commit();
	
	include("../inc/_db_close.php");

	echo "<script>location.replace('su_reg.php?mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."&mKey=".$_POST["mKey"]."');</script>";
?>
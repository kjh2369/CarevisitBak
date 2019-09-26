<?
	include("../inc/_db_open.php");
	include("../inc/_ed.php");
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
	<script src="../js/iljung.js" type="text/javascript"></script>
<?

	//print_r($_REQUEST);

	$conn->begin();

	$workType = $_POST['mWorkType'];

	$mLastDay = $_POST["mLastDay"];
	$mStatusGbn = "9";
	$mSudang = 0;

	/*
	$sql = "delete"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$_POST["mCode"]
		 . "'  and t01_mkind = '".$_POST["mKind"]
		 . "'  and t01_jumin = '".$_POST["mJuminNo"]
		 . "'  and left(t01_sugup_date, 6) = '".$_POST["calYear"].$_POST["calMonth"]
		 . "'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo "<script>
				alert('데이타 저장 중 오류가 발생하였습니다.');
				history.back();
			  </script>";
		exit;
	}
	*/

	for($mDay=1; $mDay<=$mLastDay; $mDay++){
		$checkLoop = true;
		$checkIndex = 1;

		while($checkLoop){
			$mUse        = $_POST["mUse_".$mDay."_".$checkIndex];
			$mDuplicate  = $_POST["mDuplicate_".$mDay."_".$checkIndex];
			$mSugupja    = $_POST["mSugupja_".$mDay."_".$checkIndex];
			$mTrans      = $_POST["mTrans_".$mDay."_".$checkIndex];
			$mStatus     = $_POST["mStatusGbn_".$mDay."_".$checkIndex];
			$mSurplusAmt = $_POST["mTValue_".$mDay."_".$checkIndex] - $mSudang;

			//echo $_POST["mDate_".$mDay."_".$checkIndex].'<br>';

			if ($_POST["mDate_".$mDay."_".$checkIndex] != ""){
				if ($mDuplicate != "Y") $mDuplicate = "N";
				if ($mSugupja != "Y") $mSugupja = "N";
				if ($mTrans != "Y") $mTrans = "N";
				if ($mStatus == "" || $mStatus == "0") $mStatus = "9";

				if ($workType == 'modify' ||
					$workType == 'dayModify'){
					$mTrans = 'N';
				}
				$mTrans = 'N'; // 전송여부 상관없이 삭제한다.

				//echo 'DATE : '.$_POST["mDate_".$mDay."_".$checkIndex].' TIME : '.$_POST["mFmTime_".$mDay."_".$checkIndex].'-'.$_POST["mToTime_".$mDay."_".$checkIndex].'------>USE : '.$mUse.' / Duplicate : '.$mDuplicate.' / Sugupja : '.$mSugupja.' / Trans : '.$mTrans.' / Status : '.$mStatus.'<br>';

				if ($mUse == "Y" and $mDuplicate == "N" and $mSugupja == "N" and $mTrans == "N" and $mStatus == "9"){
					if ($_POST["mSeq_".$mDay."_".$checkIndex] == '0'){
						$sql = "select ifnull(max(t01_sugup_seq), 0)"
							 . "  from t01iljung"
							 . " where t01_ccode        = '".$_POST["mCode"]
							 . "'  and t01_mkind        = '".$_POST["mKind"]
							 . "'  and t01_jumin        = '".$_POST["mJuminNo"]
							 . "'  and t01_sugup_date   = '".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_fmtime = '".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "'";
						$conn->query($sql);
						$row = $conn->fetch();
						$newSeq = $row[0];
						$newSeq = $newSeq + 1;
						$conn->row_free();

						$sql = "insert into t01iljung ("
							 . "  t01_ccode"
							 . ", t01_mkind"
							 . ", t01_jumin"
							 . ", t01_sugup_date"
							 . ", t01_sugup_fmtime"
							 . ", t01_sugup_seq"
							 . ", t01_sugup_totime"
							 . ") values ("
							 . "  '".$_POST["mCode"]
							 . "','".$_POST["mKind"]
							 . "','".$_POST["mJuminNo"]
							 . "','".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "','".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "','".$newSeq
							 . "','".$_POST["mToTime_".$mDay."_".$checkIndex]
							 . "')";
						if (!$conn->query($sql)){
							$conn->rollback();
							echo "<script>
									alert('데이타 저장 중 오류가 발생하였습니다.');
									history.back();
								  </script>";
							exit;
						}
					}else{
						$newSeq = $_POST["mSeq_".$mDay."_".$checkIndex];
					}

					if ($_POST["mDelete_".$mDay."_".$checkIndex] == 'N'){
						// 진행시간을 계산하여 입력한다.
						$tempFH = intVal(subStr($_POST["mFmTime_".$mDay."_".$checkIndex],0,2));
						$tempFM = intVal(subStr($_POST["mFmTime_".$mDay."_".$checkIndex],2,2));
						$tempTH = intVal(subStr($_POST["mToTime_".$mDay."_".$checkIndex],0,2));
						$tempTM = intVal(subStr($_POST["mToTime_".$mDay."_".$checkIndex],2,2));

						if ($tempFH > $tempTH) $tempTH += 24;

						$procTime = ($tempTH * 60 + $tempTM) - ($tempFH * 60 + $tempFM);

						$sql = "update t01iljung"
							 . "   set t01_sugup_totime   = '".$_POST["mToTime_".$mDay."_".$checkIndex]
							 //. "',     t01_sugup_soyotime = '".$_POST["mProcTime_".$mDay."_".$checkIndex]
							 . "',     t01_sugup_soyotime = '".$procTime
							 . "',     t01_sugup_yoil     = '".$_POST["mWeekDay_".$mDay."_".$checkIndex]
							 . "',     t01_svc_subcode    = '".$_POST["mSvcSubCode_".$mDay."_".$checkIndex]
							 . "',     t01_svc_subcd      = '".$_POST["mSvcSubCD_".$mDay."_".$checkIndex]
							 . "',     t01_status_gbn     = '".$mStatusGbn
							 . "',     t01_toge_umu       = '".$_POST["mTogeUmu_".$mDay."_".$checkIndex]
							 . "',     t01_bipay_umu      = '".$_POST["mBiPayUmu_".$mDay."_".$checkIndex]
							 . "',     t01_time_doub      = '".$_POST["mTimeDoub_".$mDay."_".$checkIndex]
							 . "',     t01_yoyangsa_id1   = '".$_POST["mYoy1_".$mDay."_".$checkIndex]
							 . "',     t01_yoyangsa_id2   = '".$_POST["mYoy2_".$mDay."_".$checkIndex]
							 . "',     t01_yoyangsa_id3   = '".$_POST["mYoy3_".$mDay."_".$checkIndex]
							 . "',     t01_yoyangsa_id4   = '".$_POST["mYoy4_".$mDay."_".$checkIndex]
							 . "',     t01_yoyangsa_id5   = '".$_POST["mYoy5_".$mDay."_".$checkIndex]
							 . "',     t01_yname1         = '".$_POST["mYoyNm1_".$mDay."_".$checkIndex]
							 . "',     t01_yname2         = '".$_POST["mYoyNm2_".$mDay."_".$checkIndex]
							 . "',     t01_yname3         = '".$_POST["mYoyNm3_".$mDay."_".$checkIndex]
							 . "',     t01_yname4         = '".$_POST["mYoyNm4_".$mDay."_".$checkIndex]
							 . "',     t01_yname5         = '".$_POST["mYoyNm5_".$mDay."_".$checkIndex]
							 . "',     t01_suga_code1     = '".$_POST["mSugaCode_".$mDay."_".$checkIndex]
							 . "',     t01_suga           = '".str_replace(',','',$_POST["mSValue_".$mDay."_".$checkIndex])
							 . "',     t01_suga_over      = '".str_replace(',','',$_POST["mEValue_".$mDay."_".$checkIndex])
							 . "',     t01_suga_night     = '".str_replace(',','',$_POST["mNValue_".$mDay."_".$checkIndex])
							 . "',     t01_suga_tot       = '".str_replace(',','',$_POST["mTValue_".$mDay."_".$checkIndex])
							 . "',     t01_ysigup         = '".str_replace(',','',$_POST["mYoyTA1_".$mDay."_".$checkIndex])
							 //. "',     t01_plan_work      = '".$_POST["mProcTime_".$mDay."_".$checkIndex]
							 . "',     t01_plan_work      = '".$procTime
							 . "',     t01_plan_sudang    = '".$mSudang
							 . "',     t01_plan_cha       = '".$mSurplusAmt
							 . "',     t01_car_no         = '".$_POST["mCarNo_".$mDay."_".$checkIndex]
							 . "',     t01_e_time         = '".$_POST["mETime_".$mDay."_".$checkIndex]
							 . "',     t01_n_time         = '".$_POST["mNTime_".$mDay."_".$checkIndex]
							 . "',     t01_ysudang_yn     = '".$_POST["mSudangYN_".$mDay."_".$checkIndex]
							 . "',     t01_ysudang        = '".str_replace(',','',$_POST["mSudang_".$mDay."_".$checkIndex])
							 . "',     t01_ysudang_yul1   = '".$_POST["mSudangYul1_".$mDay."_".$checkIndex]
							 . "',     t01_ysudang_yul2   = '".$_POST["mSudangYul2_".$mDay."_".$checkIndex]
							 . "',     t01_conf_suga_code = '".$_POST["mSugaCode_".$mDay."_".$checkIndex]
							 . "',     t01_conf_suga_value= '".str_replace(',','',$_POST["mTValue_".$mDay."_".$checkIndex])
							 . "',     t01_holiday        = '".$_POST["mHoliday_".$mDay."_".$checkIndex]
							 . "',     t01_modify_pos     = '".$_POST["mModifyPos_".$mDay."_".$checkIndex]
							 . "'"
							 . " where t01_ccode        = '".$_POST["mCode"]
							 . "'  and t01_mkind        = '".$_POST["mKind"]
							 . "'  and t01_jumin        = '".$_POST["mJuminNo"]
							 . "'  and t01_sugup_date   = '".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_fmtime = '".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_seq    = '".$newSeq
							 . "'";
					}else{
						$sql = "update t01iljung"
							 . "   set t01_del_yn = 'Y'"
							 . ",      t01_trans_yn = 'N'"
							 . " where t01_ccode        = '".$_POST["mCode"]
							 . "'  and t01_mkind        = '".$_POST["mKind"]
							 . "'  and t01_jumin        = '".$_POST["mJuminNo"]
							 . "'  and t01_sugup_date   = '".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_fmtime = '".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_seq    = '".$newSeq
							 . "'";
					}
					if (!$conn->query($sql)){
						$conn->rollback();
						echo "<script>
								alert('데이타 저장 중 오류가 발생하였습니다.');
								history.back();
							  </script>";
						exit;
					}
				}else{
					//echo $mUse.'/'.$mDuplicate.'/'.$mSugupja.'/'.$mTrans.'/'.$mStatus.'<br>';
				}
			}else{
				break;
			}

			$checkIndex++;
		}
	}

	$conn->commit();

	include("../inc/_db_close.php");

	if ($workType == 'modify'){
		echo "<script>";
		echo "opener.location.replace('../work/month_conf_sugupja.php?curMcode=".$_POST["mCode"]."&curMkind=".$_POST["mKind"]."&curYear=".$_POST['calYear']."&curMonth=".$_POST['calMonth']."&curSugupja=".$ed->en($_POST["mJuminNo"])."');";
		echo "location.replace('su_modify.php?mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."&mSugupja=".$ed->en($_POST["mJuminNo"])."');";
		echo "</script>";
	}else if ($workType == 'dayModify'){
		echo "<script>";
		echo "opener.location.replace('../work/day_conf.php?mType=DAY&mCode=".$_POST['mCode']."&mKind=".$_POST['mKind']."&mYear=".$_POST['calYear']."&mMonth=".$_POST['calMonth']."&mDay=".$_POST['calDay']."');";
		echo "location.replace('su_modify.php?mType=DAY&mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."&calDay=".$_POST['calDay']."&mKey=".$_POST["mKey"]."&mSugupja=".$ed->en($_POST["mJuminNo"])."');";
		echo "</script>";
	}else{
		echo "<script>location.replace('su_reg.php?mCode=".$_POST["mCode"]."&mKind=".$_POST["mKind"]."&mKey=".$_POST["mKey"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."');</script>";
	}
?>
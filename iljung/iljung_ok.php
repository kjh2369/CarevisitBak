<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once('iljung_f.php');
?>
	<script src="../js/prototype.js" type="text/javascript"></script>
	<script src="../js/xmlHTTP.js" type="text/javascript"></script>
	<script src="../js/script.js" type="text/javascript"></script>
	<script src="../js/center.js" type="text/javascript"></script>
	<script src="../js/iljung.js" type="text/javascript"></script>
<?
	if ($debug)
		$debug_mdoe = 1;
	else
		$debug_mdoe = 1;

	$conn->mode = $debug_mdoe;

	$conn->begin();

	$workType = $_POST['mWorkType'];

	$mLastDay = $_POST["mLastDay"];
	$mStatusGbn = "9";
	$mSudang = 0;
	$jumin = $ed->de($_POST['jumin']);

	// 마감일자
	/*
	$sql = "select act_cls_dt_from
			,      act_bat_conf_flag
			  from closing_progress
			 where org_no       = '".$_POST["mCode"]."'
			   and closing_yymm = '".$_POST["calYear"].$_POST["calMonth"]."'";
	$closing = $conn->get_array($sql);

	if ($closing[0] == '')  $closing[0] = '9999-12-31';
	if ($closing[1] != 'Y') $closing[1] = 'N';

	if ($closing[0] <= date('Y-m-d', mktime()) || $closing[1] == 'Y'){
		echo $myF->message($_POST["calYear"].'년 '.$_POST["calMonth"].'월 실적등록마감이 완료되어 등록/수정/삭제가 불가합니다.', 'Y', 'Y');
		exit;
	}
	*/
	$closing_yn = $conn->get_closing_act($_POST["code"], $_POST["calYear"].$_POST["calMonth"]);

	if ($closing_yn == 'Y'){
		echo $myF->message($_POST["calYear"].'년 '.$_POST["calMonth"].'월 실적등록마감이 완료되어 등록/수정/삭제가 불가합니다.', 'Y', 'Y');
		exit;
	}

	/*
	$sql = "delete"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$_POST["mCode"]
		 . "'  and t01_mkind = '".$_POST["mKind"]
		 . "'  and t01_jumin = '".$_POST["mJuminNo"]
		 . "'  and left(t01_sugup_date, 6) = '".$_POST["calYear"].$_POST["calMonth"]
		 . "'";
	if (!$conn->execute($sql)){
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
							 . " where t01_ccode        = '".$_POST["code"]
							 . "'  and t01_mkind        = '".$_POST["mKind_".$mDay."_".$checkIndex] /*$_POST["kind"]*/
							 . "'  and t01_jumin        = '".$jumin
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
							 . "  '".$_POST["code"]
							 . "','".$_POST["mKind_".$mDay."_".$checkIndex] /*$_POST["kind"]*/
							 . "','".$jumin
							 . "','".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "','".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "','".$newSeq
							 . "','".$_POST["mToTime_".$mDay."_".$checkIndex]
							 . "')";
						if (!$conn->execute($sql)){
							$conn->rollback();
							echo $conn->err_back();
							if ($conn->mode == 1) exit;
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

						$memCD1 = $_POST["mYoy1_".$mDay."_".$checkIndex];
						$memCD2 = $_POST["mYoy2_".$mDay."_".$checkIndex];

						if (!is_numeric($memCD1)) $memCD1 = $ed->de($memCD1);
						if (!is_numeric($memCD2)) $memCD2 = $ed->de($memCD2);

						$sql = "update t01iljung"
							 . "   set t01_sugup_totime   = '".$_POST["mToTime_".$mDay."_".$checkIndex]
							 . "',     t01_sugup_soyotime = '".$procTime
							 . "',     t01_sugup_proctime = '".$_POST["mProcStr_".$mDay."_".$checkIndex]
							 . "',     t01_sugup_yoil     = '".$_POST["mWeekDay_".$mDay."_".$checkIndex]
							 . "',     t01_svc_subcode    = '".$_POST["mSvcSubCode_".$mDay."_".$checkIndex]
							 . "',     t01_svc_subcd      = '".$_POST["mSvcSubCD_".$mDay."_".$checkIndex]
							 . "',     t01_status_gbn     = '".$mStatusGbn
							 . "',     t01_toge_umu       = '".$_POST["mTogeUmu_".$mDay."_".$checkIndex]
							 . "',     t01_bipay_umu      = '".$_POST["mBiPayUmu_".$mDay."_".$checkIndex]
							 . "',     t01_time_doub      = '".$_POST["mTimeDoub_".$mDay."_".$checkIndex]

							 . "',     t01_yoyangsa_id1   = '".$memCD1
							 . "',     t01_yoyangsa_id2   = '".$memCD2
							 . "',     t01_yname1         = '".$_POST["mYoyNm1_".$mDay."_".$checkIndex]
							 . "',     t01_yname2         = '".$_POST["mYoyNm2_".$mDay."_".$checkIndex]

							 #. "',     t01_yoyangsa_id3   = '".$_POST["mYoy3_".$mDay."_".$checkIndex]
							 #. "',     t01_yoyangsa_id4   = '".$_POST["mYoy4_".$mDay."_".$checkIndex]
							 #. "',     t01_yoyangsa_id5   = '".$_POST["mYoy5_".$mDay."_".$checkIndex]

							 #. "',     t01_yname3         = '".$_POST["mYoyNm3_".$mDay."_".$checkIndex]
							 #. "',     t01_yname4         = '".$_POST["mYoyNm4_".$mDay."_".$checkIndex]
							 #. "',     t01_yname5         = '".$_POST["mYoyNm5_".$mDay."_".$checkIndex]

							 . "',     t01_mem_cd1 = '".$memCD1
							 . "',     t01_mem_cd2 = '".$memCD2
							 . "',     t01_mem_nm1 = '".$_POST["mYoyNm1_".$mDay."_".$checkIndex]
							 . "',     t01_mem_nm2 = '".$_POST["mYoyNm2_".$mDay."_".$checkIndex]

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
							 . "',     t01_modify_pos     = '".$_POST["mModifyPos_".$mDay."_".$checkIndex]."'";



							/**************************************************

								비급여 실지 지급 여무 및 실비금액

							**************************************************/
							$sql .= ", t01_bipay1      = '".str_replace(',','',$_POST["mBipay1_".$mDay."_".$checkIndex])."'
									 , t01_bipay2      = '".str_replace(',','',$_POST["mBipay2_".$mDay."_".$checkIndex])."'
									 , t01_bipay3      = '".str_replace(',','',$_POST["mBipay3_".$mDay."_".$checkIndex])."'
									 , t01_expense_yn  = '".$_POST["mExpenseYn_".$mDay."_".$checkIndex]."'
									 , t01_expense_pay = '".str_replace(',','',$_POST["mExpensePay_".$mDay."_".$checkIndex])."'";
							/*************************************************/



							/**************************************************

								산모신생아 및 산모유료 추가 단가

							**************************************************/
							//school_not_cnt=2&school_not_cost=5000&school_cnt=1&school_cost=10000&family_cnt=2&family_cost=7000&home_in_yn=N&home_in_cost=0&holiday_cost=80000
							//추가단가 정보를 배열에 담는다.
							parse_str($_POST['mAddPay_'.$mDay.'_'.$checkIndex], $addpay_if);

							$sql .= ", t01_not_school_cnt  = '".($addpay_if['school_not_cnt'])."'
								     , t01_not_school_cost = '".($addpay_if['school_not_cost'])."'
								     , t01_not_school_pay  = '".($addpay_if['school_not_cnt'] * $addpay_if['school_not_cost'])."'
								     , t01_school_cnt      = '".($addpay_if['school_cnt'])."'
									 , t01_school_cost     = '".($addpay_if['school_cost'])."'
									 , t01_school_pay      = '".($addpay_if['school_cnt'] * $addpay_if['school_cost'])."'
									 , t01_family_cnt      = '".($addpay_if['family_cnt'])."'
									 , t01_family_cost     = '".($addpay_if['family_cost'])."'
									 , t01_family_pay      = '".($addpay_if['family_cnt'] * $addpay_if['family_cost'])."'
									 , t01_home_in_yn      = '".($addpay_if['home_in_yn'] == 'Y' ? 'Y' : 'N')."'
									 , t01_home_in_cost    = '".($addpay_if['home_in_yn'] == 'Y' ? $addpay_if['home_in_cost'] : 0)."'
									 , t01_holiday_cost    = '".($addpay_if['holiday_cost'])."'";
							/*************************************************/



							/**************************************************

								기타

							**************************************************/
								parse_str($_POST['mOther_'.$mDay.'_'.$checkIndex], $other_if);

								$sql .= ", t01_bipay_kind = '".($other_if['bipay_kind'])."'";
							/*************************************************/

							$sql .= " where t01_ccode        = '".$_POST["code"]
							     .  "'  and t01_mkind        = '".$_POST["mKind_".$mDay."_".$checkIndex] /*$_POST["kind"]*/
							     .  "'  and t01_jumin        = '".$jumin
							     .  "'  and t01_sugup_date   = '".$_POST["mDate_".$mDay."_".$checkIndex]
							     .  "'  and t01_sugup_fmtime = '".$_POST["mFmTime_".$mDay."_".$checkIndex]
							     .  "'  and t01_sugup_seq    = '".$newSeq
							     .  "'";
					}else{
						$sql = "update t01iljung"
							 . "   set t01_del_yn       = 'Y'"
							 . ",      t01_trans_yn     = 'N'"
							 . " where t01_ccode        = '".$_POST["code"]
							 . "'  and t01_mkind        = '".$_POST["mKind_".$mDay."_".$checkIndex] /*$_POST["kind"]*/
							 . "'  and t01_jumin        = '".$jumin
							 . "'  and t01_sugup_date   = '".$_POST["mDate_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_fmtime = '".$_POST["mFmTime_".$mDay."_".$checkIndex]
							 . "'  and t01_sugup_seq    = '".$newSeq
							 . "'";
					}

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}else{
					//echo $_POST["mDate_".$mDay."_".$checkIndex].' / '.$_POST["mFmTime_".$mDay."_".$checkIndex].' / '.$mUse.'/'.$mDuplicate.'/'.$mSugupja.'/'.$mTrans.'/'.$mStatus.'<br>';
				}
			}else{
				break;
			}

			$checkIndex++;
		}
	}

	if (!f_voucher_usetime($conn, $_POST['code'], $jumin, $_POST['year'], $_POST['month'])){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	$conn->commit();

	include_once("../inc/_db_close.php");

	if ($debug_mdoe == 1){
		if ($workType == 'modify'){
			echo "<script>";
			//echo "opener.location.replace('../work/month_conf_sugupja.php?curMcode=".$_POST["mCode"]."&curMkind=".$_POST["mKind"]."&curYear=".$_POST['calYear']."&curMonth=".$_POST['calMonth']."&curSugupja=".$ed->en($_POST["mJuminNo"])."');";
			echo "opener.location.replace('../work/result_detail.php?mode=2&code=".$_POST["code"]."&kind=".$_POST["kind"]."&year=".$_POST['calYear']."&month=".$_POST['calMonth']."&jumin=".$ed->en($jumin)."');";
			echo "location.replace('su_modify.php?mCode=".$_POST["code"]."&mKind=".$_POST["kind"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."&mSugupja=".$ed->en($jumin)."');";
			echo "</script>";
		}else if ($workType == 'dayModify'){
			echo "<script>";
			//echo "opener.location.replace('../work/day_conf.php?mType=DAY&mCode=".$_POST['mCode']."&mKind=".$_POST['mKind']."&mYear=".$_POST['calYear']."&mMonth=".$_POST['calMonth']."&mDay=".$_POST['calDay']."');";
			echo "opener.location.replace('../work/result_detail.php?mode=1&code=".$_POST['code']."&kind=".$_POST['kind']."&year=".$_POST['calYear']."&month=".$_POST['calMonth']."&day=".$_POST['calDay']."');";
			echo "location.replace('su_modify.php?mType=DAY&mCode=".$_POST["code"]."&mKind=".$_POST["kind"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."&calDay=".$_POST['calDay']."&mKey=".$_POST["key"]."&mSugupja=".$ed->en($jumin)."');";
			echo "</script>";
		}else{
			echo "<script>location.replace('iljung_reg.php?mCode=".$_POST["code"]."&mKind=".$_POST["kind"]."&mKey=".$_POST["key"]."&calYear=".$_POST['calYear']."&calMonth=".$_POST['calMonth']."');</script>";
		}
	}
?>
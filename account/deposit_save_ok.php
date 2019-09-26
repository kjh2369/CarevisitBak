<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mKey   = $_POST['mKey'];
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);
	$mPayDate  = $_POST['mPayDate'];
	$mBoninYul = $_POST['mBoninYul'];
	$mType    = $_POST['depositType'];
	$mAmount  = $_POST['amt'];
	$mDeposit = ceil(str_replace(',', '', $_POST['deposit']));
	$mDate    = date('Ymd', mkTime());
	$rowCount = sizeOf($mPayDate);

	$conn->auto_commit_unset();

	$mSeq  = 0;
	$mSeq2 = 0;

	if ($mType != '89'){
		# 입금내역 저장(선납입금은 여기서 저장하지 않는다.)
		$sql = "insert into t14deposit ("
			 . "  t14_ccode"
			 . ", t14_mkind"
			 . ", t14_jumin"
			 . ", t14_pay_date"
			 . ", t14_bonin_yul"
			 . ", t14_seq"
			 . ", t14_date"
			 . ", t14_time"
			 . ", t14_type"
			 . ", t14_amount) values ";

		for($i=0; $i<$rowCount; $i++){
			if ($tempPayDate != $mPayDate[$i].'_'.$mBoninYul[$i]){
				$tempPayDate  = $mPayDate[$i].'_'.$mBoninYul[$i];
				$sl1 = "select ifnull(max(t14_seq), 0)"
					 . "  from t14deposit"
					 . " where t14_ccode = '".$mCode
					 . "'  and t14_mkind = '".$mKind
					 . "'  and t14_jumin = '".$mJumin
					 . "'  and t14_pay_date  = '".$mPayDate[$i]
					 . "'  and t14_bonin_yul = '".$mBoninYul[$i]
					 . "'";
				$mSeq = $conn->get_data($sl1);
				$mSeq = $mSeq + 1;
			}else{
				$mSeq ++;
			}

			$mTime = date('His', mkTime());
			
			if ($mAmount[$i] > 0){
				if ($i > 0){
					$sql .= ",";
				}
				
				$sql .= " ('".$mCode
					 .  "','".$mKind
					 .  "','".$mJumin
					 .  "','".$mPayDate[$i]
					 .  "','".$mBoninYul[$i]
					 .  "','".$mSeq
					 .  "','".$mDate
					 .  "','".$mTime
					 .  "','".$mType
					 .  "','".str_replace(',', '', $mAmount[$i])
					 .  "')";
				
				if ($mType == '81'){
					if ($mSeq2 == 0){
						$sl1 = "select ifnull(max(t14_seq), 0)"
							 . "  from t14deposit"
							 . " where t14_ccode = '".$mCode
							 . "'  and t14_mkind = '".$mKind
							 . "'  and t14_jumin = '".$mJumin
							 . "'  and t14_pay_date  = '000000'"
							 . "   and t14_bonin_yul = '0'";
						$mSeq2 = $conn->get_data($sl1);
					}
					$mSeq2 ++;
					$sql .= ",";
					$sql .= " ('".$mCode
						 .  "','".$mKind
						 .  "','".$mJumin
						 .  "','000000"
						 .  "','0"
						 .  "','".$mSeq2
						 .  "','".$mDate
						 .  "','".$mTime
						 .  "','89"
						 .  "','".(ceil(str_replace(',', '', $mAmount[$i])) * -1)
						 .  "')";
				}
			}
		}

		$conn->query($sql);
	}

	# 선납금 저장
	if ($mType == '89'){
		$mSeq = 0;
		$sql = "select ifnull(max(t14_seq), 0)"
			 . "  from t14deposit"
			 . " where t14_ccode = '".$mCode
			 . "'  and t14_mkind = '".$mKind
			 . "'  and t14_jumin = '".$mJumin
			 . "'  and t14_pay_date = '000000'"
			 . "   and t14_bonin_yul = '0'";
		$mSeq = $conn->get_data($sql);
		$mSeq ++;

		$mTime = date('His', mkTime());

		$sql = "insert into t14deposit ("
			 . "  t14_ccode"
			 . ", t14_mkind"
			 . ", t14_jumin"
			 . ", t14_pay_date"
			 . ", t14_bonin_yul"
			 . ", t14_seq"
			 . ", t14_date"
			 . ", t14_time"
			 . ", t14_type"
			 . ", t14_amount"
			 . ") values ("
			 . "  '".$mCode
			 . "','".$mKind
			 . "','".$mJumin
			 . "','000000"
			 . "','0"
			 . "','".$mSeq
			 . "','".$mDate
			 . "','".$mTime
			 . "','89"
			 . "','".$mDeposit
			 . "')";
		$conn->query($sql);
	}else if ($mType == '81'){
	}else{
		if ($rowCount > 0){
			$mSeq = 0;
			if ($mDeposit > 0){
				if ($mSeq == 0){
					$sql = "select ifnull(max(t14_seq), 0)"
						 . "  from t14deposit"
						 . " where t14_ccode = '".$mCode
						 . "'  and t14_mkind = '".$mKind
						 . "'  and t14_jumin = '".$mJumin
						 . "'  and t14_pay_date = '000000'"
						 . "   and t14_bonin_yul = '0'";
					$mSeq = $conn->get_data($sql);
				}
				$mSeq ++;

				$mTime = date('His', mkTime());

				$sql = "insert into t14deposit ("
					 . "  t14_ccode"
					 . ", t14_mkind"
					 . ", t14_jumin"
					 . ", t14_pay_date"
					 . ", t14_bonin_yul"
					 . ", t14_seq"
					 . ", t14_date"
					 . ", t14_time"
					 . ", t14_type"
					 . ", t14_amount"
					 . ") values ("
					 . "  '".$mCode
					 . "','".$mKind
					 . "','".$mJumin
					 . "','000000"
					 . "','0"
					 . "','".$mSeq
					 . "','".$mDate
					 . "','".$mTime
					 . "','89"
					 . "','".$mDeposit
					 . "')";
				$conn->query($sql);
			}
		}
	}

	if ($mType != '89'){
		# 입금총금액 수정(선납입금은 수정하지 않는다.)
		$sql = "update t13sugupja"
			 . "   set t13_misu_inamt = (select ifnull(sum(t14_amount), 0)"
			 . "                           from t14deposit"
			 . "                          where t14_ccode = t13_ccode"
			 . "                            and t14_mkind = t13_mkind"
			 . "                            and t14_jumin = t13_jumin"
			 . "                            and t14_pay_date  = t13_pay_date"
			 . "                            and t14_bonin_yul = t13_bonin_yul)"
			 . " where t13_ccode = '".$mCode
			 . "'  and t13_mkind = '".$mKind
			 . "'  and t13_jumin = '".$mJumin
			 . "'  and t13_type  = '2'";
		$conn->query($sql);
	}

	include('../inc/_db_close.php');

	//echo "<script>opener.getNotAccountList('".$mCode."','".$mKind."',opener.document.f.mYear.value,opener.document.f.mMonth.value,opener.document.f.mSugup.value); window.close();</script>";
	echo "<script>opener.getNotAccountList('".$mCode."','".$mKind."'); window.close();</script>";
?>
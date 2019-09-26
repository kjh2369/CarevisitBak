<?
	function getDateStyle($date, $val = '-'){
		if (strLen($date) == 8){
			$date = substr($date,0,4).$val.substr($date,4,2).$val.substr($date,6,2);
		}else{
			$date = "";
		}
		return $date;
	}

	function getPhoneStyle($phone){
		$phone = Trim($phone);
		$phone = str_replace("-","",$phone);
		$phone = str_replace(")","",$phone);
		$phone = str_replace(".","",$phone);

		if (substr($phone, 0, 2) == "02"){
			$phone_1 = substr($phone,0,2);
			$phone   = substr($phone,2,strLen($phone));
			$phone_3 = substr($phone,strLen($phone)-4,4);
			$phone   = substr($phone,0,strLen($phone)-4);
			$phone_2 = $phone;
		}else{
			$phone_1 = substr($phone,0,3);
			$phone   = substr($phone,3,strLen($phone));
			$phone_3 = substr($phone,strLen($phone)-4,4);
			$phone   = substr($phone,0,strLen($phone)-4);
			$phone_2 = $phone;
		}

		if ($phone_1 == "02" or
			$phone_1 == "051" or
			$phone_1 == "053" or
			$phone_1 == "032" or
			$phone_1 == "062" or
			$phone_1 == "042" or
			$phone_1 == "052" or
			$phone_1 == "031" or
			$phone_1 == "033" or
			$phone_1 == "043" or
			$phone_1 == "041" or
			$phone_1 == "063" or
			$phone_1 == "061" or
			$phone_1 == "054" or
			$phone_1 == "055" or
			$phone_1 == "064"){
			$temp_phone_no = $phone_1."-".$phone_2."-".$phone_3;
			$temp_phone_no = str_replace("--","",$temp_phone_no);
		}else{
			$temp_phone_no = $phone_1."-".$phone_2."-".$phone_3;
			$temp_phone_no = str_replace("--","",$temp_phone_no);
		}
		return $temp_phone_no;
	}

	function getBizStyle($biz){
		if (strLen($biz) == 10){
			$biz = substr($biz,0,3)."-".substr($biz,3,2)."-".substr($biz,5,5);
		}else{
			$biz = "";
		}
		return $biz;
	}

	function getPostNoStyle($post){
		if (strLen($post) == 6){
			$post = substr($post,0,3)."-".substr($post,3,3);
		}else{
			$post = "";
		}
		return $post;
	}

	function getLastYMD($pYear, $pMonth, $val = '.'){
		$tempDate = mkTime(0, 0, 1, $pMonth, 1, $pYear);
		$tempLast = date('t', $tempDate);
		$tempLast = (ceil($tempLast) < 10 ? '0' : '').$tempLast;
		$tempYMD  = $pYear.$val.$pMonth.$val.$tempLast;

		return $tempYMD;
	}

	function getKindName($curMkind){
		switch($curMkind){
			case "0":
				$curMkindText = "재가요양기관";
				break;
			case "1":
				$curMkindText = "가사간병(바우처)";
				break;
			case "2":
				$curMkindText = "노인돌봄(바우처)";
				break;
			case "3":
				$curMkindText = "산모신생아(바우처)";
				break;
			case "4":
				$curMkindText = "장애인 활동보조(바우처)";
				break;
		}
		return $curMkindText;
	}

	// 주민번호로 생년월일을 판단한다.
	function getBirthDay($juminNo){
		$value = Trim($juminNo);
		$gubun = substr($value, 6, 1);
		$value = substr($value, 0, 2)."-".substr($value, 2, 2)."-".substr($value, 4, 2);

		switch($gubun){
			case "1":
				$value = "19".$value;
				break;
			case "2":
				$value = "19".$value;
				break;
			case "9":
				$value = "18".$value;
				break;
			case "0":
				$value = "18".$value;
				break;
			default:
				$value = "20".$value;
		}
		return $value;
	}

	// 주민번호로 성별을 판단한다.
	function getGender($juminNo){
		if (strlen(str_replace("-", "", $juminNo)) != 13){
			return '';
		}

		if (strlen($juminNo) < 7){
			return '';
		}

		$gender = substr($juminNo,6,1);

		if ($gender % 2 == 1){
			return '남';
		}else{
			return '여';
		}
	}

	/*
	 * 랜덤 문자열 생성(인수 : 길이, 타입)
	 * 지정된 타입의 문자열로 지정된 길이의 랜덤 문자열을 반환한다.
	 * 타입 0 : 영문 대소문자(A-Z,a-z), 숫자(0-9)
	 * 타입 1 : 영문 대문자(A-Z), 숫자(0-9)
	 * 타입 2 : 영문 소문자(a-z), 숫자(0-9)
	 * 타입 3 : 영문 대문자(A-Z)
	 * 타입 4 : 영문 소문자(a-z)
	 * 타입 5 : 숫자(0-9)
	 * 디폴트 : false 반환.
	*/
	function randStr($length, $type){
		switch($type){
			case 0:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
				break;
			case 1:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
				break;
			case 2:
				$chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
				break;
			case 3:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 4:
				$chars = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case 5:
				$chars = '1234567890';
				break;
			default:
				return false;
		}
		$chars_length = (strlen($chars) - 1);
		$string = '';
		for ($i = 0; $i < $length; $i = strlen($string)){
			$string .= $chars{rand(0, $chars_length)};
		}
		return $string;
	}

	/*
	 * 메일을 보낸다.
	 * $to_name = 받을 사람 이름
	 * $to_mail = 받을 사람 메일 주소
	 * $ok_key  = 인증키
	*/
	function sendMail($to_name, $to_mail, $ok_key){
		error_reporting(E_ALL); // 에러 검증 모드

		$charset         = 'UTF-8'; // 문자셋 : UTF-8
		$subject         = 'GITest 인증번호입니다.'; // 제목
		$toName          = $to_name; // 받는이 이름
		$toEmail         = $to_mail; // 받는이 이메일주소
		$fromName        = '관리자'; // 보내는이 이름
		$fromEmail       = 'webmaster@test.carevisit.net'; // 보내는이 이메일주소
		$body            = '<body>'.
						   '<p style="font:normal normal 13px/1.2 돋음; color: #000000;  text-align: center;">'.
						   '인증키를 확인하여 주십시오.<br>'.$ok_key.
						   '</p>'.
						   '</body>'; // 메일내용
		$encoded_subject = "=?".$charset."?B?".base64_encode($subject)."?=\n"; // 인코딩된 제목
		$to              = "\"=?".$charset."?B?".base64_encode($toName)."?=\" <".$toEmail.">" ; // 인코딩된 받는이
		$from            = "\"=?".$charset."?B?".base64_encode($fromName)."?=\" <".$fromEmail.">" ; // 인코딩된 보내는이
		$headers         = "MIME-Version: 1.0\n".
						   "Content-Type: text/html; charset=".$charset."; format=flowed\n".
						   "To: ". $to ."\n".
						   "From: ".$from."\n".
						   "Return-Path: ".$from."\n".
						   "Content-Transfer-Encoding: 8bit\n"; // 헤더 설정
		$mail = mail ( $to , $encoded_subject , $body , $headers ); // 메일 보내기

		if($mail){
			return $toEmail;
		}else{
			return "";
		}
	}

	/*
	 *	바코드 디지트 코드를 생성한다.
	 */
	function getBarcodeCheckdigit($as_barcode){
		$barcode = trim($as_barcode);
		$result  = $barcode;

		if(strlen($barcode) == 12){
			$i1  = intval(substr($barcode,0, 1));
			$i2  = intval(substr($barcode,1, 1));
			$i3  = intval(substr($barcode,2, 1));
			$i4  = intval(substr($barcode,3, 1));
			$i5  = intval(substr($barcode,4, 1));
			$i6  = intval(substr($barcode,5, 1));
			$i7  = intval(substr($barcode,6, 1));
			$i8  = intval(substr($barcode,7, 1));
			$i9  = intval(substr($barcode,8, 1));
			$i10 = intval(substr($barcode,9, 1));
			$i11 = intval(substr($barcode,10,1));
			$i12 = intval(substr($barcode,11,1));

			$ia = $i2 + $i4 + $i6 + $i8 + $i10 + $i12;
			$ia = $ia * 3;
			$ib = $i1 + $i3 + $i5 + $i7 + $i9 + $i11;
			$ic = $ia + $ib;
			$ic = 10 - ($ic % 10);

			if($ic == 10) $ic = 0;

			$result = $barcode.$ic;
		}else if(strlen($barcode) == 7){
			$i7 = intval(substr($barcode,0,1));
			$i6 = intval(substr($barcode,1,1));
			$i5 = intval(substr($barcode,2,1));
			$i4 = intval(substr($barcode,3,1));
			$i3 = intval(substr($barcode,4,1));
			$i2 = intval(substr($barcode,5,1));
			$i1 = intval(substr($barcode,6,1));

			$ia = $i1 + $i3 + $i5 + $i7;
			$ia = $ia * 3;
			$ib = $i2 + $i4 + $i6;
			$ic = $ia + $ib;
			$ic = 10 - ($ic % 10);

			if($ic == 10) $ic = 0;

			$result = $barcode.$ic;
		}
		return $result;
	}

	// 절사
	function cutOff($val){
		$val = floor($val);
		return $val - ($val % 10);
	}

	function GetSugaName($pConn, $pCode, $pSugaCode, $pDate = ''){
		$sql = "select m01_suga_cont"
			 . "  from m01suga"
			 . " where m01_mcode  = '".$pCode
			 . "'  and m01_mcode2 = '".$pSugaCode
			 . "'";

		if ($pDate == ''){
			$sql .= " and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
		}else{
			$sql .= " and '".str_replace('-', '', $pDate)."' between m01_sdate and m01_edate";
		}

		$pConn->query($sql);
		$row2 = $pConn->fetch();
		$SugaName = $row2['m01_suga_cont'];
		$pConn->row_free();

		if ($SugaName == ''){
			$sql = "select m11_suga_cont"
				 . "  from m11suga"
				 . " where m11_mcode  = '".$pCode
				 . "'  and m11_mcode2 = '".$pSugaCode
				 . "'";

			if ($pDate == ''){
				$sql .= " and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
			}else{
				$sql .= " and '".str_replace('-', '', $pDate)."' between m11_sdate and m11_edate";
			}

			$pConn->query($sql);
			$row2 = $pConn->fetch();
			$SugaName = $row2['m11_suga_cont'];
			$pConn->row_free();
		}

		return $SugaName;
	}

	function GetSugaValue($pConn, $pCode, $pSugaCode, $pDate = ''){
		$sql = "select m01_suga_value"
			 . "  from m01suga"
			 . " where m01_mcode  = '".$pCode
			 . "'  and m01_mcode2 = '".$pSugaCode
			 . "'";

		if ($pDate == ''){
			$sql .= " and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
		}else{
			$sql .= " and '".str_replace('-', '', $pDate)."' between m01_sdate and m01_edate";
		}

		$pConn->query($sql);
		$row2 = $pConn->fetch();
		$SugaName = $row2['m01_suga_value'];
		$pConn->row_free();

		if ($SugaName == ''){
			$sql = "select m11_suga_value"
				 . "  from m11suga"
				 . " where m11_mcode  = '".$pCode
				 . "'  and m11_mcode2 = '".$pSugaCode
				 . "'";

			if ($pDate == ''){
				$sql .= " and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
			}else{
				$sql .= " and '".str_replace('-', '', $pDate)."' between m11_sdate and m11_edate";
			}

			$pConn->query($sql);
			$row2 = $pConn->fetch();
			$SugaName = $row2['m11_suga_value'];
			$pConn->row_free();
		}

		return $SugaName;
	}

	// 주민번호
	function getSSNStyle($pSSNNo){
		$ssnNo = $pSSNNo;
		$ssnNo = subStr($ssnNo,0,6).'-'.subStr($ssnNo,6,1).'******';
		return $ssnNo;
	}

	function getMonthSugup($PARAM){
		$MaxAmount  = $PARAM['maxAmount'];
		$maxTempAmt = 0;
		$maxTempPrc = 0;

		$amtSugub['m200'] = 0;
		$amtSugub['m500'] = 0;
		$amtSugub['m800'] = 0;
		$amtSugub['mTot'] = 0;

		$amtBiPay['m200'] = 0;
		$amtBiPay['m500'] = 0;
		$amtBiPay['m800'] = 0;
		$amtBiPay['mTot'] = 0;

		$amtBonin['m200'] = 0;
		$amtBonin['m500'] = 0;
		$amtBonin['m800'] = 0;
		$amtBonin['mTot'] = 0;

		$amtOver['m200'] = 0;
		$amtOver['m500'] = 0;
		$amtOver['m800'] = 0;
		$amtOver['mTot'] = 0;

		$mLastDay = $PARAM['mLastDay'];

		for($mDay=1; $mDay<=$mLastDay; $mDay++){
			$checkLoop = true;
			$mIndex = 1;

			while($checkLoop){
				$mUse       = $PARAM['mUse_'.$mDay.'_'.$mIndex];
				$mDuplicate = $PARAM['mDuplicate_'.$mDay.'_'.$mIndex];

				if (!isSet($mUse)){
					$checkLoop = false;
				}

				if (!isSet($mDuplicate)){
					$checkLoop = false;
				}

				if ($checkLoop){
					$mSvcSubCode = $PARAM['mSvcSubCode_'.$mDay.'_'.$mIndex];

					if ($PARAM['mBiPayUmu_'.$mDay.'_'.$mIndex] == 'Y'){
						$amtBiPay['m'.$mSvcSubCode] += $PARAM['mTValue_'.$mDay.'_'.$mIndex];
					}else{
						$maxTempAmt = $amtSugub['m200'] + $amtSugub['m500'] + $amtSugub['m800'] + $PARAM['mTValue_'.$mDay.'_'.$mIndex];
						if ($MaxAmount > $maxTempAmt){
							$amtSugub['m'.$mSvcSubCode] += $PARAM['mTValue_'.$mDay.'_'.$mIndex];
						}else{
							if ($MaxAmount >= $amtSugub['m200'] + $amtSugub['m500'] + $amtSugub['m800']){
								$maxTempPrc = ($MaxAmount - ($amtSugub['m200'] + $amtSugub['m500'] + $amtSugub['m800']));
								$amtSugub['m'.$mSvcSubCode] += $maxTempPrc;
							}

							$amtOver['m'.$mSvcSubCode] += ($PARAM['mTValue_'.$mDay.'_'.$mIndex] - $maxTempPrc);
						}
					}

					$amtBonin['m'.$mSvcSubCode] += cutOff(ceil($PARAM['mTValue_'.$mDay.'_'.$mIndex] * $PARAM['boninYul'] / 100));
				}

				$mIndex++;
			}
		}

		$amtSugub['mTot'] = $amtSugub['m200'] + $amtSugub['m500'] + $amtSugub['m800'];
		$amtBiPay['mTot'] = $amtBiPay['m200'] + $amtBiPay['m500'] + $amtBiPay['m800'];
		$amtBonin['mTot'] = $amtBonin['m200'] + $amtBonin['m500'] + $amtBonin['m800'];
		$amtOver['mTot']  = $amtOver['m200']  + $amtOver['m500']  + $amtOver['m800'];
	}

	// 시간별 수가를 조회한다.
	function getSugaTimeValue($conn, $mCode, $svcKind, $mSvcCode, $mSugaCode, $mTime, $mDT = '', $client = ''){
		if ($svcKind == '0'){
			################################################################
			#
			# 방문재가 수가
			#
			################################################################
			$sql = "select m01_mcode2, m01_suga_cont, m01_suga_value"
				 . "  from m01suga"
				 . " where m01_mcode = '".$mCode
				 . "'";

			if ($mSvcCode == '200'){
				$sql .= "  and left(m01_mcode2, 4) = '".$mSugaCode
					 .  "' and m01_calc_time = '".$mTime
					 .  "'";
			}else{
				$sql .= "  and m01_mcode2 = '".$mSugaCode
					 .  "'";
			}

			if ($mDT == ''){
				$sql .= " and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
			}else{
				$sql .= " and '".$mDT."' between left(m01_sdate, ".strlen($mDT).") and left(m01_edate, ".strlen($mDT).")";
			}

			$conn->query($sql);
			$row = $conn->fetch();
			$sugaValue = $row[0].'//'.$row[1].'//'.$row[2];
			$conn->row_free();

			if (str_replace('//','',$sugaValue) == ''){
				$sql = "select m11_mcode2, m11_suga_cont, m11_suga_value"
					 . "  from m11suga"
					 . " where m11_mcode = '".$mCode
					 . "'";
				if ($mSvcCode == '200'){
					$sql .= "  and left(m11_mcode2, 4) = '".$mSugaCode
						 .  "' and m11_calc_time = '".$mTime
						 .  "'";
				}else{
					$sql .= "  and m11_mcode2 = '".$mSugaCode
						 .  "'";
				}
				//$sql .= "'  and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
				if ($mDT == ''){
					$sql .= " and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
				}else{
					$sql .= " and '".$mDT."' between left(m11_sdate, ".strlen($mDT).") and left(m11_edate, ".strlen($mDT).")";
				}

				$conn->query($sql);
				$row = $conn->fetch();
				$sugaValue = $row[0].'//'.$row[1].'//'.$row[2];
				$conn->row_free();
			}
		}else{
			################################################################
			#
			# 바우처 및 기타유료
			#
			################################################################
			$sql = "select service_code, service_gbn, service_cost
					  from suga_service
					 where org_no       = '$mCode'
					   and service_code = '$mSugaCode'";

			if ($mDT == ''){
				$sql .= " and date_format(now(), '%Y-%m-%d') between service_from_dt and service_to_dt";
			}else{
				$sql .= " and '".$mDT."' between left(replace(service_from_dt, '-', ''), ".strlen($mDT).") and left(replace(service_to_dt, '-', ''), ".strlen($mDT).")";
			}

			$row  = $conn->get_array($sql);
			$cost = $row[2];
			$sugaValue = $row[0].'//'.$row[1].'//';

			if ($mSvcCode > '30' && $mSvcCode < '40'){
				$kind = $conn->kind_code($conn->kind_list($mCode, true), $mSvcCode);
				$sql = "select m03_kupyeo_1
						  from m03sugupja
						 where m03_ccode = '$mCode'
						   and m03_mkind = '$kind'
						   and m03_jumin = '$client'";

				$cost = $conn->get_data($sql);
			}

			$sugaValue .= $cost;
		}

		return $sugaValue;
	}

	// 수가 할증 비율
	function getSugaRateValue($conn, $mCode, $mSugaCode){
		$sql = "select m01_rate"
			 . "  from m01suga"
			 . " where m01_mcode   = '".$mCode
			 . "'  and m01_mcode2 = '".$mSugaCode
			 . "'  and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
		$conn->query($sql);
		$row = $conn->fetch();
		$sugaValue = $row['m01_rate'];
		$conn->row_free();

		if ($sugaValue == ''){
			$sql = "select m11_rate"
				 . "  from m11suga"
				 . " where m11_mcode  = '".$mCode
				 . "'  and m11_mcode2 = '".$mSugaCode
				 . "'  and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
			$conn->query($sql);
			$row = $conn->fetch();
			$sugaValue = $row['m11_rate'];
			$conn->row_free();
		}

		return $sugaValue;
	}

	// 휴일 확인
	function getHoliday($conn, $pDate){
		$weekDay = date("w", strtotime(getDateStyle($pDate)));

		//if ($weekDay == 6 or $weekDay == 0){
		// 토요일은 펴일 처리한다.
		if ($weekDay == 0){
			$holiday = 'Y';
		}else{
			$sql = "select count(*)"
				 . "  from tbl_holiday"
				 . " where mdate = '".$pDate
				 . "'";
			$holiday = $conn->get_data($sql);

			if ($holiday > 0){
				$holiday = 'Y';
			}else{
				$holiday = 'N';
			}
		}

		return $holiday;
	}

	// 선납금액조회
	function getDepositAmount($conn, $pCode, $pKind, $pKey){
		$mTYpe  = '89';
		$mJumin = $conn->get_sugupja_jumin($pCode, $pKind, $pKey);

		$sql = "select sum(t14_amount) as t14_amount"
			 . "  from t14deposit"
			 . " where t14_ccode = '".$pCode
			 . "'  and t14_mkind = '".$pKind
			 . "'  and t14_jumin = '".$mJumin
			 . "'  and t14_pay_date  = '000000'"
			 . "   and t14_bonin_yul = '0'"
			 . "   and t14_type      = '89'";
		$amount = $conn->get_data($sql);

		return $amount;
	}

	// 명세서 발급일 저장
	function setPaymentIssu($conn, $pCode, $pKind, $pJumin, $pDate, $pBoninYul, $pBillNo, $pIssuDate){
		$sql = "select count(*)"
			 . "  from t15paymentissu"
			 . " where t15_ccode    = '".$pCode
			 . "'  and t15_mkind    = '".$pKind
			 . "'  and t15_jumin    = '".$pJumin
			 . "'  and t15_pay_date = '".$pDate
			 . "'  and t15_billno   = '".$pBillNo
			 . "'";
		$count = $conn->get_data($sql);

		if ($count == 0){
			$sql = "insert into t15paymentissu ("
				 . "  t15_ccode"
				 . ", t15_mkind"
				 . ", t15_jumin"
				 . ", t15_pay_date"
				 . ", t15_boninyul"
				 . ", t15_billno"
				 . ", t15_first"
				 . ", t15_date"
				 . ") values ("
				 . "  '".$pCode
				 . "','".$pKind
				 . "','".$pJumin
				 . "','".$pDate
				 . "','".$pBoninYul
				 . "','".$pBillNo
				 . "','".$pIssuDate
				 . "','".$pIssuDate
				 . "')";
			$conn->execute($sql);
		}else{
			/*
			$sql = "update t15paymentissu"
				 . "   set t15_date = '".$pIssuDate
				 . "'"
				 . " where t15_ccode    = '".$pCode
				 . "'  and t15_mkind    = '".$pKind
				 . "'  and t15_jumin    = '".$pJumin
				 . "'  and t15_pay_date = '".$pDate
				 . "'  and t15_billno   = '".$pBillNo
				 . "'";
			*/
		}
	}

	// 입급구분
	function getDepositList(){
		$deposit[0][0] = '01';
		$deposit[0][1] = '현금';
		$deposit[1][0] = '02';
		$deposit[1][1] = '계좌이체';
		$deposit[2][0] = '03';
		$deposit[2][1] = '지로';
		$deposit[3][0] = '04';
		$deposit[3][1] = '카드';
		$deposit[4][0] = '81';
		$deposit[4][1] = '선납';
		$deposit[5][0] = '89';
		$deposit[5][1] = '선납입금';
		$deposit[6][0] = '99';
		$deposit[6][1] = '결손';

		return $deposit;
	}

	// 입금구분
	function getDepositGbn($gbn){
		switch($gbn){
			case '01':
				return '현금';
				break;
			case '02':
				return '계좌이체';
				break;
			case '03':
				return '지로';
				break;
			case '04':
				return '카드';
				break;
			case '81':
				return '선납';
				break;
			case '89':
				return '선납입금';
				break;
			case '99':
				return '결손';
				break;
		}
	}

	// 일정년도
	function getInIljungYear($conn, $mCode){
		$yearValue = $conn->get_iljung_year($mCode);

		$requestString = '';

		for($i=$yearValue[0]; $i<=$yearValue[1]; $i++){
			$requestString .= $i.'//'.$i.';;';
		}

		return $requestString;
	}

	// 전월
	function getPMonth(){
		$tempYear = date("Y", mkTime());
		$tempMonth = date("m", mkTime());

		$tempYear = ($tempMonth == 1) ? ($tempYear - 1) : $tempYear;
		$tempMonth = ($tempMonth == 1) ? 12 : ($tempMonth - 1);

		return $tempYear.$tempMonth;
	}

	// 한글 왼쪽에서 자릿수만큼 자른 후 ...을 붙인다.
	function left($text, $length){
		if (mb_strlen($text,"UTF-8") > $length){
			$value = mb_substr($text, 0, $length,"UTF-8")."...";
		}else{
			$value = $text;
		}
		return $value;
	}

	// 한글 왼쪽에서 자릿수만큼 자른다.
	function str_left($text, $length){
		if (mb_strlen($text,"UTF-8") > $length){
			$value = mb_substr($text, 0, $length,"UTF-8");
		}else{
			$value = $text;
		}
		return $value;
	}

	// 한글 시작위치에서 종료위치까지 자른다.
	function str_mid($text, $start, $end){
		return mb_substr($text, $start, $end,"UTF-8");
	}

	// 한글의 길이를 구한다.
	function str_len($text){
		return mb_strlen($text,"UTF-8");
	}

	function imagecreatefrombmp($filename)
	{
		$f = fopen($filename, "rb");

		//read header
		$header = fread($f, 54);
		$header = unpack(	'c2identifier/Vfile_size/Vreserved/Vbitmap_data/Vheader_size/' .
							'Vwidth/Vheight/vplanes/vbits_per_pixel/Vcompression/Vdata_size/'.
							'Vh_resolution/Vv_resolution/Vcolors/Vimportant_colors', $header);

		if ($header['identifier1'] != 66 or $header['identifier2'] != 77)
		{
			die('Not a valid bmp file');
		}

		if ($header['bits_per_pixel'] != 24)
		{
			die('Only 24bit BMP images are supported');
		}

		$wid2 = ceil((3*$header['width']) / 4) * 4;

		$wid = $header['width'];
		$hei = $header['height'];

		$img = imagecreatetruecolor($header['width'], $header['height']);

		//read pixels
		for ($y=$hei-1; $y>=0; $y--)
		{
			$row = fread($f, $wid2);
			$pixels = str_split($row, 3);
			for ($x=0; $x<$wid; $x++)
			{
				imagesetpixel($img, $x, $y, dwordize($pixels[$x]));
			}
		}
		fclose($f);

		return $img;
	}

	function dwordize($str)
	{
		$a = ord($str[0]);
		$b = ord($str[1]);
		$c = ord($str[2]);
		return $c*256*256 + $b*256 + $a;
	}

	function byte3($n)
	{
		return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255);
	}
	function dword($n)
	{
		return pack("V", $n);
	}
	function word($n)
	{
		return pack("v", $n);
	}

	function imageBmp(&$img, $filename = false)
	{
		$wid = imagesx($img);
		$hei = imagesy($img);
		$wid_pad = str_pad('', $wid % 4, "\0");

		$size = 54 + ($wid + $wid_pad) * $hei * 3; //fixed

		//prepare & save header
		$header['identifier']		= 'BM';
		$header['file_size']		= dword($size);
		$header['reserved']			= dword(0);
		$header['bitmap_data']		= dword(54);
		$header['header_size']		= dword(40);
		$header['width']			= dword($wid);
		$header['height']			= dword($hei);
		$header['planes']			= word(1);
		$header['bits_per_pixel']	= word(24);
		$header['compression']		= dword(0);
		$header['data_size']		= dword(0);
		$header['h_resolution']		= dword(0);
		$header['v_resolution']		= dword(0);
		$header['colors']			= dword(0);
		$header['important_colors']	= dword(0);

		if ($filename)
		{
			$f = fopen($filename, "wb");
			foreach ($header AS $h)
			{
				fwrite($f, $h);
			}

			//save pixels
			for ($y=$hei-1; $y>=0; $y--)
			{
				for ($x=0; $x<$wid; $x++)
				{
					$rgb = imagecolorat($img, $x, $y);
					fwrite($f, byte3($rgb));
				}
				fwrite($f, $wid_pad);
			}
			fclose($f);
		}
		else
		{
			foreach ($header AS $h)
			{
				echo $h;
			}

			//save pixels
			for ($y=$hei-1; $y>=0; $y--)
			{
				for ($x=0; $x<$wid; $x++)
				{
					$rgb = imagecolorat($img, $x, $y);
					echo byte3($rgb);
				}
				echo $wid_pad;
			}
		}
	}

	//메일보내기
	function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="") 
	{
	  
		ini_set("SMTP", "115.68.110.24"); // SMTP 서버 IP를 입력합니다. (다른 서버를 이용할 수도 있습니다.)
		ini_set("sendmail_from", "admin@carevisit.co.kr"); // 강제로 php.ini -> smtp의 sendmail_from 설정 

	  
		$fname   = "=?utf-8?B?" . base64_encode($fname) . "?=";
		$subject = "=?utf-8?B?" . base64_encode($subject) . "?=";
		//$g4[charset] = ($g4[charset] != "") ? "charset=$g4[charset]" : "";

		$header  = "Return-Path: <$fmail>\n";
		$header .= "From: $fname <$fmail>\n";
		$header .= "Reply-To: <$fmail>\n";
		if ($cc)  $header .= "Cc: $cc\n";
		if ($bcc) $header .= "Bcc: $bcc\n";
		$header .= "MIME-Version: 1.0\n";
		//$header .= "X-Mailer: SIR Mailer 0.91 (sir.co.kr) : $_SERVER[SERVER_ADDR] : $_SERVER[REMOTE_ADDR] : $g4[url] : $_SERVER[PHP_SELF] : $_SERVER[HTTP_REFERER] \n";
		// UTF-8 관련 수정
		$header .= "X-Mailer: SIR Mailer 0.92 (sir.co.kr) : $_SERVER[SERVER_ADDR] : $_SERVER[REMOTE_ADDR] : $_SERVER[PHP_SELF] : $_SERVER[HTTP_REFERER] \n";

		if ($file != "") {
			$boundary = uniqid("http://sir.co.kr/");

			$header .= "Content-type: MULTIPART/MIXED; BOUNDARY=\"$boundary\"\n\n";
			$header .= "--$boundary\n";
		}

		if ($type) {
			$header .= "Content-Type: TEXT/HTML; charset=utf-8\n";
			if ($type == 2)
				$content = nl2br($content);
		} else {
			$header .= "Content-Type: TEXT/PLAIN; charset=utf-8\n";
			$content = stripslashes($content);
		}
		$header .= "Content-Transfer-Encoding: BASE64\n\n";
		$header .= chunk_split(base64_encode($content)) . "\n";

		if ($file != "") {
			foreach ($file as $f) {
				$header .= "\n--$boundary\n";
				$header .= "Content-Type: APPLICATION/OCTET-STREAM; name=\"$f[name]\"\n";
				$header .= "Content-Transfer-Encoding: BASE64\n";
				$header .= "Content-Disposition: inline; filename=\"$f[name]\"\n";

				$header .= "\n";
				$header .= chunk_split(base64_encode($f[data]));
				$header .= "\n";
			}
			$header .= "--$boundary--\n";
		}
		
		
		@mail($to, $subject, "", $header);
	}


	//소켓통신으로 외부사이트 데이터값주고받기
	function get_fsock_date($domain, $url, $port=80, $timeout=30){
		$fp = fsockopen($domain, $port, $errno, $errstr, $timeout) or die($errstr);
		
		if ($fp){
			$out  = "GET $url HTTP/1.1\r\n";
			$out .= "Host: $domain\r\n";
			$out .= "Connection: Close\r\n\r\n";
		
			fwrite($fp, $out);

			$res = '';

			while(!feof($fp)){
				$res .= fgets($fp, 128);
			}
			
			fclose($fp);

			$pattern = '/HTTP\/1\.\d\s(\d+)/';

			if (preg_match($pattern, $res, $matches) && $matches[1] == 200){
				$data_arr = explode("\r\n\r\n", $res);
				$data = $data_arr[1];
				$enc = mb_detect_encoding($data, array('EUC-KR', 'UTF-8', 'CN-GB'));

				if ($enc != 'UTF-8'){
					$data = iconv($enc, 'UTF-8', $data);
				}

				return $data;
			}
		}
	}
?>
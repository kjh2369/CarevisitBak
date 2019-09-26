<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$sr		= $_POST['sr'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	parse_str($_POST['para'],$loCalendar);

	$conn->begin();

	//기존일정 삭제
	$sql = 'DELETE
			FROM	t01iljung
			WHERE	t01_ccode		= \''.$code.'\'
			AND		t01_mkind		= \''.$svcCd.'\'
			AND		t01_jumin		= \''.$jumin.'\'';

	if ($svcCd == 'S'){
	}else{
		$sql .= '
			AND		t01_status_gbn != \'1\'
			AND		t01_status_gbn != \'5\'';
	}

	$sql .= '
			AND		t01_del_yn		= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'';

	if ($svcCd == '6'){
		$sql .= ' AND t01_svc_subcd = \''.$sr.'\'';
	}

	if ($svcCd == 'S' || $svcCd == 'R'){
		$sql .= ' AND IFNULL(t01_request,\'PERSON\') = \'PERSON\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	if ($svcCd == '5'){
		//주야간보호 비급여 삭제
		$sql = 'DELETE
				FROM	dan_nonpayment_iljung
				WHERE	org_no		= \''.$code.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		LEFT(date,6)= \''.$year.$month.'\'
				AND		CONCAT(time,\'_\',seq) NOT IN (	SELECT	CONCAT(t01_sugup_fmtime,\'_\',t01_sugup_seq)
														FROM	t01iljung
														WHERE	t01_ccode		 = \''.$code.'\'
														AND		t01_mkind		 = \''.$svcCd.'\'
														AND		t01_jumin		 = \''.$jumin.'\'
														AND		t01_status_gbn	!= \'1\'
														AND		t01_status_gbn	!= \'5\'
														AND		t01_del_yn		 = \'N\'
														AND		LEFT(t01_sugup_date,6)= \''.$year.$month.'\')';
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	//순번
	$sql = 'SELECT	t01_sugup_date AS dt
			,		MAX(t01_sugup_seq) AS seq
			FROM	t01iljung
			WHERE	t01_ccode = \''.$code.'\'
			AND		t01_mkind = \''.$svcCd.'\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			GROUP	BY t01_sugup_date';

	$arrSeq = $conn->_fetch_array($sql,'dt');

	$query = 'insert into t01iljung (
			   t01_ccode			/*기관코드*/
			  ,t01_mkind			/*서비스구분*/
			  ,t01_jumin			/*주민번호*/
			  ,t01_sugup_date		/*일자*/
			  ,t01_sugup_fmtime		/*시작시간*/
			  ,t01_sugup_totime		/*종료시간*/
			  ,t01_sugup_seq		/*순번*/
			  ,t01_sugup_soyotime	/*제공시간*/
			  ,t01_sugup_yoil		/*요일*/
			  ,t01_svc_subcode		/*서비스종류*/
			  ,t01_status_gbn		/*상태*/
			  ,t01_toge_umu			/*동거여부*/
			  ,t01_bipay_umu		/*비급여여부*/

			  ,t01_yoyangsa_id1		/*실행 주요양보호사*/
			  ,t01_yoyangsa_id2		/*실행 부요양보호사*/
			  ,t01_yname1			/*요양보호사명*/
			  ,t01_yname2			/*요양보호사명*/

			  ,t01_mem_cd1			/*계획 주요양보호사*/
			  ,t01_mem_cd2			/*계획 부요양보호사*/
			  ,t01_mem_nm1			/*요양보호사명*/
			  ,t01_mem_nm2			/*요양보호사명*/

			  ,t01_suga_code1		/*수가코드*/
			  ,t01_suga				/*수가*/
			  ,t01_suga_over		/*야간할증금액*/
			  ,t01_suga_night		/*심야할증금액*/
			  ,t01_suga_tot			/*수가총액*/

			  ,t01_e_time			/*야간시간*/
			  ,t01_n_time			/*심야시간*/
			  ,t01_ysudang_yn		/*수당지급여부*/
			  ,t01_ysudang			/*수당금액*/
			  ,t01_ysudang_yul1		/*주요양보호사 요률*/
			  ,t01_ysudang_yul2		/*부요양보호사 요율*/
			  ,t01_yoyangsa_id3		/*주요양보호사 수당*/
			  ,t01_yoyangsa_id4		/*부요양보호사 수당*/
			  ,t01_car_no			/*차량번호*/
			  ,t01_conf_suga_code	/*확정수가코드*/
			  ,t01_conf_suga_value	/*확정수가*/
			  ,t01_holiday			/*휴일여부*/

			  ,t01_bipay1			/*비급여금액(공단수가)*/
			  ,t01_bipay2			/*비급여금액(기관수가)*/
			  ,t01_bipay3			/*비급여금액(개별금액)*/
			  ,t01_expense_yn		/*실비지급여부*/
			  ,t01_expense_pay		/*실비지급금액*/

			  ,t01_not_school_cnt	/*미취학아동수*/
			  ,t01_not_school_cost	/*미취학아동단가*/
			  ,t01_not_school_pay	/*미취학아동금액*/
			  ,t01_school_cnt		/*취학아동수*/
			  ,t01_school_cost		/*취학아동단가*/
			  ,t01_school_pay		/*취학아동금액*/
			  ,t01_family_cnt		/*가족수*/
			  ,t01_family_cost		/*가족단가*/
			  ,t01_family_pay		/*가족금액*/
			  ,t01_home_in_yn		/*상주여부*/
			  ,t01_home_in_cost		/*상주단가*/
			  ,t01_holiday_cost		/*휴일단가*/

			  ,t01_bipay_kind		/*비급여 단가 선택구분*/
			  ,t01_yname5			/*수당구분 R : 비율, P : 금액*/
			  ,t01_modify_pos		/*수정구분*/
			  ,t01_time_doub		/*2인여부*/

			  ,t01_svc_subcd		/*S:재가지원 R:자원연계*/
			  ,t01_request
			  ,t01_dementia_yn		/*인지활동여부*/

			  ) values (';

	/*
	0	day_cnt
	1	week
	2	svcKind
	3	from
	4	to
	5	procTime
	6	memCd1
	7	memNm1
	8	memCd2
	9	memNm2
	10	sugaCd
	11	sugaNm
	12	cost
	13	costEvening
	14	costNight
	15	costTotal
	16	sudangPay
	17	sudangKind
	18	sudangVal1
	19	sudangVal2
	20	timeEvening
	21	timeNight
	22	ynNight
	23	ynEvening
	24	ynHoliday
	25	ynBipay
	26	ynFamily
	27	extraKind
	28	bipayCost
	29	ynRealPay
	30	realPay
	31	stat
	32	seq;
	33	babyAddPay
	34	togetherYn
	*/

	foreach($loCalendar as $row){
		$val = explode(';',$row);

		$lsDt      = $year.$month.(intval($val[0]) < 10 ? '0' : '').intval($val[0]);
		$liWeekday = date('w',strtotime($lsDt));

		if ($svcCd == '6' || $svcCd == 'S' || $svcCd == 'R'){
			//재가관리
			$lsMemCd1 = $val[6];
			$lsMemCd2 = $ed->de($val[8]);
		}else{
			$lsMemCd1 = $ed->de($val[6]);
			$lsMemCd2 = $ed->de($val[8]);
		}

		//요양보호사 및 자원이없는 경우를 걸러낸다.
		if (!$lsMemCd1) continue;

		if ($val[2] != '200'){
			$ynExtra = 'Y';
		}else{
			$ynExtra = 'N';
		}

		$lsExtrGbn = 'N'; //수당구분(비율)
		$liExtrPay = $val[16];
		$liRate1   = 0;
		$liRate2   = 0;
		$liAmt1    = 0;
		$liAmt2    = 0;

		if ($val[2] == '500'){
			if ($val[17] == 'RATE'){
				$lsExtrGbn = 'RATE'; //수당구분(비율)
				$liRate1   = $val[18];
				$liRate2   = $val[19];
				$liAmt1    = 0;
				$liAmt2    = 0;
			}else if ($val[17] == 'AMT'){
				$lsExtrGbn = 'AMT'; //수당구분(금액)
				$liRate1   = 0;
				$liRate2   = 0;
				$liAmt1    = $val[18];
				$liAmt2    = $val[19];
			}else{
				$lsExtrGbn = 'PERSON'; //개별
				$liExtrPay = 0;
				$liRate1   = 0;
				$liRate2   = 0;
				$liAmt1    = $val[18];
				$liAmt2    = $val[19];
			}
		}else if ($val[2] == '800'){
			if ($val[17] == 'AMT'){
				$lsExtrGbn = 'AMT';
			}else{
				$lsExtrGbn = 'PERSON';
				$liExtrPay = 0;
			}
		}

		$liBipayCost1 = 0;
		$liBipayCost2 = 0;
		$liBipayCost3 = 0;

		if ($val[2] == '200'){
			$liBipayCost1 = $val[28];
		}else if ($val[2] == '500'){
			$liBipayCost2 = $val[28];
		}else{
			$liBipayCost3 = $val[28];
		}

		//산모신생아 추가요금
		if ($svcCd == '3' || $svcCd == 'A'){
			$laBabyAddPay = explode('/',$val[33]);
		}

		//기타유료
		//if ($svcCd == 'B' || $svcCd == 'C'){
		//	$liProcTime = $val[5] * 60;
		//}else{
			$liProcTime = $val[5];
		//}

		$arrSeq[$lsDt]['seq'] ++;
		$val[32] = $arrSeq[$lsDt]['seq'];

		//재가지원은 항상 실적등록으로 처리한다.
		if ($svcCd == 'S'){
			$val[31] = '1';
		}

		$sql = $query.
			  '\''.$code.'\'						/*기관코드*/
			  ,\''.$svcCd.'\'						/*서비스구분*/
			  ,\''.$jumin.'\'						/*주민번호*/
			  ,\''.$lsDt.'\'						/*일자*/
			  ,\''.str_replace(':','',$val[3]).'\'	/*시작시간*/
			  ,\''.str_replace(':','',$val[4]).'\'	/*종료시간*/
			  ,\''.$val[32].'\'						/*순번*/
			  ,\''.$liProcTime.'\'					/*제공시간 t01_soyotime*/
			  ,\''.$liWeekday.'\'					/*요일 t01_sugup_yoil*/
			  ,\''.$val[2].'\'						/*서비스종류 t01_svc_subcode*/
			  ,\''.$val[31].'\'						/*상태 t01_status_gbn*/
			  ,\''.$val[26].'\'						/*동거여부 t01_toge_umu*/
			  ,\''.$val[25].'\'						/*비급여여부 t01_bipay_umu*/

			  ,\''.$lsMemCd1.'\'	/*실행 주요양보호사 t01_yoyangsa_id1*/
			  ,\''.$lsMemCd2.'\'	/*실행 부요양보호사 t01_yoyangsa_id2*/
			  ,\''.$val[7].'\'		/*요양보호사명 t01_yname1*/
			  ,\''.$val[9].'\'		/*요양보호사명 t01_yname2*/

			  ,\''.$lsMemCd1.'\'	/*계획 주요양보호사 t01_mem_cd1*/
			  ,\''.$lsMemCd2.'\'	/*계획 부요양보호사 t01_mem_cd2*/
			  ,\''.$val[7].'\'		/*요양보호사명 t01_mem_nm1*/
			  ,\''.$val[9].'\'		/*요양보호사명 t01_mem_nm2*/

			  ,\''.$val[10].'\'		/*수가코드 t01_suga_code1*/
			  ,\''.$val[12].'\'		/*수가 t01_suga*/
			  ,\''.$val[13].'\'		/*야간할증금액 t01_suga_over*/
			  ,\''.$val[14].'\'		/*심야할증금액 t01_suga_night*/
			  ,\''.$val[15].'\'		/*수가총액 t01_suga_tot*/

			  ,\''.$val[20].'\'		/*야간시간 t01_e_time*/
			  ,\''.$val[21].'\'		/*심야시간 t01_n_time*/
			  ,\''.$ynExtra.'\'		/*수당지급여부 t01_ysudang_yn*/
			  ,\''.$liExtrPay.'\'	/*수당금액 t01_ysudang*/
			  ,\''.$liRate1.'\'		/*주요양보호사 요률 t01_ysudang_yul1*/
			  ,\''.$liRate2.'\'		/*부요양보호사 요율 t01_ysudang_yul2*/
			  ,\''.$liAmt1.'\'		/*주요양보호사 수당 t01_yoyangsa_id3*/
			  ,\''.$liAmt2.'\'		/*부요양보호사 수당 t01_yoyangsa_id4*/
			  ,\'\'					/*차량번호 t01_car_no*/
			  ,\''.$val[10].'\'		/*확정수가코드 t01_conf_suga_code*/
			  ,\''.$val[15].'\'		/*확정수가 t01_conf_suga_value*/
			  ,\''.$val[24].'\'		/*휴일여부 t01_holiday*/

			  ,\''.$liBipayCost1.'\'	/*비급여금액(방문요양) t01_bipay1*/
			  ,\''.$liBipayCost2.'\'	/*비급여금액(방문목욕) t01_bipay2*/
			  ,\''.$liBipayCost3.'\'	/*비급여금액(방문간호) t01_bipay3*/
			  ,\''.$val[29].'\'			/*실비지급여부 t01_expense_yn*/
			  ,\''.$val[30].'\'			/*실비지급금액 t01_expense_pay*/

			  ,\''.intval($laBabyAddPay[0]).'\'						/*미취학아동수 t01_not_school_cnt*/
			  ,\''.intval($laBabyAddPay[1]).'\'						/*미취학아동단가 t01_not_school_cost*/
			  ,\''.intval($laBabyAddPay[2]).'\'						/*미취학아동금액 t01_not_school_pay*/
			  ,\''.intval($laBabyAddPay[3]).'\'						/*취학아동수 t01_school_cnt*/
			  ,\''.intval($laBabyAddPay[4]).'\'						/*취학아동단가 t01_school_cost*/
			  ,\''.intval($laBabyAddPay[5]).'\'						/*취학아동금액 t01_school_pay*/
			  ,\''.intval($laBabyAddPay[6]).'\'						/*가족수 t01_family_cnt*/
			  ,\''.intval($laBabyAddPay[7]).'\'						/*가족단가 t01_family_cost*/
			  ,\''.intval($laBabyAddPay[8]).'\'						/*가족금액 t01_family_pay*/
			  ,\''.(intval($laBabyAddPay[9]) > 0 ? 'Y' : 'N').'\'	/*상주여부 t01_home_in_yn*/
			  ,\''.intval($laBabyAddPay[9]).'\'						/*상주단가 t01_home_in_cost*/
			  ,\''.intval($laBabyAddPay[10]).'\'					/*휴일단가 t01_holiday_cost*/

			  ,\''.$val[27].'\'		/*비급여 단가 선택구분 t01_bipay_kind*/
			  ,\''.$lsExtrGbn.'\'
			  ,\'N\'
			  ,\''.$val[34].'\'		/*2인여부 t01_time_doub*/

			  ,\''.$sr.'\'		/* t01_svc_subcd */
			  ,'.($svcCd == 'S' || $svcCd == 'R' ? '\'PERSON\'' : 'NULL').'
			  ,\''.($val[36] == 'Y' ? 'Y' : '').'\'		/*t01_dementia_yn*/

			  ) /*수정구분 t01_modify_pos*/';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		if ($svcCd == '5'){
			//주야간보호 비급여
			$nonpayment = Explode('@',$val[35]);
			$idx = 1;

			if (is_array($nonpayment)){
				foreach($nonpayment as $tmpI => $tmpR){
					if ($tmpR){
						$npmt = Explode('#',$tmpR);

						$sql = 'INSERT INTO dan_nonpayment_iljung (org_no,jumin,date,time,seq,idx,code,amt) VALUES (
								 \''.$code.'\'
								,\''.$jumin.'\'
								,\''.$lsDt.'\'
								,\''.str_replace(':','',$val[3]).'\'
								,\''.$val[32].'\'
								,\''.$idx.'\'
								,\''.$npmt[0].'\'
								,\''.$npmt[1].'\'
								)';

						$idx ++;

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 echo $sql;
							 echo 9;
							 exit;
						}
					}
				}
			}
		}
	}

	$conn->commit();


	$orgNo	= $code;
	$yymm	= $year.$month;

	include_once('./summary.php');

	echo 1;

	include_once('../inc/_db_close.php');
?>
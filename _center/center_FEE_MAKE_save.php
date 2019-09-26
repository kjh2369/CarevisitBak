<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//$company= $_POST['company'];
	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$tmpYm	= '999912';

	$loc = $_POST['loc'];

	if ($loc == 'INFO'){
		$month ++;

		if ($month > 12){
			$month = 1;
			$year ++;
		}
	}

	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $year.$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');
	$acctYm	= $myF->dateAdd('month', 1, $yymm.'01', 'Ym');
	$lastday= $myF->dateAdd('day', -1, $acctYm.'01', 'd');


	//기본금 단가표
	$sql = 'SELECT	cnt1amt, cnt2amt, cnt3amt, cnt4amt
			FROM	cv_stnd_fee_set
			WHERE	\''.$yymm.'\' BETWEEN from_ym AND to_ym';

	$row = $conn->get_array($sql);

	$careFee15 = $row['cnt1amt']; //15인이하 기본금
	$careFee30 = $row['cnt2amt']; //30인이하 기본금
	$danFee9 = $row['cnt3amt']; //주야간보호 9명이하
	$danFee10 = $row['cnt4amt']; //주야간보호 10명이상

	Unset($row);


	//사용기간
	$useFrom = $yymm.'01';
	$useTo	 = $yymm.$lastday;


	$unitStr = Array('1'=>'고객', '2'=>'직원', '3'=>'문자', '4'=>'고정');


	//청구구분
	$sql = 'SELECT	org_no, acct_gbn
			FROM	cv_reg_info';
	$arrAcctGbn = $conn->_fetch_array($sql,'org_no');


	//서비스 항목
	$sql = 'SELECT	1 AS svc_gbn
			,		svc_cd
			,		svc_nm
			,		pro_cd
			,		unit_gbn
			,		day_cal
			FROM	cv_svc_main
			WHERE	parent_cd IS NOT NULL
			UNION	ALL
			SELECT	2
			,		svc_cd
			,		svc_nm
			,		NULL
			,		unit_gbn
			,		day_cal
			FROM	cv_svc_sub
			WHERE	parent_cd IS NOT NULL
			UNION	ALL
			SELECT	9, \'99\', \'할인금\', NULL, \'4\', \'N\'';

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$svcList[$row['svc_gbn'].'_'.$row['svc_cd']] = Array(
			'name'=>$row['svc_nm']
		,	'svcGbn'=>$row['svc_gbn']
		,	'svcCd'=>$row['svc_cd']
		,	'proCd'=>$row['pro_cd']
		,	'unitCd'=>$row['unit_gbn'] //초과 단위 1:고객, 2:직원, 3:SMS, 4:고정
		,	'unitStr'=>$unitStr[$row['unit_gbn']]
		,	'acctGbn'=>'1' //과금기준 1:정율제, 2:정액제
		,	'tgtCnt'=>0 //대상수
		,	'stndCost'=>0 //기본금
		,	'overCost'=>0 //초과단가
		,	'limitCnt'=>0 //제한수
		,	'dayCal'=>$row['day_cal'] //일수계산여부
		,	'tmpAmt'=>0 //현청구금액(임시)
		);
	}

	$conn->row_free();


	$sql = 'SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname  AS manager
			FROM	m00center
			INNER	JOIN	cv_reg_info AS a
					ON		a.org_no = m00_mcode
					AND		\''.$yymm.'\' BETWEEN LEFT(a.from_dt,6) AND LEFT(a.to_dt,6)
					AND		CASE WHEN a.rs_cd = \'3\' THEN 0
								 WHEN a.rs_cd = \'4\' AND LEFT(cont_dt, 6) > \''.$yymm.'\' THEN 0 ELSE 1 END = 1';

	if ($orgNo){
		$sql .= ' WHERE m00_mcode = \''.$orgNo.'\'';
	//}else{
	//	$sql .= ' WHERE m00_domain = \''.$company.'\'';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$centerList[$row['org_no']]['name'] = $row['org_nm'];
		$centerList[$row['org_no']]['manager'] = $row['manager'];
		$centerList[$row['org_no']]['cnt'] = SizeOf($svcList);
		$centerList[$row['org_no']]['svc']= $svcList;
	}

	$conn->row_free();

	//삭제쿼리
	$sql = 'DELETE
			FROM	cv_svc_acct_list
			WHERE	yymm = \''.$yymm.'\'';

	if ($orgNo){
		$sql .= '
			AND		org_no = \''.$orgNo.'\'';
	}

	$query[] = $sql;

	if (is_array($centerList)){
		foreach($centerList as $orgNo => $R){
			//대상자수
			$sql = 'SELECT	t01_mkind AS svc_cd, COUNT(DISTINCT t01_jumin) AS cnt
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_del_yn	= \'N\'
					AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
					AND		CASE WHEN t01_mkind = \'0\' AND t01_svc_subcode = \'200\' THEN 1
								 WHEN t01_mkind = \'5\' THEN 1 ELSE 0 END = 1
					GROUP	BY t01_mkind';

			$tgtRow = $conn->_fetch_array($sql, 'svc_cd');

			if ($tgtRow['0']['cnt'] > 15){
				$careFee = $careFee30;
			}else{
				$careFee = $careFee15;
			}

			if ($tgtRow['5']['cnt'] >= 10){
				$danFee = $danFee10;
			}else{
				$danFee = $danFee9;
			}

			$sql = 'SELECT	svc_gbn
					,		svc_cd
					,		acct_gbn
					,		stnd_cost
					,		over_cost
					,		limit_cnt
					,		from_dt
					,		to_dt
					,		acct_yn
					,		acct_from
					,		acct_to
					FROM	cv_svc_fee
					WHERE	org_no	= \''.$orgNo.'\'
					AND		use_yn	= \'Y\'
					AND		del_flag= \'N\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
					AND		LEFT(REPLACE(acct_from,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(acct_to,\'-\',\'\'),6)	>= \''.$yymm.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			if ($rowCnt > 0){
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					if (!$tmpOrgCd[$orgNo]['1_01']) $tmpOrgCd[$orgNo]['1_01'] = $row['svc_gbn'].'_'.$row['svc_cd'];

					if ($row['acct_gbn'] == '1' && $row['svc_gbn'] == '1' && $row['svc_cd'] == '11'){
						//재가요양
						$row['stnd_cost'] = $careFee;
					}else if ($row['acct_gbn'] == '1' && $row['svc_gbn'] == '1' && $row['svc_cd'] == '14'){
						//주야간보호
						$row['stnd_cost'] = $danFee;
					}

					if ($yymm >= '201512' && $row['svc_gbn'].'_'.$row['svc_cd'] == '2_11') $row['stnd_cost'] = 0;

					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['acctYn']	= $row['acct_yn']; //과금여부
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['acctGbn']	= $row['acct_gbn']; //과금기준 1:정율제, 2:정액제
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['stndAmt']	= $row['stnd_cost']; //기본금
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['limitCnt']	= $row['limit_cnt']; //제한수
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['tgtCnt']	= $tgtRow[$svcList[$row['svc_gbn'].'_'.$row['svc_cd']]['proCd']]['cnt']; //대상수
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overCnt']	= 0; //초과수
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overCost']	= $row['over_cost']; //초과단가
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['overAmt']	= 0; //초과금액
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['acctAmt']	= 0; //과금금액

					//월중간 시작과 종료시 일수로 계산한다.
					//if ($row['svc_gbn'] == '1'){
					if ($R['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['dayCal'] == 'Y'){
						if ($yymm.'01' < $row['acct_from']){
							$diffDay = $lastday - $myF->dateDiff('d', $yymm.'01', $row['acct_from']);
							$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] = $diffDay;
						}else if ($yymm.$lastday > $row['to_dt']){
							$diffDay = $myF->dateDiff('d', $yymm.'01', $row['to_dt']);
							$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] = $diffDay;
						}else{
							$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] = 0;
						}
					}else{
						$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] = 0;
					}

					//일수계산(원단위절사)
					if ($centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] > 0){
						$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['stndAmt'] = Floor($centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['stndAmt'] / $lastday * $centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['days'] / 10) * 10;
					}

					if ($row['from_dt'] <= $useFrom) $row['from_dt'] = $useFrom;
					if ($row['to_dt'] >= $useTo) $row['to_dt'] = $useTo;

					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['useFrom']	= $row['from_dt']; //사용기간
					$centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['useTo']		= $row['to_dt']; //사용기간

					//임시금액
					if ($yymm <= $tmpYm && $row['acct_yn'] == 'Y'){
						$centerList[$orgNo]['svc'][$tmpOrgCd[$orgNo]['1_01']]['tmpAmt'] += $centerList[$orgNo]['svc'][$row['svc_gbn'].'_'.$row['svc_cd']]['stndAmt'];
					}
				}
			}else{
				Unset($centerList[$orgNo]);
			}

			$conn->row_free();

			Unset($tgtRow);
		}


		if ($yymm >= '201512'){
			//2015년 12월 부터 장기요양 사용시 바우처를 무료로 함.
			foreach($centerList as $orgNo => $R){
				if ($R['svc']['1_11']['stndAmt'] > 0 && $R['svc']['1_21']['stndAmt'] + $R['svc']['1_22']['stndAmt'] + $R['svc']['1_23']['stndAmt'] + $R['svc']['1_24']['stndAmt'] > 0){
					$centerList[$orgNo]['svc']['1_21']['stndAmt'] = 0;
					$centerList[$orgNo]['svc']['1_22']['stndAmt'] = 0;
					$centerList[$orgNo]['svc']['1_23']['stndAmt'] = 0;
					$centerList[$orgNo]['svc']['1_24']['stndAmt'] = 0;
				}
			}
		}


		//과금금액을 계산한다.
		foreach($centerList as $orgNo => $R){
			foreach($R['svc'] as $svcCD =>$S){
				if ($S['svcGbn'] == '1'){
					if ($S['proCd'] == '7'){
						//복지용구
						$sql = 'SELECT	COUNT(DISTINCT app_no)
								FROM	wmd_longterm_list
								WHERE	org_no	= \''.$orgNo.'\'
								AND		yymm	= \''.$yymm.'\'';
					}else{
						//서비스 인원수
						$sql = 'SELECT	COUNT(DISTINCT t01_jumin)
								FROM	t01iljung
								WHERE	t01_ccode	= \''.$orgNo.'\'
								AND		t01_mkind	= \''.$S['proCd'].'\'
								AND		t01_del_yn	= \'N\'
								AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'';

						if ($S['proCd'] == '0') $sql .= ' AND t01_svc_subcode = \'200\'';
					}

					//서비스 인원수
					if ($S['unitCd'] == '1'){
						$unitCnt = $conn->get_data($sql);
					}else{
						$unitCnt = 0;
					}

					$centerList[$orgNo]['svc'][$svcCD]['tgtCnt'] = $unitCnt;

					//초과수
					if ($unitCnt > $S['limitCnt']){
						$centerList[$orgNo]['svc'][$svcCD]['overCnt'] = $unitCnt - $S['limitCnt']; //초과수
						$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = Floor($centerList[$orgNo]['svc'][$svcCD]['overCnt'] * $S['overCost'] / 10) * 10; //초과금액

						//임시금액
						if ($yymm <= $tmpYm && $centerList[$orgNo]['svc'][$svcCD]['acctYn'] == 'Y'){
							$centerList[$orgNo]['svc'][$tmpOrgCd[$orgNo]['1_01']]['tmpAmt'] += $centerList[$orgNo]['svc'][$svcCD]['overAmt'];
						}
					}
				}else{
					//부서비스
					//초과단위
					if ($S['unitCd'] == '1'){
						//고객
						$unitCnt = 0;
					}else if ($S['unitCd'] == '2'){
						//직원
						$unitCnt = 0;
					}else if ($S['unitCd'] == '3'){
						//SMS
						if ($yymm >= '201506'){

							if ($svcCD == '2_31'){
								/*
								$sql = 'SELECT	COUNT(*)
										FROM	sms_his
										WHERE	org_no	= \''.$orgNo.'\'
										AND		gbn		= \'L\'
										AND		DATE_FORMAT(insert_dt,\'%Y%m\') = \''.$yymm.'\'
										';
								*/
								$sql = 'SELECT	COUNT(*)
										FROM	sms_his as his
										LEFT    JOIN sms_send_fail_log as log
										ON      log.call_seq = his.call_seq
										WHERE	his.org_no	= \''.$orgNo.'\'
										AND		his.gbn		= \'L\'
										AND     log.call_rst_cd is null
										AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'
										';
							}else{
								/*
								$sql = 'SELECT	SUM(CASE WHEN IFNULL(sms_type,\'SMS\') = \'LMS\' THEN 2 ELSE 1 END)
										FROM	sms_his
										WHERE	org_no	 = \''.$orgNo.'\'
										AND		gbn		!= \'L\'
										AND		DATE_FORMAT(insert_dt,\'%Y%m\') = \''.$yymm.'\'
										';
								*/
								$sql = 'SELECT	SUM(CASE WHEN IFNULL(sms_type,\'SMS\') = \'LMS\' THEN 2 ELSE 1 END)
										FROM	sms_his as his
										LEFT    JOIN sms_send_fail_log as log
										ON      log.call_seq = his.call_seq
										WHERE	his.org_no	 = \''.$orgNo.'\'
										AND		his.gbn		!= \'L\'
										AND     log.call_rst_cd is null
										AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'
										';
							}
						}else{
							$sql = 'SELECT	COUNT(*)
									FROM	sms_'.$yymm.'
									WHERE	org_no = \''.$orgNo.'\'';
						}
						$unitCnt = $conn->get_data($sql);
					}else if ($S['unitCd'] == '4'){
						//고정
						$unitCnt = 0;
					}

					$centerList[$orgNo]['svc'][$svcCD]['tgtCnt'] = $unitCnt;

					if ($unitCnt > $S['limitCnt']){
						$centerList[$orgNo]['svc'][$svcCD]['overCnt'] = $unitCnt - $S['limitCnt']; //초과수
						$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = $centerList[$orgNo]['svc'][$svcCD]['overCnt'] * $S['overCost']; //초과금액

						//임시금액
						if ($yymm <= $tmpYm && $centerList[$orgNo]['svc'][$svcCD]['acctYn'] == 'Y') $centerList[$orgNo]['svc'][$tmpOrgCd[$orgNo]['1_01']]['tmpAmt'] += $centerList[$orgNo]['svc'][$svcCD]['overAmt'];
					}
				}

				if ($S['acctGbn'] == '1'){
					//정율제
				}else{
					//정액제
					$centerList[$orgNo]['svc'][$svcCD]['overAmt'] = 0;
				}
			}
		}

		//쿼리생성
		foreach($centerList as $orgNo => $R){
			foreach($R['svc'] as $svcCD =>$S){
				//if ($S['stndAmt'] + $S['overAmt'] != 0){
					if ($yymm > $tmpYm){
						if ($S['acctYn'] == 'Y'){
							$acctAmt= $S['stndAmt'] + $S['overAmt'];
							$disAmt	= 0;
						}else{
							$acctAmt= 0;
							$disAmt	= 0; //$S['stndAmt'] + $S['overAmt'];
							$S['stndAmt'] = 0;
						}
					}else{
						if ($svcCD == $tmpOrgCd[$orgNo]['1_01']){
							//현청구금액
							$sql = 'SELECT	amt
									FROM	cv_svc_acct_amt
									WHERE	org_no = \''.$orgNo.'\'
									AND		yymm <= \''.$yymm.'\'
									ORDER	BY yymm DESC
									LIMIT	1';

							$acctAmt = $conn->get_data($sql);
							$disAmt	= 0;

							$S['stndAmt'] = $acctAmt;
						}
					}

					if ($yymm > $tmpYm || $svcCD == $tmpOrgCd[$orgNo]['1_01']){
						$sql = 'INSERT INTO cv_svc_acct_list VALUES (
								 \''.$orgNo.'\'
								,\''.$yymm.'\'
								,\''.$S['svcGbn'].'\'
								,\''.$S['svcCd'].'\'
								,\''.$acctYm.'\'
								,\''.$S['proCd'].'\'
								,\''.($arrAcctGbn[$orgNo]['acct_gbn'] ? $arrAcctGbn[$orgNo]['acct_gbn'] : '2').'\'
								,\''.$S['stndAmt'].'\'
								,\''.$S['unitCd'].'\'
								,\''.$S['limitCnt'].'\'
								,\''.$S['overCnt'].'\'
								,\''.$S['overCost'].'\'
								,\''.$S['overAmt'].'\'
								,\''.$acctAmt.'\'
								,\''.$disAmt.'\'
								,\''.$S['useFrom'].'\'
								,\''.$S['useTo'].'\'
								,\''.$S['tmpAmt'].'\'
								,\''.$S['tgtCnt'].'\'
								,\''.$_SESSION['userCode'].'\'
								,NOW()
								)';

						$query[] = $sql;
					}
				//}
			}
		}
	}

	//쿼리
	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '!!! '.$conn->error_msg.' !!!';
				 exit;
			}
		}

		$conn->commit();
	}

	Unset($svcList);
	Unset($centerList);
	Unset($query);


	if ($loc == 'INFO'){
		/*
		$sql = 'SELECT	SUM(CASE WHEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt > 0
								 THEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END - dis_amt ELSE 0 END) AS amt
				FROM	cv_svc_acct_list
				WHERE	org_no	=  \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'
				';
		*/
		$sql = 'SELECT	SUM(CASE WHEN tmp_amt - dis_amt > 0 THEN tmp_amt - dis_amt ELSE 0 END) AS amt
				FROM	cv_svc_acct_list
				WHERE	org_no	=  \''.$orgNo.'\'
				AND		yymm	= \''.$yymm.'\'
				';

		$amt = $conn->get_data($sql);

		echo 'acctAmt='.$amt;
	}

	include_once('../inc/_db_close.php');
?>
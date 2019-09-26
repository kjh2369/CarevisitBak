<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$data	= Explode('?', $_POST['data']);


	$ADD_KEY = -100;


	// 회계사코드
	$sql = 'SELECT	m00_account_firm_cd
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_mkind = \'0\'';
	$firmCd = $conn->get_data($sql);


	// 공제 항목 추가
	$sql = 'SELECT	count(*)
			FROM	salary_addon
			WHERE	org_no		= \''.$orgNo.'\'
			AND		salary_type	= \'2\'
			AND		salary_index= \''.$ADD_KEY.'\'';
	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$sql = 'INSERT INTO salary_addon (
				 org_no
				,salary_type
				,salary_index
				,salary_subject
				,salary_pay
				,salary_seq) VALUES (
				 \''.$orgNo.'\'
				,\'2\'
				,\''.$ADD_KEY.'\'
				,\'본인부담금공제\'
				,\'0\'
				,\'1\')';
		$query[] = $sql;
	}

	$sql = 'SELECT	count(*)
			FROM	salary_addon
			WHERE	org_no		= \''.$orgNo.'\'
			AND		salary_type	= \'1\'
			AND		salary_index= \''.$ADD_KEY.'\'';
	$cnt = $conn->get_data($sql);

	if ($conn->get_data($sql) == 0){
		$sql = 'INSERT INTO salary_addon (
				 org_no
				,salary_type
				,salary_index
				,salary_subject
				,salary_pay
				,salary_seq) VALUES (
				 \''.$orgNo.'\'
				,\'1\'
				,\''.$ADD_KEY.'\'
				,\'본인부담금수당\'
				,\'0\'
				,\'1\')';
		$query[] = $sql;
	}

	$sql = 'DELETE
			FROM	unpaid_auto_list
			WHERE	org_no		= \''.$orgNo.'\'
			AND		unpaid_yymm	= \''.$year.$month.'\'';
	$query[] = $sql;

	$sql = 'DELETE
			FROM	salary_addon_pay
			WHERE	org_no		= \''.$orgNo.'\'
			AND		salary_yymm	= \''.$year.$month.'\'
			AND		salary_index= \''.$ADD_KEY.'\'';
	$query[] = $sql;

	if (is_array($data)){
		foreach($data as $tmpIdx => $R){
			parse_str($R,$R);

			//print_r($R);

			if ($R['entDt'] && $R['entSeq']){
				// 수입테이블 삭제
				$sql = 'DELETE
						FROM	center_income
						WHERE	org_no			= \''.$orgNo.'\'
						AND		income_ent_dt	= \''.$R['entDt'].'\'
						AND		deposit_seq		= \''.$R['entSeq'].'\'';
				$query[] = $sql;

				// 미납연결 삭제
				$sql = 'DELETE
						FROM	unpaid_deposit_list
						WHERE	org_no			= \''.$orgNo.'\'
						AND		deposit_ent_dt	= \''.$R['entDt'].'\'
						AND		deposit_seq		= \''.$R['entSeq'].'\'';
				$query[] = $sql;

				// 입금테이블 삭제
				$sql = 'DELETE
						FROM	unpaid_deposit
						WHERE	org_no			= \''.$orgNo.'\'
						AND		deposit_ent_dt	= \''.$R['entDt'].'\'
						AND		deposit_seq		= \''.$R['entSeq'].'\'
						AND		deposit_auto	= \'Y\'';
				$query[] = $sql;
			}

			if ($R['memCd']){
				if (!$memData[$R['memCd']]){
					 $memData[$R['memCd']]['memCd'] = $ed->de64($R['memCd']);
					 $memData[$R['memCd']]['memNm'] = $R['memNm']; //직원명
					 $memData[$R['memCd']]['payYn'] = $R['payYn']; //수당 지급여부
				}

				$memData[$R['memCd']]['cltCd'] = $memData[$R['memCd']]['cltCd'].($memData[$R['memCd']]['cltCd'] ? '|' : '').$ed->de64($R['cltCd']); //수급자 주민번호
				$memData[$R['memCd']]['amt'] += $R['amt'];
				$memData[$R['memCd']]['cnt'] ++;
			}
		}

		if (is_array($memData)){
			foreach($memData as $memCd => $R){
				// 공제 내역 저장
				$sql = 'INSERT INTO unpaid_auto_list (
						 org_no
						,unpaid_yymm
						,unpaid_jumin
						,unpaid_per_cnt
						,unpaid_per_cd
						,unpaid_amt) VALUES (
						 \''.$orgNo.'\'
						,\''.$year.$month.'\'
						,\''.$R['memCd'].'\'
						,\''.$R['cnt'].'\'
						,\''.$R['cltCd'].'\'
						,\''.$R['amt'].'\'
						)';
				$query[] = $sql;

				// 급여에 공제 적용
				$sql = 'INSERT INTO salary_addon_pay (
						 org_no
						,salary_yymm
						,salary_jumin
						,salary_type
						,salary_index
						,salary_subject
						,salary_pay) VALUES (
						 \''.$orgNo.'\'
						,\''.$year.$month.'\'
						,\''.$R['memCd'].'\'
						,\'2\'
						,\''.$ADD_KEY.'\'
						,\'본인부담금공제\'
						,\''.$R['amt'].'\'
						)';
				$query[] = $sql;

				// 동거가족 본인부담금 수당지급
				if ($R['payYn'] == 'Y'){
					$sql = 'INSERT INTO salary_addon_pay (
							 org_no
							,salary_yymm
							,salary_jumin
							,salary_type
							,salary_index
							,salary_subject
							,salary_pay) VALUES (
							 \''.$orgNo.'\'
							,\''.$year.$month.'\'
							,\''.$R['memCd'].'\'
							,\'1\'
							,\''.$ADD_KEY.'\'
							,\'본인부담금수당\'
							,\''.$R['amt'].'\'
							)';
					$query[] = $sql;
				}

			}
			Unset($memData);

			foreach($data as $tmpIdx => $R){
				parse_str($R,$R);

				$R['memCd'] = $ed->de64($R['memCd']);
				$R['cltCd'] = $ed->de64($R['cltCd']);

				//순번
				if (!$entSeq[$R['entDt']]){
					$sql = 'SELECT	IFNULL(MAX(deposit_seq),0)
							FROM	unpaid_deposit
							WHERE	org_no			= \''.$orgNo.'\'
							AND		deposit_ent_dt	= \''.$R['entDt'].'\'';
					$entSeq[$R['entDt']] = $conn->get_data($sql);
				}

				if (!$inSeq[$R['entDt']]){
					$sql = 'SELECT	IFNULL(MAX(income_seq),0)
							FROM	center_income
							WHERE	org_no			= \''.$orgNo.'\'
							AND		income_ent_dt	= \''.$R['entDt'].'\'';
					$inSeq[$R['entDt']] = $conn->get_data($sql);
				}

				$entSeq[$R['entDt']] ++;
				$inSeq[$R['entDt']] ++;

				// 입금테이블
				$sql = 'INSERT INTO unpaid_deposit (
						 org_no
						,deposit_ent_dt
						,deposit_seq
						,create_dt
						,create_id
						,deposit_reg_dt
						,deposit_jumin
						,deposit_yymm
						,deposit_type
						,deposit_amt
						,deposit_ahead
						,deposit_auto
						,deposit_mem) VALUES (
						 \''.$orgNo.'\'
						,\''.$R['entDt'].'\'
						,\''.$entSeq[$R['entDt']].'\'
						,NOW()
						,\''.$_SESSION['userCode'].'\'
						,\''.$R['entDt'].'\'
						,\''.$R['cltCd'].'\'
						,\''.$year.$month.'\'
						,\'01\'
						,\''.$R['amt'].'\'
						,\'0\'
						,\'Y\'
						,\''.$R['memCd'].'\'
						)';
				$query[] = $sql;

				// 미납연결
				$sql = 'INSERT INTO unpaid_deposit_list (
						 org_no
						,deposit_ent_dt
						,deposit_seq
						,list_seq
						,unpaid_yymm
						,unpaid_jumin
						,deposit_amt) VALUES (
						 \''.$orgNo.'\'
						,\''.$R['entDt'].'\'
						,\''.$entSeq[$R['entDt']].'\'
						,\'1\'
						,\''.$year.$month.'\'
						,\''.$R['cltCd'].'\'
						,\''.$R['amt'].'\'
						)';
				$query[] = $sql;

				$proofNo = $conn->_proofNo($orgNo, $R['entDt'], '0111111','income');

				// 수입테이블
				$sql = 'INSERT INTO center_income (
						 org_no
						,income_ent_dt
						,income_seq
						,deposit_seq
						,create_id
						,create_dt
						,account_firm_cd
						,income_acct_dt
						,income_amt
						,income_item
						,income_item_cd
						,proof_year
						,proof_no) VALUES (
						 \''.$orgNo.'\'
						,\''.$R['entDt'].'\'
						,\''.$inSeq[$R['entDt']].'\'
						,\''.$entSeq[$R['entDt']].'\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						,\''.$firmCd.'\'
						,\''.$R['entDt'].'\'
						,\''.$R['amt'].'\'
						,\'자동미수입금('.$R['cltNm'].')\'
						,\'0111111\'
						,\''.SubStr($R['entDt'],0,4).'\'
						,\''.$proofNo.'\')
						';
				$query[] = $sql;
			}
		}

		Unset($data);
	}

	if (is_array($query)){
		/*foreach($query as $sql){
			#echo $sql.'<br><br>';
			echo $sql.chr(13).chr(13);
		}*/

		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 if ($debug) echo $conn->error_msg.chr(13).$conn->error_query.chr(13);
				 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>
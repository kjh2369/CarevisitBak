<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$col['org_no'] = $_POST['txtOrgNo'];

	$sql = 'SELECT	LEFT(from_dt, 6) AS from_ym, LEFT(to_dt, 6) AS to_ym, bill_kind
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$col['org_no'].'\'
			AND		del_flag = \'N\'
			';
	$bill_info = $conn->_fetch_array($sql);

	$issueDt = $_POST['orgIssueDt'];
	$issueSeq = $_POST['orgIssueSeq'];

	$col['issue_dt'] = str_replace('-', '', $_POST['txtIssueDt']);


	if ($issueDt == $col['issue_dt']){
		$IsNew = false;
		$col['issue_seq'] = $issueSeq;
	}else{
		$IsNew = true;

		if ($issueDt && $issueSeq){
			$sql = 'UPDATE	cv_pay_in
					SET		del_flag = \'Y\'
					WHERE	org_no		= \''.$col['org_no'].'\'
					AND		issue_dt	= \''.$issueDt.'\'
					AND		issue_seq	= \''.$issueSeq.'\'';

			$query[] = $sql;
		}

		$sql = 'SELECT	IFNULL(MAX(issue_seq), 0) + 1
				FROM	cv_pay_in
				WHERE	org_no	 = \''.$col['org_no'].'\'
				AND		issue_dt = \''.$col['issue_dt'].'\'';

		$col['issue_seq'] = $conn->get_data($sql);
	}


	$col['issue_time'] = '000000'.str_replace(':', '', $_POST['txtIssueTime']);
	$col['issue_time'] = SubStr($col['issue_time'], StrLen($col['issue_time']) - 6, StrLen($col['issue_time']));
	$col['claim_dt'] = str_replace('-', '', $_POST['txtClaimDt']);
	$col['claim_amt'] = str_replace(',', '', $_POST['txtClaimAmt']);
	$col['cms_no'] = $_POST['txtCmsno'];
	$col['in_gbn'] = $_POST['optInGbn'];
	$col['in_amt'] = str_replace(',', '', $_POST['txtInAmt']);
	$col['out_stat'] = $_POST['cboOutStat'];
	$col['cont_com'] = $_POST['cboContCom'];
	$col['out_bank'] = $_POST['txtOutBank'];
	$col['out_acct_no'] = $_POST['txtOutAcctNo'];
	$col['in_bank'] = $_POST['cboInBank'];
	$col['in_acct_no'] = $_POST['txtInAcctNo'];
	$col['reg_gbn'] = '개별';
	$col['acct_log'] = $_POST['txtAcctLog'];
	$col['remark'] = $_POST['txtOther'];
	$useYymm = str_replace('-', '', $_POST['txtUseYymm']);

	//if ($col['in_gbn'] == '2') $col['out_stat'] = '';

	$sql = 'SELECT	IFNULL(MAX(show_key), 0)
					FROM	cv_pay_in
					WHERE	LEFT(show_key, 8) = \''.Date('Ymd').'\'';
	$col['show_key'] = $conn->get_data($sql);

	if ($col['show_key'] < 1) $col['show_key'] = Date('Ymd').'0000';

	$col['show_key'] ++;
	$col['del_flag'] = 'N';
	$col['insert_id'] = $_SESSION['userCode'];
	$col['insert_dt'] = Date('Y-m-d');

	if ($IsNew){
		foreach($col as $column => $val){
			$sl1 .= ($sl1 ? ',' : '').$column;
			$sl2 .= ($sl2 ? ',' : '').'\''.$val.'\'';
		}

		$query[] = 'INSERT INTO cv_pay_in ('.$sl1.') VALUE ('.$sl2.')';
	}else{
		$sql = 'UPDATE	cv_pay_in SET del_flag = \'N\' ';

		foreach($col as $column => $val){
			if ($column == 'org_no') continue;
			if ($column == 'issue_dt') continue;
			if ($column == 'issue_seq') continue;

			$sql .= ','.$column.'=\''.$val.'\'';
		}

		$sql .= '
				WHERE	org_no		= \''.$col['org_no'].'\'
				AND		issue_dt	= \''.$issueDt.'\'
				AND		issue_seq	= \''.$issueSeq.'\'';

		$query[] = $sql;
	}


	if ($issueDt && $issueSeq){
		$sql = 'UPDATE	cv_pay_in_dtl
				SET		del_flag = \'Y\'
				WHERE	org_no		= \''.$col['org_no'].'\'
				AND		issue_dt	= \''.$issueDt.'\'
				AND		issue_seq	= \''.$issueSeq.'\'';

		$query[] = $sql;
	}


	if ($col['in_gbn'] == '1'){
		$sql = 'SELECT	COUNT(*)
				FROM	cv_pay_in_dtl
				WHERE	org_no		= \''.$col['org_no'].'\'
				AND		issue_dt	= \''.$col['issue_dt'].'\'
				AND		issue_seq	= \''.$col['issue_seq'].'\'
				AND		dtl_seq		= \'1\'';

		$cnt = $conn->get_data($sql);

		$claimYymm = $myF->dateAdd('month', 1, $useYymm.'01', 'Ym');

		for($i=0; $i<count($bill_info); $i++){
			if ($bill_info[$i]['from_ym'] <= $useYymm && $bill_info[$i]['to_ym'] >= $useYymm){
				if ($bill_info[$i]['bill_kind'] == '1'){
					$claimYymm = $useYymm;
					break;
				}
			}
		}

		if ($cnt > 0){
			$sql = 'UPDATE	cv_pay_in_dtl
					SET		use_yymm	= \''.$useYymm.'\'
					,		claim_yymm	= \''.$claimYymm.'\'
					,		in_amt		= \''.$col['in_amt'].'\'
					,		del_flag	= \'N\'
					WHERE	org_no		= \''.$col['org_no'].'\'
					AND		issue_dt	= \''.$col['issue_dt'].'\'
					AND		issue_seq	= \''.$col['issue_seq'].'\'
					AND		dtl_seq		= \'1\'';
		}else{
			$sql = 'INSERT INTO cv_pay_in_dtl (org_no, issue_dt, issue_seq, dtl_seq, use_yymm, claim_yymm, in_amt) VALUES (
					 \''.$col['org_no'].'\'
					,\''.$col['issue_dt'].'\'
					,\''.$col['issue_seq'].'\'
					,\'1\'
					,\''.$useYymm.'\'
					,\''.$claimYymm.'\'
					,\''.$col['in_amt'].'\'
					)';
		}
		$query[] = $sql;
	}else{
		$usehis = Explode('?', $_POST['usehis']);

		if (is_array($usehis)){
			$dtlSeq = 1;
			foreach($usehis as $tmpIdx => $R){
				parse_str($R, $R);

				$sql = 'SELECT	COUNT(*)
						FROM	cv_pay_in_dtl
						WHERE	org_no		= \''.$col['org_no'].'\'
						AND		issue_dt	= \''.$col['issue_dt'].'\'
						AND		issue_seq	= \''.$col['issue_seq'].'\'
						AND		dtl_seq		= \''.$dtlSeq.'\'';

				$cnt = $conn->get_data($sql);

				$R['yymm'] = str_replace('-', '', $R['yymm']);
				$R['amt'] = str_replace(',', '', $R['amt']);

				$claimYymm = $myF->dateAdd('month', 1, $R['yymm'].'01', 'Ym');

				for($i=0; $i<count($bill_info); $i++){
					if ($bill_info[$i]['from_ym'] <= $R['yymm'] && $bill_info[$i]['to_ym'] >= $R['yymm']){
						if ($bill_info[$i]['bill_kind'] == '1'){
							$claimYymm = $R['yymm'];
							break;
						}
					}
				}

				if ($cnt > 0){
					$sql = 'UPDATE	cv_pay_in_dtl
							SET		use_yymm	= \''.$R['yymm'].'\'
							,		claim_yymm	= \''.$claimYymm.'\'
							,		in_amt		= \''.$R['amt'].'\'
							,		del_flag	= \'N\'
							WHERE	org_no		= \''.$col['org_no'].'\'
							AND		issue_dt	= \''.$col['issue_dt'].'\'
							AND		issue_seq	= \''.$col['issue_seq'].'\'
							AND		dtl_seq		= \''.$dtlSeq.'\'';
				}else{
					$sql = 'INSERT INTO cv_pay_in_dtl (org_no, issue_dt, issue_seq, dtl_seq, use_yymm, claim_yymm, in_amt) VALUES (
							 \''.$col['org_no'].'\'
							,\''.$col['issue_dt'].'\'
							,\''.$col['issue_seq'].'\'
							,\''.$dtlSeq.'\'
							,\''.$R['yymm'].'\'
							,\''.$claimYymm.'\'
							,\''.$R['amt'].'\'
							)';
				}

				$query[] = $sql;

				$dtlSeq ++;
			}
		}
	}


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();

			 echo 'ERROR MSG : '.$conn->error_msg.chr(13).chr(10).$conn->error_query;
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
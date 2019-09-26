<?
	if ($debug && $userCode == '1234'){
		//설정내역
		$sql = 'SELECT	acct_ym, pop_yn, stop_yn, stop_dt
				FROM	cv_acct_pop_set
				WHERE	CONCAT(acct_ym, CASE WHEN pop_day < 10 THEN \'0\' ELSE \'\' END, pop_day) <= \''.Date('Ymd').'\'
				ORDER	BY acct_ym DESC
				LIMIT	1';

		$R = $conn->get_array($sql);
		$defAmt = 0;

		if ($R['pop_yn'] == 'Y' || $R['stop_yn'] == 'Y'){
			//청구금액
			$sql = 'SELECT	SUM(acct_amt) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	 = \''.$userCode.'\'
					AND		acct_ym <= \''.$R['acct_ym'].'\'';
			$acctAmt = $conn->get_data($sql);

			//결제금액
			$sql = 'SELECT	SUM(link_amt)
					FROM	cv_cms_link
					WHERE	org_no	 = \''.$userCode.'\'
					AND		acct_ym	<= \''.$R['acct_ym'].'\'
					AND		del_flag = \'N\'
					AND		IFNULL(link_stat,\'1\') = \'1\'';
			$linkAmt = $conn->get_data($sql);
		}

		$defAmt = $acctAmt - $linkAmt;
		if ($defAmt < 0) $defAmt = 0;
		$_SESSION['UNPAID_YM'] = $R['acct_ym']; //청구년월
		$_SESSION['UNPAID_AMT'] = $defAmt; //미납금액

		Unset($R);
	}
?>
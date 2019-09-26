<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//연별 일정 카운트
	$year	= $_POST['year'];
	$orgNo	= $_POST['orgNo'];
	$arrCnt	= Array(
			1	=>Array('cnt'=>0, 'amt'=>0)
		,	2	=>Array('cnt'=>0, 'amt'=>0)
		,	3	=>Array('cnt'=>0, 'amt'=>0)
		,	4	=>Array('cnt'=>0, 'amt'=>0)
		,	5	=>Array('cnt'=>0, 'amt'=>0)
		,	6	=>Array('cnt'=>0, 'amt'=>0)
		,	7	=>Array('cnt'=>0, 'amt'=>0)
		,	8	=>Array('cnt'=>0, 'amt'=>0)
		,	9	=>Array('cnt'=>0, 'amt'=>0)
		,	10	=>Array('cnt'=>0, 'amt'=>0)
		,	11	=>Array('cnt'=>0, 'amt'=>0)
		,	12	=>Array('cnt'=>0, 'amt'=>0)
	);

	//일정수
	$sql = 'SELECT	yymm
			,		SUM(CASE WHEN sub_cd = \'500\' OR sub_cd = \'800\' THEN 0 ELSE 1 END) AS care_cnt
			,		SUM(CASE WHEN sub_cd = \'500\' OR sub_cd = \'800\' THEN 1 ELSE 0 END) AS bath_nurse_cnt
			FROM	(
					SELECT	LEFT(t01_sugup_date,6) AS yymm
					,		t01_jumin AS jumin
					,		GROUP_CONCAT(DISTINCT t01_svc_subcode) AS sub_cd
					FROM	t01iljung
					WHERE	t01_ccode  = \''.$orgNo.'\'
					AND		t01_mkind  = \'0\'
					AND		t01_del_yn = \'N\'
					AND		LEFT(t01_sugup_date,4) = \''.$year.'\'
					GROUP	BY LEFT(t01_sugup_date,6), t01_jumin
					) AS a
			GROUP	BY yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$month	= IntVal(SubStr($row['yymm'],4,2));

		$arrCnt[$month]['careCnt'] = $row['care_cnt'];
		$arrCnt[$month]['bathNurseCnt'] = $row['bath_nurse_cnt'];
	}

	$conn->row_free();


	for($i=1; $i<=12; $i++){
		$sql = 'SELECT	amt, dft_amt
				FROM	cv_svc_acct_amt
				WHERE	org_no = \''.$orgNo.'\'
				AND		yymm <= \''.$year.($i < 10 ? '0' : '').$i.'\'
				ORDER	BY yymm DESC
				LIMIT	1';

		$row = $conn->get_array($sql);

		$arrCnt[$i]['nowAmt'] = $row['amt'];

		Unset($row);
	}


	//미납금액
	$sql = 'SELECT	CAST(MID(yymm, 5) AS unsigned) AS month, dft_amt
			FROM	cv_svc_acct_amt
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(yymm, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrCnt[$row['month']]['dftAmt'] = $row['dft_amt'];
	}

	$conn->row_free();


	//입금금액
	$sql = 'SELECT	CAST(MID(use_yymm, 5) AS unsigned) AS month, in_amt
			FROM	cv_pay_in_dtl
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			AND		LEFT(use_yymm, 4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrCnt[$row['month']]['inAmt'] = $row['in_amt'];
	}

	$conn->row_free();


	//계산청구
	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		SUM(tmp_amt - dis_amt) AS amt
			FROM	cv_svc_acct_list
			WHERE	org_no =  \''.$orgNo.'\'
			AND		LEFT(yymm,4) = \''.$year.'\'
			GROUP	BY yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$arrCnt[$row['month']]['calAmt'] = $row['amt'];
	}

	$conn->row_free();

	//마감여부
	$sql = 'SELECT	DATE_FORMAT(DATE_ADD(DATE_FORMAT(CONCAT(yymm,\'01\'),\'%Y-%m-%d\'), interval -1 month),\'%m\') AS month, cls_yn
			FROM	cv_close_set
			WHERE	LEFT(DATE_ADD(DATE_FORMAT(CONCAT(yymm,\'01\'),\'%Y-%m-%d\'), interval -1 month),4) = \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrCls[IntVal($row['month'])]['clsYn'] = $row['cls_yn'];
	}

	$conn->row_free();

	//SMS 사용건수
	for($i=1; $i<=12; $i++){
		$yymm = $year.($i < 10 ? '0' : '').$i;

		if ($yymm >= '201506'){
			/*
			$sql = 'SELECT	SUM(CASE WHEN IFNULL(sms_type,\'SMS\') = \'LMS\' THEN 2 ELSE 1 END)
					FROM	sms_his
					WHERE	org_no = \''.$orgNo.'\'
					AND		DATE_FORMAT(insert_dt,\'%Y%m\') = \''.$yymm.'\'';	
			*/

			$sql = 'SELECT	SUM(CASE WHEN IFNULL(sms_type,\'SMS\') = \'LMS\' AND gbn != \'L\' THEN 2 ELSE 1 END)
					FROM	sms_his as his
					LEFT    JOIN sms_send_fail_log as log
					ON      log.call_seq = his.call_seq
					WHERE	org_no = \''.$orgNo.'\'
					AND		his.gbn		!= \'L\'
					AND     log.call_rst_cd is null
					AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'';

		}else{
			$sql = 'SELECT	COUNT(*)
					FROM	sms_'.$yymm.'
					WHERE	org_no = \''.$orgNo.'\'';
		}

		$arrCls[$i]['smsCnt'] = $conn->get_data($sql);

		$sql = 'SELECT	COUNT(*)
			FROM	sms_his as his
			LEFT    JOIN sms_send_fail_log as log
			ON      log.call_seq = his.call_seq
			WHERE	his.org_no	= \''.$orgNo.'\'
			AND		his.gbn		= \'L\'
			AND     log.call_rst_cd is null
			AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'
			';
					
		$arrCls[$i]['lmsCnt'] = $conn->get_data($sql);

	}

	$data = '';

	for($i=0; $i<=12; $i++){
		//$data .= $arrCnt[$i]['cnt'].chr(2);

		$data .= ($data ? '?' : '');
		$data .= 'careCnt='.$arrCnt[$i]['careCnt'];
		$data .= '&bathNurseCnt='.$arrCnt[$i]['bathNurseCnt'];
		//$data .= '&amt='.$arrCnt[$i]['amt'];
		$data .= '&nowAmt='.$arrCnt[$i]['nowAmt'];
		$data .= '&inAmt='.$arrCnt[$i]['inAmt'];
		$data .= '&calAmt='.$arrCnt[$i]['calAmt'];
		$data .= '&clsYn='.$arrCls[$i]['clsYn'];
		$data .= '&smsCnt='.$arrCls[$i]['smsCnt'];
		$data .= '&lmsCnt='.$arrCls[$i]['lmsCnt'];
		$data .= '&dftAmt='.$arrCnt[$i]['dftAmt'];
	}

	echo $data;

	include_once('../inc/_db_close.php');
?>
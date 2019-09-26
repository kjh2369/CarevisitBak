<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		SUM(CASE WHEN gbn = \'1\' THEN 1 ELSE 0 END) AS other_cnt1
			,		SUM(CASE WHEN gbn = \'1\' THEN amt ElSE 0 END) AS otehr_atm1
			,		SUM(CASE WHEN gbn = \'2\' THEN 1 ELSE 0 END) AS other_cnt2
			,		SUM(CASE WHEN gbn = \'2\' THEN amt ElSE 0 END) AS otehr_atm2
			FROM	ie_bm_other_in
			WHERE	org_no		= \''.$orgNo.'\'
			AND		LEFT(yymm,4)= \''.$year.'\'
			GROUP	BY yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();


	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$data[$row['month']]['otCnt1'] = $row['other_cnt1'];
		$data[$row['month']]['otAmt1'] = $row['otehr_atm1'];
		$data[$row['month']]['otCnt2'] = $row['other_cnt2'];
		$data[$row['month']]['otAmt2'] = $row['otehr_atm2'];
	}

	$conn->row_free();

	$sql = 'SELECT	CAST(MID(yymm,5) AS unsigned) AS month
			,		manager_cnt
			,		manager_salary
			,		manager_4insu
			,		member_cnt
			,		member_salary
			,		member_4insu
			FROM	ie_bm_salary
			WHERE	org_no		= \''.$orgNo.'\'
			AND		LEFT(yymm,4)= \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();


	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$data[$row['month']]['mgCnt'] = $row['manager_cnt'];
		$data[$row['month']]['mgPay'] = $row['manager_salary'];
		$data[$row['month']]['mgIns'] = $row['manager_4insu'];

		$data[$row['month']]['mmCnt'] = $row['member_cnt'];
		$data[$row['month']]['mmPay'] = $row['member_salary'];
		$data[$row['month']]['mmIns'] = $row['member_4insu'];
	}

	$conn->row_free();

	$str = '';

	if (is_array($data)){
		foreach($data as $month => $row){
			$str .= ($str ? '?' : '').'month='.$month.'&mgCnt='.$row['mgCnt'].'&mgPay='.$row['mgPay'].'&mgIns='.$row['mgIns'].'&mmCnt='.$row['mmCnt'].'&mmPay='.$row['mmPay'].'&mmIns='.$row['mmIns'].'&otCnt1='.$row['otCnt1'].'&otAmt1='.$row['otAmt1'].'&otCnt2='.$row['otCnt2'].'&otAmt2='.$row['otAmt2'];
		}
	}

	echo $str;

	Unset($data);

	include_once('../inc/_db_close.php');
?>
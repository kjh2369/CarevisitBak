<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($_SESSION['userLevel'] == 'A'){
		$orgNo = $_POST['orgNo'];
	}else{
		$orgNo = $_SESSION['userCenterCode'];
	}
	$yymm	= $_POST['yymm'];

	$sql = 'SELECT	svc_gbn, svc_cd, stnd_amt, unit_cd, limit_cnt, over_cnt, over_cost, over_amt, acct_amt, dis_amt
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$data = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= $data ? '?' : '';
		$data .= 'key='.$row['svc_gbn'].'_'.$row['svc_cd'];
		$data .= '&acctAmt='.$row['acct_amt'];
		$data .= '&stndAmt='.$row['stnd_amt'];
		$data .= '&overAmt='.$row['over_amt'];
		$data .= '&stndCnt='.$row['limit_cnt'];
		$data .= '&overCnt='.$row['over_cnt'];
		$data .= '&overCost='.$row['over_cost'];
		$data .= '&disAmt='.$row['dis_amt'];
		$data .= '&unitCd='.$row['unit_cd'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
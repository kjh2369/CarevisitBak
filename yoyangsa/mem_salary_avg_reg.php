<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code   = $_SESSION['userCenterCode'];
	$jumin  = $ed->de($_POST['jumin']);
	$year   = $_POST['year'];
	$hourly = $_POST['hourly'];
	$salary = $_POST['salary'];

	$sql = 'SELECT COUNT(*)
			  FROM salary_avg
			 WHERE org_no = \''.$code.'\'
			   AND year   = \''.$year.'\'
			   AND jumin  = \''.$jumin.'\'';
	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$sql = 'UPDATE salary_avg
				   SET h_mon  = \''.$_POST['hMon'].'\'
				,      h_pay  = \''.$_POST['hPay'].'\'
				,      h_amt  = \''.$_POST['hAmt'].'\'
				,      s_mon  = \''.$_POST['sMon'].'\'
				,      s_amt  = \''.$_POST['sAmt'].'\'
				,      b_cnt  = \''.$_POST['bCnt'].'\'
				,      b_pay  = \''.$_POST['bPay'].'\'
				,      b_mon  = \''.$_POST['bMon'].'\'
				 WHERE org_no = \''.$code.'\'
				   AND year   = \''.$year.'\'
				   AND jumin  = \''.$jumin.'\'';
	}else{
		$sql = 'INSERT INTO salary_avg (
				 org_no
				,year
				,jumin
				,h_mon
				,h_pay
				,h_amt
				,s_mon
				,s_amt
				,b_cnt
				,b_pay
				,b_mon) VALUES (
				 \''.$code.'\'
				,\''.$year.'\'
				,\''.$jumin.'\'
				,\''.$_POST['hMon'].'\'
				,\''.$_POST['hPay'].'\'
				,\''.$_POST['hAmt'].'\'
				,\''.$_POST['sMon'].'\'
				,\''.$_POST['sAmt'].'\'
				,\''.$_POST['bCnt'].'\'
				,\''.$_POST['bPay'].'\'
				,\''.$_POST['bMon'].'\'
				)';
	}

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once("../inc/_db_close.php");
?>
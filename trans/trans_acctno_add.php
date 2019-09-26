<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];

	$sql = 'SELECT IFNULL(MAX(seq),0) + 1
			  FROM acct_no
			 WHERE org_no = \''.$code.'\'';
	$seq = $conn->get_data($sql);

	$acctNm   = $_POST['acctNm'];
	$bankNm   = $_POST['bankNm'];
	$bankNo   = $_POST['bankNo'];
	$bankAcct = $_POST['bankAcct'];
	$other    = $_POST['other'];

	$sql = 'INSERT INTO acct_no (
			 org_no
			,seq
			,acct_nm
			,bank_nm
			,bank_no
			,bank_acct
			,other
			,insert_dt) VALUES (
			 \''.$code.'\'
			,\''.$seq.'\'
			,\''.$acctNm.'\'
			,\''.$bankNm.'\'
			,\''.$bankNo.'\'
			,\''.$bankAcct.'\'
			,\''.$other.'\'
			,NOW()
			)';

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once('../inc/_db_close.php');
?>
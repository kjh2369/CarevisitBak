<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code       = $_SESSION['userCenterCode'];
	$year       = Date('Y');
	$month      = Date('m');
	$jumin      = 'CENTER';
	$bankNm     = $_POST['bankNm'];
	$bankNo     = $_POST['bankNo'];
	$bankAcct   = $_POST['bankAcct'];
	$transAmt   = $_POST['amt'];

	$sql = 'SELECT IFNULL(MAX(seq),0) + 1
			  FROM trans
			 WHERE org_no = \''.$code.'\'
			   AND yymm   = \''.$year.$month.'\'
			   AND jumin  = \''.$jumin.'\'';
	$seq = $conn->get_data($sql);

	$sql = 'INSERT INTO trans (
			 org_no
			,yymm
			,jumin
			,seq
			,type
			,bank_nm
			,bank_no
			,bank_acct
			,amt
			,stat
			,request_dt) VALUES (
			 \''.$code.'\'
			,\''.$year.$month.'\'
			,\''.$jumin.'\'
			,\''.$seq.'\'
			,\'9\'
			,\''.$bankNm.'\'
			,\''.$bankNo.'\'
			,\''.$bankAcct.'\'
			,\''.$transAmt.'\'
			,\'1\'
			,NOW())';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
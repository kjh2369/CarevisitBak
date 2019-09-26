<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$acct	= $_POST['acct'];
	$amt	= str_replace(',','',$_POST['amt']);

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	ie_bm_charge
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$seq = $conn->get_data($sql);

	$sql = 'INSERT INTO ie_bm_charge (
			 org_no
			,yymm
			,seq
			,acct_cd
			,amt
			,insert_id
			,insert_dt) VALUES (
			 \''.$orgNo.'\'
			,\''.$year.$month.'\'
			,\''.$seq.'\'
			,\''.$acct.'\'
			,\''.$amt.'\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';

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
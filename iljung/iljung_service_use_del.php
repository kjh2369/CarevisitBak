<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//

	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$jumin = $ed->de($_POST['jumin']);
	$svcCD = $_POST['svcCD'];
	$svcID = $_POST['svcID'];
	$seq   = $_POST['seq'];
	$month = (intval($month) < 10 ? '0' : '').intval($month);


	$conn->begin();

	$sql = 'update voucher_make
			   set del_flag      = \'Y\'
			,      update_id     = \''.$_SESSION['userCode'].'\'
			,      update_dt     = now()
			 where org_no        = \''.$code.'\'
			   and voucher_kind  = \''.$svcCD.'\'
			   and voucher_jumin = \''.$jumin.'\'
			   and voucher_yymm  = \''.$year.$month.'\'
			   and voucher_seq   = \''.$seq.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'error';
		 exit;
	}

	$conn->commit();

	echo 'ok';

	include_once('../inc/_db_close.php');
?>
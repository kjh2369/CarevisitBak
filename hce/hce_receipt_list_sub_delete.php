<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$sr		= $_POST['SR'];
	$IPIN	= $_POST['IPIN'];
	$seq	= $_POST['seq'];

	$sql = 'UPDATE	hce_receipt
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		IPIN	= \''.$IPIN.'\'
			AND		rcpt_seq= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();

		 echo 9;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
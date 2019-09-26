<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $ed->de($_POST['orgNo']);
	$fromDt = str_replace('-', '', $_POST['fromDt']);
	$billGbn = $_POST['billGbn'];

	$sql = 'UPDATE	cv_bill_info
			SET		del_flag= \'Y\'
			,		update_id=\''.$_SESSION['userCode'].'\'
			,		update_dt=NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		bill_gbn= \''.$billGbn.'\'
			AND		from_dt	= \''.$fromDt.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();

		 echo $conn->error_msg;
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
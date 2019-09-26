<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$date	= $_POST['date'];
	$seq	= $_POST['seq'];
	$remark = $_POST['remark'];

	$sql = 'UPDATE	cv_pay_in
			SET		remark		= \''.$remark.'\'
			WHERE	org_no		= \''.$orgNo.'\'
			AND		issue_dt	= \''.$date.'\'
			AND		issue_seq	= \''.$seq.'\'
			AND		del_flag	= \'N\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo $conn->error_msg.chr(13).chr(10).$conn->error_query;
	}

	include_once('../inc/_db_close.php');
?>
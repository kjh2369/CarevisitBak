<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_POST['orgNo'];
	$issueDt = $_POST['issueDt'];
	$issueSeq = $_POST['issueSeq'];

	$sql = 'UPDATE	cv_pay_in
			SET		del_flag = \'Y\'
			WHERE	org_no		= \''.$orgNo.'\'
			AND		issue_dt	= \''.$issueDt.'\'
			AND		issue_seq	= \''.$issueSeq.'\'
			';
	$query[] = $sql;

	$sql = 'UPDATE	cv_pay_in_dtl
			SET		del_flag = \'Y\'
			WHERE	org_no		= \''.$orgNo.'\'
			AND		issue_dt	= \''.$issueDt.'\'
			AND		issue_seq	= \''.$issueSeq.'\'
			';
	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();

			 echo 'ERROR MSG : '.$conn->error_msg.chr(13).chr(10).$conn->error_query;
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
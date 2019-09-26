<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	재사정기록 저장
	 *********************************************************/
	$isptSeq = $_POST['seq'];

	$sql = 'UPDATE	hce_re_ispt
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$userCd.'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$query[SizeOf($query)] = $sql;

	$sql = 'SELECT	ispt_dt
			FROM	hce_re_ispt
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		org_type  = \''.$hce->SR.'\'
			AND		IPIN	  = \''.$hce->IPIN.'\'
			AND		rcpt_seq  = \''.$hce->rcpt.'\'
			AND		ispt_seq != \''.$isptSeq.'\'
			AND		del_flag  = \'N\'
			ORDER	BY ispt_seq DESC
			LIMIT	1';

	$isptDt = $conn->get_data($sql);


	$sql = 'UPDATE	hce_proc
			SET		rest_dt	= '.($isptDt ? '\''.$isptDt.'\'' : 'NULL').'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$query[SizeOf($query)] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
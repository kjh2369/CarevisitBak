<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	모니터링기록지
	 *********************************************************/
	$mntrSeq = $_POST['mntrSeq'];

	$sql = 'UPDATE	hce_monitor
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$userCd.'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		mntr_seq= \''.$mntrSeq.'\'';
	$query[] = $sql;

	$sql = 'SELECT	MAX(mntr_dt)
			FROM	hce_monitor
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		org_type  = \''.$hce->SR.'\'
			AND		IPIN	  = \''.$hce->IPIN.'\'
			AND		rcpt_seq  = \''.$hce->rcpt.'\'
			AND		mntr_seq != \''.$mntrSeq.'\'
			AND		del_flag  = \'N\'';
	$mntrDt = $conn->get_data($sql);


	$sql = 'UPDATE	hce_proc';

	if ($mntrDt){
		$sql .= '
			SET		mntr_dt	= \''.$mntrDt.'\'';
	}else{
		$sql .= '
			SET		mntr_dt	= NULL';
	}

	$sql .= '
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
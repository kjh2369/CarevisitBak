<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	서비스 연계 및 의뢰서 등록
	 *********************************************************/
	$orgNo	= $_SESSION['userCenterCode'];
	$connSeq= $_POST['connSeq'];

	//삭제처리
	$sql = 'UPDATE	hce_svc_connect
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		conn_seq= \''.$connSeq.'\'';
	$query[] = $sql;


	//가장최근 일자
	$sql = 'SELECT	req_dt
			FROM	hce_svc_connect
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		org_type  = \''.$hce->SR.'\'
			AND		IPIN	  = \''.$hce->IPIN.'\'
			AND		rcpt_seq  = \''.$hce->rcpt.'\'
			AND		conn_seq != \''.$connSeq.'\'
			AND		del_flag  = \'N\'
			ORDER	BY req_dt DESC
			LIMIT	1';
	$reqDt = $conn->get_data($sql);

	if ($reqDt){
		$sql = 'UPDATE	hce_proc
				SET		conn_dt	= \''.$reqDt.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';
	}else{
		$sql = 'UPDATE	hce_proc
				SET		conn_dt	= NULL
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';
	}
	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
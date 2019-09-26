<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$planSeq= $_POST['planSeq'];

	$sql = 'UPDATE	hce_plan_sheet
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$hce->SR.'\'
			AND		IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt_seq	= \''.$hce->rcpt.'\'
			AND		plan_seq	= \''.$planSeq.'\'';

	$query[] = $sql;


	$sql = 'SELECT	COUNT(*)
			FROM	hce_plan_sheet
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		org_type  = \''.$hce->SR.'\'
			AND		IPIN	  = \''.$hce->IPIN.'\'
			AND		rcpt_seq  = \''.$hce->rcpt.'\'
			AND		plan_seq != \''.$planSeq.'\'
			AND		del_flag  = \'N\'';

	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$sql = 'UPDATE	hce_proc
				SET		plan_dt	= NULL
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$query[] = $sql;
	}


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
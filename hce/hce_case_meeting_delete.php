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

	$sql = 'UPDATE	hce_meeting
			SET		del_flag= \'Y\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_seq= \''.$_POST['meetSeq'].'\'';

	$query[] = $sql;


	$sql = 'SELECT	COUNT(*)
			FROM	hce_meeting
			WHERE	org_no	  = \''.$orgNo.'\'
			AND		org_type  = \''.$hce->SR.'\'
			AND		IPIN	  = \''.$hce->IPIN.'\'
			AND		rcpt_seq  = \''.$hce->rcpt.'\'
			AND		meet_seq != \''.$_POST['meetSeq'].'\'
			AND		del_flag  = \'N\'';

	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$sql = 'UPDATE	hce_proc
				SET		meet_gbn= NULL
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
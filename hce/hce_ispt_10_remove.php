<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];

	$isptSeq = $_POST['isptSeq'];
	$picSeq = $_POST['seq'];

	/*
	$sql = 'SELECT	*
			FROM	hce_inspection_pic
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'
			AND		pic_seq	= \''.$picSeq.'\'';

	$row = $conn->get_array($sql);

	if (is_file($row['pic_path'])){
		@unlink($row['pic_path']);
	}

	Unset($row);
	*/

	$sql = 'UPDATE	hce_inspection_pic
			SET		del_flag = \'Y\'
			,		update_id= \''.$_SESSION['userCode'].'\'
			,		update_dt= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'
			AND		pic_seq	= \''.$picSeq.'\'';

	$conn->begin();
	$conn->execute($sql);
	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
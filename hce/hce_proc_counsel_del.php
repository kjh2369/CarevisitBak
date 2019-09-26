<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	과정상담 삭제
	 *********************************************************/
	$procSeq		= $_POST['procSeq'];

	if (Empty($procSeq)) $procSeq = '1';
	
	$sql = 'UPDATE	hce_proc_counsel
	           SET  del_flag = \'Y\'
			 WHERE	org_no	= \''.$orgNo.'\'
			   AND	org_type= \''.$hce->SR.'\'
			   AND	IPIN	= \''.$hce->IPIN.'\'
			   AND	rcpt_seq= \''.$hce->rcpt.'\'
			   AND	proc_seq= \''.$procSeq.'\'';

	$conn->begin();
	
	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}
		
	$conn->commit();
	
	echo '1';

	include_once('../inc/_db_close.php');
?>
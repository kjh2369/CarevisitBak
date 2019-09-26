<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$document_root = $_SERVER['DOCUMENT_ROOT'];
	$path	= $document_root.str_replace('..','', $_POST['file']);
	
	/*
	$sql = 'UPDATE	hce_inspection_needs
			SET		rough_text = \'\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';
	
	$conn->begin();
	
	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}
	
	$conn->commit();
	*/

	if (is_file($path)){
	
		@unlink($path);
	}

	echo 1;

	include_once("../inc/_db_close.php");
?>
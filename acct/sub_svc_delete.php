<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['code'];
	$svcCd	= $_POST['svcCd'];
	$seq	= $_POST['seq'];

	$sql = 'DELETE
			FROM	sub_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
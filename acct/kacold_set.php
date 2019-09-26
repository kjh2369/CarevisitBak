<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$yn		= $_POST['yn'] == 'Y' ? 'N' : 'Y';

	$sql = 'UPDATE	center_comm
			SET		kacold_yn	= \''.$yn.'\'
			WHERE	org_no		= \''.$orgNo.'\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
	}

	echo $yn;

	include_once('../inc/_db_close.php');
?>
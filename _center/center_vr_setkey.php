<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$vrNo	= $_POST['vrNo'];

	$sql = 'UPDATE	cv_vr_list
			SET		key_yn = CASE WHEN vr_no = \''.$vrNo.'\' THEN \'Y\' ELSE \'N\' END
			WHERE	org_no = \''.$orgNo.'\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
	}

	include_once('../inc/_db_close.php');
?>
<?
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_open.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_http_uri.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$contDt = $_POST['contDt'];

	$sql = 'UPDATE	cv_doc
			SET		skip_dt		= DATE_FORMAT(NOW(),\'%Y%m%d\')
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cont_dt	= \''.$contDt.'\'';

	$conn->begin();
	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
	}

	include_once($_SERVER['DOCUMENT_ROOT'].'/inc/_db_close.php');
?>
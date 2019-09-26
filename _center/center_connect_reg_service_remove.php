<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$svcGbn	= $_POST['svcGbn'];
	$svcCd	= $_POST['svcCd'];
	$seq	= $_POST['seq'];

	$sql = 'UPDATE	cv_svc_fee
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_gbn	= \''.$svcGbn.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();

	//적용
	$userCode = $orgNo;
	include_once('../inc/set_val.php');
	include_once('../inc/_db_close.php');
?>
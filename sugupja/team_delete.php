<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$seq	= $_POST['seq'];

	$sql = 'UPDATE	client_his_team
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
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

	include_once("../inc/_db_close.php");
?>
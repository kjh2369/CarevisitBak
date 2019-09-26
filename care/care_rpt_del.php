<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$suga_cd = $_POST['suga_cd'];
	$seq = $_POST['seq'];

	$sql = 'UPDATE	care_rpt
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_sr	= \''.$SR.'\'
			AND		suga_cd	= \''.$suga_cd.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		$conn->close();
		echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
	}

	include_once('../inc/_db_close.php');
?>
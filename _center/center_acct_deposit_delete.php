<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$cmsNo	= $_POST['cmsNo'];
	$cmsDt	= $_POST['cmsDt'];
	$cmsSeq	= $_POST['cmsSeq'];


	//입금적용 삭제
	$sql = 'UPDATE	cv_cms_link
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$cmsNo.'\'
			AND		cms_dt	= \''.$cmsDt.'\'
			AND		cms_seq	= \''.$cmsSeq.'\'';

	$query[] = $sql;


	//입금삭제
	$sql = 'UPDATE	cv_cms_reg
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cms_no	= \''.$cmsNo.'\'
			AND		cms_dt	= \''.$cmsDt.'\'
			AND		seq		= \''.$cmsSeq.'\'';

	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			 exit;
		}
	}

	$conn->commit();


	include_once('../inc/_db_close.php');
	exit;






	$sql = 'UPDATE	cv_cms_link
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$gbn	= '01'; //서비스 묶음별 카테고리
	$code	= $_POST['code'];


	//자식찾기
	$sql = 'SELECT	COUNT(*)
			FROM	mst_category
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		gbn		= \''.$gbn.'\'
			AND		parent	= \''.$code.'\'
			AND		del_flag= \'N\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$conn->close();
		echo 7;
		exit;
	}


	//항목찾기
	$sql = 'SELECT	COUNT(*)
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		category= \''.$code.'\'
			AND		del_flag= \'N\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$conn->close();
		echo 5;
		exit;
	}


	$sql = 'UPDATE	mst_category
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		gbn		= \''.$gbn.'\'
			AND		code	= \''.$code.'\'';

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
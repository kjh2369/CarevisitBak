<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$appNo	= $_POST['appNo'];
	$regDt	= $_POST['regDt'];
	$svcCd	= $_POST['svcCd'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];

	$sql = 'SELECT	write_yn
			FROM	lg2cv_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		app_no	= \''.$appNo.'\'
			AND		reg_dt	= \''.$regDt.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		from_tm	= \''.$from.'\'';

	$yn = $conn->get_data($sql);

	if ($yn){
		$yn = ($yn == 'Y' ? 'N' : 'Y');
		$sql = 'UPDATE	lg2cv_log
				SET		to_tm		= \''.$from.'\'
				,		write_yn	= \''.$yn.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		app_no	= \''.$appNo.'\'
				AND		reg_dt	= \''.$regDt.'\'
				AND		svc_cd	= \''.$svcCd.'\'
				AND		from_tm	= \''.$from.'\'';
	}else{
		$yn = 'Y';
		$sql = 'INSERT INTO lg2cv_log (
				 org_no
				,app_no
				,reg_dt
				,svc_cd
				,from_tm
				,to_tm
				,write_yn
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$appNo.'\'
				,\''.$regDt.'\'
				,\''.$svcCd.'\'
				,\''.$from.'\'
				,\''.$to.'\'
				,\''.$yn.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo $yn;

	include_once('../inc/_db_close.php');
?>
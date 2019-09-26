<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	COUNT(*)
			FROM	voucher_notice
			WHERE	org_no	= \''.$code.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$sql = 'UPDATE	voucher_notice
				SET		notice		= \''.AddSlashes($_POST['notice']).'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$code.'\'
				AND		svc_cd		= \''.$svcCd.'\'
				AND		yymm		= \''.$year.$month.'\'';
	}else{
		$sql = 'INSERT INTO voucher_notice (
				 org_no
				,svc_cd
				,yymm
				,notice
				,insert_id
				,insert_dt) VALUES (
				 \''.$code.'\'
				,\''.$svcCd.'\'
				,\''.$year.$month.'\'
				,\''.AddSlashes($_POST['notice']).'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->execute($sql);

	include_once('../inc/_db_close.php');
?>
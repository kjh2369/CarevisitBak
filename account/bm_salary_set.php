<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$id		= $_POST['id'];
	$val	= str_replace(',','',$_POST['val']);


	if ($id == 'txtManagerCnt'){
		$col = 'manager_cnt';
	}else if ($id == 'txtManagerPay'){
		$col = 'manager_salary';
	}else if ($id == 'txtMemberCnt'){
		$col = 'member_cnt';
	}else if ($id == 'txtMemberPay'){
		$col = 'member_salary';
	}else{
		$conn->close();
		echo 9;
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_salary
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_salary
				SET		'.$col.' = \''.$val.'\'
				,		update_id= \''.$_SESSION['userCode'].'\'
				,		update_dt= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		yymm	= \''.$year.$month.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_salary (org_no,yymm,'.$col.',insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$year.$month.'\'
				,\''.$val.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

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
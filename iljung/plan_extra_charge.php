<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];
	$yn		= $_POST['yn'];

	$sql = 'SELECT	COUNT(*)
			FROM	dan_extra_charge
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	dan_extra_charge
				SET		yn			= \''.$yn.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		yymm		= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO dan_extra_charge (
				 org_no
				,jumin
				,yymm
				,yn
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$yn.'\'
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
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['ym'];
	$pay	= $_POST['pay'];

	$sql = 'SELECT	COUNT(*)
			FROM	mem_insu_monthly
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	mem_insu_monthly
				SET		monthly	= \''.$pay.'\'
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO mem_insu_monthly (
				 org_no
				,jumin
				,yymm
				,monthly) VALUES (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$pay.'\'
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg;
		 echo 9;
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
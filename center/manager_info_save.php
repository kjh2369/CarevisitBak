<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$name = $_POST['name'];
	$telno = str_replace('-','',$_POST['tel']);

	$sql = 'SELECT	COUNT(*)
			FROM	mst_manager
			WHERE	org_no = \''.$orgNo.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	mst_manager
				SET		name	= \''.$name.'\'
				AND		mobile	= \''.$telno.'\'
				WHERE	org_no	= \''.$orgNo.'\'';
	}else{
		$sql = 'INSERT INTO mst_manager(
				 org_no
				,name
				,mobile
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$name.'\'
				,\''.$telno.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->execute($sql);

	include_once('../inc/_db_open.php');
?>
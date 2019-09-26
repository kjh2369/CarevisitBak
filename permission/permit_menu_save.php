<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$userCd	= $_SESSION['userCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$permit	= Explode('/',SubStr($_POST['menu'],1));

	$sql = 'UPDATE	menu_permit
			SET		use_yn	= \'N\'
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'';

	$conn->execute($sql);

	foreach($permit as $menu){
		$sql = 'SELECT	COUNT(*)
				FROM	menu_permit
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		menu_id	= \''.$menu.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	menu_permit
					SET		write_yn	= \'Y\'
					,		delete_yn	= \'Y\'
					,		own_yn		= \'N\'
					,		use_yn		= \'Y\'
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		menu_id	= \''.$menu.'\'';
		}else{
			$sql = 'INSERT INTO menu_permit (
					 org_no
					,jumin
					,menu_id
					,write_yn
					,delete_yn
					,own_yn
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$menu.'\'
					,\'Y\'
					,\'Y\'
					,\'N\'
					,\''.$userCd.'\'
					,NOW()
					)';
		}

		$conn->execute($sql);
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>
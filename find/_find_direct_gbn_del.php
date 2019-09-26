<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);
	$yymm  = $_POST['yymm'];

	$conn->begin();
	
	$sql = 'DELETE
			FROM	mem_direct_gbn
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';
	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->close();

		echo 'error';
		exit;
	}

	$conn->commit();
	echo 'ok';


	include_once('../inc/_db_close.php');
?>
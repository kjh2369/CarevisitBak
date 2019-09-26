<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$seq	= $_POST['seq'];

	$sql = 'UPDATE	iljung_memo
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		jumin		= \''.$jumin.'\'
			AND		yymm		= \''.$year.$month.'\'
			AND		seq			= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $sql;
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$yymm = $_POST['yymm'];
	$seq = $_POST['seq'];


	//수정
	$sql = 'UPDATE	sw_log
			SET		command			= \''.AddSlashes($_POST['txtCommand']).'\'
			,		sign_manager	= \''.$_POST['signManager'].'\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 echo $conn->error_msg;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
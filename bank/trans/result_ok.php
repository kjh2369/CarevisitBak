<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$code  = $_POST['code'];
	$yymm  = $_POST['yymm'];
	$jumin = $ed->de($_POST['jumin']);
	$seq   = $_POST['seq'];
	$stat  = $_POST['stat'];
	$other = $_POST['other'];

	$sql = 'UPDATE trans
			   SET stat         = \''.$stat.'\'
			,      result_other = \''.$other.'\'
			,      result_dt    = NOW()
			 WHERE org_no       = \''.$code.'\'
			   AND yymm         = \''.$yymm.'\'
			   AND jumin        = \''.$jumin.'\'
			   AND seq          = \''.$seq.'\'';

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
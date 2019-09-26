<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$date	= str_replace('-','',$_POST['date']);
	$time	= str_replace(':','',$_POST['time']);
	$seq	= $_POST['seq'];

	$sql = 'DELETE
			FROM	t01iljung
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \''.$SR.'\'
			AND		t01_jumin		= \''.$jumin.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_sugup_fmtime= \''.$time.'\'
			AND		t01_sugup_seq	= \''.$seq.'\'';

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

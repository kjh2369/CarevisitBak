<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$date = $_POST['date'];
	$time = $_POST['time'];
	$seq = $_POST['seq'];

	if (!$orgNo || !$jumin || !$date || !$time || !$seq){
		$conn->close();
		echo 9;
		exit;
	}

	$sql = 'UPDATE	t01iljung
			SET		t01_del_yn		= \'Y\'
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \'0\'
			AND		t01_jumin		= \''.$jumin.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_sugup_fmtime= \''.$time.'\'
			AND		t01_sugup_seq	= \''.$seq.'\'
			AND		t01_del_yn		= \'N\'';

	/*
	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	*/

	include_once('../inc/_db_close.php');
?>
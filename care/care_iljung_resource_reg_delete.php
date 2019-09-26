<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$conn->begin();

	//기존일정삭제
	#$sql = 'DELETE
	#		FROM	t01iljung
	#		WHERE	t01_ccode		= \''.$code.'\'
	#		AND		t01_mkind		= \''.$sr.'\'
	#		AND		t01_suga_code1	= \''.$suga.'\'
	#		AND		t01_sugup_date >= \''.$year.$month.'01\'
	#		AND		t01_sugup_date <= \''.$year.$month.'31\'
	#		/*AND		t01_status_gbn != \'1\'
	#		AND		t01_status_gbn != \'5\'*/
	#		AND		t01_del_yn		= \'N\'
	#		AND		IFNULL(t01_request,\'\') = \'SERVICE\'';

	$sql = 'UPDATE	t01iljung
			SET		t01_del_yn		= \'Y\'
			WHERE	t01_ccode		= \''.$code.'\'
			AND		t01_mkind		= \''.$sr.'\'
			AND		t01_suga_code1	= \''.$suga.'\'
			AND		t01_sugup_date >= \''.$year.$month.'01\'
			AND		t01_sugup_date <= \''.$year.$month.'31\'
			AND		t01_del_yn		= \'N\'
			AND		IFNULL(t01_request,\'\') = \'SERVICE\'
			';
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
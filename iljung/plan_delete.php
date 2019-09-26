<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];//$_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$year  = $_POST['year'];
	$month = $_POST['month'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$conn->begin();

	//기존일정 삭제
	$sql = 'delete
			from	t01iljung
			where	t01_ccode = \''.$code.'\'
			and		t01_mkind = \''.$svcCd.'\'
			and		t01_jumin = \''.$jumin.'\'';

	if ($svcCd == 'S'){
	}else{
		$sql .= '
			and		t01_status_gbn != \'1\'
			and		t01_status_gbn != \'5\'';
	}

	$sql .= '
			and		left(t01_sugup_date,6) = \''.$year.$month.'\'';

	if ($svcCd == 'S' || $svcCd == 'R'){
		$sql .= ' AND IFNULL(t01_request,\'PERSON\') = \'PERSON\'';
	}

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
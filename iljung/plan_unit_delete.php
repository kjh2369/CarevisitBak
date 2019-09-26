<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];//$_POST['code'];
	$jumin	= $_POST['jumin'];
	$svcCd	= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$seq	= $_POST['seq'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$conn->begin();

	//기존일정 삭제
	$sql = 'DELETE
			  FROM t01iljung
			 WHERE t01_ccode        = \''.$code.'\'
			   AND t01_mkind        = \''.$svcCd.'\'
			   AND t01_jumin        = \''.$jumin.'\'
			   AND t01_sugup_date   = \''.$date.'\'
			   AND t01_sugup_fmtime = \''.$time.'\'
			   AND t01_sugup_seq    = \''.$seq.'\'';

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
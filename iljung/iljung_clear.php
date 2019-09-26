<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_GET['code'];
	$kind  = $_GET['kind'];
	$jumin = $ed->de($_GET['jumin']);
	$date  = $_GET['date'];
	$time  = $_GET['time'];
	$seq   = $_GET['seq'];


	$sql = 'update t01iljung
			   set t01_del_yn       = \'Y\'
			 where t01_ccode        = \''.$code.'\'
			   and t01_mkind        = \''.$kind.'\'
			   and t01_jumin        = \''.$jumin.'\'
			   and t01_sugup_date   = \''.$date.'\'
			   and t01_sugup_fmtime = \''.$time.'\'
			   and t01_sugup_seq    = \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 echo 'error';
		 exit;
	}

	$conn->commit();
	echo 'ok';


	include_once('../inc/_db_close.php');
?>
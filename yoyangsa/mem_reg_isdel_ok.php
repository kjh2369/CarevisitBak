<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);

	$conn->begin();

	$sql = 'delete
			  from m02yoyangsa
			 where m02_ccode  = \''.$code.'\'
			   and m02_yjumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'N';
		 exit;
	}

	$sql = 'delete
			  from mem_his
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'N';
		 exit;
	}
	
	
	$conn->commit();
	echo 'Y';

	include_once('../inc/_db_close.php');
?>
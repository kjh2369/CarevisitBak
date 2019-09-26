<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//$company= $_POST['company'];
	$orgNo	= $_POST['orgNo'];

	$sql = 'UPDATE	m97user
			SET		m97_pass = \'1111\'
			WHERE	m97_user = \''.$orgNo.'\'
			';
	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		$conn->close();
		echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
	}

	include_once('../inc/_db_close.php');
?>
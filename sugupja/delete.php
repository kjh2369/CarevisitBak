<?
	include('../inc/_db_open.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	// 주민번호
	$mCode = $_POST['curMcode'];
	$mKind = $_POST['curMkind'];
	$mJumin = $_POST['jumin1'].$_POST['jumin2'];

	$conn->begin();

	// 일정을 삭제한다.
	$sql = "delete
			  from t01iljung
			 where t01_ccode = '$mCode'
			   and t01_mkind = '$mKind'
			   and t01_jumin = '$mJumin'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo '<script>alert("'.mysql_error().'"); history.back();</script>';
		exit;
	}

	// 수급자 데이타를 삭제한다.
	$sql = "delete
			  from m03sugupja
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'
			   and m03_jumin = '$mJumin'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo '<script>alert("'.mysql_error().'"); history.back();</script>';
		exit;
	}

	$conn->commit();

	include('../inc/_db_close.php');

	echo '<script>location.replace("sugupja.php?gubun=search");</script>';
?>
<?
	include('../inc/_db_open.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	// 주민번호
	$mCode = $_POST['curMcode'];
	$mKind = $_POST['curMkind'];
	$mJumin = $_POST['yJumin1'].$_POST['yJumin2'];

	$conn->begin();

	// 일정을 삭제한다.
	$sql = "delete
			  from t01iljung
			 where t01_ccode = '$mCode'
			   and t01_mkind = '$mKind'
			   and t01_yoyangsa_id1 = '$mJumin'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo '<script>alert("'.mysql_error().'"); history.back();</script>';
		exit;
	}

	// 수급자 데이타를 삭제한다.
	$sql = "delete
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_mkind = '$mKind'
			   and m02_yjumin = '$mJumin'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo '<script>alert("'.mysql_error().'"); history.back();</script>';
		exit;
	}

	$conn->commit();

	include('../inc/_db_close.php');

	echo '<script>location.replace("sugupja.php?gubun=search");</script>';
?>
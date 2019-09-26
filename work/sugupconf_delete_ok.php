<?
	include('../inc/_db_open.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYear  = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];
	$mRate  = $_POST['mRate'];

	$mPayDate = $mYear.$mMonth;

	$conn->begin();

	$sql = "delete"
		 . "  from t13sugupja"
		 . " where t13_ccode    = '".$mCode
		 . "'  and t13_mkind    = '".$mKind
		 . "'  and t13_pay_date = '".$mPayDate
		 . "'";
	if (!$conn->query($sql)){
		echo mysql_error();
		echo '<script>alert("확정 처리중 오류가 발생하였습니다."); history.back();</script>';
		$conn->rollback();
		exit;
	}
	
	$conn->commit();
	
	include('../inc/_db_close.php');

	echo "<script>location.replace('sugupconf.php?mCode=".$_PARAM["mCode"]."&mKind=".$_PARAM["mKind"]."&mYear=".$_PARAM["mYear"]."&mMonth=".$_PARAM["mMonth"]."');</script>";
?>
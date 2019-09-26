<?
	include("../inc/_db_open.php");

	$conn->begin();
	$sql = "update t01iljung"
		 . "   set t01_del_yn = 'Y'"
		 . " where t01_ccode        = '".$_GET["mCode"]
		 . "'  and t01_mkind        = '".$_GET["mKind"]
		 . "'  and t01_jumin        = '".$_GET["mJumin"]
		 . "'  and t01_sugup_date   = '".$_GET["mDate"]
		 . "'  and t01_sugup_fmtime = '".$_GET["mFmTime"]
		 . "'  and t01_sugup_seq    = '".$_GET["mSeq"]
		 . "'";
	if ($conn->query($sql)){
		$conn->commit();
		$request = 'Y';
	}else{
		$conn->rollback();
		$request = mysql_error();
	}
	
	include("../inc/_db_close.php");

	echo $request;
?>
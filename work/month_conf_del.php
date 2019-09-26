<?
	include('../inc/_db_open.php');
	include('../inc/_ed.php');

	$con2 = new connection();

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYear  = $_POST['confYear'];
	$mMonth = $_POST['confMonth'];
	$mSugupja = $ed->de($_POST['mSugupja']);
	$mPayDate = $mYear.$mMonth;

	$conn->begin();
	
	/*
	$sql = "select t01_ccode"
		 . ",      t01_mkind"
		 . ",      t01_jumin"
		 . ",      t01_sugup_date"
		 . ",      t01_sugup_fmtime"
		 . ",      t01_sugup_seq"
		 . ",      t01_suga_code1"
		 . ",      t01_suga"
		 . "  from t01iljung"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mSugupja
		 . "'  and left(t01_sugup_date, 6) = '".$mPayDate
		 . "'  and t01_modify_yn = 'Y'";
	#echo $sql.'<br>';
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$sql = "update t01iljung"
			 . "   set t01_conf_date       = null"
			 . " ,     t01_conf_fmtime     = null"
			 . " ,     t01_conf_totime     = null"
			 . " ,     t01_conf_soyotime   = null"
			 . " ,     t01_conf_suga_code  = '".$row['t01_suga_code1']
			 . "',     t01_conf_suga_value = '".$row['t01_suga']
			 . "',     t01_modify_yn       = 'N'"
			 . " where t01_ccode = '".$row['t01_ccode']
			 . "'  and t01_mkind = '".$row['t01_mkind']
			 . "'  and t01_jumin = '".$row['t01_jumin']
			 . "'  and t01_sugup_date   = '".$row['t01_sugup_date']
			 . "'  and t01_sugup_fmtime = '".$row['t01_sugup_fmtime']
			 . "'  and t01_sugup_seq    = '".$row['t01_sugup_seq']
			 . "'";
		#echo $sql.'<br>';
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script>alert("데이타 삭제중 오류가 발생하였습니다."); history.back();</script>';
			exit;
		}
	}
	$conn->row_free();
	*/

	$sql = "delete"
		 . "  from t13sugupja"
		 . " where t13_ccode    = '".$mCode
		 . "'  and t13_mkind    = '".$mKind
		 . "'  and t13_jumin    = '".$mSugupja
		 . "'  and t13_pay_date = '".$mPayDate
		 . "'";
	if (!$conn->query($sql)){
		$conn->rollback();
		echo '<script>alert("확정 처리 삭제중 오류가 발생하였습니다."); history.back();</script>';
		exit;
	}
	$conn->commit();
	
	include('../inc/_db_close.php');
?>
<form name="f" method="post">
<input name="curYear" type="hidden" value="">
<input name="curMonth" type="hidden" value="">
<input name="curMcode" type="hidden" value="">
<input name="curMkind" type="hidden" value="">
<input name="curSugupja" type="hidden" value="">
</form>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
	goMonthConfSugupja("<?=$_POST['confYear'];?>", "<?=$_POST['confMonth'];?>", "<?=$_POST['mCode'];?>", "<?=$_POST['mKind'];?>", "<?=$_POST['mSugupja'];?>")
</script>
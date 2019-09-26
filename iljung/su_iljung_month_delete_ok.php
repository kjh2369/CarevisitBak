<?
	include("../inc/_header.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mKey  = $_POST['mKey'];
	$mJumin    = $_POST['mJuminNo'];
	$mCalYear  = $_POST['calYear'];
	$mCalMonth = $_POST['calMonth'];
	$mCalDate  = $mCalYear.$mCalMonth;
	$contDate = $_POST['contDate'];
	$flagHistory = $_POST['flagHistory'];

	$mToday = date('Ymd', mkTime());

	if ($flagHistory == 'Y'){
		if ($contDate == $mCalDate){
			$mToday = '';
		}
	}
	
	$conn->begin();

	/*
	$sql = 'delete'
		 . '  from t01iljung'
		 . ' where t01_ccode = \''.$mCode
		 . '\' and t01_mkind = \''.$mKind
		 . '\' and t01_jumin = \''.$mJumin
		 . '\' and left(t01_sugup_date, 6) = \''.$mCalDate
		 . '\' and t01_sugup_date >= \''.$mToday.'\''
		 . '   and t01_status_gbn = \'9\''
		 . '   and t01_trans_yn = \'N\'';
	*/

	$sql = "update t01iljung"
		 . "   set t01_del_yn = 'Y'"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mJumin
		 . "'  and left(t01_sugup_date, 6) = '".$mCalDate
		 . "'";
	
	if ($mToday != ''){
		$sql .= " and t01_sugup_date >= '".$mToday."'";
	}
	$sql .= "   and t01_status_gbn in ('9', '0')"
		 .  "   and t01_trans_yn   = 'N'";

	if (!$conn->query($sql)){
		$conn->rollback();
		echo "<script>
				alert('데이타 저장 중 오류가 발생하였습니다.');
				history.back();
			  </script>";
		exit;
	}
	
	$conn->commit();

	include("../inc/_footer.php");
?>
<form name="f" method="post" action="su_reg.php">
	<input name="mCode" type="hidden" value="<?=$mCode;?>">
	<input name="mKind" type="hidden" value="<?=$mKind;?>">
	<input name="mKey"  type="hidden" value="<?=$mKey;?>">
	<input name="calYear"  type="hidden" value="<?=$mCalYear;?>">
	<input name="calMonth" type="hidden" value="<?=$mCalMonth;?>">
</form>
<script>
	document.f.submit();
</script>
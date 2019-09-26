<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("iljung_f.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$mode = $myF->get_iljung_mode();

	if ($mode == 0){
		$code	= $_POST['mCode'];
		$kind	= $_POST['mKind'];
		$key	= $_POST['mKey'];
		$jumin	= $_POST['mJuminNo'];
		$action = 'su_reg.php';
	}else{
		$code	= $_POST['code'];
		$kind	= $_POST['kind'];
		$key	= $_POST['key'];
		$jumin	= $ed->de($_POST['jumin']);
		$action = 'iljung_reg.php';
	}

	$year = $_POST['calYear'];
	$month = $_POST['calMonth'];

	$conn->begin();

	$sql = "update t01iljung
	           set t01_del_yn        = 'Y'
			 where t01_ccode         = '$code'
			   and t01_jumin         = '$jumin'
			   and t01_sugup_date like '$year$month%'
			   and t01_status_gbn in  ('0','9')";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	if (!f_voucher_usetime($conn, $code, $jumin, $year, $month)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	$conn->commit();

	include_once("../inc/_footer.php");
?>
<form name="f" method="post" action="<?=$action;?>">
	<input name="mCode" type="hidden" value="<?=$code;?>">
	<input name="mKind" type="hidden" value="<?=$kind;?>">
	<input name="mKey"  type="hidden" value="<?=$key;?>">
	<input name="calYear"  type="hidden" value="<?=$year;?>">
	<input name="calMonth" type="hidden" value="<?=$month;?>">
</form>
<script>
	document.f.submit();
</script>
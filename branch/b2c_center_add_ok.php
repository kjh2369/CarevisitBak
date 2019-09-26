<?
	include_once("../inc/_header.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$center    = $_POST[centerCode];
	$kind      = $_POST[centerKind];
	$branch    = $_POST[branchCode];
	$person    = str_replace($_POST[branchCode], '', $_POST[personCode]);
	$date      = date('Ymd', mkTime());
	$startDate = str_replace('-', '', $_POST[startDate]);
	$contDate  = str_replace('-', '', $_POST[contDate]);
	$other     = addSlashes($_POST[other]);
	$homeCareYN = ($_POST['homeCareYN'] == 'Y' ? 'Y' : 'N');
	#$voucherYN  = ($_POST['voucherYN'] == 'Y' ? 'Y' : 'N');

	$voucherYN  = '';
	$voucherYN .= ($_POST['vouNurseYN'] == 'Y' ? 'Y' : 'N');
	$voucherYN .= ($_POST['vouOldYN'] == 'Y' ? 'Y' : 'N');
	$voucherYN .= ($_POST['vouBabyYN'] == 'Y' ? 'Y' : 'N');
	$voucherYN .= ($_POST['vouDisYN'] == 'Y' ? 'Y' : 'N');

	$sql = "replace into b02center values (
			 '$center'
			,'$kind'
			,'$branch'
			,'$person'
			,'$date'
			,'$other'
			,'$homeCareYN'
			,'$voucherYN')";

	if (!$conn->execute($sql)){
		echo "
			<script>
				alert('기관과 지사담당자 연결중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				history.back();
			</script>
			 ";
		exit;
	}

	$sql = "update m00center
			   set m00_start_date = '$startDate'
			,      m00_cont_date  = '$contDate'
			 where m00_mcode      = '$center'
			   and m00_mkind      = '$kind'";
	$conn->execute($sql);

	include_once("../inc/_db_close.php");
?>
<script>
	alert('정상적으로 처리되었습니다.');
	window.self.close();
</script>
<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);
	$ent_dt	= $ed->de($_POST['ent_dt']);
	$ent_seq= $ed->de($_POST['ent_seq']);
	$memo	= date('Y-m-d H:i:s', mktime()).' | '.$_SESSION['userCode'];

	// 배치시작시간기록
	include('../inc/_batch_var.php');

	$conn->begin();

	$sql = "update unpaid_deposit
			   set del_flag = 'Y'
			,      del_memo = '$memo'
			 where org_no         = '$code'
			   and deposit_ent_dt = '$ent_dt'
			   and deposit_seq    = '$ent_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수입금 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$sql = "delete
			  from unpaid_deposit_list
			 where org_no         = '$code'
			   and deposit_ent_dt = '$ent_dt'
			   and deposit_seq    = '$ent_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수입금 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$sql = "update center_income
			   set del_flag = 'Y'
			 where org_no         = '$code'
			   and income_ent_dt  = '$ent_dt'
			   and deposit_seq    = '$ent_seq'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		$conn->batch_log('99', date('Ym', mktime()), $batch_start_sec, $start_dt, $start_tm, '미수입금 삭제중 오류발생');
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$conn->commit();

	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');
	location.replace('deposit_day_list.php?code=<?=$code;?>&kind=<?=$kind;?>&year=<?=$year;?>&month=<?=$month;?>');
</script>
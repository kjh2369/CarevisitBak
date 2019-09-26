<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$is_path = $_POST['counsel_path'] != '' ? $_POST['counsel_path'] : 'counsel';

	/*
	 * write_mode
	 * - 1 : 등록
	 * - 2 : 수정
	 */

	#if ($debug) $conn->mode = 2;

	$conn->begin();

	include_once('client_counsel_save_sub.php');

	$conn->commit();

	include_once("../inc/_db_close.php");

	if ($conn->mode != 1) exit;

if($is_path == 'reportNew'){ ?>
	<script>
		alert('<?=$myF->message("ok","N");?>');
		location.replace('../reportNew/report_body.php?gbnCd=<?=$gbnCd?>&mode=reg&report_id=CLTBR');
	</script><?
}else {?>
	<script>
		alert('<?=$myF->message("ok","N");?>');
		location.replace('client_counsel_reg.php?code=<?=$code;?>&kind=<?=$kind;?>&counsel_dt=<?=$counsel_dt;?>&counsel_seq=<?=$counsel_seq;?>');
	</script><?
} ?>
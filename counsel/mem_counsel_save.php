<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	/*
	 * counsel_mode
	 * - 1 : 등록
	 * - 2 : 수정
	 */

	include_once("mem_counsel_save_sub.php");
	include_once("../inc/_db_close.php");
?>
<script>
	alert('<?=$myF->message("ok","N");?>');

	if ('<?=$counsel_path;?>' == 'counsel'){
		location.replace('mem_counsel_reg.php?code=<?=$code;?>&kind=<?=$kind;?>&ssn=<?=$ed->en($ssn);?>');
	}else{
		location.replace('../yoyangsa/mem_reg.php?code=<?=$code;?>&jumin=<?=$ed->en($ssn);?>');
	}
</script>
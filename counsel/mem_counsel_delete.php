<?php
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	#echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);
	$type_mode = $_POST['type_mode'];

	$conn->begin();

	$sql = "update counsel_mem
			   set del_flag = 'Y'
			 where org_no   = '$code'
			   and mem_ssn  = '$ssn'";

	if ($conn->execute($sql)){
		$conn->commit();
		$result = 'OK';
	}else{
		$conn->rollback();
		$result = 'FAILE';
	}

	include_once("../inc/_db_close.php");

	if($type_mode != 'del'){
		echo $result;
	}else {
?>		
		<script>
			location.replace('mem_counsel.php');
		</script><?
	}	
?>
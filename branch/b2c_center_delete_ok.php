<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$check = $_POST['check'];
	$count = sizeOf($check);

	for($i=0; $i<$count; $i++){
		$code = explode('_', $check[$i]);

		$sql = "delete
				  from b02center
				 where b02_center = '".$code[0]."'
				   and b02_kind   = '".$code[1]."'";
		$conn->execute($sql);
	}

	include_once("../inc/_db_close.php");
?>
<script>
	location.replace('branch2center.php?menuIndex=13&menuSeq=5');
</script>
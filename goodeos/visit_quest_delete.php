<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$page	= $_POST['page'];
	$check = $_POST['check'];
	$count = sizeOf($check);
	
	for($i=0; $i<$count; $i++){
		$id = explode('_', $check[$i]);

	
		$sql = "delete
				  from counsel
				 where c_id = '$id[0]'";
		$conn->execute($sql);
	
	}

?>
<script>
	location.replace('visit_quest_list.php?page=<?=page;?>');
</script>


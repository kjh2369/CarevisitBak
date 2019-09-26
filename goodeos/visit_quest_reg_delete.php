<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$id		= $_POST['id'];
	$page	= $_POST['page'];

	$sql = "delete
			  from counsel
			 where c_id = '$id'";
	$conn->execute($sql);
	$conn->close();

?>
<script>
	location.replace('visit_quest_list.php?page=<?=page;?>');
</script>
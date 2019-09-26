<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$id		= $_POST['id'];
	$page	= $_POST['page'];

	$sql = 'delete
			  from tbl_goodeos_notice
			 where id = \''.$id.'\'';
	$conn->execute($sql);

	$sql = 'delete
			  from popup_notice
			 where notice_id = \''.$id.'\'';

	$conn->execute($sql);

	$conn->close();
?>
<script>
	location.replace('notice_list.php?page=<?=page;?>');
</script>
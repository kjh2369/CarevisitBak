<?
	include_once('../inc/_http_referer.php');

	$file = $_POST['file'];

	if (is_file($file)){
		unlink($file);
	}
?>
<script language='javascript'>
	self.close();
</script>
<?
	if ($_SERVER["HTTP_REFERER"] == ""){
		echo "
			<script>
				top.location.replace('../index.html');
			</script>
			";
		include("../inc/_db_close.php");
		exit;
	}
?>
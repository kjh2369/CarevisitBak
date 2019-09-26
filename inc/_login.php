<?
	if (!isset($_SESSION['userCode']) || $_SESSION['userCode'] == ''){
		echo "
			<script>
				top.location.replace('../index.html');
			</script>
			";
		exit;
	}
?>
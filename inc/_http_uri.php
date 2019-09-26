<?
	$urlPage = explode("/",$_SERVER["REQUEST_URI"]);

	if ($urlPage[sizeOf($urlPage)-1] != "main.php"){
		if (!isset($_SESSION["userCode"])){
			echo "
				<script>
					top.location.replace('../index.html');
				</script>
				 ";
			exit;
		}
	}

	if ($_SERVER["HTTP_REFERER"] == ""){
		echo "
			<script>
				top.location.replace('../index.html');
			</script>
			";
		exit;
	}
?>
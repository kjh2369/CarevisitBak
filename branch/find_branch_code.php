<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");

	$branch = $_POST['branch'];
	$count  = 0;

	if ($branch != ''){
		$sql = "select count(*)
				  from b00branch
				 where b00_code = '$branch'";
		$count = $conn->get_data($sql);
	}
	echo $count;

	include_once("../inc/_db_close.php");
?>
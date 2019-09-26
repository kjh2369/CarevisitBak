<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");

	$branch = $_POST['branch'];

	if ($branch != ''){
		$sql = "select ifnull(right(concat('00', cast(cast(max(b01_code) as unsigned) + 1 as char)), 3), '001')
				  from b01person
				 where b01_branch = '$branch'";
		$person = $conn->get_data($sql);
	}else{
		$person = '';
	}
	echo $person;

	include_once("../inc/_db_close.php");
?>
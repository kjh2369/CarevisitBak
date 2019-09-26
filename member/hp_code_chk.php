
<?
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	$code = $_GET['code'];

	$sql = ' select count(*)
		       from m00center
			  where m00_mcode = \''.$code.'\'';
	$cnt = $conn -> get_data($sql);

	if($cnt > 1){
		echo 1;
	}else {
		echo 9;
	}

?>
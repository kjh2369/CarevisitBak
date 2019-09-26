<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$branch = $_POST['branch'];

	$sql = "select concat(b01_branch, b01_code) as code
			,      b01_name as name
			  from b01person
			 where b01_branch = '$branch'
			 order by b01_name";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	echo '<select name="personCode" style="width:auto;">';
	echo '<option value="">--</option>';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		echo '<option value="'.$row['code'].'">'.$row['name'].'</option>';
	}

	echo '</select>';

	$conn->row_free();

	include_once("../inc/_db_close.php");
?>
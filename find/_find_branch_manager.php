<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$branch = $_POST['branch'];

	if (Empty($branch)){
		exit;
	}

	$sql = 'SELECT CONCAT(b01_branch, b01_code) AS code
			,      b01_name AS name
			  FROM b01person
			 WHERE b01_branch = \''.$branch.'\'
			 ORDER BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['code'].chr(2)
			  .  $row['name'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
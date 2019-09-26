<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_nhcs_db.php');
	


	$sql = ' select medical_org_no as code
			 ,	    medical_org_name as name
			   from medical_org
			  where del_flag = \'N\'
			  order by medical_org_name';
	

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($pageCnt + ($i + 1)).chr(2)
			  .  $row['code'].chr(2)
			  .  $row['name'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
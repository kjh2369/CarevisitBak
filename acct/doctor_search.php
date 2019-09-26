<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_nhcs_db.php');
	

	$sql = ' select doctor_licence_no as code
			 ,	    doctor_name as name
			 ,	    medical_off_name as off_name
			   from doctor
			  inner join medical_office_cd
			     on medical_off_cd = spc_subject
			  where del_flag = \'N\'
			  order by doctor_name';
	

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($pageCnt + ($i + 1)).chr(2)
			  .  $row['code'].chr(2)
			  .  $row['name'].chr(2)
			  .  $row['off_name'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
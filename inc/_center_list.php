<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	$find_branch = $_POST['find_branch'];
	$find_center = $_POST['find_center'];

	$sql = "select m00_mcode, m00_mkind, m00_store_nm, m00_mname
			  from m00center
			 inner join b02center
			    on b02_center = m00_mcode
			   and b02_kind   = m00_mkind
			   and b02_branch = '$find_branch'
			 where m00_domain = '$gDomain'
			   and m00_del_yn = 'N'
			 group by m00_mcode
			 order by m00_store_nm";

	$conn->fetch_type = 'assoc';
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	echo '<select name=\'find_center\' style=\'width:auto;\' onchange=\'set_dept();\'>';
	echo '<option value=\'\'>-선택하여 주십시오-</option>';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		echo '<option value=\''.$row['m00_mcode'].'\' '.($find_center == $row['m00_mcode'] ? 'selected' : '').'>'.$row['m00_store_nm'].'</option>';
	}

	echo '</selct>';

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
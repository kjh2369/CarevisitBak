<?
	include_once('../inc/_db_open.php');

	$find_branch = $_POST['find_branch'];

	$sql = "select b00_code, b00_name, b00_manager
			  from b00branch
			 where b00_domain = '$gDomain'
			   and b00_com_yn = 'N'
			 order by b00_code";

	$conn->fetch_type = 'assoc';
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	echo '<select name=\'find_branch\' style=\'width:auto;\' onchange=\'set_center();\'>';
	echo '<option value=\'\'>-선택하여 주십시오-</option>';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		echo '<option value=\''.$row['b00_code'].'\' '.($find_branch == $row['b00_code'] ? 'selected' : '').'>'.$row['b00_name'].'['.$row['b00_manager'].']</option>';
	}

	echo '</selct>';

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
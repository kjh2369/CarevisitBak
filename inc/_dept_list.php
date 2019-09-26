<?
	include_once('../inc/_db_open.php');

	$find_center = $_POST['find_center'];
	$find_dept   = $_POST['find_dept'];

	$sql = "select dept_cd, dept_nm, order_seq
			  from dept
			 where org_no   = '$find_center'
			   and del_flag = 'N'
			 order by order_seq";

	$conn->fetch_type = 'assoc';
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	echo '<select name=\'find_dept\' style=\'width:auto;\'>';
	echo '<option value=\'\'>전체</option>';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		echo '<option value=\''.$row['dept_cd'].'\' '.($find_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
	}

	echo '</selct>';

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
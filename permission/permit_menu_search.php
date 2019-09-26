<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);

	$sql = 'SELECT	menu_id
			FROM	menu_permit
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		use_yn	= \'Y\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= '/'.$row['menu_id'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
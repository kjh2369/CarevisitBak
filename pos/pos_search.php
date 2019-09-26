<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	pos_cd
			,		pos_nm
			,		pos_seq
			FROM	mem_pos
			WHERE	org_no		= \''.$code.'\'
			AND		del_flag	= \'N\'
			ORDER	BY	pos_seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);

		$data	.= $row['pos_cd'].chr(2);
		$data	.= $row['pos_nm'].chr(2);
		$data	.= $row['pos_seq'].chr(2);
		$data	.= chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
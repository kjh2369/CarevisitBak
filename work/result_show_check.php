<?
	include_once('../inc/_db_open.php');

	$code	= $_SESSION['userCenterCode'];

	$sql = "select count(*)
			  from closing_result
			 where org_no          = '$code'
			   and closing_read_yn = 'N'";

	$count = $conn->get_data($sql);
	$count = intval($count);

	echo $count;

	include_once('../inc/_db_close.php');
?>
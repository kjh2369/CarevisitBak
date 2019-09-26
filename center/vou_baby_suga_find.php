<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$sugaCd = 'VM'.$type.'01';

	$sql = 'SELECT	service_cost AS cost
			,		service_from_dt AS from_dt
			,		service_to_dt AS to_dt
			FROM	suga_service
			WHERE	org_no = \''.$code.'\'
			AND		service_kind = \'3\'
			AND		service_code = \''.$sugaCd.'\'
			ORDER	BY from_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= 'cost='.$row['cost'];
		$data .= '&from='.$myF->dateStyle($row['from_dt'],'.');
		$data .= '&to='.$myF->dateStyle($row['to_dt'],'.');
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
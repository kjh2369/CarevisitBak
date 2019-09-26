<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	jumin
			,		m03_key AS cd
			,		m03_name AS nm
			FROM	client_his_svc
			INNER	JOIN m03sugupja
					ON   m03_ccode	= org_no
					AND  m03_mkind	= \'6\'
					AND  m03_jumin	= jumin
					AND  m03_del_yn	= \'N\'
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \''.$SR.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
			ORDER	BY nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($data) $data .= '?';

		$data .= 'cd='.$row['cd'];
		$data .= '&nm='.$row['nm'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
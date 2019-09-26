<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$date	= $_POST['date'];
	$SR		= $_POST['SR'];

	if (!$date) exit;

	$sql = 'SELECT	DISTINCT
					m02_key AS jumin
			,		m02_yname AS name
			FROM	m02yoyangsa
			INNER	JOIN	mem_option
					ON		mem_option.org_no		= m02_ccode
					AND		mem_option.mo_jumin		= m02_yjumin';

	if ($SR == 'S'){
		$sql .= '	AND		mem_option.support_yn	= \'Y\'';
	}else{
		$sql .= '	AND		mem_option.response_yn	= \'Y\'';
	}

	$sql .= '
			INNER	JOIN	mem_his
					ON		mem_his.org_no	 = m02_ccode
					AND		mem_his.jumin	 = m02_yjumin
					AND		mem_his.join_dt	<= \''.$date.'\'
					AND		IFNULL(mem_his.quit_dt,\'9999-12-31\') >= \''.$date.'\'
			WHERE	m02_ccode = \''.$orgNo.'\'
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$data = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= ($data ? '?' : '');
		$data .= 'key='.$ed->en($row['jumin']);
		$data .= '&name='.$row['name'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
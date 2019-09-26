<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$appNo = 'L'.$_POST['appno'];

	$sql = 'SELECT	jumin
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		app_no = \''.$appNo.'\'
			ORDER	BY from_dt
			LIMIT	1';

	$jumin = $conn->get_data($sql);

	if ($jumin){
		$sql = 'SELECT	m03_name
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'0\'
				AND		m03_jumin = \''.$jumin.'\'';

		$name = $conn->get_data($sql);

		echo 'jumin='.$ed->en($jumin).'&name='.$name;
	}else{
		echo 'Y';
	}

	include_once('../inc/_db_close.php');
?>
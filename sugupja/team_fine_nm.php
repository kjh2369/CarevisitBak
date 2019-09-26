<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= Date('Ym');

	$sql = 'SELECT	team_cd
			FROM	client_his_team
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$svcCd.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_ym	<= \''.$yymm.'\'
			AND		to_ym	>= \''.$yymm.'\'
			AND		del_flag = \'N\'';
	$cd = $conn->get_data($sql);

	$sql = 'SELECT	m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode	= \''.$orgNo.'\'
			AND		m02_mkind	= \''.$svcCd.'\'
			AND		m02_yjumin	= \''.$cd.'\'';
	echo $conn->get_data($sql);


	include_once("../inc/_db_close.php");
?>
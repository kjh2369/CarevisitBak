<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$code   = $_SESSION['userCenterCode'];
	$jumin  = $_POST['jumin'];
	$svcCd  = $_POST['svcCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($svcCd == 'A' || $svcCd == 'B' || $svcCd == 'C'){
		$conn->close();
		echo 1;
		exit;
	}

	$sql = 'select max(to_dt) as to_dt
			  from client_his_svc
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$jumin.'\'';

	if ($svcCd == '3')
		$sql .= ' and svc_cd != \'4\'';
	else if ($svcCd == '4')
		$sql .= ' and svc_cd != \'3\'';
	else if ($svcCd == '6')
		$sql .= ' and svc_cd = \'6\'';
	else if ($svcCd == 'S' || $svcCd == 'R')
		$sql .= ' and svc_cd = \''.$svcCd.'\'';
	else
		$sql .= ' and svc_cd != \'S\' and svc_cd != \'R\'';

	$lsDt = $conn->get_data($sql);
	$lsDt = $myF->dateAdd('day',1,$lsDt,'Y-m-d');

	echo $lsDt;

	include_once('../inc/_db_close.php');
?>
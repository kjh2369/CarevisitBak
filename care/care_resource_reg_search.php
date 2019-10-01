<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$svcCd	= $_POST['svc'];
	$careCd = $_POST['cd'];

	$sql = 'SELECT	care.suga_cd
			,		res.from_dt
			,		res.to_dt
			,		a.nm1, a.nm2, a.nm3
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS a
					ON		CONCAT(a.cd1, a.cd2, a.cd3) = care.suga_cd
			INNER	JOIN	care_resource AS res
					ON		res.org_no	= care.org_no
					AND		res.care_sr	= care.suga_sr
					AND		res.care_svc= care.suga_cd
					AND		res.care_cd	= \''.$careCd.'\'
					AND		res.del_flag= \'N\'
			WHERE	care.org_no	= \''.$code.'\'
			AND		care.suga_sr= \''.$sr.'\'
			AND		care.suga_cd= \''.$svcCd.'\'';

	$row = $conn->get_array($sql);

	if (!is_array($row)) exit;

	$data .= 'cd='.$row['suga_cd'];
	$data .= '&nm1='.$row['nm1'];
	$data .= '&nm2='.$row['nm2'];
	$data .= '&nm3='.$row['nm3'];
	$data .= '&from='.$row['from_dt'];
	$data .= '&to='.$row['to_dt'];

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>
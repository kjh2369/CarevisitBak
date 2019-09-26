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
			,		care.suga_sub
			,		care.suga_nm
			,		res.care_cost
			,		res.from_dt
			,		res.to_dt';

	if ($IsCareYoyAddon){
		//공통항목
		$sql .= '
			FROM	(	SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm
						FROM	care_suga
						WHERE	org_no	= \''.$code.'\'
						AND		suga_sr	= \''.$sr.'\'
						UNION	ALL
						SELECT	\''.$code.'\' AS org_no, \''.$sr.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name
						FROM	care_suga_comm
					) AS care';
	}else{
		$sql .= '
			FROM	care_suga AS care';
	}

	$sql .= '
			INNER	JOIN	care_resource AS res
					ON		res.org_no	= care.org_no
					AND		res.care_sr	= care.suga_sr
					AND		res.care_svc= care.suga_cd
					AND		res.care_sub= care.suga_sub
					AND		res.care_cd	= \''.$careCd.'\'
					AND		res.del_flag= \'N\'
			WHERE	care.org_no	= \''.$code.'\'
			AND		care.suga_sr= \''.$sr.'\'
			AND		CONCAT(care.suga_cd,care.suga_sub) = \''.$svcCd.'\'';

	$row = $conn->get_array($sql);

	if (!is_array($row)) exit;

	$data .= 'cd='.$row['suga_cd'].$row['suga_sub'];
	$data .= '&nm='.$row['suga_nm'];
	$data .= '&cost='.$row['care_cost'];
	$data .= '&from='.$row['from_dt'];
	$data .= '&to='.$row['to_dt'];

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>
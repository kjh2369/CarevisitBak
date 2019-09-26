<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];
	$svcCd = $_POST['svcCd'];

	if (Empty($code)){
		exit;
	}

	//보험사 리스트
	$sql = 'SELECT g01_code AS cd
			,      g01_name AS nm
			  FROM g01ins
			 ORDER BY g01_code';
	$laInsuMst = $conn->_fetch_array($sql,'cd');

	$sql = 'SELECT seq
			,      insu_cd
			,      secu_no
			,      pay
			,      from_dt
			,      to_dt
			  FROM insu_center
			 WHERE org_no = \''.$code.'\'
			   AND svc_cd = \''.$svcCd.'\'
			 ORDER BY seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= $row['seq'].chr(2)
			  .  $row['insu_cd'].chr(2)
			  .  $laInsuMst[$row['insu_cd']]['nm'].chr(2)
			  .  $row['secu_no'].chr(2)
			  .  $myF->dateStyle($row['from_dt'],'.').chr(2)
			  .  $myF->dateStyle($row['to_dt'],'.').chr(2)
			  .  $row['pay'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once("../inc/_db_close.php");
?>
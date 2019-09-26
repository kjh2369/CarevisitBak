<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];

	if (Empty($code)){
		exit;
	}

	$svcCd  = $_POST['svcCd'];
	$seq    = $_POST['seq'];
	$insuCd = $_POST['insuCd'];
	$secuNo = $_POST['secuNo'];
	$pay    = $_POST['pay'];
	$fromDt = $_POST['fromDt'];
	$toDt   = $_POST['toDt'];
	$re     = $_POST['re'];

	if ($re == 'true'){
		$seq ++;
	}

	if ($seq < 0) $seq = 1;

	$sql = 'SELECT COUNT(*)
			  FROM insu_center
			 WHERE org_no = \''.$code.'\'
			   AND svc_cd = \''.$svcCd.'\'
			   AND seq    = \''.$seq.'\'';
	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$sql = 'UPDATE insu_center
				   SET insu_cd   = \''.$insuCd.'\'
				,      secu_no   = \''.$secuNo.'\'
				,      pay       = \''.$pay.'\'
				,      from_dt   = \''.$fromDt.'\'
				,      to_dt     = \''.$toDt.'\'
				,      insert_dt = NOW()
				 WHERE org_no = \''.$code.'\'
				   AND svc_cd = \''.$svcCd.'\'
				   AND seq    = \''.$seq.'\'';
	}else{
		$sql = 'INSERT INTO insu_center (
				 org_no
				,svc_cd
				,seq
				,insu_cd
				,secu_no
				,pay
				,from_dt
				,to_dt
				,insert_dt) VALUES (
				 \''.$code.'\'
				,\''.$svcCd.'\'
				,\''.$seq.'\'
				,\''.$insuCd.'\'
				,\''.$secuNo.'\'
				,\''.$pay.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,NOW())';
	}

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once("../inc/_db_close.php");
?>
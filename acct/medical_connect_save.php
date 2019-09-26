<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_POST['lblCode'];
	$mdOrgNo	= $_POST['lblMdCode'];
	$mdOrgName	= $_POST['lblMdName'];
	$fromDt		= str_replace('-','', $_POST['fromDt']);
	$toDt		= str_replace('-','', $_POST['toDt']);
	$modi       = $_POST['modi'];
	
	//등록된 기관인지 검사
	$sql = 'SELECT	COUNT(*)
			FROM	medical_connect
			WHERE	org_no   = \''.$orgNo.'\'
			AND     medical_org_no = \''.$mdOrgNo.'\'
			AND		date_format(from_dt,\'%Y%m%d\') between \''.$fromDt.'\' and \''.$toDt.'\'
			OR		org_no = \''.$orgNo.'\'
			AND     medical_org_no = \''.$mdOrgNo.'\'
			AND		date_format(to_dt,\'%Y%m%d\') between \''.$fromDt.'\' and \''.$toDt.'\'
			AND     del_flag = \'N\'';
	$regCnt = $conn -> get_data($sql);
	
	/*
	$sql = 'SELECT	COUNT(*)
			FROM	medical_connect
			WHERE	org_no   = \''.$orgNo.'\'
			AND		from_dt = \''.$fromDt.'\'';
	$liCnt = $conn->get_data($sql);
	*/

	if ($modi == true){
		$new = false;
	}else{	
		$new = true;
		
		if ($regCnt > 0){
			echo 'reg_y'; exit;
		}
	}
	
	
	
	if ($new){
		/*
		$sql = 'SELECT ifnull(max(seq), 0) + 1
				  FROM medical_connect
				 WHERE org_no   = \''.$orgNo.'\'
				   AND medical_org_no = \''.$mdOrgNo.'\'
			       AND from_dt = \''.$fromDt.'\'
				   AND del_flag = \'N\'';

		$seq = $conn->get_data($sql);
		*/

		$sql = 'insert into medical_connect (
				 org_no
				,medical_org_no
				,medical_org_name
				,from_dt
				,insert_id
				,insert_dt) values (
				 \''.$orgNo.'\'
				,\''.$mdOrgNo.'\'
				,\''.$mdOrgName.'\'
				,\''.$fromDt.'\'
				,\''.$_SESSION['userCode'].'\'
				,\''.date('Y-m-d', mktime()).'\')';
		$query[SizeOf($query)] = $sql;
		
	}


	$sql = 'UPDATE medical_connect
			   SET medical_org_no   = \''.$mdOrgNo.'\'
			 ,	   medical_org_name = \''.$mdOrgName.'\'
			 ,	   to_dt			= \''.$toDt.'\'
			 ,     del_flag		    = \'N\'
			 WHERE org_no			= \''.$orgNo.'\'
			   AND from_dt		    = \''.$fromDt.'\'
			   AND medical_org_no   = \''.$mdOrgNo.'\'';
	
	$query[SizeOf($query)] = $sql;
	
	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			//echo nl2br($sql); exit;
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1; 

?>
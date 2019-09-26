<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_nhcs_db.php');

	$licenceNo	= $_POST['lblCode'];
	$mdOrgNo	= $_POST['lblMdCode'];
	$mdOrgName	= $_POST['lblMdName'];
	$fromDt		= str_replace('-','', $_POST['fromDt']);
	$toDt		= str_replace('-','', $_POST['toDt']);
	
	
	$sql = 'SELECT	COUNT(*)
			FROM	medical_org_doctor
			WHERE	doctor_licence_no   = \''.$licenceNo.'\'
			AND		cntrct_dt = \''.$fromDt.'\'';
	
	$liCnt = $conn->get_data($sql);
	
	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
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

		$sql = 'insert into medical_org_doctor (
				 doctor_licence_no
				,medical_org_no
				,cntrct_dt
				,insert_id
				,insert_dt) values (
				 \''.$licenceNo.'\'
				,\''.$mdOrgNo.'\'
				,\''.$fromDt.'\'
				,\''.$_SESSION['userCode'].'\'
				,\''.date('Y-m-d', mktime()).'\')';
	
		$query[SizeOf($query)] = $sql;
		
	}


	$sql = 'UPDATE medical_org_doctor
			   SET medical_org_no	    = \''.$mdOrgNo.'\'
			 ,	   retire_dt			= \''.$toDt.'\'
			 ,     del_flag				= \'N\'
			 WHERE doctor_licence_no 	= \''.$licenceNo.'\'
			   AND cntrct_dt			= \''.$fromDt.'\'';
	
	$query[SizeOf($query)] = $sql;
	
	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			echo nl2br($sql); exit;
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1; 

?>
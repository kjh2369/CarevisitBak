<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_nhcs_db.php');
	
	$licenceNo  = $_POST['licenceNo'];
	$mdOrgNo	= $_POST['mdOrgNo'];
	$fromDt		= $_POST['fromDt'];

	$sql = 'update medical_org_doctor
			   set del_flag				= \'Y\'
			 where doctor_licence_no	= \''.$licenceNo.'\'
			   and medical_org_no		= \''.$mdOrgNo.'\'
			   and cntrct_dt			= \''.$fromDt.'\'';
	
	$query[SizeOf($query)] = $sql;

	
	$conn->begin();
	
	foreach($query as $sql){
		
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1;

?>
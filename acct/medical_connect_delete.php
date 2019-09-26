<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$orgNo    = $_POST['orgNo'];
	$mdOrgNo  = $_POST['mdOrgNo'];
	$fromDt   = $_POST['fromDt'];

	$sql = 'update medical_connect
			   set del_flag			= \'Y\'
			 where org_no			= \''.$orgNo.'\'
			   and medical_org_no	= \''.$mdOrgNo.'\'
			   and from_dt          = \''.$fromDt.'\'';
	
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
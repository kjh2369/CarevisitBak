<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	

	$sql = 'update homepage_request
			   set del_flag			= \'Y\'
			 where org_no			= \''.$_POST['code'].'\'
			   and seq				= \''.$_POST['seq'].'\'';
	
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
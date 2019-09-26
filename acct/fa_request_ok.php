<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	
	$data	= Explode(chr(11),$_POST['request']);
	
	
	foreach($data as $tmp){
		
		if ($tmp){
			Parse_Str($tmp,$col);
				
			$sql = 'update seminar_request
					   set use_yn		= \''.$col['use_yn'].'\'
					 where org_no			= \''.$col['code'].'\'
					   and seq				= \''.$col['seq'].'\'';
			
			$query[SizeOf($query)] = $sql;
		}
		
	}
	
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
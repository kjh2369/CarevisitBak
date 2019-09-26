<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');

	$code		= $_SESSION['userCenterCode'];

	$sql = 'SELECT	count(*)
			FROM	report2014_request
			WHERE	org_no = \''.$code.'\'';

	$cnt = $conn->get_data($sql);
	
	if($cnt == 0){
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	report2014_request
				WHERE	org_no = \''.$code.'\'';

		$Seq = $conn->get_data($sql);

		$sql = 'INSERT INTO report2014_request(
				 org_no
				,seq
				,insert_id
				,insert_dt) VALUES (
				 \''.$code.'\'
				,\''.$Seq.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
		
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
	}else {
		echo 2;
	}
	

	include_once('../../inc/_db_close.php');

?>
<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');

	$popId		= $_POST['popId'];
	$selCode	= Explode('/',$_POST['selCode']);
	$fromDt		= $_POST['fromDt'];
	$toDt		= $_POST['toDt'];
	$contents	= $_POST['contents'];

	if (!is_array($selCode)){
		$conn->close();
		echo 9;
		exit;
	}

	if (!$popId){
		$sql = 'SELECT	IFNULL(MAX(pop_id), 0) + 1
				FROM	center_popup';

		$popId = $conn->get_data($sql);
	}else{
		$sql = 'DELETE
				FROM	center_popup
				WHERE	pop_id = \''.$popId.'\'';

		$query[] = $sql;
	}

	foreach($selCode as $idx => $orgNo){
		if (!$orgNo) continue;

		$sql = 'INSERT INTO center_popup VALUES (
				 \''.$popId.'\'
				,\''.$orgNo.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\'\'
				,\''.$contents.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';

		$query[] = $sql;
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

	include_once('../inc/_db_close.php');
?>
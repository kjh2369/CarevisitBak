<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//$company= $_POST['company'];
	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $year.$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	//삭제쿼리
	$sql = 'DELETE
			FROM	cv_svc_acct_list
			WHERE	yymm = \''.$yymm.'\'';

	if ($orgNo){
		$sql .= '
			AND		org_no = \''.$orgNo.'\'';
	}

	$query[] = $sql;


	//쿼리
	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '!!! '.$conn->error_msg.' !!!';
				 exit;
			}
		}

		$conn->commit();
	}


	Unset($svcList);
	Unset($centerList);

	include_once('../inc/_db_close.php');
?>
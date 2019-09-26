<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$yymm = str_replace('-', '', $_POST['yymm']);

	$sql = 'DELETE
			FROM	cv_claim_yymm';
	$query[] = $sql;

	$sql = 'INSERT INTO cv_claim_yymm (yymm) VALUES (\''.$yymm.'\')';
	$query[] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();

			if ($debug){
				echo $conn->error_msg.chr(13).chr(10).$conn->error_query.chr(13).chr(10);
			}else{
				echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
			}

			exit;
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
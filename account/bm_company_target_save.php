<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_compay_target
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		yymm		= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_compay_target
				SET		amt			= \''.str_replace(',','',$_POST['amt']).'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	domain_id	= \''.$gDomainID.'\'
				AND		yymm		= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_compay_target (domain_id,yymm,amt,insert_id,insert_dt) VALUES (
				 \''.$gDomainID.'\'
				,\''.$yymm.'\'
				,\''.str_replace(',','',$_POST['amt']).'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_compay
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		year		= \''.$year.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_compay
				SET		compay_'.$month.' = \''.str_replace(',','',$_POST['amt']).'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	domain_id	= \''.$gDomainID.'\'
				AND		year		= \''.$year.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_compay (domain_id,year,compay_'.$month.',insert_id,insert_dt) VALUES (
				 \''.$gDomainID.'\'
				,\''.$year.'\'
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
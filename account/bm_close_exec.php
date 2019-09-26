<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$close	= $_POST['close'];

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_close_yn
			WHERE	yymm = \''.$year.$month.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_close_yn
				SET		close_yn	= \''.$close.'\'
				,		close_dt	= DATE_FORMAT(NOW(),\'%Y%m%d\')
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	yymm		= \''.$year.$month.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_close_yn (
				 yymm
				,close_yn
				,close_dt
				,insert_id
				,insert_dt) VALUES (
				 \''.$year.$month.'\'
				,\''.$close.'\'
				,DATE_FORMAT(NOW(),\'%Y%m%d\')
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
	exit;

	include_once('../inc/_db_close.php');
?>
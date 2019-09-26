<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$popYn	= $_POST['popYn'];
	$popDay	= IntVal($_POST['popDay']);
	$stopYn	= $_POST['stopYn'];
	$stopDt	= str_replace('-','',$_POST['stopDt']);


	$sql = 'SELECT	COUNT(*)
			FROM	cv_acct_pop_set
			WHERE	acct_ym = \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	cv_acct_pop_set
				SET		pop_yn	= \''.$popYn.'\'
				,		pop_day	= \''.$popDay.'\'
				,		stop_yn	= \''.$stopYn.'\'
				,		stop_dt	= \''.$stopDt.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	acct_ym	= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO cv_acct_pop_set (acct_ym,pop_yn,pop_day,stop_yn,stop_dt,insert_id,insert_dt) VALUES (
				 \''.$yymm.'\'
				,\''.$popYn.'\'
				,\''.$popDay.'\'
				,\''.$stopYn.'\'
				,\''.$stopDt.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
	}

	include_once('../inc/_db_close.php');
?>
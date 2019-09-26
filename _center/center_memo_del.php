<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$memoType='1';
	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];

	$sql = 'UPDATE	cv_memo
			SET		del_flag = \'Y\'
			,		update_id = \''.$_SESSION['userCode'].'\'
			,		update_dt = NOW()
			WHERE	memo_type=\''.$memoType.'\'
			AND		org_no	= \''.$orgNo.'\'
			AND		seq		= \''.$seq.'\'';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo '데이타 처리중 오류가 발생하였습니다.';
	}

	include_once('../inc/_db_close.php');
?>
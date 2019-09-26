<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$memoType='1';
	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];
	$subject= addslashes($_POST['subject']);
	$contents=addslashes($_POST['contents']);
	$regNm	= $_POST['regNm'];

	if ($seq){
		$IsNew = false;
	}else{
		$IsNew = true;

		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	cv_memo
				WHERE	memo_type=\''.$memoType.'\'
				AND		org_no	= \''.$orgNo.'\'';
		$seq = $conn->get_data($sql);
	}

	if ($IsNew){
		$sql = 'INSERT INTO cv_memo VALUES (
				 \''.$memoType.'\'
				,\''.$orgNo.'\'
				,\''.$seq.'\'
				,\''.$subject.'\'
				,\''.$contents.'\'
				,\''.$regNm.'\'
				,\''.$_SERVER['REMOTE_ADDR'].'\'
				,\'0\', \'N\', \''.$_SESSION['userCode'].'\', NOW(), NULL, NULL)';
	}else{
		$sql = 'UPDATE	cv_memo
				SET		subject	= \''.$subject.'\'
				,		contents= \''.$contents.'\'
				,		reg_nm	= \''.$regNm.'\'
				,		mod_cnt = mod_cnt + 1
				,		update_id = \''.$_SESSION['userCode'].'\'
				,		update_dt = NOW()
				WHERE	memo_type=\''.$memoType.'\'
				AND		org_no	= \''.$orgNo.'\'
				AND		seq		= \''.$seq.'\'';
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		echo '데이타 처리중 오류가 발생하였습니다.';
	}

	include_once('../inc/_db_close.php');
?>
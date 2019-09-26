<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$issYm	= str_replace('-','',$_POST['issYm']);
	$issDt	= str_replace('-','',$_POST['issDt']);
	$crGbn	= $_POST['crGbn'];

	$sql = 'SELECT	COUNT(*)
			FROM	cv_tax_his
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym	= \''.$issYm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	cv_tax_his
				SET		iss_dt		= \''.$issDt.'\'
				,		cr_gbn		= \''.$crGbn.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		acct_ym	= \''.$issYm.'\'';
	}else{
		$sql = 'INSERT INTO cv_tax_his (org_no,acct_ym,iss_dt,cr_gbn,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$issYm.'\'
				,\''.$issDt.'\'
				,\''.$crGbn.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
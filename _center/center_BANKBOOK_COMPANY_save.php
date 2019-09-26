<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$bankId		= $_POST['bankId'];
	$bankNm		= $_POST['bankNm'];
	$bankNo		= $_POST['bankNo'];
	$bankAcct	= $_POST['bankAcct'];
	$bankGbn	= $_POST['bankGbn'];

	if ($bankId){
		$sql = 'UPDATE	cv_bankbook
				SET		bank_no		= \''.$bankNo.'\'
				,		bank_nm		= \''.$bankNm.'\'
				,		bank_acct	= \''.$bankAcct.'\'
				,		bank_gbn	= \''.$bankGbn.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	bank_id		= \''.$bankId.'\'';
	}else{
		$sql = 'SELECT	IFNULL(MAX(bank_id),0)+1
				FROM	cv_bankbook';
		$bankId = $conn->get_data($sql);

		$sql = 'INSERT INTO cv_bankbook (bank_id,bank_no,bank_nm,bank_acct,bank_gbn,insert_id,insert_dt) VALUES (
				 \''.$bankId.'\'
				,\''.$bankNo.'\'
				,\''.$bankNm.'\'
				,\''.$bankAcct.'\'
				,\''.$bankGbn.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
	}

	include_once('../inc/_db_close.php');
?>
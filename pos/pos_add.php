<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$posCd	= $_POST['cd'];
	$posNm	= $_POST['pos'];
	$posSeq	= $_POST['seq'];

	$sql = 'UPDATE	mem_pos
			SET		pos_seq	= pos_seq + 1
			WHERE	org_no	= \''.$code.'\'
			AND		pos_seq > \''.$posSeq.'\'';

	$conn->execute($sql);

	if (Empty($posCd)){
		$sql = 'SELECT	MAX(pos_cd)
				FROM	mem_pos
				WHERE	org_no	= \''.$code.'\'';

		$posCd	= IntVal($conn->get_data($sql)) + 1;
		$posCd	= SubStr($posCd,StrLen($posCd) - 2,StrLen($posCd));

		$sql = 'INSERT INTO mem_pos (
				 org_no
				,pos_cd
				,pos_nm
				,pos_seq) VALUES (
				 \''.$code.'\'
				,\''.$posCd.'\'
				,\''.$posNm.'\'
				,\''.$posSeq.'\'
				)';
	}else{
		$sql = 'UPDATE	mem_pos
				SET		pos_nm	= \''.$posNm.'\'
				,		pos_seq = \''.$posSeq.'\'
				WHERE	org_no	= \''.$code.'\'
				AND		pos_cd	= \''.$posCd.'\'';
	}

	$conn->execute($sql);

	include_once('../inc/_db_close.php');
?>
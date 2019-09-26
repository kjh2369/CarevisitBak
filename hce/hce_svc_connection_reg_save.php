<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	서비스 연계 및 의뢰서 등록
	 *********************************************************/
	$connSeq	= $_POST['connSeq'];
	$connOrgNo	= $_POST['txtConnNo'];
	$connOrgNm	= $_POST['txtConnNm'];
	$perNm		= $_POST['txtPer'];
	$perJumin	= $ed->de($_POST['perJumin']);
	$reqDt		= Str_Replace('-','',$_POST['txtReqDt']);
	$reqor		= $_POST['txtReqor'];
	$reqRel		= $_POST['cboReqRel'];
	$reqRsn		= AddSlashes($_POST['txtReqRsn']);
	$reqText	= AddSlashes($_POST['txtReqText']);

	if (Empty($connSeq)) $connSeq = '1';

	$sql = 'SELECT	COUNT(*)
			FROM	hce_svc_connect
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		conn_seq= \''.$connSeq.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	if ($new){
		$sql = 'INSERT INTO hce_svc_connect (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,conn_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$connSeq.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	hce_svc_connect
			SET		conn_orgno	= \''.$connOrgNo.'\'
			,		conn_orgnm	= \''.$connOrgNm.'\'
			,		per_nm		= \''.$perNm.'\'
			,		per_jumin	= \''.$perJumin.'\'
			,		req_dt		= \''.$reqDt.'\'
			,		reqor_nm	= \''.$reqor.'\'
			,		reqor_rel	= \''.$reqRel.'\'
			,		req_rsn		= \''.$reqRsn.'\'
			,		req_text	= \''.$reqText.'\'';

	if ($new){
		$sql .= '
			,		insert_id		= \''.$userCd.'\'
			,		insert_dt		= NOW()';
	}else{
		$sql .= '
			,		update_id		= \''.$userCd.'\'
			,		update_dt		= NOW()';
	}

	$sql .= '
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		conn_seq= \''.$connSeq.'\'';

	$query[SizeOf($query)] = $sql;

	$sql = 'UPDATE	hce_proc
			SET		conn_dt	= \''.$reqDt.'\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$query[SizeOf($query)] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
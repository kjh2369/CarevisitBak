<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	과정상담 등록
	 *********************************************************/
	$procSeq		= $_POST['procSeq'];
	$counselDt		= Str_Replace('-','',$_POST['txtProcDt']);
	$counselNm		= $_POST['txtCounsel'];
	$counselJumin	= $ed->de($_POST['counselJumin']);
	$counselGbn		= $_POST['optCounselGbn'];
	$counselText	= AddSlashes($_POST['txtCounselText']);
	$counselRemark	= AddSlashes($_POST['txtRemark']);

	if (Empty($procSeq)) $procSeq = '1';

	$sql = 'SELECT	COUNT(*)
			FROM	hce_proc_counsel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		proc_seq= \''.$procSeq.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	if ($new){
		$sql = 'INSERT INTO hce_proc_counsel (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,proc_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$procSeq.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	hce_proc_counsel
			SET		counsel_dt		= \''.$counselDt.'\'
			,		counsel_nm		= \''.$counselNm.'\'
			,		counsel_jumin	= \''.$counselJumin.'\'
			,		counsel_gbn		= \''.$counselGbn.'\'
			,		counsel_text	= \''.$counselText.'\'
			,		counsel_remark	= \''.$counselRemark.'\'';

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
			AND		proc_seq= \''.$procSeq.'\'';

	$query[SizeOf($query)] = $sql;

	$sql = 'UPDATE	hce_proc
			SET		cusl_dt	= \''.$counselDt.'\'
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
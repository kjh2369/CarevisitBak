<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	재사정기록 저장
	 *********************************************************/
	$isptSeq	= $_POST['isptSeq'];

	if (Empty($isptSeq)) $isptSeq = '1';

	$sql = 'SELECT	COUNT(*)
			FROM	hce_re_ispt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	if ($new){
		$sql = 'INSERT INTO hce_re_ispt (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,ispt_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$isptSeq.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	hce_re_ispt
			SET		ispt_dt				= \''.Str_Replace('-','',$_POST['txtIsptDt']).'\'
			,		per_nm				= \''.$_POST['txtPer'].'\'
			,		per_jumin			= \''.$ed->de($_POST['perJumin']).'\'
			,		ispt_gbn			= \''.$_POST['optIsptGbn'].'\'
			,		ispt_rsn			= \''.$_POST['optIsptRsn'].'\'
			,		client_need_change	= \''.AddSlashes($_POST['txtNeedChange']).'\'
			,		svc_offer_problem	= \''.AddSlashes($_POST['txtSvcOfferProblem']).'\'
			,		wer_opion			= \''.AddSlashes($_POST['txtWerOpion']).'\'
			,		ispt_rst			= \''.$_POST['optIsptRstGbn'].'\'
			,		after_plan			= \''.AddSlashes($_POST['txtAfterPlan']).'\'';

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
			AND		ispt_seq= \''.$isptSeq.'\'';

	$query[SizeOf($query)] = $sql;


	$sql = 'UPDATE	hce_proc
			SET		rest_dt	= \''.Str_Replace('-','',$_POST['txtIsptDt']).'\'
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
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$evlSeq = $_POST['evlSeq'];

	if ($evlSeq){
		$new = false;
	}else{
		$new = true;
	}

	if ($new){
		$sql = 'SELECT	IFNULL(MAX(evl_seq),0)+1
				FROM	hce_provide_evl
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';

		$evlSeq = $conn->get_data($sql);

		$sql = 'INSERT INTO hce_provide_evl (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,evl_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$evlSeq.'\'
				)';

		$query[] = $sql;
	}

	$sql = 'UPDATE	hce_provide_evl
			SET		evl_dt		= \''.str_replace('-','',$_POST['txtPEDt']).'\'
			,		evl_cd		= \''.$ed->de($_POST['mgerJumin']).'\'
			,		svc_cont	= \''.AddSlashes($_POST['txtSvcCont']).'\'
			,		evl_cont	= \''.AddSlashes($_POST['txtEvlCont']).'\'
			,		after_plan	= \''.AddSlashes($_POST['txtAfterPlan']).'\'';

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
			AND		evl_seq	= \''.$evlSeq.'\'';

	$query[] = $sql;

	$sql = 'SELECT	MAX(evl_dt)
			FROM	hce_provide_evl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$evlDt = $conn->get_data($sql);

	if ($evlDt < str_replace('-','',$_POST['txtPEDt'])) $evlDt = str_replace('-','',$_POST['txtPEDt']);

	$sql = 'UPDATE	hce_proc
			SET		prvev_dt= \''.$evlDt.'\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$query[] = $sql;

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
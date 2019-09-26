<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	사례평가서 저장
	 *********************************************************/
	$sql = 'SELECT	COUNT(*)
			FROM	hce_evaluation
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$liCnt	= $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	/*
	$procSeq		= $_POST['procSeq'];
	$counselDt		= Str_Replace('-','',$_POST['txtProcDt']);
	$counselNm		= $_POST['txtCounsel'];
	$counselJumin	= $ed->de($_POST['counselJumin']);
	$counselGbn		= $_POST['optCounselGbn'];
	$counselText	= AddSlashes($_POST['txtCounselText']);
	$counselRemark	= AddSlashes($_POST['txtRemark']);
	*/

	if ($new){
		$sql = 'INSERT INTO hce_evaluation (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	hce_evaluation
			SET		ev_dt		= \''.Str_Replace('-','',$_POST['txtEvDt']).'\'
			,		ev_hm		= \''.Str_Replace(':','',$_POST['txtEvHm']).'\'
			,		ever		= \''.$_POST['txtEver'].'\'
			,		ever_jumin	= \''.$ed->de($_POST['everJumin']).'\'
			,		quest_1		= \''.$_POST['lblQ1'].'\'
			,		quest_2		= \''.$_POST['lblQ2'].'\'
			,		quest_3		= \''.$_POST['lblQ3'].'\'
			,		quest_4		= \''.$_POST['lblQ4'].'\'
			,		quest_5		= \''.$_POST['lblQ5'].'\'
			,		quest_6		= \''.$_POST['lblQ6'].'\'
			,		quest_7		= \''.$_POST['lblQ7'].'\'
			,		quest_8		= \''.$_POST['lblQ8'].'\'
			,		quest_9		= \''.$_POST['lblQ9'].'\'
			,		quest_10	= \''.$_POST['lblQ10'].'\'
			,		quest_11	= \''.$_POST['lblQ11'].'\'
			,		quest_12	= \''.$_POST['lblQ12'].'\'
			,		quest_13	= \''.$_POST['lblQ13'].'\'
			,		quest_14	= \''.$_POST['lblQ14'].'\'
			,		text_1		= \''.AddSlashes($_POST['txtEff1']).'\'
			,		text_2		= \''.AddSlashes($_POST['txtEff2']).'\'
			,		text_3		= \''.AddSlashes($_POST['txtEff3']).'\'
			,		text_4		= \''.AddSlashes($_POST['txtEff4']).'\'
			,		text_5		= \''.AddSlashes($_POST['txtFeel']).'\'
			,		del_flag	= \'N\'';

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
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$query[SizeOf($query)] = $sql;

	$sql = 'UPDATE	hce_proc
			SET		evln_dt	= \''.Str_Replace('-','',$_POST['txtEvDt']).'\'
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
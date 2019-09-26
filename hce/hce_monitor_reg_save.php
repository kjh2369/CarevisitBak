<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	/*********************************************************
	 *	모니터링기록지
	 *********************************************************/
	$mntrSeq		= $_POST['mntrSeq'];
	$perNm			= $_POST['txtPer'];	//전담담당자
	$perJumin		= $ed->de($_POST['perJumin']);
	$iptNm			= $_POST['txtIpt'];	//조사자
	$iptJumin		= $ed->de($_POST['iptJumin']);
	$wrtDt			= Str_Replace('-','',$_POST['txtWrtDt']);	//작성일자
	$wrtGbn			= $_POST['optRec'];	//작성구분
	$scheduleGbn	= $_POST['optSchedule'];
	$scheduleStr	= AddSlashes($_POST['txtSchedule']);
	$fullnessGbn	= $_POST['optFullness'];
	$fullnessStr	= AddSlashes($_POST['txtFullness']);
	$perinchargeGbn	= $_POST['otpPerincharge'];
	$perinchargeStr	= AddSlashes($_POST['txtPerincharge']);
	$abilityStr		= AddSlashes($_POST['txtAbility']);
	$lifeEnvStr		= AddSlashes($_POST['txtLifeEnv']);
	$extDiscomfort	= AddSlashes($_POST['txtExtDiscomfort']);
	$mntrRst		= $_POST['optRst'];
	$extDetail		= AddSlashes($_POST['txtExtDetail']);
	$Cnt            = $_POST['Cnt'];
	$MntrType       = $_POST['optMntrType']; //모니터링구분
	
	
	$sql = 'SELECT	COUNT(*)
			FROM	hce_monitor
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		mntr_seq= \''.$mntrSeq.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	//if (Empty($mntrSeq)) $mntrSeq = '1';

	if ($new){

		$sql = 'SELECT	IFNULL(MAX(mntr_seq),0)+1
				FROM	hce_monitor
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';
		$mntrSeq= $conn->get_data($sql);

		$sql = 'INSERT INTO hce_monitor (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,mntr_seq) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$mntrSeq.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	hce_monitor
			SET		mntr_dt			= \''.$wrtDt.'\'
			,		mntr_gbn		= \''.$wrtGbn.'\'
			,		mntr_type		= \''.$MntrType.'\'
			,		per_nm			= \''.$perNm.'\'
			,		per_jumin		= \''.$perJumin.'\'
			,		inspector_nm	= \''.$iptNm.'\'
			,		inspector_jumin	= \''.$iptJumin.'\'
			,		schedule_sat	= \''.$scheduleGbn.'\'
			,		schedule_svc	= \''.$scheduleStr.'\'
			,		fullness_sat	= \''.$fullnessGbn.'\'
			,		fullness_svc	= \''.$fullnessStr.'\'
			,		perincharge_sat	= \''.$perinchargeGbn.'\'
			,		perincharge_svc	= \''.$perinchargeStr.'\'
			,		ability_change	= \''.$abilityStr.'\'
			,		life_env_change	= \''.$lifeEnvStr.'\'
			,		ext_discomfort	= \''.$extDiscomfort.'\'
			,		monitor_rst		= \''.$mntrRst.'\'
			,		ext_detail		= \''.$extDetail.'\'';

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
			AND		mntr_seq= \''.$mntrSeq.'\'';
	
	
	$query[SizeOf($query)] = $sql;

	$sql = 'UPDATE	hce_proc
			SET		mntr_dt	= \''.$wrtDt.'\'
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
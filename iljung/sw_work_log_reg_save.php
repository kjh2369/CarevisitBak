<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];
	$seq	= $_POST['seq'];
	$memCd	= $ed->de($_POST['memCd']);
	$memNm	= $_POST['memNm'];
	$userCd	= $_SESSION['userCode'];

	/*
	$memTmp = Explode('/',$_POST['memList']);

	if (is_array($memTmp)){
		foreach($memTmp as $mem){
			if ($mem){
				$tmp = Explode('|',$mem);
				$idx = SizeOf($memList);

				if ($memJumin) $memJumin .= '/';
				$memJumin .= $ed->de($tmp[0]);

				if ($memName) $memName .= '/';
				$memName .= $tmp[1];
			}
		}
	}
	*/

	if (!$orgNo || !$jumin || !$memCd) exit;

	if ($yymm && $seq){
		$IsNew = false;
	}else{
		$IsNew = true;
	}

	//작업일자
	$date = str_replace('-','',$_POST['txtDate']);

	if ($IsNew){
		$yymm = ($date != '' ? SubStr($date,0,6) : $yymm);

		$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
				FROM	sw_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'';

		$seq = $conn->get_data($sql);
	}else{
		if ($yymm != SubStr($date,0,6)){
			$newYm = ($date != '' ? SubStr($date,0,6) : $yymm);

			$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
					FROM	sw_log
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		yymm	= \''.$newYm.'\'';

			$newSeq = $conn->get_data($sql);
		}else{
			$newYm = $yymm;
			$newSeq = $seq;
		}
	}

	if ($IsNew){
		//신규
		$sql = 'INSERT INTO sw_log (
				 org_no
				,jumin
				,yymm
				,seq
				,reg_jumin
				,reg_name
				,date
				,time
				,to_time
				,body_stat
				,body_stat_note
				,disease
				,medication
				,diagnosis
				,disabled
				,disabled_lvl
				,eyesight
				,hearing
				,disease_note
				,memory
				,express
				,memory_note
				,feel_stat
				,comm_other
				,comm_note
				,meal_type
				,water_type
				,intake_type
				,nutrition_note
				,env_note
				,total_note
				,target_note
				,cont_note
				,provide_note
				,check_note
				,write_log_yn
				,provide_chk_yn
				,right_svc_yn
				,house_env_yn
				,work_mind_yn
				,uniform_yn
				,action_note
				,svcporc_yn
				,notvisit_cd
				,notvisit_reason
				,comment
				,plan_rec_text
				,plan_rec_way
				,plan_body_text
				,plan_body_way
				,guard_text
				,visit_place
				,dan_yn
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$seq.'\'
				,\''.$memCd.'\'
				,\''.$memNm.'\'
				,\''.$date.'\'
				,\''.str_replace(':','',$_POST['txtTime']).'\'
				,\''.str_replace(':','',$_POST['txtToTime']).'\'
				,\''.$_POST['optBodyStat'].'\'
				,\''.AddSlashes(Trim($_POST['txtBodyStat'])).'\'
				,\''.AddSlashes(Trim($_POST['txtDisease'])).'\'
				,\''.$_POST['optMedication'].'\'
				,\''.AddSlashes(Trim($_POST['txtDiagnosis'])).'\'
				,\''.AddSlashes(Trim($_POST['txtDisabled'])).'\'
				,\''.$_POST['cboDisabled'].'\'
				,\''.$_POST['optEyesight'].'\'
				,\''.$_POST['optHearing'].'\'
				,\''.AddSlashes(Trim($_POST['txtDiseaseNote'])).'\'
				,\''.AddSlashes(Trim($_POST['optMemory'])).'\'
				,\''.$_POST['optExpress'].'\'
				,\''.AddSlashes(Trim($_POST['txtMemoryNote'])).'\'
				,\''.$_POST['optFeel'].'\'
				,\''.AddSlashes(Trim($_POST['txtCommOther'])).'\'
				,\''.AddSlashes(Trim($_POST['txtCommNote'])).'\'
				,\''.$_POST['optMealType'].'\'
				,\''.$_POST['optWaterType'].'\'
				,\''.$_POST['optIntakeType'].'\'
				,\''.AddSlashes(Trim($_POST['txtNutritionNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtEnvNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtTotalNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtTargetNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtContNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtProvideNote'])).'\'
				,\''.AddSlashes(Trim($_POST['txtCheckNote'])).'\'
				,\''.$_POST['optWriteLog'].'\'
				,\''.$_POST['optProvideChk'].'\'
				,\''.$_POST['optRightSvc'].'\'
				,\''.$_POST['optHouseEnv'].'\'
				,\''.$_POST['optWorkMind'].'\'
				,\''.$_POST['optUniform'].'\'
				,\''.AddSlashes(Trim($_POST['txtActionNote'])).'\'
				,\''.$_POST['optSvcProcYn'].'\'
				,\''.$_POST['optNotVisitReason'].'\'
				,\''.AddSlashes(Trim($_POST['txtNotVisitReason'])).'\'
				,\''.AddSlashes(Trim($_POST['txtComment'])).'\'
				,\''.AddSlashes(Trim($_POST['txtPlanRecText'])).'\'
				,\''.AddSlashes(Trim($_POST['txtPlanRecWay'])).'\'
				,\''.AddSlashes(Trim($_POST['txtPlanBodyText'])).'\'
				,\''.AddSlashes(Trim($_POST['txtPlanBodyWay'])).'\'
				,\''.AddSlashes(Trim($_POST['txtGuardText'])).'\'
				,\''.AddSlashes(Trim($_POST['txtVisitPlace'])).'\'
				,\''.($_POST['danYn'] == 'Y' ? 'Y' : 'N').'\'
				,\''.$userCd.'\'
				,NOW()
				)';
	}else{
		//수정
		$sql = 'UPDATE	sw_log
				SET		reg_jumin		= \''.$memCd.'\'
				,		reg_name		= \''.$memNm.'\'
				,		date			= \''.$date.'\'
				,		time			= \''.str_replace(':','',$_POST['txtTime']).'\'
				,		to_time			= \''.str_replace(':','',$_POST['txtToTime']).'\'
				,		body_stat		= \''.$_POST['optBodyStat'].'\'
				,		body_stat_note	= \''.AddSlashes(Trim($_POST['txtBodyStat'])).'\'
				,		disease			= \''.AddSlashes(Trim($_POST['txtDisease'])).'\'
				,		medication		= \''.$_POST['optMedication'].'\'
				,		diagnosis		= \''.AddSlashes(Trim($_POST['txtDiagnosis'])).'\'
				,		disabled		= \''.AddSlashes(Trim($_POST['txtDisabled'])).'\'
				,		disabled_lvl	= \''.$_POST['cboDisabled'].'\'
				,		eyesight		= \''.$_POST['optEyesight'].'\'
				,		hearing			= \''.$_POST['optHearing'].'\'
				,		disease_note	= \''.AddSlashes(Trim($_POST['txtDiseaseNote'])).'\'
				,		memory			= \''.AddSlashes(Trim($_POST['optMemory'])).'\'
				,		express			= \''.$_POST['optExpress'].'\'
				,		memory_note		= \''.AddSlashes(Trim($_POST['txtMemoryNote'])).'\'
				,		feel_stat		= \''.$_POST['optFeel'].'\'
				,		comm_other		= \''.AddSlashes(Trim($_POST['txtCommOther'])).'\'
				,		comm_note		= \''.AddSlashes(Trim($_POST['txtCommNote'])).'\'
				,		meal_type		= \''.$_POST['optMealType'].'\'
				,		water_type		= \''.$_POST['optWaterType'].'\'
				,		intake_type		= \''.$_POST['optIntakeType'].'\'
				,		nutrition_note	= \''.AddSlashes(Trim($_POST['txtNutritionNote'])).'\'
				,		env_note		= \''.AddSlashes(Trim($_POST['txtEnvNote'])).'\'
				,		total_note		= \''.AddSlashes(Trim($_POST['txtTotalNote'])).'\'
				,		target_note		= \''.AddSlashes(Trim($_POST['txtTargetNote'])).'\'
				,		cont_note		= \''.AddSlashes(Trim($_POST['txtContNote'])).'\'
				,		provide_note	= \''.AddSlashes(Trim($_POST['txtProvideNote'])).'\'
				,		check_note		= \''.AddSlashes(Trim($_POST['txtCheckNote'])).'\'
				,		write_log_yn	= \''.$_POST['optWriteLog'].'\'
				,		provide_chk_yn	= \''.$_POST['optProvideChk'].'\'
				,		right_svc_yn	= \''.$_POST['optRightSvc'].'\'
				,		house_env_yn	= \''.$_POST['optHouseEnv'].'\'
				,		work_mind_yn	= \''.$_POST['optWorkMind'].'\'
				,		uniform_yn		= \''.$_POST['optUniform'].'\'
				,		action_note		= \''.AddSlashes(Trim($_POST['txtActionNote'])).'\'
				,		svcporc_yn		= \''.$_POST['optSvcProcYn'].'\'
				,		notvisit_cd		= \''.$_POST['optNotVisitReason'].'\'
				,		notvisit_reason	= \''.AddSlashes(Trim($_POST['txtNotVisitReason'])).'\'
				,		comment			= \''.AddSlashes(Trim($_POST['txtComment'])).'\'
				,		plan_rec_text	= \''.AddSlashes(Trim($_POST['txtPlanRecText'])).'\'
				,		plan_rec_way	= \''.AddSlashes(Trim($_POST['txtPlanRecWay'])).'\'
				,		plan_body_text	= \''.AddSlashes(Trim($_POST['txtPlanBodyText'])).'\'
				,		plan_body_way	= \''.AddSlashes(Trim($_POST['txtPlanBodyWay'])).'\'
				,		guard_text		= \''.AddSlashes(Trim($_POST['txtGuardText'])).'\'
				,		visit_place		= \''.AddSlashes(Trim($_POST['txtVisitPlace'])).'\'
				,		dan_yn			= \''.($_POST['danYn'] == 'Y' ? 'Y' : 'N').'\'
				,		update_id		= \''.$userCd.'\'
				,		update_dt		= NOW()';

		if ($newYm != $yymm){
			$sql .= ',	yymm = \''.$newYm.'\'';
		}

		if ($newSeq != $seq){
			$sql .= ',	seq = \''.$newYm.'\'';
		}

		$sql .= '
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'
				AND		seq		= \''.$seq.'\'';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 echo $conn->error_msg;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
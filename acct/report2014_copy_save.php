<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code1 = $_POST['code1'];
	$code2 = $_POST['code2'];
	$ssn1  = $ed->de($_POST['ssn1']);
	$ssn2  = $ed->de($_POST['ssn2']);
	$seq1  = $_POST['seq1'];
	$seq2  = $_POST['seq2'];

	$memCd  = $ed->de($_POST['memCd']);
	$regDt  = $_POST['regDt'];
	$ddtCnt  = $_POST['ddtCnt'];
	$pstCnt  = $_POST['pstCnt'];
	$bsrCnt  = $_POST['bsrCnt'];
	$bsrCnt  = $_POST['bsrCnt'];
	$planCnt  = $_POST['planCnt'];
	$agrCnt  = $_POST['agrCnt'];

	if (Empty($code1) || Empty($code2)){
		echo 9;
		exit;
	}
	
	//낙상	
	if($ddtCnt > 0){
		$sql = 'SELECT	count(*)
				FROM	report_falltest
				WHERE	org_no	= \''.$code2.'\'
				AND		seq   = \''.$seq2.'\'
				AND		jumin  = \''.$ssn2.'\'';
		$liCnt = $conn->get_data($sql);
		
		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}
		
		$sql = 'SELECT	*
				FROM	report_falltest
				WHERE	org_no	= \''.$code1.'\'
				AND		seq   = \''.$seq1.'\'
				AND		jumin  = \''.$ssn1.'\'';
		$data = $conn->get_array($sql);
		
		if ($new){
			
			$sql = 'insert into report_falltest (
					 org_no
					,seq
					,jumin
					,reg_dt
					,mem_cd
					,quest
					,point
					,insert_dt
					,insert_id) values (
					 \''.$code2.'\'
					,\''.$seq2.'\'
					,\''.$ssn2.'\'
					,\''.$regDt.'\'
					,\''.$memCd.'\'
					,\''.$data['quest'].'\'
					,\''.$data['point'].'\'
					,\''.date('Y-m-d', mktime()).'\'
					,\''.$code2.'\')';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
			$conn->commit();
		
		}else {
	
			$sql = 'update report_falltest
					   set reg_dt       = \''.$regDt.'\'
					,      mem_cd       = \''.$memCd.'\'
					,      quest        = \''.$data['quest'].'\'
					,      point		= \''.$data['point'].'\'
					,      del_flag		= \'N\'
					,      update_dt	= \''.date('Y-m-d', mktime()).'\'
					,      update_id	= \''.$code2.'\'
					 where org_no		= \''.$code2.'\'
					   and seq			= \''.$seq2.'\'
					   and jumin		= \''.$ssn2.'\'';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();
			
		}
		
	}
	
	//욕창	
	if($pstCnt > 0){
		$sql = 'SELECT	count(*)
				FROM	r_cltpst
				WHERE	org_no	= \''.$code2.'\'
				AND		r_yymm   = \''.substr(str_replace('-','',$regDt),0,6).'\'
				AND		r_seq   = \''.$seq2.'\'
				AND		r_c_id  = \''.$ssn2.'\'';
		
		$liCnt = $conn->get_data($sql);
		
		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}
			
		$sql = 'SELECT	*
				FROM	r_cltpst
				WHERE	org_no	= \''.$code1.'\'
				AND		r_seq   = \''.$seq1.'\'
				AND		r_c_id  = \''.$ssn1.'\'';
		$data = $conn->get_array($sql);
		
		$sql = ' select m03_name
				   from m03sugupja
				  where m03_jumin = \''.$ssn2.'\'';
		$name = $conn -> get_data($sql);
		
		$sql = ' select m02_yname
				   from m02yoyangsa
				  where m02_yjumin = \''.$memCd.'\'';
		$yoyname = $conn -> get_data($sql);


		if ($new){
					
			$sql = 'insert into r_cltpst (
				 org_no
				,r_yymm
				,r_seq
				,r_c_id
				,r_c_nm
				,r_reg_dt
				,r_m_id
				,r_m_nm
				,r_quest
				,r_point
				,insert_dt
				,insert_id) values (
				 \''.$code2.'\'
				,\''.substr(str_replace('-','',$regDt),0,6).'\'
				,\''.$seq2.'\'
				,\''.$ssn2.'\'
				,\''.$name.'\'
				,\''.$regDt.'\'
				,\''.$memCd.'\'
				,\''.$yoyname.'\'
				,\''.$data['r_quest'].'\'
				,\''.$data['r_point'].'\'
				,\''.date('Y-m-d', mktime()).'\'
				,\''.$code2.'\')';
			
			$conn->begin();
			
			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

		}else {
			
			$sql = 'update r_cltpst
					   set r_reg_dt					= \''.$regDt.'\'
					,      r_m_id					= \''.$memCd.'\'
					,      r_m_nm					= \''.$yoyname.'\'
					,      r_quest					= \''.$data['r_quest'].'\'
					,      r_point					= \''.$data['r_point'].'\'
					,      del_flag					= \'N\'
					,      update_dt				= \''.$regDt.'\'
					,      update_id				= \''.$code.'\'
					 where org_no					= \''.$code2.'\'
					   and r_yymm					= \''.substr(str_replace('-','',$regDt),0,6).'\'
					   and r_seq					= \''.$seq2.'\'
					   and r_c_id					= \''.$ssn2.'\'';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();
			
		}

	}

	
	//욕구	
	if($bsrCnt > 0){
		$sql = 'SELECT	count(*)
				FROM	report_na
				WHERE	org_no	= \''.$code2.'\'
				AND		seq   = \''.$seq2.'\'
				AND		jumin  = \''.$ssn2.'\'';
		$liCnt = $conn->get_data($sql);
		
		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}
		
		$sql = 'SELECT	*
				FROM	report_na
				WHERE	org_no	= \''.$code1.'\'
				AND		seq   = \''.$seq1.'\'
				AND		jumin  = \''.$ssn1.'\'';
		$data = $conn->get_array($sql);
		
		if ($new){

			$sql = 'insert into report_na (
					 org_no
					,seq
					,jumin
					,insert_dt
					,insert_id) values (
					 \''.$code2.'\'
					,\''.$seq2.'\'
					,\''.$ssn2.'\'
					,\''.date('Y-m-d', mktime()).'\'
					,\''.$code2.'\')';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
			$conn->commit();
		}
		
		$sql = 'update report_na
				   set reg_dt					= \''.$regDt.'\'
				,      reg_er					= \''.$memCd.'\'
				,      height					= \''.$data['height'].'\'
				,      weight					= \''.$data['weight'].'\'
				,      body_state				= \''.$data['body_state'].'\'
				,      body_state_other			= \''.$data['body_state_other'].'\'
				,      disease_state_1			= \''.$data['disease_state_1'].'\'
				,      disease_state_2			= \''.$data['disease_state_2'].'\'
				,      disease_state_3			= \''.$data['disease_state_3'].'\'
				,      disease_state_4			= \''.$data['disease_state_4'].'\'
				,      disease_state_5			= \''.$data['disease_state_5'].'\'
				,      disease_state_6			= \''.$data['disease_state_6'].'\'
				,      disease_state_7			= \''.$data['disease_state_7'].'\'
				,      disease_state_8			= \''.$data['disease_state_8'].'\'
				,      disease_state_9			= \''.$data['disease_state_9'].'\'
				,      disease_state_10			= \''.$data['disease_state_10'].'\'
				,      medical_history			= \''.$data['medical_history'].'\'
				,      now_diagnosis			= \''.$data['now_diagnosis'].'\'
				,      disease_state_other      = \''.$data['disease_state_other'].'\'
				,      rehabilitant_state       = \''.$data['rehabilitant_state'].'\'
				,      rehabilitant_other       = \''.$data['rehabilitant_other'].'\'
				,      nurse_state				= \''.$data['nurse_state'].'\'
				,      nurse_other				= \''.$data['nurse_other'].'\'
				,      recognize_state			= \''.$data['recognize_state'].'\'
				,      recognize_state_other    = \''.$data['recognize_state_other'].'\'
				,      communicate_1			= \''.$data['communicate_1'].'\'
				,      communicate_2			= \''.$data['communicate_2'].'\'
				,      communicate_3			= \''.$data['communicate_3'].'\'
				,      communicate_other		= \''.$data['communicate_other'].'\'
				,      nutritive_state_1		= \''.$data['nutritive_state_1'].'\'
				,      nutritive_state_2		= \''.$data['nutritive_state_2'].'\'
				,      nutritive_state_3		= \''.$data['nutritive_state_3'].'\'
				,      nutritive_state_4		= \''.$data['nutritive_state_4'].'\'
				,      nutritive_state_5		= \''.$data['nutritive_state_5'].'\'
				,      nutritive_state_other	= \''.$data['nutritive_state_other'].'\'
				,      marry_yn					= \''.$data['marry_yn'].'\'
				,      spouse_life_yn			= \''.$data['spouse_life_yn'].'\'
				,      children_cnt				= \''.$data['children_cnt'].'\'
				,      caregiver_yn				= \''.$data['caregiver_yn'].'\'
				,      caregiver_age			= \''.$data['caregiver_age'].'\'
				,      caregiver_rel			= \''.$data['caregiver_rel'].'\'
				,      caregiver_other			= \''.$data['caregiver_other'].'\'
				,      caregiver_economy_state  = \''.$data['caregiver_economy_state'].'\'
				,      cohabitee				= \''.$data['cohabitee'].'\'
				,      family_env_other			= \''.$data['family_env_other'].'\'
				,      religion					= \''.$data['religion'].'\'
				,      religion_other			= \''.$data['religion_other'].'\'
				,      use_medical				= \''.$data['use_medical'].'\'
				,      medical_telno			= \''.$data['medical_telno'].'\'
				,      resource					= \''.$data['resource'].'\'
				,      resource_other			= \''.$data['resource_other'].'\'
				,      resource_use_other       = \''.$data['resource_use_other'].'\'
				,      person_needs				= \''.$data['person_needs'].'\'
				,      total_comment			= \''.$data['total_comment'].'\'
				,	   del_flag                 = \'N\'
				,      update_dt				= \''.date('Y-m-d', mktime()).'\'
				,      update_id				= \''.$code2.'\'
				 where org_no					= \''.$code2.'\'
				   and seq						= \''.$seq2.'\'
				   and jumin					= \''.$ssn2.'\'';
		
		$conn->begin();

		if (!$conn->execute($sql)){
			echo nl2br($sql); exit;
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
	}

	//급여계획	
	if($planCnt > 0){
		$sql = 'SELECT	count(*)
				FROM	report_plan_mst
				WHERE	org_no	= \''.$code2.'\'
				AND		seq   = \''.$seq2.'\'
				AND		jumin  = \''.$ssn2.'\'';
		$liCnt = $conn->get_data($sql);
		
		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}
		
		$sql = 'SELECT	*
				FROM	report_plan_mst
				WHERE	org_no	= \''.$code1.'\'
				AND		seq   = \''.$seq1.'\'
				AND		jumin  = \''.$ssn1.'\'';
		$data = $conn->get_array($sql);
		
		if ($new){

			$sql = 'insert into report_plan_mst (
					 org_no
					,seq
					,jumin
					,insert_dt
					,insert_id) values (
					 \''.$code2.'\'
					,\''.$seq2.'\'
					,\''.$ssn2.'\'
					,\''.date('Y-m-d', mktime()).'\'
					,\''.$code2.'\')';
		
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();
		}
	
		$sql = 'update report_plan_mst
				   set reg_dt       = \''.$regDt.'\'
				,      reg_er       = \''.$memCd.'\'
				,      contract_dt  = \''.$data['contract_dt'].'\'
				,      confirm_dt	= \''.$data['confirm_dt'].'\'
				,      svc_kind		= \''.$data['svc_kind'].'\'
				,      goal			= \''.$data['goal'].'\'
				,      confirmor	= \''.$data['confirmor'].'\'
				,      update_dt	= \''.date('Y-m-d', mktime()).'\'
				,      update_id	= \''.$code.'\'
				 where org_no		= \''.$code2.'\'
				   and seq			= \''.$seq2.'\'
				   and jumin		= \''.$ssn2.'\'';
		
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

			$sql = 'SELECT	count(*)
					FROM	report_plan_sub
					WHERE	org_no	= \''.$code1.'\'
					AND		seq   = \''.$seq1.'\'
					AND		jumin  = \''.$ssn1.'\'';
			$subCnt = $conn->get_data($sql);
			
			//기존 구분,세부목표 삭제
			$sql = 'DELETE
					FROM	report_plan_sub
					WHERE	org_no	= \''.$code2.'\'
					AND		jumin	= \''.$ssn2.'\'
					AND		seq		= \''.$seq2.'\'';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

			//기존 급여내용, 횟수/시간, 제공자 삭제
			$sql = 'DELETE
					FROM	report_plan_sub2
					WHERE	org_no	= \''.$code2.'\'
					AND		jumin	= \''.$ssn2.'\'
					AND		seq		= \''.$seq2.'\'';

			$conn->begin();

			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

			for($i=1; $i<=$subCnt; $i++){

				$sql = 'SELECT	*
						FROM	report_plan_sub
						WHERE	org_no	= \''.$code1.'\'
						AND		seq   = \''.$seq1.'\'
						AND		idx   = \''.$i.'\'
						AND		jumin  = \''.$ssn1.'\'';
				$sub = $conn->get_array($sql);

				$sql = 'insert into report_plan_sub (
				 org_no
				,jumin
				,seq
				,idx
				,gubun
				,dtl_goal
				) values (
				 \''.$code2.'\'
				,\''.$ssn2.'\'
				,\''.$seq2.'\'
				,\''.$i.'\'
				,\''.$sub['gubun'].'\'
				,\''.$sub['dtl_goal'].'\'
				)';
				
				
				$conn->begin();

				if (!$conn->execute($sql)){
					echo nl2br($sql); exit;
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}

				$conn->commit();

				$sql = 'SELECT	count(*)
						FROM	report_plan_sub2
						WHERE	org_no	= \''.$code1.'\'
						AND		seq   = \''.$seq1.'\'
						AND		idx   = \''.$i.'\'
						AND		jumin  = \''.$ssn1.'\'';
				$subCnt2 = $conn->get_data($sql);
				

				for($j=1; $j<=$subCnt2; $j++){

					$sql = 'SELECT	*
							FROM	report_plan_sub2
							WHERE	org_no	= \''.$code1.'\'
							AND		seq   = \''.$seq1.'\'
							AND		idx   = \''.$i.'\'
							AND		no   = \''.$j.'\'
							AND		jumin  = \''.$ssn1.'\'';
					$sub2 = $conn->get_array($sql);

					$sql = 'insert into report_plan_sub2 (
							 org_no
							,jumin
							,seq
							,idx
							,no
							,content
							,cnt
							,offerer
							) values (
							 \''.$code2.'\'
							,\''.$ssn2.'\'
							,\''.$seq2.'\'
							,\''.$i.'\'
							,\''.$j.'\'
							,\''.$sub2['content'].'\'
							,\''.$sub2['cnt'].'\'
							,\''.$sub2['offerer'].'\'
							)';
					
					$conn->begin();

					if (!$conn->execute($sql)){
						echo nl2br($sql); exit;
						 $conn->rollback();
						 $conn->close();
						 echo 9;
						 exit;
					}

					$conn->commit();

				}
				
			}

	}
	
	//이용계획서	
	
	if($agrCnt > 0){
		$sql = 'SELECT	count(*)
				FROM	report_sppc_mst
				WHERE	org_no	= \''.$code2.'\'
				AND		seq   = \''.$seq2.'\'
				AND		jumin  = \''.$ssn2.'\'';
		$liCnt = $conn->get_data($sql);
		
		if ($liCnt > 0){
			$new = false;
		}else{
			$new = true;
		}
		
		$sql = 'SELECT	*
				FROM	report_sppc_mst
				WHERE	org_no	= \''.$code1.'\'
				AND		seq   = \''.$seq1.'\'
				AND		jumin  = \''.$ssn1.'\'';
		$data = $conn->get_array($sql);
		
		if ($new){

			$sql = 'insert into report_sppc_mst (
					 org_no
					,seq
					,jumin
					,insert_dt
					,insert_id) values (
					 \''.$code2.'\'
					,\''.$seq2.'\'
					,\''.$ssn2.'\'
					,\''.date('Y-m-d', mktime()).'\'
					,\''.$code.'\')';
			
			
			$conn->begin();


			if (!$conn->execute($sql)){
				echo nl2br($sql); exit;
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			$conn->commit();

		}
	
		$sql = 'update report_sppc_mst
				   set reg_dt       = \''.$regDt.'\'
				,      mem_cd       = \''.$memCd.'\'
				,      matter_note  = \''.$data['matter_note'].'\'
				,      chk_yn       = \''.$data['chk_yn'].'\'
				,      family_yn    = \''.$data['family_yn'].'\'
				,      update_dt	= \''.date('Y-m-d', mktime()).'\'
				,      update_id	= \''.$code2.'\'
				 where org_no		= \''.$code2.'\'
				   and seq			= \''.$seq2.'\'
				   and jumin		= \''.$ssn2.'\'';
			
		$conn->begin();

		if (!$conn->execute($sql)){
			
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
		
		$conn->commit();
		
		//기존 서비스욕구 삭제
		$sql = 'DELETE
				FROM	report_sppc_plan
				WHERE	org_no	= \''.$code2.'\'
				AND		jumin	= \''.$ssn2.'\'
				AND		seq		= \''.$seq2.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
		
		$conn->commit();
		
		$sql = 'SELECT	count(*)
				FROM	report_sppc_plan
				WHERE	org_no	= \''.$code1.'\'
				AND		seq   = \''.$seq1.'\'
				AND		jumin  = \''.$ssn1.'\'';
		$cnt = $conn->get_data($sql);

		for($i=1; $i<=$cnt; $i++){

			$sql = 'SELECT	*
					FROM	report_sppc_plan
					WHERE	org_no	= \''.$code1.'\'
					AND		seq   = \''.$seq1.'\'
					AND		jumin  = \''.$ssn1.'\'
					AND		idx  = \''.$i.'\'';
			$data2 = $conn->get_array($sql);

			
			$sql = 'insert into report_sppc_plan (
					 org_no
					,jumin
					,seq
					,idx
					,kind
					,content
					,cycle
					,time
					) values (
					 \''.$code2.'\'
					,\''.$ssn2.'\'
					,\''.$seq2.'\'
					,\''.$i.'\'
					,\''.$data2['kind'].'\'
					,\''.$data2['content'].'\'
					,\''.$data2['cycle'].'\'
					,\''.$data2['time'].'\'
					)';
			
			$conn->begin();
			
			if (!$conn->execute($sql)){
				
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
			
			$conn->commit();
		}


		//기존 서비스욕구 삭제
		$sql = 'DELETE
				FROM	report_sppc_needs
				WHERE	org_no	= \''.$code2.'\'
				AND		jumin	= \''.$ssn2.'\'
				AND		seq		= \''.$seq2.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
		
		$conn->commit();
		
		for($i=1; $i<=10; $i++){
			
			$sql = 'SELECT	*
					FROM	report_sppc_needs
					WHERE	org_no	= \''.$code1.'\'
					AND		seq   = \''.$seq1.'\'
					AND		jumin  = \''.$ssn1.'\'
					AND		idx  = \''.$i.'\'';
			$data = $conn->get_array($sql);

			$sql = 'insert into report_sppc_needs (
					 org_no
					,jumin
					,seq
					,idx
					,svc_nm
					,fnc
					,needs
					,aim) values (
					 \''.$code2.'\'
					,\''.$ssn2.'\'
					,\''.$seq2.'\'
					,\''.$i.'\'
					,\''.$data['svc_nm'].'\'
					,\''.$data['fnc'].'\'
					,\''.$data['needs'].'\'
					,\''.$data['aim'].'\'
					
					)';
			
			$conn->begin();

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
		
			$conn->commit();
		}

	}
	

	echo 1;

	include_once('../inc/_db_close.php');
?>
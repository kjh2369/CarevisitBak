<?
	$visit_code = $_POST['code'];
	$visit_yymm = $_POST['yymm'];
	$visit_seq  =($_POST['visit_seq'] != '' ? $_POST['visit_seq'] : $_POST['seq']);
	
	if (!empty($_POST['visit_m_cd'])){
		$reg_id = $_SESSION['userCode'];
		$reg_dt = date('Y-m-d', mktime());

		if ($visit_seq == 0){
			$visit_yymm = substr(str_replace('-', '', $_POST['visit_dt']), 0, 6);
			//$visit_yymm = date('Ym', mktime());

			$sql = 'select ifnull(max(visit_seq), 0) + 1
					  from counsel_client_visit
					 where org_no     = \''.$visit_code.'\'
					   and visit_yymm = \''.$visit_yymm.'\'';

			$visit_seq = $conn->get_data($sql);

			$sql = 'insert into counsel_client_visit (
					 org_no
					,visit_yymm
					,visit_seq
					,visit_c_cd
					,insert_dt
					,insert_id) values (
					 \''.$visit_code.'\'
					,\''.$visit_yymm.'\'
					,\''.$visit_seq.'\'
					,\''.$ed->de($_POST['visit_ssn']).'\'
					,\''.$reg_dt.'\'
					,\''.$reg_id.'\')';


			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
			$add_sql = '';

		}else{
			$add_sql = ', update_dt = \''.$reg_dt.'\'
						, update_id = \''.$reg_id.'\'';
		}

		$visit_m_cd = $ed->de($_POST['visit_m_cd']);
		$visit_m_nm = $conn->member_name($visit_code, $visit_m_cd);

		$sql = 'update counsel_client_visit
				   set visit_m_cd      = \''.$visit_m_cd.'\'
				,      visit_m_nm      = \''.$visit_m_nm.'\'
				,      visit_dt        = \''.$_POST['visit_dt'].'\'
				,      visit_tm        = \''.$_POST['visit_hour'].':'.$_POST['visit_min'].':00\'
				,      visit_h_bp      = \''.$_POST['visit_bp'].'\'
				,      visit_h_nh      = \''.$_POST['visit_nh'].'\'
				,      visit_h_bf_bs   = \''.$_POST['visit_bf_bs'].'\'
				,      visit_h_af_time = \''.$_POST['visit_af_time'].'\'
				,      visit_h_af_bs   = \''.$_POST['visit_af_bs'].'\'
				,      visit_h_cf      = \''.addslashes($_POST['visit_cf']).'\'
				,      visit_h_body    = \''.addslashes($_POST['visit_body']).'\'
				,      visit_h_soul    = \''.addslashes($_POST['visit_soul']).'\'
				,      visit_other     = \''.addslashes($_POST['visit_other']).'\''.$add_sql.'
				 where org_no     = \''.$visit_code.'\'
				   and visit_yymm = \''.$visit_yymm.'\'
				   and visit_seq  = \''.$visit_seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
?>
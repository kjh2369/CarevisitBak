<?
	$phone_code = $_POST['code'];
	$phone_yymm = $_POST['yymm'];
	$phone_seq  = ($_POST['phone_seq'] != '' ? $_POST['phone_seq'] : $_POST['seq']);
	
	if (!empty($_POST['phone_m_cd'])){
		$reg_id = $_SESSION['userCode'];
		$reg_dt = date('Y-m-d', mktime());
		
		if ($phone_seq == 0){
			$phone_yymm = substr(str_replace('-', '', $_POST['phone_dt']), 0, 6);
			//$phone_yymm = date('Ym', mktime());
			
			$sql = 'select ifnull(max(phone_seq), 0) + 1
					  from counsel_client_phone
					 where org_no     = \''.$phone_code.'\'
					   and phone_yymm = \''.$phone_yymm.'\'';
					   
			$phone_seq = $conn->get_data($sql);
			
			$sql = 'insert into counsel_client_phone (
					 org_no
					,phone_yymm
					,phone_seq
					,phone_c_cd
					,insert_dt
					,insert_id) values (
					 \''.$phone_code.'\'
					,\''.$phone_yymm.'\'
					,\''.$phone_seq.'\'
					,\''.$ed->de($_POST['phone_ssn']).'\'
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
		
		$phone_m_cd = $ed->de($_POST['phone_m_cd']);
		$phone_m_nm = $conn->member_name($phone_code, $phone_m_cd);
		
		$sql = 'update counsel_client_phone
				   set phone_m_cd      = \''.$phone_m_cd.'\'
				,      phone_m_nm      = \''.$phone_m_nm.'\'
				,      phone_dt        = \''.$_POST['phone_dt'].'\'
				,      phone_kind      = \''.$_POST['phone_kind'].'\'
				,      phone_start     = \''.$_POST['phone_start'].':00\'
				,      phone_end       = \''.$_POST['phone_end'].':00\'
				,      phone_contents  = \''.addslashes($_POST['phone_cont']).'\'
				,      phone_result    = \''.addslashes($_POST['phone_result']).'\'
				,      phone_other     = \''.addslashes($_POST['phone_other']).'\''.$add_sql.'
				 where org_no     = \''.$phone_code.'\'
				   and phone_yymm = \''.$phone_yymm.'\'
				   and phone_seq  = \''.$phone_seq.'\'';
				   
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
?>
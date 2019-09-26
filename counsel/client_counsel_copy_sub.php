<?
	// 공통변수
		$code        = $_POST['code'];
		$kind        = $_POST['kind'];
		$counsel_dt  = $_POST['counsel_dt'];
		$counsel_seq = $_POST['counsel_seq'];
		$family_type = '2';
		$reg_cd      = $_SESSION['userCode'];
		$reg_dt      = date('Y-m-d', mktime());
		
		//if($debug) print_r($_POST); exit;

		if ($counsel_seq == 0){
			$sql = "select ifnull(max(client_seq), 0) + 1
					  from counsel_client
					 where org_no    = '$code'
					   and client_dt = '$counsel_dt'";

			$counsel_seq = $conn->get_data($sql);
			$counsel_ssn = $_POST['counsel_ssn1'].$_POST['counsel_ssn2'];
			$write_mode = 1;
		}else{
			if ($_POST['reg_cnt'] == 0){
				$counsel_ssn = $_POST['counsel_ssn1'].$_POST['counsel_ssn2'];
			}else{
				$counsel_ssn = $ed->de($_POST['counsel_ssn']);
			}

			$write_mode = 2;
		}

		if ($is_path == 'counsel'   ||
			$is_path == 'reportNew' ){
		}else {
			$counsel_ssn = $jumin;

			if (Empty($counsel_ssn)){
				return;
			}
		}


	// 상담지 구분
	//	1 : 재가/노인돌봄/가사간병/장애활동보조
	//	2 : 산모신생아
		
		
		
		$counsel_kind = $_POST['counsel_kind'];
		$svc_kind     = $_POST['svc_kind'];
		
		if ($svc_kind == ''){
			//$svc_kind = $counsel_kind;
			$svc_kind = '9';
		}else if ($svc_kind == '3'){
			$counsel_kind = $svc_kind;
		}

	// counsel_client 수급자 기본정보
		if ($is_path == 'counsel' ){
			$name         = $_POST['counsel_name'];
			$phone        = str_replace('-', '', $_POST['counsel_phone']);
			$mobile       = str_replace('-', '', $_POST['counsel_mobile']);
			$postno       = $_POST['counsel_postno'];
			$addr         = $_POST['counsel_addr'];
			$addr_dtl     = $_POST['counsel_addr_dtl'];
			$protect_nm   = $_POST['counsel_protect_nm'];
			$protect_rel  = $_POST['counsel_protect_rel'];
			$protect_tel  = str_replace('-', '', $_POST['counsel_protect_tel']);
			$family_gbn   = $_POST['family_gbn'];
			$family_other = $_POST['family_other'];
			$other_text_1 = addslashes($_POST['other_text_1']);
			$other_text_2 = addslashes($_POST['other_text_2']);
			$other_text_3 = addslashes($_POST['other_text_3']);
		}else{
			$name         = $_POST['name'];
			$phone        = str_replace('-', '', $_POST['phone']);
			$mobile       = str_replace('-', '', $_POST['mobile']);
			$postno       = $_POST['postno1'].$_POST['postno2'];
			$addr         = $_POST['addr'];
			$addr_dtl     = $_POST['addr_dtl'];
			$protect_nm   = $_POST['protect_nm'];
			$protect_rel  = $_POST['protect_rel'];
			$protect_tel  = str_replace('-', '', $_POST['protect_tel']);
			$family_gbn   = $_POST['family_gbn'];
			$family_other = $_POST['family_other'];
			$other_text_1 = addslashes($_POST['other_text_1']);
			$other_text_2 = addslashes($_POST['other_text_2']);
			$other_text_3 = addslashes($_POST['other_text_3']);
		}
		


		// 신규시 등록
		if ($write_mode == 1){
			$sql = "insert into counsel_client (
					 org_no
					,client_dt
					,client_seq
					,client_ssn) values (
					 '$code'
					,'$counsel_dt'
					,'$counsel_seq'
					,'$counsel_ssn')";
			
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
		
		// 데이타 수정
		$sql = "update counsel_client
				   set client_ssn          = '$counsel_ssn'
				,      client_nm           = '$name'
				,      client_counsel      = '$svc_kind'
				,      client_phone        = '$phone'
				,      client_mobile       = '$mobile'
				,      client_postno       = '$postno'
				,      client_addr         = '$addr'
				,      client_addr_dtl     = '$addr_dtl'
				,      client_protect_nm   = '$protect_nm'
				,      client_protect_rel  = '$protect_rel'
				,      client_protect_tel  = '$protect_tel'
				,      client_family_gbn   = '$family_gbn'
				,      client_family_other = '$family_other'
				,      client_text_1       = '$other_text_1'
				,      client_text_2       = '$other_text_2'
				,      client_text_3       = '$other_text_3'";

		if ($write_mode == 1){
			$sql .= "
				,      insert_id           = '$reg_cd'
				,      insert_dt           = '$reg_dt'";
		}else{
			$sql .= "
				,      del_flag            = 'N'
				,      update_id           = '$reg_cd'
				,      update_dt           = '$reg_dt'";
		}

		$sql .= "
				 where org_no              = '$code'
				   and client_dt           = '$counsel_dt'
				   and client_seq          = '$counsel_seq'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	
	// 가족사항
		//가족사항 삭제
		$sql = "delete
				  from counsel_family
				 where org_no      = '$code'
				   and family_type = '$family_type'
				   and family_ssn  = '$counsel_ssn'";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}

		$sql = "";

		$family_cnt = sizeof($_POST['family_name']);
		
		for($i=0; $i<$family_cnt; $i++){
			$family_name		= $_POST['family_name'][$i];
			$family_relation	= $_POST['family_relation'][$i];
			$family_age			= $_POST['family_age'][$i];
			$family_job			= addslashes($_POST['family_job'][$i]);
			$family_together	= $_POST['family_together'][$i];
			$family_salary		= str_replace(',', '', $_POST['family_salary'][$i]);

			if ($family_name != ''){
				$sql .= ($sql != "" ? "," : "");
				$sql .= "('$code'
						 ,'$family_type'
						 ,'$counsel_ssn'
						 ,'$i'
						 ,'$family_name'
						 ,'$family_relation'
						 ,'$family_age'
						 ,'$family_job'
						 ,'$family_together'
						 ,'$family_salary')";
			}
		}
		
		if ($sql != ""){
			$sql = "insert into counsel_family values ".$sql;
			
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}

	if ($counsel_kind != 3){
	// counsel_client_normal 초기상담기록지(재가/노인돌봄/가사간병/장애활동보조)
		// 신규데이타 등록
		if ($conn->get_data('select count(*) from counsel_client_normal where org_no = \''.$code.'\' and client_dt = \''.$counsel_dt.'\' and client_seq = \''.$counsel_seq.'\'') == 0){
			$sql = "insert into counsel_client_normal (
					 org_no
					,client_dt
					,client_seq) values (
					 '$code'
					,'$counsel_dt'
					,'$counsel_seq')";
			
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}

		$talk_ssn      = $ed->de($_POST['normal_talker_cd']);
		$talk_nm       = $conn->member_name($code, $talk_ssn, $kind);
		$talk_type     = $_POST['normal_counsel_type'];
		$talk_dt       = $_POST['normal_counsel_date'];
		$protect_gbn   = $_POST['normal_protect_gbn'];
		$protect_other = $_POST['normal_protect_other'];
		$level_gbn	   = $_POST['normal_level_gbn'];
		$sick_nm       = $_POST['normal_sick_nm'];
		$drug_nm       = $_POST['normal_drug_nm'];
		$diag_nm       = $_POST['normal_diag_nm'];
		$dis_nm        = $_POST['normal_dis_nm'];
		$eye_kind      = $_POST['normal_eye'];
		$ear_kind      = $_POST['normal_ear'];
		$activate_kind = $_POST['normal_activate'];
		$eat_kind      = $_POST['normal_eat'];
		$excreta_kind  = $_POST['normal_excreata'];
		$hygiene_kind  = $_POST['normal_hygiene'];
		$mind_kind     = $_POST['normal_mind'];
		$mind_other    = $_POST['normal_mind_other'];
		$talk_stat     = addslashes($_POST['normal_talk_stat']);
		$remember_kind = $_POST['normal_remember'];
		$express_kind  = $_POST['normal_express'];
		$use_kind      = $_POST['normal_use_center'];
		$use_other     = $_POST['normal_use_other'];

		// 데이타 수정
		$sql = "update counsel_client_normal
				   set talker_ssn         = '$talk_ssn'
				,      talker_nm          = '$talk_nm'
				,      talker_type        = '$talk_type'
				,      talker_dt          = '$talk_dt'
				,      protect_gbn        = '$protect_gbn'
				,      protect_other      = '$protect_other'
				,	   level_gbn	      = '$level_gbn'
				,      health_sick_nm     = '$sick_nm'
				,      health_drug_nm     = '$drug_nm'
				,      health_diag_nm     = '$diag_nm'
				,      health_dis_nm      = '$dis_nm'
				,      health_eye_kind    = '$eye_kind'
				,      health_ear_kind    = '$ear_kind'
				,      body_activate_kind = '$activate_kind'
				,      nutr_eat_kind      = '$eat_kind'
				,      nutr_excreta_kind  = '$excreta_kind'
				,      nutr_hygiene_kind  = '$hygiene_kind'
				,      talk_mind_kind     = '$mind_kind'
				,      talk_mind_other    = '$mind_other'
				,      talk_status        = '$talk_stat'
				,      rec_remember_kind  = '$remember_kind'
				,      rec_express_kind   = '$express_kind'
				,      center_use_kind    = '$use_kind'
				,      center_use_other   = '$use_other'";

		if ($write_mode == 1){
			$sql .= "
				,      insert_id           = '$reg_cd'
				,      insert_dt           = '$reg_dt'";
		}else{
			$sql .= "
				,      del_flag            = 'N'
				,      update_id           = '$reg_cd'
				,      update_dt           = '$reg_dt'";
		}

		$sql .= "
				 where org_no              = '$code'
				   and client_dt           = '$counsel_dt'
				   and client_seq          = '$counsel_seq'";
		
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
	
	if ($counsel_kind == 3){
	// counsel_client_baby 산모신생아
		//신규등록
		if ($conn->get_data('select count(*) from counsel_client_baby where org_no = \''.$code.'\' and client_dt = \''.$counsel_dt.'\' and client_seq = \''.$counsel_seq.'\'') == 0){
			$sql = "insert into counsel_client_baby (
					 org_no
					,client_dt
					,client_seq) values (
					 '$code'
					,'$counsel_dt'
					,'$counsel_seq')";

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $myF->message('error', 'Y', 'Y');
				//echo nl2br($sql);
				exit;
			}
		}

		$talk_cd       = $ed->de($_POST['baby_talker_cd']);
		$talk_nm       = $conn->member_name($code, $talk_cd, $kind);
		$talk_type     = $_POST['baby_counsel_type'];
		$talk_dt       = $_POST['baby_counsel_date'];
		$protect_gbn   = $_POST['baby_protect_gbn'];
		$delivery_dt   = $_POST['baby_delivery_dt'];
		$delivery_kind = $_POST['baby_delivery_kind'];
		$dis_lvl       = $_POST['baby_dis_lvl'];
		$dis_kind      = $_POST['baby_dis_kind'];
		$nurse         = $_POST['baby_nurse'];
		$drug          = $_POST['baby_drug'];
		$mind          = $_POST['baby_mind'];
		$body          = $_POST['baby_body'];
		$family        = $_POST['baby_family'];
		$abode         = $_POST['baby_abode'];
		$abode_other   = $_POST['baby_abode_other'];

		$hope_svc = '';
		$row = 0;
		$col = 0;

		$hope_row = $_POST['baby_hope_row'];
		$hope_col = $_POST['baby_hope_col'];

		for($i=0; $i<$hope_row; $i++){
			for($j=0; $j<$hope_col; $j++){
				$hope_svc .= ($_POST['baby_hope_svc_'.$i.'_'.$j] == 'Y' ? 'Y' : 'N');
			}
		}

		$hope_dt     = $_POST['baby_hope_dt'];
		$hope_period = $_POST['baby_hope_period'];
		$hope_time   = $_POST['baby_hope_time'];
		$hope_amt    = str_replace(',', '', $_POST['baby_hope_amt']);
		$other       = $_POST['baby_other'];

		$sql = "update counsel_client_baby
				   set talker_ssn           = '$talk_cd'
				,      talker_nm            = '$talk_nm'
				,      talker_type          = '$talk_type'
				,      talker_dt            = '$talk_dt'
				,      protect_gbn          = '$protect_gbn'
				,      protect_other        = '$delivery_dt'
				,      health_delivery_dt   = '$delivery_dt'
				,      health_delivery_kind = '$delivery_kind'
				,      health_dis_lvl       = '$dis_lvl'
				,      health_dis_kind      = '$dis_kind'
				,      health_nurse         = '$nurse'
				,      health_mind          = '$mind'
				,      health_drug          = '$drug'
				,      health_body          = '$body'
				,      family_status        = '$family'
				,      family_abode         = '$abode'
				,      family_other         = '$abode_other'
				,      hope_service         = '$hope_svc'
				,      svc_dt               = '$hope_dt'
				,      svc_period           = '$hope_period'
				,      svc_time             = '$hope_time'
				,      svc_use_amt          = '$hope_amt'
				,      other                = '$other'";

		if ($write_mode == 1){
			$sql .= "
				,      insert_id            = '$reg_cd'
				,      insert_dt            = '$reg_dt'";
		}else{
			$sql .= "
				,      del_flag            = 'N'
				,      update_id            = '$reg_cd'
				,      update_dt            = '$reg_dt'";
		}

		$sql .= "
				 where org_no               = '$code'
				   and client_dt            = '$counsel_dt'
				   and client_seq           = '$counsel_seq'";

		//if($debug) echo nl2br($sql); exit;
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
?>
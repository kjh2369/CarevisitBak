<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');


	$counsel_kind = $_POST['counsel_kind']; //상담기록지 구분
	
	if ($_POST['counsel_path'] != '') $is_path = $_POST['counsel_path'];
	
	$is_path = $_POST['is_path'];

	$code	= $_POST['code'];
	$kind	= $conn->center_kind($code);
	$name	= $conn->center_name($code, $kind);

	$counsel_dt  = $_POST['counsel_dt'];
	$counsel_seq = $_POST['counsel_seq'];

	if (!$counsel_dt)
		$counsel_dt = date('Y-m-d', mktime());

	if (!$counsel_seq)
		$counsel_seq = 0;

	//상담 개인정보
	$sql = "select *
			  from counsel_client
			 where org_no     = '$code'
			   and client_dt  = '$counsel_dt'
			   and client_seq = '$counsel_seq'";
	
	$counsel = $conn->get_array($sql);

	if (!$counsel){
		$counsel_mode = 1;
		$counsel_ssn  = '';
		$counsel['client_family_gbn'] = '3';
	}else{
		$counsel_mode = 2;
		$counsel_ssn = $counsel['client_ssn'];
	}
	
	
	if ($counsel_mode == 1){
		$normal['talker_ssn']  = '';
		$normal['talker_nm']   = '';
		$normal['talker_type'] = '1';
		$normal['talker_dt']   = date('Y-m-d', mktime());
		$normal['protect_gbn'] = 'N';

		$baby['talker_ssn']  = '';
		$baby['talker_nm']   = '';
		$baby['talker_type'] = '1';
		$baby['talker_dt']   = date('Y-m-d', mktime());
		$baby['protect_gbn'] = '1';

		$normal['health_eye_kind']    = '1';
		$normal['health_ear_kind']    = '1';
		$normal['body_activate_kind'] = '1';
		$normal['nutr_eat_kind']      = '1';
		$normal['nutr_excreta_kind']  = '1';
		$normal['nutr_hygiene_kind']  = '1';
		$normal['talk_mind_kind']     = '1';
		$normal['rec_remember_kind']  = '1';
		$normal['rec_express_kind']   = '1';
		$normal['center_use_kind']    = '1';

		$baby['health_dis_lvl']  = 'N';
		$baby['health_dis_kind'] = '9';

		$desire_cnt = 0;
		$family_cnt = 1;
	}else{
		//상담 재가/노인돌봄/가사간병/장애활동
		$sql = "select *
				  from counsel_client_normal
				 where org_no     = '$code'
				   and client_dt  = '$counsel_dt'
				   and client_seq = '$counsel_seq'";
		
		$normal = $conn->get_array($sql);
		
		//상담자
		$sql = 'select m02_yname
				from   m02yoyangsa
				where  m02_ccode = \''.$code.'\'
				and    m02_yjumin = \''.$normal['talker_ssn'].'\'';
		$talkNm = $conn -> get_data($sql);
		

		//상담 신생아
		$sql = "select *
				  from counsel_client_baby
				 where org_no     = '$code'
				   and client_dt  = '$counsel_dt'
				   and client_seq = '$counsel_seq'";

		$baby = $conn->get_array($sql);

		//상담 욕구
		$sql = "select *
				  from counsel_client_desire
				 where org_no     = '$code'
				   and desire_ssn = '$counsel_ssn'";

		$conn->query($sql);
		$conn->fetch();
		$desire_cnt = $conn->row_count();
		for($i=0; $i<$desire_cnt; $i++){
			$desire[$i] = $conn->select_row($i);
		}
		$conn->row_free();

		//가족사항
		$sql = "select *
				  from counsel_family
				 where org_no     = '$code'
				   and family_ssn = '$counsel_ssn'";

		$conn->query($sql);
		$conn->fetch();
		$family_cnt = $conn->row_count();
		for($i=0; $i<$family_cnt; $i++){
			$family[$i] = $conn->select_row($i);
		}
		$conn->row_free();
	}

	if ($is_path == 'client_reg'){
		include_once('client_counsel_head.php');
	}

	include_once('client_counsel_info.php');

	if ($counsel_kind != 3){
		include_once('client_counsel_normal.php');
		//include_once('client_counsel_desire.php');
	}else{
		include_once('client_counsel_baby.php');
	}
	
	include_once('../counsel/client_counsel_text.php');
?>
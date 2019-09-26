<?
	if ($is_path == 'find_mem'){
		include_once("../inc/_db_open.php");
		include_once("../inc/_myFun.php");
		include_once('../inc/_ed.php');

		$code = $_POST['code'];
		$ssn  = $_POST['jumin'];
	}else{
		if ($is_path == 'counsel'){
			include_once("../inc/_header.php");
			include_once("../inc/_http_uri.php");
			include_once("../inc/_myFun.php");
			include_once('../inc/_ed.php');

			$code = $_SESSION['userCenterCode'];
			$kind = $conn->center_kind($code);
			$name = $conn->center_name($code, $kind);
			$ssn  = $ed->de($_REQUEST['ssn']);
		}else{
			if ($view_type == 'read'){
				$ssn = $jumin;
			}else{
				$ssn = $ed->de($_REQUEST['jumin']);
			}
		}
	}

	$counsel_mode = 1; //등록모드

	if (strlen($ssn) == 13) $counsel_mode = 2; //수정모드

	if (!Empty($code) && !Empty($ssn)){
		$sql = "select *
				  from counsel_mem
				 where org_no  = '$code'
				   and mem_ssn = '$ssn'";

		$mem = $conn->get_array($sql);
	}

	if (!isset($mem)){
		$counsel_mode = 1;
		$ssn  = '';
	}

	#if (!isset($mem['mem_marry']))		$mem['mem_marry']		= 'N';
	#if (!isset($mem['mem_edu_lvl']))	$mem['mem_edu_lvl']		= '3';
	#if (!isset($mem['mem_gbn']))		$mem['mem_gbn']			= '1';
	#if (!isset($mem['mem_abode']))		$mem['mem_abode']		= '1';
	if (!isset($mem['mem_religion']))	$mem['mem_religion']	= 'N';
	if (!isset($mem['mem_dis_lvl']))	$mem['mem_dis_lvl']		= 'N';
	if (!isset($mem['mem_app_path']))	$mem['mem_app_path']	= '1';
	if (!isset($mem['mem_svc_work']))	$mem['mem_svc_work']	= 'N';
	if (!isset($mem['mem_work_time']))	$mem['mem_work_time']	= '1';
	if (!isset($mem['mem_talker_nm']))	$mem['mem_talker_nm']	= '--';
	if (!isset($mem['mem_counsel_gbn']))$mem['mem_counsel_gbn']	= '1';
	if (!isset($mem['mem_counsel_dt']))	$mem['mem_counsel_dt']	= date('Y-m-d', mktime());

	if (isset($mem['mem_picture'])){
		$mem['mem_picture'] = '../mem_picture/'.$mem['mem_picture'];
	}else{
		$mem['mem_picture'] = '../image/no_img_bg.gif';
	}

	if ($counsel_mode == 2){
		//기본정보
		if (empty($mem['mem_nm'])){
			$sql = 'select m02_yname as m_nm
					,      m02_ytel as mobile
					,      m02_ytel2 as phone
					,      m02_email as email
					,      m02_ypostno as postno
					,      m02_yjuso1 as addr
					,      m02_yjuso2 as addr_dtl
					  from m02yoyangsa
					 where m02_ccode  = \''.$code.'\'
					   and m02_yjumin = \''.$ssn.'\'
					   and m02_del_yn = \'N\'
					   and m02_mkind  =  '.$conn->_member_kind();

			$tmp = $conn->get_array($sql);

			$mem['mem_nm']		= $tmp['m_nm'];
			$mem['mem_phone']	= $tmp['phone'];
			$mem['mem_mobile']	= $tmp['mobile'];
			$mem['mem_email']	= $tmp['email'];
			$mem['mem_postno']	= $tmp['postno'];
			$mem['mem_addr']	= $tmp['addr'];
			$mem['mem_addr_dtl']= $tmp['addr_dtl'];

			unset($tmp);
		}

		//가족사항
		$sql = "select *
				  from counsel_family
				 where org_no      = '$code'
				   and family_ssn  = '$ssn'
				   and family_type = '1'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$family[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$family_cnt = sizeof($family);

		if ($family_cnt == 0) $family_cnt = 1;

		//교육이수
		$sql = "select *
				  from counsel_edu
				 where org_no   = '$code'
				   and edu_ssn  = '$ssn'
				   and edu_type = '1'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$edu[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$edu_cnt = sizeof($edu);

		if ($edu_cnt == 0) $edu_cnt = 1;

		//자격
		$sql = "select *
				  from counsel_license
				 where org_no       = '$code'
				   and license_ssn  = '$ssn'
				   and license_type = '1'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$li[$i] = $conn->select_row($i);
		}

		$conn->row_free();

		$li_cnt = sizeof($li);

		if ($li_cnt == 0) $li_cnt = 1;
	}else{
		$family_cnt = 1;
		$edu_cnt = 1;
		$li_cnt = 1;
	}

	// 자격증 리스트
	$sql = "select m99_code, m99_name
			  from m99license
			 order by m99_seq";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$license_list[$i][0] = $row['m99_code'];
		$license_list[$i][1] = $row['m99_name'];
	}

	$conn->row_free();

	$license_cnt = sizeof($license_list);
?>
<?
	include_once("../inc/_function.php");

	$code			= $_POST['code'];
	$kind			= $_POST['kind'];
	$counsel_path   = $_POST['counsel_path'];
	$counsel_mode	= $_POST['counsel_mode'];
	$type			= '1';

	if ($counsel_path == 'counsel'){
		if ($counsel_mode == 1){
			$ssn = $_POST['ssn1'].$_POST['ssn2'];
		}else{
			$ssn = $ed->de($_POST['ssn']);
		}
	}else{
		$ssn = $jumin;
	}

	$counsel_name	= $_POST['counsel_name'];
	$counsel_path	= $_POST['counsel_path']; //진입경로

	$counsel_marry		= $_POST['counsel_marry'];	//결혼여부
	$counsel_phone		= str_replace('-', '', $_POST['counsel_phone']);	//연락처
	$counsel_mobile		= str_replace('-', '', $_POST['counsel_mobile']);	//모바일
	$counsel_mobile_modelno = $_POST['counsel_mobile_modelno'];
	$counsel_email		= $_POST['counsel_email'];	//이메일
	$counsel_postno		= $_POST['counsel_postno'];		   //우편번호
	$counsel_addr		= $_POST['counsel_addr'];			//주소
	$counsel_addr_dtl	= $_POST['counsel_addr_dtl'];		//상세주소
	$counsel_edu_lvl	= $_POST['counsel_edu_level'];		//학력
	$counsel_gbn		= $_POST['counsel_gubun'];			//구분
	$counsel_abode		= $_POST['counsel_abode'];			//주거지
	$counsel_religion	= $_POST['counsel_religion'];		//종교
	$counsel_rel_other	= $_POST['counsel_rel_other'];		//종교기타
	$counsel_hobby		= $_POST['counsel_hobby'];			//취미
	$counsel_dis_lvl	= $_POST['counsel_dis_level'];		//장애구분
	$counsel_dis_text	= $_POST['counsel_dis_text'];		//치료중인 질병
	$counsel_app_path	= $_POST['counsel_app_path'];		//신청경로
	$counsel_app_other	= $_POST['counsel_app_other'];		//신청경로기타
	$counsel_svc_work	= $_POST['counsel_service_work'];	//자원봉사경험
	$counsel_svc_other	= $_POST['counsel_service_other'];	//자원봉사명

	$writer	= $_SESSION['userCode'];
	$today	= date('Y-m-d', mktime());

	//희망영역
	$hope_work = '';
	for($i=1; $i<=6; $i++){
		if ($_POST['counsel_hope_work'.$i] == 'Y'){
			$hope_work .= 'Y';
		}else{
			$hope_work .= ' ';
		}
	}

	$hope_other	= $_POST['counsel_hope_other'];		//희망영역기타
	$work_time	= $_POST['counsel_work_time'];		//근무가능시간
	$salary		= str_replace(',','',$_POST['counsel_hope_salary']);	//희망급여
	$hourly		= str_replace(',','',$_POST['counsel_hope_hourly']);	//희망시급
	$talker_cd	= $ed->de($_POST['talker_cd']);		//작성자
	$talker_nm	= $conn->member_name($code, $talker_cd, $kind);

	$counsel_type	= $_POST['counsel_type'];	//상담유형
	$counsel_dt		= $_POST['counsel_date'];	//상담일

	$counsel_content= addslashes($_POST['counsel_cont']);	//상담내용
	$counsel_action	= addslashes($_POST['counsel_action']);	//조치사항
	$counsel_result	= addslashes($_POST['counsel_result']);	//처리결과
	$counsel_other	= addslashes($_POST['counsel_other']);	//기타

	if ($counsel_path == 'counsel'){
		$pic = $_FILES['counsel_mem_picture'];

		$upload = false;

		if ($pic['tmp_name'] != ''){
			$tmp_info = pathinfo($pic['name']);
			$exp_nm = strtolower($tmp_info['extension']);
			$pic_nm = mktime().'.'.$exp_nm;

			if (move_uploaded_file($pic['tmp_name'], '../mem_picture/'.$pic_nm)){
				// 업로드 성공
				$upload = true;
			}
		}

		#######################################
		#
		# 이미지 축소
		if ($upload){
			$original_path = '../mem_picture/'.$pic_nm;
			$img_w = 90;
			$img_h = 120;
			$img_s = getimagesize($original_path);

			switch($exp_nm){
				case 'jpg':
					$original_img = imageCreateFromJpeg($original_path);
					break;
				case 'png':
					$original_img = imageCreateFromPng($original_path);
					break;
				case 'gif':
					$original_img = imageCreateFromGif($original_path);
					break;
				case 'bmp':
					$original_img = imageCreateFromBmp($original_path);
					break;
			}

			// 새 이미트 틀작성
			$new_img = imageCreateTrueColor($img_w, $img_h);

			// 이미지 복사
			imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1]);

			// 이미지 저장
			switch($exp_nm){
				case 'jpg':
					imageJpeg($new_img, $original_path);
					break;
				case 'png':
					imagePng($new_img, $original_path);
					break;
				case 'gif':
					imageGif($new_img, $original_path);
					break;
				case 'bmp':
					imageBmp($new_img, $original_path);
					break;
			}

			// 종료
			imageDestroy($new_img);
		}
		#
		#######################################

		// 업로드 실패시 파일명 삭제
		if (!$upload) $pic_nm = '';
	}else{
		$pic_nm = '';
	}

	if ($counsel_path == 'counsel'){
		$conn->begin();
	}

	// 가족 삭제
	$sql = "delete
			  from counsel_family
			 where org_no      = '$code'
			   and family_type = '$type'
			   and family_ssn  = '$ssn'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	// 교육삭제
	$sql = "delete
			  from counsel_edu
			 where org_no   = '$code'
			   and edu_type = '$type'
			   and edu_ssn  = '$ssn'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	// 자격삭제
	$sql = "delete
			  from counsel_license
			 where org_no       = '$code'
			   and license_type = '$type'
			   and license_ssn  = '$ssn'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	$sql = "select count(*)
			  from counsel_mem
			 where org_no  = '$code'
			   and mem_ssn = '$ssn'";

	$tmp_mem_cnt = $conn->get_data($sql);

	if ($counsel_mode == 1 || $tmp_mem_cnt == 0){
		$sql = "replace into counsel_mem (org_no,mem_ssn,mem_insert_id,mem_insert_dt) values ('$code','$ssn','$writer','$today')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->temp_query.'<br>';
			echo $conn->err_back();
		if ($conn->mode == 1) exit;
		}
	}

	$sql = "update counsel_mem
			   set mem_nm				= '$counsel_name'
			,      mem_religion			= '$counsel_religion'
			,      mem_rel_other		= '$counsel_rel_other'
			,      mem_hobby			= '$counsel_hobby'
			,      mem_dis_lvl			= '$counsel_dis_lvl'
			,      mem_dis_nm			= '$counsel_dis_text'
			,      mem_app_path			= '$counsel_app_path'
			,      mem_app_other		= '$counsel_app_other'
			,      mem_svc_work			= '$counsel_svc_work'
			,      mem_svc_other		= '$counsel_svc_other'
			,      mem_hope_work		= '$hope_work'
			,      mem_hope_other		= '$hope_other'
			,      mem_work_time		= '$work_time'
			,      mem_salary			= '$salary'
			,      mem_hourly			= '$hourly'
			,      mem_talker_id		= '$talker_cd'
			,      mem_talker_nm		= '$talker_nm'
			,      mem_counsel_gbn		= '$counsel_type'
			,      mem_counsel_dt		= '$counsel_dt'
			,      mem_counsel_content	= '$counsel_content'
			,      mem_counsel_action	= '$counsel_action'
			,      mem_counsel_result	= '$counsel_result'
			,      mem_counsel_other	= '$counsel_other'
			,      del_flag             = 'N'";

	#if ($counsel_path == 'counsel'){
		$sql .= ", mem_marry	= '$counsel_marry'
				 , mem_phone	= '$counsel_phone'
				 , mem_mobile	= '$counsel_mobile'
				 , mem_mobile_modelno = '$counsel_mobile_modelno'
				 , mem_email	= '$counsel_email'
				 , mem_postno	= '$counsel_postno'
				 , mem_addr		= '$counsel_addr'
				 , mem_addr_dtl	= '$counsel_addr_dtl'
				 , mem_edu_lvl	= '$counsel_edu_lvl'
				 , mem_gbn		= '$counsel_gbn'
				 , mem_abode	= '$counsel_abode'";
	#}

	if ($pic_nm != ''){
		$sql .= "
			,      mem_picture			= '$pic_nm'";
	}

	if ($counsel_mode == 2){
		$sql .= "
			,	   mem_update_id		= '$writer'
			,      mem_update_dt		= '$today'";
	}

	$sql .= "
			 where org_no				= '$code'
			   and mem_ssn				= '$ssn'";


	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	// 사진이 있을 경우 직원정보 테이블도 수정한다.
	if ($pic_nm != ''){
		$sql = "update m02yoyangsa
				   set m02_picture = '$pic_nm'
				 where m02_ccode   = '$code'
				   and m02_yjumin  = '$ssn'";
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}

	$sql = "";

	$family_cnt = sizeof($_POST['family_name']);

	for($i=0; $i<$family_cnt; $i++){
		$family_name		= $_POST['family_name'][$i];
		$family_relation	= $_POST['family_relation'][$i];
		$family_age			= $_POST['family_age'][$i];
		$family_job			= $_POST['family_job'][$i];
		$family_together	= $_POST['family_together'][$i];
		$family_salary		= str_replace(',', '', $_POST['family_salary'][$i]);

		if ($family_name != ''){
			$sql .= ($sql != "" ? "," : "");
			$sql .= "('$code'
					 ,'$type'
					 ,'$ssn'
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

	$sql = "";

	$edu_cnt = sizeof($_POST['edu_center']);

	for($i=0; $i<$edu_cnt; $i++){
		$edu_gbn	= $_POST['edu_gbn'][$i];
		$edu_center	= $_POST['edu_center'][$i];
		$edu_name	= $_POST['edu_name'][$i];
		$edu_date	= $_POST['edu_date'][$i];

		if ($edu_center != '' && $edu_name != ''){
			$sql .= ($sql != "" ? "," : "");
			$sql .= "('$code'
					 ,'$type'
					 ,'$ssn'
					 ,'$i'
					 ,'$edu_gbn'
					 ,'$edu_center'
					 ,'$edu_name'
					 ,''
					 ,''
					 ,'$edu_date')";
		}
	}

	if ($sql != ""){
		$sql = "insert into counsel_edu values ".$sql;

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}

	$sql = "";

	$license_cnt = sizeof($_POST['license_type']);

	for($i=0; $i<$license_cnt; $i++){
		$license_type	= $_POST['license_type'][$i];
		$license_no		= $_POST['license_no'][$i];
		$license_center	= $_POST['license_center'][$i];
		$license_date	= $_POST['license_date'][$i];

		if ($license_type != ''){
			$sql .= ($sql != "" ? "," : "");
			$sql .= "('$code'
					 ,'$type'
					 ,'$ssn'
					 ,'$i'
					 ,'$license_type'
					 ,'$license_no'
					 ,'$license_center'
					 ,'$license_date')";
		}
	}

	if ($sql != ""){
		$sql = "insert into counsel_license values ".$sql;

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}

	if ($counsel_path == 'counsel'){
		$conn->commit();
	}
?>